@extends('layouts.app')
@section('title', 'KomitKami Helper | Pengecheckan Material Gudang')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-sm btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalManualReport">
                Buat Laporan Manual
            </button>
        </div>
    </div>
</div>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="stock-in-tab" data-bs-toggle="tab" data-bs-target="#stock-in" type="button" role="tab" aria-controls="stock-in" aria-selected="true">Dari Stock In</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab" aria-controls="manual" aria-selected="false">Laporan Manual</button>
    </li>
</ul>

<div class="card">
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <!-- Tab Stock In -->
            <div class="tab-pane fade show active" id="stock-in" role="tabpanel" aria-labelledby="stock-in-tab">
                <div class="table-responsive">
                    <table class="table table-bordered border table-hover text-nowrap align-middle" id="dataTableDefault" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Informasi Barang</th>
                                <th>Jumlah Barang & Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barangMasuk->reverse() as $no => $barang)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $barang->inventory->name }}</div>
                                    <div class="text-muted small">Batch: {{ $barang->nomor_batch ?? 'tidak ada'}} &bull; Supplier: {{ $barang->vendor ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ formatJumlah($barang->stok) }} <span class="small">{{ $barang->inventory->type == 'Bahan Baku' ? 'gram' : 'pcs' }}</span></div>
                                    <div class="text-muted small">{{ $barang->tanggal_masuk->format('d-m-Y') }}</div>
                                </td>
                                <td>
                                    @if($barang->statusPengecekan() == 'Belum Diperiksa')
                                        <span class="badge bg-secondary">{{ $barang->statusPengecekan() }}</span>
                                    @elseif(str_contains($barang->statusPengecekan(), 'Selesai'))
                                        <span class="badge bg-success">{{ $barang->statusPengecekan() }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ $barang->statusPengecekan() }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('detail-pengecekan-material', $barang->id) }}?type=stock_in" class="btn btn-sm btn-primary">
                                        Buat Laporan
                                    </a>
                                    @if(str_contains($barang->statusPengecekan(), 'Selesai'))
                                    <a href="{{ route('pengecekan-material.print', $barang->id) }}?type=stock_in" target="_blank" class="btn btn-sm btn-success ms-1">
                                        Cetak PDF
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Manual -->
            <div class="tab-pane fade" id="manual" role="tabpanel" aria-labelledby="manual-tab">
                <div class="table-responsive">
                    <table class="table table-bordered border table-hover text-nowrap align-middle dataTableManual" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Informasi Material</th>
                                <th>Tanggal Laporan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($manualReports->reverse() as $no => $manual)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $manual->inventory->name }}</div>
                                    <div class="text-muted small">Batch: {{ $manual->nomor_batch ?? '-' }} &bull; Supplier: {{ $manual->suplierMaterial->name ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $manual->tanggal_laporan ? $manual->tanggal_laporan->format('d-m-Y') : '-' }}</div>
                                </td>
                                <td>
                                    <a href="{{ url('detail-pengecekan-material', $manual->id) }}?type=manual" class="btn btn-sm btn-primary">
                                        Buat Laporan
                                    </a>
                                    <a href="{{ route('pengecekan-material.print', $manual->id) }}?type=manual" target="_blank" class="btn btn-sm btn-success ms-1">
                                        Cetak PDF
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
</div>

<!-- Modal Create Manual Report -->
<div class="modal fade" id="modalManualReport" tabindex="-1" aria-labelledby="modalManualReportLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ url('store-manual-report') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="modalManualReportLabel">Buat Laporan Manual Baru</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label>Pilih Material <span class="text-danger">*</span></label>
                <select id="selectMaterial" name="inventory_id" class="form-select border " required>
                    <option value="">-- Pilih Material --</option>
                    @foreach($inventory as $inv)
                        <option value="{{ $inv->id }}">{{ $inv->name }} ({{ $inv->type }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Pilih Supplier</label>
                <select id="selectSupplier" name="suplier_material_id" class="form-select">
                    <option value="">-- Pilih Supplier --</option>
                    @foreach($supliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Nomor Batch</label>
                <input type="text" name="nomor_batch" class="form-control border-1 px-2 py-1 border">
            </div>
            <div class="mb-3">
                <label>Tanggal Laporan <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_laporan" class="form-control border-1 px-2 py-1 border" required value="{{ date('Y-m-d') }}">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan & Lanjutkan</button>
          </div>
      </form>
    </div>
  </div>
</div>
<script>
    const suppliersData = @json($supliers);

    $(document).ready(function() {
        if ($('.dataTableManual').length) {
            $('.dataTableManual').DataTable({
                ordering : false,
                lengthChange: false
            });
        }

        $('#selectMaterial').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#modalManualReport')
        });

        $('#selectSupplier').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#modalManualReport')
        });

        $('#selectMaterial').on('change', function() {
            const materialId = $(this).val();
            const supplierSelect = $('#selectSupplier');
            
            // Hapus semua option kecuali option pertama
            supplierSelect.empty();
            supplierSelect.append(new Option('-- Pilih Supplier --', '', true, true));

            if (materialId) {
                const filteredSuppliers = suppliersData.filter(s => s.inventory_id == materialId);
                filteredSuppliers.forEach(s => {
                    supplierSelect.append(new Option(s.name, s.id, false, false));
                });
            }
            // Trigger perubahan untuk select2
            supplierSelect.trigger('change');
        });
    });
</script>
 <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
     rel="stylesheet" />
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

