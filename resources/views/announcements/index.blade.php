@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Kelola Pengumuman Kantor</h5>
        <a href="{{ route('announcements.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-megaphone"></i> Buat Pengumuman
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Judul & Isi</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $ann)
                    <tr>
                        <td width="15%">{{ $ann->created_at->format('d M Y') }}</td>
                        <td>
                            <strong>{{ $ann->title }}</strong><br>
                            <small class="text-muted">{{ Str::limit($ann->content, 50) }}</small>
                        </td>
                        <td>
                            @if($ann->type == 'info') <span class="badge bg-info text-dark">Info</span>
                            @elseif($ann->type == 'warning') <span class="badge bg-warning text-dark">Peringatan</span>
                            @elseif($ann->type == 'danger') <span class="badge bg-danger">Penting</span>
                            @else <span class="badge bg-success">Sukses</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('announcements.toggle', $ann->id) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-sm {{ $ann->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }}">
                                    {{ $ann->is_active ? 'Aktif (Tayang)' : 'Non-Aktif (Sembunyi)' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('announcements.destroy', $ann->id) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">Belum ada pengumuman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection