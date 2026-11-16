<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    /**
     * Keamanan Mass Assignment:
     * Hanya atribut yang ditentukan secara eksplisit yang dapat diisi melalui request.
     */
    protected $fillable = [
        'title',
        'description',
        'location',
        'start_at',
        'quota',
        'status',
    ];

    /**
     * Tipe casting atribut untuk integritas data.
     */
    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'quota' => 'integer',
        ];
    }

    /**
     * Relasi ke pendaftaran tiket (akan digunakan pada Tahap 3).
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Cek apakah event masih aktif dan dibuka untuk pendaftaran.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'published' && $this->start_at->isFuture();
    }
}
