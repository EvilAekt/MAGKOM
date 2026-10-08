# Rencana Implementasi: Gelombang Backend Tambahan (Penyempurnaan Administrasi & Utility)

Dokumen ini mendefinisikan langkah terperinci untuk melengkapi seluruh kebutuhan backend sistem MagKom yang tertuang pada PRD (`01-PRD-MagKom.md`), TDD (`02-TDD-MagKom.md`), dan Dokumen Keamanan (`05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`).

---

## 1. Lingkup Pekerjaan & Modul

### Sub-Modul 1: Master Data & Akun Staf SuperAdmin (F-11 & BR-15)
1. **`app/Http/Controllers/SuperAdmin/StafController.php`**:
   - `index`: Daftar staf internal (Admin & Pembimbing), filter role & status aktif, pencarian nama/email.
   - `store`: Buat akun staf baru (`RoleEnum::Admin` atau `RoleEnum::Pembimbing`), otomatis `email_verified_at = now()`, `must_change_password = true`, `status_aktif = true`, audit log `staf.dibuat` (AC-11-3).
   - `resetPassword`: Reset kata sandi staf oleh SuperAdmin ke password sementara, flag `must_change_password = true`, audit log `staf.reset_password` (AC-11-4).
   - `toggleAktif`: Toggle status aktif akun staf, audit log `staf.toggle_status` (AC-11-4).
2. **`app/Http/Controllers/Auth/PasswordController.php`**:
   - Form ganti password wajib (`GET /password/ganti`, nama route: `password.ganti`).
   - Eksekusi ganti password staf (`POST /password/ganti`): Validasi `current_password`, cek password baru minimal 8 karakter dengan huruf dan angka, dan password baru tidak boleh sama dengan password lama (AC-11-5 & BR-15). Setelah berhasil: `must_change_password = false`, update password, audit log.
3. **`app/Http/Controllers/SuperAdmin/JurusanController.php`**:
   - CRUD Master Jurusan (`index`, `store`, `update`, `toggleAktif`) dengan validasi BR-12 (penolakan nonaktif jika masih ada kuota/pendaftaran/penempatan aktif) (AC-11-1, AC-11-2).
4. **`app/Http/Controllers/SuperAdmin/BidangController.php`**:
   - CRUD Master Bidang Kerja Kominfo (`index`, `store`, `update`, `toggleAktif`).
5. **`app/Http/Controllers/SuperAdmin/RubrikController.php`**:
   - CRUD Master Rubrik Penilaian (`index`, `store`, `update`, `toggleAktif`).
   - Validasi: Total bobot semua kriteria aktif wajib tepat 100% (AC-10-4).
6. **`app/Http/Controllers/SuperAdmin/PengaturanController.php`**:
   - Index & Update parameter konfigurasi sistem (8 parameter di tabel `pengaturan`), validasi tipe dan batas nilai, audit log `pengaturan.diubah`, serta invalidasi cache (AC-11-7).

### Sub-Modul 2: Penelusuran Audit Log SuperAdmin (F-13 AC-13-6)
1. **`app/Http/Controllers/SuperAdmin/AuditLogController.php`**:
   - `index`: List `audit_log` strictly read-only, paginasi 25 baris, order by waktu DESC.
   - Filter query params: `dari_tanggal`, `sampai_tanggal`, `user_id`, `tipe` (auth/aksi/service/akses_data), `entitas`, `request_id`.

### Sub-Modul 3: Aksi Lanjutan Administrasi Penempatan & Antrean (F-04 & F-07)
1. **`app/Services/PlacementService.php`**:
   - Tambah method `pindahPembimbing(Penempatan $penempatan, User $pembimbingBaru, User $admin)`: verifikasi batas beban pembimbing BR-13, update pembimbing, audit trail A-12.
   - Tambah method `batalkanPenempatan(Penempatan $penempatan, User $admin, string $alasan)`: penegakan BR-07 (sebelum tanggal mulai: kuota kembali, akun peserta aktif; sejak tanggal mulai: kuota tidak kembali, akun peserta dinonaktifkan).
2. **`app/Http/Controllers/Admin/PenempatanController.php`**:
   - Action `pindahPembimbing`: Endpoint untuk admin/superadmin memindahkan pembimbing.
   - Action `batalkan`: Endpoint untuk admin/superadmin membatalkan/mencatat pengunduran diri.
3. **`app/Http/Controllers/Admin/WaitingListController.php`**:
   - Action `tutupAntrean(BulanMagang $bulan)`: Mengubah pendaftaran `waiting_list` pada bulan terkait menjadi `ditolak` secara massal dengan catatan otomatis saat periode magang dimulai (AC-04-7).

### Sub-Modul 4: Autentikasi Lanjutan, Utility & Scheduler Kernel (F-02, F-12, NFR)
1. **`app/Console/Kernel.php`**:
   - Daftarkan scheduler harian `$schedule->command('magkom:nonaktifkan-peserta')->dailyAt('00:01')->withoutOverlapping();` (F-12).
2. **`app/Http/Controllers/Auth/VerificationController.php`**:
   - Endpoint verifikasi email standar: `GET /email/verify`, `GET /email/verify/{id}/{hash}`, `POST /email/verification-notification` (AC-02-4).
3. **`app/Http/Controllers/Auth/ResetPasswordController.php`**:
   - Endpoint lupa password & reset kata sandi peserta: `GET /forgot-password`, `POST /forgot-password`, `GET /reset-password/{token}`, `POST /reset-password` (AC-02-6, seragam untuk email terdaftar maupun tidak).
4. **Endpoint Utility & Healthcheck**:
   - `GET /health`: JSON status 200 healthcheck database & private storage (NFR-O1).
   - `GET /kebijakan-privasi`: Kebijakan privasi calon peserta (NFR-R1).
