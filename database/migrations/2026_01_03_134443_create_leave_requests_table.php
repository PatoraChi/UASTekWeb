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
    Schema::create('leave_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');

        // --- TAMBAHAN BARU ---
        $table->enum('type', ['annual', 'sick', 'emergency'])->default('annual'); // Tahunan, Sakit, Darurat
        $table->integer('days')->default(1); // Total hari cuti
        // ---------------------

        $table->date('start_date');
        $table->date('end_date');
        $table->string('reason');
        $table->string('attachment')->nullable();
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->string('admin_note')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
