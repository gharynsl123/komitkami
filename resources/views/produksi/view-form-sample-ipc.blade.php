@extends('layouts.app')
@section('title', 'Komitkami | Proses From IPC')
@section('content')
<style>
    .ipc-table {
        overflow-x: auto;
        white-space: nowrap;
    }

    .ipc-table input {
        min-width: 35px;
    }
</style>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 id="form-title"></h5>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-primary" id="prevBtn">
                < 
            </button>
            <button type="button" class="btn btn-sm btn-primary" id="nextBtn"> 
                > 
            </button>
        </div>
    </div>

    <!-- Strat Form Pengambilan Sample -->
    <section class="form-section" data-title="Form Pengambilan Sample In Process Control">
        <form action="{{url('/store-sample-ipc')}}" method="post" class="editable-form" data-update-url="{{ url('/update-sample-ipc/' . ($sampleIPC->id ?? '')) }}">
            @csrf
            @if($sampleIPC)
                @method('PUT')
            @endif
            <div class="card p-3">
                <input type="hidden" name="produksi_id" value="{{$produksi->id}}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="">Nama Produk</label>
                        <input type="text" disabled readonly value="{{$produksi->product->name}}"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Kode Produk</label>
                        <input type="text" disabled readonly value="{{$produksi->product->code}}"
                            placeholder="Isi Tahapan Proses" class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Jenis Produk</label>
                        <input type="text" disabled readonly value="{{$produksi->product->jenis ?? '-'}}"
                            placeholder="Isi Tahapan Proses" class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Nomor Bets</label>
                        <input type="text" disabled readonly value="{{$produksi->batch_number}}" placeholder="Isi Tahapan Proses"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Tanggal Sampling</label>
                        <input type="date" name="tanggal_sampling" @if($sampleIPC)
                            value="{{$sampleIPC->tanggal_sampling ?? now()->format('Y-m-d')}}" disabled @endif required
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Tahapan Proses</label>
                        <input type="text" name="tahapan_proses" @if($sampleIPC) value="{{$sampleIPC->tahapan_proses}}"
                            disabled @endif required placeholder="Isi Tahapan Proses"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Referensi Metode Sampling</label>
                        <input type="text" name="referensi_metode_sampling" @if($sampleIPC)
                            value="{{$sampleIPC->referensi_metode_sampling}}" disabled @endif required
                            placeholder="isi disini" class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="" text>Jumlah Sample Diambil Bagian Atas</label>
                        <div class="input-group">
                            <input type="text" name="jumlah_sample_diambil_bagian_atas" @if($sampleIPC)
                                value="{{$sampleIPC->jumlah_sample_diambil_bagian_atas}}" disabled @endif required
                                placeholder="isi disini" class="form-control border border-1 py-2 px-2">
                                <span class="input-group-text border border-1 py-2 px-4">ML</span>
                        </div>
                        <small class="text-danger">*cukup tulisakan angka saja</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="" text>Jumlah Sample Diambil Bagian Bawah</label>
                        <div class="input-group">
                            <input type="text" name="jumlah_sample_diambil_bagian_bawah" @if($sampleIPC)
                                value="{{$sampleIPC->jumlah_sample_diambil_bagian_bawah}}" disabled @endif required
                                placeholder="isi disini" class="form-control border border-1 py-2 px-2">
                                <span class="input-group-text border border-1 py-2 px-4">ML</span>
                        </div>
                        <small class="text-danger">*cukup tulisakan angka saja</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Peralatan Sampling</label>
                        <input type="text" name="peralatan_sampling" @if($sampleIPC)
                            value="{{$sampleIPC->peralatan_sampling}}" disabled @endif required placeholder="isi disini"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Lokasi Sampling</label>
                        <input type="text" name="lokasi_sampling" @if($sampleIPC)
                            value="{{$sampleIPC->lokasi_sampling}}" disabled @endif required placeholder="isi disini"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Catatan</label>
                        <textarea name="keterangan" required @if($sampleIPC) disabled @endif
                            placeholder="Catatan(opsional)"
                            class="form-control border border-1 py-2 px-2">@if($sampleIPC) {{$sampleIPC->keterangan}} @endif</textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                     @if($sampleIPC)
                        <button type="button" class="btn btn-sm btn-warning btn-edit">Ubah Data</button>
                        <button type="button" class="btn btn-sm btn-danger btn-cencel d-none">Batalkan</button>
                        <button type="submit" class="btn btn-sm btn-warning btn-save d-none">Simpan Perubahan</button>
                    @else
                        <button type="submit" class="btn btn-success">Submit</button>
                    @endif
                </div>
            </div>
        </form>
    </section>
    <!-- End Form Pengambilan Sample -->


    <!-- start Form Inspeksi Proses Produksi -->
    <section class="form-section" data-title="Inspeksi Proses Produksi">
        <h5 class="text-center"></h5>
        <form action="{{url('/store-inspeksi-proses')}}" method="post" class="editable-form" data-update-url="{{ url('/update-inspeksi-proses/' . ($inspeksiData->id ?? '')) }}">
            @csrf
            @if($inspeksiData)
                @method('PUT')
            @endif
            <div class="card p-3">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="">Nama Produk</label>
                        <input type="text" disabled readonly value="{{$produksi->product->name}}"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Kode Produk</label>
                        <input type="text" disabled readonly value="{{$produksi->product->code}}"
                            placeholder="Isi Tahapan Proses" class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Jenis Produk</label>
                        <input type="text" disabled readonly value="{{$produksi->product->jenis ?? '-'}}"
                            placeholder="Isi Tahapan Proses" class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Nomor Bets</label>
                        <input type="text" disabled readonly value="{{$produksi->batch_number}}" placeholder="Isi Tahapan Proses"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Besar Bets Produksi</label>
                        <input type="text" disabled readonly value="{{$produksi->batch_size}} KG"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Tanggal Produksi</label>
                        <input type="text" disabled readonly value="{{$produksi->tanggal_produksi->format('d-m-Y')}}"
                            placeholder="Isi Tahapan Proses" class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Tanggal Pengujian</label>
                        <input type="date" name="tanggal"
                            value="{{ $inspeksiData->tanggal ?? now()->format('Y-m-d') }}"
                            @if($inspeksiData) disabled @endif
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Jumlah Sampel Uji</label>
                        <div class="input-group">
                            <input type="text"
                                name="jumlah_sampel_uji"
                                value="{{ $inspeksiData->jumlah_sampel_uji ?? '' }}"
                                @if($inspeksiData) disabled @endif
                                class="form-control border border-1 py-2 px-2">
                                <span class="input-group-text border border-1 py-2 px-4">ML</span>
                        </div>
                        <small class="text-danger">*cukup tulisakan angka saja</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Kesimpulan</label>
                        <select name="kesimpulan" class="form-select border border-1 py-2 px-2" @if($inspeksiData)
                            disabled @endif required>
                            <option value="">Pilih Kesimpulan</option>
                            <option value="OK" @if(($inspeksiData) && $inspeksiData->kesimpulan === "OK") selected
                                @endif>OK</option>
                            <option value="NOT OK" @if(($inspeksiData) && $inspeksiData->kesimpulan === "NOT OK")
                                selected @endif>NOT OK</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="">Petugas QC</label>
                        <input type="text" name="petugas_qc" @if($inspeksiData)
                            disabled @endif readonly value="{{$inspeksiData->petugas_qc ?? Auth::user()->name}}"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <input type="text" name="produksi_id" value="{{$produksi->id}}" hidden>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td class="text-center align-middle" rowspan="2" style="width:25%;">Pemeriksaan</td>
                            <td class="text-center align-middle" rowspan="2" style="width:25%;">Spesifikasi</td>
                            <td class="text-center" colspan="2">Hasil</td>
                        </tr>
                        <tr>
                            <td class="text-center">Bagian Atas</td>
                            <td class="text-center">Bagian Bawah</td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($acuanPengujian as $items)
                        @if($items->section_name === "Inspeksi Proses Produksi")
                        <tr>
                            <td>{{ $items->pemeriksaan }}</td>
                            <td>{{ $items->spesifikasi }}</td>
                            <td>
                                <input type="text"
                                    name="inspeksi_bagian_atas[]"
                                    @if($inspeksiData)
                                    disabled
                                    value="{{ optional($inspeksi->where('acuan_laporan_ipc_id', $items->id)->first())->inspeksi_bagian_atas }}"
                                    @endif
                                    class="form-control border-1 border px-2" placeholder="isi disini">
                            </td>

                            <td>
                                <input type="text"
                                    name="inspeksi_bagian_bawah[]"
                                    @if($inspeksiData)
                                    disabled
                                    value="{{ optional($inspeksi->where('acuan_laporan_ipc_id', $items->id)->first())->inspeksi_bagian_bawah }}"
                                    @endif
                                    class="form-control border-1 border px-2" placeholder="isi disini">
                            </td>

                            {{-- SATU KALI SAJA --}}
                            <input type="hidden" name="acuan_laporan_ipc_id[]" value="{{ $items->id }}">
                        </tr>
                        @endif
                        @empty
                        <tr style="border: 1px solid #00000021 !important;">
                            <td colspan="3" class="text-center">
                                <p>Data Masih Kosong, Segera Konfirmasi Kepada QC untuk mengerjakan Quality Control.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end gap-2">
                    @if($inspeksiData)
                        <button type="button" class="btn btn-sm btn-warning btn-edit">Ubah Data</button>
                        <button type="button" class="btn btn-sm btn-danger btn-cencel d-none">Batalkan</button>
                        <button type="submit" class="btn btn-sm btn-warning btn-save d-none">Simpan Perubahan</button>
                    @else
                        <button type="submit" class="btn btn-success">Submit</button>
                    @endif
                </div>
            </div>
        </form>
    </section>
    <!-- end Form Inspeksi Proses Produksi -->

    <!-- start Form Report In Process Control -->
    <section class="form-section" data-title="Report In Process Control">
        <h5 class="text-center"></h5>
        <form action="{{url('/store-report-ipc')}}" method="post" class="editable-form" data-update-url="{{ url('/update-report-ipc/' . ($reportData->id ?? '')) }}">
            @csrf
            @if($reportData)
                @method('PUT')
            @endif
            <div class="card p-3">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="">Nama Produk</label>
                        <input type="text" disabled readonly value="{{$produksi->product->name}}"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Kode Produk</label>
                        <input type="text" disabled readonly value="{{$produksi->product->code}}"
                            placeholder="Isi Tahapan Proses" class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Jenis Produk</label>
                        <input type="text" disabled readonly value="{{$produksi->product->jenis ?? '-'}}"
                            placeholder="Isi Tahapan Proses" class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Nomor Bets</label>
                        <input type="text" disabled readonly value="{{$produksi->batch_number}}" placeholder="Isi Tahapan Proses"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Besar Bets Produksi</label>
                        <input type="text" disabled readonly value="{{$produksi->batch_size}} KG"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Tanggal Produksi</label>
                        <input type="text" disabled readonly value="{{$produksi->tanggal_produksi->format('d-m-Y')}}"
                            placeholder="Isi Tahapan Proses" class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Tanggal Pengujian</label>
                        <input type="date" name="tanggal_pengujian"
                            value="{{ $reportData->tanggal_pengujian ?? now()->format('Y-m-d') }}"
                            @if($reportData) disabled @endif
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="">Jumlah Sampel Uji</label>
                        <input type="text"
                            name="jumlah_sampel_uji"
                            value="{{ $reportData->jumlah_sampel_uji ?? '' }}"
                            @if($reportData) disabled @endif
                            class="form-control border border-1 py-2 px-2">
                        <small class="text-danger">*tuliskan mL/Jarigen/Botol/KG</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Disampling Oleh</label>
                        <input type="text" name="disampling_oleh" required value="{{$reportData->disampling_oleh ?? Auth::user()->name}}"
                            @if($reportData) disabled @endif placeholder="isi disini"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Kesimpulan</label>
                        <select name="kesimpulan" class="form-select border border-1 py-2 px-2" required
                            @if($reportData) disabled @endif>
                            <option value="" selected disabled>Pilih Kesimpulan</option>
                            <option value="OK" @if(($reportData) && $reportData->kesimpulan === "OK") selected @endif>OK
                            </option>
                            <option value="NOT OK" @if(($reportData) && $reportData->kesimpulan === "NOT OK") selected
                                @endif>NOT OK</option>
                            <option value="Lainnya" @if(($reportData) && $reportData->kesimpulan === "Lainnya") selected
                                @endif>Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="">Catatan</label>
                        <textarea type="text" name="catatan" @if($reportData) disabled @endif
                            class="form-control border border-1 py-2 px-2">@if($reportData) {{$reportData->catatan}} @endif</textarea>
                    </div>
                    <input type="text" name="produksi_id" value="{{$produksi->id}}" hidden>
                </div>
                <div class="table-responsive ipc-table">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td class="text-center align-middle" rowspan=2 style="width:25%;">Jenis Pengujian</td>
                                <td class="text-center align-middle" rowspan=2>Spesifikasi</td>
                                <td class="text-center" colspan="50">Hasil</td>
                            </tr>
                            <tr>
                                @for($i = 1; $i <= 50; $i++)
                                <td style="width:10px;" class="text-center align-middle">{{$i}}</td>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($acuanPengujian as $items)
                            @if($items->section_name === "Report In Process Control")
                            <tr>
                                <td>{{$items->pemeriksaan}}</td>
                                <td>{{$items->spesifikasi}}</td>
                                @for($i = 1; $i <= 50; $i++)
                                @if($items->pemeriksaan === "Kesetaraan Bobot.")
                                <td>
                                    <input type="text"
                                        name="hasil[{{ $items->id }}][{{ $i }}]"
                                        class="form-control"
                                        @if($reportData) style="background-color: transparent; padding: 0; margin: 0;" disabled  value="{{ optional($report->where('acuan_laporan_ipc_id', $items->id)->first())->hasil[$i] ?? '' }}" @endif
                                    >
                                </td>
                                @else
                                <td>
                                    <select
                                        name="hasil[{{ $items->id }}][{{ $i }}]"
                                        class="form-control"
                                        @if($reportData) style="background-color: transparent; padding: 0; margin: 0;" disabled @endif
                                    >
                                        <option value="" selected disabled>Pilih</option>
                                        <option value="OK" @if($reportData && (optional($report->where('acuan_laporan_ipc_id', $items->id)->first())->hasil[$i] ?? null) === 'OK') selected @endif>OK</option>
                                        <option value="TIDAK" @if($reportData && (optional($report->where('acuan_laporan_ipc_id', $items->id)->first())->hasil[$i] ?? null) === 'TIDAK') selected @endif>TIDAK</option>
                                    </select>
                                </td>
                                @endif
                                @endfor
                                <input type="hidden" name="acuan_laporan_ipc_id[]" value="{{ $items->id }}">
                            </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">
                                    <p>Data Masih Kosong, Segera Konfirmasi Kepada QC untuk mengerjakan Quality Control.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    @if($reportData)
                        <button type="button" class="btn btn-sm btn-warning btn-edit">Ubah Data</button>
                        <button type="button" class="btn btn-sm btn-danger btn-cencel d-none">Batalkan</button>
                        <button type="submit" class="btn btn-sm btn-warning btn-save d-none">Simpan Perubahan</button>
                    @else
                        <button type="submit" class="btn btn-success">Submit</button>
                    @endif
                </div>
            </div>
        </form>
    </section>
    <!-- End From Report In Process Control -->

    <!-- Star From Pengambilan Sample Produk Jadi -->
    <section class="form-section" data-title="Form Pengambilan Sample Produk Jadi">
        <h5 class="text-center"></h5>
        <form action="{{url('/store-sample-produk-jadi')}}" method="post" class="editable-form" data-update-url="{{ url('/update-sample-produk-jadi/' . ($pengambilanSample->id ?? '')) }}">
            @csrf
            @if($pengambilanSample)
                @method('PUT')
            @endif
            <div class="card p-3">
                <div class="row">
                    <input type="text" name="produksi_id" value="{{$produksi->id}}" hidden>
                    <div class="col-md-4 mb-3">
                        <label for="">Nama Produk Jadi</label>
                        <input type="text" disabled value="{{$produksi->product->name}}"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="">Ukuran Produk Jadi</label>
                        <input type="text" disabled value="{{$produksi->product->ukuran}}"
                            placeholder="Isi Tahapan Proses" class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="">No Batch Produk Jadi</label>
                        <input type="text" disabled value="{{$produksi->batch_number}}" placeholder="Isi Tahapan Proses"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6">
                        <label for="">Tanggal Sampling</label>
                        <input type="date" name="tanggal_sampling" 
                            required
                            value="{{ $pengambilanSample->tanggal_sampling ?? now()->format('Y-m-d') }}"
                            @if($pengambilanSample) disabled @endif
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6">
                        <label for="">Jumlah Sample Diambil</label>
                        <input name="jumlah_sample_diambil" required @if($pengambilanSample)
                            value="{{$pengambilanSample->jumlah_sample_diambil}}" disabled @endif
                            placeholder="isi disini" class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6">
                        <label for="">Peralatan Sampling</label>
                        <input type="text" name="peralatan_sampling" required @if($pengambilanSample)
                            value="{{$pengambilanSample->peralatan_sampling}}" disabled @endif placeholder="isi disini"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6">
                        <label for="">Lokasi Sampling</label>
                        <input type="text" name="lokasi_sampling" required @if($pengambilanSample)
                            value="{{$pengambilanSample->lokasi_sampling}}" disabled @endif placeholder="isi disini"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="">Catatan</label>
                        <textarea type="text" name="catatan" required @if($pengambilanSample) disabled @endif
                            placeholder="isi disin (*opsional)"
                            class="form-control border border-1 py-2 px-2"> @if($pengambilanSample) {{$pengambilanSample->catatan}} @endif</textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    @if($pengambilanSample)
                        <button type="button" class="btn btn-sm btn-warning btn-edit">Ubah Data</button>
                        <button type="button" class="btn btn-sm btn-danger btn-cencel d-none">Batalkan</button>
                        <button type="submit" class="btn btn-sm btn-warning btn-save d-none">Simpan Perubahan</button>
                    @else
                        <button type="submit" class="btn btn-success">Submit</button>
                    @endif
                </div>
            </div>
        </form>
    </section>
    <!-- End From Report Sample Produk Jadi -->

    <!-- Start Form Pengujian Produk Jadi -->
    <section class="form-section" data-title="Form Pengujian Produk Jadi">
        <h5 class="text-center"></h5>
        <form action="{{('/store-pengujian-produk')}}" method="post" class="editable-form" data-update-url="{{ url('/update-pengujian-produk/' . ($pengujianProdukJadi->id ?? '')) }}">
            @csrf
            @if($pengujianProdukJadi)
                @method('PUT')
            @endif
            <div class="card p-3">
                <div class="row">
                    <div class="col-md-6">
                        <label for="">Bagian</label>
                        <input type="text" name="bagian" required placeholder="isi disini" @if($pengujianProdukJadi)
                            value="{{$pengujianProdukJadi->bagian}}" disabled @endif
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6">
                        <label for="">Tanggal</label>
                        <input type="date" name="tanggal" required placeholder="isi disini" 
                            value="{{$pengujianProdukJadi->tanggal ?? now()->format('Y-m-d')}}"
                            @if($pengujianProdukJadi) disabled @endif
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6">
                        <label for="">Nama Produk</label>
                        <input type="text" disabled value="{{$produksi->product->name}}"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6">
                        <label for="">No Batch</label>
                        <input type="text" disabled value="{{$produksi->batch_number}}"
                            class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-6">
                        <label for="">Kesimpulan</label>
                        <select name="kesimpulan" class="form-select border border-1 py-2 px-2" required
                            @if($pengujianProdukJadi) disabled @endif>
                            <option value="">Pilih Kesimpulan</option>
                            <option value="OK" @if(($pengujianProdukJadi) && $pengujianProdukJadi->kesimpulan === "OK")
                                selected @endif>Memenuhi Syarat</option>
                            <option value="NOT OK" @if(($pengujianProdukJadi) && $pengujianProdukJadi->kesimpulan ===
                                "NOT OK") selected @endif>Tidak Memenuhi Syarat</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="">Petugas QC</label>
                        <input type="text" @if($pengujianProdukJadi) disabled @endif readonly
                            value="{{$pengujianProdukJadi->petugas_qc ?? Auth::user()->name}}" name="petugas_qc" class="form-control border border-1 py-2 px-2">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="">Catatan</label>
                        <textarea type="text" name="catatan" required placeholder="isi disin (*opsional)"
                            @if($pengujianProdukJadi) disabled @endif
                            class="form-control border border-1 py-2 px-2"> {{$pengujianProdukJadi->catatan ?? null}} </textarea>
                    </div>
                    <input type="text" name="produksi_id" value="{{$produksi->id}}" hidden>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td style="width:25%;">Pemeriksaan</td>
                            <td style="width:25%;">Spesifikasi</td>
                            <td>Hasil</td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($acuanPengujian as $items)
                        @if($items->section_name === "Pengujian Produk Jadi")
                        @php
                            $spesifikasi = $items->spesifikasi;
                        
                            // PRIORITAS 1: handle "otomatis" dulu
                            if ($spesifikasi === 'otomatis') {
                                if ($items->pemeriksaan === 'Jumlah') {
                                    $spesifikasi = $produksi->product->produkjadi->acuan ?? 100;
                                } elseif ($items->pemeriksaan === 'Volume') {
                                    $spesifikasi = $produksi->product->ukuran;
                                }
                            } 
                            // PRIORITAS 2: baru hitung angka
                            if ($produksi->batch_size != 100 && $items->pemeriksaan === 'Jumlah') {
                                $angka = (int) preg_replace('/[^0-9]/', '', $items->spesifikasi);
                                $spesifikasi = ceil(($angka / 100) * $produksi->batch_size);
                            }
                        @endphp
                        <tr>
                            <td>{{$items->pemeriksaan}}</td>
                            <td>{{$spesifikasi}}</td>
                            <td>
                                @if ($items->pemeriksaan == 'Jumlah')
                                <input type="text" name="hasil_pj[]" class="form-control border border-1 py-2 px-2" value="{{$barangJadi->nilai_actual ?? 'Belum Diisi'}} {{$produksi->product->tipe}}" placeholder="jumlah barang" readonly>
                                @else
                                <input type="text" name="hasil_pj[]" @if($pengujianProdukJadi)
                                    value="{{ $ppj->firstWhere('acuan_laporan_ipc_id', $items->id)->hasil ?? 'Belum Diisi' }}"
                                    disabled @endif required placeholder="isi disini"
                                    class="form-control border border-1 py-2 px-2">
                                @endif
                                <input type="text" name="acuan_laporan_ipc_id[]" value="{{$items->id}}" hidden>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">
                                <p>Data Masih Kosong, Segera Konfirmasi Kepada QC untuk mengerjakan Quality Control.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table> 
                <div class="d-flex justify-content-end gap-2">
                    @if($pengujianProdukJadi)
                        <button type="button" class="btn btn-sm btn-warning btn-edit">Ubah Data</button>
                        <button type="button" class="btn btn-sm btn-danger btn-cencel d-none">Batalkan</button>
                        <button type="submit" class="btn btn-sm btn-warning btn-save d-none">Simpan Perubahan</button>
                    @else
                        <button type="submit" class="btn btn-success">Submit</button>
                    @endif
                </div>
            </div>
        </form>
    </section>
</div>
@if($sampleIPC && $inspeksiData && $reportData && $pengambilanSample && $pengujianProdukJadi)
<div class="text-end">
    <a href="{{ url('/print-laporan-ipc', $produksi->batch_number) }}" class="btn btn-primary m-4 px-4 py-2">
        Cetak Laporan
    </a>
</div>
@endif
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.editable-form').forEach(form => {
        const editBtn = form.querySelector('.btn-edit');
        const saveBtn = form.querySelector('.btn-save');
        const cencel = form.querySelector('.btn-cencel');
        const inputs = form.querySelectorAll('input, textarea, select');

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                inputs.forEach(i => i.disabled = false);
                editBtn.classList.add('d-none');
                saveBtn.classList.remove('d-none');
                cencel.classList.remove('d-none');
                form.action = form.dataset.updateUrl;
            });
            if (cencel) {
                cencel.addEventListener('click', () => {
                    inputs.forEach(i => i.disabled = true);
                    editBtn.classList.remove('d-none');
                    saveBtn.classList.add('d-none');
                    cencel.classList.add('d-none');
                });
            }
        }
    });

    const sections = document.querySelectorAll(".form-section");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    const titleEl = document.getElementById("form-title");

    // Tentukan index awal pakai Blade (cek data mana yang sudah ada)
    let currentIndex = 0;

    @if($sampleIPC)
    currentIndex = 1;
    @endif

    @if($sampleIPC && $inspeksiData)
    currentIndex = 2;
    @endif

    @if($sampleIPC && $inspeksiData && $reportData)
    currentIndex = 3;
    @endif

    @if($sampleIPC && $inspeksiData && $reportData && $pengambilanSample)
    currentIndex = 4;
    @endif

    function showSection(index) {
        sections.forEach((sec, i) => {
            sec.style.display = (i === index) ? "block" : "none";
        });

        titleEl.textContent = sections[index].dataset.title;

        prevBtn.disabled = (index === 0);
        nextBtn.disabled = (index === sections.length - 1);
    }

    prevBtn.addEventListener("click", function() {
        if (currentIndex > 0) {
            currentIndex--;
            showSection(currentIndex);
        }
    });

    nextBtn.addEventListener("click", function() {
        if (currentIndex < sections.length - 1) {
            currentIndex++;
            showSection(currentIndex);
        }
    });

    showSection(currentIndex);
});
</script>
@endsection