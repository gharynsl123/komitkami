@extends('layouts.app')
@section('title', 'Komitkami Helper | Detail Permintaan Material')
@section('content')
<div class="container">
    <div class="card p-3">
        @if ($requestData->first()->keterangan == null)
        <h4 class="text-center mb-4">FORM PERMINTAAN MATERIAL PRODUKSI</h4>
        @else
        <h4 class="text-center mb-4">FORM PERMINTAAN MATERIAL KHUSUS</h4>
        @endif
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="d-flex flex-column justify-content-center">
                <strong>Nomor Dokumen: KIG-PR-F-01-01-02</strong>
                <strong>Tanggal Di Proses: @if($requestData->first()->persetujuan === 'pending') - @else {{ date('d-m-Y', strtotime($requestData->first()->tanggal_di_proses)) }} @endif</strong>
            </div>
            <div class="card shadow-lg p-2 text-white" style="background-color:#3B3B3BFF;">
                <strong>Peruntukan Material : </strong>
                @if ($requestData->first()->keterangan == null)
                {{$produksi->product->name}} - {{$produksi->batch_number}}
                @else
                {{ $requestData->first()->user->name }} - {{ $requestData->first()->keterangan }}
                @endif
            </div>
        </div>
        <div class="row g-4 overflow-scroll">
            <div class="col-md-3 text-dark"><strong>Nama Material</strong></div>
            <div class="col-md-3 text-dark text-center"><strong>Jumlah Diperlukan</strong></div>
            <div class="col-md-3 text-dark text-center"><strong>Jumlah Diberikan</strong></div>
            <div class="col-md-3 text-dark text-center"><strong>Batch Pengambilan</strong></div>
            @foreach($requestData as $i => $req)
                <div class="row mb-2">
                    <input type="hidden" name="permintaan_id[]" value="{{$req->id}}">
                    <input type="hidden" name="inventory_id[]" value="{{$req->inventory_id}}">

                    <div class="col-md-3 align-self-center">
                        {{ $req->inventory->name }}
                    </div>

                    <div class="col-md-3 text-center align-self-center">
                        {{ formatJumlah($req->jumlah) }} 
                        {{ $req->inventory->type === "Bahan Baku" ? 'gram' : 'pcs' }}
                    </div>

                    <div class="col-md-3">
                        @php
                            $totalDiberikan = $req->stockOuts->sum('jumlah');
                        @endphp
                        <input type="text" 
                            name="jumlah_diberikan[]" 
                            value="{{ $totalDiberikan }}" 
                            class="form-control border-1 border px-2 text-center" 
                            placeholder="0" required readonly>
                    </div>

                    <div class="col-md-3">
                        <select name="stock_in_id[{{$i}}][]" class="selectPenyerahan form-control border border-1 form-control-sm" multiple disabled>
                            @foreach($req->stockOuts as $so)
                                @if($so->stockIn)
                                    <option value="{{ $so->stockIn->id }}" selected>
                                        {{ $so->stockIn->nomor_batch }} 
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="my-3">
            <strong>Keterangan:</strong>
            <textarea class="form-control border border-1 py-1 px-2" name="keterangan" rows="3" readonly
                placeholder="*(Opsional)">{{ $requestData->first()->keterangan ?? ($produksi->product->name . ' ' . $produksi->batch_number) }}</textarea>
            <small class="text-danger">Keterangan Sudah Otomatis Terisi. Ubah Jika Ada Penyesuaian.</small>
        </div>
        <div class="text-end">
            <a href="{{url()->previous()}}"
                class="btn btn-secondary text-end m-0">Kembali</a>
        </div>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
<script>
$(document).ready(function() {
    $('.selectPenyerahan').select2({
        placeholder: "Pilih Batch",
        allowClear: true,
        width: '100%'
    });
});
</script>
@endsection