@php
$formId = 'form-rekonsiliasi-' . uniqid();
$isUpdate = $rekonsiliasiBahanKemas->isNotEmpty();
@endphp

<div class="tab-pane fade" id="rekonsiliasi" role="tabpanel">
    <form method="POST" action="{{ $isUpdate ? url('/update-data-rekonsilasi') : url('/rekonsiliasa-bahan-kemas') }}" id="{{ $formId }}">
        @csrf
        @if($isUpdate)
        @method('PUT')
        @endif
        <input type="hidden" name="produksi_id" value="{{ $produksiSaatini->id }}">

        <div class="card p-3 mb-3">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Tersedia</th>
                            <th>Terpakai</th>
                            <th>Keterangan</th>
                            <th>Operator</th>
                            @if($isUpdate)
                            <th>Diperiksa Oleh</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    @if($isUpdate)
                        @foreach($rekonsiliasiBahanKemas as $item)
                        @php
                        $nama_barang = $produksiSaatini->is_custom_formula ? $item->nama_barang : $item->bahanKemas->name;
                        $jumlah = $produksiSaatini->is_custom_formula ? $item->tersedia : $item->bahanKemas->jumlah;
                        @endphp
                        <tr>
                            <td>
                                <input type="text" readonly
                                    value="{{ $nama_barang }}"
                                    class="form-control border border-1 px-2">
                            </td>

                            <td>
                                <input type="number" readonly
                                    value="{{ $jumlah }}"
                                    class="form-control border border-1 px-2">
                            </td>

                            <td>
                                <input type="number"
                                    name="rekonsiliasi[{{ $item->id }}][terpakai]"
                                    value="{{ $item->terpakai ?? '' }}"
                                    class="form-control border border-1 px-2">
                            </td>

                            <td>
                                <input type="text"
                                    name="rekonsiliasi[{{ $item->id }}][keterangan]"
                                    value="{{ $item->keterangan ?? '' }}"
                                    class="form-control border border-1 px-2">
                            </td>

                            <td>
                                <input type="text"
                                    name="rekonsiliasi[{{ $item->id }}][operator]"
                                    value="{{ $item->operator ?? '' }}"
                                    class="form-control border border-1 px-2">
                            </td>
                            @if($isUpdate)
                            <td>
                                <input type="text"
                                    name="rekonsiliasi[{{ $item->id }}][diperiksa_oleh]"
                                    readonly
                                    value="{{ $item->diperiksa_oleh ?? '' }}"
                                    class="form-control border border-1 px-2" @if($isUpdate) disabled @endif>
                            </td>
                            @endif

                            <input type="hidden"
                                name="rekonsiliasi[{{ $item->id }}][id]"
                                value="{{ $item->id }}">
                        </tr>
                        @endforeach
                    @else
                        @if($produksiSaatini->is_custom_formula)
                            @foreach(json_decode($produksiSaatini->custom_formula_kemas, true) ?? [] as $index => $item)
                            @php
                            $inv = $inventories->firstWhere('id', $item['inventory_id']);
                            @endphp
                            <tr>
                                <td>
                                    <input type="text" readonly value="{{ $inv->name ?? 'Tidak ditemukan' }}" class="border px-2 form-control">
                                    <input type="hidden" name="rekonsiliasi[custom_{{ $index }}][nama_barang]" value="{{ $inv->name ?? 'Tidak ditemukan' }}">
                                    <input type="hidden" name="rekonsiliasi[custom_{{ $index }}][tersedia]" value="{{ $item['jumlah'] }}">
                                </td>
                                <td>
                                    <input type="number" readonly value="{{ $item['jumlah'] }}" class="border px-2 form-control">
                                </td>
                                <td>
                                    <input type="number" placeholder="Masukkan nilai real" name="rekonsiliasi[custom_{{ $index }}][terpakai]"
                                        class="border px-2 form-control">
                                </td>
                                <td>
                                    <input type="text" placeholder="Keterangan" name="rekonsiliasi[custom_{{ $index }}][keterangan]"
                                        class="border px-2 form-control">
                                </td>
                                <td>
                                    <input type="text" placeholder="Tuliskan Operator" name="rekonsiliasi[custom_{{ $index }}][operator]"
                                        class="border px-2 form-control">
                                </td>
                            </tr>
                            @endforeach
                        @else
                            @foreach($rekonsiliasiBarang as $item)
                            <tr>
                                <td>
                                    <input type="text" readonly
                                        value="{{ $item->name }}"
                                        class="form-control border border-1 px-2">
                                </td>

                                <td>
                                    <input type="number" readonly
                                        value="{{ $item->jumlah }}"
                                        class="form-control border border-1 px-2">
                                </td>

                                <td>
                                    <input type="number"
                                        name="rekonsiliasi[{{ $item->id }}][terpakai]"
                                        class="form-control border border-1 px-2">
                                </td>

                                <td>
                                    <input type="text"
                                        name="rekonsiliasi[{{ $item->id }}][keterangan]"
                                        class="form-control border border-1 px-2">
                                </td>

                                <td>
                                    <input type="text"
                                        name="rekonsiliasi[{{ $item->id }}][operator]"
                                        class="form-control border border-1 px-2">
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end">
            @if($isUpdate)
                @if($rekonsiliasiBahanKemas->first()->status == 'Belum Diperiksa' || $produksiSaatini->status == 'confirm')
                    <button type="button"
                        class="btn btn-primary btn-open-verifikasi-modal"
                        data-bs-toggle="modal"
                        data-bs-target="#verificationModal"
                        data-action="{{ url('/update-status-rekonsiliasi', $rekonsiliasiBahanKemas->first()->id) }}"
                        data-source="rekonsiliasi">
                        @if($rekonsiliasiBahanKemas->first()->status == 'Diverifikasi' && $produksiSaatini->status != 'close')
                        Tutup Produksi
                        @else
                        Verifikasi
                        @endif
                    </button>
                @endif
            <button type="button"
                class="btn btn-warning btn-ubah"
                onclick="toggleEdit('{{ $formId }}')">
                Ubah Data
            </button>

            <button type="button"
                class="btn btn-danger d-none btn-batal"
                onclick="cancelEdit('{{ $formId }}')">
                Batal
            </button>
            @endif

            <button type="submit"
                class="btn btn-success @if($isUpdate) d-none @endif btn-simpan">
                Simpan
            </button>
        </div>
    </form>
</div>