@extends('layouts.app')
@section('title', 'Komitkami Helper | Proses Permintaan Material')
@section('content')
<div class="container">
    <div class="card p-3">
        @if (session('error'))
            <div class="alert alert-danger text-white mb-3 shadow" role="alert">
                <strong><i class="material-icons-round align-middle me-1">error</i> Peringatan:</strong> {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success text-white mb-3 shadow" role="alert">
                <strong><i class="material-icons-round align-middle me-1">check_circle</i> Sukses:</strong> {{ session('success') }}
            </div>
        @endif

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
                {{ optional(optional($produksi)->product)->name }} - {{ optional($produksi)->batch_number }}
                @else
                {{ optional($requestData->first()->user)->name ?? 'User' }} - {{ $requestData->first()->keterangan }}
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
                    <input type="text" name="jumlah[]" 
                        class="form-control border-1 border px-2 input-jumlah" 
                        placeholder="Jumlah Penyerahan" 
                        value="{{ formatJumlah($req->jumlah) }}"
                        data-index="{{ $i }}"
                        data-name="{{ $req->inventory->name }}"
                        data-type="{{ $req->inventory->type }}"
                        required>
                </div>
                <div class="col-md-3">
                    @if ($req->inventory->type != "Bahan Baku")
                        @php
                            $totalAvailable = collect($stockInData[$req->inventory_id] ?? [])->sum('available');
                        @endphp
                        <input type="text" class="form-control border-1 border px-2 text-center" value="Tersedia: {{ formatJumlah($totalAvailable) }}" readonly>
                    @else
                        <select name="stock_in_id[{{$i}}][]"
                            class="selectPenyerahan form-control border border-1 form-control-sm" 
                            multiple
                            data-index="{{ $i }}"
                            data-name="{{ $req->inventory->name }}">
                            @foreach($stockInData[$req->inventory_id] ?? [] as $stock)
                            <option value="{{ $stock->id }}"
                                    data-plain="{{ $stock->nomor_batch }}"
                                    data-available="{{ $stock->available }}">
                                {{ $stock->nomor_batch }} (tersedia : {{ formatJumlah($stock->available) }})
                            </option>
                            @endforeach
                        </select>
                        <div class="batch-status-box mt-1" id="batch-status-{{ $i }}">
                            <small class="text-secondary"><i class="material-icons-round align-middle" style="font-size: 14px;">info</i> Silakan pilih batch pengambilan</small>
                        </div>
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
    function parseNumberJs(val) {
        if (!val) return 0;
        let str = val.toString().trim();
        if (/^\d{1,3}(\.\d{3})+$/.test(str)) {
            str = str.replace(/\./g, '');
        } else if (str.includes('.') && str.includes(',')) {
            str = str.replace(/\./g, '').replace(',', '.');
        } else if (str.includes(',')) {
            str = str.replace(',', '.');
        }
        let num = parseFloat(str);
        return isNaN(num) ? 0 : num;
    }

    function formatJumlahJs(angka) {
        if (angka === undefined || angka === null || isNaN(angka)) return '0';
        let parts = angka.toFixed(3).split('.');
        let intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        let decPart = parts[1].replace(/0+$/, '');
        return decPart.length > 0 ? intPart + ',' + decPart : intPart;
    }

    function updateBatchStatus(index) {
        const inputJumlah = $('input.input-jumlah[data-index="' + index + '"]');
        if (inputJumlah.length === 0 || inputJumlah.data('type') !== 'Bahan Baku') {
            return { valid: true };
        }

        const selectEl = $('select.selectPenyerahan[data-index="' + index + '"]');
        const statusBox = $('#batch-status-' + index);
        const needed = parseNumberJs(inputJumlah.val());

        let totalAvailable = 0;
        const selectedOptions = selectEl.find('option:selected');

        selectedOptions.each(function() {
            const avail = parseFloat($(this).data('available')) || 0;
            totalAvailable += avail;
        });

        const select2Container = selectEl.next('.select2-container').find('.select2-selection');

        if (selectedOptions.length === 0) {
            statusBox.html('<small class="text-secondary"><i class="material-icons-round align-middle" style="font-size: 14px;">info</i> Silakan pilih batch pengambilan</small>');
            select2Container.css('border-color', '');
            return { valid: true, empty: true, needed: needed, available: 0 };
        }

        if (needed > 0 && totalAvailable < needed) {
            const selisih = needed - totalAvailable;
            statusBox.html('<small class="text-danger fw-bold"><i class="material-icons-round align-middle" style="font-size: 14px;">error</i> Stok batch terpilih: ' + formatJumlahJs(totalAvailable) + ' gram (Kurang: ' + formatJumlahJs(selisih) + ' gram)</small>');
            select2Container.css('border', '1px solid #dc3545');
            return { valid: false, reason: 'insufficient', needed: needed, available: totalAvailable };
        } else {
            statusBox.html('<small class="text-success fw-bold"><i class="material-icons-round align-middle" style="font-size: 14px;">check_circle</i> Stok batch mencukupi (Terpilih: ' + formatJumlahJs(totalAvailable) + ' gram)</small>');
            select2Container.css('border', '1px solid #198754');
            return { valid: true, needed: needed, available: totalAvailable };
        }
    }

    $('.selectPenyerahan').select2({
        placeholder: "Pilih Nomor Batch (Opsional)",
        allowClear: true,
        width: '100%',
        templateSelection: function (data) {
            if (!data.id) return data.text;
            const plain = $(data.element).data('plain') || data.text;
            const avail = $(data.element).data('available');
            if (avail !== undefined && avail !== null && avail !== '') {
                return plain + ' (tersedia: ' + formatJumlahJs(parseFloat(avail)) + ')';
            }
            return plain;
        }
    });

    // Event listener saat batch dipilih / diubah
    $('.selectPenyerahan').on('change', function() {
        const idx = $(this).data('index');
        updateBatchStatus(idx);
    });

    // Event listener saat jumlah penyerahan diubah
    $('input.input-jumlah').on('input change', function() {
        const idx = $(this).data('index');
        updateBatchStatus(idx);
    });

    // Jalankan validasi visual awal untuk setiap baris batch
    $('.selectPenyerahan').each(function() {
        const idx = $(this).data('index');
        updateBatchStatus(idx);
    });

    $('#form-penyerahan').on('submit', function(e) {
        // Jika form tidak valid (misal ada required yang kosong), biarkan browser menangani
        if (!this.checkValidity()) {
            return;
        }

        let hasError = false;
        let errorMessages = [];

        $('select.selectPenyerahan').each(function() {
            const idx = $(this).data('index');
            const name = $(this).data('name') || 'Material';
            const status = updateBatchStatus(idx);

            // Hanya tolak submit jika user memilih batch namun stok batch tersebut tidak mencukupi
            if (!status.valid && status.reason === 'insufficient') {
                hasError = true;
                errorMessages.push('• Stok batch untuk "' + name + '" tidak mencukupi!\n  - Dibutuhkan: ' + formatJumlahJs(status.needed) + ' gram\n  - Terpilih: ' + formatJumlahJs(status.available) + ' gram');
            }
        });

        if (hasError) {
            e.preventDefault();
            alert("Mohon periksa kembali ketersediaan batch material:\n\n" + errorMessages.join("\n\n") + "\n\nSilakan pilih batch tambahan atau sesuaikan jumlah diberikan.");
            return false;
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
