# Senior Engineering Handbook & Implementation Plan: Redesign Diagram Graf Alur Magang Interaktif (Anti-AI-Slop & DAG Pipeline Engine)

- **Dokumen ID**: `PLAN-2026-10-14-ALUR-MAGANG-DAG-INTERACTIVE`
- **Tanggal**: 2026-10-14
- **Status**: Selesai (Verified 100% - All Tests Passed & Visual Browser Verified)
- **Author**: Lead Principal Software Architect & Head of Design Experience (Antigravity web-planning)
- **Target Pelaksana**: Junior Full-Stack Developer / Fast LLM Executor (e.g. Gemini 3.8 Flash)
- **Target URL**: `http://127.0.0.1:8000/panduan-alur`
- **Skill Compliance**:
  - `better-ui`: Surfaces, concentric radius, optical nudges, transitions, scale on press, SVG icons.
  - `better-typography`: Font hierarchy, line-height by role, tabular numbers, text-wrap balance/pretty.
  - `better-layout`: Grouping with space, logical properties, sticky inspector, 2x gap hierarchy.
  - `better-colors`: Official Sleman palette, semantic ramps, WCAG AAA/AA 4.5:1+ contrast.
  - `better-writing`: Direct address ("Anda"), verb-first buttons, explicit PRD rules (BR-01 s/d BR-10).
  - `impeccable`: Out-of-distribution craft, zero blandness, zero AI purple slop, complete delivery.
  - `ui-ux-pro-max`: Immersive DAG Process Map, decision branches, auto-tour simulator, 44px touch targets.

---

## 1. Ringkasan Eksekutif & Problem Statement

### 1.1 Evaluasi Kritis Terhadap Tampilan Saat Ini
Pada implementasi sebelumnya di `resources/views/public/panduan-alur.blade.php`:
1. **Bukan Sebuah Graf Interaktif**: Halaman hanya menampilkan deretan kotak HTML statis bertingkat (*stacked phase cards*) dengan ikon panah kecil di antaranya. Pengguna merasa *"kek ga ada grafik interaksinya"* karena tidak ada canvas diagram, tidak ada jalur pipa (*pipeline*), tidak ada visualisasi percabangan keputusan (*decision fork*), dan tidak ada animasi aliran proses (*flow pulse*).
2. **Ketiadaan Hubungan Aliran Visual (Flow Connections)**: Jalur penghubung antar tahap tidak tergambar sebagai pipa proses yang hidup, melainkan hanya garis batas kiri (*border-left*) sederhana.
3. **Loopback Revisi Hilang**: PRD menetapkan bahwa jika berkas dinyatakan *Perlu Perbaikan*, pemohon memiliki waktu 3x24 jam untuk mengunggah ulang dokumen (kembali ke Tahap 03: Unggah Berkas). Pada tampilan saat ini, ini hanya berupa kotak merah di samping kotak hijau, tanpa visualisasi aliran putar balik (*feedback loop*).
4. **Interaksi Statis**: Tidak ada fitur simulasi jalannya proses (*interactive walkthrough/tour*), tidak ada pelacak progres milestone di bagian atas (*progress tracker*), dan tidak ada highlight aliran aktif (*active beam lighting*) saat node diklik.
5. **Bug Kritis CSS (Unclosed Brace di baris 287)**: Terdapat blok CSS `.journey-board-container { display: flex; flex-direction: column;` yang tidak memiliki penutup `}` di baris 287. Akibatnya, seluruh deklarasi CSS setelahnya (termasuk `.journey-stepper-track`) diabaikan oleh browser, menyebabkan nomor milestone 01 sampai 07 tampil berantakan sebagai teks vertikal polos tanpa layout!

### 1.2 Visi Redesign: "Interactive DAG Process Pipeline Engine"
Kita akan merombak area diagram menjadi **Interactive DAG (Directed Acyclic Graph) Workflow Canvas** berstandar industri modern (terinspirasi oleh *workflow visualizer* kelas dunia seperti *n8n*, *GitHub Actions DAG*, dan *Linear Roadmap*):
1. **Interactive Vector Pipeline Overlay**: Jalur pipa SVG dinamis dengan kurva bezier mulus yang menghubungkan node-node tahapan. Saat node aktif, jalur pipa yang terhubung akan **menyala terang dengan animasi pulsa energi (*particle beam / stroke-dashoffset flow*)**.
2. **True Decision Gateway (Percabangan Nyata)**:
   - Node 04 (Verifikasi Admin SLA 1–3 Hari) bermuara pada gerbang keputusan (*Decision Fork*):
     - **Jalur Hijau (Happy Path)**: Diterima Resmi &rarr; Terbit SK Digital & QR &rarr; Masuk ke Fase 3 (Orientasi & Bimbingan).
     - **Jalur Biru (Queue Path)**: Kuota Penuh &rarr; Masuk Antrean Waiting List (FIFO) &rarr; Menunggu promosi otomatis.
     - **Jalur Merah/Amber (Loopback Path)**: Dokumen Buram/Keliru &rarr; Perlu Perbaikan (3x24 Jam) &rarr; **Garis kurva berputar kembali ke Node 03 (Unggah Berkas)**!
