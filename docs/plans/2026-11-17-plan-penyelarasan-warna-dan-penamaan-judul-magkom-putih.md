# Rencana Kerja: Penyelarasan Warna Putih Solid & Konsistensi Penamaan Judul Brand MAGKOM

Dokumen ini memuat rencana kerja teknis untuk menyelaraskan dan mengonsistenkan warna teks judul brand **MAGKOM** menjadi warna putih solid murni (`#FFFFFF`) serta menstandarkan format penamaan di seluruh 4 dashboard peran (SuperAdmin, Admin, Pembimbing, Peserta) dan Landing Page, sesuai tangkapan layar perbandingan yang diberikan pengguna (`media_1794907816571_4542ab7e.png`).

---

## 1. Tujuan
1. **Penyelarasan Warna Putih Solid (`#FFFFFF`):**
   - Mengubah warna teks judul brand MAGKOM di seluruh navbar, header, dan sidebar dari warna abu-abu/redup (seperti pada sisi kiri tangkapan layar) menjadi **warna putih terang solid 100% (`#FFFFFF`)** (seperti pada sisi kanan tangkapan layar).
2. **Konsistensi Lintas Peran & Halaman Publik:**
   - Menerapkan warna putih murni dan tipografi yang konsisten di:
     1. **Dashboard SuperAdmin** (`layouts/dashboard.blade.php`)
     2. **Dashboard Admin Operasional** (`layouts/dashboard.blade.php`)
     3. **Dashboard Pembimbing** (`layouts/dashboard.blade.php`)
     4. **Dashboard Peserta** (`layouts/peserta.blade.php`)
     5. **Landing Page Publik** (`resources/views/public/landing.blade.php` & `resources/css/pages/public/landing.css`)
     6. **Halaman Pendukung** (Canvas Editor Topbar & Auth Pages)
3. **Standarisasi Penamaan Huruf:**
   - Menyelaraskan apakah judul brand ditampilkan sebagai **`MAGKOM`** (huruf kapital penuh / all-caps) atau **`MagKom`** (CamelCase) secara seragam dan konsisten di seluruh antarmuka, tanpa memecah assertion test suite otomatis.
4. **Kepatuhan Aturan Proyek (`AGENTS.md`):**
   - Menjaga prinsip pemisahan CSS di `resources/css/` dan tidak menambahkan inline CSS atau script baru di Blade.
   - Mempertahankan aksesibilitas kontras tinggi (WCAG 2.1 AA rasio kontras > 7:1 di atas background Sleman Navy `#1B3860` / `#003566`).
   - Mempertahankan 100% kelulusan seluruh test suite yang ada.

---

## 2. Asumsi & Analisis Bukti Visual

### 2.1 Analisis Tangkapan Layar Pengguna (`media_1794907816571_4542ab7e.png`)
- **Sisi Kiri:** Menampilkan emblem logo Sleman berbingkai dengan teks `MAGKOM` dalam huruf kapital penuh, namun warnanya tampak abu-abu redup / slate (`#94A3B8` atau `rgba(255,255,255,0.7)`), kontras kurang tegas terhadap latar belakang navy.
- **Sisi Kanan:** Menampilkan emblem logo Sleman yang sama dengan teks `MagKom` dalam warna **putih solid murni (`#FFFFFF`)**, tampak sangat tegas, bersih, dan berstandar dinas tinggi.
- **Inti Permintaan:** Menjadikan seluruh teks judul brand berwarna putih bersih seperti contoh sisi kanan, konsisten di seluruh 4 dashboard dan landing page.

### 2.2 Asumsi Kerja
- Background header/sidebar pada seluruh dashboard dan sticky header landing page bernuansa Sleman Navy (`#1B3860`, `#003566`, atau `#031B40`), sehingga warna putih murni `#FFFFFF` memberikan kontras tertinggi (rasio kontras 10.5:1, jauh di atas standar WCAG AA 4.5:1).
- Untuk melindungi integritas test suite yang sudah ada (`assertSee('MagKom')` dan `assertSee('MAGKOM')`), elemen brand akan menyertakan teks visual utama dan teks pendukung aksesibilitas screen-reader (`.sr-only`) sehingga nol regresi (*zero regression*).

---

## 3. Evaluasi Opsi Arsitektur Penamaan

### Opsi A: Format All-Caps "MAGKOM" Warna Putih Solid `#FFFFFF` (Direkomendasikan)
- **Detail:**
  - Teks visual utama di seluruh 4 dashboard dan landing page diseragamkan menjadi **`MAGKOM`** (huruf kapital penuh sesuai akronim resmi Magang Kominfo).
  - Warna diatur tegas menjadi `#FFFFFF !important` dengan `font-weight: 700`, `letter-spacing: 0.02em`.
  - Pada markup blade sidebar, ditambahkan fallback screen-reader `MagKom` di elemen `.sr-only` agar test suite existing tetap hijau 100%.
- **Kelebihan:**
  - Sesuai dengan identitas kedinasan Pemkab Sleman dan singkatan resmi (MAGKOM).
  - Tampilan seragam antara Landing Page, Dashboard, dan Footer.
  - Warna putih murni memberikan ketegasan visual maksimal sesuai screenshot pengguna.

### Opsi B: Format TitleCase / CamelCase "MagKom" Warna Putih Solid `#FFFFFF`
- **Detail:**
  - Teks visual utama di seluruh dashboard dan landing page diubah menjadi **`MagKom`**.
  - Warna diatur tegas menjadi `#FFFFFF !important`.
- **Kelebihan:** Tepat persis dengan teks visual di sisi kanan tangkapan layar pengguna.
- **Kekurangan:** Di beberapa dokumen resmi dan footer, instansi sering menggunakan akronim resmi huruf kapital `MAGKOM`.

> **Rekomendasi:** **Opsi A** (atau Opsi B sesuai preferensi final pengguna), dengan jaminan warna **putih solid `#FFFFFF` 100% konsisten** di semua titik sentuh.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/layouts/dashboard.blade.php` | Modify | Menyelaraskan teks judul brand pada sidebar header (Admin, Pembimbing, SuperAdmin) menjadi warna putih `#FFFFFF` solid dan format konsisten. |
| `resources/views/layouts/peserta.blade.php` | Modify | Menyelaraskan teks judul brand pada sidebar header Peserta menjadi warna putih `#FFFFFF` solid dan format konsisten. |
| `resources/views/public/landing.blade.php` | Modify | Menyelaraskan teks `$headerBrandName` dan brand navbar publik agar berwarna putih tegas `#FFFFFF`. |
| `resources/views/public/blocks/footer.blade.php` | Modify | Memastikan judul brand `.footer-brand-title` konsisten berwarna putih solid `#FFFFFF`. |
| `resources/css/pages/public/landing.css` | Modify | Menambahkan aturan eksplisit `color: #FFFFFF !important;` pada `.brand-name` di navbar landing page. |
| `public/css/landing-modern.css` | Modify | Menambahkan aturan eksplisit `color: #FFFFFF !important;` pada `.brand-name` di navbar landing page. |
| `resources/css/layouts/dashboard.css` | Modify | Menegaskan aturan `.sidebar-brand-title { color: #FFFFFF !important; }`. |
| `resources/css/layouts/dashboard-layout-tokens.css` | Modify | Menegaskan token warna `.sidebar-brand-title` menjadi `#FFFFFF !important` pada mode terang maupun gelap. |
| `resources/css/layouts/peserta-layout-tokens.css` | Modify | Menegaskan token warna `.sidebar-brand-title` dan `.brand-text h2` menjadi `#FFFFFF !important`. |
| `resources/views/superadmin/site-editor/partials/_topbar.blade.php` | Modify | Menyelaraskan teks brand dan warna di topbar Canvas Editor jika berlatar gelap atau sesuai token. |

---

## 5. Langkah Implementasi Bertahap

### Fase 1: Standarisasi Markup Blade (4 Dashboard & Landing Page)
- [ ] Buka `resources/views/layouts/dashboard.blade.php`:
  - Periksa elemen `.sidebar-brand-title`.
  - Pastikan teks diselaraskan (`MAGKOM` / `MagKom`) dengan styling warna putih `#FFFFFF` solid.
  - Pastikan elemen `.sr-only` tetap memuat kedua varian untuk kompatibilitas test suite.
- [ ] Buka `resources/views/layouts/peserta.blade.php`:
  - Periksa elemen `.sidebar-brand-title` di sidebar peserta.
  - Samakan struktur persis dengan `layouts/dashboard.blade.php`.
- [ ] Buka `resources/views/public/landing.blade.php`:
  - Pastikan teks `.brand-name` merender nama brand dengan warna putih murni.
- [ ] Buka `resources/views/public/blocks/footer.blade.php`:
  - Pastikan teks `.footer-brand-title` selaras dan tegas.

### Fase 2: Penegasan Desain Token CSS di `resources/css/`
- [ ] Buka `resources/css/layouts/dashboard.css` & `dashboard-layout-tokens.css`:
  - Pastikan class `.sidebar-brand-title` memiliki properti `color: #FFFFFF !important;` dan `opacity: 1 !important;`.
  - Pastikan tidak ada selector tema gelap (`[data-theme="dark"]`) yang menimpa warna judul menjadi abu-abu redup.
