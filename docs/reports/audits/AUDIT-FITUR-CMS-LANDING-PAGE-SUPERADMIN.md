# AUDIT LENGKAP FITUR KELOLA & UBAH TAMPILAN LANDING PAGE
## Modul SuperAdmin Portal Magang Diskominfo Sleman (MAGKOM)
*Dokumen Konsolidasi Arsitektur, Fitur, Kontrol Pixel, dan Staging Sandbox*

---

**Tanggal Audit:** 7 November 2026  
**Klasifikasi:** Internal System Audit & Developer Specification  
**Status Sistem:** Production Ready (PHP 8.3 / Laravel 10 / Vite Asset Pipeline)  
**Tingkat Akses:** Khusus Hak Akses `superadmin` (Role-Based Access Control)  

---

## 1. RINGKASAN EKSEKUTIF

Portal MAGKOM menyediakan **3 Lapisan Arsitektur Pengelolaan Landing Page Publik** yang memungkinkan SuperAdmin mengubah seluruh konten, struktur alur, foto/dokumen, palet warna, tipografi, hingga kontrol dimensi pixel tanpa perlu menyentuh kode program (*zero hardcoded content*):

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        SUPERADMIN CMS & LANDING PAGE ENGINE                            │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 1. WORDPRESS SITE EDITOR          │ 2. CMS FORM BUILDERS         │ 3. THEME ENGINE     │
│    (/superadmin/site-editor)      │    (/superadmin/cms-landing) │    (Token Styling)  │
│    • Full-Screen Gutenberg Canvas │    • 8 Tab Terstruktur       │    • 5 Color Pickers│
│    • Inline Text Click-to-Edit    │    • Dynamic Repeaters (No   │    • Google Fonts   │
│    • Kontrol Presisi Pixel (px)   │      JSON Textarea)          │    • Border Radius  │
│    • Viewport Desktop/Tablet/HP   │    • Media Banner & PDF Upload│    • Button Styles │
├───────────────────────────────────┴──────────────────────────────┴─────────────────────┤
│                                  ▼                                                     │
│                    4. STAGING SANDBOX & DRAFT ENGINE                                   │
│                       • Penyimpanan Draf Sementara (draft_landing_*)                   │
│                       • Pratinjau Interaktif Publik (Live Preview)                     │
│                       • Satu Klik Publikasi ke Publik / Buang Draf                     │
│                       • Otomatisasi Audit Trail (Tabel audit_logs)                     │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. MODUL 1: WORDPRESS.ORG STYLE SITE EDITOR (`/superadmin/site-editor`)
*Fitur Visual Canvas & Right Inspector Terintegrasi*

Fitur ini mengadopsi standar antarmuka Gutenberg / Full Site Editor (FSE) WordPress.org.

### 2.1 Top Bar Editor Khas WP-Admin
- **Tombol Navigasi Keluar**: Kembali ke menu CMS Landing Page reguler di dashboard SuperAdmin.
- **Logo Sleman**: Identitas resmi portal Pemkab Sleman.
- **Tombol Block Inserter `[ + ]`**: Membuka laci drawer samping kiri berisi pohon daftar blok untuk melompat ke bagian tertentu.
- **Tombol Riwayat (Undo `↶` & Redo `↷`)**: Membatalkan atau mengulangi perubahan di memori lokal editor.
- **Tombol List View `[ ☰ ]`**: Menampilkan daftar blok berurutan (Hero, Kuota, Tahapan, FAQ, Footer).
- **Responsive Viewport Switcher**:
  - **Desktop (100%)**: Lebar kanvas penuh hingga maksimal 1440px.
  - **Tablet (768px)**: Kanvas diubah menjadi bingkai tablet dengan border 8px abu-abu gelap.
  - **Mobile (375px)**: Kanvas diubah menjadi bingkai smartphone 375px x 740px untuk menguji responsivitas layar HP.
- **Status Draf Real-Time (Badge Pill)**:
  - `"Draf Sandbox Aktif"` (Kuning/Emas) jika ada draf yang belum terbit.
  - `"Tersinkronisasi"` (Hijau) jika semua perubahan telah tersimpan.
  - `"Perubahan Belum Disimpan"` saat admin menggeser slider atau mengetik.
- **Tombol Simpan Draf**: Menyimpan perubahan via AJAX JSON tanpa memuat ulang kanvas (dapat dipicu lewat shortcut `Ctrl + S`).
- **Tombol Terbitkan (*Publish*)**: Tombol primer warna biru khas WordPress untuk merilis perubahan ke pengunjung publik secara instan.
- **Tombol Pengaturan Inspector `[ ⚙️ ]`**: Menyembunyikan / menampilkan panel kontrol sisi kanan.

### 2.2 Interactive WYSIWYG Canvas (Tengah)
- **Kanvas Iframe Sandbox**: Memuat halaman landing aktual dengan parameter `?editor_mode=1`.
- **Seleksi Visual Blok**: Setiap bagian memiliki penanda outline biru (`#007CBA`) saat diarahkan (*hover*) dan outline tebal saat diklik (*selected*).
- **Chip Label Blok**: Muncul badge kecil di sudut kiri atas blok terpilih (misal: `[ Blok: Hero Banner ]`, `[ Blok: FAQ Accordion ]`).
- **Inline Text Editing (*ContentEditable*)**: SuperAdmin dapat mengklik langsung teks Kicker atau Judul Hero di atas kanvas dan mengetiknya seperti di Word/Google Docs. Perubahan langsung tersinkronisasi ke state editor.
- **Floating Block Toolbar**: Toolbar melayang yang muncul di atas blok terpilih:
  - Indikator nama blok.
  - Tombol pindah urutan (*Move Up* / *Move Down*).
  - Pilihan perataan teks (*Align Left* / *Align Center*).
  - Tombol reset gaya blok ke bawaan sistem.

### 2.3 Right Block Inspector Panel (Panel Kanan)
Memiliki dua tab utama sesuai pola WordPress.org:
1. **Tab [Halaman]**:
   - Pengaturan Brand Name Navbar.
   - Subjudul Brand.
   - Alamat Kantor Dinas, Email Helpdesk, dan Teks Hak Cipta (*Copyright*).
2. **Tab [Blok] dengan Kontrol Dimensi Pixel Eksplisit**:
   - **Tipografi**:
     - *Ukuran Font Judul Hero*: Slider + input angka (`24px` s/d `72px`).
     - *Ukuran Font Subtitle / Deskripsi*: Slider + input angka (`12px` s/d `24px`).
     - *Ukuran Font FAQ*: Slider + input angka (`14px` s/d `24px`).
     - *Pilihan Font Heading Google Fonts*: Lexend, Plus Jakarta Sans, Inter, Montserrat, Poppins.
   - **Dimensi & Spasi (Pixel)**:
     - *Padding Atas-Bawah Hero*: Slider vertikal (`20px` s/d `200px`).
     - *Lebar Maksimum Kontainer*: Slider (`960px` s/d `1600px`).
     - *Jarak Antar Bagian (Section Gap)*: Slider (`24px` s/d `140px`).
     - *Padding Atas-Bawah Footer*: Slider (`32px` s/d `140px`).
   - **Batas & Sudut (Border Radius)**:
     - *Radius Sudut Kartu (Border Radius)*: Slider (`0px` s/d `32px`).
     - *Padding Dalam Kartu*: Slider (`12px` s/d `48px`).
   - **Palet Warna Tampilan**:
     - Color picker Warna Primer (Navy Sleman `#1B3860`).
     - Color picker Warna Aksen Tombol (Indigo `#4F46E5`).
     - Color picker Warna Sorot / Badge (Emas `#F59E0B`).

### 2.4 Zero-Latency Live Synchronization
- Menggunakan protokol `window.postMessage` dua arah antara jendela editor dan iframe kanvas.
- Nilai slider pixel langsung menyuntikkan CSS Custom Properties (`--wp-*`) ke `:root` kanvas secara instan (0 milidetik latency) tanpa mereload halaman iframe.

---

