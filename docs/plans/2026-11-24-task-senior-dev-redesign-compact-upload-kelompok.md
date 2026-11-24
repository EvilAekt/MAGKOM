# TASK & PRD IMPLEMENTATION PLAN: REDESIGN & KOMPAK-ISASI AREA UNGGAH SURAT SELESAI KELOMPOK

> **Dari:** Senior Software Architect / Lead Developer  
> **Kepada:** Junior Developer / AI Implementation Engineer (Gemini 3.8 Flash Compatible)  
> **Target Repositori:** `MAGKOM` (Portal Manajemen Magang Diskominfo Kabupaten Sleman)  
> **Status Dokumen:** READY FOR REVIEW & USER APPROVAL  
> **Tingkat Prioritas:** Medium-High (UI/UX Refinement: Ergonomi & Proporsi Visual)  
> **Tanggal Dokumen:** 24 November 2026  
> **Referensi Gambar User:**
> - Screenshot Unggahan User: `media_1795508478192_31d4b524.jpg` (Tampilan form upload kelompok saat ini yang memakan ruang vertikal terlalu besar dan terkesan kosong)

---

## 1. 🔍 ROOT CAUSE ANALYSIS: MENGAPA TAMPILAN SAAT INI TERASA "KEBESARAN" & KURANG RAPI?

Berdasarkan screenshot aktual yang dikirimkan oleh user (`media_1795508478192_31d4b524.jpg`), ditemukan beberapa masalah visual utama:

```
MASALAH 1: Redundansi Tombol Kembali
[ Hero Banner ] ----> Memiliki tombol "< Kembali ke Daftar Kelompok"
[ Identity Bar ] ---> Mengulang tombol "<" chevron dan mengulang nama kelompok lagi!

MASALAH 2: Oversized & Hollow Dropzone
+---------------------------------------------------------------------------------------------------+
|  Unggah Surat Tim (1x Untuk Semua Anggota)                                                        |
|  +---------------------------------------------------------------------------------------------+  |
|  |                                                                                             |  |
|  |                                            [ ^ ]                                            |  |  <--- 250px+ tinggi
|  |                                  Pilih Berkas PDF Surat Tim                                 |  |       hanya untuk 1
|  |                                      Format .PDF Maks 2MB                                   |  |       file input!
|  |                                    [ Choose File ] No file chosen                           |  |
|  |                                                                                             |  |
|  +---------------------------------------------------------------------------------------------+  |
|                                                     [ 📤 Unggah Surat Tim (Broadcast Otomatis) ]  |
+---------------------------------------------------------------------------------------------------+

MASALAH 3: Pushing Table Below the Fold
Tabel Daftar Anggota Kelompok terdorong sangat jauh ke bawah lipatan layar (below the fold),
sehingga admin harus scroll ke bawah hanya untuk melihat siapa saja anggota kelompok tersebut.
```

### 1.1 Rincian Poin Evaluasi
1. **Dashed Dropzone Terlalu Lebar & Kosong**:
   - Kotak putus-putus (`.upload-dropzone`) membentang 100% selebar monitor (1200px+) dengan tinggi vertikal berlebihan (~220px).
   - Di dalamnya hanya terdapat satu tombol file bawaan browser yang kaku (`Choose File No file chosen`) di tengah-tengah ruang kosong raksasa.
   - Untuk skenario staf admin dinas yang mengunggah 1 file PDF surat selesai, kotak sebesar ini tidak proporsional dan tidak ergonomis.
2. **Redundansi Header & Tombol Navigasi**:
   - Hero banner di atas sudah memiliki judul dan tombol `< Kembali ke Daftar Kelompok`.
   - Tepat di bawahnya, terdapat `focused-identity-bar` yang kembali memuat tombol `<` chevron dan nama kelompok yang sama persis.
3. **Tombol Aksi Terpisah di Pojok Kanan Bawah**:
   - Tombol "Unggah Surat Tim (Broadcast Otomatis)" berada di luar kotak dropzone di pojok kanan bawah, menciptakan pemisahan visual yang canggung antara area pilih file dan tombol submit.

---

## 2. 🎯 TUJUAN & STRATEGI REDESIGN (OBJECTIVES)

1. **Memangkas Ketinggian Vertikal (Compact Ergonomics)**:
   - Mengurangi tinggi area formulir unggah dari **~260px** menjadi hanya **~80px – 110px** (hemat ruang lebih dari 60%).
   - Memastikan **Tabel Anggota Kelompok** langsung terlihat di layar utama (*above the fold*) tanpa perlu scrolling panjang.
2. **Menghilangkan Redundansi**:
   - Menghapus tombol chevron `<` yang ganda pada `focused-identity-bar` karena tombol kembali utama sudah ada di Hero Banner (atau menyatukannya secara elegan).
