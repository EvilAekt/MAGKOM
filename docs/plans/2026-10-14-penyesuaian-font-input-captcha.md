# Rencana Kerja: Penyesuaian Tipografi & Font Input Kode Captcha

**Tanggal**: 14 Oktober 2026  
**Status**: 📋 Menunggu Persetujuan Pengguna  
**Dokumen Terkait**: [captcha-field.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/components/captcha-field.blade.php), [CaptchaService.php](file:///d:/MAGKOM/MAGKOM/app/Services/CaptchaService.php), [auth.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/auth.blade.php)

---

## 1. Tujuan
Menyesuaikan tipografi (font-family, ukuran font, ketebalan, letter-spacing, dan placeholder styling) saat pengguna mengisi kode verifikasi keamanan (CAPTCHA) pada halaman autentikasi (Login & Register). Tujuannya adalah membuat tampilan input field terasa modern, serasi dengan Design System MagKom (*Open Sans*), nyaman dibaca saat mengetik 4 karakter kode, dan tidak merusak visual placeholder awal.

---

## 2. Asumsi & Analisis Kondisi Saat Ini

### Kondisi Saat Ini:
1. **Input Box Captcha ([components/captcha-field.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/components/captcha-field.blade.php#L54))**:
   - Menggunakan inline style:
     ```css
     font-family: monospace, 'Courier New', Courier, sans-serif;
     font-size: 1.05rem;
     font-weight: 700;
     letter-spacing: 0.25em;
     text-transform: uppercase;
     ```
   - **Kekurangan**:
     - *Courier New* tampak kuno (*outdated*) dan kaku dibandingkan antarmuka modern MagKom.
     - `letter-spacing: 0.25em` dan `text-transform: uppercase` pada elemen `<input>` ikut memengaruhi teks placeholder (`"4 digit kode di atas"`), sehingga placeholder terlihat renggang berlebihan dan terpotong ke kiri mendekati ikon gembok/perisai.
2. **Gambar SVG Captcha ([app/Services/CaptchaService.php](file:///d:/MAGKOM/MAGKOM/app/Services/CaptchaService.php#L105))**:
   - Menggunakan:
     ```svg
     font-family="monospace, 'Courier New', Courier, sans-serif"
     ```
   - Tampilan visual huruf pada gambar captcha dapat diselaraskan dengan font sistem sans-serif/modern monospace yang lebih tegas dan bersih.

---

## 3. Pendekatan Desain

### Opsi A: Modern Monospace Stack + Normalisasi Placeholder (Direkomendasikan ⭐)
- **Karakteristik**:
  - Menggunakan stack modern monospace: `ui-monospace, 'SF Mono', Menlo, Consolas, 'Liberation Mono', monospace`.
  - Keuntungan utama: Setiap karakter memiliki lebar yang konsisten (*fixed-width*), memudahkan pembedaan huruf dan angka penting (seperti `2`, `7`, `B`, `K`, `M`, `P`, `Q`, `X`), tidak membuat kursor melompat dengan lebar variabel.
  - Teks placeholder dinormalkan ke font sistem UI MagKom (`font-family: inherit; letter-spacing: normal; text-transform: none; font-weight: 400; font-size: 0.875rem;`).
  - Saat mulai diketik, input otomatis menampilkan huruf tebal kapital rapi dengan letter-spacing proporsional (`0.2em`).

### Opsi B: Pure Design System Font (*Open Sans / Inter*)
- **Karakteristik**:
  - Menggunakan font `Open Sans, sans-serif` identik dengan seluruh form input portal MagKom.
  - Keuntungan: Sangat selaras secara visual dengan field email dan password.
  - Kelemahan: Lebar karakter bervariasi (*proportional*), sehingga input 4 karakter tidak selebar dan setegas font monospace keamanan.

> **Rekomendasi**: Mengadopsi **Opsi A**, dengan memindahkan styling inline ke kelas khusus `.captcha-input` di dalam style tag komponen Blade, serta menyelaraskan font family pada generator gambar SVG di `CaptchaService.php`.

---

## 4. Rincian Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [resources/views/components/captcha-field.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/components/captcha-field.blade.php) | Ubah | Mengganti inline style pada `<input id="captcha">` dengan class `.captcha-input`, menambahkan aturan styling khusus untuk font modern monospace saat mengetik dan normalisasi teks placeholder saat kosong. |
| [app/Services/CaptchaService.php](file:///d:/MAGKOM/MAGKOM/app/Services/CaptchaService.php) | Ubah | Menyesuaikan atribut `font-family` pada elemen `<text>` SVG agar menggunakan stack modern (*ui-monospace, Consolas, 'Courier New', monospace*) untuk keselarasan visual antara gambar kode dan field input. |

---

## 5. Langkah Implementasi Terurut

- [x] **Langkah 1**: Refaktor styling input captcha pada [resources/views/components/captcha-field.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/components/captcha-field.blade.php)
  - Hapus inline style yang kaku pada elemen `<input id="captcha">`.
  - Tambahkan class `.captcha-input` pada input element.
  - Definisikan rule CSS `.captcha-input`:
    - `font-family: ui-monospace, 'SF Mono', Menlo, Consolas, 'Liberation Mono', monospace;`
    - `font-size: 1.125rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase;`
  - Tambahkan pseudo-class selector `.captcha-input::placeholder`:
    - `font-family: 'Open Sans', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;`
    - `font-size: 0.875rem; font-weight: 400; letter-spacing: normal; text-transform: none;`
- [x] **Langkah 2**: Perbarui atribut font pada SVG di [app/Services/CaptchaService.php](file:///d:/MAGKOM/MAGKOM/app/Services/CaptchaService.php)
  - Perbarui nilai `font-family` pada generator `<text>` agar konsisten dan tajam di berbagai platform (Windows/Mac/Linux).
- [x] **Langkah 3**: Verifikasi Dark Mode & Light Mode
  - Pastikan kontras warna teks dan warna placeholder tetap jelas dan nyaman dibaca baik pada mode terang (`data-theme="light"`) maupun mode malam (`data-theme="dark"`).
- [x] **Langkah 4**: Jalankan Automated Tests
  - Eksekusi `SecurityDynamicCaptchaTest.php` dan `ReleaseTwoFeaturesTest.php` untuk memastikan fungsi verifikasi dan sanitasi kode tidak terganggu.

---

## 6. Strategi Testing

1. **Automated Unit & Feature Test**:
   - Jalankan `php artisan test tests/Feature/SecurityDynamicCaptchaTest.php`.
   - Memastikan generator SVG tetap menghasilkan kode yang valid dan lulus tes session.
2. **Manual Visual Testing**:
   - Buka halaman `/login` dan `/register` di browser.
   - Cek tampilan saat input kosong: pastikan placeholder `"4 digit kode di atas"` tampil wajar, tidak renggang/terpotong.
   - Cek tampilan saat pengguna mengetik: pastikan karakter berupa huruf kapital tebal yang proporsional, mudah dibaca, dan tidak terdistorsi.
   - Uji pergantian tema gelap (Dark Mode) dan terang (Light Mode).

---

## 7. Risiko & Mitigasi

- **Risiko**: Ukuran teks yang terlalu besar bisa menabrak padding icon gembok kiri (`.input-icon-left`).
  - *Mitigasi*: Menjaga padding kiri standar `padding-left: 2.85rem` dan mengatur `letter-spacing` proporsional di angka `0.2em` sehingga 4 karakter pas berada di tengah area input.
- **Risiko**: Browser lama yang tidak mendukung `ui-monospace`.
  - *Mitigasi*: Menyediakan fallback lengkap (`Menlo, Consolas, 'Liberation Mono', monospace`).

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Placeholder input captcha tampil natural dengan font *Open Sans* tanpa distorsi *letter-spacing*.
- [x] Teks yang diketikkan pengguna saat mengisi captcha berpenampilan modern, rapi, dan terbaca jelas (*uppercase bold monospace*).
- [x] Gambar SVG captcha dan input form memiliki konsistensi tipografi yang baik.
- [x] Berfungsi optimal dan rapi pada mode terang dan mode gelap.
- [x] Seluruh unit/feature test keamanan captcha lulus (`100% PASS`).
