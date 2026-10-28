@extends('layouts.app')
@section('title', 'Komitkami Helper | Riwayat Alat Produksi')
@section('content')
<div class="container">
    <div class="card p-3">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Pilih Nama Alat</label>
                <select name="nama_alat" id="alat-select" class="form-select px-2 py-1">
                    <option value="">-- pilih alat --</option>
                    <optgroup label="Mesin">
                        <option value="Mesin Mixing 1">Mesin Mixing 1</option>
                        <option value="Mesin Mixing 2">Mesin Mixing 2</option>
                        <option value="Mesin Mixing 3">Mesin Mixing 3</option>
                        <option value="Mesin Filling 1">Mesin Filling 1</option>
                        <option value="Mesin Filling 2">Mesin Filling 2</option>
                    </optgroup>
                    <optgroup label="Tangki">
                        @for($i = 1; $i <= 16; $i++) 
                            <option value="Tangki Mixing {{ $i }}">Tangki Mixing {{ $i }}</option>
                        @endfor
                    </optgroup>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Pilih Bulan</label>
                <input type="month" name="date" id="dateTime" class="form-control border px-2 py-1 border-1">
            </div>
        </div>

        <button id="btn-print" class="btn btn-outline-primary btn-sm">
            🖨️ Print Checklist
        </button>

    </div>

    <div class="card p-3 mt-4">
        <div class="table-responsive">
            <table class="table table-borderless" style="border:0px solid !important;" id="dataTable">
                <thead>
                    <tr>
                        <td>Tanggal</td>
                        <td>Produk Pemakaian</td>
                        <td>No Batch</td>
                        <td>Pelaksana</td>
                        <td>Pemeriksa</td>
                        <td>Keterangan</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatAlat->reverse() as $riwayat)
                    @php
                        $clean = preg_replace('/\s*\([^)]*\)/', '', $riwayat->produksi->product->name);
                        $clean = preg_replace('/\s*\d.*$/', '', $clean);
                    @endphp
                    <tr>
                        <td>{{ $riwayat->created_at->format('Y-m-d') }}</td>
                        <td>{{ $clean }}</td>
                        <td>{{ $riwayat->produksi->batch_number }}</td>
                        <td>{{ $riwayat->dibersihkan_oleh }}</td>
                        <td>
                            {{ $riwayat->diperiksa_oleh === "Belum Di periksa. Segera lapor ke QC" ? '-' : $riwayat->diperiksa_oleh ?? '-'}}
                        </td>
                        <td>{{ $riwayat->nama_alat }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById('btn-print').addEventListener('click', function() {
    let alat = document.getElementById('alat-select').value;
    let bulan = document.getElementById('dateTime').value;

    // arahkan ke route dengan query string
    window.open(`/riwayat-alat/print?alat=${alat}&bulan=${bulan}`, '_blank');
});

document.addEventListener('DOMContentLoaded', function() {
    // Init select2
    $('#alat-select').select2({
        theme: 'bootstrap-5',
        placeholder: '-- pilih alat --',
        width: '100%',
        allowClear: true

    });

    // Init DataTable
    let table = $('#dataTable').DataTable({
        paging: true,
        searching: true,
        info: false,
        lengthChange: false,
        pageLength: 10,
        ordering: false,
        order: [[0, 'desc']],
        language: {
            zeroRecords: "Tidak ada data yang ditemukan",
            infoEmpty: "Menampilkan 0 hingga 0 dari 0 entri",
            infoFiltered: "(disaring dari _MAX_ total entri)"
        }
    });


    // Filter berdasarkan Nama Alat (kolom ke-6 → index 5)
    $('#alat-select').on('change', function () {
        let value = this.value;
        table.column(5).search(value ? '^' + value + '$' : '', true, false).draw();
    });

    // Filter berdasarkan Bulan (kolom ke-1 → index 0)
    $('#dateTime').on('change', function () {
        let selectedMonth = this.value; // format yyyy-mm
        if (selectedMonth) {
            table.column(0).search('^' + selectedMonth, true, false).draw();
        } else {
            table.column(0).search('').draw();
        }
    });
});
</script>

<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection