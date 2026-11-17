<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $peserta;
    private Event $eventA;
    private Event $eventB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->forceFill(['role' => 'admin'])->save();

        $this->peserta = User::factory()->create(['role' => 'peserta']);

        $this->eventA = Event::factory()->create([
            'title' => 'Konferensi Cyber Security 2026',
            'status' => 'published',
        ]);

        $this->eventB = Event::factory()->create([
            'title' => 'Workshop AI & Data Science',
            'status' => 'published',
        ]);
    }

    public function test_halaman_scan_hanya_dapat_diakses_oleh_admin_peserta_403(): void
    {
        // 1. Peserta biasa mencoba membuka halaman scan -> 403 Forbidden
        $responsePeserta = $this->actingAs($this->peserta)->get('/admin/scan');
        $responsePeserta->assertStatus(403);

        // 2. Admin dapat membuka halaman scan -> 200 OK
        $responseAdmin = $this->actingAs($this->admin)->get('/admin/scan');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Pemindai Presensi QR Code');
    }

    public function test_verifikasi_1_penolakan_jika_token_qr_tidak_ada_di_database(): void
    {
        $payload = [
            'event_id' => $this->eventA->id,
            'qr_token' => '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef', // Token palsu
        ];

        $response = $this->actingAs($this->admin)->postJson('/admin/scan/verify', $payload);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Penolakan: Token QR tidak valid atau tidak terdaftar dalam sistem.',
        ]);
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_verifikasi_2_penolakan_jika_pendaftaran_telah_dibatalkan(): void
    {
        $regCancelled = Registration::factory()->cancelled()->create([
            'event_id' => $this->eventA->id,
            'user_id' => $this->peserta->id,
            'status' => 'cancelled',
        ]);

        $payload = [
            'event_id' => $this->eventA->id,
            'qr_token' => $regCancelled->qr_token,
        ];

        $response = $this->actingAs($this->admin)->postJson('/admin/scan/verify', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Penolakan: Pendaftaran tiket ini telah dibatalkan oleh peserta.',
        ]);
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_verifikasi_3_penolakan_jika_tiket_untuk_event_berbeda(): void
    {
        // Tiket terdaftar untuk Event B
        $regEventB = Registration::factory()->create([
            'event_id' => $this->eventB->id,
            'user_id' => $this->peserta->id,
            'status' => 'registered',
        ]);

        // Namun admin sedang memindai untuk Event A
        $payload = [
            'event_id' => $this->eventA->id,
            'qr_token' => $regEventB->qr_token,
        ];

        $response = $this->actingAs($this->admin)->postJson('/admin/scan/verify', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Penolakan: Tiket ini terdaftar untuk event "' . $this->eventB->title . '", bukan event yang sedang Anda scan.',
        ]);
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_verifikasi_4_penolakan_jika_peserta_sudah_pernah_absen_sebelumnya(): void
    {
        $reg = Registration::factory()->create([
            'event_id' => $this->eventA->id,
            'user_id' => $this->peserta->id,
            'status' => 'registered',
        ]);

        // Presensi pertama kali berhasil
        Attendance::factory()->create([
            'registration_id' => $reg->id,
            'scanned_by' => $this->admin->id,
            'scanned_at' => now(),
        ]);

        // Mencoba memindai ulang untuk kedua kalinya (Double Check-in Attack)
        $payload = [
            'event_id' => $this->eventA->id,
            'qr_token' => $reg->qr_token,
        ];

        $response = $this->actingAs($this->admin)->postJson('/admin/scan/verify', $payload);

        $response->assertStatus(422);
        $this->assertStringContainsString('sudah melakukan presensi sebelumnya', $response->json('message'));
        $this->assertDatabaseCount('attendances', 1);
    }

    public function test_verifikasi_5_sukses_mencatat_kehadiran_ke_attendances(): void
    {
        $reg = Registration::factory()->create([
            'event_id' => $this->eventA->id,
            'user_id' => $this->peserta->id,
            'status' => 'registered',
        ]);

        $payload = [
            'event_id' => $this->eventA->id,
            'qr_token' => $reg->qr_token,
        ];

        $response = $this->actingAs($this->admin)->postJson('/admin/scan/verify', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertStringContainsString('Presensi Berhasil!', $response->json('message'));

        $this->assertDatabaseHas('attendances', [
            'registration_id' => $reg->id,
            'scanned_by' => $this->admin->id,
        ]);
    }

    public function test_halaman_scan_menampilkan_placeholder_pilih_event_dan_tidak_memilih_default(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/scan');

        $response->assertStatus(200);
        $response->assertSee('Pilih event');
        $response->assertSee('Scanner Belum Aktif');
        $response->assertDontSee('id="reader"', false);
    }

    public function test_halaman_scan_hanya_menampilkan_event_published(): void
    {
        $draftEvent = Event::factory()->draft()->create(['title' => 'Event Masih Draft Rahasia']);

        $response = $this->actingAs($this->admin)->get('/admin/scan');

        $response->assertStatus(200);
        $response->assertSee($this->eventA->title);
        $response->assertDontSee('Event Masih Draft Rahasia');
    }

    public function test_halaman_scan_memberikan_peringatan_jika_event_bukan_hari_ini(): void
    {
        $futureEvent = Event::factory()->create([
            'title' => 'Event Masa Depan',
            'status' => 'published',
            'start_at' => now()->addDays(5),
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/scan?event_id={$futureEvent->id}");

        $response->assertStatus(200);
        $response->assertSee('Peringatan:');
        $response->assertSee('bukan hari ini');
    }
}
