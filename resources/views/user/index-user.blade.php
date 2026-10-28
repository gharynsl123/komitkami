@extends('layouts.app')
@section('title', 'Komitkami Helper | Users')
@section('content')
<div class="col-md-12 mb-lg-0 mb-4">
    <div class="row px-4">
        <div class="col-6 d-flex align-items-center">
            <h5 class="mb-0">Daftar Pengguna</h5>
        </div>
        <div class="col-6 text-end">
            <a class="btn bg-gradient-dark mb-0" href="{{url('/create-user')}}"><i
                    class="material-icons text-sm">add</i>&nbsp;&nbsp;Tambah Pengguna Baru</a>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body p-3">
            <div class="table-responsive p-0">
                <table class="table table-borderless table-hover border border-0 align-items-center mb-0" id="userTable">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Nama
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                Posisi
                            </th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Terdaftar pada
                            </th>
                            <th class="text-secondary opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user as $items)
                        <tr>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">{{$items->name}}</h6>
                                        <p class="text-xs text-secondary mb-0">{{$items->email}}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">
                                    @foreach($items->roles as $role)
                                    {{$role->role_name}}@if(!$loop->last), @endif
                                    @endforeach</p>
                            </td>
                            <td class="align-middle text-center">
                                <span
                                    class="text-secondary text-xs font-weight-bold">{{$items->created_at->format('d/m/Y')}}</span>
                            </td>
                            <td class="align-middle">
                                <a href="{{url('/edit-user',$items->id)}}"
                                    class="btn btn-sm btn-warning font-weight-bold text-xs" data-toggle="tooltip"
                                    data-original-title="Edit user">
                                    Ubah
                                </a>
                                <a href="{{url('/detail-user', $items->id)}}"
                                    class="btn btn-sm btn-info font-weight-bold text-xs">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        "info": true,
        "ordering": false,
        "lengthChange": false,
        "pageLength": 6,
        "searching": true
    });
    $('[data-toggle="modal"]').click(function() {
        var targetModal = $(this).data('target');
        $(targetModal).modal('show');
    });
});
</script>
@endsection