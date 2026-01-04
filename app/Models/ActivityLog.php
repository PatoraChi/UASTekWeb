<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    // Izinkan kolom ini diisi
    protected $fillable = ['user_id', 'action', 'description'];

    // Relasi: Log ini milik siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}