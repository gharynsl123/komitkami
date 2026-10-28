<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>FORM LIST KODE PRODUK</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 15px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding-top: 100px;
            /* update */
        }

        /* ===== HEADER (MUNCUL DI SETIAP PAGE) ===== */
        .pdf-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 120px;
            padding: 10px 20px 0 20px;
        }

        .logo {
            width: 260px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-top: 10px;
        }

        .doc-number {
            margin-top: 8px;
            margin-left: 5px;
            font-size: 12px;
        }

        /* ===== CONTENT ===== */
        .content {
            margin-left: 20px;
            margin-right: 10px;
        }

        .section {
            border: 1px solid #000;
            padding: 10px 10px 0px 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            /* update */
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            font-size: 11px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <!-- ===== HEADER ===== -->
    <div class="pdf-header">
        <img src="{{ asset('images/KING-DATAR.png') }}" class="logo">

        <div class="title">FORM LIST KODE PRODUK</div>
        <div class="doc-number">NOMOR DOKUMEN : KIG-QA-F-01-03-00</div>
    </div>

    <!-- ===== CONTENT ===== -->
    <div class="content">
        @foreach ($chunks as $chunk)
            <div class="section">
                <table>
                    <thead>
                        <tr>
                            <th style="width:35px;">No</th>
                            <th>Customer</th>
                            <th>Nama Barang</th>
                            <th>Ukuran Produk</th>
                            <th>Kode Produk</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($chunk as $i => $product)
                            <tr>
                                <td style="text-align:center;">{{ $i + 1 }}</td>
                                <td>{{ $product->brand->users->name ?? '-' }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->ukuran ?? '' }}</td>
                                <td>{{ $product->code ?? '-' }}</td>
                                <td>{{ $product->stock_tersedia }}</td>

                            </tr>
                        @endforeach

                        {{-- dummy row biar full 1 halaman --}}
                        @for ($j = count($chunk); $j < 27; $j++)
                            <tr>
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
        @endforeach
    </div>

</body>

</html>