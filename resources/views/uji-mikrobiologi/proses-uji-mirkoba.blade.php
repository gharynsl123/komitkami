@extends('layouts.app')
@section('title', 'Komitkami | Proses Uji Mikroba')

@section('content')

{{-- Custom Styling untuk Input Modern, Rounded Radius, Spans, dan Nav Tab Active --}}
<style>
/* --- Modern Rounded Inputs & Input Groups --- */
.input-group-modern {
    border-radius: 10px !important;
    transition: all 0.2s ease-in-out;
}
.input-group-modern:focus-within {
    box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.2) !important;
}
.input-group-modern .form-control {
    border: 1px solid #cbd5e1 !important;
    font-size: 0.85rem !important;
    padding: 0.5rem 0.85rem !important;
    color: #1e293b !important;
    border-radius: 0 !important;
}
.input-group-modern .form-control:first-child {
    border-top-left-radius: 10px !important;
    border-bottom-left-radius: 10px !important;
    border-right: none !important;
}
.input-group-modern .form-control:last-child {
    border-top-right-radius: 10px !important;
    border-bottom-right-radius: 10px !important;
    border-left: none !important;
}
.input-group-modern .input-group-text {
    background-color: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    color: #475569 !important;
    font-size: 0.8125rem !important;
    font-weight: 600 !important;
    padding: 0.5rem 0.95rem !important;
    white-space: nowrap !important;
    border-radius: 0 !important;
}
.input-group-modern .input-group-text:first-child {
    border-top-left-radius: 10px !important;
    border-bottom-left-radius: 10px !important;
    border-right: none !important;
}
.input-group-modern .input-group-text:last-child {
    border-top-right-radius: 10px !important;
    border-bottom-right-radius: 10px !important;
    border-left: none !important;
}

/* Standalone inputs & Readonly output boxes */
.form-control-modern {
    border: 1px solid #cbd5e1 !important;
    border-radius: 10px !important;
    font-size: 0.85rem !important;
    padding: 0.5rem 0.85rem !important;
    transition: all 0.2s ease-in-out;
    color: #1e293b !important;
}
.form-control-modern:focus {
    border-color: #1A73E8 !important;
    box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.2) !important;
}
.form-control-readonly {
    background-color: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    font-size: 0.875rem !important;
    padding: 0.5rem 0.85rem !important;
}

