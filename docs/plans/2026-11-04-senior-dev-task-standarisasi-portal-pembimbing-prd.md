# PRD & Senior Developer Task: Standarisasi Tampilan 4 Modul Portal Pembimbing Selaras Tugas & Progres Magang

**Target Pelaksana**: Junior Developer / Gemini 3.8 Flash (AI Model)  
**Kode Dokumen**: `PRD-TASK-2026-11-04-PEMBIMBING-PORTAL-STANDARDIZATION`  
**Status**: Ready for Review & Implementation  
**Tingkat Kerumitan**: Sedang - Terstruktur (Frontend Blade Modernization, Scoped CSS, Modular Vanilla ES Modules, Controller Metric Aggregation, Feature Tests)

---

## 1. Executive Summary & Objective

Tugas ini merupakan instruksi teknis dari **Senior Developer** untuk melakukan **standarisasi visual dan arsitektur frontend** pada 4 modul utama di Portal Pembimbing:
1. **Direktori Peserta Bimbingan** (`route('pembimbing.peserta.index')`)
2. **Review Logbook Harian Peserta** (`route('pembimbing.logbook.index')`)
3. **Evaluasi Rubrik & Penilaian Magang** (`route('pembimbing.penilaian.index')`)
4. **Agenda Mentoring & Notulensi** (`route('pembimbing.agenda.index')`)

### Tujuan Akhir:
Seluruh 4 modul di atas **wajib dibuat selaras 100%** dengan modul **Tugas & Progres Magang** (`resources/views/pembimbing/tugas/index.blade.php`) yang telah menjadi acuan standar emas (*golden standard*).

### Masalah yang Diselesaikan:
- Keempat halaman saat ini masih menggunakan layout lawas dengan breadcrumb generik `Beranda / Dashboard / [Nama Menu]`.
- Belum memiliki **Classroom Hero Banner Khas Sleman MagKom** (banner gradien biru tua dengan metadata instansi dan tombol aksi cepat).
- Tampilan kartu metrik masih flat, kosong di sisi kanan, atau bahkan hanya memiliki 2 kolom / tidak ada kartu statistik sama sekali.
- Halaman-halaman lawas masih dipenuhi oleh tag `<style>` inline, inline attribute `style="..."`, dan inline event handler (`onclick="openModal()"`), yang melanggar aturan baku arsitektur MAGKOM (`AGENTS.md` Rule 3 & Rule 5).

---

## 2. Acuan Visual (Visual Reference Mapping)

Berdasarkan 5 tangkapan layar yang dianalisis:

| Foto | Path Berkas | Halaman Terkait | Status Saat Ini vs Standar Target Baru |
| :--- | :--- | :--- | :--- |
| **Foto 1** | `media_1793784458909_c8bf301f.jpg` | **Tugas & Progres Magang** *(STANDAR ACUAN EMAS)* | • Topbar Title bersih: `Tugas & Progres Magang`<br>• Banner Hero Khas Sleman: Gradien biru, metadata Bidang, Pembimbing, dan tombol `+ Buat Tugas Baru`<br>• Grid 4 Kartu Tile-Stats: Ikon warna, persentase tren, subteks resmi, dan grafik batang mini (*sparkbars*)<br>• Panel data rounded modern dengan table ber-avatar circle dan badge status Gentelella. |
| **Foto 2** | `media_1793784471608_8c6ebc3a.jpg` | **Direktori Peserta Bimbingan** | • *Saat ini*: Breadcrumb lama, header teks flat, tanpa banner Sleman, tanpa 4 kartu metrik statistik tile-stat, banyak inline style.<br>• *Target*: Tambah Topbar Title, Hero Banner Sleman, 4 Tile-Stats (`Total Mentee`, `Peserta Aktif`, `Perlu Perhatian`, `Alumni Selesai`), dan table card modern. |
| **Foto 3** | `media_1793784528966_1ddc1ccc.jpg` | **Review Logbook Harian Peserta** | • *Saat ini*: Breadcrumb lama, filter box lawas, tanpa Hero Banner, TIDAK ADA kartu tile-stats sama sekali.<br>• *Target*: Tambah Topbar Title, Hero Banner Sleman, 4 Tile-Stats (`Total Logbook`, `Perlu Review`, `Disetujui`, `Perlu Revisi`), filter card modern, table card ber-avatar circle. |
| **Foto 4** | `media_1793784761765_97af690d.jpg` | **Agenda Mentoring & Notulensi** | • *Saat ini*: Breadcrumb lama, hanya 2 kartu metrik flat tanpa sparkbars (`Agenda Terjadwal` & `Selesai Terlaksana`), table card biasa.<br>• *Target*: Standarisasi 4 Tile-Stats (`Total Agenda`, `Terjadwal`, `Selesai Terlaksana`, `Metode Tatap Muka / Daring`) dengan sparkbars, Hero Banner Sleman, dan table card modern. |
| **Foto 5** | `media_1793784785085_c14a762a.jpg` | **Evaluasi Rubrik & Penilaian Magang** | • *Saat ini*: Breadcrumb lama, alert box terpisah, TIDAK ADA kartu statistik sama sekali, table flat.<br>• *Target*: Tambah Topbar Title, Hero Banner Sleman (dengan tombol `Ekspor Rekap CSV`), 4 Tile-Stats (`Total Peserta`, `Belum Dinilai`, `Draf Nilai`, `Final Resmi`), info panduan rubrik terintegrasi, dan table card modern. |

