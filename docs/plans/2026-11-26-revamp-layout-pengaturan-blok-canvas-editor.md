# Rencana Kerja: Revamp Layout & Desain Modern Panel Pengaturan Blok Canvas Editor

> **Dokumen Rencana Kerja (Planning Document)**  
> **Tanggal Pembuatan**: 26 November 2026  
> **Status**: Selesai Diimplementasikan & Terverifikasi (Completed & Verified)  
> **Referensi Permintaan**: Perbesaran dan modernisasi panel samping kanan "Pengaturan Blok" pada Canvas Editor SuperAdmin (`/superadmin/site-editor`).

---

## 1. Tujuan
Meningkatkan kenyamanan, ergonomi, dan estetika panel inspektur kanan (**Pengaturan Blok** / `#wp-site-inspector`) pada Canvas Editor SuperAdmin dengan:
1. **Memperbesar lebar panel inspector** dari ukuran lama (`350px`) menjadi lebar modern adaptif (`clamp(390px, 24vw, 450px)` atau basis `420px-440px`), dengan memanfaatkan ruang kosong (*empty gap*) luas yang selama ini tidak terpakai di sebelah kiri panel (antara kanvas pratinjau dan inspector).
2. **Merombak estetika antarmuka menjadi profesional dan modern (Studio Grade)** yang selaras dengan MagKom Design System (DS-07) dan standar modern visual builders (seperti Figma, Framer, Webflow, dan WordPress Gutenberg):
   - Mengganti tab datar konvensional dengan **Segmented Control Pill Tabs** yang taktil untuk navigasi *General*, *Style*, dan *Advanced*.
   - Mengganti scrollbar abu-abu tebal bawaan Windows yang kaku dengan **Custom Thin Floating Scrollbar** (6px halus).
   - Menata ulang hierarki form, accordion, label, dan input field agar memiliki *breathing room* yang lapang tanpa terasa sesak.
   - Merapikan input bertingkat (seperti Teks Tombol & Tautan URL pada *Hero Banner*) menjadi tata letak yang bersih dan seimbang.
3. **Memastikan stabilitas kanvas pratinjau tengah** (`.wp-canvas-area`) agar bingkai pratinjau *Desktop*, *Tablet*, dan *Mobile* tetap terpusat (*centered*) secara dinamis dan tidak mengalami *clipping* atau *overflow*.

---

## 2. Asumsi & Analisis Konteks

### 2.1 Asumsi Kerja Logis
1. **Resolusi Pengguna**: Mayoritas SuperAdmin mengakses Canvas Editor menggunakan layar Desktop (Full HD 1920×1080) atau Laptop (1366×768 / 1440×900). Pada monitor 1080p, bingkai kanvas desktop (`max-width: 1380px`) menyisakan celah margin horizontal ~200px di sekitarnya. Memperlebar panel inspector hingga ~420px–450px akan menyerap margin kosong tersebut tanpa mengorbankan keterbacaan pratinjau website.
2. **Preservasi Logika & Event Handler**: Seluruh logika JavaScript di `resources/js/pages/superadmin/site-editor/inspector.js` dan `editor-app.js` (seperti binding field input dinamis, accordion toggle, image picker, FAQ repeater, dan tombol close inspector) **tidak boleh dirusak atau diubah kontraknya**. Semua ID dan atribut `data-field` tetap dipertahankan 100%.
3. **Kepatuhan Aturan Blade & CSS**:
   - Seluruh perbaikan styling wajib ditulis di stylesheet terpusat: `resources/css/pages/superadmin/site-editor.css`.
   - Tidak diperkenankan menyisipkan `<style>` atau inline script di dalam file Blade `_inspector.blade.php`.
   - Asset dikompilasi melalui pipeline Vite (`npm run build`).

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter Evaluasi | Opsi 1: Fixed Width Static (420px) | Opsi 2 (Direkomendasikan): Fluid Responsive Clamp (`clamp(390px, 24vw, 450px)`) + Studio Aesthetic |
| :--- | :--- | :--- |
| **Pemanfaatan Ruang** | Mengunci lebar pada 420px statis di semua monitor. | Menyesuaikan lebar secara proporsional (~450px di monitor 1080p+, ~410px di 1440px, dan ~390px di laptop 1366px). |
| **Kenyamanan Input** | Cukup baik, namun pada monitor 2K/4K masih terasa ada ruang yang tersisa. | **Sangat Optimal**: Memberikan ruang horizontal maksimal bagi teks panjang, form multi-kolom, dan repeater. |
| **Estetika UI** | Hanya memperbesar kotak tanpa peremajaan elemen sub-komponen. | **Modern & Elegan**: Meremajakan Segmented Pill Tabs, Custom Scrollbar 6px, Card Shadows, dan Form Focus Rings. |
| **Risiko Responsif** | Potensi kanvas terhimpit pada laptop kecil jika tidak ada `min-width: 0`. | Aman karena menggunakan `min-width: 0` pada kanvas dan transisi lebar yang terkontrol. |

> **Rekomendasi Terpilih**: **Opsi 2**. Pendekatan ini secara tuntas menjawab ketiga poin permintaan user: memperbesar panel, memanfaatkan ruang kosong di sebelah kiri secara adaptif, serta menghadirkan nuansa desain yang modern dan berkelas internasional.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/superadmin/site-editor.css` | **Modify** | 1. Mengubah dimensi `.wp-site-inspector` menjadi `width: clamp(390px, 24vw, 450px); flex: 0 0 clamp(390px, 24vw, 450px);`.<br>2. Menambahkan custom thin scrollbar pada `.wp-tab-pane` dan accordion container.<br>3. Merancang ulang `.wp-block-subtabs` menjadi modern segmented pill container (`background: #f1f5f9; border-radius: 10px; padding: 4px;`) dengan active indicator putih berbayang halus.<br>4. Mempercantik `.wp-block-header-card` dan `.wp-block-switcher-box` (dropdown selector blok).<br>5. Menyelaraskan padding accordion (`padding: 18px 22px`), margin antar-field (20px), dan input multi-kolom (`.wp-input-with-icon`).<br>6. Memastikan `.wp-canvas-area` memiliki `min-width: 0` agar kanvas tidak terpotong. |
| `resources/views/superadmin/site-editor/partials/_inspector.blade.php` | **Modify** | Merapikan struktur kelas pada `.wp-block-subtabs` dan menghapus sisa inline style lama pada subpanel form agar styling 100% terkendali oleh stylesheet CSS. |
| `resources/js/pages/superadmin/site-editor/inspector.js` | **Verify/Review** | Memastikan selector DOM dan fungsi render dinamis (slide repeater, faq repeater, kebijakan repeater) tetap sinkron dan render dengan styling baru tanpa kendala. |

---

## 5. Rencana Desain Antarmuka Baru (Modern Studio Spec)

### 5.1 Dimensi & Struktur Tata Letak
- **Lebar Panel**: `width: clamp(390px, 24vw, 450px);`
- **Header Inspektur**: Tinggi 52px, latar belakang putih bersih, border bawah `1px solid #e2e8f0`, judul `14px` dengan font-weight 700 warna `#0f172a`, tombol tutup `✕` berupa lingkaran abu-abu halus berdiameter 32px dengan hover lembut.
- **Card Identitas Blok**: Latar `#f8fafc` dengan sudut halus, icon badge 42×42px berwarna Biru Kominfo (`#2563eb` / `#01347C`), label judul `15px` tebal, dan deskripsi informatif.
- **Block Switcher Dropdown**: Dropdown ganti blok dengan ketinggian 40px, rounded-lg (8px), border `#cbd5e1`, dan SVG chevron modern.

