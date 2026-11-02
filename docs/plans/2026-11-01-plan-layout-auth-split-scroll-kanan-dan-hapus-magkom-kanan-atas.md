# Rencana Kerja: Layout Autentikasi Split-Screen (Scroll Mandiri Panel Kanan) & Penghapusan Teks Brand MAGKOM di Kanan Atas

- **Tanggal**: 2026-11-01
- **Status**: Selesai / Terverifikasi (Passed 100%)
- **Dokumen Referensi**:
  - `resources/views/layouts/auth.blade.php`
  - `resources/views/auth/register.blade.php`
  - `resources/views/auth/login.blade.php`

---

## 1. Tujuan
1. **Scroll Mandiri Panel Kanan (Independent Form Scroll)**:
   - Panel visual di sebelah kiri (gambar kantor Kominfo, headline, dan identitas resmi) dibuat **tetap statis / terkunci 100vh** (tidak ikut terseret atau meregang saat form di-scroll).
   - Panel form di sebelah kanan (tempat form login, register, dan reset password) menjadi **satu-satunya area yang memiliki scroll vertikal mandiri** (`overflow-y: auto`).
2. **Penghapusan Teks "MAGKOM" di Bagian Kanan Atas**:
   - Menghilangkan teks "MAGKOM" yang muncul di header bar panel kanan.
   - Menyederhanakan judul form di panel kanan dari *"Register Akun MagKom"* menjadi *"Register Akun"* dan *"Selamat Datang di MagKom"* menjadi *"Selamat Datang"*.

---

## 2. Asumsi & Analisis Teknis

### A. Asumsi Kerja:
1. **Layar Desktop (>= 1024px)**:
   - `.auth-container` memiliki tinggi tetap `100vh` dengan `overflow: hidden`.
   - `.auth-visual-panel` (kiri) memiliki tinggi `100vh` statis tanpa scroll.
   - `.auth-form-panel` (kanan) memiliki tinggi `100vh` dengan `overflow-y: auto`, dilengkapi styling scrollbar yang rapi dan halus.
2. **Layar Mobile (< 1024px)**:
   - Panel visual kiri tersembunyi seperti rancangan responsive saat ini, dan panel kanan mengisi layar penuh dengan scroll alami perangkat mobile.
3. **Penghapusan MAGKOM Kanan Atas**:
   - Di panel kanan, logo dan branding instansi Sleman di sisi kiri visual panel sudah sangat jelas. Keberadaan kata "MAGKOM" di header bar / sudut kanan atas panel form bersifat redundan dan dapat dihapus sehingga menyisakan tombol kembali (*Back Link*) yang bersih.

---

## 3. Pendekatan Teknis & Perbandingan Opsi

| Opsi | Kelebihan | Kekurangan | Rekomendasi |
| :--- | :--- | :--- | :--- |
| **Opsi 1: CSS Grid 100vh + Flexbox Scroll Container** | Standar modern CSS, performa rendering GPU tinggi, kompatibel dengan seluruh browser, tidak ada jitter / layout shift. | Memerlukan penataan padding internal agar footer form tidak terpotong saat scroll mentok. | **Direkomendasikan** |
| **Opsi 2: JavaScript Window Scroll Locking** | Mengunci scroll window via event listener JS. | Boros resource, rawan glitch pada trackpad dan mobile, rentan bug aksesibilitas keyboard. | Tidak disarankan |

### Detail Perubahan CSS:
```css
@media (min-width: 1024px) {
    .auth-container {
        grid-template-columns: 1fr 1fr;
        height: 100vh;
        max-height: 100vh;
        overflow: hidden;
    }

    .auth-visual-panel {
        display: flex;
        height: 100vh;
        max-height: 100vh;
        position: sticky;
        top: 0;
        overflow: hidden;
    }

    .auth-form-panel {
        height: 100vh;
        max-height: 100vh;
        overflow-y: auto;
        overflow-x: hidden;
        scroll-behavior: smooth;
    }
}
```

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [resources/views/layouts/auth.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/auth.blade.php) | Modify | 1. Terapkan `height: 100vh; overflow: hidden;` pada `.auth-container` di desktop.<br>2. Jadikan `.auth-visual-panel` statis terkunci 100vh.<br>3. Terapkan `height: 100vh; overflow-y: auto;` pada `.auth-form-panel` dengan custom scrollbar halus.<br>4. Hapus teks `MAGKOM` pada `.mobile-brand` / header bar panel kanan sehingga header bar bersih. |
| [resources/views/auth/register.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/auth/register.blade.php) | Modify | Ubah judul utama dari `Register Akun MagKom` menjadi `Register Akun`. |
| [resources/views/auth/login.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/auth/login.blade.php) | Modify | Ubah judul utama dari `Selamat Datang di MagKom` menjadi `Selamat Datang`. |
| [tests/Feature/PendaftaranKelompokTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/PendaftaranKelompokTest.php) | Modify (jika ada assert judul) | Pastikan assertion judul disesuaikan jika ada. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1**: Perbarui CSS layout pada `resources/views/layouts/auth.blade.php` untuk mengunci panel kiri tetap statis 100vh dan memberikan scroll mandiri pada panel kanan.
- [x] **Langkah 2**: Perbarui `.auth-form-header-bar` pada `resources/views/layouts/auth.blade.php` untuk menghilangkan teks "MAGKOM" di area kanan atas / header form.
- [x] **Langkah 3**: Perbarui judul form di `resources/views/auth/register.blade.php` (`Register Akun`) dan `resources/views/auth/login.blade.php` (`Selamat Datang`).
- [x] **Langkah 4**: Jalankan uji otomatis menggunakan Laragon PHP (`artisan test`) untuk memastikan tidak ada pengujian yang terpengaruh.
- [x] **Langkah 5**: Lakukan verifikasi visual di browser lokal untuk memastikan:
  - Panel kiri diam tidak bergerak saat form panjang di-scroll.
  - Panel kanan men-scroll dengan lancar dari atas hingga footer.
  - Tidak ada teks "MAGKOM" di bagian kanan atas.

---

## 6. Strategi Testing

- **Automated Tests**:
  - `php artisan test tests/Feature/PendaftaranKelompokTest.php`
  - `php artisan test --filter=Auth`
- **Uji Visual & Interaktif**:
  - Buka `http://magkom.test/register?tipe=kelompok&jumlah=4` (form paling panjang dengan 4 orang).
  - Scroll ke bawah pada panel form: pastikan gambar gedung dan tulisan Kominfo di panel kiri tetap diam.
  - Buka `http://magkom.test/login`: periksa tampilan header dan judul form.

---

## 7. Risiko & Mitigasi
- **Risiko**: Scrollbar default peramban terlihat tebal atau memotong elemen form.
  - **Mitigasi**: Tambahkan styling scrollbar tipis (WebKit scrollbar 6px dengan warna transparan/halus) agar elegan dan rapi.
- **Risiko**: Pada layar resolusi tinggi atau laptop kecil, form terpotong di bagian bawah.
  - **Mitigasi**: Atur padding bawah `.auth-form-wrapper` dan letakkan footer hak cipta di dalam alur scroll sehingga selalu dapat dijangkau.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Panel visual kiri tetap diam (*fixed/statis*) dan tidak ikut ter-scroll saat form diisi.
- [x] Panel form kanan dapat di-scroll secara mandiri dan halus.
- [x] Teks "MAGKOM" di kanan atas / judul form disederhanakan dengan bersih.
- [x] Seluruh automated test lulus 100% tanpa error regresi.
