<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StockIn;
use App\PmPengambilanSampel;
use App\PmInspeksiPenerimaan;
use App\PmPengujian;
use App\PmPengujianDetail;
use App\PmLaporanInspeksi;
use App\PmLaporanInspeksiDetail;
use App\PmManualReport;
use App\SuplierMaterial;
use App\Inventory;
use App\SpesifikasiUjiMaterial;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PengecekanMaterialController extends Controller
{
    public function index() {
        $barangMasuk = StockIn::with('inventory')
            ->whereDate('created_at', '>=', '2026-07-10')
            ->where('kategori', 'incoming_material')
            ->get();
        $manualReports = PmManualReport::with('inventory')->get();
        $inventory = Inventory::all();
        $supliers = SuplierMaterial::all();
        return view('inventory.pengecekan-material.index-pengecekan', compact('barangMasuk', 'manualReports', 'inventory', 'supliers'));
    }

    public function storeManual(Request $request) {
        $manual = PmManualReport::create([
            'inventory_id' => $request->inventory_id,
            'suplier_material_id' => $request->suplier_material_id,
            'nomor_batch' => $request->nomor_batch,
            'tanggal_laporan' => $request->tanggal_laporan,
        ]);
        return redirect()->to(url('detail-pengecekan-material', $manual->id) . '?type=manual')->with('success', 'Laporan Manual berhasil dibuat.');
    }

    public function detail(Request $request, $id) {
        $type = $request->query('type', 'stock_in');
        
        $stock_in = null;
        $manual_report = null;
        $inventory = null;

        if ($type == 'manual') {
            $manual_report = PmManualReport::with('inventory.manufacturers', 'suplierMaterial')->findOrFail($id);
            $inventory = $manual_report->inventory;
            
            $pengambilanSampel = PmPengambilanSampel::where('pm_manual_report_id', $id)->first();
            $inspeksiPenerimaan = PmInspeksiPenerimaan::where('pm_manual_report_id', $id)->first();
            $pengujian = PmPengujian::with('details')->where('pm_manual_report_id', $id)->first();
            $laporanInspeksi = PmLaporanInspeksi::with('details')->where('pm_manual_report_id', $id)->first();
        } else {
            $stock_in = StockIn::with('inventory.manufacturers', 'suplierMaterial')->findOrFail($id);
            $inventory = $stock_in->inventory;
            
            $pengambilanSampel = PmPengambilanSampel::where('stock_in_id', $id)->first();
            $inspeksiPenerimaan = PmInspeksiPenerimaan::where('stock_in_id', $id)->first();
            $pengujian = PmPengujian::with('details')->where('stock_in_id', $id)->first();
            $laporanInspeksi = PmLaporanInspeksi::with('details')->where('stock_in_id', $id)->first();
        }

        // Get Specifications based on manufacturer if exists
        $manufacturer_id = null;
        if ($inventory->type == 'Bahan Baku') {
            $manufacturer_id = $inspeksiPenerimaan ? $inspeksiPenerimaan->manufacturer_id : null;
        } else {
            $manufacturer_id = $pengambilanSampel ? $pengambilanSampel->manufacturer_id : null;
        }

        if (!$manufacturer_id) {
            $manufacturer_id = $type == 'manual' ? ($manual_report->produsen_id ?? null) : ($stock_in->produsen_id ?? null);
        }

        if ($manufacturer_id) {
            $spesifikasi = SpesifikasiUjiMaterial::where('inventory_id', $inventory->id)
                ->where('manufacturer_id', $manufacturer_id)
                ->get();
        } else {
            $spesifikasi = collect();
        }
        
        $allSpesifikasi = SpesifikasiUjiMaterial::where('inventory_id', $inventory->id)->get();
        $supliers = SuplierMaterial::where('inventory_id', $inventory->id)->get();

        return view('inventory.pengecekan-material.detail-pengecekan', compact(
            'type', 'stock_in', 'manual_report', 'inventory', 'pengambilanSampel', 'inspeksiPenerimaan', 'pengujian', 'laporanInspeksi', 'spesifikasi', 'allSpesifikasi', 'manufacturer_id', 'supliers'
        ));
    }
    // code update web
    public function printReport(Request $request, $id) {
        $type = $request->query('type', 'stock_in');
        $stock_in = null;
        $manual_report = null;
        $inventory = null;

        if ($type == 'manual') {
            $manual_report = PmManualReport::with('inventory.manufacturers', 'suplierMaterial')->findOrFail($id);
            $inventory = $manual_report->inventory;
            
            $pengambilanSampel = PmPengambilanSampel::where('pm_manual_report_id', $id)->first();
            $inspeksiPenerimaan = PmInspeksiPenerimaan::where('pm_manual_report_id', $id)->first();
            $pengujian = PmPengujian::with('details')->where('pm_manual_report_id', $id)->first();
            $laporanInspeksi = PmLaporanInspeksi::with('details')->where('pm_manual_report_id', $id)->first();
        } else {
            $stock_in = StockIn::with('inventory.manufacturers', 'suplierMaterial')->findOrFail($id);
            $inventory = $stock_in->inventory;
            
            $pengambilanSampel = PmPengambilanSampel::where('stock_in_id', $id)->first();
            $inspeksiPenerimaan = PmInspeksiPenerimaan::where('stock_in_id', $id)->first();
            $pengujian = PmPengujian::with('details')->where('stock_in_id', $id)->first();
            $laporanInspeksi = PmLaporanInspeksi::with('details')->where('stock_in_id', $id)->first();
        }

        // Get Specifications based on manufacturer if exists
        $manufacturer_id = null;
        if ($inventory->type == 'Bahan Baku') {
            $manufacturer_id = $inspeksiPenerimaan ? $inspeksiPenerimaan->manufacturer_id : null;
        } else {
            $manufacturer_id = $pengambilanSampel ? $pengambilanSampel->manufacturer_id : null;
        }

        if (!$manufacturer_id) {
            $manufacturer_id = $type == 'manual' ? ($manual_report->produsen_id ?? null) : ($stock_in->produsen_id ?? null);
        }

        $spesifikasi = collect();
        if ($manufacturer_id) {
            $spesifikasi = SpesifikasiUjiMaterial::where('inventory_id', $inventory->id)
                ->where('manufacturer_id', $manufacturer_id)
                ->get();
        }
        
        $batch = $type == 'manual' ? ($manual_report->nomor_batch ?? 'Manual') : $stock_in->nomor_batch;
        $fileName = 'Laporan_Pengecekan_Material' . $batch . '.pdf';

        // Using dompdf for Portrait
        $pdfPortrait = Pdf::loadView('inventory.pengecekan-material.pdf', compact(
            'type', 'stock_in', 'manual_report', 'inventory', 'pengambilanSampel', 'inspeksiPenerimaan', 'pengujian', 'laporanInspeksi', 'spesifikasi', 'manufacturer_id'
        ))->setPaper('A4', 'portrait');

        if ($inventory->type != 'Bahan Baku') {
            $pdfLandscape = Pdf::loadView('inventory.pengecekan-material.pdf_landscape', compact(
                'type', 'stock_in', 'manual_report', 'inventory', 'pengambilanSampel', 'inspeksiPenerimaan', 'pengujian', 'laporanInspeksi', 'spesifikasi', 'manufacturer_id'
            ))->setPaper('A4', 'landscape');

            try {
                $merger = \Webklex\PDFMerger\Facades\PDFMergerFacade::init();
                $merger->addString($pdfPortrait->output(), 'all');
                $merger->addString($pdfLandscape->output(), 'all');
                $merger->merge();

                return response($merger->output(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"'
                ]);
            } catch (\Exception $e) {
                return $pdfPortrait->stream($fileName);
            }
        }

        return $pdfPortrait->stream($fileName);
    }

    public function storePengambilanSampel(Request $request) {
        if ($request->filled('expired_date') && $request->filled('pm_manual_report_id')) {
            PmManualReport::where('id', $request->pm_manual_report_id)->update([
                'expired_date' => Carbon::parse($request->expired_date)->format('Y-m-d'),
            ]);
        }
        
        if ($request->filled('stock_in_id') && $request->type == 'stock_in') {
            $stockIn = StockIn::find($request->stock_in_id);
            $stockInUpdated = false;
            if ($request->filled('suplier_material_id') && !$stockIn->suplier_material_id) {
                $stockIn->suplier_material_id = $request->suplier_material_id;
                $stockInUpdated = true;
            }
            if ($request->filled('manufacturer_id') && $stockIn->produsen_id != $request->manufacturer_id) {
                $stockIn->produsen_id = $request->manufacturer_id;
                $stockInUpdated = true;
            }
            if ($stockInUpdated) {
                $stockIn->save();
            }
        }
        
        $data = $request->except(['_token', '_method', 'type', 'suplier_material_id']);
        $data['doc_coa'] = $request->has('doc_coa') ? 1 : 0;
        $data['doc_msds'] = $request->has('doc_msds') ? 1 : 0;
        $data['doc_halal'] = $request->has('doc_halal') ? 1 : 0;
        PmPengambilanSampel::create($data);
        return back()->with('success', 'Data Pengambilan Sampel berhasil disimpan.');
    }

    public function updatePengambilanSampel(Request $request, $id) {
        $data = PmPengambilanSampel::findOrFail($id);
        if ($request->filled('expired_date') && $request->filled('pm_manual_report_id')) {
            PmManualReport::where('id', $request->pm_manual_report_id)->update([
                'expired_date' => Carbon::parse($request->expired_date)->format('Y-m-d'),
            ]);
        }
        
        if ($request->filled('stock_in_id') && $request->type == 'stock_in') {
            $stockIn = StockIn::find($request->stock_in_id);
            if ($stockIn && $request->filled('manufacturer_id') && $stockIn->produsen_id != $request->manufacturer_id) {
                $stockIn->produsen_id = $request->manufacturer_id;
                $stockIn->save();
            }
        }
        $dataUpdate = $request->except(['_token', '_method', 'type', 'suplier_material_id']);
        $dataUpdate['doc_coa'] = $request->has('doc_coa') ? 1 : 0;
        $dataUpdate['doc_msds'] = $request->has('doc_msds') ? 1 : 0;
        $dataUpdate['doc_halal'] = $request->has('doc_halal') ? 1 : 0;
        
        $data->update($dataUpdate);
        return back()->with('success', 'Data Pengambilan Sampel berhasil diupdate.');
    }

    public function storeInspeksiPenerimaan(Request $request) {
        if ($request->filled('tanggal_ed') && $request->filled('pm_manual_report_id')) {
            PmManualReport::where('id', $request->pm_manual_report_id)->update([
                'expired_date' => Carbon::parse($request->tanggal_ed)->format('Y-m-d'),
            ]);
        } 

        if ($request->filled('stock_in_id') && $request->type == 'stock_in') {
            $stockIn = StockIn::find($request->stock_in_id);
            $stockInUpdated = false;
            if ($request->filled('suplier_material_id') && !$stockIn->suplier_material_id) {
                $stockIn->suplier_material_id = $request->suplier_material_id;
                $stockInUpdated = true;
            }
            if ($request->filled('manufacturer_id') && $stockIn->produsen_id != $request->manufacturer_id) {
                $stockIn->produsen_id = $request->manufacturer_id;
                $stockInUpdated = true;
            }
            if ($stockInUpdated) {
                $stockIn->save();
            }
        }

        $dataInspeksi = $request->except(['_token', '_method', 'type', 'expired_date', 'suplier_material_id']);
        $dataInspeksi['doc_coa'] = $request->has('doc_coa') ? 1 : 0;
        $dataInspeksi['doc_msds'] = $request->has('doc_msds') ? 1 : 0;
        $dataInspeksi['doc_halal'] = $request->has('doc_halal') ? 1 : 0;

        PmInspeksiPenerimaan::create($dataInspeksi);

        return back()->with('success', 'Data Inspeksi Penerimaan berhasil disimpan.');
    }

    public function updateInspeksiPenerimaan(Request $request, $id) {
        $inspeksi = PmInspeksiPenerimaan::findOrFail($id);

        if ($request->filled('tanggal_ed') && $request->filled('pm_manual_report_id')) {
            PmManualReport::where('id', $request->pm_manual_report_id)->update([
                'expired_date' => Carbon::parse($request->tanggal_ed)->format('Y-m-d'),
            ]);
        }
        
        if ($request->filled('stock_in_id') && $request->type == 'stock_in') {
            $stockIn = StockIn::find($request->stock_in_id);
            if ($stockIn && $request->filled('manufacturer_id') && $stockIn->produsen_id != $request->manufacturer_id) {
                $stockIn->produsen_id = $request->manufacturer_id;
                $stockIn->save();
            }
        }
        $dataUpdate = $request->except(['_token', '_method', 'type', 'suplier_material_id']);
        $dataUpdate['doc_coa'] = $request->has('doc_coa') ? 1 : 0;
        $dataUpdate['doc_msds'] = $request->has('doc_msds') ? 1 : 0;
        $dataUpdate['doc_halal'] = $request->has('doc_halal') ? 1 : 0;

        $inspeksi->update($dataUpdate);
        return back()->with('success', 'Data Inspeksi Penerimaan berhasil diupdate.');
    }

    public function storePengujian(Request $request) {
        $pengujian = PmPengujian::create([
            'stock_in_id' => $request->stock_in_id ?? null,
            'pm_manual_report_id' => $request->pm_manual_report_id ?? null,
            'tanggal' => $request->tanggal,
            'kesimpulan' => $request->kesimpulan,
            'petugas_qc' => $request->petugas_qc,
        ]);

        if ($request->filled('stock_in_id') && $request->type == 'stock_in') {
            $stockIn = StockIn::find($request->stock_in_id);
            if ($stockIn && $request->filled('manufacturer_id') && $stockIn->produsen_id != $request->manufacturer_id) {
                $stockIn->produsen_id = $request->manufacturer_id;
                $stockIn->save();
            }
        }

        if($request->has('parameter_uji')){
            foreach($request->parameter_uji as $key => $param) {
                PmPengujianDetail::create([
                    'pm_pengujian_id' => $pengujian->id,
                    'parameter_uji' => $param,
                    'spesifikasi' => $request->spesifikasi[$key] ?? null,
                    'hasil' => $request->hasil[$key] ?? null,
                ]);
            }
        }
        return back()->with('success', 'Data Form Pengujian berhasil disimpan.');
    }

    public function updatePengujian(Request $request, $id) {
        $pengujian = PmPengujian::findOrFail($id);
        $pengujian->update([
            'tanggal' => $request->tanggal,
            'kesimpulan' => $request->kesimpulan,
            'petugas_qc' => $request->petugas_qc,
        ]);

        if ($request->filled('stock_in_id') && $request->type == 'stock_in') {
            $stockIn = StockIn::find($request->stock_in_id);
            if ($stockIn && $request->filled('manufacturer_id') && $stockIn->produsen_id != $request->manufacturer_id) {
                $stockIn->produsen_id = $request->manufacturer_id;
                $stockIn->save();
            }
        }

        PmPengujianDetail::where('pm_pengujian_id', $id)->delete();
        if($request->has('parameter_uji')){
            foreach($request->parameter_uji as $key => $param) {
                PmPengujianDetail::create([
                    'pm_pengujian_id' => $pengujian->id,
                    'parameter_uji' => $param,
                    'spesifikasi' => $request->spesifikasi[$key] ?? null,
                    'hasil' => $request->hasil[$key] ?? null,
                ]);
            }
        }
        return back()->with('success', 'Data Form Pengujian berhasil diupdate.');
    }

    public function storeLaporanInspeksi(Request $request) {
        $laporan = null;
        if ($request->filled('stock_in_id') && $request->type == 'stock_in') {
            $laporan = PmLaporanInspeksi::where('stock_in_id', $request->stock_in_id)->first();
        } elseif ($request->filled('pm_manual_report_id')) {
            $laporan = PmLaporanInspeksi::where('pm_manual_report_id', $request->pm_manual_report_id)->first();
        }

        $laporanData = [
            'stock_in_id' => $request->stock_in_id ?? null,
            'pm_manual_report_id' => $request->pm_manual_report_id ?? null,
            'tanggal_efektif' => $request->tanggal_efektif,
            'tanggal_inspeksi' => $request->tanggal_inspeksi,
            'jumlah_material' => $request->jumlah_material,
            'jumlah_sampel' => $request->jumlah_sampel,
            'disiapkan_oleh' => $request->disiapkan_oleh,
            'disetujui_oleh' => $request->disetujui_oleh,
            'diinspeksi_oleh' => $request->diinspeksi_oleh,
            'diperiksa_oleh' => $request->diperiksa_oleh,
            'kesimpulan' => $request->kesimpulan,
            'catatan' => $request->catatan,
        ];

        if ($laporan) {
            $laporan->update($laporanData);
        } else {
            $laporan = PmLaporanInspeksi::create($laporanData);
        }

        if ($request->filled('stock_in_id') && $request->type == 'stock_in') {
            $stockIn = StockIn::find($request->stock_in_id);
            if ($stockIn && $request->filled('manufacturer_id') && $stockIn->produsen_id != $request->manufacturer_id) {
                $stockIn->produsen_id = $request->manufacturer_id;
                $stockIn->save();
            }
        }

        if($request->has('parameter')){
            $existingDetails = PmLaporanInspeksiDetail::where('pm_laporan_inspeksi_id', $laporan->id)->get();
            $existingById = $existingDetails->keyBy('id');
            $existingByIndex = $existingDetails->values();

            $hasilKemasAll = $request->hasil_kemas;
            $hasilKemasValues = is_array($hasilKemasAll) ? array_values($hasilKemasAll) : [];

            $persyaratanAll = $request->persyaratan;
            $persyaratanValues = is_array($persyaratanAll) ? array_values($persyaratanAll) : [];

            $hasilAtasAll = $request->hasil_atas;
            $hasilAtasValues = is_array($hasilAtasAll) ? array_values($hasilAtasAll) : [];

            $hasilTengahAll = $request->hasil_tengah;
            $hasilTengahValues = is_array($hasilTengahAll) ? array_values($hasilTengahAll) : [];

            $hasilBawahAll = $request->hasil_bawah;
            $hasilBawahValues = is_array($hasilBawahAll) ? array_values($hasilBawahAll) : [];

            $hasilInspeksiAll = $request->hasil_inspeksi;
            $hasilInspeksiValues = is_array($hasilInspeksiAll) ? array_values($hasilInspeksiAll) : [];

            $detailIdAll = $request->detail_id;
            $detailIdValues = is_array($detailIdAll) ? array_values($detailIdAll) : [];

            $loopIdx = 0;
            foreach($request->parameter as $key => $param) {
                $detailId = $detailIdAll[$key] ?? ($detailIdValues[$loopIdx] ?? null);

                $detail = null;
                if ($detailId && $existingById->has($detailId)) {
                    $detail = $existingById->get($detailId);
                } elseif ($existingByIndex->has($loopIdx)) {
                    $detail = $existingByIndex->get($loopIdx);
                }

                $detailData = [
                    'parameter' => $param,
                    'persyaratan' => $persyaratanAll[$key] ?? ($persyaratanValues[$loopIdx] ?? null),
                    'hasil_atas' => $hasilAtasAll[$key] ?? ($hasilAtasValues[$loopIdx] ?? null),
                    'hasil_tengah' => $hasilTengahAll[$key] ?? ($hasilTengahValues[$loopIdx] ?? null),
                    'hasil_bawah' => $hasilBawahAll[$key] ?? ($hasilBawahValues[$loopIdx] ?? null),
                    'hasil_json' => $hasilKemasAll[$key] ?? ($hasilKemasValues[$loopIdx] ?? null),
                    'hasil_inspeksi' => $hasilInspeksiAll[$key] ?? ($hasilInspeksiValues[$loopIdx] ?? null),
                ];

                if ($detail) {
                    $detail->update($detailData);
                } else {
                    $detailData['pm_laporan_inspeksi_id'] = $laporan->id;
                    PmLaporanInspeksiDetail::create($detailData);
                }
                $loopIdx++;
            }
        }
        return back()->with('success', 'Data Laporan Inspeksi berhasil disimpan.');
    }

    public function updateLaporanInspeksi(Request $request, $id) {
        $laporan = PmLaporanInspeksi::findOrFail($id);
        $laporan->update([
            'tanggal_efektif' => $request->tanggal_efektif,
            'tanggal_inspeksi' => $request->tanggal_inspeksi,
            'jumlah_material' => $request->jumlah_material,
            'jumlah_sampel' => $request->jumlah_sampel,
            'disiapkan_oleh' => $request->disiapkan_oleh,
            'disetujui_oleh' => $request->disetujui_oleh,
            'diinspeksi_oleh' => $request->diinspeksi_oleh,
            'diperiksa_oleh' => $request->diperiksa_oleh,
            'kesimpulan' => $request->kesimpulan,
            'catatan' => $request->catatan,
        ]);

        if ($request->filled('stock_in_id') && $request->type == 'stock_in') {
            $stockIn = StockIn::find($request->stock_in_id);
            if ($stockIn && $request->filled('manufacturer_id') && $stockIn->produsen_id != $request->manufacturer_id) {
                $stockIn->produsen_id = $request->manufacturer_id;
                $stockIn->save();
            }
        }

        if($request->has('parameter')){
            $existingDetails = PmLaporanInspeksiDetail::where('pm_laporan_inspeksi_id', $id)->get();
            $existingById = $existingDetails->keyBy('id');
            $existingByIndex = $existingDetails->values();

            $hasilKemasAll = $request->hasil_kemas;
            $hasilKemasValues = is_array($hasilKemasAll) ? array_values($hasilKemasAll) : [];

            $persyaratanAll = $request->persyaratan;
            $persyaratanValues = is_array($persyaratanAll) ? array_values($persyaratanAll) : [];

            $hasilAtasAll = $request->hasil_atas;
            $hasilAtasValues = is_array($hasilAtasAll) ? array_values($hasilAtasAll) : [];

            $hasilTengahAll = $request->hasil_tengah;
            $hasilTengahValues = is_array($hasilTengahAll) ? array_values($hasilTengahAll) : [];

            $hasilBawahAll = $request->hasil_bawah;
            $hasilBawahValues = is_array($hasilBawahAll) ? array_values($hasilBawahAll) : [];

            $hasilInspeksiAll = $request->hasil_inspeksi;
            $hasilInspeksiValues = is_array($hasilInspeksiAll) ? array_values($hasilInspeksiAll) : [];

            $detailIdAll = $request->detail_id;
            $detailIdValues = is_array($detailIdAll) ? array_values($detailIdAll) : [];

            $loopIdx = 0;
            foreach($request->parameter as $key => $param) {
                $detailId = $detailIdAll[$key] ?? ($detailIdValues[$loopIdx] ?? null);

                $detail = null;
                if ($detailId && $existingById->has($detailId)) {
                    $detail = $existingById->get($detailId);
                } elseif ($existingByIndex->has($loopIdx)) {
                    $detail = $existingByIndex->get($loopIdx);
                }

                $detailData = [
                    'parameter' => $param,
                    'persyaratan' => $persyaratanAll[$key] ?? ($persyaratanValues[$loopIdx] ?? null),
                    'hasil_atas' => $hasilAtasAll[$key] ?? ($hasilAtasValues[$loopIdx] ?? null),
                    'hasil_tengah' => $hasilTengahAll[$key] ?? ($hasilTengahValues[$loopIdx] ?? null),
                    'hasil_bawah' => $hasilBawahAll[$key] ?? ($hasilBawahValues[$loopIdx] ?? null),
                    'hasil_json' => $hasilKemasAll[$key] ?? ($hasilKemasValues[$loopIdx] ?? null),
                    'hasil_inspeksi' => $hasilInspeksiAll[$key] ?? ($hasilInspeksiValues[$loopIdx] ?? null),
                ];

                if ($detail) {
                    $detail->update($detailData);
                } else {
                    $detailData['pm_laporan_inspeksi_id'] = $laporan->id;
                    PmLaporanInspeksiDetail::create($detailData);
                }
                $loopIdx++;
            }
        }
        return back()->with('success', 'Data Laporan Inspeksi berhasil diupdate.');
    }
}
