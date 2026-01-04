<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // POST /api/attendance/check-in
    public function checkIn(Request $request)
    {
        $user = $request->user(); // Ambil user dari Token
        $today = Carbon::today();

        // 1. VALIDASI: Cek apakah sudah absen hari ini? (Sama seperti Web)
        $existingAttendance = Attendance::where('user_id', $user->id)
                                        ->whereDate('date', $today)
                                        ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen masuk hari ini!',
            ], 400);
        }

        // 2. LOGIC JAM MASUK (Sama seperti Web)
        $currentTime = Carbon::now('Asia/Makassar'); 
        $workStartTime = Carbon::createFromTime(8, 0, 0, 'Asia/Makassar'); 

        $status = 'present'; 
        $lateMinutes = 0;

        if ($currentTime->gt($workStartTime)) {
            $status = 'late';
            $lateMinutes = $currentTime->diffInMinutes($workStartTime); 
        }

        // 3. SIMPAN DATA
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => $currentTime->toTimeString(),
            'status' => $status,
            'late_minutes' => $lateMinutes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Absen Masuk',
            'data' => $attendance
        ], 201);
    }

    // POST /api/attendance/check-out
    public function checkOut(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        // Cari data absen hari ini
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('date', $today)
                                ->first();

        // VALIDASI: Belum absen masuk tapi mau pulang
        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum absen masuk hari ini!',
            ], 400);
        }

        // VALIDASI: Sudah absen pulang sebelumnya
        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen pulang sebelumnya!',
            ], 400);
        }

        // Update jam pulang
        $attendance->update([
            'check_out' => Carbon::now('Asia/Makassar')->toTimeString()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Absen Pulang',
            'data' => $attendance
        ]);
    }
}