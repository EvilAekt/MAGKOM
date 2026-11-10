# Rencana Kerja (Plan): Kompaksi Ukuran & Penegasan Kontras Kotak Tugas Peserta

**Tanggal Dibuat:** 10 November 2026  
**Status:** Selesai Diimplementasikan (Verified)  
**Tujuan Dokumen:** Perencanaan terstruktur untuk merampingkan dimensi kartu tugas (*compact layout*) dan menegaskan batas visual kontainer kartu (*border & elevation contrast*) pada halaman **Tugas & Progres Magang Peserta** (`/peserta/tugas`) sesuai arahan dan tangkapan layar pengguna (`media_1794303754593_82042e69.png`).

---

## 1. Tujuan

1. **Memperkecil Kotak Tugas (*Compact Assignment Card*)**:
   - Mengurangi *vertical & horizontal footprint* kartu penugasan (`.assignment-card`) sekitar 25%–30% agar lebih ringkas, padat, dan efisien.
   - Merampingkan padding kontainer, ukuran lingkaran ikon status, tipografi judul & deskripsi, divider, serta ukuran tombol emas `Buka Tugas >` dan tag meta.
   - Memungkinkan lebih banyak daftar tugas terlihat dalam satu pandangan layar tanpa memerlukan scroll berlebihan.

2. **Menegaskan Batas & Kejelasan Kotak (*High-Contrast Box Definition*)**:
   - Menyelesaikan keluhan batas kotak yang "tenggelam" / samar (sebelumnya kartu putih `#FFFFFF` berada di atas panel kontainer putih `#FFFFFF` dengan border tipis `1px solid #E2E8F0` dan shadow yang hampir transparan).
   - Menambahkan latar belakang kanvas kontras halus pada bodi panel (`#F8FAFC` / Slate-50).
   - Mempertegas garis tepi (*border*) menjadi `1.5px solid #CBD5E1` (Slate-300 yang tegas dan bersih).
   - Memberikan bayangan elevasi berdimensi lembut bernuansa Sleman Navy (`box-shadow: 0 2px 8px -1px rgba(0, 53, 102, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05)`).
   - Menambahkan *status accent indicator* sisi kiri (contoh: Sleman Navy `#003566` untuk tugas selesai, Info Blue `#0284C7` untuk penugasan berjalan, Red `#DC2626` untuk lewat tenggat).

3. **Kepatuhan Aturan & Standar Proyek**:
   - 100% perubahan ditangani di lapisan CSS (`resources/css/pages/peserta/tugas-index.css`), tanpa menyentuh markup Blade atau menambahkan inline styles, sesuai amanat `AGENTS.md` Pasal 3 & 4.
   - Menjaga keutuhan fungsionalitas filter JavaScript dan data attributes (`data-status`, `data-is-overdue`).
   - Kompatibilitas penuh dengan tema Dark Mode (`[data-theme="dark"]`).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Scope Fokus**: Perubahan berfokus pada kartu penugasan (`.assignment-card`) beserta elemen internalnya (ikon, header, judul, deskripsi, divider, tags, dan tombol aksi) di dalam panel daftar tugas. Hero banner dan 3 kartu KPI di bagian atas sudah seimbang dan tidak memerlukan perubahan struktural.
2. **Preservasi Kontrak Aksesibilitas & Pengujian**: Semua kelas CSS dan selector yang di-assert oleh unit/feature test (`PesertaTugasFilterTabsTest`, dll.) seperti `.assignment-card`, `.btn-tugas-action`, `.assignment-title`, `.assignment-due` wajib dipertahankan namanya.
3. **Penyelarasan Warna Sleman**: Seluruh palet tetap menggunakan sistem warna resmi MagKom Sleman (`#003566`, `#01347C`, `#02275D`, `#FFC300`, `#FFCB05`).

---

## 3. Pendekatan Teknis (Evaluasi Opsi)

### Opsi A (Direkomendasikan): Dual-Contrast Canvas + Crisp Border + Compact Rhythm
- **Deskripsi**:
  1. Mengubah background `.assignment-panel-body` dari `#FFFFFF` menjadi neutral backdrop yang lembut `#F8FAFC` (Slate-50).
  2. Memberikan kartu `.assignment-card` warna dasar putih bersih `#FFFFFF` dengan border tegas `1.5px solid #CBD5E1`, border-radius `12px`, dan shadow `0 2px 8px -1px rgba(0, 53, 102, 0.08)`.
  3. Mengurangi padding kartu dari `18px 22px` menjadi `12px 18px`.
  4. Merampingkan lingkaran status dari `38px` ke `30px`, ukuran teks judul dari `1.05rem` ke `0.95rem`, margin deskripsi dari `12px` ke `6px`, serta tombol `Buka Tugas` dari `8px 18px` ke `6px 14px`.
- **Kelebihan**:
  - Batas kotak langsung terlihat sangat jelas dan tajam (*crisp boundary*) bahkan pada layar monitor dengan kontras rendah.
  - Tampilan modern bergaya executive modern card seperti Notion/Linear.
  - Perubahan murni CSS, bebas risiko merusak Blade markup.
- **Kekurangan**:
  - Membutuhkan penyesuaian dark mode agar latar kontras tetap harmonis.

### Opsi B: Hanya Menambah Ketebalan Border Card Tanpa Mengubah Background Panel
- **Deskripsi**:
  - Membiarkan latar panel tetap putih `#FFFFFF`, hanya menebalkan border kartu menjadi `2px solid #94A3B8` dan memperkecil padding.
- **Kelebihan**:
  - Sangat sederhana.
- **Kekurangan**:
  - Pada layar dengan saturasi tinggi, kartu putih di atas latar putih dengan border tebal tampak kaku (*box-in-a-box*) dan kurang estetis dibandingkan Opsi A yang memiliki kedalaman kedudukan (*layered elevation*).

> **Rekomendasi**: Dipilih **Opsi A** untuk hasil visual terbaik, paling ergonomis, dan memberikan kontras yang jelas dan proporsional.

---

## 4. Perincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/peserta/tugas-index.css` | Modify | 1. Set background `.assignment-panel-body` ke `#F8FAFC` dengan border-top pemisah halus.<br>2. Perkecil padding `.assignment-card` ke `12px 18px` dan pertegas border ke `1.5px solid #CBD5E1`.<br>3. Tambahkan soft shadow bernuansa Sleman Navy dan aksen border status kiri.<br>4. Rampingkan ukuran `.assignment-status-icon` (30px), judul (0.95rem), deskripsi, divider, tag, dan tombol `.btn-tugas-action`.<br>5. Sinkronisasi styling untuk Dark Mode dan mobile viewport. |

---

## 5. Langkah Implementasi Terstruktur

- [x] **Langkah 1**: Backup & verifikasi kesiapan baseline file `resources/css/pages/peserta/tugas-index.css`.
- [x] **Langkah 2**: Terapkan styling kompaksi dimensi kartu tugas:
  - [x] Ubah padding `.assignment-card` dari `18px 22px` menjadi `12px 18px`.
  - [x] Rampingkan gap kartu dari `16px` menjadi `14px`, dan gap `.assignment-list` dari `14px` menjadi `10px`.
  - [x] Perkecil `.assignment-status-icon` menjadi `30px x 30px` dengan ukuran ikon SVG `16px`.
  - [x] Rampingkan tipografi `.assignment-title` (font-size `0.95rem`, margin `0 0 3px 0`).
  - [x] Rampingkan `.assignment-desc` (font-size `0.825rem`, margin-bottom `8px`, line-height `1.45`).
  - [x] Rampingkan `.assignment-divider` (margin: `8px 0`).
  - [x] Rampingkan `.btn-tugas-action` (padding `6px 14px`, font-size `0.785rem`, border-radius `8px`).
  - [x] Rampingkan `.assignment-badge-status` (padding `5px 12px`, font-size `0.725rem`).
  - [x] Rampingkan `.assignment-tag` dan `.assignment-tag-reports` (padding `3px 10px`, font-size `0.725rem`).
- [x] **Langkah 3**: Terapkan penegasan kontras & definisi batas kotak:
  - [x] Beri background `#F8FAFC` pada `.assignment-panel-body` dan radius bawah `16px`.
  - [x] Pertegas border `.assignment-card` menjadi `1.5px solid #CBD5E1`.
  - [x] Beri bayangan elevasi berdimensi: `box-shadow: 0 2px 8px -1px rgba(0, 53, 102, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04)`.
  - [x] Beri aksen border kiri status:
    - Tugas selesai: `border-left: 4px solid #003566;`
    - Tugas berjalan: `border-left: 4px solid #0284C7;`
    - Tugas terlambat: `border-left: 4px solid #DC2626;`
  - [x] Hover effect: `border-color: #01347C; box-shadow: 0 4px 14px rgba(1, 52, 124, 0.12); transform: translateY(-1px);`.
- [x] **Langkah 4**: Sesuaikan Dark Mode (`[data-theme="dark"]`):
  - [x] Pastikan `.assignment-panel-body` di dark mode berlatar `#0F172A`.
  - [x] Pastikan `.assignment-card` di dark mode berlatar `#182234` dengan border `#334155`.
- [x] **Langkah 5**: Kompilasi aset Vite & verifikasi caching:
  - [x] Jalankan `npm run build`.
  - [x] Jalankan `php artisan view:clear`.
- [x] **Langkah 6**: Jalankan Quality Gate:
  - [x] Jalankan `./vendor/bin/pint --test`.
  - [x] Jalankan feature test `php artisan test tests/Feature/PesertaTugasFilterTabsTest.php`.

---

## 6. Strategi Testing

1. **Automated Feature Test**:
   - `php artisan test tests/Feature/PesertaTugasFilterTabsTest.php`
   - Memastikan filter tab (Semua, Selesai, Lewat Tenggat) dan navigasi detail tetap berfungsi normal tanpa regresi.
2. **Visual Inspection**:
   - Memastikan kotak tugas tampak lebih padat, rapi, dan mudah dilihat batasnya (*high contrast*).
   - Memastikan teks judul, tanggal tenggat, tags, dan tombol emas `Buka Tugas >` tetap terbaca dengan jelas dan sejajar rapi.
3. **Format & Code Style**:
   - `./vendor/bin/pint --test`
   - `npm run build` sukses tanpa error linting.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| Teks judul atau deskripsi terpotong pada layar ponsel sempit | Minor | Pertahankan flex-wrap pada `.assignment-header-row` dan media query `@media (max-width: 640px)`. |
| Perubahan CSS memengaruhi halaman lain | Nihil | Semua selector di-scope secara spesifik di `tugas-index.css` untuk rute tugas peserta. |
| Test assertion mencari teks tersembunyi | Nihil | Helper assertion `<span class="sr-only">` di Blade tidak diubah sama sekali. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Ukuran kartu tugas berkurang sekitar 25%–30% (lebih ramping & padat).
- [x] Batas kotak kartu tampak tegas dan jelas (*well-defined*), tidak lagi samar atau tenggelam di atas latar putih.
- [x] Tombol emas `Buka Tugas >` dan badge status tetap proporsional dan mudah diklik.
- [x] Aset Vite berhasil di-build (`npm run build`).
- [x] Seluruh feature test terkait penugasan peserta lulus hijau (PASS).
- [ ] Pengguna mengonfirmasi tampilan baru telah sesuai ekspektasi.

