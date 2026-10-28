@extends('layouts.app')
@section('title', 'Komitkami Helper | Proses Produksi')
@section('content')

<style>
table {
    width: 100%;
    border-collapse: collapse;
    color: #000000;
    margin-top: 1rem;
}

td.label {
    text-align: left;
    padding-right: 10px;
    white-space: nowrap;
    width: 16%;
    font-weight: bold;
}

td.separator {
    text-align: center;
    width: 1%;
}

.nav-tabs .nav-item {
    border-radius: 8px !important;
}

.tab-pane {
    padding: 0px !important;
    background-color: transparent !important;
}
</style>
<div class="card p-2 mb-3">
    <div class="text-center">
        <p class="fw-bold m-0">Catatan Bembuatan Batch Alkessi</p>
        <small>Pemeriksaan Kebersihan</small>
    </div>
    <div class="d-flex table-responsive">
        <!-- poroduksi saat ini -->
        <table class="w-50 border-0">
            <tr>
                <td>
                    Produksi Saat ini
                </td>
            </tr>
            <tr>
                <td class="label">Nama Produk</td>
                <td class="separator">:</td>
                <td>{{$produksiSaatini->product->name}}</td>
            </tr>
            <tr>
                <td class="label">Batch Number</td>
                <td class="separator">:</td>
                <td>{{$produksiSaatini->batch_number}}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Produksi</td>
                <td class="separator">:</td>
                <td>{{$produksiSaatini->tanggal_produksi->format('d-m-Y')}}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Kadaluarsa</td>
                <td class="separator">:</td>
                <td>{{$produksiSaatini->tanggal_expired}}</td>

            </tr>
            <tr>
                <td class="label">Batch Size</td>
                <td class="separator">:</td>
                <td>{{$produksiSaatini->batch_size}}</td>
            </tr>
        </table>
        <!-- poroduksi sebelum ini -->
        <table class="w-50 border-0">
            <tr>
                <td>
                    Produksi Sebelum ini
                </td>
            </tr>
            <tr>
                <td class="label">Nama Produk</td>
                <td class="separator">:</td>
                <td>{{$produksiSebelumIni->product->name ?? 'tidak ada'}}</td>
            </tr>
            <tr>
                <td class="label">Batch Number</td>
                <td class="separator">:</td>
                <td>{{$produksiSebelumIni->batch_number ?? 'tidak ada'}}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Produksi</td>
                <td class="separator">:</td>
                <td>{{ $produksiSebelumIni ? $produksiSebelumIni->tanggal_produksi->format('d-m-Y') : 'tidak ada'}}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Kadaluarsa</td>
                <td class="separator">:</td>
                <td>{{$produksiSebelumIni->tanggal_expired ?? 'tidak ada'}}</td>

            </tr>
            <tr>
                <td class="label">Batch Size</td>
                <td class="separator">:</td>
                <td>{{$produksiSebelumIni->batch_size ?? 'tidak ada'}}</td>
            </tr>
        </table>
    </div>
</div>

<div class="mb-4">

    @if(auth()->user()->hasAnyRole(['Producer', 'Production Manager', 'Admin', 'Supervisor']))
    <!-- Nav Tabs -->
    @php
    $tabs = [
    ['id' => 'kebersihan', 'label' => 'Daftar Periksa Kebersihan'],
    ['id' => 'penimbangan', 'label' => 'Penimbangan Bahan Baku'],
    ];
    // Lanjut tab yang lain
    $tabs[] = ['id' => 'produksi', 'label' => 'Pemeriksaan Proses Produksi'];

    // Tambahkan tab khusus jika user punya role Admin
    if (auth()->user()->hasRole('Admin')) {
    $tabs[] = ['id' => 'quality-control', 'label' => 'Quality Control Produksi'];
    }
    $tabs[] = ['id' => 'rekonsiliasi', 'label' => 'Rekonsiliasi Barang'];
    @endphp


    <div class="d-flex overflow-auto">
        <ul class="nav nav-tabs border-0 flex-nowrap" id="myTab" role="tablist">
            @foreach ($tabs as $index => $tab)
            <li class="nav-item" role="presentation">
                <button
                    class="btn btn-small text-capitalize nav-link text-dark nowrap-tab {{ $index == 0 ? 'active' : '' }}"
                    id="{{ $tab['id'] }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $tab['id'] }}" type="button"
                    role="tab" aria-controls="{{ $tab['id'] }}" aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                    {{ $tab['label'] }}
                </button>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- modal -->
    <div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="verificationForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Verifikasi Pengguna</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama QC</label>
                            <input type="text" name="users" id="nama_pic" placeholder="Masukkan Nama QC"
                                class="form-control border mb-3 px-2">
                            <input type="password" name="password_user" id="password_pic" placeholder="password"
                                class="form-control border px-2">
                            @session('error')
                            <div class="alert alert-primary" role="alert">
                                <p>Wrong Password</p>
                            </div>
                            @endsession
                        </div>
                        <!-- check box -->
                        <div class="form-check p-0" id="completeProductionWrapper">
                            <input class="form-check-input" type="checkbox" value="1" id="complete_production"
                                name="complete_production">
                            <label class="form-check-label" for="complete_production">
                                Tandai produksi ini sebagai selesai (done)
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Kirim Verifikasi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="tab-content mt-2" id="myTabContent">
        @include('produksi.tahapan-produksi.kebersihan')
        @include('produksi.tahapan-produksi.penimbangan')
        @include('produksi.tahapan-produksi.proses')
        @include('produksi.tahapan-produksi.quality-control')
        @include('produksi.tahapan-produksi.rekonsiliasi-barang')
    </div>
</div>

@php
$validasiForm = [];
if($produksiSaatini->is_custom_formula === 1){
    $validasiForm = 
            $pemeriksaanKebersihan->count() > 0 && 
            $rekonsiliasiBahanKemas->count() > 0 ;
}else{
    $validasiForm = 
            $pemeriksaanKebersihan->count() > 0 && 
            $pemeriksaanProsesProduksi->count() > 0 && 
            $pemeriksaanPenimbang->count() > 0 && 
            $rekonsiliasiBahanKemas->count() > 0 ;
}

$checkCustomFormula = $produksiSaatini->is_custom_formula != 1; 
@endphp

@if($produksiSaatini->status == 'confirm' && $validasiForm)
<div disabled class="{{ $checkCustomFormula && $pemeriksaanQualityControl->isEmpty() ? 'btn-warning' : 'btn-secondary' }} btn text-center w-100">
    {{ $checkCustomFormula ? $pemeriksaanQualityControl->isEmpty()
        ? 'Menunggu Quality Control untuk mengisi form QC Produksi.'
        : 'Seluruh form telah terisi'
        : 'Seluruh form telah terisi'
    }}
</div>
@endif

<!-- produksi done/close -->
@if(in_array($produksiSaatini->status, ['close', 'done']))
<button class="btn btn-danger w-100" disabled>Produksi {{$produksiSaatini->status}}</button>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pemeriksaanKebersihan = @json($pemeriksaanKebersihan);
    let pemeriksaanPenimbang = @json($pemeriksaanPenimbang);
    const pemeriksaanProsesProduksi = @json($pemeriksaanProsesProduksi);
    const rekonsiliasiBahanKemas = @json($rekonsiliasiBahanKemas);
    const produksiSaatini = @json($produksiSaatini);

    // Modal elements
    const verificationForm = document.getElementById('verificationForm');
    const completeProductionWrapper = document.getElementById('completeProductionWrapper');

    // Default active tab
    let activeTabId = 'kebersihan'; // Default adalah kebersihan
    let activeForm = null;

    // Logika menentukan tab mana yang aktif
    if (pemeriksaanKebersihan.length > 0) {
        activeTabId = 'penimbangan';
    }
    if (pemeriksaanPenimbang.length > 0) {
        activeTabId = 'produksi';
    }
    if (pemeriksaanProsesProduksi.length > 0) {
        activeTabId = 'rekonsiliasi';
    }
    if (rekonsiliasiBahanKemas.length > 0) {
        activeTabId = 'rekonsiliasi';
    }

    // Saat tombol modal diklik
    document.querySelectorAll('.btn-open-verifikasi-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const actionUrl = btn.getAttribute('data-action');
            const source = btn.getAttribute('data-source');

            verificationForm.setAttribute('action', actionUrl);
            if (source === 'rekonsiliasi') {
                completeProductionWrapper.style.display = 'block';
            } else {
                completeProductionWrapper.style.display = 'none';
            }
        });

    });

    // Saat form dikirim
    document.getElementById('verificationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const username = document.getElementById('nama_pic').value;
        const password = document.getElementById('password_pic').value;
        const completeProduction = document.getElementById('complete_production').checked ? 1 : 0;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Kirim data ke server
        fetch(form.getAttribute('action'), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    users: username,
                    password: password,
                    complete_production: completeProduction
                })
            })
            .then(response => {
                return response.json().catch(() => {
                    console.warn('Bukan JSON:', response);
                    throw new Error('Server tidak mengembalikan JSON');
                });
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Verifikasi gagal.');
                }
            })
            .catch(error => {
                console.error('Gagal:', error);
                alert('Terjadi kesalahan saat verifikasi.');
            });

    });

    document.querySelectorAll('.nav-link').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('show', 'active');
    });

    document.getElementById(`${activeTabId}-tab`).classList.add('active');
    document.getElementById(activeTabId).classList.add('show', 'active');
});

