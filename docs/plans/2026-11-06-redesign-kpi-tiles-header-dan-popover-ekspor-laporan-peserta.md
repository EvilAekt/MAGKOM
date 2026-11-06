# TASK / PRD: Transformasi Header, Tile KPI, Sidebar, dan Popover Ekspor Halaman Laporan Peserta
## Dokumen Spesifikasi Teknis & Rencana Kerja (Senior Developer ke Junior Developer / AI Agent)
- **Target Modul**: Portal Pembimbing — Modul Laporan Peserta (`/pembimbing/laporan-kinerja`) & Layout Navigasi
- **Role Pengembang**: Junior Developer / AI Assistant
- **Pemberi Task**: Senior Developer MagKom Sleman
- **Prioritas**: UI/UX Standardization, Design System Alignment, & Feature Polish
- **Dokumen Referensi**: 
  - Gambar 1: `media_1793950384268_01bbcb5e.png` (3 Kartu KPI Lama yang akan diganti)
  - Gambar 2: `media_1793950422926_46520131.png` (Target 4 Tile Stats MagKom Gentelella)
  - Gambar 3: `media_1793950478301_48c821be.png` (Sidebar Navigasi: Rename Laporan Peserta)
  - Gambar 4: `media_1793950583658_52d79ad1.png` (Topbar Header: Title Laporan Peserta)
  - Gambar 5: `media_1793950685842_314df061.png` (Target Komponen Popover Ekspor Rekap Cepat)

---

# 1. LATAR BELAKANG & TUJUAN (OBJECTIVES)

Halo Junior Dev / Agent,

Berdasarkan tinjauan terbaru antarmuka Portal Pembimbing, halaman pelaporan pembimbing perlu diselaraskan sepenuhnya dengan standar desain sistem MagKom Sleman. Saat ini terdapat beberapa inkonsistensi visual, penamaan menu, dan komponen yang perlu diperbaiki:

1. **Transformasi Kartu KPI Menjadi 4 Tile Stats Gentelella (Gambar 1 -> Gambar 2)**:
   - Tiga kartu KPI lama (*Total Mentee Dibimbing*, *Logbook Direview*, *Tugas Diselesaikan*) diubah menjadi **4 Tile Stat Gentelella** standar MagKom (persis seperti pada direktori peserta):
     - **Tile 1 (Total Mentee)**: `color="teal"`, title="TOTAL MENTEE", value, trend badge `+ X Total`, subtext "Seluruh riwayat penempatan", spark bars.
     - **Tile 2 (Peserta Aktif)**: `color="blue"`, title="PESERTA AKTIF", value, trend badge `+ Y%`, subtext "Sedang menjalankan magang", spark bars.
     - **Tile 3 (Perlu Perhatian)**: `color="yellow"`, title="PERLU PERHATIAN", value, trend badge `+ Z Alert`, subtext "Tugas overdue / logbook bolong", spark bars.
     - **Tile 4 (Alumni Selesai)**: `color="teal"`, title="ALUMNI SELESAI", value, trend badge `+ W%`, subtext "Magang tuntas & lulus", spark bars.

2. **Pembaruan Nama Menu Sidebar (TASK 04 Resmi Terkonfirmasi) (Gambar 3)**:
   - Mengubah label menu navigasi pembimbing pada sidebar dari **"Laporan Kinerja ASN"** menjadi **"Laporan Peserta"**.

3. **Penyelarasan Header Topbar (Gambar 4)**:
   - Mengganti teks breadcrumb topbar `Beranda / Dashboard` pada halaman laporan menjadi judul bersih **"Laporan Peserta"** (menggunakan `@section('topbar_title', 'Laporan Peserta')` selaras dengan Gambar 4).

4. **Pembersihan Teks Header & Breadcrumb Halaman**:
   - Menghapus blok teks lama:
     - Breadcrumbs: `Dashboard / Laporan Kinerja Mentoring`
     - Judul: `Laporan Kinerja Pembimbing Lapangan ASN (F-09, F-10)`
     - Subtitle: `Rekapitulasi portofolio bimbingan, jam mentoring kedinasan, dan capaian kompetensi seluruh mentee.`
   - Halaman menjadi jauh lebih ringkas, modern, dan langsung memuat bar aksi serta tile statistik.

5. **Modernisasi Tombol Ekspor Menjadi Popover "Ekspor Rekap Cepat" (Gambar 5)**:
   - Mengganti tombol cetak statis lama dengan komponen **Popover Ekspor Rekap Cepat** (mengadopsi pola sukses dari `admin/penempatan`):
     - Tombol pemicu: `↓ Ekspor Rekap ^` (tombol biru dengan icon download dan chevron rotasi).
     - Floating Card Popover:
       - Header: `Ekspor Rekap Cepat`, subtitle `Data hasil filter aktif`, counter badge `{{ $totalMentee }} Baris`.
       - Opsi 1 (Default): **Microsoft Excel** (`.xlsx`) dengan badge `Rekomendasi` dan keterangan *"Tabel resmi bergaris rapi & filterable"*.
       - Opsi 2: **CSV Tabular** (`.csv`) dengan badge `Cepat` dan keterangan *"Data mentah teks pemisah koma terstandarisasi"*.
       - Opsi 3: **Dokumen Resmi (.pdf)** (`.pdf`) dengan badge `Legal Sah` dan keterangan *"Rekapitulasi cetak ber-kop resmi kedinasan"*.
       - Aksi: Tombol `Batal` dan tombol dinamis `↓ Unduh Excel (.xlsx)` / `↓ Unduh CSV (.csv)` / `↓ Cetak Dokumen (.pdf)`.

---

# 2. ASUMSI & KEPUTUSAN TEKNIS (WORKING ASSUMPTIONS)

1. **Perhitungan 4 Metrik Tile Stats**:
   - Di `LaporanKinerjaController::index()`, tambahkan kalkulasi kedisiplinan mentee yang selaras 100% dengan `PesertaController`:
     - `$countAktif`: Penempatan aktif milik pembimbing.
     - `$countSelesai`: Penempatan selesai milik pembimbing.
     - `$countPerhatian`: Mentee aktif yang memiliki tugas *overdue* (lewat deadline & belum selesai) ATAU logbook yang bolong (>= 3 hari belum mengisi logbook).
     - `$totalMentee`: Akumulasi `$countAktif + $countSelesai`.
     - Persentase rasio: `$persenAktif`, `$persenSelesai`, `$persenPerhatian`.
2. **Topbar Title**:
   - Dalam layout `resources/views/layouts/dashboard.blade.php:3590`, header atas memeriksa `@hasSection('topbar_title')`. Jika didefinisikan, layout otomatis merender `<h1 class="topbar-title">@yield('topbar_title')</h1>` tanpa breadcrumb dashboard.
   - Maka di view laporan cukup definisikan `@section('topbar_title', 'Laporan Peserta')`.
3. **Sidebar Label**:
   - Di `resources/views/layouts/dashboard.blade.php`, ubah `<span>{{ __('Laporan Kinerja ASN') }}</span>` menjadi `<span>{{ __('Laporan Peserta') }}</span>` untuk role pembimbing.
4. **Backend Endpoint Ekspor (Excel, CSV, PDF)**:
   - Tambahkan endpoint `/pembimbing/laporan-kinerja/export` di `routes/web.php` yang memetakan ke `LaporanKinerjaController::export()`.
   - Format:
     - `xlsx`: Stream file spreadsheet XML/Excel resmi bergaris rapi berisi 6 kolom (Nama Mentee, Institusi & Jurusan, Bidang Magang, Tugas Selesai, Logbook, Status).
     - `csv`: Stream CSV murni dengan header UTF-8 BOM.
     - `pdf`: Mengarahkan ke tampilan cetak resmi LPJ (`route('pembimbing.laporan-kinerja.cetak')`).
5. **Kepatuhan Aturan CSS & JS**:
   - Styling popover ekspor dimasukkan ke `resources/css/pages/pembimbing/laporan-kinerja-index.css` (menggunakan kelas `.adm-export-*` terstandarisasi).
   - Interaksi popover (toggle popup, klik luar, switch radio format card, tombol download dinamis) diletakkan di file JavaScript terpisah `resources/js/pages/pembimbing/laporan-kinerja-index.js` dan didaftarkan pada Vite (bebas inline script di Blade).

---

# 3. EVALUASI OPSI PENDEKATAN ARSITEKTUR

Kami mengevaluasi dua opsi pendekatan implementasi:

