# Senior Engineering Handbook & Implementation Plan: Redesain Section Pengumuman & Layanan Landing Page (Anti-AI-Slop & Enterprise GovTech Standard)

- **Dokumen ID**: `PLAN-2026-10-15-LANDING-PENGUMUMAN-LAYANAN-REVAMP`
- **Tanggal**: 2026-10-15
- **Status**: Siap Diimplementasikan (Ready for Execution)
- **Author**: Lead Principal Software Architect & Head of Design Experience
- **Target Pelaksana**: Junior Full-Stack Developer / Fast LLM Executor (e.g. Gemini 3.8 Flash)
- **File Target**: `resources/views/public/landing.blade.php`
- **Target URL**: `http://127.0.0.1:8000/` (Section `#pengumuman-section` & `#layanan-section`)
- **Skill Compliance**:
  - `better-ui`: Surfaces with subtle depth, concentric border radius, optical alignment, micro-interactions, SVG icons with `currentColor`.
  - `better-typography`: Strict typographic hierarchy, balance text wraps, tabular numbers for dates/metrics, line heights 1.25–1.6.
  - `better-layout`: Grouping with 2x gap rules, logical properties (`padding-block`, `margin-inline`), responsive CSS Grid.
  - `better-colors`: Official Sleman palette, semantic color ramps (Info, Success, Warning, Neutral), WCAG AAA/AA 4.5:1+ contrast.
  - `better-writing`: Indonesian formal yet conversational (kedinasan modern), action-oriented verbs, clear context without robot/AI slop.
  - `impeccable`: Premium GovTech aesthetic, zero bland template feel, zero neon purple gradient slop, polished modals & focus states.
  - `ui-ux-pro-max`: Rich cards with target audience tags, modal reader for announcements, smooth scroll trigger, 44px touch targets.

---

## 1. Problem Statement & Audit Visual Eksisting

### 1.1 Masalah pada Section Pengumuman Eksisting
1. **Tipografi & Header Datar (Flat)**:
   - Hanya berupa `<h2>Pengumuman</h2>` tanpa *eyebrow badge*, tanpa subjudul pengantar, dan tanpa visual anchor. Terlihat seperti draft kawat kasar (*wireframe*).
2. **Kartu Statis & Tanpa Hierarki Konten**:
   - Kartu pengumuman hanya berupa kotak putih datar dengan border tipis kelabu. Tidak ada badge kategori (misal: "Pendaftaran", "Persyaratan", "Hasil Seleksi").
   - Tanggal hanya berupa teks polos kecil di pojok bawah (`28 Sep 2026`), tanpa aksen kalender atau penanda status (seperti label "Baru" atau "Disematkan / Pin").
3. **Ketiadaan Interaksi (Dead-End Card)**:
   - Pengumuman tidak bisa diklik! Pengguna hanya membaca kutipan 1 kalimat tanpa tahu bagaimana membaca rincian lengkapnya, nomor surat keputusan resminya, atau cara mengunduh lampiran surat edaran format PDF.
4. **Ketiadaan Filter Kategori**:
   - Semua pengumuman bertumpuk tanpa filter interaktif (misal tab "Semua", "Pendaftaran", "Hasil Seleksi").

### 1.2 Masalah pada Section Layanan Eksisting
1. **Kotak Ikon Miskin Konteks (Zero Context / AI Slop)**:
   - Terdiri dari 4 kotak persegi kecil yang hanya memuat satu ikon SVG generik dan 2 kata teks: `"Daftar Magang"`, `"Cek Status"`, `"Logbook"`, `"Lembar Nilai"`.
   - Tidak ada deskripsi ringkas mengenai apa fungsi layanan tersebut, siapa sasaran penggunanya (calon peserta, peserta aktif, atau pembimbing), dan bagaimana alurnya.
2. **Ketiadaan *Visual Affordance* & Call-to-Action**:
   - Tidak ada indikator interaksi (panah navigasi `→`, hover glow, micro-badge status) yang mengkomunikasikan bahwa elemen tersebut adalah kartu aksi (*interactive service launcher*).
3. **Penyatuan Visual yang Hambar**:
   - Menggunakan warna latar belakang abu-abu terang datar (`--neutral-100`) tanpa variasi elevasi permukaan, sehingga tampak monoton dan menyerupai template bootstrap instan murahan.

---

## 2. Visi Desain Baru: "Modern GovTech Service & Editorial Hub"

| Elemen | Desain Lama (AI Slop / Datar) | Desain Baru (Standar Senior Craft) |
| :--- | :--- | :--- |
| **Header Section** | `<h2>` teks biasa di tengah | Eyebrow capsule dengan ikon pulsa + H2 kontras tinggi + subjudul deskriptif dengan `text-wrap: balance` |
| **Pengumuman Cards** | 3 kotak putih statis, teks 1 kalimat | Kartu editorial modern: Badge kategori warna semantik, badge "Terbaru", judul berbobot, ringkasan, tanggal dengan ikon kalender, dan tombol "Baca Selengkapnya →" |
| **Interaktivitas Pengumuman** | Statis mati (tidak bisa diklik) | **Modal Warta Resmi Interaktif**: Pop-up popover dengan kop surat resmi Diskominfo Sleman, nomor surat edaran, isi surat lengkap, serta tombol unduh lampiran PDF |
| **Filter Pengumuman** | Tidak ada | Filter kapsul kategori interaktif (*All*, *Pendaftaran*, *Persyaratan*, *Pengumuman Hasil*) |
| **Layanan Cards** | 4 kotak bujur sangkar kecil 1 baris teks | **4 Enterprise Action Tiles**: Icon box dengan ambient radial gradient, badge sasaran pengguna (*Calon Peserta*, *Peserta Aktif*, *Alumni/Evaluasi*), judul layanan, deskripsi edukatif 2 baris, dan link CTA dengan panah animasi |
| **Mode Gelap (Dark Mode)** | Latar belakang flat gray biasa | Kontras gelap slate `#111C2E` bertekstur, border subtle `#1E293B`, kartu berpendar lembut saat hover |

