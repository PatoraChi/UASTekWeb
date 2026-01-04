@extends('layouts.app')

@section('content')
{{-- PENGUMUMAN SECTION --}}
@if(isset($announcements) && $announcements->count() > 0)
    <div class="mb-4">
        @foreach($announcements as $ann)
            <div class="alert alert-{{ $ann->type }} alert-dismissible fade show shadow-sm" role="alert">
                <h5 class="alert-heading fw-bold">
                    <i class="bi bi-megaphone-fill me-2"></i> {{ $ann->title }}
                </h5>
                <hr>
                <p class="mb-0">{{ $ann->content }}</p>
                <div class="mt-2 small opacity-75">
                    Diterbitkan: {{ $ann->created_at->format('d F Y, H:i') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endforeach
    </div>
@endif
<h2 class="mb-2">Halo, {{ Auth::user()->name }}!</h2>
<p class="text-muted mb-4">Selamat bekerja, jangan lupa berdoa.</p>

{{-- Pesan Notifikasi --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <h5 class="text-muted">Status Hari Ini</h5>

                {{-- LOGIKA TAMPILAN TOMBOL --}}
                @if(!$todayAttendance)
                    {{-- KONDISI 1: BELUM ABSEN --}}
                    <h2 class="text-danger fw-bold my-3">BELUM ABSEN</h2>
                    <p class="small text-muted">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
                    
                    <form action="{{ route('attendance.checkIn') }}" method="POST">
                        @csrf
                        <div class="d-grid gap-2 col-8 mx-auto">
                            <button type="submit" class="btn btn-primary btn-lg py-3 shadow-sm">
                                <i class="bi bi-fingerprint me-2"></i> ABSEN MASUK
                            </button>
                        </div>
                    </form>

                @elseif($todayAttendance->check_in && !$todayAttendance->check_out)
                    {{-- KONDISI 2: SUDAH MASUK, BELUM PULANG --}}
                    <h2 class="text-warning fw-bold my-3">SEDANG BEKERJA</h2>
                    <p class="mb-1">Masuk jam: <strong>{{ $todayAttendance->check_in }}</strong></p>
                    <p class="small text-muted mb-4">
                        Status: 
                        @if($todayAttendance->status == 'late')
                            <span class="badge bg-danger">Terlambat {{ $todayAttendance->late_minutes }} Menit</span>
                        @else
                            <span class="badge bg-success">Tepat Waktu</span>
                        @endif
                    </p>

                    <form action="{{ route('attendance.checkOut') }}" method="POST">
                        @csrf
                        <div class="d-grid gap-2 col-8 mx-auto">
                            <button type="submit" class="btn btn-secondary btn-lg py-3 shadow-sm">
                                <i class="bi bi-box-arrow-right me-2"></i> ABSEN PULANG
                            </button>
                        </div>
                    </form>

                @else
                    {{-- KONDISI 3: SUDAH SELESAI --}}
                    <h2 class="text-success fw-bold my-3">SUDAH PULANG</h2>
                    <p class="mb-0">Masuk: {{ $todayAttendance->check_in }}</p>
                    <p class="mb-4">Pulang: {{ $todayAttendance->check_out }}</p>
                    <div class="alert alert-success">Terima kasih atas kerja kerasmu hari ini!</div>
                @endif

            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="row g-3">
            <div class="col-6">
                <div class="card border-info h-100">
                    <div class="card border-info h-100">
                        <div class="card-body text-center">
                            {{-- PANGGIL VARIABLE DARI AUTH --}}
                            <h1 class="fw-bold text-info">{{ Auth::user()->leave_quota }}</h1>
                            <span class="small text-muted">Sisa Cuti (Hari)</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-danger h-100">
                    <div class="card-body text-center">
                        {{-- Tampilkan Total Keterlambatan Bulan Ini (Optional Logic Nanti) --}}
                        <h1 class="fw-bold text-danger">0</h1>
                        <span class="small text-muted">Total Telat (Menit)</span>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card bg-light h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold">Slip Gaji Terakhir</span><br>
                            <span class="small text-muted">Januari 2026</span>
                        </div>
                        <button class="btn btn-outline-dark btn-sm">
                            <i class="bi bi-download"></i> PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection