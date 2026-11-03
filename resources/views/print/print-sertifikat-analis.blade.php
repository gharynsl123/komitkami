<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Sertifikat Analisis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #000;
            margin: 40px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #000;
            text-align: left;
            padding: 5px;
        }

        thead>tr>th {
            font-weight: bold;
            padding: 1rem;
            text-align: center;
        }

        tfoot>tr>td {
            padding: 1.5rem 0.5rem;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td colspan="3">
                <div class="header">
                    <img src="{{ public_asset('images/KING-DATAR.png') }}" style="width: 200px;" alt="Logo KomitKami">
                    <div class="title">Sertifikat Analisis</div>
                </div>

                <p style="margin:0; padding:0; font-size:0.9rem;">No Dokumen : KIG-QC-F-07-01-01</p>
                @php
                    $name = $produksi->product->name;
                    $tipe = $produksi->product->tipe;

                    // ambil ukuran (contoh: 500 ml)
                    preg_match('/\d+\s*[a-zA-Z]+/', $name, $matchUkuran);
                    $ukuran = $matchUkuran[0] ?? '';

                    // hapus isi dalam ()
                    $cleanName = preg_replace('/\s*\([^)]*\)/', '', $name);

                    // hapus ukuran dari nama produk
                    $cleanName = preg_replace('/\s*\d+\s*[a-zA-Z]+/', '', $cleanName);

                    $cleanName = trim($cleanName);

                    // format kemasan
                    $kemasan = ucfirst($tipe) . ($ukuran ? ' ' . strtoupper($ukuran) : '');
                @endphp
        <tr>
            <td style="font-size:0.9rem; width: 35%; padding: 4px 4px; border: none;">Nama Produk</td>
            <td colspan="2" style="font-size:0.9rem; padding: 4px 4px; border: none;">: {{ $cleanName }}</td>
        </tr>
        <tr>
            <td style="font-size:0.9rem; padding: 4px 4px; border: none;">Kemasan</td>
            <td colspan="2" style="font-size:0.9rem; padding: 4px 4px; border: none;">: {{ $kemasan }}</td>
        </tr>
        <tr>
            <td style="font-size:0.9rem; padding: 4px 4px; border: none;">No Batch</td>
            <td colspan="2" style="font-size:0.9rem; padding: 4px 4px; border: none;">: {{ $batchnumber }}</td>
        </tr>
        <tr>
            <td style="font-size:0.9rem; padding: 4px 4px; border: none;">Expired Date</td>
            <td colspan="2" style="font-size:0.9rem; padding: 4px 4px; border: none;">:
                {{ \Carbon\Carbon::parse($produksi->tanggal_expired)->format('m / Y') }}</td>
        </tr>
        </td>
        </tr>
        <thead>
            <tr>
                <th>PENGUJIAN</th>
                <th>SPESIFIKASI</th>
                <th>HASIL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataQC as $item)
                @php
                    $namaParameter = strtolower($item->qualityControl->parameter_pengujian);

                    $spesifikasi = $item->qualityControl->nilai_standart;
                    if ($spesifikasi === 'otomatis') {
                        if ($item->qualityControl->parameter_pengujian === 'Volume') {
                            $spesifikasi = $produksi->product->ukuran;
                        }
                    }
                @endphp

                @if(Str::contains($namaParameter, 'uji') && !Str::contains($namaParameter, 'uji ddh'))
                    @continue
                @endif
                <tr>
                    <td>{{ $item->qualityControl->parameter_pengujian }}</td>
                    <td>{{ $spesifikasi }}</td>
                    <td>{{ $item->nilai_real }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Keputusan : DITERIMA / DITOLAK *) Lingkari yang sesuai</td>
            </tr>
            <tr class="borderless">
                <td style="border:0; display:flex;">
                    <p>Pelulusan dengan Catatan :</p>
                    <div>
                        <p>Pengujian Dilakukan Oleh,</p>
                        <br><br><br>
                        <p style="padding: 0 20px;">Quality Control</p>
                    </div>
                </td>
                <td colspan="2" style="border:0; padding:0 0.5rem;">
                    <p>&nbsp;</p>
                    <div style="text-align: center; padding: 0px 3rem ;">
                        <p>Mengetahui,</p>
                        <br><br><br><br>
                        <p>Quality Assurance</p>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>