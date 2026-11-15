# Arsitektur Pengujian Otomatis (Testing Guide)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | PHPUnit 10, konfigurasi SQLite memory, 942 kasus uji, pengujian keamanan & konkurensi |
| Referensi Implementasi | `phpunit.xml`, `tests/TestCase.php`, `tests/Feature/` |

---

## 1. Pendahuluan

Otomatisasi pengujian adalah pilar penjaminan kualitas (*Quality Assurance*) utama dalam sistem MAGKOM. Seluruh aturan bisnis (BR-01 s/d BR-19), mitigasi ancaman keamanan siber (SEC-01 s/d SEC-06), dan pencegahan regresi diuji melalui test suite otomatis berbasis **PHPUnit 10.x**.

Berdasarkan audit aktual, sistem memiliki:
- **942 Kasus Uji Otomatis (Test Methods)**
- **139 Berkas Pengujian** terbagi atas:
  - `tests/Feature/`: 124 berkas
  - `tests/Feature/Auth/`: 2 berkas
  - `tests/Feature/LandingPage/`: 8 berkas
  - `tests/Feature/SuperAdmin/`: 4 berkas
  - `tests/Unit/`: 1 berkas
- **Durasi Eksekusi**: Seluruh test suite dapat dijalankan secara instan dan efisien menggunakan basis data SQLite `:memory:`.

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
- **`TrustProxiesSecurityTest`**: Menguji konfigurasi trusted proxy dinamis dan pencegahan manipulasi pemalsuan alamat IP klien (*client IP spoofing*).
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
