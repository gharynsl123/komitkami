@extends('layouts.app')
@section('title', 'Komitkami Helper | Cetak Laporan Permintaan Material Produksi')
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Preview Laporan Permintaan Material</h6>
        </div>
        <div class="card-body">
            {{-- FILTER TANGGAL --}}
            <form method="GET" class="form-inline mb-4">
                <div class="form-group mr-2">
                    <label for="tanggal" class="mr-2 font-weight-bold">Filter Tanggal Dibuat:</label>
                    <input 
                        type="date" 
                        name="tanggal" 
                        id="tanggal"
                        class="form-control border border-1 px-2 py-1 mb-3"
                        value="{{ request('tanggal', Carbon\Carbon::now()->toDateString()) }}"
                    >
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <!-- backbutton -->
                @if (!Auth::user()->hasRole('Supervisor'))
                <a href="{{url('/permintaan-material')}}" class="btn btn-sm btn-secondary ml-2">Kembali</a>
                @endif
            </form>

            {{-- FORM CETAK LAPORAN --}}
            <form action="{{ route('print.material') }}" method="GET" target="_blank">
                <input type="hidden" name="tanggal" value="{{ request('tanggal', Carbon\Carbon::now()->toDateString()) }}">
                
                <div class="mb-3">
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="fas fa-print fa-sm text-white-50"></i> Cetak Request Material Terpilih
                    </button>
                </div>

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="produksi-tab" data-bs-toggle="tab" href="#produksi" role="tab" aria-controls="produksi" aria-selected="true">Produksi</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="umum-tab" data-bs-toggle="tab" href="#umum" role="tab" aria-controls="umum" aria-selected="false">Umum</a>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="produksi" role="tabpanel" aria-labelledby="produksi-tab">
                        {{-- LIST PRODUKSI --}}
                        <div class="table-responsive mb-4">
                            <h5 class="font-weight-bold">Permintaan Bahan Produksi</h5>
                            <p class="text-muted">Pilih batch produksi yang ingin dicetak.</p>
                            <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="5%"><input type="checkbox" id="checkAllProduksi" checked></th>
                                        <th>No</th>
                                        <th>No Produksi (Batch)</th>
                                        <th>Nama Produk</th>
                                        <th>Batch Size</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($produksi as $index => $item)
                                        <tr>
                                            <td class="text-center"><input type="checkbox" name="selected_produksi_ids[]" value="{{ $item->id }}" class="checkProduksi" checked></td>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->batch_number }}</td>
                                            <td>{{ $item->product->name ?? '-' }}</td>
                                            <td>{{ $item->batch_size }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data produksi pada periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="umum" role="tabpanel" aria-labelledby="umum-tab">
                        {{-- LIST UMUM --}}
                        <div class="table-responsive">
                            <h5 class="font-weight-bold">Permintaan Stok Keluar (Umum)</h5>
                            <p class="text-muted">Pilih permintaan umum yang ingin dicetak.</p>
                            <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="5%"><input type="checkbox" id="checkAllUmum" checked></th>
                                        <th>No</th>
                                        <th>Keterangan</th>
                                        <th>Waktu Pembuatan</th>
                                        <th>Jumlah Item</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($permintaan_umum as $identifier => $group)
                                        @php
                                            $parts = explode('--', $identifier);
                                            $keterangan = $parts[0];
                                            $waktuDisplay = $group->first()->created_at->format('d/m/Y H:i');
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="selected_umum_identifiers[]" value="{{ $identifier }}" class="checkUmum" checked>
                                            </td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $keterangan }}</td>
                                            <td>{{ $waktuDisplay }}</td>
                                            <td>{{ $group->count() }} item</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data permintaan umum pada periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>

            <script>
                document.getElementById('checkAllProduksi').addEventListener('change', function() {
                    let checkboxes = document.querySelectorAll('.checkProduksi');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
                
                document.getElementById('checkAllUmum').addEventListener('change', function() {
                    let checkboxes = document.querySelectorAll('.checkUmum');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            </script>

        </div>
    </div>
</div>
@endsection