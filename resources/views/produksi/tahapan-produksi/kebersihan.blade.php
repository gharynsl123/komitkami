@php $formId = 'form-kebersihan-' . uniqid(); @endphp

<div class="tab-pane fade show active" id="kebersihan" role="tabpanel"
    aria-labelledby="kebersihan-tab">
    @if($pemeriksaanKebersihan->isNotEmpty())
    <form method="POST" action="{{ url('/update-data-kebersihan') }}" id="{{ $formId }}">
        @csrf
        @method('PUT')
        @foreach($pemeriksaanKebersihan as $ruangProduksiId => $items)
        <div class="card p-3 mb-3">
            <input type="hidden" name="produksi_id" value="{{$produksiSaatini->id}}">

            <h4>{{ $items->first()->bagianKebersihan->ruangProduksi->nama_ruangan }}</h4>
            
            @if($items->first()->bagianKebersihan->ruangProduksi->nama_ruangan == 'Ruang Mixing')
                @php

                    $ruangId = $items->first()->bagianKebersihan->ruangProduksi->id;
                    $alatGroup = collect(data_get($riwayatAlat, $ruangId, []));

                    // ambil mesin
                    $alatMesin = $alatGroup->filter(fn($a) => Str::startsWith($a['nama_alat'], 'Mesin'))->first();

                    // ambil tangki
                    $alatTangki = $alatGroup->filter(fn($a) => Str::startsWith($a['nama_alat'], 'Tangki'))->first();
                @endphp


                <div class="mb-3 row">
                    <label>Riwayat Alat</label>
                    {{-- Mesin Mixing --}}
                    <div class="col-md-6">
                        <input type="hidden" name="riwayat_alat[{{ $ruangId }}][mesin][id]"
                            value="{{ $alatMesin->id ?? '' }}">

                        <select name="riwayat_alat[{{ $ruangId }}][mesin][nama]" class="form-control border px-2" @if($alatMesin) disabled @endif>
                            <option value="">Pilih Mesin</option>
                            @for($i = 1; $i <= 3; $i++)
                                @php $value = "Mesin Mixing $i"; @endphp
                                <option value="{{ $value }}" {{ ($alatMesin && $alatMesin->nama_alat === $value) ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Tangki Mixing --}}
                    <div class="col-md-6">
                        <input type="hidden" name="riwayat_alat[{{ $ruangId }}][tangki][id]"
                            value="{{ $alatTangki->id ?? '' }}">

                        <select name="riwayat_alat[{{ $ruangId }}][tangki][nama]" class="form-control border px-2 mb-3" @if($alatTangki) disabled @endif>
                            <option value="">Pilih Tangki</option>
                            @for($i = 1; $i <= 16; $i++)
                                @php $value = "Tangki Mixing $i"; @endphp
                                <option value="{{ $value }}" {{ ($alatTangki && $alatTangki->nama_alat === $value) ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

            @elseif($items->first()->bagianKebersihan->ruangProduksi->nama_ruangan == 'Ruang Filling')
                @php
                    $ruangId = $items->first()->bagianKebersihan->ruangProduksi->id;

                    // Ambil grup riwayat alat untuk ruang ini secara aman (bisa handle array atau Collection)
                    $alatGroup = collect(data_get($riwayatAlat, $ruangId, []));

                    // sekarang aman pakai collection helpers
                    $alatMesin = $alatGroup->firstWhere('nama_alat', 'like', 'Mesin%');
                    $alatTangki = $alatGroup->firstWhere('nama_alat', 'like', 'Tangki%');

                    // kalau butuh filling:
                    $alatFilling = $alatGroup->first();
                @endphp
                <div class="mb-3">
                    <label>Riwayat Alat (Jika Ada)</label>
                    <input type="hidden" name="riwayat_alat[{{ $items->first()->bagianKebersihan->ruangProduksi->id }}][filling][id]" 
                        value="{{ $alatFilling->id ?? '' }}">
                    <select name="riwayat_alat[{{ $items->first()->bagianKebersihan->ruangProduksi->id }}][filling][nama]"
                        class="form-control border px-2 mb-3" @if($alatFilling) disabled @endif>
                        <option value="">Pilih Alat</option>
                        <option value="Mesin Filling 1" @if($alatFilling && $alatFilling->nama_alat === 'Mesin Filling 1') selected @endif>Mesin Filling 1</option>
                        <option value="Mesin Filling 2" @if($alatFilling && $alatFilling->nama_alat === 'Mesin Filling 2') selected @endif>Mesin Filling 2</option>
                    </select>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table m-0 p-0">
                    <thead>
                        <tr>
                            <th>Bagian Yang Dibersihkan</th>
                            <th>hasil</th>
                            <th>Di bersihkan Oleh</th>
                            <th>Diperiksa Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>{{ $item->bagianKebersihan->nama_bagian }}</td>
                            <td>
                                <select name="kebersihan[{{ $item->id }}][hasil]"  class="form-control border px-2" disabled id="">
                                    <option value="Bersih" @if($item->hasil == "Bersih") selected @endif >Bersih</option>
                                    <option value="Kurang Bersih" @if($item->hasil == "Kurang Bersih") selected @endif >Kurang Bersih</option>
                                    <option value="Kotor" @if($item->hasil == "Kotor") selected @endif >Kotor</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" disabled class="form-control border px-2" name="kebersihan[{{ $item->id }}][dibersihkan_oleh]" value="{{$item->dibersihkan_oleh}}">
                            </td>
                            <td>
                            <input type="text" disabled readonly class="form-control border px-2" name="kebersihan[{{ $item->id }}][diperiksa_oleh]" placeholder="Belum Di periksa. Segera lapor ke QC" value="{{$item->diperiksa_oleh}}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
        <div class="d-flex gap-2 text-end justify-content-end">
            @if($pemeriksaanKebersihan->first()->first()->status == 'Belum Diperiksa')
            <button type="button" class="btn btn-primary mb-0 btn-open-verifikasi-modal" 
                data-bs-toggle="modal"
                data-bs-target="#verificationModal"
                data-action="{{ url('/update-status-kebersihan', $pemeriksaanKebersihan->first()->first()->id) }}"
                data-source="kebersihan">
                Verifikasi
            </button>
            @endif
            <button type="button" class="btn mb-0 btn-warning btn-ubah" onclick="toggleEdit('{{ $formId }}')">Ubah Data</button>
            <button type="button" class="btn mb-0 btn-danger d-none btn-batal" onclick="cancelEdit('{{ $formId }}')">Batal</button>
            <button type="submit" class="btn mb-0 btn-success d-none btn-simpan" form="{{ $formId }}">Simpan</button>
        </div>
    </form>
    @endif

    @if($pemeriksaanKebersihan->isEmpty())
    <form action="{{ url('/store-periksaan-ruangan') }}" enctype="multipart/form-data" method="post"
        id="pemeriksaanForm">
        @csrf
        @foreach ($rooms as $room)
        <div class="card p-3 mb-3">
            <h4>{{$room->nama_ruangan}}</h4>
            @if($room->nama_ruangan == 'Ruang Mixing')
            <div class="mb-3 row">
                <label for="">
                    Riwayat Alat (Jika Ada)
                </label>
                <div class="col-md-6">
                    <select name="riwayat_alat[{{ $room->id }}][]" class="form-control border px-2 mb-3">
                        <option value="">Pilih Mesin</option>
                        <option value="Mesin Mixing 1">Mesin Mixing 1</option>
                        <option value="Mesin Mixing 2">Mesin Mixing 2</option>
                        <option value="Mesin Mixing 3">Mesin Mixing 3</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <select name="riwayat_alat[{{ $room->id }}][]" class="form-control border px-2 mb-3">
                        <option value="">Pilih Tangki</option>
                        <option value="Tangki Mixing 1">Tangki Mixing 1</option>
                        <option value="Tangki Mixing 2">Tangki Mixing 2</option>
                        <option value="Tangki Mixing 3">Tangki Mixing 3</option>
                        <option value="Tangki Mixing 4">Tangki Mixing 4</option>
                        <option value="Tangki Mixing 5">Tangki Mixing 5</option>
                        <option value="Tangki Mixing 6">Tangki Mixing 6</option>
                        <option value="Tangki Mixing 7">Tangki Mixing 7</option>
                        <option value="Tangki Mixing 8">Tangki Mixing 8</option>
                        <option value="Tangki Mixing 9">Tangki Mixing 9</option>
                        <option value="Tangki Mixing 10">Tangki Mixing 10</option>
                        <option value="Tangki Mixing 11">Tangki Mixing 11</option>
                        <option value="Tangki Mixing 12">Tangki Mixing 12</option>
                        <option value="Tangki Mixing 13">Tangki Mixing 13</option>
                        <option value="Tangki Mixing 14">Tangki Mixing 14</option>
                        <option value="Tangki Mixing 15">Tangki Mixing 15</option>
                        <option value="Tangki Mixing 16">Tangki Mixing 16</option>
                    </select>
                </div>
            </div>
            @elseif($room->nama_ruangan == 'Ruang Filling')
            <div class="mb-3">
                <label for="">
                    Riwayat Alat (Jika Ada)
                </label>
                <select name="riwayat_alat[{{ $room->id }}][]" class="form-control border px-2 mb-3">
                    <option value="">pilih alat</option>
                    <option value="Mesin Filling 1">Mesin Filling 1</option>
                    <option value="Mesin Filling 2">Mesin Filling 2</option>
                </select>
            </div>
            @endif
            <div class="table-responsive">
                @if ($room->items->isNotEmpty())
                <table class="table m-0 p-0">
                    <thead>
                        <tr>
                            <th>Bagian yang Dibersihkan</th>
                            <th>Hasil</th>
                            <th>Di bersihkan Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($room->items as $item)
                        <tr>
                            <td>{{$item->nama_bagian}}</td>
                            <input type="hidden" name="bagian_kebersihan_id[]" value="{{$item->id}}">
                            <td>
                                <select name="hasil[]" class="border-1 border px-2 form-control">
                                    <option value="Bersih">Bersih</option>
                                    <option value="Kurang Bersih">Kurang Bersih</option>
                                    <option value="Kotor">Kotor</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="dibersihkan_oleh[]" placeholder="Tuliskan Nama"
                                    class="border px-2 border-1 form-control">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-muted">Belum ada barang.</p>
                @endif
            </div>
        </div>
        @endforeach

        <input type="hidden" name="produksi_id" value="{{$produksiSaatini->id}}">

        <div class="d-flex gap-2 justify-content-end">
            <button type="submit" class="btn btn-readonly-form btn-primary">Simpan</button>
        </div>
    </form>
    @endif
</div>