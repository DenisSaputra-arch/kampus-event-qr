<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $peserta;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->forceFill(['role' => 'admin'])->save();

        $this->peserta = User::factory()->create(['role' => 'peserta']);

        $this->event = Event::factory()->create([
            'title' => 'Simposium Keamanan Siber Nasional',
            'quota' => 100,
            'status' => 'published',
        ]);
    }

    public function test_halaman_laporan_dan_ekspor_hanya_dapat_diakses_oleh_admin(): void
    {
        // 1. Peserta mencoba mengakses laporan -> 403 Forbidden
        $responsePeserta = $this->actingAs($this->peserta)->get('/admin/reports');
        $responsePeserta->assertStatus(403);

        $responseExportPeserta = $this->actingAs($this->peserta)->get("/admin/reports/{$this->event->id}/export-csv");
        $responseExportPeserta->assertStatus(403);

        // 2. Admin dapat mengakses laporan -> 200 OK
        $responseAdmin = $this->actingAs($this->admin)->get('/admin/reports');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Rekapitulasi Terdaftar vs Hadir');
    }

    public function test_admin_dapat_melihat_rekapitulasi_terdaftar_vs_hadir(): void
    {
        // Peserta 1 terdaftar & hadir
        $reg1 = Registration::factory()->create([
            'event_id' => $this->event->id,
            'status' => 'registered',
        ]);
        Attendance::factory()->create([
            'registration_id' => $reg1->id,
            'scanned_by' => $this->admin->id,
        ]);

        // Peserta 2 terdaftar tapi belum hadir
        Registration::factory()->create([
            'event_id' => $this->event->id,
            'status' => 'registered',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/reports/{$this->event->id}");

        $response->assertStatus(200);
        $response->assertSee('Total Terdaftar');
        $response->assertSee('Total Hadir');
        $response->assertSee('Belum Hadir');
    }

    public function test_admin_dapat_mengunduh_ekspor_csv_dengan_header_tepat(): void
    {
        $reg = Registration::factory()->create([
            'event_id' => $this->event->id,
            'user_id' => $this->peserta->id,
            'status' => 'registered',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/reports/{$this->event->id}/export-csv");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename=', $response->headers->get('Content-Disposition'));
    }

    public function test_ekspor_csv_kebal_terhadap_formula_injection_cwe_1236(): void
    {
        // 4 Peserta berbahaya yang menggunakan payload formula injection pada nama atau email
        $payloads = [
            '=cmd|\' /C calc\'!A0', // Payload diawali '='
            '+1+2+3+4',              // Payload diawali '+'
            '-SUM(A1:A10)',          // Payload diawali '-'
            '@formula_test',         // Payload diawali '@'
        ];

        foreach ($payloads as $maliciousName) {
            $maliciousUser = User::factory()->create([
                'name' => $maliciousName,
                'role' => 'peserta',
            ]);

            Registration::factory()->create([
                'event_id' => $this->event->id,
                'user_id' => $maliciousUser->id,
                'status' => 'registered',
            ]);
        }

        // Admin melakukan ekspor CSV
        $response = $this->actingAs($this->admin)->get("/admin/reports/{$this->event->id}/export-csv");
        $response->assertStatus(200);

        $csvContent = $response->streamedContent();

        // Validasi Sanitasi Keamanan:
        // Setiap karakter formula di awal teks HARUS diawali dengan petik tunggal (')
        $this->assertStringContainsString("'=cmd", $csvContent);
        $this->assertStringContainsString("'+1+2+3+4", $csvContent);
        $this->assertStringContainsString("'-SUM(A1:A10)", $csvContent);
        $this->assertStringContainsString("'@formula_test", $csvContent);

        // Memastikan tidak ada cell mentah yang diawali formula tanpa petik tunggal
        $this->assertStringNotContainsString(',=cmd', $csvContent);
        $this->assertStringNotContainsString(',+1+2+3+4', $csvContent);
        $this->assertStringNotContainsString(',-SUM(A1:A10)', $csvContent);
        $this->assertStringNotContainsString(',@formula_test', $csvContent);
    }
}
