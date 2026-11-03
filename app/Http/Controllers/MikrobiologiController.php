<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\HasilMikrobiologi;
use App\Produksi;
use App\QualityControl;
use App\QualityControlCheck;

class MikrobiologiController extends Controller
{
    // Keyword parameter pengujian yang termasuk uji mikrobiologi
    const MIKRO_KEYWORDS = [
        'Uji Antibacteri : E. Coli',
        'Uji Antibacteri : S. Aureus',
        'Uji Antifungi : C. albicans',
    ];

    public function index()
    {
        /**
         * Mengambil data produksi yang quality control-nya memiliki parameter
         * "Uji Antibakteri : E. Coli", "Uji Antibakteri : S. Aureus", atau
         * "Uji Antifungi : C. albicans" DAN nilai_real-nya bukan "-" (sudah diisi).
         *
         * Caranya:
         * 1. Cari quality_control yang parameter_pengujian mengandung keyword mikro.
         * 2. Dari situ cari pemeriksaan_quality_control yang nilai_real != '-'.
         * 3. Ambil produksi_id yang unik dari sana.
         * 4. Load produksi beserta product dan hasil uji mikrobiologi yang sudah ada.
         */
        /**
         * Mengambil data produksi yang memiliki parameter uji mikrobiologi pada quality control-nya
         * (baik yang sudah diuji maupun yang belum diuji) atau yang sudah memiliki riwayat hasil uji.
         */
        $produksiList = Produksi::with(['product', 'checkQualityControl.qualityControl', 'hasilMikrobiologi'])
            ->whereHas('checkQualityControl.qualityControl', function ($qc) {
                $qc->where(function ($sub) {
                    foreach (self::MIKRO_KEYWORDS as $keyword) {
                        $sub->orWhere('parameter_pengujian', 'like', '%' . $keyword . '%');
                    }
                });
            })
            ->orWhereHas('hasilMikrobiologi')
            ->orderBy('tanggal_produksi', 'desc')
            ->get();

        // Data manual (tidak terhubung ke produksi)
        $manualList = HasilMikrobiologi::manual()
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('uji-mikrobiologi.index-mikrobiologi', compact('produksiList', 'manualList'));
    }

