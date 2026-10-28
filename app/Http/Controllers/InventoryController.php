<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\SpesifikasiUjiMaterial;
use App\StockOut;
use App\Inventory;
use App\Manufacturers;
use App\SuplierMaterial;
use App\LogWebsite;

use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $month = $request->input('month') ?: date('m');
        $year = $request->input('year') ?: date('Y');

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();

        // Gunakan Eager Loading Aggregates (withSum) untuk menghindari N+1 Query
        $inventory = Inventory::with('specifications')
            ->withSum([
                'stockIn as stok_sebelumnya_in' => function ($q) use ($startDate) {
                    $q->whereDate('tanggal_masuk', '<', $startDate);
                }
            ], 'stok')
            ->withSum([
                'stockOut as stok_sebelumnya_out' => function ($q) use ($startDate) {
                    $q->whereDate('tanggal_keluar', '<', $startDate);
                }
            ], 'jumlah')
            ->withSum([
                'stockIn as stok_masuk' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal_masuk', $month)->whereYear('tanggal_masuk', $year);
                }
            ], 'stok')
            ->withSum([
                'stockOut as stok_keluar' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal_keluar', $month)->whereYear('tanggal_keluar', $year);
                }
            ], 'jumlah')
            ->get();

        $inventoryData = $inventory->map(function ($item) {
            $stokManual = $item->stok ?? 0;

            // Stok masuk & keluar sebelum bulan ini (historis)
            $stokSebelumnyaIn  = $item->stok_sebelumnya_in  ?? 0;
            $stokSebelumnyaOut = $item->stok_sebelumnya_out ?? 0;

            // Saldo awal bulan = stok manual awal sistem + semua in sebelum bulan ini - semua out sebelum bulan ini
            // CATATAN: $stokManual adalah saldo sebelum sistem digunakan, bukan bagian dari stock_in records
            $stokAwal = $stokManual + $stokSebelumnyaIn - $stokSebelumnyaOut;

            $stokMasuk  = $item->stok_masuk  ?? 0;
            $stokKeluar = $item->stok_keluar ?? 0;

            $stokAkhir = $stokAwal + $stokMasuk - $stokKeluar;

            return [
                'name'             => $item->name,
                'code'             => $item->code,
                'slug'             => $item->slug,
                'type'             => $item->type,
                'has_specifications' => $item->specifications->count() > 1,
                'stok_awal'        => max(0, $stokAwal),
                'stok_masuk'       => $stokMasuk,
                'stok_keluar'      => $stokKeluar,
                'stok_akhir'       => max(0, $stokAkhir),
            ];
        });

        // Urutan type
        $typeOrder = [
            'bahan baku',
            'bahan kemasan',
            'bahan kemas label',
        ];

        // Sorting berdasarkan urutan type
        $inventoryData = $inventoryData
            ->sortBy(function ($item) use ($typeOrder) {
                $type = strtolower(trim($item['type'] ?? ''));

                $position = array_search($type, $typeOrder);

                return $position === false ? PHP_INT_MAX : $position;
            })
            ->values();
        return view('inventory.index-inventory', compact('inventoryData', 'month', 'year', ));
    }


    function store(Request $request)
    {
        $dataStore = $request->all();
        $dataStore['slug'] = Str::slug($request->name);
        $inventory = Inventory::create($dataStore);



        return redirect('/local-inventory')->with('success', 'Bahan baku berhasil ditambahkan');
    }

    function detail($slug)
    {
        $inventory = Inventory::where('slug', $slug)->get();

        $inventoryData = $inventory->map(function ($item) {
            $stokAwal = $item->stok ?? 0;
            $stokMasuk = $item->stockIn->sum('stok');
            $stokKeluar = $item->stockOut->sum('jumlah');
            $stokAkhir = $stokAwal + $stokMasuk - $stokKeluar;

            return [
                'name' => $item->name,
                'code' => $item->code,
                'slug' => $item->slug,
                'type' => $item->type,
                'stok_awal' => $stokAwal,
                'stok_masuk' => $stokMasuk,
                'stok_keluar' => $stokKeluar,
                'stok_akhir' => $stokAkhir,
            ];
        });

        $idventory = Inventory::where('slug', $slug)->first();

        $calculatedData = $idventory->getCalculatedBatches();
        $stockIn = $calculatedData['stockIn'];
        $sisaStokAwal = $calculatedData['sisaStokAwal'];
        $stokAwal = $idventory->stok ?? 0;

        $batchTersedia = $stockIn
            ->groupBy('nomor_batch')
            ->map(function ($items) {
                return [
                    'nomor_batch' => $items->first()->nomor_batch ?? 'Tanpa Batch',
                    'total_stok' => $items->sum('stok'),
                    'sisa_stok' => $items->sum('sisa_stok_calculated'),
                    'tanggal_ed' => $items->min('tanggal_ed'),
                ];
            });

        if ($sisaStokAwal > 0 || $stokAwal > 0) {
            $existingTanpaBatch = $batchTersedia->firstWhere('nomor_batch', 'Tanpa Batch');
            if ($existingTanpaBatch) {
                $batchTersedia = $batchTersedia->map(function ($b) use ($sisaStokAwal, $stokAwal) {
                    if ($b['nomor_batch'] === 'Tanpa Batch') {
                        $b['total_stok'] += $stokAwal;
                        $b['sisa_stok'] += $sisaStokAwal;
                    }
                    return $b;
                });
            } else {
                $batchTersedia->push([
                    'nomor_batch' => 'Tanpa Batch',
                    'total_stok' => $stokAwal,
                    'sisa_stok' => $sisaStokAwal,
                    'tanggal_ed' => null
                ]);
            }
        }

        $batchTersedia = $batchTersedia->values();

        $stockOut = stockOut::where('inventory_id', $idventory->id)->get();

        return view('inventory.detail-inventory', compact('inventory', 'stockIn', 'stockOut', 'inventoryData', 'batchTersedia'));
    }

    public function edit($slug)
    {
        $material = Inventory::where('slug', $slug)->first();
        $suplier = SuplierMaterial::where('inventory_id', $material->id)->get();

        $manufacturers = Manufacturers::where('inventory_id', $material->id)->get();

        $specsByManufacturer = SpesifikasiUjiMaterial::where('inventory_id', $material->id)
            ->get()
            ->groupBy('manufacturer_id');
        return view('inventory.edit-inventory', compact('material', 'suplier', 'manufacturers', 'specsByManufacturer'));
    }

    public function update(Request $request, $slug)
    {
        $inventory = Inventory::where('slug', $slug)->firstOrFail();

        $dataStore = $request->except(['supplier_ids', 'supplier_names', 'supplier_addresses', '_token', '_method']);
        $dataStore['slug'] = Str::slug($request->name);
        $inventory->update($dataStore);

        if (Auth::user()->hasAnyRole(['Admin', 'Production Manager'])) {
            $ids = $request->input('supplier_ids', []);
            $names = $request->input('supplier_names', []);
            $addresses = $request->input('supplier_addresses', []);

            $keepIds = [];

            foreach ($names as $index => $name) {
                if (!empty($name)) {
                    $id = $ids[$index] ?? null;
                    $address = $addresses[$index] ?? null;

                    if (!empty($id)) {
                        // Update existing record
                        $supplier = SuplierMaterial::find($id);
                        if ($supplier && $supplier->inventory_id == $inventory->id) {
                            $supplier->update([
                                'name' => $name,
                                'address' => $address
                            ]);
                            $keepIds[] = $id;
                        }
                    } else {
                        // Create new record
                        $newSupplier = SuplierMaterial::create([
                            'inventory_id' => $inventory->id,
                            'name' => $name,
                            'address' => $address
                        ]);
                        $keepIds[] = $newSupplier->id;
                    }
                }
            }

            // Delete suppliers that were removed from the UI using Eloquent to trigger Trait
            $suppliersToDelete = SuplierMaterial::where('inventory_id', $inventory->id)
                ->whereNotIn('id', $keepIds)
                ->get();
                
            foreach ($suppliersToDelete as $sup) {
                $sup->delete();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bahan baku berhasil diubah',
                'new_slug' => $inventory->slug
            ]);
        }

        return redirect('local-inventory')->with('success', 'Bahan baku berhasil diubah');
    }

    public function storeManufacturer(Request $request, $slug)
    {
        $request->validate(['name' => 'required|string|max:255', 'address' => 'required|string|max:255']);
        $inventory = Inventory::where('slug', $slug)->firstOrFail();

        $manufacturer = Manufacturers::firstOrCreate([
            'name' => $request->name,
            'address' => $request->address,
            'inventory_id' => $inventory->id
        ]);

        // Buat data kosong yang valid, bukan "Init"
        $sectionName = $inventory->type == 'Bahan Baku' ? 'Organolepis' : 'Bahan Kemas';
        $parameterName = $inventory->type == 'Bahan Baku' ? 'Bentuk' : 'Parameter Default';

        SpesifikasiUjiMaterial::firstOrCreate([
            'inventory_id' => $inventory->id,
            'manufacturer_id' => $manufacturer->id,
            'section_name' => $sectionName,
            'parameter' => $parameterName,
        ], [
            'spesifikasi' => '-'
        ]);



        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produsen berhasil ditambahkan',
                'manufacturer_id' => $manufacturer->id
            ]);
        }

        return redirect()->back()->with('success', 'Produsen berhasil ditambahkan');
    }

    public function updateManufacturer(Request $request, $slug, $manufacturer_id)
    {
        $request->validate(['name' => 'required|string|max:255', 'address' => 'required|string|max:255']);

        $manufacturer = Manufacturers::findOrFail($manufacturer_id);
        $manufacturer->update(['name' => $request->name, 'address' => $request->address]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produsen berhasil diperbarui',
                'name' => $manufacturer->name,
                'address' => $manufacturer->address
            ]);
        }

        return redirect()->back()->with('success', 'Produsen berhasil diperbarui');
    }

    public function storeSpesifikasi(Request $request, $slug, $manufacturer_id)
    {
        $inventory = Inventory::where('slug', $slug)->firstOrFail();

        $sections = $request->input('section_name', []);
        $parameters = $request->input('parameter', []);
        $spesifikasis = $request->input('spesifikasi', []);


        foreach ($sections as $index => $section) {
            $parameter = $parameters[$index] ?? null;
            $spesifikasi = $spesifikasis[$index] ?? '';

            if ($section && $parameter) {
                if ($spesifikasi !== '') {
                    SpesifikasiUjiMaterial::updateOrCreate(
                        [
                            'inventory_id' => $inventory->id,
                            'manufacturer_id' => $manufacturer_id,
                            'section_name' => $section,
                            'parameter' => $parameter,
                        ],
                        [
                            'spesifikasi' => $spesifikasi
                        ]
                    );
                } else {
                    $specsToDelete = SpesifikasiUjiMaterial::where([
                        'inventory_id' => $inventory->id,
                        'manufacturer_id' => $manufacturer_id,
                        'section_name' => $section,
                        'parameter' => $parameter,
                    ])->get();
                    
                    foreach ($specsToDelete as $spec) {
                        $spec->delete();
                    }
                }
            }
        }

        // Memastikan minimal ada 1 data terikat agar tab produsen tetap muncul di UI
        $check = SpesifikasiUjiMaterial::where('inventory_id', $inventory->id)
            ->where('manufacturer_id', $manufacturer_id)
            ->count();

        if ($check == 0) {
            $sectionName = $inventory->type == 'Bahan Baku' ? 'Organolepis' : 'Bahan Kemas';
            $parameterName = $inventory->type == 'Bahan Baku' ? 'Bentuk' : 'Parameter Default';

            SpesifikasiUjiMaterial::create([
                'inventory_id' => $inventory->id,
                'manufacturer_id' => $manufacturer_id,
                'section_name' => $sectionName,
                'parameter' => $parameterName,
                'spesifikasi' => '-'
            ]);
        }



        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Spesifikasi berhasil disimpan'
            ]);
        }

        return redirect()->back()->with('success', 'Spesifikasi berhasil disimpan');
    }

    public function deleteSpesifikasi(Request $request, $slug, $manufacturer_id)
    {
        $inventory = Inventory::where('slug', $slug)->firstOrFail();
        $manufacturer = Manufacturers::findOrFail($manufacturer_id);
        $mfgName = $manufacturer->name;

        SpesifikasiUjiMaterial::where('inventory_id', $inventory->id)
            ->where('manufacturer_id', $manufacturer_id)
            ->delete();

        $manufacturer->delete();



        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produsen dan spesifikasi berhasil dihapus'
            ]);
        }

        return redirect()->back()->with('success', 'Produsen dan spesifikasi berhasil dihapus dari material ini');
    }

    public function delete($id)
    {
        $inventory = Inventory::findOrFail($id);
        Manufacturers::where('inventory_id', $inventory->id)->delete();
        SpesifikasiUjiMaterial::where('inventory_id', $inventory->id)->delete();
        SuplierMaterial::where('inventory_id', $inventory->id)->delete();
        $inventory->delete();

        return redirect('/local-inventory')->with('success', 'Bahan baku berhasil dihapus');

    }
}