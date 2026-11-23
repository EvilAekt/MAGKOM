# Senior Developer Task Specification & PRD: Universal Password Strength Indicator, 8-Char Staff Policy, & Zero-Reload Authentication

**Target Audience**: Junior Developer / Autonomous AI Implementer (e.g., Gemini 3.8 Flash, Claude Haiku, OpenCode)  
**Author**: Senior Fullstack Web Architect & Lead Engineer  
**Date**: 22 November 2026  
**Document Code**: `PRD-AUTH-2026-11-002` (Revisi v2.0 - Penyesuaian Permintaan User)  
**Status**: ✅ Selesai Diimplementasikan & Terverifikasi (Verified)  
**Referensi Desain**: Gambar referensi user (3-segment meter: Weak [Merah], Medium [Kuning], Strong [Hijau])

---

## 1. Ringkasan Revisi & Keputusan Klien (User Feedback Breakdown)

Berdasarkan instruksi langsung dari pengguna pada revisi v2:
1. **Perilaku Meter (Murni Pemberitahuan / Edukasi Visual)**:
   - Indikator kekuatan kata sandi berfungsi murni sebagai **PEMBERITAHUAN VISUAL** (*informational / visual feedback*).
   - **JANGAN membuat tombol submit diblokir/disabled saat status Merah**.
   - Pengguna **tetap bisa melakukan submit** meskipun indikator masih berwarna merah (validasi formal akhir tetap diserahkan ke aturan backend/server).
2. **Cakupan Universal di Seluruh Form Pembuatan Kata Sandi**:
   - Indikator meter 3 segmen ini dipasang di **setiap halaman pembuatan atau pengubahan kata sandi baru** di seluruh sistem MAGKOM:
     - Form Registrasi Peserta Baru (`auth/register.blade.php`)
     - Form Atur Ulang Kata Sandi (`auth/reset-password.blade.php`)
     - Form Ganti Sandi Pertama Kali (`auth/ganti-password.blade.php`)
     - Halaman Akun / Ubah Kata Sandi SuperAdmin (`superadmin/akun/index.blade.php`)
     - Modal Tambah Staf Baru & Modal Ubah Sandi Staf di SuperAdmin (`superadmin/staf/index.blade.php`)
     - Halaman Pengaturan Akun Staf/Peserta (`peserta/pengaturan/index.blade.php`, `admin/pengaturan/index.blade.php`, `pembimbing/pengaturan/index.blade.php`)
3. **Penyederhanaan Kebijakan Sandi Staf / Akun yang Dibuat SuperAdmin**:
   - Mengubah batas minimal panjang kata sandi staf kedinasan / akun yang dibuat SuperAdmin dari **12 karakter menjadi 8 karakter** (`min:8`).
   - Tetap mensyaratkan kombinasi huruf besar, huruf kecil, angka, dan simbol, namun panjang minimal diturunkan ke 8 karakter sesuai standar industri modern agar tidak terlalu panjang.
4. **Fitur Zero-Reload pada Form Login**:
   - Di form login (`auth/login.blade.php`): Saat salah memasukkan kata sandi atau CAPTCHA, halaman **tidak me-reload penuh**, nilai email tidak hilang, CAPTCHA otomatis ter-refresh, dan field kata sandi diberi efek getar (*shake*) serta kursor tetap fokus di input password.

---

## 2. Batasan Arsitektur & Aturan Repositori MAGKOM (Wajib Dipatuhi)

Untuk mencegah implementer junior atau AI melakukan kesalahan fatal, patuhi aturan berikut:
- **Aturan Blade (Rule 3)**:
  - **DILARANG** menulis tag `<script>` atau `<style>` di dalam berkas Blade.
  - **DILARANG** menambahkan inline event handler seperti `oninput="..."`, `onclick="..."`, atau `onsubmit="..."`.
  - Penambahan markup Blade dibatasi seminimal mungkin hanya untuk kontainer semantik:
    `<div class="pw-strength-wrapper" data-target-input="[ID_INPUT]"></div>`.
