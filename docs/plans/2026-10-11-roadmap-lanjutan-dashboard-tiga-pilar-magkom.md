# Plan: Roadmap Lanjutan Dashboard Tiga Pilar MagKom (Administrasi, Pembimbing, dan SuperAdmin)

- **Tanggal**: 2026-10-11
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning)
- **Target Area**: 
  1. **Pilar 1 (Dashboard Administrasi)**: Kalender Operasional Periode Magang Dinas, Pusat Notifikasi SLA & Peringatan Offboarding/Selesai Magang, dan Hub Digital Arsip & Verifikasi Sertifikat Kelulusan.
  2. **Pilar 2 (Dashboard Pembimbing)**: Agenda Penjadwalan Mentoring & Notulensi Bimbingan, Radar Capaian Kompetensi Mentee (Early Warning Overdue/At-Risk), dan Lembar Rekap Portofolio Mentee Sekali-Klik.
  3. **Pilar 3 (Dashboard SuperAdmin)**: Panel Telemetri Kesehatan Sistem & Penggunaan Storage Berkas (`/storage/dokumen`), Deteksi Anomali Keamanan & Peringatan Forensik (Threat Radar), dan Pengelolaan Snapshot Cadangan Database (Backup Hub).
- **Dokumen Acuan**:
  - [`docs/01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (F-04, F-08, F-09, F-10, F-12, F-13, F-17; BR-07, BR-10, BR-13, BR-16; AC-04-7, AC-07-4, AC-11-7, AC-13-6)
  - [`docs/02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Bab 4 Struktur, Bab 5 Otorisasi Policy, Bab 6 Skema & Enum, Bab 8 Keamanan & Audit)
  - [`docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Rilis 3 & Rilis 4: Governance, Analytics, dan Operational Resilience)
  - [`docs/06-ADDENDUM-KEAMANAN-DAN-QA.md`](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md) (SEC-03, SEC-11, SEC-12, SEC-13, SEC-16, SEC-30, SEC-34)
  - [`docs/07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Sleman Blue `#0641CC`, font Inter/Open Sans, `.clean-scrollbar`, visual tokens)

---

## 1. Tujuan

Menyusun rencana kerja komprehensif tingkat lanjut untuk menyempurnakan dasbor operasional dari ketiga pilar utama MagKom:
1. **Memperkuat Efisiensi Administrasi (Dashboard Admin)**: Mengurangi beban koordinasi manual admin dinas dengan menyediakan kalender timeline visual, peringatan dini terhadap berkas yang melebihi SLA verifikasi (3 hari) serta peserta yang mendekati masa selesai magang (7–14 hari), dan otomatisasi penerbitan sertifikat kelulusan digital.
2. **Meningkatkan Kualitas Pendampingan (Dashboard Pembimbing)**: Memberikan pembimbing lapangan instrumen untuk menjadwalkan sesi bimbingan tatap muka/online, mencatat notulensi evaluasi mingguan, memantau radar kompetensi teknis mentee, serta mengekspor portofolio menyeluruh mentee dalam satu dokumen terpadu.
3. **Mewujudkan Resiliensi & Tata Kelola Enterprise (Dashboard SuperAdmin)**: Melengkapi dasbor eksekutif SuperAdmin dengan widget pemantauan telemetri kesehatan server (koneksi database, pemakaian storage file berkas, status job scheduler), radar deteksi anomali keamanan (serangan login gagal, mutasi di luar jam dinas), dan modul pencadangan snapshot basis data (*backup & disaster recovery*).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis & Fungsional:
1. **Kalender Operasional Administrasi**: Berbasis tanggal pembukaan, penutupan, dan pelaksanaan bulan magang aktif tanpa dependensi pustaka JavaScript yang berat (menggunakan grid kalender native Blade & CSS Sleman Blue).
2. **Agenda & Notulensi Bimbingan**: Dihubungkan langsung dengan tabel `penempatan` mentee aktif sehingga riwayat arahan pembimbing tersimpan kronologis dan dapat dibaca oleh mentee terkait.
3. **Penerbitan Sertifikat Kelulusan**: Hanya dapat diterbitkan bagi peserta yang memiliki status penempatan `selesai` dan telah memiliki `penilaian` dengan status `final` (BR-10).
4. **Telemetri Kesehatan SuperAdmin**: Mengambil metrik performa secara instan: status ping database, ruang disk terpakai di direktori `storage/app/public`, versi runtime PHP/Laravel, dan latensi request tanpa mengorbankan waktu muat dasbor (menggunakan cache 60 detik).
5. **Cadangan Basis Data (Backup Hub)**: Mendukung ekspor snapshot database SQLite lokal maupun dump MySQL yang terisolasi dan hanya dapat diakses oleh user dengan role `SuperAdmin` (SEC-03).

### Pertanyaan Terbuka:
- Tidak ada pertanyaan kritis terbuka. Alur kerja operasional dan spesifikasi keamanan telah selaras dengan PRD dan Design System Sleman.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Pendekatan Teknis:

| Aspek | Opsi A: Ekosistem Modular Terintegrasi Berbasis Blade & CSS Tokens (Terpilih - Recommended) | Opsi B: Pemasangan Microservices & Third-Party SPA Terpisah |
| :--- | :--- | :--- |
| **Beban Dependensi & Bundle Size** | **Ringan & Cepat**: Memanfaatkan komponen native Blade, CSS modern, dan Alpine/Vanilla JS yang sudah terpasang. Tidak menambah beban build NPM. | Menambah library pihak ketiga yang besar (seperti FullCalendar bundle berat, charting engine eksternal), memperlambat First Contentful Paint. |
| **Kepatuhan Desain System (Sleman Blue)** | **Konsisten 100%**: Langsung mengadopsi variabel warna `#0641CC`, font Inter, status badges, dan `.clean-scrollbar` yang seragam di seluruh portal. | Tampilan tidak harmonis karena komponen bawaan pihak ketiga membawa tema dan stylesheet sendiri yang bertabrakan. |
| **Keamanan & Guardrails (SEC-03, SEC-11, SEC-12)** | **Terpusat**: Terlindungi secara otomatis oleh policy otorisasi Laravel, middleware `EnsureUserHasRole`, dan pencatatan audit log `AuditService`. | Rentan celah otorisasi IDOR antar API service yang terpisah. |

> **Keputusan**: Mengadopsi **Opsi A** demi konsistensi desain, kecepatan akses, pemeliharaan jangka panjang, dan kepatuhan penuh terhadap standar arsitektur MagKom.

---

## 4. Matriks Perubahan File

