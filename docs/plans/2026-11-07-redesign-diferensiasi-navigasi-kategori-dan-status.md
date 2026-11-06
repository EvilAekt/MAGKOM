# Plan: Redesain Diferensiasi Navigasi Kategori Peserta & Status Alur Kerja Verifikasi Berkas

**File Dokumen**: `docs/plans/2026-11-07-redesign-diferensiasi-navigasi-kategori-dan-status.md`  
**Status**: Usulan / Menunggu Persetujuan Pengguna  
**Tanggal**: 07 November 2026  
**Target Modul**: Halaman Verifikasi Berkas Pendaftaran Admin (`resources/views/admin/pendaftaran/index.blade.php`)  
**Penulis**: Senior Developer & UI/UX Specialist  

---

## 1. Analisis Masalah & Evaluasi Desain Saat Ini (Problem Statement)

Berdasarkan tinjauan profesional atas umpan balik pengguna dan tangkapan layar antarmuka:
1. **Bentuk Fisik yang Kembar (Visual Duplication)**:
   - Kontrol **Status Alur Kerja** (`Semua Status`, `Diajukan`, `Diterima`, `Ditolak`) dan kontrol **Kategori Peserta** (`Semua Kategori`, `Individu`, `Kelompok`) saat ini menggunakan wujud komponen yang **identik/kembar**: kapsul lonjong putih (*pill track*) dengan tab lonjong dan badge counter bundar.
   - Kedua kontrol terlihat seperti salinan satu sama lain sehingga membingungkan mata pengguna dan tidak memiliki pembeda hierarki.
2. **Kepadatan Visual yang Mengganggu (Visual Clutter / "Rame")**:
   - Menempatkan kedua kapsul ini berdampingan di satu baris (atau menumpuknya secara vertikal) di dalam toolbar filter menyebabkan area filter menjadi terlalu padat dan repetitif.
   - Di sisi kiri sudah terdapat pill status alur kerja, sehingga menambahkan pill kedua di sebelahnya memecah fokus admin dan menimbulkan kesan antarmuka yang kurang terkonsep (*unbalanced*).
3. **Pemisahan Semantik yang Belum Tercermin di UI**:
   - **Kategori (Individu vs Kelompok)** adalah **Tipe Entitas / Parent Scope** (siapa yang mendaftar).
   - **Status (Diajukan, Diterima, Ditolak)** adalah **Alur Kerja Operasional / Workflow State** (tahap verifikasi berkas).
   - Kedua hal ini harus dipisahkan level hierarkinya, bukan diperlakukan sejajar dalam bentuk kapsul yang sama.

---

## 2. Pilihan Konsep Desain Profesional (3 Solusi Arsitektur)

---

### 🌟 OPSI 1: Top-Level Underline Nav Tabs (SANGAT DIREKOMENDASIKAN)
*Standar industri modern (GitHub, Linear, Stripe, Vercel, Laravel Nova)*

#### Konsep Desain:
- **Kategori Peserta** diangkat menjadi **Level Navigasi Utama (Tab Bar)** yang diletakkan tepat di bawah judul panel `Daftar Permohonan Magang Masuk` dengan gaya **Clean Underline Tabs**.
- Wujud Kategori:
  - Menggunakan tab horizontal dengan teks tegas, ikon representatif, badge hitung minimalis, dan garis aktif bawah (*active border-bottom indicator* 2.5px berwarna biru `#0641CC`).
  - **Tanpa wadah kapsul lonjong**, sehingga bentuknya **100% BERBEDA** dari kontrol status.
- Wujud Status Alur Kerja:
  - Di dalam **Toolbar Filter** di bawahnya, HANYA ADA **1 elemen kapsul alur kerja** tunggal: `[ Semua Status | Diajukan | Diterima | Ditolak ]` di sisi kiri, dan link/tombol `Reset Filter` di sisi kanan jika filter aktif.
- Visual Wireframe Opsi 1:
```text
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│ DAFTAR PERMOHONAN MAGANG MASUK                                                          [-] │
│                                                                                             │
│  [ Semua Kategori  4 ]     [ 👤 Individu  4 ]     [ 👥 Kelompok  0 ]                         │
│  ═════════════════════ (garis aksen biru pada tab aktif)                                    │
│ ─────────────────────────────────────────────────────────────────────────────────────────── │
│                                                                                             │
│  ┌─ Toolbar Filter Terpadu ───────────────────────────────────────────────────────────────┐ │
│  │                                                                                        │ │
│  │  [ Semua Status (4) | Diajukan (1) | Diterima (2) | Ditolak (0) ]        ↺ Reset Filter│ │
│  │                                                                                        │ │
│  │  [ 🔍 Cari nama... ]  [ Bulan Magang v ]  [ Jurusan v ]  [ Urutan v ]      [ Terapkan ]│ │
│  └────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                             │
│  ┌─ Tabel Berkas Pendaftaran ─────────────────────────────────────────────────────────────┐ │
│  │ No. Reg | Calon Peserta | Institusi & Jurusan | Bulan Magang | Status Berkas | Aksi    │ │
│  └────────────────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────────────────┘
```

#### Keunggulan:
- **Diferensiasi 100% Nyata**: Tab Garis (Underline) untuk Kategori vs Kapsul Lonjong (Pill) untuk Status. Tidak ada lagi komponen kembar.
- **Kerapian Maksimal**: Toolbar filter menjadi lapang, tenang, dan tidak lagi terlihat "rame".
- **Hierarki Jelas**: Admin memahami bahwa ia berada di tab "Pendaftaran Individu", lalu memfilter berdasarkan status "Diajukan/Diterima/Ditolak".

---

### OPSI 2: Header Toolbox Segmented Switch (Kanan Atas Judul Card)
*Standar Apple macOS / iPadOS Segmented Control*

