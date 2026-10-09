# Plan: Standardisasi Placeholder Auth & Redesain Visual Panel Kiri

- **Tanggal**: 2026-10-09
- **Status**: Selesai (Completed)
- **Author**: Antigravity (web-planning & web-implementation)

---

## 1. Tujuan
1. **Pembaruan Placeholder Input**: Menstandarkan nilai placeholder pada form Login dan Register sesuai arahan spesifik pengguna:
   - **Register**: `username`, `email`, `no telp`, `create password`.
   - **Login**: `email`, `password`.
2. **Redesain Visual Panel Kiri (`layouts/auth.blade.php`)**:
   - Menurunkan opacity overlay gradien dan menyelaraskan warna dengan nuansa dark hero landing page (`linear-gradient(rgba(15, 23, 42, 0.78), rgba(29, 37, 48, 0.85))`) agar foto kantor dan suasana Diskominfo Sleman tampak lebih nyata dan elegan.
   - Mempertahankan logo atas (**MAGKOM DINAS KOMINFO SLEMAN**) tanpa perubahan.
   - Memposisikan teks judul utama (**DINAS KOMUNIKASI DAN INFORMATIKA**) dan deskripsinya tepat di **tengah (center)** secara vertikal.
   - Menghapus 3 blok fitur (Transparansi Kuota, Monitoring Logbook, Penilaian Digital).
   - Memindahkan badge pill (**Sistem Magang Terpadu Sleman**) ke bagian bawah, tepat di atas baris hak cipta (*copyright footer*).

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi**:
  1. Penulisan placeholder mengikuti format huruf kecil konsisten (`username`, `email`, `no telp`, `create password`, `password`) sesuai spesifikasi teks pengguna.
  2. Logo header atas dipertahankan 100% menggunakan markup SVG dan tipografi eksisting.
  3. Tata letak panel kiri menggunakan Flexbox (`display: flex; flex-direction: column; justify-content: space-between`) di mana konten tengah otomatis berada di posisi tengah vertikal (`margin: auto 0`).
  4. Kompatibilitas dengan seluruh automated test suite tetap terjaga.
- **Pertanyaan Terbuka**: Tidak ada. Semua instruksi telah sangat spesifik dan jelas.

---

## 3. Pendekatan Desain & Arsitektur

### Panel Kiri (Visual Layout Breakdown):
```
+-------------------------------------------------------------+
| [TOP] Logo MAGKOM - DINAS KOMINFO SLEMAN (Tetap Asli)       |
|                                                             |
|                                                             |
| [CENTER]                                                    |
|   DINAS KOMUNIKASI DAN INFORMATIKA                          |
|   Platform digital terintegrasi untuk administrasi,         |
|   pemantauan, dan penilaian program magang siswa SMK...     |
|                                                             |
|                                                             |
| [BOTTOM]                                                    |
|   [• Sistem Magang Terpadu Sleman]                          |
|   -------------------------------------------------------   |
|   © 2026 Dinas Komunikasi dan Informatika Sleman...         |
+-------------------------------------------------------------+
```

### Overlay Opacity:
- Mengganti gradien biru pekat `rgba(7, 81, 255, 0.91)` - `rgba(6, 65, 204, 0.96)` menjadi gradien dark slate semi-transparan yang selaras dengan hero landing page:
  `linear-gradient(135deg, rgba(15, 23, 42, 0.76) 0%, rgba(29, 37, 48, 0.86) 100%)`.