    /**
     * Store manual report entry or batch process from produksi.
     */
    public function store(Request $request)
    {
        // ─── Mode 1: Submit dari Halaman Proses Uji Mikroba Produksi (3 baris mikroba) ───
        if ($request->has('mikroba') && is_array($request->mikroba)) {
            $request->validate([
                'produksi_id'  => 'required|exists:produksi,id',
                'tanggal'      => 'required|date',
                'sampel_uji'   => 'required|string|max:255',
                'waktu_kontak' => 'required|string|max:100',
                'no_batch'     => 'nullable|string|max:100',
                'petugas'      => 'nullable|string|max:255',
                'keterangan'   => 'nullable|string',
            ]);

            $produksi = Produksi::findOrFail($request->produksi_id);

            // Mapping 3 standar mikroba
            $standarOrganisme = [
                'candida' => [
                    'keyword' => 'C. albicans',
                    'nama'    => 'Uji Antifungi : C. albicans',
                ],
                'staph' => [
                    'keyword' => 'S. Aureus',
                    'nama'    => 'Uji Antibakteri : S. Aureus',
                ],
                'ecoli' => [
                    'keyword' => 'E. Coli',
                    'nama'    => 'Uji Antibakteri : E. Coli',
                ],
            ];

            $savedCount = 0;
            $submittedLogs = [];

            foreach ($request->mikroba as $key => $item) {
                $orgInfo = $standarOrganisme[$key] ?? null;
                $namaMikroba = $item['nama'] ?? ($orgInfo['nama'] ?? $key);

                $awalCfu = isset($item['awal_cfu_ml']) && $item['awal_cfu_ml'] !== '' ? $item['awal_cfu_ml'] : null;
                $awalLog = isset($item['awal_log']) && $item['awal_log'] !== '' ? $item['awal_log'] : null;
                $cleanCfu = isset($item['clean_cfu_ml']) && $item['clean_cfu_ml'] !== '' ? $item['clean_cfu_ml'] : null;
                $cleanLog = isset($item['clean_log']) && $item['clean_log'] !== '' ? $item['clean_log'] : null;
                $dirtyCfu = isset($item['dirty_cfu_ml']) && $item['dirty_cfu_ml'] !== '' ? $item['dirty_cfu_ml'] : null;
                $dirtyLog = isset($item['dirty_log']) && $item['dirty_log'] !== '' ? $item['dirty_log'] : null;
                $persenClean = isset($item['persen_clean']) && $item['persen_clean'] !== '' ? $item['persen_clean'] : null;
                $persenDirty = isset($item['persen_dirty']) && $item['persen_dirty'] !== '' ? $item['persen_dirty'] : null;

                // Catat log jika terisi
                if (!empty($awalLog) || !empty($awalCfu)) {
                    $submittedLogs[$key] = $awalLog;
                }

                // Simpan atau update record hasil_uji_mikrobiologi jika ada data yang diisi
                if (!empty($awalCfu) || !empty($awalLog) || !empty($cleanCfu) || !empty($dirtyCfu)) {
                    HasilMikrobiologi::updateOrCreate(
                        [
                            'produksi_id' => $produksi->id,
                            'mikroba_uji' => $namaMikroba,
                        ],
                        [
                            'tanggal'      => $request->tanggal,
                            'no_batch'     => $produksi->batch_number ?? $request->no_batch,
                            'sampel_uji'   => $request->sampel_uji,
                            'waktu_kontak' => $request->waktu_kontak,
                            'awal_cfu_ml'  => $awalCfu,
                            'awal_log'     => $awalLog,
                            'clean_cfu_ml' => $cleanCfu,
                            'clean_log'    => $cleanLog,
                            'dirty_cfu_ml' => $dirtyCfu,
                            'dirty_log'    => $dirtyLog,
                            'persen_clean' => $persenClean,
                            'persen_dirty' => $persenDirty,
                            'keterangan'   => $request->keterangan,
                            'petugas'      => $request->petugas,
                            'is_manual'    => false,
                        ]
                    );
                    $savedCount++;
                }
            }

            // ─── Otomatis Update / Isi Data Pemeriksaan Quality Control ───
            $existingChecks = QualityControlCheck::with('qualityControl')
                ->where('produksi_id', $produksi->id)
                ->get();

            if ($existingChecks->isNotEmpty()) {
                // Update pemeriksaan QC yang sudah ada
                foreach ($existingChecks as $check) {
                    $param = $check->qualityControl->parameter_pengujian ?? '';
                    foreach ($standarOrganisme as $key => $info) {
                        if (stripos($param, $info['keyword']) !== false) {
                            if (!empty($submittedLogs[$key])) {
                                $check->update([
                                    'nilai_real' => number_format((float)$submittedLogs[$key], 2, ',', '.') . ' Log',
                                    'petugas_qc' => $request->petugas ?: ($check->petugas_qc ?: '-'),
                                ]);
                            } else {
                                // Jika mikroba ini tidak diisi/tidak diuji, set '-' jika belum terisi
                                if (empty($check->nilai_real) || $check->nilai_real === '-') {
                                    $check->update([
                                        'nilai_real' => '-',
                                        'petugas_qc' => $request->petugas ?: '-',
                                    ]);
                                }
                            }
                        }
                    }
                }
            } else {
                // Jika pemeriksaan_quality_control belum ada recordnya untuk produksi ini, buatkan
                $formulaId = $produksi->formula_bahan_baku_id;
                $qcMasterList = $formulaId ? QualityControl::where('formula_id', $formulaId)->get() : collect();

                if ($qcMasterList->isNotEmpty()) {
                    foreach ($qcMasterList as $qc) {
                        $isMikroba = false;
                        $nilai = '-';
                        foreach ($standarOrganisme as $key => $info) {
                            if (stripos($qc->parameter_pengujian, $info['keyword']) !== false) {
                                $isMikroba = true;
                                if (!empty($submittedLogs[$key])) {
                                    $nilai = number_format((float)$submittedLogs[$key], 2, ',', '.') . ' Log';
                                }
                                break;
                            }
                        }
                        QualityControlCheck::create([
                            'produksi_id' => $produksi->id,
                            'quality_id'  => $qc->id,
                            'nilai_real'  => $nilai,
                            'petugas_qc'  => $isMikroba && !empty($nilai) && $nilai !== '-' ? ($request->petugas ?: '-') : '-',
                        ]);
                    }
                } else {
                    // Jika formula belum di-set, kaitkan dengan master parameter QC global
                    foreach ($standarOrganisme as $key => $info) {
                        $qc = QualityControl::where('parameter_pengujian', 'like', '%' . $info['keyword'] . '%')->first();
                        if ($qc) {
                            $nilai = !empty($submittedLogs[$key])
                                ? number_format((float)$submittedLogs[$key], 2, ',', '.') . ' Log'
                                : '-';
                            QualityControlCheck::create([
                                'produksi_id' => $produksi->id,
                                'quality_id'  => $qc->id,
                                'nilai_real'  => $nilai,
                                'petugas_qc'  => $nilai !== '-' ? ($request->petugas ?: '-') : '-',
                            ]);
                        }
                    }
                }
            }

            return redirect()->back()->with('success', 'Hasil uji mikroba dan data pemeriksaan quality control berhasil disimpan dan disinkronkan.');
        }

        // ─── Mode 2: Submit Laporan Manual Biasa (dari modal index) ───
        $request->validate([
            'tanggal'      => 'required|date',
            'sampel_uji'   => 'required|string|max:255',
            'mikroba_uji'  => 'required|string|max:255',
            'waktu_kontak' => 'required|string|max:100',
            'no_batch'     => 'nullable|string|max:100',
            'petugas'      => 'nullable|string|max:255',
            'keterangan'   => 'nullable|string',
            'awal_cfu_ml'  => 'nullable|numeric',
            'awal_log'     => 'nullable|numeric',
            'clean_cfu_ml' => 'nullable|numeric',
            'clean_log'    => 'nullable|numeric',
            'dirty_cfu_ml' => 'nullable|numeric',
            'dirty_log'    => 'nullable|numeric',
            'persen_clean' => 'nullable|numeric',
            'persen_dirty' => 'nullable|numeric',
            'produksi_id'  => 'nullable|exists:produksi,id',
        ]);

        $isManual = empty($request->produksi_id);

        HasilMikrobiologi::create([
            ...$request->only([
                'tanggal', 'sampel_uji', 'mikroba_uji', 'waktu_kontak', 'no_batch',
                'petugas', 'keterangan',
                'awal_cfu_ml', 'awal_log',
                'clean_cfu_ml', 'clean_log',
                'dirty_cfu_ml', 'dirty_log',
                'persen_clean', 'persen_dirty',
                'produksi_id',
            ]),
            'is_manual'  => $isManual,
        ]);

        $message = $isManual ? 'Laporan manual berhasil disimpan.' : 'Data hasil uji mikroba produksi berhasil disimpan.';
        return redirect()->back()->with('success', $message);
    }

