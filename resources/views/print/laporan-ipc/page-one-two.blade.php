<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    * {
        font-family: sans-serif;
        margin: 0;
    }

    body {
        font-size: 10pt;
        padding: 5mm 10mm 0px 10mm;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td,
    th {
        padding: 5px 0px 10px 5px !important;
        /* jarak isi sel */
        margin: 0;
        /* margin default = 0 */
        border: 1px solid #000;
        vertical-align: top;
    }


    h3 {
        text-align: center;
        margin-bottom: 15px;
    }

    td.colon {
        border-right: none;
        border-left: none;
    }

    td.value {
        border-right: none;
        border-left: none;
    }

    .padding>td,
    .padding>th {
        padding: 5px !important;
    }

    .center>td,
    .center>th {
        text-align: center;
        vertical-align: middle;
    }
</style>

<body>
    <!-- ONE -->
    <section style="page-break-after: always;">
        <img src="{{ asset('images/KING-DATAR.png') }}" style="width: 350px;" alt="Logo KomitKami">
        <div style="border: 1px solid #000000;">
            <h3 style="margin-top: 15px; margin-bottom: 20px !important;">FORM PENGAMBILAN SAMPEL IN PROSES CONTROL</h3>
            <p style="margin-left: 3px; margin-bottom: 5px !important;">Nomor Dokumen : KIG-QC-F-05-01-01</p>
            @php
                $name = $sampleIPC->produksi->product->name;
                $tipe = $produksi->product->tipe;

                // ambil ukuran (opsional, kalau masih dipakai di kemasan)
                preg_match('/\d+\s*[a-zA-Z]+/', $name, $matchUkuran);
                $ukuran = $matchUkuran[0] ?? '';

                // hapus isi dalam tanda kurung
                $cleanName = preg_replace('/\s*\([^)]*\)/', '', $name);
                $cleanName = trim($cleanName);

                // format kemasan
                $kemasan = ucfirst($tipe) . ($ukuran ? ' ' . strtoupper($ukuran) : '');
            @endphp
            <table>
                <tr>
                    <td class="value" style="width:35%;">Tanggal Sampling</td>
                    <td class="colon" style="width:2%;">:</td>
                    <td class="value">
                        {{ \Carbon\Carbon::parse($sampleIPC->tanggal_sampling)->format('d-m-Y') ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="value">Tahapan Proses</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $sampleIPC->tahapan_proses ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Nama Produk</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $cleanName ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Jenis Produk</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $sampleIPC->produksi->product->jenis ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Kode Produk</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $sampleIPC->produksi->product->code ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Nomor Batch</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $sampleIPC->produksi->batch_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Referensi Metode Sampling</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $sampleIPC->referensi_metode_sampling ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Jumlah Sample Diambil Bagian Atas</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $sampleIPC->jumlah_sample_diambil_bagian_atas ?? '-' }} mL</td>
                </tr>
                <tr>
                    <td class="value">Jumlah Sample Diambil Bagian Bawah</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $sampleIPC->jumlah_sample_diambil_bagian_bawah ?? '-' }} mL</td>
                </tr>
                <tr>
                    <td class="value">Peralatan Sampling</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $sampleIPC->peralatan_sampling ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Lokasi Sampling</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $sampleIPC->lokasi_sampling ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Catatan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $sampleIPC->keterangan ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <table style="margin-left: 75px !important; margin-top: 35px; width:80%;">
            <tr>
                <td>
                    <strong>
                        Disampling Oleh : <br><br><br><br>
                    </strong>
                </td>
                <td>
                    <strong>
                        Diperiksa Oleh : <br><br><br><br>
                    </strong>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>
                        Tanggal : {{ \Carbon\Carbon::parse($sampleIPC->tanggal_sampling)->format('d-m-Y') ?? '-' }}
                    </strong>
                </td>
                <td>
                    <strong>
                        Tanggal : {{ \Carbon\Carbon::parse($sampleIPC->tanggal_sampling)->format('d-m-Y') ?? '-' }}
                    </strong>
                </td>
            </tr>
        </table>
    </section>

    <!-- Two -->
    <section>
        <img src="{{ asset('images/KING-DATAR.png') }}" style="width: 350px;" alt="Logo KomitKami">
        <div style="border: 1px solid #000000;">
            <h3 style="margin-top: 15px; margin-bottom: 20px !important;">INSPEKSI PROSES PRODUKSI</h3>
            <p style="margin-left: 3px; margin-bottom: 5px !important;">Nomor Dokumen : KIG-QC-F-01-02-01</p>
            <table>
                <tr>
                    <td>Nama Produk</td>
                    <td>{{$cleanName}}</td>
                    <td>Besar Bets Produksi</td>
                    <td>{{ $inspeksiData->produksi->batch_size ?? '-' }} KG</td>
                </tr>
                <tr>
                    <td>Jenis Produk</td>
                    <td>{{ $inspeksiData->produksi->product->jenis ?? '-' }}</td>
                    <td>Tanggal Produksi</td>
                    <td>{{ $inspeksiData->produksi->tanggal_produksi->format('d-m-Y') ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td>Kode Produk</td>
                    <td>{{ $inspeksiData->produksi->product->code ?? '-' }}</td>
                    <td>Tanggal Pengujian</td>
                    <td>{{  \Carbon\Carbon::parse($inspeksiData->tanggal)->format('d-m-Y') ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Nomor Bets</td>
                    <td>{{ $inspeksiData->produksi->batch_number ?? '-' }}</td>
                    <td>Jumlah Sample Uji</td>
                    <td>{{ $inspeksiData->jumlah_sampel_uji ?? '-' }}</td>
                </tr>
            </table>
            <p style="font-weight: bold; margin: 10px 5px">In Proces Mixing</p>
            <table border="1" cellspacing="0" cellpadding="6" style="border-collapse: collapse; width:100%;">
                <thead>
                    <tr class="padding">
                        <td rowspan="2" style="width:20%; text-align:center; vertical-align:middle;">Pemeriksaan</td>
                        <td rowspan="2" style="text-align:center; vertical-align:middle;">Spesifikasi</td>
                        <td colspan="2" style="text-align:center; vertical-align:middle;">Hasil</td>
                    </tr>
                    <tr class="padding">
                        <td style="text-align:center; vertical-align:middle;">Inspeksi Bagian Atas</td>
                        <td style="text-align:center; vertical-align:middle;">Inspeksi Bagian Bawah</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $bentuk = $inspeksi->firstWhere('acuanLaporanIpc.pemeriksaan', 'Bentuk');
                        $warna = $inspeksi->firstWhere('acuanLaporanIpc.pemeriksaan', 'Warna');
                        $rasa = $inspeksi->firstWhere('acuanLaporanIpc.pemeriksaan', 'Rasa');
                    @endphp

                    {{-- Bagian Organoleptis --}}
                    <tr class="padding">
                        <td rowspan="1" style="vertical-align:top; text-align:left;">
                            <div> <strong>Organoleptis :</strong></div>
                            <div style="margin-top:6px;">Bentuk</div>
                            <div style="margin-top:6px;">Warna</div>
                            <div style="margin-top:6px;">Rasa</div>
                        </td>

                        <td style="border-bottom:none; text-align:center; vertical-align:center;">
                            <div style="margin-top:2px;">&nbsp;</div>
                            <div style="margin-top:2px;">{{ $bentuk->acuanLaporanIpc->spesifikasi ?? '-' }}</div>
                            <div style="margin-top:6px;">{{ $warna->acuanLaporanIpc->spesifikasi ?? '-' }}</div>
                            <div style="margin-top:6px;">{{ $rasa->acuanLaporanIpc->spesifikasi ?? '-' }}</div>
                        </td>

                        <td style="border-bottom:none; text-align:center; vertical-align:center;">
                            <div style="margin-top:2px;">&nbsp;</div>
                            <div style="margin-top:2px;">{{ $bentuk->inspeksi_bagian_atas ?? '-' }}</div>
                            <div style="margin-top:6px;">{{ $warna->inspeksi_bagian_atas ?? '-' }}</div>
                            <div style="margin-top:6px;">{{ $rasa->inspeksi_bagian_atas ?? '-' }}</div>
                        </td>

                        <td style="border-bottom:none; text-align:center; vertical-align:center;">
                            <div style="margin-top:2px;">&nbsp;</div>
                            <div style="margin-top:2px;">{{ $bentuk->inspeksi_bagian_bawah ?? '-' }}</div>
                            <div style="margin-top:6px;">{{ $warna->inspeksi_bagian_bawah ?? '-' }}</div>
                            <div style="margin-top:6px;">{{ $rasa->inspeksi_bagian_bawah ?? '-' }}</div>
                        </td>
                    </tr>

                    {{-- Sisanya --}}
                    @foreach($inspeksi as $item)
                        @if(!in_array($item->acuanLaporanIpc->pemeriksaan, ['Bentuk', 'Warna', 'Rasa']))
                            <tr class="padding">
                                <td>{{ $item->acuanLaporanIpc->pemeriksaan }}</td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {{ $item->acuanLaporanIpc->spesifikasi }}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $item->inspeksi_bagian_atas }}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $item->inspeksi_bagian_bawah }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 35px; margin-left:5px;">
                <p>Kesimpulan :
                    <span
                        style="display:inline-block; width:17px; height:17px; border:1px solid #000; text-align:center;">
                        {{ $inspeksiData->kesimpulan == 'OK' ? 'V' : '' }}
                    </span> Memenuhi Syarat

                    <span
                        style="display:inline-block; width:17px; height:17px; border:1px solid #000; text-align:center;">
                        {{ $inspeksiData->kesimpulan == 'NOT OK' ? 'V' : '' }}
                    </span> Tidak Memenuhi Syarat
                </p>
            </div>

            <div style="margin:90px 0 5px 5px;">
                <p>Petugas QC : {{$inspeksiData->petugas_qc ?? ''}}</p>
            </div>
        </div>
    </section>
</body>

</html>