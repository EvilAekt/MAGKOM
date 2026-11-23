# Rencana Kerja: Penyesuaian Spacing Kompak Toolbar Kontrol Berdasarkan Referensi Gambar SS 2

> **Status:** SELESAI (COMPLETED)  
> **Tanggal:** 23 November 2026  
> **Target Branch:** `test-2`  
> **Dokumen Terkait:** `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `.agents/rules/AGENTS.md`  
> **Referensi Gambar Permintaan Pengguna:**
> - **SS 1 (`media_1795428553760_90bd2986.png`):** Menunjukkan tampilan saat ini di mana jarak vertikal di atas dan di bawah bar kontrol masih terasa terlalu renggang (*"masih ke jauhan"*).
> - **SS 2 (`media_1795428727423_a7f29dc6.png`):** Menunjukkan contoh jarak yang diinginkan pengguna: celah vertikal yang sangat kompak dan dekat (~8px / 0.5rem) antara tepi bawah kartu toolbar dan tepi atas kartu tabel bergaris biru, rapi dan tidak saling menabrak.

---

## 1. 🎯 Analisis Perbandingan Spacing (Current vs SS 2)

| Parameter | Kondisi Saat Ini | Target Sesuai Contoh SS 2 | Dampak Visual |
| :--- | :--- | :--- | :--- |
| **Jarak Bawah ke Kartu Tabel (`margin-bottom`)** | `1.25rem` (20px) | **`0.5rem` (8px)** | Memangkas jarak sebesar 60%. Celah antar-kartu menjadi kompak dan rapat persis seperti potongan gambar SS 2, tanpa saling menabrak border. |
| **Jarak Atas dari Hero Banner (`margin-top`)** | `1.25rem` (20px) | **`0.625rem` (10px)** | Mengurangi kekosongan vertikal antara Hero Banner Biru dan bar kontrol agar ritme vertikal proporsional. |
| **Padding Internal Toolbar** | `0.75rem 1.25rem` | `0.65rem 1.25rem` | Sedikit merampingkan tinggi toolbar agar lebih padat (*crisp*). |

---

## 2. 📋 Kepatuhan Aturan `AGENTS.md`

1. **Zero Blade Modification (Section 3.2)**:
   - Penyesuaian ini murni masalah *visual spacing* dan diselesaikan 100% pada file stylesheet [`resources/css/pages/admin/kuota-index.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/kuota-index.css).
   - **File Blade `resources/views/admin/kuota/index.blade.php` tidak disentuh**.
2. **Kepatuhan CSS (Section 4)**:
   - Nilai margin disesuaikan pada kelas scoped `.month-switch-strip, .kuota-unified-toolbar`.
   - Tidak memengaruhi halaman lain atau layout global.

---

## 3. 🗂️ Rincian File yang Dimodifikasi

| File | Jenis | Rincian Perubahan |
| :--- | :--- | :--- |
| [`resources/css/pages/admin/kuota-index.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/kuota-index.css) | **Modify** | Mengubah `margin-top: 0.625rem; margin-bottom: 0.5rem;` dan `padding: 0.65rem 1.25rem;` pada `.month-switch-strip, .kuota-unified-toolbar`. |
| `docs/plans/2026-11-23-penyesuaian-spacing-toolbar-kompak.md` | **Create** | Dokumen rencana kerja teknis dan spesifikasi visual. |

---

## 4. 🚀 Rincian Checklist Implementasi Terurut

- [x] **Tahap 1**: Sesuaikan nilai margin dan padding `.kuota-unified-toolbar` di `resources/css/pages/admin/kuota-index.css`.
- [x] **Tahap 2**: Jalankan kompilasi aset frontend: `npm run build`.
- [x] **Tahap 3**: Jalankan seluruh feature test terkait menggunakan PHP 8.3: `php artisan test tests/Feature/AdminKuotaRevampTest.php`.
- [x] **Tahap 4**: Jalankan linter Pint: `vendor/bin/pint --test`.
- [x] **Tahap 5**: Lakukan verifikasi visual bahwa jarak antar-kartu kini rapat (~8px) persis seperti referensi SS 2.

---

## 5. ✅ Kriteria Selesai (Definition of Done)

- [x] Jarak antara kartu toolbar kontrol dan kartu tabel di bawahnya berkurang menjadi 8px (`0.5rem`), sesuai dengan tangkapan layar SS 2.
- [x] Jarak antara Hero Banner dan kartu toolbar kontrol menjadi 10px (`0.625rem`).
- [x] Tidak ada perubahan pada file Blade.
- [x] `npm run build` sukses 100% dan seluruh 21 feature test PHPUnit tetap lulus (PASS).

