@php
    $formId = 'form-penimbangan-' . uniqid();
    $isUpdate = $pemeriksaanPenimbang->isNotEmpty();
@endphp

<div class="tab-pane fade" id="penimbangan" role="tabpanel">
    @if($produksiSaatini->is_custom_formula != 1)
    <form
        method="POST"
        action="{{ $isUpdate ? url('/update-data-penimbangan') : url('/store-penimbangan') }}"
        id="{{ $formId }}"
        class="form-penimbangan"
    >
        @csrf
        @if($isUpdate)
            @method('PUT')
        @endif

        <div class="card p-3 mb-3">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Nama Bahan Baku</th>
                        <th>No Batch</th>
                        <th>Takaran (gram)</th>
                        <th>Hasil Timbang</th>
                        <th>Operator</th>
                        @if($isUpdate)
                            <th>Diperiksa Oleh</th>
                        @endif
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($isUpdate ? $pemeriksaanPenimbang : $penimbanganBahanBaku as $key => $item)
                        @php
                            $data = $isUpdate ? $item->bahanBaku : $item['formula'];
                            $hasil = smartRound(($data->jumlah / 100) * $produksiSaatini->batch_size);
                            $rowId = $isUpdate ? $item->id : $item['formula']->id;
                            $currentBatch = data_get($item, 'no_batch');

                            $latestBatch = collect($penimbanganBahanBaku[$key]['batches'] ?? [])
                                ->pluck('nomor_batch')
                                ->unique()
                                ->implode(', ');

                            $latestBatchNormalized = empty($latestBatch) ? null : $latestBatch;

                            if ($isUpdate) {
                                if (!empty($latestBatch)) {
                                    $displayValue = $latestBatch;
                                } else {
                                    $displayValue = $currentBatch ?? '';
                                }
                            } else {
                                $displayValue = !empty($latestBatch)
                                    ? $latestBatch
                                    : '';
                            }
                        @endphp
                        <tr>
                            <td>
                                <input type="text" class="form-control border border-1 px-2" readonly
                                    value="{{ $data->name }}">
                            </td>
                            <td>
                                <input type="text"
                                    name="penimbangan[{{$rowId}}][no_batch]" disabled
                                    class="form-control border border-1 px-2"
                                    value="{{ $displayValue }}">
                                @if(
                                    $isUpdate &&
                                    in_array($currentBatch, [null, '-', '']) &&
                                    !is_null($latestBatchNormalized)
                                )
                                <small class="text-danger">
                                    data diperbaharui. Tekan Ubah Data dan Simpan Kembali
                                </small>
                                @endif
                            </td>

                            <td>
                                <input type="text" readonly class="form-control border border-1 px-2"
                                    value="{{ formatJumlah($produksiSaatini->batch_size == 100 ? $data->jumlah : $hasil) }}">
                            </td>

                            <td>
                                <input type="text"
                                    name="penimbangan[{{$rowId}}][hasil_timbang]"
                                    class="form-control border border-1 px-2"
                                    value="{{ $isUpdate ? formatJumlah($item->hasil_timbang) : '' }}"
                                    disabled>
                            </td>

                            <td>
                                <input type="text"
                                    name="penimbangan[{{$rowId}}][operator_penimbangan]"
                                    class="form-control border border-1 px-2"
                                    value="{{ $isUpdate ? $item->operator_penimbangan : '' }}"
                                    disabled>
                            </td>

                            @if($isUpdate)
                            <td>
                                <input type="text" readonly class="form-control border border-1 px-2"
                                    placeholder = "Belum Di Periksa. Segera Lapor Ke QC"
                                    value="{{ $item->diperiksa_oleh }}">
                            </td>
                            @endif
                        </tr>
                        <input type="hidden" name="penimbangan[{{$rowId}}][formula_id]" readonly value="{{ $data->id }}">
                    @endforeach
                    </tbody>
                </table>

                <input type="hidden" name="produksi_id" value="{{ $produksiSaatini->id }}">
                <input type="hidden" name="durasi">
            </div>
        </div>

        {{-- ACTION BUTTON --}}
        <div class="d-flex gap-2 justify-content-end">
            @if($isUpdate && $pemeriksaanPenimbang->first()->status == 'Belum Diperiksa')
                <button type="button"
                    class="btn btn-primary btn-open-verifikasi-modal"
                    data-bs-toggle="modal"
                    data-bs-target="#verificationModal"
                    data-action="{{ url('/update-status-penimbangan', $pemeriksaanPenimbang->first()->id) }}"
                    data-source="penimbangan">
                    Verifikasi
                </button>
            @endif

            <button type="button"
                class="btn btn-warning btn-ubah"
                onclick="toggleEdit('{{ $formId }}')">
                {{ $isUpdate ? 'Ubah Data' : 'Mulai Mengerjakan' }}
            </button>

            <button type="button"
                class="btn btn-danger d-none btn-batal"
                onclick="cancelEdit('{{ $formId }}')">
                Batal
            </button>

            <button type="submit"
                class="btn btn-success d-none btn-simpan">
                Simpan
            </button>
        </div>
    </form>
    @else
    <div class="card p-3 text-center">
        <p class="m-0">Tidak ada bahan baku yang perlu ditimbang</p>
    </div>
    @endif
</div>

<script>
let startTimeValue = null;

// hook khusus sebelum toggle
window.beforeToggleEdit = function(form) {
    if (!form.classList.contains('form-penimbangan')) return;

    startTimeValue = getCurrentTime();

    const durasiInput = form.querySelector('input[name="durasi"]');
    if (durasiInput) {
        durasiInput.value = startTimeValue;
    }
};

// hook submit global
document.addEventListener('submit', function(e) {
    const form = e.target;

    if (!form.classList.contains('form-penimbangan')) return;

    const durasiInput = form.querySelector('input[name="durasi"]');
    if (durasiInput && startTimeValue) {
        durasiInput.value = startTimeValue + ' - ' + getCurrentTime();
    }
});

function getCurrentTime(){
    const d = new Date();
    let h = d.getHours();
    let m = d.getMinutes();
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    m = m < 10 ? '0' + m : m;
    return h + ":" + m + " " + ampm;
}
</script>
<!-- DONE ONE -->