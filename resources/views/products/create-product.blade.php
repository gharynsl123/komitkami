@extends('layouts.app')
@section('title', 'Komitkami Helper | Tambah Produk')
@section('content')
<div class="card p-3">
    <h6 class="mb-4">Create Products</h6>

    <form action="{{url('/store-products')}}" class="row" method="post" enctype="multipart/form-data">
        @csrf
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <label class="form-label">Name</label>
                <input name="name" type="text" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <label class="form-label">Code Product</label>
                <input name="code" type="text" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <label class="form-label">Ukuran</label>
                <input name="ukuran" type="text" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <select name="brand_id" class="form-control" id="exampleFormControlSelect1">
                    <option>-- Pilih Merek --</option>
                    @foreach($brand as $row)
                    <option value="{{$row->id}}">{{$row->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <select name="tipe" class="form-control border px-2" id="exampleFormControlSelect1">
                    <option>-- Pilih Tipe --</option>
                    <option value="jerigen">Jerigen</option>
                    <option value="botol">Botol</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <select name="kategori" class="form-control border px-2" id="exampleFormControlSelect1">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="ALKES">ALKES</option>
                    <option value="PKRT">PKRT</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <label class="form-label">Jenis</label>
                <input name="jenis" type="text" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline">
                <label class="form-label">Stok</label>
                <input name="stok" type="text" class="form-control">
            </div>
            <p class="small">*jika belum ada. maka bisa di dilewati dan di isi belakangan</p>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-outline mb-3">
                <label class="form-label">Harga produk</label>
                <input name="price" type="text" class="form-control" id="priceInput">
            </div>
        </div>

        <div class="col-md-12 mb-0 mt-4">
            <button type="submit" class=" btn btn-primary">
                Create
            </button>
            <a href="/produk" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
const input = document.getElementById('priceInput');

function formatCurrencyInput(e) {
    let value = e.target.value.replace(/\D/g, '');

    if (!value) {
        e.target.value = '';
        return;
    }

    let intValue = value.slice(0, -2);
    let decimalValue = value.slice(-2);

    if (value.length <= 2) {
        intValue = '0';
        decimalValue = value.padStart(2, '0');
    }

    let formattedInt = new Intl.NumberFormat('id-ID').format(parseInt(intValue));
    e.target.value = `Rp ${formattedInt},${decimalValue}`;
}

input.addEventListener('input', formatCurrencyInput);
});
</script>
@endsection