## 3. MODUL 2: CMS FORM BUILDERS REGULER (`/superadmin/cms-landing`)
*Pengelolaan Konten Detail Berbasis Tab & Visual Repeater (Zero JSON Raw Textarea)*

Antarmuka ini terbagi dalam 8 Tab terstruktur yang menghilangkan format textarea JSON mentah dan menggantinya dengan komponen formulir visual:

### 3.1 Tab 1: Header Brand & SEO Meta
- **Meta Title & Meta Description**: Kontrol tag SEO mesin pencari dan pratinjau tautan media sosial (*Open Graph*).
- **Brand Identity**: Nama brand navbar (`MAGKOM`) dan sub-brand (`Portal Magang · Diskominfo Sleman`).
- **Visual Nav Repeater**: Tabel daftar menu navbar dengan tombol tambah baris, input label, target URL/anchor (`#beranda`, `#kuota`, `#tahapan`, `#faq`), dan tombol hapus baris.

### 3.2 Tab 2: Hero & Media Banner
- **Teks Promosi**: Kicker lokasi (`Kabupaten Sleman`), Judul Hero Utama, Tahun Periode (`2026`), Info rentang tanggal pendaftaran, dan Teks tombol CTA (`Daftar Sekarang`).
- **Persyaratan & Berkas Pendaftaran**:
  - Label dan daftar poin persyaratan umum magang (multi-line).
  - Label dan daftar dokumen wajib (Surat Pengantar, CV, Transkrip Nilai, Pas Foto).
- **Pengelolaan Media Foto Banner**:
  - Unggah foto kustom banner (format JPG, JPEG, PNG, WEBP, ukuran maksimal 2 MB).
  - Pratinjau thumbnail foto aktif.
  - Tombol **"Reset ke Foto Default"** untuk mengembalikan ke ilustrasi resmi dinas.
- **Pengelolaan Dokumen Panduan PDF**:
  - Unggah buku panduan PDF resmi (maksimal 5 MB).
  - Penamaan otomatis file PDF aman (*safe sanitized name*).
  - Tombol **"Reset ke PDF Bawaan"** untuk mengaktifkan kembali generator PDF dinamis sistem.

### 3.3 Tab 3: Kuota & Formasi Copy
- **Judul Bagian**: Heading dan Subheading bagian kuota.
- **Pertanyaan Alur Filter**:
  - Pertanyaan 1: Label identitas calon peserta (Pill SMK vs Mahasiswa).
  - Pertanyaan 2: Label pemilihan bulan pelaksanaan magang.
  - Pertanyaan 3: Label tipe pendaftaran (Individu 1 Orang vs Kelompok/Tim).
- **Petunjuk Tim**: Label jumlah anggota kelompok dan catatan pendaftaran ketua tim.
- **Status Kuota & Tombol Aksi**:
  - Label legenda ketersediaan: *Masih banyak* (Hijau), *Tinggal sedikit* (Kuning), *Penuh/Waiting List* (Abu-abu).
  - Teks tombol: *Daftar Sekarang*, *Daftar Waiting List*, dan *Pendaftaran Ditutup*.

### 3.4 Tab 4: Alur & Tahapan Pendaftaran (Roadmap)
- **Heading & Ringkasan Alur**: Deskripsi narasi alur pendaftaran 4 langkah.
- **4 Checkpoint Alur Magang Interaktif**:
  - *Tahap 1*: Registrasi Akun (Judul, Badge, Deskripsi).
  - *Tahap 2*: Administrasi & Seleksi Berkas (Judul, Badge, Deskripsi).
  - *Tahap 3*: Pelaksanaan Magang & Logbook (Judul, Badge, Deskripsi).
  - *Tahap 4*: Kelulusan & Unduh Nilai/Sertifikat (Judul, Badge, Deskripsi).
- **Tombol Panduan & Peta**: Kustomisasi label tombol *Unduh Panduan PDF* dan tombol *Peta Alur Interaktif*.