---

## 3. Aturan Baku & Larangan Keras (Senior Dev Guardrails)

Junior developer dan AI pelaksana **WAJIB** membaca dan mematuhi aturan ini sebelum menulis satu baris kode pun:

### Dilarang Keras (DON'T):
1. **JANGAN MENULIS CSS DI BLADE**: Dilarang menggunakan tag `<style>` maupun atribut `style="..."` di Blade (`AGENTS.md` Rule 3.1).
2. **JANGAN MENULIS JAVASCRIPT DI BLADE**: Dilarang menggunakan tag `<script>` maupun inline event handler (`onclick`, `onchange`, `onsubmit`) di Blade (`AGENTS.md` Rule 3.1 & 5).
3. **JANGAN MENGUBAH BUSINESS LOGIC ATAU MERUSAK KEAMANAN**: Seluruh route, authorization Gate, proteksi Anti-IDOR (pembimbing hanya boleh mengakses mentee bimbingannya sendiri), enum, dan audit log harus dipertahankan 100%.
4. **JANGAN HARDCODE METRIK**: Nilai pada 4 kartu tile-stats harus bersumber dari kalkulasi agregat di Controller / Service, bukan nilai statis palsu.

### Wajib Dilakukan (DO):
1. **Satu Sumber Kebenaran Styling**: Seluruh stylesheet baru wajib diletakkan di `resources/css/pages/pembimbing/` dan didaftarkan di `vite.config.js`.
2. **Modular ES Modules**: Seluruh script interaktif (modal handler, bulk checkbox, auto-filter) wajib diletakkan di `resources/js/pages/pembimbing/` dan dikompilasi melalui Vite.
3. **Komponen Resmi Gentelella**: Gunakan komponen yang sudah ada: `<x-gentelella.tile-stat>`, `<x-gentelella.x-panel>`, dan `<x-gentelella.badge>`.
4. **Gunakan Prop Resmi**: Pada `<x-gentelella.tile-stat>`, gunakan prop `subText="..."` (Bukan `subtitle`), `trendText="..."`, `trendDirection="up|down"`, dan `:sparkBars="[...]"`.

---

## 4. Blueprint Arsitektur Tampilan (The Golden Standard Blueprint)

Setiap halaman dari 4 modul tersebut wajib mengikuti anatomi struktur berikut:

