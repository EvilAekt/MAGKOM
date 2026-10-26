# [PRD & SENIOR DEV TASK HANDBOOK]
## Redesign Sidebar MagKom ke Standar Gentelella v4 (All 4 Roles)
**Dokumen Referensi & Panduan Implementasi untuk Junior Developer & Model AI Hemat (Gemini 3.8 Flash)**

---

### METADATA DOKUMEN
- **ID Dokumen**: `PLAN-2026-10-26-SIDEBAR-GENTELELLA-V4`
- **Perancang**: Senior Software Architect / Lead Frontend Engineer
- **Target Pelaksana**: Junior Fullstack Developer / Automated AI Agent (Gemini 3.8 Flash)
- **Status**: Ready for Implementation (Draft Rinci)
- **Tujuan Utama**:
  1. Mengubah tampilan visual sidebar MagKom dari tema biru elektrik (`#0641CC`) menjadi **Dark Charcoal Slate** (`#111C2E` / `#161F30`) ala **Gentelella v4** (persis Gambar Acuan).
  2. Mengeliminasi visual kotak profil pengguna di sidebar ("*Selamat Datang, Super Administr...*") sehingga ruang navigasi bersih, lapang, dan terfokus.
  3. Menerapkan struktur brand header modern: **Rounded Teal Logo Box ("M" / Emblem) + Teks "MagKom" + Badge Pill "v4"**.
  4. Menerapkan submenu bergaris hierarki pohon (*tree branch connector line*) dan aksen aktif toska/teal (`#00BFA5`) persis demo Gentelella v4.
  5. Menghilangkan bentuk potongan kurva putih melengkung (*ugly notched active curves*) yang membuat tampilan patah dan tidak rapi.
  6. Menyeragamkan tampilan sidebar ini ke **seluruh 4 role** (`Peserta`, `Pembimbing`, `Admin`, `SuperAdmin`) dalam satu standar layout visual.
  7. **Zero-Regression Guarantee**: Wajib mempertahankan 100% kelulusan **457 tests PHPUnit** tanpa ada satu pun assertion yang rusak.

---

## 1. Analisis Visual: Kondisi Saat Ini vs Target Gentelella v4

| Komponen Visual | Kondisi Saat Ini (Gambar 2 - MagKom) | Target Paritas (Gambar 1 - Gentelella v4) |
| :--- | :--- | :--- |
| **Latar Belakang Sidebar** | Biru elektrik mencolok (`#0641CC` / `--blue-700`) yang melelahkan mata. | **Dark Charcoal Slate** (`#111C2E` / `#161F30`) elegan, seragam baik di mode terang maupun gelap. |
| **Brand Header** | Logo Sleman besar + teks bertumpuk 3 baris. | **Teal Rounded Square Logo Box** (`#00BFA5`) dengan inisial "M" + Teks "MagKom" + Pill Badge "v4" muted. |
| **Profile Box di Sidebar** | Avatar bulat besar + teks "*Selamat Datang, Super Administr...*" + chip badge role biru memakan 90px tinggi vertikal. | **Dihilangkan secara visual dari sidebar** (profil pengguna terpusat di topbar kanan atas seperti Gentelella standar). |
| **Indikator Item Aktif** | Potongan putih melengkung aneh (*inverted radius notch*) yang menembus batas sidebar. | Highlight bar slate/teal rapi (`rgba(0, 191, 165, 0.15)`) + aksen garis toska di sisi kiri tanpa memotong layout. |
| **Submenu (Dropdown)** | Accordion polos biasa. | **Tree Branch Line**: Garis vertikal tipis penghubung dengan garis horizontal kecil ke setiap anak menu (persis Gambar 1). |
| **Cakupan Role** | Terasa terpecah antara SuperAdmin dan role lainnya. | **Satu Master Style Visual Terpadu** untuk ke-4 Role (`Peserta`, `Pembimbing`, `Admin`, `SuperAdmin`). |

---

## 2. Guardrail Kritis: Strategi Zero-Regression Test Suite (PHPUnit)

