# Rencana Kerja: Perombakan Total Tampilan Menu Dashboard Peserta

Dokumen ini merinci rencana teknis untuk merombak tampilan Dashboard Peserta (`/peserta/dashboard`) agar **100% presisi dan identik** dengan desain referensi yang dilampirkan oleh pengguna (`media_1793932733202_53af27af.png`).

---

## 1. Analisis Desain Referensi

Berdasarkan gambar referensi, kanvas dashboard terbagi menjadi struktur layout modern dengan visual token MagKom Design System (Sleman Navy `#02275D`, Sleman Gold `#FFCB05`, dan surface putih bersih dengan border halus):

```
+----------------------------------------------------------------------------------------------------+
| TOP HERO BANNER (Sleman Abyssal Navy Gradient)                                                    |
|  - Kiri: BIDANG APTIKA • Badge Diterima • Badge Sleman • Tanggal                                   |
|          Halo, [Nama Peserta]                                                                       |
|          Peserta Magang Bidang Aptika                                                              |
|          [+ Kerjakan Tugas] (Tombol Sleman Gold Terang)                                            |
|  - Kanan: Circular Progress Gauge (3/13) • PROGRES MAGANG Minggu ke-3 dari 13 minggu               |
+----------------------------------------------------------------------------------------------------+

+-----------------------------------------------------------+ +--------------------------------------+
| KOLOM UTAMA (~68% Lebar)                                  | | KOLOM ASIDE (~32% Lebar)             |
|                                                           | |                                      |
| 1. BARIS 3 KARTU KPI RINGKAS (Row)                        | | 1. WIDGET KALENDER BULANAN           |
|    +----------------+ +----------------+ +---------------+| |    < November 2026 >                 |
|    | Logbook Aktif  | | Tugas Magang   | | Pembimbing    || |    Grid Hari: S S R K J S M          |
|    | 2 laporan      | | 2 aktif        | | Budi Santoso  || |    - Tgl 6 (Hari ini: Navy Bulat)    |
|    | [1 blm diisi]  | | [1 selesai]    | | [Aktif]       || |    - Dot Kuning di tgl tugas/logbook |
|    +----------------+ +----------------+ +---------------+| |    - Legend Kalender                 |
|                                                           | |                                      |
| 2. WIDGET LOGBOOK MINGGUAN                                | | 2. KARTU "PERLU PERHATIAN" (MERAH)   |
|    - Header: | Logbook Mingguan [Minggu 3 dari 13]        | |    ⚠️ Perlu Perhatian                |
|    - Row Stats: [2 Sudah diisi] [1 Belum diisi]           | |    1 logbook                         |
|    - Stepper Mingguan: M1 M2 [M3] M4 M5 M6 ... M13        | |    Logbook Minggu 3 belum dikirim    |
|    - Legend: Sudah diisi, Belum diisi, Mendatang          | |    batas 9 Nov                       |
|                                                           | |                                      |
| 3. WIDGET TUGAS DARI PEMBIMBING                           | | 3. KARTU "SISA HARI MAGANG" (KUNING) |
|    - Header: | Tugas dari Pembimbing                      | |    72 hari tersisa                   |
|    - Row Stats: [3 Total] [1 Dikerjakan] [0 Overdue]      | |    MINGGU KE-3 DARI 13               |
|    - Progress Bar Segmented Pengerjaan Tugas              | |    Dots Progress (13 titik)          |
|                                                           | |    Mulai 19 Okt 2026 - 17 Jan 2027   |
+-----------------------------------------------------------+ +--------------------------------------+
                                             [Portal MagKom • Dinas Kominfo Kabupaten Sleman] (Footer)
```

---

## 2. Rincian Komponen & Perubahan File

### A. Backend Controller (`app/Http/Controllers/Peserta/DashboardController.php`)
Memperkaya payload data untuk view tanpa merusak business logic atau contracts yang ada:
1. Mengambil data mingguan magang menggunakan method bawaan model `Penempatan`:
   - `$daftarMinggu = $penempatan?->getDaftarMingguMagang() ?? []`
   - `$mingguAktif = $penempatan?->minggu_aktif ?? 1`
   - `$totalMinggu = $penempatan?->total_minggu ?? 13`
2. Menghitung ringkasan status mingguan:
   - `$logbookSudahDiisi`: Jumlah minggu dengan status logbook `disetujui` atau `terkirim`.
   - `$logbookBelumDiisi`: Jumlah minggu yang sudah lewat/sedang berjalan tetapi logbook-nya belum diisi.
3. Menghitung ringkasan tugas:
   - `$tugasLewatTenggat`: Tugas aktif yang telah melewati batas deadline (`deadline < now()`).
4. Menghasilkan metadata kalender bulanan:
   - Bulan & tahun aktif (misal November 2026).
   - Array tanggal yang memiliki logbook atau batas akhir tugas (`$eventDates`).
5. Menghitung data kartu "Perlu Perhatian":
   - Jika minggu aktif belum mengunggah logbook, tampilkan deadline (tanggal akhir minggu aktif).

---

### B. View Partials (`resources/views/peserta/dashboard/partials/`)

1. **`_header.blade.php` (Atmospheric Hero Banner)**:
   - Teks kategori atas emas: `BIDANG APTIKA - DISKOMINFO SLEMAN`.
   - Badges: `● Diterima: Magang Aktif` (hijau), `★ Pemkab Sleman` (kuning), tanggal hari ini.
   - Sapaan: `Halo, {{ $user->nama_tampil ?? $user->nama }}`.
   - Subjudul: `Peserta Magang Bidang {{ $penempatan->bidang->nama_bidang ?? 'Aptika' }}`.
   - Tombol utama Sleman Gold cerah: `+ Kerjakan Tugas` (mengarah ke route `peserta.tugas.index`).
   - Widget kanan: **Circular Progress Gauge SVG** dengan label `PROGRES MAGANG`, angka `Minggu ke-X`, dan subteks `dari Y minggu`.

2. **`_kpi_bar.blade.php` (3 Kartu Ringkas Metrik)**:
   - Mengubah grid dari 4 kartu menjadi **3 kartu horizontal** dengan styling pastel modern:
     - **Kartu 1 (Logbook Aktivitas)**: Ikon buku rounded biru lavender, angka besar `{{ $logbookSudahDiisi }} laporan`, badge pill `{{ $logbookBelumDiisi }} belum diisi`.
     - **Kartu 2 (Tugas Magang)**: Ikon tugas rounded kuning emas, angka besar `{{ $tugasBelum }} aktif`, badge pill `{{ $tugasSelesai }} selesai`.
     - **Kartu 3 (Pembimbing Lapangan)**: Ikon person rounded navy, nama pembimbing `{{ $penempatan->pembimbing->nama }}`, badge pill hijau `● Pendampingan aktif`.

3. **`_phase_aktif.blade.php` (Kolom Utama Kiri)**:
   - **Widget 1: Logbook Mingguan**:
     - Header: Garis aksen vertikal Sleman Gold `| Logbook Mingguan` + badge `Minggu {{ $mingguAktif }} dari {{ $totalMinggu }}`.
     - Row Stats: 2 kotak ringkasan (`{{ $logbookSudahDiisi }} Sudah diisi` dan `{{ $logbookBelumDiisi }} Belum diisi`).
     - Stepper Grid Mingguan: Deretan pill kotak `M1` s/d `M13` dengan style:
       - Biru Sleman Navy (`#02275D`) untuk minggu yang sudah diisi.
       - Kuning Sleman Gold (`#FFCB05`) untuk minggu aktif / belum diisi.
       - Abu-abu pattern bergaris untuk minggu mendatang.
     - Legend status pengisian di bagian bawah.
   - **Widget 2: Tugas dari Pembimbing**:
     - Header: Garis aksen vertikal Sleman Gold `| Tugas dari Pembimbing`.
     - Row Stats: 3 kotak ringkasan (`{{ $totalTugas }} Total tugas`, `{{ $tugasSelesai }} Sudah dikerjakan`, `{{ $tugasLewatTenggat }} Lewat tenggat`).
     - Segmented Progress Bar di bagian bawah yang menggambarkan persentase pengerjaan tugas.

4. **`_aside.blade.php` (Kolom Kanan / Sidebar Context)**:
   - **Widget 1: Kalender Bulanan Interaktif**:
     - Header navigasi `< Bulan Tahun >`.
     - Grid hari `S`, `S`, `R`, `K`, `J`, `S`, `M` dengan penomoran tanggal.
     - Hari ini ditandai dengan bulatan Sleman Navy `#02275D`.
     - Tanggal dengan kegiatan ditandai titik kuning (dot indicator).
     - Legend kalender di bawahnya.
   - **Widget 2: Kartu "Perlu Perhatian" (Merah Solid)**:
     - Card warna merah pekat elegan (`#D32F2F`) dengan ikon peringatan segitiga.
     - Nilai: `1 logbook`.
     - Teks pesan: `Logbook Minggu {{ $mingguAktif }} belum dikirim - batas {{ $tglBatasMinggu }}`.
   - **Widget 3: Kartu "Sisa Hari Magang" (Kuning Sleman Gold)**:
     - Card warna kuning emas `#FFCB05` dengan teks tebal kontras `{{ $sisaHari }} hari tersisa`.
     - Subtitle `MINGGU KE-{{ $mingguAktif }} DARI {{ $totalMinggu }}`.
     - Indikator baris titik (segmented dots 13 minggu).
     - Tanggal mulai dan selesai magang di footer kartu.

5. **`dashboard.blade.php` (CSS & Layout)**:
   - Memperbarui layout grid cockpit peserta menjadi 2 kolom (Kiri ~68%, Kanan ~32%).
   - Menyertakan CSS styling yang bersih, scoped, dan terstruktur sesuai MagKom Design System tanpa styling eksternal berlebihan.
   - Menambahkan footer teks kecil di pojok kanan bawah kanvas.

---

## 3. Langkah-Langkah Eksekusi

1. **Persiapan Data di Controller**:
   - Perbarui `app/Http/Controllers/Peserta/DashboardController.php` untuk menghitung `$daftarMinggu`, `$mingguAktif`, `$totalMinggu`, `$logbookSudahDiisi`, `$logbookBelumDiisi`, `$tugasLewatTenggat`, tanggal batas logbook, dan metadata kalender.
2. **Implementasi Partial Views**:
   - Perbarui `_header.blade.php` dengan Circular Progress Gauge dan tombol emas `+ Kerjakan Tugas`.
   - Perbarui `_kpi_bar.blade.php` dengan 3 kartu metrik pastel modern.
   - Perbarui `_phase_aktif.blade.php` dengan widget Logbook Mingguan (stepper M1-M13) dan widget Tugas Pembimbing (stats 3 kotak & segmented progress bar).
   - Perbarui `_aside.blade.php` dengan Kalender Bulanan, Kartu Merah Perlu Perhatian, dan Kartu Kuning Sisa Hari Magang.
3. **Penyempurnaan CSS**:
   - Perbarui styling di `resources/views/peserta/dashboard.blade.php` agar warna, border-radius, typography, grid spacing, dan responsivitasnya presisi sesuai gambar referensi.
4. **Kompilasi & Testing**:
   - Jalankan `npm run build` untuk menguji build frontend.
   - Jalankan `php artisan test` untuk memastikan semua pengujian unit/fitur tetap lulus.
   - Verifikasi tampilan akhir di browser.

---

Mohon ditinjau rencana di atas. Jika Anda menyetujui rencana ini, silakan konfirmasi dan saya akan segera mengeksekusinya secara terstruktur!
