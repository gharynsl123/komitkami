@extends('layouts.app')
@section('title', 'Komitkami | Pengecekan Material')
@section('content')
    @php
        $master = $type == 'manual' ? $manual_report : $stock_in;
        $tanggal_ed = $type == 'manual' ? $master->expired_date : $master->tanggal_ed;
        $vendor = $type == 'manual' ? '-' : $master->vendor;
        $stok = $type == 'manual' ? 0 : $master->stok;
    @endphp
    <style>
        .pm-table {
            overflow-x: auto;
            white-space: nowrap;
        }

        .pm-table input {
            min-width: 50px;
        }
    </style>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 id="form-title"></h5>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url('/pengecekan-barang-masuk') }}" class="btn btn-sm btn-secondary">Kembali</a>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-primary" id="prevBtn">
                        < 
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" id="nextBtn">
                        >
                    </button>
                </div>
                @if( ($type == 'manual' && $laporanInspeksi) || ($type != 'manual' && $stock_in && str_contains($stock_in->statusPengecekan(), 'Selesai')) )
                    <a href="{{ route('pengecekan-material.print', $type == 'manual' ? $manual_report->id : $stock_in->id) }}?type={{ $type }}" target="_blank" class="btn btn-sm btn-success ms-2">
                        <i class="bi bi-printer"></i> Cetak PDF
                    </a>
                @endif
            </div>
        </div>

    @if($inventory->type == 'Bahan Baku')
        @include('inventory.pengecekan-material.partials.inspeksi-penerimaan')
        @include('inventory.pengecekan-material.partials.pengambilan-sampel')
        @include('inventory.pengecekan-material.partials.laporan-inspeksi')
        @include('inventory.pengecekan-material.partials.pengujian')
    @else
        @include('inventory.pengecekan-material.partials.pengambilan-sampel')
        @include('inventory.pengecekan-material.partials.pengujian')
        @include('inventory.pengecekan-material.partials.laporan-inspeksi')
    @endif

    </div>

    <script>
        const inventoryType = "{{ $inventory->type }}";
        const allSpesifikasi = @json($allSpesifikasi ?? []);
        const manufacturers = @json(
            $inventory->manufacturers
                ->unique('id')
                ->values()
                ->toArray()
        );

        console.log(manufacturers);

        document.addEventListener("DOMContentLoaded", function () {
            function updateSampleColumns(numSamples) {
                if (inventoryType === 'Bahan Baku') return;
                
                const colHasil = document.getElementById("col-hasil-pengujian");
                if (colHasil) colHasil.colSpan = numSamples;
                const rowHasil = document.getElementById("row-hasil-pengujian");
                if (rowHasil) {
                    let headerHtml = '';
                    for (let i = 1; i <= numSamples; i++) {
                        headerHtml += `<td class="text-center" style="min-width: 60px;">${i}</td>`;
                    }
                    rowHasil.innerHTML = headerHtml;
                }

                const tableLaporan = document.querySelector('#table-laporan-inspeksi tbody');
                if (!tableLaporan) return;

                const rows = tableLaporan.querySelectorAll('tr');
                rows.forEach((tr, rowIdx) => {
                    if (tr.children.length === 1 && tr.children[0].hasAttribute('colspan')) return;
                    if (tr.children.length < 4) return;

                    const lastCell = tr.lastElementChild;
                    const currentSampleCount = tr.children.length - 4;
                    if (currentSampleCount === numSamples) return;

                    const paramInput = tr.querySelector('input[name^="parameter["]');
                    const match = paramInput ? paramInput.name.match(/\[(\d+)\]/) : null;
                    const targetIdx = match ? match[1] : rowIdx;

                    const firstSampleCell = tr.children[3];
                    const isSelect = !!firstSampleCell.querySelector('select');
                    const isCellDisabled = !!firstSampleCell.querySelector('input:disabled, select:disabled');

                    if (numSamples > currentSampleCount) {
                        for (let s = currentSampleCount; s < numSamples; s++) {
                            const newTd = document.createElement('td');
                            newTd.className = 'text-center';
                            if (isSelect) {
                                newTd.innerHTML = `
                                    <select name="hasil_kemas[${targetIdx}][]" class="form-select border border-1 px-2" ${isCellDisabled ? 'disabled' : ''}>
                                        <option value="">Pilih</option>
                                        <option value="Ok">Ok</option>
                                        <option value="Tidak">Tidak</option>
                                    </select>
                                `;
                            } else {
                                newTd.innerHTML = `
                                    <input type="text" name="hasil_kemas[${targetIdx}][]" class="form-control border-1 border px-2" ${isCellDisabled ? 'disabled' : ''}>
                                `;
                            }
                            tr.insertBefore(newTd, lastCell);
                        }
                    } else if (numSamples < currentSampleCount) {
                        const toRemove = currentSampleCount - numSamples;
                        for (let r = 0; r < toRemove; r++) {
                            const sampleCellToRemove = lastCell.previousElementSibling;
                            if (sampleCellToRemove && sampleCellToRemove !== tr.children[2]) {
                                tr.removeChild(sampleCellToRemove);
                            }
                        }
                    }
                });
            }

            const jumlahSampelInput = document.getElementById("jumlah_sampel_laporan");
            if (jumlahSampelInput && inventoryType !== 'Bahan Baku') {
                jumlahSampelInput.addEventListener('input', function() {
                    const parsed = parseInt(this.value.replace(/\D/g, ''));
                    const numSamples = (!isNaN(parsed) && parsed > 0) ? parsed : 1;
                    
                    const tableLaporan = document.querySelector('#table-laporan-inspeksi tbody');
                    const hasRows = tableLaporan && tableLaporan.querySelectorAll('tr').length > 0 && !tableLaporan.querySelector('#col-empty-state');
                    
                    if (hasRows) {
                        updateSampleColumns(numSamples);
                    } else {
                        const activeSelect = document.querySelector('select[name="manufacturer_id"]');
                        if (activeSelect && activeSelect.value) {
                            activeSelect.dispatchEvent(new Event("change"));
                        }
                    }
                });
            }

            // Logic for dynamic manufacturer spec update
            const manufacturerSelects = document.querySelectorAll('select[name="manufacturer_id"]');
            manufacturerSelects.forEach(select => {
                select.addEventListener("change", function() {
                    const mfgId = this.value;
                    let specsForMfg = [];
                    let manufacturer = null;

                    if(mfgId) {
                        specsForMfg = allSpesifikasi.filter(spec => spec.manufacturer_id == mfgId);
                        manufacturer = manufacturers.find( m => m.id == mfgId );
                    }

                    // Sync all manufacturer dropdowns across tabs
                    manufacturerSelects.forEach(s => {
                        if(s !== this) s.value = mfgId;
                    });

                    // Update address
                    const addresses = document.querySelectorAll('input[name="negara_asal"]');
                    addresses.forEach(addr => {
                        addr.value = manufacturer ? manufacturer.address : '';
                    });
                    
                    const getSpecValue = (section, param) => {
                        if (!mfgId) return '-';
                        const spec = specsForMfg.find(s => s.section_name === section && s.parameter === param);
                        return spec ? spec.spesifikasi : '-';
                    };

                    const specBentuk = document.getElementById("spec_bentuk");
                    if(specBentuk) specBentuk.value = getSpecValue('Organolepis', 'Bentuk');

                    const specWarna = document.getElementById("spec_warna");
                    if(specWarna) specWarna.value = getSpecValue('Organolepis', 'Warna');

                    const specRasa = document.getElementById("spec_rasa");
                    if(specRasa) specRasa.value = getSpecValue('Organolepis', 'Rasa');

                    // Update Laporan Inspeksi Table
                    const tableLaporan = document.querySelector('#table-laporan-inspeksi tbody');
                    if(tableLaporan && tableLaporan.dataset.filled !== 'true') {
                        let numSamples = 1;
                        if (inventoryType !== 'Bahan Baku') {
                            const jsInput = document.getElementById("jumlah_sampel_laporan");
                            if (jsInput && jsInput.value) {
                                const parsed = parseInt(jsInput.value.replace(/\D/g, ''));
                                if (!isNaN(parsed) && parsed > 0) {
                                    numSamples = parsed;
                                }
                            }
                            
                            // Update headers
                            const colHasil = document.getElementById("col-hasil-pengujian");
                            if (colHasil) colHasil.colSpan = numSamples;
                            const rowHasil = document.getElementById("row-hasil-pengujian");
                            if (rowHasil) {
                                let headerHtml = '';
                                for (let i = 1; i <= numSamples; i++) {
                                    headerHtml += `<td class="text-center" style="min-width: 60px;">${i}</td>`;
                                }
                                rowHasil.innerHTML = headerHtml;
                            }
                        }

                        tableLaporan.innerHTML = '';
                        let count = 1;
                        specsForMfg.forEach(spec => {
                            if (spec.parameter == "Suhu" || spec.parameter == "Spindle" || spec.parameter == "Speed" || spec.parameter == "Time") {
                                return;
                            }
                            const paramName = spec.section_name === 'Bahan Kemas' 
                                ? spec.parameter 
                                : (spec.parameter === 'Spesifikasi' ? spec.section_name : spec.section_name + ': ' + spec.parameter);
                            const persyaratan = spec.spesifikasi || '';
                            const isTextInput = /(tinggi|lebar|panjang|tebal|ketebalan|diameter|berat|volume|dimensi|size|ukuran)/i.test(paramName) || /\d/.test(persyaratan);

                            let inputHtml = '';
                            if (inventoryType !== 'Bahan Baku') {
                                for (let i = 0; i < numSamples; i++) {
                                    if (isTextInput) {
                                        inputHtml += `<td class="text-center"><input type="text" name="hasil_kemas[${count - 1}][]" class="form-control border-1 border px-2"></td>`;
                                    } else {
                                        inputHtml += `<td class="text-center">
                                            <select name="hasil_kemas[${count - 1}][]" class="form-select border border-1 px-2">
                                                <option value="">Pilih</option>
                                                <option value="Ok">Ok</option>
                                                <option value="Tidak">Tidak</option>
                                            </select>
                                        </td>`;
                                    }
                                }
                            } else {
                                inputHtml = `
                                    <td><input type="text" name="hasil_atas[${count - 1}]" class="form-control border-1 border px-2"></td>
                                    <td><input type="text" name="hasil_tengah[${count - 1}]" class="form-control border-1 border px-2"></td>
                                    <td><input type="text" name="hasil_bawah[${count - 1}]" class="form-control border-1 border px-2"></td>
                                `;
                            }

                            tableLaporan.innerHTML += `
                                    <tr>
                                        <td class="text-center">${count}</td>
                                        <td>
                                            <input type="text" name="parameter[${count - 1}]" value="${paramName}" readonly
                                                class="form-control border-0 px-2 bg-transparent">
                                        </td>
                                        <td>
                                            <input type="text" name="persyaratan[${count - 1}]" value="${persyaratan}" readonly
                                                class="form-control border-0 px-2 bg-transparent">
                                        </td>
                                        ${inputHtml}
                                        <td>
                                            <select name="hasil_inspeksi[${count - 1}]" class="form-select border border-1 px-2">
                                                <option value="">Pilih</option>
                                                <option value="Pass">Pass</option>
                                                <option value="Fail">Fail</option>
                                            </select>
                                        </td>
                                    </tr>
                                `;
                            count++;
                        });
                        if (count === 1) {
                            let colSpan = inventoryType !== 'Bahan Baku' ? (numSamples + 4) : 7;
                            tableLaporan.innerHTML = `<tr><td colspan="${colSpan}" id="col-empty-state" class="text-center text-muted py-3">Belum ada data spesifikasi untuk material ini. Harap input spesifikasi material pada menu Inventory.</td></tr>`;
                        }
                    }

                    // Update Pengujian Table
                    const tablePengujian = document.querySelector('#table-pengujian tbody');
                    if(tablePengujian && tablePengujian.dataset.filled !== 'true') {
                        tablePengujian.innerHTML = '';
                        let count = 1;
                        specsForMfg.forEach(spec => {
                            if (spec.parameter == "Suhu" || spec.parameter == "Spindle" || spec.parameter == "Speed" || spec.parameter == "Time") {
                                return;
                            }
                            const paramName = spec.section_name === 'Bahan Kemas' 
                                ? spec.parameter 
                                : (spec.parameter === 'Spesifikasi' ? spec.section_name : spec.section_name + ': ' + spec.parameter);

                            tablePengujian.innerHTML += `
                                <tr>
                                    <td class="text-center">${count++}</td>
                                    <td>
                                        <input type="text" name="parameter_uji[]" value="${paramName}" readonly
                                            class="form-control border-0 px-2 bg-transparent">
                                    </td>
                                    <td>
                                        <input type="text" name="spesifikasi[]" value="${spec.spesifikasi}" readonly
                                            class="form-control border-0 px-2 bg-transparent">
                                    </td>
                                    <td>
                                        <input type="text" name="hasil[]" placeholder="Hasil pengujian..."
                                            class="form-control border-1 border px-2">
                                    </td>
                                </tr>
                            `;
                        });
                        if (count === 1) {
                            tablePengujian.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-3">Belum ada data spesifikasi untuk material ini. Harap input spesifikasi material pada menu Inventory.</td></tr>`;
                        }

                        const btnSubmitPengujian = document.getElementById('btn-submit-pengujian');
                        if (btnSubmitPengujian) {
                            btnSubmitPengujian.style.display = (count > 1) ? 'inline-block' : 'none';
                        }
                    }
                });
                
                // Initialize if already has a value
                if(select.value) {
                    select.dispatchEvent(new Event("change"));
                }
            });
            document.querySelectorAll('.editable-form').forEach(form => {
                const editBtn = form.querySelector('.btn-edit');
                const saveBtn = form.querySelector('.btn-save');
                const cencel = form.querySelector('.btn-cencel');

                if (editBtn) {
                    editBtn.addEventListener('click', () => {
                        form.querySelectorAll('input, textarea, select').forEach(i => {
                            i.disabled = false;
                        });
                        editBtn.classList.add('d-none');
                        saveBtn.classList.remove('d-none');
                        if (cencel) cencel.classList.remove('d-none');
                        if (form.dataset.updateUrl) {
                            form.action = form.dataset.updateUrl;
                        }
                    });
                    if (cencel) {
                        cencel.addEventListener('click', () => {
                            form.querySelectorAll('input, textarea, select').forEach(i => {
                                if (i.name !== '_token' && i.name !== '_method' && i.type !== 'hidden' && !i.hasAttribute('readonly')) {
                                    i.disabled = true;
                                }
                            });
                            editBtn.classList.remove('d-none');
                            saveBtn.classList.add('d-none');
                            cencel.classList.add('d-none');
                        });
                    }
                }

                // Defensive: Pastikan semua input di-enable saat submit agar data tidak hilang
                form.addEventListener('submit', () => {
                    form.querySelectorAll('input, textarea, select').forEach(i => {
                        i.disabled = false;
                    });
                });
            });

            const sections = document.querySelectorAll(".form-section");
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");
            const titleEl = document.getElementById("form-title");

            const isBahanBaku = "{{$inventory->type}}" === "Bahan Baku";

            let currentIndex = 0;

            @if($pengambilanSampel)
                currentIndex = 1;
            @endif

            @if($inventory->type == 'Bahan Baku')
                @if($pengambilanSampel && $inspeksiPenerimaan)
                    currentIndex = 2;
                @endif

                @if($pengambilanSampel && $inspeksiPenerimaan && $pengujian)
                    currentIndex = 3;
                @endif
            @else
                // Untuk Bahan Kemas
                @if($pengambilanSampel && $pengujian)
                    currentIndex = 2;
                @endif
            @endif

            function showSection(index) {
                sections.forEach((sec, i) => {
                    sec.style.display = (i === index) ? "block" : "none";
                });

                titleEl.textContent = sections[index].dataset.title;
                prevBtn.disabled = (index === 0);
                nextBtn.disabled = (index === sections.length - 1);
            }

            prevBtn.addEventListener("click", function () {
                if (currentIndex > 0) {
                    currentIndex--;
                    showSection(currentIndex);
                }
            });

            nextBtn.addEventListener("click", function () {
                if (currentIndex < sections.length - 1) {
                    currentIndex++;
                    showSection(currentIndex);
                }
            });

            showSection(currentIndex);
        });
    </script>
@endsection