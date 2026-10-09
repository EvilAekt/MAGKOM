# Portal Dokumentasi & Panduan Pengembangan MagKom

Selamat datang di direktori pusat dokumentasi dan peta rancangan teknis **MagKom (Magang Kominfo)** — Portal Manajemen Magang Dinas Komunikasi dan Informatika.

Dokumen-dokumen di dalam direktori ini disusun secara komprehensif, modular, dan saling terhubung sebagai **peta panduan tunggal (Single Source of Truth)** agar proses pengembangan website berjalan terarah, terstruktur, dan tidak menyimpang dari spesifikasi.

---

## 🗺️ Peta Navigasi Dokumen

Direktori ini membagi panduan ke dalam 7 dokumen spesialisasi utama:

| No | Dokumen | Fokus & Isi Utama | Audiens Utama |
|:---:|---|---|---|
| **01** | [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) | **Product Requirements Document (PRD) v1.1**<br>• Latar belakang, tujuan produk & target metrik<br>• User stories 4 aktor & matriks hak akses<br>• 17 Aturan Bisnis fundamental (BR-01 s/d BR-17)<br>• Alur & siklus status (State diagram)<br>• Spesifikasi kebutuhan fungsional (F-01 s/d F-19) & Acceptance Criteria (AC)<br>• Spesifikasi form, berkas, dan rubrik penilaian<br>• Daftar asumsi, risiko mitigasi, dan skenario UAT-1 s/d UAT-8 | Product Owner, Project Manager, Developer, QA Tester |
| **02** | [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) | **Technical Design Document (TDD) v1.1**<br>• Stack: Laravel 10.x & PHP 8.3 (InnoDB)<br>• Penyesuaian arsitektur khusus Laravel 10<br>• Struktur direktori aplikasi & pola controller tipis<br>• Pola implementasi kuota atomik (`lockForUpdate`)<br>• Arsitektur route, form request, service, dan policy<br>• Pola sanitasi & append-only audit trail<br>• Pipeline CI/CD, deployment rollback, dan ADR-01 s/d ADR-08 | Tech Lead, Backend / Fullstack Developer, DevOps / SysAdmin |
| **03** | [`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) | **Peta Jalan Eksekusi & Tahapan Gelombang (Anti-Ngaco)**<br>• Urutan kerja bertahap R1 (Gelombang 0 s/d Gelombang 4)<br>• Breakdown file, model, controller, service per gelombang<br>• Kriteria Definition of Ready (DoR) & Definition of Done (DoD)<br>• Rencana rilis lanjutan R2 (Should Have) & R3 (Could Have)<br>• Quality gates & integrasi slash command | Developer, Agen AI Pasangan Programming, Reviewer |
| **04** | [`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) | **Kamus Data, Skema Database & Seeder**<br>• Diagram Relasi Entitas (ERD) Mermaid lengkap<br>• Kamus data detail untuk 20 tabel domain<br>• Definisi foreign key, indeks, dan CHECK constraints database<br>• Spesifikasi master seeder (jurusan, berkas, rubrik, pengaturan) | Database Administrator, Backend Developer |
| **05** | [`05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](file:///g:/MAGKOM/docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md) | **Spesifikasi Keamanan, RBAC & Audit Trail**<br>• Matriks perizinan RBAC lengkap 4 role<br>• Pola isolasi SuperAdmin Read-Only pada ranah pembimbing<br>• Implementasi `AuditedService` & 4 tipe audit (`auth`, `aksi`, `service`, `akses_data`)<br>• Hak akses database append-only (`GRANT SELECT, INSERT`)<br>• Keamanan berkas privat, temporary signed route (5 menit), dan HTTP security headers | Security Engineer, Tech Lead, Backend Developer |
| **06** | [`06-ADDENDUM-KEAMANAN-DAN-QA.md`](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md) | **Addendum Keamanan dan Pengujian (QA) v1.0**<br>• Model ancaman konkret (THR-01 s/d THR-16)<br>• Spesifikasi kebutuhan keamanan (SEC-01 s/d SEC-74, P0/P1/P2)<br>• Matriks hak akses detail HTTP & state akun<br>• Katalog uji per AC (edge case negatif & konkurensi)<br>• Alarm keamanan audit log & runbook insiden siber | Security Engineer, QA Tester, DevOps, Developer |
| **07** | [`07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) | **Design System MagKom (DS-MagKom) v1.0**<br>• Fondasi token: 30 warna `MagKom/Warna`, tipografi Open Sans & JetBrains Mono<br>• Penyelarasan kontras WCAG 2.1 AA (K-1 s/d K-5)<br>• Spesifikasi komponen: Tombol, form, badge status 5 varian, alert, modal konfirmasi, tabel, timeline<br>• Pemetaan status lengkap PRD (enum ke UI label, varian, dan ikon Lucide)<br>• Aturan wajib tampilan (AC-DS-1 s/d AC-DS-11) & panduan Tailwind/Blade | Frontend Developer, UI/UX Designer, Product Owner |

