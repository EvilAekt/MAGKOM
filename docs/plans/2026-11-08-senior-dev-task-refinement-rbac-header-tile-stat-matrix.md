# DOKUMEN TUGAS SENIOR DEVELOPER & PRODUCT REQUIREMENTS DOCUMENT (PRD) — REVISI 2
## Refinement: Header Breadcrumb, Hero Card Ringkas, Tile-Stat Gentelella, dan Matriks Murni 4 Role
### Modul SuperAdmin: Hak Akses dan Role Pengguna — Portal MagKom Sleman

---

**Kepada:** Junior Developer / AI Implementation Agent (Gemini 3.8 Flash, Cursor, Copilot)  
**Dari:** Senior Lead Software Engineer & Frontend Architect MAGKOM  
**Tanggal:** 8 November 2026  
**Status:** Ready for Review & Implementation  
**Prioritas:** P1 — High Usability & Visual Perfection  
**Target Utama:**
1. **Header & Breadcrumbs Simplification**:
   - Breadcrumb diganti dari `Beranda / SuperAdmin / Role & Hak Akses` menjadi `Beranda / SuperAdmin / Hak Akses dan Role Pengguna`.
   - Judul teks besar `@section('page_title')` di luar card **dihapus seutuhnya**, sehingga tampilan dari topbar navigasi langsung masuk (*direct seamless transition*) ke **Hero Banner Card**.
2. **Hero Banner Card Subtitle Streamlining**:
   - Memangkas teks subtitle yang sebelumnya terlalu panjang (4 item) menjadi hanya **2 item ringkas dan padat**:
     `Cakupan: 4 Role Sistem • Total Pengguna: {{ $totalPengguna }} Akun Terdaftar`.
3. **Standarisasi 4 Card Role Menjadi `<x-gentelella.tile-stat>`**:
   - Mengganti kartu stat manual dengan komponen standar MagKom Gentelella `<x-gentelella.tile-stat>` (persis seperti tampilan di halaman *Logbook Peserta* / Dashboard Pembimbing).
   - Menampilkan icon rounded/badge, angka counter tebal, trend status badge, deskripsi wewenang role, dan visualisasi grafik `sparkBars`.
4. **Pembersihan Kolom Aksi pada Tabel Matriks**:
   - Menghapus kolom header `AKSI` dan kolom data aksi dari tabel matriks wewenang.
   - Tabel murni berfokus pada 5 kolom: `MODUL & CAKUPAN FITUR`, `SUPERADMIN`, `ADMINISTRASI`, `PEMBIMBING`, dan `PESERTA`.

---

## 1. DOKUMENTASI VISUAL & MASALAH YANG DISELESAIKAN

### 1.1 Masalah Berdasarkan Umpan Balik Pengguna
1. **Redundansi Judul Header**:
   - Breadcrumb mencantumkan `Role & Hak Akses`, lalu tepat di bawahnya ada teks judul besar `Hak Akses dan Role Pengguna`, kemudian di dalam kartu banner terdapat judul yang sama lagi. Hal ini menciptakan pemborosan ruang vertikal (*vertical space waste*).
   - Solusi: Satukan breadcrumb menjadi `Hak Akses dan Role Pengguna` dan hapus teks judul di luar kartu.
2. **Kepadatan Teks di Hero Banner**:
   - Subtitle dengan 4 baris/bullet terlalu ramai dan sulit dibaca sekilas (*information overload*).
   - Solusi: Sederhanakan menjadi 2 informasi paling penting: cakupan role dan total pengguna.
3. **Inkonsistensi Komponen Kartu Statistik**:
   - Desain 4 kartu role saat ini menggunakan box CSS custom dengan border-top warna-warni yang berbeda dengan bahasa desain tile statistik Gentelella di halaman lain.
   - Solusi: Gunakan komponen `<x-gentelella.tile-stat>` dengan `sparkBars` dan layout `.tiles-grid`.
4. **Kolom Aksi Tidak Relevan**:
   - Pengguna menginginkan tabel matriks hanya menampilkan pemetaan 4 role sistem secara bersih tanpa tombol aksi hapus kolom di sisi kanan.
   - Solusi: Hapus kolom `AKSI`, pusatkan lebar tabel pada 4 peran.

---

## 2. SPESIFIKASI TEKNIS & PERUBAHAN KODE

### 2.1 Perubahan Breadcrumbs & Header di Blade
File: `resources/views/superadmin/role/index.blade.php`

**Sebelum:**
```html
@section('title', 'Hak Akses dan Role - SuperAdmin MagKom')
@section('page_title', 'Hak Akses dan Role Pengguna')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Role & Hak Akses</span>
@endsection
```

**Sesudah (Wajib):**
```html
@extends('layouts.dashboard')

@section('title', 'Hak Akses dan Role Pengguna - SuperAdmin MagKom')
@section('topbar_title', 'Hak Akses dan Role Pengguna')
{{-- CATATAN SENIOR DEV: JANGAN gunakan @section('page_title') agar judul luar tidak dirender! --}}

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Hak Akses dan Role Pengguna</span>
@endsection
```

---

### 2.2 Perubahan Subtitle Hero Banner Card
File: `resources/views/superadmin/role/index.blade.php`

**Sebelum:**
```html
<div class="banner-subtitle">
    <span>Tata Kelola: <strong>Wewenang & Keamanan Sistem (RBAC)</strong></span>
    <span class="bullet-separator">&bull;</span>
    <span>Cakupan: <strong>4 Role Utama</strong></span>
    <span class="bullet-separator">&bull;</span>
    <span>Total Pengguna: <strong>{{ $totalPengguna }} Akun Terdaftar</strong></span>
    <span class="bullet-separator">&bull;</span>
    <span>Kebijakan: <strong>Berlaku Global per Role</strong></span>
</div>
```

**Sesudah (Ringkas 2 Item):**
```html
<div class="banner-subtitle">
    <span>Cakupan: <strong>4 Role Sistem</strong></span>
    <span class="bullet-separator">&bull;</span>
    <span>Total Pengguna: <strong>{{ $totalPengguna }} Akun Terdaftar</strong></span>
</div>
```

---

### 2.3 Transformasi 4 Kartu Role Menggunakan `<x-gentelella.tile-stat>`
File: `resources/views/superadmin/role/index.blade.php`

Gantikan kontainer `.sa-role-cards-grid` dengan struktur `.tiles-grid` yang memanggil komponen `<x-gentelella.tile-stat>`:

```html
<div class="tiles-grid">
    <!-- 1. SuperAdmin -->
    <x-gentelella.tile-stat
        title="SUPERADMIN"
        value="{{ $roleCounts['superadmin'] ?? 0 }}"
        color="blue"
        trendType="info"
        trendDirection="up"
        trendText="Eksekutif"
        subText="Tata Kelola Sistem & Eksekutif"
        :sparkBars="[30, 45, 60, 75, 85, 95]">
        <x-slot:icon>
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </x-slot:icon>
    </x-gentelella.tile-stat>

    <!-- 2. Administrasi Dinas -->
    <x-gentelella.tile-stat
        title="ADMINISTRASI"
        value="{{ $roleCounts['admin'] ?? 0 }}"
        color="teal"
        trendType="success"
        trendDirection="up"
        trendText="Dinas"
        subText="Operasional Seleksi & Kuota"
        :sparkBars="[25, 40, 55, 70, 80, 90]">
        <x-slot:icon>
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </x-slot:icon>
    </x-gentelella.tile-stat>

    <!-- 3. Pembimbing Lapangan -->
    <x-gentelella.tile-stat
        title="PEMBIMBING"
        value="{{ $roleCounts['pembimbing'] ?? 0 }}"
        color="yellow"
        trendType="warning"
        trendDirection="up"
        trendText="Lapangan"
        subText="Mentoring & Evaluasi Kinerja"
        :sparkBars="[40, 50, 65, 75, 85, 92]">
        <x-slot:icon>
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
            </svg>
        </x-slot:icon>
    </x-gentelella.tile-stat>

    <!-- 4. Peserta Magang -->
    <x-gentelella.tile-stat
        title="PESERTA MAGANG"
        value="{{ $roleCounts['peserta'] ?? 0 }}"
        color="teal"
        trendType="success"
        trendDirection="up"
        trendText="Aktif"
        subText="Pelaksanaan Program Magang"
        :sparkBars="[35, 50, 60, 75, 88, 100]">
        <x-slot:icon>
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </x-slot:icon>
    </x-gentelella.tile-stat>
</div>
```

---

### 2.4 Penghapusan Kolom `AKSI` pada Tabel Matriks
File: `resources/views/superadmin/role/index.blade.php`

**1. Pada Header Tabel (`<thead>`):**
Hapus `<th>` Aksi sehingga hanya tersisa 5 kolom:
```html
<thead>
    <tr class="headings">
        <th style="min-width: 320px;">Modul & Cakupan Fitur</th>
        <th style="text-align: center; width: 140px;">SuperAdmin</th>
        <th style="text-align: center; width: 140px;">Administrasi</th>
        <th style="text-align: center; width: 140px;">Pembimbing</th>
        <th style="text-align: center; width: 140px;">Peserta</th>
    </tr>
</thead>
```

**2. Pada Baris Tabel (`<tbody>`):**
Hapus blok `<td>` aksi dan tombol delete.
Colspan baris kosong (`@empty`) disesuaikan menjadi `colspan="5"`.

---

### 2.5 Penyesuaian CSS Scoped (`resources/css/pages/superadmin/role-index.css`)
Tambahkan aturan layout `.tiles-grid` yang mendukung `<x-gentelella.tile-stat>` agar tampil presisi dan rapi:

```css
/* Layout Tile Stats Grid Gentelella */
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
    background: #ffffff !important;
    border: 1px solid #E2E8F0 !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05) !important;
    transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.22s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.22s ease !important;
}

.tiles-grid .tile_stats_inner.stat:hover {
    transform: translateY(-2.5px) !important;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08) !important;
    border-color: #CBD5E1 !important;
}

.tiles-grid .stat-spark {
    display: flex !important;
    align-items: flex-end !important;
    gap: 3.5px !important;
    height: 36px !important;
    margin-left: auto !important;
    flex-shrink: 0 !important;
    padding-left: 8px !important;
}

.tiles-grid .stat-spark i {
    width: 4px !important;
    border-radius: 2px !important;
    transition: height 0.3s ease !important;
}

/* Dark mode untuk Tile Stat */
[data-theme="dark"] .tiles-grid .tile_stats_inner.stat {
    background-color: #003566 !important;
    border-color: rgba(63, 81, 181, 0.45) !important;
}
```

---

## 3. CHECKLIST EKSEKUSI TUGAS (TASK BREAKDOWN)

- [ ] **Task 1: Update Blade File (`superadmin/role/index.blade.php`)**
  - Ganti breadcrumb link teks ke `Hak Akses dan Role Pengguna`.
  - Hapus `@section('page_title')` untuk menghilangkan teks header ganda di luar kartu.
  - Sederhanakan teks subtitle Hero Banner Card menjadi 2 item ringkas (`Cakupan: 4 Role Sistem` dan `Total Pengguna: X Akun Terdaftar`).
  - Ganti 4 kartu role lama dengan komponen `<x-gentelella.tile-stat>`.
  - Hapus kolom `AKSI` pada `<thead>`, `<tbody>`, serta form hapus tersembunyi.
- [ ] **Task 2: Update CSS (`role-index.css`)**
  - Sisipkan aturan CSS `.tiles-grid` dan styling tile-stat Gentelella.
- [ ] **Task 3: Update Feature Test (`RolePermissionTest.php`)**
  - Sesuaikan assertion text: hapus pengecekan kata kunci aksi yang sudah dihapus, periksa subtitle ringkas dan komponen tile stat.
- [ ] **Task 4: Quality Gate Verification**
  - Jalankan `npm run build` untuk mengompilasi CSS.
  - Jalankan `php artisan test tests/Feature/SuperAdmin/RolePermissionTest.php`.
  - Jalankan `pint --test` dan `phpstan analyse`.

---

**Approval:**  
*Lead Architect & Security Specialist MAGKOM*  
*Status: Ready for Junior Dev / AI Execution.*

