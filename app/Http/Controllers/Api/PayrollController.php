<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payroll;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    // GET /api/payrolls
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil gaji milik user sendiri
        $payrolls = Payroll::where('user_id', $user->id)
                           ->orderBy('year', 'desc')
                           ->orderBy('id', 'desc') // Asumsi ID besar = bulan baru
                           ->get();

        return response()->json([
            'success' => true,
            'data' => $payrolls
        ]);
    }

    // GET /api/payrolls/{id} (Detail)
    public function show($id)
    {
        $payroll = Payroll::where('user_id', Auth::id())->find($id);

        if (!$payroll) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $payroll
        ]);
    }
}