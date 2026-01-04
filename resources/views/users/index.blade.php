@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Manajemen User (Pegawai & Admin)</h5>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus"></i> Tambah User
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Divisi</th>
                        <th>Jabatan</th>
                        <th>Gaji Pokok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $user->name }}</div>
                            <div class="small text-muted">{{ $user->email }}</div>
                        </td>
                        <td>
                            @if($user->role == 'super_admin')
                                <span class="badge bg-dark">Super Admin</span>
                            @elseif($user->role == 'admin')
                                <span class="badge bg-primary">Admin Divisi</span>
                            @else
                                <span class="badge bg-secondary">Pegawai</span>
                            @endif
                        </td>
                        <td>{{ $user->division->name ?? '-' }}</td>
                        <td>{{ $user->position ?? '-' }}</td>
                        <td>Rp {{ number_format($user->base_salary, 0, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?');">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada data user.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection