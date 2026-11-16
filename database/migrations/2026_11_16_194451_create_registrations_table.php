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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Token acak dari CSPRNG (64 hex characters) sebagai payload QR code
            // Dibuat unik dan tidak berurutan untuk mencegah enumeration attack
            $table->string('qr_token', 64)->unique();
            // Status pendaftaran: 'registered' (aktif) atau 'cancelled' (dibatalkan)
            $table->string('status', 20)->default('registered');
            $table->timestamps();

            // Constraint Keamanan: Satu user hanya dapat mendaftar satu kali per event
            $table->unique(['event_id', 'user_id']);

            // Index untuk mempercepat query cek kuota dan pendaftaran aktif
            $table->index(['event_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
