# Rencana Kerja: Perbaikan Card Metrik Penempatan (Menghilangkan Outer Card Background & Mempertahankan Garis Aksen Warna Atas)

**Tanggal Dokumen**: 2026-11-04  
**Modul**: Administrasi MagKom — Penempatan Peserta (`admin/penempatan`)  
**Status**: ✅ Selesai Diimplementasikan & Diverifikasi  

---

## 1. Ringkasan Kebutuhan & Analisis Visual

### 1.1 Masalah Eksisting Berdasarkan Tangkapan Layar Pengguna
Pada tangkapan layar pengguna (`media_1793758241803_d8000454.png`), 4 kartu metrik statistik (*Hero Stats Cards*) di halaman **Penempatan Peserta** (`resources/views/admin/penempatan/index.blade.php`) menampilkan efek **"kartu di dalam kartu" (double/nested card container)**:
1. **Outer Container**: Kelas `.admin-stat-card` membungkus kartu dengan `background: #ffffff`, border keliling abu-abu, padding `1.25rem 1.5rem`, drop shadow, dan garis aksen atas 3.5px (`border-top`).
2. **Inner Container**: Di dalamnya, komponen `<x-gentelella.tile-stat>` merender elemen `.tile_stats_inner` yang *juga* memiliki `background: #ffffff`, border keliling, padding `1rem 1.15rem`, dan drop shadow.
3. **Dampak Visual**: Kartu terlihat bertingkat/menumpuk (*redundant double background card frame*), menghasilkan ruang kosong yang tidak perlu dan tampilan yang tampak tidak rapi.

### 1.2 Tujuan
1. **Hilangkan Card Background Luar (`card bg diilangin`)**: Menghapus background putih, border ganda, dan padding berlebih pada elemen pembungkus luar (`.admin-stat-card` / `.tile_stats_count`), sehingga kartu metrik tampil sebagai **satu lapis kartu bersih (*single-layer modern card*)**.
2. **Pertahankan Garis Aksen Warna Atas (`garis vertikal warna tetap dikasih`)**: Memindahkan garis aksen warna atas (`border-top: 3.5px solid ...`) langsung ke elemen kartu utama (`.tile_stats_inner`), dengan warna tematik yang serasi:
   - **Total Penempatan** ➔ Garis Biru Royal (`#2563EB`)
   - **Peserta Magang Aktif** ➔ Garis Hijau Emerald (`#10B981`)
   - **Selesai Magang** ➔ Garis Indigo Ungu (`#4F46E5`)
   - **Jalur Khusus Mitra** ➔ Garis Amber Emas (`#F59E0B`)
   - *(Kondisional) **Menunggu Plotting** ➔ Garis Oranye Warning (`#EA580C`)*
3. **Jaga Kompatibilitas Test Visual**: Tetap mempertahankan kelas `admin-stat-card admin-stat-card--blue`, dll. pada komponen `<x-gentelella.tile-stat>` agar assertion test di `AdminPenempatanHeroStatsVisualTest` tetap **100% PASS**.
4. **Pembersihan Markup Blade**: Memperbaiki tag penutup ganda `</div>` pada baris 713 dan membersihkan CSS blok `<style>` di Blade sesuai aturan ketat `AGENTS.md`.

---

## 2. Evaluasi Pendekatan Teknis

| Kriteria | Opsi A: Reset Container Luar & Pindahkan Border-Top ke `.tile_stats_inner` (Direkomendasikan) | Opsi B: Hapus Seluruh Background Kartu (Transparan Total) |
| :--- | :--- | :--- |
| **Deskripsi** | Menjadikan pembungkus luar transparan (`padding: 0; background: transparent; border: none;`), dan memindahkan garis aksen atas `3.5px` langsung ke kartu `.tile_stats_inner`. | Membuat seluruh kartu dan inner-nya transparan tanpa background putih dan tanpa border samping/bawah. |
| **Konsistensi UI/UX** | **Sangat Tinggi (100% Paritas)**: Selaras dengan modul Laporan Tahunan, Laporan Hub, Pendaftaran, dan standar Gentelella. Kartu tetap memiliki kontras yang kokoh terhadap canvas. | **Rendah**: Teks dan sparkline akan melayang tanpa kontras batas kartu yang jelas, menyimpang dari desain dashboard lainnya. |
| **Penyelesaian Masalah** | **Tuntas**: Menghilangkan efek "kartu ganda", menghasilkan 1 kartu metrik presisi dengan garis warna atas. | Berisiko membuat tampilan terlihat botak (*flat/uncontained*). |
| **Dukungan Dark Mode** | Otomatis rapi dengan variabel `[data-theme="dark"]`. | Sulit dibaca pada kontras background gelap. |
| **Kepatuhan Aturan** | Mematuhi `AGENTS.md` (aturan CSS di `resources/css/`). | Mematuhi aturan, namun estetika berkurang. |

> **Rekomendasi**: Memilih **Opsi A**, yaitu menghilangkan outer background container sehingga kartu tampil sebagai satu kartu metrik yang rapi dengan garis aksen warna atas pada `.tile_stats_inner`.

---

## 3. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/css/pages/admin/dashboard.css` | **Modify** | 1. Reset wrapper `.tile_stats_count.admin-stat-card` agar memiliki background transparan, tanpa border, tanpa shadow, dan padding grid yang pas.<br>2. Arahkan modifier warna `.admin-stat-card--* .tile_stats_inner` untuk menyematkan `border-top: 3.5px solid <color>` langsung pada kartu tunggal.<br>3. Selaraskan varian dark mode `[data-theme="dark"]`. |
| `public/css/dashboard-cockpit-unified.css` | **Modify** | Sinkronisasi aturan CSS yang sama untuk fallback stylesheet runtime. |
| `resources/views/admin/penempatan/index.blade.php` | **Modify** | 1. Hapus deklarasi redundant `.admin-stat-card` di blok `<style>` Blade yang menyebabkan double styling.<br>2. Perbaiki kelebihan tag penutup `</div>` pada baris 713.<br>3. Pertahankan kelas semantik `admin-stat-card admin-stat-card--*` untuk kompatibilitas test. |

---

## 4. Rincian Teknis Implementasi

### A. Aturan CSS di `resources/css/pages/admin/dashboard.css`
```css
/* =========================================================================
   14. Hero Stat Cards Single-Layer System (Penempatan Admin)
   ========================================================================= */
/* Reset outer column wrapper to avoid double-card nesting */
.tile_stats_count.admin-stat-card {
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  padding: 0 10px !important;
  margin-bottom: 10px;
}

/* Apply top accent line directly to the single tile card */
.admin-stat-card--blue .tile_stats_inner {
  border-top: 3.5px solid #2563EB !important;
}

.admin-stat-card--green .tile_stats_inner {
  border-top: 3.5px solid #10B981 !important;
}

.admin-stat-card--indigo .tile_stats_inner {
  border-top: 3.5px solid #4F46E5 !important;
}

.admin-stat-card--amber .tile_stats_inner {
  border-top: 3.5px solid #F59E0B !important;
}

.admin-stat-card--orange .tile_stats_inner {
  border-top: 3.5px solid #EA580C !important;
}

/* Dark Mode Harmonization */
[data-theme="dark"] .admin-stat-card--blue .tile_stats_inner {
  border-top-color: #3B82F6 !important;
}

[data-theme="dark"] .admin-stat-card--green .tile_stats_inner {
  border-top-color: #10B981 !important;
}

[data-theme="dark"] .admin-stat-card--indigo .tile_stats_inner {
  border-top-color: #6366F1 !important;
}

[data-theme="dark"] .admin-stat-card--amber .tile_stats_inner {
  border-top-color: #F59E0B !important;
}

[data-theme="dark"] .admin-stat-card--orange .tile_stats_inner {
  border-top-color: #F97316 !important;
}
```

### B. Pembersihan di `resources/views/admin/penempatan/index.blade.php`
- Menghapus aturan `.admin-stat-card` di dalam blok `<style>` pada baris 135–149 dan baris 362–374 yang menimpa styling.
- Menghapus ekstra `</div>` di baris 713 agar struktur DOM valid.

---

## 5. Permohonan Izin Perubahan Markup Blade (Sesuai AGENTS.md Bagian 3.3)

> [!IMPORTANT]
> **Alasan Perubahan Blade**:  
> 1. Menghapus deklarasi CSS `.admin-stat-card` lama di dalam blok `<style>` Blade yang bertentangan dengan Rule 3.1 & Rule 4 `AGENTS.md` (CSS harus berada di `resources/css/`).
> 2. Memperbaiki kesalahan sintaksis struktur DOM berupa duplikasi tag penutup `</div>` pada baris 713.
> 
> Tidak ada penambahan tag HTML atau restrukturisasi layout komponen baru pada Blade.

---

## 6. Langkah Implementasi (Checklist)

- [x] **Langkah 1**: Perbarui `resources/css/pages/admin/dashboard.css` dengan aturan reset outer container dan pemindahan `border-top` langsung ke `.tile_stats_inner`.
- [x] **Langkah 2**: Sinkronkan perubahan ke `public/css/dashboard-cockpit-unified.css`.
- [x] **Langkah 3**: Bersihkan deklarasi redundant `.admin-stat-card` dari tag `<style>` dan hapus ekstra penutup `</div>` pada `resources/views/admin/penempatan/index.blade.php`.
- [x] **Langkah 4**: Jalankan build asset Vite via `npm run build`.
- [x] **Langkah 5**: Jalankan test visual dan feature: `php artisan test tests/Feature/AdminPenempatanHeroStatsVisualTest.php tests/Feature/AdminChildPagesExtendedVisualTest.php`.
- [x] **Langkah 6**: Jalankan code formatter: `./vendor/bin/pint --test`.

---

## 7. Strategi Testing

1. **Automated Feature & Visual Tests**:
   - `tests/Feature/AdminPenempatanHeroStatsVisualTest.php`: Memverifikasi seluruh modifier class `admin-stat-card--blue`, `--green`, `--indigo`, `--amber` tetap hadir di respons HTML penempatan.
   - `tests/Feature/AdminChildPagesExtendedVisualTest.php`: Memverifikasi halaman admin penempatan dapat dirender tanpa exception dengan status 200 OK.
2. **Quality Gate**:
   - `npm run build`: Memastikan bundling Vite selesai tanpa warning/error.
   - `pint --test`: Memastikan tidak ada pelanggaran konvensi kode Laravel.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Efek kartu di dalam kartu (*nested double card*) hilang sepenuhnya; kartu metrik tampil sebagai kartu tunggal yang bersih.
- [x] Garis aksen warna atas (*border-top 3.5px*) terpasang presisi langsung pada masing-masing kartu metrik sesuai warna tematiknya (Biru, Hijau, Indigo, Amber).
- [x] Tag HTML penutup seimbang tanpa duplikasi `</div>`.
- [x] Tidak ada inline CSS statis baru di Blade (`AGENTS.md` compliant).
- [x] Seluruh pengujian otomatis lulus 100% hijau (*PASS*).

