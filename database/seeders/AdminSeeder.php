<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Keamanan:
     * 1. Password admin TIDAK ditulis hardcode di kode/repositori.
     * 2. Password diambil langsung dari variabel lingkungan (ADMIN_PASSWORD di .env).
     * 3. Jika variabel belum disetel, proses dihentikan agar tidak membuat kredensial default yang rentan.
     * 4. Menggunakan forceFill() untuk mengisi atribut 'role' karena 'role' diproteksi dari mass assignment.
     */
    public function run(): void
    {
        $adminPassword = env('ADMIN_PASSWORD');

        if (empty($adminPassword)) {
            throw new RuntimeException(
                'Keamanan: Variabel ADMIN_PASSWORD belum diatur di file .env. Harap isi ADMIN_PASSWORD sebelum menjalankan seeder ini.'
            );
        }

        $adminEmail = env('ADMIN_EMAIL', 'admin@kampus.ac.id');

        $admin = User::firstOrNew(['email' => $adminEmail]);
        $admin->name = 'Administrator Event';
        $admin->password = Hash::make($adminPassword);
        // Memakai forceFill karena kolom 'role' sengaja tidak ada di $fillable User model
        $admin->forceFill(['role' => 'admin']);
        $admin->save();

        $this->command->info("Akun admin ({$adminEmail}) berhasil dibuat/diperbarui dengan role 'admin'.");
    }
}
