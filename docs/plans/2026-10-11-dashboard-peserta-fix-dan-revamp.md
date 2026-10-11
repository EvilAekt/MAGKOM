# Plan: Perbaikan Menyeluruh Seluruh Tampilan & Fitur Portal/Dashboard Peserta MagKom

| Metadata | Keterangan |
|---|---|
| **Tanggal Pembuatan** | 11 Oktober 2026 |
| **Status Dokumen** | Menunggu Persetujuan Pengguna (Draft Plan Rev. 2) |
| **Target Area** | Seluruh Modul Peserta (`/peserta/*`): Dashboard, Profil, Pendaftaran & Berkas, Logbook Harian, Tugas & Progres, Lembar Nilai, Layout & Navigasi |
| **Referensi Utama** | `docs/01-PRD-MagKom.md` (F-02, F-03, F-08, F-09, F-10, F-12, F-16), `docs/07-DESIGN-SYSTEM-MAGKOM.md` (Bab 2, 3, 4) |

---

## 1. Tujuan
Memastikan **seluruh tampilan pada portal/dashboard peserta (`/peserta/*`) dapat dibuka dengan sempurna tanpa bug**, memiliki antarmuka yang konsisten dengan Design System MagKom (Open Sans, Blue 700 `#0641CC`, Blue 600 `#0751FF`, Netral 0–900, status tokens WCAG 2.1 AA), serta memastikan seluruh fitur interaktif (CRUD profil, pengajuan pendaftaran, unggah berkas, logbook harian, submit progres tugas, hingga unduh lembar nilai PDF) berfungsi andal tanpa celah error (zero-bug policy).

Cakupan modul yang diperbaiki dan diverifikasi:
1. **Dashboard Utama (`/peserta/dashboard`)**: Pembuatan dedicated dashboard multi-fase (onboarding, seleksi berkas, aktif magang, selesai magang) lengkap dengan widget KPI dan quick action.
2. **Profil & Biodata (`/peserta/profil`)**: Pengisian data pribadi, institusi, NISN/NIM, jurusan, nomor kontak WhatsApp, dan kontak darurat beserta penanganan validasi instan.
3. **Pendaftaran Magang (`/peserta/pendaftaran`)**: Daftar riwayat pendaftaran, pengecekan kuota aktif per jurusan/jenjang, pembatasan 1 pendaftaran aktif, dan modal pendaftaran baru.
4. **Detail Pendaftaran & Unggah Berkas (`/peserta/pendaftaran/{id}`)**: Visual status tracker 5 fase, upload berkas per jenis dokumen wajib dengan batas ukuran maks MB dinamis, pratinjau dokumen signed URL, pembatalan pengajuan mandiri, dan unduh/pratinjau SK Diterima PDF resmi.
5. **Jurnal & Logbook Harian (`/peserta/logbook`)**: Pencatatan aktivitas kerja harian, validasi batas mundur hari (`logbook_maks_mundur_hari`), batasan unik 1 logbook/hari/penempatan, unggah lampiran PDF / link HTTPS, status reviu pembimbing, dan graceful state saat belum ada penempatan aktif.
6. **Tugas & Progres Pengerjaan (`/peserta/tugas` & `/peserta/tugas/{id}`)**: Tampilan kartu tugas dengan indikator tenggat waktu (deadline), detail instruksi dari pembimbing, upload file hasil kerja & catatan progres, riwayat reviu pembimbing, dan auto-update status tugas.
7. **Lembar Evaluasi Nilai (`/peserta/nilai`)**: Rincian 5 rubrik penilaian resmi, skor dan bobot snapshot, predikat huruf (A–E), catatan pembimbing, tombol unduh PDF resmi (`/peserta/nilai/unduh`), dan graceful state saat penilaian belum final.
8. **Master Layout Peserta (`layouts/peserta.blade.php`)**: Sidebar dengan item navigasi lengkap (Dashboard, Profil, Pendaftaran, Logbook, Tugas, Lembar Nilai), penandaan menu aktif yang akurat, topbar profil dropdown yang interaktif, drawer responsif mobile (<1024px), dan flash alert message.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis:
- **A-1**: Aplikasi menggunakan server-side Blade view dengan Vanilla CSS tokens terstandarisasi di `resources/views/layouts/peserta.blade.php`.
- **A-2**: Pengguna yang mengakses seluruh rute `/peserta/*` terlindungi oleh middleware pipeline: `auth`, `verified`, `must_change_password`, `role:peserta`.
- **A-3**: Akun peserta yang belum memiliki penempatan atau data tugas/logbook harus menerima tampilan status kosong (*graceful empty state*) yang informatif dan estetik, tanpa memicu error HTTP 500 atau 404 tak terduga.
- **A-4**: Seluruh relasi Eloquent (`profil`, `pendaftaran`, `dokumen`, `penempatan`, `tugas`, `logbook`, `penilaian`) menggunakan eager loading untuk menjamin performa cepat (<150ms).

