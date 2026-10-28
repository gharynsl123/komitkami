<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Surat terima barang</title>
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
        width: 100%;
    }
</style>

<body>
    <table>
        <tr>
            <td>
                <img src="{{ asset('images/kop-surat-jalan-kiri.png') }}"
                    style="position: absolute; top: 0; left: 0; z-index: -1; width: 35px;">
                <img src="{{asset('images/KING-DATAR.png')}}" alt="logo komitkami" width="300px">
            </td>
            <td style="text-align: right;">
                <img src="{{asset('images/logo kop surat MMI transparan.png')}}" alt="imgae cop right" width="90px">
            </td>
        </tr>
    </table>
    <p style="text-align: center; font-size: 16px; font-weight: bold;">TANDA TERIMA BARANG</p>

    <table style="width: 100%; margin-top: 1rem;">
        <tr>
            <td
                style="width: 50%; text-align: left; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word;">
                Kepada Yth:<br>
                <strong>{{$delivery->invoice->orders->first()->user->name ?? '_______________________'}}</strong><br>
                {{$delivery->invoice->orders->first()->user->address ?? 'Belum Ditentukan'}}<br>
            </td>
            <td style="width: 50%; text-align: left; vertical-align: top;">
                <div style="margin-left: 5rem;">
                    <!-- Tabel Bersarang agar titik dua sejajar -->
                    <table style="border-collapse: collapse;">
                        <tr>
                            <td style="width: 60px;">Tanggal</td>
                            <td style="width: 10px;">:</td>
                            <td>{{$delivery->tanggal_terbit ? \Carbon\Carbon::parse($delivery->tanggal_terbit)->format('d M Y') : '_______________________'}}
                            </td>
                        </tr>
                        <tr>
                            <td>No. SJ</td>
                            <td>:</td>
                            <td>{{$delivery->no_sj ?? '_______________________'}}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>


    <table class="bordered-table" style="margin-top: 1rem;">
        <thead>
            <tr>
                <th style="width: 0.5rem;">No</th>
                <th style="width: 3rem;">Kode Produk</th>
                <th style="width: 4rem;">Nama Produk</th>
                <th style="width: 1rem;">Jumlah</th>
                <th style="width: 3rem;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($delivery->items as $index => $item)
                @php
                    $rawName = $item->order->product->name; 
                    
                    // Hapus kurung beserta isinya
                    $cleanName = preg_replace('/\s*\(.*?\)\s*/', ' ', $rawName);
                    
                    // Rapikan spasi ganda jika ada
                    $productName = trim(preg_replace('/\s+/', ' ', $cleanName));
                @endphp
                <tr>
                    <td style="text-align: center;">{{$index + 1}}</td>
                    <td>{{$item->order->product->code ?? 'Belum Di tentukan'}}</td>
                    <td>{{ $productName ?? 'Belum Di tentukan' }}</td>
                    <td style="text-align: center;">{{$item->jumlah_barang ?? 'Belum Di tentukan'}}
                        {{$item->order->product->tipe}}</td>
                    <td style="text-align: left;">{!! nl2br(e($item->note)) !!}</td>
                </tr>
            @endforeach

            @for($i = $delivery->items->count(); $i < 10; $i++)
                <tr>
                    <td style="text-align: center;">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="text-align: center;">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <table style="margin-top: 5rem;">
        <tr>
            <td style="width: 33%; text-align: center;">
                Diterima Oleh :<br><br><br><br><br><br>
                _______________________
            </td>
            <td style="width: 33%; text-align: center;">
                Diserahkan & Diperiksa Oleh :<br><br><br><br><br><br>
                _______________________
            </td>
            <td style="width: 33%; text-align: center;">
                Hormat Kami<br><br><br><br><br><br>
                _______________________
            </td>
        </tr>
    </table>

    <div style="position: fixed; bottom: 0; right: 0; width: 30%;">
        <img src="{{asset('images/kop-surat-jalan.png')}}" alt="kop surat jalan" width="100%">
    </div>
</body>

</html>