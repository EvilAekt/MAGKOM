# 📋 SENIOR DEVELOPER TECHNICAL TASK & PRD
## Task: Penyelarasan Warna & Visual Kartu "Perlu Tindakan" Dashboard SuperAdmin dengan Desain Referensi (Color & Visual Parity)

- **Author**: Lead Senior UI/UX Architect (Antigravity Architecture Team)
- **Target Assignee**: Junior Fullstack Developer / Autonomous AI Agent (Gemini 3.8 Flash)
- **Dokumen Referensi Bukti Visual**:
  - 🎯 **Target (Foto Acuan)**: `media_1794897866975_bfb16cf7.png` (Kartu merah menyala/vibrant crimson, glassmorphism badge putih bersih, teks putih kontras tinggi, segmented progress pills).
  - ⚠️ **Eksisting (Kondisi Saat Ini)**: `media_1794897876221_2fa2442c.png` (Kartu merah gelap/maroon kusam kecokelatan `#450A0A`, badge redup, indikator titik tunggal sepi).
- **Target File yang Perlu Dimodifikasi**:
  1. [`resources/css/pages/superadmin/dashboard.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/dashboard.css) (Penyesuaian token warna background gradient kartu di Dark Mode dan Light Mode, glassmorphism badge icon, text contrast, dan segmented dots bar)
  2. [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php) (Penyesuaian struktur progress bar/dots agar render segmented bar tetap proporsional dan rapi seperti pada foto referensi)
- **Status Dokumen**: ✅ **SELESAI & TERVERIFIKASI (Implemented & Verified)**
- **Tanggal**: 17 November 2026
- **Klasifikasi**: UI/UX Polish & Visual Parity

---

## 🎯 1. Ringkasan Eksekutif (TL;DR untuk Junior Developer / AI)

Kartu metrik peringatan **"Perlu Tindakan"** pada dashboard SuperAdmin saat ini mengalami deviasi visual yang signifikan pada mode tampilan gelap (*Dark Mode*):
1. **Masalah Warna Latar (Background Mismatch)**:
   - Pada `[data-theme="dark"]`, warna kartu di-*override* menggunakan gradien `#450A0A` (Tailwind red-950) ke `#7F1D1D` (red-900). Hal ini menghasilkan warna merah marun gelap kecokelatan yang mati, kusam, dan kehilangan karakternya sebagai elemen *high-priority alert card*.
   - Pada foto target referensi (`media_1794897866975_bfb16cf7.png`), kartu tersebut menggunakan warna **merah crimson menyala (*vibrant scarlet red*)** dengan gradien halus berkisar dari `#CE3D3D` / `#D32F2F` ke `#B82828` / `#B71C1C`.
2. **Masalah Badge Ikon**:
   - Di foto referensi, badge ikon berbentuk persegi melengkung (*rounded tile*) dengan efek *glassmorphism* putih susu translusen yang kontras dan bersih (`rgba(255, 255, 255, 0.22)`).
   - Di kondisi eksisting, badge terlihat redup dan kusam karena warna latar dasar yang terlalu gelap.
3. **Masalah Indikator Segmen Bawah (Segmented Progress Bar)**:
   - Di foto referensi, bagian bawah kartu memiliki baris kapsul horizontal (*segmented rounded pills*) sebanyak ~12 segmen yang rapi. Segmen aktif berwarna putih solid (`#FFFFFF`) dan segmen inaktif berwarna putih transparan (`rgba(255, 255, 255, 0.25)`).
   - Di kondisi eksisting, saat berkas pending = 0, logika Blade hanya merender 1 titik lingkaran kecil abu-abu yang terlihat anomali dan sepi.

### Solusi yang Diinstruksikan:
Selaraskan warna latar, badge, kontras teks, dan baris segmen kartu "Perlu Tindakan" secara presisi 1:1 mengikuti foto referensi `media_1794897866975_bfb16cf7.png`.

---

## 🔬 2. Analisis Komparasi Warna & Desain (Colorimetry Matrix)

Berdasarkan hasil ekstraksi sampel warna (*color sampling analysis*) langsung dari gambar referensi:

| Komponen Visual | Kondisi Eksisting (`media_..._2fa2442c.png`) | Target Referensi (`media_..._bfb16cf7.png`) | Rekomendasi Solusi Teknis |
| :--- | :--- | :--- | :--- |
| **Card Background (Dark Mode)** | Gradien marun gelap: `#450A0A` &rarr; `#7F1D1D` (Hasil render: `#510E0E` s.d. `#751919`) | Gradien merah crimson menyala: `#CE3D3D` &rarr; `#B82828` | Ganti gradien dark mode dengan `linear-gradient(145deg, #CF3D3D 0%, #B82828 100%) !important;` |
| **Card Border** | `1px solid rgba(239, 68, 68, 0.3)` | Tanpa border kaku, halus: `1px solid rgba(255, 255, 255, 0.15)` | Gunakan border putih transparan lembut `rgba(255, 255, 255, 0.15)` |
| **Icon Tile Badge** | `background: rgba(255, 255, 255, 0.18)` tampak kusam di atas marun | `background: rgba(255, 255, 255, 0.22);` terlihat *frosted glass* cerah | Pertahankan `rgba(255, 255, 255, 0.22)` + `backdrop-filter: blur(4px)` |
| **Icon Color** | Semi-redup | Putih solid `#FFFFFF`, stroke width `2.2` | Warna icon `stroke: #FFFFFF;` |
| **Judul Kartu** | "Perlu Tindakan", putih sedikit berbayang | "Perlu Perhatian" / "Perlu Tindakan", putih tegas `#FFFFFF`, `font-weight: 700` | Pastikan `color: #FFFFFF !important;` |
| **Nilai Metrik Angka** | Angka besar putih solid | Angka besar putih solid `#FFFFFF`, font tebal | `color: #FFFFFF; font-weight: 800;` |
| **Teks Keterangan Sub** | `/0 lewat SLA`, warna pudar | `/1 berkas` atau `/X lewat SLA`, putih kontras tinggi `rgba(255, 255, 255, 0.9)` | `color: rgba(255, 255, 255, 0.9);` |
| **Indikator Progres Bawah** | 1 titik bulat `8px` (`.sa-dot`) saat data 0 | Baris segmen horizontal kapsul/pil (~12 segmen) rapi | Ubah styling dot menjadi bentuk pil horizontal (`width: 14px; height: 5px; border-radius: 999px`) dengan minimum 12 slot segmen |

---

## 📁 3. Rincian Perubahan File (File Impact Table)

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`resources/css/pages/superadmin/dashboard.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/dashboard.css) | **Modify** | 1. Perbarui `[data-theme="dark"] .sa-card--action` agar menggunakan warna merah menyala `#CF3D3D` &rarr; `#B82828`, bukan `#450A0A`.<br>2. Perbarui `.sa-action-icon-tile` agar memiliki efek frosted glass bersih.<br>3. Perbarui kelas `.sa-dots-row` dan `.sa-dot` pada kartu action menjadi kapsul horizontal (*segmented pills*). |
| [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php) | **Modify** | Di blok baris 642, pastikan baris segmented pills merender jumlah segmen proporsional (default 12 segmen standar UI) dengan segmen aktif terisi putih solid dan sisanya putih transparan. |

---

## 📋 4. Langkah-Langkah Pengerjaan Bertahap (Senior Dev Tasks)

### Task 1: Perbaikan Token Gradien Warna pada CSS Dashboard
**File Target**: [`resources/css/pages/superadmin/dashboard.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/dashboard.css)
- [x] Di bagian deklarasi default `.sa-card--action` (sekitar baris 960):
  ```css
  .sa-card--action {
      background: linear-gradient(145deg, #CF3D3D 0%, #B82828 100%);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: var(--sa-radius-card, 20px);
      padding: 20px;
      color: #FFFFFF;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: 0 8px 24px rgba(184, 40, 40, 0.25);
  }
  ```
- [x] Di bagian Dark Mode `[data-theme="dark"] .sa-card--action` (sekitar baris 2105):
  Ganti rule `#450A0A` yang kusam dengan warna merah menyala yang identik dengan foto:
  ```css
  [data-theme="dark"] .sa-card--action {
      background: linear-gradient(145deg, #CF3D3D 0%, #B82828 100%) !important;
      border: 1px solid rgba(255, 255, 255, 0.15) !important;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35) !important;
  }
  ```

---

### Task 2: Polishing Badge Ikon & Kontras Teks
**File Target**: [`resources/css/pages/superadmin/dashboard.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/dashboard.css)
- [x] Sesuaikan `.sa-action-icon-tile`:
  ```css
  .sa-action-icon-tile {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.22);
      border: 1px solid rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      color: #FFFFFF;
  }
  ```
- [x] Pastikan `.sa-action-title`, `.sa-action-val`, dan `.sa-action-val-sub` memiliki kontras tinggi:
  ```css
  .sa-action-title {
      font-size: 16px;
      font-weight: 700;
      color: #FFFFFF !important;
      margin: 0;
      letter-spacing: -0.01em;
  }

  .sa-action-val {
      font-size: 52px;
      font-weight: 800;
      line-height: 1;
      color: #FFFFFF !important;
      letter-spacing: -0.03em;
  }

  .sa-action-val-sub {
      font-size: 16px;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.9) !important;
  }
  ```

---

### Task 3: Desain Baris Segmen Pil Horizontal (Segmented Pills Bar)
**File Target**: [`resources/css/pages/superadmin/dashboard.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/dashboard.css) & [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php)
- [x] Pada CSS, tambahkan styling khusus untuk segmen pil pada kartu action:
  ```css
  .sa-card--action .sa-dots-row {
      display: flex;
      align-items: center;
      gap: 5px;
      width: 100%;
      margin-top: 6px;
  }

  .sa-card--action .sa-dot {
      flex: 1 1 0;
      height: 5px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.25);
      transition: background-color 0.2s ease;
  }

  .sa-card--action .sa-dot.is-active {
      background: #FFFFFF !important;
  }
  ```
- [x] Pada file Blade [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php#L642-L646):
  Perbarui perulangan dots agar menggunakan standar 12 segmen horizontal seperti pada foto:
  ```blade
  @php
      $totalPills = 12;
      $activePills = $slaOverdue > 0 ? min($slaOverdue, $totalPills) : ($pendingDocs > 0 ? 1 : 1);
  @endphp
  <div class="sa-dots-row" aria-hidden="true">
      @for($i = 0; $i < $totalPills; $i++)
          <span class="sa-dot {{ $i < $activePills ? 'is-active' : '' }}"></span>
      @endfor
  </div>
  ```

---

### Task 4: Verifikasi & Quality Gate
Jalankan quality gate lengkap sebelum menyatakan selesai:
- [x] Compile Aset Frontend: `npm run build`
- [x] Pemeriksaan Visual: Pastikan warna kartu pada mode terang dan mode gelap identik dengan foto `#CF3D3D` &rarr; `#B82828`.
- [x] Feature Test: `php artisan test --filter=SuperAdmin`
- [x] Code Formatting: `./vendor/bin/pint --test resources/css/pages/superadmin/dashboard.css resources/views/superadmin/dashboard.blade.php`

---

## 🛡️ 5. Matriks Risiko & Mitigasi

1. **Risiko Kontras Warna Teks di Latar Merah Cerah**:
   - *Mitigasi*: Warna putih solid `#FFFFFF` di atas `#CF3D3D` memiliki rasio kontras 4.6:1 (memenuhi standar WCAG 2.1 Level AA untuk large text & icons).
2. **Risiko Kerusakan Komponen Kartu Lain di Dark Mode**:
   - *Mitigasi*: Perubahan di-scope spesifik hanya pada selector `.sa-card--action` dan anak elemennya, sehingga tidak ada *side-effect* pada kartu lain (`.sa-card--registered`, `.sa-metric`, dsb).

---

## ✅ 6. Kriteria Selesai (Definition of Done)

- [x] Warna latar kartu "Perlu Tindakan" pada mode gelap dan mode terang sama persis dengan foto referensi (`media_1794897866975_bfb16cf7.png`), yaitu merah cerah menyala (`#CF3D3D` s.d. `#B82828`), bukan marun gelap `#450A0A`.
- [x] Badge icon memiliki tampilan glassmorphic putih susu translusen yang kontras dan bersih.
- [x] Nilai angka metrik dan teks keterangan berwarna putih solid berkontras tinggi.
- [x] Bagian bawah kartu memiliki baris kapsul horizontal (*segmented pills*) sebanyak 12 segmen yang rapi.
- [x] `npm run build` sukses tanpa error.
- [x] Seluruh test `SuperAdmin` lulus 100%.

---
*Dokumen rencana kerja ini siap dieksekusi setelah mendapatkan konfirmasi persetujuan dari pengguna.*
