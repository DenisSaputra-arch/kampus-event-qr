<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('events')
            ->where('title', 'like', '%Secure Coding%')
            ->update([
                'title' => 'Workshop Artificial Intelligence & Data Science Mahasiswa',
                'description' => 'Pelatihan pengenalan AI, machine learning, dan implementasi tools cerdas untuk kebutuhan riset dan proyek mahasiswa.',
                'location' => 'Laboratorium Komputer Terpadu',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('events')
            ->where('title', 'Workshop Artificial Intelligence & Data Science Mahasiswa')
            ->update([
                'title' => 'Workshop Hands-on: Secure Coding & OWASP Top 10',
                'description' => 'Pelatihan praktik langsung mitigasi celah keamanan web terpopuler: SQL Injection, XSS, CSRF, IDOR, dan Formula Injection pada aplikasi berbasis PHP/Laravel.',
                'location' => 'Laboratorium Komputer Jaringan & Forensik',
            ]);
    }
};
