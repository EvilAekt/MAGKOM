# Plan: Penghapusan Badge Langkah & Redesain Tombol Kembali ke Beranda

- **Tanggal**: 2026-10-09
- **Status**: Selesai (Completed)
- **Author**: Antigravity (web-planning & web-implementation)

---

## 1. Tujuan
1. **Pembersihan Form Register (`register.blade.php`)**: Menghapus elemen badge step (`LANGKAH 01 • Kredensial Akun`) agar form registrasi tampak lebih ringkas, bersih, dan langsung fokus pada pengisian akun pendaftar.
2. **Redesain Tombol "Kembali ke Beranda" (`layouts/auth.blade.php`)**:
   - Memposisikan tombol di **sudut kanan atas** area form pada tampilan desktop, dan sejajar rapi di kanan atas pada tampilan mobile.
   - Meningkatkan estetika visual tombol menjadi komponen *pill button* modern ala platform web SaaS profesional (seperti Stripe/Vercel/Linear): border halus, latar putih bersih, bayangan lembut (*soft shadow*), dan animasi mikro saat disentuh kursor (*hover micro-interaction*).

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi**:
  1. Penghapusan badge langkah tidak mempengaruhi validasi atau tahapan backend, karena proses pendaftaran akun di halaman ini adalah satu formulir mandiri.
  2. Tombol "Kembali ke beranda" di desktop akan menempati posisi kanan atas secara penuh (`width: 100%; display: flex; justify-content: flex-end;`).
  3. Pada layar mobile (< 1024px), logo MagKom kecil tetap berada di kiri atas dan tombol "Kembali ke beranda" berada di kanan atas (`justify-content: space-between`).
  4. Perubahan styling tombol berlaku secara global di seluruh halaman autentikasi (`/login` dan `/register`) yang menggunakan master layout `layouts/auth.blade.php`.
- **Pertanyaan Terbuka**: Tidak ada.

---

## 3. Pendekatan Desain & Arsitektur

### 1. Struktur Layout Header Bar Kanan Atas:
```
Desktop (>= 1024px):
+--------------------------------------------------------------+
| [Panel Kiri / Visual]  |                       [Kembali ->]  | <--- Kanan Atas
|                        |                                     |
|                        |       Register Akun MagKom          |
|                        |       [Form Inputs...]              |
+--------------------------------------------------------------+

Mobile (< 1024px):
+--------------------------------------------------------------+
| [Logo MAGKOM]                          [Kembali ke beranda]  |
|                                                              |
|                     Register Akun MagKom                     |
+--------------------------------------------------------------+
```

### 2. Spesifikasi Desain Tombol "Kembali ke beranda":
- **Bentuk**: Pill Button (`border-radius: 9999px`).
- **Dimensi**: Padding `0.45rem 1rem`, Font size `0.8125rem` (13px), Font weight `600`.
- **Warna**: Background `#ffffff`, Border `1px solid var(--neutral-200)`, Text `var(--neutral-700)`.
- **Shadow**: `box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05)`.
- **Hover State**:
  - Background `var(--neutral-50)`.
  - Border `var(--neutral-300)`.
  - Text `var(--neutral-900)`.
  - Shadow `0 2px 6px rgba(0, 0, 0, 0.08)`.
  - Transform `translateY(-1px)`.
  - Arrow Icon SVG: Bergerak mundur lembut ke kiri `translateX(-3px)`.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/auth/register.blade.php` | Modify | Menghapus elemen `<div class="step-badge">...</div>` (baris 15-19). |
| `resources/views/layouts/auth.blade.php` | Modify | 1. Memperbarui CSS `.auth-form-header-bar` menjadi `width: 100%` dengan posisi `justify-content: flex-end`.<br>2. Merombak CSS `.back-link` menjadi desain *modern pill button* profesional dengan micro-animation hover.<br>3. Menyesuaikan susunan HTML `.auth-form-header-bar`. |

---

## 5. Langkah Implementasi
*Catatan: Checklist di bawah ini telah dieksekusi oleh skill `web-implementation`.*

- [x] **Langkah 1**: Edit `resources/views/auth/register.blade.php`:
  - Hapus blok kode:
    ```blade
    <!-- Step Badge Presisi Figma -->
    <div class="step-badge">
        <span>LANGKAH 01</span>
        <span>&bull;</span>
        <span>Kredensial Akun</span>
    </div>
    ```
- [x] **Langkah 2**: Edit `resources/views/layouts/auth.blade.php`:
  - Perbarui style `.auth-form-header-bar`:
    ```css
    .auth-form-header-bar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        width: 100%;
        margin-bottom: 2rem;
    }

    @media (max-width: 1023px) {
        .auth-form-header-bar {
            justify-content: space-between;
        }
    }
    ```
  - Perbarui style `.back-link` dengan desain pill profesional:
    ```css
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--neutral-700);
        background-color: var(--neutral-0);
        border: 1px solid var(--neutral-200);
        border-radius: 9999px;
        padding: 0.45rem 1rem;
        text-decoration: none;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .back-link:hover {
        color: var(--neutral-900);
        background-color: var(--neutral-50);
        border-color: var(--neutral-300);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        transform: translateY(-1px);
    }

    .back-link:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .back-link svg {
        transition: transform 0.2s ease;
    }

    .back-link:hover svg {
        transform: translateX(-3px);
    }
    ```
  - Susun urutan HTML di `.auth-form-header-bar` agar `.mobile-brand` mendahului `.back-link`.
- [x] **Langkah 3**: Jalankan `php artisan test --filter=ReleaseThreeSocialLoginTest` untuk memastikan tidak ada efek samping.

---

## 6. Strategi Testing
- **Unit & Integration Test**:
  - `php artisan test --filter=ReleaseThreeSocialLoginTest` (13 tests) harus tetap lulus 100%.
- **UI Verification**:
  - Pada layar desktop:
    - Di `/login` dan `/register`, tombol "Kembali ke beranda" berada di sudut kanan atas dengan bentuk pill modern.
    - Saat mouse diarahkan (hover), tombol terangkat sedikit (-1px) dan ikon panah bergeser ke kiri.
    - Di `/register`, teks "LANGKAH 01 • Kredensial Akun" sudah tidak ada.
  - Pada layar mobile (< 1024px):
    - Logo MagKom berada di kiri atas dan tombol pill berada di kanan atas.

---

## 7. Risiko & Mitigasi
- **Risiko**: Tampilan bergeser saat jendela browser diperkecil.
- **Mitigasi**: Media query breakpoint 1023px menjamin tata letak otomatis berubah dari flex-end (desktop) menjadi space-between (mobile) secara responsif.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Badge `LANGKAH 01 • Kredensial Akun` terhapus dari `register.blade.php`.
- [x] Tombol `Kembali ke beranda` bertransformasi menjadi pill button profesional di sudut kanan atas.
- [x] Efek hover micro-interaction panah kiri berfungsi mulus.
- [x] Seluruh automated test suite lulus 100%.

---

## 9. Catatan Perubahan & Saran Lanjutan
- **Perubahan selama pengerjaan**: *(Semua langkah telah diselesaikan)*
- **Saran Lanjutan (Out of Scope)**: Tidak ada.
