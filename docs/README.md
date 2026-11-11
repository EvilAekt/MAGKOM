# Pusat Dokumentasi & Peta Arsitektur MagKom

Selamat datang di pusat dokumentasi resmi **MagKom (Magang Kominfo)** — Portal Manajemen dan Tata Kelola Magang Terpadu Dinas Komunikasi dan Informatika Kabupaten Sleman.

Dokumentasi ini dirancang sebagai **Single Source of Truth (SSOT)** bagi Software Architect, Fullstack Developer, DevOps/SysAdmin, QA Tester, dan AI Coding Agent untuk memahami, memelihara, menguji, dan mengembangkan sistem tanpa risiko regresi terhadap fungsionalitas yang telah stabil.

---

## 🗺️ Peta Navigasi Dokumentasi

Dokumentasi MAGKOM diorganisasikan ke dalam 5 pilar spesialisasi utama:

### 1. Spesifikasi Sistem & Bisnis
| Dokumen | Deskripsi & Ruang Lingkup | Audiens Utama |
|---|---|---|
| [**`PRODUCT.md`**](./PRODUCT.md) | **Product Context & System Boundaries**<br>• Batasan sistem tingkat tinggi, karakteristik 4 aktor pengguna, dan prinsip antarmuka status. | Product Owner, Lead Architect |
| [**`01-PRD-MagKom.md`**](./01-PRD-MagKom.md) | **Product Requirements Document (PRD v1.3)**<br>• Latar belakang kedinasan, user stories 4 aktor, dan sasaran metrik.<br>• Aturan Bisnis definitif (BR-01 s/d BR-19, termasuk Unsubmit Tugas & Batch Classroom).<br>• Spesifikasi alur pendaftaran, kuota atomik, penempatan 2 tahap, dan logbook.<br>• Kriteria penerimaan (*Acceptance Criteria*) berbasis automated testing. | Product Owner, Lead Architect, Developer, QA |
| [**`02-TDD-MagKom.md`**](./02-TDD-MagKom.md) | **Technical Design Document (TDD v1.4)**<br>• Arsitektur teknis Laravel 10.x & PHP 8.3.<br>• Pola *Modular Actions* (`app/Actions/`) dan *Specialized Queries* (`app/Queries/`).<br>• Arsitektur transaksi atomik kuota (`lockForUpdate`).<br>• Desain arsitektur Gutenberg Site Editor, Decoupled CMS, & Disaster Recovery Backup. | Tech Lead, Backend Developer, DevOps |
| [**`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`**](./04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) | **Kamus Data & Skema Basis Data**<br>• Diagram Relasi Entitas (ERD) Mermaid definitif.<br>• Kamus data detail 24 tabel domain aktif dan 5 tabel framework.<br>• Definisi foreign key, indexing, dan CHECK constraints database. | DBA, Backend Developer, Data Analyst |
| [**`05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`**](./05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md) | **Spesifikasi Keamanan, RBAC & Audit Trail**<br>• Matriks hak akses dinamis (`permissions` & `role_permissions`).<br>• Otorisasi Action classes, unsubmit workflow, dan penugasan batch.<br>• Mekanisme Audit Trail append-only 4 kanal stream.<br>• Keamanan berkas privat ber-signed URL dan perlindungan Disaster Recovery. | Security Engineer, Tech Lead, Backend Developer |
| [**`07-DESIGN-SYSTEM-MAGKOM.md`**](./07-DESIGN-SYSTEM-MAGKOM.md) | **Design System MagKom (DS-MagKom)**<br>• Fondasi 11 Master CSS Peeper Tokens (`variables.css`) & Dark Mode.<br>• Standar tipografi Open Sans & JetBrains Mono.<br>• Katalog komponen UI (Bento Grid, Sleman Preloader, Profil 360 Mentee, Modal, Timeline).<br>• Standar mikro-interaksi animasi GSAP 3.15 dan paritas dropdown akun. | Frontend Developer, UI/UX Designer |
| [**`08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md`**](./08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md) | **Spesifikasi Lengkap Fitur & Arsitektur Modular**<br>• Rujukan komprehensif seluruh kelas Actions, Queries, dan Service Layer.<br>• Fitur Batal Kirim Tugas (Unsubmit), Classroom-style Upload, & Bento Dashboard.<br>• Ekspor rekapitulasi data terstandarisasi (CSV, Excel XLSX) dan Telemetri Cockpit. | Semua Pengembang |

