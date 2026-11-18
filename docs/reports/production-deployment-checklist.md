# Panduan & Daftar Periksa Penerapan Produksi (Production Deployment Checklist)

| Metadata Dokumen | Keterangan |
|---|---|
| **Aplikasi** | MAGKOM (Sistem Informasi Manajemen Magang Kominfo) |
| **Framework Target** | Laravel 10.50.3 / PHP 8.2+ or PHP 8.3 |
| **Versi Dokumen** | 1.0.0-PROD |
| **Status Kesiapan** | Verified & Ready for Soft-Launch |
| **Klasifikasi** | Operasional Internal & Panduan DevOps |

---

## 1. Ikhtisar Panduan

Dokumen ini disusun sebagai panduan langkah demi langkah (*standard operating procedure*) bagi Administrator Sistem, Engineer DevOps, dan Tim Teknis dalam mempersiapkan, merilis, memverifikasi, serta memelihara sistem MAGKOM di lingkungan server produksi fisik maupun *cloud*.

Setiap langkah wajib diverifikasi dan diberi tanda centang `[x]` sebelum sistem dinyatakan aktif untuk publik (*go-live*).

---

## 2. Fase 1: Pra-Penerapan (Pre-Deployment Checklist)

### 2.1 Sanitasi Repositori & Pembuatan Paket Rilis Bersih
Dilarang menyalin direktori kerja pengembangan atau file `.env` lokal secara mentah ke server produksi.

- [ ] **1. Verifikasi Working Tree Git Bersih**:
  ```bash
  git status
  # Pastikan tidak ada file belum ter-commit di branch rilis (main)
  ```
- [ ] **2. Eksekusi Skrip Pembuatan Paket Rilis Bersih**:
  Gunakan utilitas packaging terisolasi yang telah mengecualikan berkas sensitif (`.env`, `.mcp.json`, `tests/`, `scratch/`, riwayat git):
  ```bash
  # Pada sistem Linux / macOS:
  ./scripts/package-release.sh 1.0.0-prod

  # Atau pada Windows PowerShell:
  powershell -ExecutionPolicy Bypass -File .\scripts\package-release.ps1 -Version "1.0.0-prod"
  ```
- [ ] **3. Verifikasi Isi Paket Rilis (`dist/magkom-release-1.0.0-prod.zip`)**:
  Pastikan berkas `.env`, kredensial lokal, dan artefak pengujian **TIDAK** berada di dalam paket arsip:
  ```bash
  unzip -l dist/magkom-release-1.0.0-prod.zip | grep -E "(\.env|\.mcp\.json|scratch|tests)"
  # Output HARUS kosong (tidak menemukan file tersebut)
  ```

---

### 2.2 Penyiapan Kredensial & Lingkungan Server (`.env`)
Salin template resmi `.env.example` ke server produksi sebagai berkas `.env` baru, lalu konfigurasikan parameter produksi secara mandiri.

- [ ] **1. Kunci Enkripsi Aplikasi (`APP_KEY`)**:
  Hasilkan kunci baru berentropi tinggi khusus lingkungan produksi (JANGAN gunakan kunci testing):
  ```bash
  php artisan key:generate --show
  ```
- [ ] **2. Mode Lingkungan & Debugging**:
  ```ini
  APP_NAME="MAGKOM - Magang Kominfo"
  APP_ENV=production
  APP_DEBUG=false
  APP_URL=https://magang.slemankab.go.id
  ```
- [ ] **3. Keamanan Host & Proxy**:
  ```ini
  # Daftarkan domain resmi untuk mencegah Host Header Injection
  TRUSTED_HOSTS=magang.slemankab.go.id

  # Daftarkan IP reverse proxy / load balancer upstream
  TRUSTED_PROXIES="10.0.0.1,172.16.0.1"
  ```
- [ ] **4. Basis Data Produksi (MySQL 8.0+)**:
  ```ini
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=magkom_production
  DB_USERNAME=magkom_app
  DB_PASSWORD=[PASSWORD_KUAT_MIN_24_KARAKTER]
  ```
- [ ] **5. Antrean, Cache, dan Sesi**:
  ```ini
  CACHE_DRIVER=file
  QUEUE_CONNECTION=database
  SESSION_DRIVER=file
  SESSION_LIFETIME=120
  SESSION_SECURE_COOKIE=true
  SESSION_HTTP_ONLY=true
  SESSION_SAME_SITE=lax
  ```
- [ ] **6. Rotasi & Pengaturan Logging**:
  ```ini
  LOG_CHANNEL=stack
  LOG_STACK=daily
  LOG_LEVEL=warning
  LOG_DAILY_DAYS=14
  ```
