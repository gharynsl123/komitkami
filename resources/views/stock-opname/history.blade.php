@extends('layouts.app')
@section('title', 'Komitkami Helper | Riwayat Stock Opname')
@section('content')

<div class="mt-4 d-flex align-items-center justify-content-between">
    <h4 class="mb-0">Riwayat Stock Opname</h4>
    <a href="{{ route('stock-opname.index') }}" class="btn btn-secondary m-0">Kembali ke Form</a>
</div>

<div class="card mt-4 p-4">
    <h6 class="mb-3">Cetak Laporan Stock Opname</h6>
    <form action="{{ route('stock-opname.print') }}" method="POST" class="row">
        @csrf
        <div class="col-md-3">
            <div class="input-group input-group-static mb-3">
                <label for="tanggal_opname" class="ms-0">Pilih Tanggal</label>
                <select name="tanggal_opname" id="tanggal_opname" class="form-control" required>
                    <option value="">-- Pilih Tanggal --</option>
                    @foreach($histories as $h)
                        <option value="{{ $h->tanggal_opname }}">{{ \Carbon\Carbon::parse($h->tanggal_opname)->format('d M Y') }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group input-group-static mb-3">
                <label for="tipe_barang" class="ms-0">Tipe Barang</label>
                <select name="tipe_barang" id="tipe_barang" class="form-control" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="Bahan Baku">Bahan Baku</option>
                    <option value="Bahan Kemas">Bahan Kemasan & Label</option>
                    <option value="Produk Jadi">Produk Jadi</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group input-group-static mb-3">
                <label for="format_cetak" class="ms-0">Format</label>
                <select name="format_cetak" id="format_cetak" class="form-control" required>
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                </select>
            </div>
        </div>
        <div class="col-md-3 d-flex align-items-end mb-3">
            <button type="submit" class="btn btn-info w-100 mb-0">Cetak Laporan</button>
        </div>
    </form>
</div>

<div class="card mt-4 p-4">
    @if(session('success'))
        <div class="alert alert-success text-white">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger text-white">
            <i class="material-icons me-1" style="font-size:16px;vertical-align:middle;">warning</i>
            {{ session('error') }}
        </div>
    @endif
    
    <div class="table-responsive p-0">
        <table class="table table-borderless table-hover border border-0 align-items-center mb-0" id="dataTableDefault">
            <thead>
                <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Barang</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Batch / ED</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stok Sistem</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Stok Fisik</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Selisih</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Keterangan</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Petugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $history)
                <tr>
                    <td class="align-middle text-secondary text-xs font-weight-bold">
                        {{ \Carbon\Carbon::parse($history->tanggal_opname)->format('d M Y') }}
                    </td>
                    <td class="align-middle text-secondary text-xs font-weight-bold">
                        {{ $history->inventory ? $history->inventory->name : ($history->product ? $history->product->name : '-') }}
                    </td>
                    <td class="align-middle text-secondary text-xs font-weight-bold">
                        @if($history->stockIn)
                            {{ $history->stockIn->nomor_batch ?? 'N/A' }} / 
                            {{ $history->stockIn->tanggal_ed ? \Carbon\Carbon::parse($history->stockIn->tanggal_ed)->format('d M Y') : 'N/A' }}
                        @elseif($history->stockInProduct)
                            {{ $history->stockInProduct->batch_number ?? $history->stockInProduct->produksi->batch_number ?? 'N/A' }} / 
                            {{ $history->stockInProduct->expired_date ? \Carbon\Carbon::parse($history->stockInProduct->expired_date)->format('d M Y') : ($history->stockInProduct->produksi->tanggal_expired ? \Carbon\Carbon::parse($history->stockInProduct->produksi->tanggal_expired)->format('d M Y') : 'N/A') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="align-middle text-secondary text-xs font-weight-bold">
                        {{ formatJumlah($history->stok_sistem) }}
                    </td>
                    <td class="align-middle text-secondary text-xs font-weight-bold">
                        {{ formatJumlah($history->stok_fisik) }}
                    </td>
                    <td class="align-middle text-xs font-weight-bold {{ $history->selisih > 0 ? 'text-success' : ($history->selisih < 0 ? 'text-danger' : 'text-secondary') }}">
                        {{ $history->selisih > 0 ? '+' : '' }}{{ formatJumlah($history->selisih) }}
                    </td>
                    <td class="align-middle text-secondary text-xs font-weight-bold">
                        {{ $history->keterangan ?: '-' }}
                    </td>
                    <td class="align-middle text-secondary text-xs font-weight-bold">
                        {{ $history->user->name ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
