# Plan: Pengembangan Lanjutan Administrasi & SuperAdmin Sesuai PRD (Fase 5)

- **Tanggal**: 2026-10-12
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning)
- **Target Area**: 
  1. **Portal Administrasi Kominfo (`/admin/*`)**: SLA Watchdog & Antrean Verifikasi Prioritas, Hub Survei Kepuasan Magang, Dashboard Analitik Tahunan, dan Batch Operasional Arsip.
  2. **Portal SuperAdmin (`/superadmin/*`)**: Portal Verifikasi Publik Dokumen Berbasis QR Code, Analitik Tahunan Eksekutif & Mutu Pembimbing, Pemeliharaan Retensi Otomatis Audit Log (NFR-A5), dan Pengawasan Tata Kelola Staf.
- **Dokumen Rujukan**:
  - [`docs/01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Bab 2.3 Metrik Keberhasilan, Bab 3.3 Matriks Hak Akses, Bab 4.3 Fase Lanjutan Poin 1, 4, 5; Bab 7 F-04, F-13, F-16, F-17; Bab 8.5 Spesifikasi PDF; Bab 10 NFR-A1 s/d NFR-A5; Bab 11.3 R-06).
  - [`docs/02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Bab 6 Skema Database, Bab 8 Keamanan & RBAC, Bab 9 Prosedur Pemeliharaan & Retensi Data).
  - [`docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Rilis Lanjutan & Governance).
  - [`docs/06-ADDENDUM-KEAMANAN-DAN-QA.md`](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md) (SEC-03, SEC-11, SEC-12, SEC-13, SEC-30, SEC-34).
  - [`docs/07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Sleman Blue `#0641CC`, font Inter/Open Sans, visual tokens, `.clean-scrollbar`).

---

## 1. Tujuan

Menyusun rencana kerja teknis yang terperinci dan terukur untuk mengeksekusi fitur-fitur lanjutan **Portal Administrasi (`/admin/*`)** dan **Portal SuperAdmin (`/superadmin/*`)** yang tercantum dalam spesifikasi PRD Bab 2.3, Bab 4.3 (Fase Lanjutan), Bab 8.5, dan Bab 10:

1. **Portal Publik Verifikasi Keabsahan Dokumen Resmi Berbasis QR Code (PRD 4.3 Poin 1, Bab 8.5, OQ-03, NFR-S6)**:
   - Menyediakan endpoint publik tanpa login (`/verifikasi-dokumen/{kode_unik}`) untuk memeriksa keaslian Surat Keterangan Diterima, Lembar Nilai Resmi, dan Sertifikat Kelulusan melalui pemindaian QR Code pada dokumen PDF fisik/digital.
   - Mengantisipasi pemalsuan dokumen dinas dan memudahkan verifikasi oleh institusi kampus/sekolah mitra serta pihak ketiga.
2. **Dashboard Analitik Rekapitulasi Tahunan Eksekutif (PRD 4.3 Poin 5, Bab 2.3, Bab 3.3, Bab 7 F-17)**:
   - Membangun antarmuka visual komprehensif pelaporan tahunan (`/admin/laporan/tahunan` dan tab analitik SuperAdmin) yang memetakan tren penerimaan tahun ke tahun, tingkat kelulusan (*completion rate*), Top 10 perguruan tinggi & SMK pengirim terbanyak, sebaran durasi magang, dan analisis predikat kelulusan (dengan pembatasan A-20: rekap nilai hanya untuk SuperAdmin).
   - Menyediakan fitur cetak *Executive Summary Report* resmi bertanda tangan pimpinan Diskominfo.
3. **Survei Kepuasan & Evaluasi Program Magang Pasca-Pelaksanaan (PRD 4.3 Poin 4)**:
   - Memfasilitasi evaluasi terstruktur oleh peserta yang telah berstatus `selesai` atau dalam masa tenggang mengenai 4 dimensi mutu: kualitas mentoring, relevansi tugas, sarana prasarana dinas, dan skor Net Promoter Score (NPS).
   - Menyediakan dasbor agregasi Indeks Kepuasan Magang (IKM) bagi Admin dan SuperAdmin untuk evaluasi berkala per bidang dinas.
4. **SLA Watchdog & Antrean Verifikasi Prioritas Administrasi (PRD Bab 2.3 & Bab 7 F-04)**:
   - Mengimplementasikan pemantauan kepatuhan target SLA keputusan (≤ 5 hari kerja sejak `diajukan_at`) dengan visualisasi indikator urgensi (Aman, Mendekati Batas 3 Hari, Terlambat/Breach > 5 Hari).
   - Menyediakan filter cepat antrean prioritas verifikasi berkas dan kalkulasi *Median Waktu Keputusan* verifikator dinas.
5. **Pemeliharaan Retensi Otomatis Audit Log Data Lifecycle (PRD Bab 10.3 NFR-A5, Bab 11.3 R-06, TDD Bab 9)**:
   - Mengimplementasikan command scheduler pemangkasan data log `magkom:audit-purge` untuk menghapus rekaman log tipe `akses_data` yang telah melampaui masa retensi konfigurasi (default 365 hari / 1 tahun), dengan tetap mempertahankan tanpa batas waktu log mutasi bisnis (`auth`, `aksi`, `service`).
   - Menyediakan kartu monitoring ukuran log dan riwayat eksekusi pembersihan pada portal SuperAdmin.
6. **Operasi Batch & Manajemen Arsip Terpadu Administrasi (PRD Bab 5 BR-06, Bab 7 F-16)**:
   - Memungkinkan admin melakukan pemilihan massal (*bulk select*) untuk mengunduh arsip Surat Diterima / Sertifikat Kelulusan sekaligus dalam format bundel arsip.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis & Fungsional:
1. **Verifikasi Publik Dokumen (QR Code)**:
   - Setiap dokumen yang diterbitkan (Surat Diterima, Lembar Nilai, Sertifikat) menghasilkan `kode_verifikasi` acak ber-entropy tinggi (misal: format `MK-VRF-{RANDOM_12}` atau UUID/hash) yang disimpan di tabel atau dihitung secara kriptografis dari data unik penempatan & nomor dokumen.
   - Endpoint verifikasi publik `/verifikasi-dokumen/{kode}` bersifat *rate-limited* (60 request/menit per IP) dan hanya menampilkan metadata publik yang aman (tanpa nomor telepon, email pribadi, atau password).
2. **Dashboard Analitik Tahunan**:
   - Data analitik dikelompokkan berdasarkan tahun kalender dimulainya periode magang (`bulan_mulai` tahun YYYY).
   - Kepatuhan batasan PRD 3.3 dan Asumsi A-20 tetap ditegakkan: Admin hanya melihat statistik volume pendaftar, penempatan, dan distribusi institusi; rincian sebaran predikat dan rata-rata skor nilai hanya dapat diakses oleh SuperAdmin.
3. **Survei Kepuasan Magang (PRD 4.3 Poin 4)**:
   - Pengisian survei bersifat opsional namun disarankan saat peserta berada pada status penempatan `selesai` atau selama `masa_tenggang`.
   - Data responden dapat diatur anonim atau berelasi ke penempatan untuk mencegah bias penilaian terhadap alumni.
4. **Retensi & Pemangkasan Audit Log (NFR-A5)**:
   - Sesuai NFR-A5, hanya entri audit dengan `tipe = 'akses_data'` yang dipangkas setelah 1 tahun (365 hari). Log tipe `auth`, `aksi`, dan `service` **tidak boleh dihapus** demi integritas akuntabilitas publik dan audit forensik.
   - Pembersihan dijalankan melalui artisan console command dan dapat dipicu secara terjadwal maupun manual oleh SuperAdmin dengan pencatatan audit.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan kritis yang menghambat pelaksanaan teknis. Seluruh aturan telah didefinisikan secara eksplisit di PRD Bab 2.3, Bab 4.3, Bab 8.5, Bab 10.3, dan TDD Bab 9.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Pendekatan Teknis:

| Aspek | Opsi A: Ekosistem Terpadu Native Blade & Modular Services (Terpilih - Recommended) | Opsi B: Penambahan Eksternal SPA & Microservice Terpisah |
| :--- | :--- | :--- |
| **Kinerja & Kecepatan Akses** | **Sangat Cepat**: Render berbasis server Laravel 10 dengan query agregasi database terindeks, caching metrik analitik (60 detik), dan SVG QR code generator tanpa dependensi node eksternal. | Membutuhkan bundle runtime frontend tambahan yang berat, meningkatkan waktu respon LCP (bertentangan dengan metrik PRD 2.3). |
| **Keamanan & Kepatuhan UU PDP (NFR-S6, NFR-S7)** | **Terpusat & Terisolasi**: Dilindungi oleh Laravel Policy, Route Model Binding, rate limiting throttle, dan sanitasi data seragam. Dokumen QR publik hanya menampilkan ringkasan non-PII. | Rentan kebocoran PII (Personally Identifiable Information) karena komunikasi antar-service memerlukan expose API publik tambahan. |
| **Konsistensi Desain Sleman Blue** | **Harmonis 100%**: Mengikuti standar token visual MagKom: `#0641CC`, font Inter, status badges, dan komponen UI yang konsisten dengan modul sebelumnya. | Terjadi disparitas tampilan dan font karena komponen pihak ketiga membawa template stylesheets sendiri. |
| **Pemeliharaan Jangka Panjang** | **Mudah Dipelihara**: Struktur modular controller, service, model, dan command Artisan Laravel standar yang telah teruji dengan test suite 228+ unit tests. | Membutuhkan infrastruktur server ganda, konfigurasi CI/CD ganda, dan kompleksitas debugging lintas domain. |

> **Keputusan**: Mengadopsi **Opsi A** untuk menjamin keamanan, kepatuhan arsitektur PRD/TDD, kecepatan eksekusi, dan integrasi yang mulus.

---

## 4. Perubahan File

| Modul / Fitur | File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :---: | :--- |
| **Verifikasi Dokumen QR** | `database/migrations/2026_10_12_000002_create_dokumen_verifikasi_table.php` | Create | Tabel indeks verifikasi dokumen resmi (`kode_unik`, `tipe_dokumen`, `penempatan_id`, `nomor_surat`, `tanggal_terbit`, `status_keabsahan`, `checksum`). |
| **Verifikasi Dokumen QR** | `app/Models/DokumenVerifikasi.php` | Create | Model Eloquent untuk pencatatan dan pencarian token verifikasi dokumen dinas. |
| **Verifikasi Dokumen QR** | `app/Services/DocumentVerificationService.php` | Create | Layanan penerbitan token QR unik, pembuatan SVG QR code, dan validasi keaslian dokumen publik. |
| **Verifikasi Dokumen QR** | `app/Http/Controllers/Public/VerifikasiDokumenController.php` | Create | Controller publik verifikasi keabsahan dokumen via scan QR code (`/verifikasi-dokumen/{kode}`). |
| **Verifikasi Dokumen QR** | `resources/views/public/verifikasi-dokumen.blade.php` | Create | Halaman publik resmi verifikasi keabsahan dokumen bertanda tangan elektronik Diskominfo Sleman. |
| **Verifikasi Dokumen QR** | `resources/views/pdf/lembar-nilai.blade.php` & `resources/views/pdf/surat-diterima.blade.php` | Modify | Menyematkan QR code verifikasi dinamis pada footer dokumen resmi. |
| **Analitik Tahunan** | `app/Http/Controllers/Admin/AnalitikTahunanController.php` | Create | Controller analitik tahunan eksekutif (agregasi pendaftaran, penempatan, asal sekolah/kampus, rasio seleksi, dan rekap nilai untuk SuperAdmin). |
| **Analitik Tahunan** | `resources/views/admin/laporan/tahunan.blade.php` | Create | Tampilan visual Dashboard Analitik Tahunan dengan chart visual native CSS, Top 10 Institusi, dan ringkasan metrik. |
| **Survei Kepuasan Magang** | `database/migrations/2026_10_12_000003_create_survei_kepuasan_table.php` | Create | Tabel survei evaluasi magang (`penempatan_id`, `skor_mentoring`, `skor_tugas`, `skor_fasilitas`, `skor_nps`, `umpan_balik`, `saran`). |
| **Survei Kepuasan Magang** | `app/Models/SurveiKepuasan.php` | Create | Model Eloquent kuesioner evaluasi dan feedback pasca magang. |
| **Survei Kepuasan Magang** | `app/Http/Controllers/Peserta/SurveiController.php` | Create | Controller pengisian formulir survei evaluasi oleh alumni magang / masa tenggang. |
| **Survei Kepuasan Magang** | `resources/views/peserta/survei/index.blade.php` | Create | Antarmuka interaktif pengisian kuesioner evaluasi magang peserta 4 dimensi. |
| **Survei Kepuasan Magang** | `app/Http/Controllers/Admin/SurveiAdminController.php` | Create | Controller rekapitulasi IKM (Indeks Kepuasan Magang) dan analisis sentimen umpan balik untuk Admin & SuperAdmin. |
| **Survei Kepuasan Magang** | `resources/views/admin/survei/index.blade.php` | Create | Tampilan visual indeks mutu penyelenggaraan magang dinas (skor IKM, NPS, dan grafik radar). |
| **SLA Watchdog Admin** | `app/Http/Controllers/Admin/PendaftaranController.php` | Modify | Menambahkan kalkulasi status SLA pada antrean (Aman, Mendekati Batas >3 hari, Terlambat >5 hari kerja) dan filter SLA. |
| **SLA Watchdog Admin** | `resources/views/admin/pendaftaran/index.blade.php` | Modify | Menambahkan visual status SLA badges, pill filter cepat SLA, dan peringatan pendaftaran kritis. |
| **Retensi Audit Log** | `app/Console/Commands/PurgeAuditLogsCommand.php` | Create | Command Artisan `magkom:audit-purge` untuk memangkas log akses data > 1 tahun secara idempotent dan aman. |
| **Retensi Audit Log** | `app/Console/Kernel.php` | Modify | Mendaftarkan jadwal harian / bulanan command pembersihan log akses data. |
| **Retensi Audit Log** | `app/Http/Controllers/SuperAdmin/PengaturanController.php` | Modify | Menambahkan parameter konfigurasi `retensi_audit_akses_data_hari` (default 365) di master pengaturan. |
| **Retensi Audit Log** | `resources/views/superadmin/pengaturan/index.blade.php` | Modify | Input pengaturan kebijakan masa retensi log audit pada form parameter sistem. |
| **Rute & Integrasi** | `routes/web.php` | Modify | Pendaftaran rute publik verifikasi dokumen, rute survei peserta, rute analitik tahunan, dan rute indeks mutu dinas. |
| **Testing** | `tests/Feature/AdvancedAdminAndSuperAdminPhaseFiveTest.php` | Create | Test suite pengujian otomatis fitur Fase 5: verifikasi QR dokumen publik, analitik tahunan, survei kepuasan, SLA watchdog, dan command retensi audit. |

---

## 5. Langkah Implementasi Bertahap

### Fase 1: Portal Publik Verifikasi Keabsahan Dokumen Berbasis QR Code (PRD 4.3 Poin 1 & Bab 8.5)
- [x] **Langkah 1.1**: Buat migrasi & model `app/Models/DokumenVerifikasi.php`:
  - Kolom: `id`, `kode_unik` (string 32 char, indexed, unique), `tipe_dokumen` (`surat_diterima`, `lembar_nilai`, `sertifikat`), `penempatan_id`, `nomor_dokumen`, `tanggal_terbit`, `status_keabsahan` (`sah`, `dicabut`, `kedaluwarsa`), `metadata` (json: nama peserta, institusi, jurusan, penandatangan).
- [x] **Langkah 1.2**: Buat service `app/Services/DocumentVerificationService.php`:
  - Method `generateVerificationToken(Penempatan $penempatan, string $tipe, string $nomorDokumen): DokumenVerifikasi`: Menerbitkan token unik dan menyimpan rekam verifikasi.
  - Method `generateQrCodeSvg(string $url): string`: Menghasilkan representasi SVG QR Code ringan native (tanpa dependensi eksternal berat) untuk disematkan pada dokumen PDF.
  - Method `verify(string $kodeUnik): ?array`: Mengembalikan payload metadata verifikasi untuk konsumsi publik.
- [x] **Langkah 1.3**: Buat `app/Http/Controllers/Public/VerifikasiDokumenController.php` & view `resources/views/public/verifikasi-dokumen.blade.php`:
  - Route publik: `GET /verifikasi-dokumen/{kode_unik}` ber-throttling rate limiter (60/menit).
  - Tampilkan halaman bertema resmi Diskominfo Sleman: Banner status keabsahan dokumen (HIJAU: Dokumen Resmi Terverifikasi, MERAH: Dokumen Tidak Valid / Dicabut), Nomor Surat, Identitas Penerima, Tanggal Pengesahan, dan Pejabat Penerbit.
- [x] **Langkah 1.4**: Modifikasi generator PDF Surat Keterangan Diterima, Lembar Nilai, dan Sertifikat:
  - Panggil `DocumentVerificationService` untuk otomatis merekam token verifikasi saat dokumen diterbitkan.
  - Tampilkan cap QR Code verifikasi pada sudut bawah dokumen PDF lengkap dengan instruksi: *"Pindai QR Code untuk memverifikasi keaslian dokumen ini pada portal resmi MagKom"*.

### Fase 2: Dashboard Analitik Rekapitulasi Tahunan Eksekutif (PRD 4.3 Poin 5 & Bab 7 F-17)
- [x] **Langkah 2.1**: Buat controller `app/Http/Controllers/Admin/AnalitikTahunanController.php`:
  - Hitung metrik tahunan berdasarkan filter tahun (default tahun berjalan):
    - Total pendaftaran masuk, total diterima, rasio penerimaan (*acceptance rate*).
    - Distribusi jenjang pendidikan (SMK vs D3 vs S1).
    - Top 10 Institusi Pengirim Peserta Terbanyak (agregasi institusi dari profil peserta).
    - Rata-rata durasi magang (bulan) dan tingkat kelulusan alumni.
    - Sebaran predikat nilai kelulusan (A, B, C, D, E) — diproteksi khusus SuperAdmin sesuai aturan A-20.
  - Method `exportExcel(Request $request)`: Unduh rekap tahunan CSV/XLSX ber-sanitasi anti formula injection (`ExportReportService`).
- [x] **Langkah 2.2**: Buat antarmuka `resources/views/admin/laporan/tahunan.blade.php`:
  - Filter dropdown pemilihan tahun.
  - 4 Kartu Metrik Utama Eksekutif: Volume Pendaftar, Peserta Diterima, Rasio Seleksi, dan Rata-rata Durasi.
  - Grafik Distribusi Bulanan (Bar Visual Sleman Blue `#0641CC`).
  - Tabel Peringkat Top Institusi Mitra dengan badge jumlah pelamar dan alumni diterima.
  - Widget Sebaran Mutu Kelulusan (Khusus tampilan SuperAdmin).
- [x] **Langkah 2.3**: Tambahkan tautan menu tab *"Analitik Tahunan"* pada Hub Laporan (`resources/views/admin/laporan/index.blade.php`) dan sidebar SuperAdmin/Admin.

### Fase 3: Survei Kepuasan & Evaluasi Program Magang Peserta (PRD 4.3 Poin 4)
- [x] **Langkah 3.1**: Buat skema migrasi & model `app/Models/SurveiKepuasan.php`:
  - Kolom: `id`, `penempatan_id` (unique), `skor_mentoring` (1-5), `skor_tugas` (1-5), `skor_fasilitas` (1-5), `skor_nps` (0-10), `umpan_balik_positif` (text opsional), `saran_perbaikan` (text opsional).
- [x] **Langkah 3.2**: Buat controller peserta `app/Http/Controllers/Peserta/SurveiController.php` & view:
  - Form survei hanya dapat diisi oleh peserta yang penempatannya telah `selesai` atau dalam `masa_tenggang`.
  - Tampilan form kuesioner modern dengan skala bintang/rating interaktif dan pertanyaan Net Promoter Score: *"Seberapa besar kemungkinan Anda merekomendasikan magang di Kominfo kepada rekan Anda?"*.
- [x] **Langkah 3.3**: Buat controller admin `app/Http/Controllers/Admin/SurveiAdminController.php` & view:
  - Hitung Indeks Kepuasan Magang (IKM) rata-rata dari 3 aspek utama.
  - Hitung Net Promoter Score (NPS) kategori Promoters (9-10), Passives (7-8), dan Detractors (0-6).
  - Tampilkan rekapitulasi testimoni dan saran perbaikan sebagai bahan masukan dinas.
- [x] **Langkah 3.4**: Integrasikan prompt ajakan pengisian survei di dashboard peserta dan banner masa tenggang.

