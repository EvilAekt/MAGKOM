# Plan: Penyempurnaan Dashboard SuperAdmin Sesuai Spesifikasi PRD, TDD & Roadmap

- **Tanggal**: 2026-10-10
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning)
- **Target Komponen**: `app/Http/Controllers/SuperAdmin/DashboardController.php` & `resources/views/superadmin/dashboard.blade.php`
- **Rujukan Dokumen**: [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md), [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md), [`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md)

---

## 1. Tujuan

Menyempurnakan halaman utama **Dashboard SuperAdmin** (`/superadmin/dashboard`) agar mencakup seluruh indikator tata kelola, aturan bisnis (BR), dan acceptance criteria (AC) yang tercantum dalam PRD/TDD namun belum terekspos secara optimal pada tampilan saat ini. Penyempurnaan ini meliputi:
1. **Live Landing Page Status Indicator (F-01, BR-04, AC-11-6)**: Menampilkan status ketersediaan publik real-time (`Tersedia`, `Tutup`, `Segera`) lengkap dengan penanda mode (`Otomatis Jadwal` vs `Override SuperAdmin`).
2. **Selektor Periode Bulan Dinamis (`?bulan_id=...`)**: Memungkinkan SuperAdmin memilih dan beralih periode bulan magang secara interaktif untuk melihat utilisasi kuota, slot khusus, dan pendaftar tanpa berpindah halaman.
3. **Grace Period & Lifecycle Tracker (F-12, AC-12-2, AC-12-3)**: Menampilkan jumlah peserta yang saat ini berada dalam masa tenggang unduh nilai pasca-selesai sebelum nonaktif permanen oleh cron job.
4. **Indikator Kepatuhan Pagu Kuota Dinas (BR-01 vs `kuota_umum_maks_per_bulan`)**: Memperlihatkan kepatuhan akumulasi kuota jurusan terhadap batas kuota umum bulanan (default 12) yang diatur di konfigurasi sistem.
5. **Indikator Keamanan Staf Internal (BR-15, AC-11-3)**: Menampilkan counter staf baru yang belum aktivasi atau belum mengganti kata sandi awal (`must_change_password`).
6. **Panel Kesehatan Sistem & Integritas Basis Data (TDD Bab 10, NFR-O1)**: Menyajikan widget ringkas status database, disk privat penyimpanan berkas, dan integritas audit trail append-only.

---

## 2. Asumsi & Investigasi Teknis

### Status Fungsionalitas PRD/TDD Saat Ini
- Seluruh modul terpisah (Master Data, Staf, Slot Khusus, Audit Log, Pengaturan, Monitoring, Arsip, Laporan, Role RBAC) **sudah 100% selesai dan lulus uji otomatis** (56+ test passing).
- Namun pada halaman *Dashboard SuperAdmin* (pintu gerbang utama eksekutif), terdapat data agregasi penting yang belum diproyeksikan dari controller ke view Blade:
  - Layanan `BulanStatusService` belum dipanggil di `DashboardController` untuk mengecek status publik aktual bulan aktif.
  - Dropdown filter bulan belum tersedia di antarmuka dashboard, sehingga metrik kuota terkunci hanya pada bulan aktif pertama.
  - Query masa tenggang (`status = Selesai` dan `tgl_nonaktif >= now()`) belum dipisahkan dari total alumni permanen.
  - Query staf pending password (`must_change_password = true`) belum dihitung.

### Asumsi
- Tampilan tetap mempertahankan struktur desain Figma Sleman MagKom (Node `560-26404` & `560-24892`) dengan tab controller segmented (Ringkasan, Seleksi, Bimbingan, Tata Kelola).
- Perubahan ini murni penambahan data agregasi dan rendering visual, tanpa mengubah struktur database (tidak perlu migrasi baru).

---

## 3. Pendekatan Desain & Arsitektur

Kami mengevaluasi 2 pendekatan teknis untuk integrasi metrik ini:

| Parameter | Opsi A: Unified Dashboard Metrics di DashboardController *(Direkomendasikan)* | Opsi B: Widget AJAX / API Endpoint Terpisah |
| :--- | :--- | :--- |
| **Arsitektur** | Server-side rendered (SSR) Blade standar sesuai pola Laravel 10 monolit (TDD Bab 4). | Endpoint JSON REST API + fetch client-side. |
| **Kinerja & Kecepatan** | Cepat, efisien dengan eager-loading, bebas dari isu async race-condition. | Memerlukan overhead request HTTP berulang dan state loading ganda. |
| **Keamanan (RBAC)** | Diproteksi middleware `role:superadmin` langsung pada level rute web. | Memerlukan otorisasi API tambahan dan mitigasi CSRF. |
| **Konsistensi Codebase** | Selaras 100% dengan implementasi modul MagKom eksisting. | Menambah kompleksitas baru di luar arsitektur TDD. |

### Rekomendasi
Memilih **Opsi A**, yaitu menambahkan query agregasi teroptimasi pada [`app/Http/Controllers/SuperAdmin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/DashboardController.php) menggunakan eager loading dan memproyeksikannya ke komponen kartu visual yang elegan pada [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php).

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`app/Http/Controllers/SuperAdmin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/DashboardController.php) | **Modify** | 1. Mendukung query parameter `?bulan_id=` untuk seleksi periode dinamis.<br>2. Mengintegrasikan `BulanStatusService` untuk status publik live bulan aktif.<br>3. Menghitung `totalPesertaMasaTenggang` (AC-12-2).<br>4. Menghitung pagu `kuota_umum_maks_per_bulan` (BR-01) vs akumulasi kuota aktif.<br>5. Menghitung `stafPendingGantiPassword` (BR-15).<br>6. Menyiapkan status kesehatan sistem ringkas (Database & Private Storage). |
| [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php) | **Modify** | 1. Menambahkan dropdown filter periode bulan magang di Quick Actions toolbar.<br>2. Menambahkan chip live status landing page publik (`Tersedia`/`Tutup`/`Segera`) dengan status override.<br>3. Menambahkan sub-metrik Peserta Masa Tenggang pada kartu Peserta Aktif.<br>4. Menambahkan kartu status kepatuhan pagu kuota dinas pada Tab Seleksi.<br>5. Menambahkan widget Aktivasi Staf pada Kolom Pengguna Terbaru.<br>6. Menambahkan kartu Status Kesehatan Sistem & Audit Scheduler pada Tab Tata Kelola. |
| [`tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php) | **Modify** | Menambahkan pengujian otomatis untuk filter `?bulan_id=`, status landing page live, dan metrik masa tenggang pada dashboard SuperAdmin. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1**: Perbarui `app/Http/Controllers/SuperAdmin/DashboardController.php`:
  - Ambil daftar seluruh bulan magang aktif (`$daftarBulan`) untuk dropdown filter.
  - Periksa parameter `$request->query('bulan_id')` untuk memilih bulan yang aktif di dashboard.
  - Gunakan `App\Services\BulanStatusService` untuk mendapatkan status publik live (`Tersedia`, `Tutup`, `Segera`) dan deteksi flag `override_status`.
  - Hitung `$totalPesertaMasaTenggang` (`status = Selesai` dan `tgl_nonaktif >= now()`).
  - Ambil batas `$kuotaMaksPagu = Pengaturan::int('kuota_umum_maks_per_bulan', 12)`.
  - Hitung `$stafPendingPassword = User::whereIn('role', [RoleEnum::Admin, RoleEnum::Pembimbing])->where('must_change_password', true)->count()`.
  - Siapkan status kesehatan disk privat `Storage::disk('private')->exists(...)` dan koneksi DB.
- [x] **Langkah 2**: Perbarui Quick Actions Toolbar di `resources/views/superadmin/dashboard.blade.php`:
  - Tambahkan form dropdown select periode bulan magang dengan auto-submit `onchange="this.form.submit()"`.
  - Tambahkan badge status publik live: `Status Publik: Tersedia / Tutup / Segera` dengan aksen warna Sleman dan penanda `[Override Aktif]` jika di-override.
- [x] **Langkah 3**: Perbarui Kartu Peserta Aktif & Kolom Pengguna Terbaru:
  - Tampilkan rincian `X Sedang Masa Tenggang (Unduh Nilai)` di bawah metrik alumni selesai.
  - Tampilkan warning pill kecil `Y Staf Belum Ganti Sandi Awal` jika terdapat staf pending ganti password.
- [x] **Langkah 4**: Perbarui Tab Seleksi & Kuota:
  - Tampilkan progress bar kepatuhan batas kuota bulanan (`Total Kuota Dialokasikan: X / Pagu Dinas: Y`).
- [x] **Langkah 5**: Perbarui Tab Tata Kelola Sistem:
  - Tambahkan kartu indikator "Status Kesehatan & Keamanan Sistem" (Database Connected, Private Storage Ready, Append-Only Audit Trail Active, Scheduled Tasks Idempotent).
- [x] **Langkah 6**: Tambahkan test cases pada `tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`.
- [x] **Langkah 7**: Jalankan automated test suite untuk memastikan *zero regression*.
- [x] **Langkah 8**: Lakukan verifikasi visual di browser nyata menggunakan `browser_subagent`.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - `php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`
   - `php artisan test tests/Feature/SuperAdminFigmaDesignAlignmentTest.php`
   - `php artisan test tests/Feature/SuperAdminDashboardAndAuditLogTest.php`
2. **Browser Verification**:
   - Buka `/superadmin/dashboard`.
   - Ubah pilihan bulan pada dropdown periode; verifikasi metrik kuota dan antrean diperbarui sesuai bulan yang dipilih.
   - Periksa keberadaan badge status publik live dan kartu kepatuhan pagu kuota.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat Dampak | Strategi Mitigasi |
| :--- | :---: | :--- |
| **Bulan ID tidak valid pada query param** | Rendah | Berikan fallback ke bulan aktif pertama jika `bulan_id` tidak ditemukan di database (`BulanMagang::find($id) ?? $defaultBulan`). |
| **Performa query jika data besar** | Rendah | Seluruh query agregasi memakai `count()` atau `sum()` terindeks tanpa memuat collection besar ke memori. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Status landing page publik live ter-render di dashboard dengan indikasi jadwal otomatis vs override manual.
- [x] SuperAdmin dapat beralih bulan magang melalui dropdown periode di dashboard.
- [x] Sisa peserta dalam masa tenggang unduh nilai (AC-12-2) terlihat jelas di dashboard.
- [x] Utilisasi kuota dibandingkan dengan batas pagu dinas (`kuota_umum_maks_per_bulan`).
- [x] Staf pending ganti password (BR-15) terpantau di kolom pengguna.
- [x] Panel status kesehatan sistem terpasang rapi di Tab Tata Kelola.
- [x] 100% automated tests lulus tanpa regresi (19 tests, 176 assertions passing).

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**:
  - Penambahan unescaped assertion parameter (`$escaped = false`) pada test integritas infrastruktur untuk menangani entitas HTML ampersand `&amp;` dari Blade template.
  - Perbaikan route name pada kartu kesehatan sistem agar sinkron dengan route healthcheck (`route('health')`).
  - Verifikasi otomatis dan visual di browser subagent berhasil dengan rendering yang sempurna dan responsif.
- **Saran Lanjutan (Out of Scope)**:
  - Grafik visual SVG/Canvas tahunan untuk tren kelulusan peserta jika dibutuhkan di masa mendatang.
