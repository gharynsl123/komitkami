<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Permintaan Material Produksi</title>
    <style>
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            font-size: 12px;
            margin-top: 15px;
            margin-left: 35px;
            margin-right: 35px;
        }

        .header {
            width: 100%;
        }

        .header td {
            vertical-align: top;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .info {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;

        }

        th, td {
            padding: 4px;
            text-align: center;
        }

        .left {
            text-align: left !important;
            vertical-align: top;
        }

        .no-border {
            border: none !important;
        }

        .small {
            font-size: 10px;
        }
    </style>
</head>
<body>
    @forelse ($permintaan_material as $productId => $productGroup)
        <div style="{{ !$loop->last ? 'page-break-after: always;' : '' }}">
            <img src="{{ public_asset('images/KING-DATAR.png') }}" alt="Logo"
                    style="width: 180px; margin-bottom:0px !important;">
            <!-- HEADER -->
            <table class="header">
                <tr>
                    <div class="title">
                        FORM PERMINTAAN MATERIAL PRODUKSI
                    </div>
                </tr>
                <tr>
                    <td style="text-align: left; border: none !important;">
                        <p style="margin-bottom: 14px !important;">Nomor Dokumen: KIG-PR-F-01-01-02</p>
                        <p> <strong>Tanggal:</strong> {{ Carbon\Carbon::parse($productGroup['created_at'] ?? $tanggal)->format('d/m/Y') }}</p>
                    </td>
                </tr>
            </table>

            <!-- TABLE -->
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2" style="width: 150px;">Nama Bahan Baku</th>

                        <th colspan="2">Penggunaan Bahan Baku</th>
                        <th style="width: 20px;" colspan="2">Permintaan <br> Bahan Baku</th>

                        <th colspan="4">Penyerahan Bahan Baku</th>
                        <th colspan="4">Pengembalian Bahan Baku</th>
                    </tr>
                    <tr style="font-size:10px;">
                        <th>Nama Produk</th>
                        <th>No Batch <br> Produk</th>

                        <th>Jumlah</th>
                        <th>Yang <br> Mengajukan</th>


                        <th>Jumlah</th>
                        <th>No Batch <br> BB</th>
                        <th>Yang <br> Memberi</th>
                        <th>Yang <br> Menerima</th>

                        <th>Jumlah</th>
                        <th>No Batch <br> BB</th>
                        <th>Yang <br> Memberi</th>
                        <th>Yang <br> Menerima</th>
                    </tr>
                </thead>

                <tbody>
                    @php 
                                            $no = 1;
                        $materials = $productGroup['materials'] ?? [];
                        $materialCount = 0;
                        foreach ($materials as $mat) {
                            $materialCount += $mat['row_count'];
                        }
                        $totalRows = max($materialCount, 15);
                        $rowCount = 0;
                    @endphp
                    @foreach ($materials as $material)
                        @php
                            $text = $material['material_name'] ?? '-';
                            $length = mb_strlen($text);

                            if ($length <= 20) {
                                $fontSize = 12;
                            } elseif ($length <= 35) {
                                $fontSize = 11;
                            } elseif ($length <= 45) {
                                $fontSize = 10;
                            } elseif ($length <= 56) {
                                $fontSize = 9;
                            } else {
                                $fontSize = 7;
                            }

                            $productName = $productGroup['product_name'] ?? '-';
                            $len = mb_strlen($productName);

                            if ($len <= 20) {
                                $fSize = '12px';
                            } elseif ($len <= 35) {
                                $fSize = '11px';
                            } elseif ($len <= 45) {
                                $fSize = '10px';
                            } elseif ($len <= 56) {
                                $fSize = '9px';
                            } else {
                                $fSize = '8px';
                            }
                        @endphp
                        @php
                            $isEmptyProduct = empty(trim($productGroup['product_name'])) && (empty(trim($productGroup['batch_first'])) || trim($productGroup['batch_first']) == '-');
                            $matRowCount = $material['row_count'];
                            $index = 0;
                        @endphp

                        @foreach($material['stock_out_details'] as $bNum => $soJumlah)
                            <tr>
                                @if($index == 0)
                                    <td rowspan="{{ $matRowCount }}">{{ $no++ }}</td>
                                    <td class="left" rowspan="{{ $matRowCount }}" style="font-size: {{ $fontSize }}px; vertical-align: middle !important;">{{ $material['material_name'] }}</td>
                                @endif

                                @if($isEmptyProduct)
                                    <td></td>
                                    <td></td>
                                @else
                                    @if($rowCount == 0)
                                        <td rowspan="{{ $materialCount }}" style="font-size: {{ $fSize }}; vertical-align: middle;">
                                            {{ $productGroup['product_name'] }}
                                            @if(!empty($productGroup['product_size']))
                                                <br>{{ $productGroup['product_size'] }}
                                            @endif
                                        </td>
                                        <td rowspan="{{ $materialCount }}" style="vertical-align: middle;">
                                            {{ $productGroup['batch_first'] }}
                                            @if($productGroup['is_batch_range'])
                                                <br>s/d<br>
                                                {{ $productGroup['batch_last'] }}
                                            @endif
                                        </td>
                                    @endif
                                @endif

                                @if($index == 0)
                                    <td rowspan="{{ $matRowCount }}">{{ $material['total_jumlah'] }}</td>
                                    <td rowspan="{{ $matRowCount }}">{{ $material['request_by'] }}</td>
                                @endif

                                <td>{{ $soJumlah > 0 ? $soJumlah : '' }}</td>
                                <td style="font-size: 8px;">{{ $bNum }}</td>
                                <td>{{ $material['taken_from'] }}</td> 
                                <td>{{ $material['request_by'] }}</td> <!-- OP Produksi -->

                                <td>-</td> <!-- Jumlah -->
                                <td></td> <!-- No Batch BB -->
                                <td></td> <!-- OP Gudang -->
                                <td></td> <!-- OP Produksi -->
                            </tr>
                            @php 
                                                $rowCount++;
                                $index++;
                            @endphp
                        @endforeach
                    @endforeach

                    @for ($i = $rowCount; $i < 15; $i++)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <table>
                <tr>
                    <td class="left" style="height: 60px;">
                        <strong>Keterangan:</strong> {{ $productGroup['keterangan'] }}
                    </td>
                </tr>
            </table>
        </div>
    @empty
        <div style="text-align: center; margin-top: 50px;">
            <h3>Tidak ada data permintaan bahan baku untuk tanggal tersebut.</h3>
        </div>
    @endforelse
</body>
</html>