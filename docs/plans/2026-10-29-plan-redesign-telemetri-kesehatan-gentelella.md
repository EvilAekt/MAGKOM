# PLAN: Redesign Total Tampilan Telemetri Kesehatan Infrastruktur SuperAdmin (Gentelella Parity & DS-MagKom Standard)

**Dokumen**: `docs/plans/2026-10-29-plan-redesign-telemetri-kesehatan-gentelella.md`  
**Author**: Erwin Smith (Technical Lead & Orchestrator)  
**Klasifikasi Kompleksitas**: K2 (Multi-modul, UI/UX Design System, Real-Time SSE Telemetry, Security Sweep)  
**Tanggal**: 29 Oktober 2026  
**Status**: APPROVED & IN IMPLEMENTATION (Tahap Delegasi Implementasi ke Eren Yeager)

---

## 1. Executive Summary & Problem Statement

Halaman Telemetri Kesehatan & Diagnostik Sistem SuperAdmin (`resources/views/superadmin/health/index.blade.php`) saat ini memiliki fungsionalitas telemetri komprehensif (latensi database, kapasitas storage, runtime PHP/Laravel, higienitas sweep, grafik Canvas 2D real-time SSE, dan suite pemeliharaan 1-klik). Namun, terdapat beberapa catatan evaluasi arsitektur frontend dan antarmuka pengguna:
1. **Proliferasi Inline Styles**: Masih ditemukan tumpukan atribut `style="..."` inline ad-hoc yang membuat kode sulit dipelihara dan rentan distorsi layout.
2. **Underutilization Komponen Gentelella**: Pustaka komponen Blade Gentelella (`x-panel`, `tile-stat`, `table`, `progress`, `badge`) belum dimanfaatkan secara maksimal.
3. **Penyelarasan dengan Referensi Operations / System Health**: Sir Alan memberikan referensi visual modern System Health Dashboard (Gentelella-inspired Operations layout) dengan elemen:
   - Header status operasional & maintenance banner
   - 4 Hero Metric Tiles (Uptime SLA, Latensi P95, Total Requests/Trafik, Status Database/Ping)
   - Layout 2-Kolom Utama: Grafik kurva latensi real-time (kiri) & Monitor Penggunaan Sumber Daya / Metering progress bar (kanan)
   - Tabel Telemetri Rute HTTP Terkini (`jambo_table` Gentelella)
   - Grid Status Layanan & Suite Aksi Pemeliharaan Proaktif 1-Klik
   - Ringkasan Job Latar Belakang & Higienitas Keamanan
4. **Preservasi Kontrak Fungsional & Automated Test**: Seluruh kontrak pengujian di `tests/Feature/RoutePerformanceTelemetryTest.php` dan `tests/Feature/SuperAdminMasterArchitectureTest.php` wajib dipertahankan 100% tanpa regresi.

---

## 2. Alur Kerja Multi-Agent (RACI Workflow)

Sesuai instruksi eksplisit Sir Alan, alur kerja eksekusi dijalankan secara disiplin melalui protokol terstruktur:

```
[Sir Alan (User)]
       │ (Request: Redesign Telemetri Kesehatan dengan Gentelella)
       ▼
[Erwin Smith] (Technical Lead) ──► 1. Perumusan Goals, Scope & TASK_BRIEF
       │
       ├─────────────────────────► 2. Delegasi Desain ke [Historia Reiss] (UI/UX)
       │                                  │
       │                                  ▼
       │                          Spesifikasi Desain & Token di:
       │                          `docs/plans/2026-10-29-design-spec-telemetri-kesehatan-gentelella.md`
       │                                  │
       ▼                                  ▼
[Erwin Smith] ◄── Review Formal ──────────┘
       │
       ├─ [REJECT/REVISION] ──► Balik ke Historia (Maks. 2 Putaran)
       │
       └─ [APPROVE] ──────────► 3. Delegasi Implementasi ke [Eren Yeager]
                                          │
                                          ▼
                                  Eksekusi Blade & CSS di:
                                  `resources/views/superadmin/health/index.blade.php`
                                          │
                                          ▼
                                  [Levi Ackerman] (Code Review Quality Gate)
                                  [Mikasa Ackerman] (Security & CSRF Audit)
                                  [Annie Leonhart] (QA & PHPUnit/Pest Test Run)
                                          │
                                          ▼
[Erwin Smith] ◄── Verifikasi Hasil ───────┘
       │
       ▼
[Sir Alan (User)] (Final Report & Delivery)
```

### RACI Matrix:
- **Accountable**: Erwin Smith (Technical Lead & Orchestrator)
- **Responsible**:
  - Phase 1 (UI/UX Design Specification & Tokens): Historia Reiss
  - Phase 2 (Blade View & CSS Implementation): Eren Yeager
- **Consulted**: Armin Arlert (Software Architect)
- **Informed & Reviewers**:
  - Levi Ackerman (Code Review & Clean Code Standard)
  - Mikasa Ackerman (Security & CSRF Protection)
  - Annie Leonhart (QA & Test Execution)
  - Sir Alan (Product Owner / User)

---

## 3. Bounded File Scope

- **Allowed Files for Historia Reiss**:
  - `docs/plans/2026-10-29-design-spec-telemetri-kesehatan-gentelella.md`
- **Allowed Files for Eren Yeager**:
  - `resources/views/superadmin/health/index.blade.php`
- **Forbidden Files (Strictly Read-Only)**:
  - `app/**` (Controller & Service telah stabil)
  - `routes/**` (Route definitions tetap tidak berubah)
  - `database/**` (Skema database tidak dimodifikasi)
  - `tests/**` (Test suites berfungsi sebagai oracle pengujian obyektif)

---

## 4. Acceptance Criteria (Definition of Done)

1. **AC-1 (Zero Broken Styles & Gentelella Parity)**: View Blade mengadopsi standar komponen Gentelella (`x-panel`, `tile_stats_count`, `tile_stats_inner`, `jambo_table`, `progress progress_sm`), bersih dari inline CSS ad-hoc.
2. **AC-2 (Hero Status & 4 Metric Cards)**: Menampilkan banner status operasional sistem dan 4 kartu metrik hero (Uptime SLA, P95 Latency, Total Trafik HTTP, Latensi Database) dengan visualisasi angka kontras dan indikator status.
3. **AC-3 (Kurva Real-time Canvas 2D & SSE)**: Mempertahankan elemen `#routeLatencyCanvas` dengan interaktivitas hover tooltip, zero dependency, dan listener SSE `magkom:telemetry-route` yang reaktif terhadap perubahan dark/light mode.
4. **AC-4 (Resource Usage Progress Bars)**: Panel visualisasi kapasitas storage, penggunaan disk, dokumen magang, dan status izin tulis dengan bilah progres berwarna Gentelella.
5. **AC-5 (Enterprise Data Table Rute HTTP)**: Menampilkan tabel telemetri request terkini dengan `jambo_table table-striped`, status code badges (200 OK, 4xx Slow, 5xx Degraded), durasi latensi, dan memory consumption.
6. **AC-6 (Suite Pemeliharaan Proaktif 1-Klik)**: Form aksi diagnostik 4 tombol (`security-sweep`, `optimize-db`, `clear-cache`, `test-storage`) dikemas rapi dengan styling Gentelella button (`.btn.btn-*`), CSRF token, dan konfirmasi interaktif.
7. **AC-7 (Grid Status Layanan & Runtime Info)**: Memvisualisasikan status kesehatan database, file storage, queue worker, dan audit log dengan label status semantik WCAG 2.1 AA.
8. **AC-8 (Test Suites Pass 100%)**: Seluruh pengujian otomatis di `tests/Feature/RoutePerformanceTelemetryTest.php` dan `tests/Feature/SuperAdminMasterArchitectureTest.php` lulus tanpa kegagalan.
