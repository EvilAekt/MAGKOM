# MASTER PRD & SENIOR DEVELOPER TASK HANDBOOK
## Redesign Dashboard Admin MagKom: Adopsi Visual "Clearview Modern Cockpit" Berbasis Arsitektur Gentelella

---

**Dokumen**: Master Task Handbook & Technical PRD  
**Versi**: 1.0 (Ready for Execution)  
**Target Eksekutor**: Junior Developer & AI Agent (e.g. Gemini 3.8 Flash)  
**Lead Architect / Author**: Senior Software Architect & UI/UX Design System Lead  
**Branch Git**: `test-2`  
**Referensi Visual Repositori**: `https://github.com/EvilAekt/clearview-admin-console.git` (Folder Scratch: `scratch/clearview-admin-console`)  
**Target View Utama**: [resources/views/admin/dashboard.blade.php](file:///g:/MAGKOM/resources/views/admin/dashboard.blade.php)  
**Target Stylesheet**: [public/css/admin-cockpit-clearview.css](file:///g:/MAGKOM/public/css/admin-cockpit-clearview.css) (atau integrasi CSS Dashboard terpadu)

---

## 1. PENGANTAR & TUJUAN TASK (WHY WE DO THIS)

### 1.1. Latar Belakang
Tim pengembang telah mengevaluasi prototipe antarmuka modern dari repositori `clearview-admin-console` yang dirancang khusus untuk portal MagKom Sleman. Tampilan tersebut memiliki keunggulan visual kelas enterprise:
- **Atmospheric Deep Blue Hero** dengan garis topografi contour SVG halus.
- **Integrated Metric Dock** (4 metrik tabular tersemat di dasar hero banner dengan sparkline).
- **Statusline Ribbon** horizontal dengan live indicator dot dan dropdown selector.
- **Attention SLA Hazard Card** dengan stripe motif kuning untuk berkas mendesak.
- **Asymmetrical 2-Column Cockpit Layout** (Main column 1fr + Sticky Aside 340px untuk *"Perlu Tindakan Hari Ini"* dan *"Aksi Cepat"*).
- **Segmented Quota Seat Bars** (visualisasi slot kursi kuota jurusan/bidang ala bioskop/kursi kabin).

### 1.2. Misi Junior Developer / AI
Tugas Anda adalah **mentransformasi tampilan Dashboard Admin MagKom** ([resources/views/admin/dashboard.blade.php](file:///g:/MAGKOM/resources/views/admin/dashboard.blade.php)) agar memiliki estetika visual, kedalaman desain, dan rasa (look-and-feel) persis seperti prototipe **Clearview Admin Console**, namun **tetap mengakar pada arsitektur Blade Gentelella** yang telah ada di MagKom (`<x-gentelella.card>`, `<x-gentelella.x-panel>`, `<x-gentelella.tile-stat>`).

---

## 2. ATURAN EMAS & GUARDRAILS (SACRED CONTRACT - BACA SEBELUM CODING!)

> [!CAUTION]
> **PELANGGARAN TERHADAP POIN-POIN DI BAWAH INI AKAN MEMBATALKAN PEKERJAAN ANDA KARENA DAPAT MERUSAK TEST SUITE CI/CD.**

1. **ZERO BACKEND LOGIC MUTATION**:
   - **DILARANG KERAS** memodifikasi `app/Http/Controllers/Admin/DashboardController.php`, `routes/web.php`, Models, Enums, Helpers, atau Migrations.
   - Semua variabel yang dikirimkan Controller (`$bulanAktif`, `$daftarBulan`, `$pengajuanMenunggu`, `$pengajuanSlaKritisCount`, `$antreanPendaftaran`, `$kuotaPerBidang`, `$bebanPembimbing`, dll.) harus digunakan sebagaimana adanya.
2. **PRESERVE SACRED DOM IDS & FORM INPUTS**:
   - Form filter bulan: `<form method="GET" action="{{ route('admin.dashboard') }}" class="adm-period-form">` wajib memiliki input select dengan `id="selectBulan"` dan `name="bulan_id"`.
   - Event listener: `onchange="this.form.submit()"` atau selector JavaScript yang relevan wajib berfungsi.
3. **PRESERVE TEST ASSERTIONS STRINGS**:
   - Test suite [tests/Feature/AdminSuperAdminPhaseTwoTest.php](file:///g:/MAGKOM/tests/Feature/AdminSuperAdminPhaseTwoTest.php) dan dashboard tests lainnya melakukan `assertSee` pada teks tertentu:
     - `'Verifikasi Tertunda'`
     - `'Perlu Verifikasi Segera'`
     - `'Pendaftar Menunggu Verifikasi'` (atau nama seksi verifikasi)
     - `'Kuota Bidang & Jurusan'`
     - `'Beban Pembimbing Lapangan'`
     - `'Ekspor Laporan'`
     - `'Pilih Bulan:'` atau label bulan
   - Teks-teks di atas **WAJIB TETAP HADIR** dalam markup Blade.
4. **AUTOMATED TEST STATUS**:
   - Seluruh **457 tests** (2,682 assertions) harus tetap berstatus **PASSED** setelah implementasi.
5. **CODE STANDARDS**:
   - Jalankan `./vendor/bin/pint --test` dan pastikan 0 error styling.

---

## 3. BEDAH DNA DESAIN "CLEARVIEW ADMIN CONSOLE"

Berdasarkan investigasi file [scratch/clearview-admin-console/src/styles.css](file:///C:/Users/galan/.gemini/antigravity-ide/brain/34d1a706-d164-4568-bc58-aef26291df8b/scratch/clearview-admin-console/src/styles.css) dan [src/routes/index.tsx](file:///C:/Users/galan/.gemini/antigravity-ide/brain/34d1a706-d164-4568-bc58-aef26291df8b/scratch/clearview-admin-console/src/routes/index.tsx), berikut komponen visual utama yang harus direplikasi:

```
+----------------------------------------------------------------------------------------------------+
| HERO BANNER ATMOSPHERIC BLUE (Gradien #06318C -> #061E5B + SVG Contour Overlay)                   |
| Eyebrow: KAMIS, 22 OKTOBER 2026 . 01:45 WIB          [Pendaftaran Dibuka / Aktif . Glowing Dot]    |
| Title: Dashboard Operasional Administrasi                                                          |
| Subtitle: Pusat verifikasi berkas pendaftaran, pengawasan SLA, alokasi kuota, & penempatan bimbingan|
| Buttons: [Ekspor Laporan CSV/PDF]   [Tinjau Antrean Berkas (N)]                                     |
| +------------------------------------------------------------------------------------------------+ |
| | FOUR-METRIC DOCK (Terhubung di dasar hero banner):                                             | |
| | [Verifikasi Tertunda | 12]   [Total Kuota | 120]   [Peserta Aktif | 86]   [SLA Kritis | 4]     | |
| +------------------------------------------------------------------------------------------------+ |
+----------------------------------------------------------------------------------------------------+

+----------------------------------------------------------------------------------------------------+
| STATUSLINE RIBBON:                                                                                 |
| * Landing Publik: Aktif  |  * Waiting List: 4 Pendaftar  |  * SLA Limit: 3 Hari Kerja  |  [Bulan v]|
+----------------------------------------------------------------------------------------------------+

+----------------------------------------------------------------------------------------------------+
| ATTENTION SLA HAZARD CARD (Stripe Bar Kuning-Oranye):                                             |
| [4 LEWAT SLA]  Berkas terlama di antrean verifikasi mendekati batas 3 hari kerja   [Buka Antrean ->]|
+----------------------------------------------------------------------------------------------------+

+-------------------------------------------------------------------------+--------------------------+
| KOLOM KIRI (Main Content - 1fr)                                         | KOLOM KANAN (Aside 340px)|
|                                                                         |                          |
| [PANEL 1: Pendaftar Menunggu Verifikasi (FIFO BR-04)]                   | [PANEL: PERLU TINDAKAN]  |
| - Tabel rapi dengan avatar inisial, asal kampus, jurusan, waktu masuk   | - 4 Berkas lewat SLA     |
|   dan badge countdown SLA (<x-gentelella.x-panel>)                       | - 2 Penempatan pending   |
|                                                                         | - Kuota TI tersisa 2     |
| [PANEL 2: Kuota Bidang & Jurusan (Segmented Seat Bars .seg)]            |                          |
| - Nama Bidang / Jurusan, angka kursi terisi / pagu, dan bar kotak-kotak | [PANEL: AKSI CEPAT]      |
|                                                                         | - + Verifikasi Masal     |
| [PANEL 3: Beban Pembimbing Lapangan (Rasio Maks 1:8 BR-13)]             | - Surat Diterima PDF     |
| - Tabel nama mentor, bidang, jumlah mentee, dan status beban (Tersedia/ | - Ekspor Rekap Bulanan   |
|   Penuh)                                                                |                          |
+-------------------------------------------------------------------------+--------------------------+
```

---

## 4. PANDUAN IMPLEMENTASI LANGKAH DEMI LANGKAH (STEP-BY-STEP TASKS)

### TAHAP 1: Pembuatan Stylesheet CSS Clearview
**File**: [public/css/admin-cockpit-clearview.css](file:///g:/MAGKOM/public/css/admin-cockpit-clearview.css)

Buat file CSS ini dan pastikan mengimpor token desain resmi MagKom, variabel CSS, efek contour, segmented bars, dan layout grid 2 kolom.

```css
/* ==========================================================================
   Admin Cockpit Clearview Theme - MagKom Sleman
   Mengadopsi Estetika Clearview Admin Console ke dalam Gentelella Architecture
   ========================================================================== */

:root {
  --cv-blue-900: #061e5b;
  --cv-blue-800: #06318c;
  --cv-blue-700: #0641cc;
  --cv-blue-600: #0751ff;
  --cv-blue-tint: #eff6ff;
  --cv-paper: #f8fafc;
  --cv-surface: #ffffff;
  --cv-line: #e2e8f0;
  --cv-line-strong: #cbd5e1;
  --cv-ink: #0f172a;
  --cv-ink-muted: #64748b;
  --cv-ok: #10b981;
  --cv-warn: #f59e0b;
  --cv-bad: #ef4444;
  --cv-contour: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='600' height='300' viewBox='0 0 600 300' fill='none' stroke='white' stroke-opacity='.09' stroke-width='1.2'%3E%3Cpath d='M-20 220C80 180 120 250 220 200S380 90 480 130 620 60 640 40'/%3E%3Cpath d='M-20 250C90 210 140 280 240 230S390 120 500 160 630 90 650 70'/%3E%3Cpath d='M-20 190C70 150 110 220 200 170S370 60 460 100 610 30 630 10'/%3E%3Cpath d='M-20 280C100 240 160 300 260 260S400 150 520 190 640 120 660 100'/%3E%3Cellipse cx='470' cy='120' rx='60' ry='26'/%3E%3Cellipse cx='470' cy='120' rx='34' ry='14'/%3E%3C/svg%3E");
}

/* 1. Cockpit 2-Column Grid Layout */
.cv-cockpit-layout {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 340px;
  gap: 1.5rem;
  align-items: start;
  width: 100%;
}

@media (max-width: 1200px) {
  .cv-cockpit-layout {
    grid-template-columns: 1fr;
  }
}

/* 2. Atmospheric Hero Banner */
.cv-hero-banner {
  position: relative;
  overflow: hidden;
  border-radius: 14px;
  color: #ffffff;
  background: var(--cv-contour) right -40px top -20px / 720px auto no-repeat,
              radial-gradient(80% 120% at 100% 0%, rgba(7, 81, 255, 0.4), transparent 60%),
              linear-gradient(160deg, var(--cv-blue-800), var(--cv-blue-900));
  box-shadow: 0 4px 20px -4px rgba(6, 49, 140, 0.35);
  padding: 1.75rem 2rem 0 2rem;
}

.cv-hero-banner::after {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: inherit;
  pointer-events: none;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.cv-eyebrow {
  font-family: monospace;
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.75);
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

.cv-hero-title {
  font-size: 1.85rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  margin: 0.4rem 0;
  color: #ffffff;
}

.cv-hero-sub {
  font-size: 0.875rem;
  color: rgba(255, 255, 255, 0.8);
  max-width: 65ch;
  line-height: 1.45;
  margin: 0;
}

.cv-stamp {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.85rem;
  border: 1.5px solid rgba(16, 185, 129, 0.6);
  color: #6ee7b7;
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-family: monospace;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  background: rgba(16, 185, 129, 0.12);
  transform: rotate(-1deg);
}

.cv-stamp .dot-pulse {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
  animation: cvPulse 2s infinite;
}

@keyframes cvPulse {
  50% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
}

/* 3. Integrated Four-Metric Dock */
.cv-metrics-dock {
  margin: 1.5rem -2rem 0 -2rem;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  border-top: 1px solid rgba(255, 255, 255, 0.12);
  background: rgba(6, 30, 91, 0.35);
}

@media (max-width: 768px) {
  .cv-metrics-dock {
    grid-template-columns: repeat(2, 1fr);
  }
}

.cv-dock-item {
  padding: 1.15rem 1.5rem;
  border-right: 1px solid rgba(255, 255, 255, 0.1);
  color: #ffffff;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.cv-dock-item:last-child {
  border-right: none;
}

.cv-dock-label {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.75);
  font-weight: 600;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cv-dock-value {
  font-size: 2.25rem;
  font-weight: 800;
  letter-spacing: -0.04em;
  line-height: 1.1;
  margin: 0.35rem 0;
  font-variant-numeric: tabular-nums;
}

.cv-dock-foot {
  font-size: 0.72rem;
  color: rgba(255, 255, 255, 0.7);
}

/* 4. Statusline Ribbon */
.cv-statusline {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
  padding: 0.75rem 1.25rem;
  background: var(--neutral-0, #ffffff);
  border: 1px solid var(--neutral-300, #e2e8f0);
  border-radius: 10px;
  font-size: 0.8125rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.cv-statusline-item {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--neutral-700, #334155);
}

.cv-statusline-item dt {
  color: var(--neutral-500, #64748b);
  font-weight: 500;
}

.cv-statusline-item dd {
  margin: 0;
  font-weight: 700;
  color: var(--neutral-900, #0f172a);
}

/* 5. Attention SLA Hazard Card */
.cv-attention-card {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 1.25rem;
  align-items: center;
  background: #ffffff;
  border: 1px solid #fed7aa;
  border-radius: 12px;
  padding: 1.15rem 1.5rem;
  position: relative;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(245, 158, 11, 0.08);
}

.cv-attention-card::before {
  content: "";
  position: absolute;
  inset: 0 auto 0 0;
  width: 6px;
  background: repeating-linear-gradient(-45deg, #f59e0b 0 8px, #fbbf24 8px 16px);
}

.cv-attention-count {
  font-size: 2.25rem;
  font-weight: 900;
  color: #d97706;
  line-height: 1;
  letter-spacing: -0.04em;
  text-align: center;
}

.cv-attention-count small {
  display: block;
  font-size: 0.6875rem;
  font-weight: 700;
  color: #64748b;
  letter-spacing: 0.05em;
  margin-top: 0.2rem;
}

/* 6. Segmented Quota Seat Bars (.cv-seg) */
.cv-seg {
  display: grid;
  grid-auto-flow: column;
  gap: 3px;
  height: 12px;
  margin-top: 0.4rem;
}

.cv-seg i {
  border-radius: 2px;
  background: #e2e8f0;
  display: block;
  height: 100%;
}

.cv-seg i.filled {
  background: linear-gradient(180deg, #0751ff, #0641cc);
}

.cv-seg.full i.filled {
  background: linear-gradient(180deg, #f59e0b, #d97706);
}

/* 7. Aside Todo & Quick Action List */
.cv-todo-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.cv-todo-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 0.875rem;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  text-decoration: none;
  transition: all 0.15s ease;
}

.cv-todo-item:hover {
  background: #eff6ff;
  border-color: #bfdbfe;
}

.cv-quick-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.5rem;
}

.cv-quick-grid .cv-btn-full {
  grid-column: 1 / -1;
}

/* Dark Mode Overrides */
[data-theme="dark"] .cv-statusline,
[data-theme="dark"] .cv-attention-card {
  background: #182234 !important;
  border-color: #26354d !important;
}

[data-theme="dark"] .cv-todo-item {
  background: #111c2e !important;
  border-color: #26354d !important;
}
```

---

### TAHAP 2: Modifikasi View Blade Dashboard Admin
**File**: [resources/views/admin/dashboard.blade.php](file:///g:/MAGKOM/resources/views/admin/dashboard.blade.php)

Lakukan perombakan markup dengan struktur berikut:

1. **Tambahkan Stylesheet**:
   ```html
   @push('styles')
       <link rel="stylesheet" href="{{ asset('css/admin-cockpit-clearview.css') }}">
   @endpush
   ```

2. **Gantikan Bagian Atas dengan Atmospheric Hero Banner & Integrated Metric Dock**:
   ```html
   {{-- ATMOSPHERIC HERO BANNER CLEARVIEW --}}
   <div class="cv-hero-banner">
       <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
           <div>
               <div class="cv-eyebrow">{{ now()->translatedFormat('l, d F Y') }} &bull; PORTAL KOMINFO SLEMAN</div>
               <h1 class="cv-hero-title">Dashboard Operasional Administrasi</h1>
               <p class="cv-hero-sub">Pusat verifikasi berkas pendaftaran, pengawasan SLA, alokasi kuota bidang dinas, dan tata kelola penempatan magang.</p>
               
               <div class="cv-stamp">
                   <span class="dot-pulse"></span>
                   <span>PERIODE: {{ $bulanAktif ? $bulanAktif->bulan_mulai?->translatedFormat('F Y') : 'TIDAK AKTIF' }}</span>
               </div>
           </div>

           <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
               <a href="{{ route('admin.laporan.index') }}" style="height: 38px; padding: 0 1rem; border-radius: 8px; background: rgba(255,255,255,0.12); color: #fff; border: 1px solid rgba(255,255,255,0.25); text-decoration: none; font-size: 0.8125rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                   <x-sa.icon name="download" :size="15" />
                   <span>Ekspor Laporan</span>
               </a>
               <a href="{{ route('admin.pendaftaran.index') }}" style="height: 38px; padding: 0 1.25rem; border-radius: 8px; background: #ffffff; color: var(--cv-blue-800); border: none; text-decoration: none; font-size: 0.8125rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.35rem; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                   <x-sa.icon name="clipboard-check" :size="15" />
                   <span>Tinjau Berkas</span>
               </a>
           </div>
       </div>

       {{-- INTEGRATED FOUR-METRIC DOCK (Mempertahankan label teks persis test suite) --}}
       <div class="cv-metrics-dock" role="region" aria-label="Metrik Operasional Administrasi">
           {{-- Dock 1: Verifikasi Tertunda --}}
           <div class="cv-dock-item">
               <div class="cv-dock-label">
                   <span>Verifikasi Tertunda</span>
                   <x-sa.icon name="clock" :size="14" />
               </div>
               <div class="cv-dock-value">{{ number_format($pengajuanMenunggu) }}</div>
               <div class="cv-dock-foot">
                   @if($pengajuanSlaKritisCount > 0)
                       <span style="color: #fca5a5; font-weight: 700;">⚠️ {{ $pengajuanSlaKritisCount }} Perlu Verifikasi Segera</span>
                   @else
                       <span>Perlu Verifikasi Segera</span>
                   @endif
               </div>
           </div>

           {{-- Dock 2: Total Kuota Magang --}}
           <div class="cv-dock-item">
               <div class="cv-dock-label">
                   <span>Total Kuota Magang</span>
                   <x-sa.icon name="layers" :size="14" />
               </div>
               <div class="cv-dock-value">{{ number_format($totalKuota) }}</div>
               <div class="cv-dock-foot">
                   <span>Terisi: {{ number_format($totalTerisi) }} kursi (Sisa: {{ max(0, $totalKuota - $totalTerisi) }})</span>
               </div>
           </div>

           {{-- Dock 3: Peserta Aktif Bimbingan --}}
           <div class="cv-dock-item">
               <div class="cv-dock-label">
                   <span>Peserta Aktif Bimbingan</span>
                   <x-sa.icon name="user-check" :size="14" />
               </div>
               <div class="cv-dock-value">{{ number_format($pesertaAktif) }}</div>
               <div class="cv-dock-foot">
                   <span>{{ $totalPenempatanAktif }} penempatan aktif terdaftar</span>
               </div>
           </div>

           {{-- Dock 4: Alumni Selesai --}}
           <div class="cv-dock-item">
               <div class="cv-dock-label">
                   <span>Alumni Selesai</span>
                   <x-sa.icon name="award" :size="14" />
               </div>
               <div class="cv-dock-value">{{ number_format($pesertaSelesai) }}</div>
               <div class="cv-dock-foot">
                   <span>Lulus &amp; sertifikat terbit</span>
               </div>
           </div>
       </div>
   </div>
   ```

3. **Statusline Ribbon & Dropdown Periode**:
   - Mempertahankan form `#selectBulan` dan selector `.adm-period-form`:
   ```html
   <div class="cv-statusline">
       <div class="cv-statusline-item">
           <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 2px rgba(16,185,129,0.2);"></span>
           <dt>Landing Publik:</dt>
           <dd>Aktif</dd>
       </div>
       <div style="width: 1px; height: 16px; background: #e2e8f0;"></div>
       <div class="cv-statusline-item">
           <dt>Pendaftar Menunggu Verifikasi:</dt>
           <dd>{{ $pengajuanMenunggu }} Berkas</dd>
       </div>
       <div style="width: 1px; height: 16px; background: #e2e8f0;"></div>
       <div class="cv-statusline-item">
           <dt>SLA Maksimal:</dt>
           <dd>3 Hari Kerja (BR-04)</dd>
       </div>

       <div style="margin-left: auto;">
           <form method="GET" action="{{ route('admin.dashboard') }}" class="adm-period-form" style="display: inline-flex; align-items: center; gap: 0.5rem;">
               <label for="selectBulan" style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); margin: 0; text-transform: uppercase;">Pilih Bulan: <span style="font-size: 0.7rem; color: var(--neutral-500);">(Bulan Magang)</span> <span class="sr-only">Bulan:</span></label>
               <select name="bulan_id" id="selectBulan" onchange="this.form.submit()" style="background: transparent; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0.25rem 0.5rem; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); outline: none; cursor: pointer;">
                   @foreach($daftarBulan as $b)
                       <option value="{{ $b->id }}" {{ $bulanAktif && $bulanAktif->id === $b->id ? 'selected' : '' }}>
                           {{ $b->bulan_mulai?->translatedFormat('F Y') }} {{ $b->aktif ? '(Aktif)' : '' }}
                       </option>
                   @endforeach
               </select>
           </form>
       </div>
   </div>
   ```

4. **Attention SLA Hazard Card**:
   ```html
   @if($pengajuanSlaKritisCount > 0)
       <div class="cv-attention-card">
           <div class="cv-attention-count">
               {{ $pengajuanSlaKritisCount }}
               <small>LEWAT SLA</small>
           </div>
           <div>
               <h4 style="font-size: 0.9375rem; font-weight: 800; color: #9a3412; margin: 0 0 0.25rem 0;">
                   Berkas Terlama Menunggu Verifikasi Seleksi &mdash; Batas 3 Hari Kerja
               </h4>
               <p style="font-size: 0.8125rem; color: #78350f; margin: 0;">
                   Segera proses peninjauan berkas pendaftar berikut untuk menjaga kepatuhan SLA dinas (BR-04 FIFO).
               </p>
           </div>
           <div>
               <a href="{{ route('admin.pendaftaran.index') }}" class="adm-btn-action-primary" style="padding: 0.55rem 1rem; border-radius: 8px;">
                   <span>Buka Antrean &rarr;</span>
               </a>
           </div>
       </div>
   @endif
   ```

5. **2-Column Cockpit Layout**:
   - **Kolom Kiri**:
     - `<x-gentelella.x-panel title="Pendaftar Menunggu Verifikasi" subtitle="Antrean seleksi berkas terurut FIFO berdasarkan waktu pengajuan">`
     - `<x-gentelella.x-panel title="Kuota Bidang & Jurusan" subtitle="Pemanfaatan pagu kursi magang per formasi bidang dinas">` (dengan loop kursi `.cv-seg`)
     - `<x-gentelella.x-panel title="Beban Pembimbing Lapangan" subtitle="Distribusi bimbingan dengan rasio maksimal 1 : 8 mentee">`
   - **Kolom Kanan (Aside Sticky)**:
     - `<x-gentelella.card>` untuk **"Perlu Tindakan Hari Ini"**:
       - Daftar todo interaktif (`.cv-todo-item`): link ke verifikasi berkas, penempatan peserta baru, atau konfirmasi kuota penuh.
     - `<x-gentelella.card>` untuk **"Aksi Cepat Admin"**:
       - Tombol cepat ke: `+ Tinjau Berkas`, `Penempatan Peserta`, `Ekspor Laporan PDF/Excel`, `Arsip Selesai`.

---

## 5. QUALITY ASSURANCE & VERIFIKASI (CHECKLIST MANDATORI)

Setelah menulis kode, Junior Developer atau AI Agent **WAJIB MENJALANKAN URUTAN TEST BERIKUT**:

```bash
# 1. Pastikan tidak ada syntax error pada Blade dan PHP
php artisan view:clear

# 2. Uji Test Suite Khusus Admin Dashboard
php artisan test --filter="AdminDashboardVisualTest|AdminSuperAdminPhaseTwoTest"

# 3. Uji Test Suite Figma Alignment
php artisan test --filter="SuperAdminFigmaDesignAlignmentTest"

# 4. Jalankan Full Automated Test Suite (Seluruh 457 tests wajib hijau!)
php artisan test

# 5. Jalankan Linter Kode
./vendor/bin/pint --test

# 6. Perbarui Graph Pengetahuan Codebase
graphify update .
```

---

## 6. PROMPT INSTRUKSI SIAP PAKAI UNTUK AI LAIN (GEMINI 3.8 FLASH / JUNIOR DEVELOPER)

Salin teks berikut jika mendelegasikan tugas ke AI yang lebih terjangkau atau junior developer:

```text
TOLONG EKSEKUSI REFACTORING DASHBOARD ADMIN MAGKOM BERDASARKAN MASTER PRD INI:
1. Buka file: docs/plans/2026-10-22-redesign-dashboard-admin-clearview-gentelella.md
2. Buat file stylesheet baru: public/css/admin-cockpit-clearview.css sesuai spesifikasi desain Clearview.
3. Edit file view: resources/views/admin/dashboard.blade.php.
   - Bungkus tampilan dengan Atmospheric Hero Banner (#06318C -> #061E5B + SVG Contour).
   - Pasang Four-Metric Dock langsung di dasar Hero Banner.
   - Pasang Statusline Ribbon horizontal lengkap dengan dropdown selectBulan dan class adm-period-form.
   - Pasang Attention SLA Hazard Card untuk berkas kritis.
   - Terapkan 2-column cockpit layout (Kolom kiri untuk tabel verifikasi FIFO, segmented kuota bars, dan beban mentor; Kolom kanan aside sticky untuk 'Perlu Tindakan Hari Ini' dan 'Aksi Cepat').
4. JANGAN UBAH controller, routes, atau model apapun.
5. PERTAHANKAN seluruh teks assertion: 'Verifikasi Tertunda', 'Perlu Verifikasi Segera', 'Pendaftar Menunggu Verifikasi', 'Kuota Bidang & Jurusan', 'Beban Pembimbing Lapangan', 'Ekspor Laporan'.
6. Verifikasi dengan menjalankan: php artisan test --filter="AdminDashboardVisualTest|AdminSuperAdminPhaseTwoTest" dan pastikan 100% passed.
```

---
*Dokumen ini disetujui untuk diimplementasikan pada branch `test-2` MagKom Sleman.*
