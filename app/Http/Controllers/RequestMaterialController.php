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

            $inventoryIds = $requestData->pluck('inventory_id')->unique()->toArray();
            $stockInData = $this->getAvailableStockBatches($inventoryIds);
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
            $stockInData = $this->getAvailableStockBatches($inventoryIds);
        }

        return view('formulir-permintaan.proses-permintaan', compact('produksi', 'requestData', 'stockInData'));
    }

    private function getAvailableStockBatches($inventoryIds) {
        $stockInData = [];
        $inventories = \App\Inventory::whereIn('id', $inventoryIds)->get();

        foreach ($inventories as $inv) {
            if ($inv->type === 'Bahan Baku') {
                $calculatedData = $inv->getCalculatedBatches();
                $stockInList = $calculatedData['stockIn'];
                $sisaStokAwal = (float)($calculatedData['sisaStokAwal'] ?? 0);

                // Group by nomor_batch
                $grouped = $stockInList->groupBy(function ($item) {
                    return trim((string)($item->nomor_batch ?? 'Tanpa Batch'));
                })->map(function ($items, $batchNum) {
                    $activeItems = $items->filter(fn($x) => ($x->sisa_stok_calculated ?? 0) > 0);
                    $totalAvailable = (float) $activeItems->sum('sisa_stok_calculated');
                    $ids = $activeItems->pluck('id')->toArray();
                    $minEd = $items->whereNotNull('tanggal_ed')->min('tanggal_ed');

                    return (object) [
                        'id'          => implode(',', $ids),
                        'nomor_batch' => $batchNum,
                        'available'   => $totalAvailable,
                        'tanggal_ed'  => $minEd,
                    ];
                })->filter(fn($b) => $b->available > 0)
                  ->sortBy(function ($b) {
                      return $b->tanggal_ed ? strtotime($b->tanggal_ed) : PHP_INT_MAX;
                  })->values();

                if ($sisaStokAwal > 0) {
                    $grouped->prepend((object) [
                        'id'          => 'null',
                        'nomor_batch' => 'Stok Awal (Tanpa Batch)',
                        'available'   => $sisaStokAwal,
                        'tanggal_ed'  => null,
                    ]);
                }

                $stockInData[$inv->id] = $grouped;
            } else {
                $totalAvailable = (float) $inv->getTotalStok();
                $stockInData[$inv->id] = collect([
                    (object) [
                        'id'          => null,
                        'nomor_batch' => '-',
                        'available'   => $totalAvailable,
                        'tanggal_ed'  => null,
                    ]
                ]);
            }
        }

        return $stockInData;
    }

    private function parseNumberInput($val) {
        if (is_null($val) || $val === '') return 0.0;
        $val = trim((string) $val);
        if (preg_match('/^\d{1,3}(\.\d{3})+$/', $val)) {
            $val = str_replace('.', '', $val);
        } elseif (strpos($val, '.') !== false && strpos($val, ',') !== false) {
            $val = str_replace('.', '', $val);
            $val = str_replace(',', '.', $val);
        } elseif (strpos($val, ',') !== false) {
            $val = str_replace(',', '.', $val);
        }
        return (float) $val;
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

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($permintaanIds as $index => $permintaanId) {

                $rawJumlah   = $jumlahList[$index] ?? 0;
                $needed      = $this->parseNumberInput($rawJumlah);
                $inventoryId = $inventoryIds[$index];
                $inventory   = \App\Inventory::findOrFail($inventoryId);

                if ($needed <= 0) {
                    continue;
                }

                $selectedBatchValues = $batchList[$index] ?? [];

                if (!is_array($selectedBatchValues)) {
                    $selectedBatchValues = [$selectedBatchValues];
                }

                $selectedBatchIds = [];
                $hasNullBatch = false;
                foreach ($selectedBatchValues as $val) {
                    if ($val === 'null' || $val === null || $val === '') {
                        $hasNullBatch = true;
                    } elseif (is_string($val) && strpos($val, ',') !== false) {
                        foreach (explode(',', $val) as $singleId) {
                            $trimmed = trim($singleId);
                            if (is_numeric($trimmed)) {
                                $selectedBatchIds[] = (int) $trimmed;
                            }
                        }
                    } elseif (is_numeric($val)) {
                        $selectedBatchIds[] = (int) $val;
                    }
                }
                $selectedBatchIds = array_unique(array_filter($selectedBatchIds));

                if (empty($selectedBatchIds)) {
                    StockOut::create([
                        'permintaan_id'  => $permintaanId,
                        'inventory_id'   => $inventoryId,
                        'stock_in_id'    => null, // Non-Bahan Baku atau Stok Awal Manual
                        'tanggal_keluar' => now(),
                        'user_id'        => Auth::id(),
                        'jumlah'         => $needed,
                        'keterangan'     => $keterangan,
                    ]);
                }
                else {
                    $calculatedData = $inventory->getCalculatedBatches();
                    $calculatedStockIns = $calculatedData['stockIn']->keyBy('id');

                    $stockInList = StockIn::whereIn('id', $selectedBatchIds)
                        ->orderBy('tanggal_ed', 'asc')
                        ->orderBy('id', 'asc')
                        ->get()
                        ->map(function ($item) use ($calculatedStockIns) {
                            if (isset($calculatedStockIns[$item->id])) {
                                $item->available = (float) $calculatedStockIns[$item->id]->sisa_stok_calculated;
                            } else {
                                $totalOut = StockOut::where('stock_in_id', $item->id)->sum('jumlah');
                                $item->available = max(0, (float)($item->stok - $totalOut));
                            }
                            return $item;
                        });

                    $totalAvailable = (float) $stockInList->sum('available');

                    if ($totalAvailable < $needed) {
                        throw new \Exception("Stok pada batch yang dipilih untuk '{$inventory->name}' tidak mencukupi! Dibutuhkan: " . formatJumlah($needed) . " gram, tersedia pada batch terpilih: " . formatJumlah($totalAvailable) . " gram.");
                    }

                    $allocations = $this->allocateFIFO($stockInList, $needed);
                    $totalAllocated = collect($allocations)->sum('jumlah');

                    if (round($totalAllocated, 3) < round($needed, 3)) {
                        throw new \Exception("Alokasi batch untuk '{$inventory->name}' tidak mencukupi seluruh jumlah penyerahan!");
                    }

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

            \Illuminate\Support\Facades\DB::commit();

            return redirect('/permintaan-material')
                ->with('success', 'Penyerahan berhasil diproses.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
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
