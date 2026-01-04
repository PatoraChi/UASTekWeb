<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    // GET /api/announcements
    public function index()
    {
        $announcements = Announcement::where('is_active', true)
                                     ->orderBy('created_at', 'desc')
                                     ->get();

        return response()->json([
            'success' => true,
            'data' => $announcements
        ]);
    }
}