# Rencana Kerja: Perapian Layout Dua Kolom Bersih — Panel Misi Kiri & Kanvas Panorama Kanan 100% Lega

Dokumen rencana kerja ini merespons masukan pengguna mengenai tampilan yang *"masih kurang rapi"* akibat kartu misi melayang di atas kanvas yang menutupi sebagian jalan raya, gedung Diskominfo, dan karakter animasi di sisi kanan. Rencana ini mengubah tata letak halaman penuh `/peta-alur` menjadi **Layout Dua Kolom Bersih & Terstruktur (*Dedicated Mission Sidebar & Clean Panoramic Viewport*)**.

---

## 1. Tujuan
1. **Menghilangkan Masalah Tumpang Tindih (*Zero Overlap*)**:
   - Memisahkan Panel Misi (Checklist & Informasi) ke dalam **Sidebar Kiri Mandiri (lebar 380px)**.
   - Kanvas animasi di sisi kanan menjadi **100% Lega & Bersih**, tanpa ada kartu yang menutupi jalur mobil, Gunung Merapi, gedung Diskominfo, atau aksi karakter bergerak.
2. **Kenyamanan Membaca & Interaksi Checklist**:
   - Informasi tahapan, estimasi jadwal, dan checklist kesiapan tersusun sangat rapi, ergonomis, dan mudah dibaca di panel kiri.
   - Efek suara *chime ding*, pendaran checklist, dan *Energy Meter* tetap beraksi secara seru saat dicentang.
3. **Penyempurnaan Estetika & Ketajaman Visual**:
   - Penataan ulang topbar dengan hierarki visual yang bersih dan tidak bertumpuk.
   - Penempatan presisi posisi karakter di samping masing-masing pos (pos 1, pos 2, pos 3, pos 4) agar proporsional dan sedap dipandang.
   - Fitur tombol *Collapse / Expand Sidebar* untuk beralih ke *Cinema Mode* (kanvas 100% layar utuh).

---

## 2. Asumsi & Pendekatan Teknis

### Asumsi:
- **Asumsi 1**: Perubahan layout ini diterapkan pada halaman penuh `/peta-alur` (`resources/views/public/peta-alur-interaktif.blade.php` dan `public/css/peta-alur-fullscreen.css`).
- **Asumsi 2**: Fitur Web Audio Synthesizer, Tour Otomatis, Kamera Zoom, dan Filter Jenjang/Tipe/Bulan tetap berfungsi penuh tanpa perubahan pada logika inti.
- **Asumsi 3**: Seluruh automated test (`PetaAlurPageTest`, `LandingPagePetaAlurModalTest`, `AlurMagangPageTest`) tetap 100% hijau.

---

## 3. Rencana Tata Letak Antarmuka Baru

```
+----------------------------------------------------------------------------------------------------------------+
| [TOPBAR: SLEMAN DISKOMINFO]  [← Beranda]   [Filter: Jenjang | Tipe | Bulan]   [Audio FX: On] [Tour] [Daftar 🚀]|
+------------------------------------+---------------------------------------------------------------------------+
| [KOLOM KIRI: MISSION SIDEBAR 380px]| [KOLOM KANAN: DIORAMA PANORAMA KANVAS LEGA 100%]                          |
| (Rapi, Terstruktur, Scroll Halus)  | (100% Bebas Kartu, Visual Penuh & Jernih)                                 |
|                                    |                                                                           |
| - Header Pos: [Tahap 1: Registrasi]|      (Siluet Gunung Merapi & Bintang Sleman)                              |
| - Jadwal: 15 Mei – 31 Mei 2026     |                                                                           |
| - Deskripsi & Panduan Jenjang      |       [Pos 1] -------------------> [Pos 2]                                |
|                                    |        (Karakter Mengetik)           (Karakter Stempel)                   |
| - Uji Kesiapan & Ketentuan Wajib:  |          \                                                                |
|   [x] NIK / NISN Valid (Ding!)     |           \ ===> Mobil Melaju Lampu Sorot                                 |
|   [x] Pas Foto Formal              |            \                                                             |
|   [ ] Verifikasi Email             |             [Pos 3] ------------> [Pos 4: Finish Diskominfo]              |
|                                    |           (Mentor ASN)               (Karakter Wisuda & Konfeti)          |
| - Energy Progress Bar (66%)        |                                                                           |
| - Tips Dokumen Format Resmi        |                                                                           |
| - Tombol [Sebelumnya] [Lanjut >]   |  [Zoom Controls: + | - | Semua]                                           |
+------------------------------------+---------------------------------------------------------------------------+
| [BOTTOM DOCK: TIMELINE CHECKPOINTS 1, 2, 3, 4 DENGAN PROGRESS INDIKATOR STEPS]                                 |
+----------------------------------------------------------------------------------------------------------------+
```

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/peta-alur-interaktif.blade.php` | Modify | Ubah struktur main dari floating overlay menjadi layout 2-kolom berdampingan: `<aside class="peta-alur-sidebar">` di kiri dan `<main class="peta-alur-canvas-stage">` di kanan. Sederhanakan tombol collapse/expand. |
| `public/css/peta-alur-fullscreen.css` | Modify | Terapkan grid/flexbox 2-kolom bersih: sidebar kiri lebar tetap 390px berlatar gelap elegan, kanvas kanan `flex: 1` mengisi sisa layar dengan proporsi sempurna tanpa tumpang tindih. |
| `tests/Feature/PetaAlurPageTest.php` | Modify | Pastikan assertion tetap memvalidasi seluruh elemen (sidebar, kanvas panorama, mobil sinematik, karakter hidup, audio toggle, dsb). |

---

## 5. Langkah Implementasi Terstruktur

### Tahap 1: Restrukturisasi HTML View Dua Kolom Bersih
- [x] Buka [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php).
- [x] Rombak kontainer utama menjadi:
  - `<div class="peta-alur-main-workspace">`:
    - `<aside class="peta-alur-sidebar" id="modalStepDetailPanel">`: Panel Misi kiri yang memuat judul tahap, deskripsi, checklist interaktif, energy bar, tips dokumen, dan navigasi pos.
    - `<section class="peta-alur-viewport" id="modalRoadCanvasWrap">`: Area kanvas SVG kanan yang bersih dan luas, bebas dari elemen penutup.
- [x] Tambahkan tombol toggle *Collapse Sidebar* di header sidebar untuk mode pemandangan penuh (*Cinema Mode*).

### Tahap 2: Styling CSS Dua Kolom & Estetika Visual Rapi
- [x] Buka [public/css/peta-alur-fullscreen.css](file:///d:/MAGKOM/MAGKOM/public/css/peta-alur-fullscreen.css).
- [x] Terapkan styling layout dua kolom:
  - `.peta-alur-main-workspace { display: flex; flex: 1; height: calc(100vh - 120px); overflow: hidden; }`
  - `.peta-alur-sidebar { width: 390px; flex-shrink: 0; background: rgba(10, 18, 34, 0.95); border-right: 1px solid rgba(56, 189, 248, 0.2); overflow-y: auto; padding: 1.5rem; display: flex; flex-direction: column; }`
  - `.peta-alur-viewport { flex: 1; height: 100%; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; }`
- [x] Rapikan topbar agar jarak antar-tombol dan filter seimbang, simetris, dan tidak padat.
- [x] Rapikan tipografi, margin, padding checklist, dan tombol navigasi agar terlihat sangat profesional (*clean dashboard look*).

### Tahap 3: Pengujian Otomatis & Verifikasi Visual
- [x] Jalankan automated test:
  - `php artisan test --filter=PetaAlurPageTest`
  - `php artisan test --filter=LandingPagePetaAlurModalTest`
  - `php artisan test --filter=AlurMagangPageTest`
- [x] Uji respons HTTP: `curl.exe -I -H "Host: magkom.test" http://127.0.0.1/peta-alur` (pastikan 200 OK).

---

## 6. Kriteria Selesai (Definition of Done)
- [x] Panel Misi berada rapi di sisi kiri (lebar ~390px), tidak lagi melayang menutupi jalan atau gedung Diskominfo.
- [x] Kanvas pemandangan di sisi kanan tampil 100% bersih, lega, dan bebas hambatan visual.
- [x] Interaksi checklist tetap bersuara *chime ding* dan memperbarui skor serta energy progress bar.
- [x] Mobil sinematik melaju dengan lampu sorot menyala dan kamera auto-zoom tetap berjalan mulus.
- [x] Seluruh automated test lulus 100% hijau.
