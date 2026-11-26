# Rencana Perbaikan: Tampilan Tablet & Mobile (Celah Sudut Bezel & Proporsi Tinggi Kanvas)

Dokumen ini merinci rencana teknis terstruktur untuk memperbaiki masalah visual pada tampilan Tablet dan Mobile, baik pada **Kanvas Editor SuperAdmin (`/superadmin/site-editor`)** maupun pada **Halaman Publik (`/kebijakan-privasi` dan Landing Page)** berdasarkan tangkapan layar dan investigasi codebase:
1. **Permasalahan 1 (Celah Putih / Tidak Penuh pada Sudut Atas dan Bawah)**: Muncul sabit putih (*white crescent/arc*) di antara bezel perangkat gelap (`#1e293b`) dan header navigasi gelap (`#1B3860`) serta di bagian sudut bawah (sebagaimana terlihat jelas pada perbesaran Gambar 3).
2. **Permasalahan 2 (Proporsi Tinggi & Keterpotongan Atas-Bawah)**: Bingkai perangkat simulasi memiliki batas tinggi kaku (`min-height: 1024px` dan `min-height: 760px`) dengan margin vertikal, yang menyebabkan scroll ganda (scroll kanvas luar dan scroll iframe dalam) serta kesan tampilan "tidak penuh/tidak full" di workspace kanvas editor.
3. **Permasalahan 3 (Kerapatan Konten Responsif pada Layar Mobile/Tablet)**: Konten kartu publik memiliki padding horizontal dan vertikal yang membuat kolom teks pada resolusi 375px menjadi sangat sempit dan menyisakan ruang kosong besar di atas dan di bawah kartu.

---

## 1. Analisis Akar Masalah (Root Cause Analysis)

### 1.1 Akar Masalah Celah Sudut Atas & Bawah (White Crescent Bleed di Sudut Bezel)
1. **Ketidaksesuaian Matematis Radius Sudut Luar vs Radius Sudut Dalam**:
   - Di `resources/css/pages/superadmin/site-editor.css`:
     ```css
     .wp-canvas-frame-container.device-mobile {
         width: 375px;
         min-height: 760px;
         border-radius: 32px;
         border: 10px solid #1e293b;
         background: #ffffff;
     }
     .wp-canvas-frame-container.device-tablet {
         width: 768px;
         min-height: 1024px;
         border-radius: 20px;
         border: 8px solid #1e293b;
         background: #ffffff;
     }
     .wp-preview-iframe {
         border-radius: inherit; /* Mewarisi 32px atau 20px! */
         background: #ffffff;
     }
     ```
   - Dalam spesifikasi W3C CSS Box Model, radius kurva batas dalam (*inner border radius* / *padding edge*) dihitung dengan rumus:
     $$\text{inner\_radius} = \max(0, \text{border\_radius} - \text{border\_width})$$
     - **Mode Mobile**: $32\text{px} - 10\text{px} = 22\text{px}$.
     - **Mode Tablet**: $20\text{px} - 8\text{px} = 12\text{px}$.
   - Karena `.wp-preview-iframe` menggunakan `border-radius: inherit`, iframe tersebut melengkung ke dalam dengan radius **32px** (mobile) dan **20px** (tablet).
   - Selisih kurva sebesar $10\text{px}$ pada mobile dan $8\text{px}$ pada tablet menyebabkan sudut iframe memotong lebih dalam daripada kurva bezel `#1e293b`.
   - Di antara kurva 22px bezel dan kurva 32px iframe, latar belakang container (`background: #ffffff;`) dan latar belakang iframe (`background: #ffffff;`) menjadi terekspos keluar.
   - Karena header website di dalam iframe berwarna biru tua pekat (`#1B3860`), perbedaan warna ini menghasilkan garis lengkung sabit putih yang sangat kontras di sudut kiri-atas, kanan-atas, kiri-bawah, dan kanan-bawah.
2. **Keterbatasan Subpixel Clipping Iframe pada Mesin Chromium/Blink**:
   - Elemen `<iframe>` merupakan *isolated browsing context*. Properti `overflow: hidden` pada elemen induk sering kali mengalami *subpixel anti-aliasing bleeding* kecuali dipaksa menggunakan isolasi layer grafis (`isolation: isolate; transform: translateZ(0); contain: paint;`).

### 1.2 Akar Masalah Tinggi Kanvas & Frame Tidak Penuh (Atas & Bawah)
1. **Konfigurasi Workspace Kanvas**:
   - Di `.wp-canvas-area`, terdapat padding kaku `padding: 24px 20px;` dan `height: 100%; overflow-y: auto;`.
   - `.wp-canvas-frame-container.device-tablet` dan `.device-mobile` memiliki `margin: 12px auto;` dan hanya menentukan `min-height`, tanpa `height: 100%` yang terkoordinasi dengan tinggi viewport kanvas.
   - Hal ini membuat frame perangkat berdiri kaku di tengah ruang kanvas, menyisakan jarak kosong di atas dan di bawah, atau memicu scrollbar ganda.

### 1.3 Akar Masalah Spacing Halaman Publik pada Layar Mobile
1. Di `resources/views/public/kebijakan-privasi.blade.php`:
   - Kontainer `<main>` memiliki padding vertikal `pt-6 pb-12 sm:pt-8 sm:pb-16` dan `px-4 sm:px-6`.
   - Kartu `<article>` memiliki padding dalam `p-6 sm:p-10`.
   - Pada viewport mobile (lebar 375px), total padding horizontal ($16\text{px} \times 2 + 24\text{px} \times 2 = 80\text{px}$) ditambah indentasi pasal `pl-9` (36px) menyisakan area baca efektif hanya sekitar 250px.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. **Fokus Utama Visual**: Indikasi utama user "tidak full tidak penuh, di atas dan di bawah" merujuk pada kebocoran celah putih di sudut lengkungan bezel atas/bawah frame simulator Site Editor (sesuai foto bukti Gambar 3) serta tata letak frame kanvas yang tidak memanfaatkan tinggi area kerja secara optimal.
