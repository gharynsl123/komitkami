<section class="form-section" data-title="Inspeksi Penerimaan Bahan Baku">
    <form action="{{ url('/store-inspeksi-penerimaan') }}" method="post" class="editable-form"
        data-update-url="{{ url('/update-inspeksi-penerimaan/' . ($inspeksiPenerimaan->id ?? '')) }}">
        @csrf
        @if($inspeksiPenerimaan)
            @method('PUT')
        @endif
        <div class="card p-4">
            <input type="hidden" name="type" value="{{ $type ?? 'stock_in' }}">
            @if(isset($type) && $type == 'manual')
                <input type="hidden" name="pm_manual_report_id" value="{{$manual_report->id}}">
            @else
                <input type="hidden" name="stock_in_id" value="{{$master->id}}">
            @endif
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="">Kategori Material</label>
                    <input type="text" readonly value="{{$inventory->type}}"
                        class="form-control border border-1 py-2 px-2" disabled>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Nama Material</label>
                    <input type="text" disabled readonly value="{{$inventory->name}}"
                        class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">No. Batch</label>
                    <input type="text" disabled readonly value="{{$master->nomor_batch}}"
                        class="form-control border border-1 py-2 px-2">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="">Nama Supplier</label>
                    <select name="suplier_material_id" class="form-select border border-1 py-2 px-2"
                        @if($inspeksiPenerimaan || ($type == 'stock_in' && $master->suplier_material_id))
                        style="pointer-events: none; background-color: #e9ecef;" tabindex="-1" readonly @endif>
                        @foreach($supliers as $suplier)
                            @php
                                $selected = false;
                                if ($type == 'stock_in' && $master->suplier_material_id == $suplier->id) {
                                    $selected = true;
                                } elseif ($type == 'manual' && $master->suplier_material_id == $suplier->id) {
                                    $selected = true;
                                }
                            @endphp
                            <option value="{{$suplier->id}}" @if($selected) selected @endif>{{$suplier->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Negara Supplier</label>
                    <input type="text" disabled readonly value="{{$master->suplierMaterial->address ?? '-'}}"
                        class="form-control border border-1 py- px-2">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Nama Produsen</label>
                    <select name="manufacturer_id" id="manufacturer_select"
                        class="form-select border border-1 py-2 px-2" @if($inspeksiPenerimaan) disabled @endif required>
                        <option value="">Pilih Produsen</option>
                        @foreach($inventory->manufacturers->unique('id') as $mfg)
                            @php
                                $selected = false;
                                if ($inspeksiPenerimaan && $inspeksiPenerimaan->manufacturer_id == $mfg->id) {
                                    $selected = true;
                                } elseif (!$inspeksiPenerimaan && $type == 'stock_in' && $master->produsen_id == $mfg->id) {
                                    $selected = true;
                                }
                            @endphp
                            <option value="{{$mfg->id}}" @if($selected) selected @endif>{{$mfg->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Negara Produsen</label>
                    <input type="text" id="negara_asal" name="negara_asal" @if($inspeksiPenerimaan)
                    value="{{$inspeksiPenerimaan->negara_asal}}" @endif
                        class="form-control border border-1 py-2 px-2" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="">Bagian</label>
                    <input type="text" name="bagian" @if($inspeksiPenerimaan) value="{{$inspeksiPenerimaan->bagian}}"
                    disabled @else value="Quality Control" @endif class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Tanggal Inspeksi</label>
                    <input type="date" name="tanggal" @if($inspeksiPenerimaan)
                    value="{{$inspeksiPenerimaan->tanggal->format('Y-m-d')}}" disabled @else
                        value="{{now()->format('Y-m-d')}}" @endif class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Expired Date</label>
                    <input type="date" name="tanggal_ed" @if($inspeksiPenerimaan)
                        value="{{$inspeksiPenerimaan->tanggal_ed ? \Carbon\Carbon::parse($inspeksiPenerimaan->tanggal_ed)->format('Y-m-d') : ''}}"
                    @else value="{{$tanggal_ed ? \Carbon\Carbon::parse($tanggal_ed)->format('Y-m-d') : ''}}" @endif
                        class="form-control border border-1 py-2 px-2" @if(isset($type) && $type != 'manual' && $tanggal_ed != null) readonly style="pointer-events: none; background-color: #e9ecef;" @endif>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="d-block fw-bold mb-2">Dokumen Pendukung</label>
                    <div class="d-flex flex-wrap gap-4 align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="doc_coa" value="1"
                                id="doc_coa_inspeksi" @if($inspeksiPenerimaan) disabled @endif @if($inspeksiPenerimaan && $inspeksiPenerimaan->doc_coa) checked @endif>
                            <label class="form-check-label" for="doc_coa_inspeksi">CoA</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="doc_msds" value="1"
                                id="doc_msds_inspeksi" @if($inspeksiPenerimaan) disabled @endif @if($inspeksiPenerimaan && $inspeksiPenerimaan->doc_msds) checked @endif>
                            <label class="form-check-label" for="doc_msds_inspeksi">MSDS</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="doc_halal" value="1"
                                id="doc_halal_inspeksi" @if($inspeksiPenerimaan) disabled @endif @if($inspeksiPenerimaan && $inspeksiPenerimaan->doc_halal) checked @endif>
                            <label class="form-check-label" for="doc_halal_inspeksi">Sertifikat Halal</label>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="text-nowrap mb-0" for="doc_lainnya_inspeksi">Doc. Lainnya</label>
                            <input type="text" name="doc_lainnya" id="doc_lainnya_inspeksi" @if($inspeksiPenerimaan)
                            value="{{$inspeksiPenerimaan->doc_lainnya}}" disabled @endif
                                class="form-control form-control-sm border border-1 px-2 py-1" style="width: 250px;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive pm-table">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td class="text-center align-middle" rowspan="2" style="width:25%;">Pemeriksaan</td>
                            <td class="text-center align-middle" rowspan="2" style="width:25%;">Spesifikasi</td>
                            <td class="text-center" colspan="3">Hasil</td>
                        </tr>
                        <tr>
                            <td class="text-center">Atas</td>
                            <td class="text-center">Tengah</td>
                            <td class="text-center">Bawah</td>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $getSpec = function ($section, $param) use ($spesifikasi) {
                                $spec = collect($spesifikasi)->where('section_name', $section)->where('parameter', $param)->first();
                                return $spec ? $spec->spesifikasi : '-';
                            };
                        @endphp
                        <tr>
                            <td class="align-middle">Organoleptis: Bentuk</td>
                            <td><input type="text" disabled readonly value="{{ $getSpec('Organolepis', 'Bentuk') }}"
                                    class="form-control border-0 bg-transparent text-center" id="spec_bentuk"></td>
                            <td><input type="text" name="hasil_bentuk_atas" @if($inspeksiPenerimaan)
                            value="{{$inspeksiPenerimaan->hasil_bentuk_atas}}" disabled @endif
                                    class="form-control border-1 border px-2"></td>
                            <td><input type="text" name="hasil_bentuk_tengah" @if($inspeksiPenerimaan)
                            value="{{$inspeksiPenerimaan->hasil_bentuk_tengah}}" disabled @endif
                                    class="form-control border-1 border px-2"></td>
                            <td><input type="text" name="hasil_bentuk_bawah" @if($inspeksiPenerimaan)
                            value="{{$inspeksiPenerimaan->hasil_bentuk_bawah}}" disabled @endif
                                    class="form-control border-1 border px-2"></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Organoleptis: Warna</td>
                            <td><input type="text" disabled readonly value="{{ $getSpec('Organolepis', 'Warna') }}"
                                    class="form-control border-0 bg-transparent text-center" id="spec_warna"></td>
                            <td><input type="text" name="hasil_warna_atas" @if($inspeksiPenerimaan)
                            value="{{$inspeksiPenerimaan->hasil_warna_atas}}" disabled @endif
                                    class="form-control border-1 border px-2"></td>
                            <td><input type="text" name="hasil_warna_tengah" @if($inspeksiPenerimaan)
                            value="{{$inspeksiPenerimaan->hasil_warna_tengah}}" disabled @endif
                                    class="form-control border-1 border px-2"></td>
                            <td><input type="text" name="hasil_warna_bawah" @if($inspeksiPenerimaan)
                            value="{{$inspeksiPenerimaan->hasil_warna_bawah}}" disabled @endif
                                    class="form-control border-1 border px-2"></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Organoleptis: Rasa</td>
                            <td><input type="text" disabled readonly value="{{ $getSpec('Organolepis', 'Rasa') }}"
                                    class="form-control border-0 bg-transparent text-center" id="spec_rasa"></td>
                            <td><input type="text" name="hasil_rasa_atas" @if($inspeksiPenerimaan)
                            value="{{$inspeksiPenerimaan->hasil_rasa_atas}}" disabled @endif
                                    class="form-control border-1 border px-2"></td>
                            <td><input type="text" name="hasil_rasa_tengah" @if($inspeksiPenerimaan)
                            value="{{$inspeksiPenerimaan->hasil_rasa_tengah}}" disabled @endif
                                    class="form-control border-1 border px-2"></td>
                            <td><input type="text" name="hasil_rasa_bawah" @if($inspeksiPenerimaan)
                            value="{{$inspeksiPenerimaan->hasil_rasa_bawah}}" disabled @endif
                                    class="form-control border-1 border px-2"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row mt-3">
                <div class="col-md-6 mb-3">
                    <label for="">Kesimpulan</label>
                    <select name="kesimpulan" class="form-select border border-1 py-2 px-2" @if($inspeksiPenerimaan)
                    disabled @endif required>
                        <option value="">Pilih Kesimpulan</option>
                        <option value="Memenuhi Syarat" @if($inspeksiPenerimaan && $inspeksiPenerimaan->kesimpulan == "Memenuhi Syarat") selected @endif>Memenuhi Syarat
                        </option>
                        <option value="Tidak Memenuhi Syarat" @if($inspeksiPenerimaan && $inspeksiPenerimaan->kesimpulan == "Tidak Memenuhi Syarat") selected @endif>Tidak Memenuhi
                            Syarat</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Petugas QC</label>
                    <input type="text" name="petugas_qc" @if($inspeksiPenerimaan) disabled
                    value="{{$inspeksiPenerimaan->petugas_qc}}" @else value="{{Auth::user()->name}}" @endif
                        class="form-control border border-1 py-2 px-2">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-2">
                @if($inspeksiPenerimaan)
                    <button type="button" class="btn btn-sm btn-warning btn-edit">Ubah Data</button>
                    <button type="button" class="btn btn-sm btn-danger btn-cencel d-none">Batalkan</button>
                    <button type="submit" class="btn btn-sm btn-warning btn-save d-none">Simpan Perubahan</button>
                @else
                    <button type="submit" class="btn btn-success px-4">Submit</button>
                @endif
            </div>
        </div>
    </form>
</section>