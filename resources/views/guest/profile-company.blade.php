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
        background: url('{{ asset("images/komitkami.jpeg") }}') no-repeat center center/cover;
        color: white;
        min-height: 30rem;
        padding: 100px 0;
        position: relative;
    }
    .hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.45);
    }
    .hero .container { position: relative; z-index: 1; }

    .investment-image img,
    .product-requirement-content img,
    .product-item img,
    .gallery-images img { width: 100%; height: auto; }

    .gallery .slider {
        display: flex;
        overflow: hidden;
        position: relative;
        width: 100%;
        height: 300px;
    }
    .gallery .slide {
        min-width: 300px;
        height: 300px;
        transition: transform 0.5s ease;
        display: flex;
        justify-content: center;
        align-items: center;
        box-sizing: border-box;
        padding: 10px;
    }
    .gallery .slide img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .slider-pagination { display: flex; justify-content: center; margin-top: 10px; }
    .slider-pagination .dot {
        height: 10px; width: 10px; margin: 0 5px;
        background-color: #bbb; border-radius: 50%;
        display: inline-block; cursor: pointer;
    }
    .slider-pagination .dot.active { background-color: #717171; }

    .product-card {
        border-radius: 1rem;
        background: #fff;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .product-card img { width: 100%; height: 200px; object-fit: cover; border-bottom: 1px solid #f0f0f0; }
    .product-card:hover { transform: translateY(-6px); box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important; }
    .product-card .p-3 { flex: 1; display: flex; flex-direction: column; }
    .product-title { font-weight: 600; margin-bottom: 5px; font-size: 1rem; min-height: 42px; }
    .product-description { font-size: 0.875rem; color: #6c757d; flex: 1; margin-bottom: 10px; min-height: 40px; }
    .product-card a { align-self: flex-start; }
    .section-title-bar {
        height: 3px; width: 50px;
        background: #ffc107; border-radius: 4px;
        margin: 0.25rem 0 1rem;
    }
</style>

{{-- Hero Section --}}
<section class="hero d-flex align-items-end pb-5">
    <div class="container">
        <p class="m-0 fw-semibold text-uppercase" style="letter-spacing: 2px;">{{ __('profile_hero_eyebrow') }}</p>
        <p class="mb-2 fs-4 fw-light">PT Komitkami Intinusa Gemilang</p>
        <h1 class="fw-bolder display-5">{{ __('profile_hero_title') }}</h1>
    </div>
</section>

{{-- Investment Section --}}
<section class="investment py-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-6" data-aos="fade-right">
                <p class="text-muted text-uppercase fw-semibold mb-1" style="letter-spacing:1px;">{{ __('profile_investment_label') }}</p>
                <h2 class="fw-bolder">{{ __('profile_investment_subtitle') }}</h2>
                <p class="text-muted mb-3">{{ __('profile_investment_for') }}</p>
                <div class="section-title-bar"></div>
                <p style="text-align: justify; white-space: pre-line;">{{ __('profile_investment_body') }}</p>
            </div>
            <div class="col-md-6 text-center" data-aos="fade-left">
                <img src="{{ asset('images/gudang.jpeg') }}" class="img-fluid rounded shadow"
                    style="max-height: 450px; object-fit: cover; width: 100%;" alt="{{ __('profile_investment_label') }}">
            </div>
        </div>
    </div>
</section>

{{-- Vision & Mission + Quality Policy --}}
<section class="py-5 bg-light">
    <div class="container">
        {{-- Vision & Mission --}}
        <div class="row align-items-center g-4 mb-5">
            <div class="col-md-4 text-center" data-aos="zoom-in">
                <img src="{{ asset('images/icon.png') }}" alt="Company Logo" style="max-width: 200px;">
            </div>
            <div class="col-md-8" data-aos="fade-left">
                <h3 class="text-primary fw-bold">{{ __('profile_vision_title') }}</h3>
                <div class="section-title-bar"></div>
                <p>{{ __('profile_vision_body') }}</p>

                <h3 class="text-primary fw-bold mt-4">{{ __('profile_mission_title') }}</h3>
                <div class="section-title-bar"></div>
                <ul class="list-unstyled">
                    <li class="d-flex align-items-start mb-2">
                        <i class="bi bi-check-circle-fill text-primary me-2 mt-1"></i>
                        {{ __('profile_mission_1') }}
                    </li>
                    <li class="d-flex align-items-start mb-2">
                        <i class="bi bi-check-circle-fill text-primary me-2 mt-1"></i>
                        {{ __('profile_mission_2') }}
                    </li>
                    <li class="d-flex align-items-start mb-2">
                        <i class="bi bi-check-circle-fill text-primary me-2 mt-1"></i>
                        {{ __('profile_mission_3') }}
                    </li>
                </ul>
            </div>
        </div>

        {{-- Cooperation & Quality Policy --}}
        <div class="row g-4" data-aos="fade-up">
            <div class="col-md-6">
                <div class="p-4 bg-white rounded shadow-sm h-100">
                    <h3 class="text-primary fw-bold">{{ __('profile_cooperation_title') }}</h3>
                    <div class="section-title-bar"></div>
                    <p>{{ __('profile_cooperation_body') }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 bg-white rounded shadow-sm h-100">
                    <h3 class="text-primary fw-bold">{{ __('profile_quality_title') }}</h3>
                    <div class="section-title-bar"></div>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-start mb-2">
                            <i class="bi bi-check-circle-fill text-primary me-2 mt-1"></i>
                            {{ __('profile_quality_1') }}
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <i class="bi bi-check-circle-fill text-primary me-2 mt-1"></i>
                            {{ __('profile_quality_2') }}
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <i class="bi bi-check-circle-fill text-primary me-2 mt-1"></i>
                            {{ __('profile_quality_3') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Product Requirements --}}
<section class="py-5">
    <div class="container">
        <p class="text-muted text-uppercase fw-semibold mb-1" style="letter-spacing:1px;">{{ __('profile_requirements_label') }}</p>
        <h2 class="fw-bolder mb-1">{{ __('profile_requirements_title') }}</h2>
        <div class="section-title-bar" style="background:#0d6efd;"></div>

        <div class="row align-items-center g-4 mt-2">
            <div class="col-md-6" data-aos="fade-right">
                <img src="{{ asset('images/produk-promosi.png') }}" alt="{{ __('profile_requirements_title') }}">
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <img src="{{ asset('images/proses-pembuatan.png') }}" alt="{{ __('profile_requirements_title') }}">
            </div>
            <div class="col-md-12" data-aos="fade-up">
                <ul class="list-unstyled mt-3">
                    <li class="d-flex align-items-start mb-3">
                        <i class="bi bi-check-circle-fill text-primary me-2 mt-1 fs-5"></i>
                        <span>{{ __('profile_req_1') }}</span>
                    </li>
                    <li class="d-flex align-items-start mb-3">
                        <i class="bi bi-check-circle-fill text-primary me-2 mt-1 fs-5"></i>
                        <span>{{ __('profile_req_2') }}</span>
                    </li>
                    <li class="d-flex align-items-start mb-3">
                        <i class="bi bi-check-circle-fill text-primary me-2 mt-1 fs-5"></i>
                        <span>{{ __('profile_req_3') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- Featured Products --}}
<section class="py-3 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">{{ __('profile_featured_products') }}</h3>
                <hr class="mt-2 mb-0" style="width: 120px; border: 2px solid #0d6efd; border-radius: 4px;">
            </div>
            <img src="{{ asset('images/KING BULET.png') }}" alt="Logo" style="width:8%; max-width: 80px;">
        </div>

        <div class="row g-4 justify-content-center">
            @php
            $products = [
                ['img' => 'Liquid for Reprocessing CSSD.png', 'name' => 'Liquid for Reprocessing CSSD',
                 'desc' => 'Detergent for Manual Wash or WD & Disinfectant Treatment',
                 'url'  => '/our-products/liquid-for-reprocessing-cssd'],
                ['img' => 'Antiseptic Medical Grade.png', 'name' => 'Antiseptic Medical Grade',
                 'desc' => 'Hand Sanitizer MedisRub & MediScrub Antiseptic Surgery',
                 'url'  => '/our-products/antiseptic-medical-grade'],
                ['img' => 'Detergent for Laundry.png', 'name' => 'Detergent for Laundry',
                 'desc' => app()->getLocale() === 'id' ? 'Deterjen Kelas Medis untuk Laundry' : 'Medical Grade Detergent for Laundry',
                 'url'  => '/our-products/detergent-for-laundry'],
                ['img' => 'Alkohol 70, 80, 96, 98.png', 'name' => 'Alkohol 70, 80, 96, 98',
                 'desc' => app()->getLocale() === 'id' ? 'Alkohol Murni untuk Kebutuhan Medis & Laboratorium' : 'Pure Alcohol for Medical & Laboratory Use',
                 'url'  => '/our-products/alkohol-70-80-96-98'],
                ['img' => 'Reggen Patologi Anatomi.png', 'name' => 'Reagen Patologi Anatomi',
                 'desc' => app()->getLocale() === 'id' ? 'Solusi Lengkap untuk Laboratorium Histopatologi' : 'Complete Solution for Histopathology Labs',
                 'url'  => '/our-products/reagen-patologi-anatomi'],
                ['img' => 'Detergent & Desinfectan Kitchen.png', 'name' => 'Detergent & Disinfectant Kitchen',
                 'desc' => app()->getLocale() === 'id' ? 'Perlindungan Maksimal untuk Dapur Steril' : 'Maximum Protection for Sterile Kitchen',
                 'url'  => '/our-products/detergent-disinfetan-kitchen'],
                ['img' => 'Ultrasound Transmission Gel.png', 'name' => 'Ultrasound Transmission Gel',
                 'desc' => app()->getLocale() === 'id' ? 'Kejernihan Gelombang untuk Diagnosa Akurat' : 'Wave Clarity for Accurate Diagnosis',
                 'url'  => '/our-products/ultrasound-transmission-gel'],
            ];
            @endphp
            @foreach($products as $p)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card shadow-sm">
                    <img src="{{ asset('images/' . $p['img']) }}" alt="{{ $p['name'] }}">
                    <div class="p-3">
                        <h6 class="product-title">{{ $p['name'] }}</h6>
                        <p class="product-description">{{ $p['desc'] }}</p>
                        <a href="{{ url($p['url']) }}" class="btn btn-sm rounded-pill mt-auto"
                            style="background-color: #e3f2fd; font-weight: 500;">
                            {{ __('section_learn_more') }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Facilities Section --}}
<section class="facilities py-5" style="background-color: #f8fafc;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge text-primary fw-semibold px-3 py-2 rounded-pill text-uppercase mb-2" style="background-color: #e3f2fd; font-size: 0.8rem; letter-spacing: 1px;">
                {{ __('profile_facilities_subtitle') }}
            </span>
            <h2 class="fw-bold mb-2">{{ __('profile_facilities_title') }}</h2>
            <div class="mx-auto" style="width: 60px; height: 3px; background: #0d6efd; border-radius: 4px;"></div>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-10">
                <!-- Facility Specifications Card -->
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white py-3 px-4 border-bottom border-light-subtle d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-building-gear text-primary fs-4 me-2"></i>
                            <h5 class="fw-bold mb-0 text-dark">
                                @if(app()->getLocale() === 'en') Factory Infrastructure & Areas @else Infrastruktur & Area Pabrik @endif
                            </h5>
                        </div>
                        <span class="badge bg-primary px-3 py-1 rounded-pill small">Standard CPAKB</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <tbody>
                                <tr>
                                    <td class="ps-4 text-nowrap fw-semibold text-secondary" style="width: 25%;">
                                        <i class="bi bi-bounding-box me-2 text-primary"></i>Factory Area
                                    </td>
                                    <td class="pe-4">
                                        <span class="badge bg-light text-dark border px-3 py-2 fs-6 fw-bold">12 M × 30 M</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 text-nowrap fw-semibold text-secondary">
                                        <i class="bi bi-building me-2 text-primary"></i>Factory Building
                                    </td>
                                    <td class="pe-4">
                                        <span class="badge bg-light text-dark border px-3 py-2 fs-6 fw-bold">12 M × 27 M</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 text-nowrap fw-semibold text-secondary">
                                        <i class="bi bi-door-open me-2 text-primary"></i>Factory Facilities
                                    </td>
                                    <td class="pe-4 text-dark">
                                        Main Lobby PT Komitkami, Room of Komitkami Institute (Training & Meeting Center).
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 text-nowrap fw-semibold text-secondary align-top pt-3">
                                        <i class="bi bi-shield-check me-2 text-primary"></i>KING Laboratory
                                    </td>
                                    <td class="pe-4 py-2">
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge rounded-pill px-3 py-2 text-primary border" style="background-color: #f0f7ff;">
                                                <i class="bi bi-bug me-1"></i> Lab of Microbiology
                                            </span>
                                            <span class="badge rounded-pill px-3 py-2 text-success border" style="background-color: #f0fff4;">
                                                <i class="bi bi-cpu me-1"></i> Lab of Product Development
                                            </span>
                                            <span class="badge rounded-pill px-3 py-2 text-info border" style="background-color: #f0faff;">
                                                <i class="bi bi-clipboard2-check me-1"></i> Lab of Quality Control
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 text-nowrap fw-semibold text-secondary">
                                        <i class="bi bi-gear-wide-connected me-2 text-primary"></i>Production Area
                                    </td>
                                    <td class="pe-4 text-dark">
                                        Scaling Room, Mixing Room, Filling Room, & Packing Area.
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 text-nowrap fw-semibold text-secondary">
                                        <i class="bi bi-boxes me-2 text-primary"></i>KING Warehouse
                                    </td>
                                    <td class="pe-4 text-dark">
                                        Chemical Raw Material, Dry Raw Material, Packaging Raw Material, Quarantine Area, Finished Goods Warehouse, & Receiving Area.
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 text-nowrap fw-semibold text-secondary">
                                        <i class="bi bi-trash3 me-2 text-primary"></i>Waste Management
                                    </td>
                                    <td class="pe-4">
                                        <span class="badge bg-danger text-white px-2 py-1 me-2">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Hazardous Wet Waste
                                        </span>
                                        <span class="badge bg-secondary text-white px-2 py-1">
                                            <i class="bi bi-recycle me-1"></i>Dry Waste
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Production Capacity Card -->
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white py-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-speedometer2 fs-4 me-2"></i>
                            <h5 class="fw-bold mb-0">
                                @if(app()->getLocale() === 'en') PRODUCTION CAPACITY @else KAPASITAS PRODUKSI @endif
                            </h5>
                        </div>
                        <span class="badge bg-white text-primary px-3 py-1 rounded-pill fw-semibold">High Output</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0 text-center">
                            <thead class="table-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="py-3 text-start ps-4">Product Type</th>
                                    <th class="py-3">Production / Day<br><small class="text-muted fw-normal">(@ Batch)</small></th>
                                    <th class="py-3">Total / Batch<br><small class="text-muted fw-normal">(@ Bottle)</small></th>
                                    <th class="py-3">Total / Day<br><small class="text-muted fw-normal">(Bottles)</small></th>
                                    <th class="py-3 pe-4 text-primary">Total / Month<br><small class="text-primary fw-normal">(Bottles)</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start ps-4 fw-bold">
                                        <i class="bi bi-prescription2 text-primary me-2"></i>Bottle — 500 ml
                                    </td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1">5 Batch</span></td>
                                    <td class="fw-semibold">200</td>
                                    <td class="fw-semibold">1,000</td>
                                    <td class="pe-4">
                                        <span class="badge text-primary fs-6 px-3 py-1 fw-bold" style="background-color: #e3f2fd;">
                                            20,000
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start ps-4 fw-bold">
                                        <i class="bi bi-bucket text-primary me-2"></i>Gallon — 5 Liter
                                    </td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1">5 Batch</span></td>
                                    <td class="fw-semibold">20</td>
                                    <td class="fw-semibold">100</td>
                                    <td class="pe-4">
                                        <span class="badge text-primary fs-6 px-3 py-1 fw-bold" style="background-color: #e3f2fd;">
                                            2,000
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Gallery Section --}}
<section class="gallery mb-5 py-5">
    <div class="container text-center">
        <h2>{{ __('profile_gallery_title') }}</h2>
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
