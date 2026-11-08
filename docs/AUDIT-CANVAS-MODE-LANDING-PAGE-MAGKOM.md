# AUDIT TEKNIS MENDALAM: CANVAS MODE (WORDPRESS SITE EDITOR)
## Portal Magang Kominfo Sleman (MAGKOM)
*Fokus Audit: Arsitektur Kanvas WYSIWYG, Dual-Way Bridge, Live Pixel Sync, dan Keamanan Iframe*

---

**Tanggal Audit:** 7 November 2026  
**Fokus Modul:** WordPress.org Style Canvas Mode (`/superadmin/site-editor` & `?editor_mode=1`)  
**Tipe Audit:** Technical Deep-Dive, Performance, Security & Usability Assessment  
**Pemeriksa:** Senior Lead Software Engineer MAGKOM  

---

## 1. PENDAHULUAN & PRINSIP KERJA CANVAS MODE

**Canvas Mode** adalah antarmuka visual berbasis kanvas interaktif di mana SuperAdmin dapat melihat, memilih, mengubah ukuran pixel, dan mengedit konten Landing Page secara langsung di atas kanvas pratinjau tanpa berpindah halaman dan tanpa pemuatan ulang (*zero-reload WYSIWYG experience*).

### Arsitektur Aliran Data (Dual-Way Bridge)

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ PARENT WINDOW: /superadmin/site-editor                                                 │
│                                                                                        │
│  [Top Bar Toolbar] ─────── [Inspector Panel (Kanan)] ───── [State Store (editor-app.js)]│
│   • Viewport Switcher       • Sliders Font Size (px)        • Dirty/Clean Tracker      │
│   • Save Draft / Publish    • Sliders Padding Y (px)        • AJAX Payload Serializer  │
│   • Undo / Redo             • Sliders Border Radius (px)    • Toast Notifier           │
│                             • Sliders Container Width (px)                             │
│                             • Color Pickers (Hex)                                      │
└──────────────────────────────────────┬─────────────────────────────────────────────────┘
                                       │ 
                 window.postMessage()  │ (Validasi Origin: window.location.origin)
                                       │
                                       ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ CHILD IFRAME: /superadmin/cms-landing/preview?editor_mode=1                             │
│ (resources/js/pages/public/landing-canvas-runtime.js)                                  │
│                                                                                        │
│  1. Selection Engine        2. Inline Text Engine       3. Dynamic Style Engine        │
│     • data-wp-block="hero"     • data-wp-editable          • document.documentElement  │
│     • Active Outline (#007CBA) • contenteditable="true"      .style.setProperty()      │
│     • Block Chip Label         • Live Input Event          • Real-time CSS Variable    │
│     • Click-to-Select Signal     Broadcaster                 Override (--wp-*)         │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. AUDIT 7 PILAR TEKNIS CANVAS MODE

### 2.1 Pilar 1: Keamanan Komunikasi Lintas Jendela (Cross-Window Security)
- **Protokol**: Menggunakan API standar peramban `window.postMessage`.
- **Verifikasi Origin (Strict Origin Whitelist)**:
  - Pada `canvas-bridge.js` (Parent): `if (event.origin !== this.targetOrigin) return;`
  - Pada `landing-canvas-runtime.js` (Child Iframe): `if (event.origin !== PARENT_ORIGIN) return;`
  - **Hasil Audit Keamanan**: ✅ **Aman (PASS)**. Pesan dari domain luar atau iframe liar ditolak 100%. Tidak ada risiko injeksi *Cross-Site Scripting (XSS)* atau manipulasi state oleh situs pihak ketiga.
- **Isolasi Iframe**:
  - `landing-canvas-runtime.js` memiliki pelindung: `if (window.self === window.top) return;`. Jika halaman landing page diakses langsung oleh pengunjung umum, seluruh runtime editor dan event listener dinonaktifkan secara otomatis.
- **Pencegahan Klik Tautan Liar**:
  - Di dalam kanvas editor, seluruh event klik pada tag `<a>` diintersepsi via `e.preventDefault()`, sehingga admin tidak akan terlempar keluar dari workspace editor saat mengklik tombol tautan di kanvas.

### 2.2 Pilar 2: Handshake Awal & State Readiness
- **Mekanisme**:
  1. Iframe dimuat dengan URL `route('superadmin.landing.preview', ['editor_mode' => 1])`.
  2. Saat dokumen iframe selesai diparsing (`DOMContentLoaded`), iframe memancarkan sinyal:
     ```javascript
     window.parent.postMessage({ type: 'WP_CANVAS_READY' }, PARENT_ORIGIN);
     ```
  3. Parent window menangkap sinyal ini melalui `CanvasBridge` dan menandai `isReady = true`.
- **Hasil Audit Keandalan**: ✅ **Optimal (PASS)**. Mengeliminasi *race condition* di mana parent mencoba mengirim perintah CSS sebelum iframe selesai memuat DOM.

### 2.3 Pilar 3: Seleksi Visual Elemen (Selection & Bounding Box)
- **Penanda Blok**:
  - Setiap section utama landing page ditandai atribut semantik:
    - `#beranda` -> `data-wp-block="hero"` (`data-wp-block-name="Hero Banner"`)
    - `#kuota` -> `data-wp-block="kuota"` (`data-wp-block-name="Kuota & Formasi"`)
    - `#tahapan` -> `data-wp-block="tahapan"` (`data-wp-block-name="Alur & Tahapan"`)
    - `#faq` -> `data-wp-block="faq"` (`data-wp-block-name="FAQ Accordion"`)
    - `#kontak` -> `data-wp-block="footer"` (`data-wp-block-name="Footer & Kontak"`)
- **Efek Visual Outline**:
  - *Hover State*: Garis putus-putus biru WordPress `outline: 2px dashed #007cba !important`.
  - *Selected State*: Garis solid biru WordPress `outline: 2px solid #007cba !important` dengan bayangan fokus `box-shadow: 0 0 0 1px rgba(0, 124, 186, 0.4)`.
  - *Chip Label Tag*: Muncul label melayang di pojok kiri atas blok: `[ BLOK: HERO BANNER ]`.
- **Interaksi Klik-ke-Inspector**:
  - Saat blok diklik di kanvas, iframe mengirim pesan `WP_BLOCK_SELECTED` beserta koordinat posisi elemen (`rect`).
  - Parent window menangkap event ini, otomatis memindahkan tab Inspector kanan ke tab **[Blok]**, memperbarui judul blok aktif, dan memunculkan **Floating Block Toolbar** di kanvas.

### 2.4 Pilar 4: Inline Content Editing (WYSIWYG di Tempat)
- **Mekanisme**:
  - Elemen teks yang dapat diedit diberi atribut `data-wp-editable="hero_kicker"` dan `data-wp-editable="hero_title"`.
  - Runtime kanvas otomatis mengaktifkan `contenteditable="true"` dan mematikan pemeriksaan ejaan liar (`spellcheck="false"`).
- **Sinkronisasi Teks**:
  - Event `input` pada elemen teks langsung mengirim pesan `WP_INLINE_TEXT_CHANGED` ke parent window.
  - Parent window langsung memperbarui nilai pada form state dan menandai status dokumen sebagai `"Perubahan Belum Disimpan"`.
- **Sanitasi Backend**:
  - Saat draf disimpan, `SaveLandingSiteEditorRequest` dan `LandingCmsStagingService` melakukan sanitasi terhadap karakter berbahaya untuk mencegah *Stored XSS*.

### 2.5 Pilar 5: Sinkronisasi Presisi Dimensi Pixel Real-Time (Zero Latency)
- **Token Dinamis**:
  Landing page menyuntikkan blok style dinamis `:root` di `<head>`:
  ```css
  :root {
      --wp-container-max-w: 1280px;
      --wp-hero-title-font-size: 48px;
      --wp-hero-desc-font-size: 16px;
      --wp-hero-padding-y: 80px;
      --wp-hero-image-height: 420px;
      --wp-section-gap-y: 64px;
      --wp-card-border-radius: 12px;
      --wp-card-padding: 24px;
      --wp-faq-title-font-size: 18px;
      --wp-footer-padding-y: 64px;
      --wp-color-primary: #1B3860;
      --wp-color-accent: #4F46E5;
      --wp-color-gold: #F59E0B;
  }
  ```
- **Alur Pembaruan**:
  1. SuperAdmin menggeser slider *Ukuran Font Judul Hero* di inspector kanan dari `48px` ke `60px`.
  2. Event `input` slider memanggil `bridge.updateStyle('--wp-hero-title-font-size', '60px')`.
  3. Iframe kanvas mengeksekusi:
     ```javascript
     document.documentElement.style.setProperty('--wp-hero-title-font-size', '60px');
     ```
  4. Peramban langsung mengkalkulasi ulang render pohon DOM seketika (**0 milidetik latensi jaringan**, tanpa reload kanvas).

### 2.6 Pilar 6: Responsivitas Layar (Responsive Viewport Switcher)
- **Mode Pratinjau Tiga Perangkat**:
  - **Desktop**: Bingkai selebar `100%` (maksimal 1440px), border none, bayangan lembut.
  - **Tablet**: Bingkai selebar `768px` x tinggi `1024px`, border `8px solid #2C3338`, border-radius `16px`.
  - **Mobile**: Bingkai selebar `375px` x tinggi `740px`, border `10px solid #2C3338`, border-radius `28px`.
- **Transisi Halus**:
  - Menggunakan kurva animasi CSS `transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1)`.
  - Seluruh *media queries* CSS bawaan landing page (`@media (max-width: 768px)`) di dalam iframe kanvas otomatis aktif sesuai lebar bingkai kanvas saat ini.

### 2.7 Pilar 7: Autosave, Shortcut Keyboard & Toast Notifier
- **Penyimpanan Berbasis AJAX**:
  - Mengirim payload JSON lengkap ke endpoint `/superadmin/site-editor/save-draft` dengan header proteksi `X-CSRF-TOKEN`.
- **Shortcut Keyboard**:
  - Menekan `Ctrl + S` (Windows) atau `Cmd + S` (Mac) langsung memicu penyimpanan draf tanpa harus mengarahkan kursor ke tombol simpan.
- **Indikator Visual Status**:
  - Pill badge di top bar: Berubah warna menjadi kuning saat ada perubahan yang belum disimpan, dan hijau saat berhasil disinkronkan ke basis data.
  - Notifikasi Toast hitam mengambang di pojok kanan bawah dengan ikon centang hijau muncul selama 3,5 detik.

---

## 3. HASIL VERIFIKASI PENGUJIAN OTOMATIS CANVAS MODE

Pengujian feature test khusus canvas mode pada `tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php`:

| ID Skenario Test | Deskripsi Pengujian | Hasil Assertion | Status |
| :--- | :--- | :--- | :--- |
| `TC-CANVAS-01` | SuperAdmin memuat kanvas editor (`/superadmin/site-editor`) | HTTP 200, iframe pratinjau dan inspector ditemukan di DOM | **PASS** |
| `TC-CANVAS-02` | Verifikasi blok landing page memiliki atribut `data-wp-block` | HTTP 200, atribut `hero`, `kuota`, `tahapan`, `faq` valid | **PASS** |
| `TC-CANVAS-03` | Pemuatan runtime kanvas (`landing-canvas-runtime.js`) | Hanya termuat saat parameter query `?editor_mode=1` aktif | **PASS** |
| `TC-CANVAS-04` | Binding token CSS variables dinamis pada `<style>` :root | Token `--wp-hero-title-font-size: 52px;` terpasang tepat | **PASS** |
| `TC-CANVAS-05` | AJAX Save Draft parameter pixel via bridge | HTTP 200, tersimpan di tabel `pengaturan` dengan prefix `draft_` | **PASS** |
| `TC-CANVAS-06` | Validasi batasan min-max slider pixel | HTTP 422 jika nilai di luar rentang aman (misal: font 999px) | **PASS** |
| `TC-CANVAS-07` | Isolasi peran non-SuperAdmin | HTTP 403 Forbidden untuk Admin dan Peserta | **PASS** |

---

## 4. ANALISIS KELEBIHAN & POTENSI PENGEMBANGAN (OPPORTUNITIES)

### Kelebihan Utama Sistem Saat Ini:
1. **Zero-Hardcode & Zero-Reload**: Tampilan dapat disesuaikan tanpa merilis ulang kode (*zero deployment*) dan perubahan terlihat dalam hitungan milidetik.
2. **Kepatuhan Arsitektur MAGKOM**: Kode frontend 100% menggunakan ES Modules modern dan CSS murni di bawah Vite, mematuhi larangan inline JavaScript di file Blade.
3. **Pemisahan Draf Aman (Sandbox)**: Perubahan di kanvas tidak akan pernah merusak tampilan pengunjung publik sebelum tombol *Terbitkan* ditekan secara sadar.

### Rekomendasi Peningkatan Masa Depan (Backlog Opsional):
1. **Drag-and-Drop Reordering**: Menambahkan kemampuan drag-and-drop antar blok di kanvas menggunakan HTML5 Drag API.
2. **Media Picker Modal Terintegrasi**: Menambahkan dialog pemilihan foto banner langsung dari kanvas tanpa harus berpindah ke tab CMS form reguler.
3. **Preset Tema Instan**: Menyediakan tombol 1-klik untuk memilih preset kombinasi warna resmi Pemkab Sleman (misal: *Tema Sleman Sembada*, *Tema Sleman Smart City*, *Tema Sleman Digital Modern*).

---

*Laporan audit khusus Canvas Mode ini disusun untuk memberikan transparansi penuh mengenai mekanisme kerja visual editor WordPress pada repositori MAGKOM.*

