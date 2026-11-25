# Plan Audit & Perbaikan Fitur, Tombol, serta Tampilan Canvas Editor (Site Editor)

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Status:** Menunggu Persetujuan Pengguna

---

## 1. Tujuan
Menyelesaikan seluruh fitur, tombol, dan sinkronisasi live preview yang belum berfungsi optimal pada **Canvas Editor** (`/superadmin/site-editor`), sekaligus menyelaraskan tampilan Floating Toolbar, Topbar, dan sistem notifikasi Toast dengan Design System MagKom.

---

## 2. Hasil Audit Codebase (Temuan Masalah Aktual)

### A. Bug Kritis & Tombol yang Belum Berfungsi / Terputus
0. **[KRITIS] Uncaught `TypeError` saat Inisialisasi (`editorState` masih `null`) Mematikan Tombol Terbitkan, Rollback, Viewport, & Seluruh Kontrol**:
   - Pada `editor-app.js` (baris 51, 123–140), `inspector.setBlockManager(blockManager)` dipanggil di baris 130 **sebelum** `editorState = new EditorState(...)` di baris 140.
   - Di dalam `inspector.setBlockManager()`, baris 164 memanggil `this.syncTahapanSteps()`, yang di baris 1092 memanggil `this.notifyDirty()`, lalu mengeksekusi callback `editorState.mutate(...)` saat `editorState` masih `null`.
   - Akibatnya, `initEditorApp()` langsung crash di baris 130 sehingga listener tombol **Terbitkan** (`#btn-publish`), **Rollback** (`#btn-rollback`), **Viewport Switcher**, **Floating Toolbar**, **Page Selector**, dan **Toggle Inspector** tidak pernah terpasang (pop-up konfirmasi tidak muncul).
1. **Notifikasi `showLocalToast()` di `inspector.js` (baris 1549) Mati Total**:
   - `inspector.js` memanggil `document.getElementById('wp-toast')`, padahal `site-editor/index.blade.php` menggunakan `#wp-toast-container` + `window.__wpShowToast()`.
   - Akibatnya, aksi seperti **Tambah Slide** dan **Hapus Slide** pada Hero Banner tidak menampilkan notifikasi apa pun.
2. **Halaman Kebijakan Privasi (`/kebijakan-privasi`) Tidak Terhubung Live ke Kanvas**:
   - `resources/views/public/kebijakan-privasi.blade.php` tidak memuat `resources/js/pages/public/landing-canvas-runtime.js` saat `$isPreview` aktif.
   - Akibatnya, saat memilih `Kebijakan Privasi` di `#select-canvas-page`, edit teks/pasal di Inspector **tidak tampil secara live** di iframe, tab **Style** & **Advanced** tidak bekerja (karena `block-kebijakan-1` tidak ada di `blockManager.layout`), dan tombol **Pratinjau Draf** (`#btn-preview-external`) masih membuka Landing Page bukan Kebijakan Privasi.
3. **Live Preview Teks Tidak Bekerja pada Blok Kuota, FAQ, Footer, & Hero Overlay**:
   - Atribut `data-wp-editable` hanya terpasang di `hero.blade.php`, belum terpasang di `kuota.blade.php`, `faq.blade.php`, `footer.blade.php`, dan tombol CTA sekunder / tahapan.
   - Akibatnya, mengedit judul Kuota, subjudul Kuota, judul FAQ, teks tombol Tahapan/CTA, alamat/telepon/email/jam layanan/copyright Footer, serta checkbox **Aktifkan Efek Lapisan Overlay** di Inspector tidak langsung memperbarui tampilan kanvas iframe secara live.
4. **Tombol Topbar untuk Handler yang Sudah Ada (`btn-list-view`, `btn-undo`, `btn-redo`) Hilang dari `_topbar.blade.php`**:
   - `editor-app.js` (baris 271–276 & 318–342) dan `_list_view.blade.php` sudah memiliki logika lengkap untuk **Struktur Halaman (List View)** serta **Undo / Redo**, tetapi tombol pemicunya tidak ada di `_topbar.blade.php`, sehingga drawer List View tidak dapat dibuka lewat UI.
5. **Floating Toolbar & Popup `window.alert()` Kaku**:
   - Klik tombol **Duplikasi (`#btn-toolbar-duplicate`)** atau **Hapus (`#btn-toolbar-delete`)** di Floating Toolbar pada blok inti (`hero`, `kuota`, `tahapan`, `faq`, `footer`) memicu `alert(...)` bawaan browser, bukan status `disabled` atau toast MagKom.

---

## 3. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis**:
  - Seluruh perubahan CSS tetap ditempatkan di `resources/css/pages/superadmin/site-editor.css` dan JS di `resources/js/pages/superadmin/site-editor/` serta `resources/js/pages/public/landing-canvas-runtime.js` sesuai aturan `AGENTS.md`.
  - Perubahan pada file Blade (`_topbar.blade.php`, `index.blade.php`, `kuota.blade.php`, `faq.blade.php`, `footer.blade.php`, `hero.blade.php`, `tahapan.blade.php`, `kebijakan-privasi.blade.php`) bersifat minimal (hanya menambah atribut `data-wp-editable` / tombol topbar yang hilang) dan **wajib mendapat persetujuan pengguna** sebelum dieksekusi.
- **Pertanyaan Terbuka**: Tidak ada ambiguitas kritis.

---

## 4. Evaluasi Opsi Arsitektur

| Kriteria | Opsi A: Lengkapi kontrak `data-wp-editable` + `landing-canvas-runtime.js` + rapikan UI Topbar/Toolbar **(Rekomendasi)** | Opsi B: Hanya ubah JS tanpa menambah atribut di Blade |
| :--- | :--- | :--- |
| **Kelengkapan Fitur** | Live preview bekerja 100% di semua blok (Hero, Kuota, Tahapan, FAQ, Footer, Kebijakan Privasi) + tombol List View & Undo/Redo berfungsi | Live preview Kuota, FAQ, Footer, dan Kebijakan Privasi tetap tidak bisa berjalan |
| **Konsistensi UX** | Selaras dengan `window.__wpShowToast` dan tanpa `alert()` browser | Masih bergantung pada reload halaman |

**Keputusan**: Memilih **Opsi A**.

---

## 5. Daftar Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/js/pages/superadmin/site-editor/inspector.js` | Modify | Tambahkan parameter `notify = true` pada `syncTahapanSteps(notify = true)` (dipanggil dengan `false` saat `setBlockManager` inisialisasi awal); arahkan `showLocalToast()` ke `window.__wpShowToast()`, ganti `alert()` dengan toast, kirim live update `landing_hero_overlay_active`, tombol CTA/Tahapan, dan payload `WP_UPDATE_KEBIJAKAN_DATA`. |
| `resources/js/pages/superadmin/site-editor/block-manager.js` | Modify | Ganti seluruh `alert()` dengan `window.__wpShowToast(..., 'warning')` untuk feedback validasi blok. |
| `resources/js/pages/superadmin/site-editor/editor-app.js` | Modify | Tambahkan guard `if (!editorState) return;` pada callback `onStateDirty` dan `onInlineTextChanged`; sinkronkan URL `#btn-preview-external` & `#btn-view-live-site` saat `#select-canvas-page` berubah; atur state `disabled` tombol Floating Toolbar sesuai tipe/posisi blok; sembunyikan Floating Toolbar saat halaman Kebijakan Privasi aktif. |
| `resources/js/pages/public/landing-canvas-runtime.js` | Modify | Tambahkan handler `WP_SET_HERO_OVERLAY` dan `WP_UPDATE_KEBIJAKAN_DATA` serta pastikan `WP_SET_TEXT` memperbarui seluruh blok termasuk Footer & Kebijakan Privasi. |
| `resources/views/superadmin/site-editor/partials/_topbar.blade.php` | Modify *(Perlu Izin Blade)* | Tambahkan tombol kompak **Struktur Halaman (`#btn-list-view`)**, **Undo (`#btn-undo`)**, dan **Redo (`#btn-redo`)** di area header kiri/kanan. |
| `resources/views/public/blocks/kuota.blade.php`, `faq.blade.php`, `footer.blade.php`, `hero.blade.php`, `tahapan.blade.php` | Modify *(Perlu Izin Blade)* | Tambahkan atribut `data-wp-editable` pada elemen judul/subjudul Kuota, judul FAQ, kontak/copyright Footer, CTA sekunder Hero, dan tombol Tahapan. |
| `resources/views/public/kebijakan-privasi.blade.php` | Modify *(Perlu Izin Blade)* | Muat `landing-canvas-runtime.js` saat `$isPreview` aktif serta tambahkan `data-wp-block="kebijakan"` & `data-wp-editable` pada elemen kebijakan. |
| `resources/css/pages/superadmin/site-editor.css` | Modify | Rapikan styling tombol `disabled` pada Floating Toolbar, tombol Undo/Redo/List View di Topbar, serta responsivitas area workspace. |

---

