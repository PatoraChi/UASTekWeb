@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Manajemen Divisi</h5>
        <a href="{{ route('divisions.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Tambah Divisi
        </a>
    </div>
    <div class="card-body">
        
        {{-- Pesan Sukses/Eror --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Divisi</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($divisions as $key => $div)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $div->name }}</td>
                    <td>
                        <form action="{{ route('divisions.destroy', $div->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus divisi ini?');">
                            <a href="{{ route('divisions.edit', $div->id) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection