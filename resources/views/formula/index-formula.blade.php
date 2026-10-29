@extends('layouts.app')
@section('title', 'Komitkami | Formula Produk')
@section('content')

<!-- Custom CSS untuk menghilangkan padding dalam tabel -->
<style>
table th,
table td {
    padding: 0 1.5rem 0 1.5rem !important;
    margin: 0 !important;
    vertical-align: middle;
    color: #000000 !important;
}

table thead th {
    background-color: #0080FFFF;
    color: white;
}

table {
    border-bottom: 1px solid #ddd;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Formula Produk</h5>
    @if(Auth::user()->hasAnyRole('Admin', 'Production Manager'))
    <a href="{{ url('/craete/formula-produk') }}" class="btn m-0 btn-dark">Buat Formula Produk</a>
    @endif
</div>

<!-- tab pill navbar -->
<div class="row">
    <div class="col-lg-4 col-md-6">
        <div class="nav-wrapper position-relative end-0">
            <ul class="nav nav-pills nav-fill p-1" id="inventoryTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link mb-0 px-0 py-1 active" id="in-tab" data-bs-toggle="tab" href="#inTable"
                        role="tab">
                        <i class="material-icons text-lg position-relative">science</i>
                        <span class="ms-1">Bahan Baku</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link mb-0 px-0 py-1" id="out-tab" data-bs-toggle="tab" href="#outTable" role="tab">
                        <i class="material-icons text-lg position-relative">all_inbox</i>
                        <span class="ms-1">Bahan Kemas</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div>
            <input type="text" id="searchInput" class="form-control bg-white border border-1 px-2" placeholder="Cari formula...">
        </div>
    </div>
</div>

<div class="tab-content mt-3" id="inventoryTabContent">
    <div class="tab-pane fade show active" id="inTable" role="tabpanel" aria-labelledby="in-tab">
        @forelse($formulaBahanBaku as $formula)
        <div class="card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="m-0 ">Nama Formula : {{ $formula->nama_formula ?: 'Nama template kosong' }}</h6>
                <div>
                    @if(Auth::user()->hasAnyRole('Admin', 'Production Manager'))
                    <a href="{{url('/edit-formula', $formula->id)}}" class="btn btn-sm btn-primary m-0">Edit</a>
                    <button type="button" class="btn btn-sm btn-danger m-0 ms-1" data-bs-toggle="modal" data-bs-target="#deleteFormulaModal" onclick="setDeleteFormulaId('{{ $formula->id }}')">Hapus</button>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th class="text-uppercase">Nama Bahan</th>
                        <th class="text-uppercase">Jumlah</th>
                        <th class="text-uppercase">Satuan</th>
                    </tr>
                    @foreach($formula->bahanBaku as $item)
                    <tr class="border">
                        <td class="border">{{ $item->name }}</td>
                        <td class="border">{{formatJumlah($item->jumlah)}}</td>
                        <td class="border">{{ $item->satuan }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
        @empty
        <div class="card p-3">
            <p class="text-center m-0">Belum ada formula bahan baku yang dibuat.</p>
        </div>
        @endforelse
    </div>

    <div class="tab-pane fade" id="outTable" role="tabpanel" aria-labelledby="out-tab">
        @forelse($formulaKemasan as $formula)
        <div class="card p-3 mb-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="m-0">Formula Kemasan : {{ $formula->nama_formula ?: 'Nama template kosong' }}</h6>
                <div>
                    <a href="{{url('/edit-formula', $formula->id)}}" class="btn btn-sm btn-primary m-0">Edit</a>
                    <button type="button" class="btn btn-sm btn-danger m-0 ms-1" data-bs-toggle="modal" data-bs-target="#deleteFormulaModal" onclick="setDeleteFormulaId('{{ $formula->id }}')">Hapus</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th class="text-uppercase">Nama Kemasan</th>
                        <th class="text-uppercase">Jumlah</th>
                        <th class="text-uppercase">Satuan</th>
                    </tr>
                    @foreach($formula->bahanKemas as $item)
                    <tr class="border">
                        <td class="border">{{ $item->name }}</td>
                        <td class="border">{{formatJumlah($item->jumlah)}}</td>
                        <td class="border">{{ $item->satuan }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
        @empty
        <div class="card p-3">
            <p class="text-center m-0">Belum ada formula kemasan yang dibuat.</p>
        </div>
        @endforelse
    </div>
</div>
<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const cards = document.querySelectorAll('.card');
    
    cards.forEach(card => {
        const templateName = card.querySelector('h6').textContent.toLowerCase();
        const tableRows = card.querySelectorAll('table tbody tr');
        let hasMatch = templateName.includes(searchTerm);
        
        card.style.display = hasMatch ? '' : 'none';
    });
});


function setDeleteFormulaId(id) {
    const form = document.getElementById('deleteFormulaForm');
    form.action = '/delete-formula/' + id;
}
</script>

<!-- Delete Formula Modal -->
<div class="modal fade" id="deleteFormulaModal" tabindex="-1" aria-labelledby="deleteFormulaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteFormulaModalLabel">Verifikasi Hapus Formula</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteFormulaForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <small style="color: #344759; font-size: 12px;">Apakah Anda yakin ingin menghapus formula ini? Masukkan password Anda untuk melanjutkan.</small> 
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control border border-1 px-2 py-1" id="password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus Formula</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection