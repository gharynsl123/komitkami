<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- yield title -->
    <title>@yield('title' ?? 'PT. Komitkami Helper')</title>

    <!-- jQuery -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>


    <!-- Plugin JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <!-- Fonts and Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <link href="../../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <!-- Core CSS -->
    <link id="pagestyle" href="/assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />

    <!-- Plugin CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />

    <style>
        /* Hide scrollbar tapi tetap bisa scroll */
        ::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        * {
            -ms-overflow-style: none;
            /* IE dan Edge */
            scrollbar-width: none;
            /* Firefox */
        }
        /* Hilangkan panah di input type="number" */
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
            appearance: textfield;
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-200">
    @include('layouts.sidebar')
    <main class="main-content position-relative mt-3 border-radius-lg ">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mb-3 mx-4 shadow-none border-radius-xl" id="navbarBlur"
            data-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Roles
                                User</a></li>
                    </ol>
                    <h6 class="font-weight-bolder mb-0">
                        @if(auth()->user()->roles && auth()->user()->roles->count())
                            @if(auth()->user()->roles->count() > 1)
                                {{ auth()->user()->roles->pluck('role_name')->implode(', ') }}
                            @else
                                {{ auth()->user()->roles->first()->role_name }}
                            @endif
                        @else
                            {{ __('No Role Assigned') }}
                        @endif
                    </h6>
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                    </div>

                    <ul class="navbar-nav  justify-content-end">
                        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                                <div class="sidenav-toggler-inner">
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item dropdown px-4 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body font-weight-bold px-0"
                                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-user me-sm-1"></i>
                                <span class="d-sm-inline d-none">{{Auth::user()->name}}</span>

                            </a>
                            <ul class="dropdown-menu  dropdown-menu-end  px-2 py-3 me-sm-n4"
                                aria-labelledby="dropdownMenuButton">
                                <li class="mb-2">
                                    <a class="dropdown-item border-radius-md" href="{{url('/profile-user')}}">
                                        <div class="d-flex py-1">
                                            <div class="avatar avatar-sm bg-gradient-secondary  me-3  my-auto">
                                                <i class="material-icons">person</i>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="text-sm font-weight-normal mb-1">
                                                    <span class="font-weight-bold">Profile</span>
                                                </h6>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item border-radius-md" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <div class="d-flex py-1">
                                            <div class="avatar avatar-sm bg-gradient-secondary  me-3  my-auto">
                                                <i class="material-icons">exit_to_app</i>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class=" text-sm font-weight-normal mb-1">
                                                    <span class="font-weight-bold">Logout</span>
                                                </h6>
                                            </div>
                                        </div>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="d-none">
                                            @csrf
                                        </form>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container min-vh-77">
            @yield('content')
        </div>
        @include('layouts.footer')
    </main>

    <script src="/assets/js/core/popper.min.js"></script>
    <script src="/assets/js/core/bootstrap.min.js"></script>
    <script src="/assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="/assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="/assets/js/plugins/chartjs.min.js"></script>

    <script src="{{ asset('js/select2.min.js') }}"></script>


    <!-- select2 js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#dataTableDefault').DataTable({
                "language": {
                    "sEmptyTable": "Tidak ada data yang tersedia pada tabel ini",
                    "sProcessing": "Sedang memproses...",
                    "sLengthMenu": "Tampilkan _MENU_ entri",
                },
                "info": true,
                "ordering": false,
                "lengthChange": false,
                "pageLength": 6,
                "searching": true
            });
        });
    </script>
    <script src="/assets/js/material-dashboard.min.js?v=3.1.0"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var sidebar = document.getElementById('sidenav-collapse-main');

            if (sidebar) {
                // Restore scroll position
                var scrollPos = localStorage.getItem('sidebarScrollTop');
                if (scrollPos) {
                    sidebar.scrollTop = parseInt(scrollPos);
                }

                // Save scroll position on scroll
                sidebar.addEventListener('scroll', function () {
                    localStorage.setItem('sidebarScrollTop', sidebar.scrollTop);
                });
            }
        });
    </script>
</body>

</html>