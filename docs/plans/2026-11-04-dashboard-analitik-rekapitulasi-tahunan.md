# Rencana Kerja: Pembaruan & Penataan Modern Dashboard Analitik Rekapitulasi Tahunan 2026

- **Tanggal**: 2026-11-04
- **Status**: Selesai / Siap Di-review
- **Dokumen Referensi**: 
  - `resources/views/admin/laporan/tahunan.blade.php`
  - `.agents/rules/AGENTS.md`
  - Lampiran Pengguna:
    - *Gambar 1*: Kondisi layout saat ini (`media_1793725308684_d1e70cdb.jpg`)
    - *Gambar 2*: Kolom "Grafik Proporsi" di tabel bulanan (`media_1793725311104_d09bfa7e.png`)
    - *Gambar 3*: Referensi grafik "Plan growth" (Stacked/curved area chart) (`media_1793725313032_baec2546.jpg`)
    - *Gambar 4*: Tombol navigasi Hub Laporan & Unduh saat ini (`media_1793725314826_3451e027.png`)

---

## 1. Tujuan
Menata ulang dan memodernisasi antarmuka **Dashboard Analitik Rekapitulasi Tahunan** (`admin.laporan.tahunan`) agar:
1. **Tampilan & Layout Terstruktur Rapi**: Menyelaraskan seluruh container, toolbar, dan kartu KPI dengan standar desain sistem MagKom (top accent border, tipografi tegas, padding proporsional, serta pembersihan inline styles ke file CSS terdedikasi sesuai `AGENTS.md`).
2. **Visualisasi Grafik Tren "Plan Growth"**: Menghadirkan visualisasi kurva area bertingkat (*stacked/smooth area chart*) bergaya *Plan Growth* (Januari – Desember) dengan kurva halus, gradien warna transparan semi-modern (Pendaftar, Diterima, Lulus/Selesai), interaksi hover tooltip, serta mempercantik kolom proporsi pada tabel bulanan.
3. **Tombol Navigasi & Aksi Elegan**: Merancang ulang tombol `← Hub Laporan Utama` (ghost/outline berkelas) dan tombol `Unduh Rekap` (solid Sleman Navy `#01347C` dengan drop shadow halus dan micro-interaction) tanpa garis bawah tautan yang mengganggu.
4. **Kepatuhan Penuh AGENTS.md**: Mempertahankan seluruh aturan otorisasi keamanan SuperAdmin (Asumsi A-20 terkait proteksi rekap nilai), kontrak route, integrasi Vite, serta pengujian otomatis PHPUnit yang sudah ada.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Data Sumber dari Controller**: Data bulanan sudah tersedia lengkap dalam variabel `$trenBulanan` dari [`AnalitikTahunanController`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/AnalitikTahunanController.php), berisi data pendaftar, diterima, selesai, dan rasio untuk 12 bulan (Januari s.d. Desember).
2. **Zero New External Dependencies**: Sesuai `AGENTS.md` Bagian 2, tidak boleh menambahkan library charting baru (seperti Chart.js atau ApexCharts via npm/CDN). Visualisasi *Plan Growth* akan dibangun menggunakan komponen vektor **Pure SVG + Vanilla JavaScript (ES Module)** yang ringan, responsif, tajam di semua resolusi layar, dan kompatibel 100% dengan pipeline Vite.
3. **Preservasi Keamanan A-20**: Fitur pelindung skor kelulusan bagi peran Admin (`Rekapitulasi Mutu Nilai Dilindungi`) dan hak akses SuperAdmin (`Sebaran Predikat & Mutu Nilai`) wajib dipertahankan tanpa perubahan kontrak hak akses.
4. **Dual Theme (Light & Dark)**: Komponen grafik dan layout akan mendukung tema terang (*Light Mode*) dan gelap (*Dark Mode*) secara dinamis mengikuti preferensi tema aplikasi.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Arsitektur:

| Kriteria | Opsi 1: Pure SVG Vector Chart (Vanilla JS & CSS) | Opsi 2: Menginstal Library Chart.js / ApexCharts |
| :--- | :--- | :--- |
| **Kepatuhan Aturan Proyek** | ✅ **100% Patuh AGENTS.md** (Vanilla JS ES Module, tanpa dependensi eksternal). | ❌ **Melanggar AGENTS.md** (menambah dependensi bundler/npm tanpa alasan mendesak). |
| **Ukuran & Performa** | Sangat ringan (~4KB SVG builder), zero overhead, rendering instan. | Berat (+150KB bundle), potensi delay inisialisasi di browser lambat. |
| **Tampilan "Plan Growth"** | **Sangat Presisi**: Menghasilkan kurva kubik Bézier (`d="M... C..."`) dengan gradient fill semi-transparan identik dengan referensi gambar 3. | Bergantung pada tema dan konfigurasi default library. |
| **Dark Mode & Interaktivitas** | Terintegrasi langsung dengan CSS custom properties (`var(--blue-600)`, dll.) dan tooltip native. | Memerlukan canvas repaint dan listener manual saat ganti tema. |
| **Rekomendasi** | **SANGAT DIREKOMENDASIKAN (Opsi 1)** | Tidak direkomendasikan |

### Detail Perubahan Desain:

#### A. Header & Toolbar Tahun (Refinement Tombol & Filter)
- **Container**: Card panel putih bersih dengan radius 12px, border halus `rgba(1, 52, 124, 0.08)`, dan bayangan lembut.
- **Tombol `← Hub Laporan Utama`**:
  - Didesain sebagai tombol navigasi sekunder berkelas (*refined outline pill*).
  - Background soft `#FFFFFF` (dark: `#1E293B`), border `1px solid rgba(1, 52, 124, 0.18)`.
  - Dilengkapi ikon SVG panah kiri yang bergeser halus saat hover (`transform: translateX(-3px)`).
  - Teks jelas, tebal, tanpa garis bawah (*no underline*).
- **Tombol `Unduh Rekap CSV (Sanitized)`**:
  - Tombol aksi primer solid Sleman Navy `#01347C` dengan aksen border Royal Blue `#3F51B5`.
  - Dilengkapi ikon download berkas SVG profesional dan drop shadow halus `0 2px 8px rgba(1, 52, 124, 0.25)`.
  - Efek hover interaktif (`transform: translateY(-1px)`).

#### B. 4 Executive Summary Hero KPI Cards (Harmonisasi SuperAdmin Standard)
- Mengadopsi standar kartu metrik penempatan yang baru saja diselaraskan:
  1. **Total Pendaftar Masuk**: Top border Sleman Navy `#01347C`, angka `3`, label pendaftar yang mengajukan berkas.
  2. **Peserta Diterima Magang**: Top border Emerald Green `#10B981`, badge acceptance rate `66.7%`.
  3. **Alumni Magang Lulus**: Top border Purple / Indigo `#8B5CF6`, badge completion rate `0.0%`.
  4. **Rata-rata Durasi Magang**: Top border Amber Gold `#F59E0B`, highlight durasi `3 Bulan`.

#### C. Komponen Visualisasi "Plan Growth" (Area Chart SVG Bertingkat)
- Diletakkan di atas tabel penyerapan bulanan di dalam kartu section utama:
  - **Dimensi**: Responsif 100% width dengan viewBox adaptif (misal: 900x260).
  - **Sumbu & Grid**: Garis grid horizontal halus dengan label sumbu Y (skala pendaftar).
  - **Sumbu X**: Label 12 bulan (Jan, Feb, Mar, Apr, Mei, Jun, Jul, Agu, Sep, Okt, Nov, Des).
  - **3 Series Area Bergradasi (Sesuai Referensi Gambar 3)**:
    - **Pendaftar Masuk**: Kurva hijau zamrud (*Emerald*) dengan fill gradasi `rgba(16, 185, 129, 0.25)` ke transparan.
    - **Peserta Diterima**: Kurva biru safir (*Royal Blue*) dengan fill gradasi `rgba(63, 81, 181, 0.25)` ke transparan.
    - **Alumni Lulus**: Kurva oranye emas (*Amber/Sunset*) dengan fill gradasi `rgba(245, 158, 11, 0.25)` ke transparan.
  - **Interaktivitas Tooltip**: Saat kursor digerakkan di atas bulan tertentu, muncul garis vertikal panduan (*crosshair*) dan floating badge yang menampilkan rincian angka bulan tersebut.
  - **Legend Card**: Penanda titik bulat berwarna di bagian bawah grafik:
    - 🟢 Pendaftar Masuk
    - 🔵 Peserta Diterima
    - 🟠 Alumni Lulus / Selesai

#### D. Penataan Tabel Tren Bulanan & Kolom "Grafik Proporsi"
- Kolom "Grafik Proporsi" yang saat ini hanya berupa garis tipis 10px abu-abu kosong ditingkatkan menjadi:
  - **Segmented Proportional Bar**: Menampilkan segmen proporsi penerimaan (persentase lolos vs belum lolos) dengan bar halus berujung membulat (*rounded-full*), warna bergradasi navy/emerald, dan indikator persentase interaktif.
- Header tabel dengan background soft neutral, tipografi tegas, padding rapi, dan border pembatas yang bersih.

#### E. Grid Bawah (Top 10 Institusi & Distribusi Jenjang)
- **Top 10 Institusi**: Tabel clean scrollable dengan pill badge persentase keterimaan.
- **Distribusi Jenjang**: Kartu SMK, D3, S1 yang proporsional dengan aksen warna identitas masing-masing jenjang.
- **Proteksi Mutu Nilai (A-20)**: Banner proteksi kunci gembok untuk admin tetap dipertahankan dengan styling modern.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/admin/laporan-tahunan.css` | **Create** | Stylesheet terdedikasi untuk layout tahunan, toolbar, tombol elegan, hero KPI cards, area chart SVG, table styling, dan mode gelap. |
| `resources/js/components/plan-growth-chart.js` | **Create** | Modul Vanilla JS (ES Module) untuk kalkulasi Bézier curve SVG, render gradient fill, dan interaktivitas tooltip hover. |
| `resources/views/admin/laporan/tahunan.blade.php` | **Modify** | Membersihkan inline style, menghapus tag `<style>` internal, mengadopsi struktur semantik baru, memasang komponen visualisasi Plan Growth, dan menyelaraskan tombol. |
| `resources/css/app.css` | **Modify** | Mengimpor `pages/admin/laporan-tahunan.css` ke dalam Vite pipeline. |
| `resources/js/app.js` | **Modify** | Mengimpor `components/plan-growth-chart.js` untuk auto-mount saat halaman analitik tahunan dibuka. |
| `tests/Feature/AdminAnalitikTahunanVisualTest.php` | **Create** | Pengujian otomatis untuk memastikan halaman dapat diakses, elemen Plan Growth ter-render, tombol navigasi tersedia, dan batasan otorisasi A-20 tetap aman. |

---

## 5. Langkah Implementasi

### Tahap 1: Pembuatan Aset Frontend Terdedikasi (CSS & JS)
- [x] **Langkah 1**: Buat file stylesheet `resources/css/pages/admin/laporan-tahunan.css` yang mendefinisikan layout container, hero KPI cards dengan top border accent, styling tombol navigasi outline dan solid, container grafik Plan Growth, tooltip hover, dan dark mode overrides.
- [x] **Langkah 2**: Impor `resources/css/pages/admin/laporan-tahunan.css` ke dalam `resources/css/app.css`.
- [x] **Langkah 3**: Buat modul JavaScript `resources/js/components/plan-growth-chart.js` yang membaca data JSON atribut dari elemen kontainer grafik, menghitung titik koordinat SVG kurva Bézier halus, merender gradient fill, dan menambahkan interaktivitas tooltip mousemove.
- [x] **Langkah 4**: Impor komponen chart tersebut ke dalam `resources/js/app.js`.

### Tahap 2: Refactoring Markup Blade (`tahunan.blade.php`)
- [x] **Langkah 5**: Hapus tag `<style>` dark mode override internal dari `@push('styles')` dan gantikan dengan class CSS eksternal.
- [x] **Langkah 6**: Restrukturisasi toolbar atas: bungkus filter tahun dan tombol aksi dalam grid/flex terstruktur; ubah tombol `← Hub Laporan Utama` menjadi tombol outline elegan dan tombol `Unduh Rekap CSV` menjadi tombol solid beraksen dengan SVG icon.
- [x] **Langkah 7**: Format ulang 4 kartu ringkasan eksekutif (KPI) menggunakan class hero metric cards bertop border accent (Navy, Emerald, Purple, Amber).
- [x] **Langkah 8**: Sisipkan container grafik Plan Growth SVG interaktif lengkap dengan atribut dataset bulanan di atas tabel tren bulanan.
- [x] **Langkah 9**: Perbarui kolom "Grafik Proporsi" pada tabel tren bulanan dengan segmented proportional bar yang rapi dan elegan.
- [x] **Langkah 10**: Rapikan kartu Top 10 Institusi dan Distribusi Jenjang di bagian bawah. Pastikan guard `@if($canViewNilai)` untuk aturan A-20 tetap utuh.

### Tahap 3: Build Asset & Quality Gate
- [x] **Langkah 11**: Jalankan `npm run build` untuk memverifikasi kompilasi Vite berjalan sukses tanpa error.
- [x] **Langkah 12**: Jalankan `./vendor/bin/pint --test` untuk memastikan kerapian kode PHP.
- [x] **Langkah 13**: Buat dan jalankan test suite `tests/Feature/AdminAnalitikTahunanVisualTest.php` serta uji regresi `tests/Feature/AdvancedAdminAndSuperAdminPhaseFiveTest.php` dengan `./vendor/bin/phpunit`.

---

## 6. Strategi Testing

1. **Uji Otomatis (PHPUnit)**:
   - Akses halaman oleh peran Admin (`GET /admin/laporan/tahunan?tahun=2026`) -> Status 200, elemen container Plan Growth, tabel, dan tombol elegan tampil.
   - Verifikasi batasan keamanan A-20: Admin **tidak boleh** melihat sebaran predikat nilai dan melihat pesan perlindungan mutu nilai.
   - Akses halaman oleh SuperAdmin -> Status 200, dapat melihat grafik dan sebaran predikat nilai.
   - Uji ekspor CSV (`/admin/laporan/tahunan/export?tahun=2026`) tetap berfungsi valid dengan MIME type `text/csv`.
2. **Uji Visual & Responsivitas**:
   - Tampilan desktop (resolusi lebar) dan tampilan mobile/tablet.
   - Uji tema terang (*Light Mode*) dan tema gelap (*Dark Mode*).
   - Uji interaktivitas hover pada area chart Plan Growth.

---

## 7. Risiko & Mitigasi

- **Risiko 1: Pelanggaran Aturan AGENTS.md terkait Blade `<style>`**:
  - *Mitigasi*: Seluruh kode CSS dipindahkan 100% ke `resources/css/pages/admin/laporan-tahunan.css` dan dikompilasi via Vite. Blade hanya berisi markup HTML/semantik bersih.
- **Risiko 2: Kerusakan Batasan Keamanan Nilai (PRD 4.3 & A-20)**:
  - *Mitigasi*: Kondisional `@if($canViewNilai)` tidak diubah perilakunya, sehingga hak akses SuperAdmin dan restriksi staf admin tetap terjaga 100%.
- **Risiko 3: Grafik SVG Rusak saat Data Nol / Kosong**:
  - *Mitigasi*: Script SVG chart dilengkapi fallback handling: jika seluruh bulan memiliki nilai 0, kurva tetap ter-render flat di sumbu dasar dengan label "Belum ada aktivitas penyerapan".

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Toolbar memiliki dropdown tahun kalender yang rapi serta tombol `Hub Laporan Utama` dan `Unduh Rekap` yang elegan tanpa underline.
- [x] 4 Hero KPI Cards memiliki top border accent yang harmonis dan tipografi yang jelas.
- [x] Visualisasi kurva bertingkat bergaya *Plan Growth* ter-render secara responsif menggunakan SVG interaktif bergradasi.
- [x] Kolom grafik proporsi pada tabel bulanan tertata rapi.
- [x] Tidak ada tag `<style>` atau inline CSS statis berantakan di dalam file Blade.
- [x] Seluruh CSS dan JavaScript baru berada di `resources/` dan terintegrasi melalui Vite.
- [x] Seluruh test suite (`phpunit`), linter (`pint`), dan build (`npm run build`) berstatus hijau (100% PASS).