3. **Interactive Guided Simulation Tour (Fitur "Play" Alur)**:
   - Tombol kontrol di header canvas: **"Mulai Simulasi Alur (Auto Tour)"**, **"Langkah Berikutnya (&rarr;)"**, **"Langkah Sebelumnya (&larr;)"**, dan **"Reset"**.
   - Pengguna dapat menyaksikan simulasi otomatis di mana sistem memandu langkah demi langkah (01 &rarr; 02 &rarr; 03 &rarr; 04 &rarr; Cabang Keputusan &rarr; 05 &rarr; 06 &rarr; 07) dengan update real-time pada Side Inspector.
4. **Milestone Journey Progress Stepper**:
   - Baris milestone 7 langkah di bagian atas canvas dengan progress bar biru yang terisi secara proporsional.
5. **Zero-Jitter, 100% Responsif, & Anti-AI-Slop**:
   - Struktur hybrid: Kartu node HTML semantik untuk teks dan tombol, dilapisi dengan canvas SVG absolut untuk garis penghubung responsif.
   - Tanpa CSS `transform: translateY` pada pemicu hover (menjamin **Zero-Jitter**).
   - Palet warna resmi Sleman Diskominfo (Navy Slate `#0F172A`, Sleman Blue `#0751FF`, Emerald `#16A34A`, Amber `#D97706`).

---

## 2. Matriks Penerapan 7 Desain Skill

| Skill | Aturan & Prinsip Utama | Implementasi Konkret pada Halaman `/panduan-alur` |
| :--- | :--- | :--- |
| **`better-ui`** | - Radius konsentris: $R_{outer} = R_{inner} + padding$.<br>- Transisi `cubic-bezier(0.2, 0, 0, 1)`, durasi $\le 200$ms.<br>- Tombol ditekan (*press*) berskala `0.96`.<br>- Ikon SVG memakai `currentColor` & tebal goresan sesuai berat font (1.5px–2px).<br>- Dukung `prefers-reduced-motion` dan batasi hover pada `@media (hover: hover)`. | - Node card: padding 16px, inner radius 8px, outer radius 16px.<br>- Efek klik `:active { transform: scale(0.96); }`.<br>- Ikon SVG seragam (Lucide style) dengan stroke 2px.<br>- Saat reduced-motion aktif, matikan animasi gerak pulsa aliran SVG dan ganti dengan garis solid menyala statis. |
| **`better-typography`** | - Font resmi: Public Sans / Inter.<br>- Heading berskala menurun, `letter-spacing: -0.02em`, `line-height: 1.25`, `text-wrap: balance`.<br>- Body copy: `line-height: 1.55`, `text-wrap: pretty`, batas lebar `65ch`.<br>- Angka metrik, waktu, dan SLA menggunakan `font-variant-numeric: tabular-nums`. | - Judul canvas H2: 1.5rem, bold 800, balance.<br>- SLA badges & step numbers: tabular-nums ("1–3 Hari", "3x24 Jam", "100 Poin", "30 Hari").<br>- Deskripsi pada inspector: `text-wrap: pretty; max-width: 60ch;`. |
| **`better-layout`** | - Hirarki gap: Jarak antar-grup $\ge 2\times$ jarak dalam-grup.<br>- Properti logika CSS (`margin-inline`, `padding-block`).<br>- Sticky side panel dengan `scroll-padding-block-start`.<br>- Kontrol interaktif terpisah tegas dari konten teks statis. | - Dalam card: gap 8px. Antar card dalam fase: gap 16px. Antar fase: gap 36px (memenuhi aturan $\ge 2\times$).<br>- Sticky Inspector Panel diberi `top: 90px; scroll-padding-block-start: 100px`.<br>- Toolbar simulasi diletakkan di atas canvas dalam control bar tersendiri. |
| **`better-colors`** | - Ramping warna semantik, bukan warna acak.<br>- Satu warna = satu arti (*single source of truth*).<br>- Kontras teks $\ge 4.5:1$ (WCAG AA/AAA).<br>- Larangan gradien ungu/pink neon generik AI slop. | - Primary: Slate 900 `#0F172A`.<br>- Brand Accent: Sleman Blue `#0751FF` (fokus & navigasi aktif).<br>- Happy Path (Diterima): Emerald `#16A34A` (bg `#DCFCE7`).<br>- Antrean: Sky Blue `#0284C7` (bg `#E0F2FE`).<br>- Revisi / Perhatian: Amber `#D97706` / Red `#DC2626`. |
| **`better-writing`** | - Bahasa Indonesia baku kedinasan yang ramah & lugas.<br>- Sapa pembaca langsung dengan kata *"Anda"*.<br>- Tombol berbasis kata kerja (*Verb-first*).<br>- Rujukan aturan bisnis PRD eksplisit (BR-01 s/d BR-10). | - Tombol: *"Mulai Simulasi"*, *"Jelajahi Langkah"*, *"Lihat Kuota"*, *"Salin Tautan"*, *"Cek Status"*, bukan "OK" atau "Klik di sini".<br>- Teks bantuan: *"Anda dapat memantau pergerakan antrean..."*, *"Pastikan berkas Anda..."*.<br>- Rujukan eksplisit PRD: BR-01, BR-02, BR-05, BR-09, BR-10, AC-12. |
| **`impeccable`** | - *Out-of-distribution craft*: Desain istimewa, elegan, dan berkarakter dinas modern.<br>- Mode gabungan: *Operate* (alat simulator) + *Read* (panduan jelas) + *Experience* (diagram hidup).<br>- Zero visual bugs & zero shortcuts. | - Diagram alur tidak lagi seperti daftar kartu biasa, melainkan peta pipeline alur kerja yang futuristik namun sangat fungsional dan mudah dipahami oleh siapa pun. |
| **`ui-ux-pro-max`** | - Pola: *Immersive Interactive Process DAG* + *Ethical Public Portal*.<br>- Happy path hijau tebal, deviasi/antrean biru, loopback revisi amber.<br>- Target sentuh $\ge 44\times 44$px untuk aksesibilitas mobile/tablet. | - Ukuran tombol kontrol minimal 44px tinggi.<br>- Diagram mendukung navigasi Keyboard (Arrow Left/Right, Enter, Space).<br>- Tersedia tombol *"Lewati Simulasi / Tampilan Tabel"* bagi pengguna yang membutuhkan format cepat. |

---

## 3. Topologi Aliran & Diagram Alur Interaktif (DAG Topology)

Berikut adalah peta relasi node yang wajib digambarkan oleh engine graf baru:

```mermaid
flowchart TD
    classDef default fill:#FFFFFF,stroke:#CBD5E1,stroke-width:2px,color:#0F172A,rx:10px,ry:10px;
    classDef active fill:#EFF6FF,stroke:#0751FF,stroke-width:3px,color:#0751FF,rx:10px,ry:10px;
    classDef success fill:#F0FDF4,stroke:#16A34A,stroke-width:2.5px,color:#166534,rx:10px,ry:10px;
    classDef queue fill:#F0F9FF,stroke:#0284C7,stroke-width:2.5px,color:#075985,rx:10px,ry:10px;
    classDef alert fill:#FFFBEB,stroke:#D97706,stroke-width:2.5px,color:#92400E,rx:10px,ry:10px;
    classDef decision fill:#F8FAFC,stroke:#64748B,stroke-width:2px,stroke-dasharray: 4 4,rx:8px,ry:8px;

    subgraph F1 ["Fase 1: Pendaftaran & Kuota Bulanan"]
        N1["<b>01. Cek Kuota</b><br/><small>Pantau Kalender Kursi Real-Time (BR-01)</small>"]
        N2["<b>02. Buat Akun</b><br/><small>Biodata NISN/NIM & Kontak WhatsApp</small>"]
        N3["<b>03. Unggah Berkas</b><br/><small>Surat Kampus, CV, Transkrip (PDF 2MB)</small>"]
        N1 -->|Pilih Periode| N2
        N2 -->|Verifikasi Email| N3
    end

    subgraph F2 ["Fase 2: Verifikasi & Keputusan Dinas (SLA 1-3 Hari)"]
        N4["<b>04. Verifikasi Dokumen</b><br/><small>Telaah Legalitas & Kompetensi Jurusan</small>"]
        DEC{"<b>Gerbang Keputusan</b><br/><small>SLA Maks. 3 Hari Kerja</small>"}
        
        N3 -->|Kirim Pendaftaran| N4
        N4 --> DEC
        
        DEC -->|Memenuhi Syarat & Ada Kuota| DIT["<b>STATUS: DITERIMA RESMI</b><br/><small>SK Digital + QR Code Sah</small>"]:::success
        DEC -->|Memenuhi Syarat & Kuota Penuh| WAI["<b>STATUS: WAITING LIST</b><br/><small>Antrean Prioritas FIFO (BR-03)</small>"]:::queue
        DEC -->|Dokumen Buram / Lampiran Salah| REV["<b>STATUS: PERLU PERBAIKAN</b><br/><small>Jendela Waktu 3x24 Jam Kalender</small>"]:::alert
        
        REV -.->|Unggah Ulang Perbaikan (Loopback)| N3
        WAI -.->|Promosi Otomatis Jika Kuota Batal| DIT
    end

    subgraph F3 ["Fase 3: Pelaksanaan & Bimbingan"]
        N5["<b>05. Orientasi & Mentor</b><br/><small>Hari Ke-1: Briefing & Penugasan Lapangan</small>"]
        N6["<b>06. Logbook Harian</b><br/><small>Input Aktivitas Harian Maks. H-1 (BR-09)</small>"]
        
        DIT -->|Konfirmasi Kehadiran BR-05| N5
        N5 -->|Supervisi Berkala| N6
    end

    subgraph F4 ["Fase 4: Evaluasi & Kelulusan"]
        N7["<b>07. Rubrik 100 Poin & Sertifikat</b><br/><small>Lembar Nilai + Sertifikat QR (Retensi 30 Hari BR-10)</small>"]
        
        N6 -->|Laporan Akhir Disahkan| N7
    end
```

---

## 4. Arsitektur Komponen Visual & Canvas Engine

Struktur tampilan pada `resources/views/public/panduan-alur.blade.php` diatur dalam layout grid 2 kolom yang sangat proporsional:

```
+---------------------------------------------------------------------------------------------------+
| HEADER HERO: Peta Interaktif Alur & Siklus Magang Diskominfo Sleman                               |
| [4 Fase Resmi]  [SLA 1-3 Hari Kerja]  [Rubrik 100 Poin]  [Sertifikat QR Digital]                  |
+---------------------------------------------------------------------------------------------------+
| CONTROL & SIMULATION TOOLBAR:                                                                     |
| Filter Jenjang: [Semua] [SMK] [Mahasiswa] | Tipe: [Mandiri] [Kelompok] | View: [Graf Pipa] [Tabel] |
| Tour Player: [▶ Mulai Tur Otomatis] [◀ Sebelumnya] [Selanjutnya ▶] [⟲ Reset]                      |
+---------------------------------------------------------------------------------------------------+
| JOURNEY MILESTONE STEPPER (Progress Bar):                                                         |
| (1) Cek Kuota === (2) Registrasi === (3) Berkas === (4) Verifikasi === (5) Orientasi === (6) Logbook === (7) Nilai |
+-------------------------------------------------------------------+-------------------------------+
| KOLOM KIRI (70%): THE INTERACTIVE DAG PIPELINE CANVAS             | KOLOM KANAN (30%):            |
|                                                                   | STICKY NODE INSPECTOR         |
| +---------------------------------------------------------------+ | +---------------------------+ |
| | [SVG Background Dynamic Connector Pipelines with Animated     | | | [Badge Fase & Warna]      | |
| |  Energy Pulses & Loopback Curves]                             | | |                           | |
| |                                                               | | | Judul Node Terpilih       | |
| |   [01. Cek Kuota]  --->  [02. Buat Akun]  --->  [03. Berkas]  | | | Deskripsi Lengkap         | |
| |                                                     ^         | | |                           | |
| |                                                     |         | | | [Aktor]    [Estimasi SLA] | |
| |                                                     |         | | |                           | |
| |                           [04. Verifikasi Admin]    |         | | | Persyaratan / Output:     | |
| |                                     |               |         | | | ✔ Item 1                  | |
| |                               [Decision Fork]       |         | | | ✔ Item 2                  | |
| |                            /        |        \      |         | | |                           | |
| |                     [Diterima] [Waiting] [Perbaikan]-+        | | | Callout Aturan PRD        | |
| |                         |                                     | | | (BR-01 / BR-09 / BR-10)   | |
| |                         v                                     | | |                           | |
| |                 [05. Orientasi] ---> [06. Logbook Harian]     | | | [Tombol Aksi Dinamis CTA] | |
| |                                              |                | | +---------------------------+ |
| |                                              v                |                                 |
| |                                     [07. Nilai & Sertifikat]  |                                 |
| +---------------------------------------------------------------+ |                               |
+-------------------------------------------------------------------+-------------------------------+
```

---

## 5. Spesifikasi Teknis CSS & Animasi (Zero-Jitter & Anti-AI-Slop)

Berikut aturan CSS wajib yang harus diintegrasikan di `@push('styles')`:

