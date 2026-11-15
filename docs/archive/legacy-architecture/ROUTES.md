# Peta Rute dan Endpoint Aplikasi MAGKOM

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | 211 Endpoint HTTP, metode request, middleware guard, controller mapping |
| Referensi Implementasi | `routes/web.php`, `app/Http/Controllers/` |

---

## 1. Pendahuluan

Seluruh rute HTTP pada sistem MAGKOM didefinisikan secara terpusat pada berkas `routes/web.php`. Total terdapat **211 rute terdaftar** yang dikelompokkan ke dalam domain wewenang terpisah dengan perlindungan middleware berlapis.

---

## 2. Struktur Pengelompokan Rute Berdasarkan Prefiks

| Prefiks URI | Jumlah Endpoint | Sasaran Pengguna | Middleware Utama |
|---|---|---|---|
| `/` (Root & Publik) | 9 | Tamu / Publik | `web`, `throttle` |
| `/login`, `/register`, `/auth` | 10 | Pengunjung Belum Terotentikasi | `guest`, `throttle:login` |
| `/realtime/*` | 3 | Pengguna Terotentikasi | `auth`, `EnsureAccountActive` |
| `/berkas/*` | 2 | Pengguna Terotorisasi Berkas | `auth`, `signed`, Laravel Policies |
| `/peserta/*` | 26 | Peserta Magang | `auth`, `role:peserta`, `EnsureParticipantWritable` |
| `/pembimbing/*` | 29 | Pembimbing Lapangan ASN | `auth`, `role:pembimbing` |
| `/admin/*` | 32 | Admin Verifikator Kominfo | `auth`, `role:admin` |
| `/superadmin/*` | 88 | SuperAdmin Sistem | `auth`, `role:superadmin`, `EnsureSuperAdminReadOnly` |

---

## 3. Rincian Rute per Domain

### 3.1 Rute Publik & Utilitas
| Method | URI | Controller Action | Middleware | Nama Rute |
|---|---|---|---|---|
| `GET` | `/` | `Public\LandingController` | `web` | `landing` |
| `GET` | `/panduan-alur` | `Public\AlurMagangController` | `web` | `panduan-alur` |
| `GET` | `/peta-alur` | `Public\AlurMagangController@petaInteraktif` | `web` | `peta-alur` |
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
| `POST` | `/peserta/tugas/{id}/submit` | `Peserta\TugasController@submit` | Penyerahan berkas laporan hasil tugas. |
| `POST` | `/peserta/tugas/{id}/unsubmit` | `Peserta\TugasController@unsubmit` | **Unsubmit**: Batalkan pengiriman sebelum dinilai (BR-18). |

---

### 3.5 Rute Modul Pembimbing (`/pembimbing/*`)
Dilindungi oleh middleware `auth` dan `role:pembimbing`.

| Method | URI | Controller Action | Deskripsi Bisnis |
|---|---|---|---|
| `GET` | `/pembimbing/dashboard` | `Pembimbing\DashboardController@index` | Dashboard metrik pembimbing ASN. |
| `GET` | `/pembimbing/peserta` | `Pembimbing\PesertaController@index` | Daftar mentee aktif binaan bidang. |
| `GET` | `/pembimbing/peserta/{id}` | `Pembimbing\PesertaController@show` | **Profil 360 Mentee**: Rekapitulasi lengkap individu. |
| `POST` | `/pembimbing/peserta/{id}/selesai` | `Pembimbing\PesertaController@selesai` | Penetapan penyelesaian magang 1-klik (BR-11). |
| `GET` | `/pembimbing/tugas` | `Pembimbing\TugasController@index` | Manajemen tugas bimbingan. |
| `GET` | `/pembimbing/tugas/create` | `Pembimbing\TugasController@create` | Formulir penerbitan tugas. |
| `POST` | `/pembimbing/tugas` | `Pembimbing\TugasController@store` | Pembuatan tugas tunggal mentee. |
| `POST` | `/pembimbing/tugas/batch` | `Pembimbing\TugasController@storeBatch` | **Batch Classroom**: Penugasan serentak multi-mentee (BR-19). |
| `POST` | `/pembimbing/tugas/progres/{id}/review` | `Pembimbing\TugasController@reviewProgres` | Koreksi dan penilaian laporan tugas mentee. |
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
| `GET` | `/admin/kuota` | `Admin\KuotaController@index` | Kontrol kuota formasi bulanan dan status buka/tutup. |
| `POST` | `/admin/kuota` | `Admin\KuotaController@store` | Pembuatan alokasi kuota jurusan baru. |
| `PUT` | `/admin/kuota/{id}` | `Admin\KuotaController@update` | Pembaruan kapasitas kuota. |
| `GET` | `/admin/laporan` | `Admin\LaporanController@index` | Pelaporan dan ekspor rekapitulasi kedinasan. |

---

### 3.7 Rute Modul SuperAdmin (`/superadmin/*`)
Dilindungi oleh middleware `auth`, `role:superadmin`, dan `EnsureSuperAdminReadOnly`.

| Method | URI | Controller Action | Deskripsi Bisnis |
|---|---|---|---|
| `GET` | `/superadmin/dashboard` | `SuperAdmin\DashboardController@index` | Cockpit telemetri dan kontrol eksekutif. |
| `GET` | `/superadmin/pengaturan` | `SuperAdmin\PengaturanController@index` | Tata kelola 12 parameter operasional sistem. |
| `PUT` | `/superadmin/pengaturan` | `SuperAdmin\PengaturanController@update` | Pembaruan konfigurasi parameter operasional. |
| `GET` | `/superadmin/role` | `SuperAdmin\RoleController@index` | **Dynamic RBAC Matrix**: Matriks izin 13 modul sistem. |
| `POST` | `/superadmin/role/{role}/toggle` | `SuperAdmin\RoleController@togglePermission` | Sakelar izin akses modul per peran. |
| `GET` | `/superadmin/staf` | `SuperAdmin\StafController@index` | Manajemen akun staf Admin dan Pembimbing. |
| `POST` | `/superadmin/staf` | `SuperAdmin\StafController@store` | Pembuatan akun staf dengan kredensial acak awal. |
| `POST` | `/superadmin/staf/{id}/reset-password` | `SuperAdmin\StafController@resetPassword` | Regenerasi kata sandi staf instan. |
| `GET` | `/superadmin/slot-khusus` | `SuperAdmin\SlotKhususController@index` | Alokasi slot kemitraan diskresi dinas. |
| `POST` | `/superadmin/peserta-khusus`| `SuperAdmin\PesertaJalurKhususController@store`| Registrasi langsung peserta jalur khusus instansi. |
| `GET` | `/superadmin/audit-log` | `SuperAdmin\AuditLogController@index` | Penelusuran forensik audit trail 4 kanal. |
| `GET` | `/superadmin/audit-log/export` | `SuperAdmin\AuditLogController@export` | Ekspor rekam jejak audit ke CSV/Excel. |
| `GET` | `/superadmin/backup` | `SuperAdmin\BackupController@index` | Pusat pencadangan & pemulihan basis data. |
| `POST` | `/superadmin/backup/create`| `SuperAdmin\BackupController@create` | Pembuatan snapshot database `.sql.gz` on-the-fly. |
| `POST` | `/superadmin/backup/{file}/restore` | `SuperAdmin\BackupController@restore` | Prosedur pemulihan (*disaster recovery restore*). |
| `GET` | `/superadmin/landing-cms` | `SuperAdmin\LandingCmsController@index` | Manajemen editor blok visual landing page publik. |
| `POST` | `/superadmin/landing-cms/publish` | `SuperAdmin\LandingCmsController@publish` | Publikasi revisi tata letak portal publik. |
| `GET` | `/superadmin/arsip` | `SuperAdmin\ArsipPesertaController@index` | Gudang arsip permanen peserta pasca-magang. |
