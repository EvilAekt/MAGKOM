# 📋 SENIOR DEVELOPER TASK BRIEF / PRD
## Standarisasi Master Data & Rekap Berkas Persyaratan Magang: 4 Berkas SMK & 4 Berkas Kampus (Perguruan Tinggi)

- **Tanggal Pembuatan:** 09 November 2026  
- **Penulis:** Senior Backend & UI Architect MagKom  
- **Target Pelaksana:** Junior Developer / AI Implementation Model (Gemini 3.8 Flash / Claude Haiku / Antigravity Agent)  
- **Status:** 🟢 **COMPLETED / VERIFIED (ALL QUALITY GATES PASSED)**  
- **Kompatibilitas:** Laravel 10.x, PHP 8.3.x, DS-MagKom Industrial Standard, OWASP Top 10  
- **Dokumen Target:**
  - [`database/seeders/JenisDokumenSeeder.php`](file:///g:/MAGKOM/database/seeders/JenisDokumenSeeder.php)
  - [`database/seeders/ProductionRealisticDataSeeder.php`](file:///g:/MAGKOM/database/seeders/ProductionRealisticDataSeeder.php)
  - [`resources/views/superadmin/arsip/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/show.blade.php)
  - [`tests/Feature/PesertaPendaftaranFlowTest.php`](file:///g:/MAGKOM/tests/Feature/PesertaPendaftaranFlowTest.php)

---

## 🎯 1. 📌 LATAR BELAKANG & TUJUAN BISNIS (Business Context)

### 1.1 Masalah Saat Ini (Current Inconsistency)
Sebelumnya, sistem MagKom memecah master data `JenisDokumen` menjadi 3 jenjang dengan nama-nama yang berbeda tipis dan membingungkan:
- **Jalur SMK:** Menggunakan nama berkas *"Surat Permohonan PKL/Magang dari Kepala Sekolah"*, *"Curriculum Vitae (CV) & Portofolio Keahlian"*, *"Salinan Kartu Pelajar"*, dan *"Surat Persetujuan Orang Tua / Wali"*.
- **Jalur D3 vs S1:** Jenjang D3 menggunakan *"Surat Pengantar dari Fakultas/Prodi"* dan *"Proposal Kegiatan Magang"*, sedangkan S1 menggunakan *"Surat Pengantar dari Dekanat/Fakultas"* dan *"Proposal Rencana Kegiatan Magang & Riset"*.

Perbedaan nama berkas antara D3 dan S1 ini memicu redundansi nama, kebingungan pemohon pendaftar kampus, serta menyulitkan verifikator dan SuperAdmin saat memeriksa rekap berkas alumni.

### 1.2 Kebijakan Baru (Golden Business Rules)
Pemerintah Kabupaten Sleman melalui Dinas Kominfo menetapkan standarisasi berkas administrasi magang secara baku menjadi **2 kelompok besar (masing-masing tepat 4 dokumen)**:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 1. KELOMPOK SMK (Siswa Sekolah Menengah Kejuruan) — Tepat 4 Berkas:                   │
│    1. Surat Permohonan PKL dari Sekolah (kode: surat_pengantar)                        │
│    2. Curriculum Vitae (CV) & Tautan Portofolio Proyek (kode: cv)                      │
│    3. Salinan Kartu Pelajar (kode: kartu_pelajar)                                      │
│    4. Surat Pengantar dari Orang Tua (kode: surat_ortu)                                │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 2. KELOMPOK KAMPUS (Mahasiswa D3 & S1 / Perguruan Tinggi) — Tepat 4 Berkas:           │
│    1. Surat Pengantar Magang Resmi dari Dekanat / Fakultas (kode: surat_pengantar)     │
│    2. Curriculum Vitae (CV) & Tautan Portofolio Proyek (kode: cv)                      │
│    3. Transkrip Nilai Akademik Sementara (kode: transkrip)                             │
│    4. Proposal Rencana Kegiatan Magang (kode: proposal)                                │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

Dengan standarisasi ini, pelamar dari kampus (baik Diploma maupun Sarjana) memiliki persyaratan berkas yang seragam, sedangkan siswa SMK memiliki berkas legalitas sekolah dan orang tua yang jelas.

---

## 🎯 2. 📋 TUJUAN & ACCEPTANCE CRITERIA (AC)

Junior Developer / AI Pelaksana wajib memenuhi seluruh kriteria penerimaan berikut:

- [x] **AC-1 (Standarisasi 4 Berkas SMK):** Master data dokumen untuk jenjang `smk` diperbarui menjadi 4 nama resmi:
  1. `Surat Permohonan PKL dari Sekolah` (kode: `surat_pengantar`, wajib: `true`)
  2. `Curriculum Vitae (CV) & Tautan Portofolio Proyek` (kode: `cv`, wajib: `true`)
  3. `Salinan Kartu Pelajar` (kode: `kartu_pelajar`, wajib: `true`)
  4. `Surat Pengantar dari Orang Tua` (kode: `surat_ortu`, wajib: `false` / opsional)
- [x] **AC-2 (Standarisasi 4 Berkas Kampus):** Master data dokumen untuk jenjang perguruan tinggi (`d3` dan `s1`) diseragamkan menjadi 4 nama resmi yang identik:
  1. `Surat Pengantar Magang Resmi dari Dekanat / Fakultas` (kode: `surat_pengantar`, wajib: `true`)
  2. `Curriculum Vitae (CV) & Tautan Portofolio Proyek` (kode: `cv`, wajib: `true`)
  3. `Transkrip Nilai Akademik Sementara` (kode: `transkrip`, wajib: `true`)
  4. `Proposal Rencana Kegiatan Magang` (kode: `proposal`, wajib: `true`)
- [x] **AC-3 (Penyelarasan Seeder & Database Eksisting):** Seeder `JenisDokumenSeeder` diperbarui, dan nama dokumen yang sudah ada di database diperbarui secara otomatis menggunakan kueri `updateOrCreate` berbasis kombinasi unik `[kode, jenjang]`.
- [x] **AC-4 (Integritas Rekap Arsip Alumni SuperAdmin):**
  - Pada halaman Detail Arsip (`superadmin/arsip/show.blade.php`), alumni berjenjang SMK hanya menampilkan 4 berkas SMK.
  - Alumni berjenjang Kampus (D3 atau S1) hanya menampilkan 4 berkas Kampus.
  - Counter tab pill dan badge total berkas tepat bernilai `4 Berkas Terdata`.
- [x] **AC-5 (Kelulusan Automated Test Suite):** Seluruh test pendaftaran (`PesertaPendaftaranFlowTest`) dan test SuperAdmin tetap lolos hijau 100% tanpa error.

---

## 🏗️ 3. 🗺️ INVENTARIS FILE & PEMETAAN DAMPAK (Impact Matrix)

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 1. MASTER DATA SEEDER                                                                  │
│    database/seeders/JenisDokumenSeeder.php                                             │
│    -> Perbarui array 4 berkas SMK dan 4 berkas Kampus (D3 & S1)                        │
│                                                                                        │
│ 2. SEEDER DATA DUMMY PRODUKSI                                                          │
│    database/seeders/ProductionRealisticDataSeeder.php                                  │
│    -> Pastikan penamaan file dummy di seeder selaras dengan nama master data baru      │
│                                                                                        │
│ 3. BLADE VIEW PRESENTASI DETAIL ARSIP                                                  │
│    resources/views/superadmin/arsip/show.blade.php                                     │
│    -> Pastikan filter jenjang SMK vs Kampus (D3/S1) bekerja konsisten                  │
│                                                                                        │
│ 4. TEST SUITE REGRESI                                                                  │
│    tests/Feature/PesertaPendaftaranFlowTest.php                                        │
│    -> Perbarui assertion nama dokumen SMK agar selaras dengan nama baru                │
│    tests/Feature/AdminLaporanDanArsipTest.php & DeltaDashboardEnhancementTest.php      │
│    -> Verifikasi akhir tidak ada regresi                                               │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🛠️ 4. 📝 PANDUAN TEKNIS EKSEKUSI TAHAP DEMI TAHAP (Step-by-Step Implementation)

### 🔹 LANGKAH 1: Perbarui `JenisDokumenSeeder.php`
**Berkas:** [`database/seeders/JenisDokumenSeeder.php`](file:///g:/MAGKOM/database/seeders/JenisDokumenSeeder.php)

Perbarui array `$dokumenList` menjadi:

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JenjangEnum;
use App\Models\JenisDokumen;
use Illuminate\Database\Seeder;

class JenisDokumenSeeder extends Seeder
{
    public function run(): void
    {
        $dokumenList = [
            // =========================================================================
            // 1. KELOMPOK SMK (4 DOKUMEN RESMI)
            // =========================================================================
            [
                'kode' => 'surat_pengantar',
                'nama' => 'Surat Permohonan PKL dari Sekolah',
                'jenjang' => JenjangEnum::Smk,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'cv',
                'nama' => 'Curriculum Vitae (CV) & Tautan Portofolio Proyek',
                'jenjang' => JenjangEnum::Smk,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'kartu_pelajar',
                'nama' => 'Salinan Kartu Pelajar',
                'jenjang' => JenjangEnum::Smk,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'surat_ortu',
                'nama' => 'Surat Pengantar dari Orang Tua',
                'jenjang' => JenjangEnum::Smk,
                'wajib' => false,
                'aktif' => true,
            ],

            // =========================================================================
            // 2. KELOMPOK KAMPUS - D3 (4 DOKUMEN RESMI)
            // =========================================================================
            [
                'kode' => 'surat_pengantar',
                'nama' => 'Surat Pengantar Magang Resmi dari Dekanat / Fakultas',
                'jenjang' => JenjangEnum::D3,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'cv',
                'nama' => 'Curriculum Vitae (CV) & Tautan Portofolio Proyek',
                'jenjang' => JenjangEnum::D3,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'transkrip',
                'nama' => 'Transkrip Nilai Akademik Sementara',
                'jenjang' => JenjangEnum::D3,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'proposal',
                'nama' => 'Proposal Rencana Kegiatan Magang',
                'jenjang' => JenjangEnum::D3,
                'wajib' => true,
                'aktif' => true,
            ],

            // =========================================================================
            // 3. KELOMPOK KAMPUS - S1 (4 DOKUMEN RESMI)
            // =========================================================================
            [
                'kode' => 'surat_pengantar',
                'nama' => 'Surat Pengantar Magang Resmi dari Dekanat / Fakultas',
                'jenjang' => JenjangEnum::S1,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'cv',
                'nama' => 'Curriculum Vitae (CV) & Tautan Portofolio Proyek',
                'jenjang' => JenjangEnum::S1,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'transkrip',
                'nama' => 'Transkrip Nilai Akademik Sementara',
                'jenjang' => JenjangEnum::S1,
                'wajib' => true,
                'aktif' => true,
            ],
            [
                'kode' => 'proposal',
                'nama' => 'Proposal Rencana Kegiatan Magang',
                'jenjang' => JenjangEnum::S1,
                'wajib' => true,
                'aktif' => true,
            ],
        ];

        foreach ($dokumenList as $item) {
            JenisDokumen::updateOrCreate(
                [
                    'kode' => $item['kode'],
                    'jenjang' => $item['jenjang']->value,
                ],
                [
                    'nama' => $item['nama'],
                    'wajib' => $item['wajib'],
                    'aktif' => $item['aktif'],
                ]
            );
        }
    }
}
```

---

### 🔹 LANGKAH 2: Eksekusi Seeder untuk Memperbarui Master Data Database
Jalankan seeder melalui CLI resmi sistem:
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan db:seed --class=JenisDokumenSeeder
```
*Hasil:* 12 baris master data di tabel `jenis_dokumen` diperbarui namanya tanpa mengubah ID, kode, maupun relasi dokumen pendaftaran yang sudah terunggah.

---

### 🔹 LANGKAH 3: Pastikan Kompatibilitas Filter di `show.blade.php`
**Berkas:** [`resources/views/superadmin/arsip/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/show.blade.php)

Pastikan logika filter jenjang mengenali kelompok SMK vs Kampus:
```blade
@php
    $jenjangPeserta = $profil?->jenjang ?? $penempatan->pendaftaran?->user?->profil?->jenjang;
    $rawDokumen = $penempatan->pendaftaran?->dokumen ?? collect();
    
    // Filter dokumen yang relevan sesuai jenjang peserta:
    // - Jika SMK: ambil dokumen dengan jenisDokumen->jenjang === 'smk'
    // - Jika Kampus (D3/S1): ambil dokumen dengan jenisDokumen->jenjang === 's1' atau 'd3'
    $filteredDokumen = $rawDokumen->filter(function ($dok) use ($jenjangPeserta) {
        if (! $jenjangPeserta || ! $dok->jenisDokumen) {
            return true;
        }
        
        $jenjangDok = $dok->jenisDokumen->jenjang?->value ?? $dok->jenisDokumen->jenjang;
        $jenjangVal = $jenjangPeserta instanceof \App\Enums\JenjangEnum ? $jenjangPeserta->value : $jenjangPeserta;
        
        if ($jenjangVal === 'smk') {
            return $jenjangDok === 'smk';
        }
        
        // Kampus (D3 dan S1)
        return in_array($jenjangDok, ['s1', 'd3'], true);
    });

    $dokumenList = $filteredDokumen->isNotEmpty() ? $filteredDokumen : $rawDokumen;
@endphp
```
Dengan filter ini, alumni yang berasal dari perguruan tinggi (baik D3 maupun S1) akan menampilkan tepat 4 berkas kampus, dan alumni SMK menampilkan tepat 4 berkas SMK.

---

### 🔹 LANGKAH 4: Selaraskan Test Suite di `PesertaPendaftaranFlowTest.php`
**Berkas:** [`tests/Feature/PesertaPendaftaranFlowTest.php`](file:///g:/MAGKOM/tests/Feature/PesertaPendaftaranFlowTest.php)

Pada baris 152-154, ubah assertion nama dokumen SMK agar selaras dengan nama master data baru:
```php
// SEBELUM:
$response->assertSee('Surat Permohonan PKL/Magang dari Kepala Sekolah');
$response->assertSee('Curriculum Vitae (CV) & Portofolio Keahlian');
$response->assertSee('Salinan Kartu Pelajar');

// SESUDAH:
$response->assertSee('Surat Permohonan PKL dari Sekolah');
$response->assertSee('Curriculum Vitae (CV) & Tautan Portofolio Proyek');
$response->assertSee('Salinan Kartu Pelajar');
```

---

## ⚡ 5. 🧪 PANDUAN QUALITY GATES & VERIFIKASI (Testing Suite)

Jalankan perintah verifikasi resmi sistem menggunakan **PHP 8.3** sistem:

```powershell
# 1. Bersihkan Cache Template
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan view:clear
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan config:clear

# 2. Jalankan Seeder Pembaruan Master Data
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan db:seed --class=JenisDokumenSeeder

# 3. Jalankan Feature Tests Pendaftaran & Arsip
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/PesertaPendaftaranFlowTest.php tests/Feature/AdminLaporanDanArsipTest.php tests/Feature/DeltaDashboardEnhancementTest.php

# 4. Jalankan Seluruh Test Suite SuperAdmin
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=SuperAdmin

# 5. Audit Format Kode dengan Pint
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/pint --test

# 6. Static Analysis dengan PHPStan
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/phpstan analyse --memory-limit=1G

# 7. Kompilasi Ulang Aset Vite
npm run build

# 8. Perbarui Knowledge Graph
graphify update .
```

---

## ✅ 6. 📋 CHECKLIST PENYELESAIAN (Definition of Done)

- [x] `JenisDokumenSeeder.php` diperbarui dengan 4 nama dokumen resmi SMK dan 4 nama dokumen resmi Kampus (D3 & S1).
- [x] Command `artisan db:seed --class=JenisDokumenSeeder` berhasil dieksekusi dan nama 12 master data di database tersinkronisasi.
- [x] Alur pendaftaran peserta SMK menampilkan 4 berkas: `Surat Permohonan PKL dari Sekolah`, `Curriculum Vitae (CV) & Tautan Portofolio Proyek`, `Salinan Kartu Pelajar`, dan `Surat Pengantar dari Orang Tua`.
- [x] Alur pendaftaran peserta Kampus menampilkan 4 berkas: `Surat Pengantar Magang Resmi dari Dekanat / Fakultas`, `Curriculum Vitae (CV) & Tautan Portofolio Proyek`, `Transkrip Nilai Akademik Sementara`, dan `Proposal Rencana Kegiatan Magang`.
- [x] Halaman Detail Arsip SuperAdmin (`show.blade.php`) secara konsisten menampilkan tepat 4 berkas sesuai jenjang alumni.
- [x] Test assertion di `PesertaPendaftaranFlowTest.php` diselaraskan dan seluruh automated test suite lolos hijau 100%.
- [x] Pint, PHPStan, dan Vite build lolos tanpa error.

---

> **Pesan Senior Architect:**  
> Standarisasi ini sangat krusial agar tidak ada disparitas atau perbedaan sebutan dokumen antara mahasiswa Diploma (D3) dan Sarjana (S1), sekaligus memberikan kejelasan hukum permohonan PKL bagi siswa SMK di lingkungan Pemerintah Kabupaten Sleman.

