# Rencana Kerja: Penyelarasan Tombol Menu Autentikasi/Login ke Warna Utama MagKom Terbaru

- **Tanggal**: 2026-11-02
- **Dokumen Referensi**:
  - `docs/plans/2026-10-28-rebranding-css-peeper-palette-header-sidebar-all-dashboards.md` (PRD Master Palette CSS Peeper)
  - `resources/views/layouts/auth.blade.php`
  - `resources/views/auth/login.blade.php`
  - `resources/views/auth/register.blade.php`
  - `resources/views/auth/forgot-password.blade.php`
  - `resources/views/auth/reset-password.blade.php`
  - `resources/views/auth/verify-email.blade.php`
  - `resources/views/auth/ganti-password.blade.php`

---

## 1. Tujuan
Menyelaraskan warna seluruh tombol utama (`.btn-primary`) dan elemen interaktif terkait pada menu autentikasi/perloginan (*Login*, *Register*, *Lupa Kata Sandi*, *Reset Kata Sandi*, *Ganti Kata Sandi*, dan *Verifikasi Email*) dari warna biru terang lama (`#0751FF`) ke **Warna Utama MagKom Terbaru** (`#01347C` - *Royal Sleman Navy*) dengan hover state (`#003566` - *Midnight Navy*) dan aksen pendukung sesuai *Design System Master Palette* MagKom.

---

## 2. Asumsi & Asumsi Kerja Logis

1. **Warna Utama MagKom Terbaru**:
   - Mengacu pada dokumen resmi rebranding palet CSS Peeper (`PRD-TASK-2026-10-28-UI-02`):
     - **Primary Base**: `#01347C` (`--magkom-primary-blue` / `--blue-600`)
     - **Primary Hover / Active**: `#003566` (`--magkom-midnight-navy` / `--blue-700`)
     - **Accent Glow / Focus Ring**: `rgba(1, 52, 124, 0.25)` atau `#3F51B5` (`--magkom-indigo-accent`)
2. **Cakupan Halaman Autentikasi**:
   - Perubahan diatur terpusat melalui token CSS pada [`resources/views/layouts/auth.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/auth.blade.php), sehingga otomatis mencakup seluruh halaman auth:
     - `login.blade.php` (Tombol Submit Login)
     - `register.blade.php` (Tombol Submit Register & selector anggota kelompok)
     - `forgot-password.blade.php` (Tombol Kirim Link Reset)
     - `reset-password.blade.php` (Tombol Simpan Password Baru)
     - `verify-email.blade.php` (Tombol Kirim Ulang Email Verifikasi)
     - `ganti-password.blade.php` (Tombol Ubah Password)
3. **Tombol Pihak Ketiga (Google OAuth)**:
   - Tombol *Login dengan Google* (`.btn-google`) tetap mempertahankan pedoman identitas Google resmi (background putih/netral, border halus, logo 4 warna Google) agar memenuhi standar Google Brand Guidelines.
4. **Aksesibilitas Kontras (WCAG 2.1 AAA)**:
   - Warna putih `#FFFFFF` di atas `#01347C` menghasilkan rasio kontras **8.5:1** (melebihi standar WCAG AAA 7:1), menjamin keterbacaan yang sangat baik.

---

## 3. Pendekatan Teknis & Evaluasi Alternatif

| Kriteria | Opsi 1: Pembaruan Token CSS Terpusat di `layouts/auth.blade.php` (Rekomendasi) | Opsi 2: Hardcode Style Inline / Class Khusus di Tiap File Blade |
| :--- | :--- | :--- |
| **Deskripsi** | Memperbarui variabel `--blue-600` (`#01347C`) dan `--blue-700` (`#003566`) serta styling `.btn-primary` di layout induk autentikasi. | Menambahkan inline style `style="background: #01347C"` pada masing-masing tombol di 6 file blade. |
| **Kelebihan** | - *Single source of truth* selaras dengan `layouts/peserta.blade.php` dan `layouts/dashboard.blade.php`.<br>- Otomatis memperbarui focus ring, link warna, dan checkbox.<br>- Mudah dipelihara di masa depan. | Cepat untuk 1 file saja. |
| **Kekurangan** | Memerlukan verifikasi bahwa tidak ada hardcoded color lama di file turunan. | Redundan, rawan inkonsistensi, melanggar prinsip DRY, menyulitkan jika ada perubahan palet lagi. |
| **Rekomendasi** | **Dipilih (Opsi 1)** | Tidak direkomendasikan |

### Detail Spesifikasi Styling CSS:
```css
/* Update pada :root di resources/views/layouts/auth.blade.php */
--blue-600: #01347C; /* Warna Utama MagKom Terbaru */
--blue-700: #003566; /* Hover Navy */
--blue-50: #EEF4FF;
--blue-100: #E0EBFF;
--blue-tint: rgba(1, 52, 124, 0.08);
--blue-tint-line: #3F51B5;

/* Tombol Primary */
.btn-primary {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 48px;
    background-color: var(--blue-600);
    color: #ffffff;
    font-size: 1rem;
    font-weight: 700;
    font-family: inherit;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(1, 52, 124, 0.2);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-primary:hover {
    background-color: var(--blue-700);
    box-shadow: 0 6px 16px rgba(1, 52, 124, 0.3);
    transform: translateY(-1px);
}

.btn-primary:active {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(1, 52, 124, 0.2);
}

.btn-primary:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px rgba(1, 52, 124, 0.35);
}
```

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/layouts/auth.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/auth.blade.php) | **Modify** | 1. Perbarui nilai `--blue-600` dari `#0751FF` ke `#01347C` dan `--blue-700` dari `#0641CC` ke `#003566`.<br>2. Tambahkan efek *hover shadow* dan *active scale* modern pada `.btn-primary`.<br>3. Selaraskan focus ring input dan checkbox accent dengan `#01347C`. |
| [`resources/views/auth/register.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/auth/register.blade.php) | **Modify** | 1. Ganti hardcoded `#0751FF` pada selector jumlah tim kelompok (`.btn-jumlah-pilihan`) agar menggunakan variabel `var(--blue-600)`.<br>2. Selaraskan background ringkasan kuota terpilih agar selaras dengan navy `#01347C`. |
| [`resources/views/auth/login.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/auth/login.blade.php) | **Modify** | Pastikan tombol login dan link sekunder (*Lupa Kata Sandi*) menggunakan token warna baru secara presisi. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1**: Perbarui token warna `--blue-600` dan `--blue-700` pada blok `:root` dan styling `.btn-primary` di file [`resources/views/layouts/auth.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/auth.blade.php).
- [x] **Langkah 2**: Periksa interaksi tombol (hover, active, focus-visible) pada mode terang dan mode gelap di [`resources/views/layouts/auth.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/auth.blade.php).
- [x] **Langkah 3**: Bersihkan inline styles di [`resources/views/auth/register.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/auth/register.blade.php) yang masih menuliskan warna hex lama `#0751FF` secara statis, ganti menjadi `var(--blue-600)`.
- [x] **Langkah 4**: Pastikan tombol pada halaman login [`resources/views/auth/login.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/auth/login.blade.php) dan halaman auth lainnya (`forgot-password`, `reset-password`, `verify-email`) tampil seragam dan proporsional.
- [x] **Langkah 5**: Jalankan test suite Laravel (`php artisan test`) untuk memastikan tidak ada fungsionalitas form login/register/auth yang terdampak atau rusak.
- [x] **Langkah 6**: Lakukan verifikasi visual di browser lokal untuk memeriksa tampilan tombol di mode terang dan mode gelap.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - Jalankan `php artisan test --filter=AuthenticationTest` (atau test suite auth yang ada).
   - Pastikan alur login peserta, login admin, dan register tetap berjalan sukses 100%.
2. **Visual & UI Verification**:
   - Buka halaman login di browser: `http://magkom.test/login` (atau `http://127.0.0.1:8000/login`).
   - Cek warna tombol login: warna biru navy elegan `#01347C`, saat di-hover menggelap menjadi `#003566`.
   - Cek kontras teks "Login" berwarna putih tajam dan terbaca jelas.
   - Buka halaman register: `http://magkom.test/register`.
   - Cek tombol submit register dan selector tombol jumlah anggota kelompok.
   - Uji perpindahan mode terang (*light*) dan mode gelap (*dark*).

---

## 7. Analisis Risiko & Mitigasi

- **Risiko**: Kontras tombol di mode gelap jika background panel ikut gelap.
  - **Mitigasi**: Di mode gelap, teks putih di atas tombol `#01347C` tetap memiliki kontras tinggi. Bila diperlukan di dark mode, tambahkan subtle border atau glow `rgba(63, 81, 181, 0.4)` agar tombol tetap mencolok (*pop out*).
- **Risiko Breaking Change**: Zero risk, perubahan murni pada CSS styling visual tanpa mengubah atribut form, nama input, action route, maupun id tombol.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Seluruh tombol `.btn-primary` di menu perloginan dan auth telah berganti ke warna utama MagKom terbaru (`#01347C` normal, `#003566` hover).
- [x] Tidak ada lagi warna biru neon elektrik lama (`#0751FF`) yang tertinggal pada tombol maupun selektor auth.
- [x] Tombol memiliki transisi halus, elevasi shadow yang rapi, dan accessible focus ring.
- [x] Seluruh automated test auth lulus tanpa error.
- [x] Tampilan konsisten di layar desktop dan mobile.
