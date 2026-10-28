<?php

namespace App\Http\Controllers;

use App\PemantauanStabilitas;
use App\DetailPemantauanStabilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

use App\BagianKebersihan;
use App\TahapanProses;
use App\QualityControl;
use App\Produksi;
use App\SerahTerimaBarang;
use App\FormulaPcs;
use App\AcuanLaporanIpc;
use App\RiwayatAlat;
use App\PemeriksaanKebersihan;
use App\PenimbanganBahanBaku;
use App\CheckTahapanProses;
use App\RekonsiliasiBahanKemas;
use App\FormulaProduksi;
use App\QualityControlCheck;
use App\User;

class ProsesProduksiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function storeTahapan(Request $request) {
        foreach ($request->nama_proses as $index => $nama_proses) {
            if (!empty($nama_proses) && !empty($request->acuan[$index])) {
                TahapanProses::create([
                    'nama_proses' => $nama_proses,
                    'acuan' => $request->acuan[$index],
                    'product_id' => $request->product_id
                ]);
            }
        }
        
        return redirect()->back();
    }

    public function storeQuality(Request $request) {
        foreach ($request->parameter_pengujian as $index => $parameter) {
            QualityControl::create([
                'product_id' => $request->product_id[$index],
                'parameter_pengujian' => $parameter,
                'nilai_standart' => $request->nilai_standart[$index],
            ]);
        }
    
        return redirect()->back()->with('success', 'Data Quality Control berhasil disimpan!');
    }
    
    public function storeQcCheck(Request $request) {
        $sections = $request->input('sections', []);
        $productId = $request->product_id;

        foreach ($sections as $sectionName => $parameters) {
            foreach ($parameters as $paramName => $data) {
                $spesifikasi = $data['spesifikasi'] ?? null;

                // cek apakah sudah ada data sebelumnya
                $existing = AcuanLaporanIpc::where('product_id', $productId)
                    ->where('section_name', $sectionName)
                    ->where('pemeriksaan', $paramName)
                    ->first();

                if ($existing) {
                    $existing->update(['spesifikasi' => $spesifikasi]);
                } else {
                    AcuanLaporanIpc::create([
                        'product_id' => $productId,
                        'section_name' => $sectionName,
                        'pemeriksaan' => $paramName,
                        'spesifikasi' => $spesifikasi,
                    ]);
                }
            }
        }
        return redirect()->back()->with('success', 'Data QC berhasil disimpan atau diperbarui!');
    }


    public function storeCheckQuality(Request $request) 
    {
        $produksiId = $request->produksi_id;
        $qualityInput = $request->input('quality', []);

        if (empty($qualityInput)) {
            return redirect()->back()->with('error', 'Data quality kosong!');
        }

        $insertedItems = [];

        // 1. Simpan setiap item ke pemeriksaan_quality_control
        foreach ($qualityInput as $item) {
            $insertedItems[] = DB::table('pemeriksaan_quality_control')->insertGetId([
                'quality_id'  => $item['quality_id'],
                'produksi_id' => $produksiId,
                'nilai_real'  => $item['nilai_real'] ?? '-',
                'petugas_qc'  => $item['petugas_qc'] ?? '-',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $firstQualityId = $insertedItems[0];
        $firstRecord = DB::table('pemeriksaan_quality_control')->find($firstQualityId);

        if ($firstRecord) {
            $pemantauan_stabilitas = PemantauanStabilitas::create([
                'produksi_id'       => $firstRecord->produksi_id,
                'pemeriksaan_qc_id' => $firstRecord->id,
            ]);

            $tgl_submit = \Carbon\Carbon::parse($firstRecord->created_at);
            
            for ($i = 3; $i <= 24; $i += 3) {
                DetailPemantauanStabilitas::create([
                    'pemantauan_stabilitas_id' => $pemantauan_stabilitas->id,
                    'bulan_ke'                 => $i,
                    'tanggal_jadwal'           => $tgl_submit->copy()->addMonths($i),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data Quality Control berhasil disimpan!');
    }


    public function storeProses(Request $request) {
        $produksiId = $request->produksi_id;
        $proses = $request->input('proses', []);

        $dataToInsert = [];

        foreach ($proses as $tahapanId => $item) {
            $dataToInsert[] = [
                'tahapan_id'        => $tahapanId,
                'produksi_id'       => $produksiId,
                'keterangan'        => $item['keterangan'] ?? null,
                'penanggung_jawab'  => $item['operator'] ?? null,
                'durasi'            => $item['jam_pengerjaan'] ?? null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        if (!empty($dataToInsert)) {
            DB::table('check_tahapan_proses')->insert($dataToInsert);
        }

        return redirect()->back()->with('success', 'Data proses berhasil disimpan!');
    }


    public function storeRekonsiliasi(Request $request) {
        $produksiId = $request->produksi_id;
        $data = $request->rekonsiliasi ?? [];

        $insertData = [];

        foreach ($data as $key => $item) {
            $formulaId = is_numeric($key) ? $key : null;

            $insertData[] = [
                'formula_bahan_kemas_id' => $formulaId,
                'produksi_id'    => $produksiId,
                'nama_barang'    => $item['nama_barang'] ?? null,
                'tersedia'       => $item['tersedia'] ?? null,
                'terpakai'       => $item['terpakai'] ?? 0,
                'keterangan'     => $item['keterangan'] ?? null,
                'operator'       => $item['operator'] ?? null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        RekonsiliasiBahanKemas::insert($insertData);

        return back()->with('success', 'Data rekonsiliasi berhasil disimpan');
    }


    public function updateStatusKebersihan($id, Request $request) {
        $pemeriksaanKebersihan = PemeriksaanKebersihan::find($id);

        if (!$pemeriksaanKebersihan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $user = User::where('username', $request->users)->first();

        if (!$user || !$user->password || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Username atau password salah.']);
        }

        // Tambahkan pengecekan apakah dia punya role QC
        if (!$user->roles->contains('role_name', 'Quality Control')) {
            return response()->json(['success' => false, 'message' => 'User bukan QC']);
        }

        PemeriksaanKebersihan::where('produksi_id', $pemeriksaanKebersihan->produksi_id)
            ->update([
                'status' => 'Diverifikasi',
                'diperiksa_oleh' => $request->input('users')
            ]);

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui']);
    }

    public function updateStatusPenimbangan($id, Request $request) {
        $penimbanganBahanBaku = PenimbanganBahanBaku::find($id);

        if (!$penimbanganBahanBaku) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $user = User::where('username', $request->users)->first();

        if (!$user || !$user->password || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Username atau password salah.']);
        }

        // Tambahkan pengecekan apakah dia punya role QC
        if (!$user->roles->contains('role_name', 'Quality Control')) {
            return response()->json(['success' => false, 'message' => 'User bukan QC']);
        }

        // update semua dengan produksi_id yang sama
        PenimbanganBahanBaku::where('produksi_id', $penimbanganBahanBaku->produksi_id)
            ->update([
                'status' => 'Diverifikasi',
                'diperiksa_oleh' => $request->input('users')
            ]);

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui']);
    }

    public function updateStatusTahapan($id, Request $request) {
        $checkTahapanProses = CheckTahapanProses::find($id);

        if (!$checkTahapanProses) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $user = User::where('username', $request->users)->first();

        if (!$user || !$user->password || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Username atau password salah.']);
        }

        // Tambahkan pengecekan apakah dia punya role QC
        if (!$user->roles->contains('role_name', 'Quality Control')) {
            return response()->json(['success' => false, 'message' => 'User bukan QC']);
        }

        // update semua dengan produksi_id yang sama
        CheckTahapanProses::where('produksi_id', $checkTahapanProses->produksi_id)
            ->update([
                'status' => 'Diverifikasi',
                'diperiksa_oleh' => $request->input('users')
            ]);

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui']);
    }

    public function updateStatusRekonsiliasi($id, Request $request) {
        $rekonsiliasiBahanKemas = RekonsiliasiBahanKemas::find($id);

        if (!$rekonsiliasiBahanKemas) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $user = User::where('username', $request->users)->first();

        if (!$user || !$user->password || !Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Username atau password salah.']);
        }
        if (!$user->roles->contains('role_name', 'Quality Control')) {
            return response()->json(['success' => false, 'message' => 'User bukan QC']);
        }

        // ketika status memang sudah diiverifikasi maka tidak perlu update apapun pada rekonsilasi bahan kemas, dan langsung tutup produksi
        $isComplete = $request->has('complete_production') && $request->complete_production == 1;

        if ($rekonsiliasiBahanKemas->status !== 'Diverifikasi') {
            RekonsiliasiBahanKemas::where('produksi_id', $rekonsiliasiBahanKemas->produksi_id)
                ->update([
                    'status' => 'Diverifikasi',
                    'diperiksa_oleh' => $request->input('users')
                ]);
        }

        if ($isComplete) {
            Produksi::where('id', $rekonsiliasiBahanKemas->produksi_id)->update(['status' => 'done']);
        }

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui']);
    }

    public function updateKebersihanData(Request $request) {
        $data = $request->input('kebersihan', []);
        $riwayatAlatInput = $request->input('riwayat_alat', []);
        $produksiId = $request->input('produksi_id');

        DB::beginTransaction();
        try {
            // update pemeriksaan kebersihan
            foreach ($data as $id => $item) {
                PemeriksaanKebersihan::where('id', $id)->update([
                    'hasil' => $item['hasil'] ?? null,
                    'dibersihkan_oleh' => $item['dibersihkan_oleh'] ?? null,
                    'diperiksa_oleh' => $item['diperiksa_oleh'] ?? null,
                ]);
            }

            // update / simpan riwayat alat
            if (!empty($riwayatAlatInput) && $produksiId) {
                foreach ($riwayatAlatInput as $ruangId => $alatSet) {
                    foreach ($alatSet as $jenis => $alatData) {
                        $alatId   = $alatData['id'] ?? null;
                        $alatNama = $alatData['nama'] ?? null;

                        if ($alatId) {
                            // udah ada record → cek update / hapus
                            $riwayat = RiwayatAlat::where('id', $alatId)
                                ->where('produksi_id', $produksiId)
                                ->where('ruang_produksi_id', $ruangId)
                                ->first();

                            if ($riwayat) {
                                if (!empty($alatNama)) {
                                    // update kalau beda nama
                                    if ($riwayat->nama_alat !== $alatNama) {
                                        $riwayat->update(['nama_alat' => $alatNama]);
                                    }
                                } else {
                                    // kalau nama kosong, hapus record
                                    $riwayat->delete();
                                }
                            }
                        } else {
                            // belum ada record → create baru kalau ada nama
                            if (!empty($alatNama)) {
                                RiwayatAlat::create([
                                    'produksi_id'      => $produksiId,
                                    'ruang_produksi_id'=> $ruangId,
                                    'nama_alat'        => $alatNama,
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data kebersihan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Update gagal: ' . $e->getMessage());
        }
    }

    public function updatePenimbanganData(Request $request) {
        $data = $request->input('penimbangan', []);
        foreach ($data as $id => $item) {
            $hasil_timbang = str_replace(',', '.', $item['hasil_timbang']);

            PenimbanganBahanBaku::where('id', $id)->update([
                'no_batch' => $item['no_batch'] ?? '-',
                'hasil_timbang' => $hasil_timbang,
                'operator_penimbangan' => $item['operator_penimbangan'],
                'updated_at' => now(),
            ]);
        }
        return redirect()->back()->with('success', 'Data penimbangan berhasil diperbarui.');
    }

    public function updateDataQc(Request $request) {
        $data = $request->input('quality', []);

        foreach ($data as $id => $item) {
            QualityControlCheck::where('id', $id)->update([
                'nilai_real' => $item['nilai_real'] ?? null,
                'petugas_qc' => $item['petugas_qc'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Data Quality Berhasil Diperbaharui.');
    }

    public function updateProsesData(Request $request) {
        $data = $request->input('proses', []);

        foreach ($data as $id => $item) {
            $item = CheckTahapanProses::where('id', $id)->update([
                'keterangan'       => $item['keterangan'] ?? null,
                'penanggung_jawab' => $item['operator'] ?? null,
                'durasi'           => $item['jam_pengerjaan'] ?? null,
                'updated_at'       => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Data proses berhasil diperbarui.');
    }
    
    public function updateRekonsiliasiData(Request $request) {
        $data = $request->rekonsiliasi ?? [];

        foreach ($data as $bahanKemasId => $item) {

            if (!isset($item['id'])) {
                continue; // safety
            }

            RekonsiliasiBahanKemas::where('id', $item['id'])->update([
                'terpakai'   => $item['terpakai'] ?? null,
                'keterangan' => $item['keterangan'] ?? null,
                'operator'   => $item['operator'] ?? null,
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Data rekonsiliasi berhasil diperbarui');
    }


    function storeItems(Request $request) {
        $data = $request->all();
        BagianKebersihan::create($data);
        return redirect()->back()->with('success', 'Ruangan Berhasil Di Tambbhkan');
    }

    public function storeProduksi(Request $request) {
        $bagianKebersihanIds = $request->input('bagian_kebersihan_id', []);
        $hasil = $request->input('hasil', []);
        $dibersihkanOleh = $request->input('dibersihkan_oleh', []);
        $produksiId = $request->input('produksi_id');

        DB::beginTransaction();
        try {
            // --- simpan pemeriksaan kebersihan ---
            $dataToInsert = [];
            foreach ($bagianKebersihanIds as $index => $id) {
                if (!is_numeric($id) || !is_numeric($produksiId)) {
                    return response()->json(['success' => false, 'message' => 'ID tidak valid!']);
                }

                $dataToInsert[] = [
                    'bagian_kebersihan_id' => $id,
                    'hasil' => $hasil[$index],
                    'dibersihkan_oleh' => $dibersihkanOleh[$index] ?? null,
                    'produksi_id' => $produksiId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // insert pemeriksaan kebersihan
            DB::table('pemeriksaan_kebersihan')->insert($dataToInsert);

            // --- simpan riwayat alat ---
            if ($request->has('riwayat_alat')) {
                foreach ($request->riwayat_alat as $ruangId => $alatList) {
                    foreach ($alatList as $alat) {
                        if (!empty($alat)) {
                            DB::table('riwayat_alat')->insert([
                                'produksi_id' => $produksiId,
                                'ruang_produksi_id' => $ruangId,
                                'nama_alat' => $alat,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function storePenimbangan(Request $request) {
        $produksiId = $request->produksi_id;
        $durasi = $request->durasi;
        $data = $request->input('penimbangan', []);

        if (empty($data)) {
            return back()->with('error', 'Data penimbangan kosong');
        }

        foreach ($data as $item) {
            $hasil_timbang = str_replace(',', '.', $item['hasil_timbang']);

            DB::table('penimbangan_bahan_baku')->insert([
                'produksi_id' => $produksiId,
                'formula_bahan_baku_id' => $item['formula_id'] ?? null,
                'no_batch' => $item['no_batch'] ?? '-',
                'hasil_timbang' => $hasil_timbang,
                'operator_penimbangan' => $item['operator_penimbangan'] ?? null,
                'durasi' => $durasi,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }

    public function storeProdukJadi(Request $request) {
        // Ambil semua data request
        $dataAll = $request->all();
        $dataAll['acuan'] = preg_replace('/[^0-9]/', '', $request->acuan);
        $serahterimaBarang = SerahTerimaBarang::create($dataAll);

        return redirect()->back()->with('success', 'Data produk jadi dan formula per pcs berhasil disimpan.');
    }
    
    
    public function deleteTahapanProses($id) {
        $tahapanProses = TahapanProses::find($id);
        $tahapanProses->delete();
        return redirect()->back()->with('success', 'data berhasil di hapus.');
    }
    
    public function formulaDelete($id) {
        $formulaData = FormulaProduksi::find($id);
        $formulaData->delete();
        return redirect()->back()->with('success', 'data berhasil di hapus.');
    }

    public function deleteQualityControl($id) {
        $qualtiyControler = QualityControl::find($id);
        $qualtiyControler->delete();
        return redirect()->back()->with('success', 'data berhasil di hapus');
    }

    public function deleteProdukJadi($id) {
        $produkjadi = SerahTerimabarang::find($id);
        $produkjadi->delete();
        return redirect()->back()->with('success', 'data berhasil di hapus');
    }

    public function updateNamaProses(Request $request, $id) {
        $request->validate([
            'nama_proses' => 'required|string',
        ]);
    
        $tahapan = TahapanProses::findOrFail($id);
        $tahapan->nama_proses = $request->nama_proses;
        $tahapan->save();
    
        return response()->json(['success' => true]);
    }
}