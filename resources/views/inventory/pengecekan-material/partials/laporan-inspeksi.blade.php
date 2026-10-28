<section class="form-section"
    data-title="Laporan Inspeksi @if($inventory->type == 'Bahan Baku') Raw Material @else Material Packaging @endif">
    <form action="{{ $laporanInspeksi ? url('/update-laporan-inspeksi/' . $laporanInspeksi->id) : url('/store-laporan-inspeksi') }}" method="post" class="editable-form"
        data-update-url="{{ url('/update-laporan-inspeksi/' . ($laporanInspeksi->id ?? '')) }}">
        @csrf
        @if($laporanInspeksi)
            @method('PUT')
        @endif
        <div class="card p-3">
            <input type="hidden" name="type" value="{{ $type ?? 'stock_in' }}">
            @if(isset($type) && $type == 'manual')
                <input type="hidden" name="pm_manual_report_id" value="{{$manual_report->id}}">
            @else
                <input type="hidden" name="stock_in_id" value="{{$master->id}}">
            @endif
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="">Nama Material</label>
                    <input type="text" disabled readonly value="{{$inventory->name}}"
                        class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Kode Material</label>
                    <input type="text" disabled readonly value="{{$inventory->code}}"
                        class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Nomor Bets</label>
                    <input type="text" disabled readonly value="{{$master->nomor_batch}}"
                        class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Kategori Material</label>
                    <input type="text" disabled readonly value="{{$inventory->type}}"
                        class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Nama Supplier</label>
                    <input type="text" disabled readonly value="{{$master->suplierMaterial->name ?? '-'}}"
                        class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Negara Supplier</label>
                    <input type="text" disabled readonly value="{{$master->suplierMaterial->address ?? '-'}}"
                        class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Nama Produsen</label>
                    @php $mfg_id = $master->produsen_id ?? ($manufacturer_id ?? ($inspeksiPenerimaan->manufacturer_id ?? ($pengambilanSampel->manufacturer_id ?? null))); @endphp
                    <select name="manufacturer_id" id="manufacturer_select"
                        class="form-select border border-1 py-2 px-2" @if($laporanInspeksi) disabled @endif required>
                        <option value="">Pilih Produsen</option>
                        @foreach($inventory->manufacturers->unique('id') as $mfg)
                            <option value="{{$mfg->id}}" @if($mfg_id == $mfg->id) selected @endif>{{$mfg->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Negara Produsen</label>
                    <input type="text" id="negara_asal" name="negara_asal" @if($inspeksiPenerimaan)
                    value="{{$inspeksiPenerimaan->negara_asal}}" @endif readonly
                        class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Tanggal Inspeksi</label>
                    <input type="date" name="tanggal_inspeksi" @if($laporanInspeksi)
                    value="{{$laporanInspeksi->tanggal_inspeksi->format('Y-m-d')}}" disabled @else
                        value="{{now()->format('Y-m-d')}}" @endif class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Tanggal Efektif</label>
                    <input type="date" name="tanggal_efektif" @if($laporanInspeksi && $laporanInspeksi->tanggal_efektif)
                    value="{{$laporanInspeksi->tanggal_efektif->format('Y-m-d')}}" disabled @endif
                        class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small">Jumlah Material</label>
                    @php
                        $defaultJumlahMaterial = '';
                        if ($laporanInspeksi && $laporanInspeksi->jumlah_material) {
                            $defaultJumlahMaterial = $laporanInspeksi->jumlah_material;
                        } elseif (isset($pengambilanSampel) && $pengambilanSampel->jumlah_barang_datang) {
                            $defaultJumlahMaterial = rtrim(rtrim(number_format($pengambilanSampel->jumlah_barang_datang, 3, '.', ''), '0'), '.');
                        } elseif (isset($stok) && $stok > 0) {
                            $defaultJumlahMaterial = rtrim(rtrim(number_format($stok, 3, '.', ''), '0'), '.');
                        }
                    @endphp
                    <div class="input-group input-group-outline">
                        <input type="number" step="any" id="jumlah_material" name="jumlah_material" 
                            value="{{ $defaultJumlahMaterial }}"
                            class="form-control border border-1 py-2 px-2" readonly placeholder="Contoh: 10 atau 10.5">
                        <span class="input-group-text bg-light text-muted p-2 border border-1" id="label-satuan-material">Unit</span>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Jumlah Sampel</label>
                    <input type="text" id="jumlah_sampel_laporan" name="jumlah_sampel" @if($laporanInspeksi)
                    value="{{$laporanInspeksi->jumlah_sampel}}" disabled @endif
                        class="form-control border border-1 py-2 px-2">
                    <small class="text-danger">*Tuliskan satuan Material : (mL, gram, pcs)</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="">Disiapkan Oleh</label>
                    <input type="text" name="disiapkan_oleh" @if($laporanInspeksi)
                    value="{{$laporanInspeksi->disiapkan_oleh}}" disabled @endif
                        class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Disetujui Oleh</label>
                    <input type="text" name="disetujui_oleh" @if($laporanInspeksi)
                    value="{{$laporanInspeksi->disetujui_oleh}}" disabled @endif
                        class="form-control border border-1 py-2 px-2">
                </div>
            </div>

            <div class="table-responsive pm-table">
                @php
                    $numSamples = 1; // default
                    if ($laporanInspeksi && $laporanInspeksi->jumlah_sampel) {
                        preg_match('/\d+/', $laporanInspeksi->jumlah_sampel, $matches);
                        if (!empty($matches[0]) && intval($matches[0]) > 0) {
                            $numSamples = intval($matches[0]);
                        }
                    }
                    if ($laporanInspeksi && count($laporanInspeksi->details) > 0) {
                        foreach($laporanInspeksi->details as $d) {
                            if (is_array($d->hasil_json) && count($d->hasil_json) > $numSamples) {
                                $numSamples = count($d->hasil_json);
                            }
                        }
                    }
                @endphp

                <table class="table table-bordered" id="table-laporan-inspeksi">
                    <thead>
                        <tr>
                            <td class="text-center align-middle" rowspan="2" style="width:5%;">No</td>
                            <td class="text-center align-middle" rowspan="2" style="width:25%;">Parameter</td>
                            <td class="text-center align-middle" rowspan="2" style="width:25%;">Persyaratan</td>
                            @if($inventory->type != 'Bahan Baku')
                                <td class="text-center" colspan="{{ $numSamples }}" id="col-hasil-pengujian">Hasil Pengujian</td>
                            @else
                                <td class="text-center" colspan="3">Hasil Pengujian</td>
                            @endif
                            <td class="text-center align-middle" rowspan="2" style="width:15%;">Hasil Inspeksi</td>
                        </tr>
                        <tr id="row-hasil-pengujian">
                            @if($inventory->type != 'Bahan Baku')
                                @for($i=1; $i<=$numSamples; $i++)
                                    <td class="text-center" style="min-width: 60px;">{{$i}}</td>
                                @endfor
                            @else
                                <td class="text-center">Atas</td>
                                <td class="text-center">Tengah</td>
                                <td class="text-center">Bawah</td>
                            @endif
                        </tr>
                    </thead>
                    <tbody data-filled="{{ ($laporanInspeksi && count($laporanInspeksi->details) > 0) ? 'true' : 'false' }}">
                        @if($laporanInspeksi && count($laporanInspeksi->details) > 0)
                            @php $rowIdx = 0; @endphp
                            @foreach($laporanInspeksi->details as $detail)
                                @php
                                    $paramName = trim(str_replace(': Spesifikasi', '', $detail->parameter));
                                    $persyaratan = $detail->persyaratan ?? '';
                                    $isTextInput = (bool)preg_match('/(tinggi|lebar|panjang|tebal|ketebalan|diameter|berat|volume|dimensi|size|ukuran)/i', $paramName) || (bool)preg_match('/\d/', $persyaratan);
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        {{ $rowIdx + 1 }}
                                        <input type="hidden" name="detail_id[{{ $rowIdx }}]" value="{{ $detail->id }}">
                                    </td>
                                    <td>
                                        <input type="text" name="parameter[{{ $rowIdx }}]" value="{{ $paramName }}" readonly
                                            class="form-control border-0 px-2 bg-transparent">
                                    </td>
                                    <td>
                                        <input type="text" name="persyaratan[{{ $rowIdx }}]" value="{{ $persyaratan }}" readonly
                                            class="form-control border-0 px-2 bg-transparent">
                                    </td>
                                    @if($inventory->type != 'Bahan Baku')
                                        @for($i=0; $i<$numSamples; $i++)
                                            @php
                                                $val = is_array($detail->hasil_json) ? ($detail->hasil_json[$i] ?? '') : '';
                                            @endphp
                                            @if($isTextInput)
                                                <td class="text-center">
                                                    <input type="text" name="hasil_kemas[{{ $rowIdx }}][]" value="{{ $val }}" disabled
                                                        class="form-control border-1 border px-2">
                                                </td>
                                            @else
                                                <td class="text-center">
                                                    <select name="hasil_kemas[{{ $rowIdx }}][]" disabled
                                                        class="form-select border border-1 px-2">
                                                        <option value="">Pilih</option>
                                                        <option value="Ok" {{ strcasecmp($val, 'Ok') === 0 ? 'selected' : '' }}>Ok</option>
                                                        <option value="Tidak" {{ strcasecmp($val, 'Tidak') === 0 ? 'selected' : '' }}>Tidak</option>
                                                        @if(!empty($val) && strcasecmp($val, 'Ok') !== 0 && strcasecmp($val, 'Tidak') !== 0)
                                                            <option value="{{ $val }}" selected>{{ $val }}</option>
                                                        @endif
                                                    </select>
                                                </td>
                                            @endif
                                        @endfor
                                    @else
                                        <td>
                                            <input type="text" name="hasil_atas[{{ $rowIdx }}]" value="{{ $detail->hasil_atas }}" disabled
                                                class="form-control border-1 border px-2">
                                        </td>
                                        <td>
                                            <input type="text" name="hasil_tengah[{{ $rowIdx }}]" value="{{ $detail->hasil_tengah }}" disabled
                                                class="form-control border-1 border px-2">
                                        </td>
                                        <td>
                                            <input type="text" name="hasil_bawah[{{ $rowIdx }}]" value="{{ $detail->hasil_bawah }}" disabled
                                                class="form-control border-1 border px-2">
                                        </td>
                                    @endif
                                    <td>
                                        <select name="hasil_inspeksi[{{ $rowIdx }}]" class="form-select border border-1 px-2" disabled>
                                            <option value="">Pilih</option>
                                            <option value="Pass" @if($detail->hasil_inspeksi == 'Pass') selected @endif>Pass
                                            </option>
                                            <option value="Fail" @if($detail->hasil_inspeksi == 'Fail') selected @endif>Fail
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                @php $rowIdx++; @endphp
                            @endforeach
                        @else
                            @php $rowIdx = 0; @endphp
                            @forelse($spesifikasi as $spec)
                                @if ($spec->parameter == "Suhu" || $spec->parameter == "Spindle" || $spec->parameter == "Speed" || $spec->parameter == "Time")
                                    @continue
                                @endif
                                @php
                                    $paramName = $spec->section_name == 'Bahan Kemas' ? $spec->parameter : ($spec->parameter == 'Spesifikasi' ? $spec->section_name : $spec->section_name . ': ' . $spec->parameter);
                                    $persyaratan = $spec->spesifikasi ?? '';
                                    $isTextInput = (bool)preg_match('/(tinggi|lebar|panjang|tebal|ketebalan|diameter|berat|volume|dimensi|size|ukuran)/i', $paramName) || (bool)preg_match('/\d/', $persyaratan);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $rowIdx + 1 }}</td>
                                    <td>
                                        <input type="text" name="parameter[{{ $rowIdx }}]"
                                            value="{{ $paramName }}" readonly
                                            class="form-control border-0 px-2 bg-transparent">
                                    </td>
                                    <td>
                                        <input type="text" name="persyaratan[{{ $rowIdx }}]" value="{{ $persyaratan }}" readonly
                                            class="form-control border-0 px-2 bg-transparent">
                                    </td>
                                    @if($inventory->type != 'Bahan Baku')
                                        @for($i=0; $i<$numSamples; $i++)
                                            @if($isTextInput)
                                            <td>
                                                <input type="text" name="hasil_kemas[{{ $rowIdx }}][]" class="form-control border-1 border px-2">
                                            </td>
                                            @else
                                            <td>
                                                <select name="hasil_kemas[{{ $rowIdx }}][]" class="form-select border border-1 px-2">
                                                    <option value="">Pilih</option>
                                                    <option value="Ok">Ok</option>
                                                    <option value="Tidak">Tidak</option>
                                                </select>
                                            </td>
                                            @endif
                                        @endfor
                                    @else
                                        <td><input type="text" name="hasil_atas[{{ $rowIdx }}]" class="form-control border-1 border px-2"></td>
                                        <td><input type="text" name="hasil_tengah[{{ $rowIdx }}]" class="form-control border-1 border px-2"></td>
                                        <td><input type="text" name="hasil_bawah[{{ $rowIdx }}]" class="form-control border-1 border px-2"></td>
                                    @endif
                                    <td>
                                        <select name="hasil_inspeksi[{{ $rowIdx }}]" class="form-select border border-1 px-2">
                                            <option value="">Pilih</option>
                                            <option value="Pass">Pass</option>
                                            <option value="Fail">Fail</option>
                                        </select>
                                    </td>
                                </tr>
                                @php $rowIdx++; @endphp
                            @empty
                                <tr>
                                    <td colspan="{{ $inventory->type != 'Bahan Baku' ? ($numSamples + 4) : 7 }}" id="col-empty-state" class="text-center text-muted py-3">Belum ada data spesifikasi untuk
                                        material ini. Harap input spesifikasi material pada menu Inventory.</td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="row mt-3">
                <div class="col-md-12 mb-3">
                    <label for="">Kesimpulan Status</label>
                    <select name="kesimpulan" class="form-select border border-1 py-2 px-2" @if($laporanInspeksi)
                    disabled @endif required>
                        <option value="">Pilih Status</option>
                        <option value="Release" @if($laporanInspeksi && $laporanInspeksi->kesimpulan == "Release")
                        selected @endif>Release</option>
                        <option value="Hold" @if($laporanInspeksi && $laporanInspeksi->kesimpulan == "Hold") selected
                        @endif>Hold</option>
                        <option value="Reject" @if($laporanInspeksi && $laporanInspeksi->kesimpulan == "Reject") selected
                        @endif>Reject</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                @if($laporanInspeksi)
                    <button type="button" class="btn btn-sm btn-warning btn-edit">Ubah Data</button>
                    <button type="button" class="btn btn-sm btn-danger btn-cencel d-none">Batalkan</button>
                    <button type="submit" class="btn btn-sm btn-warning btn-save d-none">Simpan Perubahan</button>
                @else
                    @if(count($spesifikasi) > 0)
                        <button type="submit" class="btn btn-success">Submit</button>
                    @endif
                @endif
            </div>
        </div>
    </form>
</section>

<script>
    $(document).ready(function() {
        var selectedType = "{{ $inventory->type ?? '' }}";
        if (selectedType !== 'Bahan Baku') {
            $('#label-satuan-material').text('Pcs');
        } else {
            $('#label-satuan-material').text('Gram'); // default jika ada tipe lain
        }
    });
</script>