<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ActivityLog — Jejak Audit Keamanan
 *
 * Log ini TIDAK boleh diubah setelah dibuat (append-only).
 * Hindari menyimpan password, token, atau PII sensitif dalam kolom 'action'.
 */
class ActivityLog extends Model
{
    // Tidak ada 'updated_at' — log bersifat immutable
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke pengguna yang melakukan aksi (nullable untuk aksi tanpa login)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