---

## ⚡ 5 Prinsip Emas Pengembangan MagKom ("Guardrail Kunci")

Sebelum menulis kode atau membuat komponen baru, pastikan mematuhi prinsip mutlak berikut:

1. **Penerimaan & Kuota Wajib Atomik (BR-05 & NFR-D1)**:
   Seluruh pengurangan/penambahan kuota umum dan slot khusus wajib diapit transaksi database dengan `lockForUpdate()`. Tidak boleh ada risiko kuota minus pada akses konkuren.
2. **SuperAdmin Read-Only pada Ranah Pembimbing (D-05, A-05)**:
   SuperAdmin dapat melihat seluruh aktivitas penugasan, progres, logbook, dan penilaian, namun **dilarang keras** memiliki tombol/rute mutasi untuk membuat tugas, mereview progres/logbook, atau mengubah nilai. Otorisasi ini dijaga ketat di tingkat Policy.
3. **Penyimpanan Berkas di Disk Privat (NFR-S6)**:
   Seluruh berkas pendaftaran peserta disimpan di disk `private` dengan nama acak. Berkas hanya dapat diunduh melalui temporary signed URL bertanda tangan HMAC dengan masa kedaluwarsa 5 menit setelah melewati pemeriksaan Policy kepemilikan.
4. **Audit Trail Komprehensif & Append-Only (D-08, NFR-A1 s/d NFR-A4)**:
   Setiap aksi tulis bisnis, eksekusi service method, dan akses pembacaan data wajib tercatat rapi ke tabel `audit_log` dengan penyensoran kata sandi/token secara otomatis. Akun database aplikasi tidak memiliki hak `UPDATE` maupun `DELETE` pada tabel audit.
5. **Tanpa Sistem Notifikasi Otomatis (D-02, BR-14)**:
   Sistem tidak mengirim email notifikasi penerimaan, WhatsApp, atau pengingat batas waktu tugas. Satu-satunya pengiriman email adalah tautan verifikasi email saat registrasi dan tautan reset password. Peserta memantau status secara mandiri lewat dashboard.

---

## 🚀 Alur Kerja Implementasi yang Dianjurkan

Bagi developer atau AI Assistant yang akan melanjutkan pengerjaan:
1. Buka dan pelajari [**`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`**](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) untuk melihat gelombang pengerjaan yang sedang aktif.
2. Kerjakan tugas secara bertahap mulai dari **Gelombang 0 (Fondasi & Audit)**.
3. Gunakan slash commands atau workflow terstandar yang tersedia di workspace:
   - `/plan` : Merencanakan arsitektur detail sebelum menulis kode fitur kompleks.
   - `/new-feature` : Membangun fitur baru sesuai acceptance criteria di PRD.
   - `/fix-bug` : Mengidentifikasi akar masalah dan memperbaiki anomali secara terfokus.
   - `/review` : Meninjau keamanan OWASP, RBAC, dan audit trail sebelum merge.
   - `/ui-check` : Memverifikasi responsivitas (360px+) dan aksesibilitas antarmuka.
   - `/release` : Melakukan quality gate check sebelum rilis gelombang.
