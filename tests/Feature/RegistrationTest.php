<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $pesertaA;
    private User $pesertaB;
    private User $admin;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pesertaA = User::factory()->create(['role' => 'peserta']);
        $this->pesertaB = User::factory()->create(['role' => 'peserta']);

        $this->admin = User::factory()->create();
        $this->admin->forceFill(['role' => 'admin'])->save();

        $this->event = Event::factory()->create([
            'title' => 'Webinar Keamanan Siber',
            'quota' => 2,
            'status' => 'published',
            'start_at' => now()->addDays(3),
        ]);
    }

    public function test_peserta_dapat_mendaftar_event_dan_mendapatkan_token_kriptografis(): void
    {
        $response = $this->actingAs($this->pesertaA)
            ->post("/events/{$this->event->id}/register");

        $registration = Registration::where('event_id', $this->event->id)
            ->where('user_id', $this->pesertaA->id)
            ->first();

        $this->assertNotNull($registration);
        $response->assertRedirect("/tickets/{$registration->id}");

        // Keamanan Token: Harus 64 karakter hex (256-bit CSPRNG dari bin2hex(random_bytes(32)))
        $this->assertEquals(64, strlen($registration->qr_token));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $registration->qr_token);
        $this->assertEquals('registered', $registration->status);
    }

    public function test_pendaftaran_ditolak_jika_kuota_penuh(): void
    {
        // Event hanya berkuota 2
        Registration::factory()->create(['event_id' => $this->event->id, 'status' => 'registered']);
        Registration::factory()->create(['event_id' => $this->event->id, 'status' => 'registered']);

        // Peserta A mencoba mendaftar saat kuota penuh
        $response = $this->actingAs($this->pesertaA)
            ->post("/events/{$this->event->id}/register");

        $response->assertSessionHas('error', 'Mohon maaf, kuota peserta untuk event ini telah penuh.');
        $this->assertDatabaseMissing('registrations', [
            'event_id' => $this->event->id,
            'user_id' => $this->pesertaA->id,
        ]);
    }

    public function test_peserta_tidak_dapat_mendaftar_ganda_secara_aktif(): void
    {
        // Peserta A mendaftar pertama kali
        $this->actingAs($this->pesertaA)->post("/events/{$this->event->id}/register");

        // Peserta A mencoba mendaftar lagi pada event yang sama
        $response = $this->actingAs($this->pesertaA)->post("/events/{$this->event->id}/register");

        $response->assertSessionHas('error', 'Anda sudah terdaftar aktif pada event ini.');
        $this->assertEquals(1, Registration::where('event_id', $this->event->id)->where('user_id', $this->pesertaA->id)->count());
    }

    public function test_peserta_lain_tidak_dapat_melihat_tiket_orang_lain_idor_prevention(): void
    {
        $tiketPesertaA = Registration::factory()->create([
            'event_id' => $this->event->id,
            'user_id' => $this->pesertaA->id,
            'status' => 'registered',
        ]);

        // Peserta B mencoba membuka tiket Peserta A (IDOR attack via manipulasi ID pada URL)
        $response = $this->actingAs($this->pesertaB)->get("/tickets/{$tiketPesertaA->id}");

        // Harus ditolak dengan status HTTP 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_pemilik_tiket_dan_admin_dapat_melihat_tiket(): void
    {
        $tiketPesertaA = Registration::factory()->create([
            'event_id' => $this->event->id,
            'user_id' => $this->pesertaA->id,
            'status' => 'registered',
        ]);

        // 1. Pemilik tiket dapat membuka tiketnya sendiri
        $responsePemilik = $this->actingAs($this->pesertaA)->get("/tickets/{$tiketPesertaA->id}");
        $responsePemilik->assertStatus(200);
        $responsePemilik->assertSee('Official Event Pass');
        $responsePemilik->assertSee($tiketPesertaA->qr_token);

        // 2. Administrator memiliki hak akses untuk memeriksa tiket
        $responseAdmin = $this->actingAs($this->admin)->get("/tickets/{$tiketPesertaA->id}");
        $responseAdmin->assertStatus(200);
    }

    public function test_peserta_dapat_membatalkan_tiket_miliknya(): void
    {
        $tiket = Registration::factory()->create([
            'event_id' => $this->event->id,
            'user_id' => $this->pesertaA->id,
            'status' => 'registered',
        ]);

        $response = $this->actingAs($this->pesertaA)->delete("/tickets/{$tiket->id}");

        $response->assertRedirect('/my-tickets');
        $this->assertDatabaseHas('registrations', [
            'id' => $tiket->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_peserta_tidak_dapat_membatalkan_tiket_orang_lain_idor_prevention(): void
    {
        $tiketPesertaA = Registration::factory()->create([
            'event_id' => $this->event->id,
            'user_id' => $this->pesertaA->id,
            'status' => 'registered',
        ]);

        // Peserta B mencoba membatalkan tiket milik Peserta A
        $response = $this->actingAs($this->pesertaB)->delete("/tickets/{$tiketPesertaA->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('registrations', [
            'id' => $tiketPesertaA->id,
            'status' => 'registered',
        ]);
    }

    public function test_tampilan_tiket_memuat_qrcode_svg(): void
    {
        $tiket = Registration::factory()->create([
            'event_id' => $this->event->id,
            'user_id' => $this->pesertaA->id,
            'status' => 'registered',
        ]);

        $response = $this->actingAs($this->pesertaA)->get("/tickets/{$tiket->id}");

        $response->assertStatus(200);
        // Pastikan QR code ter-render sebagai elemen SVG
        $response->assertSee('<svg', false);
    }

    public function test_pendaftaran_ditolak_jika_event_bukan_published(): void
    {
        $draftEvent = Event::factory()->draft()->create();

        $response = $this->actingAs($this->pesertaA)
            ->post("/events/{$draftEvent->id}/register");

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('registrations', [
            'event_id' => $draftEvent->id,
            'user_id' => $this->pesertaA->id,
        ]);
    }

    public function test_pendaftaran_ditolak_jika_waktu_event_sudah_lewat(): void
    {
        $pastEvent = Event::factory()->create([
            'status' => 'published',
            'start_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->pesertaA)
            ->post("/events/{$pastEvent->id}/register");

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('registrations', [
            'event_id' => $pastEvent->id,
            'user_id' => $this->pesertaA->id,
        ]);
    }

    public function test_tiket_yang_sudah_hadir_menampilkan_status_hadir_dan_menyembunyikan_tombol_batal(): void
    {
        $tiket = Registration::factory()->create([
            'event_id' => $this->event->id,
            'user_id' => $this->pesertaA->id,
            'status' => 'registered',
        ]);

        \App\Models\Attendance::create([
            'registration_id' => $tiket->id,
            'scanned_by' => $this->admin->id,
            'scanned_at' => now(),
        ]);

        $response = $this->actingAs($this->pesertaA)->get("/tickets/{$tiket->id}");

        $response->assertStatus(200);
        $response->assertSee('Hadir');
        $response->assertDontSee('Terdaftar Aktif');
        $response->assertDontSee('Batalkan Tiket Saya');
    }

    public function test_pembatalan_tiket_ditolak_jika_tiket_sudah_hadir(): void
    {
        $tiket = Registration::factory()->create([
            'event_id' => $this->event->id,
            'user_id' => $this->pesertaA->id,
            'status' => 'registered',
        ]);

        \App\Models\Attendance::create([
            'registration_id' => $tiket->id,
            'scanned_by' => $this->admin->id,
            'scanned_at' => now(),
        ]);

        $response = $this->actingAs($this->pesertaA)->delete("/tickets/{$tiket->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('registrations', [
            'id' => $tiket->id,
            'status' => 'registered',
        ]);
    }

    public function test_pembatalan_tiket_ditolak_jika_waktu_event_sudah_lewat(): void
    {
        $pastEvent = Event::factory()->create([
            'status' => 'published',
            'start_at' => now()->subDay(),
        ]);

        $tiket = Registration::factory()->create([
            'event_id' => $pastEvent->id,
            'user_id' => $this->pesertaA->id,
            'status' => 'registered',
        ]);

        $response = $this->actingAs($this->pesertaA)->delete("/tickets/{$tiket->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('registrations', [
            'id' => $tiket->id,
            'status' => 'registered',
        ]);
    }
}
