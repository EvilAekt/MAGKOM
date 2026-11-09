# Rencana Kerja: Penyelarasan Header Banner, Harmonisasi Penamaan, dan Pembersihan Badge Kuota

Dokumen rencana kerja ini disusun untuk menuntaskan 3 permintaan penyelarasan visual dan harmonisasi penamaan pada antarmuka Administrator MagKom berdasarkan 3 tangkapan layar yang diunggah pengguna.

---

## 1. Tujuan

1. **Pembersihan Badge "Bulan Aktif" (Poin 1 / Gambar 1)**:
   - Menghapus elemen badge `<span class="hero-badge-caps">Bulan Aktif</span>` beserta titik pemisahnya (`•`) pada hero header halaman **Manajemen Kuota Magang** ([`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php)).
   - Di bawah judul tersebut sudah terdapat status pill dinamis (`Dibuka` / `Ditutup`) dan periode tanggal resmi, sehingga badge ini redundan.
2. **Penyelarasan Judul Hero Banner Laporan agar Tidak Kedouble (Poin 2 / Gambar 2)**:
   - Pada halaman **Hub Laporan & Ekspor**, navbar atas (topbar) saat ini menampilkan *"Hub Laporan & Ekspor Data"*, dan kartu hero banner di bawahnya juga menampilkan teks yang sama persis (*"Hub Laporan & Ekspor Data"*).
   - Mengubah judul pada kartu hero banner di [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php) menjadi lebih formal, informatif, dan tidak menduplikasi navbar atas:
     - **Judul Banner Usulan**: **`Pusat Rekapitulasi & Ekspor Kedinasan`**
     - **Subjudul Banner**: `Rekapitulasi Pendaftaran, Penempatan & Arsip Magang • Dinas Komunikasi dan Informatika Kabupaten Sleman`
     - **Topbar Title (Tetap)**: `Hub Laporan & Ekspor Data`
3. **Harmonisasi dan Evaluasi Teks Penempatan Peserta (Poin 3 / Gambar 3)**:
   - Mengevaluasi teks pada hero banner halaman **Penempatan Peserta** ([`resources/views/admin/penempatan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/penempatan/index.blade.php)):
     - Saat ini:
       - Topbar: `Penempatan Peserta Magang`
       - Hero Title: `Distribusi & Alokasi Peserta Magang`
       - Subtitle: `Penetapan Pembimbing Lapangan & Penempatan Bidang Dinas • Dinas Komunikasi dan Informatika Kabupaten Sleman`
     - **Evaluasi Kesesuaian**: Istilah *"Distribusi & Alokasi"* terkesan dingin/kaku seperti logistik barang. Di lingkungan kedinasan Diskominfo, fokus utama halaman ini adalah **penempatan unit kerja/bidang dinas** dan **penugasan pembimbing lapangan**.
     - **Usulan Penyelarasan**:
       - **Judul Banner Diselaraskan**: **`Alokasi Penempatan & Pembimbing Magang`**
       - **Subjudul Banner Diselaraskan**: `Penetapan Bidang Dinas & Pembimbing Lapangan • Dinas Komunikasi dan Informatika Kabupaten Sleman`
       - **Topbar Title (Tetap)**: `Penempatan Peserta Magang`
     - Menyelaraskan assertion pada automated test [`tests/Feature/AdminChildPagesVisualTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminChildPagesVisualTest.php).

---

## 2. Asumsi & Batasan

- **Kepatuhan Desain Sistem**: Seluruh perubahan penamaan tetap mematuhi hierarki tipografi dan gaya kartu hero banner signature Sleman Navy yang sudah ada (`.adm-hero-banner`).
- **Zero Breaking Contract**: Nama route, controller action, dan fungsionalitas backend tidak berubah. Hanya penyesuaian teks Blade dan assertion test visual terkait.
- **Konsistensi Lintas Halaman Administrasi**:
  Tiga halaman utama kini memiliki pola penamaan yang konsisten, berpasangan, dan tidak duplikat:
  1. **Pendaftaran**: Topbar: *Verifikasi Berkas Pendaftaran Peserta* | Card: *Daftar Pendaftaran Peserta Magang*
  2. **Penempatan**: Topbar: *Penempatan Peserta Magang* | Card: *Alokasi Penempatan & Pembimbing Magang*
  3. **Laporan**: Topbar: *Hub Laporan & Ekspor Data* | Card: *Pusat Rekapitulasi & Ekspor Kedinasan*

---

## 3. Rencana Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/kuota/index.blade.php` | **Modify** | Hapus badge `<span class="hero-badge-caps">Bulan Aktif</span>` dan bullet pemisah `•` pada header eyebrow (baris 895–896). |
| `resources/views/admin/laporan/index.blade.php` | **Modify** | Ganti judul hero banner dari `Hub Laporan &amp; Ekspor Data` menjadi `Pusat Rekapitulasi &amp; Ekspor Kedinasan` agar tidak kedouble dengan navbar. |
| `resources/views/admin/penempatan/index.blade.php` | **Modify** | Selaraskan judul banner dari `Distribusi &amp; Alokasi Peserta Magang` menjadi `Alokasi Penempatan &amp; Pembimbing Magang` dan subjudul menjadi `Penetapan Bidang Dinas &amp; Pembimbing Lapangan • Dinas Komunikasi dan Informatika Kabupaten Sleman`. |
| `tests/Feature/AdminChildPagesVisualTest.php` | **Modify** | Perbarui assertion visual dari `Distribusi &amp; Alokasi Peserta Magang` menjadi `Alokasi Penempatan &amp; Pembimbing Magang`. |

---

## 4. Langkah-Langkah Implementasi Bertahap

- [x] **Fase 1: Pembersihan Badge Kuota (Gambar 1)**
  - [x] 1.1 Edit [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php): Hapus bullet dan badge `Bulan Aktif` pada blok `.hero-header-eyebrow`.

- [x] **Fase 2: Harmonisasi Teks Hero Banner Laporan (Gambar 2)**
  - [x] 2.1 Edit [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php): Ganti judul hero banner menjadi `Pusat Rekapitulasi &amp; Ekspor Kedinasan`.

- [x] **Fase 3: Penyelarasan Teks Hero Banner Penempatan (Gambar 3)**
  - [x] 3.1 Edit [`resources/views/admin/penempatan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/penempatan/index.blade.php): Ganti judul banner menjadi `Alokasi Penempatan &amp; Pembimbing Magang` dan subjudul menjadi `Penetapan Bidang Dinas &amp; Pembimbing Lapangan • Dinas Komunikasi dan Informatika Kabupaten Sleman`.
  - [x] 3.2 Edit [`tests/Feature/AdminChildPagesVisualTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminChildPagesVisualTest.php): Selaraskan assertion visual baris 161.
  - [x] 3.3 Edit [`tests/Feature/AdminKuotaRevampTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminKuotaRevampTest.php): Selaraskan assertion dari assertSee menjadi assertDontSee('Bulan Aktif').

- [x] **Fase 4: Verifikasi & Quality Gates**
  - [x] 4.1 Jalankan test spesifik: `vendor/bin/phpunit tests/Feature/AdminChildPagesVisualTest.php tests/Feature/AdminLaporanDanArsipTest.php`.
  - [x] 4.2 Jalankan Laravel Pint: `vendor/bin/pint --test`.
  - [x] 4.3 Jalankan full test suite PHPUnit: `vendor/bin/phpunit` (719 passed, 0 failures).
  - [x] 4.4 Jalankan Vite build: `npm run build`.

---

## 5. Strategi Testing

1. **Uji Halaman Kuota**: Membuka `/admin/kuota` memastikan badge `Bulan Aktif` tidak lagi tampil pada header hero.
2. **Uji Halaman Laporan**: Membuka `/admin/laporan` memastikan topbar menampilkan *"Hub Laporan & Ekspor Data"* dan kartu hero banner menampilkan *"Pusat Rekapitulasi & Ekspor Kedinasan"*, menghilangkan duplikasi teks.
3. **Uji Halaman Penempatan**: Membuka `/admin/penempatan` memastikan banner menampilkan *"Alokasi Penempatan & Pembimbing Magang"*.
4. **Uji Regresi**: Memastikan seluruh 719 test suite tetap 100% hijau.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Badge "Bulan Aktif" bersih dari halaman kuota.
- [x] Judul kartu hero banner Laporan tidak lagi kembar dengan navbar atas.
- [x] Judul kartu hero banner Penempatan lebih selaras, profesional, dan mencerminkan penempatan bidang serta pembimbing.
- [x] Seluruh automated test suite lulus 100% (719 tests, 4978 assertions).
- [x] Format kode dan build Vite berhasil tanpa error.

