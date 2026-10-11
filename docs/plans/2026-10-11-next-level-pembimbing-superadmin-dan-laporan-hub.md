# Plan: Next-Level Enhancements 3 Pilar MagKom (Pembimbing, SuperAdmin, dan Administrasi/Laporan)

- **Tanggal**: 2026-10-11
- **Status**: Selesai (Completed)
- **Author**: Antigravity (web-implementation)
- **Target Area**: 
  1. **Pilar 1 (Portal Pembimbing)**: Fitur Produktivitas Massal (Bulk Approval Logbook), Pustaka Komentar Instan (Quick Feedback Snippets), Ekspor Nilai Internal Bidang, dan Indikator SLA Respons Bimbingan.
  2. **Pilar 2 (Portal SuperAdmin)**: Penjaga SuperAdmin Terakhir & Pemutusan Sesi Paksa (SEC-03, SEC-16), Antarmuka Form Pengaturan Sistem Dinamis (`/superadmin/pengaturan`), Ekspor Audit Log CSV Terfilter (AC-13-4), dan Poles Visual Bank Data Arsip Alumni (`/superadmin/arsip`).
  3. **Pilar 3 (Portal Administrasi & Laporan)**: Visual Analytics & Charting Interaktif di Hub Laporan, Batch Ekspor Dokumen Surat Diterima (F-16), dan Fast-Track Promosi Waiting List FIFO & Penutupan Antrean Otomatis (AC-04-7).
- **Dokumen Acuan**:
  - [`docs/01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (F-04, F-09, F-11, F-13, F-16, F-17; BR-06, BR-09, BR-12, BR-13, BR-15; AC-04-7, AC-09-3, AC-11-7, AC-13-4)
  - [`docs/02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Bab 4 Struktur, Bab 5 Otorisasi Policy, Bab 6 Skema & Enum, Bab 8 Keamanan)
  - [`docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Gelombang 4, Rilis 2 & Rilis 3)
  - [`docs/06-ADDENDUM-KEAMANAN-DAN-QA.md`](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md) (SEC-03, SEC-04, SEC-16, SEC-30, SEC-34)
  - [`docs/07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Sleman Blue `#0641CC`, font Inter/Open Sans, `.clean-scrollbar`, visual tokens)

---

## 1. Tujuan

Menyusun rencana pengembangan tingkat lanjut (*Next-Level Enhancements*) untuk melengkapi dan memperkaya kapabilitas ketiga portal operasional MagKom:
1. **Meningkatkan Produktivitas Pembimbing**: Memangkas waktu administratif pembimbing dalam memeriksa puluhan logbook harian lewat fitur reviu massal (*bulk approval*) dan template komentar cepat, serta menyediakan ekspor rekap internal per bidang.
2. **Memperkokoh Tata Kelola & Keamanan SuperAdmin**: Menegakkan pengamanan tingkat enterprise (*last SuperAdmin guardrail*, *instant session invalidation* saat reset akun staf), pengelolaan parameter kebijakan secara visual tanpa sentuh database langsung, serta ekspor audit trail berfilter.
3. **Menghadirkan Intelijen Operasional Administrasi**: Memberikan visualisasi data tren pendaftaran dan kapasitas kuota melalui visual analytics, otomatisasi batch unduh surat penerimaan (F-16), dan manajemen antrean waiting list FIFO yang transparan.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis & Fungsional:
1. **Bulk Approval Logbook**: Aksi reviu massal hanya dapat dilakukan untuk entri logbook dengan status `terkirim` (status `perlu_revisi` tetap membutuhkan catatan spesifik per entri sesuai aturan bisnis BR-09).
2. **Kerahasiaan Ekspor Nilai Internal Bidang**: Pembimbing hanya diizinkan mengekspor rekapitulasi mentee yang berada di bawah bimbingannya sendiri atau dalam bidang dinas yang sama, mencegah kebocoran data antar-bidang dinas (anti-IDOR SEC-11).
3. **Visual Analytics Ringan Tanpa Dependensi Berat**: Visual chart pada dasbor laporan akan diimplementasikan menggunakan komponen visual interaktif berbasis SVG modern / Chart.js ringan via CDN terisolasi agar tetap ringan, cepat, dan kompatibel tanpa menambah beban bundle NPM berlebih.
4. **Proteksi Penjaga SuperAdmin Terakhir**: Sistem secara mutlak menolak penonaktifan atau penurunan role akun SuperAdmin jika total akun SuperAdmin aktif berjumlah $\le 1$ (SEC-16).
5. **Session Invalidation**: Saat SuperAdmin melakukan nonaktif staf atau reset password, sistem memicu invalidasi sesi dengan merevoke token/session database yang bersangkutan (SEC-03, SEC-04).