- **Opsi A (Terpilih: Reusable Gentelella Component & Standalone Vite Module)**:
  - Menggunakan komponen native Blade `<x-gentelella.tile-stat>` untuk ke-4 metrik.
  - Memisahkan logika popover ekspor ke file JS modular `resources/js/pages/pembimbing/laporan-kinerja-index.js`.
  - Mengimplementasikan endpoint ekspor fleksibel di controller yang mendukung CSV, XLSX, dan redirect PDF.
  - *Kelebihan*: Arsitektur sangat modular, bebas dari script/styling inline di Blade, 100% konsisten dengan Gambar 2, Gambar 3, Gambar 4, dan Gambar 5.
  - *Kekurangan*: Memerlukan pembuatan 1 file JS baru yang didaftarkan ke `vite.config.js`.

- **Opsi B (Menulis JavaScript Inline di Blade & Cetak PDF Saja)**:
  - Menggunakan script inline `<script>` di Blade untuk popover dan hanya mengarahkan seluruh format ke halaman cetak.
  - *Kelebihan*: Cepat dibuat.
  - *Kekurangan*: Melanggar aturan ketat repositori (`AGENTS.md` Pasal 3.1 & 5: Dilarang menulis JS di Blade), merusak pipeline Vite, dan tombol unduh Excel/CSV tidak menghasilkan file riil.

-> **Keputusan Senior Dev**: Wajib gunakan **Opsi A**.

---

# 4. PEMETAAN BERKAS YANG DIUBAH (FILE IMPACT TABLE)

| Berkas | Aksi | Ringkasan Pekerjaan |
| :--- | :---: | :--- |
| `app/Http/Controllers/Pembimbing/LaporanKinerjaController.php` | Modify | 1. Tambahkan kalkulasi `$countAktif`, `$countSelesai`, `$countPerhatian`, `$persenAktif`, `$persenSelesai`.<br>2. Tambahkan method `export(Request $request)` untuk meng-generate format `.xlsx` dan `.csv` atau redirect ke `.pdf`. |
| `routes/web.php` | Modify | Tambahkan route `Route::get('/laporan-kinerja/export', [PembimbingLaporanKinerjaController::class, 'export'])->name('laporan-kinerja.export');`. |
| `resources/views/layouts/dashboard.blade.php` | Modify | Ubah label sidebar navigasi pembimbing dari `Laporan Kinerja ASN` menjadi `Laporan Peserta`. |
| `resources/views/pembimbing/laporan-kinerja/index.blade.php` | Modify | 1. Pasang `@section('topbar_title', 'Laporan Peserta')`.<br>2. Hapus teks breadcrumbs, judul F-09 F-10, dan subtitle lama.<br>3. Pasang 4 Tile Stat `<x-gentelella.tile-stat>` selaras Gambar 2.<br>4. Pasang markup popover `Ekspor Rekap Cepat` selaras Gambar 5.<br>5. `@vite(['resources/js/pages/pembimbing/laporan-kinerja-index.js'])`. |
| `resources/css/pages/pembimbing/laporan-kinerja-index.css` | Modify | Tambahkan styling `.adm-export-popover`, `.adm-format-card`, `.adm-badge-*`, chevron animation, dan dark mode overrides. |
| `resources/js/pages/pembimbing/laporan-kinerja-index.js` | Create | Logika interaksi popover ekspor: toggle buka/tutup, click outside listener, switch format radio card, dan trigger download URL. |
| `vite.config.js` | Modify | Daftarkan `resources/js/pages/pembimbing/laporan-kinerja-index.js` ke array input Vite. |
| `tests/Feature/PembimbingLaporanKinerjaViewTest.php` | Modify | Perbarui assertion test untuk memvalidasi topbar title, label sidebar baru, 4 tile stat, ketiadaan teks F-09, dan ketersediaan popover ekspor. |

---

# 5. PANDUAN LANGKAH IMPLEMENTASI BERTINGKAT (STEP-BY-STEP RUNBOOK)

Junior Developer / Agent diwajibkan mengeksekusi langkah-langkah di bawah ini secara berurutan:

### Tahap 1: Backend Controller & Routing (`LaporanKinerjaController.php` & `routes/web.php`)
- [x] **1.1 Kalkulasi 4 Metrik Tile Stat**:
  - Di `LaporanKinerjaController::index()`, hitung data mentee aktif, alumni selesai, dan mentee yang perlu perhatian (overdue task atau logbook bolong >= 3 hari) selaras dengan implementasi `PesertaController`.
  - Teruskan variabel ke view: `$totalMentee`, `$countAktif`, `$countSelesai`, `$countPerhatian`, `$persenAktif`, `$persenSelesai`.
