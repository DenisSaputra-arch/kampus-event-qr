<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * Keamanan Kontrol Akses (RBAC):
     * Memastikan request hanya dapat dilanjutkan jika pengguna sudah login dan
     * memiliki role yang diizinkan (misal: 'admin' atau 'peserta').
     * Jika role tidak cocok, batalkan request dengan HTTP 403 (Forbidden) untuk mencegah eskalasi hak akses.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles Daftar role yang diizinkan (contoh: 'admin', 'peserta')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Verifikasi apakah user sudah terautentikasi
        if (! $request->user()) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        // 2. Verifikasi apakah role user terdapat dalam daftar role yang diperbolehkan
        if (! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