2. **Zero Breaking Changes**: Modifikasi CSS pada frame simulator editor tidak boleh memengaruhi rendering situs live publik di browser riil pengguna.
3. **Penyelarasan Blade Minimal**: Perubahan pada file Blade publik (`kebijakan-privasi.blade.php`) dibatasi pada utilitas styling responsif (Tailwind classes) tanpa merombak struktur DOM semantik atau business logic.

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter | Opsi A: Presisi Inner Radius + Hardware Clipping + Flex Canvas (Direkomendasikan) | Opsi B: Bezel-less Flat Device Frame (DevTools Mode) |
| :--- | :--- | :--- |
| **Konsep** | Memperbaiki rumus radius sudut dalam matematis ($R_{\text{inner}} = R_{\text{outer}} - W_{\text{border}}$), mengubah background container menjadi transparan/sinkron, menerapkan `isolation: isolate` dan hardware clipping, serta menyempurnakan tinggi frame kanvas. | Menghilangkan bezel tebal ponsel/tablet (`border: 8px/10px`) dan sudut melengkung, menggantinya dengan bingkai persegi flat minimalis (`border: 1px solid #cbd5e1; border-radius: 4px;`). |
| **Kelebihan** | Mempertahankan mockup fisik perangkat yang realistis, kurva sudut 100% rapat tanpa celah putih di atas maupun bawah, tampilan elegan dan profesional. | Zero risiko kebocoran kurva sudut karena sudut siku-siku murni (flat viewport). |
| **Kekurangan** | Memerlukan pengaturan radius spesifik per breakpoint perangkat (`device-tablet` dan `device-mobile`). | Menghilangkan estetika simulasi gawai (*device framing*) yang menjadi fitur visual unggulan Canvas Editor. |
| **Rekomendasi** | **Dipilih Opsi A**, dengan opsi tambahan *toggle* Clean/Flat View jika diinginkan. | Opsi alternatif cadangan. |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/superadmin/site-editor.css` | Modify | 1. Koreksi inner `border-radius` untuk `.wp-preview-iframe` pada mode tablet (12px) dan mobile (22px).<br>2. Set `background: transparent;` pada frame container & iframe untuk mencegah subpixel bleed putih.<br>3. Terapkan `isolation: isolate; contain: paint; transform: translateZ(0);` pada `.wp-canvas-frame-container`.<br>4. Atur proporsi tinggi frame tablet dan mobile agar mengisi kanvas secara optimal tanpa scroll ganda berlebih. |
| `resources/views/public/kebijakan-privasi.blade.php` | Modify | 1. Optimasi padding responsif mobile pada `<main>` (`pt-3 pb-8 sm:pt-8 sm:pb-16 px-2.5 sm:px-6`).<br>2. Optimasi padding kartu pasal di mobile (`p-4 sm:p-10`) dan indentasi teks (`pl-0 sm:pl-9` atau flex layout) agar teks terbaca penuh dan nyaman tanpa terdesak sempit. |
| `tests/Feature/SiteEditorResponsiveCanvasTest.php` | Create | Feature test untuk memastikan route pratinjau editor (`/superadmin/landing/preview`) dan halaman kebijakan privasi me-render aset stylesheet dan tag responsif dengan benar. |

---

## 5. Langkah Implementasi Terstruktur

### Fase 1: Perbaikan Geometri Sudut Bezel & Clipping di Site Editor CSS
- [x] **Langkah 1.1**: Buka `resources/css/pages/superadmin/site-editor.css` pada blok `/* Responsive Frame Containers */`.
- [x] **Langkah 1.2**: Tambahkan variabel CSS atau nilai eksplit inner border radius:
  ```css
  .wp-canvas-frame-container {
      background: transparent;
      isolation: isolate;
      contain: paint;
      transform: translateZ(0);
      -webkit-mask-image: -webkit-radial-gradient(white, black);
  }
  .wp-canvas-frame-container.device-tablet {
      width: 768px;
      border-radius: 20px;
      border: 8px solid #1e293b;
      background: #1B3860; /* Sinkron dengan navbar atas untuk zero bleed */
  }
  .wp-canvas-frame-container.device-tablet .wp-preview-iframe {
      border-radius: 12px; /* 20px - 8px = 12px */
      background: transparent;
  }
  .wp-canvas-frame-container.device-mobile {
      width: 375px;
      border-radius: 32px;
      border: 10px solid #1e293b;
      background: #1B3860; /* Sinkron dengan navbar atas untuk zero bleed */
  }
  .wp-canvas-frame-container.device-mobile .wp-preview-iframe {
      border-radius: 22px; /* 32px - 10px = 22px */
      background: transparent;
  }
  ```
- [x] **Langkah 1.3**: Koreksi tinggi frame pada kanvas editor:
  - Pastikan `.wp-canvas-frame-container.device-mobile` dan `.device-tablet` memiliki batas tinggi yang proporsional terhadap layar (misal `max-height: calc(100vh - 140px)` atau `height: calc(100% - 24px)`) saat berada di dalam area editor agar pas dan tidak terpotong canggung di atas/bawah.

### Fase 2: Penyelarasan Layout Responsif Halaman Kebijakan Privasi
- [x] **Langkah 2.1**: Sesuaikan padding kontainer `<main>` di `kebijakan-privasi.blade.php`:
  - Ubah `pt-6 pb-12 sm:pt-8 sm:pb-16` menjadi responsif yang lebih pas: `pt-3 sm:pt-6 pb-8 sm:pb-16 px-3 sm:px-6`.
- [x] **Langkah 2.2**: Sesuaikan padding kartu konten `#panePrivasi` dan `#paneSyarat`:
  - Ubah `p-6 sm:p-10` menjadi `p-4 sm:p-8 md:p-10`.
  - Sesuaikan margin indentasi `pl-9` pada mobile menjadi `pl-6 sm:pl-9` agar ruang baca teks tidak terjepit pada layar 375px.