3. **Tampilan File Picker Modern & Rapi (Custom Styled)**:
   - Menggantikan tampilan default browser `Choose File` dengan komponen input modern:
     - Tombol file picker bergaya Sleman Navy (`Pilih Berkas PDF`).
     - Indikator nama file terpilih yang terintegrasi langsung di sampingnya.
     - Tombol Unggah / Submit yang berdampingan secara harmonis.
4. **Desain Selaras dengan Individu**:
   - Menerapkan proporsi kompak yang sama ke mode individu agar konsistensi visual di seluruh modul tetap terjaga.

---

## 3. 🎨 DUA OPSI USULAN DESAIN BARU (PILIHAN)

### Opsi A (Rekomendasi Utama): Compact Horizontal Action Bar
Area status dan formulir unggah disatukan menjadi satu bar horizontal yang ramping, modern, dan sangat efisien di atas tabel anggota:

```
+---------------------------------------------------------------------------------------------------+
| [Hero Banner Sleman Navy - Unggah Surat Selesai: Tim CSIRT & Keamanan Siber Sleman]   [< Kembali] |
+---------------------------------------------------------------------------------------------------+
| [Icon] Tim CSIRT & Keamanan Siber Sleman • UNY • 3 Anggota • Bidang ITI      [ ⚠️ BELUM DIUNGGAH ] |
+---------------------------------------------------------------------------------------------------+
| 📤 UNGGAH SURAT KETERANGAN SELESAI MAGANG TIM (1x Untuk Semua Anggota)                            |
| 1 kali unggah otomatis terdistribusi ke masing-masing dashboard anggota di bawah.                 |
|                                                                                                   |
| [ 📎 Pilih Berkas PDF ] [ surat_selesai_tim_csirt.pdf (420 KB) ]       [ 📤 Unggah Surat Tim ]    |
+---------------------------------------------------------------------------------------------------+
| DAFTAR ANGGOTA KELOMPOK: TIM CSIRT & KEAMANAN SIBER SLEMAN (3 ORANG)           (Langsung Terlihat)|
| +----+-----------------------+------------------+-------------------+-------------------+-----+   |
| | NO | NAMA ANGGOTA          | PERAN TIM        | NIM / INSTITUSI   | STATUS SURAT TIM  | AKSI|   |
| +----+-----------------------+------------------+-------------------+-------------------+-----+   |
| | 1  | Farhan Aditya Pratama | [Ketua Kelompok] | 21520244001 • UNY | Belum Diterbitkan | [...]   |
| | 2  | Maya Safira Kusuma    | [Anggota]        | 21520244002 • UNY | Belum Diterbitkan | [...]   |
+---------------------------------------------------------------------------------------------------+
```
*Keunggulan Opsi A*:
- Sangat rapi, hemat ruang secara drastis, alur baca natural (kiri ke kanan).
- Tabel anggota langsung terlihat jelas begitu halaman dibuka.

---

### Opsi B: Split 2-Kolom Seimbang (Side-by-Side Card)
Membagi area kerja menjadi 2 card proporsional:
- **Kolom Kiri (40%)**: Status surat tim saat ini + tombol unduh jika sudah terbit + info jumlah anggota.
- **Kolom Kanan (60%)**: Card upload berukuran sedang dengan box dropzone mini yang proporsional.

```
+---------------------------------------------------------------------------------------------------+
| [Hero Banner Sleman Navy - Unggah Surat Selesai: Tim CSIRT & Keamanan Siber Sleman]   [< Kembali] |
+---------------------------------------------------------------------------------------------------+
| [Icon] Tim CSIRT & Keamanan Siber Sleman • UNY • 3 Anggota • Bidang ITI                           |
+--------------------------------------------------+------------------------------------------------+
| STATUS SURAT TIM SAAT INI                        | FORMULIR UNGGAH BERKAS TIM                     |
| Status: [ ⚠️ BELUM DIUNGGAH ]                     | Format .PDF Maks 2.0 MB • Broadcast Otomatis   |
| 1x unggah otomatis berlaku untuk seluruh         |                                                |
| 3 anggota tim di bawah.                          | [ 📎 Pilih PDF ] Belum ada file dipilih        |
|                                                  | [ 📤 Simpan & Unggah Surat Tim ]               |
+--------------------------------------------------+------------------------------------------------+
| DAFTAR ANGGOTA KELOMPOK: TIM CSIRT & KEAMANAN SIBER SLEMAN (3 ORANG)                              |
| (Tabel anggota kelompok tetap terisolasi dan rapi)                                                |
+---------------------------------------------------------------------------------------------------+
```

---

## 4. 📁 MATRIKS PERUBAHAN TEKNIS BERKAS

| No | File Path | Bagian yang Dimodifikasi | Detail Perubahan |
| :--- | :--- | :--- | :--- |
| 1 | `resources/views/admin/surat-selesai/index.blade.php` | Section `3.B (Tier 2 Kelompok)` & `2.B (Tier 2 Individu)` | - Hapus tombol back duplikat pada `focused-identity-bar`.<br/>- Ganti container `.dedicated-upload-card` dan `.upload-dropzone` raksasa dengan komponen `.compact-upload-strip` horizontal.<br/>- Buat label custom file picker yang rapi berdampingan dengan nama file dan tombol submit. |
| 2 | `resources/css/pages/admin/surat-selesai-index.css` | Class `.compact-upload-strip`, `.upload-inline-form`, dll. | - Gantikan rule dropzone 250px dengan layout flex horizontal padding seimbang (16px 20px).<br/>- Tambahkan style tombol custom file picker dan status indicator yang compact.<br/>- Sempurnakan responsiveness pada layar tablet/mobile. |
| 3 | `resources/js/pages/admin/surat-selesai-index.js` | Fungsi `initFileInputPreview()` | - Hubungkan event change file input ke container inline preview nama file agar tampil elegan di samping tombol upload. |
| 4 | `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` | Test Assertion | - Pastikan seluruh test case fungsional tetap PASS 100% tanpa ada perubahan kontrak backend. |

---

## 5. 🛠️ RENCANA TAHAPAN EKSEKUSI (STEP-BY-STEP)

### Tahap 1: Refactoring Markup Blade (`index.blade.php`)
1. Bersihkan tombol chevron back `<` di dalam `.focused-identity-bar` (karena tombol kembali sudah jelas dan menonjol di Hero Banner).
2. Ubah blok form upload kelompok:
   - Sediakan judul kecil yang elegan di kiri: *"Unggah Berkas Surat Tim"* & status saat ini.
   - Sediakan baris form inline:
     ```html
     <div class="compact-upload-bar">
         <div class="compact-upload-info">
             <span class="compact-upload-title">Unggah Surat Tim (1x Untuk Semua Anggota)</span>
             <span class="compact-upload-hint">Format PDF Maks 2.0 MB • Otomatis tersinkronisasi ke seluruh anggota</span>
         </div>
         <div class="compact-upload-controls">
             <label for="file_surat_kel" class="btn-custom-file-picker">
                 <svg ...>...</svg>
                 <span>Pilih Berkas PDF</span>
             </label>
             <input type="file" name="file_surat" id="file_surat_kel" accept="application/pdf" required class="d-none">
             <span id="previewFileNameKel" class="compact-file-name-preview">Belum ada berkas dipilih</span>
             <button type="submit" class="btn-compact-submit">
                 <svg ...>...</svg>
                 <span>Unggah Tim</span>
             </button>
         </div>
     </div>
     ```

### Tahap 2: Styling CSS Modern di `surat-selesai-index.css`
1. Hapus style dropzone raksasa (`.upload-dropzone` tinggi 220px).
2. Tambahkan class:
   - `.compact-upload-card`: Background `#FFFFFF`, border `1.5px solid #E2E8F0`, padding `16px 20px`, border-radius `12px`.
   - `.compact-upload-controls`: Flex container `display: flex; align-items: center; gap: 12px;`.
   - `.btn-custom-file-picker`: Tombol outline navy yang rapi dengan cursor pointer.
   - `.compact-file-name-preview`: Badge teks nama file berlatar `#EFF6FF` dengan batas max-width dan text-overflow ellipsis agar tidak merusak layout jika nama file panjang.
   - `.btn-compact-submit`: Tombol primary Sleman Navy yang padat dan tegas.

### Tahap 3: Pembaruan Interaksi JavaScript
- Pastikan saat user memilih file, `#previewFileNameKel` langsung berubah menampilkan icon `📄` + nama file + ukuran (misal: `surat_tim_csirt.pdf (450 KB)`).
- Jika file melebihi 2MB, teks preview otomatis berwarna merah dengan pesan peringatan yang ramah.

### Tahap 4: Quality Gate & Testing
- Jalankan PHPUnit test suite dengan PHP 8.3:
  `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=SuratSelesai`
- Jalankan formatting check:
  `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/pint --test`
- Jalankan kompilasi aset:
  `npm run build`

---

## 6. ✅ DEFINITION OF DONE (DOD) & CHECKLIST

- [ ] Area form unggah surat tim berukuran kompak (tinggi < 110px, tidak lagi berwujud dropzone raksasa kosong).
- [ ] Tombol kembali redundan dihilangkan dari identity bar (cukup satu tombol kembali yang konsisten di Hero Banner).
- [ ] Tombol file picker bergaya modern khas Sleman Navy (bukan button default browser).
- [ ] Nama file dan ukuran tampil rapi secara instan saat berkas dipilih.
- [ ] Tabel anggota kelompok langsung terlihat tanpa perlu scroll ke bawah layar monitor standar.
- [ ] Kepatuhan strict Blade: Nol inline CSS/JS.
- [ ] Seluruh 13 unit/feature test lolos pengujian tanpa regresi.
