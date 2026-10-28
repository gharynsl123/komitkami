<!DOCTYPE html>
<html>
<head>
    <title>Laporan Opname</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #f2f2f2; }
        .text-left { text-align: left; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="10" style="background-color: #2563eb; color: #ffffff; text-align: center; font-size: 16px; font-weight: bold; padding: 10px; border: 1px solid #000;">LAPORAN STOCK OPNAME</th>
            </tr>
            <tr>
                <th colspan="10" style="background-color: #3b82f6; color: #ffffff; text-align: center; font-size: 14px; font-weight: bold; padding: 8px; border: 1px solid #000;">TIPE: BAHAN BAKU</th>
            </tr>
            <tr>
                <th colspan="10" style="background-color: #eff6ff; text-align: left; font-size: 12px; font-weight: bold; padding: 8px; border: 1px solid #000;">Tanggal Pelaksanaan: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</th>
            </tr>
            <tr>
                <th style="background-color: #1e40af; color: white; font-weight: bold; text-align: center; border: 1px solid #000;">No</th>
                <th style="background-color: #1e40af; color: white; font-weight: bold; text-align: center; border: 1px solid #000;">Kode</th>
                <th style="background-color: #1e40af; color: white; font-weight: bold; text-align: center; border: 1px solid #000;">Nama Barang</th>
                <th style="background-color: #1e40af; color: white; font-weight: bold; text-align: center; border: 1px solid #000;">No. Batch</th>
                <th style="background-color: #1e40af; color: white; font-weight: bold; text-align: center; border: 1px solid #000;">Tgl. ED</th>
                <th style="background-color: #1e40af; color: white; font-weight: bold; text-align: center; border: 1px solid #000;">Stok Sistem</th>
                <th style="background-color: #1e40af; color: white; font-weight: bold; text-align: center; border: 1px solid #000;">Stok Fisik</th>
                <th style="background-color: #1e40af; color: white; font-weight: bold; text-align: center; border: 1px solid #000;">Selisih</th>
                <th style="background-color: #1e40af; color: white; font-weight: bold; text-align: center; border: 1px solid #000;">Keterangan</th>
                <th style="background-color: #1e40af; color: white; font-weight: bold; text-align: center; border: 1px solid #000;">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->inventory->code ?? '-' }}</td>
                <td class="text-left">{{ $row->inventory->name ?? '-' }}</td>
                <td>{{ $row->stockIn->nomor_batch ?? 'N/A' }}</td>
                <td>{{ $row->stockIn->tanggal_ed ? \Carbon\Carbon::parse($row->stockIn->tanggal_ed)->format('d/m/Y') : 'N/A' }}</td>
                <td>{{ formatJumlah($row->stok_sistem) }}</td>
                <td>{{ formatJumlah($row->stok_fisik) }}</td>
                <td style="color: {{ $row->selisih > 0 ? 'green' : ($row->selisih < 0 ? 'red' : 'black') }}">
                    {{ $row->selisih > 0 ? '+' : '' }}{{ formatJumlah($row->selisih) }}
                </td>
                <td class="text-left">{{ $row->keterangan ?: '-' }}</td>
                <td>{{ $row->user->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
