# Rencana Penyelarasan Tipografi DS-MagKom (Kepatuhan Penuh Dokumen GitHub)

| Metadata Dokumen | Keterangan |
|---|---|
| Tanggal | 18 November 2026 |
| Status | Menunggu Persetujuan Pengguna (*Pending Approval*) |
| Target Standar | `docs/design/design-system.md` (Bagian 3: Tipografi Standar) |
| Cakupan | Layouts (Dashboard, Peserta, Auth), CSS Tokens, Role Pages, Chart SVG, Mobile Guardrails |
| Dokumen Acuan Audit | `FONT_AUDIT_REPORT.md` |

---

## 1. Tujuan

Menyelaraskan seluruh implementasi kode tipografi (font) di aplikasi web MAGKOM agar **100% identik dan patuh** terhadap spesifikasi resmi pada `docs/design/design-system.md` Bagian 3:
1. **Antarmuka Utama**: `Open Sans, sans-serif` (Bobot 400 Regular, 600 Semibold, 700 Bold) untuk paragraf teks, label formulir, deskripsi tugas, navigasi sidebar, dan tabel data di seluruh 4 role.
2. **Heading & Modal**: `Plus Jakarta Sans, sans-serif` (Bobot 600 Semibold, 700 Bold) untuk seluruh judul halaman, hero header, dan dialog modal konfirmasi.
3. **Data & Kode**: `JetBrains Mono, monospace` (Bobot 500 Medium, 700 Bold) untuk data kuantitatif, nomor registrasi (#REG-xxx), NIM, hash SHA-256, dan alamat IP.
4. **Eliminasi Font Liar**: Menghapus pemuatan dan ketergantungan terhadap `Lexend`, `Source Sans 3`, `Inter`, dan `Times New Roman`.
5. **Aksesibilitas & Kerapian**: Menghapus teks mikro di bawah 12px (min 12px / WCAG 2.1 AA) dan menormalkan bobot 800 menjadi 700 untuk mencegah *faux-bolding*.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
- Dokumen `docs/design/design-system.md` Bagian 3 adalah **Single Source of Truth** tipografi proyek.
- Font stack `Arial, Helvetica, sans-serif` pada dokumen ekspor PDF (`resources/views/pdf/*.blade.php`) dan system font stack pada email (`resources/views/emails/reset-password.blade.php`) dipertahankan apa adanya demi kompatibilitas engine DomPDF dan email client modern.
- Pemuatan aset font tetap dikelola melalui Google Fonts CDN di `<head>` layout HTML dengan parameter `&display=swap`.

### Pertanyaan Terbuka:
- *Tidak ada ambiguitas kritis.* Spesifikasi tabel GitHub sudah sangat eksplisit mengenai peran, bobot, dan keluarga font.

---

## 3. Pendekatan Teknis (Evaluasi 2 Opsi Arsitektur)

### Opsi A: Penyelarasan Fondasional Berbasis Token Terpusat & Layout Cascade (*Direkomendasikan*)
- Menyelaraskan token di `resources/css/base/variables.css` (`--font-sans: 'Open Sans'`, `--font-heading: 'Plus Jakarta Sans'`, `--font-mono: 'JetBrains Mono'`).
- Mengarahkan `dashboard-cockpit.css` dan `peserta-layout-tokens.css` ke token `--font-heading` dan `--font-sans`.
- Memangkas link CDN Google Fonts di layout Blade sehingga hanya mengunduh 3 font resmi.
- **Kelebihan**: Mengubah ratusan halaman sekaligus secara seragam (*single point of control*), zero markup overhead, bebas regresi visual, dan performa jaringan meningkat drastis.
- **Kekurangan**: Memerlukan kompilasi ulang aset Vite (`npm run build`).

### Opsi B: Hardcoding Selektor per File Halaman (*Tidak Disarankan*)
- Menimpa selektor font-family di setiap file CSS pages (`superadmin/dashboard.css`, `admin/dashboard.css`, `pembimbing/dashboard.css`, `peserta/dashboard.css`).
- **Kekurangan**: Rawan terjadi inkonsistensi ulang di masa depan, menimbulkan redundansi CSS, dan tidak menyelesaikan masalah pemborosan CDN di layout.

**Keputusan**: Dipilih **Opsi A** karena menjamin integritas arsitektural jangka panjang dan sepenuhnya mematuhi prinsip *Clean Architecture* MAGKOM.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/css/base/variables.css` | **Modify** | Tetapkan `--font-sans: 'Open Sans'`, `--font-heading: 'Plus Jakarta Sans'`, `--font-mono: 'JetBrains Mono'`. Arahkan `--font-display` ke `--font-heading`. |
| `resources/views/layouts/peserta.blade.php` | **Modify** | Pangkas pemuatan Google Fonts: buang `Lexend` dan `Source Sans 3`, hanya muat `Open Sans`, `Plus Jakarta Sans`, dan `JetBrains Mono`. Hapus pemuatan ganda legacy `gentelella-theme.css`. |
| `resources/views/layouts/dashboard.blade.php` | **Modify** | Hapus pemuatan ganda legacy `gentelella-theme.css`. Pastikan query string Google Fonts memuat `Open Sans (400,600,700)`, `Plus Jakarta Sans (600,700)`, `JetBrains Mono (500,700)`. |
| `resources/views/layouts/auth-depth.blade.php` | **Modify** | Pangkas pemuatan Google Fonts menjadi hanya font resmi. |
| `resources/css/layouts/dashboard-cockpit.css` | **Modify** | Ubah semua `var(--font-display, 'Lexend', sans-serif)` menjadi `var(--font-heading, 'Plus Jakarta Sans', sans-serif)`. |
| `resources/css/layouts/peserta-layout-tokens.css` | **Modify** | Ubah body dari `Source Sans 3` ke `var(--font-sans)`, ubah heading dari `Lexend` ke `var(--font-heading)`. |
| `resources/css/pages/peserta/dashboard.css` | **Modify** | Ganti deklarasi lokal `Source Sans 3` dan `Lexend` dengan `var(--font-sans)` dan `var(--font-heading)`. |
| `resources/css/pages/superadmin/dashboard.css` | **Modify** | Batasi `Plus Jakarta Sans` hanya pada hero title dan card headings; kembalikan body dan tabel ke `var(--font-sans)` (`Open Sans`). |
| `resources/css/pages/superadmin/site-editor.css` | **Modify** | Ganti `Inter` dengan `var(--font-heading)` (`Plus Jakarta Sans`). |
| `resources/css/pages/auth/auth-depth.css` | **Modify** | Hapus baris `@import url(...)` Google Fonts yang memicu render-blocking waterfall. |
| `resources/css/pages/public/landing.css` | **Modify** | Hapus `@import url(...)` Google Fonts di baris awal. |
| `resources/css/pages/public/peta-alur.css` | **Modify** | Hapus `@import url(...)` Google Fonts di baris awal. |
| `resources/views/pembimbing/laporan-kinerja/cetak.blade.php` | **Modify** | Ganti `Times New Roman` dengan `'Plus Jakarta Sans', Arial, sans-serif`. |
| `resources/js/components/plan-growth-chart.js` | **Modify** | Naikkan font-size sumbu dari 11px/11.5px ke 12px dan sematkan font-family `Open Sans`. |
| `resources/css/components/cards.css` | **Modify** | Naikkan teks 11px ke 12px (`0.75rem`), ubah `font-weight: 800` ke `700`. |
| `resources/css/components/tables.css` | **Modify** | Naikkan mobile `th` dari 0.725rem ke 0.75rem (12px), ubah `font-weight: 800` ke `700`. |
| `resources/css/pages/superadmin/audit-log.css` | **Modify** | Naikkan teks 10px (`badge-worm-policy`, `audit-req-id`, `badge-severity-pill`) ke 0.75rem (12px). |
| `resources/css/pages/peserta/logbook-index.css` | **Modify** | Naikkan label mini widget (10px–11px) ke 0.75rem (12px). |
| `tailwind.config.js` | **Modify** | Selaraskan konfigurasi `fontFamily.sans` dengan `Open Sans` dan tambahkan `heading: ['"Plus Jakarta Sans"', 'sans-serif']`. |

---

## 5. Langkah Implementasi Terstruktur

### Tahap 1: Standardisasi Fondasi Token & Pemuatan CDN Layout
- [x] **1.1. Perbarui `resources/css/base/variables.css`**:
  - Deklarasikan `--font-sans: 'Open Sans', Arial, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;` (Antarmuka Utama).
  - Deklarasikan `--font-heading: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;` (Heading & Modal).
  - Deklarasikan `--font-mono: 'JetBrains Mono', ui-monospace, monospace;` (Data & Kode).
  - Buat alias `--font-display: var(--font-heading);` untuk backward-compatibility.
  - Buat alias `--sa-font-sans: var(--font-heading);` dan `--sa-font-mono: var(--font-mono);`.
- [x] **1.2. Pangkas Pemuatan Google Fonts di Layout HTML**:
  - Perbarui [`resources/views/layouts/dashboard.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php):
    `<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">`
    Hapus `<link rel="stylesheet" href="{{ asset('css/gentelella-theme.css') }}">`.
  - Perbarui [`resources/views/layouts/peserta.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/layouts/peserta.blade.php):
    Samakan link font persis dengan `dashboard.blade.php` (hapus `Lexend` & `Source Sans 3`).
    Hapus `<link rel="stylesheet" href="{{ asset('css/gentelella-theme.css') }}">`.
  - Perbarui [`resources/views/layouts/auth-depth.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/layouts/auth-depth.blade.php):
    Pangkas link font CDN ke font resmi.
- [x] **1.3. Hapus `@import url(...)` Render-Blocking di File CSS**:
  - Hapus `@import url(...)` di baris 1 [`resources/css/pages/auth/auth-depth.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/auth/auth-depth.css).
  - Hapus `@import url(...)` di baris 5 [`resources/css/pages/public/landing.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/public/landing.css).
  - Hapus `@import url(...)` di baris 6 [`resources/css/pages/public/peta-alur.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/public/peta-alur.css).
- [x] **1.4. Selaraskan `tailwind.config.js`**:
  - Konfigurasi `fontFamily: { sans: ['"Open Sans"', 'sans-serif'], heading: ['"Plus Jakarta Sans"', 'sans-serif'], mono: ['"JetBrains Mono"', 'monospace'] }`.

### Tahap 2: Penyelarasan Shell Dashboard & Cockpit
- [x] **2.1. Normalisasi `resources/css/layouts/dashboard-cockpit.css`**:
  - Ganti seluruh referensi `var(--font-display, 'Lexend', sans-serif)` menjadi `var(--font-heading, 'Plus Jakarta Sans', sans-serif)`.
- [x] **2.2. Normalisasi `resources/css/layouts/peserta-layout-tokens.css`**:
  - Ubah body text dari `Source Sans 3` menjadi `var(--font-sans)`.
  - Ubah title/heading dari `Lexend` menjadi `var(--font-heading)`.
  - Hapus override lokal yang bertabrakan.
- [x] **2.3. Normalisasi `resources/css/pages/peserta/dashboard.css`**:
  - Ubah `.bento-hero-title` dan cockpit headers menjadi `var(--font-heading)`.
  - Ubah teks body menjadi `var(--font-sans)`.

### Tahap 3: Penyelarasan Superadmin, Admin, Pembimbing
- [x] **3.1. Superadmin Dashboard & Site Editor**:
  - Pada `resources/css/pages/superadmin/dashboard.css:9`, hapus paksaan font pada body agar body memakai `Open Sans`, dan hero/card titles memakai `Plus Jakarta Sans`.
  - Pada `resources/css/pages/superadmin/site-editor.css:45`, ubah `'Inter'` menjadi `var(--font-heading)`.
  - Pada `resources/css/pages/superadmin/audit-log.css`, ubah teks 10px (`badge-worm-policy`, `audit-req-id`, `badge-severity-pill`) menjadi `0.75rem` (12px).
- [x] **3.2. Administrasi & Pembimbing**:
  - Pada `resources/css/pages/admin/dashboard.css`, pastikan tabel dan form konsisten memakai `Open Sans`, dan hero header memakai `Plus Jakarta Sans`.
  - Pada `resources/views/pembimbing/laporan-kinerja/cetak.blade.php:17`, ganti `Times New Roman` dengan `'Plus Jakarta Sans', Arial, sans-serif`.

### Tahap 4: Aksesibilitas Teks (< 12px), Normalisasi Bobot 800, & Chart
- [x] **4.1. Aksesibilitas Skala Minimal 12px**:
  - Pada `resources/css/components/cards.css:156, 186`, naikkan 11px ke `0.75rem` (12px).
  - Pada `resources/css/components/tables.css:893`, naikkan mobile `th` dari 0.725rem ke `0.75rem` (12px).
  - Pada `resources/css/pages/peserta/logbook-index.css:580, 1073`, naikkan 10px dan 10.4px ke `0.75rem` (12px).
- [x] **4.2. Normalisasi Bobot 800 ke 700**:
  - Ganti `font-weight: 800` menjadi `font-weight: 700` pada komponen shared cards, tables, dan layout tokens untuk mencegah efek faux-bold.
- [x] **4.3. Perbaikan Native SVG Charts**:
  - Pada `resources/js/components/plan-growth-chart.js:117, 127`, ubah font-size 11px ke 12px dan sematkan font-family `Open Sans`.
  - Pada `public/css/superadmin-dashboard.css:2106`, naikkan `.gt-chart-axis-text` dari 10px ke 12px.

---

## 6. Strategi Testing & Quality Gates

1. **Asset Pipeline Verification**:
   - Jalankan `npm run build` untuk memastikan seluruh perubahan CSS terkompilasi bersih tanpa error PostCSS/Vite.
2. **Code Style & Formatting**:
   - Jalankan `vendor/bin/pint --test` untuk memastikan formatting kode PHP tetap 100% lulus standar PSR-12.
3. **Static Analysis**:
   - Jalankan `vendor/bin/phpstan analyse --memory-limit=1G` untuk memastikan tidak ada pelanggaran tipe data.
4. **Feature Test Regression**:
   - Jalankan test suite relevan:
     - `php artisan test tests/Feature/AdminPendaftaranPerbaikanFlowTest.php`
     - `php artisan test tests/Feature/AdminVerifikasiKategoriDanUploadSuratTest.php`
     - `php artisan test tests/Feature/ProductionReadinessCommandTest.php`
5. **Auditor Verification Script**:
   - Jalankan kembali script auditor `audit_fonts.ps1` untuk memverifikasi bahwa:
     - `Lexend`, `Source Sans 3`, `Inter`, dan `Times New Roman` tereliminasi dari dashboard.
     - Teks < 12px pada komponen inti berkurang drastis ke level 0.

---

## 7. Analisis Risiko & Mitigasi

| Risiko | Tingkat | Mitigasi |
| :--- | :---: | :--- |
| **Perubahan Tata Letak (Cumulative Layout Shift / CLS)** | Rendah | Menggunakan `display=swap` pada Google Fonts dan mempertahankan fallback `sans-serif` dan `monospace` yang setara tingginya (*x-height*). |
| **Teks Meluap (Text Overflow / Truncation)** | Sedang | Perubahan ukuran teks mikro dari 10px/11px ke 12px dikompensasi dengan penyesuaian padding kartu dan penggunaan `text-overflow: ellipsis;` jika diperlukan. |
| **Cache Peramban Pengguna Lama** | Rendah | Vite asset hashing (`build/assets/*.css`) secara otomatis memastikan pengguna mengunduh bundle CSS terbaru tanpa terkena cache lawas. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Seluruh antarmuka utama (body, label, deskripsi, tabel, sidebar) di 4 role (Superadmin, Admin, Pembimbing, Peserta) memakai **`Open Sans`**.
- [ ] Seluruh judul halaman, hero banner, dan modal konfirmasi memakai **`Plus Jakarta Sans`**.
- [ ] Seluruh data telemetri, token, hash, kuota, dan NIM memakai **`JetBrains Mono`**.
- [ ] Font `Lexend`, `Source Sans 3`, `Inter`, dan `Times New Roman` tereliminasi dari dashboard.
- [ ] Tidak ada `@import url(...)` font di dalam file CSS (semua terpusat di `<head>` layout).
- [ ] Pemuatan ganda `gentelella-theme.css` di layout Blade terhapus.
- [ ] Tidak ada teks berukuran < 12px pada komponen kartu, tabel, dan badge utama.
- [ ] `npm run build`, `vendor/bin/pint --test`, `vendor/bin/phpstan`, dan PHPUnit test suite lolos 100%.

---
*(Dokumen ini dibuat otomatis oleh Agen Web Planning dan siap dieksekusi setelah mendapatkan persetujuan pengguna).*

