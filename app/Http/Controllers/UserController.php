<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        // IZINKAN Super Admin DAN Admin. Blokir hanya User biasa.
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role == 'user') {
                abort(403, 'Akses Ditolak');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = Auth::user();

        // LOGIKA FILTER LIST USER
        if ($user->role == 'super_admin') {
            // Super Admin: Bisa lihat semua user
            $users = User::with('division')->get(); 
        } else {
            // Admin Divisi: Cuma bisa lihat pegawai di divisinya sendiri
            // Dan sembunyikan dirinya sendiri agar tidak tidak sengaja terhapus
            $users = User::where('division_id', $user->division_id)
                         ->where('id', '!=', $user->id) 
                         ->where('role', '!=', 'super_admin') // Jangan tampilkan super admin
                         ->get();
        }

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $divisions = Division::all();
        return view('users.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
            // Jika Super Admin wajib pilih divisi, jika Admin Divisi boleh kosong (nanti diisi otomatis)
            'division_id' => $currentUser->role == 'super_admin' ? 'nullable' : 'nullable', 
            'base_salary' => 'numeric|min:0'
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        // LOGIKA KEAMANAN DATA
        if ($currentUser->role == 'admin') {
            // Admin Divisi MEMAKSA data pegawai baru masuk ke divisinya
            $data['division_id'] = $currentUser->division_id;
            // Admin Divisi HANYA boleh membuat role 'user' (pegawai biasa)
            $data['role'] = 'user'; 
        }

        $user = User::create($data);

        LogHelper::record('CREATE', "Menambahkan user baru: {$user->name} sebagai {$user->role}");

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        // PROTEKSI: Admin A gak boleh edit user Divisi B
        if(Auth::user()->role == 'admin' && $user->division_id != Auth::user()->division_id) {
            abort(403, 'Anda tidak berhak mengedit pegawai divisi lain.');
        }

        $divisions = Division::all();
        return view('users.edit', compact('user', 'divisions'));
    }

    public function update(Request $request, User $user)
    {
        // PROTEKSI
        if(Auth::user()->role == 'admin' && $user->division_id != Auth::user()->division_id) {
            abort(403, 'Anda tidak berhak mengedit pegawai divisi lain.');
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'base_salary' => 'numeric|min:0'
        ]);

        $data = $request->all();

        // Cek Password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }
        
        // JIKA ADMIN YANG EDIT, KUNCI ROLE & DIVISI
        // (Supaya admin tidak iseng mengubah pegawainya jadi Super Admin)
        if(Auth::user()->role == 'admin') {
            unset($data['role']); 
            unset($data['division_id']);
        }

        $user->fill($data);

        if ($user->isDirty()) {
            $changes = [];
            foreach ($user->getDirty() as $key => $newValue) {
                if ($key == 'updated_at') continue;
                if ($key == 'password') {
                    $changes[] = "Password diganti";
                    continue;
                }
                $oldValue = $user->getOriginal($key);
                $changes[] = "Mengubah $key dari '$oldValue' menjadi '$newValue'";
            }
            $user->save();
            LogHelper::record('UPDATE', "Update User {$user->name}: " . implode(', ', $changes));
            return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
        }

        return redirect()->route('users.index')->with('info', 'Tidak ada data yang diubah');
    }

    public function destroy(User $user)
    {
        // PROTEKSI
        if(Auth::user()->role == 'admin' && $user->division_id != Auth::user()->division_id) {
            abort(403, 'Anda tidak berhak menghapus pegawai divisi lain.');
        }
        
        $namaUser = $user->name;
        $user->delete();
        LogHelper::record('DELETE', "Menghapus user: {$namaUser}");
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }
}