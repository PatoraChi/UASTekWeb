@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Tambah User Baru</div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        {{-- LOGIKA TAMPILAN: BEDA ANTARA SUPER ADMIN DAN ADMIN --}}
                        @if(Auth::user()->role == 'super_admin')
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role (Peran)</label>
                                <select name="role" class="form-select" required>
                                    <option value="user">Pegawai (User)</option>
                                    <option value="admin">Admin Divisi</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Divisi</label>
                                <select name="division_id" class="form-select">
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->id }}">{{ $div->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Kosongkan jika Super Admin</small>
                            </div>
                        @else
                            {{-- JIKA ADMIN BIASA: INPUT HIDDEN (OTOMATIS) --}}
                            <input type="hidden" name="role" value="user">
                            
                            <div class="col-12 mb-3">
                                <div class="alert alert-info py-2 small">
                                    <i class="bi bi-info-circle"></i> User baru akan otomatis masuk ke Divisi: <strong>{{ Auth::user()->division->name }}</strong>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jabatan (Position)</label>
                            <input type="text" name="position" class="form-control" placeholder="Contoh: Staff IT">
                        </div>

                        {{-- PENGATURAN GAJI & TUNJANGAN --}}
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Pengaturan Gaji</label>
                            <div class="card p-3 bg-light">
                                <div class="mb-3">
                                    <label class="form-label">Gaji Pokok (Base Salary)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="base_salary" class="form-control" required placeholder="Contoh: 5000000">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label small">Tunjangan Jabatan (Bulanan)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" name="position_allowance" class="form-control" value="0">
                                        </div>
                                        <div class="form-text" style="font-size: 0.7rem">Diterima utuh per bulan.</div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label small">Uang Makan (Per Hari)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" name="meal_allowance" class="form-control" value="0">
                                        </div>
                                        <div class="form-text" style="font-size: 0.7rem">Dikali jumlah kehadiran.</div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label small">Uang Transport (Per Hari)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" name="transport_allowance" class="form-control" value="0">
                                        </div>
                                        <div class="form-text" style="font-size: 0.7rem">Dikali jumlah kehadiran.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection