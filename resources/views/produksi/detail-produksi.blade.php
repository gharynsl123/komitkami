@extends('layouts.app')
@section('title', 'Komitkami Helper | Detail Produksi')
@section('content')
<style>
.horizontal-scroll {
    overflow-x: auto;
    white-space: nowrap;
}

.card-custom {
    display: inline-block;
    width: auto;
    text-align: center;
    padding: 10px;
    margin-right: 10px;
    border-radius: 8px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}
</style>
<a onclick="history.back()" class="btn btn-secondary">kembali</a>
<div class="card p-3">
    <h3>Detail Data Tiket</h3>

    <!-- Form dimulai dari sini -->
    <form method="POST" action="{{url('/confirm-produksi/'. $produksi['id'])}}">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Batch Number -->
            <div class="col-md-6 mb-3">
                <small for="batch_number">Batch Number</small>
                <div class="input-group-outline input-group">
                    <input type="text" class="form-control" id="batch_number" value="{{ $produksi['batch_number'] }}"
                        name="batch_number">
                </div>
            </div>

            <!-- Batch Size -->
            <div class="col-md-6 mb-3">
                <small for="batch_size">Batch Size</small>
                <div class="input-group-outline input-group">
                    <input type="text" class="form-control" id="batch_size" value="{{ $produksi['batch_size'] }}"
                        readonly name="batch_size">
                </div>
            </div>

            <!-- Tanggal Produksi -->
            <div class="col-md-6 mb-3">
                <small for="tanggal_produksi">Tanggal Produksi</small>
                <div class="input-group-outline input-group">
                    <input type="text" class="form-control" id="tanggal_produksi"
                        value="{{ $produksi['tanggal_produksi']->format('d-m-Y') }}" readonly>
                </div>
            </div>

            <!-- Tanggal Expired -->
            <div class="col-md-6 mb-3">
                <small for="tanggal_expired">Tanggal Expired</small>
                <div class="input-group-outline input-group">
                    <input type="text" class="form-control" id="tanggal_expired"
                        value="{{ $produksi['tanggal_expired'] }}" readonly>
                </div>
            </div>

            <!-- Status -->
            <div class="col-md-6 mb-3">
                <small for="status">Status</small>
                <div class="input-group-outline input-group">
                    <input type="text" class="form-control" id="status" value="{{ $produksi['status'] }}" readonly>
                </div>
            </div>

            <!-- Brand ID -->
            <div class="col-md-6 mb-3">
                <small for="brand_id">Brand</small>
                <div class="input-group-outline input-group">
                    <input type="text" class="form-control" id="brand_id" value="{{ $produksi->product->brand->name }}"
                        readonly>
                </div>
            </div>

            <!-- Invoice ID / Keterangan-->
            @if($produksi->invoice)
            <div class="col-md-6 mb-3">
                <small for="invoice_id">Nomor Pesanan</small>
                <div class="input-group-outline input-group">
                    <input type="text" class="form-control" id="invoice_id"
                        value="{{ $produksi->invoice->nomor_invoice ?? '-'}}" readonly>
                </div>
            </div>
            @else
            <div class="col-md-6 mb-3">
                <small for="invoice_id">Keterangan Tiket Khusus</small>
                <div class="input-group-outline input-group">
                    <input type="text" class="form-control" id="invoice_id" value="{{ $produksi->keterangan ?? '-'}}"
                        readonly>
                </div>
            </div>
            @endif

            <!-- Product ID -->
            <div class="col-md-6 mb-3">
                <small for="product_id">Product ID</small>
                <div class="input-group-outline input-group">
                    <input type="text" class="form-control" id="product_id" value="{{ $produksi->product->name }}"
                        readonly>
                </div>
            </div>
        </div>

        @if($produksi->is_custom_formula === 1)
        <h5><strong>Formula Yang Di butuhkan</strong></h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(json_decode($produksi->custom_formula_kemas, true) ?? [] as $item)
                    @php
                    $inv = $inventories->firstWhere('id', $item['inventory_id']);
                    @endphp
                    <tr>
                        <td>{{ $inv->name ?? 'Tidak ditemukan' }}</td>
                        <td>{{ $item['jumlah'] }}</td>
                        @if($inv->type != 'Bahan Baku')
                        <td>pcs</td>
                        @else
                        <td>gram</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="mt-2">
            <h5><strong>Formula Kemasan</strong></h5>
            <div class="horizontal-scroll p-1 d-flex">
                @foreach($formulaKemasan as $items)
                @if($produksi->batch_size === '100')
                <div class="card-custom">
                    <p><strong>{{ $items->name ?? 'tidak ada' }}</strong></p>
                    <p>{{ $items->jumlah ?? 'tidak ada' }} {{ $items->satuan ?? 'tidak ada' }}
                    </p>
                </div>
                @else
                @php
                $jumlahKemas = ceil(($items->jumlah / 100) * $produksi->batch_size);
                @endphp
                <div class="card-custom">
                    <p><strong>{{ $items->name ?? 'tidak ada' }}</strong></p>
                    <p>{{ $jumlahKemas }} {{ $items->satuan ?? 'tidak ada' }}</p>
                </div>
                @endif
                @endforeach
            </div>

            <h5 class="mt-4"><strong>Formula Bahan Baku</strong></h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Bahan Baku</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($formulaBahanBaku as $items)
                        @if($produksi->batch_size === '100')
                        <tr>
                            <td>{{ $items->name ?? 'tidak ada' }}</td>
                            <td>{{ formatJumlah($items->jumlah) }}
                            </td>
                            <td>{{ $items->satuan ?? 'tidak ada' }}</td>
                        </tr>
                        @else
                        @php
                        $jumlahBaku = smartRound(($items->jumlah / 100) * $produksi->batch_size);
                        @endphp
                        <tr>
                            <td>{{ $items->name ?? 'tidak ada' }}</td>
                            <td>{{ $jumlahBaku }}</td>
                            <td>{{ $items->satuan ?? 'tidak ada' }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($produksi['status'] == "pending" && auth()->user()->hasAnyRole(['Supervisor', 'Admin']))
        <button type="submit" class="btn btn-success">Confirm</button>
        @endif
    </form>
</div>
<div class="d-flex justify-content-end gap-2 mt-3">
    @if(auth()->user()->hasAnyRole(['Production Manager', 'Admin']))
    {{-- Delete Form --}}
    <form action="{{ route('produksi.delete') }}" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" name="id" value="{{ $produksi->id }}">
        <button type="submit" class="btn btn-secondary" onclick="return confirm('Yakin ingin membatalkan tiket ini?')">
            Batalkan Tiket
        </button>
    </form>
    @endif
</div>
@endsection