```blade
@extends('layouts.dashboard')

@section('title', '[Judul Halaman] - Portal MagKom')
@section('topbar_title', '[Judul Halaman Bersih]')
@section('content_area_class', 'gentelella-fluid-canvas')

@push('styles')
    @vite(['resources/css/pages/pembimbing/[nama-modul]-index.css'])
@endpush

@section('content')
<div class="classroom-container">

    {{-- 1. Classroom Hero Banner Khas Sleman MagKom --}}
    <div class="classroom-banner">
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">{{ $pageTitle }}</h1>
                <div class="banner-subtitle">
                    <span>Bidang: <strong>{{ $bidangNama }}</strong></span>
                    <span>&bull;</span>
                    <span>Pembimbing: <strong>{{ $authUser->nama }}</strong></span>
                    <span>&bull;</span>
                    <span><strong>{{ $highlightSummary }}</strong></span>
                </div>
            </div>
            <div>
                {{-- Tombol Aksi Utama --}}
                <a href="..." class="btn-create-task">
                    <svg ...>...</svg>
                    + [Label Tombol Aksi]
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Grid 4 Kartu Tile-Stats Gentelella (Dengan Sparkbars & Trend) --}}
    <div class="tiles-grid">
        <x-gentelella.tile-stat
            title="[Metric 1]"
            value="{{ $metric1Value }}"
            color="teal"
            trendDirection="up"
            trendText="{{ $metric1Trend }}"
            subText="[Subteks Penjelasan 1]"
            :sparkBars="[30, 45, 55, 65, 75, 82, 88, 94, 100]">
            <x-slot:icon><svg ...></svg></x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="[Metric 2]"
            value="{{ $metric2Value }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="{{ $metric2Trend }}"
            subText="[Subteks Penjelasan 2]"
            :sparkBars="[40, 50, 65, 55, 70, 60, 80, 75, 70]">
            <x-slot:icon><svg ...></svg></x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="[Metric 3]"
            value="{{ $metric3Value }}"
            color="yellow"
            trendType="warning"
            trendDirection="down"
            trendText="{{ $metric3Trend }}"
            subText="[Subteks Penjelasan 3]"
            :sparkBars="[65, 75, 85, 70, 85, 80, 65, 50, 40]">
            <x-slot:icon><svg ...></svg></x-slot:icon>
        </x-gentelella.tile-stat>

        <x-gentelella.tile-stat
            title="[Metric 4]"
            value="{{ $metric4Value }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="{{ $metric4Trend }}"
            subText="[Subteks Penjelasan 4]"
            :sparkBars="[20, 35, 50, 60, 70, 75, 85, 92, 98]">
            <x-slot:icon><svg ...></svg></x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 3. X-Panel Data Utama (Filter / Search & Table Card Modern) --}}
    <x-gentelella.x-panel id="[modul]MainPanel">
        <x-slot:title>
            [Judul Panel Data]
        </x-slot:title>

        {{-- Filter Tab Segmented Controls / Form Search --}}
        ...

        {{-- Responsive Modern Table Ber-Avatar Circle --}}
        ...

        {{-- Paginasi Bersih --}}
        ...
    </x-gentelella.x-panel>

</div>

@push('scripts')
    @vite(['resources/js/pages/pembimbing/[nama-modul]-index.js'])
@endpush
@endsection
```

---

## 5. Spesifikasi Rinci 4 Modul yang Dikerjakan

### Modul 1: Direktori Peserta Bimbingan (`pembimbing/peserta`)

#### 1. Controller (`app/Http/Controllers/Pembimbing/PesertaController.php`)
- **Method**: `index(Request $request)`
- **Data Tambahan yang Wajib Dikirim ke View**:
  - `$totalMentee`: Akumulasi total mentee pernah dibimbing (`$countAktif + $countSelesai`).
  - `$persenAktif`: Persentase mentee aktif terhadap total (`round(($countAktif / max(1, $totalMentee)) * 100)`).
  - `$persenPerhatian`: Persentase mentee berstatus alert (`round(($countPerhatian / max(1, $countAktif)) * 100)`).
  - `$persenSelesai`: Persentase alumni selesai (`round(($countSelesai / max(1, $totalMentee)) * 100)`).

