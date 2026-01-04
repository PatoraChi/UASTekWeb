<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // GET /api/profile
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()->load('division')
        ]);
    }

    // POST /api/profile
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|min:6',
            'avatar' => 'nullable|image|max:2048' // Validasi gambar
        ]);

        // Update Data Dasar
        $user->name = $request->name;
        $user->email = $request->email;

        // Update Password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Upload Avatar (Multipart/Form-Data)
        if ($request->hasFile('avatar')) {
            // Hapus foto lama jika ada
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            
            // Simpan baru
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user' => $user
        ]);
    }
}