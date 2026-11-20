@extends('layouts.app')
@section('title', 'Komitkami | Uji Mikrobiologi')

@section('content')

<style>
    #pills-tab {
        margin-bottom: 0;
    }
    /* Sembunyikan moving-tab bawaan Material Dashboard yang menyebabkan glitch kotak melayang */
    .nav-wrapper .moving-tab {
        display: none !important;
    }
    #pills-tab .nav-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 16px !important;
        font-size: 0.85rem;
        font-weight: 600;
        color: #67748e;
        border-radius: 8px !important;
        transition: all 0.2s ease-in-out;
        white-space: nowrap;
    }
    #pills-tab .nav-link:hover {
        color: #344767;
    }
    #pills-tab .nav-link.active {
        background-color: #ffffff !important;
        color: #344767 !important;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12) !important;
    }
    #pills-tab .nav-link i.material-icons {
        font-size: 1.15rem;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        vertical-align: middle;
        margin-right: 6px;
        width: 20px;
        height: 20px;
    }
    #pills-tab .nav-link .badge-tab {
        margin-left: 10px;
        padding: 4px 8px;
        font-size: 0.72rem;
        font-weight: 700;
        border-radius: 6px;
        line-height: 1;
    }
</style>

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center px-4 mb-3">
    <h5 class="mb-0 font-weight-bold">Laporan Hasil Uji Antimikroba</h5>
</div>

