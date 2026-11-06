# Technical Implementation Plan: Redesign UI Detail Penempatan Peserta (12-Column Grid System)

**Document**: `docs/plans/2026-11-05-rencana-redesign-ui-detail-penempatan-peserta.md`  
**Author**: Senior Frontend Developer & UI Architect  
**Target Environment**: MAGKOM (Laravel 10, Blade, Scoped CSS in Vite, Vanilla JS)  
**Status**: Selesai Terverifikasi (100% Tests Pass & Build OK)  
**Reference**: Feedback Desain Pengguna, Aturan Tata Kelola `AGENTS.md`  

---

## 1. Executive Summary & Problem Statement

Halaman **Detail Penempatan Peserta** (`resources/views/admin/penempatan/show.blade.php`) merupakan cockpit operasional penting bagi Administrator Dinas Kominfo untuk memantau status alokasi, memvalidasi surat keputusan kedinasan, mengevaluasi progres mentee, mengelola pembimbing lapangan, dan mengunduh arsip penerimaan resmi.

### 1.1 Masalah Utama UI/UX Saat Ini
1. **Visual Clutter & Nested Card Hell**:
   - Terdapat kartu di dalam kartu (*nested containers*): Komponen `x-gentelella.x-panel` membungkus sub-kartu ber-border (`.sk-official-banner`, `.penempatan-fields-grid`, `.field-tile-card`, `.internship-timeline-box`), menimbulkan lapisan border bertumpuk, kontras border ganda, dan kesan sesak (*cramped*).
2. **Redundansi Informasi (Data Duplication)**:
   - **Jurusan**: Ditampilkan berulang 3 kali (pada chips hero atas, kartu grid penempatan, dan relasi kuota).
   - **Jalur Pendaftaran**: Ditampilkan ganda (pada badge status hero atas dan kartu bidang penempatan).
   - **Durasi & Rentang Waktu**: Angka "13 Minggu" dan tanggal rentang muncul di Stat Card #4, grid penempatan, dan kotak timeline sekaligus.
3. **Hirarki Tombol Aksi Terpecah (Scattered Button Hierarchy)**:
   - Tombol tersebar di 3 lokasi berbeda:
     - Header atas: *"Kembali ke Daftar"* & *"Unduh Surat Diterima PDF"*.
     - Hero card tengah: *"Pratinjau Surat"*, *"Pindah Pembimbing"*, *"Batalkan Penempatan"*.
     - Panel dokumen bawah: tombol *"Lihat"* dan *"Unduh"* tambahan.
   - Tidak ada pengelompokan aksi terpadu (*Primary Action vs Secondary/Danger Actions*).
4. **Ketiadaan Sistem Grid 12-Kolom yang Tegas**:
   - Layout bawah belum mengadopsi rasio proporsional desktop enterprise yang baku (**8-Kolom Konten Utama : 4-Kolom Sidebar Pendukung**).

---

## 2. Technical Stack Alignment & Guardrails (AGENTS.md)