### 3.5 Tab 5: FAQ (Tanya Jawab) — Visual Nested Accordion
- **Heading FAQ**: Judul utama bagian FAQ.
- **Visual Category & Q/A Repeater**:
  - Tambah / Hapus Kategori FAQ (misal: Syarat, Akun, Kuis Teknis, Fasilitas, Kewajiban).
  - Di dalam setiap kategori: Tambah / Hapus butir Pertanyaan (`q`) dan Jawaban (`a`).
  - Mengeliminasi risiko syntax error JSON kurung kurawal atau koma.

### 3.6 Tab 6: Footer & Kontak Dinas
- **Informasi Kedinasan**: Alamat kantor lengkap Pemkab Sleman, nomor telepon kantor, email resmi kedinasan, jam layanan kerja ASN (Senin–Jumat), dan teks hak cipta.
- **Visual Repeaters**:
  - *Media Sosial*: Platform, label username, dan URL profile (Instagram, Twitter/X, dll).
  - *Tautan Terkait*: Label link dan target URL (Kebijakan Privasi, Portal Pemkab, Verifikasi Dokumen).
  - *Tautan Akun*: Label link akses masuk peserta, pembimbing, dan admin.

### 3.7 Tab 7: Kebijakan Privasi & UU PDP No. 27/2022
- **Metadata Regulasi**: Badge kepatuhan hukum, judul kebijakan, dan tanggal pembaruan resmi.
- **Visual Section Builder (Pasal-Pasal)**:
  - Nomor urut pasal, judul pasal, dan isi penjelasan.
  - Repeater butir-butir poin hak akses data dan retensi arsip.

### 3.8 Tab 8: Tema & Desain Visual (Theming Engine)
- **Palet Warna Utama**: Navy Primer (`#1B3860`), Dark Navy (`#122846`), Indigo Aksen (`#4F46E5`), Emas Sorot (`#F59E0B`), dan Background (`#FFFFFF`).
- **Tipografi Google Fonts**:
  - Font Display / Heading: *Lexend, Plus Jakarta Sans, Inter, Montserrat, Poppins*.
  - Font Body: *Source Sans 3, Inter, Roboto, Open Sans, Nunito Sans*.
- **Bentuk Geometri Komponen**:
  - Pilihan Radius Sudut: `0px` (Tajam/Sharp), `6px` (Halus), `12px` (Standar), `16px` (Modern), `24px` (Ekstra Lengkung).
  - Gaya Tombol: *Rounded*, *Pill* (Kapsul), atau *Sharp* (Kotak Tegas).

---

## 4. MODUL 3: STAGING SANDBOX & DRAFT ENGINE (`LandingCmsStagingService`)
*Isolasi Perubahan & Pencegahan Kesalahan Publikasi Langsung*

Sistem ini memastikan tidak ada perubahan tampilan atau konten yang langsung tayang ke publik sebelum diperiksa oleh SuperAdmin:

### 4.1 Mekanisme Kunci Isolasi Basis Data
- Data live disimpan pada kunci tabel `pengaturan` dengan nama biasa: `landing_*`.
- Data draf disimpan dengan awalan: `draft_landing_*`.
- Saat pengunjung umum membuka halaman depan (`/`), sistem membaca `landing_*`.
- Saat SuperAdmin membuka mode pratinjau (`/superadmin/cms-landing/preview` atau `?editor_mode=1`), sistem membaca `draft_landing_*`.

### 4.2 Siklus Hidup Perubahan (Lifecycle)
1. **Penyimpanan Draf (*Save Draft*)**:
   - Dapat dilakukan dari CMS Form biasa atau dari WordPress Site Editor via AJAX.
   - Status draf menjadi aktif (`landing_draft_active = 1`).
   - Menyimpan cap waktu pengubahan (`landing_draft_updated_at`) dan nama SuperAdmin pembuat draf (`landing_draft_author_name`).
2. **Pratinjau Interaktif (*Interactive Live Preview*)**:
   - URL: `/superadmin/cms-landing/preview`
   - Memuat landing page penuh dengan data draf aktif.
   - Dilengkapi **Floating Control Bar** di bagian atas layar:
     - Badge penanda *"MODE PRATINJAU INTERAKTIF"*.
     - Tombol *Kembali ke Editor*.
     - Tombol *Buang Draf*.
     - Tombol *Publikasikan ke Publik*.
3. **Mempublikasikan ke Publik (*Publish Draft*)**:
   - Mengambil seluruh rekaman `draft_landing_*` di database.
   - Menyalin nilainya ke kunci live `landing_*`.
   - Memindahkan berkas foto banner dan panduan PDF sementara ke direktori publik permanen serta menghapus berkas lama.
   - Menghapus kunci draf dan menonaktifkan status draf.
   - Membersihkan cache aplikasi (`Pengaturan::clearCache()`).
4. **Membuang Draf (*Discard Draft*)**:
   - Menghapus seluruh rekaman `draft_landing_*`.
   - Menghapus berkas foto/PDF sementara di storage.
   - Mengembalikan editor ke kondisi live yang sedang aktif saat ini.

### 4.3 Audit Trail Otomatis (`AuditService`)
Setiap kali SuperAdmin melakukan aksi terkait landing page, sistem secara otomatis mencatat ke tabel `audit_logs`:
- Jenis Aksi: `TipeAuditEnum::Aksi`
- Tabel Terkait: `pengaturan`
- Detail Audit: ID SuperAdmin, alamat IP, user-agent, jenis section yang diubah, jumlah kunci yang diperbarui, dan aksi (Draf / Publikasi / Buang Draf / Reset Berkas).

---

## 5. MATRIKS KAMUS DATA TOKEN PENGATURAN LANDING PAGE

Berikut adalah daftar lengkap kunci pengaturan pada tabel `pengaturan`:

