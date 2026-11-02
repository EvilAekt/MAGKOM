# PANDUAN PENGEMBANGAN MAGKOM (DEVELOPMENT GUIDE)
**Target Lingkungan:** PHP 8.3 | Laravel 10.x | Node.js 20+ | MySQL 8.0+ / MariaDB 10.4+  

---

## 1. PRASYARAT LINGKUNGAN PENGEMBANGAN

Pastikan perangkat lokal Anda telah terpasang:
- **PHP 8.3** (Ekstensi wajib: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `gd`, `intl`, `xml`, `zip`)
- **Composer 2.2+**
- **Node.js v20.x atau v22.x** & **NPM 10+**
- **MySQL 8.0+**

> **Catatan Runtime Antigravity:** Jika lingkungan lokal Anda saat ini masih menggunakan PHP 8.2 (misal: XAMPP PHP 8.2.12), jalankan Composer commands dengan flag `--ignore-platform-req=php` sampai instalasi PHP 8.3 diaktifkan pada PATH sistem Anda.

---

## 2. PANDUAN INSTALASI & SETUP AWAL

### Langkah 1: Kloning & Branch
```bash
git clone https://github.com/EvilAekt/MAGKOM.git
cd MAGKOM
git checkout test-2
```

### Langkah 2: Konfigurasi Environment File
```bash
cp .env.example .env
```
Sesuaikan konfigurasi koneksi database pada `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=magkom_db
DB_USERNAME=root
DB_PASSWORD=
```

### Langkah 3: Instalasi Dependensi Backend (PHP 8.3)
```bash
composer install
php artisan key:generate
```

### Langkah 4: Migrasi & Database Seeding
```bash
php artisan migrate --seed
```

### Langkah 5: Instalasi Dependensi Frontend & Kompilasi Vite
```bash
npm install
npm run build
```

---

## 3. MENJALANKAN DEV SERVER

Untuk menjalankan lingkungan pengembangan lengkap:

1. **Terminal 1 — Laravel Development Server:**
   ```bash
   php artisan serve --port=8080
   ```
2. **Terminal 2 — Vite Hot Module Replacement (HMR):**
   ```bash
   npm run dev
   ```

Aplikasi dapat diakses melalui peramban pada `http://localhost:8080`.

---

## 4. QUALITY GATES & STATIC ANALYSIS

MAGKOM menerapkan pemeriksaan kualitas kode berlapis sebelum kode dapat digabungkan ke branch utama:

### 4.1 Pemeriksaan Format Kode (Laravel Pint)
```bash
# Uji kepatuhan format PSR-12 / Laravel standar
npm run lint
# atau
./vendor/bin/pint --test

# Otomatis memperbaiki formatting kode
npm run format
# atau
./vendor/bin/pint
```

### 4.2 Analisis Statis (PHPStan / Larastan Level 2)
```bash
# Menjalankan static analysis pada direktori app/
npm run typecheck
# atau
./vendor/bin/phpstan analyse --memory-limit=1G
```

### 4.3 Menjalankan Automated Feature & Unit Tests
```bash
# Menjalankan seluruh test suite
npm run test
# atau
php artisan test

# Menjalankan grup pengujian keamanan spesifik
php artisan test tests/Feature/SecurityDeepDefensePenetrationTest.php
php artisan test tests/Feature/SecuritySuperAdminAccessControlTest.php
```

---

## 5. REKOMENDASI DEBUGGING & TIPS

- **Cache Reset:** Jika terdapat perubahan konfigurasi atau rute yang tidak langsung terbaca:
  ```bash
  php artisan optimize:clear
  ```
- **Realtime SSE Debugging:** Endpoint telemetri Server-Sent Events dapat diinspeksi melalui Network tab pada peramban (`/realtime/stream`, Content-Type: `text/event-stream`).