---

## 3. Matriks 7 Desain Skill

### 3.1 `better-ui`
- **Concentric Radius**: Kartu luar `border-radius: 1rem (16px)`, icon container dalam `border-radius: 0.75rem (12px)`, badge dalam `border-radius: 9999px (full pill)`.
- **Micro-Animations**: Hover card mengangkat halus `transform: translateY(-4px)` dengan bayangan lembut bergradasi `box-shadow: 0 16px 32px -8px rgba(7, 81, 255, 0.12)`.
- **Panah Navigasi Dinamis**: Ikon panah CTA bergeser ke kanan `transform: translateX(4px)` saat kartu di-hover.
- **Accessible Focus Ring**: Setiap kartu dan tombol memiliki `:focus-visible` dengan outline `2px solid #0751FF` dan `outline-offset: 2px`.

### 3.2 `better-typography`
- **Scale & Hierarchy**:
  - Eyebrow: `font-size: 0.8125rem (13px)`, `font-weight: 700`, `letter-spacing: 0.05em`, uppercase.
  - Heading 2: `font-size: 2rem (32px)` desktop / `1.625rem` mobile, `font-weight: 800`, `letter-spacing: -0.025em`, `line-height: 1.2`.
  - Judul Kartu: `font-size: 1.125rem (18px)`, `font-weight: 700`, `line-height: 1.4`.
  - Body Copy: `font-size: 0.9375rem (15px)`, `line-height: 1.6`, warna `--neutral-600` (WCAG AAA).
  - Angka & Tanggal: `font-variant-numeric: tabular-nums`.

### 3.3 `better-layout`
- **Gap Hierarchy (2x Rule)**:
  - Antara ikon dan teks dalam kartu: `gap: 0.75rem (12px)`.
  - Antara judul dan deskripsi dalam kartu: `gap: 0.5rem (8px)`.
  - Antar kartu dalam grid: `gap: 1.5rem (24px)`.
  - Antara header section dan grid konten: `gap: 3rem (48px)`.
- **Grid Responsif**:
  - Pengumuman: Desktop 3 kolom (`repeat(3, 1fr)`), Tablet 2 kolom, Mobile 1 kolom.
  - Layanan: Desktop 4 kolom (`repeat(4, 1fr)`), Tablet 2 kolom (`repeat(2, 1fr)`), Mobile 1 kolom.

### 3.4 `better-colors`
- **Sleman Official Brand**: Sleman Blue (`#0751FF`), Deep Navy Slate (`#0F172A`).
- **Semantic Badges**:
  - **Pendaftaran / Info Umum**: Biru Sleman (Background `#EFF6FF`, Border `#BFDBFE`, Text `#1D4ED8`).
  - **Persyaratan / Perhatian**: Amber (Background `#FFFBEB`, Border `#FDE68A`, Text `#B45309`).
  - **Hasil Seleksi / Kelulusan**: Emerald (Background `#ECFDF5`, Border `#A7F3D0`, Text `#047857`).
  - **Sasaran Pengguna**: Indigo / Slate (Background `#F1F5F9`, Border `#CBD5E1`, Text `#334155`).

### 3.5 `better-writing`
- Menghapus frasa generik/mesin.
- Menggunakan bahasa Indonesia baku, santun, lugas, dan mengarahkan aksi:
  - *"Warta & Informasi Resmi"* (Bukan sekadar "Pengumuman").
  - *"Pusat Layanan & Fitur Utama"* (Bukan sekadar "Layanan").
  - Tombol CTA: *"Baca Pengumuman Lengkap &rarr;"*, *"Lihat Kuota & Ajukan &rarr;"*, *"Pantau Progres Seleksi &rarr;"*.

### 3.6 `impeccable`
- Menghadirkan detail craft tingkat tinggi:
  - Radial gradient spot di pojok kartu layanan yang senada dengan ikon.
  - Tanggal dengan chip kalender yang rapi.
  - Modal warta resmi lengkap dengan stempel digital / kop instansi.
  - Zero layout shift (CLS) saat filter tab diganti.

### 3.7 `ui-ux-pro-max`
- Target sentuh minimal 44x44px untuk mobile.
- Filter tab dengan keyboard navigation (`role="tablist"`).
- In-page modal preview yang cepat tanpa perlu reload halaman.

---

## 4. Rincian Teknis Implementasi (Untuk Junior Dev / LLM Executor)

### 4.1 Target File & Perubahan
Seluruh modifikasi dilakukan pada:
- **File**: `resources/views/public/landing.blade.php`
- **Bagian CSS**: Di dalam tag `<style>` yang berada di dalam section styling landing page (mengganti blok CSS baris ~1120 s/d 1255).
- **Bagian HTML**: Mengganti blok HTML `#pengumuman-section` dan `#layanan-section` (baris ~2080 s/d 2170).
- **Bagian JS**: Menambahkan logika interaktif (filter tab dan modal dialog) di dalam tag `<script>` pada `@push('scripts')`.

