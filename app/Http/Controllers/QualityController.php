<?php

namespace App\Http\Controllers;


use App\Inventory;
use App\PemantauanStabilitas;
use Illuminate\Http\Request;
use App\SampleInProses;
use App\InspeksiProses;
use App\ReportInProcessControl;
use App\DetailPemantauanStabilitas;
use App\SpesifikasiUjiMaterial;
use App\Manufacturers;
use App\PengambilanSampleProdukJadi;
use App\ItemInspeksiProses;
use App\Formula;
use App\ItemPpj;
use App\PengujianProdukJadi;
use App\QualityControlCheck;
use App\QualityControl;
use App\ItemReportInPc;
use App\HasilPemantauanStabilitas;

class QualityController extends Controller
{
    // has to login first
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Start Sample IPC
    public function storeSampleIPC(Request $request) {
        $data = $request->all();

        SampleInProses::create($data);
        return redirect()->back()->with('success', 'Data Berhasil Disimpan');
    }

    public function updateSampleIPC(Request $request, $id)
    {
        $sample = SampleInProses::findOrFail($id);
        $sample->update($request->all());
        return back()->with('success', 'Data Sample IPC berhasil diperbarui!');
    }
    // end fo sample ipc


    // start inspeksi proses
    public function storeInspeksiProses(Request $request) {

        $dataInspeksi = InspeksiProses::create([
            'tanggal' => $request->input('tanggal'),
            'kesimpulan' => $request->input('kesimpulan'),
            'petugas_qc' => $request->input('petugas_qc'),
            'jumlah_sampel_uji' => $request->input('jumlah_sampel_uji'),
            'produksi_id' => $request->input('produksi_id')
        ]);

        $hasil_atas = $request->input('inspeksi_bagian_atas', []);
        $hasil_bawah = $request->input('inspeksi_bagian_bawah', []);
        $qualityIds = $request->input('acuan_laporan_ipc_id', []);

        foreach ($hasil_atas as $index => $value) {
            $data = ItemInspeksiProses::create([
                'inspeksi_proses_id' => $dataInspeksi->id,
                'produksi_id' => $dataInspeksi->produksi_id,
                'acuan_laporan_ipc_id' => $qualityIds[$index] ?? null,
                'inspeksi_bagian_atas' => $value ?? null,
                'inspeksi_bagian_bawah' => $hasil_bawah[$index] ?? null,
            ]);
        }
        return redirect()->back()->with('success', 'Data Berhasil Disimpan');
    }

