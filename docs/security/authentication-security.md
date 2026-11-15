# ARSITEKTUR KEAMANAN AUTENTIKASI & KONTROL AKSES (SEC-002, SEC-003, AUTH-001, AUTH-002)

Dokumen ini menjelaskan arsitektur pertahanan autentikasi berlapis pada aplikasi **MAGKOM**, meliputi mitigasi distributed brute-force, integrasi bot protection Cloudflare Turnstile, standardisasi kebijakan kata sandi terpusat, dan penegakan Autentikasi Dua Faktor (2FA) multi-role.

---

## 1. MULTI-LAYER LOGIN BRUTE-FORCE PROTECTION (SEC-003)

Untuk menangkis serangan *distributed credential stuffing* dan *brute-force* yang terdistribusi lintas IP proxy/botnet, MAGKOM menerapkan **4-Layer Defense-in-Depth Rate Limiting**:

```
[ Incoming Login Request ]
           │
           ▼
┌──────────────────────────────────────────────┐
│ Layer 1: Per-Account Throttle                │  Membatasi kegagalan pada akun tertentu
│ Key: login_act:{sha1(email)}                 │  (Maks 5 kegagalan berturut-turut)
│ Limit: 5 attempts / 15 minutes               │  Mengunci akun terlepas dari variasi IP penyerang
└──────────────────────┬───────────────────────┘
                       ▼
┌──────────────────────────────────────────────┐
│ Layer 2: Per-IP Throttle                     │  Membatasi volume request dari satu alamat IP
│ Key: login_ip:{ip}                           │  (Maks 10 percobaan per menit)
│ Limit: 10 attempts / 1 minute                │  Mencegah spraying attacks ke banyak akun
└──────────────────────┬───────────────────────┘
                       ▼
┌──────────────────────────────────────────────┐
│ Layer 3: Account-IP Pair Throttle            │  Mendeteksi percobaan agresif kombinasi spesifik
│ Key: login_pair:{sha1(email|ip)}             │  (Maks 5 percobaan / 15 menit)
│ Progressive Backoff: 60s -> 300s -> 900s     │  Memberikan penalti waktu tunggu eksponensial
└──────────────────────┬───────────────────────┘
                       ▼
┌──────────────────────────────────────────────┐
│ Layer 4: Global Surge Circuit Breaker        │  Menghindari overload/DDoS sistem otentikasi
│ Key: login_global_throttle                   │  (Maks 300 percobaan / 1 menit sistem-wide)
│ Limit: 300 attempts / 1 minute               │  Menjaga ketersediaan server saat diserang botnet
└──────────────────────┬───────────────────────┘
                       ▼
            [ Controller Pipeline ]
```

### Karakteristik Teknis:
- **Progressive Backoff:** Menggunakan penalti waktu tunggu yang meningkat bertahap saat terjadi kegagalan berulang.
- **Fail-Fast Early Rejection:** Saat batasan terlampaui, request langsung dihentikan dengan status `HTTP 429 Too Many Requests` tanpa menjalankan operasi hash `bcrypt` yang intensif memakan CPU.
- **Forensic Audit Logging:** Setiap kegagalan dan penahanan akses dicatat ke tabel `audit_log` (`tipe: auth`) dengan masking otomatis pada password dan token.

---

## 2. SERVER-SIDE CLOUDFLARE TURNSTILE INTEGRATION (SEC-002)

Seluruh form publik (Login & Registrasi) dilindungi oleh Cloudflare Turnstile dengan validasi ketat di sisi server (`TurnstileRule` & `TurnstileService`).

### Fitur Pengamanan Turnstile:
1. **Fail-Closed Policy:** Jika Cloudflare API tidak dapat dijangkau, koneksi internet terputus, atau token kedaluwarsa, validasi **gagal** (permintaan ditolak) untuk mencegah bot memanfaatkan celah jaringan.
2. **Anti-Replay Token Cache:** Setiap token verifikasi yang telah divalidasi disimpan ke cache Redis/Database (`turnstile_used:{sha256(token)}`) dengan TTL 10 menit. Jika token yang sama dikirim kembali oleh penyerang, validasi langsung digagalkan.
3. **Pemisahan Dev-Bypass:** Bypass CAPTCHA tidak lagi menggunakan `app()->isLocal()`. Bypass hanya aktif di lingkungan lokal jika developer secara eksplisit menyetel `CAPTCHA_DEV_BYPASS=true` di `.env`.

---

## 3. KEBIJAKAN KATA SANDI TERPUSAT (AUTH-001)

Sebelumnya, aturan validasi kata sandi terfragmentasi di berbagai request dan controller. Sistem kini menggunakan kelas terpusat `App\Support\PasswordPolicy`.

### Matriks Aturan Kata Sandi:

| Peran Pengguna | Panjang Minimal | Syarat Karakter | Penerapan Endpoint |
|---|---|---|---|
| **Staf Internal** (SuperAdmin, Admin, Pembimbing) | **12 Karakter** | Huruf besar, huruf kecil, angka, dan simbol khusus (`!@#$%^&*()_+-=`) | Login first-time, Profile Settings, Admin Management, Password Reset |
| **Peserta Magang** | **8 Karakter** | Kombinasi huruf dan angka | Registrasi Peserta, Portal Profile, Password Reset |

### Helper API:
```php
// Validasi untuk staf internal
PasswordPolicy::staffRules(bool $confirmed = true);

// Validasi untuk peserta magang
PasswordPolicy::pesertaRules(bool $confirmed = true);

// Validasi dinamis otomatis berdasarkan model User
PasswordPolicy::forUser(User $user, bool $confirmed = true);

// Validasi dinamis otomatis berdasarkan email pengguna
PasswordPolicy::forEmail(string $email, bool $confirmed = true);
```

---

## 4. MULTI-ROLE TWO-FACTOR AUTHENTICATION (2FA) (AUTH-002)

Pengamanan 2FA berbasis TOTP (RFC 6238 / Google Authenticator) kini diperluas ke seluruh tingkatan pengelola data sensitif.

### Kebijakan Penegakan 2FA:

1. **SuperAdmin & Admin Dinas:**
   - **Wajib (Mandatory):** Akses ke dashboard administrasi diblokir jika 2FA belum aktif.
   - **Grace Period (7 Hari):** Pengguna baru diberikan masa tenggang 7 hari sejak pembuatan akun untuk mengaktifkan 2FA. Melewati masa tenggang, pengguna otomatis diarahkan ke halaman enrolmen 2FA (`EnsureTwoFactorEnforced` middleware).
2. **Pembimbing Lapangan:**
   - **Opsional:** Pembimbing dapat mengaktifkan 2FA secara mandiri melalui menu Pengaturan Profil untuk menambah lapisan keamanan.
3. **Penyimpanan Kunci Rahasia:**
   - Secret key TOTP disimpan terenkripsi di database kolom `two_factor_secret` menggunakan algoritma AES-256 bawaan Laravel Crypt.
   - Disediakan 8 kode pemulihan (*recovery codes*) satu kali pakai yang di-hash dengan aman.
4. **Rute Terpadu:**
   - Rute 2FA disatukan di bawah prefix `/2fa/*` (`2fa.enroll`, `2fa.verify`, `2fa.recovery`) dengan deteksi dinamis rute kembali sesuai peran pengguna.
