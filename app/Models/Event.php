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

    /**
     * Jumlah peserta yang terdaftar aktif saat ini.
     */
    public function activeRegistrationsCount(): int
    {
        return $this->registrations()->where('status', 'registered')->count();
    }

    /**
     * Sisa kuota peserta yang tersedia.
     */
    public function remainingQuota(): int
    {
        return max(0, $this->quota - $this->activeRegistrationsCount());
    }

    /**
     * Cek apakah kuota pendaftaran sudah habis.
     */
    public function isFull(): bool
    {
        return $this->remainingQuota() <= 0;
    }

    /**
     * Mendapatkan URL gambar lokal event yang relevan.
     */
    public function getImageUrl(): string
    {
        $titleLower = strtolower($this->title);

        if (str_contains($titleLower, 'cyber') || str_contains($titleLower, 'hacking')) {
            return asset('images/events/seminar-cyber.jpg');
        }
        if (str_contains($titleLower, 'workshop') || str_contains($titleLower, 'coding') || str_contains($titleLower, 'owasp')) {
            return asset('images/events/workshop-coding.jpg');
        }
        if (str_contains($titleLower, 'ctf') || str_contains($titleLower, 'kompetisi') || str_contains($titleLower, 'capture')) {
            return asset('images/events/ctf-competition.jpg');
        }
        if (str_contains($titleLower, 'kuliah') || str_contains($titleLower, 'kriptografi') || str_contains($titleLower, 'kuantum')) {
            return asset('images/events/lecture-quantum.jpg');
        }
        if (str_contains($titleLower, 'forensik') || str_contains($titleLower, 'incident') || str_contains($titleLower, 'response')) {
            return asset('images/events/digital-forensics.jpg');
        }

        return asset('images/events/campus-general.jpg');
    }
}
