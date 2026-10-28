<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda Terima</title>
</head>

<style>
    * {
        padding: 0;
        margin: 0;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 13px;
        margin: 2.5rem !important;
        color: #000;
    }

    .header-title {
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
    }

    .header-title h3 {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 5px;
    }

    .page-number {
        position: absolute;
        right: 0;
        top: 15px;
        font-size: 12px;
    }

    .notes p {
        margin-bottom: 0.2rem;
        line-height: 1.5;
    }

    .tanda-terima-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1.5rem;
        margin-bottom: 2rem;
        table-layout: fixed;
    }

    .tanda-terima-table th,
    .tanda-terima-table td {
        border: 1px solid #000 !important;
        padding: 0.7rem;
        text-align: center;
    }

    .signature-table {
        width: 100%;
        margin-top: 3rem;
        border-collapse: collapse;
    }
    
    .signature-table td {
        border: none !important;
    }
</style>

<body>
    <div class="header-title">
        <h3>PT KOMITKAMI INTINUSA GEMILANG</h3>
        <h3>TANDA TERIMA DOKUMEN PENJUALAN</h3>
        <div class="page-number">Halaman 1 dari 1</div>
    </div>

    <div class="notes">
        <p>Dengan ini kami kirimkan invoice asli sebagai berikut:</p>
        <p>Nama customer: {{ strtoupper($delivery->invoice->orders->first()->user->name) }}</p>
    </div>

    <table class="tanda-terima-table">
        <thead>
            <tr>
                <th style="width:37px">No</th>
                <th style="width:140px">Tanggal Faktur</th>
                <th style="width:140px">No Invoice</th>
                <th style="width:150px">Nomor <br> Surat Jalan</th>
                <th style="width:150px">Jumlah Tagihan (Rp)<br>Termasuk PPN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($delivery->invoice->orders as $index => $order)
                <tr>
                    <td>{{ $index + 1 }}.</td>
                    <td>{{ \Carbon\Carbon::parse($delivery->tanggal_terbit)->format('d M Y') }}</td>
                    <td>{{ $delivery->invoices->first()->no_invoice ?? "-" }}</td>
                    <td>{{ $delivery->no_sj }}</td>
                    <td style="text-align: right;">{{ number_format($order->total_harga, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            @for ($i = $delivery->invoice->orders->count(); $i < 10; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="notes">
        <p>Apabila Bapak/Ibu telah menerima dokumen ini, harap tanda terima ditandatangani, dengan mencantumkan</p>
        <p>nama jelas penerima dan tanggal diterimanya dokumen ini.</p>
        
        <br>
        <p>CATATAN:</p>
        <p>Bila dalam 3 (tiga) hari setelah tanggal diterima tidak ada komplain dari pihak penerima, maka kami anggap</p>
        <p>dokumen telah diterima dengan baik dan benar tanpa ada sanggahan.</p>
    </div>

    <table class="signature-table">
        <tr>
            <td style="width: 50%; text-align: center; padding-left: 2rem; vertical-align: top;">
                Bekasi, {{ \Carbon\Carbon::parse($delivery->tanggal_terbit)->translatedFormat('d F Y') }}<br>
                Pengirim,<br><br><br><br><br><br>
                ( Maura Adelya )
            </td>
            <td style="width: 50%; text-align: right; padding-right: 3rem; vertical-align: top;">
                <div style="display: inline-block; text-align: center;">
                    Penerima,<br><br><br><br><br><br><br>
                    (_________________________)
                </div>
            </td>
        </tr>
    </table>
</body>

</html>