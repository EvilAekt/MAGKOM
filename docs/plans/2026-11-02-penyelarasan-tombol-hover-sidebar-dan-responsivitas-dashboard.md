# Rencana Kerja: Penyelarasan Tombol, Efek Hover, Warna, Sidebar SuperAdmin, dan Responsivitas Lintas Perangkat Portal Peserta

Dokumen ini merupakan rencana kerja teknis untuk menyelaraskan seluruh tombol (*button styling* & *hover effects*), palet warna, sidebar navigasi ala SuperAdmin, serta optimasi kecocokan tata letak (*adaptive fit & responsive layout*) pada berbagai ukuran perangkat (desktop, laptop, tablet, dan ponsel) agar tidak terjadi layout shift, clipping, atau tampilan janggal.

---

## 1. Tujuan

1. **Penyelarasan Tombol & Efek Hover (Referensi Gambar 1: Tombol "Kelola Kuota &rarr;")**:
   - Menyelaraskan seluruh tombol utama (*primary action buttons*, link aksi, dan tombol formulir) di seluruh portal peserta ke standar tombol SuperAdmin: solid Sleman Navy (`#01347C`), teks putih kontras tinggi, radius 8px, panah indikator transisi halus, dan efek hover mengangkat elegan (`translateY(-1px)` dengan bayangan lembut `rgba(1, 52, 124, 0.28)`).
2. **Penyelarasan Sidebar Peserta ala SuperAdmin (Referensi Gambar 2: Sidebar SuperAdmin)**:
   - Mengadopsi visual hierarchy sidebar SuperAdmin: background Sleman Navy (`#002244` / `#003566`), group label section tegas (`OPERATIONAL MODULE`, `SYSTEM` / `MENU UTAMA`, `PELAKSANAAN MAGANG`), icon navigasi presisi.
   - Menyempurnakan status aktif & hover item menu: background transparan melengkung (`rgba(255, 255, 255, 0.08)` / `rgba(63, 81, 181, 0.25)`), border aksen halus, teks putih terang, dan **ikon aktif emas Sleman (`#FFCB05`)** seperti menu "Rubrik Nilai" pada tangkapan layar.
3. **Optimasi Fit Responsif Lintas Perangkat (*Adaptive Responsive Layout*)**:
   - Mencegah tampilan aneh/pecah di berbagai perangkat:
     - **Desktop Lebar (> 1280px)**: Tata letak cockpit 2-kolom seimbang (*main content* + *aside* 340px) tanpa whitespace berlebih.
     - **Laptop Standar (1024px – 1279px)**: Statusline ribbon wrap dinamis, hero banner fluid, KPI card 4-kolom atau 2x2 tanpa pemotongan teks.
     - **Tablet (768px – 1023px)**: Grid KPI otomatis 2x2, kolom aside mengalir rapi di bawah main content (100% width).
     - **Ponsel Pintar (< 768px)**: Hero banner padding kompak, action button touch-friendly (44px min height & full width), tabel berbungkus `overflow-x: auto` halus, drawer sidebar slide-in dengan backdrop blur tanpa horizontal overflow.

---

## 2. Asumsi & Analisis Teknis

- **Asumsi 1**: Tidak mengubah arsitektur route atau business logic backend; semua controller dan data binding yang sudah dibuat tetap dipertahankan.
- **Asumsi 2**: Tetap mendukung mode terang (*Light Mode*) dan mode gelap (*Dark Mode*) dengan rasio kontras WCAG 2.2 AA.
- **Asumsi 3**: Sidebar peserta tetap mempertahankan item menu hak akses peserta (menu terkunci / *locked items* dengan ikon gembok jika belum penempatan).

---

## 3. Pendekatan Teknis

### Opsi A: Modifikasi inline terpisah per view
- *Kelemahan*: Rentan inkonsistensi antar halaman dan sulit dipelihara.

### Opsi B: Standardisasi Global Token & Komponen CSS di Layout + CSS Shared (Direkomendasikan)
- *Keunggulan*:
  - Mengonsolidasikan kelas tombol utama (`.btn-peserta-primary`, `.saas-hero-btn-primary`, `.saas-btn-primary`) ke definisi terpusat di `resources/views/layouts/peserta.blade.php` atau `dashboard-cockpit-unified.css`.
  - Mengupdate style `.sidebar`, `.nav-item`, `.nav-item.active`, dan `.nav-item:hover` di layout utama agar langsung berlaku di seluruh halaman portal peserta.
  - Memperkuat media queries responsif pada level layout (`.main-wrapper`, `.content-area`, `.saas-cockpit-layout`, `.tile_count`) sehingga seluruh halaman (Dashboard, Biodata, Pendaftaran, Tugas, Logbook, Nilai) otomatis fit dan proporsional di semua perangkat.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/layouts/peserta.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/peserta.blade.php) | **Modify** | 1. Redesain visual sidebar ala SuperAdmin (icon emas `#FFCB05` saat aktif, background hover/active rounded pills, group headers).<br>2. Definisi global button system (`.btn-peserta-primary`, `.btn-peserta-secondary`, hover elevate, focus ring).<br>3. Media queries breakpoints (1200px, 1024px, 768px, 640px, 480px) untuk menjamin fit di semua device. |
| [`resources/views/peserta/dashboard.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard.blade.php) | **Modify** | 1. Selaraskan seluruh tombol aksi (`.saas-hero-btn-primary`, `.saas-btn-primary`, tombol unduh berkas/buka tugas) dengan format Gambar 1.<br>2. Perbaiki grid cockpit layout (`.saas-cockpit-layout`) agar stack rapi di layar &le; 1024px dan tidak terjepit di laptop 1366px.<br>3. Optimasi padding dan font-size kartu statusline ribbon agar wrap fleksibel. |
| [`resources/views/peserta/dashboard/partials/_header.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_header.blade.php) | **Modify** | Selaraskan tombol aksi di hero banner (tombol "+ Isi Logbook Mingguan", "Lihat Tugas", dan "Unduh SK Magang") dengan styling tombol solid SuperAdmin & efek hover yang konsisten. |
| [`resources/views/peserta/dashboard/partials/_kpi_bar.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_kpi_bar.blade.php) | **Modify** | Optimasi tata letak responsif 4 kartu metrik (tile count) agar rapi di tablet (2x2) dan mobile (stack 1 kolom atau 2 kolom ringkas). |
| [`resources/views/peserta/dashboard/partials/_aside.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_aside.blade.php) | **Modify** | Selaraskan tombol kontak WhatsApp pembimbing dan aksi cepat di aside dengan tombol navy SuperAdmin. |
| [`resources/views/peserta/profil/show.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/profil/show.blade.php) | **Modify** | 1. Selaraskan tombol "Simpan Biodata Profil", "Pilih Pasfoto", dan tombol aksi lainnya.<br>2. Pastikan grid 2-kolom identitas profil berubah menjadi 1-kolom proporsional di layar &le; 1024px tanpa clipping. |

---

## 5. Langkah Implementasi Terperinci

### Tahap 1: Standardisasi Button System & Efek Hover (Gambar 1)
- [x] **Langkah 1.1**: Buat/perbarui utilitas tombol `.btn-peserta-primary` dan `.saas-hero-btn-primary` di [`resources/views/layouts/peserta.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/peserta.blade.php):
  - Warna dasar: Solid Sleman Navy `#01347C`.
  - Warna teks: Putih `#FFFFFF`, font-weight 700, font-family Lexend/Open Sans.
  - Border radius: 8px, border 1px solid rgba(255, 255, 255, 0.15).
  - Indikator panah `&rarr;` atau ikon dengan transisi halus `transform: translateX(3px)`.
  - Efek Hover: Background `#003566` / `#002244`, `transform: translateY(-1.5px)`, bayangan halus `box-shadow: 0 4px 14px rgba(1, 52, 124, 0.28)`.
  - Efek Active: `transform: translateY(0)`.
- [x] **Langkah 1.2**: Terapkan tombol ini secara konsisten pada Hero Banner dashboard, kartu aksi pendaftaran, kartuaside pembimbing, dan formulir profil.

