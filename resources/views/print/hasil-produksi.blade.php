<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Produksi</title>
</head>
<style>
body {
    font-size: 11pt;
    color: #000000;
}

h1 {
    font-size: 14pt;
    font-weight: 600;
    margin: 0px !important;
    padding: 0px !important;
    text-transform: uppercase;
    text-align: center;
}

h2 {
    font-size: 12pt;
    font-weight: 600;
    margin: 0px !important;
    padding: 0px !important;
    text-transform: uppercase;
    text-align: center;
}

.sub-title-kebersihan {
    background-color: #87B1DE;
    font-weight: bold;
    text-align: center;
    padding: 3px;
    border: 1px solid #000000;
    text-transform: uppercase;
}

.sub-title-general {
    background-color: #c6dcf5;
    font-weight: bold;
    text-align: center;
    padding: 3px;
    border: 1px solid #000000;
    text-transform: uppercase;
}

table {
    border-collapse: collapse;
    width: 100%;
}

th,
td {
    border: 1px solid #aaa;
    padding: 6px;
    text-align: left;
}

.signature-space {
    height: 30px;
}

.table-borderless th,
.table-borderless td {
    border: none !important;
    padding: 4px;
}

@media print {
    .print-page {
        page-break-before: always;
        min-height: 100vh;
        /* tinggi halaman penuh */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
}

/* Saat dilihat di layar biasa, biar tetap rapi juga */
.print-page {
    min-height: 100vh;
    page-break-before: always;
    margin: 14px 0 0 0;
    border: 1px solid #000000;
    box-sizing: border-box;
}
</style>

<body>
   @php
        $productname = $produksi ? $produksi->product->name : 'tidak ada';
        $cleanname = preg_replace('/\s*\([^)]*\)/', '', $productname);
        $cleanname = trim($cleanname);

        if (str_contains($cleanname, 'KOMITKAMI NBF')) {
            $cleanname = str_replace('KOMITKAMI', 'KING', $cleanname);
        }

        $oldproduction = $produksi_sebelumnya ? $produksi_sebelumnya->product->name : 'tidak ada';
        $cleanoldname = preg_replace('/\s*\([^)]*\)/', '', $oldproduction);
        $cleanoldname = trim($cleanoldname);
    @endphp

    <div style="border: 1px solid #000000; box-sizing: border-box; margin: 14px 0 0 0;">
        <h1>CHECKLIST KELENGKAPAN CPB</h1>
        <h1>{{$produksi->product->brand->name}}</h1>
        <table style="margin: 10rem 0 4rem 3px;" class="table-borderless">
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Nama Produk</td>
                <td style="padding:2px 0;">: {{ $cleanname }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Kode Produk</td>
                <td style="padding:2px 0;">: {{ $produksi->product->code }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Batch Number</td>
                <td style="padding:2px 0;">: {{ $produksi->batch_number }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Tanggal Produksi</td>
                <td style="padding:2px 0;">: {{ $produksi->tanggal_produksi->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Tanggal Kadaluarsa</td>
                <td style="padding:2px 0;">: {{ $produksi->tanggal_expired }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Batch Size</td>
                <td style="padding:2px 0;">: {{ $produksi->batch_size }} Kg</td>
            </tr>
        </table>


        <table style="margin-bottom:15px; width:80%;">
            <tr>
                <td style="border: none; background-color: transaparant !important; text-align:center;"></td>
                <td style="border: none; background-color: transaparant !important; text-align:center;"></td>
                <td
                    style="border: none; background-color: transaparant !important; text-align:center; padding-bottom: 15px;">
                    Ada</td>
                <td
                    style="border: none; background-color: transaparant !important; text-align:center; padding-bottom: 15px;">
                    TD*</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000000; text-align:center; width:12%; padding: 0px !important;">1</td>
                <td style="border: 1px solid #000000; padding: 2px; width:55%;">Pemeriksaan Kebersihan</td>
                <td style="border: 1px solid #000000; padding: 2px;"></td>
                <td style="border: 1px solid #000000; padding: 2px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000000; text-align:center; width:12%; padding: 0px !important;">2</td>
                <td style="border: 1px solid #000000; padding: 2px; width:55%;">Persiapan Bahan Kemas dan Bahan Baku
                </td>
                <td style="border: 1px solid #000000; padding: 2px;"></td>
                <td style="border: 1px solid #000000; padding: 2px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000000; text-align:center; width:12%; padding: 0px !important;">3</td>
                <td style="border: 1px solid #000000; padding: 2px; width:55%;">Proses Pengolahan dan Pemeriksaan QC
                </td>
                <td style="border: 1px solid #000000; padding: 2px;"></td>
                <td style="border: 1px solid #000000; padding: 2px;"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000000; text-align:center; width:12%; padding: 0px !important;">4</td>
                <td style="border: 1px solid #000000; padding: 2px; width:55%;">Serah terima Produk Jadi</td>
                <td style="border: 1px solid #000000; padding: 2px;"></td>
                <td style="border: 1px solid #000000; padding: 2px;"></td>
            </tr>
        </table>
        <small style="padding: 3px;">Jumlah halaman: .....</small>

        <table style="margin-bottom: 20px; width:88%;">
            <tr>
                <td style="border:none !important; text-align:center;">
                    <p>Mengetahui</p>
                    <div class="signature-space"></div>
                    <p>Manager Produksi</p>
                </td>
                <td style="border:none !important; text-align:center;">
                    <p>Diluluskan tgl</p>
                    <div class="signature-space"></div>
                    <p>Manager QC</p>
                </td>
            </tr>
        </table>

        <table style="margin:0px !important;">
            <tr>
                <td style="border:none !important; vertical-align: top;">
                    <p>*) TD = Tidak Digunakan</p>
                </td>
                <td
                    style="border:1px solid #000000 !important; width: 27%; padding: 0px !important; vertical-align: top;">
                    <p style="text-align:start; margin:0px!important;">&nbsp; Salinan CPB disetujui Oleh : <br>(Initial,
                        Paraf, Tgl)</p>
                </td>
            </tr>
        </table>
    </div>


    <!-- section ke dua -->
    {{-- === Divisi Kebersihan === --}}
    <div class="print-page">
        <h1>CATATAN PEMBUATAN BATCH</h1>
        <h1>{{$produksi->product->brand->name}}</h1>
        <p style="font-weight:600; text-align:center; padding: 0px !important; margin: 0px !important;">Pemeriksaan
            Kebersihan</p>
        <table style="width:100%; margin: 0px 0px 15px 3px !important; " class="table-borderless">
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Nama Produk</td>
                <td style="padding:2px 0;">: {{ $cleanname }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Kode Produk</td>
                <td style="padding:2px 0;">: {{ $produksi->product->code }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Batch Number</td>
                <td style="padding:2px 0;">: {{ $produksi->batch_number }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Tanggal Produksi</td>
                <td style="padding:2px 0;">: {{ $produksi->tanggal_produksi->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Tanggal Kadaluarsa</td>
                <td style="padding:2px 0;">: {{ $produksi->tanggal_expired }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Batch Size</td>
                <td style="padding:2px 0;">: {{ $produksi->batch_size }} Kg</td>
            </tr>
        </table>

        <div class="section" style="margin-bottom:0px!important;">
            <div class="sub-title-kebersihan">
                <h2>Pemeriksaan Kebersihan</h2>
            </div>
            @foreach($kebersihan->groupBy('bagianKebersihan.ruangProduksi.nama_ruangan') as $namaRuang => $items)
            <table width="100%" cellspacing="0" style="font-size: 12px; margin-bottom:15px; margin-top:0px!important;">
                <tr style="background-color: #c6dcf5;">
                    <td style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 0px solid #000000; border-bottom:0px solid #000000; padding:2px;" colspan="1">Nama Ruang</td>
                    <td style="border-top: 1px solid #000000; border-left: none !important; border-right: 1px solid #000000; border-bottom:0px solid #000000; padding:2px;" colspan="6">: <strong>{{ $namaRuang }}</strong>
                    </td>
                </tr>
                <tr style="background-color: #c6dcf5;">
                    @php
                        // --- LOGIC VIEW: Ambil data per ruangan jika ada ---
                        // Default pakai variabel lama ($cleanoldname, $produksi_sebelumnya)
                        $displayOldName = $cleanoldname ?? '-';
                        $displayOldBatch = $produksi_sebelumnya->batch_number ?? '-';

                        if (isset($previousDataByRoom) && isset($previousDataByRoom[$namaRuang])) {
                            $prevProd = $previousDataByRoom[$namaRuang];
                            
                            // Hitung nama produk bersihnya
                            $rawName = $prevProd->product->name ?? '-';
                            $cleanNameParts = preg_replace('/\s*\([^)]*\)/', '', $rawName);
                            // Ambil part pertama yang bersih
                            $displayOldName = trim($cleanNameParts); 
                            
                            $displayOldBatch = $prevProd->batch_number ?? '-';
                        }
                    @endphp

                    <td style="border-top: none !important; border-left: 1px solid #000000; border-right: 0px solid #000000; border-bottom:none !important; padding:2px;" colspan="1">Produk sebelumnya
                    </td>
                    <td style="border-top: none !important; border-left: 0px solid #000000; border-right: 1px solid #000000; border-bottom:none !important; padding:2px;" colspan="6">:
                        <strong>{{ $displayOldName }}</strong>
                    </td>
                </tr>
                <tr style="background-color: #c6dcf5;">
                    <td style="border-top: none !important; border-left: 1px solid #000000; border-right: 0px solid #000000; border-bottom:none !important;  padding:2px;" colspan="1">No. batch</td>
                    <td style="border-top: none !important; border-left: 0px solid #000000; border-right: 1px solid #000000; border-bottom:1px solid #000000;  padding:2px;" colspan="6">:
                        <strong>{{ $displayOldBatch }}</strong>
                    </td>
                </tr>
                <tr>
                    <th rowspan="2"
                        style="border:1px solid #000000; padding: 0px 6px 0 6px !important; text-align: center; width: 20%;">
                        Bagian yang Dibersihkan</th>
                    <th colspan="3" style="border:1px solid #000000; padding: 0px !important; text-align: center;">Hasil
                    </th>
                    <th colspan="2"
                        style="border:1px solid #000000; padding: 0px !important; text-align: center; width: 20%;">
                        Dibersihkan Oleh</th>
                    <th rowspan="2" style="border:1px solid #000000; text-align: center; width: 20%;">Diperiksa Oleh
                    </th>
                </tr>
                <tr>
                    <th style="border:1px solid #000000; text-align:center;">Bersih</th>
                    <th style="border:1px solid #000000; text-align:center;">Kurang Bersih</th>
                    <th style="border:1px solid #000000; text-align:center;">Kotor</th>
                    <th style="border:1px solid #000000; text-align:center;">Nama</th>
                    <th style="border:1px solid #000000; text-align:center;">Paraf</th>
                </tr>
                @foreach($items as $item)
                <tr>
                    <td style="padding:2px; border:1px solid #000000;">{{ $item->bagianKebersihan->nama_bagian }}</td>
                    <td style="text-align:center; padding:2px; border:1px solid #000000;">{{ $item->hasil === 'Bersih' ? 'V' : '' }}</td>
                    <td style="text-align:center; padding:2px; border:1px solid #000000;">{{ $item->hasil === 'Kurang Bersih' ? 'V' : '' }}</td>
                    <td style="text-align:center; padding:2px; border:1px solid #000000;">{{ $item->hasil === 'Kotor' ? 'V' : '' }}</td>
                    <td style="padding:2px; border:1px solid #000000;">{{ $item->dibersihkan_oleh }}</td>
                    <td style="padding:2px; border:1px solid #000000;"></td>
                    <td style="padding:2px; border:1px solid #000000;">{{ $item->diperiksa_oleh ?? '-' }}</td>
                </tr>
                @endforeach
            </table>
            @endforeach

            <table style="margin-top: 20px; width:88%;">
                <tr>
                    <td style="border:none !important; text-align:center;">
                        <p style="margin:0px; padding:0px">Mengetahui</p>
                        <div class="signature-space"></div>
                        <p style="margin:0px; padding:0px">Manager Produksi</p>
                    </td>
                    <td style="border:none !important; text-align:center;">
                        <p style="margin:0px; padding:0px">Diluluskan tgl</p>
                        <div class="signature-space"></div>
                        <p style="margin:0px; padding:0px">Manager QC</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- section ketiga -->
    {{-- === Divisi Penimbangan === --}}
    <div class="print-page">
        <h1>CATATAN PEMBUATAN BATCH</h1>
        <h1>{{$produksi->product->brand->name}}</h1>
        <p style="font-weight:600; text-align:center; padding: 0px !important; margin: 0px !important;">
            Persiapan Bahan Kemas dan Bahan Baku
        </p>
        <table style="width:100%; margin: 0px 0px 15px 3px !important;" class="table-borderless">
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Nama Produk</td>
                <td style="padding:2px 0;">: {{ $cleanname }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Kode Produk</td>
                <td style="padding:2px 0;">: {{ $produksi->product->code }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Batch Number</td>
                <td style="padding:2px 0;">: {{ $produksi->batch_number }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Tanggal Produksi</td>
                <td style="padding:2px 0;">: {{ $produksi->tanggal_produksi->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Tanggal Kadaluarsa</td>
                <td style="padding:2px 0;">: {{ $produksi->tanggal_expired }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Batch Size</td>
                <td style="padding:2px 0;">: {{ $produksi->batch_size }} Kg</td>
            </tr>
        </table>

        <div class="section">
            <div class="sub-title-general">
                <h2>Persiapan Bahan Kemas</h2>
            </div>
            <div style="overflow-x:auto; width:100%;">
                <table style="margin:0px 0px 15px 0px !important; min-width:600px; width:100%;">
                    <tr>
                        <th style="border:1px solid #000000; text-align:center;">Nama Produk</th>
                        @php
                        $getDynamicFontSize = function($text) {
                            $len = strlen($text);
                            if ($len > 40) return '6pt';
                            if ($len > 25) return '7pt';
                            if ($len > 15) return '8pt';
                            return '11pt';
                        };
                        @endphp

                        @if($produksi->is_custom_formula === 1)
                        @foreach($kemasFinal as $item)
                        @php $fontSize = $getDynamicFontSize($item['name'] ?? ''); @endphp
                        <th style="border:1px solid #000000; text-align:center; font-size: {{ $fontSize }};">
                            {{ $item['name'] ?? 'Tidak ditemukan' }}
                        </th>
                        @endforeach
                        @else
                        @foreach($bahanKemas as $kemas)
                        @php $fontSize = $getDynamicFontSize($kemas->name ?? ''); @endphp
                        <th style="border:1px solid #000000; text-align:center; font-size: {{ $fontSize }};">{{$kemas->name}}</th>
                        @endforeach
                        @endif
                        <th style="border:1px solid #000000; text-align:center;">Operator</th>
                        <th style="border:1px solid #000000; text-align:center;">Gudang</th>
                    </tr>
                    <tr>
                        <td style="border:1px solid #000000; text-align:center;">{{$cleanname}}</td>
                        @if($produksi->is_custom_formula === 1)
                        @foreach($kemasFinal as $item)
                        <td style="border:1px solid #000000; text-align:center;">
                            {{ $item['jumlah'] }}
                        </td>
                        @endforeach
                        @else
                        @foreach($bahanKemas as $kemas)
                        @php
                        $jumlahKemas = ceil(($kemas->jumlah / 100) * $produksi->batch_size);
                        @endphp
                        <td style="border:1px solid #000000; text-align:center; white-space:nowrap;">
                            @if($produksi->batch_size === '100')
                            {{$kemas->jumlah}}
                            @else
                            {{$jumlahKemas}}
                            @endif
                        </td>
                        @endforeach
                        @endif
                        <td style="border:1px solid #000000; text-align:center;"></td>
                        <td style="border:1px solid #000000; text-align:center;"></td>
                    </tr>
                </table>
            </div>
            <div class="sub-title-general">
                <h2>penimbangan Bahan Baku</h2>
            </div>
            <table width="100%" cellspacing="0" style="font-size:12px; margin-bottom:0px !important;">
                <thead>
                    <tr>
                        <th style="text-align:center; border:1px solid #000000; width:auto; white-space:nowarp;">Nama Bahan Baku</th>
                        <th style="text-align:center; border:1px solid #000000">No Batch Bahan Baku</th>
                        <th style="text-align:center; border:1px solid #000000">Takaran (gram)</th>
                        <th style="text-align:center; border:1px solid #000000">Hasil Timbang (gram)</th>
                        <th style="text-align:center; border:1px solid #000000">Selisih (gram)</th>
                        <th style="text-align:center; border:1px solid #000000">Operator Penimbangan</th>
                        <th style="text-align:center; border:1px solid #000000">SPV Produksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penimbangan as $item)
                    @php
                    // Ambil jumlah acuan
                    $isCustom = $produksi->is_custom_formula ?? false;
                    $batchSize = $produksi->batch_size ?? 100;

                    if ($isCustom) {
                    // Ambil dari custom formula JSON
                    $custom = collect($produksi->custom_bahan_baku)
                    ->firstWhere('id', $item->bahan_baku_id); // atau sesuaikan dengan struktur field-nya
                    $acuan = $custom['jumlah'] ?? 0;
                    } else {
                    // Ambil dari formula default
                    $jumlahPer100kg = $item->bahanbaku->jumlah ?? 0;
                    $acuan = ($jumlahPer100kg / 100) * $batchSize;
                    }

                    $acuanFormatted = fmod($acuan, 1) == 0
                    ? number_format($acuan, 0, ',', '.')
                    : number_format($acuan, 3, ',', '.');

                    $hasilFormatted = fmod($item->hasil_timbang, 1) == 0
                    ? number_format($item->hasil_timbang, 0, ',', '.')
                    : number_format($item->hasil_timbang, 3, ',', '.');
                    @endphp
                    <tr>
                        <td style="border:1px solid #000000; width:auto; white-space:nowarp;">{{ $item->bahanBaku->name ?? '-' }}</td>
                        <td style="border:1px solid #000000;">{{$item->no_batch}}</td> 
                        <td style="border:1px solid #000000; text-align: right;">{{ $acuanFormatted }}</td>
                        <td style="border:1px solid #000000; text-align: right;">{{ $hasilFormatted }}</td>
                        <td style="border:1px solid #000000;">{{$selisish ?? "-"}}</td>
                        <td style="border:1px solid #000000;">{{ $item->operator_penimbangan }}</td>
                        <td style="border:1px solid #000000;"></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">
                            Tidak ada data penimbangan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <table style="width:88%;">
                <tr>
                    <td style="border:none !important; text-align:center; vertical-align: top; padding:0px !important;">
                        <p style="margin:0px; padding:0px;">Mengetahui</p>
                        <div class="signature-space"></div>
                        <p style="margin:0px; padding:0px">Manager Produksi</p>
                    </td>
                    <td style="border:none !important; text-align:center; vertical-align: top; padding:0px !important">
                        <p style="margin:0px; padding:0px;">Diluluskan tgl</p>
                        <div class="signature-space"></div>
                        <p style="margin:0px; padding:0px">Manager QC</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>


    <!-- section ke 4 -->
    {{-- === Divisi Proses Produksi === --}}
    <div class="print-page" style="padding: 0px !important;">
        <h1>CATATAN PEMBUATAN BATCH</h1>
        <h1>{{$produksi->product->brand->name}}</h1>
        <p style="font-weight:600; text-align:center; margin: 0px !important;">
            Pengolahan Batch dan Pemeriksaan QC
        </p>
        <table style="width:100%; padding: 0px 0px 15px 3px !important;" class="table-borderless">
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Nama Produk</td>
                <td style="padding:2px 0;">: {{ $cleanname }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Kode Produk</td>
                <td style="padding:2px 0;">: {{ $produksi->product->code }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Batch Number</td>
                <td style="padding:2px 0;">: {{ $produksi->batch_number }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Tanggal Produksi</td>
                <td style="padding:2px 0;">: {{ $produksi->tanggal_produksi->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Tanggal Kadaluarsa</td>
                <td style="padding:2px 0;">: {{ $produksi->tanggal_expired }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Batch Size</td>
                <td style="padding:2px 0;">: {{ $produksi->batch_size }} Kg</td>
            </tr>
        </table>
        <div class="section">
            <div class="sub-title-general">
                <h2>proses pengolahan</h2>
            </div>
            <table style="margin-bottom:15px; width:100%; font-size: 10px; margin-top:0px !important;">
                <thead>
                    <tr>
                        <th rowspan="2" style="border:1px solid #000000;text-align: center; width:50%; font-family:Calibry;">Tahapan Proses</th>
                        <th rowspan="2" style="border:1px solid #000000;text-align: center;">Actual</th>
                        <th colspan="2" style="border:1px solid #000000;text-align: center; width:20%;">Paraf Dan Tanggal</th>
                    </tr>
                    <tr>
                        <th style="border:1px solid #000000;text-align: center;">Operator</th>
                        <th style="border:1px solid #000000;text-align: center;">SPV Produksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tahapanProses as $item)
                    <tr>
                        <td style="padding:1px; border:1px solid #000000;">
                            {{ $loop->iteration }}. {{ $item->tapahanProses->nama_proses ?? '-' }}
                        </td>
                        <td style="padding:3px; border:1px solid #000000;">{{ $item->keterangan }}</td>
                        <td style="padding:3px; border:1px solid #000000;">{{ $item->penanggung_jawab }}</td>
                        <td style="padding:3px; border:1px solid #000000;"></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            Tidak ada data proses pengolahan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="section">
            <div class="sub-title-general">
                <h2>Pemeriksaan Quality Control</h2>
            </div>
            <table style="margin-bottom:15px; width:100%; font-size: 10px; margin-top:0px !important;">
                <thead>
                    <tr>
                        <th style="border:1px solid #000000;width:11%; text-align:center;" rowspan=2>Parameter Pengujian</th>
                        <th style="border:1px solid #000000;width:20%; text-align:center;" rowspan=2>Nilai Standart</th>
                        <th style="border:1px solid #000000;width:10%; text-align:center;" rowspan=2>Nilai Real</th>
                        <th style="border:1px solid #000000;text-align:center;" colspan="2">Paraf dan Tanggal</th>
                    </tr>
                    <tr>
                        <th style="border:1px solid #000000;width:7%; padding:0px !important; text-align:center;">QC</th>
                        <th style="border:1px solid #000000;width:7%; padding:0px !important; text-align:center;">SPV Produksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($qualityControl as $item)
                    @php
                        $namaParameter = strtolower($item->qualityControl->parameter_pengujian);

                        $spesifikasi = $item->qualityControl->nilai_standart;
                        if ($spesifikasi === 'otomatis') {
                            if ($item->qualityControl->parameter_pengujian === 'Volume') {
                                $spesifikasi = $produksi->product->ukuran;
                            }
                        }

                    @endphp

                    @if(Str::contains($namaParameter, 'uji ddh'))
                        @continue
                    @endif
                    <tr>

                        <td style="border:1px solid #000000; margin:0px; padding:1px;">
                            {{ $item->qualityControl->parameter_pengujian ?? null }}
                        </td>
                        <td
                            style="border:1px solid #000000; text-align:center; font-family: 'DejaVu Sans', sans-serif; margin:0px; padding:1px;">
                            {{ $spesifikasi }}
                        </td>
                        <td style="border:1px solid #000000; padding:1px; text-align:center;">{{ $item->nilai_real }}</td>
                        <td style="border:1px solid #000000; padding:1px; text-align:center;">{{ $item->petugas_qc ?? 'Belum Di Periksa' }}</td>
                        <td style="border:1px solid #000000; padding:1px; text-align:center;"></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            Tidak ada pemeriksaan quality control
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="section">
            <div class="sub-title-general">
                <h2>Rekonsiliasi Bahan Kemas</h2>
            </div>

            <table width="100%" border="1" cellspacing="0" cellpadding="4" style="font-size: 10px; margin-top: 0px;">
                <thead>
                    <tr>
                        <th style="border:1px solid #000000; width:30%; text-align:center;">Nama Bahan Kemas</th>
                        <th style="border:1px solid #000000; width:13%; text-align:center;">Tersedia</th>
                        <th style="border:1px solid #000000; width:13%; text-align:center;">Terpakai</th>
                        <th style="border:1px solid #000000; width:10%; text-align:center;">Keterangan</th>
                        <th style="border:1px solid #000000; text-align:center;">Operator</th>
                        <th style="border:1px solid #000000; text-align:center;">Gudang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekonsiliasi as $item)
                        <tr>
                            <td style="padding:3px; border:1px solid #000000;">{{ optional($item->bahankemas)->name ?? $item->nama_barang ?? 'Tidak ditemukan' }}</td>
                            <td style="padding:3px; text-align:center; border:1px solid #000000">{{ optional($item->bahankemas)->jumlah ? (int) optional($item->bahankemas)->jumlah : ($item->tersedia ?? 0) }} pcs</td>
                            <td style="padding:3px; text-align:center; border:1px solid #000000;">{{ $item->terpakai ?? 0 }}</td>
                            <td style="padding:3px; border:1px solid #000000; width:10%;">{{ $item->keterangan ?? 'tidak ada' }}</td>
                            <td style="padding:3px; border:1px solid #000000;">{{ $item->operator ?? '-' }}</td>
                            <td style="padding:3px; border:1px solid #000000;"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <table style="width:88%; margin-top: 15px;">
            <tr>
                <td style="border:none !important; text-align:center; vertical-align: top; padding:0px !important;">
                    <p style="margin:0px; padding:0px;">Mengetahui</p>
                    <div class="signature-space"></div>
                    <p style="margin:0px; padding:0px">Manager Produksi</p>
                </td>
                <td style="border:none !important; text-align:center; vertical-align: top; padding:0px !important">
                    <p style="margin:0px; padding:0px;">Diluluskan tgl</p>
                    <div class="signature-space"></div>
                    <p style="margin:0px; padding:0px">Manager QC</p>
                </td>
            </tr>
        </table>
    </div>
    <div class="print-page">
        <h1>CATATAN PEMBUATAN BATCH</h1>
        <h1>{{$produksi->product->brand->name}}</h1>
        <p style="font-weight:600; text-align:center; padding:0px !important; margin: 0px !important;">Serah Terima Produk Jadi</p>
        <table style="width:100%; margin:0px 0px 15px 3px !important;" class="table-borderless">
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Nama Produk</td>
                <td style="padding:2px 0;">: {{ $cleanname }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Kode Produk</td>
                <td style="padding:2px 0;">: {{ $produksi->product->code }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Batch Number</td>
                <td style="padding:2px 0;">: {{ $produksi->batch_number }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Tanggal Produksi</td>
                <td style="padding:2px 0;">: {{ $produksi->tanggal_produksi->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Tanggal Kadaluarsa</td>
                <td style="padding:2px 0;">: {{ $produksi->tanggal_expired }}</td>
            </tr>
            <tr>
                <td style="width: 20%; padding:2px 8px 2px 0;">Batch Size</td>
                <td style="padding:2px 0;">: {{ $produksi->batch_size }} Kg</td>
            </tr>
        </table>
        <div class="section">
            <div class="sub-title-general">
                <h2>Serah Terima Produk Jadi</h2>
            </div>
            <table style="margin-bottom:15px; font-size:12px;">
                <tr>
                    <th style="text-align:center; border:1px solid #000000;" rowspan="2">Produk</th>
                    <th style="text-align:center; border:1px solid #000000;" rowspan="2">Kode</th>
                    <th style="text-align:center; border:1px solid #000000;" rowspan="2">Isi</th>
                    <th style="text-align:center; border:1px solid #000000;" rowspan="2">Hasil Per Batch (Botol/Jeringen)</th>
                    <th style="text-align:center; border:1px solid #000000; width:10%;" rowspan="2">Hasil Real Per Batch</th>
                    <th style="text-align:center; border:1px solid #000000;" colspan="2">Paraf dan Tanggal</th>
                </tr>
                <tr>
                    <th style="text-align:center; border:1px solid #000000;">Produksi</th>
                    <th style="text-align:center; border:1px solid #000000;">Gudang</th>
                </tr>
                <tr>
                    @php
                        // Ambil name dan tipe
                        $name = $produksi->product->name;

                        preg_match('/\d+\s*[a-zA-Z]+/', $name, $matchUkuran);
                        $ukuran = $matchUkuran[0] ?? '';
                        
                        $nameWithoutUkuran = preg_replace('/\s*\([^)]*\)/', '', $name);
                        $cleanname = trim(str_replace($ukuran, '', $nameWithoutUkuran));

                        if (str_contains($cleanname, 'KOMITKAMI NBF')) {
                            $cleanname = str_replace('KOMITKAMI', 'KING', $cleanname);
                        }

                        // Format tipe dan ukuran jadi "Botol - 100ML"
                        $kemasan = ($ukuran ? '  ' . strtoupper($ukuran) : '');
                    @endphp
                    <td style="border:1px solid #000000; width: auto; white-space:nowrap;">{{ $cleanname ?? 'Belum tersedia'}}</td>
                    <td style="border:1px solid #000000; width: auto; white-space:nowrap;">{{ $produksi->product->code ?? 'Belum tersedia.' }}</td>
                    <td style="text-align:center; border:1px solid #000000; width:auto; white-space:nowrap;">{{ $kemasan }}</td>
                    <td style="text-align:center; border:1px solid #000000; width:auto; white-space:nowrap;">{{ $hasilAcuan->acuan ?? "data belum di isi" }}</td>
                    <td style="text-align:center; border:1px solid #000000; width:auto; white-space:nowrap;">{{ $hasilproduksi->nilai_actual ?? 'Belum tersedia.' }}</td>
                    <td style="text-align:center; border:1px solid #000000; width:auto; white-space:nowrap;"></td>
                    <td style="text-align:center; border:1px solid #000000; width:auto; white-space:nowrap;"></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="sub-title-general">
                <h2>RELEASE PRODUK JADI</h2>
            </div>
            <table style="font-size: 12px; margin-top:0px !important;">
                <tr>
                    <td style="text-align:center; border:1px solid #000000;">Kabag. Produksi</td>
                    <td style="text-align:center; border:1px solid #000000;">Kabag. QC</td>
                    <td style="text-align:center; border:1px solid #000000;">Kabag. QA</td>
                </tr>
                <tr>
                    <td style="height:5rem; border: 1px solid #000000;"></td>
                    <td style="height:5rem; border: 1px solid #000000;"></td>
                    <td style="height:5rem; border: 1px solid #000000;"></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>