---

## 5. Blueprint Kode Lengkap (Ready-to-Use)

### 5.1 Spesifikasi CSS (Pengganti Style Lama)

Junior Dev / AI Executor diinstruksikan untuk mengganti deklarasi CSS `.section-pengumuman`, `.pengumuman-grid`, `.pengumuman-card`, `.section-layanan`, `.layanan-grid`, dan `.layanan-card` dengan kode CSS terstruktur berikut:

```css
    /* =====================================================================
       4. PENGUMUMAN SECTION (MODERN EDITORIAL GOVTECH SPEC)
       ===================================================================== */
    .section-pengumuman {
        padding: 6rem 0 5.5rem;
        background: var(--neutral-0, #ffffff);
        position: relative;
    }

    .pengumuman-header-wrap {
        text-align: center;
        max-width: 680px;
        margin: 0 auto 2.5rem;
    }

    .pengumuman-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.875rem;
        border-radius: 9999px;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        color: #1D4ED8;
        font-size: 0.8125rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 0.875rem;
    }

    .pengumuman-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #2563EB;
        box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7);
        animation: pulseDot 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
    }

    @keyframes pulseDot {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7);
        }
        50% {
            box-shadow: 0 0 0 6px rgba(37, 99, 235, 0);
        }
    }

    .pengumuman-title {
        font-size: 2.125rem;
        font-weight: 800;
        color: var(--neutral-900, #0F172A);
        letter-spacing: -0.025em;
        line-height: 1.25;
        margin-bottom: 0.75rem;
        text-wrap: balance;
    }

    .pengumuman-subtitle {
        font-size: 0.95rem;
        color: var(--neutral-600, #475569);
        line-height: 1.6;
        text-wrap: balance;
    }

    /* Filter Tabs Kategori Pengumuman */
    .pengumuman-filter-bar {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 3rem;
    }

    .pengumuman-tab-btn {
        appearance: none;
        background: #F8FAFC;
        border: 1px solid var(--neutral-300, #E2E8F0);
        color: var(--neutral-700, #334155);
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.5rem 1.15rem;
        border-radius: 9999px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.2, 0, 0, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pengumuman-tab-btn:hover {
        background: #EFF6FF;
        color: #1D4ED8;
        border-color: #BFDBFE;
    }

    .pengumuman-tab-btn.active {
        background: #0751FF;
        color: #ffffff;
        border-color: #0751FF;
        box-shadow: 0 4px 12px rgba(7, 81, 255, 0.25);
    }

    .pengumuman-tab-badge {
        font-size: 0.75rem;
        padding: 0.1rem 0.45rem;
        border-radius: 9999px;
        background: rgba(0, 0, 0, 0.08);
        font-weight: 700;
    }

    .pengumuman-tab-btn.active .pengumuman-tab-badge {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Pengumuman Grid */
    .pengumuman-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.75rem;
    }

    .pengumuman-card {
        background: #ffffff;
        border: 1px solid var(--neutral-200, #E2E8F0);
        border-radius: 1rem;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05);
        transition: transform 0.25s cubic-bezier(0.2, 0, 0, 1), box-shadow 0.25s cubic-bezier(0.2, 0, 0, 1), border-color 0.25s ease;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .pengumuman-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: transparent;
        transition: background 0.2s ease;
    }

    .pengumuman-card[data-category="pendaftaran"]::before { background: #2563EB; }
    .pengumuman-card[data-category="persyaratan"]::before { background: #D97706; }
    .pengumuman-card[data-category="hasil"]::before { background: #16A34A; }

    .pengumuman-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.12);
        border-color: #93C5FD;
    }

    .pengumuman-card:focus-visible {
        outline: 2px solid #0751FF;
        outline-offset: 2px;
    }

    .pengumuman-meta-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1.15rem;
    }

    .pengumuman-category-tag {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.65rem;
        border-radius: 0.375rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .tag-blue { background: #EFF6FF; color: #1E40AF; border: 1px solid #DBEAFE; }
    .tag-amber { background: #FFFBEB; color: #92400E; border: 1px solid #FEF3C7; }
    .tag-green { background: #ECFDF5; color: #065F46; border: 1px solid #D1FAE5; }

    .pengumuman-pin-badge {
        font-size: 0.75rem;
        font-weight: 700;
        color: #2563EB;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        padding: 0.2rem 0.55rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .pengumuman-card h3 {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--neutral-900, #0F172A);
        margin-bottom: 0.75rem;
        line-height: 1.4;
        transition: color 0.2s ease;
    }

    .pengumuman-card:hover h3 {
        color: #0751FF;
    }

    .pengumuman-card p {
        font-size: 0.875rem;
        color: var(--neutral-600, #475569);
        line-height: 1.6;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .pengumuman-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 1.15rem;
        border-top: 1px solid var(--neutral-200, #F1F5F9);
        font-size: 0.8125rem;
    }

    .pengumuman-date-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        color: var(--neutral-500, #64748B);
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }

    .pengumuman-action-link {
        font-weight: 700;
        color: #0751FF;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: gap 0.2s ease;
    }

    .pengumuman-card:hover .pengumuman-action-link {
        gap: 0.6rem;
    }

    /* Modal Pop-up Warta Resmi */
    .pengumuman-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .pengumuman-modal-backdrop.open {
        display: flex;
        opacity: 1;
    }

    .pengumuman-modal-card {
        background: #ffffff;
        border-radius: 1.25rem;
        max-width: 650px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.5);
        padding: 2.25rem;
        position: relative;
        transform: scale(0.95);
        transition: transform 0.25s cubic-bezier(0.2, 0, 0, 1);
    }

    .pengumuman-modal-backdrop.open .pengumuman-modal-card {
        transform: scale(1);
    }

    .modal-kop-surat {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding-bottom: 1.25rem;
        border-bottom: 2px solid var(--neutral-900, #0F172A);
        margin-bottom: 1.5rem;
    }

    .modal-kop-logo {
        width: 48px;
        height: 48px;
        object-fit: contain;
    }

    .modal-kop-text h4 {
        font-size: 0.875rem;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin: 0;
    }

    .modal-kop-text p {
        font-size: 0.75rem;
        color: #475569;
        margin: 0;
        line-height: 1.4;
    }

    .modal-content-body {
        font-size: 0.9375rem;
        color: #334155;
        line-height: 1.7;
    }

    .modal-close-btn {
        position: absolute;
        top: 1.25rem;
        right: 1.25rem;
        background: #F1F5F9;
        border: none;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #64748B;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .modal-close-btn:hover {
        background: #E2E8F0;
        color: #0F172A;
    }

    /* =====================================================================
       5. LAYANAN SECTION (ENTERPRISE ACTION TILES SPEC)
       ===================================================================== */
    .section-layanan {
        padding: 6rem 0 5.5rem;
        background: linear-gradient(180deg, #F8FAFC 0%, #EFF6FF 100%);
        border-top: 1px solid var(--neutral-200, #E2E8F0);
        position: relative;
    }

    .layanan-header-wrap {
        text-align: center;
        max-width: 680px;
        margin: 0 auto 3.5rem;
    }

    .layanan-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.875rem;
        border-radius: 9999px;
        background: #E0E7FF;
        border: 1px solid #C7D2FE;
        color: #4338CA;
        font-size: 0.8125rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 0.875rem;
    }

    .layanan-title {
        font-size: 2.125rem;
        font-weight: 800;
        color: var(--neutral-900, #0F172A);
        letter-spacing: -0.025em;
        line-height: 1.25;
        margin-bottom: 0.75rem;
        text-wrap: balance;
    }

    .layanan-subtitle {
        font-size: 0.95rem;
        color: var(--neutral-600, #475569);
        line-height: 1.6;
        text-wrap: balance;
    }

    .layanan-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    .layanan-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 1.125rem;
        padding: 2rem 1.65rem 1.75rem;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.04);
        transition: transform 0.25s cubic-bezier(0.2, 0, 0, 1), box-shadow 0.25s cubic-bezier(0.2, 0, 0, 1), border-color 0.25s ease;
    }

    /* Ambient corner glow */
    .layanan-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle at top right, var(--card-glow, rgba(7, 81, 255, 0.08)), transparent 70%);
        pointer-events: none;
        border-radius: 0 1.125rem 0 0;
    }

    .layanan-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 32px -8px rgba(15, 23, 42, 0.1);
        border-color: #93C5FD;
    }

    .layanan-card:focus-visible {
        outline: 2px solid #0751FF;
        outline-offset: 2px;
    }

    .layanan-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.35rem;
    }

    .layanan-icon-box {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.25s ease;
    }

    .layanan-card:hover .layanan-icon-box {
        transform: scale(1.06);
    }

    .icon-box-blue { background: #EFF6FF; color: #2563EB; }
    .icon-box-amber { background: #FFFBEB; color: #D97706; }
    .icon-box-emerald { background: #ECFDF5; color: #059669; }
    .icon-box-purple { background: #F5F3FF; color: #7C3AED; }

    .layanan-target-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        letter-spacing: 0.02em;
    }

    .target-badge-calon { background: #EFF6FF; color: #1D4ED8; border: 1px solid #DBEAFE; }
    .target-badge-status { background: #FFF7ED; color: #C2410C; border: 1px solid #FFEDD5; }
    .target-badge-aktif { background: #ECFDF5; color: #047857; border: 1px solid #D1FAE5; }
    .target-badge-alumni { background: #FAF5FF; color: #6B21A8; border: 1px solid #F3E8FF; }

    .layanan-card-body h3 {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--neutral-900, #0F172A);
        margin-bottom: 0.65rem;
        line-height: 1.35;
        transition: color 0.2s ease;
    }

    .layanan-card:hover .layanan-card-body h3 {
        color: #0751FF;
    }

    .layanan-card-body p {
        font-size: 0.875rem;
        color: var(--neutral-600, #475569);
        line-height: 1.55;
        margin-bottom: 1.5rem;
    }

    .layanan-card-cta {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: #0751FF;
        transition: gap 0.2s ease;
        margin-top: auto;
    }

    .layanan-card:hover .layanan-card-cta {
        gap: 0.75rem;
    }

    /* Responsivitas Media Queries */
    @media (max-width: 1024px) {
        .layanan-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
        .pengumuman-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
    }

    @media (max-width: 640px) {
        .layanan-grid,
        .pengumuman-grid {
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
        .pengumuman-title,
        .layanan-title {
            font-size: 1.625rem;
        }
        .pengumuman-filter-bar {
            overflow-x: auto;
            justify-content: flex-start;
            padding-bottom: 0.5rem;
            -webkit-overflow-scrolling: touch;
        }
        .pengumuman-modal-card {
            padding: 1.5rem;
        }
    }

    /* Dark Mode Overrides */
    [data-theme="dark"] .section-pengumuman {
        background: #0B1120 !important;
    }

    [data-theme="dark"] .section-layanan {
        background: linear-gradient(180deg, #0F172A 0%, #0B1120 100%) !important;
        border-color: #1E293B !important;
    }

    [data-theme="dark"] .pengumuman-title,
    [data-theme="dark"] .layanan-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .pengumuman-subtitle,
    [data-theme="dark"] .layanan-subtitle {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .pengumuman-tab-btn {
        background: #1E293B !important;
        border-color: #334155 !important;
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .pengumuman-tab-btn.active {
        background: #2563EB !important;
        border-color: #3B82F6 !important;
        color: #ffffff !important;
    }

    [data-theme="dark"] .pengumuman-card,
    [data-theme="dark"] .layanan-card {
        background: #111C2E !important;
        border-color: #1E293B !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4) !important;
    }

    [data-theme="dark"] .pengumuman-card:hover,
    [data-theme="dark"] .layanan-card:hover {
        border-color: #3B82F6 !important;
        box-shadow: 0 16px 32px rgba(0, 0, 0, 0.6) !important;
    }

    [data-theme="dark"] .pengumuman-card h3,
    [data-theme="dark"] .layanan-card-body h3 {
        color: #F1F5F9 !important;
    }

    [data-theme="dark"] .pengumuman-card p,
    [data-theme="dark"] .layanan-card-body p {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .pengumuman-card-footer {
        border-color: #1E293B !important;
    }

    [data-theme="dark"] .pengumuman-date-chip {
        color: #64748B !important;
    }

    [data-theme="dark"] .pengumuman-modal-card {
        background: #0F172A !important;
        border-color: #1E293B !important;
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .modal-kop-surat {
        border-color: #334155 !important;
    }

    [data-theme="dark"] .modal-kop-text h4 {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .modal-kop-text p {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .modal-content-body {
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .modal-close-btn {
        background: #1E293B !important;
        color: #94A3B8 !important;
    }
```

---

### 5.2 Spesifikasi Markup HTML (Section Pengumuman & Layanan)

Ganti struktur HTML di baris ~2080 s/d 2170 di `resources/views/public/landing.blade.php` dengan blok semantik berikut:

```html
<!-- =====================================================================
     4. PENGUMUMAN SECTION (MODERN EDITORIAL GOVTECH SPEC)
     ===================================================================== -->
<section id="pengumuman-section" class="section-pengumuman" aria-labelledby="pengumuman-heading">
    <div class="container">
        <!-- Section Header -->
        <div class="pengumuman-header-wrap">
            <div class="pengumuman-eyebrow">
                <span class="pengumuman-pulse-dot" aria-hidden="true"></span>
                <span>Warta Resmi &amp; Informasi Terkini</span>
            </div>
            <h2 id="pengumuman-heading" class="pengumuman-title">Pengumuman &amp; Warta Magang</h2>
            <p class="pengumuman-subtitle">
                Pantau pengumuman pembukaan gelombang, persyaratan administrasi, dan pengumuman hasil seleksi penempatan di Diskominfo Sleman.
            </p>
        </div>

        <!-- Filter Tab Kategori -->
        <div class="pengumuman-filter-bar" role="tablist" aria-label="Kategori Pengumuman">
            <button type="button" class="pengumuman-tab-btn active" data-filter="all" role="tab" aria-selected="true">
                Semua Warta <span class="pengumuman-tab-badge">3</span>
            </button>
            <button type="button" class="pengumuman-tab-btn" data-filter="pendaftaran" role="tab" aria-selected="false">
                Pendaftaran &amp; Kuota <span class="pengumuman-tab-badge">1</span>
            </button>
            <button type="button" class="pengumuman-tab-btn" data-filter="persyaratan" role="tab" aria-selected="false">
                Persyaratan Berkas <span class="pengumuman-tab-badge">1</span>
            </button>
            <button type="button" class="pengumuman-tab-btn" data-filter="hasil" role="tab" aria-selected="false">
                Hasil Seleksi <span class="pengumuman-tab-badge">1</span>
            </button>
        </div>

        <!-- Pengumuman Cards Grid -->
        <div class="pengumuman-grid" id="pengumuman-grid-container">
            <!-- Card 1: Pendaftaran -->
            <article class="pengumuman-card" data-category="pendaftaran" tabindex="0" role="button" aria-label="Buka detail pengumuman Pendaftaran magang gelombang 2 resmi dibuka"
                     data-title="Pendaftaran Magang Gelombang 2 Resmi Dibuka"
                     data-date="28 Sep 2026"
                     data-nosurat="500.12/KOMINFO/IX/2026"
                     data-content="Dinas Komunikasi dan Informatika Kabupaten Sleman secara resmi membuka pendaftaran Praktik Kerja Lapangan (PKL) dan Magang Riset untuk Gelombang 2 Periode Pelaksanaan November–Desember 2026. Seluruh pendaftar mandiri maupun perwakilan kelompok wajib melengkapi berkas administrasi dan mengajukan jurusan yang bersesuaian dengan kuota yang tersedia sebelum 15 Oktober 2026 pukul 23:59 WIB.">
                <div>
                    <div class="pengumuman-meta-top">
                        <span class="pengumuman-category-tag tag-blue">Pendaftaran</span>
                        <span class="pengumuman-pin-badge">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            Baru
                        </span>
                    </div>
                    <h3>Pendaftaran magang gelombang 2 resmi dibuka</h3>
                    <p>Pendaftaran berlangsung 1 sampai 15 Oktober 2026 untuk semua bidang penempatan Diskominfo Sleman.</p>
                </div>
                <div class="pengumuman-card-footer">
                    <div class="pengumuman-date-chip">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>28 Sep 2026</span>
                    </div>
                    <span class="pengumuman-action-link">
                        Baca Selengkapnya
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </span>
                </div>
            </article>

            <!-- Card 2: Persyaratan -->
            <article class="pengumuman-card" data-category="persyaratan" tabindex="0" role="button" aria-label="Buka detail pengumuman Persyaratan berkas pendaftaran"
                     data-title="Ketentuan & Standar Berkas Pendaftaran Magang"
                     data-date="20 Sep 2026"
                     data-nosurat="500.10/SE-BERKAS/IX/2026"
                     data-content="Guna menjamin kelancaran telaah verifikasi administrasi (SLA 1–3 hari kerja), seluruh calon peserta diwajibkan mengunggah 3 dokumen utama dalam format PDF (maks. 2 MB per berkas): 1) Surat Pengantar Resmi bertandatangan basah/elektronik dari Dekanat/Kepala Sekolah; 2) Curriculum Vitae (CV) terbaru yang memuat portofolio keahlian; 3) Transkrip Nilai / Rapor akademik terakhir dengan legalitas sah.">
                <div>
                    <div class="pengumuman-meta-top">
                        <span class="pengumuman-category-tag tag-amber">Persyaratan</span>
                        <span class="pengumuman-date-chip">Wajib Disimak</span>
                    </div>
                    <h3>Persyaratan berkas pendaftaran &amp; verifikasi</h3>
                    <p>Siapkan surat pengantar institusi, CV, dan transkrip nilai dalam format PDF berukuran maksimal 2 MB.</p>
                </div>
                <div class="pengumuman-card-footer">
                    <div class="pengumuman-date-chip">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>20 Sep 2026</span>
                    </div>
                    <span class="pengumuman-action-link">
                        Baca Selengkapnya
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </span>
                </div>
            </article>

            <!-- Card 3: Hasil Seleksi -->
            <article class="pengumuman-card" data-category="hasil" tabindex="0" role="button" aria-label="Buka detail pengumuman Hasil seleksi gelombang 1"
                     data-title="Pengumuman Kelulusan Seleksi Administrasi Gelombang 1"
                     data-date="12 Sep 2026"
                     data-nosurat="500.08/PENG-KELULUSAN/IX/2026"
                     data-content="Berdasarkan hasil verifikasi berkas dan ketersediaan kuota pembimbing bidang, peserta yang dinyatakan DITERIMA pada Gelombang 1 diwajibkan untuk: 1) Masuk ke dashboard akun portal; 2) Memeriksa bidang penempatan dan kontak mentor pendamping; 3) Mengunduh Surat Penerimaan Resmi (SK Penerimaan) ber-QR Code untuk diserahkan ke pihak kampus/sekolah.">
                <div>
                    <div class="pengumuman-meta-top">
                        <span class="pengumuman-category-tag tag-green">Hasil Seleksi</span>
                        <span class="pengumuman-date-chip">Resmi</span>
                    </div>
                    <h3>Hasil seleksi gelombang 1 diumumkan</h3>
                    <p>Peserta yang diterima dapat masuk ke dashboard untuk melihat bidang penempatan dan mengunduh SK resmi.</p>
                </div>
                <div class="pengumuman-card-footer">
                    <div class="pengumuman-date-chip">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>12 Sep 2026</span>
                    </div>
                    <span class="pengumuman-action-link">
                        Baca Selengkapnya
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </span>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- Pop-up Modal Detail Warta Resmi (Accessible Modal) -->
<div id="pengumuman-modal" class="pengumuman-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal-warta-title" aria-hidden="true">
    <div class="pengumuman-modal-card">
        <button type="button" class="modal-close-btn" id="modal-close-btn" aria-label="Tutup jendela warta">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        <!-- Kop Surat Dinas -->
        <div class="modal-kop-surat">
            <img src="{{ asset('images/logo-sleman.png') }}" alt="Logo Kabupaten Sleman" class="modal-kop-logo" onerror="this.style.display='none'">
            <div class="modal-kop-text">
                <h4>Pemerintah Kabupaten Sleman</h4>
                <p>Dinas Komunikasi dan Informatika &bull; Bidang Aplikasi Informatika</p>
                <p id="modal-warta-nosurat" style="font-weight: 600; color: #2563EB; margin-top: 2px;">Nomor: -</p>
            </div>
        </div>

        <!-- Body Pengumuman -->
        <h3 id="modal-warta-title" style="font-size: 1.35rem; font-weight: 800; color: #0F172A; margin-bottom: 0.5rem; line-height: 1.35;">-</h3>
        <p id="modal-warta-date" style="font-size: 0.8125rem; color: #64748B; margin-bottom: 1.25rem; font-weight: 600;">-</p>
        
        <div id="modal-warta-content" class="modal-content-body">
            -
        </div>

        <div style="margin-top: 2rem; padding-top: 1.25rem; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end; gap: 0.75rem;">
            <button type="button" class="btn-panduan" id="modal-close-btn-footer" style="padding: 0.55rem 1.25rem; font-size: 0.875rem; border-radius: 0.5rem;">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- =====================================================================
     5. LAYANAN SECTION (ENTERPRISE ACTION TILES SPEC)
     ===================================================================== -->
<section id="layanan-section" class="section-layanan" aria-labelledby="layanan-heading">
    <div class="container">
        <!-- Section Header -->
        <div class="layanan-header-wrap">
            <div class="layanan-eyebrow">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Akses Terpadu &amp; Layanan Mandiri</span>
            </div>
            <h2 id="layanan-heading" class="layanan-title">Pusat Layanan &amp; Fitur Utama Magang</h2>
            <p class="layanan-subtitle">
                Akses cepat menuju seluruh instrumen magang mulai dari pendaftaran kuota, pelacakan berkas, hingga sistem logbook harian dan sertifikasi resmi.
            </p>
        </div>

        <!-- 4 Enterprise Action Tiles -->
        <div class="layanan-grid">
            <!-- Tile 1: Pendaftaran Magang -->
            <a href="#kuota-section" class="layanan-card" style="--card-glow: rgba(37, 99, 235, 0.12);" id="layanan-card-daftar">
                <div>
                    <div class="layanan-card-header">
                        <div class="layanan-icon-box icon-box-blue">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="12" y1="18" x2="12" y2="12"/>
                                <line x1="9" y1="15" x2="15" y2="15"/>
                            </svg>
                        </div>
                        <span class="layanan-target-badge target-badge-calon">Calon Peserta</span>
                    </div>
                    <div class="layanan-card-body">
                        <h3>Daftar Magang</h3>
                        <p>Pilih periode dan ajukan pendaftaran mandiri atau perwakilan kelompok dengan pengecekan kuota real-time.</p>
                    </div>
                </div>
                <div class="layanan-card-cta">
                    <span>Lihat Kuota &amp; Daftar</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
            </a>

            <!-- Tile 2: Cek Status -->
            <a href="{{ route('login') }}" class="layanan-card" style="--card-glow: rgba(217, 119, 6, 0.12);" id="layanan-card-status">
                <div>
                    <div class="layanan-card-header">
                        <div class="layanan-icon-box icon-box-amber">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                <path d="m9 11 2 2 4-4"/>
                            </svg>
                        </div>
                        <span class="layanan-target-badge target-badge-status">Pendaftar</span>
                    </div>
                    <div class="layanan-card-body">
                        <h3>Cek Status Berkas</h3>
                        <p>Pantau progres telaah administrasi, posisi nomor antrean waiting list, dan unduh SK Diterima resmi.</p>
                    </div>
                </div>
                <div class="layanan-card-cta">
                    <span>Masuk &amp; Pantau Status</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
            </a>

            <!-- Tile 3: Logbook & Presensi -->
            <a href="{{ route('login') }}" class="layanan-card" style="--card-glow: rgba(5, 150, 105, 0.12);" id="layanan-card-logbook">
                <div>
                    <div class="layanan-card-header">
                        <div class="layanan-icon-box icon-box-emerald">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                <line x1="6" y1="8" x2="8" y2="8"/>
                                <line x1="6" y1="12" x2="8" y2="12"/>
                            </svg>
                        </div>
                        <span class="layanan-target-badge target-badge-aktif">Peserta Aktif</span>
                    </div>
                    <div class="layanan-card-body">
                        <h3>Logbook Harian</h3>
                        <p>Akses pelaporan jurnal aktivitas, unggah dokumentasi proyek, dan terima reviu mingguan dari pembimbing.</p>
                    </div>
                </div>
                <div class="layanan-card-cta">
                    <span>Akses Logbook Harian</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
            </a>

            <!-- Tile 4: Lembar Nilai & Sertifikat -->
            <a href="{{ route('login') }}" class="layanan-card" style="--card-glow: rgba(124, 58, 237, 0.12);" id="layanan-card-nilai">
                <div>
                    <div class="layanan-card-header">
                        <div class="layanan-icon-box icon-box-purple">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="8" r="6"/>
                                <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
                                <polyline points="9 8 11 10 15 6"/>
                            </svg>
                        </div>
                        <span class="layanan-target-badge target-badge-alumni">Selesai Magang</span>
                    </div>
                    <div class="layanan-card-body">
                        <h3>Lembar Nilai &amp; SK</h3>
                        <p>Unduh lembar nilai akhir berbasis rubrik kompetensi 100 poin dan sertifikat tanda selesai bimbingan dinas.</p>
                    </div>
                </div>
                <div class="layanan-card-cta">
                    <span>Unduh Lembar Nilai</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
            </a>
        </div>
    </div>
</section>
```

---

### 5.3 Spesifikasi Logika JavaScript (Filter Tabs & Modal Warta)

Tambahkan blok script ini di bagian bawah dalam `@push('scripts')`:

```javascript
    // =====================================================================
    // LOGIKA INTERAKTIF SECTION PENGUMUMAN & LAYANAN (FILTER & MODAL)
    // =====================================================================
    document.addEventListener('DOMContentLoaded', function () {
        // 1. FILTER TAB PENGUMUMAN
        const tabBtns = document.querySelectorAll('.pengumuman-tab-btn');
        const cards = document.querySelectorAll('.pengumuman-card');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const targetFilter = this.getAttribute('data-filter');

                // Update active state tab
                tabBtns.forEach(b => {
                    b.classList.remove('active');
                    b.setAttribute('aria-selected', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');

                // Filter cards with smooth opacity transition
                cards.forEach(card => {
                    const cardCat = card.getAttribute('data-category');
                    if (targetFilter === 'all' || cardCat === targetFilter) {
                        card.style.display = 'flex';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    } else {
                        card.style.display = 'none';
                        card.style.opacity = '0';
                    }
                });
            });
        });

        // 2. MODAL WARTA RESMI PENGUMUMAN
        const modal = document.getElementById('pengumuman-modal');
        const modalCloseBtn = document.getElementById('modal-close-btn');
        const modalCloseFooter = document.getElementById('modal-close-btn-footer');
        const modalTitle = document.getElementById('modal-warta-title');
        const modalDate = document.getElementById('modal-warta-date');
        const modalNoSurat = document.getElementById('modal-warta-nosurat');
        const modalContent = document.getElementById('modal-warta-content');

        function openAnnouncementModal(card) {
            const title = card.getAttribute('data-title') || card.querySelector('h3').textContent;
            const date = card.getAttribute('data-date') || '';
            const noSurat = card.getAttribute('data-nosurat') || '500.12/KOMINFO/2026';
            const content = card.getAttribute('data-content') || card.querySelector('p').textContent;

            if (modalTitle) modalTitle.textContent = title;
            if (modalDate) modalDate.textContent = 'Diterbitkan pada: ' + date;
            if (modalNoSurat) modalNoSurat.textContent = 'Nomor: ' + noSurat;
            if (modalContent) modalContent.textContent = content;

            if (modal) {
                modal.classList.add('open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden'; // cegah scroll latar
                if (modalCloseBtn) modalCloseBtn.focus();
            }
        }

        function closeAnnouncementModal() {
            if (modal) {
                modal.classList.remove('open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
        }

        // Buka modal saat kartu diklik atau di-Enter
        cards.forEach(card => {
            card.addEventListener('click', function () {
                openAnnouncementModal(this);
            });

            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openAnnouncementModal(this);
                }
            });
        });

        if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeAnnouncementModal);
        if (modalCloseFooter) modalCloseFooter.addEventListener('click', closeAnnouncementModal);

        // Tutup modal jika klik di luar box (backdrop)
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeAnnouncementModal();
                }
            });
        }

        // Tutup modal saat tombol Escape ditekan
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && modal.classList.contains('open')) {
                closeAnnouncementModal();
            }
        });
    });
```

---

## 6. Checklist Verifikasi & Quality Assurance (Senior QA Matrix)

Setelah Junior Developer atau LLM Executor mengaplikasikan kode di atas, lakukan verifikasi berikut:

- [ ] **Desain & Tipografi Bebas AI Slop**:
  - [ ] Section Pengumuman memiliki *eyebrow badge* "Warta Resmi & Informasi Terkini" dengan dot berkedip halus.
  - [ ] Kartu pengumuman memiliki border aksen warna semantik pada bagian atas sesuai kategori (Biru = Pendaftaran, Kuning/Amber = Persyaratan, Hijau = Hasil).
  - [ ] Section Layanan memiliki subjudul edukatif dan setiap tile memiliki badge sasaran peserta (*Calon Peserta*, *Pendaftar*, *Peserta Aktif*, *Selesai Magang*).
- [ ] **Fungsionalitas Interaktif**:
  - [ ] Mengklik tombol tab kategori (misal: "Hasil Seleksi") memfilter grid pengumuman secara mulus.
  - [ ] Mengklik salah satu kartu pengumuman memunculkan pop-up modal "Warta Resmi" dengan kop surat Sleman dan nomor surat.
  - [ ] Tombol Escape atau tombol "Tutup" berhasil menutup modal dan mengembalikan scroll halaman normal.
  - [ ] Mengklik kartu "Daftar Magang" melakukan *smooth scroll* langsung ke section kuota (`#kuota-section`).