    public function updateInspeksiProses(Request $request, $id){
        $inspeksi = InspeksiProses::findOrFail($id);

        $inspeksi->update([
            'tanggal' => $request->input('tanggal'),
            'kesimpulan' => $request->input('kesimpulan'),
            'petugas_qc' => $request->input('petugas_qc'),
            'jumlah_sampel_uji' => $request->input('jumlah_sampel_uji'),
        ]);

        $existingItems = ItemInspeksiProses::where('inspeksi_proses_id', $inspeksi->id)
                            ->get()
                            ->keyBy('acuan_laporan_ipc_id'); // key by acuan_laporan_ipc_id

        $hasil_atas = $request->input('inspeksi_bagian_atas', []);
        $hasil_bawah = $request->input('inspeksi_bagian_bawah', []);
        $qualityIds = $request->input('acuan_laporan_ipc_id', []);

        foreach ($hasil_atas as $index => $value) {
            $qualityId = $qualityIds[$index] ?? null;

            if ($existingItems->has($qualityId)) {
                $existingItem = $existingItems[$qualityId];
                $existingItem->update([
                    'inspeksi_bagian_atas' => $value,
                    'inspeksi_bagian_bawah' => $hasil_bawah[$index] ?? null,
                ]);
            } else {
                ItemInspeksiProses::create([
                    'inspeksi_proses_id' => $inspeksi->id,
                    'produksi_id' => $inspeksi->produksi_id,
                    'acuan_laporan_ipc_id' => $qualityId,
                    'inspeksi_bagian_atas' => $value,
                    'inspeksi_bagian_bawah' => $hasil_bawah[$index] ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data Inspeksi Proses berhasil diperbarui');
    }
    // end inspeksi proses


    // start report ipc
    public function storeReportIPC(Request $request) {
        $report = ReportInProcessControl::create([
            'tanggal_pengujian' => $request->tanggal_pengujian,
            'disampling_oleh' => $request->disampling_oleh,
            'jumlah_sampel_uji' => $request->jumlah_sampel_uji,
            'kesimpulan' => $request->kesimpulan,
            'catatan' => $request->catatan,
            'produksi_id' => $request->produksi_id,
        ]);

        $hasil = $request->input('hasil', []);
        $qualityIds = $request->input('acuan_laporan_ipc_id', []);

        foreach ($qualityIds as $acuanId) {
            ItemReportInPc::create([
                'hasil' => $hasil[$acuanId] ?? [],
                'produksi_id' => $report->produksi_id,
                'report_in_process_control_id' => $report->id,
                'acuan_laporan_ipc_id' => $acuanId,
            ]);
        }

        return back()->with('success', 'Data Berhasil Disimpan');
    }

    public function downloadSpesifikasi(){
        $allProducts = \App\Product::all();
        $products = [];
        foreach ($allProducts as $product) {
            $matchedFormula = $product->getMatchedFormula();
            if ($matchedFormula && $matchedFormula->acuanLaporanIpc()->exists()) {
                $product->matched_formula = $matchedFormula;
                $products[] = $product;
            }
        }
        
        $inventories = Inventory::with('manufacturers')->get();

        return view('produksi.download-spesifikasi', compact('products', 'inventories'));
    }

    public function downloadPdfProduct($id)
    {
        $product = \App\Product::findOrFail($id);
        $formula = $product->getMatchedFormula();

        if (!$formula) {
            return back()->with('error', 'Formula tidak ditemukan untuk produk ini.');
        }

        $qualityControl = QualityControl::where('formula_id', $formula->id)->orderBy('urutan')->get();

        $pdf = \PDF::loadView('produksi.pdf.spesifikasi-produk', compact('product', 'formula', 'qualityControl'));
        return $pdf->stream('Spesifikasi Produk Jadi '. $product->name.'.pdf');
    }

    public function downloadPdfInventory($inventory_id, $manufacturer_id)
    {
        $inventory = Inventory::findOrFail($inventory_id);
        $manufacturer = Manufacturers::findOrFail($manufacturer_id);

        $specifications = SpesifikasiUjiMaterial::where('inventory_id', $inventory_id)
                            ->where('manufacturer_id', $manufacturer_id)
                            ->get();

        $pdf = \PDF::loadView('produksi.pdf.spesifikasi-inventory', compact('inventory', 'manufacturer', 'specifications'));
        return $pdf->stream('Spesifikasi '.$inventory->name.'.pdf');
    }
    
    public function updateReportIPC(Request $request, $id) {
        $report = ReportInProcessControl::findOrFail($id);

        $report->update([
            'tanggal_pengujian' => $request->tanggal_pengujian,
            'disampling_oleh' => $request->disampling_oleh,
            'jumlah_sampel_uji' => $request->jumlah_sampel_uji,
            'kesimpulan' => $request->kesimpulan,
            'catatan' => $request->catatan,
        ]);

        $hasil = $request->input('hasil', []);
        $qualityIds = $request->input('acuan_laporan_ipc_id', []);

        $existing = ItemReportInPc::where('report_in_process_control_id', $report->id)
            ->get()
            ->keyBy('acuan_laporan_ipc_id');

        foreach ($qualityIds as $acuanId) {
            if ($existing->has($acuanId)) {
                $existing[$acuanId]->update([
                    'hasil' => $hasil[$acuanId] ?? [],
                ]);
            } else {
                ItemReportInPc::create([
                    'hasil' => $hasil[$acuanId] ?? [],
                    'produksi_id' => $report->produksi_id,
                    'report_in_process_control_id' => $report->id,
                    'acuan_laporan_ipc_id' => $acuanId,
                ]);
            }
        }

        return back()->with('success', 'Data Report IPC berhasil diperbarui');
    }
    // end report ipc

    public function storeSampleProdukJadi(Request $request) {
        $data = $request->all();
        PengambilanSampleProdukJadi::create($data);
        return redirect()->back()->with('success', 'Data Berhasil Disimpan');
    }

    public function updateSampleProdukJadi(Request $request, $id) {
        $samplePPJ = PengambilanSampleProdukJadi::findOrFail($id);
        $samplePPJ->update($request->all());
        return back()->with('success', 'Data Sample Produk Jadi berhasil diperbarui!');
    }

    public function storePengujianProduk(Request $request) {
        $dataProdukJadi = PengujianProdukJadi::create([
            'bagian' => $request->input('bagian'),
            'tanggal' => $request->input('tanggal'),
            'petugas_qc' => $request->input('petugas_qc'),
            'kesimpulan' => $request->input('kesimpulan'),
            'catatan' => $request->input('catatan'),
            'produksi_id' => $request->input('produksi_id'),
        ]);

        $hasil = $request->input('hasil_pj', []);
        $metode = $request->input('metode', []);
        $qualityIds = $request->input('acuan_laporan_ipc_id', []);

        foreach ($hasil as $index => $value) {
            ItemPpj::create([
                'hasil' => $value ,
                'produksi_id' => $dataProdukJadi->produksi_id,
                'acuan_laporan_ipc_id' => $qualityIds[$index] ?? null,
                'pengujian_produk_jadi_id' => $dataProdukJadi->id,
            ]);
        }
        return redirect()->back()->with('success', 'Data Berhasil Disimpan');
    }

    public function updatePengujianProduk(Request $request, $id) {
        $pengujian = PengujianProdukJadi::findOrFail($id);

        $pengujian->update([
            'bagian' => $request->input('bagian'),
            'tanggal' => $request->input('tanggal'),
            'petugas_qc' => $request->input('petugas_qc'),
            'kesimpulan' => $request->input('kesimpulan'),
            'catatan' => $request->input('catatan'),
        ]);

        $existingItems = ItemPpj::where('pengujian_produk_jadi_id', $pengujian->id)
                            ->get()
                            ->keyBy('acuan_laporan_ipc_id'); // key by acuan_laporan_ipc_id

        $hasil = $request->input('hasil_pj', []);
        $qualityIds = $request->input('acuan_laporan_ipc_id', []);
        foreach ($hasil as $index => $value) {
            $qualityId = $qualityIds[$index] ?? null;

            if ($existingItems->has($qualityId)) {
                $existingItem = $existingItems[$qualityId];
                $existingItem->update([
                    'hasil' => $value
                ]);
            } else {
                ItemPpj::create([
                    'hasil' => $value,
                    'produksi_id' => $pengujian->produksi_id,
                    'acuan_laporan_ipc_id' => $qualityId,
                    'pengujian_produk_jadi_id' => $pengujian->id,
                ]);
            }
        }
        return redirect()->back()->with('success', 'Data Pengujian Produk berhasil diperbarui');
    }

    public function viewPantauStabilitas($id) {
        $stabilitas = PemantauanStabilitas::with(['pemeriksaan_qc', 'produksi.product'])
        ->where('produksi_id', $id)
        ->firstOrFail();

        $detail_stabilitas = DetailPemantauanStabilitas::with('hasil')->where('pemantauan_stabilitas_id', $stabilitas->id)
        ->orderBy('bulan_ke')
        ->get();
        
        $acuan_quality = QualityControl::where('formula_id', $stabilitas->produksi->formula_bahan_baku_id)->get();
        $pemeriksaan_qc = QualityControlCheck::where('produksi_id', $id)->get();
        
        return view('produksi.pantau-stabilitas', compact('stabilitas', 'detail_stabilitas', 'pemeriksaan_qc', 'acuan_quality'));
    }

    public function storePantauStabilitas(Request $request)
    {
        $request->validate([
            'pemantauan_stabilitas_detail_id' => 'required|exists:pemantauan_stabilitas_detail,id',
            'petugas_qc' => 'nullable|string',
            'nilai_real' => 'required|array',
        ]);

        $detail = DetailPemantauanStabilitas::findOrFail($request->pemantauan_stabilitas_detail_id);
        
        $detail->update([
            'petugas_qc' => $request->petugas_qc,
            'status' => 'selesai',
            'tanggal_pengerjaan' => now(),
        ]);

        foreach ($request->nilai_real as $qcId => $nilai) {
            if ($nilai !== null) {
                HasilPemantauanStabilitas::create([
                    'pemantauan_stabilitas_detail_id' => $detail->id,
                    'quality_control_id' => $qcId,
                    'nilai_real' => $nilai,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data Pemantauan Stabilitas Berhasil Disimpan');
    }

    public function updatePantauStabilitas(Request $request, $id)
    {
        $request->validate([
            'petugas_qc' => 'nullable|string',
            'nilai_real' => 'required|array',
        ]);

        $detail = DetailPemantauanStabilitas::findOrFail($id);
        
        $detail->update([
            'petugas_qc' => $request->petugas_qc,
        ]);

        $existingHasil = HasilPemantauanStabilitas::where('pemantauan_stabilitas_detail_id', $detail->id)
            ->get()
            ->keyBy('quality_control_id');

        foreach ($request->nilai_real as $qcId => $nilai) {
            if ($nilai !== null) {
                if ($existingHasil->has($qcId)) {
                    $existingHasil[$qcId]->update(['nilai_real' => $nilai]);
                } else {
                    HasilPemantauanStabilitas::create([
                        'pemantauan_stabilitas_detail_id' => $detail->id,
                        'quality_control_id' => $qcId,
                        'nilai_real' => $nilai,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Data Pemantauan Stabilitas Berhasil Diperbarui');
    }
}
