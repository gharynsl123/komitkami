@extends('layouts.app')
@section('title', 'Komitkami Helper | Daftar Permintaan Material Produksi')
@section('content')
<div class="row align-items-center mb-4">
    <div class="col-12 col-md-auto mb-3 mb-md-0">
        <div class="nav-wrapper position-relative">
            <ul class="nav nav-pills nav-fill p-1" id="pills-tab" role="tablist">
                <!-- jika list masih ada maka tampilkan badge/notif sisa yang harus di kerjakan -->
                <li class="nav-item" role="presentation">
                    @php
                        $total_produksi = $permintaan_produksi->count();
                    @endphp
                    <a class="nav-link mb-0 px-4 py-1 active" id="pills-produksi-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-produksi" type="button" role="tab">
                        <i class="material-icons text-lg position-relative">apps</i>
                        <span class="ms-1">Produksi</span>
                        @if($total_produksi > 0)
                            <span class="badge bg-danger">{{ $total_produksi }}</span>
                        @endif
                    </a>
                </li>
                <!-- jika list masih ada maka tampilkan badge/notif sisa yang harus di kerjakan -->
                <li class="nav-item" role="presentation">
                    @php
                        $total_lainnya = $permintaan_umum->count();
                    @endphp
                    <a class="nav-link mb-0 px-4 py-1" id="pills-lainnya-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-lainnya" type="button" role="tab">
                        <i class="material-icons text-lg position-relative">inbox</i>
                        <span class="ms-1">Lainnya</span>
                        @if($total_lainnya > 0)
                            <span class="badge bg-danger">{{ $total_lainnya }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-12 col-md-auto ms-auto d-flex flex-wrap gap-2 justify-content-center justify-content-md-end">
        <a href="{{url('/cetak-laporan-permintaan')}}" class="btn btn-outline-secondary m-0 flex-grow-1 flex-md-grow-0">
            Cetak Laporan
        </a>
        <a href="{{url('/show-all/permintaan')}}" class="btn btn-primary m-0 flex-grow-1 flex-md-grow-0">
            Show All
        </a>
    </div>
</div>
<div class="tab-content" id="pills-tabContent">
    <!-- TAB PRODUKSI -->
    <div class="tab-pane fade show active" id="pills-produksi" role="tabpanel" aria-labelledby="pills-produksi-tab">
        <div class="card p-3">
            <h4>Permintaan Bahan Produksi</h4>
            @forelse($permintaan_produksi as $batch_number => $groupedMaterials)
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <p class="m-0"><strong>Batch Number:</strong> {{ $batch_number }}</p>
                    <small class="mb-2">Barang Produksi:
                        {{ $groupedMaterials->first()->produksi->product->name ?? 'Tidak ada produk' }}
                    </small>
                    <small><strong>Tanggal : </strong>{{$groupedMaterials->first()->created_at->format('d m Y')}}</small>
                </div>
                <div class="d-flex gap-2">
                    @if(Auth::user()->hasAnyRole(['Inventory Manager', 'Admin']))
                        <a href="{{url('/proses-permintaan/produksi', $batch_number)}}" class="btn m-0 btn-sm btn-primary">Proses</a>
                    @endif
                    <span class="btn-sm btn btn-info m-0">{{ $groupedMaterials->first()->persetujuan }}</span>
                </div>
            </div>

            <div class="mb-4 card shadow-sm">
                <div class="p-2 rounded bg-light">
                    <div class="row g-3">
                        @foreach($groupedMaterials as $item)
                            <div class="mb-1 col-md-6">
                                <div class="bg-white p-3 rounded">
                                    <strong>{{ $item->bahanbaku->name ?? $item->bahankemas->name ?? $item->inventory->name }} :</strong>
                                    {{ $item->jumlah }} {{ $item->bahanbaku->satuan ?? $item->bahankemas->satuan ?? 'pcs' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @empty
            <p class="text-muted m-0">Tidak ada permintaan produksi.</p>
            @endforelse
        </div>
    </div>
    <div class="tab-pane fade" id="pills-lainnya" role="tabpanel" aria-labelledby="pills-lainnya-tab">
        <div class="card p-3">
            <h4>Permintaan Stok Keluar (Umum)</h4>
                @forelse($permintaan_umum as $keterangan => $groupedMaterials)
                @php
                    $parts = explode('--', $keterangan);
                    $actual_keterangan = $parts[0];
                    $tanggal_string = $parts[1] ?? '';
                @endphp
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <p class="m-0"><strong>Keterangan:</strong> {{ $actual_keterangan }}</p>
                        <small><strong>Pemohon : </strong> {{ $groupedMaterials->first()->user->name ?? 'User' }}</small>
                        <small><strong>Tanggal : </strong>{{$groupedMaterials->first()->created_at->format('d m Y')}}</small>
                    </div>
                    <div class="d-flex gap-2">
                        @if(Auth::user()->hasAnyRole(['Inventory Manager', 'Admin']))
                            <a href="{{url('/proses-permintaan/umum', $keterangan)}}" class="btn m-0 btn-sm btn-primary">Proses</a>
                        @endif
                        <span class="btn-sm btn btn-info m-0">{{ $groupedMaterials->first()->persetujuan }}</span>
                    </div>
                </div>

                <div class="mb-4 card shadow-sm">
                    <div class="p-2 rounded bg-light">
                        <div class="row g-3">
                            @foreach($groupedMaterials as $item)
                                <div class="mb-1 col-md-6">
                                    <div class="bg-white p-3 rounded">
                                        <strong>{{ $item->inventory->name ?? 'Barang' }} :</strong>
                                        {{ $item->jumlah }} {{ $item->inventory->type == 'Bahan Baku' ? 'gram' : 'pcs' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted m-0">Tidak ada permintaan umum.</p>
                @endforelse
        </div>
    </div>
</div>
@endsection
