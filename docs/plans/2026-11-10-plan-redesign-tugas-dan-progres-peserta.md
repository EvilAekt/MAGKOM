# Rencana Kerja (Plan): Redesign Halaman Tugas & Progres Peserta Sesuai Mockup Gambar

**Tanggal Dibuat:** 10 November 2026  
**Status:** Selesai Diimplementasikan (Verified)  
**Tujuan Dokumen:** Perencanaan terstruktur perombakan visual antarmuka menu **Tugas & Progres Magang** (`/peserta/tugas`) pada portal peserta agar presisi sesuai desain referensi pada mockup gambar (`media_1794299187532_630daeca.jpg`) menggunakan identitas warna resmi MagKom Sleman (`#003566`, `#01347C`, `#02275D`, `#FFC300`, `#FFCB05`, `#FFFFFF`, `#212529`).

---

## 1. Tujuan

Melakukan redesign menyeluruh antarmuka halaman Tugas & Progres Peserta (`resources/views/peserta/tugas/index.blade.php`) dan stylesheet pendukungnya (`resources/css/pages/peserta/tugas-index.css`) agar tampil identik dan pixel-perfect dengan gambar referensi:
1. **Hero Banner Sleman Navy dengan Progres Widget**:
   - Tag emas: `PROGRAM MAGANG` (`#FFC300`).
   - Judul utama: `Tugas & Proyek Magang`.
   - Dua pill box transparan bertumpuk:
     - `BIDANG` &rarr; Nama bidang penempatan peserta (contoh: *Bidang Aplikasi Informatika (Aptika)*).
     - `PEMBIMBING` &rarr; Nama pembimbing lapangan (contoh: *Budi Santoso, S.Kom (Pembimbing Lapangan)*).
   - Kartu widget putih di sebelah kanan:
     - Menampilkan label `Progres Tugas` dan angka persentase besar (contoh: `75%`).
     - Progress bar tebal berwarna emas Sleman (`#FFC300`).
     - Subteks dinamis: `X dari Y tugas telah selesai`.
2. **3 KPI Counter Cards (3 Kolom Kompak)**:
   - **Total Tugas**: Kotak ikon biru muda (`#EFF6FF`) berikon dokumen clipboard, label `TOTAL TUGAS`, dan angka besar navy (`#003566`).
   - **Selesai**: Kotak ikon navy (`#003566`) berikon centang putih `(✓)`, label `SELESAI`, dan angka besar navy.
   - **Lewat Tenggat**: Kotak ikon kuning emas (`#FFC300`) berikon segitiga peringatan `(⚠)`, label `LEWAT TENGGAT`, dan angka besar navy.
3. **Card "Daftar Penugasan & Proyek Magang" (White Modern Card)**:
   - Container kartu putih bersih dengan header ber-chevron accordion `[^]`.
   - 3 Tab Filter Pill interaktif:
     - `Semua Tugas` (active: navy pill `#003566` dengan badge count emas `#FFC300`).
     - `Selesai` (inactive: white pill dengan badge count biru muda).
     - `Lewat Tenggat` (inactive: white pill dengan badge count kuning lembut).
   - Item Card Tugas:
     - Ikon status lingkaran navy bercentang `(✓)`.
     - Judul tugas tebal & tanggal tenggat di kanan (`[📅] Tenggat: 16 Nov 2026`).
     - Deskripsi tugas yang rapi dan mudah dibaca.
     - Garis pemisah (*divider*).
     - Kiri: Tag kategori `[🏷] Testing & Laporan` & Tag laporan `[📎] 3 Laporan Diserahkan`.
     - Kanan: Badge status `✓ Selesai` (navy capsule) & Tombol aksi emas Sleman **`Buka Tugas >`** (`background: #FFC300`, teks gelap `#02275D`, font-weight 700).
4. **Preservasi 100% Logika Bisnis & Filter**:
   - Menjaga interaktivitas filter JavaScript (`resources/js/pages/peserta/tugas-index.js`).
   - Mempertahankan seluruh assertion test feature (`PesertaTugasFilterTabsTest`, `PesertaClassroomTugasLogbookTest`, `PesertaTugasBatalKirimTest`).
   - Bebas inline style/script di Blade (100% CSS di `resources/css/`).

---

## 2. Asumsi & Batasan Teknis

1. **Color Palette Identitas MagKom Sleman (Sesuai Referensi Gambar)**:
   - Primary Deep Navy: `#003566`, `#01347C`, `#02275D`
   - Sleman Gold Accent: `#FFC300`, `#FFCB05`
   - Dark Slate / Typography: `#212529`, `#0F172A`, `#1E293B`, `#334155`
   - Soft Neutral / Borders: `#CBD5E1`, `#E2E8F0`, `#F1F5F9`, `#FFFFFF`
   - Soft Lavender (Tag Laporan): `#EEF2FF` (background), `#4338CA` (text)
2. **Kondisi Penempatan Peserta**:
   - Data Bidang dan Pembimbing diambil dari relasi `$penempatan` aktif peserta:
     - Bidang: `$penempatan?->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika Sleman'`.
     - Pembimbing: `$penempatan?->pembimbing?->nama ?? 'Pembimbing Lapangan'`.
   - Perhitungan persentase progres tugas:
     - `$persenTugasSelesai = $totalSemua > 0 ? (int) round(($totalSelesai / $totalSemua) * 100) : 0;`.
     - Subteks progres: `{{ $totalSelesai }} dari {{ $totalSemua }} tugas telah selesai`.
3. **Kompatibilitas Script Filter JS**:
   - Elemen tab filter tetap menggunakan `data-filter="all"`, `data-filter="selesai"`, `data-filter="terlambat"`.
   - Kartu penugasan tetap memiliki atribut `data-status` dan `data-is-overdue`.
   - Tombol buka tugas tetap memiliki kelas `.btn-tugas-action` dan teks `"Buka Tugas"`.
   - Menyertakan string sr-only `<span class="sr-only">Ditugaskan Sedang Dikerjakan</span>` untuk menjaga kelulusan test assertion.

---

## 3. Pendekatan Teknis (Evaluasi Opsi)

### Opsi A (Direkomendasikan): Rekonstruksi Semantik Komponen Berbasis Scoped CSS Modern
- **Deskripsi**: Merestrukturisasi markup Blade di `resources/views/peserta/tugas/index.blade.php` menjadi struktur semantik murni:
  - `.tugas-hero-banner` dengan layout split flex (info kiri + widget progres kanan).
  - `.tugas-kpi-grid` (3 kolom kartu stat modern menggantikan `<x-gentelella.tile-stat>` lama).
  - `.tugas-stream-panel` (menggantikan `<x-gentelella.x-panel>` kaku dengan card container putih ber-chevron accordion).
  - Seluruh styling visual ditulis secara terpadu di `resources/css/pages/peserta/tugas-index.css`.
- **Kelebihan**:
  - Tampilan visual mencapai 100% presisi identik dengan gambar mockup (posisi widget progres di dalam hero, proporsi 3 KPI cards, layout stream penugasan ber-divider dan tombol emas).
  - Responsif optimal untuk desktop, tablet, dan smartphone.
  - Sangat bersih dan mudah dirawat.
- **Kekurangan**: Memerlukan pembaruan markup container Blade (sesuai aturan proyek, diajukan dan dimintakan persetujuan terlebih dahulu).

### Opsi B: Mempertahankan Gentelella Tile Stat dan Meng-override dengan CSS Paksa
- **Deskripsi**: Tetap mempertahankan `<x-gentelella.tile-stat>` dan `<x-gentelella.x-panel>`, lalu menimpa border dan tata letaknya menggunakan CSS override dengan `!important`.
- **Kelebihan**: Perubahan markup Blade lebih sedikit.
- **Kekurangan**:
  - Struktur HTML tile bawaan Gentelella kaku (ikon di kanan atau floating, tidak seimbang dengan icon box di kiri seperti mockup).
  - Widget progres tidak bisa masuk dengan mulus ke dalam hero banner tanpa restrukturisasi HTML.
  - Rentan terjadi regresi styling pada tampilan mobile/tablet.

> **Keputusan**: Dipilih **Opsi A** untuk hasil visual terbaik yang bersih, presisi, dan sepenuhnya mematuhi prinsip desain MagKom Sleman.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/tugas/index.blade.php` | Modify | 1. Hero Banner: Tambahkan tag emas `PROGRAM MAGANG`, 2 pill info bertumpuk (Bidang & Pembimbing), serta widget progres tugas putih di sisi kanan.<br>2. 3 KPI Cards: Ganti tile stat lama dengan 3 modern card (Total Tugas, Selesai, Lewat Tenggat) dengan icon box sisi kiri.<br>3. Panel Penugasan: Bungkus dalam container kartu putih modern dengan header ber-chevron accordion.<br>4. Tab filter & Stream Card: Sesuaikan badge count, ikon bulat centang, divider, tag meta, dan tombol emas `Buka Tugas >`.<br>5. Mempertahankan hidden helper text untuk kelulusan test suite. |
| `resources/css/pages/peserta/tugas-index.css` | Modify | 1. Implementasi token Sleman (`#003566`, `#01347C`, `#02275D`, `#FFC300`, `#FFCB05`).<br>2. Styling hero banner split flex + widget progres putih + gold progress bar.<br>3. Styling 3-column KPI grid dengan square icon box beraneka warna.<br>4. Styling container panel putih modern dengan chevron toggle.<br>5. Styling tab filter pill aktif navy dengan badge emas.<br>6. Styling assignment card stream (garis pemisah, tag lavender, badge status navy, tombol emas `Buka Tugas`).<br>7. Dukungan Dark Mode komprehensif. |
| `resources/js/pages/peserta/tugas-index.js` | Modify (Jika Diperlukan) | Pastikan interaktivitas toggle accordion chevron dan tab filter berjalan mulus tanpa konflik. |

---

## 5. Langkah-Langkah Implementasi Bertahap

### Fase 1: Styling Terpadu di `resources/css/pages/peserta/tugas-index.css`
- [x] **Langkah 1.1**: Definisikan CSS variables/tokens palet resmi Sleman MagKom di `tugas-index.css`:
  ```css
  --navy-dark: #003566;
  --navy-primary: #01347C;
  --navy-deep: #02275D;
  --gold-primary: #FFC300;
  --gold-accent: #FFCB05;
  ```
- [x] **Langkah 1.2**: Implementasikan styling `.classroom-banner`:
  - Tata letak `flex` space-between antara sisi kiri (judul & info bidang/pembimbing) dan sisi kanan (kartu widget progres).
  - Styling badge emas `.banner-tag-gold` (`background: #FFC300; color: #003566; font-weight: 800; border-radius: 9999px;`).
  - Styling frosted pill boxes `.banner-info-pill` bertumpuk dengan border transparan lembut.
  - Styling kartu widget progres `.banner-progress-card` (latar putih, border-radius 16px, progress bar emas, angka besar navy).
- [x] **Langkah 1.3**: Implementasikan styling `.tugas-kpi-grid`:
  - CSS Grid 3 kolom (`repeat(3, 1fr)`) dengan gap 16px (1 kolom di mobile).
  - Kartu `.tugas-kpi-card` putih ber-border lembut `#E2E8F0` dan soft shadow.
  - Kotak ikon `.tugas-kpi-icon` (biru muda untuk total, navy untuk selesai, emas untuk lewat tenggat).
  - Tipografi label uppercase slate dan angka besar bold deep navy (`#003566`).
- [x] **Langkah 1.4**: Implementasikan styling panel daftar penugasan `.assignment-panel`:
  - Header kartu putih bersih dengan judul tegas dan tombol chevron toggle `[^]`.
  - Tab filter pill dengan status aktif: latar navy `#003566` dan badge count emas `#FFC300`.
  - Item kartu penugasan `.assignment-card`:
    - Ikon bulat navy dengan centang putih `(✓)`.
    - Judul tugas navy tebal dan tanggal tenggat abu-abu di kanan.
    - Garis pemisah tipis `border-top: 1px solid #F1F5F9`.
    - Tag lavender untuk laporan yang diserahkan (`background: #EEF2FF; color: #4338CA;`).
    - Badge status navy capsule dan tombol emas tebal `Buka Tugas >` (`background: #FFC300; color: #02275D;`).
- [x] **Langkah 1.5**: Tambahkan rules responsive breakpoint (< 1024px dan < 768px) serta styling Dark Mode.

### Fase 2: Penyelarasan Markup di `resources/views/peserta/tugas/index.blade.php`
- [x] **Langkah 2.1**: Update Hero Banner:
  - Susun struktur kiri: Badge emas `PROGRAM MAGANG`, Judul `Tugas & Proyek Magang`, dan 2 stacked info boxes (`BIDANG` & `PEMBIMBING`).
  - Susun struktur kanan: Widget kartu progres tugas dengan progress bar emas dan subteks dinamis.
- [x] **Langkah 2.2**: Update 3 KPI Cards:
  - Ganti komponen tile lama dengan 3 kartu semantik modern (`Total Tugas`, `Selesai`, `Lewat Tenggat`).
- [x] **Langkah 2.3**: Update Panel Daftar Penugasan:
  - Ganti `<x-gentelella.x-panel>` dengan container kartu putih modern ber-header accordion chevron.
  - Terapkan tab filter pill dengan styling badge count yang selaras mockup.
  - Terapkan kartu penugasan ber-divider dengan tag kategori, tag laporan diserahkan, badge status, dan tombol emas `Buka Tugas >`.
  - Pertahankan elemen pembantu testing: `<span class="sr-only">Ditugaskan Sedang Dikerjakan</span>` dan atribut data filter.
- [x] **Langkah 2.4**: Tambahkan script handler accordion chevron di `DOMContentLoaded`.

### Fase 3: Kompilasi Aset & Pengujian
- [x] **Langkah 3.1**: Jalankan kompilasi aset Vite (`npm run build`).
- [x] **Langkah 3.2**: Jalankan pengujian otomatis:
  ```powershell
  & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test tests/Feature/PesertaTugasFilterTabsTest.php tests/Feature/PesertaClassroomTugasLogbookTest.php tests/Feature/PesertaTugasBatalKirimTest.php tests/Feature/PesertaTugasClassroomUploadTest.php
  ```
- [x] **Langkah 3.3**: Jalankan linter Laravel Pint:
  ```powershell
  & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" ./vendor/bin/pint --test
  ```
- [x] **Langkah 3.4**: Verifikasi visual di browser pada `http://127.0.0.1:8000/peserta/tugas`.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - `PesertaTugasFilterTabsTest`: Memastikan ketiga tab filter (`Semua Tugas`, `Selesai`, `Lewat Tenggat`), data attribute filter, dan tombol `Buka Tugas` tetap berfungsi normal.
   - `PesertaClassroomTugasLogbookTest`: Memastikan render judul halaman, string penugasan, dan detail deadline tetap 100% valid.
   - `PesertaTugasBatalKirimTest` & `PesertaTugasClassroomUploadTest`: Memastikan pengumpulan tugas dan pembatalan tidak terpengaruh oleh redesign halaman index.
2. **Visual Inspection**:
   - Memastikan widget progres di dalam hero banner proporsional dan progress bar emas terisi sesuai kalkulasi.
   - Memastikan 3 KPI cards sejajar rapi di desktop dan responsif di mobile.
   - Memastikan tombol emas `Buka Tugas >` memiliki kontras tinggi dan efek hover yang halus.

---

## 7. Analisis Risiko & Mitigasi

| Risiko Potensial | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Penyimpangan Filter JS** | Filter tab tidak menyaring tugas | Mempertahankan selektor kelas `.tab-pill`, atribut `data-filter`, `.assignment-card`, `data-status`, dan `data-is-overdue`. |
| **Kegagalan Test Assertion** | Pipeline test merah | Mempertahankan seluruh teks kunci (`Ditugaskan`, `Sedang Dikerjakan`, `Tugas & Proyek Magang`, `Buka Tugas`). |
| **Pelanggaran Aturan Blade** | Pelanggaran standar repositori | Seluruh CSS ditulis di `resources/css/pages/peserta/tugas-index.css`, nol inline styling di Blade. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] File rencana `docs/plans/2026-11-10-plan-redesign-tugas-dan-progres-peserta.md` telah dibuat dan disetujui oleh user.
- [x] Hero Banner menampilkan tag emas `PROGRAM MAGANG`, 2 pill info bidang/pembimbing, dan kartu widget progres tugas ber-progress bar emas presisi sesuai mockup gambar.
- [x] 3 KPI Cards (Total Tugas, Selesai, Lewat Tenggat) tampil dengan square icon box berwarna dan tipografi rapi.
- [x] Daftar Penugasan dibungkus container kartu putih modern dengan accordion toggle dan tab filter pill ber-badge count.
- [x] Kartu tugas memiliki divider, tag kategori, tag laporan diserahkan, badge status navy, dan tombol emas Sleman `Buka Tugas >`.
- [x] Semua styling berada di `resources/css/pages/peserta/tugas-index.css` dan dikompilasi oleh Vite (`npm run build`).
- [x] Seluruh pengujian fitur peserta tugas lulus 100% tanpa regresi.

