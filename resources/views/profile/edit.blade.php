@extends('layouts.app')

@section('content')
<h3 class="mb-4 fw-bold">Pengaturan Akun</h3>

<div class="row">
    {{-- BAGIAN KIRI: EDIT BIODATA & FOTO --}}
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-person-circle me-2"></i> Profil Saya
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="text-center mb-4">
                        {{-- 1. Beri ID pada IMG agar bisa dimanipulasi Javascript --}}
                        <div class="mx-auto" style="width: 100px; height: 100px; overflow: hidden; border-radius: 50%;">
                            @if(Auth::user()->avatar)
                                <img id="avatar-preview" src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-100 h-100" style="object-fit: cover;">
                            @else
                                {{-- Placeholder jika belum ada foto --}}
                                <img id="avatar-preview" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" class="w-100 h-100" style="object-fit: cover;">
                            @endif
                        </div>

                        <div class="mt-2">
                            <label for="avatar" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-camera"></i> Ganti Foto
                            </label>
                            
                            {{-- 2. HAPUS onchange="form.submit()" --}}
                            {{-- GANTI DENGAN onchange="previewImage()" --}}
                            <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*" onchange="previewImage(event)">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Jabatan / Divisi</label>
                        <input type="text" class="form-control bg-light" value="{{ $user->position }} - {{ $user->division->name ?? 'Umum' }}" readonly>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- BAGIAN KANAN: GANTI PASSWORD --}}
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-bold text-danger">
                <i class="bi bi-shield-lock me-2"></i> Ganti Password
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('avatar-preview');
            output.src = reader.result; // Ganti src gambar dengan file yang baru dipilih
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection