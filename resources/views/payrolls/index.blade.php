@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Riwayat Penggajian</h5>
        @if(Auth::user()->role != 'user')
        <a href="{{ route('payrolls.create') }}" class="btn btn-success btn-sm">
            <i class="bi bi-cash-stack"></i> Generate Gaji Baru
        </a>
        @endif
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Periode</th>
                    <th>Nama Pegawai</th>
                    <th>Gaji Pokok</th>
                    <th>Potongan (Telat)</th>
                    <th>Total Bersih</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrolls as $pay)
                <tr>
                    <td>{{ $pay->month }} {{ $pay->year }}</td>
                    <td>
                        <div class="fw-bold">{{ $pay->user->name }}</div>
                        <div class="small text-muted">{{ $pay->user->division->name ?? '-' }}</div>
                    </td>
                    <td>Rp {{ number_format($pay->basic_salary, 0, ',', '.') }}</td>
                    <td class="text-danger">- Rp {{ number_format($pay->deductions, 0, ',', '.') }}</td>
                    <td class="fw-bold text-success">Rp {{ number_format($pay->net_salary, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('payrolls.show', $pay->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-eye"></i> Slip
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada data gaji.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection