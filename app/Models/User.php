<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // --- TAMBAHAN BARU ---
        'role',          // super_admin, admin, user
        'division_id',   // Relasi ke divisi
        'position',      // Jabatan
        'base_salary',   // Gaji Pokok
        'leave_quota',
        'position_allowance',  // Jabatan (Tetap)
        'meal_allowance',      // Makan (Per Hari)
        'transport_allowance', // Transport (Per Hari)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELASI KE DIVISI ---
    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}