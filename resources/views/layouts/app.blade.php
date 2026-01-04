<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HRIS System') }}</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* 1. Kunci Scrollbar Utama Browser */
        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden; /* Sembunyikan scrollbar bawaan browser */
        }

        /* 2. Wrapper Utama: Flexbox memenuhi layar */
        #wrapper {
            display: flex;
            width: 100%;
            height: 100vh; /* Tinggi paksa 100% viewport (layar) */
            overflow: hidden; /* Mencegah wrapper utama scroll */
        }

        /* 3. Sidebar: Diam di kiri, punya scroll sendiri jika menu panjang */
        #sidebar-wrapper {
            width: 250px;
            background: #212529;
            color: #fff;
            flex-shrink: 0;
            height: 100%;      /* Penuh ke bawah */
            overflow-y: auto;  /* Scrollbar muncul CUMA di sidebar jika menu kepanjangan */
        }

        /* 4. Konten Kanan: Bergerak bebas, punya scroll sendiri */
        #page-content-wrapper {
            flex-grow: 1;
            width: 100%;
            height: 100%;      /* Penuh ke bawah */
            overflow-y: auto;  /* Scrollbar muncul CUMA di konten kanan */
            background-color: #f8f9fa; 
        }

        /* Styling Tambahan untuk Sidebar */
        .sidebar-heading {
            padding: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            background: #1a1e21;
            border-bottom: 1px solid #444;
            /* Opsional: Membuat Judul HRIS sticky di dalam sidebar */
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .list-group-item {
            background: #212529;
            color: #bbb;
            border: none;
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05); /* Garis tipis pemisah menu */
        }

        .list-group-item:hover {
            background: #343a40;
            color: #fff;
            padding-left: 25px; /* Efek geser dikit pas hover */
            transition: 0.2s;
        }

        .list-group-item.active {
            background: #0d6efd;
            color: #fff;
            font-weight: bold;
            border-left: 4px solid #fff; /* Aksen aktif di kiri */
        }

        .list-group-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Mempercantik Scrollbar (Chrome/Edge/Safari) */
        #sidebar-wrapper::-webkit-scrollbar, 
        #page-content-wrapper::-webkit-scrollbar {
            width: 8px;
        }
        #sidebar-wrapper::-webkit-scrollbar-thumb, 
        #page-content-wrapper::-webkit-scrollbar-thumb {
            background-color: #adb5bd;
            border-radius: 4px;
        }
        #sidebar-wrapper::-webkit-scrollbar-track, 
        #page-content-wrapper::-webkit-scrollbar-track {
            background-color: rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

    {{-- KONDISI 1: JIKA USER SUDAH LOGIN (TAMPILKAN SIDEBAR) --}}
    @auth
    <div id="wrapper">
        <div id="sidebar-wrapper">
            <div class="sidebar-heading">HRIS System</div>
            <div class="list-group list-group-flush">
                
                {{-- MENU UMUM (PENGUMUMAN) --}}
                @if(Auth::user()->role != 'user')
                    <a href="{{ route('announcements.index') }}" class="list-group-item list-group-item-action {{ Request::routeIs('announcements*') ? 'active' : '' }}">
                        <i class="bi bi-broadcast"></i> Kelola Pengumuman
                    </a>
                @endif

                {{-- MENU SUPER ADMIN --}}
                @if(Auth::user()->role == 'super_admin')
                    <div class="text-muted small fw-bold px-3 mt-3 mb-1">SUPER ADMIN</div>
                    <a href="{{ url('/home') }}" class="list-group-item list-group-item-action {{ Request::is('home') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="{{ route('divisions.index') }}" class="list-group-item list-group-item-action {{ Request::is('divisions*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i> Manajemen Divisi
                    </a>
                    <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action {{ Request::is('users*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Manajemen User (All)
                    </a>
                    
                    {{-- PERBAIKAN: Gunakan routeIs('attendance.*') --}}
                    <a href="{{ route('attendance.index') }}" class="list-group-item list-group-item-action {{ Request::routeIs('attendance.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i> Monitoring Absensi
                    </a>
                   
                    <a href="{{ route('reports.attendance') }}" class="list-group-item list-group-item-action {{ Request::routeIs('reports.attendance*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Laporan Absensi
                    </a>
                    <a href="{{ route('reports.payroll') }}" class="list-group-item list-group-item-action {{ Request::routeIs('reports.payroll*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i> Laporan Gaji
                    </a>
                    <a href="{{ route('activity_logs.index') }}" class="list-group-item list-group-item-action {{ Request::is('activity-logs*') ? 'active' : '' }}">
                        <i class="bi bi-activity"></i> Log Aktivitas
                    </a>
                @endif

                {{-- MENU ADMIN DIVISI --}}
                @if(Auth::user()->role == 'admin')
                    <div class="text-muted small fw-bold px-3 mt-3 mb-1">ADMIN DIVISI</div>
                    <a href="{{ url('/home') }}" class="list-group-item list-group-item-action {{ Request::is('home') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill"></i> Dashboard
                    </a>
                    <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action {{ Request::is('users*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Manajemen User
                    </a>
                    
                    {{-- PERBAIKAN: Gunakan routeIs('attendance.*') --}}
                    <a href="{{ route('attendance.index') }}" class="list-group-item list-group-item-action {{ Request::routeIs('attendance.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i> Monitoring Absensi
                    </a>
            
                    <a href="{{ route('reports.attendance') }}" class="list-group-item list-group-item-action {{ Request::routeIs('reports.attendance*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Laporan Absensi
                    </a>
                    <a href="{{ route('reports.payroll') }}" class="list-group-item list-group-item-action {{ Request::routeIs('reports.payroll*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i> Laporan Gaji
                    </a>
                    
                    <a href="{{ route('leaves.index') }}" class="list-group-item list-group-item-action {{ Request::is('leaves*') ? 'active' : '' }}">
                        <i class="bi bi-envelope-check"></i> Approval Cuti
                    </a>
                    <a href="{{ route('payrolls.index') }}" class="list-group-item list-group-item-action {{ Request::is('payrolls*') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i> Generate Gaji
                    </a>
                @endif

                {{-- MENU PEGAWAI --}}
                @if(Auth::user()->role == 'user')
                    <div class="text-muted small fw-bold px-3 mt-3 mb-1">PEGAWAI</div>
                    <a href="{{ url('/home') }}" class="list-group-item list-group-item-action {{ Request::is('home') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                    
                    {{-- PERBAIKAN: Gunakan routeIs('attendance.*') --}}
                    <a href="{{ route('attendance.index') }}" class="list-group-item list-group-item-action {{ Request::routeIs('attendance.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i> Riwayat Absensi
                    </a>
                    
                    <a href="{{ route('leaves.index') }}" class="list-group-item list-group-item-action {{ Request::is('leaves*') ? 'active' : '' }}">
                        <i class="bi bi-envelope-paper"></i> Pengajuan Cuti
                    </a>
                    <a href="{{ route('payrolls.index') }}" class="list-group-item list-group-item-action {{ Request::is('payrolls*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-pdf"></i> Slip Gaji
                    </a>
                @endif

                <div class="mt-4 border-top border-secondary"></div>
                <a href="{{ route('logout') }}" class="list-group-item list-group-item-action text-danger"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
                <div class="container-fluid">
                    <span class="navbar-text ms-3 fw-bold text-uppercase">
                        {{-- Cek dulu role sebelum menampilkan teks area --}}
                        @if(Auth::user()->role == 'super_admin')
                            Super Admin Area
                        @elseif(Auth::user()->role == 'admin')
                            Divisi {{ Auth::user()->division->name ?? 'Umum' }}
                        @else
                            Pegawai Area
                        @endif
                    </span>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto">
                        @guest
                        @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                
                                {{-- Tampilkan Avatar Kecil di Navbar --}}
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="rounded-circle border" width="30" height="30" style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                                
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person-gear me-2"></i> Pengaturan Akun
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endguest
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="container-fluid p-4">
                @yield('content')
            </div>
        </div>
    </div>
    @endauth

    {{-- KONDISI 2: JIKA USER BELUM LOGIN / GUEST (TAMPILKAN LAYOUT BIASA) --}}
    @guest
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'HRIS System') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @endguest

</body>
</html>