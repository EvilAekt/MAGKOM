# Rencana Kerja: Perbaikan Tampilan Mobile Footer (Simetrisasi & Penataan Kolom Tengah)

**Tanggal**: 17 November 2026  
**Status**: Selesai Diimplementasikan (Approved with Option 1: Rata Tengah / Center-Aligned)  
**Terkait**: Penataan Grid Footer Mobile pada [footer.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/blocks/footer.blade.php) dan Stylesheet Terkait

---

## 1. Tujuan
Memperbaiki tampilan 4 kolom tautan footer pada layar ponsel (*mobile viewport*):
- Baris 1: **Tautan Terkait** & **Akun**
- Baris 2: **Media Sosial** & **Pengembang**

Pengguna memilih **Solusi 1 (Rata Tengah / `text-align: center`)** agar kedua kolom dan link di dalamnya terdistribusi simetris di tengah masing-masing kolom (lebar 50% / 50%) tanpa ada kesan berat sebelah ke kiri atau ruang kosong di kanan.

---

## 2. Implementasi yang Dikerjakan

1. **`resources/css/pages/public/landing.css`**:
   - Menambahkan aturan `@media (max-width: 639.98px)` untuk `.landing-footer-grid .footer-col-tautan`, `.footer-col-akun`, `.footer-col-medsos`, `.footer-col-pengembang`.
   - Mengatur `text-align: center !important`, `display: flex !important`, `flex-direction: column !important`, `align-items: center !important`.
   - Mengatur judul kolom `.footer-col-title`, container list `.footer-links-clean`, list item `li`, dan link `li a` ke rata tengah secara presisi.

2. **`public/css/landing-modern.css`**:
   - Menyelaraskan `.landing-footer-grid` dengan `grid-template-columns: repeat(2, 1fr)` dan `gap: 2.25rem 1.25rem`.
   - Menerapkan aturan rata tengah mobile yang identik dengan `landing.css`.

3. **`resources/css/layouts/app-layout-tokens.css`**:
   - Memperbarui `@media (max-width: 768px)` untuk `.footer-grid` dan kolom footer app layout agar konsisten dengan landing page.

---

## 3. Hasil Verifikasi & Quality Gate
- `npm run build`: Berhasil tanpa error.
- `vendor/bin/pint --test`: Passed.
- `artisan test --filter=Landing`: 116 tests passed (778 assertions).
- Zero Blade modification (aturan read-only Blade ditaati penuh).

---

## 2. Asumsi & Batasan
1. **Asumsi**:
   - Perubahan fokus pada penataan CSS layout dan spacing mobile (`@media (max-width: 639.98px)` dan `@media (max-width: 768px)`).
   - Tampilan desktop (5 kolom horizontal) sudah disetujui sebelumnya dan **TIDAK** diubah.
   - Sesuai aturan `AGENTS.md`, perubahan diselesaikan murni melalui file CSS di `resources/css/` dan `public/css/` tanpa memodifikasi markup Blade yang sudah ada.
2. **Batasan**:
   - Menjaga keterbacaan teks tautan (kontras warna WCAG AA).
   - Tautan panjang seperti "Situs Kominfo Sleman" dan "Masuk Pembimbing" tidak boleh terpotong (*truncated*) atau meluap (*overflow*).

---

## 3. Evaluasi Opsi Pendekatan Teknis

Kami menganalisis **2 pendekatan teknis** untuk mencapai simetri visual:

### 🔹 Opsi A: Grid Terpusat Simetris (Symmetrical Centered Grid) — *Rekomendasi*
- **Konsep**:
  - Kolom 1 (Tautan Terkait, Media Sosial) dan Kolom 2 (Akun, Pengembang) menggunakan grid dengan lebar kolom terukur yang diposisikan persis di tengah layar (`justify-content: center`).
  - Lebar kolom diatur menjadi `repeat(2, minmax(0, 155px))` dengan `gap: 2.25rem 1.5rem` (total lebar ~325px, sangat pas dan simetris di rentang layar ponsel 360px–430px).
  - Teks tetap rata kiri (*left-aligned*) yang alami dan mudah dipindai mata, namun seluruh blok 2x2 berada tepat di tengah layar dengan margin kiri dan kanan layar yang sama persis.
  - Kolom Profil Dinas & Alamat tetap membentang penuh di baris atas (`grid-column: 1 / -1`).
- **Kelebihan**:
  - Teks tetap mudah dibaca secara cepat (*clean scanning*).
  - Margin kiri dan kanan layar seimbang 100% (bebas ruang kosong canggung di sisi kanan).
  - Tampilan profesional setara portal instansi modern.

### 🔹 Opsi B: Rata Tengah Penuh (Full Center-Aligned)
- **Konsep**:
  - Judul kolom (`.footer-col-title`) dan teks tautan (`.footer-links-clean li a`) diatur rata tengah (`text-align: center; justify-items: center;`).
