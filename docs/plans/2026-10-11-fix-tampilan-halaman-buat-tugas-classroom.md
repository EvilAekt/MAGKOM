# Plan: Perbaikan Tampilan Halaman Buat Tugas Pembimbing (Integrasi Layout Dashboard & Design System Sleman Blue)

- **Tanggal**: 2026-10-11
- **Status**: Selesai (Completed & Verified)
- **Author**: Antigravity (Senior Frontend Architect / web-planning)
- **Target File**:
  - Blade View: [`resources/views/pembimbing/tugas/create.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/create.blade.php)
  - Test Suite: [`tests/Feature/ClassroomAssignmentWorkspaceTest.php`](file:///g:/MAGKOM/tests/Feature/ClassroomAssignmentWorkspaceTest.php)

---

## 1. Analisis Akar Masalah (Root Cause Analysis)

Setelah melakukan inspeksi visual langsung di peramban, ditemukan 3 faktor penyebab utama mengapa tampilan halaman `/pembimbing/tugas/create` terlihat aneh, rusak, dan tidak selaras:

1. **Kesalahan Induk Layout (`@extends('layouts.app')` vs `@extends('layouts.dashboard')`)**:
   - Seluruh halaman pembimbing (`/pembimbing/dashboard`, `/pembimbing/tugas`, `/pembimbing/tugas/{id}`, `/pembimbing/peserta`) menggunakan layout induk `@extends('layouts.dashboard')` yang memiliki sidebar navigasi resmi, top header pembimbing, dan container `.dashboard-content`.
   - Halaman `create.blade.php` sebelumnya keliru menggunakan `@extends('layouts.app')`, yaitu layout publik beranda/landing page luar yang memunculkan footer publik Pemkab Sleman dan menghilangkan sidebar portal pembimbing.
2. **Ukuran Ikon SVG Membengkak (Missing Explicit SVG Attributes)**:
   - Ikon-ikon SVG di dalam template hanya mengandalkan utility Tailwind `w-4 h-4` atau `w-12 h-12`. Tanpa atribut eksplisit `width="16" height="16"`, peramban merender SVG dengan dimensi bawaan (*intrinsic default*) yang raksasa sehingga mendistorsi form menjadi sangat buruk dan berantakan.
3. **Inkonsistensi Styling dengan Design System MagKom (DS-MagKom)**:
   - Modul pembimbing MagKom menggunakan CSS variable tokens (seperti `var(--blue-700)`, `var(--neutral-100)`, `var(--neutral-200)`) dan inline styling yang presisi (seperti di `pembimbing/dashboard.blade.php` dan `pembimbing/tugas/show.blade.php`).
   - Halaman `create.blade.php` perlu diselaraskan dengan standar styling token MagKom agar tampil bersih, mewah, proporsional, dan menyatu sempurna dengan sidebar.

---

## 2. Asumsi & Rencana Desain Perbaikan

### Asumsi:
- Halaman tetap mengadopsi konsep **Two-Column Classroom Workspace** (Area kerja utama di kiri, Panel pengaturan di kanan) yang lapang, namun sekarang berada di dalam bingkai `.dashboard-content` dengan sidebar pembimbing tetap aktif.
- Fungsionalitas backend, validasi form request, dan controller tetap sama (`PembimbingTugasController@create` dan `store`).

---

## 3. Pendekatan Desain & Arsitektur

### Spesifikasi Visual Baru:
1. **Induk Template**: `@extends('layouts.dashboard')`
2. **Container**: `<div class="dashboard-content" style="...">`
3. **Breadcrumb & Header**:
   - Breadcrumb: `Dashboard / Tugas Peserta / Buat Tugas Baru`
   - Judul Halaman: `Buat Tugas Baru (Classroom Workspace)`
   - Tombol Top Action: Tombol Batal & Tombol `Tugaskan Sekarang` (warna `#0641CC` Sleman Blue)
4. **Grid 2 Kolom Proporsional**:
   - **Kolom Kiri (Main Canvas - 65%)**:
     - Card 1: Input Judul Tugas (font 1.15rem, tebal, placeholder jelas) dan Textarea Petunjuk Pengerjaan (tinggi 9 baris, font nyaman).
     - Card 2: Attachment Hub (Input Tautan Referensi dengan ikon rantai terukur `width="16" height="16"`, Dropzone Upload Berkas dengan ikon awan terukur `width="24" height="24"`, dan kartu preview file terpilih).
   - **Kolom Kanan (Sidebar Settings - 35%)**:
     - Card Pengaturan Sticky:
       - Dropdown Mentee Bimbingan + Live Info Box (kampus & tanggal selesai).
       - Input Kategori dengan datalist autocomplete.
       - Segmented Pills Tingkat Kompleksitas (Rendah [Hijau], Sedang [Kuning], Tinggi [Ungu]).
       - Datepicker Batas Waktu dengan auto-limit tanggal selesai magang.
       - Kotak Tips Pembimbing Magang Diskominfo Sleman.
5. **Standarisasi Seluruh Ikon SVG**:
   - Seluruh tag `<svg>` menyertakan atribut eksplisit `width="X"` dan `height="X"` agar tidak pernah melar di peramban apa pun.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/pembimbing/tugas/create.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/create.blade.php) | **Modify** | Ganti layout ke `layouts.dashboard`, terapkan container `.dashboard-content`, perbaiki dimensi seluruh SVG, dan gunakan token styling MagKom. |
| [`tests/Feature/ClassroomAssignmentWorkspaceTest.php`](file:///g:/MAGKOM/tests/Feature/ClassroomAssignmentWorkspaceTest.php) | **Verify** | Test suite tetap lulus 100% (4/4 passed, 23 assertions). |

---

## 5. Langkah Implementasi Bertahap

- [x] **Langkah 1**: Perbarui [`resources/views/pembimbing/tugas/create.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/create.blade.php):
  - Ubah `@extends('layouts.app')` menjadi `@extends('layouts.dashboard')`.
  - Bungkus konten dalam `<div class="dashboard-content" style="...">`.
  - Buat Top Header dengan breadcrumb standar portal pembimbing.
  - Perbaiki seluruh tag `<svg>` dengan menyematkan `width="X" height="X"` eksplisit.
  - Terapkan layout 2 kolom responsif dengan styling card modern Sleman Blue `#0641CC`.
- [x] **Langkah 2**: Jalankan pengujian otomatis:
  - `php artisan test --filter="ClassroomAssignmentWorkspaceTest"` (4 passed, 23 assertions)
  - `php artisan test --filter="RichTaskAssignmentTest|PembimbingDashboardEnhancementTest|PembimbingFullJobDeskTest"` (21 passed, 101 assertions)
- [x] **Langkah 3**: Verifikasi visual langsung via browser subagent:
  - Buka `/pembimbing/tugas/create` dan ambil screenshot.
  - Hasil screenshot membuktikan sidebar pembimbing aktif di kiri, tidak ada SVG raksasa, layout 2 kolom proporsional, dan desain bersih selaras portal MagKom.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Halaman `create.blade.php` terintegrasi mulus dengan `layouts.dashboard` (sidebar pembimbing aktif).
- [x] Tidak ada lagi ikon SVG raksasa yang membengkak merusak tata letak.
- [x] Desain 2 kolom proporsional, responsif, dan konsisten dengan palet Sleman Blue MagKom.
- [x] Dropzone berkas dan tautan referensi berfungsi interaktif.
- [x] Seluruh pengujian otomatis lulus 100% (**25 passed, 124 assertions**).
- [x] Hasil visual di peramban terkonfirmasi rapi, modern, dan nyaman dilihat.
