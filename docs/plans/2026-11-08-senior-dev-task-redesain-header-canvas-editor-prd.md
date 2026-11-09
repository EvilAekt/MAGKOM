# PRD & Rencana Kerja: Redesain Visual Header Topbar Canvas Editor MAGKOM
# PROJECT: MAGKOM VISUAL CANVAS SITE EDITOR
# ROLE: SENIOR FRONTEND ARCHITECT → JUNIOR DEVELOPER / AI AGENT
# PRIORITY: P1 (UI/UX POLISH & ERGONOMIC UPGRADE)

==================================================
1. KONTEKS & AUDIT VISUAL HEADER AKTUAL
==================================================

Berdasarkan tangkapan layar kondisi aktual header Canvas Editor (`media_1794104934350_8aafc7d5.png`), antarmuka bilah atas (topbar) saat ini telah memiliki seluruh fitur fungsional yang lengkap, namun memiliki sejumlah kelemahan visual, ergonomis, dan hierarki desain:

### Masalah Visual Aktual (Identified Issues):
1. **Overcrowding & Fragmentasi Pembatas (Divider Hell)**:
   - Terlalu banyak sekat garis vertikal tipis abu-abu (`|`) yang memotong header menjadi 6 segmen kaku, menyebabkan mata pengguna terdistraksi dan tata letak tampak terpecah-pecah.
2. **Ambiguitas Tombol Navigasi Kiri**:
   - Tombol kembali ke dashboard bertuliskan `← Canvas Editor`. Teks ini membingungkan pengguna karena pengguna saat ini sedang *berada di dalam Canvas Editor*, dan mengklik tombol itu justru membawa ke *Dashboard SuperAdmin*. Selain itu, teks tombol yang panjang memakan ruang horizontal sebesar ~130px.
3. **Kekakuan Dropdown Selector Halaman**:
   - Dropdown pemilihan halaman publik saat ini memiliki label teks kecil dua baris "Pilih Halaman" di luar elemen dropdown, dipadukan dengan tag `<select>` HTML standar berwarna putih kaku. Tampilannya tidak menyatu dengan estetika aplikasi visual editor modern.
4. **Pusat Kontrol Kanvas Tidak Simetris**:
   - Segmented control pemilih viewport (`Desktop`, `Tablet`, `Mobile`) terdorong terlalu ke kanan karena beban elemen di sisi kiri terlalu panjang, sehingga kehilangan titik fokus simetris tengah (*center symmetry*).
5. **Pill Status Draf Mendominasi Ruang & Pandangan**:
   - Status penyimpanan draf `⚠️ Perubahan Belum Disimpan` menggunakan pill berwarna kuning terang yang sangat lebar (~200px), sehingga menjadi elemen paling mencolok di seluruh layar—mengalahkan tombol aksi utama `Terbitkan`.
6. **Inkonsistensi Border-Radius & Hirarki Tombol Aksi Kanan**:
   - Tombol `Simpan Draf` memiliki sudut tumpul kotak (`border-radius: 8px`), sedangkan tombol `Terbitkan` dan `Rollback` berbentuk kapsul bulat penuh (`border-radius: 9999px`).
   - Tombol `Rollback` menggunakan teks merah dan border merah terang tepat di samping tombol `Terbitkan`, padahal Rollback adalah tindakan darurat/jarang (*recovery action*) yang seharusnya tidak menyaingi tombol penerbitan utama.
   - Tombol bantuan `?` berupa bulatan abu-abu kaku yang tidak proporsional.

==================================================
2. TUJUAN REDESAIN (DESIGN OBJECTIVES)
==================================================

1. **Modern SaaS & Creative Studio Aesthetic**:
   - Mengubah estetika header menjadi setara dengan tool kelas dunia (Figma, Notion, Webflow, WordPress Gutenberg v2).
   - Menggunakan permukaan bernuansa bersih (`#ffffff` dengan subtle backdrop blur), batas bawah halus (`rgba(226, 232, 240, 0.8)`), dan elevasi mikro.
2. **Harmonisasi Tiga Zona (Left, Center, Right)**:
   - **Left Zone**: Identitas instansi (Logo Pemkab Sleman & Brand MAGKOM), breadcrumb yang bersih, tombol kembali iconik minimalis, dan **Modern Page Selector Pill** yang kompak.
   - **Center Zone**: Viewport Switcher simetris berbentuk kapsul segmented yang presisi dengan active sliding elevation.
   - **Right Zone**: Status draf minimalis ber-pulsing dot, utility buttons (Help, Riwayat, Pratinjau), dan kelompok aksi penerbitan terstruktur dengan border radius seragam.
3. **Zero Regression Guarantee**:
   - Mempertahankan 100% ID elemen, event listener, dan struktur interaksi JavaScript (`#select-canvas-page`, `#btn-toggle-inserter`, `#btn-undo`, `#btn-redo`, `#btn-list-view`, `#btn-save-draft`, `#btn-publish`, `#btn-rollback`, `#btn-toggle-inspector`, `#wp-save-status`).

==================================================
3. STRUKTUR VISUAL: SEBELUM VS SESUDAH
==================================================

### A. Kondisi Saat Ini (Before):
```text
[Logo + MAGKOM] | [← Canvas Editor] | [Pilih Halaman: Dropdown v] | [+] [Undo Redo] [List]   [Desktop Tablet Mobile]   [⚠️ Perubahan Belum Disimpan] [?] [Riwayat] [Pratinjau] [Simpan Draf] [Terbitkan] [Rollback] | [Inspector Toggle]
```

### B. Konsep Redesain Modern (After):
```text
ZONA KIRI:                                      ZONA TENGAH:                 ZONA KANAN:
[←] [Logo] MAGKOM / Canvas Editor [📄 Landing Page ▾]  |  [+] [↶ ↷] [☰]  ───  [ 💻 Desktop | 📱 Tablet | 📱 Mobile ]  ───  [● Draf Belum Disimpan] [?] [🕒 Riwayat] [↗ Pratinjau] [Simpan Draf] [🚀 Terbitkan] [↺ Rollback] [◫]
```

==================================================
4. SPESIFIKASI TOKEN DESAIN & KOMPONEN
==================================================

### 4.1 Token Warna & Permukaan (Design Tokens)
- **Header Surface**: `#ffffff` dengan `backdrop-filter: blur(12px)` dan border bawah `1px solid #e2e8f0` (Slate-200).
- **Header Height**: `60px` (ergonomis, compact, memaksimalkan area kanvas vertikal).
- **Brand Primary**: Sleman Blue `#2563EB` (hover: `#1d4ed8`, active: `#1e40af`).
- **Surface Subtle**: Slate-100 `#f1f5f9` (digunakan untuk track segmented control dan hover tombol utility).
- **Border Default**: Slate-200 `#e2e8f0` / Slate-300 `#cbd5e1`.
- **Text Hierarchy**:
  - Primary: Slate-900 `#0f172a` (font-weight: 600)
  - Secondary: Slate-600 `#475569` (font-weight: 500)
  - Muted: Slate-400 `#94a3b8` (font-weight: 400)
- **Border Radius Standar**:
  - Tombol aksi & input: `8px` seragam (`rounded-lg`).
  - Viewport track & pill selector: `9999px` (`rounded-full`).

### 4.2 Zona Kiri: Brand, Navigasi & Page Selector
1. **Tombol Kembali (Back to Dashboard)**:
   - Icon button bulat `34x34px` dengan latar transparan / `hover:bg-slate-100`.
   - Tooltip: `"Kembali ke Dashboard SuperAdmin"`.
   - Menggantikan tombol memanjang bertuliskan `← Canvas Editor`.
2. **Brand & Breadcrumb**:
   - Logo Pemkab Sleman `28x28px`.
   - Teks Breadcrumb: `<span class="font-bold text-slate-900">MAGKOM</span> <span class="text-slate-300">/</span> <span class="text-slate-600 text-xs font-semibold">Canvas Editor</span>`.
3. **Modern Page Selector Pill**:
   - Latar belakang abu-abu lembut `bg-slate-100/90` dengan border halus `border-slate-200/80` dan `border-radius: 8px`.
   - Tinggi `34px` dengan padding horizontal yang seimbang.
   - Dropdown custom-styled dengan icon halaman otomatis dan chevron panah bawah halus.
   - State hover: `bg-slate-200/70` dan cursor pointer.

### 4.3 Zona Tengah-Kiri: Creation & Document Tools
1. **Tombol Inserter `+`**:
   - Ukuran `34x34px`, `border-radius: 8px`.
   - Warna Sleman Blue `#2563EB`, icon plus putih tebal `stroke-width: 2.5`.
   - Efek mikro: `hover:bg-blue-700`, `hover:shadow-sm`, tactile click transform `active:scale-95`.
2. **Undo / Redo Container**:
   - Dikelompokkan dalam satu container berlatar `bg-slate-100/80`, border `border-slate-200/80`, radius `8px`.
   - Tombol icon `28x28px` dengan hover halus. Tombol disabled tampil muted lembut (`opacity: 0.35`).
3. **List View (Document Outline)**:
   - Icon button `34x34px` dengan hover state abu-abu lembut.

### 4.4 Zona Tengah: Segmented Viewport Switcher
- Container berbentuk kapsul penuh (`rounded-full`), tinggi `32px`, latar `bg-slate-100`, border `1px solid #e2e8f0`.
- Item Desktop, Tablet, Mobile:
  - Tinggi `26px`, padding `0 10px`, radius `rounded-full`.
  - Icon SVG ramping `14x14px`.
  - Status aktif (`.is-active`): Latar putih `#ffffff`, teks biru primer `#2563EB`, elevasi halus `box-shadow: 0 1px 2px rgba(0,0,0,0.06)`.

### 4.5 Zona Kanan: Status & Publishing Actions
1. **Pill Status Draf Minimalis (Pulsing Dot Indicator)**:
   - Desain ringkas menggantikan pill kuning raksasa.
   - Indikator berupa dot kecil `6x6px` + teks ringkas:
     - Draf Belum Disimpan: Dot oranye/kuning berkedip lembut (`animate-pulse`) + label `Draf Belum Disimpan`.
     - Tersinkronisasi: Dot hijau solid + label `Tersinkronisasi`.
     - Menyimpan: Dot biru berputar + label `Menyimpan...`.
   - Background transparan atau tint sangat lembut (`bg-amber-50 text-amber-800` / `bg-emerald-50 text-emerald-800`) ber-border tipis halus.
2. **Utility Buttons (`?`, `Riwayat`, `Pratinjau`)**:
   - `?` (Help): Icon button bulat `32x32px`, font tebal, hover `bg-slate-100`.
   - `Riwayat`: Tombol ghost dengan icon jam + teks `"Riwayat"`, hover `bg-slate-100`.
   - `Pratinjau`: Tombol ghost dengan icon external-link + teks `"Pratinjau"`.
3. **Publishing Action Group**:
   - **Simpan Draf**:
     - Background putih, border `1px solid #cbd5e1`, teks `slate-700 font-semibold`, radius `8px`, tinggi `34px`.
     - Hover: `bg-slate-50 border-slate-400 text-slate-900`.
   - **Terbitkan (Primary CTA)**:
     - Background solid Sleman Blue `#2563EB`, teks putih `font-semibold`, radius `8px`, tinggi `34px`.
     - Icon roket/centang halus di samping teks `Terbitkan`.
     - Bayangan lembut: `box-shadow: 0 2px 4px rgba(37, 99, 235, 0.25)`.
     - Hover: `bg-blue-700 shadow-md`.
   - **Rollback (Secondary Recovery)**:
     - Latar belakang transparan dengan border halus `border-rose-200`, teks merah lembut `text-rose-600`, radius `8px`, tinggi `34px`.
     - Hover: `bg-rose-50 border-rose-300 text-rose-700`.
4. **Inspector Toggle Button**:
   - Icon button `34x34px` di ujung paling kanan dengan icon layout-sidebar-right.
   - Status aktif saat panel terbuka: `bg-blue-50 text-blue-600 border border-blue-200`.

==================================================
5. ANALISIS REGRESI & PRESERVASI INTERAKSI JAVASCRIPT
==================================================

Seluruh logika frontend pada `resources/js/pages/superadmin/site-editor/` bergantung pada ID dan selector berikut yang **TIDAK BOLEH BERUBAH**:
- `#select-canvas-page`: Event listener `change` untuk berpindah halaman canvas iframe.
- `#btn-toggle-inserter`: Event listener `click` untuk membuka/menutup drawer Inserter.
- `#btn-undo` & `#btn-redo`: Event listener `click` dan state `disabled` dari `editor-state.js`.
- `#btn-list-view`: Event listener `click` untuk membuka Outline pohon dokumen.
- `.wp-viewport-btn[data-device]`: Event listener untuk beralih mode layar kanvas (desktop, tablet, mobile).
- `#wp-save-status`: Target class updates (`status-dirty`, `status-saving`, `status-saved`, `status-draft`, `status-clean`, `status-error`).
- `#btn-open-shortcuts`: Modal shortcut keyboard.
- `#btn-open-revisions`: Panel riwayat revisi & snapshot.
- `#btn-preview-external`: Link pratinjau tab baru.
- `#btn-save-draft`: Trigger AJAX simpan draf dengan spinner anak `.btn-spinner`.
- `#btn-publish`: Trigger AJAX publikasi draf ke live dengan spinner anak `.btn-spinner`.
- `#btn-rollback`: Trigger AJAX rollback ke backup sebelumnya.
- `#btn-toggle-inspector`: Toggle buka/tutup panel Inspector kanan.

