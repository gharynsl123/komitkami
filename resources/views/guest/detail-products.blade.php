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
                                <div class="doc-card-square" data-bs-toggle="modal" data-bs-target="#modalSDS" role="button" title="Klik untuk preview & unduh Safety Data Sheet">
                                    <div class="doc-icon-wrap" style="background:#fee2e2; color:#dc2626;">
                                        <i class="bi bi-lock-fill"></i>
                                    </div>
                                    <div class="doc-title">Safety Data Sheet</div>
                                    <div class="doc-subtitle">SDS / MSDS</div>
                                    <span class="doc-badge">
                                        <i class="bi bi-eye"></i> Preview & Unduh
                                    </span>
                                </div>
                            </div>

                            <!-- Card 2: Product Data Sheet (PDS) -->
                            <div class="col-6">
                                <div class="doc-card-square" data-bs-toggle="modal" data-bs-target="#modalPDS" role="button" title="Klik untuk preview & unduh Product Data Sheet">
                                    <div class="doc-icon-wrap" style="background:#fef3c7; color:#d97706;">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                    </div>
                                    <div class="doc-title">Product Data Sheet</div>
                                    <div class="doc-subtitle">PDS / Spesifikasi</div>
                                    <span class="doc-badge">
                                        <i class="bi bi-eye"></i> Preview & Unduh
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
    <!-- MODAL 1: PREVIEW & DOWNLOAD SAFETY DATA SHEET (SDS)           -->
    <!-- ============================================================= -->
    <div class="modal fade" id="modalSDS" tabindex="-1" aria-labelledby="modalSDSLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-lock-fill fs-4 me-2"></i>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="modalSDSLabel">Safety Data Sheet (SDS)</h5>
                            <small class="text-white-50">{{ $product['title'] }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- Preview Container yang dapat dicetak / diunduh -->
                    <div class="sheet-preview-container shadow-sm" id="sdsContentArea">
                        <div class="sheet-header d-flex justify-content-between align-items-center">
                            <div>
                                <img src="{{ public_asset('images/KING-DATAR.png') }}" alt="Komitkami" height="38" class="mb-2">
                                <h4 class="fw-bold mb-0 text-dark">LEMBAR DATA KESELAMATAN BAHAN (SDS)</h4>
                                <small class="text-muted">Standard GHS & Kementerian Kesehatan RI</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-danger px-3 py-2 fs-6">CONFIDENTIAL / CONTROLLED</span>
                                <div class="text-muted small mt-1">Ref: SDS-{{ strtoupper(Str::slug($product['title'])) }}</div>
                            </div>
                        </div>

                        <!-- ========================================================== -->
                        <!-- KONTEN SAFETY DATA SHEET (Silakan isi / sesuaikan konten)   -->
                        <!-- ========================================================== -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-danger border-bottom pb-1">1. IDENTIFIKASI SENYAWA / PRODUK</h5>
                            <table class="table table-sm table-bordered mt-2">
                                <tr>
                                    <th style="width: 30%;">Nama Produk</th>
                                    <td><strong>{{ $product['title'] }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Kategori / Fungsi</th>
                                    <td>{{ $product['subTitle'] }}</td>
                                </tr>
                                <tr>
                                    <th>Produsen</th>
                                    <td>PT Komitkami Intinusa Gemilang</td>
                                </tr>
                                <tr>
                                    <th>Alamat Pabrik</th>
                                    <td>Komp. Bizpark 3, Jl. Sultan Agung, RT.003/RW.005, Kali Baru, Medan Satria, Kota Bekasi</td>
                                </tr>
                                <tr>
                                    <th>Kontak Darurat</th>
                                    <td>(021) 8946 3330 / +62 812-9123-8909</td>
                                </tr>
                            </table>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-danger border-bottom pb-1">2. IDENTIFIKASI BAHAYA</h5>
                            <p class="mb-2">{{ $product['peringatan'] }}</p>
                            <ul>
                                <li><strong>Klasifikasi Bahaya:</strong> Iritasi mata ringan hingga sedang (Kategori 2B), Non-korosif pada penggunaan terencerkan.</li>
                                <li><strong>Kata Sinyal:</strong> PERINGATAN (WARNING).</li>
                                <li><strong>Pernyataan Bahaya:</strong> Hindari kontak langsung tanpa APD pada larutan konsentrat. Hanya untuk penggunaan profesional.</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-danger border-bottom pb-1">3. KOMPOSISI / BAHAN PENYUSUN</h5>
                            <table class="table table-sm table-bordered mt-2">
                                <thead>
                                    <tr class="table-light">
                                        <th>Nama Komponen Bahan</th>
                                        <th>Fungsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product['komposisi'] as $komp)
                                        <tr>
                                            <td>{{ $komp }}</td>
                                            <td>Bahan Aktif / Penstabil / Formula Medis</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-danger border-bottom pb-1">4. TINDAKAN PERTOLONGAN PERTAMA (FIRST AID)</h5>
                            <ul>
                                <li><strong>Kontak Mata:</strong> Segera basuh mata dengan air bersih mengalir selama minimal 15 menit dengan kelopak mata terbuka. Jika iritasi berlanjut, hubungi dokter.</li>
                                <li><strong>Kontak Kulit:</strong> Cuci bagian yang terpapar dengan air dan sabun lembut.</li>
                                <li><strong>Tertelan:</strong> Bilas mulut dengan air bersih. Jangan memaksakan muntah kecuali atas instruksi tenaga medis. Segera bawa ke IGD.</li>
                                <li><strong>Terhirup:</strong> Pindahkan korban ke area dengan udara segar.</li>
                            </ul>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-danger border-bottom pb-1">5. PENANGANAN DAN PENYIMPANAN</h5>
                            <p>
                                Simpan dalam wadah asli tertutup rapat di tempat yang sejuk, kering, dan berventilasi baik antara suhu +5°C hingga +35°C. Jauhkan dari sinar matahari langsung, sumber panas, dan jangkauan anak-anak.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Tutup
                    </button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 shadow-sm" onclick="downloadSheet('sdsContentArea', 'Safety-Data-Sheet-{{ Str::slug($product['title']) }}')">
                        <i class="bi bi-download me-1"></i> Unduh / Cetak SDS (PDF)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- MODAL 2: PREVIEW & DOWNLOAD PRODUCT DATA SHEET (PDS)          -->
    <!-- ============================================================= -->
    <div class="modal fade" id="modalPDS" tabindex="-1" aria-labelledby="modalPDSLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-dark">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-2 text-dark"></i>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="modalPDSLabel">Product Data Sheet (PDS)</h5>
                            <small class="text-muted">{{ $product['title'] }} — Lembar Spesifikasi Teknis</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- Preview Container yang dapat dicetak / diunduh -->
                    <div class="sheet-preview-container shadow-sm" id="pdsContentArea">
                        <div class="sheet-header d-flex justify-content-between align-items-center">
                            <div>
                                <img src="{{ public_asset('images/KING-DATAR.png') }}" alt="Komitkami" height="38" class="mb-2">
                                <h4 class="fw-bold mb-0 text-primary">PRODUCT TECHNICAL DATA SHEET (PDS)</h4>
                                <small class="text-muted">PT Komitkami Intinusa Gemilang — Medical Device Division</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary px-3 py-2 fs-6">TECHNICAL SPEC</span>
                                <div class="text-muted small mt-1">Doc: PDS-{{ strtoupper(Str::slug($product['title'])) }}</div>
                            </div>
                        </div>

                        <!-- ========================================================== -->
                        <!-- KONTEN PRODUCT DATA SHEET (Silakan isi / sesuaikan konten) -->
                        <!-- ========================================================== -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary border-bottom pb-1">1. DESKRIPSI & SPESIFIKASI UMUM</h5>
                            <p class="lh-base" style="text-align: justify;">{{ $product['desc'] }}</p>
                            <table class="table table-sm table-bordered mt-2">
                                <tr>
                                    <th style="width: 30%;">Nama Produk</th>
                                    <td><strong>{{ $product['title'] }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Klasifikasi</th>
                                    <td>{{ $product['subTitle'] }}</td>
                                </tr>
                                <tr>
                                    <th>Indikasi</th>
                                    <td>{{ $product['indikasi'] }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-primary border-bottom pb-1">2. KARAKTERISTIK & KEUNGGULAN PRODUK</h5>
                            <ul>
                                @foreach ($product['karakteristik'] as $karakter)
                                    <li>{{ $karakter }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-primary border-bottom pb-1">3. PETUNJUK PENGGUNAAN & DOSIS APLIKASI</h5>
                            <div class="p-3 bg-light rounded border mb-2">
                                {{ $product['usage'] }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold text-primary border-bottom pb-1">4. KEMASAN & INFORMASI PENYIMPANAN</h5>
                            <p class="mb-1"><strong>Varian Kemasan Resmi:</strong></p>
                            <ul class="mb-2">
                                @foreach ($product['kemasan'] as $kemasan)
                                    <li>{{ $kemasan }}</li>
                                @endforeach
                            </ul>
                            <p class="mb-0 text-muted"><strong>Standar Kualitas:</strong> Diproduksi di bawah standar jaminan mutu CPAKB dan Kemenkes RI.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Tutup
                    </button>
                    <button type="button" class="btn btn-warning rounded-pill px-4 shadow-sm fw-bold" onclick="downloadSheet('pdsContentArea', 'Product-Data-Sheet-{{ Str::slug($product['title']) }}')">
                        <i class="bi bi-download me-1"></i> Unduh / Cetak PDS (PDF)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Download & Print Sheet -->
    <script>
        function downloadSheet(elementId, title) {
            const printContent = document.getElementById(elementId).innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>${title}</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body {
                            padding: 30px;
                            font-family: Arial, sans-serif;
                            color: #1e293b;
                            background: #ffffff;
                        }
                        .sheet-preview-container { border: none !important; padding: 0 !important; }
                        table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
                        th, td { border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 0.85rem; }
                        th { background-color: #f1f5f9; }
                        @media print {
                            body { padding: 0; }
                            @page { margin: 15mm; size: A4 portrait; }
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                    <script>
                        window.onload = function() {
                            window.focus();
                            window.print();
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
    </script>
@endsection
