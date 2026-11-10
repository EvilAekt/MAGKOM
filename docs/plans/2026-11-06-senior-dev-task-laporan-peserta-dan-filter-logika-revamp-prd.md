# TASK / PRD: Penyesuaian Tampilan Laporan Peserta & Perbaikan Logika Filter Tugas & Logbook Pembimbing
## Dokumen Spesifikasi Teknis & Rencana Kerja (Senior Developer ke Junior Developer / AI Model)

- **Target Modul**: 
  1. Portal Pembimbing — Modul Laporan Peserta (`/pembimbing/laporan-kinerja`)
  2. Portal Pembimbing — Modul Tugas & Progres Magang (`/pembimbing/tugas`)
  3. Portal Pembimbing — Modul Logbook Peserta (`/pembimbing/logbook`)
- **Pemberi Task**: Senior Developer MagKom Sleman
- **Pelaksana Task**: Junior Developer / AI Assistant (Gemini 3.8 Flash / Antigravity)
- **Status Dokumen**: Siap Ditinjau & Disetujui (Ready for Review & Approval)
- **Prioritas**: UI/UX Flow Optimization, Formal Government Tone Alignment & State Logic Fix

---

## 1. LATAR BELAKANG & ANALISIS MASALAH (ROOT CAUSE ANALYSIS)

Halo Junior Developer / AI,

Berikut adalah arahan arsitektur dan spesifikasi teknis untuk menyelesaikan dua keluhan utama terkait antarmuka dan logika interaksi pada Portal Pembimbing MagKom Sleman:

---

### Masalah 1: "Kenapa di Laporan Peserta Ada Logbook?" (Foto 1)
*Referensi Foto: `media_1793963967031_7992081c.png`*

#### 🔍 Penjelasan Root Cause: Mengapa Muncul Tulisan Logbook?
1. **Asal-usul Kode**: 
   Sebelumnya, kartu statistik 4-tile Gentelella pada halaman Laporan Peserta disalin (*code parity*) langsung dari *Direktori Peserta* (`resources/views/pembimbing/peserta/index.blade.php`).
2. **Kelemahan Teks / Diksi**:
   Pada Kartu ke-3 (**PERLU PERHATIAN**), teks deskripsi bawah (*subtext*) tertulis:
   ```blade
   subText="Tugas overdue / logbook bolong"
   ```
   - Penggunaan kata **"bolong"** adalah bahasa pergaulan/slang informal yang **sangat tidak pantas dan tidak profesional** untuk portal pemerintahan resmi Dinas Komunikasi dan Informatika (Diskominfo Sleman).
   - Munculnya kata **"logbook"** di kartu tersebut memicu kebingungan: *"Kenapa di modul laporan penempatan/kinerja ada istilah logbook bolong?"*.
3. **Solusi Senior Dev**:
   - Ganti diksi tidak formal tersebut dengan bahasa kedinasan baku dan lugas.
   - Pilihan standar kedinasan:
     - **Opsi Rekomendasi**: `"Tugas lewat tenggat / belum tuntas"` (Jika fokus murni pada kewajiban tugas).
     - **Opsi Alternatif**: `"Tugas terlambat / jurnal tertunda"` (Jika tetap mencakup kedua parameter evaluasi kedisiplinan).

---

### Masalah 2: Cacat Logika Filter pada Modul Tugas & Logbook (Foto 2)
*Referensi Foto: `media_1793964190705_160993ee.png`*

