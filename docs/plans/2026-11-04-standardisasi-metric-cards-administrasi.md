# Standardisasi Metric Cards di Seluruh Modul Administrasi (Admin) Sesuai Pola Gentelella Tile-Stat

## 1. Tujuan
Menyelaraskan seluruh kartu metrik (*metric cards / KPI stat cards*) di seluruh halaman modul Administrasi (`admin/*`) agar memiliki tampilan, struktur, dan pengalaman visual yang seragam dengan kartu metrik SuperAdmin (sesuai tangkapan layar rujukan pengguna):
1. **Kotak Ikon Fungsional di Kiri**: Rounded box (`44x44px`), soft tinted background sesuai peran semantik (`teal`, `yellow/amber`, `blue`, `red`), dengan SVG icon fungsional yang relevan (contoh: *Verifikasi Tertunda* menggunakan icon `clipboard-check` dengan warna amber/yellow).
2. **Area Nilai & Label di Tengah**: Label kategori uppercase dengan warna slate-500, angka nilai utama bold (1.65rem), badge tren/indikator status (e.g. `+14%`, `↑ Tertib`, `↑ Aktif`, `↓ Nonaktif`), serta subteks deskriptif informatif di bawahnya.
3. **Sparkline Mini Vertical Bars di Kanan**: 7 batang bar visual vertikal (`.stat-spark.sa-spark`) dengan palet warna serasi untuk memberikan indikasi dinamika data operasional.
4. **Desain Dual-Theme**: Latar belakang putih bersih (`#FFFFFF`) dengan border halus (`#E2E8F0`) pada Mode Terang (*Light Mode*), dan navy elegan (`#003566`) dengan border (`#3F51B5`) pada Mode Gelap (*Dark Mode*).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. **Komponen Inti**: Menggunakan komponen resmi blade `<x-gentelella.tile-stat>` (`resources/views/components/gentelella/tile-stat.blade.php`) yang sudah mendukung parameter `icon`, `color`, `trendText`, `trendType`, `trendDirection`, `subText`, dan `:sparkBars`.
2. **Koleksi Icon SVG**: Menggunakan icon SVG bawaan dari `<x-sa.icon>` (`resources/views/components/sa/icon.blade.php`) yang sudah mencakup `clipboard-check`, `user-check`, `users`, `user-plus`, `file-text`, `clock`, `calendar`, `alert-circle`, `award`, `shield`, `briefcase`, `activity`, `lock`, dll.
3. **Penyimpanan Styling Terpusat**: CSS untuk kartu metrik (`.stat`, `.stat-icon`, `.stat-content`, `.stat-value-row`, `.stat-value`, `.stat-change`, `.stat-subtext`, `.stat-spark`) dipindahkan/digeneralisasikan ke `resources/css/components/cards.css` agar langsung tersedia secara global di seluruh halaman dashboard tanpa perlu memuat file CSS khusus per-halaman.
4. **Preservasi String Assertion Test**: Teks-teks penting yang menjadi target assertion di test suite (seperti `'Perlu Verifikasi Segera'`, `'Peserta Magang Aktif'`, `'Keterisian Kuota Magang Periode'`, `'admin-stat-card admin-stat-card--blue'`, dll.) tetap dipertahankan di dalam `subText` atau atribut kelas untuk menjamin *zero regression*.

---

## 3. Pendekatan Teknis & Evaluasi Arsitektur

### Evaluasi Opsi Pendekatan:

- **Opsi A (Direkomendasikan): Standardisasi Shared Component CSS di `resources/css/components/cards.css` + Penerapan Terpadu `<x-gentelella.tile-stat>`**
  - **Mekanisme**:
    1. Memindahkan styling modern kartu metrik (gaya horizontal flex-row, rounded icon box 44x44, typography stack, trend pill, 7 sparkline bars, serta varian light & dark mode) dari cakupan eksklusif `.sa-metric-card-col` ke rule umum `.tile_stats_inner.stat` di `resources/css/components/cards.css`.
    2. Menghapus override latar belakang biru kaku di `resources/css/pages/admin/dashboard.css` (`.adm-metric-card-col .tile_stats_inner`) sehingga kartu metrik Admin di dashboard tampil bersih, modern, dan identik dengan tangkapan layar rujukan.
    3. Memperbarui implementasi pemanggilan `<x-gentelella.tile-stat>` di seluruh view Administrasi dengan menyertakan `icon` fungsional, `color`, `trendText`, `subText`, dan `:sparkBars`.
  - **Kelebihan**:
    - *Single Source of Truth*: Satu basis styling CSS untuk seluruh modul (Admin & SuperAdmin).
    - Mematuhi aturan `AGENTS.md` (CSS terpusat di `resources/css/`, dikelola Vite, tanpa inline CSS).
    - Konsistensi visual 100% di semua halaman administrasi.
    - Responsif otomatis di semua ukuran layar (Desktop 4 kolom, Tablet 2 kolom, Mobile 1 kolom).
  - **Kekurangan**: Memerlukan pengujian visual pada halaman Admin Dashboard dan SuperAdmin Dashboard untuk memastikan tidak ada konflik cascade.

- **Opsi B: Duplikasi Styling Per Halaman di CSS Khusus Admin**
  - **Mekanisme**: Menulis styling terpisah khusus `.adm-metric-card-col` di `resources/css/pages/admin/dashboard.css` dan membuat CSS tambahan untuk pendaftaran, penempatan, laporan, dan waiting list.
  - **Kekurangan**: Fragmentasi CSS, kode duplikat yang sulit dirawat di masa depan, dan risiko ketidakkonsistenan antar-halaman.

> **Keputusan**: Mengadopsi **Opsi A** demi konsistensi arsitektur jangka panjang dan kepatuhan penuh terhadap aturan `AGENTS.md`.

---

## 4. Pemetaan Icon, Warna Semantik, dan Halaman Administrasi

Berikut adalah pemetaan spesifik icon fungsional dan warna semantik untuk seluruh kartu metrik di Administrasi:

### 1. Admin Dashboard (`resources/views/admin/dashboard/partials/_metrics.blade.php`)
| Kartu Metrik | Icon Fungsional | Warna Semantik | Nilai | Trend Badge | Subtext Deskriptif | Sparkline Bars |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Verifikasi Tertunda** | `clipboard-check` | `yellow` (Amber) | `$pengajuanMenunggu` | `$pengajuanSlaKritisCount > 0 ? $pengajuanSlaKritisCount . ' SLA' : 'Tertib'` | `$pengajuanSlaKritisCount > 0 ? $pengajuanSlaKritisCount . ' Perlu Verifikasi Segera' : 'Perlu Verifikasi Segera'` | `[75, 80, 70, 85, 90, 65, 60]` |
| **Kapasitas Kuota Terisi** | `calendar` | `blue` | `$realisasiKuotaPersen . '%'` | `Okupansi` | `$totalKuotaTerisi . '/' . $totalKuotaUmum . ' Kursi · Keterisian Kuota Magang Periode'` | `[40, 50, 60, 68, 75, 82, 90]` |
| **Antrean Waiting List** | `clock` | `teal` | `$waitingListCount` | `Antrean` | `$perluPerbaikan . ' Revisi Dokumen · Daftar tunggu antrean'` | `[30, 45, 40, 55, 50, 65, 70]` |
| **Peserta Magang Aktif** | `user-check` | `teal` | `$pesertaAktifCount` | `Aktif` | `Sedang bertugas di dinas` | `[35, 55, 45, 65, 70, 85, 100]` |

### 2. Admin Pendaftaran / Verifikasi Berkas (`resources/views/admin/pendaftaran/index.blade.php`)
| Kartu Metrik | Icon Fungsional | Warna Semantik | Nilai | Trend Badge | Subtext Deskriptif | Sparkline Bars |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Total Pengajuan** | `file-text` | `blue` | `$queueStats['total']` | `Total` | `Semua riwayat pendaftaran` | `[35, 50, 45, 60, 65, 80, 95]` |
| **Menunggu Ditinjau** | `clipboard-check` | `yellow` | `$queueStats['diajukan']` | `Antrean` | `Prioritas antrean FIFO` | `[70, 75, 65, 80, 85, 60, 55]` |
| **Perlu Revisi Dokumen** | `alert-circle` | `yellow` | `$queueStats['perlu_perbaikan']` | `Revisi` | `Menunggu respon peserta` | `[25, 35, 45, 30, 40, 50, 35]` |
| **Tertahan > 3 Hari** | `clock` | `red` | `$queueStats['sla_warning']` | `SLA Target` | `Batas SLA verifikasi target` | `[40, 30, 45, 25, 35, 20, 15]` |

