# ENGINEERING PRD & TASK SPECIFICATION: Modernisasi & Standarisasi Workspace Peserta Magang

- **Document ID**: `PRD-TASK-2026-10-29-UI-04`
- **Target Audience**: Senior Developer / Core Implementer (Eren Yeager) & AI Code Models
- **Author**: Erwin Smith (Orchestrator & Technical Lead)
- **UI/UX Spec Alignment**: Historia Reiss (Lead Designer & Design Systems Lead)
- **Architectural Approval**: Armin Arlert (Software Architect)
- **Reviewer**: Levi Ackerman (Lead Developer & Final Code Reviewer)
- **QA Verification**: Annie Leonhart (QA Lead)
- **Target Module**: Portal Peserta Magang (`resources/views/peserta/dashboard.blade.php` & partials)
- **Complexity Grade**: `K2` (Multi-modul, UI Layout, Design System DS-MagKom, Accessibility)

---

## 1. Executive Summary & Objective

Saat ini, antarmuka Workspace Peserta Magang (`resources/views/peserta/dashboard.blade.php`) dan komponen partialnya memiliki inkonsistensi estetika dibanding standar modern DS-MagKom yang telah diterapkan pada Dashboard SuperAdmin, Admin, dan Pembimbing:
1. **Hero Banner**: Masih memuat ornamen SVG wave acak bernuansa sci-fi ("terlalu AI") dengan drop-shadow tebal kaku, kontras tinggi yang melelahkan mata, dan layout yang berdesakan.
2. **Kartu Metrik KPI (`_kpi_bar.blade.php` / `_phase_aktif.blade.php`)**: Masih menggunakan styling ad-hoc kotak kaku dengan progress bar neon yang kurang selaras dengan token DS-MagKom.
3. **Stepper & Phase Cards (`_phase_onboarding`, `_phase_seleksi`, `_phase_selesai`)**: Perlu penyelarasan tata letak agar terasa bersih, soft, dan elegan dengan nomor bulat pastel serta whitespace yang lapang (*breathing room*).
4. **Aside Context Sidebar (`_aside.blade.php`)**: Profil Pembimbing Lapangan dan kartu peringatan ketentuan logbook (BR-09) perlu diharmonisasikan menggunakan token semantik netral dan amber pastel.
5. **Eliminasi Inline Hover JS & Dual-Theme Contrast**: Menghilangkan seluruh inline JS hover menjadi Pure CSS dan menjamin kontras WCAG 2.1 AA (min 4.5:1) pada Mode Terang dan Mode Gelap.

### Sasaran Utama (Objectives):
1. **Atmospheric Hero Banner**: Rombak latar hero menjadi Sleman Navy solid gradien tenang (`linear-gradient(135deg, #1b3860 0%, #152d4e 100%)`), border subtle `1px solid rgba(255, 255, 255, 0.12)`, radius `16px`, dan eliminasi total pola wave SVG alien.
2. **4 Hero Metric KPI Cards**: Terapkan kartu metrik bersih (`#ffffff` di light mode, navy gelap `#003566`/`#02275D` di dark mode) dengan border tipis `#e2e8f0`, soft diffuse shadow, dan ikon pastel sejuk.
3. **Modern Phase Flow & Stepper**: Sederhanakan tampilan 4 fase (Onboarding, Seleksi, Aktif, Selesai) agar informatif, elegan, dan ramah pengguna baru.
4. **Harmonisasi Aside Context**: Format kartu profil pembimbing, aksi cepat, dan card peringatan ketentuan logbook dengan palet pastel lembut.
5. **Zero Functional Regression**: Menjaga 100% integritas route POST/GET, form CSRF, modal popups, dan test suite peserta.

---

## 2. Bounded File Scope (Single-Writer Principle)

- **`allowed_files`**:
  - `resources/views/peserta/dashboard.blade.php`
  - `resources/views/peserta/dashboard/partials/_aside.blade.php`
  - `resources/views/peserta/dashboard/partials/_header.blade.php`
  - `resources/views/peserta/dashboard/partials/_kpi_bar.blade.php`
  - `resources/views/peserta/dashboard/partials/_phase_aktif.blade.php`
  - `resources/views/peserta/dashboard/partials/_phase_onboarding.blade.php`
  - `resources/views/peserta/dashboard/partials/_phase_seleksi.blade.php`
  - `resources/views/peserta/dashboard/partials/_phase_selesai.blade.php`
  - `tests/Feature/PesertaPortalWorkflowTest.php`
  - `tests/Feature/PesertaFullPortalFeatureTest.php`
- **`forbidden_files`**:
  - `app/**` (Logika backend dan service layer tidak boleh diubah)
  - `routes/**` (Route web dan API dilarang dimodifikasi)
  - `database/**` (Skema database dilarang disentuh)
  - `resources/views/admin/**`, `resources/views/pembimbing/**`, `resources/views/superadmin/**`

---

## 3. Spesifikasi Teknis & Desain (Historia Reiss & Armin Arlert Review)

### 3.1. Hero Banner (`.dashboard-hero` / `_header.blade.php`)
```css
/* Hero Banner Peserta DS-MagKom Soft-Elegan */
.dashboard-hero {
    background: linear-gradient(135deg, #1b3860 0%, #152d4e 100%);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 0 4px 20px -2px rgba(27, 56, 96, 0.12);
    padding: 2.25rem 2rem;
    position: relative;
    overflow: hidden;
}

/* Hapus tumpukan wave SVG latar belakang */
.dashboard-hero::before,
.dashboard-hero::after {
    display: none !important;
}
```

### 3.2. 4 Kartu Metrik KPI (`_kpi_bar.blade.php` & `_phase_aktif.blade.php`)
1. **Total Tugas Magang**: Ikon pastel biru lembut (`bg-blue-50 text-blue-600`), angka metrik tegas berfont display, progress bar mikro lembut.
2. **Logbook Aktivitas**: Ikon pastel hijau sejuk (`bg-emerald-50 text-emerald-600`), akumulasi hari pengisian dan status pending review.
3. **Sisa Hari Magang**: Ikon pastel amber sejuk (`bg-amber-50 text-amber-600`), hitungan mundur hari kerja efektif.
4. **Evaluasi & Nilai**: Ikon pastel indigo lembut (`bg-indigo-50 text-indigo-600`), status penilaian pembimbing dan ketersediaan sertifikat.

### 3.3. Aside Context (`_aside.blade.php`)
- Kartu Profil Pembimbing Lapangan: Avatar inisial dengan latar navy Sleman, nama lengkap, kontak ruangan & email dalam wadah sejuk `#f8fafc`.
- Tombol Aksi Cepat: Grid kartu aksi dengan ikon ramping, transisi hover murni CSS tanpa inline JS.
- Kartu Peringatan Logbook (BR-09): Warna amber pastel `#fffbeb` dengan border halus `#fef3c7` dan teks `#92400e`.

### 3.4. Aturan Dual-Theme & Aksesibilitas WCAG 2.1 AA
- **Mode Terang**: Background kanvas `#f8fafc`, kartu permukaan `#ffffff`, border `#e2e8f0`, teks judul `#0f172a`, teks tubuh `#334155`.
- **Mode Gelap**: Background kanvas `#002548` / `#001F3F`, kartu permukaan `#003566` / `#02275D`, border `#3F51B5`, teks judul `#F8FAFC`, teks tubuh `#CBD5E1`.
- Kontras rasio teks terhadap latar belakang minimal 4.5:1.

---

## 4. Acceptance Criteria

- **AC-1 (Atmospheric Hero Banner Clean)**: Latar hero banner peserta menggunakan solid gradient Sleman Navy `#1b3860` tanpa ornamen wave SVG alien, padding proporsional, dan badge status resmi Pemkab Sleman.
- **AC-2 (4 Kartu Metrik KPI Modern)**: 4 kartu metrik mengadopsi surface bersih, border halus, soft shadow, ikon pastel sejuk, dan tipografi jelas.
- **AC-3 (Penyelarasan 4 Fase Siklus Magang)**: Tampilan fase Onboarding, Seleksi, Aktif, dan Selesai terstruktur rapi, informatif, dan tidak mengintimidasi calon peserta.
- **AC-4 (Aside Context Profil & Aksi Cepat)**: Profil pembimbing dan aksi cepat tersaji simetris dengan transisi hover murni CSS.
- **AC-5 (Pure CSS & Dual-Theme AA)**: Seluruh atribut inline JS hover (`onmouseover`/`onmouseout`) dihapus total; tampilan Light dan Dark mode memenuhi kontras WCAG 2.1 AA.
- **AC-6 (Test Suite Green & Zero Regresi)**: Seluruh test suite pada `PesertaPortalWorkflowTest` dan `PesertaFullPortalFeatureTest` wajib lulus 100% (15/15 passed).

---

## 5. RACI Assignment & Delivery Pipeline

- **Accountable**: **Erwin Smith** (Orchestrator & Technical Lead)
- **Responsible (Core Implementer)**: **Eren Yeager** (Senior Developer)
- **Consulted**: **Historia Reiss** (UI/UX Spec) & **Armin Arlert** (Software Architect)
- **Informed (Code Review)**: **Levi Ackerman** (Final Code Reviewer)
- **Informed (QA & Verification)**: **Annie Leonhart** (QA Lead)
