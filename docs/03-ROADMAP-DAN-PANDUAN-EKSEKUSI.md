# Peta Jalan & Panduan Eksekusi Pengembangan MagKom

Dokumen ini adalah **peta navigasi implementasi teknis (Execution Roadmap)** untuk membangun portal MagKom (Magang Kominfo) dari nol secara sistematis, terukur, dan terkontrol. Tujuannya adalah memastikan developer atau agen AI memiliki panduan langkah-demi-langkah yang presisi tanpa menyimpang dari spesifikasi bisnis dan arsitektur teknis.

---

## 1. Prinsip Utama Eksekusi ("Anti-Ngaco")

1. **Ikuti Urutan Gelombang (Wave-by-Wave)**: Jangan melompat ke fitur pendaftaran atau tugas sebelum fondasi autentikasi, enum, database, dan audit selesai di Gelombang 0 dan 1.
2. **Atomic & Test-Driven**: Setiap penyelesaian tugas harus disertai unit/feature test yang relevan sebelum lanjut ke tugas berikutnya.
3. **No Hallucinated APIs**: Selalu gunakan arsitektur Laravel 10 dan PHP 8.3 yang terdokumentasi di [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md).
4. **Guardrail Bisnis Kunci**:
   - Kuota mutlak dikunci dengan `lockForUpdate()` di dalam transaksi.
   - SuperAdmin berstatus read-only pada ranah pembimbing (tugas, review, penilaian).
   - Seluruh entitas memakai flag nonaktif, bukan soft deletes atau hard deletes.
   - Berkas wajib disimpan di disk privat dengan unduhan signed URL berumur 5 menit.
   - Tanpa sistem notifikasi atau blast email (hanya email verifikasi akun dan reset password).

---

## 2. Peta Rilis & Gelombang Pengembangan

```mermaid
gantt
    title Roadmap Eksekusi Rilis MagKom
    dateFormat  YYYY-MM-DD
    section Rilis 1 (Must Have)
    Gelombang 0: Fondasi & Arsitektur Inti       :active, g0, 2026-10-08, 3d
    Gelombang 1: Konfigurasi & Landing Page     :g1, after g0, 3d
    Gelombang 2: Akun, Pendaftaran & Kuota Atom :g2, after g1, 5d
    Gelombang 3: Pelaksanaan & Penilaian         :g3, after g2, 5d
    Gelombang 4: Penguatan, Hardening & UAT      :g4, after g3, 3d
    section Rilis 2 (Should Have)
    Rilis 2: Captcha, Lampiran & Ekspor          :r2, after g4, 5d
    section Rilis 3 (Could Have)
    Rilis 3: Login Sosial (Google & Facebook)    :r3, after r2, 4d
```

---

## 3. Detail Eksekusi Gelombang Rilis 1 (R1)

### Gelombang 0: Fondasi & Arsitektur Inti
> **Tujuan**: Menyiapkan pondasi proyek Laravel 10, skema database dasar, enum, RBAC, dan sistem audit trail sebelum fitur bisnis dibangun.

#### Daftar File & Komponen yang Dibuat:
1. **Enums (`app/Enums/`)**:
   - `RoleEnum.php` (`superadmin`, `admin`, `pembimbing`, `peserta`)
   - `JenjangEnum.php` (`smk`, `d3`, `s1`)
   - `JalurEnum.php` (`umum`, `khusus`)
   - `SumberPenempatanEnum.php` (`online`, `manual`)
   - `StatusKetersediaanEnum.php` (`tersedia`, `tutup`, `segera`)
   - `StatusPendaftaranEnum.php` (`berkas_belum_lengkap`, `diajukan`, `perlu_perbaikan`, `waiting_list`, `diterima`, `ditolak`, `dibatalkan`)
   - `StatusPenempatanEnum.php` (`aktif`, `selesai`, `mengundurkan_diri`, `nonaktif`)
   - `StatusTugasEnum.php` (`belum`, `proses`, `selesai`)
   - `KompleksitasEnum.php` (`rendah`, `sedang`, `tinggi`)
   - `StatusReviewProgresEnum.php` (`menunggu`, `diterima`, `perlu_revisi`)
   - `StatusLogbookEnum.php` (`terkirim`, `perlu_revisi`)
   - `StatusPenilaianEnum.php` (`draft`, `final`)
   - `TipeAuditEnum.php` (`auth`, `aksi`, `service`, `akses_data`)

