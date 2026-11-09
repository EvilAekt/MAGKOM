# TASK BRIEF SENIOR DEVELOPER: STANDARISASI HERO BANNER & 4 TILE-STAT GRID PADA 6 HALAMAN SUPERADMIN

> **Dari:** Senior Developer / Lead Architect  
> **Kepada:** Junior Developer / AI Agent (Gemini 3.8 Flash / Claude 3.5 Sonnet)  
> **Target Repositori:** MAGKOM (Magang Kominfo Kabupaten Sleman)  
> **Status:** READY FOR IMPLEMENTATION  
> **Tingkat Kesulitan:** Menengah (UI/UX Refinement, CSS Modularization, Skinny Controllers)  
> **Dokumentasi Terkait:** `AGENTS.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`

---

## 1. 🎯 RINGKASAN MISI (EXECUTIVE SUMMARY)

Setelah sukses melakukan modernisasi antarmuka pada halaman **Hak Akses dan Role Pengguna** (`superadmin/role`), tugasmu sekarang adalah mereplikasi **pola desain visual yang sama persis** pada 6 halaman inti SuperAdmin lainnya:
1. **Manajemen Staf Internal** (`superadmin/staf`)
2. **Jejak Audit & Kepatuhan** (`superadmin/audit-log`)
3. **Parameter Konfigurasi Sistem** (`superadmin/pengaturan`)
4. **Manajemen Slot Khusus Internal** (`superadmin/slot-khusus`)
5. **Master Data Jurusan** (`superadmin/jurusan`)
6. **Master Bidang Dinas Kominfo** (`superadmin/bidang`)

### 📸 Referensi Desain Baku (The Golden Standard)
Perhatikan tangkapan layar acuan yang telah disetujui:
![Referensi Desain Baku Hero Banner dan 4 Tile-Stat Grid](/media_1794135656774_68b62df2.jpg)

**Elemen Utama yang Wajib Direplikasi:**
1. **Topbar Bersih (Zero Duplicate Title):** Tidak ada lagi judul ganda di atas konten. `@section('page_title')` **wajib dihapus** dari setiap view agar header Bootstrap bawaan tidak dirender. Judul cukup muncul di Topbar / Breadcrumb.
2. **Hero Banner Khas Sleman MagKom:** Kartu dengan gradien biru Sleman (`#01347C` ke `#003566`), sudut membulat 16px, judul tebal putih, subjudul ringkas maksimal 1-2 poin data (misal `Cakupan: ... • Total: ...`), dan tombol aksi utama (*Call-to-Action*) di pojok kanan kartu.
3. **Grid 4 Kartu Statistik (`<x-gentelella.tile-stat>`):** 4 kartu metrik presisi industri dengan ikon SVG, badge tren, subteks deskriptif, dan visualisasi *sparkBars*.
4. **Kepatuhan Aturan Permanen (`AGENTS.md`):** Dilarang keras menulis tag `<style>` atau `<script>` di dalam file Blade! Semua styling diekstrak ke file CSS modular di `resources/css/`.

---

## 2. 🏛️ ATURAN ARSITEKTUR & PRINSIP KERJA (LEAD DIRECTIVES)

Sebelum menyentuh baris kode apa pun, kamu **wajib mematuhi 5 aturan baku** ini:

1. **JANGAN MERUSAK BUSINESS LOGIC & FORM CONTRACT:**  
   Tombol-tombol yang dipindahkan ke dalam Hero Banner (misal `+ Tambah Staf Baru`, `+ Tambah Jurusan Baru`, `Simpan Perubahan`) harus tetap mempertahankan ID pemicu modal JavaScript (`onclick` atau modal targeting) dan atribut `form="..."` yang sudah ada.
2. **HAPUS `@section('page_title')`:**  
   Di `resources/views/layouts/dashboard.blade.php`, layout mengecek `@hasSection('page_title')`. Jika ada, layout merender blok `.page-header-row` putih yang membuat judul terlihat duplikat dengan Hero Banner. Dengan menghapusnya, halaman akan langsung mengalir mulus ke Hero Banner.
3. **ZERO INLINE `<style>` DI BLADE:**  
   File `staf/index.blade.php`, `pengaturan/index.blade.php`, `slot-khusus/index.blade.php`, `jurusan/index.blade.php`, dan `bidang/index.blade.php` saat ini masih memiliki tag `<style>` di dalam `@push('styles')`. Kamu **wajib membersihkan tag `<style>` tersebut** dan memindahkannya ke file CSS terpusat `resources/css/pages/superadmin/superadmin-common.css`.
4. **CONTROLLER TETAP "SKINNY":**  
   Penghitungan angka-angka untuk kartu statistik dilakukan dengan query agregasi ringan (`count()`, `sum()`) langsung pada method `index()` controller terkait, lalu diteruskan ke view via data array.
5. **VERIFIKASI PHP 8.3 & TEST SUITE:**  
   Setelah selesai, jalankan `npm run build` dan PHPUnit test untuk memastikan tidak ada regresi fungsional.

---

## 3. 🎨 ARSITEKTUR CSS BERSAMA (SHARED STYLESHEET)

Buat file CSS bersama agar styling Hero Banner, tombol aksi, dan grid statistik dapat dipakai oleh ke-6 halaman tanpa duplikasi kode:

### File Baru: `resources/css/pages/superadmin/superadmin-common.css`
```css
/* =========================================================================
   MAGKOM GENTELELLA DESIGN SYSTEM: SUPERADMIN COMMON HERO & STATS
   Standarisasi Hero Banner Khas Sleman MagKom & Tile-Stat Grid
   ========================================================================= */

.sa-page-container {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    width: 100%;
}

/* 1. Hero Banner Khas Sleman MagKom */
.sa-hero-banner {
    background: radial-gradient(80% 120% at 100% 0%, rgba(63, 81, 181, 0.45), transparent 60%),
                linear-gradient(135deg, #01347C 0%, #003566 100%);
    border-radius: 16px;
    padding: 28px 32px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px -2px rgba(1, 52, 124, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.sa-hero-banner .banner-top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.25rem;
    flex-wrap: wrap;
    position: relative;
    z-index: 2;
}

.sa-hero-banner .banner-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 6px 0;
    letter-spacing: -0.01em;
    line-height: 1.3;
}

.sa-hero-banner .banner-subtitle {
    font-size: 0.9375rem;
    color: rgba(255, 255, 255, 0.92);
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    line-height: 1.5;
}

.sa-hero-banner .bullet-separator {
    opacity: 0.6;
}

.sa-hero-banner .banner-actions-group {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

/* Tombol Aksi Utama (Putih Kontras) */
.btn-banner-action {
    background: #ffffff;
    color: #01347C;
    border: none;
    border-radius: 10px;
    padding: 0.65rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    text-decoration: none;
}

.btn-banner-action:hover {
    background: #f0f4ff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    color: #002654;
}

/* Tombol Aksi Sekunder (Glassmorphism / Outline Putih) */
.btn-banner-action--secondary {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.35);
    border-radius: 10px;
    padding: 0.65rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    text-decoration: none;
    backdrop-filter: blur(4px);
}

.btn-banner-action--secondary:hover {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-1px);
}

/* 2. Grid 4 Kartu Statistik */
.tiles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 16px;
    width: 100%;
}

.tiles-grid .tile_stats_count {
    width: 100% !important;
    padding: 0 !important;
    float: none !important;
    margin: 0 !important;
}

.tiles-grid .tile_stats_inner.stat {
    border-radius: 12px !important;
    padding: 1.15rem 1.25rem !important;
    border: 1.5px solid #CBD5E1 !important;
    background: #ffffff !important;
    box-shadow: 0 2px 8px rgba(1, 52, 124, 0.04) !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.tiles-grid .tile_stats_inner.stat:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(1, 52, 124, 0.08) !important;
}

/* Dark Mode Overrides */
[data-theme="dark"] .sa-hero-banner {
    background: radial-gradient(80% 120% at 100% 0%, rgba(63, 81, 181, 0.3), transparent 60%),
                linear-gradient(135deg, #002244 0%, #001730 100%);
    border-color: rgba(63, 81, 181, 0.4);
}

[data-theme="dark"] .tiles-grid .tile_stats_inner.stat {
    background: #003566 !important;
    border-color: #3F51B5 !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5) !important;
}
```

> **Catatan Registrasi:** Daftarkan `resources/css/pages/superadmin/superadmin-common.css` ke dalam `vite.config.js` dan import di `resources/css/app.css` agar langsung tersedia di seluruh layout dashboard.

---

## 4. 📋 SPESIFIKASI RINCI PER HALAMAN (6 PAGES BREAKDOWN)

Berikut panduan detail per file untuk controller, data flow, dan view markup:

---

### 📄 HALAMAN 1: MANAJEMEN STAF INTERNAL (`superadmin/staf`)

#### A. Backend Controller: `app/Http/Controllers/SuperAdmin/StafController.php`
Di method `index(Request $request)`:
1. Hitung 4 metrik data:
   ```php
   $totalStaf = User::whereIn('role', [RoleEnum::Admin->value, RoleEnum::Pembimbing->value])->count();
   $totalAdmin = User::where('role', RoleEnum::Admin->value)->count();
   $totalPembimbing = User::where('role', RoleEnum::Pembimbing->value)->count();
   // $stafPendingPasswordCount sudah ada di baris 66-68!
   ```
2. Kirim ke view:
   ```php
   return view('superadmin.staf.index', [
       'stafList' => $stafList,
       'bidangList' => Bidang::aktif()->get(),
       'maksPeserta' => $maksPeserta,
       'stafPendingPasswordCount' => $stafPendingPasswordCount,
       'totalStaf' => $totalStaf,
       'totalAdmin' => $totalAdmin,
       'totalPembimbing' => $totalPembimbing,
   ]);
   ```

#### B. Blade View: `resources/views/superadmin/staf/index.blade.php`
1. **Hapus:** `@section('page_title', 'Manajemen Staf Internal')` dan blok `@section('page_actions')`.
2. **Ganti Topbar Title:** `@section('topbar_title', 'Manajemen Staf Internal')`.
3. **Hapus blok `<style>` di `@push('styles')`:** Ganti dengan `@vite(['resources/css/pages/superadmin/superadmin-common.css'])`.
4. **Pasang Hero Banner di awal `@section('content')`:**
   ```blade
   <div class="sa-hero-banner">
       <div class="banner-top-row">
           <div>
               <h1 class="banner-title">Manajemen Staf Internal</h1>
               <div class="banner-subtitle">
                   <span>Cakupan: <strong>Admin Verifikator & Pembimbing</strong></span>
                   <span class="bullet-separator">&bull;</span>
                   <span>Total Staf: <strong>{{ $totalStaf }} Akun Terdaftar</strong></span>
               </div>
           </div>
           <div>
               <button type="button" class="btn-banner-action" onclick="document.getElementById('modalTambahStaf').style.display='flex'">
                   <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                   <span>Tambah Staf Baru</span>
               </button>
           </div>
       </div>
   </div>
   ```
5. **Pasang 4 Kartu Statistik:**
   - **Card 1 (TOTAL STAF):** `value="{{ $totalStaf }}"`, `color="blue"`, `trendType="info"`, `trendText="Internal"`, `subText="Staf Aktif Dinas Kominfo"`, Icon `users`.
   - **Card 2 (ADMIN VERIFIKATOR):** `value="{{ $totalAdmin }}"`, `color="teal"`, `trendType="success"`, `trendText="Dinas"`, `subText="Verifikasi & Seleksi Berkas"`, Icon `shield-check`.
   - **Card 3 (PEMBIMBING):** `value="{{ $totalPembimbing }}"`, `color="amber"`, `trendType="warning"`, `trendText="Lapangan"`, `subText="Pendamping Mentoring & Unit Kerja"`, Icon `briefcase`.
   - **Card 4 (MENUNGGU SANDI):** `value="{{ $stafPendingPasswordCount }}"`, `color="purple"`, `trendType="danger"`, `trendText="BR-15"`, `subText="Wajib Ganti Password Awal"`, Icon `key`.

---

### 📄 HALAMAN 2: JEJAK AUDIT & KEPATUHAN (`superadmin/audit-log`)

#### A. Backend Controller: `app/Http/Controllers/SuperAdmin/AuditLogController.php`
Metrik sudah dihitung secara lengkap di method `index()`:
- `$total24h` (Total log 24 jam)
- `$mutasiData` (CRUD changes)
- `$sesiAuth` (Autentikasi & Sesi)
- `$peringatanKritis` (Peringatan Kritis)
- `$growthLabel` / `$growth24h`
- `$mutasiPercentage`

#### B. Blade View: `resources/views/superadmin/audit-log/partials/_header.blade.php` & `_kpi_strip.blade.php`
1. **Restrukturisasi `_header.blade.php`:**
   Ganti kartu header putih lama dengan Hero Banner Sleman MagKom:
   ```blade
   <div class="sa-hero-banner" style="margin-bottom: 1.5rem;">
       <div class="banner-top-row">
           <div>
               <h1 class="banner-title">Jejak Audit & Kepatuhan</h1>
               <div class="banner-subtitle">
                   <span>Cakupan: <strong>15 Kanal Audit Stream</strong></span>
                   <span class="bullet-separator">&bull;</span>
                   <span>Integritas: <strong>WORM SHA-256 Valid (Retensi 365 Hari)</strong></span>
               </div>
           </div>
           <div class="banner-actions-group">
               <a href="{{ route('superadmin.audit-log.export', ['channel' => $activeChannel] + request()->except('page')) }}" class="btn-banner-action">
                   <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"></path></svg>
                   <span>Unduh Audit CSV</span>
               </a>
           </div>
       </div>
   </div>
   ```
2. **Restrukturisasi `_kpi_strip.blade.php`:**
   Ganti kartu custom `.audit-stat-card` dengan 4 `<x-gentelella.tile-stat>` standar di dalam grid `.tiles-grid`:
   - **Card 1 (TOTAL AKTIVITAS 24H):** `value="{{ number_format($total24h) }}"`, `color="blue"`, `trendType="info"`, `trendText="{{ $growthPositive ? '+' : '-' }}{{ $growthLabel }}%"`, `subText="Aktivitas Sistem 24 Jam Terakhir"`.
   - **Card 2 (MUTASI DATA):** `value="{{ number_format($mutasiData) }}"`, `color="teal"`, `trendType="success"`, `trendText="{{ $mutasiPercentage }}%"`, `subText="Perubahan Nilai & Data State"`.
   - **Card 3 (SESI AUTENTIKASI):** `value="{{ number_format($sesiAuth) }}"`, `color="amber"`, `trendType="warning"`, `trendText="Auth"`, `subText="Login, Logout & Lockout Akun"`.
   - **Card 4 (PERINGATAN KRITIS):** `value="{{ number_format($peringatanKritis) }}"`, `color="red"`, `trendType="danger"`, `trendText="Keamanan"`, `subText="Percobaan Akses 403 / Anomali"`.

---

### 📄 HALAMAN 3: PARAMETER KONFIGURASI SISTEM (`superadmin/pengaturan`)

#### A. Backend Controller: `app/Http/Controllers/SuperAdmin/PengaturanController.php`
Di method `index()`:
1. Hitung parameter utama:
   ```php
   $totalParameter = Pengaturan::count();
   $kuotaBulanan = Pengaturan::int('kuota_umum_maks_per_bulan', 20);
   $rasioMentee = Pengaturan::int('maks_peserta_aktif_per_pembimbing', 10);
   $masaTenggang = Pengaturan::int('masa_tenggang_bulan', 6);
   ```
2. Kirimkan ke view bersama `$pengaturan` dan `$predikatBatas`.

#### B. Blade View: `resources/views/superadmin/pengaturan/index.blade.php`
1. **Hapus:** `@section('page_title')` dan blok `@section('page_actions')`.
2. **Ganti Topbar Title:** `@section('topbar_title', 'Parameter Konfigurasi Sistem')`.
3. **Hapus blok `<style>` inline:** Ekstrak ke stylesheet.
4. **Pasang Hero Banner dengan Dual Action Button:**
   ```blade
   <div class="sa-hero-banner">
       <div class="banner-top-row">
           <div>
               <h1 class="banner-title">Parameter Konfigurasi Sistem</h1>
               <div class="banner-subtitle">
                   <span>Tata Kelola: <strong>Aturan Bisnis & Ambang Batas</strong></span>
                   <span class="bullet-separator">&bull;</span>
                   <span>Pembaruan Terakhir: <strong>{{ \Carbon\Carbon::now()->format('d M Y') }}</strong></span>
               </div>
           </div>
           <div class="banner-actions-group">
               <a href="{{ route('superadmin.pengaturan.export-json') }}" class="btn-banner-action--secondary">
                   <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"></path></svg>
                   <span>Ekspor JSON</span>
               </a>
               <button type="submit" form="formPengaturanSistem" class="btn-banner-action">
                   <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                   <span>Simpan Perubahan</span>
               </button>
           </div>
       </div>
   </div>
   ```
5. **Pasang 4 Kartu Statistik:**
   - **Card 1 (TOTAL PARAMETER):** `value="{{ $totalParameter }}"`, `color="blue"`, `trendType="info"`, `trendText="Sistem"`, `subText="Konfigurasi Aktif Database"`.
   - **Card 2 (KUOTA BULANAN):** `value="{{ $kuotaBulanan }} Kuota"`, `color="teal"`, `trendType="success"`, `trendText="Default"`, `subText="Batas Maksimal Kuota Umum / Bulan"`.
   - **Card 3 (RASIO MENTEE):** `value="{{ $rasioMentee }} : 1"`, `color="amber"`, `trendType="warning"`, `trendText="Kapasitas"`, `subText="Maks Mentee per Pembimbing"`.
   - **Card 4 (MASA TENGGANG):** `value="{{ $masaTenggang }} Bulan"`, `color="purple"`, `trendType="info"`, `trendText="Cooldown"`, `subText="Jeda Daftar Ulang Peserta Ditolak"`.

---

### 📄 HALAMAN 4: MANAJEMEN SLOT KHUSUS INTERNAL (`superadmin/slot-khusus`)

#### A. Backend Controller: `app/Http/Controllers/SuperAdmin/SlotKhususController.php`
Di method `index()`:
1. Hitung alokasi slot khusus:
   ```php
   $totalAlokasiSlot = (int) SlotKhusus::sum('jumlah_slot');
   $totalSlotTerisi = (int) SlotKhusus::sum('terisi');
   $sisaSlotKhusus = max(0, $totalAlokasiSlot - $totalSlotTerisi);
   $defaultSlot = Pengaturan::int('slot_khusus_default', 3);
   ```
2. Kirimkan ke view:
   ```php
   return view('superadmin.slot-khusus.index', [
       'slotList' => $slotList,
       'defaultSlot' => $defaultSlot,
       'totalAlokasiSlot' => $totalAlokasiSlot,
       'totalSlotTerisi' => $totalSlotTerisi,
       'sisaSlotKhusus' => $sisaSlotKhusus,
   ]);
   ```

#### B. Blade View: `resources/views/superadmin/slot-khusus/index.blade.php`
1. **Hapus:** `@section('page_title')` dan `@section('page_actions')`.
2. **Ganti Topbar Title:** `@section('topbar_title', 'Manajemen Slot Khusus Internal')`.
3. **Hapus blok `<style>` inline:** Ekstrak ke stylesheet.
4. **Pasang Hero Banner dengan Dual Action Button:**
   ```blade
   <div class="sa-hero-banner">
       <div class="banner-top-row">
           <div>
               <h1 class="banner-title">Manajemen Slot Khusus Internal</h1>
               <div class="banner-subtitle">
                   <span>Cakupan: <strong>Diskresi & Kemitraan Khusus</strong></span>
                   <span class="bullet-separator">&bull;</span>
                   <span>Alokasi: <strong>Terpisah dari Kuota Reguler</strong></span>
               </div>
           </div>
           <div class="banner-actions-group">
               <a href="{{ route('superadmin.peserta-khusus.create') }}" class="btn-banner-action--secondary">
                   <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M8.5 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM20 8v6M23 11h-6"></path></svg>
                   <span>Tambah Peserta Khusus</span>
               </a>
               <button type="button" class="btn-banner-action" onclick="document.getElementById('modalTambahSlot').style.display='flex'">
                   <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                   <span>Tambah Slot Khusus</span>
               </button>
           </div>
       </div>
   </div>
   ```
5. **Pasang 4 Kartu Statistik:**
   - **Card 1 (TOTAL ALOKASI):** `value="{{ $totalAlokasiSlot }}"`, `color="blue"`, `trendType="info"`, `trendText="Alokasi"`, `subText="Total Kuota Khusus Disediakan"`.
   - **Card 2 (SLOT TERPAKAI):** `value="{{ $totalSlotTerisi }}"`, `color="teal"`, `trendType="success"`, `trendText="Terisi"`, `subText="Peserta Khusus Terverifikasi"`.
   - **Card 3 (SISA KUOTA KHUSUS):** `value="{{ $sisaSlotKhusus }}"`, `color="amber"`, `trendType="warning"`, `trendText="Tersedia"`, `subText="Slot Kosong Siap Dialokasikan"`.
   - **Card 4 (DEFAULT BULANAN):** `value="{{ $defaultSlot }} Slot"`, `color="purple"`, `trendType="info"`, `trendText="Baseline"`, `subText="Alokasi Default Tiap Bulan Baru"`.

---

### 📄 HALAMAN 5: MASTER DATA JURUSAN (`superadmin/jurusan`)

#### A. Backend Controller: `app/Http/Controllers/SuperAdmin/JurusanController.php`
Di method `index()`:
1. Hitung agregasi data jurusan:
   ```php
   $totalJurusan = Jurusan::count();
   $jurusanMahasiswa = Jurusan::where('kategori', 'mahasiswa')->count();
   $jurusanSmk = Jurusan::where('kategori', 'smk')->count();
   $jurusanAktif = Jurusan::where('aktif', true)->count();
   ```
2. Kirimkan ke view:
   ```php
   return view('superadmin.jurusan.index', [
       'jurusanList' => $jurusanList,
       'totalJurusan' => $totalJurusan,
       'jurusanMahasiswa' => $jurusanMahasiswa,
       'jurusanSmk' => $jurusanSmk,
       'jurusanAktif' => $jurusanAktif,
   ]);
   ```

#### B. Blade View: `resources/views/superadmin/jurusan/index.blade.php`
1. **Hapus:** `@section('page_title')` dan `@section('page_actions')`.
2. **Ganti Topbar Title:** `@section('topbar_title', 'Master Data Jurusan')`.
3. **Hapus blok `<style>` inline:** Ekstrak ke stylesheet.
4. **Pasang Hero Banner:**
   ```blade
   <div class="sa-hero-banner">
       <div class="banner-top-row">
           <div>
               <h1 class="banner-title">Master Data Jurusan</h1>
               <div class="banner-subtitle">
                   <span>Cakupan: <strong>Klasifikasi SMK & Mahasiswa</strong></span>
                   <span class="bullet-separator">&bull;</span>
                   <span>Total Jurusan: <strong>{{ $totalJurusan }} Program Studi</strong></span>
               </div>
           </div>
           <div>
               <button type="button" class="btn-banner-action" onclick="document.getElementById('modalTambahJurusan').style.display='flex'">
                   <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                   <span>Tambah Jurusan Baru</span>
               </button>
           </div>
       </div>
   </div>
   ```
5. **Pasang 4 Kartu Statistik:**
   - **Card 1 (TOTAL JURUSAN):** `value="{{ $totalJurusan }}"`, `color="blue"`, `trendType="info"`, `trendText="Prodi"`, `subText="Total Jurusan Terdaftar di Sistem"`.
   - **Card 2 (MAHASISWA / PT):** `value="{{ $jurusanMahasiswa }}"`, `color="teal"`, `trendType="success"`, `trendText="PT"`, `subText="Program Studi Jenjang D3/D4/S1"`.
   - **Card 3 (SMK / VOKASI):** `value="{{ $jurusanSmk }}"`, `color="amber"`, `trendType="warning"`, `trendText="Vokasi"`, `subText="Kompetensi Keahlian SMK/MAK"`.
   - **Card 4 (STATUS AKTIF):** `value="{{ $jurusanAktif }}"`, `color="emerald"`, `trendType="success"`, `trendText="Aktif"`, `subText="Dapat Dipilih Calon Peserta Magang"`.

---

### 📄 HALAMAN 6: MASTER BIDANG DINAS KOMINFO (`superadmin/bidang`)

#### A. Backend Controller: `app/Http/Controllers/SuperAdmin/BidangController.php`
Di method `index()`:
1. Hitung agregasi struktur bidang:
   ```php
   $totalBidang = Bidang::count();
   $bidangAktif = Bidang::where('aktif', true)->count();
   $bidangNonaktif = Bidang::where('aktif', false)->count();
   $totalPembimbing = User::where('role', RoleEnum::Pembimbing->value)->whereNotNull('bidang_id')->count();
   ```
2. Kirimkan ke view:
   ```php
   return view('superadmin.bidang.index', [
       'bidangList' => $bidangList,
       'totalBidang' => $totalBidang,
       'bidangAktif' => $bidangAktif,
       'bidangNonaktif' => $bidangNonaktif,
       'totalPembimbing' => $totalPembimbing,
   ]);
   ```

#### B. Blade View: `resources/views/superadmin/bidang/index.blade.php`
1. **Hapus:** `@section('page_title')` dan `@section('page_actions')`.
2. **Ganti Topbar Title:** `@section('topbar_title', 'Master Bidang Dinas Kominfo')`.
3. **Hapus blok `<style>` inline:** Ekstrak ke stylesheet.
4. **Pasang Hero Banner:**
   ```blade
   <div class="sa-hero-banner">
       <div class="banner-top-row">
           <div>
               <h1 class="banner-title">Master Bidang Dinas Kominfo</h1>
               <div class="banner-subtitle">
                   <span>Cakupan: <strong>Struktur Organisasi Unit Kerja</strong></span>
                   <span class="bullet-separator">&bull;</span>
                   <span>Total Bidang: <strong>{{ $totalBidang }} Bidang Dinas</strong></span>
               </div>
           </div>
           <div>
               <button type="button" class="btn-banner-action" onclick="document.getElementById('modalTambahBidang').style.display='flex'">
                   <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                   <span>Tambah Bidang Baru</span>
               </button>
           </div>
       </div>
   </div>
   ```
5. **Pasang 4 Kartu Statistik:**
   - **Card 1 (TOTAL BIDANG):** `value="{{ $totalBidang }}"`, `color="blue"`, `trendType="info"`, `trendText="Dinas"`, `subText="Unit Kerja Bidang Dinas Kominfo"`.
   - **Card 2 (BIDANG AKTIF):** `value="{{ $bidangAktif }}"`, `color="teal"`, `trendType="success"`, `trendText="Aktif"`, `subText="Menerima Penempatan Magang"`.
   - **Card 3 (TOTAL PEMBIMBING):** `value="{{ $totalPembimbing }}"`, `color="amber"`, `trendType="warning"`, `trendText="Mentor"`, `subText="Pembimbing Ditugaskan di Bidang"`.
   - **Card 4 (BIDANG ARSIP):** `value="{{ $bidangNonaktif }}"`, `color="purple"`, `trendType="info"`, `trendText="Nonaktif"`, `subText="Bidang Tidak Menerima Peserta"`.

---

## 5. 🛠️ CHECKLIST EKSEKUSI TAHAP DEMI TAHAP (STEP-BY-STEP RUNBOOK)

Sebagai Junior Developer / AI Pelaksana, ikuti urutan kerja ini:

### Fase 1: Styling Modular
- [ ] Buat file `resources/css/pages/superadmin/superadmin-common.css`.
- [ ] Daftarkan file ini pada `vite.config.js`.
- [ ] Import di `resources/css/app.css` atau sertakan via `@vite(['resources/css/pages/superadmin/superadmin-common.css'])`.
- [ ] Uji kompilasi dengan `npm run build`.

### Fase 2: Backend Controllers
- [ ] Modifikasi `StafController.php` (tambahkan perhitungan `totalStaf`, `totalAdmin`, `totalPembimbing`).
- [ ] Modifikasi `PengaturanController.php` (tambahkan perhitungan parameter kunci).
- [ ] Modifikasi `SlotKhususController.php` (tambahkan akumulasi slot khusus).
- [ ] Modifikasi `JurusanController.php` (tambahkan perhitungan kategori dan status).
- [ ] Modifikasi `BidangController.php` (tambahkan agregasi unit kerja dan pembimbing).

### Fase 3: Blade Views Refactoring
- [ ] Refactor `resources/views/superadmin/staf/index.blade.php`.
- [ ] Refactor `resources/views/superadmin/audit-log/partials/_header.blade.php` & `_kpi_strip.blade.php`.
- [ ] Refactor `resources/views/superadmin/pengaturan/index.blade.php`.
- [ ] Refactor `resources/views/superadmin/slot-khusus/index.blade.php`.
- [ ] Refactor `resources/views/superadmin/jurusan/index.blade.php`.
- [ ] Refactor `resources/views/superadmin/bidang/index.blade.php`.
- [ ] Pastikan seluruh tag `<style>` inline yang sebelumnya ada di Blade telah terhapus 100%.

### Fase 4: Quality Gate & Verifikasi
- [ ] Jalankan audit formatting: `vendor/bin/pint --test`.
- [ ] Jalankan analisa statis: `vendor/bin/phpstan analyse --memory-limit=1G`.
- [ ] Jalankan unit & feature testing: `php artisan test --filter=SuperAdmin`.
- [ ] Verifikasi tampilan di browser (`http://magkom.test:8080/superadmin/...`) baik dalam **Mode Terang** maupun **Mode Gelap**.

---

## 6. 🏆 ACCEPTANCE CRITERIA (KRITERIA PENERIMAAN)

Tugas ini dinyatakan **SELESAI (DONE)** jika dan hanya jika:
1. Seluruh 6 halaman memiliki **Hero Banner Khas Sleman MagKom** dengan tombol aksi di pojok kanan.
2. Seluruh 6 halaman memiliki **4 Kartu Statistik `<x-gentelella.tile-stat>`** dengan data dinamis yang akurat.
3. Tidak ada lagi judul halaman duplikat di atas konten (topbar langsung terhubung ke Hero Banner).
4. Tidak ada tag `<style>` inline di dalam file Blade yang dimodifikasi.
5. Mode Gelap (*Dark Mode*) berfungsi sempurna dan kontras warna memenuhi standar WCAG 2.1 AA.
6. Seluruh test suite `php artisan test` tetap berwarna hijau (pass) 100%.

