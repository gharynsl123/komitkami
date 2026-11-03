<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>

@php
    $isManual = isset($manualInvoice);
    $subtotal = 0;

    if ($isManual) {
        $customerName = $manualInvoice->manualItems->first()->user->name ?? '-';
        $customerAddress = $manualInvoice->manualItems->first()->user->address ?? 'Alamat';
        $customerNpwp = $manualInvoice->manualItems->first()->user->npwp ?? '-';
        $invoiceNumber = $manualInvoice->no_invoice ?? '-';
        $tanggalTerbit = $manualInvoice->tanggal_terbit;
        $customerReference = $manualInvoice->customer_refrence ?? '-';
        $items = $manualInvoice->manualItems;
        $termsNote = $manualInvoice->note ?? 'Net 30';

        foreach ($items as $item) {
            $subtotal += $item->quantity * $item->price_per_item;
        }
    } else {
        $customerName = $delivery->invoice->orders->first()->user->name ?? '-';
        $customerAddress = $delivery->invoice->orders->first()->user->address ?? 'Alamat';
        $customerNpwp = $delivery->invoice->orders->first()->user->npwp ?? '-';
        $invoiceNumber = $delivery->invoices->first()->no_invoice ?? '-';
        $tanggalTerbit = $delivery->tanggal_terbit;
        $customerReference = $delivery->invoices->first()->customer_refrence ?? '-';
        $items = $delivery->items;
        $termsNote = 'Net 30';

        foreach ($items as $item) {
            $quantity = intval($item->jumlah_barang);
            $subtotal += $quantity * $item->order->product->price;
        }
    }
@endphp

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
        margin: 2rem !important;
    }

    .item-table th {
        border-top: 3px solid #000 !important;
        border-bottom: 3px solid #000 !important;
        border-left: none !important;
        border-right: none !important;
        padding: 0.5rem;
    }

    .item-table td {
        border-top: 1px solid #000 !important;
        border-bottom: 1px solid #000 !important;
        border-left: none !important;
        border-right: none !important;
        padding: 0.5rem;
    }

    .payment-info th {
        text-align: left;
        background-color: #f0f0f0;
        padding: 1rem !important;
    }

    .payment-info td {
        padding: 0 !important;
        background-color: #f0f0f0;
    }
</style>

