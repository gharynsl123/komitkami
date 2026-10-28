<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Spesifikasi Produk Jadi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header-box {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            border-bottom: 0px !important;
        }
        .header-box td.box-section {
            padding: 10px;
            border-bottom: 1px solid #000;
        }
        .header-box td.box-section-last {
            padding: 10px;
            border-bottom: none;
        }
        .header-top {
            width: 100%;
            border-collapse: collapse;
        }
        .header-top td {
            padding: 0;
            border: none;
        }
        .header-bottom {
            width: 100%;
            border-collapse: collapse;
        }
        .header-bottom td {
            padding: 3px 0;
            border: none;
        }
        .header-bottom td.label {
            width: 25%;
            font-weight: bold;
        }
        .spec-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .spec-table th, .spec-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .spec-table th {
            text-align: center;
            font-weight: bold;
        }
        .spec-table th:nth-child(1) {
            width: 5%;
        }
        .spec-table th:nth-child(2) {
            width: 45%;
        }
        .spec-table th:nth-child(3) {
            width: 50%;
        }
        .footer {
            width: 100%;
            margin-top: 50px;
        }
        .signature-box {
            float: right;
            width: 250px;
        }
        .signature-title {
            font-weight: bold;
        }
        .signature-name {
            font-weight: bold;
        }
        .symbol {
            font-family: 'DejaVu Sans', sans-serif !important;
        }
    </style>
</head>
<body>

    <table class="header-box">
        <tr>
            <td class="box-section">
                <table class="header-top">
                    <tr>
                        <td style="width: 30%; vertical-align: top;">
                            <img src="{{ asset('images/KING-DATAR.png') }}" alt="Logo" style="width: 230px; height: auto;">
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%; text-align: center; vertical-align: middle; font-size: 18px; font-weight: bold; text-transform: uppercase;">
                            SPESIFIKASI PRODUK JADI
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-size: 10px; padding-top: 15px;">
                            Nomor Dokumen : KIG-QC-F-07-02-00
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="box-section-last">
                <table class="header-bottom">
                    <tr>
                        <td class="label">Nama Produk</td>
                        <td>: {{ $product->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Kemasan Produk</td>
                        <td style="text-transform:capitalize">: {{ $product->tipe }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="spec-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Parameter Uji</th>
                <th>Spesifikasi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp
            @forelse($qualityControl as $qc)
            @php
                if (strtolower($qc->parameter_pengujian) == 'volume' && strtolower($qc->nilai_standart) == 'otomatis') {
                    $qc->nilai_standart = $product->ukuran;
                }

                if (str_contains($qc->nilai_standart, '≥')) {
                    $qc->nilai_standart = str_replace('≥', '<span class="symbol">&#8805;</span>', $qc->nilai_standart);
                }
            @endphp
            <tr>
                <td style="text-align: center;">{{ $no ++ }}</td>
                <td>{{ $qc->parameter_pengujian }}</td>
                <td>{!! $qc->nilai_standart !!}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center;">Belum ada data spesifikasi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <div class="signature-title">PT. Komitkami Intinusa Gemilang</div>
            <img src="{{asset('images/stamping-spesifikasi.png')}}" alt="" style="width: 230px; height: auto;">
            <div class="signature-name">Dedy Maryadi, S.Kom</div>
            <div>Direktur</div>
        </div>
    </div>

</body>
</html>
