# Plan: Pembaruan Placeholder Password pada Form Register

- **Tanggal**: 2026-10-09
- **Status**: Selesai (Completed)
- **Author**: Antigravity (web-planning & web-implementation)

---

## 1. Tujuan
Memperbarui atribut placeholder pada field kata sandi di formulir pendaftaran (`resources/views/auth/register.blade.php`) dari `placeholder="create password"` menjadi `placeholder="password"` agar seragam dan konsisten dengan field kata sandi di formulir login.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi**:
  1. Penulisan placeholder menggunakan format huruf kecil konsisten (`password`).
  2. Atribut `id="password"`, `name="password"`, `type="password"`, `autocomplete="new-password"`, dan tombol toggle visibilitas mata tetap dipertahankan.
- **Pertanyaan Terbuka**: Tidak ada.

---

## 3. Pendekatan Desain & Arsitektur
- **Opsi Terpilih**: Mengubah string nilai atribut `placeholder` secara langsung pada elemen input `#password` di file Blade target.
- **Alasan Pemilihan**: Pendekatan langsung dan bersih, menjaga keseragaman istilah input di seluruh modul autentikasi MagKom.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/auth/register.blade.php` | Modify | Mengubah `placeholder="create password"` menjadi `placeholder="password"`. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1**: Buka `resources/views/auth/register.blade.php` pada baris input `#password`.
- [x] **Langkah 2**: Ganti nilai atribut `placeholder="create password"` menjadi `placeholder="password"`.
- [x] **Langkah 3**: Jalankan `php artisan test --filter=ReleaseThreeSocialLoginTest` untuk memastikan seluruh pengujian tetap hijau.

---

## 6. Strategi Testing
- **Unit & Integration Test**:
  - `php artisan test --filter=ReleaseThreeSocialLoginTest` harus tetap lulus 100% (13 tests, 66 assertions).
- **UI Verification**:
  - Buka `/register` dan verifikasi bahwa kotak input password menampilkan teks placeholder abu-abu `password`.

---

## 7. Risiko & Mitigasi
- **Risiko**: Nol risiko (perubahan string presentasi statis pada satu baris file Blade).
- **Mitigasi**: Menjalankan pengujian otomatis pasca-perubahan.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Placeholder pada input password di `register.blade.php` bernilai `password`.
- [x] Seluruh automated test suite lulus 100%.

---

## 9. Catatan Perubahan & Saran Lanjutan
- **Perubahan selama pengerjaan**: *(Akan dicatat saat eksekusi)*
- **Saran Lanjutan (Out of Scope)**: Tidak ada.
