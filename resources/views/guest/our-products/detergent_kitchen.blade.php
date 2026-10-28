@extends('welcome')
@section('content')
    <style>
        h2 {
            font-weight: 700;
        }

        h5 {
            font-weight: 600;
        }

        .product-card {
            border: none;
            margin-bottom: 1.5rem;
        }

        .product-card img {
            max-width: 100px;
            height: auto;
            margin-bottom: 10px;
        }
    </style>
    <div class="container py-5">
        <!-- Judul -->
        <div class="text-center mb-4">
            <h2>Detergent & Disinfektan</h2>
            <h4 class="text-muted">Hospital Kitchen Set</h4>
            <p>
                Paket lengkap untuk kebersihan dapur rumah sakit dan dapur profesional.
                Dirancang dengan standar medis, kombinasi detergent dan disinfektan ini memastikan
                peralatan makan, gelas, dan stainless steel selalu higienis, bebas kuman, serta aman digunakan.
            </p>
        </div>

        <div class="row g-4">
            <!-- Kiri -->
            <div class="col-md-6 text-center">
                <div class="product-card">
                    <img src="{{asset('images/Pro Enzyme Dish Auto.png')}}" alt="Pro Enzyme">
                    <h5>PRO ENZYME DISH AUTO</h5>
                    <p>Detergent enzim dengan kekuatan pembersih maksimal untuk menguraikan lemak dan residu makanan.</p>
                </div>

                <div class="product-card">
                    <img src="{{asset('images/rinse Pro dish auto.png')}}" alt="Rinse Pro">
                    <h5>RINSE PRO DISH AUTO</h5>
                    <p>Cairan pembilas otomatis untuk hasil kilau sempurna tanpa noda, mempercepat pengeringan peralatan.
                    </p>
                </div>
            </div>

            <!-- Kanan -->
            <div class="col-md-6">
                <div class="product-card d-flex align-items-start">
                    <img src="{{asset('images/Medicplatesafe.png')}}" alt="Medic Plate Safe" style="" class="me-3">
                    <div>
                        <h5>MEDIC PLATE SAFE</h5>
                        <p>Detergent pembersih khusus piring medis, menjaga piring tetap bersih dan pasien terlindungi.</p>
                        <ul>
                            <li>Formulasi busa rendah, memudahkan pembilasan.</li>
                        </ul>
                    </div>
                </div>

                <div class="product-card d-flex align-items-start">
                    <img src="{{asset('images/steridish cleaner.png')}}" alt="Steri Dish Cleaner" class="me-3">
                    <div>
                        <h5>STERI DISH CLEANER</h5>
                        <p>Disinfektan kuat untuk piring dan gelas, membunuh kuman dan virus tanpa meninggalkan bau.</p>
                        <ul>
                            <li>Membunuh bakteri dan virus dengan cepat.</li>
                            <li>Aman digunakan tanpa meninggalkan bau/rasa.</li>
                        </ul>
                    </div>
                </div>

                <div class="product-card d-flex align-items-start">
                    <img src="{{asset('images/foam Cleaner Spray.png')}}" alt="Kitchen Cleaner Foam" class="me-3">
                    <div>
                        <h5>KITCHEN CLEANER FOAM SPRAY (750ml)</h5>
                        <p>Pembersih dapur praktis berbentuk spray, efektif mengangkat minyak sekaligus menjaga stainless
                            steel tetap berkilau.</p>
                        <ul>
                            <li>Mudah digunakan untuk area sulit dijangkau.</li>
                            <li>Efektif melarutkan minyak membandel pada peralatan dapur industri.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection