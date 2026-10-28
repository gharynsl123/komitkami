@extends('layouts.app')
@section('title', 'Komitkami Helper | Edit Invoice Manual')
@section('content')

<!-- Select2 CSS for Bootstrap 5 Theme -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<div class="pb-0 px-3">
    <div class="row mt-3">
        <div class="col-12 d-flex align-items-center">
            <h6 class="mb-0">Edit Invoice Manual</h6>
        </div>
    </div>
</div>

<div class="pt-4 px-3">
    @if(session('error'))
    <div class="alert alert-danger text-white">
        {{ session('error') }}
    </div>
    @endif

    <form action="{{ route('manual-invoice.update', $invoice->id) }}" method="POST" id="manualInvoiceForm">
        @csrf
        @method('PUT')
        
        <div class="card shadow-sm mb-4 border-0 rounded-3">
            <div class="card-body">
                <h6 class="mb-3">Informasi Pelanggan & Invoice</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="user_id" class="form-label text-muted small">Pilih Customer</label>
                        <select class="form-select border px-3" name="user_id" id="user_id" required>
                            <option value="">Pilih Customer...</option>
                            @php
                                $selectedUserId = old('user_id', $invoice->manualItems->first()->user_id ?? '');
                            @endphp
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $selectedUserId == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} {{ $customer->npwp ? '(NPWP: '.$customer->npwp.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="no_invoice" class="form-label text-muted small">Nomor Invoice</label>
                        <input type="text" class="form-control border px-3" name="no_invoice" id="no_invoice" value="{{ old('no_invoice', $invoice->no_invoice) }}" required placeholder="Contoh: KIG/INV/001/2026">
                        @error('no_invoice') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- status -->
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label text-muted small">Status</label>
                        <select class="form-select border px-3" name="status" id="status" required>
                            <option value="" data-price="0">Pilih Status...</option>
                            <option value="un-paid" {{ old('status', $invoice->status) == 'un-paid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="paid" {{ old('status', $invoice->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                        @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="tanggal_terbit" class="form-label text-muted small">Tanggal Terbit</label>
                        <input type="date" class="form-control border px-3" name="tanggal_terbit" id="tanggal_terbit" value="{{ old('tanggal_terbit', \Carbon\Carbon::parse($invoice->tanggal_terbit)->format('Y-m-d')) }}" required>
                        @error('tanggal_terbit') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="customer_reference" class="form-label text-muted small">Customer Reference (Opsional)</label>
                        <input type="text" class="form-control border px-3" name="customer_reference" id="customer_reference" value="{{ old('customer_reference', $invoice->customer_refrence) }}" placeholder="Contoh: PO-001 - SJ-002">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="note" class="form-label text-muted small">Catatan / Terms (Opsional)</label>
                        <input type="text" class="form-control border px-3" name="note" id="note" value="{{ old('note', $invoice->note) }}" placeholder="Contoh: Net 30">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4 border-0 rounded-3">
            <div class="card-body">
                <h6 class="mb-3">Item Invoice</h6>
                
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead class="bg-light">
                            <tr>
                                <th>Produk</th>
                                <th style="width: 150px">Quantity</th>
                                <th style="width: 200px">Harga Satuan (Rp)</th>
                                <th style="width: 200px">Subtotal (Rp)</th>
                                <th style="width: 50px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            @foreach($invoice->manualItems as $index => $item)
                                <tr class="item-row existing-row" data-index="{{ $index }}">
                                    <td>
                                        <select class="form-select border px-3 product-select" name="items[{{ $index }}][product_id]" required>
                                            <option value="" data-price="0">Pilih Produk...</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" data-price="{{ $p->price }}" {{ $p->id == $item->product_id ? 'selected' : '' }}>{{ $p->name }} - {{ $p->tipe }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control border px-3 qty-input" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control border px-3 price-input" name="items[{{ $index }}][price]" value="{{ $item->price_per_item }}" min="0" step="0.01" required>
                                    </td>
                                    <td class="subtotal-cell fw-bold">
                                        Rp 0
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm p-2 mb-0 remove-btn">
                                            <i class="material-icons text-sm m-0">delete</i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Grand Total</th>
                                <th id="grandTotal" class="fs-5 text-primary fw-bold">Rp 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addItemBtn">
                    <i class="material-icons text-sm">add</i> Tambah Item
                </button>
            </div>
        </div>

        <div class="text-end">
            <a href="{{ route('delivery.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" id="btnSubmit">Perbarui Invoice</button>
        </div>
    </form>
</div>

<!-- Hidden template for a row -->
<template id="rowTemplate">
    <tr class="item-row">
        <td>
            <select class="form-select border px-3 product-select" name="items[{index}][product_id]" required>
                <option value="" data-price="0">Pilih Produk...</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" data-price="{{ $p->price }}">{{ $p->name }} - {{ $p->tipe }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="form-control border px-3 qty-input" name="items[{index}][quantity]" value="1" min="1" required>
        </td>
        <td>
            <input type="number" class="form-control border px-3 price-input" name="items[{index}][price]" value="0" min="0" step="0.01" required>
        </td>
        <td class="subtotal-cell fw-bold">
            Rp 0
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm p-2 mb-0 remove-btn">
                <i class="material-icons text-sm m-0">delete</i>
            </button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Start index based on existing items to avoid array collision in form
    let rowIndex = {{ count($invoice->manualItems) }};
    const itemsBody = document.getElementById('itemsBody');
    const rowTemplate = document.getElementById('rowTemplate').innerHTML;
    
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }
    
    function calculateTotals() {
        let grandTotal = 0;
        const rows = document.querySelectorAll('.item-row');
        
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const subtotal = qty * price;
            
            row.querySelector('.subtotal-cell').textContent = formatRupiah(subtotal);
            grandTotal += subtotal;
        });
        
        document.getElementById('grandTotal').textContent = formatRupiah(grandTotal);
    }
    
    function initializeRowEvents(row, isNew = false) {
        const $productSelect = $(row.querySelector('.product-select'));
        
        // Initialize Select2
        $productSelect.select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Setup event listeners
        $productSelect.on('select2:select', function(e) {
            const selectedOption = e.params.data.element;
            const price = selectedOption.getAttribute('data-price') || 0;
            row.querySelector('.price-input').value = price;
            calculateTotals();
        });
        
        row.querySelector('.qty-input').addEventListener('input', calculateTotals);
        row.querySelector('.price-input').addEventListener('input', calculateTotals);
        
        row.querySelector('.remove-btn').addEventListener('click', function() {
            if (document.querySelectorAll('.item-row').length > 1) {
                // If it's an existing Select2 element, we should destroy it first before removing node
                if ($productSelect.hasClass("select2-hidden-accessible")) {
                    $productSelect.select2('destroy');
                }
                row.remove();
                calculateTotals();
            } else {
                alert('Minimal harus ada 1 item.');
            }
        });
    }

    function addRow() {
        const newRowHTML = rowTemplate.replace(/{index}/g, rowIndex);
        itemsBody.insertAdjacentHTML('beforeend', newRowHTML);
        
        const newRow = itemsBody.lastElementChild;
        initializeRowEvents(newRow, true);
        
        rowIndex++;
        calculateTotals();
    }
    
    document.getElementById('addItemBtn').addEventListener('click', addRow);
    
    // Initialize existing rows
    document.querySelectorAll('.existing-row').forEach(row => {
        initializeRowEvents(row, false);
    });
    
    // Calculate initial totals
    calculateTotals();
    
    // Form submission validation
    document.getElementById('manualInvoiceForm').addEventListener('submit', function(e) {
        if (document.querySelectorAll('.item-row').length === 0) {
            e.preventDefault();
            alert('Harap tambahkan setidaknya 1 item.');
            return;
        }
        
        const btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Menyimpan...';
    });

    // Initialize Select2 for the Customer select
    $('#user_id').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
});
</script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
