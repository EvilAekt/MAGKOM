# Rencana Kerja: Redesain Visual UI/UX Site Editor, Header Topbar, & Canvas Workspace MAGKOM

**Tanggal:** 8 November 2026  
**Status:** COMPLETED (Terverifikasi 100% Lolos Test & Asset Build)  
**Dokumen Referensi:**
- Mockup Acuan Desain: [Mockup Visual Gutenberg Site Editor](file:///C:/Users/galan/.gemini/antigravity/brain/52dc3389-7237-4677-b31a-a339848599df/.user_uploaded/media_1794054835243_7ff4d4ba.jpg)
- Tangkapan Layar Kondisi Aktual: [Kondisi Aktual UI Editor](file:///C:/Users/galan/.gemini/antigravity/brain/52dc3389-7237-4677-b31a-a339848599df/.user_uploaded/media_1794101996738_662f3532.png)
- Panduan Desain: `docs/07-DESIGN-SYSTEM-MAGKOM.md` & `.agents/rules/AGENTS.md`

---

## 1. Tujuan

Meningkatkan kualitas visual (*visual polish & aesthetic upgrade*) dari antarmuka WordPress-Style Visual Site Editor MAGKOM agar mencapai kesetaraan visual 100% (*pixel-perfection*) dengan mockup acuan. Peningkatan berfokus pada:
1. **Header Topbar Editor (`_topbar.blade.php`):** Memperbaiki logo dan tipografi brand Sleman/MAGKOM yang saat ini terhimpit/terpotong, menata tombol kembali dengan rapi, menghadirkan *segmented pill switcher* modern untuk viewport (Desktop, Tablet, Mobile), serta merapikan hierarki tombol aksi (*Simpan Draf*, *Terbitkan*, *Rollback*, *Pratinjau*).
2. **Studio Canvas & Viewport Area:** Menghilangkan banner kontrol pratinjau ganda yang muncul di dalam iframe canvas saat mode editor, memberikan latar studio kanvas bernuansa modern (`#f8fafc`), bingkai viewport melayang (*floating frame*) dengan bayangan halus (*elevated soft shadow*), serta floating block toolbar yang presisi.
3. **Inspector Panel (Panel Kanan):** Mempercantik input form, switch toggle interaktif modern untuk overlay/boolean, tab navigasi (*Page* vs *Block* dan *General* / *Style* / *Advanced*), serta kartu header blok terpilih.
4. **Inserter Drawer (Panel Kiri):** Menata grid kartu blok, kategori, dan banner panduan drag & drop agar selaras dengan desain mockup.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Preservasi Kontrak JavaScript & AJAX:** Seluruh ID elemen, class selektor, dan `data-attribute` yang digunakan oleh modul JavaScript (`editor-app.js`, `editor-state.js`, `block-manager.js`, `inspector-controller.js`, `canvas-bridge.js`, `revision-manager.js`, `media-manager.js`, dan `pattern-library.js`) wajib dipertahankan secara utuh tanpa ada yang diubah namanya.
2. **Isolasi Styling di `resources/css/`:** Semua styling visual baru ditambahkan ke `resources/css/pages/superadmin/site-editor.css`. Tidak ada CSS inline `<style>` di file Blade, mematuhi aturan baku MAGKOM.
3. **Penyembunyian Banner Pratinjau di Iframe:** Ketika kanvas iframe dimuat dengan parameter `editor_mode=1`, banner pratinjau hitam/kuning (`_preview_control_bar.blade.php`) tidak perlu dirender di dalam iframe karena kontrol publikasi sudah tersedia di topbar editor. Banner tetap muncul saat pratinjau dibuka di tab mandiri (`/landing/preview`).

### Pertanyaan Terbuka:
- Tidak ada ambiguitas kritis. Seluruh target desain telah dipetakan secara visual dan komparatif antara mockup referensi dan tangkapan layar sistem saat ini.

---

## 3. Evaluasi Pendekatan Teknis

| Parameter | Opsi A: Refaktor CSS Murni (Tanpa Sentuh Blade) | Opsi B: Koordinasi Sentuhan Markup Semantik Minimal + Modernisasi CSS Tokens (Rekomendasi) |
| :--- | :--- | :--- |
| **Kesesuaian Mockup** | ⚠️ Terbatas. Teks "Site Editor" tetap terbungkus di dalam tag `<a>` sempit (36x36px) sebelum logo, dan banner hitam tetap muncul di dalam iframe. | ✅ **100% Presisi Mockup.** Menata urutan brand: Logo & Teks Brand → Pembatas → Tombol Kembali Site Editor. Banner iframe dinonaktifkan bersih saat mode editor. |
| **Kerapian DOM** | Trik CSS kompleks untuk mengatasi hierarki DOM yang terbalik dan membongkar tag yang terhimpit. | Bersih, semantik, mudah dirawat (*maintainable*), dan sesuai standar PSR-12 / HTML5. |
| **Integritas Uji & JS** | Aman. | **100% Aman** dengan mempertahankan seluruh ID (`btn-save-draft`, `btn-publish`, `btn-rollback`, `btn-undo`, dll). |
| **Rekomendasi** | Tidak disarankan karena ada cacat struktural di header. | **DIREKOMENDASIKAN (Opsi B)**. |

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/superadmin/site-editor/partials/_topbar.blade.php` | **Modify** | 1. Merestrukturisasi bagian kiri: Menampilkan Logo MAGKOM + Brand Label ("MAGKOM - Magang Kominfo Sleman") secara elegan, diikuti garis pembatas dan tombol navigasi kembali "← Site Editor".<br>2. Merapikan *segmented pill viewport switcher* di tengah.<br>3. Menstandarkan tata letak status simpan dan tombol aksi di kanan (*Simpan Draf* outline, *Terbitkan* primary solid blue pill, *Rollback* muted danger, *Pratinjau* ghost link). |
| `resources/views/public/partials/_preview_control_bar.blade.php` | **Modify** | Menambahkan pengecekan `!request()->boolean('editor_mode')` agar banner pratinjau duplikat tidak mengotori area kanvas iframe saat bekerja di Site Editor. |
| `resources/css/pages/superadmin/site-editor.css` | **Modify** | 1. Mengadopsi token warna modern MAGKOM: Sleman Blue (`#1E40AF`, `#2563EB`), Slate grays (`#0F172A`, `#334155`, `#64748B`, `#E2E8F0`, `#F8FAFC`).<br>2. Styling modern untuk Topbar Header (tinggi 64px, bayangan halus `0 1px 2px rgba(0,0,0,0.05)`, tombol konsisten 36px dengan `border-radius: 8px`).<br>3. Styling modern Segmented Pill Switcher (background `#F1F5F9`, active button `#FFFFFF` dengan bayangan mengambang).<br>4. Studio Canvas Styling: background `#F8FAFC`, bingkai kanvas dengan sudut membulat `border-radius: 12px`, bayangan melayang `box-shadow: 0 20px 25px -5px rgba(0,0,0,0.07)`, dan scrollbar halus.<br>5. Floating Toolbar Styling: chip blok `[BLOK: HERO BANNER ⚙]`, tombol aksi melayang dengan background putih bersih dan border `#E2E8F0`.<br>6. Inspector Styling: modern form inputs, tabs segmented pill, custom toggle switch untuk checkbox boolean.<br>7. Inserter Drawer: Grid 3 kolom yang rapi, hover card elevation, dan banner tips drag & drop. |
| `tests/Feature/LandingPage/FinalQualityAssuranceTest.php` | **Verify** | Memastikan pengujian RBAC, CSRF, fungsionalitas blok, dan publishing lifecycle tetap berjalan 100% hijau. |

---

## 5. Langkah Implementasi (Checklist)

### Fase 1: Perbaikan Markup Semantik Minimal (Struktur Header & Kanvas)
- [x] **Langkah 1.1:** Edit `resources/views/public/partials/_preview_control_bar.blade.php` untuk menonaktifkan tampilan banner kontrol saat berada di dalam iframe kanvas editor (`!request()->boolean('editor_mode')`).
- [x] **Langkah 1.2:** Perbarui `resources/views/superadmin/site-editor/partials/_topbar.blade.php`:
  - Tata ulang bagian kiri: Brand identity Sleman/MAGKOM (Logo + teks "MAGKOM" & "Magang Kominfo Sleman"), garis divider vertikal halus, diikuti tombol navigasi kembali `← Site Editor`.
  - Pertahankan seluruh atribut interaksi: `#btn-toggle-inserter`, `#btn-undo`, `#btn-redo`, `#btn-list-view`.
  - Tata segmented viewport switcher `#wp-viewport-switcher` dengan ikon dan label yang seimbang.
  - Standarkan bagian kanan: status draf `#wp-save-status`, `#btn-open-shortcuts`, `#btn-open-revisions`, `#btn-preview-external`, `#btn-save-draft`, `#btn-publish`, `#btn-rollback`, dan `#btn-toggle-inspector`.

### Fase 2: Modernisasi Desain Sistem & CSS (`site-editor.css`)
- [x] **Langkah 2.1:** Definisikan design tokens terpadu pada root `site-editor.css` yang selaras dengan `docs/07-DESIGN-SYSTEM-MAGKOM.md` (warna Sleman Blue `#1E40AF`, `#2563EB`, background `#F8FAFC`, border `#E2E8F0`, teks `#0F172A` & `#475569`).
- [x] **Langkah 2.2:** Styling Topbar Header:
  - Tinggi yang ergonomis (60px), background putih murni, border bawah halus.
  - Brand identity styling (logo proporsional 28px, nama brand tegas, subjudul bernuansa abu-abu muted).
  - Tombol kembali bergaya modern dengan ikon panah ramping dan hover state lembut.
  - Inserter button bergaya solid/accent rounded-lg.
  - History buttons dengan border grup halus.
- [x] **Langkah 2.3:** Styling Segmented Pill Viewport Switcher:
  - Container berbentuk pill (`border-radius: 9999px` atau `10px`), background Slate-100 (`#F1F5F9`).
  - Item tombol dengan ikon Lucide/SVG dan label jelas.
  - Status `.is-active` dengan background putih murni, teks biru primer, dan elevasi bayangan halus (`box-shadow: 0 1px 3px rgba(0,0,0,0.1)`).
- [x] **Langkah 2.4:** Styling Tombol Aksi Kanan:
  - Status sinkronisasi draf berupa pill lembut dengan indikator status dot hijau/oranye yang elegan.
  - `btn-preview-external`: ghost button dengan ikon eksternal.
  - `btn-save-draft`: secondary button ber-outline slate halus.
  - `btn-publish`: solid vibrant blue pill button (`#2563EB`), teks putih tebal, hover state menarik.
  - `btn-rollback`: tombol bahaya muted/subtle dengan border merah lembut.
  - `btn-toggle-inspector`: icon button bersahabat dengan status aktif yang jelas.
- [x] **Langkah 2.5:** Styling Studio Canvas Workspace:
  - Background kanvas: studio canvas background modern (`#F8FAFC` atau `#F1F5F9`).
  - Bingkai kanvas melayang: rounded corners (`border-radius: 12px`), batas tepi halus (`1px solid #E2E8F0`), bayangan kedalaman studio (`box-shadow: 0 20px 25px -5px rgba(0,0,0,0.06), 0 8px 10px -6px rgba(0,0,0,0.04)`).
  - Floating block toolbar: pill putih elegan di atas blok aktif dengan chip nama blok `[BLOK: HERO BANNER ⚙]` berwarna biru, tombol aksi rapi dengan divider pemisah.
  - Frame mode Tablet (768px) dan Mobile (375px) diperbarui dengan bezel modern bernuansa abu-abu gelap ramping, bukan bingkai tebal kuno.
- [x] **Langkah 2.6:** Styling Inspector Panel (Kanan):
  - Segmented tab bar untuk "Page" vs "Block" dan "General" / "Style" / "Advanced".
  - Kartu header blok dengan ikon dalam badge bulat, nama blok tebal, dan teks deskripsi abu-abu lembut.
  - Form input, select, dan textarea: rounded-lg (`8px`), border halus `#CBD5E1`, focus ring biru lembut (`focus:ring-2 focus:ring-blue-500/20`).
  - Custom switch toggle untuk checkbox boolean (seperti lapisan overlay) bergaya iOS/Gutenberg sliding pill.
  - Tombol aksi media (seperti "Ganti Gambar") bergaya modern rounded button.
- [x] **Langkah 2.7:** Styling Inserter Drawer (Kiri):
  - Penataan tab "Blocks", "Patterns", "Media".
  - Search box "Cari blok..." dengan ikon pencarian yang rapi.
  - Grid kartu blok 3 kolom dengan ikon proporsional, teks rapi, dan efek hover mikro.
  - Banner informasi di bawah: "💡 Drag & Drop untuk menambahkan blok ke halaman" bernuansa biru muda informatif.

### Fase 3: Kompilasi & Verifikasi Mutu
- [x] **Langkah 3.1:** Jalankan `npm run build` untuk memverifikasi kompilasi aset Vite tanpa error.
- [x] **Langkah 3.2:** Jalankan Feature Test `.\vendor\bin\phpunit tests/Feature/LandingPage/FinalQualityAssuranceTest.php` untuk memastikan seluruh fungsionalitas dan otorisasi tidak terganggu.
- [x] **Langkah 3.3:** Jalankan `./vendor/bin/pint --test` untuk memastikan kebersihan format kode.
- [x] **Langkah 3.4:** Jalankan `graphify update .` untuk menyinkronkan knowledge graph codebase.

---

## 6. Strategi Testing

1. **Automated Feature Testing:**
   - Menjalankan `tests/Feature/LandingPage/FinalQualityAssuranceTest.php` (12 skenario).
   - Menjalankan suite lengkap `tests/Feature/LandingPage/` (98 skenario).
   - Memastikan tidak ada regresi pada payload AJAX, CSRF, validasi form, atau otorisasi SuperAdmin.
2. **Visual & Responsive Verification:**
   - Uji tampilan Desktop (100% fluid), Tablet (768px), dan Mobile (375px).
   - Uji perilaku interaksi klik pada tombol inserter, undo/redo, riwayat, pratinjau, simpan draf, dan publikasi.
   - Uji tampilan floating toolbar saat blok diklik di dalam iframe kanvas.
3. **Cross-Browser Verification:**
   - Memastikan CSS Flexbox/Grid, Custom Properties, dan Transisi berjalan mulus di Chromium (Chrome/Edge), Firefox, dan WebKit (Safari).

---

## 7. Risiko & Mitigasi

| Risiko Potensial | Tingkat | Mitigasi |
| :--- | :---: | :--- |
| **Selektor JS Rusak:** Perubahan nama class atau ID menyebabkan fungsi editor (autosave, toolbar, undo) berhenti bekerja. | **Tinggi** | **Mitigasi:** Seluruh ID dan class yang diikat oleh event listener JavaScript TIDAK DIUBAH SAMA SEKALI. Hanya dilakukan perapian CSS dan penambahan elemen visual pendukung. |
| **Iframe Resizing Glitch:** Bingkai kanvas meluap (*overflow*) atau scrollbar ganda. | **Sedang** | **Mitigasi:** Menerapkan `overflow: hidden` pada container luar dan mengarahkan scrolling secara terkontrol pada area canvas studio. |
| **Regresi Tampilan Pratinjau Publik:** Perubahan pada file parsial pratinjau merusak halaman landing live publik. | **Rendah** | **Mitigasi:** Penonaktifkan banner floating hanya berlaku spesifik jika parameter query `editor_mode=1` aktif. Halaman publik dan tab pratinjau biasa tidak terpengaruh. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Header Topbar editor tampil modern, proporsional, dan identitas brand Sleman/MAGKOM tidak lagi terhimpit.
- [x] Segmented pill viewport switcher (Desktop, Tablet, Mobile) berfungsi mulus dengan gaya modern.
- [x] Tombol aksi di header memiliki hierarki visual yang jelas (Terbitkan = vibrant primary blue pill, Simpan Draf = secondary, Rollback = muted danger, Pratinjau = ghost link).
- [x] Banner pratinjau duplikat tidak lagi muncul di dalam iframe kanvas saat mode editor.
- [x] Area studio kanvas memiliki latar belakang elegan dan bingkai kanvas melayang (*floating elevated card*).
- [x] Floating toolbar pada kanvas tampil rapi dengan chip identitas blok dan tombol aksi yang selaras dengan mockup.
- [x] Panel Inspector dan Inserter memiliki input, switch toggle, dan kartu blok yang terpoles rapi.
- [x] `npm run build` sukses tanpa error.
- [x] Seluruh unit/feature test pada `tests/Feature/LandingPage/` (98 tests) lolos 100%.
- [x] Kode bersih sesuai standar PSR-12 dan Laravel Pint.
- [ ] Kode bersih sesuai standar PSR-12 dan Laravel Pint.

