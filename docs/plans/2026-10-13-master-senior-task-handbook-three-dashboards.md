# MASTER WORK ORDER HANDBOOK: Blueprint & Panduan Task Senior Developer untuk Junior Developer & AI (Gemini 3.8 Flash)

- **Tanggal Dokumen**: 2026-10-13
- **Author**: Lead Principal Software Architect & Head of Security Engineering (Antigravity)
- **Target Pembaca**: Junior Developers, Mid Developers, dan Model AI Operasional (Gemini 3.8 Flash / Claude Haiku / GPT-4o-mini)
- **Tujuan Dokumen**: Menyajikan panduan kerja operasional berbasis tiket task (*Job Tickets*) yang sangat terperinci, preskriptif, bebas ambiguitas, dan sepenuhnya mematuhi **Standar Industri Laravel Modern (Enterprise Best Practices)** berdasarkan 7 dokumen acuan MagKom:
  1. [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (PRD v1.1)
  2. [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Technical Design Document)
  3. [`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Roadmap & Panduan Eksekusi)
  4. [`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) (Skema DB & Kamus Data)
  5. [`05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](file:///g:/MAGKOM/docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md) (RBAC, Hak Akses & Audit Trail)
  6. [`06-ADDENDUM-KEAMANAN-DAN-QA.md`](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md) (Addendum Keamanan SEC-01 s.d. SEC-40 & QA)
  7. [`07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Design System 30 Token Figma)

---

## BAGIAN I: AUDIT KESESUAIAN STANDAR INDUSTRI LARAVEL (ENTERPRISE AUDIT)

Sebelum junior developer / AI mengeksekusi kode, pahami **10 Pilar Standar Industri Laravel** yang membedakan proyek amatir dengan standar enterprise instansi pemerintahan:

```
┌────────────────────────────────────────────────────────────────────────┐
│            10 PILAR STANDAR INDUSTRI LARAVEL ENTERPRISE                │
├────────────────────────────────────────────────────────────────────────┤
│ 1. Skinny Controller + Service/Action Pattern (Single Responsibility)  │
│ 2. Strict Typing (declare(strict_types=1)) & Return Type Declarations  │
│ 3. PHP 8.1+ Backed Enums untuk Seluruh Status Data Domain (Zero Magic) │
│ 4. FormRequest Dedicated dengan Pesan Error & Authorize Kustom        │
│ 5. Concurrency Control: DB::transaction() + lockForUpdate() (BR-01)    │
│ 6. Defense-in-Depth Authorization: Policy + Middleware + FormRequest  │
│ 7. Query Optimization: Anti N+1 via Eager Loading & withCount()        │
│ 8. Sanitasi Menyeluruh: Wildcard LIKE (SEC-33) & Anti CSV Formula      │
│ 9. Design System Konsisten: Reusable Blade Components (Open Sans, 44px)│
│ 10. Test-Driven Verification: AAA Pattern & 100% Passing Gate (296+)   │
└────────────────────────────────────────────────────────────────────────┘
```

### Penjelasan Praktis untuk Junior & AI
1. **Dilarang Fat Controller**: Controller hanya bertugas menangani input HTTP, memanggil Service, dan mengembalikan HTTP Response/View. Jika ada logika bisnis lebih dari 15 baris, pindahkan ke Service.
2. **Dilarang Magic Strings**: Jangan pernah menulis `if ($pendaftaran->status === 'diterima')`. Selalu gunakan `if ($pendaftaran->status === StatusPendaftaranEnum::Diterima)`.
3. **Wajib Pesimistik Lock untuk Kuota**: Jangan pernah mengupdate kuota tanpa `DB::transaction()` dan `lockForUpdate()`. Dua user yang mendaftar bersamaan di detik yang sama bisa menyebabkan kuota bocor (*oversold/race condition*) jika tanpa lock!
4. **Wajib Policy untuk Anti-IDOR**: Selalu gunakan `$this->authorize('update', $model)` di controller dan FormRequest `authorize()`. Jangan berasumsi ID di URL selalu milik user yang login.
5. **SuperAdmin Dilarang Mengubah Tugas/Nilai**: SuperAdmin berstatus *strictly read-only* pada bimbingan mentee (`SEC-12, AC-08-5, AC-10-7`). Jika ada permintaan POST/PUT/DELETE dari SuperAdmin di rute pembimbing, sistem wajib menolak 403 Forbidden.

---

## BAGIAN II: KATALOG TIKET TUGAS (JOB TICKETS) TIGA PILAR DASHBOARD

Setiap tiket tugas di bawah ini adalah instruksi terisolasi (*self-contained work order*) yang dapat langsung dikerjakan oleh junior developer atau model AI seperti Gemini 3.8 Flash.

---

### PILAR 1: DASHBOARD ADMINISTRASI (ADMIN)

```
================================================================================
TIKET: TASK-ADM-01
MODUL: Pendaftaran & Antrean FIFO Calon Peserta
PRIORITAS: Tinggi (Core Workflow)
RUJUKAN: PRD AC-03, AC-04-1; TDD 5.1; SEC-33; BR-05
================================================================================
```
- **Deskripsi Tugas**:
  Penyelenggaraan antrean verifikasi berkas pendaftaran calon peserta dengan metode FIFO murni berdasarkan waktu pengajuan berkas, sanitasi wildcard pencarian, dan visualisasi SLA breached.
- **Target File**:
  - Controller: `app/Http/Controllers/Admin/PendaftaranController.php`
  - FormRequest: `app/Http/Requests/KeputusanPendaftaranRequest.php`
  - Service: `app/Services/PlacementService.php`
  - View List: `resources/views/admin/pendaftaran/index.blade.php`
  - View Detail: `resources/views/admin/pendaftaran/show.blade.php`
  - Test: `tests/Feature/AdminPendaftaranWorkflowTest.php`
- **Instruksi Teknis Step-by-Step**:
  1. **Sanitasi Pencarian (SEC-33)**: Pada method `index()`, pastikan input keyword `q` di-escape dari karakter wildcard SQL `\` -> `\\`, `%` -> `\%`, `_` -> `\_` sebelum diserahkan ke query `LIKE`.
  2. **Pengurutan FIFO (AC-04-1 & BR-05)**: Sorting default wajib menggunakan `orderByRaw('COALESCE(diajukan_at, created_at) ASC')->orderBy('id', 'asc')`.
  3. **Indikator SLA Breached**: Jika `status === StatusPendaftaranEnum::Diajukan` dan `now()->diffInWeekdays($pendaftaran->diajukan_at) > 3`, tampilkan badge peringatan amber/merah *"SLA Terlewat (>3 hari)"*.
  4. **Panel Verifikasi Berkas (AC-03)**: Pada `show.blade.php`, sajikan 4 berkas wajib (Surat Permohonan, Proposal, Daftar Nilai, Identitas) dengan tombol unduh ber-signed URL sementara (5 menit).
  5. **Validasi FormRequest**: Jika keputusan adalah `tolak` atau `perlu_perbaikan`, `catatan` wajib minimal 10 karakter.
- **Pantangan Keras (Don'ts)**:
  - ❌ DILARANG meloloskan pencarian `%keyword%` mentah tanpa sanitasi.
  - ❌ DILARANG mengubah status tanpa mencatat entri ke `audit_logs` (tipe `aksi`, tabel `pendaftaran`).
- **Verifikasi Pengujian**:
  `php artisan test tests/Feature/AdminPendaftaranWorkflowTest.php`

---

```
================================================================================
TIKET: TASK-ADM-02
MODUL: Manajemen Kuota Atomik & Otomatisasi Penutupan Waiting List
PRIORITAS: Kritis (Concurrency & Business Rule)
RUJUKAN: PRD AC-04-4, AC-04-7; TDD 5.3; BR-01, BR-05
================================================================================
```
- **Deskripsi Tugas**:
  Menjamin alokasi kuota jurusan bersifat atomik dan bebas dari *race condition*, serta menutup antrean Waiting List secara otomatis saat tanggal bulan magang telah dimulai.
- **Target File**:
  - Controller: `app/Http/Controllers/Admin/WaitingListController.php`
  - Controller: `app/Http/Controllers/Admin/KuotaController.php`
  - Service: `app/Services/PlacementService.php`
  - Service: `app/Services/QuotaAllocationService.php`
  - Test: `tests/Feature/AdminWaitingListTest.php`
- **Instruksi Teknis Step-by-Step**:
  1. **Aturan Waiting List (AC-04-4)**: Calon peserta hanya boleh dimasukkan ke Waiting List jika kuota jurusan untuk bulan magang yang dipilih telah **100% penuh** (`terisi >= jumlah`). Jika kuota masih ada, tolak keputusan WL.
  2. **Penutupan Otomatis (AC-04-7)**: Buat method di `PlacementService::tutupWaitingListKadaluarsa(BulanMagang $bulan)`:
     ```php
     public function tutupWaitingListKadaluarsa(BulanMagang $bulan): int
     {
         if (now()->startOfDay()->lt($bulan->tanggal_mulai->startOfDay())) {
             return 0;
         }
         return DB::transaction(function () use ($bulan) {
             $pendaftaranList = Pendaftaran::where('bulan_magang_id', $bulan->id)
                 ->where('status', StatusPendaftaranEnum::WaitingList)
                 ->lockForUpdate()
                 ->get();

             foreach ($pendaftaranList as $pendaftaran) {
                 $pendaftaran->update([
                     'status' => StatusPendaftaranEnum::Ditolak,
                     'catatan' => 'Periode magang telah dimulai; antrean waiting list ditutup otomatis oleh sistem (AC-04-7).',
                     'diputuskan_at' => now(),
                 ]);
                 $this->auditService->catat(TipeAuditEnum::Aksi, 'pendaftaran', $pendaftaran->id, 'waiting_list_ditutup_otomatis');
             }
             return $pendaftaranList->count();
         });
     }
     ```
  3. **UI Waiting List**: Sediakan tombol *"Tutup Waiting List Kadaluarsa"* jika bulan magang terkait sudah berjalan, lengkap dengan modal konfirmasi dan informasi jumlah antrean yang akan ditutup.
- **Verifikasi Pengujian**:
  `php artisan test tests/Feature/AdminWaitingListTest.php`

---

```
================================================================================
TIKET: TASK-ADM-03
MODUL: Siklus Hidup Penempatan (Pembatalan vs Pengunduran Diri)
PRIORITAS: Kritis (Audit & Kuota Reallocation)
RUJUKAN: PRD AC-05-4, AC-05-5; BR-07, BR-13; SEC-11
================================================================================
```
- **Deskripsi Tugas**:
  Mengatur siklus hidup penempatan mentee: pembatalan sebelum magang dimulai (kuota dikembalikan) versus pengunduran diri di tengah periode (kuota hangus, akun peserta dinonaktifkan).
- **Target File**:
  - Controller: `app/Http/Controllers/Admin/PenempatanController.php`
  - Service: `app/Services/PlacementService.php`
  - View Detail: `resources/views/admin/penempatan/show.blade.php`
  - Test: `tests/Feature/AdminPenempatanLifecycleTest.php`
- **Instruksi Teknis Step-by-Step**:
  1. **Batas Beban Pembimbing (BR-13)**: Validasi bahwa pembimbing yang dipilih tidak memiliki lebih dari 5 mentee aktif (`mentee_aktif_count < 5`).
  2. **Logika Pembatalan Sebelum Periode (AC-05-4 & BR-07)**:
     - Jika `now() < $bulanMagang->tanggal_mulai`:
       - Status penempatan = `StatusPenempatanEnum::Dibatalkan`.
       - Kuota jurusan dikurangi: `$kuota->decrement('terisi')`.
       - Pendaftaran terkait dialihkan ke `StatusPendaftaranEnum::Dibatalkan`.
       - Berikan flash alert: *"Penempatan dibatalkan dan 1 kuota jurusan telah dikembalikan."*
  3. **Logika Pengunduran Diri di Tengah Periode (AC-05-5 & BR-07)**:
     - Jika `now() >= $bulanMagang->tanggal_mulai`:
       - Status penempatan = `StatusPenempatanEnum::MengundurkanDiri`.
       - Kuota jurusan **TIDAK DIKEMBALIKAN** (`$kuota->terisi` tetap).
       - Akun peserta langsung dinonaktifkan: `$peserta->update(['status_aktif' => false])`.
       - Sesi aktif peserta langsung diterminasi (`SEC-03`).
- **Verifikasi Pengujian**:
  `php artisan test tests/Feature/AdminPenempatanLifecycleTest.php`

---

### PILAR 2: DASHBOARD PEMBIMBING LAPANGAN

```
================================================================================
TIKET: TASK-PMB-01
MODUL: Manajemen Tugas, Template Penugasan & Guard Status Selesai
PRIORITAS: Tinggi (Mentoring Core)
RUJUKAN: PRD AC-08-1 s.d. AC-08-7; BR-16; SEC-11; SEC-12
================================================================================
```
- **Deskripsi Tugas**:
  Menyediakan CRUD tugas bimbingan untuk mentee aktif, pemilihan template penugasan, proteksi IDOR kepemilikan mentee, serta penguncian permanen tugas yang sudah selesai (`AC-08-7`).
- **Target File**:
  - Controller: `app/Http/Controllers/Pembimbing/TugasController.php`
  - FormRequest: `app/Http/Requests/TugasRequest.php`
  - Policy: `app/Policies/TugasPolicy.php`
  - View Create: `resources/views/pembimbing/tugas/create.blade.php`
  - View Edit: `resources/views/pembimbing/tugas/edit.blade.php`
  - View Show: `resources/views/pembimbing/tugas/show.blade.php`
  - Test: `tests/Feature/PembimbingIdorProtectionTest.php`
- **Instruksi Teknis Step-by-Step**:
  1. **Proteksi IDOR FormRequest (SEC-11)**:
     ```php
     // app/Http/Requests/TugasRequest.php
     public function authorize(): bool
     {
         $user = $this->user();
         if (! $user || ! $user->isPembimbing()) {
             return false;
         }
         $penempatanId = $this->input('penempatan_id') ?? $this->route('tugas')?->penempatan_id;
         return Penempatan::where('id', $penempatanId)
             ->where('pembimbing_id', $user->id)
             ->where('status', StatusPenempatanEnum::Aktif)
             ->exists();
     }
     ```
  2. **Validasi Batas Waktu**: `deadline` minimal hari ini dan tidak boleh melampaui tanggal selesai penempatan mentee terkait.
  3. **Guard Tugas Selesai (AC-08-7)**:
     - Di method `edit()` dan `update()`:
       ```php
       if ($tugas->status === StatusTugasEnum::Selesai) {
           return redirect()->route('pembimbing.tugas.show', $tugas)
               ->with('error', 'Tugas yang sudah selesai tidak dapat diedit kembali (AC-08-7).');
       }
       ```
  4. **SuperAdmin Read-Only (AC-08-5 & SEC-12)**:
     Di `TugasPolicy`, blokir mutasi `create`, `update`, `delete` jika `$user->isSuperAdmin()` bernilai `true`.
- **Verifikasi Pengujian**:
  `php artisan test tests/Feature/PembimbingIdorProtectionTest.php`

---

```
================================================================================
TIKET: TASK-PMB-02
MODUL: Validasi & Reviu Logbook SLA 3 Hari serta Bulk Approval
PRIORITAS: Tinggi (Daily Activity Review)
RUJUKAN: PRD AC-09-1 s.d. AC-09-4; BR-09; SEC-11
================================================================================
```
- **Deskripsi Tugas**:
  Penyelenggaraan reviu logbook harian mentee, penegakan batas mundur pengisian 3 hari kalender (BR-09), penanda visual SLA 3 hari kerja pembimbing, dan fitur *Bulk Approval*.
- **Target File**:
  - Controller: `app/Http/Controllers/Pembimbing/LogbookReviewController.php`
  - FormRequest: `app/Http/Requests/ReviewLogbookRequest.php`
  - Policy: `app/Policies/LogbookPolicy.php`
  - View Index: `resources/views/pembimbing/logbook/index.blade.php`
  - Test: `tests/Feature/TugasLogbookTest.php`
- **Instruksi Teknis Step-by-Step**:
  1. **Batas Mundur Logbook Peserta (BR-09)**: Sistem menolak logbook peserta jika tanggal aktivitas lebih dari 3 hari ke belakang dari tanggal pengisian.
  2. **Indikator SLA Pembimbing (AC-09-2)**: Pada tabel daftar review logbook, jika entri berstatus `menunggu` dan sudah berumur lebih dari 3 hari kerja, tampilkan badge peringatan berkedip/amber: *"Perlu Segera Ditinjau (>3 hari)"*.
  3. **Bulk Approval**: Sediakan endpoint `POST /pembimbing/logbook/bulk-approve` dengan payload `logbook_ids: array`:
     - Verifikasi bahwa seluruh ID logbook yang dikirimkan berelasi dengan mentee dari pembimbing yang login.
     - Ubah status menjadi `StatusLogbookEnum::Disetujui`.
     - Catat ke `audit_logs` secara batch.
  4. **Catatan Penolakan / Revisi**: Jika status diubah menjadi `ditolak` atau `perlu_revisi`, kolom `catatan_pembimbing` wajib diisi minimal 5 karakter.
- **Verifikasi Pengujian**:
  `php artisan test tests/Feature/TugasLogbookTest.php`

---

```
================================================================================
TIKET: TASK-PMB-03
MODUL: Penilaian Rubrik 100%, Riwayat Revisi & Lembar Nilai PDF
PRIORITAS: Kritis (Graduation & Certification)
RUJUKAN: PRD AC-10-1 s.d. AC-10-7, AC-12; BR-10, BR-11; SEC-12
================================================================================
```
- **Deskripsi Tugas**:
  Perhitungan nilai akhir mentee berbasis rubrik penilaian berbobot 100%, konversi predikat resmi (A, B, C, D, E), pencatatan riwayat revisi nilai, penguncian SuperAdmin read-only, dan ekspor lembar nilai PDF resmi on-the-fly.
- **Target File**:
  - Controller: `app/Http/Controllers/Pembimbing/PenilaianController.php`
  - Service: `app/Services/ScoringService.php`
  - FormRequest: `app/Http/Requests/PenilaianRequest.php`
  - View: `resources/views/pembimbing/penilaian/create.blade.php`
  - View PDF: `resources/views/pdf/lembar-nilai.blade.php`
  - Test: `tests/Feature/ScoringAndCompletionTest.php`
- **Instruksi Teknis Step-by-Step**:
  1. **Validasi Bobot Rubrik 100%**: Di `ScoringService`, jumlahkan bobot rubrik yang aktif. Jika total != 100, lempar exception dan tolak finalisasi.
  2. **Konversi Predikat Bulat**:
     - Skor $\ge 85$: Predikat `A` (Sangat Baik)
     - Skor $75 - 84.99$: Predikat `B` (Baik)
     - Skor $65 - 74.99$: Predikat `C` (Cukup)
     - Skor $50 - 64.99$: Predikat `D` (Kurang)
     - Skor $< 50$: Predikat `E` (Gagal)
  3. **Syarat Penyelesaian Penempatan (BR-10)**: Penempatan mentee TIDAK BOLEH diubah menjadi `StatusPenempatanEnum::Selesai` jika nilai final belum diterbitkan.
  4. **Riwayat Revisi Nilai (BR-11)**: Jika nilai yang sudah final direvisi kembali oleh pembimbing, field `alasan_revisi` wajib diisi minimal 10 karakter dan rekam skor lama ke tabel `riwayat_penilaian`.
  5. **SuperAdmin Read-Only (AC-10-7)**: SuperAdmin dilarang menginput atau merevisi nilai (tolak 403 Forbidden).
- **Verifikasi Pengujian**:
  `php artisan test tests/Feature/ScoringAndCompletionTest.php`

---

### PILAR 3: DASHBOARD SUPERADMIN

```
================================================================================
TIKET: TASK-SUP-01
MODUL: Akun Staf, Sandi CSPRNG 16 Karakter & Guard SuperAdmin Terakhir
PRIORITAS: Kritis (Account Governance & SEC-16)
RUJUKAN: PRD AC-16-1 s.d. AC-16-4; BR-15; SEC-02; SEC-16
================================================================================
```
- **Deskripsi Tugas**:
  Penyelenggaraan tata kelola akun staf Kominfo: pembuatan kata sandi acak CSPRNG 16 karakter tanpa input teks manual, flash session satu kali, kewajiban ganti sandi saat login awal, dan proteksi penghapusan SuperAdmin terakhir.
- **Target File**:
  - Controller: `app/Http/Controllers/SuperAdmin/StafController.php`
  - View List: `resources/views/superadmin/staf/index.blade.php`
  - Test: `tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`
- **Instruksi Teknis Step-by-Step**:
  1. **Pembuatan Sandi Acak (SEC-02, BR-15)**:
     ```php
     $plainPassword = Str::password(16, true, true, true, false);
     $user = User::create([
         'nama' => $request->validated('nama'),
         'email' => $request->validated('email'),
         'password' => Hash::make($plainPassword),
         'role' => $request->validated('role'),
         'status_aktif' => true,
         'must_change_password' => true,
     ]);
     session()->flash('password_baru', $plainPassword);
     session()->flash('email_staf_baru', $user->email);
     ```
  2. **Modal Flash Kredensial**: Pada halaman daftar staf, tampilkan banner/modal peringatan khusus yang memuat kata sandi baru sekali tampil dengan tombol salin (copy-to-clipboard).
  3. **Penjaga SuperAdmin Terakhir (SEC-16)**:
     ```php
     if ($staf->isSuperAdmin()) {
         $activeSuperAdminCount = User::where('role', RoleEnum::SuperAdmin)
             ->where('status_aktif', true)
             ->count();
         if ($activeSuperAdminCount <= 1) {
             return back()->with('error', 'Operasi ditolak (SEC-16): Sistem harus memiliki minimal satu SuperAdmin aktif.');
         }
     }
     ```
- **Verifikasi Pengujian**:
  `php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`

---

```
================================================================================
TIKET: TASK-SUP-02
MODUL: Konfigurasi Sistem Dinamis, Batas Berkas 10MB & Auto Clear Cache
PRIORITAS: Tinggi (System Hardening)
RUJUKAN: PRD AC-17-1 s.d. AC-17-3; SEC-21
================================================================================
```
- **Deskripsi Tugas**:
  Pengaturan parameter dinamis aplikasi MagKom (ukuran berkas, SLA, masa tenggang), validasi batas keamanan maksimal 10 MB, dan invalidasi cache otomatis setiap kali data diperbarui.
- **Target File**:
  - Controller: `app/Http/Controllers/SuperAdmin/PengaturanController.php`
  - Model: `app/Models/Pengaturan.php`
  - View: `resources/views/superadmin/pengaturan/index.blade.php`
  - Test: `tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`
- **Instruksi Teknis Step-by-Step**:
  1. **Validasi Batas Berkas (SEC-21)**: Parameter `berkas_maks_mb` wajib diapit aturan validasi: `['required', 'integer', 'min:1', 'max:10']`. Tolak nilai > 10 MB.
  2. **Validasi SLA**: Parameter `sla_verifikasi_hari` validasi `['required', 'integer', 'min:1', 'max:30']`.
  3. **Invalidasi Cache Otomatis**: Setiap update pada `PengaturanController::update()`, panggil method:
     ```php
     Pengaturan::clearCache();
     ```
     agar seluruh request aplikasi langsung mendapatkan konfigurasi terkini tanpa restart worker.
- **Verifikasi Pengujian**:
  `php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`

---

```
================================================================================
TIKET: TASK-SUP-03
MODUL: Arsip Peserta Permanen Multi-Tab 5 Panel
PRIORITAS: Sedang (Historical Forensics)
RUJUKAN: PRD AC-14, AC-18; TDD 9.4; SEC-36
================================================================================
```
- **Deskripsi Tugas**:
  Penyajian rekam jejak lengkap peserta magang yang telah menyelesaikan program atau mengundurkan diri dalam 5 tab read-only dengan eager loading penuh bebas N+1 query.
- **Target File**:
  - Controller: `app/Http/Controllers/SuperAdmin/ArsipPesertaController.php`
  - View Show: `resources/views/superadmin/arsip/show.blade.php`
  - Test: `tests/Feature/SuperAdminPortalVisualTest.php`
- **Instruksi Teknis Step-by-Step**:
  1. **Eager Loading Relasi Komprehensif**:
     ```php
     $pendaftaran->load([
         'user.profil.jurusan',
         'bulanMagang',
         'kuota.jurusan',
         'dokumen',
         'penempatan.pembimbing',
         'penempatan.bidang',
         'penempatan.tugas.progresLogs',
         'penempatan.logbooks',
         'penempatan.penilaians.rubrik',
     ]);
     ```
  2. **Penyajian 5 Tab Permanen**:
     - Tab 1: **Biodata & Dokumen** (Surat Permohonan, Proposal, KTP, Nilai)
     - Tab 2: **Penempatan & Bimbingan** (Bidang, Pembimbing, Periode, SK)
     - Tab 3: **Penugasan** (Daftar tugas, lampiran, progres dari mentee)
     - Tab 4: **Logbook Harian** (Catatan harian & umpan balik pembimbing)
     - Tab 5: **Penilaian Resmi** (Rincian rubrik, skor final, predikat, lembar nilai PDF)
  3. **Pencatatan Audit Akses**: Catat aksi pembukaan detail arsip ke `audit_logs` dengan aksi `akses_data_arsip`.
- **Verifikasi Pengujian**:
  `php artisan test tests/Feature/SuperAdminPortalVisualTest.php`

---

### PILAR 4: CROSS-CUTTING STANDARDS (KEAMANAN & UX KOMPONEN)

```
================================================================================
TIKET: TASK-XCT-01
MODUL: Standardisasi Komponen Blade Reusable & Design System Token
PRIORITAS: Tinggi (Aesthetic Excellence & Accessibility)
RUJUKAN: 07-DESIGN-SYSTEM-MAGKOM; WCAG AA (Touch 44px)
================================================================================
```
- **Deskripsi Tugas**:
  Standardisasi komponen Blade reusable `<x-badge>`, `<x-alert>`, `<x-stat-card>`, dan `<x-empty-state>` agar seluruh tampilan dashboard mengunci 30 token resmi Figma dan ramah aksesibilitas.
- **Target File**:
  - Components: `resources/views/components/*.blade.php`
  - Test: `tests/Feature/IndustrialBladeComponentsTest.php`
- **Instruksi Teknis Step-by-Step**:
  1. Pastikan seluruh badge memetakan status domain ke token warna resmi:
     - Emerald/Green 50/700: `diterima, aktif, selesai, disetujui, lulus`
     - Amber 50/700: `menunggu, diajukan, waiting_list, perlu_perbaikan, revisi`
     - Rose 50/700: `ditolak, dibatalkan, terlambat, mengundurkan_diri`
     - Blue 50/700: `bimbingan, info, penempatan`
  2. Seluruh tombol interaktif, input form, dan link pagination wajib memiliki target sentuh minimal **44 × 44 piksel**.
  3. Tipografi wajib Open Sans (`font-sans`).
- **Verifikasi Pengujian**:
  `php artisan test tests/Feature/IndustrialBladeComponentsTest.php`

---

```
================================================================================
TIKET: TASK-XCT-02
MODUL: Custom Error Pages Tanpa Jejak Teknis (SEC-35)
PRIORITAS: Kritis (Information Leak Prevention)
RUJUKAN: PRD SEC-35; OWASP A05:2021 Security Misconfiguration
================================================================================
```
- **Deskripsi Tugas**:
  Penyediaan halaman error kustom untuk HTTP 403, 404, 419, 429, dan 500 yang ramah pengguna, berdesain elegan, dan bebas dari kebocoran jejak teknis, path server, maupun query SQL.
- **Target File**:
  - Layout: `resources/views/errors/layout.blade.php`
  - Error Views: `resources/views/errors/{403, 404, 419, 429, 500}.blade.php`
  - Test: `tests/Feature/CustomErrorPagesTest.php`
- **Instruksi Teknis Step-by-Step**:
  1. Buat layout error kustom yang responsif dengan ilustrasi/ikon semantik Lucide.
  2. Sediakan tombol pintar *"Kembali ke Dashboard"* yang mengarahkan user sesuai perannya (`auth()->user()->role`).
  3. Dilarang menampilkan variable dump, SQL error message, atau exception trace bahkan saat mode debug terjadi error 500 bagi pengguna non-developer.
- **Verifikasi Pengujian**:
  `php artisan test tests/Feature/CustomErrorPagesTest.php`

---

## BAGIAN III: PIRAMIDA PENGUJIAN & QUALITY GATE MATRIX

Sebelum pekerjaan diserahkan (*merge/pull request*), seluruh pengujian wajib dijalankan berurutan:

```
[Level 1: Unit & Component Tests]
  php artisan test tests/Feature/IndustrialBladeComponentsTest.php

[Level 2: IDOR & Policy Authorization Tests]
  php artisan test tests/Feature/PembimbingIdorProtectionTest.php

[Level 3: Concurrency & Lifecycle Tests]
  php artisan test tests/Feature/AdminPenempatanLifecycleTest.php

[Level 4: Error Handling & Security Tests]
  php artisan test tests/Feature/CustomErrorPagesTest.php

[Level 5: Executive Dashboard & Audit Log Tests]
  php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php

[Level 6: Full Regression Master Gate (296+ Tests)]
  php artisan test
```

---

## BAGIAN IV: KRITERIA SELESAI (DEFINITION OF DONE)

Sebuah task dinyatakan **SELESAI & LULUS AUDIT SENIOR ARCHITECT** jika:
- [x] Mematuhi aturan *Skinny Controller* (logika kompleks diisolasi ke Service/Action).
- [x] Menggunakan PHP 8.1+ Enums untuk semua status data.
- [x] Menerapkan transaksi atomik `DB::transaction()` dan `lockForUpdate()` pada alokasi sumber daya kuota.
- [x] Seluruh endpoint mutasi terlindungi oleh FormRequest dan Policy dari serangan IDOR.
- [x] SuperAdmin berstatus *strictly read-only* pada fungsi operasional pembimbing.
- [x] Menggunakan Blade reusable components yang patuh pada 30 token resmi Figma (Open Sans, touch target 44px).
- [x] Tidak ada kebocoran stack trace atau SQL error pada respons error.
- [x] Seluruh unit dan feature tests terkait berstatus **PASS (Hijau)**.
- [x] Full regression test suite (`php artisan test`) menghasilkan **296 tests lulus 100% (1675 assertions)** tanpa kegagalan maupun error.
- [x] Kode bersih dari helper debugging (`dd()`, `dump()`, `console.log()`) dan kredensial rahasia.
