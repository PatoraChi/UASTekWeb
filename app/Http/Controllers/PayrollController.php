<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\LogHelper;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    // Hanya Admin dan Super Admin yang boleh akses
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 1. TAMPILKAN LIST GAJI (History)
    public function index()
    {
        $user = Auth::user();
        
        if($user->role == 'user'){
            // User biasa: Cuma lihat gajinya sendiri
            $payrolls = Payroll::where('user_id', $user->id)
                               ->orderBy('year', 'desc')
                               ->orderBy('month', 'desc')
                               ->get();
        } else {
            // Admin & Super Admin
            $query = Payroll::with('user');

            // --- TAMBAHAN: FILTER KHUSUS ADMIN DIVISI ---
            if ($user->role == 'admin') {
                $query->whereHas('user', function($q) use ($user) {
                    $q->where('division_id', $user->division_id);
                });
            }
            // -------------------------------------------

            $payrolls = $query->orderBy('created_at', 'desc')->get();
        }

        return view('payrolls.index', compact('payrolls'));
    }

    // 2. FORM GENERATE GAJI (Hanya Admin)
    public function create()
    {
        if(Auth::user()->role == 'user') abort(403);
        return view('payrolls.create');
    }

    // 3. PROSES HITUNG GAJI (CORE LOGIC)
    public function store(Request $request)
    {
        if(Auth::user()->role == 'user') abort(403);

        $request->validate([
            'month' => 'required|numeric|min:1|max:12',
            'year' => 'required|numeric|min:2020',
        ]);

        $month = $request->month;
        $year = $request->year;
        $adminDivisi = Auth::user()->division_id;

        // Ambil pegawai
        $query = User::where('role', '!=', 'super_admin');
        if(Auth::user()->role == 'admin') {
            $query->where('division_id', $adminDivisi);
        }
        $employees = $query->get();
        
        $count = 0;

        // --- SETTING VARIABEL GAJI ---
        $dendaTelatPerMenit = 1000;
        $dendaAlphaPerHari = 100000; 
        $bayaranLemburPerJam = 20000;
        $jamPulangKantor = 17; // Jam 17:00 (5 Sore)

        foreach($employees as $emp) {
            // Cek duplikasi slip gaji
            $monthName = date('F', mktime(0, 0, 0, $month, 10));
            $exists = Payroll::where('user_id', $emp->id)
                             ->where('month', $monthName)
                             ->where('year', $year)
                             ->exists();
            if($exists) continue;

            // -----------------------------------------
            // 1. HITUNG HARI KERJA & ALPHA
            // -----------------------------------------
            // Jumlah Hari Hadir
            $presentDays = Attendance::where('user_id', $emp->id)
                                ->whereMonth('date', $month)
                                ->whereYear('date', $year)
                                ->count();
            
            // Jumlah Hari Cuti (Approved)
            $leaveDays = \App\Models\LeaveRequest::where('user_id', $emp->id)
                                ->where('status', 'approved')
                                ->whereMonth('start_date', $month)
                                ->whereYear('start_date', $year)
                                ->sum('days');

            // Hitung Alpha (Asumsi 22 hari kerja efektif)
            $hariKerjaEfektif = 22; 
            $alphaDays = max(0, $hariKerjaEfektif - $presentDays - $leaveDays);


            // -----------------------------------------
            // 2. HITUNG TUNJANGAN (Hybrid)
            // -----------------------------------------
            $tjJabatan = $emp->position_allowance ?? 0;
            $uangMakan = ($emp->meal_allowance ?? 0) * $presentDays;
            $uangTransport = ($emp->transport_allowance ?? 0) * $presentDays;
            
            $totalAllowances = $tjJabatan + $uangMakan + $uangTransport;


            // -----------------------------------------
            // 3. HITUNG LEMBUR (Overtime)
            // -----------------------------------------
            $uangLembur = 0; // Inisialisasi variabel agar tidak error!

            $attendances = Attendance::where('user_id', $emp->id)
                                ->whereMonth('date', $month)
                                ->whereYear('date', $year)
                                ->whereNotNull('check_out')
                                ->get();
            
            foreach($attendances as $att) {
                $checkOutTime = Carbon::parse($att->check_out);
                // Jika pulang lewat jam 17:00
                if ($checkOutTime->hour >= $jamPulangKantor) {
                    $jamLembur = $checkOutTime->hour - $jamPulangKantor;
                    if ($jamLembur > 0) {
                        $uangLembur += ($jamLembur * $bayaranLemburPerJam);
                    }
                }
            }


            // -----------------------------------------
            // 4. HITUNG POTONGAN (Denda)
            // -----------------------------------------
            $totalLateMinutes = Attendance::where('user_id', $emp->id)
                                ->whereMonth('date', $month)
                                ->whereYear('date', $year)
                                ->sum('late_minutes');

            $potonganTelat = $totalLateMinutes * $dendaTelatPerMenit;
            $potonganAlpha = $alphaDays * $dendaAlphaPerHari;
            
            $totalDeduction = $potonganTelat + $potonganAlpha;


            // -----------------------------------------
            // 5. HITUNG GAJI BERSIH & SIMPAN
            // -----------------------------------------
            $gajiPokok = $emp->base_salary;
            $gajiBersih = ($gajiPokok + $totalAllowances + $uangLembur) - $totalDeduction;

            Payroll::create([
                'user_id' => $emp->id,
                'month' => $monthName,
                'year' => $year,
                'basic_salary' => $gajiPokok,
                'allowances' => $totalAllowances,
                'overtime_pay' => $uangLembur, // Variabel ini sekarang sudah aman
                'deductions' => $totalDeduction,
                'net_salary' => max(0, $gajiBersih)
            ]);

            $count++;
        }
        
        LogHelper::record('PAYROLL', "Generate gaji bulan $monthName $year untuk $count pegawai.");
        return redirect()->route('payrolls.index')->with('success', "Berhasil generate gaji untuk $count pegawai.");
    }
    
    // 4. DETAIL SLIP GAJI (Opsional untuk Print PDF nanti)
    public function show(Payroll $payroll)
    {
        // Pastikan user hanya bisa lihat punya sendiri (kecuali admin)
        if(Auth::user()->role == 'user' && $payroll->user_id != Auth::id()){
            abort(403);
        }
        
        return view('payrolls.show', compact('payroll'));
    }
    public function downloadSlip($id)
    {
        $payroll = Payroll::with('user.division')->findOrFail($id);

        // Cek Hak Akses (User cuma boleh punya sendiri, Admin cuma boleh divisinya)
        if(Auth::user()->role == 'user' && $payroll->user_id != Auth::user()->id) abort(403);
        
        $pdf = Pdf::loadView('pdf.slip_gaji', compact('payroll'));
        
        // Kertas A5 Landscape (Opsional, biar kayak slip bank)
        $pdf->setPaper('a5', 'landscape');

        return $pdf->stream('Slip_Gaji_'.$payroll->user->name.'.pdf');
    }
}