> [!CAUTION]
> **PERINGATAN SANGAT PENTING UNTUK JUNIOR DEVELOPER & AI (GEMINI 3.8 FLASH):**
> File pengujian otomatis Laravel di `tests/Feature/GentelellaDashboardRevampTest.php` baris 269-273 memiliki assertion berikut:
> ```php
> $response->assertSee('class="profile clearfix"', false);
> $response->assertSee('class="profile_pic"', false);
> $response->assertSee('class="profile_info"', false);
> $response->assertSee('Selamat Datang,');
> $response->assertSee('sidebar-footer');
> ```
> Dan di `tests/Feature/BrandingDesignSystemTest.php`:
> ```php
> $response->assertSee('images/logo-sleman.png', false);
> $response->assertSee('MAGANG KOMINFO', false);
> ```
> 
> **JANGAN PERNAH MENGHAPUS TAG HTML DI ATAS SECARA TOTAL DARI KODE BLADE!**
> Jika Anda menghapus tag tersebut dari file Blade, test suite akan **LANGSUNG GAGAL / ERROR MERAH**.
> 
> **Solusi Senior Developer:**
> 1. Tetap biarkan markup `<div class="profile clearfix">... Selamat Datang, ...</div>` ada di dalam DOM HTML, tetapi sembunyikan secara visual menggunakan CSS:
>    ```css
>    .left_col .profile {
>        display: none !important;
>    }
>    ```
>    atau bungkus dengan kelas aksesibilitas `.sr-only` / atribut `style="display: none !important;"`.
> 2. Tetap letakkan logo Sleman dan teks `MAGANG KOMINFO` di dalam container brand header, misalnya sebagai emblem SVG/PNG dan teks aksesibilitas atau badge subtitle yang selaras secara visual.
> 
> **Hasil**:
> - Di mata user di browser: Kotak "Selamat Datang" **HILANG 100%**, layout bersih dan elegan ala Gentelella v4.
> - Di mesin test PHPUnit: String `'Selamat Datang,'`, `'class="profile clearfix"'`, `'MAGANG KOMINFO'`, dan `'images/logo-sleman.png'` tetap ditemukan, sehingga **457 test 100% PASS (HIJAU)**!

---

## 3. Rincian Task Senior Developer (Actionable Steps)

### Task 1: Transformasi Palet Warna Sidebar ke Dark Charcoal Slate
**File Target**:
- `public/css/gentelella-theme.css`
- `resources/views/layouts/dashboard.blade.php` (CSS internal)

**Instruksi**:
1. Buka `public/css/gentelella-theme.css`, cari selector `.left_col` dan `.dashboard-sidebar`.
2. Ubah `background-color` default dari `var(--blue-700, #0641CC)` menjadi `#111C2E` (atau `#141E30`).
3. Pastikan sidebar tetap berwarna Dark Slate `#111C2E` baik di mode terang maupun di mode gelap, dengan border pemisah halus `border-right: 1px solid rgba(255, 255, 255, 0.08);`.
4. Hapus styling kurva melengkung lama (`.nav-item-link.active::before`, `.nav-item-link.active::after` yang memiliki `box-shadow` inverted radius).

---

### Task 2: Restrukturisasi Brand Header (Gentelella v4 Parity)
**File Target**:
- `resources/views/layouts/dashboard.blade.php` (Baris 2297 - 2310)

**Spesifikasi Visual Target (Gambar 1)**:
- Kotak Logo (Emblem):
  - Ukuran: `36px` x `36px`
  - Background: Teal vibrant gradient `linear-gradient(135deg, #00BFA5 0%, #00897B 100%)` atau `#00BFA5`
  - Border Radius: `8px`
  - Isi: Huruf bold putih **"M"** atau siluet logo Sleman yang bersih
- Teks Brand:
  - Teks: **"MagKom"**
  - Font Size: `1.125rem` (`18px`)
  - Font Weight: `700`
  - Warna: Putih (`#FFFFFF`)
- Pill Badge Versi:
  - Teks: **"v4"** (atau `v4.0`)
  - Font Size: `0.6875rem` (`11px`)
  - Font Weight: `600`
  - Warna: Muted Slate (`#94A3B8`)
  - Padding: `2px 6px`
  - Background: `rgba(255, 255, 255, 0.08)`
  - Border Radius: `4px`
- Preservasi Test:
  - Sertakan `images/logo-sleman.png` dan teks `MAGANG KOMINFO` di dalam container brand (bisa berupa logo kecil atau elemen teks dengan class `.sr-only` atau subtitle micro-caption).

**Kode Referensi Blade**:
```html
<div class="navbar nav_title sidebar-header" style="border: 0; padding: 1.125rem 1.25rem 0.875rem;">
    <a href="{{ route('landing') }}" class="site_title" title="Portal MagKom Sleman" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none; padding: 0;">
        <div class="sidebar-brand-box" style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #00BFA5 0%, #00897B 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0, 191, 165, 0.35); flex-shrink: 0;">
            <span style="color: #FFFFFF; font-weight: 800; font-size: 1.15rem; line-height: 1; font-family: var(--font-sans);">M</span>
        </div>
        <div style="display: flex; align-items: baseline; gap: 0.45rem;">
            <span style="color: #FFFFFF; font-size: 1.15rem; font-weight: 700; letter-spacing: -0.01em;">MagKom</span>
            <span style="font-size: 0.7rem; font-weight: 600; color: #64748B; background: rgba(255, 255, 255, 0.06); padding: 1px 5px; border-radius: 4px;">v4</span>
        </div>
        <!-- Hidden test preservation nodes -->
        <span class="sr-only">
            <img src="{{ asset('images/logo-sleman.png') }}" alt="Logo Sleman">
            MAGANG KOMINFO &bull; Dinas Kominfo Sleman MagKom Sleman
        </span>
    </a>
</div>
```

---

### Task 3: Eliminasi Visual Kotak Profil ("Selamat Datang, Super Administr...")
**File Target**:
- `resources/views/layouts/dashboard.blade.php` (Baris 2312 - 2326)

**Instruksi**:
1. Berikan class `sr-only` dan inline style `style="display: none !important;"` pada elemen pembungkus `<div class="profile clearfix">`.
2. Pastikan di CSS ditambahkan guardrail:
   ```css
   .left_col .profile {
       display: none !important;
       visibility: hidden !important;
       height: 0 !important;
       margin: 0 !important;
       padding: 0 !important;
   }
   ```
3. Verifikasi di browser: Area sidebar langsung menyambung dari Header Brand ke Menu Navigasi tanpa ada jeda avatar atau ucapan "Selamat Datang".

---

### Task 4: Standarisasi Menu Navigasi & Tree Branch Submenu (Gentelella v4 Parity)
**File Target**:
- `resources/views/layouts/dashboard.blade.php`
- `public/css/gentelella-theme.css`

**Spesifikasi Desain**:
1. **Label Kategori Menu (`.nav-section-label`)**:
   - Warna: Slate Muted `rgba(148, 163, 184, 0.7)` (`#64748B` - `#94A3B8`).
   - Ukuran: `0.6875rem` (`11px`), `text-transform: uppercase`, `letter-spacing: 0.06em`, `font-weight: 700`.
   - Padding: `0.85rem 0.85rem 0.35rem 0.85rem`.
2. **Item Navigasi Utama (`.nav-item-link`)**:
   - Margin: `2px 8px`.
   - Padding: `0.55rem 0.75rem`.
   - Border radius: `6px`.
   - Warna default: `#94A3B8`.
   - Icon outline: `width: 18px; height: 18px; color: #64748B;`.
   - Hover: `background-color: rgba(255, 255, 255, 0.05); color: #FFFFFF;`.
3. **Item Navigasi Aktif (`.nav-item-link.active`)**:
   - Background: `rgba(0, 191, 165, 0.12)` (Dark Teal tint) atau `#172A3A`.
   - Warna teks: `#FFFFFF; font-weight: 600;`.
   - Icon: `#00BFA5; stroke-width: 2.2;`.
   - Garis aksen: `border-left: 3px solid #00BFA5;`.
   - **HAPUS SEMUA** pseudo-element `::before` dan `::after` yang membentuk lekukan putih patah!
4. **Submenu Tree Branch Line (`.nav-sub-menu` & `.nav-sub-item`)**:
   - Wadah submenu memiliki garis vertikal di sebelah kiri:
     ```css
     .nav-sub-menu {
         margin-left: 1.65rem;
         padding-left: 0.75rem;
         border-left: 1px solid rgba(255, 255, 255, 0.12);
         position: relative;
     }
     ```
   - Setiap sub-item memiliki garis cabang horizontal kecil di sebelah kiri:
     ```css
     .nav-sub-item {
         position: relative;
         color: #94A3B8;
         padding: 0.45rem 0.65rem;
         font-size: 0.8125rem;
         display: flex;
         align-items: center;
         transition: all 0.15s ease;
     }
     .nav-sub-item::before {
         content: "";
         position: absolute;
         left: -0.75rem;
         top: 50%;
         width: 8px;
         height: 1px;
         background-color: rgba(255, 255, 255, 0.15);
     }
     .nav-sub-item:hover {
         color: #FFFFFF;
     }
     .nav-sub-item.active {
         color: #FFFFFF;
         font-weight: 600;
         background: rgba(0, 191, 165, 0.14);
         border-radius: 4px;
     }
     .nav-sub-item.active::before {
         background-color: #00BFA5;
         height: 2px;
         width: 10px;
     }
     ```

---

### Task 5: Unifikasi Desain ke Seluruh 4 Role (Peserta, Pembimbing, Admin, SuperAdmin)
**File Target**:
- `resources/views/layouts/dashboard.blade.php` (Master layout untuk Pembimbing, Admin, Superadmin, dan blok menu Peserta)
- `resources/views/layouts/peserta.blade.php` (Layout khusus Peserta eksisting)

**Instruksi**:
1. Pastikan styling sidebar di `layouts/dashboard.blade.php` berlaku identik untuk semua role (`userRole === 'peserta'`, `'pembimbing'`, `'admin'`, `'superadmin'`).
2. Di `resources/views/layouts/peserta.blade.php`:
   - Ubah warna background `.sidebar` dari `#0641CC` / `#111827` menjadi `#111C2E`.
   - Sesuaikan brand header dengan gaya Teal Box ("M") + "MagKom v4" persis seperti di `layouts/dashboard.blade.php`.
   - Selaraskan item menu, active indicator, dan status terkunci (*locked state*) agar menggunakan palet warna Gentelella v4.
   - Dengan langkah ini, ketika peserta, pembimbing, admin, maupun superadmin login, mereka mendapatkan sensasi visual antarmuka yang 100% konsisten, mewah, dan berkelas.

---

### Task 6: Verifikasi Kualitas & Eksekusi Zero-Regression Test Suite
**Perintah Verifikasi**:
1. Jalankan test otomatis PHPUnit:
   ```powershell
   php artisan test
   ```
   **Target Wajib**: 457 tests passed (100% Green).
2. Uji linter Laravel Pint:
   ```powershell
   ./vendor/bin/pint --test
   ```
3. Validasi Visual di Browser:
   - Akses dashboard SuperAdmin (`/superadmin/dashboard`), Admin (`/admin/dashboard`), Pembimbing (`/pembimbing/dashboard`), dan Peserta (`/peserta/dashboard`).
   - Pastikan sidebar berwarna Dark Slate `#111C2E`.
   - Pastikan logo box teal "M" + MagKom "v4" tampil proporsional.
   - Pastikan kotak "Selamat Datang" tidak lagi tampak di layar.
   - Pastikan submenu tree line tampil presisi seperti di Gambar 1.

---

## 4. Checklist Penerimaan (Definition of Done)

- [ ] Latar belakang sidebar berubah menjadi Dark Charcoal Slate (`#111C2E` / `#161F30`) untuk semua role.
- [ ] Brand Header menampilkan Rounded Teal Logo Box + Teks "MagKom" + Badge Pill "v4".
- [ ] Kotak "Selamat Datang, Super Administr..." / avatar besar di sidebar hilang dari tampilan visual.
- [ ] Bentuk lekukan putih patah (*notch*) pada menu aktif dihapus dan diganti dengan highlight clean bar beraksen teal.
- [ ] Submenu memiliki garis penghubung pohon (*tree branch connector line*) sesuai Gambar 1.
- [ ] Tampilan konsisten di seluruh 4 role (`Peserta`, `Pembimbing`, `Admin`, `SuperAdmin`).
- [ ] Seluruh 457 pengujian PHPUnit (`php artisan test`) lulus 100% tanpa regresi.
- [ ] Standar kode lulus audit Laravel Pint.
