@extends('layouts.app')
@section('title', 'Komitkami Helper | Detail Produk')
@section('content')
<!-- back button with black color -->
<div class="d-flex mb-3 gap-3">
    <a href="{{ url('produk') }}" class="btn btn-dark m-0">Kembali</a>
    <button class="btn btn-primary m-0 px-3" data-bs-toggle="modal" data-bs-target="#manualStockModal">Input Stock Manual</button>
</div>

<!-- Manual Stock Modal -->
<div class="modal fade" id="manualStockModal" tabindex="-1" aria-labelledby="manualStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manualStockModalLabel">Input Stock Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="manualStockForm" class="row g-3" method="POST" action="{{ route('stock-in-product.manual', $product->id) }}">
                    @csrf
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Jumlah Masuk</label>
                        <input type="text" class="form-control border-1 border px-2" id="quantity" name="quantity" required>
                    </div>
                    <div class="col-md-4">
                        <label for="batch" class="form-label">Batch</label>
                        <input type="text" class="form-control border-1 border px-2" id="batch" name="batch_number" required>
                    </div>
                    <div class="col-md-4">
                        <label for="date" class="form-label">Tanggal Expired</label>
                        <input type="month" class="form-control border-1 border px-2" id="date" name="expired_date" required>
                    </div>
                    <div class="col-md-12">
                        <label for="note" class="form-label">Keterangan</label>
                        <textarea class="form-control border-1 border px-2" id="note" name="note"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" form="manualStockForm" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- card table item product information  -->
<div class="card">
    <table class="table table-borderless table-hover align-items-center mb-0">
        <thead>
            <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-left">
                Nama Produk</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-left">
                Kode Produk</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-left">
                Harga</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                Acuan Produk Jadi</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                Total Masuk</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                Total Keluar</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <td class="text-left">{{$product->name}} <br> <small>({{$product->brand->name}})</small></td>
            <td class="text-left">{{$product->code}}</td>
            <td class="text-left">@currency($product->price)</td>
            <td class="text-center">
                <form action="{{ $product->produkJadi ? url('/produk-jadi-update', $product->produkJadi->id) : url('/store-acuan-produk-jadi') }}" method="POST">
                    @csrf
                    @if($product->produkJadi)
                        @method('PUT')
                    @endif
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="d-flex gap-2">
                        <input type="text" name="acuan" class="form-control px-2 py-1 border border-1 text-center" 
                            placeholder="Masukkan Acuan" 
                            value="{{ $product->produkJadi->acuan ?? '' }} {{$product->tipe}}">
                    </div>
                </form>
            <td class="text-center">{{ number_format((float)($product->sum_in ?? 0), 0) }}</td>
            <td class="text-center">{{ number_format((float)($product->sum_out ?? 0), 0) }}</td>
            <td class="text-center">{{ number_format((float)($product->stock_akhir ?? 0), 0) }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="card my-4">
    <div class="card-body px-0 pb-2">
        <div class="table-responsive p-0">
            <table class="table table-borderless table-hover align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                            Tanggal</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                            Keterangan</th>
                        <th
                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                            Jenis</th>
                        <th
                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                            Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    @endphp
                    @foreach($stockCards as $item)
                    <tr>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">{{ $item->created_at->format('d-m-Y') }}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">{{ $item->note ?? 'Tanpa Keterangan' }}</p>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">{{ $item->type }}</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">{{ number_format((float)($item->quantity ?? 0), 0) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection