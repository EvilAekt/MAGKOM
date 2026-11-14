# TASK SPECIFICATION & IMPLEMENTATION PRD (v1.0)
## Penyelarasan Tema Warna Biru Sleman pada Floating Flyout Submenu Sidebar Mini (`nav-sm`)

- **Dokumen Referensi:** PRD MagKom v1.2, Desain Sistem Dashboard MagKom (Sleman Design Tokens), Aturan Frontend MagKom (Rule 3 & 4)
- **Peran Pembuat Dokumen:** Senior Lead Developer
- **Target Pembaca / Pelaksana:** Junior Developer & AI Assistant (Gemini 3.8 Flash / Claude / Cursor)
- **Tingkat Kerumitan:** Mudah &ndash; Menengah (*CSS Design Tokens, Color Harmony, Theme Synchronization*)
- **Target Modul:** Layout Styling Dashboard (`resources/css/layouts/gentelella.css` & `resources/css/layouts/dashboard-layout-tokens.css`)
- **Task ID:** `TASK-UI-MINI-SIDEBAR-BLUE-THEME-01`
- **Status Dokumen:** **SELESAI DIEKSEKUSI & DIVERIFIKASI (*COMPLETED & VERIFIED*)**

---

## 1. DIRECTIVE & PESAN DARI SENIOR LEAD DEVELOPER

Halo Junior Developer / AI Assistant! 👋

Perhatikan tangkapan layar yang dilaporkan oleh pengguna berikut ini:

| Kondisi Saat Ini: Flyout Berwarna Dark Slate / Abu Gelap |
|:---:|
| !["Flyout Menu Saat Ini (Dark Slate) vs Sidebar Biru Sleman"](/C:/Users/galan/.gemini/antigravity/brain/b04d2e86-2b10-4ed5-a980-3894b843e602/.user_uploaded/media_1794627787995_916a92d7.png) |

### 🔍 Analisis Inkonsistensi Visual (Visual Inconsistency RCA):
1. **Sidebar Kiri (`left_col` / `#mainSidebar`)**:
   - Menggunakan warna resmi **Sleman Navy Blue** (`var(--sidebar-bg, #02275D)` pada mode terang dan `#011C44` pada mode gelap).
   - Tombol grup yang aktif/terpilih (seperti ikon Administrasi di sebelah kiri) memiliki latar belakang kapsul biru elektrik/royal blue (`#0A4ABF` / `#0641CC` atau `rgba(37, 99, 235, 0.22)`) dengan ikon berwarna *cyan/sky blue* (`#38BDF8`).
2. **Flyout Menu Melayang Saat Ini (`.nav-sm-flyout-portal`)**:
   - Menggunakan latar belakang **Dark Slate/Charcoal** (`#0F172A`).
   - Warna `#0F172A` ini tampak seperti komponen terpisah yang "asing" dan terputus dari palet warna sidebar di sampingnya.
   - Header grup menu menggunakan warna abu-abu pudar (`#94A3B8`).
   - Item aktif ("Verifikasi Berkas") menggunakan latar belakang semi-transparan tipis yang kurang menyatu dengan gaya kapsul aktif di sidebar.
3. **Instruksi Pengguna**:
   > *"buat plan untuk ini di bikin warna biru sama kayak sampingnya..."*
   Artinya, kita harus menyelaraskan palet warna flyout popover dan seluruh elemen di dalamnya agar **senada dan menyatu sempurna dengan warna biru sidebar Sleman di sampingnya**, baik pada mode terang maupun mode gelap (*dark mode*).

---

## 2. ARSITEKTUR PALET WARNA (COLOR MATRIX & DESIGN TOKENS)

Sesuai `docs/07-DESIGN-SYSTEM-MAGKOM.md` dan `resources/css/layouts/gentelella.css`, berikut adalah matriks penyelarasan warna:

| Elemen Komponen | Warna Eksisting (Dark Slate) | Warna Baru (Sleman Navy Harmony) | Token / Nilai CSS | Catatan Desain UI/UX |
| :--- | :--- | :--- | :--- | :--- |
| **Container Flyout (`.nav-sm-flyout-portal`)** | `#0F172A` (Slate Gelap) | `#02275D` (Sleman Deep Navy) | `var(--sidebar-bg, #02275D)` | Menyatu sempurna dengan sidebar kiri |
| **Border Flyout** | `rgba(255, 255, 255, 0.14)` | `rgba(147, 197, 253, 0.22)` | `border: 1px solid rgba(147, 197, 253, 0.22);` | Garis tepi biru kristal elegan, tanpa silau |
| **Shadow Elevasi** | Bayangan hitam kusam | Bayangan biru-dongker dalam | `box-shadow: 0 14px 34px -4px rgba(0, 15, 45, 0.65), 0 6px 16px -2px rgba(0, 0, 0, 0.45);` | Memberikan kesan melayang (*floating depth*) yang kaya |
| **Header Flyout (`.nav-sm-flyout-header`)** | `#94A3B8` (Abu-abu) | `#93C5FD` (Soft Sky Blue) | `color: #93C5FD;` | Kategori terlihat jelas dan elegan |
| **Garis Pembatas Header** | `rgba(255, 255, 255, 0.08)` | `rgba(147, 197, 253, 0.15)` | `border-bottom: 1px solid rgba(147, 197, 253, 0.15);` | Pembatas halus berona biru |
| **Item Default (`.nav-sm-flyout-item`)** | `#CBD5E1` (Abu Slate) | `#E2E8F0` / `#F8FAFC` | `color: #E2E8F0;` | Kontras tinggi WCAG 2.1 AA di atas latar navy |
| **Item Hover State** | `rgba(255, 255, 255, 0.08)` | `rgba(255, 255, 255, 0.12)` | `background: rgba(255, 255, 255, 0.12); color: #FFFFFF;` | Indikasi interaksi kursor yang jelas |
| **Item Aktif (`.nav-sm-flyout-item.active`)** | `rgba(37, 99, 235, 0.22)` | `#013D8D` (Sleman Royal Navy) | `background: #013D8D; border: 1px solid rgba(147, 197, 253, 0.35); color: #FFFFFF;` | Identik dengan `.nav-sub-item.active` pada sidebar penuh |
| **Indikator Titik Aktif (`::before`)** | `#3B82F6` | `#38BDF8` (Cyan Glow) | `background: #38BDF8; box-shadow: 0 0 8px #38BDF8;` | Titik penanda halaman saat ini lebih bercahaya |
| **Tooltip Tunggal (`.nav-sm-tooltip`)** | `#1E293B` | `#02275D` | `background: #02275D; border: 1px solid rgba(147, 197, 253, 0.25); color: #FFFFFF;` | Konsisten di seluruh tooltip mode mini |
| **Dark Theme (`[data-theme="dark"]`)** | Tetap `#0F172A` | `#011C44` | `background: #011C44 !important; border-color: rgba(59, 130, 246, 0.25);` | Menyelaraskan dengan dark-mode sidebar `#011C44` |

---

## 3. RUANG LINGKUP & TARGET FILE

Sesuai aturan arsitektur proyek (**Rule 3 & Rule 4 AGENTS.md**):
- **DILARANG** menulis CSS di dalam file Blade (`style="..."` atau `<style>`).
- Perubahan ini **100% berada di lapisan CSS** pada folder `resources/css/layouts/`.
- File Blade (`resources/views/layouts/dashboard.blade.php`) dan file JavaScript (`resources/js/layouts/dashboard.js`) **TIDAK PERLU DIUBAH** karena struktur DOM dan kelas-kelasnya (`.nav-sm-flyout-portal`, `.nav-sm-flyout-header`, `.nav-sm-flyout-item`, `.nav-sm-tooltip`) sudah terpasang dengan benar.

### File Sasaran:
1. `resources/css/layouts/gentelella.css`
2. `resources/css/layouts/dashboard-layout-tokens.css`

---

## 4. PANDUAN EKSEKUSI SPESIFIK (STEP-BY-STEP IMPLEMENTATION DIRECTIVE)

Junior developer atau model AI pelaksana wajib menerapkan pembaruan CSS berikut secara presisi:

### Langkah 1: Modifikasi `resources/css/layouts/gentelella.css`

Cari blok `.nav-sm-flyout-portal` (sekitar baris 840&ndash;935) dan perbarui properti warnanya:

```css
/* =========================================================================
   SEBELUMNYA (OLD - Dark Slate / Abu Gelap):
   ========================================================================= */
.nav-sm-flyout-portal {
    background: #0F172A;
    border: 1px solid rgba(255, 255, 255, 0.14);
    box-shadow: 0 12px 30px -4px rgba(0, 0, 0, 0.6), 0 6px 14px -3px rgba(0, 0, 0, 0.4);
}
.nav-sm-flyout-header {
    color: #94A3B8;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.nav-sm-flyout-item {
    color: #CBD5E1;
}
.nav-sm-flyout-item:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #93C5FD;
}
.nav-sm-flyout-item.active {
    background: rgba(37, 99, 235, 0.22);
    color: #60A5FA;
}
.nav-sm-tooltip {
    background: #1E293B;
}

/* =========================================================================
   SESUDAHNYA (NEW - Sleman Navy Blue Theme Harmony):
   ========================================================================= */
.nav-sm-flyout-portal {
    position: fixed;
    z-index: 1060;
    min-width: 220px;
    max-width: 260px;
    background: var(--sidebar-bg, #02275D);
    border: 1px solid rgba(147, 197, 253, 0.22);
    border-radius: 12px;
    box-shadow: 0 14px 34px -4px rgba(0, 15, 45, 0.65), 0 6px 16px -2px rgba(0, 0, 0, 0.45);
    padding: 0.5rem;
    animation: navSmFlyoutAnim 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    pointer-events: auto;
}

.nav-sm-flyout-header {
    padding: 0.375rem 0.75rem 0.375rem;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #93C5FD;
    border-bottom: 1px solid rgba(147, 197, 253, 0.15);
    margin-bottom: 0.375rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.nav-sm-flyout-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.825rem;
    color: #E2E8F0;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.15s ease;
    line-height: 1.3;
}

.nav-sm-flyout-item:hover {
    background: rgba(255, 255, 255, 0.12);
    color: #FFFFFF;
    transform: translateX(2px);
}

.nav-sm-flyout-item.active {
    background: #013D8D;
    border: 1px solid rgba(147, 197, 253, 0.35);
    color: #FFFFFF;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(1, 30, 75, 0.4);
}

.nav-sm-flyout-item.active::before {
    content: '';
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #38BDF8;
    box-shadow: 0 0 8px #38BDF8;
    flex-shrink: 0;
}

.nav-sm-tooltip {
    position: fixed;
    z-index: 1060;
    background: var(--sidebar-bg, #02275D);
    color: #F8FAFC;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.375rem 0.625rem;
    border-radius: 6px;
    border: 1px solid rgba(147, 197, 253, 0.25);
    box-shadow: 0 4px 14px rgba(0, 15, 45, 0.45);
    pointer-events: none;
    white-space: nowrap;
    animation: navSmTooltipAnim 0.12s ease-out;
}

/* Dark Theme Overrides */
[data-theme="dark"] .nav-sm-flyout-portal,
[data-theme="dark"] .nav-sm-tooltip {
    background: #011C44 !important;
    border-color: rgba(59, 130, 246, 0.25);
}
```

---

### Langkah 2: Sinkronisasi pada `resources/css/layouts/dashboard-layout-tokens.css`

Lakukan pembaruan token CSS yang identik pada file `resources/css/layouts/dashboard-layout-tokens.css` (sekitar baris 2758&ndash;2840) untuk memastikan arsitektur layout modular tetap sinkron 100%.

---

## 5. QUALITY GATES & VERIFIKASI PENGUJIAN

Setelah perubahan CSS diterapkan, lakukan langkah verifikasi berikut:

1. **Kompilasi Aset Frontend (Vite)**:
   ```bash
   npm run build
   ```
   *Ekspektasi*: Bundle `app.css` dan `app.js` berhasil dikompilasi tanpa syntax error.
2. **Pemeriksaan Standar Kode (Laravel Pint)**:
   ```bash
   ./vendor/bin/pint --test
   ```
   *Ekspektasi*: Format kode PHP tetap bersih dan lolos validasi.
3. **Pemeriksaan Analisis Statis (PHPStan)**:
   ```bash
   ./vendor/bin/phpstan analyse app database/seeders --memory-limit=1G
   ```
   *Ekspektasi*: 0 errors.
4. **Pengujian Regresi Layout & Sidebar (PHPUnit)**:
   ```bash
   php artisan test --filter=Sidebar
   ```
   *Ekspektasi*: Seluruh 13 pengujian terkait layout sidebar tetap berstatus **PASS** (161 assertions).
5. **Inspeksi Visual Mandiri**:
   - Buka browser dan ubah sidebar ke mode mini (`nav-sm`).
   - Klik ikon **Administrasi** &rarr; Flyout popover muncul dengan warna latar belakang biru tua khas Sleman (`#02275D`), selaras dan menyatu dengan kolom sidebar di sebelah kirinya.
   - Periksa item aktif ("Verifikasi Berkas") &rarr; Menggunakan kapsul biru royal (`#013D8D`) dengan titik cyan glow (`#38BDF8`) dan teks putih tajam.
   - Ganti tema ke Dark Mode &rarr; Flyout otomatis beradaptasi ke `#011C44`.

---

## 6. DEFINITION OF DONE (DoD)

- [x] Latar belakang flyout submenu (`.nav-sm-flyout-portal`) berwarna biru Sleman (`#02275D` / `var(--sidebar-bg)`), selaras dengan sidebar di sampingnya.
- [x] Border dan separator header menggunakan aksen biru kristal halus (`rgba(147, 197, 253, 0.22)`).
- [x] Judul header grup menu berwarna biru muda cerah (`#93C5FD`).
- [x] Item aktif submenu memiliki gaya kapsul royal blue (`#013D8D`), teks putih (`#FFFFFF`), dan indikator titik cyan bercahaya (`#38BDF8`).
- [x] Tooltip single item (`.nav-sm-tooltip`) selaras dengan palet biru Sleman.
- [x] Mode gelap (`[data-theme="dark"]`) beradaptasi ke latar `#011C44`.
- [x] Tidak ada penambahan CSS inline pada file Blade (Kepatuhan Rule 3 & 4 AGENTS.md).
- [x] Build `npm run build`, linting Pint, dan unit test PHPUnit lulus 100%.
