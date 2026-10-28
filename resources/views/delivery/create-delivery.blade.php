@extends('layouts.app')
@section('title', 'Komitkami Helper | Buat Draft Pengiriman')
@section('content')
<style>
.order-item { background-color:#f8f9fa; padding:15px; margin-bottom:10px; border-radius:5px; border-left: 4px solid #6c757d; }
.order-item:hover { background-color:#e9ecef; }
.batch-table th, .batch-table td { font-size: 0.9rem; padding: 5px; vertical-align: middle; }
.batch-input { width: 80px; text-align: center; }
.qty-invalid { border-color: #dc3545 !important; background-color: #f8d7da !important; }
.accordion-button:not(.collapsed) {
    background-color: #e7f1ff;
    color: #0c63e4;
}
</style>
<div class="container pb-5">
    <div class="row">
        <div class="col-md-12">
            <h4 class="mb-3">Buat Delivery Baru</h4>
        </div>
    </div>

    <form method="POST" action="{{ route('delivery.store') }}" id="deliveryForm">
        @csrf
        <input type="hidden" name="items" id="itemsInput">

        <!-- 1. Header Surat Jalan -->
        <div class="card p-4 mb-4 shadow-sm border-0">
            <h5 class="mb-3 border-bottom pb-2">Informasi Surat Jalan</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Nomor Surat Jalan</label>
                    <input type="text" name="no_sj" class="form-control border px-2" placeholder="(Otomatis jika dikosongkan)">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Tanggal Terbit</label>
                    <input type="date" name="tanggal_terbit" class="form-control border px-2" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
        </div>

        <!-- 2. Order List dengan Accordion -->
        <div class="card p-4 mb-4 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                <h5 class="mb-0">Pilih Item Pengiriman (Status: Process)</h5>
                <div class="w-50">
                    <input type="text" id="searchInvoice" class="form-control form-control-sm border" placeholder="Cari Nomor Invoice / PO / Produk...">
                </div>
            </div>
            
            <div class="accordion" id="accordionInvoices">
                @foreach($invoices as $index => $invoice)
                    <div class="accordion-item mb-2 border border-2 px-2 rounded">
                        <h2 class="accordion-header" id="heading-{{ $invoice->id }}">
                            <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }} fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $invoice->id }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $invoice->id }}">
                                Invoice: {{ $invoice->nomor_invoice }} ({{ $invoice->orders->count() }} Item)
                            </button>
                        </h2>
                        <div id="collapse-{{ $invoice->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading-{{ $invoice->id }}" data-bs-parent="#accordionInvoices">
                            <div class="accordion-body">
                                
                                @foreach($invoice->orders as $order)
                                <div class="order-item" id="order-container-{{ $order->id }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $order->product->name }}</h6>
                                            <small class="text-muted">Qty Order (Sisa): <span class="fw-bold text-dark qty-order-need" data-need="{{ $order->remaining_quantity }}">{{ $order->remaining_quantity }}</span></small>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-primary me-2 btn-autofill" data-order-id="{{ $order->id }}">
                                                <i class="bi bi-magic"></i> Auto-Fill FIFO
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success btn-add-order" data-order-id="{{ $order->id }}"
                                                data-product-id="{{ $order->product_id }}"
                                                data-invoice-id="{{ $invoice->id }}"
                                                data-user-id="{{ $order->user->id }}"
                                                data-product-name="{{ $order->product->name }}"
                                                data-invoice-no="{{ $invoice->nomor_invoice }}">
                                                Tambah ke Pengiriman
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Daftar Batch -->
                                    <div class="table-responsive mt-2">
                                        <table class="table table-sm table-bordered batch-table mb-0 bg-white">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No. Batch</th>
                                                    <th>Tgl Masuk/ED</th>
                                                    <th class="text-center">Stok Tersedia</th>
                                                    <th class="text-center" width="120">Ambil Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody class="batch-list-{{ $order->id }}">
                                            @php
                                                $availableBatches = 0;
                                            @endphp
                                            @foreach($order->stock_batches as $batch)
                                                @php
                                                    $used = $batch->stock_outs_sum_quantity ?? 0;
                                                    $remain = $batch->quantity - $used;
                                                @endphp
                                                @if($remain > 0)
                                                @php $availableBatches++; @endphp
                                                <tr>
                                                    <td>{{ $batch->produksi->batch_number ?? $batch->batch_number ?? '-' }}</td>
                                                    <td>{{ $batch->produksi->tanggal_expired ?? $batch->expired_date ?? '-' }}</td>
                                                    <td class="text-center fw-bold">{{ $remain }}</td>
                                                    <td class="text-center">
                                                        <input type="number" min="0" max="{{ $remain }}" class="form-control form-control-sm mx-auto batch-input"
                                                            data-batch-id="{{ $batch->id }}"
                                                            data-batch-no="{{ $batch->produksi->batch_number ?? $batch->batch_number ?? '-' }}"
                                                            data-max="{{ $remain }}"
                                                            placeholder="0">
                                                    </td>
                                                </tr>
                                                @endif
                                            @endforeach
                                            
                                            @if($availableBatches == 0)
                                                <tr>
                                                    <td colspan="4" class="text-center text-danger">Stok Kosong</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3. Tabel Daftar Pengiriman (Keranjang) -->
        <div class="card p-4 mb-4 shadow-sm border-0">
            <h5 class="mb-3 border-bottom pb-2">Daftar Pengiriman (Draft)</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle" id="deliveryTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Invoice</th>
                            <th>Produk</th>
                            <th>Batch</th>
                            <th class="text-center">Qty Kirim</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="empty-row">
                            <td colspan="5" class="text-center text-muted">Belum ada item yang ditambahkan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary fw-bold" id="btnSimpan">Simpan Delivery</button>
        </div>
    </form>
</div>

<script>
document.getElementById('searchInvoice').addEventListener('input', function() {
    let filter = this.value.toLowerCase();
    let accordions = document.querySelectorAll('#accordionInvoices .accordion-item');
    
    accordions.forEach(item => {
        let text = item.innerText.toLowerCase();
        if (text.includes(filter)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});

let items = [];

document.querySelectorAll('.batch-input').forEach(input => {
    input.addEventListener('input', function() {
        let val = parseInt(this.value) || 0;
        let max = parseInt(this.dataset.max);
        
        if (val < 0) this.value = 0;
        if (val > max) {
            this.classList.add('qty-invalid');
        } else {
            this.classList.remove('qty-invalid');
        }
    });
});

document.querySelectorAll('.btn-autofill').forEach(btn => {
    btn.addEventListener('click', function() {
        const orderId = this.dataset.orderId;
        const container = document.getElementById('order-container-' + orderId);
        let qtyNeed = parseInt(container.querySelector('.qty-order-need').dataset.need);
        
        const inputs = container.querySelectorAll('.batch-input');
        
        inputs.forEach(inp => { inp.value = ''; inp.classList.remove('qty-invalid'); });
        
        let remainingNeed = qtyNeed;
        
        inputs.forEach(inp => {
            if (remainingNeed > 0) {
                let max = parseInt(inp.dataset.max);
                let take = Math.min(max, remainingNeed);
                inp.value = take;
                remainingNeed -= take;
            }
        });
        
        if (remainingNeed > 0) {
            alert('Stok tidak mencukupi untuk memenuhi seluruh Qty Order secara otomatis.');
        }
    });
});

document.querySelectorAll('.btn-add-order').forEach(btn => {
    btn.addEventListener('click', function() {
        const orderId = this.dataset.orderId;
        const productId = this.dataset.productId;
        const invoiceId = this.dataset.invoiceId;
        const userId = this.dataset.userId;
        const productName = this.dataset.productName;
        const invoiceNo = this.dataset.invoiceNo;
        
        const container = document.getElementById('order-container-' + orderId);
        const inputs = container.querySelectorAll('.batch-input');
        
        let qtyNeed = parseInt(container.querySelector('.qty-order-need').dataset.need);
        let totalInput = 0;
        let batchSelections = [];
        let hasInvalid = false;
        
        inputs.forEach(inp => {
            let val = parseInt(inp.value) || 0;
            let max = parseInt(inp.dataset.max);
            if (val > max) hasInvalid = true;
            if (val > 0) {
                totalInput += val;
                batchSelections.push({
                    batch_id: inp.dataset.batchId,
                    batch_no: inp.dataset.batchNo,
                    qty: val
                });
            }
        });
        
        if (hasInvalid) {
            alert('Terdapat input Qty yang melebihi batas stok batch!');
            return;
        }
        
        if (totalInput <= 0) {
            alert('Masukkan Qty pada batch yang ingin diambil minimal 1.');
            return;
        }
        
        let alreadyAddedQty = 0;
        items.forEach(item => {
            if(item.order_id === orderId) {
                alreadyAddedQty += item.quantity;
            }
        });
        
        if ((totalInput + alreadyAddedQty) > qtyNeed) {
            alert(`Total Qty Kirim (${totalInput + alreadyAddedQty}) melebihi sisa Qty Order (${qtyNeed})! Hapus item di draft jika ingin mengubah.`);
            return;
        }
        
        let existingItemIndex = items.findIndex(item => item.order_id === orderId);

        if (existingItemIndex !== -1) {
            items[existingItemIndex].quantity += totalInput;
            
            batchSelections.forEach(selection => {
                let existingBatch = items[existingItemIndex].batches.find(b => b.batch_id === selection.batch_id);
                if (existingBatch) {
                    existingBatch.qty += selection.qty;
                } else {
                    items[existingItemIndex].batches.push(selection);
                }
            });
        } else {
            items.push({
                order_id: orderId,
                product_id: productId,
                invoice_id: invoiceId,
                user_id: userId,
                name: productName,
                invoice_no: invoiceNo,
                quantity: totalInput,
                batches: batchSelections
            });
        }
        
        // Kosongkan input setelah ditambah
        inputs.forEach(inp => inp.value = '');
        
        renderTable();
    });
});

function renderTable(){
    const tbody = document.querySelector('#deliveryTable tbody');
    tbody.innerHTML = '';
    
    if (items.length === 0) {
        tbody.innerHTML = `<tr id="empty-row"><td colspan="5" class="text-center text-muted">Belum ada item yang ditambahkan</td></tr>`;
    } else {
        items.forEach((item, index) => {
            // Format daftar batch untuk ditampilkan di tabel UI
            let batchDetails = item.batches ? item.batches.map(b => `${b.batch_no} (${b.qty} pcs)`).join('<br>') : '-';
            
            tbody.innerHTML += `
                <tr>
                    <td>${item.invoice_no}</td>
                    <td class="fw-bold">${item.name}</td>
                    <td>${batchDetails}</td>
                    <td class="text-center fw-bold text-primary">${item.quantity}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${index})">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </td>
                </tr>`;
        });
    }
    
    document.getElementById('itemsInput').value = JSON.stringify(items);
}

function removeItem(index) {
    items.splice(index, 1);
    renderTable();
}

// Validasi Form Submit
document.getElementById('deliveryForm').addEventListener('submit', function(e) {
    if (items.length === 0) {
        e.preventDefault();
        alert('Daftar Pengiriman masih kosong! Tambahkan item terlebih dahulu.');
    }
});
</script>
@endsection