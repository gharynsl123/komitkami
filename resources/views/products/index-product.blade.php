@extends('layouts.app')
@section('title', 'Komitkami Helper | Daftar Produk')
@section('content')
<div class="d-flex justify-content-between px-4">
    <div class="d-flex align-items-center">
        <h5 class="mb-0">Produk Kita</h5>
    </div>
    <div class="d-flex gap-2">
        <a href="{{url('/print-log-book-product')}}" class="btn btn-secondary m-0" target="_blank">Log Book Pengiriman Barang</a>
        <a href="{{url('/print-product')}}" class="btn btn-primary m-0">Print Produk</a>
        <a class="btn bg-gradient-dark mb-0" href="{{('/create-products')}}"><i
                class="material-icons text-sm">add</i>&nbsp;&nbsp;Tambah Produk Baru</a>
    </div>
</div>
<div class="card my-4 px-4">
    <div class="card-body px-0 pb-2">
        <div class="table-responsive p-0">
            <table class="table table-borderless table-hover border border-0 align-items-center mb-0"
                id="dataTableDefault">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                            Produk</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                            stok</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                            Dimiliki oleh</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                            harga</th>
                        <th class="text-secondary opacity-7"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $items)
                    <tr>
                        <td>
                            <h6 class="mb-0 text-sm">{{$items->name}}</h6>
                            <small class="text-muted">{{$items->code}}</small>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">{{$items->data_stock ? $items->data_stock : '0'}}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">{{$items->brand->users->name}} <br><small>{{$items->brand->name}}</small></p>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">@currency($items->price)</span>
                        </td>
                        <td class="align-middle">
                            @if (!Auth::user()->hasRole('Inventory Manager'))
                            <a href="{{url('/edit-products', $items->id)}}"
                                class="btn btn-sm btn-warning font-weight-bold text-xs" data-toggle="tooltip"
                                data-original-title="Edit product">
                                Edit
                            </a>
                            <a href="{{ route('products.show', $items->id) }}"
                                class="btn btn-sm btn-success font-weight-bold text-xs" data-toggle="tooltip"
                                data-original-title="Edit product">
                                Details
                            </a>
                            @endif
                            <a href="{{ url('/print-kartu-stok-product', $items->name) }}"
                                class="btn btn-sm btn-secondary font-weight-bold text-xs" data-toggle="tooltip"
                                data-original-title="Edit product">
                                Kartu Stok
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#productTable').DataTable({
        "info": true,
        "ordering": false,
        "lengthChange": false,
        "pageLength": 6,
        "searching": true
    });
});
</script>
@endsection