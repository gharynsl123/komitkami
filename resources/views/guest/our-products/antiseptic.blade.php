@extends('welcome')
@section('content')
    <style>
        .header-backdrop {
            position: relative;
            width: 100%;
            height: 75vh;
            overflow: hidden;
        }

        .header-backdrop img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* mengatur posisi gambaragak ke atas */
            object-position: center 50%;
            display: block;
            filter: brightness(50%);
        }

        .product-box {
            border-left: 2px solid;
            padding: 20px;
            background: #fff;
            height: 100%;
        }

        .triangle {
            font-size: 2rem;
            line-height: 1;
            margin-right: 8px;
        }

        .title {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .subtitle {
            font-weight: 500;
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .desc {
            font-size: 0.95rem;
            margin-bottom: 0.6rem;
        }

        .section-title {
            font-weight: bold;
            margin-top: 1rem;
        }
    </style>
    <div class="header-backdrop">
        <img src="{{ asset('images/Antiseptic Medical Grade.png') }}" alt="Produk 1">
    </div>
    <div class="container py-5">
        <!-- Heading -->
        <h2 class="text-center text-success mb-5">Medical Grade Antimicrobial</h2>
        <div class="row g-4">
            <!-- MEDISCRUB -->
            <div class="col-md-6">
                <div class="product-box border-danger">
                    <div class="d-flex align-items-center mb-2">
                        <span class="triangle text-danger">▲</span>
                        <span class="title">MEDI<span class="text-danger">SCRUB</span></span>
                    </div>
                    <div class="subtitle">Hand Scrub Liquid Antimicrobial</div>
                    <p class="section-title">Deskripsi</p>
                    <p class="desc">Sabun antiseptik cair untuk mencuci tangan tenaga medis di rumah sakit.</p>
                    <p class="desc"><strong>Mengandung zat aktif Chlorhexidine Gluconate 4%.</strong></p>
                    <p class="desc">Mengandung pelembab, nyaman di kulit, serta memiliki sifat antibakteri.</p>

                    <p class="section-title">Efektivitas</p>
                    <ul class="desc">
                        <li>Perawatan higienis: 3 ml selama 35 detik.</li>
                        <li>Desinfeksi bedah: 2 tahap, masing-masing 3 ml selama 45 detik.</li>
                        <li>Digunakan dengan bilasan air bersih.</li>
                    </ul>

                    <p class="desc"><strong>Kemasan:</strong> 500ml, 5 Liter</p>
                </div>
            </div>

            <!-- MEDISRUB -->
            <div class="col-md-6">
                <div class="product-box border-primary">
                    <div class="d-flex align-items-center mb-2">
                        <span class="triangle text-primary">▲</span>
                        <span class="title">MEDIS<span class="text-primary">RUB</span></span>
                    </div>
                    <div class="subtitle">Hand Rub Liquid Antimicrobial</div>
                    <p class="section-title">Deskripsi</p>
                    <p class="desc">Cairan antiseptik untuk tangan yang digunakan oleh tenaga medis di rumah sakit.</p>
                    <p class="desc"><strong>Mengandung Chlorhexidine Gluconate 0,5%, Ethyl Alcohol, dan Aloe Vera</strong>,
                        efektif membunuh kuman sekaligus melembutkan kulit.</p>

                    <p class="section-title">Efektivitas</p>
                    <ul class="desc">
                        <li>Tanpa bilas, digunakan langsung tanpa air.</li>
                        <li>Perawatan higienis: 3 ml selama 35 detik.</li>
                        <li>Desinfeksi bedah: 2 tahap, masing-masing 3 ml selama 45 detik.</li>
                        <li>Mengandung moisturizer untuk kenyamanan kulit.</li>
                    </ul>

                    <p class="desc"><strong>Kemasan:</strong> 500ml, 5 Liter</p>
                </div>
            </div>
        </div>
    </div>
@endsection