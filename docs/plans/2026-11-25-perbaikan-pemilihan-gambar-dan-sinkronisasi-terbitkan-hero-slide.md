# Rencana Perbaikan: Pemilihan Gambar Media Picker & Sinkronisasi Penerbitan Multi-Slide Hero Banner (Canvas Editor)

Dokumen ini merinci rencana teknis terstruktur untuk menyelesaikan dua permasalahan pada Canvas Site Editor (`/superadmin/site-editor`):
1. **Permasalahan 1 (Media Picker)**: Pengguna tidak dapat memilih gambar ketika menekan tombol "Pilih Gambar" pada kartu slide inspector (UX tidak responsif, kurangnya interaksi instan double-click/quick select di kartu berkas, dan minimnya visual feedback).
2. **Permasalahan 2 (Sinkronisasi Penerbitan Slide)**: Perubahan slide (menambah slide baru, mengubah judul/deskripsi, mengganti tipe slide, atau menghapus slide) tidak tercermin pada halaman pratinjau maupun situs live publik setelah tombol "Terbitkan" ditekan.

---

## 1. Analisis Akar Masalah (Root Cause Analysis)

### Akar Masalah 1: Pemilihan Gambar pada Kartu Slide Inspector
1. **Pemisahan Aksi Pilih & Konfirmasi di Media Modal**:
   - Di `media-library.js`, saat modal media dibuka via `open('image', callback)`, grid kartu berkas hanya memiliki event `click` untuk menandai kartu dengan border biru (`selectItem()`) dan mengaktifkan tombol footer `#btn-select-media-action` ("Gunakan Berkas Ini").
   - Pengguna web umumnya mengharapkan **double-click** pada kartu gambar langsung memilih berkas dan menutup modal, atau adanya tombol aksi cepat **"Pilih"** di atas kartu gambar.
   - Karena tombol konfirmasi berada di pojok kanan bawah modal footer (terpisah dari area grid foto), pengguna sering kali mengira gambar tidak bisa dipilih karena kartu yang diklik tidak langsung diterapkan ke slide.
2. **Feedback Visual Thumbnail Pasca-Pemilihan**:
   - Di `setSlideImage(idx, mediaItem)` pada `inspector.js`, pembaruan thumbnail hanya mengubah atribut `src` elemen `#wp-slide-thumb-${idx}`. Jika URL media memiliki variasi path relatif (`landing/media/...`), penanganan string URL dapat menghasilkan gambar blank tanpa peringatan.
   - Tidak ada toast notifikasi lokal konfirmasi bahwa gambar slide telah berhasil dipasang.

### Akar Masalah 2: Slide yang Diedit/Ditambah/Dihapus Tidak Berubah di Live Pasca-Terbit
1. **Celah Pemetaan Data di `LegacyCmsAdapter.php`**:
   - Di `convertLegacyToDocument()`, pembuatan konten blok `hero` **sama sekali tidak memetakan key `landing_hero_slides`**. Akibatnya, setiap kali fallback atau konversi legacy dijalankan oleh sistem, key `'slides'` bernilai kosong/hilang.
   - Di `syncLegacyKeysFromDocument()`, sistem menyinkronkan seluruh field (`kicker`, `title`, `year`, `image`, dll.) tetapi **melewatkan penyimpanan `landing_hero_slides`**.
2. **Definisi Bawaan Blok Hero di `BlockRegistry.php`**:
   - Skema `defaultContent` untuk blok `hero` di `BlockRegistry.php` belum mendaftarkan key `'slides' => []`.
3. **Logika Sinkronisasi Pasca-Terbit di `LandingCmsStagingService.php`**:
   - Pada method `publishDraft()`, sinkronisasi `$liveDoc['blocks']` untuk blok `hero` mengecek `if (is_array($liveHeroSlides) && count($liveHeroSlides) > 0)`. Jika pengguna menghapus slide hingga tersisa 1 slide default atau array kosong, kondisi ini berpotensi tidak menyinkronkan perubahan penghapusan ke dokumen live.
4. **Cache Browser pada Iframe Kanvas dan Pratinjau**:
   - Di `editor-app.js`, setelah publikasi berhasil, iframe kanvas di-reload dengan `iframe.contentWindow.location.reload()`. Tanpa query string anti-cache (misal `?ts=123456789`), browser sering kali melayani halaman dari memory cache lokal sehingga pengguna merasa perubahannya belum tampil.
5. **Fallback Dinamis pada View `hero.blade.php`**:
   - Di `resources/views/public/blocks/hero.blade.php`, jika `$content['slides']` bernilai null atau kosong, template langsung jatuh ke 1 slide statis tanpa memeriksa nilai `Pengaturan::json('landing_hero_slides')` sebagai fallback database.

---

## 2. Solusi Teknis & Rencana Perubahan

```
+-----------------------------------------------------------------------------------+
|                           ALUR SINKRONISASI HERO SLIDE                            |
+-----------------------------------------------------------------------------------+
| 1. User Edit/Tambah/Pilih Gambar Slide di Inspector                              |
|    └─► Double-click / Klik "Pilih" di Media Modal -> setSlideImage(idx, item)     |
|    └─► syncHeroSlides() -> Update this.state['landing_hero_slides'] & heroBlock   |
|    └─► Bridge kirim WP_UPDATE_HERO_SLIDES -> Kanvas Iframe live update           |
+-----------------------------------------------------------------------------------+
                                        │
                                        ▼ (Klik "Terbitkan")
+-----------------------------------------------------------------------------------+
| 2. Penyimpanan Draf & Eksekusi Terbitkan                                          |
|    └─► editorState.saveDraft(false, force=true)                                   |
|        └─► Payload memuat landing_hero_slides & landing_page_document             |
|        └─► StagingService menyimpan draft_landing_hero_slides & draft_doc         |
|    └─► POST /superadmin/site-editor/publish                                       |
|        └─► draft_landing_hero_slides -> landing_hero_slides                       |
|        └─► draft_landing_page_document -> landing_page_document                   |
|        └─► Pengaturan::clearCache()                                               |
+-----------------------------------------------------------------------------------+
                                        │
                                        ▼ (Hasil Publikasi)
+-----------------------------------------------------------------------------------+
| 3. Render Live & Reload Anti-Cache                                                |
|    └─► Kanvas Iframe di-reload dengan URL anti-cache (ts=Date.now())             |
|    └─► hero.blade.php membaca content['slides'] / Pengaturan landing_hero_slides |
|    └─► Slide baru, judul, foto, dan deskripsi tampil 100% sempurna di Publik      |
+-----------------------------------------------------------------------------------+
```

---

## 3. Rincian File yang Akan Dimodifikasi

### A. Frontend (JavaScript & CSS)
1. **`resources/js/pages/superadmin/site-editor/media-library.js`**:
   - Tambahkan event listener `dblclick` pada setiap `.wp-media-card` agar double-click langsung memilih berkas dan menutup modal.
   - Tambahkan tombol quick-action "Pilih" pada hover kartu berkas untuk aksesibilitas satu klik.
   - Sempurnakan transisi dan feedback visual saat berkas berhasil dipilih.
   - Perbaiki alur pasca-upload agar berkas yang baru diunggah langsung disorot dan siap dipilih secara instan.
2. **`resources/js/pages/superadmin/site-editor/inspector.js`**:
   - Sempurnakan method `setSlideImage(idx, mediaItem)`: normalisasi URL (`/storage/...`), perbarui thumbnail DOM, sinkronkan ke `this.heroSlides[idx]`, panggil `this.syncHeroSlides()`, dan tampilkan toast lokal.
   - Pastikan method `syncHeroSlides()` selalu memperbarui referensi di `heroBlock.content.slides` dan memicu `this.notifyDirty()`.
3. **`resources/js/pages/superadmin/site-editor/editor-app.js`**:
   - Pada penanganan sukses tombol "Terbitkan", perbarui reload iframe kanvas pratinjau dengan parameter query anti-cache `timestamp=${Date.now()}` agar kanvas langsung memperbarui tampilan dengan data live terbaru tanpa cache browser.
4. **`resources/css/pages/superadmin/site-editor.css`**:
   - Berikan styling halus untuk tombol hover quick-action "Pilih" di dalam `.wp-media-card` dan transisi seleksi kartu gambar yang lebih jelas.

### B. Backend (PHP & Laravel Services)
1. **`app/Services/LandingPage/LegacyCmsAdapter.php`**:
   - Di `convertLegacyToDocument()`: Tambahkan pembacaan `'slides' => $jsonGetter('landing_hero_slides', [])` pada konten blok `hero`.
   - Di `syncLegacyKeysFromDocument()`: Tambahkan penyimpanan dua arah `Pengaturan::setJson($prefix.'landing_hero_slides', $content['slides'])` saat blok bertipe `hero` diproses.
2. **`app/Services/LandingPage/BlockRegistry.php`**:
   - Daftarkan key `'slides' => []` pada array `defaultContent` blok `hero`.
3. **`app/Services/LandingCmsStagingService.php`**:
   - Pada `publishDraft()`: Pastikan sinkronisasi `$liveHeroSlides` memperbarui `$b['content']['slides']` baik saat slide berisi banyak data, 1 data, maupun array kosong (penghapusan).
