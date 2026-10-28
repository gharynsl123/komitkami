@extends('layouts.app')
@section('title', 'Komitkami Helper | Pantau Stabilitas')
@section('content')
<style>
    /* ==== Nav tabs scroll horizontal, tidak wrap ==== */
    .stabilitas-tab-scroll {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .stabilitas-tab-scroll .nav-tabs {
        flex-wrap: nowrap;
        border-bottom: 1px solid #dee2e6;
    }
    .stabilitas-tab-scroll .nav-item {
        flex: 0 0 auto;
    }
    .stabilitas-tab-scroll .nav-link {
        white-space: nowrap;
    }
    .stabilitas-tab-scroll::-webkit-scrollbar {
        height: 6px;
    }
    .stabilitas-tab-scroll::-webkit-scrollbar-thumb {
        background: #c9c9c9;
        border-radius: 3px;
    }
</style>
<div class="container-fluid">

    {{-- =========================================================
        TITLE
    ========================================================== --}}
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ url('/qc-produksi') }}" class="btn m-0 btn-secondary">Kembali</a>
        <h4 class="mb-0">Form Pemantauan Stabilitas Produk Jadi</h4>
    </div>

    {{-- =========================================================
        INFORMATION CARD
    ========================================================== --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-2">
                    <div class="text-muted small">Nama Produk</div>
                    <div class="fw-bold">{{ $stabilitas->produksi->product->name ?? '-' }}</div>
                </div>
                <div class="col-xl-3 col-md-6 mb-2">
                    <div class="text-muted small">No Batch</div>
                    <div class="fw-bold">{{ $stabilitas->produksi->batch_number ?? '-' }}</div>
                </div>
                <div class="col-xl-3 col-md-6 mb-2">
                    <div class="text-muted small">Tanggal Mulai</div>
                    <div class="fw-bold">
                        {{ optional($stabilitas->pemeriksaan_qc->created_at)->format('d-m-Y') ?? '-' }}
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-2 d-flex align-items-center">
                    <a href="{{ url('/print-stabilitas-produk-jadi', $stabilitas->id) }}" class="btn btn-primary m-0 btn-sm">
                        <i class="material-icons text-sm m-0">download</i>
                        Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
        STABILITY NAV TABS
    ========================================================== --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="stabilitas-tab-scroll">
                <ul class="nav nav-tabs" id="stabilitasTab" role="tablist">

                    {{-- ============ TAB BULAN 0 (data awal QC) ============ --}}
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link active"
                            id="bulan-0-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#bulan-0"
                            type="button"
                            role="tab"
                            aria-controls="bulan-0"
                            aria-selected="true"
                        >
                            Bulan 0
                            <span class="badge bg-success ms-1">Selesai</span>
                        </button>
                    </li>

                    {{-- ============ TAB BULAN 3..24 ============ --}}
                    @foreach($detail_stabilitas as $detail)
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link"
                                id="bulan-{{ $detail->bulan_ke }}-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#bulan-{{ $detail->bulan_ke }}"
                                type="button"
                                role="tab"
                                aria-controls="bulan-{{ $detail->bulan_ke }}"
                                aria-selected="false"
                            >
                                Bulan {{ $detail->bulan_ke }}
                                @if($detail->status === 'selesai')
                                    <span class="badge bg-success ms-1">Selesai</span>
                                @elseif($detail->status === 'proses')
                                    <span class="badge bg-warning text-dark ms-1">Proses</span>
                                @else
                                    <span class="badge bg-secondary ms-1">Menunggu</span>
                                @endif
                            </button>
                        </li>
                    @endforeach

                </ul>
            </div>

            {{-- =====================================================
                TAB CONTENT
            ====================================================== --}}
            <div class="tab-content pt-4" id="stabilitasTabContent">
                <div class="tab-pane fade show active" id="bulan-0" role="tabpanel" aria-labelledby="bulan-0-tab">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-1">Pemeriksaan Awal (Bulan 0)</h5>
                            <small class="text-muted">
                                Tanggal pemeriksaan:
                                {{ optional($stabilitas->pemeriksaan_qc->created_at)->format('d-m-Y') ?? '-' }}
                            </small>
                        </div>
                        <!-- cancel button -->
                        <div class="d-flex gap-2" id="btnGroupBulan0">
                            <button type="button" class="btn btn-outline-secondary btn-sm d-none" id="btnCancelBulan0" onclick="disableBulan0Cancel()">
                                Batal
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnEditBulan0" onclick="enableBulan0Edit()">
                                Ubah Data
                            </button>
                        </div>
                    </div>
                    <form action="{{ url('/update-quality-control') }}" method="POST" id="formBulan0">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="produksi_id" value="{{ $stabilitas->produksi_id }}">

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th width="30%">Parameter Pengujian</th>
                                        <th width="30%">Nilai Standar</th>
                                        <th width="25%">Nilai Aktual</th>
                                        <th width="15%">Operator</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pemeriksaan_qc->sortBy('urutan') as $qc)
                                    <tr>
                                        <td>{{ $qc->qualityControl->parameter_pengujian }}</td>
                                        <td>
                                            @if ($qc->qualityControl->parameter_pengujian === 'Volume' && $qc->qualityControl->nilai_standart === 'otomatis')
                                                {{ $qc->produksi->product->ukuran }}
                                            @else
                                                {{ $qc->qualityControl->nilai_standart }}
                                            @endif
                                        </td>
                                        <td>
                                            <input type="text" class="form-control border border-1 px-2 bulan0-input" name="quality[{{ $qc->id }}][nilai_real]" value="{{ $qc->nilai_real }}" disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control border border-1 px-2 bulan0-input" name="quality[{{ $qc->id }}][petugas_qc]" value="{{ $qc->petugas_qc }}" disabled>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary px-4 d-none" id="btnSimpanBulan0">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
                @foreach($detail_stabilitas as $detail)
                    <div
                        class="tab-pane fade"
                        id="bulan-{{ $detail->bulan_ke }}"
                        role="tabpanel"
                        aria-labelledby="bulan-{{ $detail->bulan_ke }}-tab"
                    >
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="mb-1">Pemantauan Bulan {{ $detail->bulan_ke }}</h5>
                                <small class="text-muted">
                                    Jadwal pemeriksaan:
                                    {{ $detail->tanggal_jadwal ? \Carbon\Carbon::parse($detail->tanggal_jadwal)->format('d-m-Y') : '-' }}
                                </small>
                            </div>
                        </div>

                        @if($detail->status === 'selesai')
                            <div class="d-flex justify-content-end align-items-center mb-3">
                                <div class="d-flex gap-2" id="btnGroupBulan{{ $detail->bulan_ke }}">
                                    <button type="button" class="btn btn-outline-secondary btn-sm d-none" id="btnCancelBulan{{ $detail->bulan_ke }}" onclick="disableBulanEdit({{ $detail->bulan_ke }})">
                                        Batal
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnEditBulan{{ $detail->bulan_ke }}" onclick="enableBulanEdit({{ $detail->bulan_ke }})">
                                        Ubah Data
                                    </button>
                                </div>
                            </div>
                            <form action="{{ url('/update-pantau-stabilitas/'.$detail->id) }}" method="POST" id="formBulan{{ $detail->bulan_ke }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label class="form-label">Petugas QC</label>
                                    <input type="text" class="form-control bulan{{ $detail->bulan_ke }}-input border px-2 py-1" name="petugas_qc" value="{{ $detail->petugas_qc }}" disabled required>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th width="40%">Parameter Pengujian</th>
                                                <th width="30%">Nilai Standar</th>
                                                <th width="30%">Nilai Aktual</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($acuan_quality->sortBy('urutan') as $items )
                                                @php
                                                    $hasil = $detail->hasil->where('quality_control_id', $items->id)->first();
                                                @endphp
                                            <tr>
                                                <td>{{ $items->parameter_pengujian }}</td>
                                                <td>{{ $items->nilai_standart }}</td>
                                                <td>
                                                    <input type="text" class="form-control border px-2 bulan{{ $detail->bulan_ke }}-input" name="nilai_real[{{ $items->id }}]" value="{{ $hasil ? $hasil->nilai_real : '' }}" disabled required>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary px-4 d-none" id="btnSimpanBulan{{ $detail->bulan_ke }}">Simpan Perubahan</button>
                                </div>
                            </form>
                        @elseif(now() >= $detail->tanggal_jadwal)
                            <form action="{{ url('/store-pantau-stabilitas') }}" method="POST">
                                @csrf
                                <input type="hidden" name="pemantauan_stabilitas_detail_id" value="{{ $detail->id }}">
                                
                                <div class="mb-3">
                                    <label class="form-label">Petugas QC</label>
                                    <input type="text" class="form-control border px-2 py-1" name="petugas_qc" required>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th width="40%">Parameter Pengujian</th>
                                                <th width="30%">Nilai Standar</th>
                                                <th width="30%">Nilai Aktual</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($acuan_quality->sortBy('urutan') as $items )
                                            @php
                                                $spesifikasi = $items->nilai_standart;
                                                if ($items->parameter_pengujian === 'Volume') {
                                                    $spesifikasi = $stabilitas->produksi->product->ukuran;
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $items->parameter_pengujian }}</td>
                                                <td>{{ $spesifikasi }}</td>
                                                <td><input type="text" class="form-control border px-2" name="nilai_real[{{ $items->id }}]" required></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary px-4">Simpan Pemeriksaan</button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-secondary">
                                Pemeriksaan bulan {{ $detail->bulan_ke }} belum dapat dilakukan.
                            </div>
                            <div class="text-center py-4">
                                <button type="button" class="btn btn-secondary" disabled>
                                    Pemeriksaan Belum Aktif
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>

