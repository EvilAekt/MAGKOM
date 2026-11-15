# Pre-Deployment & Runtime Hardening Checklist

**Aplikasi:** MAGKOM (Sistem Informasi Manajemen Magang Kominfo)  
**Versi Target:** Production v1.0.0-hardened  
**Tanggal Verifikasi:** 15 November 2026  
**Auditor:** Senior Software Engineer & Release Team  

---

## 1. Ikhtisar

Checklist ini merupakan panduan *pre-flight verification* wajib sebelum kode hasil pengerasan (*production hardening*) di-deploy ke peladen (*server*) produksi Diskominfo. Setiap item pemeriksaan harus diverifikasi dan ditandatangani oleh operator rilis.

---

## 2. Matriks Pemeriksaan Pra-Rilis (Pre-Flight Checklist)

### 2.1 Lingkungan & Versi PHP / MySQL
- [ ] **PHP Version**: Pastikan peladen menjalankan **PHP 8.3** (ZTS atau NTS) dengan ekstensi wajib: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`, `zip`, `intl`.
- [ ] **MySQL Version**: MySQL minimal versi **8.0.30+** (disarankan MySQL 8.4 LTS) dengan support transaksi InnoDB dan `ROW_FORMAT=DYNAMIC`.
- [ ] **Database Testing Terisolasi**: Pastikan terdapat database staging/testing terpisah (`magkom_test`) dan database produksi (`magkom_prod`) memiliki kredensial terpisah dengan hak akses terbatas.

### 2.2 Konfigurasi Environment (`.env`)
- [ ] `APP_ENV=production` dan `APP_DEBUG=false`.
- [ ] `APP_KEY` terisi dengan 32-byte base64 string unik (`base64:...`) yang belum pernah bocor di repository.
- [ ] `BACKUP_ENCRYPTION_ENABLED=true` dengan `BACKUP_ENCRYPTION_KEY` terisi 32-byte key khusus backup (berbeda dari `APP_KEY`).
- [ ] `REALTIME_SSE_ENABLED=false` untuk mencegah worker PHP-FPM starvation di server dengan pool pekerja terbatas (<30 worker). Fallback polling aktif otomatis di port `GET /realtime/poll`.
- [ ] `REALTIME_POLL_INTERVAL=5` (atau interval 5–10 detik).
- [ ] `QUEUE_CONNECTION=database` untuk memproses email verifikasi dan reset password secara asinkron tanpa menahan HTTP request.
- [ ] `MAIL_MAILER=smtp`, dengan host, port (587 TLS atau 465 SSL), username, dan password resmi instansi Diskominfo.

### 2.3 Antrean & Worker (Queue Deployment)
- [ ] Tabel `jobs` dan `failed_jobs` telah termigrasi via `php artisan migrate --force`.
- [ ] Supervisor atau systemd service unit `magkom-worker` telah dikonfigurasi dan aktif (`running`).
- [ ] Parameter supervisor mencakup: `php artisan queue:work database --sleep=3 --tries=3 --timeout=60 --max-time=3600 --max-jobs=1000`.
- [ ] Script deployment memanggil `php artisan queue:restart` setelah pembaruan kode.

### 2.4 Keamanan Autentikasi & Akun
- [ ] Migrasi `add_two_factor_columns_to_users_table` telah dijalankan.
- [ ] Kebijakan kata sandi SuperAdmin aktif (minimal 12 karakter, huruf besar, huruf kecil, angka, simbol).
- [ ] Akun SuperAdmin lama telah dihimbau untuk mengaktifkan 2FA melalui menu profil (`/superadmin/security/2fa`).
- [ ] Operator memahami prosedur pemulihan darurat 2FA via CLI (`php artisan auth:2fa-reset email@domain.com`).

### 2.5 Keamanan Berkas & Media CMS
- [ ] Ekstensi SVG telah dihapus dari `UploadMediaRequest` dan `ReplaceMediaRequest`.
- [ ] Upload gambar CMS hanya mengizinkan format raster (`image/jpeg`, `image/png`, `image/webp`) dengan verifikasi magic bytes `getimagesize()`.
- [ ] Direktori `storage/app/public` terhubung ke web root via `php artisan storage:link`.
- [ ] Direktori `storage/app/private` (dokumen peserta) berada di luar web root dan tidak dapat diakses langsung tanpa otorisasi.

### 2.6 Cadangan & Pemulihan (Backup & Disaster Recovery)
- [ ] Perintah `magkom:restore-database` dan `magkom:restore-files` telah teruji melalui uji coba pemulihan (restore drill).
- [ ] Jalur executable `mysqldump` terverifikasi di peladen Linux (`/usr/bin/mysqldump`).
- [ ] Target backup terenkripsi AES-256-CBC dengan signature HMAC-SHA256 (`.sql.gz.enc`).
- [ ] Disk offsite (S3, SFTP, atau external storage) telah dikonfigurasi jika multi-site backup diaktifkan.

### 2.7 Kebersihan Repositori & Paket Distribusi
- [ ] Berkas temporer (`scratch/`, `graphify-out/`, `dist/`, `.phpunit.result.cache*`) dikecualikan dalam `.gitignore` dan `.gitattributes`.
- [ ] Paket rilis dibuat menggunakan skrip resmi `scripts/package-release.sh` (Linux) atau `scripts/package-release.ps1` (Windows).
- [ ] Berkas rahasia (`.env`, `.mcp.json`, log, dokumen peserta) terbukti 100% tidak ada dalam arsip rilis.

---

## 3. Langkah-Langkah Eksekusi Deployment (Run Order)

```bash
# 1. Masuk ke maintenance mode
php artisan down --secret="kominfo-magkom-maintenance-bypass" --retry=60

# 2. Tarik kode versi rilis terbaru
git pull origin main

# 3. Instal dependensi produksi (tanpa dev packages)
composer install --no-dev --optimize-autoloader --no-interaction

# 4. Jalankan migrasi database
php artisan migrate --force

# 5. Bersihkan dan optimalkan cache framework
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Restart queue worker agar memuat kode terbaru
php artisan queue:restart

# 7. Restart PHP-FPM service (menghindari opcache stale)
sudo systemctl reload php8.3-fpm

# 8. Verifikasi status kesehatan aplikasi
curl -I -s http://127.0.0.1/health

# 9. Matikan maintenance mode
php artisan up
```

---

## 4. Validasi Pasca-Deployment (Smoke Test)

1. **Akses Halaman Publik**: Buka halaman beranda (`/`), pastikan aset CSS/JS termuat sempurna.
2. **Endpoint Health**: Panggil `GET /health`, pastikan status database dan cache menunjukkan `UP`.
3. **Login Pengguna**: Uji login akun peserta dan SuperAdmin.
4. **Verifikasi 2FA**: Pastikan tantangan 2FA berfungsi mulus pada SuperAdmin yang telah mengaktifkan 2FA.
5. **Realtime Polling**: Pastikan notifikasi/toast polling berjalan tanpa error konsol JavaScript.
6. **Queue Worker**: Kirim satu email percobaan atau request lupa sandi, pastikan job diproses oleh `queue:work` dalam <5 detik.
