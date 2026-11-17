<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Pengujian SQL Injection pada kolom pencarian event.
     */
    public function test_pencarian_event_kebal_terhadap_sql_injection(): void
    {
        Event::factory()->create([
            'title' => 'Seminar Cyber Security',
            'status' => 'published',
        ]);

        $response = $this->get('/events?q=' . urlencode("' OR '1'='1"));

        $response->assertStatus(200);
        $response->assertDontSee('SQLSTATE');
    }

    /**
     * 2. Pengujian Kontrol Akses (RBAC): Peserta biasa dilarang mengakses halaman admin.
     */
    public function test_peserta_tidak_bisa_mengakses_halaman_admin(): void
    {
        $peserta = User::factory()->create(['role' => 'peserta']);

        $response = $this->actingAs($peserta)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /**
     * 3. Pengujian IDOR: Peserta tidak boleh melihat atau mengakses tiket milik peserta lain.
     */
    public function test_peserta_tidak_bisa_melihat_tiket_peserta_lain_idor(): void
    {
        $pesertaA = User::factory()->create(['role' => 'peserta']);
        $pesertaB = User::factory()->create(['role' => 'peserta']);
        $event = Event::factory()->create(['status' => 'published']);

        $tiketPesertaA = Registration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $pesertaA->id,
            'status' => 'registered',
        ]);

        $response = $this->actingAs($pesertaB)->get("/tickets/{$tiketPesertaA->id}");

        $response->assertStatus(403);
    }

    /**
     * 4. Pengujian Keamanan Presensi: Mencegah presensi ganda dengan QR yang sama.
     */
    public function test_scan_qr_presensi_ganda_ditolak(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $peserta = User::factory()->create(['role' => 'peserta']);
        $event = Event::factory()->create(['status' => 'published']);

        $tiket = Registration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $peserta->id,
            'status' => 'registered',
        ]);

        Attendance::create([
            'registration_id' => $tiket->id,
            'scanned_by' => $admin->id,
            'scanned_at' => now(),
        ]);

        $response = $this->actingAs($admin)->postJson('/admin/scan/verify', [
            'event_id' => $event->id,
            'qr_token' => $tiket->qr_token,
        ]);

        $response->assertStatus(422);
    }

    /**
     * 5. Pengujian Token QR: Menolak token QR palsu atau tidak terdaftar.
     */
    public function test_scan_token_qr_palsu_ditolak(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = Event::factory()->create(['status' => 'published']);

        $response = $this->actingAs($admin)->postJson('/admin/scan/verify', [
            'event_id' => $event->id,
            'qr_token' => 'token_palsu_yang_pasti_tidak_ada_di_database_1234567890',
        ]);

        $response->assertStatus(404);
    }

    /**
     * 6. Pengujian Autentikasi: Password lemah ditolak saat pendaftaran akun.
     */
    public function test_password_lemah_ditolak_saat_pendaftaran(): void
    {
        $response = $this->post('/register', [
            'name' => 'Peserta Baru',
            'email' => 'pesertabaru@kampus.ac.id',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'pesertabaru@kampus.ac.id']);
    }

    /**
     * 7. Pengujian Ekspor Data: Kebal terhadap serangan CSV Formula Injection (CWE-1236).
     */
    public function test_ekspor_csv_kebal_terhadap_formula_injection(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = Event::factory()->create(['status' => 'published']);
        $peserta = User::factory()->create([
            'name' => "=cmd|' /C calc'!A0",
            'role' => 'peserta',
        ]);

        $tiket = Registration::factory()->create([
            'event_id' => $event->id,
            'user_id' => $peserta->id,
            'status' => 'registered',
        ]);

        Attendance::create([
            'registration_id' => $tiket->id,
            'scanned_by' => $admin->id,
            'scanned_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.export', $event));

        $response->assertStatus(200);
        $content = $response->streamedContent();
        $this->assertStringContainsString("'=cmd|' /C calc'!A0", $content);
    }
}
