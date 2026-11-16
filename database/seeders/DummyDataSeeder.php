<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder data dummy untuk demonstrasi dan evaluasi aplikasi.
 *
 * Menyiapkan data realistis:
 * 1. Peserta demo (Budi, Siti, Ahmad)
 * 2. Daftar event kampus (Published, Draft, Cancelled, Masa Depan & Lampau)
 * 3. Tiket pendaftaran QR (Aktif & Dibatalkan)
 * 4. Presensi kehadiran (Attendance)
 * 5. Jejak audit keamanan (ActivityLog)
 */
class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Peserta Demo
        $pesertaPassword = Hash::make('Mahasiswa123!#');

        $budi = User::firstOrCreate(
            ['email' => 'budi@mahasiswa.kampus.ac.id'],
            [
                'name' => 'Budi Santoso',
                'password' => $pesertaPassword,
                'email_verified_at' => now(),
            ]
        );
        $budi->forceFill(['role' => 'peserta'])->save();

        $siti = User::firstOrCreate(
            ['email' => 'siti@mahasiswa.kampus.ac.id'],
            [
                'name' => 'Siti Rahmawati',
                'password' => $pesertaPassword,
                'email_verified_at' => now(),
            ]
        );
        $siti->forceFill(['role' => 'peserta'])->save();

        $ahmad = User::firstOrCreate(
            ['email' => 'ahmad@mahasiswa.kampus.ac.id'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => $pesertaPassword,
                'email_verified_at' => now(),
            ]
        );
        $ahmad->forceFill(['role' => 'peserta'])->save();

        // Cari admin untuk pencatatan scanner attendance
        $admin = User::where('role', 'admin')->first();

        // 2. Buat Daftar Event Kampus Realistis
        $eventSeminar = Event::firstOrCreate(
            ['title' => 'Seminar Nasional Cyber Security & Ethical Hacking 2026'],
            [
                'description' => 'Seminar komprehensif mengupas tren ancaman siber terkini, teknik penetration testing modern, dan strategi pertahanan jaringan kampus.',
                'location' => 'Auditorium Utama Gedung Rektorat Lt. 3',
                'start_at' => now()->addDays(14)->setTime(9, 0),
                'quota' => 150,
                'status' => 'published',
            ]
        );

        $eventWorkshop = Event::firstOrCreate(
            ['title' => 'Workshop Hands-on: Secure Coding & OWASP Top 10'],
            [
                'description' => 'Pelatihan praktik langsung mitigasi celah keamanan web terpopuler: SQL Injection, XSS, CSRF, IDOR, dan Formula Injection pada aplikasi berbasis PHP/Laravel.',
                'location' => 'Laboratorium Komputer Jaringan & Forensik',
                'start_at' => now()->addDays(7)->setTime(13, 30),
                'quota' => 40,
                'status' => 'published',
            ]
        );

        $eventCtf = Event::firstOrCreate(
            ['title' => 'Kompetisi Capture The Flag (CTF) Antar Kampus'],
            [
                'description' => 'Ajang kompetisi keamanan siber kategori Jeopardy: Cryptography, Reverse Engineering, Web Exploitation, Forensics, dan Binary Exploitation.',
                'location' => 'Gedung Pusat Inovasi Digital',
                'start_at' => now()->addDays(21)->setTime(8, 0),
                'quota' => 30,
                'status' => 'published',
            ]
        );

        $eventLampau = Event::firstOrCreate(
            ['title' => 'Kuliah Tamu: Kriptografi Kuantum & Privasi Data'],
            [
                'description' => 'Kuliah umum bersama pakar keamanan informasi internasional mengenai masa depan algoritma pasca-kuantum dan perlindungan data pribadi.',
                'location' => 'Ruang Seminar Bersama',
                'start_at' => now()->subDays(3)->setTime(10, 0),
                'quota' => 100,
                'status' => 'published',
            ]
        );

        $eventDraft = Event::firstOrCreate(
            ['title' => 'Pelatihan Incident Response & Digital Forensics (Internal Lab)'],
            [
                'description' => 'Draft rencana pelatihan investigasi insiden dan forensik digital untuk asisten laboratorium.',
                'location' => 'Lab Forensik Siber Lt. 2',
                'start_at' => now()->addDays(30)->setTime(9, 0),
                'quota' => 20,
                'status' => 'draft',
            ]
        );

        $eventCancelled = Event::firstOrCreate(
            ['title' => 'Simulasi Red Team vs Blue Team (Dibatalkan)'],
            [
                'description' => 'Acara ditunda karena pemeliharaan infrastruktur jaringan kampus.',
                'location' => 'Data Center Kampus',
                'start_at' => now()->addDays(5)->setTime(14, 0),
                'quota' => 25,
                'status' => 'cancelled',
            ]
        );

        // 3. Buat Registrasi Tiket QR
        // Budi mendaftar ke Seminar Nasional
        $regBudiSeminar = Registration::firstOrCreate(
            [
                'event_id' => $eventSeminar->id,
                'user_id' => $budi->id,
            ],
            [
                'qr_token' => bin2hex(random_bytes(32)),
                'status' => 'registered',
            ]
        );

        // Siti mendaftar ke Seminar Nasional
        $regSitiSeminar = Registration::firstOrCreate(
            [
                'event_id' => $eventSeminar->id,
                'user_id' => $siti->id,
            ],
            [
                'qr_token' => bin2hex(random_bytes(32)),
                'status' => 'registered',
            ]
        );

        // Ahmad mendaftar ke Workshop Secure Coding
        $regAhmadWorkshop = Registration::firstOrCreate(
            [
                'event_id' => $eventWorkshop->id,
                'user_id' => $ahmad->id,
            ],
            [
                'qr_token' => bin2hex(random_bytes(32)),
                'status' => 'registered',
            ]
        );

        // Budi & Siti pernah mendaftar ke Kuliah Tamu (Lampau)
        $regBudiLampau = Registration::firstOrCreate(
            [
                'event_id' => $eventLampau->id,
                'user_id' => $budi->id,
            ],
            [
                'qr_token' => bin2hex(random_bytes(32)),
                'status' => 'registered',
            ]
        );

        $regSitiLampau = Registration::firstOrCreate(
            [
                'event_id' => $eventLampau->id,
                'user_id' => $siti->id,
            ],
            [
                'qr_token' => bin2hex(random_bytes(32)),
                'status' => 'registered',
            ]
        );

        // 4. Buat Data Kehadiran (Attendance) pada Event Lampau
        if ($admin) {
            Attendance::firstOrCreate(
                ['registration_id' => $regBudiLampau->id],
                [
                    'scanned_by' => $admin->id,
                    'scanned_at' => $eventLampau->start_at->addMinutes(15),
                ]
            );

            Attendance::firstOrCreate(
                ['registration_id' => $regSitiLampau->id],
                [
                    'scanned_by' => $admin->id,
                    'scanned_at' => $eventLampau->start_at->addMinutes(25),
                ]
            );
        }

        // 5. Catat Log Aktivitas Contoh (Audit Trail)
        ActivityLog::create([
            'user_id' => $budi->id,
            'action' => 'register_event: event_id=' . $eventSeminar->id,
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(2),
        ]);

        ActivityLog::create([
            'user_id' => $siti->id,
            'action' => 'register_event: event_id=' . $eventSeminar->id,
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDay(),
        ]);

        if ($admin) {
            ActivityLog::create([
                'user_id' => $admin->id,
                'action' => 'scan_qr_success: event_id=' . $eventLampau->id,
                'ip_address' => '127.0.0.1',
                'created_at' => $eventLampau->start_at->addMinutes(15),
            ]);
        }

        $this->command->info('Data dummy (Events, Peserta, Tiket QR, Kehadiran, Log Aktivitas) berhasil di-seed!');
    }
}
