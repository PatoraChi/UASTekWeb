@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Daftar Permohonan Cuti</h5>
        @if(Auth::user()->role == 'user')
            <a href="{{ route('leaves.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Ajukan Cuti
            </a>
        @endif
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                {{-- HEADER TABLE --}}
                <thead class="table-light">
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <th>Tipe & Durasi</th>
                        {{-- Kolom Pegawai hanya muncul untuk Admin --}}
                        @if(Auth::user()->role != 'user') 
                            <th>Pegawai</th> 
                        @endif
                        <th>Tanggal Cuti</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Aksi</th> {{-- Header Aksi ada di Paling Akhir --}}
                    </tr>
                </thead>
                
                {{-- BODY TABLE --}}
                <tbody>
                    @forelse($leaves as $leave)
                    <tr>
                        {{-- 1. Tanggal Pengajuan --}}
                        <td>{{ $leave->created_at->format('d M Y') }}</td>
                        
                        {{-- 2. Tipe & Durasi --}}
                        <td>
                            @if($leave->type == 'annual')
                                <span class="badge bg-primary">Tahunan</span>
                            @elseif($leave->type == 'sick')
                                <span class="badge bg-warning text-dark">Sakit</span>
                            @else
                                <span class="badge bg-secondary">Darurat</span>
                            @endif
                            <br><span class="small text-muted">{{ $leave->days }} Hari</span>
                        </td>

                        {{-- 3. Pegawai (Hanya Admin) --}}
                        @if(Auth::user()->role != 'user') 
                            <td>
                                <strong>{{ $leave->user->name }}</strong><br>
                                <span class="small text-muted">{{ $leave->user->division->name ?? '-' }}</span>
                            </td> 
                        @endif

                        {{-- 4. Tanggal Cuti --}}
                        <td>
                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} s/d 
                            {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                        </td>

                        {{-- 5. Alasan & Bukti --}}
                        <td>
                            {{ $leave->reason }}
                            @if($leave->attachment)
                                <div class="mt-1">
                                    <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0" style="font-size: 0.7rem;">
                                        <i class="bi bi-paperclip"></i> Lihat Bukti
                                    </a>
                                </div>
                            @endif
                            @if($leave->admin_note)
                                <br><small class="text-danger fst-italic">Note: {{ $leave->admin_note }}</small>
                            @endif
                        </td>

                        {{-- 6. Status --}}
                        <td>
                            @if($leave->status == 'pending')
                                <span class="badge bg-warning text-dark">Menunggu</span>
                            @elseif($leave->status == 'approved')
                                <span class="badge bg-success">Disetujui</span>
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </td>

                        {{-- 7. KOLOM AKSI (GABUNGAN USER & ADMIN) --}}
                        {{-- Posisi td ini harus paling akhir, sesuai header --}}
                        <td>
                            @if(Auth::user()->role != 'user')
                                {{-- LOGIC ADMIN: APPROVAL --}}
                                @if($leave->status == 'pending')
                                    <form action="{{ route('leaves.updateStatus', $leave->id) }}" method="POST" class="d-flex gap-2">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" name="status" value="approved" class="btn btn-success btn-sm" title="Setujui" onclick="return confirm('Setujui cuti ini?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm" title="Tolak" onclick="return confirm('Tolak cuti ini?')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small fst-italic">Selesai</span>
                                @endif

                            @else
                                {{-- LOGIC USER: BATALKAN --}}
                                @if($leave->status == 'pending')
                                    <form action="{{ route('leaves.destroy', $leave->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                                            <i class="bi bi-trash"></i> Batal
                                        </button>
                                    </form>
                                @else
                                    {{-- Jika sudah diapprove/reject, tampilkan icon status saja --}}
                                    @if($leave->status == 'approved')
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i></span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i></span>
                                    @endif
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada data cuti.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection