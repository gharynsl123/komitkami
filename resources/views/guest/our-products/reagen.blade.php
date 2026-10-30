@extends('welcome')
@section('content')
    <style>
        .header-backdrop {
            position: relative;
            width: 100%;
            height: 70vh;
            overflow: hidden;
        }

        .header-backdrop img {
            width: 100%;
            height: 100%;
            object-position: center 41%;
            object-fit: cover;
            filter: brightness(90%);
        }

        .header-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.3));
        }

        .section-title {
            font-weight: 700;
            text-transform: uppercase;
            color: #003366;
        }

        .card-product {
            border: 1px solid #e6e6e6;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: #fff;
            height: 100%;
        }

        .card-product:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            transform: translateY(-4px);
        }

        .card-product h5 {
            color: #003366;
            font-weight: 600;
        }

        .card-product .badge {
            font-size: 0.8rem;
        }
    </style>

    <!-- Header -->
    <div class="header-backdrop">
        <img src="{{ asset('images/Reggen Patologi Anatomi.png') }}" alt="Produk 1">
        <div class="header-overlay"></div>
    </div>

    <!-- Content -->
    <div class="container py-5">
        <h2 class="text-center section-title mb-3">
            Produk Reagen Histologi & Patologi Anatomi
        </h2>
        <p class="text-center text-muted mb-5">
            Alur lengkap histologi: <strong>Fiksasi → Clearing → Embedding → Pewarnaan Histologi (H&E, Blueing) →
                Pewarnaan Sitologi (Pap stain) → Pewarnaan Diagnostik (BTA)</strong>
        </p>

        <div class="row g-4">
            <!-- Produk 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-product p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h5>KING BTA (Ziehl–Neelsen)</h5>
                        <p><strong>Fungsi:</strong> Identifikasi Basil Tahan Asam (TB, mikobakteri)</p>
                        <p><strong>Aplikasi:</strong> Diagnostik patologi anatomi & mikrobiologi</p>
                        <span class="badge bg-primary mb-3">Botol 100 mL</span>
                    </div>
                    <div>
                        <a href="{{ url('our-products/detail/king-bta') }}" class="btn btn-sm btn-primary rounded-pill w-100">
                            Detail Produk
                        </a>
                    </div>
                </div>
            </div>
            <!-- Produk 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-product p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h5>Buffer Formalin 10%</h5>
                        <p><strong>Fungsi:</strong> Fiksatif jaringan, mengawetkan & menjaga struktur</p>
                        <p><strong>Aplikasi:</strong> Fiksasi jaringan sebelum pemeriksaan histologi</p>
                        <span class="badge bg-primary mb-3">Botol 1 L, Jerigen 5 & 20 L</span>
                    </div>
                    <div>
                        <a href="{{ url('our-products/detail/buffer-formalin-10') }}" class="btn btn-sm btn-primary rounded-pill w-100">
                            Detail Produk
                        </a>
                    </div>
                </div>
            </div>
            <!-- Produk 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-product p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h5>Xylene</h5>
                        <p><strong>Fungsi:</strong> Clearing & dewaxing, melarutkan parafin</p>
                        <p><strong>Aplikasi:</strong> Proses histologi sebelum pewarnaan & mounting</p>
                        <span class="badge bg-primary mb-3">Botol 1 L, Jerigen 5 & 20 L</span>
                    </div>
                    <div>
                        <a href="{{ url('our-products/detail/xylene') }}" class="btn btn-sm btn-primary rounded-pill w-100">
                            Detail Produk
                        </a>
                    </div>
                </div>
            </div>
            <!-- Produk 4 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-product p-4">
                    <h5>Parafin Lilin</h5>
                    <p><strong>Fungsi:</strong> Media infiltrasi & embedding jaringan</p>
                    <p><strong>Aplikasi:</strong> Pembuatan blok jaringan sebelum pemotongan</p>
                    <span class="badge bg-primary">Blok 1 kg, Karton 25 kg</span>
                </div>
            </div>
            <!-- Produk 5 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-product p-4">
                    <h5>Hematoxylin</h5>
                    <p><strong>Fungsi:</strong> Pewarna inti sel (biru/ungu)</p>
                    <p><strong>Aplikasi:</strong> Pewarnaan H&E histologi</p>
                    <span class="badge bg-primary">Botol 100 mL, 500 mL, 1 L</span>
                </div>
            </div>
            <!-- Produk 6 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-product p-4">
                    <h5>Eosin</h5>
                    <p><strong>Fungsi:</strong> Pewarna sitoplasma (merah muda)</p>
                    <p><strong>Aplikasi:</strong> Pewarnaan H&E histologi</p>
                    <span class="badge bg-primary">Botol 100 mL, 500 mL, 1 L</span>
                </div>
            </div>
            <!-- Produk 7 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-product p-4">
                    <h5>Blue Buffer</h5>
                    <p><strong>Fungsi:</strong> Menetralkan & menstabilkan warna hematoxylin</p>
                    <p><strong>Aplikasi:</strong> Tahap blueing setelah hematoxylin</p>
                    <span class="badge bg-primary">Botol 100 mL, 500 mL, 1 L</span>
                </div>
            </div>
            <!-- Produk 8 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-product p-4">
                    <h5>EA (Eosin Azure)</h5>
                    <p><strong>Fungsi:</strong> Diferensiasi sitoplasma sel dalam Pap smear</p>
                    <p><strong>Aplikasi:</strong> Sitologi (Papanicolaou staining)</p>
                    <span class="badge bg-primary">Botol 100 mL, 500 mL, 1 L</span>
                </div>
            </div>
            <!-- Produk 9 -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-product p-4">
                    <h5>Orange G</h5>
                    <p><strong>Fungsi:</strong> Pewarna kontras sitoplasma sel superfisial</p>
                    <p><strong>Aplikasi:</strong> Sitologi (Pap smear)</p>
                    <span class="badge bg-primary">Botol 100 mL, 500 mL, 1 L</span>
                </div>
            </div>
        </div>
    </div>
@endsection