# Panduan Penerapan Produksi (Deployment Guide)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | Deployment Nginx, PHP-FPM 8.3, build aset, optimasi cache, cron scheduler |
| Referensi Implementasi | `docs/operations/nginx-performance-optimization.conf`, `run.bat` |

---

## 1. Prasyarat Server Produksi

Sebelum melakukan deployment sistem MAGKOM pada server produksi (Ubuntu Server 22.04/24.04 LTS atau RHEL/Rocky Linux), pastikan komponen berikut tersedia:

- **Web Server**: Nginx 1.20+ (dengan modul HTTP/2 dan Gzip aktif).
- **PHP**: PHP 8.3-FPM dengan ekstensi: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`.
- **Composer**: Versi 2.x terkini.
- **Node.js**: Versi 20 LTS dan NPM 10+.
- **Basis Data**: MySQL 8.0+ atau MariaDB 10.5+ (InnoDB).
- **Proses Supervisor / Cron**: Untuk menjalankan penjadwal berkala Laravel (`schedule:run`).

---

## 2. Prosedur Deployment Langkah-demi-Langkah

### Langkah 1: Kloning Kode Sumber & Checkout Branch `main`
```bash
cd /var/www/
git clone https://github.com/EvilAekt/MAGKOM.git magkom
cd magkom
git checkout main
```

### Langkah 2: Konfigurasi Hak Akses Direktori
Pastikan pengguna web server (`www-data` atau `nginx`) memiliki izin tulis pada direktori `storage` dan `bootstrap/cache`:

```bash
sudo chown -R www-data:www-data /var/www/magkom
sudo find /var/www/magkom -type d -exec chmod 755 {} \;
sudo find /var/www/magkom -type f -exec chmod 644 {} \;
sudo chmod -R 775 /var/www/magkom/storage /var/www/magkom/bootstrap/cache
```

### Langkah 3: Pemasangan Dependensi Backend (Production Mode)
Pasang pustaka Composer tanpa paket development dan optimasi autoloader:

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

### Langkah 4: Pemasangan Dependensi Frontend & Kompilasi Aset
Kompilasi seluruh 87 aset entri Vite ke direktori `public/build/`:

```bash
npm ci
npm run build
```

### Langkah 5: Konfigurasi Berkas Lingkungan (.env)
Siapkan berkas `.env` produksi dari `.env.example`:

```bash
cp .env.example .env
php artisan key:generate --force
```

Sesuaikan parameter produksi penting:
```env
APP_NAME=MagKom
APP_ENV=production
APP_DEBUG=false
APP_URL=https://magang.kominfo.slemankab.go.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=magkom_production
DB_USERNAME=user_magkom
DB_PASSWORD=KombinasiPasswordSangatKuatDB2026!

# Wajib diisi untuk keamanan seeder produksi
SEED_SUPERADMIN_PASSWORD=KombinasiSuperAdminSleman2026!

SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

### Langkah 6: Migrasi Basis Data & Seeding Master Data
Jalankan migrasi skema tabel dan inisialisasi master data:

```bash
php artisan migrate --force --seed
```

> [!IMPORTANT]
> Flag `--force` wajib disertakan pada environment `production`. Seeder otomatis hanya menjalankan data master resmi (`JurusanSeeder`, `BidangSeeder`, `PengaturanSeeder`, `PermissionSeeder`). Seluruh data pengujian dummy otomatis dibatalkan.

### Langkah 7: Tautkan Direktori Penyimpanan (Storage Link)
```bash
php artisan storage:link
```

### Langkah 8: Optimasi Kinerja Produksi (Cache Optimization)
Jalankan serialisasi konfigurasi, rute, dan template Blade untuk kecepatan respons maksimum:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 3. Konfigurasi Web Server Nginx

Gunakan template konfigurasi resmi MAGKOM dari `docs/operations/nginx-performance-optimization.conf`:

```nginx
server {
    listen 443 ssl http2;
    server_name magang.kominfo.slemankab.go.id;
    root /var/www/magkom/public;

    index index.php;
    charset utf-8;

    # SSL Certificates
    ssl_certificate /etc/ssl/certs/magang.crt;
    ssl_certificate_key /etc/ssl/private/magang.key;

    # Penanganan Khusus Realtime SSE (/realtime/stream) - Wajib Tanpa Buffering pada PHP-FPM
    location /realtime/stream {
        try_files $uri $uri/ /index.php?$query_string;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root/index.php;
        include fastcgi_params;
        fastcgi_buffering off;
        fastcgi_cache off;
        fastcgi_read_timeout 60s;
        fastcgi_hide_header X-Powered-By;
    }

    # Catatan: Jika menggunakan Laravel Octane / Reverse Proxy Port 8000 alih-alih PHP-FPM:
    # location /realtime/stream {
    #     proxy_pass http://127.0.0.1:8000;
    #     proxy_buffering off;
    #     proxy_cache off;
    #     proxy_set_header Connection '';
    #     proxy_http_version 1.1;
    #     chunked_transfer_encoding off;
    #     proxy_read_timeout 60s;
    # }

    # Static Assets Caching (1 Tahun dengan Immutable Hash Vite)
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, no-transform, immutable";
        access_log off;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
    }

    # Blokir akses ke berkas sensitif
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 4. Konfigurasi Penjadwal Tugas Berkala (Cron Scheduler)

MAGKOM membutuhkan Cron scheduler aktif untuk mengeksekusi tugas-tugas terdaftar pada `app/Console/Kernel.php`:

| Jadwal Eksekusi | Perintah Artisan | Deskripsi & Regulasi |
|---|---|---|
| **Setiap Menit** (`* * * * *`) | `magkom:publikasikan-tugas-terjadwal` | Mempublikasikan tugas bimbingan yang telah dijadwalkan oleh pembimbing. |
| **Setiap Hari 00:01 WIB** | `magkom:nonaktifkan-peserta` | Menonaktifkan akun & penempatan peserta yang melewati batas masa tenggang (BR-07). |
| **Setiap Hari 02:00 WIB** | `magkom:backup-database --clean-old` | Membuat backup basis data terkompresi Gzip dan memangkas snapshot usang. |
| **Setiap Hari 03:00 WIB** | `magkom:security-sweep` | Membersihkan token kedaluwarsa, berkas arsip zip sementara, dan registrasi menggantung (PRD 8.4, 10.3). |
| **Setiap Hari 03:30 WIB** | `RealtimeEventService::pruneOldEvents` | Memangkas antrean outbox event realtime yang telah melampaui retensi 3 hari. |
| **Tanggal 1 Bulanan 02:00 WIB** | `magkom:audit-purge` | Memangkas log audit akses data yang melewati batas retensi 365 hari (NFR-A5). |

### Pemasangan Crontab Server:
Buka konfigurasi crontab service user (misal `www-data`):
```bash
sudo crontab -u www-data -e
```

Tambahkan baris scheduler standar Laravel berikut:
```cron
* * * * * cd /var/www/magkom && php artisan schedule:run >> /dev/null 2>&1
```
