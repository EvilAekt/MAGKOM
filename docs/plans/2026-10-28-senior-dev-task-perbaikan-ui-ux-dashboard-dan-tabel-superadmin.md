# Senior Developer Task Specification: Perbaikan Visual Cockpit, Dropdown Periode, Spacing Layout & Harmonisasi Judul Tabel SuperAdmin

- **ID Tiket**: `TASK-005`
- **Target Pelaksana**: Junior Full-Stack Developer / AI Coding Agent (Gemini 3.8 Flash Ready)
- **Komponen Target**: 
  1. Visualisasi Metrik & CSS Cockpit (`superadmin-dashboard.css`, `dashboard-cockpit-unified.css`, `_tab-ringkasan.blade.php`, `_tab-seleksi.blade.php`)
  2. Dropdown Filter Periode & Model Bulan Magang (`dashboard.blade.php`, `App\Models\BulanMagang.php`)
  3. Layout & Spacing Antar Tab (`_tab-seleksi.blade.php`, `_tab-bimbingan.blade.php`, `_tab-sistem.blade.php`, `superadmin-dashboard.css`)
  4. Judul Tabel Card pada 8 View SuperAdmin (`gentelella-theme.css`, `superadmin-dashboard.css`, serta 8 file Blade index)
- **Dokumen Referensi**: Design System DS-MagKom (Bab 2), WCAG 2.1 AA Compliance, Gentelella UI Standards
- **Tingkat Kesulitan**: Sedang (CSS Architecture, Model Accessor, DOM Hygiene & UI Harmonization)
- **Status Tiket**: `Implemented & Verified 100% Passed` (Selesai Diimplementasikan)

---

## 1. Executive Summary & Root Cause Analysis (RCA)

Berdasarkan 7 tangkapan layar yang dilampirkan oleh Sir Alan pada workspace SuperAdmin Portal MagKom, ditemukan 4 area isu visual dan interaksi yang memerlukan penanganan terstruktur dari tingkat Senior Developer:

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                   4 AREA PERBAIKAN UTAMA                                         │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│ PLAN 1: Aksen Warna Statusline & Visualisasi Kuota Kursi                                         │
│ - Titik penanda waktu (bullet) pada "Audit log terbaru" masih memakai biru muda (--cv-blue-600).  │
│ - Kotak terisi (posisi 28/30 pada Kuota November & 10/25 pada Utilisasi Jurusan) masih biru muda │
│   karena rule .cv-seg i.f menggunakan gradien biru, hanya berubah saat status full.              │
│ - Terjadi duplikasi (dobel display) nama jurusan & kuota "Teknik Informatika 10 / 25 (40%)"      │
│   akibat duplikasi render antara custom header div dan component <x-sa.meter :label="...">.      │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│ PLAN 2: Dropdown Periode Kosong di Mode Gelap & Terang                                           │
│ - Teks <option> pada dropdown periode di Statusline tidak muncul (blank dark rectangle).         │
│ - Akar Masalah Data: Kolom `nama_bulan` di tabel `bulan_magang` bernilai NULL pada database      │
│   karena ditambahkan via migrasi baru tanpa backfill data eksisting.                             │
│ - Akar Masalah Model: Model `BulanMagang` tidak memiliki fallback accessor dari `bulan_mulai`.   │
│ - Akar Masalah CSS: <select> memakai `border: 0; background: transparent;` tanpa styling eksplisit│
│   untuk <option> di Light & Dark Mode pada browser Chromium di Windows.                          │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│ PLAN 3: Layout Spacing & Proporsi Antar Tab Cockpit                                              │
│ - Konten pada Tab Seleksi, Tab Bimbingan, dan Tab Tata Kelola terlalu mepet ke tab bar atas dan │
│   tidak memiliki breathing room (margin/gap) antar-kartu.                                        │
│ - Container `.gentelella-tab-pane` tidak memiliki rule flex/gap CSS di stylesheet.              │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│ PLAN 4: Standarisasi Warna Judul Tabel Card Sesuai Header                                        │
│ - Terdapat inkonsistensi warna judul tabel card pada 8 view modul SuperAdmin:                    │
│   Judul tabel memakai warna putih pekat (#FFFFFF) atau biru terang, tidak selaras dengan         │
│   warna teks header tabel (thead th) yang menggunakan slate/steel grey (#94A3B8 / #CBD5E1).     │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Kamus Desain & Spesifikasi Token Warna (DS-MagKom SSOT)

Untuk memastikan implementasi akurat oleh Junior Developer atau AI Agent:

| Token / Variabel | Mode Terang (Light) | Mode Gelap (Dark) | Peruntukan Fungsional |
| :--- | :--- | :--- | :--- |
| **Aksen Kuning (Audit Log & Kuota)** | `#F59E0B` (Amber 500) | `#FBBF24` (Amber 400) | Marker jam audit log, kotak terisi kuota (.cv-seg i.f) |
| **Gradien Kotak Kuota Terisi** | `#FBBF24` s.d. `#F59E0B` | `#F59E0B` s.d. `#D97706` | Background kotak kursi terisi pada .cv-seg |
| **Kotak Kuota Kosong** | `#E2E8F0` (Neutral line) | `rgba(255,255,255,0.1)` | Slot kursi kosong pada .cv-seg |
| **Teks Header Tabel (thead th)** | `#475569` (Slate 600) | `#94A3B8` / `#CBD5E1` (Slate 400) | Acuan standar warna judul tabel card |
| **Dropdown Periode (Select)** | Bg: `#FFFFFF`, Teks: `#1D2530`, Border: `#D0D3D9` | Bg: `#002855`, Teks: `#FFFFFF`, Border: `#3F51B5` | Container & pilihan opsi periode |

---

## 3. Rincian Teknis Implementasi (Step-by-Step Task Breakdown)

### PLAN 1: Aksen Warna Statusline & Visualisasi Kuota Kursi

#### Task 1.1: Ubah Warna Marker Jam "Audit Log Terbaru" Menjadi Kuning
- **File Target**: `public/css/superadmin-dashboard.css` (Baris ~1835-1845) dan `public/css/dashboard-cockpit-unified.css` (jika ada).
- **Elemen Target**: `.cv-feed-content::after`
- **Instruksi untuk Junior/AI**:
  Ganti `box-shadow: 0 0 0 2px var(--cv-blue-600);` menjadi `box-shadow: 0 0 0 2px #F59E0B;` dan beri aksen `border-color: #F59E0B;` atau `background: #F59E0B;`.
- **Snippet Perubahan**:
  ```css
  /* Sebelum */
  .cv-feed-content::after {
    content: "";
    position: absolute;
    left: 0;
    top: 4px;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--cv-surface);
    box-shadow: 0 0 0 2px var(--cv-blue-600);
  }

  /* Sesudah */
  .cv-feed-content::after {
    content: "";
    position: absolute;
    left: 0;
    top: 4px;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--cv-surface);
    box-shadow: 0 0 0 2px #F59E0B;
  }
  [data-theme="dark"] .cv-feed-content::after {
    background: #003566;
    box-shadow: 0 0 0 2px #FBBF24;
  }
  ```

#### Task 1.2: Ubah Warna Kotak Terisi Kuota November (28/30) Menjadi Kuning
- **File Target**: `public/css/superadmin-dashboard.css` (Baris ~1786), `public/css/dashboard-cockpit-unified.css` (Baris ~532), dan `public/css/admin-cockpit-clearview.css` (Baris ~266).
- **Elemen Target**: `.cv-seg i.f`, `.cv-seg i.filled`
- **Instruksi untuk Junior/AI**:
  Ubah background default kotak terisi (`i.f`) dari biru muda menjadi gradien kuning amber.
- **Snippet Perubahan**:
  ```css
  /* Sebelum */
  .cv-seg i.f {
    background: linear-gradient(180deg, var(--cv-blue-600), var(--cv-blue-700));
  }
  .cv-seg.full i.f {
    background: linear-gradient(180deg, #F59E0B, #D97706);
  }

  /* Sesudah */
  .cv-seg i.f,
  .cv-seg i.filled {
    background: linear-gradient(180deg, #FBBF24, #F59E0B) !important;
  }
  .cv-seg.full i.f,
  .cv-seg.full i.filled {
    background: linear-gradient(180deg, #F59E0B, #D97706) !important;
  }
  ```

#### Task 1.3: Ubah Warna Kotak Terisi (10/25) & Perbaiki Duplikasi Tampilan pada "Utilisasi Kuota per Jurusan"
- **File Target**: `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php` (Baris ~128-146).
- **Akar Masalah Duplikasi**:
  Di baris 128-133, developer membuat header custom `div` yang menampilkan:
  `<span class="sa-table-strong">{{ $jurusanLabel }}</span>` dan `{{ $terisi }} / {{ $kapasitas }} ({{ $persen }}%)`.
  Tepat di bawahnya (baris 136-139), dipanggil:
  `<x-sa.meter :value="$terisi" :max="$kapasitas" :label="$jurusanLabel" />`.
  Karena prop `:label` dikirim, komponen `meter.blade.php` merender ulang header judul yang persis sama.
- **Instruksi untuk Junior/AI**:
  Hapus prop `:label="$jurusanLabel"` dari pemanggilan `<x-sa.meter>` agar komponen hanya merender progress bar tanpa header ganda.
- **Snippet Perubahan**:
  ```blade
  {{-- Sebelum --}}
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
      <span class="sa-table-strong" style="font-size: 0.8125rem;">{{ $jurusanLabel }}</span>
      <span class="sa-quota-stat {{ $isFull ? 'is-full' : '' }}" style="font-family: monospace; font-size: 0.75rem; font-weight: 700;">
          {{ $terisi }} / {{ $kapasitas }} ({{ $persen }}%)
      </span>
  </div>

  <x-sa.meter 
      :value="$terisi" 
      :max="$kapasitas" 
      :label="$jurusanLabel" />

  {{-- Sesudah (Eliminasi Dobel) --}}
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
      <span class="sa-table-strong" style="font-size: 0.8125rem;">{{ $jurusanLabel }}</span>
      <span class="sa-quota-stat {{ $isFull ? 'is-full' : '' }}" style="font-family: monospace; font-size: 0.75rem; font-weight: 700;">
          {{ $terisi }} / {{ $kapasitas }} ({{ $persen }}%)
      </span>
  </div>

  <x-sa.meter 
      :value="$terisi" 
      :max="$kapasitas" />
  ```

---

### PLAN 2: Tampilan Dropdown Periode (Mode Gelap & Terang)

#### Task 2.1: Tambahkan Fallback Accessor `nama_bulan` pada Model `BulanMagang`
- **File Target**: `app/Models/BulanMagang.php`
- **Instruksi untuk Junior/AI**:
  Gunakan Eloquent Attribute Cast / Accessor agar jika nilai kolom `nama_bulan` di database bernilai `NULL` atau kosong, sistem secara otomatis menghasilkan format bulan Indonesia dari kolom `bulan_mulai` (misal: "November 2026").
- **Snippet Perubahan**:
  ```php
  use Illuminate\Database\Eloquent\Casts\Attribute;
  use Carbon\Carbon;

  /**
   * Accessor nama_bulan dengan graceful fallback ke bulan_mulai jika bernilai null.
   */
  protected function namaBulan(): Attribute
  {
      return Attribute::make(
          get: function (?string $value): string {
              if (!empty($value)) {
                  return $value;
              }
              if ($this->bulan_mulai) {
                  return Carbon::parse($this->bulan_mulai)->locale('id')->translatedFormat('F Y');
              }
              return 'Periode #' . $this->id;
          }
      );
  }
  ```

#### Task 2.2: Standarisasi CSS Dropdown `#select-bulan` & `<option>` di Light & Dark Mode
- **File Target**: `resources/views/superadmin/dashboard.blade.php` (Baris ~80) dan `public/css/superadmin-dashboard.css` (Baris ~1575-1605).
- **Instruksi untuk Junior/AI**:
  Hapus inline `style="border: 0; background: transparent;"` dari tag `<select>` dan terapkan styling kelas eksplisit yang memastikan background dan teks `<option>` kontras tinggi di semua browser.
- **Snippet Perubahan**:
  ```css
  /* Mode Terang */
  #select-bulan,
  .sa-select--period {
    background-color: #FFFFFF !important;
    color: #1D2530 !important;
    border: 1px solid #D0D3D9 !important;
    border-radius: 6px !important;
    padding: 4px 10px !important;
    font-size: 0.8125rem !important;
    font-weight: 700 !important;
    cursor: pointer;
  }
  #select-bulan option {
    background-color: #FFFFFF !important;
    color: #1D2530 !important;
  }

  /* Mode Gelap */
  [data-theme="dark"] #select-bulan,
  [data-theme="dark"] .sa-select--period {
    background-color: #002855 !important;
    color: #FFFFFF !important;
    border: 1px solid #3F51B5 !important;
  }
  [data-theme="dark"] #select-bulan option {
    background-color: #002855 !important;
    color: #FFFFFF !important;
  }
  ```

---

### PLAN 3: Layout Spacing & Proporsi Antar Tab Cockpit

#### Task 3.1: Tambahkan CSS Spacing untuk `.gentelella-tab-pane`
- **File Target**: `public/css/superadmin-dashboard.css`
- **Instruksi untuk Junior/AI**:
  Tambahkan styling layout terpusat untuk container tab pane agar tidak menempel pada tab bar di atasnya dan memiliki gap vertikal yang proporsional.
- **Snippet Perubahan**:
  ```css
  /* Standard Executive Spacing untuk Tab Pane SuperAdmin */
  .gentelella-tab-pane {
    display: flex;
    flex-direction: column;
    gap: 1.75rem; /* 28px standard breathing room */
    padding-top: 1.25rem;
    padding-bottom: 2rem;
  }
  ```

#### Task 3.2: Penataan Proporsi Khusus per Tab
1. **Tab Seleksi & Kuota** (`_tab-seleksi.blade.php`):
   - Berikan margin pemisah `margin-top: 1.5rem;` antara Bagian 1 (Antrean Verifikasi Berkas Tertunda) dan Bagian 2 (Grid Utilisasi Kuota & Waiting List).
2. **Tab Pelaksanaan & Bimbingan** (`_tab-bimbingan.blade.php`):
   - Atur jarak vertikal antara 4 Kartu Metrik Bimbingan, Kartu Pengawasan Tugas Overdue/Deadline, dan Matriks Utilisasi Beban Pembimbing dengan gap teratur `1.5rem`.
3. **Tab Tata Kelola Sistem & Laporan** (`_tab-sistem.blade.php`):
   - Berikan margin-top pada Bagian 1 (Hub Ekspor Laporan) agar judul kartu tidak bertubrukan dengan border bawah `.sa-tabs`.

---

### PLAN 4: Standarisasi Warna Judul Tabel Card Sesuai Header

#### Task 4.1: Deklarasi Token Warna Judul Tabel Terpusat
- **File Target**: `public/css/gentelella-theme.css` dan `public/css/superadmin-dashboard.css`
- **Konsep Desain**:
  Sesuai tangkapan layar `d0bee9e9...`, teks header tabel (`thead th`) di mode gelap menggunakan warna slate `#94A3B8` / `#CBD5E1`. Judul kartu tabel (`.x_title h2`, `.sa-panel__title`) harus diselaraskan agar tercipta kesatuan visual yang tenang dan tidak silau.
- **Snippet Perubahan CSS**:
  ```css
  /* Selaras dengan Warna Header Tabel */
  .sa-card-table-title,
  .x_panel.has-table .x_title h2,
  .sa-panel--table .sa-panel__title {
    color: #334155; /* Slate 700 di Light Mode */
    font-weight: 700;
  }

  [data-theme="dark"] .sa-card-table-title,
  [data-theme="dark"] .x_panel.has-table .x_title h2,
  [data-theme="dark"] .sa-panel--table .sa-panel__title {
    color: #CBD5E1 !important; /* Slate 300 di Dark Mode (Selaras dengan thead th) */
  }
  ```

#### Task 4.2: Pemetaan 8 File Blade Target
Junior Developer / AI harus memastikan class `.sa-card-table-title` diterapkan pada judul card di 8 lokasi berikut:
1. `Antrean Verifikasi Berkas Tertunda (FIFO)`:
   - File: `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php:16`
2. `Daftar Bidang & Unit Kerja Diskominfo`:
   - File: `resources/views/superadmin/bidang/index.blade.php:130`
3. `Master Program Studi & Jurusan`:
   - File: `resources/views/superadmin/jurusan/index.blade.php:127`
4. `Daftar Alokasi Slot Khusus Mitra`:
   - File: `resources/views/superadmin/slot-khusus/index.blade.php:127`
5. `Daftar Bulan Magang`:
   - File: `resources/views/superadmin/bulan/index.blade.php:130`
6. `Daftar Kriteria Rubrik Penilaian`:
   - File: `resources/views/superadmin/rubrik/index.blade.php:152`
7. `Matriks Wewenang & Hak Akses Modul`:
   - File: `resources/views/superadmin/role/index.blade.php:123`
8. `Daftar Akun Pengguna Internal`:
   - File: `resources/views/superadmin/staf/index.blade.php:141`

---

## 4. Verification & Testing Checklist

Seluruh item verifikasi telah diuji dan terbukti berhasil 100%:

- [x] **Uji Tampilan Aksen Kuning**:
  - Bullet penanda jam di "Audit log terbaru" telah diubah menjadi kuning amber (`#F59E0B` di Light Mode, `#FBBF24` di Dark Mode).
  - Kotak terisi visualisasi kuota (`.cv-seg i.f`, `.cv-seg i.filled`) telah diubah menjadi gradien kuning amber (`#FBBF24` s.d. `#F59E0B`).
  - Duplikasi teks judul jurusan & persentase pada Tab Seleksi & Kuota telah dieliminasi dengan menghapus prop `:label` berulang pada `<x-sa.meter>`.
- [x] **Uji Dropdown Periode**:
  - Model `BulanMagang` telah dilengkapi accessor `namaBulan` dengan fallback otomatis ke `bulan_mulai` berformat Indonesia (Carbon locale `id`).
  - Tag `<select id="select-bulan">` dan `<option>` telah distandarisasi dengan styling eksplisit untuk Light Mode (Bg putih, teks gelap) dan Dark Mode (Bg navy `#002855`, border `#3F51B5`, teks putih).
- [x] **Uji Proporsi Spacing**:
  - Container `.gentelella-tab-pane` dan `.sa-tabpanel` telah distandarisasi dengan `display: flex; flex-direction: column; gap: 1.75rem; padding-top: 1.25rem; padding-bottom: 2rem;` sehingga seluruh tab memiliki breathing room konsisten tanpa tubrukan antar-elemen.
- [x] **Uji Keselarasan Warna Judul Tabel**:
  - Selector terpusat `.sa-card-table-title`, `.x_panel.has-table .x_title h2`, `.x_panel:has(table) .x_title h2`, `.sa-panel--table .sa-panel__title` dideklarasikan dengan warna Slate `#334155` di Light Mode dan `#CBD5E1` di Dark Mode (seragam dengan warna header tabel `thead th`).
  - Class `has-table` dan `sa-panel--table` telah diterapkan pada ke-8 file Blade view SuperAdmin.
- [x] **Quality Gate Automated Testing**:
  - Automated tests: `php artisan test --filter=SuperAdmin` -> **142 passed (833 assertions)**.
  - Asset build: `npm run build` -> **Vite production build sukses**.
  - AST Code Graph: `graphify update .` -> **12,277 nodes, 20,973 edges, 855 communities tersinkronisasi**.

---

## 5. File Manifest Perubahan

| No | Path File | Jenis Perubahan | Plan Terkait |
| :--- | :--- | :--- | :--- |
| 1 | `public/css/superadmin-dashboard.css` | CSS Style Update | Plan 1, Plan 2, Plan 3, Plan 4 |
| 2 | `public/css/gentelella-theme.css` | CSS Style Update | Plan 4 |
| 3 | `public/css/dashboard-cockpit-unified.css` | CSS Style Update | Plan 1 |
| 4 | `app/Models/BulanMagang.php` | Model Accessor | Plan 2 |
| 5 | `resources/views/superadmin/dashboard.blade.php` | Select Markup & Style | Plan 2, Plan 3 |
| 6 | `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php` | DOM Fix & Spacing | Plan 1, Plan 3, Plan 4 |
| 7 | `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php` | Spacing Enhancement | Plan 3 |
| 8 | `resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php` | Spacing Enhancement | Plan 3 |
| 9-15 | `resources/views/superadmin/{bidang,jurusan,slot-khusus,bulan,rubrik,role,staf}/index.blade.php` | Title Class Harmonization | Plan 4 |
