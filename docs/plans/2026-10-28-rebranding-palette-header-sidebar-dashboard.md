# TASK SPECIFICATION & PRD: Rebranding Color Palette & Header-Sidebar Synchronization (SuperAdmin, Pembimbing, Administrasi)

- **Document ID**: `PRD-TASK-2026-10-28-UI-01`
- **Target Audience**: Junior Full-Stack Developer & AI Execution Models (e.g., Gemini 3.8 Flash)
- **Author**: Senior Lead Frontend & UI/UX Architect
- **Target Roles**: SuperAdmin, Pembimbing, Administrasi (Admin)
- **Primary Layout File**: `resources/views/layouts/dashboard.blade.php`
- **Secondary Asset Files**: `public/css/gentelella-theme.css`, `public/css/dashboard-cockpit-unified.css`

---

## 1. Tujuan & Ringkasan Eksekutif (Objective)

Menyeragamkan identitas visual (*brand identity*) pada **3 peran internal** (SuperAdmin, Pembimbing Lapangan, dan Administrasi/Admin) agar memiliki **Header Topbar yang identik dengan Peserta Magang** (sesuai referensi tangkapan layar `attachment`), serta merefaktor seluruh skema warna (Sidebar, Topbar, Card Surfaces, Canvas, Badges, dan Buttons) menggunakan **4 Palet Warna Baru**:

1. **`#1B262C`** — Midnight Charcoal Navy (Darkest Base / Foundation)
2. **`#0F4C75`** — Deep Classic Navy (Primary Brand / Sleman Dark Blue)
3. **`#3282B8`** — Vibrant Steel Blue (Accent / Interactive / State Hover)
4. **`#BBE1FA`** — Soft Ice Blue Tint (Light Surface / Card Accent / Highlighting)

Spesifikasi ini dirancang dengan instruksi sangat detail (*fail-safe* & *zero-ambiguity*) agar dapat dieksekusi secara mandiri oleh Junior Developer maupun model AI tanpa risiko halusinasi kode atau regresi fungsional.

---

## 2. Senior Technical Critique, Evaluasi Opsi, & Analisis Trade-Off

Sebagai tinjauan objektif dari Senior Developer untuk arsitektur UI sistem MagKom:

### A. Evaluasi Penggunaan Card Warna `#BBE1FA` di Mode Terang
- **Karakteristik `#BBE1FA`**: Warna ini memiliki *relative luminance* tinggi (~0.73), masuk kategori *pastel ice blue*.
- **Trade-off & Risiko**:
  - Jika **seluruh card** (tabel data besar, formulir bertingkat, container logbook) diberi background solid 100% `#BBE1FA`, mata pengguna akan mengalami *visual fatigue* (kelelahan visual) karena kejenuhan warna biru muda yang mendominasi kanvas putih.
  - **Kontras Aksesibilitas (WCAG 2.1 AA)**: Teks di atas background `#BBE1FA` **DILARANG KERAS** menggunakan warna putih (`#FFFFFF`) atau abu-abu terang (rasio kontras < 2:1 = FAIL). Teks wajib menggunakan warna gelap `#1B262C` (rasio kontras 13.5:1 = AAA) atau `#0F4C75` (rasio kontras 8.2:1 = AAA).
- **Rekomendasi Arsitektural (Opsi Terbaik)**:
  - **Metric / Stat / Highlight Card**: Gunakan solid background `#BBE1FA` dengan border `1px solid #3282B8` dan teks heading/angka menggunakan `#1B262C` & `#0F4C75`.
  - **Content & Table Card**: Gunakan surface bersih `--neutral-0` (`#FFFFFF`) dengan border halus `#BBE1FA` dan aksen header / strip atas bertema `#BBE1FA`.
  - Opsi ini mempertahankan estetika segar yang diminta user tanpa mengorbankan kenyamanan pembacaan data administrasi ribuan baris.

### B. Evaluasi Perbandingan Pendekatan Refaktor

| Indikator | Opsi A: Hardcode Tailwind Classes di Tiap View | Opsi B: Centralized CSS Variables Tokens di `dashboard.blade.php` (Direkomendasikan) |
| :--- | :--- | :--- |
| **Penyebaran File** | Menyentuh 40+ file Blade di folder `admin/`, `pembimbing/`, `superadmin/` | Cukup 1 file master layout (`layouts/dashboard.blade.php`) + 2 stylesheet aset |
| **Kemudahan Maintenance** | Sangat rentan inkonsistensi saat palet diganti di masa depan | 1 titik perubahan (*Single Source of Truth*) |
| **Dukungan Dark/Light Mode** | Memerlukan utility `dark:` di ratusan tag HTML | Otomatis berganti via selektor `[data-theme="dark"]` |
| **Rekomendasi** | ❌ Ditolak (High Risk, High Friction) | ✅ **Dipilih (Clean, Robust, 100% Zero-Regression)** |

---

## 3. Kamus Token Warna Baru & Matriks Kontras (Design Tokens)

### 3.1. Master Palette Table

| Kode Hex | Nama Semantik | Nilai RGB | Peran di Mode Terang (Light) | Peran di Mode Gelap (Dark) |
| :--- | :--- | :--- | :--- | :--- |
| **`#1B262C`** | `Midnight Navy` | `rgb(27, 38, 44)` | Sidebar background, Text primer pada card `#BBE1FA`, Dropdown menu border | Body canvas background, Card surface container, Header background |
| **`#0F4C75`** | `Deep Classic Navy`| `rgb(15, 76, 117)`| **Header Topbar (Gradient start)**, Primary button, Active menu item background | Card secondary border, Subtitle text, Secondary interactive state |
| **`#3282B8`** | `Steel Blue Accent`| `rgb(50, 130, 184)`| Active indicator border, Icons, Focus rings, Link aksen, Secondary button border | Button primary, Bright accent icon, Active glowing element, Highlights |
| **`#BBE1FA`** | `Ice Blue Tint` | `rgb(187, 225, 250)`| **Card surface / highlight background**, Badges info, Topbar breadcrumb text | Heading titles, Card border accent, Glowing badge text |

### 3.2. Token Mapping Dictionary (Lama ➔ Baru)

Untuk Junior Dev / AI: Ganti variabel CSS pada selector `:root` dan `[data-theme="dark"]` di `resources/views/layouts/dashboard.blade.php` sesuai tabel berikut:

```css
/* MODE TERANG (Light Mode) */
:root {
    /* Brand Tokens Baru */
    --brand-darkest: #1B262C;
    --brand-navy: #0F4C75;
    --brand-accent: #3282B8;
    --brand-ice: #BBE1FA;

    /* Override Variabel Eksisting */
    --blue-600: #0F4C75;           /* Warna utama aksi/brand */
    --blue-700: #1B262C;           /* Warna hover brand */
    --blue-500: #3282B8;           /* Warna aksen terang */
    --blue-tint: #BBE1FA;          /* Background badge/highlight card */
    --blue-tint-line: #3282B8;     /* Border pembatas aksen */
    --sky-100: #BBE1FA;

    /* Card & Canvas */
    --card-highlight-bg: #BBE1FA;
    --card-highlight-border: #3282B8;
    --card-highlight-text: #1B262C;

    /* Header & Sidebar */
    --topbar-bg-start: #0F4C75;
    --topbar-bg-end: #1B262C;
    --topbar-border: rgba(187, 225, 250, 0.2);
    --topbar-text: #FFFFFF;
    --topbar-text-muted: #BBE1FA;

    --sidebar-bg: #1B262C;
    --sidebar-text: #BBE1FA;
    --sidebar-active-bg: #0F4C75;
    --sidebar-active-border: #3282B8;
}

/* MODE GELAP (Dark Mode) */
[data-theme="dark"] {
    --neutral-0: #1B262C;          /* Card surface mode gelap */
    --neutral-50: #151E24;         /* Container sidebar/topbar gelap */
    --neutral-100: #1B262C;        /* Input background */
    --neutral-150: #121A1F;        /* Main canvas background */
    --neutral-200: #24343D;        /* Borders & Separators */
    --neutral-300: #3282B8;        /* Accent borders */
    --neutral-700: #E2E8F0;        /* Regular text */
    --neutral-800: #BBE1FA;        /* Headings & prominent text */
    --neutral-900: #FFFFFF;        /* Brightest text */

    --blue-600: #3282B8;           /* Tombol aksen di mode gelap */
    --blue-700: #0F4C75;           /* Hover tombol di mode gelap */
    --blue-tint: rgba(15, 76, 117, 0.4);
    --blue-tint-line: #3282B8;

    --topbar-bg-start: #1B262C;
    --topbar-bg-end: #151E24;
    --topbar-border: rgba(50, 130, 184, 0.35);

    --sidebar-bg: #151E24;
    --sidebar-text: #BBE1FA;
    --sidebar-active-bg: #0F4C75;
    --sidebar-active-border: #3282B8;
}
```

---

## 4. Rincian Perubahan File (File Impact Matrix)

| File Target | Aksi | Ringkasan Perubahan Teknis |
| :--- | :---: | :--- |
| `resources/views/layouts/dashboard.blade.php` | **Modify** | 1. Deklarasi token 4 warna baru di `:root` & `[data-theme="dark"]`.<br>2. Restrukturisasi CSS Topbar (`.top_nav`, `.nav_menu`, dll.) agar sama dengan Peserta Magang.<br>3. Restrukturisasi CSS Sidebar (`.left_col`, `.dashboard-sidebar`) dengan `#1B262C` dan active state `#0F4C75`.<br>4. Tambah tombol Cepat Ganti Mode Gelap/Terang (`#themeToggleBtn`) di kanan Topbar persis seperti di Peserta Magang.<br>5. Refactor Card surfaces ke mode terang (`#BBE1FA`) & mode gelap (`#1B262C`). |
| `public/css/gentelella-theme.css` | **Modify** | Sinkronisasi fallback `.left_col` ke `#1B262C !important` dan `.top_nav` background agar tidak terjadi visual glitch saat CSS Blade terlambat load. |
| `public/css/dashboard-cockpit-unified.css` | **Modify** | Update variabel `--cv-blue-*` dan `.cv-hero-banner` background gradient ke `#0F4C75` ➔ `#1B262C`. |

---

## 5. Panduan Implementasi Langkah demi Langkah (Junior Dev & AI Task Runbook)

### Phase 1: CSS Tokens Refactoring
- [ ] Buka `resources/views/layouts/dashboard.blade.php`.
- [ ] Pada tag `<style>` (sekitar baris 50-130), masukkan token palet baru `#1B262C`, `#0F4C75`, `#3282B8`, dan `#BBE1FA` untuk `:root` dan `[data-theme="dark"]`.

### Phase 2: Header Topbar Refactoring (Menyamakan dengan Peserta Magang)
- [ ] Ubah rule CSS `.top_nav` dan `.dashboard-topbar`:
  ```css
  .top_nav {
      background: linear-gradient(135deg, #0F4C75 0%, #1B262C 100%) !important;
      border-bottom: 1px solid rgba(187, 225, 250, 0.18) !important;
      box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.25);
  }
  ```
- [ ] Styling Tombol Hamburger (`#menu_toggle` & `.mobile-toggle-btn`):
  ```css
  #menu_toggle, .mobile-toggle-btn {
      background: rgba(255, 255, 255, 0.12) !important;
      border: 1px solid rgba(255, 255, 255, 0.22) !important;
      color: #FFFFFF !important;
      border-radius: 8px;
  }
  #menu_toggle:hover, .mobile-toggle-btn:hover {
      background: rgba(255, 255, 255, 0.22) !important;
      border-color: rgba(187, 225, 250, 0.4) !important;
  }
  ```
- [ ] Styling Breadcrumbs & Teks Header:
  ```css
  .breadcrumb-trail a { color: #BBE1FA !important; }
  .breadcrumb-trail a:hover { color: #FFFFFF !important; text-decoration: underline; }
  .breadcrumb-separator { color: rgba(187, 225, 250, 0.5) !important; }
  .breadcrumb-current { color: #FFFFFF !important; font-weight: 700; }
  ```
- [ ] Styling Search Bar Topbar (`.topbar-search-input`):
  ```css
  .topbar-search-input {
      background: rgba(255, 255, 255, 0.12) !important;
      border: 1px solid rgba(187, 225, 250, 0.25) !important;
      color: #FFFFFF !important;
  }
  .topbar-search-input::placeholder {
      color: rgba(187, 225, 250, 0.75) !important;
  }
  .topbar-search-input:focus {
      background: rgba(27, 38, 44, 0.85) !important;
      border-color: #3282B8 !important;
      box-shadow: 0 0 0 3px rgba(50, 130, 184, 0.35) !important;
  }
  ```
- [ ] Tambahkan Tombol Cepat Ganti Mode (`#themeToggleBtn`) di HTML Topbar (`.topbar-right` sebelum `#userMenuBtn`):
  ```html
  <button type="button" 
          class="topbar-theme-toggle-btn" 
          id="themeToggleBtn" 
          onclick="togglePortalThemeQuick()" 
          aria-label="{{ __('Ganti Mode Terang / Gelap') }}" 
          title="{{ __('Ganti Mode Terang / Gelap') }}">
      <svg class="sun-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
      </svg>
      <svg class="moon-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
      </svg>
  </button>
  ```
- [ ] Styling User Menu Trigger Pill (`#userMenuBtn`):
  ```css
  .user-menu-trigger {
      background: rgba(255, 255, 255, 0.12) !important;
      border: 1px solid rgba(255, 255, 255, 0.22) !important;
      border-radius: 9999px !important;
      padding: 4px 14px 4px 4px !important;
      color: #FFFFFF !important;
  }
  .user-name-text { color: #FFFFFF !important; font-weight: 600; }
  .role-chip-badge {
      background: #0F4C75 !important;
      color: #BBE1FA !important;
      border: 1px solid rgba(187, 225, 250, 0.3) !important;
  }
  .user-chevron-icon { color: #BBE1FA !important; }
  ```

### Phase 3: Sidebar ("Sliderbar") Refactoring
- [ ] Ubah background sidebar `.dashboard-sidebar` dan `.left_col` menjadi `#1B262C`:
  ```css
  .dashboard-sidebar, .left_col {
      background-color: #1B262C !important;
      border-right: 1px solid rgba(50, 130, 184, 0.2) !important;
  }
  ```
- [ ] Brand title box di header sidebar:
  ```css
  .sidebar-brand-emblem {
      background: rgba(255, 255, 255, 0.08) !important;
      border: 1px solid rgba(187, 225, 250, 0.2) !important;
  }
  .sidebar-brand-title {
      color: #FFFFFF !important;
  }
  ```
- [ ] Section Titles & Item Links:
  ```css
  .menu_section h3 {
      color: #BBE1FA !important;
      opacity: 0.8;
  }
  .nav.side-menu > li > a {
      color: #BBE1FA !important;
  }
  .nav.side-menu > li > a i,
  .nav.side-menu > li > a svg {
      color: #3282B8 !important;
  }
  .nav.side-menu > li > a:hover {
      background: rgba(50, 130, 184, 0.15) !important;
      color: #FFFFFF !important;
  }
  .nav.side-menu > li.active > a,
  .nav.side-menu > li.current-page > a {
      background: #0F4C75 !important;
      color: #FFFFFF !important;
      border-left: 4px solid #3282B8 !important;
      box-shadow: 0 2px 8px rgba(15, 76, 117, 0.4) !important;
  }
  .nav.side-menu > li.active > a svg,
  .nav.side-menu > li.current-page > a svg {
      color: #BBE1FA !important;
  }
  ```

### Phase 4: Card Surfaces Refactoring (Mode Terang `#BBE1FA` & Mode Gelap `#1B262C`)
- [ ] Card Highlight / Stat Card di Mode Terang:
  ```css
  /* Mode Terang: Highlight Card memakai #BBE1FA */
  html:not([data-theme="dark"]) .card-highlight,
  html:not([data-theme="dark"]) .stat-card-accent,
  html:not([data-theme="dark"]) .cv-stat-card {
      background-color: #BBE1FA !important;
      border: 1px solid #3282B8 !important;
      box-shadow: 0 4px 14px -2px rgba(50, 130, 184, 0.18) !important;
  }
  html:not([data-theme="dark"]) .card-highlight .card-title,
  html:not([data-theme="dark"]) .stat-card-accent .stat-val {
      color: #1B262C !important;
      font-weight: 700;
  }
  html:not([data-theme="dark"]) .card-highlight .card-desc,
  html:not([data-theme="dark"]) .stat-card-accent .stat-label {
      color: #0F4C75 !important;
      font-weight: 600;
  }

  /* Mode Terang: Container Card Utama */
  html:not([data-theme="dark"]) .x_panel,
  html:not([data-theme="dark"]) .card {
      background-color: #FFFFFF;
      border: 1px solid #BBE1FA;
      box-shadow: 0 2px 8px rgba(27, 38, 44, 0.04);
  }
  ```
- [ ] Card Surfaces di Mode Gelap:
  ```css
  /* Mode Gelap: Card memakai #1B262C dengan aksen #3282B8 */
  [data-theme="dark"] .x_panel,
  [data-theme="dark"] .card,
  [data-theme="dark"] .cv-stat-card {
      background-color: #1B262C !important;
      border: 1px solid #24343D !important;
      box-shadow: 0 4px 14px -2px rgba(0, 0, 0, 0.4) !important;
  }
  [data-theme="dark"] .card-title,
  [data-theme="dark"] .x_title h2,
  [data-theme="dark"] .stat-val {
      color: #BBE1FA !important;
  }
  ```

