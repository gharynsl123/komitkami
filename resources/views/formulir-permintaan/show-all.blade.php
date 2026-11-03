@extends('layouts.app')
@section('title', 'Komitkami Helper | Daftar Permintaan Material Produksi')
<style>
    /* Container Utama */
    .custom-search-container {
        display: flex;
        align-items: stretch;
        background: #fff;
        border-radius: 12px;
        /* Membuat sudut membulat halus */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        /* Bayangan lembut */
        overflow: hidden;
        border: 1px solid #e0e0e0;
        height: 45px;
        /* Tinggi tetap agar presisi */
    }

    /* Tombol Kalender Kustom */
    .btn-calendar {
        background-color: #e91e63;
        /* Warna pink/merah sesuai gambar */
        color: white;
        border: none;
        padding: 0 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease;
        cursor: pointer;
    }

    .btn-calendar:hover {
        background-color: #d81b60;
    }

    /* Area Input */
    .search-input-group {
        flex-grow: 1;
        display: flex;
        align-items: center;
    }

    .search-input-group input {
        width: 100%;
        border: none;
        padding: 0 15px;
        font-size: 14px;
        color: #333;
        outline: none;
        /* Menghapus outline biru default browser */
    }

    .search-input-group input::placeholder {
        color: #adb5bd;
    }

    /* Responsivitas Mobile */
    @media (max-width: 768px) {
        .custom-search-container {
            width: 100%;
            /* Full width di mobile */
            margin-top: 10px;
        }
    }
</style>
@section('content')
    <div class="row align-items-center mb-4 g-3">
        <div class="col-12 col-md-auto">
            <a href="{{ url('/permintaan-material') }}"
                class="btn btn-outline-secondary m-0 d-flex align-items-center justify-content-center">
                <i class="material-icons text-md me-1">arrow_back_ios_new</i> Kembali
            </a>
        </div>

        <div class="col-12 col-md-auto">
            <div class="nav-wrapper position-relative">
                <ul class="nav nav-pills nav-fill p-1" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link mb-0 px-4 py-1 active" id="pills-produksi-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-produksi" type="button" role="tab">
                            <i class="material-icons text-lg position-relative">apps</i>
                            <span class="ms-1">Produksi</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link mb-0 px-4 py-1" id="pills-lainnya-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-lainnya" type="button" role="tab">
                            <i class="material-icons text-lg position-relative">inbox</i>
                            <span class="ms-1">Lainnya</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFilter" aria-labelledby="offcanvasFilterLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasFilterLabel">Filter Tanggal</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form action="{{ url('/show-all/permintaan') }}" method="get">
                    <div class="mb-3">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ request()->start_date }}">
                    </div>
                    <div class="mb-3">
                        <label for="end_date">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="{{ request()->end_date }}">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </form>
            </div>
        </div>

        <div class="col-12 col-md-auto ms-auto">
            <div class="custom-search-container">
                <button class="btn-calendar" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFilter">
                    <i class="material-icons">date_range</i>
                </button>
                <div class="search-input-group">
                    <input type="text" id="search-data" placeholder="Cari...">
                </div>
            </div>
        </div>
    </div>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-produksi" role="tabpanel" aria-labelledby="pills-produksi-tab">
            <div class="card p-3">
                <h4>Permintaan Bahan Produksi</h4>
                @forelse($permintaan_produksi->reverse() as $batch_number => $groupedMaterials)
                    <div data-item>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <p class="m-0"><strong>Batch Number:</strong> {{ $batch_number }}</p>
                                <small class="mb-2"><strong>Barang Produksi:</strong>
                                    {{ $groupedMaterials->first()->produksi->product->name ?? 'Tidak ada produk' }}
                                </small>
                                <small><strong>Tanggal : </strong>{{$groupedMaterials->first()->created_at->format('d m Y')}}</small>
                            </div>
                            <div class="d-flex gap-2">
                                @if (Auth::user()->hasAnyRole(['Inventory Manager', 'Admin']))
                                    <a href="{{ url('/detail-permintaan/produksi', $batch_number) }}"
                                        class="btn m-0 btn-sm btn-primary">detail</a>
                                @endif
                                <span class="btn-sm btn btn-info m-0">{{ $groupedMaterials->first()->persetujuan }}</span>
                            </div>
                        </div>

                        <div class="mb-4 card shadow-sm">
                            <div class="p-2 rounded bg-light">
                                <div class="row g-3">
                                    @foreach ($groupedMaterials as $item)
                                        <div class="mb-1 col-md-6">
                                            <div class="bg-white p-3 rounded">
                                                <strong>{{ $item->bahanbaku->name ?? ($item->bahankemas->name ?? $item->inventory->name) }}
                                                    :</strong>
                                                {{ formatJumlah($item->jumlah) }}
                                                {{ $item->bahanbaku->satuan ?? ($item->bahankemas->satuan ?? 'pcs') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
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
                @forelse($permintaan_umum->reverse() as $keterangan => $groupedMaterials)
                    @php
                        $parts = explode('--', $keterangan);
                        $actual_keterangan = $parts[0];
                        $tanggal_string = $parts[1] ?? '';
                    @endphp
                    <div data-item>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <p class="m-0"><strong>Keterangan :</strong> {{ $actual_keterangan }}</p>
                                <small><strong>Pemohon : </strong>{{ $groupedMaterials->first()->user->name ?? 'User' }}</small>
                                <small><strong>Tanggal : </strong>{{$groupedMaterials->first()->created_at->format('d m Y')}}</small>
                            </div>
                            <div class="d-flex gap-2">
                                @if (Auth::user()->hasAnyRole(['Inventory Manager', 'Admin']))
                                    <a href="{{ url('/detail-permintaan/umum', $keterangan) }}"
                                        class="btn m-0 btn-sm btn-primary">Detail</a>
                                @endif
                                <span class="btn-sm btn btn-info m-0">{{ $groupedMaterials->first()->persetujuan }}</span>
                            </div>
                        </div>

                        <div class="mb-4 card shadow-sm">
                            <div class="p-2 rounded bg-light">
                                <div class="row g-3">
                                    @foreach ($groupedMaterials as $item)
                                        <div class="mb-1 col-md-6">
                                            <div class="bg-white p-3 rounded">
                                                <strong>{{ $item->inventory->name ?? 'Barang' }} :</strong>
                                                {{ formatJumlah($item->jumlah) }} {{ $item->inventory->unit ?? 'pcs' }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted m-0">Tidak ada permintaan umum.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- js filter data from batch number/keterangan/user name (title of the card) -->
    <script>
        $(document).ready(function() {
            $('#search-data').on('keyup', function() {
                var value = $(this).val().toLowerCase();

                // Memfilter data-item di dalam tab yang sedang aktif saja
                $('.tab-pane.active [data-item]').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Reset search bar saat ganti tab (opsional, tapi bagus untuk UX)
            $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function() {
                $('#search-data').val('');
                $('[data-item]').show();
            });
        });
    </script>
@endsection