/* --- Nav Tabs Modern & Active Styling --- */
.nav-pills-modern {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 14px !important;
    padding: 5px !important;
    display: flex;
    gap: 6px;
}
/* Nonaktifkan moving-tab bawaan Material Dashboard yang sering offset/glitch */
.nav-pills-modern .moving-tab {
    display: none !important;
}
.nav-pills-modern .nav-item {
    flex: 1 1 0;
}
.nav-pills-modern .nav-link {
    border-radius: 10px !important;
    padding: 10px 18px !important;
    font-size: 0.825rem !important;
    font-weight: 600 !important;
    color: #64748b !important;
    border: 1px solid transparent !important;
    background: transparent !important;
    transition: all 0.22s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    cursor: pointer;
}
.nav-pills-modern .nav-link i {
    font-size: 16px;
    margin-right: 6px;
    color: #94a3b8;
    transition: color 0.2s ease-in-out;
}
.nav-pills-modern .nav-link:hover {
    color: #1e293b !important;
    background: rgba(255, 255, 255, 0.85) !important;
}
.nav-pills-modern .nav-link:hover i {
    color: #1e293b !important;
}
/* Active Tab Style */
.nav-pills-modern .nav-link.active {
    background: linear-gradient(195deg, #49a3f1 0%, #1A73E8 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(26, 115, 232, 0.35) !important;
    border-color: transparent !important;
}
.nav-pills-modern .nav-link.active i {
    color: #ffffff !important;
}
</style>

{{-- Header & Breadcrumb --}}
<div class="d-flex justify-content-between align-items-center px-4 mb-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-1 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ url('/uji-mikrobiologi') }}">Uji Mikrobiologi</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Proses Pengujian</li>
            </ol>
        </nav>
        <h5 class="mb-0 font-weight-bold">Proses Uji Antimikroba Produksi</h5>
    </div>
    <div class="text-end">
        <a href="{{ url('/uji-mikrobiologi') }}" class="btn btn-outline-secondary btn-sm mb-1" style="border-radius: 8px;">
            <i class="material-icons text-sm me-1">arrow_back</i> Kembali
        </a>
        <div class="text-xs text-muted">No. Dokumen: <strong>KIG-QC-F-09-02-01</strong></div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success mx-4 alert-dismissible fade show text-white" role="alert" style="border-radius: 12px;">
    <i class="material-icons text-sm me-1">check_circle</i>
    {{ session('success') }}
    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger mx-4 alert-dismissible fade show text-white" role="alert" style="border-radius: 12px;">
    <p class="mb-1 font-weight-bold">Terdapat kesalahan:</p>
    <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

{{-- 1. INFORMATION CARD PRODUKSI --}}
<div class="card mx-4 mb-4 shadow-sm" style="border-radius: 14px;">
    <div class="card-header pb-0 bg-transparent">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 font-weight-bold text-dark">
                <i class="material-icons text-primary text-sm me-1">inventory_2</i>
                Informasi Produksi Terpilih
            </h6>
            @if($existingHasil->isNotEmpty())
                <span class="badge bg-gradient-success" style="border-radius: 8px;">
                    <i class="material-icons text-xs me-1">check_circle</i> Sudah Ada {{ $existingHasil->count() }} Parameter Tersimpan
                </span>
            @else
                <span class="badge bg-gradient-warning" style="border-radius: 8px;">
                    <i class="material-icons text-xs me-1">pending</i> Belum Ada Laporan
                </span>
            @endif
        </div>
    </div>
    <div class="card-body pt-3">
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
                <small class="text-xs text-uppercase font-weight-bolder text-secondary d-block">Nomor Batch</small>
                <span class="font-weight-bold text-dark h6 mb-0">{{ $produksi->batch_number ?? '-' }}</span>
            </div>
            <div class="col-md-4 col-sm-6">
                <small class="text-xs text-uppercase font-weight-bolder text-secondary d-block">Nama Produk</small>
                <span class="font-weight-bold text-dark h6 mb-0">{{ $produksi->product->name ?? '-' }}</span>
            </div>
            <div class="col-md-2 col-sm-6">
                <small class="text-xs text-uppercase font-weight-bolder text-secondary d-block">Tanggal Produksi</small>
                <span class="text-dark text-sm font-weight-bold">
                    {{ $produksi->tanggal_produksi ? \Carbon\Carbon::parse($produksi->tanggal_produksi)->format('d M Y') : '-' }}
                </span>
            </div>
            <div class="col-md-3 col-sm-6">
                <small class="text-xs text-uppercase font-weight-bolder text-secondary d-block">Parameter Mikro QC Terhubung</small>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    @forelse($mikroChecks as $check)
                        <span class="badge badge-sm bg-gradient-info" style="border-radius: 6px;">
                            {{ $check->qualityControl->parameter_pengujian ?? '-' }}
                            @if(!empty($check->nilai_real) && $check->nilai_real !== '-')
                                ({{ $check->nilai_real }})
                            @endif
                        </span>
                    @empty
                        <span class="text-muted text-xs">Belum ada pemeriksaan QC terdaftar</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. LIVE RUMUS BERJALAN (2 CARDS col-md-6) --}}
<div class="px-4 mb-4">
    <div class="row g-3">
        {{-- Card 1: Live Rumus Jumlah Mikroba CFU --}}
        <div class="col-md-6">
            <div class="card h-100 border border-info shadow-sm" style="border-radius: 14px; background: linear-gradient(135deg, #f8fbfd 0%, #eef6fc 100%);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 text-info font-weight-bold text-sm">
                            <i class="material-icons text-sm me-1">functions</i>
                            1. Rumus Live: Jumlah Mikroba (CFU/mL)
                        </h6>
                        <span class="badge bg-info text-white text-xxs font-weight-bold" id="labelActiveTargetCfu" style="border-radius: 6px;">Candida albicans (Suspensi)</span>
                    </div>

                    {{-- Rumus statis standar --}}
                    <div class="bg-white rounded p-2 border mb-2 text-center text-xs" style="border-radius: 8px !important;">
                        <span class="text-muted">Rumus:</span>
                        <strong class="text-dark ms-1">Jumlah Mikroba = Koloni &times; ( 1 / Pengenceran ) = Koloni &times; 10<sup>pangkat</sup></strong>
                    </div>

                    {{-- Live Display calculation --}}
                    <div class="p-3 bg-white border border-info text-center shadow-sm" style="border-radius: 12px;">
                        <div class="text-xs text-muted mb-1">Perhitungan Berjalan (Live):</div>
                        <div class="h5 mb-1 font-weight-bolder text-dark">
                            <span class="badge bg-light text-primary border px-2 py-1" id="live_koloni" style="border-radius: 6px;">0</span>
                            &times; 10<sup><span class="badge bg-light text-dark border px-2 py-1" id="live_pangkat" style="border-radius: 6px;">0</span></sup>
                            =
                            <span class="text-info" id="live_cfu">0</span>
                            <span class="text-xs font-weight-normal text-muted">CFU / mL</span>
                        </div>
                        <small class="text-muted text-xxs d-block">
                            *Angka dasar 10 tetap. Nilai terhitung otomatis saat input koloni &amp; pangkat pengenceran.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Live Rumus Log Mikroba --}}
        <div class="col-md-6">
            <div class="card h-100 border border-primary shadow-sm" style="border-radius: 14px; background: linear-gradient(135deg, #faf9fe 0%, #f1effd 100%);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 text-primary font-weight-bold text-sm">
                            <i class="material-icons text-sm me-1">calculate</i>
                            2. Rumus Live: Jumlah Log Mikroba
                        </h6>
                        <span class="badge bg-primary text-white text-xxs font-weight-bold" id="labelActiveTargetLog" style="border-radius: 6px;">Candida albicans (Suspensi)</span>
                    </div>

                    {{-- Rumus statis standar --}}
                    <div class="bg-white rounded p-2 border mb-2 text-center text-xs" style="border-radius: 8px !important;">
                        <span class="text-muted">Rumus:</span>
                        <strong class="text-dark ms-1">Jumlah Log Mikroba = Log<sub>10</sub>( Jumlah Mikroba )</strong>
                    </div>

                    {{-- Live Display calculation --}}
                    <div class="p-3 bg-white border border-primary text-center shadow-sm" style="border-radius: 12px;">
                        <div class="text-xs text-muted mb-1">Perhitungan Berjalan (Live):</div>
                        <div class="h5 mb-1 font-weight-bolder text-dark">
                            Log ( <span class="text-info" id="live_log_cfu">0</span> )
                            =
                            <span class="text-primary" id="live_log_result">0.00</span>
                            <span class="text-xs font-weight-normal text-muted">Log</span>
                        </div>
                        <small class="text-muted text-xxs d-block">
                            *Logaritma basis 10 dihitung langsung dari hasil CFU/mL.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. FORM INPUT HASIL UJI MIKROBIOLOGI --}}
<div class="card mx-4 mb-4 shadow-sm" style="border-radius: 14px;">
    <div class="card-header pb-0 bg-transparent">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 font-weight-bold text-dark">
                    <i class="material-icons text-success text-sm me-1">biotech</i>
                    Form Input Pengujian &amp; Sinkronisasi Quality Control
                </h6>
                <p class="text-xs text-muted mt-1 mb-0">
                    Isi jumlah koloni dan pangkat pengenceran untuk masing-masing mikroba. Saat disimpan, data akan <strong>otomatis mengisi/memperbarui</strong> pemeriksaan Quality Control produksi ini.
                </p>
            </div>
            <span class="badge bg-gradient-dark" style="border-radius: 6px;">Auto-Sync QC</span>
        </div>
    </div>

    <form action="{{ route('mikrobiologi.store') }}" method="POST" id="formProsesMikroba">
        @csrf
        <input type="hidden" name="produksi_id" value="{{ $produksi->id }}">
        <input type="hidden" name="no_batch" value="{{ $produksi->batch_number }}">

        <div class="card-body pt-3">

            {{-- Row Identitas Pengujian --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label text-xs font-weight-bold text-dark mb-1">
                        Tanggal Pengujian <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="tanggal" class="form-control form-control-modern"
                           value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="form-label text-xs font-weight-bold text-dark mb-1">
                        Sampel Uji <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="sampel_uji" class="form-control form-control-modern"
                           value="{{ $produksi->product->name ?? 'Sampel Uji' }}" required>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label text-xs font-weight-bold text-dark mb-1">
                        Waktu Kontak <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="waktu_kontak" class="form-control form-control-modern"
                           value="30 Detik" required>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label text-xs font-weight-bold text-dark mb-1">
                        Petugas Penguji (QC)
                    </label>
                    <input type="text" name="petugas" class="form-control form-control-modern"
                           placeholder="Nama Petugas QC" value="{{ auth()->user()->name ?? '' }}">
                </div>
            </div>

            <hr class="horizontal dark my-3">

            {{-- ========================================================================= --}}
            {{-- BAGIAN 1: PERHITUNGAN SUSPENSI MIKROBA (3 BARIS LANGSUNG SESUAI REQUEST)  --}}
            {{-- ========================================================================= --}}
            <div class="p-3 mb-4" style="background-color: #f8fafc; border-left: 4px solid #17a2b8; border-radius: 14px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-sm font-weight-bold text-dark mb-0">
                        <span class="badge bg-gradient-info me-1" style="border-radius: 6px;">Bagian 1</span>
                        Perhitungan Suspensi Mikroba (Jumlah Awal Mikroba) &mdash; 3 Parameter Pengujian
                    </h6>
                    <small class="text-muted text-xs">Sesuai KIG-QC-F-09-02-01 Bagian 2</small>
                </div>
                <p class="text-xs text-muted mb-3">
                    Langsung isi <strong>Jumlah Koloni</strong> dan <strong>Pengenceran</strong> untuk masing-masing mikroba. Jika suatu mikroba tidak diuji pada batch ini, biarkan kosong (akan otomatis tercatat sebagai "<strong>-</strong>" pada Quality Control).
                </p>

                <div class="card border shadow-none" style="border-radius: 12px; overflow: hidden; border-color: #e2e8f0 !important;">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center bg-white mb-0" id="tableBagian1" style="font-size: 13px;">
                            <thead style="background-color: #f8fafc;">
                                <tr>
                                    <th style="width: 28%;" class="text-start ps-3 py-3 font-weight-bolder text-dark">Mikroba Uji</th>
                                    <th style="width: 18%;" class="py-3 font-weight-bolder text-dark">Jumlah Koloni Suspensi</th>
                                    <th style="width: 18%;" class="py-3 font-weight-bolder text-dark">Pengenceran (10<sup>x</sup>)</th>
                                    <th style="width: 18%;" class="py-3 font-weight-bolder text-info">Jumlah Mikroba (CFU/mL)</th>
                                    <th style="width: 18%;" class="py-3 font-weight-bolder text-primary">Log Mikroba (Log)</th>
                                </tr>
                            </thead>
                            <tbody>

                                {{-- ROW 1: Candida albicans --}}
                                @php
                                    $cExisting = $existingByOrg['candida'] ?? null;
                                @endphp
                                <tr class="align-middle row-mikro" data-key="candida" data-nama="Candida albicans">
                                    <td class="text-start ps-3 py-3">
                                        <h6 class="mb-0 text-sm font-weight-bold text-dark">Uji Antifungi Candida albicans</h6>
                                        <input type="hidden" name="mikroba[candida][nama]" value="Uji Antifungi : C. albicans">
                                        <small class="text-muted text-xxs">Parameter QC: Uji Antifungi : C. albicans</small>
                                    </td>
                                    <td class="p-2">
                                        <div class="input-group input-group-modern">
                                            <input type="number" step="any" name="mikroba[candida][suspensi_koloni]"
                                                   class="form-control text-center input-suspensi"
                                                   data-key="candida" data-nama="Candida albicans"
                                                   placeholder="Contoh: 97">
                                            <span class="input-group-text">Koloni</span>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <div class="input-group input-group-modern">
                                            <input type="number" step="any" name="mikroba[candida][suspensi_pangkat]"
                                                   class="form-control text-center input-suspensi"
                                                   data-key="candida" data-nama="Candida albicans"
                                                   placeholder="Contoh: 4">
                                            <span class="input-group-text font-weight-bold">10<sup>x</sup></span>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="any" name="mikroba[candida][awal_cfu_ml]" id="cfu_candida"
                                               class="form-control form-control-readonly text-center text-info"
                                               value="{{ $cExisting->awal_cfu_ml ?? '' }}"
                                               placeholder="0" readonly>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="any" name="mikroba[candida][awal_log]" id="log_candida"
                                               class="form-control form-control-readonly text-center text-primary"
                                               value="{{ $cExisting->awal_log ?? '' }}"
                                               placeholder="0.00" readonly>
                                    </td>
                                </tr>

                                {{-- ROW 2: Staphylococcus aureus --}}
                                @php
                                    $sExisting = $existingByOrg['staph'] ?? null;
                                @endphp
                                <tr class="align-middle row-mikro" data-key="staph" data-nama="Staphylococcus aureus">
                                    <td class="text-start ps-3 py-3">
                                        <h6 class="mb-0 text-sm font-weight-bold text-dark">Uji Antibakteri Staphylococcus aureus</h6>
                                        <input type="hidden" name="mikroba[staph][nama]" value="Uji Antibakteri : S. Aureus">
                                        <small class="text-muted text-xxs">Parameter QC: Uji Antibakteri : S. Aureus</small>
                                    </td>
                                    <td class="p-2">
                                        <div class="input-group input-group-modern">
                                            <input type="number" step="any" name="mikroba[staph][suspensi_koloni]"
                                                   class="form-control text-center input-suspensi"
                                                   data-key="staph" data-nama="Staphylococcus aureus"
                                                   placeholder="Contoh: 125">
                                            <span class="input-group-text">Koloni</span>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <div class="input-group input-group-modern">
                                            <input type="number" step="any" name="mikroba[staph][suspensi_pangkat]"
                                                   class="form-control text-center input-suspensi"
                                                   data-key="staph" data-nama="Staphylococcus aureus"
                                                   placeholder="Contoh: 4">
                                            <span class="input-group-text font-weight-bold">10<sup>x</sup></span>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="any" name="mikroba[staph][awal_cfu_ml]" id="cfu_staph"
                                               class="form-control form-control-readonly text-center text-info"
                                               value="{{ $sExisting->awal_cfu_ml ?? '' }}"
                                               placeholder="0" readonly>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="any" name="mikroba[staph][awal_log]" id="log_staph"
                                               class="form-control form-control-readonly text-center text-primary"
                                               value="{{ $sExisting->awal_log ?? '' }}"
                                               placeholder="0.00" readonly>
                                    </td>
                                </tr>

                                {{-- ROW 3: Escherichia coli --}}
                                @php
                                    $eExisting = $existingByOrg['ecoli'] ?? null;
                                @endphp
                                <tr class="align-middle row-mikro" data-key="ecoli" data-nama="Escherichia coli">
                                    <td class="text-start ps-3 py-3">
                                        <h6 class="mb-0 text-sm font-weight-bold text-dark">Uji Antibakteri Escherichia coli</h6>
                                        <input type="hidden" name="mikroba[ecoli][nama]" value="Uji Antibakteri : E. Coli">
                                        <small class="text-muted text-xxs">Parameter QC: Uji Antibakteri : E. Coli</small>
                                    </td>
                                    <td class="p-2">
                                        <div class="input-group input-group-modern">
                                            <input type="number" step="any" name="mikroba[ecoli][suspensi_koloni]"
                                                   class="form-control text-center input-suspensi"
                                                   data-key="ecoli" data-nama="Escherichia coli"
                                                   placeholder="Contoh: 112">
                                            <span class="input-group-text">Koloni</span>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <div class="input-group input-group-modern">
                                            <input type="number" step="any" name="mikroba[ecoli][suspensi_pangkat]"
                                                   class="form-control text-center input-suspensi"
                                                   data-key="ecoli" data-nama="Escherichia coli"
                                                   placeholder="Contoh: 4">
                                            <span class="input-group-text font-weight-bold">10<sup>x</sup></span>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="any" name="mikroba[ecoli][awal_cfu_ml]" id="cfu_ecoli"
                                               class="form-control form-control-readonly text-center text-info"
                                               value="{{ $eExisting->awal_cfu_ml ?? '' }}"
                                               placeholder="0" readonly>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" step="any" name="mikroba[ecoli][awal_log]" id="log_ecoli"
                                               class="form-control form-control-readonly text-center text-primary"
                                               value="{{ $eExisting->awal_log ?? '' }}"
                                               placeholder="0.00" readonly>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- BAGIAN 2: PERHITUNGAN JUMLAH MIKROBA HIDUP (CLEAN & DIRTY CONDITION)      --}}
            {{-- ========================================================================= --}}
            <div class="p-3 mb-4" style="background-color: #fcfbf8; border-left: 4px solid #fb8c00; border-radius: 14px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-sm font-weight-bold text-dark mb-0">
                        <span class="badge bg-gradient-warning me-1" style="border-radius: 6px;">Bagian 2</span>
                        Perhitungan Jumlah Mikroba yang Hidup &amp; % Terbunuh (Clean &amp; Dirty Condition)
                    </h6>
                    <small class="text-muted text-xs">Sesuai KIG-QC-F-09-02-01 Bagian 3</small>
                </div>
                <p class="text-xs text-muted mb-3">
                    Pilih tab mikroba di bawah untuk menginput hasil pengujian pada <strong>Clean Condition</strong> dan <strong>Dirty Condition</strong> (opsional).
                </p>

                {{-- NAV TABS 3 MIKROBA (MODERN PILLS DENGAN ACTIVE STYLE) --}}
                <div class="mb-3">
                    <ul class="nav nav-pills-modern" id="pillsMikroba" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pill-candida-tab" data-bs-toggle="pill"
                                    data-bs-target="#pill-candida" type="button" role="tab" aria-selected="true">
                                <i class="material-icons">science</i> Candida albicans
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pill-staph-tab" data-bs-toggle="pill"
                                    data-bs-target="#pill-staph" type="button" role="tab" aria-selected="false">
                                <i class="material-icons">science</i> Staphylococcus aureus
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pill-ecoli-tab" data-bs-toggle="pill"
                                    data-bs-target="#pill-ecoli" type="button" role="tab" aria-selected="false">
                                <i class="material-icons">science</i> Escherichia coli
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="pillsMikrobaContent">

                    {{-- TAB PANE: CANDIDA ALBICANS --}}
                    <div class="tab-pane fade show active" id="pill-candida" role="tabpanel">
                        @include('uji-mikrobiologi.partials.kondisi-pengujian', [
                            'key'      => 'candida',
                            'nama'     => 'Candida albicans',
                            'existing' => $cExisting
                        ])
                    </div>

                    {{-- TAB PANE: STAPHYLOCOCCUS AUREUS --}}
                    <div class="tab-pane fade" id="pill-staph" role="tabpanel">
                        @include('uji-mikrobiologi.partials.kondisi-pengujian', [
                            'key'      => 'staph',
                            'nama'     => 'Staphylococcus aureus',
                            'existing' => $sExisting
                        ])
                    </div>

                    {{-- TAB PANE: ESCHERICHIA COLI --}}
                    <div class="tab-pane fade" id="pill-ecoli" role="tabpanel">
                        @include('uji-mikrobiologi.partials.kondisi-pengujian', [
                            'key'      => 'ecoli',
                            'nama'     => 'Escherichia coli',
                            'existing' => $eExisting
                        ])
                    </div>

                </div>
            </div>

            {{-- Row Catatan & Petugas --}}
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label text-xs font-weight-bold text-dark mb-1">Keterangan / Catatan Pengujian</label>
                    <textarea name="keterangan" class="form-control form-control-modern" rows="2"
                              placeholder="Catatan tambahan mengenai kondisi pengujian, media, atau hasil pengamatan (opsional)..."></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-xs text-muted">
                    <i class="material-icons text-xs me-1 text-info align-middle">info</i>
                    Mikroba yang tidak diisi akan otomatis tercatat sebagai "<strong>-</strong>" pada Quality Control produksi.
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('/uji-mikrobiologi') }}" class="btn btn-secondary btn-sm mb-0" style="border-radius: 8px;">Batal</a>
                    <button type="submit" class="btn bg-gradient-primary btn-sm mb-0" style="border-radius: 8px;">
                        <i class="material-icons text-sm me-1">save</i> Simpan &amp; Sinkronkan ke Quality Control
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

