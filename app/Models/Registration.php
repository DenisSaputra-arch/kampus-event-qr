<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    use HasFactory;

    /**
     * Keamanan Mass Assignment:
     * Menentukan kolom yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'qr_token',
        'status',
    ];

    /**
     * Relasi ke Event terkait.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relasi ke User (pemilik tiket pendaftaran).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke pencatatan kehadiran (Tahap 4).
     */
    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class);
    }

    /**
     * Cek apakah pendaftaran berstatus aktif.
     */
    public function isActive(): bool
    {
        return $this->status === 'registered';
    }

    /**
     * Cek apakah pendaftaran sudah dibatalkan.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Cek apakah pendaftaran sudah tercatat hadir.
     */
    public function isAttended(): bool
    {
        return $this->relationLoaded('attendance')
            ? $this->attendance !== null
            : $this->attendance()->exists();
    }
}
