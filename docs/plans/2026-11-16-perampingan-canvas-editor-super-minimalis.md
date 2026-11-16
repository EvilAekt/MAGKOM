# Rencana Kerja: Perampingan Canvas Editor Menjadi Super Minimalis (Skor 10/10)

Dokumen ini memuat rencana kerja teknis terstruktur untuk merampingkan Canvas Editor Landing CMS (`/superadmin/site-editor`) dari kondisi saat ini (skor 8.5/10) menjadi **Super Minimalis (skor 10/10)**, dengan fokus murni pada pengelolaan konten kedinasan tanpa distraksi konfigurasi desainer grafis.

---

## 1. Tujuan
1. **Fokus 100% Konten**: Menghapus opsi *Global Styles* (ganti font dan palet warna global) dari antarmuka pengguna agar identitas visual kedinasan (Pemkab Sleman) terlindungi dan tidak diubah sembarangan oleh staf non-desainer.
2. **Penyederhanaan Inspector (Panel Kanan)**: Menjadikan panel kanan sebagai panel tunggal *Block Settings & Content Editor* tanpa tab bertingkat yang membingungkan.
3. **Pembersihan Toolbar Melayang (Floating Toolbar)**: Merampingkan tombol aksi melayang di atas kanvas hanya untuk fungsi esensial (Nama Blok, Naik, Turun, Duplikasi, dan Hapus), tanpa tombol duplikasi navigasi.
4. **Mempertahankan Zero-Breakage**: Menjamin seluruh backend API, skema dokumen (`PageDocument`), database, dan 94 test suite yang sudah ada tetap kompatibel dan lulus 100%.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi 1:** Identitas visual (Font *Lexend* / *Source Sans 3* dan Warna Dinas Sleman `#1B3860`) bersifat baku dan paten sesuai *Design System MagKom*, sehingga tidak perlu diubah melalui UI oleh SuperAdmin harian. Nilai default tetap tersimpan aman di database/backend.
- **Asumsi 2:** Blok wajib sistem (*Hero*, *Footer*) tetap dilindungi oleh proteksi *System Lock* agar tidak dapat dihapus secara tidak sengaja.
- **Asumsi 3:** Fitur pratinjau multi-halaman (*Landing*, *Peta Alur*, *Kebijakan Privasi*) dan Rollback 1-langkah tetap dipertahankan sebagai fitur inti yang esensial.

---

## 3. Evaluasi Opsi Arsitektur

### Opsi A: Penghapusan Tuntas Tab Global Styles dari UI Frontend (Direkomendasikan)
- **Cara Kerja:**
  - Hapus tab `Styles` di `_inspector.blade.php` dan nonaktifkan render kontrol font/warna di `inspector.js`.
  - Panel Inspector langsung menampilkan kontrol konten blok aktif secara permanen tanpa tombol tab di atasnya.
  - Nilai `global_styles` tetap dikirim secara transparan oleh frontend dari dokumen awal ke backend saat menyimpan draf, sehingga skema `LandingPageDocument` tidak mengalami *breaking change*.
- **Kelebihan:**
  - Sangat bersih, intuitif, dan tidak ada kurva belajar bagi ASN baru.
  - Nol risiko kesalahan perusakan palet warna dinas oleh admin.
  - Kompatibilitas backend & database terjaga 100% tanpa perlu migrasi basis data.
- **Kekurangan:** Jika suatu saat instansi benar-benar ingin mengganti warna primer secara global lewat UI, opsi tersebut harus dibuka kembali di kode.

### Opsi B: Menyembunyikan Tab Styles di Menu Akordion Bawah / Sub-Menu "Lanjutan"
- **Cara Kerja:** Menyembunyikan opsi font dan warna di dalam akordion tertutup berlabel "Pengaturan Desain Tingkat Lanjut" di bagian paling bawah Inspector.
- **Kelebihan:** Fitur teknis tetap tersedia jika dicari secara sengaja.
- **Kekurangan:** Tetap menambah bobot visual, kode JavaScript tetap memuat listener warna/font yang kompleks, dan target "Super Minimalis 10/10" tidak tercapai maksimal.

> **Rekomendasi:** **Opsi A** karena portal pemerintah memerlukan konsistensi brand yang kaku, bukan kustomisasi bebas seperti website pribadi/blog.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/superadmin/site-editor/partials/_inspector.blade.php` | Modify | Menghapus tab header navigasi (`Block` vs `Styles`), menghapus container `#wp-tab-content-styles`, menjadikan kontainer blok sebagai panel utama permanen. |
| `resources/views/superadmin/site-editor/index.blade.php` | Modify | Merampingkan `#wp-floating-toolbar` (menghapus tombol duplikasi ke List View dan drag handle statis). |
| `resources/js/pages/superadmin/site-editor/inspector.js` | Modify | Menonaktifkan `initMainTabs()`, merampingkan inisialisasi kontrol warna/font global agar tidak mencari DOM yang sudah dihapus, tetap mempertahankan fallback `globalStyles` di payload state. |
| `resources/js/pages/superadmin/site-editor/editor-app.js` | Modify | Menghapus event listener tombol floating toolbar yang dirampingkan (`btn-toolbar-list`, `btn-toolbar-drag`). |
| `resources/css/pages/superadmin/site-editor.css` | Modify | Merapikan layout header Inspector yang kini tanpa tab, memastikan padding dan scrolling panel kanan optimal. |
| `tests/Feature/LandingPage/InspectorDesignSystemTest.php` | Modify | Menyesuaikan assertion pengujian UI Inspector jika ada yang memeriksa keberadaan elemen tab DOM. |

