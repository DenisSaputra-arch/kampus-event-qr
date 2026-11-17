# Kampus Event QR — Sistem Tiket & Presensi Event Kampus Berbasis QR Code

Aplikasi web manajemen event kampus dan presensi kehadiran berbasis **QR Code** yang dirancang dengan penerapan prinsip-prinsip **Cyber Security / Secure Software Development Lifecycle (SSDLC)** dan pertahanan berlapis (*Defense in Depth*) terhadap celah keamanan web populer (OWASP Top 10 & CWE).

---

## 📌 Gambaran Umum Aplikasi

Aplikasi ini memfasilitasi dua peran pengguna:

1. **Peserta (Mahasiswa/Umum):**
   - Registrasi dan login akun dengan validasi kata sandi kuat.
   - Menjelajahi katalog event kampus yang berstatus *published* disertai fitur pencarian aman.
   - Mendaftar ke event dengan sistem kuota yang kebal terhadap *Race Condition*.
   - Mendapatkan e-tiket digital dengan **QR Code** berbasis token acak kriptografis (CSPRNG).
   - Membatalkan pendaftaran tiket milik sendiri (dilindungi otorisasi anti-IDOR).

2. **Administrator:**
   - Panel dashboard admin dengan kontrol akses berbasis peran (RBAC).
   - Manajemen penuh (CRUD) data event (title, description, location, start_at, quota, status).
   - Pemindai QR Code presensi langsung melalui kamera browser (*HTML5-QRCode*) maupun input token manual dengan verifikasi berurutan 5 tahap.
   - Laporan kehadiran terdaftar vs hadir secara visual (progress bar dan kartu statistik).
   - Fitur ekspor laporan presensi ke format CSV yang diamankan dari serangan *CSV / Formula Injection* (CWE-1236).
   - Jejak audit keamanan (*Activity Log*) untuk memantau aksi penting pengguna.

---

## 🛡️ Analisis & Implementasi Keamanan (Cyber Security)

Proyek ini mengimplementasikan mitigasi keamanan ketat terhadap berbagai vektor serangan:

| Celah Keamanan / Vektor Serangan | Standar / CWE | Mitigasi yang Diterapkan dalam Aplikasi |
| :--- | :--- | :--- |
| **Race Condition / Over-subscription** | CWE-362 | Penggunaan `DB::transaction()` dipadukan dengan *Pessimistic Locking* (`lockForUpdate()`) saat memeriksa kuota dan menyimpan pendaftaran tiket. |
| **Insecure Direct Object Reference (IDOR)** | CWE-639 / OWASP A01 | Otorisasi ketat melalui `RegistrationPolicy` dan `EventPolicy`. Peserta hanya dapat membuka atau membatalkan tiket miliknya sendiri; event berstatus `draft`/`cancelled` hanya dapat dibuka admin. |
| **CSV / Formula Injection** | CWE-1236 | Metode `sanitizeField()` pada `ReportController` yang menetralkan karakter eksekusi formula spreadsheet (`=`, `+`, `-`, `@`, `\t`, `\r`) dengan awalan tanda petik tunggal (`'`) serta penambahan BOM UTF-8 (`\xEF\xBB\xBF`). |
| **Mass Assignment Vulnerability** | CWE-915 | Kolom sensitif `role` sengaja dikecualikan dari atribut `#[Fillable]` model `User`. Penetapan role admin hanya dapat dilakukan via internal script/seeder dengan `forceFill()`. |
| **Cross-Site Scripting (XSS)** | CWE-79 / OWASP A03 | Semua output dinamis di-render menggunakan *Blade automatic escaping* (`{{ }}`). Penggunaan `{!! !!}` hanya dikhususkan untuk elemen SVG QR Code yang di-generate server secara internal. |
| **Clickjacking & MIME Sniffing** | CWE-1021, CWE-430 | Penerapan security headers menggunakan paket `bepsvpt/secure-headers`: `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, dan `Referrer-Policy: strict-origin-when-cross-origin`. |
| **Content Security Policy (CSP)** | W3C CSP Level 3 | Kebijakan CSP ketat yang membatasi sumber eksekusi script, style, frame ancestors (`'none'`), serta mengizinkan worker blob untuk library scanning kamera. |
| **Token Predictability / Guessing** | CWE-330 | Token QR Code dibuat menggunakan CSPRNG (`bin2hex(random_bytes(32))`) menghasilkan string heksadesimal 64 karakter (256-bit entropy) yang mustahil ditebak. |
| **Brute-Force & Denial of Service** | CWE-307, CWE-400 | Pembatasan laju (*Rate Limiting*): maksimal 5 percobaan login per menit per IP (`throttle:login`) dan 60 verifikasi scan QR per menit (`throttle:scan`). |
| **Weak Password / Credential Stuffing** | CWE-521 / OWASP A07 | Kebijakan password default: minimal 8 karakter, huruf besar, huruf kecil, angka, simbol, dan pengecekan kebocoran data (*Have I Been Pwned*). |
| **Insufficient Logging & Monitoring** | CWE-778 / OWASP A09 | Pencatatan otomatis ke tabel `activity_logs` (*immutable audit trail*) untuk event login, logout, pendaftaran, pembatalan tiket, presensi QR, dan perubahan event tanpa mencatat data rahasia/token. |

---

## 💻 Stack Teknologi

- **Backend Framework:** Laravel 13 (PHP 8.4 / 8.5)
- **Frontend / Templating:** Laravel Blade, Tailwind CSS, Alpine.js (Laravel Breeze)
- **Database:** MySQL / MariaDB (XAMPP lokal / Cloud Managed DB)
- **QR Code Generator:** `simplesoftwareio/simple-qrcode` (Vector SVG output)
- **QR Code Scanner:** `html5-qrcode` (Kamera web & integrasi JavaScript)
- **Security Headers:** `bepsvpt/secure-headers`
- **Testing:** PHPUnit (7 Automated Security Tests)
- **Containerization:** Docker (Multi-stage build PHP 8.4 Apache)

---

## 🚀 Panduan Instalasi & Menjalankan Lokal

### 1. Prasyarat Sistem
- **PHP** >= 8.2 (disarankan PHP 8.4+)
- **Composer** >= 2.x
- **Node.js** >= 18.x & **NPM**
- **MySQL / XAMPP** (port 3306)

### 2. Langkah-Langkah Setup

```bash
# 1. Clone repositori
git clone https://github.com/DenisSaputra-arch/kampus-event-qr.git
cd kampus-event-qr

