<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_baru_otomatis_memiliki_role_peserta(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'budi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('peserta', $user->role);
        $this->assertTrue($user->isPeserta());
        $this->assertFalse($user->isAdmin());
    }

    public function test_kolom_role_kebal_terhadap_serangan_mass_assignment(): void
    {
        // Penyerang mencoba mengirim role 'admin' lewat payload registrasi
        $response = $this->post('/register', [
            'name' => 'Attacker',
            'email' => 'attacker@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin', // Injeksi manipulasi role
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'attacker@example.com')->first();
        $this->assertNotNull($user);
        // Pastikan role tetap 'peserta', tidak berubah jadi 'admin'
        $this->assertEquals('peserta', $user->role);
        $this->assertFalse($user->isAdmin());
    }

    public function test_peserta_tidak_dapat_mengakses_halaman_admin_403_forbidden(): void
    {
        $peserta = User::factory()->create([
            'role' => 'peserta',
        ]);

        $response = $this->actingAs($peserta)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_guest_tidak_dapat_mengakses_halaman_admin(): void
    {
        $response = $this->get('/admin/dashboard');

        // Middleware auth me-redirect guest ke halaman login
        $response->assertRedirect('/login');
    }

    public function test_admin_dapat_mengakses_halaman_admin_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    public function test_admin_seeder_membuat_akun_admin_dari_variabel_lingkungan(): void
    {
        putenv('ADMIN_PASSWORD=KredensialRahasiaEnv123!');

        $this->seed(AdminSeeder::class);

        $admin = User::where('email', 'admin@kampus.ac.id')->first();
        $this->assertNotNull($admin);
        $this->assertEquals('admin', $admin->role);
        $this->assertTrue($admin->isAdmin());
    }
}
