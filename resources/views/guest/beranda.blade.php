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
        height: 100%;
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
        display: flex;
        flex-direction: column;
    }
    .product-title {
        font-weight: 600;
        margin-bottom: 5px;
        font-size: 1rem;
        min-height: 42px;
    }
    .product-description {
        font-size: 0.875rem;
        color: #6c757d;
        flex: 1;
        margin-bottom: 10px;
        min-height: 40px;
    }
    .product-card a { align-self: flex-start; }

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
        left: 8%;
        right: 8%;
        height: 3px;
        background: #cbd5e1;
        z-index: 0;
    }
    .step {
        flex: 1;
        min-width: 180px;
        text-align: center;
        position: relative;
        z-index: 1;
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
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.25);
    }
    .step h5 { margin-top: 15px; font-size: 1rem; }

    @media (max-width: 768px) {
        .progress-steps { flex-direction: column; align-items: center; }
        .progress-steps::before { content: none; }
        .step { margin-bottom: 2rem; }
    }
</style>

{{-- Hero Section --}}
<section class="hero d-flex align-items-center" id="beranda"
    style="min-height: 90vh; background: linear-gradient(135deg, #BDE1FA 0%, #ffffff 100%);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right" data-aos-delay="200">
                <h1 class="fw-bold display-6 mb-3">
                    {{ __('hero_heading_1') }}<br><span class="text-primary">{{ __('hero_heading_2') }}</span>
                </h1>
                <p class="lead text-secondary mb-4">
                    {{ __('hero_description') }}
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="#contact" class="btn btn-primary px-4 py-2 rounded-pill shadow">
                        {{ __('hero_contact_btn') }}
                    </a>
                    <a href="#ourProduct" class="btn btn-outline-primary px-4 py-2 rounded-pill">
                        {{ __('hero_product_btn') }}
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center" data-aos="fade-left" data-aos-delay="400">
                <img src="{{ asset('images/inventasi.png') }}" alt="Komitkami"
                    class="img-fluid" style="max-height: 420px; border-radius: 20px;">
            </div>
        </div>
    </div>
</section>

{{-- Company Profile / Video Section --}}
<section class="py-5 mb-3 bg-light" id="produksi">
    <div class="container">
        <div class="text-lg-start text-center mb-4">
            <h2 class="fw-bold display-6">{{ __('section_company_profile') }}</h2>
            <hr style="width: 80px; border: 3px solid #0d6efd; border-radius: 4px; margin: 0.5rem 0 0;">
        </div>
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-6" data-aos="fade-right">
                <div class="ratio ratio-16x9 shadow rounded overflow-hidden">
                    <iframe src="https://www.youtube.com/embed/tqz9did4mgg?si=i_usINNQqJYUxo6r"
                        title="Komitkami Intinusa Gemilang" frameborder="0"
                        allow="clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>
                </div>
            </div>
            <div class="col-12 col-lg-6" data-aos="fade-left">
                <h3 class="fw-semibold mb-3">{!! nl2br(e(__('how_we_make_it'))) !!}</h3>
                <p class="text-muted lh-lg" style="text-align: justify;">
                    {!! nl2br(e(__('how_we_make_it_description'))) !!}
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Quality Steps --}}
<section class="py-5 px-3 px-lg-5" id="kualitas">
    <div class="container-fluid">
        <div class="text-center mb-5">
            <p class="text-primary fw-semibold text-uppercase" style="letter-spacing:1px;">{{ __('high') }}</p>
            <h2 class="fw-bold">{{ __('quality_products') }}</h2>
        </div>
        <div class="progress-steps">
            <div class="step" data-aos="fade-up" data-aos-delay="100">
                <div class="circle"><i class="bi bi-hospital"></i></div>
                <h5 class="mt-3 fw-semibold">{{ __('step_medical_need') }}</h5>
                <p class="text-muted small">{{ __('step_medical_need_desc') }}</p>
            </div>
            <div class="step" data-aos="fade-up" data-aos-delay="200">
                <div class="circle"><i class="bi bi-eyedropper"></i></div>
                <h5 class="mt-3 fw-semibold">{{ __('step_lab_test') }}</h5>
                <p class="text-muted small">{{ __('step_lab_test_desc') }}</p>
            </div>
            <div class="step" data-aos="fade-up" data-aos-delay="300">
                <div class="circle"><i class="bi bi-file-earmark-text"></i></div>
                <h5 class="mt-3 fw-semibold">{{ __('step_registration') }}</h5>
                <p class="text-muted small">{{ __('step_registration_desc') }}</p>
            </div>
            <div class="step" data-aos="fade-up" data-aos-delay="400">
                <div class="circle"><i class="bi bi-patch-check"></i></div>
                <h5 class="mt-3 fw-semibold">{{ __('step_tkdn') }}</h5>
                <p class="text-muted small">{{ __('step_tkdn_desc') }}</p>
            </div>
            <div class="step" data-aos="fade-up" data-aos-delay="500">
                <div class="circle"><i class="bi bi-lightbulb"></i></div>
                <h5 class="mt-3 fw-semibold">{{ __('step_development') }}</h5>
                <p class="text-muted small">{{ __('step_development_desc') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Nilai Kami --}}
<section class="py-5 bg-light" id="nilai">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-5 text-center" data-aos="fade-right">
                <img src="{{ asset('images/nilai-kami.png') }}" class="img-fluid div-hover" alt="{{ __('nav_values') }}"
                    style="max-height: 380px;">
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <h2 class="fw-bold mb-3">{{ __('what_differentiates_us') }}</h2>
                <hr style="width: 60px; border: 3px solid #ffc107; margin: 0 0 1rem;">
                <p style="text-align: justify; line-height: 1.8;">
                    {!! nl2br(e(__('what_differentiates_us_description'))) !!}
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Produk Unggulan --}}
<section class="py-5" id="ourProduct">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold">{{ __('section_our_products') }}</h3>
                <hr class="mt-2 mb-0" style="width: 120px; border: 2px solid #0d6efd; border-radius: 4px;">
            </div>
            <img src="{{ asset('images/KING BULET.png') }}" alt="Logo" style="width: 8%; max-width: 70px;">
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

{{-- Siklus / Pelayanan --}}
<section class="py-5 bg-light" id="siklus">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-md-6 text-center" data-aos="fade-right">
                <img src="{{ asset('images/siklus.png') }}" alt="" class="img-fluid div-hover" style="max-width: 80%;">
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <h2 class="fw-bold mb-3">{{ __('note') }}</h2>
                <hr style="width: 60px; border: 3px solid #0d6efd; margin: 0 0 1rem;">
                <p style="text-align: justify; line-height: 1.8;">
                    {!! nl2br(e(__('note_description'))) !!}
                </p>
            </div>
        </div>
    </div>
</section>

@endsection