- [ ] **7. Konfigurasi Email Resmi & Validasi Kredensial**:
  ```ini
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.mailgun.org
  MAIL_PORT=587
  MAIL_USERNAME=[KREDENSIAL_RESMI]
  MAIL_PASSWORD=[KREDENSIAL_RESMI]
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS="magang@slemankab.go.id"
  MAIL_FROM_NAME="Kominfo Sleman"
  ```
  Jalankan perintah validasi ketat pra-deployment:
  ```bash
  php artisan email:validate-config --strict
  # Pastikan muncul output: [OK] Production email configuration is strictly valid.
  ```
- [ ] **8. Fitur Realtime Server-Sent Events (SSE)**:
  Tentukan kebijakan real-time sesuai kapasitas server:
  ```ini
  # Setel true jika server memiliki PHP-FPM pool >= 25 workers
  # Setel false jika server menggunakan RAM <= 2GB untuk beralih ke mode polling ringan
  REALTIME_SSE_ENABLED=false
  ```
- [ ] **9. Kredensial Google OAuth & Layanan Eksternal**:
  Pastikan `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, dan `GOOGLE_REDIRECT_URI` merujuk ke project Google Cloud resmi produksi dengan domain yang sesuai.

---

## 3. Fase 2: Konfigurasi Infrastruktur Server

### 3.1 Prasyarat Modul & Ekstensi PHP
Pastikan PHP versi 8.2 atau 8.3 berjalan dengan ekstensi wajib aktif:
- [ ] `ext-bcmath` (Perhitungan kuota presisi)
- [ ] `ext-ctype`, `ext-fileinfo` (Deteksi MIME type dokumen privat)
- [ ] `ext-json`, `ext-mbstring`, `ext-openssl`, `ext-tokenizer`
- [ ] `ext-pdo_mysql` (Koneksi basis data MySQL)
- [ ] `ext-zip` (Ekstraksi & pembuatan arsip ZIP dokumen pendaftaran)
- [ ] `ext-gd` atau `ext-imagick` (Pemrosesan foto profil)
- [ ] `ext-opcache` (Direkomendasikan aktif untuk performa tinggi)

### 3.2 Penyetelan Pool PHP-FPM (`/etc/php/8.3/fpm/pool.d/www.conf`)
- [ ] Konfigurasi process manager dinamis:
  ```ini
  pm = dynamic
  pm.max_children = 50
  pm.start_servers = 10
  pm.min_spare_servers = 5
  pm.max_spare_servers = 20
  pm.max_requests = 1000
  request_terminate_timeout = 120s
  ```

### 3.3 Konfigurasi Web Server Nginx (`/etc/nginx/sites-available/magkom`)
- [ ] Pastikan konfigurasi Nginx menerapkan SSL modern, penonaktifan buffer untuk SSE, dan blokir file tersembunyi:
  ```nginx
  server {
      listen 443 ssl http2;
      server_name magang.slemankab.go.id;
      root /var/www/magkom/current/public;
      index index.php;

      ssl_certificate /etc/letsencrypt/live/magang.slemankab.go.id/fullchain.pem;
      ssl_certificate_key /etc/letsencrypt/live/magang.slemankab.go.id/privkey.pem;
      ssl_protocols TLSv1.2 TLSv1.3;
      ssl_ciphers HIGH:!aNULL:!MD5;

      # Security Headers Tambahan
      add_header X-Frame-Options "SAMEORIGIN" always;
      add_header X-Content-Type-Options "nosniff" always;
      add_header Referrer-Policy "strict-origin-when-cross-origin" always;
      add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

      client_max_body_size 20M;

      location / {
          try_files $uri $uri/ /index.php?$query_string;
      }

      # Endpoint Realtime SSE: Wajib matikan buffering FastCGI
      location /realtime/stream {
          try_files $uri /index.php?$query_string;
          fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
          fastcgi_index index.php;
          fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
          include fastcgi_params;

          fastcgi_buffering off;
          fastcgi_cache off;
          fastcgi_read_timeout 600s;
      }

      location ~ \.php$ {
          fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
          fastcgi_index index.php;
          fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
          include fastcgi_params;
          fastcgi_read_timeout 120s;
      }

      # Blokir akses ke file .ht, .git, .env, dsb.
      location ~ /\.(?!well-known).* {
          deny all;
      }
  }

  server {
      listen 80;
      server_name magang.slemankab.go.id;
      return 301 https://$host$request_uri;
  }
  ```

---

## 4. Fase 3: Langkah Eksekusi Penerapan (Deployment Execution)

Lakukan tahapan deployment berikut pada direktori server (disarankan menggunakan struktur symlink versi rilis, misalnya `/var/www/magkom/releases/1.0.0` diarahkan ke `/var/www/magkom/current`):

- [ ] **1. Ekstraksi Paket Rilis**:
  ```bash
  mkdir -p /var/www/magkom/releases/1.0.0
  unzip dist/magkom-release-1.0.0-prod.zip -d /var/www/magkom/releases/1.0.0
  cd /var/www/magkom/releases/1.0.0
  ```
- [ ] **2. Hubungkan Berkas Lingkungan Bersama**:
  ```bash
  ln -nfs /var/www/magkom/shared/.env /var/www/magkom/releases/1.0.0/.env
  ```
- [ ] **3. Atur Hak Akses Direktori Storage & Cache**:
  ```bash
  # Hubungkan storage persisten
  mkdir -p /var/www/magkom/shared/storage/{app/private,app/public,framework/{cache,sessions,views},logs}
  rm -rf /var/www/magkom/releases/1.0.0/storage
  ln -nfs /var/www/magkom/shared/storage /var/www/magkom/releases/1.0.0/storage

  # Setel kepemilikan dan perizinan direktori
  chown -R www-data:www-data /var/www/magkom/releases/1.0.0
  chown -R www-data:www-data /var/www/magkom/shared/storage
  chmod -R 775 /var/www/magkom/releases/1.0.0/bootstrap/cache
  chmod -R 775 /var/www/magkom/shared/storage
  ```
- [ ] **4. Migrasi Basis Data Produksi**:
  Jalankan migrasi aman (termasuk pembuatan tabel `jobs` dan `job_batches`):
  ```bash
  php artisan migrate --force
  ```
- [ ] **5. Inisialisasi Data Master (Hanya untuk Fresh Deployment)**:
  > [!IMPORTANT]
  > Pada instalasi perdana, jalankan seeder dengan memberikan sandi awal SuperAdmin melalui variabel environment. Dilarang menjalankan seeder tanpa password yang ditentukan!
  ```bash
  SEED_SUPERADMIN_PASSWORD="[PASSWORD_SUPERADMIN_KUAT]" php artisan db:seed --force
  ```
- [ ] **6. Buat Symbolic Link Storage Publik**:
  ```bash
  php artisan storage:link
  ```
- [ ] **7. Bangun Cache Performa Produksi**:
  Kompilasi seluruh konfigurasi, rute, view, dan event ke dalam bytecode cache:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan event:cache
  ```
