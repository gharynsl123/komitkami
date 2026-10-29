<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark"
    id="sidenav-main">
    <div class="sidenav-header bg-white">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <div class="navbar-brand m-0">
            <a href="{{url('/')}}">
                <img src="{{asset('images/KING-DATAR.png')}}" width="100%" heigth="100%" alt="" srcset="">
            </a>
        </div>
    </div>
    <div class="collapse navbar-collapse w-auto " id="sidenav-collapse-main">
        @php
            $user = auth()->user();
        @endphp

        <ul class="navbar-nav">
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Main Menu
                </h6>
            </li>
            <!-- Dashboard (All) -->
            <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('dashboard') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ url('/dashboard') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            <!-- Configuration Role & User -->
            @if($user->hasRole('Admin'))
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('role-configuration') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/role-configuration') }}">
                        <i class="material-icons opacity-10">manage_accounts</i>
                        <span class="nav-link-text ms-1">Configuration Role</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('user-configuration') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/user-configuration') }}">
                        <i class="material-icons opacity-10">people</i>
                        <span class="nav-link-text ms-1">Configuration User</span>
                    </a>
                </li>
            @endif

            <!-- Pesanan Pembelian -->
            @if($user->hasAnyRole(['Customer', 'Sales', 'Admin', 'Inventory Manager']))
                 @if (!$user->hasRole('Inventory Manager'))
                    <li class="nav-item mt-3">
                        <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">
                            Ordering Menu
                        </h6>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ Request::is('view-order') ? 'active bg-gradient-primary' : '' }}"
                            href="{{ url('/view-order') }}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">receipt_long</i>
                            </div>
                            <span class="nav-link-text ms-1">Pesanan Pembelian</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ Request::is('mutasi') ? 'active bg-gradient-primary' : '' }}"
                            href="{{ url('/mutasi') }}">
                            <i class="material-icons opacity-10">swap_horiz</i>
                            <span class="nav-link-text ms-1">Mutasi Pembelian</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ Request::is('pembayaran') ? 'active bg-gradient-primary' : '' }}"
                            href="{{ url('/pembayaran') }}">
                            <i class="material-icons opacity-10">credit_card</i>
                            <span class="nav-link-text ms-1">Pembayaran Belum Lunas</span>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('pengiriman-barang') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/pengiriman-barang') }}">
                        <i class="material-icons opacity-10">local_shipping</i>
                        <span class="nav-link-text ms-1">Pengiriman Barang</span>
                    </a>
                </li>
            @endif

            <!-- Inventory -->
            @if($user->hasAnyRole(['Inventory Manager', 'Admin']))
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Inventory Menu
                    </h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('permintaan-material') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/permintaan-material') }}">
                        <i class="material-icons opacity-10">request_quote</i>
                        <span class="nav-link-text ms-1">Proses Permintaan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('transaction/in') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/transaction/in') }}">
                        <i class="material-icons opacity-10">inventory</i>
                        <span class="nav-link-text ms-1">Input Stok Barang</span>
                    </a>
                </li>
            @endif

            @if($user->hasAnyRole(['Inventory Manager', 'Admin', 'Production Manager', 'Supervisor', 'Quality Control']))
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('transaction/out') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/transaction/out') }}">
                        <i class="material-icons opacity-10">exit_to_app</i>
                        <span class="nav-link-text ms-1">
                            Permintaan Material
                        </span>
                    </a>
                </li>
                @if($user->hasAnyRole(['Production Manager', 'Inventory Manager', 'Admin']))
                    <li class="nav-item">
                        <a class="nav-link text-white {{ Request::is('local-inventory') ? 'active bg-gradient-primary' : '' }}"
                            href="{{ url('/local-inventory') }}">
                            <i class="material-icons opacity-10">storage</i>
                            <span class="nav-link-text ms-1">Persediaan Stok Barang</span>
                        </a>
                    </li>
                    <!-- baru -->
                    <li class="nav-item">
                        <a class="nav-link text-white {{ Request::is('stock-opname*') ? 'active bg-gradient-primary' : '' }}"
                            href="{{ url('/stock-opname') }}">
                            <i class="material-icons opacity-10">rule_folder</i>
                            <span class="nav-link-text ms-1">Stock Opname</span>
                        </a>
                    </li>
                @endif
            @endif

            @if($user->hasAnyRole(['Admin', 'Sales', 'Production Manager', 'Inventory Manager']))
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Katalog Menu
                    </h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('produk') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/produk') }}">
                        <i class="material-icons opacity-10">category</i>
                        <span class="nav-link-text ms-1">Produk</span>
                    </a>
                </li>
                @if($user->hasAnyRole(['Admin', 'Sales', 'Production Manager']))
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('merek') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/merek') }}">
                        <i class="material-icons opacity-10">branding_watermark</i>
                        <span class="nav-link-text ms-1">Merek</span>
                    </a>
                </li>
                @endif
            @endif

            @if($user->hasAnyRole(['Production Manager', 'Supervisor', 'Admin', 'Inventory Manager']))
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Produksi Menu
                    </h6>
                </li>
                @if ($user->hasRole('Supervisor'))
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('cetak-laporan-permintaan') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/cetak-laporan-permintaan') }}">
                        <i class="material-icons opacity-10">view_list</i>
                        <span class="nav-link-text ms-1">Cetak Permintaan Material</span>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('ruang-produksi') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/ruang-produksi') }}">
                        <i class="material-icons opacity-10">play_circle</i>
                        <span class="nav-link-text ms-1">Mulai Produksi</span>
                    </a>
                </li>
                @if(!$user->hasRole('Inventory Manager'))
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('riwayat-alat-produksi') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('riwayat-alat-produksi') }}">
                        <i class="material-icons me-2" style="font-size:20px;">build</i>
                        <span>Riwayat Alat</span>
                    </a>
                </li>
                @endif
            @endif

            @if($user->hasAnyRole(['Producer', 'Admin', 'Production Manager', 'Supervisor', 'Inventory Manager']))
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('riwayat-produksi') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/riwayat-produksi') }}">
                        <i class="material-icons opacity-10">history</i>
                        <span class="nav-link-text ms-1">Riwayat Produksi</span>
                    </a>
                </li>
            @endif

            @if($user->hasAnyRole(['Admin', 'Quality Control', 'Production Manager']))
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('download-spesifikasi') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/download-spesifikasi') }}">
                        <i class="material-icons opacity-10">download</i>
                        <span class="nav-link-text ms-1">Download Spesifikasi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('qc-produksi') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/qc-produksi') }}">
                        <i class="material-icons opacity-10">fact_check</i>
                        <span class="nav-link-text ms-1">Data Quality Control</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('pengambilan-sample-ipc') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/pengambilan-sample-ipc') }}">
                        <i class="material-icons opacity-10">biotech</i>
                        <span class="nav-link-text ms-1">Pengambilan Sample IPC</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('pengecekan-barang-masuk') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/pengecekan-barang-masuk') }}">
                        <i class="material-icons opacity-10">rule</i>
                        <span class="nav-link-text ms-1">Pengecekan Material</span>
                    </a>
                </li>
            @endif

            @if($user->hasAnyRole(['Admin', 'Production Manager', 'Inventory Manager']))
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('formula-produk') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/formula-produk') }}">
                        <i class="material-icons opacity-10">science</i>
                        <span class="nav-link-text ms-1">Formula Produk</span>
                    </a>
                </li>
                @if(!$user->hasRole('Inventory Manager'))
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('atur-ruangan') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/atur-ruangan') }}">
                        <i class="material-icons opacity-10">room_preferences</i>
                        <span class="nav-link-text ms-1">Atur Ruangan</span>
                    </a>
                </li>
                @endif
            @endif
            @if($user->hasRole('Admin'))
                <li class="nav-item">
                    <a class="nav-link text-white {{ Request::is('log-table') ? 'active bg-gradient-primary' : '' }}"
                        href="{{ url('/log-table') }}">
                        <i class="material-icons opacity-10">history</i>
                        <span class="nav-link-text ms-1">Log Website</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</aside>