2. **Database Migrations (`database/migrations/`)**:
   - `0001_create_users_table.php` (dengan kolom role, status_aktif, must_change_password)
   - `0002_create_master_tables.php` (`jurusan`, `bidang`, `rubrik`, `pengaturan`)
   - `0003_create_audit_log_table.php` (append-only dengan JSON detail & index komprehensif)

3. **Core Services & Middleware**:
   - `app/Services/AuditService.php`: Layanan pencatatan audit tersanitasi.
   - `app/Services/AuditedService.php`: Abstract class pembungkus eksekusi service method.
   - `app/Http/Middleware/EnsureUserHasRole.php`: Middleware multi-role.
   - `app/Http/Middleware/EnsurePasswordChanged.php`: Middleware penegak ganti password pertama staf.
   - `app/Http/Middleware/LogAuditTrail.php`: Terminable middleware untuk audit log akses data.
   - `app/Http/Middleware/SecurityHeaders.php`: Injeksi header keamanan HTTP.

4. **Seeders**:
   - `JurusanSeeder.php` (6 jurusan standar)
   - `RubrikSeeder.php` (5 kriteria awal bobot 100)
   - `PengaturanSeeder.php` (8 parameter konfigurasi)
   - `SuperAdminSeeder.php` (Akun SuperAdmin awal dari env)

#### Checklist Verifikasi Gelombang 0:
- [ ] `php artisan migrate:fresh --seed` berjalan mulus tanpa error.
- [ ] Test auth & role middleware menghasilkan status 403 bila role tidak diizinkan.
- [ ] AuditService berhasil mencatat log saat aksi mutasi database dipanggil.

---

### Gelombang 1: Konfigurasi & Landing Page Publik (F-11, F-05, F-06, F-01)
> **Tujuan**: Memungkinkan SuperAdmin & Admin mengatur periode bulan, kuota umum, dan slot khusus, serta menampilkan status publik yang akurat di landing page.

#### Daftar File & Komponen yang Dibuat:
1. **Migrations & Models**:
   - `create_bulan_magang_table.php` & `BulanMagang.php`
   - `create_kuota_table.php` & `Kuota.php` (dengan check constraint `terisi <= jumlah`)
   - `create_slot_khusus_table.php` & `SlotKhusus.php`

2. **Services & Support**:
   - `app/Services/BulanStatusService.php` (Logika penentuan Tersedia, Tutup, Segera & filter landing page)
   - `app/Support/Pengaturan.php` (Helper cache konfigurasi sistem)

3. **Controllers & Routes**:
   - `app/Http/Controllers/Public/LandingController.php` (F-01)
   - `app/Http/Controllers/SuperAdmin/BulanController.php` (Kelola bulan & override status)
   - `app/Http/Controllers/SuperAdmin/SlotKhususController.php` (F-06: Slot internal)
   - `app/Http/Controllers/Admin/KuotaController.php` (F-05: Kuota umum per jurusan)

4. **Views (Blade)**:
   - `resources/views/public/landing.blade.php` (Responsive 360px+, WCAG AA, grouping SMK & Mahasiswa)
   - `resources/views/superadmin/bulan/`
   - `resources/views/admin/kuota/`

#### Checklist Verifikasi Gelombang 1:
- [x] Feature test `LandingPageTest`: memastikan slot khusus **tidak pernah bocor** di response publik (AC-01-5).
- [x] Perubahan override status SuperAdmin langsung mengubah status landing page.
- [x] Admin tidak dapat menurunkan jumlah kuota di bawah `terisi` (AC-05-3).

---

### Gelombang 2: Akun, Pendaftaran & Kuota Atomik (F-02, F-03, F-04, F-07)
> **Tujuan**: Membangun alur pendaftaran peserta, upload berkas terenkripsi/privat, proses verifikasi admin, penempatan otomatis, dan transaksi kuota atomik anti-race condition.

#### Daftar File & Komponen yang Dibuat:
1. **Migrations & Models**:
   - `create_peserta_profil_table.php` & `PesertaProfil.php`
   - `create_jenis_dokumen_table.php` & `JenisDokumen.php`
   - `create_pendaftaran_table.php` & `Pendaftaran.php`
   - `create_dokumen_table.php` & `Dokumen.php`
   - `create_penempatan_table.php` & `Penempatan.php`

2. **Services**:
   - `app/Services/QuotaAllocationService.php` (Implementasi `lockForUpdate()` & rollback atomik)
   - `app/Services/PlacementService.php` (Alur penerimaan online & input manual peserta khusus)
   - `app/Support/PeriodeMagang.php` (Kalkulasi tanggal 1 s/d akhir bulan durasi N)

3. **Policies**:
   - `app/Policies/PendaftaranPolicy.php`
   - `app/Policies/DokumenPolicy.php`
   - `app/Policies/PenempatanPolicy.php`

4. **Controllers & Form Requests**:
   - `app/Http/Controllers/Peserta/ProfilController.php`
   - `app/Http/Controllers/Peserta/PendaftaranController.php`
   - `app/Http/Controllers/Peserta/BerkasController.php`
   - `app/Http/Controllers/Admin/PendaftaranController.php` (Review & Keputusan)
   - `app/Http/Controllers/Admin/WaitingListController.php`
   - `app/Http/Controllers/Admin/PenempatanController.php`

#### Checklist Verifikasi Gelombang 2:
- [x] **Test Konkurensi MySQL**: Uji 2 request bersamaan untuk 1 kuota sisa; verifikasi tepat 1 yang diterima dan tidak terjadi kuota minus (AC-05-4).
- [x] Berkas tersimpan di disk `private` dan link download memiliki masa kedaluwarsa 5 menit serta signed signature valid.
- [x] Validasi BR-17 (hanya 1 pendaftaran aktif per peserta) berjalan sempurna.

---

### Gelombang 3: Pelaksanaan & Penilaian (F-08, F-09, F-10, F-12)
> **Tujuan**: Menjalankan siklus magang aktif: pembagian tugas, unggah progres, pengisian logbook harian berbatas waktu, penilaian rubrik pembimbing, ekspor PDF Lembar Nilai, serta otomatisasi masa tenggang.

#### Daftar File & Komponen yang Dibuat:
1. **Migrations & Models**:
   - `create_tugas_table.php` & `Tugas.php` (dengan accessor runtime `terlambat`)
   - `create_progres_log_table.php` & `ProgresLog.php`
   - `create_logbook_table.php` & `Logbook.php` (unique `penempatan_id` + `tanggal`)
   - `create_penilaian_tables.php` (`penilaian`, `penilaian_detail`, `penilaian_riwayat`)

2. **Services & PDF**:
   - `app/Services/ScoringService.php` (Hitung skor terbobot, konversi predikat A-E)
   - `app/Services/MasaTenggangService.php` (Kalkulasi `tgl_nonaktif`)
   - `app/Console/Commands/NonaktifkanPesertaSelesai.php` (Command harian idempotent)
   - Blade PDF Template: `resources/views/pdf/lembar-nilai.blade.php`

3. **Policies**:
   - `app/Policies/TugasPolicy.php`
   - `app/Policies/LogbookPolicy.php`
   - `app/Policies/PenilaianPolicy.php` (SuperAdmin strictly read-only)

