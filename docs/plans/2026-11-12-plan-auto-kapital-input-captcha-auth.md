# Plan: Implementasi Auto-Kapital (Uppercase) & Enhanced UX pada Input Teks CAPTCHA (Login & Register)

> **Dokumen Panduan Teknis & Rencana Kerja (Plan)**  
> **Target Repositori**: MAGKOM (Portal Manajemen Magang Diskominfo)  
> **Modul Terkait**: Formulir Otentikasi (`login.blade.php`, `register.blade.php`, `captcha-field.blade.php`, `auth-depth.css`, `auth-depth.js`, `AuthController.php`)  
> **Tingkat Prioritas**: P1 (Penyempurnaan Antarmuka, Ergonomi Pengguna, & Aksesibilitas Mobile)

---

## 1. 🎯 Ringkasan Eksekutif & Tujuan (Executive Summary)

### 1.1 Latar Belakang & Kebutuhan Pengguna
Saat ini, gambar verifikasi visual CAPTCHA di MagKom (`route('captcha.image')`) selalu menghasilkan 4 karakter acak berupa angka dan **huruf besar/kapital** (misal: `7KQ2`, `DE4F`, `M9XA`, `R3TP`).
Meskipun sistem backend (`CaptchaService::validate()`) telah bersifat *case-insensitive* (`hash_equals(strtoupper(...), strtoupper(...))`), ketika pengguna mengetik di formulir:
1. Huruf yang muncul di kolom teks secara bawaan berukuran huruf kecil (*lowercase*), misalnya `7kq2` atau `de4f`.
2. Pengguna sering kali merasa ragu apakah huruf yang mereka ketik sudah benar atau harus menekan tombol CapsLock / Shift agar cocok dengan gambar.
3. Di perangkat seluler (smartphone Android & iOS), keyboard virtual secara default menampilkan huruf kecil, memaksa pengguna menekan tombol Shift satu per satu untuk setiap huruf.

### 1.2 Tujuan Solusi
1. **Auto-Kapital Visual & Perilaku (Automatic Uppercase Display)**: Setiap karakter huruf yang diketikkan pengguna otomatis berubah menjadi huruf kapital (*uppercase*) seketika saat diketik.
2. **Optimalisasi Keyboard Seluler (Mobile Virtual Keyboard Optimization)**: Keyboard virtual pada smartphone otomatis berada dalam mode huruf kapital (*all caps / characters mode*) saat fokus pada input CAPTCHA melalui atribut standar `autocapitalize="characters"`.
3. **Penyelarasan Tipografi & Jarak Huruf (Letter Spacing & Monospace Aesthetics)**: Teks CAPTCHA yang diketik memiliki bobot tegas (`font-weight: 700`) dan jarak antar-karakter yang renggang (`letter-spacing: 0.12em`), sehingga 4 karakter kode terlihat tegas, jelas, dan seragam dengan badge gambar CAPTCHA.
4. **Preservasi Kursor Input (Caret Preservation)**: Logika JavaScript mempertahankan posisi kursor (*selection start & end*) sehingga apabila pengguna mengedit huruf di tengah, kursor tidak meloncat ke ujung akhir teks.
5. **Backend Normalization Safety**: Memastikan controller melakukan normalisasi `strtoupper(trim(...))` secara konsisten.

---

## 2. 🔍 Investigasi Codebase & Titik Sentuh (Codebase Investigation)

Berdasarkan penelusuran kode, berikut adalah elemen input CAPTCHA yang ada di repositori:

1. **`resources/views/auth/login.blade.php` (baris 74-78)**:
   ```blade
   <input id="kode" name="captcha" class="fld" type="text" maxlength="4" autocomplete="off" placeholder="4 karakter kode di atas" ...>
   ```
2. **`resources/views/auth/register.blade.php` (baris 192-196)**:
   ```blade
   <input id="kode" name="captcha" class="fld" type="text" maxlength="4" autocomplete="off" aria-describedby="hint-kode" placeholder="4 digit kode di atas" ...>
   ```
3. **`resources/views/components/captcha-field.blade.php` (baris 44-55)**:
   ```blade
   <input type="text" id="captcha" name="captcha" maxlength="4" required class="form-input captcha-input @error('captcha') form-input-error @enderror" placeholder="4 digit kode di atas" ...>
   ```
4. **`resources/css/pages/auth/auth-depth.css`**:
   Merupakan stylesheet utama untuk halaman `/login` dan `/register` dengan pola layout *auth-depth*.
5. **`resources/js/pages/auth/auth-depth.js`**:
   Script ES Module yang menginisialisasi interaksi halaman auth (`initAuthDepth()`).
6. **`app/Http/Controllers/Auth/AuthController.php`**:
   Menangani validasi input login dan registrasi.
7. **`app/Services/CaptchaService.php`**:
   Menghasilkan SVG captcha dengan karakter `config('captcha.characters', '23456789ABCDEFGHJKLMNPQRSTUVWXYZ')`.

---

## 3. 🏗️ Evaluasi Opsi Pendekatan Arsitektur (Architectural Options)

| Kriteria Evaluasi | Opsi 1: Triple-Layer Ergonomic Architecture (CSS + JS + HTML5 + Backend) [DIREKOMENDASIKAN] | Opsi 2: Hanya CSS `text-transform: uppercase` | Opsi 3: Hanya JS Event Listener |
| :--- | :--- | :--- | :--- |
| **Mekanisme** | 1. **CSS**: `text-transform: uppercase` + `letter-spacing: 0.12em` + `font-weight: 700`.<br>2. **HTML5**: `autocapitalize="characters"` & `spellcheck="false"`.<br>3. **JS**: Event `input` auto-casing dengan kursor preservation.<br>4. **Backend**: `strtoupper(trim(...))` normalization. | Hanya menambahkan aturan CSS `text-transform: uppercase` pada input captcha. | Hanya menambahkan script `e.target.value.toUpperCase()` tanpa styling CSS atau atribut HTML5. |
| **Respon Visual (Zero Latency)** | **Instan 0ms**: Tampilan langsung kapital sebelum JS tereksekusi sekalipun. | **Instan 0ms**. | Ada sedikit jeda/kedip (*flicker*) saat huruf kecil berubah menjadi huruf besar jika CPU sibuk. |
| **Dukungan Keyboard HP (Android / iOS)** | **Sempurna**: Virtual keyboard langsung mengaktifkan tombol Shift/Caps secara otomatis karena atribut `autocapitalize="characters"`. | **Gagal**: Virtual keyboard HP tetap membuka mode huruf kecil biasa. Pengguna harus manual tap Shift. | **Gagal**: Virtual keyboard tetap huruf kecil sampai pengguna menekan huruf. |
| **Integritas Nilai Input (DOM & Form Submit)** | **100% Konsisten**: Nilai di clipboard saat di-copy, inspect element, dan form payload berhuruf besar rapi. | Nilai form payload sebenarnya di memori DOM masih huruf kecil (hanya tampilannya yang besar). | Nilai form payload berhuruf besar. |
| **Tampilan Placeholder** | **Rapi & Proporsional**: Placeholder tetap menggunakan huruf reguler (`text-transform: none`) agar tidak terkesan membentak pengguna. | Jika selector tidak teliti, teks placeholder juga ikut membesar dan berjarak renggang. | Placeholder normal. |
| **Rekomendasi Senior Dev** | **SANGAT DIREKOMENDASIKAN (OPSI 1)**. | Kurang memuaskan untuk pengguna mobile. | Kurang memuaskan secara visual. |

---

## 4. 📁 Rencana Perubahan Berkas (File Modification Plan)

| File Target | Aksi | Ringkasan Perubahan Teknis |
| :--- | :---: | :--- |
| `resources/css/pages/auth/auth-depth.css` | **Modify** | Tambahkan rule CSS `.fld-captcha`, `.captcha-input`, dan `input[name="captcha"]` dengan `text-transform: uppercase`, `font-weight: 700`, `letter-spacing: 0.12em`. Pastikan rule `::placeholder` mereset `text-transform: none`, `letter-spacing: normal`, dan `font-weight: 400`. |
| `resources/js/pages/auth/auth-depth.js` | **Modify** | Buat fungsi `initAutoCapitalizeCaptcha()` yang mendengarkan event `input` pada `input[name="captcha"]`, mengubah value ke uppercase dengan preservasi kursor, dan panggil di dalam `initAuthDepth()`. |
| `resources/views/auth/login.blade.php` | **Modify** | Tambahkan class `captcha-input`, atribut `autocapitalize="characters"`, dan `spellcheck="false"` pada field input captcha. |
| `resources/views/auth/register.blade.php` | **Modify** | Tambahkan class `captcha-input`, atribut `autocapitalize="characters"`, dan `spellcheck="false"` pada field input captcha. |
| `resources/views/components/captcha-field.blade.php` | **Modify** | Tambahkan atribut `autocapitalize="characters"`, `spellcheck="false"`, dan styling CSS scoped untuk text-transform uppercase & letter-spacing. |
| `app/Http/Controllers/Auth/AuthController.php` | **Modify** | Tambahkan normalisasi `$request->merge(['captcha' => strtoupper(trim((string) $request->input('captcha')))])` pada method `login()` dan `register()`. |
| `tests/Feature/SecurityDynamicCaptchaTest.php` | **Modify** | Tambahkan assertion pengujian bahwa input huruf kecil, huruf besar, maupun campuran diverifikasi secara akurat dan konsisten. |

