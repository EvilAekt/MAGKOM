> Diperbarui: 2026-11-02 | audit: Task-DOC-004 | status: Synced

# Peta Rute & Endpoint (ROUTES.md)

Dokumen ini memetakan seluruh rute pada `routes/web.php` dan `routes/api.php`, lengkap dengan middleware proteksi, layout pembungkus, controller pemroses, dan sumber data utamanya.

---

## 1. Diagram Peta Navigasi & Segmentasi Hak Akses

```mermaid
graph LR
    subgraph PublicZone ["Publik & Guest Zone"]
        R_Landing["/ (Landing)"]
        R_Health["/health"]
        R_Privasi["/kebijakan-privasi"]
        R_PubModul["/publik/modul/{slug}"]
        R_Login["/login"]
        R_Register["/register (Mandiri & Kelompok)"]
        R_Oauth["/auth/{provider}/*"]
        R_VerifDoc["/verifikasi-dokumen/{kode}"]
    end

    subgraph PesertaZone ["Area Peserta (/peserta/*)"]
        P_Dash["/peserta/dashboard"]
        P_Profil["/peserta/profil (Roster Kelompok)"]
        P_Daftar["/peserta/pendaftaran"]
        P_Tugas["/peserta/tugas"]
        P_Logbook["/peserta/logbook (Mingguan)"]
        P_Nilai["/peserta/nilai"]
        P_Survei["/peserta/survei"]
    end

    subgraph PembimbingZone ["Area Pembimbing (/pembimbing/*)"]
        M_Dash["/pembimbing/dashboard"]
        M_Peserta["/pembimbing/peserta"]
        M_Tugas["/pembimbing/tugas"]
        M_Logbook["/pembimbing/logbook (Mingguan)"]
        M_Nilai["/pembimbing/penilaian"]
        M_Agenda["/pembimbing/agenda"]
        M_Kinerja["/pembimbing/laporan-kinerja"]
    end

    subgraph AdminZone ["Area Administrator (/admin/*)"]
        A_Dash["/admin/dashboard"]
        A_Kuota["/admin/kuota"]
        A_Pendaftaran["/admin/pendaftaran"]
        A_Wait["/admin/waiting-list"]
        A_Plot["/admin/penempatan"]
        A_Lapor["/admin/laporan"]
        A_Cert["/admin/sertifikat"]
    end

    subgraph SuperAdminZone ["Area SuperAdmin (/superadmin/*)"]
        SA_Dash["/superadmin/dashboard"]
        SA_Staf["/superadmin/staf"]
        SA_Master["/superadmin/jurusan & bidang"]
        SA_Builder["/superadmin/modul-builder"]
        SA_DynMod["/superadmin/modul/{slug}"]
        SA_Audit["/superadmin/audit-log"]
        SA_Backup["/superadmin/backup"]
        SA_Health["/superadmin/health-monitor"]
    end

    subgraph CustomZone ["Universal Modul (/kustom-modul/*)"]
        K_Modul["/kustom-modul/{slug} (RBAC Granular)"]
    end

    PublicZone -->|Login sebagai Peserta| PesertaZone
    PublicZone -->|Login sebagai Pembimbing| PembimbingZone
    PublicZone -->|Login sebagai Admin| AdminZone
    PublicZone -->|Login sebagai SuperAdmin| SuperAdminZone
    PesertaZone -.->|Akses Modul Diizinkan| CustomZone
    PembimbingZone -.->|Akses Modul Diizinkan| CustomZone
    AdminZone -.->|Akses Modul Diizinkan| CustomZone
    SuperAdminZone -->|Kelola & Akses Penuh| CustomZone
```

---

## 2. Tabel Rute Lengkap

### A. Rute Publik & Tamu (Guest)
| Method | URI | Controller / Action | Layout View | Data Source / Model | Middleware / Auth |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/` | `Public\LandingController` | `layouts.app` | `BulanMagang`, `Kuota`, `Jurusan` | None |
| `GET` | `/health` | `Public\HealthController` | JSON / Plain | DB connection check | None |
| `GET` | `/kebijakan-privasi` | View `public.kebijakan-privasi` | `layouts.app` | Static Content | None |
| `GET` | `/.well-known/security.txt`| Closure | Plain text | `public/.well-known/security.txt` | None |
| `GET` | `/verifikasi-dokumen/{kode}`| `Public\VerifikasiDokumenController`| `layouts.app` | `DokumenVerifikasi`, `Penempatan` | `throttle:60,1` |
| `GET` | `/publik/modul/{slug}` | `PublicModulController@index` | `layouts.public-modul` | `ModulDinamis`, `ModulDinamisData` | None (`is_public_read=true`) |
| `GET` | `/publik/modul/{slug}/{id}` | `PublicModulController@show` | `layouts.public-modul` | `ModulDinamis`, `ModulDinamisData` | None (`is_public_read=true`) |
| `GET` | `/login` | `Auth\AuthController@showLogin` | `layouts.auth` | - | `guest` |
| `POST`| `/login` | `Auth\AuthController@login` | - | `User` | `guest`, `throttle:login` |
| `GET` | `/register` | `Auth\AuthController@showRegister`| `layouts.auth`| `Jurusan` | `guest` |
| `POST`| `/register` | `Auth\AuthController@register` | - | `User`, `PesertaProfil`, `KelompokMagang` | `guest`, `throttle:pendaftaran` *(mandiri & kelompok)* |
| `GET` | `/auth/{provider}/redirect`| `Auth\SocialiteController@redirect`| - | Google OAuth API | `guest` |
| `GET` | `/auth/{provider}/callback`| `Auth\SocialiteController@callback`| - | `User`, Google OAuth API | `guest` |
| `GET` | `/forgot-password` | `Auth\ResetPasswordController@showForgot`| `layouts.auth`| - | `guest` |
| `POST`| `/forgot-password` | `Auth\ResetPasswordController@sendResetLink`| - | Password broker | `guest`, `throttle:5,1` |
| `GET` | `/reset-password/{token}` | `Auth\ResetPasswordController@showReset`| `layouts.auth`| - | `guest` |
| `POST`| `/reset-password` | `Auth\ResetPasswordController@reset`| - | `User` | `guest` |

### B. Rute Berkas Privat & Verifikasi Akun
| Method | URI | Controller / Action | Layout View | Data Source / Model | Middleware / Auth |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `POST`| `/logout` | `Auth\AuthController@logout` | - | Session purge | `auth` |
| `GET` | `/email/verify` | `Auth\VerificationController@notice` | `layouts.auth` | `User` | `auth` |
| `GET` | `/email/verify/{id}/{hash}`| `Auth\VerificationController@verify` | - | `User` | `auth`, `signed`, `throttle:6,1`|
| `POST`| `/email/verification-notification`| `Auth\VerificationController@resend` | - | Mail notification | `auth`, `throttle:6,1` |
| `GET` | `/password/ganti` | `Auth\PasswordController@showGanti` | `layouts.auth` | `User` | `auth` |
| `POST`| `/password/ganti` | `Auth\PasswordController@updateGanti` | - | `User` | `auth` |
| `GET` | `/berkas/{dokumen}/download`| `Peserta\BerkasController@unduh` | File Stream | `Dokumen`, `DocumentStorageService`| `auth`, `signed:relative` |
| `GET` | `/berkas/{dokumen}/pratinjau`| `Peserta\BerkasController@pratinjau`| File Stream | `Dokumen`, `DocumentStorageService`| `auth`, `signed:relative` |

### C. Rute Portal Peserta (`prefix: peserta`)
*Middleware Terapan*: `['auth', 'verified', 'must_change_password', 'role:peserta']`

| Method | URI | Controller / Action | Layout View | Data Source / Model |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/peserta/dashboard` | `Peserta\DashboardController@index` | `layouts.peserta` | `Pendaftaran`, `Penempatan`, `Tugas`, `Logbook` |
| `GET` | `/peserta/profil` | `Peserta\ProfilController@show` | `layouts.peserta` | `User`, `PesertaProfil`, `Jurusan`, `KelompokMagang` |
| `POST`| `/peserta/profil` | `Peserta\ProfilController@simpan` | - | `User`, `PesertaProfil` |
| `GET` | `/peserta/pengaturan` | `Peserta\PengaturanController@index` | `layouts.peserta` | `User` |
| `PUT` | `/peserta/pengaturan/password`| `Peserta\PengaturanController@updatePassword`| - | `User` |
| `POST`| `/peserta/pengaturan/preferensi`| `Peserta\PengaturanController@updatePreferensi`| - | `User` |
| `GET` | `/peserta/pendaftaran` | `Peserta\PendaftaranController@index` | `layouts.peserta` | `BulanMagang`, `Pendaftaran`, `Kuota`, `KelompokMagang` |
| `POST`| `/peserta/pendaftaran` | `Peserta\PendaftaranController@store` | - | `Pendaftaran`, `PlacementService` |
| `GET` | `/peserta/pendaftaran/{id}` | `Peserta\PendaftaranController@detail`| `layouts.peserta` | `Pendaftaran`, `Dokumen` |
| `POST`| `/peserta/pendaftaran/{id}/ajukan`| `Peserta\PendaftaranController@ajukan`| - | `Pendaftaran` |
| `POST`| `/peserta/pendaftaran/{id}/batalkan`| `Peserta\PendaftaranController@batalkan`| - | `Pendaftaran` |
| `POST`| `/peserta/pendaftaran/{id}/berkas`| `Peserta\BerkasController@unggah` | - | `Dokumen`, Storage |
| `GET` | `/peserta/penempatan/{id}/surat-diterima`| `Peserta\PendaftaranController@unduhSuratDiterima`| PDF stream | `SuratDiterimaPdfService` |
| `GET` | `/peserta/tugas` | `Peserta\TugasController@index` | `layouts.peserta` | `Tugas`, `ProgresLog` *(Khusus peserta diterima)* |
| `GET` | `/peserta/tugas/{id}` | `Peserta\TugasController@show` | `layouts.peserta` | `Tugas`, `ProgresLog` |
| `POST`| `/peserta/tugas/{id}/progres`| `Peserta\TugasController@unggahProgres`| - | `ProgresLog` |
| `GET` | `/peserta/logbook` | `Peserta\LogbookController@index` | `layouts.peserta` | `Logbook` *(Mingguan)* |
| `POST`| `/peserta/logbook` | `Peserta\LogbookController@store` | - | `Logbook` *(Mingguan)* |
| `PUT` | `/peserta/logbook/{id}`| `Peserta\LogbookController@update` | - | `Logbook` *(Mingguan)* |
| `GET` | `/peserta/nilai` | `Peserta\NilaiController@show` | `layouts.peserta` | `Penilaian`, `PenilaianDetail` |
| `GET` | `/peserta/nilai/unduh`| `Peserta\NilaiController@unduhPdf` | PDF stream | `Penilaian`, DomPDF |
| `GET` | `/peserta/survei` | `Peserta\SurveiController@show` | `layouts.peserta` | `SurveiKepuasan` |
| `POST`| `/peserta/survei` | `Peserta\SurveiController@store` | - | `SurveiKepuasan` |

### D. Rute Portal Pembimbing (`prefix: pembimbing`)
*Middleware Terapan*: `['auth', 'verified', 'must_change_password', 'account.active', 'role:pembimbing,admin,superadmin']`

| Method | URI | Controller / Action | Layout View | Data Source / Model |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/pembimbing/dashboard` | `Pembimbing\DashboardController@index` | `layouts.dashboard` | `PembimbingDashboardMetricsService` |
| `GET` | `/pembimbing/peserta` | `Pembimbing\PesertaController@index` | `layouts.dashboard` | `Penempatan`, `User` |
| `GET` | `/pembimbing/peserta/{id}`| `Pembimbing\PesertaController@show` | `layouts.dashboard` | `Penempatan`, `Logbook`, `Tugas` |
| `GET` | `/pembimbing/tugas/template`| `Pembimbing\TugasTemplateController@index`| `layouts.dashboard`| `TugasTemplate` |
| `POST`| `/pembimbing/tugas/template`| `Pembimbing\TugasTemplateController@store`| - | `TugasTemplate` |
| `GET` | `/pembimbing/tugas/template-picker`| `Pembimbing\TugasTemplateController@pickerJson`| JSON | `TugasTemplate` |
| `GET` | `/pembimbing/tugas` | `Pembimbing\TugasController@index` | `layouts.dashboard` | `Tugas` |
| `POST`| `/pembimbing/tugas` | `Pembimbing\TugasController@store` | - | `Tugas` |
| `POST`| `/pembimbing/progres/{id}/review`| `Pembimbing\TugasController@reviewProgres`| - | `ProgresLog`, `Tugas` |
| `GET` | `/pembimbing/logbook` | `Pembimbing\LogbookReviewController@index`| `layouts.dashboard`| `Logbook`, `Penempatan` |
| `POST`| `/pembimbing/logbook/bulk-review`| `Pembimbing\LogbookReviewController@bulkReview`| - | `Logbook` |
| `GET` | `/pembimbing/penilaian` | `Pembimbing\PenilaianController@index` | `layouts.dashboard` | `Penempatan`, `Penilaian` |
| `POST`| `/pembimbing/penilaian/{id}`| `Pembimbing\PenilaianController@store` | - | `Penilaian`, `ScoringService` |
| `GET` | `/pembimbing/agenda` | `Pembimbing\AgendaBimbinganController@index`| `layouts.dashboard`| `AgendaBimbingan` |
| `GET` | `/pembimbing/laporan-kinerja`| `Pembimbing\LaporanKinerjaController@index`| `layouts.dashboard`| Mentoring analytics |

### E. Rute Portal Administrator (`prefix: admin`)
*Middleware Terapan*: `['auth', 'verified', 'must_change_password', 'role:admin,superadmin']`

| Method | URI | Controller / Action | Layout View | Data Source / Model |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/admin/dashboard` | `Admin\DashboardController@index` | `layouts.dashboard` | `AdminDashboardMetricsService` |
| `GET` | `/admin/kuota` | `Admin\KuotaController@index` | `layouts.dashboard` | `Kuota`, `BulanMagang`, `Jurusan` |
| `POST`| `/admin/kuota` | `Admin\KuotaController@store` | - | `Kuota`, `AuditService` |
| `GET` | `/admin/pendaftaran` | `Admin\PendaftaranController@index` | `layouts.dashboard` | `Pendaftaran`, `PesertaProfil` |
| `GET` | `/admin/pendaftaran/download-zip`| `Admin\PendaftaranController@downloadBundelZip`| ZIP stream | `ZipArchive`, `Dokumen` |
| `POST`| `/admin/pendaftaran/{id}/putuskan`| `Admin\PendaftaranController@putuskan`| - | `Pendaftaran`, `PlacementService` |
| `GET` | `/admin/waiting-list` | `Admin\WaitingListController@index` | `layouts.dashboard` | `Pendaftaran` (status: waiting_list) |
| `POST`| `/admin/waiting-list/promote`| `Admin\WaitingListController@promoteFifo`| - | `PlacementService`, `AuditService` |
| `GET` | `/admin/penempatan` | `Admin\PenempatanController@index` | `layouts.dashboard` | `Penempatan`, `User` |
| `POST`| `/admin/penempatan/bulk-reassign`| `Admin\PenempatanController@bulkReassignPembimbing`| - | `Penempatan` |
| `GET` | `/admin/laporan` | `Admin\LaporanController@index` | `layouts.dashboard` | `ExportReportService` |
| `GET` | `/admin/sertifikat` | `Admin\SertifikatController@index` | `layouts.dashboard` | `Penempatan`, `Penilaian` |
| `GET` | `/admin/sertifikat/{id}/cetak`| `Admin\SertifikatController@cetak` | PDF stream | `DomPDF`, QR Verification |