- [x] **1.2 Method Ekspor Rekap (`export`)**:
  - Tambahkan method `export(Request $request)`:
    - Format `pdf`: redirect ke `route('pembimbing.laporan-kinerja.cetak')`.
    - Format `csv`: stream response file CSV dengan delimiter koma dan header 6 kolom.
    - Format `xlsx`: stream response format Excel tabular XML dengan metadata resmi.
- [x] **1.3 Daftarkan Route Ekspor**:
  - Di `routes/web.php` dalam grup pembimbing:
    ```php
    Route::get('/laporan-kinerja/export', [PembimbingLaporanKinerjaController::class, 'export'])->name('laporan-kinerja.export');
    ```

---

### Tahap 2: Navigasi & Sidebar (`dashboard.blade.php`)
- [x] **2.1 Rename Menu Sidebar Pembimbing**:
  - Cari `{{ __('Laporan Kinerja ASN') }}` di `resources/views/layouts/dashboard.blade.php`.
  - Ubah menjadi `{{ __('Laporan Peserta') }}` (baik pada desktop sidebar maupun sub-navigasi mobile jika ada).

---

### Tahap 3: Frontend Styling & JavaScript Modular
- [x] **3.1 Buat Script Popover (`resources/js/pages/pembimbing/laporan-kinerja-index.js`)**:
  - Implementasikan event listener:
    - Buka/tutup popover saat tombol `#btnToggleExportPopover` diklik.
    - Tutup popover jika user menekan tombol Escape atau mengklik area di luar popover (*click outside*).
    - Perbarui tampilan card pilihan saat format diubah (toggle class `.adm-format-selected` dan perbarui label tombol download, misal *"Unduh Excel (.xlsx)"*, *"Unduh CSV (.csv)"*, *"Cetak Dokumen (.pdf)"*).
    - Eksekusi download saat tombol submit popover diklik dengan membawa format yang dipilih.
- [x] **3.2 Daftarkan Script di `vite.config.js`**:
  - Tambahkan `'resources/js/pages/pembimbing/laporan-kinerja-index.js'` ke dalam `input` Vite.
- [x] **3.3 Lengkapi CSS Popover di `laporan-kinerja-index.css`**:
  - Tambahkan aturan CSS popover, pointer arrow, format radio cards, dan badge status (`Rekomendasi`, `Cepat`, `Legal Sah`) selaras dengan Gambar 5.

---

### Tahap 4: Blade View Refactor (`laporan-kinerja/index.blade.php`)
- [x] **4.1 Topbar Title & Hapus Header Teks Lama**:
  - Pasang `@section('topbar_title', 'Laporan Peserta')` di bagian atas Blade.
  - Hapus tag `<div class="laporan-header-row">` lama yang memuat breadcrumb, judul F-09, dan subtitle deskripsi panjang.
- [x] **4.2 Bar Aksi Ekspor**:
  - Buat bar aksi flex di bagian atas kanan yang menampung tombol `#btnToggleExportPopover` (`↓ Ekspor Rekap ^`) dan kontainer popover `#admExportPopover`.
- [x] **4.3 Pasang 4 Tile Stat Gentelella**:
  - Ganti container `.laporan-kpi-grid` lama dengan `.tiles-grid` yang memuat 4 komponen `<x-gentelella.tile-stat>`:
    1. **Total Mentee**: `title="Total Mentee"`, `value="{{ $totalMentee }}"`, `trendText="{{ $totalMentee }} Total"`, `subText="Seluruh riwayat penempatan"`, `color="teal"`.
    2. **Peserta Aktif**: `title="Peserta Aktif"`, `value="{{ $countAktif }}"`, `trendText="{{ $persenAktif }}%"`, `subText="Sedang menjalankan magang"`, `color="blue"`.
    3. **Perlu Perhatian**: `title="Perlu Perhatian"`, `value="{{ $countPerhatian }}"`, `trendText="{{ $countPerhatian }} Alert"`, `subText="Tugas overdue / logbook bolong"`, `color="yellow"`.
    4. **Alumni Selesai**: `title="Alumni Selesai"`, `value="{{ $countSelesai }}"`, `trendText="{{ $persenSelesai }}%"`, `subText="Magang tuntas & lulus"`, `color="teal"`.
- [x] **4.4 Muat Skrip Vite**:
  - Tambahkan `@push('scripts') @vite(['resources/js/pages/pembimbing/laporan-kinerja-index.js']) @endpush`.

