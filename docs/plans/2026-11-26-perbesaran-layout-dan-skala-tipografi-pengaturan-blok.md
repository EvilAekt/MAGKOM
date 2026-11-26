# Rencana Kerja: Perbesaran Maksimal Layout & Peningkatan Skala Tipografi Panel Pengaturan Blok

> **Dokumen Rencana Kerja (Planning Document)**  
> **Tanggal Pembuatan**: 26 November 2026  
> **Status**: Selesai Diimplementasikan & Terverifikasi (Completed & Verified)  
> **Referensi Permintaan**: Memperbesar lagi lebar tata letak panel samping kanan "Pengaturan Blok" pada Canvas Editor dan menaikkan ukuran font (*font-size*) yang terlalu kecil agar nyaman dibaca dan tidak membuat mata lelah.

---

## 1. Tujuan
1. **Memperbesar Lebar Panel Inspector Lebih Maksimal**:
   - Meningkatkan lebar `#wp-site-inspector` dari ukuran sebelumnya (`clamp(390px, 24vw, 450px)`) menjadi **`clamp(460px, 28vw, 540px)`** (berkisar antara **480px s/d 540px** pada layar monitor desktop 1080p+ dan minimal **460px** pada laptop standar).
   - Memanfaatkan ruang kanvas abu-abu kosong di sebelah kiri panel secara optimal sehingga panel terasa kokoh, leluasa, dan sekelas aplikasi desain profesional (seperti Webflow, Framer, dan Shopify Theme Editor).
2. **Meningkatkan Skala Tipografi (Keterbacaan Optimal)**:
   - Mengeliminasi seluruh ukuran teks mikro (`10.5px`, `11px`, `11.5px`, `12px`) yang sulit dibaca (*eyestrain*).
   - Menetapkan hierarki ukuran font yang lebih ramah di mata (*legible & accessible*):
     - **Header Bar & Judul Blok**: Naik ke `16px - 17px` (bold, kontras tinggi).
     - **Subtab Navigasi (*General, Style, Advanced*)**: Naik ke `13.5px - 14px` (bold/semi-bold).
     - **Accordion Summary Heading**: Naik ke `15px` (font-weight: 700).
     - **Label Form (*Field Label*)**: Naik ke `14px` (font-weight: 600, warna `#1e293b`).
     - **Input & Textarea Teks**: Naik ke `14.5px - 15px` dengan padding vertikal lebih nyaman.
     - **Teks Bantuan / Petunjuk (*Help Text*)**: Naik dari `11px` ke `12.5px - 13px` (warna netral `#64748b`).
     - **Subpanel Cards (Pos Halte, Kebijakan, PDF Card, Guidelines)**: Teks naik ke `13px - 14px` dan badge naik ke `12px`.
3. **Harmonisasi Kanvas WYSIWYG**:
   - Memastikan area tengah `.wp-canvas-area` tetap responsif dan bingkai kanvas *Desktop*, *Tablet*, dan *Mobile* tetap berada di tengah secara visual tanpa ada pergeseran liar atau *horizontal scrollbar*.

---

## 2. Asumsi & Analisis Konteks

### 2.1 Asumsi Kerja Logis
1. **Ergonomi Pengguna**: SuperAdmin memerlukan visibilitas teks yang jelas saat mengetik teks panjang, menyusun alur tahapan, atau mengatur kebijakan privasi. Ukuran font `11px` terbukti terlalu kecil pada monitor resolusi tinggi (DPI tinggi) atau laptop 14 inci.
2. **Integritas Logic & Kontrak ID DOM**: Penyesuaian tipografi dan dimensi CSS murni bersifat visual presentasional (`site-editor.css`). Semua ID DOM, fungsi binding event di `inspector.js`, dan mekanisme pertukaran data *postMessage* iframe tetap utuh 100%.
3. **Kepatuhan Aturan Repositori**:
   - Seluruh perubahan font dan dimensi dikelola di `resources/css/pages/superadmin/site-editor.css`.
   - Template Blade `_inspector.blade.php` tetap dipertahankan *read-only* tanpa menambahkan inline CSS.
   - Perubahan dikompilasi menggunakan Vite (`npm run build`).

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter Evaluasi | Opsi 1: Static Fixed Width (480px) + Skala Font Global Tunggal | Opsi 2 (Direkomendasikan): Fluid Studio Clamp (`clamp(460px, 28vw, 540px)`) + Tipografi Proporsional Terstruktur |
| :--- | :--- | :--- |
| **Fleksibilitas Resolusi** | Lebar kaku 480px di semua ukuran monitor. Pada layar 2K/4K terasa kurang lebar, sedangkan di layar 1366px memakan porsi cukup besar. | **Sangat Adaptif**: Melebar dinamis hingga 540px di monitor 1080p/2K dan menyusut anggun ke 460px di laptop 1366px/1440px. |
| **Kenyamanan Input 2-Kolom** | Cukup, namun kolom tombol teks + URL memiliki lebar ~220px. | **Sangat Nyaman**: Memberikan ruang ~240px - 260px per kolom input, sehingga URL panjang dan teks tombol tampil utuh tanpa terpotong. |
| **Keterbacaan Teks (Readability)** | Menaikkan font secara global, berisiko merusak proporsi teks tombol kecil atau badge. | **Terukur & Hierarkis**: Menerapkan skala tipografi spesifik (Label `14px`, Input `15px`, Helper `12.5px`, Heading `15-17px`). |
| **Kerapian Antarmuka** | Tampilan membesar namun terkesan padat jika padding tidak disesuaikan. | **Elegan & Proporsional**: Padding diselaraskan seiring kenaikan font sehingga ritme visual tetap seimbang. |

> **Rekomendasi Terpilih**: **Opsi 2**. Memberikan peningkatan kenyamanan membaca yang drastis sekaligus menjaga proporsi ruang kerja canvas editor tetap profesional.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/superadmin/site-editor.css` | **Modify** | 1. Memperbesar dimensi panel: `width: clamp(460px, 28vw, 540px); flex: 0 0 clamp(460px, 28vw, 540px);`.<br>2. Menaikkan skala font pada `.wp-site-inspector`: Top Bar (`16px`), Block Title (`17px`), Block Subtitle (`13.5px`), Block Switcher Select (`14px`).<br>3. Menaikkan font Pill Subtabs (`13.5px - 14px`, padding 9px 14px).<br>4. Menaikkan font Accordion Summary Heading (`15px`, bold) dan Arrow (`14px`).<br>5. Menaikkan font Form Field Labels (`14px`, semi-bold) dan Inputs/Textareas (`14.5px - 15px`, line-height 1.6).<br>6. Menaikkan font Helper Text (`.wp-field-help`) dari `11px` menjadi `12.5px - 13px`.<br>7. Menaikkan font pada subpanel kartu (Pos Tahapan, Pasal Kebijakan, PDF Status Box, Guidelines list) dari 11px/12px menjadi 13px/14px. |
| `docs/plans/2026-11-26-perbesaran-layout-dan-skala-tipografi-pengaturan-blok.md` | **Create** | Dokumen rencana kerja terstruktur ini. |

---

## 5. Rencana Skala Tipografi Baru (Typography Scale Matrix)

| Komponen Inspector | Ukuran Font Lama | Ukuran Font Baru | Keterangan & Dampak Keterbacaan |
| :--- | :--- | :--- | :--- |
| **Inspector Heading** | 14px | **16px** (Bold, `#0f172a`) | Judul panel atas lebih tegas dan mudah diidentifikasi. |
| **Active Block Title** | 15px | **17px** (Bold, `#0f172a`) | Nama blok aktif (cth: Hero Banner) langsung terlihat jelas. |
| **Active Block Description** | 12.5px | **13.5px** (Warna `#64748b`) | Deskripsi fungsi blok tidak lagi berukuran mini. |
| **Block Switcher Dropdown** | 12px / 13px | **14px** (Font-weight 600) | Pilihan ganti blok terbaca jelas saat diklik. |
| **Subtab Buttons (Pills)** | 12.5px | **13.5px - 14px** (Font-weight 600/700) | Teks *General*, *Style*, *Advanced* lebih nyaman ditekan. |
| **Accordion Summary Heading**| 13.5px | **15px** (Font-weight 700, `#1e293b`) | Judul bagian form (cth: Konten Hero Banner) lebih kontras. |
| **Field Group Labels** | 13px | **14px** (Font-weight 600, `#1e293b`) | Label input (Judul Utama, Sub Judul, dll) sangat mudah dibaca. |
| **Inputs & Textarea Text** | 14px | **14.5px - 15px** (Font-weight 500) | Karakter teks input lebih besar dan nyaman saat diketik. |
| **Field Help Text** | 11px | **12.5px - 13px** (Warna `#64748b`) | Petunjuk pengisian tidak lagi terlalu kecil/buram. |
| **Subpanel Cards (Pos/Pasal)**| 11px - 12px | **13px - 14px** | Teks nomor Pos, judul pos, dan pasal kebijakan terbaca jelas. |
| **Guidelines Box (Banner)** | 11px - 12px | **13px** | Ketentuan ukuran resolusi banner terbaca tanpa zoom. |
| **Badge & Toggle Labels** | 10.5px / 12.5px | **12px / 13.5px** | Status dan label sakelar overlay lebih proporsional. |

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1: Persiapan & Konfirmasi Working Tree**
  - Pastikan working tree git dalam kondisi bersih (`git status`).
  - Verifikasi branch aktif adalah `test-2`.
