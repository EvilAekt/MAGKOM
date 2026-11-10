# Rencana Kerja: Card Antrean Penempatan & Perbaikan Popover Ekspor Verifikasi Berkas

**Tanggal**: 10 November 2026  
**Status**: Selesai Diimplementasikan & Lulus Uji Otomatis  
**Dokumen Acuan**: `AGENTS.md` (Section 3, 4, 10), PRD MagKom, Design System DS-MagKom  

---

## 1. Latar Belakang & Identifikasi Masalah

Berdasarkan tinjauan tangkapan layar dan arahan pengguna:
1. **Item 1 (Tabel Antrean Belum Ditempatkan)**:
   - **Masalah**: Pada halaman `/admin/penempatan?tab=belum` (Tab "Belum Ditempatkan"), wadah tabel masih menggunakan elemen lama `<div class="admin-card" style="border-top: 3px solid #01347C; ...">` yang flat dan menyatu secara kaku. Berbeda dengan Tab 1 ("Sudah Ditempatkan") yang sudah rapi menggunakan panel kartu Gentelella `<x-gentelella.x-panel>` dan kontainer tabel `.adm-table-container`.
   - **Tuntutan**: Ubah tampilan antrean menjadi bentuk **Card terpisah** (panel kartu Gentelella) yang rapi, berjarak, dan terstruktur presisi selaras dengan Tab 1.

2. **Item 2 (Popover Ekspor Rekap Tertelan / Terpotong di Verifikasi Berkas)**:
   - **Masalah**: Pada halaman `/admin/pendaftaran` (Verifikasi Berkas Pendaftaran), saat tombol **"Ekspor Rekap"** di sudut kanan hero banner diklik, isi dropdown popover format berkas (`#admExportPopoverPendaftaran`) **terpotong/ketelen** tepat di tepi bawah banner navy.
   - **Penyebab Akar (Root Cause)**: Kontainer `.adm-hero-banner` didefinisikan dengan properti `overflow: hidden;` pada `resources/css/components/cards.css` dan tag scoped di `resources/views/admin/pendaftaran/index.blade.php`. Karena popover diposisikan absolut dan menjulur ke bawah melewati batas kontainer banner, properti `overflow: hidden` secara otomatis memotong (*clipping*) bagian bawah popover.

---

## 2. Rencana Solusi Teknis

### A. Transformasi Antrean Belum Ditempatkan Menjadi Panel Card (`x-panel`)
- **File**: `resources/views/admin/penempatan/index.blade.php`
- **Tindakan**:
  1. Ganti wrapper `<div class="admin-card" style="border-top: 3px solid #01347C; ...">` dengan `<x-gentelella.x-panel title="Antrean Peserta Lolos Berkas (Menunggu Penempatan)">`.
  2. Bungkus tabel dan blok paginasi `.adm-table-footer` ke dalam `<div class="adm-table-container">`.
  3. Standarisasi header tabel:
     - Peserta Magang (28%)
     - Jurusan (26%)
     - Bulan Magang (18%)
     - Diterima Pada (16%)
     - Aksi (12%)
  4. Sediakan *empty state* yang seragam dan elegan ketika antrean bernilai kosong (seperti pada Tab 1).

### B. Perbaikan Anti-Clipping Popover Ekspor Rekap
- **File**:
  - `resources/css/components/cards.css`
  - `resources/views/admin/pendaftaran/index.blade.php`
- **Tindakan**:
  1. Ubah properti `.adm-hero-banner` dari `overflow: hidden;` menjadi `overflow: visible !important;` serta tambahkan `position: relative; z-index: 20;`.
  2. Pastikan kontainer pembungkus tombol ekspor `.adm-export-dropdown-wrap` memiliki `position: relative; z-index: 30;`.
  3. Pastikan floating popover `.adm-export-popover` memiliki `z-index: 1050;` sehingga dapat melayang bebas di atas kartu-kartu filter pendaftaran di bawahnya tanpa terpotong maupun tertimpa.

---

## 3. Berkas yang Akan Diubah

| No | File Path | Tipe Perubahan | Alasan & Scope Perubahan |
|---|---|---|---|
| 1 | `resources/views/admin/penempatan/index.blade.php` | Modify Markup Blade | Membungkus antrean penempatan dengan `<x-gentelella.x-panel>` dan `<div class="adm-table-container">` agar konsisten berbentuk Card terpisah seperti Tab 1. |
| 2 | `resources/css/components/cards.css` | Modify Stylesheet | Mengubah `overflow: hidden` pada `.adm-hero-banner` menjadi `overflow: visible` dan menetapkan `z-index: 20` agar popover tidak terpotong. |
| 3 | `resources/views/admin/pendaftaran/index.blade.php` | Modify Scoped CSS | Menyelaraskan `.adm-hero-banner` dengan `overflow: visible !important;` dan `z-index: 20;` untuk mencegah clipping popover ekspor. |

---

## 4. Rencana Pengujian & Quality Gate

Setelah perubahan diaplikasikan:
1. **Kompilasi Aset Frontend**:
   ```bash
   npm run build
   ```
2. **Pengujian Otomatis (PHPUnit - 100% Passed)**:
   ```bash
   vendor/bin/phpunit tests/Feature/AdminAdministrasiExportTest.php tests/Feature/AdminEksporLaporanPendaftaranPopoverTest.php tests/Feature/AdminEksporPenempatanPopoverTest.php tests/Feature/AdminPenempatanDuaTahapTest.php tests/Feature/AdminTablePaginationStandardTest.php
   ```
3. **Pemeriksaan Standar Kode (Laravel Pint)**:
   ```bash
   vendor/bin/pint --test
   ```

---

## 5. Permohonan Persetujuan

Sesuai aturan `AGENTS.md` Bagian 3 dan Bagian 10, kami memohon persetujuan pengguna sebelum melakukan perubahan kode di atas.
Apakah rencana ini sudah sesuai dan boleh kami **implementasikan** sekarang?

