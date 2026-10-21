> Diperbarui: 2026-10-13 | commit: 1553e56 | mode: A

# Konfigurasi Lingkungan & Skrip (ENV-AND-CONFIG.md)

Dokumen ini mendokumentasikan variabel lingkungan (*environment variables*) yang diperlukan, skrip otomasi proyek, serta alur CI/CD dan target deployment.

> **PENTING: KEBIJAKAN KERAHASIAAN KEAMANAN**  
> Dokumen ini **HANYA** mencatat nama kunci (*key names*) konfigurasi dan tujuan penggunaannya. Dilarang keras menuliskan nilai rahasia (*secrets, tokens, passwords*) di dalam berkas dokumentasi maupun commit repositori.

---

## 1. Inventaris Environment Variables

Data diambil dari template `.env.example` dan deklarasi konfigurasi di direktori `config/`:

| Nama Variabel | Status | Dipakai di Berkas Konfigurasi | Deskripsi Singkat Kegunaan |
| :--- | :--- | :--- | :--- |
| `APP_NAME` | Wajib | `config/app.php` | Nama aplikasi yang ditampilkan di header & email |
| `APP_ENV` | Wajib | `config/app.php` | Mode lingkungan runtime (`local`, `staging`, `production`) |
| `APP_KEY` | Wajib | `config/app.php` | Kunci enkripsi AES-256 untuk sesi & cookie |
| `APP_DEBUG` | Wajib | `config/app.php` | Flag penampil stack trace (Wajib `false` di production) |
| `APP_TIMEZONE` | Wajib | `config/app.php` | Zona waktu sistem (disetel ke `Asia/Jakarta`) |
| `APP_URL` | Wajib | `config/app.php` | Basis URL aplikasi untuk pembuatan Signed URL & aset |
| `APP_LOCALE` | Wajib | `config/app.php` | Bahasa default antarmuka sistem (`id`) |
| `LOG_CHANNEL` | Wajib | `config/logging.php` | Driver logging (biasanya `stack` atau `daily`) |
| `LOG_LEVEL` | Opsional | `config/logging.php` | Batas level minimum log (`debug`, `info`, `warning`) |
| `DB_CONNECTION` | Wajib | `config/database.php` | Driver database relasional (`mysql`, `sqlite`, `pgsql`) |
| `DB_HOST` | Wajib | `config/database.php` | Host server database |
| `DB_PORT` | Wajib | `config/database.php` | Port koneksi database (default MySQL: `3306`) |
| `DB_DATABASE` | Wajib | `config/database.php` | Nama basis data operasional |
| `DB_USERNAME` | Wajib | `config/database.php` | Username kredensial database |
| `DB_PASSWORD` | Wajib | `config/database.php` | Password kredensial database |
| `SESSION_DRIVER` | Wajib | `config/session.php` | Driver penyimpanan sesi (disetel ke `database`) |
| `SESSION_LIFETIME` | Wajib | `config/session.php` | Durasi kedaluwarsa sesi dalam menit (default: `120`) |
| `CACHE_STORE` | Wajib | `config/cache.php` | Driver caching aplikasi (disetel ke `database`) |
| `QUEUE_CONNECTION` | Wajib | `config/queue.php` | Driver antrean pekerjaan background (disetel ke `database`) |
| `MAIL_MAILER` | Wajib | `config/mail.php` | Driver pengiriman email (`log` di local, `smtp` di prod) |
| `MAIL_HOST` | Opsional | `config/mail.php` | Host server SMTP |
| `MAIL_PORT` | Opsional | `config/mail.php` | Port server SMTP |
| `MAIL_USERNAME` | Opsional | `config/mail.php` | Username otentikasi SMTP |
| `MAIL_PASSWORD` | Opsional | `config/mail.php` | Password otentikasi SMTP |
| `MAIL_FROM_ADDRESS`| Wajib | `config/mail.php` | Alamat email pengirim notifikasi resmi |
| `MAIL_FROM_NAME` | Wajib | `config/mail.php` | Nama pengirim email resmi |
| `GOOGLE_CLIENT_ID` | Opsional | `config/services.php` | Client ID Google OAuth2 untuk fitur Social Login peserta |
| `GOOGLE_CLIENT_SECRET`| Opsional| `config/services.php` | Client Secret Google OAuth2 |
| `GOOGLE_REDIRECT_URL` | Opsional| `config/services.php` | URL Callback OAuth Google (`/auth/google/callback`) |

---

## 2. Skrip Proyek & Perintah Penting

### A. Composer Scripts (`composer.json`)
- `composer install` : Memasang dependensi PHP sesuai `composer.lock`.
- `composer dump-autoload` : Regenerasi autoload class mapping.
- `php artisan optimize:clear` : Membersihkan seluruh cache route, config, view, dan event.
- `php artisan migrate` : Menjalankan migrasi database ke versi terkini.
- `php artisan db:seed` : Menjalankan seeder akun dan data master.

### B. NPM Scripts (`package.json`)
| Perintah | Definisi Perintah | Kegunaan |
| :--- | :--- | :--- |
| `npm run dev` | `vite` | Menjalankan Vite dev server untuk hot module replacement |
| `npm run build`| `vite build` | Mengompilasi dan meminifikasi aset CSS/JS untuk produksi |
| `npm run lint` | `./vendor/bin/pint --test` | Menjalankan uji kepatuhan standar format kode via Laravel Pint |
| `npm run format`| `./vendor/bin/pint` | Memperbaiki dan merapikan format kode PHP secara otomatis |
| `npm run test` | `php artisan test` | Menjalankan seluruh rangkaian automated test suite |

---

## 3. Konfigurasi CI/CD & Deployment

- **Strategi Build**: Single-node server atau containerized environment dengan Nginx + PHP-FPM 8.2+.
- **File System Storage Link**: Wajib mengeksekusi `php artisan storage:link` untuk akses publik aset statis. Berkas privat peserta (`storage/app/berkas/`) tetap terisolasi di luar public link.
- **Kesiapan Audit Keamanan**: Dilengkapi file kepatuhan RFC 9116 di `public/.well-known/security.txt` dan rute `/health` untuk health-check probe load balancer.
