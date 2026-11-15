# Roadmap & Panduan Arsitektur Upgrade: Laravel 10 ke Laravel 11/12

| Metadata | Keterangan |
| :--- | :--- |
| **Aplikasi** | MAGKOM (Sistem Informasi Magang Kominfo) |
| **Repository** | `EvilAekt/MAGKOM` |
| **Status EOL Laravel 10** | **End of Bug Fixes:** 6 Agustus 2024<br>**End of Security Fixes:** 4 Februari 2025 |
| **Target Framework** | Laravel 11.x (dengan kesiapan jalur ke Laravel 12.x) |
| **Kebutuhan PHP Runtime** | PHP 8.2 - 8.3 (MAGKOM saat ini sudah menggunakan PHP ^8.3) |
| **Kategori Temuan Audit** | **F-03 (P2 Medium - Confirmed)** |
| **Dokumen Status** | Final Architecture Plan (Ready for Execution Phase) |

---

## 1. Ringkasan Eksekutif & Penilaian Risiko (F-03)

### 1.1 Latar Belakang & Urgensi
Sesuai dengan kebijakan dukungan resmi Laravel (*Laravel Support Policy*), dukungan perbaikan keamanan (*security fixes*) untuk Laravel versi 10 resmi berakhir pada **4 Februari 2025**. 

Berada pada versi *End-of-Life* (EOL) menimbulkan risiko keamanan dan operasional berikut:
1. **Tidak Ada Patch Keamanan Resmi Core**: Celah keamanan baru (CVE / zero-day) pada komponen internal framework (seperti HTTP foundation, cookie encryption, query builder, auth guard) tidak lagi ditambal oleh Laravel Core Team.
2. **Ecosystem & Package Abandonment**: Package populer di ekosistem PHP/Laravel (Sanctum, Sentry, DomPDF, Larastan) secara bertahap menghentikan pengujian dan kompatibilitas dengan Laravel 10 pada rilis minor/mayor mereka berikutnya.
3. **Penyimpangan Kepatuhan Audit**: Dalam audit sistem informasi pemerintahan/publik, penggunaan framework EOL dapat dikategorikan sebagai temuan ketidakpatuhan keamanan infrastruktur perangkat lunak.

### 1.2 Kesiapan Proyek MAGKOM
Kabar baik bagi MAGKOM:
- Proyek sudah dikonfigurasi dengan `php: ^8.3` di `composer.json` dan berjalan pada PHP 8.3.6.
- Arsitektur modular yang rapi (pemisahan `app/Actions`, `app/Services`, `app/Queries`, `app/Policies`, dan `app/Support`).
- Memiliki cakupan unit & feature testing yang komprehensif (945+ tests, 6.190+ assertions) yang siap menjadi safety net otomatis saat migrasi.

---

## 2. Matriks Kompatibilitas Dependensi

Berikut adalah pemetaan dependensi `composer.json` dari versi eksisting menuju Laravel 11:

### 2.1 Production Dependencies (`require`)

| Package Name | Versi Saat Ini | Target Laravel 11 | Catatan Kompatibilitas & Perubahan |
| :--- | :--- | :--- | :--- |
| `php` | `^8.3` | `^8.2 \| ^8.3` | Sudah memenuhi syarat minimum Laravel 11 (PHP >= 8.2). |
| `laravel/framework` | `^10.0` | `^11.0` | Perubahan besar pada bootstrapping framework (`bootstrap/app.php`). |
| `laravel/sanctum` | `^3.2` | `^4.0` | Sanctum v4 menghentikan beberapa method legacy; pastikan migrasi `personal_access_tokens` tetap sinkron. |
| `laravel/socialite` | `^5.0` | `^5.16` | Kompatibel penuh; tidak ada breaking changes pada alur OAuth Google. |
| `barryvdh/laravel-dompdf` | `^2.0` | `^2.2 \| ^3.0` | Kompatibel dengan Laravel 11. Perhatikan konfigurasi font & canvas di render laporan. |
| `sentry/sentry-laravel` | `^4.29` | `^4.29` | Versi 4.29 sudah mendukung Laravel 11 secara native. |
| `guzzlehttp/guzzle` | `^7.2` | `^7.8` | Tidak ada breaking changes. |
| `laravel/tinker` | `^2.8` | `^2.9` | Minor bump. |

### 2.2 Development Dependencies (`require-dev`)

| Package Name | Versi Saat Ini | Target Laravel 11 | Catatan |
| :--- | :--- | :--- | :--- |
| `phpunit/phpunit` | `^10.0` | `^10.5 \| ^11.0` | Laravel 11 mendukung PHPUnit 10 dan 11. |
| `larastan/larastan` | `2.9` | `^2.9.2` | Menjamin static analysis PHPStan tetap 0 error. |
| `laravel/pint` | `^1.0` | `^1.15` | Format standar PSR-12 / Laravel preset. |
| `spatie/laravel-ignition` | `^2.0` | `^2.4` | Error page handler untuk local environment. |
| `nunomaduro/collision` | `^7.0` | `^8.1` | CLI error output. |
| `fakerphp/faker` | `^1.9.1` | `^1.23` | Kompatibel penuh. |
| `mockery/mockery` | `^1.4.4` | `^1.6` | Kompatibel penuh. |

---

## 3. Analisis Dampak Arsitektur (Architectural Changes)

Laravel 11 memperkenalkan struktur aplikasi yang jauh lebih ramping (*streamlined application structure*). Berikut adalah area kode MAGKOM yang terdampak beserta solusi refactoring-nya:

### 3.1 Unifikasi Kernel ke `bootstrap/app.php`
- **Sebelumnya (Laravel 10)**:
  - Middleware terdaftar di `app/Http/Kernel.php` (`$middleware`, `$middlewareGroups`, `$middlewareAliases`).
  - Scheduling didaftarkan di `app/Console/Kernel.php` method `schedule()`.
  - Exception handling diatur di `app/Exceptions/Handler.php`.
- **Pada Laravel 11**:
  - Seluruh konfigurasi middleware, routing, schedule, dan exceptions dipindahkan ke closure builder di `bootstrap/app.php`.

#### Implementasi Baru `bootstrap/app.php`:
```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global Middleware
        $middleware->append([
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\TrustProxies::class,
        ]);

        // Middleware Aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'account.active' => \App\Http\Middleware\EnsureAccountIsActive::class,
            'must_change_password' => \App\Http\Middleware\EnsurePasswordChanged::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Sentry or Custom Exception Reporters
    })->create();
```

### 3.2 Migrasi Task Scheduling ke `routes/console.php`
Pindahkan perintah penjadwalan harian (backup, sinkronisasi kuota, pembersihan stream) dari `app/Console/Kernel.php` ke `routes/console.php`:
```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:clean')->daily()->at('01:00')->withoutOverlapping();
Schedule::command('backup:run')->daily()->at('02:00')->withoutOverlapping();
Schedule::command('magkom:sync-kuota')->dailyAt('00:01')->withoutOverlapping();
```

### 3.3 Transisi Model Attribute Casting
Di Laravel 11, properti `$casts = [...]` pada Eloquent Model dianjurkan bermigrasi menjadi method `protected function casts(): array`. Ini memberikan fleksibilitas memanggil method atau class-based casts secara dinamis tanpa overhead array static.

Contoh penyesuaian pada `app/Models/User.php`:
```php
// Sebelum (Laravel 10):
protected $casts = [
    'email_verified_at' => 'datetime',
    'status_aktif' => 'boolean',
    'must_change_password' => 'boolean',
];

// Sesudah (Laravel 11):
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'status_aktif' => 'boolean',
        'must_change_password' => 'boolean',
        'password' => 'hashed',
    ];
}
```

---

## 4. Rencana Eksekusi Bertahap (Phased Execution Plan)

