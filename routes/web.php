<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// --- TAMBAHKAN INI ---
Route::resource('divisions', App\Http\Controllers\DivisionController::class);
Route::resource('users', App\Http\Controllers\UserController::class);
// --- ROUTE ABSENSI ---
Route::post('/attendance/checkin', [App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('attendance.checkIn');
Route::post('/attendance/checkout', [App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('attendance.checkOut');
Route::get('/attendance/history', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
Route::resource('payrolls', App\Http\Controllers\PayrollController::class);
Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity_logs.index');
// --- MANAJEMEN CUTI ---
// Hapus 'destroy' dari list except
Route::resource('leaves', App\Http\Controllers\LeaveController::class)->except(['show', 'edit', 'update']);
// Route khusus untuk Approval Admin
Route::put('/leaves/{leave}/status', [App\Http\Controllers\LeaveController::class, 'updateStatus'])->name('leaves.updateStatus');

// --- PROFIL USER ---
Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
// --- LAPORAN ---
Route::get('/reports/attendance', [App\Http\Controllers\ReportController::class, 'attendance'])->name('reports.attendance');

// --- PENGUMUMAN (Admin Only) ---
Route::resource('announcements', App\Http\Controllers\AnnouncementController::class)->except(['show', 'edit', 'update']);
// Route khusus untuk toggle status aktif/tidak
Route::put('/announcements/{id}/toggle', [App\Http\Controllers\AnnouncementController::class, 'toggle'])->name('announcements.toggle');
Route::get('/reports/payroll', [App\Http\Controllers\ReportController::class, 'payroll'])->name('reports.payroll');
// Route PDF
Route::get('/reports/attendance/pdf', [App\Http\Controllers\ReportController::class, 'downloadAttendancePdf'])->name('reports.attendance.pdf');
Route::get('/reports/payroll/pdf', [App\Http\Controllers\ReportController::class, 'downloadPayrollPdf'])->name('reports.payroll.pdf');
Route::get('/payrolls/{id}/pdf', [App\Http\Controllers\PayrollController::class, 'downloadSlip'])->name('payrolls.downloadSlip');