<body>
    <table>
        <tr>
            <td>
                <img src="{{ public_asset('images/KING-DATAR.png') }}" alt="logo komitkami" width="250px">
            </td>
            <td>
                <div class="address">
                    <p style="text-align: right; font-size: 12px;">
                        <strong>PT Komitkami Intinusa Gemilang</strong> <br><br>
                        KAWASAN BIZZPARK 3 BLOK E/20 <br>
                        JL. SULTAN AGUNG KM 28, BEKASI, JAWA BARAT, 17131 <br>
                        komitkami@gmail.com
                    </p>
                </div>
            </td>
        </tr>
        <tr>
            <td style="height: 1rem;">&nbsp;</td>
            <td style="height: 1rem;">&nbsp;</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">
                <p style="text-transform: uppercase; line-height: 1.5;">
                    {{ $customerName }} <br>
                    {{ $customerAddress }} <br>
                    NPWP : {{ $customerNpwp }}
                </p>
            </td>
            <td style="background-color: #f0f0f0; padding: 1rem; vertical-align: top;">
                <p style="text-align: right; line-height: 1.8;">
                    <strong>
                        Invoice # : {{ $invoiceNumber }} <br>
                        {{ \Carbon\Carbon::parse($tanggalTerbit)->format('d/m/Y') }} <br>
                    </strong>
                    Terms : {{ $termsNote }} <br>
                    Due Date : {{ \Carbon\Carbon::parse($tanggalTerbit)->addDays(30)->format('d/m/Y') }} <br>

                    <strong>
                        Total: Rp {{ number_format(trim($subtotal), 0, ',', '.') }}
                    </strong>
                </p>
            </td>
        </tr>
        <tr>
            <td style="height: 1rem;">&nbsp;</td>
            <td style="height: 1rem;">&nbsp;</td>
        </tr>
    </table>

    <table cellspacing="0" style="width: 100%; border-collapse: collapse;" class="item-table">
        <tr>
            <th style="text-align: left; width:2.5rem;">QTY</th>
            <th style="text-align: left; width:15rem;">DESCRIPTION</th>
            <th style="text-align: right; width: 1rem;">DISC</th>
            <th style="text-align: right; width: 7rem;">UNIT PRICE (Rp.)</th>
            <th style="text-align: right; width:0.5rem;">TAXED</th>
            <th style="text-align: right; width: 5.5rem;">AMOUNT (Rp.)</th>
        </tr>
        @foreach ($items as $item)
            @php
                if ($isManual) {
                    $qty = $item->quantity;
                    $productName = $item->product->name ?? '-';
                    $productTipe = $item->product->tipe ?? '-';
                    $price = $item->price_per_item;
                    $amount = $qty * $price;
                } else {
                    $qty = $item->jumlah_barang;
                    $productName = $item->order->product->name ?? '-';
                    $productTipe = $item->order->product->tipe ?? '-';
                    $price = $item->order->product->price;
                    $amount = $qty * $price;
                }
            @endphp
            <tr>
                <td style="text-align: left;">{{ formatJumlah($qty) }} - {{ $productTipe == 'botol' ? 'btl' : 'jrg'}}
                </td>
                <td>{{ $productName }}</td>
                <td style="text-align: right;">0.0%</td>
                <td style="text-align: right; white-space: nowrap;">@currency($price)</td>
                <td style="text-align: right;">X</td>
                <td style="text-align: right; white-space: nowrap;">@currency($amount)</td>
            </tr>
        @endforeach
        @php
            $dpp = $subtotal / 1.11;
            $ppn = $subtotal - $dpp;
        @endphp
        <tr>
            <td style="border-bottom: none !important; border-top: none !important;" colspan="2"></td>
            <td style="text-align:right" colspan="3">SUBTOTAL</td>
            <td style="text-align:right; white-space: nowrap;">@currency($dpp)</td>
        </tr>
        <tr>
            <td style="border-bottom: none !important; border-top: none !important;" colspan="2"></td>
            <td style="text-align:right" colspan="3">PPN 11.0% incl.</td>
            <td style="text-align:right; white-space: nowrap;">@currency($ppn)</td>
        </tr>
        <tr>
            <td style="border-bottom: none !important; border-top: none !important;" colspan="2"></td>
            <td style="text-align:right" colspan="3">TOTAL</td>
            <td style="text-align:right; white-space: nowrap;">@currency($subtotal)</td>
        </tr>
        <tr>
            <td style="border-bottom: none !important; border-top: none !important;" colspan="2"></td>
            <td style="border-bottom: none !important; text-align:right" colspan="3">BALANCE DUE</td>
            <td style="text-align:right; border-bottom: none !important; white-space: nowrap;">@currency($subtotal)</td>
        </tr>
    </table>

    <div style="background-color: #f4f4f4; padding: 1.5rem; margin-top: 2rem;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 1rem;">
                    <div style="font-weight: bold; margin-bottom: 1rem;">Payment Detail</div>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 35%; padding-bottom: 0.5rem; vertical-align: top;">Bank Name :</td>
                            <td style="width: 65%; padding-bottom: 0.5rem; vertical-align: top;">BANK MANDIRI</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 0.5rem; vertical-align: top;">Bank Branch :</td>
                            <td style="padding-bottom: 0.5rem; vertical-align: top;">CABANG KELAPA GADING BARAT</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 0.5rem; vertical-align: top;">Account Name :</td>
                            <td style="padding-bottom: 0.5rem; vertical-align: top;">PT. KOMITKAMI INTINUSA GEMILANG</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 0.5rem; vertical-align: top;">Account Number :</td>
                            <td style="padding-bottom: 0.5rem; vertical-align: top;">125-0030052005</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Payment Reference :</td>
                            <td style="vertical-align: top;">{{ $invoiceNumber }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 1rem;">
                    <div style="font-weight: bold; margin-bottom: 1rem;">Other Information</div>
                    <div>
                        Customer Reference : {{ $customerReference }}
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <table style="margin-top: 5rem; width: 100%;">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                Penerima :
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                {{ \Carbon\Carbon::parse($tanggalTerbit)->locale('id')->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td style="height: 7rem; text-align: center; vertical-align: bottom;">
                <span style="display: inline-block; border-bottom: 1px solid #000; width: 180px; padding-bottom: 0px; color: transparent;">
                    _
                </span>
            </td>
            <td style="height: 7rem; text-align: center; vertical-align: bottom;">
                <span style="display: inline-block; border-bottom: 1px solid #000; width: 180px; padding-bottom: 0px;">
                    Meti Rahmawati, S.E
                </span>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                Finance
            </td>
        </tr>
    </table>
    <p style="bottom: 2rem; left: 2rem; position: absolute; color: #6c757d;">Sales Invoice #{{ $invoiceNumber }}</p>
</body>

</html>