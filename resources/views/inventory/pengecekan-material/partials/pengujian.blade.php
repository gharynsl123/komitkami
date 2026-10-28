<section class="form-section" data-title="Form Pengujian {{$inventory->type}}">
    <form action="{{ url('/store-pengujian') }}" method="post" class="editable-form"
        data-update-url="{{ url('/update-pengujian/' . ($pengujian->id ?? '')) }}">
        @csrf
        @if($pengujian)
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
                    <label for="">Nama Bahan</label>
                    <input type="text" name="nama_bahan" readonly disabled value="{{ $inventory->name }}" class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Nomor Bets</label>
                    <input type="text" name="nomor_bets" readonly disabled value="{{ $master->nomor_batch }}" class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="">Tanggal Uji</label>
                    <input type="date" name="tanggal" @if($pengujian)
                    value="{{$pengujian->tanggal->format('Y-m-d')}}" disabled @else
                        value="{{now()->format('Y-m-d')}}" @endif class="form-control border border-1 py-2 px-2">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="">Nama Supplier</label>
                    <input type="text" name="nama_supplier" readonly disabled value="{{ $master->suplierMaterial->name ?? '-' }}" class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Negara Supplier</label>
                    <input type="text" name="negara_supplier" readonly disabled value="{{ $master->suplierMaterial->address ?? '-' }}" class="form-control border border-1 py-2 px-2">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Nama Produsen</label>
                    @php 
                        $mfg_id = $master->produsen_id ?? ($manufacturer_id ?? ($inspeksiPenerimaan->manufacturer_id ?? ($pengambilanSampel->manufacturer_id ?? null))); 
                        $mfgObj = $inventory->manufacturers->firstWhere('id', $mfg_id);
                        $negara_asal_val = $inspeksiPenerimaan->negara_asal ?? ($mfgObj->address ?? '');
                    @endphp
                    <select name="manufacturer_id" id="manufacturer_select" class="form-select border border-1 py-2 px-2" @if($pengujian) disabled @endif required>
                        <option value="">Pilih Produsen</option>
                        @foreach($inventory->manufacturers->unique('id') as $mfg)
                            <option value="{{$mfg->id}}" @if($mfg_id == $mfg->id) selected @endif>{{$mfg->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Negara Produsen</label>
                    <input type="text" id="negara_asal" name="negara_asal" value="{{ $negara_asal_val }}" readonly class="form-control border border-1 py-2 px-2">
                </div>
            </div>
            <table class="table table-bordered pm-table" id="table-pengujian">
                <thead>
                    <tr>
                        <td class="text-center align-middle" style="width:5%;">No</td>
                        <td class="text-center align-middle" style="width:30%;">Parameter Uji</td>
                        <td class="text-center align-middle" style="width:35%;">Spesifikasi</td>
                        <td class="text-center" style="width:30%;">Hasil</td>
                    </tr>
                </thead>
                <tbody data-filled="{{ ($pengujian && count($pengujian->details) > 0) ? 'true' : 'false' }}">
                    @if($pengujian && count($pengujian->details) > 0)
                        @foreach($pengujian->details as $idx => $detail)
                            <tr>
                                <td class="text-center align-middle">{{$idx + 1}}</td>
                                <td>
                                    <input type="text" name="parameter_uji[]" value="{{ trim(str_replace([': Spesifikasi', 'Bahan Kemas:'], '', $detail->parameter_uji)) }}" readonly
                                        class="form-control border-0 px-2 bg-transparent">
                                </td>
                                <td>
                                    <input type="text" name="spesifikasi[]" value="{{$detail->spesifikasi}}" readonly
                                        class="form-control border-0 px-2 bg-transparent">
                                </td>
                                <td>
                                    <input type="text" name="hasil[]" value="{{$detail->hasil}}" disabled
                                        class="form-control border-1 border px-2">
                                </td>
                            </tr>
                        @endforeach
                    @else
                        @forelse($spesifikasi as $idx => $spec)
                            @if ($spec->parameter == "Suhu" || $spec->parameter == "Spindle" || $spec->parameter == "Speed" || $spec->parameter == "Time")
                                @continue
                            @endif
                            <tr>
                                <td class="text-center align-middle">{{$idx + 1}}</td>
                                <td>
                                    <input type="text" name="parameter_uji[]" value="{{ $spec->section_name == 'Bahan Kemas' ? $spec->parameter : ($spec->parameter == 'Spesifikasi' ? $spec->section_name : $spec->section_name . ': ' . $spec->parameter) }}" readonly
                                        class="form-control border-0 px-2 bg-transparent">
                                </td>
                                <td>
                                    <input type="text" name="spesifikasi[]" value="{{$spec->spesifikasi}}" readonly
                                        class="form-control border-0 px-2 bg-transparent">
                                </td>
                                <td>
                                    <input type="text" name="hasil[]" placeholder="Hasil pengujian..."
                                        class="form-control border-1 border px-2">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada data spesifikasi untuk
                                    material ini. Harap input spesifikasi material pada menu Inventory.</td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>

            <div class="row mt-3">
                <div class="col-md-6 mb-3">
                    <label for="">Kesimpulan</label>
                    <select name="kesimpulan" class="form-select border border-1 py-2 px-2" @if($pengujian)
                    disabled @endif required>
                        <option value="">Pilih Kesimpulan</option>
                        <option value="Memenuhi Syarat" @if($pengujian && $pengujian->kesimpulan == "Memenuhi Syarat") selected @endif>Memenuhi Syarat
                        </option>
                        <option value="Tidak Memenuhi Syarat" @if($pengujian && $pengujian->kesimpulan == "Tidak Memenuhi Syarat") selected @endif>Tidak Memenuhi
                            Syarat</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="">Petugas QC</label>
                    <input type="text" name="petugas_qc" @if($pengujian) disabled value="{{$pengujian->petugas_qc}}"
                    @else value="{{Auth::user()->name}}" @endif class="form-control border border-1 py-2 px-2">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-2">
                @if($pengujian)
                    <button type="button" class="btn btn-sm btn-warning btn-edit">Ubah Data</button>
                    <button type="button" class="btn btn-sm btn-danger btn-cencel d-none">Batalkan</button>
                    <button type="submit" class="btn btn-sm btn-warning btn-save d-none">Simpan Perubahan</button>
                @else
                    <button type="submit" class="btn btn-success px-4" id="btn-submit-pengujian" @if(count($spesifikasi) == 0) style="display: none;" @endif>Submit</button>
                @endif
            </div>
        </div>
    </form>
</section>