| Kategori | Kunci Live di Basis Data | Kunci Draf di Sandbox | Tipe / Satuan | Keterangan & Batasan |
| :--- | :--- | :--- | :--- | :--- |
| **Dimensi Pixel** | `landing_container_max_width_px` | `draft_landing_container_max_width_px` | Integer (`px`) | Lebar kontainer (960 - 1600 px) |
| **Dimensi Pixel** | `landing_hero_title_size_px` | `draft_landing_hero_title_size_px` | Integer (`px`) | Ukuran font judul hero (24 - 72 px) |
| **Dimensi Pixel** | `landing_hero_desc_size_px` | `draft_landing_hero_desc_size_px` | Integer (`px`) | Ukuran font subtitle hero (12 - 24 px) |
| **Dimensi Pixel** | `landing_hero_padding_y_px` | `draft_landing_hero_padding_y_px` | Integer (`px`) | Padding vertikal hero (20 - 200 px) |
| **Dimensi Pixel** | `landing_hero_image_height_px` | `draft_landing_hero_image_height_px` | Integer (`px`) | Tinggi foto banner (250 - 700 px) |
| **Dimensi Pixel** | `landing_section_gap_px` | `draft_landing_section_gap_px` | Integer (`px`) | Jarak vertikal antar section (24 - 140 px) |
| **Dimensi Pixel** | `landing_card_radius_px` | `draft_landing_card_radius_px` | Integer (`px`) | Kelengkungan sudut kartu (0 - 32 px) |
| **Dimensi Pixel** | `landing_card_padding_px` | `draft_landing_card_padding_px` | Integer (`px`) | Bantalan dalam kartu (12 - 48 px) |
| **Dimensi Pixel** | `landing_faq_title_size_px` | `draft_landing_faq_title_size_px` | Integer (`px`) | Ukuran font pertanyaan FAQ (14 - 24 px) |
| **Dimensi Pixel** | `landing_footer_padding_y_px` | `draft_landing_footer_padding_y_px` | Integer (`px`) | Padding vertikal footer (32 - 140 px) |
| **Tema Warna** | `landing_theme_primary_color` | `draft_landing_theme_primary_color` | Hex Color | Warna primer dinas (#1B3860) |
| **Tema Warna** | `landing_theme_navy_dark` | `draft_landing_theme_navy_dark` | Hex Color | Warna gelap (#122846) |
| **Tema Warna** | `landing_theme_accent_color` | `draft_landing_theme_accent_color` | Hex Color | Warna aksen (#4F46E5) |
| **Tema Warna** | `landing_theme_gold_color` | `draft_landing_theme_gold_color` | Hex Color | Warna emas highlight (#F59E0B) |
| **Tema Warna** | `landing_theme_bg_color` | `draft_landing_theme_bg_color` | Hex Color | Warna background kanvas (#FFFFFF) |
| **Tipografi** | `landing_theme_font_display` | `draft_landing_theme_font_display` | String | Font judul Google Fonts (Lexend) |
| **Tipografi** | `landing_theme_font_body` | `draft_landing_theme_font_body` | String | Font teks bacaan (Source Sans 3) |
| **Media Berkas** | `landing_hero_image` | `draft_landing_hero_image` | File Path | Lokasi berkas gambar storage |
| **Media Berkas** | `landing_panduan_pdf` | `draft_landing_panduan_pdf` | File Path | Lokasi berkas buku panduan PDF |
| **Teks Hero** | `landing_hero_kicker` | `draft_landing_hero_kicker` | String | Teks pembuka atas judul |
| **Teks Hero** | `landing_hero_title` | `draft_landing_hero_title` | String | Teks judul utama periode |
| **Struktur Data**| `landing_header_nav` | `draft_landing_header_nav` | JSON Array | Daftar item navigasi navbar |
| **Struktur Data**| `landing_tahapan_steps` | `draft_landing_tahapan_steps` | JSON Array | 4 tahapan alur magang |
| **Struktur Data**| `landing_faq_data` | `draft_landing_faq_data` | JSON Array | Kategori dan daftar Q&A FAQ |
| **Struktur Data**| `landing_footer_medsos` | `draft_landing_footer_medsos` | JSON Array | Daftar akun sosial media dinas |
| **Struktur Data**| `landing_footer_tautan` | `draft_landing_footer_tautan` | JSON Array | Tautan portal eksternal & privasi |
| **Struktur Data**| `landing_footer_akun` | `draft_landing_footer_akun` | JSON Array | Tautan rute login peran |
| **Metadata Draf**| `landing_draft_active` | - | Boolean ('1'/'0') | Status apakah kontainer draf aktif |
| **Metadata Draf**| `landing_draft_updated_at` | - | String | Tanggal & waktu pembaruan draf |
| **Metadata Draf**| `landing_draft_author_name`| - | String | Nama SuperAdmin penyunting draf |

---

## 6. DAFTAR FILE & RUTE TERKAIT FITUR LANDING PAGE

### 6.1 Backend (PHP / Laravel 10)
- `app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php`: Controller WordPress Site Editor (AJAX Save Draft, Publish, Discard).
- `app/Http/Controllers/SuperAdmin/LandingCmsController.php`: Controller CMS Landing Page reguler (8 Tab, upload file, preview).
- `app/Http/Requests/SuperAdmin/SaveLandingSiteEditorRequest.php`: Validasi skema parameter pixel, warna, teks, dan array draf.
- `app/Services/LandingCmsStagingService.php`: Layanan staging sandbox database dan promosi berkas storage.
- `app/Support/Pengaturan.php`: Helper singleton pengambil konfigurasi dengan dukungan otomatis mode pratinjau.
- `app/Models/Pengaturan.php`: Model Eloquent tabel basis data `pengaturan`.

### 6.2 Rute Web (`routes/web.php`)
- `GET  /superadmin/site-editor`: Antarmuka WordPress Site Editor full-screen.
- `POST /superadmin/site-editor/save-draft`: Endpoint AJAX penyimpanan draf editor.
- `POST /superadmin/site-editor/publish`: Endpoint AJAX publikasi live editor.
- `DELETE /superadmin/site-editor/discard`: Endpoint AJAX buang draf editor.
- `GET  /superadmin/cms-landing`: Halaman CMS form reguler 8 tab.
- `PUT  /superadmin/cms-landing`: Penyimpanan section form CMS.
- `POST /superadmin/cms-landing/publish`: Publikasi draf CMS ke publik.
- `DELETE /superadmin/cms-landing/discard`: Buang draf CMS.
- `GET  /superadmin/cms-landing/preview`: Pratinjau interaktif draf penuh.
- `DELETE /superadmin/cms-landing/foto`: Reset foto banner ke bawaan.
- `DELETE /superadmin/cms-landing/panduan`: Reset dokumen PDF ke bawaan.

### 6.3 Frontend Views (Blade)
- `resources/views/superadmin/site-editor/index.blade.php`: Workspace WordPress Site Editor.
- `resources/views/superadmin/site-editor/partials/_topbar.blade.php`: Bar atas editor (viewport switcher, tombol simpan/publish).
- `resources/views/superadmin/site-editor/partials/_inspector.blade.php`: Panel kontrol kanan dengan slider ukuran pixel.
- `resources/views/superadmin/site-editor/partials/_inserter.blade.php`: Laci drawer daftar blok halaman.
- `resources/views/superadmin/landing/index.blade.php`: Dashboard CMS 8 tab dengan banner draf sandbox.
- `resources/views/superadmin/landing/partials/_draft_status_banner.blade.php`: Banner peringatan draf aktif.
- `resources/views/public/partials/_preview_control_bar.blade.php`: Floating toolbar navigasi pada halaman pratinjau.
- `resources/views/public/landing.blade.php`: Landing page publik dengan injeksi token CSS dinamis.

### 6.4 Frontend Assets (Vite ES Modules & CSS)
- `resources/css/pages/superadmin/site-editor.css`: Stylesheet bertema WordPress.org Gutenberg.
- `resources/js/pages/superadmin/site-editor/editor-app.js`: Main orchestrator WordPress Site Editor.
- `resources/js/pages/superadmin/site-editor/inspector.js`: Logika panel slider pixel dan color picker.
- `resources/js/pages/superadmin/site-editor/canvas-bridge.js`: Jembatan komunikasi dua arah `postMessage` parent-iframe.
- `resources/js/pages/public/landing-canvas-runtime.js`: Runtime kanvas iframe untuk seleksi blok dan inline editing.
- `resources/js/pages/superadmin/cms-builder.js`: Skrip dynamic repeaters untuk Form CMS reguler.

---

## 7. HASIL PENGUJIAN OTOMATIS & VERIFIKASI KUALITAS

Seluruh fitur telah diverifikasi melalui pengujian otomatis PHPUnit dan linter:

1. **Test Suite `LandingCmsSiteEditorTest` (7 Tests, 40 Assertions - LULUS 100%)**:
   - Verifikasi hak akses RBAC SuperAdmin (status 200) dan penolakan peran lain (status 403 / redirect 302).
   - Verifikasi AJAX save draft dengan nilai pixel integer tersimpan di staging database.
   - Verifikasi penolakan nilai pixel di luar rentang min-max (validasi 422).
   - Verifikasi publikasi draf memindahkan data ke versi live publik.
   - Verifikasi pembuangan draf (*discard*) membersihkan seluruh data sandbox.
   - Verifikasi binding CSS variables dinamis pada landing page saat `?editor_mode=1`.

2. **Test Suite `LandingCmsStagingTest` (7 Tests, 49 Assertions - LULUS 100%)**:
   - Menjamin integritas pemisahan data live dan data draf.
   - Promosi berkas foto dan dokumen PDF saat publikasi.

3. **Test Suite `LandingCmsVisualBuilderAndThemeTest` (6 Tests, 42 Assertions - LULUS 100%)**:
   - Memastikan repeater form navigasi, FAQ, footer, kebijakan, dan tema berfungsi tanpa error sintaks JSON.

4. **Standar Kode Laravel Pint**:
   - `100% Passed` sesuai standar PSR-12 dan konvensi MAGKOM.

---

*Laporan audit ini digenerate secara otomatis oleh sistem engineering MAGKOM sebagai satu sumber kebenaran (Single Source of Truth) dokumentasi fitur perubahan tampilan Landing Page SuperAdmin.*

