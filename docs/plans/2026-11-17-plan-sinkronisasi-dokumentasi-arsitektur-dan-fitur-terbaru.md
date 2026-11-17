# Rencana Kerja: Sinkronisasi Pustaka Dokumentasi MAGKOM (Arsitektur, Desain Sistem, Fitur & QA)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Memperbarui pustaka dokumentasi teknis MAGKOM di folder `docs/` agar mencerminkan kondisi aktual arsitektur sistem terkini pasca implementasi Dark Mode standarisasi, Multi-Bahasa (Localization ID/EN), isolasi dashboard, perampingan Canvas Editor, penyempurnaan alur pendaftaran ulang (re-apply), dan pembaruan test suite 1.067 test.

**Architecture:** Menggunakan kerangka dokumentasi teknis Diátaxis yang sudah ada pada `docs/`. Pembaruan dilakukan modular per domain tanggung jawab: Desain Sistem & Token CSS, Arsitektur Frontend & Engine Tema, Arsitektur Backend & Middleware Lokalisasi, Fitur Bisnis (Pendaftaran & SuperAdmin Site Editor), serta Penjaminan Kualitas & Pengujian Otomatis.

**Tech Stack:** Markdown (GitHub Flavored Markdown), Mermaid Diagrams, Laravel 10 / PHP 8.3 Architecture, CSS Design Tokens, Vite 6 Pipeline, PHPUnit 10 QA Suite.

**Spec:** Branch `test-2` (commit `7ce2cf0`), `docs/development/darkmode-audit.txt`, `docs/development/bahasa-audit.txt`, hasil verifikasi PHPUnit (1.067 test / 6.713 assertions).

---

## Global Constraints

- **Single Source of Truth**: Dokumentasi harus 100% akurat terhadap kode sumber aktual di branch `test-2` (tanpa halusinasi API atau angka palsu).
- **Diátaxis Framework Compliance**: Pertahankan struktur dokumentasi eksisting (`docs/README.md`, `docs/architecture/`, `docs/design/`, `docs/features/`, `docs/development/`).
- **No Hallucinated Metrics**: Angka pengujian wajib ditulis tepat: **1.067 kasus uji (test methods)** dan **6.713 assertions** (100% PASS).
- **Bilingual & Scope Clarification**: Jelaskan secara eksplisit aturan isolasi bahwa fitur Dark Mode dan Multi-Bahasa secara eksklusif hanya aktif di 4 Portal Dashboard (`/peserta`, `/pembimbing`, `/admin`, `/superadmin`), sedangkan halaman publik dan autentikasi terkunci statis pada Mode Terang dan Bahasa Indonesia.

---

## Review Focus

1. **Akurasi Token Dark Mode**: Memastikan daftar token `--dm-*` pada `docs/design/design-system.md` sesuai dengan `resources/css/base/variables.css`.
2. **Dokumentasi Anti-Flash Engine**: Penjelasan alur kerja synchronous head script `public/js/theme-init.js` dan guard `data-portal="dashboard"` terdokumentasi jelas di `docs/architecture/frontend.md`.
3. **Dokumentasi SetLocale Middleware**: Penjelasan mekanisme isolasi rute dashboard dan prioritas fallback session/cookie tercatat di `docs/architecture/backend.md`.
4. **Katalog Test Suite Baru**: Semua file test baru (`LanguageLocalizationTest`, `LangParityTest`, `DarkModeTokenConsistencyTest`, `DashboardLayoutTest`, `PesertaReapplyAfterRejectionTest`, `AdminSlaWorkingDaysTest`) terdaftar di `docs/development/testing.md`.
5. **Perampingan Site Editor**: Dokumentasi modul Site Editor di `docs/features/superadmin.md` selaras dengan arsitektur super-minimalis baru.

---

## Task Breakdown

### Task 1: Pembaruan Sistem Desain & Token Dark Mode (`docs/design/design-system.md`)
Mendokumentasikan fondasi desain sistem Dark Mode, master tokens `--dm-*`, dan spesifikasi tombol toggle tema terpadu.

**Files:**
- Modify: `docs/design/design-system.md`

**Steps:**
- [x] Buka `docs/design/design-system.md` dan perbarui tabel metadata (*Terakhir Diverifikasi: 2026-11-17*, *Cakupan: Master Tokens, Dark Mode Bento Navy, Bento Grid, Katalog Komponen UI, Animasi GSAP*).
- [x] Pada Bagian 2 (Master CSS Design Tokens), tambahkan dokumentasi lengkap Master Dark Mode Tokens (`--dm-bg-canvas: #0B132B`, `--dm-bg-card: #1C2541`, `--dm-border-color: rgba(255, 255, 255, 0.08)`, dll.) yang terdefinisi di `resources/css/base/variables.css`.
- [x] Tambahkan Sub-bab baru: **Standar Ergonomi Dark Mode & WCAG 2.1 AA**:
  - Palet warna Bento Navy ramah mata (tidak menggunakan hitam pekat `#000000`).
  - Pemilihan warna teks kontras tinggi (`--dm-text-primary: #F8FAFC`, `--dm-text-secondary: #94A3B8`).
  - Lencana status semantik yang disesuaikan untuk latar gelap (`--status-*-dark`).
- [x] Tambahkan Sub-bab baru pada Katalog Komponen: **Komponen Theme Toggle Sidebar Footer**:
  - Tombol tunggal seragam berbasis ikon SVG (`sun-icon` vs `moon-icon`).
  - Penegasan status visual berbasis atribut CSS `html[data-theme="dark"]` dan `html:not([data-theme="dark"])`.
- [x] Verifikasi format markdown dan tautan berkas.

---

### Task 2: Pembaruan Arsitektur Frontend & Engine Anti-Flash (`docs/architecture/frontend.md`)
Mendokumentasikan siklus hidup tema, arsitektur anti-flash FOUC, pipeline Vite, dan lokalisasi tampilan.

**Files:**
- Modify: `docs/architecture/frontend.md`

**Steps:**
- [x] Buka `docs/architecture/frontend.md` dan perbarui tabel metadata (*Terakhir Diverifikasi: 2026-11-17*).
- [x] Perbarui Sub-bab 2.1 (Struktur Entri Aset Vite):
  - Catat penambahan entri stylesheet baru (`resources/css/pages/superadmin/pengaturan.css`, adaptasi layout tokens).
- [x] Tambahkan Bab Baru: **Arsitektur Dark Mode & Anti-Flash Engine**:
  - Penjelasan file eksternal `public/js/theme-init.js` yang dimuat sinkron pada `<head>` sebelum parsing body (mematuhi CSP `script-src 'self'`).
  - Mekanisme guard check isolasi 2 lapis: atribut `data-portal="dashboard"` pada tag `<html>` dan verifikasi ekspresi reguler pathname `/^\/(peserta|pembimbing|admin|superadmin)($|\/)/i`.
  - Pembacaan preferensi hirarkis: `localStorage.magkom_theme` -> cookie `tema_tampilan` -> preferensi sistem OS `prefers-color-scheme`.
  - Penguncian statis halaman non-dashboard (Landing Page, Login, Register, Lupa Password, Peta Alur, Privasi) ke `data-theme="light"` dan `data-pref-theme="terang"`.
- [x] Tambahkan Bab Baru: **Arsitektur Multi-Bahasa Frontend (Localization)**:
  - Struktur kamus bahasa terpadu `lang/id.json` dan `lang/en.json` (4.000+ frasa terjemahan).
  - Standar pemanggilan helper `__()` dan `@lang()` pada view Blade tanpa merusak aturan Zero Inline Code.
- [x] Verifikasi format markdown dan tautan berkas.

---

### Task 3: Pembaruan Arsitektur Backend & Middleware Lokalisasi (`docs/architecture/backend.md`)
Mendokumentasikan middleware `SetLocale`, resolusi session/cookie bahasa, dan proteksi isolasi dashboard.

**Files:**
- Modify: `docs/architecture/backend.md`

**Steps:**
- [x] Buka `docs/architecture/backend.md` dan perbarui tabel metadata (*Terakhir Diverifikasi: 2026-11-17*).
- [x] Pada Sub-bab 3.1 (HTTP Middleware), tambahkan `SetLocale` ke dalam tabel middleware resmi:
  - Kelas: `App\Http\Middleware\SetLocale`
  - Lokasi: `app/Http/Middleware/SetLocale.php`
  - Fungsi: Mengatur locale aplikasi secara dinamis berdasarkan session/cookie `bahasa_portal` khusus untuk 4 rute dashboard (`peserta.*`, `pembimbing.*`, `admin.*`, `superadmin.*`) dan mengunci rute non-dashboard ke Bahasa Indonesia (`id`).
- [x] Tambahkan diagram urutan siklus hidup resolusi bahasa:
  ```mermaid
  sequenceDiagram
      autonumber
      actor User as Peramban Pengguna
      participant MW as Middleware SetLocale
      participant Sess as Sesi / Cookie
      participant App as Laravel App & Carbon
      participant View as Blade View Engine

      User->>MW: HTTP Request Masuk
      alt Rute Dashboard (/peserta, /pembimbing, /admin, /superadmin)
          MW->>Sess: Cek session('bahasa_portal') / cookie('bahasa_portal')
          Sess-->>MW: Kode bahasa ('id' / 'en')
          MW->>App: App::setLocale() & Carbon::setLocale()
          MW->>View: view()->share('bahasaAktif', $locale)
      else Rute Publik / Autentikasi (/, /login, /register, dll)
          MW->>App: Paksa Default ('id')
          MW->>View: view()->share('bahasaAktif', 'id')
      end
      MW->>User: Lanjutkan Response Pipeline
  ```
- [x] Dokumentasikan endpoint pembaruan preferensi tema & bahasa pada Controller akun/pengaturan tiap peran (`*.pengaturan.preferensi.update`).
- [x] Verifikasi format markdown dan tautan berkas.

---

### Task 4: Pembaruan Fitur Bisnis (Pendaftaran, Mobile UX, & SuperAdmin Site Editor)
Memperbarui dokumentasi spesifikasi fitur pada `docs/features/registration.md`, `docs/features/public-portal.md`, dan `docs/features/superadmin.md`.

**Files:**
- Modify: `docs/features/registration.md`
- Modify: `docs/features/public-portal.md`
- Modify: `docs/features/superadmin.md`

**Steps:**
- [x] **Pembaruan `docs/features/registration.md`**:
  - Tambahkan alur bisnis: **Pendaftaran Ulang Pasca Ditolak (Re-apply Workflow)**. Ketika permohonan magang sebelumnya berstatus `ditolak`, peserta diizinkan mengajukan kembali permohonan baru pada periode/jurusan lain tanpa terblokir validasi akun unik (`PesertaReapplyAfterRejectionTest`).
  - Dokumentasikan kartu pilihan kuota mandiri (`_selected_quota_card.blade.php`) yang terisi otomatis dari parameter URL landing page (`?bulan=...&jurusan=...`).
- [x] **Pembaruan `docs/features/public-portal.md`**:
  - Dokumentasikan adaptasi mobile UX (*Lentera Sleman Style*): Bottom sheet navigasi login mobile, tampilan kartu kuota sentuh, dan perapian visual simulator peta alur interaktif.
- [x] **Pembaruan `docs/features/superadmin.md`**:
  - Perbarui Sub-bab Gutenberg-Style Site Editor: Dokumentasikan perampingan arsitektur menjadi **Canvas Block Editor Super-Minimalis** (fokus manipulasi langsung blok tata letak, penghapusan dependensi berat modal reusable block dan riwayat revisi usang).
- [x] Verifikasi keselarasan seluruh alur bisnis terhadap kode implementasi.

---

