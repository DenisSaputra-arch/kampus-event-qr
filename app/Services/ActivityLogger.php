<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Request;

/**
 * ActivityLogger — Service untuk mencatat jejak audit keamanan.
 *
 * Prinsip keamanan:
 * - Gunakan aksi pendek yang deskriptif (e.g. 'login', 'scan_qr_success')
 * - JANGAN cantumkan password, qr_token, atau data sensitif apapun
 * - IP address dicatat untuk keperluan investigasi (audit trail)
 */
class ActivityLogger
{
    /**
     * Catat satu entri log aktivitas.
     *
     * @param  string    $action  Kode aksi singkat, e.g. 'login', 'register_event', 'event_deleted'
     * @param  User|null $user    Pengguna yang melakukan aksi; null untuk aksi anonim
     */
    public static function log(string $action, ?User $user = null): void
    {
        // Ambil IP nyata; hormati X-Forwarded-For jika diizinkan Laravel TrustProxies
        $ip = Request::ip();

        ActivityLog::create([
            'user_id'    => $user?->id,
            'action'     => $action,
            'ip_address' => $ip,
        ]);
    }
}
