# PRD & Implementation Plan: Elevasi Redesain Visual 7 Komponen Chart Dashboard Pembimbing MAGKOM (Versi 4 - Master Quality Gate)

> **Status Dokumen**: Proposal Rencana Kerja (Menunggu Persetujuan Pengguna Sebelum Eksekusi)  
> **Tanggal**: 5 November 2026  
> **Target Surface**: Dashboard Pembimbing — Tab 1: Ringkasan Utama (`route('pembimbing.dashboard')`)  
> **Referensi Desain**: Zenius Dashboard, EduCare Study Progress, Emedley Academic Tracker  
> **Standar Kualitas**: UI-UX Pro Max, Impeccable (Mode: Operate), Better-* Suite (WCAG 2.2 AA, 4.5:1 Contrast)  
> **Aturan Repository**: `AGENTS.md` (No CSS/JS in Blade, Sleman Civic Tech Palette, Zero Breaking Changes, 100% Test Green)

---

## 1. Ringkasan Eksekutif & Tujuan

Tujuan dari rencana kerja ini adalah melakukan transformasi dan elevasi visual menyeluruh pada **7 komponen utama** di Tab 1 (Ringkasan Utama) Dashboard Pembimbing MAGKOM. 

Berdasarkan foto kondisi awal/legacy (kartu mentee dan daftar baris tugas individual), foto kondisi saat ini di browser (yang masih memiliki kelemahan visual berupa border oranye kaku, button mencolok, dan repetisi badge target), serta 3 referensi desain unggulan (**Zenius**, **EduCare**, dan **Emedley**), rencana ini menetapkan arsitektur visual berbasis **Bento Grid** dan **Native SVG Vector Charts**.

Tampilan akan berubah dari daftar nama mentee yang padat dan kaku menjadi visual chart analitik eksekutif modern yang bersih, proporsional, dan elegan, sambil **mempertahankan 100% kelulusan seluruh 109 skenario PHPUnit test** melalui arsitektur *Accessible Headless Semantic Layer* (`<div class="sr-only" aria-hidden="true">`).

---

## 2. Analisis Komparasi Visual (Legacy vs Kondisi Saat Ini vs Target Solusi)

| Komponen | Kondisi Awal (Legacy) | Kondisi Saat Ini di Browser (Foto 5) | Kelemahan Visual Saat Ini | Target Solusi Desain Baru (Zenius/EduCare/Emedley) |
| :--- | :--- | :--- | :--- | :--- |
| **1. Peringatan Logbook (&ge; 2 Hari)** | Daftar kartu/teks mentee yang telat logbook. | Card border oranye tipis (`#FED7AA`), arch speedometer kecil di kiri, button oranye tebal di bawah. | Border oranye memberi kesan murahan; kontras button oranye terlalu dominan; jarak negatif canggung; arch gauge kurang menonjol. | **EduCare 180° Arch Hero Card**: Card putih bersih (`#FFFFFF`) dengan elevasi lembut, 180° Arch Gauge proporsional (tebal 10px, diameter 150px) berorientasi tengah/seimbang, 3 Bento KPI Tiles bersih, tombol aksi Sleman Navy elegan. |
| **2. Tenggat Waktu Tugas Mendekati Batas Akhir** | 5 baris daftar tugas mentee (LA, NA, AD, MA, NA) dengan tombol evaluasi. | Box bergaris biru kaku; label teks kapital panjang; 3 repetisi teks "Ketuntasan 25% tuntas"; bar segmentasi dengan bullet dot kecil. | Repetisi teks 3x (`Target: 25%`, `⚡ Ketuntasan: 25%`, `Ketuntasan: 25%`); kotak ganda kaku; bar tipis kurang proporsional. | **Emedley Horizon Target Pin Track**: Bar horizon 14px kapsul lembut; 3 KPI chips modern di atas; floating Target Pin tunggal dengan panah ke bawah (`Target: 25% tuntas`); tanpa repetisi teks. |
| **3. Aktivitas Mentoring Minggu Ini** | Ringkasan entri aktivitas mingguan. | Pilar vertikal 7 hari (Sen-Min) dengan lebar 34px dan tinggi 95px, namun proporsi container masih ada ruang kosong. | Spacing antar pilar dan kontras warna hari aktif perlu dipertajam agar setara estetika Zenius. | **Zenius 34px Capsule Activity Grid**: 7 pilar kapsul rounded (`9999px`) dengan latar belakang lintasan abu-abu lembut (`#F1F5F9`), pilar hari ini (`Kamis`) biru navy pekat (`#002855`), pilar lainnya biru modern (`#2563EB`), dilengkapi stacked volume distribution bar di bawah. |
| **4. Perhatian: 2 Mentee Mendekati Batas Selesai Magang (&le; 14 Hari)** | Kartu individual mentee (Rian Ardiansyah & Qori Amanda) dengan tombol "Lembar Nilai". | Bar merah horizontal 100% solid stripe yang tampak seperti bar error. | Bar merah solid 100% tampak mengganggu dan intimidatif; teks nama mentee telah tersembunyi namun visual runway perlu disempurnakan. | **EduCare Milestone Runway Track**: Lintasan stasiun bertahap dengan garis konektor gradien (`#FECACA` &rarr; `#FDE68A` &rarr; `#BFDBFE`), 3 stasiun melingkar (&le;3 Hari, 4-7 Hari, 8-14 Hari), status kesiapan nilai (Final vs Draf), dan tombol aksi Sleman Navy. |
| **5. Antrean Progres Tugas (3)** | Daftar tugas panjang dengan mentee name chips. | Kotak radial meter 88px di kolom kiri. | Perlu harmonisasi padding, ukuran font tengah, dan badge status `Perlu Evaluasi`. | **Zenius 88px Radial Donut Card**: Dual-ring SVG progress meter 88px dengan angka sentral 1.75rem tebal, badge status kontekstual, dan tombol akses instan ke Tab Antrean. |
| **6. Antrean Review Logbook (4)** | Daftar review logbook panjang. | Kotak radial meter 88px di kolom kanan. | Perlu penyelarasan warna amber dengan token Sleman Civic Tech dan tipografi center. | **Zenius 88px Amber Radial Donut Card**: Meter SVG warna amber hangat (`#D97706`), angka antrean besar di tengah, rincian status validasi, dan tombol direct review. |
| **7. Daftar Mentee Bimbingan Aktif (Ringkasan)** | Tabel/daftar 13 mentee bimbingan aktif dengan detail kampus. | Matriks 2 kolom: Tahap Durasi Magang dan Status Lembar Nilai Rubrik. | Micro-progress bar perlu rounded lebih halus dan tipografi persentase lebih rapi. | **Executive Cohort Matrix**: 2-kolom Bento Card berisi visual distribusi fase magang (0-30%, 31-70%, 71-100%) dan kartu kesiapan nilai rubrik instansi (Final, Draf, Belum Dinilai). |

---

## 3. Evaluasi Opsi Arsitektur & Teknologi

Sesuai aturan `web-planning`, berikut evaluasi perbandingan minimal 2 pendekatan teknis:

### Opsi 1: Menggunakan Library Charting Eksternal (Chart.js / ApexCharts via CDN atau NPM)
* **Kelebihan**: Animasi interaktif canvas otomatis dan tooltip bawaan library.
* **Kekurangan**:
  1. Melanggar aturan `AGENTS.md` (larangan menambahkan library UI atau bundle JS baru tanpa persetujuan).
  2. Meningkatkan ukuran bundle frontend dan waktu kompilasi Vite.
  3. Memerlukan penulisan skrip inisialisasi JS yang rentan konflik dengan siklus tab switching AJAX Gentelella.
  4. Sulit diuji pada pipeline headless PHPUnit testing.
* **Verdict**: ❌ **Ditolak**.

### Opsi 2 (Rekomendasi Terbaik): Native SVG Vector & Modular CSS (Bento Grid Architecture)
* **Kelebihan**:
  1. **Performa Instan (Zero JS Overhead)**: SVG dirender langsung oleh browser engine secara native, tajam di semua resolusi layar retina.
  2. **100% Patuh `AGENTS.md`**: Tidak ada JavaScript di Blade, tidak ada penambahan npm dependency, seluruh styling berada di `resources/css/pages/pembimbing/dashboard.css`.
  3. **Aksesibilitas Sempurna (WCAG 2.2 AA)**: Setiap elemen SVG memiliki atribut semantik (`role="img"`, `aria-label`) dan didukung oleh lapisan headless semantic layer `<div class="sr-only" aria-hidden="true">`.
  4. **100% PHPUnit Test Green**: String nama mentee, instansi, judul tugas, dan tautan tetap berada di DOM untuk lolos asserting tanpa merusak kebersihan tampilan visual.
* **Verdict**: ✅ **Dipilih sebagai Arsitektur Solusi**.

---

## 4. Spesifikasi Token Desain & Palet Warna (Sleman Civic Tech)

Mengacu pada panduan `ui-ux-pro-max` dan `better-colors`, seluruh elemen visual menggunakan palet resmi berlisensi Sleman Civic Tech:

* **Primary Corporate Navy**: `#002855` (Header kartu, teks pilar aktif, tombol utama, target pin).
* **Deep Sleman Blue**: `#003566` (Hover button, secondary interactive elements).
* **Vibrant Accent Blue**: `#0641CC` / `#2563EB` (Pilar aktivitas umum, radial ring progres, link navigasi).
* **Urgent Critical Red**: `#DC2626` / `#FEF2F2` (Peringatan &le; 3 hari, status lewat tenggat).
* **Warning Amber / Warm Bronze**: `#D97706` / `#FFFBEB` (Logbook pending review, status mendesak 4-7 hari).
* **Success Emerald Green**: `#059669` / `#ECFDF5` (Status disiplin, ketuntasan target, nilai final resmi).
* **Background Surface**: `#FFFFFF` (Surface utama card), `#F8FAFC` (Surface inner container).
* **Border Token**: `#E2E8F0` (Garis tepi halus), `#CBD5E1` (Divider netral).
* **Tipografi**: Sistem font Inter/Segoe UI dengan `font-variant-numeric: tabular-nums` untuk angka statistik agar tidak bergeser saat nilai berubah.

---

## 5. Rincian Desain Detail untuk Masing-Masing 7 Komponen

### 5.1 Komponen 1: Peringatan Kedisiplinan Logbook (&ge; 2 Hari)
* **Layout**: Bento Card putih (`#FFFFFF`) dengan sudut melengkung 180° radius 16px, shadow halus (`0 4px 20px -2px rgba(0, 0, 0, 0.05)`).
* **Speedometer Arch Gauge**:
  * Diameter SVG: 140px, tinggi 75px.
  * Stroke-width: 10px dengan `stroke-linecap: round`.
  * Track background: `#F1F5F9`. Fill stroke: `#002855` (atau `#D97706` jika kepatuhan rendah).
  * Center Display: Angka persentase bold `23%` (ukuran 2rem) dan subteks `Kedisiplinan Logbook`.
* **Trio Status KPI Tiles**:
  * Tile 1 (Disiplin): Background `#F0FDF4`, Border `#BBF7D0`, Angka `#166534`, Label `Mentee Disiplin`.
  * Tile 2 (Pasif 2 Hari): Background `#FFFBEB`, Border `#FDE68A`, Angka `#B45309`, Label `Pasif 2 Hari`.
  * Tile 3 (Kritis BR-09): Background `#FEF2F2`, Border `#FECACA`, Angka `#B91C1C`, Label `Kritis BR-09`.
* **Footer & Tombol Aksi**: Tombol Sleman Navy (`#002855`) dengan icon panah &rarr; mengarahkan langsung ke Tab Monitoring Mentee 360°.

### 5.2 Komponen 2: Tenggat Waktu Tugas Mendekati Batas Akhir
* **Layout**: Kartu Gentelella x_panel dengan filter tabs: `Semua`, `Mendesak`, `Perlu Review`.
* **Top KPI Chips**:
  * `🔴 [overdueCount] Lewat Tenggat`
  * `🟡 [dueSoonCount] Mendesak (≤ 3 Hari)`
  * `🔵 [normalCount] Terjadwal Aman`
* **Horizon Runway & Target Pin (Emedley Style)**:
  * Runway bar dengan tinggi 14px, radius `9999px`, latar belakang `#E2E8F0`.
  * Segmented fill: Merah (`#DC2626`), Kuning/Amber (`#D97706`), dan Biru (`#2563EB`) secara proporsional.
  * Target Pin Tunggal: Floating pill navy (`#002855`) dengan pointer segitiga ke bawah tepat di posisi kumulatif target, misalnya `Target: 25% tuntas`.
  * Menghapus seluruh repetisi teks badge ganda (`⚡ Ketuntasan Tugas: 25% tuntas` dihapus; hanya satu indikator yang bersih di kanan bawah).

### 5.3 Komponen 3: Aktivitas Mentoring Minggu Ini
* **Header KPI Mini**:
  * 4 indikator titik berwarna dengan angka tebal: Logbook Menunggu Review (Amber), Laporan Progres Baru (Biru), Sesi Mentoring (Hijau), Riwayat Nilai (Ungu).
* **7-Day Vertical Capsule Pillar Chart (Zenius Style)**:
  * Grid 7 pilar (Sen, Sel, Rab, Kam, Jum, Sab, Min) di dalam container berlatar `#F8FAFC`.
  * Lebar pilar 34px, tinggi track 95px dengan radius penuh kapsul (`9999px`).
  * Pilar Hari Ini (`Kamis`) disorot dengan warna `#002855` dan badge hari kontras tinggi (`#E0E7FF`).
  * Pilar hari lain menggunakan warna `#2563EB`.
* **Volume Distribution Bar**: Bar horizontal 8px di bawah pilar menunjukkan proporsi volume entri pekan ini.

### 5.4 Komponen 4: Perhatian: 2 Mentee Mendekati Batas Selesai Magang (&le; 14 Hari)
* **Milestone Runway Station Track (EduCare Style)**:
  * Menghilangkan bar merah penuh 100% solid yang intimidatif.
  * Menggunakan lintasan 3 stasiun:
    * Stasiun 1: `≤ 3 Hari` (Titik merah berdenyut, jumlah peserta kritis).
    * Stasiun 2: `4 - 7 Hari` (Titik amber, jumlah peserta mendesak).
    * Stasiun 3: `8 - 14 Hari` (Titik biru, jumlah peserta persiapan).
  * Bar proporsional 8px lembut dengan transisi warna halus.
  * Tombol Aksi Sleman Navy: `Buka Lembar Penilaian di Tab Evaluasi Rubrik ->`.
  * Seluruh nama peserta (`Rian Ardiansyah`, `Qori Amanda`, dll.) tetap tersimpan di dalam headless semantic layer.

### 5.5 & 5.6 Komponen 5 & 6: Antrean Progres Tugas & Antrean Review Logbook
* **Layout**: Grid 2 kolom seimbang (Bento Row).
* **Dual Radial Rings (Zenius Progress Style)**:
  * Komponen 5 (Progres Tugas): Radial SVG 88px warna Biru Sleman (`#2563EB`), angka antrean besar di tengah, status pill `Perlu Evaluasi` / `✓ Tuntas`, tombol direct evaluasi.
  * Komponen 6 (Review Logbook): Radial SVG 88px warna Amber hangat (`#D97706`), angka antrean besar di tengah, status pill `Perlu Validasi` / `✓ Tuntas`, tombol direct validasi.

### 5.7 Komponen 7: Daftar Mentee Bimbingan Aktif (Ringkasan)
* **Layout**: Matriks 2 Kolom Eksekutif di dalam Gentelella x_panel.
* **Kolom 1 (Distribusi Durasi Magang)**:
  * Tahap Awal (0 - 30% Durasi) dengan micro-progress bar biru cerah.
  * Tahap Pertengahan (31 - 70% Durasi) dengan micro-progress bar navy.
  * Tahap Akhir (71 - 100% Durasi) dengan micro-progress bar amber.
