@php
    $formId   = 'form-quality-' . uniqid();
    $isUpdate = $pemeriksaanQualityControl->isNotEmpty();
@endphp

@if(Auth::user()->hasAnyRole(['Quality Control', 'Admin']))
<div @if(Auth::user()->hasRole('Admin')) class="tab-pane fade overflow-scroll" id="quality-control" role="tabpanel" aria-labelledby="produksi-tab" @endif>
    @if($produksiSaatini->is_custom_formula != 1)
    <form method="POST"
        action="{{ $isUpdate ? url('/update-quality-control') : url('/store-quality-control-check') }}"
        id="{{ $formId }}">

        @csrf
        @if($isUpdate)
            @method('PUT')
        @endif

        <div class="card p-3 mb-3">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Parameter Pengujian</th>
                            <th>Nilai Standar</th>
                            <th>Nilai Aktual</th>
                            <th>Operator</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($isUpdate ? $pemeriksaanQualityControl : $quality->sortBy('urutan') as $item)
                        @php
                            $rowId = $isUpdate ? $item->id : $item->id;
                            $qc    = $isUpdate ? $item->qualityControl : $item;

                            $spesifikasi = $qc->nilai_standart;

                            // Logic otomatis berdasarkan pemeriksaan
                            if ($spesifikasi === 'otomatis') {
                                if ($qc->parameter_pengujian === 'Volume') {
                                    $spesifikasi = $produksiSaatini->product->ukuran;
                                } 
                            }
                        @endphp

                        <tr data-index="{{ $rowId }}">
                            <td>{{ $qc->parameter_pengujian }}</td>
                            <td>{{ $spesifikasi }}</td>

                            <td>
                                <input type="text"
                                    name="quality[{{ $rowId }}][nilai_real]"
                                    class="form-control px-2 border"
                                    value="{{ $isUpdate ? $item->nilai_real : '' }}"
                                    @if($isUpdate) disabled @endif>
                            </td>

                            <td>
                                <input type="text"
                                    name="quality[{{ $rowId }}][petugas_qc]"
                                    class="form-control px-2 border"
                                    value="{{ $isUpdate ? $item->petugas_qc : '' }}"
                                    @if($isUpdate) disabled @endif>
                            </td>

                            @if(!$isUpdate)
                                <input type="hidden"
                                    name="quality[{{ $rowId }}][quality_id]"
                                    value="{{ $item->id }}">
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <input type="hidden" name="produksi_id" value="{{ $produksiSaatini->id }}">
            </div>
        </div>

        {{-- ACTION BUTTON --}}
        <div class="d-flex gap-2 justify-content-end">

            @if($isUpdate)
                <button type="button"
                    class="btn btn-warning btn-ubah"
                    onclick="toggleEdit('{{ $formId }}')">
                    Ubah Data
                </button>

                <button type="button"
                    class="btn btn-danger btn-batal d-none"
                    onclick="cancelEdit('{{ $formId }}')">
                    Batal
                </button>
            @endif

            <button type="submit"
                class="btn btn-success btn-simpan
                @if($isUpdate) d-none @endif">
                Simpan
            </button>
        </div>

    </form>
    @else
    <div class="card p-3 text-center">
        <p class="m-0">Produksi Ini tidak memiliki Quality Control</p>
    </div>
    @endif
</div>
@endif