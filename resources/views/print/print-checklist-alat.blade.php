<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 120px 40px 40px 40px;
            /* Atas ditinggikan untuk logo & kop */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
        }

        .page-break:last-child {
            page-break-after: never;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }

        /* Logo fixed di area margin-top @page */
        .logo {
            position: fixed;
            top: -100px;
            left: 0px;
            width: 250px;
        }

        .header-section {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            text-align: center;
        }

        tbody td {
            height: 20px;
        }

        .wrap-text {
            white-space: normal;
            word-break: break-word;
            overflow-wrap: break-word;
        }
    </style>
</head>

<body>
    <img src="{{ public_path('images/KING-DATAR.png') }}" class="logo" alt="logo">

    @foreach($chunks as $chunkIndex => $chunk)
        <div class="{{ !$loop->last ? 'page-break' : '' }}">
            <table style="width:100%; margin-bottom: 0px !important; border: 1px solid #000 !important;">
                <tr style="text-align:center; border:none;">
                    <td colspan="3" style="margin: 0; border:none;">
                        <h3 style="margin:0px !important;">CLEARNING RECORD MESIN DAN ALAT PRODUKSI</h3>
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; border:none; padding:2px 4px;"><strong>Nomor Dokumen</strong></td>
                    <td style="width:10px; border:none; padding:2px 4px;">:</td>
                    <td style="border:none; padding:2px 4px;">KIG-PR-F-01-06-02</td>
                </tr>
                <tr>
                    <td style="width:120px; border:none; padding:2px 4px;"><strong>Mesin / Alat</strong></td>
                    <td style="width:10px; border:none; padding:2px 4px;">:</td>
                    <td style="border:none; padding:2px 4px;">{{ $alat ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="border:none; padding:2px 4px;"><strong>Bulan / Tahun</strong></td>
                    <td style="border:none; padding:2px 4px;">:</td>
                    <td style="border:none; padding:2px 4px;">
                        {{ $bulan ? \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') : '-' }}</td>
                </tr>
            </table>

            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Tanggal</th>
                        <th style="width: 150px;">Pemakaian Sebelumnya</th>
                        <th style="width: 100px;">No Batch</th>
                        <th style="width: 90px;">Pelaksana</th>
                        <th style="width: 90px;">Pemeriksa</th>
                        <th style="width: 100px;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $getDynamicFontSize = function ($text) {
                            $len = strlen($text ?? '');
                            if ($len <= 32)
                                return '9pt';
                            if ($len <= 36)
                                return '8.5pt';
                            if ($len <= 40)
                                return '8pt';
                            return '7.5pt';
                        };
                    @endphp
                    @foreach($chunk as $riwayat)
                        @php
                            $name = $riwayat->produksi->product->name ?? '-';
                            $clean = preg_replace('/\s*\([^)]*\)/', '', $name);
                            $clean = preg_replace('/\s*\d.*$/', '', $clean);
                        @endphp
                        <tr>
                            <td style="text-align:center;">{{ $riwayat->created_at->format('d-m-Y') }}</td>
                            <td class="wrap-text" style="font-size: {{ $getDynamicFontSize($clean)}};">{{ $clean }}</td>
                            <td style="text-align:center;">{{ $riwayat->produksi->batch_number ?? '-' }}</td>
                            <td style="text-align:center;">{{ $riwayat->dibersihkan_oleh }}</td>
                            <td style="text-align:center;">
                                {{ $riwayat->diperiksa_oleh === "Belum Diperiksa. Segera lapor ke QC" ? '-' : $riwayat->diperiksa_oleh }}
                            </td>
                            <td></td>
                        </tr>
                    @endforeach

                    {{-- Baris Kosong untuk konsistensi --}}
                    @for($i = count($chunk); $i < $rowsPerPage; $i++)
                        <tr>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    @endforeach
</body>

</html>