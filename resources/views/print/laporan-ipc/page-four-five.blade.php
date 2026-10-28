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
        padding: 0px 5px 0px 5px !important;
    }

    .center>td,
    .center>th {
        text-align: center;
        vertical-align: middle;
    }
</style>

<body>

    <section style="page-break-after: always;">
        <img src="{{ asset('images/KING-DATAR.png') }}" style="width: 350px;" alt="Logo KomitKami">

        <div style="border: 1px solid #000000;">
            <h3 style="margin-top: 15px; margin-bottom: 20px !important;">FORM PENGAMBILAN SAMPEL PRODUK JADI</h3>
            <p style="margin-left: 3px; margin-bottom: 5px !important;">Nomor Dokumen : KIG-QC-F-08-01-01</p>
            @php
                $name = $pengambilanSample->produksi->product->name;
                $tipe = $pengambilanSample->produksi->product->tipe;

                preg_match('/\d+\s*[a-zA-Z]+/', $name, $matchUkuran);
                $ukuran = $matchUkuran[0] ?? '';

                $cleanName = preg_replace('/\s*\([^)]*\)/', '', $name);
                $cleanName = trim($cleanName);

                // format kemasan
                $kemasan = ucfirst($tipe) . ($ukuran ? ' ' . strtoupper($ukuran) : '');
            @endphp
            <table>
                <tr>
                    <td class="value" style="width:28%;">Tanggal Sampling</td>
                    <td class="colon" style="width:2%;">:</td>
                    <td class="value">
                        {{ \Carbon\Carbon::parse($pengambilanSample->tanggal_sampling)->format('d-m-Y') ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="value">Nama Produk Jadi</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $cleanName ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Ukuran Produk Jadi</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pengambilanSample->produksi->product->ukuran ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">No Batch Produk Jadi</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pengambilanSample->produksi->batch_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Jumlah Sample diambil</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pengambilanSample->jumlah_sample_diambil ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Peralatan Sampling</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pengambilanSample->peralatan_sampling ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Lokasi Sampling</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pengambilanSample->lokasi_sampling ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Catatan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pengambilanSample->catatan ?? '-' }}</td>
                </tr>
            </table>
        </div>
        <table style="margin-top: 35px; width:100%;">
            <tr>
                <td>
                    Disampling Oleh : <br><br><br><br>
                </td>
                <td>
                    Diperiksa Oleh : <br><br><br><br>
                </td>
            </tr>
            <tr>
                <td>
                    Tanggal : {{ \Carbon\Carbon::parse($pengambilanSample->tanggal_sampling)->format('d-m-Y') ?? '-' }}
                </td>
                <td>
                    Tanggal : {{ \Carbon\Carbon::parse($pengambilanSample->tanggal_sampling)->format('d-m-Y') ?? '-' }}
                </td>
            </tr>
        </table>
    </section>

    <section>
        <img src="{{ asset('images/KING-DATAR.png') }}" style="width: 350px;" alt="Logo KomitKami">
        <div style="border: 1px solid #000000;">
            <h3 style="margin-top: 15px; margin-bottom: 20px !important;">FORM PENGUJIAN PRODUK JADI</h3>
            <p style="margin-left: 3px; margin-bottom: 5px !important;">Nomor Dokumen : KIG-QC-F-01-03-01</p>
            <table>
                <tr>
                    <td class="value" style="width:28%;">Bagian</td>
                    <td class="colon" style="width:2%;">:</td>
                    <td class="value">{{ $pengujianProdukJadi->bagian ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Tanggal</td>
                    <td class="colon">:</td>
                    <td class="value">{{ \Carbon\Carbon::parse($pengujianProdukJadi->tanggal)->format('d-m-Y') ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="value">Nama Produk</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $cleanName ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="value">Nomor Batch</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $pengujianProdukJadi->produksi->batch_number ?? '-' }}</td>
                </tr>
            </table>
            <table>
                <thead>
                    <tr class="center padding">
                        <td style="font-weight: bold;">Pemeriksaan</td>
                        <td style="font-weight: bold;">Spesifikasi</td>
                        <td style="font-weight: bold;">Hasil</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ppj as $items)
                        @php
                            $acuan = $items->acuanLaporanIpc;
                            $pemeriksaan = $acuan->pemeriksaan ?? '';
                            $spesifikasi = $acuan->spesifikasi ?? '';
                            $hasil = $items->hasil;

                            // PRIORITAS 1: handle "otomatis" dulu
                            if ($spesifikasi === 'otomatis') {
                                if (strtolower($pemeriksaan) === 'jumlah') {
                                    $spesifikasi = $produksi->product->produkjadi->acuan ?? 100;
                                } elseif (strtolower($pemeriksaan) === 'volume') {
                                    $spesifikasi = $produksi->product->ukuran ?? '-';
                                }
                            } 
                            
                            if ($produksi->batch_size != 100 && strtolower($pemeriksaan) === 'jumlah') {
                                $angka = (int) preg_replace('/[^0-9]/', '', $spesifikasi);
                                $spesifikasi = ceil(($angka / 100) * $produksi->batch_size);
                            }

                            if (strtolower($pemeriksaan) === 'jumlah') {
                                $nilaiActual = $barangJadi->nilai_actual ?? 'Belum Diisi';
                                $tipeProduct = $produksi->product->tipe ?? '';
                                $hasil = trim($nilaiActual . ' ' . $tipeProduct);
                            }
                        @endphp

                        <tr class="padding">
                            <td>{{ $pemeriksaan }}</td>

                            <td style="font-family: 'DejaVu Sans', sans-serif; text-align: center;">
                                {{ $spesifikasi }}
                            </td>

                            <td style="font-family: 'DejaVu Sans', sans-serif; text-align: center;">
                                {{ $hasil ?? 'Belum Diisi' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top:12px; margin-left:5px;">
                <p>Kesimpulan :
                    <span
                        style="display:inline-block; width:17px; height:17px; border:1px solid #000; text-align:center; vertical-align:middle;">
                        {{ $pengujianProdukJadi->kesimpulan == 'OK' ? 'V' : '' }}
                    </span> Memenuhi Syarat

                    <span
                        style="display:inline-block; width:17px; height:17px; border:1px solid #000; text-align:center; vertical-align:middle; margin-left:15px;">
                        {{ $pengujianProdukJadi->kesimpulan == 'NO OK' ? 'V' : '' }}
                    </span> Tidak Memenuhi Syarat
                </p>
            </div>

            <div style="margin-top:60px; margin-left:5px;">
                <p>Petugas QC : {{$pengujianProdukJadi->petugas_qc}}</p>
            </div>
        </div>
    </section>
</body>

</html>