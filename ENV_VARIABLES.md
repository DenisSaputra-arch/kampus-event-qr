# Panduan Variabel Lingkungan (Environment Variables)

Dokumen ini memuat daftar lengkap variabel lingkungan (`.env`) yang digunakan oleh aplikasi **Kampus Event QR**, beserta deskripsi fungsi, nilai bawaan, dan rekomendasi keamanannya.

> [!WARNING]
> **PENTING: JANGAN PERNAH** menyimpan file `.env` yang berisi kredensial atau password nyata ke dalam version control (Git). Gunakan `.env.example` sebagai referensi templat saja.

---

## 1. Konfigurasi Inti Aplikasi (`APP_*`)

| Variabel | Tipe Data | Nilai Bawaan (Lokal) | Rekomendasi Production | Penjelasan Keamanan |
| :--- | :--- | :--- | :--- | :--- |
| `APP_NAME` | String | `"Kampus Event QR"` | Sesuai nama instansi | Nama aplikasi. Wajib diapit tanda kutip dua jika mengandung spasi. |
| `APP_ENV` | String | `local` | `production` | Menentukan mode environment. Di `production`, error internal tidak ditampilkan ke user. |
| `APP_KEY` | String | *(Kosong saat clone)* | `base64:...` (32 bytes) | **KRITIS:** Kunci enkripsi sesi, cookie terenkripsi, dan token Laravel. Generate via `php artisan key:generate`. |
| `APP_DEBUG` | Boolean | `true` | `false` | **KRITIS:** Wajib `false` di production untuk mencegah kebocoran stack trace, database query, dan struktur folder (*Information Disclosure* - CWE-200). |
| `APP_URL` | URL | `http://localhost:8000` | `https://domain-anda.com` | URL basis aplikasi. Di production wajib diawali `https://` agar tautan dan asset dihasilkan via HTTPS. |

---

## 2. Autentikasi & Akun Administrator Awal

| Variabel | Tipe Data | Nilai Contoh | Penjelasan Keamanan |
| :--- | :--- | :--- | :--- |
| `ADMIN_EMAIL` | Email | `admin@kampus.ac.id` | Alamat email akun administrator yang akan dibuat oleh `AdminSeeder`. Default jika tidak diatur: `admin@kampus.ac.id`. |
| `ADMIN_PASSWORD` | String | *(Wajib diisi saat seeding)* | **KRITIS:** Password awal untuk akun admin. Wajib memiliki panjang minimal 8 karakter dengan kombinasi huruf besar, kecil, angka, dan simbol. **Tidak boleh bernilai kosong saat menjalankan `php artisan db:seed --class=AdminSeeder`**. |
| `BCRYPT_ROUNDS` | Integer | `12` | Faktor biaya (cost factor) algoritma hashing password bcrypt. Nilai `12` memberikan keseimbangan optimal antara keamanan brute-force dan performa server. |

---

## 3. Konfigurasi Basis Data (`DB_*`)

| Variabel | Tipe Data | Nilai Bawaan (Lokal XAMPP) | Rekomendasi Production | Penjelasan |
| :--- | :--- | :--- | :--- | :--- |
| `DB_CONNECTION` | String | `mysql` | `mysql` | Driver database relasional yang digunakan oleh Eloquent ORM. |
| `DB_HOST` | Hostname/IP | `127.0.0.1` | Host database cloud / service name | Alamat host server MySQL/MariaDB. Gunakan `127.0.0.1` daripada `localhost` untuk menghindari socket lookup pada macOS/Linux. |
| `DB_PORT` | Integer | `3306` | Port database | Port standar MySQL. |
| `DB_DATABASE` | String | `event_qr` | Nama database produksi | Nama database relasional yang menyimpan tabel event, registrasi, kehadiran, dan log. |
| `DB_USERNAME` | String | `root` | User non-root berprivilese terbatas | Di production, gunakan akun database khusus dengan hak akses terbatas ke database aplikasi saja. |
| `DB_PASSWORD` | String | `""` (kosong di XAMPP) | Password kuat & acak | Password koneksi database server. |

---

## 4. Konfigurasi Sesi & Cache (`SESSION_*`, `CACHE_*`)

| Variabel | Tipe Data | Nilai Bawaan | Rekomendasi Production | Penjelasan |
| :--- | :--- | :--- | :--- | :--- |
| `SESSION_DRIVER` | String | `database` | `database` atau `redis` | Driver penyimpanan sesi HTTP. Default menggunakan tabel `sessions` di database. |
| `SESSION_LIFETIME` | Integer | `120` | `60` – `120` (menit) | Masa berlaku sesi login pengguna sebelum kedaluwarsa secara otomatis (inactivity timeout). |
| `SESSION_ENCRYPT` | Boolean | `false` | `true` | Jika `true`, payload data sesi di database akan dienkripsi secara simetris menggunakan `APP_KEY`. |
| `CACHE_STORE` | String | `database` | `database` atau `redis` | Driver caching aplikasi. |

---

## 5. Konfigurasi Docker & Cloud Deployment (Railway / Render)

| Variabel | Tipe Data | Nilai Contoh | Penjelasan |
| :--- | :--- | :--- | :--- |
| `PORT` | Integer | `80` atau dinamis | Port HTTP tempat web server Apache mendengar request. Disediakan secara otomatis oleh platform PaaS seperti Railway dan Render. |
| `RUN_MIGRATIONS` | Boolean | `true` | Jika disetel `true` pada container Docker, script `docker/entrypoint.sh` akan secara otomatis menjalankan `php artisan migrate --force` saat container pertama kali menyala. |

---

## 6. Protokol Logging (`LOG_*`)

| Variabel | Nilai Bawaan (Lokal) | Rekomendasi Production | Penjelasan |
| :--- | :--- | :--- | :--- |
| `LOG_CHANNEL` | `stack` | `stack` atau `stderr` | Saluran pencatatan error/peringatan. Di environment Docker/Kubernetes/PaaS, gunakan `stderr` agar log langsung teralirkan ke log agregator platform. |
| `LOG_LEVEL` | `debug` | `error` atau `warning` | Batas minimum level log yang dicatat. Hindari `debug` di production agar tidak membebani kapasitas disk dan tidak mencatat data verbose. |
