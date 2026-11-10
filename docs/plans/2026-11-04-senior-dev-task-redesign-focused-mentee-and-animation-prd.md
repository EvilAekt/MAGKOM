# Senior Developer Task Specification & PRD
## Redesign Focused Mentee Header, Modern Tab Filter, & Fluid JavaScript Animations

**Project**: Portal Pembimbing MAGKOM — Modul Tugas & Progres Magang  
**Target Audience**: Junior Developer / AI Engine (Gemini 3.8 Flash)  
**Tipe Dokumen**: Product Requirement Document (PRD) & Senior Dev Implementation Blueprint  
**Status**: Ready for Review & Implementation

---

## 1. Executive Summary & Problem Breakdown

Berdasarkan evaluasi antarmuka dan feedback pengguna pada tahap Focused View sebelumnya, terdapat 5 area yang harus disempurnakan:

```
[Area 1: Tombol Kembali] ──> Pindah ke sisi kiri sejajar dengan identitas peserta, bersihkan duplikasi panah ("← ←").
[Area 2: Header Mentee]  ──> Hapus teks kaku "Tugas & Progres:", pindahkan Avatar [NP], Nama, & Institusi langsung ke Header Utama.
[Area 3: Duplikasi Kartu]──> Hilangkan chip statistik (6 Tugas, 2 Selesai, 1 Reviu) dari kartu & sembunyikan header kartu duplikat di mode fokus.
[Area 4: Filter Status]  ──> Redesign Tab Segmented Pills ("Semua Tugas", "Ditugaskan", "Selesai", "Perlu Evaluasi") menjadi modern & polished.
[Area 5: Animasi & Typo] ──> Tambahkan animasi transisi JS/CSS (staggered cascade, smooth state transition) dan rapikan tipografi.
```

---

## 2. Visual Wireframe: Before vs After

### 2.1 Kondisi Saat Ini (Masalah Visual)
```
+---------------------------------------------------------------------------------------------+
| [ ← ← Kembali ke Semua Mentee ]   <-- (Ada duplikasi ikon & teks panah, posisi mengambang)  |
| Judul: Tugas & Progres: Naufal Pratama  SMK Negeri 2 Depok Sleman <-- (Kaku & ada teks duplikat) |
| [Semua Tugas 6] [Ditugaskan 2] [Selesai 2] [⚠️ Perlu Evaluasi 1]                            |
+---------------------------------------------------------------------------------------------+
| [NP] Naufal Pratama   SMK Negeri 2 Depok Sleman     [6 Tugas] [2 Selesai] [1 Reviu]   v     | <-- (Redundan!)
| ------------------------------------------------------------------------------------------- |
| [📋] Tugas 1 ...                                                                            |
| [✓]  Tugas 2 ...                                                                            |
+---------------------------------------------------------------------------------------------+
```

### 2.2 Desain Baru yang Diharapkan (Bersih, Terpadu, Modern)
```
+---------------------------------------------------------------------------------------------+
| [ ← Kembali ] | [NP] Naufal Pratama                                            [Kartu][Tabel] |
|                 SMK Negeri 2 Depok Sleman                                                   |
+---------------------------------------------------------------------------------------------+
| [ (Semua Tugas) 6 ]  [ Ditugaskan 2 ]  [ Selesai 2 ]  [ ⚠️ Perlu Evaluasi 1 ]               |
+---------------------------------------------------------------------------------------------+
| [📋] Penyusunan Draf Laporan Akhir Magang & Portofolio   Tenggat: 24 Jan 2027  [Ditugaskan] [Detail >] |
| [✓]  Orientasi & Pemahaman Arsitektur Sistem EGOV         Tenggat: 11 Nov 2026  [Selesai]    [Detail >] |
| [⚠️] Pengerjaan Modul Teknis: Aplikasi Informatika        Tenggat: 08 Nov 2026  [Reviu]      [Detail >] |
| [📋] Slicing & Integrasi Frontend Dashboard Peserta      Tenggat: 06 Nov 2026  [Ditugaskan] [Detail >] |
| [📋] Uji Fungsional & Penyusunan Dokumen Laporan         Tenggat: 16 Nov 2026  [Ditugaskan] [Detail >] |
| [✓]  Analisis Kebutuhan Sistem & Pemetaan API MagKom     Tenggat: 30 Okt 2026  [Selesai]    [Detail >] |
+---------------------------------------------------------------------------------------------+
```
*(Tidak ada lagi teks kaku "Tugas & Progres:", tidak ada lagi header kartu redundan di bawah, dan tidak ada lagi chip statistik yang membingungkan).*