### Phase 5: Action Buttons & Interactive Controls
- [ ] Tombol Utama (Primary Button):
  ```css
  .btn-primary, .btn-primary-gradient {
      background: #0F4C75 !important;
      border-color: #0F4C75 !important;
      color: #FFFFFF !important;
  }
  .btn-primary:hover {
      background: #1B262C !important;
      border-color: #1B262C !important;
  }
  [data-theme="dark"] .btn-primary {
      background: #3282B8 !important;
      border-color: #3282B8 !important;
      color: #FFFFFF !important;
  }
  [data-theme="dark"] .btn-primary:hover {
      background: #0F4C75 !important;
      border-color: #0F4C75 !important;
  }
  ```

---

## 6. Strategi Pengujian & Jaminan Mutu (Testing Strategy)

### 6.1. Automated Feature Test (PHPUnit / Pest)
Pastikan test suite layout eksisting tetap lulus 100% tanpa regresi:
```bash
php artisan test --filter=DashboardLayoutTest
php artisan test --filter=AdminDashboardEnhancementTest
php artisan test --filter=PembimbingDashboardEnhancementTest
php artisan test --filter=SuperAdminDashboardRedesignTest
```
*Catatan Penting untuk Junior Dev*: Seluruh teks menu, teks role, dan tag `sr-only` (`Portal MagKom`, `Dinas Kominfo Sleman`, dll.) yang telah di-assert oleh unit test **TIDAK BOLEH DIHAPUS**.

### 6.2. Visual QA Matrix
Lakukan pengetesan visual manual pada browser dengan 3 akun pengujian bawaan:
1. **Administrasi**: `admin@magkom.test` ➔ Cek Topbar, Sidebar, dan tabel antrean kuota.
2. **Pembimbing**: `pembimbing@magkom.test` ➔ Cek Topbar, Sidebar, dan kartu peserta bimbingan.
3. **SuperAdmin**: `superadmin@magkom.test` ➔ Cek Topbar, Sidebar, dan cockpit telemetri.
4. **Theme Toggle Check**:
   - Klik tombol bulan/matahari di topbar kanan.
   - Pastikan warna beralih instan antara Mode Terang dan Mode Gelap tanpa kedipan putih (*flash of unstyled content*).
   - Pastikan teks pada kartu `#BBE1FA` di mode terang tetap terbaca sangat tajam (*high legibility*, kontras rasio > 7:1).
5. **Responsive Check**:
   - Layar Mobile (< 768px): Hamburger drawer terbuka mulus, topbar search menciut elegan.
   - Layar Desktop (1280px+): Sidebar statis lebar 230px, topbar sticky saat di-scroll.

---

## 7. Risiko & Mitigasi Khusus (Senior Advisory Guardrails)

1. **Risiko Cache View Blade**:
   - *Penyebab*: Modifikasi layout Blade terkadang tidak langsung terlihat di browser jika view cache aktif.
   - *Mitigasi*: Jalankan `php artisan view:clear` dan `php artisan optimize:clear` setelah file diubah.
2. **Risiko Bentrok CSS Gentelella**:
   - *Penyebab*: `gentelella-theme.css` memiliki selektor spesifik seperti `.left_col` dengan background default `#111C2E`.
   - *Mitigasi*: Gunakan specificity yang tepat di `<style>` layout atau sinkronkan langsung di `public/css/gentelella-theme.css`.
3. **Risiko Aksesibilitas Kontras Teks**:
   - *Penyebab*: Menaruh teks putih di atas card `#BBE1FA`.
   - *Mitigasi*: Wajib mengikat warna teks card `#BBE1FA` ke `#1B262C` atau `#0F4C75`.

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Header (Topbar) SuperAdmin, Pembimbing, dan Administrasi berlatar gradien biru gelap (`#0F4C75` ke `#1B262C`), teks putih, dan tombol hamburger/toggle bertransparansi halus identik dengan Peserta Magang.
- [ ] Tombol quick toggle mode gelap/terang hadir di sebelah tombol profil pengguna pada Topbar ketiga role tersebut.
- [ ] Sidebar ("sliderbar") berlatar belakang `#1B262C`, menu aktif berlatar `#0F4C75` dengan aksen border `#3282B8`.
- [ ] Card di Mode Terang mengadopsi `#BBE1FA` dengan tipografi teks gelap berstandar WCAG 2.1 AA.
- [ ] Card di Mode Gelap beralih rapi ke tone `#1B262C` dengan border halus `#3282B8`.
- [ ] Tombol utama (primary) dan elemen interaktif konsisten memakai `#0F4C75` / `#3282B8`.
- [ ] Semua tes otomatis (`php artisan test`) lolos hijau (0 errors, 0 failures).
- [ ] Kode bersih sesuai standar PSR-12 dan tidak ada inline styling yang bocor.