### 2. Arsitektur Teknis Rinci
| Dokumen | Deskripsi & Ruang Lingkup | Audiens Utama |
|---|---|---|
| [**`ARCHITECTURE.md`**](./ARCHITECTURE.md) | **Ikhtisar Arsitektur Aplikasi Pasca-Migrasi**<br>• Arsitektur sistem berlapis, pemisahan dependensi, dan aliran data utama. | Semua Developer |
| [**`FRONTEND-ARCHITECTURE.md`**](./FRONTEND-ARCHITECTURE.md) | **Arsitektur Frontend & Panduan Aset Vite**<br>• Pipeline Vite 6, 48 entry points terisolasi per halaman.<br>• Aturan wajib: Zero inline script/style di Blade & kepatuhan penuh CSP. | Frontend Developer, Blade Integrator |
| [**`architecture/ROUTES.md`**](./architecture/ROUTES.md) | **Peta Rute & Endpoint Aplikasi**<br>• Peta seluruh rute `routes/web.php` dan `routes/api.php` beserta proteksi middleware. | Backend & Fullstack Developer |

### 3. Panduan Pengembangan & Lingkungan Kerja
| Dokumen | Deskripsi & Ruang Lingkup | Audiens Utama |
|---|---|---|
| [**`DEVELOPMENT-GUIDE.md`**](./DEVELOPMENT-GUIDE.md) | **Panduan Pengembangan & Setup Lingkungan**<br>• Prasyarat wajib PHP 8.3, Composer 2, Node 20, dan MySQL 8.0.<br>• Prosedur instalasi lokal, migrasi database, dan seed data awal.<br>• Perintah diagnostik server, quality gates (Pint, PHPStan, PHPUnit). | Developer Baru, DevOps |
| [**`06-ADDENDUM-KEAMANAN-DAN-QA.md`**](./06-ADDENDUM-KEAMANAN-DAN-QA.md) | **Addendum Keamanan & Penjaminan Kualitas (QA)**<br>• Model ancaman (THR-01 s/d THR-16) dan mitigasi SEC-01 s/d SEC-06.<br>• Katalog pengujian otomatis PHPUnit dan integrasi Strix AI Security Gate. | Security Engineer, QA Tester |

### 4. Perencanaan Kerja & Task Briefs
| Direktori | Deskripsi & Ruang Lingkup |
|---|---|
| [**`plans/`**](./plans/) | **Repositori Task Briefs & Rencana Kerja Senior Dev**<br>• Berisi dokumen PRD dan brief implementasi fitur terstruktur.<br>• Diatur ke dalam status aktif dan arsip fitur yang telah selesai diimplementasikan. |

### 5. Arsip Catatan Historis (*Historical Records*)
| Dokumen | Keterangan Historis |
|---|---|
| [**`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`**](./03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) | Peta jalan rilis Gelombang 0 s/d 4 fase awal (Oktober 2026) dan catatan penguatan arsitektur November 2026. |
| [**`TECHNICAL-MIGRATION-AUDIT.md`**](./TECHNICAL-MIGRATION-AUDIT.md) | Laporan audit kondisi teknis sebelum migrasi Vite dan penguatan keamanan (2 November 2026). |
| [**`TECHNICAL-MIGRATION-REPORT.md`**](./TECHNICAL-MIGRATION-REPORT.md) | Laporan eksekusi 10 tahap migrasi teknis PHP 8.3, Vite 6, dan mitigasi SEC-01 s/d SEC-06. |

---

## 👥 Tim Pengembang & Jobdesk (Contributors & Jobdesk)

Sistem **MagKom** dikembangkan secara kolaboratif oleh 3 pengembang inti dengan pembagian peran dan tanggung jawab tugas:

| Akun GitHub | Peran & Jabatan | Jobdesk Utama |
|---|---|---|
| **[@EvilAekt](https://github.com/EvilAekt)**<br>(Adhitya Galang) | **Lead Software Architect & Security Lead**<br>*(Project Owner / Tech Lead)* | • Menetapkan arah arsitektur sistem, regulasi kedinasan, dan aturan bisnis definitif (*SSOT*).<br>• Mendesain skema tabel database, relasi kunci asing, dan transaksi locking kuota atomik (`lockForUpdate`).<br>• Mengelola arsitektur kernel, middleware otorisasi RBAC, service layer, dan pola Modular Actions & Queries.<br>• Memimpin otomatisasi pengujian (740+ tests di PHPUnit) dan memegang wewenang approval/merge final ke `main`. |
| **[@aldiartha](https://github.com/aldiartha)**<br>(Aldi Artha Mida) | **Fullstack Developer & Admin Systems Specialist**<br>*(Admin & Placement Domain Lead)* | • Mengelola seluruh logika bisnis dan antarmuka modul Administrasi Kominfo (`/admin/*`).<br>• Merancang alur verifikasi berkas kandidat magang ber-SLA (*SLA Watchdog*) dan penerbitan Surat Penerimaan resmi.<br>• Mengembangkan sistem plotting penempatan dua tahap dan pembatasan beban bimbingan staf dinas (*mentee limiter*).<br>• Menyusun query pelaporan dan standardisasi format ekspor berkas spreadsheet kedinasan (CSV, Excel, PDF). |
| **[@NAUFALFEBRIAN62](https://github.com/NAUFALFEBRIAN62)**<br>(Naufal Febrian) | **Frontend Specialist & UI/UX Interaction Engineer**<br>*(Public Portal & Peserta Domain Lead)* | • Mengembangkan antarmuka Blade dengan kepatuhan mutlak: **Zero Inline CSS/JS**.<br>• Merancang antarmuka publik dan portal peserta (`/peserta/*`) yang ergonomis, responsif, dan ramah perangkat mobile.<br>• Membangun dashboard modular Bento Grid, turn-in tugas classroom, dan pendaftaran tim/kelompok.<br>• Mengelola interaktivitas Vanilla JavaScript (ES Modules), animasi GSAP, dan harmonisasi Dark Mode (`DS-MagKom`). |

### Matriks RACI Pengembangan MagKom
- **Penentuan Aturan Bisnis & Spesifikasi PRD**: Adhitya Galang (**A**), Aldi Artha Mida (**C**), Naufal Febrian (**C**)
- **Desain Skema Basis Data & Konkurensi Kuota**: Adhitya Galang (**A/R**), Aldi Artha Mida (**R**), Naufal Febrian (**I**)
- **Implementasi Modular Actions, Queries & Services**: Adhitya Galang (**A**), Aldi Artha Mida (**R**), Naufal Febrian (**I**)
- **Pengerasan Keamanan & Validasi Otorisasi**: Adhitya Galang (**A**), Aldi Artha Mida (**R**), Naufal Febrian (**I**)
- **Pengembangan Blade, Bento Grid & CSS Tokens**: Adhitya Galang (**A**), Aldi Artha Mida (**C**), Naufal Febrian (**R**)
- **Pemeliharaan Test Suite Otomatis (740+ Tests)**: Adhitya Galang (**A**), Aldi Artha Mida (**R**), Naufal Febrian (**C**)
- **Pemeliharaan Living Docs & Changelog**: Adhitya Galang (**A**), Aldi Artha Mida (**C**), Naufal Febrian (**R**)
- **Review Kode Pull Request & Merge ke `main`**: Adhitya Galang (**A**), Aldi Artha Mida (**R**), Naufal Febrian (**R**)

---

## ⚡ 7 Prinsip Emas Pengembangan MagKom ("Golden Guardrails")

Sebelum melakukan perubahan kode atau menambahkan komponen baru, pastikan mematuhi prinsip mutlak berikut:

1. **Penerimaan & Kuota Wajib Atomik (BR-05)**:  
   Seluruh mutasi kuota pendaftaran formasi bulanan dan slot khusus wajib diapit transaksi basis data dengan `lockForUpdate()`. Dilarang melakukan pengecekan kuota di luar transaksi database.
2. **SuperAdmin Read-Only pada Ranah Pembimbing (D-05)**:  
   SuperAdmin memiliki hak akses pemantauan penuh, namun **dilarang keras** memiliki tombol/rute mutasi untuk membuat tugas, mereview progres mentee, atau memvalidasi logbook.
3. **Penyimpanan Berkas di Disk Privat (NFR-S6)**:  
   Seluruh dokumen pendaftaran disimpan di disk `storage/app/private/`. Berkas hanya dapat diunduh melalui temporary signed URL bertanda tangan HMAC dengan TTL 5 menit yang divalidasi oleh Policy kepemilikan.
4. **Audit Trail Komprehensif & Append-Only (D-08)**:  
   Setiap mutasi bisnis dan autentikasi wajib tercatat ke tabel `audit_log` dengan penyensoran rekursif kata sandi/token. Akun database aplikasi tidak memiliki izin `UPDATE` maupun `DELETE` pada tabel audit.
5. **Kepatuhan Presentasi Blade (Zero Inline Frontend)**:  
   Blade hanya menjadi lapisan presentasi markup HTML. Dilarang menulis CSS di `<style>`, JavaScript di `<script>`, atau inline event handler (`onclick`, dll.) di dalam berkas Blade.
6. **Kepatuhan Runtime PHP 8.3 Mutlak**:  
   Proyek terkunci pada runtime PHP 8.3.x. Dilarang merekomendasikan PHP 8.2 atau menganggap flag `--ignore-platform-req=php` sebagai solusi kompatibilitas runtime aplikasi.
7. **Pola Backend Wajib Modular Actions & Queries (Skinny Controllers)**:  
   Controller hanya bertugas mengorkestrasi request HTTP. Logika mutasi bisnis wajib diisolasi di `app/Actions/`, operasi filter/paginasi data kompleks di `app/Queries/`, dan logika domain lintas-tabel di `app/Services/`.

---

## 🏢 Struktur 13 Modul Sistem Definitif

Sesuai dengan seeder resmi `PermissionSeeder.php` dan implementasi sistem, MAGKOM mengelola 13 modul hak akses perizinan dan 1 modul manajemen konten antarmuka publik:

1. **Portal Publik & Registrasi Calon Peserta** (`publik.portal-registrasi`): Informasi kuota real-time, panduan magang, registrasi akun calon peserta (Mandiri & Kelompok), Google OAuth.
2. **Pemberkasan & Pendaftaran Magang** (`pendaftaran.magang`): Unggah berkas persyaratan jenjang SMK/D3/S1, pemilihan formasi bulan magang, unduh SK penerimaan.
3. **Verifikasi Berkas & Seleksi Administratif** (`seleksi.verifikasi-berkas`): Antrean seleksi admin dalam SLA 5 hari kerja, keputusan langsung Diterima atau Ditolak, unduh bundel arsip ZIP.
4. **Manajemen Kuota Bulanan & Bidang Dinas** (`kuota.manajemen`): Kontrol kuota umum bulanan, alokasi per bidang unit kerja, sakelar periode formasi.
5. **Penetapan Penempatan & Pembimbing Lapangan** (`penempatan.pembimbing`): Plotting peserta dua tahap, reassign pembimbing 1-klik, plotting massal.
6. **Logbook Mingguan & Manajemen Penugasan Proyek** (`bimbingan.logbook-tugas`): Instruksi tugas ber-deadline, review progres log, validasi logbook mingguan (maks. mundur 3 hari), penyelesaian bimbingan 1-klik.
7. **Rekapitulasi Portofolio Bimbingan & Laporan Kinerja** (`laporan.kinerja-portofolio`): Profil 360 Mentee, ekspor portofolio kinerja ASN Sleman (PDF), laporan pendaftaran & penempatan (Excel XLSX).
8. **Manajemen Staf Internal & Akun Pegawai** (`staf.manajemen`): Manajemen akun Admin dan Pembimbing, reset/regenerasi kredensial acak, modal salin kredensial awal.
9. **Slot Khusus Kemitraan & Override Kuota** (`kuota.slot-khusus`): Alokasi kuota diskresi kemitraan instansi dan pembuatan peserta khusus dinas beserta `password_awal`.
10. **Konfigurasi Kebijakan & Parameter Sistem** (`konfigurasi.parameter`): 12 parameter operasional aplikasi, ekspor konfigurasi format JSON, purge audit log kedaluwarsa.
11. **Pusat Cadangan & Pemulihan Basis Data** (`sistem.cadangan-database`): Disaster recovery backup basis data instan format `.sql.gz`, checksum SHA-256, unduh aman, dan restore.
12. **Telemetri Kesehatan & Diagnostik Sistem** (`sistem.telemetri-kesehatan`): Monitoring latensi HTTP & DB, kesehatan storage privat, dan 4 aksi diagnostik server (Security Sweep, Optimize DB, Clear Cache, Storage Test).
13. **Jejak Audit Trail & Log Keamanan Sistem** (`keamanan.audit-trail`): Rekam jejak audit append-only 4 kanal stream (`auth`, `aksi`, `service`, `akses_data`) dengan ekspor CSV/Excel.
14. *(Tata Kelola Publik SuperAdmin)* **WordPress-Style Visual Site Editor & CMS Landing Page**: Pengelolaan blok konten dinamis landing page publik, Block Registry, Pattern Library, Reusable Blocks, Media Library, Staging Sandbox, serta Revisions History.

---

## 🗄️ Pemisahan Struktur Tabel Basis Data

Skema basis data MAGKOM memisahkan secara tegas antara tabel domain aplikasi dan tabel bawaan framework:

### Tabel Domain Aplikasi MAGKOM (24 Tabel)
1. `audit_log` — Log audit mutasi data append-only multi-channel.
2. `bulan_magang` — Master periode bulan pendaftaran magang.
3. `kuota` — Alokasi kapasitas kuota per jurusan dan periode bulan.
4. `slot_khusus` — Kuota diskresi kemitraan instansi / kampus.
5. `jurusan` — Master data konsentrasi keahlian / pendidikan.
6. `bidang` — Master unit kerja bidang teknis Dinas Kominfo.
7. `bidang_jurusan` — Pivot pemetaan relevansi jurusan terhadap bidang dinas.
8. `jenis_dokumen` — Master persyaratan berkas sesuai jenjang (SMK, D3, S1).
9. `peserta_profil` — Biodata lengkap mentee, kontak darurat, persetujuan wali, alamat.
10. `kelompok_magang` — Master rombongan magang kelompok (maksimal 4 orang).
11. `kelompok_anggota` — Anggota tim magang kelompok yang terhubung ke ketua.
12. `pendaftaran` — Rekaman pendaftaran formasi magang dan status verifikasi.
13. `dokumen` — Metadata berkas persyaratan yang diunggah peserta.
14. `dokumen_verifikasi` — Catatan riwayat pemeriksaan berkas oleh verifikator.
15. `penempatan` — Rekaman plotting peserta aktif ke bidang dan pembimbing ASN.
16. `tugas` — Instruksi tugas proyek bimbingan terstruktur ber-deadline.
17. `tugas_template` — Library template penugasan bimbingan mandiri.
18. `progres_log` — Rekaman laporan hasil pengerjaan tugas oleh mentee.
19. `logbook` — Presensi aktivitas harian/mingguan mentee (maksimal mundur 3 hari).
20. `realtime_events` — Event store untuk Server-Sent Events (SSE) notification.
21. `route_metrics` — Telemetri performa rute HTTP dan waktu respons.
22. `pengaturan` — Konfigurasi 12 parameter operasional aplikasi.
23. `permissions` — Master 13 modul izin akses fungsional sistem.
24. `role_permissions` — Pivot relasi matriks hak akses peran pengguna terhadap modul.

### Tabel Bawaan Framework Laravel (5 Tabel)
1. `users` — Akun pengguna terotentikasi (diperkaya dengan atribut domain: `no_telp`, `foto`, `password_awal`, status keaktifan).
2. `password_reset_tokens` — Token reset kata sandi email.
3. `sessions` — Penyimpanan sesi pengguna berbasis basis data.
4. `failed_jobs` — Antrean kegagalan background jobs.
5. `personal_access_tokens` — Token otentikasi API Laravel Sanctum.

---

## 🚀 Alur Kerja Lintas Dokumen

1. Untuk memahami **batasan sistem & profil pengguna**: Buka [`PRODUCT.md`](./PRODUCT.md).
2. Untuk mempelajari **kebutuhan fitur dan aturan bisnis**: Buka [`01-PRD-MagKom.md`](./01-PRD-MagKom.md).
3. Untuk mempelajari **arsitektur backend dan service layer**: Buka [`02-TDD-MagKom.md`](./02-TDD-MagKom.md).
4. Untuk mempelajari **spesifikasi teknis Actions & Queries lengkap**: Buka [`08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md`](./08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md).
5. Untuk mempelajari **struktur relasi tabel basis data**: Buka [`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](./04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md).
6. Untuk mempelajari **matriks perizinan dan audit log**: Buka [`05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](./05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md).
7. Untuk mempelajari **standar antarmuka dan design tokens**: Buka [`07-DESIGN-SYSTEM-MAGKOM.md`](./07-DESIGN-SYSTEM-MAGKOM.md).
8. Untuk panduan **instalasi lokal dan quality gates**: Buka [`DEVELOPMENT-GUIDE.md`](./DEVELOPMENT-GUIDE.md).
9. Untuk panduan **aset frontend Vite dan larangan Blade**: Buka [`FRONTEND-ARCHITECTURE.md`](./FRONTEND-ARCHITECTURE.md).
10. Untuk rujukan **peta rute HTTP aktif**: Buka [`architecture/ROUTES.md`](./architecture/ROUTES.md).
