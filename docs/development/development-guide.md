# Panduan Lengkap Pengembangan (Development Guide)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | Alur kerja pengembangan, penambahan fitur, quality gates, seeder safety |
| Referensi Implementasi | `CONTRIBUTING.md`, `phpunit.xml`, `phpstan.neon` |

---

## 1. Alur Kerja Git & Percabangan (Git Workflow)

1. **Branch Utama (`main`)**:
   - `main` adalah branch produksi stabil yang siap rilis.
   - Dilarang keras melakukan commit atau push langsung ke `main`.
2. **Konvensi Penamaan Branch**:
   - Fitur baru: `feat/nama-fitur` (misal: `feat/alokasi-slot-kemitraan`)
   - Perbaikan bug: `fix/deskripsi-masalah` (misal: `fix/validasi-batas-h3-logbook`)
   - Penataan ulang kode: `refactor/nama-modul` (misal: `refactor/aksi-penempatan`)
   - Pembaruan dokumentasi: `docs/cakupan-pembaruan`
3. **Format Pesan Commit (Conventional Commits)**:
   - `feat(domain): deskripsi fitur baru`
   - `fix(domain): deskripsi perbaikan bug`
   - `refactor(domain): penyempurnaan struktur tanpa ubah logika`
   - `test(domain): penambahan kasus uji otomatis`
   - `style(domain): perapian format kode atau CSS tokens`
   - `docs(domain): perbaikan dokumentasi teknis`

---

## 2. Empat Gerbang Kualitas Wajib (The 4 Quality Gates)

Setiap pengembang wajib menjalankan dan meloloskan 4 pemeriksaan lokal berikut sebelum mengajukan *Pull Request* ke branch utama:

### Gerbang 1: Pengujian Otomatis (PHPUnit)
Seluruh 942 kasus uji otomatis wajib berstatus lulus (100% hijau):

```bash
php artisan test
```

Untuk menjalankan kelompok pengujian spesifik saat proses pengerjaan fitur:
```bash
# Menjalankan satu berkas pengujian spesifik
php artisan test tests/Feature/AdminPenempatanDuaTahapTest.php

# Menjalankan pengujian dengan filter nama method atau class
php artisan test --filter=QuotaConcurrencyTest
```

### Gerbang 2: Analisis Statis (PHPStan / Larastan)
Memverifikasi konsistensi tipe data dan kepatuhan method level 2:

```bash
./vendor/bin/phpstan analyse --memory-limit=1G
```
Wajib menghasilkan: `[OK] No errors`.

### Gerbang 3: Pemeriksaan Standar Gaya Kode (Laravel Pint)
Memverifikasi kepatuhan format penulisan kode PHP standar PSR-12:

```bash
# Memeriksa kepatuhan format tanpa mengubah berkas
./vendor/bin/pint --test

# Memperbaiki format kode secara otomatis jika ditemukan deviasi
./vendor/bin/pint
```

### Gerbang 4: Kompilasi Aset Frontend (Vite Build)
Memastikan seluruh 87 entri aset frontend terkompilasi tanpa error sintaks atau missing import:

```bash
npm run build
```

---

## 3. Prosedur Menambahkan Fitur Baru

Untuk menjaga konsistensi arsitektur, ikuti tahapan pengembangan berikut:

### Langkah A: Perubahan Skema Basis Data (Jika Diperlukan)
1. Buat berkas migrasi baru menggunakan Artisan:
   ```bash
   php artisan make:migration nama_tabel_atau_perubahan --create=nama_tabel
   ```
2. Tetapkan foreign key constraints, tipe data kolom, dan nilai default secara eksplisit.
3. Selalu sediakan implementasi method `down()` yang membersihkan perubahan pada migrasi `up()`.
4. Definisikan atribut `$fillable` dan relasi Eloquent pada Model terkait. **Dilarang keras** menggunakan `$guarded = []`.

### Langkah B: Pembuatan Logika Bisnis (Actions & Queries)
1. Tempatkan mutasi data pada kelas Action di `app/Actions/{Domain}/`.
2. Tempatkan kueri kompleks pada kelas Query di `app/Queries/{Domain}/`.
3. Jika melibatkan operasi multi-tabel atau pemotongan kuota, bungkus dalam `DB::transaction()` dengan `lockForUpdate()`.
4. Panggil `AuditService` untuk mencatat mutasi data ke tabel `audit_log`.

### Langkah C: Validasi Input dan Keamanan
1. Buat Form Request di `app/Http/Requests/` untuk menangani validasi input.
2. Definisikan otorisasi objek berbasis Policy di `app/Policies/` untuk mencegah celah IDOR.

### Langkah D: Antarmuka Blade & Aset Vite
1. Buat template Blade di `resources/views/{domain}/`.
2. Patuhi aturan mutlak: **Zero Inline CSS/JS**.
3. Buat file stylesheet terpisah di `resources/css/pages/{domain}/`.
4. Buat modul JavaScript di `resources/js/pages/{domain}/`.
5. Daftarkan entri baru pada `vite.config.js` di dalam array `input`.

### Langkah E: Otomatisasi Kasus Uji (Automated Tests)
1. Buat Feature Test di `tests/Feature/`.
2. Tulis skenario pengujian sukses (*happy path*), skenario validasi gagal (HTTP 422), dan skenario otorisasi ditolak (HTTP 403).
3. Jalankan `php artisan test` untuk memastikan pengujian baru lulus dan tidak memicu regresi pada suite pengujian lain.

---

## 4. Tata Kelola Data Awal (Seeders) & Keamanan Produksi

MAGKOM menerapkan isolasi ketat pada berkas seeder untuk mencegah data pengujian masuk ke server produksi:

```php
// Prinsip Seeder Production Safety (SeederProductionSafetyTest)
if (app()->environment('production')) {
    // Seeder data dummy (TestingUserSeeder, DummyPesertaSeeder) otomatis dibatalkan
    if (empty(env('SEED_SUPERADMIN_PASSWORD'))) {
        throw new RuntimeException('Variabel SEED_SUPERADMIN_PASSWORD wajib didefinisikan di .env production');
    }
}
```

- **Data Master Wajib**: `JurusanSeeder`, `BidangSeeder`, `JenisDokumenSeeder`, `PengaturanSeeder`, `PermissionSeeder`. (Aman dijalankan di production).
- **Data Pengujian**: `TestingUserSeeder`, `DummyDuaPuluhPesertaSeeder`, `PurgeAndSeedCleanGmailAccountsSeeder`. (Hanya diizinkan di environment `local` dan `testing`).