<script>
    function enableBulan0Edit() {
        document.querySelectorAll('.bulan0-input').forEach(function (el) {
            el.disabled = false;
        });
        document.getElementById('btnSimpanBulan0').classList.remove('d-none');
        document.getElementById('btnCancelBulan0').classList.remove('d-none');
    }
    // cancelbutton
    function disableBulan0Cancel() {
        document.querySelectorAll('.bulan0-input').forEach(function (el) {
            el.disabled = true;
            el.value = el.defaultValue;
        });
        document.getElementById('btnSimpanBulan0').classList.add('d-none');
        document.getElementById('btnCancelBulan0').classList.add('d-none');
    }

    function enableBulanEdit(bulanKe) {
        document.querySelectorAll('.bulan' + bulanKe + '-input').forEach(function (el) {
            el.disabled = false;
        });
        document.getElementById('btnSimpanBulan' + bulanKe).classList.remove('d-none');
        document.getElementById('btnCancelBulan' + bulanKe).classList.remove('d-none');
    }
    
    function disableBulanEdit(bulanKe) {
        document.querySelectorAll('.bulan' + bulanKe + '-input').forEach(function (el) {
            el.disabled = true;
            el.value = el.defaultValue;
        });
        document.getElementById('btnSimpanBulan' + bulanKe).classList.add('d-none');
        document.getElementById('btnCancelBulan' + bulanKe).classList.add('d-none');
    }
</script>
@endsection
