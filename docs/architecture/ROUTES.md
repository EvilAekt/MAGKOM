# Peta Rute dan Endpoint Aplikasi MAGKOM

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-25 |
| Cakupan | 217 Rute Aplikasi (222 Total termasuk rute internal framework), metode request, middleware guard, controller mapping |
| Referensi Implementasi | `routes/web.php`, `app/Http/Controllers/` |

---

## 1. Pendahuluan

Seluruh rute HTTP pada sistem MAGKOM didefinisikan secara terpusat pada berkas `routes/web.php`. Total terdapat **217 rute aplikasi** (222 termasuk rute internal framework `_ignition`, `sanctum`, `api/user`) yang dikelompokkan ke dalam domain wewenang terpisah dengan perlindungan middleware berlapis.

---

## 2. Struktur Pengelompokan Rute Berdasarkan Prefiks

| Prefiks URI | Jumlah Endpoint | Sasaran Pengguna | Middleware Utama |
|---|---|---|---|
| `/` (Root & Publik) | 8 | Tamu / Publik | `web`, `throttle` |
| `/login`, `/register`, `/auth`, `/password`, `/email`, `/two-factor-challenge` | 19 | Pengunjung & Autentikasi | `guest` / `auth`, `throttle` |
| `/keamanan/2fa/*` | 5 | Pengguna Terotentikasi (Multi-Role 2FA) | `auth`, `EnsureAccountActive` |
| `/realtime/*` | 4 | Pengguna Terotentikasi | `auth`, `EnsureAccountActive` |
| `/berkas/*` | 2 | Pengguna Terotorisasi Berkas | `auth`, `signed`, Laravel Policies |
| `/peserta/*` | 28 | Peserta Magang | `auth`, `role:peserta`, `EnsureParticipantWritable` |
| `/pembimbing/*` | 33 | Pembimbing Lapangan ASN | `auth`, `role:pembimbing` |
| `/admin/*` | 56 | Admin Verifikator Kominfo | `auth`, `role:admin` |
| `/superadmin/*` | 62 | SuperAdmin Sistem | `auth`, `role:superadmin`, `EnsureSuperAdminReadOnly` |

---

## 3. Rincian Rute per Domain

### 3.1 Rute Publik & Utilitas
| Method | URI | Controller Action | Middleware | Nama Rute |
|---|---|---|---|---|
| `GET` | `/` | `Public\LandingController` | `web` | `landing` |
| `GET` | `/panduan-alur` | `Public\AlurMagangController` | `web` | `panduan-alur` |
| `GET` | `/panduan-magang-pdf` | `Public\PanduanPdfController@download` | `web` | `panduan.pdf` |
| `GET` | `/health` | `Public\HealthController` | `web` | `health` |
| `GET` | `/kebijakan-privasi` | View `public.kebijakan-privasi` | `web` | `kebijakan-privasi` |
| `GET` | `/verifikasi-dokumen/{kode}` | `Public\VerifikasiDokumenController` | `web`, `throttle:60,1` | `dokumen.verifikasi` |
| `GET` | `/captcha/image` | `Public\CaptchaController@image` | `web`, `throttle:60,1` | `captcha.image` |
| `GET` | `/.well-known/security.txt` | Closure | `web` | `security.txt` |

---

### 3.2 Rute Autentikasi & Reset Password
| Method | URI | Controller Action | Middleware | Nama Rute |
|---|---|---|---|---|
| `GET` | `/login` | `Auth\AuthController@showLogin` | `guest` | `login` |
| `POST` | `/login` | `Auth\AuthController@login` | `guest`, `throttle:login` | - |
| `GET` | `/register` | `Auth\AuthController@showRegister` | `guest` | `register` |
| `POST` | `/register` | `Auth\AuthController@register` | `guest`, `throttle:pendaftaran` | - |
| `POST` | `/logout` | `Auth\AuthController@logout` | `auth` | `logout` |
| `GET` | `/auth/{provider}/redirect` | `Auth\SocialiteController@redirect` | `guest` | `oauth.redirect` |
| `GET` | `/auth/{provider}/callback` | `Auth\SocialiteController@callback` | `guest` | `oauth.callback` |
| `GET` | `/forgot-password` | `Auth\PasswordController@showForgot` | `guest` | `password.request` |
| `POST` | `/forgot-password` | `Auth\PasswordController@sendResetLink` | `guest`, `throttle:6,1` | `password.email` |
| `GET` | `/reset-password/{token}` | `Auth\ResetPasswordController@showReset` | `guest` | `password.reset` |
| `POST` | `/reset-password` | `Auth\ResetPasswordController@reset` | `guest` | `password.update` |

---

### 3.3 Rute Dokumen Privat & Realtime
| Method | URI | Controller Action | Middleware | Deskripsi |
|---|---|---|---|---|
| `GET` | `/realtime/stream` | `RealtimeStreamController@stream` | `auth`, `EnsureAccountActive` | Aliran SSE (RFC 8895) satu arah per pengguna. |
| `GET` | `/berkas/signed/{id}` | `Peserta\BerkasController@download` | `auth`, `signed` (TTL 5 menit) | Unduhan berkas persyaratan privat bertanda tangan HMAC. |
| `GET` | `/berkas/tugas/{id}` | `Peserta\BerkasController@downloadTugas` | `auth`, `signed` (TTL 5 menit) | Unduhan berkas hasil tugas mentee berproteksi Policy. |

---

### 3.4 Rute Modul Peserta (`/peserta/*`)
Semua rute di bawah ini dilindungi oleh middleware `auth` dan `role:peserta`.

| Method | URI | Controller Action | Deskripsi Bisnis |
|---|---|---|---|
| `GET` | `/peserta/dashboard` | `Peserta\DashboardController@index` | Dashboard Bento Grid status peserta. |
| `GET` | `/peserta/pendaftaran` | `Peserta\PendaftaranController@index` | Wizard formulir pendaftaran magang. |
| `POST` | `/peserta/pendaftaran` | `Peserta\PendaftaranController@store` | Simpan draf pendaftaran dan unggah berkas. |
| `POST` | `/peserta/pendaftaran/{id}/ajukan` | `Peserta\PendaftaranController@ajukan` | Penguncian kuota atomik dan pengajuan berkas. |
| `POST` | `/peserta/pendaftaran/{id}/batal` | `Peserta\PendaftaranController@batal` | Pembatalan permohonan mandiri. |
| `GET` | `/peserta/profil` | `Peserta\ProfilController@show` | Antarmuka biodata dan data instansi. |
| `POST` | `/peserta/profil` | `Peserta\ProfilController@update` | Pembaruan data pribadi peserta. |
| `GET` | `/peserta/logbook` | `Peserta\LogbookController@index` | Riwayat logbook presensi harian. |
| `POST` | `/peserta/logbook` | `Peserta\LogbookController@store` | Pengiriman jurnal harian (maks. H-3). |
| `PUT` | `/peserta/logbook/{id}` | `Peserta\LogbookController@update` | Pembaruan logbook status `perlu_revisi`. |
| `GET` | `/peserta/tugas` | `Peserta\TugasController@index` | Daftar tugas bimbingan terstruktur. |
| `GET` | `/peserta/tugas/{id}` | `Peserta\TugasController@show` | Lembar instruksi pengerjaan tugas. |
| `POST` | `/peserta/tugas/{tugas}/progres` | `Peserta\TugasController@unggahProgres` | Penyerahan berkas/tautan laporan hasil tugas. |
| `POST` | `/peserta/tugas/{tugas}/batal-kirim` | `Peserta\TugasController@batalkanPengiriman` | **Unsubmit**: Batalkan pengiriman sebelum direview (BR-18). |
| `GET` | `/peserta/selesai-magang` | `Peserta\SelesaiMagangController@index` | Halaman status selesai magang & pratinjau surat selesai. |
| `GET` | `/peserta/selesai-magang/unduh` | `Peserta\SelesaiMagangController@unduh` | Unduhan Surat Keterangan Selesai Magang. |
| `GET` | `/peserta/pengaturan` | `Peserta\PengaturanController@index` | Pengaturan akun, sandi, preferensi bahasa/tema, dan 2FA. |

---

### 3.5 Rute Modul Pembimbing (`/pembimbing/*`)
Dilindungi oleh middleware `auth` dan `role:pembimbing`.

| Method | URI | Controller Action | Deskripsi Bisnis |
|---|---|---|---|
| `GET` | `/pembimbing/dashboard` | `Pembimbing\DashboardController@index` | Dashboard metrik pembimbing ASN. |
| `GET` | `/pembimbing/peserta` | `Pembimbing\PesertaController@index` | Daftar mentee aktif binaan bidang. |
| `GET` | `/pembimbing/peserta/{id}` | `Pembimbing\PesertaController@show` | **Profil 360 Mentee**: Rekapitulasi lengkap individu. |
| `POST` | `/pembimbing/peserta/{id}/selesai` | `Pembimbing\PesertaController@selesai` | Penetapan penyelesaian magang 1-klik (BR-11). |
| `GET` | `/pembimbing/tugas` | `Pembimbing\TugasController@index` | Manajemen tugas bimbingan (individu & tim). |
| `GET` | `/pembimbing/tugas/create` | `Pembimbing\TugasController@create` | Formulir penerbitan tugas individu/batch. |
| `POST` | `/pembimbing/tugas` | `Pembimbing\TugasController@store` | Pembuatan tugas individu/batch mentee. |
| `GET` | `/pembimbing/tugas/kelompok/{kelompok}` | `Pembimbing\TugasController@kelompokShow` | Detail tugas & progres kelompok tim. |
| `GET` | `/pembimbing/tugas/kelompok/{kelompok}/project/create` | `Pembimbing\TugasController@createProjectBersama` | Formulir pembuatan project bersama tim. |
| `POST` | `/pembimbing/tugas/kelompok/{kelompok}/project` | `Pembimbing\TugasController@storeProjectBersama` | Penyimpanan project bersama tim. |
| `PUT` | `/pembimbing/tugas/{tugas}/project-assignment` | `Pembimbing\TugasController@updateProjectAssignment` | Pembaruan penugasan anggota dalam project tim. |
| `POST` | `/pembimbing/progres/{progres}/review` | `Pembimbing\TugasController@reviewProgres` | Koreksi dan review laporan tugas mentee. |
| `GET` | `/pembimbing/logbook` | `Pembimbing\LogbookReviewController@index` | Antrean review logbook kegiatan harian. |
| `POST` | `/pembimbing/logbook/{id}/review` | `Pembimbing\LogbookReviewController@review` | Persetujuan / catatan revisi logbook tunggal. |
| `POST` | `/pembimbing/logbook/bulk-review` | `Pembimbing\LogbookReviewController@bulkReview` | Persetujuan logbook massal (*bulk approval*). |
| `GET` | `/pembimbing/laporan-kinerja` | `Pembimbing\LaporanKinerjaController@index` | Rekapitulasi capaian mentoring. |
| `GET` | `/pembimbing/laporan-kinerja/export/xlsx` | `Pembimbing\LaporanKinerjaController@exportXlsx` | Ekspor Excel OpenXML terproteksi formula injection. |
| `GET` | `/pembimbing/laporan-kinerja/export/csv` | `Pembimbing\LaporanKinerjaController@exportCsv` | Ekspor CSV kinerja mentoring. |
| `GET` | `/pembimbing/laporan-kinerja/export/pdf` | `Pembimbing\LaporanKinerjaController@exportPdf` | Unduhan berkas PDF portofolio kinerja resmi. |

---

### 3.6 Rute Modul Admin Verifikator (`/admin/*`)
Dilindungi oleh middleware `auth` dan `role:admin`.

| Method | URI | Controller Action | Deskripsi Bisnis |
|---|---|---|---|
| `GET` | `/admin/dashboard` | `Admin\DashboardController@index` | Dashboard verifikasi & SLA monitor. |
| `GET` | `/admin/pendaftaran` | `Admin\PendaftaranController@index` | Antrean seleksi berkas pendaftar (SLA Watchdog). |
| `GET` | `/admin/pendaftaran/{id}` | `Admin\PendaftaranController@show` | Detail berkas dan riwayat verifikasi pemohon. |
| `POST` | `/admin/pendaftaran/{id}/putuskan` | `Admin\PendaftaranController@putuskan` | Keputusan seleksi (Disetujui/Perbaikan/Ditolak). |
| `GET` | `/admin/pendaftaran/{id}/unduh-zip` | `Admin\PendaftaranController@downloadZip` | Pengunduhan arsip bundel berkas ZIP pendaftar. |
| `GET` | `/admin/penempatan` | `Admin\PenempatanController@index` | Antrean penempatan dua tahap & plotting pembimbing. |
| `POST` | `/admin/penempatan` | `Admin\PenempatanController@store` | Penetapan bidang dan pembimbing mentee (BR-07). |
| `POST` | `/admin/penempatan/reassign` | `Admin\PenempatanController@reassign` | Pemindahan mentee ke pembimbing lain. |
| `POST` | `/admin/penempatan/bulk-reassign`| `Admin\PenempatanController@bulkReassign` | Pemindahan seluruh mentee bimbingan massal. |
| `GET` | `/admin/kuota` | `Admin\KuotaController@index` | Kontrol kuota formasi bulanan, bidang, dan status buka/tutup. |
| `POST` | `/admin/kuota` | `Admin\KuotaController@store` | Pembuatan alokasi kuota jurusan baru. |
| `PUT` | `/admin/kuota/{kuota}` | `Admin\KuotaController@update` | Pembaruan kapasitas kuota. |
| `GET` | `/admin/bidang` | `Admin\BidangController@index` | Master data bidang teknis dinas. |
| `GET` | `/admin/bulan` | `Admin\BulanController@index` | Master data periode bulan magang. |
| `GET` | `/admin/jurusan` | `Admin\JurusanController@index` | Master data jurusan / keahlian. |
| `GET` | `/admin/slot-khusus` | `Admin\SlotKhususController@index` | Alokasi slot kemitraan diskresi dinas. |
| `GET` | `/admin/peserta-khusus/create` | `Admin\PesertaJalurKhususController@create` | Registrasi langsung peserta jalur khusus instansi. |
| `GET` | `/admin/surat-selesai` | `Admin\SuratSelesaiController@index` | Manajemen distribusi Surat Selesai Magang (Individu & Kelompok). |
| `POST` | `/admin/penempatan/{penempatan}/surat-selesai` | `Admin\PenempatanController@uploadSuratSelesai` | Unggah berkas Surat Selesai Magang. |
| `DELETE` | `/admin/penempatan/{penempatan}/surat-selesai` | `Admin\PenempatanController@hapusSuratSelesai` | Hapus berkas Surat Selesai Magang. |
| `GET` | `/admin/penempatan/{penempatan}/surat-selesai` | `Admin\PenempatanController@unduhSuratSelesai` | Unduh berkas Surat Selesai Magang. |
| `GET` | `/admin/arsip-peserta` | `Admin\ArsipPesertaController@index` | Arsip peserta pasca-magang (cakupan Administrasi). |
| `GET` | `/admin/arsip-peserta/{penempatan}` | `Admin\ArsipPesertaController@show` | Detail arsip peserta pasca-magang. |
| `GET` | `/admin/laporan` | `Admin\LaporanController@index` | Pelaporan dan ekspor rekapitulasi kedinasan. |

---

### 3.7 Rute Modul SuperAdmin (`/superadmin/*`)
Dilindungi oleh middleware `auth`, `role:superadmin`, dan `EnsureSuperAdminReadOnly`.

