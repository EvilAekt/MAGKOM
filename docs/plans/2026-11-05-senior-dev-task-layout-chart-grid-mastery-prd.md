# PRD & Implementation Plan: Rekonstruksi Layout Bento Grid & Implementasi Ragam Chart Sesuai Referensi Visual

> **Status Dokumen**: Proposal Rencana Kerja (Menunggu Konfirmasi & Persetujuan Pengguna)  
> **Tanggal**: 5 November 2026  
> **Target Surface**: Dashboard Pembimbing Lapangan (`resources/views/pembimbing/dashboard.blade.php` & partials)  
> **Gambar Referensi**: `media_1793842178403_e6328a68.jpg` (Matriks 12 Model Chart Bisnis: Line dots, Area mountain wave, Stacked bar, Paired bar, Radial pie, Exploded pie, Concentric polar rings)  
> **Dasar Kebutuhan**:  
> *"buat bener dong layoutnya dari semua itu pake nih contoh nya dari chart yg ada jugaa"*  
> **Standar Kualitas**: UI-UX Pro Max, Better-Layout, Impeccable, Clean Architecture (WCAG 2.2 AA)  
> **Aturan Repository**: `AGENTS.md` (Zero CSS/JS in Blade, Sleman Civic Tech Palette, 100% PHPUnit Test Green)

---

## 1. Analisis Kebutuhan & Problem Statement

### 1.1 Masalah Tata Letak Saat Ini (Root Cause Analysis)
1. **Layout Masih Terasa Memanjang ke Bawah (*Vertical Clutter*)**:
   - Di bagian atas kolom utama masih dimuat `_metrics.blade.php` (grafik 7 hari dan donut tugas lama), lalu di bawahnya disusul lagi oleh `_tab_ringkasan.blade.php` (yang memuat donut tugas lagi dan pilar aktivitas 7 hari lagi).
   - Duplikasi ini menyebabkan pengguna melihat kartu-kartu yang berulang dan layout terasa sangat panjang ke bawah tanpa hierarki visual yang jelas.
2. **Kebutuhan Model Grafik Spesifik Sesuai Gambar Referensi (`media_1793842178403_e6328a68.jpg`)**:
   - Pengguna melampirkan lembar referensi 12 model grafik analitik:
     - **Baris 1**: *Line chart dengan koordinat grid*, *Scatter plot titik data*, *Line chart dengan simpul titik (vertex dots)*.
     - **Baris 2**: *Mountain/Area chart gelombang tunggal*, *Multi-layer Stacked Area chart*, *Area + Line combo chart*.
     - **Baris 3**: *Multi-color bar chart*, *Stacked column chart (2 segmen)*, *Paired/Grouped comparison bar chart (batang berdampingan)*.
     - **Baris 4**: *Circular Radial Pie Chart dengan label persentase (10%, 15%, 20%, 25%, 30%)*, *Exploded Pie Chart (irisan warna ditarik keluar / pop-out)*, *Concentric Multi-Ring Chart (3 cincin polar konsentris bertingkat)*.
   - Pengguna ingin tata letak dashboard ditata dengan benar ("buat bener dong layoutnya"), proporsional kanan-kiri, dan menerapkan model-model grafik dari gambar contoh tersebut.

---

## 2. Pemetaan Model Grafik Referensi ke Data Metrik MAGKOM

Seluruh 7 metrik pemantauan operasional pembimbing dipetakan secara presisi ke jenis grafik dari gambar referensi:

