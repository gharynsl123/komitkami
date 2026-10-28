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
            object-position: center 30%;
            display: block;
            filter: brightness(50%);
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            text-align: left;
            height: 100%;
            background: #fff;
        }

        .triangle {
            font-size: 2rem;
            margin-right: 8px;
            line-height: 1;
        }

        .title {
            font-weight: bold;
            font-size: 1.2rem;
        }

        .desc {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
    </style>
    <div class="header-backdrop">
        <img src="{{ asset('images/Liquid for Reprocessing CSSD.png') }}" alt="Produk 1">
    </div>
    <div class="container mb-5 py-5">
        <h2 class="text-center text-primary mb-5">Detergent for Manual Wash or WD & Disinfectant Treatment</h2>
        <div class="row g-4">
            <!-- DEnzymeWD -->
            <div class="col-sm-6 col-md-4">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="triangle text-success">▲</span>
                        <span class="title">DEnzyme<span class="text-success">WD</span></span>
                    </div>
                    <p class="fw-bold">Deskripsi</p>
                    <p class="desc">Detergent Pencucian mesin WD<br>
                        Jenis: Detergent Multi Enzyme, <em>Medical Grade, low foam</em>.</p>
                    <p class="fw-bold">Kompatibilitas:</p>
                    <p class="desc">Mesin WD & Pencucian manual</p>

                    <a href="{{url('our-products/detail/denzyme-wd')}}" class="btn btn-sm btn-primary rounded-pill">Detail
                        Produk</a>
                </div>
            </div>

            <!-- DERinseAID -->
            <div class="col-sm-6 col-md-4">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="triangle text-primary">▲</span>
                        <span class="title">DERinse<span class="text-primary">AID</span></span>
                    </div>
                    <p class="fw-bold">Deskripsi</p>
                    <p class="desc">Detergent pembilasan dan pengeringan<br>
                        Jenis: Membantu proses pengeringan instrumen pada mesin WD.</p>
                    <p class="fw-bold">Kompatibilitas:</p>
                    <p class="desc">Mesin Washer Disinfector</p>

                    <a href="{{url('our-products/detail/derinse-aid')}}" class="btn btn-sm btn-primary rounded-pill">Detail
                        Produk</a>
                </div>
            </div>

            <!-- DEnzymePLUS -->
            <div class="col-sm-6 col-md-4">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="triangle text-danger">▲</span>
                        <span class="title">DEnzyme<span class="text-danger">PLUS</span></span>
                    </div>
                    <p class="fw-bold">Deskripsi</p>
                    <p class="desc">Detergent Pencucian manual dengan disinfectant<br>
                        Jenis: Detergent & Disinfectant dengan Multi Enzyme dan Disinfectan.</p>
                    <p class="fw-bold">Kompatibilitas:</p>
                    <p class="desc">Pencucian manual</p>

                    <a href="{{url('our-products/detail/denzyme-plus')}}" class="btn btn-sm btn-primary rounded-pill">Detail
                        Produk</a>

                </div>
            </div>

            <!-- DISGluta -->
            <div class="col-sm-6 col-md-4">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="triangle text-danger">▲</span>
                        <span class="title">DISGluta</span>
                    </div>
                    <p class="fw-bold">Deskripsi</p>
                    <p class="desc">Cairan perendaman untuk Disinfectant Tingkat Tinggi<br>
                        Jenis: Disinfectant dengan glutaraldehyde, cairan siap pakai tanpa perlu dicampur air.</p>

                    <a href="{{url('our-products/detail/disgluta')}}" class="btn btn-sm btn-primary rounded-pill">Detail
                        Produk</a>

                </div>
            </div>

            <!-- SurgiFoam Active -->
            <div class="col-sm-6 col-md-4">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="triangle text-success">▲</span>
                        <span class="title">SurgiFoam <span class="text-success"> Active</span></span>
                    </div>
                    <p class="fw-bold">Deskripsi</p>
                    <p class="desc">Cairan Foam aktif untuk mencegah instrumen kering dan membunuh bakteri.<br>
                        Jenis: Foam aktif untuk menjaga instrumen tetap basah & lembab agar mempermudah pencucian.</p>

                    <a href="{{url('our-products/detail/surgi-foam')}}" class="btn btn-sm btn-primary rounded-pill">Detail
                        Produk</a>

                </div>
            </div>

            <!-- MEDILUBE Spray -->
            <div class="col-sm-6 col-md-4">
                <div class="product-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="triangle text-success">▲</span>
                        <span class="title">MEDILUBE Spray</span>
                    </div>
                    <p class="fw-bold">Deskripsi</p>
                    <p class="desc">Pelumas untuk pemeliharaan instrumen.<br>
                        Jenis: Pelumas khusus untuk instrumen medis dengan kualitas medis.</p>
                    <a href="{{url('our-products/detail/medilube-spray')}}"
                        class="btn btn-sm btn-primary rounded-pill">Detail Produk</a>
                </div>
            </div>
        </div>
    </div>
@endsection