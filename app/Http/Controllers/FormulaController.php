<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Inventory;
use App\FormulaBahanBaku;
use App\FormulaBahanKemas;
use App\TahapanProses;
use App\AcuanLaporanIpc;
use App\QualityControl;
use App\Formula;
use App\SerahTerimaBarang;
use App\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class FormulaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function index() {
        $inventory = Inventory::all();
        $formulaBahanBaku = Formula::with('bahanBaku')
                            ->where('tipe', 'bahan_baku')
                            ->orderBy('nama_formula', 'asc')
                            ->get();
        $formulaKemasan = Formula::with('bahanKemas')
                            ->where('tipe', 'bahan_kemas')
                            ->orderBy('nama_formula', 'asc')
                            ->get();

        return view('formula.index-formula', compact('inventory', 'formulaBahanBaku', 'formulaKemasan'));
    }

    function create() {
        $inventory = Inventory::all();
        $products = Product::all();
        return view('formula.create-formula', compact('inventory', 'products'));
    }

    public function store(Request $request)
    {
        $inventory_ids = $request->input('inventory_id', []);
        $templateName = $request->input('nama_template');
        $nama_bahan_bakus = $request->input('nama_bahan_baku', []);
        $jumlahs = $request->input('jumlah', []);
        $satuans = $request->input('satuan', []);

        // generate slug
        $slug = Str::slug($templateName);
        $tipeValue = ($request->input('type') == 'Bahan Baku') ? 'bahan_baku' : 'bahan_kemas';

        // 1. Create Parent Formula
        $formula = Formula::create([
            'nama_formula' => $templateName,
            'slug' => $slug,
            'tipe' => $tipeValue,
        ]);

        $data = [];
        foreach ($nama_bahan_bakus as $key => $bahan_baku) {
            $data[] = [
                'formula_id' => $formula->id,
                'name' => $bahan_baku,
                'inventory_id' => $inventory_ids[$key] ?? null,
                'jumlah' => str_replace(',', '.', $jumlahs[$key] ?? null),
                'satuan' => $satuans[$key] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if (!empty($data)) {
            if ($request->input('type') == 'Bahan Baku') {
                foreach ($data as $item) {
                    FormulaBahanBaku::create($item);
                }
            } elseif ($request->input('type') == 'Bahan Pelengkap') {
                foreach ($data as $item) {
                    FormulaBahanKemas::create($item);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Formula and ingredients saved successfully.',
            'formula_id' => $formula->id
        ]);
    }

    public function storeTahapanProses(Request $request, $formula_id)
    {
        $nama_proses = $request->input('nama_proses', []);
        $acuan = $request->input('acuan', []);

        $dataProses = [];
        foreach ($nama_proses as $i => $proses) {
            if (!empty($proses)) {
                $dataProses[] = [
                    'formula_id' => $formula_id,
                    'nama_proses' => $proses,
                    'acuan' => $acuan[$i] ?? '',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        if (!empty($dataProses)) {
            foreach ($dataProses as $data) {
                TahapanProses::create($data);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Tahapan Proses saved successfully.',
            'formula_id' => $formula_id
        ]);
    }

    public function storeQualityControl(Request $request, $formula_id)
    {
        $parameter_pengujian = $request->input('parameter_pengujian', []);
        $nilai_standart = $request->input('nilai_standart', []);

        $dataQc = [];
        $urutanQc = 1;
        foreach ($parameter_pengujian as $i => $param) {
            if (!empty($param)) {
                $dataQc[] = [
                    'formula_id' => $formula_id,
                    'parameter_pengujian' => $param,
                    'nilai_standart' => $nilai_standart[$i] ?? '-',
                    'urutan' => $urutanQc++,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
        if (!empty($dataQc)) {
            foreach ($dataQc as $data) {
                QualityControl::create($data);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Quality Control saved successfully.',
            'formula_id' => $formula_id
        ]);
    }

    public function storeAcuanLaporanIpc(Request $request, $formula_id)
    {
        $sections = $request->input('sections', []);

        $dataIpc = [];
        foreach ($sections as $sectionName => $parameters) {
            foreach ($parameters as $param => $detail) {
                if (isset($detail['spesifikasi'])) {
                    $dataIpc[] = [
                        'formula_id' => $formula_id,
                        'section_name' => $sectionName,
                        'pemeriksaan' => $param,
                        'spesifikasi' => $detail['spesifikasi'] ?? '',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
        }
        if (!empty($dataIpc)) {
            foreach ($dataIpc as $data) {
                AcuanLaporanIpc::create($data);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Acuan Laporan IPC saved successfully.',
            'formula_id' => $formula_id
        ]);
    }


    public function edit($id) {
        $formula = Formula::with([
            'bahanBaku',
            'bahanKemas',
            'tahapanProses',
            'qualityControl',
            'acuanLaporanIpc'
        ])->findOrFail($id);

        $inventory = Inventory::all();
        $products = Product::all();

        return view('formula.edit-formula', compact(
            'formula',
            'inventory',
            'products'
        ));
    }


    public function update(Request $request, $id)
    {
        $formula = Formula::findOrFail($id);

        // ========================
        // INPUTS
        // ========================
        $inventory_ids     = $request->input('inventory_id', []);
        $templateName      = $request->input('nama_template');
        $nama_bahan_bakus  = $request->input('nama_bahan_baku', []);
        $jumlahs           = $request->input('jumlah', []);
        $satuans           = $request->input('satuan', []);
        $bahan_ids         = $request->input('bahan_id', []);

        $nama_proses       = $request->input('nama_proses', []);
        $acuan             = $request->input('acuan', []);
        $proses_ids        = $request->input('proses_id', []);

        $qualityControls   = $request->input('quality_control', []);
        $sections          = $request->input('sections', []);

        $slug = Str::slug($templateName);
        $tipeValue = ($request->input('type') == 'Bahan Baku') ? 'bahan_baku' : 'bahan_kemas';

        // ========================
        // 1. UPDATE PARENT
        // ========================
        $formula->update([
            'nama_formula' => $templateName,
            'slug' => $slug,
            'tipe' => $tipeValue,
        ]);

        // ========================
        // 2. INGREDIENT (UPSERT)
        // ========================
        $bahan_ids_request = $request->input('bahan_id', []);
        $existingBahanIds = [];

        foreach ($nama_bahan_bakus as $i => $nama) {

            if (empty($nama)) continue;

            $data = [
                'formula_id'   => $id,
                'name'         => $nama,
                'inventory_id' => $inventory_ids[$i] ?? null,
                'jumlah'       => str_replace(',', '.', $jumlahs[$i] ?? null),
                'satuan'       => $satuans[$i] ?? null,
            ];

            if (!empty($bahan_ids_request[$i])) {
                // UPDATE
                $existingBahanIds[] = $bahan_ids_request[$i];

                if ($tipeValue === 'bahan_baku') {
                    $bahan = FormulaBahanBaku::find($bahan_ids_request[$i]);
                    if ($bahan) {
                        $bahan->update($data);
                    }
                } else {
                    $bahan = FormulaBahanKemas::find($bahan_ids_request[$i]);
                    if ($bahan) {
                        $bahan->update($data);
                    }
                }
            } else {
                // INSERT
                if ($tipeValue === 'bahan_baku') {
                    $new = FormulaBahanBaku::create($data);
                } else {
                    $new = FormulaBahanKemas::create($data);
                }

                $existingBahanIds[] = $new->id;
            }
        }


        // 🔥 DELETE YANG SUDAH DIHAPUS DI UI
        if ($tipeValue === 'bahan_baku') {
            $deletedBahan = FormulaBahanBaku::where('formula_id', $id)
                ->whereNotIn('id', $existingBahanIds)
                ->get();
            foreach ($deletedBahan as $del) {
                $del->delete();
            }
        } else {
            $deletedBahan = FormulaBahanKemas::where('formula_id', $id)
                ->whereNotIn('id', $existingBahanIds)
                ->get();
            foreach ($deletedBahan as $del) {
                $del->delete();
            }
        }

        // ========================
        // 3. TAHAPAN PROSES (UPSERT)
        // ========================
        $proses_ids_request = $request->input('proses_id', []);
        $existingProsesIds = [];

        foreach ($nama_proses as $i => $proses) {

            if (empty($proses)) continue;

            $data = [
                'formula_id'  => $id,
                'nama_proses' => $proses,
                'acuan'       => $acuan[$i] ?? '',
            ];

            if (!empty($proses_ids_request[$i])) {
                $existingProsesIds[] = $proses_ids_request[$i];

                $tp = TahapanProses::find($proses_ids_request[$i]);
                if ($tp) {
                    $tp->update($data);
                }
            } else {
                $new = TahapanProses::create($data);
                $existingProsesIds[] = $new->id;
            }
        }


        $tahapanToDelete = TahapanProses::where('formula_id', $id)
            ->whereNotIn('id', $existingProsesIds)
            ->get();
        foreach ($tahapanToDelete as $del) {
            $del->delete();
        }

        // ========================
        // 4. QUALITY CONTROL (UPSERT)
        // ========================
        $urutanQc = 1;

        foreach ($qualityControls as $param => $detail) {

            $existing = QualityControl::where('formula_id', $id)
                ->where('parameter_pengujian', $param)
                ->first();

            if ($existing) {
                // UPDATE
                $existing->update([
                    'nilai_standart' => $detail['nilai_standart'] ?? '',
                    'urutan' => $urutanQc++
                ]);
            } else {
                // INSERT
                QualityControl::create([
                    'formula_id' => $id,
                    'parameter_pengujian' => $param,
                    'nilai_standart' => $detail['nilai_standart'] ?? '',
                    'urutan' => $urutanQc++
                ]);
            }
        }

        // ========================
        // 5. IPC (UPSERT)
        // ========================
        foreach ($sections as $sectionName => $parameters) {

            foreach ($parameters as $param => $detail) {

                $existing = AcuanLaporanIpc::where('formula_id', $id)
                    ->where('section_name', $sectionName)
                    ->where('pemeriksaan', $param)
                    ->first();

                if ($existing) {
                    // UPDATE
                    $existing->update([
                        'spesifikasi' => $detail['spesifikasi'] ?? ''
                    ]);
                } else {
                    // INSERT
                    AcuanLaporanIpc::create([
                        'formula_id' => $id,
                        'section_name' => $sectionName,
                        'pemeriksaan' => $param,
                        'spesifikasi' => $detail['spesifikasi'] ?? ''
                    ]);
                }
            }
        }

        // ========================
        // DONE
        // ========================
        return redirect('/formula-produk')->with('success', 'Formula berhasil diperbarui!');
    }

    public function updateProdukJadi(Request $request, $id) {
        $produkjadi = SerahTerimaBarang::find($id);
        $produkjadi->acuan = preg_replace('/[^0-9]/', '', $request->acuan);
        $produkjadi->save();

        return redirect()->back()->with('success', 'Produk jadi berhasil diperbarui!');
    }

    public function delete(Request $request, $id) {
        $user = Auth::user();

        if (!$user->hasAnyRole(['Admin', 'Production Manager'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus formula.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Password salah! Tidak dapat menghapus formula.');
        }

        $formula = Formula::findOrFail($id);

        $formula->bahanBaku()->delete();
        $formula->bahanKemas()->delete();
        $formula->tahapanProses()->delete();
        $formula->qualityControl()->delete();
        $formula->acuanLaporanIpc()->delete();

        $formula->delete();

        return redirect('/formula-produk')->with('success', 'Formula beserta seluruh isi datanya berhasil dihapus!');
    }

}
