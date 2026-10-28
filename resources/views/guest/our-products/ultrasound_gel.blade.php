@extends('welcome')
@section('content')
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
        }

        h1,
        h2,
        h3,
        h5 {
            font-weight: 700;
            color: #003366;
        }

        .hero {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .hero img {
            max-width: 100%;
            height: auto;
        }

        .section-title {
            margin: 3rem 0 1rem;
            border-left: 5px solid #007bff;
            padding-left: 10px;
        }

        ul li {
            margin-bottom: 8px;
        }

        .icon-list li::before {
            content: "✔️";
            margin-right: 8px;
            color: #007bff;
        }

        .card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 1rem;
        }

        .min-120 {
            min-height: 110px;
            display: flex;
            justify-content: center;
        }
    </style>
    <div class="container py-5 body">

        <!-- HERO -->
        <div class="row align-items-center hero">
            <div class="col-md-5 text-center">
                <img src="{{asset('images/gel usg 5l.png')}}" alt="UltraGel Product">
            </div>
            <div class="col-md-7">
                <h1>UltraGel</h1>
                <h4 class="text-muted">Ultrasound Transmission Gel</h4>
                <p><strong>Kejernihan Gelombang untuk Diagnosis Akurat</strong></p>
                <p>
                    UltraGel adalah gel transmisi ultrasound berkualitas tinggi yang diformulasikan khusus
                    untuk memastikan kejernihan gambar selama pemeriksaan ultrasonografi (USG).
                    Teksturnya lembut, tidak lengket, mudah dibersihkan, memberikan kenyamanan maksimal bagi pasien
                    serta kemudahan bagi tenaga medis.
                </p>
            </div>
        </div>

        <div class="d-flex gap-4 flex-column flex-lg-row">
            <div>
                <!-- KEUNGGULAN -->
                <h3 class="section-title">Keunggulan Produk</h3>
                <ul class="icon-list">
                    <li><strong>Kejernihan Maksimal:</strong> memastikan transmisi gelombang ultrasound tanpa distorsi.</li>
                    <li><strong>Formulasi Aman:</strong> non-iritasi, bebas lemak, dan tidak merusak probe ultrasound.</li>
                    <li><strong>Nyaman Digunakan:</strong> tekstur lembut, tidak meninggalkan residu, mudah dibersihkan.
                    </li>
                    <li><strong>Stabilitas Tinggi:</strong> tidak mengering cepat selama pemeriksaan.</li>
                    <li><strong>Hemat Pemakaian:</strong> cukup sedikit untuk area luas.</li>
                </ul>
            </div>

            <div>
                <!-- APLIKASI -->
                <h3 class="section-title">Aplikasi</h3>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card p-3">
                            <ul class="mb-0">
                                <li>Pemeriksaan USG diagnostik umum</li>
                                <li>USG obstetri dan ginekologi</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card p-3">
                            <ul class="mb-0">
                                <li>USG kardiologi</li>
                                <li>USG muskuloskeletal & pemeriksaan medis lainnya</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- CARA PAKAI -->
        <h3 class="section-title">Cara Pakai</h3>
        <div class="row g-2">
            <div class="col-md-3">
                <div class="card p-3 min-120">1. Bersihkan area kulit yang akan diperiksa.</div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 min-120">2. Oleskan UltraGel secukupnya pada permukaan kulit atau probe ultrasound.
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 min-120">3. Lakukan pemeriksaan sesuai prosedur.</div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 min-120">4. Setelah selesai, bersihkan gel dari kulit dan probe menggunakan kain/tisu
                    bersih.</div>
            </div>
        </div>

        <!-- KEMASAN -->
        <h3 class="section-title">Kemasan Tersedia</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-center p-3">
                    <h5>Botol 250 ml</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center p-3">
                    <h5>Botol 500 ml</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center p-3">
                    <h5>Jerigen 5 Liter (Refill)</h5>
                </div>
            </div>
        </div>
    </div>
@endsection