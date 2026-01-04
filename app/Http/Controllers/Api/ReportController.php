<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class ReportController extends Controller
{
    // GET /api/reports/summary
    public function summary(Request $request)
    {
        $user = $request->user();
        
        // Default bulan ini jika tidak ada filter
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        // Ambil data absensi bulan tersebut
        $attendances = Attendance::where('user_id', $user->id)
                                 ->whereMonth('date', $month)
                                 ->whereYear('date', $year)
                                 ->get();

        // HITUNG REKAPITULASI (Logic Kalkulasi di Backend)
        $totalHadir = $attendances->whereIn('status', ['present', 'late'])->count();
        $totalTelat = $attendances->where('status', 'late')->count();
        $totalMenitTelat = $attendances->sum('late_minutes');
        $totalSakit = $attendances->where('status', 'sick')->count();
        $totalCuti = $attendances->where('status', 'leave')->count();
        $totalAlpha = $attendances->where('status', 'alpha')->count();

        // Return Data Matang (Bukan Raw Data)
        return response()->json([
            'success' => true,
            'period' => [
                'month' => $month,
                'year' => $year
            ],
            'summary' => [
                'total_kehadiran_hari' => $totalHadir,
                'total_terlambat_kali' => $totalTelat,
                'total_terlambat_menit' => $totalMenitTelat,
                'total_sakit' => $totalSakit,
                'total_cuti' => $totalCuti,
                'total_alpha' => $totalAlpha,
            ]
        ]);
    }
}