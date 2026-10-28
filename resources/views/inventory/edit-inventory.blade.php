@extends('layouts.app')
@section('title', 'Komitkami Helpers | Edit Material')
@section('content')
<div class="d-flex justify-content-between align-items-center">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="edit-material-tab" data-bs-toggle="tab" data-bs-target="#edit-material" type="button" role="tab" aria-controls="edit-material" aria-selected="true">Edit Material</button>
        </li>
        @if(Auth::user()->hasAnyRole(['Admin', 'Production Manager']))
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="edit-spesifikasi-tab" data-bs-toggle="tab" data-bs-target="#edit-spesifikasi" type="button" role="tab" aria-controls="edit-spesifikasi" aria-selected="false">Edit Spesifikasi</button>
        </li>
        @endif
    </ul>
</div>
<div>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="edit-material" role="tabpanel" aria-labelledby="edit-material-tab">
            <div class="card p-3">
                <form id="update-material-form" action="{{ url('/update-material', $material->slug) }}" method="post">
                    @csrf
                    @method('PUT')
                    <h5>Edit Material</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name">Nama Material</label>
                            <input type="text" class="form-control px-2 py-1 border" name="name" value="{{ $material->name }}" id="name">
                        </div>
                        <div class="col-md-6">
                            <label for="code">Code Material</label>
                            <input type="text" class="form-control px-2 py-1 border" name="code" value="{{ $material->code }}"
                                id="code">
                        </div>
                        <div class="col-md-6">
                            <label for="type">Type Material</label>
                            <select name="type" class="form-control border py-1 px-2">
                                <option value="Bahan Baku" @if ($material->type == 'Bahan Baku') selected @endif>Bahan Baku</option>
                                <option value="Bahan Kemasan" @if ($material->type == 'Bahan Kemasan') selected @endif>Bahan Kemasan</option>
                                <option value="Bahan Kemas Label" @if ($material->type == 'Bahan Kemas Label') selected @endif>Bahan Kemas Label</option>
                            </select>
                        </div>
                    </div>
                    @if(Auth::user()->hasAnyRole(['Admin', 'Production Manager']))
                    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                        <h5 class="mb-0">Suplier Material</h5>
                        <button type="button" class="btn btn-sm m-0 btn-primary" id="add-supplier">
                            + Tambah Suplier
                        </button>
                    </div>
                    <div id="supplier-container">
                        @forelse ($suplier as $item)
                            <div class="row g-3 mb-2 supplier-row">
                                <input type="hidden" name="supplier_ids[]" value="{{ $item->id }}">
                                <div class="col-md-5">
                                    <label>Nama Suplier</label>
                                    <input type="text" class="form-control px-2 py-1 border" name="supplier_names[]" value="{{ $item->name ?? '' }}" required>
                                </div>
                                <div class="col-md-5">
                                    <label>Alamat</label>
                                    <input type="text" class="form-control px-2 py-1 border" name="supplier_addresses[]" value="{{ $item->address ?? '' }}" required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm w-100 m-0 remove-supplier">Hapus</button>
                                </div>
                            </div>
                        @empty
                            <div class="row g-3 mb-2 supplier-row">
                                <input type="hidden" name="supplier_ids[]" value="">
                                <div class="col-md-5">
                                    <label>Nama Suplier</label>
                                    <input type="text" class="form-control px-2 py-1 border" name="supplier_names[]" required>
                                </div>
                                <div class="col-md-5">
                                    <label>Alamat</label>
                                    <input type="text" class="form-control px-2 py-1 border" name="supplier_addresses[]" required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm m-0 w-100 remove-supplier">Hapus</button>
                                </div>
                            </div>
                        @endforelse

                    </div>
                    @endif
                </form>
                
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" form="update-material-form" class="btn btn-success mb-0">Update</button>
                    <a href="{{ url('/local-inventory') }}" class="btn btn-secondary mb-0">Kembali</a>
                    <form action="{{ url('/delete-material', $material->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus material ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger mb-0">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @if(Auth::user()->hasAnyRole(['Admin', 'Production Manager']))
        <div class="tab-pane fade" id="edit-spesifikasi" role="tabpanel" aria-labelledby="edit-spesifikasi-tab">
            @include('inventory.spesifikasi-uji')
        </div>
        @endif
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('supplier-container');
    const addButton = document.getElementById('add-supplier');

    addButton.addEventListener('click', function () {
        const newRow = document.createElement('div');
        newRow.className = 'row g-3 mb-2 supplier-row';
        newRow.innerHTML = `
            <input type="hidden" name="supplier_ids[]" value="">
            <div class="col-md-5">
                <label>Nama Suplier</label>
                <input type="text" class="form-control px-2 py-1 border" name="supplier_names[]" required>
            </div>
            <div class="col-md-5">
                <label>Alamat</label>
                <input type="text" class="form-control px-2 py-1 border" name="supplier_addresses[]" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm w-100 m-0 remove-supplier">Hapus</button>
            </div>
        `;
        container.appendChild(newRow);
    });

    container.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-supplier')) {
            const rows = container.querySelectorAll('.supplier-row');
            if (rows.length > 1) {
                e.target.closest('.supplier-row').remove();
            } else {
                alert('Minimal harus ada 1 suplier.');
            }
        }
    });

    // Restore active tab after reload
    const activeTab = localStorage.getItem('activeTabMfg');
    if (activeTab) {
        // Aktifkan Parent Tab (Edit Spesifikasi) terlebih dahulu
        const mainSpecTab = document.querySelector('#edit-spesifikasi-tab');
        if (mainSpecTab) {
            document.querySelectorAll('#myTab .nav-link').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('#myTabContent > .tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
            
            mainSpecTab.classList.add('active');
            const parentContent = document.querySelector('#edit-spesifikasi');
            if(parentContent) parentContent.classList.add('show', 'active');
        }

        // Aktifkan Child Tab (Produsen yang spesifik)
        const tabBtn = document.querySelector(`#tab-${activeTab}`);
        if (tabBtn) {
            document.querySelectorAll('.nav-pills .nav-link').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content .tab-pane').forEach(pane => {
                // Hanya hapus kelas active jika tab pane ini bukan si parent #edit-spesifikasi
                if(pane.id !== 'edit-spesifikasi' && pane.id !== 'edit-material') {
                    pane.classList.remove('show', 'active');
                }
            });
            
            tabBtn.classList.add('active');
            const content = document.querySelector(`#content-${activeTab}`);
            if(content) content.classList.add('show', 'active');
        }
        localStorage.removeItem('activeTabMfg');
    }

    // Save active tab when clicked (normal click)
    document.querySelectorAll('.nav-link[data-bs-toggle="tab"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(e) {
            // Get ID from target (e.g. #content-123)
            const targetId = e.target.getAttribute('data-bs-target').replace('#content-', '');
            localStorage.setItem('activeTabMfg', targetId);
        });
    });

    // Delete manufacturer
    const deleteButtons = document.querySelectorAll('.delete-manufacturer');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent tab from opening
            const manufacturerId = this.getAttribute('data-id');
            const manufacturerName = this.getAttribute('data-name');
            
            Swal.fire({
                title: 'Hapus Produsen?',
                text: `Apakah Anda yakin ingin menghapus data produsen "${manufacturerName}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('_method', 'DELETE');
                        
                        const response = await fetch(`{{ url('/inventory/spesifikasi/' . ($material->slug ?? '')) }}/${manufacturerId}`, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            body: formData
                        });
                        const data = await response.json();
                        if (data.success) {
                            // Hapus tab dan content dari DOM
                            const tab = document.querySelector(`#tab-${manufacturerId}`).closest('.nav-item');
                            const content = document.querySelector(`#content-${manufacturerId}`);
                            
                            if (tab) tab.remove();
                            if (content) content.remove();
                            
                            // Jika tab yang dihapus sedang aktif, aktifkan tab pertama yang ada
                            if (!document.querySelector('.nav-link.active')) {
                                const firstTab = document.querySelector('.nav-link[data-bs-toggle="tab"]');
                                if (firstTab) {
                                    firstTab.classList.add('active');
                                    const targetId = firstTab.getAttribute('data-bs-target');
                                    const targetPane = document.querySelector(targetId);
                                    if (targetPane) {
                                        targetPane.classList.add('show', 'active');
                                    }
                                } else {
                                    // Jika tidak ada tab lagi yang tersisa
                                    document.querySelector('#myTabContent').innerHTML = '<div class="alert alert-info">Belum ada produsen. Silakan tambah produsen terlebih dahulu.</div>';
                                }
                            }
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    } catch(e) {
                        console.error(e);
                    }
                }
            });
        });
    });

    // Double click to edit manufacturer name
    const manufacturerNames = document.querySelectorAll('.manufacturer-name');
    manufacturerNames.forEach(span => {
        span.addEventListener('dblclick', async function(e) {
            e.stopPropagation();
            const manufacturerId = this.getAttribute('data-id');
            const currentName = this.getAttribute('data-name');
            const currentAddress = this.getAttribute('data-address');
            
            const { value: formValues } = await Swal.fire({
                title: 'Edit Produsen',
                html: `
                    <div style="text-align: left;">
                        <label for="swal-input-name" style="font-weight: bold; display: block; margin-bottom: 5px;">Nama Produsen</label>
                        <input id="swal-input-name" class="form-control border px-2 py-1 mb-3" value="${currentName}">
                        
                        <label for="swal-input-address" style="font-weight: bold; display: block; margin-bottom: 5px;">Alamat Produsen</label>
                        <input id="swal-input-address" class="form-control border px-2 py-1" value="${currentAddress}">
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const name = document.getElementById('swal-input-name').value.trim();
                    const address = document.getElementById('swal-input-address').value.trim();
                    
                    if (!name) {
                        Swal.showValidationMessage('Nama produsen tidak boleh kosong!');
                        return false;
                    }
                    
                    return { name: name, address: address };
                }
            });

            if (formValues) {
                console.log('Nama Baru:', formValues.name);
                console.log('Alamat Baru:', formValues.address);
            }

            if (formValues && (formValues.name !== currentName || formValues.address !== currentAddress)) {
                try {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'PUT');
                    formData.append('name', formValues.name);
                    formData.append('address', formValues.address);
                    
                    const response = await fetch(`{{ url('/inventory/spesifikasi/' . ($material->slug ?? '')) }}/manufacturer/${manufacturerId}`, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });
                    const data = await response.json();
                    if(data.success) {
                        span.textContent = data.name;
                        span.setAttribute('data-name', data.name);  
                        span.setAttribute('data-address', data.address);  
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                } catch(e) {
                    console.error(e);
                }
            }
        });
    });

    // Add Manufacturer Form
    const addMfgForm = document.querySelector('#addManufacturerModal form');
    if(addMfgForm) {
        addMfgForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Menyimpan...';
            submitBtn.disabled = true;
            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const data = await response.json();
                if(data.success) {
                    localStorage.setItem('activeTabMfg', data.manufacturer_id);
                    window.location.reload();
                }
            } catch(error) {
                console.error(error);
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // Save Spesifikasi Forms
    document.querySelectorAll('.tab-pane form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            const data = await response.json();
            if(data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    if (data.new_slug) {
                        const currentUrl = window.location.href;
                        const newUrl = currentUrl.replace(/\/edit-material\/[^\/?#]+/, '/edit-material/' + data.new_slug);
                        if (currentUrl !== newUrl) {
                            window.location.href = newUrl; // Redirect to the new URL
                        }
                    }
                });
            }
        });
    });
});
</script>
@endsection