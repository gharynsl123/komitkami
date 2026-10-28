<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Invoice;
use App\StockInProduct;
use App\StockOutProduct;
use Illuminate\Support\Facades\DB;
use App\Order;
use App\User;
use Illuminate\Support\Facades\Storage;
use PDF;
use App\Deliveries;
use App\DeliveryItems;
use App\DeliveryInvoices;
use App\ManualInvoiceItem;
use App\Product;
use App\LogWebsite;
class DeliveryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function index()
    {
        $deliveries = Deliveries::with(['invoice', 'items.order.product', 'items.order.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $manualInvoices = DeliveryInvoices::with(['manualItems.product', 'manualItems.user'])
            ->whereNull('delivery_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('delivery.index-delivery', compact('deliveries', 'manualInvoices'));
    }

    public function create()
    {
        $invoices = Invoice::with([
            'orders.product',
            'orders.user',
            'orders.deliveryItems'
        ])
            ->whereHas('orders', function ($query) {
                $query->whereNotIn('status', ['accept', 'pending', 'done']);
            })
            ->get();

        $filteredInvoices = collect();

        foreach ($invoices as $invoice) {

            $validOrders = $invoice->orders->filter(function ($order) {

                $totalSent = $order->deliveryItems->sum('jumlah_barang');
                $remainingQuantity = $order->quantity - $totalSent;

                if ($remainingQuantity <= 0) {
                    return false;
                }

                $order->remaining_quantity = $remainingQuantity;

                $stockIns = StockInProduct::where('product_id', $order->product_id)
                    ->with('produksi')
                    ->withSum('stockOuts', 'quantity')
                    ->get()
                    ->sortBy(function ($s) {
                        $expiredDate = $s->produksi?->tanggal_expired ?? $s->expired_date;
                        return $expiredDate ? strtotime($expiredDate) : PHP_INT_MAX;
                    });

                $order->available_stock = $stockIns->sum(
                    fn($s) => $s->quantity - ($s->stock_outs_sum_quantity ?? 0)
                );

                $order->stock_batches = $stockIns;

                return true;
            });

            if ($validOrders->isEmpty()) {
                continue;
            }

            $invoice->setRelation('orders', $validOrders);

            $filteredInvoices->push($invoice);
        }

        return view('delivery.create-delivery', [
            'invoices' => $filteredInvoices
        ]);
    }

    public function addToList(Request $request)
    {
        $productId = $request->product_id;

        $available = StockInProduct::where('product_id', $productId)
            ->sum('quantity')
            -
            StockOutProduct::whereHas('stockIn', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })->sum('quantity');

        return response()->json([
            'success' => true,
            'available' => $available
        ]);
    }


    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

            $items = json_decode($request->items, true);
            $now = now();
            $year = $now->year;

            $last = Deliveries::latest()->first();
            $counter = 1;

            if ($last && $last->created_at->year == $year) {
                $parts = explode('/', $last->no_sj);
                if (count($parts) >= 3) {
                    $counter = intval($parts[2]) + 1;
                }
            }
            $No_SJ = $request->no_sj ?? "KIG/SJ/" . str_pad($counter, 3, '0', STR_PAD_LEFT) . "/$year";
            $slug = str_replace('/', '-', $No_SJ);

            $itemsByInvoice = collect($items)->groupBy('invoice_id');

            foreach ($itemsByInvoice as $invoiceId => $invoiceItems) {
                if ($counter > 1 && $invoiceId != $itemsByInvoice->keys()->first()) {
                    $No_SJ = "KIG/SJ/" . str_pad(++$counter, 3, '0', STR_PAD_LEFT) . "/$year";
                    $slug = str_replace('/', '-', $No_SJ);
                }

                $delivery = Deliveries::create([
                    'slug' => $slug,
                    'tanggal_terbit' => $request->tanggal_terbit,
                    'no_sj' => $request->no_sj ?? $No_SJ,
                    'note' => $request->note,
                    'status' => 'in_progress',
                    'invoice_id' => $invoiceId,
                ]);

                foreach ($invoiceItems as $item) {
                    $qtyNeed = $item['quantity'];

                    // Create Delivery Item
                    $deliveryItem = DeliveryItems::create([
                        'delivery_id' => $delivery->id,
                        'order_id' => $item['order_id'],
                        'jumlah_barang' => $qtyNeed,
                        // 'jenis_kendaraan', 'nomor_kendaraan' are not in form
                    ]);

                    $batchLog = [];
                    $qtyNeedCheck = $qtyNeed;

                    if (isset($item['batches']) && is_array($item['batches']) && count($item['batches']) > 0) {
                        foreach ($item['batches'] as $batchSelect) {
                            $stockInId = $batchSelect['batch_id'];
                            $take = $batchSelect['qty'];

                            if ($take <= 0)
                                continue;

                            $stockIn = StockInProduct::with('produksi')->find($stockInId);
                            if (!$stockIn) {
                                throw new \Exception("Batch tidak ditemukan");
                            }

                            $used = StockOutProduct::where('stock_in_product_id', $stockInId)->sum('quantity');
                            $available = $stockIn->quantity - $used;

                            if ($available < $take) {
                                throw new \Exception("Stok tidak mencukupi untuk batch " . ($stockIn->produksi->batch_number ?? $stockIn->batch_number ?? '-'));
                            }

                            StockOutProduct::create([
                                'stock_in_product_id' => $stockIn->id,
                                'quantity' => $take,
                                'delivery_item_id' => $deliveryItem->id,
                                'product_id' => $item['product_id'],
                                'note' => 'Pengiriman ' . $No_SJ,
                            ]);

                            $batchLog[] = [
                                'batch' => $stockIn->produksi->batch_number ?? $stockIn->batch_number ?? '-',
                                'ed' => $stockIn->produksi->tanggal_expired ?? $stockIn->expired_date ?? '-',
                                'qty' => $take
                            ];

                            $qtyNeedCheck -= $take;
                        }
                    } else {
                        // Stock Deduction Logic (Fallback FIFO)
                        $stockIns = StockInProduct::where('product_id', $item['product_id'])
                            ->withSum('stockOuts', 'quantity')
                            ->get()
                            ->sortBy(function ($s) {
                                $expiredDate = $s->produksi?->tanggal_expired ?? $s->expired_date;
                                return $expiredDate ? strtotime($expiredDate) : PHP_INT_MAX;
                            });

                        foreach ($stockIns as $stockIn) {
                            $used = $stockIn->stock_outs_sum_quantity ?? 0;
                            $available = $stockIn->quantity - $used;

                            if ($available <= 0)
                                continue;

                            $take = min($available, $qtyNeedCheck);

                            StockOutProduct::create([
                                'stock_in_product_id' => $stockIn->id,
                                'quantity' => $take,
                                'delivery_item_id' => $deliveryItem->id,
                                'product_id' => $item['product_id'],
                                'note' => 'Pengiriman ' . $No_SJ,
                            ]);

                            $batchLog[] = [
                                'batch' => $stockIn->produksi->batch_number ?? $stockIn->batch_number ?? '-',
                                'ed' => $stockIn->produksi->tanggal_expired ?? $stockIn->expired_date ?? '-',
                                'qty' => $take
                            ];

                            $qtyNeedCheck -= $take;
                            if ($qtyNeedCheck <= 0)
                                break;
                        }
                    }

                    if ($qtyNeedCheck > 0) {
                        throw new \Exception("Stok tidak mencukupi untuk " . $item['name']);
                    }

                    // Group by ED
                    $grouped = collect($batchLog)->groupBy(function ($item) {
                        return $item['ed']
                            ? date('m/Y', strtotime($item['ed']))
                            : '-';
                    });

                    $noteParts = [];

                    foreach ($grouped as $ed => $batches) {

                        foreach ($batches as $b) {
                            $noteParts[] = "- {$b['batch']} : {$b['qty']} pcs";
                        }

                        $noteParts[] = "ED {$ed}";
                    }

                    $deliveryItem->note = trim(implode("\n", $noteParts));
                    $deliveryItem->save();

                    // Update Order Status
                    $totalSent = DeliveryItems::where('order_id', $item['order_id'])->sum('jumlah_barang');

                    $order = Order::find($item['order_id']);

                    if ($totalSent >= $order->quantity) {
                        $order->status = 'On The Way';
                        $order->save();
                    }
                }

                // Cek apakah masih ada order dalam invoice ini yang belum selesai
                $unfinishedOrders = Order::where('invoice_id', $invoiceId)
                    ->where(function ($q) {
                        $q->where('status', '!=', 'On The Way')
                            ->orWhereNull('status');
                    })
                    ->exists();

                if (!$unfinishedOrders) {
                    Invoice::where('id', $invoiceId)
                        ->update(['status' => 'On The Way']);
                }
            }
        });

        return redirect()->route('delivery.index')->with('success', 'Delivery berhasil dibuat');
    }

    public function letter($slug)
    {
        $delivery = Deliveries::with(['items.order.product', 'items.order.user', 'invoice'])->where('slug', $slug)->firstOrFail();

        // Render kedua view menjadi HTML
        $html1 = view('delivery.surat-jalan', compact('delivery'))->render();
        $html2 = view('delivery.surat-terima-barang', compact('delivery'))->render();

        $combinedHtml = $html1 . $html2;

        $pdf = PDF::loadHTML($combinedHtml);

        return $pdf->stream('surat-jalan-dan-terima-barang.pdf');
    }

    public function printInvoice($slug)
    {
        $delivery = Deliveries::with('invoice')->where('slug', $slug)->firstOrFail();

        $html3 = view('delivery.invoice-sales', compact('delivery'))->render();
        $html4 = view('delivery.tanda-terima', compact('delivery'))->render();

        // Menggabungkan kedua view dan memberikan pemisah halaman (page break)
        $combinedHtml = $html3 . $html4;

        $pdf = PDF::loadHTML($combinedHtml);

        return $pdf->stream('invoice-sales.pdf');
    }

    public function updateDelivery($slug)
    {
        $delivery = Deliveries::where('slug', $slug)->firstOrFail();
        $invoices = DeliveryInvoices::where('delivery_id', $delivery->id)->first();

        return view('delivery.edit-delivery', compact('delivery', 'invoices'));
    }

    public function updateStatus(Request $request, $slug)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,delivered,cancelled',
            'photo_surat_jalan' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5048',
            'no_invoice' => 'nullable|string',
            'status_invoice' => 'nullable|in:un-paid,paid',
        ]);

        try {
            DB::transaction(function () use ($request, $slug) {
                // Find Delivery by slug
                $delivery = Deliveries::where('slug', $slug)->firstOrFail();

                $updateData = [
                    'status' => $request->status,
                ];

                if ($request->hasFile('photo_surat_jalan')) {
                    $file = $request->file('photo_surat_jalan');

                    // Gunakan Str::slug atau str_replace untuk membersihkan nama file
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeName = Str::slug($originalName) . '.' . $file->getClientOriginalExtension();

                    $filename = time() . '_' . $safeName;

                    $path = $file->storeAs('surat-jalan', $filename, 'public');
                    $updateData['photo_surat_jalan'] = 'storage/' . $path;
                }

                $delivery->update($updateData);

                if ($request->filled('no_invoice')) {
                    $totalHarga = 0;
                    $items = DeliveryItems::with('order.product')
                        ->where('delivery_id', $delivery->id)
                        ->get();

                    foreach ($items as $item) {
                        $quantity = (int) $item->jumlah_barang;
                        $price = $item->order->price ?? 0;

                        $totalHarga += $quantity * $price;
                    }

                    if ($delivery->status == 'delivered' || $delivery->status == 'pending' || $delivery->status == 'canceled') {
                        $invoice = DeliveryInvoices::where('delivery_id', $delivery->id)->first();
                        if ($invoice) {
                            $invoice->update([
                                'no_invoice' => $request->no_invoice,
                                'total_harga' => $totalHarga,
                                'status' => $request->status_invoice,
                                'customer_refrence' => $request->customer_refrence,
                            ]);
                        } else {
                            DeliveryInvoices::create([
                                'no_invoice' => $request->no_invoice,
                                'total_harga' => $totalHarga,
                                'status' => 'un-paid',
                                'delivery_id' => $delivery->id,
                                'invoice_id' => $delivery->invoice->id,
                                'customer_refrence' => $request->customer_refrence,
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('delivery.index')->with('success', 'Status pengiriman berhasil diperbarui');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    public function invoice($slug)
    {
        $delivery = Deliveries::where('slug', $slug)->firstOrFail();

        if ($delivery->status !== 'delivered' && empty($delivery->photo_surat_jalan)) {
            return redirect()->back()->with('error', 'Akses Ditolak: Surat Jalan belum dikonfirmasi kembali (Upload bukti surat jalan untuk membuka invoice).');
        }

        $html3 = view('delivery.invoice-sales', compact('delivery'))->render();

        $pdf = PDF::loadHTML($html3);

        return $pdf->stream('invoice-sales.pdf');
    }

    public function createManualInvoice()
    {
        $customers = User::whereHas('roles', function ($q) {
            $q->where('role_name', 'Customer');
        })->get();
        $products = Product::all();

        return view('delivery.create-manual-invoice', compact('customers', 'products'));
    }

    public function storeManualInvoice(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_terbit' => 'required',
            'no_invoice' => 'required|string|unique:delivery_invoices,no_invoice',
            'customer_refrence' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:product,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $totalHarga = 0;

                foreach ($request->items as $item) {
                    $totalHarga += ($item['quantity'] * $item['price']);
                }

                $invoice = DeliveryInvoices::create([
                    'no_invoice' => $request->no_invoice,
                    'total_harga' => $totalHarga,
                    'status' => 'un-paid',
                    'tanggal_terbit' => $request->tanggal_terbit,
                    'note' => $request->note,
                    'customer_refrence' => $request->customer_reference,
                    'delivery_id' => null,
                    'invoice_id' => null,
                ]);

                foreach ($request->items as $item) {
                    ManualInvoiceItem::create([
                        'delivery_invoice_id' => $invoice->id,
                        'user_id' => $request->user_id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price_per_item' => $item['price'],
                        'total_price' => $item['quantity'] * $item['price'],
                    ]);
                }
            });

            return redirect()->route('delivery.index')->with('success', 'Invoice Manual berhasil dibuat');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat invoice manual: ' . $e->getMessage())->withInput();
        }
    }

    public function printManualInvoice($id)
    {
        $manualInvoice = DeliveryInvoices::with(['manualItems.product', 'manualItems.user'])->findOrFail($id);

        $html = view('delivery.invoice-sales', compact('manualInvoice'))->render();

        $pdf = PDF::loadHTML($html);

        return $pdf->stream('manual-invoice-' . $manualInvoice->no_invoice . '.pdf');
    }

    public function editManualInvoice($id)
    {
        $invoice = DeliveryInvoices::with('manualItems')->findOrFail($id);
        
        $customers = User::whereHas('roles', function($q){
            $q->where('role_name', 'Customer');
        })->get();
        $products = Product::all();
        return view('delivery.edit-manual-invoice', compact('invoice', 'customers', 'products'));
    }
    public function updateManualInvoice(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_terbit' => 'required',
            'no_invoice' => 'required|string|unique:delivery_invoices,no_invoice,'.$id,
            'customer_refrence' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:product,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'status' => 'required|in:un-paid,paid',
        ]);
        try {
            DB::transaction(function () use ($request, $id) {
                $invoice = DeliveryInvoices::findOrFail($id);
                $totalHarga = 0;
                
                foreach ($request->items as $item) {
                    $totalHarga += ($item['quantity'] * $item['price']);
                }
                $invoice->update([
                    'no_invoice' => $request->no_invoice,
                    'total_harga' => $totalHarga,
                    'tanggal_terbit' => $request->tanggal_terbit,
                    'status' => $request->status,
                    'note' => $request->note,
                    'customer_refrence' => $request->customer_reference,
                ]);
                // Delete all old items and re-create them
                $invoice->manualItems()->delete();
                foreach ($request->items as $item) {
                    ManualInvoiceItem::create([
                        'delivery_invoice_id' => $invoice->id,
                        'user_id' => $request->user_id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price_per_item' => $item['price'],
                        'total_price' => $item['quantity'] * $item['price'],
                    ]);
                }
            });
            return redirect()->route('delivery.index')->with('success', 'Invoice Manual berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui invoice manual: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyItem($id)
    {
        if (!Auth::user()->hasAnyRole(['Admin', 'Sales', 'Production Manager'])) {
            return redirect()->route('delivery.index')->with('error', 'Anda tidak memiliki hak akses untuk menghapus item ini.');
        }

        try {
            $message = DB::transaction(function () use ($id) {
                // 1. Ambil data delivery item beserta relasi terkait
                $item = DeliveryItems::with([
                    'delivery',
                    'order.invoice',
                    'order.product',
                    'stockOuts.stockInProduct.produksi'
                ])->findOrFail($id);

                $delivery = $item->delivery;
                $order = $item->order;
                $productName = $order->product->name ?? 'Produk';
                $qty = (int) $item->jumlah_barang;
                $noSj = $delivery->no_sj;

                // 2. Kumpulkan informasi batch dari StockOutProduct sebelum dihapus untuk logging
                $stockOuts = StockOutProduct::with('stockInProduct.produksi')
                    ->where('delivery_item_id', $item->id)
                    ->get();

                $returnedBatches = [];
                foreach ($stockOuts as $stockOut) {
                    $stockIn = $stockOut->stockInProduct;
                    $batchNumber = $stockIn?->produksi?->batch_number ?? $stockIn?->batch_number ?? '-';
                    $returnedBatches[] = "Batch {$batchNumber} ({$stockOut->quantity} pcs)";
                }

                // 3. HAPUS records stock_out_product
                // Dengan menghapus record stock_out_product ini, stok pada batch stock_in_product
                // otomatis kembali (tersedia) karena stok batch dan stok produk dihitung secara dinamis:
                // stok_batch = stock_in_product.quantity - sum(stock_out_product.quantity)
                StockOutProduct::where('delivery_item_id', $item->id)->delete();

                // 4. HAPUS delivery_item
                $item->delete();

                // 5. Update Status Order & PO Invoice jika pengiriman belum penuh lagi
                if ($order) {
                    $totalSentRemaining = DeliveryItems::where('order_id', $order->id)->sum('jumlah_barang');

                    // Jika sisa barang terkirim kurang dari quantity order dan statusnya tadinya 'On The Way'
                    if ($totalSentRemaining < (int) $order->quantity && $order->status === 'On The Way') {
                        $order->status = 'process';
                        $order->save();

                        // Cek status PO Invoice
                        if ($order->invoice && $order->invoice->status === 'On The Way') {
                            $order->invoice->status = 'process';
                            $order->invoice->save();
                        }
                    }
                }

                // 6. Cek sisa item di pengiriman (delivery) ini
                $remainingItemsCount = DeliveryItems::where('delivery_id', $delivery->id)->count();

                if ($remainingItemsCount === 0) {
                    // Jika tidak ada item tersisa, hapus invoice pengiriman dan delivery header
                    DeliveryInvoices::where('delivery_id', $delivery->id)->delete();
                    $delivery->delete();

                    $logMsg = "Menghapus item delivery #{$id} ({$productName}, Qty: {$qty}) dari SJ: {$noSj}. Stok batch berhasil dikembalikan ke: " . (!empty($returnedBatches) ? implode(', ', $returnedBatches) : 'gudang') . ". Draft pengiriman {$noSj} otomatis dihapus karena sudah tidak memiliki item tersisa.";

                    if (class_exists(LogWebsite::class) && Auth::check()) {
                        LogWebsite::create([
                            'users_id' => Auth::id(),
                            'action' => 'DELETE',
                            'keterangan' => $logMsg,
                        ]);
                    }

                    return "Item {$productName} ({$qty} pcs) berhasil dihapus dan stok telah dikembalikan ke batch asalnya. Karena tidak ada item lain tersisa, draft pengiriman {$noSj} juga telah dihapus.";
                } else {
                    // Update total_harga pada DeliveryInvoices jika ada
                    $deliveryInvoice = DeliveryInvoices::where('delivery_id', $delivery->id)->first();
                    if ($deliveryInvoice) {
                        $remainingItems = DeliveryItems::with('order.product')
                            ->where('delivery_id', $delivery->id)
                            ->get();

                        $newTotal = 0;
                        foreach ($remainingItems as $remItem) {
                            $q = (int) $remItem->jumlah_barang;
                            $p = $remItem->order->product->price ?? 0;
                            $newTotal += ($q * $p);
                        }
                        $deliveryInvoice->total_harga = $newTotal;
                        $deliveryInvoice->save();
                    }

                    $logMsg = "Menghapus item delivery #{$id} ({$productName}, Qty: {$qty}) dari SJ: {$noSj}. Stok batch berhasil dikembalikan ke: " . (!empty($returnedBatches) ? implode(', ', $returnedBatches) : 'gudang') . ".";

                    if (class_exists(LogWebsite::class) && Auth::check()) {
                        LogWebsite::create([
                            'users_id' => Auth::id(),
                            'action' => 'DELETE',
                            'keterangan' => $logMsg,
                        ]);
                    }

                    return "Item {$productName} ({$qty} pcs) berhasil dihapus dan stok telah dikembalikan ke batch asalnya.";
                }
            });

            return redirect()->route('delivery.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('delivery.index')->with('error', 'Gagal menghapus item: ' . $e->getMessage());
        }
    }

    public function destroyDelivery($id)
    {
        if (!Auth::user()->hasAnyRole(['Admin', 'Sales', 'Production Manager'])) {
            return redirect()->route('delivery.index')->with('error', 'Anda tidak memiliki hak akses untuk menghapus pengiriman ini.');
        }

        try {
            $message = DB::transaction(function () use ($id) {
                $delivery = Deliveries::with(['items.stockOuts.stockInProduct.produksi', 'items.order.invoice'])->findOrFail($id);
                $noSj = $delivery->no_sj;
                $deletedItemsInfo = [];

                foreach ($delivery->items as $item) {
                    $order = $item->order;
                    $productName = $order->product->name ?? 'Produk';
                    $qty = (int) $item->jumlah_barang;

                    // Hapus stock out untuk item ini agar stok batch kembali
                    StockOutProduct::where('delivery_item_id', $item->id)->delete();

                    // Hapus delivery item
                    $item->delete();

                    // Kembalikan status order jika On The Way
                    if ($order) {
                        $totalSentRemaining = DeliveryItems::where('order_id', $order->id)->sum('jumlah_barang');
                        if ($totalSentRemaining < (int) $order->quantity && $order->status === 'On The Way') {
                            $order->status = 'process';
                            $order->save();

                            if ($order->invoice && $order->invoice->status === 'On The Way') {
                                $order->invoice->status = 'process';
                                $order->invoice->save();
                            }
                        }
                    }

                    $deletedItemsInfo[] = "{$productName} ({$qty} pcs)";
                }

                // Hapus invoice pengiriman jika ada
                DeliveryInvoices::where('delivery_id', $delivery->id)->delete();

                // Hapus file surat jalan jika ada di storage
                if ($delivery->photo_surat_jalan && Storage::disk('public')->exists(str_replace('storage/', '', $delivery->photo_surat_jalan))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $delivery->photo_surat_jalan));
                }

                $delivery->delete();

                $logMsg = "Menghapus draft pengiriman {$noSj} beserta seluruh itemnya: " . implode(', ', $deletedItemsInfo) . ". Semua stok batch berhasil dikembalikan ke gudang.";

                if (class_exists(LogWebsite::class) && Auth::check()) {
                    LogWebsite::create([
                        'users_id' => Auth::id(),
                        'action' => 'DELETE',
                        'keterangan' => $logMsg,
                    ]);
                }

                return "Draft pengiriman {$noSj} berhasil dihapus dan seluruh stok item telah dikembalikan ke batch masing-masing.";
            });

            return redirect()->route('delivery.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('delivery.index')->with('error', 'Gagal menghapus pengiriman: ' . $e->getMessage());
        }
    }
}