@if(session('success'))
<div class="alert alert-success mx-4 alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Nav Tab Style ala Download Spesifikasi --}}
<div class="row mx-3 mb-4">
    <div class="col-lg-5 col-md-7 col-12">
        <div class="nav-wrapper position-relative end-0">
            <ul class="nav nav-pills nav-fill p-1" id="pills-tab" role="tablist">
                {{-- DATA PRODUKSI TAB --}}
                <li class="nav-item" role="presentation">
                    <a class="nav-link mb-0 active" id="pills-produksi-tab" data-bs-toggle="tab" href="#pills-produksi" role="tab"
                        aria-controls="pills-produksi" aria-selected="true">
                        <i class="material-icons">science</i>
                        <span>Data Produksi</span>
                        <span class="badge bg-gradient-primary badge-tab">{{ $produksiList->count() }}</span>
                    </a>
                </li>

                {{-- DATA MANUAL TAB --}}
                <li class="nav-item" role="presentation">
                    <a class="nav-link mb-0" id="pills-manual-tab" data-bs-toggle="tab" href="#pills-manual" role="tab"
                        aria-controls="pills-manual" aria-selected="false">
                        <i class="material-icons">edit_note</i>
                        <span>Data Manual</span>
                        <span class="badge bg-gradient-secondary badge-tab">{{ $manualList->count() }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="tab-content" id="pills-tabContent">

    {{-- ====================================================== --}}
    {{-- TAB 1: DATA PRODUKSI                                    --}}
    {{-- ====================================================== --}}
    <div class="tab-pane fade show active" id="pills-produksi" role="tabpanel" aria-labelledby="pills-produksi-tab">
        <div class="card mx-4">
            <div class="card-body px-0 pb-2">
                <div class="table-responsive px-3">
                    <table class="table table-hover table-borderless align-middle mb-0" id="tableProduksi">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No.</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No. Batch</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produk</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tgl. Produksi</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Laporan</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($produksiList as $i => $produksi)
                            @php
                                $mikroChecks = $produksi->checkQualityControl->filter(function ($check) {
                                    $param = $check->qualityControl->parameter_pengujian ?? '';
                                    $standar = trim($check->qualityControl->nilai_standart ?? '');
                                    return (stripos($param, 'E. Coli') !== false
                                        || stripos($param, 'S. Aureus') !== false
                                        || stripos($param, 'albicans') !== false)
                                        && !in_array($standar, ['', '-', ' ']);
                                });

                                $sudahAdaLaporan = $produksi->hasilMikrobiologi->isNotEmpty();
                            @endphp
                            <tr class="align-middle">
                                <td class="text-sm ps-2 text-center" style="width: 40px;">{{ $i + 1 }}</td>
                                <td>
                                    <h6 class="mb-0 text-sm font-weight-bold">{{ $produksi->batch_number ?? '-' }}</h6>
                                </td>
                                <td>
                                    <p class="text-sm mb-0">{{ $produksi->product->name ?? '-' }}</p>
                                </td>
                                <td>
                                    <p class="text-sm mb-0">
                                        {{ $produksi->tanggal_produksi ? \Carbon\Carbon::parse($produksi->tanggal_produksi)->format('d M Y') : '-' }}
                                    </p>
                                </td>
                                <td>
                                    @if($sudahAdaLaporan)
                                        <span class="badge bg-gradient-success">
                                            <i class="material-icons text-xs me-1">check_circle</i>Sudah Ada Laporan
                                        </span>
                                    @else
                                        <span class="badge bg-gradient-warning">
                                            <i class="material-icons text-xs me-1">pending</i>Belum Ada Laporan
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        @if($sudahAdaLaporan)
                                            <a href="{{ url('/print-laporan-mikrobiologi', $produksi->batch_number) }}" target="_blank" class="btn btn-sm btn-success mb-0 d-inline-flex align-items-center px-2 py-1" title="Download Laporan PDF">
                                                <i class="material-icons text-sm me-1">download</i> PDF
                                            </a>
                                        @endif
                                        <a href="{{ url('proses-uji-mikroba', $produksi->batch_number) }}" class="btn btn-sm btn-outline-primary mb-0 d-inline-flex align-items-center px-2 py-1">
                                            <i class="material-icons text-sm me-1">visibility</i> Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="material-icons" style="font-size:36px;">science</i>
                                    <p class="mt-2 mb-0">Tidak ada data produksi dengan parameter uji antimikroba.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- TAB 2: DATA MANUAL                                      --}}
    {{-- ====================================================== --}}
    <div class="tab-pane fade" id="pills-manual" role="tabpanel" aria-labelledby="pills-manual-tab">
        <div class="card mx-4">
            <div class="card-body px-0 pb-2">
                <div class="d-flex justify-content-end px-3 mb-3">
                    <button class="btn bg-gradient-dark btn-sm mb-0"
                            data-bs-toggle="modal" data-bs-target="#modalTambahManual">
                        <i class="material-icons text-sm">add</i>&nbsp;Tambah Laporan Manual
                    </button>
                </div>

                <div class="table-responsive px-3">
                    <table class="table table-hover table-borderless align-middle mb-0" id="tableManual">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No.</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sampel Uji</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mikroba Uji</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No. Batch</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Waktu Kontak</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Awal (CFU/mL)</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Clean (%)</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dirty (%)</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Petugas</th>
                                <th class="text-secondary opacity-7"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($manualList as $i => $laporan)
                            <tr>
                                <td class="text-sm ps-2">{{ $i + 1 }}</td>
                                <td class="text-sm">
                                    {{ $laporan->tanggal ? $laporan->tanggal->format('d M Y') : '-' }}
                                </td>
                                <td class="text-sm">{{ $laporan->sampel_uji }}</td>
                                <td>
                                    <span class="badge badge-sm bg-gradient-info">{{ $laporan->mikroba_uji }}</span>
                                </td>
                                <td class="text-sm">{{ $laporan->no_batch ?? '-' }}</td>
                                <td class="text-sm">{{ $laporan->waktu_kontak }}</td>
                                <td class="text-sm">{{ $laporan->awal_cfu_ml !== null ? number_format($laporan->awal_cfu_ml) : '-' }}</td>
                                <td class="text-sm">
                                    {{ $laporan->persen_clean !== null ? number_format($laporan->persen_clean, 2).'%' : '-' }}
                                </td>
                                <td class="text-sm">
                                    {{ $laporan->persen_dirty !== null ? number_format($laporan->persen_dirty, 2).'%' : '-' }}
                                </td>
                                <td class="text-sm">{{ $laporan->petugas ?? '-' }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ url('/print-laporan-mikrobiologi-manual', $laporan->id) }}" target="_blank" class="btn btn-sm btn-success mb-0 d-inline-flex align-items-center px-2 py-1" title="Download Laporan PDF">
                                        <i class="material-icons text-sm me-1">download</i> PDF
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-5">
                                    <i class="material-icons" style="font-size:36px;">edit_note</i>
                                    <p class="mt-2 mb-0">Belum ada laporan manual.</p>
                                    <small>Klik <strong>"Tambah Laporan Manual"</strong> untuk menambahkan data.</small>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>{{-- end tab-content --}}

