<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Stok Gudang</title>

    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            padding: 130px 10px 0 10px !important;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        @page {
            size: A4;
        }

        section {
            border: 1px solid black;
            margin: 0px 10px 0px 20px !important;
            padding: 0px !important;
        }

        .row-item {
            display: inline-block;
            margin-right: 40px;
        }

        .box {
            display: inline-block;
            width: 26px;
            height: 13px;
            border: 1px solid #000;
            margin-right: 6px;
            vertical-align: middle;
            font-family: 'Arial', 'DejaVu Sans', sans-serif;
            font-size: 20px;
            line-height: 0px;
            text-align: center;
        }

        th.vert {
            padding: 0 !important;
            vertical-align: middle;
            text-align: center;
            height: 60px;
            width: 20px;
            line-height: 0;
        }

        th.vert>div {
            transform: rotate(-90deg);
            white-space: nowrap;
            display: inline-block;
            padding: 0;
            margin: 0;
            line-height: 1;
        }

        th,
        td {
            border: 1px solid black;
            padding: 2px !important;
            font-size: 10px;
        }

        .date-custom .left {
            position: relative;
            width: 14px;
            height: 30px;
        }

        .date-custom .day,
        .date-custom .month,
        .date-custom .year {
            font-size: 8px;
            /* ukuran kecil */
        }

        .date-custom .day {
            position: absolute;
            top: 0;
            left: 0;
        }

        .date-custom .year {
            position: absolute;
            /* hasur di tengan */
            top: 50%;
            transform: translateY(-50%);
            left: 13;
            right: 0;
        }

        .date-custom .month {
            position: absolute;
            bottom: 0;
            right: 0;
        }

        .date-custom .slash {
            position: absolute;
            top: 4px;
            left: 8px;
            width: 1px;
            /* garis tipis */
            height: 22px;
            /* lebih kecil */
            background: black;
            transform: rotate(40deg);
        }

        table {
            border-collapse: collapse;
            margin: 0px !important;
            padding: 0 !important;
            width: 100%;
        }
    </style>
</head>

<body>
    <header
        style="position: fixed; top: 0; left: 0; right: 0; background: #fff; padding: 10px 10px 0 10px; border-bottom: 1px solid transparent;">
        <img src="{{ asset('images/KING-DATAR.png') }}" alt="Logo" style="width: 280px; margin-bottom:0px !important;">
        <section>
            <h3 style="text-align: center; margin-top: 10px;">KARTU STOK GUDANG</h3>
        </section>
    </header>
    <!-- spacer to push page content below fixed header; adjust height if needed -->
    <section style="border:none !important;">
        @foreach ($chunks as $chunk)
            <table>
                <thead>
                    <tr>
                        <th style="width:30px;">No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Satuan</th>
                        <th style="width:70px;">Stok Tersedia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chunk as $i => $inv)
                        <tr>
                            <td style="text-align:center;">
                                {{ $i + 1 }}
                            </td>
                            <td>{{ $inv->code ?? '-' }}</td>
                            <td>{{ $inv->name }}</td>
                            <td>{{ $inv->type }}</td>
                            <td>{{ $inv->satuan ?? '-' }}</td>
                            <td style="text-align:right;">
                                {{ formatJumlah($inv->stok_tersedia) }}
                            </td>
                        </tr>
                    @endforeach

                    <!-- buat table dummy ketika data di page itu tidak sampai dengan chuck yang di berikan -->
                    @for ($j = count($chunk); $j < 53; $j++)
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

            @if (!$loop->last)
                <div style="page-break-after: always;"></div>
            @endif
        @endforeach
    </section>
</body>

</html>