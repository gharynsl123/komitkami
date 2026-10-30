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

        .hero {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            padding: 2.5rem;
            margin-bottom: 2rem;
        }

        .hero img {
            max-width: 260px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 8px 16px rgba(0,0,0,0.1));
        }

        .section-title {
            margin: 2rem 0 1rem;
            border-left: 5px solid #007bff;
            padding-left: 12px;
            font-weight: 700;
            color: #003366;
        }

        .icon-list {
            list-style: none;
            padding-left: 0;
        }

        .icon-list li {
            margin-bottom: 10px;
            position: relative;
            padding-left: 28px;
        }

        .icon-list li::before {
            content: "✔";
            position: absolute;
            left: 0;
            color: #0d6efd;
            font-weight: bold;
        }

        .card-custom {
            border-radius: 14px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            height: 100%;
        }
    </style>

    <div class="header-backdrop">
        <img src="{{ asset('images/Ultrasound Transmission Gel.png') }}" alt="Ultrasound Transmission Gel">
    </div>

    <div class="container py-5">
        <!-- HERO CARD -->
        <div class="row align-items-center hero">
            <div class="col-md-5 text-center mb-4 mb-md-0">
                <img src="{{ asset('images/gel usg 5l.png') }}" alt="UltraGel Product">
            </div>
            <div class="col-md-7">
                <span class="badge bg-primary px-3 py-1 rounded-pill mb-2">Diagnostic Sonography Grade</span>
                <h1 class="fw-bold text-dark">KING Ultrasound Transmission Gel</h1>
                <h4 class="text-primary fw-light mb-3">Kejernihan Gelombang untuk Diagnosa Akurat</h4>
                <p class="text-secondary lh-lg mb-4" style="text-align: justify;">
                    UltraGel adalah gel transmisi ultrasonografi medis berkualitas tinggi yang diformulasikan khusus untuk memastikan konduktivitas akustik tanpa distorsi selama pemeriksaan USG, Doppler, dan fisioterapi. Lembut di kulit, mudah dibersihkan, dan tidak merusak probe transducer.
                </p>
                <div>
                    <a href="{{ url('our-products/detail/ultrasound-transmission-gel') }}" class="btn btn-primary rounded-pill px-4 py-2">
                        <i class="bi bi-info-circle me-1"></i> Detail Spesifikasi Produk
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-lg-6">
                <!-- KEUNGGULAN -->
                <h3 class="section-title">Keunggulan Produk</h3>
                <ul class="icon-list">
                    <li><strong>Kejernihan Akustik Maksimal:</strong> memastikan transmisi gelombang ultrasound tanpa gelembung atau distorsi.</li>
                    <li><strong>Formula Aman & Hypoallergenic:</strong> tidak menyebabkan iritasi, bebas garam, dan tidak merusak membran probe ultrasound.</li>
                    <li><strong>Water Soluble:</strong> tekstur lembut, mudah dibersihkan hanya dengan tisu tanpa noda minyak atau lengket.</li>
                    <li><strong>Viskositas Stabil:</strong> tidak cepat mencair atau mengering selama prosedur sonografi berlangsung.</li>
                </ul>
            </div>

            <div class="col-lg-6">
                <!-- APLIKASI -->
                <h3 class="section-title">Aplikasi Medis</h3>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="card card-custom p-3">
                            <h6 class="fw-bold text-primary mb-1"><i class="bi bi-heart-pulse me-1"></i> USG Kebidanan & Ginekologi</h6>
                            <small class="text-muted">Pemeriksaan fetal & organ reproduksi</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card card-custom p-3">
                            <h6 class="fw-bold text-primary mb-1"><i class="bi bi-activity me-1"></i> USG Kardiologi & Doppler</h6>
                            <small class="text-muted">Evaluasi vaskular & aliran darah</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card card-custom p-3">
                            <h6 class="fw-bold text-primary mb-1"><i class="bi bi-person-arms-up me-1"></i> USG Muskuloskeletal</h6>
                            <small class="text-muted">Pemeriksaan otot, sendi & tendon</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card card-custom p-3">
                            <h6 class="fw-bold text-primary mb-1"><i class="bi bi-hospital me-1"></i> Fisioterapi Ultrasound</h6>
                            <small class="text-muted">Terapi konduksi gelombang akustik</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KEMASAN -->
        <h3 class="section-title mt-5">Kemasan Tersedia</h3>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card card-custom text-center p-3">
                    <h5 class="fw-bold text-dark mb-1">Botol 250 ml</h5>
                    <small class="text-muted">Dengan Tutup Fliptop Praktis</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom text-center p-3">
                    <h5 class="fw-bold text-dark mb-1">Botol 500 ml</h5>
                    <small class="text-muted">Kapasitas Sedang Ruang Praktik</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom text-center p-3 border-primary">
                    <h5 class="fw-bold text-primary mb-1">Jerigen 5 Liter</h5>
                    <small class="text-muted">Ekonomis Dilengkapi Corong Tuang</small>
                </div>
            </div>
        </div>
    </div>
@endsection
