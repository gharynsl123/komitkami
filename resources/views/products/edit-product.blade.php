@extends('layouts.app')
@section('title', 'Komitkami Helper | Edit Produk')
@section('content')
<div class="card p-3">
    <h6 class="mb-4">Edit Product</h6>
    <form id="update-product-form" action="{{ url('/update-product/' . $product->id) }}" class="row" method="post">
        @csrf
        @method('PUT')
        <div class="col-md-6">
            <label class="form-label">Name</label>
            <div class="input-group input-group-outline mb-3">
                <input name="name" type="text" class="form-control" value="{{ old('name', $product->name ?? '-') }}">
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Code Product</label>
            <div class="input-group input-group-outline mb-3">
                <input name="code" type="text" class="form-control" value="{{ old('code', $product->code ?? '-') }}">
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Ukuran</label>
            <div class="input-group input-group-outline mb-3">
                <input name="ukuran" type="text" class="form-control" value="{{ old('ukuran', $product->ukuran ?? '-') }}">
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Merek</label>
            <div class="input-group input-group-outline mb-3">
                <select name="brand_id" class="form-control" id="exampleFormControlSelect1">
                    <option>-- Pilih Merek --</option>
                    @foreach($brand as $row)
                        <option value="{{ $row->id }}" {{ $row->id == $product->brand_id ? 'selected' : '' }}>
                            {{ $row->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Kemasan</label>
                <select name="tipe" class="form-control border px-2" id="exampleFormControlSelect1">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="jerigen" {{ $product->tipe == 'jerigen' ? 'selected' : '' }}>Jerigen</option>
                    <option value="botol" {{ $product->tipe == 'botol' ? 'selected' : '' }}>Botol</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Kategori</label>
            <div class="input-group input-group-outline mb-3">
                <select name="kategori" class="form-control border px-2" id="exampleFormControlSelect1">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="ALKES" {{ $product->kategori == 'ALKES' ? 'selected' : '' }}>ALKES</option>
                    <option value="PKRT" {{ $product->kategori == 'PKRT' ? 'selected' : '' }}>PKRT</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Jenis Produk</label>
            <div class="input-group input-group-outline mb-3">
                <input name="jenis" type="text" class="form-control" value="{{ old('jenis', $product->jenis ?? '-') }}">
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Stok</label>
            <div class="input-group input-group-outline">
                <input name="stok" type="text" class="form-control" value="{{ old('stok', $product->stok ?? '-') }}">
            </div>
            <p class="small m-0">*jika belum ada, maka bisa dilewati dan diisi belakangan</p>
        </div>

        <div class="col-md-6">
            <label class="form-label">Harga Produk</label>
            <div class="input-group input-group-outline mb-3">
                <input 
                    name="price" 
                    type="text" 
                    class="form-control" 
                    id="priceInput"
                    value="@currency($product->price)">
            </div>
        </div>
    </form>
    
    <div class="col-md-12 mb-0 mt-4 d-flex gap-2">
        <button type="submit" form="update-product-form" class="btn btn-primary m-0">
            Update
        </button>
        <a href="/produk" class="btn btn-outline-secondary m-0">Cancel</a>
        <form action="{{ url('/delete-product/' . $product->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger m-0">Hapus</button>
        </form>
    </div>
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
</script>
@endsection
