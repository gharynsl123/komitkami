<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Inventory;
use App\StockIn;
use App\StockOut;
use App\Product;
use App\StockOpname;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockOpnameExport;

class StockOpnameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $inventories = Inventory::all();
        $products = Product::all();
        return view('stock-opname.index-stock-opname', compact('inventories', 'products'));
    }

    public function getBatches($type, $id)
    {
        if ($type === 'inventory') {
            $inventory = Inventory::findOrFail($id);
            $calculatedData = $inventory->getCalculatedBatches();
            $stockIn = $calculatedData['stockIn'];

            $batches = $stockIn->map(function ($item) {
                $item->available = $item->sisa_stok_calculated;
                $item->tanggal_ed_formatted = $item->tanggal_ed
                    ? Carbon::parse($item->tanggal_ed)->format('d-m-Y')
                    : null;
                return $item;
            })->where('available', '>=', 0)->values();

            // Bahan kemas → kembalikan satu baris aggregate (tidak perlu grouping per batch)
            if ($inventory->type !== 'Bahan Baku') {
                $totalAvailable = $batches->sum('available');
                return response()->json([[
                    'id'                  => '',
                    'batch_ids_json'      => '[]',
                    'nomor_batch'         => 'Bahan Kemasan / Label (Running Stock)',
                    'available'           => $totalAvailable,
                    'tanggal_ed_formatted'=> null,
                    'is_kemas'            => true,
                ]]);
            }

            // Bahan baku → group by nomor_batch agar satu batch = satu baris opname
            $grouped = $batches
                ->groupBy(fn($b) => $b->nomor_batch ?? '__TANPA_BATCH__')
                ->map(function ($items, $key) {
                    $batchName      = $key === '__TANPA_BATCH__' ? null : $key;
                    $primaryItem    = $items->first(); // entry terlama (sudah diurutkan FIFO)
                    $totalAvailable = $items->sum('available');
                    $allIds         = $items->pluck('id')->toArray();

                    // Ambil tanggal ED terpendek (paling dekat expire) dari group ini
                    $earliestEd = $items->filter(fn($i) => !is_null($i->tanggal_ed))
                        ->sortBy('tanggal_ed')->first();

                    return [
                        'id'                  => $primaryItem->id,     // primary ID untuk opname record
                        'batch_ids_json'      => json_encode($allIds),  // semua ID dalam group
                        'nomor_batch'         => $batchName,
                        'available'           => $totalAvailable,
                        'entry_count'         => count($allIds),        // berapa kali batch ini masuk
                        'tanggal_ed_formatted'=> $earliestEd
                            ? Carbon::parse($earliestEd->tanggal_ed)->format('d-m-Y')
                            : null,
                    ];
                })->values();

            return response()->json($grouped);

        } else if ($type === 'product') {
            $product = Product::findOrFail($id);
            $calculatedData = $product->getCalculatedBatches();
            $stockIn = $calculatedData['stockIn'];

            $batches = $stockIn->map(function ($item) {
                $item->available   = $item->sisa_stok_calculated;
                $item->nomor_batch = $item->batch_number
                    ?? optional($item->produksi)->batch_number;
                $item->tanggal_ed_formatted = ($item->expired_date
                    ? Carbon::parse($item->expired_date)->format('d-m-Y')
                    : null)
                    ?? (optional($item->produksi)->tanggal_expired
                        ? Carbon::parse($item->produksi->tanggal_expired)->format('d-m-Y')
                        : null);
                return $item;
            })->where('available', '>=', 0)->values();

            // Produk jadi → group by batch_number
            $grouped = $batches
                ->groupBy(fn($b) => $b->nomor_batch ?? '__TANPA_BATCH__')
                ->map(function ($items, $key) {
                    $batchName      = $key === '__TANPA_BATCH__' ? null : $key;
                    $primaryItem    = $items->first();
                    $totalAvailable = $items->sum('available');
                    $allIds         = $items->pluck('id')->toArray();
                    $firstWithEd    = $items->firstWhere('tanggal_ed_formatted', '!=', null);

                    return [
                        'id'                  => $primaryItem->id,
                        'batch_ids_json'      => json_encode($allIds),
                        'nomor_batch'         => $batchName,
                        'available'           => $totalAvailable,
                        'entry_count'         => count($allIds),
                        'tanggal_ed_formatted'=> $firstWithEd ? $firstWithEd->tanggal_ed_formatted : null,
                    ];
                })->values();

            return response()->json($grouped);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_selection' => 'required|string',
            'batches'        => 'required|array',
            'new_batches'    => 'nullable|array',
            'keterangan'     => 'nullable|string',
        ]);

        $parts    = explode('_', $request->item_selection);
        $itemType = $parts[0];
        $itemId   = $parts[1];

        $tanggal_opname = Carbon::now()->toDateString();
        $totalPerubahan = 0;

        // ── Helper: normalisasi string angka (handle koma & titik) ────────────
        $toFloat = fn($v) => (float) str_replace(',', '.', (string) ($v ?? 0));

        // ── Proses batch yang sudah ada di sistem ─────────────────────────────
        foreach ($request->batches as $batch) {
            $selisih    = $toFloat($batch['selisih']);
            $stokSistem = $toFloat($batch['stok_sistem'] ?? 0);
            $stokFisik  = $toFloat($batch['stok_fisik']  ?? 0);
            $batchId    = !empty($batch['batch_id']) ? $batch['batch_id'] : null;

            // Hitung ulang selisih dari nilai yang sudah dinormalisasi (double check)
            $selisihRecalc = round($stokFisik - $stokSistem, 3);
            // Gunakan selisih recalculated jika ada perbedaan kecil akibat floating point
            if (abs($selisihRecalc - $selisih) > 0.001) {
                $selisih = $selisihRecalc;
            }

            // Skip batch yang tidak mengalami perubahan
            if (abs($selisih) < 0.001) {
                continue;
            }

            $totalPerubahan++;

            // Simpan record opname
            $opname = StockOpname::create([
                'inventory_id'        => $itemType == 'inventory' ? $itemId : null,
                'product_id'          => $itemType == 'product'   ? $itemId : null,
                'stock_in_id'         => $itemType == 'inventory' ? $batchId : null,
                'stock_in_product_id' => $itemType == 'product'   ? $batchId : null,
                'stok_sistem'         => $stokSistem,
                'stok_fisik'          => $stokFisik,
                'selisih'             => $selisih,
                'tanggal_opname'      => $tanggal_opname,
                'keterangan'          => $request->keterangan,
                'user_id'             => Auth::id(),
            ]);

            // Proses penyesuaian stok berdasarkan selisih
            if ($selisih > 0) {
                // Stok kurang dari fisik → tambah stok masuk (koreksi positif)
                if ($itemType == 'inventory') {
                    $originalBatch = $batchId ? StockIn::find($batchId) : null;
                    StockIn::create([
                        'inventory_id'       => $itemId,
                        'vendor'             => 'Penyesuaian Stock Opname',
                        'nomor_batch'        => ($originalBatch ? $originalBatch->nomor_batch : null) ?: ($batch['nomor_batch'] ?? null),
                        'nomor_sj'           => 'OPNAME-' . $opname->id,
                        'stok'               => $selisih,
                        'kategori'           => 'stock_opname',
                        'parent_stock_in_id' => $batchId,
                        'tanggal_masuk'      => $tanggal_opname,
                        'tanggal_ed'         => $originalBatch
                            ? $originalBatch->tanggal_ed
                            : Carbon::now()->addYear()->toDateString(),
                    ]);
                } else {
                    $originalBatch = $batchId ? \App\StockInProduct::find($batchId) : null;
                    \App\StockInProduct::create([
                        'product_id'                 => $itemId,
                        'produksi_id'                => null,
                        'batch_number'               => ($originalBatch
                            ? ($originalBatch->batch_number ?? optional($originalBatch->produksi)->batch_number)
                            : null) ?: ($batch['nomor_batch'] ?? null),
                        'quantity'                   => $selisih,
                        'expired_date'               => $originalBatch
                            ? ($originalBatch->expired_date ?? optional($originalBatch->produksi)->tanggal_expired)
                            : Carbon::now()->addYear()->toDateString(),
                        'note'                       => 'Penyesuaian Stock Opname - #' . $opname->id,
                        'kategori'                   => 'stock_opname',
                        'parent_stock_in_product_id' => $batchId,
                    ]);
                }
            } elseif ($selisih < 0) {
                // Stok lebih dari fisik → kurangi (koreksi negatif)
                // Distribusikan pengurangan secara FIFO ke SEMUA entry dalam group (batch_ids_json)
                $allBatchIds = [];
                if (!empty($batch['batch_ids_json'])) {
                    $allBatchIds = json_decode($batch['batch_ids_json'], true) ?? [];
                }
                // Fallback ke single batchId jika batch_ids_json tidak ada
                if (empty($allBatchIds) && $batchId) {
                    $allBatchIds = [$batchId];
                }

                $remaining = abs($selisih);

                if ($itemType == 'inventory') {
                    foreach ($allBatchIds as $bid) {
                        if ($remaining <= 0.001) break;

                        $entry = StockIn::find($bid);
                        if (!$entry) continue;

                        // Hitung available untuk entry ini beserta child corrections-nya
                        $childIds         = StockIn::where('parent_stock_in_id', $bid)->pluck('id')->toArray();
                        $entryStok        = $entry->stok + StockIn::whereIn('id', $childIds)->sum('stok');
                        $entryOut         = StockOut::whereIn('stock_in_id', array_merge([$bid], $childIds))->sum('jumlah');
                        $entryAvailable   = max(0, $entryStok - $entryOut);

                        if ($entryAvailable <= 0.001) continue;

                        $toDeduct  = min($remaining, $entryAvailable);
                        $remaining = round($remaining - $toDeduct, 3);

                        StockOut::create([
                            'inventory_id'   => $itemId,
                            'stock_in_id'    => $bid,
                            'user_id'        => Auth::id(),
                            'jumlah'         => $toDeduct,
                            'tanggal_keluar' => $tanggal_opname,
                            'keterangan'     => 'Penyesuaian Stock Opname - #' . $opname->id,
                            'kategori'       => 'stock_opname',
                        ]);
                    }

                    // Safety fallback jika masih ada sisa
                    if ($remaining > 0.001 && !empty($allBatchIds)) {
                        $lastBid = end($allBatchIds);
                        StockOut::create([
                            'inventory_id'   => $itemId,
                            'stock_in_id'    => $lastBid,
                            'user_id'        => Auth::id(),
                            'jumlah'         => $remaining,
                            'tanggal_keluar' => $tanggal_opname,
                            'keterangan'     => 'Penyesuaian Stock Opname - #' . $opname->id,
                            'kategori'       => 'stock_opname',
                        ]);
                    }
                } else {
                    // Produk jadi
                    foreach ($allBatchIds as $bid) {
                        if ($remaining <= 0.001) break;

                        $entry = \App\StockInProduct::find($bid);
                        if (!$entry) continue;

                        $childIds       = \App\StockInProduct::where('parent_stock_in_product_id', $bid)->pluck('id')->toArray();
                        $entryStok      = $entry->quantity + \App\StockInProduct::whereIn('id', $childIds)->sum('quantity');
                        $entryOut       = \App\StockOutProduct::whereIn('stock_in_product_id', array_merge([$bid], $childIds))->sum('quantity');
                        $entryAvailable = max(0, $entryStok - $entryOut);

                        if ($entryAvailable <= 0.001) continue;

                        $toDeduct  = min($remaining, $entryAvailable);
                        $remaining = round($remaining - $toDeduct, 3);

                        \App\StockOutProduct::create([
                            'product_id'          => $itemId,
                            'stock_in_product_id' => $bid,
                            'delivery_item_id'    => null,
                            'quantity'            => $toDeduct,
                            'note'                => 'Penyesuaian Stock Opname - #' . $opname->id,
                            'kategori'            => 'stock_opname',
                        ]);
                    }

                    // Safety fallback jika masih ada sisa
                    if ($remaining > 0.001 && !empty($allBatchIds)) {
                        $lastBid = end($allBatchIds);
                        \App\StockOutProduct::create([
                            'product_id'          => $itemId,
                            'stock_in_product_id' => $lastBid,
                            'delivery_item_id'    => null,
                            'quantity'            => $remaining,
                            'note'                => 'Penyesuaian Stock Opname - #' . $opname->id,
                            'kategori'            => 'stock_opname',
                        ]);
                    }
                }
            }
        }

        // ── Proses batch BARU yang tidak ada di sistem ────────────────────────
        if ($request->has('new_batches') && is_array($request->new_batches)) {
            foreach ($request->new_batches as $newBatch) {
                $nomorBatch = trim($newBatch['nomor_batch'] ?? '');
                $stokFisik  = $toFloat($newBatch['stok_fisik'] ?? 0);
                $tanggalEd  = !empty($newBatch['tanggal_ed']) ? $newBatch['tanggal_ed'] : null;

                // Skip jika nomor batch kosong atau stok = 0
                if ($nomorBatch === '' || $stokFisik <= 0) {
                    continue;
                }

                $totalPerubahan++;

                // Buat StockIn baru sebagai stok opname (bukan incoming_material)
                if ($itemType == 'inventory') {
                    $newStockIn = StockIn::create([
                        'inventory_id' => $itemId,
                        'vendor'       => 'Penyesuaian Stock Opname (Batch Baru)',
                        'nomor_batch'  => $nomorBatch,
                        'nomor_sj'     => 'OPNAME-NEW-' . now()->format('YmdHis'),
                        'stok'         => $stokFisik,
                        'kategori'     => 'stock_opname',
                        'tanggal_masuk'=> $tanggal_opname,
                        'tanggal_ed'   => $tanggalEd ?? Carbon::now()->addYear()->toDateString(),
                        'user_id'      => Auth::id(),
                    ]);

                    // Catat ke opname sebagai tambahan (stok_sistem = 0, stok_fisik = nilai input)
                    StockOpname::create([
                        'inventory_id'  => $itemId,
                        'product_id'    => null,
                        'stock_in_id'   => $newStockIn->id,
                        'stok_sistem'   => 0,
                        'stok_fisik'    => $stokFisik,
                        'selisih'       => $stokFisik,
                        'tanggal_opname'=> $tanggal_opname,
                        'keterangan'    => '[Batch Baru] ' . ($request->keterangan ?? ''),
                        'user_id'       => Auth::id(),
                    ]);

                } else {
                    // Produk jadi — batch baru
                    $newStockInProduct = \App\StockInProduct::create([
                        'product_id'   => $itemId,
                        'produksi_id'  => null,
                        'batch_number' => $nomorBatch,
                        'quantity'     => $stokFisik,
                        'expired_date' => $tanggalEd ?? Carbon::now()->addYear()->toDateString(),
                        'note'         => 'Batch Baru dari Stock Opname',
                        'kategori'     => 'stock_opname',
                    ]);

                    StockOpname::create([
                        'inventory_id'        => null,
                        'product_id'          => $itemId,
                        'stock_in_product_id' => $newStockInProduct->id,
                        'stok_sistem'         => 0,
                        'stok_fisik'          => $stokFisik,
                        'selisih'             => $stokFisik,
                        'tanggal_opname'      => $tanggal_opname,
                        'keterangan'          => '[Batch Baru] ' . ($request->keterangan ?? ''),
                        'user_id'             => Auth::id(),
                    ]);
                }
            }
        }

        // Guard: tidak ada perubahan sama sekali
        if ($totalPerubahan === 0) {
            return redirect()->back()
                ->with('error', 'Tidak ada perubahan stok yang ditemukan. Pastikan stok fisik berbeda dari stok sistem atau isi batch baru.');
        }

        return redirect()->route('stock-opname.history')
            ->with('success', $totalPerubahan . ' batch berhasil disimpan dan stok disesuaikan.');
    }


    public function history()
    {
        $histories = StockOpname::with(['inventory', 'stockIn', 'user', 'product', 'stockInProduct'])
            ->select('tanggal_opname')
            ->distinct()
            ->orderBy('tanggal_opname', 'desc')
            ->get();
            
        $data = StockOpname::with(['inventory', 'stockIn', 'user', 'product', 'stockInProduct'])->orderBy('created_at', 'desc')->get();
        return view('stock-opname.history', compact('histories', 'data'));
    }

    public function printReport(Request $request)
    {
        $request->validate([
            'tanggal_opname' => 'required|date',
            'tipe_barang' => 'required|string',
            'format_cetak' => 'required|in:pdf,excel',
        ]);

        $tanggal = $request->tanggal_opname;
        $tipe = $request->tipe_barang;
        $format = $request->format_cetak;

        // formated date for title
        $tanggal_formated = Carbon::parse($tanggal)->format('d_m_Y');
        // end formated date for title

        if ($tipe === 'Produk Jadi') {
             $data = StockOpname::with(['product', 'stockInProduct', 'user'])
                ->whereDate('tanggal_opname', $tanggal)
                ->whereNotNull('product_id')
                ->get();
        } else {
             $data = StockOpname::with(['inventory', 'stockIn', 'user'])
                ->whereDate('tanggal_opname', $tanggal)
                ->whereHas('inventory', function($q) use ($tipe) {
                    if ($tipe === 'Bahan Kemas') {
                        $q->where('type', '!=', 'Bahan Baku');
                    } else {
                        $q->where('type', $tipe);
                    }
                })
                ->get(); 
        }

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data stock opname untuk filter tersebut.');
        }

        if ($format == 'excel') {
            return Excel::download(new StockOpnameExport($data, $tipe, $tanggal), 'Laporan_Stock_Opname.xlsx');
        } else {
            if ($tipe === 'Produk Jadi') {
                $viewName = 'stock-opname.reports.produk-jadi';
            } else {
                $viewName = $tipe == 'Bahan Baku' ? 'stock-opname.reports.bahan-baku' : 'stock-opname.reports.bahan-kemas';
            }
            $pdf = Pdf::loadView($viewName, compact('data', 'tanggal'))->setPaper('a4', 'landscape');
            return $pdf->download('Laporan_Stock_Opname.pdf');
        }
    }
}