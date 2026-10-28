@extends('welcome')
@section('content')
    <style>
        .product-card {
            border-radius: 1rem;
            background: #fff;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            /* Biar tombol bisa nempel bawah */
            height: 100%;
            /* Biar seragam */
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
        }

        .product-card .p-3 {
            flex: 1;
            /* isi teks jadi fleksibel */
            display: flex;
            flex-direction: column;
        }

        .product-title {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 1rem;
            min-height: 42px;
            /* Paksa judul min 2 baris */
        }

        .product-description {
            font-size: 0.875rem;
            color: #6c757d;
            flex: 1;
            /* biar isi ini ngisi ruang kosong */
            margin-bottom: 10px;
            min-height: 40px;
            /* Biar rata kalau teks pendek */
        }

        .product-card a {
            align-self: flex-start;
            /* tombol tetap rapi kiri */
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            gap: 2rem;
            position: relative;
            flex-wrap: wrap;
        }

        .progress-steps::before {
            content: "";
            position: absolute;
            top: 25px;
            left: 0;
            right: 0;
            height: 4px;
            background: #e0e0e0;
            z-index: 1;
        }

        .step {
            flex: 1;
            min-width: 220px;
            text-align: center;
            position: relative;
        }

        .circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #0d6efd;
            color: white;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .step h5 {
            margin-top: 15px;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .progress-steps {
                flex-direction: column;
                align-items: center;
            }

            .progress-steps::before {
                content: none;
            }

            .step {
                margin-bottom: 2rem;
            }
        }
    </style>
    <section class="hero d-flex align-items-center" id="beranda"
        style="min-height: 90vh; background: linear-gradient(135deg, #BDE1FA 0%, #ffffff 100%);">
        <div class="container">
            <div class="row align-items-center g-5">
                <!-- Teks -->
                <div class="col-lg-6">
                    <h1 class="fw-bold display-5 mb-3" data-aos="fade-right" data-aos-delay="300">
                        Solusi Inovatif untuk<br><span class="text-primary">Industri Kesehatan</span>
                    </h1>
                    <p class="lead text-secondary mb-4" data-aos="fade-right" data-aos-delay="500">
                        Kami menghadirkan produk berkualitas dengan harga terjangkau,
                        mendukung registrasi kesehatan & distribusi cepat untuk mendukung pertumbuhan bisnis Anda.
                    </p>
                    <div class="d-flex gap-3" data-aos="fade-right" data-aos-delay="700">
                        <a href="#contact" class="btn btn-primary px-4 py-2 rounded-pill shadow">
                            Hubungi Kami
                        </a>
                        <a href="#produk" class="btn btn-outline-primary px-4 py-2 rounded-pill">
                            Lihat Produk
                        </a>
                    </div>
                </div>

                <!-- Gambar -->
                <div class="col-lg-6 text-center">
                    <img src="{{asset('images/inventasi.png')}}" alt="Strategi Investasi" class="img-fluid"
                        data-aos="fade-left" data-aos-delay="500" style="max-height: 420px; border-radius: 20px;">
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 mb-5  bg-light" id="produksi">
        <div class="container">
            <!-- Bagian judul -->
            <div class="text-lg-start text-center mb-4">
                <h1 class="fw-bold display-6">Company Profile</h1>
            </div>
            <div class="row align-items-center g-4">
                <!-- Kolom video -->
                <div class="col-12 col-lg-6">
                    <div class="ratio ratio-16x9 shadow rounded overflow-hidden">
                        <iframe src="https://www.youtube.com/embed/tqz9did4mgg?si=i_usINNQqJYUxo6r"
                            title="Komitkami Intinusa Gemilang" frameborder="0"
                            allow="clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                        </iframe>
                    </div>
                </div>

                <!-- Kolom teks -->
                <div class="col-12 col-lg-6">
                    <h3 class="fw-semibold mb-3">{!! nl2br(e(__('how_we_make_it'))) !!}</h3>
                    <p class="text-muted lh-lg text-justify">
                        {!! nl2br(e(__('how_we_make_it_description'))) !!}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <sectoin class="py-5 mb-5">
        <h4 class="text-primary text-center fw-semibold mb-2">{{__('high')}}</h4>
        <h2 class="fw-bold text-center mb-4">{{__('quality_products')}}</h2>
        <div class="progress-steps">
            <!-- Step 1 -->
            <div class="step">
                <div class="circle"><i class="bi bi-hospital"></i></div>
                <h5 class="mt-3 fw-semibold">Kebutuhan Medis</h5>
                <p class="text-muted">
                    Konsep dan ide produk dengan kualitas tinggi kami ciptakan
                    untuk memenuhi kebutuhan medis di Rumah Sakit.
                </p>
            </div>
            <!-- Step 2 -->
            <div class="step">
                <div class="circle"><i class="bi bi-eyedropper"></i></div>
                <h5 class="mt-3 fw-semibold">Uji Laboratorium</h5>
                <p class="text-muted">
                    Produk yang kami produksi melalui tahap pengujian secara berkala.
                </p>
            </div>
            <!-- Step 3 -->
            <div class="step">
                <div class="circle"><i class="bi bi-file-earmark-text"></i></div>
                <h5 class="mt-3 fw-semibold">Registrasi</h5>
                <p class="text-muted">
                    Produk dibuat dengan dokumen yang lengkap sebagai pendukung registrasi
                    dan ekatalog.
                </p>
            </div>
            <!-- Step 4 -->
            <div class="step">
                <div class="circle"><i class="bi bi-patch-check"></i></div>
                <h5 class="mt-3 fw-semibold">TKDN</h5>
                <p class="text-muted">
                    Bahan baku yang kami dapatkan mendukung untuk sertifikat TKDN yang tinggi.
                </p>
            </div>
            <!-- Step 5 -->
            <div class="step">
                <div class="circle"><i class="bi bi-lightbulb"></i></div>
                <h5 class="mt-3 fw-semibold">Pengembangan</h5>
                <p class="text-muted">
                    Produk terbaru akan terus ditawarkan untuk bisa terus berkompetisi.
                </p>
            </div>
        </div>
    </sectoin>

    <section class="py-5 my-5 bg-light" id="nilai">
        <div class="container d-flex justify-content-between align-items-center flex-lg-row flex-column">
            <img src="{{ asset('images/nilai-kami.png') }}" class="div-hover" alt="nilai kami">
            <div>
                <h2 class="fw-light m-0">{{ __('what_differentiates_us') }}</h2>
                <p class="mt-3" style="text-align:justify;">
                    {!! nl2br(e(__('what_differentiates_us_description'))) !!}
                </p>
            </div>
        </div>
    </section>

    <section class="container" id="ourProduct">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">Produk Unggulan Kami</h3>
                <hr class="mt-2 mb-0" style="width: 120px; border: 2px solid #0d6efd; border-radius: 4px;">
            </div>
            <img src="{{asset('images/KING BULET.png')}}" alt="Logo" style="width:8%; max-width: 80px;">
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card shadow-sm">
                    <img src="{{asset('images/Liquid for Reprocessing CSSD.png')}}" alt="Produk 1">
                    <div class="p-3">
                        <h6 class="product-title">Liquid for Reprocessing CSSD</h6>
                        <p class="product-description">Detergent for Manual Wash or WD & Disinfectant Treatment</p>
                        <a href="{{url('/our-products/liquid-for-reprocessing-cssd')}}" class="btn btn-sm rounded-pill mt-3"
                            style="background-color: #e3f2fd; font-weight:500;">Ketahui Lebih Lanjut</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card shadow-sm">
                    <img src="{{asset('images/Antiseptic Medical Grade.png')}}" alt="Produk 2">
                    <div class="p-3">
                        <h6 class="product-title">Antiseptic Medical Grade</h6>
                        <p class="product-description">Hand Sanitizer MedisRub & MediScrub Antiseptic Surgery</p>
                        <a href="{{url('/our-products/antiseptic-medical-grade')}}" class="btn btn-sm rounded-pill mt-3"
                            style="background-color: #e3f2fd; font-weight:500;">Ketahui Lebih Lanjut</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card shadow-sm">
                    <img src="{{asset('images/Detergent for Laundry.png')}}" alt="Produk 3">
                    <div class="p-3">
                        <h6 class="product-title">Detergent for Laundry</h6>
                        <p class="product-description">Medical Grade Detergent for Laundry</p>
                        <a href="{{url('/our-products/detergent-for-laundry')}}" class="btn btn-sm rounded-pill mt-3"
                            style="background-color: #e3f2fd; font-weight:500;">Ketahui Lebih Lanjut</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card shadow-sm">
                    <img src="{{asset('images/Alkohol 70, 80, 96, 98.png')}}" alt="Produk 4">
                    <div class="p-3">
                        <h6 class="product-title">Alkohol 70, 80, 96, 98</h6>
                        <p class="product-description">Alkohol Murni untuk Kebutuhan Medis & Laboratorium</p>
                        <a href="{{url('/our-products/alkohol-70-80-96-98')}}" class="btn btn-sm rounded-pill mt-3"
                            style="background-color: #e3f2fd; font-weight:500;">Ketahui Lebih Lanjut</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card shadow-sm">
                    <img src="{{asset('images/Reggen Patologi Anatomi.png')}}" alt="Produk 5">
                    <div class="p-3">
                        <h6 class="product-title">Reagen Patologi Anatomi</h6>
                        <p class="product-description">Solusi Lengkap untuk Laboratorium Histopatologi</p>
                        <a href="{{url('/our-products/reagen-patologi-anatomi')}}" class="btn btn-sm rounded-pill mt-3"
                            style="background-color: #e3f2fd; font-weight:500;">Ketahui Lebih Lanjut</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card shadow-sm">
                    <img src="{{asset('images/Detergent & Desinfectan Kitchen.png')}}" alt="Produk 6">
                    <div class="p-3">
                        <h6 class="product-title">Detergent & Disinfetan Kitchen</h6>
                        <p class="product-description">Perlindungan Maksimal untuk Dapur Steril</p>
                        <a href="{{url('/our-products/detergent-disinfetan-kitchen')}}" class="btn btn-sm rounded-pill mt-3"
                            style="background-color: #e3f2fd; font-weight:500;">Ketahui Lebih Lanjut</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card shadow-sm">
                    <img src="{{asset('images/Ultrasound Transmission Gel.png')}}" alt="Produk 7">
                    <div class="p-3">
                        <h6 class="product-title">Ultrasound Transmission Gel</h6>
                        <p class="product-description">Kejernihan Gelombang untuk Diagnosa Akurat</p>
                        <a href="{{url('/our-products/ultrasound-transmission-gel')}}" class="btn btn-sm rounded-pill mt-3"
                            style="background-color: #e3f2fd; font-weight:500;">Ketahui Lebih Lanjut</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="mt-5 pb-5 bg-light" id="siklus">
        <div class="container">
            <div class="row g-4 mt-5">
                <div class="col-md-6">
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('images/siklus.png') }}" alt="" width="70%" height="100%" srcset=""
                            class="div-hover">
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div>
                        <h1 class="m-0">{{ __('note') }}</h1>
                        <p class="mt-3" style="text-align: justify;">{!! nl2br(e(__('note_description'))) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection