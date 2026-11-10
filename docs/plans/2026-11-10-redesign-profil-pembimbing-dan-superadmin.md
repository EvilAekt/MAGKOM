# TASK & PRD IMPLEMENTASI: Redesain UI Profil Pembimbing & SuperAdmin Selaras Desain Peserta & Topbar Dropdown

> **Dokumen Task / PRD**: `docs/plans/2026-11-10-redesign-profil-pembimbing-dan-superadmin.md`  
> **Target Pelaksana**: Junior Full-Stack Developer / AI Model (Gemini 3.8 Flash / Antigravity Agent)  
> **Author / Reviewer**: Senior Full-Stack Engineer (MAGKOM Core Team)  
> **Status**: Menunggu Persetujuan User (Ready for Implementation)  
> **Referensi Visual**: Tangkapan Layar `Biodata & Profil Peserta.png` & `User Dropdown Menu` (terlampir)

---

## 📌 DAFTAR ISI
1. [Latar Belakang & Tujuan](#1-latar-belakang--tujuan)
2. [Aturan Wajib Repositori (Critical Guardrails)](#2-aturan-wajib-repositori-critical-guardrails)
3. [Analisis Komponen Desain & Pemetaan Data](#3-analisis-komponen-desain--pemetaan-data)
4. [Evaluasi Opsi Arsitektur Frontend](#4-evaluasi-opsi-arsitektur-frontend)
5. [Daftar Perubahan File](#5-daftar-perubahan-file)
6. [Panduan Implementasi Tahap demi Tahap (Task Checklist)](#6-panduan-implementasi-tahap-demi-tahap-task-checklist)
   - [Task 1: Pendaftaran Asset di Vite (`vite.config.js`)](#task-1-pendaftaran-asset-di-vite-viteconfigjs)
   - [Task 2: Pembuatan Stylesheet CSS (`resources/css/pages/...`)](#task-2-pembuatan-stylesheet-css-resourcescsspages)
   - [Task 3: Pembuatan Script Interaktif ES Modules (`resources/js/pages/...`)](#task-3-pembuatan-script-interaktif-es-modules-resourcesjspages)
   - [Task 4: Refaktor Template Blade Pembimbing (`pembimbing/profil/show.blade.php`)](#task-4-refaktor-template-blade-pembimbing-pembimbingprofilshowbladephp)
   - [Task 5: Refaktor Template Blade SuperAdmin (`superadmin/profil/show.blade.php`)](#task-5-refaktor-template-blade-superadmin-superadminprofilshowbladephp)
   - [Task 6: Standardisasi User Dropdown Topbar (`layouts/dashboard.blade.php`)](#task-6-standardisasi-user-dropdown-topbar-layoutsdashboardbladephp)
   - [Task 7: Build Asset & Eksekusi Regression Testing](#task-7-build-asset--eksekusi-regression-testing)
7. [Blueprint & Boilerplate Kode yang Siap Pakai](#7-blueprint--boilerplate-kode-yang-siap-pakai)
8. [Matriks Pengujian & Verifikasi (QA Test Matrix)](#8-matriks-pengujian--verifikasi-qa-test-matrix)
9. [Kriteria Selesai (Definition of Done)](#9-kriteria-selesai-definition-of-done)

---

## 1. Latar Belakang & Tujuan

Halaman **Biodata & Profil Peserta** (`/peserta/profil`) telah sukses dimodernisasi menggunakan tata letak *single-container fluid card* yang bersih, elegan, dan berstandar pemerintahan (mengacu pada token warna Sleman Navy `#01347C`, aksen emas/amber `#FFCB05`, dan latar `#F8FAFC`).

Namun saat ini, halaman **Profil Pembimbing** (`/pembimbing/profil`) dan **Profil SuperAdmin** (`/superadmin/profil`):
1. Masih menggunakan tata letak lama berbasis 2 kolom asimetris Gentelella (`x-gentelella.card`).
2. Mengandung pelanggaran aturan arsitektur MAGKOM (`AGENTS.md`) karena memiliki ratusan baris inline `<style>` dan inline `<script>` di dalam file Blade masing-masing (>800 baris per file).
3. Dropdown user di navbar topbar `layouts.dashboard` belum memiliki kartu identitas atas (*rich user header*) yang seragam dengan `layouts.peserta` (foto/inisial, nama lengkap, email, dan pill badge peran huruf kapital).

### 🎯 Tujuan Utama:
1. Menyelaraskan tampilan halaman **Profil Pembimbing** dan **Profil SuperAdmin** dengan visual model profil yang baru (seperti pada tangkapan layar 1 & 2), tetapi **tetap mempertahankan isi data dan logika bisnis masing-masing peran**.
2. Menyelaraskan tampilan **User Dropdown Topbar** di `layouts/dashboard.blade.php` agar identik dengan model dropdown di `layouts/peserta.blade.php` (tangkapan layar 3).
3. Mengekstraksi seluruh CSS dan JavaScript dari Blade ke file terdedikasi di `resources/css/` dan `resources/js/` yang dikompilasi melalui Vite.
4. Memastikan seluruh pengujian otomatis (`StaffProfileAndSettingsTest`) lulus 100% tanpa regresi.

---

## 2. Aturan Wajib Repositori (Critical Guardrails)

Junior developer dan AI wajib membaca dan menaati aturan `g:\MAGKOM\.agents\rules\AGENTS.md`:

| # | Aturan | Larangan Keras | Kewajiban |
|---|---|---|---|
| 1 | **Blade Bukan Tempat CSS/JS** | DILARANG menyisipkan tag `<style>` dan `<script>` di dalam file `.blade.php`. | Semua styling harus di `resources/css/`, semua logika DOM di `resources/js/`. |
| 2 | **No Inline Attributes** | DILARANG menambahkan `style="..."` statis atau inline event handler seperti `onclick="..."`, `onchange="..."`. | Gunakan semantic classes dan `addEventListener` di modul JS terpisah. |
| 3 | **Stack & Dependencies** | DILARANG menambahkan Tailwind, React, Vue, jQuery, atau library UI baru. | Gunakan Vanilla CSS (CSS variables/design tokens) dan Vanilla JavaScript (ES Modules). |
| 4 | **Kontrak Backend Utuh** | DILARANG mengubah nama route, method controller, request validation, form input names, atau struktur tabel database. | Pertahankan `StaffProfilRequest`, `route('pembimbing.profil.update')`, `route('superadmin.profil.update')`. |
| 5 | **Runtime Environment** | DILARANG menggunakan `--ignore-platform-reqs` atau PHP selain 8.3. | Jalankan testing menggunakan PHP 8.3 lokal. |

---

## 3. Analisis Komponen Desain & Pemetaan Data

### A. Anatomi Desain Referensi (Sesuai Screenshot):
1. **Outer Container (`.profil-main-card`)**: Card putih beradius `16px`, subtle border `#E2E8F0`, padding lega `28px 32px`, responsif mobile-to-desktop.
2. **Navy Hero Banner (`.profil-identity-banner`)**:
   - Background gradien Deep Sleman Navy (`#02275D` ke `#01347C`).
   - Avatar lingkaran (diameter 72px) dengan inisial atau foto profil, serta badge overlay kamera (`#FFCB05`) di sudut kanan bawah.
   - Baris Identitas: Nama Pengguna, Pill Badge Peran berwarna kuning/amber, dan Email resmi di baris kedua.
   - Sisi Kanan: Tombol putih "Pilih Pasfoto" dengan ikon upload dan link "Hapus Pasfoto" berwarna merah muda.
3. **Status / Indicator Card (`.profil-completeness-card`)**:
   - Judul kategori dan indikator persentase/rasio tebal.
   - Progress bar tergradasi halus.
   - Checklist status 4-5 poin dengan ikon centang aktif dan lingkaran pending.
4. **Form Section Card 1: Informasi Kedinasan / Penugasan (`.profil-section-card`)**:
   - Header: Icon squircle navy bergradien + Judul Seksi + Subjudul + Tombol "Edit" (pencil icon) di kanan atas.
   - Grid 2-kolom: Field input nama (editable), field email resmi (readonly dengan lock icon), field penugasan/instansi (readonly dengan lock icon), dan field peran kedinasan (readonly dengan lock icon).
5. **Form Section Card 2: Kontak Bimbingan / Otoritas (`.profil-section-card`)**:
   - Header: Icon squircle navy + Judul Seksi Kontak + Subjudul + Tombol "Edit" di kanan atas.
   - Field: Nomor WhatsApp / Telepon Aktif (editable, dengan icon handphone leading dan helper text).
6. **Bottom Action Bar (`.profil-bottom-actions`)**:
   - Checkbox konfirmasi kebenaran data kedinasan.
   - Tombol utama submit: "Simpan Perubahan Profil" (navy solid button, ikon centang, state disabled jika belum setuju, dan spinner loading saat submit).
7. **User Dropdown Topbar (Parity Screenshot 3)**:
   - Header popup dropdown: Avatar lingkaran, Nama Staf, Email, Pill Badge peran huruf kapital tebal (misal: `PEMBIMBING LAPANGAN`, `SUPER ADMINISTRATOR`).
   - Garis pemisah (`dropdown-divider`).
   - Navigasi link dengan ikon garis SVG halus.
   - Garis pemisah kedua.
   - Tombol logout resmi (POST CSRF) berwarna merah dengan ikon keluar: "Keluar Akun".

---

### B. Pemetaan Data Spesifik Peran:

| Komponen Desain | Halaman Pembimbing (`pembimbing.profil.show`) | Halaman SuperAdmin (`superadmin.profil.show`) |
|---|---|---|
| **Page Title** | `Profil & Data Pembimbing Lapangan` | `Profil & Data Super Administrator` |
| **Subtitle** | *Kelola identitas, pasfoto, dan kontak pembimbingan peserta magang.* | *Kelola identitas, pasfoto, dan kontak otoritas tata kelola sistem MagKom.* |
| **Banner Badge** | `BIDANG {nama_bidang}` (misal: `APTIKA`) atau `PEMBIMBING LAPANGAN` | `SUPER ADMINISTRATOR - OTORITAS SISTEM` |
| **Indicator Card Title** | `KAPASITAS & STATISTIK BIMBINGAN` | `OTORITAS & MONITORING TATA KELOLA` |
| **Indicator Ratio / KPI** | Rasio mentee aktif: `{{ $menteeAktifCount }} / {{ $maksPeserta }} Mentee` | Status otoritas: `{{ $totalStafAktif }} Staf Aktif • {{ $totalAuditTrail }} Log` |
| **Checklist Items** | 1. Akun Pembimbing Terverifikasi<br>2. Pasfoto Resmi<br>3. Bidang Penugasan Dinas<br>4. Kontak WhatsApp Aktif<br>5. Kuota Bimbingan Tersedia | 1. Otoritas Superuser Aktif<br>2. Pasfoto Resmi<br>3. Instansi Induk Diskominfo<br>4. Kontak Darurat Terdaftar<br>5. Audit Trail Terintegrasi |
| **Section 1 Title** | `Informasi Identitas & Penugasan Bimbingan` | `Informasi Kedinasan & Identitas Otoritas` |
| **Field 1 (Editable)** | `nama` (Nama Lengkap Pembimbing beserta Gelar) | `nama` (Nama Lengkap Super Administrator) |
| **Field 2 (Readonly)** | `email` (Email Akun Pembimbing) | `email` (Email Utama Super Administrator) |
| **Field 3 (Readonly)** | `Bidang Penugasan Dinas` (`{{ $bidang->nama ?? 'Aptika/IKP' }}`) | `Unit Kerja / Instansi` (`Dinas Kominfo Kab. Sleman`) |
| **Field 4 (Readonly)** | `Peran Bimbingan` (`Pembimbing Lapangan Magang`) | `Peran Tata Kelola` (`Super Administrator (Pengelola Utama)`) |
| **Section 2 Title** | `Kontak Bimbingan & Koordinasi` | `Kontak Resmi Otoritas & Keamanan` |
| **Field 5 (Editable)** | `no_telp` (Nomor WhatsApp / Telepon Pembimbing) | `no_telp` (Nomor Telepon Darurat / WhatsApp) |
| **Checkbox Konfirmasi** | "Saya menyatakan data identitas dan kontak bimbingan sudah benar." | "Saya menyatakan data identitas dan kontak otoritas sudah benar." |
| **Action Route (PUT)** | `route('pembimbing.profil.update')` | `route('superadmin.profil.update')` |

---

## 4. Evaluasi Opsi Arsitektur Frontend

### Opsi 1: Menulis Ulang CSS & JS Langsung di Blade Menggunakan `<style>` dan `<script>`
- ❌ **Ditolak**: Melanggar keras Aturan 1 & 2 pada `AGENTS.md`. Membuat file Blade sulit dipelihara (>800 baris) dan tidak memanfaatkan caching Vite.

### Opsi 2: Membuat File CSS/JS Duplikat Terpisah untuk Tiap Peran
- ⚠️ **Kelemahan**: Menghasilkan redundansi kode karena styling form card pembimbing dan superadmin memiliki token visual yang 90% identik.

### Opsi 3 (Rekomendasi Terpilih): Arsitektur Modul Scoped Stylesheet & Shared Tokens
- ✅ **Keunggulan**:
  1. Buat stylesheet terdedikasi `resources/css/pages/pembimbing/profil-show.css` dan `resources/css/pages/superadmin/profil-show.css` yang mewarisi pola desain `.profil-main-card`, `.profil-identity-banner`, `.profil-section-card`.
  2. Buat script modular ES Module `resources/js/pages/pembimbing/profil-show.js` dan perbarui `resources/js/pages/superadmin/profil-show.js` untuk mengelola live preview foto, smooth focus tombol Edit, checkbox agreement toggle, dan loading spinner saat submit.
  3. Daftarkan di `vite.config.js` sehingga aset dikompilasi secara optimal.
  4. File Blade murni berisi markup semantik bersih (~180-220 baris), mudah dibaca oleh junior/AI mana pun.

---

## 5. Daftar Perubahan File

| File Path | Tindakan | Deskripsi Perubahan |
|---|---|---|
| `vite.config.js` | **Modify** | Daftarkan entry point CSS & JS baru untuk profil pembimbing dan superadmin. |
| `resources/css/pages/pembimbing/profil-show.css` | **Create** | Stylesheet scoped untuk halaman profil Pembimbing selaras desain referensi. |
| `resources/css/pages/superadmin/profil-show.css` | **Create** | Stylesheet scoped untuk halaman profil SuperAdmin selaras desain referensi. |
| `resources/js/pages/pembimbing/profil-show.js` | **Create** | Modul JS interaktif: upload/preview pasfoto, tombol Edit scroll & focus, validasi tombol simpan, spinner. |
| `resources/js/pages/superadmin/profil-show.js` | **Modify** | Lengkapi fungsi tombol Edit scroll & focus, checkbox toggle, dan spinner submit. |
| `resources/views/pembimbing/profil/show.blade.php` | **Modify** | Restrukturisasi layout ke single-card modern, hapus tag `<style>` dan `<script>` inline, load via `@vite`. |
| `resources/views/superadmin/profil/show.blade.php` | **Modify** | Restrukturisasi layout ke single-card modern, hapus tag `<style>` inline, load via `@vite`. |
| `resources/views/layouts/dashboard.blade.php` | **Modify** | Tambahkan `.dropdown-header` di `#userDropdown` agar memiliki kartu profil atas yang presisi dengan tangkapan layar 3. |
| `tests/Feature/StaffProfileAndSettingsTest.php` | **Verify** | Jalankan pengujian otomatis untuk memverifikasi fungsionalitas backend tidak terganggu. |

---

## 6. Panduan Implementasi Tahap demi Tahap (Task Checklist)

Setiap langkah berikut bersifat atomic dan wajib dijalankan secara berurutan:

### Task 1: Pendaftaran Asset di Vite (`vite.config.js`)
- [x] Buka `vite.config.js`.
- [x] Tambahkan baris berikut pada array `input`:
  ```javascript
  'resources/css/pages/pembimbing/profil-show.css',
  'resources/css/pages/superadmin/profil-show.css',
  'resources/js/pages/pembimbing/profil-show.js',
  ```
  *(Catatan: `resources/js/pages/superadmin/profil-show.js` sudah terdaftar sebelumnya).*
- [x] Simpan file dan pastikan sintaks array tetap valid.

---

### Task 2: Pembuatan Stylesheet CSS (`resources/css/pages/...`)
- [x] Buat file `resources/css/pages/pembimbing/profil-show.css` dan `resources/css/pages/superadmin/profil-show.css`.
- [x] Masukkan struktur kelas styling berikut (mengacu pada `resources/css/pages/peserta/profil-show.css`):
  - `.profil-page-wrapper`: Container luar dengan padding bawah `32px`.
  - `.profil-main-card`: Card container putih `#ffffff`, radius `16px`, border `1px solid #E2E8F0`, padding `28px 32px`, flex column gap `20px`.
  - `.profil-alert` (`.profil-alert-success`, `.profil-alert-danger`): Notifikasi flash message modern.
  - `.profil-identity-banner`: Background radial & linear gradient (`#02275D` ke `#01347C`), padding `20px 24px`, radius `14px`, border transparan putih.
  - `.profil-avatar-box` & `.profil-avatar-img-wrap`: Lingkaran avatar 72px, border putih 3px, inisial 1.75rem tebal.
  - `.profil-camera-badge`: Tombol kamera kuning `#FFCB05` dengan border putih di sudut avatar.
  - `.profil-user-name` & `.profil-badge-role`: Tipografi nama 1.15rem putih dan pill badge peran kuning emas.
  - `.profil-btn-upload-photo`: Tombol putih "Pilih Pasfoto" dengan icon upload.
  - `.profil-completeness-card`: Card KPI & Checklist latar `#F8FAFC`, border `#E2E8F0`, progress bar ratio.
  - `.profil-section-card`: Card form putih `#F8FAFC`, radius `14px`, border `#E2E8F0`.
  - `.profil-section-header` & `.profil-section-icon-badge`: Icon squircle navy bergradien dan tombol edit outline.
  - `.profil-fields-grid`: Grid 2 kolom responsif untuk input form.
  - `.profil-input`, `.profil-textarea`: Field input modern dengan focus ring navy `#02275D`.
  - `.profil-lock-icon` & `.profil-input:read-only`: Indikator gembok halus dan background disabled `#F8FAFC`.
  - `.profil-bottom-actions`: Baris aksi bawah berisi checkbox persetujuan dan tombol simpan navy gradient dengan spinner loading.
  - Tambahkan selector tema gelap `[data-theme="dark"]` agar kontras warna tetap sempurna saat mode gelap aktif.

---

### Task 3: Pembuatan Script Interaktif ES Modules (`resources/js/pages/...`)
- [x] Buat `resources/js/pages/pembimbing/profil-show.js` dan perbarui `resources/js/pages/superadmin/profil-show.js`.
- [x] Implementasikan fungsi-fungsi berikut:
  1. **Upload & Instant Preview Pasfoto**:
     - Event click pada tombol "Pilih Pasfoto" dan tombol kamera memicu `inputFotoProfil.click()`.
     - Validasi client-side: jika file > 2 MB atau ekstensi bukan jpg/png/webp, beri peringatan dan batalkan.
     - `FileReader` mengubah `src` gambar pratinjau secara instan dan menyembunyikan inisial avatar.
     - Menghubungkan tombol "Hapus Pasfoto" dengan konfirmasi dialog dan input hidden `hapus_foto = 1`.
  2. **Tombol "Edit" Scroll & Focus**:
     - Klik tombol "Edit" pada Seksi 1 mengarahkan kursor dan fokus halus (*smooth focus*) ke input `nama`.
     - Klik tombol "Edit" pada Seksi 2 mengarahkan fokus ke input `no_telp`.
  3. **Checkbox Persetujuan & Tombol Submit**:
     - Checkbox "Saya menyatakan bahwa data sudah benar..." mengaktifkan/menonaktifkan tombol submit.
     - Saat form disubmit, tombol otomatis dinonaktifkan (`disabled = true`) dan memunculkan animasi spinner untuk mencegah klik ganda (*double submission*).

---

### Task 4: Refaktor Template Blade Pembimbing (`pembimbing/profil/show.blade.php`)
- [x] Buka `resources/views/pembimbing/profil/show.blade.php`.
- [x] Ganti baris `@push('styles')` dan `@push('scripts')` dengan memanggil `@vite`:
  ```blade
  @push('styles')
      @vite(['resources/css/pages/pembimbing/profil-show.css'])
  @endpush

  @push('scripts')
      @vite(['resources/js/pages/pembimbing/profil-show.js'])
  @endpush
  ```
- [x] Bersihkan seluruh tag `<style>` dan `<script>` inline yang saat ini ada di bagian bawah file (hapus baris 272-880).
- [x] Terapkan struktur markup semantic modern:
  - Form pembimbing membungkus card identitas, card statistik, card identitas kedinasan, card kontak, dan action bar.
  - Hubungkan data `$menteeAktifCount`, `$maksPeserta`, `$bidang`, `$user->nama`, `$user->email`, `$user->no_telp`.
- [x] Pastikan CSRF `@csrf` dan `@method('PUT')` terpasang dengan benar.

---

### Task 5: Refaktor Template Blade SuperAdmin (`superadmin/profil/show.blade.php`)
- [x] Buka `resources/views/superadmin/profil/show.blade.php`.
- [x] Pasang `@vite(['resources/css/pages/superadmin/profil-show.css'])` dan `@vite(['resources/js/pages/superadmin/profil-show.js'])`.
- [x] Hapus seluruh blok `<style>` inline yang ada di bagian bawah file (hapus baris 272-800).
- [x] Terapkan struktur markup semantic modern:
  - Hubungkan data `$totalStafAktif`, `$totalAuditTrail`, `$user->nama`, `$user->email`, `$user->no_telp`.
  - Tampilkan data unit kerja ("Dinas Komunikasi dan Informatika Kabupaten Sleman") dan peran sistem ("Super Administrator (Pengelola Utama)") sebagai field readonly dengan ikon gembok.
- [x] Pastikan CSRF `@csrf` dan `@method('PUT')` terpasang dengan benar.

---

### Task 6: Standardisasi User Dropdown Topbar (`layouts/dashboard.blade.php`)
- [x] Buka `resources/views/layouts/dashboard.blade.php`.
- [x] Cari elemen `#userDropdown` (sekitar baris 3528).
- [x] Sisipkan komponen `.dropdown-header` di posisi teratas sebelum daftar menu tautan (mengadopsi pola dari `layouts/peserta.blade.php`):
  ```blade
  <div class="dropdown-header">
      <div class="dropdown-header-avatar" style="overflow: hidden;">
          @if($currentUser?->foto_url)
              <img src="{{ $currentUser->foto_url }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
          @else
              {{ strtoupper(substr($currentUser?->nama ?? 'U', 0, 1)) }}
          @endif
      </div>
      <div class="dropdown-header-meta">
          <span class="dropdown-header-name">{{ $currentUser?->nama ?? 'Pengguna' }}</span>
          <span class="dropdown-header-email">{{ $currentUser?->email ?? '-' }}</span>
          <span class="dropdown-header-role">{{ strtoupper($userRole === 'pembimbing' ? 'Pembimbing Lapangan' : ($userRole === 'superadmin' ? 'Super Administrator' : $userRole)) }}</span>
      </div>
  </div>
  <div class="dropdown-divider"></div>
  ```
- [x] Sesuaikan styling CSS dropdown di `layouts/dashboard.blade.php` atau `dashboard-cockpit.css` jika diperlukan agar ukuran font, radius, dan pembatas selaras dengan referensi tangkapan layar 3.

---

### Task 7: Build Asset & Eksekusi Regression Testing
- [x] Jalankan kompilasi frontend melalui terminal:
  ```powershell
  npm run build
  ```
  *Verifikasi bahwa seluruh file manifest dihasilkan tanpa galat.*
- [x] Jalankan test suite profil staf untuk memastikan tidak ada logika backend yang rusak:
  ```powershell
  php artisan test --filter=StaffProfileAndSettingsTest
  ```
  *Pastikan seluruh test berstatus **PASS** (100% hijau).*
- [x] Lakukan pemeriksaan PHPStan & formatting Pint:
  ```powershell
  ./vendor/bin/pint --test
  ```

---

## 7. Blueprint & Boilerplate Kode yang Siap Pakai

Berikut adalah blueprint markup Blade yang dapat langsung dijadikan acuan oleh Junior Developer atau AI pelaksana:

```blade
{{-- Contoh Blueprint: resources/views/pembimbing/profil/show.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Profil & Data Pembimbing Lapangan - MagKom')
@section('page_title', 'Profil & Data Pembimbing')

@push('styles')
    @vite(['resources/css/pages/pembimbing/profil-show.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/pembimbing/profil-show.js'])
@endpush

@section('content')
<div class="profil-page-wrapper">
    <div class="profil-main-card">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="profil-alert profil-alert-success" role="alert">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="profil-alert profil-alert-danger" role="alert">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Terdapat kesalahan pada input Anda. Silakan periksa kolom yang ditandai merah.</span>
            </div>
        @endif

        {{-- Form Profil --}}
        <form action="{{ route('pembimbing.profil.update') }}" method="POST" id="formProfilStaff" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="file" id="inputFotoProfil" name="foto" accept="image/jpeg,image/png,image/webp" style="display: none;">
            <input type="hidden" id="inputHapusFoto" name="hapus_foto" value="0">

            {{-- 1. HERO IDENTITY BANNER --}}
            <div class="profil-identity-banner">
                <div class="profil-identity-left">
                    <div class="profil-avatar-box">
                        <div class="profil-avatar-img-wrap">
                            <img id="avatarPreviewImg"
                                 src="{{ $user->foto_url ?? '' }}"
                                 alt="Pasfoto {{ $user->nama }}"
                                 class="profil-avatar-img"
                                 style="{{ $user->foto_url ? 'display: block;' : 'display: none;' }}">
                            <span id="avatarFallbackIcon"
                                  class="profil-avatar-initial"
                                  style="{{ $user->foto_url ? 'display: none;' : 'display: block;' }}">
                                {{ strtoupper(substr($user->nama ?? 'P', 0, 1)) }}
                            </span>
                        </div>
                        <button type="button" class="profil-camera-badge" id="btnPilihFotoOverlay" title="Ganti pasfoto" aria-label="Ganti pasfoto">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="profil-user-info">
                        <div class="profil-name-row">
                            <h2 class="profil-user-name">{{ $user->nama }}</h2>
                            <span class="profil-badge-role">
                                {{ is_object($bidang ?? null) ? ($bidang->nama ?? 'BIDANG DINAS') : 'PEMBIMBING LAPANGAN' }}
                            </span>
                        </div>
                        <p class="profil-user-email">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="profil-identity-right">
                    <button type="button" class="profil-btn-upload-photo" id="btnPilihFoto">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        <span>Pilih Pasfoto</span>
                    </button>
                    @if($user->foto)
                        <button type="button" id="btnHapusFoto" class="profil-btn-remove-photo">Hapus Pasfoto</button>
                    @endif
                    <span id="selectedFileName" class="profil-photo-subtext">Maksimal 2 MB (JPG, PNG, WEBP)</span>
                </div>
            </div>

            {{-- 2. STATUS & KAPASITAS CARD --}}
            <div class="profil-completeness-card">
                <div class="profil-completeness-header">
                    <span class="profil-completeness-title">KAPASITAS & STATISTIK BIMBINGAN</span>
                    <span class="profil-completeness-percent">{{ $menteeAktifCount }} / {{ $maksPeserta }} Mentee Aktif</span>
                </div>
                <div class="profil-progress-track">
                    <div class="profil-progress-fill" style="width: {{ min(100, round(($menteeAktifCount / max(1, $maksPeserta)) * 100)) }}%;"></div>
                </div>
                <div class="profil-checklist-grid">
                    <div class="profil-checklist-item">
                        <span class="profil-checklist-icon done">✓</span>
                        <span>Akun Terverifikasi</span>
                    </div>
                    <div class="profil-checklist-item">
                        <span class="profil-checklist-icon {{ $user->foto ? 'done' : 'pending' }}">{{ $user->foto ? '✓' : '○' }}</span>
                        <span>Pasfoto Resmi</span>
                    </div>
                    <div class="profil-checklist-item">
                        <span class="profil-checklist-icon done">✓</span>
                        <span>Bidang Penugasan</span>
                    </div>
                    <div class="profil-checklist-item">
                        <span class="profil-checklist-icon {{ $user->no_telp ? 'done' : 'pending' }}">{{ $user->no_telp ? '✓' : '○' }}</span>
                        <span>WhatsApp Aktif</span>
                    </div>
                    <div class="profil-checklist-item">
                        <span class="profil-checklist-icon done">✓</span>
                        <span>Sisa Kuota: {{ max(0, $maksPeserta - $menteeAktifCount) }} Slot</span>
                    </div>
                </div>
            </div>

            {{-- 3. SEKSI 1: IDENTITAS & PENUGASAN --}}
            <div class="profil-section-card" id="sectionIdentitas">
                <div class="profil-section-header">
                    <div class="profil-section-title-wrap">
                        <div class="profil-section-icon-badge">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="profil-section-heading">Informasi Identitas & Penugasan</h3>
                            <p class="profil-section-subheading">Identitas resmi pembimbing lapangan bagi mahasiswa/siswa magang.</p>
                        </div>
                    </div>
                    <button type="button" class="profil-btn-edit" id="btnEditIdentitas" data-focus-target="nama">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        <span>Edit</span>
                    </button>
                </div>

                <div class="profil-fields-grid">
                    <div class="profil-form-group profil-field-col-full">
                        <label for="nama" class="profil-label">
                            <span>Nama Lengkap Pembimbing <span class="profil-required">*</span></span>
                        </label>
                        <input type="text" id="nama" name="nama" class="profil-input @error('nama') is-invalid @enderror" value="{{ old('nama', $user->nama) }}" placeholder="Contoh: Ir. Nama Lengkap, M.Eng" required>
                        @error('nama') <span class="profil-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="profil-form-group">
                        <label for="email" class="profil-label">
                            <span>Email Resmi (Akun)</span>
                            <svg class="profil-lock-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </label>
                        <input type="email" id="email" class="profil-input" value="{{ $user->email }}" readonly disabled>
                        <span class="profil-helper-text">Perubahan email dikelola oleh Super Administrator.</span>
                    </div>

                    <div class="profil-form-group">
                        <label class="profil-label">
                            <span>Bidang Penugasan Dinas</span>
                            <svg class="profil-lock-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </label>
                        <input type="text" class="profil-input" value="{{ is_object($bidang ?? null) ? ($bidang->nama ?? 'Aptika/IKP') : ($bidang ?? $user->bidang?->nama ?? 'Bidang Penugasan MagKom') }}" readonly disabled>
                    </div>

                    <div class="profil-form-group profil-field-col-full">
                        <label class="profil-label">
                            <span>Peran Penugasan</span>
                            <svg class="profil-lock-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </label>
                        <input type="text" class="profil-input" value="Pembimbing Lapangan Magang" readonly disabled>
                    </div>
                </div>
            </div>

            {{-- 4. SEKSI 2: KONTAK OPERASIONAL --}}
            <div class="profil-section-card" id="sectionKontak">
                <div class="profil-section-header">
                    <div class="profil-section-title-wrap">
                        <div class="profil-section-icon-badge">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="profil-section-heading">Kontak Bimbingan & Komunikasi</h3>
                            <p class="profil-section-subheading">Nomor kontak aktif untuk memudahkan koordinasi bimbingan tugas.</p>
                        </div>
                    </div>
                    <button type="button" class="profil-btn-edit" id="btnEditKontak" data-focus-target="no_telp">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        <span>Edit</span>
                    </button>
                </div>

                <div class="profil-fields-grid">
                    <div class="profil-form-group profil-field-col-full">
                        <label for="no_telp" class="profil-label">
                            <span>Nomor WhatsApp / Telepon Aktif</span>
                        </label>
                        <input type="tel" id="no_telp" name="no_telp" class="profil-input @error('no_telp') is-invalid @enderror" value="{{ old('no_telp', $user->no_telp) }}" placeholder="Contoh: 081234567890">
                        @error('no_telp') <span class="profil-field-error">{{ $message }}</span> @enderror
                        <span class="profil-helper-text">Digunakan oleh peserta magang untuk menghubungi pembimbing saat konfirmasi tugas dan agenda harian.</span>
                    </div>
                </div>
            </div>

            {{-- 5. BOTTOM ACTION BAR --}}
            <div class="profil-bottom-actions">
                <label class="profil-agreement-label">
                    <input type="checkbox" id="checkPersetujuan" class="profil-checkbox">
                    <span>Saya menyatakan bahwa data profil dan kontak bimbingan yang saya perbarui adalah benar dan siap digunakan untuk koordinasi magang.</span>
                </label>
                <button type="submit" class="profil-btn-submit" id="btnSubmitProfil" disabled>
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
```

---

## 8. Matriks Pengujian & Verifikasi (QA Test Matrix)

Junior developer / AI pelaksana wajib memvalidasi setiap skenario pengujian berikut sebelum melaporkan pekerjaan selesai:

| ID Skenario | Kategori | Kondisi Pengujian | Ekspektasi Hasil |
|---|---|---|---|
| **QA-01** | Visual | Akses `/pembimbing/profil` dengan akun Pembimbing | Halaman dirender dalam single-card fluid putih, banner navy, dan tanpa tag `<style>`/`<script>` inline. |
| **QA-02** | Visual | Akses `/superadmin/profil` dengan akun SuperAdmin | Tampilan identik, metrik menampilkan total staf aktif dan total audit log. |
| **QA-03** | Interaksi | Klik tombol "Edit" pada Seksi 1 atau Seksi 2 | Layar melakukan smooth scroll dan kursor langsung memfokuskan field `nama` atau `no_telp`. |
| **QA-04** | Pasfoto | Memilih file gambar baru (< 2MB) | Live preview avatar langsung berubah, inisial disembunyikan, nama file terpampang. |
| **QA-05** | Pasfoto | Memilih file gambar > 2MB | Sistem membatalkan pilihan file dan memunculkan toast/alert batas maksimal 2MB. |
| **QA-06** | Form | Checkbox persetujuan belum dicentang | Tombol "Simpan Perubahan Profil" terkunci (*disabled*). |
| **QA-07** | Form | Checkbox dicentang dan form disubmit | Data tersimpan ke database, redirect dengan session success, tombol terkunci dengan spinner loading saat proses submit. |
| **QA-08** | Dropdown | Klik avatar pengguna di topbar navbar | Menu dropdown terbuka ke bawah dengan header kartu profil lengkap (inisial/foto, nama, email, role badge) sesuai gambar 3. |
| **QA-09** | Backend Test | Menjalankan `php artisan test --filter=StaffProfileAndSettingsTest` | Seluruh unit/feature test berstatus **PASS** (100% hijau). |
| **QA-10** | Asset Build | Menjalankan `npm run build` | Kompilasi Vite sukses 0 error, manifest terbarui. |

---

## 9. Kriteria Selesai (Definition of Done)

Tugas ini dinyatakan selesai jika dan hanya jika:
- [x] File `resources/views/pembimbing/profil/show.blade.php` telah direfaktor ke model card tunggal dan **0 baris inline `<style>` maupun `<script>`**.
- [x] File `resources/views/superadmin/profil/show.blade.php` telah direfaktor ke model card tunggal dan **0 baris inline `<style>` maupun `<script>`**.
- [x] Stylesheet `resources/css/pages/pembimbing/profil-show.css` dan `resources/css/pages/superadmin/profil-show.css` telah dibuat dan terdaftar di `vite.config.js`.
- [x] Script `resources/js/pages/pembimbing/profil-show.js` dan `resources/js/pages/superadmin/profil-show.js` telah dibuat/diperbarui dan terdaftar di `vite.config.js`.
- [x] Header dropdown user di `resources/views/layouts/dashboard.blade.php` menampilkan header identitas lengkap (avatar, nama, email, badge peran) selaras gambar referensi 3.
- [x] Perintah `npm run build` berhasil dijalankan tanpa galat.
- [x] Perintah `php artisan test --filter=StaffProfileAndSettingsTest` berhasil lulus 100%.
- [x] Mode gelap (`[data-theme="dark"]`) dan tampilan responsif mobile terverifikasi rapi dan terbaca jelas.
- [x] Seluruh perubahan dilaporkan secara transparan sesuai format pelaporan `AGENTS.md`.

