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
            object-fit: cover;
            object-position: center 40%;
            display: block;
            filter: brightness(50%);
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 22px;
            background: #fff;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .title {
            font-weight: 700;
            font-size: 1.25rem;
            color: #198754;
        }

        .desc {
            font-size: 0.92rem;
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .check-item {
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .check-item::before {
            content: "✔";
            color: #198754;
            margin-right: 8px;
            font-weight: bold;
        }
    </style>
    <div class="header-backdrop">
        <img src="{{ asset('images/Alkohol 70, 80, 96, 98.png') }}" alt="Alkohol Medis">
    </div>
    <div class="container py-5">
        <div class="row g-4 mb-5 align-items-center">
            <div class="col-lg-6">
                <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill mb-2 fw-semibold" style="background:#e8f5e9;">
                    Medical & Laboratory Grade
                </span>
                <h1 class="text-success fw-bold">Alkosept</h1>
                <h3 class="text-muted fw-light mb-3">Alkohol Murni Medis & Farmasi</h3>
                <p class="text-secondary lh-lg" style="text-align: justify;">
                    Produk <strong>Alkosept</strong> adalah alkohol murni dengan berbagai tingkatan kadar formulasi khusus untuk kebutuhan medis, desinfeksi, sterilisasi klinik, laboratorium analitis, maupun industri farmasi. Diproduksi dengan standar kemurnian tinggi menjamin keamanan dan efektivitas.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="p-4 bg-light rounded-4 border">
                    <h5 class="fw-bold mb-3 text-dark">Keunggulan Utama Alkosept:</h5>
                    <div class="check-item"><strong>Kualitas Medical Grade</strong> sesuai standar Farmakope Indonesia</div>
                    <div class="check-item"><strong>Kemurnian Tinggi</strong> tanpa residu kimia atau noda berbahaya</div>
                    <div class="check-item"><strong>Konsistensi Kadar Teruji</strong> laboratorium terakreditasi nasional</div>
                    <div class="check-item"><strong>Kemasan Segel Higienis</strong> botol 1 Liter dan jerigen 5 Liter</div>
                </div>
            </div>
        </div>

        <h3 class="fw-bold mb-4 text-center text-dark">Pilihan Varian Kadar Alkosept</h3>

        <div class="row g-4">
            <!-- Alkosept 70% -->
            <div class="col-sm-6 col-lg-3">
                <div class="product-card border-top border-4 border-success">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="title">Alkosept 70%</span>
                            <span class="badge bg-success">Antiseptik</span>
                        </div>
                        <p class="desc">
                            Standar emas antiseptik kulit sebelum injeksi dan disinfektan permukaan alat non-kritis. Efektif denaturasi protein bakteri dan virus.
                        </p>
                    </div>
                    <div>
                        <a href="{{ url('our-products/detail/alkosept-70') }}" class="btn btn-sm btn-primary rounded-pill w-100 py-2">
                            Detail Produk
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alkosept 80% -->
            <div class="col-sm-6 col-lg-3">
                <div class="product-card border-top border-4 border-primary">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="title text-primary">Alkosept 80%</span>
                            <span class="badge bg-primary">Fast Kill</span>
                        </div>
                        <p class="desc">
                            Sesuai anjuran formulasi WHO untuk daya bunuh mikroorganisme lebih cepat pada area perawatan intensif (ICU/isolasi).
                        </p>
                    </div>
                    <div>
                        <a href="{{ url('our-products/detail/alkosept-80') }}" class="btn btn-sm btn-primary rounded-pill w-100 py-2">
                            Detail Produk
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alkosept 96% -->
            <div class="col-sm-6 col-lg-3">
                <div class="product-card border-top border-4 border-info">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="title text-info">Alkosept 96%</span>
                            <span class="badge bg-info text-dark">Analitis</span>
                        </div>
                        <p class="desc">
                            Kemurnian tinggi untuk keperluan analisis laboratorium, pelarut reagen kimia, ekstraksi, dan sterilisasi instrumen presisi.
                        </p>
                    </div>
                    <div>
                        <a href="{{ url('our-products/detail/alkosept-96') }}" class="btn btn-sm btn-primary rounded-pill w-100 py-2">
                            Detail Produk
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alkosept 98% -->
            <div class="col-sm-6 col-lg-3">
                <div class="product-card border-top border-4 border-dark">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="title text-dark">Alkosept 98%</span>
                            <span class="badge bg-dark">Absolut</span>
                        </div>
                        <p class="desc">
                            Alkohol absolut kadar tertinggi, diformulasikan khusus untuk laboratorium riset, sintesis farmasi, dan kromatografi analitik.
                        </p>
                    </div>
                    <div>
                        <a href="{{ url('our-products/detail/alkosept-98') }}" class="btn btn-sm btn-primary rounded-pill w-100 py-2">
                            Detail Produk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
