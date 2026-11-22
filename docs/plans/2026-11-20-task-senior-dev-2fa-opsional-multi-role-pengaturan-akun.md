# TASK BRIEF SENIOR DEVELOPER: TRANSFORMASI 2FA DARI ENFORCEMENT WAJIB MENJADI FITUR KEAMANAN OPSIONAL DI MENU PENGATURAN AKUN MULTI-ROLE (ADMINISTRASI, PEMBIMBING, PESERTA) DENGAN PENGECUALIAN SUPERADMIN WAJIB

> **Dari:** Lead Architect / Senior Software Engineer  
> **Kepada:** Junior Full-Stack Developer & Budget AI Agent (Gemini 3.8 Flash, dsb.)  
> **Target Repositori:** `MAGKOM` (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Status:** READY FOR IMPLEMENTATION  
> **Tingkat Prioritas & Dampak:** HIGH (Authentication, Security Compliance, UX Optimization)  
> **Dokumen Terkait:** `AGENTS.md`, `docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `docs/02-TDD-MagKom.md`  
> **Tanggal Rilis Dokumen:** 20 November 2026  

---

## 1. 🎯 RINGKASAN MISI & LATAR BELAKANG BISNIS (EXECUTIVE SUMMARY)

### 1.1 Masalah Saat Ini (Current Friction)
Sebelumnya, sistem MagKom menerapkan kebijakan penegakan Autentikasi Dua Faktor (2FA) yang sangat kaku melalui middleware `EnsureTwoFactorEnforced` (`AUTH-002`):
1. Pengguna peran **Administrasi (`admin`)** dan **Pembimbing (`pembimbing`)** yang baru dibuatkan akun oleh SuperAdmin dipaksa mengaktifkan 2FA setelah masa tenggang 7 hari (`2fa_grace_days`) habis.
2. Jika masa tenggang terlewati, akses mereka ke seluruh dashboard operasional diblokir total dan dialihkan paksa (*intercepted & locked*) ke halaman `/keamanan/2fa`.
3. Hal ini menimbulkan friksi operasional tinggi di lapangan: staf kedinasan dan pembimbing lapangan sering kesulitan melakukan setup authenticator secara mendesak di awal penugasan.
4. Sementara itu, peran **Peserta (`peserta`)** sama sekali tidak memiliki antarmuka di menu Pengaturan Akun untuk mengamankan akun mereka sendiri menggunakan 2FA.

### 1.2 Kebijakan Baru yang Ditetapkan (Target Business Requirement)
1. **2FA Menjadi Pilihan Bebas (Opsional) untuk Staf & Peserta:**
   - Akun **Administrasi (`admin`)** dan **Pembimbing (`pembimbing`)** yang telah dibuatkan akun **TIDAK LAGI** dipaksa atau diblokir untuk mengaktifkan 2FA. Mengaktifkan 2FA adalah **pilihan mandiri (opt-in)**.
   - Akun **Peserta (`peserta`)** kini diberikan hak yang sama untuk dapat mengaktifkan 2FA secara mandiri demi melindungi data pribadi dan dokumen magang mereka.
2. **Pusat Kendali 2FA Berada di Menu Pengaturan Akun Masing-Masing:**
   - Fitur 2FA (status aktif/belum, tombol aktivasi, tombol nonaktifkan, dan pembaruan kode pemulihan) diintegrasikan langsung ke dalam halaman **Pengaturan Akun** (`/admin/pengaturan`, `/pembimbing/pengaturan`, `/peserta/pengaturan`).
3. **Pengecualian Mutlak untuk SuperAdmin (Zero Tolerance Exception):**
   - **Akun SuperAdmin hanya ada SATU di seluruh sistem MagKom.**
   - SuperAdmin memegang hak akses tertinggi (*root/privilege*) terhadap konfigurasi portal, kuota, audit trail, backup, dan manajemen akun.
   - Oleh karena itu, **SuperAdmin TETAP WAJIB 2FA (MANDATORY ENFORCEMENT)**. SuperAdmin tidak boleh menonaktifkan 2FA sesuka hati, dan tetap dikenakan penegakan middleware `EnsureTwoFactorEnforced` untuk mencegah bencana pengambilalihan akun tunggal (*Single Point of Failure / Account Takeover*).

---

## 2. 📊 MATRIKS KEBIJAKAN AKSES & PERAN (ROLE-BASED 2FA MATRIX)

| Peran (Role) | Status Kebijakan 2FA | Dicegat Middleware `EnsureTwoFactorEnforced`? | Lokasi Pengaturan 2FA | Opsi Nonaktifkan 2FA? | Konfirmasi Sandi untuk Disable/Regenerate? |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **SuperAdmin** (`superadmin`) | **WAJIB (Enforced)** | **YA** (Wajib aktif setelah grace period) | `/superadmin/akun` & `/superadmin/keamanan/2fa` | ❌ **DILARANG** (Kebijakan Terkunci) | Wajib (hanya untuk regenerasi recovery codes) |
| **Administrasi** (`admin`) | **OPSIONAL (Sukarela)** | ❌ **TIDAK** (Akses dashboard tidak pernah diblokir) | `/admin/pengaturan` | ✅ **BISA** | ✅ **WAJIB** (`current_password`) |
| **Pembimbing** (`pembimbing`) | **OPSIONAL (Sukarela)** | ❌ **TIDAK** (Akses dashboard tidak pernah diblokir) | `/pembimbing/pengaturan` | ✅ **BISA** | ✅ **WAJIB** (`current_password`) |
| **Peserta** (`peserta`) | **OPSIONAL (Sukarela)** | ❌ **TIDAK** | `/peserta/pengaturan` | ✅ **BISA** | ✅ **WAJIB** (`current_password`) |

> [!IMPORTANT]
> Meskipun 2FA bersifat opsional untuk Admin, Pembimbing, dan Peserta, **jika pengguna tersebut telah mengaktifkan 2FA**, sistem autentikasi (`AuthController@login`) **WAKTU LOGIN** tetap wajib mencegat mereka ke halaman tantangan 2FA (`/two-factor-challenge`). Pilihan opsional terletak pada *keputusan mengaktifkan/menonaktifkan*, bukan pada bypass verifikasi saat 2FA sudah aktif!

---

## 3. 🛡️ ATURAN KEAMANAN WAJIB "ANTI-CELAH" (NON-NEGOTIABLE SECURITY GUARDRAILS)

Junior Developer & AI Agent wajib mengimplementasikan 6 benteng pertahanan ini tanpa ada yang terlewat:

### G-01: Re-authentication Guard pada Aksi Sensitif (Anti-Session Hijack)
Setiap permintaan untuk:
- Menonaktifkan 2FA (`POST 2fa.disable`)
- Memperbarui Recovery Codes (`POST 2fa.regenerate-recovery-codes`)  
**WAJIB** memvalidasi kata sandi akun saat ini (`current_password`) dengan `Hash::check()`. Jangan pernah mengizinkan penonaktifan hanya dengan mengklik tombol tanpa input sandi, karena rentan dieksploitasi jika perangkat pengguna ditinggalkan tanpa terkunci (*unattended workstation*).

### G-02: SuperAdmin Immutability Guard
Pada `TwoFactorAuthController::disable`, pasang proteksi keras:
```php
if ($user->isSuperAdmin()) {
    return back()->with('error', 'Kebijakan Keamanan: Akun Super Administrator adalah akun tunggal sistem dan wajib mempertahankan Autentikasi Dua Faktor (2FA) tetap aktif.');
}
```

### G-03: Rate Limiting Perlindungan Brute-Force
- Endpoint konfirmasi aktivasi TOTP (`POST 2fa.confirm`) wajib diproteksi `RateLimiter::hit` maksimal 5 percobaan per menit per pengguna untuk mencegah *online brute-force* terhadap secret key yang baru dibuat.
- Endpoint login tantangan (`POST /two-factor-challenge`) tetap mempertahankan limit 5 percobaan per IP & user ID.

### G-04: Single-Use Encrypted Recovery Codes (Kamus Darurat Sekali Pakai)
- Recovery codes tetap menggunakan hashing SHA-256 (`users.two_factor_recovery_codes`) di database.
- Begitu salah satu kode digunakan untuk login darurat, kode tersebut langsung hangus dan dihapus dari daftar kode yang tersisa di database.

### G-05: Audit Trail Komprehensif (SEC-03 & OWASP Logging)
Setiap status transisi 2FA wajib dicatat ke `audit_logs` melalui `AuditService`:
- `2fa.enrolled_and_confirmed` (Severity: `info`, Channel: `security-auth`)
- `2fa.disabled` (Severity: `warning`, Channel: `security-auth`)
- `2fa.recovery_codes_regenerated` (Severity: `info`, Channel: `security-auth`)
- `2fa.challenge_failed` (Severity: `warning`, Channel: `security-auth`)

### G-06: Kebersihan Sesi (Session Hygiene)
- Secret key sementara di session (`auth.2fa.enroll_secret`) **WAJIB** dihapus segera setelah verifikasi pertama berhasil (`session()->forget('auth.2fa.enroll_secret')`) atau saat pengguna menekan tombol batal.

---

## 4. 🎨 ATURAN DESAIN & REPOSITORI (STRICT BLADE & ASSET RULES)

Sesuai aturan permanen `.agents/rules/AGENTS.md`:
1. **DILARANG MENULIS CSS ATAU JAVASCRIPT DI BLADE:**
   - Tidak boleh ada tag `<style>` di Blade.
   - Tidak boleh ada tag `<script>` di Blade.
   - Tidak boleh ada inline attribute event handler seperti `onclick="..."`, `onsubmit="..."`, dll.
   - Tidak boleh ada atribut `style="..."` statis pada elemen baru.
2. **SEMUA CSS BARU BERADA DI `resources/css/`:**
   - Tambahkan styling komponen 2FA ke `resources/css/components/pengaturan-akun.css` yang sudah terdaftar di `resources/css/app.css`.
   - Gunakan token warna Design System MagKom:
     - Sleman Navy: `#003566` / `#02275D`
     - Sleman Gold: `#FFCB05`
     - Success Green: `#16A34A` (badge aktif: `#DCFCE7`, teks: `#166534`)
     - Neutral Gray: `#64748B` (badge nonaktif: `#F1F5F9`, teks: `#475569`)
     - Danger Red: `#DC2626`
3. **SEMUA JAVASCRIPT INTERAKTIF BERADA DI `resources/js/`:**
   - Letakkan interaksi modal konfirmasi sandi di `resources/js/components/pengaturan-2fa-modal.js` dan inisialisasi via `resources/js/app.js` menggunakan **Event Delegation**.
4. **PARITAS LAYOUT BLADE DINAMIS:**
   - Halaman enrollment (`two-factor-enroll.blade.php`) dan recovery codes (`two-factor-recovery-codes.blade.php`) wajib mengecek peran pengguna:
     - Jika pengguna adalah Peserta (`$user->isPeserta()`), extend `layouts.peserta`.
     - Jika Staf/Admin/SuperAdmin, extend `layouts.dashboard`.
   - Tombol "Kembali" / "Selesai" harus mengarahkan kembali ke route Pengaturan Akun masing-masing peran secara dinamis.

---

## 5. 📁 RINCIAN BERKAS & SPESIFIKASI PERUBAHAN (TECHNICAL CHANGESPEC)

```
MAGKOM/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/PengaturanController.php               [MODIFIKASI] Siapkan data 2FA
│   │   │   ├── Auth/TwoFactorAuthController.php             [MODIFIKASI] Dynamic redirect, layout, SuperAdmin guard
│   │   │   ├── Pembimbing/PengaturanController.php          [MODIFIKASI] Siapkan data 2FA
│   │   │   ├── Peserta/PengaturanController.php             [MODIFIKASI] Siapkan data 2FA
│   │   │   └── SuperAdmin/AkunController.php                [MODIFIKASI] Siapkan data status 2FA wajib
│   │   └── Middleware/
│   │       └── EnsureTwoFactorEnforced.php                  [MODIFIKASI] Hapus Admin & Pembimbing dari $rolesWajib
├── resources/
│   ├── css/
│   │   └── components/
│   │       └── pengaturan-akun.css                          [MODIFIKASI] Styling card 2FA, badge status, & modal dialog
│   ├── js/
│   │   ├── app.js                                           [MODIFIKASI] Import & inisialisasi pengaturan-2fa-modal.js
│   │   └── components/
│   │       └── pengaturan-2fa-modal.js                      [BUAT BARU] Event delegation toggle modal sandi nonaktifkan 2FA
│   └── views/
│       ├── admin/pengaturan/index.blade.php                 [MODIFIKASI] Sisipkan partial card 2FA
│       ├── components/pengaturan/
│       │   └── two-factor-card.blade.php                    [BUAT BARU] Komponen UI bersih 2FA Card
│       ├── pembimbing/pengaturan/index.blade.php            [MODIFIKASI] Sisipkan partial card 2FA
│       ├── peserta/pengaturan/index.blade.php               [MODIFIKASI] Sisipkan partial card 2FA
│       ├── superadmin/akun/index.blade.php                  [MODIFIKASI] Sisipkan card info 2FA Terkunci/Wajib
│       └── superadmin/security/
│           ├── two-factor-enroll.blade.php                  [MODIFIKASI] Dynamic layout & dynamic back URL
│           └── two-factor-recovery-codes.blade.php          [MODIFIKASI] Dynamic layout & dynamic finish URL
└── tests/
    └── Feature/
        ├── EnsureTwoFactorEnforcedTest.php                  [MODIFIKASI] Update test: Admin & Pembimbing lolos tanpa 2FA
        └── MultiRoleTwoFactorAuthTest.php                   [MODIFIKASI] Tambah test flow opsional Peserta, Admin, Pembimbing
```

---

## 6. 🔨 PANDUAN LANGKAH EKSEKUSI DETAIL (STEP-BY-STEP RECIPE)

### Langkah 1: Refactor Middleware `EnsureTwoFactorEnforced.php`
Buka `app/Http/Middleware/EnsureTwoFactorEnforced.php`:
Ubah baris penentuan peran wajib agar **HANYA** menyertakan `RoleEnum::SuperAdmin`.

```php
// SEBELUM:
$rolesWajib = [
    RoleEnum::SuperAdmin,
    RoleEnum::Admin,
];
if ((bool) config('auth.2fa_enforce_pembimbing', true)) {
    $rolesWajib[] = RoleEnum::Pembimbing;
}

// SESUDAH:
// Hanya SuperAdmin yang wajib 2FA karena merupakan akun tunggal sistem (Root/Privilege).
// Admin, Pembimbing, dan Peserta menjadikan 2FA sebagai opsi sukarela di menu Pengaturan Akun.
$rolesWajib = [
    RoleEnum::SuperAdmin,
];
```

### Langkah 2: Penyempurnaan `TwoFactorAuthController.php`
Buka `app/Http/Controllers/Auth/TwoFactorAuthController.php`:

1. **Perbaiki helper route tujuan `getSettingsRoute(?User $user)`:**
```php
protected function getSettingsRoute(?User $user): string
{
    if (! $user) {
        return route('login');
    }

    return match ($user->role) {
        RoleEnum::SuperAdmin => route('superadmin.akun.index'),
        RoleEnum::Admin => route('admin.pengaturan.index'),
        RoleEnum::Pembimbing => route('pembimbing.pengaturan.index'),
        RoleEnum::Peserta => route('peserta.pengaturan.index'),
    };
}
```

2. **Kunci Penonaktifan 2FA untuk SuperAdmin pada method `disable()`:**
```php
public function disable(Request $request): RedirectResponse
{
    $user = $request->user();

    // Guard G-02: SuperAdmin tidak boleh mematikan 2FA
    if ($user->isSuperAdmin()) {
        return redirect()->to($this->getSettingsRoute($user))
            ->with('error', 'Kebijakan Keamanan: Akun Super Administrator adalah akun tunggal sistem dan wajib mempertahankan Autentikasi Dua Faktor (2FA) tetap aktif.');
    }

    $request->validate([
        'current_password' => ['required', 'string'],
    ], [
        'current_password.required' => 'Kata sandi saat ini wajib diisi untuk menonaktifkan 2FA.',
    ]);

    if (! Hash::check((string) $request->input('current_password'), (string) $user->password)) {
        throw ValidationException::withMessages([
            'current_password' => 'Kata sandi yang Anda masukkan tidak sesuai.',
        ]);
    }

    $user->disableTwoFactor();

    $this->auditService->catat(
        tipe: TipeAuditEnum::Auth,
        tabel: 'users',
        idData: $user->id,
        nilaiLama: ['two_factor_enabled' => true],
        nilaiBaru: ['two_factor_enabled' => false],
        detail: [
            'aksi' => '2fa.disabled',
            'ip_address' => $request->ip(),
        ],
        user: $user,
        channel: 'security-auth',
        severity: 'warning'
    );

    return redirect()->to($this->getSettingsRoute($user))
        ->with('success', 'Autentikasi dua faktor berhasil dinonaktifkan.');
}
```

3. **Kirim Data Dinamis ke View Enrollment dan Recovery Codes:**
Pastikan `enable()` dan `confirm()` melewatkan parameter `$backUrl` atau layout yang tepat ke view.

### Langkah 3: Update Controller Pengaturan untuk Semua Role
Pada masing-masing Controller Pengaturan (`Peserta`, `Pembimbing`, `Admin`, `SuperAdmin`):
Pastikan `$user->hasEnabledTwoFactor()` atau variabel `$twoFactorEnabled` dikirimkan ke view `index`.
Contoh pada `app/Http/Controllers/Peserta/PengaturanController.php`:
```php
return view('peserta.pengaturan.index', [
    'user' => $user,
    'profil' => $profil,
    'penempatan' => $penempatan,
    'terakhirMasuk' => $terakhirMasuk,
    'temaAktif' => $temaAktif,
    'bahasaAktif' => $bahasaAktif,
    'twoFactorEnabled' => $user->hasEnabledTwoFactor(),
]);
```
Lakukan hal serupa pada:
- `app/Http/Controllers/Pembimbing/PengaturanController.php`
- `app/Http/Controllers/Admin/PengaturanController.php`
- `app/Http/Controllers/SuperAdmin/AkunController.php`

### Langkah 4: Pembuatan Komponen Blade Bersih `two-factor-card.blade.php`
Buat berkas: `resources/views/components/pengaturan/two-factor-card.blade.php`.
Komponen ini menerima props `:user="$user"` dan `:isSuperAdmin="$user->isSuperAdmin()"`.

**Spesifikasi Markup:**
- Container: `<div class="settings-white-card settings-2fa-card">`
- Header:
  - Judul: `Autentikasi Dua Faktor (2FA / TOTP)`
  - Subjudul: `Lapisan keamanan ekstra menggunakan Google Authenticator, Microsoft Authenticator, atau Authy.`
  - Badge Status:
    - Jika aktif: `<span class="badge-status-pill badge-pill-success"><span class="status-indicator-dot green"></span>{{ __('Aktif') }}</span>`
    - Jika belum: `<span class="badge-status-pill badge-pill-muted"><span class="status-indicator-dot gray"></span>{{ __('Belum Aktif') }}</span>`
- Isi:
  - **Kondisi 1 (SuperAdmin):**
    Menampilkan info box bahwa 2FA aktif dan wajib bagi akun SuperAdmin, dengan tombol `[Kelola / Regenerasi Recovery Codes]`. Tidak ada tombol nonaktifkan.
  - **Kondisi 2 (Belum Aktif - Peserta, Pembimbing, Admin):**
    Menampilkan penjelasan manfaat 2FA opsional dan form tombol CTA:
    ```blade
    <form action="{{ route('2fa.enable') }}" method="POST">
        @csrf
        <button type="submit" class="btn-action-navy">
            <i class="fas fa-shield-alt"></i> {{ __('Aktifkan 2FA Sekarang') }}
        </button>
    </form>
    ```
  - **Kondisi 3 (Sudah Aktif - Peserta, Pembimbing, Admin):**
    Menampilkan tanggal konfirmasi dan 2 tombol:
    - Tombol Buka Modal Regenerasi Recovery Codes (`data-action="open-modal-2fa-recovery"`)
    - Tombol Buka Modal Nonaktifkan 2FA (`data-action="open-modal-2fa-disable"`)

- Sisipkan 2 Modal Dialog Tersembunyi di akhir kartu:
  1. `#modal2faDisable`: Berisi form `POST route('2fa.disable')`, input `current_password`, tombol Batal, dan tombol submit merah `Nonaktifkan 2FA`.
  2. `#modal2faRecovery`: Berisi form `POST route('2fa.regenerate-recovery-codes')`, input `current_password`, tombol Batal, dan tombol submit `Buat Kode Baru`.

### Langkah 5: Sisipkan Komponen ke dalam Masing-Masing Halaman Pengaturan
Sematkan `<x-pengaturan.two-factor-card :user="$user" />` pada:
1. `resources/views/peserta/pengaturan/index.blade.php`
2. `resources/views/pembimbing/pengaturan/index.blade.php`
3. `resources/views/admin/pengaturan/index.blade.php`
4. `resources/views/superadmin/akun/index.blade.php`

### Langkah 6: Styling CSS Scoped di `pengaturan-akun.css`
Tambahkan rule CSS di `resources/css/components/pengaturan-akun.css` (tanpa menyentuh file inline):
- `.settings-2fa-card`
- `.badge-status-pill`, `.badge-pill-success`, `.badge-pill-muted`
- `.status-indicator-dot`
- `.modal-2fa-backdrop`, `.modal-2fa-dialog`, `.modal-2fa-header`, `.modal-2fa-body`, `.modal-2fa-footer`
- Support Dark Mode (`[data-theme="dark"]`) sesuai standar token yang ada.

### Langkah 7: JavaScript Modal di `pengaturan-2fa-modal.js`
Buat `resources/js/components/pengaturan-2fa-modal.js`:
- Gunakan Event Delegation pada `document.addEventListener('click', ...)` untuk membaca atribut `data-action="open-modal-2fa-disable"`, `data-action="close-modal-2fa"`, dll.
- Tangani keyboard accessibility (`Escape` untuk menutup modal).
- Import dan inisialisasi modul ini di `resources/js/app.js`.

### Langkah 8: Penyesuaian View Enrollment & Recovery Codes
1. Di `resources/views/superadmin/security/two-factor-enroll.blade.php`:
   - Ganti `@extends('layouts.dashboard')` menjadi:
     ```blade
     @extends(auth()->user()?->isPeserta() ? 'layouts.peserta' : 'layouts.dashboard')
     ```
   - Tombol Batal/Kembali mengarah ke `$backUrl` atau `route('peserta.pengaturan.index')` / `route('admin.pengaturan.index')` / `route('pembimbing.pengaturan.index')`.
2. Di `resources/views/superadmin/security/two-factor-recovery-codes.blade.php`:
   - Ganti `@extends('layouts.dashboard')` menjadi layout dinamis.
   - Tombol "Saya Sudah Menyimpan Seluruh Kode" mengarah ke halaman pengaturan peran yang bersangkutan.

---

## 7. 🧪 SUITE PENGUJIAN & SKENARIO VERIFIKASI (TEST PLAN)

Junior Dev / AI wajib menjalankan dan memastikan tes berikut lulus 100%:

### 7.1 Unit & Feature Test Matrix (PHPUnit)

| ID Test | Nama Skenario Test | Ekspektasi | File Target |
| :--- | :--- | :--- | :--- |
| **TC-01** | `test_admin_past_grace_period_can_access_dashboard` | Admin tanpa 2FA setelah 10 hari tetap bisa akses dashboard dengan kode 200 OK (tidak diredirect). | `EnsureTwoFactorEnforcedTest.php` |
| **TC-02** | `test_pembimbing_past_grace_period_can_access_dashboard` | Pembimbing tanpa 2FA setelah 10 hari tetap bisa akses dashboard dengan kode 200 OK. | `EnsureTwoFactorEnforcedTest.php` |
| **TC-03** | `test_superadmin_past_grace_period_is_strictly_redirected` | SuperAdmin tanpa 2FA setelah 10 hari TETAP dialihkan ke `superadmin.2fa.settings` dengan flash error. | `EnsureTwoFactorEnforcedTest.php` |
| **TC-04** | `test_peserta_can_view_2fa_card_in_settings` | Peserta membuka `/peserta/pengaturan`, melihat status 2FA `Belum Aktif` dan tombol aktivasi. | `MultiRoleTwoFactorAuthTest.php` |
| **TC-05** | `test_peserta_can_enroll_and_confirm_2fa` | Peserta menjalankan flow enable -> confirm dengan OTP valid -> 2FA aktif dan recovery codes terbit. | `MultiRoleTwoFactorAuthTest.php` |
| **TC-06** | `test_peserta_with_active_2fa_is_intercepted_at_login` | Peserta dengan 2FA aktif saat login dicegat ke `/two-factor-challenge`. | `MultiRoleTwoFactorAuthTest.php` |
| **TC-07** | `test_admin_can_disable_2fa_with_valid_password` | Admin menonaktifkan 2FA dengan sandi yang benar -> 2FA nonaktif, log audit tercatat. | `MultiRoleTwoFactorAuthTest.php` |
| **TC-08** | `test_disable_2fa_fails_with_invalid_password` | Admin input sandi salah saat menonaktifkan 2FA -> validasi gagal, 2FA tetap aktif. | `MultiRoleTwoFactorAuthTest.php` |
| **TC-09** | `test_superadmin_cannot_disable_2fa` | Request `POST 2fa.disable` oleh SuperAdmin ditolak dengan error keamanan. | `MultiRoleTwoFactorAuthTest.php` |
| **TC-10** | `test_regenerate_recovery_codes_requires_password` | Regenerasi kode pemulihan sukses hanya jika password saat ini valid. | `MultiRoleTwoFactorAuthTest.php` |

### 7.2 Perintah Eksekusi Verifikasi
```bash
# 1. Jalankan test suite keamanan 2FA
php artisan test tests/Feature/EnsureTwoFactorEnforcedTest.php
php artisan test tests/Feature/MultiRoleTwoFactorAuthTest.php
php artisan test tests/Feature/SuperAdmin/SuperAdminTwoFactorAuthTest.php

# 2. Analisis Statis PHPStan (Maksimal Level 8/Clean)
./vendor/bin/phpstan analyse --memory-limit=1G

# 3. Validasi Format Kode (Laravel Pint)
./vendor/bin/pint --test

# 4. Uji Kompilasi Aset Frontend (Vite)
npm run build
```

---

## 8. ⚠️ DAFTAR JEBAKAN UMUM (COMMON PITFALLS & HOW TO AVOID)

1. **Jebakan Layout Leakage pada Peserta:**
   - *Kesalahan:* Membiarkan `two-factor-enroll.blade.php` meng-`@extends('layouts.dashboard')`.
   - *Dampak:* Peserta magang akan melihat sidebar dan topbar staf kedinasan / SuperAdmin saat mengaktifkan 2FA.
   - *Solusi:* Gunakan `@extends(auth()->user()?->isPeserta() ? 'layouts.peserta' : 'layouts.dashboard')`.

2. **Jebakan Hardcoded Redirect URL:**
   - *Kesalahan:* Di `TwoFactorAuthController`, setelah `disable()` atau `confirm()`, memanggil `redirect()->route('2fa.settings')`.
   - *Dampak:* Peserta atau Pembimbing akan dilempar ke halaman SuperAdmin `/keamanan/2fa` yang asing.
   - *Solusi:* Selalu gunakan helper `getSettingsRoute($user)` yang mengembalikan URL Pengaturan Akun sesuai role.

3. **Jebakan Blade Script / Style Tag:**
   - *Kesalahan:* Menulis `<script>` untuk modal pop-up atau `<style>` untuk styling kartu di file Blade.
   - *Dampak:* Pelanggaran fatal aturan repositori MAGKOM (`.agents/rules/AGENTS.md`) dan CSP violation.
   - *Solusi:* Semua CSS di `pengaturan-akun.css`, semua JS di `pengaturan-2fa-modal.js`.

4. **Jebakan Lupa Re-authentication Password:**
   - *Kesalahan:* Membuat tombol submit "Nonaktifkan" tanpa form input password saat ini.
   - *Dampak:* Celah keamanan kritis (Session Hijacking / CSRF Abuse).
   - *Solusi:* Wajibkan input `current_password` dan periksa dengan `Hash::check()`.

---

## 9. ✅ CHECKLIST SELESAI (DEFINITION OF DONE)

- [ ] Middleware `EnsureTwoFactorEnforced` hanya menegakkan kewajiban 2FA untuk `RoleEnum::SuperAdmin`.
- [ ] Akun `admin` dan `pembimbing` baru tidak lagi diblokir setelah masa tenggang 7 hari habis.
- [ ] Kartu pengaturan 2FA tampil rapi dan responsif di halaman Pengaturan Akun:
  - [ ] `/peserta/pengaturan`
  - [ ] `/pembimbing/pengaturan`
  - [ ] `/admin/pengaturan`
  - [ ] `/superadmin/akun` (mode locked/mandatory)
- [ ] Pengguna non-SuperAdmin dapat mengaktifkan 2FA secara sukarela dan dipandu ke enrollment QR code.
- [ ] Halaman enrollment dan recovery codes menyesuaikan layout (`layouts.peserta` vs `layouts.dashboard`).
- [ ] Tombol selesai/batal pada enrollment mengarahkan kembali ke Pengaturan Akun asal masing-masing peran.
- [ ] Penonaktifan 2FA wajib memasukkan kata sandi saat ini dan berhasil mencatat log audit.
- [ ] SuperAdmin dicegah menonaktifkan 2FA baik melalui UI maupun POST request langsung.
- [ ] Tidak ada tag `<style>` atau `<script>` baru di dalam file Blade.
- [ ] Seluruh pengujian PHPUnit lulus 100% tanpa regresi.
- [ ] `npm run build` berhasil tanpa error.
