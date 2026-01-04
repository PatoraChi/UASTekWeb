@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Generate Gaji Bulanan</div>
            <div class="card-body">
                <div class="alert alert-info small">
                    <i class="bi bi-info-circle"></i> Sistem akan otomatis menghitung gaji pokok dikurangi denda keterlambatan (Rp 1.000/menit) untuk semua pegawai di divisi Anda.
                </div>

                <form action="{{ route('payrolls.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Pilih Bulan</label>
                        <select name="month" class="form-select" required>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="year" class="form-control" value="{{ date('Y') }}" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-calculator"></i> Proses Hitung Gaji
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection