<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Helpers\LogHelper;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        // Hanya Admin/Super Admin yang boleh kelola pengumuman
        // User biasa hanya boleh LIHAT (di dashboard), tidak akses controller ini
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role == 'user') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $announcements = Announcement::latest()->get();
        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('announcements.create');
    }

public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'type' => 'required',
        ]);

        // Simpan ke variabel dulu biar bisa ambil judulnya
        $ann = Announcement::create($request->all());
        
        // LOG LEBIH DETAIL
        LogHelper::record('CREATE', "Membuat pengumuman baru: '{$ann->title}'");
        
        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil diterbitkan.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        LogHelper::record('DELETE', 'Menghapus pengumuman.');
        return back()->with('success', 'Pengumuman dihapus.');
    }
    
    // Fitur Matikan/Nyalakan Pengumuman (Opsional tapi keren)
    public function toggle($id)
    {
        $ann = Announcement::find($id);
        $ann->is_active = !$ann->is_active; 
        $ann->save();

        // LOG STATUS BERUBAH
        $statusText = $ann->is_active ? 'AKTIF (Tayang)' : 'NON-AKTIF (Sembunyi)';
        LogHelper::record('UPDATE', "Mengubah status pengumuman '{$ann->title}' menjadi {$statusText}.");

        return back()->with('success', 'Status pengumuman diubah.');
    }
}