4. **Controllers**:
   - `app/Http/Controllers/Pembimbing/TugasController.php`
   - `app/Http/Controllers/Pembimbing/LogbookReviewController.php`
   - `app/Http/Controllers/Pembimbing/PenilaianController.php`
   - `app/Http/Controllers/Peserta/TugasController.php`
   - `app/Http/Controllers/Peserta/LogbookController.php`
   - `app/Http/Controllers/Peserta/NilaiController.php`

#### Checklist Verifikasi Gelombang 3:
- [x] Pengisian logbook mundur lebih dari batas konfigurasi (default 3 hari) ditolak validasi (BR-09).
- [x] SuperAdmin ditolak dengan status HTTP 403 saat mencoba membuat tugas atau memberi nilai (AC-08-5, AC-10-7).
- [x] Unduh Lembar Nilai PDF berhasil di-generate secara on-the-fly dari snapshot nilai terkini.
- [x] Scheduler harian menonaktifkan akun yang melewati `tgl_nonaktif` secara aman dan idempotent.

---

### Gelombang 4: Penguatan, Hardening & UAT
> **Tujuan**: Menyelesaikan pengujian end-to-end, audit keamanan, hardening proteksi, dan validasi seluruh skenario UAT-1 s/d UAT-8 sesuai PRD Bab 12.3.

#### Fokus Pengerjaan & Implementasi:
1. **Security Hardening**:
   - [x] Sanitasi kredensial otomatis pada `AuditService` (masking `password`, `token`, `secret`, `api_key` menjadi `[DISAMARKAN]`).
   - [x] Implementasi Middleware `SecurityHeaders` (`X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`, CSP).
   - [x] Konfigurasi Rate Limiter di `RouteServiceProvider` (Auth login: 5 req/min, Pendaftaran: 10 req/jam, API: 60 req/min).
2. **Automated End-to-End Test Suite**:
   - [x] `tests/Feature/EndToEndUatTest.php` mencakup 8 skenario UAT lengkap sesuai PRD Bab 12.3:
     - **UAT-1**: Konfigurasi master bulan magang, kuota jurusan (12), slot khusus (3), status override, dan landing page publik.
     - **UAT-2**: Calon peserta mendaftar dari landing page, verifikasi email, upload berkas wajib ke private disk, hingga status Diajukan.
     - **UAT-3**: Alur perbaikan berkas oleh admin, pengajuan ulang oleh peserta, penerimaan, serta pembentukan penempatan atomik.
     - **UAT-4**: Otomatisasi antrean Waiting List saat kuota reguler jurusan terisi penuh (AC-01-3 / BR-05).
     - **UAT-5**: Penempatan manual jalur khusus (internal/rekomendasi) dengan proteksi Zero Leak informasi di landing page publik (AC-01-5).
     - **UAT-6**: Pelaksanaan tugas bertenggat waktu, runtime accessor keterlambatan (BR-16), reviu progres bertahap, dan pengisian logbook harian berbatas mundur (BR-09).
     - **UAT-7**: Penilaian akhir 5 rubrik kompetensi terbobot, riwayat audit revisi nilai (AC-10), dan pencetakan instan Lembar Nilai PDF resmi (AC-12).
     - **UAT-8**: Eksekusi console scheduler `magkom:nonaktifkan-peserta` saat masa tenggang kedaluwarsa (BR-07), serta verifikasi integritas audit trail append-only.

#### Hasil Verifikasi UAT:
- [x] Seluruh 8 skenario UAT (**54 assertions**) lulus 100% (`PASS`).
- [x] Seluruh pengujian aplikasi (**34 tests, 183 assertions**) lulus 100% tanpa adanya regresi.

---

## 4. Rencana Rilis Lanjutan (R2 & R3)

