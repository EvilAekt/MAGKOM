# Panduan Deployment & Operasional Queue Worker (Production)

**Project:** MAGKOM (Sistem Informasi Manajemen Magang Kominfo)  
**Framework:** Laravel 10.x  
**Driver Rekomendasi:** `database` (Migrasi tabel `jobs`, `failed_jobs`, dan `job_batches` aktif)  
**Tujuan:** Menjamin pemrosesan asinkron (pengiriman email notifikasi, reset password, dan background jobs) berjalan stabil tanpa membebani request pengguna.

---

## 1. Arsitektur Queue & State Pengiriman

Untuk menghindari ketergantungan pada Redis di lingkungan server instansi daerah (Diskominfo), aplikasi MAGKOM menggunakan driver `database` yang teruji dan tangguh.

### Parameter Penting pada `.env`:
```ini
# Gunakan database driver untuk production
QUEUE_CONNECTION=database
```

Tabel pendukung pada database:
- `jobs`: Menyimpan antrean job yang siap dieksekusi.
- `failed_jobs`: Menyimpan job yang gagal setelah melewati batas maksimal percobaan.
- `job_batches`: Menyimpan pelacakan batching jobs jika digunakan.

---

## 2. Pengelolaan Queue Worker di Lingkungan Server Linux

### Pilihan A: Menggunakan Supervisor (Rekomendasi Utama Server Ubuntu/Debian)

1. Pasang Supervisor:
   ```bash
   sudo apt-get update
   sudo apt-get install supervisor
   ```

2. Buat berkas konfigurasi `/etc/supervisor/conf.d/magkom-worker.conf`:
   ```ini
   [program:magkom-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /var/www/magkom/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=60
   autostart=true
   autorestart=true
   stopasgroup=true
   killasgroup=true
   user=www-data
   numprocs=2
   redirect_stderr=true
   stdout_logfile=/var/www/magkom/storage/logs/worker.log
   stopwaitsecs=3600
   ```

3. Terapkan konfigurasi:
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start magkom-worker:*
   ```

---

### Pilihan B: Menggunakan systemd Service (Alternatif RHEL / CentOS / Rocky Linux)

1. Buat berkas `/etc/systemd/system/magkom-worker.service`:
   ```ini
   [Unit]
   Description=MAGKOM Queue Worker Service
   After=network.target mysql.service

   [Service]
   Type=simple
   User=www-data
   Group=www-data
   Restart=always
   RestartSec=5s
   ExecStart=/usr/bin/php /var/www/magkom/artisan queue:work database --sleep=3 --tries=3 --timeout=60
   WorkingDirectory=/var/www/magkom

   [Install]
   WantedBy=multi-user.target
   ```

2. Aktifkan dan jalankan service:
   ```bash
   sudo systemctl daemon-reload
   sudo systemctl enable magkom-worker
   sudo systemctl start magkom-worker
   ```

---

## 3. Prosedur Restart Worker Saat Deployment

Queue worker Laravel memuat kode aplikasi ke dalam memori satu kali. **Setiap kali kode diperbarui saat deployment, worker wajib di-restart** agar memuat kode terbaru:

```bash
# Perintah graceful restart: worker akan menyelesaikan job yang sedang berjalan lalu restart otomatis
php artisan queue:restart
```

Perintah di atas memberi instruksi kepada seluruh worker via cache. Supervisor atau systemd akan otomatis menyalakan kembali proses worker yang mati.

---

## 4. Monitoring & Penanganan Failed Jobs

### 4.1 Memeriksa Antrean Gagal
```bash
# Melihat daftar job yang gagal
php artisan queue:failed
```

### 4.2 Mencoba Ulang (Retry) Job Gagal
```bash
# Mencoba ulang semua job yang gagal
php artisan queue:retry all

# Atau mencoba ulang job tertentu berdasarkan UUID/ID
php artisan queue:retry <job-id>
```

### 4.3 Menghapus Job Usang
```bash
# Menghapus job gagal setelah diinvestigasi
php artisan queue:forget <job-id>

# Menghapus seluruh log failed jobs
php artisan queue:flush
```

---

## 5. Health Check & Troubleshooting

1. **Memeriksa Status Antrean:**
   ```bash
   php artisan queue:monitor database:default --max=50
   ```
2. **Worker Mati / Macet:**
   - Periksa log worker di `storage/logs/worker.log` atau `storage/logs/laravel.log`.
   - Pastikan koneksi basis data tidak mengalami `MySQL server has gone away` (telah ditangani oleh `--max-time=3600`).
   - Periksa izin folder `storage/` dan `bootstrap/cache/` agar dapat ditulisi oleh pengguna `www-data`.
