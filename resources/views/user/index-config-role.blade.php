@extends('layouts.app')
@section('title', 'Komitkami Helper | Role Configuration')
@section('content')
<div class="col-md-12 mb-lg-0 mb-4">
    <div class="row px-4">
        <div class="col-6 d-flex align-items-center">
            <h5 class="mb-0">Daftar Role Pengguna</h5>
        </div>
        <div class="col-6 text-end">
            <!-- Button trigger modal -->
            <button type="button" class="btn bg-gradient-dark mb-0" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                <i class="material-icons text-sm">add</i> Tambah Role Baru
            </button>
        </div>
    </div>

            <!-- Modal -->
            <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form action="{{ url('/add-role') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                      <h5 class="modal-title" id="addRoleModalLabel">Tambah Role Baru</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body align-items-start">
                        <div class="mb-3">
                            <label>Nama Role</label>
                            <input type="text" class="form-control border px-3 py-1 border-1" id="role_name" name="role_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
    <div class="card mt-3">
        <div class="card-body p-3">
            <div class="table-responsive p-0">
                <table class="table table-borderless table-hover border border-0 align-items-center mb-0"
                    id="roleTable">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Nama Role
                            </th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Jumlah Pengguna
                            </th>
                            <th class="text-secondary opacity-7"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">{{$role->role_name}}</h6>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <span class="text-secondary text-xs font-weight-bold">
                                    <!-- counting role -->
                                    {{ $role->users->count() }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <a href="{{url('/edit-role', $role->id)}}"
                                    class="btn btn-sm btn-warning font-weight-bold text-xs" data-toggle="tooltip"
                                    data-original-title="Edit role">
                                    Ubah
                                </a>
                                <a href="{{url('/detail-role', $role->id)}}"
                                    class="btn btn-sm btn-info font-weight-bold text-xs" data-toggle="tooltip"
                                    data-original-title="Detail role">
                                    Detail
                                </a>
                                <form action="{{url('/delete-role', $role->id)}}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger font-weight-bold text-xs"
                                        data-toggle="tooltip" data-original-title="Hapus role">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection