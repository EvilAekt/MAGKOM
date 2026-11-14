# Panduan Pengembangan & Lingkungan Kerja MagKom (Development Guide)

**Target Lingkungan Terkunci:** **PHP 8.3.x** | **Laravel 10.x** | **Node.js 20+** | **MySQL 8.0+** (atau SQLite `:memory:` untuk Automated Testing)

Dokumen ini merupakan panduan orientasi bagi developer yang baru bergabung atau melanjutkan pengembangan sistem MagKom. Ikuti instruksi ini secara disiplin untuk menjaga konsistensi dan integritas kode.

---

## 1. Prasyarat Lingkungan Pengembangan

Pastikan mesin pengembangan lokal Anda telah memenuhi spesifikasi berikut:

- **PHP 8.3.x Mutlak**: Target runtime sistem terkunci pada PHP 8.3.
  - *Ekstensi Wajib*: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `intl`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `pdo_sqlite`, `sqlite3`, `tokenizer`, `xml`, `zip`.
- **Composer 2.2+**: Pengelola paket dependensi PHP.
- **Node.js v20.x LTS** & **NPM 10+**: Runtime untuk pipeline aset Vite.
- **MySQL Server 8.0+ / MariaDB 10.5+**: Basis data pengembangan lokal (menggunakan engine InnoDB).

> [!IMPORTANT]
> ### Penjelasan Teknis Terkait Platform Requirement PHP 8.3
> Repositori MAGKOM menetapkan batasan platform ketat pada `composer.json` (`"php": "^8.3"`).
>
> Jika mesin lokal Anda masih mengarahkan PATH ke instalasi PHP versi lama (misalnya PHP 8.2.12 pada XAMPP bawaan), eksekusi perintah `php artisan` atau `vendor/bin/phpunit` akan langsung dihentikan oleh berkas `vendor/composer/platform_check.php` dengan pesan:
> ```text
> PHP Fatal error: Uncaught RuntimeException: Composer detected issues in your platform: 
> Your Composer dependencies require a PHP version ">= 8.3.0". You are running 8.2.12.
> ```
> Kondisi ini menghasilkan **exit code 1** sebelum framework Laravel sempat melakukan proses booting.
>
> **Solusi Standar**: Anda wajib mengarahkan variabel lingkungan PATH sistem ke biner PHP 8.3 atau menjalankan lingkungan pengembangan melalui container Docker / WSL ber-PHP 8.3. Flag Composer `--ignore-platform-req=php` **bukanlah solusi dan dilarang dianggap sebagai bukti kompatibilitas runtime**, karena aplikasi memanfaatkan fitur dan pengetatan tipe data PHP 8.3 pada tingkat kode. Pada pipeline CI/CD GitHub Actions (`.github/workflows/security.yml`), pengujian otomatis dijalankan secara native pada runner Ubuntu ber-PHP 8.3.

---

## 2. Panduan Instalasi & Setup Awal

### Langkah 1: Kloning Repositori & Masuk ke Branch Kerja
```bash
git clone https://github.com/EvilAekt/MAGKOM.git
cd MAGKOM
git checkout main
```

### Langkah 2: Konfigurasi Environment File
```bash
cp .env.example .env
```
Sesuaikan konfigurasi koneksi basis data lokal pada berkas `.env`:
```env
APP_NAME=MagKom
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=magkom_db
DB_USERNAME=root
DB_PASSWORD=
```

### Langkah 3: Pemasangan Dependensi Backend & Kunci Aplikasi
```bash
composer install --optimize-autoloader
php artisan key:generate
```

### Langkah 4: Migrasi Basis Data & Seeding Master Data
```bash
php artisan migrate --seed
```
*Catatan*: Seeder akan mengisi data master bidang, jurusan, pemetaan bidang-jurusan, 12 parameter sistem operasional, 13 izin modul RBAC (`PermissionSeeder`), master persyaratan berkas (`JenisDokumenSeeder`), serta akun pegawai awal.

### Langkah 5: Pemasangan Dependensi Frontend & Kompilasi Aset Vite
```bash
npm ci
npm run build
```

### Langkah 6: Pembuatan Tautan Simbolik Direktori Storage
```bash
php artisan storage:link
```

---

## 3. Menjalankan Server Pengembangan

Untuk menjalankan server lokal lengkap:

1. **Terminal 1 — Laravel Application Server:**
   ```bash
   php artisan serve --port=8080
   ```
2. **Terminal 2 — Vite Hot Module Replacement (HMR):**
   ```bash
   npm run dev
   ```

Aplikasi dapat dibuka melalui peramban pada alamat `http://localhost:8080`.

---

## 4. Quality Gates & Standardisasi Kode

MAGKOM menerapkan 4 lapisan quality gates sebelum kode diizinkan untuk di-commit atau dibuatkan Pull Request:

### 4.1 Pemeriksaan Standar Penulisan Kode (Laravel Pint)
Seluruh kode PHP wajib mematuhi standar PSR-12 / Laravel Pint:
```bash
# Memeriksa kepatuhan kode tanpa memodifikasi berkas
./vendor/bin/pint --test

# Memperbaiki formatting otomatis
./vendor/bin/pint
```

### 4.2 Analisis Statis Tipe Data (Larastan / PHPStan)
Larastan dikonfigurasi dengan alokasi memori 1G untuk memastikan tipe data presisi dan bebas dari potensi bug runtime:
```bash
./vendor/bin/phpstan analyse --memory-limit=1G
```

### 4.3 Pengujian Otomatis (PHPUnit 10)
Pengujian otomatis menggunakan basis data SQLite in-memory yang terisolasi penuh (`:memory:`) sesuai konfigurasi [phpunit.xml](../phpunit.xml):
```bash
# Menjalankan seluruh test suite otomatis
php artisan test

# Menjalankan test suite spesifik
php artisan test tests/Feature/QuotaConcurrencyTest.php
```

### 4.4 Verifikasi Build Frontend
Memastikan aset CSS dan JavaScript modular terkompilasi tanpa error:
```bash
npm run build
```

### 4.5 Konvensi Pembuatan Action & Query Baru
Untuk menjaga controller tetap ringkas (*Skinny Controllers*) dan mematuhi prinsip *Single Responsibility Principle (SRP)*:
1. **Kelas Aksi Mutasi (`app/Actions/`)**:
   - Penamaan: `App\Actions\{Role}\{Domain}\{Verb}{Noun}Action.php` (misal: `App\Actions\Admin\Penempatan\AssignPesertaAction.php`).
   - Wajib mengekspos public method `execute(...)` dengan type hinting PHP 8.3 ketat.
   - Bertanggung jawab membungkus mutasi data dalam `DB::transaction(...)` dan memanggil `AuditService` untuk rekam jejak audit forensik.
2. **Kelas Query Terspesialisasi (`app/Queries/`)**:
   - Penamaan: `App\Queries\{Role}\{Domain}\{Noun}{Type}Query.php` (misal: `App\Queries\Admin\Pendaftaran\PendaftaranIndexQuery.php`).
   - Wajib mengekspos method `get(...)` atau `paginate(...)`.
   - Mengisolasi pemfilteran parameter request, pencarian teks, pengurutan, dan eager loading relasi untuk mencegah N+1 query problem.

---

## 5. Hub Diagnostik Server & Disaster Recovery

SuperAdmin dilengkapi dengan antarmuka telemetri dan pemulihan bencana mandiri pada panel web:

### 5.1 Empat Aksi Diagnostik Server (`/superadmin/health-monitor`)
1. **Security Sweep** (`POST /superadmin/health-monitor/security-sweep`): Menjalankan audit berkas privat, verifikasi izin direktori, dan deteksi konfigurasi debug berbahaya.
2. **Optimize Database** (`POST /superadmin/health-monitor/optimize-db`): Menganalisis indeks tabel, defragmentasi ruang penyimpanan MySQL, dan verifikasi relasi kunci asing.
3. **Clear System Cache** (`POST /superadmin/health-monitor/clear-cache`): Membersihkan cache konfigurasi, cache rute, dan cache template Blade yang kedaluwarsa.
4. **Test Storage Write** (`POST /superadmin/health-monitor/test-storage`): Menguji izin baca-tulis pada disk privat `storage/app/private/`.

### 5.2 Pusat Cadangan Basis Data / Disaster Recovery (`/superadmin/backup`)
- **Snapshot Instan**: Membuat cadangan basis data terkompresi `.sql.gz` dengan penamaan berbasis waktu dan penghitungan checksum SHA-256 otomatis.
- **Unduh Privat**: Berkas cadangan hanya dapat diunduh melalui rute berizin SuperAdmin dengan validasi anti-path traversal.
- **Pemulihan Cepat**: Memulihkan skema dan data instan dari berkas arsip yang terverifikasi utuh.

---

## 6. Aturan Perubahan Kode Sumber (Development Guardrails)

1. **Dilarang Menulis CSS/JS di Blade**: Seluruh stylesheet baru wajib berada di `resources/css/pages/` dan script interaktif di `resources/js/pages/`.
2. **Dilarang Menambah Inline Event Handler**: Jangan gunakan `onclick="..."` di elemen Blade. Gunakan atribut HTML5 `data-*` dan *event delegation* di file JavaScript.
3. **Penerimaan Kuota Selalu Atomik**: Seluruh perubahan kuota harus diapit transaksi database dengan `lockForUpdate()`.
4. **SuperAdmin Read-Only pada Fungsi Pembimbing**: Dilarang membuat rute atau tombol mutasi yang memungkinkan SuperAdmin membuat tugas, mereview progres, atau mengubah logbook mentee.
5. **Preservasi Pekerjaan Lokal**: Jangan menjalankan perintah destruktif seperti `git reset --hard`, `git clean -fd`, atau `git checkout .` yang dapat menghapus pekerjaan lokal developer lain.