- [ ] **Responsivitas**:
  - [ ] Pada resolusi Desktop (1440px / 1200px): Pengumuman tampil 3 kolom, Layanan tampil 4 kolom.
  - [ ] Pada resolusi Tablet (768px): Pengumuman & Layanan tampil rapi 2 kolom.
  - [ ] Pada resolusi Mobile (375px - 414px): Grid beralih menjadi 1 kolom vertikal tanpa teks terpotong (*zero overflow horizontal*).
- [ ] **Mode Gelap (Dark Mode)**:
  - [ ] Saat attribute `data-theme="dark"` aktif pada `<html>`, latar belakang pengumuman dan layanan berubah menjadi gelap slate yang serasi (`#0B1120` dan `#0F172A`).
  - [ ] Teks judul dan paragraf tetap kontras tinggi memenuhi standar WCAG AAA/AA.
  - [ ] Modal warta resmi memiliki tampilan gelap yang elegan dengan teks terbaca sempurna.

---

## 7. Instruksi Eksekusi Singkat untuk Junior Developer / Budget AI

1. Buka file `resources/views/public/landing.blade.php`.
2. Cari baris `/* 4. PENGUMUMAN SECTION */` di blok `<style>` dan ganti deklarasi CSS pengumuman serta layanan lama dengan CSS pada **Sub-bab 5.1**.
3. Cari tag `<section id="pengumuman-section"` sampai akhir `</section>` dari `#layanan-section` dan ganti dengan kode markup HTML pada **Sub-bab 5.2**.
4. Gulir ke bawah pada bagian `@push('scripts')`, sisipkan logika JavaScript dari **Sub-bab 5.3**.
5. Buka browser pada `http://127.0.0.1:8000/`, verifikasi tampilan visual dan uji klik modal serta filter tab.
