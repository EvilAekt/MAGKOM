# Rencana Kerja: Fitur Lihat Kata Sandi (Password Visibility Toggle) Pendaftaran Anggota Tim / Kelompok

**Dokumen Rencana:** `docs/plans/2026-11-26-toggle-password-pendaftaran-kelompok.md`  
**Tanggal:** 26 November 2026  
**Status:** Selesai Diimplementasikan (Completed & Verified)  
**Terkait:** Form Pendaftaran Akun Peserta & Anggota Tim (`resources/views/auth/register.blade.php`)

---

## 1. Tujuan
Menyediakan tombol interaktif "lihat/sembunyikan kata sandi" (toggle visibility dengan ikon mata) pada kolom **Kata Sandi Akun** dan **Ulangi Kata Sandi** untuk setiap rekan anggota tim pada pendaftaran kelompok di halaman registrasi. Fitur ini menyamakan pengalaman pengguna (UX parity) dengan kolom kata sandi Ketua Tim yang sudah memiliki fitur toggle kata sandi.

---

## 2. Asumsi & Investigasi Awal

### Asumsi Kerja:
1. Fitur toggle kata sandi berlaku untuk kedua kolom: `anggota[i][password]` dan `anggota[i][password_confirmation]` pada semua kartu rekan anggota tim (baik yang tampil langsung maupun yang ditambah dinamis via tombol *"Tambah Rekan Anggota"*).
2. Perilaku toggle mengikuti standar yang sudah ada di halaman autentikasi MAGKOM (`resources/js/pages/auth/auth-depth.js`):
   - Klik pertama: Mengubah `type="password"` menjadi `type="text"`, menyembunyikan ikon mata terbuka (`.pw-icon-show`), menampilkan ikon mata dicoret (`.pw-icon-hide`), dan memperbarui `aria-label="Sembunyikan kata sandi"`.
   - Klik kedua: Mengembalikan ke `type="password"`, menampilkan `.pw-icon-show`, menyembunyikan `.pw-icon-hide`, dan memperbarui `aria-label="Tampilkan kata sandi"`.
3. Sesuai aturan **AGENTS.md**, penambahan CSS diletakkan di `resources/css/pages/auth/auth-depth.css` dan interaksi di `resources/js/pages/auth/auth-depth.js`. Tidak ada inline CSS/JS baru di Blade.

---

## 3. Pendekatan Teknis & Evaluasi Arsitektur

### Evaluasi Opsi:

- **Opsi 1: Markup Semantik di Blade + Event Handling & Scope CSS di Asset Terdedikasi (Direkomendasikan)**
  - *Cara kerja*: Membungkus input kata sandi anggota dalam kontainer relatif semantik (`.card-anggota-pw-wrap`) dengan tombol `<button type="button" class="btn-ico btn-toggle-pw" data-action="toggle-password" ...>` di `resources/views/auth/register.blade.php`. Styling diatur rapi di `resources/css/pages/auth/auth-depth.css`. Interaksi didukung via event delegation di `resources/js/pages/auth/auth-depth.js`.
  - *Kelebihan*: Zero layout shift (CLS), aksesibilitas keyboard dan screen-reader (WCAG 2.1 AA) terjamin sejak server-side rendering, mudah diuji secara otomatis via PHPUnit (`assertSee`), serta konsisten dengan field kata sandi ketua tim.
  - *Kekurangan*: Memerlukan modifikasi kecil pada markup Blade (wajib persetujuan pengguna sesuai AGENTS.md).

- **Opsi 2: Injeksi Tombol Dinamis 100% via JavaScript (DOM Manipulation)**
  - *Cara kerja*: Blade dibiarkan tanpa perubahan sama sekali; JavaScript saat runtime membuat elemen tombol dan wrapper, lalu menyisipkannya ke setiap input anggota.
  - *Kelebihan*: Tidak menyentuh file Blade.
  - *Kekurangan*: Berisiko Cumulative Layout Shift (CLS) saat hydration, tombol berkedip (FOUC), rapuh saat user menambah/menghapus baris anggota secara dinamis, serta tidak dapat diverifikasi oleh automated HTTP feature test (PHPUnit) tanpa headless browser.

**Rekomendasi:** **Opsi 1**, karena stabil, tangguh, performan, dan ramah aksesibilitas.

---

## 4. Analisis Kebutuhan Perubahan Blade (Patuhi AGENTS.md Bagian 3.3)

Sesuai aturan **AGENTS.md Section 3.3 (Pengecualian Perubahan Blade)**:
1. **Alasan Perubahan**: Input password anggota saat ini berposisi telanjang di dalam grid (`<input class="fld" ...>`). Agar tombol ikon mata dapat diposisikan tepat di sisi kanan input secara absolut tanpa merusak tata letak grid dan tetap responsif di mobile, diperlukan kontainer pembungkus (`.card-anggota-pw-wrap`) dan elemen semantik `<button type="button">`.
2. **File yang Perlu Diubah**: `resources/views/auth/register.blade.php` (baris 155–158).
3. **Pemberitahuan & Izin**: Langkah ini diajukan secara transparan kepada pengguna dalam rencana kerja ini untuk mendapatkan persetujuan eksplisit sebelum kode diubah.

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/auth/register.blade.php` | Modify | Membungkus input kata sandi & konfirmasi anggota dalam wrapper dan menambahkan tombol `<button type="button" data-action="toggle-password">` dengan atribut aksesibilitas lengkap. |
| `resources/css/pages/auth/auth-depth.css` | Modify | Menambahkan class CSS `.card-anggota-pw-wrap` dan `.btn-toggle-pw-compact` (ukuran kompak `36x36px`, padding input kanan `36px` agar teks tidak tertimpa ikon). |
| `resources/js/pages/auth/auth-depth.js` | Modify | 1. Memastikan `initAuthInteractions()` mendukung event delegation pada dynamic cards.<br>2. Memperbaiki selector di `updateRosterState()` agar tidak gagal mengidentifikasi field saat tipenya sedang diubah dari `password` ke `text`. |
| `tests/Feature/Auth/RegisterPasswordConfirmationTest.php` | Modify | Menambahkan assertion uji server rendering untuk tombol toggle kata sandi rekan anggota tim. |

---

## 6. Langkah Implementasi Terperinci

- [x] **Langkah 1: Perbarui CSS di `resources/css/pages/auth/auth-depth.css`**
  - Definisikan `.card-anggota-pw-wrap` dengan `position: relative; width: 100%;`.
  - Definisikan `.card-anggota-pw-wrap .fld-pw-compact` dengan `padding-right: 38px;`.
  - Definisikan `.btn-toggle-pw-compact` dengan ukuran `34px x 34px`, posisi `absolute; right: 3px; top: 3px;` dengan efek hover dan focus outline yang harmonis.
- [x] **Langkah 2: Perbarui Markup di `resources/views/auth/register.blade.php` (Setelah Izin)**
  - Terapkan wrapper dan tombol toggle pada input `anggota[{{ $i }}][password]` (`id="anggota_{{ $i }}_password"`).
  - Terapkan wrapper dan tombol toggle pada input `anggota[{{ $i }}][password_confirmation]` (`id="anggota_{{ $i }}_password_confirmation"`).
  - Pastikan SVG ikon mata (`.pw-icon-show` dan `.pw-icon-hide`) disertakan lengkap dengan `aria-hidden="true"` dan `aria-label` yang ramah pembaca layar.
- [x] **Langkah 3: Perbarui Logika JavaScript di `resources/js/pages/auth/auth-depth.js`**
  - Pastikan click handler untuk `[data-action="toggle-password"]` menggunakan event delegation pada container atau dokumen, sehingga aman saat rekan anggota baru ditambahkan via tombol dinamis.
  - Perbaiki selector di `updateRosterState()`: ubah pencarian input kata sandi dari mengandalkan `input[type="password"]` menjadi selektor yang tahan terhadap perubahan tipe teks, misalnya `input[name*="[password]"]`.
- [x] **Langkah 4: Jalankan Build Asset Vite**
  - Jalankan `npm run build` dan pastikan manifest serta bundle terkompilasi tanpa error.
- [x] **Langkah 5: Perbarui dan Jalankan Test Suite**
  - Tambahkan pengujian pada `tests/Feature/Auth/RegisterPasswordConfirmationTest.php` untuk memastikan elemen toggle anggota dirender dengan benar.
  - Jalankan `php artisan test tests/Feature/Auth/RegisterPasswordConfirmationTest.php`.
  - Jalankan linting dengan `./vendor/bin/pint --test`.
- [x] **Langkah 6: Verifikasi Visual & Interaksi di Browser**
  - Uji langsung halaman pendaftaran dengan memilih tab Kelompok/Tim:
    - Verifikasi klik tombol mata pada Kata Sandi Rekan Anggota #1: teks terlihat/tersembunyi.
    - Verifikasi klik tombol mata pada Ulangi Kata Sandi Rekan Anggota #1.
    - Klik tombol "Tambah Rekan Anggota", lalu verifikasi toggle pada Rekan Anggota #2 berjalan lancar.

---

## 7. Strategi Testing

1. **Feature Testing (PHPUnit 10 - Isolated)**:
   - `test_register_view_renders_two_password_fields_and_toggle_buttons`: diperluas untuk menguji keberadaan `data-action="toggle-password"` pada field anggota kelompok.
   - Memastikan tidak ada regresi pada validasi form dan proses registrasi.
2. **Quality Gate Checks**:
   - `npm run build`: Kompilasi CSS/JS berhasil 100%.
   - `./vendor/bin/pint --test`: Tidak ada pelanggaran standar kode PSR-12.
3. **Manual / Functional Check**:
   - Mengetik kata sandi di field anggota, mengklik tombol mata, memastikan karakter berubah dari bullet (dots) menjadi teks terbaca dan sebaliknya.
   - Menambahkan rekan baru, menghapus rekan, dan memastikan ID/name serta toggle tetap sinkron dan tidak rusak.

---

## 8. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Pencarian input rusak saat `type` berubah ke `text`** | Re-indexing input saat tambah/hapus anggota bisa terganggu jika query selector mengandalkan `[type="password"]`. | Menggunakan selektor berbasis atribut nama/posisi yang stabil seperti `input[name*="[password]"]`. |
| **Tombol menimpa teks panjang kata sandi** | Teks kata sandi tertutup oleh tombol ikon mata jika padding kanan tidak cukup. | Menambahkan padding kanan khusus (`padding-right: 38px`) pada input di dalam `.card-anggota-pw-wrap`. |
| **Event listener hilang saat card dinamis diaktifkan** | Tombol pada card anggota yang baru dimunculkan tidak merespons klik. | Menggunakan *Event Delegation* pada `rosterContainer` atau level dokumen. |

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Dokumen rencana kerja disetujui oleh pengguna.
- [x] Ikon toggle mata tampil di kedua input kata sandi setiap kartu rekan anggota tim.
- [x] Klik ikon mata berhasil menampilkan dan menyembunyikan kata sandi secara responsif.
- [x] Penambahan dan penghapusan rekan anggota dinamis tidak merusak fungsi toggle.
- [x] Seluruh CSS berada di `resources/css/pages/auth/auth-depth.css` dan JS di `resources/js/pages/auth/auth-depth.js`.
- [x] `npm run build` sukses tanpa warning/error.
- [x] PHPUnit test terkait lolos (`assertOk`, `assertSee`).
- [x] Tidak ada regresi pada fungsionalitas registrasi kelompok.
