@extends('layouts.app')
@section('title', 'Komitkami Helper | Pengambilan Sample In Prosos Control')
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
<div class="container">
    <h5 class="text-center">Pengambilan Sample In Prosos Control</h5>
    <div class="card p-3">
        <div class="table-responsive">
            <table class="table table-borderless" id="dataTable">
                <thead>
                    <tr>
                        <th>Tanggal Produksi</th>
                        <th>nama produk</th>
                        <th>batch number</th>
                        <th>action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data->reverse() as $item)
                    <tr>
                        <td>{{ $item->tanggal_produksi->format('d-m-Y')}}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->batch_number }}</td>
                        <td>
                            <a href="{{url('/view-form-sample-IPC', $item->batch_number)}}" class="btn btn-primary m-0 px-3 py-2">View Form</a>
                            @if($item->cetakLaporanIPC)
                            <a href="{{ url('/print-laporan-ipc', $item->batch_number) }}" class="btn btn-success m-0 px-4 py-2">
                                Cetak Laporan
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "info": true,
        "ordering": false,
        "lengthChange": false,
        "pageLength": 6,
        "searching": true,
        "language": {
            emptyTable: "Tidak ada data"
        }
    });
});</script>
@endsection