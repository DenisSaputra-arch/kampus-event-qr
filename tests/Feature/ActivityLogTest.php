<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test untuk Activity Log dan integrasi logging ke controller.
 */
class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    private function createPeserta(): User
    {
        return User::factory()->create(['name' => 'Peserta Test']);
    }

    /**
     * ActivityLogger::log() harus menyimpan entri ke database.
     */
    public function test_activity_logger_menyimpan_log_ke_database(): void
    {
        $user = $this->createPeserta();

        ActivityLogger::log('test_action', $user);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'test_action',
        ]);
    }

    /**
     * Log anonim (tanpa user) juga harus berhasil disimpan.
     */
    public function test_activity_logger_bisa_menyimpan_log_tanpa_user(): void
    {
        ActivityLogger::log('anonymous_action', null);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => null,
            'action' => 'anonymous_action',
        ]);
    }

    /**
     * Login berhasil harus mencatat log 'login'.
     */
    public function test_login_berhasil_mencatat_log(): void
    {
        $user = User::factory()->create(['password' => bcrypt('StrongP@ssw0rd!')]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'StrongP@ssw0rd!',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'login',
        ]);
    }

    /**
     * Logout harus mencatat log 'logout'.
     */
    public function test_logout_mencatat_log(): void
    {
        $user = $this->createPeserta();

        $this->actingAs($user)->post('/logout');

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'logout',
        ]);
    }

    /**
     * Pendaftaran event harus mencatat log 'register_event'.
     */
    public function test_pendaftaran_event_mencatat_log(): void
    {
        $user = $this->createPeserta();
        $event = Event::factory()->create([
            'status' => 'published',
            'quota' => 10,
            'start_at' => now()->addDays(7),
        ]);

        $this->actingAs($user)->post(route('events.register', $event));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'register_event: event_id=' . $event->id,
        ]);
    }

    /**
     * Pembatalan tiket harus mencatat log 'cancel_ticket'.
     */
    public function test_pembatalan_tiket_mencatat_log(): void
    {
        $user = $this->createPeserta();
        $event = Event::factory()->create([
            'status' => 'published',
            'quota' => 10,
            'start_at' => now()->addDays(7),
        ]);
        $registration = Registration::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'registered',
            'qr_token' => bin2hex(random_bytes(32)),
        ]);

        $this->actingAs($user)->delete(route('registrations.destroy', $registration));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'cancel_ticket: registration_id=' . $registration->id,
        ]);
    }

    /**
     * Admin dapat melihat halaman log aktivitas.
     */
    public function test_admin_dapat_melihat_halaman_log_aktivitas(): void
    {
        $admin = $this->createAdmin();

        ActivityLogger::log('test_action');

        $response = $this->actingAs($admin)->get(route('admin.activity-logs.index'));

        $response->assertOk();
        $response->assertViewIs('admin.activity-logs.index');
    }

    /**
     * Peserta tidak dapat mengakses halaman log aktivitas.
     */
    public function test_peserta_tidak_dapat_melihat_log_aktivitas(): void
    {
        $peserta = $this->createPeserta();

        $response = $this->actingAs($peserta)->get(route('admin.activity-logs.index'));

        $response->assertStatus(403);
    }

    /**
     * Model relasi: ActivityLog milik User.
     */
    public function test_relasi_activity_log_dengan_user(): void
    {
        $user = $this->createPeserta();
        ActivityLogger::log('relasi_test', $user);

        $log = ActivityLog::where('user_id', $user->id)->first();

        $this->assertNotNull($log->user);
        $this->assertEquals($user->name, $log->user->name);
    }
}
