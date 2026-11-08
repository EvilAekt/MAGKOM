# DOKUMEN TUGAS SENIOR DEVELOPER
## Fitur: Visual Form Builders (Eliminasi JSON Manual) & Theming Engine (Warna, Tipografi & Desain)
### Modul Super Admin CMS Landing Page — MAGKOM

---

**Kepada:** Junior Developer / AI Coding Agent (Gemini 3.8 Flash, Cursor, Copilot)  
**Dari:** Senior Lead Software Engineer  
**Tanggal:** 7 November 2026  
**Status:** Ready for Step-by-Step Implementation  
**Prioritas:** P1 — High Usability & Full Customization Capability  
**Target:** 
1. 100% Menghilangkan Textarea Format JSON Mentah dan Menggantinya dengan Komponen Formulir Visual Interaktif (Repeater List / Builder Baris Dinamis).
2. Membangun Modul Kustomisasi Desain & Tema (Theming Engine) agar Super Admin dapat mengubah Palet Warna, Tipografi Google Fonts, dan Radius Sudut secara dinamis tanpa menyentuh kode CSS.
3. Terintegrasi penuh dengan sistem Staging Sandbox (Draf, Pratinjau Interaktif, dan Publikasi).

---

## 1. LATAR BELAKANG & IDENTIFIKASI MASALAH

### 1.1 Masalah Saat Ini: Textarea JSON Mentah (Anti-Pattern UX)
Berdasarkan hasil tangkapan layar antarmuka CMS Super Admin saat ini:
1. **Gambar 1 (Header Navigasi)**: Super Admin masih disodori textarea JSON mentah untuk mengedit menu navigasi (`[{"label":"Beranda","url":"#beranda"}]`).
2. **Gambar 2 (FAQ Accordion)**: Super Admin harus menulis array JSON bertingkat untuk kategori dan daftar pertanyaan-jawaban. Satu koma atau kurung kurawal yang salah ketik akan menyebabkan error parsing.
3. **Gambar 3 (Footer Kontak, Medsos & Akun)**: Tiga textarea gelap berisi JSON array untuk akun media sosial, tautan terkait, dan tautan akun.
4. **Gambar 4 (Kebijakan Privasi)**: Textarea besar berisi JSON array nomor, judul, konten, dan array items.

Bagi pengguna instansi non-teknis, mengedit kode JSON mentah sangat rentan menimbulkan kesalahan sintaks (*syntax error*), merusak tampilan, dan terasa rumit.

### 1.2 Kebutuhan Ekstensi: Kustomisasi Warna & Tipografi (Theming Engine)
Instansi meminta fleksibilitas agar identitas visual Landing Page dapat disesuaikan tanpa perlu melakukan deployment ulang kode:
- Mengubah warna utama (*primary brand color*), warna aksen (*accent color*), warna emas highlight (*gold accent*), dan background.
- Mengubah kombinasi jenis huruf (*Font Family* Google Fonts) untuk Heading dan Body Text.
- Mengubah gaya sudut kartu dan tombol (*Border Radius*).

---

## 2. ARSITEKTUR SOLUSI TEKNIS

```
[ CMS SUPER ADMIN: KELOLA LANDING PAGE ]
   │
   ├── Tab 0: Header & SEO ──────> [ Visual Repeater: Tambah/Hapus Menu Navbar ]
   ├── Tab 1: Hero & Media ──────> [ Form Input Teks & Upload Berkas ]
   ├── Tab 2: Kuota & Formasi ───> [ Form Input Pertanyaan & Label Tombol ]
   ├── Tab 3: Alur & Tahapan ────> [ Form 4 Checkpoint Alur Pendaftaran ]
   ├── Tab 4: FAQ (Tanya Jawab) ──> [ Visual Accordion Builder: Kategori & List Q/A ]
   ├── Tab 5: Footer & Kontak ───> [ Visual Repeater: Medsos, Tautan & Akun ]
   ├── Tab 6: Kebijakan Privasi ──> [ Visual Section Builder: Pasal & Butir Poin ]
   └── Tab 7: Tema & Desain Visual > [ BARU: Color Pickers, Font Selector, Border Radius ]
                                          │
                                          ▼
                         [ Staging Sandbox (draft_landing_*) ]
                                          │
                                          ▼
                   [ Interactive Live Preview (/?preview=1) ]
                                          │
                                          ▼
                       [ Publikasikan ke Live (landing_*) ]
```

### Prinsip Desain:
1. **No Inline JavaScript in Blade**: Sesuai aturan repository MAGKOM (`AGENTS.md`), tidak boleh ada tag `<script>` atau atribut `onclick`/`onchange` di Blade. Seluruh interaksi repeater ditangani oleh file ES Module terdedikasi di `resources/js/pages/superadmin/cms-builder.js`.
2. **Dual-Format Controller Acceptance**: Controller menerima input formulir dalam bentuk array HTML native (`nav[0][label]`, `nav[0][url]`), lalu secara otomatis mengemasnya menjadi format JSON yang aman untuk disimpan ke tabel `pengaturan`. Controller juga tetap mendukung input JSON string legacy agar 625 test suite yang sudah ada tetap lulus 100%.
3. **Dynamic CSS Variables Injection**: Nilai tema disuntikkan ke `:root` CSS di `landing.blade.php`, sehingga seluruh token warna dan font langsung berubah di seluruh halaman secara instan.

---

## 3. SPESIFIKASI FORMULIR VISUAL (PENGGANTI JSON TEXTAREA)

### 3.1 Tab Header: Builder Navigasi Navbar
Menggantikan textarea Gambar 1 dengan tabel daftar baris interaktif:

```
[ DAFTAR MENU NAVIGASI NAVBAR ]
┌─────────────────────────┬─────────────────────────┬──────────────┐
│ Label Menu              │ Target URL / Anchor     │ Aksi         │
├─────────────────────────┼─────────────────────────┼──────────────┤
│ [ Beranda             ] │ [ #beranda            ] │ [ 🗑️ Hapus ] │
│ [ Kuota               ] │ [ #kuota              ] │ [ 🗑️ Hapus ] │
│ [ Tahapan             ] │ [ #tahapan            ] │ [ 🗑️ Hapus ] │
│ [ FAQ                 ] │ [ #faq                ] │ [ 🗑️ Hapus ] │
└─────────────────────────┴─────────────────────────┴──────────────┘
[ + Tambah Menu Navigasi Baru ]
```
- Atribut HTML: `data-repeater="nav"`
- Field: `name="nav_items[INDEX][label]"` dan `name="nav_items[INDEX][url]"`

---

### 3.2 Tab FAQ: Visual Category & Q/A Nested Builder
Menggantikan textarea Gambar 2 dengan kartu kategori interaktif:

```
[ KELOLA FAQ & TANYA JAWAB ]

▼ Kategori 1: Syarat dan Tata Cara Mendaftar
  ID Kategori: [ syarat ]   Nama Kategori: [ Syarat dan Tata Cara Mendaftar ]
  ----------------------------------------------------------------------------
  • Pertanyaan 1:
    [ Bagaimana alur dan tata cara mendaftar magang di Diskominfo Sleman?   ]
    Jawaban:
    [ Pendaftaran dilakukan secara mandiri atau kelompok melalui portal...  ]
    [ 🗑️ Hapus Pertanyaan ]
  ----------------------------------------------------------------------------
  [ + Tambah Pertanyaan ke Kategori Ini ]
  [ 🗑️ Hapus Kategori Ini ]

[ + Tambah Kategori FAQ Baru ]
```
- Atribut HTML: `data-repeater="faq-categories"`
- Field:
  - `name="faq_categories[CAT_INDEX][id]"`
  - `name="faq_categories[CAT_INDEX][nama]"`
  - `name="faq_categories[CAT_INDEX][items][ITEM_INDEX][q]"`
  - `name="faq_categories[CAT_INDEX][items][ITEM_INDEX][a]"`

---

### 3.3 Tab Footer: Visual Repeaters (Medsos, Tautan & Akun)
Menggantikan 3 textarea Gambar 3 dengan 3 tabel repeater yang bersih:

1. **Akun Media Sosial**:
   - Kolom: Platform (Pilihan: Instagram, Twitter / X, YouTube, Facebook, LinkedIn, TikTok), Label/Username, URL Tautan.
   - Field: `name="footer_medsos[INDEX][platform]"`, `name="footer_medsos[INDEX][label]"`, `name="footer_medsos[INDEX][url]"`.
2. **Tautan Terkait**:
   - Kolom: Nama Tautan, URL Tautan.
   - Field: `name="footer_tautan[INDEX][label]"`, `name="footer_tautan[INDEX][url]"`.
3. **Tautan Menu Akun**:
   - Kolom: Label Masuk Akun, URL Login.
   - Field: `name="footer_akun[INDEX][label]"`, `name="footer_akun[INDEX][url]"`.

---

### 3.4 Tab Kebijakan Privasi: Visual Section & Bullet Builder
Menggantikan textarea Gambar 4 dengan kartu per pasal/bagian:

```
[ DAFTAR PASAL KEBIJAKAN PRIVASI ]

▼ Bagian 1: Komitmen Perlindungan Data Pribadi
  Nomor: [ 1 ]  Judul Pasal: [ Komitmen Perlindungan Data Pribadi ]
  Isi Paragraf Konten:
  [ Dinas Komunikasi dan Informatika (Diskominfo) Kabupaten Sleman... ]
  
  Daftar Butir Poin / Sub-items (Opsional):
  - [ Butir 1: Identitas diri... ] [ 🗑️ ]
  - [ Butir 2: Kontak...         ] [ 🗑️ ]
  [ + Tambah Butir Poin ]
  [ 🗑️ Hapus Pasal Ini ]

[ + Tambah Pasal Baru ]
```
- Field:
  - `name="kebijakan_sections[SEC_INDEX][nomor]"`
  - `name="kebijakan_sections[SEC_INDEX][judul]"`
  - `name="kebijakan_sections[SEC_INDEX][konten]"`
  - `name="kebijakan_sections[SEC_INDEX][items][]"`

---

## 4. SPESIFIKASI MODUL TEMA & DESAIN VISUAL (TAB BARU)

Kita menambahkan **Tab 8: Tema & Desain Visual** (`tab=theme`) pada CMS Super Admin.

### 4.1 Kamus Data Pengaturan Tema (Theme Tokens)
Seluruh token disimpan pada tabel `pengaturan` dan mendukung draf staging:

| Kunci Pengaturan | Tipe | Nilai Bawaan (Default) | Keterangan |
|---|---|---|---|
| `landing_theme_primary_color` | Color HEX | `#1B3860` | Warna Navy Utama Header & Kartu |
| `landing_theme_navy_dark` | Color HEX | `#122846` | Warna Navy Gelap Hero & Footer |
| `landing_theme_accent_color` | Color HEX | `#4F46E5` | Warna Aksen Interaktif & Tombol Utama |
| `landing_theme_gold_color` | Color HEX | `#F59E0B` | Warna Aksen Emas & Badge Sorotan |
| `landing_theme_bg_color` | Color HEX | `#FFFFFF` | Warna Latar Belakang Body |
| `landing_theme_font_display` | Select | `Lexend` | Font Judul (Heading & Hero) |
| `landing_theme_font_body` | Select | `Source Sans 3` | Font Isi Konten & Paragraf |
| `landing_theme_border_radius` | Select | `12px` | Radius Sudut Kartu & Panel |
| `landing_theme_btn_style` | Select | `rounded` | Bentuk Tombol: `rounded` (8px) / `pill` (9999px) |

### 4.2 Daftar Pilihan Font Resmi (Google Fonts Whitelist)
Untuk menjaga kecepatan (*Core Web Vitals*) dan keselarasan desain instansi:
- **Heading / Display Font**:
  1. `Lexend` (Bawaan MAGKOM - Modern, Bersih, Geometris)
  2. `Plus Jakarta Sans` (Standar Portal Pemerintah Digital Modern)
  3. `Poppins` (Ramah & Elegan)
  4. `Inter` (Netral & Berstandar Internasional)
  5. `Montserrat` (Tegas & Berwibawa)
- **Body / Sans Font**:
  1. `Source Sans 3` (Bawaan MAGKOM - Keterbacaan Tinggi)
  2. `Plus Jakarta Sans`
  3. `Inter`
  4. `Roboto`
  5. `Open Sans`

### 4.3 Penerapan Dinamis di View (`public/landing.blade.php`)
Di dalam `<head>` file `landing.blade.php`, muat stylesheet font dinamis dan timpa variabel CSS `:root`:

```blade
@php
    $themePrimary = \App\Support\Pengaturan::get('landing_theme_primary_color', '#1B3860');
    $themeNavyDark = \App\Support\Pengaturan::get('landing_theme_navy_dark', '#122846');
    $themeAccent = \App\Support\Pengaturan::get('landing_theme_accent_color', '#4F46E5');
    $themeGold = \App\Support\Pengaturan::get('landing_theme_gold_color', '#F59E0B');
    $themeBg = \App\Support\Pengaturan::get('landing_theme_bg_color', '#FFFFFF');
    $fontDisplay = \App\Support\Pengaturan::get('landing_theme_font_display', 'Lexend');
    $fontBody = \App\Support\Pengaturan::get('landing_theme_font_body', 'Source Sans 3');
    $cardRadius = \App\Support\Pengaturan::get('landing_theme_border_radius', '12px');
    $btnStyle = \App\Support\Pengaturan::get('landing_theme_btn_style', 'rounded');
    $btnRadius = $btnStyle === 'pill' ? '9999px' : '8px';
@endphp

<!-- Dynamic Google Fonts Loader -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family={{ urlencode($fontDisplay) }}:wght@400;500;600;700&family={{ urlencode($fontBody) }}:wght@400;500;600;700&display=swap" rel="stylesheet">

<style id="magkom-dynamic-theme-vars">
    :root {
        --navy: {{ $themePrimary }} !important;
        --navy-dark: {{ $themeNavyDark }} !important;
        --accent: {{ $themeAccent }} !important;
        --gold: {{ $themeGold }} !important;
        --background: {{ $themeBg }} !important;
        --font-display: '{{ $fontDisplay }}', sans-serif !important;
        --font-sans: '{{ $fontBody }}', sans-serif !important;
        --radius-card: {{ $cardRadius }} !important;
        --radius-button: {{ $btnRadius }} !important;
    }
</style>
```

---

## 5. RUNBOOK LANGKAH IMPLEMENTASI (STEP-BY-STEP)

Sebagai Senior Developer, berikut tahapan kerja terurut yang harus dieksekusi oleh Junior Developer / AI:

### Langkah 1: Bangun Modul JavaScript Repeater Terpusat (Tanpa Inline Script)
- **Berkas**: `resources/js/pages/superadmin/cms-builder.js`
- **Fungsi**:
  - Menginisialisasi tombol `[+ Tambah]` dan `[🗑️ Hapus]` untuk tabel repeater navigasi, medsos, tautan, dan akun.
  - Menginisialisasi builder bertingkat untuk kategori FAQ dan daftar pertanyaan.
  - Menginisialisasi builder pasal Kebijakan Privasi dan butir poin.
  - Mendaftarkan file ini ke `vite.config.js` dan entry point `@vite`.

### Langkah 2: Perbarui Controller Backend (`LandingCmsController.php`)
- **Tugas**:
  - Modifikasi method `update()` agar dapat memproses data array form native (`$request->input('nav_items')`, `$request->input('faq_categories')`, `$request->input('footer_medsos')`, dll.) secara otomatis.
  - Tambahkan validasi dan sanitasi array.
  - Pertahankan fallback dukungan JSON string jika ada request legacy dari API/unit test.
  - Tambahkan penanganan section baru: `section === 'theme'` untuk menyimpan token warna, font, dan border radius.

### Langkah 3: Redesign Tampilan Form Blade CMS (`superadmin/landing/index.blade.php`)
- **Tugas**:
  - Hapus seluruh `<textarea name="..._json">` gelap pada Tab 0, 4, 5, dan 6.
  - Ganti dengan komponen repeater visual yang rapi, bersih, memiliki ikon, dan mudah dipahami.
  - Tambahkan Tab 8: `Tema & Desain Visual` lengkap dengan HTML5 color picker (`<input type="color">`), input teks kode HEX, dan `<select>` font Google Fonts.