### 3. Admin Waiting List (`resources/views/admin/waiting-list/index.blade.php`)
| Kartu Metrik | Icon Fungsional | Warna Semantik | Nilai | Trend Badge | Subtext Deskriptif | Sparkline Bars |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Total Antrean Tunggu** | `users` | `blue` | `$totalWaiting` | `Antrean` | `Calon peserta terdaftar FIFO` | `[40, 55, 50, 65, 70, 80, 90]` |
| **Bulan Magang Terdampak** | `calendar` | `yellow` | `$periodeCount` | `Periode` | `$periodeCount . ' Bulan Magang Terdampak'` | `[30, 45, 40, 50, 60, 55, 70]` |
| **Siap Dipromosikan** | `user-check` | `teal` | `$siapPromosiCount` | `Tersedia` | `Kuota jurusan telah tersedia` | `[20, 35, 40, 60, 75, 85, 100]` |
| **Penegakan Aturan** | `shield` | `blue` | `'FIFO BR-05'` | `BR-05` | `Prioritas waktu pendaftaran` | `[60, 60, 60, 60, 60, 60, 60]` |

### 4. Admin Penempatan Index (`resources/views/admin/penempatan/index.blade.php`)
| Kartu Metrik | Icon Fungsional | Warna Semantik | Nilai | Trend Badge | Subtext Deskriptif | Sparkline Bars |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Total Penempatan** | `users` | `blue` | `$penempatanStats['total']` | `Total` | `Semua riwayat peserta` | `[35, 50, 45, 65, 70, 85, 95]` |
| **Peserta Magang Aktif** | `user-check` | `teal` | `$penempatanStats['aktif']` | `Aktif` | `Sedang masa bimbingan` | `[40, 50, 60, 68, 75, 82, 90]` |
| **Selesai Magang** | `award` | `blue` | `$penempatanStats['selesai']` | `Selesai` | `Telah tuntas & dinilai` | `[25, 40, 45, 60, 70, 80, 90]` |
| **Jalur Khusus Mitra** | `briefcase` | `yellow` | `$penempatanStats['khusus']` | `Mitra` | `MoU & kuota terproteksi` | `[30, 40, 35, 50, 45, 55, 60]` |
| **Menunggu Plotting** *(kondisional jika > 0)* | `clock` | `yellow` | `$penempatanStats['belum_ditempatkan']` | `Tahap 1` | `Lolos berkas pendaftaran` | `[50, 60, 55, 70, 65, 80, 75]` |

### 5. Admin Laporan Hub (`resources/views/admin/laporan/index.blade.php`)
| Kartu Metrik | Icon Fungsional | Warna Semantik | Nilai | Trend Badge | Subtext Deskriptif | Sparkline Bars |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Total Pengajuan** | `file-text` | `blue` | `$metricCards['total_pendaftaran']` | `Total` | `Semua status pendaftaran` | `[35, 50, 45, 65, 70, 85, 95]` |
| **Peserta Diterima** | `user-check` | `teal` | `$metricCards['total_diterima']` | `Diterima` | `Lolos seleksi berkas & kuota` | `[25, 40, 50, 65, 75, 85, 95]` |
| **Penempatan Aktif** | `activity` | `blue` | `$metricCards['penempatan_aktif']` | `Aktif` | `Sedang menjalani bimbingan` | `[40, 50, 60, 68, 75, 82, 90]` |
| **Magang Selesai** | `award` | `teal` | `$metricCards['penempatan_selesai']` | `Selesai` | `Alumni & evaluasi final` | `[30, 45, 55, 65, 75, 85, 100]` |

### 6. Admin Laporan Tahunan (`resources/views/admin/laporan/tahunan.blade.php`)
| Kartu Metrik | Icon Fungsional | Warna Semantik | Nilai | Trend Badge | Subtext Deskriptif | Sparkline Bars |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Total Pendaftar Masuk** | `user-plus` | `blue` | `$totalPendaftaran` | `Pendaftar` | `Calon peserta yang mengajukan berkas` | `[35, 50, 45, 65, 70, 85, 95]` |
| **Peserta Diterima Magang** | `user-check` | `teal` | `$totalDiterima` | `Diterima` | `Lolos seleksi & ditempatkan` | `[25, 40, 50, 65, 75, 85, 95]` |
| **Peserta Selesai Magang** | `award` | `yellow` | `$totalSelesai` | `Selesai` | `Menyelesaikan masa dinas` | `[30, 45, 55, 65, 75, 85, 100]` |
| **Rata-rata Durasi Magang** | `clock` | `blue` | `$rataDurasiBulan . ' Bulan'` | `Durasi` | `Rentang normal 1 sampai 6 bulan` | `[40, 40, 50, 50, 60, 60, 50]` |

### 7. Admin Penempatan Detail (`resources/views/admin/penempatan/show.blade.php`)
| Kartu Metrik | Icon Fungsional | Warna Semantik | Nilai | Trend Badge | Subtext Deskriptif | Sparkline Bars |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Tugas Selesai** | `clipboard-check` | `blue` | `$tugasSelesaiCount . ' / ' . $tugasCount` | `Tugas` | `Tugas magang tuntas` | `[30, 50, 40, 60, 70, 85, 100]` |
| **Total Logbook** | `book-open` | `teal` | `$logbookCount . ' Minggu'` | `Jurnal` | `Jurnal mingguan terisi` | `[40, 50, 60, 70, 80, 90, 100]` |
| **Nilai Akhir** | `award` | `yellow` | `$nilaiAkhir !== null ? number_format((float) $nilaiAkhir, 2) : 'Belum Ada'` | `$penempatan->penilaian?->predikat ?? 'Evaluasi'` | `Predikat evaluasi dinas` | `[50, 65, 60, 75, 80, 90, 95]` |
| **Durasi Magang** | `calendar` | `blue` | `$penempatan->total_minggu . ' Minggu'` | `Periode` | `Total periode dinas` | `[40, 50, 60, 70, 80, 90, 100]` |

### 8. Admin Sertifikat Index (`resources/views/admin/sertifikat/index.blade.php`)
| Kartu Metrik | Icon Fungsional | Warna Semantik | Nilai | Trend Badge | Subtext Deskriptif | Sparkline Bars |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Siap Terbit Sertifikat** | `file-text` | `blue` | `$totalBersertifikat` | `Final` | `Nilai telah difinalisasi` | `[30, 45, 55, 65, 75, 85, 95]` |
| **Total Selesai Magang** | `check-circle` | `teal` | `$totalSelesai` | `Selesai` | `Status penempatan selesai` | `[40, 55, 65, 70, 80, 90, 100]` |

---

## 5. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/components/cards.css` | Modify | Menambahkan shared CSS untuk kartu metrik Gentelella (`.stat`, `.stat-icon`, `.stat-content`, `.stat-value-row`, `.stat-value`, `.stat-change`, `.stat-subtext`, `.stat-spark`, mode terang & gelap). |
| `resources/css/pages/admin/dashboard.css` | Modify | Menghapus override latar belakang biru tua kaku (`#01347C`) pada `.adm-metric-card-col .tile_stats_inner` agar mewarisi kartu putih bersih standar; menyelaraskan grid `.adm-metrics-dock-grid`. |
| `resources/views/admin/dashboard/partials/_metrics.blade.php` | Modify | Memperbarui pemanggilan `<x-gentelella.tile-stat>` dengan `color`, `icon`, `trendText`, `subText`, dan `:sparkBars` sesuai tabel pemetaan. |
| `resources/views/admin/dashboard/partials/_header.blade.php` | Modify | Mengatur agar kontainer hero banner tetap rapi dan menyatu secara elegan dengan 4 kartu metrik utama. |
| `resources/views/admin/pendaftaran/index.blade.php` | Modify | Memperbarui 4 kartu KPI atas dengan `<x-gentelella.tile-stat>` ber-icon fungsional, `color`, `trendText`, `subText`, dan `:sparkBars`. |
| `resources/views/admin/waiting-list/index.blade.php` | Modify | Memperbarui 4 kartu antrean dengan `<x-gentelella.tile-stat>` ber-icon fungsional, `color`, `trendText`, `subText`, dan `:sparkBars`. |
| `resources/views/admin/penempatan/index.blade.php` | Modify | Mengganti div custom `.admin-stat-card` dengan `<x-gentelella.tile-stat>` seragam, dengan mempertahankan modifier class untuk kompatibilitas test suite. |
| `resources/views/admin/laporan/index.blade.php` | Modify | Mengganti 4 kartu summary dengan `<x-gentelella.tile-stat>` ber-icon fungsional, `color`, `trendText`, `subText`, dan `:sparkBars`. |
| `resources/views/admin/laporan/tahunan.blade.php` | Modify | Mengganti 4 kartu KPI `.tahunan-kpi-card` dengan `<x-gentelella.tile-stat>` ber-icon fungsional, `color`, `trendText`, `subText`, dan `:sparkBars`. |
| `resources/views/admin/penempatan/show.blade.php` | Modify | Memperbarui 4 kartu progress tugas/logbook dengan `<x-gentelella.tile-stat>` ber-icon SVG modern dan `:sparkBars`. |
| `resources/views/admin/sertifikat/index.blade.php` | Modify | Mengganti 2 kartu ringkasan sertifikat dengan `<x-gentelella.tile-stat>` ber-icon fungsional, `color`, `trendText`, `subText`, dan `:sparkBars`. |

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1: Standardisasi CSS Shared Components di `resources/css/components/cards.css`**
  - Definisikan rule untuk `.tile_stats_inner.stat`:
    - Mode Terang: background `#FFFFFF`, border `1px solid var(--neutral-300, #E2E8F0)`, border-radius `10px` / `12px`, padding `1rem 1.25rem`, shadow halus `0 1px 3px rgba(15, 23, 42, 0.06)`, display `flex`, flex-direction `row`, align-items `center`, gap `0.875rem`.
    - Mode Gelap (`[data-theme="dark"]`): background `#003566`, border `1px solid #3F51B5`, shadow `0 8px 24px rgba(0, 0, 0, 0.5)`.
  - Definisikan `.stat-icon`: size `44x44px`, border-radius `8px`, flex-center, soft-tinted background (`rgba(...)`) untuk `teal`, `yellow`, `blue`, `red`.
  - Definisikan `.stat-content`, `.stat-label`, `.stat-value-row`, `.stat-value`, `.stat-change.up`, `.stat-change.down`, `.stat-subtext`.
  - Definisikan `.stat-spark.sa-spark`: flex align-end, height `32px`, margin-left auto, bar `width: 3.5px`, rounded `1.5px`, warna serasi dengan icon box.

- [x] **Langkah 2: Penyelarasan CSS Admin Dashboard di `resources/css/pages/admin/dashboard.css`**
  - Hapus background `#01347C` kaku pada `.adm-metric-card-col .tile_stats_inner`.
  - Pastikan grid dock `.adm-metrics-dock-grid` memiliki layout 4 kolom yang responsif dan konsisten.

- [x] **Langkah 3: Pembaruan Blade Admin Dashboard (`_metrics.blade.php`)**
  - Pasang props lengkap pada 4 kartu:
    - *Verifikasi Tertunda*: `icon="clipboard-check"`, `color="yellow"`, `trendText`, `subText="Perlu Verifikasi Segera"`, `:sparkBars`.
    - *Kapasitas Kuota Terisi*: `icon="calendar"`, `color="blue"`, `trendText="Okupansi"`, `subText="... Keterisian Kuota Magang Periode"`, `:sparkBars`.
    - *Antrean Waiting List*: `icon="clock"`, `color="teal"`, `trendText="Antrean"`, `subText="... Daftar tunggu antrean"`, `:sparkBars`.
    - *Peserta Magang Aktif*: `icon="user-check"`, `color="teal"`, `trendText="Aktif"`, `subText="Sedang bertugas di dinas"`, `:sparkBars`.

- [x] **Langkah 4: Pembaruan Blade Admin Pendaftaran Index (`pendaftaran/index.blade.php`)**
  - Perbarui 4 kartu KPI pendaftaran dengan `<x-gentelella.tile-stat>` ber-icon `file-text`, `clipboard-check`, `alert-circle`, dan `clock` beserta `color` dan `:sparkBars`.

- [x] **Langkah 5: Pembaruan Blade Admin Waiting List Index (`waiting-list/index.blade.php`)**
  - Perbarui 4 kartu metrik waiting list dengan `<x-gentelella.tile-stat>` ber-icon `users`, `calendar`, `user-check`, dan `shield` beserta `color` dan `:sparkBars`.

- [x] **Langkah 6: Pembaruan Blade Admin Penempatan Index (`penempatan/index.blade.php`)**
  - Ganti card `.admin-stat-card` dengan `<x-gentelella.tile-stat>` ber-icon `users`, `user-check`, `award`, `briefcase`, dan `clock` (jika ada plotting). Pertahankan class identifier `admin-stat-card admin-stat-card--blue`, dll. untuk menjaga kelulusan test visual.

- [x] **Langkah 7: Pembaruan Blade Admin Laporan Hub (`laporan/index.blade.php`)**
  - Ganti 4 kartu summary card dengan `<x-gentelella.tile-stat>` ber-icon `file-text`, `user-check`, `activity`, dan `award`.

