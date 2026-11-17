# Rencana Kerja: Perampingan Canvas Editor – Penghapusan Inserter (+ Blok & Media) & Fokus Klik-to-Edit

Dokumen ini memuat rencana kerja teknis terstruktur untuk merampingkan alur kerja Canvas Editor Landing CMS (`/superadmin/site-editor`). Dokumen ini disusun berdasarkan 4 tangkapan layar (SS 1–SS 4) dan instruksi spesifik pengguna untuk menghilangkan antarmuka Inserter blok & media drawer, sembari **100% mempertahankan fungsionalitas pengeditan konten blok langsung dari kanvas (Klik Blok di Kanvas -> Buka Form Pengaturan di Inspector Kanan)**.

---

## 1. Tujuan
1. **Menghilangkan Tombol `+` (Inserter) & Drawer "Blocks / Media":**
   - Menghapus tombol `+` di Topbar (`#btn-toggle-inserter`) seperti pada tangkapan layar **SS 2**.
   - Menghapus tombol `+` melayang antar-blok di kanvas (`.wp-canvas-add-handle`) seperti yang terlihat pada pembatas section di **SS 3**.
   - Menghilangkan panel drawer laci kiri Inserter yang memuat tab *Blocks* dan *Media* (`resources/views/superadmin/site-editor/partials/_inserter.blade.php`) seperti pada tangkapan layar **SS 1**.
2. **Mempertahankan Fungsionalitas Inti Klik-to-Edit (SS 3 -> SS 4):**
   - Ketika pengguna mengklik section/elemen mana pun pada kanvas pratinjau (seperti memilih "Hero Banner" pada **SS 3**):
     - Blok di kanvas langsung terseleksi aktif dengan badge chip ("BLOK: HERO BANNER") dan floating toolbar aksi (Naik, Turun, Duplikasi, Hapus).
     - Panel Inspector kanan berlabel "Pengaturan Blok" (**SS 4**) otomatis terbuka dan langsung menampilkan form isian konten lengkap (Judul Utama, Sub Judul/Kicker, Info Periode, Tombol Utama & Sekunder, serta Background Banner).
3. **Mempertahankan Pengelolaan Media Terintegrasi:**
   - Fitur penggantian media (gambar latar belakang hero, banner promosi, berkas panduan PDF) tetap 100% dapat dioperasikan melalui tombol "Ganti Gambar" / "Pilih Berkas" di dalam form panel Inspector kanan yang terhubung langsung ke Modal Media Library (`_media_modal.blade.php`).
4. **Kepatuhan Mutlak Aturan Proyek (`AGENTS.md`):**
   - Mempertahankan Blade sebagai lapisan markup murni tanpa inline `<style>`, inline `<script>`, atau inline event handler.
   - Seluruh penyesuaian styling ditempatkan di `resources/css/` dan logika interaksi di `resources/js/`.
   - Menjamin 111 test suite tetap lulus 100% hijau tanpa regresi pada backend, skema penyimpanan draf, penerbitan, maupun rollback.

---

## 2. Asumsi & Analisis Bukti Visual

### 2.1 Analisis Tangkapan Layar Pengguna
- **SS 1 (`media_1794898389809_64432fa7.png`):** Drawer kiri Inserter dengan tab *Blocks* & *Media*, search box "Cari blok...", serta kartu blok (Section, Container, Columns, Group, Spacer, Separator, Heading, Paragraph, Image, Button, List, Icon, FAQ, Testimonial, Gallery, Video, File PDF, Form). **Status: Dihilangkan.**
- **SS 2 (`media_1794898406627_42f1d687.png`):** Tombol ikon `+` (kotak biru muda bergaris halus) di Topbar dan tombol `+` bulat di kanvas yang memicu pembukaan drawer Inserter. **Status: Dihapus.**
- **SS 3 (`media_1794898517009_4190dc0a.jpg`):** Kanvas pratinjau WYSIWYG interaktif di mana pengguna mengklik section Hero Banner, menampilkan badge chip "BLOK: HERO BANNER" dan floating toolbar di atasnya. (Tombol `+` bulat di batas bawah section dihapus). **Status: Dipertahankan & Ditingkatkan.**
- **SS 4 (`media_1794898585608_d943d8b1.png`):** Panel Inspector sisi kanan berlabel "Pengaturan Blok" yang langsung menyajikan form isian konten blok aktif (General, Style, Advanced, judul, kicker, periode, tombol, ganti gambar). **Status: Dipertahankan & Menjadi Alur Utama.**

### 2.2 Asumsi Kerja
- Struktur section halaman publik kedinasan MAGKOM bersifat baku (Hero, Informasi/Persyaratan, Kuota, Tahapan, FAQ, Footer, Kebijakan Privasi, Peta Alur), sehingga admin dinas tidak memerlukan inserter blok layout bebas layaknya platform blog umum.
- Backend API (`LandingPageDocumentService`, `saveDraft`, `publish`, `rollback`, media upload) tetap dipertahankan secara utuh untuk menjamin integritas data dan kompatibilitas API.

---

## 3. Evaluasi Opsi Arsitektur

### Opsi A: Penghapusan Penuh Markup Inserter & Penyelarasan Event Klik-to-Edit (Direkomendasikan)
- **Cara Kerja:**
  - Hapus tombol `#btn-toggle-inserter` dari `_topbar.blade.php`.
  - Hapus `@include('superadmin.site-editor.partials._inserter')` dari `index.blade.php` dan hapus file partial `_inserter.blade.php`.
  - Hapus elemen tombol `.wp-canvas-add-handle` dari `landing-canvas-runtime.js` agar batas antar-section di kanvas menjadi bersih tanpa tombol `+`.
  - Pada `editor-app.js`, hapus binding Inserter dan optimalkan event `onBlockSelected` agar jika Inspector kanan sedang tersembunyi, sistem secara otomatis menampilkannya (`display: flex`) saat blok di kanvas diklik.
  - Hapus tombol `#btn-listview-add-block` pada footer `_list_view.blade.php` karena List View kini murni berfungsi sebagai navigator struktur pohon blok.
- **Kelebihan:**
  - Membersihkan DOM dari elemen-elemen mati yang tidak lagi digunakan.
  - Antarmuka menjadi sangat bersih, profesional, dan fokus pada tugas pengeditan ASN (bebas distraksi).
  - Alur SS 3 -> SS 4 menjadi lebih responsif dan deterministik: satu klik di kanvas langsung membuka pengaturan blok terkait di kanan.
  - Memenuhi 100% prinsip *Single Source of Truth* dan aturan `AGENTS.md`.
- **Kekurangan:** Perlu penyesuaian pada beberapa file Blade dan file ES Module terkait.

### Opsi B: Hanya Menyembunyikan Inserter Melalui CSS (`display: none`) Tanpa Mengubah Blade
- **Cara Kerja:** Menambahkan rule `display: none !important;` pada `#btn-toggle-inserter`, `#wp-inserter-drawer`, dan `.wp-canvas-add-handle`.
- **Kelebihan:** Tidak perlu mengubah file Blade.
- **Kekurangan:**
  - Meninggalkan 15 KB kode HTML mati di DOM (`_inserter.blade.php`) dan event listeners yang tidak pernah dipicu.
  - Rentan muncul kembali jika ada konflik styling CSS atau shortcut keyboard (`Ctrl+Shift+comma`).
  - Tidak mematuhi standar *clean architecture* MAGKOM.

> **Rekomendasi:** **Opsi A** karena memberikan kebersihan kode maksimal, performa rendering optimal, dan keandalan antarmuka jangka panjang.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/superadmin/site-editor/partials/_topbar.blade.php` | Modify | Menghapus tombol `#btn-toggle-inserter` (tombol `+` Inserter di header topbar). |
| `resources/views/superadmin/site-editor/index.blade.php` | Modify | Menghapus `@include('superadmin.site-editor.partials._inserter')`. |
| `resources/views/superadmin/site-editor/partials/_inserter.blade.php` | Delete | Menghapus file partial drawer Inserter yang tidak lagi digunakan. |
| `resources/views/superadmin/site-editor/partials/_list_view.blade.php` | Modify | Menghapus tombol footer `#btn-listview-add-block` ("Tambah Blok Baru"). |
| `resources/js/pages/public/landing-canvas-runtime.js` | Modify | Menghapus pembuatan tombol `+` melayang (`.wp-canvas-add-handle`) dan event listener `WP_INSERT_BLOCK_REQUEST`. Memastikan klik blok (`selectBlock`) tetap 100% memicu pengiriman event `WP_BLOCK_SELECTED`. |
| `resources/js/pages/superadmin/site-editor/editor-app.js` | Modify | Menghapus binding event tombol Inserter (`#btn-toggle-inserter`, `#btn-close-inserter`, search filter, card click). Menambahkan auto-open Inspector pada handler `onBlockSelected` saat blok di kanvas diklik. |
| `resources/js/pages/superadmin/site-editor/canvas-bridge.js` | Modify | Menghapus handler `WP_INSERT_BLOCK_REQUEST` dari daftar tipe pesan terdaftar. |
| `resources/css/pages/superadmin/site-editor.css` | Modify | Merapikan aturan CSS dengan menghapus selector khusus kartu Inserter grid yang tidak lagi dipakai, mempertahankan aturan `.wp-list-view-drawer` dan area kanvas tengah. |