# 2. Install dependensi PHP (Composer)
composer install

# 3. Install dependensi JavaScript (NPM) & build asset frontend
npm install
npm run build

# 4. Buat file environment (.env)
cp .env.example .env

# 5. Generate Application Key
php artisan key:generate
```

### 3. Konfigurasi Basis Data (`.env`)

Buka file `.env` dan sesuaikan koneksi database MySQL Anda (misal untuk XAMPP default):

```ini
APP_NAME="Kampus Event QR"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_qr
DB_USERNAME=root
DB_PASSWORD=

# Password awal untuk Admin Seeder (wajib diisi dengan kombinasi kuat)
ADMIN_PASSWORD=PasswordKuat123!
```

> **Catatan:** Pastikan database `event_qr` telah dibuat di MySQL (misal via phpMyAdmin: `CREATE DATABASE event_qr;`).

### 4. Migrasi & Seeding Data Awal

Jalankan perintah berikut untuk membuat seluruh tabel dan mengisinya dengan data uji coba lengkap:

```bash
# Jalankan migrasi tabel
php artisan migrate

# Jalankan seeder (membuat akun Admin dan data dummy)
php artisan db:seed
```

### 5. Menjalankan Server Lokal

```bash
php artisan serve
```

Aplikasi dapat diakses melalui browser di: **`http://localhost:8000`**

---

## 🔑 Akun Uji Coba / Demo

Setelah menjalankan `php artisan db:seed`, Anda dapat langsung menggunakan akun-akun berikut:

### 1. Akun Administrator
- **Email:** `admin@kampus.ac.id`
- **Password:** Sesuai nilai `ADMIN_PASSWORD` yang Anda isi di file `.env` (misal: `PasswordKuat123!`)
- **Akses:** Akses penuh ke CRUD event, Scanner QR, Laporan kehadiran & Ekspor CSV, serta Log Aktivitas.

### 2. Akun Peserta Demo
- **Peserta 1:** `budi@mahasiswa.kampus.ac.id` (Password: `Mahasiswa123!#`)
- **Peserta 2:** `siti@mahasiswa.kampus.ac.id` (Password: `Mahasiswa123!#`)
- **Peserta 3:** `ahmad@mahasiswa.kampus.ac.id` (Password: `Mahasiswa123!#`)
- **Akses:** Melihat event, mendaftar event, melihat tiket QR di menu "Tiket Saya", dan membatalkan tiket.

---

## 🧪 Pengujian Otomatis Keamanan (Security Self-Testing)

Aplikasi dilengkapi dengan rangkaian **7 automated security tests** (`SecurityTest.php`) yang menguji skenario celah keamanan penting:

```bash
php artisan test
```

Cakupan 7 skenario pengujian meliputi:
1. `test_pencarian_event_kebal_terhadap_sql_injection`: Validasi parameter binding Eloquent ORM kebal terhadap injeksi query SQL.
2. `test_peserta_tidak_bisa_mengakses_halaman_admin`: Proteksi Broken Access Control (RBAC) memblokir peserta biasa dari panel admin (403 Forbidden).
3. `test_peserta_tidak_bisa_melihat_tiket_peserta_lain_idor`: Otorisasi RegistrationPolicy memblokir akses ke tiket QR milik pengguna lain (Anti-IDOR).
4. `test_scan_qr_presensi_ganda_ditolak`: Mencegah presensi ganda (*double check-in*) pada QR Code yang sama.
5. `test_scan_token_qr_palsu_ditolak`: Menolak token QR buatan yang tidak terdaftar di basis data (404 Not Found).
6. `test_password_lemah_ditolak_saat_pendaftaran`: Memastikan kebijakan kata sandi kuat pada form registrasi.
7. `test_ekspor_csv_kebal_terhadap_formula_injection`: Netralisasi karakter eksekusi formula spreadsheet (`=`, `+`, `-`, `@`) pada berkas CSV hasil ekspor (CWE-1236).

---

## 🐳 Menjalankan Menggunakan Docker

Tersedia `Dockerfile` multi-stage siap pakai untuk deployment cloud (Railway, Render, Fly.io, dsb.):

```bash
# Build image Docker
docker build -t kampus-event-qr .

# Jalankan kontainer
docker run -d -p 8080:80 \
  -e APP_KEY=base64:... \
  -e DB_HOST=host.docker.internal \
  -e DB_DATABASE=event_qr \
  -e DB_USERNAME=root \
  -e DB_PASSWORD= \
  -e RUN_MIGRATIONS=true \
  kampus-event-qr
```

Lihat file [`ENV_VARIABLES.md`](file:///Users/mac/development/kampus-event-qr/ENV_VARIABLES.md) untuk panduan konfigurasi variabel lingkungan selengkapnya.

---

## 📄 Lisensi

Proyek ini dikembangkan untuk keperluan akademik dan tugas mata kuliah **Cyber Security**. Lisensi di bawah [MIT License](LICENSE).