### F. Rute Super Administrator (`prefix: superadmin`)
*Middleware Terapan*: `['auth', 'verified', 'must_change_password', 'role:superadmin']`

| Method | URI | Controller / Action | Layout View | Data Source / Model |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/superadmin/dashboard` | `SuperAdmin\DashboardController@index`| `layouts.dashboard` | `SuperAdminExecutiveMetricsService`|
| `GET` | `/superadmin/monitoring-peserta`| `SuperAdmin\MonitoringPesertaController@index`| `layouts.dashboard`| Realtime placement tracking |
| `GET` | `/superadmin/bulan` | `SuperAdmin\BulanController@index` | `layouts.dashboard` | `BulanMagang` |
| `GET` | `/superadmin/slot-khusus` | `SuperAdmin\SlotKhususController@index`| `layouts.dashboard` | `SlotKhusus` |
| `GET` | `/superadmin/staf` | `SuperAdmin\StafController@index` | `layouts.dashboard` | `User` (role != peserta) |
| `GET` | `/superadmin/role` | `SuperAdmin\RoleController@index` | `layouts.dashboard` | RBAC Matrix |
| `GET` | `/superadmin/jurusan` | `SuperAdmin\JurusanController@index` | `layouts.dashboard` | `Jurusan` |
| `GET` | `/superadmin/bidang` | `SuperAdmin\BidangController@index` | `layouts.dashboard` | `Bidang` |
| `GET` | `/superadmin/rubrik` | `SuperAdmin\RubrikController@index` | `layouts.dashboard` | `Rubrik` |
| `GET` | `/superadmin/pengaturan` | `SuperAdmin\PengaturanController@index`| `layouts.dashboard` | `Pengaturan` |
| `GET` | `/superadmin/arsip-peserta`| `SuperAdmin\ArsipPesertaController@index`| `layouts.dashboard`| `Penempatan`, `Dokumen` |
| `GET` | `/superadmin/audit-log` | `SuperAdmin\AuditLogController@index` | `layouts.dashboard` | `AuditLog` |
| `GET` | `/superadmin/health-monitor`| `SuperAdmin\HealthMonitorController@index`| `layouts.dashboard` | Server stats, DB latency (`superadmin.health.index`) |
| `GET` | `/superadmin/health-monitor/telemetry-data`| `SuperAdmin\HealthMonitorController@telemetryData`| JSON | CPU, memory & storage realtime telemetry |
| `POST`| `/superadmin/health-monitor/security-sweep`| `SuperAdmin\HealthMonitorController@runSecuritySweep`| JSON | `SecuritySweepService` file & env integrity |
| `POST`| `/superadmin/health-monitor/optimize-db`| `SuperAdmin\HealthMonitorController@optimizeDatabase`| JSON | MySQL optimize & analyze tables |
| `POST`| `/superadmin/health-monitor/clear-cache`| `SuperAdmin\HealthMonitorController@clearSystemCache`| JSON | Artisan config & route cache purge |
| `POST`| `/superadmin/health-monitor/test-storage`| `SuperAdmin\HealthMonitorController@testStorageWrite`| JSON | Private disk write & read latency test |
| `GET` | `/superadmin/modul-builder` | `SuperAdmin\ModulBuilderController@index` | `layouts.dashboard` | `ModulDinamis` (Daftar modul low-code) |
| `GET` | `/superadmin/modul-builder/create` | `SuperAdmin\ModulBuilderController@create` | `layouts.dashboard` | Form perancang modul baru |
| `POST`| `/superadmin/modul-builder` | `SuperAdmin\ModulBuilderController@store` | - | `ModulDinamis`, `ModulDinamisField` |
| `GET` | `/superadmin/modul-builder/{modul}/edit` | `SuperAdmin\ModulBuilderController@edit` | `layouts.dashboard` | Form edit skema & atribut kolom modul |
| `PUT` | `/superadmin/modul-builder/{modul}` | `SuperAdmin\ModulBuilderController@update` | - | `ModulDinamis`, `ModulDinamisField` |
| `PATCH`| `/superadmin/modul-builder/{modul}/toggle` | `SuperAdmin\ModulBuilderController@toggleAktif` | - | Toggle status aktif modul |
| `DELETE`| `/superadmin/modul-builder/{modul}` | `SuperAdmin\ModulBuilderController@destroy` | - | Hapus skema modul (soft delete) |
| `GET` | `/superadmin/modul-builder/api/tables` | `SuperAdmin\ModulBuilderController@apiGetTables` | JSON | Reverse-engineering daftar tabel database |
| `GET` | `/superadmin/modul-builder/api/inspect`| `SuperAdmin\ModulBuilderController@apiInspectTable` | JSON | Reverse-engineering inspeksi kolom tabel |
| `GET` | `/superadmin/modul/{slug}` | `SuperAdmin\ModulDataController@index` | `layouts.dashboard` | Rekaman data modul (POV SuperAdmin) |
| `POST`| `/superadmin/modul/{slug}` | `SuperAdmin\ModulDataController@store` | - | Simpan record baru modul |
| `PUT` | `/superadmin/modul/{slug}/{id}` | `SuperAdmin\ModulDataController@update` | - | Perbarui data record modul |
| `DELETE`| `/superadmin/modul/{slug}/{id}` | `SuperAdmin\ModulDataController@destroy` | - | Hapus data record modul (soft delete) |
| `GET` | `/superadmin/modul/{slug}/export` | `SuperAdmin\ModulDataController@export` | CSV stream | Ekspor rekaman modul ke CSV |
| `GET` | `/superadmin/modul/{slug}/export-pdf` | `SuperAdmin\ModulDataController@exportPdf` | PDF stream | Ekspor laporan rekaman modul ke PDF |
| `GET` | `/superadmin/modul/{slug}/print` | `SuperAdmin\ModulDataController@printPreview` | `layouts.print` | Pratinjau cetak data modul |
| `GET` | `/superadmin/modul/{slug}/file/{id}/{field}` | `SuperAdmin\ModulDataController@downloadFile` | File stream | Unduh lampiran file field modul dinamis |
| `GET` | `/superadmin/backup` | `SuperAdmin\BackupController@index` | `layouts.dashboard` | Database backup files |

### G. Rute Universal Modul Kustom (`prefix: kustom-modul`)
*Middleware Terapan*: `['auth', 'verified', 'must_change_password', 'account.active']`  
*Otorisasi*: Berdasarkan `target_roles` dan matriks sub-aksi `izin_aksi` (`list`, `create`, `read`, `update`, `delete`).

| Method | URI | Controller / Action | Layout View | Data Source / Model |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/kustom-modul/{slug}` | `SuperAdmin\ModulDataController@index` | `layouts.dashboard` | `ModulDinamis`, `ModulDinamisData` |
| `POST`| `/kustom-modul/{slug}` | `SuperAdmin\ModulDataController@store` | - | Validasi dinamis & simpan rekaman |
| `PUT` | `/kustom-modul/{slug}/{id}` | `SuperAdmin\ModulDataController@update` | - | Validasi & perbarui rekaman |
| `DELETE`| `/kustom-modul/{slug}/{id}` | `SuperAdmin\ModulDataController@destroy` | - | Soft delete rekaman data |
| `GET` | `/kustom-modul/{slug}/export` | `SuperAdmin\ModulDataController@export` | CSV stream | Ekspor rekaman data ke CSV |
| `GET` | `/kustom-modul/{slug}/export-pdf` | `SuperAdmin\ModulDataController@exportPdf` | PDF stream | Ekspor rekaman data ke PDF |
| `GET` | `/kustom-modul/{slug}/print` | `SuperAdmin\ModulDataController@printPreview` | `layouts.print` | Pratinjau cetak tabular rekaman |
| `GET` | `/kustom-modul/{slug}/file/{id}/{field}` | `SuperAdmin\ModulDataController@downloadFile` | File stream | Unduh file privat lampiran |

### H. Realtime SSE Stream & API
| Method | URI | Controller / Action | Keterangan |
| :--- | :--- | :--- | :--- |
| `GET` | `/realtime/stream` | `RealtimeStreamController@stream` | Text/event-stream SSE, interval heartbeat 15s |
| `POST`| `/realtime/read/{id}` | `RealtimeStreamController@markRead` | Menandai notifikasi telah dibaca |
| `GET` | `/api/user` | Closure via `auth:sanctum` | Informasi profil pengguna API |
