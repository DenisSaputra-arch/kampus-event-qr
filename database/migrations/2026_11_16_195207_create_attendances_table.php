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
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            // User (admin/panitia) yang memindai QR code
            $table->foreignId('scanned_by')->constrained('users')->cascadeOnDelete();
            // Waktu presensi kehadiran dicatat
            $table->timestamp('scanned_at');
            $table->timestamps();

            // Constraint Keamanan: Satu tiket pendaftaran hanya dapat diabsen satu kali (mencegah double-checkin)
            $table->unique('registration_id');
            $table->index('scanned_at');
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
