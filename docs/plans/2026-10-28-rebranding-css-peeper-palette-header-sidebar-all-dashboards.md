# TASK SPECIFICATION & PRD: Rebranding Color Palette & Header-Sidebar Synchronization (SuperAdmin, Pembimbing, Administrasi, & Peserta)

- **Document ID**: `PRD-TASK-2026-10-28-UI-02`
- **Target Audience**: Junior Full-Stack Developer & AI Execution Agents (e.g., Gemini 3.8 Flash)
- **Author**: Senior Lead Frontend & UI/UX Systems Architect
- **Approved by**: Sir Alan
- **Target Roles**: SuperAdmin, Pembimbing Lapangan, Administrasi (Admin), dan Peserta Magang
- **Target Layout Files**:
  - [`resources/views/layouts/dashboard.blade.php`](file:///G:/MAGKOM/resources/views/layouts/dashboard.blade.php) (SuperAdmin, Pembimbing, Admin)
  - [`resources/views/layouts/peserta.blade.php`](file:///G:/MAGKOM/resources/views/layouts/peserta.blade.php) (Peserta Magang)
- **Secondary Asset Files**:
  - [`public/css/gentelella-theme.css`](file:///G:/MAGKOM/public/css/gentelella-theme.css)
  - [`public/css/dashboard-cockpit-unified.css`](file:///G:/MAGKOM/public/css/dashboard-cockpit-unified.css)

---

## 1. Executive Summary & Objective

Tugas ini bertujuan untuk:
1. **Menyeragamkan Header (Topbar) di 3 peran internal**:
   - **SuperAdmin**
   - **Pembimbing**
   - **Administrasi (Admin)**
   Ketiga header ini diubah agar **identik dalam warna, gaya gradien, dan visual hierarchy dengan Header Peserta Magang**.
2. **Mengadopsi Penuh Master Palette Baru (11 Warna CSS Peeper)** ke seluruh elemen navigasi (Header/Topbar, Sidebar, Breadcrumb, Search Box, User Chip), Card Surfaces (Mode Terang & Mode Gelap), Badges, Buttons, dan Border lines.
3. **Menerapkan Standar Card Khusus**:
   - Di **Mode Terang**, kartu metrik / highlight card mengadopsi warna dasar **`#01347C`** dengan tipografi kontras tinggi **`#FFFFFF`** dan aksen gold **`#FFCB05` / `#FFC300`**.
   - Di **Mode Gelap**, seluruh card dan kontainer beradaptasi ke kanvas gelap elegan (`#003566`, `#02275D`, atau `#212529`) dengan border tegas dan teks yang nyaman dibaca tanpa silau (*zero eye-strain*).
4. Menghasilkan spesifikasi teknis tingkat Senior Developer yang sangat presisi, modular, dan terstruktur tanpa celah ambiguitas (*bulletproof zero-regression*) sehingga dapat langsung dieksekusi oleh Junior Developer maupun model AI tanpa kebingungan.

---

## 2. Senior Lead Technical Critique & Trade-Off Analysis

Sebelum memulai penulisan kode, Junior Developer dan AI wajib memahami prinsip teknis di balik arsitektur visual ini:

### 2.1. Analisis Trade-Off Implementasi Card `#01347C` di Mode Terang
- **Karakteristik `#01347C`**:
  Warna `#01347C` adalah *Royal Corporate Navy* dengan nilai *relative luminance* sangat rendah (~0.045). Ini adalah warna yang sangat berwibawa, tegas, dan mencerminkan instansi pemerintahan modern.
- **Critical Risk (Aksesibilitas & Kontras WCAG 2.1 AA/AAA)**:
  - ⚠️ **DILARANG KERAS** menggunakan warna teks gelap (`#212529` atau `#000000`) di atas permukaan kartu `#01347C`! Rasio kontrasnya hanya 1.3:1 (Gagal total / *Unreadable*).
  - ✅ **Wajib**: Teks judul, metrik angka, dan label di dalam kartu `#01347C` **WAJIB** berwarna putih murni (`#FFFFFF`, rasio kontras 8.5:1 = AAA) atau soft silver (`#BBBBBB` / `#AAAAAA` untuk subtitle, rasio 4.5:1+).
  - ✅ **Aksen Gold**: Badge status, pill penanda, atau persentase kenaikan di dalam kartu `#01347C` wajib menggunakan `#FFCB05` atau `#FFC300` (rasio kontras 5.5:1+ = AA).
- **Hierarki Kartu (Card Distribution)**:
  - **Stat / Metric Cards & Hero Summary Banners**: Sangat direkomendasikan menggunakan background solid `#01347C` (atau linear gradient `#01347C` ke `#003566`). Ini menciptakan kesan *punchy*, profesional, dan langsung memusatkan perhatian pada angka penting.
  - **Data Table Cards & Long Forms**: Untuk tabel data ratusan baris atau form isian panjang, gunakan background `#FFFFFF` dengan garis aksen atas (top border) `3px solid #01347C` atau header tabel `#01347C`. Hal ini mencegah *visual clutter* saat staf administrasi menatap layar berjam-jam.

### 2.2. Mengapa Menggunakan Centralized CSS Variables (Bukan Utility Hardcoding)
- **Single Source of Truth**: Dengan mendefinisikan 11 warna baru ke dalam `:root` dan `[data-theme="dark"]` pada master layout [`dashboard.blade.php`](file:///G:/MAGKOM/resources/views/layouts/dashboard.blade.php), kita menyelaraskan 50+ view Blade secara instan tanpa perlu mengedit file per halaman secara manual.
- **Seamless Dual-Theme Switch**: Pengguna dapat berpindah dari Mode Terang ke Mode Gelap secara instan melalui atribut `[data-theme="dark"]` tanpa komputasi ulang JavaScript yang berat.

---

## 3. Kamus Master Palet Warna (CSS Peeper Tokens)

Berikut adalah 11 warna resmi yang diekstraksi dan wajib digunakan di seluruh ekosistem Portal MagKom:

| Hex Code | Nama Semantik Token | Peran Utama di Mode Terang (Light) | Peran Utama di Mode Gelap (Dark) | WCAG Contrast Safe Text |
| :--- | :--- | :--- | :--- | :--- |
| **`#01347C`** | `--magkom-primary-blue` | **Header Gradient Start**, Primary Button, **Solid Card Background (Metric)** | Active Menu Background, Accent Glow, Card Border Highlight | `#FFFFFF`, `#FFCB05` |
| **`#003566`** | `--magkom-midnight-navy`| **Header Gradient End**, Topbar Shadow, Hover Button | **Card Surface Container**, Panel Header, Deep Container | `#FFFFFF`, `#BBBBBB` |
| **`#02275D`** | `--magkom-abyssal-navy` | **Sidebar Background**, Footer Bar, Master Brand Anchors | **Sidebar Background Dark**, Elevated Modal Surface | `#FFFFFF`, `#BBBBBB` |
| **`#3F51B5`** | `--magkom-indigo-accent`| Secondary Interactive Action, Focus Ring, Link Text | Bright Interactive Button, Glowing Active Tabs | `#FFFFFF` |
| **`#FFC300`** | `--magkom-amber-gold` | Active Sidebar Left Indicator, Warning Badges, Star Rating | Glowing Status Warning, Warning Border, Metric Accents | `#000000`, `#02275D` |
| **`#FFCB05`** | `--magkom-sun-yellow` | Metric Highlight Pill di dalam card `#01347C`, Gold Hover | Gold Accent Badge, Stat Trend Up Indicator | `#000000`, `#01347C` |
| **`#212529`** | `--magkom-dark-charcoal`| Teks Utama pada Kartu Putih, Dark Modal Headers | **Main Canvas/Body Background**, Darkest Dropdown Menu | `#FFFFFF`, `#BBBBBB` |
| **`#000000`** | `--magkom-pure-black` | Bayangan Box Shadow (`rgba(0,0,0,...)`), Pembatas Kontras | Deep Canvas Base, High-Contrast Borders | `#FFFFFF` |
| **`#AAAAAA`** | `--magkom-gray-medium` | Placeholder Input, Divider Sekunder, Muted Caption | Teks Subtitle / Label Muted, Border Netral | `#000000` (Light) / `#FFFFFF` (Dark) |
| **`#BBBBBB`** | `--magkom-gray-light` | Border Kartu Netral, Inactive Sidebar Links, Divider | Teks Sekunder Body, Border Kartu Mode Gelap | `#212529` (Light) / `#000000` (Dark) |
| **`#FFFFFF`** | `--magkom-pure-white` | Surface Kartu Tabel/Form, Teks Header & Sidebar | Teks Utama Heading, High-Contrast Text Element | `#01347C`, `#212529` |

---

## 4. Arsitektur Komponen: Spesifikasi Teknis Per Area

### 4.1. Header (Topbar Navigation) — Harmonisasi 100% dengan Peserta
Header pada SuperAdmin, Pembimbing, dan Admin diselaraskan agar identik dengan Peserta:
- **Tinggi Bar**: `64px` (Standar MagKom Topbar).
- **Background Mode Terang**:
  ```css
  background: linear-gradient(135deg, #01347C 0%, #003566 100%) !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.14) !important;
  box-shadow: 0 4px 20px -2px rgba(1, 52, 124, 0.25) !important;
  ```
- **Background Mode Gelap**:
  ```css
  background: linear-gradient(135deg, #02275D 0%, #003566 100%) !important;
  border-bottom: 1px solid rgba(63, 81, 181, 0.35) !important;
  box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.6) !important;
  ```
- **Elemen Internal Topbar**:
  - **Menu Toggle (Hamburger)**: Warna icon `#FFFFFF`, background hover `rgba(255, 255, 255, 0.12)`.
  - **Breadcrumbs Trail**:
    - Link teks: `rgba(255, 255, 255, 0.75)` (hover: `#FFCB05`).
    - Separator `/`: `rgba(255, 255, 255, 0.4)`.
    - Active crumb: `#FFFFFF` (font-weight: 600).
  - **Search Input Bar (`.topbar-search-box`)**:
    - Background: `rgba(255, 255, 255, 0.12)`.
    - Border: `1px solid rgba(255, 255, 255, 0.22)`.
    - Teks input & icon: `#FFFFFF`, placeholder: `rgba(255, 255, 255, 0.65)`.
    - Focus state: background `rgba(255, 255, 255, 0.2)`, border-color `#FFCB05`, ring `0 0 0 3px rgba(255, 203, 5, 0.25)`.
  - **Theme Toggle Quick Button**:
    - Background: `rgba(255, 255, 255, 0.12)`, hover: `rgba(255, 255, 255, 0.22)`.
    - Icon color: `#FFCB05` (Sun) / `#BBBBBB` (Moon).
  - **User Profile Chip**:
    - Container: background `rgba(255, 255, 255, 0.12)`, border `1px solid rgba(255, 255, 255, 0.2)`.
    - User name: `#FFFFFF` (font-weight: 600).
    - Role badge: background `#FFC300`, teks `#02275D` (font-weight: 700).

---

### 4.2. Sidebar Navigation (Gentelella `left_col`)
Sidebar menyatu secara elegan dengan palet Abyssal & Midnight Navy:
- **Background Utama**: `#02275D` (solid, berwibawa).
- **Header Brand (Logo Sleman & Judul MagKom)**:
  - Border bawah: `1px solid rgba(255, 255, 255, 0.1)`.
  - Title text: `#FFFFFF`, Sub-title: `#BBBBBB`.
- **Menu Items (`.nav.side-menu li a`)**:
  - Warna default link: `#BBBBBB`.
  - Icon menu: `#AAAAAA` (hover ke `#FFFFFF`).
  - Hover state: background `rgba(63, 81, 181, 0.25)`, text `#FFFFFF`.
  - **Active State (`.active`)**:
    - Background: `linear-gradient(90deg, rgba(1, 52, 124, 0.8) 0%, rgba(2, 39, 93, 0.4) 100%)`.
    - Border kiri (Active Indicator): `4px solid #FFC300` (Kominfo Amber Gold).
    - Text: `#FFFFFF` (font-weight: 600).
    - Icon: `#FFCB05`.
- **Sidebar Footer Buttons**:
  - Background: `#003566`, border-top: `1px solid rgba(255, 255, 255, 0.1)`.
  - Icon button hover: text `#FFCB05`, background `rgba(255, 255, 255, 0.1)`.

---

### 4.3. Card Styling: Strategi Dual-Theme (Sesuai Permintaan Sir Alan)

Sesuai instruksi spesifik Sir Alan: *"di sesuaikan lagi dengan kecocokan themes dari ketiga warnat tersebut di mode gelap dan mode terang jadi mungkin contoh nya ketika mode terang card nya memakai warna #01347C"*:

#### A. Mode Terang (Light Mode)
1. **Stat Cards & Metric Highlights (`.cv-stat-card`, `.metric-card-highlight`)**:
   - **Background**: `#01347C` (Solid Sleman Primary Blue).
   - **Border**: `1px solid #3F51B5` atau `1px solid rgba(255, 255, 255, 0.15)`.
   - **Box Shadow**: `0 10px 20px -3px rgba(1, 52, 124, 0.25), 0 4px 6px -4px rgba(1, 52, 124, 0.15)`.
   - **Judul Metrik**: `#BBBBBB` (uppercase, letter-spacing: 0.05em, font-weight: 600).
   - **Angka Metrik (Hero Value)**: `#FFFFFF` (font-weight: 700, font-size: 1.75rem - 2.25rem).
   - **Badge Trend / Status**:
     - Background: `#FFCB05`.
     - Teks: `#02275D` (font-weight: 700).
   - **Sub-label / Keterangan**: `rgba(255, 255, 255, 0.8)`.

2. **Content Cards, Tables & Form Containers (`.cv-card`, `.card`, `.x_panel`)**:
   - **Background**: `#FFFFFF` (permukaan bersih untuk keterbacaan data maksimal).
   - **Border**: `1px solid #BBBBBB`.
   - **Aksen Garis Atas (Top Accent Ribbon)**: `3px solid #01347C`.
   - **Header Card (`.x_title`, `.cv-card-header`)**:
     - Judul: `#212529` (font-weight: 700).
     - Border bawah: `1px solid #BBBBBB`.
   - **Table Rows**:
     - Header tabel (`<th>`): background `#01347C`, text `#FFFFFF`.
     - Zebra stripe: selang-seling `#FFFFFF` dan `#F8FAFC`.
     - Hover row: background `rgba(1, 52, 124, 0.06)`.
     - Teks data: `#212529`.

#### B. Mode Gelap (Dark Mode)
1. **Canvas Body**:
   - Background: `#212529` (atau `#121820`).
2. **Seluruh Cards (`.cv-stat-card`, `.cv-card`, `.card`, `.x_panel`)**:
   - **Background**: `#003566` (Midnight Navy Container) atau `#02275D`.
   - **Border**: `1px solid #3F51B5` (memberikan efek *subtle glowing contour* yang sangat premium).
   - **Box Shadow**: `0 8px 24px rgba(0, 0, 0, 0.5)`.
   - **Judul Card & Metrik**:
     - Judul / label: `#BBBBBB`.
     - Angka Metrik: `#FFFFFF` (kontras penuh).
   - **Table Rows di Mode Gelap**:
     - Header tabel (`<th>`): background `#02275D`, text `#FFFFFF`, border-bottom `1px solid #3F51B5`.
     - Body rows: background `#003566`, border-bottom `1px solid rgba(255, 255, 255, 0.08)`.
     - Hover row: background `rgba(63, 81, 181, 0.25)`.
     - Teks data: `#FFFFFF` / `#BBBBBB`.

---

## 5. Implementation Code Blueprint (Concrete Diffs)

Junior Developer / AI harus menerapkan kode CSS berikut ke dalam layout master dan stylesheet utama:

### 5.1. Update Master Tokens di [`resources/views/layouts/dashboard.blade.php`](file:///G:/MAGKOM/resources/views/layouts/dashboard.blade.php)

Cari blok `:root` dan `[data-theme="dark"]` di bagian `<style>` dalam file [`dashboard.blade.php`](file:///G:/MAGKOM/resources/views/layouts/dashboard.blade.php), lalu gantikan dengan:

```css
:root {
    /* =========================================================================
       11 Master CSS Peeper Tokens - MagKom Rebranded Palette
       ========================================================================= */
    --magkom-primary-blue: #01347C;
    --magkom-midnight-navy: #003566;
    --magkom-abyssal-navy: #02275D;
    --magkom-indigo-accent: #3F51B5;
    --magkom-amber-gold: #FFC300;
    --magkom-sun-yellow: #FFCB05;
    --magkom-dark-charcoal: #212529;
    --magkom-pure-black: #000000;
    --magkom-gray-medium: #AAAAAA;
    --magkom-gray-light: #BBBBBB;
    --magkom-pure-white: #FFFFFF;

    /* Semantic Aliases - Mode Terang */
    --blue-600: var(--magkom-primary-blue);
    --blue-700: var(--magkom-midnight-navy);
    --blue-800: var(--magkom-abyssal-navy);
    --blue-500: var(--magkom-indigo-accent);
    --blue-tint: rgba(1, 52, 124, 0.08);
    --blue-tint-line: var(--magkom-indigo-accent);

    /* Topbar & Header Tokens (Identik dengan Peserta) */
    --topbar-bg-start: var(--magkom-primary-blue);
    --topbar-bg-end: var(--magkom-midnight-navy);
    --topbar-border: rgba(255, 255, 255, 0.14);
    --topbar-text: var(--magkom-pure-white);
    --topbar-text-muted: var(--magkom-gray-light);

    /* Sidebar Tokens */
    --sidebar-bg: var(--magkom-abyssal-navy);
    --sidebar-text: var(--magkom-gray-light);
    --sidebar-active-bg: var(--magkom-primary-blue);
    --sidebar-active-border: var(--magkom-amber-gold);

    /* Card Tokens (Mode Terang) */
    --card-surface: var(--magkom-pure-white);
    --card-border: var(--magkom-gray-light);
    --card-text-primary: var(--magkom-dark-charcoal);
    --card-text-muted: var(--magkom-gray-medium);

    /* Highlight / Metric Card Khusus (#01347C) */
    --card-highlight-bg: var(--magkom-primary-blue);
    --card-highlight-border: var(--magkom-indigo-accent);
    --card-highlight-text: var(--magkom-pure-white);
    --card-highlight-badge-bg: var(--magkom-sun-yellow);
    --card-highlight-badge-text: var(--magkom-abyssal-navy);

    /* Status Tokens */
    --green-100: #E8F7EE;
    --green-600: #16A34A;
    --green-700: #166534;
    --amber-100: #FFFBEB;
    --amber-200: #FEF3C7;
    --amber-300: var(--magkom-sun-yellow);
    --amber-700: #92400E;
    --red-100: #FEF2F2;
    --red-600: #DC2626;
    --red-700: #991B1B;

    --sidebar-width: 230px;
    --topbar-height: 64px;
}

/* =========================================================================
   Mode Gelap (Dark Mode Tokens)
   ========================================================================= */
[data-theme="dark"] {
    --neutral-0: var(--magkom-midnight-navy);
    --neutral-50: var(--magkom-abyssal-navy);
    --neutral-100: var(--magkom-midnight-navy);
    --neutral-150: var(--magkom-dark-charcoal);
    --neutral-200: #2C3A4E;
    --neutral-300: var(--magkom-indigo-accent);
    --neutral-400: var(--magkom-gray-medium);
    --neutral-500: var(--magkom-gray-light);
    --neutral-700: #E2E8F0;
    --neutral-800: var(--magkom-pure-white);
    --neutral-900: var(--magkom-pure-white);

    /* Topbar Mode Gelap */
    --topbar-bg-start: var(--magkom-abyssal-navy);
    --topbar-bg-end: var(--magkom-midnight-navy);
    --topbar-border: rgba(63, 81, 181, 0.35);

    /* Sidebar Mode Gelap */
    --sidebar-bg: #011C44;
    --sidebar-text: var(--magkom-gray-light);
    --sidebar-active-bg: var(--magkom-primary-blue);
    --sidebar-active-border: var(--magkom-sun-yellow);

    /* Card Mode Gelap */
    --card-surface: var(--magkom-midnight-navy);
    --card-border: var(--magkom-indigo-accent);
    --card-text-primary: var(--magkom-pure-white);
    --card-text-muted: var(--magkom-gray-light);

    --card-highlight-bg: var(--magkom-abyssal-navy);
    --card-highlight-border: var(--magkom-indigo-accent);
    --card-highlight-text: var(--magkom-pure-white);
    --card-highlight-badge-bg: var(--magkom-sun-yellow);
    --card-highlight-badge-text: var(--magkom-pure-black);
}
```

---

### 5.2. Update Topbar & Sidebar Rules di [`public/css/gentelella-theme.css`](file:///G:/MAGKOM/public/css/gentelella-theme.css)

Pastikan selektor `.left_col` dan `.top_nav` menggunakan variabel semantik baru:

```css
/* Sidebar Navigasi */
.left_col {
    background-color: var(--sidebar-bg, #02275D) !important;
    color: var(--sidebar-text, #BBBBBB);
}

.sidebar-header {
    background-color: transparent;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.nav.side-menu > li > a {
    color: var(--sidebar-text, #BBBBBB) !important;
}

.nav.side-menu > li > a:hover {
    color: #FFFFFF !important;
    background-color: rgba(63, 81, 181, 0.25) !important;
}

.nav.side-menu > li.active > a {
    background: linear-gradient(90deg, rgba(1, 52, 124, 0.8) 0%, rgba(2, 39, 93, 0.4) 100%) !important;
    color: #FFFFFF !important;
    border-left: 4px solid var(--magkom-amber-gold, #FFC300) !important;
}

/* Topbar Header Identik Peserta Magang */
.top_nav, .dashboard-topbar-wrapper {
    background: linear-gradient(135deg, var(--topbar-bg-start, #01347C) 0%, var(--topbar-bg-end, #003566) 100%) !important;
    border-bottom: var(--topbar-border, 1px solid rgba(255, 255, 255, 0.14)) !important;
    box-shadow: 0 4px 20px -2px rgba(1, 52, 124, 0.25) !important;
}

.nav_menu {
    background: transparent !important;
}

.breadcrumb-trail a {
    color: rgba(255, 255, 255, 0.8) !important;
}

.breadcrumb-trail a:hover {
    color: #FFCB05 !important;
}

.breadcrumb-current {
    color: #FFFFFF !important;
    font-weight: 600;
}
```

---

### 5.3. Update Cockpit Cards di [`public/css/dashboard-cockpit-unified.css`](file:///G:/MAGKOM/public/css/dashboard-cockpit-unified.css)

Pastikan kartu metrik cockpit mengimplementasikan warna `#01347C` di Mode Terang dan adaptasi gelap:

```css
/* Stat / Metric Card dengan Warna Baru #01347C */
.cv-stat-card {
    background-color: var(--card-highlight-bg, #01347C) !important;
    border: 1px solid var(--card-highlight-border, #3F51B5) !important;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 10px 20px -3px rgba(1, 52, 124, 0.22);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.cv-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 24px -2px rgba(1, 52, 124, 0.32);
}

.cv-stat-card .cv-stat-label {
    color: var(--magkom-gray-light, #BBBBBB) !important;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.cv-stat-card .cv-stat-value {
    color: var(--magkom-pure-white, #FFFFFF) !important;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1.2;
}

.cv-stat-card .cv-stat-badge {
    background-color: var(--magkom-sun-yellow, #FFCB05) !important;
    color: var(--magkom-abyssal-navy, #02275D) !important;
    font-weight: 700;
    border-radius: 9999px;
    padding: 0.25rem 0.625rem;
    font-size: 0.75rem;
}

/* Standard Content Cards (Tabel & Form) */
.cv-card, .x_panel {
    background-color: var(--card-surface, #FFFFFF) !important;
    border: 1px solid var(--card-border, #BBBBBB) !important;
    border-top: 3px solid var(--magkom-primary-blue, #01347C) !important;
    border-radius: 12px;
}
```

---

## 6. Step-by-Step Execution Plan (Runbook for Junior Dev / AI)

Ikuti urutan langkah berikut secara disiplin untuk mencegah regresi:

- [x] **Langkah 1: Audit & Verifikasi File Eksisting**
  - Periksa file [`dashboard.blade.php`](file:///G:/MAGKOM/resources/views/layouts/dashboard.blade.php) pada baris 50-180.
  - Periksa file [`peserta.blade.php`](file:///G:/MAGKOM/resources/views/layouts/peserta.blade.php) pada baris 50-130 dan 849-865.
  - Periksa file [`gentelella-theme.css`](file:///G:/MAGKOM/public/css/gentelella-theme.css) dan [`dashboard-cockpit-unified.css`](file:///G:/MAGKOM/public/css/dashboard-cockpit-unified.css).

- [x] **Langkah 2: Terapkan Token Baru pada `:root` dan `[data-theme="dark"]`**
  - Perbarui variabel CSS di [`dashboard.blade.php`](file:///G:/MAGKOM/resources/views/layouts/dashboard.blade.php) sesuai Sub-bab 5.1.
  - Pastikan variabel lama (`--brand-darkest`, `--brand-navy`, dll.) dipetakan secara aman ke token baru tanpa menghapus alias yang mungkin digunakan di sub-view.

- [x] **Langkah 3: Terapkan Styling Header Topbar & Sidebar di Gentelella CSS**
  - Perbarui class `.top_nav`, `.dashboard-topbar-wrapper`, `.left_col`, dan menu navigasi di [`gentelella-theme.css`](file:///G:/MAGKOM/public/css/gentelella-theme.css).
  - Pastikan gradien warna header persis menggunakan `#01347C` ke `#003566` pada Mode Terang dan `#02275D` ke `#003566` pada Mode Gelap.

- [x] **Langkah 4: Terapkan Styling Card (#01347C & Dual-Theme)**
  - Perbarui style `.cv-stat-card`, `.cv-card`, dan `.x_panel` di [`dashboard-cockpit-unified.css`](file:///G:/MAGKOM/public/css/dashboard-cockpit-unified.css).
  - Pastikan teks di atas card `#01347C` berwarna putih (`#FFFFFF`) dan badge menggunakan kuning/gold (`#FFCB05`).

- [x] **Langkah 5: Penyelarasan Layout Peserta Magang**
  - Pastikan token di [`peserta.blade.php`](file:///G:/MAGKOM/resources/views/layouts/peserta.blade.php) juga menyelaraskan variabel primer `--blue-600` dan `--navy` dengan `#01347C` dan `#02275D` agar seluruh website 100% konsisten.

- [x] **Langkah 6: Validasi Visual & Cross-Role Smoke Test**
  - Buka browser dan login sebagai:
    1. **SuperAdmin** (`/superadmin/dashboard`)
    2. **Pembimbing** (`/pembimbing/dashboard`)
    3. **Admin** (`/admin/dashboard`)
    4. **Peserta** (`/peserta/dashboard`)
  - Uji tombol pergantian tema (Terang ➔ Gelap ➔ Sistem). Pastikan tidak ada teks yang hilang atau tidak terbaca (*zero contrast violation*).

---

## 7. Verification Checklist & Definition of Done (DoD)

Tugas ini dinyatakan selesai jika dan hanya jika seluruh kriteria berikut terpenuhi:

1. [x] **Header Parity**: Header Topbar di SuperAdmin, Pembimbing, dan Administrasi memiliki warna dasar gradien (`#01347C` ke `#003566`), tinggi bar, dan styling search box yang harmonis dengan Peserta Magang.
2. [x] **11 Master Colors Compliance**: Tidak ada warna sembarangan di luar palet 11 CSS Peeper (`#01347C`, `#003566`, `#02275D`, `#3F51B5`, `#FFC300`, `#FFCB05`, `#212529`, `#000000`, `#AAAAAA`, `#BBBBBB`, `#FFFFFF`).
3. [x] **Light Mode Card Standard**: Kartu metrik utama tampil tegas dan elegan dengan background `#01347C`, teks judul `#BBBBBB`, angka metrik putih `#FFFFFF`, dan pill badge `#FFCB05`.
4. [x] **Dark Mode Card Standard**: Seluruh kartu di mode gelap beradaptasi ke background `#003566` / `#02275D` dengan border `#3F51B5` dan teks putih `#FFFFFF`.
5. [x] **WCAG 2.1 AA Compliance**: Seluruh teks normal memiliki rasio kontras minimal 4.5:1, dan teks besar/angka memiliki rasio minimal 3:1.
6. [x] **Zero Functional Regression**: Menu dropdown profil, tombol toggle tema, toggle sidebar drawer, dan breadcrumbs berfungsi lancar di desktop maupun mobile.
