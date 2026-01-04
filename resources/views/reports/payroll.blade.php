@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bi bi-cash-coin me-2"></i> Laporan Rekap Gaji</h5>
    </div>
    
    <div class="card-body">
        {{-- FORM FILTER --}}
        <form action="{{ route('reports.payroll') }}" method="GET" class="row g-2 mb-4 align-items-end p-3 bg-light rounded d-print-none">
            
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
                <a href="{{ route('reports.payroll.pdf', request()->all()) }}" target="_blank" class="btn btn-danger w-100">
                    <i class="bi bi-file-pdf"></i> Download PDF
                </a>
            </div>
        </form>

        {{-- JUDUL LAPORAN (Print Only) --}}
        <div class="text-center mb-4 d-none d-print-block">
            <h4 class="fw-bold mb-0">LAPORAN PENGGAJIAN PERUSAHAAN</h4>
            <p class="text-muted mb-0">
                Periode: {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}
            </p>
        </div>

        {{-- TABEL DATA --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle text-center table-sm">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th class="text-start">Nama Pegawai</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan (+)</th>
                        <th>Lembur (+)</th>
                        <th>Potongan (-)</th>
                        <th class="bg-success text-white">Total Diterima</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start">
                            <div class="fw-bold">{{ $p->user->name }}</div>
                            <small class="text-muted">{{ $p->user->division->name ?? '-' }}</small>
                        </td>
                        <td class="text-end">{{ number_format($p->basic_salary, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($p->allowances, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($p->overtime_pay, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">{{ number_format($p->deductions, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">{{ number_format($p->net_salary, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-muted py-4">Belum ada data gaji yang digenerate pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
                {{-- FOOTER TOTAL --}}
                @if($payrolls->count() > 0)
                <tfoot class="table-secondary fw-bold">
                    <tr>
                        <td colspan="6" class="text-end">TOTAL PENGELUARAN GAJI BULAN INI:</td>
                        <td class="text-end text-success" style="font-size: 1.1em">
                            Rp {{ number_format($totalExpense, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        {{-- TANDA TANGAN --}}
        <div class="d-none d-print-block mt-5">
            <div class="row">
                <div class="col-4 text-center">
                    <p>Dibuat Oleh,</p>
                    <br><br><br>
                    <p class="fw-bold text-decoration-underline">{{ Auth::user()->name }}</p>
                    <p class="small">Admin HR</p>
                </div>
                <div class="col-4 offset-4 text-center">
                    <p>Bali, {{ date('d F Y') }}</p>
                    <p>Disetujui Oleh,</p>
                    <br><br><br>
                    <p class="fw-bold text-decoration-underline">_______________________</p>
                    <p class="small">Direktur Keuangan</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .d-print-none { display: none !important; }
        .d-print-block { display: block !important; }
        .card { border: none !important; shadow: none !important; }
        .card-header { display: none !important; }
        body { background-color: white !important; font-size: 12px; }
    }
</style>
@endsection