### Pertanyaan Terbuka:
- Tidak ada pertanyaan kritis terbuka. Logika bisnis, kamus data, dan arsitektur otorisasi telah didefinisikan secara komprehensif di PRD, TDD, dan Addendum Keamanan MagKom.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Pendekatan Teknis:

| Aspek | Opsi A: Implementasi Modular Terintegrasi Bertahap (Terpilih - Recommended) | Opsi B: Pembangunan Add-on Terpisah |
| :--- | :--- | :--- |
| **Integrasi Pengalaman Pengguna (UX)** | **Seamless & Kohesif**: Fitur produktivitas disematkan langsung ke dalam halaman eksisting (misal tombol bulk action pada tabel logbook, template komentar pada modal reviu, panel chart pada tab laporan). | Tampilan terpecah di sub-halaman baru yang membuat navigasi pengguna semakin berjenjang dan lambat. |
| **Efisiensi Kode & Maintainability** | **Tinggi**: Mengoptimalkan controller dan service yang sudah ada (`LogbookReviewController`, `SuperAdmin\PengaturanController`, `Admin\LaporanController`) dengan metode baru yang clean. | Terjadi duplikasi query dan logika bisnis di berbagai file controller baru. |
| **Keamanan & Guardrails** | **Terpusat**: Memanfaatkan Policy dan middleware eksisting (`EnsureUserHasRole`, `AuditService`) untuk mencatat aksi massal ke audit log secara atomik. | Rentan celah otorisasi karena rute baru mungkin melewatkan guardrail penting. |

> **Keputusan**: Mengadopsi **Opsi A (Implementasi Modular Terintegrasi Bertahap)** untuk menjamin fungsionalitas langsung terasa manfaatnya di layar kerja harian pengguna tanpa mengganggu kestabilan sistem eksisting.

---

## 4. Matriks Perubahan File