- [ ] **8. Alihkan Symlink Rilis Aktif**:
  ```bash
  ln -nfs /var/www/magkom/releases/1.0.0 /var/www/magkom/current
  ```
- [ ] **9. Reload Layanan Web Server & PHP-FPM**:
  ```bash
  sudo systemctl reload php8.3-fpm
  sudo systemctl reload nginx
  ```

---

## 5. Fase 4: Konfigurasi Service Latar Belakang (Daemons & Scheduler)

### 5.1 Pekerja Antrean Supervisor (`/etc/supervisor/conf.d/magkom-worker.conf`)
Pastikan proses antrean latar belakang berjalan secara persisten untuk menangani notifikasi email, pembuatan sertifikat, dan pemrosesan batch dokumen:

- [ ] Pasang konfigurasi supervisor:
  ```ini
  [program:magkom-worker]
  process_name=%(program_name)s_%(process_num)02d
  command=php /var/www/magkom/current/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=90
  autostart=true
  autorestart=true
  stopasgroup=true
  killasgroup=true
  user=www-data
  numprocs=2
  redirect_stderr=true
  stdout_logfile=/var/www/magkom/shared/storage/logs/worker.log
  stopwaitsecs=3600
  ```
- [ ] Muat ulang konfigurasi supervisor:
  ```bash
  sudo supervisorctl reread
  sudo supervisorctl update
  sudo supervisorctl start magkom-worker:*
  ```

### 5.2 Penjadwalan Tugas Otomatis Crontab (`crontab -e -u www-data`)
Sistem MAGKOM membutuhkan Laravel Scheduler berjalan setiap menit untuk mengeksekusi penonaktifan peserta pasca magang (`magkom:nonaktifkan-peserta`), pembersihan draft sampah pendaftaran (`magkom:bersihkan-draft-sampah`), dan pengingat review pembimbing (`magkom:ingatkan-pembimbing-review`).