| Modul / Pilar | File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :---: | :--- |
| **Pilar 1: Administrasi** | `app/Http/Controllers/Admin/DashboardController.php` | Modify | Menghitung metrik SLA pendaftaran tertunda ($>3$ hari), daftar peserta *upcoming offboarding* (7–14 hari), dan agenda periode magang aktif. |
| **Pilar 1: Administrasi** | `resources/views/admin/dashboard.blade.php` | Modify | Menambahkan widget kalender timeline gelombang magang dan panel peringatan dini SLA & Offboarding Tracker. |
| **Pilar 1: Administrasi** | `app/Http/Controllers/Admin/SertifikatController.php` | Create | Controller penerbitan dan unduh lembar Sertifikat Kelulusan Resmi Magang ber-nomor surat dinas & verifikasi QR code (AC-12). |
| **Pilar 1: Administrasi** | `resources/views/admin/sertifikat/index.blade.php` | Create | Halaman hub manajemen sertifikat kelulusan peserta berstatus selesai dengan preview dan cetak massal. |
| **Pilar 2: Pembimbing** | `app/Models/AgendaBimbingan.php` | Create | Model Eloquent untuk pencatatan sesi mentoring tatap muka/online, tanggal, lokasi, dan notulensi arahan pembimbing. |
| **Pilar 2: Pembimbing** | `database/migrations/2026_10_12_000001_create_agenda_bimbingan_table.php` | Create | Skema migrasi database untuk tabel `agenda_bimbingan` terelasi ke `penempatan` dan `users` (pembimbing). |
| **Pilar 2: Pembimbing** | `app/Http/Controllers/Pembimbing/DashboardController.php` | Modify | Menambahkan kalkulasi radar kompetensi rata-rata mentee, deteksi mentee *at-risk* (tugas overdue / logbook bolong), dan agenda mentoring mendatang. |
| **Pilar 2: Pembimbing** | `resources/views/pembimbing/dashboard.blade.php` | Modify | Menambahkan panel visual Radar Kompetensi & Capaian Belajar Mentee serta widget agenda mentoring terjadwal. |
| **Pilar 2: Pembimbing** | `app/Http/Controllers/Pembimbing/AgendaBimbinganController.php` | Create | Controller CRUD jadwal bimbingan dan simpan notulensi arahan mentoring lapangan. |
| **Pilar 2: Pembimbing** | `resources/views/pembimbing/agenda/index.blade.php` | Create | Tampilan kalender & daftar jadwal mentoring serta form modal input notulensi bimbingan. |
| **Pilar 3: SuperAdmin** | `app/Http/Controllers/SuperAdmin/HealthMonitorController.php` | Create | Controller telemetri kesehatan sistem: status database, storage berkas dokumen, utilisasi disk, dan status background cron. |
| **Pilar 3: SuperAdmin** | `resources/views/superadmin/dashboard.blade.php` | Modify | Menyematkan widget mini telemetri kesehatan sistem dan radar peringatan aktivitas keamanan anomali (*Threat Radar*). |
| **Pilar 3: SuperAdmin** | `resources/views/superadmin/health/index.blade.php` | Create | Halaman audit kesehatan infrastruktur sistem mandiri dengan diagnostics tool terisolasi. |
| **Pilar 3: SuperAdmin** | `app/Http/Controllers/SuperAdmin/BackupController.php` | Create | Controller pemicu pencadangan snapshot database dan unduh cadangan aman terotorisasi khusus SuperAdmin. |
| **Pilar 3: SuperAdmin** | `resources/views/superadmin/backup/index.blade.php` | Create | Antarmuka manajemen snapshot cadangan database dengan riwayat checksum SHA-256 dan ukuran berkas. |
| **Sistem & Rute** | `routes/web.php` | Modify | Pendaftaran rute-rute baru modul sertifikat admin, agenda bimbingan pembimbing, telemetri kesehatan superadmin, dan backup hub. |
| **Testing** | `tests/Feature/AdvancedThreePillarsDashboardTest.php` | Create | Automated feature test suite untuk menguji seluruh alur baru: SLA admin, agenda bimbingan pembimbing, telemetri health superadmin, dan backup database. |

---

## 5. Langkah Implementasi Bertahap

### Fase 1: Smart Operational Hub & Sertifikasi Digital (Dashboard Administrasi)
- [ ] **Langkah 1.1**: Update `app/Http/Controllers/Admin/DashboardController.php`:
  - Hitung antrean pendaftaran dengan `status = pending` yang `created_at <= now()->subDays(3)` sebagai indikator *Breached SLA*.
  - Ambil daftar penempatan aktif yang akan selesai dalam rentang $H-14$ sampai $H$ hari untuk tabel *Upcoming Offboarding Tracker*.
  - Siapkan data gelombang magang aktif dan kuota per bulan untuk widget timeline.
- [ ] **Langkah 1.2**: Perbarui `resources/views/admin/dashboard.blade.php`:
  - Tambahkan kartu *Pusat Pengingat & SLA Watchdog*: peringatan pendaftaran tertunda dan hitung mundur peserta selesai magang.
  - Tambahkan widget visual *Timeline Gelombang Magang* dengan penanda status pembukaan pendaftaran dan tanggal pelaksanaan.
- [ ] **Langkah 1.3**: Buat `app/Http/Controllers/Admin/SertifikatController.php` & `resources/views/admin/sertifikat/index.blade.php`:
  - Tampilkan daftar alumni magang (status penempatan `selesai` dan memiliki `penilaian` final).
  - Sediakan aksi unduh/cetak lembar Sertifikat Kelulusan Resmi Magang dengan nomor seri registrasi otomatis (`500.12/SRT/{ID}/DISKOMINFO/{TAHUN}`).