### Task 5: Pembaruan Penjaminan Kualitas & Pengujian Otomatis (`docs/development/testing.md`)
Memperbarui metrik pengujian, daftar berkas test suite baru, dan panduan menjalankan pengujian lokalisasi serta tema.

**Files:**
- Modify: `docs/development/testing.md`

**Steps:**
- [x] Buka `docs/development/testing.md` dan perbarui metrik pengujian:
  - Jumlah Test Method: **1.067 kasus uji** (dari sebelumnya 1.020).
  - Jumlah Assertions: **6.713 assertions** (dari sebelumnya 6.540).
  - Tingkat Kelulusan: **100% Pass** (0 Failures, 0 Errors).
- [x] Tambahkan katalog suite pengujian baru pada daftar tes penting:
  1. `Tests\Feature\LanguageLocalizationTest`: Pengujian kepatuhan lokalisasi ID/EN, resolusi fallback, pemulihan cookie, dan penguncian rute publik ke `lang="id"`.
  2. `Tests\Unit\LangParityTest`: Pengujian integritas JSON kamus bahasa (100% key parity antara `id.json` dan `en.json`, nilai non-kosong).
  3. `Tests\Feature\DarkModeTokenConsistencyTest`: Pengujian validasi token CSS `--dm-*` dan pencegahan hardcoded hex warna pada dark mode.
  4. `Tests\Feature\DashboardLayoutTest`: Pengujian perenderan toggle tema seragam pada 4 dashboard peran.
  5. `Tests\Feature\PesertaReapplyAfterRejectionTest`: Pengujian pendaftaran ulang peserta setelah status ditolak.
  6. `Tests\Feature\AdminSlaWorkingDaysTest`: Pengujian batas waktu SLA verifikasi berkas berbasis hari kerja aktif dinas.
- [x] Tambahkan perintah eksekusi pengujian spesifik per domain:
  ```powershell
  # Pengujian Lokalisasi & Multi-Bahasa
  php artisan test --filter=LanguageLocalizationTest
  php artisan test --filter=LangParityTest

  # Pengujian Desain Sistem & Dark Mode
  php artisan test --filter=DarkModeTokenConsistencyTest
  php artisan test --filter=DashboardLayoutTest
  ```
- [x] Verifikasi format markdown dan akurasi perintah CLI.

---

### Task 6: Pembaruan Indeks Navigasi & Peta Dokumentasi (`docs/README.md`)
Memperbarui halaman gerbang utama dokumentasi repositori.

**Files:**
- Modify: `docs/README.md`

**Steps:**
- [x] Buka `docs/README.md` dan perbarui tabel informasi:
  - *Terakhir Diverifikasi: 2026-11-17*
  - *Branch Referensi: `test-2` & `main`*
- [x] Perbarui ringkasan pada Sub-bab 2.2 (Panduan Pengembang Aplikasi) dan Sub-bab 2.6 (Penjaminan Kualitas & Pengujian) dengan mencantumkan arsitektur Dark Mode, Multi-Bahasa, dan total 1.067 automated tests.
- [x] Lakukan verifikasi menyeluruh terhadap seluruh tautan internal dokumen (*link validation*).

---

## Kriteria Penyelesaian (Done Criteria)

1. Seluruh 6 dokumen rujukan (`docs/design/design-system.md`, `docs/architecture/frontend.md`, `docs/architecture/backend.md`, `docs/features/registration.md`, `docs/features/superadmin.md`, `docs/development/testing.md`, dan `docs/README.md`) mencerminkan kondisi kode aktual sistem secara 100% presisi.
2. Tidak ada angka kasus uji atau assertions yang kedaluwarsa (terdokumentasi resmi: 1.067 test methods / 6.713 assertions).
3. Aturan isolasi Dark Mode & Multi-Bahasa khusus 4 Dashboard serta penguncian statis mode terang & Bahasa Indonesia pada publik/auth terdokumentasi jelas di seluruh lapisan (desain, frontend, backend, testing).
4. Tidak ada sintaks Markdown atau Mermaid yang rusak.