### Rilis 2 (Should Have) — [SELESAI 100%]
- [x] **F-14 (Captcha Proteksi Bot)**: Integrasi Cloudflare Turnstile pada registrasi (fail-closed) dan login (fail-open) dengan Rate Limiting.
- [x] **F-15 (Lampiran Logbook Harian)**: Fitur unggah file PDF laporan (maks 2MB) ke disk privat & tautan eksternal aman skema `https://` (`rel="noopener noreferrer"`).
- [x] **F-16 (Surat Keterangan Diterima PDF)**: Template dan generator on-the-fly dokumen resmi PDF Surat Keterangan Diterima Magang berkop dinas untuk peserta dan admin.
- [x] **F-17 (Laporan & Ekspor Data)**: Ekspor CSV/Spreadsheet rekapitulasi pendaftaran, penempatan, realisasi kuota, dan nilai (khusus SuperAdmin) dengan proteksi sanitasi *formula injection* (`=`, `+`, `-`, `@`) dan pencatatan audit log `AC-13-4`.
- [x] **Automated Feature Test**: `tests/Feature/ReleaseTwoFeaturesTest.php` lulus 100% (4 tests, 44 assertions).

### Gelombang Tambahan: Penyempurnaan Administrasi, Audit Trail & Utility — [SELESAI 100%]
- [x] **F-11 (Akun Staf & Penegakan BR-15)**: CRUD staf internal (Admin & Pembimbing) oleh SuperAdmin, reset password staf, nonaktifkan staf, dan form/eksekusi ganti kata sandi wajib staf (`must_change_password`) anti-kata sandi lama.
- [x] **F-11 (Master Data)**: CRUD master jurusan (penegakan BR-12), bidang dinas, rubrik penilaian (penegakan bobot 100%), dan pengaturan parameter sistem dengan auto cache invalidation.
- [x] **F-13 (Audit Log Viewer)**: Endpoint `GET /superadmin/audit-log` strictly read-only, paginasi 25 baris, filter tanggal, tipe, entitas, dan request ID.
- [x] **F-04 & F-07 (Operasi Penempatan Lanjutan)**: Pemindahan pembimbing dengan validasi batas beban (BR-13), pembatalan penempatan / pengunduran diri sesuai BR-07 (sebelum vs sesudah mulai magang), dan tutup antrean waiting list massal awal bulan (AC-04-7).
- [x] **F-02, F-12 & NFR**: Registrasi scheduler harian di Kernel (`magkom:nonaktifkan-peserta`), alur verifikasi email peserta, alur lupa/reset password peserta dengan respon seragam anti-enumeration, endpoint healthcheck `/health`, dan halaman kebijakan privasi `/kebijakan-privasi`.
- [x] **Automated Test Suite**: `tests/Feature/BackendSupplementalFeaturesTest.php` lulus 100% (5 tests, 47 assertions).


### Rilis 3 (Could Have)
- [x] **F-18 (OAuth Google Social Login)**: Registrasi & Login "Continue with Google" via Laravel Socialite khusus akun role Peserta dengan validasi penautan email anti-pembajakan (*anti-email-hijacking*, NFR-S9, AC-18-1) dan pemblokiran login sosial untuk peran staf.
- [ ] **F-19 (OAuth Facebook Social Login)**: Menyusul setelah integrasi Google stabil dan verifikasi aplikasi Meta selesai (opsional/future release).
- [x] **Automated Feature Test**: `tests/Feature/ReleaseThreeSocialLoginTest.php` lulus 100% (11 tests, 57 assertions).

---

## 5. Ringkasan Keterkaitan Dokumen

Untuk navigasi lebih dalam, silakan buka:
- 📄 Spesifikasi Kebutuhan Lengkap: [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md)
- 🛠️ Desain Arsitektur & Teknis: [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md)
- 🗄️ Kamus Data & Skema Database: [`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md)
- 🔒 Blueprint Keamanan, RBAC & Audit: [`05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](file:///g:/MAGKOM/docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md)