- Foto background `auth_office_bg.png` akan terlihat lebih hidup dengan kontras teks putih yang tetap memenuhi standar aksesibilitas WCAG AA (kontras > 7:1).

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/layouts/auth.blade.php` | Modify | 1. Memperbarui `.auth-visual-overlay` dengan opacity lebih rendah bergaya dark landing hero.<br>2. Menghapus 3 feature card `.visual-features`.<br>3. Memindahkan `.brand-pill` ke bawah di atas footer.<br>4. Memposisikan headline dan deskripsi ke tengah vertikal. |
| `resources/views/auth/login.blade.php` | Modify | Memperbarui placeholder email menjadi `email` dan password menjadi `password`. |
| `resources/views/auth/register.blade.php` | Modify | Memperbarui placeholder username menjadi `username`, email menjadi `email`, no telp menjadi `no telp`, dan password menjadi `create password`. |

---

## 5. Langkah Implementasi
*Catatan: Checklist di bawah ini telah dieksekusi oleh skill `web-implementation`.*

- [x] **Langkah 1 (Layout Auth CSS & Struktur Visual)**:
  - Pada `resources/views/layouts/auth.blade.php`:
    - Perbarui `.auth-visual-overlay`:
      ```css
      .auth-visual-overlay {
          position: absolute;
          inset: 0;
          background: linear-gradient(135deg, rgba(15, 23, 42, 0.76) 0%, rgba(29, 37, 48, 0.86) 100%);
          z-index: 1;
      }
      ```
    - Tambahkan styling untuk `.visual-center-content`:
      ```css
      .visual-center-content {
          margin: auto 0;
      }
      ```
    - Perbarui struktur HTML `.auth-visual-content`:
      - Bagian atas: hanya logo MagKom Kominfo Sleman.
      - Bagian tengah: `.visual-center-content` membungkus `.visual-headline` dan `.visual-subtext`.
      - Hapus blok `.visual-features` (3 feature box).
      - Bagian bawah: `.brand-pill` diletakkan tepat di atas `.visual-footer`.
- [x] **Langkah 2 (Form Login Placeholder)**:
  - Pada `resources/views/auth/login.blade.php`:
    - Input email: `placeholder="email"`.
    - Input password: `placeholder="password"`.
- [x] **Langkah 3 (Form Register Placeholder)**:
  - Pada `resources/views/auth/register.blade.php`:
    - Input username: `placeholder="username"`.
    - Input email: `placeholder="email"`.
    - Input no_telp: `placeholder="no telp"`.
    - Input password: `placeholder="create password"`.
- [x] **Langkah 4 (Quality Gate)**:
  - Jalankan `php artisan test --filter=ReleaseThreeSocialLoginTest` untuk memvalidasi fungsi autentikasi.
  - Verifikasi tampilan visual via browser.

---

## 6. Strategi Testing
- **Unit & Integration Test**:
  - `php artisan test --filter=ReleaseThreeSocialLoginTest` (13 tests, 66 assertions) harus lulus 100%.
- **UI & Visual Verification**:
  - Visual panel kiri di layar desktop (>= 1024px):
    - Background foto tampak jelas dengan overlay gelap elegan (tidak biru pekat).
    - Logo atas tetap ada.
    - Judul "DINAS KOMUNIKASI DAN INFORMATIKA" dan deskripsi berada di tengah panel.
    - 3 item fitur sudah tidak ada.
    - Pill "Sistem Magang Terpadu Sleman" berada di bagian bawah di atas footer copyright.
  - Form login:
    - Field email bertuliskan placeholder `email`.
    - Field password bertuliskan placeholder `password`.
  - Form register:
    - Field username bertuliskan placeholder `username`.
    - Field email bertuliskan placeholder `email`.
    - Field no telp bertuliskan placeholder `no telp`.
    - Field password bertuliskan placeholder `create password`.

---

## 7. Risiko & Mitigasi
- **Kontras Teks**:
  - *Risiko*: Jika opacity overlay terlalu rendah, teks headline putih bisa sulit dibaca di atas bagian foto yang terang.
  - *Mitigasi*: Menggunakan nilai gradien `rgba(15, 23, 42, 0.76)` hingga `0.86` yang terbukti optimal pada hero landing page untuk mempertahankan rasio kontras teks > 7:1 (WCAG AAA).
- **Responsivitas**:
  - Menggunakan flex layout dengan `margin: auto 0` memastikan konten tengah selalu seimbang pada berbagai tinggi layar tanpa overflow.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Placeholder form register diperbarui (`username`, `email`, `no telp`, `create password`).
- [x] Placeholder form login diperbarui (`email`, `password`).
- [x] Opacity overlay panel kiri diturunkan dan diselaraskan dengan hero landing page.
- [x] Logo atas di panel kiri tetap utuh seperti awal.
- [x] Headline dan subtext panel kiri berada di posisi tengah secara vertikal.
- [x] Tiga item fitur di panel kiri dihapus.
- [x] Pill "Sistem Magang Terpadu Sleman" berada di bawah di atas baris copyright.
- [x] Seluruh automated test suite autentikasi lulus 100%.

---

## 9. Catatan Perubahan & Saran Lanjutan
- **Perubahan selama pengerjaan**: *(Akan dicatat saat eksekusi)*
- **Saran Lanjutan (Out of Scope)**:
  - Penambahan animasi fade-in lembut saat pergantian halaman auth.
