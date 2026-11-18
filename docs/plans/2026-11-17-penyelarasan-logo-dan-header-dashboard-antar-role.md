# Rencana Kerja: Penyelarasan Logo Brand & Header Dashboard Antar Role (Peserta, Pembimbing, Administrasi, SuperAdmin)

- **Tanggal Dokumen**: 17 November 2026
- **Status**: Selesai Diimplementasikan & Lulus Verifikasi (Completed)
- **Tujuan**: Menyelaraskan tampilan visual sidebar (logo Sleman & teks brand "MAGKOM") serta header (topbar) di ke-4 peran pengguna (Peserta Magang, Pembimbing, Administrasi, dan SuperAdmin) agar 100% konsisten, proporsional, dan identik sesuai standar desain sistem MagKom.
- **Acuan Visual**: Tangkapan layar dari pengguna (`media_1794914220425_192379dd.png` s/d `media_1794914252850_8b47ed15.png`).

---

## 1. Analisis Kesenjangan Visual (Gap Analysis)

Berdasarkan investigasi terhadap 4 foto tangkapan layar dan basis kode saat ini:

| Komponen | SuperAdmin & Administrasi (Acuan Baku) | Pembimbing (Kondisi Saat Ini) | Peserta Magang (Kondisi Saat Ini) | Status Kesenjangan & Masalah |
| :--- | :--- | :--- | :--- | :--- |
| **Emblem Logo Sleman** | Kotak `36px × 36px`, `border-radius: 8px`, `background: #0b3a7a`, `border: 1px solid rgba(255,255,255,0.15)`. | Sama persis dengan acuan. | Sama persis (sudah disinkronkan ke `#0b3a7a`). | Selesai (100% Selaras). |
| **Teks "MAGKOM" Sidebar** | Solid putih murni `#FFFFFF`, font-weight 800, font-size 1.15rem, opacity 1. | Solid putih murni `#FFFFFF`, font-weight 800, font-size 1.15rem, opacity 1. | Solid putih murni `#FFFFFF`, font-weight 800, font-size 1.15rem, opacity 1. | Selesai (100% Selaras). |
| **Tombol Toggle Topbar (Hamburger)** | `44px × 44px`, `border-radius: 8px`, `background: rgba(255, 255, 255, 0.12)`, `border: 1px solid rgba(255, 255, 255, 0.22)`, icon SVG 3 garis `line`. | Menggunakan `layouts/dashboard.blade.php`, sama dengan acuan. | `44px × 44px`, `border-radius: 8px`, `background: rgba(255, 255, 255, 0.12)`, `border: 1px solid rgba(255, 255, 255, 0.22)`, icon SVG 3 garis `line`. | Selesai (100% Selaras). |
| **Tipografi Judul Topbar** | Font **Plus Jakarta Sans**, `font-size: 1.15rem`, `font-weight: 700`, `color: #FFFFFF`. | Font **Plus Jakarta Sans**, `font-size: 1.15rem`, `font-weight: 700`, `color: #FFFFFF`. | Font **Plus Jakarta Sans**, `font-size: 1.15rem`, `font-weight: 700`, `color: #FFFFFF`. | Selesai (100% Selaras). |
| **Latar Gradien Topbar** | `linear-gradient(135deg, #01347C 0%, #003566 100%)`. | `linear-gradient(135deg, #01347C 0%, #003566 100%)`. | `linear-gradient(135deg, #01347C 0%, #003566 100%)`. | Selesai (100% Selaras). |
| **Padding Topbar** | `padding: 0 1.5rem` (24px) / `0 1.75rem` (28px). | `padding: 0 1.5rem` / `0 1.75rem`. | `padding: 0 1.5rem` (24px). | Selesai (100% Selaras). |

---

## 2. Rencana Implementasi Bertahap (Step-by-Step)

### Bagian 1: Penyelarasan Sidebar Peserta (Logo & Teks Brand MAGKOM)
1. **Pembaruan Markup Blade (`resources/views/layouts/peserta.blade.php`)**:
   - Menghapus class `.brand-text` pada container judul sidebar (`<div class="sidebar-brand-text" style="display: flex; flex-direction: column;">`) agar tidak terpengaruh oleh style legacy `.brand-text span`.
   - Memastikan elemen teks `<span class="sidebar-brand-title">MAGKOM</span>` memiliki styling solid putih `#FFFFFF` dengan bobot 800.
2. **Pembaruan CSS Layout Tokens (`resources/css/layouts/peserta-layout-tokens.css`)**:
   - Membatasi cakupan selector `.brand-text span` agar tidak menimpa elemen `.sidebar-brand-title`.
   - Menambahkan rule spesifik untuk `.sidebar-brand-title` di peserta:
     ```css
     .sidebar-brand-title {
         color: #FFFFFF !important;
         font-size: 1.15rem !important;
         font-weight: 800 !important;
         letter-spacing: 0.04em !important;
         line-height: 1.2 !important;
         opacity: 1 !important;
     }
     ```

### Bagian 2: Penyelarasan Header (Topbar) Peserta
1. **Pembaruan Markup Blade (`resources/views/layouts/peserta.blade.php`)**:
   - Menambahkan Google Fonts `Plus Jakarta Sans` pada `<link>` di bagian `<head>`.
   - Menyelaraskan tombol `#sidebarToggleBtn`:
     - Tambahkan class `.topbar-toggle-btn`.
     - Gunakan icon SVG standar dengan 3 garis `line` yang persis sama dengan `#menu_toggle` pada `dashboard.blade.php`:
       ```html
       <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
           <line x1="3" y1="12" x2="21" y2="12"></line>
           <line x1="3" y1="6" x2="21" y2="6"></line>
           <line x1="3" y1="18" x2="21" y2="18"></line>
       </svg>
       ```
2. **Pembaruan CSS Layout Tokens (`resources/css/layouts/peserta-layout-tokens.css`)**:
   - Mengubah ukuran dan gaya tombol toggle Peserta (`.sidebar-toggle-btn`, `.topbar-toggle-btn`):
     - `width: 44px; height: 44px; min-width: 44px; min-height: 44px;`
     - `border-radius: 8px;`
     - `background: rgba(255, 255, 255, 0.12) !important;`
     - `border: 1px solid rgba(255, 255, 255, 0.22) !important;`
     - Hover: `background: rgba(255, 255, 255, 0.22) !important; border-color: rgba(255, 255, 255, 0.4) !important;`
   - Mengubah tipografi judul topbar `.topbar-title`:
     - `font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;`
     - `font-size: 1.15rem !important;`
     - `font-weight: 700 !important;`
     - `color: #FFFFFF !important;`
   - Mengubah padding `.topbar` dari `0 32px` menjadi `0 1.5rem` (24px) agar jarak tepi dan proporsinya selaras dengan dashboard master.

### Bagian 3: Penyelarasan Header (Topbar) Pembimbing
1. **Pembaruan CSS Halaman Pembimbing (`resources/css/pages/pembimbing/dashboard.css`)**:
   - Memperbaiki arah gradien latar header:
     - Ubah dari `linear-gradient(135deg, #003566 0%, #01347C 100%) !important;` menjadi `linear-gradient(135deg, #01347C 0%, #003566 100%) !important;` (sama persis dengan SuperAdmin dan Administrasi).
   - Menambahkan aturan tipografi judul topbar agar seragam:
     ```css
     body.role-pembimbing .topbar-title {
         font-size: 1.15rem !important;
         font-weight: 700 !important;
     }
     ```

### Bagian 4: Penyelarasan Global Layout Tokens (`resources/css/layouts/dashboard-layout-tokens.css`)
1. Menyelaraskan default `.topbar-title` pada `dashboard-layout-tokens.css` agar secara bawaan menggunakan `font-family: var(--font-sans, 'Plus Jakarta Sans', sans-serif); font-size: 1.15rem; font-weight: 700;`, sehingga role Administrasi, Pembimbing, dan SuperAdmin memiliki konsistensi alami.

---

## 3. Daftar File yang Terkena Dampak (Target Files)

| File | Kategori | Alasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/layouts/peserta.blade.php` | Blade Layout | Menghapus class pembungkus pudar `.brand-text`, memuat font `Plus Jakarta Sans`, dan menyelaraskan SVG toggle button dengan master dashboard. |
| `resources/css/layouts/peserta-layout-tokens.css` | Stylesheet | Memberikan warna putih pekat `#FFFFFF` pada teks "MAGKOM", menyamakan ukuran toggle button (`44px`, `radius 8px`), menyamakan font judul ke `Plus Jakarta Sans 1.15rem`, dan padding topbar. |
| `resources/css/pages/pembimbing/dashboard.css` | Stylesheet | Menyelaraskan arah gradien warna navy topbar Pembimbing `#01347C -> #003566` dan ukuran font judul `1.15rem`. |
| `resources/css/layouts/dashboard-layout-tokens.css` | Stylesheet | Menjadikan `1.15rem` dan font `Plus Jakarta Sans` sebagai nilai dasar judul topbar dashboard. |

---

## 4. Kriteria Keberhasilan (Definition of Done)

1. [x] **Teks MAGKOM Peserta**: Teks "MAGKOM" di sidebar Peserta berwarna putih solid (`#FFFFFF`) dengan kontras tajam, identik dengan SuperAdmin, Pembimbing, dan Administrasi.
2. [x] **Toggle Button Peserta**: Berukuran `44px × 44px`, sudut `8px`, efek hover transparan putih `0.12 -> 0.22`, dan garis SVG identik dengan SuperAdmin/Admin.
3. [x] **Judul Topbar Peserta**: Menggunakan font `Plus Jakarta Sans`, ukuran `1.15rem`, tebal `700`, warna putih murni.
4. [x] **Header Pembimbing**: Gradien warna navy mengalir dari `#01347C` (kiri) ke `#003566` (kanan), dan ukuran judul tepat `1.15rem`.
5. [x] **Build Aset**: `npm run build` berhasil tanpa warning/error.
6. [x] **Test Otomatis**: Seluruh pengujian branding (`tests/Feature/BrandingDesignSystemTest.php`) dan pengujian layout (`tests/Feature/DashboardLayoutTest.php`) lulus 100% (green / pass).