---

## 5. 📝 Panduan Teknis Spesifikasi Kode (Code Specs)

### 5.1 CSS Styling (`resources/css/pages/auth/auth-depth.css`)
```css
/* CAPTCHA Input Auto-Capitalize & Monospace Spacing */
.captcha-input,
input[name="captcha"] {
  text-transform: uppercase !important;
  font-weight: 700 !important;
  letter-spacing: 0.12em !important;
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace, inherit;
}

.captcha-input::placeholder,
input[name="captcha"]::placeholder {
  text-transform: none !important;
  font-weight: 400 !important;
  letter-spacing: normal !important;
  font-family: inherit;
  color: #5F6B76;
}
```

### 5.2 JavaScript Input Handler (`resources/js/pages/auth/auth-depth.js`)
```javascript
/**
 * Auto-capitalization & live caret preservation for CAPTCHA inputs
 */
export function initAutoCapitalizeCaptcha() {
  const inputs = document.querySelectorAll('input[name="captcha"], .captcha-input');
  inputs.forEach((input) => {
    // Pastikan mobile keyboard default ke uppercase
    if (!input.hasAttribute('autocapitalize')) {
      input.setAttribute('autocapitalize', 'characters');
    }
    input.setAttribute('spellcheck', 'false');

    input.addEventListener('input', (event) => {
      const target = event.target;
      if (!target || typeof target.value !== 'string') return;

      const start = target.selectionStart;
      const end = target.selectionEnd;
      const originalValue = target.value;
      const upperValue = originalValue.toUpperCase();

      if (originalValue !== upperValue) {
        target.value = upperValue;
        if (start !== null && end !== null) {
          target.setSelectionRange(start, end);
        }
      }
    });
  });
}
```

### 5.3 Atribut Markup Blade (`login.blade.php` & `register.blade.php`)
```blade
<input 
  id="kode" 
  name="captcha" 
  class="fld captcha-input" 
  type="text" 
  maxlength="4" 
  autocomplete="off" 
  autocapitalize="characters"
  spellcheck="false"
  placeholder="4 karakter kode di atas" 
  style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 16px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF"
>
```

### 5.4 Backend Controller Normalization (`AuthController.php`)
```php
if ($request->has('captcha')) {
    $request->merge([
        'captcha' => strtoupper(trim((string) $request->input('captcha'))),
    ]);
}
```

---

## 6. 📋 Checklist Langkah Implementasi Bertahap (Implementation Checklist)

### Fase 1: Styling & CSS Layer
- [x] **Langkah 1.1**: Buka `resources/css/pages/auth/auth-depth.css`.
- [x] **Langkah 1.2**: Tambahkan styling `.captcha-input` dan `input[name="captcha"]` dengan `text-transform: uppercase`, `font-weight: 700`, dan `letter-spacing: 0.12em`.
- [x] **Langkah 1.3**: Tambahkan penyesuaian `::placeholder` agar placeholder tetap normal dan mudah dibaca.
- [x] **Langkah 1.4**: Buka `resources/views/components/captcha-field.blade.php`, tambahkan rule CSS scoped yang selaras.

### Fase 2: JavaScript Interaction & Caret Preservation Layer
- [x] **Langkah 2.1**: Buka `resources/js/pages/auth/auth-depth.js`.
- [x] **Langkah 2.2**: Tulis fungsi `initAutoCapitalizeCaptcha()`.
- [x] **Langkah 2.3**: Daftarkan pemanggilan `initAutoCapitalizeCaptcha()` di dalam `initAuthDepth()`.

### Fase 3: Markup Enhancement (HTML5 Attributes)
- [x] **Langkah 3.1**: Buka `resources/views/auth/login.blade.php`, tambahkan `class="fld captcha-input"`, `autocapitalize="characters"`, dan `spellcheck="false"`.
- [x] **Langkah 3.2**: Buka `resources/views/auth/register.blade.php`, tambahkan `class="fld captcha-input"`, `autocapitalize="characters"`, dan `spellcheck="false"`.
- [x] **Langkah 3.3**: Buka `resources/views/components/captcha-field.blade.php`, pastikan atribut `autocapitalize="characters"` dan `spellcheck="false"` terpasang.

### Fase 4: Backend Normalization & Testing
- [x] **Langkah 4.1**: Buka `app/Http/Controllers/Auth/AuthController.php`, tambahkan normalisasi `strtoupper(trim(...))` untuk input `captcha` pada `login()` dan `register()`.
- [x] **Langkah 4.2**: Buka `tests/Feature/SecurityDynamicCaptchaTest.php`, pastikan test case validasi case-insensitivity dan input kapital lulus 100%.
- [x] **Langkah 4.3**: Jalankan seluruh feature test:
  ```powershell
  php artisan test tests/Feature/SecurityDynamicCaptchaTest.php
  php artisan test tests/Feature/AuthSecurityAndPolicyTest.php
  php artisan test tests/Feature/SecuritySqlInjectionAndBruteForceLockoutTest.php
  ```
- [x] **Langkah 4.4**: Jalankan linter dan build asset:
  ```powershell
  ./vendor/bin/pint --test
  ./vendor/bin/phpstan analyse app/Http/Controllers/Auth/AuthController.php
  npm run build
  ```

---

## 7. 🧪 Skenario Pengujian (Test Specifications)

1. **Happy Path Input Lowercase**:
   - Pengguna mengetik `7kq2`.
   - Di layar langsung tampil `7KQ2` dengan font tegas dan renggang.
   - Dikirim ke backend sebagai `7KQ2`.
   - Validasi CAPTCHA sukses dan login berhasil.
2. **Happy Path Input Uppercase**:
   - Pengguna mengetik `7KQ2`.
   - Di layar tampil `7KQ2`.
   - Validasi sukses.
3. **Penyuntingan di Tengah Karakter (Caret Preservation)**:
   - Pengguna mengetik `7K2`, lalu menekan tombol panah kiri ke posisi setelah `K` dan mengetik `Q`.
   - Nilai berubah menjadi `7KQ2`, dan kursor tetap berada di antara `Q` dan `2` (tidak melompat ke ujung akhir teks).
4. **Tampilan Placeholder**:
   - Saat input kosong, placeholder *"4 karakter kode di atas"* tampil dengan huruf normal dan warna netral abu-abu.
5. **Aksesibilitas Mobile**:
   - Atribut `autocapitalize="characters"` memicu keyboard virtual smartphone untuk langsung berada dalam status CAPS.

---

## 8. ⚠️ Analisis Risiko & Mitigasi (Risk Assessment)

| Potensi Masalah | Tingkat | Mitigasi |
| :--- | :---: | :--- |
| **Placeholder Ikut Menjadi Huruf Besar & Renggang** | Rendah | Menggunakan pseudo-element `::placeholder` eksplisit dengan `text-transform: none !important; letter-spacing: normal !important;`. |
| **Kursor Meloncat ke Ujung Saat Mengetik di Tengah** | Sedang | Logika JS menyimpan `selectionStart` dan `selectionEnd` sebelum nilai diubah ke uppercase, lalu memulihkannya kembali lewat `setSelectionRange()`. |
| **Regresi Pengujian Otomatis** | Sangat Rendah | `CaptchaService` sejak awal sudah menggunakan `strtoupper()` pada pembanding string, sehingga perubahan ini backward-compatible 100%. |

---

## 9. 🏁 Kriteria Penyelesaian (Definition of Done / DoD)

- [x] Input field CAPTCHA pada `/login` dan `/register` otomatis menampilkan huruf kapital saat diketik.
- [x] Karakter teks CAPTCHA memiliki jarak renggang (`letter-spacing: 0.12em`) dan font tebal (`font-weight: 700`) yang serasi dengan badge SVG CAPTCHA.
- [x] Placeholder input tetap dalam format teks biasa (*sentence case*) tanpa terpengaruh efek kapital.
- [x] Atribut `autocapitalize="characters"` dan `spellcheck="false"` terpasang rapi di semua formulir CAPTCHA.
- [x] Posisi kursor teks tidak meloncat saat pengguna menyisipkan huruf di tengah kata.
- [x] Backend `AuthController` menerima dan menormalisasi input `captcha` menjadi uppercase.
- [x] Seluruh unit/feature test lulus 100%.
- [x] Linter Pint, static analysis PHPStan, dan `npm run build` lulus tanpa error.