{{-- ================================================================== --}}
{{-- MODAL: Tambah Laporan Hasil Uji Antimikroba (Manual)               --}}
{{-- ================================================================== --}}
<div class="modal fade" id="modalTambahManual" tabindex="-1"
     aria-labelledby="modalManualLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalManualLabel">
                    <i class="material-icons text-sm me-1">biotech</i>
                    Tambah Laporan Hasil Uji Antimikroba &mdash; Manual
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('mikrobiologi.store') }}" method="POST" id="formManualLaporan">
                @csrf
                <div class="modal-body">

                    <p class="text-xs text-muted mb-4">
                        <i class="material-icons text-xs">description</i>
                        No. Dokumen: <strong>KIG-QC-F-09-02-01</strong>
                    </p>

                    {{-- ---- Identitas ---- --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label text-sm font-weight-bold">
                                Tanggal <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal" class="form-control form-control-sm"
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-sm font-weight-bold">
                                Sampel Uji <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="sampel_uji" class="form-control form-control-sm"
                                   placeholder="Nama sampel yang diuji" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-sm font-weight-bold">
                                Mikroba Uji <span class="text-danger">*</span>
                            </label>
                            <select name="mikroba_uji" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Mikroba --</option>
                                <option value="Uji Antibakteri : E. Coli">Uji Antibakteri : E. Coli</option>
                                <option value="Uji Antibakteri : S. Aureus">Uji Antibakteri : S. Aureus</option>
                                <option value="Uji Antifungi : C. albicans">Uji Antifungi : C. albicans</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-sm font-weight-bold">No. Batch</label>
                            <input type="text" name="no_batch" class="form-control form-control-sm"
                                   placeholder="Opsional">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label text-sm font-weight-bold">
                                Waktu Kontak <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="waktu_kontak" class="form-control form-control-sm"
                                   value="30 Detik" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-sm font-weight-bold">Petugas</label>
                            <input type="text" name="petugas" class="form-control form-control-sm"
                                   placeholder="Nama petugas">
                        </div>
                    </div>

                    <hr class="horizontal dark">

                    {{-- ---- Tabel Data Pengujian (sesuai form fisik) ---- --}}
                    <p class="text-sm font-weight-bold mb-3">
                        <i class="material-icons text-sm me-1">table_chart</i>
                        Data Hasil Pengujian
                    </p>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm text-center align-middle"
                               style="font-size: 12px; min-width: 700px;">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="align-middle" style="width:130px;">
                                        Jumlah Awal Mikroba<br>
                                        <small class="text-muted">pada suspensi</small>
                                    </th>
                                    <th colspan="2" class="align-middle bg-info bg-opacity-10">
                                        Jumlah Mikroba Hidup<br>
                                        <small>Clean Condition</small>
                                    </th>
                                    <th colspan="2" class="align-middle bg-warning bg-opacity-10">
                                        Jumlah Mikroba Hidup<br>
                                        <small>Dirty Condition</small>
                                    </th>
                                    <th colspan="2" class="align-middle bg-success bg-opacity-10">
                                        % Mikroba Terbunuh
                                    </th>
                                </tr>
                                <tr>
                                    <th class="bg-info bg-opacity-10">CFU/mL</th>
                                    <th class="bg-info bg-opacity-10">Log</th>
                                    <th class="bg-warning bg-opacity-10">CFU/mL</th>
                                    <th class="bg-warning bg-opacity-10">Log</th>
                                    <th class="bg-success bg-opacity-10">Clean (%)</th>
                                    <th class="bg-success bg-opacity-10">Dirty (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    {{-- Jumlah Awal --}}
                                    <td>
                                        <div class="mb-1">
                                            <small class="text-muted">CFU/mL</small>
                                            <input type="number" step="any" name="awal_cfu_ml"
                                                   class="form-control form-control-sm text-center"
                                                   placeholder="0">
                                        </div>
                                        <div>
                                            <small class="text-muted">Log</small>
                                            <input type="number" step="any" name="awal_log"
                                                   class="form-control form-control-sm text-center"
                                                   placeholder="0.00">
                                        </div>
                                    </td>
                                    {{-- Clean --}}
                                    <td>
                                        <input type="number" step="any" name="clean_cfu_ml"
                                               class="form-control form-control-sm text-center"
                                               placeholder="0">
                                    </td>
                                    <td>
                                        <input type="number" step="any" name="clean_log"
                                               class="form-control form-control-sm text-center"
                                               placeholder="0.00">
                                    </td>
                                    {{-- Dirty --}}
                                    <td>
                                        <input type="number" step="any" name="dirty_cfu_ml"
                                               class="form-control form-control-sm text-center"
                                               placeholder="0">
                                    </td>
                                    <td>
                                        <input type="number" step="any" name="dirty_log"
                                               class="form-control form-control-sm text-center"
                                               placeholder="0.00">
                                    </td>
                                    {{-- Persen --}}
                                    <td>
                                        <input type="number" step="any" name="persen_clean"
                                               class="form-control form-control-sm text-center"
                                               placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" step="any" name="persen_dirty"
                                               class="form-control form-control-sm text-center"
                                               placeholder="0.00">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr class="horizontal dark">

                    {{-- ---- Keterangan ---- --}}
                    <div class="mb-2">
                        <label class="form-label text-sm font-weight-bold">Keterangan / Catatan</label>
                        <textarea name="keterangan" class="form-control form-control-sm" rows="2"
                                  placeholder="Catatan tambahan (opsional)..."></textarea>
                    </div>

                </div>{{-- end modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="material-icons text-sm">close</i>&nbsp;Batal
                    </button>
                    <button type="submit" class="btn bg-gradient-primary btn-sm">
                        <i class="material-icons text-sm">save</i>&nbsp;Simpan Laporan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {

    // DataTable — Tab Produksi
    $('#tableProduksi').DataTable({
        info        : true,
        ordering    : true,
        lengthChange: true,
        searching   : true,
        pageLength  : 10,
        language: {
            search      : 'Cari:',
            lengthMenu  : 'Tampilkan _MENU_ data',
            info        : 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty   : 'Tidak ada data',
            zeroRecords : 'Data tidak ditemukan',
            paginate    : { previous: '‹', next: '›' }
        }
    });

    // DataTable — Tab Manual
    $('#tableManual').DataTable({
        info        : true,
        ordering    : true,
        lengthChange: true,
        searching   : true,
        pageLength  : 10,
        language: {
            search      : 'Cari:',
            lengthMenu  : 'Tampilkan _MENU_ data',
            info        : 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty   : 'Tidak ada data',
            zeroRecords : 'Data tidak ditemukan',
            paginate    : { previous: '‹', next: '›' }
        }
    });

    // Re-init DataTable when switching tabs (fixes column width glitch)
    $('[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

});
</script>

@endsection