#### 2. Konfigurasi 4 Kartu Tile-Stats:
1. **Total Mentee**: `color="teal"`, `trendText="{{ $totalMentee }} Total"`, `subText="Seluruh riwayat penempatan"`, `:sparkBars="[35, 45, 55, 65, 75, 82, 88, 92, 100]"`
2. **Peserta Aktif**: `color="blue"`, `trendType="info"`, `trendText="{{ $persenAktif }}%"`, `subText="Sedang menjalankan magang"`, `:sparkBars="[40, 50, 65, 55, 70, 60, 80, 75, 70]"`
3. **Perlu Perhatian (Alert)**: `color="yellow"`, `trendType="warning"`, `trendDirection="down"`, `trendText="{{ $countPerhatian }} Alert"`, `subText="Tugas overdue / logbook bolong"`, `:sparkBars="[70, 85, 90, 65, 50, 60, 45, 30, 20]"`
4. **Alumni Selesai**: `color="teal"`, `trendType="success"`, `trendText="{{ $persenSelesai }}%"`, `subText="Magang tuntas &amp; lulus"`, `:sparkBars="[15, 25, 40, 50, 65, 75, 85, 95, 100]"`

#### 3. Hero Banner Sleman:
- Judul: `Direktori Peserta Bimbingan`
- Subteks: Bidang, Pembimbing, `{{ $countAktif }} Mentee Aktif Bimbingan`
- Tombol Aksi: `+ Beri Tugas Baru` (menuju `route('pembimbing.tugas.create')`)

#### 4. File Asset Terkait:
- CSS: `resources/css/pages/pembimbing/peserta-index.css`
- JS: `resources/js/pages/pembimbing/peserta-index.js`
- View: `resources/views/pembimbing/peserta/index.blade.php`

---

### Modul 2: Review Logbook Harian Peserta (`pembimbing/logbook`)

#### 1. Controller (`app/Http/Controllers/Pembimbing/LogbookReviewController.php`)
- **Method**: `index(Request $request)`
- **Data Tambahan yang Wajib Dikirim ke View**:
  - `$totalLogbook`: Akumulasi total entri logbook mentee bimbingan.
  - `$countMenunggu`: Jumlah logbook berstatus `terkirim` (menunggu review).
  - `$countDisetujui`: Jumlah logbook berstatus `disetujui`.
  - `$countRevisi`: Jumlah logbook berstatus `perlu_revisi`.
  - Persentase tren masing-masing status terhadap `$totalLogbook`.

#### 2. Konfigurasi 4 Kartu Tile-Stats:
1. **Total Logbook**: `color="teal"`, `trendText="{{ $totalLogbook }} Entri"`, `subText="Akumulasi aktivitas harian"`, `:sparkBars="[30, 45, 55, 60, 70, 78, 85, 92, 100]"`
2. **Perlu Review**: `color="yellow"`, `trendType="warning"`, `trendDirection="down"`, `trendText="{{ $countMenunggu > 0 ? $countMenunggu . ' Menunggu' : 'Tuntas' }}"`, `subText="Menunggu verifikasi pembimbing"`, `:sparkBars="[60, 75, 85, 70, 90, 80, 65, 50, 40]"`
3. **Disetujui**: `color="teal"`, `trendType="success"`, `trendText="{{ $persenDisetujui }}%"`, `subText="Aktivitas valid &amp; diterima"`, `:sparkBars="[25, 40, 50, 60, 70, 80, 85, 92, 98]"`
4. **Perlu Revisi**: `color="red"`, `trendType="danger"`, `trendText="{{ $countRevisi > 0 ? $countRevisi . ' Revisi' : 'Nihil' }}"`, `subText="Catatan perbaikan dikirim"`, `:sparkBars="[40, 30, 45, 20, 35, 25, 15, 10, 5]"`

#### 3. Hero Banner Sleman:
- Judul: `Review Logbook Harian Peserta`
- Subteks: Bidang, Pembimbing, `{{ $countMenunggu }} Logbook Menunggu Review`
- Tombol Aksi: `Review Cepat Logbook` (membuka filter status `terkirim` atau modal review)

#### 4. Modular JS Refactoring:
- Pindahkan fungsi global `toggleSelectAll(this)` dari Blade ke modul event listener terstruktur di `resources/js/pages/pembimbing/logbook-index.js`.
- Delegasi event `change` pada `#selectAllCheckbox` untuk mencentang semua item bulk action tanpa inline script.

#### 5. File Asset Terkait:
- CSS: `resources/css/pages/pembimbing/logbook-index.css`
- JS: `resources/js/pages/pembimbing/logbook-index.js`
- View: `resources/views/pembimbing/logbook/index.blade.php`