| Method | URI | Controller Action | Deskripsi Bisnis |
|---|---|---|---|
| `GET` | `/superadmin/dashboard` | `SuperAdmin\DashboardController@index` | Cockpit telemetri dan kontrol eksekutif. |
| `POST` | `/superadmin/dashboard/toggle-kran` | `SuperAdmin\DashboardController@toggleKran` | Sakelar kontrol darurat kran kuota publik (`override_status`). |
| `GET` | `/superadmin/pengaturan` | `SuperAdmin\PengaturanController@index` | Tata kelola parameter operasional dan konten landing page. |
| `PUT` | `/superadmin/pengaturan` | `SuperAdmin\PengaturanController@update` | Pembaruan konfigurasi parameter operasional dan berkas media landing page. |
| `POST` | `/superadmin/pengaturan/reset-hero-image` | `SuperAdmin\PengaturanController@resetHeroImage` | Reset foto hero banner landing page ke default Diskominfo Sleman. |
| `POST` | `/superadmin/pengaturan/reset-panduan-pdf` | `SuperAdmin\PengaturanController@resetPanduanPdf` | Reset berkas panduan PDF alur ke generator PDF otomatis sistem. |
| `GET` | `/superadmin/role` | `SuperAdmin\RoleController@index` | **Dynamic RBAC Matrix**: Matriks izin 13 modul sistem. |
| `POST` | `/superadmin/role/{role}/toggle` | `SuperAdmin\RoleController@togglePermission` | Sakelar izin akses modul per peran. |
| `GET` | `/superadmin/staf` | `SuperAdmin\StafController@index` | Manajemen akun staf Admin dan Pembimbing. |
| `POST` | `/superadmin/staf` | `SuperAdmin\StafController@store` | Pembuatan akun staf dengan kredensial acak awal. |
| `POST` | `/superadmin/staf/{id}/reset-password` | `SuperAdmin\StafController@resetPassword` | Regenerasi kata sandi staf instan. |
| `GET` | `/superadmin/slot-khusus` | `RedirectController` → `/admin/slot-khusus` | Redirect kompatibilitas ke modul Administrasi. |
| `GET` | `/superadmin/peserta-khusus/create` | `RedirectController` → `/admin/peserta-khusus/create` | Redirect kompatibilitas ke modul Administrasi. |
| `GET` | `/superadmin/bidang`, `bulan`, `jurusan` | `RedirectController` → `/admin/*` | Redirect kompatibilitas master data ke modul Administrasi. |
| `GET` | `/superadmin/audit-log` | `SuperAdmin\AuditLogController@index` | Penelusuran forensik audit trail 4 kanal. |
| `GET` | `/superadmin/audit-log/export` | `SuperAdmin\AuditLogController@export` | Ekspor rekam jejak audit ke CSV/Excel. |
| `GET` | `/superadmin/backup` | `SuperAdmin\BackupController@index` | Pusat pencadangan & pemulihan basis data. |
| `POST` | `/superadmin/backup/create`| `SuperAdmin\BackupController@create` | Pembuatan snapshot database `.sql.gz` on-the-fly. |
| `POST` | `/superadmin/backup/{file}/restore` | `SuperAdmin\BackupController@restore` | Prosedur pemulihan (*disaster recovery restore*). |
| `GET` | `/superadmin/site-editor` | `SuperAdmin\LandingCmsSiteEditorController@index` | Visual Site Editor blok landing page publik. |
| `POST` | `/superadmin/site-editor/publish` | `SuperAdmin\LandingCmsSiteEditorController@publish` | Publikasi revisi tata letak portal publik. |
| `GET` | `/superadmin/arsip-peserta` | `SuperAdmin\ArsipPesertaController@index` | Gudang arsip permanen peserta pasca-magang (Read-Only). |
| `GET` | `/superadmin/keamanan/2fa` | `Auth\TwoFactorAuthController@showSettings` | Pengaturan Two-Factor Authentication (TOTP). |
