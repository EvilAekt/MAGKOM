# DYNAMIC RBAC PERMISSION MATRIX & AUTHORIZATION ENFORCEMENT (RBAC-001)

Dokumen ini mendokumentasikan penegakan otorisasi dinamis berbasis peran (*Dynamic Role-Based Access Control*) pada aplikasi **MAGKOM**. Hak akses tidak lagi di-hardcode pada level role statis, melainkan diverifikasi secara granular terhadap matriks wewenang di database.

---

## 1. MEKANISME PENEGAKAN OTORISASI (AUTHORIZATION LAYER)

### A. Middleware `permission:<slug>`
Setiap rute yang mengakses data sensitif diproteksi oleh middleware `App\Http\Middleware\EnsureUserHasPermission`:

```php
Route::middleware(['auth', 'role:admin,superadmin', 'permission:seleksi.verifikasi-berkas'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('pendaftaran', AdminPendaftaranController::class);
    });
```

Jika pengguna tidak memiliki permission terkait pada tabel `role_permissions`, sistem:
1. Mengembalikan status respon `HTTP 403 Forbidden`.
2. Mencatat upaya akses ilegal ke `audit_log` (`tipe: auth`, entitas: `permissions`, deskripsi pelanggaran wewenang).

### B. SuperAdmin Global Bypass
Melalui `Gate::before` di `AuthServiceProvider`, peran `SuperAdmin` selalu memiliki wewenang menyeluruh untuk menjamin sistem dapat diadministrasikan dalam kondisi apa pun:

```php
Gate::before(function ($user, $ability) {
    if ($user->role === RoleEnum::SuperAdmin) {
        return true;
    }
});
```

### C. Caching & Invalidation
- Izin akses pengguna per-role di-cache di memori aplikasi selama 30 menit (`role_perms_{role}`).
- Setiap kali SuperAdmin memperbarui matriks wewenang melalui antarmuka *Role & Permission Management* (`RoleController`), cache seluruh role dibersihkan seketika (`Cache::forget("role_perms_{$role}")`).

---

## 2. MATRIKS 13 MODUL SISTEM & WEWENANG BAWAAN

Berikut konfigurasi baku 13 modul sistem bawaan yang terdaftar otomatis melalui `PermissionService`:

| No | Modul Sistem | Slug Wewenang | Kategori | Role Bawaan | Deskripsi |
|---|---|---|---|---|---|
| 1 | Portal Publik & Registrasi | `publik.portal-registrasi` | Publik | SuperAdmin, Admin, Pembimbing, Peserta | Akses informasi formasi, alur pendaftaran, dan registrasi akun. |
| 2 | Pemberkasan & Pendaftaran | `pendaftaran.magang` | Pendaftaran | SuperAdmin, Peserta | Unggah berkas, pemilihan formasi bulan magang, dan pengajuan magang. |
| 3 | Verifikasi Berkas & Seleksi | `seleksi.verifikasi-berkas` | Seleksi | SuperAdmin, Admin | Pemeriksaan berkas, persetujuan/penolakan, dan penerbitan SK. |
| 4 | Manajemen Kuota Bulanan | `kuota.manajemen` | Kuota | SuperAdmin, Admin | Pengaturan batas kuota umum, per bidang, dan buka/tutup bulan. |
| 5 | Penetapan Penempatan | `penempatan.pembimbing` | Penempatan | SuperAdmin, Admin | Penugasan mentee ke unit kerja dan pembimbing lapangan ASN. |
| 6 | Logbook & Penugasan Proyek | `bimbingan.logbook-tugas` | Bimbingan | SuperAdmin, Pembimbing, Peserta | Pengisian logbook mingguan, pembagian tugas proyek, dan mentoring. |
| 7 | Rekapitulasi Portofolio | `laporan.kinerja-portofolio` | Pelaporan | SuperAdmin, Admin, Pembimbing | Rekap capaian tugas proyek mentee dan ekspor laporan kinerja mentoring. |
| 8 | Manajemen Staf Internal | `staf.manajemen` | Tata Kelola | SuperAdmin | Pengelolaan akun Admin Dinas dan Pembimbing Lapangan. |
| 9 | Slot Khusus Kemitraan | `kuota.slot-khusus` | Kuota | SuperAdmin | Alokasi kuota kemitraan kampus/SMK dan diskresi jalur dinas. |
| 10 | Parameter & Konfigurasi | `konfigurasi.parameter` | Tata Kelola | SuperAdmin | Pengaturan parameter operasional, SLA verifikasi, dan retensi audit. |
| 11 | Cadangan & Pemulihan Basis Data | `sistem.cadangan-database` | Tata Kelola | SuperAdmin | Snapshot basis data terisolasi, enkripsi AES-256, dan restorasi. |
| 12 | Telemetri Kesehatan | `sistem.telemetri-kesehatan` | Keamanan | SuperAdmin | Pemantauan latensi real-time, storage, MySQL, dan SSE stream. |
| 13 | Jejak Audit Trail | `keamanan.audit-trail` | Keamanan | SuperAdmin | Audit trail append-only mutasi data bisnis dan otentikasi. |

---

## 3. IDEMPOTENT RECOVERY & PROVISIONING

Sistem dilengkapi mekanisme *self-healing*. Jika tabel wewenang dalam kondisi kosong (misalnya setelah migrasi segar tanpa seeding), pemanggilan `User::hasPermission()` secara otomatis memicu `PermissionService::ensureSystemPermissionsExist()` untuk memulihkan ke-13 modul sistem beserta relasi role bawaan tanpa merusak data eksisting.