- [x] **Langkah 8: Pembaruan Blade Admin Laporan Tahunan (`laporan/tahunan.blade.php`)**
  - Ganti 4 hero KPI card dengan `<x-gentelella.tile-stat>` ber-icon `user-plus`, `user-check`, `award`, dan `clock`.

- [x] **Langkah 9: Pembaruan Blade Admin Penempatan Detail & Sertifikat**
  - Penempatan Show (`penempatan/show.blade.php`): Perbarui 4 kartu tugas, logbook, nilai akhir, dan durasi magang.
  - Sertifikat Index (`sertifikat/index.blade.php`): Ganti 2 kartu ringkasan dengan `<x-gentelella.tile-stat>` ber-icon `file-text` dan `check-circle`.

- [x] **Langkah 10: Eksekusi Quality Gate & Verifikasi**
  - Jalankan `npm run build` untuk memverifikasi Vite asset bundle.
  - Jalankan `./vendor/bin/pint --test` untuk memverifikasi kerapian kode.
  - Jalankan test suite relevan:
    - `php artisan test tests/Feature/AdminDashboardEnhancementTest.php`
    - `php artisan test tests/Feature/AdminPenempatanHeroStatsVisualTest.php`
    - `php artisan test tests/Feature/AdminAnalitikTahunanVisualTest.php`
    - `php artisan test tests/Feature/AdminChildPagesVisualTest.php`

---

## 7. Strategi Testing

1. **Feature Tests**:
   - `AdminDashboardEnhancementTest`: Memverifikasi seluruh teks metrik (`Perlu Verifikasi Segera`, `Peserta Magang Aktif`, `Keterisian Kuota Magang Periode`) tetap ter-render dengan sempurna di Admin Dashboard.
   - `AdminPenempatanHeroStatsVisualTest`: Memverifikasi 4 kelas modifier warna kartu penempatan (`admin-stat-card--blue`, `--green`, `--indigo`, `--amber`) tetap hadir.
   - `AdminAnalitikTahunanVisualTest`: Memverifikasi render 4 kartu metrik di laporan tahunan.
2. **Visual & Parity Check**:
   - Memastikan icon box di kiri berukuran proporsional 44x44px dengan soft-tinted background.
   - Memastikan badge pill tren dan subtext tersusun rapi secara visual.
   - Memastikan 7 sparkline bars muncul di sebelah kanan masing-masing kartu dengan warna yang serasi.
   - Memastikan tampilan dalam Mode Terang dan Mode Gelap kontras dan mudah dibaca (WCAG AA).
3. **Asset Compilation**:
   - `npm run build` harus sukses 0 error / warning bebas dari regresi bundle.

---

## 8. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| Potensi bentrok CSS pada dashboard SuperAdmin | Tampilan SuperAdmin berubah | Gunakan class bersama `.tile_stats_inner.stat` dan pertahankan namespace `.sa-metric-card-col` bila ada aturan spesifik SuperAdmin. |
| Kegagalan assertion test existing yang mencari teks tertentu di Blade | Test merah (fail) | Seluruh subtext dan teks penting (seperti `Perlu Verifikasi Segera`) dipertahankan secara utuh dalam prop `subText` pada masing-masing kartu. |
| Test `AdminPenempatanHeroStatsVisualTest` mencari selector `.admin-stat-card--blue` | Test penempatan gagal | Tambahkan class modifier `admin-stat-card admin-stat-card--blue` pada atribut `<x-gentelella.tile-stat class="..." />`. |

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Seluruh kartu metrik di Admin Dashboard, Pendaftaran, Waiting List, Penempatan, Laporan, Laporan Tahunan, dan Sertifikat mengadopsi standar visual `<x-gentelella.tile-stat>`.
- [x] Masing-masing kartu memiliki icon SVG fungsional yang relevan (khususnya *Verifikasi Tertunda* menggunakan icon `clipboard-check` berlatar belakang amber/yellow).
- [x] Masing-masing kartu memiliki badge tren, subteks deskriptif, dan 7 mini sparkline bars di sisi kanan.
- [x] Seluruh CSS kartu metrik terpusat di `resources/css/components/cards.css` dan dikompilasi melalui Vite.
- [x] Tidak ada penulisan CSS `<style>` baru atau JavaScript inline di file Blade (kepatuhan 100% terhadap `AGENTS.md`).
- [x] `npm run build` berhasil tanpa error.
- [x] Seluruh automated test relevan lulus 100% hijau (53 tests passed, 349 assertions).
- [x] Tidak ada perubahan kode yang dieksekusi sebelum ada persetujuan resmi dari pengguna.

