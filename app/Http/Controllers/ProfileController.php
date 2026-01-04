<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helpers\LogHelper;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 1. TAMPILKAN FORM EDIT
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // 2. UPDATE BIODATA & FOTO
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Array untuk menampung daftar perubahan
        $changes = [];

        // 1. CEK PERUBAHAN FOTO
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            
            // Catat perubahan foto
            $changes[] = "Mengganti Foto Profil";
        }

        // 2. CEK PERUBAHAN TEXT (Nama & Email)
        // Kita isi dulu datanya ke model (tapi belum di-save ke DB)
        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Cek apakah ada kolom yang "kotor" (berubah dari aslinya)?
        if ($user->isDirty(['name', 'email'])) {
            foreach ($user->getDirty() as $key => $newValue) {
                // Abaikan avatar karena sudah dicek manual di atas
                if ($key == 'avatar') continue; 

                $oldValue = $user->getOriginal($key); // Ambil data lama
                
                // Format kalimat log: "name dari 'Budi' menjadi 'Budi Santoso'"
                $changes[] = "$key dari '$oldValue' menjadi '$newValue'";
            }
        }

        // 3. SIMPAN KE DATABASE & CATAT LOG
        // Hanya simpan jika ada perubahan (foto atau text)
        if (count($changes) > 0) {
            $user->save();
            
            // Gabungkan array changes jadi satu kalimat string
            $logMessage = "Update Profil: " . implode(', ', $changes);
            LogHelper::record('UPDATE', $logMessage);

            return back()->with('success', 'Profil berhasil diperbarui.');
        }

        return back()->with('info', 'Tidak ada perubahan data.');
    }

    // 3. UPDATE PASSWORD
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed', // field konfirmasi: new_password_confirmation
        ]);

        $user = Auth::user();

        // Cek Password Lama Benar/Salah
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah!']);
        }

        // Update Password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        LogHelper::record('SECURITY', 'Mengubah password akun.');

        return back()->with('success', 'Password berhasil diubah!');
    }
}