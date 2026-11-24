# TASK & PRD IMPLEMENTATION PLAN: REDESIGN HALAMAN DETAIL PENEMPATAN KELOMPOK MENJADI LAYOUT 2-KOLOM (PARITY DENGAN WORKSPACE TIM CSIRT)

> **Dari:** Senior Software Architect / Lead Developer  
> **Kepada:** Junior Developer / AI Implementation Engineer (Gemini 3.8 Flash Compatible)  
> **Target Repositori:** `MAGKOM` (Portal Manajemen Magang Diskominfo Kabupaten Sleman)  
> **Status Dokumen:** READY FOR REVIEW & USER APPROVAL  
> **Tingkat Prioritas:** Critical (Direct User Feedback on Detail Page Experience)  
> **Tanggal Dokumen:** 24 November 2026  
> **Referensi Gambar User:**
> 1. Gambar 1: `media_1795510784154_fa355f43.png` (Tampilan Layout Acuan Tim CSIRT: Top Identity Kelompok, Kolom Kiri Info Penempatan Kelompok, Kolom Kanan Formulir Unggah Surat Selesai)
> 2. Gambar 2: `media_1795510792059_d737f2c9.png` (Kolom `AKSI INDIVIDUAL` dengan tombol `[ Detail ]` yang saat ini membuka halaman penempatan)

---

## 1. 🎯 RINGKASAN EKSEKUTIF & LATAR BELAKANG PRD

### 1.1 Latar Belakang Masalah (Problem Statement)
Pengguna memberikan koreksi eksplisit:
> *"yg bagian detail isi nya tampilan ini Tim CSIRT & Keamanan Siber Sleman kayak di foto bukan kayak yg sekarang"*

Saat ini, ketika admin berada di halaman Surat Selesai Kelompok dan mengklik tombol **`[ Detail ]`** pada kolom **`AKSI INDIVIDUAL`** (Gambar 2):
1. Sistem membuka route `admin.penempatan.show` (`/admin/penempatan/{id}`).
2. Halaman tersebut saat ini menampilkan antarmuka *Detail Penempatan Peserta* bergaya dashboard individu klasik:
   - 4 kotak stat metrik (Tugas Selesai 0/0, Total Logbook 0 Minggu, Status Magang, Durasi Magang).
   - Layout grid 8-kolom vs 4-kolom yang berantakan untuk konteks kelompok.
   - Form unggah surat kecil di pojok kanan bawah.
3. Admin merasa tidak sinkron dan kecewa karena ekspektasinya ketika membuka detail penempatan dari kelompok adalah melihat tampilan **Tim CSIRT & Keamanan Siber Sleman** yang bersih, rapi, dan terpadu seperti pada **Gambar 1**!

### 1.2 Target Solusi (Objectives & Key Results)
1. **Redesign Khusus Kelompok pada `admin.penempatan.show`**:
   - Jika peserta merupakan anggota kelompok (`$penempatan->isKelompok()` bernilai `true`):
     - Gantikan seluruh isi dashboard klasik (4 stat metrik tugas/logbook) dengan **Tampilan Tim CSIRT Terpadu** persis seperti Gambar 1:
       1. **Top Identity Bar Kelompok**:
          - Icon kelompok `[👥]`, Nama Kelompok: `Tim CSIRT & Keamanan Siber Sleman`
          - Subtitle: `Universitas Negeri Yogyakarta • 3 Anggota Terdaftar • Bidang: Infrastruktur Teknologi Informasi (ITI)`
          - Chip Status di kanan: `[ ⚠️ BELUM DIUNGGAH ]` atau `[ ✓ SURAT TIM TERBIT ]`
       2. **Grid 2-Kolom Seimbang (`.focused-content-grid`)**:
          - **Kolom Kiri**: Card *Informasi Penempatan Kelompok* (Nama Kelompok, Bidang Penempatan, Pembimbing Lapangan, Periode Magang, Tanggal Selesai, Status Surat Tim & Tombol Unduh).
          - **Kolom Kanan**: Card *Formulir Unggah Surat Selesai* dengan Custom File Picker PDF (maks 2MB) dan tombol aksi Sleman Navy.
       3. **Di Bawah Grid 2-Kolom**: Panel *Daftar Anggota Kelompok* (Tabel Rincian Anggota Tim mandiri dengan nomor, nama, peran tim, NIM/institusi, status surat, dan penanda `(Sedang Dilihat)` untuk peserta saat ini).
2. **Preservasi untuk Peserta Individu Mandiri**:
   - Jika peserta mendaftar secara individu mandiri (`!$penempatan->isKelompok()`), halaman `admin.penempatan.show` tetap mempertahankan metrik tugas dan logbook individualnya seperti biasa.