- **Kelebihan**:
  - Memberikan simetri visual seketika di setiap kolom.
- **Kekurangan**:
  - Daftar tautan dengan panjang baris tidak seragam (misal: "Kebijakan Privasi" vs "Sleman Digital") dapat terlihat kurang rapi jika di-center rata tengah dibandingkan rata kiri terpusat.

**Rekomendasi**: Mengadopsi **Opsi A** (Grid Terpusat Simetris) dengan opsi penyempurnaan padding Kolom 2 agar jarak antar-kolom harmonis.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/public/landing.css` | Modify | Perbarui aturan `.landing-footer-grid` untuk mobile (`@media (max-width: 639.98px)`): gunakan `grid-template-columns: repeat(2, minmax(0, 155px)); justify-content: center;` dan sesuaikan padding/gap. |
| `public/css/landing-modern.css` | Modify | Sinkronisasi aturan CSS mobile footer yang sama ke file CSS runtime publik. |
| `resources/css/layouts/app-layout-tokens.css` | Modify | Perbarui aturan mobile `.footer-grid` pada `@media (max-width: 768px)` agar konsisten dengan landing page. |

---

## 5. Rincian Implementasi CSS

```css
/* Mobile Footer Grid Simetris (<= 639.98px) */
.landing-footer-grid {
    position: relative;
    z-index: 2;
    max-width: 76rem;
    margin: 0 auto;
    padding: 3.5rem 1.25rem 2.75rem;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 155px));
    justify-content: center;
    gap: 2.25rem 1.5rem;
    font-size: 0.875rem;
}

.footer-col-profil {
    grid-column: 1 / -1;
    order: 1;
    width: 100%;
}

.footer-col-tautan {
    order: 2;
}

.footer-col-akun {
    order: 3;
}

.footer-col-medsos {
    order: 4;
}

.footer-col-pengembang {
    order: 5;
}

/* Tablet (640px - 1023px) */
@media (min-width: 640px) and (max-width: 1023.98px) {
    .landing-footer-grid {
        grid-template-columns: repeat(2, minmax(0, 220px));
        justify-content: center;
        gap: 2.5rem 3rem;
    }
}

/* Desktop (>= 1024px) - Tetap 5 Kolom Horizontal */
@media (min-width: 1024px) {
    .landing-footer-grid {
        grid-template-columns: 1.6fr 1.1fr 1fr 1.1fr 1.1fr;
        justify-content: stretch;
        gap: 2rem;
    }
    ...
}
```

---

## 6. Langkah Implementasi
- [ ] 1. Edit [landing.css](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css) di baris `.landing-footer-grid`:
  - [ ] Ubah `grid-template-columns` mobile menjadi `repeat(2, minmax(0, 155px))` dengan `justify-content: center; gap: 2.25rem 1.5rem;`.
  - [ ] Pastikan `.footer-col-profil` menggunakan `grid-column: 1 / -1;`.
- [ ] 2. Edit [landing-modern.css](file:///d:/MAGKOM/MAGKOM/public/css/landing-modern.css) dengan aturan CSS yang identik.
- [ ] 3. Edit [app-layout-tokens.css](file:///d:/MAGKOM/MAGKOM/resources/css/layouts/app-layout-tokens.css) untuk menyelaraskan tampilan footer layout dasar.
- [ ] 4. Jalankan kompilasi frontend:
  - [ ] `$env:PATH = "D:\laragon\bin\nodejs\node-v22;" + $env:PATH; npm run build`
- [ ] 5. Jalankan quality gates dan pengujian:
  - [ ] `vendor/bin/pint --test`
  - [ ] `artisan test --filter=Landing`

---

## 7. Strategi Testing
- **Visual & Layout Check**:
  - Menguji pada resolusi mobile 360px (Samsung/Xiaomi), 375px (iPhone SE), 390px (iPhone 14/15), dan 412px (Pixel/Galaxy).
  - Memverifikasi bahwa jarak dari teks kolom kiri ke tepi layar kiri sama persis dengan jarak dari teks kolom kanan ke tepi layar kanan (margin simetris).
  - Memastikan teks tidak terpotong (*no overflow/wrapping artifacts*).
- **Automated Tests**:
  - `php artisan test --filter=Landing` -> Memastikan seluruh 116 skenario pengujian landing page tetap hijau.

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] 4 kolom tautan footer pada mobile berada tepat di tengah layar secara simetris.
- [ ] Margin kosong di sisi kanan kolom *Akun* dan *Pengembang* hilang dan seimbang dengan margin kiri.
- [ ] Tampilan desktop tetap 5 kolom tanpa perubahan.
- [ ] Tidak ada file Blade yang disentuh (perubahan murni di CSS).
- [ ] `npm run build` dan `vendor/bin/pint --test` berhasil 100%.
- [ ] Semua tes `artisan test --filter=Landing` lulus hijau.

