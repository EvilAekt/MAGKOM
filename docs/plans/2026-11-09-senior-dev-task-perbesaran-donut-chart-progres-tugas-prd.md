# TASK BRIEF SENIOR DEVELOPER: PANDUAN IMPLEMENTASI PERBESARAN DONUT CHART PROGRES TUGAS SESUAI PROPORSI KARTU KPI (PROFIL 360° MENTEE)

> **Dari:** Senior Developer & Lead Software Architect  
> **Kepada:** Junior Developer / AI Model Pelaksana (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Kabupaten Sleman)  
> **Status:** READY FOR REVIEW & IMPLEMENTATION  
> **Tingkat Kesulitan:** Rendah (CSS Data Visualization Scaling, Radial Alignment, Visual Balance)  
> **Dokumentasi Terkait:** `AGENTS.md`, `.agents/rules/AGENTS.md`, `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`  
> **Target Endpoint & Rute:** `GET /pembimbing/peserta/{id}` (`route('pembimbing.peserta.show')`)  
> **Berkas Acuan Desain Resmi:**  
> 1. `docs/plans/2026-11-09-profil-360-crop-donut-chart-50.png` (Crop Acuan Donut Chart Progres Tugas)  
> 2. `docs/plans/2026-11-09-profil-360-crop-avatar-circle-na.png` (Crop Acuan Komparasi Elemen Sirkular Avatar NA)  
> 3. `docs/plans/2026-11-09-profil-360-v3-master.jpg` (Desain Target Mockup Halaman Master)

---

## 1. 🔍 EXECUTIVE SUMMARY & ANALISIS MASALAH

Berdasarkan review visual terhadap crop donut chart yang diunggah pengguna:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 🔴 MASALAH: UKURAN DONUT CHART TERLALU KECIL & TIDAK PROPORSIONAL DENGAN KARTU KPI     │
│    - Saat ini .donut-chart-circle berukuran hanya 96px x 96px dengan diameter lubang   │
│      tengah 64px x 64px dan teks persentase 1.375rem (22px).                           │
│    - Di sebelah kanan chart terdapat 3 baris kapsul legenda horizontal (Selesai,       │
│      Dikerjakan, Belum) dengan total tinggi gabungan ~132px.                           │
│    - Akibatnya, donut chart terlihat "tenggelam", kekecilan, dan menyisakan ruang      │
│      kosong yang canggung di dalam kartu KPI PROGRES TUGAS (.kpi-card-blue).           │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 💡 SOLUSI ARSITEKTUR: SCALING UP PROPORSI DONUT CHART (+29%)                           │
│    - Menaikkan diameter luar chart dari 96px menjadi 124px.                            │
│    - Menaikkan diameter lubang dalam dari 64px menjadi 84px (ketebalan ring = 20px).   │
│    - Menaikkan tipografi angka persentase tengah dari 1.375rem (22px) menjadi 1.75rem  │
│      (28px) tebal (font-weight: 800) agar menjadi titik fokus visual (visual anchor).  │
│    - Menyelaraskan tinggi chart (124px) dengan tinggi 3 baris kapsul legenda (132px). │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. 📐 MATRIKS KOMPARASI SPESIFIKASI (BEFORE VS AFTER)

Semua developer junior dan AI pelaksana wajib menerapkan parameter ukuran berikut:

| Parameter Komponen | Nilai Saat Ini (Current) | Nilai Target Rencana (Planned Target) | Manfaat Desain & UX |
|---|---|---|---|
| **Diameter Luar (`.donut-chart-circle`)** | `96px` x `96px` | **`124px` x `124px`** | Donut chart tampil gagah, mengisi ruang kartu secara proporsional. |
| **Lebar Minimal (`min-width`)** | `96px` | **`124px`** | Mencegah chart gepeng atau terkompresi saat layar mengecil. |
| **Diameter Lubang Dalam (`.donut-chart-inner`)**| `64px` x `64px` | **`84px` x `84px`** | Memberikan ruang yang lega bagi angka persentase. |
| **Ketebalan Ring Warna (*Ring Stroke*)** | `16px` | **`20px`** | Segmen warna (Kuning, Putih, Abu-abu) lebih tebal dan kontras. |
| **Ukuran Teks Persentase (`.donut-percentage-text`)** | `1.375rem` (22px) | **`1.75rem`** (28px, font-weight 800) | Angka persentase penyelesaian tugas terbaca sangat jelas sekilas pandang. |
| **Gap Antara Chart & Legenda (`gap`)** | `1.25rem` (20px) | **`1.5rem`** (24px) | Pemisahan elemen visual chart dan teks legenda lebih lapang. |
| **Keseimbangan Vertikal (`align-items`)** | `center` | **`center`** | Posisi chart dan 3 baris kapsul legenda sejajar presisi di tengah kartu. |

---

## 3. 🎨 BLUEPRINT VISUAL & PROPORSI KARTU PROGRES TUGAS

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ KARTU 1: PROGRES TUGAS (#28487A) ── Tinggi Kartu Seimbang (~200px) ────────────────────┐
│                                                                                        │
│ PROGRES TUGAS (C7D2FE, 0.75rem, Font-weight: 800)                                      │
│                                                                                        │
│ ┌─ DONUT CHART (124px x 124px) ─┐   ┌─ 3 BARIS KAPSUL LEGENDA HORIZONTAL (T: ~132px) ──┐│
│ │         Segmen Kuning         │   │ ┌─ [● Kuning #F59E0B] Selesai ───────── [ 3 ] ──┐││
│ │         #F59E0B               │   │ │  (Pad: 0.5rem 0.85rem, Radius: 12px)          │││
│ │       ┌─────────────┐         │   │ └───────────────────────────────────────────────┘││
│ │       │             │         │   │ ┌─ [● Putih #FFFFFF]  Dikerjakan ────── [ 2 ] ──┐││
│ │ Segmen│    50%      │ Segmen  │   │ │  (Hover: geser +4px, highlight terang)        │││
│ │ Abu   │ (28px Bold) │ Putih   │   │ └───────────────────────────────────────────────┘││
│ │       │             │         │   │ ┌─ [● Abu-abu #A0AEC0] Belum ────────── [ 1 ] ──┐││
│ │       └─────────────┘         │   │ │  (Teks & Angka Kontras Tinggi)                │││
│ │         Inner Hole: 84px      │   │ └───────────────────────────────────────────────┘││
│ └───────────────────────────────┘   └──────────────────────────────────────────────────┘│
│                                                                                        │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. 🛠️ INSTRUKSI KODE DETAIL UNTUK JUNIOR DEVELOPER & AI MODEL

Semua modifikasi styling dilakukan terpusat pada file CSS tanpa perlu merusak struktur Blade:

### Berkas: `resources/css/pages/pembimbing/peserta-show.css`

Cari blok `.kpi-donut-layout`, `.donut-chart-circle`, `.donut-chart-inner`, dan `.donut-percentage-text`, kemudian sesuaikan menjadi:

```css
/* ==========================================================================
   4. Baris 3 Kartu Metrik Ringkasan - KARTU 1: PROGRES TUGAS
   ========================================================================== */
.kpi-donut-layout {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-top: auto;
    margin-bottom: auto;
    padding: 0.35rem 0;
}

.donut-chart-circle {
    width: 124px;
    height: 124px;
    min-width: 124px;
    border-radius: 50%;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    background: conic-gradient(
        var(--mentee-amber) 0deg var(--deg-selesai, 180deg),
        #FFFFFF var(--deg-selesai, 180deg) var(--deg-proses, 300deg),
        var(--mentee-kpi-badge) var(--deg-proses, 300deg) 360deg
    );
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
    flex-shrink: 0;
}

.donut-chart-inner {
    width: 84px;
    height: 84px;
    border-radius: 50%;
    background-color: var(--mentee-kpi-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15);
}

.donut-percentage-text {
    font-size: 1.75rem;
    font-weight: 800;
    color: #FFFFFF;
    line-height: 1;
    letter-spacing: -0.02em;
}
```

---

## 5. 🚦 PROTOKOL QUALITY GATE & VERIFIKASI

1. **Build Aset Frontend (Vite)**:
   ```bash
   npm run build
   ```
   *Ekspektasi:* Kompilasi CSS sukses tanpa warning.
2. **Feature Testing (PHPUnit)**:
   ```powershell
   & "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/PembimbingPortalStandardizationTest.php
   ```
   *Ekspektasi:* Seluruh 5 feature tests lolos 100%.
3. **Verifikasi Visual (Acceptance Checklist)**:
   - [ ] Donut chart berdiameter 124px x 124px dan tidak gepeng.
   - [ ] Angka persentase tengah berukuran 1.75rem (28px) tebal dan berpusat tepat di tengah lubang 84px.
   - [ ] Tinggi donut chart (124px) sejajar dan seimbang sempurna dengan 3 baris kapsul legenda di sebelahnya.
   - [ ] Ketebalan ring donut (20px) menampilkan warna kuning `#F59E0B`, putih, dan abu-abu secara jelas.