#### 🔍 Penjelasan Root Cause: Kenapa Langsung Masuk ke Nama Peserta dan Filternya Lenyap?
1. **Perilaku Rusak Saat Ini**:
   - Di halaman `Tugas & Progres Magang` dan `Logbook Peserta`, terdapat bar filter: `Filter Nama Peserta`, `Filter Bulan`, tombol `Filter`, dan `Reset`.
   - Namun, ketika pembimbing memilih salah satu nama peserta di dropdown, sistem menganggap aksi tersebut sebagai **"Fokus / Drill-down ke Profil Mentee"**.
   - Akibat implementasi sebelumnya (`2026-11-06-sinkronisasi-header-mentee-dan-sembunyikan-filter-fokus.md`):
     - **Filter Bar Diberi Class `d-none`**: Elemen form filter tiba-tiba menghilang dari layar! Pembimbing tidak bisa melihat apa yang sedang difilter, tidak bisa memilih peserta lain dari dropdown, dan tidak bisa mengganti bulan.
     - **Judul Utama Panel Diberi Class `d-none`**: Judul resmi panel `"Daftar Penugasan & Progres Mentee"` (pada tugas) dan `"Daftar Logbook & Catatan Pembimbing"` (pada logbook) lenyap, digantikan paksa oleh header profil mentee (avatar, nama, dan institusi).
2. **Keluhan Pengguna yang Sangat Beralasan**:
   > *"Filter yang ada Logbook Peserta dan Tugas & Progres Magang ketika pilih nama jangan langsung masuk ke dalam nama peserta tapi munculin dulu misal ke Daftar Penugasan & Progres Mentee dan Daftar Logbook & Catatan Pembimbing coba logikanya dibenerin dong."*
3. **Solusi Senior Dev (Logika yang Benar)**:
   - **Filter adalah Filter, Bukan Navigasi Halaman**:
     1. **Filter Card TETAP TAMPIL**: Form filter di atas panel TIDAK BOLEH disembunyikan (`d-none`). Pembimbing harus selalu bisa melihat dropdown nama peserta yang sedang aktif, mengganti pilihan, memilih bulan, atau klik `Reset`.
     2. **Judul Panel Utama TETAP MUNCUL**:
        - Halaman Tugas: Judul panel tetap `"Daftar Penugasan & Progres Mentee"`.
        - Halaman Logbook: Judul panel tetap `"Daftar Logbook & Catatan Pembimbing"`.
     3. **Tampilan Hasil Filter**:
        - Di dalam panel, data otomatis terfilter menampilkan entri/kartu milik mentee yang dipilih saja.
        - Tampilkan **badge filter aktif kecil** yang elegan di samping judul panel (misal: `<span class="badge-filter-mentee-active">Filter: Lukman Hakim</span>`), TANPA menghapus judul panel dan TANPA menghilangkan filter card.

---

## 2. SPESIFIKASI TEKNIS & PERUBAHAN ARSITEKTUR

### 2.1 Perubahan pada Modul Laporan Peserta
1. **File View**: `resources/views/pembimbing/laporan-kinerja/index.blade.php`
   - Pada Tile 3 (`<x-gentelella.tile-stat title="PERLU PERHATIAN" ...>`):
     - Ubah `subText="Tugas overdue / logbook bolong"` menjadi:
       ```blade
       subText="Tugas lewat tenggat / belum tuntas"
       ```
2. **File Test**: `tests/Feature/PembimbingLaporanKinerjaViewTest.php`
   - Sesuaikan string assertion: ganti pencarian `'Tugas overdue / logbook bolong'` dengan `'Tugas lewat tenggat / belum tuntas'`.

---

### 2.2 Perubahan pada Modul Tugas & Progres Magang
1. **File View**: `resources/views/pembimbing/tugas/index.blade.php`
   - **Hapus `d-none` dari Filter Card**:
     - Hilangkan kelas dinamis `{{ $isFocusActive ? 'd-none' : '' }}` dari `<div id="tugasFilterCard" class="tugas-filter-card">`.
   - **Pertahankan Judul Default Panel**:
     - Judul `"Daftar Penugasan & Progres Mentee"` selalu tampil (`defaultTitleText` tidak diberi `d-none`).
     - Jika `$isFocusActive` bernilai true, tampilkan badge filter aktif:
       ```blade
       <div class="panel-title-with-filter">
           <span class="default-panel-title">Daftar Penugasan &amp; Progres Mentee</span>
           @if($isFocusActive)
               <span class="badge-filter-mentee-active">
                   Filter: <strong>{{ $focusedNama }}</strong>
               </span>
           @endif
       </div>
       ```
