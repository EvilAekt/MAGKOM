# TASK BRIEF SENIOR DEVELOPER: REVAMP TOTAL TAMPILAN PROFIL 360° PESERTA BIMBINGAN (ROLE PEMBIMBING) - REVISI PENYELARASAN PIXEL-PERFECT V2

> **Dari:** Senior Developer / Lead Software Architect  
> **Kepada:** Junior Developer / AI Agent Pelaksana (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Kabupaten Sleman)  
> **Status:** APPROVED FOR IMPLEMENTATION (REVISI PIXEL-PERFECT V2: WARNA KUNING TERPADU #F59E0B, CANVAS BG #E9F0F6, & BADGE PROGRES TUGAS #4E6992)  
> **Tingkat Kesulitan:** Menengah (UI/UX Restructuring, Color Harmony Unification, Contrast Correction, Blade & CSS Slicing)  
> **Dokumentasi Terkait:** `AGENTS.md`, `.agents/rules/AGENTS.md`, `docs/01-PRD-MagKom.md`, `docs/02-TDD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`  
> **File Acuan Desain:**  
> 1. `media_1794217269645_de6c281b.jpg` (Mockup Halaman Penuh)  
> 2. `media_1794217301612_50eef04e.png` (Crop Detail Legenda & Badge Progres Tugas)  
> 3. `media_1794217378716_421f6e9d.png` (Crop Detail Kartu Periode Magang)  
> 4. `media_1794217386903_bf9c8aa7.png` (Crop Detail Segmented Bar Garis Kuning)  
> 5. `media_1794217393013_df4ab51e.png` (Crop Detail Kolom & Kartu Tugas Proses)

---

## 1. 🔍 LATAR BELAKANG TEMUAN & REVISI V2

Berdasarkan audit komparasi visual langsung dari 5 tangkapan layar detail yang diunggah pengguna:
1. **Masalah Kontras Card Putih**:
   - Kartu `LOGBOOK` dan kartu `Riwayat Penugasan` berwarna dasar putih murni (`#FFFFFF`).
   - Karena kanvas halaman sebelumnya masih transparan atau berlatar putih bawaan template, kartu putih tersebut terlihat menyatu dengan latar dan "kurang terlihat jelas/belum ada background-nya".
   - **Solusi**: Menerapkan warna latar kanvas **`#E9F0F6`** (soft grayish-blue canvas) pada seluruh workspace area profil mentee, sehingga kartu-kartu putih terlihat tegas, memiliki elevasi, dan kontras tajam.
2. **Masalah Background & Komposisi Legenda Progres Tugas**:
   - Pada kartu `PROGRES TUGAS` ([`media_1794217301612_50eef04e.png`](file:///C:/Users/galan/.gemini/antigravity/brain/eccea852-443f-4ee4-a11b-827f29382866/.user_uploaded/media_1794217301612_50eef04e.png)):
     - Tidak ada dot/titik warna di depan teks `Selesai`, `Dikerjakan`, `Belum`. Teks tersebut adalah teks putih bersih di atas latar navy `#28487A`.
     - Badge angka `3`, `2`, `1` di sebelah kanan memiliki warna background tersendiri, yaitu **`#4E6992`** (slate-blue pill), bukan transparan samar.
3. **Masalah Inkonsistensi Warna Kuning (Wajib 100% Sama Persis)**:
   - Sebelumnya terdapat percampuran antara kuning muda `#FBBF24` dan kuning oranye `#F59E0B`.
   - Analisis piksel sampling menunjukkan bahwa:
     - Kartu `PERIODE MAGANG` = **`#F59E0B`**
     - Garis tengah `Segmented Bar` (Proses) = **`#F59E0B`**
     - Kedua kartu tugas di kolom `Proses` = **`#F59E0B`**
     - Kapsul tanggal hari ini = **`#F59E0B`**
     - Tombol `Tugas` dan tombol `Kelola Tugas` = **`#F59E0B`**
     - Ring border avatar inisial dan teks `DATA PESERTA` = **`#F59E0B`**
   - **Solusi**: Seluruh elemen kuning disatukan 100% menggunakan satu token mutlak: **`#F59E0B`**!

---

## 2. 🎨 HASIL AUDIT PIKSEL & SPESIFIKASI TOKEN WARNA BARU

| Nama Token / Komponen | Kode HEX Resmi | Elemen yang Menggunakan |
|---|---|---|
| `--mentee-canvas-bg` | **`#E9F0F6`** | Background kanvas area konten pembimbing (`.mentee-canvas-bg`, `.mentee-profile-page`). |
| `--mentee-amber-unified` | **`#F59E0B`** | **Semua elemen kuning**: Kapsul tanggal, ring avatar, tombol "Tugas", header "DATA PESERTA", kartu "Periode Magang", tombol "Kelola Tugas", segmented bar proses, dan semua kartu tugas kolom "Proses". |
| `--mentee-amber-dark` | **`#D97706`** | Hover state tombol kuning & hover link aksi. |
| `--mentee-kpi-blue` | **`#28487A`** | Background kartu "PROGRES TUGAS". |
| `--mentee-kpi-badge` | **`#4E6992`** | Background badge counter (3, 2, 1) pada legenda kartu "PROGRES TUGAS". |
| `--mentee-navy-card` | **`#0F3875`** | Background kartu Hero Profil Mentee, background kartu tugas kolom "Belum". |
| `--mentee-navy-deep` | **`#0F2C59`** | Tombol `← Kembali`, badge count kapsul header kanban, kotak minggu logbook selesai. |
| `--mentee-royal-blue` | **`#2563EB`** | Background kartu tugas kolom "Selesai", segmen segmented bar selesai. |
| `--mentee-surface-white` | **`#FFFFFF`** | Background kartu "LOGBOOK", background kartu "Riwayat Penugasan" (berdiri di atas kanvas `#E9F0F6`). |

---

## 3. 📐 BLUEPRINT DETAIL PERBAIKAN KOMPONEN

### 3.1 Penataan Background Kanvas Halaman
- Pada layout dashboard, container pembungkus konten utama dipastikan memiliki latar belakang `#E9F0F6`.
- Di Blade: `@section('content_area_class', 'mentee-canvas-bg')`
- Di CSS:
  ```css
  .dashboard-content-area.mentee-canvas-bg,
  body.role-pembimbing .right_col.dashboard-main {
      background-color: #E9F0F6 !important;
  }
  .mentee-profile-page {
      background-color: #E9F0F6;
  }
  ```
- Dampak Visual: Kartu putih `LOGBOOK` dan kartu putih `Riwayat Penugasan` memiliki batas kontras tajam terhadap kanvas abu-abu lembut `#E9F0F6`.

### 3.2 Penataan Kartu "PROGRES TUGAS" & Legenda
- Card background: `#28487A` (solid navy).
- Donut chart:
  - Segmen Selesai = `#F59E0B`
  - Segmen Dikerjakan = `#FFFFFF`
  - Segmen Belum = `#4E6992`
  - Pusat donat = lingkaran `#28487A` dengan teks putih tebal `50%`.
- Legenda (Sesuai `media_1794217301612_50eef04e.png`):
  - Hapus bulatan dot warna di sebelah kiri teks.
  - Teks: `Selesai`, `Dikerjakan`, `Belum` (teks putih murni, font-weight 600, font-size 13px).
  - Badge counter di sebelah kanan:
    - Background: **`#4E6992`** (solid slate-blue).
    - Teks: Putih tebal `3`, `2`, `1`.
    - Bentuk: Oval pill berdiameter ~24px x 24px atau padding horizontal `8px`.

### 3.3 Penyatuan Seluruh Warna Kuning ke `#F59E0B`
- Mengeliminasi kode `#FBBF24` dan menstandarkan semua turunan kuning/oranye ke `#F59E0B`:
  - Kapsul tanggal: background `#F59E0B`, teks `#0F172A`.
  - Avatar ring: `3.5px solid #F59E0B`, teks inisial `#F59E0B`.
  - Tombol `Tugas`: background `#F59E0B`, teks `#0F172A`.
  - Header `DATA PESERTA`: teks `#F59E0B`.
  - Kartu `PERIODE MAGANG`: background `#F59E0B`, teks `#0F172A`.
  - Tombol `Kelola Tugas →`: background `#F59E0B`, teks `#0F172A`.
  - Segmented bar `Proses`: fill bar `#F59E0B`.
  - Dot header kanban `Proses`: dot `#F59E0B`.
  - Kartu tugas kolom `Proses`: background `#F59E0B`, teks `#0F172A`, tombol `Detail` outline border `#0F172A`.

---

## 4. 📝 WORKFLOW EKSEKUSI TAHAP DEMI TAHAP

1. **Update Blade (`resources/views/pembimbing/peserta/show.blade.php`)**:
   - Tambahkan `@section('content_area_class', 'mentee-canvas-bg')`.
   - Bersihkan dot bulatan dari legenda donat tugas sehingga hanya menampilkan teks `Selesai`, `Dikerjakan`, `Belum` dan badge `#4E6992`.
2. **Update CSS (`resources/css/pages/pembimbing/peserta-show.css`)**:
   - Terapkan background kanvas `#E9F0F6` pada `.dashboard-content-area.mentee-canvas-bg` dan `.mentee-profile-page`.
   - Ganti seluruh variabel `--mentee-amber` menjadi `#F59E0B`.
   - Terapkan background `#4E6992` pada `.legend-count-pill`.
   - Berikan elevasi dan border halus pada kartu-kartu putih:
     `background-color: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);`
3. **Build & Quality Gates**:
   - Jalankan `npm run build` untuk kompilasi Vite.
   - Jalankan `artisan test --filter=Pembimbing` untuk memastikan 128 test tetap 100% hijau.
   - Verifikasi tidak ada error sintaks atau regresi IDOR.
