<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test keamanan HTTP headers dan aturan password.
 */
class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifikasi header X-Content-Type-Options ada di setiap respons web.
     */
    public function test_response_memiliki_x_content_type_options_header(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    /**
     * Verifikasi header X-Frame-Options dipasang (cegah clickjacking).
     */
    public function test_response_memiliki_x_frame_options_header(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'DENY');
    }

    /**
     * Verifikasi header X-Powered-By tidak bocorkan versi PHP/Laravel.
     */
    public function test_response_tidak_memiliki_x_powered_by_header(): void
    {
        $response = $this->get('/');

        $response->assertHeaderMissing('X-Powered-By');
    }

    /**
     * Verifikasi header Referrer-Policy ada.
     */
    public function test_response_memiliki_referrer_policy_header(): void
    {
        $response = $this->get('/');

        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Verifikasi Content-Security-Policy ada.
     */
    public function test_response_memiliki_content_security_policy_header(): void
    {
        $response = $this->get('/');

        $response->assertHeader('Content-Security-Policy');
    }

    /**
     * Verifikasi aturan password minimum 8 karakter diterapkan saat registrasi.
     */
    public function test_registrasi_ditolak_jika_password_terlalu_pendek(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        // Harus ada error validasi password
        $response->assertSessionHasErrors('password');
    }

    /**
     * Verifikasi aturan password perlu huruf besar+kecil+angka+simbol.
     */
    public function test_registrasi_ditolak_jika_password_tidak_cukup_kuat(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'allowercase123',
            'password_confirmation' => 'allowercase123',
        ]);

        // Harus ada error validasi password (tidak ada huruf besar + simbol)
        $response->assertSessionHasErrors('password');
    }

    /**
     * Verifikasi password kuat diterima saat registrasi.
     */
    public function test_registrasi_berhasil_dengan_password_kuat(): void
    {
        // Gunakan password unik yang tidak ada di kamus kebocoran Have I Been Pwned
        $password = 'K@mpus3v3ntQr!2026SecureX#';

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }
}
