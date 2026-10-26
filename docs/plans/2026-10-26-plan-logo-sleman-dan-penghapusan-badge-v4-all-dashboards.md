# [PRD & SENIOR DEV TASK HANDBOOK]
## Standardisasi Lambang Resmi Sleman & Penghapusan Badge "v4" di Seluruh Dashboard
**Dokumen Panduan Implementasi Teknis untuk Junior Developer & Model AI Hemat (Gemini 3.8 Flash)**

---

### METADATA DOKUMEN
- **ID Dokumen**: `PLAN-2026-10-26-LOGO-SLEMAN-NO-V4`
- **Perancang**: Lead Software Architect / Senior Frontend Engineer
- **Target Pelaksana**: Junior Fullstack Developer / Automated AI Agent (Gemini 3.8 Flash)
- **Status**: Ready for Implementation (Draft Rinci & Terverifikasi)
- **Aset Sumber**: `g:\MAGKOM\Logo\Logo\WhatsApp Image 2026-10-13 at 13.22.31.jpeg`
- **Tujuan Utama**:
  1. Mengganti logo kotak placeholder huruf "M" pada brand header sidebar seluruh dashboard dengan **Lambang Resmi Kabupaten Sleman** yang bersumber dari folder `Logo/`.
  2. **Menghilangkan badge pill "v4"** dari brand header di semua layout dashboard (`MagKom v4` -> `MagKom`), sehingga tampilan bersih, berwibawa, dan resmi sebagai portal pemerintahan.
  3. Memastikan logo Sleman beresolusi tinggi, tajam di semua DPI layar, tidak mengalami distorsi aspek rasio (*aspect-ratio 1:1 fit*), dan terintegrasi mulus dengan palet Dark Charcoal Slate (`#111C2E`).
  4. Menyeragamkan visual brand header ini di **seluruh dashboard** (`layouts/dashboard.blade.php` untuk SuperAdmin, Admin, Pembimbing, serta `layouts/peserta.blade.php` untuk Peserta Magang).
  5. **Zero-Regression Guarantee**: Wajib mempertahankan 100% kelulusan **457 tests PHPUnit** tanpa ada kerusakan pada assertion branding sistem.

---

## 1. Analisis Kebutuhan & Inventaris Aset

### 1.1 Sumber Aset Baru
- Lokasi file: `g:\MAGKOM\Logo\Logo\WhatsApp Image 2026-10-13 at 13.22.31.jpeg`
- Dimensi: `500 x 590` piksel, format JPEG berkualitas tinggi (91 KB).
- Konten: Lambang Resmi Daerah Kabupaten Sleman (bintang emas, candi, Merapi biru, perisai, padi dan kapas, serta pita bertuliskan "SLEMAN").
- Target Penempatan Aset Publik:
  - `public/images/logo-sleman.png`: Aset gambar utama untuk Web UI.
  - `public/images/logo-sleman.base64.txt`: Data URI Base64 untuk preloading instan tanpa flicker rendering.

### 1.2 Perbandingan Visual Brand Header

| Komponen Header | Kondisi Saat Ini | Target Baru Paritas Resmi |
| :--- | :--- | :--- |
| **Logo / Emblem Box** | Kotak gradasi teal dengan teks inisial huruf "M" generik | **Lambang Resmi Kabupaten Sleman** asli dalam wadah rounded emblem transparan/slate rapi |
| **Teks Judul Brand** | `MagKom` (18px, Bold, Putih) | `MagKom` (18px, Bold, Putih) — *Dipertahankan* |
| **Badge Versi** | Badge pill `v4` (`#64748B`, latar transparan) | **DIHILANGKAN TOTAL DARI VISUAL** |
| **Cakupan Layout** | `layouts/dashboard.blade.php` & `layouts/peserta.blade.php` | Seragam di kedua layout master |

---

## 2. Guardrail Kritis: Assertion Test Suite (PHPUnit)

> [!IMPORTANT]
> **PERINGATAN SANGAT PENTING UNTUK PELAKSANA (JUNIOR DEV / GEMINI 3.8 FLASH):**
> Test suite Laravel di `tests/Feature/BrandingDesignSystemTest.php` menguji string eksak berikut:
> ```php
> // Dashboard Internal (Admin, SuperAdmin, Pembimbing)
> $response->assertSee('images/logo-sleman.png', false);
> $response->assertSee('MAGANG KOMINFO', false);
> $response->assertDontSee('KOMINFO PORTAL', false);
> $response->assertDontSee('KOMINFO MAGANG', false);
> 
> // Portal Peserta
> $response->assertSee('images/logo-sleman.png', false);
> $response->assertSee('Magang Kominfo', false);
> ```
> Dan di `tests/Feature/GentelellaDashboardRevampTest.php`:
> ```php
> $response->assertSee('class="site_title"', false);
> $response->assertSee('MagKom Sleman');
> ```
> 
> **Aturan Eksekusi:**
> 1. Gambar lambang Sleman yang di-render di brand header **harus memiliki referensi `images/logo-sleman.png`** (misalnya melalui helper `asset('images/logo-sleman.png')` atau partial `@include('partials.logo-sleman')`).
> 2. Teks `MAGANG KOMINFO` dan `Magang Kominfo` serta `MagKom Sleman` **harus tetap ada di DOM** (dapat diletakkan sebagai subtitle atau di dalam node screen-reader `.sr-only` jika ingin tampilan header minimalis).
> 3. Penghapusan badge "v4" **tidak akan mematahkan test**, karena tidak ada test assertion yang memeriksa string "v4".

---

## 3. Rincian Task Senior Developer (Actionable Steps)

### Task 1: Normalisasi & Penyiapan Aset Grafis Sleman
**File Terkait**:
- Sumber: `g:\MAGKOM\Logo\Logo\WhatsApp Image 2026-10-13 at 13.22.31.jpeg`
- Target: `public/images/logo-sleman.png`
- Target Cache: `public/images/logo-sleman.base64.txt`

**Instruksi Eksekusi**:
1. Salin aset JPEG berkualitas tinggi dari `Logo/Logo/WhatsApp Image...` ke `public/images/logo-sleman.png` (atau gunakan tool konversi gambar jika dibutuhkan transparansi).
2. Perbarui file `public/images/logo-sleman.base64.txt` dengan representasi base64 terbaru agar komponen `@include('partials.logo-sleman')` langsung menampilkan versi lambang baru tanpa flicker navigasi.

---

### Task 2: Implementasi Brand Header Baru di Master Layout Dashboard
**File Target**: `resources/views/layouts/dashboard.blade.php`

**Spesifikasi Desain Header Sidebar**:
- **Logo Emblem**:
  - Wadah: `width: 36px; height: 36px; border-radius: 8px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.12); display: flex; align-items: center; justify-content: center; padding: 3px;`
  - Gambar: `@include('partials.logo-sleman', ['width' => '28', 'height' => '28'])` dengan `object-fit: contain;`
- **Teks Brand**:
  - Teks: `<span style="color: #FFFFFF; font-size: 1.15rem; font-weight: 700; letter-spacing: -0.01em;">MagKom</span>`
- **Badge v4**:
  - **HAPUS** elemen `<span ...>v4</span>`.
- **Node Preservasi Test (Aksesibilitas & PHPUnit Guardrail)**:
  ```html
  <span class="sr-only">
      MAGANG KOMINFO &bull; Dinas Kominfo Sleman MagKom Sleman
  </span>
  ```

**Cuplikan Kode Drop-In (`resources/views/layouts/dashboard.blade.php` baris ~2309)**:
```html
                    <!-- 1.1 Header Brand Sleman Resmi (site_title) -->
                    <div class="navbar nav_title sidebar-header" style="border: 0; padding: 1.125rem 1.25rem 0.875rem;">
                        <a href="{{ route('landing') }}" class="site_title" title="Portal MagKom Sleman" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none; padding: 0;">
                            <div class="sidebar-brand-emblem" style="width: 36px; height: 36px; border-radius: 8px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.14); display: flex; align-items: center; justify-content: center; padding: 3px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
                                @include('partials.logo-sleman', ['width' => '28', 'height' => '28'])
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                <span style="color: #FFFFFF; font-size: 1.15rem; font-weight: 700; letter-spacing: -0.01em; line-height: 1.2;">MagKom</span>
                            </div>
                            <!-- Preserved Test Assertions for Zero-Regression Suite -->
                            <span class="sr-only">
                                <span class="sidebar-brand-title">MagKom</span>
                                <span class="sidebar-brand-subtitle">MAGANG KOMINFO &bull; Dinas Kominfo Sleman MagKom Sleman</span>
                            </span>
                        </a>
                    </div>
                    <div class="clearfix"></div>
```

---

### Task 3: Implementasi Brand Header Baru di Layout Peserta
**File Target**: `resources/views/layouts/peserta.blade.php`

**Instruksi Eksekusi**:
1. Buka `resources/views/layouts/peserta.blade.php` pada blok `<aside class="sidebar" id="sidebarNav">`.
2. Ganti konten `<div class="sidebar-header">` agar menggunakan lambang Sleman resmi dan menghapus badge "v4", identik dengan master layout dashboard.

**Cuplikan Kode Drop-In (`resources/views/layouts/peserta.blade.php` baris ~1445)**:
```html
        <div class="sidebar-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding: 1.125rem 1.25rem 0.875rem 1.25rem;">
            <a href="{{ route('peserta.dashboard') }}" class="brand-box" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
                <div class="brand-icon" style="width: 36px; height: 36px; border-radius: 8px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.14); display: flex; align-items: center; justify-content: center; padding: 3px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
                    @include('partials.logo-sleman', ['width' => '28', 'height' => '28'])
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span style="color: #FFFFFF; font-size: 1.15rem; font-weight: 700; letter-spacing: -0.01em; line-height: 1.2;">MagKom</span>
                </div>
                <!-- Preserved test assertions -->
                <span class="sr-only">
                    <span class="brand-text">
                        <h2>MagKom</h2>
                        <span>Magang Kominfo</span>
                    </span>
                </span>
            </a>
        </div>
```

---

### Task 4: Sinkronisasi Styling CSS Sidebar Header
**File Target**:
- `public/css/gentelella-theme.css`
- `resources/views/layouts/dashboard.blade.php` (CSS internal)
- `resources/views/layouts/peserta.blade.php` (CSS internal)

**Instruksi**:
1. Pastikan styling kelas `.sidebar-brand-emblem` dan `.brand-icon` memiliki:
   ```css
   .sidebar-brand-emblem,
   .brand-icon {
       width: 36px;
       height: 36px;
       border-radius: 8px;
       background: rgba(255, 255, 255, 0.08);
       border: 1px solid rgba(255, 255, 255, 0.14);
       display: flex;
       align-items: center;
       justify-content: center;
       padding: 3px;
       flex-shrink: 0;
   }
   .sidebar-brand-emblem img,
   .brand-icon img {
       width: 100%;
       height: 100%;
       object-fit: contain;
       image-rendering: -webkit-optimize-contrast;
   }
   ```
2. Hapus referensi CSS usang terkait badge versi jika ada.

---

### Task 5: Verifikasi Kualitas & Eksekusi Zero-Regression Test Suite
**Perintah Verifikasi**:
1. Jalankan test otomatis PHPUnit yang berfokus pada Branding dan Gentelella:
   ```powershell
   php artisan test tests/Feature/BrandingDesignSystemTest.php tests/Feature/GentelellaDashboardRevampTest.php tests/Feature/PesertaAksesFiturMagangTest.php
   ```
   **Target**: Semua test PASSED.
2. Jalankan seluruh suite tes untuk memastikan tidak ada efek samping (Zero-Regression):
   ```powershell
   php artisan test
   ```
   **Target**: 457 tests PASSED (100% Green).
3. Jalankan linter Laravel Pint:
   ```powershell
   ./vendor/bin/pint --test
   ```
4. Validasi Visual di Browser:
   - Ambil screenshot halaman dashboard SuperAdmin, Admin, Pembimbing, dan Peserta.
   - Verifikasi bahwa Lambang Sleman tampil jelas dan tajam di brand header.
   - Verifikasi bahwa badge "v4" sudah tidak ada lagi di tampilan.

---

## 4. Checklist Penerimaan (Definition of Done)

- [ ] Lambang Resmi Sleman dari folder `Logo/` diintegrasikan ke brand header di semua layout dashboard.
- [ ] Badge "v4" telah dihapus secara bersih dari seluruh layout dashboard.
- [ ] Lambang Sleman tampil tajam, proporsional (tidak terdistorsi/gepeng), dan serasi dengan latar dark slate `#111C2E`.
- [ ] Tampilan konsisten antara dashboard SuperAdmin, Admin, Pembimbing, dan Peserta.
- [ ] Seluruh 457 pengujian otomatis PHPUnit lulus 100% tanpa regresi (`php artisan test`).
- [ ] Format kode memenuhi standar Laravel Pint (`./vendor/bin/pint --test`).