Sesuai aturan permanen repository [`AGENTS.md`](file:///d:/laragon/www/MAGKOM/.agents/rules/AGENTS.md):
- **Core Baseline**: PHP 8.3, Laravel 10.x, Blade Templates, MySQL.
- **Frontend Stack**: Vanilla CSS (ES Modules via Vite) dan Vanilla JavaScript.
- **Batasan**: Dilarang menginstal Tailwind CSS via npm, React, Vue, atau library eksternal baru yang mengubah bundler.
- **Arsitektur Solusi**: Kita mengimplementasikan **12-Column Grid System & Utility Design Tokens** bergaya Tailwind secara native di dalam stylesheet modular scoped [`resources/css/pages/admin/penempatan-detail.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/penempatan-detail.css) yang terdaftar di Vite pipeline (`resources/css/app.css`). Hal ini menghasilkan estetika modern sekelas Tailwind tanpa menambah bobot dependency baru.

---

## 3. Arsitektur Komponen Baru (Component Breakdowns)

Struktur antarmuka dipecah menjadi 8 modul semantik yang saling melengkapi dan bebas nesting berlebih:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [1] HEADER BAR: Breadcrumbs + Judul Halaman + Unified Action Button Group              │
│     [ Kembali ]                    [ Pratinjau ] [ Aksi Lainnya ▼ ] [ Unduh PDF (Pri) ] │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [2] COMPACT PROFILE BANNER: 1-Line User Card                                           │
│     [Avatar]  Naufal Pratama   [Status: Aktif] [Jalur: Reguler]                        │
│               NIM: 0054321987  •  SMK N 2 Depok Sleman  •  Teknik Komputer & Jaringan  │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [3] MINIMAL METRICS SUMMARY: 4 Modern Metric Tiles                                     │
│     ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐ ┌────────────────────┐ │
│     │ Tugas Selesai   │ │ Total Logbook   │ │ Nilai Akhir     │ │ Durasi Magang      │ │
│     │ 1 / 3 (33%)     │ │ 3 Minggu Terisi │ │ Belum Ada       │ │ 13 Minggu          │ │
│     └─────────────────┘ └─────────────────┘ └─────────────────┘ └────────────────────┘ │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [4] MAIN CONTENT 12-COLUMN GRID SYSTEM (8-col Left : 4-col Right)                      │
│ ┌──────────────────────────────────────────────┐ ┌───────────────────────────────────┐ │
│ │ LEFT COLUMN (8-COL / ~66.67%)                │ │ RIGHT COLUMN (4-COL / ~33.33%)    │ │
│ │                                              │ │                                   │ │
│ │ A. Official SK Banner                        │ │ D. Field Supervisor Card          │ │
│ │    No. SK: 500.12/0001/DISKOMINFO/2026       │ │    - Monogram Avatar              │ │
│ │                                              │ │    - Nama & Email Pembimbing      │ │
│ │ B. Placement Details Grid (Clean 2x2)        │ │    - NIP                          │ │
│ │    - Bidang: Aplikasi Informatika (EGOV)     │ │    - Beban Mentee: 0/5 Aktif      │ │
│ │    - Tanggal Mulai: 02 Okt 2026              │ │                                   │ │
│ │    - Tanggal Selesai: 31 Des 2026            │ │ E. Document List & Verification   │ │
│ │    - Status Akun: Aktif                      │ │    - SK Diterima Magang (PDF)     │ │
│ │                                              │ │    - Berkas Pendaftaran Siswa     │ │
│ │ C. Internship Progress Timeline              │ │      (Surat Rekomendasi, dll)     │ │
│ │    Mulai ────●──── Berjalan (35%) ──── Selesai│ │                                   │ │
│ └──────────────────────────────────────────────┘ └───────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

### Rincian Sub-Komponen

| ID | Nama Komponen | Tanggung Jawab & Fitur Utama |
| :-: | :--- | :--- |
| **C-01** | `penempatan-header-bar` | Navigasi breadcrumbs, judul halaman, dan **Unified Action Toolbar** di pojok kanan (Primary: Tombol biru Sleman "Unduh PDF", Secondary: "Pratinjau Surat", Dropdown Aksi: "Pindah Pembimbing" & "Batalkan Penempatan"). |
| **C-02** | `penempatan-profile-banner` | Banner profil ringkas 1-baris (*compact one-line*): Avatar bulat/monogram, nama peserta tebal, status dinamis (Aktif/Selesai/Batal), badge jalur, dan meta chips tipis (NIM, Institusi, Jurusan). |
| **C-03** | `penempatan-metrics-grid` | 4 kartu metrik presisi tanpa chart hardcode semu: Angka tugas tuntas + mini bar, total logbook, nilai akhir + predikat, durasi magang. |
| **C-04** | `penempatan-sk-banner` | Banner biru navy kontur resmi Sleman untuk nomor SK Keputusan Kedinasan bergaya *monospace font*. |
| **C-05** | `penempatan-details-card` | Kartu putih datar (*flat card*) tanpa nested border: Menampilkan 4 atribut utama non-redundan (Bidang Kedinasan, Tanggal Mulai, Tanggal Selesai, Status Akun Sistem). |
| **C-06** | `penempatan-timeline-card` | Visual milestone linier: Titik Mulai, Titik Hari Ini (progress bar dinamis sesuai persentase hari berjalan), dan Titik Selesai Magang. |
| **C-07** | `penempatan-supervisor-card` | Kartu profil pembimbing teknis lapangan: Avatar monogram, Nama, Email dinas, NIP, dan kapasitas bimbingan aktif. |
| **C-08** | `penempatan-document-list` | Daftar dokumen resmi magang: Tile PDF Surat Diterima dengan tombol aksi langsung + daftar berkas pendaftaran terverifikasi. |

---

## 4. State & Data Mapping (Eliminasi Redundansi Data)

### 4.1 Matriks Eliminasi Redundansi Data

| Data / Elemen | Lokasi Desain Lama | Lokasi Baru (Single Source of Truth) | Alasan Perbaikan |
| :--- | :--- | :--- | :--- |
| **Jurusan Peserta** | 1. Hero Chips<br>2. Placement Grid<br>3. Relasi Kuota | **Hanya di Profile Banner (C-02)** | Menghilangkan perulangan teks panjang di kolom kiri. |
| **Jalur Pendaftaran** | 1. Hero Title Row<br>2. Placement Grid Card | **Hanya di Profile Banner (C-02)** | Cukup satu badge kontras di samping status aktif. |
| **Durasi Magang** | 1. Metric Stat #4<br>2. Placement Grid<br>3. Timeline Box | **Metric Stat #4 (C-03)** & Sumbu Timeline (C-06) | Menghindari pengulangan label "13 Minggu" di 3 tempat berbeda. |
| **Tombol Aksi** | Tersebar di Header, Hero, dan Card Dokumen | **Terkonsentrasi di Header Toolbar (C-01)** | Hirarki tombol terstandarisasi (Primary vs Secondary vs Danger). |

### 4.2 Pemetaan Variabel Controller & Blade

```php
// Backend Source: app/Http/Controllers/Admin/PenempatanController.php@show
$penempatan          // Model: Penempatan (id, no_surat, status, tgl_mulai, tgl_selesai, total_minggu)
$peserta             // Model: User (nama, email, role, status_aktif)
$profil              // Model: PesertaProfil (nim_nisn, institusi, foto_url, jurusan_id)
$pembimbing          // Model: User (nama, email, nip, mentee_aktif_count)
$pembimbingList      // Collection: Daftar pembimbing aktif untuk dropdown modal
$tugasCount          // int: Total tugas magang diberikan
$tugasSelesaiCount   // int: Total tugas berstatus selesai
$logbookCount        // int: Total logbook mingguan
$nilaiAkhir          // ?float: Nilai akhir evaluasi (atau null)
$timelinePersen      // int: Persentase hari magang berjalan (0 - 100%)
$noSuratResmi        // string: Nomor SK resmi format dinas
```

---

## 5. Rencana Hirarki Tombol Aksi (Button Hierarchy)

```
[ Page Actions Toolbar ]
│
├── Primary Action:
│   └── [ Unduh Surat Diterima PDF ] ──> Background #0641CC, Text White, Icon Download, Shadow
│
├── Secondary Actions:
│   └── [ Pratinjau Surat ]          ──> Background White, Border #CBD5E1, Text #334155, Icon Eye
│
└── Contextual Actions Dropdown (Jika Status Aktif):
    └── [ Opsi Penempatan ▼ ]
        ├── [ Pindah Pembimbing ]     ──> Icon User-Switch, trigger modal alihkan
        └── [ Batalkan Penempatan ]   ──> Icon User-X, Text #DC2626 (Destructive Danger), trigger modal batal
```

*Keuntungan*: Header halaman menjadi rapi, kartu profil peserta bersih dari tombol-tombol yang memadati area teks, dan tombol destruktif tersimpan aman dari salah klik (*accidental click*).

---

## 6. Rencana Implementasi Bertahap (Step-by-Step Task Checklist)

### Fase 1: CSS Grid 12-Kolom & Fondasi Layout (2.0 Jam)
- [x] Buat container 12-kolom `.penempatan-grid-12` pada `resources/css/pages/admin/penempatan-detail.css`.
- [x] Atur kolom utama `.col-span-8` (66.67% width) dan sidebar `.col-span-4` (33.33% width).
- [x] Terapkan CSS Gap konsisten (`gap: 1.5rem` / `24px`) dengan baseline layout MAGKOM.
- [x] Definisikan variabel warna CSS (Sleman Navy `#0641CC`, Slate `#0F172A`, Border `#E2E8F0`).

### Fase 2: Header & Action Button Hierarchy (1.5 Jam)
- [x] Rapikan navigasi breadcrumbs dan judul halaman di `@section('breadcrumbs')` dan `@section('page_title')`.
- [x] Bangun komponen toolbar aksi terpadu di `@section('page_actions')`.
- [x] Integrasikan tombol utama **Unduh PDF** dan dropdown/secondary actions **Pratinjau**, **Pindah Pembimbing**, dan **Batalkan Penempatan**.
- [x] Pastikan trigger JavaScript modal (`openPindahModal()`, `openBatalModal()`) tetap terhubung 100%.

### Fase 3: Compact Profile Banner (1.0 Jam)
- [x] Desain ulang kartu profil menjadi format horizontal padat (*compact 1-line user card*).
- [x] Tampilkan avatar peserta (foto asli atau monogram 2-huruf tajam).
- [x] Satukan Nama Lengkap, Status Badge (dengan dot indikator warna), dan Badge Jalur (Reguler / Khusus).
- [x] Susun meta chips tipis sebaris: `NIM/NISN`, `Institusi Asal`, dan `Jurusan Terdaftar`.

### Fase 4: Minimal Metrics Summary (1.0 Jam)
- [x] Restrukturisasi 4 stat cards agar lebih ramping dan berfokus pada data esensial.
- [x] Kartu Tugas: Rasio selesai + mini progress bar.
- [x] Kartu Logbook: Akumulasi minggu terisi dengan ikon centang checklist.
- [x] Kartu Nilai Akhir: Angka 2 desimal + predikat resmi.
- [x] Kartu Durasi: Total minggu tanpa teks tanggal yang tumpang-tindih.

### Fase 5: Left Column (8-Col) Content Assembly (1.5 Jam)
- [x] **Official SK Banner**: Desain banner biru navy elegan dengan nomor surat monospace dan badge stempel dinas resmi.
- [x] **Clean Placement Details**: Susun kartu 2x2 flat tanpa nested panel untuk Bidang, Tanggal Mulai, Tanggal Selesai, dan Status Akun.
- [x] **Internship Progress Timeline**: Rancang ulang track timeline linier dengan indikator progress bar dan 3 titik milestone (Mulai, Sedang Berjalan %, Selesai).

### Fase 6: Right Column (4-Col) Sidebar Assembly (1.0 Jam)
- [x] **Field Supervisor Card**: Tata kartu profil pembimbing lapangan dengan avatar monogram, nama bergelar, email, NIP, serta meteran beban mentee aktif.
- [x] **Document List Card**: Tampilkan tile Surat Keterangan Diterima Magang (PDF) dan daftar berkas unggahan terverifikasi.

### Fase 7: Responsivitas & Dark Mode (1.0 Jam)
- [x] Terapkan responsive breakpoint:
  - Layar Desktop (`> 1080px`): Grid 12-kolom (8-col : 4-col).
  - Layar Tablet / Ponsel (`<= 1080px`): Stack 1-kolom penuh (100% width).
- [x] Terapkan styling `[data-theme="dark"]` menyeluruh untuk semua background, border, dan typography.

### Fase 8: Quality Gate & Pengujian (1.0 Jam)
- [x] Jalankan kompilasi aset: `npm run build`.
- [x] Jalankan automated testing PHPUnit:
  - `php artisan test tests/Feature/AdminPenempatanLifecycleTest.php`
  - `php artisan test tests/Feature/DeltaDashboardEnhancementTest.php`
- [x] Jalankan code formatting check: `./vendor/bin/pint --test`.
- [x] Verifikasi PHP linting: `php -l resources/views/admin/penempatan/show.blade.php`.

---

## 7. Estimasi Waktu Pengerjaan (Work Estimation)

| Fase Pengerjaan | Scope Pekerjaan | Estimasi Waktu |
| :--- | :--- | :---: |
| **Fase 1** | CSS Grid 12-Kolom & Utility Foundation | 2.0 jam |
| **Fase 2** | Header & Unified Action Toolbar | 1.5 jam |
| **Fase 3** | Compact 1-Line Profile Banner | 1.0 jam |
| **Fase 4** | Minimal Metrics Summary (4 Cards) | 1.0 jam |
| **Fase 5** | Left Column 8-Col Assembly (SK, Details, Timeline) | 1.5 jam |
| **Fase 6** | Right Column 4-Col Assembly (Supervisor, Documents) | 1.0 jam |
| **Fase 7** | Responsive Breakpoints & Dark Mode Tokens | 1.0 jam |
| **Fase 8** | QA, Accessibility, Modals, Tests & Pint | 1.0 jam |
| **TOTAL ESTIMASI** | **End-to-End Delivery** | **10.0 jam** |

---

## 8. Permohonan Persetujuan Perubahan Markup Blade (Sesuai AGENTS.md Bagian 3.3)

Sesuai aturan permanen **AGENTS.md Section 3.3**:
1. **Mengapa Perubahan Markup Diperlukan**: Mengubah struktur antarmuka dari panel Gentelella nested lama menjadi sistem 12-kolom modern (8-col : 4-col), menyederhanakan Profile Banner menjadi compact 1-line, dan memindahkan tombol aksi ke Header Toolbar memerlukan perubahan elemen kontainer HTML semantik di Blade.
2. **File Target**: [`resources/views/admin/penempatan/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/penempatan/show.blade.php).
3. **Jaminan Stabilitas**:
   - Tidak ada CSS atau JavaScript baru yang disisipkan di dalam Blade.
   - Semua rute controller (`admin.penempatan.surat-diterima`, `admin.penempatan.pindah-pembimbing`, `admin.penempatan.batalkan`) tetap 100% utuh.
   - Seluruh ID modal (`#modal-pindah-pembimbing`, `#modal-batalkan-penempatan`) dan interaksi form tetap terjaga.
   - Semua automated feature tests dijamin tetap **100% PASS**.

