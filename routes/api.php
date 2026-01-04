<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\AnnouncementController;

// PUBLIC
Route::post('/login', [AuthController::class, 'login']);

// PROTECTED (BUTUH TOKEN)
Route::middleware('auth:sanctum')->group(function () {
    
    // 1. Auth & Profile
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Profile Update (POST karena upload file kadang bermasalah dengan PUT di mobile)
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);

    // 2. Absensi
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    
    // 3. Laporan & Pengumuman
    Route::get('/reports/summary', [ReportController::class, 'summary']);
    Route::get('/announcements', [AnnouncementController::class, 'index']);

    // 4. Cuti (Leaves)
    Route::get('/leaves', [LeaveController::class, 'index']);
    Route::post('/leaves', [LeaveController::class, 'store']);
    Route::post('/leaves/{id}/action', [LeaveController::class, 'approve']); // Khusus Admin

    // 5. Gaji (Payroll)
    Route::get('/payrolls', [PayrollController::class, 'index']);
    Route::get('/payrolls/{id}', [PayrollController::class, 'show']);

});