<table>
    {{-- Row 1: Title --}}
    <tr>
        <td colspan="9"><strong>CATATAN PRODUKSI</strong></td>
    </tr>

    {{-- Row 2: Column Headers --}}
    <tr>
        <th>NAMA PRODUK</th>
        <th>NO. BATCH PRODUK</th>
        <th>KODE PRODUK</th>
        <th>QTY HASIL PRODUKSI</th>
        <th>SATUAN</th>
        <th>TANGGAL PRODUKSI</th>
        <th>TANGGAL KADALUARSA</th>
        <th>KAPASITAS PRODUKSI</th>
        <th>KETERANGAN</th>
    </tr>

    {{-- Data Rows --}}
    @foreach($produksiList as $produksi)
    @php
        // Kumpulkan bahan baku dari formula
        $keterangan = '';
        if ($produksi->bahanBaku && $produksi->bahanBaku->bahanBaku->isNotEmpty()) {
            $items = $produksi->bahanBaku->bahanBaku->map(function ($item) {
                $nama = optional($item->inventory)->name ?? '-';
                $qty  = rtrim(rtrim(number_format((float)$item->jumlah, 3, '.', ''), '0'), '.');
                $sat  = $item->satuan ?? '';
                return "{$nama} {$qty} {$sat}";
            });
            $keterangan = 'Formula: ' . $produksi->bahanBaku->nama_formula;
        }

        // Nilai barang jadi (qty hasil produksi)
        $qtyHasil  = optional($produksi->produkJadi)->nilai_actual ?? '-';
        $satuan    = optional($produksi->product)->ukuran ?? '-';
        $tglProd   = $produksi->tanggal_produksi ? \Carbon\Carbon::parse($produksi->tanggal_produksi)->format('d/m/Y') : '-';
        $tglKadal  = $produksi->tanggal_expired ? \Carbon\Carbon::parse($produksi->tanggal_expired)->format('m/Y') : '-';
    @endphp
    <tr>
        <td>{{ optional($produksi->product)->name ?? '-' }}</td>
        <td>{{ $produksi->batch_number ?? '-' }}</td>
        <td>{{ optional($produksi->product)->code ?? '-' }}</td>
        <td>{{ $qtyHasil }}</td>
        <td>{{ $satuan }}</td>
        <td>{{ $tglProd }}</td>
        <td>{{ $tglKadal }}</td>
        <td>{{ $produksi->batch_size ?? '-' }}</td>
        <td>{{ $keterangan }}</td>
    </tr>
    @endforeach
</table>

