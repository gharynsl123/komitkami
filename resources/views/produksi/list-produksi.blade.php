<style>
    .produksi-container {
        color: #000000;
    }

    .step-flow {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0px !important;
        flex-wrap: nowrap;
    }

    .step-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        min-width: 120px;
    }

    .step-box {
        padding: 5px 13px;
        border: 3px solid #d3d3d3;
        border-radius: 8px;
        background-color: #fff;
        font-weight: 500;
        font-size: 12px;
        text-align: center;
        min-width: 100%;
    }

    .step-line {
        flex: 1;
        height: 3px;
        background-color: #d3d3d3;
        align-self: center;
        transform: translateY(-10px);
    }

    .status-note {
        font-size: 10px;
        margin-top: 5px;
        color: #000000;
        text-align: center;
    }

    .status-grey {
        background-color: #f0f0f0;
        border-color: #d3d3d3;
        color: #555;
    }

    .status-blue {
        background-color: #cce5ff;
        border-color: #66b0ff;
        color: #004085;
    }

    .status-yellow {
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
    }

    .status-dark-yellow {
        background-color: #f9e79f;
        border-color: #f7dc6f;
        color: #7d6608;
        position: relative;
    }

    .status-green {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

</style>
<div class="produksi-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0">Daftar Produksi Sedang Berlangsung</h5>
    </div>

    
    @foreach($tikets as $item)
    <a class="text-dark card p-3 mb-3" href="{{url('/mulai-produksi', $item->batch_number)}}" >
        <h6>{{ $item->product->name }}</h6>
        <div class="d-flex justify-content-between">
            <div class="produksi-info">
                Nomor Batch: {{ $item->batch_number }}  <br>
                Ukuran Batch: {{ $item->batch_size }}kg 
            </div>
    
            <div class="status-area">
                Tanggal Produksi: 
                {{ $item->tanggal_produksi->format('d M Y') }} 
                @if($item->tanggal_produksi && $item->tanggal_produksi->isSameDay(\Carbon\Carbon::now()))
                    <strong>(Hari ini)</strong>
                @endif
                <br>
                Status: {{ $item->status }}
            </div>
        </div>

        @php
            $kebersihanStatus = getStepStatus($item->checkKebersihan);
            $penimbanganStatus = getStepStatus($item->checkPenimbangan, ['no_batch', 'hasil_timbang', 'operator_penimbangan']);
            $prosesStatus = getStepStatus($item->checkProses, ['keterangan', 'penanggung_jawab']);
            $qcStatus = getStepStatus($item->checkQualityControl, ['nilai_real', 'petugas_qc']);
            $rekonsiliasiStatus = getStepStatus($item->checkRekonsiliasi, ['terpakai', 'keterangan', 'operator']);
        @endphp

        <div class="step-flow mt-4">
            @foreach([
                ['label' => 'Kebersihan', 'status' => $kebersihanStatus],
                ['label' => 'Penimbangan', 'status' => $penimbanganStatus],
                ['label' => 'Proses Mixing', 'status' => $prosesStatus],
                ['label' => 'Quality Control', 'status' => $qcStatus],
                ['label' => 'Rekonsiliasi', 'status' => $rekonsiliasiStatus],
            ] as $i => $step)
                <div class="step-container">
                    <div class="step-box {{ $step['status'] }}">
                        {{ $step['label'] }}
                    </div>
                    <div class="status-note ">
                        @if($step['status'] == 'status-dark-yellow')
                            masih ada yg kosong
                        @elseif($step['status'] == 'status-yellow')
                            @if($step['label'] != 'Quality Control')
                            Lakukan Verifikasi Segera
                            @else
                            Data sudah diisi
                            @endif
                        @elseif($step['status'] == 'status-blue')
                            Sedang Berjalan
                        @elseif($step['status'] == 'status-green')
                            selesai
                        @else
                            belum dimulai
                        @endif
                    </div>
                </div>
                @if($i < 4)
                    <div class="step-line"></div>
                @endif
            @endforeach
        </div>
    </a>
    @endforeach

    @if($tikets->whereIn('status', ['confirm', 'on-going'])->isEmpty())
    <div class="card">
        <p class="text-center my-3">Belum ada Produksi Yang Sedang Berlangsung</p>
    </div>
    @endif
</div>