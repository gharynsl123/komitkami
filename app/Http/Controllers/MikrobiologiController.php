<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\HasilMikrobiologi;
use App\Produksi;
use App\QualityControl;
use App\QualityControlCheck;
use Barryvdh\DomPDF\Facade\Pdf;

class MikrobiologiController extends Controller
{
    // Keyword parameter pengujian yang termasuk uji mikrobiologi
    const MIKRO_KEYWORDS = [
        'E. Coli',
        'S. Aureus',
        'C. albicans',
        'albicans',
    ];

    public function index()
    {
        /**
         * Mengambil data produksi yang formula quality control-nya memiliki parameter uji mikrobiologi
         * (seperti Uji Antibacteri : E. Coli, Uji Antibacteri : S. Aureus, Uji Antifungi : C. albicans)
         * dengan nilai standar selain null atau "-".
         *
         * Jika 3 parameter mikrobiologi tersebut berisi "-" atau kosong, maka jangan ambil item produksi itu.
         */
        $produksiList = Produksi::with(['product', 'bahanBaku.qualityControl', 'checkQualityControl.qualityControl', 'hasilMikrobiologi'])
            ->where(function ($query) {
                // 1. Cek parameter mikroba pada Quality Control milik Formula Bahan Baku
                $query->whereHas('bahanBaku.qualityControl', function ($qc) {
                    $qc->where(function ($sub) {
                        foreach (self::MIKRO_KEYWORDS as $keyword) {
                            $sub->orWhere('parameter_pengujian', 'like', '%' . $keyword . '%');
                        }
                    })
                    ->whereNotNull('nilai_standart')
                    ->whereRaw("TRIM(nilai_standart) != ''")
                    ->whereRaw("TRIM(nilai_standart) != '-'");
                })
                // 2. Atau dari Pemeriksaan Quality Control yang terhubung ke produksi
                ->orWhereHas('checkQualityControl.qualityControl', function ($qc) {
                    $qc->where(function ($sub) {
                        foreach (self::MIKRO_KEYWORDS as $keyword) {
                            $sub->orWhere('parameter_pengujian', 'like', '%' . $keyword . '%');
                        }
                    })
                    ->whereNotNull('nilai_standart')
                    ->whereRaw("TRIM(nilai_standart) != ''")
                    ->whereRaw("TRIM(nilai_standart) != '-'");
                });
            })
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
            $this->syncQualityControl($produksi, $submittedLogs, $request->petugas);

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

    /**
     * Update hasil uji mikroba untuk produksi tertentu (Mode Update).
     */
    public function updateProses(Request $request, $batch_number)
    {
        $request->validate([
            'produksi_id'  => 'required|exists:produksi,id',
            'tanggal'      => 'required|date',
            'sampel_uji'   => 'required|string|max:255',
            'waktu_kontak' => 'required|string|max:100',
            'no_batch'     => 'nullable|string|max:100',
            'petugas'      => 'nullable|string|max:255',
            'keterangan'   => 'nullable|string',
            'mikroba'      => 'required|array',
        ]);

        $produksi = Produksi::where('batch_number', $batch_number)
            ->orWhere('id', $request->produksi_id)
            ->firstOrFail();

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

            if (!empty($awalLog) || !empty($awalCfu)) {
                $submittedLogs[$key] = $awalLog;
            }

            // Simpan / perbarui record pengujian mikroba
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
        }

        // Sinkronkan ke data pemeriksaan Quality Control produksi
        $this->syncQualityControl($produksi, $submittedLogs, $request->petugas);

        return redirect()->back()->with('success', 'Data hasil uji mikroba dan pemeriksaan Quality Control berhasil diperbarui dan disinkronkan.');
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

    /**
     * Download / Stream PDF Laporan Hasil Uji Antimikroba Produksi
     * — 3 halaman terpisah: Candida albicans, E. Coli, S. Aureus
     */
    public function printLaporan($batch_number)
    {
        $produksi = Produksi::with(['product', 'hasilMikrobiologi'])
            ->where('batch_number', $batch_number)
            ->firstOrFail();

        $hasilAll = $produksi->hasilMikrobiologi;

        if ($hasilAll->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada data laporan mikrobiologi untuk batch ini.');
        }

        $namaProduk = $produksi->product->name ?? 'Sampel Uji';
        $noBatch    = $produksi->batch_number;
        $noDokumen  = $hasilAll->first()->no_dokumen ?? 'KIG-QC-F-09-02-01';

        // Kelompokkan per jenis mikroba (3 halaman)
        $groupedHasil = $this->groupHasilByMikroba($hasilAll);

        $pdf = Pdf::loadView('print.laporan-hasil-uji-antimikroba', compact(
            'produksi',
            'groupedHasil',
            'namaProduk',
            'noBatch',
            'noDokumen'
        ))->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan_Hasil_Uji_Antimikroba_' . $batch_number . '.pdf');
    }

    /**
     * Download / Stream PDF Laporan Hasil Uji Antimikroba Manual
     * — Karena manual hanya 1 baris, tampilkan 1 halaman saja untuk mikroba terkait
     */
    public function printLaporanManual($id)
    {
        $hasil      = HasilMikrobiologi::findOrFail($id);
        $hasilAll   = collect([$hasil]);
        $namaProduk = $hasil->sampel_uji;
        $noBatch    = $hasil->no_batch ?? '-';
        $noDokumen  = $hasil->no_dokumen ?? 'KIG-QC-F-09-02-01';
        $produksi   = null;

        // Kelompokkan (manual hanya 1 mikroba, sisanya kosong)
        $groupedHasil = $this->groupHasilByMikroba($hasilAll);

        $pdf = Pdf::loadView('print.laporan-hasil-uji-antimikroba', compact(
            'produksi',
            'groupedHasil',
            'namaProduk',
            'noBatch',
            'noDokumen'
        ))->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan_Hasil_Uji_Antimikroba_Manual_' . $hasil->id . '.pdf');
    }

    /**
     * Kelompokkan hasil uji berdasarkan 3 jenis mikroba standar.
     * Mengembalikan array berisi 3 elemen dengan label dan koleksi data masing-masing.
     */
    private function groupHasilByMikroba($hasilAll): array
    {
        $groups = [
            [
                'label' => 'Candida albicans',
                'items' => collect(),
            ],
            [
                'label' => 'Staphylococcus aureus',
                'items' => collect(),
            ],
            [
                'label' => 'Escherichia coli',
                'items' => collect(),
            ],
        ];

        foreach ($hasilAll as $row) {
            $p = strtolower($row->mikroba_uji ?? '');
            if (str_contains($p, 'candida') || str_contains($p, 'c. albicans')) {
                $groups[0]['items']->push($row);
            } elseif (str_contains($p, 'staph') || str_contains($p, 's. aureus')) {
                $groups[1]['items']->push($row);
            } elseif (str_contains($p, 'e. coli') || str_contains($p, 'escherichia') || str_contains($p, 'ecoli')) {
                $groups[2]['items']->push($row);
            } else {
                // Mikroba lain masuk ke grup Candida sebagai fallback
                $groups[0]['items']->push($row);
            }
        }

        return $groups;
    }

    /**
     * Sinkronkan hasil uji mikrobiologi ke data pemeriksaan Quality Control produksi
     */
    private function syncQualityControl(Produksi $produksi, array $submittedLogs, ?string $petugas = null): void
    {
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

        $existingChecks = QualityControlCheck::with('qualityControl')
            ->where('produksi_id', $produksi->id)
            ->get();

        if ($existingChecks->isNotEmpty()) {
            foreach ($existingChecks as $check) {
                $param = $check->qualityControl->parameter_pengujian ?? '';
                foreach ($standarOrganisme as $key => $info) {
                    if (stripos($param, $info['keyword']) !== false) {
                        if (!empty($submittedLogs[$key])) {
                            $check->update([
                                'nilai_real' => number_format((float)$submittedLogs[$key], 2, ',', '.') . ' Log',
                                'petugas_qc' => $petugas ?: ($check->petugas_qc ?: '-'),
                            ]);
                        } else {
                            $check->update([
                                'nilai_real' => '-',
                                'petugas_qc' => $petugas ?: ($check->petugas_qc ?: '-'),
                            ]);
                        }
                    }
                }
            }
        } else {
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
                        'petugas_qc'  => $isMikroba && !empty($nilai) && $nilai !== '-' ? ($petugas ?: '-') : '-',
                    ]);
                }
            } else {
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
                            'petugas_qc'  => $nilai !== '-' ? ($petugas ?: '-') : '-',
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Ekstrak estimasi koloni dan pangkat (10^x) dari nilai CFU tersimpan
     * Contoh: 970000 => ['97', '4']
     *         10     => ['1', '1']
     *         4      => ['4', '0']
     */
    public static function extractKoloniPangkat($cfu): array
    {
        if ($cfu === null || $cfu === '' || !is_numeric($cfu) || $cfu < 0) {
            return ['', ''];
        }
        $cfu = (float) $cfu;
        if ($cfu == 0.0) {
            return ['0', '0'];
        }
        $pangkat = 0;
        $koloni = $cfu;
        while ($koloni >= 10 && fmod($koloni, 10) == 0.0) {
            $koloni /= 10;
            $pangkat++;
        }
        return [(string)$koloni, (string)$pangkat];
    }
}
