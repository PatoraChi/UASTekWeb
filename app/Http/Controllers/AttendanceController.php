<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Division;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class AttendanceController extends Controller
{
    // 1. Logika Absen Masuk (Check In)
    public function checkIn()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Cek apakah hari ini sudah absen?
        $existingAttendance = Attendance::where('user_id', $user->id)
                                        ->whereDate('date', $today)
                                        ->first();

        if ($existingAttendance) {
            return back()->with('error', 'Anda sudah melakukan absen masuk hari ini!');
        }

        // Tentukan Jam Masuk Kantor (Misal 08:00 WITA)
        // Catatan: Server biasanya UTC, jadi kita pakai zona waktu Asia/Makassar (WITA)
        $currentTime = Carbon::now('Asia/Makassar'); 
        $workStartTime = Carbon::createFromTime(8, 0, 0, 'Asia/Makassar'); 

        // Default Status
        $status = 'present'; 
        $lateMinutes = 0;

        // Logika Telat
        // Logika Telat
        if ($currentTime->gt($workStartTime)) {
            $status = 'late';
            // Tambahkan parameter 'true' di akhir agar hasilnya selalu positif (absolute)
            $lateMinutes = $currentTime->diffInMinutes($workStartTime, true); 
        }

        // Simpan ke Database
        Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => $currentTime->toTimeString(),
            'status' => $status,
            'late_minutes' => $lateMinutes,
        ]);
        LogHelper::record('ATTENDANCE', 'Melakukan Absen Masuk.');
        return back()->with('success', 'Berhasil Absen Masuk! Semangat Bekerja.');
    }

    // 2. Logika Absen Pulang (Check Out)
    public function checkOut()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Cari data absen hari ini
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('date', $today)
                                ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum absen masuk hari ini!');
        }

        // Update jam pulang
        $attendance->update([
            'check_out' => Carbon::now('Asia/Makassar')->toTimeString()
        ]);
        LogHelper::record('ATTENDANCE', 'Melakukan Absen Pulang.');
        return back()->with('success', 'Berhasil Absen Pulang. Hati-hati di jalan!');
    }
    // HALAMAN MONITORING UNTUK ADMIN
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Siapkan Query Dasar
        $query = Attendance::with('user');

        // 1. FILTER HAK AKSES (Agar user tidak melihat data orang lain)
        if ($user->role === 'user') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'admin') {
            $query->whereHas('user', function($q) use ($user) {
                $q->where('division_id', $user->division_id);
            });
        } elseif ($user->role === 'super_admin' && $request->filled('division_id')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        // 2. FITUR BARU: FILTER TANGGAL
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        // Ambil data (urutkan dari yang terbaru)
        $attendances = $query->orderBy('date', 'desc')->get();
        $divisions = Division::all();

        return view('attendance.index', compact('attendances', 'divisions'));
    }
}