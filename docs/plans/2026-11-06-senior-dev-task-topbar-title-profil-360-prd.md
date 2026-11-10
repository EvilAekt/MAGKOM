# SENIOR DEVELOPER TASK & TECHNICAL PRD
## Standardisasi Topbar Header: Mengubah "Beranda / Dashboard" Menjadi "Profil 360° Mentee Bimbingan"

- **Tingkat Kesulitan**: Low-Medium (Blade Architecture Standardization & Consistency)
- **Target Implementer**: Junior Developer / AI Model (Gemini 3.8 Flash / Claude Haiku / Cursor)
- **Reviewer**: Lead Senior Full-Stack Developer
- **Target Halaman**: Portal Pembimbing — `Profil 360° Mentee Bimbingan`
- **File Target Utama**:
  1. `resources/views/pembimbing/peserta/show.blade.php` (Penambahan deklarasi `@section('topbar_title')`)
  2. `tests/Feature/PembimbingPortalStandardizationTest.php` (Penambahan assertion test otomatis)
- **Lampiran Tangkapan Layar User**: `media_1793975945600_d29f9e8c.png` (Hamburger menu dengan teks `Beranda / Dashboard`)

---

## 1. Problem Statement & Root Cause Analysis (RCA)

### 1.1 Masalah yang Ditemukan (User Pain Point)
Pengguna mendapati bahwa pada bar navigasi atas (*topbar navbar*) halaman **Profil 360° Mentee Bimbingan**, teks yang muncul di sebelah ikon hamburger menu masih berupa:
```
[ ≡ ]  Beranda  /  Dashboard
```
Pengguna menghendaki teks tersebut diganti secara spesifik menjadi:
```
[ ≡ ]  Profil 360° Mentee Bimbingan
```

### 1.2 Root Cause Analysis (RCA) di Tingkat Kode
Mari telusuri arsitektur layout master di `resources/views/layouts/dashboard.blade.php` (baris 3573–3586):
```blade
@hasSection('topbar_title')
    <h1 class="topbar-title">@yield('topbar_title')</h1>
@else
    <!-- Breadcrumb Trail Fallback -->
    <nav class="breadcrumb-trail" aria-label="Breadcrumb">
        <a href="{{ route('landing') }}">{{ __('Beranda') }}</a>
        <span class="breadcrumb-separator">/</span>
        @hasSection('breadcrumbs')
            @yield('breadcrumbs')
        @else
            <span class="breadcrumb-current">{{ __('Dashboard') }}</span>
        @endif
    </nav>
@endif
```

#### Hasil Audit Seluruh Halaman Pembimbing di MagKom:
Seluruh halaman kerja pembimbing telah menerapkan `@section('topbar_title', '...')`:
- `resources/views/pembimbing/dashboard.blade.php` ➔ `@section('topbar_title', 'Dashboard Pembimbing')`
- `resources/views/pembimbing/peserta/index.blade.php` ➔ `@section('topbar_title', 'Direktori Peserta Bimbingan')`
- `resources/views/pembimbing/tugas/index.blade.php` ➔ `@section('topbar_title', 'Tugas & Progres Magang')`
- `resources/views/pembimbing/tugas/create.blade.php` ➔ `@section('topbar_title', 'Buat Tugas Baru')`
- `resources/views/pembimbing/tugas/edit.blade.php` ➔ `@section('topbar_title', 'Edit Tugas Mentee')`
- `resources/views/pembimbing/tugas/show.blade.php` ➔ `@section('topbar_title', 'Review & Evaluasi Penugasan')`
- `resources/views/pembimbing/logbook/index.blade.php` ➔ `@section('topbar_title', 'Logbook Peserta')`
- `resources/views/pembimbing/penilaian/index.blade.php` ➔ `@section('topbar_title', 'Evaluasi Rubrik & Penilaian Magang')`
- `resources/views/pembimbing/laporan-kinerja/index.blade.php` ➔ `@section('topbar_title', 'Laporan Peserta')`

**Hanya satu file** yang belum mendeklarasikan `@section('topbar_title')`, yaitu:
`resources/views/pembimbing/peserta/show.blade.php`.

Akibatnya, layout master mengeksekusi blok `@else` dan merender fallback: `Beranda / Dashboard`.

---

## 2. Solusi Teknis & Rencana Perubahan (Architecture Solution)

### 2.1 Solusi di Blade View
Tambahkan deklarasi `@section('topbar_title', 'Profil 360° Mentee Bimbingan')` pada bagian header file `resources/views/pembimbing/peserta/show.blade.php`.

Dengan satu baris deklarasi ini:
1. Kondisi `@hasSection('topbar_title')` pada layout master bernilai `true`.
2. Navbar atas akan merender:
   ```html
   <h1 class="topbar-title">Profil 360° Mentee Bimbingan</h1>
   ```
3. Fallback breadcrumb `Beranda / Dashboard` otomatis tidak lagi dirender.
4. Tampilan navbar atas selaras 100% dengan 9 modul pembimbing lainnya di sistem MagKom.

---

## 3. Instruksi Langkah Demi Langkah untuk Junior Developer / AI

### Langkah 1: Modifikasi File Blade (`resources/views/pembimbing/peserta/show.blade.php`)

Buka file `resources/views/pembimbing/peserta/show.blade.php` dan tambahkan `@section('topbar_title', 'Profil 360° Mentee Bimbingan')` di bawah `@section('title')`:

```diff
--- a/resources/views/pembimbing/peserta/show.blade.php
+++ b/resources/views/pembimbing/peserta/show.blade.php
@@ -1,6 +1,7 @@
 @extends('layouts.dashboard')
 
 @section('title', 'Profil Mentee: ' . ($penempatan->peserta?->nama ?? 'Peserta') . ' - Portal MagKom')
+@section('topbar_title', 'Profil 360° Mentee Bimbingan')
 
 @push('styles')
     @vite(['resources/css/pages/pembimbing/peserta-show.css'])
```

### Langkah 2: Tambahkan Assertion Otomatis pada Test Suite (`tests/Feature/PembimbingPortalStandardizationTest.php`)

Agar perubahan ini teruji secara otomatis dan tidak mengalami regresi di kemudian hari, buka `tests/Feature/PembimbingPortalStandardizationTest.php` pada method `test_pembimbing_peserta_show_menggunakan_desain_classroom_banner_tanpa_status_penilaian()`:

Tambahkan assertion untuk `topbar-title` dan pastikan `breadcrumb-trail` fallback tidak muncul:

```php
// Verifikasi Topbar Header Navbar Atas
$response->assertSee('class="topbar-title"', false);
$response->assertSee('Profil 360° Mentee Bimbingan');
$response->assertDontSee('class="breadcrumb-trail"', false);
```

---

## 4. Batasan & Larangan Keras (Guardrails)

- [ ] **JANGAN MENGUBAH LAYOUT UTAMA**: Jangan memodifikasi `resources/views/layouts/dashboard.blade.php` karena layout tersebut dipakai oleh seluruh role pengguna (SuperAdmin, Admin, Pembimbing, Peserta).
- [ ] **NO INLINE STYLING**: Jangan menambahkan atribut `style="..."` inline baru.
- [ ] **NO BREAKING CHANGES**: Pertahankan seluruh variabel controller yang sudah ada.

---

## 5. Rencana Verifikasi & Quality Gates

Jalankan perintah pengujian berikut di terminal untuk memastikan implementasi valid dan aman:

```bash
# 1. Jalankan Feature Test Spesifik
php artisan test --filter=PembimbingPortalStandardizationTest

# 2. Jalankan Feature Test Pembimbing Lainnya
php artisan test --filter=PembimbingFullJobDeskTest
php artisan test --filter=PembimbingPortalFeatureTest

# 3. Linter & Static Analysis
./vendor/bin/pint --test
./vendor/bin/phpstan analyse --memory-limit=1G
```

---

## 6. Checklist Definition of Done (DoD)

- [ ] `@section('topbar_title', 'Profil 360° Mentee Bimbingan')` telah terpasang di `resources/views/pembimbing/peserta/show.blade.php`.
- [ ] Teks `Beranda / Dashboard` di sebelah tombol hamburger tidak lagi muncul.
- [ ] Teks `Profil 360° Mentee Bimbingan` tampil rapi dengan class `.topbar-title`.
- [ ] Pengujian otomatis di `PembimbingPortalStandardizationTest` lolos 100% (*passed*).
- [ ] `Laravel Pint` dan `PHPStan` lolos tanpa error.