---

### Tahap 5: Build, Testing, & Quality Gates
- [x] Jalankan kompilasi Vite: `npm.cmd run build` (pastikan tidak ada error kompilasi).
- [x] Jalankan formatting Pint: `php vendor/bin/pint --test`.
- [x] Perbarui Feature Test di `tests/Feature/PembimbingLaporanKinerjaViewTest.php` untuk memvalidasi 4 Tile Stat, topbar title `Laporan Peserta`, label sidebar baru, ketiadaan teks lama F-09, dan endpoint ekspor.
- [x] Jalankan test suite: `php artisan test tests/Feature/PembimbingLaporanKinerjaViewTest.php tests/Feature/AdvancedRoleEnhancementPhaseSixTest.php`.

---

# 6. SKENARIO PENGUJIAN & VERIFIKASI (TESTING STRATEGY)

### Skenario 1: Verifikasi Topbar & Sidebar Navigasi
- Buka dashboard pembimbing.
- **Sidebar**: Memastikan menu tertulis `Laporan Peserta` (bukan lagi `Laporan Kinerja ASN`).
- **Topbar**: Saat berada di route `pembimbing.laporan-kinerja.index`, topbar header menampilkan judul `Laporan Peserta` (bukan lagi `Beranda / Dashboard`).

### Skenario 2: Verifikasi Ketiadaan Teks Lama F-09 & Breadcrumb
- Di halaman Laporan Peserta, memastikan teks `Laporan Kinerja Pembimbing Lapangan ASN (F-09, F-10)` dan `Rekapitulasi portofolio bimbingan, jam mentoring kedinasan...` **TIDAK DITAMPILKAN LAGI**.

### Skenario 3: Verifikasi 4 Tile Stat Gentelella
- Halaman menampilkan 4 Tile Stat dalam grid: `Total Mentee`, `Peserta Aktif`, `Perlu Perhatian`, dan `Alumni Selesai` dengan nilai dan badge trend yang sesuai.

### Skenario 4: Verifikasi Popover Ekspor Rekap Cepat
- Klik tombol `Ekspor Rekap` -> Popover muncul dengan pilihan Excel, CSV, dan PDF serta badge `14 Baris` (jumlah baris mentee).
- Klik di luar popover / tombol Batal -> Popover tertutup.
- Memilih opsi radio mengubah status aktif kartu dan label tombol unduh secara dinamis.
- Mengklik tombol unduh memicu request ekspor sesuai format yang dipilih.

---

# 7. RISIKO, EDGE CASES, & MITIGASI

1. **Edge Case: Pembimbing Tanpa Mentee Perlu Perhatian**:
   - Jika `$countPerhatian === 0`, tile tetap menampilkan angka `0` dengan badge `0 Alert` secara normal tanpa error.
2. **Edge Case: Navigasi Mobile**:
   - Popover ekspor harus memiliki styling `adm-export-popover-right` atau max-width `calc(100vw - 32px)` agar tidak terpotong di layar smartphone.
3. **Pencegahan Double-Firing Event Listener**:
   - Skrip `laporan-kinerja-index.js` harus menggunakan pengecekan idempotensi agar tidak mendaftarkan event listener berulang.

---

# 8. KRITERIA SELESAI (DEFINITION OF DONE)

- [x] 3 Kartu KPI lama telah diganti dengan 4 Tile Stat Gentelella (*Total Mentee*, *Peserta Aktif*, *Perlu Perhatian*, *Alumni Selesai*) selaras dengan Gambar 2.
- [x] Menu navigasi sidebar pembimbing berganti nama menjadi `Laporan Peserta` selaras dengan Gambar 3.
- [x] Header topbar halaman laporan menampilkan judul `Laporan Peserta` selaras dengan Gambar 4.
- [x] Teks breadcrumbs, judul F-09, dan subtitle panjang telah dihapus dari view laporan.
- [x] Tombol cetak lama telah diganti dengan Popover `Ekspor Rekap Cepat` (Excel, CSV, PDF) selaras dengan Gambar 5.
- [x] Kode JavaScript popover terisolasi di `resources/js/` dan CSS di `resources/css/` (tanpa inline script/style di Blade).
- [x] Kompilasi Vite (`npm.cmd run build`) berhasil 100%.
- [x] Linter formatting PHP (`php vendor/bin/pint --test`) lulus 100%.
- [x] Seluruh automated test lulus hijau 100%.

