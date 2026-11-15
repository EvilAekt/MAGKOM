# Autentikasi dan Otorisasi (Dynamic RBAC & Policies)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | Multi-guard auth, Dynamic RBAC 13 modul, 5 Policies anti-IDOR, Temporary Signed URLs |
| Referensi Implementasi | `app/Services/PermissionService.php`, `app/Policies/`, `app/Http/Middleware/` |

---

## 1. Arsitektur Autentikasi

MAGKOM menerapkan sistem autentikasi multi-kanal yang aman dan terisolasi:

1. **Stateful Session Authentication**: Guard web standar Laravel menggunakan cookie terenkripsi (`EncryptCookies`) dan penyimpanan sesi berbasis basis data (`sessions` table).
2. **Social Login (Google OAuth)**: Menggunakan Laravel Socialite untuk mempermudah pendaftaran dan login peserta magang via akun Google resmi institusi atau pribadi.
3. **API Token Authentication**: Laravel Sanctum disiapkan untuk pertukaran data API terotentikasi.
4. **Alur Kredensial Staf Awal**: Akun staf kedinasan (Admin dan Pembimbing) dibuat oleh SuperAdmin dengan kata sandi acak sementara (`password_awal`). Sistem menandai flag `must_change_password = true`, mewajibkan staf mengganti kata sandi pada sesi login perdana sebelum dapat mengakses menu operasional.

---

## 2. Matriks Hak Akses Dinamis (Dynamic RBAC Matrix)

MAGKOM mengelola hak akses perizinan secara granular melalui 13 modul sistem definitif (berdasarkan `PermissionService`). SuperAdmin dapat mengaktifkan atau menonaktifkan izin modul untuk setiap peran melalui antarmuka web tanpa menyunting berkas konfigurasi statis.

### 13 Modul Sistem Definitif

| No | Nama Modul | Slug Izin | Kategori | Peran Default |
|:---:|---|---|---|---|
| 1 | Portal Publik & Registrasi Calon Peserta | `publik.portal-registrasi` | Publik | SuperAdmin, Admin, Pembimbing, Peserta |
| 2 | Pemberkasan & Pendaftaran Magang | `pendaftaran.magang` | Pendaftaran | SuperAdmin, Peserta |
| 3 | Verifikasi Berkas & Seleksi Administratif | `seleksi.verifikasi-berkas` | Seleksi | SuperAdmin, Admin |
| 4 | Manajemen Kuota Bulanan & Bidang Dinas | `kuota.manajemen` | Kuota | SuperAdmin, Admin |
| 5 | Penetapan Penempatan & Pembimbing Lapangan | `penempatan.pembimbing` | Penempatan | SuperAdmin, Admin |
| 6 | Logbook Mingguan & Manajemen Penugasan Proyek | `bimbingan.logbook-tugas` | Bimbingan | SuperAdmin, Pembimbing, Peserta |
| 7 | Rekapitulasi Portofolio Bimbingan & Kinerja | `laporan.kinerja-portofolio` | Pelaporan | SuperAdmin, Admin, Pembimbing |
| 8 | Manajemen Staf Internal & Akun Pegawai | `staf.manajemen` | Tata Kelola | SuperAdmin |
| 9 | Slot Khusus Kemitraan & Override Kuota | `kuota.slot-khusus` | Kuota | SuperAdmin |
| 10 | Konfigurasi Kebijakan & Parameter Sistem | `konfigurasi.parameter` | Tata Kelola | SuperAdmin |
| 11 | Pusat Cadangan & Pemulihan Basis Data | `sistem.cadangan-database` | Tata Kelola | SuperAdmin |
| 12 | Telemetri Kesehatan & Diagnostik Sistem | `sistem.telemetri-kesehatan` | Keamanan | SuperAdmin |
| 13 | Jejak Audit Trail & Log Keamanan Sistem | `keamanan.audit-trail` | Keamanan | SuperAdmin |

> [!NOTE]
> Modul sistem dikelola melalui relasi tabel `permissions` dan tabel pivot `role_permissions`. Method `PermissionService::ensureSystemPermissionsExist()` menjamin ke-13 modul sistem selalu terdaftar secara idempotent saat booting aplikasi.

---

## 3. Pencegahan Celah IDOR melalui Laravel Policies

Insecure Direct Object Reference (IDOR) adalah kerentanan di mana pengguna dapat memanipulasi parameter URL (seperti ID dokumen atau ID pendaftaran) untuk mengakses milik pengguna lain. MAGKOM mencegah celah ini melalui **5 kelas Policy ketat**:

### 3.1 `DokumenPolicy`
- **Tujuan**: Mencegah unduhan berkas pendaftaran milik peserta lain.
- **Logika Otorisasi**:
  ```php
  public function download(User $user, Dokumen $dokumen): bool
  {
      // Admin dan SuperAdmin berhak memeriksa berkas
      if ($user->hasRole([RoleEnum::Admin, RoleEnum::SuperAdmin])) {
          return true;
      }
      // Peserta hanya berhak mengunduh berkas miliknya sendiri
      return $dokumen->pendaftaran->user_id === $user->id;
  }
  ```

### 3.2 `PendaftaranPolicy`
- **Tujuan**: Mencegah peserta lain membatalkan atau mengubah formulir pendaftaran orang lain.
- **Logika Otorisasi**: Memverifikasi `$pendaftaran->user_id === $user->id`.

### 3.3 `PenempatanPolicy`
- **Tujuan**: Memastikan pembimbing hanya dapat melihat data mentee aktif binaannya.
- **Logika Otorisasi**: Memverifikasi `$penempatan->pembimbing_id === $user->id`.

### 3.4 `TugasPolicy`
- **Tujuan**: Membatasi aksi pengumpulan tugas hanya untuk mentee penerima tugas, dan membatasi penilaian tugas hanya oleh pembimbing pembuat tugas.

### 3.5 `LogbookPolicy`
- **Tujuan**: Memastikan hanya pembimbing yang ditugaskan pada penempatan terkait yang dapat menyetujui atau meminta revisi logbook.

---

## 4. Keamanan Berkas Privat & Temporary Signed URLs

Seluruh dokumen identitas dan laporan tugas disimpan pada disk privat (`storage/app/private/`) di luar folder web root.

### Mekanisme Akses:
1. Pendaftar atau pembimbing mengklik tautan unduhan pada antarmuka.
2. Server membuat URL sementara bertanda tangan kriptografis HMAC SHA-256 dengan batas waktu kedaluwarsa 5 menit:
   ```php
   $url = URL::temporarySignedRoute(
       'berkas.signed.download',
       now()->addMinutes(5),
       ['id' => $dokumen->id]
   );
   ```
3. Permintaan ke URL tersebut divalidasi oleh middleware `ValidateSignature` bawaan Laravel dan diperiksa oleh `DokumenPolicy`.
4. Jika tanda tangan URL diubah satu karakter saja atau waktu 5 menit terlewati, server menolak akses dengan HTTP 403 Invalid Signature.
