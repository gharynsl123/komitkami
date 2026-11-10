<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Permintaan Dokumen Produk</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f6f9;
            color: #334155;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #0d6efd 0%, #03449e 100%);
            color: #ffffff;
            padding: 28px 30px;
            text-align: center;
        }
        .header h2 {
            margin: 0 0 6px 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .badge-doc {
            display: inline-block;
            background-color: #fee2e2;
            color: #dc2626;
            font-weight: bold;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 15px;
        }
        .badge-doc.pds {
            background-color: #fef3c7;
            color: #d97706;
        }
        .table-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        .table-info th, .table-info td {
            padding: 10px 14px;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
        }
        .table-info th {
            width: 35%;
            color: #64748b;
            font-weight: 600;
            background-color: #f8fafc;
        }
        .table-info td {
            color: #1e293b;
        }
        .callout {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 14px 18px;
            border-radius: 6px;
            font-size: 13px;
            color: #1e40af;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .btn-reply {
            display: inline-block;
            background-color: #0d6efd;
            color: #ffffff !important;
            text-decoration: none;
            padding: 10px 22px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: bold;
        }
        .footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 18px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>NOTIFIKASI PERMINTAAN DOKUMEN</h2>
            <p>PT Komitkami Intinusa Gemilang &mdash; Website Form Request</p>
        </div>

        <div class="content">
            @php
                $isPds = str_contains(strtoupper($doc_type ?? ''), 'PDS') || str_contains(strtoupper($doc_type ?? ''), 'PRODUCT');
            @endphp
            <div class="badge-doc {{ $isPds ? 'pds' : '' }}">
                {{ $doc_type ?? 'Dokumen' }}
            </div>

            <p style="margin-top:0; font-size:14px; line-height:1.6;">
                Halo Admin, terdapat permintaan dokumen produk dari pengunjung website Komitkami dengan detail sebagai berikut:
            </p>

            <table class="table-info">
                <tr>
                    <th>Dokumen</th>
                    <td><strong>{{ $doc_type ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <th>Nama Produk</th>
                    <td><strong>{{ $product_title ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <th>Nama Pemohon</th>
                    <td>{{ $name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Email Pemohon</th>
                    <td>
                        <a href="mailto:{{ $email ?? '' }}" style="color:#0d6efd; font-weight:600;">
                            {{ $email ?? '-' }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <th>No. WhatsApp / Telp</th>
                    <td>{{ $phone ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Instansi / Perusahaan</th>
                    <td>{{ $company ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Waktu Permintaan</th>
                    <td>{{ now()->format('d M Y, H:i') }} WIB</td>
                </tr>
                @if(!empty($notes))
                <tr>
                    <th>Catatan / Pesan</th>
                    <td>{{ $notes }}</td>
                </tr>
                @endif
            </table>

            <div class="callout">
                <strong>Tindakan Admin:</strong><br>
                Silakan balas langsung ke email <strong>{{ $email ?? '' }}</strong> dengan melampirkan file PDF dokumen <strong>{{ $doc_type ?? 'Dokumen' }}</strong> untuk produk <strong>{{ $product_title ?? 'tersebut' }}</strong>.
            </div>

            <div style="text-align: center; margin: 25px 0 10px 0;">
                <a href="mailto:{{ $email }}?subject=Pengiriman%20{{ rawurlencode($doc_type ?? 'Dokumen') }}%20-%20{{ rawurlencode($product_title ?? '') }}&body=Yth.%20{{ rawurlencode($name ?? '') }},%0A%0ATerima%20kasih%20telah%20menghubungi%20PT%20Komitkami%20Intinusa%20Gemilang.%20Berikut%20kami%20lampirkan%20dokumen%20yang%20Anda%20minta." class="btn-reply">
                    Balas &amp; Kirim PDF ke Pemohon
                </a>
            </div>
        </div>

        <div class="footer">
            Email otomatis dari Sistem Website Komitkami (komitkami.com)<br>
        </div>
    </div>
</body>
</html>