==================================================
6. TAHAPAN EKSEKUSI (STEP-BY-STEP IMPLEMENTATION)
==================================================

### Fase 1: Perbaikan Struktur Markup Semantik (`_topbar.blade.php`)
1. Restrukturisasi Zona Kiri:
   - Ganti tautan kembali panjang dengan icon button kembali berbentuk rounded dengan SVG panah presisi dan tooltip `"Kembali ke Dashboard SuperAdmin"`.
   - Rancang brand breadcrumb: Logo Sleman + Teks `MAGKOM` + separator `/` + teks `Canvas Editor`.
   - Rancang **Page Selector Dropdown Pill** yang elegan, membungkus `<select id="select-canvas-page">` dengan ikon SVG indikator dan chevron panah bawah halus.
2. Tata grup pembuatan konten:
   - Bungkus tombol `#btn-toggle-inserter`, `#btn-undo`, `#btn-redo`, dan `#btn-list-view` dalam satu flex container `wp-tool-group` dengan spasi terukur.
3. Tata Zona Tengah:
   - Sempurnakan container segmented switcher `.wp-viewport-switcher` agar memiliki visual balance di tengah layar.
4. Tata Zona Kanan:
   - Modifikasi `#wp-save-status` agar menampilkan indikator dot pulsing dan label dinamis yang ringkas.
   - Seragamkan seluruh tombol aksi (`#btn-save-draft`, `#btn-publish`, `#btn-rollback`) dengan `border-radius: 8px` konsisten.
   - Berikan styling elegan pada tombol Rollback sebagai secondary action yang tidak mencolok berlebihan.

### Fase 2: Penyesuaian & Penyempurnaan Styling CSS (`site-editor.css`)
1. Update styling `.wp-editor-header`:
   - Latar belakang bersih `#ffffff`, border-bottom `1px solid #e2e8f0`, bayangan tipis `0 1px 3px 0 rgba(15, 23, 42, 0.04)`.
   - Padding horizontal seimbang (`0 16px`), tinggi `60px`.
2. Update styling Page Selector Pill:
   - Latar belakang halus, border transparan/halus, padding rapi, transisi hover yang halus.
3. Update styling Buttons & Groups:
   - `.wp-icon-btn` berukuran `34x34px`, radius `8px`.
   - `.wp-btn-primary` ber-gradient halus biru sleman, shadow lembut, radius `8px`.
   - `.wp-btn-secondary` berlatar putih dengan border slate-300, radius `8px`.
   - `.wp-btn-danger-outline` berlatar transparan dengan border rose-200 dan teks rose-600.
   - `.wp-save-status-pill` dengan pulsing dot animasi dan typography yang compact.
4. Tambahkan responsiveness rules:
   - Pada viewport sedang (lebar layar < 1280px), teks pada tombol Riwayat dan Pratinjau otomatis disembunyikan dan menampilkan icon saja agar tombol tidak terpotong.

### Fase 3: Pengujian & Quality Gate
1. Jalankan unit & feature test:
   - `php artisan test tests/Feature/SuperAdmin/CanvasEditorConsolidatedFeaturesTest.php`
   - `php artisan test tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php`
2. Jalankan formatter:
   - `./vendor/bin/pint --test`
3. Jalankan kompilasi frontend:
   - `npm run build`
4. Lakukan verifikasi visual browser terhadap tampilan header baru.

==================================================
7. ESTIMASI DAMPAK & RISIKO (RISK ASSESSMENT)
==================================================
- **Dampak Logika Bisnis**: Tidak ada (Zero Logic Risk). Perubahan hanya menyentuh lapisan markup presentasi Blade topbar dan stylesheet CSS.
- **Dampak Kompatibilitas Browser**: Sangat rendah; seluruh CSS menggunakan properti modern standar (Flexbox, CSS Variables, SVG, SVG data URI background).
- **Dampak Responsivitas**: Sangat positif; header akan jauh lebih tahan terhadap layar kecil/menengah karena elemen yang sebelumnya memakan ruang lebar (teks kembali panjang dan pill status raksasa) kini telah dioptimalkan secara kompak.

