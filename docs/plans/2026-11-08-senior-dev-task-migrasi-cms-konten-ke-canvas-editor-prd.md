# DOKUMEN TUGAS SENIOR DEVELOPER & PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Fitur: Migrasi & Konsolidasi Penuh Fitur CMS Konten Publik ke Canvas Editor MAGKOM
### Modul SuperAdmin Visual Site Editor — Dinas Komunikasi dan Informatika Kabupaten Sleman

---

**Kepada:** Junior Developer / AI Coding Agent (Gemini 3.8 Flash, Cursor, Copilot, Antigravity)  
**Dari:** Senior Software Architect & Lead Backend Engineer MAGKOM  
**Tanggal:** 8 November 2026  
**Status:** DRAFT PLAN — REVISED WITH DECOMMISSIONING SCOPE  
**Prioritas:** P0 — Critical Usability & Single Source of Truth Content Management  
**Target Utama:**  
1. **Rebranding:** Mengubah penamaan dari *"WordPress Site Editor"* / *"WordPress-Style Site Editor"* menjadi **"Canvas Editor"** (atau *"Canvas Editor MAGKOM"*) di seluruh antarmuka pengguna, label navigasi, breadcrumbs, modal, dan dokumentasi.
2. **Konsolidasi Fitur 100%:** Memindahkan seluruh kapabilitas pengelolaan dari *CMS Pengelolaan Landing Page & Konten Publik* lama (8 tab form statis) ke dalam satu wadah terpusat **Canvas Editor**.
3. **Multi-Page Visual Canvas:** Admin dapat beralih melihat dan mengedit halaman publik langsung di atas kanvas:
   - **Landing Page** (`/`)
   - **Peta Alur Interaktif** (`/peta-alur`)
   - **Halaman Kebijakan Privasi & Perlindungan Data** (`/kebijakan-privasi`)
4. **Pengelolaan Buku Panduan PDF Terintegrasi:** Mengunggah, memilih dari Media Library PDF, mengunduh/mempratinjau, dan mereset berkas panduan PDF resmi langsung dari Inspector Canvas Editor.
5. **Penghapusan & Pengalihan Total Fitur CMS Lama (Decommissioning):**
   - Menghapus ketergantungan pada form statis lama (`resources/views/superadmin/landing/index.blade.php`).
   - Rute `/superadmin/cms-landing` dialihkan secara otomatis (*redirect 302*) ke Canvas Editor (`/superadmin/site-editor`).
   - Menu navigasi sidebar utama diubah menjadi **"Canvas Editor"** yang mengarah langsung ke Canvas Editor.
   - Pensiunkan berkas legacy frontend lama (`resources/js/pages/superadmin/cms-builder.js`) agar tidak ada duplikasi kode.
6. **Zero Hardcode & Backward Compatibility:** Seluruh data tetap tersimpan di tabel `pengaturan` (dengan dukungan draft sandbox `draft_*`, revisi historis, rollback, dan audit logs) tanpa mengubah struktur database yang sudah ada.

---

## 1. LATAR BELAKANG & STRATEGI PENGHAPUSAN CMS LAMA

### 1.1 Masalah Dualisme Antarmuka
Sebelumnya, sistem memiliki dua antarmuka terpisah untuk tujuan yang sama:
1. **CMS Pengelolaan Landing Page Lama (`/superadmin/cms-landing`):**
   - Berbasis form builder statis terbagi menjadi 8 tab: *1. Header & SEO, 2. Hero & Media, 3. Kuota & Formasi, 4. Alur & Tahapan, 5. FAQ, 6. Footer & Kontak, 7. Kebijakan Privasi, 8. Tema & Desain*.
   - Tidak memiliki visual WYSIWYG interaktif; pengguna harus mengisi form panjang, menekan tombol simpan, lalu membuka tab pratinjau terpisah untuk melihat hasilnya.
   - Menyebabkan kebingungan (*cognitive load*) bagi SuperAdmin karena tidak jelas apakah harus mengedit di form lama atau di editor visual.
2. **Canvas Editor (Sebelumnya WordPress Site Editor) (`/superadmin/site-editor`):**
   - Memiliki WYSIWYG canvas interaktif, viewport switcher (Desktop, Tablet, Mobile), inspector sisi kanan, undo/redo, autosave, revision history, dan media library.
   - Namun sebelumnya belum mencakup kontrol visual untuk Peta Alur, Buku Panduan PDF, dan Kebijakan Privasi, serta menggunakan nama bawaan *"WordPress Site Editor"*.

### 1.2 Keputusan Arsitektur: Satu Pintu Melalui Canvas Editor (Single Hub)
Sesuai arahan teknis, **CMS Form Builder lama resmi dihapus/dipensiunkan** dan seluruh fungsinya disatukan ke dalam **Canvas Editor**:
- **Satu Pintu Navigasi:** Menu di Sidebar SuperAdmin berubah dari *"Landing Page / CMS Landing Page"* menjadi **"Canvas Editor"**.
- **Pengalihan Mulus (Seamless Redirect):** Rute lama `/superadmin/cms-landing` tidak dimatikan melainkan dialihkan otomatis (*redirect*) menuju Canvas Editor, sehingga link lama atau bookmark tidak akan mengalami error 404.
- **Pembersihan Aset Legacy:** Berkas JavaScript lama `cms-builder.js` dinonaktifkan sehingga bundle frontend lebih ramping dan seluruh logika kini berpusat pada modul ES modern di `resources/js/pages/superadmin/site-editor/`.

---

## 2. ARSITEKTUR SISTEM & PEMETAAN DATA (DATA SETTINGS MAPPING)

Seluruh data konten publik dikelola oleh singleton `\App\Support\Pengaturan` dan `\App\Services\LandingCmsStagingService`. Tidak ada pembuatan tabel baru, seluruh field mapping kompatibel 100%:

### 2.1 Matriks Pemetaan Pengaturan (Settings Keys)
| Bagian / Halaman | Kunci Pengaturan (`pengaturan.kunci`) | Draf Sandbox (`draft_*`) | Tipe Data | Deskripsi & Komponen UI |
|---|---|---|---|---|
| **Header & SEO** | `landing_meta_title`, `landing_meta_desc`, `landing_header_brand_name`, `landing_header_brand_sub`, `landing_header_nav` | `draft_landing_meta_title`, dst. | String / JSON | Judul tab browser, deskripsi search engine, brand navbar, link menu |
| **Hero Banner** | `landing_hero_title`, `landing_hero_kicker`, `landing_hero_year`, `landing_hero_period_info`, `landing_hero_cta_text`, `landing_hero_image` | `draft_landing_hero_title`, dst. | String / Path Gambar | Teks hero, kicker Sleman, foto gedung Kominfo, tombol aksi |
| **Buku Panduan PDF** | `landing_panduan_pdf`, `landing_panduan_pdf_name`, `landing_tahapan_btn_pdf` | `draft_landing_panduan_pdf`, dst. | Path Storage / String | Berkas dokumen PDF panduan pendaftaran, nama asli berkas, label tombol |
| **Kuota & Formasi** | `landing_kuota_heading`, `landing_kuota_subheading`, teks tombol ketersediaan kuota | `draft_landing_kuota_heading`, dst. | String | Judul, subjudul, petunjuk pemilihan jenjang SMK/Mahasiswa |
| **Alur & Tahapan** | `landing_tahapan_heading`, `landing_tahapan_summary`, `landing_tahapan_steps`, `landing_tahapan_btn_peta` | `draft_landing_tahapan_steps`, dst. | String / JSON (Array 4 Pos) | Data 4 pos alur (`step`, `title`, `badge`, `desc`). **Digunakan bersama oleh Landing Page dan Peta Alur** |
| **Peta Alur Interaktif** | Mengonsumsi data yang sama dari `landing_tahapan_steps` | Mengonsumsi `draft_landing_tahapan_steps` saat preview | JSON | Simulator panorama: Halte Pos 01 s/d Pos 04 disinkronkan secara otomatis |
| **FAQ (Tanya Jawab)** | `landing_faq_heading`, `landing_faq_data` | `draft_landing_faq_data`, dst. | String / JSON (Kategori & QnA) | Kategori FAQ (Syarat, Akun, Kuis, Benefit, Kewajiban) dan daftar tanya jawab |
| **Footer & Kontak** | `landing_footer_alamat`, `landing_footer_telepon`, `landing_footer_email`, `landing_footer_jam_layanan`, `landing_footer_copyright`, `landing_footer_medsos`, `landing_footer_tautan` | `draft_landing_footer_alamat`, dst. | String / JSON | Kontak fisik dinas, email helpdesk, jam layanan ASN, tautan medsos resmi |
| **Kebijakan Privasi** | `kebijakan_privasi_badge`, `kebijakan_privasi_title`, `kebijakan_privasi_updated_at`, `kebijakan_privasi_sections` | `draft_kebijakan_privasi_sections`, dst. | String / JSON (Pasal 1 s/d N) | Judul kebijakan, badge UU PDP, tanggal berlaku, pasal-pasal perlindungan data |
| **Tema & Desain** | `landing_theme_primary_color`, `landing_theme_navy_dark`, `landing_theme_accent_color`, `landing_theme_gold_color`, `landing_theme_bg_color`, `landing_theme_font_display`, `landing_theme_font_body` | `draft_landing_theme_*`, dst. | Hex Color / Font Name | Palet warna resmi instansi, font Lexend & Source Sans 3, border radius |

---

## 3. SPESIFIKASI WIREFRAME & TAMPILAN KANVAS BARU

### 3.1 Wireframe Top Bar & Kanvas Canvas Editor Terkonsolidasi

```text
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [CANVAS EDITOR TOPBAR]                                                                                                           │
│ [Logo Sleman]  [Canvas Editor]  │  [ Halaman: 📄 Landing Page ▾ ]  │  [ + Inserter ]  [ ↶ Undo ] [ ↷ Redo ]  │ [ 💻 Tablet 📱 ]    │
│ Status: [ ✓ Draf Sandbox Aktif ]   [ ❓ Bantuan ]   [ 🕒 Riwayat ]   [ 👁️ Pratinjau ]   [ Simpan Draf ]   [ Terbitkan ]   [ ⚙️ ]  │
├────────────────────────────────────────────────────────────────────────────────────────────────────┬─────────────────────────────┤
│ [AREA KANVAS TENGAH (THE CANVAS FRAME)]                                                            │ [RIGHT INSPECTOR PANEL]     │
│ Background: Studio Dark Navy Slate / Mesh Shadow Frame                                             │ Lebar: 340px                │
│                                                                                                    │                             │
│   ┌── [MODE DROPDOWN HALAMAN TERPILIH] ────────────────────────────────────────────────────────┐   │ [ Tab: Page ]  [ *Block* ]  │
│   │ Pilihan Halaman:                                                                           │   │ ─────────────────────────── │
│   │  • 📄 Landing Page Utama (/)                                                               │   │ Context: Halaman Terpilih   │
│   │  • 🗺️ Peta Alur Interaktif (/peta-alur)                                                    │   │                             │
│   │  • 🔒 Kebijakan Privasi (/kebijakan-privasi)                                               │   │ Jika Halaman = Peta Alur:   │
│   └────────────────────────────────────────────────────────────────────────────────────────────┘   │  ▼ Pos 01: Registrasi       │
│                                                                                                    │    Judul Pos: [Registrasi]  │
│   ┌────────────────────────────────────────────────────────────────────────────────────────────┐   │    Deskripsi Pos: [...]     │
│   │ [IFRAME KANVAS SESUAI HALAMAN YANG DIPILIH]                                                │   │  ▼ Pos 02: Administrasi     │
│   │                                                                                            │   │  ▼ Pos 03: Pelaksanaan      │
│   │ [KASUS A: LANDING PAGE]                                                                    │   │  ▼ Pos 04: Kelulusan        │
│   │ Tampilan Hero, Kuota, Tahapan, FAQ, Footer (Full WYSIWYG Editable)                         │   │                             │
│   │                                                                                            │   │ Jika Halaman = Kebijakan:   │
│   │ [KASUS B: PETA ALUR INTERAKTIF]                                                            │   │  ▼ Badge: [UU PDP No 27...] │
│   │ Diorama Jalan Raya, Mobil Analog, 4 Halte Checkpoint (Sinkron Otomatis!)                   │   │  ▼ Judul: [Kebijakan Privasi│
│   │                                                                                            │   │  ▼ Pasal 1 s/d 5 (Repeater) │
│   │ [KASUS C: KEBIJAKAN PRIVASI]                                                               │   │                             │
│   │ Surat Keputusan & Dokumen Resmi UU PDP dengan Pasal-Pasal Dinamis                          │   │ ▼ Dokumen Panduan PDF       │
│   │                                                                                            │   │   [📄 Panduan-Resmi.pdf]    │
│   │                                                                                            │   │   [Pilih dari Media Library]│
│   │                                                                                            │   │   [Unduh]  [Reset Bawaan]   │
│   └────────────────────────────────────────────────────────────────────────────────────────────┘   │                             │
└────────────────────────────────────────────────────────────────────────────────────────────────────┴─────────────────────────────┘
```

---

## 4. CHECKLIST IMPLEMENTASI BERTAPAH (TASK SENIOR → JUNIOR DEVELOPER)

Berikut adalah instruksi pengerjaan teknis yang harus dieksekusi secara terstruktur:

### Task 1: Rebranding Total dari "WordPress Site Editor" ke "Canvas Editor"
- [ ] **1.1 Update Judul & Meta View:**
  - File: `resources/views/superadmin/site-editor/index.blade.php`
  - Ganti `<title>WordPress Site Editor – ...</title>` menjadi `<title>Canvas Editor – Landing Page & Konten Publik MAGKOM</title>`.
  - Ganti title iframe menjadi `"Kanvas Pratinjau Interaktif Canvas Editor"`.
- [ ] **1.2 Update Label Topbar:**
  - File: `resources/views/superadmin/site-editor/partials/_topbar.blade.php`
  - Ganti teks `<span class="wp-back-label">Site Editor</span>` menjadi `<span class="wp-back-label">Canvas Editor</span>`.
- [ ] **1.3 Update Navigasi Sidebar Dashboard:**
  - File: `resources/views/layouts/dashboard.blade.php` (baris ~3466)
  - Ganti link menu:
    ```blade
    <a href="{{ route('superadmin.site-editor.index') }}" class="nav-sub-item {{ request()->routeIs('superadmin.site-editor.*') || request()->routeIs('superadmin.landing.*') ? 'active' : '' }}">
        <span>{{ __('Canvas Editor') }}</span>
        <span class="sr-only">Canvas Editor Konten Publik</span>
    </a>
    ```
- [ ] **1.4 Pertahankan Kompatibilitas CSS & JS:**
  - Tetap gunakan class existing (seperti `.wp-*`) di backend stylesheet agar tidak merusak selector CSS/JS yang sudah ada, namun semua teks tampak (*human-facing text*) menggunakan nama **Canvas Editor**.

### Task 2: Multi-Page Canvas Engine (Landing Page, Peta Alur, Kebijakan Privasi)
- [ ] **2.1 Tambahkan Page Selector Switcher di Topbar:**
  - File: `resources/views/superadmin/site-editor/partials/_topbar.blade.php`
  - Tambahkan dropdown pilihan halaman di area tengah/kiri topbar:
    ```html
    <div class="canvas-page-selector-wrap">
        <label for="select-canvas-page" class="sr-only">Pilih Halaman yang Diedit</label>
        <select id="select-canvas-page" class="canvas-page-select" title="Pilih Halaman Publik yang Sedang Diedit">
            <option value="landing" selected>📄 Landing Page (/)</option>
            <option value="peta-alur">🗺️ Peta Alur Interaktif (/peta-alur)</option>
            <option value="kebijakan-privasi">🔒 Kebijakan Privasi (/kebijakan-privasi)</option>
        </select>
    </div>
    ```
- [ ] **2.2 Perluas Endpoint Preview Controller:**
  - File: `app/Http/Controllers/SuperAdmin/LandingCmsController.php` (method `preview`)
  - Tambahkan parameter `page`:
    - Jika `page === 'peta-alur'`: Render `view('public.peta-alur-interaktif', ['isPreview' => true, 'editor_mode' => $request->boolean('editor_mode')])`.
    - Jika `page === 'kebijakan-privasi'`: Render `view('public.kebijakan-privasi', ['isPreview' => true, 'editor_mode' => $request->boolean('editor_mode')])`.
    - Default / `landing`: Tetap render `view('public.landing', ...)`.
  - Pastikan `Pengaturan::setPreviewMode(true)` aktif pada semua branch sehingga membaca draf kontainer sandbox.
- [ ] **2.3 Penanganan Event Pergantian Halaman di JS:**
  - File: `resources/js/pages/superadmin/site-editor/editor-app.js`
  - Ketika dropdown `#select-canvas-page` berubah:
    1. Periksa `isDirty` (jika ada perubahan yang belum disimpan, berikan konfirmasi atau lakukan autosave terlebih dahulu).
    2. Ubah `iframe.src` ke URL preview halaman yang dipilih (misal: `route('superadmin.landing.preview') + '?page=' + selectedPage + '&editor_mode=1'`).
    3. Sesuaikan tampilan tab Inspector agar relevan dengan halaman yang sedang dibuka.

### Task 3: Integrasi Pengelolaan Buku Panduan PDF Langsung di Canvas Editor
- [ ] **3.1 Tambahkan Accordion Pengelolaan PDF di Inspector:**
  - File: `resources/views/superadmin/site-editor/partials/_inspector.blade.php`
  - Di Tab Page atau Accordion Hero/Tahapan, tambahkan sub-section khusus **Buku Panduan Pendaftaran (PDF)**:
    - Status Berkas: Menampilkan apakah sedang menggunakan file kustom yang diunggah atau template generator DomPDF bawaan sistem.
    - Informasi Nama Berkas: Menampilkan nama file aktif (misal `landing_panduan_pdf_name`).
    - Tombol Aksi:
      - `[ Pilih dari Media Library ]`: Membuka modal Media Library dengan filter otomatis tipe `pdf`.
      - `[ Unggah Berkas Baru ]`: Membuka file picker langsung untuk berkas `.pdf` (maks 5MB).
      - `[ Unduh / Pratinjau PDF ]`: Tautan langsung untuk membuka PDF saat ini.
      - `[ Reset ke Template Bawaan ]`: Tombol untuk mereset kembali ke generator PDF sistem bawaan.
- [ ] **3.2 Hubungkan dengan Controller & Media Library:**
  - File: `app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php`
  - Buat method `resetPanduanPdf(Request $request): JsonResponse` yang mereset `landing_panduan_pdf` dan `landing_panduan_pdf_name` ke nilai kosong (mirip `destroyPanduan` di CMS lama, namun mengembalikan respons JSON standar Canvas Editor).
  - Daftarkan route di `routes/web.php` dalam grup `superadmin.site-editor.reset-panduan`.
  - Hubungkan aksi pilih PDF pada Media Library ke field input draf `landing_panduan_pdf`.

### Task 4: Inspector Panel untuk Peta Alur & Kebijakan Privasi
- [ ] **4.1 Accordion Pengaturan Halte Peta Alur:**
  - File: `resources/views/superadmin/site-editor/partials/_inspector.blade.php`
  - Tambahkan form kontrol untuk 4 Pos Tahapan:
    - Pos 01: Judul (`step1_title`), Deskripsi Narasi (`step1_desc`).
    - Pos 02: Judul (`step2_title`), Deskripsi Narasi (`step2_desc`).
    - Pos 03: Judul (`step3_title`), Deskripsi Narasi (`step3_desc`).
    - Pos 04: Judul (`step4_title`), Deskripsi Narasi (`step4_desc`).
    - Catatan: Perubahan pada pos ini otomatis mengupdate `landing_tahapan_steps` yang dikonsumsi oleh blok Tahapan di Landing Page DAN simulator di Peta Alur.
