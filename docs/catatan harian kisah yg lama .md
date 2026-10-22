10 Paket Tugas Bertahap (Sprint Breakdown - Estimasi ± 17 Jam):

Tugas 1: Baseline Test Suite (tests/Feature/GentelellaDashboardRevampTest.php) (🟢 Mudah, 30 mnt)
Tugas 2: Pembuatan Komponen Blade Reusable (x-panel, tile-stat, page-title) (🟡 Sedang, 1,5 jam)
Tugas 3: Transformasi Master Layout 

layouts/dashboard.blade.php
 & CSS Modern (🔴 Tinggi, 2,5 jam)
Tugas 4: Redesign Dashboard SuperAdmin 

superadmin/dashboard.blade.php
 (🟡 Sedang, 2 jam)
Tugas 5: Redesign Dashboard Admin 

admin/dashboard.blade.php
 (🟡 Sedang, 2 jam)
Tugas 6: Redesign Dashboard Pembimbing 

pembimbing/dashboard.blade.php
 (🟡 Sedang, 2 jam)
Tugas 7: Penyelarasan Layout & Dashboard Peserta 

peserta/dashboard.blade.php
 (🟡 Sedang, 2 jam)
Tugas 8: Interaktivitas UI & Responsivitas Mobile (🟡 Sedang, 1,5 jam)
Tugas 9: Integrasi Dark Mode & Aksesibilitas WCAG (🟢 Mudah, 1 jam)
Tugas 10: Pengujian Regresi Otomatis PHPUnit & Pembersihan Kode (🔴 Tinggi, 1,5 jam)

---

### Fase 2: Redesign Seluruh Child Pages & Sub-Modul ke Template Gentelella (Estimasi ± 26,5 Jam)
Dokumen Rencana: docs/plans/2026-10-21-redesign-child-pages-gentelella-template.md

Paket A: Fondasi & Standardisasi Komponen Reusable Blade (Tugas A1) (🟢 Mudah, 45 mnt)
Paket B: Sub-Modul Admin Operasional (Tugas B1 - B5) (🟡 Sedang, 6,5 jam)
Paket C: Sub-Modul Pembimbing Lapangan (Tugas C1 - C4) (🟡 Sedang, 6 jam)
Paket D: Sub-Modul Peserta Magang (Tugas D1 - D4) (🟡 Sedang, 5 jam)
Paket E: Sub-Modul SuperAdmin & Master Data (Tugas E1 - E4) (🟡 Sedang / 🔴 Tinggi, 5,5 jam)
Paket F: Modul Pengaturan & Profil Terpadu (Tugas F1) (🟢 Mudah, 1 jam)
Paket G: Quality Gate, Linter Pint & Verifikasi Regresi 100% Green (Tugas G1) (🔴 Tinggi, 1,5 jam)

---

### Fase 3: Eksekusi Menyeluruh Child Pages Gentelella (Estimasi ± 19,5 Jam)
Dokumen Rencana: docs/plans/2026-10-22-sprint-fase-3-redesign-komprehensif-child-pages-gentelella.md

Siklus 1: Seleksi & Penempatan Admin (Tugas 1.1 - 1.5) (🟡 Sedang, 4,5 jam)
Siklus 2: Pembimbing Lapangan (Tugas 2.1 - 2.3) (🟡 Sedang, 3,5 jam)
Siklus 3: Peserta Magang (Tugas 3.1 - 3.4) (🟡 Sedang, 4,25 jam)
Siklus 4: SuperAdmin & Observabilitas (Tugas 4.1 - 4.4) (🟡 Sedang / 🔴 Tinggi, 5,5 jam)
Siklus 5: Quality Gate & Finishing (Tugas 5.1 - 5.2) (🔴 Tinggi, 1,75 jam)

---

### Status Terkini & Hasil Verifikasi Implementasi Fase 3 (2026-10-21):
- **Komponen Reusable Gentelella Selesai**:
  - `resources/views/components/gentelella/modal.blade.php`
  - `resources/views/components/gentelella/action-btn.blade.php`
  - `resources/views/components/gentelella/filter-bar.blade.php`
  - `resources/views/components/gentelella/table.blade.php`
  - `resources/views/components/gentelella/x-panel.blade.php`
  - `resources/views/components/gentelella/tile-stat.blade.php`
  - `resources/views/components/gentelella/page-title.blade.php`
- **Child Pages yang Berhasil Diberi Sentuhan Gentelella Clean Port**:
  - **Admin**: `admin/pendaftaran/index.blade.php`, `admin/waiting-list/index.blade.php`, `admin/penempatan/index.blade.php`.
  - **Pembimbing**: `pembimbing/peserta/index.blade.php`, `pembimbing/logbook/index.blade.php`, `pembimbing/penilaian/index.blade.php`, `pembimbing/penilaian/show.blade.php`, `pembimbing/agenda/index.blade.php`, `pembimbing/tugas/index.blade.php`.
  - **SuperAdmin**: `superadmin/bulan/index.blade.php`, `superadmin/bidang/index.blade.php`, `superadmin/jurusan/index.blade.php`, `superadmin/slot-khusus/index.blade.php`, `superadmin/staf/index.blade.php`, `superadmin/role/index.blade.php`, `superadmin/rubrik/index.blade.php`.
- **Hasil Quality Gate 100% Green**:
  - **PHPUnit Feature & Unit Test Suite**: `457 passed, 0 failed (2,682 assertions)`.
  - **PHP Coding Standards (Laravel Pint)**: `Passed (0 syntax/formatting violations)`.
  - **Knowledge Graph Synchronization (Graphify)**: `Synchronized (7,224 nodes, 13,839 edges, 566 communities)`.
- **Integritas Logika & Keamanan**:
  - 0 Controller/Model/Route/Migration/Request Logic Touched.
  - 0 jQuery / Bootstrap 3 Dependency Injected (100% Vanilla Modern CSS & Vanilla JS).
  - Seluruh DOM ID sakral dan string assertion tes dipertahankan seutuhnya.

---

### Fase 4: Unifikasi Seluruh Card, Metric Widget, & Callout ke Desain Gentelella (Estimasi ± 18 Jam)
Dokumen Rencana: docs/plans/2026-10-22-unifikasi-card-dan-widget-gentelella-semua-role.md

Siklus 1: Standarisasi Blade Card Primitives & CSS Engine (Tugas 1.1 - 1.4) (🟢 Mudah / 🟡 Sedang, 2,5 jam)
Siklus 2: Unifikasi Card Portal Admin Operasional (Tugas 2.1 - 2.5) (🟡 Sedang, 4 jam)
Siklus 3: Unifikasi Card Portal Pembimbing Lapangan (Tugas 3.1 - 3.4) (🟡 Sedang, 3,5 jam)
Siklus 4: Unifikasi Card Portal Peserta Magang (Tugas 4.1 - 4.4) (🟡 Sedang, 4 jam)
Siklus 5: Unifikasi Card Portal SuperAdmin, Auth Layer & Quality Gate (Tugas 5.1 - 5.5) (🟡 Sedang / 🔴 Tinggi, 4 jam)