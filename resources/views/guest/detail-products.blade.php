@extends('welcome')
@section('content')
    <style>
        body {
            font-family: "Inter", sans-serif;
            background: #f9f9f9;
            color: #333;
        }

        .hero {
            height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            color: white;
            border-radius: 0 0 30px 30px;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('{{ $product["photos"][0] ?? "https://via.placeholder.com/1200x600.png?text=Produk" }}') center/cover no-repeat;
            filter: brightness(0.6) blur(1px);
            z-index: -99999;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 20px;
            backdrop-filter: blur(4px);
            background: rgba(0, 0, 0, 0.35);
            border-radius: 12px;
        }

        .product-detail {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 40px;
            margin-top: -40px;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1e88e5;
        }

        .product-image img {
            width: 25%;
            border-radius: 16px;
        }

        .highlight {
            background: #e8f5e9;
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
    </style>

    <!-- Hero -->
    <section class="hero position-relative">
        <div class="hero-bg"></div>
        <div class="container text-center text-white hero-content">
            <h1 class="display-4 fw-bold">{{ $product['title'] }}</h1>
            <p class="lead">{{ $product['subTitle'] }}</p>
        </div>
    </section>

    <!-- Product Detail -->
    <div class="container product-detail">
        <div class="row align-items-center">
            <div class="col-md-6 product-image text-center">
                <img src="{{ isset($product['photos'][1]) ? asset('images/' . $product['photos'][1]) : 'https://via.placeholder.com/500x400.png?text=Gambar+Produk' }}"
                    alt="{{ $product['title'] }}">
            </div>
            <div class="col-md-6">
                <h2 class="mb-3">{{ $product['title'] }}</h2>
                <p>{{ $product['desc'] }}</p>
            </div>
        </div>

        <hr class="my-5">

        <div class="row">
            <div class="col-md-6">
                <h3 class="section-title">Indikasi</h3>
                <div class="highlight">
                    {{ $product['indikasi'] }}
                </div>

                <h3 class="section-title">Komposisi</h3>
                <ul>
                    @foreach ($product['komposisi'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>

                <h3 class="section-title">Karakteristik</h3>
                <ul>
                    @foreach ($product['karakteristik'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-6">
                <h3 class="section-title">Petunjuk Penggunaan</h3>
                <p>{{ $product['usage'] }}</p>

                <h3 class="section-title">Peringatan</h3>
                <p>{{ $product['peringatan'] }}</p>

                <h3 class="section-title">Kemasan</h3>
                <ul>
                    @foreach ($product['kemasan'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection