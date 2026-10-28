@extends('layouts.app')
@section('title', 'Komitkami Helper | Stock Opname')
@section('content')

<div class="mt-4 d-flex align-items-center justify-content-between">
    <h4 class="mb-0">Form Stock Opname</h4>
    <a href="{{ route('stock-opname.history') }}" class="btn btn-primary m-0">Riwayat Opname</a>
</div>

<div class="card p-4 mt-3">
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
    @if($errors->any())
        <div class="alert alert-danger text-white">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('stock-opname.store') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="input-group input-group-static mb-4">
                    <label for="item_selection" class="ms-0">Pilih Item (Bahan atau Produk)</label>
                    <select name="item_selection" id="item_selection" class="form-control" required>
                        <option value="">-- Pilih Item --</option>
                        <optgroup label="Bahan Baku & Kemas">
                            @foreach($inventories as $inv)
                                <option value="inventory_{{ $inv->id }}">{{ $inv->name }} ({{ $inv->code }})</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Produk Jadi">
                            @foreach($products as $prod)
                                <option value="product_{{ $prod->id }}">{{ $prod->name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>

            <!-- Container for Batch List -->
            <div class="col-md-12 mb-3" id="batch_list_container" style="display: none;">
                <h5>Daftar Batch / Stok Tersedia</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nomor Batch</th>
                                <th>ED</th>
                                <th>Stok Sistem</th>
                                <th style="width: 200px;">Stok Aktual</th>
                                <th>Selisih</th>
                            </tr>
                        </thead>
                        <tbody id="batch_list_body">
                            <!-- Rows dynamically added via JS -->
                        </tbody>
                        <tfoot id="batch_list_foot" style="display: none;">
                            <tr class="fw-bold bg-light">
                                <td colspan="2" class="text-end text-dark font-weight-bold">Total Keseluruhan:</td>
                                <td class="text-dark font-weight-bold" id="total_stok_sistem">0</td>
                                <td class="text-dark font-weight-bold" id="total_stok_aktual">0</td>
                                <td class="text-dark font-weight-bold" id="total_selisih">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Container: Tambah Batch Baru yang tidak ada di sistem -->
            <div class="col-md-12 mb-3" id="new_batch_container" style="display: none;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="mb-0">Tambah Batch Baru <span class="badge bg-gradient-warning text-dark">Tidak ada di sistem</span></h6>
                    <button type="button" class="btn btn-sm btn-outline-primary m-0" id="btn_add_new_batch">
                        <i class="material-icons" style="font-size:16px;vertical-align:middle;">add</i> Tambah Baris
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="new_batch_table">
                        <thead>
                            <tr>
                                <th>Nomor Batch <span class="text-danger">*</span></th>
                                <th style="width:160px;">Stok Fisik <span class="text-danger">*</span></th>
                                <th style="width:160px;">Tanggal ED</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="new_batch_body">
                            <!-- rows added via JS -->
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">Batch ini belum pernah tercatat di sistem. Akan ditambahkan sebagai stok baru dengan kategori <code>stock_opname</code>.</small>
            </div>

            <div class="col-md-12 mb-3">
                <label class="m-0" for="keterangan">Keterangan / Catatan Global</label>
                <div class="input-group input-group-outline mb-4">
                    <textarea name="keterangan" id="keterangan" rows="2" class="form-control" placeholder="Contoh: Stok fisik di gudang B sudah dicek"></textarea>
                </div>
            </div>

            <div class="col-md-12">
                <button type="submit" class="btn btn-primary" id="btn-submit" disabled>Simpan Penyesuaian</button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemSelect = document.getElementById('item_selection');
    const tbody = document.getElementById('batch_list_body');
    const tfoot = document.getElementById('batch_list_foot');
    const btnSubmit = document.getElementById('btn-submit');

    $('#item_selection').select2({
        width: '100%'
    });
    
    function calculateTotals() {
        let totalSistem = 0;
        let totalAktual = 0;
        let totalSelisih = 0;

        $('#batch_list_body tr').each(function() {
            const index = $(this).find('.stok-fisik').data('index');
            if (index !== undefined) {
                const sistem = parseFloat($(`input[name="batches[${index}][stok_sistem]"]`).val()) || 0;
                const fisik = parseFloat($(`input[name="batches[${index}][stok_fisik]"]`).val()) || 0;
                const selisih = parseFloat($(`input[name="batches[${index}][selisih]"]`).val()) || 0;

                totalSistem += sistem;
                totalAktual += fisik;
                totalSelisih += selisih;
            }
        });

        $('#total_stok_sistem').text(parseFloat(totalSistem.toFixed(3)));
        $('#total_stok_aktual').text(parseFloat(totalAktual.toFixed(3)));
        
        const selisihEl = $('#total_selisih');
        selisihEl.text(parseFloat(totalSelisih.toFixed(3)));
        if (totalSelisih > 0) {
            selisihEl.removeClass('text-danger text-dark').addClass('text-success');
            selisihEl.text('+' + parseFloat(totalSelisih.toFixed(3)));
        } else if (totalSelisih < 0) {
            selisihEl.removeClass('text-success text-dark').addClass('text-danger');
        } else {
            selisihEl.removeClass('text-success text-danger').addClass('text-dark');
        }
    }

    $('#item_selection').on('select2:select', function() {
        const val = this.value;
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Loading...</td></tr>';
        tfoot.style.display = 'none';
        $('#batch_list_container').show();
        btnSubmit.disabled = true;

        if (!val) {
            $('#batch_list_container').hide();
            return;
        }

        const parts = val.split('_');
        const type = parts[0];
        const id = parts[1];

        // Tampilkan tombol tambah batch baru setelah item dipilih
        $('#new_batch_container').show();
        $('#new_batch_body').empty();
        
        fetch(`/stock-opname/get-batches/${type}/${id}`)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada batch/stok tersedia.</td></tr>';
                    btnSubmit.disabled = true;
                    return;
                }
                
                const activeBatches = data.filter(b => parseFloat(b.available || 0) > 0 || b.is_kemas);
                const inactiveBatches = data.filter(b => parseFloat(b.available || 0) <= 0 && !b.is_kemas);
                
                let renderRow = (batch, index, isInactive) => {
                    const tr = document.createElement('tr');
                    if (isInactive) {
                        tr.className = 'inactive-batch';
                        tr.style.display = 'none';
                        tr.style.backgroundColor = '#f8f9fa';
                    }

                    const rawBatchName = batch.nomor_batch || '-';
                    const entryCount   = parseInt(batch.entry_count || 1);
                    // Badge jika batch ini masuk lebih dari 1 kali pengiriman
                    const entryBadge   = entryCount > 1
                        ? ` <span class="badge badge-sm bg-gradient-info" title="${entryCount} penerimaan digabung">${entryCount}x</span>`
                        : '';
                    const batchName    = rawBatchName + entryBadge;

                    const ed          = batch.tanggal_ed_formatted || '-';
                    const stockSystem = parseFloat(batch.available || 0);

                    // Default stok_fisik = stok_sistem → selisih default = 0
                    // Batch yang tidak diubah user akan di-skip oleh controller (tidak disimpan)
                    const defaultFisik   = stockSystem;
                    const defaultSelisih = 0;

                    tr.innerHTML = `
                        <td>
                            ${batchName}
                            <input type="hidden" name="batches[${index}][batch_id]"      value="${batch.id || ''}">
                            <input type="hidden" name="batches[${index}][batch_ids_json]" value='${batch.batch_ids_json || '[]'}'>
                            <input type="hidden" name="batches[${index}][nomor_batch]"    value="${rawBatchName}">
                        </td>
                        <td>${ed}</td>
                        <td>
                            ${stockSystem}
                            <input type="hidden" name="batches[${index}][stok_sistem]" value="${stockSystem}">
                        </td>
                        <td>
                            <div class="input-group input-group-outline is-filled">
                                <input type="number" step="0.001"
                                    name="batches[${index}][stok_fisik]"
                                    class="form-control stok-fisik"
                                    data-index="${index}"
                                    data-sistem="${stockSystem}"
                                    value="${parseFloat(defaultFisik.toFixed(3))}"
                                    required>
                            </div>
                        </td>
                        <td id="td_selisih_${index}">
                            <div class="input-group input-group-static">
                                <input type="number" step="0.001"
                                    name="batches[${index}][selisih]"
                                    id="selisih_${index}"
                                    class="form-control text-dark"
                                    value="${parseFloat(defaultSelisih.toFixed(3))}"
                                    readonly>
                            </div>
                        </td>
                    `;
                    return tr;
                };

                let globalIndex = 0;
                
                if (activeBatches.length === 0 && inactiveBatches.length > 0) {
                     tbody.innerHTML = '<tr><td colspan="5" class="text-center text-warning">Semua batch bersaldo 0. Silakan tampilkan batch kosong untuk mengisi.</td></tr>';
                }

                activeBatches.forEach(batch => {
                    tbody.appendChild(renderRow(batch, globalIndex++, false));
                });
                
                if (inactiveBatches.length > 0) {
                    const trToggle = document.createElement('tr');
                    trToggle.id = 'row_toggle_inactive';
                    trToggle.innerHTML = `
                        <td colspan="5" class="text-center p-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary m-0" id="btn_toggle_inactive">
                                Tampilkan ${inactiveBatches.length} Batch dengan Stock 0
                            </button>
                        </td>
                    `;
                    tbody.appendChild(trToggle);
                    
                    inactiveBatches.forEach(batch => {
                        tbody.appendChild(renderRow(batch, globalIndex++, true));
                    });
                }
                
                tfoot.style.display = '';
                calculateTotals();
                btnSubmit.disabled = false;
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Terjadi kesalahan saat memuat data.</td></tr>';
                console.error(err);
            });
    });

    $(document).on('click', '#btn_toggle_inactive', function() {
        const isHidden = $('.inactive-batch').first().is(':hidden');
        if (isHidden) {
            $('.inactive-batch').show();
            $(this).text('Sembunyikan Batch dengan Stock 0');
        } else {
            $('.inactive-batch').hide();
            $(this).text(`Tampilkan ${$('.inactive-batch').length} Batch dengan Stock 0`);
        }
    });
    
    // ─── Normalize comma → dot saat user mengetik di stok-fisik ───────────────
    $(document).on('input', '.stok-fisik', function() {
        // Normalisasi koma ke titik sebelum parse
        let rawVal = $(this).val().replace(',', '.');
        if (rawVal !== $(this).val()) $(this).val(rawVal);

        const index  = $(this).data('index');
        const sistem = parseFloat($(this).data('sistem') || $(`input[name="batches[${index}][stok_sistem]"]`).val()) || 0;
        const fisik  = parseFloat(rawVal) || 0;

        const selisih = parseFloat((fisik - sistem).toFixed(3));
        const $selisihInput = $(`#selisih_${index}`);
        $selisihInput.val(selisih);

        // Warnai sel selisih agar user langsung tahu ada perubahan
        if (selisih > 0) {
            $selisihInput.removeClass('text-danger text-dark').addClass('text-success fw-bold');
        } else if (selisih < 0) {
            $selisihInput.removeClass('text-success text-dark').addClass('text-danger fw-bold');
        } else {
            $selisihInput.removeClass('text-success text-danger fw-bold').addClass('text-dark');
        }

        calculateTotals();
    });

    // ─── Tambah baris batch baru ─────────────────────────────────────────────
    let newBatchIndex = 0;
    $(document).on('click', '#btn_add_new_batch', function() {
        const idx = newBatchIndex++;
        const tr = document.createElement('tr');
        tr.id = `new_batch_row_${idx}`;
        tr.innerHTML = `
            <td>
                <div class="input-group input-group-outline is-filled">
                    <input type="text" name="new_batches[${idx}][nomor_batch]" class="form-control"
                           placeholder="Contoh: BB-2026-001" required>
                </div>
            </td>
            <td>
                <div class="input-group input-group-outline is-filled">
                    <input type="text" name="new_batches[${idx}][stok_fisik]" class="form-control new-stok-fisik"
                           placeholder="0" required inputmode="decimal">
                </div>
            </td>
            <td>
                <div class="input-group input-group-outline is-filled">
                    <input type="date" name="new_batches[${idx}][tanggal_ed]" class="form-control">
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger m-0 btn-remove-new-batch"
                        data-id="${idx}">
                    <i class="material-icons" style="font-size:14px;vertical-align:middle;">delete</i>
                </button>
            </td>
        `;
        document.getElementById('new_batch_body').appendChild(tr);
    });

    // Hapus baris batch baru
    $(document).on('click', '.btn-remove-new-batch', function() {
        $(`#new_batch_row_${$(this).data('id')}`).remove();
    });

    // Normalize comma → dot untuk input stok baru
    $(document).on('input', '.new-stok-fisik', function() {
        let val = $(this).val().replace(',', '.');
        if (val !== $(this).val()) $(this).val(val);
    });

    // ─── Normalize semua numeric field sebelum submit ────────────────────────
    $('form').on('submit', function() {
        // Normalisasi stok_fisik (existing batches)
        $('.stok-fisik').each(function() {
            $(this).val(parseFloat(($(this).val() || '0').replace(',', '.')) || 0);
        });
        // Normalisasi new_batches stok fisik
        $('.new-stok-fisik').each(function() {
            $(this).val(parseFloat(($(this).val() || '0').replace(',', '.')) || 0);
        });
        return true; // lanjutkan submit
    });
});
</script>
@endsection
