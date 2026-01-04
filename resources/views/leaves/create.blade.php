@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        
        {{-- TAMPILKAN EROR DI SINI --}}
        @if(session('error'))
            <div class="alert alert-danger mb-3">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- TAMPILKAN EROR VALIDASI (Misal: Lupa upload file) --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Form Pengajuan Cuti</div>
            <div class="card-body">
                
                {{-- PENTING: Tambahkan enctype agar bisa upload file --}}
                <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Jenis Cuti / Izin</label>
                        <select name="type" class="form-select" required>
                            <option value="annual">Cuti Tahunan (Potong Kuota)</option>
                            <option value="sick">Sakit (Wajib Surat Dokter)</option>
                            <option value="emergency">Izin Darurat / Duka</option>
                        </select>
                        <div class="form-text text-muted">Sisa Cuti Tahunan: <strong>{{ Auth::user()->leave_quota }} Hari</strong></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mulai</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Selesai</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alasan</label>
                        <textarea name="reason" class="form-control" rows="2" required></textarea>
                    </div>

                    {{-- INPUT FILE BARU --}}
                    <div class="mb-3">
                        <label class="form-label">Lampiran (Surat Dokter / Bukti)</label>
                        <input type="file" name="attachment" class="form-control">
                        <div class="form-text text-danger">* Wajib jika memilih Izin Sakit (Max 2MB)</div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('leaves.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection