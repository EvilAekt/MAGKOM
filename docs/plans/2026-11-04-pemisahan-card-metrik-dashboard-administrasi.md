# Rencana Kerja: Pemisahan Kartu Metrik dari Banner Header pada Dashboard Operasional Administrasi (Penyelarasan Desain SuperAdmin)

**Tanggal Dokumen**: 2026-11-04  
**Modul**: Administrasi MagKom — Dashboard Operasional Administrasi (`admin/dashboard`)  
**Status**: ✅ Selesai Diimplementasikan & Diverifikasi  

---

## 1. Ringkasan Kebutuhan & Analisis Visual

### 1.1 Perbandingan Visual (Screenshot 1 vs Screenshot 2)
1. **Kondisi Eksisting (Screenshot 1: Dashboard Operasional Administrasi)**:
   - Ke-4 kartu metrik operasional (`Verifikasi Tertunda`, `Kapasitas Kuota Terisi`, `Antrean Waiting List`, `Peserta Magang Aktif`) berada **di dalam container banner biru** (`.cv-hero-banner`).
   - Kartu metrik menempel di bagian bawah banner dengan dock semi-transparan gelap (`rgba(4, 18, 56, 0.45)`) dan border atas tipis (`border-top: 1px solid rgba(255, 255, 255, 0.15)`), membentuk tampilan bertipe *integrated banner dock*.
2. **Target Desain (Screenshot 2: Dashboard SuperAdmin)**:
   - Banner header (`Dashboard SuperAdmin`) berdiri sendiri secara mandiri (*standalone banner box*) dengan keempat sudut melengkung sempurna (*rounded corners* `border-radius: 14px–16px`) dan padding bawah yang proporsional.
   - Ke-4 kartu metrik utama **terpisah di bawah banner** sebagai baris grid mandiri (`.sa-metrics-row`), melayang bersih di atas canvas latar belakang halaman berwarna abu-abu/putih netral dengan drop shadow lembut.
   - Di bawah kartu metrik, baris horizontal *statusline ribbon* dan area konten cockpit utama tertata rapi.

### 1.2 Tujuan Pekerjaan
1. Memisahkan 4 kartu metrik dari dalam container banner biru menjadi **baris tersendiri di bawah banner** (`<section class="adm-metrics-row">`), selaras 100% dengan pola arsitektur tampilan SuperAdmin (Screenshot 2).
2. Memperbarui styling banner `.cv-hero-banner` agar memiliki padding bawah proporsional dan rounded corners utuh tanpa dock menyatu.
3. Menata container grid kartu metrik `.adm-metrics-dock-grid` agar tampil bersih di atas canvas halaman (tanpa negative margin `-2rem`, tanpa background gelap dock, dan tanpa blur).
4. Menempatkan pita status *statusline ribbon* (`.cv-statusline`) di bawah baris kartu metrik sebelum konten utama, persis seperti pola SuperAdmin.
5. Memastikan seluruh pengujian otomatis (*automated feature tests*, *visual tests*, dan *Vite build*) tetap lulus 100% hijau (*zero regression*).

---

## 2. Evaluasi Pendekatan Teknis

| Parameter | Opsi A: Pemisahan Struktural Standar SuperAdmin (Direkomendasikan) | Opsi B: Mempertahankan Struktur Blade & Mengakali Posisi via CSS Absolute/Negative Margin |
| :--- | :--- | :--- |
| **Deskripsi** | Mengeluarkan `@include('admin.dashboard.partials._metrics')` dari dalam `_header.blade.php` dan menempatkannya sebagai baris mandiri `<section class="adm-metrics-row">` di `dashboard.blade.php`. | Membiarkan `_metrics` tetap di dalam banner Blade, lalu menariknya ke bawah menggunakan `transform: translateY(...)` atau `position: absolute`. |
| **Konsistensi Arsitektur** | **Sangat Tinggi (100% Paritas)**: Identik dengan arsitektur `superadmin/dashboard.blade.php` (`sa-hero` ➔ `sa-metrics-row` ➔ `sa-cockpit-layout`). | **Rendah**: Pola hacky yang rentan patah (*overlap*) pada berbagai breakpoint responsif layar. |
| **Dukungan Responsif** | **Sempurna**: Grid CSS fluid mengikuti breakpoint standar `1100px` (2 kolom) dan `640px` (1 kolom) tanpa overflow. | **Buruk**: Membutuhkan banyak manual offset calculation pada tiap media query. |
| **Kepatuhan Aturan AGENTS.md** | **Patuh**: Perubahan markup minimal untuk penataan semantik, CSS terpusat di `resources/css/pages/admin/dashboard.css`. | Berpotensi memunculkan styling anomali yang sulit dirawat. |

> **Rekomendasi**: Mengadopsi **Opsi A**, yaitu pemisahan semantik bersih yang identik dengan standar SuperAdmin.

---

## 3. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/dashboard/partials/_header.blade.php` | **Modify** | Mengeluarkan baris `@include('admin.dashboard.partials._metrics')` dan pita `.cv-statusline` dari dalam `_header.blade.php`, menyisakan hanya banner hero mandiri. |
| `resources/views/admin/dashboard.blade.php` | **Modify** | Menata urutan layout menjadi 3 seksi berurutan: <br>1. `@include('admin.dashboard.partials._header')` (Banner Hero)<br>2. `<section class="adm-metrics-row">@include('admin.dashboard.partials._metrics')</section>` (4 Kartu Metrik Mandiri)<br>3. Pita statusline `.cv-statusline` di bawah metrik sebelum layout cockpit 2-kolom. |
| `resources/css/pages/admin/dashboard.css` | **Modify** | 1. Berikan padding bawah proporsional pada `.cv-hero-banner` (`padding: 1.75rem 2rem; margin-bottom: 1.25rem;`).<br>2. Tambahkan kelas pembungkus `.adm-metrics-row`.<br>3. Reset `.adm-metrics-dock-grid` dari dock negatif menjadi grid bersih mandiri (`margin: 0; padding: 0; background: transparent; border: none; backdrop-filter: none;`).<br>4. Selaraskan styling kartu metrik dan Dark Mode. |
| `public/css/dashboard-cockpit-unified.css` | **Modify** | Sinkronisasi penyesuaian CSS fallback runtime. |

---

## 4. Rincian Teknis Implementasi

### A. Rekayasa Markup di `resources/views/admin/dashboard/partials/_header.blade.php`
Banner hero menjadi bersih dan mandiri:
```blade
<div class="cv-hero-banner gentelella-hero-banner">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.25rem;">
        <div>
            <div class="cv-eyebrow">{{ now()->translatedFormat('l, d F Y') }}</div>
            <h1 class="cv-hero-title">Dashboard Operasional Administrasi</h1>
            <p class="cv-hero-sub">Pusat kendali verifikasi berkas pendaftaran, pengawasan SLA, dan alokasi kuota kejuruan dinas.</p>
            
            <div class="cv-stamp" style="margin-top: 0.65rem;">
                <span class="dot-pulse"></span>
                <span>Bulan: <strong>{{ $bulanAktif ? $bulanAktif->bulan_mulai?->translatedFormat('F Y') : 'Tidak Aktif' }}</strong> {{ $bulanAktif && $bulanAktif->aktif ? '(Aktif)' : '' }}</span>
            </div>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            {{-- Form Periode Bulan & Tombol Ekspor Laporan --}}
            ...
        </div>
    </div>
</div>
```

### B. Rekayasa Urutan di `resources/views/admin/dashboard.blade.php`
```blade
<div class="gentelella-dashboard-admin adm-cockpit-wrapper" style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">

    {{-- BARIS 1: Atmospheric Hero Banner Mandiri --}}
    @include('admin.dashboard.partials._header')

    {{-- BARIS 2: 4 Kartu Metrik Utama Terpisah (SuperAdmin Parity) --}}
    <section class="adm-metrics-row">
        @include('admin.dashboard.partials._metrics')
    </section>

    {{-- BARIS 3: Statusline Horizontal Ribbon --}}
    <div class="cv-statusline gentelella-statusline">
        ...
    </div>

    {{-- BARIS 4: Asymmetrical Cockpit 2-Column Grid --}}
    <div class="cv-cockpit-layout">
        ...
    </div>
</div>
```

### C. Aturan CSS di `resources/css/pages/admin/dashboard.css`
```css
/* 2. Atmospheric Hero Banner (Clean Standalone Box) */
.cv-hero-banner {
  position: relative;
  overflow: hidden;
  border-radius: 14px;
  color: #ffffff;
  background: var(--cv-contour) right -40px top -20px / 720px auto no-repeat,
              radial-gradient(80% 120% at 100% 0%, rgba(7, 81, 255, 0.4), transparent 60%),
              linear-gradient(160deg, var(--cv-blue-800), var(--cv-blue-900));
  box-shadow: 0 8px 24px -4px rgba(6, 49, 140, 0.35);
  padding: 1.75rem 2rem 1.75rem 2rem;
  margin-bottom: 0.5rem;
}

/* 3. Standalone Four-Metric Cards Grid (SuperAdmin Parity) */
.adm-metrics-row {
  width: 100%;
  margin-bottom: 0.5rem;
}

.adm-metrics-dock-grid {
  display: grid !important;
  grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
  gap: 1.25rem !important;
  width: 100% !important;
  max-width: 100% !important;
  margin: 0 !important;
  padding: 0 !important;
  background: transparent !important;
  border: none !important;
  backdrop-filter: none !important;
  box-sizing: border-box !important;
}

@media (max-width: 1100px) {
  .adm-metrics-dock-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 1rem !important;
  }
}

@media (max-width: 640px) {
  .adm-metrics-dock-grid {
    grid-template-columns: 1fr !important;
    gap: 0.75rem !important;
  }
}
```

---

## 5. Permohonan Izin Perubahan Markup Blade (Sesuai AGENTS.md Bagian 3.3)

> [!IMPORTANT]
> **Alasan Perubahan Blade**:  
> Perubahan visual dari kartu metrik yang menyatu di dalam banner biru menjadi kartu mandiri yang melayang di bawah banner **mutlak memerlukan pemisahan tag `@include` di level struktur HTML**.
> 
> - **File Blade yang Akan Diubah**:
>   1. `resources/views/admin/dashboard/partials/_header.blade.php`: Memindahkan `@include('admin.dashboard.partials._metrics')` dan `.cv-statusline` ke luar container banner.
>   2. `resources/views/admin/dashboard.blade.php`: Menyusun urutan semantik baru (`_header` ➔ `<section class="adm-metrics-row">` ➔ `statusline` ➔ `cockpit-layout`).
> - Tidak ada logika bisnis atau kontrak route/API yang diubah.

---

## 6. Langkah Implementasi (Checklist)

- [x] **Langkah 1**: Perbarui `resources/views/admin/dashboard/partials/_header.blade.php` untuk menghapus include metrik dan memindahkan statusline.
- [x] **Langkah 2**: Perbarui `resources/views/admin/dashboard.blade.php` untuk menampung seksi `<section class="adm-metrics-row">` dan statusline di bawah banner.
- [x] **Langkah 3**: Perbarui stylesheet `resources/css/pages/admin/dashboard.css` (padding banner hero, container `.adm-metrics-row`, dan reset `.adm-metrics-dock-grid`).
- [x] **Langkah 4**: Sinkronkan aturan CSS ke `public/css/dashboard-cockpit-unified.css`.
- [x] **Langkah 5**: Jalankan asset compilation: `npm run build`.
- [x] **Langkah 6**: Jalankan automated test suite admin dashboard: `php artisan test tests/Feature/AdminDashboardEnhancementTest.php tests/Feature/AdminTerminologyDesignSystemTest.php tests/Feature/GentelellaDashboardRevampTest.php`.
- [x] **Langkah 7**: Jalankan Laravel Pint: `./vendor/bin/pint --test`.

---

## 7. Strategi Testing

1. **Automated Feature Tests**:
   - `test_admin_dashboard_metrics_dock_renders_full_width_grid_structure()`: Memastikan kelas `adm-metrics-dock-grid` dan `adm-metric-card-col` tetap hadir dan dirender penuh.
   - `test_admin_bisa_mengakses_dashboard_dengan_metrik_lengkap_dan_command_center()`: Memastikan seluruh data 4 kartu metrik (nilai, ikon, SLA, okupansi kuota) tetap lengkap.
   - `AdminTerminologyDesignSystemTest`: Memastikan terminologi bulan magang tetap tampil valid.
   - `GentelellaDashboardRevampTest`: Memastikan standar tema Gentelella tetap terpenuhi.
2. **Quality Gate**:
   - `npm run build`: 100% build sukses tanpa error.
   - `pint --test`: 100% clean formatting.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Banner biru `Dashboard Operasional Administrasi` berdiri sendiri dengan 4 sudut membulat rapi dan padding bawah proporsional.
- [x] Ke-4 kartu metrik operasional terpisah di bawah banner di atas canvas halaman, persis seperti contoh tampilan SuperAdmin (Screenshot 2).
- [x] Statusline ribbon tertata di bawah metrik sebelum layout utama.
- [x] Tampilan responsif pada tablet (2 kolom) dan mobile (1 kolom) tetap fluid dan tidak ada elemen yang tumpang tindih.
- [x] Seluruh automated test suite lulus 100% hijau (*PASS*).

