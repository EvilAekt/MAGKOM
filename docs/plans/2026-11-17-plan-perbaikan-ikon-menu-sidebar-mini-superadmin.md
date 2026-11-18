# Rencana Kerja: Pemulihan dan Penyelarasan Ikon Menu Grup pada Sidebar Mini SuperAdmin

- **Dokumen Referensi:** PRD MagKom v1.2, Desain Sistem Dashboard MagKom (Sleman Design Tokens), Aturan Frontend MagKom (Rule 3 & 4), `docs/plans/2026-11-17-plan-perbaikan-sidebar-mini-esc-semua-dashboard.md`
- **Target Pembaca / Pelaksana:** Full-Stack Developer & AI Assistant
- **Tingkat Kerumitan:** Rendah ke Menengah (*CSS Selector Scoping & Icon Geometry Alignment*)
- **Target Modul:** 
  - `resources/css/layouts/dashboard.css`
  - `resources/css/layouts/dashboard-layout-tokens.css`
  - `resources/css/layouts/gentelella.css`
  - `resources/css/components/cards.css`
- **Prinsip Utama:** **Blade Read-Only**. Seluruh pemulihan diselesaikan murni melalui perbaikan selector CSS scoped di `resources/css/` tanpa memodifikasi markup Blade.

---

## 1. Analisis Akar Masalah (Root Cause Analysis)

Pada tangkapan layar terbaru (`media_1794919566969_13a4724b.jpg`), terdapat ruang kosong melompong (*empty gap*) di bawah ikon Dashboard dan di bawah modul sistem pada mode mini (`nav-sm`). Ikon-ikon menu akordeon SuperAdmin (Administrasi, Pembimbing, Peserta, Konfigurasi) hilang dan tidak terlihat.

### Penyebab Teknis:
1. Struktur HTML di `dashboard.blade.php`:
   ```html
   <button type="button" class="nav-group-btn ...">
       <span class="nav-group-btn-left">
           <span class="nav-item-icon">
               <svg>...</svg>
           </span>
           <span>Administrasi</span>
       </span>
       <svg class="nav-group-chevron" ...></svg>
   </button>
   ```
2. Selector CSS yang sebelumnya diterapkan:
   ```css
   body.nav-sm .nav-group-btn span:not(.nav-item-icon) {
       display: none !important;
   }
   ```
3. Akibat selector terlalu luas:
   Elemen pembungkus `<span class="nav-group-btn-left">` adalah sebuah tag `<span>` yang **tidak** memiliki class `.nav-item-icon`.
   Akibatnya, browser mencocokkan selector tersebut terhadap `<span class="nav-group-btn-left">` dan memberikan `display: none !important;` pada **seluruh kontainer kiri**.
   Karena ikon SVG berada di dalam kontainer kiri tersebut, **ikon ikut tersembunyi total** bersama teksnya.

---

## 2. Sasaran Perbaikan (Target Deliverables)

1. **Pemulihan Ikon Menu Akordeon**:
   - Memperbaiki selector CSS agar **hanya** menyembunyikan teks nama grup (`span:not(.nav-item-icon)` yang berada langsung di dalam `.nav-group-btn-left`), dan **tetap menampilkan** `.nav-group-btn-left` serta `.nav-item-icon` (SVG).
2. **Geometri Simetris Pill Ikon 44 &times; 38 px**:
   - Pastikan ikon 4 grup akordeon:
     1. **Administrasi** (ikon folder/filing)
     2. **Pembimbing** (ikon clipboard check)
     3. **Peserta** (ikon group users)
     4. **Konfigurasi** (ikon gear)
     Tampil persis di tengah *pill* simetris berukuran 44px &times; 38px (`margin: 0.25rem auto`) rata dengan ikon-ikon lainnya (Dashboard, Data Staf, Hak Akses, Audit Log, Backup Database).
3. **Hover & Active State Paritas**:
   - Saat di-hover, ikon berubah menjadi kuning `#FFCB05` dengan scale 1.1.
   - Saat aktif (`has-active`), ikon berlatar navy elegan dan berwarna kuning `#FFCB05`.
   - Panah chevron (`>`) dan teks nama grup tetap disembunyikan.

---

## 3. Rencana Langkah Eksekusi (Implementation Steps)

### Langkah 1: Perbaikan Selector CSS di Seluruh Stylesheet
- **Files**:
  - `resources/css/layouts/dashboard.css`
  - `resources/css/layouts/dashboard-layout-tokens.css`
  - `resources/css/layouts/gentelella.css`
  - `resources/css/components/cards.css`
- **Perubahan Selector**:
  ```css
  /* 1. Tampilkan kontainer kiri tombol grup secara flex di tengah */
  body.nav-sm .nav-group-btn-left {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      width: 100% !important;
      height: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      gap: 0 !important;
  }

  /* 2. Pastikan ikon navigasi di dalamnya tampil di tengah */
  body.nav-sm .nav-group-btn-left .nav-item-icon {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      width: 20px !important;
      height: 20px !important;
      margin: 0 auto !important;
      color: var(--magkom-gray-medium, #AAAAAA) !important;
      transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.15s ease !important;
  }

  /* 3. Hanya sembunyikan teks label langsung di dalam nav-group-btn-left */
  body.nav-sm .nav-group-btn-left > span:not(.nav-item-icon),
  body.nav-sm .nav-item-link > span:not(.nav-item-icon) {
      display: none !important;
  }

  /* 4. Tetap sembunyikan panah chevron dan submenu inline */
  body.nav-sm .nav-group-chevron,
  body.nav-sm .nav-group-btn svg.nav-group-chevron,
  body.nav-sm .nav-group-btn .nav-group-chevron {
      display: none !important;
  }

  /* 5. Hover & active effect pada ikon grup */
  body.nav-sm .nav-group-btn:hover .nav-item-icon {
      color: #FFCB05 !important;
      transform: scale(1.1) translateY(-1px) !important;
  }

  body.nav-sm .nav-group-btn.has-active .nav-item-icon {
      color: var(--magkom-sun-yellow, #FFCB05) !important;
  }
  ```

---

### Langkah 2: Build Aset & Verifikasi
1. Jalankan `npm run build` untuk mengompilasi CSS.
2. Jalankan PHPUnit test suite:
   ```bash
   php artisan test --filter=Dashboard
   php artisan test --filter=Sidebar
   ```
3. Lakukan verifikasi visual: seluruh 9 item menu SuperAdmin (Dashboard, Administrasi, Pembimbing, Peserta, Data Staf, Hak Akses, Audit Log, Backup Database, Konfigurasi) tampil utuh sebagai deretan *pill* ikon simetris 44 &times; 38 px tanpa teks yang bocor dan tanpa ruang kosong yang hilang.
