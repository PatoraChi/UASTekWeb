<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use App\Helpers\LogHelper;

class DivisionController extends Controller
{
    // Agar hanya Super Admin yang bisa akses Controller ini
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'super_admin') {
                abort(403, 'Anda tidak memiliki akses ke halaman ini.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $divisions = Division::all();
        return view('divisions.index', compact('divisions'));
    }

    public function create()
    {
        return view('divisions.create');
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name',
        ]);

        Division::create($request->all());
        LogHelper::record('CREATE', 'Menambahkan divisi baru: ' . $request->name);
        return redirect()->route('divisions.index')
                         ->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function edit(Division $division)
    {
        return view('divisions.edit', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name,' . $division->id,
        ]);

        $division->update($request->all());
        LogHelper::record('UPDATE', 'Mengubah nama divisi menjadi: ' . $request->name);
        return redirect()->route('divisions.index')
                         ->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Division $division)
    {
        // Cek apakah divisi masih punya pegawai? (Optional Logic)
        if($division->users()->count() > 0){
             return redirect()->route('divisions.index')
                         ->with('error', 'Gagal hapus! Masih ada pegawai di divisi ini.');
        }

        $division->delete();
        LogHelper::record('DELETE', 'Menghapus divisi ID: ' . $division->id);
        return redirect()->route('divisions.index')
                         ->with('success', 'Divisi berhasil dihapus.');
    }
}