---

## 3. Detail Kebutuhan Perubahan (Step-by-Step Task)

### Task 1: Redesign Header Terpadu di Sisi Kiri (Blade & CSS)
1. **Navigasi Terintegrasi**:
   - Posisikan tombol kembali di sisi paling kiri header.
   - Hilangkan teks panah duplikat `&larr;` di dalam HTML (cukup gunakan ikon SVG panah yang tajam dan presisi).
   - Label tombol: `"Kembali"` atau `"Semua Mentee"`.
2. **Identitas Mentee Terpadu**:
   - Tepat di sebelah kanan tombol kembali (diberi garis pemisah vertikal elegan):
     - Tampilkan lingkaran avatar inisial mentee `[NP]` (ukuran 42px).
     - Nama mentee: `Naufal Pratama` (font-size: 1.25rem, font-weight: 800, color: `#0F172A`).
     - Sub-label institusi: `SMK Negeri 2 Depok Sleman` (font-size: 0.875rem, font-weight: 500, color: `#64748B`).
   - Teks kaku `"Tugas & Progres:"` **dihapus total**.

### Task 2: Eliminasi Chip Statistik & Header Kartu Duplikat
1. **Penghapusan Chip Statistik**:
   - Di mode fokus, chip `6 Tugas   ✓ 2 Selesai   ⚠️ 1 Reviu` dihilangkan.
   - Di kartu mentee biasa saat mode master, tampilkan badge ringkas atau sembunyikan jika tab status sudah cukup jelas.
2. **Penyembunyian Header Kartu Duplikat**:
   - Saat sebuah kartu mentee aktif dalam mode fokus (`.mentee-classroom-card.is-active-focus`), header tombol accordionnya disembunyikan (`display: none;`), karena identitas mentee sudah berada di header panel utama.
   - Konten drawer tugas (`.classroom-tasks-list`) langsung tampil sebagai konten utama panel.

### Task 3: Redesign Modern Segmented Pill Tabs (Filter Status)
1. **Visual Style**:
   - Container track: background `#F8FAFC` dengan border `1px solid #E2E8F0`, rounded `9999px`, padding `4px`.
   - Tab Item (`.tab-pill`):
     - Padding: `7px 16px`, font-size `0.85rem`, font-weight `600`, color `#475569`.
     - Border radius: `9999px`.
     - Transisi: `all 0.2s cubic-bezier(0.16, 1, 0.3, 1)`.
   - Tab Aktif (`.tab-pill.active`):
     - Background: Solid Sleman Blue `#01347C`.
     - Text: Putih `#FFFFFF`.
     - Shadow: `0 2px 10px rgba(1, 52, 124, 0.28)`.
     - Badge angka di dalam tab aktif: background `rgba(255, 255, 255, 0.22)`, text putih, bulat proporsional.
   - Tab Tidak Aktif:
     - Badge angka: background `#E2E8F0`, text `#475569`.
     - Hover: background `#EDF2F7`, color `#01347C`.
   - Badge "⚠️ Perlu Evaluasi":
     - Warna khusus amber warning lembut (`#FEF3C7` / `#B45309`) saat tidak aktif.

### Task 4: Animasi Fluida Berbasis JavaScript & CSS
1. **Transisi Masuk Focused View**:
   - Ketika pembimbing mengklik kartu mentee:
     - Header baru muncul dengan animasi `fade-in-down` (durasi 250ms).
     - Daftar tugas muncul dengan animasi **staggered cascade** (tugas ke-1 delay 0ms, tugas ke-2 delay 40ms, tugas ke-3 delay 80ms, dst.) menghasilkan efek visual modern ala Google Classroom / Linear App.
2. **Transisi Kembali ke Semua Mentee**:
   - Saat tombol kembali diklik:
     - Header kembali ke default dengan transisi halus.
     - Kartu-kartu mentee kembali muncul secara lembut (`fade-in-up`).
3. **Hover & Active Micro-Interactions**:
   - Tombol kembali: ikon panah bergeser `translateX(-3px)` saat hover.
   - Baris tugas: hover `translateY(-1.5px)` dengan box shadow lembut dan aksen border `#01347C`.

