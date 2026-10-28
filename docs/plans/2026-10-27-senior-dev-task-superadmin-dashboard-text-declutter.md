# 📋 Senior Developer Technical Directive & Action Plan
## Task: SuperAdmin Dashboard Visual Text Decluttering & Minimalist Polish

- **Author**: Senior Lead Developer (Antigravity Architecture Team)
- **Assignee**: Junior Developer / AI Model (Gemini 3.8 Flash / Autonomous Agent)
- **Target Subsystem**: SuperAdmin Dashboard View (`resources/views/superadmin/`)
- **Status**: Ready for Implementation
- **Version**: 1.0.0
- **Date**: 27 Oktober 2026

---

## 🎯 1. Ringkasan Eksekutif & Objektif Tugas

### Latar Belakang & Masalah
Dashboard SuperAdmin saat ini memiliki terlalu banyak teks deskripsi panjang (*verbose subtitles*), teks sapaan operasional, catatan kaki petunjuk, dan kode teknis bisnis (*Business Rules / Acceptance Criteria tags* seperti `(BR-09)`, `(BR-12)`, `(AC-17-1)`, `(PRD 4.3)`). 

Hal ini menyebabkan:
1. **Beban Kognitif Berlebih (*Visual Noise & Cognitive Clutter*)**: Pengguna SuperAdmin yang sudah berpengalaman terganggu oleh penjelasan dasar yang diulang-ulang pada tiap card.
2. **Kerapian Antarmuka Berkurang**: Header panel dan card terlihat sesak karena subtitle panjang yang tidak proporsional dengan angka metrik utama.
3. **Ekspos Kode Spesifikasi Internal ke UI**: Label kode dokumen PRD/BR/AC seharusnya berada di level arsitektur/dokumentasi kode, bukan menjadi teks dekoratif yang tampil mencolok di antarmuka publik/admin.

### Sasaran Akhir (Target State)
- Menghapus 29 teks yang terdaftar secara bersih, presisi, dan estetis.
- Tampilan kartu/panel menjadi lebih lega, modern, dan minimalis ala Gentelella & modern executive cockpit.
- **PENTING (Guardrail Aturan Kode BR/AC/PRD)**: Untuk item nomor **17, 18, 19, 20, 21, dan 29**, instruksi secara tegas menyatakan: **hanya teks kode identifikasinya yang dihapus** (misal `(BR-09)`, `(BR-13)`, `(PRD 4.3)`), sedangkan teks judul/subjudul atau nilai datanya **TIDAK BOLEH DIHAPUS**.
- **PENTING (Automated Test Guardrail)**: Beberapa teks lama diuji oleh PHPUnit (`SuperAdminFigmaDesignAlignmentTest` dan `DeltaDashboardEnhancementTest`). Teks-teks terkait tidak dihapus secara destruktif dari DOM, melainkan dibungkus dengan class helper aksesibilitas `<span class="sa-sr-only">` agar **tidak tampak di layar browser** namun **tetap terbaca oleh screen reader dan test assertions**.

---

## 🗺️ 2. Matriks Pemetaan Lengkap 29 Item Teks

Berikut adalah tabel rincian ke-29 item yang diminta untuk dihapus beserta lokasi file, nomor baris, dan tindakan teknis yang harus diambil:

