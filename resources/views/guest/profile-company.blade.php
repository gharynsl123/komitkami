@extends('welcome')

@section('content')
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .hero {
            background: url('images/komitkami.jpeg') no-repeat center center/cover;
            color: white;
            height: 35rem;
            padding: 100px 0;
        }

        .investment-image img,
        .product-requirement-content img,
        .product-item img,
        .gallery-images img {
            width: 100%;
            height: auto;
        }

        .gallery .slider {
            display: flex;
            overflow: hidden;
            position: relative;
            width: 100%;
            height: 300px;
            /* Adjust height as needed */
        }

        .gallery .slide {
            min-width: 300px;
            /* Set a fixed width for the slide */
            height: 300px;
            /* Set a fixed height for the slide */
            transition: transform 0.5s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
            padding: 10px;
            /* Add some padding if needed */
        }

        .gallery .slide img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            /* Ensure image fits within the box */
        }

        .slider-pagination {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .slider-pagination .dot {
            height: 10px;
            width: 10px;
            margin: 0 5px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
        }

        .slider-pagination .dot.active {
            background-color: #717171;
        }

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
    </style>
    <!-- Hero Section -->
    <section class="hero d-flex align-items-center">
        <div class="container">
            <p class="m-0 fw-bolder">THE COMPANY PROFILE</p>
            <p class="mb-3 fs-2 fw-lighter">PT Komitkami Intinusa Gemilang</p>
            <h1 class="fw-bolder">BUSINESS <br> COOPERATION</h1>
        </div>
    </section>

    <!-- Investment Section -->
    <section class="investment py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 d-flex justify-content-center">
                    <div style="width:70%">
                        <h2>INVESTMEN</h2>
                        <hr>
                        <h3 class="fw-bolder">NEW
                            GROWTH
                            ENGINE</h3>
                        <p>For your business</p>

                        <p class="text-justify">Ada banyak alasan mengapa Anda harus memilih kami. <br> <br>

                            Kami membuat produk untuk merek Anda, membangun perusahaan Anda dengan produk berkualitas, dan
                            layanan pelanggan yang sangat baik. <br> <br>

                            Dengan banyaknya jenis produk dalam satu lini bisnis akan membuat promosi Anda lebih efektif.
                            <br> <br>

                            Gunakan kesempatan ini sebagai mesin penghasil keuntungan perusahaan Anda dengan cepat.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 d-flex justify-content-center">
                    <img src="{{asset('images/gudang.jpeg')}}" class="div-hover" height="600px" alt="Investment">
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values py-5 bg-light">
        <div class="container bg-light py-5">
            <!-- Vision and Mission Section -->
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <img src="{{asset('images/icon.png')}}" alt="Company Image" class="img-fluid rounded-circle">
                </div>
                <div class="col-md-8">
                    <h3 class="text-primary">VISI KAMI</h3>
                    <hr class="text-warning" style="height: 2px; width: 50px; margin-left: 0;">
                    <p>"Menjadi perusahaan manufaktur alat kesehatan dan terintegrasi dengan institusi kesehatan dalam
                        menciptakan produk berkualitas tinggi dan jaminan mutu berkelanjutan"</p>
                    <h3 class="text-primary">MISI KAMI</h3>
                    <hr class="text-warning" style="height: 2px; width: 50px; margin-left: 0;">
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-primary me-2"></i>
                            Menghasilkan produk berkualitas tinggi dengan jaminan mutu sebagai prioritas utama.
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-primary me-2"></i>
                            Mewujudkan produk inovatif yang ramah lingkungan untuk kebutuhan praktisi kesehatan.
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-primary me-2"></i>
                            Membuat metode produksi yang baik, dan didukung teknologi dalam meningkatkan nilai perusahaan.
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Cooperation and Quality Policy Section -->
            <div class="row align-items-center mt-5">
                <div class="col-md-12">
                    <h3 class="text-primary">KERJASAMA DENGAN KAMI</h3>
                    <hr class="text-warning" style="height: 2px; width: 50px; margin-left: 0;">
                    <p>Pabrik kami bergerak di bidang industri alat kesehatan, khusus cairan medis anti mikroba, yang diisi
                        oleh tenaga ahli dan berpengalaman dari Indonesia sebagai kekuatan kami dalam bekerja.</p>
                    <h3 class="text-primary">KEBIJAKAN KUALITAS</h3>
                    <hr class="text-warning" style="height: 2px; width: 50px; margin-left: 0;">
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-primary me-2"></i>
                            Mematuhi peraturan perundang-undangan yang berlaku dari Kementerian Kesehatan Republik
                            Indonesia.
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-primary me-2"></i>
                            Uji mutu produk di lembaga laboratorium yang memiliki sertifikasi akreditasi nasional.
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-primary me-2"></i>
                            Setiap produk memiliki kandungan dalam negeri untuk mendukung peraturan kementerian
                            perindustrian republik Indonesia.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Requirement Section -->
    <section class="product-requirement py-5">
        <div class="container">
            <p class="m-0">we are following</p>
            <h2 class="fw-bolder">Product <span class="text-info">Requirement</span></h2>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="{{asset('images/produk-promosi.png')}}" alt="Product Requirement" class="img-fluid">
                </div>
                <div class="col-md-6">
                    <img src="{{asset('images/proses-pembuatan.png')}}" alt="Product Requirement"
                        class="div-hover img-fluid">
                </div>
                <div class="col-md-12">
                    <h3 class="text-primary">MISI KAMI</h3>
                    <hr class="text-warning" style="height: 2px; width: 50px; margin-left: 0;">
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-primary me-2"></i>
                            Kami memproduksi produk dengan memberikan jaminan kualitas dan mengikuti peraturan produksi
                            sesuai dengan peraturan dari kementerian perindustrian dan kementerian kesehatan republik
                            Indonesia.
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-primary me-2"></i>
                            Produk diuji secara berkala dengan catatan produksi yang dilaporkan langsung melalui formulir
                            online Kementerian Kesehatan.
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-primary me-2"></i>
                            PT Komitkami Intinusa Gemilang sedang memproses sertifikat Cara Produksi Alat Kesehatan.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-3 bg-light">
        <div class="container">
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
                            <a href="#" class="btn btn-sm rounded-pill mt-3"
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
                            <a href="#" class="btn btn-sm rounded-pill mt-3"
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
                            <a href="#" class="btn btn-sm rounded-pill mt-3"
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
                            <a href="#" class="btn btn-sm rounded-pill mt-3"
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
                            <a href="#" class="btn btn-sm rounded-pill mt-3"
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
                            <a href="#" class="btn btn-sm rounded-pill mt-3"
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
                            <a href="#" class="btn btn-sm rounded-pill mt-3"
                                style="background-color: #e3f2fd; font-weight:500;">Ketahui Lebih Lanjut</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="facilities py-5">
        <div class="container">
            <h2 class="text-center">Our Facilities</h2>
            <p class="text-center">Production</p>
            <div class="d-flex flex-column justify-content-center align-items-center">
                <div class="table-responsive" style="width:75%;">
                    <table class="table table-bordered">
                        <tr>
                            <th>Factory Area</th>
                            <td>12 M x 30 M</td>
                        </tr>
                        <tr>
                            <th>Factory Building</th>
                            <td>12 M x 27 M</td>
                        </tr>
                        <tr>
                            <th>Factory Facilities</th>
                            <td>Main lobby of PT Komitkami Room of Komitkami Institute (Training & Meeting).</td>
                        </tr>
                        <tr>
                            <th>KING Laboratory</th>
                            <td>
                                <ul class="m-0">
                                    <li>Lab of Microbiology</li>
                                    <li>Lab of Development Product</li>
                                    <li>Lab of Quality Control</li>
                                </ul>

                            </td>
                        </tr>
                        <tr>
                            <th>Production Area</th>
                            <td>Scaling Room, Mixing Room, Filling Room & Packing Area.</td>
                        </tr>
                        <tr>
                            <th>KING Where house</th>
                            <td>Chemical Raw Material, dry Raw Material, Packaging Raw Material, Quarantine Area, Finished
                                goods
                                warehouse, Receiving area.</td>
                        </tr>
                        <tr>
                            <th>Waste Managing</th>
                            <td>
                                <ul class="m-0">
                                    <li>Hazardous wet waste</li>
                                    <li>Dry Waste</li>
                                </ul>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="table-responsive" style="width:75%;">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="5" class="text-center">PRODUCTION CAPACITY</th>
                            </tr>
                            <tr>
                                <th>Product</th>
                                <th>Production/Day (@ Batch)</th>
                                <th>Total/Batch (@ Bottle)</th>
                                <th>Total Production/Day (Bottle)</th>
                                <th>Total Production/Month (Bottle)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Bottle - 500ml</td>
                                <td>5</td>
                                <td>200</td>
                                <td>1,000</td>
                                <td>20,000</td>
                            </tr>
                            <tr>
                                <td>Gallons - 5 Liter</td>
                                <td>5</td>
                                <td>20</td>
                                <td>100</td>
                                <td>2,000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery mb-5 py-5">
        <div class="container text-center">
            <h2>Gallery</h2>
            <div class="slider" id="gallerySlider">
                @foreach($images as $item)
                    <div class="slide">
                        <img src="{{ $item->path }}" alt="{{ $item->title }}">
                    </div>
                @endforeach
            </div>
            <div class="slider-pagination" id="sliderPagination"></div>
        </div>
    </section>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slides = document.querySelectorAll('#gallerySlider .slide');
            const pagination = document.getElementById('sliderPagination');
            let currentIndex = 0;

            // Create pagination dots
            slides.forEach((slide, index) => {
                const dot = document.createElement('span');
                dot.classList.add('dot');
                if (index === 0) {
                    dot.classList.add('active');
                }
                dot.addEventListener('click', () => goToSlide(index));
                pagination.appendChild(dot);
            });

            const dots = document.querySelectorAll('#sliderPagination .dot');

            function goToSlide(index) {
                currentIndex = index;
                updateSlider();
            }

            function updateSlider() {
                const offset = -currentIndex * 100;
                slides.forEach(slide => {
                    slide.style.transform = `translateX(${offset}%)`;
                });
                dots.forEach(dot => dot.classList.remove('active'));
                dots[currentIndex].classList.add('active');
            }

            function autoSlide() {
                currentIndex = (currentIndex + 1) % slides.length;
                updateSlider();
            }

            setInterval(autoSlide, 3000); // Change slide every 3 seconds

            window.addEventListener('resize', () => {
                updateSlider(); // Ensure slider updates correctly on resize
            });

            // Initial update to set the correct slide
            updateSlider();
        });
    </script>
@endsection