### Pertanyaan Terbuka:
- Tidak ada pertanyaan pemblokir. Semua alur bisnis telah tertera lengkap dalam PRD dan dokumen arsitektur MagKom.

---

## 3. Evaluasi Opsi Desain & Mitigasi Bug

### 3.1 Penanganan State Kosong (*Empty State Handling*)
- **Opsi 1: Menampilkan Error 404 / 403 saat Data Relasi Belum Ada (Misal belum ada Penempatan).**
  - *Kekurangan*: Pengalaman pengguna sangat buruk; peserta baru merasa aplikasi rusak atau terjadi bug.
- **Opsi 2: Graceful Empty State dengan Card Panduan Edukatif (Direkomendasikan).**
  - *Kelebihan*: Bila peserta membuka menu Tugas atau Logbook sebelum diterima magang, halaman tetap terbuka dengan status 200 OK dan menampilkan banner panduan ramah (contoh: *"Fitur Logbook dan Tugas akan aktif otomatis setelah pendaftaran Anda diterima dan ditempatkan pada bidang magang"*).
  - *Rekomendasi*: **Opsi 2**.

### 3.2 Navigasi & Redirection
- **Opsi 1: Membiarkan redirect login ke `/peserta/pendaftaran`.**
  - *Kekurangan*: Inkonsistensi UX dengan Admin/Pembimbing/SuperAdmin yang memiliki `/dashboard`.
- **Opsi 2: Menjadikan `/peserta/dashboard` sebagai Pusat Kendali Tunggal (Direkomendasikan).**
  - *Kelebihan*: Peserta selalu melihat progres terpenting mereka di satu layar, dari status pendaftaran hingga tugas magang yang mendekati deadline.
  - *Rekomendasi*: **Opsi 2**.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| `app/Http/Controllers/Peserta/DashboardController.php` | Buat | Controller agregasi seluruh data fase peserta (profil, status pendaftaran, penempatan, kpi tugas, logbook, nilai). |
| `resources/views/peserta/dashboard.blade.php` | Buat | Tampilan terpadu dashboard peserta multi-fase (Onboarding, Seleksi Pendaftaran, Aktif Magang, Kelulusan). |
| `resources/views/layouts/peserta.blade.php` | Ubah | Tambahkan menu **Dashboard** aktif di sidebar teratas, perbarui logo brand link ke `peserta.dashboard`, pastikan dropdown profil topbar berfungsi optimal. |
| `routes/web.php` | Ubah | Daftarkan route `Route::get('/dashboard', [PesertaDashboardController::class, 'index'])->name('dashboard');`. |
| `app/Http/Controllers/Auth/AuthController.php` | Ubah | Ubah target redirect login `RoleEnum::Peserta` ke `route('peserta.dashboard')`. |
| `resources/views/peserta/logbook/index.blade.php` | Periksa & Poles | Pastikan empty state ketika belum ada penempatan aktif tampil elegan dan tidak ada form submit error. |
| `resources/views/peserta/tugas/index.blade.php` | Periksa & Poles | Pastikan empty state saat belum ada tugas tampil ramah, badge deadline tidak ada bug format tanggal. |
| `resources/views/peserta/nilai/show.blade.php` | Periksa & Poles | Pastikan penanganan saat penilaian belum berstatus `final` menampilkan kartu edukasi reviu tanpa broken link. |
| `tests/Feature/PesertaFullPortalFeatureTest.php` | Buat | Test suite komprehensif menguji **seluruh 8 halaman dan fitur peserta** (bisa dibuka dengan status 200, bebas error, proteksi role, dan pencegahan bug). |

---

## 5. Langkah Implementasi Terperinci

