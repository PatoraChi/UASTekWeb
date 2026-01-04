@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div class="fw-bold">
            <i class="bi bi-calendar-check me-2"></i> Riwayat Absensi
        </div>
        
        <a href="{{ route('reports.attendance.pdf', request()->all()) }}" target="_blank" class="btn btn-danger btn-sm">
            <i class="bi bi-file-pdf"></i> Download PDF
        </a>
    </div>
    
    <div class="card-body">
        
        {{-- FORM FILTER --}}
        <div class="mb-4 p-3 bg-light rounded border d-print-none">
            <form action="{{ route('attendance.index') }}" method="GET" class="row g-2 align-items-end">
                
                {{-- Filter Divisi (Hanya Super Admin) --}}
                @if(Auth::user()->role == 'super_admin')
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Divisi</label>
                    <select name="division_id" class="form-select form-select-sm">
                        <option value="">-- Semua Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>
                                Divisi {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Filter Tanggal (Untuk Semua User) --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-filter"></i> Tampilkan
                    </button>
                    @if(request()->hasAny(['division_id', 'start_date', 'end_date']))
                        <a href="{{ route('attendance.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABEL DATA (TAMPILAN TETAP SAMA SEPERTI SEBELUMNYA) --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Pegawai</th>
                        <th>Divisi</th> 
                        <th>Status</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Keterlambatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                        <td>
                            <div class="fw-bold">{{ $att->user->name }}</div>
                            <div class="small text-muted">{{ $att->user->role == 'admin' ? 'Admin' : 'Staff' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark">
                                {{ $att->user->division->name ?? '-' }}
                            </span>
                        </td>
                        <td>
                            @if($att->status == 'present')
                                <span class="badge bg-success">Hadir</span>
                            @elseif($att->status == 'late')
                                <span class="badge bg-warning text-dark">Terlambat</span>
                            @elseif($att->status == 'sick')
                                <span class="badge bg-primary">Sakit</span>
                            @elseif($att->status == 'leave')
                                <span class="badge bg-info text-dark">Cuti</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($att->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $att->check_in ?? '-' }}</td>
                        <td>{{ $att->check_out ?? '-' }}</td>
                        <td>
                            @if($att->late_minutes > 0)
                                <span class="text-danger fw-bold">{{ $att->late_minutes }} Menit</span>
                            @else
                                <span class="text-success">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada data absensi yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection