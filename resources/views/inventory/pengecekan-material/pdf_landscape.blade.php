@php
    $master = $type == 'manual' ? $manual_report : $stock_in;

    // Fallbacks for manual report 
    $tanggal_ed = $type == 'manual' ? $master->expired_date : $master->tanggal_ed;
    $vendor = $type == 'manual' ? '-' : $master->vendor;
    $stok = $type == 'manual' ? 0 : $master->stok;
    $tanggal_kedatangan = $type == 'manual' ? $master->created_at : $master->tanggal_masuk;
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
        $logo = asset('images/KING-DATAR.png');

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
    @if($laporanInspeksi)
        @php
            $numSamples = 1;
            if ($laporanInspeksi && $laporanInspeksi->jumlah_sampel) {
                preg_match('/\d+/', $laporanInspeksi->jumlah_sampel, $matches);
                if (!empty($matches[0]) && intval($matches[0]) > 0) {
                    $numSamples = intval($matches[0]);
                }
            }
            if ($laporanInspeksi && count($laporanInspeksi->details) > 0) {
                foreach($laporanInspeksi->details as $d) {
                    if (is_array($d->hasil_json) && count($d->hasil_json) > $numSamples) {
                        $numSamples = count($d->hasil_json);
                    }
                }
            }
            
            $chunkSize = 20;
            $totalChunks = ceil($numSamples / $chunkSize);
            if ($totalChunks == 0) $totalChunks = 1;
        @endphp

        @for($chunkIndex = 0; $chunkIndex < $totalChunks; $chunkIndex++)
            @php
                $startCol = $chunkIndex * $chunkSize + 1;
                $endCol = min(($chunkIndex + 1) * $chunkSize, $numSamples);
                $currentCols = $endCol - $startCol + 1;
            @endphp

            @if($chunkIndex > 0)
                <div class="page-break"></div>
            @endif

            <div class="header mb-2">
                @if($logo)
                    <img src="{{ $logo }}" alt="Logo" style="height: 80px;">
                @else
                    <h2>KOMITKAMI INTINUSA GEMILANG</h2>
                @endif
            </div>

            <table class="table-bordered w-100" style="table-layout: fixed;">
                <tr>
                    <td colspan="6" class="text-center" style="padding-top: 10px; position: relative;">
                        <div class="title mb-2">LAPORAN INSPEKSI MATERIAL PACKAGING</div>
                        <div class="text-left" style="font-size: 9px; margin-top: -5px; margin-bottom: 5px;">Nomor Dokumen : KIG-QC-F-04-04-02</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 13%;">Kategori Material</td>
                    <td style="width: 21%;">: {{ $inventory->type }}</td>
                    <td style="width: 13%;">Nama Supplier</td>
                    <td style="width: 21%;">: {{ $master->suplierMaterial->name ?? '-' }}</td>
                    <td style="width: 13%;">Alamat Supplier</td>
                    <td style="width: 21%;">: {{ $master->suplierMaterial->address ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Nama Material</td>
                    <td>: <span style="{{ $getResizableStyle($inventory->name) }}">{{ $inventory->name }}</span></td>
                    <td>Nama Produsen</td>
                    <td>: {{ $mfgName }}</td>
                    <td>Negara Asal Material</td>
                    <td>: {{ $inspeksiPenerimaan->negara_asal ?? $mfgAddress ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Kode Material</td>
                    <td>: <span style="{{ $getResizableStyle($inventory->code) }}">{{ $inventory->code }}</span></td>
                    <td>Tanggal Kedatangan</td>
                    <td>: {{ $tanggal_kedatangan ? $tanggal_kedatangan->format('d-m-Y') : '-' }}</td>
                    <td>Tanggal Inspeksi</td>
                    <td>: {{ $formatDate($laporanInspeksi->tanggal_inspeksi) }}</td>
                </tr>
                <tr>
                    <td>Nomor Bets</td>
                    <td>: {{ $master->nomor_batch ?? '-' }}</td>
                    <td>Jumlah Material</td>
                    <td>: {{ $laporanInspeksi->jumlah_material ? $laporanInspeksi->jumlah_material . ' ' . ($inventory->type == 'Bahan Baku' ? 'Gram' : 'Pcs') : '-' }}</td>
                    <td>Jumlah Sampel</td>
                    <td>: {{ $laporanInspeksi->jumlah_sampel ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Disiapkan Oleh</td>
                    <td>: {{ $laporanInspeksi->disiapkan_oleh ?? '-' }}</td>
                    <td></td>
                    <td></td>
                    <td>Disetujui oleh</td>
                    <td>: {{ $laporanInspeksi->disetujui_oleh ?? '-' }}</td>
                </tr>
            </table>

            <table class="table-bordered w-100" style="margin-top: 15px; table-layout: fixed;">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 4%; vertical-align: middle;">No</th>
                        <th rowspan="2" style="width: 16%; vertical-align: middle;">Parameter</th>
                        <th rowspan="2" style="width: 16%; vertical-align: middle;">Persyaratan</th>
                        <th colspan="{{ $chunkSize }}" style="width: 52%; vertical-align: middle;">Hasil</th>
                        <th colspan="2" rowspan="2" style="width: 12%; vertical-align: middle;">Hasil Inspeksi</th>
                    </tr>
                    <tr>
                        @for($i = 0; $i < $chunkSize; $i++)
                            @php $colNum = $startCol + $i; @endphp
                            <th class="text-center" style="vertical-align: middle;">{{ $colNum <= $numSamples ? $colNum : '' }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporanInspeksi->details as $idx => $detail)
                        @php
                            $rowHasNumber = false;
                            if (is_array($detail->hasil_json)) {
                                foreach ($detail->hasil_json as $valCheck) {
                                    if (preg_match('/\d/', (string)$valCheck)) {
                                        $rowHasNumber = true;
                                        break;
                                    }
                                }
                            }
                            $rowHeight = $rowHasNumber ? '30px' : '20px';
                        @endphp
                        <tr>
                            <td class="text-center" style="vertical-align: middle;">{{ $idx + 1 }}</td>
                            <td style="vertical-align: middle;">{{ trim(str_replace(': Spesifikasi', '', $detail->parameter)) }}</td>
                            <td style="vertical-align: middle;">{{ $detail->persyaratan }}</td>
                            @for($i = 0; $i < $chunkSize; $i++)
                                @php
                                    $actualIndex = $startCol - 1 + $i;
                                    $val = ($actualIndex < $numSamples && is_array($detail->hasil_json)) ? ($detail->hasil_json[$actualIndex] ?? '') : '';
                                    $hasNumber = (bool)preg_match('/\d/', (string)$val);
                                    $cellFontSize = $hasNumber ? (strlen((string)$val) > 7 ? '7.5px' : '8px') : '10px';
                                @endphp
                                <td style="width: 1px !important; height: {{ $rowHeight }}; padding: 0 !important; text-align: center; vertical-align: middle; font-size: {{ $cellFontSize }};">
                                    @if($actualIndex < $numSamples && $val !== '' && $val !== null)
                                        <div style="transform: rotate(-90deg); white-space: nowrap; font-size: {{ $cellFontSize }}; line-height: 1;">
                                            {{ $val }}
                                        </div>
                                    @endif
                                </td>
                            @endfor
                            <td style="width: 6%; white-space: nowrap; font-size: 10px; vertical-align: middle;">
                                <span class="checkbox {{ ($detail->hasil_inspeksi == 'Pass') ? 'checked' : '' }}"></span>&nbsp;Pass
                            </td>
                            <td style="width: 6%; white-space: nowrap; font-size: 10px; vertical-align: middle;">
                                <span class="checkbox {{ ($detail->hasil_inspeksi == 'Fail') ? 'checked' : '' }}"></span>&nbsp;Fail
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="w-100 mt-4" style="border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; border: 0px solid #000; padding: 10px; vertical-align: top; height: 60px;">
                        <strong>Diinspeksi Oleh :</strong><br><br><br>
                        {{ $laporanInspeksi->diinspeksi_oleh ?? '-' }}<br><br>
                        <strong>Tanggal :</strong> {{ $formatDate($laporanInspeksi->tanggal_inspeksi) }}
                    </td>
                    <td style="width: 50%; border: 0px solid #000; padding: 10px; vertical-align: top; height: 60px;">
                        <strong>Diperiksa Oleh :</strong><br><br><br><br><br>
                        <strong>Tanggal :</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border: 0px solid #000; padding: 10px;">
                        <strong>Kesimpulan :</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        [ <span class="checkbox {{ ($laporanInspeksi->kesimpulan == 'Release') ? 'checked' : '' }}" style="border:none;"></span> ] Diterima/ Released
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        [ <span class="checkbox {{ ($laporanInspeksi->kesimpulan == 'Reject') ? 'checked' : '' }}" style="border:none;"></span> ] Ditolak/ Rejected
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        [ <span class="checkbox {{ ($laporanInspeksi->kesimpulan == 'Hold') ? 'checked' : '' }}" style="border:none;"></span> ] Lainnya/ Other :
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border: 0px dashed #000; padding: 10px; height: 40px; vertical-align: top;">
                        <strong>Catatan :</strong> {{ $laporanInspeksi->catatan ?? '-' }}
                    </td>
                </tr>
            </table>
        @endfor
    @endif

</body>

</html>