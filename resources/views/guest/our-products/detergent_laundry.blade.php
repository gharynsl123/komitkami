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
            object-position: center 47%;
            display: block;
            filter: brightness(50%);
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            background: #fff;
            height: 100%;
        }

        .icon {
            font-size: 0.9rem;
            margin-right: 8px;
            position: relative;
            top: -6px;
            margin-right: 2px;
            color: green;
            /* ikon baju hijau */
        }

        .title {
            font-weight: bold;
            font-size: 1.2rem;
            color: #000;
            /* teks judul hitam */
        }

        .desc {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <div class="header-backdrop">
        <img src="{{ asset('images/Detergent for Laundry.png') }}" alt="Produk 1">
        <div class="overlay-text">
            <h1>Nama Produk</h1>
            <p>Deskripsi singkat produk</p>
        </div>
    </div>
    <div class="container py-5">
        <!-- Heading -->
        <h2 class="text-center text-success mb-5">Hospital Grade Detergent Laundry</h2>

        <div class="row g-4">
            <!-- MediWash Pro -->
            <div class="col-sm-6 col-md-3">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="fa-solid fa-shirt icon"></span>
                        <span class="title">Mediwash Pro</span>
                    </div>
                    <p class="desc">Detergent khusus untuk laundry rumah sakit dan fasilitas kesehatan dengan formula
                        medical grade yang efektif mengangkat noda berat sekaligus aman untuk linen medis.</p>

                    <a href="{{url('our-products/detail/alkaliforce-med')}}"
                        class="btn btn-sm rounded btn-primary rounded-pill">Detail Produk</a>
                </div>
            </div>

            <!-- AlkaliForce Med -->
            <div class="col-sm-6 col-md-3">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="fa-solid fa-shirt icon"></span>
                        <span class="title">AlkaliForce Med</span>
                    </div>
                    <p class="desc">Detergent alkali khusus untuk laundry medis, dengan formula kuat untuk mengangkat noda
                        membandel hingga ke serat terdalam linen rumah sakit. Efektif untuk noda berat & melekat.</p>

                    <a href="{{url('our-products/detail/alkaliforce-med')}}"
                        class="btn btn-sm rounded btn-primary rounded-pill">Detail Produk</a>

                </div>
            </div>

            <!-- EmulsiMed -->
            <div class="col-sm-6 col-md-3">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="fa-solid fa-shirt icon"></span>
                        <span class="title">EmulsiMed</span>
                    </div>
                    <p class="desc">Emulsifier efektif pengikat lemak yang diformulasikan khusus untuk laundry standar
                        medis. Membantu menguraikan kotoran berminyak dan lemak pada linen rumah sakit.</p>
                    <a href="{{url('our-products/detail/emulsimed')}}"
                        class="btn btn-sm rounded btn-primary rounded-pill">Detail Produk</a>
                </div>
            </div>

            <!-- NeutraCare -->
            <div class="col-sm-6 col-md-3">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="fa-solid fa-shirt icon"></span>
                        <span class="title">NeutraCare</span>
                    </div>
                    <p class="desc">NeutraCare diformulasikan untuk menghilangkan residu kimia dan menormalkan serat linen
                        setelah proses pencucian. Cocok digunakan di laundry rumah sakit.</p>

                    <a href="{{url('our-products/detail/neutra-care')}}"
                        class="btn btn-sm rounded btn-primary rounded-pill">Detail Produk</a>
                </div>
            </div>


            <!-- MediBleach -->
            <div class="col-sm-6 col-md-3">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="fa-solid fa-shirt icon"></span>
                        <span class="title">MediBleach</span>
                    </div>
                    <p class="desc">MediBleach diformulasikan khusus sebagai pemutih linen untuk kebutuhan laundry rumah
                        sakit. Aman digunakan pada standar medis, menjaga kualitas serat kain.</p>

                    <a href="{{url('our-products/detail/medibleach')}}"
                        class="btn btn-sm rounded btn-primary rounded-pill">Detail Produk</a>

                </div>
            </div>

            <!-- OxyBright Plus -->
            <div class="col-sm-6 col-md-3">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="fa-solid fa-shirt icon"></span>
                        <span class="title">OxyBright Plus</span>
                    </div>
                    <p class="desc">OxyBright Plus adalah booster berbasis oksigen aktif yang diformulasikan khusus untuk
                        linen berwarna di laundry rumah sakit. Membantu mengangkat noda membandel tanpa merusak warna,
                        sehingga linen tetap cerah, higienis, dan tahan lama.</p>

                    <a href="{{url('our-products/detail/oxybright-plus')}}"
                        class="btn btn-sm rounded btn-primary rounded-pill">Detail Produk</a>
                </div>
            </div>

            <!-- LinoSoft RX -->
            <div class="col-sm-6 col-md-3">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="fa-solid fa-shirt icon"></span>
                        <span class="title">LinoSoft RX</span>
                    </div>
                    <p class="desc">LinoSoft RX adalah pelembut linen medis dengan formulasi lembut, menjaga serat kain
                        tetap halus dan higienis sesuai standar laundry rumah sakit.</p>

                    <a href="{{url('our-products/detail/linosoft')}}"
                        class="btn btn-sm rounded btn-primary rounded-pill">Detail Produk</a>
                </div>
            </div>
            <!-- LinoFresh -->
            <div class="col-sm-6 col-md-3">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="fa-solid fa-shirt icon"></span>
                        <span class="title">LinoFresh</span>
                    </div>
                    <p class="desc">LinoFresh adalah pewangi linen rumah sakit dengan aroma segar higienis yang
                        diformulasikan untuk menjaga kesegaran linen medis tanpa merusak serat kain.</p>

                    <a href="{{url('our-products/detail/linofresh')}}"
                        class="btn btn-sm rounded btn-primary rounded-pill">Detail Produk</a>

                </div>
            </div>
        </div>
    </div>
@endsection