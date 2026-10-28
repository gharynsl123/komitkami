@php
$formId = 'form-proses-' . uniqid();
$isUpdate = $pemeriksaanProsesProduksi->isNotEmpty();
@endphp

<div class="tab-pane fade overflow-scroll" id="produksi" role="tabpanel">
    @if($produksiSaatini->is_custom_formula != 1)
    <form method="POST" action="{{ $isUpdate ? url('/update-data-proses') : url('/store-data-proses') }}"
        id="{{ $formId }}" class="form-proses-produksi">
        @csrf
        @if($isUpdate)
        @method('PUT')
        @endif
        <div class="card p-3 mb-3">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tahapan Proses</th>
                            <th>Acuan</th>
                            <th>Nilai Aktual</th>
                            <th>Operator</th>
                            @if($isUpdate)
                            <th>Diperiksa Oleh</th>
                            @endif
                            @if(Auth::user()->hasAnyRole(['Admin', 'Production Manager']))
                            <th>Jam Pengerjaan</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($isUpdate ? $pemeriksaanProsesProduksi : $tahapanProses as $item)
                        @php
                        $data = $isUpdate ? $item->tapahanProses : $item;
                        $rowId = $isUpdate ? $item->id : $item->id;
                        @endphp

                        <tr data-index="{{ $rowId }}">
                            <td style="text-wrap: wrap; text-align: left; max-width: 360px;">
                                {{ trim($data->nama_proses) }}
                            </td>
                            <td>
                                {{ $data->acuan }}
                            </td>
                            <td class="d-flex gap-2">
                                @if(Str::contains(strtolower($data->acuan), 'menit'))
                                <input type="text" name="proses[{{ $rowId }}][keterangan]"
                                    class="form-control border border-1 px-2 py-1 durasi-ketarangan" readonly value="{{ $item->keterangan ?? '' }}" @if($isUpdate) disabled @endif>

                                <button type="button" class="btn m-0 btn-sm btn-primary keterangan-input @if($isUpdate) d-none @endif"
                                    data-index="{{ $rowId }}">
                                    Mulai
                                </button>

                                <button type="button" class="btn m-0 btn-sm btn-danger stop-time d-none"
                                    data-index="{{ $rowId }}">
                                    Stop
                                </button>
                                @else
                                <input type="text" name="proses[{{ $rowId }}][keterangan]" class="form-control border border-1 px-2 py-1"
                                    value="{{ $isUpdate ? $item->keterangan : '' }}" @if($isUpdate) disabled @endif>
                                @endif
                            </td>

                            <td>
                                <input type="text" name="proses[{{ $rowId }}][operator]" class="form-control border border-1 px-2 py-1"
                                    value="{{ $isUpdate ? $item->penanggung_jawab : '' }}" @if($isUpdate) disabled @endif>
                            </td>

                            @if($isUpdate)
                            <td>
                                <input type="text" class="form-control border border-1 px-2 py-1" 
                                    readonly 
                                    placeholder="Belum Di Periksa. Segera Lapor Ke QC" 
                                    value="{{ $item->diperiksa_oleh }}" @if($isUpdate) disabled @endif>
                            </td>
                            @endif

                            @if(Auth::user()->hasAnyRole(['Admin', 'Production Manager']))
                            <td>
                                <input type="text" name="proses[{{ $rowId }}][jam_pengerjaan]"
                                    class="form-control input-durasi border border-1 px-2 py-1"
                                    value="{{ $isUpdate ? $item->durasi : 'Tidak Diketahui' }}" @if($isUpdate) disabled @endif>
                            </td>
                            @else
                                <input type="hidden" name="proses[{{ $rowId }}][jam_pengerjaan]" class="input-durasi" value="{{ $isUpdate ? $item->durasi : '' }}" @if($isUpdate) disabled @endif>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <input type="hidden" name="produksi_id" value="{{ $produksiSaatini->id }}">
            </div>
        </div>

        {{-- ACTION BUTTON --}}
        <div class="d-flex justify-content-end gap-2">
            @if($isUpdate)
                @if($pemeriksaanProsesProduksi->first()->status == 'Belum Diperiksa')
                <button type="button"
                    class="btn btn-primary btn-open-verifikasi-modal"
                    data-bs-toggle="modal"
                    data-bs-target="#verificationModal"
                    data-action="{{ url('/update-status-tahapan', $pemeriksaanProsesProduksi->first()->id) }}"
                    data-source="proses">
                    Verifikasi
                </button>
                @endif
            <button type="button" class="btn btn-warning btn-ubah" onclick="toggleEdit('{{ $formId }}')">
                Ubah Data
            </button>

            <button type="button" class="btn btn-danger btn-batal d-none" onclick="cancelEdit('{{ $formId }}')">
                Batal
            </button>
            @endif

            <button type="submit" class="btn btn-success btn-simpan @if($isUpdate) d-none @endif">
                Simpan
            </button>
        </div>
    </form>
    @else
    <div class="card p-3 text-center">
        <p class="m-0">Produksi Ini tidak memiliki Tahapan Proses</p>
    </div>
    @endif
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const startTimes = {};
    const intervals = {};

    function attachTimerHandlers(row, index) {
        const startBtn = row.querySelector(".keterangan-input");
        const stopBtn = row.querySelector(".stop-time");
        const durasiInput = row.querySelector(".durasi-ketarangan");
        const jam = row.querySelector(".input-durasi");

        if (!startBtn || !stopBtn || !durasiInput) return;

        // hindari double binding
        if (startBtn.dataset.bound === "true") return;
        startBtn.dataset.bound = "true";
        stopBtn.dataset.bound = "true";

        startBtn.addEventListener("click", () => {
            const now = new Date();
            startTimes[index] = now;
            
            const pad = (n) => n.toString().padStart(2, '0');
            const timeStr = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
            startBtn.dataset.startTime = timeStr;

            startBtn.classList.add("d-none");
            stopBtn.classList.remove("d-none");
            if (jam) jam.value = `${timeStr} - (Berjalan)`;

            durasiInput.value = "0 detik";

            intervals[index] = setInterval(() => {
                const diffMs = new Date() - startTimes[index];
                const diffSeconds = Math.floor(diffMs / 1000);
                const hours = Math.floor(diffSeconds / 3600);
                const minutes = Math.floor((diffSeconds % 3600) / 60);
                const seconds = diffSeconds % 60;

                let durasiText = "";
                if (hours > 0) durasiText += `${hours} jam `;
                if (minutes > 0) durasiText += `${minutes} menit `;
                durasiText += `${seconds} detik`;

                durasiInput.value = durasiText.trim();
            }, 1000);
        });

        stopBtn.addEventListener("click", () => {
            const now = new Date();
            const startTime = startTimes[index];
            const startTimeStr = startBtn.dataset.startTime;

            if (!startTime) return;

            clearInterval(intervals[index]);

            const diffMs = now - startTime;
            const diffSeconds = Math.floor(diffMs / 1000);
            const hours = Math.floor(diffSeconds / 3600);
            const minutes = Math.floor((diffSeconds % 3600) / 60);
            const seconds = diffSeconds % 60;

            let durasiText = "";
            if (hours > 0) durasiText += `${hours} jam `;
            if (minutes > 0) durasiText += `${minutes} menit `;
            durasiText += `${seconds} detik`;

            durasiInput.value = durasiText.trim();
            
            const pad = (n) => n.toString().padStart(2, '0');
            const stopTimeStr = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
            
            if (jam) jam.value = `${startTimeStr} - ${stopTimeStr}`;

            stopBtn.disabled = true;
            stopBtn.classList.add("d-none");
        });
    }

    // Pasang handler ke semua tr (baik default maupun edit)
    document.querySelectorAll("tr[data-index]").forEach(row => {
        const index = row.getAttribute("data-index");
        attachTimerHandlers(row, index);
    });

    window.attachTimerHandlers = attachTimerHandlers;
});
</script>