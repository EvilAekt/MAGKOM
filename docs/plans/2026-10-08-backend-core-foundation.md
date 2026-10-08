# Plan: Implementasi Backend Gelombang 0 (Fondasi & Arsitektur Inti)

- **Tanggal**: 2026-10-08
- **Status**: Completed (Lulus Semua Pengujian)
- **Author**: Antigravity (`web-planning`)
- **Referensi**: [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md), [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md), [`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md)

---

## 1. Tujuan
Membangun fondasi backend awal (*Core Backend Foundation*) aplikasi MagKom menggunakan **Laravel 10.x** dan **PHP 8.2+ / 8.3** sesuai dengan panduan Gelombang 0 pada Roadmap. Tahap ini mencakup kerangka aplikasi, 13 backed enums, model dan migrasi database awal, arsitektur `AuditService` & `AuditedService`, middleware RBAC & keamanan, serta master seeder awal.

---

## 2. Asumsi & Lingkungan
- **Runtime PHP**: PHP 8.2.12 terpasang di sistem (`F:\XAMPEEK\php\php.exe`), kompatibel dengan platform Laravel 10.
- **Dependency Manager**: Composer v2.10.2 terpasang.
- **Database**: MySQL/MariaDB aktif secara lokal di port 3306 (database `magkom_db`).
- **Workspace**: Root direktori `g:/MAGKOM` sudah memiliki file dokumen `docs/`, `.env.example`, dan `package.json`. Inisialisasi scaffolding Laravel 10 akan dilakukan tanpa menghapus file dokumen yang sudah ada.

---

## 3. Pendekatan Desain & Arsitektur
- **Opsi Terpilih**: Monolit Laravel 10.x Server-Rendered (Blade) dengan arsitektur **Route → Middleware → Controller (tipis) → Form Request → Service Layer → Eloquent Model**.
- **Alasan Pemilihan**:
  1. Memenuhi keputusan terkunci D-01 dan ADR-01 serta ADR-03.
  2. Lapisan `AuditedService` sebagai base class memastikan seluruh eksekusi bisnis tercatat ke `audit_log` secara otomatis tanpa membebani controller.
  3. Menggunakan Backed Enum PHP untuk menjaga integritas tipe data status di database tanpa *magic string*.
  4. Pengamanan ketat: kolom `role` dilarang masuk ke `$fillable`, password di-hash Bcrypt, dan header HTTP diinjeksi via middleware.

---

## 4. Perubahan & Pembuatan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `composer.json` & struktur Laravel 10 | Create | Scaffolding paket Laravel 10.x resmi dengan dependencies |
| `.env` & `.env.example` | Create / Update | Konfigurasi database MySQL, timezone `Asia/Jakarta`, locale `id` |
| `app/Enums/*.php` (13 file) | Create | Definisi seluruh backed enum sesuai PRD bab 8.1 |
| `app/Models/User.php` | Modify | Update casts enum `RoleEnum`, flag `status_aktif`, `must_change_password` |
| `app/Models/Jurusan.php` | Create | Model master jurusan |
| `app/Models/Bidang.php` | Create | Model master unit kerja bidang |
| `app/Models/Rubrik.php` | Create | Model master rubrik penilaian |
| `app/Models/Pengaturan.php` | Create | Model konfigurasi sistem dinamis |
| `app/Models/AuditLog.php` | Create | Model append-only untuk jejak audit |
| `database/migrations/*` | Create | Migrasi untuk `users`, `jurusan`, `bidang`, `rubrik`, `pengaturan`, `audit_log` |
| `app/Services/AuditService.php` | Create | Service logging dengan auto-sanitasi data sensitif |
| `app/Services/AuditedService.php` | Create | Abstract class pembungkus eksekusi service method |
| `app/Http/Middleware/EnsureUserHasRole.php` | Create | Middleware pengecekan peran (RBAC) |
| `app/Http/Middleware/EnsurePasswordChanged.php` | Create | Middleware paksa ganti password pertama staf |
| `app/Http/Middleware/SecurityHeaders.php` | Create | Middleware injeksi header HTTP security |
| `app/Http/Middleware/LogAuditTrail.php` | Create | Middleware terminable untuk pencatatan akses data |
| `app/Http/Kernel.php` | Modify | Registrasi middleware alias di Laravel 10 |
| `database/seeders/*` | Create | Seeder untuk master data (6 jurusan, 5 rubrik, 8 pengaturan, 1 superadmin) |
| `tests/Feature/CoreFoundationTest.php` | Create | Pengujian otomatis verifikasi enum, RBAC, dan audit trail |

---

## 5. Langkah Implementasi

- [x] **Langkah 1**: Inisialisasi scaffolding Laravel 10.x dan dependensi Composer yang dibutuhkan (`laravel/breeze`, `barryvdh/laravel-dompdf`).
- [x] **Langkah 2**: Konfigurasi environment (`.env`) untuk koneksi MySQL `magkom_db`, zona waktu `Asia/Jakarta`, dan konfigurasi aplikasi.
- [x] **Langkah 3**: Buat 13 Backed Enum PHP di `app/Enums/`:
  - `RoleEnum.php`
  - `JenjangEnum.php`
  - `JalurEnum.php`
  - `SumberPenempatanEnum.php`
  - `StatusKetersediaanEnum.php`
  - `StatusPendaftaranEnum.php`
  - `StatusPenempatanEnum.php`
  - `StatusTugasEnum.php`
  - `KompleksitasEnum.php`
  - `StatusReviewProgresEnum.php`
  - `StatusLogbookEnum.php`
  - `StatusPenilaianEnum.php`
  - `TipeAuditEnum.php`
- [x] **Langkah 4**: Buat migrasi database fondasi:
  - `0001_01_01_000000_create_users_table.php` (disesuaikan dengan skema `users` di TDD bab 6.3)
  - `create_master_tables.php` (`jurusan`, `bidang`, `rubrik`, `pengaturan`)
  - `create_audit_log_table.php` (skema append-only dengan kolom `detail` JSON dan indeks komprehensif)
- [x] **Langkah 5**: Buat Eloquent Models (`User`, `Jurusan`, `Bidang`, `Rubrik`, `Pengaturan`, `AuditLog`) lengkap dengan relasi dan casting.
- [x] **Langkah 6**: Buat `AuditService` dan `AuditedService` (pola transaksi, auto-masking kredensial, dan pencatatan eksekusi).
- [x] **Langkah 7**: Buat Middleware RBAC & Keamanan (`EnsureUserHasRole`, `EnsurePasswordChanged`, `SecurityHeaders`, `LogAuditTrail`) dan daftarkan ke `$middlewareAliases` di `app/Http/Kernel.php`.
- [x] **Langkah 8**: Buat Master Seeders (`JurusanSeeder`, `RubrikSeeder`, `PengaturanSeeder`, `SuperAdminSeeder`) dan jalankan migrasi database.
- [x] **Langkah 9**: Tulis unit/feature test di `tests/Feature/CoreFoundationTest.php` untuk memvalidasi:
  - Role check & middleware blocking (403).
  - Sanitasi AuditService (tidak membocorkan kata sandi/token).
  - Integritas master data seeder.

---

## 6. Strategi Testing
- **Automated Feature Test**:
  - `CoreFoundationTest`: Pengujian eksekusi middleware role untuk SuperAdmin, Admin, Pembimbing, dan Peserta.
  - `AuditTrailTest`: Memastikan pemanggilan method service tercatat di `audit_log` dengan data yang tersanitasi.
- **Database Verification**:
  - `php artisan migrate:fresh --seed` berhasil dijalankan tanpa violation constraint.

---

## 7. Risiko & Mitigasi
- **Konflik File Existing**: Workspace sudah memiliki `docs/`, `package.json`, dan `.env.example`. Scaffolding Composer akan dilakukan secara aman tanpa menimpa direktori dokumentasi.
- **PHP Version Warning**: Lingkungan lokal menggunakan PHP 8.2.12. Konfigurasi `composer.json` akan menggunakan platform PHP 8.2/8.3 agar kompatibel secara sempurna dengan dependensi Laravel 10.x.

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] Seluruh 13 backed enums telah terdefinisi.
- [ ] Tabel `users`, `jurusan`, `bidang`, `rubrik`, `pengaturan`, dan `audit_log` terbentuk di database via migration.
- [ ] `AuditService` aktif dan berfungsi mencatat log dengan penyensoran kredensial.
- [ ] Middleware RBAC dan security headers terdaftar di HTTP Kernel.
- [ ] Seeder awal berhasil memasukkan 6 jurusan, 5 rubrik, 8 pengaturan, dan 1 SuperAdmin.
- [ ] Automated feature test lulus 100%.