4. **`resources/views/public/blocks/hero.blade.php`**:
   - Perkuat fallback data `$rawSlides`: jika `$content['slides']` tidak terdefinisi, fallback ke `\App\Support\Pengaturan::json('landing_hero_slides', null)`.

---

## 4. Tahapan Pengerjaan Bertahap (Tasks)

- [ ] **Task 1: Perbaikan Interaksi Media Picker (`media-library.js` & `site-editor.css`)**
  - Implementasikan event `dblclick` pada kartu berkas di grid media.
  - Tambahkan tombol quick-action "Pilih Berkas" di dalam kartu berkas.
  - Pastikan tombol "Gunakan Berkas Ini" di footer dan aksi instan sinkron.
- [ ] **Task 2: Sempurnakan Logika Pemilihan & Thumbnail Slide (`inspector.js`)**
  - Perbaiki `setSlideImage()` agar selalu menormalisasi path/url gambar.
  - Tampilkan toast konfirmasi lokal "Gambar Slide #[N] berhasil diperbarui".
  - Pastikan kanvas iframe langsung bergeser ke slide yang gambarnya baru diganti.
- [ ] **Task 3: Perbaiki Pemetaan Data Legacy & Registry Blok (`LegacyCmsAdapter.php` & `BlockRegistry.php`)**
  - Daftarkan `'slides' => []` di `defaultContent` blok `hero` pada `BlockRegistry.php`.
  - Tambahkan pembacaan dan penyimpanan dua arah `landing_hero_slides` pada `LegacyCmsAdapter.php`.
- [ ] **Task 4: Pastikan Ketahanan Sinkronisasi Penerbitan (`LandingCmsStagingService.php`)**
  - Perbaiki kondisi sinkronisasi `$liveHeroSlides` di `publishDraft()` agar menangani penambahan, pengeditan, maupun penghapusan slide secara akurat.
- [ ] **Task 5: Perkuat Fallback di View Publik & Anti-Cache Reload (`hero.blade.php` & `editor-app.js`)**
  - Tambahkan query anti-cache timestamp saat me-reload iframe kanvas pasca-publikasi di `editor-app.js`.
  - Perkuat fallback `$rawSlides` di `hero.blade.php` agar membaca `landing_hero_slides` jika key `slides` di dokumen tidak tersedia.
- [ ] **Task 6: Pengujian & Quality Gate**
  - Kompilasi aset dengan `npm run build`.
  - Jalankan test suite PHPUnit dengan runtime PHP 8.3.
  - Jalankan Laravel Pint untuk memastikan standar PSR-12.

---

## 5. Rencana Pengujian & Verifikasi (Quality Gate)

1. **Uji Kasus 1 - Pemilihan Gambar**:
   - Buka `/superadmin/site-editor`.
   - Pilih blok Hero Banner -> Buka inspector slide.
   - Klik "Pilih Gambar" pada Slide #1 atau Slide #2.
   - Klik ganda (double-click) pada salah satu gambar di pustaka media -> Modal harus langsung tertutup dan gambar slide langsung berganti baik di kartu inspector maupun di kanvas iframe.
2. **Uji Kasus 2 - Penambahan Slide Baru & Pengeditan Teks**:
   - Klik "+ Tambah Slide Baru" -> Kartu Slide #2 muncul.
   - Ubah tipe slide ke "Foto & Info" atau "Banner Poster".
   - Pilih gambar melalui media picker.
   - Ketik Judul Slide baru dan Deskripsi baru.
   - Amati kanvas iframe bergeser ke Slide #2 dan menampilkan konten baru secara live.
3. **Uji Kasus 3 - Penerbitan ke Live Publik**:
   - Klik tombol "Terbitkan" di topbar.
   - Konfirmasi pada modal pop-up "Ya, Terbitkan".
   - Tunggu hingga toast notifikasi sukses muncul.
   - Buka halaman publik `/` (Situs Live) di tab baru.
   - Geser slider banner ke Slide #2: pastikan gambar, judul, deskripsi, dan tombol yang baru dibuat tampil persis sesuai yang diedit.
4. **Uji Kasus 4 - Penghapusan Slide**:
   - Di Canvas Editor, hapus salah satu slide via tombol tempat sampah di kartu slide.
   - Klik "Terbitkan" -> Konfirmasi.
   - Buka kembali situs live `/` -> Pastikan slide yang dihapus tidak lagi muncul di slider.
5. **Quality Gate Otomatis**:
   - `npm run build` sukses 0 error.
   - `php artisan test` sukses (PHP 8.3).
   - `./vendor/bin/pint --test` sukses bersih.

