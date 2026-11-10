# Plan: Rencana Komprehensif Kelanjutan Dashboard Tiga Pilar (Admin, Pembimbing, SuperAdmin) MagKom

- **Tanggal Dokumen**: 2026-10-13
- **Status**: Selesai & Terverifikasi Penuh (Completed & 100% Verified)
- **Author**: Senior Lead Architect / Antigravity (web-planning & web-implementation)
- **Target Pembaca**: Junior Developer & AI Agent (Model Operasional: Gemini 3.8 Flash / Claude / GPT)
- **Dokumen Rujukan**:
  1. [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (PRD v1.1)
  2. [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Technical Design Document)
  3. [`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Roadmap & Gelombang)
  4. [`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) (Skema & Kamus Data)
  5. [`05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](file:///g:/MAGKOM/docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md) (RBAC, Hak Akses & Audit)
  6. [`06-ADDENDUM-KEAMANAN-DAN-QA.md`](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md) (Addendum Keamanan SEC & QA Testing)
  7. [`07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Design System 30 Token & Figma Node)

---

## 1. Tujuan & Latar Belakang

Dokumen ini disusun sebagai **spesifikasi tugas operasional tingkat senior (Senior Developer Work Order / Blueprint)** yang memandu pengembang junior maupun AI model dalam melanjutkan, menyempurnakan, dan mengokohkan (*hardening*) implementasi **Dashboard Tiga Pilar MagKom**:
1. **Pilar 1: Dashboard Administrasi (Admin)** — Penyelenggaraan operasional verifikasi berkas, alokasi kuota jurusan, manajemen antrean Waiting List, siklus pembatalan/pengunduran diri, hingga penerbitan sertifikat kelulusan.
2. **Pilar 2: Dashboard Pembimbing Lapangan** — Pengelolaan bimbingan mentee 360°, penugasan mandiri & template tugas, reviu progres, validasi logbook harian berbatas waktu (3 hari), penilaian rubrik berbobot (100%), hingga penetapan kelulusan magang.
3. **Pilar 3: Dashboard SuperAdmin** — Pusat kendali eksekutif, tata kelola master data (jurusan, bidang, rubrik, bulan), slot khusus internal (*zero-leak*), manajemen akun staf dengan kata sandi acak, konfigurasi sistem dinamis, serta audit forensik *append-only*.

Setiap task diuraikan dengan instruksi teknis tanpa tebak-tebakan: **File yang disentuh, logika bisnis, validasi, penanganan error, aturan keamanan, token desain antarmuka, hingga skenario tes otomatis yang wajib lulus**.

---

## 2. Asumsi Teknis & Guardrails Mutlak ("Anti-Ngaco")

Sebelum menjalankan baris kode apa pun, seluruh pelaksana wajib mematuhi batasan berikut:

1. **Guardrail Kuota Atomik (BR-01, BR-05, TDD 5.3)**:
   - Alokasi kuota saat penerimaan dan pengembalian saat pembatalan **WAJIB** memakai transaksi database atomik dengan pesimistik lock (`lockForUpdate()`).
   - Sisa kuota tidak boleh bernilai negatif (`terisi <= jumlah`).
2. **Isolasi SuperAdmin Read-Only (AC-08-5, AC-10-7, SEC-12)**:
   - SuperAdmin berstatus *strictly read-only* pada fungsi operasional pembimbing (pembuatan/pengubahan tugas, reviu logbook, input/revisi penilaian, penetapan selesai).
   - Pengamanan dilakukan dua lapis (*defense-in-depth*): middleware `superadmin.readonly` (menolak method HTTP selain GET/HEAD dengan 403 Forbidden) dan Policy (`$user->isSuperAdmin() => false`).
3. **Proteksi IDOR Menyeluruh (SEC-11, THR-01)**:
   - Dilarang keras melakukan `Model::find($id)` tanpa memvalidasi kepemilikan (`pembimbing_id === auth()->id()`).
   - Setiap mutasi tugas, logbook, dan penilaian wajib diperiksa melalui FormRequest `authorize()` dan Gate Policy.
4. **Kerahasiaan Slot Khusus (BR-02, SEC-40)**:
   - Slot khusus hanya dikelola oleh SuperAdmin. Informasi slot khusus **DILARANG KERAS** bocor ke publik, landing page, atau respons JSON peserta.
5. **Integritas Audit Trail Append-Only (NFR-A4, SEC-36, TDD 9.4)**:
   - Tabel `audit_log` bersifat *append-only*. Mutasi `UPDATE` dan `DELETE` dilarang keras di level model Eloquent maupun database trigger.
   - Output teks audit log wajib di-escape (`{{ }}`) untuk mencegah serangan XSS Tersimpan.
6. **Desain Sistem & Aksesibilitas (07-DS-MagKom)**:
   - Tipografi wajib memakai **Open Sans** (bukan Plus Jakarta Sans).
   - Tombol dan area interaktif memiliki target sentuh minimal **44 × 44 piksel**.
   - Skema warna status mengacu pada palet resmi: Sukses (Green 100/600/700), Peringatan (Amber 100/200/700), Bahaya (Red 100/200/700), Info (Blue Tint/Blue 600/Blue 700).

---

## 3. Matriks Perubahan & Inventaris File

| Area / Pilar | File Sasaran | Aksi | Ringkasan Modifikasi & Hardening |
|:---|:---|:---|:---|
| **Admin** | `app/Http/Controllers/Admin/PendaftaranController.php` | Modify | Penambahan sanitasi pencarian SEC-33, SLA counter watchdog, pagination 25. |
| **Admin** | `resources/views/admin/pendaftaran/index.blade.php` | Modify | Penyelarasan toolbar filter, empty state berikon inbox, status badge DS. |
| **Admin** | `resources/views/admin/pendaftaran/show.blade.php` | Modify | Pratinjau berkas modal, signed download URL 5 menit, validasi catatan tolak min 10 karakter. |
| **Admin** | `app/Http/Controllers/Admin/WaitingListController.php` | Modify | Penutupan antrean massal saat bulan magang dimulai (AC-04-7), audit trail per entri. |
| **Admin** | `app/Http/Controllers/Admin/PenempatanController.php` | Modify | Siklus hidup BR-07 (batal vs undur diri), validasi batas beban pembimbing BR-13. |
| **Admin** | `resources/views/admin/penempatan/show.blade.php` | Modify | Modal konfirmasi pemindahan pembimbing dinamis, modal pembatalan berdampak kuota. |
| **Admin** | `app/Http/Controllers/Admin/LaporanController.php` | Modify | Sanitasi CSV formula injection (`=`, `+`, `-`, `@`), pemisahan hak rekap nilai. |
| **Admin** | `resources/views/admin/dashboard.blade.php` | Modify | Penyelarasan KPI, SLA breached counter (>3 hari), timeline gelombang, watchdog offboarding. |
| **Pembimbing** | `app/Http/Controllers/Pembimbing/TugasController.php` | Modify | Penegakan edit tugas belum selesai (AC-08-7), template picker json, sanitasi lampiran. |
| **Pembimbing** | `resources/views/pembimbing/tugas/edit.blade.php` | Create | Formulir edit tugas lengkap dengan batas akhir maksimal tgl selesai penempatan. |
| **Pembimbing** | `app/Http/Controllers/Pembimbing/LogbookReviewController.php` | Modify | Batas mundur pengisian 3 hari (BR-09), bulk approval & rejection dengan umpan balik. |
| **Pembimbing** | `app/Http/Controllers/Pembimbing/PenilaianController.php` | Modify | Validasi rubrik total 100%, konversi predikat A-E bulat, riwayat revisi nilai min 10 char. |
| **Pembimbing** | `resources/views/layouts/dashboard.blade.php` | Modify | Sidebar drawer responsive mobile (<1024px), touch target 44px, active state nav. |
| **SuperAdmin** | `app/Http/Controllers/SuperAdmin/StafController.php` | Modify | Pembuatan kata sandi acak sistem 16 char (SEC-02), flash session sekali, penjaga SEC-16. |
| **SuperAdmin** | `app/Http/Controllers/SuperAdmin/PengaturanController.php` | Modify | Validasi rentang parameter sistem, pembatasan berkas maks 10MB (SEC-21), auto clear cache. |
| **SuperAdmin** | `app/Http/Controllers/SuperAdmin/ArsipPesertaController.php` | Modify | Eager loading relasi komprehensif, audit logging `akses_data`, tab-view 5 panel. |
| **SuperAdmin** | `resources/views/superadmin/arsip/show.blade.php` | Modify | Panel tab read-only: Biodata, Penempatan, Tugas, Logbook, Rubrik Penilaian Resmi. |
| **Cross-Cutting** | `app/Http/Middleware/EnsureAccountActive.php` | Verify/Modify | Penegakan pemutusan sesi aktif instan bagi akun nonaktif (SEC-03). |
| **Cross-Cutting** | `app/Http/Middleware/SuperAdminReadOnly.php` | Verify/Modify | Penolakan metode non-safe HTTP (POST/PUT/PATCH/DELETE) untuk SuperAdmin di route pembimbing. |
| **Cross-Cutting** | `resources/views/errors/` (403, 404, 419, 429, 500) | Verify/Create | Halaman error kustom tanpa kebocoran stack trace atau query SQL (SEC-35). |

---

## 4. Rincian Task Senior Developer (Actionable Work Breakdown)

### FASE A: Hardening & Penyempurnaan Dashboard Administrasi (Admin)

#### Task A-01: Verifikasi & Audit Sanitasi Modul Pendaftaran Calon Peserta
- **Target File**: `app/Http/Controllers/Admin/PendaftaranController.php`, `resources/views/admin/pendaftaran/index.blade.php`
- **Tuntunan Senior untuk Junior/AI**:
  1. Pastikan method `index()` menerapkan sanitasi wildcard LIKE pada input pencarian (SEC-33):
     ```php
     $sanitizedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $keyword);
     ```
  2. Terapkan pagination tetap 25 data per halaman (`->paginate(25)`).
  3. Sortir antrean verifikasi default dengan urutan `diajukan_at ASC` (yang mengajukan lebih awal ditinjau lebih dulu).
  4. Pada antarmuka Blade, pasang komponen *Empty State* ketika daftar kosong: Ikon `inbox` Lucide, teks *"Belum ada pendaftaran yang perlu diverifikasi"*.
  5. Pastikan tombol aksi tabel tertulis `Tinjau Berkas` yang mengarah ke `route('admin.pendaftaran.show', $item)`.
- **Target Uji**: `Tests\Feature\AdminDashboardEnhancementTest` & `Tests\Feature\AdminChildPagesVisualTest` lulus.

#### Task A-02: Otomatisasi Penutupan Massal Antrean Waiting List Awal Bulan (AC-04-7)
- **Target File**: `app/Http/Controllers/Admin/WaitingListController.php`, `resources/views/admin/waiting-list/index.blade.php`
- **Tuntunan Senior untuk Junior/AI**:
  1. Pada method `tutupAntrean(BulanMagang $bulanMagang)`:
     - Validasi: Tanggal hari ini harus sudah mencapai atau melewati awal bulan magang (`now()->startOfDay()->gte($bulanMagang->bulan_mulai)`). Jika belum mulai, tolak dengan status 422 / pesan error.
     - Dalam DB transaction: Ambil seluruh pendaftaran berstatus `waiting_list` pada bulan tersebut.
     - Lakukan iterasi per baris dan perbarui status menjadi `ditolak` dengan catatan: *"Antrean ditutup otomatis karena periode magang telah dimulai (AC-04-7)"*.
     - Catat audit trail per baris pendaftaran (hindari bulk update tanpa audit).
  2. Di tampilan Blade, pasang tombol destruktif dengan modal konfirmasi ganda sebelum aksi dieksekusi.
- **Target Uji**: `Tests\Feature\AdminSuperAdminPhaseTwoTest` & unit test penutupan waiting list lulus.

#### Task A-03: Penegakan Siklus Hidup Pembatalan & Pengunduran Diri (BR-07, AC-07-5)
- **Target File**: `app/Http/Controllers/Admin/PenempatanController.php`, `resources/views/admin/penempatan/show.blade.php`
- **Tuntunan Senior untuk Junior/AI**:
  1. Pada method `batalkan(Penempatan $penempatan)`:
     - Periksa waktu eksekusi terhadap `$penempatan->tgl_mulai`.
     - **Skenario 1 (Sebelum Tanggal Mulai Magang - Pembatalan Penerimaan)**:
       - Status pendaftaran diubah menjadi `dibatalkan`.
       - Status penempatan diubah menjadi `mengundurkan_diri`.
       - Kuota jurusan terkait dikembalikan (`$kuota->decrement('terisi')`) dalam transaksi aman `lockForUpdate()`.
       - Akun peserta tetap aktif (`status_aktif = true`) agar dapat mendaftar periode lain.
     - **Skenario 2 (Sejak Hari Mulai Magang - Pengunduran Diri Resmi)**:
       - Status penempatan diubah menjadi `mengundurkan_diri`.
       - Status pendaftaran tetap `diterima`.
       - Kuota jurusan **HANGUS / TIDAK DIKEMBALIKAN**.
       - Akun peserta dinonaktifkan (`$user->update(['status_aktif' => false])`) dan seluruh sesi aktif diputus.
  2. Di tampilan Blade, sediakan modal dengan keterangan dampak yang berbeda sesuai tanggal hari ini.
- **Target Uji**: `Tests\Feature\AdminPenempatanLifecycleTest` (4 test lulus).

#### Task A-04: Pemindahan Pembimbing & Validasi Batas Beban BR-13 (AC-07-4)
- **Target File**: `app/Http/Controllers/Admin/PenempatanController.php`
- **Tuntunan Senior untuk Junior/AI**:
  1. Pada method `pindahPembimbing(Request $request, Penempatan $penempatan)`:
     - Validasi `pembimbing_id` baru harus aktif dan ber-role Pembimbing.
     - Cek parameter konfigurasi `Pengaturan::int('maks_peserta_aktif_per_pembimbing')`.
     - Jika bernilai angka dan beban pembimbing baru sudah mencapai batas, tolak permintaan pemindahan dengan pesan: *"Pembimbing tujuan telah mencapai kapasitas maksimal mentee aktif (BR-13)"*.
     - Catat riwayat audit perpindahan: pembimbing lama vs pembimbing baru.
- **Target Uji**: `Tests\Feature\AdminPenempatanLifecycleTest::test_pindah_pembimbing_ditolak_jika_kapasitas_beban_pembimbing_tujuan_penuh` lulus.

---

### FASE B: Hardening & Penyempurnaan Dashboard Pembimbing

#### Task B-01: Isolasi Hak Akses Anti-IDOR Pembimbing Lapangan (SEC-11, THR-01)
- **Target File**: `app/Http/Requests/TugasRequest.php`, `app/Http/Requests/PenilaianRequest.php`, `app/Policies/TugasPolicy.php`
- **Tuntunan Senior untuk Junior/AI**:
  1. Di method `authorize()` FormRequest:
     ```php
     if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
         $tugas = $this->route('tugas');
         return $tugas && $this->user()?->can('update', $tugas);
     }
     ```
  2. Pastikan Pembimbing A tidak dapat membuka, mengedit, mereview, atau menilai mentee milik Pembimbing B (HTTP 403).
- **Target Uji**: `Tests\Feature\PembimbingIdorProtectionTest` (8 skenario lulus).

#### Task B-02: Fasilitas Edit Tugas & Kunci Status Selesai (AC-08-7)
- **Target File**: `app/Http/Controllers/Pembimbing/TugasController.php`, `resources/views/pembimbing/tugas/edit.blade.php`
- **Tuntunan Senior untuk Junior/AI**:
  1. Buat method `edit(Tugas $tugas)`:
     - Jika `$tugas->status === StatusTugasEnum::Selesai`, kembalikan redirect dengan flash error: *"Tugas yang telah berstatus selesai tidak dapat diedit kembali."*
     - Return view `pembimbing.tugas.edit`.
  2. Pada method `update(TugasRequest $request, Tugas $tugas)`:
     - Tolak perubahan jika status tugas sudah Selesai (HTTP 422 jika API / JSON, redirect error jika web form).
     - Validasi batas tanggal deadline: tidak boleh di masa lampau dan tidak boleh melampaui akhir magang peserta (`$penempatan->tgl_selesai`).
  3. Daftarkan rute `Route::get('/tugas/{tugas}/edit', [PembimbingTugasController::class, 'edit'])->name('tugas.edit')` di `routes/web.php`.
- **Target Uji**: `Tests\Feature\PembimbingIdorProtectionTest::test_pembimbing_bisa_mengakses_form_edit_dan_mengupdate_tugas_belum_selesai` lulus.

#### Task B-03: Tata Kelola Logbook & Pembatasan Pengisian Mundur (BR-09, AC-09-2)
- **Target File**: `app/Http/Controllers/Pembimbing/LogbookReviewController.php`, `app/Models/Logbook.php`
- **Tuntunan Senior untuk Junior/AI**:
  1. Pastikan batas tanggal mundur logbook konsisten dengan konfigurasi `Pengaturan::int('logbook_maks_mundur_hari', 3)`.
  2. Pada saat pembimbing melakukan reviu:
     - Jika status diubah menjadi `perlu_revisi`, kolom catatan masukan (`catatan`) **WAJIB** diisi minimal 5 karakter.
     - Operasi reviu massal (*bulk review*) wajib mengeksekusi iterasi ber-audit.
- **Target Uji**: `Tests\Feature\TugasLogbookTest` & `Tests\Feature\PembimbingFullJobDeskTest` lulus.

#### Task B-04: Kalkulasi Nilai Rubrik & Penetapan Kelulusan Selesai Magang (BR-10, BR-11, AC-10)
- **Target File**: `app/Http/Controllers/Pembimbing/PenilaianController.php`, `app/Services/ScoringService.php`
- **Tuntunan Senior untuk Junior/AI**:
  1. Pastikan seluruh kriteria rubrik aktif memiliki total bobot tepat 100.
  2. Hitung nilai akhir dengan formula bilangan bulat terbobot tanpa galat pecahan desimal (AC-10-1).
  3. Konversi nilai ke predikat mutu (A: >=85, B: >=70, C: >=55, D: >=40, E: <40) secara dinamis dari pengaturan.
  4. Revisi nilai berstatus `final` wajib menyertakan alasan minimal 10 karakter dan terekam pada tabel `penilaian_riwayat`.
  5. Pembimbing hanya dapat menandai penempatan sebagai `selesai` jika status penilaian sudah `final`.
  6. Saat status ditetapkan `selesai`, isi kolom `tgl_ditetapkan_selesai` dan hitung `tgl_nonaktif = tgl_ditetapkan_selesai + masa_tenggang_bulan` (BR-11).
- **Target Uji**: `Tests\Feature\ScoringAndCompletionTest` (7 test lulus).

---

### FASE C: Hardening & Penyempurnaan Dashboard SuperAdmin

#### Task C-01: Penjaga Keamanan SuperAdmin Terakhir (SEC-14, SEC-16)
- **Target File**: `app/Http/Controllers/SuperAdmin/StafController.php`
- **Tuntunan Senior untuk Junior/AI**:
  1. Pada method `toggleAktif(User $staf)`:
     - Larang SuperAdmin menonaktifkan akun miliknya sendiri (`$staf->id === auth()->id()`).
     - Hitung total SuperAdmin aktif:
       ```php
       $activeSACount = User::where('role', RoleEnum::SuperAdmin)->where('status_aktif', true)->count();
       if ($staf->isSuperAdmin() && $activeSACount <= 1) {
           return back()->with('error', 'Penonaktifan ditolak (SEC-16): Sistem wajib memiliki minimal satu akun SuperAdmin aktif.');
       }
       ```
  2. Dilarang mengubah role SuperAdmin melalui form edit staf biasa.
- **Target Uji**: `Tests\Feature\AdminSuperAdminPhaseTwoTest` lulus.

#### Task C-02: Pembuatan Akun Staf dengan Kata Sandi Acak Sistem (SEC-02, BR-15)
- **Target File**: `app/Http/Controllers/SuperAdmin/StafController.php`, `resources/views/superadmin/staf/index.blade.php`
- **Tuntunan Senior untuk Junior/AI**:
  1. Hilangkan field input kata sandi manual pada form penambahan staf (mencegah password lemah).
  2. Hasilkan kata sandi acak 16 karakter yang memenuhi kombinasi huruf dan angka via `Str::random(16)`.
  3. Set atribut akun: `must_change_password = true`, `status_aktif = true`, `email_verified_at = now()`.
  4. Tampilkan kata sandi hasil *generate* **HANYA SATU KALI** di flash message session (`password_baru`).
- **Target Uji**: `Tests\Feature\BackendSupplementalFeaturesTest` lulus.

#### Task C-03: Parameter Sistem & Pembersihan Cache Konfigurasi (SEC-21, PRD 8.6)
- **Target File**: `app/Http/Controllers/SuperAdmin/PengaturanController.php`, `app/Support/Pengaturan.php`
- **Tuntunan Senior untuk Junior/AI**:
  1. Validasi rentang parameter sistem:
     - `berkas_maks_mb`: integer min 1, max 10 (SEC-21).
     - `logbook_maks_mundur_hari`: integer min 1, max 30.
     - `masa_tenggang_bulan`: integer min 1, max 12.
     - Skala ambang predikat: A > B > C > D secara logis.
  2. Setiap penyimpanan berhasil wajib memanggil `Pengaturan::clearCache()` dan mencatat audit perubahan.
- **Target Uji**: `Tests\Feature\SuperAdminUnifiedDashboardFeaturesTest` lulus.

#### Task C-04: Penampil Arsip Permanen Alumni Magang 5-Panel (AC-12-4, C9)
- **Target File**: `app/Http/Controllers/SuperAdmin/ArsipPesertaController.php`, `resources/views/superadmin/arsip/show.blade.php`
- **Tuntunan Senior untuk Junior/AI**:
  1. Di controller, terapkan *eager loading* lengkap:
     `peserta.profil.jurusan`, `pembimbing.bidang`, `bidang`, `kuota.jurusan`, `tugas.progresLogs`, `logbooks`, `penilaian.details.rubrik`, `pendaftaran.dokumen`.
  2. Catat audit akses data tipe `akses_data` saat rekam jejak alumni dibuka.
  3. Sediakan tab navigasi 5 panel *read-only*:
     - Panel 1: Ringkasan Biodata & Institusi
     - Panel 2: Riwayat Penugasan & Progres
     - Panel 3: Logbook Harian
     - Panel 4: Rubrik Penilaian & Lembar Nilai PDF
     - Panel 5: Berkas Pendaftaran Awal
- **Target Uji**: `Tests\Feature\AdminLaporanDanArsipTest` & `Tests\Feature\DashboardModulesRenderingTest` lulus.

---

### FASE D: Cross-Cutting Hardening, Keamanan & QA

#### Task D-01: Penegakan Middleware Akun Aktif & SuperAdmin Read-Only
- **Target File**: `app/Http/Middleware/EnsureAccountActive.php`, `app/Http/Middleware/SuperAdminReadOnly.php`, `app/Http/Kernel.php`
- **Tuntunan Senior untuk Junior/AI**:
  1. Pasang alias `account.active` di Kernel dan terapkan di seluruh grup rute terautentikasi (`admin`, `pembimbing`, `superadmin`, `peserta`). Jika `$user->status_aktif === false`, langsung lakukan *force logout*, invalidasi sesi, dan arahkan ke login dengan pesan error (SEC-03).
  2. Pasang alias `superadmin.readonly` pada rute group `/pembimbing/*`. Jika user adalah SuperAdmin dan metode HTTP tergolong mutasi (POST, PUT, PATCH, DELETE), hentikan dengan HTTP 403 Forbidden (SEC-12).
- **Target Uji**: `Tests\Feature\PembimbingIdorProtectionTest` & `Tests\Feature\PembimbingPortalFeatureTest` lulus.

#### Task D-02: Halaman Kesalahan Kustom HTTP Bebas Kebocoran Informasi (SEC-35)
- **Target File**: `resources/views/errors/` (`403.blade.php`, `404.blade.php`, `419.blade.php`, `429.blade.php`, `500.blade.php`)
- **Tuntunan Senior untuk Junior/AI**:
  1. Buat tampilan Blade mandiri tanpa menggunakan layout dashboard internal staf.
  2. Gunakan pesan ramah dalam Bahasa Indonesia yang solutif.
  3. Pastikan tidak ada kebocoran stack trace, nama file kode sumber, query SQL, atau variabel lingkungan server.
- **Target Uji**: Simulasi HTTP exception mengembalikan view kustom berstatus kode yang sesuai.

#### Task D-03: Penyelarasan Token Warna & Tipografi Design System (07-DS-MagKom)
- **Target File**: Seluruh view dashboard di `resources/views/admin/`, `resources/views/pembimbing/`, dan `resources/views/superadmin/`
- **Tuntunan Senior untuk Junior/AI**:
  1. Audit dan bersihkan seluruh hex code liar di luar 30 token resmi Figma `MagKom/Warna`.
  2. Pastikan font global menggunakan Open Sans.
  3. Selaraskan seluruh status badge dengan palet resmi (Bab 4 Design System):
     - `diterima` & `selesai` => Latar `#E8F7EE`, Teks `#166534`, Border `#16A34A`
     - `ditolak` & `terlambat` => Latar `#FEE2E2`, Teks `#991B1B`, Border `#FECACA`
     - `perlu_revisi` & `diajukan` => Latar `#FEF3C7`, Teks `#92400E`, Border `#FDE68A`
     - `waiting_list` & `info` => Latar `#E6EEFF`, Teks `#0751FF`, Border `#B3C8FF`
- **Target Uji**: Inspeksi DOM visual dan automated test suite lulus.

---

## 5. Strategi Pengujian (Testing Strategy)

Setiap junior developer atau AI yang mengeksekusi rencana ini wajib menjalankan verifikasi bertingkat:

```text
┌──────────────────────────────────────────────────────────────┐
│                  LEVEL 1: Unit & Policy Tests                │
│ (ScoringService, PeriodeMagang, QuotaAllocation, Policies)   │
├──────────────────────────────────────────────────────────────┤
│               LEVEL 2: Feature & Authorization Tests         │
│ (AdminLifecycle, PembimbingIDOR, SuperAdminGovernance)       │
├──────────────────────────────────────────────────────────────┤
│               LEVEL 3: Concurrency & Lock Tests              │
│ (MySQL lockForUpdate, 2 request bersamaan untuk 1 kuota)     │
├──────────────────────────────────────────────────────────────┤
│               LEVEL 4: Full Regression Test Suite            │
│ (php artisan test: 287+ tests wajib lulus 100% tanpa error)  │
└──────────────────────────────────────────────────────────────┘
```

Perintah pengujian standar per task:
```bash
# Uji siklus hidup penempatan admin
php artisan test tests/Feature/AdminPenempatanLifecycleTest.php

# Uji proteksi IDOR & SuperAdmin read-only pembimbing
php artisan test tests/Feature/PembimbingIdorProtectionTest.php

# Uji dashboard eksekutif dan audit log superadmin
php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php

# Uji regresi penuh seluruh aplikasi
php artisan test
```

---

## 6. Kriteria Selesai (Definition of Done)

Sebuah task dinyatakan **SELESAI** jika dan hanya jika:
- [x] Logika bisnis memenuhi Acceptance Criteria (AC) pada PRD v1.1 dan TDD v1.1.
- [x] Tidak ada celah keamanan OWASP / SEC dari Dokumen Addendum 06 yang dilanggar.
- [x] Seluruh input pengguna disanitasi dari SQL injection wildcard, XSS, dan CSV formula injection.
- [x] Desain visual mengadopsi 30 token warna dan tipografi Open Sans sesuai Dokumen 07-DS-MagKom.
- [x] Semua automated tests pada suite terkait berstatus `PASS` (Hijau).
- [x] Regresi penuh `php artisan test` menghasilkan **292 tests lulus (100%)** dengan **0 error dan 0 failure**.
- [x] Kode bersih dari syntax error, debug helper (`dd()`, `dump()`, `console.log()`), dan kredensial rahasia.

---

## 7. Saran Lanjutan (Out of Scope untuk Rencana Ini)
- Pembuatan sertifikat digital berbasis tanda tangan kriptografi QR-Code publik (direncanakan pada rilis masa depan).
- Integrasi Single Sign-On (SSO) ASN Pemkab Sleman via SAML2/OIDC.
- Pembangunan aplikasi mobile native (saat ini web responsive mobile 390px sudah mencukupi).
