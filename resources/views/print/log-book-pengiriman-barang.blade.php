<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Book Pengiriman Barang</title>
    <style>
        body {
            font-family: 'Calibri', sans-serif;
            margin: 0;
            padding: 0;
        }
        @page {
            size: A4 landscape;
            margin: 5mm;
        }

        .main-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 11px;
            padding-top: 0 !important;
            table-layout: fixed;
            word-wrap: break-word;
        }

        .main-table th, .main-table td {
            border-top: none;
            border-left: none;
            border-bottom: 1px solid #000;
            border-right: 1px solid #000;
        }

        .main-table tr > th:first-child,
        .main-table tr > td:first-child {
            border-left: 1px solid #000;
        }

        /* Style for the image row to appear outside the border visually */
        thead tr:first-child td {
            border: none !important;
        }

        /* Container header styling */
        .container-header {
            padding: 10px 0;
            text-align: left;
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
            text-align: center;
            padding: 5px;
            border-top: 1px solid #000;
        }

        .data-row td {
            padding: 5px;
            vertical-align: middle !important;
            text-align: center !important;
        }

        /* Let DOMPDF break the page naturally */
    </style>
</head>
<body>
    <table class="main-table">
        <thead>
            <tr>
                <td colspan="13" style="border: none !important; text-align: left;">
                    <div style="margin-bottom: 2px !important;">
                        <img src="{{ public_path('images/KING-DATAR.png') }}" alt="" height="80px">
                        <div class="container-header">
                            <h1 style="margin: 0px !important; padding: 0px !important;">LOG BOOK PENGIRIMAN BARANG</h1>
                            <p>Nomor Dokumen : KIG-WL-F-03-02-01</p>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="col-header">
                <th rowspan="2" style="width: 3%;">No</th>
                <th rowspan="2" style="width: 8%;">Tanggal Pengiriman</th>
                <th rowspan="2" style="width: 14%;">Customer</th>
                <th rowspan="2" style="width: 11%;">Surat Jalan</th>
                <th rowspan="2" style="width: 10%;">No. PO</th>
                <th rowspan="2" style="width: 5%;">Sisa<br>PO</th>
                <th colspan="4" style="width: 32%;">Produk Yang Dikirim</th>
                <th rowspan="2" style="width: 5%;">Jumlah<br>Terkirim</th>
                <th rowspan="2" style="width: 7%;">Pengirim</th>
                <th rowspan="2" style="width: 5%;">Keterangan</th>
            </tr>
            <tr class="col-header">
                <th style="width: 12%;">Nama<br>Produk</th>
                <th style="width: 9%;">No.<br>Batch<br>Produk</th>
                <th style="width: 6%;">Expired.<br>Date</th>
                <th style="width: 5%;">Qty</th>
            </tr>
        </thead>
        @php
            $deliveredPerOrder = [];

            $rowNumber = 0;
 
            $getDynamicFontSize = function ($text) {
                $len = strlen($text);
                if ($len > 40) return '8px';
                if ($len > 25) return '9px';
                if ($len > 15) return '10px';
                return '11px';
            };
        @endphp
        <tbody>
        @foreach($deliveries as $index => $delivery)
            @php
                $noValue       = $index + 1;
                $tanggalValue  = $delivery->tanggal_terbit ? Carbon\Carbon::parse($delivery->tanggal_terbit)->format('d-m-Y') : '-';
                $customerValue = optional(optional($delivery->invoice)->user)->name ?? '-';
                $suratJalanValue = $delivery->no_sj ?? '-';
                $noPoValue     = optional($delivery->invoice)->nomor_invoice ?? '-';
                $pengirimValue = 'PT Komitkami';
                $keteranganValue = $delivery->note ?? '-';
            @endphp

            @if($delivery->items->isEmpty())
                @php $rowNumber++; @endphp
                <tr class="data-row">
                    <td>{{ $rowNumber }}</td>
                    <td>{{ $tanggalValue }}</td>
                    <td>{{ $customerValue }}</td>
                    <td>{{ $suratJalanValue }}</td>
                    <td>{{ $noPoValue }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td style="font-size: 10px;">{{ $pengirimValue }}</td>
                    <td>{{ $keteranganValue }}</td>
                </tr>
            @else
                @php
                    $customerValue = optional(optional($delivery->items->first()->order)->user)->name ?? $customerValue;
                @endphp
                @foreach($delivery->items as $itemIndex => $item)
                    @php
                        $orderId = $item->order_id;
                        if (!isset($deliveredPerOrder[$orderId])) {
                            $deliveredPerOrder[$orderId] = 0;
                        }
                        $sisaPo = (optional($item->order)->quantity ?? 0) - $deliveredPerOrder[$orderId];
                        $deliveredPerOrder[$orderId] += $item->jumlah_barang;

                        // product name clean
                        $productName = optional(optional($item->order)->product)->name ?? '-';
                        $productName = str_replace('(Green Tea)', '', $productName);
                        $productName = str_replace('(Vanilla)', '', $productName);
                        $productName = str_replace('(Lemon)', '', $productName);
                        $productName = str_replace('(Strawberry)', '', $productName);

                        $cleanname = preg_replace('/\s*\([^)]*\)/', '', $productName);
                        $cleanname = trim($cleanname);

                        if (str_contains($cleanname, 'KOMITKAMI NBF')) {
                            $productName = str_replace('KOMITKAMI', 'KING', $productName);
                        }
                    @endphp

                    @if($item->stockOuts->isEmpty())
                        @php $rowNumber++; @endphp
                        <tr class="data-row">
                            <td>{{ $rowNumber }}</td>
                            <td>{{ $tanggalValue }}</td>
                            <td>{{ $customerValue }}</td>
                            <td>{{ $suratJalanValue }}</td>
                            <td>{{ $noPoValue }}</td>
                            <td>{{ $sisaPo }} pcs</td>
                            <td style="font-size: {{ $getDynamicFontSize($productName ?? '-') }};">{{ $productName ?? '-' }}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>{{ formatJumlah($item->jumlah_barang) . ' pcs' }}</td>
                            <td style="font-size: 10px;">{{ $pengirimValue }}</td>
                            <td>{{ $keteranganValue }}</td>
                        </tr>
                    @else
                        @php
                            $rowspan = $item->stockOuts->count();
                            $middleRowIndex = floor(($rowspan - 1) / 2);
                        @endphp
                        @foreach($item->stockOuts as $batchIndex => $batch)
                            @php 
                                $rowNumber++; 
                                $isMiddleBatch = $batchIndex == $middleRowIndex;
                                $isLastBatch = $batchIndex == ($rowspan - 1);
                                $bBottom = $isLastBatch ? '1px solid #000' : 'none';
                            @endphp
                            <tr class="data-row">
                                <td>{{ $rowNumber }}</td>
                                <td>{{ $tanggalValue }}</td>
                                <td>{{ $customerValue }}</td>
                                <td>{{ $suratJalanValue }}</td>
                                <td>{{ $noPoValue }}</td>

                                <td style="border-bottom: {{ $bBottom }} !important; vertical-align: middle;">
                                    {!! $isMiddleBatch ? $sisaPo . ' pcs' : '&nbsp;' !!}
                                </td>
                                <td style="border-bottom: {{ $bBottom }} !important; font-size: {{ $getDynamicFontSize($productName ?? '-') }}; vertical-align: middle;">
                                    {!! $isMiddleBatch ? htmlspecialchars($productName ?? '-') : '&nbsp;' !!}
                                </td>

                                <td>{{ optional(optional($batch->stockInProduct)->produksi)->batch_number ?? optional($batch->stockInProduct)->batch_number ?? '-' }}</td>
                                <td>{{ optional(optional($batch->stockInProduct)->produksi)->tanggal_expired ? Carbon\Carbon::parse($batch->stockInProduct->produksi->tanggal_expired)->format('m/Y') : Carbon\Carbon::parse($batch->stockInProduct->expired_date)->format('m/Y') ?? '-' }}</td>
                                <td>{{ formatJumlah($batch->quantity) . ' pcs' }}</td>

                                <td style="border-bottom: {{ $bBottom }} !important; vertical-align: middle;">
                                    {!! $isMiddleBatch ? formatJumlah($item->jumlah_barang) . ' pcs' : '&nbsp;' !!}
                                </td>

                                <td style="font-size: 10px;">{{ $pengirimValue }}</td>
                                <td>{{ $keteranganValue }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach
        </tbody>
    </table>
</body>
</html>