---

### Modul 3: Evaluasi Rubrik & Penilaian Magang (`pembimbing/penilaian`)

#### 1. Controller (`app/Http/Controllers/Pembimbing/PenilaianController.php`)
- **Method**: `index(Request $request)`
- **Data Tambahan yang Wajib Dikirim ke View**:
  - `$totalMentee`: Total penempatan peserta di bawah pembimbing.
  - `$countBelumDinilai`: Jumlah penempatan yang `whereDoesntHave('penilaian')`.
  - `$countDraft`: Jumlah penempatan yang `penilaian.status == 'draft'`.
  - `$countFinal`: Jumlah penempatan yang `penilaian.status == 'final'`.
  - Persentase kelengkapan penilaian (`$persenFinal`).

#### 2. Konfigurasi 4 Kartu Tile-Stats:
1. **Total Peserta**: `color="teal"`, `trendText="{{ $totalMentee }} Mentee"`, `subText="Total peserta binaan"`, `:sparkBars="[30, 45, 55, 65, 75, 85, 90, 95, 100]"`
2. **Belum Dinilai**: `color="blue"`, `trendType="info"`, `trendText="{{ $persenBelum }}%"`, `subText="Menunggu evaluasi akhir"`, `:sparkBars="[50, 60, 55, 70, 65, 60, 50, 45, 40]"`
3. **Draf Tersimpan**: `color="yellow"`, `trendType="warning"`, `trendText="{{ $countDraft }} Draf"`, `subText="Belum difinalisasi resmi"`, `:sparkBars="[20, 35, 40, 50, 45, 60, 55, 50, 45]"`
4. **Final Resmi**: `color="teal"`, `trendType="success"`, `trendText="{{ $persenFinal }}%"`, `subText="Nilai terkunci &amp; terverifikasi"`, `:sparkBars="[10, 20, 30, 45, 60, 70, 80, 90, 98]"`

#### 3. Hero Banner Sleman:
- Judul: `Evaluasi Rubrik & Penilaian Magang`
- Subteks: Bidang, Pembimbing, `{{ $countFinal }} dari {{ $totalMentee }} Nilai Final`
- Tombol Aksi: `Ekspor Rekap Bimbingan (CSV)` (menuju `route('pembimbing.penilaian.ekspor-bidang')`)

#### 4. Banner Panduan Rubrik:
- Hapus tag inline `style="..."` pada banner info `Petunjuk Evaluasi Rubrik Resmi Diskominfo Sleman`. Pindahkan styling ke kelas `.rubrik-guide-banner` di `penilaian-index.css`.

#### 5. File Asset Terkait:
- CSS: `resources/css/pages/pembimbing/penilaian-index.css`
- JS: `resources/js/pages/pembimbing/penilaian-index.js`
- View: `resources/views/pembimbing/penilaian/index.blade.php`

---

### Modul 4: Agenda Mentoring & Notulensi (`pembimbing/agenda`)

#### 1. Controller (`app/Http/Controllers/Pembimbing/AgendaBimbinganController.php`)
- **Method**: `index(Request $request)`
- **Data Tambahan yang Wajib Dikirim ke View**:
  - `$totalAgenda`: Total sesi bimbingan (`$totalTerjadwal + $totalSelesai`).
  - `$totalTerjadwal`: Jumlah agenda berstatus `terjadwal`.
  - `$totalSelesai`: Jumlah agenda berstatus `selesai`.
  - `$totalTatapMuka`: Sesi metode `tatap_muka`.
  - `$totalDaring`: Sesi metode `daring`.
  - Persentase tren selesai (`round(($totalSelesai / max(1, $totalAgenda)) * 100)`).