---

## 5. Langkah Implementasi Bertahap

### Fase 1: Perampingan UI Inspector (Blade & CSS)
- [ ] Buka `resources/views/superadmin/site-editor/partials/_inspector.blade.php`.
- [ ] Hapus elemen `.wp-inspector-tabs` (tombol tab `Block` dan `Styles`).
- [ ] Ganti header Inspector dengan judul tunggal yang elegan: `Pengaturan Blok` beserta badge nama blok aktif.
- [ ] Hapus markup container `#wp-tab-content-styles` (palet warna primer, font heading/body, card radius, dan layout container).
- [ ] Pastikan container `#wp-tab-content-block` menjadi satu-satunya konten yang langsung terlihat dan memenuhi panel samping.
- [ ] Sesuaikan `resources/css/pages/superadmin/site-editor.css` agar header Inspector memiliki tinggi dan tipografi yang rapi tanpa tombol tab.

### Fase 2: Penyelarasan Logic Frontend (JavaScript)
- [ ] Buka `resources/js/pages/superadmin/site-editor/inspector.js`.
- [ ] Hapus pemanggilan `this.initMainTabs()` dan method terkait.
- [ ] Pastikan `getSerializedState()` dan `globalStyles` tetap menyuplai nilai default desain dinas ke `editorState` tanpa error ketika tombol warna tidak ada di DOM.
- [ ] Buka `resources/views/superadmin/site-editor/index.blade.php` pada bagian `#wp-floating-toolbar`:
  - Hapus tombol `#btn-toolbar-drag` (statis/clunky).
  - Hapus tombol `#btn-toolbar-list` (sudah ada di topbar utama).
  - Pertahankan: Label Nama Blok, Pindah Atas, Pindah Bawah, Duplikasi, dan Hapus Blok.
- [ ] Sesuaikan event listener di `resources/js/pages/superadmin/site-editor/editor-app.js` untuk menghapus referensi tombol yang telah dipangkas.

### Fase 3: Kompilasi Aset & Verifikasi Integrasi
- [ ] Jalankan kompilasi aset frontend Vite: `npm run build`.
- [ ] Pastikan tidak ada *unresolved selector* atau *warning* pada bundle JS/CSS.
- [ ] Jalankan format checker: `./vendor/bin/pint --test`.

### Fase 4: Pengujian Otomatis & Regression Testing
- [ ] Jalankan analisis statis: `./vendor/bin/phpstan analyse --memory-limit=1G`.
- [ ] Jalankan test suite Canvas Editor & Landing Page:
  `php artisan test tests/Feature/LandingPage/ tests/Feature/SuperAdmin/CanvasEditorConsolidatedFeaturesTest.php tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php`.
- [ ] Verifikasi bahwa simpan draf, penerbitan, rollback, dan pratinjau live tetap berjalan 100% tanpa regresi.

---

## 6. Strategi Testing

1. **Unit & Feature Tests (`php artisan test`):**
   - Memastikan endpoint `POST /superadmin/site-editor/save-draft` tetap menerima dan menyimpan payload draf dengan sukses.
   - Memastikan `POST /superadmin/site-editor/publish` tetap menerbitkan seluruh blok dengan nilai default desain sistem dinas yang utuh.
   - Memastikan rute `GET /superadmin/site-editor` merender halaman dengan status 200 OK tanpa error missing blade partial.
2. **Pengujian Fungsional Browser (Manual):**
   - Klik setiap blok di kanvas (Hero, Informasi, Tahapan, FAQ, Footer) -> Panel kanan langsung berganti menampilkan form teks/gambar blok tersebut secara instan.
   - Uji tombol Simpan Draf, Terbitkan, dan Rollback di topbar.
   - Uji navigasi ganti halaman ke Peta Alur dan Kebijakan Privasi.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| Payload `save-draft` kehilangan field `global_styles` | Validasi backend gagal atau style publik hilang | `inspector.js` tetap menyimpan objek `globalStyles` bawaan di memori JavaScript dan mengirimkannya secara otomatis saat payload disimpan. |
| Test suite `InspectorDesignSystemTest` gagal karena mencari selektor tab | Status CI menjadi merah | Sesuaikan assertion test agar menguji logika backend & rendering desain, bukan keberadaan tab DOM yang telah dirampingkan. |

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] Tab *Global Styles* (font & palet warna) telah dihapus dari antarmuka pengguna Canvas Editor.
- [ ] Panel kanan Inspector menjadi panel tunggal *Block Settings* yang rapi dan bebas distraksi.
- [ ] Floating toolbar kanvas lebih ringkas dan hanya berisi tombol esensial.
- [ ] Aset Vite terkompilasi bersih tanpa peringatan (`npm run build`).
- [ ] PHPStan level 5 lulus 100% tanpa error (`[OK] No errors`).
- [ ] Laravel Pint lulus tanpa pelanggaran gaya kode.
- [ ] Seluruh 94 test suite Canvas Editor & Landing CMS lulus (`PASS`).
- [ ] Iframe pratinjau interaktif di 3 halaman publik berfungsi mulus.

