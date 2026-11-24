# Rencana Kerja: Perbaikan Logika & Interaksi Tambah Slide Hero Banner

- **Dokumen**: `docs/plans/2026-11-24-perbaikan-logika-dan-interaksi-tambah-slide-hero-banner.md`
- **Tanggal**: 24 November 2026
- **Status**: Menunggu Konfirmasi Pengguna
- **Target Sub-Sistem**: WordPress Site Editor (Hero Banner Multi-Slide Repeater), Canvas Bridge, & Landing Runtime

---

## 1. Latar Belakang & Masalah (Problem Statement)
Pengguna melaporkan bahwa saat menekan tombol `+ Tambah Slide` di panel Site Editor (Hero Banner):
1. Tombol dirasa **"belum bisa" / macet** karena setelah diklik tidak ada perubahan yang terlihat di layar.
2. Logika slide dirasa **"masih janggal"**:
   - Pengguna ingin menambahkan slide dokumentasi yang memiliki foto/gambar beserta subjudul/judul dan deskripsi keterangan.
   - Namun sistem sebelumnya membuat slide baru dengan default tipe `banner` (poster Canva murni) yang **menyembunyikan field judul dan deskripsi**.
   - Sistem lama juga menduplikasi gambar dari Slide #1 ke slide baru, sehingga kartu baru tampak identik dan membingungkan.
   - Panel Inspector tidak melakukan auto-scroll ke bawah saat kartu baru terbentuk di paling bawah repeater, sehingga pengguna yang berada di posisi atas mengira tombol tidak berfungsi sama sekali.
   - Slider di kanvas iframe tengah layar tidak otomatis bergeser ke slide baru yang baru ditambahkan.

---

## 2. Investigasi & Akar Masalah (Root Causes)

| No | Gejala / Isu | Lokasi Kode | Akar Masalah |
| :--- | :--- | :--- | :--- |
| 1 | Event click tidak terpusat | `inspector.js` (`setMediaLibrary`) | Listener `#btn-add-hero-slide` dipasang di dalam metode `setMediaLibrary()`. Jika ada anomali urutan inisialisasi, listener tidak aktif. Seharusnya diikat permanen di `initContentInputs()`. |
| 2 | Tidak ada respons visual (kesan macet) | `inspector.js` (`addHeroSlide`) | Ketika slide baru di-push ke array dan di-render, container tidak di-scroll. Pengguna tetap melihat Slide #1 di atas dan tidak tahu Slide #2 sudah muncul di bawah. |
| 3 | Field judul & deskripsi hilang | `inspector.js` (`addHeroSlide`) | Default tipe slide baru disetel ke `type: 'banner'`. Pada tipe `banner`, input judul & deskripsi di-hide (`showText = false`), membuat pengguna bingung di mana harus mengisi keterangannya. |
| 4 | Duplikasi gambar lama | `inspector.js` (`addHeroSlide`) | Kode lama menyalin `this.heroSlides[0]?.image`, sehingga Slide #2 otomatis memakai foto Slide #1. |
| 5 | Kanvas tidak bergeser | `landing-canvas-runtime.js` | Slider di iframe menerima payload update slides, tapi index slider aktif tidak dipindahkan ke slide baru yang baru saja dibuat. |

---

## 3. Pendekatan Arsitektur & Solusi (Senior Dev Recommendation)

Kami merekomendasikan **Solusi Komprehensif (Opsi B)**:
1. **Robohkan Kerapuhan Event Binding**:
   - Pindahkan binding tombol `#btn-add-hero-slide` ke metode `initHeroSlidesControls()` yang dipanggil langsung saat inisialisasi Inspector (`initContentInputs()`).
   - Beri proteksi tombol dan pencegahan *double-click* cepat.
2. **Logika Default Tipe Slide yang Natural ("Foto & Info")**:
   - Ubah default tipe slide baru dari `banner` menjadi **`photo_text` (Foto & Info)**.
   - Ketika tombol `+ Tambah Slide` diklik, pengguna **LANGSUNG** melihat:
     - Pemilih Tipe Slide (Foto & Info aktif)
     - Tombol "Pilih Gambar" dengan thumbnail default bersih (`/images/hero_dokumentasi_diskominfo.png`)
     - Input **Label Badge** (misal: "DOKUMENTASI RESMI")
     - Input **Judul Slide** (misal: "Dokumentasi Kegiatan 2")
     - Input **Deskripsi / Keterangan** (siap diisi penjelasan kegiatan)
     - Input **Teks Tombol & Tautan** (opsional)
   - Jika pengguna memang ingin mengunggah poster visual Canva/Photoshop (yang gambarnya sudah ada teks desain grafisnya), pengguna cukup mengklik tab `🖼️ Banner` untuk mematikan overlay teks agar gambarnya bersih tanpa tertimpa teks web.
3. **Feedback Visual Instan & UX Fluid**:
   - Begitu tombol `+ Tambah Slide` diklik:
     - Kartu slide baru otomatis di-scroll ke area pandang (`scrollIntoView({ behavior: 'smooth', block: 'nearest' })`).
     - Diberikan efek highlight border flash biru lembut selama 1.2 detik pada kartu baru.
     - Kursor otomatis memfokuskan field **Judul Slide** baru.
     - Tampilkan toast notification kecil: *"Slide #[nomor] berhasil ditambahkan."*
4. **Sinkronisasi Slider Kanvas Iframe**:
   - Kirim sinyal lewat `canvasBridge` ke iframe kanvas untuk otomatis menggeser slider ke slide baru yang baru ditambahkan, sehingga di layar utama pengguna langsung melihat slide barunya.
5. **Kompilasi Asset Vite & Verifikasi**:
   - Jalankan `npm run build` dan automated test suite untuk memastikan tidak ada regresi.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/js/pages/superadmin/site-editor/inspector.js` | Modify | 1. Pindahkan listener `btn-add-hero-slide` ke `initHeroSlidesControls()`.<br>2. Ubah default tipe slide baru ke `photo_text`.<br>3. Bersihkan duplikasi gambar pertama (gunakan placeholder bersih).<br>4. Tambahkan auto-scroll, flash highlight, focus input, dan toast notification.<br>5. Kirim event perpindahan slider kanvas ke `canvasBridge`. |
| `resources/js/pages/superadmin/site-editor/canvas-bridge.js` | Modify | Tambahkan metode `navigateHeroSlide(slideIndex)` untuk memberi tahu iframe kanvas agar menggeser slider ke index slide tertentu. |
| `resources/js/pages/public/landing-canvas-runtime.js` | Modify | Tambahkan penanganan pesan `WP_NAVIGATE_HERO_SLIDE` agar track slider otomatis bergeser ke index slide target dan tombol dot aktif berpindah. |
| `resources/css/pages/superadmin/site-editor.css` | Modify | Tambahkan style animasi highlight `@keyframes wpSlideHighlight` untuk kartu slide yang baru ditambahkan. |

---

## 5. Rencana Langkah Implementasi

- [x] **Langkah 1**: Perbarui `canvas-bridge.js` dengan fungsi `navigateHeroSlide(slideIndex)`.
- [x] **Langkah 2**: Perbarui `landing-canvas-runtime.js` untuk menerima dan memproses `WP_NAVIGATE_HERO_SLIDE` pada track slider desktop dan mobile.
- [x] **Langkah 3**: Tambahkan class styling animasi `.wp-slide-card-new-highlight` di `resources/css/pages/superadmin/site-editor.css`.
- [x] **Langkah 4**: Refactor logika `addHeroSlide()` dan event binding repeater di `resources/js/pages/superadmin/site-editor/inspector.js`:
  - Ubah default `type: 'photo_text'`.
  - Berikan placeholder gambar bersih.
  - Implementasikan auto-scroll, flash highlight, auto-focus, dan toast feedback.
  - Panggil `this.bridge.navigateHeroSlide(...)`.
- [x] **Langkah 5**: Kompilasi ulang aset dengan `npm run build`.
- [x] **Langkah 6**: Jalankan formatting Pint `./vendor/bin/pint --test` dan automated test PHPUnit.

---

## 6. Strategi Testing

1. **Pengujian Klik Tambah Slide**:
   - Buka Site Editor, klik `+ Tambah Slide`.
   - Pastikan panel otomatis scroll ke `Slide #2`, kartu menyala (highlight), dan field Judul terfokus.
   - Pastikan toast "Slide #2 berhasil ditambahkan" muncul.
   - Pastikan iframe kanvas otomatis bergeser menampilkan Slide #2.
2. **Pengujian Pengisian Data Slide**:
   - Masukkan judul: "Pelatihan SPBE 2026", deskripsi: "Kegiatan pelatihan infrastruktur SPBE Diskominfo".
   - Beralih antar tipe (`Banner`, `Foto & Info`, `Pengumuman`):
     - Saat memilih `Banner`: field teks disembunyikan, kanvas menampilkan poster penuh.
     - Saat memilih `Foto & Info`: foto dan teks overlay muncul bersamaan.
     - Saat memilih `Pengumuman`: tampilan kartu informasi elegan tanpa foto.
3. **Pengujian Penghapusan Slide**:
   - Hapus slide tambahan, pastikan tombol hapus disabled saat sisa 1 slide.
4. **Pengujian Simpan Draf & Publikasi**:
   - Simpan draf, lakukan reload halaman, pastikan data seluruh slide tetap utuh dan tersinkronisasi.
5. **Quality Gates**:
   - `npm run build` sukses 0 error.
   - `./vendor/bin/pint --test` lolos.
   - Test suite `LandingCmsSiteEditorTest` dan `LandingCmsStagingTest` hijau.

---

## 7. Kriteria Selesai (Definition of Done)

- [x] Tombol `+ Tambah Slide` 100% responsif dan terikat permanen.
- [x] Kartu baru otomatis muncul dengan tipe **Foto & Info** (lengkap dengan foto, badge, judul, deskripsi, dan tombol).
- [x] Ada auto-scroll halus dan efek highlight visual sehingga pengguna tahu pasti bahwa slide telah berhasil ditambahkan.
- [x] Kanvas iframe otomatis menggeser slider ke slide baru.
- [x] Pengguna bebas beralih ke `Banner` jika mengunggah poster visual Canva yang teksnya tidak ingin ditimpa teks web.
- [x] Asset Vite terkompilasi bersih dan test suite lulus.

