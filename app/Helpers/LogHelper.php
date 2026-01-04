<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    public static function record($action, $description)
    {
        ActivityLog::create([
            'user_id' => Auth::id(), // Bisa null jika user belum login (tapi jarang)
            'action' => $action,
            'description' => $description
        ]);
    }
}