# Rencana Kerja: Perbaikan Logika Canvas Site Editor, Publikasi Foto Banner, dan Konsistensi Konten (General, Style, Advanced)

**Status:** Menunggu Persetujuan Pengguna (Drafting Plan)  
**Dokumen Referensi:** `AGENTS.md`, `docs/01-PRD-MagKom.md`, `docs/02-TDD-MagKom.md`  
**Target Rilis:** Canvas Site Editor v1.2 (Bugfix & Data Pipeline Synchronization)  

---

## 1. Analisis Akar Masalah (Root Cause Analysis / RCA)

Berdasarkan penelusuran mendalam pada kode backend Laravel 10 dan frontend Vanilla ES Modules, berikut adalah letak kesalahan, logika, dan file penyebab masalah:

### 1.1 Masalah Foto Banner (Ganti Gambar) Tidak Berubah / Rusak Setelah Diterbitkan
* **File 1:** [`app/Http/Requests/SuperAdmin/SaveLandingSiteEditorRequest.php`](file:///d:/laragon/www/MAGKOM/app/Http/Requests/SuperAdmin/SaveLandingSiteEditorRequest.php#L25-L117)
  * **Logika:** Kunci `'landing_hero_image'` **tidak terdaftar** dalam metode `rules()`. Ketika frontend mengirimkan `{ "landing_hero_image": "landing/media/xxx.jpg" }`, method `$request->validated()` di controller secara otomatis membuang field tersebut. Akibatnya, `draft_landing_hero_image` tidak pernah tersimpan di database saat draf disimpan.
* **File 2:** [`app/Services/LandingCmsStagingService.php`](file:///d:/laragon/www/MAGKOM/app/Services/LandingCmsStagingService.php#L168-L180) (pada metode `publishDraft()`)
  * **Logika:** Terdapat sisa logika legacy lama yang mencoba memindahkan file fisik:
    ```php
    $newLivePath = 'landing/'.basename($draftImagePath);
    Storage::disk('public')->move($draftImagePath, $newLivePath);
    ```
    Pada arsitektur baru, gambar dari **Media Library** disimpan permanen di folder `landing/media/`. Saat perintah `move` ini dieksekusi, file di lokasi asalnya terhapus/berpindah. Sementara itu, dokumen JSON `landing_page_document` blok Hero masih menyimpan path `landing/media/xxx.jpg`. Saat halaman publik dibuka, `Storage::disk('public')->exists('landing/media/xxx.jpg')` bernilai `false`, sehingga sistem secara otomatis fallback menampilkan foto default bawaan (`hero_dokumentasi_diskominfo.png`).
* **File 3:** [`resources/views/public/blocks/hero.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/public/blocks/hero.blade.php#L63-L138)
  * **Logika:** Hero Banner desktop memiliki 2 slide: Slide 1 adalah kartu 3 kolom pendaftaran (`.hero-box`), dan Slide 2 adalah foto banner dokumentasi (`.hero-full-img`). Saat pratinjau kanvas dimuat, slider default berada di Slide 1, sehingga user tidak langsung melihat perubahan foto jika belum menggeser slider ke Slide 2 atau jika preview image bridge tidak menyentuh Slide 2.

### 1.2 Masalah Mengganti Sub Judul / Judul / Info Periode / Tombol di General Tidak Berubah Setelah Diterbitkan
* **File 1:** [`resources/js/pages/superadmin/site-editor/inspector.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/superadmin/site-editor/inspector.js#L775-L810) (metode `initContentInputs()`)
  * **Logika (Desinkronisasi Data Dua Arah):** Input form pada panel Inspector menggunakan atribut `data-field="landing_hero_title"`, `data-field="landing_hero_kicker"`, dll. Event listener `input` hanya memperbarui objek flat `this.state[field]`. **Sama sekali tidak memperbarui `block.content`** pada objek blok aktif (`this.getActiveBlockData()` / `blockManager`).
  * Ketika `EditorState.saveDraft()` dipanggil, data blok di dalam `landing_page_document.blocks` masih membawa nilai teks lama (stale data).
* **File 2:** [`app/Services/LandingCmsStagingService.php`](file:///d:/laragon/www/MAGKOM/app/Services/LandingCmsStagingService.php#L650-L663) & [`app/Services/LandingPage/LegacyCmsAdapter.php`](file:///d:/laragon/www/MAGKOM/app/Services/LandingPage/LegacyCmsAdapter.php#L287-L337)
  * **Logika:** Pada `saveSiteEditorDraft()`, backend melakukan sinkronisasi balik: `LegacyCmsAdapter::syncDocumentToLegacySettings($value, draft: true)`. Karena `landing_page_document` berisi blok dengan teks lama, adapter justru **menimpa kembali** nilai `draft_landing_hero_*` dengan teks lama tersebut!
  * Saat tombol "Terbitkan" ditekan, `LandingPageRenderer` me-render `landing_page_document` yang masih berisi konten lama.
* **File 3:** [`resources/views/public/blocks/hero.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/public/blocks/hero.blade.php#L68-L75) (baris 68-75 dan baris 149-156)
  * **Logika (Hardcoded Supression Bug):** Terdapat kode kondisional berikut:
    ```php
    @if($cmsTitle && trim(str_replace(["\r", "\n"], ' ', $cmsTitle)) !== 'Program Magang Kominfo Sleman')
        <h1 class="hero-title">{!! nl2br(e($cmsTitle)) !!}</h1>
    @else
        <h1 class="hero-title">Magang <span class="text-accent">Kominfo</span><br>Periode II</h1>
    @endif
    ```
    Jika user mengisikan `"Program Magang Kominfo Sleman"` (nilai default yang ada di database & form inspector), sistem secara paksa menolak menampilkannya dan tetap menampilkan `"Magang Kominfo Periode II"`. Inilah penyebab pasti mengapa judul utama tidak berubah di kanvas pratinjau maupun publik.

### 1.3 Masalah Tab Style & Advanced Tidak Tersimpan Saat Tombol "Terbitkan" Ditekan
* **File 1:** [`resources/js/pages/superadmin/site-editor/editor-app.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/superadmin/site-editor/editor-app.js#L308-L327)
  * **Logika (Race Condition / Unflushed Publish):** Tombol `#btn-publish` langsung mengeksekusi `fetch('/superadmin/site-editor/publish')` **tanpa** terlebih dahulu menunggu `await editorState.saveDraft()`. Jika user baru saja mengubah nilai di General, Style, atau Advanced dan langsung mengklik "-> Terbitkan", perubahan di memori browser belum sempat tersimpan ke database staging, sehingga server hanya mempublikasikan versi draf lama sebelum perubahan dilakukan.
* **File 2:** [`resources/js/pages/superadmin/site-editor/canvas-bridge.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/superadmin/site-editor/canvas-bridge.js) & [`resources/js/pages/public/landing-canvas-runtime.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/public/landing-canvas-runtime.js#L263-L269)
  * **Logika:** `landing-canvas-runtime.js` sudah siap menerima event `WP_SET_TEXT`, namun `canvas-bridge.js` dan `inspector.js` tidak mengirimkan pesan postMessage saat input teks diubah, sehingga kanvas iframe tidak melakukan live preview teks secara instan.
* **File 3:** [`resources/views/public/blocks/*.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/public/blocks/)
  * **Logika:** Variabel `$settings['custom_css_class']` dan `$settings['html_anchor']` yang disanitasi di `BlockSchemaValidator` belum diikatkan pada tag pembuka `<section>` masing-masing blok.

---

## 2. Rencana Solusi & Arsitektur Perbaikan

### Fase 1: Perbaikan Backend & Validasi Penyimpanan (Save Draft & Publish)
1. **Daftarkan Kunci Gambar di Form Request:**
   * Tambahkan aturan validasi untuk `'landing_hero_image'` di [`app/Http/Requests/SuperAdmin/SaveLandingSiteEditorRequest.php`](file:///d:/laragon/www/MAGKOM/app/Http/Requests/SuperAdmin/SaveLandingSiteEditorRequest.php):
     ```php
     'landing_hero_image' => ['sometimes', 'nullable', 'string', 'max:255'],
     ```
2. **Perbaiki Logika Pemindahan Berkas di `LandingCmsStagingService::publishDraft()`:**
   * Di [`app/Services/LandingCmsStagingService.php`](file:///d:/laragon/www/MAGKOM/app/Services/LandingCmsStagingService.php), jangan memindahkan (`Storage::move`) file jika file tersebut sudah berada di dalam direktori `landing/media/` atau `landing/`. File dari Media Library adalah asset permanen yang tidak boleh dipindahkan/di-rename agar integritas URL dokumen tetap valid.
   * Pastikan `Pengaturan::set('landing_hero_image', ...)` menyimpan path yang benar dan sinkron dengan `landing_page_document`.
3. **Penyelarasan Sinkronisasi Dua Arah (`LegacyCmsAdapter`):**
   * Pastikan saat `saveSiteEditorDraft()` menerima `data` dari frontend, nilai `content` blok diupdate dari flat keys jika flat keys tersedia, atau prioritaskan flat keys agar tidak ditimpa oleh stale block content.

### Fase 2: Perbaikan Frontend Inspector & Editor State (Single Source of Truth)
1. **Sinkronisasi Input `data-field` ke `block.content` (`inspector.js`):**
   * Saat user mengetik pada input dengan `data-field` (misal `landing_hero_title`, `landing_hero_kicker`, `landing_hero_period_info`, `landing_hero_cta_text`, dll.), selain mengisi `this.state[field]`, fungsi ini harus memetakan dan memperbarui properti `block.content[mapping]` pada blok yang bersangkutan di `this.blockManager`.
   * Contoh pemetaan:
     * `landing_hero_title` ➔ `block.content.title`
     * `landing_hero_kicker` ➔ `block.content.kicker`
     * `landing_hero_period_info` ➔ `block.content.period_info`
     * `landing_hero_cta_text` ➔ `block.content.cta_primary_text`
     * `landing_hero_cta_primary_url` ➔ `block.content.cta_primary_url`
     * `landing_hero_cta_secondary_text` ➔ `block.content.cta_secondary_text`
     * `landing_hero_cta_secondary_url` ➔ `block.content.cta_secondary_url`
     * `landing_hero_overlay_active` ➔ `block.content.overlay_active`
     * `landing_hero_year` ➔ `block.content.year`
2. **Kirim Pesan Live Update ke Iframe Kanvas (`canvas-bridge.js`):**
   * Tambahkan metode `updateText(fieldName, text)` di `CanvasBridge`.
   * Panggil `this.bridge.updateText(...)` saat user mengetik di inspector, sehingga teks di kanvas iframe langsung berubah seketika tanpa perlu reload.
3. **Pastikan "Terbitkan" Selalu Menyimpan Draf Terbaru Terlebih Dahulu (`editor-app.js`):**
   * Ubah event handler `#btn-publish` agar melakukan `await editorState.saveDraft(false, true)` terlebih dahulu sebelum memanggil endpoint `/superadmin/site-editor/publish`.
   * Jika simpan draf gagal, batalkan publikasi dan tampilkan notifikasi kesalahan yang jelas kepada pengguna.

### Fase 3: Penyelarasan Blade Markup (Sesuai Aturan AGENTS.md Bagian 3.3)
> [!IMPORTANT]
> **Permohonan Persetujuan Perubahan Markup Blade:**
> Sesuai aturan inti proyek `AGENTS.md` (bagian 3.3 Pengecualian Perubahan Blade), berikut adalah perubahan file Blade yang mutlak diperlukan untuk menyelesaikan bug ini:
> 1. [`resources/views/public/blocks/hero.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/public/blocks/hero.blade.php):
>    * Hapus hardcoded suppression check `trim(str_replace(["\r", "\n"], ' ', $cmsTitle)) !== 'Program Magang Kominfo Sleman'`. Ganti dengan pemeriksaan normal `if (!empty($cmsTitle))` sehingga judul apa pun yang diketikkan pengguna (termasuk "Program Magang Kominfo Sleman") benar-benar dirender ke layar.
>    * Pasang atribut `$settings['custom_css_class'] ?? ''` dan id `$settings['html_anchor'] ?? 'beranda'` pada tag `<section id="beranda">`.
> 2. Blok lainnya di `resources/views/public/blocks/`:
>    * Tambahkan dukungan class kustom `$settings['custom_css_class'] ?? ''` dan anchor `$settings['html_anchor']` agar konfigurasi tab Advanced berfungsi penuh secara konsisten di seluruh blok.

---

## 3. Matriks Pengujian & Verifikasi Kualitas (Quality Gates)

| Komponen / Skenario | Alat Uji / Command | Kriteria Keberhasilan |
| :--- | :--- | :--- |
| **Simpan Draf Foto Banner** | Feature Test PHPUnit | Field `landing_hero_image` lolos validasi `SaveLandingSiteEditorRequest` dan tersimpan ke `draft_landing_hero_image` serta `draft_landing_page_document`. |
| **Publikasi Foto Banner** | Feature Test PHPUnit & Browser | File foto di `landing/media/` tidak terhapus/rusak saat diterbitkan. Halaman publik menampilkan URL gambar yang valid tanpa fallback. |
| **Perubahan Teks General** | Feature Test PHPUnit & Iframe Bridge | Sub judul, judul utama, dan info periode yang diubah di inspector langsung ter-update di draf, tersimpan ke `landing_page_document`, dan tampil di landing page publik setelah diterbitkan. |
| **Judul "Program Magang Kominfo Sleman"** | Assertion Blade Test | Judul tidak lagi ditolak secara paksa oleh `hero.blade.php`. |
| **Konsistensi Tombol Terbitkan** | Manual & Integration Test | Tombol Terbitkan otomatis menyimpan perubahan terakhir sebelum eksekusi publish. |
| **Seluruh Test Suite Terkait** | `php artisan test --filter=SiteEditor` | 100% tes lolos hijau (0 regression). |
| **Formatting Standar PSR-12** | `./vendor/bin/pint --test` | Lolos tanpa pelanggaran styling kode PHP. |
| **Frontend Asset Build** | `npm run build` | Kompilasi bundle Vite berhasil tanpa error. |

---

## 4. Pertanyaan Terbuka & Konfirmasi

Sebelum melangkah ke tahap eksekusi implementasi kode:
1. Apakah Anda menyetujui penghapusan hardcoded pengecekan judul `"Program Magang Kominfo Sleman"` di `hero.blade.php` sehingga judul tersebut dapat tampil persis seperti yang Anda ketikkan?
2. Apakah rencana kerja ini sudah sesuai dengan yang Anda harapkan untuk kami eksekusi sekarang?