#### 2. Konfigurasi 4 Kartu Tile-Stats (Menggantikan 2 Kartu Flat Lama):
1. **Total Agenda**: `color="teal"`, `trendText="{{ $totalAgenda }} Sesi"`, `subText="Seluruh riwayat bimbingan"`, `:sparkBars="[30, 40, 50, 65, 70, 80, 85, 92, 100]"`
2. **Agenda Terjadwal**: `color="blue"`, `trendType="info"`, `trendText="{{ $totalTerjadwal }} Sesi"`, `subText="Sesi bimbingan mendatang"`, `:sparkBars="[40, 50, 65, 55, 70, 60, 75, 80, 85]"`
3. **Selesai Terlaksana**: `color="teal"`, `trendType="success"`, `trendText="{{ $persenSelesai }}%"`, `subText="Notulensi tercatat rapi"`, `:sparkBars="[20, 35, 45, 60, 70, 75, 85, 90, 98]"`
4. **Metode Sesi**: `color="yellow"`, `trendType="warning"`, `trendText="{{ $totalTatapMuka }} Luring / {{ $totalDaring }} Daring"`, `subText="Distribusi tatap muka &amp; online"`, `:sparkBars="[50, 60, 45, 55, 70, 65, 80, 75, 70]"`

#### 3. Hero Banner Sleman:
- Judul: `Agenda Mentoring & Notulensi`
- Subteks: Bidang, Pembimbing, `{{ $totalTerjadwal }} Agenda Terjadwal`
- Tombol Aksi: `+ Jadwalkan Mentoring Baru` (dengan `id="btnOpenCreateAgenda"` tanpa `onclick="..."` inline)

#### 4. Modular JS Refactoring:
- Pindahkan modal handler `openCreateModal()` dan `openNotulensiModal()` dari inline script Blade ke `resources/js/pages/pembimbing/agenda-index.js`.
- Gunakan data attributes (`data-modal-target="#createModal"`, `data-agenda-id="..."`) dan event delegation aman.

#### 5. File Asset Terkait:
- CSS: `resources/css/pages/pembimbing/agenda-index.css`
- JS: `resources/js/pages/pembimbing/agenda-index.js`
- View: `resources/views/pembimbing/agenda/index.blade.php`

---

## 6. Matriks Perubahan Berkas (File Change Matrix)

| Berkas | Aksi | Ringkasan Modifikasi |
| :--- | :--- | :--- |
| `vite.config.js` | **Modify** | Daftarkan 4 file CSS dan 4 file JS baru portal pembimbing. |
| `app/Http/Controllers/Pembimbing/PesertaController.php` | **Modify** | Siapkan agregasi counter `$totalMentee`, `$persenAktif`, `$persenPerhatian`, `$persenSelesai`. |
| `app/Http/Controllers/Pembimbing/LogbookReviewController.php` | **Modify** | Siapkan agregasi counter `$totalLogbook`, `$countMenunggu`, `$countDisetujui`, `$countRevisi`, persentase. |
| `app/Http/Controllers/Pembimbing/PenilaianController.php` | **Modify** | Siapkan agregasi counter `$totalMentee`, `$countBelumDinilai`, `$countDraft`, `$countFinal`, persentase. |
| `app/Http/Controllers/Pembimbing/AgendaBimbinganController.php` | **Modify** | Siapkan agregasi counter `$totalAgenda`, `$totalTatapMuka`, `$totalDaring`, persentase. |
| `resources/css/pages/pembimbing/peserta-index.css` | **Create** | Scoped CSS direktori peserta (banner, tiles grid, table modern, avatar circle). |
| `resources/js/pages/pembimbing/peserta-index.js` | **Create** | Modular JS search & tab filter handler. |
| `resources/views/pembimbing/peserta/index.blade.php` | **Modify** | Pasang banner Sleman, grid 4 tile-stat, hapus inline styles, tautkan Vite asset. |
| `resources/css/pages/pembimbing/logbook-index.css` | **Create** | Scoped CSS review logbook (banner, tiles grid, table modern, yellow quote block). |
| `resources/js/pages/pembimbing/logbook-index.js` | **Create** | Modular JS bulk review checkbox & modal delegation. |
| `resources/views/pembimbing/logbook/index.blade.php` | **Modify** | Pasang banner Sleman, grid 4 tile-stat, hapus inline script/style, tautkan Vite asset. |
| `resources/css/pages/pembimbing/penilaian-index.css` | **Create** | Scoped CSS penilaian rubrik (banner, tiles grid, guide banner, table modern). |
| `resources/js/pages/pembimbing/penilaian-index.js` | **Create** | Modular JS filter & modal handler. |
| `resources/views/pembimbing/penilaian/index.blade.php` | **Modify** | Pasang banner Sleman, grid 4 tile-stat, panduan rubrik terpadu, tautkan Vite asset. |
| `resources/css/pages/pembimbing/agenda-index.css` | **Create** | Scoped CSS agenda mentoring (banner, tiles grid, table modern, modal styling). |
| `resources/js/pages/pembimbing/agenda-index.js` | **Create** | Modular JS modal create/edit agenda & notulensi. |
| `resources/views/pembimbing/agenda/index.blade.php` | **Modify** | Pasang banner Sleman, ubah 2 kartu jadi grid 4 tile-stat, hapus inline onclick. |
| `tests/Feature/PembimbingPortalStandardizationTest.php` | **Create** | Automated feature test untuk memverifikasi 4 modul merender banner, 4 stats, dan lulus anti-IDOR. |

