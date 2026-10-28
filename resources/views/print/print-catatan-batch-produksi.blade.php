<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Batch Produksi</title>
    <!-- no margin/padding for * -->
    <style>
        * {
            margin: 0;
            padding: 2px 10px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        th {
            text-align: center;
            margin: 0px;
            border: 1px !important;
        }

        td {
            text-align: left;
            margin: 0px;
        }

        .table {
            border-collapse: collapse;
            /* Agar tidak ada gap di antaranya */
            margin: 0px;
            padding: 0px;
            border: 1px solid black !important;
            font-size: 11px;
        }

        th,
        td {
            padding: 10px !important;
            border: 0.1px solid black !important;
            /* Border untuk tiap cell */
        }

        section {
            margin: 0px 25px;
            border: 1px solid black;
        }
    </style>
</head>

<body style="margin-top:177px !important;">
    <header
        style="position: fixed; top: 0; left: 0; right: 0; background: #fff; padding: 10px 10px 0 10px; border-bottom: 1px solid transparent;">
        <img src="{{asset('images/KING-DATAR.png')}}" alt="ICON KOMITKAMI" style="width:300px; height:auto;">
        <section class="header" style="padding:10px 2px !important;">
            <h3 style="text-align:center; transform:capitalized; margin-bottom:5px;">CATATAN BATCH PRODUKSI</h3>
            <p style="font-size: 12px;">Nomor Dokumen : KIG-PR-F-01-05-01</p>
        </section>
    </header>
    <section style="padding: 0px !important; border: none !important;">
        @foreach($chunks as $pageIndex => $chunk)
            <table class="table" style="width:100%; border-collapse: collapse; margin-bottom: 40px;">
                <thead>
                    <tr>
                        <th style="width:60px;">Tanggal</th>
                        <th>Nama Produk</th>
                        <th>No. Batch</th>
                        <th>Jumlah Produk (pcs)</th>
                        <th>Exp Date</th>
                        <th>Petugas</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($chunk as $item)
                        @php
                            $clearName = preg_replace('/\s*\([^)]*\)/', '', $item->product->name);
                            $clearName = trim($clearName);
                            if (str_contains($clearName, 'KOMITKAMI NBF')) {
                                $clearName = str_replace('KOMITKAMI', 'KING', $clearName);
                            }
                        @endphp
                        <tr>
                            <td>{{ $item->tanggal_produksi->format('d-m-Y') }}</td>
                            <td>{{ $clearName }}</td>
                            <td>{{ $item->batch_number }}</td>
                            <td>{{ $item->produkjadi->nilai_actual ?? 'Tidak Ada' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_expired)->format('m-Y') }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach

                    <!-- dummy tr -->
                    @php
                        $rowsOnPage = $chunk->count();
                        $blanksToAdd = $rowsPerPage - $rowsOnPage;
                    @endphp

                    @for ($d = 0; $d < $blanksToAdd; $d++)
                        <tr>
                            <td>&nbsp;</td>
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

            {{-- PAGE BREAK SETIAP HALAMAN KECUALI HALAMAN TERAKHIR --}}
            @if(!$loop->last)
                <div style="page-break-after: always;"></div>
            @endif
        @endforeach
    </section>
</body>

</html>