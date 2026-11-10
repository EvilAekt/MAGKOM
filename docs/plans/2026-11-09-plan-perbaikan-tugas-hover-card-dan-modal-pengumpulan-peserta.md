# Rencana Kerja: Perbaikan Hover Tab Filter, Kompaksi Card Tugas, Penguncian Pengumpulan Tugas, dan Penyelarasan Modal Pop-up Peserta

Dokumen ini berisi spesifikasi teknis dan rencana implementasi terstruktur untuk memenuhi permintaan penyesuaian pada modul **Tugas & Progres Magang** peserta:
1. Menghilangkan efek hover yang terpotong pada tab filter (*Semua Tugas*, *Selesai*, *Lewat Tenggat*).
2. Menyesuaikan proporsi dan ukuran kartu penugasan (*assignment card*) agar lebih kompak dan proporsional.
3. Mengunci formulir pengumpulan tugas setelah peserta menyerahkan tugas (mencegah penyerahan berulang jika belum ada revisi).
4. Menyelaraskan tampilan pop-up modal tambah tautan (*link*) dan unggah berkas (*file*) peserta agar identik dengan standar Design System MagKom di sisi Pembimbing.

---

## 1. Tujuan
- Menyediakan pengalaman antarmuka yang mulus tanpa *clipping* visual pada tab filter segmented penugasan.
- Menghadirkan tata letak daftar tugas yang padat, proporsional, dan nyaman dibaca (*compact assignment cards*).
- Menegakkan integritas alur pengerjaan tugas: sekali peserta menyerahkan progres tugas, status berubah menjadi *Diserahkan* dan tombol penyerahan dikunci hingga pembimbing memberikan review/revisi.
- Menstandardisasi pop-up modal pengumpulan tugas (tautan & file) dengan bahasa Indonesia formal dan desain yang konsisten dengan modul Pembimbing.

---

## 2. Asumsi & Pertanyaan Terbuka
### Asumsi Kerja:
1. **Penguncian Pengumpulan Tugas**:
   - Jika peserta sudah mengumpulkan tugas minimal 1 kali (`$tugas->progresLogs->isNotEmpty()`) dan status tugas belum `selesai`, maka status tugas adalah **Diserahkan (Menunggu Review)**.
   - Selama status review menunggu (`StatusReviewProgresEnum::Menunggu`), peserta **tidak dapat** menyerahkan tugas lagi.
   - Peserta hanya diperbolehkan mengumpulkan ulang jika pembimbing memberikan status review **Perlu Revisi** (`StatusReviewProgresEnum::PerluRevisi`).
   - Jika status tugas sudah **Selesai** (`StatusTugasEnum::Selesai`), seluruh aksi penyerahan ditutup permanen.
2. **Kompaksi Ukuran Kartu**:
   - Kartu penugasan dikurangi padding-nya dari `20px 24px` menjadi `14px 18px`.
   - Ukuran ikon status disesuaikan dari `44px` menjadi `36px` dengan ikon `18px`.
   - Skala tipografi judul tugas disesuaikan dari `1.0625rem` menjadi `0.95rem` (700 bold).
3. **Penyelarasan Modal Pop-up**:
   - Modal peserta diselaraskan dengan arsitektur modal pembimbing di `resources/views/pembimbing/tugas/create.blade.php`:
     - Modal Link: Header *"Tambahkan link"*, field filled dengan label *"Link*"*, tombol *"Batal"* & *"Tambahkan link"*.
     - Modal File: Header *"Upload file"*, dropzone dengan teks bahasa Indonesia *"Pilih Berkas / Browse"* dan *"atau seret file ke sini untuk mengupload"*, tombol *"Batal"* & *"Upload"*.

---

## 3. Pendekatan Teknis

### A. Solusi Hover Terpotong pada Tab Filter
- **Penyebab**: `.classroom-tabs` memiliki `overflow-x: auto` dengan padding atas 0px. Ketika `.tab-pill` mengalami hover dengan `transform: translateY(-1px)` dan `box-shadow`, batas container langsung memotong sisi atas dan bayangan tab.
- **Solusi**:
  - Berikan ruang padding vertikal pada `.classroom-tabs`: `padding: 6px 4px 10px 4px;` dan kompensasi margin `margin: -4px -4px 18px -4px;`.
  - Pastikan `.tab-pill` memiliki `will-change: transform;` dan ruang isolasi bayangan yang cukup.

### B. Solusi Kompaksi Card Tugas
- Memperbarui `resources/css/pages/peserta/tugas-index.css`:
  - Mengurangi padding `.assignment-card`: `14px 18px`.
  - Mengecilkan wrapper ikon `.assignment-icon-wrapper`: `36px x 36px` (border-radius 10px atau lingkaran 50%).
  - Mengatur ukuran font `.assignment-title`: `0.95rem` (15.2px) dengan line-height `1.35`.
  - Mengatur `.assignment-desc`: `0.8125rem` (13px), margin-bottom `8px`.
  - Mengatur `.assignment-footer`: `padding-top: 8px`.

### C. Solusi Penguncian Pengumpulan Tugas
- **Frontend (`resources/views/peserta/tugas/show.blade.php`)**:
  - Evaluasi variabel state:
    ```php
    $hasSubmitted = $tugas->progresLogs->isNotEmpty();
    $latestReviewStatus = $latestProgres?->status_review?->value ?? (string)$latestProgres?->status_review;
    $bisaKumpulLagi = !$isSelesai && (!$hasSubmitted || $latestReviewStatus === 'perlu_revisi');
    ```
  - Jika `$bisaKumpulLagi === false`:
    - Sembunyikan tombol dropdown `+ Tambah berkas` dan tombol `Serahkan / Kirim Progres`.
    - Tampilkan kartu status penyerahan:
      - Bila menunggu review: Alert info berlatar `#EFF6FF` dengan border `#BFDBFE` dan teks biru navy: *"✓ Tugas telah Anda serahkan pada [Tanggal]. Menunggu peninjauan dari pembimbing lapangan."*
      - Bila sudah selesai: Alert sukses berlatar `#ECFDF5` dengan border `#A7F3D0` dan teks hijau: *"✓ Tugas telah ditinjau dan dinyatakan selesai oleh pembimbing."*
- **Backend Controller (`app/Http/Controllers/Peserta/TugasController.php`)**:
  - Pada method `unggahProgres()`:
    - Cek apakah tugas sudah berstatus selesai atau masih menunggu review tanpa permintaan revisi:
    - Jika ya, tolak penyerahan ganda dan kembalikan response error / redirect back: *"Tugas ini sudah diserahkan dan sedang menunggu review dari pembimbing."*

### D. Penyelarasan Pop-up Modal Upload File & Tambah Link
- Memperbarui markup modal di `resources/views/peserta/tugas/show.blade.php`:
  1. **Modal Tambah Link**:
     - Mengadopsi kelas `.tugas-dialog--link` dan `.tugas-field-filled`.
     - Teks bahasa Indonesia: *"Tambahkan link"*, placeholder *"https://drive.google.com/..."*, tombol *"Batal"* dan *"Tambahkan link"*.
  2. **Modal Upload File**:
     - Mengadopsi dropzone bergaya pembimbing dengan teks *"Pilih Berkas"* dan *"atau seret file ke sini untuk mengunggah"*.
     - Area pratinjau file terpilih yang ringkas dengan nama, ukuran, dan tombol hapus merah.
     - Tombol aksi *"Batal"* dan *"Upload"*.
- Memperbarui stylesheet `resources/css/pages/peserta/tugas-detail.css` dan modul JavaScript `resources/js/pages/peserta/tugas-detail.js`.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/peserta/tugas-index.css` | **Modify** | Perbaiki padding container tab filter agar hover tidak terpotong; kompaksi padding, ikon, tipografi, dan margin `.assignment-card`. |
| `resources/views/peserta/tugas/index.blade.php` | **Modify** | Sesuaikan ukuran ikon SVG assignment card (`18px`) dan struktur badge yang kompak. |
| `app/Http/Controllers/Peserta/TugasController.php` | **Modify** | Validasi backend: cegah pengumpulan progres berulang saat tugas sudah diserahkan dan belum berstatus perlu revisi / sudah selesai. |
| `resources/views/peserta/tugas/show.blade.php` | **Modify** | Sembunyikan tombol serah jika tugas sudah diserahkan; tampilkan banner status diserahkan; selaraskan modal link & file dengan UI pembimbing. |
| `resources/css/pages/peserta/tugas-detail.css` | **Modify** | Terapkan styling dialog modal pembimbing (`.tugas-dialog`, `.tugas-field-filled`, tombol MagKom navy/gold). |
| `resources/js/pages/peserta/tugas-detail.js` | **Modify** | Sinkronisasi event listener dan validasi form modal baru (link & upload file). |
| `tests/Feature/PesertaFullPortalFeatureTest.php` | **Modify** | Tambah skenario feature test: verifikasi tombol serah terkunci setelah tugas diserahkan dan penolakan pengumpulan ganda. |

---

## 5. Langkah Implementasi Bertahap

### Fase 1: Perbaikan Hover Tab Filter & Kompaksi Assignment Card
- [ ] 1.1 Buka `resources/css/pages/peserta/tugas-index.css` dan sesuaikan `.classroom-tabs` dengan padding vertikal `padding: 6px 4px 10px 4px;` serta kompensasi margin untuk mencegah *clipping*.
- [ ] 1.2 Sesuaikan ukuran `.assignment-card`: padding `14px 18px`, ikon wrapper `36px x 36px`, judul `0.95rem`, deskripsi `0.8125rem`, dan footer `8px`.
- [ ] 1.3 Periksa `resources/views/peserta/tugas/index.blade.php` untuk memastikan ikon SVG berukuran `18px x 18px`.

### Fase 2: Logika Penguncian Pengumpulan Tugas
- [ ] 2.1 Buka `app/Http/Controllers/Peserta/TugasController.php`, tambahkan pengecekan pada method `unggahProgres()`:
  - Jika tugas sudah `selesai`, tolak penyerahan.
  - Jika tugas sudah memiliki `progresLogs` dengan status review `menunggu`, tolak penyerahan berulang.
- [ ] 2.2 Buka `resources/views/peserta/tugas/show.blade.php`:
  - Definisikan `$bisaKumpulLagi`.
  - Jika `$bisaKumpulLagi` bernilai false, sembunyikan dropdown `+ Tambah berkas` dan tombol `Serahkan`.
  - Render banner status: *"Tugas telah diserahkan dan menunggu peninjauan pembimbing"*.

### Fase 3: Penyelarasan Modal Pop-up Link & File
- [ ] 3.1 Perbarui struktur modal link di `resources/views/peserta/tugas/show.blade.php` mengikuti desain pembimbing (`.tugas-dialog--link`, input filled, teks bahasa Indonesia).
- [ ] 3.2 Perbarui struktur modal file di `resources/views/peserta/tugas/show.blade.php` dengan dropzone bersih, preview file terlampir, dan tombol bahasa Indonesia.
- [ ] 3.3 Tambahkan aturan styling modal terkait di `resources/css/pages/peserta/tugas-detail.css`.
- [ ] 3.4 Perbarui interaksi buka/tutup dan validasi input di `resources/js/pages/peserta/tugas-detail.js`.

### Fase 4: Quality Gate & Pengujian
- [ ] 4.1 Jalankan `npm run build` untuk mengompilasi stylesheet dan skrip frontend.
- [ ] 4.2 Jalankan Laravel Pint: `./vendor/bin/pint --test`.
- [ ] 4.3 Jalankan PHPStan: `./vendor/bin/phpstan analyse --memory-limit=1G`.
- [ ] 4.4 Jalankan PHPUnit feature test suite: `php artisan test tests/Feature/PesertaPortalWorkflowTest.php tests/Feature/PesertaFullPortalFeatureTest.php`.

---

## 6. Strategi Testing
- **Feature Tests**:
  - `test_peserta_dapat_melihat_daftar_tugas_dengan_filter_tab()`: Memastikan daftar tugas tampil dan status tab filter bekerja.
  - `test_peserta_dapat_mengumpulkan_tugas_pertama_kali()`: Memastikan progres tugas berhasil disimpan dan status berubah menjadi `proses` / `menunggu`.
  - `test_peserta_tidak_dapat_mengumpulkan_tugas_kedua_kali_jika_sedang_menunggu_review()`: Memastikan server mengembalikan penolakan jika peserta mencoba submit lagi sebelum direview/diminta revisi.
  - `test_peserta_dapat_mengumpulkan_ulang_hanya_jika_berstatus_perlu_revisi()`: Memastikan form terbuka kembali jika pembimbing menetapkan `perlu_revisi`.

---

## 7. Risiko & Mitigasi
- **Risiko**: Peserta salah mengunggah file dan ingin langsung mengganti sebelum pembimbing mereview.
  - **Mitigasi**: Tampilkan informasi jelas di kartu bahwa tugas telah terkirim, serta beri saluran pesan/komentar pribadi untuk menghubungi pembimbing jika terjadi kekeliruan lampiran mendesak.
- **Risiko**: Kompilasi Vite pada modal pop-up baru.
  - **Mitigasi**: Pastikan seluruh kelas CSS di-scoped di bawah `.classroom-assignment-container` dan entry point Vite terpasang dengan benar.

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] Efek hover pada filter tab *Semua Tugas*, *Selesai*, *Lewat Tenggat* tidak terpotong di tepi mana pun.
- [ ] Kartu penugasan tampil lebih kompak, proporsional, dan rapi sesuai Design System MagKom.
- [ ] Tombol pengumpulan tugas menghilang / terkunci setelah peserta menyerahkan tugas (kecuali pembimbing meminta revisi).
- [ ] Pop-up modal upload file dan tambah link menggunakan bahasa Indonesia, desain, dan komponen yang selaras dengan antarmuka Pembimbing.
- [ ] Seluruh automated test suite (PHPUnit), PHPStan, Pint, dan Vite build berstatus **HIJAU (100% Passed)**.

