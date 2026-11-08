# Rencana Kerja: Redesign Kompak & Penataan Rapi Navigasi Penempatan Peserta

**Tanggal:** 8 November 2026  
**Target File:** 
- `resources/views/admin/penempatan/index.blade.php`
- `tests/Feature/AdminChildPagesVisualTest.php`

**Tujuan:** Mengganti 2 tombol navigasi penempatan yang sebelumnya berukuran terlalu besar (raksasa/card-like) dengan **Compact Segmented Pill Tab Bar** yang proporsional, terstruktur, elegan, dan konsisten dengan sistem desain MagKom (`DS-MagKom`), serta merapikan tata letak (*layout*) keseluruhan halaman Penempatan Peserta.

---

## 1. Analisis Masalah Tampilan Saat Ini

![Tampilan Saat Ini](/C:/Users/ALDI%20BOYS/.gemini/antigravity/brain/8ac85b61-3384-4008-bb69-7f98f2cbd22d/.user_uploaded/media_1794117420021_55de3e90.png)

### Masalah Utama yang Dikeluhkan:
1. **Ukuran Tombol Terlalu Besar & Membingungkan**: Dua tombol navigasi (`Sudah Ditempatkan` & `Belum Ditempatkan`) membentang 50% lebar layar masing-masing (`flex: 1`), lengkap dengan ikon 42px, judul tebal, subteks deskripsi panjang, dan badge counter besar. Tampilannya menyerupai baris kartu statistik baru atau banner promosi raksasa, bukan tombol tab yang efisien.
2. **Komposisi Janggal**: Keberadaan tombol raksasa di antara Hero Stat Cards dan Filter Bar merusak hierarki visual dashboard, membuat pengguna bingung membedakan antara kartu metrik data dengan tombol navigasi tab.
3. **Ketidaksinkronan dengan Halaman Lain**: Halaman Verifikasi Berkas (`admin.pendaftaran.index`) menggunakan bilah tab segmen (*segmented pill bar*) yang ringkas dan rapi (`.adm-category-nav-bar`). Halaman penempatan harus diselaraskan agar konsisten 100%.

---

## 2. Asumsi Kerja & Penyelarasan Desain

1. **Konsistensi DS-MagKom (Design System MagKom)**:
   - Pola navigasi tab harus menggunakan gaya **Segmented Pill Controller** terpadu (`inline-flex`, tinggi ~40px, latar abu-abu lembut `#F1F5F9`, border halus `#E2E8F0`).
   - Tab aktif berwarna putih bersih (`#FFFFFF`) dengan elevasi halus (*subtle shadow*) dan teks biru Sleman (`#0641CC`) tebal.
   - Tab non-aktif berwarna transparan dengan teks abu-abu netral (`#64748B`).
2. **Subteks Dihilangkan dari Tombol**:
   - Teks penjelas ("Peserta aktif & riwayat penempatan") dihilangkan dari dalam tombol agar tombol tetap ringkas dan tidak makan tempat. Teks tersebut dapat dijadikan atribut `title` (tooltip).
3. **Preservasi Kontrak Fungsionalitas & Test Suite**:
   - Selector ID seperti `tab-btn-sudah`, `tab-btn-belum`, `panel-penempatan-sudah`, dan `panel-penempatan-belum` tetap dipertahankan agar fungsi JavaScript switching instan dan seluruh assertion PHPUnit existing tetap lulus 100%.

---

## 3. Evaluasi Opsi Desain

| Aspek | Opsi A: Sleman Navy Segmented Pill Bar (Rekomendasi) | Opsi B: Horizontal Line Underline Tabs |
| :--- | :--- | :--- |
| **Deskripsi** | Bilah tab segmented pill kompak (`inline-flex`) dengan container `#F1F5F9` dan badge pill counter rapi, persis seperti di `admin.pendaftaran.index`. | Deretan tab teks bergaris bawah (*underline border*) tepat di atas tabel. |
| **Ukuran & Efisiensi** | **Sangat Kompak & Ergonomis**: Tinggi hanya 40px, lebar proporsional mengikuti teks/badge, tidak melar 100% layar. | Cukup ringkas, namun garis batas bawah sering bertabrakan dengan border card filter. |
| **Kejelasan Visual** | Pengguna langsung paham bahwa ini adalah switcher filter dua kondisi data tanpa mengira itu adalah kartu statistik. | Terkadang kurang kontras jika diletakkan di luar kartu. |
| **Konsistensi** | **100% Identik** dengan pola `adm-category-nav-bar` di modul pendaftaran. | Menggunakan gaya baru yang berbeda dengan modul admin lainnya. |

**Rekomendasi:** Gunakan **Opsi A (Sleman Navy Segmented Pill Bar)**.

---

## 4. Rincian Redesign Tata Letak (Layout Composition)

### 4.1. Visual Mockup Bilah Tab Baru:
```
+-----------------------------------------------------------------------------------+
| [ Total Penempatan ]  [ Peserta Aktif ]  [ Selesai Magang ]  [ Jalur Khusus ]     | <- Hero Stat Cards
+-----------------------------------------------------------------------------------+

[  ✓ Sudah Ditempatkan (3)   |   ⏳ Belum Ditempatkan (0)  ]                         <- Bilah Tab Kompak (inline-flex, 40px)

+-----------------------------------------------------------------------------------+
| [ Cari Peserta... ] [ Bulan Magang v ] [ Status v ] [ Pembimbing v ] [ Bidang v ] | <- Filter Card Rapi
+-----------------------------------------------------------------------------------+

+-----------------------------------------------------------------------------------+
| Data Penempatan Peserta                                                           | <- Tabel Data Resmi
| [ ] | PESERTA MAGANG | PEMBIMBING | JALUR/BIDANG | BULAN MAGANG | STATUS | AKSI   |
+-----------------------------------------------------------------------------------+
```

### 4.2. Spesifikasi Styling Bilah Tab:
- **Wadah Luar (`.penempatan-nav-tabs`)**:
  - `display: inline-flex`
  - `align-items: center`
  - `background: #F1F5F9`
  - `border: 1px solid #E2E8F0`
  - `border-radius: 10px`
  - `padding: 4px`
  - `gap: 4px`
  - `margin-bottom: 1.25rem`
- **Item Tab (`#tab-btn-sudah`, `#tab-btn-belum`)**:
  - `display: inline-flex; align-items: center; gap: 0.5rem;`
  - `height: 38px; padding: 0 1rem;`
  - `font-size: 0.84375rem; font-weight: 600; border-radius: 7px; border: none;`
  - `cursor: pointer; transition: all 0.2s ease;`
- **State Tab Aktif**:
  - `background: #FFFFFF`
  - `color: #0641CC`
  - `font-weight: 700`
  - `box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04)`
- **State Tab Nonaktif**:
  - `background: transparent`
  - `color: #64748B`
  - `font-weight: 600`
- **Counter Badge**:
  - `padding: 0.15rem 0.55rem; font-size: 0.75rem; font-weight: 700; border-radius: 9999px;`
  - Pada tab aktif: Latar `#EFF6FF`, teks `#0641CC`.
  - Pada tab antrean belum ditempatkan (jika > 0): Latar amber cerah `#FEF3C7`, teks `#B45309`.

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/penempatan/index.blade.php` | Modify | 1. Mengubah struktur markup dan style bilah tab navigasi menjadi *compact segmented pill bar* (`inline-flex`).<br>2. Menghilangkan subteks panjang dari tombol agar rapi.<br>3. Memperbarui fungsi JavaScript `switchPenempatanTab` agar toggle class/style selaras dengan bilah segmented.<br>4. Merapikan margin dan proporsi spacing antara Stat Cards, Tab Bar, Filter Card, dan Tabel Data.<br>5. Memperbarui Dark Mode styles. |
| `tests/Feature/AdminChildPagesVisualTest.php` | Modify | Memastikan seluruh visual assertions untuk kedua tab dan panel tetap berjalan lancar dan 100% lulus. |

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1**: Perbarui `resources/views/admin/penempatan/index.blade.php`:
  - Ubah markup `.penempatan-nav-tabs` dan kedua tombolnya menjadi format *compact segmented pill bar*.
  - Perbarui style scoped dan dark mode overrides.
  - Perbarui logika visual switcher pada fungsi `switchPenempatanTab` di tag `<script>`.
- [x] **Langkah 2**: Jalankan pemeriksaan linter:
  ```bash
  vendor\bin\pint.bat --test
  ```
- [x] **Langkah 3**: Jalankan test suite PHPUnit:
  ```bash
  d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe artisan test --filter "AdminPenempatan|AdminChildPagesVisualTest|DashboardModulesRenderingTest"
  ```
- [x] **Langkah 4**: Kompilasi aset Vite:
  ```bash
  npm run build
  ```
- [x] **Langkah 5**: Verifikasi visual di browser lokal (`http://127.0.0.1:8000/admin/penempatan`):
  - Periksa ukuran tombol tab yang kini kompak dan proporsional.
  - Periksa responsivitas dan perpindahan tab secara instan.
  - Periksa keselarasan visual terhadap dashboard secara keseluruhan.

---

## 7. Strategi Testing

1. **Unit & Feature Testing (PHPUnit)**:
   - `test_admin_penempatan_index_renders_two_navigation_tabs_with_counters`: Memastikan kedua tombol tab dan counter badge tetap ada di DOM.
   - `test_admin_penempatan_belum_tab_renders_empty_state_when_all_placed`: Memastikan empty state tetap bekerja ketika membuka tab antrean kosong.
   - `test_admin_penempatan_belum_tab_renders_unassigned_queue_when_accepted_applicant_exists`: Memastikan antrean peserta lolos berkas tetap muncul saat ada data.
2. **Quality Gate Baseline**:
   - Memastikan seluruh 23 feature tests tetap 100% lulus (*Green*).
   - Memastikan nol JavaScript error di console browser.

---

## 8. Risiko & Mitigasi

- **Risiko**: Test PHPUnit gagal jika ada teks assertion yang hilang.
- **Mitigasi**: Teks `Sudah Ditempatkan`, `Belum Ditempatkan`, counter badge, serta ID container `#tab-btn-sudah`, `#tab-btn-belum`, `#panel-penempatan-sudah`, `#panel-penempatan-belum` tetap dipertahankan secara utuh.

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Bilah tab navigasi tidak lagi berukuran besar/raksasa, melainkan berbentuk *Segmented Pill Tab Bar* yang ringkas, elegan, dan proporsional (`height: ~40px`, `inline-flex`).
- [x] Komposisi halaman Penempatan Peserta tampak terstruktur, rapi, dan serasi dengan Hero Stat Cards di atasnya serta Filter Bar di bawahnya.
- [x] Perpindahan tab ("Sudah Ditempatkan" vs "Belum Ditempatkan") tetap instan tanpa page reload dan URL query tetap tersinkronisasi.
- [x] Mendukung tampilan terang (*Light Mode*) dan gelap (*Dark Mode*).
- [x] Seluruh automated test suite (`php artisan test`) 100% lolos hijau.
- [x] Format kode 100% PSR-12 lolos Laravel Pint.

