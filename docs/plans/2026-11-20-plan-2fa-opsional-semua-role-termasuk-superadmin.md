# IMPLEMENTATION PLAN: TRANSFORMASI 2FA MENJADI 100% OPSIONAL UNTUK SELURUH PERAN (TERMASUK SUPERADMIN)

> **Dari:** Lead Architect / Senior Software Engineer  
> **Kepada:** Tim Pengembang MAGKOM  
> **Target Repositori:** `MAGKOM` (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Status:** READY FOR IMPLEMENTATION / MENUNGGU PERSETUJUAN  
> **Tingkat Prioritas:** Medium-High (UX Modernization, Multi-Role Consistency)  
> **Dokumen Terkait:** `docs/plans/2026-11-20-task-senior-dev-2fa-opsional-multi-role-pengaturan-akun.md`, `docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`  
> **Tanggal Dokumen:** 20 November 2026  

---

## 1. 🎯 RINGKASAN PERUBAHAN & LATAR BELAKANG

### 1.1 Kondisi Saat Ini (Current State)
Pada implementasi sebelumnya:
- Akun **Administrasi (`admin`)**, **Pembimbing (`pembimbing`)**, dan **Peserta (`peserta`)** telah diubah menjadi 2FA opsional sukarela di menu Pengaturan Akun masing-masing.
- Namun, **SuperAdmin (`superadmin`)** masih dipertahankan sebagai **Wajib (Mandatory Enforcement)** di middleware `EnsureTwoFactorEnforced` dan dikunci agar tidak dapat menonaktifkan 2FA.
- Akibatnya, akun SuperAdmin yang baru dibuat atau yang belum mengaktifkan 2FA setelah masa tenggang 7 hari habis tetap dicegat (*intercepted*) dan dialihkan paksa ke halaman aktivasi `/keamanan/2fa`.

### 1.2 Target yang Diinginkan Pengguna (Target State)
- **SuperAdmin TIDAK LAGI diwajibkan menghidupkan 2FA.**
- Seluruh peran di sistem MagKom (**SuperAdmin, Admin, Pembimbing, dan Peserta**) kini memiliki status yang seragam: **2FA bersifat 100% OPSIONAL (SUKARELA)**.
- SuperAdmin dapat memilih untuk mengaktifkan atau menonaktifkan 2FA kapan saja melalui menu Pengaturan Akun (`/superadmin/akun`).
- Tidak ada pengguna yang akan dicegat atau diblokir akses dashboard-nya oleh middleware `EnsureTwoFactorEnforced`.

---

## 2. 📊 MATRIKS PERUBAHAN PERAN TERBARU

| Peran (Role) | Kebijakan Sebelumnya | Kebijakan Baru (Target) | Dicegat Middleware? | Tombol Nonaktifkan 2FA |
| :--- | :--- | :--- | :--- | :--- |
| **SuperAdmin** (`superadmin`) | **WAJIB (Enforced)** | **OPSIONAL (Sukarela)** | ❌ **TIDAK** | ✅ **BISA** (Wajib sandi saat ini) |
| **Administrasi** (`admin`) | Opsional | Opsional | ❌ Tidak | ✅ Bisa (Wajib sandi saat ini) |
| **Pembimbing** (`pembimbing`) | Opsional | Opsional | ❌ Tidak | ✅ Bisa (Wajib sandi saat ini) |
| **Peserta** (`peserta`) | Opsional | Opsional | ❌ Tidak | ✅ Bisa (Wajib sandi saat ini) |

> [!NOTE]
> Meskipun aktivasi 2FA bersifat sukarela untuk semua role, **jika pengguna (siapa pun) memilih mengaktifkan 2FA**, saat login sistem tetap wajib meminta verifikasi kode 6-digit TOTP (`/two-factor-challenge`). Pilihan sukarela berada pada keputusan aktivasi/deaktivasi akun.

---

## 3. 🛡️ BENTENG KEAMANAN ANTI-CELAH (SECURITY GUARDRAILS)

1. **Re-Authentication Guard pada Deaktivasi:**  
   Ketika SuperAdmin (maupun role lain) menonaktifkan 2FA, sistem tetap **WAJIB** meminta konfirmasi kata sandi saat ini (`current_password`) guna mencegah sesi tak terkunci disalahgunakan pihak lain.
2. **Audit Logging Komprehensif:**  
   Setiap perubahan status 2FA SuperAdmin (aktif/nonaktif/regenerasi kode) tetap tercatat di tabel `audit_logs` dengan channel `security-auth` dan severity yang relevan (`info` atau `warning`).
3. **Session Cleanliness:**  
   Secret enrollment sementara tetap dihapus dari session saat proses selesai atau dibatalkan.
4. **Brute-Force Rate Limiting:**  
   Percobaan input OTP maupun konfirmasi kata sandi saat menonaktifkan tetap diproteksi rate limiting (maksimal 5 percobaan/menit).

---

## 4. 📁 SPESIFIKASI TEKNIS & PERUBAHAN BERKAS

### 4.1 Backend (PHP / Laravel)

1. **`app/Http/Middleware/EnsureTwoFactorEnforced.php`**
   - Hapus `RoleEnum::SuperAdmin` dari `$rolesWajib` (menjadi array kosong `[]`).
   - Karena tidak ada peran yang wajib 2FA secara paksa, middleware akan meloloskan request (`return $next($request);`) ke controller tujuan tanpa mengalihkan paksa pengguna.

2. **`app/Http/Controllers/Auth/TwoFactorAuthController.php`**
   - Pada method `disable()`: Hapus guard penolakan SuperAdmin (`if ($user->isSuperAdmin())`).
   - SuperAdmin kini dapat menonaktifkan 2FA seperti peran lainnya setelah lolos verifikasi `current_password`.
   - Audit log tetap mencatat aksi `2fa.disabled` dengan user ID SuperAdmin.

### 4.2 Frontend (Blade & Komponen)

3. **`resources/views/components/pengaturan/two-factor-card.blade.php`**
   - Hapus percabangan khusus SuperAdmin yang mengunci status menjadi `"Wajib & Terkunci"`.
   - Samakan antarmuka kartu untuk semua peran:
     - Jika 2FA belum aktif: Tampilkan badge `Belum Aktif`, pesan penjelasan manfaat, dan tombol `[Aktifkan 2FA Sekarang]`.
     - Jika 2FA aktif: Tampilkan badge `Aktif`, waktu konfirmasi, tombol `[Perbarui Recovery Codes]`, dan tombol `[Nonaktifkan 2FA]` (membuka modal input password).
   - Pastikan modal `#modal2faDisable` dapat diakses oleh SuperAdmin saat 2FA sedang aktif.

4. **`resources/views/superadmin/akun/index.blade.php`**
   - Hapus parameter `:isSuperAdmin="true"` agar komponen menggunakan flow standar yang fleksibel.

5. **`resources/views/superadmin/security/two-factor.blade.php`**
   - Sesuaikan teks pengantar agar tidak menyebutkan kata "kewajiban" dan pastikan opsi nonaktifkan 2FA berfungsi dengan konfirmasi kata sandi.

### 4.3 Pengujian (Automated Test Suite)

6. **`tests/Feature/EnsureTwoFactorEnforcedTest.php`**
   - Perbarui skenario `test_superadmin_past_grace_period_without_2fa`:
     - Sebelumnya: assertRedirect ke `superadmin.2fa.settings`.
     - Target: assertOk (SuperAdmin tanpa 2FA dapat mengakses dashboard secara normal tanpa dialihkan).

7. **`tests/Feature/MultiRoleTwoFactorAuthTest.php`**
   - Ubah `test_superadmin_cannot_disable_2fa` menjadi `test_superadmin_can_disable_2fa_with_valid_password`:
     - SuperAdmin dapat menonaktifkan 2FA jika kata sandi valid, dialihkan ke `superadmin.akun.index` dengan session `success`.
     - Verifikasi `$superAdmin->hasEnabledTwoFactor()` menjadi `false`.

---

## 5. 📋 TAHAPAN EKSEKUSI (STEP-BY-STEP RECIPE)

1. **Step 1:** Modifikasi middleware `EnsureTwoFactorEnforced.php` untuk mengosongkan `$rolesWajib`.
2. **Step 2:** Hapus guard pembatas SuperAdmin di `TwoFactorAuthController@disable`.
3. **Step 3:** Perbarui komponen `two-factor-card.blade.php` dan view `superadmin/akun/index.blade.php`.
4. **Step 4:** Perbarui assertions pada `EnsureTwoFactorEnforcedTest.php` dan `MultiRoleTwoFactorAuthTest.php`.
5. **Step 5:** Jalankan seluruh automated test suite:
   ```bash
   php artisan test tests/Feature/EnsureTwoFactorEnforcedTest.php
   php artisan test tests/Feature/MultiRoleTwoFactorAuthTest.php
   ./vendor/bin/pint --test
   ./vendor/bin/phpstan analyse --memory-limit=1G
   npm run build
   ```
6. **Step 6:** Verifikasi manual tampilan di browser dan lapor status ke pengguna.

---

## 6. ✅ DEFINISI SELESAI (DEFINITION OF DONE)

- [ ] SuperAdmin tanpa 2FA tidak lagi dicegat saat mengakses dashboard atau menu apa pun.
- [ ] Kartu 2FA di `/superadmin/akun` menampilkan opsi aktifkan/nonaktifkan secara sukarela.
- [ ] SuperAdmin dapat menonaktifkan 2FA dengan memasukkan kata sandi saat ini yang valid.
- [ ] Input kata sandi salah saat menonaktifkan 2FA tetap ditolak.
- [ ] Log audit mencatat setiap aktivasi dan penonaktifan 2FA SuperAdmin.
- [ ] 100% automated test suite lulus tanpa regresi.
