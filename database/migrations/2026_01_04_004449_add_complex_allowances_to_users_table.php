<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tunjangan Tetap (Bulanan)
            $table->decimal('position_allowance', 15, 2)->default(0)->after('base_salary');
            
            // Tunjangan Variable (Rate per Hari Hadir)
            $table->decimal('meal_allowance', 15, 2)->default(0)->after('position_allowance');
            $table->decimal('transport_allowance', 15, 2)->default(0)->after('meal_allowance');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['position_allowance', 'meal_allowance', 'transport_allowance']);
        });
    }
};