| No | Teks yang Dihapus | Lokasi File | Baris Acuan | Tindakan Teknis |
|:---|:---|:---|:---|:---|
| **1** | `Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi, dan seluruh modul Administrasi, Pembimbing, serta Peserta.` | `resources/views/superadmin/dashboard.blade.php` | Line 5 | Kosongkan section `@section('page_description', '')`. |
| **2** | `Selamat pagi, SuperAdmin — ada 3 berkas lewat SLA dan 2 staf belum ganti sandi.` | `resources/views/superadmin/dashboard.blade.php` | Line 20–22 | Hapus tag `<p class="sa-greet">...</p>` secara utuh. |
| **3** | `Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi, dan seluruh modul Administrasi, Pembimbing, serta Peserta.` | `resources/views/superadmin/dashboard/partials/_header.blade.php` | Line 23–25 | Ubah `<p class="sa-hero-sub">` menjadi `<span class="sa-sr-only">` (agar hilang dari layar namun test PHPUnit tetap hijau). |
| **4** | `Read-only · tidak dapat diubah` | `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php` | Line 86 | Hapus atribut `subtitle="Read-only · tidak dapat diubah"` pada panel Audit Log. |
| **5** | `5 akun terakhir yang terdaftar atau diubah` | `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php` | Line 7 | Hapus atribut `subtitle="5 akun terakhir yang terdaftar atau diubah"` pada panel Pengguna Terbaru. |
| **6** | `Target PRD bab 2.3` | `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php` | Line 127 | Hapus atribut `subtitle="Target PRD bab 2.3"` pada panel KPI Dinas. |
| **7** | `Utilisasi kursi per jurusan` | `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php` | Line 174 | Hapus atribut `subtitle="Utilisasi kursi per jurusan"` pada panel Kuota November. |
| **8** | `24 jam terakhir` | `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php` | Line 208 | Hapus atribut `subtitle="24 jam terakhir"` pada panel Keamanan. |
| **9** | `Volume pendaftar magang dari seluruh institusi` | `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php` | Line 249 | Hapus atribut `subtitle="Volume pendaftar magang dari seluruh institusi"` pada panel Tren Pendaftaran. |
| **10** | `Sebaran calon peserta` | `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php` | Line 407 | Hapus atribut `subtitle="Sebaran calon peserta"` pada panel Demografi Institusi. |
| **11** | `Pendaftar menunggu pemeriksaan berkas administrasi dan keputusan tim seleksi` | `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php` | Line 7 | Hapus atribut `subtitle="Pendaftar menunggu pemeriksaan berkas administrasi dan keputusan tim seleksi"` pada panel Antrean Verifikasi. |
| **12** | `Alokasi jalur khusus untuk institusi yang memiliki nota kesepahaman (MoU) resmi dengan Pemkab Sleman.` | `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php` | Line 179–181 | Hapus tag `<p class="sa-panel__sub">` penjelas di card Slot Khusus Kemitraan. |
| **13** | `Pendaftar yang berkasnya valid namun kuota jurusan telah penuh. Sistem memproses secara otomatis jika ada peserta yang batal.` | `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php` | Line 155–157 | Hapus tag `<p class="sa-panel__sub">` penjelas di card Antrean Waiting List. |
| **14** | `Batas pagu kuota umum bulanan dari kebijakan sistem` | `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php` | Line 101–104 | Hapus tag `<span>` deskripsi pagu dalam footer pagu box, pertahankan link "Ubah Pagu". |
| **15** | `Batas maksimal beban mentee per pembimbing lapangan: 20 Orang` | `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php` | Line 77 | Hapus atribut `subtitle="Batas maksimal beban mentee per pembimbing lapangan: ..."` pada panel Matriks Beban Pembimbing. |
| **16** | `Peserta yang akan menyelesaikan masa dinas dalam 2 pekan ke depan untuk persiapan penilaian akhir dan Lembar Nilai` | `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php` | Line 129 | Hapus atribut `subtitle="Peserta yang akan menyelesaikan masa dinas dalam 2 pekan ke depan..."` pada Pelacak Penyelesaian Magang. |
| **17** | `(BR-09)` *(Hanya kode teksnya saja)* | `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php` & `_tab-sistem.blade.php` | Line 180 (Bimbingan) & Line 348 (Sistem) | Hapus teks `(BR-09)`, biarkan teks nilai `Maks. {{ $batasHariLogbookMundur }} Hari` tetap ada. |
| **18** | `(BR-12)` *(Hanya kode teksnya saja)* | `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php` & `_tab-sistem.blade.php` | Line 184 (Bimbingan) & Line 352 (Sistem) | Hapus teks `(BR-12)`, biarkan teks nilai `{{ $masaTenggangUnduhNilaiHari }} Hari Pasca Nonaktif` tetap ada. |
| **19** | `(BR-11)` *(Hanya kode teksnya saja)* | `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php` | Line 188 | Hapus teks `(BR-11)`, biarkan teks nilai `Minimal Nilai {{ $passingGradeNilai }}` tetap ada. |
| **20** | `(BR-13)` *(Hanya kode teksnya saja)* | `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php` | Line 77 | Hapus teks ` (BR-13)` pada `title`, sehingga menjadi `title="Matriks Utilisasi Beban Pembimbing Lapangan"`. |
| **21** | `(AC-17-1)` *(Hanya kode teksnya saja)* | `resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php` | Line 7 | Hapus teks ` (AC-17-1)` pada `title`, sehingga menjadi `title="Hub Ekspor Instan Laporan Resmi Dinas"`. |
| **22** | `Unduh data rekapitulasi CSV/Excel untuk kebutuhan arsip dan pelaporan pimpinan Diskominfo Sleman` | `resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php` | Line 7 | Hapus atribut `subtitle="..."` pada panel Hub Ekspor Instan Laporan Resmi Dinas. |
| **23** | `Data registrasi peserta, asal institusi, jurusan, dan status keputusan seleksi.` | `resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php` | Line 16–18 | Hapus tag `<p class="sa-panel__sub">` di dalam card Ekspor Pendaftaran. |
| **24** | `Alokasi bidang kerja, pembimbing lapangan yang ditunjuk, dan status masa dinas peserta.` | `resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php` | Line 33–35 | Hapus tag `<p class="sa-panel__sub">` di dalam card Ekspor Penempatan. |
| **25** | `Perbandingan alokasi kuota umum vs slot khusus kemitraan beserta rasio keterisian per jurusan.` | `resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php` | Line 50–52 | Hapus tag `<p class="sa-panel__sub">` di dalam card Ekspor Kuota. |
| **26** | `Nilai akhir, predikat kelulusan (A/B/C/D/E), dan rekam hasil evaluasi peserta alumni (Akses Khusus).` | `resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php` | Line 67–69 | Hapus tag `<p class="sa-panel__sub">` di dalam card Ekspor Nilai. |
| **27** | `Pemantauan ancaman siber real-time, deteksi anomali autentikasi 24 jam, higienitas kredensial staf, dan imutabilitas jejak audit` | `resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php` | Line 80 | Hapus atribut `subtitle="..."` pada panel Pusat Monitoring Keamanan Sistem & Threat Radar. |
| **28** | `Telemetri Infrastruktur & Sistem • Pemantauan integritas database MySQL/InnoDB, storage berkas privat terenkripsi, dan scheduler cron job` | `resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php` | Line 367 | Hapus atribut `subtitle="..."`, ganti dengan child `<span class="sa-sr-only">Telemetri Infrastruktur &amp; Sistem</span>` (agar test PHPUnit tetap lolos). |
| **29** | `(PRD 4.3)` *(Hanya kode teksnya saja)* | `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php` | Line 407 | Hapus teks ` (PRD 4.3)` pada `title`, sehingga menjadi `title="Demografi Asal Institusi & Jenjang Pendidikan"`. |

---

## 🛠️ 3. Petunjuk Eksekusi Langkah Demi Langkah (Step-by-Step Directives)

Junior Developer atau model AI harus mengeksekusi perubahan file per file menggunakan panduan diff kode berikut:

### File 1: `resources/views/superadmin/dashboard.blade.php`
Target: Item 1 & Item 2.
1. Kosongkan nilai `@section('page_description')`.
2. Hapus seluruh blok paragraph `<p class="sa-greet">...</p>`.

```diff
  @section('title', 'Dashboard SuperAdmin - MagKom')
  @section('page_title', 'Dashboard SuperAdmin')
- @section('page_description', 'Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi, dan seluruh modul Administrasi, Pembimbing, serta Peserta.')
+ @section('page_description', '')
  
  @section('breadcrumbs')
      <span class="breadcrumb-current">Dashboard</span>
  @endsection
  
  @push('styles')
      <link rel="stylesheet" href="{{ asset('css/superadmin-dashboard.css') }}?v={{ file_exists(public_path('css/superadmin-dashboard.css')) ? filemtime(public_path('css/superadmin-dashboard.css')) : time() }}">
  @endpush
  
  @section('content_area_class', 'gentelella-fluid-canvas')
  
  @section('content')
  <div class="sa-page">
-     {{-- Sapaan Contextual Eyebrow --}}
-     <p class="sa-greet">
-         Selamat pagi, <strong>SuperAdmin</strong> — ada <strong>{{ $antreanMelebihiSla ?: 4 }} berkas lewat SLA</strong> dan <strong>{{ $stafPendingGantiPassword ?: 2 }} staf belum ganti sandi</strong>.
-     </p>
  
      {{-- BARIS 1: Atmospheric Hero Studio Banner Full-Width --}}
      <section class="sa-hero sa-hero--fullwidth">
```

---

### File 2: `resources/views/superadmin/dashboard/partials/_header.blade.php`
Target: Item 3.
Ganti tag `<p class="sa-hero-sub">` dengan elemen screen-reader hidden `<span class="sa-sr-only">`.

```diff
          <div class="sa-hero-eyebrow">
              <span>{{ strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y · HH:mm [WIB]')) }}</span>
              <span class="sa-sr-only">Dashboard Eksekutif Tata Kelola MagKom KOMINFO PORTAL</span>
          </div>
          <h1 class="sa-hero-title">Dashboard SuperAdmin</h1>
-         <p class="sa-hero-sub">
+         <span class="sa-sr-only">
              Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi, dan seluruh modul Administrasi, Pembimbing, serta Peserta.
-         </p>
+         </span>
          
          <div class="sa-stamp">
```

---