{{-- 4. RIWAYAT HASIL UJI MIKROBA UNTUK BATCH INI (JIKA SUDAH ADA) --}}
@if($existingHasil->isNotEmpty())
<div class="card mx-4 mb-4 shadow-sm" style="border-radius: 14px;">
    <div class="card-header pb-0 bg-transparent">
        <h6 class="mb-0 font-weight-bold text-dark">
            <i class="material-icons text-info text-sm me-1">history</i>
            Riwayat Laporan Uji Mikroba Tersimpan untuk Batch {{ $produksi->batch_number }}
        </h6>
    </div>
    <div class="card-body px-0 pb-2">
        <div class="table-responsive px-3">
            <table class="table table-hover table-bordered align-middle text-center mb-0" style="font-size: 12px; border-radius: 10px; overflow: hidden;">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="align-middle">Tanggal</th>
                        <th rowspan="2" class="align-middle">Mikroba Uji</th>
                        <th colspan="2" class="align-middle bg-info bg-opacity-10">Jumlah Awal</th>
                        <th colspan="3" class="align-middle bg-success bg-opacity-10">Clean Condition</th>
                        <th colspan="3" class="align-middle bg-warning bg-opacity-10">Dirty Condition</th>
                        <th rowspan="2" class="align-middle">Petugas</th>
                    </tr>
                    <tr>
                        <th class="bg-info bg-opacity-10">CFU/mL</th>
                        <th class="bg-info bg-opacity-10">Log</th>
                        <th class="bg-success bg-opacity-10">CFU/mL</th>
                        <th class="bg-success bg-opacity-10">Log</th>
                        <th class="bg-success bg-opacity-10">% Bunuh</th>
                        <th class="bg-warning bg-opacity-10">CFU/mL</th>
                        <th class="bg-warning bg-opacity-10">Log</th>
                        <th class="bg-warning bg-opacity-10">% Bunuh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($existingHasil as $row)
                    <tr>
                        <td>{{ $row->tanggal ? $row->tanggal->format('d M Y') : '-' }}</td>
                        <td>
                            <span class="badge bg-gradient-info" style="border-radius: 6px;">{{ $row->mikroba_uji }}</span>
                        </td>
                        <td>{{ $row->awal_cfu_ml !== null ? number_format($row->awal_cfu_ml) : '-' }}</td>
                        <td>{{ $row->awal_log !== null ? number_format($row->awal_log, 2) : '-' }}</td>
                        <td>{{ $row->clean_cfu_ml !== null ? number_format($row->clean_cfu_ml) : '-' }}</td>
                        <td>{{ $row->clean_log !== null ? number_format($row->clean_log, 2) : '-' }}</td>
                        <td><strong>{{ $row->persen_clean !== null ? number_format($row->persen_clean, 2).'%' : '-' }}</strong></td>
                        <td>{{ $row->dirty_cfu_ml !== null ? number_format($row->dirty_cfu_ml) : '-' }}</td>
                        <td>{{ $row->dirty_log !== null ? number_format($row->dirty_log, 2) : '-' }}</td>
                        <td><strong>{{ $row->persen_dirty !== null ? number_format($row->persen_dirty, 2).'%' : '-' }}</strong></td>
                        <td>{{ $row->petugas ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- JAVASCRIPT: LIVE CALCULATION LOGIC --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Helper format ribuan
    function formatNumber(num) {
        if (num === null || isNaN(num) || num === '') return '0';
        return Number(num).toLocaleString('id-ID');
    }

    // Helper hitung CFU: Koloni * 10^pangkat
    function hitungCFU(koloni, pangkat) {
        if (koloni === '' || isNaN(koloni) || pangkat === '' || isNaN(pangkat)) return null;
        const k = parseFloat(koloni);
        const p = parseFloat(pangkat);
        return k * Math.pow(10, p);
    }

    // Helper hitung Log10
    function hitungLog(cfu) {
        if (cfu === null || isNaN(cfu) || cfu <= 0) return null;
        return Math.log10(cfu);
    }

    // Helper hitung % Mikroba Terbunuh
    function hitungPersen(awal, sisa) {
        if (awal === null || sisa === null || isNaN(awal) || isNaN(sisa) || awal <= 0) return null;
        let persen = ((awal - sisa) / awal) * 100;
        if (persen < 0) persen = 0;
        if (persen > 100) persen = 100;
        return persen;
    }

    // Referensi Card Live Rumus
    const liveKoloni = document.getElementById('live_koloni');
    const livePangkat = document.getElementById('live_pangkat');
    const liveCfu = document.getElementById('live_cfu');
    const liveLogCfu = document.getElementById('live_log_cfu');
    const liveLogResult = document.getElementById('live_log_result');
    const labelActiveTargetCfu = document.getElementById('labelActiveTargetCfu');
    const labelActiveTargetLog = document.getElementById('labelActiveTargetLog');

    function updateLiveFormulaCard(label, koloniVal, pangkatVal, cfuVal, logVal) {
        labelActiveTargetCfu.textContent = label;
        labelActiveTargetLog.textContent = label;

        liveKoloni.textContent = (koloniVal !== '' && !isNaN(koloniVal)) ? koloniVal : '0';
        livePangkat.textContent = (pangkatVal !== '' && !isNaN(pangkatVal)) ? pangkatVal : '0';

        if (cfuVal !== null) {
            liveCfu.textContent = formatNumber(cfuVal);
            liveLogCfu.textContent = formatNumber(cfuVal);
        } else {
            liveCfu.textContent = '0';
            liveLogCfu.textContent = '0';
        }

        if (logVal !== null) {
            liveLogResult.textContent = logVal.toFixed(2);
        } else {
            liveLogResult.textContent = '0.00';
        }
    }

    const mikroLabels = {
        'candida': 'Candida albicans',
        'staph'  : 'Staphylococcus aureus',
        'ecoli'  : 'Escherichia coli'
    };

    // Fungsi kalkulasi untuk satu mikroba
    function calculateOrganism(key, activeCondition) {
        const orgLabel = mikroLabels[key] || key;

        // 1. Suspensi Awal
        const suspKoloniInput = document.querySelector(`input[name="mikroba[${key}][suspensi_koloni]"]`);
        const suspPangkatInput = document.querySelector(`input[name="mikroba[${key}][suspensi_pangkat]"]`);
        const cfuOutput = document.getElementById(`cfu_${key}`);
        const logOutput = document.getElementById(`log_${key}`);

        const suspKoloni = suspKoloniInput ? suspKoloniInput.value : '';
        const suspPangkat = suspPangkatInput ? suspPangkatInput.value : '';
        const suspCfu = hitungCFU(suspKoloni, suspPangkat);
        const suspLog = hitungLog(suspCfu);

        if (suspCfu !== null) {
            if (cfuOutput) cfuOutput.value = suspCfu;
            if (logOutput) logOutput.value = suspLog !== null ? suspLog.toFixed(4) : '';
        } else {
            if (cfuOutput && !cfuOutput.dataset.manual) cfuOutput.value = '';
            if (logOutput && !logOutput.dataset.manual) logOutput.value = '';
        }

        // Nilai awal CFU efektif untuk perhitungan % terbunuh
        const effectiveAwalCfu = suspCfu !== null ? suspCfu : (cfuOutput && cfuOutput.value ? parseFloat(cfuOutput.value) : null);

        // 2. Clean Condition
        const cleanKoloniInput = document.getElementById(`clean_koloni_${key}`);
        const cleanPangkatInput = document.getElementById(`clean_pangkat_${key}`);
        const cleanCfuInput = document.getElementById(`clean_cfu_${key}`);
        const cleanLogInput = document.getElementById(`clean_log_${key}`);
        const cleanPersenInput = document.getElementById(`clean_persen_${key}`);

        const cleanKoloni = cleanKoloniInput ? cleanKoloniInput.value : '';
        const cleanPangkat = cleanPangkatInput ? cleanPangkatInput.value : '';
        const cleanCfu = hitungCFU(cleanKoloni, cleanPangkat);
        const cleanLog = hitungLog(cleanCfu);
        const cleanPersen = hitungPersen(effectiveAwalCfu, cleanCfu);

        if (cleanCfu !== null) {
            if (cleanCfuInput) cleanCfuInput.value = cleanCfu;
            if (cleanLogInput) cleanLogInput.value = cleanLog !== null ? cleanLog.toFixed(4) : '';
        }
        if (cleanPersen !== null && cleanPersenInput) {
            cleanPersenInput.value = cleanPersen.toFixed(2);
        }

        // 3. Dirty Condition
        const dirtyKoloniInput = document.getElementById(`dirty_koloni_${key}`);
        const dirtyPangkatInput = document.getElementById(`dirty_pangkat_${key}`);
        const dirtyCfuInput = document.getElementById(`dirty_cfu_${key}`);
        const dirtyLogInput = document.getElementById(`dirty_log_${key}`);
        const dirtyPersenInput = document.getElementById(`dirty_persen_${key}`);

        const dirtyKoloni = dirtyKoloniInput ? dirtyKoloniInput.value : '';
        const dirtyPangkat = dirtyPangkatInput ? dirtyPangkatInput.value : '';
        const dirtyCfu = hitungCFU(dirtyKoloni, dirtyPangkat);
        const dirtyLog = hitungLog(dirtyCfu);
        const dirtyPersen = hitungPersen(effectiveAwalCfu, dirtyCfu);

        if (dirtyCfu !== null) {
            if (dirtyCfuInput) dirtyCfuInput.value = dirtyCfu;
            if (dirtyLogInput) dirtyLogInput.value = dirtyLog !== null ? dirtyLog.toFixed(4) : '';
        }
        if (dirtyPersen !== null && dirtyPersenInput) {
            dirtyPersenInput.value = dirtyPersen.toFixed(2);
        }

        // Update Live Rumus Card
        if (activeCondition === 'clean') {
            updateLiveFormulaCard(`${orgLabel} (Clean)`, cleanKoloni, cleanPangkat, cleanCfu, cleanLog);
        } else if (activeCondition === 'dirty') {
            updateLiveFormulaCard(`${orgLabel} (Dirty)`, dirtyKoloni, dirtyPangkat, dirtyCfu, dirtyLog);
        } else {
            updateLiveFormulaCard(`${orgLabel} (Suspensi)`, suspKoloni, suspPangkat, suspCfu, suspLog);
        }
    }

    // Pasang listener pada input Bagian 1 (Suspensi)
    const suspInputs = document.querySelectorAll('.input-suspensi');
    suspInputs.forEach(function (input) {
        const key = input.getAttribute('data-key');
        input.addEventListener('input', function () {
            calculateOrganism(key, 'suspensi');
        });
        input.addEventListener('focus', function () {
            calculateOrganism(key, 'suspensi');
        });
    });

    // Pasang listener pada input Bagian 2 (Clean & Dirty)
    const kondisiInputs = document.querySelectorAll('.input-kondisi');
    kondisiInputs.forEach(function (input) {
        const key = input.getAttribute('data-key');
        const condition = input.getAttribute('data-cond');
        input.addEventListener('input', function () {
            calculateOrganism(key, condition);
        });
        input.addEventListener('focus', function () {
            calculateOrganism(key, condition);
        });
    });

    // Inisialisasi awal
    calculateOrganism('candida', 'suspensi');
});
</script>

@endsection
