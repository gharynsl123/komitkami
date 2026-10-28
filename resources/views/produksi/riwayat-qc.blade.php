@extends('layouts.app')
@section('title', 'Komitkami Helper | Riwayat Quality Control')
@section('content')
<style>
    th {
        text-align: left;
        margin: 0px;
        border:1px !important;
    }
    td {
        text-align: left;
        margin: 0px;
    }
    .table {
        border-collapse: collapse; /* Agar tidak ada gap di antaranya */
        margin-bottom:0px;
    }
    th, td {
        padding:10px !important; 
        border: 0.1px solid black !important; /* Border untuk tiap cell */
    }
    #dataTable .progress::after {
        display: none !important;
        content: none !important;
        box-shadow: none !important;
    }
    #dataTable .progress-bar {
        height: 100% !important;
        min-height: 8px;
    }
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Data Quality Control</h4>
    <div class="card p-2">
        <div class="d-flex align-items-center">
            <label for="filterBulan" class="me-2 mb-0 fw-bold">Filter To Do List:</label>
            <select id="filterBulan" class="form-select form-select-sm" style="width: auto;">
                <option value="">Semua</option>
                <option value="Selesai">Selesai</option>
                <option value="Belum Waktunya">Belum Waktunya</option>
                <option value="Bulan 0">Bulan 0</option>
                <option value="Bulan 3">Bulan 3</option>
                <option value="Bulan 6">Bulan 6</option>
                <option value="Bulan 9">Bulan 9</option>
                <option value="Bulan 12">Bulan 12</option>
                <option value="Bulan 18">Bulan 18</option>
                <option value="Bulan 24">Bulan 24</option>
                <option value="Bulan 36">Bulan 36</option>
                <option value="Bulan 48">Bulan 48</option>
                <option value="Bulan 60">Bulan 60</option>
            </select>
        </div>
    </div>
</div>
<div class="card p-3 shadow">
    <div class="table-responsive">
        <table class="table table-borderless" id="dataTable">
            <thead>
                <tr>
                    <th>Tanggal Produksi</th>
                    <th>nama produk</th>
                    <th>Progress Pemantauan</th>
                    <th>batch number</th>
                    <th>hasil produksi</th>
                    <th>size</th>
                    <th>status</th>
                    <th class="text-center">action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tikets as $item)
                @php
                    $totalBulan = 8; 
                    $bulanSelesai = 0;
                    $currentBulanFilter = 'Belum Mulai';

                    if ($item->pemantauanStabilitas && $item->pemantauanStabilitas->detail) {
                        $totalBulan = $item->pemantauanStabilitas->detail->count() ?: 8;
                        $selesaiDetails = $item->pemantauanStabilitas->detail->where('status', 'selesai');
                        $bulanSelesai = $selesaiDetails->count();
                        $nextDetail = $item->pemantauanStabilitas->detail->where('status', '!=', 'selesai')->sortBy('bulan_ke')->first();
                        
                        if ($nextDetail) {
                            if ($nextDetail->tanggal_jadwal && \Carbon\Carbon::parse($nextDetail->tanggal_jadwal)->startOfDay()->lte(\Carbon\Carbon::now()->startOfDay())) {
                                $currentBulanFilter = 'Bulan ' . $nextDetail->bulan_ke;
                            } else {
                                $currentBulanFilter = 'Belum Waktunya';
                            }
                        } else {
                            // Jika semua sudah selesai
                            $currentBulanFilter = 'Selesai';
                        }
                    } else if ($item->checkQualityControl && $item->checkQualityControl->count() > 0) {
                        // Jika pengecekan QC awal (Bulan 0) sudah ada, cek apakah sudah 3 bulan dari tanggal produksi
                        if ($item->tanggal_produksi && $item->tanggal_produksi->copy()->addMonths(3)->startOfDay()->lte(\Carbon\Carbon::now()->startOfDay())) {
                            $currentBulanFilter = 'Bulan 3';
                        } else {
                            $currentBulanFilter = 'Belum Waktunya';
                        }
                    } else {
                        $currentBulanFilter = 'Bulan 0';
                    }
                    
                    $progress = ($totalBulan > 0) ? ($bulanSelesai / $totalBulan) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $item->tanggal_produksi->format('d-m-Y')}}</td>
                    <td>{{ $item->product->name }}</td>
                    <td data-search="{{ $currentBulanFilter }}">
                        <div class="d-flex align-items-center">
                            <span class="me-2 small fw-bold">{{ $bulanSelesai }}/{{ $totalBulan }}</span>
                            <div class="progress flex-grow-1"
                                style="
                                    height: 8px;
                                    min-width: 80px;
                                    max-width: 150px;
                                    overflow: hidden;
                                    border-radius: 4px;
                                ">
                                <div class="progress-bar bg-success"
                                    role="progressbar"
                                    style="
                                        width: {{ $progress }}%;
                                        height: 100%;
                                    "
                                    aria-valuenow="{{ $progress }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $item->batch_number }}</td>
                    <td>{{ $item->produkjadi->nilai_actual ?? 'Belum Ada' }}</td>
                    <td>{{ $item->batch_size }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        @if(Auth::user()->hasAnyRole(['Quality Control', 'Admin', 'Production Manager']) && $item->checkQualityControl->first())
                        <a href="{{url('/pantau-stabilitas', $item->id)}}" class="btn btn-sm btn-info m-0">
                            Pantau Stabilitas
                        </a>
                        <a href="{{url('/print-sertifikat-qc', $item->batch_number)}}" class="btn btn-sm btn-success m-0">
                            <i class="material-icons text-sm m-0">download</i>
                            Sertifikat
                        </a>
                        @else
                        <a href="{{url('/mulai-produksi', $item->batch_number)}}" class="btn btn-sm btn-primary m-0">Detail</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada Produksi yang selesai</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#dataTable').DataTable({
        "info": true,
        "ordering": false,
        "lengthChange": false,
        "pageLength": 6,
        "searching": true
    });

    $('#filterBulan').on('change', function() {
        var val = $(this).val();
        // Exact match or empty
        table.column(2).search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();
    });
});
</script>
@endsection