### Langkah 4: Terapkan Variabel Desain Dinamis pada `landing.blade.php`
- **Tugas**:
  - Suntikkan `:root` theme style block pada `<head>` file `landing.blade.php`.
  - Pastikan tombol CTA, banner hero, kartu kuota, dan footer mengadopsi variabel `--radius-card` dan `--radius-button`.

### Langkah 5: Pembuatan Automated Feature Tests & Regression
- **Berkas Baru**: `tests/Feature/LandingCmsVisualBuilderAndThemeTest.php`
- **Skenario Uji (AAA)**:
  1. Super Admin menyimpan menu navigasi lewat array visual form -> tersimpan dan muncul di navbar.
  2. Super Admin menyimpan FAQ berkategori lewat array visual form -> muncul di accordion landing.
  3. Super Admin menyimpan konfigurasi tema warna & font kustom -> tersimpan dan dirender di tag `:root` landing page.
  4. Pengujian Staging Sandbox pada tema: perubahan tema dapat diuji di `/superadmin/cms-landing/preview` sebelum dipublikasikan ke live publik.
  5. Seluruh 625 existing test suite tetap lulus 100%.

---

## 6. KASUS UJI & SKENARIO VERIFIKASI (TEST SUITE SPECIFICATION)

```php
// Skenario 1: Simpan navigasi via form visual array
$this->actingAs($superAdmin)
    ->put(route('superadmin.landing.update'), [
        'section' => 'header_seo',
        'action_type' => 'publish',
        'meta_title' => 'Portal MagKom Sleman',
        'meta_desc' => 'Deskripsi Resmi',
        'header_brand_name' => 'MAGKOM',
        'header_brand_sub' => 'Diskominfo Sleman',
        'nav_items' => [
            ['label' => 'Beranda Utama', 'url' => '#beranda'],
            ['label' => 'Alur Pendaftaran', 'url' => '#tahapan'],
        ],
    ])->assertRedirect();

$this->get(route('landing'))
    ->assertSee('Beranda Utama')
    ->assertSee('Alur Pendaftaran');

// Skenario 2: Kustomisasi tema warna & tipografi
$this->actingAs($superAdmin)
    ->put(route('superadmin.landing.update'), [
        'section' => 'theme',
        'action_type' => 'draft',
        'primary_color' => '#0F766E', // Hijau Teal
        'font_display' => 'Plus Jakarta Sans',
        'border_radius' => '16px',
    ])->assertRedirect();

// Publik tetap melihat warna live awal
$this->get(route('landing'))->assertDontSee('#0F766E');

// Mode pratinjau superadmin melihat warna baru
$this->actingAs($superAdmin)
    ->get(route('superadmin.landing.preview'))
    ->assertSee('#0F766E')
    ->assertSee('Plus Jakarta Sans');
```

---

## 7. DEFINITION OF DONE (DoD)

Pekerjaan dianggap selesai jika dan hanya jika:
- [ ] **NOL Textarea JSON**: Tidak ada lagi textarea bertipe JSON di antarmuka CMS Super Admin. Semua digantikan oleh komponen repeater visual.
- [ ] Tersedia tombol tambah baris, hapus baris, dan penataan item yang intuitif.
- [ ] Tersedia Tab baru **"8. Tema & Desain Visual"** yang memungkinkan kustomisasi warna brand, font Google Fonts, dan radius kartu.
- [ ] Fitur tema terhubung secara mulus dengan sistem **Staging Sandbox** (bisa disimpan sebagai Draf, di-preview secara live, lalu dipublikasikan).
- [ ] Tidak ada JavaScript yang ditulis di dalam file Blade (seluruh interaksi modular di `resources/js/`).
- [ ] Seluruh automated feature tests baru lulus 100%.
- [ ] Seluruh 625 automated feature tests yang ada tetap lulus tanpa regresi (`php artisan test`).
- [ ] Pemeriksaan Laravel Pint lulus 100% (`./vendor/bin/pint --test`).
- [ ] Kompilasi aset Vite berhasil (`npm run build`).

---

**Instruksi Penutup Senior Developer:**
> *"Perubahan ini akan meningkatkan kualitas UX CMS Super Admin MAGKOM ke standar aplikasi modern kelas enterprise. Laksanakan runbook langkah demi langkah, utamakan kode yang bersih dan teruji!"*

