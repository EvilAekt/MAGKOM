# TASK BRIEF SENIOR DEVELOPER: PANDUAN IMPLEMENTASI REVISI PROGRES TUGAS (HOVER PILLS), PROPORSI BIODATA HERO, & ADAPTASI DATA SISWA SMK VS MAHASISWA

> **Dari:** Senior Developer & Lead Software Architect  
> **Kepada:** Junior Developer / AI Model Pelaksana (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Kabupaten Sleman)  
> **Status:** READY FOR REVIEW & IMPLEMENTATION  
> **Tingkat Kesulitan:** Menengah (UI Micro-interaction, CSS Capsule Engineering, Conditional Blade Templating, Context-Aware Data Rendering)  
> **Dokumentasi Terkait:** `AGENTS.md`, `.agents/rules/AGENTS.md`, `docs/01-PRD-MagKom.md`, `docs/02-TDD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`  
> **Target Endpoint & Rute:** `GET /pembimbing/peserta/{id}` (`route('pembimbing.peserta.show')`)  
> **Berkas Acuan Desain Resmi:**  
> 1. `docs/plans/2026-11-09-profil-360-v3-master.jpg` (Desain Target Halaman Master)  
> 2. `docs/plans/2026-11-09-profil-360-v3-crop-progres-task-pills.png` (Crop Acuan Legenda Progres Tugas Kapsul Horizontal & Hover)  
> 3. `docs/plans/2026-11-09-profil-360-v3-crop-biodata-hero-card.png` (Crop Acuan Proporsi Biodata Hero Card Mentee)

---

## 1. 🔍 EXECUTIVE SUMMARY & 3 SPESIFIKASI PEKERJAAN

Berdasarkan review visual dan instruksi tegas dari Product Owner, terdapat 3 penyempurnaan utama pada halaman **Profil 360° Peserta Bimbingan**:

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ 1. RESTRUKTURISASI LEGENDA PROGRES TUGAS: KAPSUL HORIZONTAL DENGAN HOVER EFFECT         │
│    - Mengubah legenda menjadi 3 baris kapsul memanjang (pill horizontal).               │
│    - Background transparan bergradasi (#5A69BF / rgba(255,255,255,0.14)), border radius │
│      12px, dot warna di kiri, teks label, dan angka counter bold di kanan.              │
│    - Interaksi hover dinamis: highlight lebih terang, transform geser 4px, soft shadow. │
├─────────────────────────────────────────────────────────────────────────────────────────┤
│ 2. PENYESUAIAN UKURAN & PROPORSI BIODATA PADA HERO CARD MENTEE                          │
│    - Menyeleraskan proporsi kolom kiri (.mentee-hero-left) dan kanan (.mentee-hero-right│
│      dengan rasio seimbang (46% : 54%).                                                 │
│    - Avatar ring emas glowing (diameter 84px, border 3.5px #F59E0B, glow 16px).         │
│    - Inisial nama otomatis 2 huruf (contoh: "NA" untuk Naufal Pratama).                 │
│    - Jarak padding 2rem 2.25rem dan distribusi vertikal teks nama, badges, dan tombol.  │
├─────────────────────────────────────────────────────────────────────────────────────────┤
│ 3. ADAPTASI KONTEKSTUAL DATA PESERTA: SISWA SMK VS MAHASISWA                            │
│    - Deteksi otomatis jenjang peserta (SMK vs D3/S1).                                   │
│    - Siswa SMK: TIDAK ADA KAMPUS & DOSEN. Label menjadi "KOMPETENSI KEAHLIAN",          │
│      nomor induk menjadi "NISN", dan catatan pembimbing menjadi "Guru Pembimbing".      │
│    - Mahasiswa (D3/S1): Label "PROGRAM STUDI", nomor induk "NIM", dan catatan           │
│      pembimbing kampus "Dosen Pembimbing".                                              │
│    - DILARANG menampilkan "Data dosen dan kampus belum diisi." untuk siswa SMK!         │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. 🎯 BEDAH DETAIL SPESIFIKASI & SOLUSI ARSITEKTUR

### Fitur 1: Restrukturisasi Legenda Progres Tugas (Kapsul Horizontal & Hover)
* **Kebutuhan Visual Sesuai Gambar (`profil-360-v3-crop-progres-task-pills.png`):**
  - Kartu `PROGRES TUGAS` berlatar Slate Navy `#28487A`.
  - Di sebelah kanan donat chart 50%, legenda disusun menjadi **3 baris kapsul horizontal memanjang** (`.donut-legend-pill`).
  - Struktur setiap kapsul:
    1. **Baris 1 (Selesai)**: Kapsul berlatar `rgba(255, 255, 255, 0.14)`, sisi kiri memiliki dot bulat kuning `#F59E0B` (diameter 9px) dan teks `Selesai` putih tebal. Sisi kanan menampilkan angka `3` putih tebal.
    2. **Baris 2 (Dikerjakan)**: Kapsul berlatar `rgba(255, 255, 255, 0.14)`, sisi kiri memiliki dot bulat putih `#FFFFFF` dan teks `Dikerjakan`. Sisi kanan menampilkan angka `2`.
    3. **Baris 3 (Belum)**: Kapsul berlatar `rgba(255, 255, 255, 0.14)`, sisi kiri memiliki dot bulat abu-abu `#A0AEC0` dan teks `Belum`. Sisi kanan menampilkan angka `1`.
  - **Efek Hover Interaktif (`:hover`)**:
    - `background-color: rgba(255, 255, 255, 0.24);`
    - `transform: translateX(4px);`
    - `box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);`
    - `transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);`

---

### Fitur 2: Penyesuaian Proporsi & Ukuran Biodata Hero Card
* **Kebutuhan Visual Sesuai Gambar (`profil-360-v3-crop-biodata-hero-card.png`):**
  - Kolom kiri `.mentee-hero-left` membungkus avatar dan data identitas mentee dengan susunan yang harmonis terhadap tinggi kartu:
    - **Avatar**:
      - Diameter: `84px` x `84px` (min-width `84px`).
      - Border: `3.5px solid #F59E0B`.
      - Box-shadow glow: `0 0 16px rgba(245, 158, 11, 0.28)`.
      - Inisial teks: 2 karakter depan nama mentee (`strtoupper(substr($nama, 0, 2))`), menghasilkan inisial `NA` untuk "Naufal Pratama" sesuai acuan mockup.
    - **Identitas Mentee**:
      - Nama: `font-size: 1.5rem` (24px), font-weight `800`, warna `#FFFFFF`, line-height `1.2`.
      - Institusi: `font-size: 0.875rem` (14px), font-weight `500`, warna `#CBD5E1`.
    - **Badges Status Row**:
      - `[Aktif]`: Emerald green `#10B981`, padding `0.25rem 0.85rem`, border-radius `9999px`, font-size `0.75rem`, font-weight `700`.
      - `[Jalur Umum]`: Background gelap `rgba(10, 30, 63, 0.55)`, border `1.5px solid #F59E0B`, text `#FDE68A`, border-radius `9999px`, font-size `0.75rem`, font-weight `700`.
    - **Action Buttons Row**:
      - `[Logbook]`: Outline transparan, border `1.5px solid rgba(255, 255, 255, 0.7)`, text `#FFFFFF`, border-radius `9999px`, padding `0.45rem 1.4rem`.
      - `[Tugas]`: Solid amber `#F59E0B`, border `1.5px solid #F59E0B`, text `#0F172A`, font-weight `700`, border-radius `9999px`, padding `0.45rem 1.4rem`.
    - **Garis Pembatas Vertikal**:
      - Border pemisah tipis `border-left: 1px solid rgba(255, 255, 255, 0.15)` dengan padding-left `2.25rem`.

---

### Fitur 3: Adaptasi Kontekstual Data Peserta (SMK vs Mahasiswa)
* **Logika Domain Bisnis**:
  - Kolom `jenjang` pada `peserta_profil` bertipe enum `JenjangEnum` dengan nilai:
    - `JenjangEnum::Smk` (`'smk'`)
    - `JenjangEnum::D3` (`'d3'`)
    - `JenjangEnum::S1` (`'s1'`)
  - **Skenario 1: Peserta SMK (`jenjang === JenjangEnum::Smk`):**
    - Peserta berstatus siswa sekolah menengah kejuruan.
    - **Label Bidang**: Tampilkan `KOMPETENSI KEAHLIAN` atau `JURUSAN` (contoh: `TKJ`). Jangan gunakan istilah "Program Studi".
    - **Label Nomor Induk**: Tampilkan `NISN` (Nomor Induk Siswa Nasional). Jangan gunakan istilah "NIM".
    - **Catatan Pembimbing Asal**:
      - Jika `$profil?->nama_pembimbing_sekolah` terisi:
        `Guru Pembimbing: {{ $profil->nama_pembimbing_sekolah }}`
      - Jika belum terisi:
        `Data guru pembimbing sekolah belum diisi.`
      - **DILARANG MENAMPILKAN**: "Data dosen dan kampus belum diisi." karena siswa SMK tidak memiliki dosen maupun kampus!
  - **Skenario 2: Peserta Mahasiswa (`jenjang !== JenjangEnum::Smk`):**
    - Peserta berstatus mahasiswa perguruan tinggi.
    - **Label Bidang**: Tampilkan `PROGRAM STUDI` (contoh: `Teknik Informatika`).
    - **Label Nomor Induk**: Tampilkan `NIM` (Nomor Induk Mahasiswa).
    - **Catatan Dosen Pembimbing**:
      - Jika `$profil?->nama_pembimbing_sekolah` terisi:
        `Dosen Pembimbing: {{ $profil->nama_pembimbing_sekolah }}`
      - Jika belum terisi:
        `Data dosen pembimbing kampus belum diisi.`

---

## 3. 🎨 TOKEN WARNA & SPESIFIKASI CSS RESMI

Semua perubahan styling wajib menggunakan token warna terstandarisasi berikut:

| Token / Nilai | Kode HEX / RGBA | Penerapan Komponen |
|---|---|---|
| `--mentee-amber` | **`#F59E0B`** | Dot selesai, ring avatar inisial, tombol tugas, judul DATA PESERTA, kartu periode magang. |
| `--mentee-pill-bg` | **`rgba(255, 255, 255, 0.14)`** | Background default baris kapsul legenda progres tugas (`.donut-legend-pill`). |
| `--mentee-pill-hover` | **`rgba(255, 255, 255, 0.24)`** | Background state hover kapsul legenda progres tugas (`.donut-legend-pill:hover`). |
| `--mentee-kpi-blue` | **`#28487A`** | Background kartu KPI PROGRES TUGAS. |
| `--mentee-navy-card` | **`#0F3875`** | Background kartu Hero Profil Mentee. |
| `--mentee-surface` | **`#FFFFFF`** | Teks label putih, dot dikerjakan, kartu logbook & kanban. |
| `--mentee-gray-dot` | **`#A0AEC0`** | Dot status belum pada kapsul progres tugas. |

---

## 4. 👨‍💻 PANDUAN KODE IMPLEMENTASI STEP-BY-STEP (UNTUK JUNIOR DEV / AI)

### Step 1: Penyesuaian Markup Blade (`resources/views/pembimbing/peserta/show.blade.php`)

Ganti blok Hero Card dan KPI Progres Tugas di Blade menjadi:

```blade
{{-- 2. Hero Card Profil Mentee (Navy Blue Card #0F3875) --}}
@php
    $profil = $penempatan->peserta?->profil;
    $isSmk = $profil?->jenjang === \App\Enums\JenjangEnum::Smk;
@endphp
<div class="mentee-hero-card">
    <div class="mentee-hero-left">
        <div class="mentee-avatar-gold-ring">
            <span>{{ strtoupper(substr($penempatan->peserta?->nama ?? 'P', 0, 2)) }}</span>
        </div>
        <div class="mentee-hero-identity">
            <h1 class="mentee-hero-name">{{ $penempatan->peserta?->nama }}</h1>
            <p class="mentee-hero-institution">{{ $profil?->institusi ?? ($isSmk ? 'Sekolah Belum Dilengkapi' : 'Institusi Belum Dilengkapi') }}</p>
            <div class="mentee-badges-row">
                <span class="badge-status-pill badge-status-aktif">
                    {{ ucfirst($penempatan->status->value) }}
                </span>
                <span class="badge-status-pill badge-jalur-outline">
                    Jalur {{ ucfirst($penempatan->jalur?->value ?? 'Umum') }}
                </span>
            </div>
            <div class="mentee-action-buttons-row">
                <a href="{{ route('pembimbing.logbook.index', ['masuk' => $penempatan->id]) }}" class="btn-pill-action btn-pill-logbook" title="Lihat logbook harian mentee">
                    Logbook
                </a>
                <a href="{{ route('pembimbing.tugas.index', ['masuk' => $penempatan->id]) }}" class="btn-pill-action btn-pill-tugas" title="Lihat tugas mentee">
                    Tugas
                </a>
            </div>
        </div>
    </div>

    <div class="mentee-hero-right">
        <div class="data-peserta-header">DATA PESERTA</div>
        <div class="data-peserta-grid">
            <div class="data-peserta-cell">
                <span class="data-peserta-label">EMAIL</span>
                <span class="data-peserta-val" title="{{ $penempatan->peserta?->email }}">{{ $penempatan->peserta?->email ?? '-' }}</span>
            </div>
            <div class="data-peserta-cell">
                <span class="data-peserta-label">TELEPON / WA</span>
                <span class="data-peserta-val">{{ $penempatan->peserta?->no_telp ?? $profil?->no_hp ?? '-' }}</span>
            </div>
            <div class="data-peserta-cell">
                <span class="data-peserta-label">{{ $isSmk ? 'KOMPETENSI KEAHLIAN' : 'PROGRAM STUDI' }}</span>
                <span class="data-peserta-val">{{ $profil?->jurusan?->nama_jurusan ?? '-' }}</span>
            </div>
            <div class="data-peserta-cell">
                <span class="data-peserta-label">{{ $isSmk ? 'NISN' : 'NIM' }}</span>
                <span class="data-peserta-val">{{ $profil?->nim_nisn ?? '-' }}</span>
            </div>
            <div class="data-peserta-cell cell-full-width">
                <span class="data-peserta-label">BIDANG</span>
                <span class="data-peserta-val">{{ $penempatan->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika' }}</span>
            </div>
        </div>
        <div class="data-peserta-dosen-note">
            @if($isSmk)
                {{ $profil?->nama_pembimbing_sekolah ? 'Guru Pembimbing: ' . $profil->nama_pembimbing_sekolah : 'Data guru pembimbing sekolah belum diisi.' }}
            @else
                {{ $profil?->nama_pembimbing_sekolah ? 'Dosen Pembimbing: ' . $profil->nama_pembimbing_sekolah : 'Data dosen pembimbing kampus belum diisi.' }}
            @endif
        </div>
    </div>
</div>

{{-- 3. Baris 3 Kartu Metrik Ringkasan --}}
<div class="mentee-kpi-summary-grid">
    {{-- KARTU 1: PROGRES TUGAS (DEEP BLUE DENGAN LEGENDA PILL HOVER) --}}
    <div class="kpi-card kpi-card-blue">
        <div class="kpi-card-title">PROGRES TUGAS</div>
        <div class="kpi-donut-layout">
            <div class="donut-chart-circle" style="--deg-selesai: {{ $degSelesai }}deg; --deg-proses: {{ $degProses }}deg;">
                <div class="donut-chart-inner">
                    <span class="donut-percentage-text">{{ $completionRate }}%</span>
                </div>
            </div>
            <div class="donut-chart-legends">
                <div class="donut-legend-pill" title="Tugas Selesai: {{ $tugasSelesaiCount }}">
                    <div class="legend-pill-left">
                        <span class="legend-dot-indicator dot-selesai"></span>
                        <span class="legend-pill-text">Selesai</span>
                    </div>
                    <span class="legend-pill-count">{{ $tugasSelesaiCount }}</span>
                </div>
                <div class="donut-legend-pill" title="Tugas Dikerjakan: {{ $tugasProsesCount }}">
                    <div class="legend-pill-left">
                        <span class="legend-dot-indicator dot-dikerjakan"></span>
                        <span class="legend-pill-text">Dikerjakan</span>
                    </div>
                    <span class="legend-pill-count">{{ $tugasProsesCount }}</span>
                </div>
                <div class="donut-legend-pill" title="Tugas Belum Dikerjakan: {{ $tugasBelumCount }}">
                    <div class="legend-pill-left">
                        <span class="legend-dot-indicator dot-belum"></span>
                        <span class="legend-pill-text">Belum</span>
                    </div>
                    <span class="legend-pill-count">{{ $tugasBelumCount }}</span>
                </div>
            </div>
        </div>
    </div>
```

---

### Step 2: Penyesuaian Stylesheet CSS (`resources/css/pages/pembimbing/peserta-show.css`)

Perbarui bagian styling legenda donat dan proporsi biodata:

```css
/* Legenda Donut Kapsul Horizontal dengan Hover */
.donut-chart-legends {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
    flex: 1;
}

.donut-legend-pill {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: rgba(255, 255, 255, 0.14);
    border-radius: 12px;
    padding: 0.5rem 0.85rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: default;
}

.donut-legend-pill:hover {
    background-color: rgba(255, 255, 255, 0.24);
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
}

.legend-pill-left {
    display: flex;
    align-items: center;
    gap: 0.55rem;
}

.legend-dot-indicator {
    width: 9px;
    height: 9px;
    min-width: 9px;
    border-radius: 50%;
    display: inline-block;
}

.legend-dot-indicator.dot-selesai {
    background-color: var(--mentee-amber); /* #F59E0B */
}

.legend-dot-indicator.dot-dikerjakan {
    background-color: #FFFFFF;
}

.legend-dot-indicator.dot-belum {
    background-color: #A0AEC0;
}

.legend-pill-text {
    color: #FFFFFF;
    font-weight: 700;
    font-size: 0.8125rem;
    letter-spacing: 0.01em;
}

.legend-pill-count {
    color: #FFFFFF;
    font-weight: 800;
    font-size: 0.875rem;
}

/* Proporsi & Keseimbangan Kartu Hero */
.mentee-hero-card {
    background-color: var(--mentee-navy-card);
    border-radius: 16px;
    padding: 2rem 2.25rem;
    color: #FFFFFF;
    display: grid;
    grid-template-columns: 1fr 1.25fr;
    gap: 2.25rem;
    box-shadow: 0 10px 25px -5px rgba(15, 56, 117, 0.35);
    margin-bottom: 1.25rem;
    align-items: center;
}

.mentee-hero-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.mentee-avatar-gold-ring {
    width: 84px;
    height: 84px;
    min-width: 84px;
    border-radius: 50%;
    background-color: var(--mentee-navy-darker);
    border: 3.5px solid var(--mentee-amber);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--mentee-amber);
    font-size: 1.875rem;
    font-weight: 800;
    box-shadow: 0 0 16px rgba(245, 158, 11, 0.28);
}
```

---

## 5. 🛡️ ATURAN ARSITEKTURAL & ANTI-REGRESI

1. **LARANGAN CSS/JS INLINE DI BLADE**: Semua kode styling masuk ke file `peserta-show.css`. Dilarang menggunakan `<style>` atau inline event handlers di Blade.
2. **KONSISTENSI WARNA KUNING**: Seluruh warna kuning tetap mengacu pada `--mentee-amber: #F59E0B`.
3. **UJI SUITE KOMPREHENSIF**: Perubahan harus memvalidasi jenjang SMK dan jenjang Mahasiswa pada feature test.

---

## 6. 🚦 QUALITY GATE & PROTOKOL VERIFIKASI

Sebelum task dinyatakan selesai, jalankan urutan pengujian:
1. `npm run build` (Wajib sukses).
2. `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/PembimbingPortalStandardizationTest.php` (Wajib 100% PASS).
3. `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=Pembimbing` (Wajib 128 tests PASS).
4. **Verifikasi Visual**:
   - Legenda donat tampil dalam 3 baris kapsul horizontal dengan efek hover.
   - Kolom kiri biodata mengisi card secara proporsional.
   - Peserta SMK menampilkan label `KOMPETENSI KEAHLIAN` dan `NISN`, serta tanpa keterangan "kampus/dosen".

