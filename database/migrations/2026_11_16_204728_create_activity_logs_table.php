<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel activity_logs: mencatat jejak audit aksi penting pengguna.
 *
 * PRIVASI: JANGAN pernah simpan password, qr_token, atau data sensitif di kolom 'details'.
 * Kolom 'ip_address' disimpan untuk investigasi keamanan (IP logging).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // user_id nullable: aksi seperti login-gagal bisa terjadi tanpa sesi aktif
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Kode aksi singkat, e.g. 'login', 'register_event', 'scan_qr_success'
            $table->string('action', 100);

            // IP address IPv4 atau IPv6 (maks 45 karakter untuk IPv6 penuh)
            $table->string('ip_address', 45)->nullable();

            // Kolom 'created_at' saja — tidak perlu 'updated_at' (log tidak diubah)
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
