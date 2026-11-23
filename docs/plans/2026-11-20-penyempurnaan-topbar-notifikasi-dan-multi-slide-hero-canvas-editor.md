# Rencana Kerja: Penyempurnaan Topbar, Notifikasi Mengambang, dan Multi-Slide Hero Banner Dinamis pada Canvas Editor

**Tanggal**: 20 November 2026  
**Status**: Selesai Diimplementasikan (Completed)  
**Dokumen Terkait**: `docs/01-PRD-MagKom.md`, `docs/02-TDD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `.agents/rules/AGENTS.md`  
**Area Terdampak**: SuperAdmin Site Editor (Topbar, Inspector, Floating Notifications), Hero Block Renderer, Landing Slider Interactive Engine, Canvas Runtime Bridge, Landing CMS Staging Service.

---

## 1. Tujuan & Sasaran Pengembangan

Berdasarkan 6 poin kebutuhan yang diajukan oleh pengguna beserta 5 tangkapan layar pendukung:
1. **Pembersihan Toolbar Kiri (SS 1)**: Menghapus tombol Undo, Redo, dan List View dari topbar header agar antarmuka lebih bersih dan fokus.
2. **Pembersihan Label Workspace (SS 2)**: Menghapus badge teks "Canvas Editor" dan garis pemisah breadcrumb slash di samping dropdown halaman.
3. **Mempertahankan Dropdown Halaman (SS 3)**: Memastikan dropdown pemilih halaman (`Landing Page (/)` dan `Kebijakan Privasi (/kebijakan-privasi)`) tetap aktif, terakses langsung di sebelah logo MAGKOM.
4. **Transformasi Status Simpan ke Notifikasi Mengambang (SS 4)**: Mengubah indikator status draf statis di header menjadi **Floating Top Toast / Notification Bar** yang muncul mengambang di bagian atas layar secara reaktif saat ada perubahan, proses simpan, atau draf berhasil disimpan.
5. **Multi-Slide Hero Banner Dinamis (SS 5)**: Memungkinkan SuperAdmin menambahkan slide banner sebanyak yang diinginkan (2, 3, 5, 10, atau lebih) secara dinamis, lengkap dengan gambar banner, label badge, judul keterangan, deskripsi keterangan, dan tombol aksi per slide.
6. **Ketentuan & Rekomendasi Ukuran Banner**: Menyediakan kartu panduan ketentuan resolusi, rasio aspek, dan area aman (*safe zone*) banner langsung di panel Inspector agar banner yang diunggah tidak terpotong di layar desktop maupun mobile.

---

## 2. Analisis Kebutuhan Berdasarkan Tangkapan Layar

| No | Referensi Gambar | Elemen Saat Ini | Kebutuhan Perubahan |
| :--- | :--- | :--- | :--- |
| 1 | **SS 1** (`media_1795171263292_a2857473.png`) | Tombol Undo (panah kiri), Redo (panah kanan), dan List View (ikon baris tiga) | **Dihapus** dari markup topbar header dan dinonaktifkan dari event listener terkait di JavaScript. |
| 2 | **SS 2** (`media_1795171327233_5b7973d7.png`) | Badge "Canvas Editor" dan separator breadcrumb `/` | **Dihapus** dari topbar header agar logo MAGKOM langsung berdampingan dengan pemilih halaman. |
| 3 | **SS 3** (`media_1795171399786_3ec031bc.png`) | Dropdown pemilih halaman (`/` dan `/kebijakan-privasi`) | **Dipertahankan 100%**, tetap berfungsi mulus untuk mengganti kanvas yang sedang diedit. |
| 4 | **SS 4** (`media_1795171436713_985071af.png`) | Pill status draf statis di header: `✓ Tersimpan (20 November 2026 17:40 WIB)` | **Diubah menjadi Notifikasi Mengambang di Atas (Top Floating Toast)**. Header menjadi bersih; notifikasi muncul mengambang saat ada perubahan, saat menyimpan, dan saat sukses tersimpan. |
| 5 | **SS 5** (`media_1795171587696_8852ea13.jpg`) | Slide hero banner saat ini hanya 2 slide statis (Info + 1 Foto) | **Multi-Slide Dinamis**: SuperAdmin dapat menambah slide semaunya (5, 10, dst.), mengatur gambar, judul, dan deskripsi keterangan per slide. |
| 6 | Poin 6 | Belum ada petunjuk ukuran banner di UI upload | **Ketentuan Ukuran Banner**: Ditambahkan kartu petunjuk rekomendasi dimensi (1920x600 px s/d 1920x720 px, rasio ~16:5 / 16:6), format, dan panduan area aman (*safe zone*). |

---

## 3. Arsitektur Teknis & Desain Solusi

### A. Desain Topbar Minimalis & Floating Top Toast
- **Markup Topbar (`_topbar.blade.php`)**:
  - Hapus elemen `.wp-workspace-badge`, `.wp-breadcrumb-slash`, dan `.wp-creation-tools` (tombol `#btn-undo`, `#btn-redo`, `#btn-list-view`).
  - Hapus elemen statis `.wp-status-container` dari baris header kanan.
  - Komposisi Topbar baru:
    - **Sisi Kiri**: `[Tombol Kembali] [Logo MAGKOM Magang Sleman] [Dropdown Pemilih Halaman]`.
    - **Sisi Tengah**: `[Viewport Switcher: Desktop | Tablet | Mobile]`.
    - **Sisi Kanan**: `[Pratinjau] [Simpan Draf] [Terbitkan] [Rollback] [Toggle Inspector]`.
- **Floating Top Notification (`#wp-top-notification-bar`)**:
  - Ditempatkan mengambang di atas tengah layar (`position: fixed; top: 16px; left: 50%; transform: translateX(-50%); z-index: 100;`).
  - Status Perubahan:
    - **Dirty / Ada Perubahan**: `⚠️ Perubahan Terdeteksi (Draf Belum Disimpan)` (kuning-oranye hangat, tetap terlihat selama ada perubahan yang belum disimpan).
    - **Saving / Sedang Menyimpan**: `⏳ Menyimpan Draf...` (biru netral dengan spinner halus).
    - **Saved / Tersimpan**: `✓ Tersimpan (<Waktu>)` (hijau emerald, otomatis memudar halus setelah 3.5 detik).
    - **Error**: `❌ Gagal Menyimpan Draf` (merah).

### B. Arsitektur Multi-Slide Hero Banner Dinamis
- **Struktur Data JSON (Array `slides`) pada Blok Hero**:
  ```json
  {
    "type": "hero",
    "content": {
      "title": "Program Magang...",
      "slides": [
        {
          "id": "slide-1",
          "image": "landing/media/foto1.jpg",
          "badge": "DOKUMENTASI RESMI",
          "title": "Magang Diskominfo Sleman",
          "description": "Pengembangan ekosistem digital SPBE, infrastruktur jaringan, dan talenta muda...",
          "cta_text": "Daftar Sekarang",
          "cta_url": "#kuota"
        },
        {
          "id": "slide-2",
          "image": "landing/media/foto2.jpg",
          "badge": "KEGIATAN DISKOMINFO",
          "title": "Workshop & Pembekalan Teknis",
          "description": "Bimbingan langsung dari mentor ASN profesional mengenai tata kelola data...",
          "cta_text": "Pelajari Lebih Lanjut",
          "cta_url": "#tahapan"
        }
      ]
    }
  }
  ```
- **Fallback Backward Compatibility**:
  Jika data lama belum memiliki array `slides`, sistem secara otomatis mengonversi `image` lama menjadi slide pertama dalam daftar `slides`, sehingga gambar banner eksisting tidak pernah hilang.