    public function proses($batch_number) {
        $produksi = Produksi::with(['product', 'checkQualityControl.qualityControl', 'hasilMikrobiologi'])
            ->where('batch_number', $batch_number)
            ->first();

        if (!$produksi) {
            abort(404, 'Produksi tidak ditemukan.');
        }

        // Ambil QC checks yang terkait uji mikroba
        $mikroChecks = $produksi->checkQualityControl->filter(function ($check) {
            $param = $check->qualityControl->parameter_pengujian ?? '';
            return str_contains($param, 'E. Coli')
                || str_contains($param, 'S. Aureus')
                || str_contains($param, 'C. albicans');
        });

        // Riwayat laporan hasil mikroba yang sudah ada untuk batch produksi ini
        $existingHasil = $produksi->hasilMikrobiologi;

        // Group by organism key agar bisa pre-fill jika sudah pernah diisi
        $existingByOrg = [];
        foreach ($existingHasil as $row) {
            $p = strtolower($row->mikroba_uji);
            if (str_contains($p, 'c. albicans') || str_contains($p, 'candida')) {
                $existingByOrg['candida'] = $row;
            } elseif (str_contains($p, 's. aureus') || str_contains($p, 'staph')) {
                $existingByOrg['staph'] = $row;
            } elseif (str_contains($p, 'e. coli') || str_contains($p, 'escherichia')) {
                $existingByOrg['ecoli'] = $row;
            }
        }

        return view('uji-mikrobiologi.proses-uji-mirkoba', compact('produksi', 'mikroChecks', 'existingHasil', 'existingByOrg'));
    }
}
