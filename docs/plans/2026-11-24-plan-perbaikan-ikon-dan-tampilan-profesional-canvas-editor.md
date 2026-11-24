# Plan: Modernisasi Ikon & Antarmuka Inspector Canvas Editor (Menghapus Emoji AI)

## 1. Latar Belakang & Identifikasi Masalah

Berdasarkan tinjauan visual pada dua tangkapan layar (SS 1 & SS 2):
1. **SS 1 (Header Card Blok & Quick Switcher)**:
   - Ikon blok terpilih (`#wp-active-block-icon`) menggunakan emoji Unicode mentah (`🔷` blue diamond), yang terlihat seperti mockup kasar bawaan prompt AI tanpa sentuhan desain profesional.
   - Pilihan dropdown blok (`#wp-block-quick-switcher`) menyertakan emoji berwarna-warni (`🔷`, `📋`, `📊`, `🔄`, `❓`, `🏢`, `🔒`) yang membuat antarmuka CMS instansi pemerintah terkesan tidak konsisten dan tidak elegan.
2. **SS 2 (Slide Card Hero Banner)**:
   - Tombol segmented control tipe slide (`.wp-slide-type-btn`) menggunakan emoji (`🖼️ Banner`, `📝 Foto & Info`, `📢 Pengumuman`).
   - Kotak catatan panduan tipe slide (`.wp-slide-type-note`) juga menyisipkan emoji di awal teks tipografi.

## 2. Sasaran Perbaikan (Design Principles & Standards)

- **Zero Unicode Emoji in UI**: Seluruh emoji amatir/AI dihilangkan 100% dari elemen antarmuka editor.
- **Standar SVG Stroke Lucide/Feather**: Menggunakan SVG vector stroke murni (`stroke="currentColor"`, `stroke-width="2"`, `stroke-linecap="round"`, `stroke-linejoin="round"`) dengan ukuran konsisten (14px – 20px).
- **Konsistensi Design System MagKom (DS-MagKom)**:
  - Warna tema utama: Sleman Blue (`#2563eb`), Dark Blue (`#1d4ed8`), Slate Text (`#334155`), Muted (`#64748b`), dan Soft Surface (`#eff6ff` / `#f8fafc`).
  - Badge container dengan border halus (`#e2e8f0` / `#bfdbfe`) dan sudut melengkung profesional (`border-radius: 8px`).

---

## 3. Rincian Rencana Implementasi

### Tahap 1: Pembuatan Kamus SVG Icon Resolver di JavaScript
Di dalam [inspector.js](file:///d:/laragon/www/MAGKOM/resources/js/pages/superadmin/site-editor/inspector.js):
- Buat helper method `getBlockIconSvg(blockType, size = 18)` yang mengembalikan string SVG stroke bersih untuk tiap blok:
  - `hero`: Ikon Monitor / Layout Banner (`<rect>`, `<path>`)
  - `informasi`: Ikon FileText / Clipboard Checklist (`<path>`, `<polyline>`, `<line>`)
  - `kuota`: Ikon BarChart3 / Diagram Kuota (`<line>`)
  - `tahapan`: Ikon GitCommit / Milestone Alur (`<circle>`, `<line>`)
  - `faq`: Ikon HelpCircle / Tanya Jawab (`<circle>`, `<path>`, `<line>`)
  - `footer`: Ikon Building / Landmark Kantor (`<path>`, `<rect>`)
  - `kebijakan`: Ikon ShieldCheck / Keamanan Privasi (`<path>`)
- Buat helper method `getSlideTypeIconSvg(slideType, size = 14)`:
  - `banner`: Ikon Image / Poster Landscape (`<rect>`, `<circle>`, `<polyline>`)
  - `photo_text`: Ikon Layout Foto + Konten Teks (`<rect>`, `<line>`)
  - `announcement`: Ikon Megaphone / Pengumuman Resmi (`<path>`)

### Tahap 2: Modernisasi Header Card & Quick Switcher (SS 1)
1. **Header Card Icon (`#wp-active-block-icon`)**:
   - Di [\_inspector.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/superadmin/site-editor/partials/_inspector.blade.php):
     Ubah markup default dari emoji `🔷` menjadi badge SVG dinamis berkelas:
     ```html
     <div class="wp-block-badge-icon" id="wp-active-block-icon" aria-hidden="true">
         <!-- SVG di-inject secara dinamis dan presisi -->
     </div>
     ```
   - Di [inspector.js](file:///d:/laragon/www/MAGKOM/resources/js/pages/superadmin/site-editor/inspector.js):
     Perbarui `setActiveBlock()` agar merender SVG icon melalui `innerHTML = this.getBlockIconSvg(blockName, 20)`.
2. **Quick Switcher Select (`#wp-block-quick-switcher`)**:
   - Bersihkan seluruh teks `<option>` dari emoji:
     - `"Hero Banner"`
     - `"Persyaratan & Dokumen"`
     - `"Kuota & Formasi"`
     - `"Alur & Tahapan"`
     - `"FAQ (Tanya Jawab)"`
     - `"Footer & Kontak"`
     - `"Kebijakan Privasi"`
   - Berikan wrapper dengan ikon prefix SVG dan chevron down modern agar selaras dengan `select-canvas-page` di topbar header.

### Tahap 3: Modernisasi Segmented Control & Note Tipe Slide (SS 2)
1. **Slide Type Buttons (`.wp-slide-type-switcher`)**:
   - Di `renderHeroSlidesList()` pada [inspector.js](file:///d:/laragon/www/MAGKOM/resources/js/pages/superadmin/site-editor/inspector.js):
     Ganti span emoji dengan icon SVG + label teks yang elegan:
     ```html
     <button type="button" class="wp-slide-type-btn ${currentType === 'banner' ? 'active' : ''}" data-type="banner" data-slide-index="${idx}">
         ${this.getSlideTypeIconSvg('banner')}
         <span>Banner Poster</span>
     </button>
     <button type="button" class="wp-slide-type-btn ${currentType === 'photo_text' ? 'active' : ''}" data-type="photo_text" data-slide-index="${idx}">
         ${this.getSlideTypeIconSvg('photo_text')}
         <span>Foto & Info</span>
     </button>
     <button type="button" class="wp-slide-type-btn ${currentType === 'announcement' ? 'active' : ''}" data-type="announcement" data-slide-index="${idx}">
         ${this.getSlideTypeIconSvg('announcement')}
         <span>Pengumuman</span>
     </button>
     ```
2. **Slide Type Note (`.wp-slide-type-note`)**:
   - Hapus emoji `🖼️`, `📝`, `📢`.
   - Ganti dengan callout box ber-ikon SVG info mini (`<svg class="wp-note-icon">...`) dengan typography clean:
     - *Banner Poster: Gambar desain jadi (Canva/Photoshop/Figma). Teks web tidak menimpa gambar.*
     - *Foto & Info: Foto dokumentasi kegiatan dengan teks keterangan dan tombol aksi.*
     - *Pengumuman: Kartu informasi pengumuman resmi instansi tanpa foto.*

### Tahap 4: Penataan CSS & Styling Scoped
Di [site-editor.css](file:///d:/laragon/www/MAGKOM/resources/css/pages/superadmin/site-editor.css):
- Styling `.wp-block-badge-icon`:
  - `width: 36px; height: 36px;`
  - `border-radius: 8px;`
  - `background: #eff6ff; border: 1px solid #dbeafe; color: #2563eb;`
  - `display: flex; align-items: center; justify-content: center;`
  - Box-shadow halus modern.
- Styling `.wp-slide-type-switcher` & `.wp-slide-type-btn`:
  - Tata letak rapi, transisi halus, active state dengan kontras tinggi (`#ffffff` bg, `#1d4ed8` text, shadow mikro).
- Styling `.wp-slide-type-note`:
  - Background slate lembut (`#f8fafc`), border kiri biru Sleman (`3px solid #3b82f6`), flex alignment dengan SVG info icon.

---

## 4. Daftar File yang Terlibat

| File | Tindakan | Alasan Teknis |
| :--- | :--- | :--- |
| `resources/views/superadmin/site-editor/partials/_inspector.blade.php` | Edit | Mengganti placeholder emoji `🔷` di header card menjadi container icon badge SVG `.wp-block-badge-icon` |
| `resources/js/pages/superadmin/site-editor/inspector.js` | Edit | Menghapus seluruh emoji dari daftar blok switcher, mengganti tombol tipe slide dengan SVG icon modern, dan menghapus emoji dari teks note panduan |
| `resources/css/pages/superadmin/site-editor.css` | Edit | Menambahkan styling scoped untuk icon badge, segmented control tipe slide ber-SVG, dan clean callout note |
| `app/Services/LandingPage/BlockRegistry.php` | Edit | Membersihkan atribut emoji fallback pada definisi blok agar backend juga menyediakan nama/tipe icon yang bersih |

---

## 5. Rencana Verifikasi & Quality Gate

1. **Vite Asset Build**: `npm run build` untuk memvalidasi tidak ada syntax error pada CSS maupun ES Module JavaScript.
2. **Blade & Route Cache**: `php artisan view:clear` dan `php artisan route:clear`.
3. **Pint Code Style**: `vendor/bin/pint --test` memastikan standar PSR-12 tetap 100% rapi.
4. **PHPStan Static Analysis**: `vendor/bin/phpstan analyse` memastikan tidak ada type mismatch.
5. **Feature Tests**: `php artisan test --filter=LandingCmsSiteEditorTest` memastikan seluruh fungsi staging, publikasi, dan draf tetap lulus 100%.

