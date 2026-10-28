@extends('layouts.app')
@section('title', 'Komitkami Helper | Draft Pengiriman')
@section('content')
    <style>
        p {
            margin: 0 !important;
        }
    </style>
    <div class="pb-0 px-3">
        <div class="row mt-3 align-items-center">
            <div class="col-md-4 col-12 mb-3 mb-md-0 d-flex align-items-center">
                <h6 class="mb-0">Draft Pengiriman</h6>
            </div>
            <div class="col-md-4 col-12 mb-3 mb-md-0">
                <div class="input-group input-group-outline">
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari PO/SJ/Customer...">
                </div>
            </div>
            @if(Auth::user()->hasAnyRole(['Admin', 'Sales', 'Production Manager',  'Inventory Manager']))
                <div class="col-md-4 col-12 text-md-end text-start">
                    <div class="d-flex justify-content-md-end flex-wrap gap-2">
                        <a class="btn btn-outline-primary btn-sm mb-0" href="{{ route('manual-invoice.create') }}">
                            <i class="material-icons text-sm">add</i>&nbsp;Buat Invoice Manual
                        </a>
                        <a class="btn bg-gradient-dark btn-sm mb-0" href="{{url('/buat-devilery')}}">
                            <i class="material-icons text-sm">add</i>&nbsp;Tambahkan Draft
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Nav Pills -->
        <div class="nav-wrapper position-relative mt-4">
            <ul class="nav nav-pills nav-fill p-1" id="deliveryTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link mb-0 px-4 py-1 active" id="draft-tab" data-bs-toggle="pill"
                        data-bs-target="#draft-content" type="button" role="tab">
                        <i class="material-icons text-lg position-relative">local_shipping</i>
                        <span class="ms-1 fw-bold">Draft Pengiriman</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link mb-0 px-4 py-1" id="manual-tab" data-bs-toggle="pill"
                        data-bs-target="#manual-content" type="button" role="tab">
                        <i class="material-icons text-lg position-relative">receipt_long</i>
                        <span class="ms-1 fw-bold">Invoice Manual</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="pt-4 px-3 tab-content" id="deliveryTabContent">

        @if(session('success'))
            <div class="alert alert-success text-white">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger text-white">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tab Draft Pengiriman -->
        <div class="tab-pane fade show active" id="draft-content" role="tabpanel">
            <div id="draft-list">
                @forelse($deliveries as $delivery)

                    <div class="card shadow-sm mb-4 border-0 rounded-3 searchable-card"
                        data-search="{{ strtolower($delivery->invoice->nomor_invoice ?? '') }} {{ strtolower($delivery->no_sj ?? '') }} {{ strtolower($delivery->items->first()->order->user->name ?? '') }}">
                        <div class="card-body">

                            {{-- Header --}}
                            <div
                                class="d-flex justify-content-between align-items-start flex-wrap gap-3 border-bottom pb-3 mb-3">

                                <div class="row w-100 gy-2 gx-4">
                                    <div class="col-md-3 col-6">
                                        <small class="text-muted">Nomor PO</small>
                                        <div class="fw-semibold">{{ $delivery->invoice->nomor_invoice ?? '-' }}</div>
                                    </div>

                                    <div class="col-md-3 col-6">
                                        <small class="text-muted">Tanggal Terbit</small>
                                        <div class="fw-semibold">
                                            {{ $delivery->tanggal_terbit ? \Carbon\Carbon::parse($delivery->tanggal_terbit)->format('d/m/Y') : "-" }}
                                        </div>
                                    </div>

                                    @if(Auth::user()->hasAnyRole(['Production Manager', 'Sales', 'Admin', 'Inventory Manager']))
                                        <div class="col-md-3 col-6">
                                            <small class="text-muted">Nomor Surat Jalan</small>
                                            <div class="fw-semibold">{{ $delivery->no_sj }}</div>
                                        </div>

                                        <div class="col-md-3 col-6">
                                            <small class="text-muted">Nama Customer</small>
                                            <div class="fw-semibold">
                                                {{ $delivery->items->first()->order->user->name ?? '-' }}
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-3 col-6">
                                        <small class="text-muted">Status</small>
                                        <div>
                                            @if($delivery->status == 'delivered')
                                                <span class="badge bg-success">Terkirim</span>
                                            @elseif($delivery->status == 'in_progress')
                                                <span class="badge bg-info">Dalam Perjalanan</span>
                                            @elseif($delivery->status == 'cancelled')
                                                <span class="badge bg-danger">Dibatalkan</span>
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 flex-wrap w-100">
                                    <a href="{{ url('/lihat-surat', $delivery->slug) }}" class="btn btn-primary btn-sm m-0">
                                        Lihat Surat
                                    </a>

                                    @if($delivery->status == 'delivered' || $delivery->photo_surat_jalan)
                                        <a href="{{ url('/print-invoice-sales/' . $delivery->slug) }}"
                                            class="btn btn-info btn-sm m-0" target="_blank">
                                            Invoice
                                        </a>
                                    @else
                                        <button class="btn btn-secondary btn-sm m-0" disabled
                                            title="Konfirmasi pengiriman terlebih dahulu">Invoice</button>
                                    @endif

                                    <a href="{{ url('/buat-invoice', $delivery->slug) }}" class="btn btn-warning btn-sm m-0" title="Edit Pengiriman">
                                        <i class="material-icons text-sm">edit</i>
                                    </a>

                                    @if(Auth::user()->hasAnyRole(['Production Manager', 'Sales', 'Admin']))
                                        <form action="{{ route('delivery.destroy', $delivery->id) }}" method="POST" class="d-inline delete-delivery-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-outline-danger btn-sm m-0 btn-delete-delivery"
                                                data-no-sj="{{ $delivery->no_sj }}"
                                                title="Hapus Seluruh Pengiriman & Kembalikan Stok">
                                                <i class="material-icons text-sm">delete</i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            {{-- Order list --}}
                            <div class="table-responsive">
                                <table class="table table-borderless border border-0 align-middle mb-0">
                                    <thead class="text-muted small">
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-center">Qty Dikirim</th>
                                            <th class="text-end">Harga x Qty</th>
                                            @if(Auth::user()->hasAnyRole(['Production Manager', 'Sales', 'Admin']))
                                                <th class="text-center" style="width: 80px;">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($delivery->items as $item)

                                            @php
                                                $quantity = intval($item->jumlah_barang);
                                                $price = $item->order->product->price ?? 0;
                                                $totalPrice = $quantity * $price;
                                            @endphp

                                            <tr>
                                                <td class="fw-semibold">
                                                    <div>{{ $item->order->product->name ?? '-' }}</div>
                                                    @if($item->note)
                                                        <div class="text-muted mt-1 font-monospace" style="font-size: 0.72rem; white-space: pre-line; line-height: 1.25;">
                                                            {{ $item->note }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark px-3 py-2">
                                                        {{ $quantity }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-bold text-primary">
                                                    @currency($totalPrice)
                                                </td>
                                                @if(Auth::user()->hasAnyRole(['Production Manager', 'Sales', 'Admin']))
                                                    <td class="text-center">
                                                        <form action="{{ route('delivery.destroy_item', $item->id) }}" method="POST" class="d-inline delete-item-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2 m-0 btn-delete-item"
                                                                data-item-name="{{ $item->order->product->name ?? 'Produk' }}"
                                                                data-qty="{{ $quantity }}"
                                                                data-batches="{{ $item->note ?? '' }}"
                                                                title="Hapus Item & Kembalikan Stok">
                                                                <i class="material-icons text-sm align-middle">delete</i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>

                                        @endforeach
                                    </tbody>
                                    <tfoot class="border-top">
                                        <tr>
                                            @php
                                                $deliveryTotal = 0;
                                                foreach($delivery->items as $item) {
                                                    $quantity = intval($item->jumlah_barang);
                                                    $price = $item->order->product->price ?? 0;
                                                    $deliveryTotal += $quantity * $price;
                                                }
                                            @endphp
                                            <td colspan="2" class="text-end fw-bold pt-3">Grand Total</td>
                                            <td class="text-end fw-bold text-primary fs-6 pt-3">
                                                @currency($deliveryTotal)
                                            </td>
                                            @if(Auth::user()->hasAnyRole(['Production Manager', 'Sales', 'Admin']))
                                                <td></td>
                                            @endif
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>

                @empty
                    <div class="card text-center p-4 shadow-sm">
                        <p class="mb-0 text-muted">Tidak ada delivery yang ditemukan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab Invoice Manual -->
        <div class="tab-pane fade" id="manual-content" role="tabpanel">
            <div id="manual-list">

                @forelse($manualInvoices as $manualInvoice)
                    <div class="card shadow-sm mb-4 border-0 rounded-3 border-start border-primary border-4 searchable-card"
                        data-search="{{ strtolower($manualInvoice->no_invoice ?? '') }} {{ strtolower($manualInvoice->manualItems->first()->user->name ?? '') }}">
                        <div class="card-body">
                            <div
                                class="d-flex justify-content-between align-items-start flex-wrap gap-3 border-bottom pb-3 mb-3">
                                <div class="row w-100 gy-2 gx-4">
                                    <div class="col-md-3 col-6">
                                        <small class="text-muted">Nomor Invoice</small>
                                        <div class="fw-semibold">{{ $manualInvoice->no_invoice }}</div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <small class="text-muted">Tanggal Dibuat</small>
                                        <div class="fw-semibold">
                                            {{ $manualInvoice->tanggal_terbit ? \Carbon\Carbon::parse($manualInvoice->tanggal_terbit)->format('d/m/Y') : 'Tanggal tidak ditemukan' }}
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <small class="text-muted">Nama Customer</small>
                                        <div class="fw-semibold">
                                            {{ $manualInvoice->manualItems->first()->user->name ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <small class="text-muted">Status</small>
                                        <div>
                                            @if($manualInvoice->status == 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-warning">Unpaid</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap w-100">
                                    <a href="{{ route('manual-invoice.edit', $manualInvoice->id) }}" class="btn btn-warning btn-sm m-0 d-flex align-items-center gap-1">
                                        <i class="material-icons text-sm m-0">edit</i> Edit
                                     </a>
                                    <a href="{{ route('manual-invoice.print', $manualInvoice->id) }}"
                                        class="btn btn-info btn-sm m-0 d-flex align-items-center gap-1 text-nowrap" target="_blank">
                                        <i class="material-icons text-sm m-0">print</i> Cetak Invoice
                                    </a>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-borderless border border-0 align-middle mb-0">
                                    <thead class="text-muted small">
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Harga Satuan</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($manualInvoice->manualItems as $item)
                                            <tr>
                                                <td class="fw-semibold">
                                                    {{ $item->product->name ?? '-' }}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark px-3 py-2">
                                                        {{ $item->quantity }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    @currency($item->price_per_item)
                                                </td>
                                                <td class="text-end fw-bold text-primary">
                                                    @currency($item->total_price)
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="border-top">
                                        <tr>
                                            @php
                                                $manualTotal = 0;
                                                foreach($manualInvoice->manualItems as $item) {
                                                    $manualTotal += $item->total_price;
                                                }
                                            @endphp
                                            <td colspan="3" class="text-end fw-bold pt-3">Grand Total</td>
                                            <td class="text-end fw-bold text-primary fs-6 pt-3">
                                                @currency($manualTotal)
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card text-center p-4 shadow-sm">
                        <p class="mb-0 text-muted">Tidak ada invoice manual yang ditemukan.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();
                    const cards = document.querySelectorAll('.searchable-card');

                    cards.forEach(card => {
                        const searchData = card.getAttribute('data-search') || '';
                        if (searchData.includes(query)) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }

            // Konfirmasi Hapus Item Delivery & Kembalikan Stok
            document.querySelectorAll('.btn-delete-item').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    const itemName = this.getAttribute('data-item-name');
                    const qty = this.getAttribute('data-qty');
                    const batches = this.getAttribute('data-batches');

                    let batchDetails = '';
                    if (batches && batches.trim() !== '') {
                        const formattedBatches = batches.trim().replace(/\n/g, '<br>');
                        batchDetails = `
                            <div class="mt-3 p-2 text-start rounded border bg-light small" style="font-family: monospace; font-size: 0.78rem; line-height: 1.4;">
                                <strong class="text-dark">Batch yang akan dikembalikan:</strong><br>
                                <span class="text-secondary">${formattedBatches}</span>
                            </div>
                        `;
                    }

                    Swal.fire({
                        title: 'Hapus Item Delivery?',
                        html: `
                            <p class="mb-2">Apakah Anda yakin ingin menghapus <strong>${itemName}</strong> (${qty} pcs) dari draft pengiriman ini?</p>
                            <div class="alert alert-warning text-white p-2 mb-0 small text-start">
                                <i class="material-icons text-sm align-middle me-1">restore</i>
                                Stok batch yang terambil akan <strong>otomatis dikembalikan ke gudang</strong>.
                            </div>
                            ${batchDetails}
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e91e63',
                        cancelButtonColor: '#7b809a',
                        confirmButtonText: 'Ya, Hapus & Kembalikan Stok',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Konfirmasi Hapus Seluruh Draft Pengiriman
            document.querySelectorAll('.btn-delete-delivery').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    const noSj = this.getAttribute('data-no-sj');

                    Swal.fire({
                        title: 'Hapus Draft Pengiriman?',
                        html: `
                            <p class="mb-2">Apakah Anda yakin ingin menghapus seluruh draft pengiriman <strong>${noSj}</strong>?</p>
                            <div class="alert alert-danger text-white p-2 mb-0 small text-start">
                                <i class="material-icons text-sm align-middle me-1">warning</i>
                                Semua item pengiriman akan dihapus dan <strong>seluruh stok batch yang terambil akan dikembalikan</strong> ke gudang.
                            </div>
                        `,
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#d32f2f',
                        cancelButtonColor: '#7b809a',
                        confirmButtonText: 'Ya, Hapus Pengiriman',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
