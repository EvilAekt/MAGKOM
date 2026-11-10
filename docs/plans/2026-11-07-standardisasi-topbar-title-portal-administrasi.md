# Rencana Kerja: Standarisasi Header Navigasi Portal Administrasi (Topbar Title Bersih Tanpa Breadcrumbs)

Dokumen perencanaan teknis untuk standarisasi antarmuka bilah navigasi atas (*topbar header*) pada seluruh modul portal administrasi MagKom, menggantikan rantai *breadcrumb* panjang (`Beranda / Dashboard / ...`) menjadi judul halaman tegas (`topbar_title`) langsung di samping tombol menu *hamburger*.

---

## 1. Tujuan
1. **Menghilangkan Rantai Breadcrumb Redundan**: Menghapus tampilan `Beranda / Dashboard / [Nama Halaman]` pada bilah navigasi atas di portal admin karena admin sudah berada di dalam sistem operasional tertutup (*intranet/internal portal*).
2. **Menerapkan Judul Halaman Tegas (Topbar Title)**: Mengubah seluruh header modul administrasi menjadi judul langsung yang tebal, putih, dan elegan (`<h1 class="topbar-title">`) tepat di samping tombol *hamburger*, persis seperti acuan desain pada portal pembimbing (Gambar 2).
3. **Penyelarasan & Konsistensi Total**: Menerapkan pola ini secara merata ke seluruh 13 halaman modul di bawah `resources/views/admin/`.

---

## 2. Analisis Komparasi UI (Gambar 1 vs Gambar 2)

### A. Kondisi Saat Ini (Gambar 1)
```
[ ☰ ]  Beranda  /  Dashboard  /  Verifikasi Berkas Pendaftaran Peserta
```
- **Masalah**:
  - Teks terlalu panjang dan bertingkat-tingkat (*breadcrumb clutter*).
  - Mengurangi fokus dan keterbacaan judul utama halaman.
  - Memakan ruang horizontal pada layar tablet dan desktop kecil.

### B. Desain Acuan yang Ditetapkan (Gambar 2 - Portal Pembimbing)
```
[ ☰ ]  Direktori Peserta Bimbingan
```
- **Keunggulan**:
  - Judul tegas, elegan, bersih, dan berbobot (*font Lexend, weight 700, color #ffffff, size 1.25rem*).
  - Langsung memberikan konteks instan bagi staf mengenai modul aktif yang sedang dibuka.
  - Arsitektur layout `layouts/dashboard.blade.php` sudah memiliki mekanisme native:
    ```blade
    @hasSection('topbar_title')
        <h1 class="topbar-title">@yield('topbar_title')</h1>
    @else
        <!-- Breadcrumb Trail -->
        <nav class="breadcrumb-trail">...</nav>
    @endif
    ```

---

## 3. Inventarisasi & Pemetaan 13 Modul Administrasi

Seluruh 13 halaman modul admin akan dialihkan dari `@section('breadcrumbs')` menjadi `@section('topbar_title', '...')`:

| No | File View Blade | Judul Topbar Baru (`topbar_title`) | Keterangan Modul |
|:---:|:---|:---|:---|
| 1 | `admin/pendaftaran/index.blade.php` | **Verifikasi Berkas Pendaftaran Peserta** | Meja verifikasi berkas permohonan |
| 2 | `admin/pendaftaran/show.blade.php` | **Verifikasi Berkas Pendaftaran Peserta** | Detail verifikasi & telaah dokumen |
| 3 | `admin/dashboard.blade.php` | **Dashboard Operasional Administrasi** | Dasbor utama metrik & operasional |
| 4 | `admin/kuota/index.blade.php` | **Manajemen Kuota Magang** | Pengaturan periode bulan & kuota jurusan |
| 5 | `admin/penempatan/index.blade.php` | **Penempatan Peserta Magang** | Alokasi pembimbing & bidang kerja |
| 6 | `admin/penempatan/show.blade.php` | **Detail Penempatan Peserta** | Profil penempatan & monitoring progres |
| 7 | `admin/waiting-list/index.blade.php` | **Antrean Waiting List** | Manajemen antrean cadangan berkas |
| 8 | `admin/laporan/index.blade.php` | **Hub Laporan & Ekspor Data** | Pusat laporan komprehensif & rekap |
| 9 | `admin/laporan/tahunan.blade.php` | **Dashboard Analitik Rekapitulasi Tahunan {{ $tahun }}** | Analitik tahunan & evaluasi mutu |
| 10 | `admin/survei/index.blade.php` | **Evaluasi Mutu & Survei Kepuasan** | Indeks kepuasan magang (IKM/NPS) |
| 11 | `admin/sertifikat/index.blade.php` | **Hub Penerbitan Sertifikat Kelulusan** | Penerbitan & e-sertifikat alumni |
| 12 | `admin/profil/show.blade.php` | **Profil Saya** | Profil akun staf admin |
| 13 | `admin/pengaturan/index.blade.php` | **Akun & Pengaturan** | Preferensi akun & keamanan sistem |

---

## 4. Rencana Langkah Implementasi Terstruktur

- [ ] **Langkah 1**: Refaktor Meja Verifikasi Berkas:
  - `resources/views/admin/pendaftaran/index.blade.php`: Ganti `@section('breadcrumbs')` dengan `@section('topbar_title', 'Verifikasi Berkas Pendaftaran Peserta')`.
  - `resources/views/admin/pendaftaran/show.blade.php`: Ganti `@section('breadcrumbs')` dengan `@section('topbar_title', 'Verifikasi Berkas Pendaftaran Peserta')`. Pada tombol kembali, pastikan memuat teks `Kembali ke Verifikasi Berkas Pendaftaran Peserta` untuk menjaga keutuhan assertion pengujian.
- [ ] **Langkah 2**: Refaktor Dasbor & Alur Seleksi:
  - `resources/views/admin/dashboard.blade.php`: Ganti `@section('breadcrumbs')` dengan `@section('topbar_title', 'Dashboard Operasional Administrasi')`.
  - `resources/views/admin/kuota/index.blade.php`: Ganti `@section('breadcrumbs')` dengan `@section('topbar_title', 'Manajemen Kuota Magang')`.
  - `resources/views/admin/waiting-list/index.blade.php`: Ganti `@section('breadcrumbs')` dengan `@section('topbar_title', 'Antrean Waiting List')`.
- [ ] **Langkah 3**: Refaktor Modul Penempatan:
  - `resources/views/admin/penempatan/index.blade.php`: Ganti `@section('breadcrumbs')` dengan `@section('topbar_title', 'Penempatan Peserta Magang')`.
  - `resources/views/admin/penempatan/show.blade.php`: Ganti `@section('breadcrumbs')` dengan `@section('topbar_title', 'Detail Penempatan Peserta')`.
- [ ] **Langkah 4**: Refaktor Modul Laporan, Survei, dan Sertifikat:
  - `resources/views/admin/laporan/index.blade.php`: Ganti `@section('breadcrumbs')` dengan `@section('topbar_title', 'Hub Laporan & Ekspor Data')`.
  - `resources/views/admin/laporan/tahunan.blade.php`: Ganti `@section('breadcrumbs')` dengan `@section('topbar_title', "Dashboard Analitik Rekapitulasi Tahunan {$tahun}")`.
  - `resources/views/admin/survei/index.blade.php`: Ganti `@section('breadcrumbs')` dengan `@section('topbar_title', 'Evaluasi Mutu & Survei Kepuasan')`.
  - `resources/views/admin/sertifikat/index.blade.php`: Tambahkan `@section('topbar_title', 'Hub Penerbitan Sertifikat Kelulusan')`.
- [ ] **Langkah 5**: Refaktor Profil & Pengaturan Akun:
  - `resources/views/admin/profil/show.blade.php`: Tambahkan `@section('topbar_title', 'Profil Saya')`.
  - `resources/views/admin/pengaturan/index.blade.php`: Tambahkan `@section('topbar_title', 'Akun & Pengaturan')`.
- [ ] **Langkah 6**: Quality Gate & Verifikasi Pengujian:
  - Jalankan `php artisan test --filter "AdminChildPages|AdminDashboard|AdminVerifikasi|AdminLaporan|AdminAnalitik"`.
  - Jalankan `vendor\bin\pint.bat --test`.
  - Jalankan `npm run build`.

---

## 5. Strategi Testing & Pencegahan Regresi
- Seluruh assertion pengujian visual seperti `assertSee('Verifikasi Berkas Pendaftaran Peserta')` akan tetap lulus karena string tersebut kini dirender langsung di dalam tag `<h1>` bilah atas.
- Tidak ada query database atau business logic yang berubah (murni refaktor presentasi header).

---

## 6. Kriteria Selesai (Definition of Done)
- [ ] Seluruh 13 view admin tidak lagi menampilkan rantai `Beranda / Dashboard / ...`.
- [ ] Setiap halaman admin menampilkan judul langsung yang tebal, tegas, dan elegan di samping tombol menu *hamburger*.
- [ ] Seluruh pengujian fitur admin lulus 100%.
- [ ] Linter Pint dan Vite build bebas error.

