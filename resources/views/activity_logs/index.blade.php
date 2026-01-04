@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div class="fw-bold">
            <i class="bi bi-activity me-2"></i> Semua Log Aktivitas Sistem
        </div>
    </div>
    <div class="card-body">
        
        {{-- FORM FILTER DIVISI --}}
        <form action="{{ route('activity_logs.index') }}" method="GET" class="mb-4 p-3 bg-light border rounded">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <span class="fw-bold text-muted small">FILTER LOG:</span>
                </div>
                <div class="col-md-4">
                    <select name="division_id" class="form-select form-select-sm">
                        <option value="">-- Tampilkan Semua Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>
                                Divisi {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    @if(request('division_id'))
                        <a href="{{ route('activity_logs.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        {{-- TABEL DATA --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle small">
                <thead class="table-dark">
                    <tr>
                        <th width="15%">Waktu</th>
                        <th width="15%">User (Pelaku)</th>
                        <th>Divisi</th> <th width="10%">Action</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td>
                            @if($log->user)
                                <span class="fw-bold">{{ $log->user->name }}</span><br>
                                <span class="text-muted" style="font-size:0.8em">({{ $log->user->role }})</span>
                            @else
                                <span class="text-danger">System/Guest</span>
                            @endif
                        </td>
                        <td>
                            {{-- Tampilkan Nama Divisi --}}
                            @if($log->user && $log->user->division)
                                <span class="badge bg-secondary">{{ $log->user->division->name }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($log->action == 'CREATE')
                                <span class="badge bg-success">CREATE</span>
                            @elseif($log->action == 'UPDATE')
                                <span class="badge bg-warning text-dark">UPDATE</span>
                            @elseif($log->action == 'DELETE')
                                <span class="badge bg-danger">DELETE</span>
                            @elseif($log->action == 'LOGIN')
                                <span class="badge bg-info text-dark">LOGIN</span>
                            @else
                                <span class="badge bg-secondary">{{ $log->action }}</span>
                            @endif
                        </td>
                        <td>{{ $log->description }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Tidak ada log aktivitas untuk kriteria ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination (Agar filter tidak hilang saat pindah halaman) --}}
        <div class="mt-3">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection