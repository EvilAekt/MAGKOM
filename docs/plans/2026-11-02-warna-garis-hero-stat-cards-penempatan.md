# Rencana Kerja: Penyelarasan Garis Border Atas (Top Accent Border) Hero Stats Cards Penempatan

Dokumen ini memuat rencana kerja teknis untuk menyelaraskan garis batas atas (*top accent line / border-top*) pada seluruh 4 kartu ringkasan metrik statistik (Hero Stats Cards) di halaman **Penempatan Peserta** (`admin/penempatan`), sehingga tidak hanya kartu kedua ("Peserta Magang Aktif") yang memiliki garis hijau, melainkan setiap kartu memiliki warna tematik yang serasi dan konsisten.

---

## 1. Tujuan

Menyelaraskan aksen garis atas (*top accent line*) pada 4 Hero Stats Cards di modul Penempatan Admin:
1. **Total Penempatan** ➔ Garis Biru Royal (`#2563EB`) selaras dengan ikon dan tema data total peserta.
2. **Peserta Magang Aktif** ➔ Garis Hijau Emerald (`#10B981`) selaras dengan ikon centang dan status aktif.
3. **Selesai Magang** ➔ Garis Indigo / Deep Blue (`#4F46E5` atau `#1E40AF`) selaras dengan ikon bintang dan prestasi tuntas.
4. **Jalur Khusus Mitra** ➔ Garis Amber Emas (`#F59E0B`) selaras dengan ikon kemitraan dan MoU khusus.
*(Opsional / Kondisional: **Menunggu Plotting** tetap menggunakan Garis Amber Warning `#F59E0B`)*.

---

## 2. Asumsi Kerja & Analisis Visual

1. **Kondisi Eksisting (Temuan Masalah)**:
   - Pada file [`resources/views/admin/penempatan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/penempatan/index.blade.php) baris 646–711, hanya kartu kedua (`Peserta Aktif`) yang memiliki atribut inline:
     `style="border-top: 3.5px solid #10B981; border-left: none;"`
   - Sedangkan kartu 1, 3, dan 4 tidak memiliki garis atas (`border-top`) sama sekali, sehingga tampilan terlihat asimetris dan tidak konsisten seperti yang ditunjukkan pada tangkapan layar pengguna.
2. **Kepatuhan Aturan Proyek (`AGENTS.md`)**:
   - Aturan 3.1 & 4 melarang penambahan *inline style statis* baru di Blade dan mengharuskan styling dipusatkan di `resources/css/pages/admin/dashboard.css`.
   - Oleh karena itu, kita akan mendefinisikan modifier class semantik (`.admin-stat-card--blue`, `.admin-stat-card--green`, `.admin-stat-card--indigo`, `.admin-stat-card--amber`) di stylesheet dan membersihkan inline style statis dari Blade.

---

## 3. Evaluasi Pendekatan Teknis

| Parameter | Opsi A: Menambahkan Inline Style di Blade | Opsi B: Class Semantik Terstruktur di CSS (Rekomendasi) |
| :--- | :--- | :--- |
| **Kepatuhan Aturan** | Melanggar Rule 3.1 & Rule 4 `AGENTS.md` (menulis inline style statis di Blade). | **100% Patuh** pada Rule 3.1 & Rule 4 (CSS di `resources/css/`). |
| **Dukungan Dark Mode** | Sulit diatur; warna border statis tidak dapat beradaptasi dengan mode gelap. | Mudah diadaptasi; dapat menggunakan varian `[data-theme="dark"]` secara konsisten. |
| **Keterbacaan Kode** | Tag Blade menjadi panjang dan penuh duplikasi style string. | Bersih, deklaratif, dan mudah dibaca (`class="admin-stat-card admin-stat-card--blue"`). |
| **Pemeliharaan** | Jika ketebalan border ingin diubah dari `3.5px` menjadi `3px`, harus mengubah semua tag Blade. | Cukup ubah di 1 baris CSS token. |

**Pilihan**: **Opsi B** dipilih sebagai pendekatan standar rekayasa perangkat lunak terbaik.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/css/pages/admin/dashboard.css` | **Modify** | Menambahkan definisi modifier class `.admin-stat-card--blue`, `.admin-stat-card--green`, `.admin-stat-card--indigo`, `.admin-stat-card--amber`, dan `.admin-stat-card--orange` lengkap dengan varian dark mode. |
| `public/css/dashboard-cockpit-unified.css` | **Modify** | Sinkronisasi stylesheet fallback untuk runtime dashboard. |
| `resources/views/admin/penempatan/index.blade.php` | **Modify** | Memasang class modifier semantik pada ke-4 Hero Stats Cards dan menghapus inline style statis `border-top` pada kartu kedua. |
| `tests/Feature/AdminPenempatanHeroStatsVisualTest.php` | **Create** | Feature test untuk memverifikasi seluruh 4 Hero Stats Cards memiliki class modifier warna border-top yang sesuai. |

---

## 5. Rincian Teknis Implementasi

### A. Aturan CSS di `resources/css/pages/admin/dashboard.css`
```css
/* =========================================================================
   14. Hero Stat Cards Top Accent Border System
   ========================================================================= */
.admin-stat-card--blue {
  border-top: 3.5px solid #2563EB !important;
}

.admin-stat-card--green {
  border-top: 3.5px solid #10B981 !important;
}

.admin-stat-card--indigo {
  border-top: 3.5px solid #4F46E5 !important;
}

.admin-stat-card--amber {
  border-top: 3.5px solid #F59E0B !important;
}

.admin-stat-card--orange {
  border-top: 3.5px solid #EA580C !important;
}

/* Dark Mode Harmonization */
[data-theme="dark"] .admin-stat-card--blue {
  border-top-color: #3B82F6 !important;
}

[data-theme="dark"] .admin-stat-card--green {
  border-top-color: #10B981 !important;
}

[data-theme="dark"] .admin-stat-card--indigo {
  border-top-color: #6366F1 !important;
}

[data-theme="dark"] .admin-stat-card--amber {
  border-top-color: #F59E0B !important;
}

[data-theme="dark"] .admin-stat-card--orange {
  border-top-color: #F97316 !important;
}
```

### B. Markup Blade di `resources/views/admin/penempatan/index.blade.php`
```blade
    {{-- 1. HERO STATS CARDS --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; width: 100%;">
        {{-- Total Penempatan --}}
        <div class="admin-stat-card admin-stat-card--blue">
            ...
        </div>

        {{-- Peserta Aktif --}}
        <div class="admin-stat-card admin-stat-card--green">
            ...
        </div>

        {{-- Selesai Magang --}}
        <div class="admin-stat-card admin-stat-card--indigo">
            ...
        </div>

        {{-- Jalur Khusus (Kemitraan/Slot) --}}
        <div class="admin-stat-card admin-stat-card--amber">
            ...
        </div>

        {{-- Menunggu Penempatan (Antrean Lolos Berkas) --}}
        @if(($penempatanStats['belum_ditempatkan'] ?? 0) > 0)
            <div class="admin-stat-card admin-stat-card--orange" style="background: #FFFDF5;">
                ...
            </div>
        @endif
    </div>
```

---

## 6. Langkah-Langkah Implementasi Bertahap

- [x] **Fase 1: Persetujuan Pengguna**
  - [x] Mendapatkan konfirmasi persetujuan dari pengguna untuk rencana kerja dan pemilihan palet warna.
- [x] **Fase 2: Penambahan CSS Modifier**
  - [x] Tambahkan modifier `.admin-stat-card--blue`, `.admin-stat-card--green`, `.admin-stat-card--indigo`, `.admin-stat-card--amber` di `resources/css/pages/admin/dashboard.css`.
  - [x] Sinkronisasikan ke `public/css/dashboard-cockpit-unified.css`.
- [x] **Fase 3: Pembaruan Markup Blade (`admin/penempatan/index.blade.php`)**
  - [x] Pasang class modifier pada masing-masing dari ke-4 Hero Stats Cards.
  - [x] Hapus inline style `style="border-top: 3.5px solid #10B981; border-left: none;"` pada kartu kedua agar sepenuhnya dikontrol oleh class CSS.
- [x] **Fase 4: Asset Compilation & Quality Gate**
  - [x] Jalankan `npm run build` untuk mengompilasi CSS Vite.
  - [x] Jalankan `php vendor/bin/pint --dirty --test` untuk memastikan kebersihan kode.
  - [x] Buat automated test `tests/Feature/AdminPenempatanHeroStatsVisualTest.php` dan jalankan via `php artisan test`.
- [x] **Fase 5: Verifikasi Visual dengan Playwright MCP**
  - [x] Akses halaman `http://magkom.test/admin/penempatan`.
  - [x] Evaluasi computed style dan render ke-4 kartu metrik, verifikasi bahwa keempatnya memiliki garis aksen atas dengan warna tematik masing-masing (Biru, Hijau, Indigo, Amber).

---

## 7. Strategi Testing

1. **Automated Feature Test (`AdminPenempatanHeroStatsVisualTest.php`)**:
   - `test_hero_stats_cards_render_all_four_color_modifiers()`: Memastikan keempat class modifier (`admin-stat-card--blue`, `admin-stat-card--green`, `admin-stat-card--indigo`, `admin-stat-card--amber`) hadir pada output HTML halaman admin penempatan.
2. **Quality Gate**:
   - `php vendor/bin/pint --dirty --test` ➔ `0 issues`.
   - `npm run build` ➔ `0 errors`.
3. **Manual / Visual Testing**:
   - Computed style check di engine rendering Chrome Playwright.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Keempat Hero Stats Cards di `admin/penempatan` memiliki garis border-top (`3.5px solid`) dengan warna yang selaras:
  - Card 1: Biru (`#2563EB`)
  - Card 2: Hijau (`#10B981`)
  - Card 3: Indigo / Deep Blue (`#4F46E5`)
  - Card 4: Amber / Emas (`#F59E0B`)
- [x] Tidak ada lagi inline style statis `border-top` yang tertinggal di Blade (seluruhnya dikelola oleh CSS classes).
- [x] Tampilan rapi dan simetris baik pada mode terang maupun mode gelap.
- [x] Automated tests hijau (`100% passed`).
- [x] Code formatting Pint bersih.

