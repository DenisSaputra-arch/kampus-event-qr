<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->forceFill(['role' => 'admin'])->save();

        $this->peserta = User::factory()->create([
            'role' => 'peserta',
        ]);
    }

    public function test_siapa_saja_dapat_melihat_daftar_event_published(): void
    {
        Event::factory()->create([
            'title' => 'Workshop Ethical Hacking',
            'status' => 'published',
        ]);

        $response = $this->get('/events');

        $response->assertStatus(200);
        $response->assertSee('Workshop Ethical Hacking');
    }

    public function test_pencarian_event_berfungsi_dan_kebal_terhadap_sql_injection(): void
    {
        Event::factory()->create([
            'title' => 'Kuliah Umum Kriptografi',
            'location' => 'Lab Cyber Sec',
            'status' => 'published',
        ]);

        Event::factory()->create([
            'title' => 'Lomba Desain UI/UX',
            'location' => 'Gedung B',
            'status' => 'published',
        ]);

        // 1. Pencarian normal
        $response = $this->get('/events?q=Kriptografi');
        $response->assertStatus(200);
        $response->assertSee('Kuliah Umum Kriptografi');
        $response->assertDontSee('Lomba Desain UI/UX');

        // 2. Uji injeksi SQL (SQL Injection payload)
        // Parameter binding Eloquent harus memperlakukan payload sebagai string biasa tanpa syntax error
        $sqlInjectionPayload = "' OR '1'='1' -- ";
        $responseInjection = $this->get('/events?q=' . urlencode($sqlInjectionPayload));
        $responseInjection->assertStatus(200);
        // Tidak boleh menampilkan semua record secara sembarangan karena '1'='1'
        $responseInjection->assertDontSee('Kuliah Umum Kriptografi');
    }

    public function test_peserta_tidak_dapat_melihat_event_draft_atau_cancelled(): void
    {
        $draftEvent = Event::factory()->draft()->create([
            'title' => 'Event Rahasia Internal',
        ]);

        // Guest atau peserta mencoba mengakses langsung URL event draft (harus 404 Not Found)
        $response = $this->actingAs($this->peserta)->get("/events/{$draftEvent->id}");
        $response->assertStatus(404);
    }

    public function test_admin_dapat_melihat_event_draft(): void
    {
        $draftEvent = Event::factory()->draft()->create([
            'title' => 'Event Rencana Admin',
        ]);

        $response = $this->actingAs($this->admin)->get("/events/{$draftEvent->id}");
        $response->assertStatus(200);
        $response->assertSee('Event Rencana Admin');
    }

    public function test_admin_dapat_membuat_event_baru(): void
    {
        $payload = [
            'title' => 'Seminar Cloud Security 2026',
            'description' => 'Membahas pengamanan infrastruktur cloud dan container.',
            'location' => 'Auditorium Lt. 3',
            'start_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'quota' => 150,
            'status' => 'published',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/events', $payload);

        $response->assertRedirect('/admin/events');
        $this->assertDatabaseHas('events', [
            'title' => 'Seminar Cloud Security 2026',
            'quota' => 150,
        ]);
    }

    public function test_validasi_form_request_menolak_kuota_tidak_valid_atau_data_kosong(): void
    {
        $invalidPayload = [
            'title' => '',
            'description' => '',
            'location' => '',
            'start_at' => now()->subDay()->format('Y-m-d H:i:s'), // Waktu di masa lalu
            'quota' => 0, // Kuota minimal harus 1
            'status' => 'invalid_status',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/events', $invalidPayload);

        $response->assertSessionHasErrors(['title', 'description', 'location', 'start_at', 'quota', 'status']);
        $this->assertDatabaseCount('events', 0);
    }

    public function test_peserta_ditolak_saat_mencoba_membuat_event_403_forbidden(): void
    {
        $payload = [
            'title' => 'Event Liar Peserta',
            'description' => 'Mencoba bypass otorisasi role.',
            'location' => 'Online',
            'start_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'quota' => 50,
            'status' => 'published',
        ];

        $response = $this->actingAs($this->peserta)->post('/admin/events', $payload);

        // Middleware role:admin menolak request dengan HTTP 403
        $response->assertStatus(403);
        $this->assertDatabaseMissing('events', ['title' => 'Event Liar Peserta']);
    }

    public function test_peserta_ditolak_saat_mencoba_mengubah_atau_menghapus_event_idor_protection(): void
    {
        $event = Event::factory()->create(['title' => 'Event Asli']);

        // 1. Coba update
        $responseUpdate = $this->actingAs($this->peserta)->put("/admin/events/{$event->id}", [
            'title' => 'Di-hack oleh Peserta',
            'description' => 'Deskripsi baru',
            'location' => 'Lokasi baru',
            'start_at' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'quota' => 999,
            'status' => 'published',
        ]);
        $responseUpdate->assertStatus(403);

        // 2. Coba delete
        $responseDelete = $this->actingAs($this->peserta)->delete("/admin/events/{$event->id}");
        $responseDelete->assertStatus(403);

        // Pastikan data di database tidak berubah
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Event Asli',
        ]);
    }

    public function test_admin_dapat_mengubah_dan_menghapus_event(): void
    {
        $event = Event::factory()->create([
            'title' => 'Judul Sebelum Diubah',
            'quota' => 50,
        ]);

        // 1. Admin melakukan update
        $responseUpdate = $this->actingAs($this->admin)->put("/admin/events/{$event->id}", [
            'title' => 'Judul Setelah Diperbarui',
            'description' => $event->description,
            'location' => $event->location,
            'start_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'quota' => 80,
            'status' => 'published',
        ]);
        $responseUpdate->assertRedirect('/admin/events');
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Judul Setelah Diperbarui',
            'quota' => 80,
        ]);

        // 2. Admin melakukan delete
        $responseDelete = $this->actingAs($this->admin)->delete("/admin/events/{$event->id}");
        $responseDelete->assertRedirect('/admin/events');
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }
}
