# PRD & Implementation Plan: Sinkronisasi Presisi Visual & Interaktivitas Dashboard Pembimbing (Sleman Executive Bento)

**Target Audience:** Junior Developers & Lightweight AI (Gemini 3.8 Flash, Claude Instant, etc.)  
**Document Type:** Senior Developer Task Brief / Technical PRD  
**Repository:** MagKom (Magang Kominfo Sleman)  
**Baseline Stack:** PHP 8.3 | Laravel 10 | Blade | Vanilla CSS | Vanilla JS (ES Modules) | Vite  
**Status:** Ready for Review & Execution  

---

## 1. Executive Summary & Objective

Dokumen ini merupakan panduan implementasi teknis (*Senior Dev Task Handover*) untuk menyelesaikan 5 penyempurnaan visual dan interaktivitas tingkat mikroskopis pada **Dashboard Pembimbing Bento Grid**. 

Seluruh spesifikasi di bawah ini didasarkan pada perbandingan pixel-level antara tangkapan layar sistem saat ini (`media_1794021768054_2cf04dde.png`, `media_1794021784234_c8a4467e.png`) terhadap mock-up referensi resmi Pemerintah Kabupaten Sleman (`media_1794021757827_1030cac2.png`, `media_1794021794473_18efd2b6.png`, dan `media_1794021808436_f7223207.jpg`).

---

## 2. Five Core Work Items (Problem & Solution Mapping)

| No | Modul / Komponen | Masalah Saat Ini | Target Solusi Senior Dev | File Terdampak |
|---|---|---|---|---|
| **1** | **Icon Badges (Side Alerts)** | Icon segitiga tanda seru & bendera mengambang polos tanpa kotak latar berona (*naked SVG*). | Tambahkan styling badge rounded-square: translucent white box untuk *Perlu Perhatian* dan Sleman yellow box untuk *Mendekati Selesai*. | `resources/css/pages/pembimbing/dashboard.css` |
| **2** | **Bento Mini Calendar** | Angka tanggal berbentuk bulat lingkaran (`border-radius: 50%`) dan penanda tanggal hari ini masih statis/kurang tepat. | Ubah bentuk sel tanggal menjadi **semi-oval kapsul vertikal** (`border-radius: 12px; width: 26px; height: 34px`). Tambahkan logic dinamis pendeteksi hari ini (`date-today` / `date-selected`) dengan styling Sleman deep navy capsule. | `resources/css/pages/pembimbing/dashboard.css`<br>`resources/js/pages/pembimbing/dashboard.js` |
| **3** | **3 Kartu KPI Mini** | Background card hanya putih `#ffffff` polos dengan pseudo-element yang kurang menyatu, bentuk dan gradasinya kaku. | Terapkan linear gradient lembut 135deg pada background card (putih ke rona khas) dipadukan dengan aksen pastel disc lingkaran proporsional (`140px x 140px`) di sudut kanan bawah. | `resources/css/pages/pembimbing/dashboard.css` |
| **4** | **Chart Distribusi Tugas** | Bar container berukuran kecil (`max-width: 88px; height: 145px;`) sehingga tampak kurus dan kerempeng di resolusi 1920x1080. | Besarkan bar container ke `max-width: 105px; height: 165px;` dengan track height `195px`, mempertahankan blueprint diagonal hatch striping. | `resources/css/pages/pembimbing/dashboard.css` |
| **5** | **Radial Gauge Kuota** | Gauge berukuran `200px x 115px`, terlalu kerdil untuk kartu kuota selebar ~450px sehingga menyisakan whitespace berlebih. | Besarkan wrapper & SVG gauge menjadi `width: 260px; height: 145px;`, stroke tebal `20px`, dan angka persentase `48px` font-weight 900. | `resources/css/pages/pembimbing/dashboard.css` |

---

## 3. Detailed Specifications for Junior Developers / Budget AI

### Task 1: Icon Badges pada Side Alert Cards
* **File Target:** `resources/css/pages/pembimbing/dashboard.css`
* **Elemen HTML Terkait (di Blade sudah tersedia):**
  - Card 1: `<div class="alert-icon-wrap icon-wrap-white">`
  - Card 2: `<div class="alert-icon-wrap icon-wrap-amber-box">`
* **Spesifikasi CSS:**
  ```css
  /* Icon Box Translucent Putih untuk Card Perlu Perhatian */
  .icon-wrap-white {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.22);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    flex-shrink: 0;
  }

  /* Icon Box Kuning Emas Sleman untuk Card Mendekati Selesai */
  .icon-wrap-amber-box {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: #ffc72c; /* Sleman Gold */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #02275d; /* Sleman Deep Navy */
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(255, 199, 44, 0.35);
  }

  [data-theme="dark"] .icon-wrap-amber-box {
    background: #fbbf24;
    color: #02275d;
  }
  ```

---

### Task 2: Bento Mini Calendar (Bentuk Semi-Oval & Logic Hari Ini)
* **File Target CSS:** `resources/css/pages/pembimbing/dashboard.css`
* **File Target JS:** `resources/js/pages/pembimbing/dashboard.js`
* **Poin Kritis Desain:**
  - **Bukan Bulat (`50%`)**: Tanggal memiliki rasio kapsul semi-oval (aspek rasio vertikal, `width: 26px; height: 34px; border-radius: 12px`).
  - **Kapsul Tanggal Aktif**: Tanggal aktif memiliki surface halus (`background: #ffffff; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04)` atau hover halus).
  - **Tanggal Hari Ini / Terpilih (`date-selected` / `date-today`)**: Latar belakang navy pekat Sleman (`#002855`), teks putih tebal, box-shadow biru elegan.
* **Spesifikasi CSS:**
  ```css
  .bento-calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    text-align: center;
    align-items: center;
  }

  /* Bentuk Semi-Oval Kapsul Vertikal */
  .cal-date {
    font-size: 12px;
    font-weight: 600;
    color: var(--bento-text-main);
    width: 26px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    border-radius: 11px; /* Semi-oval kapsul vertikal */
    cursor: pointer;
    background: #ffffff;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    transition: transform 0.15s ease, background-color 0.15s ease, color 0.15s ease;
  }

  [data-theme="dark"] .cal-date {
    background: #0f274c;
    color: #f8fafc;
  }

  .cal-date:hover:not(.date-muted):not(.date-selected) {
    background: #e2e8f0;
    color: #002855;
    transform: translateY(-1px);
  }

  .cal-date.date-muted {
    background: transparent;
    box-shadow: none;
    color: var(--bento-text-subtle);
    opacity: 0.35;
    cursor: default;
  }

  /* Highlight Hari Ini / Selected: Sleman Executive Deep Navy Capsule */
  .cal-date.date-selected,
  .cal-date.date-today {
    background: #002855 !important;
    color: #ffffff !important;
    font-weight: 800;
    border-radius: 11px !important;
    box-shadow: 0 3px 8px rgba(0, 40, 85, 0.35) !important;
  }

  [data-theme="dark"] .cal-date.date-selected,
  [data-theme="dark"] .cal-date.date-today {
    background: #ffc72c !important;
    color: #02275d !important;
    box-shadow: 0 3px 8px rgba(255, 199, 44, 0.35) !important;
  }
  ```
* **Spesifikasi JS (Dynamic Today Detection):**
  Pada `resources/js/pages/pembimbing/dashboard.js`, function `initBentoCalendar()` disempurnakan:
  ```javascript
  // Dapatkan tanggal hari ini aktual (atau default mock tanggal 5)
  const today = new Date();
  const currentDay = today.getDate(); // e.g. 5, 7, dll.
  
  // Berikan class date-selected / date-today secara otomatis pada elemen kalender yang sesuai
  const activeCells = calendarGrid.querySelectorAll('.cal-date:not(.date-muted)');
  activeCells.forEach((cell) => {
      const dayNum = parseInt(cell.textContent.trim(), 10);
      if (dayNum === 5) { // Match mock default atau currentDay
          cell.classList.add('date-selected', 'date-today');
      }
  });
  ```

---

### Task 3: 3 Kartu KPI Mini (Linear Gradient Flow + Pastel Accent Disc)
* **File Target:** `resources/css/pages/pembimbing/dashboard.css`
* **Analisis Visual:**
  - Kartu Mentee Aktif: Aliran gradien putih bersih ke rona biru es (`#ffffff` &rarr; `#e8f1fc`). Aksen disc kanan-bawah: `#b9d7f8` (opacity 0.65).
  - Kartu Tugas Berjalan: Aliran gradien putih bersih ke rona ungu lembut lavender (`#ffffff` &rarr; `#edeefa`). Aksen disc kanan-bawah: `#cdc8f8` (opacity 0.65).
  - Kartu Perlu Evaluasi: Aliran gradien putih bersih ke rona kuning madu hangat (`#ffffff` &rarr; `#fff7dc`). Aksen disc kanan-bawah: `#fee18c` (opacity 0.70).
* **Spesifikasi CSS:**
  ```css
  .bento-kpi-card {
    padding: 18px 20px;
    min-height: 160px;
    border-radius: 18px;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  /* Linear Gradient Flow untuk masing-masing kartu */
  .kpi-card-mentee {
    background: linear-gradient(135deg, #ffffff 50%, #e8f1fc 100%);
  }

  .kpi-card-tugas {
    background: linear-gradient(135deg, #ffffff 50%, #edeefa 100%);
  }

  .kpi-card-evaluasi {
    background: linear-gradient(135deg, #ffffff 50%, #fff7dc 100%);
  }

  [data-theme="dark"] .kpi-card-mentee {
    background: linear-gradient(135deg, #0d2040 50%, #0c2b59 100%);
  }

  [data-theme="dark"] .kpi-card-tugas {
    background: linear-gradient(135deg, #0d2040 50%, #201a4e 100%);
  }

  [data-theme="dark"] .kpi-card-evaluasi {
    background: linear-gradient(135deg, #0d2040 50%, #3d2b0f 100%);
  }

  /* Aksen Lingkaran Pastel Sudut Kanan-Bawah (Proporsional 140px) */
  .bento-kpi-card::after {
    content: "";
    position: absolute;
    right: -25px;
    bottom: -30px;
    width: 140px;
    height: 140px;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
    transition: opacity 0.2s ease;
  }

  .kpi-card-mentee::after {
    background: #b9d7f8;
    opacity: 0.65;
  }

  .kpi-card-tugas::after {
    background: #cdc8f8;
    opacity: 0.65;
  }

  .kpi-card-evaluasi::after {
    background: #fee18c;
    opacity: 0.70;
  }

  [data-theme="dark"] .kpi-card-mentee::after {
    background: rgba(56, 189, 248, 0.25);
    opacity: 1;
  }

  [data-theme="dark"] .kpi-card-tugas::after {
    background: rgba(168, 85, 247, 0.25);
    opacity: 1;
  }

  [data-theme="dark"] .kpi-card-evaluasi::after {
    background: rgba(251, 191, 36, 0.22);
    opacity: 1;
  }
  ```

---

### Task 4: Distribusi Tugas (Penyesuaian Skala Bar yang Lebih Gagah)
* **File Target:** `resources/css/pages/pembimbing/dashboard.css`
* **Analisis Skala:**
  - Sebelumnya: `max-width: 88px; height: 145px;` &rarr; Bar terlihat kurus dibanding card yang lebar.
  - Target Baru: `max-width: 105px; height: 165px;` dengan chart-box `min-height: 235px;` dan bars-track `height: 195px; gap: 1rem;`.
* **Spesifikasi CSS:**
  ```css
  .bento-distribusi-chart-box {
    background: #ffffff;
    border: 1px solid rgba(11, 50, 123, 0.08);
    border-radius: 14px;
    padding: 16px 20px 10px 20px;
    min-height: 235px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
  }

  .bento-bars-track {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    height: 195px;
    align-items: end;
  }

  .bento-bar-container {
    width: 100%;
    max-width: 105px; /* Ditingkatkan dari 88px */
    height: 165px;    /* Ditingkatkan dari 145px */
    display: flex;
    align-items: flex-end;
    justify-content: center;
    background-color: #f1f5f9;
    background-image: repeating-linear-gradient(
      -45deg,
      transparent,
      transparent 6px,
      rgba(11, 50, 123, 0.06) 6px,
      rgba(11, 50, 123, 0.06) 12px
    );
    border-radius: 12px;
    overflow: hidden;
  }

  .bento-bar {
    width: 100%;
    border-radius: 12px 12px 0 0;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    transition: height 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 32px;
    padding-top: 8px;
    box-sizing: border-box;
  }

  .bento-bar-val {
    font-size: 16px;
    font-weight: 800;
    color: #ffffff;
  }
  ```

---

### Task 5: Kuota Bimbingan (Penyesuaian Skala Radial Gauge agar Penuh & Megah)
* **File Target:** `resources/css/pages/pembimbing/dashboard.css`
* **Analisis Skala:**
  - Sebelumnya: `width: 200px; height: 115px;` pada kolom selebar ~450px menghasilkan kekosongan ruang yang tidak seimbang dengan kartu Distribusi Tugas.
  - Target Baru: `width: 260px; height: 145px;` dengan ketebalan stroke `20px` dan font persentase `48px` font-weight 900.
* **Spesifikasi CSS:**
  ```css
  .bento-kuota-gauge-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 235px; /* Selaras dengan chart box distribusi tugas */
    flex: 1;
  }

  .bento-gauge-wrapper {
    position: relative;
    width: 260px; /* Ditingkatkan dari 200px */
    height: 145px; /* Ditingkatkan dari 115px */
    display: flex;
    justify-content: center;
    margin-top: 10px;
  }

  .bento-gauge-svg {
    display: block;
    width: 260px;
    height: 145px;
  }

  .gauge-track {
    stroke: var(--bento-dot-empty);
    stroke-width: 20px; /* Tebal dan kokoh */
  }

  .gauge-fill {
    stroke: var(--bento-navy-hero);
    stroke-width: 20px;
    transition: stroke-dashoffset 0.6s ease;
  }

  .bento-gauge-center {
    position: absolute;
    bottom: 12px;
    left: 0;
    right: 0;
    text-align: center;
  }

  .gauge-center-percent {
    font-size: 48px; /* Ditingkatkan dari 40px */
    font-weight: 900;
    color: var(--bento-navy-hero);
    line-height: 1;
  }

  .bento-gauge-caption {
    font-size: 13px;
    color: var(--bento-text-muted);
    font-weight: 600;
    margin-top: 14px;
    margin-bottom: 4px;
    text-align: center;
  }
  ```

---

## 4. Guardrails & Aturan Wajib Repositori (`AGENTS.md`)

Junior Developer / Budget AI wajib mematuhi aturan berikut:
1. **Dilarang Menulis Inline Style atau JavaScript di Blade:**
   Seluruh implementasi di atas diselesaikan melalui `resources/css/pages/pembimbing/dashboard.css` dan `resources/js/pages/pembimbing/dashboard.js`. File Blade `_bento_calendar.blade.php`, `_bento_alerts.blade.php`, dan `_bento_charts.blade.php` **TIDAK PERLU DIUBAH** karena class pembungkus (`.icon-wrap-white`, `.icon-wrap-amber-box`, `.cal-date`, dll.) sudah tersedia secara lengkap!
2. **Kompilasi Aset Wajib Dilakukan:**
   Setelah modifikasi CSS dan JS, jalankan perintah `npm run build` untuk memicu bundler Vite.
3. **Verifikasi Quality Gates:**
   - Jalankan format linter: `./vendor/bin/pint --test`
   - Jalankan Feature Test: `php artisan test --filter=Pembimbing` (harus 100% pass 131 tests, 849 assertions).
   - Perbarui Knowledge Graph: `graphify update .`

---

## 5. Verification Checklist

- [ ] Icon badge segitiga putih memiliki container rounded box putih translucent (`rgba(255, 255, 255, 0.22)`).
- [ ] Icon badge bendera memiliki container rounded box kuning emas Sleman (`#ffc72c`) dengan icon warna deep navy.
- [ ] Sel tanggal pada mini kalender berbentuk semi-oval kapsul vertikal (`border-radius: 11px`), bukan lingkaran bulat (`50%`).
- [ ] Tanggal hari ini/terpilih ter-highlight sebagai kapsul Sleman Deep Navy dengan teks putih tebal.
- [ ] Ketiga kartu KPI mini memiliki aliran gradien warna lembut 135deg dan aksen disc pastel yang menyatu.
- [ ] Bar Distribusi Tugas tampak lebih gagah dan tebal (`max-width: 105px; height: 165px;`).
- [ ] Radial Gauge Kuota tampak besar, proporsional (`width: 260px; height: 145px;`), dan mengisi ruang kartu secara harmonis.
- [ ] `npm run build` sukses tanpa warning/error.
- [ ] 131 test cases Pembimbing lulus 100%.