### Tahap 2: Penyelarasan Sidebar Navigasi ala SuperAdmin (Gambar 2)
- [x] **Langkah 2.1**: Update styling `.sidebar` di [`resources/views/layouts/peserta.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/peserta.blade.php):
  - Background sidebar: Sleman Navy `#002244` (atau `#003566` di mode terang dan gelap), border-right `1px solid rgba(255, 255, 255, 0.1)`.
- [x] **Langkah 2.2**: Update `.nav-label` (Group Header) menjadi huruf kapital kecil (*small uppercase*), letter-spacing `0.06em`, warna teks muted slate `#94A3B8`.
- [x] **Langkah 2.3**: Update `.nav-item` & `.nav-item.active`:
  - Item aktif mengadopsi background `rgba(255, 255, 255, 0.08)`, border `1px solid rgba(255, 255, 255, 0.15)`, sudut rounded-lg (8px).
  - Teks aktif: `#FFFFFF` dengan font-weight 700.
  - **Ikon menu aktif: Aksen emas Sleman `#FFCB05`** (seperti ikon "Rubrik Nilai" di Gambar 2).
  - Item hover: Background `rgba(255, 255, 255, 0.05)`, teks `#FFFFFF`.
- [x] **Langkah 2.4**: Pertahankan toolbar footer sidebar (Pengaturan, Theme Switcher, Logout) dengan ikon 18px rapi dan circle button transparan hover-active.

### Tahap 3: Fit & Responsivitas Dashboard Lintas Perangkat
- [x] **Langkah 3.1 (Desktop & Laptop 1024px – 1366px)**:
  - Periksa `.saas-cockpit-layout` di [`resources/views/peserta/dashboard.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard.blade.php):
    - Pada layar lebar (&ge; 1200px): Grid 2 kolom `minmax(0, 1fr) 340px`.
    - Pada layar laptop/tablet (&le; 1199px): Otomatis menjadi 1 kolom (`grid-template-columns: 1fr`), aside pindah ke bawah main content tanpa memotong teks.
  - Statusline Ribbon: Gunakan `flex-wrap: wrap; gap: 12px;` dengan `min-width: 0` agar setiap metrik ribbon tidak overflow.
- [x] **Langkah 3.2 (Tablet 768px – 1023px)**:
  - Metrik KPI Grid (`.saas-kpi-grid` / `.tile_count`):
    - Terapkan `grid-template-columns: repeat(2, 1fr); gap: 14px;` agar 4 metrik KPI tertata 2 baris x 2 kolom secara simetris dan rapi.
  - Hero banner: Susunan tombol aksi wrap ke bawah dengan gap 10px.
- [x] **Langkah 3.3 (Mobile HP < 768px)**:
  - Padding `.content-area`: Sesuaikan dari `32px` ke `16px 12px` untuk memaksimalkan ruang baca di layar ponsel.
  - Hero banner: Sesuaikan padding menjadi `20px 16px`, ukuran judul `1.35rem`, teks subjudul `0.85rem`.
  - Tombol aksi di mobile: Menjadi `width: 100%` dengan teks tengah agar sangat nyaman disentuh jari (*mobile-friendly tap targets* &ge; 44px).
  - Tabel (roster kelompok, riwayat, logbook): Tambahkan wrapper `overflow-x: auto; -webkit-overflow-scrolling: touch;` dengan border-radius rapi agar tidak menimbulkan horizontal scrollbar pada jendela browser utama (*zero body overflow*).
  - Sidebar mobile drawer: Animasi buka-tutup slide-in dari kiri dengan `backdrop-filter: blur(4px)` dan tombol close yang responsif.

### Tahap 4: Verifikasi & Testing
- [x] **Langkah 4.1**: Jalankan unit test dan feature test:
  - `php artisan test tests/Feature/PendaftaranKelompokTest.php`
  - `php artisan test tests/Feature/PesertaFullPortalFeatureTest.php`
- [x] **Langkah 4.2**: Pengujian visual di Chrome / DevTools pada breakpoint:
  - Desktop 1920x1080 & 1366x768
  - Tablet iPad 1024x768 & 768x1024
  - Mobile iPhone / Android 375x667 & 414x896
- [x] **Langkah 4.3**: Uji peralihan Mode Terang (*Light Mode*) dan Mode Gelap (*Dark Mode*) pada sidebar dan tombol.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Seluruh tombol aksi di portal peserta memiliki warna solid navy `#01347C`, font tebal kontras putih, dan efek hover mengangkat elegan sesuai Gambar 1.
- [x] Sidebar navigasi peserta mengadopsi struktur, warna, dan status aktif dengan ikon emas `#FFCB05` sesuai Gambar 2.
- [x] Dashboard dan halaman peserta tampil pas (*perfect fit*) di desktop, laptop 1366px, tablet, dan smartphone tanpa horizontal overflow atau teks terpotong.
- [x] Seluruh automated feature test (`PendaftaranKelompokTest` & `PesertaFullPortalFeatureTest`) lulus 100% (0 error).
