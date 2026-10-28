@extends('layouts.app')
@section('title', 'Komitkami Helper | List Tiket Produksi')
@section('content')

<div class="d-flex align-items-center">
    <a href="{{url('/ruang-produksi')}}" class="btn me-3 my-0 btn-secondary">Kembali</a>
    <h4 class="mb-0">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</h4>
</div>

@if(session('success'))
    <div class="alert alert-success text-white mt-3 mb-0" role="alert">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger text-white mt-3 mb-0" role="alert">
        {{ session('error') }}
    </div>
@endif

<div class="card mt-3">
    <div class="table-responsive">
        <table class="table m-0 table-striped align-items-center">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Nomor Batch</th>
                    <th>Batch Size</th>
                    <th>Status</th>
                    <th style="min-width: 170px;">Barang Jadi</th>
                    <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tikets as $tiket)
                <tr>
                    <td class="align-middle">{{ $tiket->product->name ?? "Unknown"}}</td>
                    <td class="align-middle">{{ $tiket->batch_number }}</td>
                    <td class="align-middle">{{ $tiket->batch_size }}</td>
                    <td class="align-middle">
                        @if($tiket->status == 'close')
                            <span class="badge bg-success">close</span>
                        @elseif($tiket->status == 'done')
                            <span class="badge bg-info">done</span>
                        @else
                            <span class="badge bg-secondary">{{ $tiket->status }}</span>
                        @endif
                    </td>
                    <td class="align-middle">
                        @if(in_array($tiket->status, ['close']))
                            <form action="{{ route('update-barang-jadi', $tiket->id) }}" method="post" class="d-flex align-items-center m-0">
                                @csrf
                                @method('PUT')
                                <input type="number" name="nilai_actual" value="{{ $tiket->produkJadi->nilai_actual ?? '' }}" class="form-control border px-2 py-1 text-center" style="width: 80px;" placeholder="0" required>
                                <button type="submit" class="btn btn-warning btn-sm my-0 ms-2 px-2 py-1">Edit</button>
                            </form>
                        @elseif(in_array($tiket->status, ['done']))
                            <form action="{{ route('kirim-barang-jadi', $tiket->id) }}" method="post" class="d-flex align-items-center m-0">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="produksi_id" value="{{ $tiket->id }}">
                                <input type="hidden" name="operator" value="{{ Auth::user()->name }}">
                                <input type="number" name="nilai_actual" class="form-control border px-2 py-1 text-center" style="width: 80px;" placeholder="Jumlah" required>
                                <button type="submit" class="btn btn-primary btn-sm my-0 ms-2 px-2 py-1">Kirim</button>
                            </form>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="align-middle text-center">
                        @if(in_array($tiket->status, ['close']))
                            <a href="{{url('/mulai-produksi', $tiket->batch_number)}}" class="btn btn-primary btn-sm my-0 px-3 py-1">Detail</a>
                        @else
                            <a href="{{ url('/detail-produksi', $tiket->batch_number) }}" class="btn btn-outline-primary btn-sm my-0 px-3 py-1">View</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-3">Tidak ada tiket untuk tanggal ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection