# TASK BRIEF SENIOR DEVELOPER: REDESAIN PROFIL ADMINISTRASI & DROPDOWN AKUN BERGAYA PORTAL PESERTA (DESIGN SYSTEM UNIFORMITY)

> **Dari:** Senior Developer / Lead Frontend & Backend Architect  
> **Kepada:** Junior Developer / AI Agent (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Kabupaten Sleman)  
> **Status:** READY FOR IMPLEMENTATION (DRAFT RENCANA DETAIL - MENUNGGU PERSETUJUAN PENGGUNA)  
> **Tingkat Kesulitan:** Menengah (*Blade Refactoring*, *Vanilla CSS Scoped*, *ES Modules JavaScript*, *Vite Integration*, *Zero-Regression Testing*)  
> **Halaman Acuan (Gold Standard):**  
> 1. Gambar 1: Dropdown Profil Akun Topbar ([`media_1794316768007_3446e67c.png`](file:///C:/Users/ALDI%20BOYS/.gemini/antigravity/brain/8ac85b61-3384-4008-bb69-7f98f2cbd22d/.user_uploaded/media_1794316768007_3446e67c.png))  
> 2. Gambar 2 & 3: Halaman Profil & Biodata ([`resources/views/peserta/profil/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/peserta/profil/show.blade.php) & [`resources/css/pages/peserta/profil-show.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/peserta/profil-show.css))  
> **Halaman Target:**  
> 1. Topbar Navigation Dropdown: [`resources/views/layouts/dashboard.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php)  
> 2. Profil Staf Administrasi View: [`resources/views/admin/profil/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/profil/show.blade.php)  
> 3. Scoped Stylesheet: [`resources/css/pages/admin/profil-show.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/profil-show.css)  
> 4. Scoped ES Module JS: [`resources/js/pages/admin/profil-show.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/admin/profil-show.js)  
> **Dokumentasi & Regulasi Terkait:** `AGENTS.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `tests/Feature/StaffProfileAndSettingsTest.php`

---

## 1. 🔍 LATAR BELAKANG & ANALISIS KESENJANGAN VISUAL (VISUAL GAP ANALYSIS)

### 1.1 Masalah Tampilan Profil Administrasi Saat Ini
Halaman Profil Administrasi saat ini ([`resources/views/admin/profil/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/profil/show.blade.php)) masih menggunakan tata letak warisan (*legacy two-column Gentelella layout*) yang terpisah menjadi kartu identitas sempit di sisi kiri dan form di sisi kanan. Selain itu:
1. **Pelanggaran Aturan Arsitektur `AGENTS.md`**: File Blade profil admin saat ini memuat lebih dari 600 baris tag `<style>` dan `<script>` inline di dalam file Blade itu sendiri. Aturan `AGENTS.md` secara tegas melarang penulisan CSS dan JS langsung di dalam Blade.
2. **Kesenjangan Estetika Antara Modul Peserta dan Admin**:
   - Modul Peserta (Gambar 2 & 3) telah memiliki antarmuka yang sangat modern, bersih, berwibawa khas kedinasan Kominfo Sleman (kartu identitas *Navy Banner* `#02275D`, badge emas `#FFCB05`, kartu indikator metrik, *section cards* berikon *rounded navy*, dan *action bar* bawah yang tertata rapi).
   - Sebaliknya, modul Administrasi terlihat kaku, tidak serasi, dan terasa seperti modul usang yang belum terintegrasi ke dalam *Design System MagKom 2026*.
3. **Dropdown Profil Pengguna pada Topbar (Gambar 1)**:
   - Menu dropdown profil pada Topbar Admin ([`resources/views/layouts/dashboard.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php)) saat ini masih polos, belum memiliki *Dropdown Header Box* yang memuat avatar foto/inisial, nama lengkap pengguna, email resmi, dan pill badge role emas (`ADMINISTRATOR DINAS`) seperti yang tampak pada Gambar 1.

---

## 2. 🗺️ PEMETAAN DATA EKSISTING KE MODEL BARU (DATA-TO-DESIGN MAPPING)

Tampilan baru **wajib menggunakan 100% data dan variabel yang telah disediakan oleh backend eksisting**, tanpa mengubah kontrak controller maupun skema database:

| Komponen Visual Acuan (Gambar 1, 2, 3) | Elemen pada Desain Peserta | Implementasi Pada Profil Administrasi (Konten Sekarang) | Sumber Data Backend (`ProfilController.php`) |
|---|---|---|---|
| **Topbar Dropdown Header (Gambar 1)** | Avatar, Nama, Email, Badge `PESERTA MAGANG` | Avatar Staf, Nama Staf, Email Kedinasan, Badge `ADMINISTRATOR DINAS` | `$currentUser->nama`, `$currentUser->email`, `$userRole` |
| **Topbar Dropdown Menu (Gambar 1)** | Dashboard, Biodata & Profil, Riwayat, Pengaturan, Keluar Akun | **Dashboard**, **Profil Saya**, **Pengaturan Akun**, **Ke Beranda Publik**, **Keluar Akun (Merah)** | Route `admin.dashboard`, `admin.profil.show`, `admin.pengaturan.index`, `landing`, `logout` (POST) |
| **Hero Identity Banner (Gambar 2)** | Avatar 72px + Tombol Kamera Kuning, Nama Peserta, Badge Bidang Kuning, Email, Tombol "Pilih Pasfoto" | **Avatar Staf 72px** (foto/inisial) + **Badge Kamera Kuning**, **Nama Staf Administrasi**, **Badge Emas `ADMINISTRATOR DINAS`**, **Email Resmi Staf**, Tombol Putih **"Pilih Pasfoto"** & Link Merah **"Hapus Pasfoto"** | `$user->nama`, `$user->foto_url`, `$user->email`, `$user->foto` |
| **Indikator Metrik (Gambar 2)** | *Kelengkapan Biodata 80%* + 5 Checklist Pendaftaran | **Status Operasional & Kinerja Administrasi** (Status: `100% Akun Terverifikasi ASN Sleman`), 4 Checklist Kesiapan Operasional, serta **2 Widget Metrik Utama**: Pendaftaran Masuk & Penempatan Disetujui | `$totalPendaftaran`, `$totalPenempatan`, status aktif akun dinas |
| **Section Card 1 (Gambar 2)** | *Informasi Akademik & Pendidikan* (Kampus, NIM, Jenjang) | **Informasi Kedinasan & Identitas Resmi**:<br>• Nama Lengkap Staf * (Editable)<br>• Email Resmi Akun (Readonly gembok)<br>• Instansi / Unit Kerja (Readonly: *Dinas Kominfo Kab. Sleman*)<br>• Peran Akses Sistem (Readonly: *Administrasi Operasional*) | Input form `name="nama"`, `$user->nama`, `$user->email` |
| **Section Card 2 (Gambar 3)** | *Kontak Utama & Jalur Darurat* (No WA, Alamat, Darurat) | **Kontak Operasional & Jalur Koordinasi**:<br>• Nomor WhatsApp / Telepon Aktif * (Editable)<br>• Alamat Kantor Kedinasan (Readonly/Deskriptif: *Kompleks Pemda Sleman*) | Input form `name="no_telp"`, `$user->no_telp` |
| **Bottom Action Bar (Gambar 3)** | Checkbox Konfirmasi Magang + Tombol Simpan Biru | **Checkbox Konfirmasi Operasional Staf**: *"Data profil kedinasan yang saya isikan sudah benar dan siap digunakan untuk administrasi operasional dinas."* + **Tombol Submit Solid Navy** *"✓ Simpan Perubahan Profil"* | Validasi form submission client-side + method PUT `route('admin.profil.update')` |

---

## 3. 🏛️ BLUEPRINT ARSITEKTUR & WIREFRAME VISUAL

### 3.1 Blueprint Halaman Profil Staf Administrasi (`admin/profil/show.blade.php`)

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [TOPBAR NAVIGATION]  MagKom Admin / Profil & Data Staf Administrasi        [Akun v]   │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [ALERT BANNER JIKA ADA FLASH SUCCESS / ERROR]                                          │
├────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                        │
│ ┌────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ ┌─────┐  Budi Santoso, S.Kom, M.Eng   [ ADMINISTRATOR DINAS ]    [  Pilih Pasfoto ] │ │
│ │ │ (B) │  admin.operasional@slemankab.go.id                       (Hapus Pasfoto)   │ │
│ │ └──📷─┘  Dinas Komunikasi dan Informatika Kabupaten Sleman                          │ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ STATUS OPERASIONAL & KINERJA ADMINISTRASI            [ 100% Akun Terverifikasi ASN ]│ │
│ │ ══════════════════════════════════════════════════════════════════════════════════ │ │
│ │  (✓) Akun Resmi Sleman     (✓) Pasfoto Kedinasan    (✓) Nomor WA    (✓) Hak Akses  │ │
│ │ ────────────────────────────────────────────────────────────────────────────────── │ │
│ │   [ 124 ] Pendaftaran Masuk          │          [ 48 ] Penempatan Disetujui        │ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ [🪪] Informasi Kedinasan & Identitas Resmi                                [ Edit ] │ │
│ │ ────────────────────────────────────────────────────────────────────────────────── │ │
│ │  Nama Lengkap Staf *                      Email Resmi (Akun) [🔒 Terkunci]         │ │
│ │  [ Budi Santoso, S.Kom, M.Eng        ]    [ admin.operasional@slemankab.go.id  ]   │ │
│ │                                                                                    │ │
│ │  Instansi / Unit Kerja [🔒 Sistem]         Peran Akses [🔒 Sistem]                  │ │
│ │  [ Dinas Komunikasi dan Informatika  ]    [ Administrasi Operasional (Verifikasi)] │ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ [📞] Kontak Operasional & Jalur Koordinasi                                [ Edit ] │ │
│ │ ────────────────────────────────────────────────────────────────────────────────── │ │
│ │  Nomor WhatsApp / Telepon Aktif *         Alamat Kantor Kedinasan [🔒 Sistem]      │ │
│ │  [ 081234567890                      ]    [ Jl. Parasamya No. 1, Beran, Sleman ]   │ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ [ ] Data profil kedinasan yang saya isikan sudah benar.  [ ✓ Simpan Perubahan Profil]│
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

### 3.2 Blueprint Dropdown Topbar Akun Admin (`layouts/dashboard.blade.php`)

```text
       ┌─────────────────────────────────────────────────┐
       │ ┌─────┐  Budi Santoso                           │
       │ │ (B) │  admin.operasional@slemankab.go.id      │
       │ └─────┘  [ ADMINISTRATOR DINAS ]                │
       ├─────────────────────────────────────────────────┤
       │  [🏠]  Dashboard                                │
       │  [👤]  Profil Saya                              │
       │  [⚙️]  Pengaturan Akun (Ganti Kata Sandi)        │
       │  [🌐]  Ke Beranda Publik                        │
       ├─────────────────────────────────────────────────┤
       │  [🚪]  Keluar Akun                              │ (Merah)
       └─────────────────────────────────────────────────┘
```

---

## 4. 🎨 TOKEN DESAIN & STANDAR STYLING (SESUAI MAGKOM DESIGN SYSTEM)

Semua class visual harus mengikuti token warna resmi yang selaras dengan portal peserta:

| Token / Variabel | Nilai HEX / CSS | Penggunaan Spesifik |
|---|---|---|
| `--magkom-navy-dark` | `#02275D` | Background Banner Kiri, Tombol Utama Submit, Header Card Icon |
| `--magkom-navy-mid` | `#01347C` | Gradien Banner Tengah, State Hover Tombol Submit |
| `--magkom-gold` | `#FFCB05` | Overlay Badge Kamera, Role Pill Badge `ADMINISTRATOR DINAS` |
| `--magkom-gold-hover` | `#F59E0B` | State Hover Badge Kamera |
| `--magkom-surface-white` | `#FFFFFF` | Background Card Utama, Tombol "Pilih Pasfoto" |
| `--magkom-border-soft` | `#E2E8F0` | Border Container Card, Input Border, Divider |
| `--magkom-text-main` | `#0F172A` | Judul Halaman, Nama Staf, Label Field |
| `--magkom-text-muted` | `#64748B` | Subtitle, Deskripsi Seksi, Helper Text |
| `--magkom-danger` | `#EF4444` / `#DC2626` | Menu "Keluar Akun", Tombol "Hapus Pasfoto", Input Error |
| `--magkom-success` | `#10B981` | Metric Angka Penempatan Disetujui, Checklist Icon Hijau |

---

## 5. 🛠️ INSTRUKSI LANGKAH DEMI LANGKAH UNTUK JUNIOR DEVELOPER / GEMINI 3.8 FLASH

Junior Developer atau AI Agen pelaksana **wajib** mengikuti 4 tugas di bawah secara runtut:

### 📋 TUGAS 1: Standardisasi Dropdown Akun Topbar di `resources/views/layouts/dashboard.blade.php`
1. Buka berkas [`resources/views/layouts/dashboard.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php) pada baris 3528 (`<div id="userDropdown" class="user-dropdown-menu"...>`).
2. Tambahkan **Header Kartu Profil Dropdown** di bagian paling atas dropdown menu (sebelum item navigasi):
   - Avatar bulat 40px (menampilkan `foto_url` staf jika ada, atau fallback inisial 2 huruf kapital pertama).
   - Nama lengkap pengguna: `{{ $currentUser?->nama ?? 'Pengguna' }}`.
   - Email resmi pengguna: `{{ $currentUser?->email ?? '-' }}`.
   - Badge Role Emas: `ADMINISTRATOR DINAS` (untuk role `admin`), `SUPER ADMINISTRATOR` (untuk role `superadmin`), `PEMBIMBING LAPANGAN` (untuk role `pembimbing`).
3. Tambahkan pemisah horizontal `<div class="dropdown-divider"></div>`.
4. Rapikan item link navigasi dengan icon SVG clean:
   - Dashboard (`admin.dashboard`)
   - Profil Saya (`admin.profil.show`)
   - Pengaturan Akun (Ganti Kata Sandi) (`admin.pengaturan.index`)
   - Ke Beranda Publik (`landing`)
5. Pada bagian tombol logout, pastikan tetap berupa `<form method="POST" action="{{ route('logout') }}">` dengan CSRF token, namun ubah labelnya menjadi `"Keluar Akun"` berwarna merah dengan icon logout merah yang elegan.
6. **PENTING**: Semua styling dropdown dipastikan sudah terdukung di CSS layout dashboard tanpa menambahkan tag `<style>` baru.

---

### 📋 TUGAS 2: Buat Stylesheet Scoped `resources/css/pages/admin/profil-show.css` & Daftarkan di `vite.config.js`
1. Buka [`vite.config.js`](file:///d:/laragon/www/MAGKOM/vite.config.js), lalu tambahkan entri berikut ke dalam array `input`:
   - `'resources/css/pages/admin/profil-show.css',` (di bawah `'resources/css/pages/admin/dashboard.css'`)
   - `'resources/js/pages/admin/profil-show.js',` (di bawah file JS admin/peserta lainnya)
2. Buat berkas baru: [`resources/css/pages/admin/profil-show.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/profil-show.css).
3. Terapkan seluruh struktur CSS modern yang scoped untuk kelas-kelas:
   - `.admin-profil-wrapper`: Container utama berjarak rapi, responsif, dan fluid.
   - `.admin-profil-main-card`: Card putih rounded 16px dengan border `#E2E8F0` dan padding lega (28px 32px).
   - `.profil-identity-banner`: Gradien Navy Sleman `#02275D` ke `#01347C`, border-radius 14px, bayangan halus.
   - `.profil-avatar-box`, `.profil-avatar-img-wrap`, `.profil-avatar-initial`, `.profil-camera-badge`: Styling presisi untuk avatar 72px dengan badge kamera kuning.
   - `.profil-badge-role`: Pill badge emas berhuruf tebal kapital.
   - `.profil-btn-upload-photo`: Tombol putih elegan dengan border lembut dan efek hover naik 1px.
   - `.admin-kpi-card`: Card metrik operasional staf dengan progress bar emas/emerald dan 2 bento metric counter (Pendaftaran Masuk & Penempatan Disetujui).
   - `.admin-section-card`: Card seksi rounded 12px dengan header berikon kotak biru navy, label tebal, dan input field wrapper berikon.
   - `.field-input.readonly`: Styling field input terkunci dengan icon gembok dan background `#F8FAFC`.
   - `.profil-bottom-actions`: Baris aksi bawah yang menyejajarkan checkbox konfirmasi di kiri dan tombol simpan di kanan.
   - `@media (max-width: 768px)`: Responsivitas penuh untuk tablet dan smartphone (tumpukan kolom vertikal).

---

### 📋 TUGAS 3: Buat ES Module Script `resources/js/pages/admin/profil-show.js`
1. Buat berkas baru: [`resources/js/pages/admin/profil-show.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/admin/profil-show.js).
2. Terapkan logika interaksi aman DOM:
   - **Trigger File Input**: Klik tombol `"Pilih Pasfoto"` atau badge kamera kuning memicu `inputFotoProfil.click()`.
   - **Instant Image Preview**: Saat file gambar dipilih:
     - Validasi ukuran file client-side (maksimal 2 MB / `2 * 1024 * 1024` bytes). Jika melebihi batas, batalkan input dan tampilkan alert ramah.
     - Validasi MIME type (`image/jpeg`, `image/png`, `image/webp`).
     - Baca dengan `FileReader` dan perbarui atribut `src` pada `#avatarImg`, sembunyikan inisial, dan tampilkan nama file pada text hint.
   - **Hapus Pasfoto**: Klik tombol `"Hapus Pasfoto"` mengubah hidden input `#inputHapusFoto` menjadi `1`, mereset gambar ke inisial nama, dan menyembunyikan tombol hapus.
   - **Checkbox Agreement Toggle**: Checkbox konfirmasi `"Data profil kedinasan yang saya isikan sudah benar..."` secara dinamis mengaktifkan/menonaktifkan tombol submit (`#btnSimpanProfil.disabled = !checkbox.checked`).
   - **Submit Loading State**: Ketika form di-submit, ubah tombol menjadi status *loading* dengan spinner dan cegah *double submission*.

---

### 📋 TUGAS 4: Tulis Ulang Template Blade `resources/views/admin/profil/show.blade.php`
1. Bersihkan seluruh tag `<style>` dan `<script>` yang ada di berkas [`resources/views/admin/profil/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/profil/show.blade.php).
2. Pasang pemanggilan asset via Vite di bagian atas:
   ```blade
   @push('styles')
       @vite(['resources/css/pages/admin/profil-show.css'])
   @endpush

   @push('scripts')
       @vite(['resources/js/pages/admin/profil-show.js'])
   @endpush
   ```
3. Susun struktur HTML semantik baru:
   - Container: `<div class="admin-profil-wrapper">`
   - Judul & Breadcrumbs: Menggunakan `<x-gentelella.page-title title="Profil & Data Staf Administrasi" ...>` agar teks `'Data Staf Administrasi'` tetap lulus pada Feature Test.
   - Form: `<form action="{{ route('admin.profil.update') }}" method="POST" enctype="multipart/form-data" id="formProfilStaff">` dengan `@csrf` dan `@method('PUT')`.
   - Hidden inputs untuk `foto` dan `hapus_foto`.
   - Card 1: **Hero Identity Banner** (Avatar + Kamera Emas + Nama Staf + Badge Role + Email + Tombol Upload Pasfoto).
   - Card 2: **Kinerja & Status Operasional** (Progress status, 4 checklist kesiapan, counter Pendaftaran Masuk & Penempatan Disetujui).
   - Card 3: **Seksi Informasi Kedinasan & Identitas** (Nama Lengkap Staf editable, Email readonly, Instansi readonly, Peran Akses readonly).
   - Card 4: **Seksi Kontak Operasional** (Nomor Telepon/WhatsApp editable, Alamat Kantor readonly).
   - Footer Bar: **Checkbox Konfirmasi Data** + **Tombol Simpan Perubahan Profil (Solid Navy)**.

---

## 6. ⚠️ BATASAN KETAT ARSITEKTUR & REGULASI REPOSITORI (`AGENTS.md`)

Junior Developer dan AI wajib menaati poin-poin mutlak berikut:
1. **Dilarang Menulis CSS/JS di Blade**: Tidak boleh ada `<style>` atau `<script>` inline di dalam `resources/views/admin/profil/show.blade.php`.
2. **Dilarang Mengubah Kontrak Routing & Controller**:
   - Route GET tetap: `admin.profil.show`
   - Route PUT tetap: `admin.profil.update`
   - Form request tetap: `StaffProfilRequest`
3. **Integritas Pengujian Otomatis (Zero-Regression)**:
   - Feature Test [`tests/Feature/StaffProfileAndSettingsTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/StaffProfileAndSettingsTest.php) melakukan pengecekan teks:
     - `assertSee('Data Staf Administrasi')`
     - `assertSee($this->admin->nama)`
     - `assertSee($this->admin->email)`
   - Seluruh teks ini **WAJIB** tetap muncul di halaman setelah redesain.
4. **Keamanan Backend**:
   - Tetap pertahankan CSRF token (`@csrf`) dan method spoofing (`@method('PUT')`).
   - Tidak membocorkan credential, password, atau token pada form.

---

## 7. 🧪 QUALITY GATES & KRITERIA PENERIMAAN (ACCEPTANCE CRITERIA)

Tugas ini dinyatakan selesai dan siap dimerge hanya jika memenuhi kriteria berikut:

| No | Kriteria Pengujian | Command / Langkah Verifikasi | Target Hasil |
|---|---|---|---|
| 1 | **Feature Test Lulus 100%** | `php artisan test --filter=StaffProfileAndSettingsTest` | 13/13 tests passed, 0 failures, 0 errors |
| 2 | **Paritas Desain Tercapai** | Inspeksi visual pada browser di `http://127.0.0.1:8000/admin/profil` | Layout identik dengan acuan Gambar 2 & 3, Hero Navy Banner rapi, responsive |
| 3 | **Dropdown Topbar Terstandarisasi** | Buka topbar dropdown admin di browser | Tampilan identik Gambar 1: Avatar, Nama, Email, Badge Gold, Menu bersih, Logout merah |
| 4 | **Interaktivitas JavaScript Berjalan** | Pilih foto > 2MB & < 2MB di form | Validasi ukuran file muncul, preview foto instan ter-render, checkbox toggle tombol simpan |
| 5 | **Update Data Berhasil** | Simpan perubahan nama dan nomor telepon | Redirect sukses, pesan flash hijau muncul, data tersimpan di DB |
| 6 | **Static Analysis Bersih** | `./vendor/bin/phpstan analyse --memory-limit=1G` | No errors / Clean |
| 7 | **Formatting Sesuai Standar** | `./vendor/bin/pint --test` | All files pass Pint formatting |
| 8 | **Kompilasi Aset Frontend Berhasil** | `npm run build` | Build Vite sukses tanpa error manifest |

---

> **Disusun oleh:** Senior Developer & Frontend Architect MagKom  
> **Tanggal:** 10 November 2026  
> **Dokumen Tersimpan di:** `docs/plans/2026-11-10-senior-dev-task-redesign-profil-administrasi-gaya-portal-peserta.md`