### Fase 1: Pra-Upgrade & Baseline Guard (Estimasi: 0.5 Hari Kerja)
1. Buat branch git terisolasi: `feature/upgrade-laravel-11`.
2. Pastikan test suite 100% lulus pada baseline saat ini:
   ```bash
   php artisan test
   ./vendor/bin/pint --test
   ./vendor/bin/phpstan analyse --memory-limit=1G
   npm run build
   ```
3. Buat backup database dan snapshot konfigurasi `.env`.

### Fase 2: Pembaruan Dependensi Composer (Estimasi: 0.5 Hari Kerja)
1. Perbarui `composer.json` dengan target versi yang telah ditetapkan pada Bab 2.
2. Jalankan pembaruan dependensi:
   ```bash
   composer update --with-all-dependencies
   ```
3. Verifikasi apakah terdapat package collision atau dependensi yang belum kompatibel.

### Fase 3: Refactoring Arsitektur & Konfigurasi (Estimasi: 1 - 1.5 Hari Kerja)
1. Migrasi `bootstrap/app.php` menggunakan builder pattern Laravel 11.
2. Migrasikan registrasi middleware kustom:
   - `SecurityHeaders`
   - `TrustProxies`
   - `RoleMiddleware`
   - `EnsureAccountIsActive`
   - `EnsurePasswordChanged`
3. Pindahkan jadwal task dari `Console/Kernel.php` ke `routes/console.php`.
4. Hapus file kernel usang (`app/Http/Kernel.php` dan `app/Console/Kernel.php`) setelah semua middleware dan schedule berhasil dipindahkan.
5. Jalankan audit model casting pada 24 model di direktori `app/Models/`.

### Fase 4: Validasi & Regression Testing (Estimasi: 1 Hari Kerja)
1. Jalankan pengujian menyeluruh:
   ```bash
   php artisan test
   ```
2. Jalankan pengujian spesifik keamanan dan middleware:
   ```bash
   php artisan test tests/Feature/SecurityHeadersProductionTest.php
   php artisan test tests/Feature/RealtimeStreamConcurrencyTest.php
   php artisan test tests/Feature/SeederProductionSafetyTest.php
   php artisan test tests/Feature/TugasDeskripsiSecurityTest.php
   php artisan test tests/Feature/TrustProxiesSecurityTest.php
   php artisan test tests/Feature/LoginAggregateThrottleTest.php
   ```
3. Jalankan static analysis dan code formatting:
   ```bash
   ./vendor/bin/phpstan analyse --memory-limit=1G
   ./vendor/bin/pint --test
   ```
4. Build aset frontend Vite untuk memastikan asset pipeline tetap utuh:
   ```bash
   npm run build
   ```

### Fase 5: Deployment Staging & Smoke Test (Estimasi: 0.5 Hari Kerja)
1. Deploy build hasil upgrade ke lingkungan Staging/UAT.
2. Lakukan smoke test untuk skenario:
   - Login SuperAdmin, Admin, Pembimbing, dan Peserta.
   - Pendaftaran peserta baru & verifikasi kuota per jurusan.
   - Penempatan peserta & review logbook/tugas.
   - Cetak laporan PDF via DomPDF.
   - SSE notification stream via EventSource.

---

## 5. Rencana Kontingensi & Rollback Plan

Jika ditemukan isu *blocker* yang tidak dapat diselesaikan dalam jangka waktu deployment:
1. **Rollback Source Code**: Checkout kembali ke branch `main` pada commit stabil terakhir sebelum upgrade.
2. **Rollback Composer**: Jalankan `composer install` untuk mengembalikan vendor dependencies ke `composer.lock` Laravel 10.
3. **Rollback Cache**: Jalankan pembersihan cache:
   ```bash
   php artisan optimize:clear
   php artisan view:clear
   php artisan config:clear
   ```
4. Verifikasi ulang kesehatan aplikasi dengan menjalankan test suite baseline.