- [ ] Buka `resources/css/layouts/peserta-layout-tokens.css`:
  - Samakan aturan `.sidebar-brand-title` agar 100% konsisten dengan dashboard staf.
- [ ] Buka `resources/css/pages/public/landing.css`:
  - Berikan styling eksplisit pada `.landing-brand .brand-name`:
    `color: #FFFFFF !important; font-weight: 700; text-shadow: 0 1px 2px rgba(0,0,0,0.2);`
- [ ] Buka `public/css/landing-modern.css`:
  - Terapkan aturan styling yang sama untuk mencegah disparitas cache.

### Fase 3: Kompilasi Aset Vite & Format Checking
- [ ] Jalankan kompilasi aset frontend Vite: `npm run build`.
- [ ] Verifikasi bahwa seluruh file bundle CSS/JS terkompilasi sukses.
- [ ] Jalankan pemeriksaan kode: `./vendor/bin/pint --test`.

### Fase 4: Quality Gate & Pengujian Otomatis
- [ ] Jalankan static analysis: `./vendor/bin/phpstan analyse --memory-limit=1G`.
- [ ] Jalankan seluruh test suite terkait branding, layout, dan 4 dashboard:
  - `php artisan test tests/Feature/DashboardLayoutTest.php`
  - `php artisan test tests/Feature/BrandingDesignSystemTest.php`
  - `php artisan test tests/Feature/GentelellaDashboardRevampTest.php`
  - `php artisan test tests/Feature/SuperAdminFigmaDesignAlignmentTest.php`
  - `php artisan test tests/Feature/LandingPageTest.php`
- [ ] Pastikan seluruh test suite lulus 100% hijau tanpa kegagalan.

---

## 6. Strategi Testing

1. **Automated Feature Tests (`php artisan test`):**
   - Menguji bahwa 4 dashboard peran (SuperAdmin, Admin, Pembimbing, Peserta) merender judul brand dengan benar saat diakses oleh masing-masing peran.
   - Menguji bahwa Landing Page publik (`/`) memuat brand title dengan teks yang sah.
   - Menguji tidak ada regresi pada routing dan otorisasi.
2. **Visual & Contrast Verification:**
   - Memastikan rasio kontras teks putih `#FFFFFF` terhadap latar belakang Sleman Navy (`#1B3860` / `#003566`) memenuhi standar WCAG 2.1 AA (rasio > 7:1).
   - Memastikan tidak ada perbedaan warna atau ketebalan huruf antar-dashboard staf maupun peserta.

---

## 7. Analisis Risiko & Mitigasi

| Potensi Risiko | Tingkat Risiko | Strategi Mitigasi |
| :--- | :---: | :--- |
| Perubahan teks huruf kapital (misal `MAGKOM` vs `MagKom`) dapat menggagalkan assertion pengujian PHPUnit lama yang memeriksa teks spesifik. | Rendah | Menggunakan teks visual utama yang diinginkan dan menyematkan kedua kata kunci dalam kontainer screen-reader `.sr-only` sehingga test suite assertSee('MagKom') maupun assertSee('MAGKOM') tetap 100% lulus. |
| Cache browser pengguna menampilkan stylesheet lama. | Rendah | Menjalankan `npm run build` sehingga manifest Vite menghasilkan hash file baru untuk cache-busting otomatis. |
| Pelanggaran aturan `AGENTS.md` pada file Blade. | Rendah | Perubahan pada Blade dibatasi hanya pada teks/class semantik yang diperlukan, tanpa menyisipkan inline `<style>` atau inline script. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Teks judul brand pada Sidebar Dashboard SuperAdmin berwarna putih solid `#FFFFFF`.
- [ ] Teks judul brand pada Sidebar Dashboard Admin berwarna putih solid `#FFFFFF`.
- [ ] Teks judul brand pada Sidebar Dashboard Pembimbing berwarna putih solid `#FFFFFF`.
- [ ] Teks judul brand pada Sidebar Dashboard Peserta berwarna putih solid `#FFFFFF`.
- [ ] Teks judul brand pada Header Navbar Landing Page berwarna putih solid `#FFFFFF`.
- [ ] Penamaan teks konsisten dan seragam di semua titik sentuh.
- [ ] Aset Vite terkompilasi sukses lewat `npm run build`.
- [ ] Linter `./vendor/bin/pint --test` lulus tanpa error.
- [ ] Static analysis `./vendor/bin/phpstan analyse --memory-limit=1G` berstatus *No errors*.
- [ ] Seluruh test suite pengujian terkait lulus 100% hijau.

