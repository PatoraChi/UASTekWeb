<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    // Izinkan kolom 'name' diisi secara massal (melalui formulir)
    protected $fillable = ['name'];

    // Relasi: Satu Divisi memiliki banyak User (Pegawai)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}