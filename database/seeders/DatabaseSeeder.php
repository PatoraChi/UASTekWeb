<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Division;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Announcement;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // KITA PAKAI MEMORY LIMIT BESAR KARENA GENERATE BANYAK DATA
        ini_set('memory_limit', '512M');

        $this->command->info('Memulai seeding data HRIS (2025-2026)...');

        // ---------------------------------------------------
        // 1. BUAT DIVISI
        // ---------------------------------------------------
        $divIT = Division::create(['name' => 'Information Technology']);
        $divHR = Division::create(['name' => 'Human Resources']);
        $divFin = Division::create(['name' => 'Finance & Accounting']);
        $divOps = Division::create(['name' => 'Operations']);

        // ---------------------------------------------------
        // 2. BUAT PENGUMUMAN
        // ---------------------------------------------------
        Announcement::create([
            'title' => 'Selamat Datang di HRIS Baru',
            'content' => 'Sistem ini mulai digunakan efektif per Januari 2025.',
            'type' => 'info',
            'created_at' => '2025-01-01 08:00:00'
        ]);
        Announcement::create([
            'title' => 'Libur Lebaran 2025',
            'content' => 'Cuti bersama ditetapkan tanggal sekian...',
            'type' => 'success',
            'created_at' => '2025-03-01 08:00:00'
        ]);
        Announcement::create([
            'title' => 'Maintenance Server',
            'content' => 'Server akan down pada hari Sabtu malam.',
            'type' => 'warning',
            'is_active' => true
        ]);

        // ---------------------------------------------------
        // 3. BUAT USER (PEGAWAI)
        // ---------------------------------------------------
        
        // A. Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@company.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'position' => 'CTO',
            'base_salary' => 20000000,
        ]);

        // B. Admin Divisi
        $adminIT = User::create([
            'name' => 'Admin IT',
            'email' => 'admin.it@company.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'division_id' => $divIT->id,
            'position' => 'IT Manager',
            'base_salary' => 12000000,
            'position_allowance' => 2000000,
            'meal_allowance' => 50000,
            'transport_allowance' => 25000,
        ]);

        $adminHR = User::create([
            'name' => 'Admin HRD',
            'email' => 'admin.hr@company.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'division_id' => $divHR->id,
            'position' => 'HR Manager',
            'base_salary' => 11000000,
            'position_allowance' => 1500000,
            'meal_allowance' => 50000,
            'transport_allowance' => 25000,
        ]);

        // C. Pegawai Biasa (Staff) - Kita buat array biar mudah di-loop
        $staffs = [];

        // Buat 5 Staff IT
        for ($i = 1; $i <= 5; $i++) {
            $staffs[] = User::create([
                'name' => "Staff IT $i",
                'email' => "it$i@company.com",
                'password' => Hash::make('password'),
                'role' => 'user',
                'division_id' => $divIT->id,
                'position' => 'Programmer',
                'base_salary' => 7000000 + ($i * 100000), // Gaji beda dikit
                'meal_allowance' => 35000,
                'transport_allowance' => 15000,
            ]);
        }

        // Buat 3 Staff HR
        for ($i = 1; $i <= 3; $i++) {
            $staffs[] = User::create([
                'name' => "Staff HR $i",
                'email' => "hr$i@company.com",
                'password' => Hash::make('password'),
                'role' => 'user',
                'division_id' => $divHR->id,
                'position' => 'Recruiter',
                'base_salary' => 6000000,
                'meal_allowance' => 35000,
                'transport_allowance' => 15000,
            ]);
        }

         // Buat 2 Staff Finance
         for ($i = 1; $i <= 2; $i++) {
            $staffs[] = User::create([
                'name' => "Staff Finance $i",
                'email' => "fin$i@company.com",
                'password' => Hash::make('password'),
                'role' => 'user',
                'division_id' => $divFin->id,
                'position' => 'Accountant',
                'base_salary' => 6500000,
                'meal_allowance' => 35000,
                'transport_allowance' => 15000,
            ]);
        }

        // Gabungkan semua user yang perlu diabsenkan (Admin Divisi + Staff)
        $allEmployees = array_merge([$adminIT, $adminHR], $staffs);

        // ---------------------------------------------------
        // 4. GENERATE ABSENSI & CUTI (1 Jan 2025 - 4 Jan 2026)
        // ---------------------------------------------------
        $startDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::create(2026, 1, 4); // Sampai hari ini
        $period = CarbonPeriod::create($startDate, $endDate);

        $this->command->info('Generating Absensi & Cuti...');

        foreach ($period as $date) {
            // Skip Sabtu & Minggu
            if ($date->isWeekend()) continue;

            foreach ($allEmployees as $emp) {
                // Random Logic: 
                // 85% Hadir, 5% Telat, 2% Izin/Sakit, 3% Alpha, 5% Cuti Tahunan
                $rand = rand(1, 100);

                // --- A. DATA CUTI (SIMULASI) ---
                if ($rand > 95) { // 5% Cuti
                    // Cek biar gak double cuti
                    $exists = LeaveRequest::where('user_id', $emp->id)->whereDate('start_date', $date)->exists();
                    if (!$exists) {
                        LeaveRequest::create([
                            'user_id' => $emp->id,
                            'type' => 'annual',
                            'days' => 1,
                            'start_date' => $date->format('Y-m-d'),
                            'end_date' => $date->format('Y-m-d'),
                            'reason' => 'Cuti simulasi seeder',
                            'status' => 'approved',
                            'created_at' => $date->copy()->subDays(7) // Diajukan seminggu sebelumnya
                        ]);
                    }
                    continue; // Kalau cuti, jangan buat absen
                }

                // --- B. DATA ABSENSI ---
                
                // Variabel Default
                $checkIn = '08:00:00';
                $checkOut = '17:00:00';
                $status = 'present';
                $lateMinutes = 0;

                if ($rand <= 85) { 
                    // 85% HADIR TEPAT WAKTU
                    // Random jam masuk 07:30 - 08:00
                    $checkIn = $date->copy()->setTime(7, rand(30, 59), 0)->toTimeString();
                    
                    // Random Lembur (20% kemungkinan lembur)
                    if (rand(1, 100) <= 20) {
                        // Pulang jam 18:00 - 20:00
                        $checkOut = $date->copy()->setTime(rand(18, 20), rand(0, 59), 0)->toTimeString();
                    } else {
                        // Pulang normal 17:00 - 17:30
                        $checkOut = $date->copy()->setTime(17, rand(0, 30), 0)->toTimeString();
                    }

                } elseif ($rand <= 90) { 
                    // 5% TELAT
                    $menitTelat = rand(5, 120); // Telat 5 menit - 2 jam
                    $lateMinutes = $menitTelat;
                    $status = 'late';
                    $checkIn = $date->copy()->setTime(8, 0, 0)->addMinutes($menitTelat)->toTimeString();
                    $checkOut = $date->copy()->setTime(17, rand(0, 30), 0)->toTimeString();

                } elseif ($rand <= 92) {
                    // 2% SAKIT (Ada data absen tapi status sick)
                    Attendance::create([
                        'user_id' => $emp->id,
                        'date' => $date->format('Y-m-d'),
                        'status' => 'sick',
                        'note' => 'Sakit Flu',
                        'is_approved' => true
                    ]);
                    continue; // Lanjut ke user berikutnya

                } else {
                    // 3% ALPHA (Tidak ada record absen sama sekali)
                    continue; 
                }

                // Simpan Absen Hadir/Telat
                Attendance::create([
                    'user_id' => $emp->id,
                    'date' => $date->format('Y-m-d'),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $status,
                    'late_minutes' => $lateMinutes,
                ]);
            }
        }

        // ---------------------------------------------------
        // 5. GENERATE PAYROLL (Jan 2025 - Des 2025)
        // ---------------------------------------------------
        // Kita tidak generate Jan 2026 agar user bisa coba tombol "Generate" sendiri.
        
        $this->command->info('Generating Payroll 2025...');
        
        for ($m = 1; $m <= 12; $m++) {
            $year = 2025;
            $monthName = date('F', mktime(0, 0, 0, $m, 10));

            foreach ($allEmployees as $emp) {
                // Hitung Data dari Database Absensi yang baru dibuat di atas
                // Agar data Laporan Konsisten dengan Grafik
                
                $attendances = Attendance::where('user_id', $emp->id)
                                         ->whereMonth('date', $m)
                                         ->whereYear('date', $year)
                                         ->get();

                $presentDays = $attendances->whereIn('status', ['present', 'late'])->count();
                $totalLateMinutes = $attendances->sum('late_minutes');
                
                // Hitung Lembur (Simple Logic untuk Seeder)
                $uangLembur = 0;
                foreach($attendances as $att) {
                    if($att->check_out) {
                        $out = Carbon::parse($att->check_out);
                        if($out->hour >= 17) {
                            $jam = $out->hour - 17;
                            if($jam > 0) $uangLembur += ($jam * 20000);
                        }
                    }
                }

                // Hitung Tunjangan
                $allowances = ($emp->position_allowance ?? 0) + 
                              (($emp->meal_allowance ?? 0) * $presentDays) + 
                              (($emp->transport_allowance ?? 0) * $presentDays);

                // Hitung Potongan
                $deductions = $totalLateMinutes * 1000;

                // Gaji Bersih
                $netSalary = ($emp->base_salary + $allowances + $uangLembur) - $deductions;

                Payroll::create([
                    'user_id' => $emp->id,
                    'month' => $monthName,
                    'year' => $year,
                    'basic_salary' => $emp->base_salary,
                    'allowances' => $allowances,
                    'overtime_pay' => $uangLembur,
                    'deductions' => $deductions,
                    'net_salary' => $netSalary,
                    'created_at' => Carbon::create($year, $m, 28) // Gaji dibuat tiap tgl 28
                ]);
            }
        }

        $this->command->info('SELESAI! Database berhasil diisi data dummy 1 tahun.');
    }
}