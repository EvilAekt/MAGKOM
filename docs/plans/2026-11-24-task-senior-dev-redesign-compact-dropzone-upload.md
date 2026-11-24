# [PLAN] Redesign Kompak & Proporsional Area Drag-and-Drop Unggah Surat Selesai

> **Ticket ID**: `TASK-MAGKOM-SS-006`  
> **Status**: `PROPOSED - WAITING USER APPROVAL`  
> **Scope**: Redesain visual area dropzone unggah berkas surat selesai agar tidak terlalu besar (*too bulky*), proporsional, dan seimbang dengan card di sampingnya.  
> **Files Affected**:
> - `resources/css/pages/admin/surat-selesai-index.css`
> - `resources/views/admin/penempatan/partials/_show_kelompok.blade.php`
> - `resources/views/admin/surat-selesai/index.blade.php`

---

## 1. Analisis Masalah & Evaluasi Desain

### 1.1 Masalah pada Desain Saat Ini (Tangkapan Layar User)
Berdasarkan tangkapan layar yang dikirimkan oleh pengguna:
1. **Dashed Box Terlalu Besar / Dominan (*Too Bulky*)**:
   - Dashed box dropzone saat ini menggunakan `flex: 1` dengan `min-height: 190px` dan padding `26px 20px`, sehingga meregang terlalu tinggi dan mendominasi hampir seluruh card kanan.
2. **Terlalu Banyak Elemen Vertikal yang Bertumpuk**:
   - Di dalam satu kotak dashed terdapat 5 elemen yang ditumpuk dari atas ke bawah:
     1. Lingkaran icon besar (`52px x 52px`)
     2. Judul promosi (*"Pilih Berkas PDF Surat Selesai"*)
     3. Subteks format (*"Format berkas resmi .PDF..."*)
     4. Tombol pilihan file (*"Telusuri Berkas PDF"*)
     5. Kotak preview abu-abu (*"Belum ada berkas PDF dipilih..."*)
   - Akibatnya kotak dropzone tampak "tebal", memakan tempat secara masif, dan terkesan tidak efisien.

---

## 2. Solusi Desain yang Diusulkan: "Compact Sleek Dropzone with Document Guidelines"

### 2.1 Konsep Utama
1. **Rampingkan Dashed Dropzone Box**:
   - Kurangi tinggi dashed box secara signifikan: dari tinggi masif ~220px menjadi **tinggi kompak ~115px - 125px**.
   - Perkecil lingkaran icon dari `52px` menjadi `36px` yang minimalis dan manis.
   - Satukan teks ajakan menjadi 1-2 baris padat.
   - Format tombol dan preview menjadi horizontal/inline yang rapi.
2. **Tambahkan Box Panduan/Ketentuan Berkas Resmi (Pengisi Ruang yang Bermanfaat)**:
   - Alih-alih mengisi ruang kosong dengan memperbesar dashed box raksasa, kita masukkan elemen informasi bernilai: **Kotak Ketentuan Berkas Resmi** (*Document Guidelines Card*).
   - Memuat 3 poin ringkas:
     - ✓ Format PDF asli bertanda tangan & berstempel resmi dinas.
     - ✓ Ukuran file maksimal 2.0 MB.
     - ✓ Otomatis disinkronkan ke dashboard mentee/rekan tim.
3. **Keseimbangan Visual (Balanced Symmetry)**:
   - Card kiri memuat informasi teks + alert box status.
   - Card kanan memuat header + dashed dropzone ramping + kotak ketentuan berkas + tombol aksi Sleman Navy.
   - Kedua card memiliki tinggi yang seimbang dan tidak ada ruang kosong melompong maupun kotak upload yang terlalu besar.

---

## 3. Wireframe Perbandingan (Before vs After)

### 3.1 Before (Saat Ini - Dashed Box Terlalu Raksasa)
```
+-----------------------------------------------------------------------+
| Formulir Unggah Surat Selesai                                         |
| Unggah berkas resmi Surat Keterangan Selesai Magang PDF maks 2MB.     |
|                                                                       |
| + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - + |
| |                            [ ☁️ (52px) ]                          | |
| |                                                                   | |
| |                    Pilih Berkas PDF Surat Selesai                 | |
| |             Format berkas resmi .PDF • Ukuran maksimal 2.0 MB     | |
| |                                                                   | |
| |                       [ 📎 Telusuri Berkas PDF ]                  | |
| |                                                                   | |
| |             [ Belum ada berkas PDF dipilih (Maks 2.0 MB) ]        | |
| + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - + |
|                                                                       |
| [ ⭡ Unggah Surat Resmi (Sleman Navy)                                ] |
| (i) Berkas yang diunggah otomatis tersinkronisasi...                  |
+-----------------------------------------------------------------------+
```

