<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',        // Misal: "January"
        'year',         // Misal: 2026
        'basic_salary',
        'allowances',
        'overtime_pay',
        'deductions',   // Total Potongan
        'net_salary',   // Gaji Bersih
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}