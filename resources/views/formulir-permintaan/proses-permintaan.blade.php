@extends('layouts.app')
@section('title', 'Komitkami Helper | Proses Permintaan Material')
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
                <strong>Tanggal: {{ now()->format('d-m-Y') }}</strong>
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
        <form id="form-penyerahan" action="{{url('/store-penyerahan')}}" method="post">
            @csrf
            <div class="row g-4 overflow-scroll">
                <div class="col-md-3 text-dark"><strong>Nama Material</strong></div>
                <div class="col-md-3 text-dark text-center"><strong>Jumlah Diperlukan</strong></div>
                <div class="col-md-3 text-dark text-center"><strong>Jumlah Diberikan</strong></div>
                <div class="col-md-3 text-dark text-center"><strong>Batch Pengambilan</strong></div>
                @foreach($requestData as $i => $req)
                <!-- permintaan id & inventory for stock out -->
                <input type="hidden" name="permintaan_id[]" value="{{$req->id}}">
                <input type="hidden" name="inventory_id[]" value="{{$req->inventory->id}}">
                <div class="col-md-3 align-self-center">
                    {{$req->inventory->name}}
                </div>
                <div class="col-md-3 text-end align-self-center">
                    {{formatJumlah($req->jumlah)}} {{$req->inventory->type === "Bahan Baku" ? 'gram' : 'pcs'}} 
                </div>
                <!-- jumlah stock out -->
                <div class="col-md-3">
                    <input type="text" name="jumlah[]" class="form-control border-1 border px-2" placeholder="Jumlah Penyerahan" required>
                </div>
                <div class="col-md-3">
                    @if ($req->inventory->type != "Bahan Baku")
                        @php
                            $totalAvailable = collect($stockInData[$req->inventory_id] ?? [])->sum('available');
                        @endphp
                        <input type="text" class="form-control border-1 border px-2 text-center" value="Tersedia: {{ formatJumlah($totalAvailable) }}" readonly>
                    @else
                        <select name="stock_in_id[{{$i}}][]"
                            class="selectPenyerahan form-control border border-1 form-control-sm" multiple>
                            @foreach($stockInData[$req->inventory_id] ?? [] as $stock)
                            <option value="{{ $stock->id }}"
                                    data-plain="{{ $stock->nomor_batch }}">
                                {{ $stock->nomor_batch }} (tersedia : {{ formatJumlah($stock->available) }})
                            </option>
                            @endforeach
                        </select>
                    @endif
                </div>
                @endforeach
            </div>
            <div class="my-3">
                <strong>Keterangan:</strong>
                <textarea class="form-control border border-1 py-1 px-2" name="keterangan" rows="3" 
                    placeholder="*(Opsional)">{{ $requestData->first()->keterangan ?? ($produksi->product->name . ' ' . $produksi->batch_number) }}</textarea>
                <small class="text-danger">Keterangan Sudah Otomatis Terisi. Ubah Jika Ada Penyesuaian.</small>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary text-end m-0">Simpan</button>
            </div>
        </form>
    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
<script>
$(document).ready(function() {
    $('.selectPenyerahan').select2({
        placeholder: "Pilih Nomo Batch",
        allowClear: true,
        width: '100%',
        templateSelection: function (data) {
            return $(data.element).data('plain'); // tampil setelah dipilih
        }
    });

    $('#form-penyerahan').on('submit', function(e) {
        // Jika form tidak valid (misal ada required yang kosong), biarkan browser menangani
        if (!this.checkValidity()) {
            return;
        }

        // Cegah double klik dengan mendisable tombol
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        
        // Munculkan loading overlay untuk mencegah klik area manapun
        if ($('#loading-overlay').length === 0) {
            $('body').append('<div id="loading-overlay" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); z-index:9999; display:flex; justify-content:center; align-items:center; flex-direction:column;"><div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status"><span class="visually-hidden"></span></div><h5 class="text-white mt-3">Sedang memproses...</h5></div>');
        }
    });
});
</script>
@endsection