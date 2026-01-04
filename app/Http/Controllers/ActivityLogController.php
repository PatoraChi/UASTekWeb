<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Division; // <--- PENTING: Tambahkan Model Divisi

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'super_admin') {
                abort(403, 'Akses Ditolak');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // 1. Siapkan Query Dasar
        $query = ActivityLog::with('user');

        // 2. Cek apakah ada Filter Divisi?
        if ($request->filled('division_id')) {
            // "Carikan log yang user-nya punya division_id X"
            $query->whereHas('user', function($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        // 3. Eksekusi Query (Pagination)
        $logs = $query->latest()->paginate(20);

        // 4. Ambil Data Divisi untuk Dropdown Filter
        $divisions = Division::all();
        
        return view('activity_logs.index', compact('logs', 'divisions'));
    }
}