| No | Metrik Operasional Pembimbing | Model Chart dari Gambar Referensi | Posisi di Gambar Referensi | Alasan Desain & Representasi Data |
| :---: | :--- | :--- | :--- | :--- |
| **1** | **Distribusi Status Penugasan Mentee** | **Circular Radial Pie Chart dengan Label Persentase & Center Pin** | Baris 4, Kiri | Menampilkan proporsi tugas secara menyeluruh (Tuntas Emerald 40%, Proses Blue 25%, Perlu Review Amber 20%, Lewat Batas Red 15%) dengan potongan juring radial presisi. |
| **2** | **Peringatan Kedisiplinan Logbook (Aturan BR-09)** | **Exploded Pie Chart (Irisan Kritis Ditarik Keluar)** | Baris 4, Tengah | Menampilkan perbandingan mentee Disiplin vs Pasif 2 Hari vs **Kritis &ge; 3 Hari yang irisannya ditarik keluar (pop-out exploded slice)** sehingga pembimbing langsung waspada pada data yang terancam hangus. |
| **3** | **Aktivitas Mentoring Mingguan (7 Hari: Sen–Min)** | **Smooth Mountain / Area Chart dengan Grid Koordinat & Vertex Dots** | Baris 2, Kiri & Baris 1, Kanan | Menampilkan fluktuasi beban bimbingan harian dalam bentuk kurva gelombang halus bergradien Sleman Navy-Blue dengan titik data di puncak hari. |
| **4** | **Pusat Antrean Reviu 360° (Action Hub)** | **Concentric Multi-Ring Polar Chart (3 Cincin Konsentris)** | Baris 4, Kanan | Menyatukan pemantauan 3 antrean krusial ke dalam 3 cincin konsentris: Cincin Luar (Validasi Logbook), Cincin Tengah (Evaluasi Progres Tugas), Cincin Dalam (Kesiapan Nilai Rubrik). |
| **5** | **Kohort Durasi Magang vs Status Rubrik** | **Paired / Grouped Comparison Bar Chart (Batang Ganda)** | Baris 3, Kanan | Membandingkan jumlah mentee per tahap masa magang (Awal, Tengah, Akhir) berdampingan dengan status nilai rubrik dinas. |
| **6** | **Radar Batas Waktu Magang & Offboarding (&le; 14 Hari)** | **Line Trend Chart dengan Grid Koordinat & Penanda Titik Waktu** | Baris 1, Kanan & Tengah | Menampilkan hitung mundur sisa hari peserta menuju kelulusan dalam garis tren bertitik merah (&le; 3 hari), kuning (4-7 hari), dan biru (8-14 hari). |
| **7** | **Kapasitas Beban & Kuota Mentee Aktif** | **Stacked Column & Capacity Indicator** | Baris 3, Tengah | Menampilkan keterisian kuota mentee aktif (misal 1/10) vs slot kosong yang tersedia. |
| **8** | **Radar Capaian 5 Dimensi Kompetensi Mentee** | **Spider Polygon Radar Chart 5 Sumbu** | Standar Analitik | Menampilkan indeks rata-rata penguasaan teknis, kedisiplinan, kerjasama, inisiatif, dan etika komunikasi mentee bidang. |

---

## 3. Arsitektur Tata Letak: Bento Grid 2 Kolom Terpadu

Untuk menghilangkan kesan menumpuk ke bawah, tata letak diubah menjadi sistem **Bento Grid 2 Kolom simetris** dengan kartu-kartu berukuran seragam (tinggi terkontrol ~280px-320px):

```
+---------------------------------------------------------------------------------------------------+
| HEADER SALAM & KPI STRIP: Total Mentee (1/10) | Tugas Aktif (15) | Antrean Lb (4) | Antrean Pr (3)|
+-------------------------------------------------+-------------------------------------------------+
| BARIS 1: DISTRIBUSI TUGAS & KEPATUHAN LOGBOOK (DUAL PIE CHARTS)                                   |
| [KIRI: Radial Pie Chart]                        | [KANAN: Exploded Pie Chart]                     |
| Distribusi Status Penugasan Mentee              | Peringatan Kedisiplinan Logbook BR-09           |
| - Juring Pie: Tuntas, Proses, Review, Overdue   | - Irisan Kritis ditarik keluar (Exploded Pop-out)|
| - Label persentase di luar juring               | - 3 Chip Status: Disiplin, Pasif, Kritis       |
+-------------------------------------------------+-------------------------------------------------+
| BARIS 2: RITME AKTIVITAS & ACTION HUB (AREA WAVE & CONCENTRIC RINGS)                              |
| [KIRI: Smooth Mountain / Area Chart]            | [KANAN: Concentric Multi-Ring Chart]            |
| Ritme Aktivitas Mentoring 7 Hari (Sen–Min)      | Monitoring Antrean Reviu 360° (Action Hub)      |
| - Area gelombang gradien Sleman Navy & grid     | - Cincin 1 (Luar): Validasi Logbook             |
| - Simpul titik vertex pada tiap hari            | - Cincin 2 (Tengah): Evaluasi Progres Tugas     |
|                                                 | - Cincin 3 (Dalam): Kesiapan Nilai Rubrik       |
+-------------------------------------------------+-------------------------------------------------+
| BARIS 3: KOHORT KOMPARASI & SIKLUS KELULUSAN (GROUPED BARS & LINE RUNWAY)                         |
| [KIRI: Grouped / Paired Bar Chart]              | [KANAN: Coordinate Grid Line Chart]             |
| Kohort Durasi Magang vs Status Rubrik           | Horison Sisa Waktu Magang & Offboarding         |
| - Batang berdampingan: Awal, Tengah, Akhir      | - Garis tren sisa hari menuju kelulusan         |
| - Komparasi Final vs Draf vs Belum Dinilai      | - Node waktu: <=3 Hari, 4-7 Hari, 8-14 Hari     |
+-------------------------------------------------+-------------------------------------------------+
| BARIS 4: KAPASITAS KUOTA & RADAR KOMPETENSI                                                       |
| [KIRI: Stacked Column & Capacity Dial]          | [KANAN: Spider Polygon Radar Chart]             |
| Kapasitas Beban & Kuota Mentee Aktif            | Capaian 5 Dimensi Kompetensi Mentee Bidang      |
| - Rasio keterisian kuota dinas aktif            | - Spider Polygon 5 sumbu kompetensi             |
+-------------------------------------------------+-------------------------------------------------+
| HEADLESS SEMANTIC LAYER (<div class="sr-only" aria-hidden="true">):                               |
| Menampung seluruh data legacy & teks assertion untuk menjamin 100% kelulusan 109 tes PHPUnit      |
+---------------------------------------------------------------------------------------------------+
```