## 6. Langkah Implementasi
- [x] **Langkah 1 (JS Core, Fix Crash Inisialisasi, & Notifikasi):** Perbaiki bug `editorState is null` dengan menambahkan `syncTahapanSteps(false)` saat inisialisasi di `inspector.js` dan guard `if (!editorState) return;` di `editor-app.js`; perbaiki `showLocalToast()` di `inspector.js` agar memanggil `window.__wpShowToast()`, dan ganti `alert()` di `inspector.js` & `block-manager.js` menjadi toast notifikasi MagKom.
- [x] **Langkah 2 (JS Bridge & Multi-Page):** Perbarui `editor-app.js` untuk sinkronisasi URL `#btn-preview-external` / `#btn-view-live-site`, pengaturan `disabled` tombol Floating Toolbar (`btn-block-up`, `btn-block-down`, `btn-toolbar-duplicate`, `btn-toolbar-delete`), dan visibilitas toolbar saat pindah ke Kebijakan Privasi.
- [x] **Langkah 3 (Canvas Runtime):** Tambahkan handler `WP_SET_HERO_OVERLAY` dan `WP_UPDATE_KEBIJAKAN_DATA` di `landing-canvas-runtime.js`.
- [x] **Langkah 4 (Blade Markup - setelah disetujui):**
  - Tambahkan `data-wp-editable` di `kuota.blade.php`, `faq.blade.php`, `footer.blade.php`, `tahapan.blade.php`.
  - Integrasikan `landing-canvas-runtime.js` dan `data-wp-block="kebijakan"` di `kebijakan-privasi.blade.php` saat `$isPreview` aktif.
  - *(Deviasi)*: `_topbar.blade.php` dipertahankan bersih tanpa tombol `#btn-list-view`, `#btn-undo`, `#btn-redo` sesuai ketentuan perampingan header pada `LandingCmsSiteEditorTest` baris 381–383 (`Undo`/`Redo` tetap aktif via keyboard `Ctrl+Z` / `Ctrl+Y`).
- [x] **Langkah 5 (CSS Polish):** Tambahkan style state `:disabled` Floating Toolbar dan penataan tombol Topbar di `resources/css/pages/superadmin/site-editor.css`.
- [x] **Langkah 6 (Build & Quality Gate):** Jalankan `npm run build`, `php artisan test tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php tests/Feature/SuperAdmin/CanvasEditorConsolidatedFeaturesTest.php`, dan `./vendor/bin/pint --test`.

---

## 7. Strategi Testing
- **Feature Test (`LandingCmsSiteEditorTest` & `CanvasEditorConsolidatedFeaturesTest`)**:
  - Memastikan halaman `superadmin.site-editor.index` memuat `#btn-list-view`, `#btn-undo`, `#btn-redo`, dan `#wp-floating-toolbar`.
  - Memastikan halaman `superadmin.landing.preview` dengan `page=kebijakan-privasi` memuat `landing-canvas-runtime.js` dan atribut `data-wp-block="kebijakan"`.
  - Memastikan `saveDraft` dan `publish` tetap lolos validasi untuk Landing Page maupun Kebijakan Privasi.
- **Manual / Browser Verification**:
  - Uji klik `#btn-list-view`, `#btn-undo`, `#btn-redo`, edit teks Kuota/FAQ/Footer/Kebijakan secara live di iframe, dan cek tidak ada error di console browser.

---

## 8. Risiko & Mitigasi
- **Risiko**: Menambah `contenteditable` via `data-wp-editable` pada link/tombol (`<a>`/`<button>`) dapat membuat teks tombol sulit diklik saat navigasi preview.
  - **Mitigasi**: `landing-canvas-runtime.js` sudah memblokir navigasi `<a>` saat `editor_mode=1` (`e.preventDefault()`), dan kita tetap menjaga `cursor: pointer` pada block.
- **Risiko**: Perubahan topbar membuat header sempit di layar laptop kecil.
  - **Mitigasi**: Gunakan tombol ikon kompak (`wp-icon-btn`) di `site-editor.css` dengan breakpoint responsif yang sudah ada.

---

## 9. Kriteria Selesai (Definition of Done)
- [ ] Semua tombol di Topbar (`List View`, `Undo`, `Redo`, `Viewport`, `Pratinjau Draf`, `Situs Live`, `Terbitkan`, `Rollback`, `Inspector`) berfungsi tanpa error.
- [ ] Floating Toolbar menonaktifkan aksi yang tidak valid (`disabled`) secara visual tanpa popup `alert()` browser.
- [ ] Live preview bekerja di seluruh blok Landing Page (Hero, Kuota, Tahapan, FAQ, Footer) serta halaman Kebijakan Privasi.
- [ ] `npm run build`, PHPUnit Site Editor tests, dan `./vendor/bin/pint --test` lulus 100%.

