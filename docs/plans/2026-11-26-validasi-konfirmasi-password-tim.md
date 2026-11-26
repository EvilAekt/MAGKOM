# Rencana Kerja: Sinkronisasi & Validasi Kesamaan Kata Sandi (Password Matching) Pendaftaran Tim

**Dokumen Rencana:** `docs/plans/2026-11-26-validasi-konfirmasi-password-tim.md`  
**Tanggal:** 26 November 2026  
**Status:** Selesai Diimplementasikan (Completed & Verified)  
**Terkait:** Form Pendaftaran Peserta & Rekan Anggota Tim (`resources/views/auth/register.blade.php`, `resources/js/pages/auth/auth-depth.js`, `app/Http/Controllers/Auth/AuthController.php`)

---

## 1. Tujuan
Menyempurnakan logika validasi kesamaan kata sandi (**Kata Sandi Akun** dan **Ulangi Kata Sandi**) pada saat pendaftar mengetik (*real-time feedback*) serta saat formulir dikirim (*client-side & server-side validation*), baik untuk Ketua Tim maupun seluruh Rekan Anggota Tim pada pendaftaran jalur kelompok.

Hasil yang diharapkan:
1. **Feedback Real-time Saat Mengetik**: Saat pengguna mengetik di kolom *"Ulangi Kata Sandi"* untuk rekan anggota tim:
   - Jika cocok: border input menjadi hijau (`#86EFAC`) dan muncul teks feedback hijau `✓ Konfirmasi kata sandi cocok.`
   - Jika belum cocok: border input menjadi merah (`#FCA5A5`) dan muncul teks peringatan merah `Kata sandi belum cocok.`
   - Jika kosong: border kembali normal (`#AAAAAA`) dan teks feedback disembunyikan.
2. **Client-side Submission Guard**: Mencegah pengiriman formulir jika terdapat ketidakcocokan kata sandi pada Ketua maupun Rekan Anggota Tim, dengan langsung memfokuskan field yang salah dan menampilkan pesan error instan tanpa perlu reload/round-trip jaringan.
3. **Server-side Robustness**: Memastikan backend (`AuthController.php`) tetap memvalidasi aturan `confirmed` untuk setiap anggota dengan pesan error spesifik dan user-friendly.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. Pola visual feedback mengikuti standar yang sudah diterapkan pada kolom Ketua Tim (`#hint-sandi-confirmation` dengan warna `#16A34A` untuk cocok dan `#DC2626` untuk tidak cocok).
2. Mekanisme feedback real-time harus mendukung penambahan atau penghapusan rekan anggota secara dinamis (menggunakan *event delegation* pada event `input` di container `#roster-anggota-container`).
3. Seluruh styling baru ditampung di `resources/css/pages/auth/auth-depth.css` dan logika interaksi di `resources/js/pages/auth/auth-depth.js`.

---

## 3. Pendekatan Teknis & Evaluasi Arsitektur

### Evaluasi Opsi:

- **Opsi 1: Unified Event Delegation + Semantik Hint Element (Direkomendasikan)**
  - *Cara kerja*:
    1. Menyediakan elemen hint semantik `<p class="card-anggota-pw-hint" data-anggota-hint="{{ $i }}">` di bawah grid kata sandi rekan anggota tim.
    2. Memperluas fungsi `initPasswordConfirmationMatch()` di `resources/js/pages/auth/auth-depth.js` dengan listener delegasi `input` pada `#roster-anggota-container`.
    3. Menambahkan *pre-submit check* di `initAjaxRegisterForm()` untuk memvalidasi kesamaan kata sandi Ketua dan seluruh Rekan Anggota Tim sebelum payload dikirim via fetch.
    4. Menyempurnakan penempatan error response backend di `initAjaxRegisterForm()` agar tidak merusak layout grid input password.
  - *Kelebihan*: Responsif instan (zero latency), konsisten antara Ketua dan Anggota Tim, tahan terhadap penambahan dinamis kartu anggota, ramah aksesibilitas (a11y), dan teruji end-to-end.
  - *Kekurangan*: Memerlukan penambahan elemen hint kecil di Blade (membutuhkan persetujuan pengguna sesuai AGENTS.md).

