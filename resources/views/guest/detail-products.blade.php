@extends('welcome')
@section('content')
    <style>
        body {
            font-family: "Inter", sans-serif;
            background: #f8fafc;
            color: #333;
        }

        .hero {
            min-height: 42vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            color: white;
            border-radius: 0 0 30px 30px;
            overflow: hidden;
            z-index: 1;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background: url('{{ $product["photos"][0] ?? "https://via.placeholder.com/1200x600.png?text=Produk" }}') center/cover no-repeat;
            filter: brightness(0.4) blur(1px);
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 30px 24px;
            backdrop-filter: blur(8px);
            background: rgba(0, 0, 0, 0.45);
            border-radius: 16px;
            max-width: 850px;
        }

        .product-detail {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
            padding: 40px;
            margin-top: -50px;
            position: relative;
            z-index: 3;
            margin-bottom: 60px;
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: #0d6efd;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 8px;
        }

        .product-image img {
            max-width: 280px;
            width: 100%;
            height: auto;
            object-fit: contain;
            border-radius: 16px;
            filter: drop-shadow(0 12px 24px rgba(0, 0, 0, 0.1));
            transition: transform 0.3s ease;
        }

        .product-image img:hover {
            transform: translateY(-4px) scale(1.02);
        }

        .highlight-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .warning-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            height: 100%;
        }

        .spec-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 20px;
        }

        .spec-list li {
            position: relative;
            padding-left: 24px;
            margin-bottom: 8px;
            font-size: 0.92rem;
            color: #475569;
            line-height: 1.5;
        }

        .spec-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #0d6efd;
            font-weight: bold;
        }

        .badge-package {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.85rem;
            display: inline-block;
            margin: 0 6px 6px 0;
            border: 1px solid #bae6fd;
        }

        /* Square Clickable Document Cards */
        .doc-card-square {
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 16px;
            text-align: center;
            transition: all 0.25s ease-in-out;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            aspect-ratio: 1 / 1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: relative;
        }

        .doc-card-square:hover {
            border-color: #0d6efd;
            background: #f8faff;
            transform: translateY(-4px);
            box-shadow: 0 10px 22px rgba(13, 110, 253, 0.12);
        }

        .doc-card-square .doc-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.55rem;
            margin-bottom: 10px;
            transition: transform 0.25s ease;
        }

        .doc-card-square:hover .doc-icon-wrap {
            transform: scale(1.12);
        }

        .doc-card-square .doc-title {
            font-size: 0.88rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.25;
            margin-bottom: 4px;
        }

        .doc-card-square .doc-subtitle {
            font-size: 0.75rem;
            color: #64748b;
        }

        .doc-card-square .doc-badge {
            margin-top: 10px;
            font-size: 0.72rem;
            padding: 4px 10px;
            border-radius: 20px;
            background: #f1f5f9;
            color: #475569;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }

        .doc-card-square:hover .doc-badge {
            background: #0d6efd;
            color: #ffffff;
        }

        /* Printable Sheet in Modal */
        .sheet-preview-container {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 30px;
            color: #1e293b;
            font-size: 0.92rem;
        }

        .sheet-preview-container .sheet-header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
    </style>

    <!-- Hero Header -->
    <section class="hero position-relative">
        <div class="hero-bg"></div>
        <div class="container text-center text-white hero-content">
            <span class="badge bg-primary px-3 py-1 rounded-pill mb-2 text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">
                Medical Grade Product
            </span>
            <h1 class="display-5 fw-bold mb-2">{{ $product['title'] }}</h1>
            <p class="lead mb-0 text-white-50 fs-6">{{ $product['subTitle'] }}</p>
        </div>
    </section>

    <!-- Product Detail Container -->
    <div class="container product-detail">
        {{-- Flash message jika form disubmit --}}
        @if(session('success_doc_request'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 text-success me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Permintaan Dokumen Berhasil Dikirim!</h6>
                        <p class="mb-0 small text-secondary">{{ session('success_doc_request') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Breadcrumb & Back button bar --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 pb-2 border-bottom">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ url('/#ourProduct') }}" class="text-decoration-none text-muted">Produk</a></li>
                    <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">{{ $product['title'] }}</li>
                </ol>
            </nav>
            <a href="javascript:history.back()" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        {{-- Overview Row --}}
        <div class="row align-items-center g-4 mb-4">
            <div class="col-md-5 product-image text-center">
                <img src="{{ isset($product['photos'][1]) ? asset('images/' . $product['photos'][1]) : 'https://via.placeholder.com/500x400.png?text=Gambar+Produk' }}"
                    alt="{{ $product['title'] }}">
            </div>
            <div class="col-md-7">
                <h2 class="fw-bold mb-3 text-dark">{{ $product['title'] }}</h2>
                <p class="text-secondary lh-lg mb-3" style="text-align: justify;">{{ $product['desc'] }}</p>

                <h3 class="section-title mt-4">
                    <i class="bi bi-box-seam"></i> Kemasan Tersedia
                </h3>
                <div class="d-flex flex-wrap">
                    @foreach ($product['kemasan'] as $kemasan)
                        <span class="badge-package">
                            <i class="bi bi-check2-circle me-1"></i>{{ $kemasan }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <hr class="my-4 text-muted opacity-25">

        {{-- Details Specifications Grid --}}
        <div class="row g-4">
            <!-- Left Column: Indikasi & Komposisi & Karakteristik -->
            <div class="col-lg-6">
                <div class="info-card">
                    <h3 class="section-title">
                        <i class="bi bi-shield-check"></i> Indikasi Penggunaan
                    </h3>
                    <div class="highlight-box">
                        <i class="bi bi-info-circle-fill me-1"></i> {{ $product['indikasi'] }}
                    </div>

                    <h3 class="section-title mt-4">
                        <i class="bi bi-droplet-half"></i> Komposisi Bahan
                    </h3>
                    <ul class="spec-list">
                        @foreach ($product['komposisi'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>

                    <h3 class="section-title mt-4">
                        <i class="bi bi-stars"></i> Karakteristik & Keunggulan
                    </h3>
                    <ul class="spec-list mb-0">
                        @foreach ($product['karakteristik'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Right Column: Usage & Warnings -->
            <div class="col-lg-6">
                <div class="info-card">
                    <h3 class="section-title">
                        <i class="bi bi-journal-text"></i> Petunjuk Penggunaan
                    </h3>
                    <p class="text-secondary lh-base mb-4">{{ $product['usage'] }}</p>

                    <h3 class="section-title text-danger">
                        <i class="bi bi-exclamation-octagon-fill"></i> Peringatan & Keselamatan
                    </h3>
                    <div class="warning-box mb-4">
                        <i class="bi bi-shield-exclamation me-1"></i> {{ $product['peringatan'] }}
                    </div>

                    {{-- Data Sheet Cards (Square Clickable) --}}
                    <div class="pt-3 border-top">
                        <h4 class="section-title text-dark fs-6 mb-3">
                            <i class="bi bi-file-earmark-arrow-down text-primary"></i> Dokumen & Lembar Data
                        </h4>
                        <div class="row g-3">
                            <!-- Card 1: Safety Data Sheet (SDS) -->
                            <div class="col-6">
                                <div class="doc-card-square" data-bs-toggle="modal" data-bs-target="#modalRequestSDS" role="button" title="Klik untuk meminta file Safety Data Sheet via email">
                                    <div class="doc-icon-wrap" style="background:#fee2e2; color:#dc2626;">
                                        <i class="bi bi-file-earmark-medical-fill"></i>
                                    </div>
                                    <div class="doc-title">Safety Data Sheet</div>
                                    <div class="doc-subtitle">SDS / MSDS</div>
                                    <span class="doc-badge">
                                        <i class="bi bi-envelope-plus"></i> Minta File PDF
                                    </span>
                                </div>
                            </div>

                            <!-- Card 2: Product Data Sheet (PDS) -->
                            <div class="col-6">
                                <div class="doc-card-square" data-bs-toggle="modal" data-bs-target="#modalRequestPDS" role="button" title="Klik untuk meminta file Product Data Sheet via email">
                                    <div class="doc-icon-wrap" style="background:#fef3c7; color:#d97706;">
                                        <i class="bi bi-file-earmark-text-fill"></i>
                                    </div>
                                    <div class="doc-title">Product Data Sheet</div>
                                    <div class="doc-subtitle">PDS / Spesifikasi</div>
                                    <span class="doc-badge">
                                        <i class="bi bi-envelope-plus"></i> Minta File PDF
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- MODAL 1: FORM PERMINTAAN SAFETY DATA SHEET (SDS)              -->
    <!-- ============================================================= -->
    <div class="modal fade" id="modalRequestSDS" tabindex="-1" aria-labelledby="modalRequestSDSLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-medical-fill fs-3 me-2"></i>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="modalRequestSDSLabel">Minta Dokumen SDS</h5>
                            <small class="text-white-50">{{ $product['title'] }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('product.requestDocument') }}" method="POST" class="doc-request-form">
                    @csrf
                    <input type="hidden" name="doc_type" value="Safety Data Sheet (SDS)">
                    <input type="hidden" name="product_title" value="{{ $product['title'] }}">
                    {{-- Anti-Spam: Time Gate --}}
                    <input type="hidden" name="_form_time" value="{{ encrypt(time()) }}">

                    {{-- Anti-Spam: Honeypot Trap (Tersembunyi untuk manusia, otomatis diisi oleh bot) --}}
                    <div style="position: absolute; left: -9999px; top: -9999px; opacity: 0; pointer-events: none; width: 0; height: 0; overflow: hidden;" aria-hidden="true">
                        <label for="company_website_sds">Website</label>
                        <input type="text" id="company_website_sds" name="company_website" tabindex="-1" autocomplete="off" value="">
                    </div>

                    <div class="modal-body p-4 bg-white modal-form-body">
                        <div class="alert alert-light border d-flex align-items-start mb-3 text-secondary small py-2 px-3 rounded-3">
                            <i class="bi bi-info-circle-fill text-danger me-2 fs-5 flex-shrink-0"></i>
                            <div>
                                Silakan lengkapi nama dan email Anda. Admin kami akan segera mengirimkan file PDF <strong>Safety Data Sheet (SDS)</strong> produk ini ke email Anda.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark mb-1">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Masukkan nama lengkap Anda" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark mb-1">
                                Alamat Email Penerima <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="contoh: nama@perusahaan.com" required>
                            <div class="text-muted" style="font-size: 0.75rem; margin-top: 4px;">
                                <i class="bi bi-shield-check text-success me-1"></i> File PDF akan dikirimkan ke alamat email ini.
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-bold small text-dark mb-1">No. WhatsApp / Telp</label>
                                <input type="tel" name="phone" class="form-control rounded-3" placeholder="0812xxxx (opsional)">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-bold small text-dark mb-1">Instansi / Perusahaan</label>
                                <input type="text" name="company" class="form-control rounded-3" placeholder="RS / PT (opsional)">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold small text-dark mb-1">Catatan / Keperluan (Opsional)</label>
                            <textarea name="notes" rows="2" class="form-control rounded-3" placeholder="Tuliskan catatan tambahan jika ada..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-between modal-form-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm fw-bold btn-submit-doc">
                            <i class="bi bi-send-fill me-1"></i> Kirim Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- MODAL 2: FORM PERMINTAAN PRODUCT DATA SHEET (PDS)             -->
    <!-- ============================================================= -->
    <div class="modal fade" id="modalRequestPDS" tabindex="-1" aria-labelledby="modalRequestPDSLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-text-fill fs-3 me-2"></i>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-white" id="modalRequestPDSLabel">Minta Dokumen PDS</h5>
                            <small class="text-white-50">{{ $product['title'] }} — Spesifikasi Teknis</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('product.requestDocument') }}" method="POST" class="doc-request-form">
                    @csrf
                    <input type="hidden" name="doc_type" value="Product Data Sheet (PDS)">
                    <input type="hidden" name="product_title" value="{{ $product['title'] }}">
                    {{-- Anti-Spam: Time Gate --}}
                    <input type="hidden" name="_form_time" value="{{ encrypt(time()) }}">

                    {{-- Anti-Spam: Honeypot Trap (Tersembunyi untuk manusia, otomatis diisi oleh bot) --}}
                    <div style="position: absolute; left: -9999px; top: -9999px; opacity: 0; pointer-events: none; width: 0; height: 0; overflow: hidden;" aria-hidden="true">
                        <label for="company_website_pds">Website</label>
                        <input type="text" id="company_website_pds" name="company_website" tabindex="-1" autocomplete="off" value="">
                    </div>

                    <div class="modal-body p-4 bg-white modal-form-body">
                        <div class="alert alert-light border d-flex align-items-start mb-3 text-secondary small py-2 px-3 rounded-3">
                            <i class="bi bi-info-circle-fill text-warning me-2 fs-5 flex-shrink-0"></i>
                            <div>
                                Silakan lengkapi nama dan email Anda. Admin kami akan segera mengirimkan file PDF <strong>Product Data Sheet (PDS)</strong> produk ini ke email Anda.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark mb-1">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Masukkan nama lengkap Anda" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark mb-1">
                                Alamat Email Penerima <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="contoh: nama@perusahaan.com" required>
                            <div class="text-muted" style="font-size: 0.75rem; margin-top: 4px;">
                                <i class="bi bi-shield-check text-success me-1"></i> File PDF akan dikirimkan ke alamat email ini.
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-bold small text-dark mb-1">No. WhatsApp / Telp</label>
                                <input type="tel" name="phone" class="form-control rounded-3" placeholder="0812xxxx (opsional)">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-bold small text-dark mb-1">Instansi / Perusahaan</label>
                                <input type="text" name="company" class="form-control rounded-3" placeholder="RS / PT (opsional)">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold small text-dark mb-1">Catatan / Keperluan (Opsional)</label>
                            <textarea name="notes" rows="2" class="form-control rounded-3" placeholder="Tuliskan catatan tambahan jika ada..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-between modal-form-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 shadow-sm fw-bold btn-submit-doc">
                            <i class="bi bi-send-fill me-1"></i> Kirim Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script AJAX Handler untuk Form Permintaan Dokumen -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('.doc-request-form');

            forms.forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const submitBtn = form.querySelector('.btn-submit-doc');
                    const originalBtnContent = submitBtn.innerHTML;
                    const modalBody = form.querySelector('.modal-form-body');
                    const modalFooter = form.querySelector('.modal-form-footer');

                    // Disable button and show spinner
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mengirim...';

                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Terjadi kesalahan saat memproses permintaan.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Tampilkan pesan sukses di dalam modal
                        modalBody.innerHTML = `
                            <div class="text-center py-4 px-2">
                                <div class="mb-3">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; background: #dcfce7; color: #16a34a; font-size: 2.2rem;">
                                        <i class="bi bi-check-lg"></i>
                                    </span>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Permintaan Berhasil Dikirim!</h5>
                                <p class="text-secondary small mb-4 lh-base">
                                    Terima kasih, <strong>${data.name || 'Bpk/Ibu'}</strong>.<br>
                                    Permintaan dokumen <strong>${data.doc_type || 'PDF'}</strong> untuk produk <strong>${formData.get('product_title')}</strong> telah kami terima.<br>
                                    Tim admin kami akan segera mengirimkan file PDF ke email <strong>${data.email}</strong>.
                                </p>
                                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">
                                    Selesai
                                </button>
                            </div>
                        `;
                        // Sembunyikan footer lama
                        if (modalFooter) {
                            modalFooter.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Kembalikan tombol ke keadaan semula
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnContent;

                        // Tampilkan alert error di atas form
                        let errorAlert = modalBody.querySelector('.alert-submit-error');
                        if (!errorAlert) {
                            errorAlert = document.createElement('div');
                            errorAlert.className = 'alert alert-danger alert-submit-error small py-2 px-3 rounded-3 mb-3';
                            modalBody.prepend(errorAlert);
                        }
                        errorAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Maaf, terjadi kendala saat mengirim permintaan. Silakan coba lagi atau hubungi kontak WhatsApp kami.';
                    });
                });
            });
        });
    </script>
@endsection
