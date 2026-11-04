# Project Knowledge & Memory Base: MAGKOM

Dokumen ini adalah ringkasan memori dan konteks arsitektur proyek untuk AI Agents dan Developer di **Sokudo IDE / Antigravity**.

## 1. Arsitektur & Aturan Proyek
- **Tech Stack**: **Laravel 10.x** (platform PHP locked), **PHP 8.3**, **MySQL 8.0+ / MariaDB 10.4+ (InnoDB)**, **Blade (Server-Rendered Markup)**, **Vite 6**.
- **Frontend Standard**: **Vanilla CSS (Design Tokens & Scoped CSS)** dan **Vanilla JavaScript (Modular ES Modules)**. Framework eksternal seperti Tailwind, Bootstrap, Alpine.js, React, atau jQuery **dilarang keras** sesuai aturan permanen [`AGENTS.md`](file:///g:/MAGKOM/.agents/rules/AGENTS.md).
- **Project Rules**: Tersedia di direktori [`.agents/rules/AGENTS.md`](file:///g:/MAGKOM/.agents/rules/AGENTS.md).
- **Knowledge Graph Codebase**: Tersedia di direktori `graphify-out/` dan laporan [CODEBASE_GRAPH_REPORT.md](./CODEBASE_GRAPH_REPORT.md).

## 2. Model & Relasi Utama
- **User Roles (4 Role Resmi RBAC)**:
  1. `superadmin`: Pengendalian sistem, konfigurasi, audit trail, module builder, disaster recovery.
  2. `admin`: Verifikasi berkas pendaftar, alokasi kuota, plotting pembimbing, penerbitan sertifikat.
  3. `pembimbing`: Penugasan tugas mentoring, review progres log, tinjauan logbook mingguan, penilaian akhir.
  4. `peserta`: Pendaftaran mandiri/kelompok, unggah berkas, penerimaan tugas, pengisian logbook mingguan.
  *(Catatan: Peran fiktif seperti 'Instruktur' atau 'Reviewer' tidak ada di sistem ini).*
- **Activity & Audit Trail**: Pencatatan mutasi data immutable di `/superadmin/audit-log` dengan masking rekursif data rahasia.
- **Modul Unggulan**:
  - *Pendaftaran Kelompok* (`F-03A`): Rombongan magang maksimal 4 orang (1 ketua + hingga 3 anggota) dengan alokasi kuota atomik.
  - *Dynamic CRUD Module Builder* (`F-20`): Generator modul data low-code SuperAdmin (skema hybrid JSON/EAV atau tabel basis data fisik).
  - *Realtime Stream*: Notifikasi interaktif berbasis Server-Sent Events (SSE) `/realtime/stream`.
  - *Health Monitor*: Pemeriksaan kesehatan infrastruktur, latensi DB, sweep keamanan di `/superadmin/health-monitor`.

## 3. Dokumentasi Induk
Untuk pemahaman mendalam, baca dokumen resmi:
- [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Product Requirements Document v1.2)
- [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Technical Design Document v1.2)
- [`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) (Kamus Data 32 Tabel)
- [`DEVELOPMENT-GUIDE.md`](file:///g:/MAGKOM/docs/DEVELOPMENT-GUIDE.md) (Panduan Orientasi & Quality Gates)
- [`architecture/ROUTES.md`](file:///g:/MAGKOM/docs/architecture/ROUTES.md) (Peta Rute & Proteksi Middleware)

## 4. Skills yang Terhubung
Agent skills aktif dan terhubung di `.agents/skills/`. Gunakan workflow terstruktur seperti `dev-workflows`, `web-planning`, `web-implementation`, `impeccable`, dan tool security Strix sesuai standar quality gates MAGKOM.
