@extends('layouts.app')
@section('title', 'Komitkami Helper | Request Material Khusus')
@section('content')

<div class="card p-3 mb-3">
    <h6>Permintaan Barang</h6>
    <form action="{{ url('/request-store-out') }}" class="row mt-3" method="post">
        @csrf
        <div class="col-md-3 mb-3">
            <div class="input-group input-group-outline">
                <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                <input type="text" readonly value="{{Auth::user()->name}}" class="form-control">
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="input-group input-group-outline">
                <input type="text" readonly value="{{ auth()->user()->roles->first()->role_name }}"
                    class="form-control">
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="input-group input-group-outline">
                <input type="string" name="keterangan" class="form-control" placeholder="Keterangan Untuk" required>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="input-group input-group-outline">
                <input type="date" value="{{date('Y-m-d')}}" readonly class="form-control" required>
            </div>
            <small class="text-danger">*Tanggal Permintaan</small>
        </div>

        <!-- dinamy rows -->
        <div id="wrapper">
            <!-- 1 ROW -->
            <div class="item-row row g-2 align-items-end mb-2">
                
                <div class="col-md-6">
                    <select name="inventory_id[]" class="form-select" required>
                        <option value="">-- Pilih Items --</option>
                        @foreach($inventory as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }} - {{ $item->code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5">
                    <input type="text" name="jumlah[]" class="form-control border-1 border px-2 py-1"
                        placeholder="Jumlah Barang Yang Diminta" required>
                </div>

                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-primary w-100 btn-add m-0">
                        <i class="material-icons">add</i>
                    </button>
                </div>

            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Create</button>
            <button type="button" id="close-btn" class="btn btn-outline-secondary">Tutup Form</button>
        </div>
    </form>
</div>

<div class="text-end mt-3" style="display:none;" id="create-form">
    <button type="button" class="btn btn-dark">Input Keluar Stok Barang</button>
</div>

<!-- table untuk melihat request dari sang user itu sendiri -->
<div class="card p-3 shadow-lg">
    <div class="table-responsive">
        <table class="table table-hover align-items-center m-0">
            <thead class="thead-light">
                <tr>
                    <th style="width: 15%;">Keterangan Permintaan</th>
                    <th style="width: 15%;" class="text-center">Waktu Dibuat</th>
                    <th style="width: 15%;" class="text-center">Status</th>
                    <th style="width: 15%;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permintaan as $identifier => $items)
                @php
                    // Pecah identifier untuk mendapatkan teks asli keterangan
                    $parts = explode('--', $identifier);
                    $actualKeterangan = $parts[0];
                    $firstItem = $items->first();
                @endphp
                <tr>
                    <td>
                        <span class="d-block font-weight-bold text-dark">{{ $actualKeterangan }}</span>
                        <small class="text-muted">ID: {{ $identifier }}</small>
                    </td>
                    <td class="text-center">
                        {{ $firstItem->created_at->format('d/m/Y') }}<br>
                        <small class="text-secondary">{{ $firstItem->created_at->format('H:i') }} WIB</small>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-warning border-warning border text-uppercase">
                            {{ $firstItem->persetujuan }}
                        </span>
                    </td>
                    <td class="text-center">
                        <!-- Gunakan identifier lengkap untuk link detail -->
                        <a href="{{ url('/detail-permintaan/umum', $identifier) }}" 
                        class="btn btn-primary btn-icon-only shadow-sm m-0 py-2" 
                        title="Proses Permintaan">
                            <i class="material-icons">visibility</i>
                        </a>
                        <!-- tombol print -->
                        <a href="{{ route('print.material', ['selected_umum_identifiers' => [$identifier], 'tanggal' => $firstItem->created_at->toDateString()]) }}" 
                        target="_blank"
                        class="btn btn-success btn-icon-only shadow-sm m-0 py-2" 
                        title="Print Permintaan">
                            <i class="material-icons">print</i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="material-icons d-block mb-2" style="font-size: 40px;">inventory_2</i>
                        Belum ada permintaan yang perlu diproses.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // init select2 pertama
    $('.form-select').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Pilih Items --'
    });

    // ADD ROW
    $('#wrapper').on('click', '.btn-add', function() {

        let newRow = `
        <div class="item-row row g-2 align-items-end mb-2">
            <div class="col-md-6">
                <select name="inventory_id[]" class="form-select" required>
                    <option value="">-- Pilih Items --</option>
                    @foreach($inventory as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->name }} - {{ $item->code }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-5">
                <input type="text" name="jumlah[]" class="form-control border-1 border px-2 py-1"
                    placeholder="Jumlah Barang Yang Diminta" required>
            </div>

            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger w-100 btn-delete m-0">
                    <i class="material-icons">delete</i>
                </button>
            </div>

        </div>
        `;

        $('#wrapper').append(newRow);

        // INIT SELECT2 HANYA DI ROW BARU
        $('#wrapper .item-row:last .form-select').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Items --'
        });
    });

    // DELETE ROW (FIX)
    $('#wrapper').on('click', '.btn-delete', function() {
        $(this).closest('.item-row').remove();
    });

});
</script>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
@endsection