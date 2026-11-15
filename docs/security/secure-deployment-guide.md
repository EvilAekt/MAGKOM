# PANDUAN DEPLOYMENT AMAN KE LINGKUNGAN PRODUKSI (SEC-001)

Dokumen ini merupakan panduan resmi (*standard operating procedure*) untuk mempersiapkan, memverifikasi, dan melakukan deployment aplikasi **Sistem Manajemen Magang Kominfo (MAGKOM)** ke server produksi dengan prinsip *secure-by-default*.

---

## 1. PRINSIP DASAR DEPLOYMENT

1. **Secure by Default:** Konfigurasi default tidak boleh membuka celah keamanan. Mode debug aktif (`APP_DEBUG=true`) atau akun demo bawaan dilarang keras di lingkungan publik.
2. **Independent Controls:** Mekanisme keamanan (CAPTCHA, CSP, Secure Cookie, 2FA) tidak boleh dinonaktifkan secara otomatis hanya karena perbedaan nilai `APP_ENV`.
3. **Fail-Closed Guardrails:** Jika parameter kritis (Turnstile Secret, Backup Encryption Key, Offsite Disk) belum siap, pipeline deployment dan runtime aplikasi wajib membatalkan inisialisasi (*fail-closed*).

---

## 2. PRE-FLIGHT READINESS CHECK

Sebelum proses migrasi atau serving aplikasi diaktifkan, jalankan perintah pra-deployment resmi:

```bash
php artisan app:check-production-readiness
```

### Parameter yang Diverifikasi:

| Parameter | Ekspektasi Produksi | Dampak Jika Gagal |
|---|---|---|
| `APP_ENV` | `production` | Exit Code 1 (Deployment Diblokir) |
| `APP_DEBUG` | `false` | Exit Code 1 (Mencegah Leaking Stacktrace & Env) |
| `APP_KEY` | Terisi (32 byte base64) | Exit Code 1 (Enkripsi sesi/cookie gagal) |
| `APP_URL` | Berawalan `https://` | Exit Code 1 (Mencegah transmisi plain-text) |
| `SESSION_SECURE_COOKIE` | `true` | Exit Code 1 (Cookie hanya dikirim via TLS/HTTPS) |
| `SESSION_HTTP_ONLY` | `true` | Exit Code 1 (Mitigasi pencurian cookie via XSS) |
| `CAPTCHA_ENABLED` | `true` | Exit Code 1 (Proteksi bot login/registrasi) |
| `TURNSTILE_SECRET_KEY` | Terisi (valid Cloudflare secret) | Exit Code 1 (Verifikasi CAPTCHA gagal) |
| `BACKUP_ENCRYPTION_KEY` | Terisi & **BERBEDA** dari `APP_KEY` | Exit Code 1 (Pemisahan kunci cadangan bencana) |
| `BACKUP_OFFSITE_DISK` | Terisi (misal: `s3`, `r2`) | Exit Code 1 (Kewajiban replikasi off-site) |
| `SEED_DEMO_ACCOUNTS` | `false` / kosong | Exit Code 1 (Mencegah pembuatan akun demo) |

> **Catatan CI/CD Pipeline:** Integrasikan `php artisan app:check-production-readiness` ke dalam script CI/CD (GitHub Actions / GitLab CI) sebelum deployment beralih ke server aktif.

---

## 3. PANDUAN KONFIGURASI `.env` PRODUKSI

Gunakan acuan berkas `.env.example` terbaru:

```ini
APP_NAME="MAGKOM - Magang Kominfo"
APP_ENV=production
APP_KEY=base64:GENERATE_VIA_PHP_ARTISAN_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://magkom.slemankab.go.id

# Proteksi Sesi & Cookie Browser
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Content Security Policy & Security Headers
# Di production, CSP aktif secara ENFORCED (bukan report-only).
# Nonce dinamis diinjeksi pada setiap HTTP request oleh SecurityHeaders middleware.

# Cloudflare Turnstile Bot Mitigation (SEC-002)
CAPTCHA_ENABLED=true
CAPTCHA_DEV_BYPASS=false
TURNSTILE_ENABLED=true
TURNSTILE_SITE_KEY=0x4AAAAAA...
TURNSTILE_SECRET_KEY=0x4AAAAAA...

# Kunci Enkripsi & Replikasi Cadangan (BACKUP-001, BACKUP-002, BACKUP-003)
# BACKUP_ENCRYPTION_KEY WAJIB BERBEDA DENGAN APP_KEY!
BACKUP_ENCRYPTION_ENABLED=true
BACKUP_ENCRYPTION_KEY=base64:GENERATE_SEPARATE_32_BYTE_SECRET_KEY
BACKUP_KEY_ID=prod-2026-v1
BACKUP_OFFSITE_DISK=s3
BACKUP_RETENTION_MAX=30
BACKUP_MIN_FREE_DISK_MB=500

# Pengamanan Demo & Seeding
# Dilarang mengaktifkan flag ini di lingkungan produksi!
SEED_DEMO_ACCOUNTS=false

# Trusted Proxies & Anti-Spoofing
# Jika di belakang Cloudflare / AWS ALB, konfigurasikan IP reverse proxy:
TRUSTED_PROXIES=*
```

---

## 4. KEBIJAKAN PENGAMANAN SEEDER & AKUN BAWAAN

1. `DatabaseSeeder` telah diproteksi dengan guardrail ketat:
   - Seeder yang membuat akun demo bawaan (`UserSeeder`, `TestingUserSeeder`, dll.) **hanya akan berjalan jika `SEED_DEMO_ACCOUNTS=true` dan environment BUKAN `production`**.
   - Jika dijalankan di `APP_ENV=production`, eksekusi demo seeder akan memunculkan exception `\RuntimeException` dan dibatalkan seketika.
2. Deployment produksi hanya boleh menjalankan migrasi database:
   ```bash
   php artisan migrate --force
   ```
   Jangan pernah menyertakan flag `--seed` pada perintah deployment otomatis di server produksi.

---

## 5. REVERSE PROXY & PENGATURAN NGINX

Pastikan Nginx meneruskan header HTTPS dan real IP pengguna:

```nginx
server {
    listen 443 ssl http2;
    server_name magkom.slemankab.go.id;
    root /var/www/magkom/public;

    ssl_certificate /etc/ssl/certs/magkom.crt;
    ssl_certificate_key /etc/ssl/private/magkom.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
