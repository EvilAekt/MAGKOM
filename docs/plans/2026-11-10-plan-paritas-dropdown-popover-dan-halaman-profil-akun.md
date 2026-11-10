# TASK BRIEF SENIOR DEVELOPER: STANDARISASI DROPDOWN POPOVER AKUN TOPBAR, PENYELARASAN HALAMAN BIODATA & PROFIL, DAN PENGATURAN AKUN LINTAS PERAN (SUPERADMIN & PEMBIMBING BERGAYA PORTAL PESERTA)

> **Dari:** Senior Developer / Lead Frontend & Backend Architect  
> **Kepada:** Junior Developer / AI Agent (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Komunikasi dan Informatika Kabupaten Sleman)  
> **Status:** READY FOR IMPLEMENTATION (DRAFT RENCANA DETAIL - MENUNGGU PERSETUJUAN PENGGUNA)  
> **Tingkat Kesulitan:** Menengah (*Blade Architecture*, *Topbar Navigation Parity*, *Layout Alignment*, *Zero-Regression Testing*, *AGENTS.md Strict Compliance*)  
> **Halaman Acuan (Gold Standard):**  
> 1. Gambar Referensi Popover Dropdown Pengguna: [`media_1794323350660_61e80c46.png`](file:///C:/Users/galan/.gemini/antigravity/brain/3da8831e-f820-4fa8-a0e7-235f9633cade/.user_uploaded/media_1794323350660_61e80c46.png)  
> 2. Gambar Temuan Masalah Blank Space / Dead Space: [`media_1794323278861_dbbd8ee4.png`](file:///C:/Users/galan/.gemini/antigravity/brain/3da8831e-f820-4fa8-a0e7-235f9633cade/.user_uploaded/media_1794323278861_dbbd8ee4.png), [`media_1794323300073_29ef2b2f.png`](file:///C:/Users/galan/.gemini/antigravity/brain/3da8831e-f820-4fa8-a0e7-235f9633cade/.user_uploaded/media_1794323300073_29ef2b2f.png), [`media_1794323325632_311e9f47.png`](file:///C:/Users/galan/.gemini/antigravity/brain/3da8831e-f820-4fa8-a0e7-235f9633cade/.user_uploaded/media_1794323325632_311e9f47.png)  
> **Halaman Target Implementasi:**  
> 1. Topbar Navigation Layout: [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php)  
> 2. Profil SuperAdmin: [`resources/views/superadmin/profil/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/profil/show.blade.php) & [`resources/css/pages/superadmin/profil-show.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/profil-show.css)  
> 3. Pengaturan Akun SuperAdmin: [`resources/views/superadmin/akun/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/akun/index.blade.php)  
> 4. Verifikasi & Perlindungan Modul Pembimbing: [`resources/views/pembimbing/profil/show.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/profil/show.blade.php) & [`resources/views/pembimbing/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/pengaturan/index.blade.php)  
> **Dokumentasi & Regulasi Terkait:** `AGENTS.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `tests/Feature/StaffProfileAndSettingsTest.php`

---

## 1. 🔍 LATAR BELAKANG & ANALISIS KESENJANGAN VISUAL (VISUAL GAP ANALYSIS)

### 1.1 Masalah Tampilan Popover Dropdown Akun di Kanan Atas Topbar
Berdasarkan perbandingan langsung antara tangkapan layar acuan (Gambar 4: Portal Peserta) dan implementasi saat ini pada layout dashboard internal:
1. **Ketidakkonsistenan Penamaan Navigasi**:
   - Di Portal Peserta (Gambar 4 Acuan): Label menu pengaturan akun tertulis secara sangat jelas dan deskriptif:  
     `Pengaturan Akun` dengan subtext muted `(Ganti Kata Sandi)`.
   - Di SuperAdmin (`layouts/dashboard.blade.php`): Masih tertulis `"Akun & Preferensi"` tanpa subtext dan dengan ikon yang belum seragam.
   - Di Pembimbing (`layouts/dashboard.blade.php`): Tertulis `"Pengaturan Akun"` tanpa subtext muted `(Ganti Kata Sandi)`.
2. **Penamaan Menu Profil**:
   - Di Peserta tertulis `"Biodata & Profil"`.
   - Di SuperAdmin, Pembimbing, dan Admin saat ini tertulis `"Profil Saya"`. User menginginkan standarisasi yang jelas dan konsisten.
3. **Penyelarasan Role Badge Header Dropdown**:
   - Di Peserta (Gambar 4): Badge role biru navy berbentuk kapsul `PESERTA MAGANG`.
   - Di SuperAdmin: Harus menggunakan badge kapsul emas elegan bertuliskan `SUPER ADMINISTRATOR`.
   - Di Pembimbing: Harus menggunakan badge kapsul biru/emas bertuliskan `PEMBIMBING LAPANGAN`.
   - Di Admin: Badge kapsul emas bertuliskan `ADMINISTRATOR DINAS`.
4. **Tautan Beranda Publik & Logout**:
   - Tetap sediakan `"Ke Beranda Publik"` untuk seluruh role internal (Admin, Pembimbing, SuperAdmin) sebelum divider logout.
   - Tombol logout tetap menggunakan form POST resmi dengan teks `"Keluar Akun"` dan ikon merah.

### 1.2 Masalah Tampilan Halaman Biodata & Profil serta Pengaturan Akun (Analisis Blank Space)
Pada tangkapan layar yang dilaporkan pengguna (`media_1794323278861_dbbd8ee4.png`, `media_1794323300073_29ef2b2f.png`, dan `media_1794323325632_311e9f47.png`):
1. **Dead Space / Blank Space Putih Besar di Bagian Atas Halaman**:
   - Pada halaman Profil SuperAdmin, terdapat jeda kosong putih vertikal yang sangat besar di atas Hero Banner. Hal ini disebabkan oleh rendering judul halaman ganda melalui container `.page-header-row` (akibat penggunaan `@section('page_title')` bersamaan dengan Hero Banner) dan inkonsistensi styling container utama `.right_col`.
   - Pada halaman Pengaturan Akun SuperAdmin (`superadmin/akun/index.blade.php`), banner hero dan grid bento sebelumnya membawa atribut `data-scroll-reveal` tanpa modul observer aktif, menyebabkan kartu atas menjadi `opacity: 0; visibility: hidden` sehingga menyisakan ruang kosong besar dan hanya menampilkan kartu paling bawah.
2. **Instruksi Khusus untuk Modul Pembimbing**:
   - Pengguna memberikan arahan eksplisit:  
     *"untuk perubahan ini di misal di pembimbing masih sama buat sama juga misal di pembimbing ga ada problem jangan di ubah jugaaa"*
   - **Prinsip Utama:** Jika di Pembimbing tampilannya sudah stabil, rapi, dan tidak ada bug visual, **JANGAN DIUBAH ATAU DI-REFACTOR**. Kita hanya menyelaraskan dropdown menu topbar dan memastikan tidak ada dead space pada modul yang bermasalah.

---

## 2. 🗺️ MATRIKS STANDARISASI LINTAS PERAN (ROLE PARITY MATRIX)

Tabel berikut menjadi acuan mutlak bagi Junior Developer / AI pelaksana dalam menyelaraskan elemen di setiap peran:

### 2.1 Matriks Popover Dropdown Menu Topbar

| Elemen Dropdown | Peserta (Acuan Gambar 4) | Admin Dinas | Pembimbing Lapangan | Super Administrator |
|---|---|---|---|---|
| **Avatar Header** | Avatar Inisial / Foto Bulat | Avatar Inisial / Foto Bulat | Avatar Inisial / Foto Bulat | Avatar Inisial / Foto Bulat |
| **Nama Header** | `$currentUser->nama` | `$currentUser->nama` | `$currentUser->nama` | `$currentUser->nama` |
| **Email Header** | `$currentUser->email` | `$currentUser->email` | `$currentUser->email` | `$currentUser->email` |
| **Role Pill Badge** | `PESERTA MAGANG` (Biru) | `ADMINISTRATOR DINAS` (Emas) | `PEMBIMBING LAPANGAN` (Biru/Emas) | `SUPER ADMINISTRATOR` (Emas) |
| **Item 1: Dashboard** | `Dashboard` (`peserta.dashboard`) | `Dashboard` (`admin.dashboard`) | `Dashboard` (`pembimbing.dashboard`) | `Dashboard` (`superadmin.dashboard`) |
| **Item 2: Profil** | `Biodata & Profil` (`peserta.profil.show`) | `Biodata & Profil` (`admin.profil.show`) | `Biodata & Profil` (`pembimbing.profil.show`) | `Biodata & Profil` (`superadmin.profil.show`) |
| **Item 3: Khusus** | `Riwayat Pendaftaran` (`peserta.pendaftaran.index`) | *Tidak Ada (Internal)* | *Tidak Ada (Internal)* | *Tidak Ada (Internal)* |
| **Item 4: Pengaturan** | `Pengaturan Akun (Ganti Kata Sandi)` (`peserta.pengaturan.index`) | `Pengaturan Akun (Ganti Kata Sandi)` (`admin.pengaturan.index`) | `Pengaturan Akun (Ganti Kata Sandi)` (`pembimbing.pengaturan.index`) | `Pengaturan Akun (Ganti Kata Sandi)` (`superadmin.akun.index`) |
| **Item 5: Publik** | *Tidak Ada* | `Ke Beranda Publik` (`landing`) | `Ke Beranda Publik` (`landing`) | `Ke Beranda Publik` (`landing`) |
| **Item 6: Logout** | `Keluar Akun` (Merah, Form POST) | `Keluar Akun` (Merah, Form POST) | `Keluar Akun` (Merah, Form POST) | `Keluar Akun` (Merah, Form POST) |

### 2.2 Matriks Halaman Biodata & Profil

| Aspek Halaman | Peserta | Admin Dinas | Pembimbing Lapangan | Super Administrator |
|---|---|---|---|---|
| **Route GET** | `peserta.profil.show` | `admin.profil.show` | `pembimbing.profil.show` | `superadmin.profil.show` |
| **Topbar Title** | "Biodata & Profil Peserta" | "Profil & Data Staf Administrasi" | "Profil & Data Pembimbing" | "Profil & Data Super Administrator" |
| **Hero Banner** | Navy Banner + Pasfoto + Badge | Navy Banner + Pasfoto + Badge Emas | Navy Banner + Pasfoto + Badge Dinas | Navy Banner + Pasfoto + Badge Emas |
| **Metrik Ringkasan** | Kelengkapan Profil 100% | Kinerja Operasional ASN | Mentee Aktif & Tugas Lapangan | Otoritas & Monitoring Tata Kelola |
| **Status Dead Space** | Bersih (Langsung Banner) | Bersih (Langsung Banner) | Bersih (Pertahankan layout stabil) | **Target Fix**: Hilangkan blank space atas |

### 2.3 Matriks Halaman Pengaturan Akun

| Aspek Halaman | Peserta | Admin Dinas | Pembimbing Lapangan | Super Administrator |
|---|---|---|---|---|
| **Route GET** | `peserta.pengaturan.index` | `admin.pengaturan.index` | `pembimbing.pengaturan.index` | `superadmin.akun.index` |
| **Topbar Title** | "Pengaturan Akun" | "Pengaturan Akun" | "Pengaturan Akun" | "Pengaturan Akun" |
| **Hero Banner** | Navy Banner + Widget Status Emas | Navy Banner + Widget Status Emas | Navy Banner + Widget Status Emas | Navy Banner + Widget Status Emas |
| **Bento Grid Kolom 1** | Informasi Kredensial Akun | Informasi Kredensial Staf | Informasi Kredensial Pembimbing | Informasi Kredensial SuperAdmin |
| **Bento Grid Kolom 2** | Form Ganti Kata Sandi | Form Ganti Kata Sandi | Form Ganti Kata Sandi | Form Ganti Kata Sandi |
| **Preferensi Tampilan** | Tema & Bahasa (Di bawah) | Tema & Bahasa (Di bawah) | Tema & Bahasa (Di bawah) | Tema & Bahasa (Di bawah) |
| **Status Visibilitas** | 100% Visible seketika | 100% Visible seketika | 100% Visible seketika | **Target Fix**: Hapus `data-scroll-reveal` |

---

## 3. 🏛️ BLUEPRINT WIREFRAME & STRUKTUR VISUAL

### 3.1 Blueprint Popover Dropdown Topbar Terpadu (`#userDropdown`)

```text
┌────────────────────────────────────────────────────────┐
│ [AVATAR BULAT 40px]   Super Administrator Kominfo      │
│                       superadmin@magkom.go.id          │
│                       [ SUPER ADMINISTRATOR ] (Emas)   │
├────────────────────────────────────────────────────────┤
│  [🏠]  Dashboard                                       │
│  [👤]  Biodata & Profil                                │
│  [⚙️]  Pengaturan Akun (Ganti Kata Sandi)               │
│  [🌐]  Ke Beranda Publik                               │
├────────────────────────────────────────────────────────┤
│  [🚪]  Keluar Akun                                     │ (Merah, POST Form)
└────────────────────────────────────────────────────────┘
```

### 3.2 Blueprint Halaman Profil Tanpa Dead Space

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [TOPBAR NAV]  MagKom  |  Profil & Data Super Administrator                    [User v] │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [ALERT FLASH MESSAGE JIKA ADA (SUCCESS / ERROR)]                                       │
│ ┌────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─────┐  Super Administrator Kominfo     [ SUPER ADMINISTRATOR ]   [ Pilih Pasfoto] │
│ │ │ (S) │  superadmin@magkom.go.id                                   (Hapus Pasfoto)  │
│ │ └──📷─┘  Dinas Komunikasi dan Informatika Kabupaten Sleman                          │
│ │ ────────────────────────────────────────────────────────────────────────────────── │ │
│ │  OTORITAS & MONITORING TATA KELOLA               10 Staf Aktif • 4,321 Audit Log    │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ [🪪] Informasi Kedinasan & Identitas Resmi                                         │ │
│ │ ────────────────────────────────────────────────────────────────────────────────── │ │
│ │  Nama Lengkap Staf *                      Email Resmi Akun [🔒 Terkunci]           │ │
│ │  [ Super Administrator Kominfo       ]    [ superadmin@magkom.go.id            ]   │ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ [📞] Kontak Operasional & Jalur Koordinasi                                         │ │
│ │ ────────────────────────────────────────────────────────────────────────────────── │ │
│ │  Nomor WhatsApp / Telepon Aktif *         Alamat Kantor Kedinasan [🔒 Sistem]      │ │
│ │  [ 081234567890                      ]    [ Jl. Parasamya No. 1, Beran, Sleman ]   │ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ [✓] Data profil kedinasan yang saya isikan sudah benar.  [✓ Simpan Perubahan Profil]│
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. 🎨 TOKEN DESAIN & STANDAR STYLING (MAGKOM DESIGN SYSTEM)

Seluruh komponen wajib mengacu pada token warna resmi MagKom:
- **Navy Primary / Abyssal**: `#02275D` (Border banner, tombol utama submit, icon header card).
- **Navy Mid / Sleman**: `#01347C` (Gradien banner, link hover state, active breadcrumb).
- **Gold Accent / Sleman**: `#FFCB05` (Role pill badge SuperAdmin, status widget aktif, badge kamera).
- **Gold Text / Hover**: `#F59E0B` / `#D97706` (Border badge emas, hover aksen).
- **White Surface**: `#FFFFFF` (Background card, modal dropdown popover, tombol upload).
- **Border Neutral**: `#E2E8F0` (Card container border, input border, divider popover).
- **Slate Text Dark**: `#0F172A` (Heading, nama staf, teks utama).
- **Muted Text**: `#64748B` (Subtitle, helper text, label terkunci, subtext `(Ganti Kata Sandi)`).
- **Danger Red**: `#EF4444` / `#DC2626` (Tombol Keluar Akun, alert error).

---

## 5. 🛠️ INSTRUKSI LANGKAH DEMI LANGKAH UNTUK JUNIOR DEVELOPER / GEMINI 3.8 FLASH

Junior Developer atau AI pelaksana **wajib** mengikuti instruksi terperinci berikut:

### 📋 TUGAS 1: Standarisasi Dropdown Menu Akun di Topbar (`resources/views/layouts/dashboard.blade.php`)
1. Buka berkas [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) pada bagian dropdown `#userDropdown` (sekitar baris 3712 s/d 3815).
2. Perbarui blok navigasi untuk setiap peran agar seragam dengan standar acuan Gambar 4:
   - **Role `superadmin`**:
     ```blade
     @elseif($userRole === 'superadmin')
         <a href="{{ route('superadmin.dashboard') }}" class="dropdown-item-link" role="menuitem">
             <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                 <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
             </svg>
             <span>{{ __('Dashboard') }}</span>
         </a>
         <a href="{{ route('superadmin.profil.show') }}" class="dropdown-item-link" role="menuitem">
             <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                 <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                 <circle cx="12" cy="7" r="4"></circle>
             </svg>
             <span>{{ __('Biodata & Profil') }}</span>
         </a>
         <a href="{{ route('superadmin.akun.index') }}" class="dropdown-item-link" role="menuitem">
             <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                 <circle cx="12" cy="12" r="3"></circle>
                 <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
             </svg>
             <span>{{ __('Pengaturan Akun') }} <span style="font-size: 0.72rem; opacity: 0.7; font-weight: 400;">({{ __('Ganti Kata Sandi') }})</span></span>
         </a>
     ```
   - **Role `pembimbing`**:
     ```blade
     @elseif($userRole === 'pembimbing')
         <a href="{{ route('pembimbing.dashboard') }}" class="dropdown-item-link" role="menuitem">
             <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                 <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
             </svg>
             <span>{{ __('Dashboard') }}</span>
         </a>
         <a href="{{ route('pembimbing.profil.show') }}" class="dropdown-item-link" role="menuitem">
             <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                 <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                 <circle cx="12" cy="7" r="4"></circle>
             </svg>
             <span>{{ __('Biodata & Profil') }}</span>
         </a>
         <a href="{{ route('pembimbing.pengaturan.index') }}" class="dropdown-item-link" role="menuitem">
             <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                 <circle cx="12" cy="12" r="3"></circle>
                 <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
             </svg>
             <span>{{ __('Pengaturan Akun') }} <span style="font-size: 0.72rem; opacity: 0.7; font-weight: 400;">({{ __('Ganti Kata Sandi') }})</span></span>
         </a>
     ```
   - **Role `admin`**: Selaraskan teks `"Profil Saya"` menjadi `"Biodata & Profil"`, pastikan subtext `(Ganti Kata Sandi)` tetap rapi.
3. Pastikan penutup dropdown tetap memuat tautan `Ke Beranda Publik` dan tombol logout `Keluar Akun` berbasis form POST resmi.

---

### 📋 TUGAS 2: Mengeliminasi Dead Space & Menyelaraskan Halaman Profil SuperAdmin (`superadmin/profil/show.blade.php`)
1. Buka berkas [`resources/views/superadmin/profil/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/profil/show.blade.php).
2. Pastikan bagian header Blade menggunakan `@section('topbar_title')` agar judul menyatu di Topbar Navigasi:
   ```blade
   @section('title', 'Biodata & Profil Super Administrator - MagKom')
   @section('topbar_title', 'Biodata & Profil Super Administrator')
   ```
   **PENTING**: Jangan mendefinisikan `@section('page_title')` yang memicu `.page-header-row` di luar card container.
3. Periksa keberadaan teks assertion pengujian:
   - Pastikan teks `'Data Super Administrator'` tetap ada di dalam DOM (tercantum pada badge role, meta deskripsi, atau atribut banner) agar test `StaffProfileAndSettingsTest::test_superadmin_dapat_melihat_halaman_profil_dan_akun` tetap lulus 100%.
4. Di file styling [`resources/css/pages/superadmin/profil-show.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/profil-show.css):
   - Pastikan `.profil-page-wrapper` memiliki `margin: 0 auto; padding: 0 0 32px 0;` tanpa margin-top negatif atau berlebih.
   - Pastikan `.profil-main-card` tidak memiliki gap atas yang anomali.

---

### 📋 TUGAS 3: Menyelaraskan Halaman Pengaturan Akun SuperAdmin (`superadmin/akun/index.blade.php`)
1. Buka berkas [`resources/views/superadmin/akun/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/akun/index.blade.php).
2. Periksa judul topbar:
   ```blade
   @section('title', 'Pengaturan Akun - Super Administrator MagKom')
   @section('topbar_title', 'Pengaturan Akun')
   ```
3. Pastikan **TIDAK ADA** atribut `data-scroll-reveal` pada elemen `.settings-hero-banner`, `.settings-grid-2col`, maupun `.settings-white-card` di halaman ini, agar konten halaman langsung terlihat 100% tanpa risiko opacity 0 saat di-scroll.
4. Pastikan teks assertion testing:
   - Teks `'Akun'` dan `'Ganti Kata Sandi'` tetap ada di halaman (sudah ada pada hero title dan form heading).

---

### 📋 TUGAS 4: Verifikasi & Proteksi Modul Pembimbing (`pembimbing/profil/show.blade.php` & `pembimbing/pengaturan/index.blade.php`)
1. Buka dan audit berkas [`resources/views/pembimbing/profil/show.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/profil/show.blade.php):
   - Verifikasi bahwa halaman pembimbing saat ini sudah rapi, stabil, dan menggunakan `@section('topbar_title', 'Profil & Data Pembimbing')`.
   - **ATURAN MUTLAK USER**: Jika tidak ada kerusakan visual pada modul Pembimbing, **JANGAN UBAH KODE TAMPILANNYA**.
2. Buka dan audit berkas [`resources/views/pembimbing/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/pengaturan/index.blade.php):
   - Tambahkan `@section('topbar_title', 'Pengaturan Akun')` agar topbar menampilkan judul yang konsisten dengan halaman lainnya.
   - Pastikan tidak ada class motion yang menyembunyikan konten.

---

## 6. ⚠️ BATASAN KETAT ARSITEKTUR REPOSITORI (`AGENTS.md`)

Junior Developer dan AI pelaksana wajib mematuhi aturan baku berikut:
1. **Dilarang Menulis Tag `<style>` atau `<script>` di Dalam Blade**:
   - Seluruh styling harus berada di `resources/css/` dan interaksi di `resources/js/`.
   - Modul pengaturan akun telah terpusat di `resources/css/components/pengaturan-akun.css`.
2. **Pertahankan Seluruh Kontrak Routing & Keamanan**:
   - Route `superadmin.profil.show`, `superadmin.profil.update`, `superadmin.akun.index`, `superadmin.akun.password.update`, dan `superadmin.akun.preferensi.update` tidak boleh diubah.
   - Route `pembimbing.profil.show`, `pembimbing.profil.update`, `pembimbing.pengaturan.index`, dan seterusnya tetap dipertahankan.
   - Tetap wajib menggunakan CSRF token (`@csrf`) dan method spoofing (`@method('PUT')`).
3. **Zero-Regression Testing**:
   - Pengujian `tests/Feature/StaffProfileAndSettingsTest.php` harus tetap lulus 14/14 tests (91 assertions).

---

## 7. 🧪 QUALITY GATES & VERIFIKASI SEBELUM TUGAS DINYATAKAN SELESAI

Tugas dinyatakan berhasil hanya jika seluruh checklist verifikasi berikut terpenuhi:

| No | Parameter Pemeriksaan | Perintah / Uji Aktual | Kriteria Sukses |
|---|---|---|---|
| 1 | **Feature Tests Lolos 100%** | `C:\Users\galan\php83\php.exe artisan test --filter=StaffProfileAndSettingsTest` | 14 passed (91 assertions), 0 failures |
| 2 | **Standardisasi Popover Dropdown** | Buka topbar dropdown di SuperAdmin, Pembimbing, Admin | Nama menu seragam: `Dashboard`, `Biodata & Profil`, `Pengaturan Akun (Ganti Kata Sandi)`, `Ke Beranda Publik`, `Keluar Akun` |
| 3 | **Eliminasi Blank Space Profil** | Inspeksi `/superadmin/profil` di browser | Tidak ada ruang kosong putih raksasa di atas banner; topbar menyatu rapi |
| 4 | **Visibilitas Pengaturan Akun** | Inspeksi `/superadmin/akun` di browser | Hero banner, kartu kredensial, form ganti sandi, dan kartu tema langsung muncul seketika tanpa scroll-reveal bug |
| 5 | **Stabilitas Modul Pembimbing** | Inspeksi `/pembimbing/profil` & `/pembimbing/pengaturan` | Halaman tetap utuh, rapi, dan tidak mengalami regresi |
| 6 | **Formatting Standar Pint** | `./vendor/bin/pint --test` | Seluruh file Blade/PHP lolos Pint formatting |
| 7 | **Kompilasi Aset Frontend** | `npm run build` | Sukses tanpa error build manifest Vite |

---

> **Disusun oleh:** Senior Developer & Frontend Architect MagKom  
> **Tanggal:** 10 November 2026  
> **Dokumen Tersimpan di:** `docs/plans/2026-11-10-plan-paritas-dropdown-popover-dan-halaman-profil-akun.md`

