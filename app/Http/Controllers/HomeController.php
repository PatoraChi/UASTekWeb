<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Division;
use App\Models\Attendance;
use App\Models\ActivityLog;
use App\Models\Announcement; // Pastikan Model Announcement dipanggil

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) 
    {
        $user = auth()->user();

        // Ambil Pengumuman Aktif (Untuk Semua Role)
        $announcements = Announcement::where('is_active', true)->latest()->get();

        // 1. Logika untuk SUPER ADMIN
        if ($user->role === 'super_admin') {
            // --- VARIABEL STATISTIK (Ini yang tadi hilang) ---
            $totalDivisions = Division::count();
            $totalUsers = User::count();
            $recentLogs = ActivityLog::with('user')->latest()->take(5)->get();

            // --- LOGIKA GRAFIK (FILTER TAHUNAN) ---
            $filterMonth = $request->input('month', date('n')); 
            $filterYear = $request->input('year', date('Y'));

            $labels = [];
            $dataGaji = [];
            $chartLabel = "";

            $divisions = Division::all();

            // Tentukan Judul Grafik
            if($filterMonth == 'all') {
                $chartLabel = "Total Gaji Tahun $filterYear";
            } else {
                $monthName = date('F', mktime(0, 0, 0, $filterMonth, 10));
                $chartLabel = "Total Gaji $monthName $filterYear";
            }

            // Loop Data Divisi untuk Grafik
            foreach ($divisions as $div) {
                $labels[] = $div->name;

                $query = \App\Models\Payroll::whereHas('user', function($q) use ($div) {
                            $q->where('division_id', $div->id);
                         })
                         ->where('year', $filterYear);

                if ($filterMonth != 'all') {
                    $monthName = date('F', mktime(0, 0, 0, $filterMonth, 10));
                    $query->where('month', $monthName);
                }

                $total = $query->sum('net_salary');
                $dataGaji[] = $total;
            }
            
            // Kirim Semua Variabel ke View Super Admin
            return view('dashboard.super_admin', compact(
                'totalDivisions', 
                'totalUsers', 
                'recentLogs', 
                'labels', 
                'dataGaji',
                'filterMonth', 
                'filterYear',
                'chartLabel',
                'announcements'
            ));
        } 
        
        // 2. Logika untuk ADMIN DIVISI
        elseif ($user->role === 'admin') {
            $totalEmployees = User::where('division_id', $user->division_id)->count();
            
            return view('dashboard.admin', compact('totalEmployees', 'announcements'));
        } 
        
        // 3. Logika untuk USER BIASA
        else {
            $todayAttendance = Attendance::where('user_id', $user->id)
                                ->whereDate('date', now())
                                ->first();

            return view('dashboard.user', compact('todayAttendance', 'announcements'));
        }
    }
}