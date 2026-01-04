<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Landing Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .hero-section { background: linear-gradient(135deg, #0d6efd 0%, #0099ff 100%); color: white; padding: 100px 0; }
        .feature-icon { font-size: 2.5rem; color: #0d6efd; margin-bottom: 1rem; }
    </style>
</head>
<body>
    {{-- NAVBAR --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#"><i class="bi bi-building"></i> HRIS PRO</a>
            <div class="d-flex">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Login</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <section class="hero-section text-center mt-5">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Sistem Manajemen SDM Modern</h1>
            <p class="lead mb-4">Kelola Absensi, Gaji, dan Cuti Pegawai dalam satu aplikasi terintegrasi.</p>
            @guest
                <a href="{{ route('login') }}" class="btn btn-light btn-lg fw-bold text-primary px-5 shadow">Masuk Sekarang</a>
            @else
                <a href="{{ url('/home') }}" class="btn btn-light btn-lg fw-bold text-primary px-5 shadow">Buka Dashboard</a>
            @endguest
        </div>
    </section>

    {{-- FEATURES --}}
    <section class="py-5">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-md-4">
                    <div class="p-4 border rounded shadow-sm h-100">
                        <i class="bi bi-fingerprint feature-icon"></i>
                        <h5>Absensi Online</h5>
                        <p class="text-muted">Catat kehadiran secara real-time dengan sistem denda keterlambatan otomatis.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border rounded shadow-sm h-100">
                        <i class="bi bi-cash-stack feature-icon"></i>
                        <h5>Payroll Otomatis</h5>
                        <p class="text-muted">Hitung gaji, tunjangan, lembur, dan potongan alpha dalam sekali klik.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border rounded shadow-sm h-100">
                        <i class="bi bi-envelope-paper feature-icon"></i>
                        <h5>Cuti & Approval</h5>
                        <p class="text-muted">Pengajuan cuti digital dengan sistem persetujuan berjenjang yang transparan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <small>&copy; {{ date('Y') }} HRIS System. All Rights Reserved.</small>
        </div>
    </footer>
</body>
</html>