2. **File JavaScript**: `resources/js/pages/pembimbing/tugas-index.js`
   - Pada fungsi `focusOnMentee()`:
     - **HAPUS** baris `filterCard.classList.add('d-none');`. Filter card harus selalu terlihat.
     - Jangan sembunyikan `defaultTitleText`.
   - Pada fungsi `resetToAllMentees()`:
     - Tetap sinkronkan nilai dropdown dan URL.

---

### 2.3 Perubahan pada Modul Logbook Peserta
1. **File View**: `resources/views/pembimbing/logbook/index.blade.php`
   - **Hapus `d-none` dari Filter Card**:
     - Hilangkan kelas dinamis `{{ $isFocusActive ? 'd-none' : '' }}` dari `<div id="logbookFilterCard" class="logbook-filter-card">`.
   - **Pertahankan Judul Default Panel**:
     - Judul `"Daftar Logbook & Catatan Pembimbing"` selalu tampil.
     - Tambahkan badge filter aktif ketika `$isFocusActive` bernilai true:
       ```blade
       <div class="panel-title-with-filter">
           <span class="default-panel-title">Daftar Logbook &amp; Catatan Pembimbing</span>
           @if($isFocusActive)
               <span class="badge-filter-mentee-active">
                   Filter: <strong>{{ $focusedNama }}</strong>
               </span>
           @endif
       </div>
       ```
2. **File JavaScript**: `resources/js/pages/pembimbing/logbook-index.js`
   - Pada fungsi `focusOnMentee()`:
     - **HAPUS** baris `if (filterCard) filterCard.classList.add('d-none');`. Filter card harus selalu terlihat.
     - Jangan sembunyikan `defaultTitleText`.
   - Pada fungsi `resetToAllMentees()`:
     - Tetap sinkronkan state tampilan dan dropdown.

---

## 3. TABEL PEMETAAN BERKAS (FILE IMPACT MATRIX)

| No | File Path | Aksi | Deskripsi Perubahan |
|---|---|---|---|
| 1 | `resources/views/pembimbing/laporan-kinerja/index.blade.php` | Edit | Mengganti subtext Tile 3 dari slang `"Tugas overdue / logbook bolong"` menjadi bahasa baku `"Tugas lewat tenggat / belum tuntas"`. |
| 2 | `resources/views/pembimbing/tugas/index.blade.php` | Edit | Memastikan `#tugasFilterCard` tidak disembunyikan saat filter aktif; judul `"Daftar Penugasan & Progres Mentee"` selalu tampil; menambahkan badge filter aktif. |
| 3 | `resources/views/pembimbing/logbook/index.blade.php` | Edit | Memastikan `#logbookFilterCard` tidak disembunyikan saat filter aktif; judul `"Daftar Logbook & Catatan Pembimbing"` selalu tampil; menambahkan badge filter aktif. |
| 4 | `resources/js/pages/pembimbing/tugas-index.js` | Edit | Menghapus pemanggilan `classList.add('d-none')` pada filter card saat filter nama mentee dipilih. |
| 5 | `resources/js/pages/pembimbing/logbook-index.js` | Edit | Menghapus pemanggilan `classList.add('d-none')` pada filter card saat filter nama mentee dipilih. |
| 6 | `resources/css/pages/pembimbing/tugas-index.css` & `logbook-index.css` | Edit | Menambahkan styling untuk `.panel-title-with-filter` dan `.badge-filter-mentee-active`. |
| 7 | `tests/Feature/PembimbingLaporanKinerjaViewTest.php` | Edit | Memperbarui teks assertion dari kalimat lama ke kalimat baku baru. |
| 8 | `tests/Feature/PembimbingFocusedMenteeTugasViewTest.php` & `LogbookViewTest.php` | Edit | Menyesuaikan ekspektasi test bahwa filter card tetap ada dan judul utama tetap tampil saat filter aktif. |

---

## 4. PANDUAN EKSEKUSI TAHAP DEMI TAHAP (STEP-BY-STEP RUNBOOK)

Junior Developer / AI harus mengeksekusi tahapan ini secara berurutan:

### Tahap 1: Perbaikan Modul Laporan Peserta
- [ ] Buka `resources/views/pembimbing/laporan-kinerja/index.blade.php`.
- [ ] Ubah subText Tile 3 menjadi `"Tugas lewat tenggat / belum tuntas"`.
- [ ] Buka `tests/Feature/PembimbingLaporanKinerjaViewTest.php`, perbarui assertSee teks tersebut.
- [ ] Jalankan test: `php artisan test --filter=PembimbingLaporanKinerjaViewTest`.

### Tahap 2: Perbaikan Logika Filter Tugas
- [ ] Buka `resources/views/pembimbing/tugas/index.blade.php`.
- [ ] Hapus kondisi `d-none` dari wrapper `#tugasFilterCard`.
- [ ] Pastikan `#defaultTitleText` `"Daftar Penugasan & Progres Mentee"` selalu dirender.
- [ ] Tambahkan badge informasi filter jika `$isFocusActive`.
- [ ] Buka `resources/js/pages/pembimbing/tugas-index.js`, cegah penghilangan kartu filter di event handler.

### Tahap 3: Perbaikan Logika Filter Logbook
- [ ] Buka `resources/views/pembimbing/logbook/index.blade.php`.
- [ ] Hapus kondisi `d-none` dari wrapper `#logbookFilterCard`.
- [ ] Pastikan `#defaultTitleText` `"Daftar Logbook & Catatan Pembimbing"` selalu dirender.
- [ ] Tambahkan badge informasi filter jika `$isFocusActive`.
- [ ] Buka `resources/js/pages/pembimbing/logbook-index.js`, cegah penghilangan kartu filter di event handler.

### Tahap 4: Penyesuaian CSS
- [ ] Buka `resources/css/pages/pembimbing/tugas-index.css` dan `resources/css/pages/pembimbing/logbook-index.css`.
- [ ] Tambahkan styling `.panel-title-with-filter` dan `.badge-filter-mentee-active` (badge biru MagKom elegan).

### Tahap 5: Quality Gate & Verifikasi
- [ ] Jalankan build frontend: `npm run build`.
- [ ] Jalankan linter kode: `./vendor/bin/pint --test`.
- [ ] Jalankan test suite pembimbing: `php artisan test --filter=Pembimbing`.

---

## 5. KRITERIA PENERIMAAN (ACCEPTANCE CRITERIA)

1. **Laporan Peserta**:
   - Tidak ada lagi kata slang *"bolong"* di seluruh halaman Laporan Peserta.
   - Tile 3 menampilkan teks resmi: `"Tugas lewat tenggat / belum tuntas"`.
2. **Logika Filter Tugas & Logbook**:
   - Saat pembimbing memilih nama peserta di dropdown filter atau memfilter bulan, **Filter Bar tetap terlihat** di atas panel dan tidak menghilang.
   - Judul utama panel **"Daftar Penugasan & Progres Mentee"** dan **"Daftar Logbook & Catatan Pembimbing"** tetap terpampang jelas.
   - Hasil filter data di dalam panel menampilkan entri peserta yang dipilih, disertai badge status filter aktif.
   - Pembimbing dapat dengan mudah mengganti peserta atau menekan tombol `Reset` kapan saja tanpa harus mencari tombol kembali.
3. **Quality Gate**:
   - Seluruh test PHPUnit `Pembimbing` lolos hijau (`100% PASS`).
   - Tidak ada syntax error atau inline script/style di file Blade.

