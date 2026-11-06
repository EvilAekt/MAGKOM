# SENIOR DEVELOPER TASK & TECHNICAL PRD
## Penambahan Filter Pencarian Peserta pada Halaman Laporan Peserta (Laporan Kinerja Mentoring)

**Target Audience:** Junior Developers & AI Agents (Gemini 3.8 Flash / Claude Haiku / Cursor)  
**Status:** DRAFT / PROPOSED (Menunggu Konfirmasi User)  
**Tanggal:** 06 November 2026  
**Modul:** Portal Pembimbing — Laporan Peserta ([`pembimbing.laporan-kinerja.index`](file:///g:/MAGKOM/resources/views/pembimbing/laporan-kinerja/index.blade.php))  
**Referensi Desain:** Search Filter Bar pada Modul Tugas & Progres Magang ([`tugas.index`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/index.blade.php))  

---

## 1. Executive Summary & Objective

### 1.1 Problem Statement
Pada halaman **Laporan Peserta** (`resources/views/pembimbing/laporan-kinerja/index.blade.php`), komponen filter saat ini hanya menyediakan filter dropdown:
- `Filter Peserta` (dropdown `penempatan_id`)
- `Filter Status` (dropdown `status`)
- `Filter Bulan` (dropdown `bulan`)

Ketiadaan input pencarian teks (`q`) menyulitkan pembimbing ketika memiliki banyak mentee bimbingan. Pembimbing harus menggulir daftar dropdown yang panjang alih-alih mengetik nama, kampus, atau NIM mentee secara instan. Selain itu, terdapat inkonsistensi antarmuka dengan modul **Tugas & Progres Magang** dan **Logbook Peserta** yang telah memiliki komponen `Pencarian Peserta` dengan input icon search.

### 1.2 Objective & Acceptance Criteria
1. **Input Pencarian Peserta di Filter Bar**:
   - Menambahkan field `Pencarian Peserta` di kolom pertama filter bar (`flex: 1.3`).
   - Menyediakan input pencarian dengan placeholder: `Cari nama, kampus, NIM...` dan icon kaca pembesar SVG di sebelah kiri.
   - Mempertahankan value pencarian (`value="{{ request('q') }}"`) setelah filter diterapkan.
2. **Penyelarasan Label & Dropdown**:
   - Mengubah label `Filter Peserta` menjadi `Filter Nama Peserta` agar konsisten dengan halaman Tugas & Logbook.
   - Mempertahankan dropdown `Filter Status` dan `Filter Bulan`.
   - Tombol filter `[🔍 Filter]` dan tombol `Reset` otomatis muncul ketika salah satu dari filter `q`, `penempatan_id`, `status`, atau `bulan` aktif.
3. **Backend Search Query Synchronization**:
   - Menambahkan filter pencarian keyword `q` pada `LaporanKinerjaController`:
     - Method `index()`: Menyaring nama peserta, institusi kampus/sekolah, dan NIM/NISN.
     - Method `export()`: Memastikan data yang diunduh (Excel/CSV) mematuhi pencarian aktif.
     - Method `cetak()`: Memastikan dokumen cetak PDF resmi mematuhi pencarian aktif.
4. **Kepatuhan Arsitektur MAGKOM**:
   - Dilarang menambahkan tag `<style>` atau `<script>` di Blade.
   - Seluruh styling ditempatkan di `resources/css/pages/pembimbing/laporan-kinerja-index.css` dan dikompilasi via Vite (`npm run build`).
5. **Zero Regresi**:
   - Tile stats, popover ekspor berkas, pagination bar, dan pengurutan tabel tetap berjalan 100% normal.
   - Automated Feature Tests tetap berstatus hijau (PASS).

---

## 2. File Mapping & Impact Analysis

| Path File | Tanggung Jawab | Jenis Perubahan |
|---|---|---|
| `resources/views/pembimbing/laporan-kinerja/index.blade.php` | View Blade Laporan Peserta | **Update Markup**: Tambahkan container `Pencarian Peserta` dengan input `name="q"`, ubah label dropdown peserta, dan sesuaikan tombol reset |
| `app/Http/Controllers/Pembimbing/LaporanKinerjaController.php` | Controller Logika Laporan & Ekspor | **Update Backend Query**: Tambahkan filter `whereHas('peserta', ...)` untuk keyword `q` di method `index()`, `export()`, dan `cetak()` |
| `resources/css/pages/pembimbing/laporan-kinerja-index.css` | Stylesheet Halaman Laporan | **Update Stylesheet**: Tambahkan selector `.peserta-search-input-wrap`, `.peserta-search-input`, `.peserta-search-icon`, dan styling responsif/dark mode |
| `tests/Feature/PembimbingLaporanKinerjaViewTest.php` | Automated Feature Test | **Update Test Suite**: Tambahkan test assertion untuk memverifikasi pencarian `q` berdasarkan nama, kampus, dan NIM |

---

## 3. Step-by-Step Implementation Guide

### Langkah 1: Perbarui Controller Backend (`app/Http/Controllers/Pembimbing/LaporanKinerjaController.php`)

Tambahkan blok penanganan parameter pencarian `q` pada:
1. Method `index(Request $request)`: Sekitar baris 48 (sebelum filter `penempatan_id`).
2. Method `export(Request $request)`: Sekitar baris 245.
3. Method `cetak(Request $request)`: Sekitar baris 458.

#### 🟢 Snippet Logika Pencarian:
```php
        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->whereHas('peserta', function ($sub) use ($keyword) {
                $sub->where('nama', 'like', "%{$keyword}%")
                    ->orWhereHas('profil', function ($p) use ($keyword) {
                        $p->where('institusi', 'like', "%{$keyword}%")
                            ->orWhere('nim_nisn', 'like', "%{$keyword}%");
                    });
            });
        }
```

---

### Langkah 2: Perbarui View Blade (`resources/views/pembimbing/laporan-kinerja/index.blade.php`)

**Lokasi Baris:** Sekitar baris 205-249 pada file `resources/views/pembimbing/laporan-kinerja/index.blade.php`.

#### 🔴 Kode Lama:
```blade
    {{-- 2.5 Filter Bar 3-Dimensi: Nama Peserta, Status, dan Bulan --}}
    <div class="laporan-filter-card">
        <form method="GET" action="{{ route('pembimbing.laporan-kinerja.index') }}" class="laporan-filter-form" id="formFilterLaporan">
            <div class="filter-group">
                <label for="filterPeserta">Filter Peserta</label>
                <select name="penempatan_id" id="filterPeserta" class="filter-control" onchange="this.form.submit()">
                    <option value="">Semua Peserta Bimbingan</option>
                    @foreach($penempatanOptions ?? [] as $pen)
                        <option value="{{ $pen->id }}" {{ request('penempatan_id') == $pen->id ? 'selected' : '' }}>
                            {{ $pen->peserta?->nama }} ({{ $pen->peserta?->profil?->institusi ?? 'Institusi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="filterStatus">Filter Status</label>
                ...
```

#### 🟢 Kode Baru:
```blade
    {{-- 2.5 Filter Bar: Pencarian Teks, Nama Peserta, Status, dan Bulan --}}
    <div class="laporan-filter-card">
        <form method="GET" action="{{ route('pembimbing.laporan-kinerja.index') }}" class="laporan-filter-form" id="formFilterLaporan">
            {{-- Kolom 1: Pencarian Peserta (Nama, Kampus, NIM) --}}
            <div class="filter-group filter-group-search" style="flex: 1.3;">
                <label for="inputSearchLaporan">Pencarian Peserta</label>
                <div class="peserta-search-input-wrap">
                    <input type="text" 
                           name="q" 
                           id="inputSearchLaporan" 
                           value="{{ request('q') }}" 
                           placeholder="Cari nama, kampus, NIM..." 
                           class="peserta-search-input">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="peserta-search-icon" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            {{-- Kolom 2: Dropdown Filter Nama Peserta --}}
            <div class="filter-group">
                <label for="filterPeserta">Filter Nama Peserta</label>
                <select name="penempatan_id" id="filterPeserta" class="filter-control" onchange="this.form.submit()">
                    <option value="">Semua Peserta Bimbingan</option>
                    @foreach($penempatanOptions ?? [] as $pen)
                        <option value="{{ $pen->id }}" {{ request('penempatan_id') == $pen->id ? 'selected' : '' }}>
                            {{ $pen->peserta?->nama }} ({{ $pen->peserta?->profil?->institusi ?? 'Institusi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kolom 3: Dropdown Filter Status --}}
            <div class="filter-group">
                <label for="filterStatus">Filter Status</label>
                <select name="status" id="filterStatus" class="filter-control" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ request('status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif / Selesai</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            {{-- Kolom 4: Dropdown Filter Bulan --}}
            <div class="filter-group">
                <label for="filterBulan">Filter Bulan</label>
                <select name="bulan" id="filterBulan" class="filter-control" onchange="this.form.submit()">
                    <option value="">Semua Bulan</option>
                    @foreach($daftarBulan ?? [] as $val => $label)
                        <option value="{{ $val }}" {{ request('bulan') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Aksi Submit Filter & Reset --}}
            <div class="filter-actions">
                <button type="submit" class="btn-filter-apply">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['q', 'penempatan_id', 'status', 'bulan']))
                    <a href="{{ route('pembimbing.laporan-kinerja.index') }}" class="btn-filter-reset">Reset</a>
                @endif
            </div>
        </form>
    </div>
```

---

### Langkah 3: Perbarui Stylesheet (`resources/css/pages/pembimbing/laporan-kinerja-index.css`)

Tambahkan komponen styling untuk search input wrap di `resources/css/pages/pembimbing/laporan-kinerja-index.css`:

```css
/* Search Input Wrapper for Laporan Filter */
.laporan-filter-form .peserta-search-input-wrap {
    position: relative;
    width: 100%;
}

.laporan-filter-form .peserta-search-input {
    width: 100%;
    height: 38px;
    padding: 0 12px 0 36px;
    border-radius: 8px;
    border: 1px solid var(--neutral-400, #D0D3D9);
    font-size: 0.875rem;
    background: var(--neutral-100, #F8FAFC);
    color: var(--neutral-800, #1D2530);
    outline: none;
    transition: border-color 0.2s, background-color 0.2s;
    box-sizing: border-box;
}

.laporan-filter-form .peserta-search-input:focus {
    border-color: #0641CC;
    background: #ffffff;
}

.laporan-filter-form .peserta-search-icon {
    position: absolute;
    left: 10px;
    top: 11px;
    color: #94A3B8;
    pointer-events: none;
}

/* Dark Mode Support */
[data-theme="dark"] .laporan-filter-form .peserta-search-input {
    background: #0F172A;
    border-color: #334155;
    color: #F8FAFC;
}

[data-theme="dark"] .laporan-filter-form .peserta-search-input:focus {
    border-color: #3B82F6;
    background: #1E293B;
}

[data-theme="dark"] .laporan-filter-form .peserta-search-icon {
    color: #64748B;
}
```

---

## 4. Quality Assurance (QA) Checklist

| No | Poin Pemeriksaan | Kriteria Sukses |
|---|---|---|
| 1 | **Tampilan Filter Bar** | Field `Pencarian Peserta` tampil di kolom pertama dengan icon search dan placeholder `Cari nama, kampus, NIM...`. Label dropdown kedua adalah `Filter Nama Peserta`. |
| 2 | **Pencarian Nama** | Mengetik nama mentee (misal "Indra") lalu submit menyaring daftar tabel hanya menampilkan mentee bernama Indra. Nilai input tetap terisi. |
| 3 | **Pencarian Kampus** | Mengetik nama kampus (misal "UGM" atau "Universitas Gadjah Mada") menyaring daftar mentee yang berasal dari kampus tersebut. |
| 4 | **Pencarian NIM/NISN** | Mengetik nomor NIM mentee menyaring mentee dengan nomor identitas tersebut secara akurat. |
| 5 | **Kombinasi Filter** | Mengombinasikan keyword `q` dengan `status` atau `bulan` menghasilkan subset data yang benar tanpa error SQL. |
| 6 | **Tombol Reset** | Tautan tombol `Reset` tampil saat `q` terisi, dan saat diklik mengembalikan tabel ke daftar penuh. |
| 7 | **Ekspor Berkas** | Mengunduh file Excel/CSV setelah filter `q` diterapkan hanya mengekspor baris yang sesuai hasil pencarian. |
| 8 | **Build & Tests** | `npm run build` sukses, `php artisan test --filter=PembimbingLaporanKinerjaViewTest` passed 100%, `./vendor/bin/pint --test` lolos. |

---

## 5. Architectural Guardrails (Wajib Dipatuhi)

1. **JANGAN Menulis CSS atau JS di Blade**: Styling input search wajib berada di `resources/css/pages/pembimbing/laporan-kinerja-index.css`.
2. **Keamanan Query (Anti-SQL Injection)**: Gunakan parameter binding Eloquent `whereHas('peserta', ...)` dengan wildcard escaping standar.
3. **Preserve IDOR Boundary**: Filter pencarian wajib tetap berada dalam lingkup pembimbing terkait (`where('pembimbing_id', $user->id)`).

