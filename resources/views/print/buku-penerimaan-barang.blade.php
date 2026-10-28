<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Penerimaan Barang</title>
    <style>
        body {
            font-family: 'Calibri', sans-serif;
            margin: 0;
            padding: 0;
        }
        
        /* Print-specific rules to ensure headers repeat and pages break correctly */
        @media print {
            @page {
                margin-top: 1mm; /* Mengatur jarak atas halaman saat di-print */
            }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            tr { page-break-inside: avoid; }
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            padding-top: 0 !important;
        }
        
        .main-table th, .main-table td {
            border: 1px solid #000;
        }

        /* Style for the image row to appear outside the border visually */
        .image-row td {
            border: none !important;
            padding: 0 0 10px 0;
            text-align: left;
        }

        /* Container header styling */
        .container-header {
            padding: 10px !important;
            text-align: left;
            border-bottom: none !important;
        }
        
        .container-header h1 {
            font-size: 18px;
            text-align: center;
            margin: 0 0 15px 0;
            text-transform: uppercase;
        }
        
        .container-header p {
            margin: 0;
            font-size: 14px;
        }

        /* Table Columns */
        .col-header th {
            background-color: #f2f2f2;
            text-align: center;
            padding: 5px;
            border-top: 1px solid #000 !important;
        }
        
        .data-row td {
            padding: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <table class="main-table">
        <thead>
            <tr class="image-row">
                <td colspan="14">
                    <img src="{{ asset('images/KING-DATAR.png') }}" alt="" height="80px">
                </td>
            </tr>
            <tr>
                <td colspan="14" class="container-header">
                    <h1>Buku Penerimaan Barang</h1>
                    <p>Nomor Dokumen : KIG-WL-F-01-01-02</p>
                </td>
            </tr>
            <tr class="col-header">
                <th>No</th>
                <th>Tgl</th>
                <th>Nama <br>Barang</th>
                <th>No. Batch / No. LOT</th>
                <th>Tanggal <br>Expired</th>
                <th>Jumlah <br>Barang</th>
                <th>Supplier</th>
                <th>Produsen</th>
                <th>Negara Produsen</th>
                <th>No. PO</th>
                <th>Yang <br>Menerima</th>
                <th>Yang <br>Menyerahkan</th>
                <th>Operator Gudang</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stockIn as $item)
                <tr class="data-row">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->tanggal_masuk ? Carbon\Carbon::parse($item->tanggal_masuk)->format('d-m-Y') : 'Tidak Ditemukan' }}</td>
                    <td>{{ $item->inventory->name ?? 'Tidak Ditemukan' }}</td>
                    <td>{{ $item->nomor_batch ?? 'Tanpa Batch'}}</td>
                    <td>{{ $item->tanggal_ed ? Carbon\Carbon::parse($item->tanggal_ed)->format('d-m-Y') : '-' }}</td>
                    <td>{{ formatJumlah($item->stok) }} {{ $item->inventory->type == 'Bahan Baku' ? 'Gram' : 'Pcs' }}</td>
                    <td>{{ $item->suplier_material->name ?? $item->vendor ?? '' }}</td>
                    <td>{{ $item->produser->name ?? '' }}</td>
                    <td>{{ $item->produser->negara ?? '' }}</td>
                    <td>{{ $item->nomor_sj ?? 'Tidak ada'}}</td>
                    <td>{{ $item->user->name ?? '' }}</td>
                    <td>{{ $item->nama_penyerah ?? 'Tidak ada' }}</td>
                    <td>{{ $item->user->name ?? ''}}</td>
                    <td>{{ $item->keterangan ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>