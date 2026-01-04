@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-dark">
            <div class="card-header bg-white text-center border-bottom-0 pb-0">
                <h4 class="fw-bold mb-0">SLIP GAJI PEGAWAI</h4>
                <p class="text-muted">Periode: {{ $payroll->month }} {{ $payroll->year }}</p>
            </div>
            <div class="card-body">
                <hr>
                <div class="row mb-4">
                    <div class="col-6">
                        <strong>Nama:</strong> {{ $payroll->user->name }}<br>
                        <strong>Jabatan:</strong> {{ $payroll->user->position ?? '-' }}<br>
                        <strong>Divisi:</strong> {{ $payroll->user->division->name ?? '-' }}
                    </div>
                    <div class="col-6 text-end">
                        <strong>Tanggal Cetak:</strong> {{ $payroll->created_at->format('d M Y') }}
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Keterangan</th>
                            <th class="text-end">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Gaji Pokok</strong></td>
                            <td class="text-end">{{ number_format($payroll->basic_salary, 0, ',', '.') }}</td>
                        </tr>
                        
                        {{-- RINCIAN TUNJANGAN --}}
                        <tr>
                            <td colspan="2" class="bg-light fw-bold small">Rincian Tunjangan</td>
                        </tr>
                        @php
                            // Ambil data tarif user (Snapshot saat ini)
                            // Catatan: Idealnya tarif ini disimpan di tabel payrolls juga agar histori aman jika tarif user berubah.
                            // Tapi untuk UAS, ambil dari user->relation saja cukup.
                            $u = $payroll->user;
                            
                            // Kita hitung mundur (Reverse Engineering) untuk display, atau tampilkan Total saja jika ingin simpel.
                            // Di sini kita tampilkan Total Gabungan sesuai database agar akurat
                        @endphp
                        
                        <tr>
                            <td>Total Tunjangan (Jabatan + Makan + Transport)</td>
                            <td class="text-end text-success">+ {{ number_format($payroll->allowances, 0, ',', '.') }}</td>
                        </tr>

                        <tr>
                            <td>Lembur / Overtime</td>
                            <td class="text-end text-success">+ {{ number_format($payroll->overtime_pay, 0, ',', '.') }}</td>
                        </tr>
                        
                        {{-- RINCIAN POTONGAN --}}
                        <tr>
                            <td colspan="2" class="bg-light fw-bold small">Potongan</td>
                        </tr>
                        <tr>
                            <td class="text-danger">Total Potongan (Telat & Alpha)</td>
                            <td class="text-end text-danger">- {{ number_format($payroll->deductions, 0, ',', '.') }}</td>
                        </tr>
                        
                        <tr class="table-dark fw-bold" style="font-size: 1.1rem">
                            <td>TOTAL GAJI DITERIMA (TAKE HOME PAY)</td>
                            <td class="text-end">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4 text-center">
                    <a href="{{ route('payrolls.downloadSlip', $payroll->id) }}" target="_blank" class="btn btn-danger btn-sm">
                        <i class="bi bi-file-pdf"></i> Download Slip PDF
                    </a>
                    <a href="{{ route('payrolls.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection