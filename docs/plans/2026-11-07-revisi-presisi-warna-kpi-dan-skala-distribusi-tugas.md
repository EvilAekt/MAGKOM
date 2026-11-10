# Rencana Kerja: Presisi Warna Kartu KPI & Penskalaan Bar Distribusi Tugas (Resolusi 1920x1080)

**Dokumen Acuan**: `docs/plans/2026-11-07-revisi-presisi-warna-kpi-dan-skala-distribusi-tugas.md`  
**Target Rilis**: Dashboard Pembimbing - MagKom Sleman Executive Bento Grid  
**Resolusi Acuan**: Full HD (1920x1080) & Standar Laptop (1366x768 - 1440x900)  
**Status**: 📋 *Menunggu Persetujuan Pengguna (Awaiting User Approval)*

---

## 1. Analisis Masalah Visual & Komparasi Layar

Berdasarkan 4 gambar perbandingan yang diunggah oleh pengguna:
- **Image 1 (Referensi Mockup KPI Cards)** vs **Image 2 (Tampilan Saat Ini)**:
  1. *Bentuk Aksen Sudut*: Pada Image 1, sudut kanan-bawah kartu bukan gradien kabur (*blurry gradient*) yang mencemari seluruh bidang kartu, melainkan **elemen lingkaran geometris lembut (*soft circle disc*)** berdiameter ~150px dengan batas lengkung yang jelas dan elegan di pojok kanan bawah. Permukaan kartu selebihnya tetap putih bersih murni (`#ffffff`).
  2. *Proporsi Kartu*: Pada Image 1, kartu memiliki rasio aspek seimbang dan tinggi bernapas (`min-height: 220px; padding: 24px;`). Pada Image 2 di layar 1920x1080, kartu ceper (`height: ~140px`) dan melebar secara horizontal, menyebabkan gradien menyebar menjadi garis kabur yang mengaburkan teks.
  3. *Ikon & Warna*: Kotak ikon pada Image 1 berukuran `48px x 48px` dengan `border-radius: 14px` solid (Navy `#003566` untuk Mentee, Royal/Indigo `#2544a8` untuk Tugas).
- **Image 3 (Referensi Mockup Distribusi Tugas)** vs **Image 4 (Tampilan Saat Ini di 1920x1080)**:
  1. *Ketinggian Chart*: Pada Image 3, kartu Distribusi Tugas berpostur tinggi dan gagah (`min-height: 420px; height: 320px` track chart). Pada Image 4, kartu ceper (`min-height: 260px; height: 145px`).
  2. *Lebar Batang Bar*: Pada Image 4, kolom bar terkunci pada `max-width: 64px;`. Pada resolusi 1920x1080 di mana kartu berlebar ~720px, 4 bar selebar 60px hanya memakan total 240px, menyisakan ~480px ruang kosong melompong (*dead white space*). Bar tampak kurus dan kecil seperti korek api.
  3. *Proporsi Arsir Diagonal (Striped CAD Track)*: Pada Image 3, di atas bar rendah (seperti Review = 3, Terlambat = 2), pola garis arsir diagonal membubung tinggi (~200px arsir), menciptakan kontras visual arsitektural yang sangat kuat dan presisi.

---

## 2. Asumsi Teknis & Parameter Desain

1. **Aturan Repositori & Stack**:
   - Mematuhi penuh aturan `.agents/rules/AGENTS.md` (PHP 8.3, Laravel 10, Blade read-only by default, CSS modular murni di `resources/css/pages/pembimbing/dashboard.css`, dikompilasi via Vite).
   - Seluruh perubahan tampilan diselesaikan melalui stylesheet CSS tanpa memodifikasi markup Blade.
2. **Kesesuaian Resolusi 1920x1080**:
   - Grid layout utama `.bento-layout-grid` dan baris tengah `.bento-mid-row` didesain responsif adaptif terhadap layar 1920px (Full HD) dengan penskalaan proporsional (*fluid clamped widths*).
   - Kartu pendamping *Kuota Bimbingan* diselaraskan tingginya dengan *Distribusi Tugas* agar keduanya sejajar rapi (*baseline alignment*).
3. **Dukungan Dark Mode**:
   - Pola lingkaran aksen sudut dan batang bar memiliki token Dark Mode terkalibrasi (`[data-theme="dark"]`).

---

## 3. Pendekatan Teknis (Opsi Terpilih)

**Pendekatan Terpilih: Pseudo-Element Discrete Circle + Fluid Clamped Columns (Opsi 1)**

### A. Rekayasa Kartu KPI (Mentee Aktif, Tugas Berjalan, Perlu Evaluasi)
- Mengubah background dasar kartu menjadi murni `#ffffff` (Dark: `#0d2040`).
- Menggantikan `radial-gradient` yang kabur dengan **pseudo-element `::after`** yang terpotong rapi oleh `overflow: hidden`:
  ```css
  .bento-kpi-card::after {
    content: "";
    position: absolute;
    right: -25px;
    bottom: -35px;
    width: 155px;
    height: 155px;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
  }
  .kpi-card-mentee::after {
    background: #dbe7f6; /* Soft Slate-Blue Disc */
  }
  .kpi-card-tugas::after {
    background: #e4e6f9; /* Soft Lavender-Indigo Disc */
  }
  .kpi-card-evaluasi::after {
    background: #fef0c7; /* Soft Golden-Amber Disc */
  }
  ```
- Menyelaraskan kartu KPI: `min-height: 220px; padding: 24px; border-radius: 20px;`.
- Ikon box: `width: 48px; height: 48px; border-radius: 14px;` dengan warna solid dan ikon putih.
- Tipografi: Angka besar dinaikkan ke `42px`, `font-weight: 800; color: #002855;`.

### B. Rekayasa Kartu Distribusi Tugas (Skala Resolusi 1920x1080)
- Ketinggian wadah kartu dinaikkan menjadi `min-height: 420px;` (padding: `24px 28px;`).
- Ketinggian wadah chart (`.bento-distribusi-chart-box`) dinaikkan menjadi `min-height: 330px; padding: 22px 24px 14px;`.
- Jalur bar (`.bento-bars-track`): `height: 270px; gap: 1.5rem;`.
- Kolom bar (`.bento-bar-container`):
  - Diberikan lebar adaptif: `width: 100%; max-width: 120px; height: 230px; border-radius: 12px;`.
  - Pola arsir diagonal (`repeating-linear-gradient`) membentang setinggi 230px.
  - Batang bar solid: `border-radius: 12px 12px 0 0; min-height: 36px;`.
  - Angka dalam bar: `font-size: 18px; font-weight: 800;` diletakkan di bagian atas bar (`padding-top: 12px;`).
  - Label kategori di bawah: `font-size: 13px; font-weight: 700; margin-top: 12px;`.

### C. Penyelarasan Kartu Kuota Bimbingan
- Ketinggian kartu Kuota Bimbingan diselaraskan menjadi `min-height: 420px;`.
- Wadah gauge diskalakan: `width: 250px; height: 145px;` dengan `gauge-center-percent` sebesar `48px`, serta keterangan subteks `14px` agar proporsional dan tidak ada kekosongan ruang.

---

## 4. Rincian Perubahan File

| File Target | Aksi | Rincian Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/pembimbing/dashboard.css` | **Modify** | 1. Implementasi pseudo-element `::after` untuk aksen lingkaran sudut kartu KPI (Mentee, Tugas, Evaluasi).<br>2. Penskalaan `min-height: 220px` dan padding `24px` pada kartu KPI.<br>3. Penskalaan `min-height: 420px` pada baris tengah (`.bento-mid-row`).<br>4. Perluasan lebar kolom bar Distribusi Tugas dari `64px` menjadi `max-width: 120px; height: 230px;`.<br>5. Penskalaan ukuran font angka bar (`18px`), label kolom (`13px`), dan gauge Kuota Bimbingan (`48px`).<br>6. Kalibrasi Dark Mode untuk lingkaran sudut dan batang bar. |

---

## 5. Rencana Langkah Implementasi Terstruktur

- [ ] **Langkah 1: Konfigurasi Kartu KPI & Lingkaran Aksen Sudut**
  - Mengubah `.bento-kpi-card` menjadi berlatar belakang `#ffffff` murni dengan `min-height: 220px; padding: 24px; border-radius: 20px; position: relative; overflow: hidden;`.
  - Mengonfigurasi `.kpi-card-mentee::after` dengan lingkaran `#dbe7f6` (Dark: `rgba(56, 189, 248, 0.22)`).
  - Mengonfigurasi `.kpi-card-tugas::after` dengan lingkaran `#e4e6f9` (Dark: `rgba(168, 85, 247, 0.22)`).
  - Mengonfigurasi `.kpi-card-evaluasi::after` dengan lingkaran `#fef0c7` (Dark: `rgba(251, 191, 36, 0.20)`).
  - Mengatur ukuran kotak ikon menjadi `48px x 48px; border-radius: 14px;`.
  - Mengatur angka KPI ke `42px; font-weight: 800; color: #002855;`.

- [ ] **Langkah 2: Penskalaan Dimensi & Lebar Kolom Distribusi Tugas (1920x1080)**
  - Menetapkan `.bento-distribusi-card` dan `.bento-kuota-card` ke `min-height: 420px; padding: 24px 28px;`.
  - Mengatur `.bento-distribusi-chart-box` ke `min-height: 330px; padding: 22px 24px 14px;`.
  - Mengubah `.bento-bars-track` ke `height: 270px; gap: 1.5rem;`.
  - Mengubah `.bento-bar-container` dari `max-width: 64px; height: 145px;` menjadi `max-width: 120px; height: 230px; border-radius: 12px;`.
  - Memperbarui `.bento-bar` dengan `border-radius: 12px 12px 0 0; min-height: 36px; padding-top: 10px;`.
  - Memperbarui teks nilai di dalam bar ke `font-size: 18px; font-weight: 800;`.
  - Memperbarui label bawah ke `font-size: 13px; font-weight: 700; margin-top: 12px;`.

- [ ] **Langkah 3: Penskalaan Kartu Kuota Bimbingan agar Sejajar**
  - Mengatur `.bento-kuota-gauge-box` ke `height: 270px;`.
  - Memperbesar `.bento-gauge-wrapper` dan `.bento-gauge-svg` ke `width: 250px; height: 145px;`.
  - Menaikkan font persentase tengah `.gauge-center-percent` ke `font-size: 48px; font-weight: 900;`.
  - Menyesuaikan caption keterangan kuota ke `font-size: 14px; font-weight: 600;`.

- [ ] **Langkah 4: Kompilasi Aset Frontend (Vite)**
  - Menjalankan `npm run build` dan memverifikasi modul terkompilasi bersih tanpa *warning* CSS.

- [ ] **Langkah 5: Quality Gate & Verifikasi Pengujian**
  - Menjalankan `./vendor/bin/pint --test` untuk memastikan integritas format PHP.
  - Menjalankan `php artisan test --filter=Pembimbing` untuk memastikan 131 test (849 assertions) tetap 100% lulus.
  - Menjalankan `graphify update .` untuk menyinkronkan knowledge graph repositori.

---

## 6. Strategi Pengujian (Testing Strategy)

1. **Uji Fungsional & Anti-Regresi**:
   - Menjalankan seluruh test suite pembimbing: `php artisan test --filter=Pembimbing`.
   - Memastikan tidak ada *assertion error* pada metrik beban kuota, distribusi tugas, penugasan, dan logbook.
2. **Uji Visual Multi-Resolusi**:
   - Memverifikasi tampilan pada resolusi **1920x1080** (Full HD) untuk memastikan bar kokoh selebar ~110-120px dan tidak kurus.
   - Memverifikasi pada breakpoint laptop **1366x768** dan **1024px** agar bar tetap beradaptasi tanpa *overflow horizontal*.
3. **Uji Dark Mode**:
   - Memeriksa transisi tema menggunakan atribut `[data-theme="dark"]` untuk memastikan lingkaran aksen sudut dan bar tetap memiliki kontras WCAG 2.1 AA.

---

## 7. Risiko & Mitigasi

| Risiko Potensial | Tingkat | Strategi Mitigasi |
| :--- | :--- | :--- |
| Tampilan meluap (*overflow*) pada layar laptop kecil (< 1200px) | Rendah | Menggunakan `max-width: 120px; width: 100%;` dengan grid `repeat(4, minmax(0, 1fr))` sehingga kolom menyusut secara elegan saat layar menyempit. |
| Lingkaran pseudo-element menutupi teks angka/label | Rendah | Diberikan `z-index: 0; pointer-events: none;` pada `::after`, sementara konten teks dan angka berada di `position: relative; z-index: 1;`. |
| Pelanggaran aturan Blade | Nol | Perubahan dilakukan 100% pada file CSS `dashboard.css`. Tidak ada perubahan markup Blade. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Latar belakang kartu KPI Mentee Aktif, Tugas Berjalan, dan Perlu Evaluasi memiliki lingkaran aksen sudut lembut (`::after` discrete circle) yang presisi sesuai Image 1.
- [ ] Batang bar Distribusi Tugas di resolusi 1920x1080 tampil besar, tinggi (~230px), kokoh (~120px lebar), dengan pola garis arsir diagonal yang lapang dan proporsional sesuai Image 3.
- [ ] Kartu Kuota Bimbingan memiliki tinggi sejajar dan gauge proporsional terhadap Distribusi Tugas.
- [ ] `npm run build` berhasil dikompilasi tanpa error.
- [ ] `./vendor/bin/pint --test` lulus 100%.
- [ ] `php artisan test --filter=Pembimbing` lulus 100% (131 test, 849 assertion).
- [ ] Status Git bersih dan knowledge graph terbarui (`graphify update .`).