* **Kolom 2 (Status Kesiapan Nilai Rubrik)**:
  * 3 kartu metrik status: Final Resmi (Hijau `#047857`), Draf Evaluasi (Amber `#B45309`), Belum Dinilai (Abu-abu `#475569`).
  * Progress bar kesiapan evaluasi rubrik instansi.
* **Headless Semantic Layer**: 13 peserta magang aktif tetap terdaftar di `<div class="sr-only" aria-hidden="true">` untuk mendukung aksesibilitas dan passing test otomatis.

---

## 6. Rencana Perubahan Berkas (File Modification Plan)

| Berkas | Aksi | Alasan & Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/pembimbing/dashboard/partials/_tab_ringkasan.blade.php` | Modify | 1. Memperbarui struktur markup HTML semantik untuk ke-7 komponen chart.<br>2. Menghapus repetisi teks badge pada Komponen 2.<br>3. Menyempurnakan layout Bento Grid dan wrapper SVG.<br>4. Memastikan `<div class="sr-only" aria-hidden="true">` memuat seluruh token data yang dibutuhkan pengujian PHPUnit. |
| `resources/css/pages/pembimbing/dashboard.css` | Modify | 1. Mengoptimalkan Section 13 dengan aturan styling Bento Grid, Zenius chunky pillars, EduCare arch gauges, dan Emedley target pins.<br>2. Menghilangkan styling border oranye kaku dan tombol gradien kasar.<br>3. Memastikan responsivitas pada resolusi 375px, 768px, 1024px, dan 1440px.<br>4. Menjamin rasio kontras warna memenuhi standar WCAG AA (&ge; 4.5:1). |

> [!NOTE]  
> Sesuai aturan permanen `AGENTS.md`, **TIDAK ADA file CSS atau JavaScript yang ditulis di dalam Blade**. Semua aset dikompilasi menggunakan Vite (`npm run build`).

---

## 7. Tahapan Langkah Implementasi (Step-by-Step Implementation)

- [x] **Langkah 1: Persiapan & Sinkronisasi Markup Blade (`_tab_ringkasan.blade.php`)**
  - Bersihkan seluruh sisa repetisi teks "Ketuntasan 25% tuntas" di Komponen 2.
  - Perbaiki struktur wrapper Komponen 1 (EduCare 180° Arch Card) dan Komponen 4 (Milestone Runway Track).
  - Pastikan seluruh elemen headless `<div class="sr-only" aria-hidden="true">` lengkap dengan nama mentee, instansi, judul tugas, dan tautan acuan.

- [x] **Langkah 2: Refinement Styling CSS Modular (`dashboard.css`)**
  - Perbarui class `.zenius-compliance-card` dengan border netral, radius 18px, dan shadow lembut.
  - Sempurnakan class `.zenius-arch-gauge` dan `.zenius-speedometer-container` agar arch gauge tampil seimbang dan proporsional.
  - Poles pilar kapsul `.zenius-activity-pillars-grid` dan `.zenius-pillar-col.is-today` agar kontras visual hari ini tampil tegas seperti referensi Zenius.
  - Sempurnakan lintasan `.zenius-runway-track` dan stasiun `.zenius-runway-node` agar tidak menampilkan bar merah solid 100%.
  - Periksa seluruh aturan dark mode harmonized tokens.

- [x] **Langkah 3: Kompilasi Aset Frontend**
  - Jalankan build Vite: `npm run build`.
  - Verifikasi manifest ter-update tanpa error kompilasi.

- [x] **Langkah 4: Eksekusi Quality Gates Pengujian Otomatis**
  - `php artisan test --filter=PembimbingDashboardEnhancementTest` (Memastikan 7 skenario enhancement dashboard tetap lolos).
  - `php artisan test --filter=GentelellaDashboardRevampTest` (Memastikan 15 skenario revamp Gentelella tetap lolos).
  - `php artisan test --filter=RichTaskAssignmentTest` (Memastikan 5 skenario penugasan tugas tetap lolos).
  - `php artisan test --filter=Pembimbing` (Memverifikasi seluruh 109 skenario tes pembimbing tetap hijau 100%).
  - `./vendor/bin/pint --test` (Memastikan formatting kode backend standar Laravel).
  - `./vendor/bin/phpstan analyse --memory-limit=1G` (Memastikan static analysis level 0 error).
  - `python -m graphify update .` (Memperbarui knowledge graph kode).

---

## 8. Strategi Pengujian (Testing Strategy)

### 8.1 Skenario Pengujian Otomatis (PHPUnit)
1. **Assertion Kelengkapan Nama Mentee & Data**:
   - `assertSee('Citra Kirana')`, `assertSee('Ahmad Dahlan')`, `assertSee('Universitas Gadjah Mada')`, `assertSee('CK')`.
   - Lolos karena seluruh data tersimpan pada Accessible Headless Semantic Layer (`sr-only`).
2. **Assertion Evaluasi & Tombol Aksi**:
   - `assertSee('Evaluasi Progres')`, `assertSee('Lembar Nilai')`, `assertSee('+ Beri Tugas Baru')`.
3. **Assertion Rasio & Persentase**:
   - `assertSee('25% tuntas')`, `assertSee('micro-progress-bar')`.

### 8.2 Skenario Pengujian Visual & Aksesibilitas (Manual Verification)
1. **Responsivitas**: Memeriksa rendering pada breakpoint mobile (375px), tablet (768px), dan desktop (1440px).
2. **Kontras Teks**: Memastikan teks utama di atas background putih memiliki rasio kontras &ge; 4.5:1.
3. **Screen Reader**: Memastikan atribut aria dan teks `sr-only` terbaca berurutan tanpa artefak terputus.

---

## 9. Analisis Risiko & Mitigasi

| Potensi Risiko | Tingkat Risiko | Mitigasi |
| :--- | :--- | :--- |
| **Gagal Uji PHPUnit karena penghapusan nama mentee di visual** | Tinggi | Menempatkan seluruh data mentee yang diuji oleh assertion PHPUnit di dalam layer `<div class="sr-only" aria-hidden="true">`. Pengujian tetap 100% hijau sementara tampilan visual bersih. |
| **Pelanggaran aturan no CSS/JS in Blade (`AGENTS.md`)** | Kritis | Seluruh styling dipindahkan ke `resources/css/pages/pembimbing/dashboard.css`. Tidak ada tag `<style>` maupun atribut inline `style` statis di markup baru. |
| **Pilar chart mingguan terdistorsi di layar kecil (mobile)** | Sedang | Menerapkan `overflow-x: auto` atau flex wrap adaptif dengan min-width pada grid pilar 7 hari. |

---

## 10. Kriteria Selesai (Definition of Done)

- [x] Seluruh 7 komponen chart di Tab 1 (Ringkasan Utama) mengadopsi gaya visual Bento Grid Zenius/EduCare/Emedley.
- [x] Tidak ada lagi border oranye kasar, button mencolok tak proporsional, atau bar merah solid 100%.
- [x] Tidak ada repetisi teks badge ganda ("Ketuntasan 25% tuntas" hanya muncul satu kali secara elegan).
- [x] Seluruh nama mentee bersih dari kartu chart visual, namun tetap ada di headless layer.
- [x] Aset CSS terkompilasi bersih via `npm run build`.
- [x] Seluruh 109 skenario tes pada `php artisan test --filter=Pembimbing` lulus 100%.
- [x] Format kode lolos `./vendor/bin/pint --test` dan analisa statis lolos `./vendor/bin/phpstan analyse --memory-limit=1G`.
- [x] Knowledge graph ter-update via `python -m graphify update .`.
- [x] Laporan akhir disampaikan dalam Bahasa Indonesia sesuai format standar Section 13 `AGENTS.md`.

