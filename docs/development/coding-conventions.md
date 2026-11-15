# Konvensi Penulisan Kode (Coding Conventions)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | Standar penulisan PHP, arsitektur Blade, CSS tokens, JavaScript ES Modules, pengujian |
| Referensi Implementasi | `CONTRIBUTING.md`, `phpstan.neon`, `vite.config.js` |

---

## 1. Standar Penulisan Backend (PHP 8.3 & Laravel 10)

### 1.1 Penegakan Tipe Data Ketat (Strict Typing)
Setiap berkas PHP baru wajib menyertakan deklarasi `declare(strict_types=1);` di baris pertama setelah tag pembuka `<?php`:

```php
<?php

declare(strict_types=1);

namespace App\Actions\Admin\Penempatan;
```

### 1.2 Type Hinting Eksplisit
Seluruh parameter method dan nilai kembalian (*return type*) wajib didefinisikan secara eksplisit:

```php
// CONTOH BENAR
public function execute(int $pendaftaranId, int $bidangId, int $pembimbingId): Penempatan
{
    // Logika mutasi
}

// CONTOH SALAH - Hindari parameter tanpa tipe atau tanpa return type
public function execute($pendaftaranId, $bidangId, $pembimbingId)
{
    // Logika mutasi
}
```

### 1.3 Batasan Tanggung Jawab Controller (Skinny Controllers)
Controller hanya bertugas menerima request HTTP dan memetakan respons:
- Panjang rata-rata method controller dibatasi maksimal 30–50 baris kode.
- Dilarang menulis raw SQL query (`DB::raw(...)` atau perakitan string SQL manual).
- Dilarang mengeksekusi `DB::beginTransaction()` atau `lockForUpdate()` langsung di dalam controller. Pindahkan ke kelas Action atau Service.

### 1.4 Konvensi Kelas Action (`app/Actions/`)
- Satu kelas Action hanya memiliki **satu tanggung jawab tunggal** (*Single Responsibility Principle*).
- Menyediakan satu method eksekusi utama bernama `execute()`.
- Wajib memanggil `AuditService` jika operasi tersebut mengubah data penting pada pendaftaran, penempatan, kuota, atau akun pengguna.

```php
namespace App\Actions\Admin\Penempatan;

use App\Models\Penempatan;
use App\Services\AuditService;
use App\Enums\TipeAuditEnum;

class AssignPesertaAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function execute(int $pendaftaranId, int $bidangId, int $pembimbingId): Penempatan
    {
        // Eksekusi mutasi
    }
}
```

### 1.5 Konvensi Model Eloquent
- Seluruh model wajib mendeklarasikan nama tabel `$table` secara eksplisit.
- Seluruh model wajib mendefinisikan array `$fillable` secara eksplisit untuk mencegah kerentanan Mass Assignment. **Dilarang keras** menggunakan `$guarded = []`.
- Seluruh relasi wajib memiliki return type yang jelas (`BelongsTo`, `HasMany`, `BelongsToMany`).
- Gunakan atribut `$casts` untuk enumerasi PHP (`RoleEnum`, `StatusPendaftaranEnum`, dll.) dan format tanggal.

---

## 2. Standar Antarmuka Frontend (Blade & Vite)

### 2.1 Aturan Mutlak Zero Inline pada Blade
Blade hanya berfungsi sebagai lapisan presentasi semantik HTML:
- **DILARANG** menulis tag `<style>` di dalam berkas `.blade.php`.
- **DILARANG** menulis tag `<script>` di dalam berkas `.blade.php`.
- **DILARANG** menggunakan inline event handlers (misalnya: `onclick="..."`, `onchange="..."`, `onsubmit="..."`).
- **DILARANG** menambahkan inline styling statis (misalnya: `style="color: red; margin-top: 10px;"`). Gunakan class CSS.

### 2.2 Penggunaan CSS Tokens (DS-MagKom)
- Seluruh pewarnaan wajib mengacu pada variabel token resmi di `resources/css/base/variables.css`:
  - `--magkom-primary-navy` (`#02275D`)
  - `--magkom-royal-blue` (`#01347C`)
  - `--magkom-gold-yellow` (`#FFC300`)
  - `--magkom-dark-text` (`#212529`)
  - `--magkom-neutral-gray` (`#AAAAAA`)
- Dilarang melakukan hardcode warna HEX acak di file CSS jika token yang setara telah tersedia.

### 2.3 Konvensi JavaScript (Vanilla ES Modules)
- Tulis modul menggunakan sintaks ES2022 (`import` / `export`).
- Pisahkan modul interaktivitas per halaman ke dalam direktori `resources/js/pages/{domain}/`.
- Daftarkan berkas JavaScript baru pada array `input` di `vite.config.js`.
- Gunakan `addEventListener` terstruktur dan cegah multiple attachment listener.

---

## 3. Standar Pengujian Otomatis (Testing Conventions)

- Seluruh pengujian fitur ditempatkan di `tests/Feature/`.
- Beri nama method pengujian yang deskriptif dan mencerminkan skenario bisnis yang diuji:
  ```php
  // CONTOH BENAR
  public function test_peserta_dapat_membatalkan_tugas_yang_belum_dinilai(): void
  
  public function test_logbook_dengan_tanggal_lebih_dari_h3_ditolak(): void
  ```
- Terapkan pola **Arrange - Act - Assert (AAA)** dalam penyusunan blok tes.
- Hindari ketergantungan antar-test: Setiap test harus dapat berjalan independen tanpa mengandalkan urutan eksekusi suite.