---

## 7. Langkah-Langkah Implementasi Bertahap (Step-by-Step Execution Plan)

### Fase 1: Pendaftaran Vite & Pembuatan Scoped CSS/JS
- [ ] **Langkah 1.1**: Tambahkan 8 entry point di `vite.config.js`:
  - `resources/css/pages/pembimbing/peserta-index.css`
  - `resources/css/pages/pembimbing/logbook-index.css`
  - `resources/css/pages/pembimbing/penilaian-index.css`
  - `resources/css/pages/pembimbing/agenda-index.css`
  - `resources/js/pages/pembimbing/peserta-index.js`
  - `resources/js/pages/pembimbing/logbook-index.js`
  - `resources/js/pages/pembimbing/penilaian-index.js`
  - `resources/js/pages/pembimbing/agenda-index.js`
- [ ] **Langkah 1.2**: Buat kerangka file CSS dasar untuk keempat modul mengadopsi token desain Sleman Blue (`#01347C`, `#003566`, `.classroom-banner`, `.tiles-grid`, `.stat-spark`).
- [ ] **Langkah 1.3**: Buat kerangka file JavaScript modular dengan `DOMContentLoaded` listener aman tanpa global leakage.

### Fase 2: Backend Controller Metrik Aggregation
- [ ] **Langkah 2.1**: Update `PesertaController@index` untuk menghitung `$totalMentee`, `$persenAktif`, `$persenPerhatian`, `$persenSelesai`.
- [ ] **Langkah 2.2**: Update `LogbookReviewController@index` untuk menghitung `$totalLogbook`, `$countMenunggu`, `$countDisetujui`, `$countRevisi`, dan persentasenya.
- [ ] **Langkah 2.3**: Update `PenilaianController@index` untuk menghitung `$totalMentee`, `$countBelumDinilai`, `$countDraft`, `$countFinal`, dan persentasenya.
- [ ] **Langkah 2.4**: Update `AgendaBimbinganController@index` untuk menghitung `$totalAgenda`, `$totalTatapMuka`, `$totalDaring`, dan persentasenya.

### Fase 3: Refactoring Blade Modul 1 & 2 (Peserta & Logbook)
- [ ] **Langkah 3.1**: Refactor `resources/views/pembimbing/peserta/index.blade.php`:
  - Pasang `@section('topbar_title', 'Direktori Peserta Bimbingan')` & `@section('content_area_class', 'gentelella-fluid-canvas')`.
  - Pasang Classroom Hero Banner Sleman dengan tombol `+ Beri Tugas Baru`.
  - Pasang `.tiles-grid` dengan 4 `<x-gentelella.tile-stat>` lengkap dengan `:sparkBars`.
  - Hapus seluruh inline styles pada filter tab dan tabel daftar mentee.
- [ ] **Langkah 3.2**: Refactor `resources/views/pembimbing/logbook/index.blade.php`:
  - Pasang `@section('topbar_title', 'Review Logbook Harian Peserta')` & `@section('content_area_class', 'gentelella-fluid-canvas')`.
  - Pasang Classroom Hero Banner Sleman.
  - Pasang `.tiles-grid` dengan 4 `<x-gentelella.tile-stat>` (`Total Logbook`, `Perlu Review`, `Disetujui`, `Perlu Revisi`).
  - Hapus inline `onchange="toggleSelectAll(this)"` dan bersihkan inline styles tabel & filter.