### 5.2 Segmented Control Pill Subtabs (*General / Style / Advanced*)
- **Container**: Latar `#f1f5f9`, padding 4px, border-radius 10px, margin horizontal 18px.
- **Tab Buttons**:
  - Inactive: Teks abu-abu netral (`#64748b`), font-size 12.5px, font-weight 600, transisi warna dan latar belakang 0.2s.
  - Active: Latar putih solid (`#ffffff`), teks gelap `#0f172a`, `box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);`, border-radius 7px.

### 5.3 Modern Form Controls & Spacing
- **Accordion Header**: Padding 14px 20px, judul 13.5px bold, indikator chevron panah berputar dengan transisi CSS halus `transform: rotate(180deg)`.
- **Accordion Body**: Padding 18px 22px 24px 22px, memberikan ruang bernapas yang nyaman.
- **Field Groups**: Jarak antar-input `18px - 20px`. Label 13px font-weight 600 warna `#334155`.
- **Inputs & Textareas**:
  - Tinggi text input: 42px dengan padding `9px 14px`.
  - Textarea: Minimum height 84px dengan line-height 1.6 yang nyaman.
  - Fokus: Soft glow ring `box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); border-color: #2563eb;`.
- **Grup Tombol Multi-Input (Teks + URL)**:
  - Menggunakan grid 2-kolom seimbang (`grid-template-columns: 1fr 1fr; gap: 8px;`) atau stacked berlabel mikro, sehingga SuperAdmin dapat melihat teks tombol dan URL target berdampingan tanpa terjepit.

### 5.4 Custom Thin Scrollbar
- Menggantikan scrollbar bawaan sistem operasi yang kaku:
  ```css
  .wp-tab-pane {
      scrollbar-width: thin;
      scrollbar-color: #cbd5e1 transparent;
  }
  .wp-tab-pane::-webkit-scrollbar {
      width: 6px;
  }
  .wp-tab-pane::-webkit-scrollbar-track {
      background: transparent;
  }
  .wp-tab-pane::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 9999px;
  }
  .wp-tab-pane::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
  }
  ```

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1: Persiapan & Backup Visual Baseline**
  - Pastikan branch aktif adalah `test-2` dan working tree tercatat dengan rapi.
  - Periksa referensi tangkapan layar user (`media_1795685675114_22b5f723.jpg`, `media_1795685726950_b8ba58b3.png`, `media_1795685763658_d0329b7e.png`).
- [x] **Langkah 2: Pembaruan Styling CSS (`site-editor.css`)**
  - Perbarui lebar `.wp-site-inspector` dari 350px menjadi `clamp(390px, 24vw, 450px)`.
  - Terapkan styling modern pada `.wp-inspector-top-bar`, `.wp-inspector-heading`, dan `.wp-inspector-close-btn`.
  - Terapkan **Segmented Control Pill Tabs** pada `.wp-block-subtabs` dan `.wp-subtab-btn`.
  - Terapkan styling baru pada Card Identitas Blok (`.wp-block-header-card`, `.wp-block-badge-icon`, `.wp-block-switcher-box`).
  - Perhalus styling Accordion (`.wp-accordion`, `.wp-accordion-summary`, `.wp-accordion-body`) dan form fields (`.wp-field-group`, `.wp-text-input`, `.wp-textarea-input`, `.wp-select-input`).
  - Atur tata letak multi-input `.wp-input-with-icon` dengan grid responsif yang lapang.
  - Tambahkan implementasi *custom thin scrollbar* pada `.wp-tab-pane` dan kontainer yang memiliki *overflow*.
- [x] **Langkah 3: Pembersihan & Harmonisasi Markup Blade (`_inspector.blade.php`)**
  - Bersihkan inline styles lama pada kontainer subpanel, badge, dan kartu pos tahapan/panduan PDF agar teratur menggunakan kelas CSS.
  - Pastikan seluruh ID elemen dan atribut `data-field` tetap utuh.
- [x] **Langkah 4: Kompilasi Aset Frontend & Verifikasi Runtime**
  - Jalankan `npm run build` untuk memvalidasi kompilasi CSS dan JS tanpa error.
  - Jalankan `php artisan optimize:clear` untuk memastikan template Blade dan view cache bersih.
