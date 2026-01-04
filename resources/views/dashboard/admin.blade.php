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
<h2 class="mb-4">Dashboard Divisi: {{ Auth::user()->division->name ?? 'Umum' }}</h2>

{{-- Pesan Notifikasi --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow border-primary h-100">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="bi bi-fingerprint me-2"></i> Absensi Saya (Admin)
            </div>
            <div class="card-body text-center d-flex flex-column justify-content-center">
                {{-- Kita ambil data absen hari ini lewat Controller nanti --}}
                {{-- Karena variable $todayAttendance belum dikirim ke Admin, kita pakai Logika Blade Sederhana atau Update Controller --}}
                
                @php
                    $todayAtt = \App\Models\Attendance::where('user_id', Auth::id())
                                ->whereDate('date', now())
                                ->first();
                @endphp

                @if(!$todayAtt)
                    <h3 class="text-danger fw-bold my-3">BELUM ABSEN</h3>
                    <form action="{{ route('attendance.checkIn') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            ABSEN MASUK
                        </button>
                    </form>
                @elseif($todayAtt->check_in && !$todayAtt->check_out)
                    <h3 class="text-warning fw-bold my-3">SEDANG BEKERJA</h3>
                    <form action="{{ route('attendance.checkOut') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary w-100 py-2">
                            ABSEN PULANG
                        </button>
                    </form>
                @else
                    <h3 class="text-success fw-bold my-3">SUDAH PULANG</h3>
                    <div class="alert alert-success py-1">See you tomorrow!</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-cash-stack me-2"></i> Manajemen Gaji Divisi
            </div>
            <div class="card-body">
                 <p class="text-muted small">Hitung gaji seluruh pegawai di divisi {{ Auth::user()->division->name }}.</p>
                 <a href="{{ route('payrolls.create') }}" class="btn btn-success w-100">
                    <i class="bi bi-calculator"></i> Generate Gaji Sekarang
                 </a>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-people me-2"></i> Monitoring Pegawai
            </div>
            <div class="card-body">
                 <div class="d-flex justify-content-between text-center">
                     <div>
                         <h3>{{ $totalEmployees ?? 0 }}</h3>
                         <span class="small text-muted">Total Pegawai</span>
                     </div>
                     <div>
                         <a href="{{ route('attendance.index') }}" class="btn btn-outline-primary btn-sm mt-2">
                             Lihat Absensi Tim
                         </a>
                     </div>
                 </div>
            </div>
        </div>
    </div>
</div>
@endsection