@extends('layouts.app')
@section('title', 'Komitkami Helper | Dashboard')

@section('content')
@if(Auth::user()->hasRole('Customer'))
@include('home.costumer')

@elseif(Auth::user()->hasRole('Sales') || Auth::user()->hasRole('Admin'))
@include('home.admin-seles')

@elseif(Auth::user()->hasAnyRole(['Quality Control', 'Producer']))
@include('produksi.list-produksi')

@else
<div class="row mt-4">
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-header pb-0 px-3">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <h6 class="mb-0">Informasi Pemesanan</h6>
                    </div>
                </div>
            </div>
            <div id="orderCard" class="card-body pt-4 p-3">
                <ul class="list-group">
                    @if(!auth()->user()->hasRole('Production Manager'))
                    @forelse($groupedOrders->where('status' , 'pending')->reverse() as $items)                    
                    @include('widget.list-group')
                    @empty
                    <p class="text-center">Tidak ada pesanan menunggu permintaan</p>
                    @endforelse
                    @else
                    @forelse($groupedOrders->whereIn('status', ['pending', 'accept', 'prosses'])->reverse() as $items)
                    @include('widget.list-group')
                    @empty
                    <p class="text-center">Tidak ada pesanan menunggu permintaan</p>
                    @endforelse
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endif
@endsection