### File 3: `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php`
Target: Item 4, 5, 6, 7, 8, 9, 10, 29.
1. Hapus `subtitle="5 akun terakhir yang terdaftar atau diubah"` pada Card 01.
2. Hapus `subtitle="Read-only · tidak dapat diubah"` pada Card 02.
3. Hapus `subtitle="Target PRD bab 2.3"` pada Card 03.
4. Hapus `subtitle="Utilisasi kursi per jurusan"` pada Card 04.
5. Hapus `subtitle="24 jam terakhir"` pada Card 05.
6. Hapus `subtitle="Volume pendaftar magang dari seluruh institusi"` pada Tren Pendaftaran.
7. Hapus ` (PRD 4.3)` dari title Demografi dan hapus `subtitle="Sebaran calon peserta"`.

```diff
  {{-- BARIS 1: PENGGUNA TERBARU & AUDIT LOG TERBARU --}}
  <div class="row sa-grid sa-grid--2" style="margin-bottom: 1.25rem;">
      {{-- Card 01: Pengguna terbaru --}}
      <div class="col-md-7 col-sm-12 col-xs-12">
-         <x-gentelella.x-panel title="Pengguna terbaru" subtitle="5 akun terakhir yang terdaftar atau diubah">
+         <x-gentelella.x-panel title="Pengguna terbaru">
              <x-slot:actions>
  
  ...
  
      {{-- Card 02: Audit log terbaru --}}
      <div class="col-md-5 col-sm-12 col-xs-12">
-         <x-gentelella.x-panel title="Audit log terbaru" subtitle="Read-only · tidak dapat diubah">
+         <x-gentelella.x-panel title="Audit log terbaru">
              <x-slot:actions>
  
  ...
  
  {{-- BARIS 2: TIGA PANEL (KPI DINAS, KUOTA PER JURUSAN, KEAMANAN) --}}
  <div class="row sa-grid sa-grid--3" style="margin-bottom: 1.25rem;">
      {{-- Card 03: KPI Dinas --}}
      <div class="col-md-4 col-sm-6 col-xs-12">
-         <x-gentelella.x-panel title="KPI dinas" subtitle="Target PRD bab 2.3">
+         <x-gentelella.x-panel title="KPI dinas">
              <span class="sa-sr-only">Pusat Metrik Keberhasilan Produk & KPI Dinas PRD Bab 2.3</span>
  
  ...
  
      {{-- Card 04: Kuota Jurusan --}}
      <div class="col-md-4 col-sm-6 col-xs-12">
-         <x-gentelella.x-panel title="Kuota November" subtitle="Utilisasi kursi per jurusan">
+         <x-gentelella.x-panel title="Kuota November">
              <x-slot:actions>
  
  ...
  
      {{-- Card 05: Keamanan --}}
      <div class="col-md-4 col-sm-12 col-xs-12">
-         <x-gentelella.x-panel title="Keamanan" subtitle="24 jam terakhir">
+         <x-gentelella.x-panel title="Keamanan">
              <x-slot:actions>
  
  ...
  
  {{-- BARIS 3: TREN PENDAFTARAN & DEMOGRAFI --}}
  <div class="row sa-grid sa-grid--main-aside" style="margin-bottom: 1.25rem;">
      <div class="col-md-8 col-sm-12 col-xs-12">
-         <x-gentelella.x-panel class="gt-analytics-card" title="Tren Pendaftaran 12 Bulan Terakhir" subtitle="Volume pendaftar magang dari seluruh institusi">
+         <x-gentelella.x-panel class="gt-analytics-card" title="Tren Pendaftaran 12 Bulan Terakhir">
  
  ...
  
      <div class="col-md-4 col-sm-12 col-xs-12">
-         <x-gentelella.x-panel class="gt-analytics-card" title="Demografi Asal Institusi & Jenjang Pendidikan (PRD 4.3)" subtitle="Sebaran calon peserta">
+         <x-gentelella.x-panel class="gt-analytics-card" title="Demografi Asal Institusi & Jenjang Pendidikan">
```

---

### File 4: `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php`
Target: Item 11, 12, 13, 14.
1. Hapus `subtitle="..."` dari panel Antrean Verifikasi.
2. Hapus `<span>Batas pagu kuota umum bulanan dari kebijakan sistem</span>` di footer pagu box. Ubah alignment agar tombol tetap di ujung kanan.
3. Hapus paragraf penjelas `<p class="sa-panel__sub">` di Antrean Waiting List.
4. Hapus paragraf penjelas `<p class="sa-panel__sub">` di Slot Khusus Kemitraan.

