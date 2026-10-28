@extends('layouts.app')
@section('title', 'Komitkami Helper | Persedian Bahan Baku / Inventory')
@section('content')
<div class="mt-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <form id="filter-form" method="GET" action="{{ url('/local-inventory') }}" class="m-0">
        <div class="d-flex align-items-center gap-2">
            <select id="month" name="month" class="form-control border px-3 py-2 text-sm bg-white" style="min-width: 140px;">
                <option value="">Semua Bulan</option>
                @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                </option>
                @endforeach
            </select>
            
            <select id="year" name="year" class="form-control border px-3 py-2 text-sm bg-white" style="min-width: 120px;">
                <option value="">Semua Tahun</option>
                @foreach(range(date('Y'), 2000) as $y)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
                @endforeach
            </select>

            <button type="submit"
                class="btn btn-primary m-0 px-3 text-sm flex-shrink-0"
                style="white-space: nowrap; height: 40px;">
                FILTER
            </button>

            @if(request('month') || request('year'))
                <a href="{{ url('/local-inventory') }}"
                    class="btn btn-secondary m-0 px-3 text-s flex-shrink-0"
                    style="white-space: nowrap; height: 40px;"
                    title="Reset">
                    RESET
                </a>
            @endif
        </div>
    </form>
    
    <div class="d-flex align-items-center gap-2">
        <a href="{{url('/print-stock-gudang')}}" class="btn btn-outline-primary m-0 py-2 text-sm">
            <i class="material-icons text-sm align-middle me-1">print</i> Print Stok
        </a>
        <a class="btn bg-gradient-dark m-0 py-2 text-sm" id="btn-create" style="cursor: pointer;">
            <i class="material-icons text-sm align-middle me-1">add</i> Tambah Bahan Baku
        </a>
    </div>
</div>

<div class="card p-3 mt-3" id="form-create" style="display: none;">
    <h6 class="mb-4">Create Products</h6>
    <form action="{{url('/store-bahanbaku')}}" class="row" method="post">
        @csrf
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <label for="name" class="form-label">Nama Barang</label>
                <input name="name" id="name" type="text" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <label for="code" class="form-label">Kode Barang</label>
                <input name="code" id="code" type="text" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <select name="type" class="form-control border py-2">
                    <option value="Bahan Baku">Pilih Tipe Bahan Baku</option>
                    <option value="Bahan Baku">Bahan Baku</option>
                    <option value="Bahan Kemasan">Bahan Kemasan</option>
                    <option value="Bahan Kemas Label">Bahan Kemas Label</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <label for="stock" class="form-label">Stok Awal</label>
                <input name="stok" id="stock" type="string" class="form-control">
            </div>
        </div>
        <div class="col-md-12 mb-0">
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </form>
</div>


<div class="table-div">
    <div class="card mt-4 px-4 tab-content" id="nav-tabContent">
        <div class="card-body px-0 pb-2 tab-pane fade show active" id="general" role="tabpanel">
            <div class="table-responsive p-0">
                <table class="table table-borderless table-hover border border-0 align-items-center mb-0"
                    id="dataTableDefault">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Nama</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Spesifikasi</th>
                            <th class="d-none text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Tipe Bahan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Stok Awal</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Barang Masuk</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Barang Keluar</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Stok Akhir</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryData as $item)
                        <tr>
                            <td class="align-middle text-secondary text-xs font-weight-bold">
                                {{ $item['name'] }} <br> <small class="text-muted font-weight-normal">{{ $item['code'] }}</small>
                            </td>
                            <!-- status speksifikasi menggunakan badge-->
                            <td class="align-middle text-center text-nowrap">
                                @if($item['has_specifications'])
                                    <span class="badge bg-success">Sudah Di isi</span>
                                @else
                                    <span class="badge bg-danger">Belum Di isi</span>
                                @endif
                            </td>
                            <td class="align-middle text-secondary text-xs font-weight-bold d-none">
                                {{ $item['type'] }}
                            </td>
                            <td class="align-middle text-center text-secondary text-xs font-weight-bold">                                   
                                {{formatJumlah($item['stok_awal'])}}
                            </td>
                            <td class="align-middle text-center text-secondary text-xs font-weight-bold">
                                {{formatJumlah($item['stok_masuk'])}}
                            </td>
                            <td class="align-middle text-center text-secondary text-xs font-weight-bold">
                                {{formatJumlah($item['stok_keluar'])}}
                            </td>
                            <td class="align-middle text-center text-secondary text-xs font-weight-bold">
                                {{formatJumlah($item['stok_akhir'])}}
                            </td>
                            <td class="align-middle text-center text-nowrap">
                                <a href="/local-inventory/detail/{{$item['slug']}}" class="btn btn-sm btn-success m-0">Detail</a>
                                <a href="{{url('/print-kartu-stok/'.$item['slug'])}}" class="btn btn-sm btn-secondary m-0">Kartu Stok</a>
                                <a href="{{url('/edit-material/'.$item['slug'])}}" class="btn btn-sm btn-info m-0">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnCreate = document.getElementById('btn-create');
    const formCreateItem = document.getElementById('form-create');
    const filterForm = document.getElementById('filter-form');
    const divTable = document.querySelector('.table-div');

    const tableRows = document.querySelectorAll('#dataTableDefault tbody tr');
    const table = document.getElementById('dataTableDefault');

    btnCreate.addEventListener('click', function() {
        if (formCreateItem.style.display === 'none') {
            btnCreate.textContent = "Tutup Form";
            formCreateItem.style.display = 'block';
            divTable.style.display = 'none';
            filterForm.style.display = 'none';
        } else {
            btnCreate.textContent = "Tambah Bahan Baku";
            formCreateItem.style.display = 'none';
            divTable.style.display = 'block';
            filterForm.style.display = 'block';
        }
    });
});
</script>
@endsection