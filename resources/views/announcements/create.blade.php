@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Buat Pengumuman Baru</div>
            <div class="card-body">
                <form action="{{ route('announcements.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" required placeholder="Contoh: Libur Cuti Bersama">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe (Warna)</label>
                        <select name="type" class="form-select">
                            <option value="info">Info (Biru)</option>
                            <option value="warning">Peringatan (Kuning)</option>
                            <option value="danger">Penting / Darurat (Merah)</option>
                            <option value="success">Berita Baik (Hijau)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Pengumuman</label>
                        <textarea name="content" class="form-control" rows="4" required placeholder="Tulis detail pengumuman di sini..."></textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Terbitkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection