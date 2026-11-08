# DOKUMEN TUGAS SENIOR DEVELOPER & PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Fitur: WordPress.org Style Site Editor (Gutenberg Block Canvas & Right Inspector) untuk Landing Page MAGKOM
### Modul SuperAdmin CMS Landing Page — Dinas Komunikasi dan Informatika

---

**Kepada:** Junior Developer / AI Coding Agent (Gemini 3.8 Flash, Cursor, Copilot)  
**Dari:** Senior Lead Software Engineer MAGKOM  
**Tanggal:** 7 November 2026  
**Status:** Ready for Review & Implementation  
**Prioritas:** P1 — High Usability & Paritas Antarmuka WordPress.org  
**Target:** Menggantikan form CMS biasa dengan antarmuka pengeditan landing page yang **100% mengadopsi pola WordPress.org Site Editor (Gutenberg)**:
1. **Top Bar WP Editor**: Tombol Block Inserter `[+]`, Undo/Redo, List View (Pohon Blok), Viewport Preview (Desktop, Tablet, Mobile), Simpan Draf (*Save Draft*), dan Terbitkan (*Publish*).
2. **Interactive WYSIWYG Canvas (Tengah)**: Setiap bagian landing page diperlakukan sebagai **Block** (Blok Hero, Blok Kuota, Blok Tahapan, Blok FAQ, Blok Footer). Admin dapat **mengklik dan mengetik langsung teks di atas kanvas (*inline editing*)**.
3. **Floating Block Toolbar**: Toolbar melayang di atas blok yang sedang aktif (Alignment, Geser Urutan Atas/Bawah, Hapus/Reset).
4. **Right Block Inspector Panel (Kanan)**: Sesuai standar WordPress.org, panel inspector berada di **sisi kanan**, memiliki tab **[Halaman]** dan **[Blok]**, dengan kontrol ukuran eksplisit dalam satuan **Pixel (`px`)**:
   - **Tipografi**: Ukuran font (`px`), Tinggi baris (*line-height*), Ketebalan font.
   - **Dimensi & Spasi**: Padding atas/bawah (`px`), Padding samping (`px`), Jarak antar elemen/gap (`px`).
   - **Batas & Sudut**: Radius sudut / melengkung (`px`), Lebar border (`px`).
   - **Warna**: Warna teks, Warna latar, Warna aksen.
5. Terintegrasi penuh dengan **Staging Sandbox Engine** (`LandingCmsStagingService`), tabel `pengaturan`, dan proteksi otorisasi SuperAdmin.

---

## 1. LATAR BELAKANG & FILOSOFI DESAIN WORDPRESS.ORG

### 1.1 Mengapa Harus Pola WordPress.org?
WordPress.org adalah standar industri (*gold standard*) pengelolaan konten web yang digunakan oleh jutaan situs di seluruh dunia. Antarmuka Gutenberg Block Editor dan Full Site Editor (FSE) di WordPress.org memiliki karakteristik khas:
1. **Kanvas WYSIWYG Nyata di Tengah**: Pengguna tidak mengetik di formulir samping yang terpisah. Tampilan di kanvas adalah tampilan persis yang akan dilihat oleh pengunjung umum.
2. **Editing Inline di Tempat**: Jika ingin mengubah judul hero, pengguna cukup mengklik teks judul di kanvas dan langsung mengetik seperti di Word / Google Docs (*contenteditable*).
3. **Inspector di Sisi Kanan (Bukan Kiri)**: Sesuai tata letak baku `wp-admin/site-editor.php`, panel pengaturan blok berada di **kolom kanan**.
4. **Kontrol Dimensi Pixel Baku**: Pengguna dapat memasukkan angka ukuran pixel secara presisi atau menggeser slider untuk melihat perubahan langsung detik itu juga (*real-time visual feedback*).

---

## 2. SPESIFIKASI ANTARMUKA PENGGUNA (UI/UX WIREFRAME WORDPRESS.ORG)

### 2.1 Tata Letak Lengkap (Top Bar, Center Canvas, Right Inspector)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [WP-STYLE TOP BAR]                                                                                                     │
│ [W/Logo]  [ + Tambah ]  [ ↶ Undo ] [ ↷ Redo ]  [ ☰ List View ]  │  Landing Page  │  [📱 Pratinjau ▾]  [Simpan Draf]  [Terbitkan]  [⚙️]│
├────────────────────────────────────────────────────────────────────────────────────────────┬───────────────────────────┤
│ [CENTER CANVAS - AREA EDITING GUTENBERG WYSIWYG]                                           │ [RIGHT BLOCK INSPECTOR]   │
│ Area Kanvas Interaktif (Background: Abu-abu Terang Editor #F0F0F1)                         │ Lebar: 320px (Fixed)      │
│                                                                                            │                           │
│   ┌────────────────────────────────────────────────────────────────────────────────────┐   │ [ Halaman ]  [ *Blok* ]   │
│   │ [DOKUMEN KANVAS LANDING PAGE] (Lebar Kontainer: 100% / Tablet 768px / Mobile 375px)│   │ ───────────────────────── │
│   │                                                                                    │   │ 🔷 BLOK: HERO BANNER      │
│   │   [Header Navigasi]                                                                │   │ Bagian pengenalan utama   │
│   │                                                                                    │   │                           │
│   │   ┌── [FLOATING TOOLBAR MELAYANG DI ATAS BLOK AKTIF] ──────────────────────────┐   │   │ ▼ Tipografi               │
│   │   │ [Hero] │ [ ≡ Rata Tengah ] │ [ ↑ Naik ] [ ↓ Turun ] │ [ 🗑️ Hapus ]         │   │   Ukuran Font Judul:      │
│   │   └──┬─────────────────────────────────────────────────────────────────────────┘   │   [=======●=======] [ 48 px ] │
│   │   ┌──▼─────────────────────────────────────────────────────────────────────────┐   │   Tinggi Baris:               │
│   │   │ Kabupaten Sleman (Kicker)                                                  │   │   [====●==========] [ 1.2   ] │
│   │   │                                                                            │   │                           │
│   │   │ MAGANG KOMINFO SLEMAN 2026 (Klik & Ketik Langsung di Sini!)                 │   │ ▼ Dimensi & Spasi         │
│   │   │                                                                            │   │   Padding Atas-Bawah:     │
│   │   │ Pendaftaran dibuka 1-15 Oktober 2026...                                    │   │   [========●======] [ 80 px ] │
│   │   │                                                                            │   │   Lebar Kontainer Maks:   │
│   │   │ [ Tombol: Daftar Sekarang ]                                                │   │   [==========●====] [1280px ] │
│   │   └────────────────────────────────────────────────────────────────────────────┘   │                           │
│   │                                                                                    │   │ ▼ Batas & Sudut           │
│   │   ┌────────────────────────────────────────────────────────────────────────────┐   │   Radius Sudut (Radius):  │
│   │   │ BLOK: KETERSEDIAAN KUOTA & FORMASI                                         │   │   [====●==========] [ 12 px ] │
│   │   │ (Klik untuk memilih blok ini dan membuka pengaturan di inspector kanan)    │   │                           │
│   │   └────────────────────────────────────────────────────────────────────────────┘   │ ▼ Warna                   │
│   │                                                                                    │   Warna Teks:  [ #1B3860 ■ ]  │
│   │   ┌────────────────────────────────────────────────────────────────────────────┐   │   Latar:       [ #FFFFFF □ ]  │
│   │   │ BLOK: ALUR & TAHAPAN MAGANG (4 Checkpoints)                                │   │                               │
│   │   └────────────────────────────────────────────────────────────────────────────┘   │ ▼ Media Banner            │
│   │   ┌────────────────────────────────────────────────────────────────────────────┐   │   [ Ganti Foto Banner ]   │
│   │   │ BLOK: FAQ ACCORDION                                                        │   │   Tinggi Banner: [ 420 px ]   │
│   │   └────────────────────────────────────────────────────────────────────────────┘   │                           │
│   │   ┌────────────────────────────────────────────────────────────────────────────┐   │                           │
│   │   │ BLOK: FOOTER & KONTAK DINAS                                                │   │                           │
│   │   └────────────────────────────────────────────────────────────────────────────┘   │                           │
│   └────────────────────────────────────────────────────────────────────────────────────┘   │                           │
└────────────────────────────────────────────────────────────────────────────────────────────┴───────────────────────────┘
```

---

## 3. SPESIFIKASI FITUR DETAIL ALA WORDPRESS.ORG

### 3.1 Top Bar Editor (Khas WP-Admin)
1. **Logo Instansi / Sleman**: Tombol kembali ke daftar manajemen Landing Page di dashboard SuperAdmin.
2. **Tombol Block Inserter `[ + ]`**: Membuka drawer panel di sisi kiri berisi daftar blok landing page (Hero, Kuota, Tahapan, FAQ, Footer, Kebijakan). Klik salah satu blok akan otomatis menggulirkan kanvas (*smooth scroll*) ke posisi blok tersebut.
3. **Tombol History (Undo `↶` & Redo `↷`)**: Membatalkan perubahan ketikan teks atau slider dimensi pixel di memori lokal browser.
4. **Tombol List View `[ ☰ ]`**: Menampilkan pohon hierarki blok landing page untuk navigasi cepat.
5. **Dropdown Pratinjau Perangkat (Device Switcher)**:
   - **Desktop**: Kanvas selebar `100%` (maksimal container 1280px).
   - **Tablet**: Kanvas dibatasi menjadi selebar `768px` dengan bingkai tablet.
   - **Mobile**: Kanvas dibatasi menjadi selebar `375px` dengan bingkai smartphone.
6. **Tombol Simpan Draf (*Save Draft*)**: Menyimpan draf konfigurasi ke database tanpa mempublikasikannya ke pengunjung publik.
7. **Tombol Terbitkan (*Publish*)**: Tombol berwarna biru primer khas WordPress (`#0073AA` atau brand navy `#1B3860`) untuk mempublikasikan seluruh perubahan draf ke versi live pengunjung publik.
8. **Tombol Toggle Settings `[ ⚙️ ]`**: Menyembunyikan atau memunculkan panel Inspector di sisi kanan.

### 3.2 Kanvas Interaktif Tengah (WYSIWYG Gutenberg Style)
1. **Blok Sebagai Satuan Desain**: Setiap bagian landing page adalah elemen HTML dengan atribut `data-wp-block="hero"`, `data-wp-block="kuota"`, `data-wp-block="tahapan"`, `data-wp-block="faq"`, `data-wp-block="footer"`.
2. **Seleksi Visual (Active Bounding Box)**:
   - Saat mouse melayang (*hover*), muncul garis outline putus-putus berwarna biru WordPress (`#007cba`).
   - Saat diklik (*click*), blok menjadi aktif dengan outline solid biru tebal 2px dan label tag blok di pojok kiri atas (misal: `[ Blok Hero ]`).
3. **Inline Content Editing (*contenteditable*)**:
   - Teks Kicker, Judul Utama, Deskripsi, dan Pertanyaan FAQ dapat diklik dan diedit langsung di kanvas.
   - Perubahan teks langsung disimpan ke dalam State Manager di memori browser.
4. **Floating Block Toolbar**:
   - Toolbar melayang otomatis muncul tepat di atas blok yang sedang aktif.
   - Memuat aksi: Nama blok, opsi perataan teks (Kiri, Tengah, Kanan), tombol pindah posisi (Naik / Turun), dan tombol reset ke default.

### 3.3 Right Inspector Panel (Sisi Kanan)
Panel kanan memiliki dua tab:
- **Tab [Halaman]**: Pengaturan umum landing page (Meta Title, Meta Description, Brand Name di Navbar, SEO Tags).
- **Tab [Blok]**: Pengaturan spesifik untuk blok yang sedang dipilih di kanvas.

Di dalam tab **[Blok]**, terdapat panel kontrol terstruktur dengan satuan **Pixel (`px`)**:

#### A. Panel Tipografi (Typography)
- **Ukuran Font Judul (`px`)**: Slider + input angka (contoh: range `24px` – `72px`).
- **Ukuran Font Deskripsi (`px`)**: Slider + input angka (range `12px` – `24px`).
- **Tinggi Baris (*Line Height*)**: Slider angka rasio (contoh: `1.0` – `2.0`).

#### B. Panel Dimensi & Jarak Spasi (Dimensions / Spacing)
- **Padding Atas-Bawah (`px`)**: Slider jarak bantalan vertikal section (range `20px` – `200px`).
- **Lebar Maksimum Kontainer (`px`)**: Pilihan preset (`1024px`, `1200px`, `1280px`, `1440px`) atau custom pixel.
- **Jarak Antar Kartu / Gap (`px`)**: Slider jarak antar kartu kuota/tahapan (range `12px` – `48px`).

#### C. Panel Sudut Melengkung & Batas (Border & Radius)
- **Radius Sudut Kotak (`px`)**: Slider kelengkungan kartu (range `0px` – `32px` atau tombol bentuk pil `9999px`).
- **Ketebalan Border (`px`)**: Slider garis tepi kartu (range `0px` – `4px`).

#### D. Panel Warna (Color Palette)
- **Warna Teks**: Color picker interaktif.
- **Warna Latar Belakang (Background)**: Color picker interaktif.
- **Warna Aksen Tombol**: Color picker interaktif.

---

## 4. KAMUS DATA & DAFTAR TOKEN PENGATURAN (DATABASE MAPPING)

Seluruh nilai dikelola melalui model `Pengaturan` dan disimpan dengan prefix `draft_landing_*` saat tahap draf, dan `landing_*` saat dipublikasikan ke publik:

| Kunci Pengaturan (Key) | Label Pengaturan | Tipe | Nilai Bawaan | Validasi Min - Max | Target Variabel CSS di Kanvas |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `landing_container_max_width_px` | Lebar Maksimum Kontainer | Int | `1280` | `960` s/d `1600` px | `--wp-container-max-w` |
| `landing_hero_title_size_px` | Ukuran Font Judul Hero | Int | `48` | `24` s/d `72` px | `--wp-hero-title-font-size` |
| `landing_hero_desc_size_px` | Ukuran Font Deskripsi Hero | Int | `16` | `12` s/d `24` px | `--wp-hero-desc-font-size` |
| `landing_hero_padding_y_px` | Padding Vertikal Hero | Int | `80` | `20` s/d `200` px | `--wp-hero-padding-y` |
| `landing_hero_image_height_px` | Tinggi Maksimum Banner | Int | `420` | `250` s/d `700` px | `--wp-hero-image-height` |
| `landing_section_gap_px` | Jarak Antar Section | Int | `64` | `24` s/d `140` px | `--wp-section-gap-y` |
| `landing_card_radius_px` | Sudut Lengkung Card | Int | `12` | `0` s/d `32` px | `--wp-card-border-radius` |
| `landing_card_padding_px` | Padding Dalam Card | Int | `24` | `12` s/d `48` px | `--wp-card-padding` |
| `landing_faq_title_size_px` | Ukuran Font Pertanyaan FAQ | Int | `18` | `14` s/d `24` px | `--wp-faq-title-font-size` |
| `landing_footer_padding_y_px` | Padding Vertikal Footer | Int | `64` | `32` s/d `140` px | `--wp-footer-padding-y` |

---

## 5. STRUKTUR FILE & ARSITEKTUR IMPLEMENTASI

Semua implementasi wajib mematuhi standar repositori MAGKOM:
- Backend: PHP 8.3 & Laravel 10 native.
- Frontend: Vanilla JavaScript (ES Modules) di `resources/js/` dan CSS di `resources/css/` dikelola oleh **Vite**.
- **Dilarang keras menulis script JS atau CSS `<style>` di dalam file Blade** (Blade murni markup/skeleton).

### Daftar File:

| File | Tindakan | Deskripsi Peran |
| :--- | :--- | :--- |
| `app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php` | **CREATE** | Controller utama WordPress-style Site Editor: load initial state, handle AJAX save draft, publish, dan discard. |
| `app/Http/Requests/SuperAdmin/SaveLandingSiteEditorRequest.php` | **CREATE** | Validasi request payload teks, JSON blok, dan integer dimensi pixel. |
| `app/Services/LandingCmsStagingService.php` | **MODIFY** | Tambahkan method `saveSiteEditorDraft(array $payload, ?User $user)` untuk batch saving staging sandbox. |
| `routes/web.php` | **MODIFY** | Daftarkan rute `/superadmin/site-editor/*`. |
| `resources/views/superadmin/site-editor/index.blade.php` | **CREATE** | Layout full-screen WordPress Site Editor (Top Bar, Center Canvas Frame, Right Inspector Sidebar, Drawer Block Inserter). |
| `resources/views/superadmin/site-editor/partials/_topbar.blade.php` | **CREATE** | Komponen Top Bar khas WP-Admin (Inserter, Undo/Redo, Viewport, Simpan, Terbitkan, Toggle Settings). |
| `resources/views/superadmin/site-editor/partials/_inspector.blade.php` | **CREATE** | Komponen Right Inspector Panel (Tab Halaman & Tab Blok dengan kontrol slider pixel & color picker). |
| `resources/views/public/landing.blade.php` | **MODIFY** | Binding token CSS variables `--wp-*` pada `:root` dan memuat runtime editor jika query `?editor_mode=1` aktif. |
| `resources/css/pages/superadmin/site-editor.css` | **CREATE** | Stylesheet tampilan WordPress Site Editor (skema warna WP `#1E1E1E`, `#F0F0F1`, `#007CBA`, floating toolbar, dan frame responsive). |
| `resources/js/pages/superadmin/site-editor/editor-app.js` | **CREATE** | Core Orchestrator Editor: state management, undo/redo stack, keyboard shortcuts (Ctrl+Z, Ctrl+S), dan dispatch API. |
| `resources/js/pages/superadmin/site-editor/inspector.js` | **CREATE** | Controller panel sisi kanan: sinkronisasi slider pixel ke kanvas, tab switcher, dan color pickers. |
| `resources/js/pages/superadmin/site-editor/canvas-bridge.js` | **CREATE** | Jembatan komunikasi dua arah antara Kanvas WYSIWYG dan Editor (click-to-select, inline text sync, instant CSS property update). |
| `vite.config.js` | **MODIFY** | Daftarkan entry point `site-editor.css` dan `editor-app.js`. |
| `tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php` | **CREATE** | Test suite Feature Testing: otorisasi SuperAdmin, validasi pixel bounds, alur simpan draf, dan publikasi. |

---

## 6. LANGKAH-LANGKAH IMPLEMENTASI BERTAHAP (SENIOR DEV TASK BREAKDOWN)

Junior Developer atau AI Coding Agent (Gemini 3.8 Flash) wajib mengikuti urutan langkah atomic berikut:

### Task 1: Backend Service & Validasi Skema Pixel
1. Buka `app/Services/LandingCmsStagingService.php`.
2. Tambahkan method `saveSiteEditorDraft(array $data, ?User $user = null): void`.
   - Melakukan sanitasi field teks menggunakan `strip_tags` (kecuali tag format dasar).
   - Memastikan setiap nilai parameter pixel adalah integer dalam batas min-max.
   - Menyimpan seluruh kunci dengan prefix `draft_landing_*` ke tabel `pengaturan` menggunakan transaksi database.
3. Buat file Form Request `app/Http/Requests/SuperAdmin/SaveLandingSiteEditorRequest.php` dengan rules validasi:
   - Pixel dimensions: `['required', 'integer', 'between:min,max']`.
   - Colors: `['required', 'regex:/^#([A-Fa-f0-9]{6})$/']`.
   - Text fields: `['nullable', 'string', 'max:5000']`.

### Task 2: Controller & Endpoints API Editor
1. Buat controller `app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php`.
2. Implementasikan aksi:
   - `index()`: Mengaktifkan mode preview draf (`Pengaturan::setPreviewMode(true)`), membaca seluruh konfigurasi, dan merender view `superadmin.site-editor.index`.
   - `saveDraft(SaveLandingSiteEditorRequest $request)`: Menerima JSON via AJAX, memanggil `saveSiteEditorDraft()`, dan mengembalikan response JSON `{ success: true, updated_at: '...' }`.
   - `publish(Request $request)`: Menerbitkan draf ke publik menggunakan `stagingService->publishDraft($request->user())`.
   - `discard(Request $request)`: Membuang draf menggunakan `stagingService->discardDraft($request->user())`.

### Task 3: Pendaftaran Rute Web & Tombol Akses
1. Daftarkan grup rute baru di `routes/web.php` pada middleware `role:superadmin`:
   ```php
   Route::prefix('site-editor')->name('site-editor.')->group(function () {
       Route::get('/', [LandingCmsSiteEditorController::class, 'index'])->name('index');
       Route::post('/save-draft', [LandingCmsSiteEditorController::class, 'saveDraft'])->name('save-draft');
       Route::post('/publish', [LandingCmsSiteEditorController::class, 'publish'])->name('publish');
       Route::delete('/discard', [LandingCmsSiteEditorController::class, 'discard'])->name('discard');
   });
   ```
2. Pada view `superadmin/landing/index.blade.php`, tambahkan kartu/tombol navigasi utama:
   `"Buka WordPress Site Editor 🎨"` yang mengarah ke `route('superadmin.site-editor.index')`.

### Task 4: Template Workspace WordPress.org (Blade Views)
1. Buat view `resources/views/superadmin/site-editor/index.blade.php` dengan tata letak full-screen:
   - Tidak memuat sidebar default dashboard Gentelella, agar ruang kerja 100% fokus ke kanvas dan inspector.
   - Kontainer 3 bagian:
     - **Header**: `@include('superadmin.site-editor.partials._topbar')`
     - **Tengah**: Area kanvas iframe yang memuat `route('superadmin.landing.preview', ['editor_mode' => 1])`.
     - **Kanan**: `@include('superadmin.site-editor.partials._inspector')`
2. Di dalam partial `_topbar.blade.php`:
   - Tombol Inserter Blok `[ + ]`.
   - History button (Undo/Redo).
   - Viewport buttons (`data-device="desktop"`, `data-device="tablet"`, `data-device="mobile"`).
   - Tombol "Simpan Draf" dan tombol "Terbitkan" (warna biru khas WordPress).
   - Tombol toggle gear `[ ⚙️ ]` untuk menampilkan/menyembunyikan panel inspector kanan.
3. Di dalam partial `_inspector.blade.php`:
   - Tab header: `[ Halaman ]` dan `[ Blok ]`.
   - Kontrol pixel slider dengan label dan badge nilai:
     ```html
     <div class="wp-control-group">
         <div class="wp-control-label">
             <span>Ukuran Font Judul</span>
             <span class="wp-pixel-value" data-value-display="hero-title-size">{{ $cmsData['hero_title_size_px'] ?? 48 }}px</span>
         </div>
         <input type="range" class="wp-slider-input" min="24" max="72" 
                value="{{ $cmsData['hero_title_size_px'] ?? 48 }}" 
                data-property="--wp-hero-title-font-size" data-field="landing_hero_title_size_px">
     </div>
     ```

### Task 5: Penyelarasan Variabel CSS pada Landing Page
1. Buka `resources/views/public/landing.blade.php`.
2. Tambahkan CSS custom properties bindings ke dalam tag `<style id="wp-site-editor-vars">` pada bagian `<head>`:
   ```html
   <style id="wp-site-editor-vars">
       :root {
           --wp-container-max-w: {{ \App\Support\Pengaturan::get('landing_container_max_width_px', '1280') }}px;
           --wp-hero-title-font-size: {{ \App\Support\Pengaturan::get('landing_hero_title_size_px', '48') }}px;
           --wp-hero-desc-font-size: {{ \App\Support\Pengaturan::get('landing_hero_desc_size_px', '16') }}px;
           --wp-hero-padding-y: {{ \App\Support\Pengaturan::get('landing_hero_padding_y_px', '80') }}px;
           --wp-hero-image-height: {{ \App\Support\Pengaturan::get('landing_hero_image_height_px', '420') }}px;
           --wp-section-gap-y: {{ \App\Support\Pengaturan::get('landing_section_gap_px', '64') }}px;
           --wp-card-border-radius: {{ \App\Support\Pengaturan::get('landing_card_radius_px', '12') }}px;
           --wp-card-padding: {{ \App\Support\Pengaturan::get('landing_card_padding_px', '24') }}px;
           --wp-faq-title-font-size: {{ \App\Support\Pengaturan::get('landing_faq_title_size_px', '18') }}px;
           --wp-footer-padding-y: {{ \App\Support\Pengaturan::get('landing_footer_padding_y_px', '64') }}px;
       }
   </style>
   ```

### Task 6: Stylesheet Antarmuka Editor (`site-editor.css`)
1. Buat file `resources/css/pages/superadmin/site-editor.css`.
2. Terapkan design token WordPress.org:
   - Warna bar atas: Background putih bersih dengan border bawah `#E0E0E0`.
   - Warna kanvas luar: `#F0F0F1` (abu-abu netral WordPress).
   - Bounding box seleksi blok: Outline solid 2px `#007CBA`.
   - Floating block toolbar: Popover mengambang putih dengan shadow `0 2px 8px rgba(0,0,0,0.12)`.
   - Responsif viewport mock: Bingkai tablet (lebar 768px, box shadow lembut) dan mobile (lebar 375px).

### Task 7: JavaScript ES Modules WordPress Editor Engine
1. Buat folder `resources/js/pages/superadmin/site-editor/` dengan 3 modul terpisah:
   - `canvas-bridge.js`: Menangani pesan `window.postMessage` antara parent editor dan kanvas iframe.
     - Saat blok di kanvas diklik, kirim sinyal ke parent untuk membuka tab `[Blok]` di inspector kanan.
     - Saat teks di kanvas diubah (*contenteditable*), kirim data teks ke state manager.
     - Saat slider digeser di inspector, terapkan `document.documentElement.style.setProperty()` secara instan di kanvas.
   - `inspector.js`: Menangani interaksi panel sisi kanan:
     - Event slider range `input`: memperbarui teks badge pixel dan mengirim nilai ke kanvas.
     - Color picker sync.
     - Switcher antara tab Halaman dan tab Blok.
   - `editor-app.js`: Main Entrypoint:
     - Viewport switcher (Desktop / Tablet / Mobile).
     - Keyboard shortcut: `Ctrl + S` untuk Simpan Draf, `Ctrl + Z` untuk Undo.
     - Auto-save draf debounced (2 detik setelah selesai mengetik).
     - Tombol Simpan Draf & Publikasikan (AJAX fetch ke backend dengan CSRF token).
2. Daftarkan script dan style di `vite.config.js`.

### Task 8: Pengujian & Quality Gate
1. Buat unit & feature test di `tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php`:
   - Uji otorisasi role: Hanya SuperAdmin yang berhak mengakses (status 200), pengguna lain ditolak (status 403).
   - Uji validasi input pixel: Nilai di luar rentang min-max ditolak dengan status HTTP 422.
   - Uji penyimpanan draf: Request AJAX berhasil menulis ke tabel `pengaturan` dengan prefix `draft_landing_*`.
   - Uji publikasi: Draf berhasil disalin ke kunci publik live `landing_*`.
2. Jalankan audit format kode: `./vendor/bin/pint --test`.
3. Jalankan pengujian: `php artisan test --filter=LandingCmsSiteEditorTest`.

---

## 7. STRATEGI TESTING & QA ACCEPTANCE CRITERIA

| Test Case | Deskripsi Pengujian | Hasil yang Diharapkan |
| :--- | :--- | :--- |
| `TC-WP-01` | Akses route `/superadmin/site-editor` sebagai SuperAdmin | Status HTTP 200, antarmuka WordPress Site Editor termuat lengkap. |
| `TC-WP-02` | Akses route sebagai Admin / Peserta / Guest | Status HTTP 403 / Redirect Login. |
| `TC-WP-03` | Klik blok Hero di kanvas | Blok terpilih dengan outline biru, panel inspector kanan membuka tab `[Blok]` Hero. |
| `TC-WP-04` | Geser slider ukuran font judul dari `48px` ke `60px` | Ukuran font di kanvas seketika membesar menjadi `60px` tanpa reload halaman. |
| `TC-WP-05` | Klik tombol "Mobile" di Top Bar | Kanvas menciut menjadi bingkai smartphone 375px secara mulus. |
| `TC-WP-06` | Simpan Draf via tombol / shortcut `Ctrl+S` | Muncul notifikasi "Draf Tersimpan", data tersimpan di staging database. |
| `TC-WP-07` | Klik tombol "Terbitkan" warna biru | Seluruh draf dipublikasikan ke live publik dan pengunjung umum dapat melihatnya. |

---

## 8. DEFINITION OF DONE (DOD)

- [ ] Antarmuka 100% mengadopsi struktur WordPress.org (Top Bar, Kanvas Tengah WYSIWYG, Inspector Sisi Kanan).
- [ ] Teks dapat diedit langsung di kanvas (*inline editing*).
- [ ] Pengaturan ukuran font, padding, margin, lebar kontainer, dan radius sudut memiliki kontrol angka **Pixel (`px`)** eksplisit.
- [ ] Zero-latency live update saat slider digeser tanpa memuat ulang kanvas.
- [ ] Viewport Switcher (Desktop, Tablet, Mobile) berfungsi mulus.
- [ ] Terintegrasi dengan sistem Draf Staging dan Publikasi Live MAGKOM.
- [ ] Kode mematuhi standar repositori (tidak ada inline JS/CSS di Blade, Vanilla ES Modules di Vite, PHP 8.3).
- [ ] 100% lulus audit `./vendor/bin/pint --test` dan pengujian otomatis PHPUnit.
