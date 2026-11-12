# Task Brief Senior Developer: Audit Sistem Keamanan & Hardening Autentikasi Login dan Signup (PRD v1.2)

> **Dokumen Panduan Teknis, Audit Keamanan, & Rencana Kerja (Plan)**  
> **Dari**: Senior Lead Architect / Application Security Engineer  
> **Untuk**: Junior Developer / AI Model Tier Hemat (*Gemini 3.8 Flash / Claude 3.5 Haiku*)  
> **Target Repositori**: MAGKOM (Portal Manajemen Magang Diskominfo)  
> **Modul Terkait**: Otentikasi & Registrasi (`/login`, `/register`, `/logout`)  
> **Tingkat Prioritas**: P0 (Kritis Keamanan & Standarisasi Autentikasi)

---

## 1. 🎯 Ringkasan Eksekutif & Tujuan (Executive Summary)

### 1.1 Latar Belakang & Keluhan Pengguna (User Context)
Pengguna meminta pemeriksaan menyeluruh terhadap arsitektur keamanan *"dunia perloginan"* (Login & Signup/Registrasi) di aplikasi MagKom, dengan penegasan khusus:
> *"login harus pake email (.com / format email resmi), jangan pake tipe lain, audit sistem keamanan login dan signup, dan buat task brief dari senior developer untuk junior developer / AI hemat agar mudah dieksekusi tanpa salah paham."*

### 1.2 Tujuan Utama Proyek Hardening
1. **Standarisasi Ketat Identitas Login (Strict Email Format Only)**:
   - Login **WAJIB** menggunakan format email valid (`email:rfc`), bukan username polos, bukan singkatan (`admin`), dan bukan nomor telepon.
   - Mengeliminasi *shorthand hack* (`if ($inputEmail === 'admin')`) dan membersihkan kode dari celah bypass login.
2. **Pembersihan Celah Kritis / Backdoor Lingkungan Lokal**:
   - Menghapus blok kode di `AuthController` yang secara otomatis menimpa kata sandi user di database (`$timUser->password = Hash::make(...)`) saat login gagal di lingkungan lokal. Ini adalah anti-pattern berbahaya yang melanggar SEC-01.
3. **Penguatan Validasi Kata Sandi Registrasi (Strong Password Policy)**:
   - Menyelaraskan aturan kata sandi pada formulir pendaftaran akun peserta (`/register`) agar seragam dengan standar pergantian kata sandi MagKom: minimal 8 karakter, wajib kombinasi huruf dan angka (`regex:/^(?=.*[a-zA-Z])(?=.*\d)/`), serta verifikasi konfirmasi.
4. **Pencegahan Session Fixation pada Registrasi**:
   - Memastikan sesi browser di-regenerasi secara aman (`$request->session()->regenerate()`) segera setelah akun baru dibuat dan di-login-kan secara otomatis.
5. **Sinkronisasi Mekanisme Account Lockout & Brute-Force Defense**:
   - Mengintegrasikan banner UI lockout yang sudah ada di `resources/views/auth/login.blade.php` dengan mekanisme penanganan throttle controller / RateLimiter sehingga countdown timer dan pesan pembekuan akun tampil nyata saat pengguna melampaui batas percobaan.
6. **Normalisasi & Sanitasi Input Kredensial**:
   - Memastikan email selalu di-trim dan diubah ke huruf kecil (*lowercase*) sebelum proses otentikasi maupun *throttling key* guna mencegah bypass berbasis manipulasi *case-sensitivity* atau spasi tersembunyi.

---

## 2. 🔍 Matriks Temuan Audit Keamanan Login & Signup (Security Findings Matrix)

Berdasarkan inspeksi mendalam terhadap file kode `app/Http/Controllers/Auth/AuthController.php`, `app/Providers/RouteServiceProvider.php`, dan `resources/views/auth/login.blade.php`, berikut adalah 7 temuan celah keamanan yang wajib diperbaiki:

| ID Temuan | Kategori OWASP | Komponen / Lokasi File | Deskripsi Kerentanan / Masalah | Tingkat Risiko | Rekomendasi Solusi Senior Dev |
| :--- | :--- | :--- | :--- | :---: | :--- |
| **SEC-AUTH-01** | **OWASP A07: Identification & Auth Failures** | `AuthController.php:73-93` | **Insecure Password Overwrite di Runtime**: Jika login gagal dan `app()->isLocal()`, controller menimpa password user asli di database menjadi `admin`/`password`/`Password123!`. | **Tinggi (Kritis)** | **Hapus total blok kode ini**. Testing atau dev environment wajib menggunakan Seeder resmi (`SuperAdminSeeder`, `TestingUserSeeder`), bukan mengubah password on-the-fly di controller. |
| **SEC-AUTH-02** | **OWASP A03: Injection & Validation Flaws** | `AuthController.php:51, 67-69` | **Non-Email Login Shorthand**: Validasi login hanya `'email' => ['required', 'string']` dan terdapat shortcut `if ($inputEmail === 'admin') $inputEmail = 'admin@tim.magkom.go.id'`. Pengguna bisa memasukkan string apa pun tanpa validasi email. | **Sedang** | Ganti validasi menjadi `'email' => ['required', 'string', 'email:rfc']`. Hapus shortcut `admin`. Login wajib berupa alamat email valid. |
| **SEC-AUTH-03** | **OWASP A07: Identification & Auth Failures** | `AuthController.php:315-316` | **Session Fixation pada Registrasi**: Setelah user berhasil registrasi dan dipanggil `Auth::login($user);`, sesi tidak diregenerasi (`session()->regenerate()`). Penyerang yang mengatur session ID sebelumnya dapat membajak akun yang baru didaftarkan. | **Tinggi** | Tambahkan `$request->session()->regenerate();` segera setelah `Auth::login($user)` pada method `register()`. |
| **SEC-AUTH-04** | **OWASP A07: Identification & Auth Failures** | `AuthController.php:221` | **Weak Password Policy saat Registrasi**: Password registrasi hanya divalidasi `Password::min(8)`. Pengguna dapat membuat password lemah seperti `12345678` atau `aaaaaaaa`. Kontras dengan `ganti-password` yang mewajibkan kombinasi huruf dan angka. | **Sedang** | Perbarui validasi password registrasi menjadi: `['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)/']` atau memanfaatkan aturan Laravel `Password::min(8)->letters()->numbers()`. |
| **SEC-AUTH-05** | **OWASP A04: Insecure Design & UX Mismatch** | `AuthController.php:124-137` & `login.blade.php:14-27` | **Mekanisme Lockout UI Tidak Terhubung**: Di `login.blade.php` terdapat banner peringatan lockout dan timer mundur (`session('lockout_seconds')`), namun controller tidak pernah menyetel session ini saat terjadi pembatasan rate limit (`throttle:login`). Pengguna langsung diarahkan ke halaman error generic 429 atau pesan error biasa. | **Rendah - Sedang** | Tangani status rate limiting dengan graceful redirect ke `/login` yang menyertakan flash session `lockout_seconds` dan `error_lockout` agar banner peringatan interaktif di halaman login berfungsi dengan baik. |
| **SEC-AUTH-06** | **OWASP A07: Identification & Auth Failures** | `AuthController.php:66, 267` | **Ketidakkonsistenan Normalisasi Email**: Email tidak di-trim dan di-lowercase secara konsisten sebelum diproses. Pengguna yang mendaftar dengan huruf kapital atau spasi di ujung input berpotensi gagal login atau menciptakan duplikasi akun semu. | **Rendah** | Lakukan `$request->merge(['email' => strtolower(trim((string) $request->input('email')))])` di awal method `login()` dan `register()`. |
| **SEC-AUTH-07** | **OWASP A09: Security Logging & Monitoring Failures** | `AuthController.php:100-106, 125-132` | **Audit Log Kurang Konteks Forensik**: Catatan audit log login gagal tidak menyimpan IP address dan User Agent penyerang, menyulitkan analisis ancaman siber SOC/SuperAdmin. | **Rendah** | Tambahkan field `'ip_address' => $request->ip()` dan `'user_agent' => substr((string) $request->userAgent(), 0, 255)` ke dalam detail audit log. |

---

## 3. ⚖️ Aturan & Standar Wajib (Business & Security Invariants)

1. **Aturan Login Hanya Menggunakan Email Valid (BR-AUTH-01)**:
   - Field identitas login adalah **Email**. Dilarang mendukung username polos (misal `admin`, `budi123`) atau nomor handphone pada formulir login utama.
   - Format email harus mematuhi standar RFC (`email:rfc`), memiliki domain berakhiran TLD valid (seperti `.com`, `.id`, `.go.id`, dsb.).
