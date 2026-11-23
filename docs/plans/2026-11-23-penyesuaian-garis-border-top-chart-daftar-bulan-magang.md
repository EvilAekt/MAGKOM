# Rencana Teknis: Penyesuaian Garis Border Atas Card Daftar Bulan Magang Selaras dengan Kuota Per Bidang

Dokumen ini disusun berdasarkan skill `web-planning` untuk merencanakan standardisasi visual pada panel/card **Daftar Bulan Magang** agar memiliki aksen garis batas atas (*top line border*) yang identik dengan card **Kuota per bidang**.

---

## 1. Tujuan

Menyelaraskan gaya visual container card **Daftar Bulan Magang** (`.table-bulan-card`) pada halaman Manajemen Kuota (`/admin/kuota?tab=bulan`) dengan card **Kuota per bidang** (`.x_panel`), khususnya aksen garis border atas:
- **Light Mode**: Garis tebal beraksen biru primer Sleman (`border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important;`) dengan border keliling `1.5px solid #CBD5E1` dan elevasi halus.
- **Dark Mode**: Garis aksen biru terang (`border-top: 3.5px solid var(--blue-600, #3B82F6) !important;`) dengan surface gelap (`--dm-surface` / `#0B1E3B`) dan border keliling (`#1E3A8A`).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. Istilah *"chart daftar bulan magang"* dan *"line garis"* pada instruksi pengguna mengacu pada container card tabel **Daftar Bulan Magang** yang memiliki elemen visual serupa kartu Gentelella, dan potongan gambar yang dilampirkan menunjukkan sudut kiri atas bergaris biru navy tebal milik card **Kuota per bidang** (`.x_panel`).
2. Perubahan dilakukan secara murni melalui lapisan CSS (`resources/css/`), tanpa mengubah struktur markup Blade (`resources/views/admin/kuota/index.blade.php`), mematuhi aturan ketat **AGENTS.md** Bagian 3.2 (*Blade read-only secara default untuk urusan visual*).
3. Tidak ada perubahan pada business logic, controller, atau interaksi JavaScript di `kuota-index.js`.

### Pertanyaan Terbuka:
- Tidak ada ambiguitas yang menghalangi perancangan, karena referensi visual dan token desain sudah sangat jelas di codebase.

---

## 3. Pendekatan Teknis

### Perbandingan Pendekatan:

| Kriteria | Opsi 1: Penyesuaian CSS Scoped & Global Card Tokens (Rekomendasi) | Opsi 2: Refactor Markup Blade Menggunakan `<x-gentelella.x-panel>` |
|---|---|---|
| **Kepatuhan Aturan Proyek** | **Sangat Baik**: 100% patuh pada aturan `AGENTS.md` (tidak menyentuh Blade untuk estetika). | **Buruk**: Melanggar aturan read-only Blade tanpa kebutuhan fungsional/markup baru. |
| **Resiko Regresi JS** | **Nol**: Selector kelas `.table-bulan-card` tetap utuh bagi event handler. | **Tinggi**: Berpotensi merusak selektor DOM pada `kuota-index.js` dan responsive wrapper. |
| **Dukungan Tema Terang/Gelap** | Terintegrasi mulus melalui variabel CSS dan token desain yang ada. | Bergantung pada style bawaan x-panel. |
| **Efisiensi Build** | Minimal & terisolasi pada `kuota-index.css` & `cards.css`. | Membutuhkan modifikasi view dan kompilasi ulang view. |

**Rekomendasi**: Memilih **Opsi 1** dengan mengupdate styling `.table-bulan-card` di `resources/css/pages/admin/kuota-index.css` serta mendaftarkan `.table-bulan-card` pada kelompok container card standar di `resources/css/components/cards.css`.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
|---|---|---|
| `resources/css/pages/admin/kuota-index.css` | **Modify** | Menambahkan `border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important;`, memperbarui `border: 1.5px solid #CBD5E1;`, serta menambahkan rule Dark Mode `border-top: 3.5px solid var(--blue-600, #3B82F6) !important;`. |
| `resources/css/components/cards.css` | **Modify** | Memasukkan `.table-bulan-card` ke dalam rule container card standar sistem MagKom (mode terang & mode gelap). |
| `docs/plans/2026-11-23-penyesuaian-garis-border-top-chart-daftar-bulan-magang.md` | **Create** | Dokumen rencana kerja teknis ini. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1: Update Styling Card di `resources/css/pages/admin/kuota-index.css`**
  - Pada `.table-bulan-card`:
    - Ganti `border: 1px solid #E2E8F0;` menjadi `border: 1.5px solid #CBD5E1;`.
    - Tambahkan `border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important;`.
    - Sesuaikan `box-shadow: 0 2px 8px rgba(1, 52, 124, 0.04);`.
  - Pada `[data-theme="dark"] .table-bulan-card`:
    - Tambahkan `border-top: 3.5px solid var(--blue-600, #3B82F6) !important;`.
- [x] **Langkah 2: Daftarkan Selector di `resources/css/components/cards.css`**
  - Tambahkan `.table-bulan-card` pada selector container card utama:
    - `html:not([data-theme="dark"]) ... , html:not([data-theme="dark"]) .table-bulan-card`
    - `[data-theme="dark"] ... , [data-theme="dark"] .table-bulan-card`
- [x] **Langkah 3: Kompilasi Aset Frontend (Vite Build)**
  - Jalankan `npm run build` dan verifikasi bahwa bundel aset `kuota-index-*.css` terkompilasi bersih tanpa error resolusi.
- [x] **Langkah 4: Jalankan Quality Gates**
  - Jalankan test terkait: `php artisan test tests/Feature/AdminKuotaRevampTest.php` dan `tests/Feature/AdminChildPagesVisualTest.php`.
  - Jalankan `vendor/bin/pint --test` dan `vendor/bin/phpstan analyse --memory-limit=1G`.

---

## 6. Strategi Testing

1. **Uji Kompilasi Aset**:
   - `npm run build` sukses 100% dan menghasilkan CSS teroptimasi.
2. **Uji Regresi Fitur & Visual**:
   - `tests/Feature/AdminKuotaRevampTest.php` lolos tanpa kegagalan (100% PASS).
   - `tests/Feature/AdminChildPagesVisualTest.php` lolos tanpa kegagalan (100% PASS).
   - `tests/Feature/AdminSuperAdminPortalTest.php` lolos (100% PASS).
3. **Uji Analisis Statis & Format**:
   - Pint dan PHPStan lolos dengan 0 error.

---

## 7. Risiko & Mitigasi

- **Risiko**: Nilai `border-top` tertimpa oleh rule border default.  
  **Mitigasi**: Gunakan `!important` selaras dengan deklarasi di `.x_panel` pada `cards.css` dan `gentelella.css`.
- **Risiko**: Tampilan di Dark Mode tampak silau atau tidak harmonis.  
  **Mitigasi**: Gunakan token warna `--blue-600` (`#3B82F6`) yang merupakan standar tema gelap resmi MagKom untuk border atas kartu.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Card "Daftar Bulan Magang" memiliki garis border atas tebal biru navy (`#01347C`) di mode terang.
- [x] Card "Daftar Bulan Magang" memiliki garis border atas tebal biru terang (`#3B82F6`) di mode gelap.
- [x] Tidak ada perubahan atau modifikasi markup pada file Blade apa pun.
- [x] `npm run build` sukses tanpa error.
- [x] Seluruh test suite otomatis, Pint, dan PHPStan tetap lulus 100%.

