@extends('layouts.app')
@section('title', 'Komitkami Helper | Edit User')
@section('content')
<div class="card p-3">
    <h6 class="mb-4">Edit User</h6>

    <form action="{{ url('/update-user/' . $user->id) }}" class="row" method="post">
        @csrf
        @method('put')
        <div class="col-md-6">
            <label class="form-label">Name</label>
            <div class="input-group input-group-outline mb-3">
                <input name="name" type="text" class="form-control" value="{{ $user->name }}">
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">User Name</label>
            <div class="input-group input-group-outline mb-3">
                <input name="username" type="text" class="form-control" value="{{ $user->username }}">
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Role</label>
            <div class="input-group input-group-outline mb-3">
                <select name="roles[]" class="form-control" id="roleselect" multiple>
                    @foreach($allRoles as $role)
                    <option value="{{ $role->id }}" @if($user->roles->contains($role->id)) selected @endif>
                        {{ $role->role_name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        @if($user->hasRole('Customer'))
        <div class="col-md-6">
            <label class="form-label">tipe</label>
            <div class="input-group input-group-outline mb-3">
                <select name="tipe" class="form-control">
                    <option value="khusus" @if($user->tipe == 'khusus') selected @endif>khusus</option>
                    <option value="general" @if($user->tipe == 'general') selected @endif>general</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">NPWP</label>
            <div class="input-group input-group-outline mb-3">
                <input name="npwp" type="text" class="form-control" value="{{ $user->npwp }}">
            </div>
        </div>
        @endif
        <div class="col-md-6">
            <label class="form-label">E-mail</label>
            <div class="input-group input-group-outline mb-3">
                <input name="email" value="{{ $user->email}}" type="email" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Password</label>
            <div class="input-group input-group-outline mb-3">
                <input name="password" type="password" class="form-control">
            </div>
        </div>
        <div class="col-md-12">
            <label for="">Address</label>
            <textarea class="form-control border mb-3 px-2" name="address">
                {{$user->address}}
            </textarea>
        </div>
        <div class="col-md-12 gap-3">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="/user-configuration" class="btn btn-outline-secondary">Cancel</a>

        </div>
    </form>
    <!-- Memisahkan form delete dan tombol cancel di luar form update -->
    <div class="d-flex justify-content-end align-items-center">
        <!-- Form Delete -->
        <form action="{{ url('/delete-user', $user->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">Delete User</button>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Select2 pada elemen select dengan class 'form-control'
    $('#roleselect').select2({
        placeholder: "Pilih Role",
        allowClear: true
    });
});
</script>
@endsection