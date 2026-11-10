# Indeks Dokumen Perencanaan & Task Briefs (docs/plans)

Direktori ini memuat seluruh berkas spesifikasi teknis tingkat pengerjaan (*task briefs*), rencana implementasi berurutan (*playbooks*), dan dokumen PRD ad-hoc yang memandu eksekusi fitur harian di repositori **MAGKOM**.

---

## 📌 Status Dokumen Perencanaan Kerja

### 1. In Progress (Sedang Dikerjakan di Working Tree)
Dokumen-dokumen brief berikut merepresentasikan tugas aktif yang saat ini sedang dikerjakan oleh developer di working tree:
- [`2026-11-09-senior-dev-task-roadmap-remediasi-superadmin-prd.md`](./2026-11-09-senior-dev-task-roadmap-remediasi-superadmin-prd.md) — Roadmap 4 Prioritas perbaikan antarmuka SuperAdmin (koneksi diagnostik server, migrasi inline onclick ke `data-*`, pemindahan inline script Blade ke `resources/js/pages/superadmin/`, dan refaktorisasi `LandingCmsController`).
- [`2026-11-09-animasi-gsap-timeline-superadmin-dan-chart-hover.md`](./2026-11-09-animasi-gsap-timeline-superadmin-dan-chart-hover.md) — Implementasi timeline entrance animation GSAP dan hover micro-interactions pada dashboard SuperAdmin.
- [`2026-11-09-senior-dev-task-audit-komprehensif-seluruh-fitur-superadmin-prd.md`](./2026-11-09-senior-dev-task-audit-komprehensif-seluruh-fitur-superadmin-prd.md) — Audit komprehensif seluruh antarmuka dan kepatuhan CSP modul SuperAdmin.

---

### 2. Implemented & Verified (Telah Selesai Diimplementasikan)
Rencana kerja berikut telah selesai dieksekusi, diuji melalui Feature Tests, dan telah masuk ke basis kode aktif branch `test-2`:

#### A. Decommissioning Modul Usang & Penyederhanaan Alur
- [`2026-11-09-decommissioning-total-fitur-waiting-list-dan-pembersihan-residu.md`](./2026-11-09-decommissioning-total-fitur-waiting-list-dan-pembersihan-residu.md) — Eliminasi antrean Waiting List dan pembersihan residu antrean verifikasi.
- [`2026-11-09-senior-dev-task-penghapusan-menyeluruh-fitur-penilaian-dan-skala-predikat-prd.md`](./2026-11-09-senior-dev-task-penghapusan-menyeluruh-fitur-penilaian-dan-skala-predikat-prd.md) — Penghapusan permanen tabel penilaian dan rubrik nilai (migrasi `drop_penilaian_and_rubrik_tables`).
- [`2026-11-09-senior-dev-task-penghapusan-menyeluruh-fitur-sertifikat-dan-survei-kepuasan-prd.md`](./2026-11-09-senior-dev-task-penghapusan-menyeluruh-fitur-sertifikat-dan-survei-kepuasan-prd.md) — Penghapusan modul sertifikat digital dan survei kepuasan pasca-magang.
- [`2026-11-09-senior-dev-task-penghapusan-fitur-katalog-modul-kustom-prd.md`](./2026-11-09-senior-dev-task-penghapusan-fitur-katalog-modul-kustom-prd.md) — Penghapusan Dynamic CRUD Module Builder.
- [`2026-11-09-senior-dev-task-penghapusan-menyeluruh-fitur-monitoring-peserta-superadmin-prd.md`](./2026-11-09-senior-dev-task-penghapusan-menyeluruh-fitur-monitoring-peserta-superadmin-prd.md) — Decommissioning modul monitoring peserta SuperAdmin.
- [`2026-11-09-penghapusan-analitik-tahunan-dan-realisasi-kuota-laporan.md`](./2026-11-09-penghapusan-analitik-tahunan-dan-realisasi-kuota-laporan.md) — Eliminasi grafik analitik tahunan pada modul laporan admin.

#### B. Fitur Baru Tata Kelola, Keamanan & Bimbingan
- [`2026-11-09-senior-dev-task-fitur-backup-database-superadmin-prd.md`](./2026-11-09-senior-dev-task-fitur-backup-database-superadmin-prd.md) — Pusat cadangan basis data terisolasi (Disaster Recovery) dan snapshot `.sql.gz`.
- [`2026-11-09-senior-dev-task-sinkronisasi-matriks-hak-akses-role-dan-eliminasi-modul-usang-prd.md`](./2026-11-09-senior-dev-task-sinkronisasi-matriks-hak-akses-role-dan-eliminasi-modul-usang-prd.md) — Sinkronisasi matriks hak akses 13 modul sistem dan pembersihan permission usang.
- [`2026-11-09-senior-dev-task-revamp-total-profil-peserta-bimbingan-pembimbing-prd.md`](./2026-11-09-senior-dev-task-revamp-total-profil-peserta-bimbingan-pembimbing-prd.md) — Revamp Profil 360 Mentee: donut chart, timeline durasi magang, dan micro-interactions GSAP.
- [`2026-11-09-senior-dev-task-standarisasi-peserta-khusus-dan-tabel-slot-khusus-prd.md`](./2026-11-09-senior-dev-task-standarisasi-peserta-khusus-dan-tabel-slot-khusus-prd.md) — Standardisasi pendaftaran peserta jalur khusus kemitraan dan modal salin `password_awal`.
- [`2026-11-09-senior-dev-task-standarisasi-pagination-10-tabel-superadmin-prd.md`](./2026-11-09-senior-dev-task-standarisasi-pagination-10-tabel-superadmin-prd.md) — Standardisasi paginasi 10 entri per halaman di seluruh tabel master SuperAdmin.
- [`2026-11-09-senior-dev-task-standarisasi-parameter-konfigurasi-sistem-dan-fitur-baru-prd.md`](./2026-11-09-senior-dev-task-standarisasi-parameter-konfigurasi-sistem-dan-fitur-baru-prd.md) — Standardisasi 12 parameter operasional aplikasi.
- [`2026-11-07-canvas-site-editor-prd.md`](./2026-11-07-canvas-site-editor-prd.md) & [`2026-11-08-site-editor-reusable-block-and-pattern-library.md`](./2026-11-08-site-editor-reusable-block-and-pattern-library.md) — WordPress-Style Gutenberg Visual Site Editor, Block Engine, Reusable Blocks, dan Media Library.
- [`2026-11-01-pendaftaran-kelompok-magkom.md`](./2026-11-01-pendaftaran-kelompok-magkom.md) — Pendaftaran magang kelompok (1 ketua + maks. 3 anggota) dengan kuota atomik.

---

## 💡 Panduan Penambahan Task Brief Baru

Bagi developer atau AI Assistant yang menyusun rencana kerja baru:
1. Beri nama berkas berformat `YYYY-MM-DD-<slug-deskriptif>-prd.md` atau `plan.md`.
2. Selalu cantumkan dokumen induk terkait ([`01-PRD-MagKom.md`](../01-PRD-MagKom.md), [`02-TDD-MagKom.md`](../02-TDD-MagKom.md), [`.agents/rules/AGENTS.md`](../../.agents/rules/AGENTS.md)).
3. Susun daftar checklist `- [ ]` bertahap (*step-by-step*) dengan instruksi verifikasi linter dan test otomatis yang jelas.
4. Gunakan relative Markdown links yang portable.

