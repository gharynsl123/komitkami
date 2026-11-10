<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom-style.css') }}">
    <link rel="icon" href="{{ asset('images/KING BULET.png') }}">

    <title>Welcome To Komitkami!</title>
</head>

<style>
    .flag-icon {
        width: 20px;
        height: auto;
        vertical-align: middle;
    }

    /* Lang toggle pill */
    .lang-toggle {
        display: inline-flex;
        align-items: center;
        border: 1.5px solid #0d6efd;
        border-radius: 50px;
        overflow: hidden;
        font-size: 0.8rem;
    }
    .lang-toggle a {
        padding: 3px 10px;
        text-decoration: none;
        color: #0d6efd;
        font-weight: 600;
        transition: background 0.2s, color 0.2s;
    }
    .lang-toggle a.active-lang {
        background: #0d6efd;
        color: #fff;
    }
    .lang-toggle a:not(.active-lang):hover {
        background: #e8f0fe;
    }
    .lang-divider {
        color: #0d6efd;
        font-weight: 400;
        font-size: 0.75rem;
    }

    /* Floating buttons */
    .floating-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        gap: 12px;
        align-items: center;
        z-index: 999;
    }

    #slideButton {
        background: #007bff;
        color: white;
        font-weight: 500;
        font-size: 0.85rem;
        padding: 8px 18px;
        border: none;
        border-radius: 50px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        transition: all 0.3s ease;
        display: none;
    }

    #slideButton.visible {
        display: block;
    }

    #slideButton:hover {
        background: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
    }

    #WAButton {
        background: #25D366;
        width: 52px;
        height: 52px;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    #WAButton img {
        width: 26px;
        height: 26px;
    }

    #WAButton:hover {
        background: #20bd5a;
        transform: scale(1.08);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
    }
</style>

<body>
    <!-- Navbar -->
    <nav id="navbar" class="navbar py-3 navbar-expand-lg navbar-light sticky-top shadow-sm" style="background-color: #e3f2fd;">
        <div class="container">
            <a class="navbar-brand py-0 d-flex align-items-center" href="/">
                <img src="{{ asset('images/KING-DATAR.png') }}" alt="Komitkami" height="50px" style="max-height: 50px; width: auto; object-fit: contain;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                <ul class="navbar-nav mb-2 mb-lg-0 align-items-lg-center gap-lg-1">

                    @if(Request::is('/'))
                        {{-- In-page anchor links (HANYA tampil di halaman beranda / landing page) --}}
                        <li class="nav-item">
                            <a class="nav-link" href="#beranda">{{ __('nav_home') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#nilai">{{ __('nav_values') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#ourProduct">{{ __('nav_products') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#siklus">{{ __('nav_service') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">{{ __('nav_contact') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('profile-perusahaan') }}">{{ __('nav_profile') }}</a>
                        </li>
                    @else
                        {{-- Navigasi bersih & teratur untuk semua sub-halaman (Detail Produk, Our Products, Profil Perusahaan, dll) --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">{{ __('nav_home') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/#ourProduct') }}">{{ __('nav_products') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('profile-perusahaan') ? 'fw-bold text-primary active' : '' }}"
                               href="{{ url('profile-perusahaan') }}">{{ __('nav_profile') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/#contact') }}">{{ __('nav_contact') }}</a>
                        </li>
                    @endif

                    {{-- Login / Dashboard --}}
                    <li class="nav-item">
                        @auth
                            <a class="btn btn-info btn-sm rounded-pill px-3 py-1"
                               href="{{ url('dashboard') }}">Dashboard</a>
                        @endauth
                        @guest
                            <a class="btn btn-info btn-sm rounded-pill px-3 py-1 fw-bold text-white shadow-sm"
                               href="{{ url('login') }}">ISKING</a>
                        @endguest
                    </li>

                    {{-- Language Toggle Pill --}}
                    <li class="nav-item">
                        @php $locale = app()->getLocale(); @endphp
                        <div class="lang-toggle">
                            <a href="{{ url('lang/id') }}" class="{{ $locale === 'id' ? 'active-lang' : '' }}">
                                <img class="flag-icon mb-0" src="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.4.6/flags/4x3/id.svg" alt="ID"> ID
                            </a>
                            <span class="lang-divider">|</span>
                            <a href="{{ url('lang/en') }}" class="{{ $locale === 'en' ? 'active-lang' : '' }}">
                                <img class="flag-icon mb-0" src="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.4.6/flags/4x3/gb.svg" alt="EN"> EN
                            </a>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    @yield('content')

    @include('widget.footer-welcome')

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <div class="floating-container">
        <button id="slideButton">{{ __('btn_back_to_top') }}</button>
        <button id="WAButton" aria-label="WhatsApp">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp">
        </button>
    </div>

    <script>
        AOS.init();

        document.getElementById('WAButton').addEventListener('click', function () {
            window.open('https://wa.me/6281291238909', '_blank');
        });

        window.addEventListener('scroll', function () {
            const slideButton = document.getElementById('slideButton');
            if (window.scrollY > 150) {
                slideButton.classList.add('visible');
            } else {
                slideButton.classList.remove('visible');
            }
        });

        document.getElementById('slideButton').addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>

</body>

</html>
