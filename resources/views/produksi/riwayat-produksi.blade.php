@extends('layouts.app')
@section('title', 'Komitkami Helper | Riwayat Produksi')
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
</style>
<div class="d-flex justify-content-between">
    <h4>Riwayat Produksi</h4>
    <a href="{{url('/print-catatan-batch-produksi')}}" class="btn btn-sm btn-secondary">Print Catatan Batch Produksi</a>
</div>
<div class="card p-3 shadow">
    <div class="table-responsive">
        <table class="table table-borderless" id="dataTable">
            <thead>
                <tr>
                    <th>Tanggal Produksi</th>
                    <th>nama produk</th>
                    <th>batch number</th>
                    <th>hasil produksi</th>
                    <th>size</th>
                    <th>status</th>
                    <th>action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tikets as $item)
                @php
                $fontSize = '12px';
                if(strlen($item->bahanBaku->nama_formula) > 20){
                    $fontSize = '10px';
                }
                @endphp
                <tr>
                    <td>{{ $item->tanggal_produksi->format('d-m-Y')}}</td>
                    <td>
                        {{ $item->product->name }}
                        <br><small class="badge bg-primary" style="font-size: {{ $fontSize }}">{{ $item->bahanBaku->nama_formula }}</small>
                    </td>
                    <td>{{ $item->batch_number }}</td>
                    <td>{{ $item->produkjadi->nilai_actual ?? 'Tidak Ada' }}</td>
                    <td>{{ $item->batch_size }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        <a href="{{url('/mulai-produksi', $item->batch_number)}}" class="btn btn-primary m-0 px-3 py-2">Detail</a>
                        @if(Auth::user()->hasAnyRole(['Admin', 'Supervisor', 'Production Manager']) && ($item->status == 'close' || $item->status == 'failed' || $item->status == 'done'))
                        <a href="{{url('/print-laporan-produksi', $item->batch_number)}}" class="m-0 btn btn-success">
                            <i class="material-icons opacity-10">download</i> Hasil Produksi
                        </a>
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
    $('#dataTable').DataTable({
        "info": true,
        "ordering": false,
        "lengthChange": false,
        "pageLength": 6,
        "searching": true
    });
});
</script>
@endsection