- [x] **Langkah 5: Pengujian Responsif & Integritas Fitur**
  - Verifikasi tampilan pada kanvas mode Desktop (100%), Tablet (768px), dan Mobile (375px).
  - Pastikan pengeditan input (Hero, Kuota, Tahapan, FAQ, Footer) tetap tersimpan dan tersinkronisasi ke kanvas pratinjau secara instan.
  - Uji tombol tutup/buka inspector (`#btn-close-inspector` dan `#btn-toggle-inspector`) untuk memastikan transisi buka-tutup tetap lancar.

---

## 7. Strategi Testing & Validasi

1. **Pengujian Build & Linting**:
   - Menjalankan `npm run build` untuk memastikan bundling Vite sukses tanpa warning atau syntax error pada CSS.
2. **Pengujian Responsif & Layout Kanvas**:
   - Uji kanvas pada mode **Desktop**: memastikan jarak margin kanvas di sisi kiri seimbang dan tidak terpotong.
   - Uji kanvas pada mode **Tablet** (768px): memastikan kanvas tablet tetap terpusat di tengah layar.
   - Uji kanvas pada mode **Mobile** (375px): memastikan simulasi smartphone tetap berada di posisi tengah yang proporsional.
3. **Pengujian Interaksi Form & Sinkronisasi Pratinjau**:
   - Mengubah judul hero, tombol CTA, dan persyaratan umum: memastikan pesan *postMessage* ke iframe tetap tersalurkan secara real-time.
   - Menguji peralihan tab *General* -> *Style* -> *Advanced*: memastikan pill tab aktif berpindah dengan animasi yang mulus.
   - Menguji dropdown *Block Quick Switcher*: memastikan blok aktif dapat berganti dengan benar.
4. **Pengujian Browser Scrollbar**:
   - Memastikan scrollbar tipis (6px) tampil rapi di Chrome/Edge tanpa adanya scrollbar horizontal liar (*accidental horizontal overflow*).

---

## 8. Risiko & Mitigasi

| Potensi Risiko | Tingkat | Rencana Mitigasi |
| :--- | :--- | :--- |
| **Kanvas terhimpit pada layar laptop 1366px** | Sedang | Menggunakan `clamp(390px, 24vw, 450px)` dan menambahkan `min-width: 0` pada `.wp-canvas-area`, sehingga pada layar 1366px lebarnya otomatis menyesuaikan pada kisaran ~390px dan kanvas tetap leluasa. |
| **Pemisahan baris input CTA rusak** | Rendah | Menggunakan CSS Grid dengan `minmax(0, 1fr)` sehingga field input teks dan field URL selalu membagi ruang secara adil tanpa meluap ke luar kontainer. |
| **Inline style bawaan Blade menimpa CSS baru** | Rendah | Memindahkan semua styling hardcoded inline style di Blade ke dalam kelas CSS yang terstruktur di `site-editor.css`. |

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Lebar panel **Pengaturan Blok** telah diperbesar secara adaptif (`clamp(390px, 24vw, 450px)`), memanfaatkan ruang kosong di sebelah kiri kanvas secara efektif.
- [x] Tampilan antarmuka inspector berpenampilan modern dan profesional:
  - Tab *General / Style / Advanced* berbentuk Segmented Control Pill.
  - Scrollbar tipis dan elegan (6px halus), tidak ada scrollbar abu-abu tebal Windows.
  - Form input, label, dan accordion memiliki *breathing room* yang lega dan nyaman.
  - Multi-input (Teks Tombol + URL) tertata rapi.
- [x] Kanvas pratinjau (Desktop, Tablet, Mobile) tetap terpusat dan tidak mengalami *clipping*.
- [x] Seluruh fungsi interaktif (tutup/buka inspector, ganti blok, edit form, sinkronisasi real-time) berfungsi normal.
- [x] `npm run build` sukses 100% tanpa error.
