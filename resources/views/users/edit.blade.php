@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Edit User: {{ $user->name }}</div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    {{-- LOGIKA TAMPILAN EDIT --}}
                    @if(Auth::user()->role == 'super_admin')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Pegawai (User)</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin Divisi</option>
                                    <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Divisi</label>
                                <select name="division_id" class="form-select">
                                    <option value="">-- Tidak Ada Divisi --</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->id }}" {{ $user->division_id == $div->id ? 'selected' : '' }}>
                                            {{ $div->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @else
                        {{-- ADMIN DIVISI HANYA MELIHAT DATA (READONLY) --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Role</label>
                                <input type="text" class="form-control bg-light" value="{{ $user->role }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Divisi</label>
                                <input type="text" class="form-control bg-light" value="{{ $user->division->name ?? '-' }}" readonly>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Jabatan (Position)</label>
                        <input type="text" name="position" class="form-control" value="{{ old('position', $user->position) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti password">
                    </div>

                    <hr>
                    <h5 class="mb-3 text-primary"><i class="bi bi-cash-stack me-2"></i>Pengaturan Gaji</h5>

                    <div class="card p-3 bg-light mb-3">
                        <div class="mb-3">
                            <label class="form-label">Gaji Pokok (Base Salary)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="base_salary" class="form-control" value="{{ old('base_salary', $user->base_salary) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label small">Tunjangan Jabatan (Bulanan)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="position_allowance" class="form-control" value="{{ old('position_allowance', $user->position_allowance) }}">
                                </div>
                                <div class="form-text" style="font-size: 0.7rem">Diterima utuh per bulan.</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label small">Uang Makan (Per Hari)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="meal_allowance" class="form-control" value="{{ old('meal_allowance', $user->meal_allowance) }}">
                                </div>
                                <div class="form-text" style="font-size: 0.7rem">Dikali jumlah kehadiran.</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label small">Uang Transport (Per Hari)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="transport_allowance" class="form-control" value="{{ old('transport_allowance', $user->transport_allowance) }}">
                                </div>
                                <div class="form-text" style="font-size: 0.7rem">Dikali jumlah kehadiran.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jatah Cuti Tahunan</label>
                        <input type="number" name="leave_quota" class="form-control" value="{{ old('leave_quota', $user->leave_quota) }}">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection