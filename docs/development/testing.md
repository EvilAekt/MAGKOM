# Arsitektur Pengujian Otomatis (Testing Guide)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | PHPUnit 10, konfigurasi SQLite memory & MySQL 8.4 test, 1.020 kasus uji, pengujian keamanan, 2FA, backup & konkurensi |
| Referensi Implementasi | `phpunit.xml`, `phpunit.mysql.xml`, `tests/TestCase.php`, `tests/Feature/` |

---

## 1. Pendahuluan

Otomatisasi pengujian adalah pilar penjaminan kualitas (*Quality Assurance*) utama dalam sistem MAGKOM. Seluruh aturan bisnis (BR-01 s/d BR-19), mitigasi ancaman keamanan siber (SEC-01 s/d SEC-06), dan pencegahan regresi diuji melalui test suite otomatis berbasis **PHPUnit 10.x**.

Berdasarkan audit dan hardening produksi terbaru, sistem memiliki:
- **1.020 Kasus Uji Otomatis (Test Methods)** dengan **6.540 assertions** (100% lulus / 0 error)
- **150 Berkas Pengujian** terbagi atas:
  - `tests/Feature/`: 133 berkas
  - `tests/Feature/Auth/`: 2 berkas
  - `tests/Feature/LandingPage/`: 8 berkas
  - `tests/Feature/SuperAdmin/`: 5 berkas
  - `tests/Unit/`: 2 berkas
- **Durasi Eksekusi**: Seluruh test suite dapat dijalankan secara instan dan efisien menggunakan basis data SQLite `:memory:` atau MySQL 8.4 `magkom_test` via `phpunit.mysql.xml`.

---

## 2. Konfigurasi Lingkungan Pengujian (`phpunit.xml`)

Pengujian otomatis dikonfigurasi untuk berjalan secara terisolasi tanpa mempengaruhi basis data fisik MySQL pengembang:

```xml
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="APP_KEY" value="base64:2fl+Ktvkfl+Fuz4Qp/1SeSgqqN7DVBhhY+GwYqS1nvg="/>
    <env name="BCRYPT_ROUNDS" value="4"/>
    <env name="CACHE_DRIVER" value="array"/>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
    <env name="MAIL_MAILER" value="array"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
    <env name="SESSION_DRIVER" value="array"/>
    <env name="TELESCOPE_ENABLED" value="false"/>
</php>
```

### 2.1 Konfigurasi Pengujian MySQL Concurrency (`phpunit.mysql.xml`)

Untuk memverifikasi mekanisme atomik tingkat basis data seperti `lockForUpdate()`, isolasi transaksi ACID, dan pencegahan race condition kuota pendaftaran secara native, repositori menyediakan konfigurasi pengujian khusus MySQL (`phpunit.mysql.xml`):

```bash
# Menjalankan pengujian konkurensi di atas MySQL terisolasi (magkom_test) dengan PHP 8.3
php vendor/bin/phpunit -c phpunit.mysql.xml tests/Feature/QuotaConcurrencyTest.php
php vendor/bin/phpunit -c phpunit.mysql.xml tests/Feature/RealtimeStreamConcurrencyTest.php
```

> [!NOTE]
> Database `magkom_test` bersifat khusus testing dan terpisah sepenuhnya dari basis data runtime. Cache pengujian PHPUnit (`.phpunit.result.cache`) dapat dibersihkan secara aman apabila terjadi perubahan nama kelas atau refaktor test:
> ```powershell
> Remove-Item .phpunit.result.cache -ErrorAction SilentlyContinue
> ```

- **In-Memory SQLite**: Migrasi dijalankan langsung pada RAM, menghasilkan waktu pengujian yang sangat cepat.
- **Bcrypt Rounds 4**: Mempercepat proses hashing kata sandi selama pembuatan data uji.
- **Array Mailer**: Menangkap pengiriman email dalam memori tanpa mengirimkan email fisik keluar.

---

## 3. Komponen Pengujian Inti (Core Test Suites)

Berikut adalah daftar test suite utama yang memverifikasi integritas arsitektur dan kepatuhan sistem:

### 3.1 Integritas Pondasi & Keamanan (Core Foundation & Security)
- **`CoreFoundationTest`**: Menguji casting role enum pada model User, integritas fillable, pencegahan akses unauthorized via middleware role, dan sifat append-only tabel `audit_log` (menolak update dan delete).
- **`SecurityHeadersProductionTest`**: Memverifikasi keberadaan header Content Security Policy (CSP), serta memindai seluruh **185 file Blade** untuk menjamin **0 inline `<script>`** dan **0 inline `on*=` event handler**.
- **`SeederProductionSafetyTest`**: Memverifikasi bahwa seeder data pengujian (dummy) otomatis diblokir saat dijalankan pada environment produksi, serta mewajibkan keberadaan variabel `SEED_SUPERADMIN_PASSWORD`.
- **`PackagingSecurityTest`**: Memverifikasi mekanisme rilis terisolasi dari artefak sensitif (`.env`, `.mcp.json`, credentials, backup) serta pengecualian `.gitattributes`.
- **`TrustProxiesSecurityTest`**: Menguji konfigurasi trusted proxy dinamis melalui `config/trustedproxy.php` (tanpa direct `env()`) dan pencegahan manipulasi pemalsuan alamat IP klien (*client IP spoofing*).
- **`TugasDeskripsiSecurityTest`**: Memverifikasi sanitasi rekursif XSS SafeHtmlSanitizer dari serangan nested tag unwrap bypass, XSS atribut, javascript URI, serta sanitasi deskripsi tugas.
- **`ProductionEmailConfigurationTest`**: Memvalidasi kesiapan konfigurasi SMTP produksi (`email:validate-config`) dan runtime blocking pengiriman log mailer di environment production.
- **`RealtimeStreamConcurrencyTest`**: Menguji mekanisme nonaktif Server-Sent Events (SSE) via variabel `REALTIME_SSE_ENABLED=false` dan integritas streaming payload.
- **`ConfigurationCompatibilityTest`**: Menguji kompatibilitas parameter cache/session, pembuatan tabel antrean database (`jobs`, `job_batches`), serta perlindungan header Host spoofing via `TrustHosts`.
- **`ProductionCommandSafetyTest`**: Memverifikasi pemblokiran perintah destruktif/pengujian developer (`TestResetPasswordCommand`, `SeedDummyPesertaCommand`, `GenerateSampleDocumentsCommand`) saat aplikasi berada di environment production.
- **`AuthSecurityReviewTest`**: Menguji pencegahan pengambilalihan akun staf via Google OAuth, penolakan registrasi email kosong, pembatasan reset password khusus peserta, dan pengujian kekuatan sandi.
- **`LoginAggregateThrottleTest`**: Menguji mitigasi serangan password spraying dan pembatasan login multi-tier per akun dan per IP address.

### 3.2 Konkurensi & Alokasi Kuota Atomik (BR-05)
- **`QuotaConcurrencyTest`**: Memverifikasi transaksi atomik pemotongan kuota jurusan dengan `lockForUpdate()`, memastikan tidak terjadi kuota minus saat beberapa pendaftar mendaftar secara simultan.

### 3.3 Penempatan Dua Tahap & Beban Bimbingan (BR-07 & BR-08)
- **`AdminPenempatanDuaTahapTest`**: Menguji alur pemisahan verifikasi berkas dan plotting penempatan, memastikan pendaftar lolos masuk ke antrean belum ditempatkan.
- **`AdminPenempatanArchitectureAndSecurityTest`**: Menguji pembatasan beban bimbingan maksimal 20 mentee per pembimbing ASN, serta rotasi pembimbing massal (*bulk reassign*).

### 3.4 Pelaksanaan Magang & Fleksibilitas Penugasan (BR-09, BR-18, BR-19)
- **`TugasLogbookTest`**: Menguji penolakan validasi terhadap tanggal logbook yang lebih lampau dari H-3 kerja.
- **`PesertaTugasBatalKirimTest`**: Menguji alur pembatalan pengiriman tugas (*unsubmit*) oleh peserta selama tugas belum direview pembimbing, dan penolakan unsubmit jika telah disetujui.
- **`PembimbingTugasArchitectureSafetyTest`**: Menguji penerbitan tugas batch classroom serentak ke multi-mentee sekaligus.
- **`PembimbingLaporanKinerjaExportArchitectureTest`**: Menguji sanitasi anti-formula injection pada ekspor berkas spreadsheet CSV dan XLSX kinerja bimbingan.