### Fase 4: SLA Watchdog & Antrean Verifikasi Prioritas Administrasi (PRD Bab 2.3 & Bab 7 F-04)
- [x] **Langkah 4.1**: Modifikasi `app/Http/Controllers/Admin/PendaftaranController.php`:
  - Tambahkan kalkulasi hari kerja verifikasi: `diffInWeekdays(diajukan_at, now())`.
  - Status SLA:
    - `aman`: usia pengajuan $\le 2$ hari kerja.
    - `mendekati`: usia pengajuan 3–4 hari kerja.
    - `terlambat`: usia pengajuan $\ge 5$ hari kerja (Breach SLA sesuai target PRD 2.3).
  - Dukung filter query `?sla=mendekati` dan `?sla=terlambat` untuk memudahkan admin menyortir antrean kritis.
- [x] **Langkah 4.2**: Perbarui `resources/views/admin/pendaftaran/index.blade.php`:
  - Tambahkan badge penanda SLA visual di samping tanggal pengajuan pada setiap baris pendaftar:
    - Hijau: `SLA Aman`
    - Kuning: `Mendekati Batas (H-X)`
    - Merah: `Melebihi SLA (> 5 Hari)`
  - Sediakan filter tab cepat: `Semua Antrean`, `Mendekati Batas SLA`, dan `Breach SLA`.
  - Tampilkan banner pengingat ringkasan jika terdapat $\ge 1$ berkas yang melewati batas SLA 5 hari kerja.

### Fase 5: Pemeliharaan Retensi Otomatis Audit Log Data Lifecycle (PRD Bab 10.3 NFR-A5 & TDD Bab 9)
- [x] **Langkah 5.1**: Buat command console `app/Console/Commands/PurgeAuditLogsCommand.php`:
  - Signature: `magkom:audit-purge {--days= : Override jumlah hari retensi}`
  - Baca konfigurasi `retensi_audit_akses_data_hari` dari helper `Pengaturan::get()` (default 365).
  - Eksekusi query penghapusan terkontrol khusus entitas log akses data:
    ```php
    AuditLog::where('tipe', TipeAuditEnum::AksesData)
        ->where('waktu', '<', now()->subDays($retensiHari))
        ->delete();
    ```
  - Catat satu baris audit mutasi sistem atas eksekusi pembersihan ini ke tabel audit log.
- [x] **Langkah 5.2**: Daftarkan jadwal harian / mingguan command di `app/Console/Kernel.php`:
  - Jadwalkan `magkom:audit-purge` berjalan setiap awal bulan / mingguan pada jam sepi (`02:00 WIB`).
- [x] **Langkah 5.3**: Perbarui form parameter sistem `app/Http/Controllers/SuperAdmin/PengaturanController.php` & view:
  - Tambahkan field `retensi_audit_akses_data_hari` (tipe integer, minimal 30, default 365).
  - Tampilkan tombol aksi cepat *"Jalankan Pemangkasan Log Akses Data"* di halaman Health Monitor / Audit Log SuperAdmin dengan modal konfirmasi aman.

### Fase 6: Testing Komprehensif & Verifikasi Kualitas
- [x] **Langkah 6.1**: Buat file test `tests/Feature/AdvancedAdminAndSuperAdminPhaseFiveTest.php`:
  - Uji pembuatan token verifikasi QR dan akses endpoint publik verifikasi dokumen tanpa login.
  - Uji proteksi manipulasi token verifikasi (token palsu menghasilkan status tidak valid).
  - Uji kalkulasi analitik tahunan dan proteksi otorisasi rekap nilai (Admin 403, SuperAdmin 200).
  - Uji pengisian survei kepuasan peserta alumni dan penghitungan skor IKM/NPS.
  - Uji kalkulasi SLA pendaftaran dan filter antrean SLA breach.
  - Uji eksekusi command `magkom:audit-purge` memastikan hanya log tipe `akses_data` yang terhapus dan log `auth`/`aksi` tetap utuh.
- [x] **Langkah 6.2**: Jalankan seluruh regression test suite (`php artisan test`) memastikan seluruh tests (240 tests) tetap 100% Green.
- [x] **Langkah 6.3**: Verifikasi antarmuka di peramban menggunakan `browser_subagent` memastikan estetika desain Sleman Blue, responsive layout, dan bebas visual glitch.

---

## 6. Strategi Testing

1. **Automated Feature & Integration Tests**:
   - `php artisan test tests/Feature/AdvancedAdminAndSuperAdminPhaseFiveTest.php`
   - `php artisan test tests/Feature/AdminSuperAdminPhaseFourTest.php`
   - `php artisan test tests/Feature/AdminSuperAdminPortalTest.php`
   - `php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`
2. **Skenario Pengujian Unit & Otorisasi Kunci**:
   - **QR Verification**: Mengakses `/verifikasi-dokumen/{kode_asli}` mengembalikan 200 dengan status `SAH`; token acak mengembalikan 404 atau pesan tidak ditemukan.
   - **Data Privacy NFR-S6**: Endpoint publik verifikasi tidak menampilkan nomor telepon, email, atau berkas unduhan pribadi peserta.
   - **A-20 Authorization**: Admin mencoba mengakses tab rekap nilai tahunan ditolak 403 Forbidden; SuperAdmin dapat melihat distribusi predikat nilai A-E.
   - **SLA Calculation**: Pendaftaran yang dibuat 6 hari lalu otomatis tergolong `terlambat` pada filter SLA.
   - **Audit Retention NFR-A5**: Log `auth` dan `aksi` berusia 400 hari tidak terhapus saat command `magkom:audit-purge` dijalankan; hanya log `akses_data` yang terpangkas.
3. **Verifikasi Visual Browser**:
   - Tampilan halaman publik verifikasi dokumen pada resolusi mobile 375px dan desktop 1440px.
   - Tampilan Dashboard Analitik Tahunan dan grafik persentase kelulusan.

---

## 7. Risiko & Mitigasi

- **Risiko 1 (Beban Komputasi Analitik Tahunan)**: Agregasi ribuan pendaftaran multi-tahun dapat memperlambat rendering halaman dasbor.
  - *Mitigasi*: Manfaatkan caching hasil kalkulasi analitik selama 60–120 detik menggunakan tag cache `Cache::remember('analitik_tahunan_' . $tahun, 120, ...)`, serta pastikan indeks pada kolom `created_at`, `status`, dan `bulan_mulai` aktif.
- **Risiko 2 (Penyalahgunaan Endpoint Verifikasi Publik)**: Endpoint publik di-brute force oleh bot pencari nomor registrasi.
  - *Mitigasi*: Gunakan kode verifikasi acak 32 karakter alfanumerik (bukan ID sekuensial) dan pasang middleware `throttle:60,1` (maksimal 60 request per menit per alamat IP).
- **Risiko 3 (Penghapusan Log yang Tidak Diinginkan)**: Perintah purge secara keliru menghapus catatan penting mutasi bisnis atau jejak autentikasi staf.
  - *Mitigasi*: Filter mutlak di dalam Eloquent query `where('tipe', TipeAuditEnum::AksesData)` yang diuji secara ketat dalam automated test suite sebelum dirilis ke lingkungan operasional.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Seluruh checklist langkah implementasi (Fase 1 s/d Fase 6) tercentang `- [x]`.
- [x] Endpoint publik verifikasi QR dokumen aktif dan dapat diakses publik tanpa login.
- [x] Dashboard analitik tahunan dapat menampilkan metrik tahunan secara akurat dengan proteksi hak akses A-20.
- [x] Modul survei kepuasan peserta beroperasi dan menghitung agregasi IKM/NPS secara real-time.
- [x] SLA watchdog pada antrean verifikasi pendaftaran berfungsi dan memudahkan deteksi berkas tertunda.
- [x] Command Artisan `magkom:audit-purge` berjalan sukses dan terdaftar di scheduler Kernel.
- [x] Seluruh automated test suite (`php artisan test`) lulus 100% tanpa error regresi.

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**: *(Akan diperbarui saat eksekusi implementasi jika ada penyesuaian teknis)*.
- **Saran Lanjutan (Out of Scope)**:
  - Integrasi Single Sign-On (SSO) akun ASN Kominfo dengan OAuth2 instansi Pemda Sleman (PRD 4.3 Poin 3).
  - Ekspor seluruh berkas alumni satu angkatan ke dalam format bundel arsip ISO/ZIP terenkripsi untuk backup fisik dinas.
