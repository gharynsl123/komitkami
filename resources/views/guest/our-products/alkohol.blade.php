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
            object-position: center 40%;
            display: block;
            filter: brightness(50%);
        }

        h1 {
            font-weight: 700;
        }

        h2 {
            font-weight: 600;
        }

        .check-item {
            margin-bottom: 0.5rem;
        }

        .check-item::before {
            content: "✔";
            color: green;
            margin-right: 8px;
            font-weight: bold;
        }

        .product-card {
            border-left: 4px solid #28a745;
            padding-left: 12px;
            margin-bottom: 1.2rem;
        }

        .product-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #222;
        }

        .product-title::before {
            content: "➕";
            margin-right: 6px;
            color: #28a745;
            font-weight: bold;
        }
    </style>
    <div class="header-backdrop">
        <img src="{{ asset('images/Alkohol 70, 80, 96, 98.png') }}" alt="Produk 1">
    </div>
    <div class="container py-5">
        <div class="row g-5 align-items-start">
            <!-- Left Column -->
            <div class="col-md-6">
                <h1 class="text-success">Alkosept</h1>
                <h2 class="text-muted">Alkohol Murni Medis</h2>
                <p>
                    Produk <b>Alkosept</b> adalah alkohol murni dengan berbagai kadar, diformulasikan untuk kebutuhan medis,
                    laboratorium, maupun industri.
                    Diproduksi dengan standar tinggi untuk memastikan kemurnian, efektivitas, dan keamanan penggunaan.
                </p>

                <h5 class="mt-4"><b>Keunggulan:</b></h5>
                <div class="check-item"><b>Kualitas medical grade</b></div>
                <div class="check-item"><b>Proses produksi sesuai standar kesehatan</b></div>
                <div class="check-item"><b>Kemurnian tinggi & konsistensi kadar</b></div>
                <div class="check-item"><b>Kemasan higienis dan aman</b></div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
                <div class="product-card">
                    <div class="product-title">Alkosept 70%</div>
                    <p>Cocok sebagai antiseptik tangan dan disinfektan permukaan. Efektif membunuh kuman, bakteri, dan virus
                        sesuai standar medis.</p>
                </div>

                <div class="product-card">
                    <div class="product-title">Alkosept 80%</div>
                    <p>Memberikan daya bunuh mikroorganisme lebih cepat. Ideal untuk rumah sakit, klinik, dan fasilitas
                        kesehatan.</p>
                </div>

                <div class="product-card">
                    <div class="product-title">Alkosept 96%</div>
                    <p>Digunakan untuk keperluan analisis laboratorium, farmasi, dan proses sterilisasi dengan kebutuhan
                        alkohol murni tinggi.</p>
                </div>

                <div class="product-card">
                    <div class="product-title">Alkosept 98%</div>
                    <p>Kadar tertinggi, diformulasikan khusus untuk laboratorium riset, industri farmasi, dan keperluan
                        kimia analitik.</p>
                </div>
            </div>
        </div>
    </div>
@endsection