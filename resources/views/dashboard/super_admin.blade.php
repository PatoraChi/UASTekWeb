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
<h2 class="mb-4">Dashboard Overview</h2>

{{-- ... (Kode Card Statistik Total Divisi & User biarkan sama) ... --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <h1 class="display-4 fw-bold">{{ $totalDivisions }}</h1>
                <p>Total Divisi</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <h1 class="display-4 fw-bold">{{ $totalUsers }}</h1>
                <p>Total Admin/User</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <h1 class="display-4 fw-bold">0</h1>
                <p>Isu / Laporan</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- BAGIAN GRAFIK --}}
<div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div class="fw-bold">
                    <i class="bi bi-bar-chart-fill me-2"></i> Grafik Gaji per Divisi
                </div>
                
                {{-- FORM FILTER PERIODE --}}
                <form action="{{ route('home') }}" method="GET" class="d-flex gap-2">
                    <form action="{{ route('home') }}" method="GET" class="d-flex gap-2">
                    <select name="month" class="form-select form-select-sm" style="width: 150px;">
                        {{-- OPSI BARU: SEMUA BULAN --}}
                        <option value="all" {{ $filterMonth == 'all' ? 'selected' : '' }}>
                            -- 1 Tahun Penuh --
                        </option>
                        
                        {{-- OPSI BULAN 1-12 --}}
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $filterMonth == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select name="year" class="form-select form-select-sm" style="width: 80px;">
                        @for($y = 2024; $y <= date('Y'); $y++)
                            <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
                    
                    <!-- <select name="year" class="form-select form-select-sm" style="width: 80px;">
                        @for($y = 2024; $y <= date('Y'); $y++)
                            <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select> -->

                    <!-- <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search"></i>
                    </button> -->
                </form>
            </div>

            <div class="card-body">
                {{-- KETERANGAN PERIODE DINAMIS --}}
                <div class="text-center text-muted small mb-2">
                    Laporan: <strong>{{ $chartLabel }}</strong>
                </div>

                <div style="height: 300px;">
                    <canvas id="payrollChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- BAGIAN LOG AKTIVITAS (Biarkan sama) --}}
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-activity me-2"></i> Log Aktivitas Terbaru
            </div>
            <div class="card-body p-0">
                {{-- ... (Isi Log Aktivitas biarkan sama) ... --}}
                <ul class="list-group list-group-flush small">
                    @forelse($recentLogs as $log)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong class="{{ $log->action == 'DELETE' ? 'text-danger' : 'text-primary' }}">
                                    {{ $log->action }}
                                </strong>
                                <span class="text-muted" style="font-size: 0.8em">
                                    {{ $log->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <div>
                                <span class="fw-bold">{{ $log->user->name ?? 'System' }}:</span> 
                                {{ $log->description }}
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted">Belum ada aktivitas.</li>
                    @endforelse
                </ul>
                <div class="card-footer text-center p-1">
                    <a href="{{ route('activity_logs.index') }}" class="small text-decoration-none">Lihat Semua Log</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT DIPINDAHKAN KE DALAM SECTION --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('payrollChart');

    // Cek apakah data kosong agar tidak error di console
    const labels = {!! json_encode($labels) !!};
    const dataGaji = {!! json_encode($dataGaji) !!};

    if(ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels, 
                datasets: [{
                    label: {!! json_encode($chartLabel) !!}, 
                    data: dataGaji,
                    borderWidth: 1,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)', 
                        'rgba(255, 99, 132, 0.6)', 
                        'rgba(255, 206, 86, 0.6)', 
                        'rgba(75, 192, 192, 0.6)', 
                        'rgba(153, 102, 255, 0.6)', 
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
</script>

@endsection