@extends('layouts.app')
@section('title', 'Komitkami | Edit Formula Produk')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Edit Formula: {{ $formula->nama_formula }}</h5>
    <a href="{{ url('/formula-produk') }}" class="btn m-0 btn-secondary">Kembali</a>
</div>

<!-- Nav tabs (UI Only) -->
<ul class="nav nav-tabs" id="formulaTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="text-dark nav-link active" id="formula-tab" data-bs-toggle="tab" data-bs-target="#formula" type="button" role="tab" aria-selected="true">1. Data Formula</button>
    </li>
    <li class="nav-item procedural-tab" role="presentation">
        <button class="text-dark nav-link" id="tahapan-tab" data-bs-toggle="tab" data-bs-target="#tahapan" type="button" role="tab" aria-selected="false">2. Tahapan Proses</button>
    </li>
    <li class="nav-item procedural-tab" role="presentation">
        <button class="text-dark nav-link" id="qc-tab" data-bs-toggle="tab" data-bs-target="#qc" type="button" role="tab" aria-selected="false">3. Quality Control</button>
    </li>
    <li class="nav-item procedural-tab" role="presentation">
        <button class="text-dark nav-link" id="ipc-tab" data-bs-toggle="tab" data-bs-target="#ipc" type="button" role="tab" aria-selected="false">4. Acuan Laporan IPC</button>
    </li>
</ul>

<!-- FORM SINGLE SAVE -->
<form action="{{ url('/update-formula/' . $formula->id) }}" method="POST" id="edit-formula-form">
    @csrf
    @method('PUT')
    <div class="tab-content mt-3" id="formulaTabsContent">

        <!-- TAB 1: FORMULA -->
        <div class="tab-pane fade show active" id="formula" role="tabpanel">
            <div class="card p-4 shadow-sm mb-4">
                <div class="row align-items-center mb-3">
                    <div class="col-md-4">
                        <label class="font-weight-bold">Formula Template Name <span class="text-danger">*</span></label>
                        <input type="text" name="nama_template" value="{{ old('nama_template', $formula->nama_formula) }}" class="form-control border px-3" required autocomplete="off">
                    </div>

                    <div class="col-md-4">
                    <label class="font-weight-bold">Type Formula <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-control border px-3" required readonly>
                            <option value="Bahan Baku" {{ $formula->tipe == 'bahan_baku' ? 'selected' : '' }}>Bahan Baku</option>
                            <option value="Bahan Pelengkap" {{ $formula->tipe == 'bahan_kemas' ? 'selected' : '' }}>Bahan Pelengkap</option>
                        </select>
                    </div>
                </div>


                <!-- DYNAMIC TABLE INGREDIENTS -->
                <div class="mt-4">
                    <h6 class="font-weight-bold mb-3 border-bottom pb-2">Daftar Kebutuhan</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="tabel-bahan">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50%">Nama Bahan <span class="text-danger">*</span></th>
                                    <th width="20%">Jumlah <span class="text-danger">*</span></th>
                                    <th width="20%">Satuan <span class="text-danger">*</span></th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $ingredients = $formula->tipe == 'bahan_baku' ? $formula->bahanBaku : $formula->bahanKemas; @endphp
                                @foreach($ingredients as $index => $item)
                                <tr>
                                    <td>
                                        <input type="hidden" name="bahan_id[]" value="{{ $item->id }}">
                                        <select name="inventory_id[]" class="form-control dynamic-select-bahan" required>
                                            <option value="" disabled>-- Pilih persediaan --</option>
                                            @foreach($inventory as $inv)
                                                <option value="{{ $inv->id }}" data-type="{{ $inv->type }}" data-name="{{ $inv->name }}" {{ $item->inventory_id == $inv->id ? 'selected' : '' }}>
                                                    {{ $inv->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="nama_bahan_baku[]" value="{{ $item->inventory->name ?? $item->name }}" class="hidden-nama-bahan">
                                    </td>
                                    <td>
                                        <input type="text" name="jumlah[]" class="form-control border px-2" value="{{ formatJumlah($item->jumlah) }}" required placeholder="0">
                                    </td>
                                    <td>
                                        <select name="satuan[]" class="form-control border px-2" required>
                                            <option value="gram" {{ $item->satuan == 'gram' ? 'selected' : '' }}>gram</option>
                                            <option value="kg" {{ $item->satuan == 'kg' ? 'selected' : '' }}>kg</option>
                                            <option value="ml" {{ $item->satuan == 'ml' ? 'selected' : '' }}>ml</option>
                                            <option value="liter" {{ $item->satuan == 'liter' ? 'selected' : '' }}>liter</option>
                                            <option value="pcs" {{ $item->satuan == 'pcs' ? 'selected' : '' }}>pcs</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm m-0 remove-row-bahan">
                                            <i class="material-icons opacity-10" style="font-size: 1rem;">delete</i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" id="btn-add-bahan" class="btn btn-secondary btn-sm mt-3">+ Tambah Bahan</button>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="button" class="btn btn-info btn-sm m-0" id="btn-next-tahapan">Lanjut ke Proses</button>
                    <button type="submit" class="btn btn-success btn-sm m-0 d-none btn-simpan-semua">Simpan Perubahan</button>
                </div>
            </div>
        </div>


        <!-- TAB 2: TAHAPAN PROSES -->
        <div class="tab-pane fade" id="tahapan" role="tabpanel">
            <div class="card p-4 shadow-sm mb-4">
                <h6 class="font-weight-bold mb-3 border-bottom pb-2">Tahapan Proses</h6>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="tabel-tahapan">
                        <thead class="bg-light">
                            <tr>
                                <th width="45%">Nama Proses <small class="text-muted">(opsional)</small></th>
                                <th width="45%">Acuan <small class="text-muted">(opsional)</small></th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($formula->tahapanProses) > 0)
                                @foreach($formula->tahapanProses as $proses)
                                <tr>
                                    <td>
                                        <input type="hidden" name="proses_id[]" value="{{ $proses->id }}">
                                        <textarea type="text" name="nama_proses[]" value="{{ $proses->nama_proses }}" class="form-control border px-2">{!! $proses->nama_proses !!}</textarea>
                                    </td>
                                    <td><input type="text" name="acuan[]" value="{{ $proses->acuan }}" class="form-control border px-2"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm m-0 remove-row-tahapan"><i class="material-icons opacity-10" style="font-size: 1rem;">delete</i></button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td><textarea type="text" name="nama_proses[]" class="form-control border px-2"></textarea></td>
                                    <td><input type="text" name="acuan[]" class="form-control border px-2"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm m-0 remove-row-tahapan"><i class="material-icons opacity-10" style="font-size: 1rem;">delete</i></button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <button type="button" id="btn-add-tahapan" class="btn btn-secondary btn-sm mt-3">+ Tambah Proses</button>
                </div>

                <div class="mt-4 text-end">
                    <button type="button" class="btn btn-info btn-sm m-0" id="btn-next-qc">Lanjut ke QC</button>
                </div>
            </div>
        </div>

        <!-- TAB 3: QUALITY CONTROL -->
        <div class="tab-pane fade" id="qc" role="tabpanel">
            <div class="card p-4 shadow-sm mb-4">
                <h6 class="font-weight-bold mb-3 border-bottom pb-2">Parameter Quality Control</h6>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="tabel-qc">
                        <thead class="bg-light">
                            <tr>
                                <th width="45%">Parameter Pengujian <small class="text-muted">(opsional)</small></th>
                                <th width="45%">Nilai Standart <small class="text-muted">(opsional)</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $qc_data = [];

                                foreach($formula->qualityControl as $qc) {
                                    $qc_data[$qc->parameter_pengujian] = [
                                        'nilai_standart' => $qc->nilai_standart
                                    ];
                                }

                                $parameters = [
                                    'Organoleptik',
                                    'Volume',
                                    'pH',
                                    'Viskositas',
                                    'Uji DDH',
                                    'Uji Antibacteri : E. Coli',
                                    'Uji Antibacteri : S. Aureus',
                                    'Uji Antifungi : C. albicans'
                                ];
                            @endphp
                            @foreach($parameters as $param)
                            <tr>
                                <td>
                                    <input type="text"
                                        class="form-control"
                                        value="{{ $param }}"
                                        readonly>
                                </td>
                                <td>
                                    <input type="text"
                                        name="quality_control[{{ $param }}][nilai_standart]"
                                        class="form-control border px-2"
                                        value="{{ $qc_data[$param]['nilai_standart'] ?? '-' }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-end">
                    <button type="button" class="btn btn-info btn-sm m-0" id="btn-next-ipc">Lanjut ke IPC</button>
                </div>
            </div>
        </div>

        <!-- TAB 4: ACUAN LAPORAN IPC -->
        <div class="tab-pane fade" id="ipc" role="tabpanel">
            <div class="card p-4 shadow-sm mb-4">
                <h6 class="font-weight-bold mb-3 border-bottom pb-2">Acuan Laporan IPC</h6>

                @php
                $sections_data = [];
                foreach($formula->acuanLaporanIpc as $ipc) {
                    $sections_data[$ipc->section_name][$ipc->pemeriksaan] = ['spesifikasi' => $ipc->spesifikasi];
                }
                @endphp

                {{-- SECTION 1: INSPEKSI PROSES PRODUKSI --}}
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Inspeksi Proses Produksi</h5>
                    @php
                    $sectionName = 'Inspeksi Proses Produksi';
                    $parameters = ['Bentuk', 'Warna', 'Rasa', 'pH', 'Viskositas'];
                    @endphp

                    <div class="table-responsive">
                        <table class="table align-middle table-bordered mb-0">
                            <thead class="bg-light">
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
                                            class="form-control form-control-sm border border-1 px-3"
                                            placeholder="Spesifikasi {{ strtolower($param) }}"
                                            value="{{ $sections_data[$sectionName][$param]['spesifikasi'] ?? '' }}">
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
                        <table class="table align-middle table-bordered mb-0">
                            <thead class="bg-light">
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
                                            class="form-control form-control-sm border border-1 px-3"
                                            placeholder="Spesifikasi {{ strtolower($param) }}"
                                            value="{{ $sections_data[$sectionName][$param]['spesifikasi'] ?? $defaultValues[$param] }}">
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
                        <table class="table align-middle table-bordered mb-0">
                            <thead class="bg-light">
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
                                            class="form-control form-control-sm border border-1 px-3"
                                            placeholder="Spesifikasi {{ strtolower($param) }}"
                                            value="{{ $sections_data[$sectionName][$param]['spesifikasi'] ?? $defaultValues[$param] }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-success m-0" id="btn-save-final">Simpan Perubahan</button>
                </div>
            </div>
        </div>

    </div>
</form>

<!-- Include Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {

    // Inisialisasi Select2
    function initSelect2() {
        $('.dynamic-select-bahan').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih persediaan --'
        });
    }

    $('#product_id').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Pilih Produk'
    });

    initSelect2();

    // Auto-fill hidden input select
    $(document).on('change', '.dynamic-select-bahan', function() {
        var selectedOption = $(this).find('option:selected');
        var selectedName = selectedOption.data('name') || selectedOption.text().trim();
        $(this).closest('td').find('.hidden-nama-bahan').val(selectedName);
    });

    // Validasi Bahan saat ganti tipe formula
    function validateInventoryType() {
        let selectedType = $('#type').val(); // "Bahan Baku" atau "Bahan Pelengkap"

        $('.dynamic-select-bahan').each(function() {
            let select = $(this);
            let currentVal = select.val();
            let isValid = false;

            // Loop melalui options dan hanya disable yang tidak cocok
            select.find('option').each(function() {
                let option = $(this);
                let optType = option.data('type');

                if (option.val() === "") return; // Skip placeholder

                if (selectedType === 'Bahan Pelengkap') {
                    if (optType === 'Bahan Baku') {
                        option.prop('disabled', true);
                        if (currentVal == option.val()) {
                            isValid = false;
                        }
                    } else {
                        option.prop('disabled', false);
                        if (currentVal == option.val()) {
                            isValid = true;
                        }
                    }
                } else {
                    if (optType !== selectedType) {
                        option.prop('disabled', true);
                        if (currentVal == option.val()) {
                            isValid = false;
                        }
                    } else {
                        option.prop('disabled', false);
                        if (currentVal == option.val()) {
                            isValid = true;
                        }
                    }
                }
            });

            // Reselect Select2 to update UI disabled items
            select.select2('destroy');
            initSelect2();

            // jika value sekarang di disable, kosongkan
            if (!isValid && currentVal) {
                select.val("").trigger('change');
            }
        });
    }

    // Handle Conditional Tabs based on Type
    function handleTabsVisibility() {
        let type = $('#type').val();
        if (type === 'Bahan Pelengkap') {
            $('.procedural-tab').hide();
            $('#btn-next-tahapan').hide();
            $('.btn-simpan-semua').removeClass('d-none');
            // Pastikan tab active kembali ke Formula
            $('#formula-tab').tab('show');
        } else {
            $('.procedural-tab').show();
            $('#btn-next-tahapan').show();
            $('.btn-simpan-semua').addClass('d-none');
        }
        validateInventoryType();
    }

    $('#type').on('change', handleTabsVisibility);
    handleTabsVisibility(); // Run on load to pre-filter Select2 inputs based on saved DB type

    // Buttons Next Tab (UI Only)
    $('#btn-next-tahapan').click(function() { $('#tahapan-tab').tab('show'); });
    $('#btn-next-qc').click(function() { $('#qc-tab').tab('show'); });
    $('#btn-next-ipc').click(function() { $('#ipc-tab').tab('show'); });

    // === ADD ROW LOGIC ===
    // Bahan Baku / Kemas
    $('#btn-add-bahan').on('click', function() {
        var rowHtml = `
            <tr>
                <td>
                    <select name="inventory_id[]" class="form-control dynamic-select-bahan" required>
                        <option value="" disabled selected>-- Pilih persediaan --</option>
                        @foreach($inventory as $inv)
                            <option value="{{ $inv->id }}" data-type="{{ $inv->type }}" data-name="{{ $inv->name }}">{{ $inv->name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="nama_bahan_baku[]" class="hidden-nama-bahan">
                </td>
                <td><input type="text" name="jumlah[]" class="form-control border px-2" required placeholder="0"></td>
                <td>
                    <select name="satuan[]" class="form-control border px-2" required>
                        <option value="gram">gram</option>
                        <option value="kg">kg</option>
                        <option value="ml">ml</option>
                        <option value="liter">liter</option>
                        <option value="pcs">pcs</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm m-0 remove-row-bahan">
                        <i class="material-icons opacity-10" style="font-size: 1rem;">delete</i>
                    </button>
                </td>
            </tr>`;
        $('#tabel-bahan tbody').append(rowHtml);
        initSelect2();
        validateInventoryType();
    });

    // Tahapan Proses
    $('#btn-add-tahapan').on('click', function() {
        var rowHtml = `
            <tr>
                <td><input type="text" name="nama_proses[]" class="form-control border px-2"></td>
                <td><input type="text" name="acuan[]" class="form-control border px-2"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm m-0 remove-row-tahapan"><i class="material-icons opacity-10" style="font-size: 1rem;">delete</i></button>
                </td>
            </tr>`;
        $('#tabel-tahapan tbody').append(rowHtml);
    });

    // === REMOVE ROW LOGIC ===
    $(document).on('click', '.remove-row-bahan', function() {
        if ($('#tabel-bahan tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            alert('Minimal harus ada 1 bahan.');
        }
    });
    $(document).on('click', '.remove-row-tahapan', function() { $(this).closest('tr').remove(); });

});
</script>
@endsection
