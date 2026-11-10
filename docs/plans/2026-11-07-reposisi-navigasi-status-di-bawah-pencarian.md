# Plan: Reposisi & Tata Letak Terstruktur Tombol Navigasi Status Verifikasi Berkas

**File Dokumen**: `docs/plans/2026-11-07-reposisi-navigasi-status-di-bawah-pencarian.md`  
**Status**: Usulan / Menunggu Persetujuan Pengguna  
**Tanggal**: 07 November 2026  
**Target Modul**: Halaman Verifikasi Berkas Admin (`resources/views/admin/pendaftaran/index.blade.php`)  
**Penulis**: Senior Developer & UI/UX Specialist  

---

## 1. Analisis Masalah Tata Letak (Problem Statement)

Berdasarkan evaluasi atas keluhan pengguna:
1. **Posisi Bertumpuk & Terlalu Berdekatan**:
   - Tab navigasi **Kategori Peserta** (`Semua Kategori`, `Individu`, `Kelompok`) dan kapsul navigasi **Status Alur Kerja** (`Semua Status`, `Diajukan`, `Diterima`, `Ditolak`) saat ini sama-sama diletakkan di bagian paling atas secara berurutan.
   - Hal ini membuat area atas card panel menjadi terlalu padat oleh dua lapis kontrol navigasi berturut-turut sebelum pengguna melihat kolom pencarian.
2. **Keinginan Pengguna**:
   - Memindahkan kapsul tombol navigasi status ke **bawah kolom pencarian** atau menatanya di lokasi yang terstruktur, rapi, elegan, tidak berantakan ("rame"), dan berstandar profesional.

---

## 2. Pilihan Solusi Tata Letak Profesional

---

### 🌟 OPSI 1: Kapsul Status Terintegrasi di Atas Tabel (Tepat di Bawah Form Pencarian) - REKOMENDASI UTAMA
*Standar industri enterprise SaaS (Stripe, Linear, GitHub Issues, Shopify Admin)*

#### Alur Informasi (Information Architecture):
1. **Level 1 (Card Header Tab)**: Kategori Pendaftaran (`Semua Kategori`, `Individu`, `Kelompok`) sebagai penyaring cakupan utama entitas di bagian teratas.
2. **Level 2 (Form Pencarian & Filter)**: Box form input pencarian nama, bulan magang, jurusan, dan urutan data.
3. **Level 3 (Table Toolbar / Di Atas Tabel Data)**: Kapsul status alur kerja (`Semua Status`, `Diajukan`, `Diterima`, `Ditolak`) dipindahkan ke baris kepala tabel (*table meta header*), berdampingan dengan informasi jumlah berkas (`Menampilkan 1-4 dari 4 berkas`).

#### Visual Wireframe Opsi 1:
```text
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│ DAFTAR PERMOHONAN MAGANG MASUK                                                          [-] │
│                                                                                             │
│  [ Semua Kategori  4 ]     [ 👤 Individu  4 ]     [ 👥 Kelompok  0 ]                         │
│  ═════════════════════ (garis aksen biru pada tab aktif)                                    │
│ ─────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                             │
│  ┌─ Box Pencarian & Filter Parameter ─────────────────────────────────────────────────────┐ │
│  │ [ 🔍 Cari nama peserta... ]  [ Bulan Magang v ]  [ Jurusan v ]  [ Urutan v ] [Terapkan]│ │
│  └────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                             │
│  ┌─ Tabel Berkas Pendaftaran ─────────────────────────────────────────────────────────────┐ │
│  │ ┌─ Toolbar Status Tabel (Di Bawah Pencarian) ────────────────────────────────────────┐ │ │
│  │ │ [ Semua Status 4 | Diajukan 1 | Diterima 2 | Ditolak 0 ]        Menampilkan 1-4    │ │ │
│  │ └────────────────────────────────────────────────────────────────────────────────────┘ │ │
│  │  No. Reg | Calon Peserta | Institusi & Jurusan | Bulan Magang | Status Berkas | Aksi   │ │
│  │  ...                                                                                   │ │
│  └────────────────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────────────────┘
```

#### Keunggulan Opsi 1:
- **Pemisahan Jarak Fisik yang Sempurna**: Tab Kategori (atas) dan Kapsul Status (bawah) kini dipisahkan oleh box form pencarian. Tidak ada lagi dua baris navigasi yang menempel atau berdekatan.
- **Konteks Sangat Relevan**: Kapsul status berada langsung menempel di atas data tabel, berfungsi sebagai *quick table view filter* yang menyaring baris tabel di bawahnya secara instan.
- **Tampilan Sangat Bersih**: Box filter di atas menjadi murni fokus pada kolom input teks dan dropdown tanpa elemen kapsul yang membuat sesak.

---

### OPSI 2: Baris Kapsul Status Mandiri Di Bawah Box Filter (Floating Sub-Bar)
*Gaya Dashboard Analitik Modern*

#### Alur Informasi:
- Kategori tetap di atas sebagai tab.
- Box pencarian di tengah.
- Di antara box pencarian dan tabel, disediakan 1 baris mandiri khusus kapsul status di sisi kiri dan tombol `Reset Filter` di sisi kanan.

#### Visual Wireframe Opsi 2:
```text
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│  [ Semua Kategori  4 ]     [ 👤 Individu  4 ]     [ 👥 Kelompok  0 ]                         │
│  ────────────────────────────────────────────────────────────────────────────────────────── │
│  ┌─ Box Pencarian & Filter ───────────────────────────────────────────────────────────────┐ │
│  │ [ 🔍 Cari nama... ]  [ Bulan Magang v ]  [ Jurusan v ]  [ Urutan v ]       [ Terapkan ]│ │
│  └────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                             │
│  [ Semua Status 4 | Diajukan 1 | Diterima 2 | Ditolak 0 ]                      ↺ Reset Filter│
│                                                                                             │
│  ┌─ Tabel Data Berkas ────────────────────────────────────────────────────────────────────┐ │
│  │ Menampilkan 1-4 dari 4 berkas pendaftaran                                              │ │
│  │ No. Reg | Calon Peserta | Institusi & Jurusan | Bulan Magang | Status Berkas | Aksi    │ │
│  └────────────────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────────────────┘
```

#### Keunggulan:
- Memberikan jeda napas (*white space*) yang lega antara box formulir pencarian dan tabel data.

---

## 3. Rencana Implementasi Teknis (Berdasarkan Rekomendasi Opsi 1)

1. **Perubahan Markup Blade** (`resources/views/admin/pendaftaran/index.blade.php`):
   - Pindahkan blok `.adm-status-track-wrapper` dari dalam `.adm-filter-toolbar` ke bagian atas `.adm-table-container` (atau menyatu ke dalam `.adm-table-meta-bar`).
   - Jadikan `.adm-filter-toolbar` murni wadah form grid 4 kolom pencarian.
   - Di baris tabel header, sisi kiri memuat Kapsul Status Alur Kerja (`Semua Status`, `Diajukan`, `Diterima`, `Ditolak`), dan sisi kanan memuat info jumlah data serta link Reset Filter jika ada filter aktif.
2. **Penyesuaian CSS Styling**:
   - Sesuaikan margin dan padding `.adm-table-meta-bar` agar membungkus kapsul status dan info data dengan fleksibel dan responsif di mobile/tablet.
   - Pastikan styling dark mode tetap konsisten.
3. **Pengujian & Quality Gate**:
   - `d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe artisan test --filter AdminVerifikasiKategoriDanUploadSuratTest`
   - `d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe vendor/bin/phpstan analyse --memory-limit=1G`
   - `vendor\bin\pint.bat --test`
   - `npm run build`

---

## 4. Keputusan Pengguna

Apakah Anda menyetujui rekomendasi **OPSI 1 (Kapsul Status Dipindahkan ke Bawah Kolom Pencarian / Menyatu di Atas Kepala Tabel Data)**?  
Silakan konfirmasi agar implementasi dapat segera dieksekusi.