```css
/* =========================================================================
   INTERACTIVE DAG PIPELINE STYLING (DS-MAGKOM / BETTER-UI COMPLIANT)
   ========================================================================= */

/* 1. Canvas Card Container */
.dag-canvas-container {
    position: relative;
    background: #ffffff;
    border: 1px solid var(--neutral-300, #E2E8F0);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.05), 0 2px 4px -2px rgba(15, 23, 42, 0.03);
    overflow: hidden;
}

[data-theme="dark"] .dag-canvas-container {
    background: #182234;
    border-color: #26354D;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
}

/* 2. Top Journey Stepper */
.journey-stepper-track {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    margin-bottom: 28px;
    padding: 12px 16px;
    background: var(--neutral-100, #F8FAFC);
    border: 1px solid var(--neutral-200, #E2E8F0);
    border-radius: 12px;
}

[data-theme="dark"] .journey-stepper-track {
    background: #0F172A;
    border-color: #26354D;
}

.stepper-progress-fill {
    position: absolute;
    top: 50%;
    left: 20px;
    right: 20px;
    height: 3px;
    background: var(--neutral-300, #E2E8F0);
    transform: translateY(-50%);
    z-index: 1;
}

.stepper-progress-bar {
    height: 100%;
    background: var(--blue-600, #0751FF);
    width: 14.28%;
    transition: width 300ms cubic-bezier(0.2, 0, 0, 1);
}

.stepper-node-dot {
    position: relative;
    z-index: 2;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid var(--neutral-300, #CBD5E1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--neutral-600, #475569);
    cursor: pointer;
    transition: all 180ms cubic-bezier(0.2, 0, 0, 1);
    font-variant-numeric: tabular-nums;
}

.stepper-node-dot.active {
    background: var(--blue-600, #0751FF);
    border-color: var(--blue-600, #0751FF);
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(7, 81, 255, 0.2);
}

.stepper-node-dot.completed {
    background: #16A34A;
    border-color: #16A34A;
    color: #ffffff;
}

/* 3. Interactive DAG Stage Grid */
.dag-stage-grid {
    display: flex;
    flex-direction: column;
    gap: 32px;
    position: relative;
    z-index: 2;
}

.dag-stage-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    align-items: stretch;
}

/* 4. Interactive Node Card (ZERO JITTER) */
.dag-node-card {
    position: relative;
    background: #ffffff;
    border: 1.5px solid var(--neutral-300, #CBD5E1);
    border-radius: 12px;
    padding: 14px 16px;
    cursor: pointer;
    user-select: none;
    transition: border-color 150ms ease, box-shadow 150ms ease, background-color 150ms ease;
    /* DILARANG: transform: translateY(...) untuk cegah mouse jitter */
}

.dag-node-card:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.4);
    border-color: var(--blue-600, #0751FF);
}

.dag-node-card:active {
    transform: scale(0.98); /* Aman karena scale seragam pada sumbu tengah */
}

@media (hover: hover) {
    .dag-node-card:hover {
        border-color: var(--blue-500, #3B82F6);
        box-shadow: 0 8px 16px -2px rgba(7, 81, 255, 0.12);
        background-color: #F8FAFC;
    }
}

.dag-node-card.active {
    border-color: var(--blue-600, #0751FF);
    background-color: #EFF6FF;
    box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.25), 0 8px 20px -2px rgba(7, 81, 255, 0.15);
}

[data-theme="dark"] .dag-node-card {
    background: #1E293B;
    border-color: #334155;
}

[data-theme="dark"] .dag-node-card:hover {
    background: #243247;
    border-color: #3B82F6;
}

[data-theme="dark"] .dag-node-card.active {
    background: #172554;
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

/* 5. Decision Hub & Branches */
.dag-decision-hub {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 8px;
}

@media (max-width: 768px) {
    .dag-decision-hub {
        grid-template-columns: 1fr;
    }
}

.dag-branch-pill {
    padding: 10px 12px;
    border-radius: 10px;
    border: 1.5px solid transparent;
    cursor: pointer;
    transition: all 150ms ease;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.dag-branch-pill:active {
    transform: scale(0.97);
}

.dag-branch-pill.diterima {
    background: #F0FDF4;
    border-color: #BBF7D0;
    color: #166534;
}
.dag-branch-pill.diterima.active,
.dag-branch-pill.diterima:hover {
    border-color: #16A34A;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.25);
    background: #DCFCE7;
}

.dag-branch-pill.waiting {
    background: #F0F9FF;
    border-color: #BAE6FD;
    color: #0369A1;
}
.dag-branch-pill.waiting.active,
.dag-branch-pill.waiting:hover {
    border-color: #0284C7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.25);
    background: #E0F2FE;
}

.dag-branch-pill.perbaikan {
    background: #FFFBEB;
    border-color: #FDE68A;
    color: #92400E;
}
.dag-branch-pill.perbaikan.active,
.dag-branch-pill.perbaikan:hover {
    border-color: #D97706;
    box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.25);
    background: #FEF3C7;
}

/* 6. Dynamic SVG Pipeline Overlay */
.dag-svg-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.dag-pipeline-path {
    fill: none;
    stroke: var(--neutral-300, #CBD5E1);
    stroke-width: 2.5px;
    stroke-dasharray: 6 6;
    transition: stroke 300ms ease, stroke-width 300ms ease;
}

.dag-pipeline-path.active {
    stroke: var(--blue-600, #0751FF);
    stroke-width: 3.5px;
    stroke-dasharray: 8 4;
    animation: pipelineFlow 1.2s linear infinite;
}

.dag-pipeline-path.loopback {
    stroke: #D97706;
    stroke-dasharray: 6 6;
}

.dag-pipeline-path.loopback.active {
    stroke: #D97706;
    stroke-width: 3.5px;
    animation: pipelineFlow 1s linear infinite reverse;
}

@keyframes pipelineFlow {
    from { stroke-dashoffset: 24; }
    to { stroke-dashoffset: 0; }
}

@media (prefers-reduced-motion: reduce) {
    .dag-pipeline-path.active,
    .dag-pipeline-path.loopback.active {
        animation: none !important;
        stroke-dasharray: none !important;
    }
}

/* 7. Simulation Control Bar */
.dag-sim-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 12px 18px;
    background: var(--neutral-0, #ffffff);
    border: 1px solid var(--neutral-300, #E2E8F0);
    border-radius: 12px;
    margin-bottom: 20px;
}

.dag-sim-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    font-size: 0.8125rem;
    font-weight: 700;
    border-radius: 8px;
    border: 1px solid var(--neutral-300, #CBD5E1);
    background: var(--neutral-0, #ffffff);
    color: var(--neutral-700, #334155);
    cursor: pointer;
    min-height: 40px;
    transition: all 150ms ease;
}

.dag-sim-btn:hover {
    border-color: var(--blue-600, #0751FF);
    color: var(--blue-600, #0751FF);
    background: #F8FAFC;
}

.dag-sim-btn.primary {
    background: var(--blue-600, #0751FF);
    border-color: var(--blue-600, #0751FF);
    color: #ffffff;
}

.dag-sim-btn.primary:hover {
    background: var(--blue-700, #0043DB);
}
```

---

## 6. Spesifikasi Engine JavaScript & State Machine

Junior Developer atau AI (Gemini Flash) wajib mengimplementasikan state machine JavaScript berikut tanpa ketergantungan library luar (*vanilla JavaScript murni*):

```javascript
// =========================================================================
// DAG INTERACTIVE ENGINE & SIMULATOR CONTROLLER
// =========================================================================

const DAG_NODES_CONFIG = {
    1: { id: 1, stepNumber: '01', title: 'Cek Kuota Bulanan', phaseId: 1, next: 2, prev: null },
    2: { id: 2, stepNumber: '02', title: 'Registrasi Akun Peserta', phaseId: 1, next: 3, prev: 1 },
    3: { id: 3, stepNumber: '03', title: 'Unggah Berkas PDF', phaseId: 1, next: 4, prev: 2 },
    4: { id: 4, stepNumber: '04', title: 'Verifikasi Berkas Admin', phaseId: 2, next: 'diterima', prev: 3 },
    'diterima': { id: 'diterima', stepNumber: '04-A', title: 'Status Diterima Resmi', phaseId: 2, next: 5, prev: 4 },
    'waiting': { id: 'waiting', stepNumber: '04-B', title: 'Status Waiting List', phaseId: 2, next: 'diterima', prev: 4 },
    'perbaikan': { id: 'perbaikan', stepNumber: '04-C', title: 'Status Perlu Perbaikan', phaseId: 2, next: 3, prev: 4, isLoopback: true },
    5: { id: 5, stepNumber: '05', title: 'Orientasi & Penetapan Mentor', phaseId: 3, next: 6, prev: 'diterima' },
    6: { id: 6, stepNumber: '06', title: 'Pengisian Logbook Harian', phaseId: 3, next: 7, prev: 5 },
    7: { id: 7, stepNumber: '07', title: 'Rubrik 100 Poin & Sertifikat', phaseId: 4, next: null, prev: 6 }
};

let currentActiveNodeId = 1;
let isAutoPlaying = false;
let autoPlayTimer = null;

// 1. Fungsi Pemilihan Node
function selectDagNode(nodeId, shouldScrollToInspector = false) {
    currentActiveNodeId = nodeId;
    
    // Nonaktifkan seluruh node & branch
    document.querySelectorAll('.dag-node-card, .dag-branch-pill, .svg-node-group').forEach(el => {
        el.classList.remove('active');
    });
    
    // Aktifkan elemen terpilih
    const targetEl = document.getElementById(`node-${nodeId}`);
    if (targetEl) {
        targetEl.classList.add('active');
    }
    
    // Update Stepper Progress
    updateStepperProgress(nodeId);
    
    // Update Pipeline Connections
    updatePipelineGlow(nodeId);
    
    // Update Side Inspector
    updateSideInspector(nodeId);

    // Update URL param secara mulus tanpa reload
    if (history.replaceState) {
        const url = new URL(window.location);
        url.searchParams.set('step', nodeId);
        window.history.replaceState({}, '', url);
    }
}

// 2. Update Progress Stepper
function updateStepperProgress(nodeId) {
    const stepOrder = [1, 2, 3, 4, 'diterima', 5, 6, 7];
    const index = stepOrder.indexOf(nodeId);
    const progressPercent = index >= 0 ? Math.round(((index + 1) / stepOrder.length) * 100) : 15;
    
    const progressBar = document.getElementById('dagProgressBar');
    if (progressBar) {
        progressBar.style.width = `${progressPercent}%`;
    }
    
    document.querySelectorAll('.stepper-node-dot').forEach((dot, i) => {
        const dotStep = parseInt(dot.getAttribute('data-step'), 10);
        dot.classList.remove('active', 'completed');
        if (i < index) {
            dot.classList.add('completed');
        } else if (i === index) {
            dot.classList.add('active');
        }
    });
}

// 3. Update Pipeline Glow pada SVG
function updatePipelineGlow(nodeId) {
    document.querySelectorAll('.dag-pipeline-path').forEach(path => path.classList.remove('active'));
    
    // Highlight jalur masuk dan keluar node
    const incomingPath = document.getElementById(`path-to-${nodeId}`);
    const outgoingPath = document.getElementById(`path-from-${nodeId}`);
    
    if (incomingPath) incomingPath.classList.add('active');
    if (outgoingPath) outgoingPath.classList.add('active');
    
    // Jika loopback perbaikan aktif
    if (nodeId === 'perbaikan') {
        const loopbackPath = document.getElementById('path-loopback-perbaikan');
        if (loopbackPath) loopbackPath.classList.add('active');
    }
}

// 4. Simulasi Otomatis (Auto-Tour Walkthrough)
function toggleAutoPlayTour() {
    const playBtn = document.getElementById('btnPlayTour');
    if (isAutoPlaying) {
        stopAutoPlayTour();
    } else {
        startAutoPlayTour();
    }
}

function startAutoPlayTour() {
    isAutoPlaying = true;
    const playBtn = document.getElementById('btnPlayTour');
    if (playBtn) {
        playBtn.innerHTML = `
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
            <span>Jeda Simulasi</span>
        `;
        playBtn.classList.add('primary');
    }
    
    runTourStep();
}

function runTourStep() {
    if (!isAutoPlaying) return;
    
    const tourSequence = [1, 2, 3, 4, 'diterima', 5, 6, 7];
    let currentIndex = tourSequence.indexOf(currentActiveNodeId);
    let nextIndex = (currentIndex + 1) % tourSequence.length;
    
    selectDagNode(tourSequence[nextIndex]);
    
    autoPlayTimer = setTimeout(() => {
        runTourStep();
    }, 2800); // 2.8 detik per tahap (waktu ideal untuk membaca)
}

function stopAutoPlayTour() {
    isAutoPlaying = false;
    clearTimeout(autoPlayTimer);
    const playBtn = document.getElementById('btnPlayTour');
    if (playBtn) {
        playBtn.innerHTML = `
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            <span>Mulai Simulasi Alur</span>
        `;
        playBtn.classList.remove('primary');
    }
}

function nextDagStep() {
    stopAutoPlayTour();
    const config = DAG_NODES_CONFIG[currentActiveNodeId];
    if (config && config.next) {
        selectDagNode(config.next);
    } else {
        selectDagNode(1);
    }
}

function prevDagStep() {
    stopAutoPlayTour();
    const config = DAG_NODES_CONFIG[currentActiveNodeId];
    if (config && config.prev) {
        selectDagNode(config.prev);
    }
}

function resetDagTour() {
    stopAutoPlayTour();
    selectDagNode(1);
}
```

---

## 7. Rencana Kerja Terstruktur (Senior Tasks untuk Junior Developer / Gemini Flash)

Junior Developer atau AI pelaksana wajib menyelesaikan tugas ini secara berurutan (*sequential*):

### Fase 1: Perbaikan Bug Kritis CSS & Pembuatan Markup DAG Canvas
- [x] **Task 1.1**: Perbaiki bug CSS unclosed brace pada baris 287 di `resources/views/public/panduan-alur.blade.php`.
- [x] **Task 1.2**: Tambahkan Toolbar Kontrol Simulasi (`.dag-sim-toolbar`) tepat di atas area graf, memuat:
  - Tombol *"Mulai Simulasi Alur"* (`#btnPlayTour`)
  - Tombol *"Sebelumnya"* (`#btnPrevStep`) & *"Selanjutnya"* (`#btnNextStep`)
  - Tombol *"Reset"* (`#btnResetStep`)
  - Indikator *"Langkah X dari 7"*
- [x] **Task 1.3**: Tambahkan Stepper Progres Perjalanan (`.journey-stepper-track`) dengan 7 bulatan nomor yang terhubung progress bar dinamis.
- [x] **Task 1.4**: Buat container `.dag-canvas-container` yang membungkus:
  - Elemen SVG Background Overlay (`<svg class="dag-svg-overlay">`) dengan definisi marker panah dan jalur bezier untuk:
    - Path 01 &rarr; 02
    - Path 02 &rarr; 03
    - Path 03 &rarr; 04
    - Path 04 &rarr; Decision Hub
    - Path Diterima &rarr; 05
    - Path 05 &rarr; 06
    - Path 06 &rarr; 07
    - Path Loopback: Perbaikan &rarr; 03 (kurva putar balik dengan panah kembali)
  - Grid Node Semantik (`.dag-stage-grid`):
    - Row 1: Node 01, Node 02, Node 03
    - Row 2: Node 04 (Verifikasi) + Gerbang Percabangan 3 Pill (`#node-diterima`, `#node-waiting`, `#node-perbaikan`)
    - Row 3: Node 05 (Orientasi) & Node 06 (Logbook Harian)
    - Row 4: Node 07 (Rubrik 100 Poin & Sertifikat Digital)

### Fase 2: Implementasi CSS Tokens & Anti-Jitter Styling
- [x] **Task 2.1**: Terapkan seluruh token warna dari Section 5 di `@push('styles')`.
- [x] **Task 2.2**: Pastikan seluruh kelas `.dag-node-card` dan `.dag-branch-pill` **TIDAK MENGGUNAKAN `transform: translateY`** pada efek hover.
- [x] **Task 2.3**: Tambahkan aturan `@media (prefers-reduced-motion: reduce)` untuk mematikan animasi gerak `pipelineFlow` bagi pengguna dengan preferensi aksesibilitas gerakan minimal.
- [x] **Task 2.4**: Lengkapi CSS Dark Mode untuk seluruh komponen baru (`[data-theme="dark"]`).

