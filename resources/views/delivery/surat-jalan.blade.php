<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Surat jalan dan terima barang</title>
</head>
<style>
    * {
        padding: 0;
        margin: 0;
    }

    table,
    td,
    th {
        width: 100%;
        border-collapse: collapse;
        border: none !important;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 1rem 2rem !important;
    }

    .bordered-table,
    .bordered-table th,
    .bordered-table td {
        border: 1px solid #000000 !important;
        border-collapse: collapse;
        padding: 5px;
        margin: 0px;
        width: 80%;
    }
</style>

<body>
    <table>
        <tr>
            <td>
                <img src="{{ public_asset('images/kop-surat-jalan-kiri.png') }}"
                    style="position: absolute; top: 0; left: 0; z-index: -1; width: 35px;">
                <img src="{{ public_asset('images/KING-DATAR.png') }}" alt="logo komitkami" width="300px">
            </td>
            <td style="text-align: right;">
                <img src="{{ public_asset('images/logo kop surat MMI transparan.png') }}" alt="imgae cop right" width="90px">
            </td>
        </tr>
    </table>
    <p style="text-align: center; font-size: 16px; font-weight: bold;">SURAT JALAN</p>

    <table>
        <tr>
            <td style="height: 3rem;">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 1rem;">Nomor</td>
            <td style="width: 10px;">:</td>
            <td style="width:15rem;">{{$delivery->no_sj ?? 'Belum Di tentukan'}}</td>
            <td>Pengirim :</td>
        </tr>
        <tr>
            <td style="width: 1rem;">tanggal</td>
            <td style="width: 10px;">:</td>
            <td style="width:15rem;">
                {{$delivery->tanggal_terbit ? \Carbon\Carbon::parse($delivery->tanggal_terbit)->format('d M Y') : 'Belum Di tentukan'}}
            </td>
            <td><strong>PT Komitkami Intinusa Gemilang</strong></td>
        </tr>
        <tr>
            <td style="width: 1rem;">No. PO</td>
            <td style="width: 10px;">:</td>
            <td style="width:15rem;">{{$delivery->invoice->nomor_invoice ?? 'Belum Di tentukan'}}</td>
            <td>Komp. Bizzpark 3 Blok E/20 <br>Jl. Sultan Agung, Kelurahan Kali Baru, Kecamatan Medan Satria, Kota
                Bekasi</td>
        </tr>
        <tr>
            <td style="width: 1rem;"> <br> Catatan </td>
            <td style="width: 10px;"><br> :</td>
            <td style="width:15rem;"><br>{{$delivery->note ?? '-'}}</td>
            <td></td>
        </tr>
        <tr>
            <td style="width: 1rem;"></td>
            <td style="width: 10px;"></td>
            <td style="width:15rem;"></td>
            <td>
                <br> Penerima : <br>
                <span style="font-weight: bold;">{{ $delivery->invoice->orders->first()->user->name }}</span>
            </td>
        </tr>

        <tr>
            <td style="height: 4rem;">&nbsp;</td>
        </tr>
    </table>

    <p style="margin-bottom: 1.5rem;"><Strong>Jenis Kendaraan :</Strong></p>
    <p style="margin-bottom: 1.5rem;"><Strong>Nomor Kendaraan :</Strong></p>
    <table class="bordered-table">
        <thead>
            <tr>
                <th style="width: 80px;">Jumlah</th>
                <th style="width: 80px;">Kemasan</th>
                <th>Nama Produk</th>
            </tr>
        </thead>
        <tbody>
            @foreach($delivery->items as $item)
                <!-- trim text inside brackets but keep the suffix -->
                @php
                    $rawName = $item->order->product->name; 
                    
                    // Hapus kurung beserta isinya
                    $cleanName = preg_replace('/\s*\(.*?\)\s*/', ' ', $rawName);
                    
                    // Rapikan spasi ganda jika ada
                    $productName = trim(preg_replace('/\s+/', ' ', $cleanName));
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $item->jumlah_barang }}</td>
                    <td>{{ ucwords(strtolower($item->order->product->tipe)) }}</td>
                    <td>{{ $productName }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top: 6rem;">
        <tr>
            <td style="width: 50%; text-align: center;">
                Pengirim,<br>Tanda Tangan/Cap<br><br><br><br><br>
                (_______________________)
            </td>
            <td style="width: 50%; text-align: center;">
                Penerima,<br>Tanda Tangan/Cap<br><br><br><br><br>
                (_______________________)
            </td>
        </tr>
    </table>

    <div style="position: fixed; bottom: 0; right: 0; width: 30%;">
        <img src="{{ public_asset('images/kop-surat-jalan.png') }}" alt="kop surat jalan" width="100%">
    </div>
</body>

</html>