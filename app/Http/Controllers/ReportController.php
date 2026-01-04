<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Division;
use App\Models\Attendance;
use App\Models\Payroll;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // --- HALAMAN LAPORAN ABSENSI (WEB) ---
    public function attendance(Request $request)
    {
        if (Auth::user()->role == 'user') abort(403);

        $divisionId = $request->input('division_id');
        if (Auth::user()->role == 'admin') {
            $divisionId = Auth::user()->division_id;
        }

        $divisions = Division::all();
        $reportData = [];      
        $attendanceDetails = []; 
        
        // 1. MODE DETAIL (HARIAN)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $mode = 'detail';
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $query = Attendance::with('user.division')
                        ->whereBetween('date', [$startDate, $endDate]);

            if ($divisionId) {
                $query->whereHas('user', function($q) use ($divisionId) {
                    $q->where('division_id', $divisionId);
                });
            }
            // Sembunyikan Super Admin
            $query->whereHas('user', function($q) {
                $q->where('role', '!=', 'super_admin');
            });

            $attendanceDetails = $query->orderBy('date', 'desc')->orderBy('user_id')->get();
            $month = date('n'); 
            $year = date('Y');

        } 
        // 2. MODE REKAP (BULANAN)
        else {
            $mode = 'summary';
            $month = $request->input('month', date('n'));
            $year = $request->input('year', date('Y'));

            $usersQuery = User::where('role', '!=', 'super_admin');
            if ($divisionId) {
                $usersQuery->where('division_id', $divisionId);
            }
            $employees = $usersQuery->get();

            foreach ($employees as $emp) {
                $attendances = Attendance::where('user_id', $emp->id)
                                         ->whereMonth('date', $month)
                                         ->whereYear('date', $year)
                                         ->get();

                $reportData[] = [
                    'name' => $emp->name,
                    'division' => $emp->division->name ?? '-',
                    'total_present' => $attendances->whereIn('status', ['present', 'late'])->count(),
                    'total_late' => $attendances->sum('late_minutes'),
                    'total_sick' => $attendances->where('status', 'sick')->count(),
                    'total_leave' => $attendances->where('status', 'leave')->count(),
                    'total_alpha' => $attendances->where('status', 'alpha')->count()
                ];
            }
        }

        return view('reports.attendance', compact(
            'mode', 'reportData', 'attendanceDetails', 
            'divisions', 'month', 'year', 'divisionId'
        ));
    }

    // --- HALAMAN LAPORAN GAJI (WEB) ---
    public function payroll(Request $request)
    {
        if (Auth::user()->role == 'user') abort(403);
        
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        
        // Konversi angka bulan ke nama bulan (1 -> January)
        $monthName = date('F', mktime(0, 0, 0, $month, 10));

        $divisionId = $request->input('division_id');
        if (Auth::user()->role == 'admin') {
            $divisionId = Auth::user()->division_id;
        }

        $query = Payroll::with('user')
                    ->where('month', $monthName)
                    ->where('year', $year);

        if ($divisionId) {
            $query->whereHas('user', function($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        $payrolls = $query->get();
        $divisions = Division::all();
        $totalExpense = $payrolls->sum('net_salary');

        return view('reports.payroll', compact(
            'payrolls', 'divisions', 'month', 'year', 'divisionId', 'totalExpense'
        ));
    }

    // --- DOWNLOAD PDF ABSENSI ---
    public function downloadAttendancePdf(Request $request) 
    {
        $query = Attendance::with('user.division');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
            $period = \Carbon\Carbon::parse($request->start_date)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($request->end_date)->format('d M Y');
        } else {
            $query->whereMonth('date', date('m'))->whereYear('date', date('Y'));
            $period = date('F Y');
        }

        if (Auth::user()->role == 'admin') {
            $query->whereHas('user', function($q) {
                $q->where('division_id', Auth::user()->division_id);
            });
        } elseif ($request->filled('division_id')) {
             $query->whereHas('user', function($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        // Sembunyikan Super Admin dari laporan
        $query->whereHas('user', function($q) {
            $q->where('role', '!=', 'super_admin');
        });

        $data = $query->orderBy('date', 'desc')->get();
        
        $pdf = Pdf::loadView('pdf.attendance', compact('data', 'period'));
        return $pdf->stream('Laporan_Absensi.pdf');
    }

    // --- DOWNLOAD PDF GAJI ---
    public function downloadPayrollPdf(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        
        $monthName = date('F', mktime(0, 0, 0, $month, 10));

        // DEFINISI QUERY
        $query = Payroll::with('user')
                    ->where('month', $monthName)
                    ->where('year', $year);

        // Filter Divisi
        if (Auth::user()->role == 'admin') {
            $query->whereHas('user', function($q) {
                $q->where('division_id', Auth::user()->division_id);
            });
        } elseif ($request->filled('division_id')) {
             $query->whereHas('user', function($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        $payrolls = $query->get();
        $totalExpense = $payrolls->sum('net_salary');

        // PERBAIKAN: Masukkan semua data ke dalam array di sini
        // Kita kirim 'month' => $monthName agar View tidak bingung
        $data = [
            'payrolls' => $payrolls,
            'month' => $monthName, // <--- INI KUNCINYA (Kirim sebagai 'month')
            'year' => $year,
            'totalExpense' => $totalExpense
        ];

        $pdf = Pdf::loadView('pdf.payroll_report', $data);

        return $pdf->stream('Laporan_Gaji_'.$monthName.'_'.$year.'.pdf');
    }
}