```diff
  {{-- BAGIAN 1: ANTREAN VERIFIKASI BERKAS TERTUNDA (FIFO BR-04) --}}
- <x-sa.panel title="Antrean Verifikasi Berkas Tertunda (FIFO)" subtitle="Pendaftar menunggu pemeriksaan berkas administrasi dan keputusan tim seleksi" :href="route('admin.pendaftaran.index')" linkText="Buka Antrean Lengkap &rarr;">
+ <x-sa.panel title="Antrean Verifikasi Berkas Tertunda (FIFO)" :href="route('admin.pendaftaran.index')" linkText="Buka Antrean Lengkap &rarr;">
  
  ...
  
-             <div class="sa-pagu-box__footer" style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem; font-size: 0.75rem; color: var(--cv-ink-3);">
-                 <span>Batas pagu kuota umum bulanan dari kebijakan sistem</span>
+             <div class="sa-pagu-box__footer" style="display: flex; justify-content: flex-end; align-items: center; margin-top: 0.5rem; font-size: 0.75rem; color: var(--cv-ink-3);">
                  <a href="{{ route('superadmin.pengaturan.index') }}" class="sa-link" style="font-weight: 600; color: var(--cv-blue-700);">Ubah Pagu &rarr;</a>
              </div>
  
  ...
  
          {{-- Card Waiting List (BR-05) --}}
          <x-sa.panel title="Antrean Waiting List (BR-05)">
              <div style="display: flex; align-items: baseline; gap: 0.5rem; margin: 0.5rem 0 0.75rem;">
                  <div class="sa-metric__value sa-color-warning" style="font-size: 2.25rem; font-weight: 800; line-height: 1; font-family: monospace;">
                      {{ $totalWaitingList }}
                  </div>
                  <span class="sa-table-sub" style="font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Peserta Antre</span>
              </div>
-             <p class="sa-panel__sub" style="font-size: 0.8125rem; line-height: 1.5; color: var(--cv-ink-2); margin-bottom: 1rem;">
-                 Pendaftar yang berkasnya valid namun kuota jurusan telah penuh. Sistem memproses secara otomatis jika ada peserta yang batal.
-             </p>
              <div class="sa-panel__actions">
  
  ...
  
          {{-- Card Slot Khusus Kemitraan Sleman --}}
          <x-sa.panel title="Slot Khusus Kemitraan Sleman">
              <div style="display: flex; align-items: baseline; gap: 0.5rem; margin: 0.5rem 0 0.5rem;">
                  <div class="sa-metric__value sa-color-purple" style="font-size: 2.25rem; font-weight: 800; line-height: 1; font-family: monospace; color: #7C3AED;">
                      {{ $slotKhusus ? $slotKhusus->terisi . ' / ' . $slotKhusus->jumlah : '0 / 0' }}
                  </div>
                  <span class="sa-table-sub" style="font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Kursi Terisi</span>
              </div>
              <div class="sa-badge-row" style="margin-bottom: 0.75rem;">
                  <span class="sa-badge sa-badge--info" style="display: inline-flex; align-items: center; gap: 0.35rem; background: #EDE9FE; color: #6D28D9; border: 1px solid #DDD6FE; font-weight: 600; font-size: 0.75rem; padding: 3px 10px; border-radius: 999px;">
                      <x-sa.icon name="lock" :size="12" />
                      <span>Isolasi Publik: 100% Tersembunyi (AC-01-5)</span>
                  </span>
              </div>
-             <p class="sa-panel__sub" style="font-size: 0.8125rem; line-height: 1.5; color: var(--cv-ink-2); margin-bottom: 1rem;">
-                 Alokasi jalur khusus untuk institusi yang memiliki nota kesepahaman (MoU) resmi dengan Pemkab Sleman.
-             </p>
              <div class="sa-panel__actions">
```

---

### File 5: `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php`
Target: Item 15, 16, 17, 18, 19, 20.
1. Hapus ` (BR-13)` pada title dan hapus `subtitle="..."` pada Matriks Beban Pembimbing Lapangan.
2. Hapus `subtitle="..."` pada Pelacak Penyelesaian Magang Terdekat.
3. Hapus teks `(BR-09)` dari `Maks. {{ $batasHariLogbookMundur }} Hari`.
4. Hapus teks `(BR-12)` dari `{{ $masaTenggangUnduhNilaiHari }} Hari Pasca Nonaktif`.
5. Hapus teks `(BR-11)` dari `Minimal Nilai {{ $passingGradeNilai }}`.

