 @extends('layouts.app')
 @section('title', 'Buat Tiket Produksi')
 @section('content')
 <div class="d-flex align-items-center">
     <a href="{{url()->previous()}}" class="btn m-0 btn-secondary">Kembali</a>
     <h6 class="mx-4 my-0"> Buat tiket</h6>
 </div>


 <div class="col-lg-6 col-md-6 my-3">
     <div class="nav-wrapper position-relative end-0">
         <ul class="nav nav-pills nav-fill p-1" role="tablist">
             @foreach ([
             'pembuatan-produksi-po' => 'Buat Tiket Perdasarkan PO',
             'pembuatan-produksi-khusus' => 'Pembuatan Tiket Khusus',
             ] as $id => $name)
             <li class="nav-item" role="presentation">
                 <a class="nav-link mb-0 px-0 py-1 {{ $loop->first ? 'active' : '' }}" id="{{ $id }}-tab"
                     data-bs-toggle="pill" data-bs-target="#{{ $id }}" role="tab" aria-controls="{{ $id }}"
                     aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                     <span class="ms-1">{{ $name }}</span>
                 </a>
             </li>
             @endforeach
         </ul>
     </div>
 </div>

 <div class="tab-content" id="myTabContent">
     <div class="tab-pane fade show active" id="pembuatan-produksi-po" role="tabpanel"
         aria-labelledby="pembuatan-produksi-po-tab">
         {{-- Konten untuk "Buat Tiket Perdasarkan PO" --}}
         <form action="{{url('/store-data-tiket')}}" id="form-po" method="POST">
             @csrf
             <div class="card p-3 shadow">
                 <div class="row">
                     <div class="col-md-6 mb-3">
                         <div class="input-group-outline input-group">
                             <select name="invoice_id" class="form-control" id="invoice-select">
                                 <option value="">-- pilih PO --</option>
                                 @foreach($invoice as $row)
                                 <option value="{{$row->id}}">{{$row->nomor_invoice}}</option>
                                 @endforeach
                             </select>
                         </div>
                     </div>
                     <div class="col-md-6 mb-3">
                         <div class="input-group-outline input-group">
                             <select name="product_id" class="form-control" id="product-select">
                                 <option value="">-- pilih produk --</option>
                                 <!-- Produk yang dipesan akan muncul di sini -->
                             </select>
                         </div>
                     </div>
                     <div class="col-md-6 mb-3 d-none" id="tiket-quantity">
                         <div class="input-group-outline input-group">
                             <input type="text" name="jumlah_tiket" placeholder="Jumlah Tiket" class="form-control">
                         </div>
                         <div class="form-check m-0 p-0">
                             <input class="form-check-input" type="checkbox" name="confirm_selection"
                                 id="confirm-selection">
                             <label class="form-check-label" for="confirm-selection">
                                 Buat tiket untuk semua jumlah produk yang dipesan
                             </label>
                         </div>
                     </div>


                     <hr class="border border-1">

                     <div class="warp-tikects ticket-container">
                         <!-- Tiket akan di-generate di sini -->
                     </div>
                 </div>
                 <div class="row">
                     <div class="col-md-6">
                         <h4>Persediaan Bahan Kemas</h4>
                         <div class="d-none mb-3" id="formula-select-container-kemas">
                             <select id="template-kemas-select" class="form-control border-1 px-2 py-1 border">
                                 <option value="">-- pilih formula --</option>
                             </select>
                             <input type="hidden" id="selected-kemasan-template" name="formula_bahan_kemas_id">
                         </div>
                         <div class="table-responsive">
                             <table class="table table-bordered">
                                 <thead>
                                     <tr>
                                         <th>Nama</th>
                                         <th>Dibutuhkan</th>
                                         <th>Stok Tersedia</th>
                                         <th>Status</th>
                                     </tr>
                                 </thead>
                                 <tbody class="formula-bahan-kemas">
                                     <!-- Data formula akan dimasukkan di sini oleh JavaScript -->
                                 </tbody>
                             </table>
                         </div>
                     </div>
                     <div class="col-md-6">
                         <h4>Formula Bahan Baku</h4>
                         <div class="d-none mb-3" id="formula-select-container">
                             <select id="template-select" class="form-control border-1 px-2 py-1 border">
                                 <option value="">-- pilih formula --</option>
                             </select>
                             <input type="hidden" id="selected-template" name="formula_bahan_baku_id">
                         </div>
                         <div class="table-responsive">
                             <table class="table table-bordered">
                                 <thead>
                                     <tr>
                                         <th>Nama</th>
                                         <th>Dibutuhkan</th>
                                         <th>Stok Tersedia</th>
                                         <th>Status</th>
                                     </tr>
                                 </thead>
                                 <tbody class="formula-bahan-baku">
                                     <!-- Data formula akan dimasukkan di sini oleh JavaScript -->
                                 </tbody>
                             </table>
                         </div>
                     </div>
                 </div>
                 <div class="mt-4">
                     <button type="submit" class="btn btn-primary">Simpan Tiket</button>
                 </div>
             </div>
         </form>
     </div>

     <div class="tab-pane fade" id="pembuatan-produksi-khusus" role="tabpanel"
         aria-labelledby="pembuatan-produksi-khusus-tab">
         {{-- Konten untuk "Pembuatan Tiket Khusus" --}}
         <form action="{{url('/store-data-tiket-khusus')}}" id="form-khusus" method="POST">
             @csrf
             <div class="card p-3 shadow">
                 <div class="row">
                     <div class="col-md-6 mb-3">
                         <select name="product_id" class="form-select" id="select-product-tiket-khusus">
                             <option value="">-- pilih produk --</option>
                             @foreach($product as $row)
                             @php
                             $productJson = json_encode([
                             "product_id" => $row->id,
                             "product_name" => $row->name,
                             "brand_name" => $row->brand->name ?? '',
                             "hasil_satu_batch" => $row->produkjadi->acuan ?? 1,
                             "ukuran_batch" => $row->produkjadi->size_batch ?? 100,
                             "code_product" => $row->code,
                             ]);
                             @endphp
                             <option value="{{ $productJson }}">{{ $row->name }}</option>
                             @endforeach
                             <!-- Produk yang dipesan akan muncul di sini -->
                         </select>
                     </div>
                     <div class="col-md-6 mb-3" id="tiket-quantity">
                         <div class="input-group-outline input-group">
                             <input type="text" name="jumlah_tiket_khusus" placeholder="Jumlah Tiket"
                                 class="form-control">
                         </div>
                     </div>
                     <div class="col-md-6 mb-3">
                         <div class="input-group-outline input-group">
                             <input type="text" name="keterangan" placeholder="Keteragan/Kebutuhan"
                                 class="form-control">
                         </div>
                     </div>
                     <div class="col-md-6 mb-3">
                         <div class="form-check mb-3 m-0 p-0">
                             <input class="form-check-input" type="checkbox" value="1" id="isCustomFormula"
                                 name="is_custom_formula">
                             <label class="form-check-label" for="isCustomFormula">
                                 Gunakan Formula Khusus
                             </label>
                         </div>
                     </div>

                     <hr class="border border-1">

                     <div class="warp-tikects ticket-container">
                         <!-- Tiket akan di-generate di sini -->
                     </div>
                 </div>

                 <div id="customFormulaSection" style="display: none;">
                     <p class="text-muted">Silakan isi formula manual di bawah ini</p>

                     <div id="customKemasContainer" class="container">
                         <!-- Dinamis, akan ditambah lewat JS -->
                     </div>

                     <button type="button" class="btn btn-sm btn-success text-end" onclick="addCustomKemasRow()">+
                         Tambah Bahan Kemas</button>
                 </div>

                 <div class="row" id="formulaDefault">
                     <div class="col-md-6">
                         <h4>Persediaan Bahan Kemas</h4>
                         <div class="d-none mb-3" id="formula-select-container-kemas">
                             <select id="template-kemas-select" class="form-control border-1 px-2 py-1 border">
                                 <option value="">-- pilih formula --</option>
                             </select>
                             <input type="hidden" id="selected-kemasan-template" name="formula_bahan_kemas_id">
                         </div>
                         <div class="table-responsive">
                             <table class="table table-bordered">
                                 <thead>
                                     <tr>
                                         <th>Nama</th>
                                         <th>Dibutuhkan</th>
                                         <th>Stok Tersedia</th>
                                         <th>Status</th>
                                     </tr>
                                 </thead>
                                 <tbody class="formula-bahan-kemas">
                                     <!-- Data formula akan dimasukkan di sini oleh JavaScript -->
                                 </tbody>
                             </table>
                         </div>
                     </div>
                     <div class="col-md-6">
                         <h4>Formula Bahan Baku</h4>
                         <div class="d-none mb-3" id="formula-select-container">
                             <select id="template-select" class="form-control border-1 px-2 py-1 border">
                                 <option value="">-- pilih formula --</option>
                             </select>
                             <input type="hidden" id="selected-template" name="formula_bahan_baku_id">
                         </div>
                         <div class="table-responsive">
                             <table class="table table-bordered">
                                 <thead>
                                     <tr>
                                         <th>Nama</th>
                                         <th>Dibutuhkan</th>
                                         <th>Stok Tersedia</th>
                                         <th>Status</th>
                                     </tr>
                                 </thead>
                                 <tbody class="formula-bahan-baku">
                                     <!-- Data formula akan dimasukkan di sini oleh JavaScript -->
                                 </tbody>
                             </table>
                         </div>
                     </div>
                 </div>
                 <div class="mt-4">
                     <button type="submit" class="btn btn-primary">Simpan Tiket</button>
                 </div>
             </div>
         </form>
     </div>
 </div>

 <!-- Di luar body atau di dalam script -->
 <template id="kemas-row-template">
     <div class="kemas-row mb-2 border pt-2 rounded row">
         <div class="col-md-5">
             <select name="custom_formula_kemas[inventory_id][]" class="border-1 px-2 py-1 border form-control"
                 required>
                 @foreach($inventory->where('type', '!=', 'Bahan Baku') as $inv)
                 <option value="{{ $inv->id }}">{{ $inv->name }} ({{$inv->code}})</option>
                 @endforeach
             </select>

         </div>
         <div class="col-md-5">
             <input type="number" step="0.01" name="custom_formula_kemas[jumlah][]" placeholder="Jumlah Yang butuhkan"
                 class="form-control  border-1 px-2 py-1 border mb-1" required>
         </div>
         <div class="col-md-2 text-end">
             <button type="button" class="btn btn-sm btn-danger"
                 onclick="this.closest('.kemas-row').remove()">Hapus</button>
         </div>
     </div>
 </template>


 <script>
let originalFormulaDefaultHTML = '';

function addCustomKemasRow() {
    const container = document.getElementById('customKemasContainer');
    const template = document.getElementById('kemas-row-template');
    const clone = template.content.cloneNode(true);
    container.appendChild(clone);
}

document.addEventListener('DOMContentLoaded', function() {
    const invoiceSelect = document.getElementById('invoice-select');
    const productSelect = document.getElementById('product-select');
    const productKhususSelect = document.getElementById('select-product-tiket-khusus');
    const tiketQuantityContainer = document.getElementById('tiket-quantity');
    const tiketQuantityKhususContainer = document.getElementById('tiket-quantity-khusus');
    const formulaDefault = document.getElementById('formulaDefault');

    if (formulaDefault) {
        originalFormulaDefaultHTML = formulaDefault.innerHTML;
    }

    const isCustomFormula = document.getElementById('isCustomFormula');

    isCustomFormula.addEventListener('change', function() {
        const section = document.getElementById('customFormulaSection');
        const productId = $('#select-product-tiket-khusus').val();

        if (this.checked) {
            section.style.display = 'block';
            formulaDefault.classList.add('d-none');

            if (productId) {
                clearDefaultFormulaInputs();
            }

        } else {
            section.style.display = 'none';
            formulaDefault.classList.remove('d-none');
            formulaDefault.innerHTML = originalFormulaDefaultHTML;

            if (productId && !this.checked) {
                const product = JSON.parse(productId);
                const inputJumlahTiket = document.querySelector('input[name="jumlah_tiket_khusus"]');
                const currentForm = document.getElementById('form-khusus');

                inputJumlahTiket.addEventListener('input', () => {
                    const jumlah = parseInt(inputJumlahTiket.value);
                    if (!isNaN(jumlah)) {
                        const updatedProducts = [{
                            ...product,
                            jumlah_tiket: jumlah
                        }];
                        getLastBatchNumberAndGenerateTickets(updatedProducts, jumlah,
                            currentForm);
                    }
                });
            }

        }
    });

    $('#invoice-select').select2({
        theme: 'bootstrap-5',
        placeholder: '-- pilih PO --',
        width: '100%'
    });

    $('#invoice-select').on('change', function() {
        const invoiceId = $(this).val();

        if (invoiceId) {
            fetch(`/get-products/${invoiceId}`)
                .then(response => response.json())
                .then(products => {
                    // Pastikan productSelect sudah dideklarasikan
                    const productSelect = document.getElementById('product-select');
                    productSelect.innerHTML = '<option value="">-- pilih produk --</option>';

                    products.forEach(product => {
                        const option = document.createElement('option');
                        option.value = JSON.stringify(product);
                        option.textContent =
                            `${product.product_name} - ${product.brand_name} (${product.jumlah_tersisa} pcs)`;
                        productSelect.appendChild(option);
                    });
                });
        } else {
            const productSelect = document.getElementById('product-select');
            productSelect.innerHTML = '<option value="">-- pilih produk --</option>';
        }
    });

    productSelect.addEventListener('change', function() {
        const selectedProduct = this.value;
        const invoiceId = invoiceSelect.value;

        console.log(selectedProduct);

        if (selectedProduct && invoiceId) {
            const product = JSON.parse(selectedProduct);
            const inputJumlahTiket = document.querySelector('input[name="jumlah_tiket"]');
            const checkboxSemua = document.querySelector('input[name="confirm_selection"]');
            tiketQuantityContainer.classList.remove('d-none');

            inputJumlahTiket.addEventListener('input', () => {
                const jumlah = parseInt(inputJumlahTiket.value);
                if (!isNaN(jumlah)) {
                    const updatedProducts = [{
                        ...product,
                        jumlah_tiket: jumlah
                    }];
                    const currentForm = document.getElementById('form-po');
                    getLastBatchNumberAndGenerateTickets(updatedProducts, jumlah, currentForm);
                }
            });

            checkboxSemua.addEventListener('change', () => {
                if (checkboxSemua.checked) {
                    const jumlah = product.jumlah_tiket;
                    inputJumlahTiket.value = jumlah;
                    const updatedProducts = [{
                        ...product,
                        jumlah_tiket: jumlah
                    }];
                    const currentForm = document.getElementById('form-po');
                    getLastBatchNumberAndGenerateTickets(updatedProducts, jumlah, currentForm);
                }
            });
        }
    });

    $('#select-product-tiket-khusus').on('select2:select', function(e) {
        const selectedProduct = e.params.data.element.value;
        console.log(selectedProduct);
        console.log(isCustomFormula.checked);
        if (selectedProduct && !isCustomFormula.checked) {
            const product = JSON.parse(selectedProduct);
            const inputJumlahTiket = document.querySelector('input[name="jumlah_tiket_khusus"]');
            const currentForm = document.getElementById('form-khusus');

            inputJumlahTiket.addEventListener('input', () => {
                const jumlah = parseInt(inputJumlahTiket.value);
                if (!isNaN(jumlah)) {
                    const updatedProducts = [{
                        ...product,
                        jumlah_tiket: jumlah
                    }];
                    getLastBatchNumberAndGenerateTickets(updatedProducts, jumlah, currentForm);
                }
            });
        }
    });

    function clearDefaultFormulaInputs() {
        const defaultContainer = document.getElementById('formulaDefault');
        if (defaultContainer) {
            defaultContainer.innerHTML = '';
        }
    }

    function getLastBatchNumberAndGenerateTickets(products, jumlahTiket, formScope) {
        const year = new Date().getFullYear();
        fetch(`/get-last-batch-number?year=${year}`)
            .then(res => res.json())
            .then(data => {
                const lastNumber = data.success ? data.lastProductionNumber : 0;
                generateTicketInputs(products, lastNumber + 1, formScope);
            });
    }

    function generateTicketInputs(products, startingNumber, formScope) {
        const ticketContainer = formScope.querySelector('.ticket-container');
        ticketContainer.innerHTML = '';
        const year = new Date().getFullYear();
        const month = new Date().getMonth() + 1;
        let runningNumber = startingNumber;

        products.forEach(product => {
            const totalTickets = product.jumlah_tiket;

            for (let i = 0; i < totalTickets; i++) {
                const batchNumber = generateBatchNumber(product.code_product, year, month,
                    runningNumber++);
                const ticketHtml = `
                    <div class="row g-3 mb-3 ticket-row" data-index="${i}">
                        <div class="col-md-3">
                            <input type="text" name="batch_number[]" class="form-control border px-2 py-1" value="${batchNumber}">
                            <small class="text-danger">*nomor batch otomatis</small>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="batch_size[]" class="form-control border px-2 py-1 batch_size" value="${product.ukuran_batch}">
                            <small>Batch Size</small>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="total_barang_jadi[]" class="form-control border px-2 py-1 hasil_produk_jadi" value="${product.hasil_satu_batch}">
                            <small>Barang Jadi</small>
                        </div>
                        <div class="col-md-3">
                            <input type="month" name="tanggal_expired[]" class="form-control border px-2 py-1">
                            <small>Expired</small>
                        </div>
                        <input type="hidden" name="product_id[]" value="${product.product_id}">
                    </div>
                `;
                ticketContainer.insertAdjacentHTML('beforeend', ticketHtml);
            }

            setupAutoHitungHasilJadi(product);
            setupBatchListeners(product, formScope);
            getFormulasAndGenerateTickets(product, product.invoice_id, totalTickets, formScope);
        });
    }

    function getFormulasAndGenerateTickets(product, invoiceId, jumlahTiketManual, formScope) {
        const ticketRows = formScope.querySelectorAll('.ticket-row');

        // Ambil select container relatif ke formScope
        const formulaSelectContainer = formScope.querySelector('#formula-select-container');
        const formulaSelectContainerKemas = formScope.querySelector('#formula-select-container-kemas');
        const templateSelect = formScope.querySelector('#template-select');
        const templateKemasSelect = formScope.querySelector('#template-kemas-select');

        // Tampilkan container select template jika awalnya d-none
        if (formulaSelectContainer && formulaSelectContainer.classList.contains('d-none')) {
            formulaSelectContainer.classList.remove('d-none');
        }

        if (formulaSelectContainerKemas && formulaSelectContainerKemas.classList.contains('d-none')) {
            formulaSelectContainerKemas.classList.remove('d-none');
        }

        // Generate tiket list sesuai jumlah tiket manual
        ticketRows.forEach((row, index) => {
            const batchInput = row.querySelector('.batch_size');
            const batchSize = parseFloat(batchInput?.value || 100);

            // Jika custom formula aktif, skip
            const isCustomFormula = formScope.querySelector('#isCustomFormula')?.checked;
            if (isCustomFormula) return;

            // Ambil semua template yang mirip produk
            fetch(`/formula-templates/${product.product_id}`)
                .then(res => res.json())
                .then(templates => {
                    templateSelect.innerHTML = `<option value="">-- pilih formula --</option>`;
                    templates.forEach(t => {
                        const option = document.createElement('option');
                        option.value = t.id;
                        option.dataset.id = t.id;
                        option.textContent = t.nama_formula;
                        templateSelect.appendChild(option);
                    });
                });

            fetch(`/formula-templates-kemas/${product.product_id}`)
                .then(res => res.json())
                .then(templates => {
                    // Kosongkan template select
                    templateKemasSelect.innerHTML = `<option value="">-- pilih kemasan --</option>`;
                    templates.forEach(t => {
                        const option = document.createElement('option');
                        option.value = t.id;
                        option.dataset.id = t.id;
                        option.textContent = t.nama_formula;
                        templateKemasSelect.appendChild(option);
                    });
                });
        });

        // -----------------------------
        // Event listener saat user pilih template
        // -----------------------------
        if (templateSelect) {
            templateSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const formulaId = this.value;

                console.log('Selected Template:', formulaId);
                const BakuInput = formScope.querySelector('#selected-template');
                BakuInput.value = formulaId;
                
                if (!formulaId) return;

                // Ambil data formula dari server berdasarkan template
                fetch(`/formula-by-template?formula_id=${encodeURIComponent(formulaId)}`)
                    .then(res => res.json())
                    .then(formulasWithStock => {
                        // Ambil total tiket
                        const inputJumlahTiket = formScope.querySelector(
                            'input[name="jumlah_tiket"], input[name="jumlah_tiket_khusus"]');
                        const totalTickets = parseInt(inputJumlahTiket?.value) || 1;

                        // Ambil batch size dari tiket terakhir
                        let batchSize = 100; // default
                        const ticketRows = formScope.querySelectorAll('.ticket-row');
                        if (ticketRows.length > 0) {
                            const lastRow = ticketRows[ticketRows.length - 1];
                            batchSize = parseFloat(lastRow.querySelector('.batch_size')?.value ||
                                100);
                        }

                        // Tampilkan formulasWithStock ke tabel
                        displayFormulas(formulasWithStock, totalTickets, batchSize, formScope, 'baku');
                    });
            });
        }

        if (templateKemasSelect) {
            templateKemasSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const formulaId = this.value;

                console.log('Selected Template:', formulaId);
                const KemasanInput = formScope.querySelector('#selected-kemasan-template');
                KemasanInput.value = formulaId;

                if (!formulaId) return;

                // Ambil data formula dari server berdasarkan template
                fetch(`/formula-kemasan-by-template?formula_id=${encodeURIComponent(formulaId)}`)
                    .then(res => res.json())
                    .then(formulasWithStock => {
                        // Ambil total tiket
                        const inputJumlahTiket = formScope.querySelector(
                            'input[name="jumlah_tiket"], input[name="jumlah_tiket_khusus"]');
                        const totalTickets = parseInt(inputJumlahTiket?.value) || 1;

                        // Ambil batch size dari tiket terakhir
                        let batchSize = 100; // default
                        const ticketRows = formScope.querySelectorAll('.ticket-row');
                        if (ticketRows.length > 0) {
                            const lastRow = ticketRows[ticketRows.length - 1];
                            batchSize = parseFloat(lastRow.querySelector('.batch_size')?.value ||
                                100);
                        }

                        // Tampilkan formulasWithStock ke tabel
                        displayFormulas(formulasWithStock, totalTickets, batchSize, formScope, 'kemas');
                    });
            });
        }
    }

    function displayFormulas(formulasWithStock, totalTickets, batchSize, formScope, jenis) {
        const bahanBakuContainer = formScope.querySelector('.formula-bahan-baku');
        const bahanKemasContainer = formScope.querySelector('.formula-bahan-kemas');

        if (jenis === 'baku') {
            bahanBakuContainer.innerHTML = '';
        } else if (jenis === 'kemas') {
            bahanKemasContainer.innerHTML = '';
        }

        formulasWithStock.forEach(formula => {
            let jumlahPerTicket = 0;
            if (jenis === 'kemas') {
                jumlahPerTicket = Math.ceil((parseFloat(formula.jumlah || 0) / 100) * batchSize);
            } else {
                jumlahPerTicket = smartRound((parseFloat(formula.jumlah || 0) / 100) * batchSize);
            }
            const jumlah = jumlahPerTicket * totalTickets;
            
            const isAqua = formula.name && formula.name.toLowerCase().includes('aqua');
            const status = isAqua ? 'Cukup' : (formula.stok_akhir >= jumlah ? 'Cukup' : 'Tidak Cukup');
            const stokTersedia = isAqua ? '-' : formula.stok_akhir;

            const row = `
                <tr>
                    <td>${formula.name}</td>
                    <td>${jumlah} ${formula.satuan}</td>
                    <td>${stokTersedia}</td>
                    <td class="${status === 'Cukup' ? 'text-success' : 'text-danger'}">${status}</td>
                </tr>
            `;

            if (jenis === 'baku') {
                bahanBakuContainer.innerHTML += row;
            } else if (jenis === 'kemas') {
                bahanKemasContainer.innerHTML += row;
            }
        });
    }

    function setupBatchListeners(product, formScope) {
        const ticketRows = formScope.querySelectorAll('.ticket-row');

        const templateSelect = formScope.querySelector('#template-select');
        const templateKemasSelect = formScope.querySelector('#template-kemas-select');

        ticketRows.forEach((row, index) => {
            const batchInput = row.querySelector('.batch_size');
            const hasilInput = row.querySelector('.hasil_produk_jadi');

            batchInput.addEventListener('input', () => {
                if (templateSelect && templateSelect.value) {
                    templateSelect.dispatchEvent(new Event('change'));
                }
                if (templateKemasSelect && templateKemasSelect.value) {
                    templateKemasSelect.dispatchEvent(new Event('change'));
                }
            });

            hasilInput.addEventListener('input', () => {
                // hasil input jadi tidak mengubah proporsi formula
            });
        });
    }

    function setupAutoHitungHasilJadi(product) {
        const rows = document.querySelectorAll('.ticket-row');

        rows.forEach((row, index) => {
            const batchInput = row.querySelector('.batch_size');
            const hasilInput = row.querySelector('.hasil_produk_jadi');

            batchInput.addEventListener('input', () => {
                const batchVal = parseFloat(batchInput.value);
                const hasilPer100 = parseFloat(product.hasil_satu_batch || 0);

                if (!isNaN(batchVal) && !isNaN(hasilPer100)) {
                    const hasil = Math.round((batchVal / 100) * hasilPer100);
                    hasilInput.value = hasil;
                    console.log(`Row ${index} - Auto hitung hasil jadi: ${hasil}`);
                }
            });
        });
    }

    function generateBatchNumber(code, year, month, number) {
        const yearCode = (year % 1000).toString().padStart(3, '0');
        const monthCode = month.toString().padStart(2, '0');
        const productCode = extractNumericCode(code);
        const running = number.toString().padStart(3, '0');
        return `${yearCode}${productCode}${monthCode}${running}`;
    }

    function smartRound(value) {
        const floatVal = parseFloat(value);
        if (floatVal < 10 && !Number.isInteger(floatVal)) {
            return parseFloat(floatVal.toFixed(2));
        }
        return Math.round(floatVal);
    }

    function extractNumericCode(code) {
        return (code.match(/\d+/g)?.join('') || '000').slice(-3);
    }

    $('#select-product-tiket-khusus').select2({
        theme: 'bootstrap-5',
        placeholder: '-- pilih produk --',
        width: '100%'
    });
});
 </script>
 <!-- CSS -->
 <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
     rel="stylesheet" />

 <!-- JS -->
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

 @endsection