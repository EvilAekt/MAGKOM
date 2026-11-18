# Plan: Fitur Edit Foto Hero Banner & Upload Panduan PDF pada Konfigurasi SuperAdmin

Dokumen ini merinci rencana implementasi penambahan fitur manajemen konten publik (ganti foto banner hero slider & upload berkas panduan PDF) langsung di dalam menu **Konfigurasi Sistem (Pengaturan)** di Dashboard SuperAdmin (`/superadmin/pengaturan`), dengan mengesampingkan Canvas Editor terlebih dahulu sesuai arahan pengguna.

---

## 1. Tujuan
1. **Ganti Foto Banner Hero Slider**:
   - SuperAdmin dapat mengunggah foto baru (JPEG, PNG, JPG, WebP) dari dashboard pengaturan.
   - Foto yang diunggah langsung menggantikan foto dokumentasi pada slide kedua carousel banner di Landing Page utama.
   - Pratinjau langsung (*live image preview*) sebelum dan sesudah disimpan.
   - Menyediakan tombol reset untuk kembali ke foto default bawaan Diskominfo Sleman (`hero_dokumentasi_diskominfo.png`).
   - Menjamin tata letak, grid, dan rasio tampilan Hero Slider di landing page tetap proporsional dan tidak rusak di perangkat desktop maupun mobile.

2. **Upload & Ganti Dokumen Panduan PDF**:
   - SuperAdmin dapat mengunggah berkas PDF petunjuk teknis/buku panduan pendaftaran baru.
   - Tombol "Unduh Panduan PDF" di bagian alur/tahapan pendaftaran Landing Page langsung mengunduh/membuka berkas PDF kustom yang diunggah.
   - Menampilkan status berkas aktif (nama file & ukuran berkas) serta tombol pratinjau unduh.
   - Menyediakan tombol reset untuk kembali ke berkas generator PDF otomatis bawaan sistem (`PanduanPdfController`).

3. **Penyelarasan Desain (DS-MagKom)**:
   - Antarmuka baru di halaman Pengaturan mengadopsi standar desain MagKom (Gentelella Card/Tile, Sleman Royal Blue `#1B3860`, font Plus Jakarta Sans & Open Sans, border radius 12px, token CSS yang ada).
   - Mengikuti aturan ketat repositori: tidak menulis CSS/JS inline di Blade, melainkan terstruktur di `resources/css/` dan `resources/js/`.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi Teknis**:
  - Penyimpanan berkas menggunakan storage disk `public` bawaan Laravel (`landing/hero/` untuk gambar dan `landing/panduan/` untuk dokumen PDF).
  - Batasan ukuran: Gambar maksimal 5MB (dimensi rekomendasi 1920x600 px), PDF maksimal 10MB.
  - Nilai konfigurasi disimpan pada model `Pengaturan` menggunakan kunci:
    - `landing_hero_image`: path berkas foto aktif di disk publik.
    - `landing_panduan_pdf`: path berkas PDF aktif di disk publik.
    - `landing_panduan_pdf_name`: nama asli berkas PDF saat diunggah untuk penamaan download.
  - Jika dokumen JSON `landing_page_document` tersimpan di database, nilai blok `hero` juga disinkronkan secara otomatis agar konsisten dengan `LegacyCmsAdapter`.
- **Pertanyaan Terbuka**: Tidak ada ambiguitas kritis. Seluruh kebutuhan bisnis dan teknis telah teridentifikasi dengan jelas.

---

## 3. Pendekatan Teknis

### Perbandingan Alternatif
- **Opsi A (Terpilih)**: Mengintegrasikan kartu baru *"Konten Publik Landing Page (Hero Banner & Panduan)"* ke dalam form utama `superadmin.pengaturan.index` dengan controller `PengaturanController`.
  - *Kelebihan*: Sesuai permintaan pengguna (*"di dalam konfigurasi di dashboard superadmin"*), satu halaman terpusat, alur simpan seragam, UX intuitif, memanfaatkan arsitektur `Pengaturan::get()` dan `PanduanPdfController` yang sudah ada.
  - *Kekurangan*: Memerlukan penambahan atribut `enctype="multipart/form-data"` pada form utama pengaturan dan penanganan input file pada controller.
- **Opsi B**: Membuat submenu/halaman terpisah di luar pengaturan.
  - *Alasan Ditolak*: Tidak efisien dan bertentangan dengan instruksi pengguna yang menginginkan fitur ini berada di dalam menu Konfigurasi Dashboard SuperAdmin.

### Arsitektur Alur Data
```mermaid
flowchart TD
    A[SuperAdmin Dashboard: Pengaturan] -->|Upload Foto / PDF| B[PengaturanController@update]
    B -->|Validasi File: Mime & Size| C[Storage Disk: public]
    C -->|Simpan path| D[Model Pengaturan & Document Sync]
    D -->|Audit Log| E[AuditService]
    D -->|landing_hero_image| F[Landing Page: Hero Banner Slide 2]
    D -->|landing_panduan_pdf| G[Landing Page: Tombol Unduh Panduan PDF]
```

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Controllers/SuperAdmin/PengaturanController.php` | Modify | Menambahkan validasi file `hero_image` & `panduan_pdf`, penyimpanan storage, penghapusan file lama, sinkronisasi kunci pengaturan, pemanggilan audit log, dan method reset. |
| `routes/web.php` | Modify | Menambahkan rute POST untuk reset foto hero banner dan reset panduan PDF pada grup pengaturan superadmin. |
| `resources/views/superadmin/pengaturan/index.blade.php` | Modify | Menambahkan `enctype="multipart/form-data"`, menambahkan Kartu 4 (Konten Publik: Hero Banner & Panduan PDF) lengkap dengan thumbnail preview, status berkas, dan tombol reset. |
| `resources/css/pages/superadmin/pengaturan.css` | Modify | Menambahkan kelas styling kartu upload konten publik, preview thumbnail, drag/drop container, badge status, dan tombol aksi sesuai DS-MagKom. |
| `resources/js/pages/superadmin/pengaturan.js` | Create | Skrip ES Module untuk client-side instant image preview, validasi ukuran file sebelum upload, dan penanganan UX interaktif. |
| `vite.config.js` | Modify | Mendaftarkan entry point `resources/js/pages/superadmin/pengaturan.js`. |
| `resources/views/public/blocks/hero.blade.php` | Verify/Modify | Memastikan styling container foto `.hero-full-img` memiliki `object-fit: cover` dan aspect ratio terkunci agar foto apa pun tidak merusak layout slider/grid. |
| `tests/Feature/SuperAdminLandingContentSettingsTest.php` | Create | Feature test komprehensif untuk pengujian upload foto, upload PDF, validasi tipe file, reset default, dan integritas respon landing page. |

---

## 5. Langkah Implementasi Bertahap

### Tahap 1: Backend & Controller Logic
- [ ] **1.1 Perbarui `PengaturanController.php`**:
  - Perbarui method `index()` untuk mengirim data:
    - Path foto hero aktif (`$heroImageUrl`) beserta flag apakah sedang menggunakan gambar kustom.
    - Status dokumen panduan PDF aktif (`$customPdfExists`, `$pdfFileName`, `$pdfFileSize`).
  - Perbarui method `update(Request $request)`:
    - Tambahkan aturan validasi file opsional:
      - `'hero_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120']` (5MB).
      - `'panduan_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240']` (10MB).
    - Proses penyimpanan berkas ke disk `public` (`landing/hero` dan `landing/panduan`).
    - Hapus berkas lama jika ada penggantian berkas baru.
    - Simpan path berkas ke `Pengaturan::set('landing_hero_image', ...)` dan `Pengaturan::set('landing_panduan_pdf', ...)`.
    - Sinkronkan blok `hero` pada `LandingPageDocumentService` (jika dokumen JSON aktif) sehingga tidak ada perbedaan data.
    - Catat aktivitas ke `AuditService` untuk kepatuhan SEC-03.
- [ ] **1.2 Tambahkan Method Reset di `PengaturanController.php` & Rute**:
  - Tambahkan method `resetHeroImage()`: menghapus foto kustom di storage, mengosongkan kunci `landing_hero_image`, dan mencatat audit log.
  - Tambahkan method `resetPanduanPdf()`: menghapus PDF kustom di storage, mengosongkan `landing_panduan_pdf` & `landing_panduan_pdf_name`, dan mencatat audit log.
  - Daftarkan rute di `routes/web.php`:
    - `POST /superadmin/pengaturan/reset-hero-image` (`superadmin.pengaturan.reset-hero`)
    - `POST /superadmin/pengaturan/reset-panduan-pdf` (`superadmin.pengaturan.reset-panduan`)

### Tahap 2: Antarmuka Dashboard SuperAdmin (Blade & Styling)
- [ ] **2.1 Modifikasi `resources/views/superadmin/pengaturan/index.blade.php`**:
  - Tambahkan atribut `enctype="multipart/form-data"` pada `<form id="formPengaturanSistem">`.
  - Tambahkan **KARTU 4: KONTEN PUBLIK LANDING PAGE (HERO BANNER & PANDUAN RESMI)**:
    - **Komponen 1: Hero Slider Banner Foto**:
      - Tampilan visual pratinjau (*live preview*) foto saat ini dengan rasio banner yang proporsional.
      - Input file foto baru dengan petunjuk rekomendasi dimensi (1920x600 px atau rasio lebar landscape, format PNG/JPG/WebP, maks 5MB).
      - Badge indikator: `Foto Default Diskominfo` atau `Foto Kustom Aktif`.
      - Tombol "Kembalikan Foto Default" (jika foto kustom sedang aktif).
    - **Komponen 2: Berkas Panduan Pendaftaran (PDF)**:
      - Informasi status berkas: Badge `Template PDF Otomatis Sistem` atau `Berkas Kustom Aktif`.
      - Tampilkan nama berkas, ukuran, dan tombol pratinjau unduh dokumen.
      - Input file dokumen PDF baru (maks 10MB).
      - Tombol "Kembalikan ke Generator PDF Sistem".
- [ ] **2.2 Tambahkan Styling di `resources/css/pages/superadmin/pengaturan.css`**:
  - Susun grid 2-kolom responsif untuk pengaturan konten publik.
  - Desain kartu pratinjau gambar dengan border halus, efek hover yang elegan, dan rasio aspek yang terkunci.
  - Desain box upload drag-and-drop mikro yang serasi dengan warna Sleman Royal Blue (`#1B3860`).
  - Penataan tombol reset sekunder yang aman dan rapi.
- [ ] **2.3 Buat Modul JavaScript `resources/js/pages/superadmin/pengaturan.js`**:
  - Implementasikan instant live preview saat user memilih gambar lokal sebelum menekan tombol simpan.
  - Validasi ukuran berkas di sisi klien (peringatan jika > 5MB untuk gambar atau > 10MB untuk PDF).
  - Daftarkan skrip ke `vite.config.js` dan sertakan di blade via `@vite(['resources/js/pages/superadmin/pengaturan.js'])`.

### Tahap 3: Proteksi Layout & Integrasi Landing Page
- [ ] **3.1 Periksa & Kunci Tata Letak Hero Slider (`hero.blade.php` & `landing.css`)**:
  - Pastikan elemen `.hero-full-img` memiliki `object-fit: cover; width: 100%; height: 100%;` dan `max-height: 100%`.
  - Pastikan `.hero-slider-track` dan `.hero-full-image-slide` menjaga dimensi absolut/relatif sehingga gambar dengan resolusi apa pun tidak akan memecahkan tinggi slider, tidak membuat layout meluap (*horizontal scrollbar*), dan slider navigasi tetap berfungsi sempurna.
- [ ] **3.2 Verifikasi Alur Unduh Dokumen Panduan**:
  - Pastikan rute `panduan.pdf` (`PanduanPdfController`) tetap menyajikan file PDF kustom secara *inline/attachment* dengan header HTTP yang aman, dan fallback otomatis ke DomPDF template jika berkas kustom dihapus/direset.

### Tahap 4: Pengujian & Quality Gate
- [ ] **4.1 Buat Automated Feature Test `SuperAdminLandingContentSettingsTest.php`**:
  - Test SuperAdmin berhasil mengunggah foto hero banner dan verifikasi file tersimpan di disk `public`.
  - Test validasi gagal jika mengunggah file non-gambar atau ukuran melebihi batas (5MB).
  - Test SuperAdmin berhasil mereset foto hero kembali ke default.
  - Test SuperAdmin berhasil mengunggah berkas PDF dan verifikasi file tersimpan di disk `public`.
  - Test validasi gagal jika mengunggah file non-PDF atau melebihi batas (10MB).
  - Test SuperAdmin berhasil mereset berkas panduan kembali ke generator PDF sistem.
  - Test endpoint landing page (`/`) dan endpoint download PDF (`/panduan-magang-pdf`) merespons dengan status `200 OK` dan menampilkan aset yang benar.
- [ ] **4.2 Eksekusi Quality Gate**:
  - Jalankan `php artisan test --filter=SuperAdminLandingContentSettingsTest`.
  - Jalankan test regresi pengaturan: `php artisan test --filter=SuperAdminBackendStabilityTest`.
  - Jalankan kompilasi frontend: `npm run build`.
  - Verifikasi browser langsung di `http://magkom.test` dan `http://magkom.test/superadmin/pengaturan`.

---

## 6. Strategi Testing

| Tipe Test | Kasus Uji | Ekspektasi |
| :--- | :--- | :--- |
| **Feature Test** | Upload foto banner valid (`.png`, 1200x400) | File tersimpan di `storage/landing/hero/`, status 302 redirect dengan pesan sukses, foto tampil di landing page. |
| **Feature Test** | Upload file foto palsu (file `.txt` atau `.exe` di-rename) | Validasi gagal 422/redirect dengan pesan error, file tidak tersimpan di storage. |
| **Feature Test** | Upload berkas PDF panduan valid | File tersimpan di `storage/landing/panduan/`, route `/panduan-magang-pdf` mengembalikan file yang diunggah. |
| **Feature Test** | Reset foto hero ke default | File kustom terhapus dari storage, landing page kembali menggunakan `hero_dokumentasi_diskominfo.png`. |
| **Feature Test** | Reset panduan PDF ke default | File kustom terhapus, route `/panduan-magang-pdf` memicu DomPDF template bawaan sistem. |
| **Visual & Layout Test** | Cek layout hero slider landing page dengan foto dimensi ekstrem (misal potret / super-lebar) | Gambar ter-crop rapi dengan `object-fit: cover`, tinggi slider tetap konsisten, grid dan kartu navigasi tidak bergeser. |

---

## 7. Risiko & Mitigasi

1. **Risiko Keamanan Upload Berkas (OWASP Top 10 - Unrestricted File Upload)**:
   - *Mitigasi*: Validasi ekstensi dan MIME type ketat (`mimes:jpeg,png,jpg,webp` dan `mimes:pdf`). Nama file di-hash secara acak (`Str::uuid()`) untuk mencegah penimpaan file sistem atau *directory traversal*. Simpan di direktori non-executable.
2. **Risiko Kerusakan Grid / Layout Landing Page**:
   - *Mitigasi*: Penggunaan aturan CSS `object-fit: cover; width: 100%; height: 100%;` pada container `.hero-full-image-slide` sehingga aspek rasio dan tinggi container hero slider tidak terpengaruh oleh rasio foto asli.
3. **Risiko Backward Compatibility Payload Form Pengaturan**:
   - *Mitigasi*: Parameter pengaturan yang sudah ada tetap dipertahankan dengan validasi yang sama. Input file baru dibuat `nullable` agar SuperAdmin tetap dapat memperbarui parameter operasional (kuota, SLA, dll.) tanpa wajib mengunggah file setiap saat.

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] Terdapat kartu baru di `/superadmin/pengaturan` khusus untuk pengelolaan Foto Hero Banner dan Panduan PDF.
- [ ] Unggah foto hero banner berhasil menggantikan gambar di slide 2 carousel landing page.
- [ ] Unggah berkas panduan PDF berhasil menggantikan file saat tombol "Unduh Panduan PDF" diklik di landing page.
- [ ] Tombol reset foto dan reset PDF berfungsi dengan sempurna mengembalikan ke kondisi bawaan.
- [ ] Grid, layout, dan slider landing page terbukti tidak rusak/pecah baik di mode desktop maupun mobile.
- [ ] Seluruh styling mematuhi Design System MagKom tanpa adanya inline CSS/JS di Blade.
- [ ] Semua automated test baru dan regression test lama lulus 100% (`php artisan test`).
- [ ] Aset frontend terkompilasi bersih dengan `npm run build`.