#### Konsep Desain:
- Memanfaatkan ruang kosong di baris judul panel `x-panel` (slot header/toolbox).
- Pemilih Kategori ditaruh di sebelah kanan judul panel sebagai **Compact Segmented Button**:
  `[ Semua Kategori | 👤 Individu | 👥 Kelompok ]` dengan tinggi ramping 32px dan latar abu-abu halus.
- Toolbar filter di bawahnya HANYA memuat Kapsul Status di kiri dan tombol Reset di kanan.

#### Visual Wireframe Opsi 2:
```text
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│ Daftar Permohonan Magang Masuk        [ Semua Kategori | 👤 Individu | 👥 Kelompok ]    [-] │
├─────────────────────────────────────────────────────────────────────────────────────────────┤
│ ┌─ Toolbar Filter ────────────────────────────────────────────────────────────────────────┐ │
│ │ [ Semua Status (4) | Diajukan (1) | Diterima (2) | Ditolak (0) ]          ↺ Reset Filter│ │
│ │                                                                                         │ │
│ │ [ 🔍 Cari nama... ]  [ Bulan Magang v ]  [ Jurusan v ]  [ Urutan v ]       [ Terapkan ] │ │
│ └─────────────────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────────────────┘
```

#### Keunggulan:
- Sangat hemat ruang vertikal.
- Memanfaatkan header card yang sering kosong.

---

### OPSI 3: Kapsul Status Tunggal di Atas + Segmented Chips di Form Grid
*Standar Material 3 / Modern Form Filters*

#### Konsep Desain:
- Baris atas toolbar HANYA memuat 1 Kapsul Status Alur Kerja.
- Kategori dipindahkan ke dalam Form Grid di bawahnya sebagai **Segmented Chip Group** (bukan dropdown `<select>` biasa), diletakkan tepat setelah kotak input pencarian.

#### Keunggulan:
- Semua filter formulir terkumpul dalam 1 grid. Namun, segmented chips di dalam form grid memakan lebar horizontal yang cukup besar pada layar laptop standar.

---

## 3. Rencana Implementasi Teknis (Berdasarkan Rekomendasi Opsi 1)

Jika Opsi 1 disetujui, langkah implementasi mencakup:

### 3.1 Perubahan Markup Blade (`resources/views/admin/pendaftaran/index.blade.php`)
- **Persetujuan Markup**: Memerlukan penyesuaian markup Blade sesuai Bagian 3.3 aturan repositori.
- **Komponen Tab Navigasi Kategori Baru** (`.adm-category-nav-bar`):
  - Ditempatkan tepat di atas `.adm-filter-toolbar` di dalam `x-panel`.
  - Terdiri dari 3 link tab:
    1. `Semua Kategori` dengan counter total pendaftaran.
    2. `Individu` dengan ikon user SVG dan counter `total_individu`.
    3. `Kelompok` dengan ikon users SVG dan counter `total_kelompok`.
- **Pembersihan Toolbar Filter Bawah**:
  - Hapus track kategori kembar dari `.adm-status-track-wrapper`.
  - Toolbar kini hanya memiliki `.adm-status-track` alur kerja di kiri, dan `.adm-filter-reset-link` di kanan.
  - Dropdown formulir pencarian tetap rapi 4 kolom (Pencarian, Bulan Magang, Jurusan, Urutan Data + Tombol Terapkan & Reset).

### 3.2 Styling CSS Terfokus (`resources/views/admin/pendaftaran/index.blade.php` atau CSS Page)
- Membuat class CSS baru `.adm-category-nav-bar`:
  - `display: flex; gap: 1.75rem; border-bottom: 1.5px solid #E2E8F0; margin-bottom: 1.25rem;`
- Membuat class `.adm-category-nav-item`:
  - `display: inline-flex; align-items: center; gap: 8px; padding: 0.6rem 0.25rem 0.85rem 0.25rem; font-size: 0.875rem; font-weight: 600; color: #64748B; border-bottom: 2.5px solid transparent; text-decoration: none;`
- Active State:
  - `.adm-category-nav-item.active`: `color: #0641CC; border-bottom-color: #0641CC; font-weight: 700;`
  - `.adm-category-nav-item.active-individu`: `color: #0284C7; border-bottom-color: #0284C7;`
  - `.adm-category-nav-item.active-kelompok`: `color: #7C3AED; border-bottom-color: #7C3AED;`
- Counter badge netral `.adm-category-nav-badge`:
  - `background: #F1F5F9; color: #475569; padding: 2px 7px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;`
- Support Dark Mode:
  - Menyesuaikan warna border dan text pada selector `[data-theme="dark"]`.

### 3.3 Preservasi Kontrak & Pengujian (Quality Gate)
- Menjaga query string helper `$makeFilterUrl()` agar transisi antar-tab mempertahankan filter bulan, jurusan, pencarian, dan status yang sedang aktif.
- Menjaga teks `'Semua Kategori'`, `'Individu'`, dan `'Kelompok'` tetap ada dalam DOM agar suite pengujian otomatis lulus:
  - `php artisan test --filter AdminVerifikasiKategoriDanUploadSuratTest`
- Jalankan formatting linter:
  - `./vendor/bin/pint --test`
- Jalankan build aset:
  - `npm run build`

---

## 4. Keputusan & Langkah Selanjutnya

Menunggu konfirmasi dan persetujuan dari pengguna:
- **Apakah Anda menyetujui rekomendasi OPSI 1 (Top-Level Underline Nav Tabs)** untuk membedakan bentuk navigasi Kategori dan Status secara tegas, rapi, dan elegan?
- Atau Anda lebih memilih **OPSI 2 (Header Toolbox Switcher)** / **OPSI 3 (Chips Filter di Form Grid)**?