---

## 5. Langkah Implementasi Bertahap

### Fase 1: Pembersihan Markup Blade (Sesuai Aturan AGENTS.md Poin 3.3)
- [ ] Buka `resources/views/superadmin/site-editor/partials/_topbar.blade.php`.
- [ ] Hapus elemen `<button type="button" id="btn-toggle-inserter" ...>` beserta ikon svg plus di dalamnya.
- [ ] Buka `resources/views/superadmin/site-editor/index.blade.php`.
- [ ] Hapus baris `@include('superadmin.site-editor.partials._inserter')`.
- [ ] Buka `resources/views/superadmin/site-editor/partials/_list_view.blade.php`.
- [ ] Hapus kontainer footer `<div class="wp-listview-footer">` beserta tombol `#btn-listview-add-block`.
- [ ] Hapus file `resources/views/superadmin/site-editor/partials/_inserter.blade.php`.

### Fase 2: Penyesuaian Kanvas Pratinjau (Iframe Runtime)
- [ ] Buka `resources/js/pages/public/landing-canvas-runtime.js`.
- [ ] Hapus blok kode pembuatan tombol `.wp-canvas-add-handle` pada fungsi `initEditorInteraction()`.
- [ ] Hapus style injector untuk `.wp-canvas-add-handle`.
- [ ] Pastikan listener klik blok `blockEl.addEventListener('click', ...)` tetap berjalan mulus memanggil `selectBlock(blockEl)` dan mengirim `WP_BLOCK_SELECTED` ke parent window.

### Fase 3: Penyelarasan Logika Frontend Parent (ES Modules)
- [ ] Buka `resources/js/pages/superadmin/site-editor/editor-app.js`.
- [ ] Pada inisialisasi `CanvasBridge`, perbarui handler `onBlockSelected`:
  - Pastikan memanggil `blockManager.setActiveBlock(activeSelectedBlockId)` dan `inspector.setActiveBlock(blockName, blockTitle, activeSelectedBlockId)`.
  - Tambahkan pengecekan: jika `#wp-site-inspector` memiliki `display: none`, otomatis ubah menjadi `display: flex` dan tambahkan kelas `is-active` pada `#btn-toggle-inspector`.
  - Hapus handler `onInsertBlockRequest`.
- [ ] Hapus variabel dan listener yang merujuk pada Inserter:
  - `toggleInserterBtn`, `closeInserterBtn`, `openInserter()`, `closeInserter()`.
  - Event listener pada `toggleInserterBtn`, `closeInserterBtn`, `searchInput`, dan `.wp-block-card`.
- [ ] Sesuaikan tombol List View: tombol `#btn-list-view` tetap berfungsi membuka dan menutup drawer List View secara independen tanpa mencoba memanggil `closeInserter()`.
- [ ] Buka `resources/js/pages/superadmin/site-editor/canvas-bridge.js`.
  - Hapus `'WP_INSERT_BLOCK_REQUEST'` dari `allowedMessageTypes`.

### Fase 4: Penataan CSS & Kompilasi Aset Vite
- [ ] Buka `resources/css/pages/superadmin/site-editor.css`.
- [ ] Bersihkan styling `.wp-btn-inserter` dan komponen kartu pencarian Inserter yang sudah dihapus.
- [ ] Pastikan `.wp-list-view-drawer` memiliki properti tata letak mandiri yang rapi.
- [ ] Jalankan kompilasi aset Vite: `npm run build`.
- [ ] Pastikan file `public/build/manifest.json` terbarui tanpa error kompilasi.

### Fase 5: Quality Gate & Pengujian Otomatis
- [ ] Jalankan linter dan formatter: `./vendor/bin/pint --test`.
- [ ] Jalankan static analysis: `./vendor/bin/phpstan analyse --memory-limit=1G`.
- [ ] Jalankan seluruh test suite terkait Canvas Editor dan Landing Page:
  - `php artisan test tests/Feature/LandingPage/`
  - `php artisan test tests/Feature/SuperAdmin/CanvasEditorConsolidatedFeaturesTest.php`
  - `php artisan test tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php`
- [ ] Pastikan seluruh pengujian menghasilkan 100% HIJAU (0 failure).

---

## 6. Strategi Testing

1. **Pengujian Fungsional Otomatis (Feature Tests):**
   - Menguji bahwa rute `GET /superadmin/site-editor` mengembalikan status 200 OK dengan markup kanvas pratinjau (`wp-preview-iframe`) dan panel Inspector (`wp-site-inspector`).
   - Menguji bahwa tidak ada error 500 akibat hilangnya file partial `_inserter.blade.php`.
   - Menguji bahwa endpoint simpan draf (`POST /superadmin/site-editor/save-draft`), publikasi (`POST /superadmin/site-editor/publish`), dan rollback (`POST /superadmin/site-editor/rollback`) tetap berfungsi 100% normal.
2. **Pengujian Alur Interaksi UI (Manual & Browser Verification):**
   - **Verifikasi Hilangnya Inserter:** Periksa bahwa tombol `+` di topbar dan tombol `+` di antar-section kanvas sudah tidak ada sama sekali.
   - **Verifikasi Klik-to-Edit (SS 3 -> SS 4):**
     1. Klik section "Hero Banner" pada kanvas -> Inspector kanan langsung terbuka dan menampilkan form "Konten Hero Banner" (Judul, Kicker, Periode, Tombol, Background Banner).
     2. Klik section "Informasi" / "Persyaratan" pada kanvas -> Inspector kanan langsung menampilkan form dokumen & persyaratan.
     3. Klik section "Kuota Magang" pada kanvas -> Inspector kanan langsung menampilkan form konfigurasi kuota.
     4. Klik section "Tahapan Seleksi" pada kanvas -> Inspector kanan langsung menampilkan form 4 checkpoint alur seleksi & tombol unduh buku panduan.
     5. Klik section "FAQ" pada kanvas -> Inspector kanan langsung menampilkan form akordeon FAQ.
     6. Klik section "Footer" pada kanvas -> Inspector kanan langsung menampilkan form kontak & alamat kantor dinas.
   - **Verifikasi Media Modal:** Klik tombol "Ganti Gambar" pada form Hero Banner di Inspector kanan -> Modal Media Library (`#wp-media-modal`) terbuka lancar untuk memilih atau mengunggah gambar baru.

---

## 7. Analisis Risiko & Mitigasi

| Potensi Risiko | Tingkat Risiko | Strategi Mitigasi |
| :--- | :---: | :--- |
| Terjadi JavaScript Error `null reference` akibat kode mencoba mengakses elemen `#btn-toggle-inserter` yang sudah dihapus. | Rendah | Seluruh pemanggilan elemen Inserter di `editor-app.js` dibersihkan tuntas dan menggunakan optional chaining (`?.`). |
| Panel Inspector kanan tetap tertutup saat pengguna mengklik blok di kanvas jika sebelumnya ditutup secara manual oleh user. | Sedang | Pada handler `onBlockSelected`, ditambahkan logika otomatis: jika `#wp-site-inspector` sedang berstatus `display: none`, otomatis diubah menjadi `display: flex` dan tombol toggle inspector diaktifkan. |
| Fitur penggantian media gambar atau PDF terganggu karena tab media pada drawer inserter dihapus. | Rendah | Mengonfirmasi bahwa modal `#wp-media-modal` beroperasi secara independen dan dipicu langsung oleh tombol "Ganti Gambar" / "Pilih Berkas" di dalam panel Inspector. |
| Perubahan file Blade melanggar aturan proyek `AGENTS.md`. | Rendah | Sesuai aturan `AGENTS.md` poin 3.3, perubahan Blade telah dijelaskan secara transparan, terbatas pada penghapusan elemen markup yang diminta pengguna, tanpa menyisipkan CSS/JS inline di Blade, dan diajukan untuk persetujuan eksplisit pengguna sebelum eksekusi. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Tombol `+` di topbar (`#btn-toggle-inserter`) telah dihapus.
- [ ] Tombol `+` melayang antar-blok di kanvas (`.wp-canvas-add-handle`) telah dihapus.
- [ ] Drawer panel kiri Inserter (`_inserter.blade.php`) telah dihapus dari codebase.
- [ ] Tombol "Tambah Blok Baru" di footer List View telah dihapus.
- [ ] Mengklik blok mana pun di kanvas pratinjau (SS 3) langsung membuka dan memfokuskan form isian blok terkait pada Inspector panel kanan (SS 4).
- [ ] Tombol "Ganti Gambar" dan "Pilih Dokumen PDF" di Inspector tetap dapat membuka Media Library modal secara sempurna.
- [ ] Seluruh aset terkompilasi sukses lewat `npm run build`.
- [ ] `./vendor/bin/pint --test` lulus tanpa pelanggaran gaya kode PSR-12.
- [ ] `./vendor/bin/phpstan analyse --memory-limit=1G` menghasilkan status *No errors*.
- [ ] Seluruh test suite terkait (`BlockManagementEngineTest`, `CanvasEditorConsolidatedFeaturesTest`, `LandingCmsSiteEditorTest`, dll.) lulus 100% hijau.