### Tahap 1: Implementasi Dashboard Peserta & Alur Login
- [x] Buat controller `app/Http/Controllers/Peserta/DashboardController.php` dengan penanganan data komprehensif (profil, pendaftaran, penempatan, tugas, logbook, penilaian).
- [x] Buat Blade view `resources/views/peserta/dashboard.blade.php` dengan komponen:
  - Header sapaan personal & info tanggal.
  - Kartu fase adaptif (Fase 1: Onboarding, Fase 2: Seleksi Pendaftaran, Fase 3: Aktif Magang, Fase 4: Selesai Magang).
  - 4 Kartu KPI Statistik (Tugas, Logbook, Sisa Hari Magang, Nilai).
  - Tombol aksi cepat (Isi Logbook, Lihat Tugas, Lengkapi Biodata).
  - List tugas mendesak & logbook terbaru.
- [x] Daftarkan rute `Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');` di `routes/web.php`.
- [x] Perbarui redirect `AuthController.php` untuk `RoleEnum::Peserta` agar menuju `route('peserta.dashboard')`.

### Tahap 2: Penyelarasan Layout Shell & Navigasi Sidebar
- [x] Perbarui `resources/views/layouts/peserta.blade.php`:
  - Tambahkan item menu `Dashboard` paling atas di bawah `Menu Utama` dengan route matching `request()->routeIs('peserta.dashboard')`.
  - Ubah target link logo `brand-box` ke `route('peserta.dashboard')`.
  - Verifikasi seluruh link menu (Profil, Pendaftaran, Logbook, Tugas, Lembar Nilai) agar penanda `.active` menyala presisi saat sub-halaman dibuka (seperti detail tugas dan detail pendaftaran).
  - Verifikasi topbar dropdown profil (Avatar inisial, nama peserta, email, badge PESERTA, tautan menu, tombol logout).

### Tahap 3: Verifikasi Bebas Bug & Polish Setiap Halaman Peserta
- [x] **Halaman Profil (`peserta.profil.show`)**:
  - Verifikasi pengisian biodata, institusi, NIM/NISN, nomor HP, kontak darurat.
  - Pastikan pesan sukses/error (flash session) tampil rapi.
- [x] **Halaman Pendaftaran (`peserta.pendaftaran.index`)**:
  - Verifikasi pembatasan maksimal 1 pendaftaran aktif.
  - Verifikasi modal/form buat pendaftaran baru dengan kuota aktif yang tersedia.
- [x] **Halaman Detail Pendaftaran (`peserta.pendaftaran.detail`)**:
  - Verifikasi visual status tracker 5 tahapan.
  - Verifikasi upload dokumen persyaratan (validasi format dan batas MB).
  - Pastikan tombol "Ajukan Pendaftaran" disable jika dokumen wajib belum lengkap.
  - Verifikasi tombol unduh & pratinjau SK Diterima bila status diterima.
- [x] **Halaman Logbook (`peserta.logbook.index`)**:
  - Pastikan jika peserta belum aktif magang, form di-disable dan muncul alert edukatif ("Belum memiliki penempatan magang aktif").
  - Pastikan validasi batas tanggal mundur dan pencegahan duplikasi logbook di tanggal yang sama berjalan akurat.
- [x] **Halaman Tugas (`peserta.tugas.index` & `peserta.tugas.show`)**:
  - Pastikan jika belum ada tugas, muncul kartu empty state yang rapi.
  - Verifikasi form submit progres pengerjaan tugas (upload file & catatan).
- [x] **Halaman Nilai (`peserta.nilai.show`)**:
  - Pastikan jika nilai belum final, muncul kartu informatif yang ramah tanpa tombol unduh rusak.
  - Pastikan jika nilai final, tabel 5 rubrik, predikat huruf (A–E), dan tombol cetak PDF resmi berfungsi lancar.

### Tahap 4: Test Suite Otomatis Komprehensif (Zero-Bug Assurance)
- [x] Buat file test `tests/Feature/PesertaFullPortalFeatureTest.php`.
- [x] Implementasikan uji menyeluruh untuk:
  1. `peserta.dashboard`: bisa dibuka 200 OK untuk peserta baru, peserta mendaftar, peserta aktif, dan peserta selesai.
  2. `peserta.profil.show` & `peserta.profil.simpan`: bisa dibuka dan berhasil update profil.
  3. `peserta.pendaftaran.index` & `peserta.pendaftaran.detail`: bisa dibuka dan me-render dokumen dengan baik.
  4. `peserta.logbook.index`: bisa dibuka tanpa error saat ada maupun belum ada penempatan.
  5. `peserta.tugas.index` & `peserta.tugas.show`: bisa dibuka dan merender tugas dengan baik.
  6. `peserta.nilai.show`: bisa dibuka dan menangani kondisi draft vs final secara elegan.
  7. Proteksi IDOR & Otorisasi role: admin/pembimbing/tamu tidak bisa membobol akses peserta sembarangan.
- [x] Jalankan test suite via `php artisan test --filter=PesertaFullPortalFeatureTest` dan pastikan seluruh test 100% Green.

---

## 6. Strategi Testing

| Kasus Uji | Endpoint | Peran Penguji | Kondisi Data | Ekspektasi |
|---|---|---|---|---|
| Dashboard Onboarding | `GET /peserta/dashboard` | Peserta | Akun baru, belum ada pendaftaran | HTTP 200, Panduan onboarding muncul |
| Dashboard Seleksi | `GET /peserta/dashboard` | Peserta | Pendaftaran diajukan | HTTP 200, Status tracker pendaftaran muncul |
| Dashboard Aktif | `GET /peserta/dashboard` | Peserta | Penempatan Aktif, ada tugas & logbook | HTTP 200, KPI tugas & logbook muncul |
| Dashboard Selesai | `GET /peserta/dashboard` | Peserta | Penempatan Selesai, nilai final | HTTP 200, Predikat nilai & CTA unduh PDF muncul |
| Halaman Profil | `GET /peserta/profil` | Peserta | Ada profil | HTTP 200, Formulir biodata tampil |
| Halaman Pendaftaran | `GET /peserta/pendaftaran` | Peserta | Ada riwayat | HTTP 200, Tabel riwayat tampil |
| Detail Pendaftaran | `GET /peserta/pendaftaran/{id}` | Peserta | Milik sendiri | HTTP 200, Berkas & status tracker tampil |
| Logbook Peserta | `GET /peserta/logbook` | Peserta | Belum ada penempatan | HTTP 200, Empty state ramah muncul |
| Tugas Peserta | `GET /peserta/tugas` | Peserta | Penempatan aktif | HTTP 200, Daftar tugas tampil |
| Nilai Peserta | `GET /peserta/nilai` | Peserta | Nilai belum final | HTTP 200, State edukatif muncul aman |
| Unduh Nilai Final | `GET /peserta/nilai/unduh` | Peserta | Nilai final | HTTP 200, Stream response PDF resmi |
| Proteksi Tamu | `GET /peserta/dashboard` | Guest | Belum login | HTTP 302 Redirect ke `/login` |

---

## 7. Risiko & Mitigasi

| Risiko | Tingkat | Mitigasi |
|---|---|---|
| Broken link atau halaman 404 pada menu navigasi | Kritis | Seluruh menu menggunakan nama rute resmi Laravel (`route('peserta.*')`) dan divalidasi lewat Feature Test. |
| Error 500 saat memuat relasi null (contoh: belum ada penempatan) | Tinggi | Menggunakan operator *null-safe* (`?->`) dan penanganan `optional()` / fallback Blade view di semua halaman. |
| Celah IDOR pada berkas privat atau pendaftaran | Kritis | Seluruh controller menggunakan otorisasi Policy resmi (`$this->authorize(...)`) dan validasi `user_id == auth()->id()`. |

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Seluruh halaman di portal peserta (`/peserta/dashboard`, `/peserta/profil`, `/peserta/pendaftaran`, `/peserta/pendaftaran/{id}`, `/peserta/logbook`, `/peserta/tugas`, `/peserta/tugas/{id}`, `/peserta/nilai`) dapat dibuka dengan HTTP 200 OK.
- [x] Fitur-fitur interaktif (simpan profil, buat pendaftaran, unggah berkas, tulis logbook, kirim progres tugas, unduh lembar nilai) berjalan lancar tanpa bug.
- [x] Seluruh tampilan mematuhi standar estetika Design System MagKom (Open Sans, palet warna resmi, WCAG 2.1 AA).
- [x] Seluruh skenario pengujian pada `PesertaFullPortalFeatureTest` lolos 100% (Green).
