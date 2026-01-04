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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            
            // Status: Hadir, Telat, Sakit, Izin, Alpha
            $table->enum('status', ['present', 'late', 'sick', 'leave', 'alpha'])->default('alpha');
            
            $table->integer('late_minutes')->default(0); // Disimpan untuk hitung denda
            $table->text('note')->nullable(); // Alasan
            $table->boolean('is_approved')->default(false); // Untuk Cuti/Lembur perlu ACC Admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
