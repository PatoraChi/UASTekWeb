@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Tambah Divisi Baru</div>
            <div class="card-body">
                <form action="{{ route('divisions.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Divisi</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Marketing" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('divisions.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection