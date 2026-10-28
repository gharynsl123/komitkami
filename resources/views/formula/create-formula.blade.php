@extends('layouts.app')
@section('title', 'Komitkami | Buat Formula Produk')
@section('content')
<style>
.card-color-custome {
    background-color: #A9A9A943;
}
</style>

<div class="card p-3">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs border-0 border-bottom" id="formulaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="text-dark nav-link active" id="formula-tab" data-bs-toggle="tab" data-bs-target="#formula" type="button" role="tab" aria-controls="formula" aria-selected="true">1. Data Formula</button>
        </li>
        <li class="nav-item procedural-tab" role="presentation">
            <button class="text-dark nav-link" id="tahapan-tab" data-bs-toggle="tab" data-bs-target="#tahapan" type="button" role="tab" aria-controls="tahapan" aria-selected="false">2. Tahapan Proses</button>
        </li>
        <li class="nav-item procedural-tab" role="presentation">
            <button class="text-dark nav-link" id="qc-tab" data-bs-toggle="tab" data-bs-target="#qc" type="button" role="tab" aria-controls="qc" aria-selected="false">3. Quality Control</button>
        </li>
        <li class="nav-item procedural-tab" role="presentation">
            <button class="text-dark nav-link" id="ipc-tab" data-bs-toggle="tab" data-bs-target="#ipc" type="button" role="tab" aria-controls="ipc" aria-selected="false">4. Acuan Laporan IPC</button>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content" id="formulaTabsContent">
        <!-- TAB 1: FORMULA -->
        <div class="tab-pane fade show active mt-3" id="formula" role="tabpanel" aria-labelledby="formula-tab">
            <form action="{{url('/store-formula')}}" class="overflow-scroll" id="form-formula" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="card p-3 shadow-none rounded card-color-custome text-dark">
                            <p class="m-0">Formula ini di buat berdasarkan 100 kg batch sebuah produk itu</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="m-0">Masukkan nama formula produk</label>
                        <input type="text" name="nama_template" class="form-control px-2 py-1 border"
                            placeholder="Name" required>
                    </div>
                    <div class="col-md-4">
                        <label class="m-0">Pilih Type Formula</label>
                        <select id="type" name="type" class="form-select px-2 py-1 border" required>
                            <option value="">Pilih Tipe</option>
                            <option value="Bahan Baku">Bahan Baku</option>
                            <option value="Bahan Pelengkap">Bahan Pelengkap</option>
                        </select>
                    </div>
                </div>
    
                <div class="bahan-baku mt-3">
                    <div class="const row">
                        <input type="text" name="inventory_id[]" hidden class="idInventory">
                        <div class="col-md-3 d-flex flex-column">
                            <label class="m-0">Pilih Persediaan Bahan</label>
                            <select name="nama_bahan_baku[]" class="dynamic-select-bahan">
                                <option>-- Pilih persediaan --</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="m-0">Tipe Persediaan Bahan</label>
                            <input type="text" readonly class="px-2 py-1 form-control border typePersediaan">
                        </div>
                        <div class="col-md-5">
                            <label class="m-0" for="">Nominal Dibutuhkan</label>
                            <div class="d-flex align-items-center">
                                <input class="form-control px-2 py-1 border" name="jumlah[]">
                                <span class="px-2 py-1 border bg-light rounded ms-2 spanSatuan">satuan</span>
                                <input type="hidden" name="satuan[]" class="typeSatuan">
                            </div>
                        </div>
                        <div class="col-md-1 d-flex justify-content-end align-items-center">
                            <button type="button" class="addmultiplealat btn btn-success mt-4">
                                <span class="material-icons">add</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4 p-0 mx-0">
                    <a href="{{url('/formula-produk')}}" class="m-0 btn btn-secondary btn-sm">Batal</a>
                    <button type="submit" id="btn-save-formula" class="m-0 btn btn-primary btn-sm">Simpan & Lanjut</button>
                </div>
            </form>
        </div>
    
        <!-- TAB 2: TAHAPAN PROSES -->
        <div class="tab-pane fade mt-3" id="tahapan" role="tabpanel" aria-labelledby="tahapan-tab">
            <form id="form-tahapan">
                @csrf
                <input type="hidden" name="formula_id" class="hidden_formula_id">
                <div class="tahapan-proses">
                    <div class="const-proses row mt-2">
                        <div class="col-md-5">
                            <label class="m-0">Nama Proses</label>
                            <input type="text" name="nama_proses[]" class="form-control px-2 py-1 border">
                        </div>
                        <div class="col-md-6">
                            <label class="m-0">Acuan</label>
                            <input type="text" name="acuan[]" class="form-control px-2 py-1 border">
                        </div>
                        <div class="col-md-1 d-flex justify-content-end align-items-center">
                            <button type="button" class="add-proses btn btn-success mt-4">
                                <span class="material-icons">add</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4 p-0 mx-0">
                    <button type="submit" class="m-0 btn btn-primary btn-sm">Simpan & Lanjut</button>
                </div>
            </form>
        </div>
    
        <!-- TAB 3: QUALITY CONTROL -->
        <div class="tab-pane fade mt-3" id="qc" role="tabpanel" aria-labelledby="qc-tab">
            <form id="form-qc">
                @csrf
                <input type="hidden" name="formula_id" class="hidden_formula_id">
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Quality Control</h5>
                    @php
                    $parameters = ['Organoleptik', 'Volume', 'pH', 'Viskositas', 'Uji DDH', 'Uji Antibacteri : E. Coli', 'Uji Antibacteri : S. Aureus', 'Uji Antifungi : C. albicans'];
                    @endphp
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%">Parameter</th>
                                    <th style="width: 35%">Spesifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parameters as $param)
                                <tr>
                                    <td class="fw-semibold">
                                        <input type="text" 
                                            name="parameter_pengujian[]"
                                            class="form-control form-control-sm"
                                            value="{{ $param }}"
                                            readonly>
                                    </td>
                                    <td>
                                        <input type="text" 
                                            name="nilai_standart[]"
                                            class="form-control form-control-sm border border-1"
                                            placeholder="Masukkan nilai standart">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4 p-0 mx-0">
                    <button type="submit" class="m-0 btn btn-primary btn-sm">Simpan & Lanjut</button>
                </div>
            </form>
        </div>
    
        <!-- TAB 4: ACUAN LAPORAN IPC -->
        <div class="tab-pane fade mt-3" id="ipc" role="tabpanel" aria-labelledby="ipc-tab">
            <form id="form-ipc">
                @csrf
                <input type="hidden" name="formula_id" class="hidden_formula_id">
                {{-- SECTION 1: INSPEKSI PROSES PRODUKSI --}}
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Inspeksi Proses Produksi</h5>
                    @php
                    $sectionName = 'Inspeksi Proses Produksi';
                    $parameters = ['Bentuk', 'Warna', 'Rasa', 'pH', 'Viskositas'];
                    @endphp

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%">Parameter</th>
                                    <th style="width: 35%">Spesifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parameters as $param)
                                <tr>
                                    <td class="fw-semibold">{{ $param }}</td>
                                    <td>
                                        <input type="text" 
                                            name="sections[{{ $sectionName }}][{{ $param }}][spesifikasi]"
                                            class="form-control form-control-sm border border-1"
                                            placeholder="Spesifikasi {{ strtolower($param) }}"
                                            value="{{ $sections[$sectionName][$param]['spesifikasi'] ?? '' }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SECTION 2: REPORT IN PROCESS CONTROL --}}
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Report In Process Control</h5>
                    @php
                    $sectionName = 'Report In Process Control';
                    $parameters = ['Kesetaraan Bobot.', 'Uji Kebocoran kemasan', 'Uji Kerapatan Tutup/Sagel'];
                    $defaultValues = [
                        'Kesetaraan Bobot.' => '± 10 mL', 
                        'Uji Kebocoran kemasan' => 'Tidak Bocor', 
                        'Uji Kerapatan Tutup/Sagel' => 'Tutup Rapat'
                    ];
                    @endphp

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%">Parameter</th>
                                    <th style="width: 35%">Spesifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parameters as $param)
                                <tr>
                                    <td class="fw-semibold">{{ $param }}</td>
                                    <td>
                                        <input type="text" 
                                            name="sections[{{ $sectionName }}][{{ $param }}][spesifikasi]"
                                            class="form-control form-control-sm border border-1"
                                            placeholder="Spesifikasi {{ strtolower($param) }}"
                                            value="{{ $defaultValues[$param] }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SECTION 3: PENGUJIAN PRODUK JADI (TANPA HASIL) --}}
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Pengujian Produk Jadi</h5>
                    @php
                    $sectionName = 'Pengujian Produk Jadi';
                    $parameters = ['Volume', 'Jumlah', 'Penandaan Stiker', 'Uji Antibacteri : E. Coli', 'Uji Antibacteri : S. Aureus', 'Uji Antifungi : C. albicans'];
                    $defaultValues = [
                        'Volume' => 'otomatis', 
                        'Jumlah' => 'otomatis', 
                        'Penandaan Stiker' => 'sesuai',
                        'Uji Antibacteri : E. Coli' => '≥  Log',
                        'Uji Antibacteri : S. Aureus' => '≥  Log',
                        'Uji Antifungi : C. albicans' => '≥  Log'
                    ];
                    @endphp

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%">Parameter</th>
                                    <th style="width: 35%">Spesifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parameters as $param)
                                <tr>
                                    <td class="fw-semibold">{{ $param }}</td>
                                    <td>
                                        <input type="text" 
                                            name="sections[{{ $sectionName }}][{{ $param }}][spesifikasi]"
                                            class="form-control form-control-sm border border-1"
                                            placeholder="Spesifikasi {{ strtolower($param) }}"
                                            value="{{ $defaultValues[$param] }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- tombol submit --}}
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary btn-sm m-0">Simpan (selesai)</button>
                </div>
            </form>
        </div>
    
    </div>
</div>

<script>
function addPeralatan() {
    let datamultipleinputalat = `
        <div class="row mt-3 const">
            <div class="col-md-3 d-flex flex-column">
                <label class="m-0">Pilih Persediaan Bahan</label>
                <select name="nama_bahan_baku[]" class="dynamic-select-bahan">
                    <option>-- Pilih persediaan --</option>
                    @foreach($inventory as $row)
                    <option value="{{$row->name}}" data-type="{{$row->type}}" data-id="{{$row->id}}">
                        {{$row->name}} - {{$row->type}}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="inventory_id[]" class="idInventory">
            <div class="col-md-3">
                <label class="m-0">Tipe Persediaan Bahan</label>
                <input type="text" readonly class="form-control border py-1 px-2 typePersediaan">
            </div>
            <div class="col-md-5">
                <label class="m-0">Nominal yang Dibutuhkan</label>
                <div class="d-flex align-items-center">
                    <input type="text" class="form-control px-2 py-1 border" name="jumlah[]">
                    <span class="px-2 py-1 border bg-light rounded ms-2 spanSatuan">satuan</span>
                    <input type="hidden" name="satuan[]" class="typeSatuan">
                </div>
            </div>
            <div class="col-md-1 d-flex justify-content-end align-items-center">
                <button type="button" class="btn btn-danger remove-alat mt-4">
                    <span class="material-icons">remove</span>
                </button>
            </div>
        </div>
    `;
    
    // Append elemen baru
    let newElement = $(datamultipleinputalat).appendTo('.bahan-baku');

    // Initialize select2 untuk elemen baru
    newElement.find('.dynamic-select-bahan').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Pilih persediaan --'
    });

    // Terapkan filter type ke elemen baru (optional: jika type sudah dipilih)
    let selectedType = $('#type').val();
    if(selectedType) {
        filterDynamicSelect(newElement.find('.dynamic-select-bahan'), selectedType);
    }
}

function addProses() {
    let html = `
        <div class="const-proses row mt-2">
            <div class="col-md-5">
                <label class="m-0">Nama Proses</label>
                <input type="text" name="nama_proses[]" class="form-control px-2 py-1 border">
            </div>
            <div class="col-md-6">
                <label class="m-0">Acuan</label>
                <input type="text" name="acuan[]" class="form-control px-2 py-1 border">
            </div>
            <div class="col-md-1 d-flex justify-content-end align-items-center">
                <button type="button" class="btn btn-danger remove-proses mt-4">
                    <span class="material-icons">remove</span>
                </button>
            </div>
        </div>
    `;
    $(html).appendTo('.tahapan-proses');
}

// Fungsi filter untuk dynamic select
function filterDynamicSelect(selectElement, selectedType) {
    let filteredOptions = [];
    if (selectedType === 'Bahan Pelengkap') {
        filteredOptions = $inventory.filter(item => item.type !== 'Bahan Baku');
    } else {
        filteredOptions = $inventory.filter(item => item.type === selectedType);
    }
    let currentValue = selectElement.val();

    selectElement.empty().append('<option>-- Pilih persediaan --</option>');
    filteredOptions.forEach(function(item) {
        let isSelected = item.name === currentValue ? 'selected' : '';
        selectElement.append(`<option value="${item.name}" data-type="${item.type}" data-id="${item.id}" ${isSelected}>${item.name} - ${item.type}</option>`);
    });
}

// Event listeners
document.addEventListener("DOMContentLoaded", function() {
    $inventory = @json($inventory);

    // Remove row
    $(document).on('click', '.remove-alat', function() {
        $(this).closest('.const').remove();
    });

    $(document).on('click', '.remove-proses', function() {
        $(this).closest('.const-proses').remove();
    });

    $(document).on('click', '.remove-qc', function() {
        $(this).closest('.const-qc').remove();
    });

    $(document).on('click', '.remove-ipc', function() {
        $(this).closest('.const-ipc').remove();
    });

    // Filter select by type
    $(document).on('change', '#type', function() {
        let selectedType = $(this).val();
        $('.dynamic-select-bahan').each(function() {
            filterDynamicSelect($(this), selectedType);
        });
    });

    // Update hidden fields & satuan saat select berubah
    $(document).on('change', '.dynamic-select-bahan', function() {
        let selectedOption = $(this).find(':selected');
        let inventoryId = selectedOption.data('id');
        let type = selectedOption.data('type');
        let parent = $(this).closest('.const');

        parent.find('.idInventory').val(inventoryId || '');
        parent.find('.typePersediaan').val(type || '');

        let satuan = type === "Bahan Baku" ? "gram" : (type === "Bahan Kemasan" || type === "Bahan Kemas Label" ? "pcs" : '');
        parent.find('.spanSatuan').text(satuan);
        parent.find('.typeSatuan').val(satuan);
    });

    // Tambah row baru
    $(document).on('click', '.addmultiplealat', function() {
        addPeralatan();
    });

    $(document).on('click', '.add-proses', function() {
        addProses();
    });

    // Initialize select2 untuk select awal
    $('.dynamic-select-bahan').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Pilih persediaan --'
    });

    // Initialize Select2 for product_id
    $('#product_id').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Pilih Produk'
    });

    // Handle Conditional Tabs based on Type
    function handleTabsVisibility() {
        let type = $('#type').val();
        if (type === 'Bahan Pelengkap') {
            $('.procedural-tab').hide();
            $('#btn-save-formula').text('Simpan Semua (Selesai)');
        } else {
            $('.procedural-tab').show();
            $('#btn-save-formula').text('Simpan & Lanjut');
        }
    }

    $('#type').on('change', handleTabsVisibility);
    handleTabsVisibility();

    // AJAX for Form Formula
    $('#form-formula').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    if ($('#type').val() === 'Bahan Pelengkap') {
                        // redirect on finish
                        window.location.href = "{{url('/formula-produk')}}";
                    } else {
                        // populate hidden IDs
                        $('.hidden_formula_id').val(response.formula_id);
                        // unlock next tab
                        $('#tahapan-tab').removeClass('disabled').tab('show');
                        submitBtn.prop('disabled', false).text('Tersimpan');
                    }
                }
            },
            error: function(err) {
                alert("Terjadi kesalahan saat menyimpan data formula.");
                submitBtn.prop('disabled', false).text('Simpan & Lanjut');
            }
        });
    });

    // AJAX for Form Tahapan Proses
    $('#form-tahapan').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let formulaId = form.find('.hidden_formula_id').val();
        if (!formulaId) return alert('Data Formula belum disimpan.');

        let submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{url('/store-tahapan-proses')}}/" + formulaId,
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if(response.status === 'success') {
                    $('#qc-tab').removeClass('disabled').tab('show');
                    submitBtn.prop('disabled', false).text('Tersimpan');
                }
            },
            error: function() {
                alert("Gagal menyimpan Tahapan Proses.");
                submitBtn.prop('disabled', false).text('Simpan & Lanjut');
            }
        });
    });

    // AJAX for Form Quality Control
    $('#form-qc').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let formulaId = form.find('.hidden_formula_id').val();
        if (!formulaId) return alert('Data Formula belum disimpan.');

        let submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{url('/store-quality-control')}}/" + formulaId,
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if(response.status === 'success') {
                    $('#ipc-tab').removeClass('disabled').tab('show');
                    submitBtn.prop('disabled', false).text('Tersimpan');
                }
            },
            error: function() {
                alert("Gagal menyimpan Quality Control.");
                submitBtn.prop('disabled', false).text('Simpan & Lanjut');
            }
        });
    });

    // AJAX for Form IPC
    $('#form-ipc').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let formulaId = form.find('.hidden_formula_id').val();
        if (!formulaId) return alert('Data Formula belum disimpan.');

        let submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{url('/store-acuan-laporan-ipc')}}/" + formulaId,
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if(response.status === 'success') {
                    window.location.href = "{{url('/formula-produk')}}";
                }
            },
            error: function() {
                alert("Gagal menyimpan Acuan Laporan IPC.");
                submitBtn.prop('disabled', false).text('Simpan Semua (Selesai)');
            }
        });
    });

});
</script>

<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
@endsection