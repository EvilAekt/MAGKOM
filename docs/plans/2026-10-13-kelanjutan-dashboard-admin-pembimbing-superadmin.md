# Plan: Kelanjutan Dashboard Administrasi, Pembimbing & SuperAdmin — Task Book untuk Junior Developer / AI

- **Tanggal**: 2026-10-13
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning)
- **Acuan**: [PRD](file:///g:/MAGKOM/docs/01-PRD-MagKom.md), [TDD](file:///g:/MAGKOM/docs/02-TDD-MagKom.md), [Roadmap](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md), [DB Schema](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md), [RBAC](file:///g:/MAGKOM/docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md), [QA Addendum](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md), [Design System](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md)

---

## 0. Panduan untuk Junior Developer / AI Agent

> **PENTING — Baca ini SEBELUM mulai mengerjakan task apa pun.**

### Aturan Wajib
1. **Baca file terkait** sebelum mengedit. Jangan menebak struktur kode.
2. **Jangan mengubah kode yang sudah berfungsi** kecuali task menyebutkan secara eksplisit.
3. **Jangan mengarang API, method, atau library.** Gunakan Laravel 10 + PHP 8.3 yang terdokumentasi di TDD.
4. **Setiap task HARUS ada test-nya.** Minimal feature test yang memverifikasi halaman bisa diakses dan role yang tidak boleh mendapat 403.
5. **Jalankan `php artisan test`** setelah setiap task selesai. SEMUA test harus PASS sebelum lanjut ke task berikutnya.
6. **Gunakan token Design System** dari [07-DESIGN-SYSTEM-MAGKOM.md](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md). Jangan pakai warna hex literal langsung di view.
7. **SuperAdmin = READ ONLY** pada fungsi Pembimbing (tugas, review, penilaian). Ini mutlak.
8. **Slot khusus TIDAK PERNAH bocor** ke halaman publik, endpoint publik, atau response JSON publik.
9. **Semua pesan validasi dan teks UI dalam Bahasa Indonesia.**
10. **Credential / secret hanya di `.env`.** Jangan pernah hardcode di kode.

### Konvensi Kode
- Controller tipis → panggil Service → return view / redirect
- Validasi di FormRequest
- Otorisasi di Policy + middleware `role:`
- `$fillable` ketat, `$guarded = []` DILARANG
- Eager loading wajib (`with()`) untuk menghindari N+1
- Audit log via `AuditService` untuk setiap aksi tulis

### Cara Membaca Task
- Setiap task punya **ID unik** (T-XX), **prioritas** (P0/P1/P2), **estimasi waktu**, dan **definisi selesai**.
- **P0** = wajib sebelum go-live, **P1** = sebaiknya sebelum go-live, **P2** = boleh setelah go-live.
- **Dependensi** tercantum — jangan kerjakan task jika dependensinya belum selesai.
- **File yang disentuh** dicantumkan eksplisit agar tidak ada perubahan tak terduga.

---

## 1. Tujuan

Melengkapi seluruh dashboard Administrasi, Pembimbing, dan SuperAdmin agar sesuai 100% dengan PRD v1.1, Design System, dan Addendum Keamanan. Cakupan:

1. **Gap Alignment** — Menutup celah antara kode yang sudah ada dengan spesifikasi PRD/Design System
2. **Security Hardening** — Implementasi kebutuhan SEC dari Addendum Keamanan (doc 06)
3. **UI/UX Polish** — Penyelarasan seluruh view ke Design System token dan pola komponen
4. **Missing Features** — Halaman dan fitur yang belum ada tapi ada di PRD
5. **Testing** — Test coverage untuk setiap halaman dan fitur baru

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi
- **A1**: Seluruh 256+ test yang ada harus tetap PASS setelah setiap fase selesai.
- **A2**: Tidak ada penambahan tabel database baru; semua 20 tabel sudah ada.
- **A3**: Layout `dashboard.blade.php` sudah berfungsi dan dipakai semua role.
- **A4**: Service layer (`AuditService`, `PlacementService`, `QuotaAllocationService`, `ScoringService`, `MasaTenggangService`) sudah ada dan berfungsi.
- **A5**: Middleware (`EnsureUserHasRole`, `EnsurePasswordChanged`, `EnsureParticipantWritable`, `LogAuditTrail`, `SecurityHeaders`) sudah terdaftar di Kernel.
- **A6**: PDF templates (`lembar-nilai.blade.php`, `surat-keterangan-diterima.blade.php`) sudah ada.

### Pertanyaan Terbuka
- Tidak ada pertanyaan kritis. Semua spesifikasi sudah terdokumentasi di 7 dokumen referensi.

---

## 3. Pendekatan Desain & Arsitektur

### Opsi Terpilih: Phased Incremental Enhancement
- Dikerjakan per-fase berurutan: **Fase A (Admin)** → **Fase B (Pembimbing)** → **Fase C (SuperAdmin)** → **Fase D (Cross-cutting)**
- Setiap fase diakhiri dengan full regression test

### Alasan Pemilihan
- Risiko regresi minimal karena perubahan terisolasi per area
- Junior developer / AI bisa fokus satu konteks tanpa bingung switching context
- Setiap fase bisa di-review dan deploy secara independen

### Alternatif yang Dipertimbangkan
- **Big Bang**: Semua sekaligus → Ditolak karena risiko konflik tinggi dan sulit debug
- **Feature-by-feature across roles**: Ditolak karena context switching berat untuk junior

---

## 4. Gap Analysis: PRD vs Codebase Saat Ini

### 4.1 Dashboard Administrasi (Admin)

| # | Fitur PRD (AC) | Status Code | Gap | Prioritas |
|:--|:--|:--|:--|:--|
| G-A01 | Peserta Manual Input (F-07, AC-07-3) — form buat akun peserta + profil + penempatan jalur khusus/umum | ✅ Sudah ada route & controller | **View perlu verifikasi completeness** terhadap PRD 8.2 | P0 |
| G-A02 | Detail Pendaftaran (AC-04-2) — profil, pilihan bulan, berkas unduh signed 5min, riwayat status | ✅ `admin/pendaftaran/show.blade.php` ada | **Verifikasi timeline riwayat status lengkap** | P0 |
| G-A03 | Tutup Antrean Massal (AC-04-7) — semua `waiting_list` bulan tertentu → `ditolak` | Perlu verifikasi | **Pastikan ada tombol + modal konfirmasi + catatan otomatis** | P0 |
| G-A04 | Pembatalan Penerimaan / Pengunduran Diri (BR-07, AC-07-5) — efek berbeda sebelum vs sesudah tgl_mulai | Perlu verifikasi | **Pastikan UI membedakan + modal konfirmasi** | P0 |
| G-A05 | Sidebar: Peserta Manual terpisah dari Penempatan | Perlu verifikasi | **Menu sidebar harus jelas** | P1 |
| G-A06 | Dashboard stat cards sesuai Design System token | ✅ Ada tapi perlu alignment | **Warna badge, font, spacing sesuai DS Bab 2** | P1 |
| G-A07 | Filter & Pencarian admin pendaftaran — escape `%` dan `_` (SEC-33) | Perlu verifikasi | **Anti-injection pada parameter pencarian** | P0 |
| G-A08 | Pagination 25 per halaman (PRD 9.4) | Perlu verifikasi | **Standarisasi semua halaman daftar** | P1 |
| G-A09 | Empty state & error state di setiap halaman daftar (PRD 12.2) | Parsial | **Pastikan ada ikon inbox + pesan langkah berikutnya** | P1 |
| G-A10 | Admin Pengaturan halaman (konfigurasi sistem) | ✅ Ada | **Hanya SuperAdmin bisa akses; Admin redirect** | P0 |

### 4.2 Dashboard Pembimbing

| # | Fitur PRD (AC) | Status Code | Gap | Prioritas |
|:--|:--|:--|:--|:--|
| G-B01 | Daftar Peserta Bimbingan — hanya peserta miliknya (AC-08-5) | ✅ Ada | **Verifikasi IDOR protection — scope `pembimbing_id`** | P0 |
| G-B02 | Buat Tugas (AC-08-1) — deadline ≥ hari ini dan ≤ tgl_selesai penempatan | ✅ Ada | **Verifikasi validasi tanggal di FormRequest** | P0 |
| G-B03 | Review Progres (AC-08-4) — `diterima`/`perlu_revisi`, feedback wajib untuk revisi | ✅ Ada | **Verifikasi feedback min char enforcement** | P0 |
| G-B04 | Review Logbook (AC-09-4) — filter peserta & tanggal, tandai `perlu_revisi` + catatan wajib | ✅ Ada | **Verifikasi catatan wajib saat perlu_revisi** | P0 |
| G-B05 | Form Penilaian (AC-10-1 s/d AC-10-5) — skor per rubrik, draft/final, alasan ubah | ✅ Ada | **Verifikasi snapshot bobot di penilaian_detail** | P0 |
| G-B06 | Tetapkan Selesai (AC-10-5) — harus ada penilaian final dulu | ✅ Ada | **Verifikasi tombol disabled jika belum ada final** | P0 |
| G-B07 | Unduh Lembar Nilai PDF (AC-10-6) — on-the-fly dari data terbaru | ✅ Ada | **Verifikasi PDF content sesuai PRD 8.5** | P0 |
| G-B08 | Tugas editable selama belum selesai (AC-08-7) | Perlu verifikasi | **Pastikan ada tombol edit + route** | P1 |
| G-B09 | Label Terlambat & Mendekati Deadline (BR-16) — badge bahaya/peringatan | ✅ Ada accessor | **Verifikasi badge muncul di view sesuai DS Bab 4** | P1 |
| G-B10 | Mode read-only: SuperAdmin lihat fungsi Pembimbing tanpa bisa tulis | ✅ Policy ada | **Verifikasi di VIEW: tombol tulis hidden/disabled untuk SA** | P0 |
| G-B11 | Responsive mobile view pembimbing — sidebar jadi drawer < 1024px | Perlu verifikasi | **Test di viewport 390px** | P1 |

### 4.3 Dashboard SuperAdmin

| # | Fitur PRD (AC) | Status Code | Gap | Prioritas |
|:--|:--|:--|:--|:--|
| G-C01 | Master Jurusan CRUD + penegakan BR-12 (tidak bisa nonaktifkan jika ada data aktif) | ✅ Ada | **Verifikasi error message menyebut penyebab** | P0 |
| G-C02 | Master Bidang CRUD + flag aktif/nonaktif | ✅ Ada | **Verifikasi tidak ada tombol hapus (hanya toggle)** | P0 |
| G-C03 | Master Rubrik CRUD + total bobot harus 100 (AC-10-4) | ✅ Ada | **Verifikasi validasi realtime total bobot** | P0 |
| G-C04 | Bulan Magang CRUD + override status (AC-11-6) | ✅ Ada | **Verifikasi dropdown override Tersedia/Tutup/Segera/Kosong** | P0 |
| G-C05 | Slot Khusus (F-06) — AC-06-1 s/d AC-06-5 | ✅ Ada | **Verifikasi SuperAdmin only + default dari pengaturan** | P0 |
| G-C06 | Akun Staf CRUD (AC-11-3) — must_change_password, tidak kirim email | ✅ Ada | **Verifikasi password acak (SEC-02) bukan manual input** | P0 |
| G-C07 | Reset Password Staf (AC-11-4) — password baru diserahkan manual | ✅ Ada | **Verifikasi display sekali lalu hilang** | P0 |
| G-C08 | Nonaktifkan Staf — penegakan BR-12 (Pembimbing terakhir tidak bisa dinonaktifkan) | ✅ Ada | **Verifikasi error message** | P0 |
| G-C09 | Pengaturan Sistem (AC-11-7) — 8 parameter dari PRD 8.6 | ✅ Ada | **Verifikasi validasi tipe dan rentang + cache clear** | P0 |
| G-C10 | Audit Log Viewer (AC-13-6) — filter, read-only, TIDAK ada edit/hapus | ✅ Ada | **Verifikasi tidak ada POST/PUT/DELETE route** | P0 |
| G-C11 | Arsip Peserta (AC-12-4) — data lengkap read-only untuk peserta nonaktif | ✅ Ada | **Verifikasi tab navigasi: Profil, Timeline, Tugas, Logbook, Penilaian** | P1 |
| G-C12 | Penempatan Master View — lihat semua penempatan termasuk nonaktif (AC-07-7) | Perlu verifikasi | **Pastikan filter status termasuk nonaktif** | P1 |
| G-C13 | Pindah Pembimbing (AC-07-4) — validasi batas beban (BR-13) + audit | Perlu verifikasi | **Pastikan ada di view + modal konfirmasi** | P0 |
| G-C14 | Penjaga SuperAdmin terakhir (SEC-14) — tidak bisa nonaktifkan diri sendiri | Perlu verifikasi | **Tambah validasi jika belum ada** | P0 |

---

## 5. Langkah Implementasi (Task List)

> **Setiap task = 1 commit. Jalankan `php artisan test` setelah setiap task.**

### FASE A: Dashboard Administrasi (Admin)

#### T-A01: Verifikasi & Perbaiki Halaman Admin Pendaftaran Index (P0)
**Estimasi**: 45 menit | **Dependensi**: Tidak ada | **AC**: AC-04-1

**Langkah:**
1. Buka `app/Http/Controllers/Admin/PendaftaranController.php` method `index()`
2. Pastikan query:
   - Filter status: `diajukan`, `waiting_list` (default)
   - Filter bulan, jurusan, jalur
   - Pencarian: nama, email, NIM/NISN — **escape `%` dan `_` dengan `str_replace(['%', '_'], ['\\%', '\\_'], $search)`** (SEC-33)
   - Sort: `diajukan_at` ASC (terlama dulu)
   - Pagination: `->paginate(25)` (PRD 9.4)
3. Buka `resources/views/admin/pendaftaran/index.blade.php`
4. Pastikan ada:
   - Toolbar: search input, dropdown filter status, dropdown filter bulan, dropdown filter jurusan
   - Tabel: kolom Nama, Email, NIM/NISN, Jurusan, Bulan, Status (badge sesuai DS Bab 4), Tanggal Diajukan, Aksi (Detail)
   - Pagination bawah: "Menampilkan 1–25 dari X pendaftaran"
   - **Empty state**: ikon `inbox`, teks "Belum ada pendaftaran yang perlu diverifikasi"
5. Buka `resources/views/admin/pendaftaran/show.blade.php`
6. Pastikan ada:
   - Card profil peserta (nama, email, NIM/NISN, institusi, jurusan, jenjang, no_hp)
   - Card pilihan pendaftaran (bulan, durasi, jalur)
   - Daftar berkas dengan tombol unduh (tautan signed 5 menit) — `URL::temporarySignedRoute('berkas.unduh', now()->addMinutes(5), ['dokumen' => $doc->id])`
   - Timeline riwayat status (dari audit_log atau dari kolom timestamps)
   - Form keputusan: dropdown (Diterima, Ditolak, Waiting List, Perlu Perbaikan) + textarea catatan (wajib min 10 char untuk Ditolak/Perlu Perbaikan)

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Controllers/Admin/PendaftaranController.php` | Verify/Modify |
| `resources/views/admin/pendaftaran/index.blade.php` | Verify/Modify |
| `resources/views/admin/pendaftaran/show.blade.php` | Verify/Modify |
| `app/Http/Requests/Admin/KeputusanPendaftaranRequest.php` | Verify/Create |

**Definisi Selesai:**
- [ ] Pencarian escape `%` dan `_`
- [ ] Pagination 25/halaman
- [ ] Empty state ada
- [ ] Timeline riwayat ada di show
- [ ] Form keputusan validasi catatan min 10 char
- [ ] Test: Admin bisa akses, Peserta 403, Pembimbing 403

---

#### T-A02: Tombol Tutup Antrean Massal Waiting List (P0)
**Estimasi**: 60 menit | **Dependensi**: T-A01 | **AC**: AC-04-7

**Langkah:**
1. Buka `app/Http/Controllers/Admin/WaitingListController.php`
2. Pastikan ada method `tutupAntrean(Request $request)` yang:
   - Menerima `bulan_magang_id`
   - Validasi: bulan sudah dimulai (tanggal 1 bulan itu ≤ hari ini)
   - Dalam DB transaction: update semua pendaftaran `waiting_list` bulan itu → `ditolak`
   - Catatan otomatis: "Antrean ditutup karena bulan magang telah dimulai"
   - Audit log per baris (BUKAN bulk update tanpa event — loop dan audit satu-satu)
3. Buka `resources/views/admin/waiting-list/index.blade.php`
4. Tambah tombol "Tutup Antrean Bulan Ini" dengan:
   - Warna destruktif (Red 700)
   - Modal konfirmasi: "Yakin ingin menutup seluruh antrean bulan X? Semua pendaftar waiting list akan ditolak. Aksi ini tidak dapat dibatalkan."
   - Tombol "Tutup Antrean" dan "Batal"
5. Route: `POST /admin/waiting-list/tutup-antrean` dengan middleware `role:admin,superadmin`

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Controllers/Admin/WaitingListController.php` | Verify/Modify |
| `resources/views/admin/waiting-list/index.blade.php` | Verify/Modify |
| `routes/web.php` | Verify route exists |

**Definisi Selesai:**
- [ ] Tutup antrean hanya jika bulan sudah dimulai
- [ ] Semua WL → ditolak dengan catatan otomatis
- [ ] Audit log per baris
- [ ] Modal konfirmasi destruktif
- [ ] Test: bulan belum mulai → ditolak; bulan sudah mulai → berhasil

---

#### T-A03: Pembatalan Penerimaan & Pengunduran Diri (P0)
**Estimasi**: 90 menit | **Dependensi**: T-A01 | **AC**: AC-07-5, BR-07

**Langkah:**
1. Buka `app/Http/Controllers/Admin/PenempatanController.php`
2. Pastikan ada method `batalkan(Penempatan $penempatan)` yang:
   - **Sebelum tgl_mulai** (pembatalan):
     - Pendaftaran → `dibatalkan`
     - Penempatan → `mengundurkan_diri`
     - Kuota `terisi` dikurangi 1 (dalam transaksi + lockForUpdate)
     - Akun peserta tetap aktif
   - **Sejak tgl_mulai** (pengunduran diri):
     - Penempatan → `mengundurkan_diri`
     - Pendaftaran tetap `diterima`
     - Kuota TIDAK dikembalikan
     - Akun peserta dinonaktifkan (`status_aktif = false`)
   - Semua dalam satu DB::transaction
   - Audit log
3. Di view `admin/penempatan/show.blade.php` atau `index.blade.php`:
   - Tombol "Batalkan" / "Catat Pengunduran Diri" (sesuai konteks tanggal)
   - Modal konfirmasi yang menjelaskan dampak berbeda
   - Teks berbeda: sebelum mulai → "Kuota akan dikembalikan", sesudah mulai → "Kuota TIDAK dikembalikan, akun peserta akan dinonaktifkan"

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Controllers/Admin/PenempatanController.php` | Verify/Modify |
| `app/Services/PlacementService.php` | Verify method exists |
| `resources/views/admin/penempatan/show.blade.php` | Verify/Modify |

**Definisi Selesai:**
- [ ] Sebelum tgl_mulai: kuota kembali, akun aktif
- [ ] Sejak tgl_mulai: kuota tidak kembali, akun nonaktif
- [ ] Satu transaksi DB
- [ ] Audit log lengkap
- [ ] Modal konfirmasi dengan dampak yang benar
- [ ] Test: kedua skenario (sebelum & sesudah tgl_mulai)

---

#### T-A04: Pindah Pembimbing dari View Admin (P0)
**Estimasi**: 60 menit | **Dependensi**: T-A01 | **AC**: AC-07-4, BR-13

**Langkah:**
1. Pastikan di `app/Http/Controllers/Admin/PenempatanController.php` ada method `pindahPembimbing(Request $request, Penempatan $penempatan)` yang:
   - Menerima `pembimbing_id` baru (dropdown Pembimbing aktif)
   - Cek batas beban `maks_peserta_aktif_per_pembimbing` via `Pengaturan::int(...)`
   - Jika melebihi → tolak dengan pesan
   - Update `pembimbing_id` dalam transaksi
   - Audit log: pembimbing lama, pembimbing baru
2. Di view `admin/penempatan/show.blade.php`:
   - Tombol "Pindah Pembimbing" → modal
   - Modal: dropdown Pembimbing aktif + info beban saat ini + tombol Pindahkan
3. Route: `PUT /admin/penempatan/{penempatan}/pindah-pembimbing`

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Controllers/Admin/PenempatanController.php` | Verify/Modify |
| `resources/views/admin/penempatan/show.blade.php` | Verify/Modify |

**Definisi Selesai:**
- [ ] Dropdown hanya Pembimbing aktif
- [ ] Batas beban dicek
- [ ] Audit log perpindahan
- [ ] Test: pindah berhasil, batas penuh → ditolak

---

#### T-A05: Verifikasi Empty State & Error State Semua Halaman Admin (P1)
**Estimasi**: 45 menit | **Dependensi**: T-A01 | **AC**: PRD 12.2 butir 5, AC-DS-10

**Langkah:**
1. Buka setiap view di `resources/views/admin/`:
   - `dashboard.blade.php`
   - `pendaftaran/index.blade.php`
   - `kuota/index.blade.php`
   - `waiting-list/index.blade.php`
   - `penempatan/index.blade.php`
   - `laporan/index.blade.php`
2. Untuk setiap view, pastikan ada:
   - **Empty state**: `@if($items->isEmpty())` → tampilkan ikon `inbox` dari Lucide, teks deskriptif Bahasa Indonesia, langkah berikutnya
   - **Loading state**: skeleton placeholder (opsional untuk Blade, tapi bagus untuk UX)
   - **Error state**: catch exception → pesan informatif tanpa detail teknis
3. Pattern komponen Blade yang direkomendasikan:
```blade
@if($pendaftaran->isEmpty())
<div class="flex flex-col items-center py-16 text-neutral-600">
    {{-- Lucide inbox icon SVG --}}
    <svg class="w-12 h-12 mb-4 text-neutral-400">...</svg>
    <p class="text-body font-semibold">Belum ada pendaftaran</p>
    <p class="text-caption mt-1">Pendaftaran akan muncul setelah peserta mengajukan berkas.</p>
</div>
@else
    {{-- tabel data --}}
@endif
```

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| Semua view di `resources/views/admin/` | Verify/Modify |

**Definisi Selesai:**
- [ ] Setiap halaman daftar punya empty state dengan ikon + pesan
- [ ] Tidak ada error 500 jika data kosong

---

#### T-A06: Alignment Design System Token di Semua View Admin (P1)
**Estimasi**: 90 menit | **Dependensi**: T-A05 | **AC**: AC-DS-1

**Langkah:**
1. Audit semua file di `resources/views/admin/`:
   - Cari hex literal `#[0-9A-Fa-f]{3,8}` → ganti ke class Tailwind dari DS token
   - Cari `style=` inline → ganti ke class Tailwind
2. Badge status harus pakai komponen Blade `<x-status-badge>` (jika ada) atau pattern yang konsisten:
   - `diterima` → badge sukses (Green 100 bg, Green 700 text)
   - `ditolak` → badge bahaya (Red 200 bg, Red 700 text)
   - `waiting_list` → badge info (Blue Tint bg, Blue 700 text)
   - `perlu_perbaikan` → badge peringatan (Amber 200 bg, Amber 700 text)
   - `diajukan` → badge peringatan
   - `berkas_belum_lengkap` → badge netral (Neutral 200 bg, Neutral 600 text)
   - `dibatalkan` → badge netral
3. Tombol: primer (Blue 600), sekunder (outline Blue 600), destruktif (Red 700)
4. Font: Open Sans, bukan Plus Jakarta Sans
5. Radius: 8 default, 12 callout, 16 modal, full badge

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| Semua view di `resources/views/admin/` | Modify |

**Definisi Selesai:**
- [ ] Tidak ada hex literal di view (kecuali token file)
- [ ] Tidak ada `style=` inline
- [ ] Badge status konsisten dengan DS Bab 4
- [ ] Test CI: grep `#[0-9A-Fa-f]` → 0 matches di admin views

---

### FASE B: Dashboard Pembimbing

#### T-B01: Verifikasi IDOR Protection Seluruh Route Pembimbing (P0)
**Estimasi**: 60 menit | **Dependensi**: Tidak ada | **AC**: SEC-11, THR-01

**Langkah:**
1. Buka `app/Http/Controllers/Pembimbing/TugasController.php`:
   - Setiap method yang menerima `Tugas $tugas` HARUS cek `$tugas->penempatan->pembimbing_id === auth()->id()`
   - Atau gunakan scoped binding: `Route::get('/tugas/{tugas}', ...)` dengan scope via `Penempatan`
2. Buka `app/Http/Controllers/Pembimbing/LogbookReviewController.php`:
   - Sama: scope ke peserta bimbingannya saja
3. Buka `app/Http/Controllers/Pembimbing/PenilaianController.php`:
   - Sama: `$penilaian->pembimbing_id === auth()->id()`
4. Buka `app/Http/Controllers/Pembimbing/PesertaController.php`:
   - Query `Penempatan::where('pembimbing_id', auth()->id())`
5. Buat test `tests/Feature/PembimbingIdorProtectionTest.php`:
```php
// P-PB1 mencoba akses tugas milik P-PB2
public function test_pembimbing_tidak_bisa_akses_tugas_bukan_miliknya()
{
    $this->actingAs($pembimbing1)->get("/pembimbing/tugas/{$tugasMilikPembimbing2->id}")
        ->assertStatus(403);
}
```

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Controllers/Pembimbing/TugasController.php` | Verify |
| `app/Http/Controllers/Pembimbing/LogbookReviewController.php` | Verify |
| `app/Http/Controllers/Pembimbing/PenilaianController.php` | Verify |
| `app/Http/Controllers/Pembimbing/PesertaController.php` | Verify |
| `app/Policies/TugasPolicy.php` | Verify |
| `app/Policies/PenilaianPolicy.php` | Verify |
| `tests/Feature/PembimbingIdorProtectionTest.php` | Create |

**Definisi Selesai:**
- [ ] Semua route Pembimbing di-scope ke pembimbing_id
- [ ] Test IDOR: Pembimbing A akses data Pembimbing B → 403
- [ ] Test IDOR: Peserta akses route Pembimbing → 403

---

#### T-B02: Verifikasi SuperAdmin Read-Only di View Pembimbing (P0)
**Estimasi**: 45 menit | **Dependensi**: T-B01 | **AC**: AC-08-5, AC-10-7, SEC-12

**Langkah:**
1. Buka setiap view di `resources/views/pembimbing/`:
   - `tugas/index.blade.php`, `tugas/show.blade.php`, `tugas/create.blade.php`
   - `logbook/index.blade.php`
   - `penilaian/index.blade.php`, `penilaian/show.blade.php`
   - `peserta/index.blade.php`, `peserta/show.blade.php`
2. Pastikan SEMUA tombol tulis (Buat Tugas, Review, Simpan Nilai, Tetapkan Selesai, dll) dibungkus:
```blade
@if(auth()->user()->role !== \App\Enums\RoleEnum::SuperAdmin)
    <button ...>Buat Tugas</button>
@endif
```
3. Atau pakai `@can()`:
```blade
@can('create', [App\Models\Tugas::class, $penempatan])
    <button ...>Buat Tugas</button>
@endcan
```
4. Pastikan ada **banner read-only** untuk SuperAdmin:
```blade
@if(auth()->user()->role === \App\Enums\RoleEnum::SuperAdmin)
<div class="bg-blue-tint border border-blue-line rounded-lg p-4 mb-6">
    <p class="text-blue-700 text-body">
        <svg class="inline w-5 h-5 mr-2">{{-- info icon --}}</svg>
        Anda mengakses halaman ini dalam mode hanya-lihat.
    </p>
</div>
@endif
```
5. Pastikan middleware `superadmin.readonly` (SEC-12) terpasang di route group `/pembimbing/*`
6. Test: SuperAdmin POST ke route pembimbing → 403

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| Semua view di `resources/views/pembimbing/` | Verify/Modify |
| `routes/web.php` | Verify middleware |
| `app/Http/Middleware/SuperAdminReadOnly.php` | Verify/Create |

**Definisi Selesai:**
- [ ] Semua tombol tulis hidden untuk SuperAdmin
- [ ] Banner read-only tampil untuk SuperAdmin
- [ ] POST/PUT/PATCH dari SuperAdmin → 403
- [ ] Test: SA GET → 200, SA POST → 403

---

#### T-B03: Edit Tugas oleh Pembimbing (P1)
**Estimasi**: 60 menit | **Dependensi**: T-B01 | **AC**: AC-08-7

**Langkah:**
1. Buka `app/Http/Controllers/Pembimbing/TugasController.php`
2. Pastikan ada method `edit(Tugas $tugas)` dan `update(Request $request, Tugas $tugas)`:
   - Edit hanya jika tugas belum selesai (`$tugas->status !== StatusTugasEnum::Selesai`)
   - Validasi: judul 3-150 char, deskripsi max 2000, kategori max 50, kompleksitas enum, deadline ≥ hari ini dan ≤ tgl_selesai
   - Audit log perubahan
3. Buat / verifikasi view `resources/views/pembimbing/tugas/edit.blade.php` (bisa reuse form dari create)
4. Route: `GET /pembimbing/tugas/{tugas}/edit`, `PUT /pembimbing/tugas/{tugas}`
5. Di `tugas/show.blade.php`: tombol "Edit" hanya tampil jika tugas belum selesai dan user = Pembimbing

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Controllers/Pembimbing/TugasController.php` | Verify/Modify |
| `resources/views/pembimbing/tugas/edit.blade.php` | Create if not exists |
| `resources/views/pembimbing/tugas/show.blade.php` | Verify tombol edit |
| `routes/web.php` | Verify routes |

**Definisi Selesai:**
- [ ] Edit berhasil jika tugas belum selesai
- [ ] Edit ditolak jika tugas sudah selesai
- [ ] Validasi PRD 8.2 terpenuhi
- [ ] Audit log perubahan
- [ ] Test: edit sukses + edit ditolak (tugas selesai)

---

#### T-B04: Alignment Design System Token View Pembimbing (P1)
**Estimasi**: 90 menit | **Dependensi**: T-B02 | **AC**: AC-DS-1 s/d AC-DS-11

**Langkah:**
Sama dengan T-A06 tapi untuk `resources/views/pembimbing/`.

Tambahan khusus Pembimbing:
1. Badge status tugas: Belum (netral), Proses (peringatan), Selesai (sukses), Terlambat (bahaya)
2. Badge mendekati deadline (peringatan, ikon alarm-clock)
3. Badge review progres: Menunggu (peringatan), Diterima (sukses), Perlu Revisi (peringatan)
4. Badge logbook: Terkirim (sukses), Perlu Revisi (peringatan)
5. Badge penilaian: Draft (netral), Final (sukses)

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| Semua view di `resources/views/pembimbing/` | Modify |

**Definisi Selesai:**
- [ ] Badge status konsisten dengan DS Bab 4
- [ ] Tidak ada hex literal atau `style=` inline

---

#### T-B05: Responsive Mobile View Pembimbing (P1)
**Estimasi**: 90 menit | **Dependensi**: T-B04 | **AC**: AC-DS-6

**Langkah:**
1. Pembimbing adalah satu-satunya role staf yang punya desain Figma mobile (390px)
2. Verifikasi layout `dashboard.blade.php`:
   - Sidebar jadi drawer di `< 1024px` (Tailwind: `lg:` breakpoint)
   - Tombol hamburger menu 44x44 px
   - Konten padding 16px mobile
3. Tabel di `pembimbing/peserta/index.blade.php` dan `tugas/index.blade.php`:
   - Horizontal scroll wrapper atau card layout di mobile
4. Modal: lebar full di mobile, 520px di desktop
5. Touch target: semua tombol dan link min 44x44px

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `resources/views/layouts/dashboard.blade.php` | Verify responsive |
| Semua view di `resources/views/pembimbing/` | Verify/Modify |

**Definisi Selesai:**
- [ ] Tidak ada horizontal scroll pada 390px (kecuali tabel dengan wrapper)
- [ ] Sidebar drawer berfungsi
- [ ] Touch target min 44px

---

### FASE C: Dashboard SuperAdmin

#### T-C01: Verifikasi Penjaga SuperAdmin Terakhir (P0)
**Estimasi**: 30 menit | **Dependensi**: Tidak ada | **AC**: SEC-14

**Langkah:**
1. Buka `app/Http/Controllers/SuperAdmin/StafController.php` atau `AkunController.php`
2. Pastikan ada validasi:
   - SuperAdmin TIDAK bisa menonaktifkan dirinya sendiri
   - SuperAdmin TIDAK bisa mengubah role dirinya sendiri
   - Harus selalu ada minimal 1 SuperAdmin aktif
3. Contoh kode:
```php
if ($user->id === auth()->id()) {
    return back()->withErrors(['error' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
}
$superadminAktif = User::where('role', RoleEnum::SuperAdmin)->where('status_aktif', true)->count();
if ($superadminAktif <= 1 && $user->role === RoleEnum::SuperAdmin) {
    return back()->withErrors(['error' => 'Tidak dapat menonaktifkan SuperAdmin terakhir.']);
}
```
4. Buat test:
```php
public function test_superadmin_tidak_bisa_nonaktifkan_diri_sendiri()
{
    $this->actingAs($superadmin)
        ->put("/superadmin/staf/{$superadmin->id}/toggle-status")
        ->assertSessionHasErrors();
}
```

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Controllers/SuperAdmin/StafController.php` | Verify/Modify |
| `app/Http/Controllers/SuperAdmin/AkunController.php` | Verify |
| Test file | Create/Modify |

**Definisi Selesai:**
- [ ] SA tidak bisa nonaktifkan diri sendiri
- [ ] SA terakhir tidak bisa dinonaktifkan
- [ ] Test kedua skenario

---

#### T-C02: Verifikasi Password Acak untuk Akun Staf (P0)
**Estimasi**: 45 menit | **Dependensi**: Tidak ada | **AC**: SEC-02, BR-15

**Langkah:**
1. Buka `app/Http/Controllers/SuperAdmin/StafController.php` atau `AkunController.php` method `store()`
2. **UBAH** dari "password manual input" ke "password acak sistem":
```php
// BUKAN: $validated['password']
// TAPI:
$password = Str::random(16); // 16 karakter acak
// Pastikan ada huruf dan angka:
while (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $password = Str::random(16);
}
$user = User::create([
    // ...
    'password' => Hash::make($password),
    'must_change_password' => true,
    'email_verified_at' => now(),
]);
// Password ditampilkan SEKALI di response, TIDAK disimpan
return redirect()->route('superadmin.staf.index')
    ->with('password_baru', $password)
    ->with('nama_staf', $user->nama);
```
3. Di view `staf/index.blade.php`, tampilkan flash message password baru:
```blade
@if(session('password_baru'))
<div class="bg-amber-100 border border-amber-700 rounded-lg p-4 mb-6">
    <p class="text-amber-700 font-semibold">Password untuk {{ session('nama_staf') }}:</p>
    <code class="font-mono text-lg select-all">{{ session('password_baru') }}</code>
    <p class="text-caption text-amber-700 mt-2">⚠️ Catat password ini sekarang. Password tidak akan ditampilkan lagi.</p>
</div>
@endif
```
4. **JANGAN** log password di audit (sudah ditangani AuditService sanitasi)

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Controllers/SuperAdmin/StafController.php` | Modify |
| `resources/views/superadmin/staf/index.blade.php` | Modify |
| FormRequest staf | Modify (hapus field password dari user input) |

**Definisi Selesai:**
- [ ] Password dibuat acak oleh sistem (16 char, huruf+angka)
- [ ] Ditampilkan SEKALI di flash message
- [ ] Tidak ada field password di form create staf
- [ ] must_change_password = true
- [ ] Test: create staf → session has 'password_baru'

---

#### T-C03: Verifikasi Audit Log Append-Only (P0)
**Estimasi**: 30 menit | **Dependensi**: Tidak ada | **AC**: AC-13-6, TDD 9.4

**Langkah:**
1. Buka `app/Models/AuditLog.php`:
   - Pastikan `const UPDATED_AT = null`
   - Pastikan ada override yang menolak `update()` dan `delete()`
```php
public function update(array $attributes = [], array $options = [])
{
    throw new \RuntimeException('Audit log bersifat append-only dan tidak dapat diubah.');
}

public function delete()
{
    throw new \RuntimeException('Audit log bersifat append-only dan tidak dapat dihapus.');
}
```
2. Buka `routes/web.php` route group superadmin audit-log:
   - Pastikan HANYA ada `GET` route, TIDAK ada `POST`, `PUT`, `PATCH`, `DELETE`
3. Buka `resources/views/superadmin/audit-log/index.blade.php`:
   - Pastikan TIDAK ada tombol edit, hapus, atau form mutasi apa pun
4. Test:
```php
public function test_audit_log_tidak_bisa_dihapus()
{
    $log = AuditLog::factory()->create();
    $this->expectException(\RuntimeException::class);
    $log->delete();
}
```

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Models/AuditLog.php` | Verify/Modify |
| `routes/web.php` | Verify |
| `resources/views/superadmin/audit-log/index.blade.php` | Verify |

**Definisi Selesai:**
- [ ] Model menolak update() dan delete()
- [ ] Tidak ada route mutasi untuk audit-log
- [ ] Tidak ada tombol edit/hapus di view
- [ ] Test model exception

---

#### T-C04: Verifikasi Pengaturan Sistem Validasi & Cache (P0)
**Estimasi**: 45 menit | **Dependensi**: Tidak ada | **AC**: AC-11-7

**Langkah:**
1. Buka `app/Http/Controllers/SuperAdmin/PengaturanController.php` method `update()`
2. Pastikan validasi setiap key sesuai PRD 8.6:

| Kunci | Tipe | Min | Max | Default |
|:---|:---|:---|:---|:---|
| `masa_tenggang_bulan` | integer | 0 | - | 1 |
| `kuota_umum_maks_per_bulan` | integer | 0 | - | 12 |
| `slot_khusus_default` | integer | 0 | - | 3 |
| `maks_peserta_aktif_per_pembimbing` | integer/null | 0 | - | null |
| `logbook_maks_mundur_hari` | integer | 0 | - | 3 |
| `berkas_maks_mb` | integer | 1 | 10 | 2 |
| `deadline_mendekati_hari` | integer | 0 | - | 3 |
| `predikat_batas` | JSON | - | - | `{"A":85,"B":70,"C":55,"D":40}` |

3. Setelah update, pastikan cache dibersihkan: `Cache::forget('pengaturan')` atau mekanisme yang sudah ada di `Support/Pengaturan.php`
4. Audit log setiap perubahan

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Controllers/SuperAdmin/PengaturanController.php` | Verify |
| `app/Support/Pengaturan.php` | Verify cache mechanism |

**Definisi Selesai:**
- [ ] Validasi setiap key sesuai tipe dan rentang
- [ ] `berkas_maks_mb` max 10 (SEC-21)
- [ ] Cache di-clear setelah update
- [ ] Audit log

---

#### T-C05: Alignment Design System Token View SuperAdmin (P1)
**Estimasi**: 120 menit | **Dependensi**: T-C01 s/d T-C04 | **AC**: AC-DS-1

Sama dengan T-A06 tapi untuk seluruh `resources/views/superadmin/`.

**Perhatian khusus:**
1. Audit log viewer: data dari pengguna (user-agent, filter, nama berkas, catatan) harus di-escape `{{ }}` bukan `{!! !!}` (SEC-36)
2. Detail JSON (nilai_lama, nilai_baru) harus dipotong 4KB dan di-escape (SEC-36)
3. Badge tipe audit: `auth` (info), `aksi` (sukses), `service` (netral), `akses_data` (peringatan)

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| Semua view di `resources/views/superadmin/` | Modify |

**Definisi Selesai:**
- [ ] Tidak ada hex literal atau `style=` inline
- [ ] Semua output user-generated di-escape
- [ ] JSON detail truncated 4KB
- [ ] Badge tipe audit konsisten

---

#### T-C06: Verifikasi Arsip Peserta Tab View (P1)
**Estimasi**: 60 menit | **Dependensi**: T-C05 | **AC**: AC-12-4, AC-07-7

**Langkah:**
1. Buka `resources/views/superadmin/arsip/show.blade.php`
2. Pastikan ada tab navigasi:
   - **Profil**: nama, email, NIM/NISN, institusi, jurusan, jenjang, no_hp
   - **Penempatan**: bulan, durasi, jalur, sumber, pembimbing, tgl_mulai, tgl_selesai, status
   - **Tugas & Progres**: daftar tugas + progres masing-masing (read-only)
   - **Logbook**: daftar logbook harian (read-only)
   - **Penilaian**: skor per rubrik, nilai akhir, predikat, riwayat perubahan (read-only)
3. Semua data di-eager load di controller
4. Tombol "Unduh Lembar Nilai PDF" jika penilaian final ada

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Controllers/SuperAdmin/ArsipPesertaController.php` | Verify eager loading |
| `resources/views/superadmin/arsip/show.blade.php` | Verify/Modify |

**Definisi Selesai:**
- [ ] Semua tab render tanpa error
- [ ] Data read-only (tidak ada form)
- [ ] Eager loading lengkap

---

### FASE D: Cross-cutting Concerns

#### T-D01: Implementasi Middleware EnsureAccountActive (P0)
**Estimasi**: 45 menit | **Dependensi**: Tidak ada | **AC**: SEC-03

**Langkah:**
1. Cek apakah `app/Http/Middleware/EnsureAccountActive.php` sudah ada
2. Jika belum, buat sesuai Addendum 06 A.3:
```php
public function handle(Request $request, Closure $next): Response
{
    $user = $request->user();
    if ($user && !$user->status_aktif) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withErrors(['email' => 'Akun tidak aktif. Hubungi Admin Kominfo']);
    }
    return $next($request);
}
```
3. Daftarkan alias `account.active` di `app/Http/Kernel.php` → `$middlewareAliases`
4. Pasang di setiap route group setelah `auth`: admin, pembimbing, superadmin, peserta
5. Test: user `status_aktif=false` dengan sesi aktif → logout paksa

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Middleware/EnsureAccountActive.php` | Create if not exists |
| `app/Http/Kernel.php` | Modify |
| `routes/web.php` | Modify middleware groups |

**Definisi Selesai:**
- [ ] Middleware terdaftar dan terpasang di semua route group auth
- [ ] Akun nonaktif dengan sesi hidup → logout paksa
- [ ] Test: request setelah akun dinonaktifkan → redirect ke login

---

#### T-D02: Implementasi Middleware SuperAdmin Read-Only (P0)
**Estimasi**: 30 menit | **Dependensi**: Tidak ada | **AC**: SEC-12

**Langkah:**
1. Cek apakah `app/Http/Middleware/SuperAdminReadOnly.php` sudah ada
2. Jika belum, buat:
```php
public function handle(Request $request, Closure $next): Response
{
    if ($request->user()?->role === RoleEnum::SuperAdmin && !$request->isMethodSafe()) {
        abort(403);
    }
    return $next($request);
}
```
3. Daftarkan alias `superadmin.readonly` di Kernel
4. Pasang di route group `/pembimbing/*`
5. Test: SA GET pembimbing → 200, SA POST pembimbing → 403

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Middleware/SuperAdminReadOnly.php` | Create if not exists |
| `app/Http/Kernel.php` | Modify |
| `routes/web.php` | Modify |

**Definisi Selesai:**
- [ ] Middleware terpasang di route group pembimbing
- [ ] SA method safe → pass, SA method unsafe → 403

---

#### T-D03: Halaman Error Kustom 403, 404, 419, 429, 500 (P0)
**Estimasi**: 60 menit | **Dependensi**: Tidak ada | **AC**: PRD 9.3, SEC-35

**Langkah:**
1. Cek apakah folder `resources/views/errors/` sudah ada
2. Buat/verifikasi halaman:
   - `403.blade.php`: "Akses Ditolak — Anda tidak memiliki izin untuk mengakses halaman ini."
   - `404.blade.php`: "Halaman Tidak Ditemukan — Halaman yang Anda cari tidak tersedia."
   - `419.blade.php`: "Sesi Kedaluwarsa — Silakan muat ulang halaman dan coba lagi."
   - `429.blade.php`: "Terlalu Banyak Permintaan — Silakan tunggu beberapa saat sebelum mencoba lagi."
   - `500.blade.php`: "Terjadi Kesalahan — Kami sedang memperbaiki masalah ini. Coba lagi nanti."
3. Setiap halaman:
   - Pakai layout sederhana (bukan dashboard layout)
   - Logo MagKom, kode error besar, pesan deskriptif, tombol "Kembali ke Beranda"
   - **TIDAK boleh** menampilkan stack trace, query, atau detail teknis apa pun
   - Memakai token DS: warna, font, spacing
4. Test: `APP_DEBUG=false` dan akses route yang tidak ada → 404 kustom (bukan Whoops)

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `resources/views/errors/403.blade.php` | Create |
| `resources/views/errors/404.blade.php` | Create |
| `resources/views/errors/419.blade.php` | Create |
| `resources/views/errors/429.blade.php` | Create |
| `resources/views/errors/500.blade.php` | Create |

**Definisi Selesai:**
- [ ] Semua 5 halaman error ada dan berisi pesan Bahasa Indonesia
- [ ] Tidak ada detail teknis di halaman error
- [ ] Test: route tidak ada → 404 kustom

---

#### T-D04: Verifikasi Cache-Control: no-store untuk Halaman Terautentikasi (P0)
**Estimasi**: 20 menit | **Dependensi**: Tidak ada | **AC**: SEC-32

**Langkah:**
1. Buka `app/Http/Middleware/SecurityHeaders.php`
2. Pastikan ada logic:
```php
if ($request->user() || $request->routeIs('berkas.*')) {
    $response->headers->set('Cache-Control', 'no-store, private');
    $response->headers->set('Pragma', 'no-cache');
}
```
3. Test: akses halaman admin yang terautentikasi → response header contains `Cache-Control: no-store`

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Middleware/SecurityHeaders.php` | Verify/Modify |

**Definisi Selesai:**
- [ ] Header `Cache-Control: no-store, private` ada di response terautentikasi
- [ ] Landing publik TIDAK di-no-store (boleh cache 30 detik)

---

#### T-D05: Verifikasi TrustHosts Middleware (P0)
**Estimasi**: 15 menit | **Dependensi**: Tidak ada | **AC**: SEC-30

**Langkah:**
1. Buka `app/Http/Middleware/TrustHosts.php`
2. Pastikan method `hosts()` return domain dari `APP_URL`:
```php
public function hosts(): array
{
    return ['^'.preg_quote(parse_url(config('app.url'), PHP_URL_HOST)).'$'];
}
```
3. Buka `app/Http/Kernel.php` → pastikan `TrustHosts::class` ada di `$middleware` (bukan di-comment)

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `app/Http/Middleware/TrustHosts.php` | Verify |
| `app/Http/Kernel.php` | Verify not commented |

**Definisi Selesai:**
- [ ] TrustHosts aktif dan return domain dari APP_URL

---

#### T-D06: Verifikasi dompdf Hardening (P0)
**Estimasi**: 15 menit | **Dependensi**: Tidak ada | **AC**: SEC-34

**Langkah:**
1. Buka `config/dompdf.php`
2. Pastikan:
   - `'enable_remote' => false` (PENTING: mencegah SSRF)
   - `'enable_php' => false` (mencegah code execution)
3. Pastikan semua data pengguna di template PDF di-escape: `{{ }}` bukan `{!! !!}`
4. Buka `resources/views/pdf/lembar-nilai.blade.php` dan `surat-keterangan-diterima.blade.php` → cari `{!! !!}` → ganti ke `{{ }}`

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `config/dompdf.php` | Verify |
| `resources/views/pdf/lembar-nilai.blade.php` | Verify |
| `resources/views/pdf/surat-keterangan-diterima.blade.php` | Verify |

**Definisi Selesai:**
- [ ] `enable_remote = false`
- [ ] `enable_php = false`
- [ ] Tidak ada `{!! !!}` di template PDF

---

#### T-D07: Forbidden Pattern CI Check (P0)
**Estimasi**: 30 menit | **Dependensi**: Tidak ada | **AC**: SEC-61

**Langkah:**
1. Buat script `scripts/cek-pola-terlarang.sh` (atau `.ps1` untuk Windows):
```bash
#!/bin/bash
echo "=== Checking forbidden patterns ==="
ERRORS=0

# Check for unescaped blade output
if grep -rnF '{!!' app/ resources/views/ --include="*.blade.php" | grep -v 'pdf/' | grep -v '{{--'; then
    echo "ERROR: Found {!! !!} in blade files (excluding PDF templates)"
    ERRORS=$((ERRORS+1))
fi

# Check for raw DB queries with user input
if grep -rnF 'DB::raw' app/ --include="*.php"; then
    echo "WARNING: Found DB::raw - verify no user input"
fi

if grep -rnF 'whereRaw' app/ --include="*.php"; then
    echo "WARNING: Found whereRaw - verify no user input"
fi

if grep -rnF '$guarded = []' app/ --include="*.php"; then
    echo "ERROR: Found \$guarded = [] - this is forbidden"
    ERRORS=$((ERRORS+1))
fi

exit $ERRORS
```

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `scripts/cek-pola-terlarang.sh` | Create |

**Definisi Selesai:**
- [ ] Script berjalan tanpa error pada codebase saat ini
- [ ] Bisa diintegrasikan ke CI pipeline

---

#### T-D08: Comprehensive RBAC Matrix Test (P0)
**Estimasi**: 120 menit | **Dependensi**: T-D01, T-D02 | **AC**: B.3 Addendum QA

**Langkah:**
1. Buat `tests/Feature/RbacMatrixTest.php` dengan data provider dari matriks Addendum 06 B.3
2. Test setiap kombinasi: [Role × Route × HTTP Method → Expected Status Code]
3. Minimal cover:
   - Tamu (guest) ke route admin → redirect login
   - Peserta ke route admin → 403
   - Admin ke route pembimbing → 403
   - Pembimbing ke route superadmin → 403
   - SuperAdmin GET ke route pembimbing → 200
   - SuperAdmin POST ke route pembimbing → 403
   - Peserta ke data peserta lain → 403
   - Pembimbing ke data pembimbing lain → 403
   - Method DELETE ke route mana pun → 405

**File yang disentuh:**
| File | Aksi |
|:---|:---|
| `tests/Feature/RbacMatrixTest.php` | Create |

**Definisi Selesai:**
- [ ] Minimal 50 test cases covering matrix
- [ ] All PASS
- [ ] Setiap route baru harus ditambahkan ke matrix

---

#### T-D09: Mobile Responsive Verification Admin & SuperAdmin (P2)
**Estimasi**: 60 menit | **Dependensi**: T-A06, T-C05

**Langkah:**
1. Area Admin dan SuperAdmin **tidak wajib responsif ponsel** menurut PRD (A-26 hanya mewajibkan publik, auth, dan Peserta)
2. Namun, **SEBAIKNYA** tidak rusak di tablet (1024px)
3. Verifikasi:
   - Sidebar collapse ke drawer di `< 1024px`
   - Tabel punya horizontal scroll wrapper
   - Modal tidak overflow viewport
4. **JANGAN** menghabiskan waktu mengoptimalkan layout mobile untuk staf — fokus desktop

**Definisi Selesai:**
- [ ] Tidak ada layout yang rusak total di 1024px
- [ ] Tabel punya overflow-x-auto wrapper

---

## 6. Strategi Testing

### Unit & Integration Test

| Test File | Cakupan | Target Assertions |
|:---|:---|:---|
| `RbacMatrixTest.php` (T-D08) | Matriks hak akses semua role × route | 50+ |
| `PembimbingIdorProtectionTest.php` (T-B01) | IDOR protection semua route pembimbing | 15+ |
| `SecurityMiddlewareTest.php` (T-D01, T-D02) | EnsureAccountActive, SuperAdminReadOnly | 10+ |
| `AdminOperasiPenempatanTest.php` (T-A02 s/d T-A04) | Tutup antrean, pembatalan, pindah pembimbing | 15+ |
| `SuperAdminGuardTest.php` (T-C01 s/d T-C03) | Penjaga SA terakhir, audit append-only | 10+ |

### UI & Manual Verification
- Admin dashboard di 1920×1080: semua halaman accessible
- Pembimbing di 390px: sidebar drawer, modal, tabel
- SuperAdmin di 1920×1080: audit log, master data
- Error pages: 403, 404, 419, 429, 500

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|:---|:---|:---|
| **Regresi test lama** | Fitur yang sudah jalan rusak | Full `php artisan test` setelah setiap task |
| **N+1 query** pada halaman detail | Performa lambat | Wajib `with()` eager loading |
| **IDOR terlewat** pada route baru | Data peserta bocor | Test IDOR wajib per task |
| **Password staf bocor** di audit | Credential leak | AuditService sanitasi sudah ada; verify |
| **{!! !!} di template** | XSS tersimpan | CI check (T-D07) |
| **dompdf SSRF** | Server compromise | enable_remote=false (T-D06) |
| **Slot khusus bocor** | Pelanggaran BR-02 | Test AC-01-5 sudah ada; jangan sentuh |

### Breaking Changes
- T-C02 mengubah flow create staf (password acak bukan manual) → **WAJIB** update test yang ada

### Aksesibilitas
- Semua tombol: focus state visible (cincin 3px Blue 600 20%)
- Badge: ikon + teks + warna (triple redundancy, DS Bab 4)
- Modal: focus trap, Esc tutup, return focus ke trigger

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Semua checklist task (T-A01 s/d T-D09) tercentang `[x]`
- [ ] Full test suite (256+ existing + new) PASS tanpa regresi
- [ ] Lint (Pint) dan analisis statis (Larastan) PASS
- [ ] Tidak ada N+1 query pada halaman daftar
- [ ] Semua pesan validasi dan UI dalam Bahasa Indonesia
- [ ] Empty state dan error state di setiap halaman daftar
- [ ] Badge status sesuai Design System Bab 4
- [ ] Tidak ada hex literal atau `style=` inline di view
- [ ] Halaman error kustom (403, 404, 419, 429, 500) ada
- [ ] Middleware EnsureAccountActive dan SuperAdminReadOnly aktif
- [ ] dompdf `enable_remote=false` dan `enable_php=false`
- [ ] RBAC matrix test coverage 50+ assertions

---

## 9. Urutan Eksekusi yang Disarankan

> **Untuk junior developer / AI agent, kerjakan dalam urutan ini:**

### Sprint 1: Security Foundation (Hari 1)
1. T-D01 (EnsureAccountActive middleware)
2. T-D02 (SuperAdminReadOnly middleware)
3. T-D04 (Cache-Control no-store)
4. T-D05 (TrustHosts)
5. T-D06 (dompdf hardening)
6. T-D07 (Forbidden pattern check)

### Sprint 2: Admin Operations (Hari 2)
7. T-A01 (Admin pendaftaran verify)
8. T-A02 (Tutup antrean massal)
9. T-A03 (Pembatalan/pengunduran diri)
10. T-A04 (Pindah pembimbing)

### Sprint 3: Pembimbing Protection (Hari 3)
11. T-B01 (IDOR protection)
12. T-B02 (SuperAdmin read-only di view)
13. T-B03 (Edit tugas)

### Sprint 4: SuperAdmin Verification (Hari 4)
14. T-C01 (Penjaga SA terakhir)
15. T-C02 (Password acak staf)
16. T-C03 (Audit log append-only)
17. T-C04 (Pengaturan validasi)

### Sprint 5: Error Pages & Testing (Hari 5)
18. T-D03 (Error pages)
19. T-D08 (RBAC matrix test)

### Sprint 6: Design System Polish (Hari 6-7)
20. T-A05 (Admin empty state)
21. T-A06 (Admin DS alignment)
22. T-B04 (Pembimbing DS alignment)
23. T-B05 (Pembimbing mobile)
24. T-C05 (SuperAdmin DS alignment)
25. T-C06 (Arsip tab view)

### Sprint 7: Final (Hari 8)
26. T-D09 (Responsive verification)
27. Full regression test
28. Review & cleanup

---

## 10. Perubahan File (Ringkasan)

| File Path | Aksi | Sprint | Task |
|:---|:---|:---|:---|
| `app/Http/Middleware/EnsureAccountActive.php` | Create/Verify | 1 | T-D01 |
| `app/Http/Middleware/SuperAdminReadOnly.php` | Create/Verify | 1 | T-D02 |
| `app/Http/Middleware/SecurityHeaders.php` | Verify/Modify | 1 | T-D04 |
| `app/Http/Middleware/TrustHosts.php` | Verify | 1 | T-D05 |
| `app/Http/Kernel.php` | Modify | 1 | T-D01,D02 |
| `config/dompdf.php` | Verify | 1 | T-D06 |
| `scripts/cek-pola-terlarang.sh` | Create | 1 | T-D07 |
| `app/Http/Controllers/Admin/PendaftaranController.php` | Verify/Modify | 2 | T-A01 |
| `resources/views/admin/pendaftaran/index.blade.php` | Verify/Modify | 2 | T-A01 |
| `resources/views/admin/pendaftaran/show.blade.php` | Verify/Modify | 2 | T-A01 |
| `app/Http/Controllers/Admin/WaitingListController.php` | Verify/Modify | 2 | T-A02 |
| `resources/views/admin/waiting-list/index.blade.php` | Verify/Modify | 2 | T-A02 |
| `app/Http/Controllers/Admin/PenempatanController.php` | Verify/Modify | 2 | T-A03,A04 |
| `resources/views/admin/penempatan/show.blade.php` | Verify/Modify | 2 | T-A03,A04 |
| `app/Http/Controllers/Pembimbing/TugasController.php` | Verify | 3 | T-B01,B03 |
| `app/Http/Controllers/Pembimbing/LogbookReviewController.php` | Verify | 3 | T-B01 |
| `app/Http/Controllers/Pembimbing/PenilaianController.php` | Verify | 3 | T-B01 |
| `app/Http/Controllers/Pembimbing/PesertaController.php` | Verify | 3 | T-B01 |
| `app/Policies/TugasPolicy.php` | Verify | 3 | T-B01 |
| `app/Policies/PenilaianPolicy.php` | Verify | 3 | T-B01 |
| Semua view `pembimbing/*` | Verify/Modify | 3,6 | T-B02,B04,B05 |
| `resources/views/pembimbing/tugas/edit.blade.php` | Create if needed | 3 | T-B03 |
| `app/Http/Controllers/SuperAdmin/StafController.php` | Modify | 4 | T-C01,C02 |
| `app/Models/AuditLog.php` | Verify | 4 | T-C03 |
| `app/Http/Controllers/SuperAdmin/PengaturanController.php` | Verify | 4 | T-C04 |
| `resources/views/errors/403.blade.php` | Create | 5 | T-D03 |
| `resources/views/errors/404.blade.php` | Create | 5 | T-D03 |
| `resources/views/errors/419.blade.php` | Create | 5 | T-D03 |
| `resources/views/errors/429.blade.php` | Create | 5 | T-D03 |
| `resources/views/errors/500.blade.php` | Create | 5 | T-D03 |
| `tests/Feature/RbacMatrixTest.php` | Create | 5 | T-D08 |
| `tests/Feature/PembimbingIdorProtectionTest.php` | Create | 3 | T-B01 |
| Semua view `admin/*` | Modify | 6 | T-A05,A06 |
| Semua view `superadmin/*` | Modify | 6 | T-C05,C06 |
| `resources/views/superadmin/arsip/show.blade.php` | Verify/Modify | 6 | T-C06 |

---

## 11. Catatan Perubahan & Saran Lanjutan

### Perubahan selama pengerjaan
*(Kosong pada tahap draft; diisi saat eksekusi jika ada penyesuaian)*

### Saran Lanjutan (Out of Scope)
1. **MFA TOTP untuk SuperAdmin** (SEC-05) — Sangat direkomendasikan P1, tapi perlu tambahan tabel dan library
2. **ClamAV malware scan** (SEC-24) — Integrasi scanning saat upload berkas
3. **Pembatasan IP SuperAdmin** (SEC-56) — Nginx `allow`/`deny` untuk `/superadmin/*`
4. **DAST scan dengan OWASP ZAP** (SEC-64) — Scan otomatis sebelum setiap rilis
5. **Alarm keamanan 9 aturan** (SEC-71) — Cron tiap 10 menit query audit_log
6. **Runbook insiden** (SEC-72) — Dokumen prosedur insiden keamanan
7. **Data anak persetujuan** (SEC-41) — Checkbox khusus peserta SMK < 18 tahun
8. **Anonimisasi peserta** (SEC-44) — Command `magkom:anonimkan-peserta`
9. **Playwright E2E tests** — Test browser otomatis untuk 6 alur kritis
10. **k6 performance test** — Load testing sebelum go-live