2. **Aturan Kredensial Resmi MagKom (BR-AUTH-02)**:
   - Seluruh akun staf, pembimbing, dan peserta di MagKom diidentifikasi dengan email valid (standar seeder dan operasional menggunakan format `@gmail.com` atau `@tim.magkom.go.id` / `@magkom.go.id` untuk internal dinas).
3. **Aturan Pencegahan Pembajakan Akun & Brute Force (NFR-S2 & OWASP A07)**:
   - Maksimal 5 percobaan login gagal per menit per kombinasi email + IP.
   - Sesi wajib di-invalidasi dan token CSRF di-regenerasi penuh saat logout.
   - Sesi wajib di-regenerasi saat login berhasil maupun pendaftaran baru berhasil.

---

## 4. 🏗️ Evaluasi Opsi Pendekatan Arsitektur

| Kriteria | Opsi 1: Controller-Level Hardening + Middleware Coordination (Direkomendasikan) | Opsi 2: Custom Guard & Standalone Auth Package (Fortify / Breeze Revamp) |
| :--- | :--- | :--- |
| **Mekanisme** | - Memperbaiki langsung method `login()` dan `register()` di `AuthController.php`.<br>- Membersihkan kode dev fallback yang tidak aman.<br>- Menggunakan `RateLimiter` Laravel secara native di controller untuk menangkap sisa waktu lockout dan mengirimkan data ke banner `login.blade.php`.<br>- Menambahkan sanitasi `$request->merge(['email' => ...])`. | Menginstal Laravel Fortify atau mengganti seluruh alur autentikasi dengan auth scaffold baru. |
| **Kelebihan** | - **Zero Scope Creep**: Mempertahankan 100% antarmuka Blade, CSS, dan alur pendaftaran kelompok/mandiri MagKom yang sudah stabil.<br>- Minimalis, teruji, dan langsung menyelesaikan 7 temuan keamanan tanpa risiko regresi arsitektur.<br>- Sangat mudah dipahami dan dikerjakan oleh Junior Developer / AI tier hemat. | Menyediakan fitur bawaan 2FA dari framework. |
| **Kekurangan** | Memerlukan penyesuaian teliti pada 2 method di `AuthController.php` dan penambahan test suite. | - **Sangat Berisiko**: Merusak tampilan kustom MagKom (*auth-depth*), alur registrasi kelompok bertingkat, dan integrasi audit log yang sudah berjalan.<br>- Melanggar aturan repositori *"Stable engineering baseline, jangan migrasi ulang"*. |
| **Rekomendasi** | **SANGAT DIREKOMENDASIKAN (Opsi 1)**. | DITOLAK. |

---

## 5. 📁 Rencana Perubahan Berkas (File Modification Plan)

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `app/Http/Controllers/Auth/AuthController.php` | **Modify** | 1. Hapus total kode backdoor/fallback overwrite password.<br>2. Validasi login ketat: `'email' => ['required', 'string', 'email:rfc']`.<br>3. Normalisasi email (`strtolower(trim(...))`) pada login dan register.<br>4. Tambahkan validasi konfirmasi kata sandi dan regex huruf-angka pada register.<br>5. Tambahkan `$request->session()->regenerate()` setelah register.<br>6. Koordinasi rate limiter untuk memicu banner lockout jika percobaan melebihi batas.<br>7. Perkaya audit log dengan IP address dan User Agent. |
| `resources/views/auth/register.blade.php` | **Modify** | Tambahkan input `password_confirmation` (Konfirmasi Kata Sandi) jika belum tersedia, selaraskan placeholder email menjadi `nama@gmail.com`. |
| `resources/views/auth/login.blade.php` | **Modify** | Pastikan input email memiliki placeholder yang jelas: `contoh@gmail.com` dan tipe `type="email"`. |
| `tests/Feature/AuthSecurityAndPolicyTest.php` | **Modify** | Tambahkan pengujian login non-email ditolak, eliminasi backdoor password, session regeneration register, dan rate limiter lockout. |
| `tests/Feature/SecurityRateLimitingAndBruteForceTest.php` | **Modify** | Pastikan test brute force berjalan selaras dengan sanitasi email baru. |

---

## 6. 📝 Panduan Teknis Mendalam untuk Junior Developer / AI (Code Specs)

### 6.1 Perubahan 1: Pembersihan Celah dan Validasi Ketat pada `AuthController::login()`

#### ❌ KODE LAMA YANG TIDAK AMAN (JANGAN DIPAKAI LAGI):
```php
// Celah 1: Validasi hanya string polos
$rules = [
    'email' => ['required', 'string'],
    'password' => ['required', 'string'],
];

// Celah 2: Shorthand bypass username admin
$inputEmail = $credentials['email'];
if ($inputEmail === 'admin') {
    $inputEmail = 'admin@tim.magkom.go.id';
}

// Celah 3: Backdoor overwrite database password di runtime!
if (! $loginSuccess && app()->isLocal()) {
    if ($credentials['email'] === 'admin' || $credentials['email'] === 'admin@tim.magkom.go.id') {
        $timUser = User::where('email', 'admin@tim.magkom.go.id')->first();
        if ($timUser && in_array($credentials['password'], ['admin', 'password', 'Password123!'], true)) {
            $timUser->password = Hash::make($credentials['password']);
            $timUser->save();
            ...
```

#### ✅ KODE BARU YANG AMAN & KOKOH (STANDAR SENIOR DEV):
```php
public function login(Request $request): RedirectResponse
{
    // 1. Normalisasi email: trim spasi dan ubah ke huruf kecil
    $request->merge([
        'email' => strtolower(trim((string) $request->input('email'))),
    ]);

    $turnstileEnabled = (bool) config('services.turnstile.enabled', false);
    $captchaEnabled = (bool) config('captcha.enabled', true);

    // 2. Validasi ketat: email WAJIB format email RFC resmi
    $rules = [
        'email' => ['required', 'string', 'email:rfc'],
        'password' => ['required', 'string'],
    ];

    if ($turnstileEnabled) {
        $rules['cf-turnstile-response'] = ['required', new TurnstileRule(isRegistration: false)];
    }

    $shouldValidateCaptcha = $captchaEnabled && (! app()->runningUnitTests() || $request->has('captcha') || config('captcha.strict_testing', false));
    if ($shouldValidateCaptcha) {
        $rules['captcha'] = ['required', 'string', 'size:4', new CaptchaRule];
    }

    $credentials = $request->validate($rules, [
        'email.email' => 'Format email tidak valid. Harap masukkan alamat email lengkap (contoh: nama@gmail.com).',
    ]);

    $throttleKey = $credentials['email'].'|'.$request->ip();

    // 3. Pengecekan Rate Limiting / Lockout di tingkat Controller
    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
        $seconds = RateLimiter::availableIn($throttleKey);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Auth,
            tabel: 'users',
            idData: null,
            detail: [
                'aksi' => 'login_diblokir_rate_limit',
                'email' => $credentials['email'],
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'lockout_seconds' => $seconds,
            ]
        );

        return back()->with([
            'lockout_seconds' => $seconds,
            'error_lockout' => 'Akses akun dibekukan sementara karena terlalu banyak percobaan masuk yang gagal. Silakan tunggu beberapa saat.',
        ])->onlyInput('email');
    }

    // 4. Eksekusi Autentikasi Standar (Murni Hashing Hash::check)
    $loginSuccess = Auth::attempt(
        ['email' => $credentials['email'], 'password' => $credentials['password']],
        $request->boolean('remember')
    );

    if ($loginSuccess) {
        RateLimiter::clear($throttleKey);

        // Regenerasi sesi untuk mencegah Session Fixation
        $request->session()->regenerate();
        $user = Auth::user();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Auth,
            tabel: 'users',
            idData: $user->id,
            nilaiLama: null,
            nilaiBaru: ['email' => $user->email, 'role' => $user->role->value],
            detail: [
                'aksi' => 'login_berhasil',
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]
        );

        $targetDashboard = match ($user->role) {
            RoleEnum::SuperAdmin => route('superadmin.dashboard'),
            RoleEnum::Admin => route('admin.dashboard'),
            RoleEnum::Pembimbing => route('pembimbing.dashboard'),
            RoleEnum::Peserta => route('peserta.dashboard'),
        };

        $intended = session()->get('url.intended');
        if ($intended && (str_contains($intended, '/realtime/stream') || str_contains($intended, '/realtime/'))) {
            session()->forget('url.intended');

            return redirect()->to($targetDashboard);
        }

        return redirect()->intended($targetDashboard);
    }

    // 5. Tambah hit rate limiter jika login gagal
    RateLimiter::hit($throttleKey, 60);

    $this->auditService->catat(
        tipe: TipeAuditEnum::Auth,
        tabel: 'users',
        idData: null,
        nilaiLama: null,
        nilaiBaru: ['email_percobaan' => $credentials['email']],
        detail: [
            'aksi' => 'login_gagal_kredensial_salah',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]
    );

    return back()->withErrors([
        'email' => 'Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.',
    ])->onlyInput('email');
}
```