- [x] **Langkah 2: Pembaruan Dimensi Panel di `site-editor.css`**
  - Ubah `.wp-site-inspector` menjadi `width: clamp(460px, 28vw, 540px); flex: 0 0 clamp(460px, 28vw, 540px);`.
  - Pastikan transisi CSS tetap halus saat resize.
- [x] **Langkah 3: Penerapan Skala Tipografi Baru di `site-editor.css`**
  - Terapkan font-size baru untuk Header Bar, Block Header Card, dan Switcher Select.
  - Terapkan font-size baru pada Pill Subtabs (`.wp-subtab-btn`).
  - Terapkan font-size baru pada Accordion (`.wp-accordion-summary`, `.wp-accordion-heading`).
  - Terapkan font-size baru pada Field Labels (`.wp-field-group>label`) dan Helper (`.wp-field-help`).
  - Terapkan font-size baru pada Text Inputs (`.wp-text-input`, `.wp-select-input`, `.wp-textarea-input`).
  - Terapkan font-size baru pada Subpanel Cards (`.wp-card-subpanel`, pos tahapan, pasal kebijakan, dan banner guidelines).
- [x] **Langkah 4: Kompilasi Aset Frontend Vite**
  - Jalankan `npm run build` untuk memverifikasi kompilasi CSS tanpa error.
  - Jalankan `php artisan optimize:clear` (menggunakan PHP 8.3) untuk membersihkan view cache.
- [x] **Langkah 5: Pengujian Otomatis & Verifikasi Visual**
  - Jalankan test suite `tests/Feature/LandingPage/SiteEditorResponsiveCanvasTest.php` dan `tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php`.
  - Verifikasi kenyamanan keterbacaan teks dan kestabilan kanvas pada resolusi Desktop, Tablet, dan Mobile.
- [x] **Langkah 6: Penyelesaian Dokumen Plan & Laporan ke Pengguna**
  - Perbarui status dokumen rencana kerja menjadi selesai.
  - Berikan panduan verifikasi browser (`Ctrl + F5`) kepada pengguna.

---

## 7. Strategi Testing & Validasi

1. **Automated Testing Suite**:
   - Jalankan `php artisan test` pada file pengujian canvas editor untuk memastikan 23 test dan 186 assertions tetap 100% *passed*.
2. **Kompilasi Aset**:
   - Memastikan `npm run build` berhasil menghasilkan bundle asset tanpa warning CSS.
3. **Pemeriksaan Keterbacaan & Aksesibilitas Visual**:
   - Memastikan tidak ada teks yang lebih kecil dari `12.5px` di seluruh area panel inspector.
   - Memastikan label input `14px` dan input text `15px` memiliki kontras warna yang tajam (`#0f172a` dan `#1e293b`).
4. **Pemeriksaan Responsivitas Kanvas**:
   - Memastikan kanvas pratinjau tengah tetap terpusat (*centered*) dan tidak mengalami pemotongan bingkai pada mode Desktop, Tablet (768px), maupun Mobile (375px).

---

## 8. Risiko & Mitigasi

| Potensi Risiko | Tingkat | Rencana Mitigasi |
| :--- | :--- | :--- |
| **Kanvas terdesak pada layar laptop 1366px** | Rendah | Menggunakan `clamp(460px, 28vw, 540px)` sehingga pada layar 1366px panel otomatis mengunci di 460px dan kanvas tengah tetap memiliki ruang sisa ~900px yang sangat memadai. |
| **Input bertingkat (Teks + URL) meluap karena font membesar** | Rendah | Karena lebar panel kini mencapai 480px - 540px, masing-masing kolom input grid memiliki ruang ~230px+, sangat longgar untuk menampung teks font 14.5px. |

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Lebar panel **Pengaturan Blok** telah diperbesar menjadi `clamp(460px, 28vw, 540px)`.
- [x] Skala tipografi panel telah dinaikkan secara menyeluruh (Label 14px, Input 15px, Heading 15-17px, Helper 12.5-13px).
- [x] Seluruh teks berukuran mikro (<12px) telah dieliminasi dari panel inspector.
- [x] Kanvas pratinjau (Desktop, Tablet, Mobile) tetap terpusat dan responsif.
- [x] `npm run build` berhasil 100% tanpa error.
- [x] Seluruh automated test suite (23 test) lolos 100%.