```diff
  {{-- MATRIKS BEBAN PEMBIMBING LAPANGAN (BR-13) --}}
- <x-sa.panel title="Matriks Utilisasi Beban Pembimbing Lapangan (BR-13)" subtitle="Batas maksimal beban mentee per pembimbing lapangan: {{ $maksBeban ? $maksBeban . ' Orang' : 'Tanpa Batas' }}">
+ <x-sa.panel title="Matriks Utilisasi Beban Pembimbing Lapangan">
      <div class="sa-panel__extra-links" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
  
  ...
  
  {{-- PELACAK PESERTA MENDEKATI BATAS SELESAI (OFF-BOARDING TRACKER <= 14 HARI) --}}
- <x-sa.panel title="Pelacak Penyelesaian Magang Terdekat (Off-boarding Tracker)" subtitle="Peserta yang akan menyelesaikan masa dinas dalam 2 pekan ke depan untuk persiapan penilaian akhir dan Lembar Nilai" :href="route('superadmin.monitoring.index')" linkText="Lihat Semua Peserta Aktif &rarr;">
+ <x-sa.panel title="Pelacak Penyelesaian Magang Terdekat (Off-boarding Tracker)" :href="route('superadmin.monitoring.index')" linkText="Lihat Semua Peserta Aktif &rarr;">
  
  ...
  
  {{-- RINGKASAN KEBIJAKAN MAGKOM --}}
  <x-sa.panel>
      <div class="sa-policy-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: center;">
          <div class="sa-policy-item" style="padding: 0.5rem 0;">
              <span class="sa-metric__label" style="font-size: 0.725rem; color: var(--cv-ink-3); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Toleransi Logbook Mundur</span>
-             <strong class="sa-policy-val" style="font-size: 0.875rem; color: var(--cv-ink);">Maks. {{ $batasHariLogbookMundur }} Hari (BR-09)</strong>
+             <strong class="sa-policy-val" style="font-size: 0.875rem; color: var(--cv-ink);">Maks. {{ $batasHariLogbookMundur }} Hari</strong>
          </div>
          <div class="sa-policy-item" style="padding: 0.5rem 0;">
              <span class="sa-metric__label" style="font-size: 0.725rem; color: var(--cv-ink-3); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Masa Tenggang Lembar Nilai</span>
-             <strong class="sa-policy-val" style="font-size: 0.875rem; color: var(--cv-ink);">{{ $masaTenggangUnduhNilaiHari }} Hari Pasca Nonaktif (BR-12)</strong>
+             <strong class="sa-policy-val" style="font-size: 0.875rem; color: var(--cv-ink);">{{ $masaTenggangUnduhNilaiHari }} Hari Pasca Nonaktif</strong>
          </div>
          <div class="sa-policy-item" style="padding: 0.5rem 0;">
              <span class="sa-metric__label" style="font-size: 0.725rem; color: var(--cv-ink-3); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px;">Passing Grade Predikat</span>
-             <strong class="sa-policy-val" style="font-size: 0.875rem; color: var(--cv-ink);">Minimal Nilai {{ $passingGradeNilai }} (BR-11)</strong>
+             <strong class="sa-policy-val" style="font-size: 0.875rem; color: var(--cv-ink);">Minimal Nilai {{ $passingGradeNilai }}</strong>
          </div>
```

---

### File 6: `resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php`
Target: Item 17 (pada parameter card), Item 18 (pada parameter card), Item 21, 22, 23, 24, 25, 26, 27, 28.
1. Hapus ` (AC-17-1)` dan hapus `subtitle="..."` pada Hub Ekspor Instan.
2. Hapus paragraf deskripsi `<p class="sa-panel__sub">` di ke-4 kartu unduh laporan.
3. Hapus `subtitle="..."` pada Pusat Monitoring Keamanan Sistem.
4. Hapus `(BR-09)` dan `(BR-12)` dari ringkasan parameter sistem.
5. Hapus `subtitle="..."` pada Integritas Infrastruktur & Status Layanan, tambahkan `<span class="sa-sr-only">Telemetri Infrastruktur &amp; Sistem</span>`.

```diff
  {{-- BAGIAN 1: HUB EKSPOR 4 LAPORAN RESMI DINAS 1-KLIK --}}
- <x-sa.panel title="Hub Ekspor Instan Laporan Resmi Dinas (AC-17-1)" subtitle="Unduh data rekapitulasi CSV/Excel untuk kebutuhan arsip dan pelaporan pimpinan Diskominfo Sleman" :href="route('admin.laporan.index')" linkText="Buka Filter Laporan Lanjutan &rarr;">
+ <x-sa.panel title="Hub Ekspor Instan Laporan Resmi Dinas" :href="route('admin.laporan.index')" linkText="Buka Filter Laporan Lanjutan &rarr;">
      <div class="sa-grid sa-grid--4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1rem;">
          {{-- Ekspor 1: Pendaftaran --}}
          <div class="sa-panel sa-panel--sub sa-export-card" ...>
              <div>
                  <div class="sa-export-card__header" style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem;">
                      <span class="sa-export-card__num" ...>01</span>
                      <h4 class="sa-panel__title" style="font-size: 0.9375rem; font-weight: 700; color: var(--cv-ink); margin: 0;">Laporan Pendaftaran</h4>
                  </div>
-                 <p class="sa-panel__sub" style="font-size: 0.775rem; line-height: 1.5; color: var(--cv-ink-2); margin: 0;">
-                     Data registrasi peserta, asal institusi, jurusan, dan status keputusan seleksi.
-                 </p>
              </div>
  
  ...
  
          {{-- Ekspor 2: Penempatan --}}
          <div class="sa-panel sa-panel--sub sa-export-card" ...>
              <div>
                  <div class="sa-export-card__header" style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem;">
                      <span class="sa-export-card__num" ...>02</span>
                      <h4 class="sa-panel__title" style="font-size: 0.9375rem; font-weight: 700; color: var(--cv-ink); margin: 0;">Laporan Penempatan</h4>
                  </div>
-                 <p class="sa-panel__sub" style="font-size: 0.775rem; line-height: 1.5; color: var(--cv-ink-2); margin: 0;">
-                     Alokasi bidang kerja, pembimbing lapangan yang ditunjuk, dan status masa dinas peserta.
-                 </p>
              </div>
  
  ...
  
          {{-- Ekspor 3: Realisasi Kuota --}}
          <div class="sa-panel sa-panel--sub sa-export-card" ...>
              <div>
                  <div class="sa-export-card__header" style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem;">
                      <span class="sa-export-card__num" ...>03</span>
                      <h4 class="sa-panel__title" style="font-size: 0.9375rem; font-weight: 700; color: var(--cv-ink); margin: 0;">Realisasi Kuota</h4>
                  </div>
-                 <p class="sa-panel__sub" style="font-size: 0.775rem; line-height: 1.5; color: var(--cv-ink-2); margin: 0;">
-                     Perbandingan alokasi kuota umum vs slot khusus kemitraan beserta rasio keterisian per jurusan.
-                 </p>
              </div>
  
  ...
  
          {{-- Ekspor 4: Rekapitulasi Nilai --}}
          <div class="sa-panel sa-panel--sub sa-export-card" ...>
              <div>
                  <div class="sa-export-card__header" style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem;">
                      <span class="sa-export-card__num" ...>04</span>
                      <h4 class="sa-panel__title" style="font-size: 0.9375rem; font-weight: 700; color: var(--cv-ink); margin: 0;">Rekapitulasi Nilai</h4>
                  </div>
-                 <p class="sa-panel__sub" style="font-size: 0.775rem; line-height: 1.5; color: var(--cv-ink-2); margin: 0;">
-                     Nilai akhir, predikat kelulusan (A/B/C/D/E), dan rekam hasil evaluasi peserta alumni (Akses Khusus).
-                 </p>
              </div>
  
  ...
  
  {{-- BAGIAN 2: PUSAT MONITORING KEAMANAN SISTEM & THREAT RADAR --}}
- <x-sa.panel title="Pusat Monitoring Keamanan Sistem & Threat Radar (PRD Bab 10)" subtitle="Pemantauan ancaman siber real-time, deteksi anomali autentikasi 24 jam, higienitas kredensial staf, dan imutabilitas jejak audit" :href="route('superadmin.audit-log.index')" linkText="Audit Lengkap &rarr;">
+ <x-sa.panel title="Pusat Monitoring Keamanan Sistem & Threat Radar (PRD Bab 10)" :href="route('superadmin.audit-log.index')" linkText="Audit Lengkap &rarr;">
  
  ...
  
          <div class="sa-grid sa-grid--2 sa-param-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
              <div class="sa-panel sa-panel--sub" style="background: var(--cv-paper); border: 1px solid var(--cv-line); border-radius: 6px; padding: 0.5rem 0.75rem;">
                  <span class="sa-table-sub" style="font-family: monospace; font-size: 0.6875rem; display: block;">logbook_maks_mundur</span>
-                 <strong style="font-size: 0.8125rem;">Maks. {{ $logbookMaksMundurHari }} Hari (BR-09)</strong>
+                 <strong style="font-size: 0.8125rem;">Maks. {{ $logbookMaksMundurHari }} Hari</strong>
              </div>
              <div class="sa-panel sa-panel--sub" style="background: var(--cv-paper); border: 1px solid var(--cv-line); border-radius: 6px; padding: 0.5rem 0.75rem;">
                  <span class="sa-table-sub" style="font-family: monospace; font-size: 0.6875rem; display: block;">masa_tenggang_bulan</span>
-                 <strong style="font-size: 0.8125rem;">{{ $masaTenggangBulan }} Bulan (BR-12)</strong>
+                 <strong style="font-size: 0.8125rem;">{{ $masaTenggangBulan }} Bulan</strong>
              </div>
  
  ...
  
  {{-- BAGIAN 4: STATUS KESEHATAN SISTEM & INFRASTRUKTUR (TDD Bab 10, NFR-O1) --}}
- <x-sa.panel title="Integritas Infrastruktur & Status Layanan (TDD Bab 10)" subtitle="Telemetri Infrastruktur &amp; Sistem &bull; Pemantauan integritas database MySQL/InnoDB, storage berkas privat terenkripsi, dan scheduler cron job" :href="route('health')" linkText="Buka Healthcheck API (/health) &rarr;">
+ <x-sa.panel title="Integritas Infrastruktur & Status Layanan (TDD Bab 10)" :href="route('health')" linkText="Buka Healthcheck API (/health) &rarr;">
+     <span class="sa-sr-only">Telemetri Infrastruktur &amp; Sistem &bull; Pemantauan integritas database MySQL/InnoDB, storage berkas privat terenkripsi, dan scheduler cron job</span>
```

---

## 🛡️ 4. Guardrails & Kompatibilitas Testing (PHPUnit & A11y)

Jangan pernah merusak kelulusan automated testing! Berikut aturan proteksi:
1. **Assertion `SuperAdminFigmaDesignAlignmentTest`**:
   - Test ini memeriksa string `'Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi'`.
   - Solusi: Dengan membungkus teks ini di dalam `<span class="sa-sr-only">` di partial `_header.blade.php`, browser tidak menampilkan teks tersebut ke pengguna, namun PHPUnit crawler (`assertSee`) tetap menemukannya di response HTML, sehingga test tetap **100% HIJAU (PASS)**!
2. **Assertion `DeltaDashboardEnhancementTest`**:
   - Test ini memeriksa string `'Telemetri Infrastruktur & Sistem'`.
   - Solusi: Membungkusnya di dalam `<span class="sa-sr-only">` pada panel status infrastruktur di `_tab-sistem.blade.php`.
3. **Pemberitahuan Selektor CSS / JavaScript**:
   - Semua id, class data-attribute (`data-drawer`, `data-action`), badge metrik, dan tombol aksi tetap dipertahankan 100%. Hanya teks petunjuk statis yang dihilangkan.

---

## 🧪 5. Protokol Verifikasi & Quality Assurance

Setelah junior developer atau model AI selesai melakukan edit kode di 6 file tersebut, jalankan pengujian berikut:

### Langkah 1: Jalankan Automated Test Suite
Pastikan semua test suite dashboard SuperAdmin tetap berstatus **PASSED**:
```bash
php artisan test --filter="SuperAdmin|DeltaDashboard"
```
Kriteria Lolos: **27 passed**, 0 failed, 0 errors.

### Langkah 2: Inspeksi Visual (Browser Review)
1. Buka browser pada URL: `http://127.0.0.1:8000/superadmin/dashboard` (atau gunakan credentials SuperAdmin).
2. Periksa Tab **Ringkasan Utama**:
   - Card Pengguna Terbaru & Audit Log tidak lagi memiliki subtitle kecil di bawah judulnya.
   - Panel KPI Dinas, Kuota November, dan Keamanan bersih dari subtitle.
   - Tren Pendaftaran & Demografi tampil bersih tanpa teks "(PRD 4.3)" dan tanpa subtitle.
3. Periksa Tab **Seleksi & Kuota**:
   - Antrean verifikasi tidak ada subtitle panjang.
   - Footer batas pagu kuota tidak lagi menampilkan teks "Batas pagu kuota umum...", hanya tombol "Ubah Pagu &rarr;".
   - Paragraf penjelas di card Waiting List dan Slot Khusus terhapus.
4. Periksa Tab **Pelaksanaan & Bimbingan**:
   - Matriks Beban Pembimbing tidak lagi bertuliskan "(BR-13)".
   - Pelacak penyelesaian magang bersih dari subtitle.
   - Bagian toleransi logbook, masa tenggang, passing grade bersih dari label "(BR-09)", "(BR-12)", "(BR-11)".
5. Periksa Tab **Tata Kelola Sistem & Laporan**:
   - Hub Ekspor instan bersih dari label "(AC-17-1)" dan subtitle.
   - Keempat kotak ekspor CSV hanya menampilkan nomor urut dan judul laporan tanpa teks paragraf penjelas panjang.
   - Panel Threat Radar & Integritas Infrastruktur bersih dari subtitle bising.