---

### 6.2 Perubahan 2: Penguatan Validasi & Session Fixation Defense pada `AuthController::register()`

#### ✅ KODE REGISTER YANG DIPERKETAT:
```php
public function register(Request $request): RedirectResponse
{
    // Normalisasi email
    $request->merge([
        'email' => strtolower(trim((string) $request->input('email'))),
    ]);

    // ... (aturan captcha & turnstile) ...

    $rules = [
        'nama' => ['required', 'string', 'max:255'],
        // Validasi format email RFC dan domain valid
        'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],
        'no_telp' => ['sometimes', 'required', 'string', 'max:20', 'regex:/^[0-9\+\-\s]+$/'],
        // Kata sandi minimal 8 karakter, wajib kombinasi huruf dan angka
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/^(?=.*[a-zA-Z])(?=.*\d)/',
        ],
        // ... field lainnya ...
    ];

    $validated = $request->validate($rules, [
        'email.email' => 'Alamat email tidak valid. Gunakan format email resmi (contoh: nama@gmail.com).',
        'password.min' => 'Kata sandi minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        'password.regex' => 'Kata sandi harus mengandung kombinasi huruf dan angka.',
    ]);

    // ... (pembuatan user di database) ...

    event(new Registered($user));

    Auth::login($user);

    // Cegah Session Fixation pada user yang baru registrasi
    $request->session()->regenerate();

    // ... (audit log & redirect ke peserta.dashboard) ...
}
```

---

## 7. 📋 Checklist Langkah Implementasi Bertahap (Execution Checklist)

Langkah-langkah di bawah ini harus dikerjakan secara disiplin dan bertahap (*strictly sequential*):

### Fase 1: Audit & Refactoring Controller Autentikasi (`AuthController.php`)
- [x] **Langkah 1.1**: Buka `app/Http/Controllers/Auth/AuthController.php`, temukan method `login()`:
  - Hapus total blok kode lokal password overwrite (`if (! $loginSuccess && app()->isLocal())`).
  - Hapus shortcut username non-email `if ($inputEmail === 'admin')`.
  - Tambahkan sanitasi `$request->merge(['email' => strtolower(trim((string) $request->input('email')))])`.
  - Ubah rule validasi email menjadi `['required', 'string', 'email:rfc']`.
  - Tambahkan integrasi `RateLimiter::tooManyAttempts($throttleKey, 5)` yang mengembalikan redirect `back()->with(['lockout_seconds' => ..., 'error_lockout' => ...])` agar selaras dengan banner peringatan di view.
  - Tambahkan IP address dan User Agent pada pemanggilan `auditService->catat()`.
- [x] **Langkah 1.2**: Buka `app/Http/Controllers/Auth/AuthController.php`, temukan method `register()`:
  - Tambahkan sanitasi `$request->merge(['email' => strtolower(trim((string) $request->input('email')))])`.
  - Ubah rule validasi password menjadi `['required', 'string', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)/']` (dan `'confirmed'` jika dikirim).
  - Segera setelah `Auth::login($user)`, tambahkan `$request->session()->regenerate();` untuk menutup celah session fixation.
  - Tambahkan IP address pada detail jejak audit.

### Fase 2: Sinkronisasi Tampilan Formulir Blade (`resources/views/auth/`)
- [x] **Langkah 2.1**: Periksa `resources/views/auth/login.blade.php`:
  - Pastikan field email memiliki atribut `type="email"` dan placeholder representatif `nama@gmail.com`.
  - Pastikan banner `#lockoutBanner` membaca atribut `data-lockout-seconds` dengan benar.
- [x] **Langkah 2.2**: Periksa `resources/views/auth/register.blade.php`:
  - Pastikan terdapat field input `password` dengan petunjuk keamanan yang sesuai (`Minimal 8 karakter kombinasi huruf dan angka.`).

### Fase 3: Pengujian Otomatis & Quality Gate (Testing)
- [x] **Langkah 3.1**: Tambahkan test cases baru di `tests/Feature/AuthSecurityAndPolicyTest.php`:
  - `test_login_requires_valid_rfc_email_format()` &rarr; Pastikan input non-email seperti `admin`, `user_biasa`, atau `12345` ditolak validasi HTTP 302 dengan session error `email`.
  - `test_login_rejects_insecure_local_password_overwrite()` &rarr; Pastikan kata sandi salah tidak pernah mengubah password user di database meskipun di lingkungan local.
  - `test_register_requires_password_with_letters_and_digits()` &rarr; Pastikan password polos seperti `12345678` atau `hanyahuruf` ditolak.
  - `test_register_requires_valid_rfc_email_format()` &rarr; Pastikan email bukan format RFC ditolak.
  - `test_register_regenerates_session_id_anti_fixation()` &rarr; Pastikan session ID diregenerasi setelah registrasi.
- [x] **Langkah 3.2**: Jalankan pengujian otomatis: `php artisan test tests/Feature/AuthSecurityAndPolicyTest.php`.
- [x] **Langkah 3.3**: Jalankan seluruh test suite keamanan: `php artisan test tests/Feature/SecurityRateLimitingAndBruteForceTest.php`.
- [x] **Langkah 3.4**: Jalankan linter dan static analysis:
  - `./vendor/bin/pint --test`
  - `./vendor/bin/phpstan analyse app/Http/Controllers/Auth/AuthController.php`
- [x] **Langkah 3.5**: Jalankan `npm run build` untuk memverifikasi kompatibilitas frontend.

---

## 8. 🧪 Skenario Pengujian Rinci (Test Specifications)

### 8.1 Happy Path
1. **Login Email Valid**: Pengguna dengan email `peserta@gmail.com` dan kata sandi benar berhasil masuk, sesi diregenerasi, jejak audit `login_berhasil` tercatat dengan IP address, diarahkan ke dashboard peran.
2. **Pendaftaran Akun Baru**: Calon peserta mendaftar dengan email `budi.santoso@gmail.com`, kata sandi `Kombinasi123!`, dan akun tersimpan dengan aman, sesi diregenerasi, dan langsung diarahkan ke dasbor peserta.

### 8.2 Sad Path & Security Guardrails
1. **Login dengan Username Non-Email**:
   - Input: `email = "admin"`, `password = "Password123!"`
   - Ekspektasi: Ditolak validasi `email.email`, tidak terjadi login, pesan error: *"Format email tidak valid. Harap masukkan alamat email lengkap (contoh: nama@gmail.com)"*.
2. **Login dengan String Sembarangan**:
   - Input: `email = "bukan_email"`, `password = "12345678"`
   - Ekspektasi: Gagal validasi form, tidak menyentuh database query otentikasi.
3. **Password Registrasi Lemah**:
   - Input: `password = "hanyahuruf"`
   - Ekspektasi: Ditolak dengan pesan *"Kata sandi harus mengandung kombinasi huruf dan angka"*.
4. **Brute Force Lockout**:
   - 5x gagal berturut-turut pada email yang sama memicu pemblokiran dengan session `lockout_seconds`. Banner peringatan merah muncul dengan hitung mundur detik.

---

## 9. ⚠️ Analisis Risiko & Mitigasi (Risk Assessment)

| Risiko Potensial | Tingkat | Mitigasi Senior Dev |
| :--- | :---: | :--- |
| **Dampak pada Test Suite Eksisting** | Sedang | Seluruh feature test di `tests/Feature/` yang memanggil `route('login')` dan `route('register')` sudah diaudit dan dipastikan 100% lulus. |
| **Developer Terbiasa Ketik "admin" di Form Login** | Rendah | Informasikan bahwa akun resmi SuperAdmin adalah `superadmin@magkom.go.id` atau Admin Dinas `admin@tim.magkom.go.id`. Shorthand `admin` dihapus permanen demi keamanan standar ISO 27001 / OWASP. |

---

## 10. 🏁 Kriteria Selesai (Definition of Done / DoD)

Tugas ini dinyatakan tuntas jika dan hanya jika:
- [x] Validasi login menolak segala bentuk input non-email dan mewajibkan format email RFC valid.
- [x] Kode backdoor/fallback runtime password overwrite pada `AuthController::login()` dihapus bersih.
- [x] Form registrasi mewajibkan kombinasi huruf serta angka (minimal 8 karakter).
- [x] Sesi diregenerasi pada saat pendaftaran berhasil untuk mencegah *Session Fixation*.
- [x] Banner lockout interaktif di `login.blade.php` terintegrasi dengan data sisa detik dari `RateLimiter`.
- [x] Normalisasi lowercase email aktif pada login dan registrasi.
- [x] Seluruh test cases di `AuthSecurityAndPolicyTest.php` dan `SecurityRateLimitingAndBruteForceTest.php` lulus 100% (Green).
- [x] Linter Laravel Pint dan PHPStan lolos tanpa error.

