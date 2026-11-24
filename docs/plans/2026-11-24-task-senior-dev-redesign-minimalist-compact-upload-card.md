# [TASK / PRD SENIOR DEV] Redesain Minimalis & Kompak Formulir Unggah Surat Selesai (To-The-Point & Compact Card)

> **Ticket ID**: `TASK-MAGKOM-SS-007`  
> **Author**: Senior Lead Developer (Antigravity)  
> **Assignee**: Junior Full-Stack Developer / Autonomous Coding AI (Gemini 3.8 Flash)  
> **Status**: `COMPLETED & VERIFIED`  
> **Target Release**: Sprint MagKom v1.1.5  
> **Files Affected**:
> - `resources/css/pages/admin/surat-selesai-index.css`
> - `resources/views/admin/penempatan/partials/_show_kelompok.blade.php`
> - `resources/views/admin/surat-selesai/index.blade.php`
> - `resources/js/pages/admin/penempatan-show.js`
> - `resources/js/pages/admin/surat-selesai-index.js`
> - `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php`

---

## 1. Tujuan & Deskripsi Tugas

### 1.1 Kebutuhan Pengguna (User Request Analysis)
Pengguna meminta perampingan radikal pada card **Formulir Unggah Surat Selesai**:
1. **Hapus Elemen-Elemen Non-Esensial**:
   - ❌ Hapus kotak teks preview abu-abu statis: `"Belum ada berkas PDF dipilih (Maks 2.0 MB)"`.
   - ❌ Hapus box `"Ketentuan Berkas Resmi:"` beserta 3 butir daftarnya.
   - ❌ Hapus alert notifikasi baris bawah: `"Berkas otomatis tersinkronisasi ke akun peserta ini dan tim Tim CSIRT & Keamanan Siber Sleman."`.
2. **Karakteristik Tampilan Baru**:
   - Card formulir unggah **dikecilkan ukurannya (*compact height*)**, TIDAK dipaksa meregang penuh setinggi layout sampingnya (*ga full card*).
   - Isinya fokus dan *to the point*: hanya judul formulir, area pilih/telusuri file PDF, dan tombol `[ ⭡ Unggah Surat Resmi ]`.

---

## 2. Perbandingan Wireframe (Before vs After)

### 2.1 Kondisi Saat Ini (Before - Bulky & Penuh Teks Tambahan)
```
+-------------------------------------------------------------------------------+
| Formulir Unggah Surat Selesai                                                 |
| Unggah berkas resmi Surat Keterangan Selesai Magang format PDF maks 2MB.      |
|                                                                               |
| + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - + |
| |                         [ ☁️ ]                                             | |
| |              Pilih Berkas PDF Surat Selesai                               | |
| |         Format berkas resmi .PDF • Ukuran maksimal 2.0 MB                 | |
| |                   [ 📎 Telusuri Berkas PDF ]                              | |
| |           [ Belum ada berkas PDF dipilih (Maks 2.0 MB) ] ❌ (HAPUS)        | |
| + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - + |
|                                                                               |
| ❌ (HAPUS):                                                                    |
| KETENTUAN BERKAS RESMI:                                                       |
| • Dokumen format PDF asli bertanda tangan atau stempel dinas                  |
| • Ukuran file maksimal 2.0 MB agar mudah diunduh mentee                       |
| • Otomatis terdistribusi ke akun peserta dan rombongan tim                    |
|                                                                               |
| [ ⭡ Unggah Surat Resmi (Sleman Navy)                                        ] |
|                                                                               |
| ❌ (HAPUS): (i) Berkas otomatis tersinkronisasi ke akun peserta ini...        |
+-------------------------------------------------------------------------------+
```

### 2.2 Desain Baru yang Diinginkan (After - Super Clean & Compact Card)
```
+-------------------------------------------------------------------------------+
| Formulir Unggah Surat Selesai                                                 |
| Unggah berkas resmi format PDF (Maks. 2.0 MB).                                |
|                                                                               |
| + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - + |
| |                           [ ☁️ ]                                           | |
| |                Pilih Berkas PDF Surat Selesai                             | |
| |                     [ 📎 Telusuri Berkas PDF ]                            | |
| |            (File name badge dinamis muncul HANYA jika file dipilih)       | |
| + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - + |
|                                                                               |
| [ ⭡ Unggah Surat Resmi (Sleman Navy)                                        ] |
+-------------------------------------------------------------------------------+
 (Card berakhir di sini — tinggi alami, ramping, dan bebas visual clutter)
```

---

## 3. Evaluasi Pendekatan Teknis (Architectural Options)

| Kriteria | Opsi A: `align-items: start` + Minimalist Card (Direkomendasikan) | Opsi B: Full Height Stretch dengan Ruang Kosong |
| :--- | :--- | :--- |
| **Tinggi Card** | **Alami & Ramping**: Card hanya setinggi kontennya (~190px–220px). | Dipaksa setinggi card info kiri (~400px). |
| **Kesesuaian Permintaan** | **100% Sesuai**: Pengguna secara eksplisit meminta *"di kecilin aja ga full cart kayak yg tadi"*. | Bertentangan dengan instruksi pengguna karena tetap full card. |
| **Kebersihan Visual** | **Sangat Bersih**: Zero elemen pengisi palsu, zero teks redundan. | Meninggalkan ruang kosong putih yang canggung di bagian bawah. |
| **Responsivitas** | Sangat fleksibel di desktop maupun mobile. | Kaku pada resolusi menengah. |

**Keputusan Arsitektur**: Memilih **Opsi A**. Pada grid 2-kolom (`.focused-content-grid`), atur `align-items: start;` sehingga card formulir unggah tidak dipaksa meregang penuh.

---

## 4. Rincian Perubahan File & Komponen

### 4.1 CSS (`resources/css/pages/admin/surat-selesai-index.css`)
1. **Grid Alignment**:
   ```css
   .focused-content-grid {
       display: grid;
       grid-template-columns: 1fr 1.15fr;
       gap: 1.25rem;
       align-items: start; /* Card kanan tidak lagi dipaksa setinggi card kiri */
   }
   ```
2. **Card Unggah Kompak**:
   - `.focused-card.upload-card`: Hilangkan `height: 100%` dan `min-height: 420px`. Card menggunakan tinggi alami (`height: auto`).
   - `.upload-dropzone-panel`: Padding ringkas `18px 16px; margin: 10px 0 14px;`, border `1.5px dashed #CBD5E1`.
   - `.dropzone-icon-circle`: Ukuran `36px x 36px` dengan icon SVG `18px`.
   - `.dropzone-prompt-title`: Ukuran `0.875rem; font-weight: 700; margin: 0 0 10px;`.
   - Hapus class styling yang tidak lagi dipakai (`.upload-guidelines-box`, `.guidelines-title`, `.guidelines-list`, `.guidelines-item`, `.upload-sync-notice`).
3. **Dynamic File Name Chip**:
   - Wadah nama berkas `#previewFileNameKelShow` secara default disembunyikan (`display: none;`).
   - Ketika admin memilih file PDF, wadah ini muncul sebagai chip hijau elegan di bawah tombol telusuri: `display: inline-flex; background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; font-size: 0.775rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;`.

### 4.2 Blade Views
1. **`resources/views/admin/penempatan/partials/_show_kelompok.blade.php`**:
   - Hapus wadah statis abu-abu `previewFileNameKelShow` yang berisi `"Belum ada berkas PDF dipilih"`. Ganti dengan chip dinamis yang tersembunyi jika belum ada file.
   - Hapus blok markup `<div class="upload-guidelines-box">...</div>`.
   - Hapus blok markup `<div class="upload-sync-notice">...</div>`.
   - Sisakan hanya: Header card, dropzone box dengan tombol telusuri, dan tombol submit `Unggah Surat Resmi`.
2. **`resources/views/admin/surat-selesai/index.blade.php`**:
   - Terapkan perampingan yang sama persis pada mode fokus individu (lines 255-295) dan mode fokus kelompok (lines 580-620).

### 4.3 JavaScript Handlers
- Update `setupFilePicker` di `penempatan-show.js` dan `surat-selesai-index.js`:
  - Jika file dipilih: tampilkan chip nama file dengan indikator centang hijau.
  - Jika reset/kosong: sembunyikan kembali chip (`display: none`).

---

## 5. Rencana Langkah Implementasi (Checklist)

- [x] **Step 1**: Perbarui CSS di `surat-selesai-index.css` (`align-items: start;` pada `.focused-content-grid`, hilangkan `height: 100%` pada `.upload-card`, rapikan dropzone).
- [x] **Step 2**: Modifikasi `_show_kelompok.blade.php` (hapus teks ketentuan, preview statis kosong, dan notice baris bawah).
- [x] **Step 3**: Modifikasi `surat-selesai/index.blade.php` pada tab individu dan tab kelompok agar seragam minimalis.
- [x] **Step 4**: Sesuaikan handler JS di `penempatan-show.js` dan `surat-selesai-index.js` agar file preview chip bersifat dinamis.
- [x] **Step 5**: Jalankan `npm run build` untuk kompilasi aset Vite.
- [x] **Step 6**: Jalankan test otomatis dengan PHP 8.3 (`php artisan test tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php`).
- [x] **Step 7**: Jalankan `./vendor/bin/pint --test` untuk verifikasi format kode.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Card *Formulir Unggah Surat Selesai* berukuran kompak sesuai isi, tidak lagi meregang penuh setinggi layout samping.
- [x] Teks preview statis *"Belum ada berkas PDF dipilih (Maks 2.0 MB)"* telah dihapus.
- [x] Box *"Ketentuan Berkas Resmi"* dan poin-poinnya telah dihapus seluruhnya.
- [x] Notifikasi *"Berkas otomatis tersinkronisasi..."* telah dihapus.
- [x] Form hanya menyisakan header, area pilih berkas PDF yang rapi, dan tombol `[ ⭡ Unggah Surat Resmi ]`.
- [x] Validasi PDF dan fungsi upload tetap berjalan 100% normal.
- [x] Tidak ada tag `<style>` atau `<script>` yang dimasukkan ke Blade.
- [x] Seluruh automated test lulus (100% PASS) di PHP 8.3.
- [x] `npm run build` sukses tanpa error.
