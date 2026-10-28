<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    * {
        font-family: sans-serif;
        margin: 0;
    }

    body {
        font-size: 10pt;
        padding: 5mm 10mm 0px 10mm;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td,
    th {
        padding: 5px 0px 10px 5px !important;
        /* jarak isi sel */
        margin: 0;
        /* margin default = 0 */
        border: 1px solid #000;
        vertical-align: top;
    }


    h3 {
        text-align: center;
        margin-bottom: 15px;
    }

    td.colon {
        border-right: none;
        border-left: none;
    }

    td.value {
        border-right: none;
        border-left: none;
    }
</style>

<body>
    @php
        $numSamplesInput = 0;
        if (!empty($reportData->jumlah_sampel_uji)) {
            preg_match('/\d+/', $reportData->jumlah_sampel_uji, $matches);
            if (!empty($matches[0]) && intval($matches[0]) > 0) {
                $numSamplesInput = intval($matches[0]);
            }
        }
        if ($numSamplesInput == 0) {
            $numSamplesInput = 25; // fallback
        }

        $chunkSize = 25;
        $totalChunks = ceil($numSamplesInput / $chunkSize);
        if ($totalChunks == 0) $totalChunks = 1;

        // Paksakan $numSamples untuk digenapkan ke kelipatan 25 (25 atau 50 dsb)
        $numSamples = $totalChunks * $chunkSize;
    @endphp

    @for($chunkIndex = 0; $chunkIndex < $totalChunks; $chunkIndex++)
        @php
            $startCol = $chunkIndex * $chunkSize + 1;
        @endphp
        <section {!! $chunkIndex > 0 ? 'style="page-break-before: always;"' : '' !!}>
        <img src="{{ public_path('images/KING-DATAR.png') }}" style="width: 350px;" alt="Logo KomitKami">
        <div style="border: 1px solid #000000;">
            <h3 style="margin-top: 15px; margin-bottom: 20px !important;">REPORT IN PROCESS CONTROL</h3>
            <p style="margin-left: 3px; margin-bottom: 5px !important;">Nomor Dokumen : KIG-QC-F-05-02-01</p>
            @php
                $name = $reportData->produksi->product->name;
                $tipe = $produksi->product->tipe;

                // ambil ukuran (opsional, kalau masih dipakai di kemasan)
                preg_match('/\d+\s*[a-zA-Z]+/', $name, $matchUkuran);
                $ukuran = $matchUkuran[0] ?? '';

                // hapus isi dalam tanda kurung
                $cleanName = preg_replace('/\s*\([^)]*\)/', '', $name);
                $cleanName = trim($cleanName);

                // format kemasan
                $kemasan = ucfirst($tipe) . ($ukuran ? ' ' . strtoupper($ukuran) : '');
            @endphp
            <table>
                <tr>
                    <td>Nama Produk</td>
                    <td>{{$cleanName}}</td>
                    <td>Besar Bets</td>
                    <td>{{ $reportData->produksi->batch_size ?? '-' }} KG</td>
                </tr>
                <tr>
                    <td>Jenis Produk</td>
                    <td>{{ $reportData->produksi->product->jenis ?? '-' }}</td>
                    <td>Tanggal Produksi</td>
                    <td>{{ $reportData->produksi->tanggal_produksi->format('d-m-Y') ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td>Kode Produk</td>
                    <td>{{ $reportData->produksi->product->code ?? '-' }}</td>
                    <td>Tanggal Pengujian</td>
                    <td>{{  \Carbon\Carbon::parse($reportData->tanggal_pengujian)->format('d-m-Y') ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Nomor Bets</td>
                    <td>{{ $reportData->produksi->batch_number ?? '-' }}</td>
                    <td>Jumlah Sampel Uji</td>
                    <td>{{ $reportData->jumlah_sampel_uji ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Disiapkan Oleh</td>
                    <td style="height: 60px !important; vertical-align: bottom; text-align: center;">
                        Quality Control
                    </td>
                    <td>Disetujui Oleh</td>
                    <!-- text center bottom -->
                    <td style="height: 60px !important; vertical-align: bottom; text-align: center;">
                        Quality Assurance
                    </td>
                </tr>
            </table>
        </div>

        <div style="margin-top:12px; margin-bottom:0px;">
            <p><strong>In Proses Filling</strong></p>
        </div>

        <table>
            <thead>
                <tr>
                    <td rowspan="2" width="15px"
                        style="white-space:nowrap; padding:0px; text-align:center; vertical-align:middle">
                        <strong>No</strong>
                    </td>
                    <td rowspan="2" width="85px;" style="text-align:center; vertical-align:middle">
                        <strong>Jenis Pengujian</strong>
                    </td>
                    <td rowspan="2" width="85px" style="text-align:center; vertical-align:middle">
                        <strong>Spesifikasi</strong>
                    </td>
                    <td colspan="25" style="text-align:center; vertical-align:middle">
                        <strong>Hasil</strong>
                    </td>
                </tr>
                <tr>
                    @for($i = 0; $i < $chunkSize; $i++)
                        @php $colNum = $startCol + $i; @endphp
                        <td
                            style="width: 8px !important; padding: 0px !important; text-align: center; vertical-align: middle; font-size: 9px;">
                            {{ $colNum <= $numSamples ? $colNum : '' }}</td>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($report as $items)
                    @php
                        $hasil = is_array($items->hasil) ? $items->hasil : [];
                        $getDynamicFontSize = function ($text) {
                            $len = strlen($text);
                            if ($len > 10)
                                return '5px';
                            if ($len > 7)
                                return '6px';
                            if ($len >= 5)
                                return '7px';
                            return '9px';
                        };
                    @endphp
                    <tr>
                        <td style="padding: 5px 0px 0px 5px !important; text-align: center; vertical-align: middle;">
                            {{$loop->iteration}}</td>
                        <td style="padding: 5px 0px 0px 5px !important;">{{$items->acuanLaporanIpc->pemeriksaan ?? '-'}}
                        </td>
                        <td style="padding: 5px 0px 0px 5px !important;">
                            {{$items->acuanLaporanIpc->spesifikasi ?? '-'}}
                        </td>
                        @for($i = 0; $i < $chunkSize; $i++)
                            @php $actualIndex = $startCol + $i; @endphp
                            <td
                                style="width: 1px !important; height: 20px; padding: 0 !important; text-align: center; vertical-align: middle;">
                                @if($actualIndex <= $numSamples)
                                    <div
                                        style="transform: rotate(-90deg); white-space: nowrap; font-size: {{ $getDynamicFontSize($hasil[$actualIndex] ?? '-') }}; ">
                                        {{ $hasil[$actualIndex] ?? '-' }}
                                    </div>
                                @endif
                            </td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table
            style="margin-left: 15px !important; width:80%; margin-top:15px; border-collapse:collapse; border: none !important;">
            <tr style="border: none !important;">
                <td style="border: none !important; width:50%; vertical-align:top;">
                    <p><strong>Disampling Oleh :</strong> {{$reportData->disampling_oleh}}</p>
                    <p style="margin-top:15px; margin-bottom:0;"><strong>Tanggal /
                            {{ \Carbon\Carbon::parse($reportData->tanggal_pengujian)->format('d-m-Y')}}</strong></p>
                </td>
                <td style="border: none !important; width:50%; vertical-align:top;">
                    <p><strong>Diperiksa Oleh :</strong></p>
                    <p style="margin-top:15px; margin-bottom:0;"><strong>Tanggal /
                            {{ \Carbon\Carbon::parse($reportData->tanggal_pengujian)->format('d-m-Y')}}</strong></p>
                </td>
            </tr>
        </table>
        <div style="margin-top:15px; margin-left:40px;">
            <p>
                <strong>Kesimpulan :</strong>

                <label style="margin-right:20px;">
                    <span style="display:inline-block; width:14px; height:14px; border:1px solid #000; 
                                margin-right:5px; vertical-align:middle; text-align:center; font-size:12px;">
                        {{ $reportData->kesimpulan == 'OK' ? 'V' : '' }}
                    </span>
                    Diterima/ Released
                </label>

                <label style="margin-right:20px;">
                    <span style="display:inline-block; width:14px; height:14px; border:1px solid #000; 
                                margin-right:5px; vertical-align:middle; text-align:center;">
                        {{ $reportData->kesimpulan == 'NOT OK' ? 'V' : '' }}
                    </span>
                    Ditolak/ Rejected
                </label>

                <label>
                    <span style="display:inline-block; width:14px; height:14px; border:1px solid #000; 
                                margin-right:5px; vertical-align:middle; text-align:center;">
                        {{ $reportData->kesimpulan == 'LAINNYA' ? 'V' : '' }}
                    </span>
                    Lainnya/ Other :
                </label>
            </p>
            <p style="margin-top:30px;"><strong>Catatan :</strong> {{$reportData->catatan}}</p>
        </div>
    </section>
    @endfor
</body>

</html>