- **Opsi 2: Pure Server-side Validation Round-trip (Tanpa Feedback Real-time)**
  - *Cara kerja*: Mengandalkan hanya validasi Laravel di backend saat tombol submit diklik.
  - *Kelebihan*: Tidak memerlukan penambahan elemen di Blade.
  - *Kekurangan*: Pengalaman pengguna buruk (user harus menunggu submit sebelum tahu kata sandi tidak cocok), tidak memenuhi permintaan user ("saat mengisi password dan mengisi password ulang harus sama"), dan rentan frustrasi pendaftar.

**Rekomendasi:** **Opsi 1**.

---

## 4. Analisis Kebutuhan Perubahan Blade (Patuhi AGENTS.md Bagian 3.3)

Sesuai aturan **AGENTS.md Section 3.3**:
1. **Alasan Perubahan**: Diperlukan satu baris elemen semantik teks petunjuk (`<p class="card-anggota-pw-hint" ...>`) di bawah baris input kata sandi rekan anggota tim agar pesan status kecocokan kata sandi dapat dibaca jelas oleh pengguna dan screen-reader tanpa merusak layout grid 2 kolom.
2. **File yang Perlu Diubah**: `resources/views/auth/register.blade.php` (di dalam loop `@for($i = 0; $i < $maksRekanTambahan; $i++)`).
3. **Pemberitahuan**: Penambahan ini diajukan secara eksplisit dalam rencana kerja ini untuk mendapatkan persetujuan pengguna sebelum dilakukan modifikasi.

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/auth/register.blade.php` | Modify | Menambahkan elemen semantik `<p class="card-anggota-pw-hint" data-hint-index="{{ $i }}"></p>` di bawah grid kata sandi masing-masing rekan anggota tim. |
| `resources/css/pages/auth/auth-depth.css` | Modify | Menambahkan styling `.card-anggota-pw-hint` dengan font compact, transisi warna, dan penataan margin harmonis. |
| `resources/js/pages/auth/auth-depth.js` | Modify | 1. Memperluas `initPasswordConfirmationMatch()` untuk memantau input kata sandi rekan anggota tim secara delegasi.<br>2. Menambahkan *pre-submit validation guard* di `initAjaxRegisterForm()` untuk mencegah submit jika ada ketidakcocokan kata sandi.<br>3. Merapikan penempatan error backend untuk field anggota tim. |
| `app/Http/Controllers/Auth/AuthController.php` | Modify | Memastikan aturan `anggota.*.password` memvalidasi `confirmed` dan mencakup pesan error bahasa Indonesia yang jelas. |
| `tests/Feature/Auth/RegisterPasswordConfirmationTest.php` | Modify | Menambahkan test case pengujian validasi kesamaan kata sandi anggota tim (gagal jika beda, berhasil jika sama). |

---

## 6. Langkah Implementasi Terperinci

- [x] **Langkah 1: Perbarui CSS di `resources/css/pages/auth/auth-depth.css`**
  - Definisikan class `.card-anggota-pw-hint` (font-size: 12px, margin-top: 4px, line-height: 1.35, transisi warna).
  - Pastikan styling input `.is-valid-match` (`border-color: #86EFAC !important;`) dan `.is-invalid-match` (`border-color: #FCA5A5 !important;`).
- [x] **Langkah 2: Perbarui Markup di `resources/views/auth/register.blade.php` (Setelah Izin)**
  - Letakkan elemen `<p class="card-anggota-pw-hint" data-hint-index="{{ $i }}" style="display: none;"></p>` tepat setelah `.card-anggota-grid` password pada kartu rekan anggota.
- [x] **Langkah 3: Perbarui Logika JavaScript di `resources/js/pages/auth/auth-depth.js`**
  - Di `initPasswordConfirmationMatch()`: tambahkan event listener `input` pada level `#roster-anggota-container` yang mendeteksi perubahan nilai pada input password atau confirmation milik kartu anggota.
  - Perbarui border input dan teks hint kartu secara instan:
    - Jika konfirmasi kosong: sembunyikan hint, kembalikan border ke normal.
    - Jika cocok: tampilkan teks `✓ Konfirmasi kata sandi cocok.` (warna hijau `#16A34A`), border `#86EFAC`.
    - Jika tidak cocok: tampilkan teks `Kata sandi belum cocok.` (warna merah `#DC2626`), border `#FCA5A5`.
  - Di `initAjaxRegisterForm()`: tambahkan validasi sebelum `fetch`:
    - Periksa password Ketua (`sandi` vs `sandi_confirmation`).
    - Periksa password setiap kartu rekan tim yang sedang tampil/aktif.
    - Jika ada yang tidak cocok, batalkan submit, munculkan alert, dan beri fokus pada field yang tidak cocok.
- [x] **Langkah 4: Periksa & Selaraskan Backend di `AuthController.php`**
  - Pastikan validasi `anggota.*.password` memiliki aturan `confirmed` yang aktif saat password diisi.
  - Pastikan pesan error kustom `'anggota.*.password.confirmed' => 'Konfirmasi kata sandi rekan anggota tim tidak cocok.'` selalu terpasang.
- [x] **Langkah 5: Kompilasi Aset Vite (`npm run build`)**
  - Jalankan `npm run build` dan verifikasi keberhasilan kompilasi tanpa error.
- [x] **Langkah 6: Perbarui dan Jalankan Test Suite PHPUnit & Pint**
  - Tambahkan skenario pengujian pada `RegisterPasswordConfirmationTest.php`:
    - Uji kegagalan registrasi kelompok jika password konfirmasi anggota tidak cocok.
    - Uji keberhasilan registrasi kelompok jika password konfirmasi anggota cocok.
  - Jalankan `php artisan test tests/Feature/Auth/RegisterPasswordConfirmationTest.php`.
  - Jalankan `./vendor/bin/pint --test`.
- [x] **Langkah 7: Verifikasi Manual & Interaksi Browser**
  - Uji ketik di formulir pendaftaran tab Tim:
    - Ketik password beda: pastikan border merah dan teks "Kata sandi belum cocok" muncul langsung.
    - Samakan password: pastikan border hijau dan teks "✓ Konfirmasi kata sandi cocok" muncul langsung.
    - Uji klik tombol submit dalam kondisi tidak cocok: pastikan pengiriman dicegah dan field disorot.

---

## 7. Strategi Testing

1. **Feature Testing (PHPUnit 10)**:
   - `test_registration_fails_when_team_member_password_confirmation_does_not_match`: memverifikasi respon 422 / error session `Konfirmasi kata sandi rekan anggota tim tidak cocok.` saat password anggota tidak cocok.
   - `test_registration_succeeds_when_team_member_passwords_match`: memverifikasi pendaftaran kelompok berhasil dan akun seluruh anggota terdaftar saat password cocok.
2. **Quality Gate Checks**:
   - `npm run build`: 100% lulus.
   - `./vendor/bin/pint --test`: 100% kepatuhan PSR-12.

---

## 8. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Kartu anggota ditambah/dihapus dinamis** | Listener input hilang jika diikat langsung ke elemen saat load. | Menggunakan *Event Delegation* pada `#roster-anggota-container` sehingga kartu baru otomatis ter-cover. |
| **Pendaftar hanya mengisi sebagian rekan** | Validasi error pada kartu yang tidak diisi. | Hanya memvalidasi kartu rekan tim yang berstatus aktif (`display !== 'none'` dan `!disabled`). |
| **Error backend memecah tata letak input** | Text error dari AJAX merusak grid 2 kolom. | Menempatkan elemen error container di bawah grid atau menggunakan hint element yang sudah ada. |

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Dokumen rencana kerja disetujui pengguna.
- [x] Terdapat visual feedback real-time (hijau/merah) saat mengetik kata sandi & ulangi kata sandi pada kartu rekan anggota tim.
- [x] Submit dicegah pada client-side jika ada ketidakcocokan kata sandi rekan anggota tim.
- [x] Backend menolak pendaftaran dengan error ramah jika password anggota tidak cocok.
- [x] Seluruh CSS di `resources/css/` dan JS di `resources/js/`.
- [x] `npm run build` sukses.
- [x] Seluruh test suite terkait lulus (`php artisan test`).
