<!DOCTYPE html>
<html>
<head>
    <title>Form Pemantauan Stabilitas Produk Jadi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 4px;
            border: 1px solid #000;
        }
        .info-table td.label {
            width: 150px;
            font-weight: bold;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 5px;
        }
        .text-left {
            text-align: left !important;
        }
        .signature-cell {
            text-align: left;
            vertical-align: top;
            height: 60px;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="20%">
                <img src="{{ public_asset('images/KING-DATAR.png') }}" alt="Logo" style="height: 50px;">
            </td>
            <td width="80%" class="title">
                FORM PEMANTAUAN STABILITAS PRODUK JADI
            </td>
        </tr>
    </table>

    <div style="margin-bottom: 5px;">No Dokumen : KIG-QC-F-07-03-00</div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Produk</td>
            <td>: {{ $stabilitas->produksi->product->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">No Batch</td>
            <td>: {{ $stabilitas->produksi->batch_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Produksi</td>
            <td>: {{ optional($stabilitas->produksi->tanggal_produksi)->format('d-m-Y') ?? '-' }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" width="12%">Parameter Uji</th>
                <th colspan="9">Waktu</th>
                <th rowspan="2" width="8%">Keterangan</th>
            </tr>
            <tr>
                @php
                    $months = [3, 6, 9, 12, 15, 18, 21, 24];
                    $tgl0 = $pemeriksaan_qc->first() ? $pemeriksaan_qc->first()->created_at->format('d/m/Y') : '-';
                @endphp
                <th>Bulan Ke-<br>0<br><span style="font-weight: normal; font-size: 9px;">Tanggal : {{ $tgl0 }}</span></th>
                @foreach($months as $m)
                    @php
                        $det = $detail_stabilitas->where('bulan_ke', $m)->first();
                        $tgl = ($det && $det->status == 'selesai') ? $det->updated_at->format('d/m/Y') : '-';
                    @endphp
                    <th>Bulan Ke-<br>{{ $m }}<br><span style="font-weight: normal; font-size: 9px;">Tanggal : {{ $tgl }}</span></th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($acuan_quality as $qc)
                <tr>
                    <td class="text-left">{{ $qc->parameter_pengujian }}</td>
                    
                    <!-- Bulan 0 -->
                    @php
                        $val0 = $pemeriksaan_qc->where('quality_id', $qc->id)->first();
                        if($val0 && strtolower($val0->nilai_real) == strtolower('sesuai')) {
                            $val0->nilai_real = $acuan_quality->where('id', $qc->id)->first()->nilai_standart;
                        }
                    @endphp
                    <td>{{ $val0 ? $val0->nilai_real : '' }}</td>
                    
                    <!-- Bulan 3-24 -->
                    @foreach($months as $m)
                        @php
                            $det = $detail_stabilitas->where('bulan_ke', $m)->first();
                            $hasil = $det ? $det->hasil->where('quality_control_id', $qc->id)->first() : null;
                            if($hasil && strtolower($hasil->nilai_real) == strtolower('sesuai')) {
                                $hasil->nilai_real = $acuan_quality->where('id', $qc->id)->first()->nilai_standart;
                            }
                        @endphp
                        <td>{{ $hasil ? $hasil->nilai_real : '' }}</td>
                    @endforeach
                    
                    <!-- Keterangan -->
                    <td></td>
                </tr>
            @endforeach

            <!-- Signature Row -->
            <tr>
                <td></td>
                
                <!-- Bulan 0 -->
                <td class="signature-cell">
                    Pelaksana :<br>{{ $pemeriksaan_qc->first()->petugas_qc ?? '-' }}
                    <br><br><br>
                    Mengetahui : <br> &nbsp; 
                </td>
                
                <!-- Bulan 3-24 -->
                @foreach($months as $m)
                    @php
                        $det = $detail_stabilitas->where('bulan_ke', $m)->first();
                        $pelaksana = ($det && $det->status == 'selesai') ? $det->petugas_qc : '-';
                    @endphp
                    <td class="signature-cell">
                        Pelaksana :<br>{{ $pelaksana }}
                        <br><br><br>
                        Mengetahui :
                    </td>
                @endforeach
                
                <td></td>
            </tr>
        </tbody>
    </table>

</body>
</html>