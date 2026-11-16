# Rencana Penyesuaian Sudut Tombol Pilihan Jurusan Landing Page (Subtle Rounded / Sedikit Kotak)

**Tanggal**: 16 November 2026  
**Dokumen**: `docs/plans/2026-11-16-plan-penyesuaian-sudut-tombol-jurusan-landing-page.md`  
**Status**: Menunggu Konfirmasi User  
**Referensi Gambar**: User uploaded image (`media_1794808243444_c10da05e.png`)

---

## 1. Analisis Masalah & Kebutuhan Pengguna

### 1.1 Masalah Tampilan Saat Ini
Pada kartu kuota penempatan di Landing Page (`#kuota`):
- Tombol pemilihan jurusan (`.jurusan-pill-btn`) saat ini menggunakan `border-radius: 9999px` (kapsul / pill bulat penuh).
- Sementara tombol utama di bawahnya (**"Daftar Sekarang ➔"** / `.btn-card-action`) menggunakan `border-radius: 0.375rem` (6px) yang memiliki sudut lengkung persegi halus (*subtle rounded rectangle*).
- Kontras bentuk yang terlalu jauh antara tombol jurusan (sangat bulat lonjong/kapsul) dan tombol aksi utama (persegi modern) menimbulkan ketidakserasian visual (*visual inconsistency*). Pengguna merasa tombol jurusan tersebut "terlalu bulat" dan ingin diubah menjadi **sedikit kotak** agar tampil lebih kokoh, rapi, dan seirama.

```
[ Kondisi Sekarang (Pill / Kapsul Bulat Penuh) ]
 (  ✓ Administrasi Perkantoran  )   <-- border-radius: 9999px (terlalu bulat)
 (  Desain Grafis (SMK)         )
 [       Daftar Sekarang ➔       ]   <-- border-radius: 6px (rounded kotak)

                      ▼ DIUBAH MENJADI ▼

[ Rencana Baru (Subtle Rounded / Sedikit Kotak) ]
 [ ✓ Administrasi Perkantoran ]     <-- border-radius: 6px - 8px (serasi & modern)
 [ Desain Grafis (SMK)        ]
 [     Daftar Sekarang ➔      ]     <-- border-radius: 6px
```

---

## 2. Opsi Desain Sudut Lengkung (*Border Radius*)

| Opsi | Border Radius | Padding & Karakteristik | Rekomendasi |
|---|---|---|---|
| **Opsi A (Sangat Selaras dengan Tombol CTA)** | `border-radius: 6px` (`0.375rem`) | `padding: 0.35rem 0.65rem;`<br>Identik dengan radius tombol `Daftar Sekarang ➔` (0.375rem). Tampilan menjadi sangat konsisten, tegas, dan modern. | **(Rekomendasi Utama)** |
| **Opsi B (Modern Smooth Card Radius)** | `border-radius: 8px` (`0.5rem`) | `padding: 0.35rem 0.7rem;`<br>Sedikit lebih melengkung halus dari opsi A, cocok jika ingin tombol tetap terkesan lembut namun tidak lagi berbentuk kapsul. | Alternatif |
| **Opsi C (Micro Rounded)** | `border-radius: 4px` (`0.25rem`) | `padding: 0.3rem 0.6rem;`<br>Tegas menyerupai badge tabular/sistem kantor formal. | Opsi Tambahan |

---

## 3. Rincian Teknis Perubahan

### 3.1 File Stylesheet yang Diubah
- **Lokasi File**: `resources/css/pages/public/landing.css`
- **Selector Target**:
  - `.jurusan-pill-btn`:
    - Mengubah `border-radius: 9999px;` menjadi `border-radius: 6px;` (atau `8px`).
    - Mengoptimalkan padding vertikal-horizontal dari `0.25rem 0.6rem` menjadi `0.35rem 0.65rem` agar proporsi tinggi dan lebar tombol kotak tampak ergonomis dan seimbang saat teks dibaca.
  - `.jurusan-pill-btn.active`:
    - Mempertahankan background Sleman Navy (`var(--navy)` / `#01347C`), border navy, warna teks putih `#ffffff`, dan shadow lembut `0 2px 6px rgba(27, 56, 96, 0.22)`.
  - `.jurusan-pill-btn .pill-check-icon`:
    - Mempertahankan tanda centang `✓` saat aktif agar tetap informatif dan mudah dipahami pengguna.

### 3.2 File Markup & Interaktivitas (Zero Regression)
- **Blade (`resources/views/public/blocks/kuota.blade.php`)**: Tidak memerlukan perubahan markup, struktur HTML dan accessibility (`role="radio"`, `aria-checked`) tetap utuh.
- **JavaScript (`resources/js/pages/public/landing.js`)**: Logika event listener klik dan update query URL `/register` tetap berjalan normal tanpa gangguan.

---

## 4. Tahapan Eksekusi

1. **Pembaruan CSS**:
   - Edit `resources/css/pages/public/landing.css` pada rule `.jurusan-pill-btn`.
   - Ubah `border-radius: 9999px` ke `border-radius: 6px` (opsi A yang disarankan) atau sesuai persetujuan.
2. **Kompilasi Aset Frontend**:
   - Jalankan `npm run build` dengan Vite untuk memperbarui bundel CSS landing page.
3. **Verifikasi Quality Gates**:
   - Jalankan automated tests: PHPUnit (`LandingPageTest`, `LandingPageToRegistrationPrefillTest`).
   - Cek formatting dengan Laravel Pint (`./vendor/bin/pint --test`).
   - Verifikasi tampilan langsung pada dev server `http://127.0.0.1:8000/#kuota`.

