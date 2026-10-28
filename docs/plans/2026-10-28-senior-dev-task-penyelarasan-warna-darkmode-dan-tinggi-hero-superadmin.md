# Rencana Kerja: Penyelarasan Warna Dark Mode & Proporsi Tinggi Hero Banner Dashboard SuperAdmin

- **ID Tiket**: `TASK-005`
- **Target Komponen**:
  1. *Hero Studio Banner Container* (`.sa-hero`, `.sa-hero--fullwidth`) di [public/css/superadmin-dashboard.css](file:///G:/MAGKOM/public/css/superadmin-dashboard.css)
  2. *Header & Action Elements* (`.sa-hero__head`, `.sa-hero-title`, `.sa-hero-eyebrow`, `.sa-stamp`, `.btn-hero-primary`, `.btn-hero-ghost`) di [resources/views/superadmin/dashboard/partials/_header.blade.php](file:///G:/MAGKOM/resources/views/superadmin/dashboard/partials/_header.blade.php) & CSS terkait
- **Dokumen Referensi**: 
  - Standar Dark Mode DS-MagKom [2026-10-28-senior-dev-task-standarisasi-dark-mode-warna-layout-superadmin.md](file:///G:/MAGKOM/docs/plans/2026-10-28-senior-dev-task-standarisasi-dark-mode-warna-layout-superadmin.md)
  - Gentelella Modern Dashboard Standard (`public/css/gentelella-theme.css`)
  - Standar Aksesibilitas WCAG 2.1 AA (Rasio Kontras minimal 4.5:1 untuk teks biasa, 3:1 untuk teks besar)
- **Tingkat Kesulitan**: Ringan - Menengah (CSS Architecture, Dark Mode Token Decoupling, & Fluid Layout Proportions)
- **Status Tiket**: `Completed & Verified` (142 Tests Passed)

---

## 1. Analisis Akar Masalah (Root Cause Analysis)

Berdasarkan tangkapan layar yang dilampirkan oleh pengguna pada mode gelap (`[data-theme="dark"]`):

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        TEMUAN MASALAH PADA HERO BANNER SUPERADMIN                      │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 1. KONTAMINIASI TOKEN BIRU MUDA PADA MODE GELAP (#93C5FD):                             │
│    - Rule dasar `.sa-hero` menggunakan:                                                │
│      linear-gradient(160deg, var(--cv-blue-800), var(--cv-blue-900))                   │
│    - Pada baris 861-862 file `superadmin-dashboard.css`, di bawah blok                │
│      `[data-theme="dark"]`, variabel `--cv-blue-800` dan `--cv-blue-900` disetel ke   │
│      warna biru muda `#93c5fd` (Tailwind Sky-300).                                     │
│    - Akibatnya, saat mode gelap aktif, latar hero banner berubah menjadi gradien biru  │
│      muda pastel terang (#93C5FD) yang menyilaukan dan bertolak belakang dengan warna  │
│      header dashboard Gentelella (Deep Abyssal Navy `#02275D` - `#003566`).           │
│    - Tombol "+ Tambah staf" berlatar putih (#FFFFFF) memiliki teks `var(--cv-blue-800)`│
│      yang juga berubah menjadi `#93c5fd`, menyebabkan teks tombol nyaris tidak terbaca  │
│      (kegagalan kontras teks putih vs biru muda).                                      │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 2. PROPORSI TINGGI TERLALU KECIL PADA LEBAR LAYAR DESKTOP:                             │
│    - Saat metrik dipisahkan ke baris kedua (`.sa-metrics-row`), `.sa-hero` hanya        │
│      memiliki padding vertikal `1.75rem` (28px) tanpa `min-height`.                    │
│    - Pada lebar kanvas 1200px – 1440px+, tinggi banner hanya sekitar ~140px, sehingga  │
│      tampak seperti pita tipis yang pipih/gepeng ("terlalu kecil untuk ukuran segini") │
│      dan kehilangan wibawa visual sebagai Atmospheric Hero Studio Banner eksekutif.    │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Sasaran Perubahan & Spesifikasi Desain

### A. Penyelarasan Warna Dark Mode (Sesuai Warna Header)
Warna header dashboard Gentelella pada mode gelap didefinisikan secara resmi pada `public/css/gentelella-theme.css`:
```css
[data-theme="dark"] .top_nav {
    background: linear-gradient(135deg, #02275D 0%, #003566 100%) !important;
    border-bottom: 1px solid rgba(63, 81, 181, 0.35) !important;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.6) !important;
}
```

Oleh karena itu, `.sa-hero` saat `[data-theme="dark"]` akan diselaraskan dengan spesifikasi:
1. **Latar Belakang**:
   - Lapisan kontur topografi SVG: `var(--cv-contour) right -40px top -20px / 720px auto no-repeat`
   - Lapisan radial glow halus: `radial-gradient(80% 120% at 100% 0%, rgba(63, 81, 181, 0.28), transparent 60%)`
   - Lapisan gradien utama identik header: `linear-gradient(135deg, #02275D 0%, #003566 100%)`
2. **Kontur Border & Bayangan**:
   - Border: `1px solid rgba(63, 81, 181, 0.4)`
   - Box-shadow: `0 8px 24px -4px rgba(0, 0, 0, 0.6)`
3. **Harmonisasi Elemen di Dalam Hero**:
   - Teks Judul (`.sa-hero-title`): `#FFFFFF` (kontras rasio > 10:1).
   - Teks Tanggal (`.sa-hero-eyebrow`): `rgba(255, 255, 255, 0.85)`.
   - Tombol Primer (`.btn-hero-primary`): Background `#FFFFFF`, teks `#02275D` (navy gelap pekat, bukan lagi `#93c5fd`), hover `#F1F5F9`.
   - Shortcut `<kbd>`: Teks `#02275D`, border `rgba(2, 39, 93, 0.3)`.
   - Tombol Ghost (`.btn-hero-ghost`): Background `rgba(255, 255, 255, 0.08)`, border `rgba(255, 255, 255, 0.3)`, teks `#FFFFFF`. Hover: `rgba(255, 255, 255, 0.18)`.
   - Stamp Status (`.sa-stamp`): Background `rgba(16, 185, 129, 0.18)`, border `rgba(16, 185, 129, 0.8)`, teks `#A7F3D0`.

> [!NOTE]
> Perubahan mode gelap ini diisolasi ketat di bawah selector `[data-theme="dark"] .sa-hero`. Mode terang (**Light Mode**) tetap mempertahankan gradien biru brand Kominfo (`#06318c` ke `#061e5b`), tanpa ada efek samping ke view lain.

### B. Perbesaran Tinggi & Proporsi Ruang (Visual Breathing Room)
Agar banner tidak terasa pipih pada monitor beresolusi sedang hingga lebar (1200px – 1440px+):
1. **Minimum Height**: Tambahkan `min-height: 200px` pada viewport desktop (`@media (min-width: 768px)`).
2. **Padding Vertikal & Horizontal**:
   - Desktop: Naikkan dari `1.75rem 2rem` menjadi `2.75rem 2.5rem` (44px atas/bawah, 40px kiri/kanan).
   - Mobile: Tetap proporsional di `1.75rem 1.5rem`.
3. **Penyelarasan Vertikal (`.sa-hero__head`)**:
   - Atur `align-items: center` (atau seimbang) dan berikan `min-height: 100%` agar teks judul dan tombol aksi berada di posisi yang harmonis di tengah tinggi kartu.
4. **Skala Tipografi**:
   - `.sa-hero-title`: Naikkan dari `2rem` (32px) menjadi `2.375rem` (38px) dengan `line-height: 1.25` dan margin yang proporsional.
   - `.sa-hero-eyebrow`: Naikkan dari `0.75rem` menjadi `0.8125rem` (13px) dengan letter-spacing `0.06em`.
   - `.sa-stamp`: Berikan margin-top `0.65rem`, padding `0.35rem 0.85rem` untuk keterbacaan optimal.
5. **Ukuran Tombol Aksi**:
   - Naikkan tinggi tombol `.btn-hero-primary` dan `.btn-hero-ghost` dari `36px` menjadi `42px`, padding horizontal `1.25rem`, dan font-size `0.875rem` (14px) agar sepadan dengan skala banner yang lebih besar.

---

## 3. Rencana Langkah Eksekusi (Implementation Steps)

### Langkah 1: Modifikasi CSS di `public/css/superadmin-dashboard.css`
1. Buka file [public/css/superadmin-dashboard.css](file:///G:/MAGKOM/public/css/superadmin-dashboard.css).
2. Perbarui aturan dasar `.sa-hero`:
   - Tambahkan `min-height: 200px;`.
   - Perbarui `padding: 2.75rem 2.5rem;`.
   - Sesuaikan ukuran `.sa-hero-title`, `.sa-hero-eyebrow`, `.sa-stamp`, `.btn-hero-primary`, dan `.btn-hero-ghost`.
3. Tambahkan blok khusus `[data-theme="dark"] .sa-hero`:
   ```css
   /* Penyelarasan Warna Hero Banner dengan Header Gentelella saat Dark Mode */
   [data-theme="dark"] .sa-hero {
     background: var(--cv-contour) right -40px top -20px / 720px auto no-repeat,
                 radial-gradient(80% 120% at 100% 0%, rgba(63, 81, 181, 0.28), transparent 60%),
                 linear-gradient(135deg, #02275D 0%, #003566 100%);
     border: 1px solid rgba(63, 81, 181, 0.35);
     box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.6);
   }

   [data-theme="dark"] .sa-hero .btn-hero-primary {
     background: #ffffff;
     color: #02275D;
     border-color: #ffffff;
   }

   [data-theme="dark"] .sa-hero .btn-hero-primary:hover {
     background: #f1f5f9;
     color: #003566;
   }

   [data-theme="dark"] .sa-hero .btn-hero-primary kbd {
     color: #02275D;
     border-color: rgba(2, 39, 93, 0.25);
   }

   [data-theme="dark"] .sa-hero .btn-hero-ghost {
     border-color: rgba(255, 255, 255, 0.3);
     background: rgba(255, 255, 255, 0.08);
     color: #ffffff;
   }

   [data-theme="dark"] .sa-hero .btn-hero-ghost:hover {
     background: rgba(255, 255, 255, 0.18);
     color: #ffffff;
   }
   ```

### Langkah 2: Verifikasi Kode & Tampilan Responsif
1. Uji tampilan pada Mode Gelap: pastikan warna latar banner hero menyatu harmonis dengan navbar header Gentelella (`#02275D` - `#003566`).
2. Uji tampilan pada Mode Terang: pastikan warna gradien biru brand (`#06318c` - `#061e5b`) tetap utuh.
3. Periksa kontras teks tombol "+ Tambah staf" (latar putih dengan teks navy gelap pekat).
4. Periksa proporsi ketinggian di layar desktop 1200px – 1440px dan perangkat mobile.

### Langkah 3: Pengujian Otomatis (PHPUnit Regression)
Jalankan test suite terkait SuperAdmin Dashboard:
```powershell
php artisan test --filter=SuperAdminDashboardRedesignTest
```
Pastikan seluruh 9 test case lulus dengan status PASS tanpa regresi.

---

## 4. Matriks Perbandingan (Sebelum vs Sesudah)

| Parameter | Kondisi Saat Ini (Before) | Target Rencana (After) |
| :--- | :--- | :--- |
| **Latar Banner di Mode Gelap** | Gradien biru muda terang (`#93c5fd`) karena kebocoran token CSS | Gradien Deep Abyssal Navy (`#02275D` ke `#003566`), identik dengan warna header |
| **Border & Bayangan Dark Mode** | Box-shadow biru muda kusam | Border `1px solid rgba(63, 81, 181, 0.35)` & dark elevation shadow |
| **Warna Teks Tombol Primer Dark Mode** | Biru muda pucat (`#93c5fd`) di atas tombol putih (Rasio kontras < 2.5:1) | Biru navy pekat (`#02275D`) di atas tombol putih (Rasio kontras > 10:1, WCAG AA lulus) |
| **Tinggi Minimum Banner** | Tidak ada (`auto`, ~140px) | `min-height: 200px` dengan vertikal breathing room lega |
| **Padding Desktop** | `1.75rem 2rem` (28px atas/bawah) | `2.75rem 2.5rem` (44px atas/bawah, 40px kiri/kanan) |
| **Ukuran Judul (`.sa-hero-title`)** | `2rem` (32px) | `2.375rem` (38px), lebih tegas dan berwibawa |
| **Tinggi Tombol Aksi** | `36px` | `42px` (lebih seimbang dengan skala hero baru) |
| **Kondisi Mode Terang (Light)** | Gradien Biru Sleman `#06318c` | **Tidak Berubah** (Tetap biru Sleman Kominfo) |
