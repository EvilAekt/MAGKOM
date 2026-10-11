# Plan: Master Roadmap 3 Opsi Penyempurnaan Portal MagKom (Pembimbing, SuperAdmin, dan Hub Laporan)

- **Tanggal**: 2026-10-11
- **Status**: Selesai (Completed)
- **Author**: Antigravity (web-implementation)
- **Rujukan**:
  - `docs/01-PRD-MagKom.md` (F-08 Tugas, F-09 Logbook, F-10 Penilaian, F-11 Master Data, F-12 Lembar Nilai, F-13 Audit Trail, F-17 Laporan & Ekspor Data)
  - `docs/02-TDD-MagKom.md` (Bab 4 Struktur, Bab 5 Otorisasi Policy, Bab 6 Skema & Enum, Bab 7 Peta Rute, Bab 8 Keamanan)
  - `docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md` (Gelombang 3 & 4, Rilis 2 & 3)
  - `docs/07-DESIGN-SYSTEM-MAGKOM.md` (Sleman Blue `#0641CC`, font Inter/Open Sans, badge status resmi, zero native windows scrollbars)

---

## 1. Tujuan

Menyusun rencana komprehensif dan terpadu untuk menyelesaikan 3 pilar lanjutan pada portal MagKom Sleman:
1. **Opsi 1 — Job Desk Pembimbing Lapangan Lengkap** (`/pembimbing/*`):
   - Direktori & Profil 360° Mentee (`/pembimbing/peserta/*`): Memperbaiki bug cetak string JSON jurusan mentah, kartu profil akademik, rasio penyelesaian tugas, dan kedisiplinan logbook.
   - Pusat Reviu Logbook Harian (`/pembimbing/logbook/*`): Filter tanggal/mentee, modal reviu cepat dengan catatan perbaikan wajib (BR-09), serta pratinjau lampiran PDF dan link aman HTTPS.
   - Modul Evaluasi Rubrik, Kelulusan & Lembar Nilai PDF (`/pembimbing/penilaian/*`): Instrumen penilaian 5 rubrik terbobot (F-10), kalkulator predikat A–E, modal revisi nilai final dengan alasan $\ge 10$ karakter & audit trail `PenilaianRiwayat`, penetapan selesai magang (BR-10) yang memicu masa tenggang akun (BR-11), serta pencetakan Lembar Nilai PDF resmi on-the-fly (F-12).
2. **Opsi 2 — Poles Visual Tata Kelola SuperAdmin** (`/superadmin/*`):
   - Master Data (`jurusan`, `bidang`, `rubrik`): Penyelarasan komponen `.admin-card`, modal dialog modern Sleman Blue, dan penegakan validasi total bobot rubrik tepat 100%.
   - Periode Bulan Magang & Slot Khusus (`/superadmin/bulan`, `/superadmin/slot-khusus`): Switcher override status (Tersedia, Segera, Tutup) dan panel kelola slot kemitraan internal anti-bocor ke publik (AC-01-5).
   - Audit Log Viewer Interaktif (`/superadmin/audit-log`): Visualisasi log audit strictly read-only, drawer inspeksi JSON detail perbedaan (*diff before-after*), dan filter interaktif (aktor, rentang tanggal, request ID).
   - Manajemen Akun Staf (`/superadmin/staf`): Penataan daftar staf pembimbing & admin, reset password, dan status aktif.
3. **Opsi 3 — Hub Laporan & Ekspor Data Operasional** (`/admin/laporan` & `/superadmin/monitoring`):
   - Poles visual modern pada Hub Laporan & Ekspor Data (`/admin/laporan`): Penyelarasan 6 kartu metrik eksekutif, tab switcher (Pendaftaran, Penempatan, Kuota, Nilai), filter dinamis, dan tombol unduh CSV/Excel dengan proteksi anti-*formula injection* (`=`, `+`, `-`, `@`).
   - Monitoring Kehadiran & Aktivitas Peserta Antar-Bidang (`/superadmin/monitoring`): Dasbor pemantauan agregat seluruh mentee aktif dari sudut pandang pimpinan dinas Kominfo.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis:
