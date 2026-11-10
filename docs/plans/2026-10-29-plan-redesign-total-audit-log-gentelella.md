# PLAN: Redesign Total Tampilan Audit Log SuperAdmin (Gentelella Parity & DS-MagKom Standard)

**Dokumen**: `docs/plans/2026-10-29-plan-redesign-total-audit-log-gentelella.md`  
**Author**: Erwin Smith (Technical Lead & Orchestrator)  
**Klasifikasi Kompleksitas**: K2 (Multi-modul, UI/UX Design System, Security Audit Trail)  
**Tanggal**: 29 Oktober 2026  
**Status**: DRAFT (Diserahkan ke Historia Reiss untuk Spesifikasi Desain)

---

## 1. Executive Summary & Problem Statement

Halaman Jejak Audit Sistem Multi-Kanal SuperAdmin (`resources/views/superadmin/audit-log/index.blade.php`) saat ini telah memiliki fungsionalitas dasar 15 kanal dan modal diff inspeksi. Namun, dari segi arsitektur antarmuka dan estetika frontend, halaman tersebut memiliki sejumlah kelemahan kritis:
1. **Proliferasi Inline Styles**: Ratusan baris kode menggunakan atribut `style="..."` inline secara ad-hoc, menyebabkan inkonsistensi visual, pembengkakan ukuran DOM, dan sulitnya pemeliharaan (*maintenance nightmare*).
2. **Underutilization Komponen Gentelella**: Sistem telah memiliki pustaka komponen Blade standar (`resources/views/components/gentelella/`), seperti `tile-stat`, `x-panel`, `table` (`jambo_table`), `badge`, dan `filter-bar`, namun halaman audit log belum memanfaatkannya secara optimal.
3. **Hirarki Navigasi Kanal Kurang Padu**: Daftar 15 kanal di kolom kiri masih menggunakan layout kustom yang belum menyatu sempurna dengan pola Gentelella Sidebar/List Group.
4. **Data Table Perlu Standardisasi Paritas Tinggi**: Tabel data perlu diselaraskan dengan standar `table jambo_table table-striped bulk_action` dengan top accent ribbon `3px solid #01347C`, status severity pills WCAG 2.1 AA, dan badge aktor yang profesional.
5. **Modal Diff Inspector Perlu Penyegaran**: Modal inspeksi JSON perlu dirapikan dengan struktur Gentelella modal yang elegan, pembagian tab/kolom side-by-side responsif, dan dukungan keyboard accessibility.

---

## 2. Alur Kerja Multi-Agent (RACI Workflow)

Sesuai arahan Sir Alan dan protokol Orkestrasi Erwin Smith, alur eksekusi dibagi ke dalam tahapan sekuensial yang ketat:

```
[Erwin Smith] ──(1. TASK_BRIEF & Req)──> [Historia Reiss] (UI/UX Spec & Tokens)
                                                  │
                                                  ▼
[Erwin Smith] <──(2. RESULT_REPORT: Spec)─────────┘
      │
      ├─ [Reject/Revision] ──> Balik ke Historia (Maks. 2 Putaran)
      │
      └─ [APPROVE] ──(3. TASK_BRIEF Implementasi)──> [Eren Yeager] (Blade & CSS Implementation)
                                                             │
                                                             ▼
                                                    [Levi Ackerman] (Code Review)
                                                    [Mikasa Ackerman] (Security Audit)
                                                    [Annie Leonhart] (QA & Regresi Test)
                                                             │
                                                             ▼
[Sir Alan / Erwin Smith] <──(4. Final Delivery & Merge Gate)─┘
```

### RACI Matrix:
- **Accountable**: Erwin Smith (Technical Lead & Orchestrator)
- **Responsible**:
  - Phase 1 (Design): Historia Reiss (Lead Designer)
  - Phase 2 (Implementation): Eren Yeager (Senior Developer)
- **Consulted**: Armin Arlert (Software Architect)
- **Informed & Reviewers**:
  - Levi Ackerman (Final Code Review & Quality Gate)
  - Mikasa Ackerman (AppSec & Audit Log Data Integrity)
  - Annie Leonhart (QA & Test Execution)
  - Sir Alan (Product Owner / User)

---

## 3. Spesifikasi Kebutuhan & Target Arsitektur UI (Gentelella Parity)

### 3.1. Struktur Grid & Tata Letak (12-Column Responsive)
- **Master Layout**: Menginduk ke `layouts.dashboard` (`@extends('layouts.dashboard')`).
- **Header Section**:
  - Breadcrumbs Gentelella (`SuperAdmin / Audit Log / [Kanal]`).
  - Page Title: `Jejak Audit Sistem Multi-Kanal (Audit Trail)`.
  - Page Action: Tombol Ekspor CSV dengan ikon Font Awesome `fa fa-download` (`.btn.btn-primary`).
- **Row 1: 4 Hero Stat Tiles (`<x-gentelella.tile-stat>`)**:
  1. *Peristiwa 24 Jam*: Total peristiwa tercatat dalam 24 jam terakhir (Ikon: `fa fa-clock-o`, Warna: `teal`/`primary`).
  2. *Total Log Database*: Akumulasi seluruh log tersimpan (Ikon: `fa fa-database`, Warna: `blue`/`info`).
  3. *Peringatan Keamanan*: Insiden keamanan severity danger/warning 24 jam (Ikon: `fa fa-shield`, Warna: `red`/`danger`).
  4. *Aliran Terpantau*: Total 15 kanal aktif (Ikon: `fa fa-sitemap`, Warna: `yellow`/`warning`).
- **Row 2: 2-Kolom Grid (Desktop: `col-md-3 col-sm-4` & `col-md-9 col-sm-8`)**:
  - **Kolom Kiri (Kanal Navigator Panel)**:
    - Kontainer: `<x-gentelella.x-panel title="<i class='fa fa-filter'></i> Kanal Audit" subtitle="15 Aliran Peristiwa" :toolbox="true" :collapsible="true">`.
    - Navigasi Kanal: Mengadopsi pola list-group Gentelella dengan icon representatif, nama kanal, badge counter 24 jam, dan state active yang tegas.
    - Footer Panel: Kartu penegasan *Append-Only Immutability & Anti-Tamper Guarantee*.
  - **Kolom Kanan (Main Stream Workspace)**:
    - *Widget Banner Kanal Aktif*: Menampilkan detail kanal terpilih, deskripsi, color accent ribbon, dan total hitungan baris query.
    - *Filter Toolbar Panel (`<x-gentelella.x-panel>`)*: Collapsible filter dengan input kata kunci (`q`), dropdown Severity, Tipe Audit, Aktor Staf, rentang tanggal (`dari_tanggal` & `sampai_tanggal`), tombol Reset (`.btn.btn-default`), dan tombol Terapkan Filter (`.btn.btn-primary`).
    - *Enterprise Data Table Panel (`<x-gentelella.x-panel class="has-table">`)`:
      - Menggunakan komponen atau struktur `<x-gentelella.table>` (`table jambo_table table-striped bulk_action`).
      - Aksen pita atas `3px solid #01347C`.
      - Judul tabel `.sa-card-table-title` (Slate 700 `#334155` / Slate 300 `#CBD5E1`).
      - Kolom terstruktur: Waktu (WIB + Relative), Kanal & Severity Pill, Aktor (Inisial avatar + nama + role), Aksi & Entitas Mutasi, Konteks HTTP (Method, Status, IP, Request ID), dan Aksi Inspeksi.
      - Pagination bar rapi di footer panel.
- **Row 3: Modal Inspeksi Diff JSON (Tri-Core Inspector)**:
  - Header modal dengan ID log, kanal, dan severity badge.
  - Metadata grid 2-kolom ringkas (Aktor, Waktu, IP, Request ID, Entitas, Rute HTTP).
  - Side-by-side diff mutasi (*Nilai Lama* vs *Nilai Baru*) dengan syntax highlighting yang kontras.
  - Event payload detail viewer.
  - Keyboard accessibility (Esc key to close) dan backdrop click handler.

### 3.2. Standar Desain Token & Aksesibilitas
- Mematuhi DS-MagKom Design Tokens (`--blue-600`, `--neutral-*`, `--table-*`).
- **Dual-Theme**: Keterbacaan kontras tinggi di Light Mode (latar `#FFFFFF`) dan Dark Mode (Navy `#003566` / `#02275D`, border `#3F51B5`).
- **WCAG 2.1 AA**: Rasio kontras minimal 4.5:1 untuk teks biasa dan 3:1 untuk label badge.
- **Pure CSS**: Eliminasi seluruh inline JavaScript hover (`onmouseover`/`onmouseout`).

---

## 4. Bounded File Scope

### Allowed Files:
- **Dokumentasi & Desain**:
  - `docs/plans/2026-10-29-plan-redesign-total-audit-log-gentelella.md` (Dokumen ini)
  - `docs/plans/2026-10-29-design-spec-audit-log-gentelella.md` (Spesifikasi UI/UX oleh Historia Reiss)
- **Implementasi Tampilan Blade**:
  - `resources/views/superadmin/audit-log/index.blade.php` (Implementasi oleh Eren Yeager)

### Forbidden Files:
- `app/**` (Logika backend, kueri database, controller, dan enum tidak boleh diubah)
- `routes/**` (Rute audit log sudah stabil dan final)
- `database/**` (Skema migrasi database tidak boleh disentuh)
- `public/css/gentelella-theme.css` (Gunakan class utilitas dan CSS variables eksisting)

---

## 5. Acceptance Criteria

- **AC-1 (Gentelella Parity & Zero Inline Styles Mess)**: Tampilan audit log 100% selaras dengan tema Gentelella DS-MagKom; meminimalisir inline styles ad-hoc dan memanfaatkan class Gentelella / token CSS sentral.
- **AC-2 (4 Hero Metric Stat Cards)**: Menampilkan 4 kartu metrik berbasis Gentelella tile-stat (`tile_stats_count` / `tile_stats_inner`) dengan data dinamis riil.
- **AC-3 (Kanal Navigator & Active State)**: 15 kanal tersaji rapi dalam panel navigasi kiri dengan badge counter 24 jam dan status kanal aktif yang jelas.
- **AC-4 (Comprehensive Filter Toolbar)**: Form pencarian dan filter multi-parameter lengkap (`q`, `severity`, `tipe`, `user_id`, tanggal) beroperasi sempurna tanpa kehilangan konteks parameter URL.
- **AC-5 (Enterprise Jambo Data Table)**: Tabel audit menggunakan kelas `jambo_table` dengan striping rapi, top accent ribbon `3px solid #01347C`, judul tabel Slate 700 / Slate 300, dan severity badge WCAG 2.1 AA.
- **AC-6 (Modern Tri-Core Diff Modal)**: Modal inspeksi JSON menampilkan metadata lengkap dan komparasi side-by-side nilai lama vs baru dengan navigasi ramah keyboard (Esc).
- **AC-7 (Dual-Theme & WCAG 2.1 AA Compliance)**: Tampilan nyaman dan kontras di Light Mode maupun Dark Mode tanpa teks pudar atau latar bertabrakan.
- **AC-8 (Zero Functional Regression)**: Seluruh feature tests `Tests\Feature\SuperAdminDashboardAndAuditLogTest` wajib lulus 100% (4 passed, 30 assertions).
