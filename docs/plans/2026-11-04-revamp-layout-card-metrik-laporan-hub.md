# Plan: Revamp & Restrukturisasi Tata Letak Card Metrik Hub Laporan Admin

## 1. Tujuan
Memperbaiki tata letak (*layout*) dan estetika visual 6 kartu metrik pada halaman Hub Laporan Admin (`resources/views/admin/laporan/index.blade.php`) yang saat ini berjejal dalam 1 baris sempit (seperti pada tangkapan layar `media_1793756126085_99bae267.png`), sehingga:
1. **Tata Letak Terstruktur & Bernapas**: Mengubah grid 6 kolom yang sempit menjadi tata letak simetris 2 baris (3 kolom x 2 baris) yang proporsional, rapi, dan seimbang.
2. **Menghilangkan Pemotongan Teks Cacat (*Truncation / Clashing*)**:
   - Judul kartu (`TOTAL PENGAJUAN`, `REALISASI KUOTA UMUM`, dll.) muat dalam 1 baris tanpa tertekuk kasar (*no awkward line-breaks*).
   - Angka nilai dan badge tren (*pill*) memiliki jarak aman (*breathing room*) dari mini sparkline di sebelah kanan.
   - Subteks deskriptif (`Semua status pendaftaran`, `Lolos seleksi berkas & kuota`, dll.) tampil utuh dan jelas tanpa terpotong `...`.
3. **Pengelompokan Semantik Logis**:
   - **Baris 1 (Pipeline Alur Peserta Magang)**: Total Pengajuan &rarr; Peserta Diterima &rarr; Penempatan Aktif.
   - **Baris 2 (Hasil & Utilisasi Kuota)**: Magang Selesai &rarr; Realisasi Kuota Umum &rarr; Slot Khusus Terisi.
4. **Responsif Adaptif**: Tampil optimal di Desktop (3 kolom), Tablet (2 kolom), dan Mobile (1 kolom) sesuai standar Clean Industrial UI MAGKOM.

---

## 2. Asumsi & Analisis Akar Masalah

### Akar Masalah pada Tangkapan Layar (`media_1793756126085_99bae267.png`):
- Kontainer saat ini menggunakan: `display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));`
- Pada resolusi layar desktop 1280px–1440px, CSS tersebut memaksakan seluruh 6 kartu berjejer horizontal dalam 1 baris.
- Lebar masing-masing kartu menyusut drastis ke ~180px–200px:
  - Kotak ikon kiri mengambil 44px + gap 14px (~58px).
  - Sparkline vertikal kanan mengambil ~35px.
  - Ruang tengah yang tersisa hanya ~85px–100px.
- Akibatnya:
  1. Judul tertekuk paksa menjadi 2-3 baris vertikal.
  2. Nilai angka dan badge pill berhimpitan langsung dengan grafik sparkline.
  3. Subteks terpotong kaku (`Semua sta...`, `Lolos selel...`, `Sedang m...`, `Kursi teris...`, `Jalur kemit...`).
  4. Tampilan berkesan padat, tidak terstruktur, dan tidak proporsional.

### Asumsi Kerja:
- Seluruh 6 data metrik (`total_pendaftaran`, `total_diterima`, `penempatan_aktif`, `penempatan_selesai`, `kuota_terisi`, `slot_terisi`) tetap dipertahankan karena dibutuhkan oleh administrator.
- Komponen blade `<x-gentelella.tile-stat>` tetap menjadi basis kartu metrik sesuai standardisasi yang telah disetujui.
- Pengaturan styling grid dipusatkan di file CSS terstruktur (`resources/css/components/cards.css` dan `resources/css/pages/admin/laporan-tahunan.css` / scoped class) agar patuh terhadap aturan `AGENTS.md` (tanpa inline styling di Blade).

---

## 3. Pendekatan Teknis & Evaluasi Arsitektur

### Perbandingan Pendekatan:

- **Opsi A (Sangat Direkomendasikan): Grid Simetris 3 Kolom x 2 Baris (`.adm-laporan-kpi-grid`)**
  - **Mekanisme**:
    Mengatur kontainer 6 kartu menjadi grid simetris 3 kolom:
    ```css
    .adm-laporan-kpi-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 1.25rem !important;
        width: 100% !important;
        margin-bottom: 1.75rem !important;
    }
    @media (max-width: 1100px) {
        .adm-laporan-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }
    @media (max-width: 640px) {
        .adm-laporan-kpi-grid {
            grid-template-columns: 1fr !important;
        }
    }
    ```
  - **Kelebihan**:
    1. **Lebar Kartu Sangat Ideal (~380px–440px per kartu)**: Area tengah memiliki ruang ~280px. Seluruh judul muat 1 baris, badge tren tidak menabrak sparkline, dan subteks tidak terpotong.
    2. **Simetri Visual Sempurna**: 3 kartu di baris atas dan 3 kartu di baris bawah menghasilkan tata letak yang stabil, seimbang, dan kokoh dipandang.
    3. **Alur Semantik Jelas**:
       - Baris Atas = Tahapan Pendaftaran & Penugasan Peserta.
       - Baris Bawah = Tahapan Kelulusan & Realisasi Alokasi Kuota.
  - **Kekurangan**: Membutuhkan penyesuaian kelas kontainer di Blade dan penambahan CSS semantik di `cards.css`.

- **Opsi B: Layout Hierarkis 4 Kolom di Atas + 2 Kolom di Bawah (4 + 2)**
  - **Mekanisme**: Memisahkan 4 kartu siklus peserta di baris atas (`repeat(4, 1fr)`) dan 2 kartu alokasi kuota di baris bawah (`repeat(2, 1fr)`).
  - **Kelebihan**: Mengelompokkan 4 kartu siklus peserta secara eksplisit.
  - **Kekurangan**: 2 kartu di baris bawah menjadi 2 kali lebih lebar (~580px) dibanding 4 kartu di baris atas (~280px), menciptakan ketimpangan visual (*asymmetrical weight*) yang kurang sedap dipandang.

> **Keputusan Rekomendasi**: Memilih **Opsi A** (Grid Simetris 3x2) karena memberikan estetika terbaik, ruang bernapas yang proporsional untuk seluruh kartu, dan bebas dari asimetri.

---

## 4. Rincian Penataan & Konten Kartu (Opsi A)

### Baris 1: Pipeline Alur & Bimbingan Peserta
| Posisi | Kartu Metrik | Ikon Fungsional | Warna | Nilai | Trend Badge | Subteks Lengkap (Tanpa Terpotong) | Sparkline Bars |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Kiri** | **Total Pengajuan** | `file-text` | `blue` | `$metricCards['total_pendaftaran']` | `Total` | `Semua berkas masuk portal` | `[35, 50, 45, 65, 70, 85, 95]` |
| **Tengah** | **Peserta Diterima** | `user-check` | `teal` | `$metricCards['total_diterima']` | `Diterima` | `Lolos seleksi berkas & kuota` | `[25, 40, 50, 65, 75, 85, 95]` |
| **Kanan** | **Penempatan Aktif** | `activity` | `blue` | `$metricCards['penempatan_aktif']` | `Aktif` | `Sedang menjalani masa magang` | `[40, 50, 60, 68, 75, 82, 90]` |

### Baris 2: Kelulusan & Utilisasi Kuota
| Posisi | Kartu Metrik | Ikon Fungsional | Warna | Nilai | Trend Badge | Subteks Lengkap (Tanpa Terpotong) | Sparkline Bars |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Kiri** | **Magang Selesai** | `award` | `teal` | `$metricCards['penempatan_selesai']` | `Selesai` | `Telah tuntas dinas & dinilai` | `[30, 45, 55, 65, 75, 85, 100]` |
| **Tengah** | **Realisasi Kuota Umum** | `calendar` | `yellow` | `$metricCards['kuota_terisi']` | `Reguler` | `Kursi terisi jalur online dinas` | `[40, 50, 60, 70, 65, 80, 85]` |
| **Kanan** | **Slot Khusus Terisi** | `briefcase` | `blue` | `$metricCards['slot_terisi']` | `Mitra` | `Alokasi MoU kemitraan dinas` | `[20, 30, 40, 35, 50, 60, 70]` |

---

## 5. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/components/cards.css` | Modify | Menambahkan class layout responsif `.adm-laporan-kpi-grid` (desktop 3 kolom, tablet 2 kolom, mobile 1 kolom) dan perbaikan fleksibilitas ruang teks kartu metrik. |
| `resources/views/admin/laporan/index.blade.php` | Modify | Mengganti kontainer inline style baris kartu metrik dengan class `.adm-laporan-kpi-grid`, merapikan subteks agar lebih informatif dan pas di kartu. |

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1: Menambahkan CSS Layout Semantik di `resources/css/components/cards.css`**
  - Definisikan `.adm-laporan-kpi-grid`:
    - Desktop (&ge; 1100px): `display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.25rem; width: 100%; margin-bottom: 1.75rem;`
    - Tablet (641px - 1099px): `grid-template-columns: repeat(2, minmax(0, 1fr));`
    - Mobile (&le; 640px): `grid-template-columns: 1fr;`
  - Tambahkan penyempurnaan tipografi agar teks label dan subteks tidak terpotong canggung saat kartu memiliki ruang lega.

- [x] **Langkah 2: Memperbarui Markup Kontainer di `resources/views/admin/laporan/index.blade.php`**
  - Ganti kontainer `<div class="row tile_count" style="...">` dengan `<div class="adm-laporan-kpi-grid" role="region" aria-label="Metrik Ringkasan Laporan">`.
  - Rapikan atribut pemanggilan 6 `<x-gentelella.tile-stat>` dengan label dan subteks yang tajam, presisi, dan elegan.

- [x] **Langkah 3: Quality Gate & Verifikasi**
  - Jalankan build aset Vite: `npm run build`.
  - Jalankan format linter: `./vendor/bin/pint --test`.
  - Jalankan test suite relevan:
    - `php artisan test tests/Feature/AdminLaporanDanArsipTest.php`
    - `php artisan test tests/Feature/AdminDashboardEnhancementTest.php`

---

## 7. Strategi Testing
1. **Automated Feature Tests**:
   - Memastikan `AdminLaporanDanArsipTest.php` tetap 100% lulus (memverifikasi akses halaman laporan hub, angka metrik, dan tabel).
2. **Visual & Responsive Verification**:
   - Memeriksa tampilan kartu metrik pada breakpoint Desktop (1440px): 2 baris x 3 kolom simetris.
   - Memeriksa tampilan pada Tablet (768px): 3 baris x 2 kolom.
   - Memeriksa tampilan pada Mobile (375px): 1 kolom menurun rapi.
   - Memastikan tidak ada teks judul yang tertekuk aneh atau subteks yang terpotong `...`.
3. **Dual Theme Verification**:
   - Memastikan latar belakang putih bersih di mode terang dan navy di mode gelap tetap kontras dan jernih.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] 6 kartu metrik di Hub Laporan Admin tidak lagi berjejal dalam 1 baris, melainkan tertata rapi dalam grid simetris 3 kolom x 2 baris.
- [x] Setiap kartu memiliki lebar yang lega (~380px–440px), bebas dari teks terpotong atau berhimpitan dengan sparkline.
- [x] Seluruh CSS berada di `resources/css/components/cards.css` dan dikompilasi melalui Vite (kepatuhan `AGENTS.md`).
- [x] `npm run build` sukses tanpa error.
- [x] Seluruh automated test relevan lulus 100% hijau.
- [x] Tidak ada kode yang diubah sebelum disetujui pengguna.

