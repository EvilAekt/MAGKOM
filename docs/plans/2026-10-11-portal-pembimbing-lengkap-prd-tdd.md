# Plan: Penyempurnaan Komprehensif Portal Pembimbing (/pembimbing/*) Sesuai PRD, TDD, dan Roadmap

- **Tanggal**: 2026-10-11
- **Status**: Completed (Terverifikasi Penuh)
- **Author**: Antigravity (web-implementation)
- **Dokumen Acuan**:
  - [Figma Mockup Portal Pembimbing Node 560-41611](https://www.figma.com/design/uKH1OZjwBIi8iRoKQXEbQr/MAGKOM-Kominfo-Sleman?node-id=560-41611&t=y08kZClFvlEyXLj3-4)
  - [`docs/01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Fitur F-08, F-09, F-10, F-11, F-12, Aturan Bisnis BR-09, BR-10, BR-11, BR-13, BR-15, BR-16)
  - [`docs/02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Bab 4 Struktur, Bab 5 Otorisasi Policy, Bab 6 Skema, Bab 8 Keamanan & Matriks Endpoint)
  - [`docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Gelombang 3 Pelaksanaan & Penilaian)
  - [`docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) (Tabel `tugas`, `progres_log`, `logbook`, `rubrik`, `penilaian`, `penilaian_detail`, `penilaian_riwayat`)
  - [`docs/07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Design System MagKom, Palet Sleman Blue `#0641CC`, Komponen & Tipografi)

---

## 1. Tujuan

Membangun dan menyempurnakan seluruh ekosistem **Portal Pembimbing (`/pembimbing/*`)** di MagKom agar selaras 100% dengan rancangan visual [Figma Node 560-41611](https://www.figma.com/design/uKH1OZjwBIi8iRoKQXEbQr/MAGKOM-Kominfo-Sleman?node-id=560-41611&t=y08kZClFvlEyXLj3-4), PRD, TDD, dan Roadmap Diskominfo Sleman. Implementasi ini mencakup penyediaan **Dashboard Eksekutif Pembimbing** yang presisi secara visual dan fungsional, modul **Peserta Bimbingan (Mentee Hub)**, penegakan aturan validasi **Tugas & Progres (F-08)**, penyempurnaan **Reviu Logbook Harian (F-09)**, penguatan **Evaluasi Rubrik & Penetapan Selesai Berbasis Masa Tenggang (F-10, F-12)**, penataan sidebar layout modern, serta jaminan keamanan anti-IDOR dan isolasi SuperAdmin strictly read-only yang diverifikasi oleh automated test suite lengkap.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis & Fungsional:
1. **Batas Beban Pembimbing (BR-13)**: Konfigurasi `maks_peserta_aktif_per_pembimbing` di tabel `pengaturan` (default 10) digunakan sebagai acuan kapasitas pembimbing pada kartu ringkasan dashboard.
2. **Kategori & Status Tugas**: Status tugas mengikuti `StatusTugasEnum` (`belum`, `proses`, `selesai`) dan kategori tugas berupa string fleksibel (contoh: `Pengembangan Sistem`, `Jaringan & Infrastruktur`, `Desain & Multimedia`, `Administrasi & Pelayanan Publik`).
3. **Konversi Predikat Nilai (PRD Bab 8.3 & Bab 8.6)**: Menggunakan batas predikat resmi:
   - **A**: Nilai Akhir $\ge 85.00$
   - **B**: $70.00 \le$ Nilai Akhir $< 85.00$
   - **C**: $55.00 \le$ Nilai Akhir $< 70.00$
   - **D**: $40.00 \le$ Nilai Akhir $< 55.00$
   - **E**: Nilai Akhir $< 40.00$
4. **Isolasi Kepemilikan (Anti-IDOR, NFR-S7)**: Pembimbing hanya berhak mengelola data peserta magang yang memiliki `penempatan.pembimbing_id === Auth::id()`. SuperAdmin dan Admin hanya memiliki akses baca (read-only), dilarang melakukan aksi tulis (HTTP 403 Forbidden).

---

## 3. Pendekatan Desain & Arsitektur

### Perbandingan Pendekatan:

| Kriteria | Pendekatan A: Fragmentasi Parsial (Hanya melengkapi form lama tanpa dashboard) | Pendekatan B: Portal Terpadu Modular (Recommended) |
| :--- | :--- | :--- |
| **Arsitektur Dashboard** | Tidak ada dashboard pembimbing; login langsung lempar ke tabel tugas. | Menyediakan `/pembimbing/dashboard` sebagai pusat kendali (*operations hub*) sesuai PRD 9.2 & TDD. |
| **Manajemen Mentee** | Informasi peserta tercecer di dalam tabel tugas dan penilaian. | Menyediakan modul khusus `/pembimbing/peserta` untuk melihat profil lengkap, timeline, dan kontak darurat mentee. |
| **Kepatuhan PRD & TDD** | Rendah (tidak memenuhi Bab 9.2 PRD dan Matriks Rute TDD Bab 8). | **100% Penuh** mencakup seluruh AC (AC-08, AC-09, AC-10, AC-12, BR-10, BR-11, BR-13, BR-16). |
| **Pengalaman Pengguna (UX)** | Terputus-putus dan kaku, pembimbing kesulitan memantau prioritas pekerjaan harian. | Cepat, efisien, dengan *Pending Action Center* dan indikator visual beban kerja yang jelas. |

> **Keputusan**: Mengadopsi **Pendekatan B (Portal Terpadu Modular)** karena memenuhi standar kelayakan PRD Bab 9.2, TDD Bab 8, dan Roadmap Gelombang 3 secara tuntas dan elegan.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `routes/web.php` | Modify | Menambahkan rute `/pembimbing/dashboard`, `/pembimbing/peserta`, dan `/pembimbing/peserta/{penempatan}`. |
| `app/Http/Controllers/Auth/AuthController.php` | Modify | Mengarahkan redirect login role `pembimbing` ke `pembimbing.dashboard`. |
| `app/Http/Controllers/Pembimbing/DashboardController.php` | Create | Controller dashboard pembimbing: metrik mentee aktif, logbook & progres pending, tugas overdue, off-boarding tracker, dan pending action center. |
| `app/Http/Controllers/Pembimbing/PesertaController.php` | Create | Controller daftar mentee bimbingan dan detail profil 360° mentee (timeline, tugas, logbook, rubrik). |
| `app/Http/Controllers/Pembimbing/TugasController.php` | Modify | Memperbaiki filter status tugas sesuai `StatusTugasEnum`, penegakan AC-08-4 dan AC-08-7. |
| `app/Http/Requests/TugasRequest.php` | Modify | Validasi `deadline`: wajib `after_or_equal:today` dan $\le$ `penempatan.tgl_selesai` (AC-08-1). |
| `app/Http/Requests/ReviewProgresRequest.php` | Modify | Validasi feedback wajib bila `status_review = perlu_revisi` (AC-08-4). |
| `app/Http/Controllers/Pembimbing/LogbookReviewController.php` | Modify | Penambahan sanitasi tautan eksternal (AC-09-6), filter peserta & tanggal pencarian, dan feedback revisi wajib (AC-09-4). |
| `app/Http/Controllers/Pembimbing/PenilaianController.php` | Modify | Integrasi live predikat scoring, modal revisi nilai final (AC-10-3), dan penetapan selesai berstatus final dengan masa tenggang (BR-10, BR-11, AC-10-5). |
| `resources/views/layouts/dashboard.blade.php` | Modify | Memperbarui menu sidebar role `pembimbing` agar memuat menu Dashboard, Peserta Bimbingan, Tugas, Logbook, dan Penilaian. |
| `resources/views/pembimbing/dashboard.blade.php` | Create | Tampilan visual Dashboard Pembimbing dengan Hero kapasitas dinas, 4 kartu KPI, Pending Action Center, dan Pelacak Off-boarding Mentee. |
| `resources/views/pembimbing/peserta/index.blade.php` | Create | Tampilan daftar mentee aktif dan alumni bimbingan lengkap dengan progress bar masa dinas magang. |
| `resources/views/pembimbing/peserta/show.blade.php` | Create | Tampilan profil mentee 360°: data diri, kontak kampus/dosen, timeline magang, riwayat tugas, dan logbook. |
| `resources/views/pembimbing/tugas/index.blade.php` | Modify | Perbaikan opsi status filter (`belum`, `proses`, `selesai`), badge overdue & $\le$ 3 hari (BR-16), dan modal buat tugas responsif. |
| `resources/views/pembimbing/tugas/show.blade.php` | Modify | Perbaikan timeline progres log, modal review progres dengan feedback wajib untuk `perlu_revisi`, dan tombol tandai selesai. |
| `resources/views/pembimbing/logbook/index.blade.php` | Modify | Filter interaktif, preview berkas privat, pembukaan tautan https aman `rel="noopener noreferrer"`, dan modal review catatan revisi. |
| `resources/views/pembimbing/penilaian/index.blade.php` | Modify | Tab filter peserta aktif siap dinilai vs penempatan selesai, status predikat huruf, dan shortcut lembar nilai. |
| `resources/views/pembimbing/penilaian/show.blade.php` | Modify | Live JavaScript kalkulator skor terbobot & predikat, modal konfirmasi revisi nilai final (min 10 karakter), dan tombol penetapan selesai. |
| `tests/Feature/PembimbingPortalFeatureTest.php` | Create | Test suite komprehensif menguji seluruh rute pembimbing, validasi AC, anti-IDOR, dan isolasi SuperAdmin write-lock. |

---

## 5. Rincian Fitur & Modul Portal Pembimbing

### Modul 1: Dashboard Eksekutif Pembimbing (`/pembimbing/dashboard`)
1. **Hero Header**:
   - Ucapan selamat datang personal, NIP/identitas, dan nama Bidang Dinas Diskominfo Sleman tempat pembimbing bertugas.
   - Indikator kapasitas beban bimbingan (BR-13): misal `7 / 10 Mentee Aktif (70% Kapasitas Terisi)`.
2. **4 Kartu Metrik Operasional Utama**:
   - **Peserta Bimbingan Aktif**: Jumlah mentee berstatus `aktif` yang sedang dibimbing saat ini.
   - **Logbook Menunggu Reviu**: Jumlah entri logbook harian berstatus `terkirim` yang belum ditinjau.
   - **Laporan Progres Menunggu Evaluasi**: Jumlah progres tugas dari peserta berstatus `menunggu`.
   - **Kedisiplinan Tugas (Overdue vs Aktif)**: Total tugas belum/proses dengan penanda tugas yang melewati deadline.
3. **Pending Action Center (Pusat Tindakan Mendesak)**:
   - Menampilkan antrean 5 progres tugas terbaru yang butuh evaluasi pembimbing.
   - Menampilkan antrean 5 logbook harian terbaru yang butuh pemeriksaan catatan pembimbing.
4. **Pelacak Peserta Mendekati Batas Selesai (Off-boarding Tracker $\le$ 14 Hari)**:
   - Mengingatkan pembimbing untuk segera menyusun penilaian rubrik final dan menetapkan status selesai sebelum masa dinas berakhir.

### Modul 2: Direktori Peserta Bimbingan (`/pembimbing/peserta` & `/pembimbing/peserta/{penempatan}`)
1. **Halaman Indeks Mentee (`index.blade.php`)**:
   - Filter tab: `Peserta Aktif` vs `Alumni Bimbingan Selesai`.
   - Kartu/Tabel interaktif: Foto/Avatar, Nama Peserta, Asal Institusi & Jurusan, Jalur (Umum/Khusus), Tanggal Mulai s/d Selesai, Progres Durasi (Progress bar waktu magang), dan Status Nilai.
   - Tombol Aksi Cepat: `Lihat Profil Mentee`, `Beri Tugas`, `Buka Logbook`, `Lembar Nilai`.
2. **Halaman Profil Mentee 360° (`show.blade.php`)**:
   - Rincian Data Pribadi & Akademik (NIM/NISN, No. Telepon, Email, Kampus/Sekolah).
   - Informasi Kontak Pembimbing Akademik / Dosen Pembimbing Lapangan.
   - Widget Ringkasan Kinerja Mentee:
     - Total Tugas Selesai / Total Tugas Diberikan.
     - Total Logbook Terkirim vs Perlu Revisi.
     - Status Penilaian (Belum Dinilai / Draft / Final dengan Skor & Predikat).

### Modul 3: Tugas & Progres Kerja (F-08, BR-16)
1. **Validasi Pembuatan Tugas (AC-08-1)**:
   - `deadline` divalidasi wajib $\ge$ hari ini dan $\le$ `tgl_selesai` dari penempatan peserta terkait.
   - Validasi kepemilikan penempatan (hanya untuk mentee milik pembimbing yang sedang login).
2. **Status Tugas Sesuai Database Enum**:
   - Sinkronisasi nilai status: `belum`, `proses`, `selesai`.
   - Visual badge: `Belum Dikerjakan` (abu-abu/biru), `Sedang Dikerjakan` (kuning/oranye), `Selesai` (hijau).
   - Label khusus: `Terlambat (Overdue)` jika `deadline < today` dan status belum selesai; `Mendekati Deadline` jika sisa hari $\le$ 3 hari (BR-16).
3. **Review Progres & Penanda Selesai (AC-08-3 & AC-08-4)**:
   - Form review progres: pilihan status `diterima` atau `perlu_revisi`.
   - Jika `perlu_revisi`, kolom `feedback` wajib diisi minimal 5 karakter.
   - Opsi tombol `Tandai Tugas Selesai` secara eksklusif hanya dapat dilakukan oleh pembimbing (A-22).

### Modul 4: Reviu Logbook Harian (F-09, AC-09)
1. **Filter & Navigasi Logbook (AC-09-4)**:
   - Filter dropdown berdasarkan Mentee tertentu, rentang tanggal, dan status (`Semua`, `Terkirim`, `Perlu Revisi`).
2. **Penanganan Lampiran & Tautan Aman (AC-09-6)**:
   - Berkas PDF privat diakses via signed URL dengan pengecekan policy hak akses.
   - Tautan eksternal diverifikasi skema `https://` dan disajikan dengan atribut `target="_blank" rel="noopener noreferrer"`.
3. **Catatan Reviu Pembimbing**:
   - Modal reviu dengan kolom catatan pembimbing. Jika pembimbing memilih opsi status `perlu_revisi`, maka catatan pembimbing wajib diisi agar peserta mengetahui perbaikan yang harus dilakukan.

### Modul 5: Instrumen Penilaian & Penetapan Selesai (F-10, F-12, BR-10, BR-11)
1. **Instrumen Rubrik Penilaian (AC-10-1 & AC-10-2)**:
   - Input skor 0 sampai 100 untuk setiap kriteria rubrik aktif.
   - Live JavaScript kalkulator yang menghitung Nilai Akhir = $\frac{\sum (\text{skor} \times \text{bobot})}{\sum \text{bobot}}$ dibulatkan 2 desimal serta menampilkan predikat secara langsung (A, B, C, D, E).
   - Simpan sebagai `draft` (belum tampil ke peserta) atau `final` (tampil ke peserta). Status `final` memvalidasi seluruh kriteria terisi lengkap.
2. **Riwayat Perubahan Nilai Final (AC-10-3)**:
   - Jika penilaian yang sudah berstatus `Final` diubah, sistem memunculkan modal alasan perubahan wajib (minimal 10 karakter).
   - Menyimpan snapshot nilai lama, nilai baru, identitas pengubah, alasan, dan waktu ke tabel `penilaian_riwayat`.
3. **Penetapan Selesai & Masa Tenggang (AC-10-5, BR-10, BR-11)**:
   - Tombol `Tetapkan Selesai` memvalidasi bahwa penilaian telah berstatus `Final`.
   - Mengubah status penempatan menjadi `selesai`, mengisi `tgl_ditetapkan_selesai = now()`, dan menghitung `tgl_nonaktif = now() + masa_tenggang_bulan` (default 1 bulan).
4. **Unduh Lembar Nilai PDF Resmi (AC-10-6)**:
   - Pratinjau dan unduh berkas PDF Lembar Nilai resmi yang di-generate dari snapshot data terkini.

---

## 6. Langkah Implementasi Bertahap

*Catatan: Seluruh tahapan pengerjaan telah berhasil dieksekusi dan terverifikasi penuh.*

### Tahap 1: Routing, Sidebar Navigasi & Redirect Auth
- [x] **Langkah 1.1**: Daftarkan rute baru di `routes/web.php` dalam grup middleware `role:pembimbing,admin`:
  - `GET /pembimbing/dashboard` (`pembimbing.dashboard`)
  - `GET /pembimbing/peserta` (`pembimbing.peserta.index`)
  - `GET /pembimbing/peserta/{penempatan}` (`pembimbing.peserta.show`)
- [x] **Langkah 1.2**: Perbarui redirection role pembimbing di `app/Http/Controllers/Auth/AuthController.php` agar mengarah ke `route('pembimbing.dashboard')`.
- [x] **Langkah 1.3**: Perbarui menu sidebar pada `resources/views/layouts/dashboard.blade.php` untuk peran `pembimbing`:
  - Dashboard Pembimbing
  - Peserta Bimbingan
  - Tugas Peserta
  - Reviu Logbook
  - Penilaian Magang

### Tahap 2: Dashboard Pembimbing (`/pembimbing/dashboard`)
- [x] **Langkah 2.1**: Buat controller `app/Http/Controllers/Pembimbing/DashboardController.php`:
  - Hitung total mentee aktif vs batas beban `maks_peserta_aktif_per_pembimbing`.
  - Hitung antrean logbook pending reviu (`status = 'terkirim'`).
  - Hitung antrean progres tugas pending reviu (`status_review = 'menunggu'`).
  - Hitung tugas aktif vs tugas terlambat (overdue).
  - Ambil 5 progres tugas terbaru dan 5 logbook terbaru untuk *Pending Action Center*.
  - Ambil peserta aktif bimbingan yang akan selesai dalam $\le$ 14 hari (*Off-boarding Tracker*).
- [x] **Langkah 2.2**: Buat view `resources/views/pembimbing/dashboard.blade.php` dengan estetika modern, kartu metrik bergradien halus, list antrean aksi cepat, dan timeline bimbingan.

### Tahap 3: Direktori Mentee Bimbingan (`/pembimbing/peserta`)
- [x] **Langkah 3.1**: Buat controller `app/Http/Controllers/Pembimbing/PesertaController.php`:
  - Method `index()`: query penempatan bimbingan milik pembimbing yang login dengan filter tab `aktif` dan `selesai`, paginasi 15 data.
  - Method `show()`: query detail penempatan mentee beserta data diri, profil, kampus, akumulasi tugas, logbook, dan status nilai.
- [x] **Langkah 3.2**: Buat view `resources/views/pembimbing/peserta/index.blade.php`:
  - Tabel/kartu daftar mentee dengan progress bar durasi magang, avatar inisial, asal institusi, bidang, dan tombol aksi cepat.
- [x] **Langkah 3.3**: Buat view `resources/views/pembimbing/peserta/show.blade.php`:
  - Profil 360° peserta magang bimbingan, kontak darurat/dosen, timeline magang, dan riwayat penugasan.

### Tahap 4: Modul Tugas & Progres (F-08, BR-16)
- [x] **Langkah 4.1**: Perbarui `app/Http/Requests/TugasRequest.php`:
  - Tambahkan validasi deadline: `deadline >= today` dan `deadline <= penempatan.tgl_selesai` (AC-08-1).
- [x] **Langkah 4.2**: Perbarui `app/Http/Requests/ReviewProgresRequest.php`:
  - Validasi kolom `feedback`: wajib jika `status_review === 'perlu_revisi'` (AC-08-4).
- [x] **Langkah 4.3**: Perbarui `app/Http/Controllers/Pembimbing/TugasController.php`:
  - Sinkronkan filter query status dengan `StatusTugasEnum` (`belum`, `proses`, `selesai`).
  - Tambahkan method untuk menandai tugas selesai (`selesai`).
- [x] **Langkah 4.4**: Perbarui view `resources/views/pembimbing/tugas/index.blade.php` dan `show.blade.php`:
  - Koreksi opsi dropdown status agar sesuai enum (`belum`, `proses`, `selesai`).
  - Pasang label visual `Terlambat (Overdue)` dan `Mendekati Deadline (≤ 3 Hari)` (BR-16).
  - Sempurnakan modal buat tugas dan modal review progres kerja.

### Tahap 5: Modul Reviu Logbook (F-09, AC-09)
- [x] **Langkah 5.1**: Perbarui `app/Http/Controllers/Pembimbing/LogbookReviewController.php`:
  - Perluas filter pencarian (peserta bimbingan, tanggal, status terkirim / perlu revisi).
  - Pastikan pencatatan audit log saat reviu logbook disimpan.
- [x] **Langkah 5.2**: Perbarui view `resources/views/pembimbing/logbook/index.blade.php`:
  - Pastikan tautan eksternal memiliki atribut `target="_blank" rel="noopener noreferrer"` dan skema `https://` (AC-09-6).
  - Pasang modal reviu logbook dengan validasi catatan wajib saat memilih status `perlu_revisi`.

### Tahap 6: Modul Penilaian, Riwayat & Masa Tenggang (F-10, F-12, BR-10, BR-11)
- [x] **Langkah 6.1**: Sempurnakan `app/Http/Controllers/Pembimbing/PenilaianController.php`:
  - Pastikan validasi bahwa `selesai()` menolak aksi jika penilaian belum final (AC-10-5).
  - Pastikan pencatatan riwayat revisi nilai final tersimpan rapi di tabel `penilaian_riwayat` (AC-10-3).
- [x] **Langkah 6.2**: Perbarui view `resources/views/pembimbing/penilaian/index.blade.php` dan `show.blade.php`:
  - Pasang live JavaScript score calculator & predikat preview.
  - Sediakan modal konfirmasi revisi nilai final dengan input alasan perubahan (min 10 karakter).
  - Sediakan tombol konfirmasi penetapan selesai magang dengan pesan penjelasan masa tenggang.

### Tahap 7: Automated Testing & Verifikasi Komprehensif
- [x] **Langkah 7.1**: Buat test suite lengkap `tests/Feature/PembimbingPortalFeatureTest.php` untuk menguji:
  - Akses dashboard pembimbing dan perhitungan metrik KPI bimbingan.
  - Akses daftar peserta bimbingan dan detail profil mentee.
  - Validasi deadline tugas ($\ge$ today dan $\le$ `tgl_selesai`) (AC-08-1).
  - Indikator tugas overdue dan mendekati deadline (BR-16).
  - Umpan balik review progres wajib saat `perlu_revisi` (AC-08-4).
  - Review logbook dan penanganan tautan aman (AC-09-4, AC-09-6).
  - Perhitungan skor rubrik, snapshot bobot, dan riwayat revisi nilai final (AC-10-1 s/d AC-10-3).
  - Penetapan selesai mensyaratkan penilaian final dan otomatisasi masa tenggang (BR-11, AC-10-5).
  - Proteksi IDOR lintas pembimbing (Pembimbing A tidak bisa edit data mentee Pembimbing B, NFR-S7).
  - Proteksi SuperAdmin strictly read-only (SuperAdmin ditolak HTTP 403 saat mencoba membuat tugas/review/nilai, AC-08-5, AC-10-7).
- [x] **Langkah 7.2**: Jalankan seluruh feature test lama (`TugasLogbookTest.php`, `ScoringAndCompletionTest.php`, `DashboardModulesRenderingTest.php`, dll.) untuk memastikan zero regression.

---

## 7. Strategi Testing

1. **Automated Feature Tests**:
   - `php artisan test tests/Feature/PembimbingPortalFeatureTest.php`
   - `php artisan test tests/Feature/TugasLogbookTest.php`
   - `php artisan test tests/Feature/ScoringAndCompletionTest.php`
   - `php artisan test tests/Feature/DashboardModulesRenderingTest.php`
2. **Skenario Pengujian Unit & Otorisasi**:
   - Pembimbing mencoba membuat tugas dengan deadline kemarin $\rightarrow$ ditolak validasi.
   - Pembimbing mencoba membuat tugas dengan deadline melebihi `tgl_selesai` magang mentee $\rightarrow$ ditolak validasi.
   - Pembimbing A mencoba membuka atau mengedit tugas/logbook/nilai mentee milik Pembimbing B $\rightarrow$ ditolak 403 / 404.
   - SuperAdmin mencoba POST membuat tugas atau menilai $\rightarrow$ ditolak HTTP 403.
   - Pembimbing menetapkan selesai tanpa penilaian final $\rightarrow$ ditolak validasi.

---

## 8. Risiko & Mitigasi

| Potensi Risiko | Tingkat Dampak | Strategi Mitigasi |
| :--- | :---: | :--- |
| **Peralihan rute redirect login pembimbing** | Rendah | Mengarahkan `RoleEnum::Pembimbing` di `AuthController` langsung ke `pembimbing.dashboard` yang telah diuji aktif. |
| **Perbedaan nilai status tugas antara view dan enum** | Sedang | Menstandarkan nilai status di HTML select option hanya ke `belum`, `proses`, dan `selesai` sesuai `StatusTugasEnum`. |
| **Kueri kalkulasi dashboard memperlambat loading** | Rendah | Menggunakan `withCount` dan filter berindeks `pembimbing_id` serta eager loading relasi penempatan. |
| **Penetapan status selesai tanpa nilai final** | Tinggi | Validasi ganda di level Controller, Service, dan Database constraint sebelum status diubah ke `selesai`. |

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Rute `/pembimbing/dashboard`, `/pembimbing/peserta`, dan `/pembimbing/peserta/{penempatan}` terdaftar dan dapat diakses dengan middleware otorisasi yang tepat.
- [x] Dashboard Pembimbing menampilkan hero kapasitas bimbingan, 4 kartu KPI bimbingan, Pending Action Center, dan Off-boarding Tracker mentee.
- [x] Modul Peserta Bimbingan menyajikan daftar mentee dan profil 360° lengkap.
- [x] Modul Tugas memvalidasi deadline sesuai AC-08-1 dan menyajikan label overdue/mendekati deadline sesuai BR-16.
- [x] Modul Reviu Logbook memvalidasi catatan revisi wajib dan sanitasi tautan `noopener noreferrer`.
- [x] Modul Penilaian mendukung live score calculator, snapshot rubrik, riwayat revisi nilai final (AC-10-3), dan penetapan selesai bermasa tenggang (BR-11, AC-10-5).
- [x] Sidebar layout untuk pembimbing menampilkan seluruh menu secara rapi dengan active-state yang akurat.
- [x] Seluruh hak akses anti-IDOR dan aturan SuperAdmin strictly read-only teruji aman.
- [x] Tampilan visual dan tata letak Portal Pembimbing (Dashboard, Peserta Bimbingan, Tugas, Reviu Logbook, dan Penilaian) presisi secara estetika selaras dengan mockup Figma Node 560-41611 dan Design System MagKom.
- [x] 100% automated test suite lulus tanpa regresi (22 test, 133 assertions, 0 failure).

---

## 10. Catatan Perubahan & Hasil Eksekusi

- **Hasil Pengujian Otomatis**:
  - `tests/Feature/PembimbingPortalFeatureTest.php`: 8 tests, 36 assertions (PASS)
  - `tests/Feature/TugasLogbookTest.php`: 4 tests, 28 assertions (PASS)
  - `tests/Feature/ScoringAndCompletionTest.php`: 7 tests, 31 assertions (PASS)
  - `tests/Feature/DashboardModulesRenderingTest.php`: 3 tests, 38 assertions (PASS)
  - **Total**: 22 tests, 133 assertions, 0 failure.
- **Hasil Verifikasi Visual Browser Subagent**:
  - Login role pembimbing (`pembimbing@magkom.go.id`) sukses mengarah langsung ke `/pembimbing/dashboard`.
  - Tampilan visual kelima halaman Portal Pembimbing terverifikasi presisi:
    - `pembimbing_dashboard_1791663850720.png`
    - `pembimbing_peserta_1791663864040.png`
    - `pembimbing_tugas_1791663873863.png`
    - `pembimbing_logbook_1791663884771.png`
    - `pembimbing_penilaian_1791663895228.png`
- **Saran Lanjutan (Out of Scope)**:
  - Notifikasi push atau email alert berkala ke pembimbing ketika ada entri logbook atau progres baru (fitur opsional di masa mendatang).
