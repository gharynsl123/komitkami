@extends('layouts.app')

@section('title', 'Komitkami Helper | Download Spesifikasi')

@section('content')
    <style>
        .specification-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
        }

        .specification-card .card-body {
            padding: 24px;
        }

        .datatable-title {
            margin: 0;
            padding: 0;
            font-size: 18px;
            font-weight: 600;
            color: #344767;
            line-height: 1.5;
        }

        .dt-layout-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .dt-layout-row:first-child {
            margin-bottom: 18px;
        }

        .dt-layout-row:last-child {
            margin-top: 18px;
        }

        .dt-search {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dt-search label {
            margin: 0;
            color: #7b809a;
            font-size: 15px;
            font-weight: 500;
        }

        .dt-search input {
            width: 230px !important;
            height: 30px;
            padding: 4px 8px !important;
            border: 1px solid #ced4da;
            border-radius: 6px;
            background-color: #fff;
            color: #344767;
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        .dt-search input:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.08);
        }

        .datatable {
            width: 100% !important;
            margin-top: 0 !important;
            border-collapse: collapse !important;
        }

        .datatable thead th {
            background-color: #fff;
            color: #67748e;
            font-size: 14px;
            font-weight: 600;
            padding: 15px 16px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .datatable tbody td {
            color: #67748e;
            font-size: 14px;
            padding: 15px 16px;
            vertical-align: middle;
        }

        .datatable tbody tr {
            transition: background-color 0.15s ease;
        }

        .datatable tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.015);
        }

        #productTable th:first-child,
        #productTable td:first-child,
        #inventoryTable th:first-child,
        #inventoryTable td:first-child {
            width: 70px;
        }

        #productTable th:last-child,
        #productTable td:last-child,
        #inventoryTable th:last-child,
        #inventoryTable td:last-child {
            width: 180px;
        }

        .btn-download {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 170px;
            padding: 9px 18px;
            border: none;
            border-radius: 8px;
            background-color: #ed1760;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(237, 23, 96, 0.20);
            transition: all 0.2s ease;
        }

        .btn-download:hover {
            background-color: #d91456;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 5px 10px rgba(237, 23, 96, 0.25);
        }

        .btn-download:active {
            transform: translateY(0);
        }

        .dt-info {
            color: #444;
            font-size: 14px;
        }

        .dt-paging {
            margin: 0;
        }

        .dt-paging .pagination {
            margin: 0;
        }

        .dt-paging .page-link {
            color: #333;
            border: none;
            min-width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 14px;
        }

        .dt-paging .page-link:hover {
            background-color: #f4f4f4;
            color: #333;
        }

        .dt-paging .page-item.active .page-link {
            background-color: #fff;
            color: #333;
            border: 1px solid #aaa;
        }

        .dt-paging .page-item.disabled .page-link {
            color: #999;
            background-color: transparent;
        }

        table.dataTable thead th {
            cursor: pointer;
        }

        #pills-tab {
            margin-bottom: 0;
        }

        @media (max-width: 768px) {

            .specification-card .card-body {
                padding: 15px;
            }

            .dt-layout-row {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }

            .dt-search {
                justify-content: space-between;
            }

            .dt-search input {
                width: 200px !important;
            }

            .dt-layout-row:last-child {
                flex-direction: column;
                align-items: flex-start;
            }

            .datatable thead th,
            .datatable tbody td {
                padding: 12px;
                font-size: 13px;
            }

            .btn-download {
                min-width: 150px;
                font-size: 11px;
                padding: 8px 12px;
            }
        }
    </style>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.3/css/dataTables.bootstrap5.min.css" >
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="nav-wrapper position-relative end-0">
                <ul class="nav nav-pills nav-fill p-1" id="pills-tab" role="tablist">
                    {{-- PRODUCT TAB --}}
                    <li class="nav-item" role="presentation">
                        <a class="nav-link mb-0 px-0 py-1 active" id="pills-product-tab" data-bs-toggle="tab" href="#pills-product" role="tab" 
                            aria-controls="pills-product" aria-selected="true" 
                        >
                            <span class="ms-1">Product</span>
                        </a>
                    </li>

                    {{-- INVENTORY TAB --}}
                    <li class="nav-item" role="presentation">
                        <a class="nav-link mb-0 px-0 py-1" id="pills-inventory-tab" data-bs-toggle="tab" href="#pills-inventory" role="tab" 
                            aria-controls="pills-inventory" aria-selected="false" 
                        >
                            <span class="ms-1">Inventory</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="tab-content" id="pills-tabContent" >
        <div class="tab-pane fade show active" id="pills-product" role="tabpanel" aria-labelledby="pills-product-tab">
            <div class="card specification-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered datatable" id="productTable" >
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Nama Formula</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $index => $p)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $p->name }}</td>
                                        <td>{{ $p->matched_formula->nama_formula }}</td>
                                        <td>
                                            <a href="{{ route('download.spesifikasi.product', $p->id) }}" class="btn-download" target="_blank" >
                                                Download Spesifikasi
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
        <div class="tab-pane fade" id="pills-inventory" role="tabpanel" aria-labelledby="pills-inventory-tab">
            <div class="card specification-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered datatable" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang (Inventory)</th>
                                    <th>Manufacturer</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach($inventories as $inv)
                                    @foreach($inv->manufacturers->unique('id') as $manuf)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $inv->name }}</td>
                                            <td>{{ $manuf->name }}</td>
                                            <td>
                                                <a href="{{ route('download.spesifikasi.inventory', [ 'inventory_id' => $inv->id, 'manufacturer_id' => $manuf->id ]) }}" class="btn-download" target="_blank" >
                                                    Download Spesifikasi
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.datatables.net/2.3.3/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.3/js/dataTables.bootstrap5.min.js"></script>
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            const productTable = new DataTable('#productTable', {
                pageLength: 6,
                ordering: false,
                searching: true,
                info: true,
                language: {
                    search: 'Search:',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    zeroRecords: 'No matching records found',
                    emptyTable: 'No data available in table',
                    paginate: {
                        previous: 'Previous',
                        next: 'Next'
                    }
                },
                layout: {
                    topStart: function () {
                        const title = document.createElement('h5');
                        title.className = 'datatable-title';
                        title.textContent = 'Download Spesifikasi Product';
                        return title;
                    },
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                columnDefs: [
                    {
                        targets: 3,
                        orderable: false
                    }
                ]
            });

            const inventoryTable = new DataTable('#inventoryTable', {
                pageLength: 6,
                ordering: false,
                searching: true,
                info: true,
                language: {
                    search: 'Search:',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    zeroRecords: 'No matching records found',
                    emptyTable: 'No data available in table',
                    paginate: {
                        previous: 'Previous',
                        next: 'Next'
                    }
                },
                layout: {
                    topStart: function () {
                        const title = document.createElement('h5');
                        title.className = 'datatable-title';
                        title.textContent = 'Download Spesifikasi Inventory';
                        return title;
                    },
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                columnDefs: [
                    {
                        targets: 3,
                        orderable: false
                    }
                ]
            });
            const inventoryTab = document.getElementById('pills-inventory-tab');
            const productTab = document.getElementById('pills-product-tab');

            if (inventoryTab) {
                inventoryTab.addEventListener(
                    'shown.bs.tab',
                    function () {
                        inventoryTable.columns.adjust();
                    }
                );
            }
            if (productTab) {
                productTab.addEventListener(
                    'shown.bs.tab',
                    function () {
                        productTable.columns.adjust();
                    }
                );
            }
        });
    </script>
@endsection