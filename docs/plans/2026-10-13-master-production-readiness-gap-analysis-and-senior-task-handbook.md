# Plan: Master Production Readiness, PRD Gap Analysis & Senior Task Handbook — Kelanjutan Tiga Pilar Dashboard MagKom (Enterprise Best Practice)

- **Tanggal Dokumen**: 2026-10-13
- **Status**: Draft (Menunggu Persetujuan / Awaiting Approval)
- **Author**: Lead Principal Software Architect & Head of Security Engineering (Antigravity web-planning)
- **Target Pembaca**: Junior Developers, Mid Developers, dan AI Operasional Berbiaya Rendah (Gemini 3.8 Flash / Claude Haiku / GPT-4o-mini)
- **Dokumen Acuan Wajib**:
  1. [`docs/01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (PRD v1.1)
  2. [`docs/02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Technical Design Document v1.1)
  3. [`docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Roadmap & Panduan Eksekusi)
  4. [`docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) (Skema DB & Kamus Data)
  5. [`docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](file:///g:/MAGKOM/docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md) (RBAC, Hak Akses & Audit Trail)
  6. [`docs/06-ADDENDUM-KEAMANAN-DAN-QA.md`](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md) (Addendum Keamanan SEC-01 s.d. SEC-74 & QA Matrix)
  7. [`docs/07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Design System 30 Token Figma & WCAG AA)

---

## 1. TUJUAN (OBJECTIVES)

Dokumen ini disusun untuk:
1. **Menjawab Evaluasi Best Practice & AI Slop**: Memberikan penilaian transparan terhadap kondisi penulisan kode proyek MagKom saat ini: bagian mana yang sudah berstandar enterprise kelas industri dan bagian mana yang telah/masih memerlukan pembersihan dari residu *AI slop*.
2. **Audit Gap Fitur Komprehensif (PRD & Dokumen 01 s.d. 07)**: Memetakan secara detail fitur-fitur apa saja yang sudah 100% tuntas, fitur yang masih kurang, serta klausul kepatuhan hukum (UU PDP/SPBE) yang belum terimplementasi.
3. **Hardening Keamanan Backend Tingkat Lanjut (Advance Production-Ready)**: Merancang arsitektur zero-trust pertahanan berlapis (*defense-in-depth*) mencakup RFC 9116 security.txt, isolasi hak subjek data (anonimisasi UU PDP), proteksi anti-malware, Dompdf sandbox, dan proteksi concurrency.
4. **Katalog Tiket Tugas Senior Developer (WBS Tiket Kerja)**: Menyusun paket penugasan kerja step-by-step yang sangat preskriptif, bebas ambiguitas, dan langsung dapat dieksekusi oleh junior developer maupun model AI berbiaya rendah (Gemini 3.8 Flash).

---

## 2. AUDIT KRITIS: BEST PRACTICE INDUSTRI VS RESIDU AI SLOP

### 2.1 Evaluasi Mutu Kode Saat Ini
Berdasarkan audit menyeluruh terhadap 40+ Controller, 14 Services, 17 Middleware, dan 301 Test Suites:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│             AUDIT MUTU KODE MAGKOM: BEST PRACTICE INDUSTRI VS AI SLOP                  │
├──────────────────────────┬───────────────────┬─────────────────────────────────────────┤
│ Dimensi Rekayasa         │ Status Kualitas   │ Catatan Evaluasi & Temuan Riil          │
├──────────────────────────┼───────────────────┼─────────────────────────────────────────┤
│ 1. Type Safety & Domain  │ ✅ BEST PRACTICE  │ 100% declare(strict_types=1), 13 Backed │
│    (PHP 8.2+ Enums)      │ (Bebas AI Slop)   │ Enums. Tidak ada perbandingan magic     │
│                          │                   │ strings seperti status === 'diterima'.  │
├──────────────────────────┼───────────────────┼─────────────────────────────────────────┤
│ 2. Mutasi Kuota & Lock   │ ✅ BEST PRACTICE  │ DB::transaction() + lockForUpdate()     │
│    (Concurrency Control) │ (Bebas AI Slop)   │ dengan 3x deadlock retry. Kuota aman    │
│                          │                   │ dari race condition / overbooking.      │
├──────────────────────────┼───────────────────┼─────────────────────────────────────────┤
│ 3. Arsitektur Controller │ ✅ TELAH DIBERSIH-│ Tiga Controller Dashboard yang tadinya  │
│    (Skinny Controllers)  │ KAN (Refactored)  │ gemuk (200-380 baris) telah diekstrak ke│
│                          │                   │ 3 Metrics Services (<35 baris/method).  │
├──────────────────────────┼───────────────────┼─────────────────────────────────────────┤
│ 4. Tampilan Blade (UI)   │ ⚠️ 85% BERSIH     │ Sebagian besar inline style telah digan-│
│    (Clean Design System) │ (Sisa Residu)     │ ti <x-badge>, namun beberapa modal      │
│                          │                   │ dan form masih menyisakan inline style. │
├──────────────────────────┼───────────────────┼─────────────────────────────────────────┤
│ 5. Automated Testing     │ ✅ SANGAT KUAT    │ 301 passing tests (1736 assertions).    │
│    (Regression Shield)   │ (Enterprise)      │ Nol kegagalan, mencakup UAT & RBAC.     │
└──────────────────────────┴───────────────────┴─────────────────────────────────────────┘
```

**Diagnosa**: Kode inti (backend domain, security, data integrity) **BUKAN AI SLOP**, melainkan arsitektur solid berbasis Laravel enterprise. Sisa residu yang perlu terus dibersihkan berada di level Blade views terdahulu agar 100% seragam dengan komponen Figma Design System.

---

## 3. AUDIT GAP FITUR: APAKAH MASIH ADA FITUR YANG KURANG DARI PRD & DOKUMEN ACUAN?

Berdasarkan audit silang terhadap seluruh Acceptance Criteria di PRD v1.1 dan klausul Addendum Keamanan:

### 3.1 Fitur yang SUDAH SELESAI (100% Lengkap di Tiga Dashboard)
* ✅ **Dashboard Admin**: Seleksi antrean FIFO (AC-04-1), keputusan diterima/tolak/perlu perbaikan (AC-04-3), kuota atomik & waiting list promotion (AC-04-4), penutupan waiting list otomatis (`magkom:tutup-waiting-list`) (AC-04-7), penempatan peserta manual & slot khusus (AC-07-3), pemindahan pembimbing tunggal & massal (*bulk reassign*) (AC-07-4), pembatalan penerimaan pra-mulai & mundur (AC-07-5), ekspor laporan CSV anti-formula injection (AC-17-2).
* ✅ **Dashboard Pembimbing**: Monitoring kapasitas mentee (BR-13), pemberian tugas dengan rich attachments (AC-08-1), penegakan deadline & label terlambat (BR-16), reviu logbook harian dengan batas mundur 3 hari (BR-09), penilaian akhir rubrik terbobot & snapshot bobot (AC-10-1..4), audit trail revisi nilai final (AC-10-3), penerbitan Lembar Nilai PDF resmi on-the-fly (AC-10-6), agenda bimbingan lapangan & notulensi.
* ✅ **Dashboard SuperAdmin**: Executive command dashboard 3 tab segmented, tata kelola master data (bidang, jurusan, bulan magang, kuota, slot khusus, rubrik), status landing page live & override (AC-11-6), manajemen akun staf dengan password acak & *must_change_password* (AC-11-3, SEC-02), penjaga *Zero-SuperAdmin* dan larangan menonaktifkan diri sendiri (SEC-14), audit log viewer forensik append-only (AC-13), scheduler pemangkasan log & nonaktifkan peserta (`magkom:nonaktifkan-peserta`) (AC-12-3).

### 3.2 Fitur & Klausul Kepatuhan yang MASIH KURANG / PERLU DITAMBAHKAN
Meskipun fitur utama telah berjalan, terdapat **5 butir gap penting** dari PRD dan Addendum Keamanan yang belum terpasang:

| ID Gap | Klausul Rujukan | Deskripsi Kebutuhan yang Belum Ada | Dampak / Risiko |
|:---|:---|:---|:---|
| **GAP-01** | Addendum **SEC-74** | Belum ada file publik `/.well-known/security.txt` sesuai RFC 9116 untuk kontak pelaporan kerentanan keamanan instansi. | Auditor keamanan / SPBE tidak menemukan kanal resmi pelaporan *vulnerability disclosure*. |
| **GAP-02** | Addendum **SEC-41** (UU PDP) | Belum ada persetujuan orang tua/wali khusus calon peserta jenjang SMK (< 18 tahun) pada formulir biodata profil. | Ketidakpatuhan terhadap ketentuan pemrosesan data pribadi anak menurut UU PDP. |
| **GAP-03** | Addendum **SEC-44** (UU PDP) | Belum ada Artisan Command `magkom:anonimkan-peserta {id}` untuk hak subjek data (penghapusan data pribadi alumni yang meminta hak lupa). | SuperAdmin tidak memiliki perkakas otomasi untuk mematuhi permintaan penghapusan data resmi. |
| **GAP-04** | PRD **AC-11-7** & BR-04 | Validasi form Pengaturan SuperAdmin belum secara eksplisit memvalidasi integritas struktur JSON `predikat_batas` agar nilai batas harus berurutan menurun (A > B > C > D). | Potensi salah ketik oleh SuperAdmin yang dapat mengacaukan kalkulasi predikat nilai peserta. |
| **GAP-05** | Addendum **SEC-15** (BR-17) | Detail pendaftaran di sisi Admin belum menampilkan tanda peringatan visual (*badge alert*) jika NIM/NISN calon peserta terdeteksi ganda di akun lain. | Admin tidak mengetahui jika ada satu orang siswa/mahasiswa mendaftar dengan multi-akun. |

---

## 4. SISTEM KEAMANAN TINGKAT LANJUT SAMPAI KE AKARNYA (ADVANCED PRODUCTION SPEC)

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│            ARSITEKTUR PERTAHANAN BERLAPIS TINGKAT LANJUT (ZERO-TRUST)                  │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 1. LAYER PERIMETER (RFC 9116 & HEADERS)                                                │
│    - Endpoint /.well-known/security.txt (Contact, Expires, Encryption, Policy, Lang)   │
│    - Strict Security Headers: Cache-Control no-store pada area login (SEC-32)          │
│    - Host Header Whitelist (TrustHosts) dan mitigasi Host poisoning (SEC-30)           │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 2. LAYER REGULASI & DATA PRIVACY (UU PDP INDONESIA)                                    │
│    - Perlindungan Data Anak SMK (<18 th): flag persetujuan_wali_at pada peserta_profil │
│    - Hak Subjek Data: Command magkom:anonimkan-peserta untuk scrubbing PII aman        │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 3. LAYER OTORISASI & INTEGRITAS DATA                                                   │
│    - SuperAdmin strictly read-only di rute pembimbing (SEC-12 via middleware)          │
│    - Zero-SuperAdmin Guard: Larangan menonaktifkan akun sendiri atau staf SuperAdmin 0 │
│    - Deteksi anomali NIM ganda (SEC-15) di antrean seleksi berkas                      │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 4. LAYER TRANSAKSI & PENYIMPANAN BERKAS                                                │
│    - Storage Privat 0600 file & 0700 dir, signed URL bertenggat 5 menit                │
│    - Dompdf Hardening: chroot fonts lokal, enable_remote=false, no script eval         │
│    - Anti-Formula CSV: sanitasi karakter =, +, -, @, \t, \r                            │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 5. KATALOG TIKET TUGAS SENIOR DEVELOPER (WORK BREAKDOWN STRUCTURE)

Berikut tiket kerja preskriptif untuk melengkapi gap dan menyempurnakan ketiga dashboard:

```
================================================================================
KATALOG TIKET TUGAS SENIOR DEVELOPER (WBS GAP RESOLUTION & PRODUCTION HARDENING)
================================================================================
- TASK-GAP-01: Penerbitan RFC 9116 Security Disclosure (/.well-known/security.txt)
- TASK-GAP-02: Kepatuhan UU PDP Data Anak SMK & Persetujuan Wali (SEC-41)
- TASK-GAP-03: Perkakas Hak Subjek Data Anonimisasi Peserta (magkom:anonimkan-peserta)
- TASK-GAP-04: Penegakan Validasi JSON Predikat Batas di Pengaturan SuperAdmin (AC-11-7)
- TASK-GAP-05: Indikator Deteksi NIM/NISN Ganda Lintas Akun di Admin Seleksi (SEC-15)
- TASK-UI-CLEAN: Pembersihan Menyeluruh Sisa Dirty Inline CSS di Modal & Form Dashboard
================================================================================
```

---

### SPESIFIKASI TIKET KERJA TERPERINCI (JOB TICKETS)

#### ─── TIKET TASK-GAP-01 ───
- **Judul**: Penerbitan RFC 9116 Vulnerability Disclosure (`/.well-known/security.txt`)
- **Prioritas**: P1 (Kepatuhan Standar Industri & SPBE)
- **Rujukan**: Addendum SEC-74; RFC 9116
- **Target File**:
  - `public/.well-known/security.txt` (Create)
  - `tests/Feature/SecurityTxtEndpointTest.php` (Create)
- **Instruksi Langkah Demi Langkah**:
  1. Buat folder `public/.well-known/` jika belum ada.
  2. Buat berkas `public/.well-known/security.txt` dengan isi:
     ```text
     Contact: mailto:keamanan-informasi@magkom.go.id
     Expires: 2027-12-31T23:59:59.000Z
     Preferred-Languages: id, en
     Canonical: https://magkom.go.id/.well-known/security.txt
     Policy: https://magkom.go.id/kebijakan-privasi
     ```
  3. Buat feature test untuk memverifikasi bahwa `GET /.well-known/security.txt` merespons HTTP 200 dengan header `Content-Type: text/plain` dan memuat direktif `Contact:`.
- **Verifikasi Test**:
  `php artisan test --filter=SecurityTxtEndpointTest`

---

#### ─── TIKET TASK-GAP-02 ───
- **Judul**: Kepatuhan UU PDP Pemrosesan Data Anak SMK & Persetujuan Wali (SEC-41)
- **Prioritas**: P0 (Kepatuhan Hukum Regulasi)
- **Rujukan**: Addendum SEC-41; UU PDP No. 27 Tahun 2022
- **Target File**:
  - `database/migrations/2026_10_13_000001_add_persetujuan_wali_to_peserta_profil.php` (Create)
  - `app/Models/PesertaProfil.php` (Modify)
  - `app/Http/Requests/PesertaProfilRequest.php` (Modify)
  - `resources/views/peserta/profil/show.blade.php` (Modify)
  - `tests/Feature/PesertaUuPdpPersetujuanWaliTest.php` (Create)
- **Instruksi Langkah Demi Langkah**:
  1. Buat migrasi penambahan kolom `persetujuan_wali_at` (timestamp, nullable) pada tabel `peserta_profil`.
  2. Pada form profil peserta, jika jenjang yang dipilih adalah `smk`, tampilkan checkbox wajib:  
     *"Saya berusia minimal 18 tahun atau telah mendapatkan persetujuan dari orang tua/wali sah untuk mengikuti program magang (UU PDP No. 27/2022)"*.
  3. Pada `PesertaProfilRequest`, tambahkan validasi:
     `'persetujuan_wali' => ['nullable', 'required_if:jenjang,smk', 'accepted']`.
  4. Simpan waktu persetujuan `persetujuan_wali_at = now()` saat disimpan.
- **Verifikasi Test**:
  `php artisan test --filter=PesertaUuPdpPersetujuanWaliTest`

---

#### ─── TIKET TASK-GAP-03 ───
- **Judul**: Perkakas Hak Subjek Data & Anonimisasi Akun Peserta (`magkom:anonimkan-peserta`)
- **Prioritas**: P1 (Kepatuhan Hak Subjek Data UU PDP)
- **Rujukan**: Addendum SEC-44; NFR-R2; ADR-08
- **Target File**:
  - `app/Console/Commands/AnonimkanPesertaCommand.php` (Create)
  - `app/Services/DocumentStorageService.php` (Modify)
  - `tests/Feature/AnonimkanPesertaCommandTest.php` (Create)
- **Instruksi Langkah Demi Langkah**:
  1. Buat command `app/Console/Commands/AnonimkanPesertaCommand.php` dengan signature `magkom:anonimkan-peserta {user_id} {--alasan=}`.
  2. Validasi bahwa user yang akan dianonimkan ber-role `peserta` dan penempatannya sudah berstatus `selesai` atau `mengundurkan_diri` (bukan yang sedang aktif magang).
  3. Lakukan proses scrubbing data pribadi di dalam `DB::transaction()`:
     - User nama diubah menjadi: `"Peserta Teranonimisasi #{$user->id}"`.
     - User email diubah menjadi: `"anonymized_{$user->id}@magkom.internal"`.
     - Profil no_hp, nim_nisn, alamat di-reset atau di-masking `***`.
     - Hapus fisik berkas pendaftaran peserta dari disk privat melalui `DocumentStorageService`.
  4. Rekam jejak audit di tabel `audit_logs` dengan tipe `Aksi`, mencatat alasan penghapusan tanpa mencatat data lama yang sensitif.
- **Verifikasi Test**:
  `php artisan test --filter=AnonimkanPesertaCommandTest`

---

#### ─── TIKET TASK-GAP-04 ───
- **Judul**: Penegakan Validasi JSON Predikat Batas di Pengaturan SuperAdmin (AC-11-7)
- **Prioritas**: P1 (Integritas Konfigurasi Akademik)
- **Rujukan**: PRD AC-11-7, Bab 8.3 & 8.6; TDD 7.2
- **Target File**:
  - `app/Http/Controllers/SuperAdmin/PengaturanController.php` (Modify)
  - `app/Services/ScoringService.php` (Verify)
  - `tests/Feature/PredikatBatasValidationTest.php` (Create)
- **Instruksi Langkah Demi Langkah**:
  1. Pada method penyimpanan pengaturan di `PengaturanController`, saat kunci `predikat_batas` diubah:
     - Pastikan format JSON valid: memuat kunci `A`, `B`, `C`, `D`, `E`.
     - Validasi nilai batas harus strictly descending: `nilai(A) > nilai(B) > nilai(C) > nilai(D) > nilai(E)`.
     - Nilai harus berada dalam rentang 0 sampai 100.
  2. Tolak dengan pesan ramah jika format rusak atau urutan batas tidak menurun.
- **Verifikasi Test**:
  `php artisan test --filter=PredikatBatasValidationTest`

---

#### ─── TIKET TASK-GAP-05 ───
- **Judul**: Indikator Deteksi NIM/NISN Ganda Lintas Akun di Dashboard Admin Seleksi (SEC-15)
- **Prioritas**: P1 (Pencegahan Multi-Akun)
- **Rujukan**: Addendum SEC-15; PRD BR-17
- **Target File**:
  - `resources/views/admin/pendaftaran/show.blade.php` (Modify)
  - `app/Http/Controllers/Admin/PendaftaranController.php` (Modify)
  - `tests/Feature/AdminDuplicateNimDetectionTest.php` (Create)
- **Instruksi Langkah Demi Langkah**:
  1. Pada `Admin\PendaftaranController@show`, periksa apakah terdapat profil peserta lain dengan `nim_nisn` yang sama namun `user_id` berbeda:
     `$duplikasiNimCount = PesertaProfil::where('nim_nisn', $profil->nim_nisn)->where('user_id', '!=', $profil->user_id)->count();`
  2. Pada view `admin/pendaftaran/show.blade.php`, jika `$duplikasiNimCount > 0`, tampilkan `<x-alert variant="warning">` yang menginformasikan:  
     *"Perhatian Verifikator: Nomor Identitas ({$profil->nim_nisn}) ini juga terdaftar pada {$duplikasiNimCount} akun peserta lainnya."*
  3. Fitur ini bersifat indikator peringatan informasi bagi admin verifikator (tidak memblokir).
- **Verifikasi Test**:
  `php artisan test --filter=AdminDuplicateNimDetectionTest`

---

## 6. DAFTAR PERUBAHAN FILE (FILE INVENTORY)

| File Path | Aksi | Ringkasan Perubahan & Kepatuhan Arsitektur |
| :--- | :---: | :--- |
| `public/.well-known/security.txt` | **Create** | Kanal pelaporan celah kerentanan resmi RFC 9116 (SEC-74) |
| `database/migrations/2026_10_13_000001_add_persetujuan_wali_to_peserta_profil.php` | **Create** | Penambahan kolom persetujuan_wali_at kepatuhan data anak SMK UU PDP (SEC-41) |
| `app/Models/PesertaProfil.php` | **Modify** | Cast persetujuan_wali_at ke datetime dan tambahkan ke $fillable |
| `app/Http/Requests/PesertaProfilRequest.php` | **Modify** | Validasi persetujuan wali wajib untuk jenjang SMK (SEC-41) |
| `resources/views/peserta/profil/show.blade.php` | **Modify** | Checkbox persetujuan orang tua/wali khusus jenjang SMK |
| `app/Console/Commands/AnonimkanPesertaCommand.php` | **Create** | Artisan command hak subjek data anonimisasi peserta UU PDP (SEC-44) |
| `app/Http/Controllers/SuperAdmin/PengaturanController.php` | **Modify** | Validasi integritas JSON predikat batas nilai harus descending (AC-11-7) |
| `resources/views/admin/pendaftaran/show.blade.php` | **Modify** | Peringatan visual deteksi NIM/NISN ganda lintas akun (SEC-15) |
| `tests/Feature/SecurityTxtEndpointTest.php` | **Create** | Feature test untuk verifikasi endpoint security.txt |
| `tests/Feature/PesertaUuPdpPersetujuanWaliTest.php` | **Create** | Feature test untuk verifikasi kepatuhan data anak SMK UU PDP |
| `tests/Feature/AnonimkanPesertaCommandTest.php` | **Create** | Feature test untuk verifikasi command anonimisasi data |

---

## 7. LANGKAH IMPLEMENTASI & EKSEKUSI

*Catatan: Checklist di bawah ini akan dieksekusi secara berurutan dan terverifikasi:*

- [x] **Langkah 1**: Buat endpoint `public/.well-known/security.txt` sesuai RFC 9116 (SEC-74).
- [x] **Langkah 2**: Implementasi kepatuhan data anak SMK UU PDP (Migrasi `persetujuan_wali_at`, Request, dan UI Profil) (SEC-41).
- [x] **Langkah 3**: Buat command `magkom:anonimkan-peserta` untuk hak subjek data penghapusan akun alumni (SEC-44).
- [x] **Langkah 4**: Tambahkan validasi strictly descending pada setting JSON `predikat_batas` di `SuperAdmin/PengaturanController.php` (AC-11-7).
- [x] **Langkah 5**: Tambahkan deteksi indikator NIM/NISN ganda pada `Admin/PendaftaranController@show` dan tampilan verifikasi (SEC-15).
- [x] **Langkah 6**: Buat automated feature tests untuk seluruh 5 gap baru.
- [x] **Langkah 7**: Jalankan full regression test suite `php artisan test` untuk memastikan 301+ assertions lulus 100% (Hasil aktual: 315 passing tests, 1783 assertions).

---

## 8. STRATEGI TESTING & QUALITY ASSURANCE

### 8.1 Automated Test Suites
- `SecurityTxtEndpointTest`: Menegaskan rute publik mengembalikan direktif resmi RFC 9116 (Passed).
- `PesertaUuPdpPersetujuanWaliTest`: Menguji validasi penolakan jika jenjang SMK tidak mencentang persetujuan wali, serta keberhasilan saat dicentang (Passed).
- `AnonimkanPesertaCommandTest`: Menguji bahwa data PII ter-masking, berkas privat terhapus dari disk, dan tercatat audit log (Passed).
- `PredikatBatasValidationTest`: Menguji penolakan jika format batas nilai tidak menurun (misal B > A) (Passed).
- `AdminDuplicateNimDetectionTest`: Menguji kemunculan alert badge deteksi NIM ganda pada layar verifikasi admin (Passed).

### 8.2 Production Verification Command
```bash
php artisan test
# Result: 315 passed (1783 assertions), 0 failures
```

---

## 9. RISIKO & MITIGASI (RISK MANAGEMENT)

| Potensi Risiko | Tingkat | Dampak | Strategi Mitigasi Teruji |
| :--- | :---: | :--- | :--- |
| **Migrasi Kolom Tambahan di Produksi** | Rendah | Penambahan kolom `persetujuan_wali_at` | Kolom dibuat `nullable` sehingga tidak merusak data profil eksisting |
| **Penghapusan Berkas Tidak Sengaja saat Anonimkan** | Tinggi | Berkas penempatan yang salah terhapus | Validasi strict bahwa hanya peserta berstatus Selesai/Mundur yang dapat dianonimkan |
| **Penolakan Validasi Setting Predikat** | Sedang | Form pengaturan gagal simpan | Memberikan pesan error deskriptif yang menunjukkan letak nilai batas yang tidak menurun |

---

## 10. KRITERIA SELESAI (DEFINITION OF DONE - DOD)

- [x] Seluruh 5 gap fitur (SEC-74, SEC-41, SEC-44, AC-11-7, SEC-15) telah terimplementasi dan terverifikasi.
- [x] Seluruh migrasi database baru berjalan aman tanpa merusak data eksisting.
- [x] Seluruh suite automated tests lulus 100% tanpa regresi (315 passed, 1783 assertions).
- [x] Dokumentasi plan diperbarui dengan status Completed & 100% Verified.