- [ ] Daftarkan crontab untuk user `www-data`:
  ```crontab
  * * * * * cd /var/www/magkom/current && php artisan schedule:run >> /dev/null 2>&1
  ```

---

## 6. Fase 5: Uji Asap Pasca-Penerapan (Post-Deployment Smoke Test)

Setelah deployment aktif, lakukan verifikasi fungsional dan keamanan langsung pada URL produksi:

- [ ] **1. Pemeriksaan Status HTTP & Pengalihan HTTPS**:
  ```bash
  curl -I https://magang.slemankab.go.id
  # Verifikasi: HTTP/2 200 OK
  # Verifikasi Header Keamanan: Content-Security-Policy, X-Frame-Options, X-Content-Type-Options
  ```
- [ ] **2. Pemeriksaan Proteksi Cookie Sesi**:
  Periksa cookie respons peramban pada halaman login. Pastikan atribut `Secure; HttpOnly; SameSite=Lax` terpasang secara aktif.
- [ ] **3. Uji Coba Autentikasi Pengguna**:
  - [ ] Login sebagai SuperAdmin / Admin (Kredensial lokal + CAPTCHA).
  - [ ] Verifikasi tombol Google OAuth hanya aktif untuk pendaftar/peserta, dan menolak login staf internal.
  - [ ] Verifikasi alur Lupa Kata Sandi menolak permintaan dari email staf.
- [ ] **4. Uji Alur Pengunggahan Dokumen Privat**:
  - [ ] Unggah dokumen pendaftaran (PDF/JPG) melalui akun peserta uji.
  - [ ] Verifikasi bahwa dokumen disimpan pada `storage/app/private/dokumen/` dan TIDAK dapat diakses langsung via URL publik web tanpa tanda tangan digital (*Signed URL*).
- [ ] **5. Uji Coba Integritas Alokasi Kuota**:
  - [ ] Buka halaman pendaftaran jalur umum dan khusus.
  - [ ] Pastikan kuota terisi bertambah secara atomik dan tidak terjadi kuota minus.
- [ ] **6. Uji Coba Pengiriman Email**:
  - [ ] Lakukan pemicuan email verifikasi atau reset sandi peserta.
  - [ ] Verifikasi email diterima di kotak masuk penerima (bukan spam) dan log mencatat status pengiriman sukses.
- [ ] **7. Verifikasi File Log Bersih**:
  ```bash
  tail -n 50 /var/www/magkom/shared/storage/logs/laravel-$(date +%F).log
  # Pastikan tidak ada unhandled exception atau error fatal
  ```

---

## 7. Rencana Penanggulangan & Pemulihan Darurat (Rollback Plan)

Jika ditemukan anomali kritis pasca deployment (misalnya kegagalan database atau aplikasi berhenti melayani request):

### Langkah Cepat Rollback Aplikasi (Zero-Downtime)
1. **Arahkan Kembali Symlink ke Versi Rilis Sebelumnya**:
   ```bash
   ln -nfs /var/www/magkom/releases/[VERSI_SEBELUMNYA] /var/www/magkom/current
   ```
2. **Bersihkan dan Bangun Ulang Cache**:
   ```bash
   cd /var/www/magkom/current
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
3. **Restart Worker dan Web Server**:
   ```bash
   sudo supervisorctl restart magkom-worker:*
   sudo systemctl reload php8.3-fpm
   sudo systemctl reload nginx
   ```

### Pemulihan Basis Data (Database Restore)
Jika migrasi mengalami kegagalan struktural atau data terkorupsi:
1. Aktifkan mode pemeliharaan:
   ```bash
   php artisan down --secret="[TOKEN_BYPASS_DARURAT]"
   ```
2. Pulihkan snapshot cadangan basis data pra-deployment:
   ```bash
   mysql -u magkom_app -p magkom_production < /var/backups/magkom/backup_pre_deploy.sql
   ```
3. Nonaktifkan mode pemeliharaan:
   ```bash
   php artisan up
   ```

---

## 8. Tanda Tangan Persetujuan Peluncuran (Sign-off)

| Peran | Nama Personel | Tanggal Verifikasi | Tanda Tangan / Status |
|---|---|---|---|
| **Lead Developer** | ____________________ | ____ / ____ / 2026 | [ ] APPROVED |
| **DevOps / SysAdmin** | ____________________ | ____ / ____ / 2026 | [ ] APPROVED |
| **Security Officer** | ____________________ | ____ / ____ / 2026 | [ ] APPROVED |
| **Project Owner** | ____________________ | ____ / ____ / 2026 | [ ] APPROVED |