- **Antarmuka Inspector (`_inspector.blade.php` & `inspector.js`)**:
  - **Petunjuk Rekomendasi Banner**:
    - Dimensi: `1920 × 600 px` s/d `1920 × 720 px` (Landscape, rasio ~16:5 atau minimal 1200 × 500 px).
    - Format: JPG, PNG, WebP (maksimal 2MB).
    - Safe Zone: Obyek dan teks penting berada di tengah agar tidak terpotong di layar mobile/tablet.
  - **Repeater Slide**:
    - Tombol `+ Tambah Slide Banner`.
    - Kartu per slide yang memuat:
      - Thumbnail gambar + tombol "Pilih / Ganti Gambar" (terhubung dengan Media Library modal).
      - Input "Badge Label" (opsional, misal: DOKUMENTASI RESMI).
      - Input "Judul Slide" (wajib).
      - Textarea "Deskripsi / Keterangan Slide" (wajib).
      - Input "Teks Tombol CTA" & "Tautan CTA" (opsional).
      - Tombol "Hapus Slide" (ikon tempat sampah, dinonaktifkan jika hanya ada 1 slide).
- **Renderer Hero Publik & Kanvas (`hero.blade.php`, `landing.js`, `landing-canvas-runtime.js`)**:
  - Di Desktop: Render Slide 1 (Box 3 Kolom Pendaftaran) + Slide 2..N (Masing-masing slide banner).
  - Di Mobile: Render Slide 1..3 (Pendaftaran, Syarat, Dokumen) + Slide 4..N (Masing-masing slide banner).
  - Dots indikator di bawah slider otomatis menyesuaikan jumlah total slide:
    - Desktop: `1 + N` dots.
    - Mobile: `3 + N` dots.
  - Slider controller di `landing.js` sudah mendukung jumlah slide dinamis tanpa batas (`track.querySelectorAll(...)`).

---

## 4. Daftar File yang Akan Diubah

| No | File | Jenis Perubahan | Alasan Perubahan |
| :--- | :--- | :--- | :--- |
| 1 | `resources/views/superadmin/site-editor/partials/_topbar.blade.php` | Modify | Menghapus tombol Undo/Redo/List View, menghapus badge "Canvas Editor" & slash, mempertahankan dropdown halaman, dan mengganti pill status statis dengan notifikasi mengambang di atas. |
| 2 | `resources/views/superadmin/site-editor/index.blade.php` | Modify | Menambahkan kontainer notifikasi mengambang `#wp-top-notification-bar` di bagian atas kanvas. |
| 3 | `resources/views/superadmin/site-editor/partials/_inspector.blade.php` | Modify | Menambahkan kartu ketentuan ukuran banner dan UI repeater untuk mengelola multi-slide banner hero (tambah, hapus, ganti gambar, edit keterangan). |
| 4 | `resources/views/public/blocks/hero.blade.php` | Modify | Merender multi-slide banner dan dots indikator secara dinamis berdasarkan array `slides`, dengan fallback backward compatibility. |
| 5 | `resources/js/pages/superadmin/site-editor/editor-app.js` | Modify | Menghapus binding event untuk tombol yang dihapus (undo/redo/list-view) dan mengintegrasikan floating top notification. |
| 6 | `resources/js/pages/superadmin/site-editor/editor-state.js` | Modify | Menyesuaikan dispatch status perubahan ke Floating Top Notification Bar (dirty, saving, saved, error). |
| 7 | `resources/js/pages/superadmin/site-editor/inspector.js` | Modify | Mengelola state repeater multi-slide hero, penambahan slide baru, pemilihan media per slide, penghapusan slide, dan sinkronisasi ke kanvas via postMessage. |
| 8 | `resources/js/pages/public/landing-canvas-runtime.js` | Modify | Menangani pesan kanvas `WP_UPDATE_HERO_SLIDES` untuk memperbarui DOM slide dan dots slider di iframe kanvas secara instan. |
| 9 | `resources/css/pages/superadmin/site-editor.css` | Modify | Styling untuk Floating Top Notification Bar, kartu ketentuan ukuran banner, dan kartu repeater slide di Inspector. |
| 10 | `app/Http/Requests/SuperAdmin/SaveLandingSiteEditorRequest.php` | Modify | Menambahkan validasi array `landing_hero_slides` dan atributnya (`image`, `badge`, `title`, `description`, `cta_text`, `cta_url`). |
| 11 | `app/Services/LandingCmsStagingService.php` | Modify | Menangani sinkronisasi array `slides` ke blok hero dalam dokumen draf dan penerbitan live. |
| 12 | `tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php` | Modify | Menambahkan test case untuk verifikasi topbar bersih, floating notification, dan alur simpan/terbit multi-slide hero banner. |

---

## 5. Rincian Langkah Implementasi Bertahap

### Tahap 1: Pembersihan Topbar & Integrasi Floating Top Notification
1. Edit `resources/views/superadmin/site-editor/partials/_topbar.blade.php`:
   - Hapus `.wp-workspace-badge` dan `.wp-breadcrumb-slash`.
   - Hapus `.wp-creation-tools` (tombol Undo, Redo, List View).
   - Hapus `.wp-status-container` statis di kanan.
   - Pertahankan dropdown `#select-canvas-page` di sebelah logo MAGKOM.
2. Edit `resources/views/superadmin/site-editor/index.blade.php`:
   - Tempatkan komponen `#wp-top-notification-bar` di bagian atas kanvas.
3. Edit `resources/css/pages/superadmin/site-editor.css`:
   - Buat class `.wp-top-notification-bar` dengan posisi `fixed`, animasi slide-down halus, ikon status, dan varian warna (warning/dirty, info/saving, success/saved, danger/error).
4. Update `editor-state.js` & `editor-app.js`:
   - Arahkan update status dirty/saving/saved langsung ke `#wp-top-notification-bar`.

### Tahap 2: Komponen Ketentuan Ukuran Banner & UI Multi-Slide di Inspector
1. Edit `resources/views/superadmin/site-editor/partials/_inspector.blade.php`:
   - Tambahkan box informasi **Ketentuan Ukuran Hero Banner** dengan rincian:
     - Dimensi: 1920x600 px s/d 1920x720 px (rasio ~16:5 atau min 1200x500 px).
     - Format: JPG, PNG, WebP (Maks. 2MB).
     - Safe Zone: Obyek utama di tengah horizontal dan vertikal.
   - Tambahkan kontainer repeater `#wp-hero-slides-container` dan tombol `#btn-add-hero-slide`.
2. Edit `resources/js/pages/superadmin/site-editor/inspector.js`:
   - Inisialisasi daftar slide dari `heroBlock.content.slides` (atau fallback dari single image).
   - Buat fungsi render dinamis kartu-kartu slide.
   - Hubungkan setiap tombol "Pilih Gambar" pada kartu slide dengan `MediaLibrary` modal.
   - Buat fungsi tambah slide baru dan hapus slide.
   - Kirim event postMessage `WP_UPDATE_HERO_SLIDES` ke iframe kanvas setiap kali ada mutasi slide.

### Tahap 3: Reaktivitas Kanvas & Rendering Multi-Slide Publik
1. Edit `resources/views/public/blocks/hero.blade.php`:
   - Loop array `$slides` untuk desktop slides (`.hero-slide-desktop`) dan mobile photo slides (`.hero-slide-mobile`).
   - Render dots navigasi desktop dan mobile secara dinamis berdasarkan jumlah slide aktual.
2. Edit `resources/js/pages/public/landing-canvas-runtime.js`:
   - Tangani aksi `WP_UPDATE_HERO_SLIDES`: update HTML track slider di kanvas dan render ulang dots, lalu panggil ulang inisialisasi slider.

### Tahap 4: Backend Synchronization & Validasi Form
1. Edit `app/Http/Requests/SuperAdmin/SaveLandingSiteEditorRequest.php`:
   - Daftarkan validasi untuk `landing_hero_slides` (array) dan sub-fieldnya.
2. Edit `app/Services/LandingCmsStagingService.php`:
   - Tangani penyimpanan draf array `slides` ke dalam blok hero di `draft_landing_page_document`.
   - Pada saat `publishDraft()`, sinkronkan array `slides` ke `landing_page_document`.

