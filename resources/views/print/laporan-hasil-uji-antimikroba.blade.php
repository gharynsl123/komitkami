<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Uji Antimikroba</title>
    <style>
        @page {
            /* Margin atas/bawah 15px, kiri/kanan menggunakan margin default */
            margin-top: 15px;
            margin-bottom: 15px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* Kotak bingkai luar sesuai dokumen fisik */
        .outer-box {
            border: 1px solid #000;
            padding: 6px 8px 8px 8px;
            box-sizing: border-box;
        }

        /* Header dokumen */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 4px;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .logo-img {
            width: 250px;
            height: auto;
        }

        .doc-title {
            text-align: center;
            font-size: 11.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .meta-line {
            font-size: 8pt;
            margin: 2px 0;
            line-height: 1.35;
        }

        /* Tabel Utama Pengujian */
        table.laporan-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-top: 4px;
            font-size: 7.5pt;
        }

        table.laporan-table th,
        table.laporan-table td {
            border: 1px solid #000;
            padding: 2.5px 3px;
            vertical-align: middle;
            box-sizing: border-box;
        }

        table.laporan-table thead th {
            text-align: center;
            font-weight: bold;
            font-size: 7.2pt;
            background-color: #ffffff;
            line-height: 1.15;
        }

        /* Alignment Data */
        .text-center { text-align: center !important; }
        .text-left   { text-align: left   !important; }
        .text-right  { text-align: right  !important; }

        .row-data td  { height: 19px; }
        .row-empty td { height: 17px; }

        .produk-title {
            font-weight: bold;
            font-size: 7.8pt;
            display: block;
        }

        .mikroba-subtitle {
            font-size: 6.8pt;
            color: #333;
            font-style: italic;
            display: block;
            margin-top: 1px;
        }

        /* Header vertikal (rotasi miring) */
        th.th-vert {
            padding: 0 !important;
            vertical-align: middle;
            text-align: center;
            height: 60px;
            width: 24px;
            line-height: 0;
        }

        th.th-vert > div {
            transform: rotate(-90deg);
            white-space: nowrap;
            display: inline-block;
            padding: 0;
            margin: 0;
            line-height: 1;
            font-size: 7.2pt;
            font-weight: bold;
        }

        /* Format tanggal diagonal seperti kartu stok */
        .date-custom {
            text-align: center;
        }

        .date-custom .left {
            position: relative;
            width: 24px;
            height: 30px;
            margin: 0 auto;
        }

        .date-custom .day,
        .date-custom .month,
        .date-custom .year {
            font-size: 6.5px;
            line-height: 1;
        }

        .date-custom .day {
            position: absolute;
            top: 1px;
            left: 1px;
        }

        .date-custom .slash {
            position: absolute;
            top: 3px;
            left: 9px;
            width: 1px;
            height: 20px;
            background: black;
            transform: rotate(40deg);
        }

        .date-custom .year {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 14px;
            white-space: nowrap;
        }

        .date-custom .month {
            position: absolute;
            bottom: 1px;
            left: 6px;
        }

        /* Page break antar mikroba */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@php
    $logoSrc = function_exists('public_asset') ? public_asset('images/KING-DATAR.png') : '';
@endphp

{{-- ══════════════════════════════════════════════════════════════════
     Loop 3 halaman: 1 per jenis mikroba
     ══════════════════════════════════════════════════════════════════ --}}
@foreach($groupedHasil as $loopIndex => $group)
    @php
        $hasilList  = $group['items'];
        $mikrobaUji = $group['label'];

        // Formatter helpers
        $fmtCfu = function($val) {
            if ($val === null || $val === '') return '-';
            return fmod((float)$val, 1) == 0
                ? number_format((float)$val, 0, ',', '.')
                : number_format((float)$val, 2, ',', '.');
        };
        $fmtLog = function($val) {
            if ($val === null || $val === '') return '-';
            return number_format((float)$val, 2, ',', '.');
        };
        $fmtPct = function($val) {
            if ($val === null || $val === '') return '-';
            $num = (float) str_replace(',', '.', (string) $val);
            if (fmod($num, 1) == 0.0) {
                return number_format($num, 0, ',', '.') . '%';
            }
            return rtrim(rtrim(number_format($num, 2, ',', '.'), '0'), ',') . '%';
        };

        $filledCount    = count($hasilList);
        $targetTotalRows = 16;
        $emptyCount     = max(0, $targetTotalRows - $filledCount);
    @endphp

    @if(!empty($logoSrc))
        <img src="{{ $logoSrc }}" class="logo-img" alt="KOMITKAMI INTINUSA GEMILANG">
    @else
        <div style="font-weight: bold; font-size: 9pt; color: #1a73e8;">
            KOMITKAMI<br>
            <span style="font-size: 7pt; color: #555;">INTINUSA GEMILANG</span>
        </div>
    @endif
    {{-- 1. KOP / HEADER --}}
    <div class="outer-box">
        <table class="header-table">
            <tr>
                <td style="width: 75%;">
                    <div class="doc-title">LAPORAN HASIL UJI ANTIMICROBA</div>
                </td>
            </tr>
        </table>

        {{-- Nomor Dokumen --}}
        <div class="meta-line" style="margin-top: 3px;">
            <strong>Nomor Dokumen:</strong> {{ $noDokumen ?? 'KIG-QC-F-09-02-01' }}
        </div>

        {{-- Mikroba Uji — label per halaman --}}
        <div class="meta-line" style="margin-bottom: 3px;">
            <strong>Mikroba uji:</strong> <em>{{ $mikrobaUji }}</em>
        </div>

        {{-- 2. TABEL LAPORAN --}}
        <table class="laporan-table">
            <thead>
                {{-- Header Baris 1 --}}
                <tr>
                    <th class="th-vert" rowspan="3" style="width: 2.8%;"><div>Tanggal</div></th>
                    <th rowspan="3" style="width: 20.2%;">Sampel Uji</th>
                    <th rowspan="3" style="width: 9%;">No. Batch</th>
                    <th rowspan="3" style="width: 6%;">Waktu<br>Kontak</th>
                    <th colspan="2" rowspan="2" style="width: 14%;">Jumlah awal mikroba<br>pada suspensi</th>
                    <th colspan="4" style="width: 28%;">Jumlah mikroba yang hidup</th>
                    <th colspan="2" style="width: 20%;">Persentase jumlah<br>mikroba yang terbunuh</th>
                </tr>
                {{-- Header Baris 2 --}}
                <tr>
                    <th colspan="2" style="font-style: italic;">Clean condition</th>
                    <th colspan="2" style="font-style: italic;">Dirty condition</th>
                    <th style="font-style: italic; font-size: 6.8pt;">Clean condition</th>
                    <th style="font-style: italic; font-size: 6.8pt;">Dirty condition</th>
                </tr>
                {{-- Header Baris 3 --}}
                <tr>
                    <th style="width: 7.5%;">CFU/mL</th>
                    <th style="width: 6.5%;">Log</th>
                    <th style="width: 7.5%;">CFU/mL</th>
                    <th style="width: 6.5%;">Log</th>
                    <th style="width: 7.5%;">CFU/mL</th>
                    <th style="width: 6.5%;">Log</th>
                    <th style="width: 10%;">%</th>
                    <th style="width: 10%;">%</th>
                </tr>
            </thead>
            <tbody>
                {{-- Baris Data Terisi --}}
                @foreach($hasilList as $item)
                    @php
                        // Format Tanggal
                        $tgl = null;
                        $tglDay = '-'; $tglMonth = ''; $tglYear = '';
                        if (!empty($item->tanggal)) {
                            try {
                                $tglCarbon = \Carbon\Carbon::parse($item->tanggal);
                                $tglDay    = $tglCarbon->format('d');
                                $tglMonth  = $tglCarbon->format('m');
                                $tglYear   = $tglCarbon->format('Y');
                                $tgl = true;
                            } catch (\Exception $e) {
                                $tglDay = (string) $item->tanggal;
                            }
                        }

                        $displaySampel = $namaProduk ?? ($item->sampel_uji ?? '-');
                        $displayBatch  = $noBatch    ?? ($item->no_batch   ?? '-');
                        $displayKontak = $item->waktu_kontak ?? '30 Detik';
                    @endphp
                    <tr class="row-data">
                        <td class="text-center">
                            @if($tgl)
                                <div class="date-custom">
                                    <div class="left">
                                        <span class="day">{{ $tglDay }}</span>
                                        <span class="slash"></span>
                                        <span class="month">{{ $tglMonth }}</span>
                                        <div class="year">{{ $tglYear }}</div>
                                    </div>
                                </div>
                            @else
                                {{ $tglDay }}
                            @endif
                        </td>
                        <td class="text-left">
                            <span class="produk-title">{{ $displaySampel }}</span>
                        </td>
                        <td class="text-center">{{ $displayBatch }}</td>
                        <td class="text-center">{{ $displayKontak }}</td>
                        <td class="text-right">{{ $fmtCfu($item->awal_cfu_ml) }}</td>
                        <td class="text-center">{{ $fmtLog($item->awal_log) }}</td>
                        <td class="text-right">{{ $fmtCfu($item->clean_cfu_ml) }}</td>
                        <td class="text-center">{{ $fmtLog($item->clean_log) }}</td>
                        <td class="text-right">{{ $fmtCfu($item->dirty_cfu_ml) }}</td>
                        <td class="text-center">{{ $fmtLog($item->dirty_log) }}</td>
                        <td class="text-center">{{ $fmtPct($item->persen_clean) }}</td>
                        <td class="text-center">{{ $fmtPct($item->persen_dirty) }}</td>
                    </tr>
                @endforeach

                {{-- Baris Kosong agar menyerupai format lembar fisik (minimal 16 baris total) --}}
                @for($k = 0; $k < $emptyCount; $k++)
                    <tr class="row-empty">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    {{-- Page break setelah setiap halaman kecuali yang terakhir --}}
    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>
