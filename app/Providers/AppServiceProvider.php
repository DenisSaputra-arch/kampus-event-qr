<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set locale waktu ke Bahasa Indonesia (Senin, Selasa, dst.)
        \Carbon\Carbon::setLocale('id');

        // ─── Aturan Password Kuat (CWE-521) ───────────────────────────────────
        // Wajib: minimal 8 karakter, huruf besar+kecil, angka, simbol,
        // dan tidak ada dalam daftar password yang bocor (Have I Been Pwned)
        Password::defaults(fn () => Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()
        );

        // ─── Rate Limiting: Login (Brute Force Protection) ────────────────────
        // Maksimal 5 percobaan login per menit per IP (CWE-307)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function () {
                    return redirect()->back()
                        ->withErrors(['email' => 'Terlalu banyak percobaan login. Coba lagi dalam 1 menit.']);
                });
        });

        // ─── Rate Limiting: Scan QR (Anti-Replay Attack) ──────────────────────
        // Maksimal 60 scan per menit per admin (mencegah brute-force token QR)
        RateLimiter::for('scan', function (Request $request) {
            return Limit::perMinute(60)
                ->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