### Fase 3: Kompilasi Aset & Pengujian Visual
- [x] **Langkah 3.1**: Jalankan `npm run build` untuk mengompilasi CSS Vite terbaru.
- [x] **Langkah 3.2**: Lakukan pengujian otomatis backend/frontend dengan PHPUnit (`php artisan test`).
- [x] **Langkah 3.3**: Verifikasi visual di browser pada mode Desktop, Tablet (768px), dan Mobile (375px) di Canvas Editor (`/superadmin/site-editor`).
- [x] **Langkah 3.4**: Pastikan sudut kiri-atas, kanan-atas, kiri-bawah, dan kanan-bawah tidak lagi memiliki sabit putih (*white arc*) dan menyatu mulus dengan bezel `#1e293b`.

---

## 6. Strategi Testing

1. **Uji Otomatis (PHPUnit & Feature Test)**:
   - Verifikasi route editor: `GET /superadmin/site-editor` (status 200 untuk superadmin, 403 untuk selain superadmin).
   - Verifikasi route preview: `GET /superadmin/landing/preview?editor_mode=1&page=kebijakan-privasi` (status 200).
   - Verifikasi route publik: `GET /kebijakan-privasi` (status 200).
2. **Uji Visual Responsif (Browser Verification)**:
   - Buka `/superadmin/site-editor`, alihkan ke halaman `Kebijakan Privasi`.
   - Klik tombol viewport **Tablet (768px)**:
     - Periksa sudut atas: Navigasi `#1B3860` harus menempel penuh pada kurva bezel tanpa celah putih.
     - Periksa sudut bawah: Footer `#122846` atau konten kartu harus melengkung rapat dengan inner radius 12px.
   - Klik tombol viewport **Mobile (375px)**:
     - Periksa sudut atas: Navigasi `#1B3860` harus menempel presisi pada inner radius 22px tanpa celah sabit putih.
     - Periksa sudut bawah: Konten menempel rapat tanpa kebocoran warna background.
     - Periksa scroll di dalam iframe: Konten dapat di-scroll lancar tanpa memicu glitch grafis.

---

## 7. Analisis Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Subpixel Antialiasing pada Resolusi Layar Tertentu (High-DPI / Retina)** | Celah 0.5px tipis masih terlihat pada zoom level tertentu di Chrome | Gunakan warna background container `#1B3860` (sama dengan header) sehingga jika ada selisih subpixel pecahan, warna yang terlihat adalah warna navbar yang identik, bukan warna putih. |
| **Scrollbar Native Iframe di Mobile Frame** | Tampilan scrollbar browser default terlihat kaku di dalam mockup iPhone/Android | Tambahkan utility CSS *custom thin scrollbar* untuk iframe preview agar selaras dengan estetika gawai mobile. |
| **Regresi Layout Publik** | Halaman live publik berubah tidak diinginkan pada desktop | Seluruh perbaikan bezel diisolasi di dalam `resources/css/pages/superadmin/site-editor.css`. Perubahan Blade publik hanya menggunakan Tailwind responsive prefix (`sm:`, `md:`) sehingga tampilan desktop tetap 100% identik. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Celah sabit putih (*white arc*) pada keempat sudut bezel frame Tablet dan Mobile di Canvas Editor telah hilang 100%.
- [x] Header `#1B3860` di bagian atas dan footer/konten di bagian bawah menempel penuh (*flush*) dan mulus pada kurva bezel perangkat.
- [x] Tinggi dan proporsi frame simulasi di area kerja kanvas proporsional, nyaman digunakan, dan tidak terpotong canggung di atas/bawah.
- [x] Tampilan halaman Kebijakan Privasi pada layar mobile (375px) dan tablet (768px) proporsional, teks pasal leluasa dibaca, dan tidak terhimpit padding berlebih.
- [x] Aset Vite berhasil dikompilasi dengan `npm run build` tanpa error.
- [x] Seluruh test suite PHPUnit yang relevan berjalan hijau (passing 100%).
