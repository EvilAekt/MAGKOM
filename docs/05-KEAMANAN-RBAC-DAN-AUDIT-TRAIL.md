# Spesifikasi Keamanan, RBAC & Audit Trail MagKom

Dokumen ini memuat blueprint implementasi pengamanan tingkat aplikasi, matriks otorisasi Role-Based Access Control (RBAC), arsitektur Policy, matriks hak akses dinamis, serta spesifikasi jejak audit (*Audit Trail*) untuk sistem **MagKom (Magang Kominfo)**.

---

## 1. Matriks Otorisasi RBAC Komprehensif

Sistem membedakan hak akses secara ketat berdasarkan 4 peran resmi: **SuperAdmin**, **Admin** (Administrasi Kominfo), **Pembimbing**, dan **Peserta**.

| Fungsi & Kemampuan | SuperAdmin | Admin | Pembimbing | Peserta | Mekanisme Penegakan |
|---|---|---|---|---|---|
| Kelola akun staf & reset password staf | **Ya** | – | – | – | Middleware `role:superadmin` |
| Kelola matriks hak akses dinamis (`/role`) | **Ya** | – | – | – | Middleware `role:superadmin` |
| Kelola master data (jurusan, bidang) | **Ya** | – | – | – | Middleware `role:superadmin` |
| Buka / atur periode bulan magang | **Ya** | – | – | – | Middleware `role:superadmin` |
| Kelola alokasi kuota slot khusus & peserta khusus | **Ya** | – | – | – | Middleware `role:superadmin` |
| Kelola Visual Site Editor & CMS Landing Page | **Ya** | – | – | – | Middleware `role:superadmin` |
| Buat snapshot & unduh cadangan database | **Ya** | – | – | – | Middleware `role:superadmin` & Policy |
| Jalankan aksi diagnostik server & telemetri | **Ya** | – | – | – | Middleware `role:superadmin` |
| Isi kuota umum bulanan per jurusan | **Ya** | **Ya** | – | – | Middleware `role:admin,superadmin` |
| Verifikasi berkas pendaftar (Diterima / Ditolak) | **Ya** | **Ya** | – | – | Middleware `role:admin,superadmin` |
| Plotting penempatan & reassign pembimbing | **Ya** | **Ya** | – | – | Middleware `role:admin,superadmin` |
| Beri tugas & review progres tugas mentee | **Lihat** | – | **Ya** | – | `TugasPolicy` (SuperAdmin ditolak mutasi) |
| Terbitkan tugas serentak (*batch classroom*) | – | – | **Ya** | – | `CreateBatchTugasAction` & `TugasPolicy` |
| Batal kirim penyerahan tugas (*unsubmit*) | – | – | – | **Ya** | `TugasPolicy@unsubmit` (selama belum dinilai) |
| Review & minta revisi logbook mingguan | **Lihat** | – | **Ya** | – | `LogbookPolicy` (SuperAdmin ditolak mutasi) |
| Tetapkan status penempatan Selesai (1-Klik) | **Lihat** | – | **Ya** | – | `PembimbingPesertaController@selesai` |
| Ekspor portofolio kinerja ASN (PDF/Excel) | **Lihat** | – | **Ya** | – | Controller & Policy Pembimbing |
| Lihat audit log trail (append-only) 4 kanal | **Lihat** | – | – | – | Middleware `role:superadmin` (tanpa edit/hapus) |
| Ubah konfigurasi 12 parameter sistem | **Ya** | – | – | – | Middleware `role:superadmin` |
| Daftar magang, upload berkas, isi logbook | – | – | – | **Ya** | Middleware `role:peserta` & Policy kepemilikan |

---

## 2. Pola Isolasi SuperAdmin Read-Only pada Ranah Pembimbing

Salah satu aturan bisnis paling fundamental (`D-05`, `BR-10`) adalah: **SuperAdmin berstatus Read-Only mutlak pada ranah pembimbingan (penugasan proyek, peninjauan progres, dan pengesahan logbook). SuperAdmin dilarang keras memiliki tombol maupun rute mutasi untuk mengubah data pembimbingan.**

### Pola Implementasi Policy Laravel 10

Pada [app/Policies/TugasPolicy.php](../app/Policies/TugasPolicy.php) dan [app/Policies/LogbookPolicy.php](../app/Policies/LogbookPolicy.php), hak baca dibuka untuk SuperAdmin, namun seluruh metode mutasi (`create`, `update`, `delete`, `review`) menolak peran SuperAdmin secara tegas:

```php
// app/Policies/TugasPolicy.php
namespace App\Policies;

use App\Models\Penempatan;
use App\Models\Tugas;
use App\Models\User;

class TugasPolicy
{
    /**
     * SuperAdmin dan Admin diizinkan melihat seluruh penugasan bimbingan.
     */
    public function view(User $user, Tugas $tugas): bool
    {
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        $penempatan = $tugas->penempatan;
        if (! $penempatan) {
            return false;
        }

        if ($penempatan->peserta_id === $user->id) {
            return $tugas->status_publikasi === 'dipublikasikan';
        }

        return $penempatan->pembimbing_id === $user->id;
    }

    /**
     * AC-08-5: SuperAdmin STRICTLY READ-ONLY.
     * Ditolak mutlak (return false) jika SuperAdmin mencoba membuat penugasan.
     */
    public function create(User $user, Penempatan $penempatan): bool
    {
        if ($user->isSuperAdmin()) {
            return false;
        }

        return $penempatan->pembimbing_id === $user->id || $user->isAdmin();
    }
}
```

---

## 3. Matriks Hak Akses Dinamis (Dynamic Role-Permission Matrix)

Untuk mengakomodasi fleksibilitas operasional tanpa merusak kontrak kode, sistem mengintegrasikan **Dynamic Permission Matrix** berbasis tabel basis data:
- `permissions`: Master 13 modul sistem utama (`publik.portal-registrasi`, `pendaftaran.magang`, `seleksi.verifikasi-berkas`, `kuota.manajemen`, `penempatan.pembimbing`, `bimbingan.logbook-tugas`, `laporan.kinerja-portofolio`, `staf.manajemen`, `kuota.slot-khusus`, `konfigurasi.parameter`, `sistem.cadangan-database`, `sistem.telemetri-kesehatan`, `keamanan.audit-trail`).
- `role_permissions`: Pivot penugasan izin modul terhadap peran pengguna.

SuperAdmin dapat meninjau dan mengubah matriks izin secara visual melalui rute `/superadmin/role`. Modul yang ditandai `is_system = true` diproteksi dari penghapusan tidak disengaja.

---

## 4. Keamanan Pusat Cadangan Basis Data (Disaster Recovery)

Fasilitas cadangan basis data pada [BackupController.php](../app/Http/Controllers/SuperAdmin/BackupController.php) dan [BackupDatabaseService.php](../app/Services/BackupDatabaseService.php) menerapkan standar keamanan:
1. **Penyimpanan Terenkapsulasi**: Berkas snapshot `.sql.gz` disimpan secara privat di `storage/app/backups/` yang tidak dapat diakses langsung oleh web server.
2. **Checksum Forensik SHA-256**: Setiap berkas cadangan dihitung nilai hash SHA-256 pada saat pembuatan dan divalidasi integritasnya sebelum proses pemulihan (*restore*).
3. **Anti-Path Traversal**: Rute unduhan berkas cadangan (`/superadmin/backup/{filename}/download`) divalidasi ketat menggunakan `basename()` dan regex whitelist nama berkas untuk mencegah serangan manipulasi path direktori (`../`).

---

## 5. Audit Trail Komprehensif (Multi-Channel Stream)

Setiap mutasi entitas bisnis dicatat secara otomatis ke tabel `audit_log` melalui `AuditService` dan `AuditedService` (abstract base):
- **4 Tipe Rekaman**: `auth` (login/logout/failed), `aksi` (create/update/delete form), `service` (eksekusi domain service logic), `akses_data` (unduh dokumen/ekspor).
- **4 Kanal Aliran (Channels)**: `auth`, `pendaftaran`, `penempatan`, `system`.
- **Event Audit Mutasi Baru (November 2026)**:
  - `tugas.unsubmit`: Tercatat saat peserta membatalkan submission pengerjaan tugas (`BR-22`).
  - `tugas.batch_create`: Tercatat saat pembimbing menerbitkan tugas massal ke multi-mentee (`BR-23`).
  - `penempatan.reassign_bulk`: Tercatat saat admin merotasi beberapa pembimbing sekaligus.
- **Sanitasi Otomatis Data Rahasia**: Seluruh kata sandi, password awal, token OAuth, dan kredensial disensor rekursif (`***REDACTED***`) sebelum payload JSON disimpan ke basis data.
- **Anti-Formula Injection**: Ekspor riwayat audit log ke format CSV memformat karakter formula spreadsheet (`=`, `+`, `-`, `@`) dengan prepended apostrof (`'`) untuk melindungi perangkat administrator.

---

## 6. Perlindungan Berkas Privat & Signed URLs

1. Berkas persyaratan peserta disimpan pada disk `storage/app/private/`.
2. Seluruh akses unduhan atau pratinjau wajib melalui rute bertanda tangan HMAC:
   - `GET /berkas/{dokumen}/download` (`middleware: ['signed:relative', 'auth']`)
   - `GET /berkas/{dokumen}/pratinjau` (`middleware: ['signed:relative', 'auth']`)
3. Masa berlaku tautan dibatasi maksimal **5 menit**.
4. Policy kepemilikan memverifikasi bahwa peserta hanya dapat mengunduh berkas miliknya sendiri (atau verifikator/pembimbing terkait), mencegah serangan *Insecure Direct Object References* (IDOR).

---

## 7. HTTP Security Headers & Content Security Policy (CSP)

Middleware `SecurityHeaders` menginjeksi header keamanan pada seluruh respons HTTP:
- `X-Frame-Options: SAMEORIGIN` (mencegah Clickjacking)
- `X-Content-Type-Options: nosniff` (mencegah MIME-sniffing)
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy: camera=(), microphone=(), geolocation=()`
- `Content-Security-Policy`:
  `default-src 'self'; script-src 'self' 'unsafe-inline' https://challenges.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self';`