- [ ] **4.2 Accordion Pengaturan Kebijakan Privasi:**
  - File: `resources/views/superadmin/site-editor/partials/_inspector.blade.php`
  - Tambahkan accordion **Kebijakan Privasi & Perlindungan Data**:
    - `kebijakan_privasi_badge`: Input teks (default: "Undang-Undang PDP No. 27 Tahun 2022").
    - `kebijakan_privasi_title`: Input teks (default: "Kebijakan Privasi & Perlindungan Data").
    - `kebijakan_privasi_updated_at`: Input teks tanggal pembaruan.
    - Dynamic Repeater Pasal-pasal:
      - Menampilkan daftar pasal (Nomor, Judul Pasal, Konten Paragraf, Butir Poin `items`, dan Kotak Peringatan `highlight`).
      - Tombol tambah pasal baru `[ + Tambah Pasal ]` dan tombol hapus pasal.
- [ ] **4.3 Penanganan Data di State Management JS:**
  - File: `resources/js/pages/superadmin/site-editor/editor-state.js`
  - Pastikan state manager dapat melacak mutasi pada objek `kebijakan_privasi_sections` dan `landing_tahapan_steps` dengan autosave, dirty indicator, dan undo/redo.

### Task 5: Penghapusan, Pengalihan, & Decommissioning Fitur CMS Lama
- [ ] **5.1 Pengalihan Otomatis Rute CMS Lama:**
  - File: `app/Http/Controllers/SuperAdmin/LandingCmsController.php` (method `index`)
  - Modifikasi method `index()`:
    ```php
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('superadmin.site-editor.index')
            ->with('info', 'CMS Pengelolaan Konten Publik kini terpusat sepenuhnya di Canvas Editor.');
    }
    ```
  - Ini memastikan setiap pengguna atau tautan internal yang membuka `/superadmin/cms-landing` langsung dialihkan dengan rapi ke Canvas Editor.
- [ ] **5.2 Pembersihan Berkas View & Script Legacy:**
  - File `resources/views/superadmin/landing/index.blade.php` dinonaktifkan / digantikan dengan template pengalihan ringan (*fallback notice*).
  - Skrip `resources/js/pages/superadmin/cms-builder.js` dipensiunkan agar tidak membebani kompilasi Vite.
- [ ] **5.3 Penyelarasan Tombol Kembali di Topbar Canvas Editor:**
  - File: `resources/views/superadmin/site-editor/partials/_topbar.blade.php`
  - Arahkan tombol logo/kembali ke `route('superadmin.dashboard')` (Dashboard SuperAdmin), bukan lagi ke CMS lama yang sudah dipensiunkan.

### Task 6: Validasi, Staging Sandbox, & Concurrency Control
- [ ] **6.1 Update Request Validation:**
  - File: `app/Http/Requests/SuperAdmin/SaveLandingSiteEditorRequest.php`
  - Pastikan rules mengizinkan field baru bila dikirimkan via AJAX:
    - `landing_panduan_pdf` (nullable, string)
    - `landing_panduan_pdf_name` (nullable, string)
    - `kebijakan_privasi_badge` (nullable, string, max:150)
    - `kebijakan_privasi_title` (nullable, string, max:255)
    - `kebijakan_privasi_updated_at` (nullable, string, max:255)
    - `kebijakan_privasi_sections` (nullable, array)
    - `kebijakan_privasi_sections.*.nomor` (nullable, integer)
    - `kebijakan_privasi_sections.*.judul` (nullable, string, max:255)
    - `kebijakan_privasi_sections.*.konten` (nullable, string)
    - `kebijakan_privasi_sections.*.items` (nullable, array)
    - `kebijakan_privasi_sections.*.highlight` (nullable, string)
- [ ] **6.2 Update Staging Service Saver:**
  - File: `app/Services/LandingCmsStagingService.php` (method `saveSiteEditorDraft`)
  - Pastikan field-field di atas masuk ke draf container `draft_*` sehingga dapat di-preview secara terisolasi sebelum dipublikasikan.

### Task 7: Styling & Polish Sesuai Desain System MagKom
- [ ] **7.1 Style Selector Halaman & Accordion Baru:**
  - File: `resources/css/pages/superadmin/site-editor.css`
  - Buat style scoped untuk:
    - `.canvas-page-selector-wrap` dan `.canvas-page-select` di Topbar (desain rapi, border halus, font Inter/Plus Jakarta Sans, focus ring biru Sleman).
    - `.wp-accordion.block-form-kebijakan` dan `.wp-repeater-card` untuk kartu pasal kebijakan privasi.
    - Kontainer status PDF dan action buttons (`.pdf-status-badge`, `.btn-pdf-action`).
  - **Patuhi aturan MAGKOM**: DILARANG menambahkan tag `<style>` di Blade. Semua CSS wajib di file `.css`.

---

## 5. QUALITY GATE & VERIFIKASI (TEST SUITES)

Sebelum menyatakan tugas selesai, seluruh pengujian berikut wajib dijalankan dan dipastikan **HIJAU (PASSED)**:

1. **Feature Test Otomatis (PHPUnit):**
   - Buat test suite baru: `tests/Feature/SuperAdmin/CanvasEditorConsolidatedFeaturesTest.php`
   - Test cases:
     1. `test_superadmin_can_access_canvas_editor_with_rebranded_title()`: Memastikan halaman berlabel Canvas Editor dan dapat diakses SuperAdmin.
     2. `test_old_cms_landing_index_redirects_to_canvas_editor()`: Memastikan `/superadmin/cms-landing` melakukan redirect otomatis ke `/superadmin/site-editor`.
     3. `test_preview_supports_switching_between_landing_peta_alur_and_kebijakan()`: Memastikan endpoint preview merespons halaman yang diminta dengan status HTTP 200 dan mode pratinjau aktif.
     4. `test_canvas_editor_can_save_draft_for_peta_alur_and_kebijakan_privasi()`: Menguji penyimpanan draf sandbox via AJAX JSON untuk field tahapan alur dan pasal kebijakan.
     5. `test_canvas_editor_can_select_and_reset_panduan_pdf()`: Menguji integrasi pemilihan PDF dari media library dan reset ke default generator.
     6. `test_non_superadmin_is_forbidden_from_canvas_editor()`: Memastikan pengamanan RBAC dan hak akses read-only terjaga ketat.
2. **Regression Test:**
   - Jalankan test suite landing page: `php artisan test tests/Feature/LandingPage/`
   - Pastikan seluruh 98+ tests landing page yang ada tetap lulus tanpa regresi.
3. **Frontend Build:**
   - Jalankan `npm run build` untuk memastikan aset CSS dan JS terkompilasi bersih tanpa syntax error.
4. **Code Style & Formatting:**
   - Jalankan `./vendor/bin/pint --test` untuk memastikan kepatuhan standar PSR-12 / Laravel Pint.

---

## 6. BATASAN & LARANGAN TEKNIS (NON-NEGOTIABLE RULES)

Sesuai aturan permanen proyek MAGKOM (`.agents/rules/AGENTS.md`):
1. **Dilarang Menulis CSS/JS di Blade:**
   - Jangan menyisipkan `<style>` atau `<script>` di file Blade manapun.
   - Semua CSS harus berada di `resources/css/pages/superadmin/site-editor.css`.
   - Semua JS harus berada di `resources/js/pages/superadmin/site-editor/`.
2. **Dilarang Mengubah Skema Database Tanpa Alasan:**
   - Manfaatkan struktur tabel `pengaturan` dan key-value storage yang sudah terbukti stabil.
3. **Pertahankan CSRF & RBAC:**
   - Semua permintaan AJAX POST/PUT/DELETE wajib menyertakan token CSRF dan header `X-Requested-With: XMLHttpRequest`.
   - Hanya pengguna dengan peran `SuperAdmin` (dan bukan status Read-Only) yang diizinkan melakukan modifikasi konten.
4. **Pertahankan Audit Trail:**
   - Setiap pembaruan atau publikasi konten publik wajib dicatat melalui `AuditService` untuk akuntabilitas instansi pemerintah.
