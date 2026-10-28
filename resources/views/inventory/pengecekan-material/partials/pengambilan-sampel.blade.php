<section class="form-section" data-title="Form Pengambilan Sampel Material">
    <form action="{{ url('/store-pengambilan-sampel') }}" method="post" class="editable-form"
        data-update-url="{{ url('/update-pengambilan-sampel/' . ($pengambilanSampel->id ?? '')) }}">
        @csrf
        @if($pengambilanSampel)
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
                    <input type="text" name="kategori_material" readonly value="{{$inventory->type}}"
                        class="form-control border border-1 py-2 px-2" disabled>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Nama Material</label>
                    <input type="text" readonly disabled value="{{$inventory->name}}"
                        class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Nomor Bets</label>
                    <input type="text" readonly disabled value="{{$master->nomor_batch ?? "none"}}"
                        class="form-control border border-1 py-2 px-2">
                </div>
            </div>
            <div class="row">
                @if($inventory->type != 'Bahan Baku')
                <div class="col-md-6 mb-3">
                    <label for="">Nama Supplier</label>
                    <select name="suplier_material_id" class="form-select border border-1 py-2 px-2" @if($pengambilanSampel || ($type == 'stock_in' && $master->suplier_material_id)) style="pointer-events: none; background-color: #e9ecef;" tabindex="-1" readonly @endif>
                        <option value="">Pilih Supplier</option>
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
                    <input type="text" disabled readonly value="{{$master->suplierMaterial->address ?? '-'}}" class="form-control border border-1 py- px-2">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Nama Produsen</label>
                    <select name="manufacturer_id" class="form-select border border-1 py-2 px-2"
                        @if($pengambilanSampel) disabled @endif required>
                        <option value="">Pilih Produsen</option>
                        @foreach($inventory->manufacturers->unique('id') as $mfg)
                            @php
                                $selected = false;
                                if ($pengambilanSampel && $pengambilanSampel->manufacturer_id == $mfg->id) {
                                    $selected = true;
                                } elseif (!$pengambilanSampel && $type == 'stock_in' && $master->produsen_id == $mfg->id) {
                                    $selected = true;
                                }
                            @endphp
                            <option value="{{$mfg->id}}" @if($selected) selected @endif>{{$mfg->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Negara Produsen</label>
                    <input type="text" id="negara_asal" name="negara_asal" @if($pengambilanSampel) value="{{$pengambilanSampel->negara_pemasok ?? ''}}" @endif class="form-control border border-1 py-2 px-2" readonly>
                </div>
                @else
                <div class="col-md-6 mb-3">
                    <label for="">Nama Supplier</label>
                    <select name="suplier_material_id" class="form-select border border-1 py-2 px-2" @if($pengambilanSampel || ($type == 'stock_in' && $master->suplier_material_id)) style="pointer-events: none; background-color: #e9ecef;" tabindex="-1" readonly @endif>
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
                    <input type="text" disabled readonly value="{{$master->suplierMaterial->address ?? '-'}}" class="form-control border border-1 py- px-2">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Nama Produsen</label>
                    <select name="manufacturer_id" id="manufacturer_select" class="form-select border border-1 py-2 px-2" @if($pengambilanSampel) disabled @endif required>
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
                    <input type="text" id="negara_asal" name="negara_asal" @if($pengambilanSampel) value="{{$pengambilanSampel->negara_pemasok ?? ''}}" @endif class="form-control border border-1 py-2 px-2" readonly>
                </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="">Tanggal Sampling</label>
                    <input type="date" name="tanggal_sampling" @if($pengambilanSampel)
                    value="{{$pengambilanSampel->tanggal_sampling->format('Y-m-d')}}" disabled @else
                        value="{{now()->format('Y-m-d')}}" @endif required
                        class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Expired Date</label>
                    <input type="date" name="expired_date" @if($pengambilanSampel)
                        value="{{$pengambilanSampel->expired_date ? $pengambilanSampel->expired_date->format('Y-m-d') : ''}}"
                        @if(isset($type) && $type != 'manual') readonly style="pointer-events: none; background-color: #e9ecef;" class="form-control border border-1 py-2 px-2" @else class="form-control border border-1 py-2 px-2" readonly style="pointer-events: none; background-color: #e9ecef;" @endif 
                    @else
                        value="{{$tanggal_ed ? \Carbon\Carbon::parse($tanggal_ed)->format('Y-m-d') : ''}}"
                        @if(isset($type) && $type != 'manual' && $tanggal_ed != null) readonly style="pointer-events: none; background-color: #e9ecef;" class="form-control border border-1 py-2 px-2" @else class="form-control border border-1 py-2 px-2" @endif 
                    @endif>
                </div>  
                <div class="col-md-4 mb-3">
                    <label class="form-label small">Jumlah Barang Datang</label>
                    <div class="input-group input-group-outline">
                        <input type="number" step="any" name="jumlah_barang_datang" 
                            @if($pengambilanSampel)
                                value="{{ rtrim(rtrim(number_format($pengambilanSampel->jumlah_barang_datang, 3, '.', ''), '0'), '.') }}"
                                class="form-control border border-1 py-2 px-2" readonly
                            @else
                                @if($type == 'manual')
                                    class="form-control border border-1 py-2 px-2" required
                                @else
                                    value="{{ rtrim(rtrim(number_format($stok, 3, '.', ''), '0'), '.') }}"
                                    readonly
                                    class="form-control border border-1 py-2 px-2"
                                @endif
                            @endif
                            placeholder="Contoh: 10 atau 10.5"
                        >
                        <span class="input-group-text bg-light text-muted p-2 border border-1" id="label-satuan">Unit</span>
                    </div>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="d-block mb-2">Dokumen Pendukung</label>
                    <div class="d-flex flex-wrap gap-4 align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="doc_coa" value="1" id="doc_coa_sampling" @if($pengambilanSampel) disabled @endif @if($pengambilanSampel && $pengambilanSampel->doc_coa) checked @endif>
                            <label class="form-check-label" for="doc_coa_sampling">CoA</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="doc_msds" value="1" id="doc_msds_sampling" @if($pengambilanSampel) disabled @endif @if($pengambilanSampel && $pengambilanSampel->doc_msds) checked @endif>
                            <label class="form-check-label" for="doc_msds_sampling">MSDS</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="doc_halal" value="1" id="doc_halal_sampling" @if($pengambilanSampel) disabled @endif @if($pengambilanSampel && $pengambilanSampel->doc_halal) checked @endif>
                            <label class="form-check-label" for="doc_halal_sampling">Sertifikat Halal</label>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="text-nowrap mb-0" for="doc_lainnya_sampling">Doc. Lainnya</label>
                            <input type="text" name="doc_lainnya" id="doc_lainnya_sampling" @if($pengambilanSampel) value="{{$pengambilanSampel->doc_lainnya}}" disabled @endif class="form-control form-control-sm border border-1 px-2 py-1" style="width: 250px;">
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="">Referensi Metode Sampling</label>
                    <select name="referensi_metode" class="form-select border border-1 py-2 px-2" @if($pengambilanSampel) disabled @endif>
                        <option value="">Pilih Referensi</option>
                        <option value="Metode ANSI" @if(isset($pengambilanSampel) && $pengambilanSampel->referensi_metode == 'Metode ANSI') selected @endif>Metode ANSI</option>
                        <option value="Spot Sampling" @if(isset($pengambilanSampel) && $pengambilanSampel->referensi_metode == 'Spot Sampling') selected @endif>Spot Sampling</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Jumlah Sampel Diambil</label>
                    <input type="text" name="jumlah_sampel" id="jumlah_sampel" @if($pengambilanSampel)
                    value="{{$pengambilanSampel->jumlah_sampel}}" disabled @endif required
                        placeholder="Misal: 50 gram" class="form-control border border-1 py-2 px-2">
                        <small class="text-danger">*Tuliskan satuan Material : (mL, gram, pcs)</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Peralatan Sampling</label>
                    <select name="peralatan_sampling" class="form-select border border-1 py-2 px-2" @if($pengambilanSampel) disabled @else required @endif>
                        <option value="">Pilih Peralatan</option>
                        <option value="Liquid thief sampler" @if(isset($pengambilanSampel) && $pengambilanSampel->peralatan_sampling == 'Liquid thief sampler') selected @endif>Liquid thief sampler</option>
                        <option value="Powder thief sampler" @if(isset($pengambilanSampel) && $pengambilanSampel->peralatan_sampling == 'Powder thief sampler') selected @endif>Powder thief sampler</option>
                        <option value="-" @if(isset($pengambilanSampel) && $pengambilanSampel->peralatan_sampling == '-') selected @endif>-</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Lokasi Sampling</label>
                    <input type="text" name="lokasi_sampling" @if($pengambilanSampel)
                    value="{{$pengambilanSampel->lokasi_sampling}}" disabled @endif required
                        class="form-control border border-1 py-2 px-2">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="">Disampling Oleh</label>
                    <input type="text" name="disampling_oleh" @if($pengambilanSampel)
                    value="{{$pengambilanSampel->disampling_oleh}}" disabled @else
                        value="{{Auth::user()->name}}" @endif class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Diperiksa Oleh</label>
                    <input type="text" name="diperiksa_oleh" @if($pengambilanSampel)
                    value="{{$pengambilanSampel->diperiksa_oleh}}" disabled @endif
                        class="form-control border border-1 py-2 px-2">
                </div>

                <div class="col-md-12 mb-3">
                    <label>Catatan</label>
                    <textarea name="catatan" @if($pengambilanSampel) disabled @endif
                        placeholder="Catatan (opsional)"
                        class="form-control border border-1 py-2 px-2">@if($pengambilanSampel){{$pengambilanSampel->catatan}}@endif</textarea>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-2">
                @if($pengambilanSampel)
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

<script>
    $(document).ready(function() {
        var selectedType = "{{ $inventory->type ?? '' }}";
        if (selectedType !== 'Bahan Baku') {
            $('#label-satuan').text('Pcs');
        } else {
            $('#label-satuan').text('Gram'); // default jika ada tipe lain
        }
    });
</script>
