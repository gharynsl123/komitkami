@php
    $master = $type == 'manual' ? $manual_report : $stock_in;

    // Fallbacks for manual report 
    $tanggal_ed = $type == 'manual' ? $master->expired_date : $master->tanggal_ed;
    $vendor = $type == 'manual' ? '-' : $master->vendor;
    $stok = $type == 'manual' ? $pengambilanSampel->jumlah_barang_datang : $master->stok;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pengecekan Material</title>
    <style>
        @page {
            margin-top: 25px;
            margin-bottom: 30px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .page-break {
            page-break-after: always;
        }

        @page landscape_page {
            size: A4 landscape;
        }

        .landscape-section {
            page: landscape_page;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        .mb-3 {
            margin-bottom: 15px;
        }

        .mt-3 {
            margin-top: 15px;
        }

        .w-100 {
            width: 100%;
        }

        .w-50 {
            width: 50%;
        }

        .title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .checkbox-container {
            display: inline-block;
            margin-right: 15px;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            font-family: 'DejaVu Sans', sans-serif;
            margin-right: 5px;
            position: relative;
            top: 2px;
        }

        .checkbox.checked:after {
            content: "✓";
            position: absolute;
            top: -3px;
            left: 1px;
            font-size: 12px;
        }

        .label-cell {
            width: 30%;
        }
    </style>
</head>

<body>
    @php
        $logo = public_asset('images/KING-DATAR.png');

        $isBahanBaku = ($inventory->type == 'Bahan Baku');

        $getResizableStyle = function ($text) {
            $len = strlen($text);
            if ($len > 45) {
                return 'font-size: 8.5px;';
            } elseif ($len > 30) {
                return 'font-size: 9.5px;';
            }
            return '';
        };

        // Helper to format date
        $formatDate = function ($date) {
            return $date ? \Carbon\Carbon::parse($date)->format('d-m-Y') : '-';
        };

        // Spesifikasi Helper
        $getSpec = function ($section, $param) use ($spesifikasi) {
            $spec = collect($spesifikasi)->where('section_name', $section)->where('parameter', $param)->first();
            return $spec ? $spec->spesifikasi : '-';
        };

        // Determine Manufacturer data globally for this view
        $mfgName = '-';
        $mfgAddress = '-';
        if (isset($manufacturer_id) && $manufacturer_id) {
            $mfg = $inventory->manufacturers->where('id', $manufacturer_id)->first();
            if ($mfg) {
                $mfgName = $mfg->name;
                $mfgAddress = $mfg->address;
            }
        }
    @endphp

    <!-- 1. INSPEKSI PENERIMAAN -->
    @if($isBahanBaku || $inspeksiPenerimaan)
        <div class="header mb-2">
            @if($logo)
                <img src="{{ $logo }}" alt="Logo" style="height: 80px;">
            @else
                <h2>KOMITKAMI INTINUSA GEMILANG</h2>
            @endif
        </div>

        <table class="table-bordered w-100">
            <tr>
                <td colspan="4" class="text-center">
                    <div class="title mt-3">INSPEKSI PENERIMAAN {{ strtoupper($inventory->type) }}</div>
                    <div class="text-left mt-3" style="font-size: 10px;">Nomor Dokumen : KIG-QC-F-01-01-02</div>
                </td>
            </tr>
            <tr>
                <td class="label-cell">Bagian</td>
                <td colspan="3">: QC</td>
            </tr>
            <tr>
                <td class="label-cell">Tanggal</td>
                <td colspan="3">: {{ $inspeksiPenerimaan->tanggal->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td class="label-cell">Nama Material</td>
                <td colspan="3">: <span style="{{ $getResizableStyle($inventory->name) }}">{{ $inventory->name }}</span></td>
            </tr>
            <tr>
                <td class="label-cell">No. Batch</td>
                <td colspan="3">: {{ $master->nomor_batch ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Expired Date</td>
                <td colspan="3">: {{ $formatDate($pengambilanSampel->expired_date ?? $inspeksiPenerimaan->tanggal_ed ?? $tanggal_ed) }}</td>
            </tr>
            <tr>
                <td class="label-cell">Nama Supplier</td>
                <td colspan="3">: {{ $master->suplierMaterial->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Negara Supplier</td>
                <td colspan="3">: {{ $master->suplierMaterial->address ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Nama Produsen</td>
                <td colspan="3">: {{ $mfgName }}</td>
            </tr>
            <tr>
                <td class="label-cell">Negara Produsen</td>
                <td colspan="3">: {{ $inspeksiPenerimaan->negara_asal ?? $mfgAddress ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Dokumen Pendukung</td>
                <td colspan="3">:
                    <span class="checkbox-container"><span
                            class="checkbox {{ ($inspeksiPenerimaan && $inspeksiPenerimaan->doc_coa) ? 'checked' : '' }}"></span>
                        CoA</span>
                    <span class="checkbox-container"><span
                            class="checkbox {{ ($inspeksiPenerimaan && $inspeksiPenerimaan->doc_msds) ? 'checked' : '' }}"></span>
                        MSDS</span>
                    <span class="checkbox-container"><span
                            class="checkbox {{ ($inspeksiPenerimaan && $inspeksiPenerimaan->doc_halal) ? 'checked' : '' }}"></span>
                        Sertifikat Halal</span>
                    <span class="checkbox-container"><span
                            class="checkbox {{ ($inspeksiPenerimaan && $inspeksiPenerimaan->doc_lainnya) ? 'checked' : '' }}"></span>
                        Doc. Lainnya: {{ $inspeksiPenerimaan->doc_lainnya ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td class="text-center font-weight-bold" rowspan="2" style="vertical-align: middle;">Pemeriksaan</td>
                <td class="text-center font-weight-bold" rowspan="2" style="vertical-align: middle;">Spesifikasi</td>
                <td class="text-center font-weight-bold" colspan="2">Hasil</td>
            </tr>
            <tr>
                <td class="text-center font-weight-bold">Atas</td>
                <td class="text-center font-weight-bold">Bawah</td>
            </tr>
            <tr>
                <td>
                    Organoleptis :<br><br>
                    Bentuk<br><br>
                    Warna<br><br>
                    Rasa
                </td>
                <td style="text-align: center">
                    <br><br>
                    {{ $getSpec('Organolepis', 'Bentuk') }}<br><br>
                    {{ $getSpec('Organolepis', 'Warna') }}<br><br>
                    {{ $getSpec('Organolepis', 'Rasa') }}
                </td>
                <td style="text-align: center">
                    <br><br>
                    {{ $inspeksiPenerimaan->hasil_bentuk_atas ?? '-' }}<br><br>
                    {{ $inspeksiPenerimaan->hasil_warna_atas ?? '-' }}<br><br>
                    {{ $inspeksiPenerimaan->hasil_rasa_atas ?? '-' }}
                </td>
                <td style="text-align: center">
                    <br><br>
                    {{ $inspeksiPenerimaan->hasil_bentuk_bawah ?? '-' }}<br><br>
                    {{ $inspeksiPenerimaan->hasil_warna_bawah ?? '-' }}<br><br>
                    {{ $inspeksiPenerimaan->hasil_rasa_bawah ?? '-' }}
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding: 20px 5px;">
                    Kesimpulan : &nbsp;&nbsp;&nbsp; Memenuhi syarat
                    <span class="checkbox-container"><span
                            class="checkbox {{ ($inspeksiPenerimaan && in_array($inspeksiPenerimaan->kesimpulan, ['Memenuhi syarat', 'Memenuhi Syarat', 'memenuhisyarat'])) ? 'checked' : '' }}"></span>
                    </span>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Tidak Memenuhi Syarat
                    <span class="checkbox-container"><span
                            class="checkbox {{ ($inspeksiPenerimaan && in_array($inspeksiPenerimaan->kesimpulan, ['Tidak Memenuhi Syarat', 'tidakmemenuhisyarat'])) ? 'checked' : '' }}"></span>
                    </span>
                    <br><br><br><br>
                    Petugas QC : {{ $inspeksiPenerimaan->petugas_qc ?? '-' }}
                </td>
            </tr>
        </table>
    @endif

    @if($isBahanBaku)
        <div class="page-break"></div>
    @endif

    <!-- 2. PENGAMBILAN SAMPEL MATERIAL -->
    @if($pengambilanSampel)
        <div class="header mb-2">
            @if($logo)
                <img src="{{ $logo }}" alt="Logo" style="height: 80px;">
            @else
                <h2>KOMITKAMI INTINUSA GEMILANG</h2>
            @endif
        </div>

        <table class="table-bordered w-100">
            <tr>
                <td colspan="2" class="text-center">
                    <div class="title mt-3">FORM PENGAMBILAN SAMPEL MATERIAL</div>
                    <div class="text-left mt-3" style="font-size: 10px;">Nomor Dokumen : KIG-QC-F-04-03-02</div>
                </td>
            </tr>
            <tr>
                <td class="label-cell">Tanggal Sampling</td>
                <td>: {{ $formatDate($pengambilanSampel->tanggal_sampling) }}</td>
            </tr>
            <tr>
                <td class="label-cell">Kategori Material</td>
                <td>:
                    @php
                        $kategori = $pengambilanSampel->kategori_material ?? $inventory->type;
                    @endphp
                    <span class="checkbox-container"><span
                            class="checkbox {{ ($kategori == 'Bahan Baku') ? 'checked' : '' }}"></span> Bahan Baku</span>
                    <span class="checkbox-container"><span
                            class="checkbox {{ ($kategori != 'Bahan Baku') ? 'checked' : '' }}"></span> Bahan
                        Pengemas</span>
                    <span class="checkbox-container"><span
                            class="checkbox {{ ($kategori == 'Bahan Lainnya') ? 'checked' : '' }}"></span> Bahan
                        Lainnya</span>
                </td>
            </tr>
            <tr>
                <td class="label-cell">Nama Material</td>
                <td>: <span style="{{ $getResizableStyle($inventory->name) }}">{{ $inventory->name }}</span></td>
            </tr>
            <tr>
                <td class="label-cell">Nama Supplier</td>
                <td>: {{ $master->suplierMaterial->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Negara Supplier</td>
                <td>: {{ $master->suplierMaterial->address ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Nama Produsen</td>
                <td>: {{ $mfgName }}</td>
            </tr>
            <tr>
                <td class="label-cell">Negara Produsen</td>
                <td>: {{ $inspeksiPenerimaan->negara_asal ?? $mfgAddress ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Nomor Bets</td>
                <td>: {{ $master->nomor_batch ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Expired Date</td>
                <td>: {{ $formatDate($pengambilanSampel->expired_date ?? $tanggal_ed) }}</td>
            </tr>
            <tr>
                <td class="label-cell">Dokumen Pendukung Bahan</td>
                <td>:
                    @php
                        $docs = [];
                        if ($pengambilanSampel->doc_coa)
                            $docs[] = "CoA";
                        if ($pengambilanSampel->doc_msds)
                            $docs[] = "MSDS";
                        if ($pengambilanSampel->doc_halal)
                            $docs[] = "Sertifikat Halal";
                        if ($pengambilanSampel->doc_lainnya)
                            $docs[] = $pengambilanSampel->doc_lainnya;
                    @endphp
                    {{ implode(', ', $docs) }}
                </td>
            </tr>
            <tr>
                <td class="label-cell">Referensi Metode Sampling</td>
                <td>: {{ $pengambilanSampel->referensi_metode ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Jumlah Barang Datang</td>
                <td>: {{ formatJumlah($stok) }} {{ $inventory->type == 'Bahan Baku' ? 'Gram' : 'Pcs' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Jumlah Sampel diambil</td>
                <td>: {{ $pengambilanSampel->jumlah_sampel ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Peralatan Sampling</td>
                <td>: {{ $pengambilanSampel->peralatan_sampling ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Lokasi Sampling</td>
                <td>: {{ $pengambilanSampel->lokasi_sampling ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Catatan</td>
                <td>: {{ $pengambilanSampel->catatan ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 20px 5px; border-right: none; text-align: center;">
                    Disampling Oleh :
                    <br><br><br><br>
                    Tanggal :
                </td>
                <td style="padding: 20px 5px; border-left: none; text-align: center;">
                    Diperiksa Oleh :
                    <br><br><br><br>
                    Tanggal :
                </td>
            </tr>
        </table>
        <div class="page-break"></div>
    @endif


    @php
        $numSamples = 1; // default
        if (isset($laporanInspeksi) && $laporanInspeksi && $laporanInspeksi->jumlah_sampel) {
            preg_match('/\d+/', $laporanInspeksi->jumlah_sampel, $matches);
            if (!empty($matches[0]) && intval($matches[0]) > 0) {
                $numSamples = intval($matches[0]);
            }
        }
    @endphp

    @if($isBahanBaku)
        <!-- 3. LAPORAN INSPEKSI RAW MATERIAL -->

        @if($laporanInspeksi)
            <div class="header mb-2">
                @if($logo)
                    <img src="{{ $logo }}" alt="Logo" style="height: 80px;">
                @else
                    <h2>KOMITKAMI INTINUSA GEMILANG</h2>
                @endif
            </div>

            <table class="table-bordered w-100">
                <tr>
                    <td colspan="4" class="text-center">
                        <div class="title mt-3">LAPORAN INSPEKSI RAW MATERIAL</div>
                        <div class="text-left mt-3" style="font-size: 10px;">Nomor Dokumen : KIG-QC-F-04-07-00</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%;">Kategori Material</td>
                    <td style="width: 25%;">: {{ $inventory->type }}</td>
                    <td style="width: 25%;">Tanggal Efektif</td>
                    <td style="width: 25%;">: {{ $formatDate($laporanInspeksi->tanggal_efektif) }}</td>
                </tr>
                <tr>
                    <td>Nama Material</td>
                    <td>: <span style="{{ $getResizableStyle($inventory->name) }}">{{ $inventory->name }}</span></td>
                    <td>Jumlah Material</td>
                    <td>: {{ $laporanInspeksi->jumlah_material ? $laporanInspeksi->jumlah_material . ' ' . ($inventory->type == 'Bahan Baku' ? 'Gram' : 'Pcs') : '-' }}</td>
                </tr>
                <tr>
                    <td>Kode Material</td>
                    <td>: <span style="{{ $getResizableStyle($inventory->code) }}">{{ $inventory->code }}</span></td>
                    <td>Jumlah Sampel</td>
                    <td>: {{ $laporanInspeksi->jumlah_sampel ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Nomor Bets</td>
                    <td>: {{ $master->nomor_batch ?? '-' }}</td>
                    <td>Tanggal Inspeksi</td>
                    <td>: {{ $formatDate($laporanInspeksi->tanggal_inspeksi) }}</td>
                </tr>
                <tr>
                    <td>Nama Supplier</td>
                    <td>: {{ $master->suplierMaterial->name ?? '-' }}</td>
                    <td>Alamat Supplier</td>
                    <td>: {{ $master->suplierMaterial->address ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Nama Produsen</td>
                    <td>: {{ $mfgName }}</td>
                    <td>Negara Produsen</td>
                    <td>: {{ $inspeksiPenerimaan->negara_asal ?? $mfgAddress ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Disiapkan Oleh</td>
                    <td>: {{ $laporanInspeksi->disiapkan_oleh ?? '-' }}</td>
                    <td>Disetujui oleh</td>
                    <td>: {{ $laporanInspeksi->disetujui_oleh ?? '-' }}</td>
                </tr>
            </table>

            <table class="table-bordered w-100" style="margin-top: 10px;">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Parameter</th>
                        <th rowspan="2">Persyaratan</th>
                        <th colspan="3">Hasil</th>
                        <th rowspan="2">Hasil Inspeksi</th>
                    </tr>
                    <tr>
                        <th>Atas</th>
                        <th>Tengah</th>
                        <th>Bawah</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($laporanInspeksi->details as $detail)
                        @php
                            $persyaratan = trim($detail->persyaratan);
                            $hasil_atas = trim($detail->hasil_atas ?? '-');
                            $hasil_tengah = trim($detail->hasil_tengah ?? '-');
                            $hasil_bawah = trim($detail->hasil_bawah ?? '-');
                            
                            // Skip row if persyaratan is '-' OR all results are '-'
                            if ($persyaratan === '-' || ($hasil_atas === '-' && $hasil_tengah === '-' && $hasil_bawah === '-')) {
                                continue;
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ trim(str_replace(': Spesifikasi', '', $detail->parameter)) }}</td>
                            <td>{{ $detail->persyaratan }}</td>
                            <td>{{ $detail->hasil_atas ?? '-' }}</td>
                            <td>{{ $detail->hasil_tengah ?? '-' }}</td>
                            <td>{{ $detail->hasil_bawah ?? '-' }}</td>
                            <td style="white-space: nowrap;">
                                <span class="checkbox {{ ($detail->hasil_inspeksi == 'Pass') ? 'checked' : '' }}"></span> Pass
                                &nbsp;
                                <span class="checkbox {{ ($detail->hasil_inspeksi == 'Fail') ? 'checked' : '' }}"></span> Fail
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="w-100 mt-3" style="border: none;">
                <tr>
                    <td style="border: none; padding: 10px;">
                        <strong>Diinspeksi Oleh :</strong><br> {{ $laporanInspeksi->diinspeksi_oleh ?? '-' }}<br><br>
                        <strong>Tanggal :</strong><br> {{ $formatDate($laporanInspeksi->tanggal_inspeksi) }}
                    </td>
                    <td style="border: none; padding: 10px;">
                        <strong>Diperiksa Oleh :</strong><br><br><br>
                        <strong>Tanggal :</strong><br> {{ $formatDate($laporanInspeksi->tanggal_inspeksi) }}
                    </td>
                </tr>
            </table>

            <div class="mt-3">
                <strong>Kesimpulan :</strong>
                &nbsp;&nbsp;[ <span class="checkbox {{ ($laporanInspeksi->kesimpulan == 'Release') ? 'checked' : '' }}"
                    style="border:none;"></span> ] Diterima/ Released
                &nbsp;&nbsp;[ <span class="checkbox {{ ($laporanInspeksi->kesimpulan == 'Reject') ? 'checked' : '' }}"
                    style="border:none;"></span> ] Ditolak/ Rejected
                &nbsp;&nbsp;[ <span class="checkbox {{ ($laporanInspeksi->kesimpulan == 'Hold') ? 'checked' : '' }}"
                    style="border:none;"></span> ] Lainnya/ Other :
            </div>
            <div class="mt-3">
                <strong>Catatan :</strong> {{ $laporanInspeksi->catatan ?? '-' }}
            </div>

            @if($isBahanBaku)
                <div class="page-break"></div>
            @endif
        @endif


        <!-- 4. FORM PENGUJIAN BAHAN BAKU -->

        @if($pengujian)
            <div class="header mb-2">
                @if($logo)
                    <img src="{{ $logo }}" alt="Logo" style="height: 80px;">
                @else
                    <h2>KOMITKAMI INTINUSA GEMILANG</h2>
                @endif
            </div>

            <table class="table-bordered w-100">
                <tr>
                    <td colspan="2" class="text-center">
                        <div class="title mt-3">FORM PENGUJIAN {{ strtoupper($inventory->type) }}</div>
                        <div class="text-left mt-3" style="font-size: 10px;">Nomor Dokumen : KIG-QC-F-06-04-02</div>
                    </td>
                </tr>
                <tr>
                    <td class="label-cell">Nama Bahan</td>
                    <td>: <span style="{{ $getResizableStyle($inventory->name) }}">{{ $inventory->name }}</span></td>
                </tr>
                <tr>
                    <td class="label-cell">Nomer Bets</td>
                    <td>: {{ $master->nomor_batch ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Tanggal</td>
                    <td>: {{ $formatDate($pengujian->tanggal) }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Nama Supplier</td>
                    <td>: {{ $master->suplierMaterial->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Negara Supplier</td>
                    <td>: {{ $master->suplierMaterial->address ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Nama Produsen</td>
                    <td>: {{ $mfgName }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Negara Produsen</td>
                    <td>: {{ $inspeksiPenerimaan->negara_asal ?? $mfgAddress ?? '-' }}</td>
                </tr>
            </table>

            <table class="table-bordered w-100" style="margin-top: -1px;">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Parameter Uji</th>
                        <th style="width: 35%;">Spesifikasi</th>
                        <th style="width: 30%;">Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($pengujian->details as $detail)
                        @php
                            $spesifikasi = trim($detail->spesifikasi);
                            $hasil = trim($detail->hasil ?? '-');
                            
                            // Skip row if spesifikasi is '-' OR hasil is '-'
                            if ($spesifikasi === '-' || $hasil === '-') {
                                continue;
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ trim(str_replace(': Spesifikasi', '', $detail->parameter_uji)) }}</td>
                            <td>{{ $detail->spesifikasi }}</td>
                            <td>{{ $detail->hasil }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="table-bordered w-100" style="margin-top: -1px; border-top: none;">
                <tr>
                    <td style="padding: 20px 10px; border-top: none;">
                        Kesimpulan : Memenuhi Syarat <span
                            class="checkbox {{ (in_array($pengujian->kesimpulan, ['Memenuhi syarat', 'Memenuhi Syarat', 'memenuhisyarat'])) ? 'checked' : '' }}"></span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Tidak Memenuhi Syarat <span
                            class="checkbox {{ (in_array($pengujian->kesimpulan, ['Tidak Memenuhi Syarat', 'tidakmemenuhisyarat'])) ? 'checked' : '' }}"></span>
                        <br><br><br><br>
                        Petugas QC : {{ $pengujian->petugas_qc ?? '-' }}
                    </td>
                </tr>
            </table>
        @endif


    @else
        <!-- 4. FORM PENGUJIAN BAHAN KEMAS -->

        @if($pengujian)
            <div class="header mb-2">
                @if($logo)
                    <img src="{{ $logo }}" alt="Logo" style="height: 80px;">
                @else
                    <h2>KOMITKAMI INTINUSA GEMILANG</h2>
                @endif
            </div>

            <table class="table-bordered w-100">
                <tr>
                    <td colspan="2" class="text-center">
                        <div class="title mt-3">FORM PENGUJIAN {{ strtoupper($inventory->type) }}</div>
                        <div class="text-left mt-3" style="font-size: 10px;">Nomor Dokumen : KIG-QC-F-06-04-02</div>
                    </td>
                </tr>
                <tr>
                    <td class="label-cell">Nama Bahan</td>
                    <td>: <span style="{{ $getResizableStyle($inventory->name) }}">{{ $inventory->name }}</span></td>
                </tr>
                <tr>
                    <td class="label-cell">Nomer Bets</td>
                    <td>: {{ $master->nomor_batch ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Tanggal</td>
                    <td>: {{ $formatDate($pengujian->tanggal) }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Nama Supplier</td>
                    <td>: {{ $master->suplierMaterial->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Negara Supplier</td>
                    <td>: {{ $master->suplierMaterial->address ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Nama Produsen</td>
                    <td>: {{ $mfgName }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Negara Produsen</td>
                    <td>: {{ $inspeksiPenerimaan->negara_asal ?? $mfgAddress ?? '-' }}</td>
                </tr>
            </table>

            <table class="table-bordered w-100" style="margin-top: -1px;">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Parameter Uji</th>
                        <th style="width: 35%;">Spesifikasi</th>
                        <th style="width: 30%;">Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengujian->details as $idx => $detail)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td>{{ trim(str_replace(': Spesifikasi', '', $detail->parameter_uji)) }}</td>
                            <td>{{ $detail->spesifikasi }}</td>
                            <td>{{ $detail->hasil }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="table-bordered w-100" style="margin-top: -1px; border-top: none;">
                <tr>
                    <td style="padding: 20px 10px; border-top: none;">
                        Kesimpulan : Memenuhi Syarat <span
                            class="checkbox {{ (in_array($pengujian->kesimpulan, ['Memenuhi syarat', 'Memenuhi Syarat', 'memenuhisyarat'])) ? 'checked' : '' }}"></span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Tidak Memenuhi Syarat <span
                            class="checkbox {{ (in_array($pengujian->kesimpulan, ['Tidak Memenuhi Syarat', 'tidakmemenuhisyarat'])) ? 'checked' : '' }}"></span>
                        <br><br><br><br>
                        Petugas QC : {{ $pengujian->petugas_qc ?? '-' }}
                    </td>
                </tr>
            </table>
        @endif

    @endif
</body>

</html>