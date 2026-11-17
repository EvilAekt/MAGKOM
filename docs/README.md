# Pusat Dokumentasi Rekayasa Perangkat Lunak MAGKOM

| Informasi | Keterangan |
|---|---|
| Status | Verified Living Documentation |
| Terakhir Diverifikasi | 2026-11-17 |
| Pendekatan Arsitektur | Diátaxis Framework (Tutorials, How-to, Explanations, Reference) |
| Repositori | `EvilAekt/MAGKOM` (Branch: `test-2` / `main`) |

---

## 1. Selamat Datang di Dokumentasi MAGKOM

Dokumentasi ini disusun sebagai rujukan teknis definitif (*Single Source of Truth*) bagi Software Engineer, Architect, QA Tester, DevOps/SysAdmin, dan pengembang baru yang bergabung dalam pemeliharaan sistem **MAGKOM** (Sistem Informasi Manajemen dan Tata Kelola Magang Terpadu Dinas Komunikasi dan Informatika Kabupaten Sleman).

Seluruh informasi dalam pustaka dokumentasi ini telah diaudit dan diverifikasi secara independen terhadap kondisi aktual kode sumber pada branch `main`.

---

## 2. Peta Navigasi Berdasarkan Kebutuhan Pembaca

### 2.1 Memulai Proyek (Getting Started)
Panduan dasar bagi pengembang yang baru pertama kali membuka repositori MAGKOM:

- [**Ringkasan Produk (`product/overview.md`)**](./product/overview.md): Identitas sistem, latar belakang kedinasan, permasalahan yang diselesaikan, aktor pengguna, batasan sistem, dan alur bisnis tingkat tinggi.
- [**Panduan Memulai Cepat (`development/getting-started.md`)**](./development/getting-started.md): Prasyarat perangkat lunak (PHP 8.3, Node 20, MySQL 8), instalasi dependensi, migrasi basis data, akun pengujian lokal bawaan, dan verifikasi awal.
- [**Ikhtisar Arsitektur Sistem (`architecture/overview.md`)**](./architecture/overview.md): Diagram tingkat tinggi, gaya Clean Layered Architecture, spesifikasi komponen, siklus hidup request, dan keputusan arsitektural (ADR).

---

### 2.2 Panduan Pengembang Aplikasi (For Application Developers)
Panduan teknis mendalam untuk pengembangan fitur pada lapisan backend dan frontend:

- [**Arsitektur Backend (`architecture/backend.md`)**](./architecture/backend.md): Struktur Controller ramping, 24 kelas Action, 12 kelas Query, 40 kelas Service, 5 kelas Policy, 13 Backed Enums, isolasi dwibahasa middleware `SetLocale`, dan panduan menambahkan aksi bisnis baru.
- [**Arsitektur Frontend (`architecture/frontend.md`)**](./architecture/frontend.md): Pipeline Vite 6, 88 entri aset, aturan wajib Zero Inline CSS/JS pada 185 Blade view, integrasi token DS-MagKom, Anti-Flash Theme Engine (`theme-init.js`), lokalisasi tampilan dwibahasa, dan animasi GSAP 3.15.
- [**Konvensi Penulisan Kode (`development/coding-conventions.md`)**](./development/coding-conventions.md): Standar PSR-12, deklarasi strict typing PHP 8.3, Single Responsibility Actions, form requests, dan konvensi commit Git.
- [**Panduan Lengkap Pengembangan (`development/development-guide.md`)**](./development/development-guide.md): Alur kerja Git, 4 gerbang kualitas wajib (*Quality Gates*), panduan migrasi basis data aman, dan aturan keamanan seeder produksi.
- [**Panduan Penanganan Masalah (`development/troubleshooting.md`)**](./development/troubleshooting.md): Diagnosis dan solusi error umum lingkungan lokal (PHP version mismatch, symlink storage Windows, manifest Vite, error SQLite testing).

---

### 2.3 Dokumentasi Fitur dan Alur Bisnis (Features & Workflows)
Spesifikasi lengkap alur kerja, validasi, dan implementasi teknis per modul fungsional:

- [**Portal Publik & Peta Alur (`features/public-portal.md`)**](./features/public-portal.md): Informasi kuota real-time, logo kemitraan tri-institusi footer (Diskominfo, AMIKOM, MI), layout grid 5-kolom simetris & kolom Tim Pengembang, mobile UX jalan lurus tahapan magang, tombol aksi sejajar, indikator geser kuota carousel mobile, 4-slide hero carousel, perapian kebijakan privasi, captcha SVG internal, dan verifikasi dokumen digital.
- [**Pendaftaran & Alokasi Kuota (`features/registration.md`)**](./features/registration.md): Pendaftaran mandiri dan kelompok berbasis kartu anggota tim dinamis (*Dynamic Team Roster Cards*), penguncian kuota terpilih (*Preselected Quota Locking*), sanitasi tautan login bersih dari query parameter, unggah berkas sesuai jenjang (SMK, D3, S1), penguncian kuota atomik pesimistik (`lockForUpdate`), dan alur pendaftaran ulang pasca ditolak (*re-apply*).
- [**Penempatan Dua Tahap (`features/placement.md`)**](./features/placement.md): Antrean belum ditempatkan, penunjukan pembimbing ASN, batas beban rasio 20 mentee (*mentee limiter*), rotasi pembimbing massal, dan penerbitan Surat Penerimaan PDF ber-QR Code.
- [**Portal Peserta Magang (`features/participants.md`)**](./features/participants.md): Dashboard Bento Grid, pengumpulan tugas classroom, alur pembatalan pengiriman (*unsubmit workflow*), dan presensi logbook harian dengan batas mundur maksimal H-3 kerja.
- [**Pembimbing Lapangan & Mentee (`features/mentors.md`)**](./features/mentors.md): Penugasan serentak multi-mentee (*batch classroom*), review logbook massal (*bulk approval*), inspeksi Profil 360 Mentee, ekspor rekapitulasi kinerja ASN, dan penetapan selesai magang 1-klik.
- [**Administrasi & Verifikasi Berkas (`features/administration.md`)**](./features/administration.md): Antrean seleksi berbatas SLA 5 hari kerja aktif (*SLA Watchdog*), sanitasi anti-formula injection pada ekspor CSV/Excel, streaming download hemat memori via `StreamedResponse`, keputusan verifikasi berkas, pengunduhan bundel arsip ZIP, dan manajemen kuota jurusan.
- [**Tata Kelola SuperAdmin (`features/superadmin.md`)**](./features/superadmin.md): Pengaturan 12 parameter operasional terpusat, matriks perizinan dinamis (*Dynamic RBAC* 13 modul), akun staf dengan `password_awal`, slot khusus kemitraan, pusat backup & restore basis data, telemetri diagnostik, dan perampingan visual site editor berbasis alur interaksi *Click-to-Edit* langsung pada iframe kanvas.

---

### 2.4 Keamanan, RBAC & Jejak Forensik (Security & Governance)
Spesifikasi keamanan siber, kepatuhan hukum kedinasan, dan mitigasi ancaman:

- [**Ikhtisar Keamanan Sistem (`security/security-overview.md`)**](./security/security-overview.md): Filosofi pertahanan berlapis, model ancaman THR-01 s/d THR-16, mitigasi standar SEC-01 s/d SEC-06, konfigurasi header CSP, dan proteksi brute force.
- [**Autentikasi & Otorisasi RBAC (`security/authentication-and-authorization.md`)**](./security/authentication-and-authorization.md): Multi-guard auth, matriks izin dinamis 13 modul sistem, 5 kelas Policy pencegah IDOR, dan akses berkas privat via Temporary Signed URLs (TTL 5 menit).
- [**Rekam Jejak Audit Forensik (`security/audit-trail.md`)**](./security/audit-trail.md): Kepatuhan UU PDP No. 27/2022, arsitektur append-only tabel `audit_log`, klasifikasi 4 kanal stream (`auth`, `aksi`, `service`, `akses_data`), penyensoran rekursif kata sandi/token, dan proteksi ekspor anti-formula injection.

---

### 2.5 Operasional, Infrastruktur & Penerapan (Operations & Infra)
Panduan bagi SysAdmin, DevOps, dan System Engineer untuk menjalankan MAGKOM di lingkungan produksi:

- [**Panduan Penerapan Produksi (`operations/deployment.md`)**](./operations/deployment.md): Prasyarat server produksi, langkah deployment aman, serialisasi cache, hak akses berkas, integrasi Nginx, dan konfigurasi cron scheduler.
- [**Konfigurasi Lingkungan (`operations/environment-configuration.md`)**](./operations/environment-configuration.md): Kamus lengkap seluruh variabel `.env.example`, konfigurasi basis data, sesi, cache, dan prosedur rotasi rahasia.
- [**Pusat Cadangan & Pemulihan (`operations/backup-and-restore.md`)**](./operations/backup-and-restore.md): Arsitektur backup instan `.sql.gz`, verifikasi hash SHA-256, retensi otomatis maksimal 30 berkas (FIFO), dan prosedur pemulihan bencana (*disaster recovery*).
- [**Pemantauan & Telemetri (`operations/monitoring.md`)**](./operations/monitoring.md): Endpoint kesehatan `/health`, 4 aksi diagnostik server SuperAdmin, pelacakan exception Sentry, dan pembatasan konkurensi SSE.
- [**Konfigurasi Surel & SMTP (`operations/email-configuration.md`)**](./operations/email-configuration.md): Pengaturan server SMTP kedinasan, surel verifikasi akun, token reset kata sandi, dan perintah pengujian mandiri via CLI (`auth:test-reset`).
- [**Konfigurasi Server Web Nginx (`operations/nginx-performance-optimization.conf`)**](./operations/nginx-performance-optimization.conf): Berkas konfigurasi Nginx siap pakai dengan FastCGI caching, optimasi SSE buffering off, dan proteksi berkas tersembunyi.

---

### 2.6 Penjaminan Kualitas & Pengujian (Quality Assurance)
Panduan bagi QA Engineer dan Software Engineer dalam memverifikasi kebenaran sistem:

- [**Strategi Pengujian Otomatis (`development/testing.md`)**](./development/testing.md): Katalog 1.067 kasus uji PHPUnit 10 (153 berkas pengujian / 6.713 assertions), arsitektur SQLite in-memory, pengujian konkurensi kuota atomik, pengujian paritas dwibahasa, konsistensi token dark mode, dan pengujian alur pendaftaran ulang.
- [**Spesifikasi Kebutuhan & Aturan Bisnis (`product/requirements.md`)**](./product/requirements.md): Rujukan resmi aturan bisnis definitif (BR-01 s/d BR-22), user stories 4 aktor pengguna, dan pemetaan kriteria penerimaan terhadap automated feature tests.

---

### 2.7 Referensi & Desain (Reference & Design System)
- [**Glosarium Istilah (`reference/glossary.md`)**](./reference/glossary.md): Kamus komprehensif istilah domain kedinasan MAGKOM dan konsep teknis arsitektur perangkat lunak.
- [**Sistem Desain MAGKOM (`design/design-system.md`)**](./design/design-system.md): 11 Master CSS Tokens & Dark Mode Bento Navy (`--dm-*`), tipografi Open Sans & JetBrains Mono, arsitektur Bento Grid, katalog komponen tombol, modal, badge, toast, sakelar tema sidebar footer, dan standar animasi GSAP.
- [**Peta Rute & Endpoint (`architecture/routes.md`)**](./architecture/routes.md): Rujukan lengkap seluruh 211 rute HTTP aktif beserta proteksi middleware dan mapping controller.
- [**Skema Basis Data & Kamus Data (`architecture/database.md`)**](./architecture/database.md): Diagram ERD Mermaid definitif, kamus data detail 24 tabel domain aplikasi dan 5 tabel framework Laravel, relasi kunci asing, dan indeks komposit.
- [**Aliran Data & Siklus Proses (`architecture/data-flow.md`)**](./architecture/data-flow.md): Diagram sekuens alur pendaftaran atomik, penempatan dua tahap, penugasan batch classroom, pengisian logbook H-3, dan sanitasi audit log.
- [**Ekosistem Tooling & Integrasi Agent (`tooling/tooling.md`)**](./tooling/tooling.md): Konfigurasi server MCP (TestSprite, Composio, Playwright, Cloudflare, Sentry), CLI tools (Graphify, Strix), dan skill agents.
- [**Pipeline CI/CD DevSecOps (`tooling/ci-security-and-projects.md`)**](./tooling/ci-security-and-projects.md): Alur kerja GitHub Actions 5 lapisan, 7 branch protection checks, pemindaian Semgrep SAST, Gitleaks, dan manajemen papan tugas.

---

### 2.8 Laporan Audit & Catatan Historis (Reports & Historical Archives)
Arsip laporan evaluasi teknis, pengujian performa, dan riwayat migrasi terdahulu:

- [**Laporan Audit Keamanan, Feature Inventory & UX (`reports/audits/`)**](./reports/audits/): Memuat inventarisasi fitur awal, audit logika integrasi, verifikasi final, dan audit mode canvas.
- [**Laporan Pengujian Kinerja & Benchmark (`reports/performance/`)**](./reports/performance/): Laporan implementasi optimasi performa fase P0 hingga P3 dan laporan validasi performa final.
- [**Laporan Migrasi Teknis & Roadmap Upgrade (`reports/migrations/`)**](./reports/migrations/): Laporan migrasi teknis PHP 8.3/Vite 6, roadmap upgrade Laravel 11/12, dan catatan eksekusi gelombang rilis awal.
- [**Arsip Spesifikasi & Arsitektur Lama (`archive/`)**](./archive/): Penyimpanan dokumen spesifikasi historis terdahulu untuk keperluan preservasi rekam jejak proyek.
