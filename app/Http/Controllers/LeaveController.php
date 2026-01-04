<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Helpers\LogHelper; // Jangan lupa log activity!
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 1. HALAMAN UTAMA (LIST CUTI)
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'user') {
            // User cuma lihat punya sendiri
            $leaves = LeaveRequest::where('user_id', $user->id)
                                  ->orderBy('created_at', 'desc')
                                  ->get();
        } else {
            // Admin lihat pengajuan anak buahnya (satu divisi)
            // Super Admin lihat semua
            $query = LeaveRequest::with('user');
            
            if ($user->role == 'admin') {
                $query->whereHas('user', function($q) use ($user) {
                    $q->where('division_id', $user->division_id);
                });
            }
            
            $leaves = $query->orderBy('status', 'asc') // Yang pending di atas
                            ->orderBy('created_at', 'desc')
                            ->get();
        }

        return view('leaves.index', compact('leaves'));
    }

    // 2. FORM PENGAJUAN (HANYA USER)
    public function create()
    {
        return view('leaves.create');
    }

    // 3. SIMPAN PENGAJUAN
    public function store(Request $request)
    {
        // 1. VALIDASI INPUT
        $request->validate([
            'type' => 'required|in:annual,sick,emergency',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
            // Jika sakit, file wajib ada (mimes: jpg, png, pdf, max 2MB)
            'attachment' => 'nullable|required_if:type,sick|mimes:jpg,jpeg,png,pdf|max:2048', 
        ], [
            // Custom Error Message bahasa Indonesia
            'attachment.required_if' => 'Untuk izin sakit, wajib melampirkan Surat Dokter.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $user = Auth::user();
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $totalDays = $start->diffInDays($end) + 1;

        // 2. CEK TABRAKAN TANGGAL (OVERLAP CHECK) - LOGIC PENTING!
        // Cari cuti user ini yang statusnya BUKAN rejected
        // Dan tanggalnya beririsan dengan tanggal yang baru diajukan
        $overlap = LeaveRequest::where('user_id', $user->id)
                    ->where('status', '!=', 'rejected') 
                    ->where(function ($query) use ($start, $end) {
                        $query->whereBetween('start_date', [$start, $end])
                              ->orWhereBetween('end_date', [$start, $end])
                              ->orWhere(function ($q) use ($start, $end) {
                                  $q->where('start_date', '<=', $start)
                                    ->where('end_date', '>=', $end);
                              });
                    })
                    ->exists();

        if ($overlap) {
            return back()->with('error', 'Gagal! Anda sudah memiliki pengajuan cuti pada tanggal tersebut.');
        }

        // 3. CEK KUOTA (Khusus Tahunan)
        if ($request->type == 'annual') {
            if ($user->leave_quota < $totalDays) {
                return back()->with('error', "Gagal! Sisa cuti Anda hanya {$user->leave_quota} hari, tapi Anda meminta {$totalDays} hari.");
            }
        }

        // 4. PROSES UPLOAD FILE (Jika ada)
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            // Simpan ke folder: storage/app/public/attachments
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        // 5. SIMPAN KE DATABASE
        LeaveRequest::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'days' => $totalDays,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'attachment' => $attachmentPath, // Simpan path file
            'status' => 'pending'
        ]);

        LogHelper::record('CREATE', "Mengajukan cuti ({$request->type}) selama {$totalDays} hari.");

        return redirect()->route('leaves.index')->with('success', 'Permohonan berhasil dikirim.');
    }

    // 4. PROSES APPROVAL (HANYA ADMIN)
    public function updateStatus(Request $request, LeaveRequest $leave)
    {
        if (Auth::user()->role == 'user') abort(403);

        $request->validate(['status' => 'required|in:approved,rejected']);

        // Jika DISETUJUI dan tipe cuti TAHUNAN -> POTONG KUOTA USER
        if ($request->status == 'approved' && $leave->type == 'annual' && $leave->status != 'approved') {
            $user = $leave->user;
            
            // Cek lagi (takutnya kuota berubah saat nunggu approval)
            if ($user->leave_quota < $leave->days) {
                return back()->with('error', 'Gagal approve! Kuota user sudah habis.');
            }

            $user->decrement('leave_quota', $leave->days); // Kurangi Kuota
        }

        $leave->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note
        ]);

        LogHelper::record('APPROVE', "Memproses cuti {$leave->user->name}: {$request->status}");

        return back()->with('success', 'Status cuti diperbarui.');
    }
    // 5. BATALKAN CUTI (USER)
    // GANTI baris: public function destroy(LeaveRequest $leave)
    // MENJADI:
    public function destroy($id) 
    {
        // 1. Cari data secara MANUAL agar tidak error (null)
        $leave = LeaveRequest::find($id);

        // Jika data tidak ditemukan (misal sudah terhapus)
        if (!$leave) {
            return back()->with('error', 'Data pengajuan tidak ditemukan.');
        }

        // 2. Cek apakah ini milik user yang sedang login?
        if ($leave->user_id != Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus data ini.');
        }

        // 3. Cek apakah status masih Pending?
        if ($leave->status != 'pending') {
            return back()->with('error', 'Gagal! Hanya pengajuan yang masih "Menunggu" yang bisa dibatalkan.');
        }

        // 4. Hapus File Lampiran (Jika ada)
        if ($leave->attachment) {
            Storage::disk('public')->delete($leave->attachment);
        }

        // 5. Hapus Data
        $leave->delete();

        // 6. Catat Log
        LogHelper::record('DELETE', "Membatalkan pengajuan cuti {$leave->type}.");

        return back()->with('success', 'Permohonan cuti berhasil dibatalkan.');
    }
}