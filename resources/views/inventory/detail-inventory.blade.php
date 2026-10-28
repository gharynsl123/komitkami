@extends('layouts.app')
@section('title', 'Komitkami Helper | Detail Inventory')
@section('content')
<a href="/local-inventory" class="btn btn-dark m-0 btn-small">Kembali</a>
<div>
    <!-- start table INFORMASI -->
    <div class="card mt-3 px-4 tab-content" id="nav-tabContent">
        <div class="card-body px-0 pb-2 tab-pane fade show active" id="general" role="tabpanel">
            <div class="table-responsive p-0">
                <table class="table table-borderless table-hover border border-0 align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder">
                                Nama</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder">
                                Kode Barang</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder">
                                stok awal</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder">
                                barang masuk</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder">
                                barang keluar</th>
                            <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder">
                                stok akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryData as $item)
                        <tr>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <h6 class="mb-0 text-sm">{{ $item['name'] }}</h6>
                                </div>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <h6 class="mb-0 text-sm">{{ $item['code'] }}</h6>
                            </td>
                            <!-- formater number -->
                            <td class="align-middle text-center">
                                <span class="text-secondary text-xs font-weight-bold">
                                    {{ formatStokInventory($item['stok_awal']) }}
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                <span class="text-secondary text-xs font-weight-bold">
                                    {{ formatStokInventory($item['stok_masuk']) }}
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                <span class="text-secondary text-xs font-weight-bold">
                                    {{ formatStokInventory($item['stok_keluar']) }}
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                <span class="text-secondary text-xs font-weight-bold">
                                    {{ formatStokInventory($item['stok_akhir']) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                        <tr style="border-top: 1px solid #000 !important;">
                            <td>Batch Yang Tersedia : </td>
                            <td class="text-center">total stok</td>
                            <td class="text-center">tanggal Expired</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach ($batchTersedia as $batch)
                        <tr>
                            <td class="">{{ $batch['nomor_batch'] }}</td>
                            <td class="text-center">
                                {{ formatStokInventory($batch['total_stok']) }}
                                @if ($batch['sisa_stok'] <= 0)
                                    <span class="text-danger">(Habis)</span>
                                @else
                                    <span class="text-success">(Tersedia: {{ formatStokInventory($batch['sisa_stok']) }})</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $batch['tanggal_ed'] 
                                    ? \Carbon\Carbon::parse($batch['tanggal_ed'])->format('d-m-Y') 
                                    : '-' 
                                }}
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- END TABLE INFORMASI BB -->

    <!-- START DETAIL INVENTORY -->
    <div class="row">
        <div class="col-lg-4 col-md-6 mt-3">
            <div class="nav-wrapper position-relative end-0">
                <ul class="nav nav-pills nav-fill p-1" id="inventoryTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link mb-0 px-0 py-1 active" id="in-tab" data-bs-toggle="tab" href="#inTable"
                            role="tab">
                            <i class="material-icons text-lg position-relative">email</i>
                            <span class="ms-1">out</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link mb-0 px-0 py-1" id="out-tab" data-bs-toggle="tab" href="#outTable"
                            role="tab">
                            <i class="material-icons text-lg position-relative">home</i>
                            <span class="ms-1">in</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="tab-content mt-3" id="inventoryTabContent">
        <h6>History Transacction Barang</h6>
        <div class="tab-pane fade show active" id="inTable" role="tabpanel" aria-labelledby="in-tab">
            @include('widget.table-out')
        </div>
        <div class="tab-pane fade" id="outTable" role="tabpanel" aria-labelledby="out-tab">
            @include('widget.table-in')
        </div>
    </div>

</div>
@endsection