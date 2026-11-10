# Rencana Kerja: Pembersihan Teks Deskripsi Panjang, Helper Text, dan Kode Spesifikasi PRD/BR/AC di Seluruh Tampilan SuperAdmin

**Dokumen Rencana**: `docs/plans/2026-11-02-pembersihan-teks-dan-kode-spesifikasi-superadmin.md`  
**Status**: Selesai Diimplementasikan & Terverifikasi  
**Tujuan**: Membersihkan teks deskripsi panjang, banner penjelasan aturan internal, label subtitle berlebih, dan kode teknis spesifikasi (seperti `F-06`, `BR-12`, `BR-13`, `BR-09`, `BR-04`, `BR-16`, `BR-10`, `AC-11-7`, `AC-10-4`, `PRD 8.3 & 8.6`, `PRD 4.3`, `PRD Bab 3.1 & 3.3`) dari antarmuka SuperAdmin agar tampilan antarmuka lebih bersih, profesional, minimalis, dan siap produksi (*production-ready*).

---

## 1. Analisis Kebutuhan & Pemetaan Teks

Berdasarkan daftar teks yang diminta oleh user, seluruh teks terdistribusi di 9 file Blade view di dalam direktori `resources/views/superadmin/` (ditambah 1 file partials dashboard pengawasan):

### A. Teks Deskripsi Halaman (`@section('page_description')`)
1. `Kelola daftar unit kerja / bidang dinas tempat penempatan peserta magang di lingkungan Diskominfo Sleman.` &rarr; `resources/views/superadmin/bidang/index.blade.php:5`
2. `Kelola jadwal pembukaan pendaftaran dan override status ketersediaan bulan magang.` &rarr; `resources/views/superadmin/bulan/index.blade.php:5`
3. `Kelola daftar jurusan program pendidikan (SMK dan Perguruan Tinggi) untuk alokasi kuota magang.` &rarr; `resources/views/superadmin/jurusan/index.blade.php:5`
4. `Kelola, konfigurasikan, dan pantau seluruh modul kustom yang aktif di portal MagKom Sleman.` &rarr; `resources/views/superadmin/modul-builder/index.blade.php:5`
5. `Kelola batasan kebijakan teknis dan aturan operasional portal MagKom (AC-11-7).` &rarr; `resources/views/superadmin/pengaturan/index.blade.php:5`
6. `Matriks wewenang, batasan fungsional, dan cakupan hak akses seluruh peran sistem MagKom (PRD Bab 3.1 & 3.3).` &rarr; `resources/views/superadmin/role/index.blade.php:5`
7. `Kelola kriteria evaluasi kompetensi magang dengan penegakan total bobot tepat 100% (AC-10-4).` &rarr; `resources/views/superadmin/rubrik/index.blade.php:5`
8. `Kelola alokasi kuota khusus instansi kerjasama atau penugasan internal dinas (F-06).` &rarr; `resources/views/superadmin/slot-khusus/index.blade.php:5`
9. `Kelola akun staf dinas (Admin Verifikator & Pembimbing Lapangan), reset kata sandi, dan status aktifasi.` &rarr; `resources/views/superadmin/staf/index.blade.php:5`

*Catatan Mekanisme Layout*: Di `resources/views/layouts/dashboard.blade.php`, elemen `<p class="page-desc-text">` dibungkus dalam `@hasSection('page_description')`. Jika `@section('page_description')` dihapus, maka header halaman menjadi ramping dan rapi tanpa menyisakan margin/padding kosong.

---

### B. Teks Banner Peringatan / Aturan Operasional Internal
1. `Setiap peserta magang yang lolos seleksi akan ditempatkan di salah satu bidang unit kerja dinas di bawah bimbingan staf pembimbing lapangan terkait sesuai formasi penugasan.` &rarr; `resources/views/superadmin/bidang/index.blade.php:110` (Banner "Unit Kerja Penempatan Magang")
2. `Secara default sistem menghitung status ketersediaan berdasarkan rentang tanggal buka-tutup pendaftaran. SuperAdmin dapat melakukan intervensi manual (Override) jika kuota telah habis atau terdapat kebijakan diskresi khusus dinas.` &rarr; `resources/views/superadmin/bulan/index.blade.php:110` (Banner "Otomasi Siklus & Override Status Pendaftaran")
3. `Jurusan tidak dapat dinonaktifkan jika masih terikat dengan kuota aktif pada bulan pendaftaran berjalan atau masih memiliki peserta magang aktif yang ditempatkan di dinas.` &rarr; `resources/views/superadmin/jurusan/index.blade.php:107` (Banner "Aturan Penonaktifan Jurusan (BR-12)")
4. `Slot khusus tidak mengurangi kuota umum publik dan dialokasikan untuk kemitraan strategis lembaga/kampus atau penugasan kedinasan khusus atas instruksi pimpinan Kominfo.` &rarr; `resources/views/superadmin/slot-khusus/index.blade.php:107` (Banner "Alokasi Jalur Khusus (F-06)")
5. `Sebagai pemegang wewenang tata kelola teknis tertinggi dinas (SuperAdmin), Anda memiliki akses pengawasan dan kontrol mutlak terhadap modul Administrasi Dinas, Pembimbing Lapangan, dan Peserta Magang (PRD Bab 3.1 & 3.3).` &rarr; `resources/views/superadmin/role/index.blade.php:60` (Banner "SuperAdmin Mewarisi Seluruh Hak Akses Tiga Peran Lain")
6. `Sistem mewajibkan total akumulasi bobot dari seluruh rubrik yang berstatus Aktif tepat bernilai 100% agar instrumen penilaian sah digunakan oleh pembimbing lapangan saat evaluasi nilai akhir.` &rarr; `resources/views/superadmin/rubrik/index.blade.php:120` (Card Integritas Bobot)

---

### C. Subtitle Panel & Card
1. `Riwayat jadwal gelombang magang dan status penerimaan calon peserta.` &rarr; `resources/views/superadmin/bulan/index.blade.php:116` (`subtitle` pada `<x-gentelella.x-panel>`)
2. `Daftar program kualifikasi akademik yang diakomodasi untuk seleksi magang.` &rarr; `resources/views/superadmin/jurusan/index.blade.php:113` (`subtitle` pada `<x-gentelella.x-panel>`)
3. `Standar pembagian otorisasi RBAC (Role-Based Access Control) MagKom.` &rarr; `resources/views/superadmin/role/index.blade.php:109` (`subtitle` pada `<x-gentelella.x-panel>`)
4. Deskripsi Kartu Peran di `resources/views/superadmin/role/index.blade.php`:
   - Line 74: `Kontrol penuh akun staf, audit log, kuota kemitraan, dan konfigurasi master.`
   - Line 84: `Verifikasi berkas pendaftar, alokasi kuota jurusan, penempatan, dan laporan dinas.`
   - Line 94: `Pemberian tugas teknis, reviu logbook harian, dan penilaian rubrik akhir mentee.`
   - Line 104: `Pendaftaran mandiri, pelaporan logbook, penyelesaian tugas, dan unduh sertifikat.`
5. Subtitle Kartu Pengaturan di `resources/views/superadmin/pengaturan/index.blade.php`:
   - Line 110: `Batasan daya tampung reguler, slot cadangan kemitraan, dan batas rasio pembimbingan.` (Card 1)
   - Line 159: `Ketentuan batas mundur entri jurnal harian, ukuran file dokumen, dan peringatan deadline tugas.` (Card 2)
   - Line 208: `Masa aktif akun peserta setelah dinyatakan selesai magang sebelum dinonaktifkan permanen.` (Card 3)
   - Line 240: `Menjalankan pemangkasan data log pembacaan (akses_data) manual saat ini juga tanpa menunggu jadwal bulanan.` (Audit Purge Subtitle)
   - Line 260: `Konfigurasi ambang skor minimum untuk predikat A, B, C, dan D (Di bawah ambang D otomatis predikat E).` (Card 4)
   - Line 314: `Kontrol fleksibilitas aktivasi modul lanjutan (Sertifikat Digital, Survei Kepuasan, dan Analitik Tahunan).` (Card 5)

---

### D. Helper Text Form & Keterangan Sakelar Fitur (`pengaturan/index.blade.php`)
1. Line 122: `Maksimal alokasi kuota reguler per bulan pendaftaran.`
2. Line 132: `Kapasitas cadangan kemitraan khusus internal (F-06).`
3. Line 142: `Penegakan beban kerja pembimbing (BR-13). Kosongkan jika tanpa batas.`
4. Line 171: `Maksimal hari mundur pengisian logbook peserta (BR-09).`
5. Line 181: `Ukuran batas file berkas persyaratan dan lampiran (BR-04).`
6. Line 191: `Masa aktif indikator mendekati batas waktu penugasan (BR-16).`
7. Line 220: `Rentang waktu akun peserta tetap aktif untuk mengunduh sertifikat dan lembar nilai (BR-10).`
8. Line 230: `Batas retensi log akses data sebelum dipangkas otomatis (NFR-A5; default 365 hari / 1 tahun).`
9. Line 332: `Mengaktifkan direktori sertifikat alumni, pratinjau, dan ekspor unduhan PDF sertifikat resmi (AC-12, BR-10).`
10. Line 353: `Mengaktifkan instrumen evaluasi kepuasan peserta (IKM) dan dasbor rekapitulasi rating operasional (PRD 4.3 Poin 4).`
11. Line 374: `Mengaktifkan ringkasan tren tahunan, rasio seleksi peserta, dan ekspor CSV analitik (PRD 4.3 Poin 5).`

---

### E. Kode-Kode Spesifikasi (`F-06`, `BR-12`, `AC-10-4`, `PRD 8.3 & 8.6`, `BR-16`)
1. `(F-06)`:
   - Judul di `resources/views/superadmin/slot-khusus/index.blade.php:104`: `Alokasi Jalur Khusus (F-06)` &rarr; hilangkan `(F-06)` menjadi `Alokasi Jalur Khusus` (atau bersihkan jika banner diangkat).
   - Di pengaturan Line 132 & deskripsi halaman.
2. `(BR-12)`:
   - Judul di `resources/views/superadmin/jurusan/index.blade.php:104`: `Aturan Penonaktifan Jurusan (BR-12)` &rarr; hilangkan ` (BR-12)`.
3. `(AC-10-4)`:
   - Judul di `resources/views/superadmin/rubrik/index.blade.php:105`: `Aturan Validasi Integritas Rubrik (AC-10-4)` &rarr; hilangkan ` (AC-10-4)` menjadi `Aturan Validasi Integritas Rubrik`.
4. `(PRD 8.3 & 8.6)`:
   - Judul di `resources/views/superadmin/pengaturan/index.blade.php:257`: `Skala Ambang Batas Predikat Nilai (PRD 8.3 & 8.6)` &rarr; hilangkan ` (PRD 8.3 & 8.6)` menjadi `Skala Ambang Batas Predikat Nilai`.
5. `BR-16`:
   - Di `resources/views/superadmin/pengaturan/index.blade.php:191`.
   - Di `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php:85`: `Tenggat mendekati sesuai aturan BR-16` &rarr; bersihkan menjadi `Tenggat mendekati batas akhir` atau hapus hint.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Penghapusan Bersih (Clean Removal)**:
   - Untuk `@section('page_description')`, directive ini dihapus langsung sehingga header halaman menjadi clean dan padat.
   - Untuk atribut `subtitle="..."` pada komponen `<x-gentelella.x-panel>`, atribut tersebut dihilangkan sehingga panel hanya menampilkan judul utama tabel.
   - Untuk teks helper form (`<span style="...">...</span>`) dan teks paragraf subtitle pada kartu/card pengaturan, elemen pembungkusnya dibersihkan agar tidak meninggalkan tag kosong atau jarak vertikal berlebih.
   - Untuk banner peringatan (Alert Banners) di `bidang`, `bulan`, `jurusan`, `slot-khusus`, dan `role`: karena isi konten utamanya adalah teks yang diminta dihapus oleh user, maka seluruh container banner tersebut dihapus sehingga langsung menampilkan tabel data/kartu utama (konsisten dengan halaman admin).
   - Untuk Rubrik Penilaian: pada kartu integritas bobot 100%, badge persentase (misal `100% Sah & Terpenuhi`) dan progress bar dipertahankan agar admin tetap dapat melihat validitas bobot secara visual, namun teks aturan teknis panjang dan kode `(AC-10-4)` dihilangkan.

---

## 3. Evaluasi Pendekatan Teknis

| Parameter | **Opsi A: Clean Structural Stripping (Rekomendasi)** | **Opsi B: Text-Only Deletion (Empty Tags)** |
| :--- | :--- | :--- |
| **Deskripsi** | Menghapus teks beserta elemen HTML pembungkusnya yang menjadi mubazir (seperti tag `<p>`, `<span>`, banner kosong, atau atribut `subtitle`). | Hanya menghapus string teks di dalam tag, membiarkan tag `<p></p>` atau banner kosong tetap ada di DOM. |
| **Dampak Visual UI** | **Sangat Rapi & Modern**: Tidak ada celah/gap margin kosong, tidak ada kotak alert kosong tanpa isi, tabel data langsung menjadi fokus utama. | **Kurang Rapi**: Ada margin kosong, container alert bergaris biru tanpa isi teks atau hanya berisi icon. |
| **Konsistensi** | Selaras dengan pembersihan layout pada antarmuka admin sebelumnya (tabel langsung di atas). | Tidak standar dan menyisakan *dead markup*. |
| **Rekomendasi** | **Dipilih (Opsi A)** karena memberikan hasil antarmuka profesional dan bersih. | Dihindari. |

---

## 4. Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/superadmin/bidang/index.blade.php` | `Modify` | Hapus `@section('page_description')` dan bersihkan alert banner penempatan peserta. |
| `resources/views/superadmin/bulan/index.blade.php` | `Modify` | Hapus `@section('page_description')`, alert banner otomasi jadwal, dan atribut `subtitle` panel. |
| `resources/views/superadmin/jurusan/index.blade.php` | `Modify` | Hapus `@section('page_description')`, alert banner aturan penonaktifan (BR-12), dan atribut `subtitle` panel. |
| `resources/views/superadmin/modul-builder/index.blade.php` | `Modify` | Hapus `@section('page_description')`. |
| `resources/views/superadmin/pengaturan/index.blade.php` | `Modify` | Hapus `@section('page_description')`, bersihkan subtitle kartu 1 s/d 5, helper text form, kode `(F-06)`, `(BR-13)`, `(BR-09)`, `(BR-04)`, `(BR-16)`, `(BR-10)`, `(NFR-A5)`, `(PRD 8.3 & 8.6)`, serta keterangan sakelar fitur. |
| `resources/views/superadmin/role/index.blade.php` | `Modify` | Hapus `@section('page_description')`, alert banner wewenang SuperAdmin, deskripsi di bawah kartu peran, dan atribut `subtitle` panel RBAC. |
| `resources/views/superadmin/rubrik/index.blade.php` | `Modify` | Hapus `@section('page_description')`, kode `(AC-10-4)` pada judul integritas bobot, serta teks aturan akumulasi 100%. |
| `resources/views/superadmin/slot-khusus/index.blade.php` | `Modify` | Hapus `@section('page_description')` dan alert banner alokasi jalur khusus (F-06). |
| `resources/views/superadmin/staf/index.blade.php` | `Modify` | Hapus `@section('page_description')`. |
| `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php` | `Modify` | Bersihkan string `BR-16` pada metric hint deadline. |

---

## 5. Langkah Implementasi (Checklist)

- [x] **Langkah 1**: Bersihkan deskripsi halaman dan alert banner di `resources/views/superadmin/bidang/index.blade.php`.
- [x] **Langkah 2**: Bersihkan deskripsi halaman, banner otomasi siklus, dan subtitle panel di `resources/views/superadmin/bulan/index.blade.php`.
- [x] **Langkah 3**: Bersihkan deskripsi halaman, banner penonaktifan BR-12, dan subtitle panel di `resources/views/superadmin/jurusan/index.blade.php`.
- [x] **Langkah 4**: Bersihkan deskripsi halaman di `resources/views/superadmin/modul-builder/index.blade.php`.
- [x] **Langkah 5**: Lakukan refactoring menyeluruh pada `resources/views/superadmin/pengaturan/index.blade.php`:
  - Hapus `@section('page_description')`.
  - Hapus subtitle pada Card 1, 2, 3, 4, 5, dan tombol pemangkasan audit.
  - Hapus helper text input (`kuota_umum_maks_per_bulan`, `slot_khusus_default`, `maks_peserta_aktif_per_pembimbing`, `logbook_maks_mundur_hari`, `berkas_maks_mb`, `deadline_mendekati_hari`, `masa_tenggang_bulan`, `retensi_audit_akses_data_hari`).
  - Hapus kode `(PRD 8.3 & 8.6)` dari judul skala ambang batas.
  - Hapus deskripsi modul di bawah sakelar sertifikat, survei, dan analitik.
- [x] **Langkah 6**: Bersihkan `resources/views/superadmin/role/index.blade.php`:
  - Hapus `@section('page_description')`.
  - Hapus alert banner hak akses SuperAdmin.
  - Hapus span deskripsi di 4 kartu ringkasan peran.
  - Hapus atribut `subtitle` pada `<x-gentelella.x-panel>`.
- [x] **Langkah 7**: Bersihkan `resources/views/superadmin/rubrik/index.blade.php`:
  - Hapus `@section('page_description')`.
  - Hapus `(AC-10-4)` dari judul aturan validasi.
  - Hapus teks penjelasan akumulasi 100%.
- [x] **Langkah 8**: Bersihkan `resources/views/superadmin/slot-khusus/index.blade.php`:
  - Hapus `@section('page_description')`.
  - Hapus alert banner alokasi jalur khusus (F-06).
- [x] **Langkah 9**: Bersihkan `resources/views/superadmin/staf/index.blade.php`:
  - Hapus `@section('page_description')`.
- [x] **Langkah 10**: Bersihkan referensi `BR-16` di `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php`.

---

## 6. Strategi Testing

1. **Feature Test Verification**:
   - Jalankan `php artisan test --filter=SuperAdminPortalVisualTest` untuk memastikan seluruh halaman superadmin tetap dapat dirender dengan status HTTP 200 tanpa error Blade syntax.
   - Jalankan `php artisan test --filter=SuperAdminUnifiedDashboardFeaturesTest` untuk memverifikasi dashboard superadmin dan tab pengawasan tetap berjalan normal.
   - Jalankan `php artisan test --filter=AdminSuperAdminPhaseThreeTest` dan `AdminSuperAdminPhaseTwoTest` untuk memastikan logika bisnis underlying (seperti validasi controller penolakan BR-12, AC-10-4) tetap berfungsi penuh di backend.
2. **Regression Check**:
   - Pastikan form input pengaturan di `pengaturan/index.blade.php` tetap mengirimkan payload input name dan value yang sama ke backend controller saat disubmit (`POST`).

---

## 7. Risiko & Mitigasi

- **Risiko**: Ada sisa whitespace vertikal yang terlalu longgar akibat hilangnya subtitle atau alert box.  
  **Mitigasi**: Pastikan container tabel dan card mempertahankan jarak standar container flexbox/grid (`gap: 1.5rem`) yang rapi tanpa inline margin ganda.
- **Risiko**: Backend logic terpengaruh.  
  **Mitigasi**: Seluruh perubahan hanya berada pada lapisan presentasi Blade View (`.blade.php`), tidak ada modifikasi pada Controller, Model, Request Validation, ataupun Database Migration.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Seluruh 32+ item teks yang diminta user telah dihapus dari file view terkait di SuperAdmin.
- [x] Tidak ada kode spesifikasi dokumen internal (`F-06`, `BR-12`, `BR-13`, `BR-09`, `BR-04`, `BR-16`, `BR-10`, `AC-11-7`, `AC-10-4`, `PRD ...`) yang terpapar di halaman-halaman tersebut.
- [x] Form pengaturan tetap berfungsi normal saat menyimpan konfigurasi.
- [x] Seluruh pengujian otomatis SuperAdmin (`SuperAdminPortalVisualTest`, `SuperAdminDashboardAndAuditLogTest`) lolos 100% (*green*).
- [ ] Knowledge graph diperbarui dengan `graphify update .`.

