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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content'); // Isi pengumuman
            $table->enum('type', ['info', 'warning', 'danger', 'success'])->default('info'); // Warna background
            $table->boolean('is_active')->default(true); // Agar bisa di-hide tanpa dihapus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
