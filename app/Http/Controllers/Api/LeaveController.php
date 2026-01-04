<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    // GET /api/leaves
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role == 'admin' || $user->role == 'super_admin') {
            // Admin: Lihat Semua (Bisa filter by status)
            $query = LeaveRequest::with('user');
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            $leaves = $query->orderBy('created_at', 'desc')->get();
        } else {
            // User: Lihat Punya Sendiri
            $leaves = LeaveRequest::where('user_id', $user->id)
                                  ->orderBy('created_at', 'desc')
                                  ->get();
        }

        return response()->json(['success' => true, 'data' => $leaves]);
    }

    // POST /api/leaves (Ajukan Cuti)
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required',
            'attachment' => 'nullable|image|max:2048'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        // Hitung selisih hari
        $start = \Carbon\Carbon::parse($request->start_date);
        $end = \Carbon\Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;
        $data['days'] = $days;

        // Cek Kuota (Sederhana)
        if (Auth::user()->leave_quota < $days) {
            return response()->json(['success' => false, 'message' => 'Sisa cuti tidak mencukupi'], 400);
        }

        // Upload Bukti
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('leaves', 'public');
        }

        $leave = LeaveRequest::create($data);

        return response()->json(['success' => true, 'message' => 'Pengajuan berhasil', 'data' => $leave], 201);
    }

    // POST /api/leaves/{id}/approve (Khusus Admin)
    public function approve(Request $request, $id)
    {
        if (Auth::user()->role == 'user') return response()->json(['message' => 'Unauthorized'], 403);

        $leave = LeaveRequest::findOrFail($id);
        
        $request->validate(['status' => 'required|in:approved,rejected']);

        $leave->status = $request->status;
        $leave->admin_note = $request->admin_note; // Alasan penolakan/acc
        $leave->save();

        // Potong Kuota jika Approved
        if ($request->status == 'approved') {
            $user = $leave->user;
            $user->leave_quota -= $leave->days;
            $user->save();
        }

        return response()->json(['success' => true, 'message' => 'Status cuti diperbarui']);
    }
}