### Tahap 5: Quality Gate & Pengujian Menyeluruh (Zero-Bug Policy)
1. **Asset Build**: Jalankan `npm run build` dan pastikan tidak ada error kompilasi Vite.
2. **Code Style**: Jalankan `./vendor/bin/pint --test` untuk memastikan standar PSR-12.
3. **Static Analysis**: Jalankan `./vendor/bin/phpstan analyse --memory-limit=1G` untuk memastikan type safety.
4. **Feature Testing**: Jalankan `php artisan test tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php` dan pastikan seluruh skenario baru maupun lama lulus hijau (100% PASS).
5. **Verifikasi Visual**:
   - Topbar bersih: Tidak ada tombol undo/redo/list view, tidak ada teks "Canvas Editor", dropdown halaman tetap ada.
   - Notifikasi mengambang: Mengambang di atas saat ada perubahan draf dan saat tersimpan.
   - Multi-slide hero: Tambah slide 3, 5, atau 10, ubah keterangan judul dan deskripsi, cek kanvas live, simpan draf, dan terbitkan ke live.
   - Ketentuan ukuran: Terbaca jelas dan informatif di panel Inspector.

---

## 6. Mitigasi Risiko & Backward Compatibility

1. **Kompatibilitas Data Lama**:
   - Jika dokumen landing page yang ada di database belum memiliki field `slides`, sistem secara otomatis membungkus `landing_hero_image` dan teks dokumentasi eksisting ke dalam slide pertama. Tidak ada gambar banner yang hilang atau rusak.
2. **Keamanan Media & Validasi**:
   - Gambar yang dipilih tetap melalui Media Library yang telah divalidasi MIME type dan aman dari SVG berbahaya / path traversal.
3. **Responsivitas Layar (Desktop vs Mobile)**:
   - Slider di desktop menampilkan kartu 3-kolom diikuti oleh N slide banner.
   - Slider di mobile menampilkan 3 kartu awal (Pendaftaran, Syarat, Dokumen) diikuti oleh N slide foto banner dengan caption yang mudah dibaca di layar sentuh.
4. **Zero Script / Style di Blade**:
   - Seluruh logika interaksi tetap menggunakan Vanilla ES Modules di `resources/js/` dan styling di `resources/css/site-editor.css`. Markup Blade hanya berisi elemen presentasional semantik.

---

## 7. Checklist Implementasi

- [x] **Pembersihan Topbar**: Hapus tombol Undo, Redo, List View, dan teks "Canvas Editor" di `_topbar.blade.php`.
- [x] **Pertahankan Dropdown**: Pastikan dropdown pemilih halaman tetap tampil di sebelah logo MAGKOM.
- [x] **Floating Notification**: Implementasikan kontainer notifikasi mengambang di atas di `index.blade.php` & `site-editor.css`.
- [x] **Hubungkan State Status**: Sambungkan `editor-state.js` ke floating notification bar untuk status dirty, saving, dan saved.
- [x] **Petunjuk Ukuran Banner**: Buat card petunjuk ketentuan ukuran banner (1920x600 px s/d 1920x720 px, format, safe zone) di `_inspector.blade.php`.
- [x] **Repeater Multi-Slide di Inspector**: Buat UI tambah/hapus slide dan edit keterangan judul/deskripsi di `_inspector.blade.php` & `inspector.js`.
- [x] **Media Picker Integrasi**: Hubungkan tombol pilih gambar tiap slide dengan `MediaLibrary` modal.
- [x] **Renderer Hero Dinamis**: Perbarui `hero.blade.php` untuk merender N slide banner dan dots dinamis di desktop & mobile.
- [x] **Live Kanvas PostMessage**: Implementasikan `WP_UPDATE_HERO_SLIDES` di `landing-canvas-runtime.js`.
- [x] **Backend Staging & Validasi**: Perbarui `SaveLandingSiteEditorRequest.php` dan `LandingCmsStagingService.php`.
- [x] **Feature Tests**: Tambahkan unit & feature test di `LandingCmsSiteEditorTest.php`.
- [x] **Quality Gate**: Jalankan `npm run build`, `phpstan`, `pint`, dan `artisan test` hingga 100% lulus bersih.

