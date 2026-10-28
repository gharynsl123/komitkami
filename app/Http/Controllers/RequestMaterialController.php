<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Produksi;
use App\StockIn;
use App\StockOut;
use App\MaterialProduksi;

class RequestMaterialController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $permintaan_produksi = MaterialProduksi::where('persetujuan', 'pending')
            ->whereNotNull('produksi_id')
            ->with(['bahanbaku', 'bahankemas', 'produksi.product', 'inventory'])
            ->get()
            ->groupBy('produksi.batch_number');

        $permintaan_umum = MaterialProduksi::where('persetujuan', 'pending')
            ->whereNull('produksi_id')
            ->with('inventory')
            ->get()
            ->groupBy(function ($item) {
                return $item->keterangan . '--' . $item->created_at->format('dmyHi');
            });

        return view('formulir-permintaan.index-permintaan', compact('permintaan_produksi', 'permintaan_umum'));
    }

    function showAll(Request $request) {

        if($request->start_date && $request->end_date) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        } else {
            $start_date = null;
            $end_date = null;
        }

        $permintaan_produksi = $start_date && $end_date 
            ? MaterialProduksi::whereNotIn('persetujuan', ['pending'])
                ->whereNotNull('produksi_id')
                ->whereBetween('tanggal_di_proses', [$start_date, $end_date])
                ->with([
                    'bahanbaku',
                    'bahankemas',
                    'produksi.product',
                'inventory'
            ])
            ->get()
            ->groupBy('produksi.batch_number')
            : MaterialProduksi::whereNotIn('persetujuan', ['pending'])
                ->whereNotNull('produksi_id')
                ->with([
                    'bahanbaku',
                    'bahankemas',
                    'produksi.product',
                    'inventory'
                ])
                ->get()
                ->groupBy('produksi.batch_number');

        $permintaan_umum = $start_date && $end_date
            ? MaterialProduksi::whereNotIn('persetujuan', ['pending'])
                ->whereNull('produksi_id')
                ->whereBetween('tanggal_di_proses', [$start_date, $end_date])
                ->with('inventory')
                ->get() 
                ->groupBy(function ($item) {
                    return $item->keterangan . '--' . $item->created_at->format('dmyHi');
                })
            : MaterialProduksi::whereNotIn('persetujuan', ['pending'])
                ->whereNull('produksi_id')
                ->with('inventory')
                ->get() 
                ->groupBy(function ($item) {
                    return $item->keterangan . '--' . $item->created_at->format('dmyHi');
                });

        return view('formulir-permintaan.show-all', compact('permintaan_produksi', 'permintaan_umum'));
    }

    public function proses($type, $identifier) {
        $produksi = null;
        $requestData = null;
        $stockInData = null;

        if ($type === 'produksi') {
            $produksi = Produksi::where('batch_number', $identifier)->first();
            $requestData = MaterialProduksi::where('produksi_id', $produksi->id)
                ->with('inventory')
                ->get();

            $inventoryIds = $requestData->pluck('inventory_id')->toArray();
            $stockIn = StockIn::whereIn('inventory_id', $inventoryIds)
                ->withSum('stockOuts as total_keluar', 'jumlah')
                ->orderBy('tanggal_ed', 'asc')
                ->get();

            $stockInData = $stockIn->map(function ($item) {
                $item->available = $item->stok - ($item->total_keluar ?? 0);
                return $item;
            })->where('available', '>', 0)
            ->groupBy('inventory_id');
        }else{
            // 1. Pecah identifier
            $parts = explode('--', $identifier);
            $keterangan = $parts[0];
            $timeInput = isset($parts[1]) ? $parts[1] : null;

            // 2. Query dasar
            $query = MaterialProduksi::where('keterangan', $keterangan)
                ->whereNull('produksi_id')
                ->with(['inventory', 'stockOuts']);

            if ($timeInput) {
                $query->whereRaw("DATE_FORMAT(created_at, '%d%m%y%H%i') = ?", [$timeInput]);
            }

            $requestData = $query->get();

            $inventoryIds = $requestData->pluck('inventory_id')->unique()->toArray();

            $stockIn = StockIn::whereIn('inventory_id', $inventoryIds)
                ->withSum('stockOuts as total_keluar', 'jumlah') 
                ->orderBy('tanggal_ed', 'asc')
                ->get();

            $stockInData = $stockIn->map(function ($item) {
                $item->available = $item->stok - ($item->total_keluar ?? 0);
                return $item;
            })->where('available', '>', 0)
            ->groupBy('inventory_id');
        }

        return view('formulir-permintaan.proses-permintaan', compact('produksi', 'requestData', 'stockInData'));
    }

    public function detail($type, $identifier) {
        $produksi = null;
        $requestData = null;

        if ($type === 'produksi') {
            $produksi = Produksi::where('batch_number', $identifier)->firstOrFail();

            $requestData = MaterialProduksi::where('produksi_id', $produksi->id)
                ->with(['inventory', 'stockOuts.stockIn'])
                ->get();

        } else {
            $parts = explode('--', $identifier);
            $keterangan = $parts[0];
            $timeInput = isset($parts[1]) ? $parts[1] : null;

            $query = MaterialProduksi::where('keterangan', $keterangan)
                ->whereNull('produksi_id')
                ->with(['inventory', 'stockOuts']);

            if ($timeInput) {
                $query->whereRaw("DATE_FORMAT(created_at, '%d%m%y%H%i') = ?", [$timeInput]);
            }

            $requestData = $query->get();
        }
        return view('formulir-permintaan.detail-permintaan', compact('produksi', 'requestData'));
    }

    public function storePenyerahan(Request $request) {
        $permintaanIds = $request->permintaan_id;
        $inventoryIds  = $request->inventory_id;
        $jumlahList    = $request->jumlah;
        $batchList     = $request->stock_in_id;
        $keterangan    = $request->keterangan;

        foreach ($permintaanIds as $index => $permintaanId) {

            $needed      = floatval(str_replace(',', '.', $jumlahList[$index]));
            $inventoryId = $inventoryIds[$index];

            $selectedBatchIds = $batchList[$index] ?? [];

            if (!is_array($selectedBatchIds)) {
                $selectedBatchIds = [$selectedBatchIds];
            }

            if (empty($selectedBatchIds)) {
                StockOut::create([
                    'permintaan_id'  => $permintaanId,
                    'inventory_id'   => $inventoryId,
                    'stock_in_id'    => null, // <- penting
                    'tanggal_keluar' => now(),
                    'user_id'        => Auth::id(),
                    'jumlah'         => $needed,
                    'keterangan'     => $keterangan,
                ]);

            } 
            else {
                $stockInList = StockIn::whereIn('id', $selectedBatchIds)
                    ->orderBy('tanggal_ed', 'asc')
                    ->get()
                    ->map(function ($item) {
                        $totalOut = StockOut::where('stock_in_id', $item->id)->sum('jumlah');
                        $item->available = $item->stok - $totalOut;
                        return $item;
                    });

                $allocations = $this->allocateFIFO($stockInList, $needed);

                foreach ($allocations as $row) {
                    StockOut::create([
                        'permintaan_id'  => $permintaanId,
                        'inventory_id'   => $inventoryId,
                        'stock_in_id'    => $row['stock_in_id'],
                        'tanggal_keluar' => now(),
                        'user_id'        => Auth::id(),
                        'jumlah'         => $row['jumlah'],
                        'keterangan'     => $keterangan,
                    ]);
                }
            }

            MaterialProduksi::where('id', $permintaanId)
                ->update(['persetujuan' => 'done', 'tanggal_di_proses' => now()->toDateString()]);
        }

        return redirect('/permintaan-material')
            ->with('success', 'Penyerahan berhasil diproses.');
    }

    public function RequestMaterialPreview(Request $request) {
        $tanggal = $request->tanggal;
        $query = Produksi::with('product');
        
        // nilai default bulannya adalah bulan ini
        if (!$tanggal) {
            $tanggal = Carbon::now()->toDateString();
        }

        $produksi = $query->whereHas('materialProduksi', function($q) {
                $q->where('persetujuan', 'done');
            })
            ->where(function($q) use ($tanggal) {
                $q->whereDate('created_at', $tanggal)
                  ->orWhereHas('materialProduksi', function($q2) use ($tanggal) {
                      $q2->whereDate('created_at', $tanggal);
                  });
            })
            ->get();

        $permintaan_umum = MaterialProduksi::whereNull('produksi_id')
            ->where('persetujuan', 'done')
            ->whereDate('created_at', $tanggal)
            ->with('inventory')
            ->get()
            ->groupBy(function ($item) {
                return $item->keterangan . '--' . $item->created_at->format('dmyHi');
            });

        return view('formulir-permintaan.cetak-laporan', compact('produksi', 'permintaan_umum', 'tanggal'));
    }

    private function allocateFIFO($stockInList, $needed) {
        $result = [];
        $remaining = $needed;

        foreach ($stockInList as $stock) {
            if ($remaining <= 0) break;

            $available = floatval($stock->available);

            if ($available <= 0) continue;

            $take = min($available, $remaining);

            if ($take > 0) {
                $result[] = [
                    'stock_in_id' => $stock->id,
                    'jumlah'      => $take,
                ];
            }

            $remaining -= $take;
        }

        return $result;
    }

}