### 3.2 After (Diusulkan - Dropzone Ramping + Ketentuan Berkas Elegan)
```
+-----------------------------------------------------------------------+
| Formulir Unggah Surat Selesai                                         |
| Workspace penerbitan surat keterangan selesai magang resmi.           |
|                                                                       |
| + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - + |
| |  [☁️] Tarik & Letakkan Berkas PDF di Sini                         | |
| |       atau [ 📎 Telusuri File ]  (Maks 2.0 MB)                     | |
| |       Status: Belum ada berkas PDF dipilih                        | |
| + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - + |
|                                                                       |
| Ketentuan Berkas Surat Selesai:                                       |
| • Format resmi PDF asli bertanda tangan atau stempel dinas            |
| • Ukuran file tidak melebihi 2.0 MB                                   |
| • Otomatis tersinkronisasi ke seluruh akun rombongan tim              |
|                                                                       |
| [ ⭡ Unggah Surat Resmi (Sleman Navy)                                ] |
+-----------------------------------------------------------------------+
```

---

## 4. Rincian Teknis Implementasi

### 4.1 Modifikasi CSS (`surat-selesai-index.css`)
1. **Rampingkan `.upload-dropzone-panel`**:
   - `padding: 14px 16px;` (sebelumnya `26px 20px`).
   - `min-height: 110px;` (sebelumnya `190px`).
   - `margin: 10px 0 12px;`
   - Border halus `1.5px dashed #CBD5E1`.
2. **Icon & Teks Ramping**:
   - `.dropzone-icon-circle`: ukuran `36px x 36px` dengan icon SVG `18px`.
   - `.dropzone-prompt-title`: font-size `0.875rem`, font-weight `700`.
   - Tombol `.btn-custom-file-picker`: padding `6px 14px`, font-size `0.785rem`.
3. **Komponen Baru `.upload-guidelines-box`**:
   - Background `#F8FAFC`, border `1px solid #E2E8F0`, border-radius `8px`, padding: `10px 14px`.
   - Menampilkan 3 item ceklis ketentuan file resmi.
4. **Tombol Submit & Notice**:
   - Tombol submit Sleman Navy full width dengan padding `9px 16px`.

### 4.2 Modifikasi Blade View
- Perbarui markup di:
  1. `resources/views/admin/penempatan/partials/_show_kelompok.blade.php`
  2. `resources/views/admin/surat-selesai/index.blade.php` (mode fokus individu & kelompok)
- Tetap mematuhi aturan strict Blade:
  - **Zero `<style>` / Zero `<script>`** di file Blade.
  - Menggunakan class CSS terdaftar di Vite.

---

## 5. Rencana Pengujian & Quality Gate

1. **Visual Testing**:
   - Memastikan tinggi card kanan sejajar dan proporsional dengan card kiri pada layar desktop (`1440px`).
   - Memastikan responsive layout aman pada tampilan tablet (`768px`) dan mobile (`375px`).
2. **Feature Tests**:
   - Menjalankan `php artisan test tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` untuk memastikan upload form dan parsing PDF tetap berjalan sempurna.
3. **Asset Compilation**:
   - `npm run build` untuk memverifikasi bundle CSS dan JS bebas error.
4. **Code Style**:
   - `./vendor/bin/pint --test`.

---

## 6. Definition of Done (DoD)

- [x] Ukuran dashed drag-and-drop box berkurang sekitar 40-50% (tinggi ~115px) sehingga tidak lagi terasa terlalu besar.
- [x] Elemen icon dan tombol dirampingkan secara proporsional.
- [x] Tersedia box ketentuan berkas yang informatif untuk menjaga simetri tinggi dengan card kiri.
- [x] Validasi file picker (PDF maks 2MB) dan submit form tetap berjalan normal.
- [x] Semua test PHPUnit 100% pass di PHP 8.3.
- [x] `npm run build` berhasil tanpa error.
