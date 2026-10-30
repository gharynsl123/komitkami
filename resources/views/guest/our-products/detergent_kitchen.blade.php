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

        .product-img {
            max-height: 120px;
            width: auto;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .title {
            font-weight: 700;
            font-size: 1.25rem;
            color: #0d6efd;
        }

        .desc {
            font-size: 0.92rem;
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
    </style>

    <div class="header-backdrop">
        <img src="{{ asset('images/Detergent & Desinfectan Kitchen.png') }}" alt="Detergent & Disinfektan Kitchen">
    </div>

    <div class="container py-5">
        <!-- Judul -->
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill mb-2 fw-semibold" style="background:#e3f2fd;">
                Hospital Kitchen Hygiene
            </span>
            <h2 class="fw-bold text-dark">Detergent & Disinfektan Dapur Medis</h2>
            <h5 class="text-muted fw-light mb-3">Hospital Kitchen & Catering Sanitation</h5>
            <p class="text-secondary mx-auto" style="max-width: 750px; line-height: 1.7;">
                Paket higienitas lengkap untuk dapur gizi rumah sakit dan fasilitas katering profesional.
                Dirancang dengan standar sanitasi medis, menjamin seluruh peralatan makan pasien, piring, gelas,
                dan perlengkapan stainless steel selalu higienis, bebas kuman, serta aman digunakan (food contact safe).
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Pro Enzyme Dish Auto -->
            <div class="col-md-4">
                <div class="product-card border-top border-4 border-primary text-center">
                    <div>
                        <img src="{{ asset('images/Pro Enzyme Dish Auto.png') }}" alt="Pro Enzyme" class="product-img">
                        <h5 class="title text-start">PRO ENZYME DISH AUTO</h5>
                        <p class="desc text-start">
                            Detergent enzim otomatis dengan kekuatan pembersih maksimal untuk menguraikan noda lemak membandel dan residu protein makanan pada mesin dishwasher.
                        </p>
                    </div>
                    <div>
                        <a href="{{ url('our-products/detail/pro-enzyme-dish-auto') }}" class="btn btn-sm btn-primary rounded-pill w-100 py-2">
                            Detail Produk
                        </a>
                    </div>
                </div>
            </div>

            <!-- Rinse Pro Dish Auto -->
            <div class="col-md-4">
                <div class="product-card border-top border-4 border-info text-center">
                    <div>
                        <img src="{{ asset('images/rinse Pro dish auto.png') }}" alt="Rinse Pro" class="product-img">
                        <h5 class="title text-info text-start">RINSE PRO DISH AUTO</h5>
                        <p class="desc text-start">
                            Cairan pembilas otomatis untuk hasil kilau cemerlang tanpa bercak air (water spot), mempercepat pengeringan peralatan makan higienis.
                        </p>
                    </div>
                    <div>
                        <a href="{{ url('our-products/detail/rinse-pro-dish-auto') }}" class="btn btn-sm btn-primary rounded-pill w-100 py-2">
                            Detail Produk
                        </a>
                    </div>
                </div>
            </div>

            <!-- Medic Plate Safe -->
            <div class="col-md-4">
                <div class="product-card border-top border-4 border-success text-center">
                    <div>
                        <img src="{{ asset('images/Medicplatesafe.png') }}" alt="Medic Plate Safe" class="product-img">
                        <h5 class="title text-success text-start">MEDIC PLATE SAFE</h5>
                        <p class="desc text-start">
                            Detergent pencucian piring manual khusus peralatan makan pasien medis, formula busa lembut dan efektif mengikis bau amis serta bakteri.
                        </p>
                    </div>
                    <div>
                        <a href="{{ url('our-products/detail/medic-plate-safe') }}" class="btn btn-sm btn-primary rounded-pill w-100 py-2">
                            Detail Produk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
