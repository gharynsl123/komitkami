@extends('layouts.app')
@section('title', 'Komitkami | Buat User Baru')
@section('content')
<div class="card p-3">
    <h6 class="mb-4">Create User</h6>

    <form action="{{url('/store-user')}}" class="row" method="post">
        @csrf
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <input name="name" type="text" class="form-control" placeholder="Nama">
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <input name="username" type="text" class="form-control" placeholder="UserName">
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <select name="roles[]" class="form-control" id="roles" multiple>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 d-none" id="tipeDiv">
            <div class="input-group input-group-outline mb-3">
                <select name="tipe" class="form-control">
                    <option value="khusus">khusus</option>
                    <option value="general">general</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 d-none" id="npwpDiv">
            <div class="input-group input-group-outline mb-3">
                <input name="npwp" type="text" class="form-control" placeholder="NPWP">
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <input name="email" type="email" class="form-control" placeholder="e-mail">
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <input name="password" type="password" class="form-control" placeholder="password">
            </div>
        </div>
        <div class="col-md-12">
            <div class="input-group input-group-outline mb-3">
                <textarea name="address" type="text" placeholder="address" class="form-control"></textarea>
            </div>
        </div>
        <div class="col-md-12 mb-0">
            <button type="submit" class=" btn btn-primary">
                Create
            </button>
            <a href="/user-configuration" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
<script>
$(document).ready(function() {
    $('#roles').select2({
        placeholder: "Pilih satu atau lebih role",
        allowClear: true
    });

    $('#roles').on('change', function () {
        // Ambil semua text dari role yang dipilih
        let selectedRoles = $(this).find('option:selected').map(function() {
            return $(this).text().toLowerCase();
        }).get();

        console.log(selectedRoles); // cek di console

        // Show/hide tipeDiv and npwpDiv
        if (selectedRoles.includes('customer')) {
            $('#tipeDiv').removeClass('d-none');
            $('#npwpDiv').removeClass('d-none');
        } else {
            $('#tipeDiv').addClass('d-none');
            $('#npwpDiv').addClass('d-none');
        }
    });
});

</script>
@endsection