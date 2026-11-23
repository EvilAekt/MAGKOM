# Rencana Kerja: Penyesuaian Spacing Kompak Antar-Kartu Melalui Kalibrasi Root Flex Gap

> **Status:** SELESAI (COMPLETED)  
> **Tanggal:** 23 November 2026  
> **Target Branch:** `test-2`  
> **Dokumen Terkait:** `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `.agents/rules/AGENTS.md`  
> **Permintaan Pengguna:** *"buat plan untuk space nya deketkan lagi,masih kejauhan ini"*  
> **Referensi Gambar Permintaan Pengguna:**
> - **SS 2:** Potongan zoom jarak antar-kartu yang sangat rapat (~8px / 0.5rem).

---

## 1. 🔍 Root Cause Analysis (Penyebab Ruang Masih Terasa Renggang)

Setelah dilakukan audit mendalam pada stylesheet [`resources/css/pages/admin/kuota-index.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/kuota-index.css), ditemukan bahwa:
1. Container pembungkus utama `.adm-kuota-revamp-wrap` memiliki aturan:
   ```css
   .adm-kuota-revamp-wrap {
       display: flex;
       flex-direction: column;
       gap: 1.75rem; /* <-- 28px flex gap bawaan */
       width: 100%;
       ...
   }
   ```
2. Karena seluruh komponen tingkat atas (Hero Banner, Toolbar Kontrol, dan Panel Tabel) adalah *direct flex children* dari `.adm-kuota-revamp-wrap`, properti `gap: 1.75rem` ini memaksakan jarak vertikal minimal sebesar **28px** di antara setiap komponen.
3. Ditambah margin pada komponen, total jarak vertikal antara toolbar dan tabel mencapai **~36px**, yang membuat antarmuka tetap terlihat renggang meskipun margin telah dikurangi.

---

## 2. 🎯 Solusi Rekayasa Spacing Kompak (Presisi Sesuai SS 2)

1. **Kalibrasi Root Container Flex Gap**:
   - Mengubah `gap` pada `.adm-kuota-revamp-wrap` dari `1.75rem` (28px) menjadi **`0.5rem` (8px)**.
2. **Pembersihan Margin pada Toolbar Kontrol**:
   - Menghapus `margin-top` dan `margin-bottom` pada `.month-switch-strip, .kuota-unified-toolbar` (`margin: 0;`).
3. **Hasil Akhir**:
   - Jarak antara **Hero Banner Biru** dan **Toolbar Kontrol** = **8px**.
   - Jarak antara **Toolbar Kontrol** dan **Kartu Tabel** di bawahnya = **8px**.
   - Jarak ini 100% konsisten, rapat, dan identik dengan contoh potongan zoom pada **Gambar SS 2** yang Anda kirimkan.

---

## 3. 📋 Kepatuhan Aturan `AGENTS.md`

1. **Zero Blade Modification (Section 3.2)**:
   - Masalah ini murni pada deklarasi flexbox di stylesheet [`resources/css/pages/admin/kuota-index.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/kuota-index.css).
   - **File Blade tidak disentuh sama sekali**.
2. **CSS Scoped (Section 4)**:
   - Perubahan hanya pada scoped container `.adm-kuota-revamp-wrap` dan `.kuota-unified-toolbar`.

---

## 4. 🗂️ Rincian File yang Dimodifikasi

| File | Jenis | Rincian Perubahan |
| :--- | :--- | :--- |
| [`resources/css/pages/admin/kuota-index.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/kuota-index.css) | **Modify** | 1. `.adm-kuota-revamp-wrap`: ubah `gap: 1.75rem;` menjadi `gap: 0.5rem;`<br>2. `.month-switch-strip, .kuota-unified-toolbar`: ubah `margin-top` dan `margin-bottom` menjadi `margin: 0;` |
| `docs/plans/2026-11-23-penyesuaian-spacing-kompak-root-flex-kuota.md` | **Create** | Dokumen rencana kerja teknis dan spesifikasi visual. |

---

## 5. 🚀 Rincian Checklist Implementasi Terurut

- [x] **Tahap 1**: Sesuaikan `gap: 0.5rem;` pada `.adm-kuota-revamp-wrap` dan `margin: 0;` pada `.kuota-unified-toolbar` di `resources/css/pages/admin/kuota-index.css`.
- [x] **Tahap 2**: Jalankan kompilasi aset frontend: `npm run build`.
- [x] **Tahap 3**: Jalankan seluruh feature test: `php artisan test tests/Feature/AdminKuotaRevampTest.php` menggunakan PHP 8.3.
- [x] **Tahap 4**: Jalankan linter Pint: `vendor/bin/pint --test`.
- [x] **Tahap 5**: Lakukan verifikasi visual bahwa jarak antar-komponen kini benar-benar rapat (~8px) persis seperti referensi SS 2.

---

## 6. ✅ Kriteria Selesai (Definition of Done)

- [x] Jarak vertikal antar-kartu (Hero Banner, Toolbar Kontrol, dan Tabel) murni berjarak 8px (`0.5rem`).
- [x] Tampilan kompak dan selaras persis seperti contoh zoom SS 2.
- [x] Tidak ada file Blade yang disentuh.
- [x] `npm run build` dan seluruh 21 feature test PHPUnit lulus 100% (PASS).