- **Aturan CSS (Rule 4)**:
  - Buat stylesheet terpusat di [resources/css/components/password-strength.css](file:///g:/MAGKOM/resources/css/components/password-strength.css) dan import di `resources/css/app.css` serta `resources/css/pages/auth/auth-depth.css`.
- **Aturan JavaScript (Rule 5)**:
  - Buat komponen modular di [resources/js/components/password-strength.js](file:///g:/MAGKOM/resources/js/components/password-strength.js) yang dapat diinisialisasi secara universal pada semua form kata sandi.
  - Logika login asinkron (Zero-Reload) ditempatkan di [resources/js/pages/auth/auth-depth.js](file:///g:/MAGKOM/resources/js/pages/auth/auth-depth.js).
- **Aturan Backend (Rule 7 & 8)**:
  - Perbarui [app/Support/PasswordPolicy.php](file:///g:/MAGKOM/app/Support/PasswordPolicy.php) pada method `staffRules()` dari `Password::min(12)` menjadi `Password::min(8)`.
  - Dukung respon JSON pada method `AuthController::login()` saat `$request->expectsJson()` bernilai true.
  - Perbarui automated test suite di `tests/Feature/CentralizedPasswordPolicyTest.php` dan `tests/Feature/SuperAdmin/SuperAdminPasswordPolicyTest.php` agar selaras dengan batas minimal 8 karakter.

---

## 3. Spesifikasi Visual & Logika Password Strength Meter

### 3.1 Visual 3-Segment (Sesuai Gambar Referensi)
Komponen terdiri dari:
1. Tiga batang horizontal berdampingan dengan sudut tumpul (`border-radius: 9999px; height: 6px;`).
2. Label status teks di bawah batang sebelah kiri dengan ukuran `font-size: 12.5px; font-weight: 600;`.
3. Indikator status warna:
   - **Kondisi Kosong (0 karakter)**:
     - Batang 1, 2, 3: Abu-abu netral (`#E2E8F0`).
     - Teks: Tersembunyi (kosong).
     - Tombol Submit: Tetap normal (tidak disentuh).
   - **Kondisi Lemah (Weak / Merah)**:
     - Kriteria: Panjang < 8 karakter, ATAU hanya huruf saja, ATAU hanya angka saja.
     - Batang 1: Merah (`#DC2626`).
     - Batang 2, 3: Abu-abu netral (`#E2E8F0`).
     - Label Teks: "Lemah" (`#DC2626`).
     - **Perilaku Tombol**: **TETAP BISA DIKLIK / SUBMIT DIIZINKAN** (hanya sebagai pemberitahuan visual).
   - **Kondisi Sedang (Medium / Kuning)**:
     - Kriteria: Panjang >= 8 karakter DAN kombinasi minimal huruf dan angka (memenuhi aturan peserta).
     - Batang 1, 2: Kuning/Amber (`#EAB308`).
     - Batang 3: Abu-abu netral (`#E2E8F0`).
     - Label Teks: "Sedang" (`#CA8A04`).
     - **Perilaku Tombol**: **TETAP BISA DIKLIK / SUBMIT DIIZINKAN**.
   - **Kondisi Kuat (Strong / Hijau)**:
     - Kriteria: Panjang >= 8 karakter DAN mengandung kombinasi lengkap huruf besar, huruf kecil, angka, serta simbol khusus.
     - Batang 1, 2, 3: Hijau (`#16A34A`).
     - Label Teks: "Kuat" (`#16A34A`).
     - **Perilaku Tombol**: **TETAP BISA DIKLIK / SUBMIT DIIZINKAN**.

```
Ilustrasi Visual (Pure Notification):
[Batang 1] [Batang 2] [Batang 3]
  Merah      Abu        Abu      -> "Lemah"   (Submit: BISA DIKLIK / PEMBERITAHUAN SAJA)
  Kuning    Kuning      Abu      -> "Sedang"  (Submit: BISA DIKLIK)
  Hijau     Hijau      Hijau     -> "Kuat"    (Submit: BISA DIKLIK)
```

---

## 4. Spesifikasi Perubahan Backend (Kebijakan Sandi 8 Karakter)

Di dalam berkas [app/Support/PasswordPolicy.php](file:///g:/MAGKOM/app/Support/PasswordPolicy.php):

```php
// SEBELUM:
public static function staffRules(bool $confirmed = true, bool $required = true): array
{
    ...
    $rules[] = Password::min(12) // << 12 Karakter
        ->letters()
        ->mixedCase()
        ->numbers()
        ->symbols();
    return $rules;
}

// SESUDAH (Sesuai Permintaan Klien: Standar Industri 8 Karakter):
public static function staffRules(bool $confirmed = true, bool $required = true): array
{
    ...
    $rules[] = Password::min(8) // << Minimal 8 Karakter
        ->letters()
        ->mixedCase()
        ->numbers()
        ->symbols();
    return $rules;
}
```

Dampaknya:
- Akun yang dibuat oleh SuperAdmin (Tambah Staf, Reset Sandi Staf, Ubah Sandi SuperAdmin) kini dapat menggunakan kata sandi 8 karakter (asalkan tetap memiliki huruf besar/kecil, angka, dan simbol).
- Teks petunjuk di antarmuka SuperAdmin (`superadmin/akun/index.blade.php:183` yang sudah bertuliskan *"Minimal 8 karakter, kombinasi huruf besar/kecil, angka, dan simbol"*) kini menjadi **100% konsisten** dengan validasi backend.

---

## 5. Rincian Perubahan Berkas (File Changes Matrix)

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Support/PasswordPolicy.php` | `Modify` | Ubah `Password::min(12)` menjadi `Password::min(8)` pada `staffRules()`. |
| `tests/Feature/CentralizedPasswordPolicyTest.php` | `Modify` | Perbarui ekspektasi test panjang kata sandi staf dari 12 karakter ke 8 karakter. |
| `tests/Feature/SuperAdmin/SuperAdminPasswordPolicyTest.php` | `Modify` | Perbarui test ganti password SuperAdmin agar menerima 8 karakter kombinasi kuat dan menolak < 8 karakter. |
| `resources/css/components/password-strength.css` | `Create` | Stylesheet terpusat untuk meter 3 segmen (merah, kuning, hijau) dan animasi getar. |
| `resources/css/app.css` | `Modify` | Import `@import './components/password-strength.css';`. |
| `resources/css/pages/auth/auth-depth.css` | `Modify` | Import atau integrasikan styling meter pada layout auth. |
| `resources/js/components/password-strength.js` | `Create` | Modul ES Modules universal untuk menghitung skor regex dan memperbarui visual 3 segmen secara realtime. |
| `resources/js/pages/auth/auth-depth.js` | `Modify` | Panggil `initPasswordStrengthMeter()` dan implementasikan `initAjaxLoginForm()` (Zero-Reload). |
| `resources/js/pages/superadmin/akun-index.js` | `Modify` | Panggil `initPasswordStrengthMeter()` untuk form ubah kata sandi SuperAdmin. |
| `resources/js/pages/superadmin/staf-index.js` | `Modify` | Panggil `initPasswordStrengthMeter()` untuk modal Tambah Staf dan Ubah Sandi Staf. |
| `resources/views/auth/login.blade.php` | `Modify` | Tambahkan kontainer alert `#dynamicAuthAlert` untuk pesan error asinkron. |
| `resources/views/auth/register.blade.php` | `Modify` | Sisipkan `<div class="pw-strength-wrapper" data-target-input="sandi"></div>` di bawah field kata sandi. |
| `resources/views/auth/reset-password.blade.php` | `Modify` | Sisipkan kontainer meter di bawah field kata sandi baru. |
| `resources/views/auth/ganti-password.blade.php` | `Modify` | Sisipkan kontainer meter di bawah field kata sandi baru. |
| `resources/views/superadmin/akun/index.blade.php` | `Modify` | Sisipkan kontainer meter di bawah field kata sandi baru SuperAdmin. |
| `resources/views/superadmin/staf/index.blade.php` | `Modify` | Sisipkan kontainer meter pada modal Tambah Staf dan modal Ubah Sandi Staf. |
| `app/Http/Controllers/Auth/AuthController.php` | `Modify` | Tambahkan percabangan respon JSON pada `login()` jika `$request->expectsJson()`. |

---

## 6. Petunjuk Implementasi Kode untuk Junior Developer / AI Pelaksana

### 6.1 Berkas CSS Terpusat: `resources/css/components/password-strength.css`

```css
/* =========================================================================
   UNIVERSAL 3-SEGMENT PASSWORD STRENGTH METER (INFORMATIONAL INDICATOR)
   ========================================================================= */

.pw-strength-wrapper {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-top: 8px;
  width: 100%;
}

.pw-strength-bars {
  display: flex;
  align-items: center;
  gap: 6px;
  width: 100%;
}

.pw-strength-bar {
  flex: 1;
  height: 6px;
  border-radius: 9999px;
  background-color: #E2E8F0;
  transition: background-color 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

/* State: Weak (Merah) */
.pw-strength-wrapper[data-level="weak"] .pw-bar-1 {
  background-color: #DC2626;
}

/* State: Medium (Kuning) */
.pw-strength-wrapper[data-level="medium"] .pw-bar-1,
.pw-strength-wrapper[data-level="medium"] .pw-bar-2 {
  background-color: #EAB308;
}

/* State: Strong (Hijau) */
.pw-strength-wrapper[data-level="strong"] .pw-bar-1,
.pw-strength-wrapper[data-level="strong"] .pw-bar-2,
.pw-strength-wrapper[data-level="strong"] .pw-bar-3 {
  background-color: #16A34A;
}

.pw-strength-label-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12.5px;
  font-weight: 600;
}

.pw-strength-text {
  transition: color 0.25s ease;
  color: #64748B;
}

.pw-strength-wrapper[data-level="weak"] .pw-strength-text {
  color: #DC2626;
}

.pw-strength-wrapper[data-level="medium"] .pw-strength-text {
  color: #CA8A04;
}

.pw-strength-wrapper[data-level="strong"] .pw-strength-text {
  color: #16A34A;
}

/* Efek Getar Halus saat Input Salah (Zero-Reload Login) */
@keyframes shakeInput {
  0%, 100% { transform: translateX(0); }
  20%, 60% { transform: translateX(-6px); }
  40%, 80% { transform: translateX(6px); }
}

.shake-error {
  animation: shakeInput 0.4s ease-in-out;
  border-color: #DC2626 !important;
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15) !important;
}
```

---

### 6.2 Berkas JavaScript Universal: `resources/js/components/password-strength.js`

```javascript
/**
 * Universal 3-Segment Password Strength Meter (Informative Feedback)
 * 1 Segment Merah  = Lemah  (Submit TETAP BISA DIKLIK)
 * 2 Segment Kuning = Sedang (Submit BISA DIKLIK)
 * 3 Segment Hijau  = Kuat   (Submit BISA DIKLIK)
 */
export function initPasswordStrengthMeter() {
  const wrappers = document.querySelectorAll('.pw-strength-wrapper');
  
  wrappers.forEach((wrapper) => {
    // Cegah double listener jika modul diinisialisasi ulang
    if (wrapper.dataset.initialized === 'true') return;
    wrapper.dataset.initialized = 'true';

    // Buat struktur HTML 3 segmen secara otomatis jika belum ada
    if (!wrapper.querySelector('.pw-strength-bars')) {
      wrapper.innerHTML = `
        <div class="pw-strength-bars">
          <div class="pw-strength-bar pw-bar-1"></div>
          <div class="pw-strength-bar pw-bar-2"></div>
          <div class="pw-strength-bar pw-bar-3"></div>
        </div>
        <div class="pw-strength-label-row">
          <span class="pw-strength-text"></span>
        </div>
      `;
    }

    const targetInputId = wrapper.getAttribute('data-target-input') || 'password';
    let input = document.getElementById(targetInputId);
    
    // Fallback: cari input password terdekat jika ID berbeda
    if (!input) {
      input = wrapper.previousElementSibling?.querySelector('input[type="password"]') ||
              wrapper.closest('.form-group, .form-group-password, div')?.querySelector('input[type="password"]');
    }
    if (!input) return;

    const textSpan = wrapper.querySelector('.pw-strength-text');

    const evaluate = () => {
      const val = input.value || '';
      
      // Jika field kosong
      if (val.length === 0) {
        wrapper.removeAttribute('data-level');
        if (textSpan) textSpan.textContent = '';
        return;
      }

      const hasMinLength = val.length >= 8;
      const hasLetters = /[a-zA-Z]/.test(val);
      const hasNumbers = /[0-9]/.test(val);
      const hasSpecial = /[^a-zA-Z0-9]/.test(val);
      const hasMixedCase = /[a-z]/.test(val) && /[A-Z]/.test(val);

      let level = 'weak';
      let label = 'Lemah';

      // Evaluasi Level
      if (hasMinLength && hasLetters && hasNumbers) {
        if (hasMixedCase && hasSpecial) {
          level = 'strong';
          label = 'Kuat';
        } else {
          level = 'medium';
          label = 'Sedang';
        }
      }

      wrapper.setAttribute('data-level', level);
      if (textSpan) textSpan.textContent = label;
      
      // CATATAN SENIOR DEV:
      // Submit TIDAK didisable saat level === 'weak' sesuai revisi permintaan user!
      // Tombol tetap aktif dan berfungsi normal.
    };

    input.addEventListener('input', evaluate);
    evaluate();
  });
}
```

---

### 6.3 Logika Zero-Reload Login di `resources/js/pages/auth/auth-depth.js`

```javascript
/**
 * Penanganan Login Asinkron (Zero-Reload / AJAX Fetch)
 */
export function initAjaxLoginForm() {
  const loginForm = document.querySelector('form[action*="/login"]');
  if (!loginForm) return;

  const submitBtn = loginForm.querySelector('button[type="submit"]');
  const alertContainer = document.getElementById('dynamicAuthAlert');
  const passwordInput = document.getElementById('sandi');
  const captchaInput = document.getElementById('kode');
  const captchaImg = document.querySelector('[data-captcha-img]');

  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerText = 'Memverifikasi...';
    }

    const formData = new FormData(loginForm);
    const actionUrl = loginForm.getAttribute('action');

    try {
      const response = await fetch(actionUrl, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: formData
      });

      const data = await response.json().catch(() => ({}));

      if (response.ok && data.success) {
        window.location.href = data.redirect || '/dashboard';
        return;
      }

      // Refresh Captcha Otomatis tanpa reload halaman
      if (captchaImg) {
        captchaImg.src = '/captcha/image?t=' + Date.now();
      }
      if (captchaInput) {
        captchaInput.value = '';
      }

      // Efek getar pada input password & kursor tetap fokus
      if (passwordInput) {
        passwordInput.classList.add('shake-error');
        setTimeout(() => passwordInput.classList.remove('shake-error'), 500);
        passwordInput.focus();
        passwordInput.select();
      }

      // Tampilkan Alert Error Dinamis tanpa reload halaman (Email tetap ada)
      const errorMsg = data.message || 'Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.';
      if (alertContainer) {
        alertContainer.style.display = 'block';
        alertContainer.innerHTML = `
          <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; font-size: 13.5px; line-height: 1.5; display: flex; align-items: center; gap: 8px">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <span>${errorMsg}</span>
          </div>
        `;
      }

    } catch (err) {
      // Fallback jika fetch terganggu
      loginForm.submit();
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Masuk';
      }
    }
  });
}
```

---

## 7. Langkah Implementasi Bertahap (Task Checklist)

- [x] **Langkah 1 (Backend Password Policy & Test Suite)**:  
  - Buka `app/Support/PasswordPolicy.php`. Ubah `Password::min(12)` menjadi `Password::min(8)` pada method `staffRules()`.
  - Buka `tests/Feature/CentralizedPasswordPolicyTest.php` dan `tests/Feature/SuperAdmin/SuperAdminPasswordPolicyTest.php`. Sesuaikan assertion test agar memvalidasi batas minimal 8 karakter (bukan 12 karakter). Jalankan `php artisan test --filter=PasswordPolicy`.
- [x] **Langkah 2 (CSS Styling Terpusat)**:  
  - Buat berkas baru `resources/css/components/password-strength.css` berisi styling 3 segmen batang dan animasi shake.
  - Import berkas tersebut di `resources/css/app.css` dan `resources/css/pages/auth/auth-depth.css`.
- [x] **Langkah 3 (JavaScript Component Universal)**:  
  - Buat berkas baru `resources/js/components/password-strength.js`.
  - Pasang dan inisialisasi di `resources/js/pages/auth/auth-depth.js`, `resources/js/pages/superadmin/akun-index.js`, dan `resources/js/pages/superadmin/staf-index.js`.
- [x] **Langkah 4 (Zero-Reload Login Engine)**:  
  - Di `resources/js/pages/auth/auth-depth.js`, implementasikan `initAjaxLoginForm()`.
  - Di `app/Http/Controllers/Auth/AuthController.php`, tambahkan pengecekan `$request->expectsJson()` pada method `login()`.
- [x] **Langkah 5 (Penyisipan Markup Semantik Blade)**:  
  - Sisipkan elemen semantik `<div class="pw-strength-wrapper" data-target-input="..."></div>` pada:
    1. `resources/views/auth/register.blade.php` (di bawah field `#sandi`)
    2. `resources/views/auth/reset-password.blade.php` (di bawah field `#password`)
    3. `resources/views/auth/ganti-password.blade.php` (di bawah field `#password`)
    4. `resources/views/superadmin/akun/index.blade.php` (di bawah field `#password`)
    5. `resources/views/superadmin/staf/index.blade.php` (di bawah field modal tambah staf dan modal ubah sandi)
  - Tambahkan container `<div id="dynamicAuthAlert" style="display: none;"></div>` pada `resources/views/auth/login.blade.php`.
- [x] **Langkah 6 (Kompilasi Aset Frontend)**:  
  Jalankan `npm run build` untuk memverifikasi pipeline Vite berjalan tanpa error.
- [x] **Langkah 7 (Automated Test Suite QA)**:  
  Jalankan seluruh test suite autentikasi dan policy:
  `php artisan test --filter=CentralizedPasswordPolicyTest`
  `php artisan test --filter=SuperAdminPasswordPolicyTest`
  `php artisan test --filter=SuperAdminDashboardAndAuditLogTest`

---

## 8. Strategi Pengujian (QA Test Cases)

1. **Uji Sandi Staf / SuperAdmin 8 Karakter**:
   - Kirim password staf `Staff8!A` (8 karakter, kombinasi besar/kecil/angka/simbol).
   - Ekspektasi: Diterima oleh backend (PASS).
   - Kirim password staf `Staff!1` (7 karakter).
   - Ekspektasi: Ditolak oleh backend dengan validasi error (PASS).
2. **Uji Pemberitahuan Visual Meter (Non-Blocking)**:
   - Ketik password lemah `12345` pada form register.
   - Ekspektasi: Batang 1 berwarna merah, label "Lemah", **tombol submit tetap aktif dan bisa diklik**.
   - Ketik `magang2026` (8 karakter huruf & angka).
   - Ekspektasi: Batang 1 & 2 berwarna kuning/amber, label "Sedang", tombol submit aktif.
   - Ketik `Magang#2026!` (8+ karakter, kombinasi lengkap).
   - Ekspektasi: Seluruh batang berwarna hijau, label "Kuat", tombol submit aktif.
3. **Uji Zero-Reload Login**:
   - Masukkan email `admin@diskominfo.go.id` dan password salah.
   - Ekspektasi: Tidak ada reload browser, pesan error muncul instan, teks email tidak hilang, CAPTCHA otomatis me-refresh kode baru, input password bergetar (*shake*).

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Batas minimal kata sandi akun staf kedinasan / SuperAdmin resmi menjadi **8 karakter** di `PasswordPolicy::staffRules()` dan seluruh test policy berstatus hijau.
- [x] Indikator visual 3 segmen tampil di seluruh form pembuatan/pengubahan kata sandi (Register, Reset Sandi, Ganti Sandi, Akun SuperAdmin, Modal Staf).
- [x] Status Merah (Lemah) pada meter berfungsi sebagai pemberitahuan visual saja dan **tidak memblokir/menonaktifkan tombol submit**.
- [x] Form login menangani kegagalan autentikasi tanpa me-reload halaman (*Zero-Reload*), menjaga isi input email, dan me-refresh CAPTCHA secara otomatis.
- [x] Aturan Blade Read-Only dipatuhi: 0 baris `<script>`, 0 baris `<style>`, dan 0 inline event handler di Blade.
- [x] `npm run build` sukses 100% dan seluruh automated feature test berstatus PASS.
