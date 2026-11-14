# Project Knowledge & Memory Base: MAGKOM

Dokumen ini adalah ringkasan memori dan konteks arsitektur proyek untuk AI Agents dan Developer di repositori **MAGKOM**.

---

## 1. Arsitektur & Aturan Proyek
- **Tech Stack**: **Laravel 10.x** (platform terkunci), **PHP 8.3.x Mutlak**, **MySQL 8.0+ / MariaDB 10.5+ (InnoDB)** untuk runtime, **SQLite `:memory:`** untuk testing suite otomatis, **Blade (Server-Rendered Markup)**, **Vite 6.x**, **GSAP 3.15.1**.
- **Frontend Standard**: **Scoped Vanilla CSS (Design Tokens & CSS Variables)** dan **Modular Vanilla JavaScript (ES Modules)**. Framework eksternal seperti Tailwind utility baru di Blade, Bootstrap, Alpine.js, React, atau jQuery **dilarang keras** sesuai aturan permanen [`project-rules/frontend.md`](./project-rules/frontend.md).
- **Aturan Pengembangan Agen AI**: Sumber kebenaran tunggal aturan agen tersedia di direktori [`project-rules/`](./project-rules/).
- **Knowledge Graph Codebase**: Tersedia di direktori `graphify-out/` dan laporan [CODEBASE_GRAPH_REPORT.md](./CODEBASE_GRAPH_REPORT.md).

---

## 2. Model & Relasi Utama
- **User Roles (4 Peran Resmi RBAC)**:
  1. `superadmin`: Pengendalian 12 parameter sistem, matriks izin dinamis (`permissions`), manajemen akun pegawai, disaster recovery backup, telemetri kesehatan & aksi diagnostik, serta Visual Site Editor.
  2. `admin`: Verifikasi berkas pendaftar (SLA 5 hari, Diterima/Ditolak), alokasi kuota umum bulanan, penempatan 2 tahap (plotting pembimbing), dan ekspor laporan Excel.
  3. `pembimbing`: Penugasan proyek bimbingan ber-deadline, review laporan progres, validasi logbook mingguan (H-3), Profil 360 Mentee, penyelesaian magang 1-klik, dan ekspor portofolio kinerja ASN (PDF/Excel).
  4. `peserta`: Pendaftaran mandiri/kelompok, unggah berkas sesuai jenjang SMK/D3/S1, penerimaan tugas proyek, pengisian logbook mingguan, dan unduh SK penerimaan.
- **Activity & Audit Trail**: Pencatatan mutasi data immutable di `/superadmin/audit-log` dengan 4 kanal stream (`auth`, `pendaftaran`, `penempatan`, `system`) dan sensor otomatis data rahasia.
- **Modul Unggulan Sistem**:
  - *Pendaftaran Kelompok* (`F-03A`): Rombongan magang maksimal 4 orang (1 ketua + hingga 3 anggota) dengan alokasi kuota atomik.
  - *WordPress-Style Visual Site Editor*: Pengelolaan blok konten dinamis landing page publik dengan Gutenberg Canvas, Pattern Library, Reusable Blocks, Media Library, Staging Sandbox, serta Revisions History.
  - *Pusat Cadangan Basis Data (Disaster Recovery)*: Pembuatan snapshot basis data terisolasi format `.sql.gz`, validasi SHA-256, unduh privat, dan restore.
  - *Telemetri Kesehatan & Diagnostik*: Command center pemantauan performa server dan 4 aksi diagnostik (Security Sweep, Optimize DB, Clear Cache, Storage Test).
  - *Realtime Stream*: Notifikasi interaktif berbasis Server-Sent Events (SSE) `/realtime/stream`.

---

## 3. Dokumentasi Induk Resmi
Untuk pemahaman mendalam, baca dokumen resmi:
- [`01-PRD-MagKom.md`](../01-PRD-MagKom.md) (Product Requirements Document v1.3)
- [`02-TDD-MagKom.md`](../02-TDD-MagKom.md) (Technical Design Document v1.3)
- [`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](../04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) (Kamus Data 24 Tabel Domain + 5 Tabel Framework)
- [`05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](../05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md) (Matriks RBAC & Audit Trail)
- [`07-DESIGN-SYSTEM-MAGKOM.md`](../07-DESIGN-SYSTEM-MAGKOM.md) (Design System MagKom)
- [`DEVELOPMENT-GUIDE.md`](../DEVELOPMENT-GUIDE.md) (Panduan Orientasi & Quality Gates)
- [`architecture/FRONTEND-ARCHITECTURE.md`](../architecture/FRONTEND-ARCHITECTURE.md) (Arsitektur Aset Frontend Vite)
- [`08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md`](../08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md) (Dokumentasi Fitur & Actions)
- [`09-PANDUAN-SMTP-DAN-NOTIFIKASI-EMAIL.md`](../09-PANDUAN-SMTP-DAN-NOTIFIKASI-EMAIL.md) (Panduan Layanan SMTP & Notifikasi Email)
- [`architecture/ROUTES.md`](../architecture/ROUTES.md) (Peta Rute & Proteksi Middleware)

---

## 4. Skills yang Terhubung
Agent skills aktif dan terhubung di `.agents/skills/`. Gunakan workflow terstruktur seperti `dev-workflows`, `web-planning`, `web-implementation`, dan tool security Strix sesuai standar quality gates MAGKOM.
