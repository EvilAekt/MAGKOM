# Rencana Kerja: Penyesuaian dan Perapian Spacing Antara Navigasi Kategori, Selector Bulan, dan Tabel Kuota

> **Status:** SELESAI (COMPLETED)  
> **Tanggal:** 23 November 2026  
> **Target Branch:** `test-2`  
> **Dokumen Terkait:** `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `.agents/rules/AGENTS.md`  
> **Referensi Gambar Permintaan Pengguna:**
> - **SS Pengguna (`media_1795427027546_74659166.png`):** Menunjukkan jarak vertikal yang terlalu renggang antara Tab Navigasi Kategori (`[ 📅 Daftar Bulan Magang ] [ 🗂️ Kuota Per Bidang ]`), Bar Selector Bulan Magang (`[ 📅 Bulan Magang: ... ]`), dan kartu tabel di bawahnya.
> - **Arahan Pengguna:** *"ini space nya terlalu jauh bgt jaraknya, tolong yang dekat aja space nya yg penting tidak mepet bgt 2cm munkin space nya"*

---

## 1. 🎯 Analisis Masalah Spacing & Solusi

### 1.1 Kondisi Saat Ini (Current State)
Saat ini pada [`resources/css/pages/admin/kuota-index.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/kuota-index.css):
- `.kuota-category-nav-wrapper`:
  - `margin-top: 1.25rem;` (20px)
  - `margin-bottom: 0.5rem;` (8px)
- `.month-switch-strip`:
  - `margin-top: 1rem;` (16px)
  - `margin-bottom: 1.5rem;` (24px)
- **Akumulasi Jarak Vertikal**:
  - Jarak antara Tab Navigasi dan Bar Selector Bulan mencapai **16px - 24px**.
  - Jarak antara Bar Selector Bulan dan Tabel di bawahnya mencapai **24px**.
  - Total ruang kosong mencapai **~60px**, sehingga komponen terlihat terpisah jauh (terlalu renggang / "terlalu jauh bgt").

### 1.2 Solusi Desain yang Diusulkan (Target State)
Sesuai arahan pengguna (*"yang dekat aja space nya yg penting tidak mepet bgt"*):
- **Jarak Atas (Hero Banner ke Tab Navigasi)**:
  - Disesuaikan menjadi `margin-top: 0.875rem;` (14px) agar transisi dari Hero Banner ke Tab terasa proporsional.
- **Jarak Tengah (Tab Navigasi ke Bar Selector Bulan)**:
  - `.kuota-category-nav-wrapper` memiliki `margin-bottom: 0.5rem;` (8px).
  - `.month-switch-strip` diatur `margin-top: 0;`.
  - Jarak bersih menjadi **8px** (rapat, terhubung sebagai satu grup kontrol navigasi).
- **Jarak Bawah (Bar Selector Bulan ke Kartu Tabel)**:
  - `.month-switch-strip` diatur `margin-bottom: 0.75rem;` (12px).
  - Jarak **12px** ini terbukti ideal:
    - **TIDAK MEPET**: Masih memiliki jarak pemisah 12px sehingga border kartu tabel tidak bersentuhan langsung dengan bar selector.
    - **TIDAK JAUH**: Mengurangi jarak dari 24px menjadi 12px (berkurang 50%), menghasilkan tampilan yang padat, rapi, dan ergonomis sesuai harapan pengguna.

---

## 2. 📋 Kepatuhan Aturan `AGENTS.md`

1. **Perubahan Murni CSS (Zero Blade Modification)**:
   - Sesuai aturan **Section 3.2 Blade read-only secara default**: Masalah spacing ini diselesaikan sepenuhnya melalui file stylesheet [`resources/css/pages/admin/kuota-index.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/kuota-index.css).
   - **TIDAK ADA perubahan pada file Blade**.
2. **Kepatuhan CSS (Section 4)**:
   - Perubahan hanya pada nilai margin selektor scoped yang sudah ada (`.kuota-category-nav-wrapper` dan `.month-switch-strip`).
   - Tidak menambah selector global atau override yang merusak halaman lain.

---

## 3. 🗂️ Rincian File yang Akan Dimodifikasi

| File | Jenis | Rincian Perubahan |
| :--- | :--- | :--- |
| [`resources/css/pages/admin/kuota-index.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/kuota-index.css) | **Modify** | 1. Ubah `.kuota-category-nav-wrapper`: `margin-top: 0.875rem; margin-bottom: 0.5rem;`<br>2. Ubah `.month-switch-strip`: `margin-top: 0; margin-bottom: 0.75rem;` |
| `docs/plans/2026-11-23-penyesuaian-spacing-kontrol-kuota.md` | **Create** | Dokumen rencana kerja teknis dan spesifikasi visual. |

---

## 4. 🚀 Rincian Checklist Implementasi Terurut

- [x] **Tahap 1**: Sesuaikan nilai margin pada `.kuota-category-nav-wrapper` dan `.month-switch-strip` di `resources/css/pages/admin/kuota-index.css`.
- [x] **Tahap 2**: Jalankan kompilasi aset frontend via Vite: `npm run build`.
- [x] **Tahap 3**: Jalankan seluruh feature test terkait menggunakan PHP 8.3: `php artisan test tests/Feature/AdminKuotaRevampTest.php`.
- [x] **Tahap 4**: Jalankan linter Pint: `vendor/bin/pint --test`.
- [x] **Tahap 5**: Lakukan verifikasi visual bahwa jarak antar-komponen kini lebih dekat, rapi, dan tidak mepet.

---

## 5. ✅ Kriteria Selesai (Definition of Done)

- [x] Jarak antara Tab Navigasi Kategori dan Bar Selector Bulan berkurang menjadi ~8px.
- [x] Jarak antara Bar Selector Bulan dan Kartu Tabel di bawahnya berkurang menjadi 12px (0.75rem), tidak mepet dan tidak renggang.
- [x] Tidak ada file Blade yang diubah.
- [x] Kompilasi Vite `npm run build` sukses 100% dan seluruh 21 test PHPUnit tetap lulus (PASS).