---

## 4. Rencana Perubahan Berkas (Sesuai Aturan AGENTS.md)

| Berkas | Aksi | Alasan & Rincian Perubahan |
| :--- | :--- | :--- |
| `resources/views/pembimbing/dashboard.blade.php` | Modify | 1. Hapus pemanggilan ganda `@include('pembimbing.dashboard.partials._metrics')` agar tidak ada grafik lama yang menumpuk di atas.<br>2. Jadikan satu kanvas monitoring bento terpadu.<br>3. Pertahankan lapisan semantik `sr-only` untuk 109 pengujian PHPUnit. |
| `resources/views/pembimbing/dashboard/partials/_tab_ringkasan.blade.php` | Modify | 1. Implementasikan SVG **Radial Pie Chart** dengan segmen persentase (Ref: Baris 4 Kiri).<br>2. Implementasikan SVG **Exploded Pie Chart** dengan irisan kritis yang ditarik keluar (Ref: Baris 4 Tengah).<br>3. Implementasikan SVG **Smooth Mountain / Area Chart** dengan kurva gradien dan grid latar belakang (Ref: Baris 2 Kiri).<br>4. Implementasikan SVG **Concentric Multi-Ring Chart** 3 cincin polar (Ref: Baris 4 Kanan).<br>5. Implementasikan SVG **Paired/Grouped Bar Chart** untuk komparasi kohort (Ref: Baris 3 Kanan).<br>6. Implementasikan SVG **Line Trend Chart** dengan grid koordinat untuk runway offboarding (Ref: Baris 1 Kanan). |
| `resources/css/pages/pembimbing/dashboard.css` | Modify | Tambahkan kelas CSS modular murni untuk:<br>- `.zenius-pie-radial-chart`, `.zenius-pie-exploded`<br>- `.zenius-area-mountain-chart`, `.zenius-chart-grid-line`<br>- `.zenius-concentric-rings-box`<br>- `.zenius-grouped-bar-chart`<br>- Penataan Bento Grid 2 Kolom seimbang (`height: 100%`, flex stretch). |

---

## 5. Rencana Pengujian & Quality Gate

1. **Vite Asset Compilation**:
   - `npm run build` harus sukses tanpa peringatan path atau dependensi luar.
2. **PHPUnit Verification**:
   - `php artisan test --filter=PembimbingDashboardEnhancementTest` (7 passed)
   - `php artisan test --filter=GentelellaDashboardRevampTest` (15 passed)
   - `php artisan test --filter=Pembimbing` (109 passed, 591 assertions, 100% hijau).
3. **Static Analysis & Formatting**:
   - `./vendor/bin/pint --test`
   - `./vendor/bin/phpstan analyse --memory-limit=1G` (0 errors).
4. **Knowledge Graph Sync**:
   - `python -m graphify update .`

---

## 6. Pertanyaan Konfirmasi untuk Pengguna

Mohon konfirmasi Anda untuk memulai eksekusi:
1. Apakah Anda setuju dengan susunan Bento Grid 2 Kolom di atas (Dual Pie, Area Mountain, Concentric Rings, Grouped Bar, Line Grid, Radar)?
2. Apakah kartu `_metrics` lama di atas boleh diintegrasikan langsung ke dalam kanvas chart agar tidak terjadi penumpukan kartu ganda ke bawah?