### Task 5: Tipografi Refinement (Open Sans System)
1. **Keseimbangan Ukuran & Skala Tipografi**:
   - Hero Banner Title: `1.5rem` / `800` (Bold Ekstrem).
   - Focused Mentee Name: `1.25rem` / `800`.
   - Nama Peserta di Mode Master: `1.05rem` / `700`.
   - Judul Tugas di Baris Classroom: `0.9375rem` / `600` (line-height 1.4, neutral-900).
   - Metatags / Tenggat Waktu: `0.8125rem` / `600` (neutral-500, status overdue warna `#DC2626` / bold 700).

---

## 4. Technical Architecture & File Modifications

### 4.1 File: `resources/views/pembimbing/tugas/index.blade.php`
- Ubah struktur slot header x-panel:
  - Default mode: Judul *"Daftar Penugasan & Progres Mentee"*.
  - Focused mode: Flex container horizontal kiri:
    - Tombol `#btnBackToMentees` (ikon panah tunggal SVG + teks "Semua Mentee").
    - Divider vertikal.
    - Avatar initial mentee (`.header-mentee-avatar`).
    - Nama mentee (`#focusedMenteeName`) dan nama institusi (`#focusedMenteeInst`).
- Pada kartu mentee:
  - Hilangkan chip statistik `.mentee-stats-badges` dari mode fokus.
  - Sembunyikan `.mentee-card-header` pada kartu yang sedang fokus (`.is-active-focus`) agar baris tugas langsung terhubung ke header panel utama tanpa pengulangan.

### 4.2 File: `resources/css/pages/pembimbing/tugas-index.css`
- Tambahkan styling layout flex header kiri: `.mentee-unified-header`, `.header-back-col`, `.header-divider`, `.header-mentee-col`.
- Styling tombol kembali `.btn-back-mentee-modern`.
- Redesign tab pills: `.classroom-tabs-wrapper`, `.classroom-tabs`, `.tab-pill`, `.tab-pill-badge`.
- Kelas animasi CSS Keyframes: `@keyframes fadeInCascade`, `@keyframes slideInDown`, `@keyframes fadeInUp`.
- Dark mode overrides untuk semua komponen baru.

### 4.3 File: `resources/js/pages/pembimbing/tugas-index.js`
- Perbarui logika `focusOnMentee()`:
  - Perbarui avatar inisial, nama mentee, dan institusi di header terpadu sisi kiri.
  - Jalankan trigger animasi staggered cascade pada elemen `.classroom-task-row`.
- Perbarui logika `resetToAllMentees()`:
  - Kembalikan header ke judul default.
  - Jalankan animasi fade-in pada kartu-kartu mentee.
- Sinkronkan event `popstate` agar transisi animasi tetap berjalan saat navigasi tombol browser.

### 4.4 File: `tests/Feature/PembimbingFocusedMenteeTugasViewTest.php`
- Perbarui assertions test untuk memvalidasi:
  - Teks `"Tugas & Progres:"` sudah tidak ada di mode fokus.
  - Nama peserta muncul di header utama bersama avatar inisial dan institusi.
  - Tombol kembali terpasang rapi di sisi kiri.
  - Tab status filter tetap bekerja dengan benar.

---

## 5. Quality Gates Checklist

```bash
# 1. PHPUnit feature tests
php artisan test --filter=PembimbingFocusedMenteeTugasViewTest
php artisan test --filter=Tugas

# 2. PHPStan analyse
vendor/bin/phpstan analyse app/Http/Controllers/Pembimbing/TugasController.php --memory-limit=1G

# 3. Laravel Pint code formatting
vendor/bin/pint --test

# 4. Vite build check
npm run build

# 5. Graphify AST knowledge graph update
python -m graphify update .
```

---

## 6. Keputusan yang Siap Dikonfirmasi

1. **Teks pada Tombol Kembali**:
   - Pilihan: `"← Semua Mentee"` atau `"← Kembali"`. (Rekomendasi: `"← Semua Mentee"` karena lebih deskriptif dan jelas).
2. **Penghapusan Header Kartu Duplikat**:
   - Saat Naufal Pratama diklik, avatar dan namanya langsung pindah ke atas di samping tombol kembali, dan daftar tugas langsung tampil tanpa header kartu bertumpuk di bawahnya.

