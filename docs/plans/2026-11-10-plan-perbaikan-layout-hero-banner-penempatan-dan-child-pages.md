# Rencana Kerja: Perbaikan Layout Hero Banner Halaman Penempatan & Child Pages

**Tanggal:** 10 November 2026  
**Status:** Selesai (Implemented)  
**Tautan Dokumen Rencana:** `docs/plans/2026-11-10-plan-perbaikan-layout-hero-banner-penempatan-dan-child-pages.md`  
**Referensi:**  
- Tangkapan Layar Masalah: `media_1794293540553_ae936a4b.png` (Hero Banner Halaman Penempatan `/admin/penempatan`)  
- File Sumber: `resources/css/pages/admin/dashboard.css`, `resources/css/components/cards.css`, `resources/views/admin/penempatan/index.blade.php`  
- Aturan Pengembangan: `AGENTS.md` (PHP 8.3, Laravel 10, Isolasi CSS Scoped, Zero Side-Effects, Zero Test Regression).

---

## 1. Root Cause Analysis (Analisis Akar Masalah)

### Temuan Investigasi:
1. **Kebocoran Selektor Global CSS (*CSS Selector Leak*)**:
   - File `resources/css/pages/admin/dashboard.css` diimpor secara global di `resources/css/app.css:20`:
     ```css
     @import './pages/admin/dashboard.css';
     ```
   - Pada `dashboard.css:109`, didefinisikan selektor hero tanpa scope:
     ```css
     .adm-hero-content {
       display: flex;
       flex-direction: column;
       align-items: flex-start;
       z-index: 1;
     }
     ```
2. **Dampak pada Halaman Penempatan (`/admin/penempatan`) dan Child Pages**:
   - Komponen Hero Banner standar pada halaman Penempatan (`resources/views/admin/penempatan/index.blade.php`) dan `resources/css/components/cards.css` dirancang memiliki 2 elemen horizontal:
     - Sisi Kiri: `.adm-hero-text` (Judul `"Alokasi Penempatan & Pembimbing Magang"` + Subjudul)
     - Sisi Kanan: `.adm-hero-actions` (Tombol `"Ekspor Rekap ∨"` dan `"+ Penempatan Jalur Khusus"`)
   - Karena selektor `.adm-hero-content` di `dashboard.css` bersifat global dan memiliki `flex-direction: column`, aturan ini menimpa layout default.
   - Akibatnya, pada tampilan desktop (layar lebar):
     - Tombol aksi `.adm-hero-actions` **turun ke bawah (*drop to new row*)** di bawah teks judul.
     - Area kanan banner menjadi **kosong melompong** dan tata letak tampak tidak proporsional / berantakan persis seperti pada tangkapan layar `media_1794293540553_ae936a4b.png`.

---

## 2. Solusi & Spesifikasi Perbaikan

### 1. Isolasi Penuh (Scoping) CSS Dashboard Administrasi
Membatasi seluruh aturan hero banner di `resources/css/pages/admin/dashboard.css` agar HANYA berlaku di dalam kontainer `.adm-bento-wrapper` (dashboard admin):
```css
.adm-bento-wrapper .adm-hero-banner { ... }
.adm-bento-wrapper .adm-hero-content {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  z-index: 1;
}
.adm-bento-wrapper .adm-hero-tag { ... }
.adm-bento-wrapper .adm-hero-title { ... }
.adm-bento-wrapper .adm-hero-desc { ... }
.adm-bento-wrapper .adm-hero-status-pill { ... }
.adm-bento-wrapper .adm-hero-graphic { ... }
.adm-bento-wrapper .adm-hero-bar { ... }
```
Hal ini memastikan rule dashboard tidak akan pernah bocor (*leak*) ke halaman modul lain seperti Penempatan, Pendaftaran, atau Hub Laporan.

### 2. Penguatan Layout Horizontal Signature pada Child Pages
Pada `resources/css/components/cards.css` dan `resources/views/admin/penempatan/index.blade.php`:
- Menegaskan `flex-direction: row !important; justify-content: space-between !important; align-items: center !important; width: 100%;` untuk `.adm-hero-content` pada layar desktop/tablet (> 768px).
- Mempertahankan responsivitas mobile: pada layar kecil (<= 768px), tata letak otomatis beralih menjadi kolom (`flex-direction: column; align-items: flex-start;`) secara rapi dan proporsional.

---

## 3. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/admin/dashboard.css` | Modify | Melakukan scoping ketat pada selektor `.adm-hero-*` di bawah `.adm-bento-wrapper` agar tidak mencemari komponen hero di modul lain. |
| `resources/css/components/cards.css` | Modify | Menegaskan `flex-direction: row; justify-content: space-between; align-items: center; width: 100%;` pada `.adm-hero-content` standar. |
| `resources/views/admin/penempatan/index.blade.php` | Modify | Mempertegas styling `.adm-hero-content` di blok style halaman penempatan agar teks di kiri dan tombol aksi di kanan sejajar horizontal secara presisi. |
| `resources/views/admin/pendaftaran/index.blade.php` | Modify | Mempertegas styling `.adm-hero-content` di blok style halaman pendaftaran agar konsisten dan tahan bocor. |

---

## 4. Langkah Implementasi (Checklist)

- [x] **Langkah 1**: Edit `resources/css/pages/admin/dashboard.css`:
  - Ubah `.adm-hero-banner`, `.adm-hero-content`, `.adm-hero-tag`, `.adm-hero-title`, `.adm-hero-desc`, `.adm-hero-status-pill`, `.adm-hero-graphic`, dan `.adm-hero-bar` menjadi `.adm-bento-wrapper .adm-hero-*`.
  - Sesuaikan media query `@media (max-width: 640px)` di `dashboard.css` agar tetap scoped ke `.adm-bento-wrapper`.
- [x] **Langkah 2**: Edit `resources/css/components/cards.css`:
  - Pertegas `.adm-hero-content` dengan `flex-direction: row; justify-content: space-between; align-items: center; width: 100%;` dan styling `.adm-hero-actions`.
- [x] **Langkah 3**: Edit `resources/views/admin/penempatan/index.blade.php` & `pendaftaran/index.blade.php`:
  - Pertegas `.adm-hero-content` dengan `flex-direction: row !important; justify-content: space-between !important; align-items: center !important; width: 100%;`.
- [x] **Langkah 4**: Kompilasi Asset:
  - Jalankan `npm run build` untuk mengompilasi CSS terbaru (selesai dalam 2.58s).
- [x] **Langkah 5**: Quality Gate & Pengujian Otomatis:
  - Jalankan test PHPUnit modul Penempatan & Dashboard: seluruh 48 skenario tes 100% lulus (OK).

---

## 5. Kriteria Selesai (Definition of Done)

- [x] Pada layar desktop/laptop, banner `"Alokasi Penempatan & Pembimbing Magang"` menampilkan teks judul di kiri dan tombol aksi (`Ekspor Rekap ∨` serta `+ Penempatan Jalur Khusus`) di sisi kanan sejajar horizontal.
- [x] Tidak ada lagi ruang kosong melompong di sisi kanan hero banner penempatan.
- [x] Halaman Admin Dashboard (`/admin/dashboard`) tetap tampil sempurna dengan Bento Grid, 4 batang dekoratif vertikal di kanan, dan status pill aktif.
- [x] Responsivitas mobile (<= 768px) tetap berfungsi mulus (tumpuk vertikal yang rapi saat layar sempit).
- [x] `npm run build` sukses.
- [x] Seluruh unit test dan feature test PHPUnit lulus 100%.

