<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    /**
     * Keamanan Mass Assignment:
     * Kolom eksplisit yang diizinkan untuk diisi.
     */
    protected $fillable = [
        'registration_id',
        'scanned_by',
        'scanned_at',
    ];

    /**
     * Tipe casting atribut.
     */
    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke pendaftaran tiket yang discan.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * Relasi ke admin/petugas yang memindai tiket.
     */
    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