### Fase 3: Integrasi Engine JavaScript & Kontrol Interaktif
- [x] **Task 3.1**: Masukkan seluruh script controller DAG dari Section 6 ke dalam blok `@push('scripts')`.
- [x] **Task 3.2**: Hubungkan event listener `click` pada tombol tour, tombol next, prev, dan reset.
- [x] **Task 3.3**: Tambahkan event listener navigasi keyboard:
  - Tombol panah kanan / bawah (`ArrowRight` / `ArrowDown`): Lanjut ke langkah berikutnya.
  - Tombol panah kiri / atas (`ArrowLeft` / `ArrowUp`): Kembali ke langkah sebelumnya.
  - Tombol spasi (`Space`): Putar / Jeda simulasi otomatis.
- [x] **Task 3.4**: Pastikan fungsi sinkronisasi `updateSideInspector()` tetap kompatibel dengan ID elemen inspector yang sudah ada (`#inspectorTitle`, `#inspectorDesc`, `#inspectorActor`, `#inspectorSLA`, `#inspectorChecklist`, `#inspectorCallout`, `#inspectorCTA`).

### Fase 4: Testing & Verifikasi Kualitas
- [x] **Task 4.1**: Jalankan `php artisan test tests/Feature/AlurMagangPageTest.php` untuk memastikan seluruh assertion lolos.
- [x] **Task 4.2**: Jalankan pengujian menyeluruh `php artisan test` (377 test cases) untuk memastikan tidak ada efek samping/regresi.
- [x] **Task 4.3**: Jalankan `browser_subagent` untuk verifikasi visual:
  - Memastikan kanvas diagram alur terlihat atraktif, profesional, dan nyata (bukan kotak form bertumpuk).
  - Menguji tombol simulasi otomatis berjalan lancar memindahkan node aktif setiap 2.8 detik.
  - Menguji klik pada cabang *"Perlu Perbaikan"* menampilkan garis kurva putar balik (*loopback*) ke Node 03.
  - Menguji hover mouse di seluruh node: **bebas getar (zero-jitter)**.
- [x] **Task 4.4**: Jalankan `graphify update .` untuk memperbarui knowledge graph proyek.

---

## 8. Strategi Testing & Assertion Matrix

File pengujian `tests/Feature/AlurMagangPageTest.php` harus memvalidasi komponen baru:

```php
public function test_halaman_panduan_alur_memuat_dag_canvas_dan_simulasi_interaktif(): void
{
    $response = $this->get(route('panduan-alur'));

    $response->assertStatus(200);
    // Verifikasi container DAG canvas
    $response->assertSee('dag-canvas-container');
    $response->assertSee('journey-stepper-track');
    $response->assertSee('dag-sim-toolbar');
    
    // Verifikasi tombol simulasi
    $response->assertSee('Mulai Simulasi Alur');
    $response->assertSee('Selanjutnya');
    
    // Verifikasi seluruh node 01 sampai 07
    $response->assertSee('node-1');
    $response->assertSee('node-2');
    $response->assertSee('node-3');
    $response->assertSee('node-4');
    $response->assertSee('node-diterima');
    $response->assertSee('node-waiting');
    $response->assertSee('node-perbaikan');
    $response->assertSee('node-5');
    $response->assertSee('node-6');
    $response->assertSee('node-7');
    
    // Verifikasi rujukan aturan PRD
    $response->assertSee('BR-01');
    $response->assertSee('BR-09');
    $response->assertSee('BR-10');
}
```

---

## 9. Definition of Done (DoD)

- [x] Diagram alur di `/panduan-alur` tampil sebagai **Interactive DAG Workflow Canvas** nyata dengan jalur pipa dinamis, bukan sekadar kotak kartu bertumpuk biasa.
- [x] Terdapat visualisasi gerbang percabangan keputusan (*Decision Fork*) dengan jalur khusus untuk *Diterima*, *Waiting List*, dan kurva *loopback* untuk *Perlu Perbaikan* ke Node 03.
- [x] Fitur simulasi otomatis (*Auto Tour Player*) dengan tombol Play, Pause, Next, Prev, dan Reset berfungsi sempurna.
- [x] Stepper milestone 7 langkah di bagian atas canvas menyala dinamis mengikuti progres.
- [x] Seluruh animasi dan interaksi bebas dari getar mouse (*zero-jitter*).
- [x] Mematuhi seluruh standar `better-ui`, `better-typography`, `better-layout`, `better-colors`, `better-writing`, `impeccable`, dan `ui-ux-pro-max`.
- [x] Pengujian automated test (`php artisan test`) 100% PASS (377 tests).
- [x] Verifikasi visual melalui browser subagent terkonfirmasi rapi, estetik, dan elegan.
