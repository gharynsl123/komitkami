@extends('layouts.app')
@section('title', 'Komitkami Helper | Input Stok Barang')
@section('content')
<style>
.detail-card {
    padding: 14px 16px;
    background: #f8f9fb;
    border-radius: 10px;
    min-height: 70px;
}

.detail-label {
    font-size: 13px;
    color: #7b8499;
    margin-bottom: 5px;
}

.detail-value {
    font-size: 15px;
    font-weight: 500;
    color: #30384a;
    word-break: break-all;
    white-space: normal; /* allow text to break into new lines */
    overflow-wrap: break-word; /* break long words if necessary */
}
</style>
<div class="card p-3 mb-3" id="div-form">
    <h6 class="mb-4">Proses Input stok barang</h6>

    <form action="{{ url('/store-transaction') }}" class="row g-3" method="POST" autocomplete="off">
        @csrf

        <!-- User / Admin -->
        <div class="col-md-6">
            <label class="form-label small fw-bold">Petugas / User <span class="text-danger">*</span></label>
            <div class="input-group input-group-outline">
                <input readonly value="{{ Auth::user()->name }}" class="form-control bg-light">
                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
            </div>
        </div>
        
        <!-- Nama Penyerah -->
        <div class="col-md-6">
            <label class="form-label small fw-bold">Nama Penyerah <span class="text-danger">*</span></label>
            <div class="input-group input-group-outline">
                <input name="nama_penyerah" type="text" id="nama_penyerah" class="form-control" placeholder="Masukkan Nama Penyerah" required>
            </div>
        </div>

        <!-- Nama Material -->
        <div class="col-md-6">
            <label class="form-label small fw-bold">Nama Material <span class="text-danger">*</span></label>
            <div class="input-group input-group-outline">
                <select name="inventory_id" class="form-control" id="select-material" required>
                    <option value="">-- Pilih Material --</option>
                    @foreach($inventory as $row)
                        <option value="{{ $row->id }}" data-type="{{ $row->type }}">{{ $row->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Supplier -->
        <div class="col-md-6">
            <label class="form-label small fw-bold">Nama Supplier <span class="text-danger">*</span> (Jangan Ketik Manual)</label>
            <div class="input-group input-group-outline">
                <select name="vendor" class="form-control" id="pilih-vendor" required>
                    <option value="">-- Pilih Supplier --</option>
                </select>
            </div>
            <input type="hidden" name="suplier_material_id" id="supplier_id">
        </div>

        <!-- Nomor Batch -->
        <div class="col-md-6" id="batch-container">
            <label class="form-label small fw-bold">Nomor Batch</label>
            <div class="input-group input-group-outline">
                <input type="text" name="nomor_batch" id="input_nomor_batch" class="form-control" placeholder="Masukkan nomor batch">
            </div>
            <small class="text-muted d-block mt-1">*Optional</small>
        </div>

        <!-- Running Stock (Tampil Dinamis) -->
        <div class="col-md-6" id="stock-info-container" style="display: none;">
            <label class="form-label small fw-bold">Stok Tersedia Saat Ini</label>
            <div class="input-group input-group-outline">
                <input type="text" id="stok_tersedia_info" class="form-control bg-light" readonly placeholder="Loading stok...">
            </div>
            <small class="text-info d-block mt-1">Running Stock</small>
        </div>

        <!-- Jumlah Barang Masuk -->
        <div class="col-md-6">
            <label class="form-label small fw-bold">Jumlah Barang Masuk <span class="text-danger">*</span></label>
            <div class="input-group input-group-outline">
                <input type="number" step="any" name="stok" class="form-control" placeholder="Contoh: 10 atau 10.5" required>
                <!-- Addon Satuan di Sisi Kanan -->
                <span class="input-group-text bg-light text-muted p-2" id="label-satuan">Unit</span>
            </div>
            <small class="text-muted d-block mt-1">*Gunakan titik (.) untuk desimal</small>
        </div>

        <!-- Tanggal Transaksi -->
        <div class="col-md-6">
            <label class="form-label small fw-bold">Tanggal Transaksi <span class="text-danger">*</span></label>
            <div class="input-group input-group-outline">
                <input name="tanggal_masuk" id="date" type="date" id="tanggal_masuk" class="form-control" required>
            </div>
        </div>

        <!-- Tanggal Expired -->
        <div class="col-md-6">
            <label class="form-label small fw-bold">Tanggal Expired (ED)</label>
            <div class="input-group input-group-outline">
                <input name="tanggal_ed" type="date" id="tanggal_ed" class="form-control">
            </div>
            <small class="text-muted d-block mt-1">*Optional</small>
        </div>

        <!-- Nomor PO -->
        <div class="col-md-6">
            <label class="form-label small fw-bold">Nomor PO / Surat Jalan</label>
            <div class="input-group input-group-outline">
                <input type="text" name="nomor_sj" class="form-control" placeholder="Masukkan Nomor PO / SJ">
            </div>
        </div>

        <!-- Keterangan -->
        <div class="col-md-6">
            <label class="form-label small fw-bold">Keterangan</label>
            <div class="input-group input-group-outline">
                <input type="text" name="keterangan" class="form-control" placeholder="Tambahkan catatan jika ada">
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="col-12 mt-4 d-flex align-items-center gap-2">
            <button type="submit" class="btn btn-primary px-4 m-0">
                <i class="fas fa-save me-1"></i> Buat Transaksi
            </button>
            <div class="d-flex align-items-stretch rounded" style="border: 1px solid #000;">
                <select id="print_year" class="form-select border-0 bg-transparent shadow-none px-2" style="width: 70px; cursor: pointer;">
                    <option value="">All</option>
                    @php $currentYear = date('Y'); @endphp
                    @for ($i = $currentYear; $i >= $currentYear - 5; $i--)
                        <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                <div style="width: 1px; background-color: #000;"></div>
                <button type="button" class="btn m-0 border-0 text-dark" onclick="printReport()">
                    Print Buku penerimaan barang
                </button>
            </div>
        </div>
    </form>
</div>

<div class="text-end mt-3" style="display:none;" id="create-form">
    <button type="button" class="btn btn-dark">Input Stok
        Barang
    </button>
</div>

@include('widget.table-in')

<script>
$(document).ready(function() {
    // select inventory items
    $('#select-material').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Nama Material --'
    });

    $('#select-material').on('change', function() {
        var inventoryId = $(this).val();
        
        if (inventoryId) {
            var selectedType = $(this).find(':selected').data('type');

            if (selectedType !== 'Bahan Baku') {
                $('#label-satuan').text('Pcs');
            } else {
                $('#label-satuan').text('Gram'); // default jika ada tipe lain
            }
            
            // Ambil Data Supplier
            $.ajax({
                url: '/get-supliers/' + inventoryId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);

                    $('#pilih-vendor').empty();
                    $('#pilih-vendor').append('<option value="">Nama Supplier</option>');

                    let suppliers = response.supliers || response;

                    $.each(suppliers, function(index, supplier) {
                        $('#pilih-vendor').append(
                            `<option value="${supplier.name}" data-id="${supplier.id}">
                                ${supplier.name}
                            </option>`
                        );
                    });

                    $('#pilih-vendor').trigger('change');

                    $('#pilih-vendor').on('change', function() {
                        let supplierId = $(this).find(':selected').data('id');
                        $('#supplier_id').val(supplierId);
                    });
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
            
            // Bahan Kemas Handler
            if (selectedType !== 'Bahan Baku') {
                $('#batch-container').hide();
                $('#input_nomor_batch').val('');
                $('#stock-info-container').show();
                $('#stok_tersedia_info').val('Loading stok...');
                
                // Fetch total stock
                $.ajax({
                    url: '/stock-opname/get-batches/inventory/' + inventoryId,
                    type: 'GET',
                    success: function(data) {
                        let totalStok = 0;
                        if (data && data.length > 0) {
                            data.forEach(function(batch) {
                                totalStok += parseFloat(batch.available || 0);
                            });
                        }
                        $('#stok_tersedia_info').val(totalStok);
                    },
                    error: function() {
                        $('#stok_tersedia_info').val('Gagal mengambil data stok');
                    }
                });
            } else {
                $('#batch-container').show();
                $('#stock-info-container').hide();
            }

        } else {
            $('#pilih-vendor').empty();
            $('#pilih-vendor').append('<option value="#">Nama Supplier</option>');
            $('#pilih-vendor').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Nama Supplier'
            });
        }
    });

    $('#pilih-vendor').select2({
        tags: true,
        theme: 'bootstrap-5',
        width: '100%',
        createTag: function(params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            };
        },
        insertTag: function(data, tag) {
            data.push(tag);
        }
    });

    const autoDate = document.getElementById("date");
    autoDate.valueAsDate = new Date();
});

function printReport() {
    var year = $('#print_year').val();
    var url = "{{ url('/print-buku-penerimaan-barang') }}";
    if (year) {
        url += "?year=" + year;
    }
    window.open(url, '_blank');
}
</script>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
@endsection