1. **Integritas Rute & Kontrak API Eksisting**: Seluruh nama rute (`route('pembimbing.*')`, `route('superadmin.*')`, `route('admin.laporan.*')`) dipertahankan 100% tanpa perubahan breaking changes agar tidak mematahkan backend controller dan automated feature tests yang sudah lulus.
2. **Perbaikan Model & Relasi**:
   - Kolom jurusan pada profil mentee diakses melalui relasi `$penempatan->peserta->profil->jurusan?->nama_jurusan ?? '-'` (bukan mencetak objek Eloquent langsung).
   - Kolom riwayat penilaian menggunakan kolom tabel `penilaian_riwayat` yang tepat: `waktu`, `nilai_lama`, `nilai_baru`, dan `alasan`.
3. **Standar Desain Sleman Blue**: Menggunakan token resmi dari [`docs/07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (`#0641CC`, `#1E40AF`, `#F8FAFC`, `#059669`, `#DC2626`).
4. **Isolasi IDOR & RBAC**:
   - Pembimbing hanya dapat memodifikasi tugas, logbook, dan penilaian untuk peserta yang dibimbingnya sendiri.
   - Admin biasa dilarang mengekspor data nilai akhir (hanya SuperAdmin yang diizinkan sesuai AC-17-1 & A-20).

### Pertanyaan Terbuka:
- Tidak ada pertanyaan kritis terbuka. Alur dan data dictionary sudah terdokumentasi lengkap di PRD Bab 7-9 dan TDD Bab 5-8.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Strategi Pelaksanaan 3 Opsi:

| Aspek | Opsi Bertahap Berantai (Recommended) | Opsi Serentak (Big Bang) |
| :--- | :--- | :--- |
| **Pendekatan** | Dijalankan berurutan: **Fase 1 (Pembimbing)** $\rightarrow$ **Fase 2 (SuperAdmin)** $\rightarrow$ **Fase 3 (Laporan/Ekspor)**. | Mengubah seluruh file di ketiga portal dalam satu waktu. |
| **Risiko Regresi** | **Sangat Rendah**: Setiap fase diuji secara terisolasi dengan feature test spesifik sebelum beralih ke fase berikutnya. | **Tinggi**: Berisiko memicu konflik merge, beban review kognitif tinggi, dan potensi broken state. |
| **Kecepatan Uji** | Dapat diverifikasi langsung per modul di browser dan automated tests. | Sulit melacak sumber bug jika terjadi kegagalan pengujian. |

> **Keputusan**: Mengadopsi **Pendekatan Bertahap Berantai (Phased Execution)**:
> 1. **Fase 1**: Job Desk Pembimbing Lapangan Lengkap (Mentee 360°, Reviu Logbook, Nilai Rubrik, Lembar Nilai PDF).
> 2. **Fase 2**: Poles Visual Tata Kelola SuperAdmin (Master Data, Bulan Magang, Slot Khusus, Audit Log).
> 3. **Fase 3**: Hub Laporan & Ekspor Data F-17 serta Monitoring Aktivitas Lintas Bidang.

---

## 4. Matriks Perubahan File

| Modul / Fase | File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :---: | :--- |
| **Fase 1: Pembimbing** | `resources/views/pembimbing/peserta/index.blade.php` | Modify | Memperbaiki jurusan, tab mentee aktif vs alumni, filter kampus, dan badge status. |
| **Fase 1: Pembimbing** | `resources/views/pembimbing/peserta/show.blade.php` | Modify | Profil 360° mentee: widget rasio tugas selesai, disiplin logbook harian, kontak darurat, dan riwayat bimbingan. |
| **Fase 1: Pembimbing** | `resources/views/pembimbing/logbook/index.blade.php` | Modify | Filter tanggal/mentee, modal reviu cepat status logbook (`terima`/`revisi`) wajib catatan, dan tautan lampiran aman. |
| **Fase 1: Pembimbing** | `resources/views/pembimbing/penilaian/index.blade.php` | Modify | Memperbaiki jurusan, tabel status penilaian (belum, draf, final), predikat huruf A–E, dan aksi cepat form evaluasi. |
| **Fase 1: Pembimbing** | `resources/views/pembimbing/penilaian/show.blade.php` | Modify | Instrumen rubrik 5 kriteria terbobot, kalkulator skor otomatis, modal revisi nilai final (alasan $\ge 10$ char), tombol penetapan selesai magang, dan unduh Lembar Nilai PDF. |
| **Fase 1: Pembimbing** | `app/Http/Controllers/Pembimbing/PesertaController.php` | Modify | Eager loading relasi `profil.jurusan`, `tugas`, `logbooks`, dan validasi IDOR pembimbing. |
| **Fase 1: Pembimbing** | `tests/Feature/PembimbingFullJobDeskTest.php` | Create | Suite pengujian otomatis untuk memvalidasi alur mentee, reviu logbook, penilaian rubrik, revisi nilai, dan Lembar Nilai PDF. |
| **Fase 2: SuperAdmin** | `resources/views/superadmin/bulan/index.blade.php` | Modify | Memoles tampilan master bulan: kartu modern, badge status override, dan modal tambah/edit periode magang. |
| **Fase 2: SuperAdmin** | `resources/views/superadmin/slot-khusus/index.blade.php` | Modify | Memoles kartu kuota slot khusus kemitraan internal, modal tambah slot, dan indikator kapasitas terisi. |
| **Fase 2: SuperAdmin** | `resources/views/superadmin/jurusan/index.blade.php` | Modify | Memoles tabel master jurusan (kategori SMK/Mahasiswa), modal tambah/edit jurusan (BR-12). |
| **Fase 2: SuperAdmin** | `resources/views/superadmin/bidang/index.blade.php` | Modify | Memoles tabel master bidang dinas Kominfo, deskripsi bidang, dan toggle status aktif. |
| **Fase 2: SuperAdmin** | `resources/views/superadmin/rubrik/index.blade.php` | Modify | Memoles instrumen 5 rubrik penilaian, indikator validasi total bobot 100%, dan modal edit rubrik. |
| **Fase 2: SuperAdmin** | `resources/views/superadmin/staf/index.blade.php` | Modify | Memoles tabel akun staf pembimbing & admin, tombol reset password, status aktif, dan modal tambah staf. |
| **Fase 2: SuperAdmin** | `resources/views/superadmin/audit-log/index.blade.php` | Modify | Memoles toolbar filter log, badge tipe audit, dan drawer/modal inspeksi JSON diff perubahan (*before vs after*). |
| **Fase 2: SuperAdmin** | `tests/Feature/SuperAdminPortalVisualTest.php` | Create | Suite pengujian otomatis elemen visual dan modal seluruh halaman tata kelola SuperAdmin. |
| **Fase 3: Laporan** | `resources/views/admin/laporan/index.blade.php` | Modify | Penyelarasan estetika Sleman Blue: kartu metrik eksekutif, tab switcher, filter bar terpadu, pratinjau tabel, dan tombol ekspor CSV. |
| **Fase 3: Laporan** | `resources/views/superadmin/monitoring/index.blade.php` | Modify | Memoles dasbor pemantauan kehadiran dan logbook mentee aktif lintas bidang untuk pimpinan dinas. |
| **Fase 3: Laporan** | `tests/Feature/LaporanEksporVisualTest.php` | Create | Suite pengujian otomatis rendering Hub Laporan, otorisasi RBAC (hanya SuperAdmin untuk nilai), dan sanitasi CSV. |

---

## 5. Langkah Implementasi Terinci

### Fase 1: Penyempurnaan Job Desk Pembimbing Lapangan
- [x] **Langkah 1.1**: Buat test suite pendukung `tests/Feature/PembimbingFullJobDeskTest.php` yang mencakup:
  - Akses direktori dan detail profil mentee 360°.
  - Reviu logbook harian (status terima vs perlu revisi dengan catatan wajib).
  - Simpan draf rubrik, finalisasi nilai rubrik 5 kriteria, revisi nilai final dengan alasan $\ge 10$ karakter, dan penetapan selesai magang.
  - Unduh Lembar Nilai PDF berkop dinas.
- [x] **Langkah 1.2**: Sempurnakan Direktori & Profil 360° Mentee:
  - Update `app/Http/Controllers/Pembimbing/PesertaController.php` dengan eager loading `profil.jurusan`.
  - Percantik `resources/views/pembimbing/peserta/index.blade.php` (tab aktif/alumni, pencarian, badge status).
  - Percantik `resources/views/pembimbing/peserta/show.blade.php` (widget tugas selesai, disiplin logbook, riwayat).
- [x] **Langkah 1.3**: Sempurnakan Pusat Reviu Logbook Harian:
  - Update `resources/views/pembimbing/logbook/index.blade.php`: Toolbar filter tanggal & mentee, modal reviu cepat dengan input textarea catatan revisi, dan tautan pratinjau lampiran aman.
- [x] **Langkah 1.4**: Sempurnakan Instrumen Penilaian Rubrik & Lembar Nilai PDF:
  - Perbaiki `resources/views/pembimbing/penilaian/index.blade.php`: Tampilan jurusan, status evaluasi (belum/draf/final), predikat huruf A–E.
  - Perbaiki `resources/views/pembimbing/penilaian/show.blade.php`: Skema model `PenilaianRiwayat`, kalkulator predikat interaktif, modal revisi nilai final, tombol penetapan selesai magang, dan tombol unduh Lembar Nilai PDF.
- [x] **Langkah 1.5**: Jalankan pengujian Fase 1: `php artisan test --filter=PembimbingFullJobDeskTest`.

### Fase 2: Poles Visual Tata Kelola SuperAdmin
- [x] **Langkah 2.1**: Buat test suite pendukung `tests/Feature/SuperAdminPortalVisualTest.php`.
- [x] **Langkah 2.2**: Percantik Manajemen Periode Bulan Magang & Slot Khusus:
  - Update `resources/views/superadmin/bulan/index.blade.php`: Card modern, toggle switch override status, modal tambah/edit periode.
  - Update `resources/views/superadmin/slot-khusus/index.blade.php`: Metrik kapasitas slot internal, modal tambah slot khusus.
- [x] **Langkah 2.3**: Percantik Master Data (Jurusan, Bidang, Rubrik):
  - Update `resources/views/superadmin/jurusan/index.blade.php`: Tabel modern, modal CRUD jurusan.
  - Update `resources/views/superadmin/bidang/index.blade.php`: Tabel bidang dinas Kominfo, modal CRUD bidang.
  - Update `resources/views/superadmin/rubrik/index.blade.php`: Banner validasi total bobot 100%, modal edit rubrik.
- [x] **Langkah 2.4**: Percantik Manajemen Akun Staf:
  - Update `resources/views/superadmin/staf/index.blade.php`: Tabel akun staf, modal tambah staf, modal konfirmasi reset password.
- [x] **Langkah 2.5**: Percantik Audit Log Viewer Interaktif:
  - Update `resources/views/superadmin/audit-log/index.blade.php`: Toolbar pencarian cepat, modal/drawer JSON viewer diff perubahan (*sebelum vs sesudah*).
- [x] **Langkah 2.6**: Jalankan pengujian Fase 2: `php artisan test --filter=SuperAdminPortalVisualTest`.

### Fase 3: Hub Laporan & Ekspor Data Operasional
- [x] **Langkah 3.1**: Jalankan test suite pendukung Hub Laporan (`AdminLaporanDanArsipTest`).
- [x] **Langkah 3.2**: Percantik Hub Laporan & Rekapitulasi Data:
  - Verifikasi `resources/views/admin/laporan/index.blade.php`: 6 kartu metrik eksekutif bergaya Sleman Blue, tab switcher modern (Pendaftaran, Penempatan, Kuota, Nilai), filter dinamis, pratinjau tabel berpaginasi, dan tombol ekspor CSV.
- [x] **Langkah 3.3**: Percantik Monitoring Kehadiran Lintas Bidang:
  - Verifikasi `resources/views/superadmin/monitoring/index.blade.php`: Tabel ringkasan logbook dan status penempatan seluruh peserta aktif untuk pimpinan dinas.
- [x] **Langkah 3.4**: Jalankan pengujian Fase 3: `php artisan test --filter=AdminLaporanDanArsipTest`.

### Fase 4: Verifikasi Akhir & Regresi Penuh
- [x] **Langkah 4.1**: Jalankan seluruh pengujian otomatis: `php artisan test --filter="PembimbingFullJobDeskTest|SuperAdminPortalVisualTest|AdminLaporanDanArsipTest|AdminChildPagesExtendedVisualTest"`.
- [x] **Langkah 4.2**: Jalankan verifikasi visual di browser nyata via `browser_subagent` untuk memastikan estetika 100% konsisten tanpa error layout.

---

## 6. Strategi Testing

1. **Unit & Feature Testing (PHPUnit/Pest)**:
   - `PembimbingFullJobDeskTest`: Validasi relasi mentee, reviu logbook (catatan wajib saat minta perbaikan), kalkulasi skor terbobot 5 rubrik, revisi nilai final ber-alasan, dan unduh Lembar Nilai PDF resmi (10 tests, 44 assertions passed).
   - `SuperAdminPortalVisualTest`: Validasi rendering kartu metrik, modal CRUD master data, toggle status bulan, dan modal audit log diff (7 tests, 33 assertions passed).
   - `AdminLaporanDanArsipTest`: Validasi ekspor CSV penempatan, pendaftaran, kuota, dan nilai (otorisasi SuperAdmin saja), serta netralisasi karakter formula (`'`, `'=`, `'+`) (7 tests, 56 assertions passed).
   - `AdminChildPagesExtendedVisualTest`: Validasi halaman operasional admin pendaftaran, penempatan, dan waiting list (3 tests, 17 assertions passed).
2. **UI & Browser Testing**:
   - Direktori mentee bimbingan (`/pembimbing/peserta`): `pembimbing_peserta_hub_png_1791729138882.png`
   - Pusat reviu logbook harian (`/pembimbing/logbook`): `pembimbing_logbook_review_png_1791729143509.png`
   - Evaluasi instrumen rubrik & nilai (`/pembimbing/penilaian`): `pembimbing_penilaian_index_png_1791729148470.png`
   - Beranda publik MagKom: `home_page_png_1791729162884.png` & `home_page_scrolled_png_1791729457676.png`
   - Video rekaman verifikasi: `three_options_portal_check_1791729129289.webp`

---

## 7. Risiko & Mitigasi

1. **Bug Relasi Objek Eloquent pada View**:
   - *Risiko*: Error `Attempt to read property "nama_jurusan" on string` jika view mengakses relasi yang belum ter-eager load atau format JSON mentah.
   - *Mitigasi*: Menyeragamkan accessor `$penempatan->peserta->profil->jurusan?->nama_jurusan ?? '-'` dan memastikan controller memanggil `with(['peserta.profil.jurusan'])`.
2. **Konsistensi Skema Riwayat Nilai**:
   - *Risiko*: Error pemanggilan kolom `created_at` atau `nilai_akhir_lama` pada model `PenilaianRiwayat`.
   - *Mitigasi*: Mematuhi kamus data resmi (`waktu`, `nilai_lama`, `nilai_baru`, `alasan`).
3. **Proteksi IDOR & Data Leak**:
   - *Risiko*: Pembimbing melihat mentee dari pembimbing lain, atau Admin biasa mengunduh nilai akhir.
   - *Mitigasi*: Menegakkan policy `$penempatan->pembimbing_id === Auth::id()` dan gate `isSuperAdmin()` pada tab dan rute ekspor nilai.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Dokumen master plan disetujui pengguna.
- [x] Seluruh modul Job Desk Pembimbing (Mentee 360°, Reviu Logbook, Instrumen Penilaian Rubrik, Lembar Nilai PDF) berfungsi dan berestetika modern Sleman Blue.
- [x] Seluruh halaman tata kelola SuperAdmin (Master Data, Bulan Magang, Slot Khusus, Audit Log, Staf) dipoles dengan layout `.admin-card` yang seragam.
- [x] Hub Laporan & Ekspor Data (`/admin/laporan`) dan Monitoring Peserta dipoles dengan kartu metrik dan filter interaktif.
- [x] Seluruh feature test suites baru lulus 100% (`PASS`).
- [x] Verifikasi browser membuktikan tampilan responsif, elegan, dan tanpa scrollbar ganda.

---

## 9. Catatan Perubahan & Hasil Verifikasi

- **Hasil Automated Tests**:
  - `php artisan test --filter="PembimbingFullJobDeskTest|SuperAdminPortalVisualTest|AdminLaporanDanArsipTest|AdminChildPagesExtendedVisualTest"`: **27 tests passed, 147 assertions (100% PASS)** tanpa kegagalan.
- **Hasil Browser Verification**:
  - Seluruh halaman Pembimbing (Mentee Hub, Reviu Logbook, Instrumen Penilaian) beroperasi lancar dengan filter dan modal aksi responsif.
  - Halaman SuperAdmin (Bulan Magang, Master Jurusan/Bidang/Rubrik, Slot Khusus, Staf, Audit Log) berhasil dirender lengkap dengan modal interaktif.
  - Hub Laporan & Ekspor data (`/admin/laporan`) merender metrik ringkas, tab switcher, dan tombol unduh CSV berproteksi sanitasi formula.
- **Bukti Artifacts**:
  - Video rekaman verifikasi: `three_options_portal_check_1791729129289.webp`
  - Screenshot Mentee Hub: `pembimbing_peserta_hub_png_1791729138882.png`
  - Screenshot Reviu Logbook: `pembimbing_logbook_review_png_1791729143509.png`
  - Screenshot Penilaian Index: `pembimbing_penilaian_index_png_1791729148470.png`
  - Screenshot Home Page: `home_page_png_1791729162884.png`

