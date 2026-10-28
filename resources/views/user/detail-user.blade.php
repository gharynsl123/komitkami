@extends('layouts.app')
@section('title', 'Komitkami Helper | Detail User')
@section('content')
<!-- informasi user yang di klik -->
<div class="card p-3">
    <h6 class="mb-4">Detail Pengguna</h6>
    <div class="row">
        <div class="col-md-6">
            <label class="form-label me-2">Nama</label>
            <div class="input-group input-group-outline mb-3">
                <input type="text" class="form-control" value="{{$user->name}}" readonly>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label me-2">Username</label>
            <div class="input-group input-group-outline mb-3">
                <input type="text" class="form-control" value="{{$user->username}}" readonly>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label me-2">Role</label>
            <div class="input-group input-group-outline mb-3">
                <input type="text" class="form-control" value="{{ $user->roles->pluck('role_name')->join(', ') }}" readonly>
            </div>
        </div>
        @if($user->hasRole('Customer'))
        <div class="col-md-6">
            <label class="form-label me-2">Tipe</label>
            <div class="input-group input-group-outline mb-3">
                <input type="text" class="form-control" value="{{$user->tipe}}" readonly>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label me-2">NPWP</label>
            <div class="input-group input-group-outline mb-3">
                <input type="text" class="form-control" value="{{$user->npwp}}" readonly>
            </div>
        </div>
        @endif
        <div class="col-md-6">
            <label class="form-label me-2">Email</label>
            <div class="input-group input-group-outline mb-3">
                <input type="text" class="form-control" value="{{$user->email ?? 'tidak ada'}}" readonly>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label me-2">Password</label>
            <div class="input-group input-group-outline mb-3">
                <input type="text" value="{{$user->raw_pass}}" class="form-control">
            </div>
        </div>
    </div>

    <a href="{{url('/user-configuration')}}" class="btn btn-outline-secondary">Kembali</a>
</div>
@endsection