| Modul / Pilar | File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :---: | :--- |
| **Pilar 1: Pembimbing** | `app/Http/Controllers/Pembimbing/LogbookReviewController.php` | Modify | Menambahkan method `bulkReview(Request $request)` untuk persetujuan logbook massal berstatus terkirim dengan pencatatan audit log per baris. |
| **Pilar 1: Pembimbing** | `resources/views/pembimbing/logbook/index.blade.php` | Modify | Menambahkan checkbox per baris, floating bulk action bar ("Setujui N Terpilih"), dan dropdown canned responses pada modal reviu individual. |
| **Pilar 1: Pembimbing** | `resources/views/pembimbing/tugas/show.blade.php` | Modify | Menambahkan quick feedback selector (canned responses) pada modal evaluasi progres tugas peserta. |
| **Pilar 1: Pembimbing** | `app/Http/Controllers/Pembimbing/PenilaianController.php` | Modify | Menambahkan method `exportRekapBidang()` untuk ekspor CSV rekapitulasi penilaian mentee dalam bidang bersangkutan (terproteksi formula injection). |
| **Pilar 1: Pembimbing** | `routes/web.php` | Modify | Menambahkan rute `post('/pembimbing/logbook/bulk-review')` dan `get('/pembimbing/penilaian/ekspor-bidang')`. |
| **Pilar 2: SuperAdmin** | `app/Http/Controllers/SuperAdmin/StafController.php` | Modify | Menambahkan guardrail pencegahan penonaktifan SuperAdmin terakhir (SEC-16) dan invalidasi sesi paksa saat reset password/nonaktif akun (SEC-03, SEC-04). |
| **Pilar 2: SuperAdmin** | `app/Http/Controllers/SuperAdmin/PengaturanController.php` | Modify | Validasi dan persistensi 8 parameter kebijakan operasional sistem dengan cache invalidation `Pengaturan::clearCache()`. |
| **Pilar 2: SuperAdmin** | `resources/views/superadmin/pengaturan/index.blade.php` | Modify | Memoles tampilan kartu form konfigurasi sistem bergaya Sleman Blue dengan alert feedback dan helper keterangan aturan bisnis. |
| **Pilar 2: SuperAdmin** | `app/Http/Controllers/SuperAdmin/AuditLogController.php` | Modify | Menambahkan method `exportCsv(Request $request)` dengan filter tanggal, aktor, dan tipe audit ber-sanitasi CSV (F-13, AC-13-4). |
| **Pilar 2: SuperAdmin** | `resources/views/superadmin/audit-log/index.blade.php` | Modify | Menambahkan tombol unduh ekspor audit log CSV sesuai filter aktif saat ini. |
| **Pilar 3: Laporan** | `resources/views/admin/laporan/index.blade.php` | Modify | Menambahkan komponen visual analytics (grafik distribusi kuota terisi vs sisa per jurusan dan rasio penerimaan) menggunakan chart styling modern Sleman Blue. |
| **Pilar 3: Laporan** | `app/Http/Controllers/Admin/PenempatanController.php` | Modify | Menambahkan fitur batch download/rekapitulasi surat keterangan diterima magang per bulan/jurusan (F-16). |
| **Pilar 3: Laporan** | `app/Http/Controllers/Admin/WaitingListController.php` | Modify | Menambahkan method penutupan antrean massal saat bulan dimulai (`tutupAntreanMassal`, AC-04-7) dan promosi cepat dengan validasi kuota sisa. |
| **Testing** | `tests/Feature/NextLevelEnhancementsTest.php` | Create | Suite pengujian otomatis untuk memvalidasi: bulk review logbook, pencegahan nonaktif last superadmin, update konfigurasi pengaturan, ekspor audit log berfilter, dan visual analytics. |

---

## 5. Langkah Implementasi Bertahap

### Fase 1: Produktivitas & Mentoring Tools Pembimbing (Pilar 1)
- [x] **Langkah 1.1**: Update `app/Http/Controllers/Pembimbing/LogbookReviewController.php`:
  - Buat method `bulkReview(Request $request)` yang menerima array `logbook_ids`.
  - Pastikan setiap entri logbook diverifikasi kepemilikannya (`pembimbing_id === Auth::id()`), berstatus `terkirim`, dan ubah status menjadi `disetujui`.
  - Catat setiap perubahan ke `AuditService` secara atomik.
- [x] **Langkah 1.2**: Sempurnakan `resources/views/pembimbing/logbook/index.blade.php`:
  - Tambahkan checkbox "Pilih Semua" di header tabel dan checkbox individual per baris berstatus `terkirim`.
  - Hadirkan floating bar aksi massal (*sticky bottom bar*) bertema Sleman Blue dengan tombol "Setujui N Entri Logbook".
  - Sediakan dropdown "Komentar Cepat" (Canned Responses) pada modal reviu cepat individual:
    - *"Laporan kegiatan terverifikasi lengkap dan sesuai arahan."*
    - *"Mohon sertakan bukti hasil kerja atau tautan berkas pendukung."*
    - *"Uraian aktivitas terlalu singkat, mohon diperjelas rincian tugas harian."*
- [x] **Langkah 1.3**: Sempurnakan `resources/views/pembimbing/tugas/show.blade.php`:
  - Tambahkan pilihan template umpan balik cepat pada modal ulasan progres tugas:
    - *"Hasil pekerjaan telah sesuai dengan spesifikasi tugas."*
    - *"Perlu perbaikan format dokumen dan penambahan dokumentasi pengujian."*
- [x] **Langkah 1.4**: Tambahkan fitur Ekspor Rekap Nilai Bidang di `app/Http/Controllers/Pembimbing/PenilaianController.php`:
  - Method `exportRekapBidang()`: Ekspor CSV seluruh mentee bimbingan dengan data: Nama, Institusi, Jurusan, Total Tugas Selesai, Disiplin Logbook, Nilai Akhir, Predikat, dan Status Kelulusan.
  - Sanitasi karakter formula injection (`'`, `'=`, `'+`, `'-`, `'@`).

