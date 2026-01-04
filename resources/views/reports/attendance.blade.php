@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-spreadsheet me-2"></i> Laporan Rekap Absensi</h5>
    </div>
    
    <div class="card-body">
        {{-- FORM FILTER --}}
        <form action="{{ route('reports.attendance') }}" method="GET" class="row g-2 mb-4 align-items-end p-3 bg-light rounded d-print-none">
            
            {{-- Filter Divisi (Hanya Muncul untuk Super Admin) --}}
            @if(Auth::user()->role == 'super_admin')
            <div class="col-md-3">
                <label class="form-label small fw-bold">Divisi</label>
                <select name="division_id" class="form-select">
                    <option value="">-- Semua Divisi --</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ $divisionId == $div->id ? 'selected' : '' }}>
                            {{ $div->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="col-md-3">
                <label class="form-label small fw-bold">Bulan</label>
                <select name="month" class="form-select">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold">Tahun</label>
                <select name="year" class="form-select">
                    @for($y = 2024; $y <= date('Y'); $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i> Tampilkan
                </button>
            </div>
            
            <div class="col-md-2">
                <a href="{{ route('reports.attendance.pdf', request()->all()) }}" target="_blank" class="btn btn-danger btn-sm w-100">
                    <i class="bi bi-file-pdf"></i> Download PDF
                </a>
            </div>
        </form>

        {{-- JUDUL LAPORAN (Untuk Print) --}}
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-0">LAPORAN KEHADIRAN PEGAWAI</h4>
            <p class="text-muted mb-0">
                Periode: {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}
            </p>
            @if($divisionId)
                @php $namaDivisi = \App\Models\Division::find($divisionId)->name ?? '-'; @endphp
                <p class="fw-bold">Divisi: {{ $namaDivisi }}</p>
            @endif
        </div>

        {{-- TABEL DATA --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th class="text-start">Nama Pegawai</th>
                        <th>Divisi</th>
                        <th>Jml Hadir (Hari)</th>
                        <th>Total Terlambat (Menit)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $index => $data)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start fw-bold">{{ $data['name'] }}</td>
                        <td>{{ $data['division'] }}</td>
                        
                        <td>
                            @if($data['total_present'] > 0)
                                <span class="badge bg-success" style="font-size: 0.9rem">{{ $data['total_present'] }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        
                        <td class="{{ $data['total_late'] > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                            {{ $data['total_late'] }} Menit
                        </td>
                        
                        <td>
                            @if($data['total_present'] >= 20) 
                                <span class="badge bg-success">Rajin</span>
                            @elseif($data['total_present'] >= 10)
                                <span class="badge bg-warning text-dark">Cukup</span>
                            @else
                                <span class="badge bg-danger">Kurang</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-muted py-4">Tidak ada data absensi pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER TTD (Hanya muncul saat Print) --}}
        <div class="d-none d-print-block mt-5">
            <div class="row">
                <div class="col-4 offset-8 text-center">
                    <p>Bali, {{ date('d F Y') }}</p>
                    <p class="mb-5">Mengetahui,<br>Kepala Bagian HRD</p>
                    <br>
                    <p class="fw-bold text-decoration-underline">{{ Auth::user()->name }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS KHUSUS PRINT --}}
<style>
    @media print {
        .d-print-none { display: none !important; }
        .d-print-block { display: block !important; }
        .card { border: none !important; shadow: none !important; }
        .card-header { display: none !important; }
        body { background-color: white !important; }
    }
</style>
@endsection