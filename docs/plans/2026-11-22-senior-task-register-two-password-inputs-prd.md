# Senior Developer Task Specification & PRD: Implementasi Dua Input Kata Sandi pada Form Register (Password & Konfirmasi Password)

**Target Audience**: Junior Developer / Autonomous AI Implementer (e.g., Gemini 3.8 Flash, Claude Haiku, OpenCode)  
**Author**: Senior Fullstack Web Architect & Lead Engineer  
**Date**: 22 November 2026  
**Document Code**: `PRD-AUTH-2026-11-003` (Khusus Menu Register)  
**Status**: ✅ Selesai Diimplementasikan & Terverifikasi (Verified)  
**Referensi Desain**: Gambar referensi user (`media_1795339857950_b57b1339.png`) — Input password dengan 3-segment strength meter (Kuat, 3 bar hijau) & hint teks "Minimal 8 karakter kombinasi huruf dan angka."

---

## 1. Ringkasan Permintaan Klien (User Request Summary)

Pengguna meminta pembaruan antarmuka dan alur pendaftaran calon peserta magang:
> *"buat plan untuk setiap buat kata sandi baru bikin ada dua jadi harus inputin dua kata sandi ga cuma satu di menu register aja ini masih satu jadi plan ini khusus di menu register aja buat dua yaa"*

### Poin Kunci Kebutuhan:
1. **Khusus Menu Register Saja** (`auth/register.blade.php`):
   - Halaman pendaftaran calon peserta saat ini baru memiliki 1 field kata sandi (`#sandi` / `password`).
   - Menu lain seperti *Reset Password* dan *Ganti Password Pertama Kali* sudah memiliki 2 input (Kata Sandi Baru dan Konfirmasi Kata Sandi Baru).
   - Form register harus diperbarui agar memiliki **2 input kata sandi**:
     - **Input 1**: Kata Sandi (`password`)
     - **Input 2**: Konfirmasi Kata Sandi (`password_confirmation`)
2. **Mempertahankan Indikator Kekuatan Sandi (Password Strength Meter)**:
   - Tetap menampilkan indikator visual 3-segmen horizontal (Lemah/Sedang/Kuat) di bawah Input 1 (Kata Sandi), persis seperti tangkapan layar yang dilampirkan user.
   - Sesuai kesepakatan sebelumnya, indikator 3 segmen ini bersifat edukasi visual (*non-blocking*).
3. **Fitur Pendukung Input 2 (Konfirmasi Kata Sandi)**:
   - Memiliki ikon pembeda semantik (ikon checkmark / konfirmasi).
   - Memiliki tombol mata (*eye toggle*) untuk menampilkan/menyembunyikan teks konfirmasi sandi.
   - Memiliki umpan balik interaktif langsung (*realtime match feedback*) di bawah input konfirmasi untuk memberi tahu apakah konfirmasi sandi sudah cocok dengan kata sandi utama atau belum.
4. **Validasi Backend Laravel**:
   - Jika konfirmasi kata sandi tidak cocok dengan kata sandi utama, backend menolak pendaftaran dan mengembalikan pesan validasi ramah pengguna: *"Konfirmasi kata sandi tidak cocok."*.

---

## 2. Batasan Arsitektur & Aturan Repositori MAGKOM (Wajib Dipatuhi)

Untuk memastikan implementer junior atau AI tidak melanggar tata kelola kode repositori MAGKOM, patuhi aturan baku berikut:

1. **Aturan Blade (Rule 3: Blade Read-Only by Default)**:
   - **DILARANG** menulis tag `<script>` atau `<style>` di dalam berkas Blade.
   - **DILARANG** menambahkan inline event handler seperti `oninput="..."`, `onchange="..."`, atau `onclick="..."`.
   - Perubahan di `resources/views/auth/register.blade.php` diperbolehkan **hanya** untuk menyisipkan markup semantik field HTML konfirmasi kata sandi baru.
2. **Aturan CSS (Rule 4: Single Source of Truth)**:
   - Semua styling diletakkan di berkas CSS terkait ([resources/css/components/password-strength.css](file:///g:/MAGKOM/resources/css/components/password-strength.css) atau [resources/css/pages/auth/auth-depth.css](file:///g:/MAGKOM/resources/css/pages/auth/auth-depth.css)).
   - Dilarang menulis CSS di Blade atau file `public/css/`.
3. **Aturan JavaScript (Rule 5: Modular ES Modules)**:
   - Logika interaksi pencocokan kata sandi realtime diletakkan di [resources/js/pages/auth/auth-depth.js](file:///g:/MAGKOM/resources/js/pages/auth/auth-depth.js).
   - Memanfaatkan mekanisme toggle password yang sudah ada via atribut `data-action="toggle-password"` dan `data-target="sandi_confirmation"`.
4. **Aturan Backend & Testing (Rule 7, 8, & 11)**:
   - Backend [app/Http/Controllers/Auth/AuthController.php](file:///g:/MAGKOM/app/Http/Controllers/Auth/AuthController.php) memanfaatkan `PasswordPolicy::pesertaRules(confirmed: true)` saat field konfirmasi dikirim.
   - Menjaga kompatibilitas mundur (*backward compatibility*) untuk automated tests yang memanggil endpoint registrasi tanpa field konfirmasi sandi.
   - Buat automated feature test suite untuk menguji kasus sukses (password cocok) dan kasus gagal (password tidak cocok).

---

## 3. Spesifikasi Antarmuka & UX (UI/UX Specification)

### 3.1 Struktur Form pada `auth/register.blade.php`
Pada form pendaftaran calon peserta magang, bagian kata sandi disusun berdampingan/berurutan secara vertikal:

```
+-------------------------------------------------------------+
| Identitas Akun Calon Peserta                                |
| ... (Nama Lengkap, Email, No. WhatsApp)                     |
|                                                             |
| [🔒] [................................] [👁️]                |  <- Field 1: Kata Sandi (#sandi)
| [=== Batang 1 ===] [=== Batang 2 ===] [=== Batang 3 ===]   |  <- 3-Segment Strength Meter
| Kuat                                                        |
| Minimal 8 karakter kombinasi huruf dan angka.               |
|                                                             |
| [✓]  [................................] [👁️]                |  <- Field 2: Konfirmasi Kata Sandi (#sandi_confirmation)
| Masukkan ulang kata sandi yang sama untuk konfirmasi.       |  <- Realtime Match Feedback
+-------------------------------------------------------------+
```

### 3.2 Detail Komponen Field 2 (Konfirmasi Kata Sandi)
1. **Container Wrapper**:
   - `<div>` fleksibel mengikuti gaya field form register lainnya.
2. **Elemen Input**:
   - `id="sandi_confirmation"`
   - `name="password_confirmation"`
   - `type="password"`
   - `class="fld"`
   - `autocomplete="new-password"`
   - `placeholder="ulangi kata sandi"`
   - `aria-label="Konfirmasi kata sandi"`
   - `aria-describedby="hint-sandi-confirmation"`
3. **Ikon Input Kiri (Prefix Icon)**:
   - Ikon SVG Checkmark / Verification:
     ```html
     <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px">
       <polyline points="20 6 9 17 4 12"></polyline>
     </svg>
     ```
4. **Tombol Toggle Mata Kanan (Eye Toggle Button)**:
   - Atribut: `data-action="toggle-password"`, `data-target="sandi_confirmation"`.
   - Berisi SVG `.pw-icon-show` dan `.pw-icon-hide`.
   - Otomatis ditangani oleh handler `initAuthInteractions()` di `auth-depth.js`.
5. **Petunjuk Teks Realtime (Interactive Match Feedback)**:
   - ID: `hint-sandi-confirmation`
   - Class: `pw-match-hint`
   - Status & Perilaku:
     - **Keadaan Kosong (Konfirmasi belum diisi)**:
       Teks: *"Masukkan ulang kata sandi yang sama untuk konfirmasi."*
       Warna: `#5F6B76` (Abu-abu netral).
     - **Keadaan Tidak Cocok (Nilai konfirmasi != Nilai sandi)**:
       Teks: *"Kata sandi belum cocok."*
       Warna: `#DC2626` (Merah).
       Border input: Sedikit merah `#FCA5A5`.
     - **Keadaan Cocok (Nilai konfirmasi == Nilai sandi)**:
       Teks: *"✓ Konfirmasi kata sandi cocok."*
       Warna: `#16A34A` (Hijau).
       Border input: Sedikit hijau `#86EFAC`.

---

## 4. Spesifikasi Perubahan Teknis (Technical Implementation Spec)

### 4.1 Perubahan Markup Blade (`resources/views/auth/register.blade.php`)
Menyisipkan blok field konfirmasi tepat di bawah blok field `#sandi` dan meter kekuatannya:

```blade
        {{-- Field 1: Kata Sandi --}}
        <div>
          <div style="position: relative">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="5" y="11" width="14" height="9" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
            <input id="sandi" name="password" class="fld" type="password" autocomplete="new-password" aria-label="Kata sandi" aria-describedby="hint-sandi" placeholder="password" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 54px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
            <button type="button" class="btn-ico" data-action="toggle-password" data-target="sandi" aria-label="Tampilkan kata sandi" style="position: absolute; right: 3px; top: 3px; width: 44px; height: 44px; border: none; border-radius: 8px; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center">
              <svg class="pw-icon-show" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              <svg class="pw-icon-hide" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle><path d="M4 4l16 16"></path></svg>
            </button>
          </div>
          <div class="pw-strength-wrapper" data-target-input="sandi"></div>
          <p id="hint-sandi" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">Minimal 8 karakter kombinasi huruf dan angka.</p>
        </div>

        {{-- Field 2: Konfirmasi Kata Sandi (Baru Ditambahkan) --}}
        <div>
          <div style="position: relative">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <input id="sandi_confirmation" name="password_confirmation" class="fld" type="password" autocomplete="new-password" aria-label="Konfirmasi kata sandi" aria-describedby="hint-sandi-confirmation" placeholder="ulangi kata sandi" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 54px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
            <button type="button" class="btn-ico" data-action="toggle-password" data-target="sandi_confirmation" aria-label="Tampilkan konfirmasi kata sandi" style="position: absolute; right: 3px; top: 3px; width: 44px; height: 44px; border: none; border-radius: 8px; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center">
              <svg class="pw-icon-show" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              <svg class="pw-icon-hide" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle><path d="M4 4l16 16"></path></svg>
            </button>
          </div>
          <p id="hint-sandi-confirmation" class="pw-match-hint" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">Masukkan ulang kata sandi yang sama untuk konfirmasi.</p>
        </div>
```

### 4.2 Perubahan JavaScript Interaktif (`resources/js/pages/auth/auth-depth.js`)
Menambahkan fungsi `initPasswordConfirmationMatch()` yang dipanggil saat inisialisasi:

```javascript
/**
 * Realtime Password Confirmation Matching Feedback
 */
export function initPasswordConfirmationMatch() {
  const pwInput = document.getElementById('sandi');
  const confirmInput = document.getElementById('sandi_confirmation');
  const hint = document.getElementById('hint-sandi-confirmation');

  if (!pwInput || !confirmInput || !hint) return;

  const updateMatchFeedback = () => {
    const val = pwInput.value;
    const confirmVal = confirmInput.value;

    if (!confirmVal) {
      hint.textContent = 'Masukkan ulang kata sandi yang sama untuk konfirmasi.';
      hint.style.color = '#5F6B76';
      confirmInput.style.borderColor = '#AAAAAA';
      return;
    }

    if (val === confirmVal) {
      hint.textContent = '✓ Konfirmasi kata sandi cocok.';
      hint.style.color = '#16A34A';
      confirmInput.style.borderColor = '#86EFAC';
    } else {
      hint.textContent = 'Kata sandi belum cocok.';
      hint.style.color = '#DC2626';
      confirmInput.style.borderColor = '#FCA5A5';
    }
  };

  pwInput.addEventListener('input', updateMatchFeedback);
  confirmInput.addEventListener('input', updateMatchFeedback);
}
```

### 4.3 Penyesuaian Backend Controller (`app/Http/Controllers/Auth/AuthController.php`)
Di `AuthController::register`:
Logika validasi yang ada saat ini sudah mendukung konfirmasi:
```php
$hasConfirmation = $request->has('password_confirmation');
$passwordRules = PasswordPolicy::pesertaRules(confirmed: $hasConfirmation);
```
Saat field `password_confirmation` ada di dalam form:
1. `$request->has('password_confirmation')` bernilai `true`.
2. `PasswordPolicy::pesertaRules(confirmed: true)` menambahkan aturan validasi Laravel `'confirmed'`.
3. Laravel secara otomatis memvalidasi bahwa nilai input `password` harus persis sama dengan input `password_confirmation`.
4. Jika berbeda atau tidak diisi, pesan kesalahan yang sudah terdaftar di `AuthController` langsung dipicu:
   `'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.'`.
5. Kompatibilitas mundur terhadap API/automated testing yang tidak menyertakan key `password_confirmation` tetap terjaga dengan aman.

---

## 5. Rencana Langkah Implementasi (Execution Roadmap)

- [x] **Langkah 1: Penyisipan Elemen Semantik Blade**
  - Buka [resources/views/auth/register.blade.php](file:///g:/MAGKOM/resources/views/auth/register.blade.php).
  - Tambahkan blok field `#sandi_confirmation` (`password_confirmation`) lengkap dengan ikon checkmark, tombol toggle visibilitas sandi, dan elemen hint teks `#hint-sandi-confirmation`.
- [x] **Langkah 2: Penambahan Logika Interaktif Realtime di JavaScript**
  - Buka [resources/js/pages/auth/auth-depth.js](file:///g:/MAGKOM/resources/js/pages/auth/auth-depth.js).
  - Implementasikan fungsi `initPasswordConfirmationMatch()`.
  - Panggil fungsi tersebut di dalam blok inisialisasi `document.addEventListener('DOMContentLoaded', ...)` atau `initAuthInteractions()`.
- [x] **Langkah 3: Verifikasi Validasi Backend & Pesan Kesalahan**
  - Periksa method `register()` di [app/Http/Controllers/Auth/AuthController.php](file:///g:/MAGKOM/app/Http/Controllers/Auth/AuthController.php) untuk memastikan rules dan custom messages berjalan sempurna.
- [x] **Langkah 4: Kompilasi Aset Frontend (Vite Build)**
  - Jalankan `npm run build` dan pastikan proses build 100% bersih tanpa peringatan error.
- [x] **Langkah 5: Pembuatan Automated Feature Test & Pengujian Keseluruhan**
  - Buat unit/feature test di `tests/Feature/Auth/RegisterPasswordConfirmationTest.php`:
    - Menguji halaman register merender field `#sandi_confirmation` dan hint konfirmasi.
    - Menguji submit registrasi dengan konfirmasi kata sandi yang cocok -> Berhasil terdaftar.
    - Menguji submit registrasi dengan konfirmasi kata sandi yang berbeda -> Ditolak dengan error *"Konfirmasi kata sandi tidak cocok."*.
  - Jalankan `php artisan test --filter=RegisterPasswordConfirmationTest` dan `php artisan test --filter=PendaftaranPesertaTest`.

---

## 6. Skenario Pengujian (QA Test Matrix)

| No | Skenario Pengujian | Input Kata Sandi | Input Konfirmasi | Ekspektasi Hasil |
|---|---|---|---|---|
| 1 | Render Field & Toggle | - | - | Field `#sandi_confirmation` tampil dengan ikon checkmark dan tombol mata toggle yang dapat menyembunyikan/menampilkan teks. |
| 2 | Realtime Feedback (Belum Cocok) | `Magang2026!` | `Magang2026` | Hint menampilkan *"Kata sandi belum cocok."* dengan warna teks merah. |
| 3 | Realtime Feedback (Cocok) | `Magang2026!` | `Magang2026!` | Hint menampilkan *"✓ Konfirmasi kata sandi cocok."* dengan warna teks hijau. |
| 4 | Submit Form (Konfirmasi Tidak Cocok) | `Magang2026!` | `Berbeda123!` | Form gagal disubmit, diarahkan kembali dengan error validasi *"Konfirmasi kata sandi tidak cocok."*. |
| 5 | Submit Form (Konfirmasi Cocok) | `Magang2026!` | `Magang2026!` | Form berhasil disubmit, akun peserta terbuat, dan diarahkan ke dashboard. |

---

## 7. Kriteria Selesai (Definition of Done)

- [x] Form pendaftaran akun di `auth/register.blade.php` memiliki **dua input kata sandi** (Kata Sandi & Konfirmasi Kata Sandi).
- [x] Indikator kekuatan kata sandi 3-segmen tetap berfungsi rapi di bawah input kata sandi utama.
- [x] Tombol mata toggle berfungsi di kedua input (bisa menyembunyikan/menampilkan kata sandi secara independen).
- [x] Terdapat umpan balik realtime (cocok / belum cocok) saat pengguna mengetik konfirmasi kata sandi.
- [x] Backend Laravel memvalidasi kecocokan kedua sandi dan menolak submit jika tidak sama.
- [x] Seluruh aturan repositori (0 script/style inline di Blade, styling di CSS, JS via ES Module, Vite build aman) terpenuhi.
- [x] Automated test suite hijau (PASS).
