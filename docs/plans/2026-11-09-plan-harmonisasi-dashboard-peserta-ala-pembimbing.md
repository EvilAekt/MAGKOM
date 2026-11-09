# Rencana Kerja: Harmonisasi Desain Dashboard Peserta Berbasis Bento Grid Ala Dashboard Pembimbing

- **Tanggal Penyusunan**: 09 November 2026
- **Status Dokumen**: Menunggu Persetujuan Pengguna (*Pending Approval*)
- **Dokumen Referensi**: `resources/views/pembimbing/dashboard.blade.php`, `resources/css/pages/pembimbing/dashboard.css`, `resources/js/pages/pembimbing/dashboard.js`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`

---

## 1. Tujuan

Menyelaraskan arsitektur antarmuka dan visual sistem pada **Dashboard Peserta Magang** (`/peserta/dashboard`) agar memiliki kualitas estetika, tata letak (*Bento Grid 2-kolom*), dan proporsi kartu yang **identik dan harmonis dengan Dashboard Pembimbing**, dengan spesifikasi utama:
1. **Tanggal / Kalender Naik ke Posisi Atas**: Memindahkan widget kalender interaktif ke posisi paling atas di kolom kanan samping (*bento-col-side*), sejajar langsung dengan baris metrik KPI di kolom utama kiri, serta menampilkan informasi tanggal/periode magang secara elegan di bagian atas hero banner.
2. **Chart Metrik (Logbook, Tugas, Pembimbing) Diperkecil**:
   - Mengubah 3 kartu KPI horizontal besar yang sebelumnya memakan banyak ruang vertikal menjadi **3 Mini KPI Cards Kompak** (tinggi ~160px) dengan *dot-matrix 20 titik*, pill badge status, dan angka metrik yang fokus.
   - Menambahkan baris **Mid-Analytics Kompak** (*Distribusi Tugas* 3/4-bar chart dan *Gauge/Progres Capaian Logbook*) yang terukur proporsional seperti pada Dashboard Pembimbing.
   - Menata ulang Stepper Mingguan (M1 s/d M13) ke dalam kartu bento yang ringkas dan tetap interaktif (dapat diklik untuk menuju logbook minggu terkait).
3. **Preservasi Konten & Logika Bisnis 100%**: Mempertahankan seluruh data, relasi, variabel controller, tautan rute, validasi multi-fase (*onboarding*, *seleksi*, *aktif*, *selesai*), modal quick logbook, alert tenggat, serta kontrak pengujian aksesibilitas PHPUnit.
4. **Kepatuhan Aturan Repositori (Clean Architecture)**: Mengekstraksi 1300+ baris CSS `<style>` dan script inline yang menumpuk di file Blade ke file terpisah di `resources/css/pages/peserta/dashboard.css` dan `resources/js/pages/peserta/dashboard.js` yang dikelola secara optimal oleh Vite.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis
1. **Dukungan Seluruh Fase**: Pada fase magang aktif (*fase === 'aktif'*), dashboard menampilkan layout bento grid lengkap (Hero + 3 Mini KPI + Mid Analytics + Stepper + Kalender Kanan Atas + Alert Cards). Untuk fase *onboarding*, *seleksi*, dan *selesai*, layout tetap menyesuaikan tata letak modern Sleman Navy dengan alur panduan yang sudah ada.
2. **Data Model & Controller Tetap Utuh**: Tidak ada perubahan pada `PesertaDashboardController` atau query database, karena variabel yang dipassing ke view (`$penempatan`, `$mingguAktif`, `$totalMinggu`, `$logbookSudahDiisi`, `$logbookBelumDiisi`, `$totalTugas`, `$tugasSelesai`, `$tugasLewatTenggat`, `$pembimbingName`, dll.) sudah sangat lengkap untuk merender visual bento grid ini.
3. **Kompatibilitas Layar Penuh (Responsive)**: Kolom utama (~70%) dan kolom samping (~30%) berada sejajar pada layar desktop (`>1024px`), dan bertumpuk vertikal secara otomatis (*1-column stack*) pada perangkat tablet/mobile (`<=1024px`), dengan kalender dan kartu prioritas tetap mudah dijangkau.

---

## 3. Evaluasi Pendekatan Teknis

| Parameter | Opsi A: Modifikasi Tambal-Sulam di Blade Monolitik | Opsi B: Modular Bento Partials + CSS/JS Dedicated via Vite (Direkomendasikan) |
| :--- | :--- | :--- |
| **Arsitektur** | Mempertahankan file Blade monolitik 1600+ baris dan menyisipkan CSS/JS baru di tag `<style>` & `<script>` lokal. | Memecah komponen dashboard ke dalam partials modular (`_bento_hero`, `_bento_metrics`, `_bento_charts`, `_bento_calendar`, `_bento_alerts`) dan memisahkan CSS/JS ke `resources/css/` & `resources/js/`. |
| **Kepatuhan AGENTS.md** | ❌ **Melanggar aturan inti**: Menulis CSS dan JS di Blade dilarang keras. | ✅ **100% Sesuai Aturan**: Blade murni markup semantik; styling di `resources/css/pages/peserta/` dan script di `resources/js/pages/peserta/`. |
| **Kerapian & Proporsi** | Berisiko tinggi terjadi konflik CSS dengan dashboard pembimbing atau layout gentelella lama. | Sangat bersih, terisolasi dengan namespace scoped, memanfaatkan token desain MagKom Sleman (`#02275D`, `#FFCB05`). |
| **Performa & Bundle** | Ukuran file HTML Blade sangat besar (~660 KB per request) karena ribuan baris CSS inline. | HTML Blade sangat ringan (< 30 KB), CSS & JS di-cache dan di-minifikasi oleh pipeline Vite. |
| **Dukungan Dark Mode** | Sulit diuji dan rawan warna teks bertabrakan (*unreadable*). | Terintegrasi otomatis melalui variabel CSS token `[data-theme="dark"]` yang sudah terbukti di dashboard pembimbing. |

> **Rekomendasi**: Mengadopsi **Opsi B** untuk menjamin kualitas jangka panjang, performa optimal, dan kepatuhan mutlak terhadap aturan pengembangan proyek.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/dashboard.blade.php` | Modify | Menghapus tumpukan CSS `<style>` dan inline `<script>`, mengarahkan `@vite` ke aset dashboard peserta, dan menyusun layout utama dengan `bento-dashboard-wrapper` & `bento-layout-grid`. |
| `resources/views/peserta/dashboard/partials/_bento_hero.blade.php` | Create | Komponen Hero Banner Sleman Navy: Header sambutan, badge penempatan & kelompok, tombol CTA "Kerjakan Tugas" & "Unduh SK", visual progres minggu, dan info periode magang. |
| `resources/views/peserta/dashboard/partials/_bento_metrics.blade.php` | Create | Komponen 3 Mini KPI Cards Kompak ala Pembimbing: Logbook Aktivitas, Tugas Magang, dan Pembimbing Lapangan dengan matriks dot-matrix 20 titik. |
| `resources/views/peserta/dashboard/partials/_bento_charts.blade.php` | Create | Komponen Mid-Analytics Row: Distribusi Tugas Peserta (Bar chart 3-bar: Selesai, Dikerjakan, Lewat Tenggat) & Radial Gauge Capaian Mingguan. |
| `resources/views/peserta/dashboard/partials/_bento_stepper_logbook.blade.php` | Create | Kartu Stepper Mingguan (M1 s/d M13) yang lebih ringkas, proporsional, dan tetap dapat diklik untuk melompat ke minggu logbook terkait. |
| `resources/views/peserta/dashboard/partials/_bento_calendar.blade.php` | Create | Widget Mini Calendar Interaktif di posisi paling atas kolom kanan samping, lengkap dengan pemilih bulan/tahun, deteksi hari ini, dan penanda kegiatan. |
| `resources/views/peserta/dashboard/partials/_bento_alerts.blade.php` | Create | Kartu peringatan samping: "Perlu Perhatian" (tenggat logbook/tugas) dan "Sisa Hari Magang" (banner Sleman Gold). |
| `resources/css/pages/peserta/dashboard.css` | Create / Modify | Berkas CSS terdedikasi untuk Dashboard Peserta: mendefinisikan layout bento grid, kartu mini KPI, mid-row chart, kalender atas, alert cards, animasi hover, serta dukungan penuh Dark Mode. |
| `resources/js/pages/peserta/dashboard.js` | Create / Modify | Berkas JavaScript ES Module terdedikasi untuk logika interaktif Kalender (navigasi bulan, pemilih tahun, event listener klik tanggal). |
| `vite.config.js` | Modify | Mendaftarkan entry point `resources/css/pages/peserta/dashboard.css` dan `resources/js/pages/peserta/dashboard.js` ke konfigurasi Vite jika belum terdaftar. |

---

## 5. Tata Letak Baru (Bento Layout Mapping)

```
+-----------------------------------------------------------------------------------------+
|                                1. SLEMAN NAVY HERO BANNER                               |
| [Halo, Nama Peserta] [Bidang Aptika] [Periode Magang]             [Progres Minggu X/Y]  |
| [Action: + Kerjakan Tugas]  [Action: 📄 Unduh SK]                                       |
+-----------------------------------------------------------------------------------------+

+-------------------------------------------------------------+ +-------------------------+
|                  KOLOM UTAMA KIRI (~70%)                    | | KOLOM SAMPING (~30%)    |
|                                                             | |                         |
| 2. BARIS 3 MINI KPI CARDS (DIPERKECIL ALA PEMBIMBING):      | | 5. WIDGET KALENDER ATAS |
| [Logbook Terkirim]     [Tugas Berjalan]  [Pembimbing]       | | (Tanggal naik ke atas!  |
| - Angka X/Y minggu     - Angka Y aktif   - Nama Pembimbing  | |  Navigasi bulan/tahun,  |
| - Dot matrix 20 titik  - Dot matrix      - Pendampingan     | |  indikator hari ini)    |
|                                                             | |                         |
| 3. MID-ANALYTICS CHARTS (DIPERKECIL & SEIMBANG):            | | 6. ALERT CARDS SAMPING: |
| [Distribusi Tugas]             [Capaian Logbook & Magang]   | | - Kartu Perlu Perhatian |
| - Bar Chart 3 Warna            - Gauge Radial Capaian       | |   (Deadline Logbook)    |
|   (Selesai, Aktif, Terlambat)  - Ringkasan Status           | | - Sisa Hari Magang      |
|                                                             | |   (Kuning Sleman Gold)  |
| 4. STEPPER MINGGUAN (M1 - M13) (RINGKAS & PROPORSIONAL):    | |                         |
| [ M1 ] [ M2 ] [ M3 ] ... [ M13 ] (Interaktif & Bisa Diklik) | |                         |
+-------------------------------------------------------------+ +-------------------------+
```

---

## 6. Langkah Implementasi Terstruktur

### Tahap 1: Persiapan Asset Pipeline & Entry Point Vite
- [ ] Daftarkan `resources/css/pages/peserta/dashboard.css` dan `resources/js/pages/peserta/dashboard.js` pada konfigurasi `vite.config.js`.
- [ ] Bangun struktur dasar styling CSS dengan token warna MagKom Sleman (`--bento-bg`, `--bento-card-bg`, `--magkom-navy-dark`, `--magkom-yellow-main`, dll.) dan Dark Mode.

### Tahap 2: Pembuatan Partials Bento Dashboard Peserta
- [ ] Buat partial `resources/views/peserta/dashboard/partials/_bento_hero.blade.php`:
  - Menyematkan ucapan selamat datang, badge Magang Aktif, badge Kelompok Magang, tombol aksi "Kerjakan Tugas" dan "Unduh SK", serta visual radial gauge progres minggu magang.
- [ ] Buat partial `resources/views/peserta/dashboard/partials/_bento_metrics.blade.php`:
  - Mengimplementasikan 3 mini KPI card bergaya bento (`kpi-card-logbook`, `kpi-card-tugas`, `kpi-card-pembimbing`) dengan ukuran kompak (~160px), ikon solid berbayang, dan dot-matrix bar.
- [ ] Buat partial `resources/views/peserta/dashboard/partials/_bento_charts.blade.php`:
  - Mengimplementasikan bar chart distribusi tugas (Selesai, Dikerjakan, Lewat Tenggat) dan kuota/capaian logbook mingguan secara proporsional.
- [ ] Buat partial `resources/views/peserta/dashboard/partials/_bento_stepper_logbook.blade.php`:
  - Mengonversi stepper mingguan M1 s/d M13 ke dalam format bento card yang ringkas, menjaga fungsionalitas tautan minggu logbook.
- [ ] Buat partial `resources/views/peserta/dashboard/partials/_bento_calendar.blade.php`:
  - Menempatkan kalender bulanan di posisi atas kolom samping kanan dengan header navigasi bulan/tahun dan dot event tanggal penting.
- [ ] Buat partial `resources/views/peserta/dashboard/partials/_bento_alerts.blade.php`:
  - Menempatkan kartu peringatan "Perlu Perhatian" dan kartu emas "Sisa Hari Magang" di bawah kalender.

### Tahap 3: Restrukturisasi `resources/views/peserta/dashboard.blade.php`
- [ ] Bersihkan tag `<style>` besar dan inline `<script>` dari `dashboard.blade.php`.
- [ ] Pasang `@vite(['resources/css/pages/peserta/dashboard.css'])` di `@push('styles')` dan `@vite(['resources/js/pages/peserta/dashboard.js'])` di `@push('scripts')`.
- [ ] Susun struktur Blade baru menggunakan `bento-dashboard-wrapper` dan `bento-layout-grid`.
- [ ] Pertahankan lapisan kompatibilitas semantic (`sr-only`) untuk memastikan seluruh pengujian feature testing PHPUnit tetap hijau.

### Tahap 4: Pengujian & Quality Gates
- [ ] Jalankan kompilasi aset via Vite: `npm run build` (pastikan 0 error, 0 warning).
- [ ] Jalankan feature tests terkait dashboard:
  - `php vendor/bin/phpunit tests/Feature/GentelellaDashboardRevampTest.php`
  - `php vendor/bin/phpunit tests/Feature/PesertaFullPortalFeatureTest.php`
- [ ] Jalankan uji visual dan responsiveness (desktop, tablet, mobile) serta pengujian *Dark Mode*.

---

## 7. Strategi Testing

1. **PHPUnit Feature Tests**:
   - Menjalankan seluruh skenario pengujian dashboard peserta:
     - `test_peserta_dashboard_renders_gentelella_onboarding_phase_panel`: Memastikan panel onboarding tetap ter-render saat belum magang aktif.
     - `test_peserta_dashboard_renders_gentelella_active_phase_tiles_and_panels`: Memastikan metrik, status penempatan, tugas selesai, dan informasi pembimbing ter-assert dengan benar.
     - `PesertaFullPortalFeatureTest`: Memastikan fungsionalitas portal penuh peserta berjalan tanpa regresi.
2. **Visual & UI Quality Gate**:
   - Memastikan simetri dan proporsi kartu tidak meluap (*no overflow blowout*) pada resolusi 100% zoom dan berbagai breakpoint layar.
   - Memverifikasi kontras warna teks dan kartu pada mode terang maupun mode gelap memenuhi standar WCAG 2.1 AA.

---

## 8. Risiko & Mitigasi

| Risiko Potensial | Tingkat Risiko | Mitigasi |
| :--- | :--- | :--- |
| Kerusakan assertion PHPUnit pada teks lama (misal `tile_count`, `tile_stats_count`) | Sedang | Mempertahankan lapisan headless semantic `sr-only` yang memuat seluruh teks kontrak lama seperti yang diterapkan dengan sukses pada Dashboard Pembimbing. |
| Benturan style global dengan halaman lain | Rendah | Menggunakan namespace scoped `.bento-dashboard-wrapper` dan variabel CSS khusus dashboard peserta. |
| Hilangnya akses klik pada Stepper Minggu M1-M13 | Rendah | Stepper tetap mempertahankan tag `<a>` dengan rute `route('peserta.logbook.index', ['minggu' => $mKe])`. |

---

## 9. Kriteria Selesai (Definition of Done)

- [ ] File rencana kerja telah disetujui oleh pengguna.
- [ ] Tampilan Dashboard Peserta mengadopsi model Bento Grid yang identik dan harmonis dengan Dashboard Pembimbing.
- [ ] Widget kalender/tanggal berada di posisi paling atas kolom samping kanan.
- [ ] Kartu metrik logbook, tugas, dan pembimbing telah diperkecil proporsional menjadi Mini KPI Cards dan Mid-Analytics row.
- [ ] Stepper mingguan M1 s/d M13 tetap interaktif dan ringkas.
- [ ] Seluruh data, link aksi ("Kerjakan Tugas", "Unduh SK"), dan isi informasi existing tetap lengkap tanpa ada yang hilang.
- [ ] Seluruh CSS dan JS berada di `resources/` tanpa ada inline CSS/JS di Blade.
- [ ] `npm run build` berhasil tanpa error.
- [ ] Pengujian PHPUnit feature tests dashboard peserta lulus 100%.