### Fase 2: Tata Kelola & Pengamanan Lanjutan SuperAdmin (Pilar 2)
- [x] **Langkah 2.1**: Update `app/Http/Controllers/SuperAdmin/StafController.php`:
  - Pasang guardrail **Last SuperAdmin Protection** (SEC-16): Cegah penonaktifan atau mutasi role jika `User::where('role', 'superadmin')->where('status_aktif', true)->count() <= 1`.
  - Pasang **Session Invalidation** (SEC-03, SEC-04): Bersihkan token remember dan sesi database pengguna target saat status dinonaktifkan atau password direset.
- [x] **Langkah 2.2**: Hubungkan dan poles `app/Http/Controllers/SuperAdmin/PengaturanController.php` & `resources/views/superadmin/pengaturan/index.blade.php`:
  - Validasi parameter sistem: `durasi_magang_default`, `max_hari_logbook_mundur` (1–7 hari), `masa_tenggang_hari` (1–90 hari), `kuota_umum_maks_per_bulan`, `batas_bimbingan_per_pembimbing`.
  - Panggil `Pengaturan::clearCache()` setelah pembaruan agar perubahan langsung aktif seketika di seluruh aplikasi.
- [x] **Langkah 2.3**: Sempurnakan `app/Http/Controllers/SuperAdmin/AuditLogController.php` & `resources/views/superadmin/audit-log/index.blade.php`:
  - Method `exportCsv(Request $request)`: Mengunduh baris audit log sesuai query filter pencarian aktif (rentang tanggal, tipe, aktor).
  - Terapkan sanitasi formula injection pada detail aksi audit.

### Fase 3: Intelijen Operasional Administrasi & Waiting List FIFO (Pilar 3)
- [x] **Langkah 3.1**: Tambahkan Visual Analytics di `resources/views/admin/laporan/index.blade.php`:
  - Hadirkan 2 widget visual interaktif di atas tab laporan:
    1. *Tingkat Keterisian Kuota per Jurusan*: Bar progress interaktif Sleman Blue menampilkan rasio `terisi / jumlah` dan indikator status kuota (Tersedia / Penuh / Waiting List).
    2. *Proporsi Asal Peserta*: Donut / Segment chart ringkas perbandingan peserta jenjang SMK vs Mahasiswa (D3/S1).
- [x] **Langkah 3.2**: Optimalkan Manajemen Waiting List di `app/Http/Controllers/Admin/WaitingListController.php`:
  - Action `tutupAntreanMassal(BulanMagang $bulan)` (AC-04-7): Menolak secara massal seluruh pendaftaran `waiting_list` pada bulan yang sudah berjalan dengan catatan otomatis resmi.
  - Tampilkan modal konfirmasi kapasitas kuota saat mempromosikan pendaftar waiting list ke status diterima.
- [x] **Langkah 3.3**: Tambahkan Rekapitulasi Berkas Surat Diterima di `app/Http/Controllers/Admin/PenempatanController.php`:
  - Tombol aksi rekap untuk mencetak daftar peserta penerima surat penerimaan per periode bulan magang.

### Fase 4: Automated Testing & Verifikasi Penuh
- [x] **Langkah 4.1**: Buat test suite komprehensif `tests/Feature/NextLevelEnhancementsTest.php`:
  - Test pembimbing bulk approve logbook & audit recording.
  - Test pembimbing ekspor rekap bimbingan bidang anti-formula injection.
  - Test pencegahan penonaktifan SuperAdmin terakhir (SEC-16).
  - Test update form pengaturan sistem & cache clearing.
  - Test ekspor audit log berfilter ke CSV.
  - Test tutup antrean waiting list massal awal bulan (AC-04-7).
- [x] **Langkah 4.2**: Jalankan seluruh regression test suite (`php artisan test`) memastikan 207+ tests tetap 100% Green (Hasil: 218 passed).
- [x] **Langkah 4.3**: Verifikasi visual di browser melalui `browser_subagent` untuk memastikan UI bulk logbook bar, quick snippets, panel pengaturan, dan visual analytics tampil sempurna.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - `php artisan test tests/Feature/NextLevelEnhancementsTest.php`
   - `php artisan test tests/Feature/PembimbingFullJobDeskTest.php`
   - `php artisan test tests/Feature/SuperAdminPortalVisualTest.php`
   - `php artisan test tests/Feature/AdminLaporanDanArsipTest.php`
2. **Skenario Pengujian Unit & Otorisasi**:
   - Pembimbing memilih 3 logbook status `terkirim` $\rightarrow$ klik setujui massal $\rightarrow$ ketiga logbook menjadi `disetujui`, audit log tercatat 3 kali.
   - Pembimbing memilih logbook milik mentee pembimbing lain $\rightarrow$ ditolak 403 (anti-IDOR).
   - SuperAdmin mencoba menonaktifkan dirinya sendiri saat ia adalah satu-satunya SuperAdmin aktif $\rightarrow$ gagal dengan pesan error validasi (SEC-16).
   - SuperAdmin mengubah parameter `max_hari_logbook_mundur` dari 3 menjadi 5 $\rightarrow$ pengaturan tersimpan, cache ter-refresh, dan aturan logbook mengikuti parameter baru.
   - SuperAdmin mengunduh ekspor CSV audit log $\rightarrow$ header CSV valid, sel dengan karakter diawali `=`, `+`, `-`, `@` diawali kutip tunggal (`'`).
   - Admin menutup antrean waiting list bulan yang sudah dimulai $\rightarrow$ seluruh antrean waiting list berubah status menjadi `ditolak` dengan catatan otomatis (AC-04-7).
3. **Verifikasi Visual Browser**:
   - Tampilan floating bulk bar pada tabel logbook pembimbing.
   - Tampilan modal quick feedback canned responses.
   - Tampilan kartu formulir pengaturan kebijakan sistem.
   - Tampilan visual progress bar dan visual chart keterisian kuota pada dasbor laporan.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat Dampak | Strategi Mitigasi |
| :--- | :---: | :--- |
| **Aksi massal logbook membebani memori jika dipilih ratusan baris sekaligus** | Sedang | Membatasi aksi bulk approval maksimal 50 baris per eksekusi dan menggunakan database chunking / transactional update. |
| **Sistem terkunci tanpa SuperAdmin aktif jika akun satu-satunya dinonaktifkan** | Kritis | Menegakkan pengecekan mutlak `User::where('role', 'superadmin')->where('status_aktif', true)->count() > 1` sebelum mengizinkan aksi update status atau role. |
| **Cache konfigurasi tidak sinkron antar server/request** | Sedang | Memanggil `Pengaturan::clearCache()` secara eksplisit di dalam blok transaksi penyimpanan model `Pengaturan`. |
| **Formula injection pada CSV ekspor audit log dan rekap bimbingan** | Tinggi | Menjalankan string sanitizer terpadu yang mem-prefix tanda petik tunggal (`'`) pada data sel yang berawalan `=`, `+`, `-`, `@`. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Fitur Bulk Approval Logbook dan Canned Responses berfungsi mulus pada Portal Pembimbing.
- [x] Fitur Ekspor Rekap Penilaian Bidang tersedia dan aman dari formula injection.
- [x] Guardrail Last SuperAdmin Protection dan invalidasi sesi aktif terpasang pada manajemen staf.
- [x] Panel Pengaturan Konfigurasi Sistem (`/superadmin/pengaturan`) dapat diperbarui secara visual dan langsung berefek ke sistem.
- [x] Fitur Ekspor Audit Log CSV ber-filter tanggal/tipe berfungsi dengan baik.
- [x] Visual Analytics keterisian kuota terintegrasi rapi pada Hub Laporan (`/admin/laporan`).
- [x] Seluruh unit dan feature tests pada `NextLevelEnhancementsTest.php` dan seluruh test regresi (218 tests) lulus 100% (**Green**).
- [x] Verifikasi visual di browser membuktikan antarmuka responsif, estetik bertema Sleman Blue `#0641CC`, dan bebas error layout.

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**:
  - *(Akan dicatat saat eksekusi implementasi)*
- **Saran Lanjutan (Out of Scope)**:
  - Integrasi notifikasi Telegram/WhatsApp Bot resmi Pemkab Sleman untuk alert real-time pendaftaran kuota penuh atau reminder logbook harian (jika diizinkan kebijakan dinas di masa depan).