### 3.5 Tata Kelola Sistem & Dynamic RBAC
- **`PredikatBatasValidationTest`**: Menguji batas validasi 12 parameter operasional aplikasi di panel SuperAdmin.
- **`SuperAdminRolePermissionTest`**: Menguji isolasi hak akses matriks perizinan 13 modul sistem dan perlindungan guard `EnsureSuperAdminReadOnly`.
- **`EndToEndUatTest`**: Menguji skenario menyeluruh dari registrasi awal pendaftar, seleksi berkas, penempatan bidang, pengerjaan tugas, pengisian logbook, penyelesaian magang, hingga masa tenggang akun.

### 3.6 Pengujian Pengerasan Produksi & Keamanan (Production Hardening Suites)
- **`SuperAdminTwoFactorAuthTest`**: Menguji seluruh alur 2FA TOTP RFC 6238, intersepsi tantangan login, verifikasi single-use recovery code, dan emergency reset via CLI.
- **`SuperAdminPasswordPolicyTest`**: Menguji kebijakan kata sandi 12+ karakter kompleks (mixed case, angka, simbol) untuk SuperAdmin dan transisi aman tanpa lockout akun lama.
- **`SvgUploadSecurityTest`**: Menguji proteksi stored XSS pada upload CMS dengan penolakan file SVG dan verifikasi magic bytes gambar raster.
- **`BackupEncryptionAndRestoreTest`**: Menguji enkripsi snapshot AES-256-CBC dengan autentikasi HMAC-SHA256, integritas header `MAGKOM_ENC_v1`, dan prosedur dekripsi pemulihan.
- **`AsyncEmailQueueTest`**: Menguji pengiriman notifikasi verifikasi email dan lupa sandi melalui queue worker `database` tanpa menahan HTTP request.
- **`RealtimeStreamConcurrencyTest`**: Menguji perlindungan worker PHP-FPM dengan konfigurasi nonaktif SSE dan graceful polling fallback `GET /realtime/poll`.
- **`SafeHtmlSanitizerPhp83Test`**: Menguji kompatibilitas PHP 8.3 pada sanitizer HTML tanpa parameter deprecated `HTML-ENTITIES`, mempertahankan karakter UTF-8, aksen, dan emoji.
- **`PackagingSecurityTest`**: Menguji pengecualian berkas rahasia (`.env`, log, dokumen privat) dari skrip pengemas rilis.

---

## 4. Cara Menjalankan Pengujian

### Menjalankan Seluruh Rangkaian Tes
```bash
php artisan test
```

### Menjalankan Pengujian Berdasarkan Filter Kelas
```bash
php artisan test --filter=SecurityHeadersProductionTest
php artisan test --filter=QuotaConcurrencyTest
php artisan test --filter=AdminPenempatanDuaTahapTest
```

### Menjalankan Pengujian Spesifik Berkas
```bash
php artisan test tests/Feature/CoreFoundationTest.php
```

---

## 5. Pedoman Menulis Kasus Uji Baru

Saat menambahkan kasus uji baru di `tests/Feature/`, terapkan standar berikut:

```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\Penempatan;
use App\Enums\RoleEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContohFiturBaruTest extends TestCase
{
    use RefreshDatabase;

    public function test_peserta_dapat_mengakses_fitur_terkait(): void
    {
        // 1. Arrange: Siapkan data pengguna dan entitas uji
        $peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'is_active' => true,
        ]);

        // 2. Act: Lakukan pemanggilan rute HTTP
        $response = $this->actingAs($peserta)
            ->get(route('peserta.dashboard'));

        // 3. Assert: Verifikasi respon HTTP dan kondisi basis data
        $response->assertOk();
        $response->assertViewIs('peserta.dashboard');
    }
}
```
