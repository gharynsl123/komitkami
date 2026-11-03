<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Stok</title>

    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            padding: 220px 10px 0 10px !important;
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
        <img src="{{ public_asset('images/KING-DATAR.png') }}" alt="Logo" style="width: 280px; margin-bottom:0px !important;">
        <section>
            <h3 style="text-align: center; margin-top: 10px;">KARTU STOK BARANG</h3>
            <p style="margin-left: 5px !important;">Nomor Dokumen : KIG-WL-F-02-02-02</p>
            <div style="margin: 20px 0px 10px 8px !important;">
                <span class="row-item">
                    <span class="box"></span> Bahan Baku
                </span>

                <span class="row-item">
                    <span class="box"></span> Bahan Kemas
                </span>

                <span class="row-item">
                    <span class="box">&#x2713;</span> Produk Jadi
                </span>

                <span class="row-item">
                    <span class="box"></span> Lainnya................
                </span>
            </div>
            <p style="margin: 0px 0px 10px 5px !important;">
                <strong>Nama Barang</strong> : {{$inventory->first()->name ?? 'Tidak Diketahui'}}
            </p>
        </section>
    </header>
    <section style="border:none !important;">
        @foreach ($chunks as $chunk)
            <table>
                <thead>
                    <tr>
                        <th class="vert" rowspan="2">
                            <div>Tanggal</div>
                        </th>
                        <th rowspan="2" style="width:70px;">Dok. Ref</th>

                        <th colspan="6" style="text-align:center;">Jumlah</th>

                        <th class="vert" rowspan="2">
                            <div>Satuan</div>
                        </th>
                        <th rowspan="2" style="width:30px;">Sisa</th>
                        <th rowspan="2" style="width:120px;">Keterangan</th>
                    </tr>
                    <tr>
                        <th style="width:45px;">In</th>
                        <th style="width:60px;">No Batch</th>
                        <th style="width:60px;">ED</th>
                        <th style="width:45px;">Out</th>
                        <th style="width:60px;">No Batch</th>
                        <th style="width:60px;">ED</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($chunk as $row)
                        @php
                            $date = \Carbon\Carbon::parse($row->tanggal_transaksi);
                            $day = $date->format('d');
                            $month = $date->format('m');
                            $year = $date->format('Y');
                        @endphp
                        <tr>
                            {{-- TANGGAL --}}
                            <td>
                                <div class="date-custom">
                                    <div class="left">
                                        <span class="day">{{ $day }}</span>
                                        <span class="slash"></span>
                                        <span class="month">{{ $month }}</span>
                                        <div class="year">{{ $year }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- DOK REF --}}
                            <td>{{ $row->dokumen_ref ?? '-' }}</td>

                            {{-- IN --}}
                            <td>{{ $row->jenis == 'in' ? formatJumlah($row->jumlah_barang) : '' }}</td>
                            <td>{{ $row->jenis == 'in' ? $row->no_bach : '' }}</td>
                            <td>{{ $row->jenis == 'in' ? $row->tanggal_ed : '' }}</td>

                            {{-- OUT --}}
                            <td>{{ $row->jenis == 'out' ? formatJumlah($row->jumlah_barang) : '' }}</td>
                            <td>{{ $row->jenis == 'out' ? $row->no_bach : '' }}</td>
                            <td>{{ $row->jenis == 'out' ? $row->tanggal_ed : '' }}</td>

                            {{-- SATUAN --}}
                            <td>pcs</td>

                            {{-- SISA --}}
                            <td style="text-align:right;">
                                {{ formatJumlah($row->running_sisa) }}
                            </td>

                            {{-- KETERANGAN --}}
                            @php
                                $keterangan = $row->keterangan;
                                if(str_contains($keterangan, 'Produksi')){
                                    $keterangan = '';
                                }
                            @endphp
                            <td>{{ $keterangan }}</td>
                        </tr>
                    @endforeach

                    {{-- BARIS KOSONG UNTUK CETAK --}}
                    @for ($i = $chunk->count(); $i < 22; $i++)
                        <tr>
                            <td style="height:30px;">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
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

            @if (!$loop->last)
                <div style="page-break-after: always;"></div>
            @endif
        @endforeach
    </section>

</body>

</html>