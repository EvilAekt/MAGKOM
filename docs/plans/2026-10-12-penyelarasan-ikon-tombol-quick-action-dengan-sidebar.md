# Rencana Kerja: Penyelarasan Ikon Tombol Aksi Peserta dengan Ikon Navigasi Sidebar

## 1. Ringkasan Kebutuhan
- **Latar Belakang**:
  Pada menu navigasi sidebar peserta ([`resources/views/layouts/peserta.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/peserta.blade.php)), setiap fitur magang memiliki ikon identitas resmi:
  1. **Logbook Mingguan**: Ikon Buku Terbuka (*Open Book / Journal*).
  2. **Tugas & Progres**: Ikon Papan Tugas dengan Tanda Centang (*Clipboard Checklist*).
  3. **Lembar Nilai**: Ikon Sertifikat Prestasi / Bintang Penghargaan (*Evaluation Badge / Certificate*).
- **Masalah Saat Ini**:
  Pada tombol aksi cepat di Dashboard Peserta ([`resources/views/peserta/dashboard.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard.blade.php)), ketiga tombol menggunakan ikon yang berbeda:
  - Tombol *"Isi Logbook Mingguan"* menggunakan ikon Pensil Edit (`edit/pencil`).
  - Tombol *"Lihat Daftar Tugas"* menggunakan ikon Clipboard polos tanpa centang.
  - Tombol *"Cek Lembar Nilai"* menggunakan ikon Grafik Batang (`bar chart`).
- **Permintaan Pengguna**:
  Menyelaraskan ikon tombol-tombol yang memiliki nama dan fungsi sama agar seragam 100% menggunakan ikon yang sama persis seperti pada menu sidebar.

---

## 2. Pemetaan & Perbandingan Ikon SVG

| Nama Fitur / Tombol | Ikon Saat Ini (Quick Action) | Ikon Target Resmi (Sidebar MagKom) | Path SVG Target |
| :--- | :--- | :--- | :--- |
| **Isi Logbook Mingguan** | Pensil Edit (`M11 5H6...`) | **Buku Terbuka** (*Open Book*) | `d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"` |
| **Lihat Daftar Tugas** | Clipboard polos tanpa centang | **Clipboard dengan Checklist** | `d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"` |
| **Cek Lembar Nilai** | Grafik Batang Chart | **Sertifikat / Bintang Prestasi** | `d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"` |

---

## 3. Rencana Modifikasi File

### Target 1: [`resources/views/peserta/dashboard.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard.blade.php)
Pada blok `.quick-actions-bar` (baris 600–620):
1. Ganti SVG tombol **Isi Logbook Mingguan** dengan SVG Buku Terbuka (ukuran 18x18, `stroke-width="2"`).
2. Ganti SVG tombol **Lihat Daftar Tugas** dengan SVG Clipboard Checklist (ukuran 18x18, `stroke-width="2"`).
3. Ganti SVG tombol **Cek Lembar Nilai** dengan SVG Sertifikat / Bintang Penghargaan (ukuran 18x18, `stroke-width="2"`).

### Target 2: Audit Tombol Terkait Lainnya (Opsional & Serasi)
- Periksa tombol aksi logbook pada header halaman Logbook ([`resources/views/peserta/logbook/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/logbook/index.blade.php)) untuk memastikan jika menggunakan ikon, selalu selaras dengan ikon Buku Terbuka resmi MagKom.

---

## 4. Rencana Verifikasi
1. **Verifikasi Sintaks Blade**: Memastikan tidak ada tag SVG yang unclosed atau merusak tata letak tombol.
2. **Uji Render Programatik**: Memastikan view `peserta.dashboard` berhasil di-render dengan ketiga path SVG sidebar baru.
3. **Automated Feature Tests**: Menjalankan `php artisan test --filter=DashboardModulesRenderingTest` untuk memverifikasi fungsionalitas view.

---

## 5. Pertanyaan Konfirmasi Pengguna
Apakah rencana penyesuaian ikon tombol quick action agar seragam dengan ikon sidebar ini disetujui untuk diimplementasikan?
