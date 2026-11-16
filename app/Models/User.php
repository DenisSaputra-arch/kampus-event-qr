<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Keamanan:
 * - Kolom 'role' sengaja TIDAK dimasukkan ke dalam $fillable / #[Fillable].
 *   Hal ini untuk mencegah Mass Assignment Vulnerability, sehingga input request dari luar
 *   (misal saat registrasi atau update profil) tidak dapat memanipulasi hak akses menjadi 'admin'.
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Password otomatis di-hash dengan bcrypt/argon2
        ];
    }

    /**
     * Cek apakah user memiliki peran sebagai admin.
     * Bagian penting dari kontrol akses RBAC.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user memiliki peran sebagai peserta.
     */
    public function isPeserta(): bool
    {
        return $this->role === 'peserta';
    }

    /**
     * Relasi ke pendaftaran event yang diikuti oleh user ini.
     */
    public function registrations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Relasi ke log aktivitas yang dilakukan oleh user ini (jejak audit).
     */
    public function activityLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
