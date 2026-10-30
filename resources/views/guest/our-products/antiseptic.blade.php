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
            object-position: center 50%;
            display: block;
            filter: brightness(50%);
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 24px;
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

        .triangle {
            font-size: 1.8rem;
            line-height: 1;
            margin-right: 8px;
        }

        .title {
            font-weight: bold;
            font-size: 1.4rem;
        }

        .subtitle {
            font-weight: 600;
            font-size: 0.95rem;
            color: #6c757d;
            margin-bottom: 0.8rem;
        }

        .desc {
            font-size: 0.92rem;
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }

        .section-label {
            font-weight: 700;
            font-size: 0.95rem;
            margin-top: 0.8rem;
            margin-bottom: 0.3rem;
        }
    </style>
    <div class="header-backdrop">
        <img src="{{ asset('images/Antiseptic Medical Grade.png') }}" alt="Antiseptic Medical Grade">
    </div>
    <div class="container py-5">
        <!-- Heading -->
        <h2 class="text-center text-success mb-2 fw-bold">Medical Grade Antimicrobial</h2>
        <p class="text-center text-muted mb-5">Standar antiseptik dan cairan pembersih higienis untuk tenaga medis dan fasilitas kesehatan</p>

        <div class="row g-4 justify-content-center">
            <!-- MEDISCRUB -->
            <div class="col-md-6 col-lg-5">
                <div class="product-card border-top border-4 border-danger">
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="triangle text-danger">▲</span>
                            <span class="title">MEDI<span class="text-danger">SCRUB</span></span>
                        </div>
                        <div class="subtitle">Hand Scrub Liquid Antimicrobial 4%</div>
                        <p class="section-label text-dark">Deskripsi:</p>
                        <p class="desc text-secondary">Sabun antiseptik cair khusus untuk mencuci tangan tenaga medis di rumah sakit dan ruang bedah dengan kandungan <strong>Chlorhexidine Gluconate 4%</strong>.</p>

                        <p class="section-label text-dark">Efektivitas:</p>
                        <ul class="desc text-secondary ps-3">
                            <li>Perawatan higienis: 3 ml selama 35 detik.</li>
                            <li>Desinfeksi bedah: 2 tahap, masing-masing 3 ml selama 45 detik.</li>
                            <li>Digunakan dengan bilasan air bersih mengalir.</li>
                        </ul>

                        <p class="desc mb-3"><strong>Kemasan:</strong> Botol 500ml (Pompa), Jerigen 5 Liter</p>
                    </div>

                    <div class="mt-3">
                        <a href="{{ url('our-products/detail/mediscrub') }}" class="btn btn-sm btn-primary rounded-pill px-4 py-2">
                            Detail Produk
                        </a>
                    </div>
                </div>
            </div>

            <!-- MEDISRUB -->
            <div class="col-md-6 col-lg-5">
                <div class="product-card border-top border-4 border-primary">
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="triangle text-primary">▲</span>
                            <span class="title">MEDIS<span class="text-primary">RUB</span></span>
                        </div>
                        <div class="subtitle">Hand Rub Liquid Antimicrobial Tanpa Bilas</div>
                        <p class="section-label text-dark">Deskripsi:</p>
                        <p class="desc text-secondary">Cairan antiseptik tangan cepat kering tanpa bilas dengan formula <strong>Chlorhexidine Gluconate 0,5% + Ethyl Alcohol 70% + Aloe Vera</strong>.</p>

                        <p class="section-label text-dark">Efektivitas:</p>
                        <ul class="desc text-secondary ps-3">
                            <li>Cepat kering 20–30 detik tanpa perlu air.</li>
                            <li>Perawatan higienis dan antisepsis rutin ruang rawat inap.</li>
                            <li>Mengandung moisturizer pelembab agar kulit tetap halus.</li>
                        </ul>

                        <p class="desc mb-3"><strong>Kemasan:</strong> Botol 500ml (Bracket), Jerigen 5 Liter</p>
                    </div>

                    <div class="mt-3">
                        <a href="{{ url('our-products/detail/medisrub') }}" class="btn btn-sm btn-primary rounded-pill px-4 py-2">
                            Detail Produk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
