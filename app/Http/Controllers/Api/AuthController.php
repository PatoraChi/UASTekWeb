<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // POST /api/login
    public function login(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Cek Kredensial
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah.',
            ], 401);
        }

        // 3. Ambil User & Buat Token
        $user = User::where('email', $request->email)->first();
        
        // Hapus token lama biar gak numpuk (Opsional)
        $user->tokens()->delete();
        
        // Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Return Response JSON
        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'data' => [
                'user' => $user,
                'token' => $token, // INI YANG PENTING
            ]
        ], 200);
    }

    // POST /api/logout
    public function logout(Request $request)
    {
        // Hapus token yang sedang dipakai
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout Berhasil'
        ]);
    }
    
    // GET /api/me (Cek User yg sedang login)
    public function me(Request $request) 
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }
}