---

## 2. 🛡️ ATURAN ARSITEKTUR & KEAMANAN (SENIOR GUARDRAILS)

1. **JANGAN MERUSAK STRUKTUR DATABASE (Zero-Schema Change)**:
   - Gunakan relasi Eloquent yang sudah ada:
     - `$penempatan->isKelompok()`
     - `$penempatan->pendaftaran?->kelompokMagang`
     - `$penempatan->getRekanKelompokPenempatan()`
2. **Kepatuhan Aturan Blade (STRICT NO INLINE CSS/JS)**:
   - Dilarang menulis tag `<style>` atau `<script>` di dalam file Blade.
   - Gunakan class CSS terstruktur yang sudah ada di [penempatan-detail.css](file:///G:/MAGKOM/resources/css/pages/admin/penempatan-detail.css) dan [surat-selesai-index.css](file:///G:/MAGKOM/resources/css/pages/admin/surat-selesai-index.css).
3. **Standar Runtime & Testing**:
   - Seluruh command verifikasi dijalankan menggunakan PHP 8.3 aktual: `F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe`.

---

## 3. 🎨 WIREFRAME VISUAL PERUBAHAN (`admin.penempatan.show` MODE KELOMPOK)

Tampilan baru ketika membuka Detail Penempatan Peserta Kelompok (Persis Gambar 1):
```
+---------------------------------------------------------------------------------------------------+
| Detail Penempatan Peserta                                          [< Kembali] [Pratinjau] [Unduh]|
+---------------------------------------------------------------------------------------------------+
| [👥] Tim CSIRT & Keamanan Siber Sleman                                       [ ⚠️ BELUM DIUNGGAH ] |
|      Universitas Negeri Yogyakarta • 3 Anggota Terdaftar • Bidang: Infrastruktur Teknologi ITI    |
+--------------------------------------------------+------------------------------------------------+
| INFORMASI PENEMPATAN KELOMPOK                    | FORMULIR UNGGAH SURAT SELESAI                  |
| (Kolom Kiri - Persis Gambar 1)                   | (Kolom Kanan - Persis Gambar 1)                |
|                                                  |                                                |
| NAMA KELOMPOK                                    | Unggah berkas resmi Surat Keterangan Selesai   |
| Tim CSIRT & Keamanan Siber Sleman                | Magang format PDF dengan ukuran maksimal 2MB.  |
|                                                  |                                                |
| BIDANG PENEMPATAN                                | [ 📎 Pilih Berkas PDF ] [ Belum ada file... ]   |
| Infrastruktur Teknologi Informasi (ITI)          |                                                |
|                                                  | +--------------------------------------------+ |
| PEMBIMBING LAPANGAN                              | | 📤 Unggah Surat Resmi                      | |
| Sri Wahyuni, S.I.Kom                             | +--------------------------------------------+ |
|                                                  |                                                |
| PERIODE MAGANG                                   |                                                |
| 24 Agt 2026 s/d 22 Nov 2026                      |                                                |
|                                                  |                                                |
| TANGGAL SELESAI                                  |                                                |
| 22 November 2026                                 |                                                |
|                                                  |                                                |
| +----------------------------------------------+ |                                                |
| | Status Surat Selesai Tim: Menunggu Berkas    | |                                                |
| | Surat keterangan tim belum diunggah...       | |                                                |
| +----------------------------------------------+ |                                                |
+--------------------------------------------------+------------------------------------------------+
| DAFTAR ANGGOTA KELOMPOK: TIM CSIRT & KEAMANAN SIBER SLEMAN (3 ORANG)                              |
| +----+-----------------------+------------------+-------------------+-------------------+-----+   |
| | NO | NAMA ANGGOTA          | PERAN TIM        | NIM / INSTITUSI   | STATUS SURAT TIM  | AKSI|   |
| +----+-----------------------+------------------+-------------------+-------------------+-----+   |
| | 1  | Farhan Aditya Pratama | [Ketua Kelompok] | 21520244001 • UNY | Belum Diterbitkan | [...]   |
| | 2  | Maya Safira Kusuma    | [Anggota]        | 21520244002 • UNY | Belum Diterbitkan | [...]   |
| | 3  | Gilang Pamungkas      | [Anggota]        | 21520244003 • UNY | Belum Diterbitkan | [...]   |
| +----+-----------------------+------------------+-------------------+-------------------+-----+   |
```

---

## 4. 📁 MATRIKS PERUBAHAN TEKNIS BERKAS

| No | File Path | Aksi | Tanggung Jawab & Detail Perubahan |
| :--- | :--- | :---: | :--- |
| 1 | `resources/views/admin/penempatan/show.blade.php` | **Modify** | Bungkus antarmuka utama dengan pengkondisian `@if($penempatan->isKelompok())`: render workspace kelompok terpadu persis Gambar 1 (Top Identity Bar Kelompok, Grid 2-Kolom Informasi Penempatan & Formulir Unggah Surat Selesai, dan Dedicated Members Panel). Jika individu, render antarmuka individu eksisting. |
| 2 | `resources/css/pages/admin/penempatan-detail.css` | **Modify** | Pastikan style identity bar, 2-column grid (`.focused-content-grid`), status box, dan dedicated table ter-import dan ter-render sempurna di halaman penempatan detail. |
| 3 | `resources/js/pages/admin/penempatan-show.js` | **Modify** | Pastikan listener custom file picker (`inputSuratSelesai` atau `file_surat_kel`) terhubung untuk pratinjau nama dan ukuran berkas PDF secara real-time. |
| 4 | `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` | **Modify** | Tambahkan pengujian asersi bahwa ketika membuka detail penempatan peserta kelompok, halaman me-render *Informasi Penempatan Kelompok*, *Formulir Unggah Surat Selesai*, dan *Daftar Anggota Kelompok*. |

---

## 5. 🛠️ RENCANA TAHAPAN EKSEKUSI (STEP-BY-STEP)

### Tahap 1: Struktur Blade Dinamis di `resources/views/admin/penempatan/show.blade.php`
1. Buka `resources/views/admin/penempatan/show.blade.php`.
2. Pada bagian `@section('content')`:
   ```blade
   @if($penempatan->isKelompok())
       {{-- =====================================================================
            TAMPILAN DETAIL PENEMPATAN KELOMPOK (PARITY WORKSPACE GAMBAR 1)
            ===================================================================== --}}
       @include('admin.penempatan.partials._show_kelompok', [
           'penempatan' => $penempatan,
           'kelompok' => $penempatan->pendaftaran?->kelompokMagang,
           'rekanList' => $penempatan->getRekanKelompokPenempatan(),
           ...
       ])
   @else
       {{-- =====================================================================
            TAMPILAN DETAIL PENEMPATAN INDIVIDU EKSISTING
            ===================================================================== --}}
       ... (Tampilan Dashboard Individu dengan 4 Card Metrik & Timeline) ...
   @endif
   ```
   *Tip Senior*: Kita dapat membuat partial khusus `_show_kelompok.blade.php` agar kode tetap modular, bersih, dan sangat mudah dirawat oleh junior developer.

### Tahap 2: Styling CSS di `resources/css/pages/admin/penempatan-detail.css`
- Pastikan class `.focused-content-grid`, `.focused-card`, `.info-card`, `.upload-card`, `.dedicated-members-panel`, dan `.dedicated-group-table` terdefinisi dan responsif pada mobile breakpoint.

### Tahap 3: Interaktivitas File Picker di `resources/js/pages/admin/penempatan-show.js`
- Pasang listener file input agar admin dapat memilih berkas PDF dan melihat nama serta ukuran berkas secara live sebelum klik tombol submit.

### Tahap 4: Quality Gate & Automated Testing
- Jalankan automated tests:
  `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=AdminSuratSelesaiIndividuKelompokTest`
  `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=Penempatan`
- Jalankan build Vite:
  `npm run build`
- Periksa status Git dan pastikan bebas error syntax.

---

## 6. ✅ DEFINITION OF DONE (DOD) & CHECKLIST

- [ ] Mengklik tombol `[ Detail ]` pada tabel anggota kelompok membuka halaman Detail Penempatan yang menampilkan tampilan kelompok persis seperti Gambar 1.
- [ ] Terdapat Top Identity Bar dengan nama kelompok, kampus, total anggota, bidang, dan badge status berkas.
- [ ] Terdapat Grid 2-Kolom Seimbang: Kolom Kiri Informasi Penempatan Kelompok + Box Status, Kolom Kanan Formulir Unggah Surat Selesai.
- [ ] Terdapat Tabel Daftar Anggota Kelompok di bawah Grid 2-Kolom dengan highlight `(Sedang Dilihat)` pada peserta yang sedang dibuka.
- [ ] Halaman Detail Penempatan untuk peserta individu mandiri tetap berjalan normal tanpa gangguan.
- [ ] Zero inline CSS / JavaScript di Blade.
- [ ] Seluruh test suite PHPUnit lolos 100% pada PHP 8.3.
- [ ] Vite asset compilation sukses tanpa error.