function togglePasswordVisibility() {
    const passwordInput = document.getElementById('passwordInput');
    const toggleIcon = document.querySelector('#passwordInput + small');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);

    // change text to hide and show
    toggleIcon.textContent = type === 'password' ? 'show' : 'hide';
}

function toggleEdit(formId) {
    const container = document.getElementById(formId);
    const inputs = container.querySelectorAll('input, textarea');
    const selects = container.querySelectorAll('select');
    const form = container;

    if (typeof window.beforeToggleEdit === 'function') {
        window.beforeToggleEdit(form);
    }

    const btnEdit = container.querySelector('.btn-ubah');
    const btnSave = container.querySelector('.btn-simpan');
    const btnCancel = container.querySelector('.btn-batal');

    form.querySelectorAll('.durasi-ketarangan').forEach(input => {
        if (!input.disabled && input.value === 'Tidak Diketahui') {
            let td = input.closest('td');
            let startBtn = td.querySelector('.keterangan-input');
            if (startBtn) startBtn.classList.remove('d-none');
        }
    });

    form.querySelectorAll("tr[data-index]").forEach(row => {
        const index = row.getAttribute("data-index");
        attachTimerHandlers(row, index);
    });

    inputs.forEach(input => {
        input.removeAttribute('disabled');
        input.classList.add('bg-white');
        input.setAttribute('data-original', input.value);
    });

    selects.forEach(select => {
        select.removeAttribute('disabled');
        select.setAttribute('data-original', select.value);
    });

    container.querySelectorAll(".keterangan-input").forEach(btn => {
        btn.removeAttribute("disabled");
        btn.classList.remove("d-none");
    });

    btnEdit.classList.add('d-none');
    btnSave.classList.remove('d-none');
    btnCancel.classList.remove('d-none');

    // 🔌 HOOK SESUDAH
    if (typeof window.afterToggleEdit === 'function') {
        window.afterToggleEdit(form);
    }
}

function cancelEdit(formId) {
    const container = document.getElementById(formId);
    const inputs = container.querySelectorAll('input, textarea');
    const selects = container.querySelectorAll('select');

    inputs.forEach(input => {
        const original = input.getAttribute('data-original');
        if (original !== null) input.value = original;
        input.setAttribute('disabled', true);
        input.classList.remove('bg-white');
    });

    selects.forEach(select => {
        const original = select.getAttribute('data-original');
        if (original !== null) select.value = original;
        select.setAttribute('disabled', true);
    });

    // Khusus tahapan proses → balikin tombol mulai/stop ke kondisi default
    container.querySelectorAll(".keterangan-input").forEach(btn => {
        btn.classList.add("d-none");
    });
    container.querySelectorAll(".stop-time").forEach(btn => {
        btn.classList.add("d-none");
        btn.disabled = false;
    });

    const btnEdit = container.querySelector('.btn-ubah');
    const btnSave = container.querySelector('.btn-simpan');
    const btnCancel = container.querySelector('.btn-batal');

    btnEdit.classList.remove('d-none');
    btnSave.classList.add('d-none');
    btnCancel.classList.add('d-none');
}
</script>
@endsection