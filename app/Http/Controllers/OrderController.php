<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Events\OrderUpdated;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Brand;
use App\Order;
use App\Product;
use App\Laporan;
use App\Invoice;
use App\Formula;
use Carbon\Carbon;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    function index() {
        $orders = Order::whereNotIn('status', ['reject']);
    
        if (Auth::user()->hasRole('Customer')) {
            $userInstansiId = Auth::user()->id;
            $orders->where('id_user', $userInstansiId);
        }
    
        $groupedOrders = $orders->with('invoice')->get()->groupBy('invoice_id');
    
        $groupedOrders->transform(function ($orders) {
            return $orders->first();
        });
    
        return view('ordering.index-order', compact('groupedOrders'));
    }
    
    function create() {

        $brand = Brand::where('id_user', Auth::id())->get();
        $product = Product::all();

        $orders = Order::all();
        
        return view('ordering.create-order', compact('brand', 'product', 'orders'));
    }

    function filterSelesai() {
        $orders = Order::whereIn('status', ['reject', 'an-paid', 'paid', 'done',]);

        $groupedOrders = $orders->with('invoice')->get()->groupBy('invoice_id');
        $groupedOrders->transform(function ($orders) {
            return $orders->first();
        });

        return view('ordering.filtered-order', compact('groupedOrders'));
    }

    function store(Request $request) {
        $now = Carbon::now();
        $nowDays = $now->format('dmY');

        $dataInvoice['nomor_invoice'] = $request->nomor_po;
        $dataInvoice['slug'] = Str::slug($request->nomor_po);
        $dataInvoice['total_harga'] = $request->input('datatotalsemuaharga') ;

        $invoice = Invoice::create($dataInvoice);

        $id_instansi = $request->input('id_instansi');
        $product_id = $request->input('product_id');
        $quantity = $request->input('quantity');
        $total_harga = $request->input('total_harga');
        $catatan = $request->input('catatan');

        $data = [];
        foreach ($id_instansi as $key => $id_instansi) {
            $data[] = [
                'id_user' => $id_instansi,
                'invoice_id' => $invoice->id,
                'product_id' => $product_id[$key],
                'quantity' => $quantity[$key],
                'total_harga' => $total_harga[$key],
                'catatan' => $catatan[$key],
                'created_at' => Carbon::now(),
            ];
        }    
        Order::insert($data);

        return redirect('/view-order')->with('success', 'berhasil');
    }

    function detail($slug) {
        $invoice = Invoice::where('slug', $slug)->first();
        $orders = Order::where('invoice_id', $invoice->id)->get();
        $orderInformation = Order::where('invoice_id', $invoice->id)->first();
        
        return view('ordering.show-order', compact('invoice', 'orders', 'orderInformation'));
    }

    public function detailCharts(Request $request) {
        $monthName = $request->input('month');
        $product = $request->input('product');
    
        $monthNumber = date('m', strtotime($monthName));
    
        $productId = Product::where('name', $product)->value('id');
    
        $orders = Order::whereMonth('created_at', $monthNumber)
                       ->where('product_id', $productId)
                       ->get();

    
        return view('ordering.index-detail-charts', compact('orders', 'monthName', 'product'));
    
    }
    

    public function rekapPO(Request $request) {
        $fromMonth = Carbon::parse($request->input('fromMonth'))->format('Y-m-d');
        $toMonth = Carbon::parse($request->input('toMonth'))->format('Y-m-d');


        if(Auth::user()->hasRole('Customer')){
            $orders = Order::wheredate('created_at', '>=', $fromMonth)
                    ->wheredate('created_at', '<=', $toMonth)
                    ->where('id_user', Auth::user()->id)
                    ->get();
        }else{
            $orders = Order::wheredate('created_at', '>=', $fromMonth)
                    ->wheredate('created_at', '<=', $toMonth)
                    ->get();
        }


        $uniqueProducts = [];


        foreach ($orders as $order) {
            if ($order->status == 'reject') {
                continue;
            }
        
            $productName = $order->product->name;
            $productPriceString = $order->product->price; // Harga produk sebagai string
            $productPrice = (float) str_replace(['Rp ', '.', ], '', $productPriceString); // Menghapus karakter non-numerik dan mengonversi ke float
            $productSlug = $order->invoice->slug;
            $productCode = $order->product->code;
            $quantity = $order->quantity;
            $totalPrice = $productPrice * $quantity;
        
            if (array_key_exists($productName, $uniqueProducts)) {
                $uniqueProducts[$productName]['quantity'] += $quantity;
                $uniqueProducts[$productName]['code'] = $productCode;
                $uniqueProducts[$productName]['slug'] = $productSlug;
                $uniqueProducts[$productName]['price'] = $productPrice;
                $uniqueProducts[$productName]['totalPrice'] += $totalPrice;
                // Perbarui jumlah barang terkirim dan dalam proses
                if ($order->status == 'done') {
                    $uniqueProducts[$productName]['shipped'] += $quantity;
                } else if($order->status == 'accept'){
                    $uniqueProducts[$productName]['inProcess'] += $quantity;
                }
            } else {
                $uniqueProducts[$productName] = [
                    'quantity' => $quantity,
                    'code' => $productCode,
                    'slug' => $productSlug,
                    'price' => $productPrice,
                    'totalPrice' => $totalPrice,
                    'shipped' => $order->status == 'done' ? $quantity : 0, // Inisialisasi jumlah barang terkirim
                    'inProcess' => $order->status == 'accept' ? 0 : $quantity // Inisialisasi jumlah barang dalam proses
                ];
            }
        }
    
        return view('ordering.index-rekap', compact('orders', 'uniqueProducts','fromMonth', 'toMonth'));
    }

    function detailRekap(Request $request) {
        $fromMonth = Carbon::parse($request->input('fromMonth'));
        $toMonth = Carbon::parse($request->input('toMonth'));

        if(Auth::user()->hasRole('Customer')){
            $orders = Order::wheredate('created_at', '>=', $fromMonth)
                    ->wheredate('created_at', '<=', $toMonth)
                    ->where('id_user', Auth::user()->id)
                    ->get();
        }else{
            $orders = Order::wheredate('created_at', '>=', $fromMonth)
                    ->wheredate('created_at', '<=', $toMonth)
                    ->get();
        }


        $uniqueProductsWithPO = [];

        foreach ($orders as $order) {
            if ($order->status == 'reject') {
                continue;
            }

            $productName = $order->product->name;
            $productCode = $order->product->code;
            $productQuantity = $order->quantity;
            $productStatus = $order->status;
            $productPONumber = $order->invoice->nomor_invoice;

            // Jika produk sudah ada dalam array
            if (array_key_exists($productName, $uniqueProductsWithPO)) {
                // Cek apakah nomor invoice sudah ada dalam array
                $index = array_search($productPONumber, $uniqueProductsWithPO[$productName]['nomor_invoice']);
                if ($index !== false) {
                    // Jika nomor invoice sudah ada, tambahkan jumlah dan status ke indeks yang sesuai
                    $uniqueProductsWithPO[$productName]['quantity'][$index] += $productQuantity;
                    $uniqueProductsWithPO[$productName]['status'][$index] = $productStatus;
                } else {
                    // Jika nomor invoice belum ada, tambahkan nomor invoice, jumlah, dan status baru
                    $uniqueProductsWithPO[$productName]['nomor_invoice'][] = $productPONumber;
                    $uniqueProductsWithPO[$productName]['quantity'][] = $productQuantity;
                    $uniqueProductsWithPO[$productName]['status'][] = $productStatus;
                }
            } else {
                $uniqueProductsWithPO[$productName] = [
                    'nomor_invoice' => [$productPONumber],
                    'code' => $productCode,
                    'quantity' => [$productQuantity],
                    'status' => [$productStatus]
                ];
            }
        }

        return view('ordering.detail-rekap-po', compact('uniqueProductsWithPO'));
    }

    public function unpaid(){ 
        $order = Order::query();
        $instansiId = Auth::user()->id;
        
        $product = null;
        $orderData = null;
        
        if (Auth::user()->hasRole('Customer')) {
            $order->whereHas('invoice', function ($query) use ($instansiId) {
                $query->where('id_user', $instansiId);
            });

            $product = Product::join('brand', 'product.brand_id', '=', 'brand.id')
            ->where('brand.id_user', auth()->user()->id)
            ->select('product.*')
            ->get();  
            
            $orderData = Order::where('id_user', $instansiId)
            ->where(function($query) {
                $query->where('status', '!=', 'paid')
                      ->orWhere('status', '!=', 'done');
            })
            ->get();

            $byproduct = Order::where('id_user', $instansiId)
            ->where(function($query) {
                $query->where('status', '!=', 'paid')
                      ->orWhere('status', '!=', 'done');
            })
            ->get();

        }else{ 
            $orderData = Order::all();
            
            $byproduct = Order::all();

            $product = Product::all();
        }

        $invoiceData = $order->with('invoice')->get()->groupBy('invoice_id');
        $invoiceData->transform(function ($order) {
            return $order->first();
        });

        return view ('ordering.index-unpaid-po', compact('invoiceData','byproduct','product','orderData'));
    }
    
    

    public function changetime(Request $request, $id)
    {
        $order = Invoice::findOrFail($id);
        $dataUpdate = $request->all();

        // Update status menjadi 'accept'
        $order->update($dataUpdate);

        return redirect()->back()->with('success', 'Order has been accepted.');
    }
    
    public function accept(Request $request, $id){
        $invoice = Invoice::find($id);
        $dataAccept = $request->all();
        $dataAccept['status'] = 'accept';
        $dataAccept['estimate_arrive'] =  $request->input('estimate_arrive');;

        $invoice->update($dataAccept);
        $orders = Order::where('invoice_id', $invoice->id)->get();

        foreach ($orders as $order) {
            $order->update(['status' => 'accept']);
        }

        return redirect('/view-order')->with('success', 'Order has been accepted.');
    }

    public function updateStatus(Request $request, $id) {
        $invoice = Invoice::find($id);
    
        // Update status invoice dari input
        $invoice->status = $request->input('status');
        $invoice->save();
    
        // Ambil semua order terkait invoice
        $orders = Order::where('invoice_id', $invoice->id)->get();
    
        // Cek status invoice dan update status order
        foreach ($orders as $order) {
            if ($invoice->status == 'arrived') {
                $order->update(['status' => 'arrived']);
            } elseif ($invoice->status == 'done') {
                $order->update(['status' => 'done']);
            } else {
                $order->update(['status' => 'process']);
            }
        }
    
        // Redirect dengan pesan sukses
        return redirect()->back()->with('status', 'Status updated successfully');
    }
    
    public function kirimRevisi(Request $request)
    {
        // Validasi data yang diterima dari formulir
        $request->validate([
            'revisi' => 'required|string|max:255',
        ]);

        // Buat entri revisi baru
        $revisi = new Laporan([
            'isi_laporan' => $request->revisi,
            'type' => $request->type,
            'invoice_id' => $request->invoice_id,
        ]);
        
        // Simpan revisi ke dalam database
        $revisi->save();

        // Redirect ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Revisi berhasil dikirim.');
    }

    public function reject($id)
    {
        $invoice = Invoice::find($id);
        $invoice->update(['status' => 'reject']);
        $orders = Order::where('invoice_id', $invoice->id)->get();


        foreach ($orders as $order) {
            $order->update(['status' => 'reject']);
        }
        return redirect('/view-order')->with('success', 'Order has been rejected.');
    }
    
}