# Kampus Event QR

Aplikasi web untuk manajemen pendaftaran event kampus dan presensi kehadiran mahasiswa menggunakan QR Code.

- **URL Aplikasi Aktif:** [https://kampus-event-qr-production.up.railway.app](https://kampus-event-qr-production.up.railway.app)
- **Repositori Git:** [https://github.com/DenisSaputra-arch/kampus-event-qr](https://github.com/DenisSaputra-arch/kampus-event-qr)

---

## 📌 Fitur Aplikasi

1. **Mahasiswa / Peserta:**
   - Registrasi dan login akun.
   - Melihat daftar event kampus yang sedang buka pendaftaran.
   - Mendaftar ke event dan mendapatkan e-tiket dengan QR Code otomatis.
   - Melihat dan mencetak e-tiket di menu "Tiket Saya".
   - Membatalkan pendaftaran tiket sebelum acara berlangsung.

2. **Panitia / Administrator:**
   - Kelola data event (tambah, edit, hapus, dan lihat detail event).
   - Pemindai QR Code presensi menggunakan kamera perangkat atau input token manual.
   - Rekapitulasi jumlah peserta terdaftar dan hadir.
   - Unduh laporan presensi ke berkas CSV.
   - Memantau log aktivitas sistem (login, akses, pembatalan, dan scan presensi).

---

## 🛠️ Teknologi yang Digunakan

- **Backend:** Laravel 11 (PHP 8.4)
- **Frontend:** Laravel Blade, Tailwind CSS, Alpine.js
- **Basis Data:** MySQL
- **Pustaka QR Code:**
  - `simplesoftwareio/simple-qrcode` (membuat QR Code tiket)
  - `html5-qrcode` (memindai QR Code lewat kamera browser)
- **Hosting & Deployment:** Railway (Docker & Managed MySQL)

---

## ⚙️ Konfigurasi Environment Variable (`.env`)

Salin berkas `.env.example` menjadi `.env`. Variabel penting yang perlu disesuaikan:

```ini
APP_NAME="Kampus Event QR"
APP_ENV=local
APP_KEY=                 # Dibuat otomatis lewat php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000

# Konfigurasi Database MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_qr
DB_USERNAME=root
DB_PASSWORD=

# Password awal untuk akun Administrator saat seeding database
ADMIN_PASSWORD=PasswordKuat123!
```

---

## 🚀 Panduan Menjalankan Secara Lokal

1. **Clone Repositori:**
   ```bash
   git clone https://github.com/DenisSaputra-arch/kampus-event-qr.git
   cd kampus-event-qr
   ```

2. **Pasang Dependensi:**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Setup Environment & Database:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Pastikan database `event_qr` sudah dibuat di MySQL Anda.*

4. **Migrasi dan Data Awal (Seeder):**
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Buka di browser: `http://localhost:8000`

---

## 🔑 Akun Uji Coba (Demo)

Setelah menjalankan `php artisan migrate --seed`, akun berikut dapat langsung digunakan:

1. **Administrator (Panitia):**
   - **Email:** `admin@kampus.ac.id`
   - **Password:** `PasswordKuat123!`
   - **Fitur:** Kelola event, scanner presensi QR, laporan & ekspor CSV, log aktivitas.

2. **Peserta (Mahasiswa):**
   - **Email:** `budi@mahasiswa.kampus.ac.id`
   - **Password:** `Mahasiswa123!#`
   - **Fitur:** Daftar event, buka tiket QR, batalkan tiket.

---

## 🧪 Hasil Self-Testing Keamanan

Pengujian keamanan dilakukan secara otomatis menggunakan PHPUnit:

```bash
php artisan test
```

Terdapat 7 skenario pengujian utama:
1. **SQL Injection:** Memastikan parameter pencarian event aman dari manipulasi query SQL.
2. **Kontrol Akses (Role):** Memastikan peserta biasa ditolak (403 Forbidden) saat mencoba membuka halaman dashboard admin.
3. **Anti-IDOR:** Memastikan peserta tidak dapat melihat atau membatalkan tiket milik orang lain dengan mengganti ID pada URL.
4. **Anti-Presensi Ganda:** Memastikan tiket QR yang sudah tercatat hadir tidak bisa digunakan kembali untuk presensi.
5. **Token Palsu:** Memastikan sistem menolak token QR yang tidak terdaftar di database.
6. **Validasi Password:** Memastikan pendaftaran akun menolak kombinasi password yang terlalu lemah.
7. **Anti-Formula Injection (CSV):** Memastikan ekspor data kehadiran membersihkan karakter formula spreadsheet agar berkas CSV aman dibuka di aplikasi spreadsheet.

---

## 🤖 Ringkasan Penggunaan AI Agent

Sesuai ketentuan tugas, AI Agent digunakan sebagai asisten dalam proses pengembangan:
- **Alat yang digunakan:** Claude (Anthropic) dan Gemini (Google).
- **Bagian yang dibantu:**
  - Membantu penyusunan struktur basis data relasional (event, registrasi, kehadiran, dan log aktivitas).
  - Membantu integrasi pustaka scanner kamera JavaScript (`html5-qrcode`) ke Blade view.
  - Membantu pembuatan skenario pengujian otomatis (`tests/Feature/SecurityTest.php`).
  - Membantu debugging konfigurasi Dockerfile dan Apache untuk deployment di Railway.
- **Contoh Prompt Utama:**
  - *"Buat policy di Laravel agar peserta hanya bisa membuka tiket miliknya sendiri dan admin bisa membuka semua tiket."*
  - *"Bagaimana cara mengamankan ekspor CSV dari formula injection saat nama peserta diawali karakter seperti sama dengan (=)?"*
  - *"Tuliskan test otomatis PHPUnit untuk memverifikasi bahwa presensi ganda dengan token QR yang sama akan ditolak."*