### Fase 4: Refactoring Blade Modul 3 & 4 (Penilaian & Agenda)
- [ ] **Langkah 4.1**: Refactor `resources/views/pembimbing/penilaian/index.blade.php`:
  - Pasang `@section('topbar_title', 'Evaluasi Rubrik & Penilaian Magang')` & `@section('content_area_class', 'gentelella-fluid-canvas')`.
  - Pasang Classroom Hero Banner Sleman dengan tombol `Ekspor Rekap Bimbingan (CSV)`.
  - Pasang `.tiles-grid` dengan 4 `<x-gentelella.tile-stat>` (`Total Peserta`, `Belum Dinilai`, `Draf Nilai`, `Final Resmi`).
  - Rapikan styling petunjuk rubrik ke dalam kelas CSS `.rubrik-guide-banner`.
- [ ] **Langkah 4.2**: Refactor `resources/views/pembimbing/agenda/index.blade.php`:
  - Pasang `@section('topbar_title', 'Agenda Mentoring & Notulensi')` & `@section('content_area_class', 'gentelella-fluid-canvas')`.
  - Pasang Classroom Hero Banner Sleman dengan tombol `+ Jadwalkan Mentoring Baru`.
  - Ubah 2 kartu lama menjadi `.tiles-grid` dengan 4 `<x-gentelella.tile-stat>` lengkap dengan sparkbars.
  - Hapus inline `onclick="openCreateModal()"` dan ganti dengan event listener di JS.

### Fase 5: Quality Gate & Automated Testing
- [ ] **Langkah 5.1**: Buat automated feature test `tests/Feature/PembimbingPortalStandardizationTest.php` untuk memvalidasi:
  - Keempat rute GET mengembalikan status 200 OK.
  - Teks banner Sleman dan 4 kartu tile-stat ter-render dengan benar.
  - Proteksi anti-IDOR tetap aktif (pembimbing dilarang melihat data pembimbing lain).
- [ ] **Langkah 5.2**: Jalankan build Vite:
  ```bash
  npm run build
  ```
- [ ] **Langkah 5.3**: Jalankan seluruh test modul pembimbing:
  ```bash
  php artisan test --filter=Pembimbing
  ```
- [ ] **Langkah 5.4**: Jalankan Pint code formatting check:
  ```bash
  ./vendor/bin/pint --test
  ```
- [ ] **Langkah 5.5**: Jalankan PHPStan static analysis:
  ```bash
  ./vendor/bin/phpstan analyse --memory-limit=1G
  ```
- [ ] **Langkah 5.6**: Update Knowledge Graph:
  ```bash
  python -m graphify update .
  ```

---

## 8. Definition of Done (DoD)

Pekerjaan dinyatakan selesai jika dan hanya jika:
1. Keempat halaman (`pembimbing/peserta`, `pembimbing/logbook`, `pembimbing/penilaian`, `pembimbing/agenda`) memiliki bahasa desain yang seragam dengan `pembimbing/tugas`:
   - Topbar Title bersih (tanpa breadcrumb generik).
   - Classroom Hero Banner Khas Sleman dengan gradien biru tua dan tombol aksi.
   - 4 Kartu Tile-Stats Gentelella lengkap dengan angka, persentase tren, subteks resmi, dan grafik batang mini (*sparkbars*).
   - Tabel responsif modern ber-avatar circle dengan badge status Gentelella.
2. Tidak ada satu pun tag `<style>`, atribut inline `style="..."`, tag `<script>`, atau inline event handler (`onclick="..."`) di dalam file Blade yang disentuh.
3. Seluruh asset CSS & JS berada di `resources/` dan terdaftar di `vite.config.js`.
4. `npm run build` sukses tanpa warning / error.
5. Seluruh feature test PHPUnit berhasil (100% PASS).
6. Pint dan PHPStan berhasil tanpa error (`[OK] No errors`).
7. Git working tree bersih dan terdokumentasi rapi.