### Fase 2: Mentoring Scheduling & Competency Radar (Dashboard Pembimbing)
- [ ] **Langkah 2.1**: Buat migrasi & model `app/Models/AgendaBimbingan.php`:
  - Kolom: `id`, `penempatan_id`, `pembimbing_id`, `judul_kegiatan`, `tipe` (tatap_muka/daring), `tgl_bimbingan`, `jam_mulai`, `lokasi_ruang`, `notulensi_arahan`, `status` (terjadwal/selesai/dibatalkan).
  - Hubungkan relasi `belongsTo` ke `Penempatan` dan `User` (pembimbing).
- [ ] **Langkah 2.2**: Update `app/Http/Controllers/Pembimbing/DashboardController.php`:
  - Hitung matriks pencapaian nilai per kategori rubrik untuk membentuk peta radar kompetensi mentee bidang.
  - Deteksi mentee yang membutuhkan perhatian khusus (*Early Warning/At-Risk*): mentee dengan tugas `terlambat` atau logbook tidak terisi $> 3$ hari.
  - Ambil 5 agenda bimbingan mendatang untuk ditampilkan di dasbor.
- [ ] **Langkah 2.3**: Buat `app/Http/Controllers/Pembimbing/AgendaBimbinganController.php` & views:
  - Kelola CRUD agenda bimbingan: buat jadwal baru, ubah jadwal, dan input notulensi arahan mentoring pasca bimbingan.
  - Sediakan validasi anti-IDOR bahwa pembimbing hanya dapat menjadwalkan mentee di bawah bimbingannya sendiri.
- [ ] **Langkah 2.4**: Sempurnakan tampilan `resources/views/pembimbing/dashboard.blade.php`:
  - Hadirkan kartu *Matriks Capaian Belajar Mentee* dan widget *Jadwal Bimbingan Mendatang* dengan tombol pintasan tambah sesi.

### Fase 3: Telemetri Infrastruktur & Disaster Recovery (Dashboard SuperAdmin)
- [ ] **Langkah 3.1**: Buat `app/Http/Controllers/SuperAdmin/HealthMonitorController.php`:
  - Query health checks: koneksi database SQLite/MySQL, status storage berkas (`is_writable`, disk free space, total berkas dokumen), status cache runtime, dan scheduler heartbeat.
  - Catat akses audit trail saat diagnosa sistem dijalankan.
- [ ] **Langkah 3.2**: Buat `app/Http/Controllers/SuperAdmin/BackupController.php`:
  - Method `triggerSnapshot(Request $request)`: Memicu pencadangan basis data ke direktori aman terproteksi (`storage/app/backups/`).
  - Menghitung checksum SHA-256 dan ukuran file backup untuk integritas forensik data.
  - Method `download(string $filename)`: Unduh file backup terproteksi otorisasi ketat khusus SuperAdmin.
- [ ] **Langkah 3.3**: Sempurnakan `resources/views/superadmin/dashboard.blade.php`:
  - Sematkan kartu ringkasan *System Telemetry Pulse* (status DB, Storage, Versi Runtime, dan Uptime indicator) pada tab Sistem.
  - Hubungkan tombol pintasan cepat ke halaman `superadmin.health.index` dan `superadmin.backup.index`.

### Fase 4: Testing Komprehensif & Verifikasi Kualitas
- [ ] **Langkah 4.1**: Tulis suite pengujian otomatis `tests/Feature/AdvancedThreePillarsDashboardTest.php`:
  - Uji kalkulasi SLA pendaftaran tertunda dan upcoming offboarding di dasbor admin.
  - Uji penerbitan dan otorisasi sertifikat kelulusan alumni.
  - Uji pembuatan agenda bimbingan dan proteksi IDOR pembimbing.
  - Uji endpoint telemetri kesehatan sistem superadmin.
  - Uji mekanisme trigger backup database dan verifikasi otorisasi SuperAdmin.
- [ ] **Langkah 4.2**: Jalankan seluruh regression test suite (`php artisan test`) memastikan seluruh 218+ tests tetap 100% Green.
- [ ] **Langkah 4.3**: Verifikasi visual di browser melalui `browser_subagent` untuk memastikan estetika tampilan Sleman Blue pada ketiga dashboard.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - `php artisan test tests/Feature/AdvancedThreePillarsDashboardTest.php`
   - `php artisan test tests/Feature/NextLevelEnhancementsTest.php`
   - `php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`
   - `php artisan test tests/Feature/AdminSuperAdminPortalTest.php`
2. **Skenario Pengujian Unit & Otorisasi**:
   - Admin membuka dashboard $\rightarrow$ melihat alert jika ada pendaftaran belum diputuskan $>3$ hari.
   - Admin membuka modul sertifikat $\rightarrow$ hanya peserta berstatus `selesai` dengan nilai final yang memiliki tombol unduh sertifikat.
   - Pembimbing membuat agenda bimbingan untuk mentee miliknya $\rightarrow$ agenda tersimpan, mentee dapat melihat jadwal.
   - Pembimbing mencoba membuat agenda bimbingan untuk mentee pembimbing lain $\rightarrow$ ditolak 403 Forbidden (anti-IDOR).
   - SuperAdmin membuka halaman telemetri $\rightarrow$ indikator status DB `OK`, kapasitas storage tertera dengan akurat.
   - User non-SuperAdmin mencoba mengakses endpoint backup $\rightarrow$ ditolak 403 Forbidden.
3. **Verifikasi Visual Browser**:
   - Uji responsivitas widget timeline kalender pada layar desktop (1920px) dan tablet (768px).
   - Pastikan warna dan badge mengikuti design tokens Sleman Blue (`#0641CC`, `#16A34A`, `#B91C1C`).

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat Dampak | Strategi Mitigasi |
| :--- | :---: | :--- |
| **Pengecekan telemetri membebani I/O disk jika diakses berulang-ulang** | Sedang | Menerapkan caching cerdas selama 60 detik pada hasil kalkulasi telemetri storage dan kesehatan sistem. |
| **File backup database bocor atau dapat diakses publik via URL** | Kritis | Menyimpan file snapshot di direktori `storage/app/backups` (di luar `public/`) dan hanya melayani unduhan via StreamedResponse berotorisasi role `superadmin`. |
| **Pembimbing salah menjadwalkan bimbingan untuk peserta bidang lain** | Sedang | Menegakkan filter query dropdown mentee berdasarkan `pembimbing_id = auth()->id()` dan validasi backend `exists:penempatan,id,pembimbing_id,{id}`. |
| **Sertifikat diterbitkan untuk peserta yang belum menuntaskan penilaian** | Tinggi | Validasi ketat pada `SertifikatController`: memeriksa `status == Selesai` dan `penilaian.status == Final` sebelum mengizinkan generasi berkas. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Widget Kalender Timeline dan Pusat Pengingat SLA & Offboarding aktif pada Dashboard Administrasi.
- [x] Modul penerbitan dan unduh Sertifikat Kelulusan Magang berfungsi dengan baik.
- [x] Fitur Penjadwalan Mentoring & Notulensi Bimbingan terimplementasi dengan proteksi anti-IDOR.
- [x] Panel Radar Capaian Belajar Mentee aktif pada Dashboard Pembimbing.
- [x] Widget Telemetri Kesehatan Sistem dan Deteksi Anomali Keamanan terintegrasi pada Dashboard SuperAdmin.
- [x] Modul Backup Hub Database berfungsi aman dan hanya dapat diakses SuperAdmin.
- [x] Seluruh unit dan feature tests pada `AdvancedThreePillarsDashboardTest.php` dan seluruh regression test (228 tests, 1317 assertions) lulus 100% (**Green**).
- [x] Verifikasi visual di browser membuktikan layout rapi, responsif, bertema Sleman Blue `#0641CC`, dan bebas error console.

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**:
  - *(Akan dicatat saat eksekusi implementasi)*
- **Saran Lanjutan (Out of Scope)**:
  - Integrasi tanda tangan digital tersertifikasi BSrE (Badan Siber dan Sandi Negara) untuk dokumen sertifikat resmi Pemkab Sleman tingkat produksi lanjutan.
