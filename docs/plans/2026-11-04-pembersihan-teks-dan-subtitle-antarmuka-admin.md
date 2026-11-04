# Plan: Pembersihan Teks Redundan & Subtitle Antarmuka Administrasi (Clean & Minimalist Admin UI)

**Tanggal**: 04 November 2026  
**Status**: Selesai Terverifikasi  
**Referensi**: Permintaan Pengguna, 5 Tangkapan Layar (`media_1793809576403_130df9ca.png` s/d `media_1793810092899_0484f2ba.png`), Aturan `AGENTS.md`.

---

## 1. Analisis Masalah (Visual Clutter & Cognitive Load)

Berdasarkan 5 tangkapan layar yang diunggah oleh pengguna dan instruksi poin 3:
*"hilangkan atau bersihkan teks yang membuat tampilan menjadi rame dan penuh... yang di waitinglist, penempatan perserta, laporan & ekspor blm saya ss jika anda tau teks yang rame dan terlihat membuat tampilan penuh dan terlihat tidak rapi, maka eksekusi sekalian"*

### A. Teks yang Di-screenshot oleh Pengguna
1. **Screenshot 1 (`media_1793809576403_130df9ca.png`)**:
   - **Teks**: `"Tidak ada berkas yang menunggu tindak lanjut saat ini."`
   - **Lokasi**: [`resources/views/admin/dashboard/partials/_queue_table.blade.php:37`](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_queue_table.blade.php#L37)
   - **Masalah**: Tepat di atas teks ini sudah ada judul tebal `<h4>Semua Berkas Sudah Selesai Diverifikasi</h4>` dengan ikon centang hijau. Kalimat kedua bersifat redundan dan membuat kotak status kosong tampak penuh teks.
2. **Screenshot 2 (`media_1793809864986_c94a9bd1.png`)**:
   - **Teks**: `"Pusat validasi berkas pendaftaran calon peserta magang dan monitoring SLA permohonan dinas."`
   - **Lokasi**: [`resources/views/admin/pendaftaran/index.blade.php:494`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php#L494) (`page-title description`)
   - **Masalah**: Subtitle deskripsi di bawah judul utama halaman *"Verifikasi Berkas Pendaftaran Peserta"* terlalu panjang dan formal, memenuhi area header atas halaman.
3. **Screenshot 3 (`media_1793809913634_677c4595.png`)**:
   - **Teks**: `"Manajemen antrean calon peserta magang Sleman"`
   - **Lokasi**: [`resources/views/admin/pendaftaran/index.blade.php:553`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php#L553) (`x-panel subtitle`)
   - **Masalah**: Teks abu-abu kecil di sebelah judul panel *"Daftar Permohonan Magang Masuk"* membuat baris judul kartu tabel terlihat ramai.
4. **Screenshot 4 (`media_1793810051594_14f720f0.png`)**:
   - **Teks**: `"Atur bulan pendaftaran dan kuota magang per bidang."`
   - **Lokasi**: [`resources/views/admin/kuota/index.blade.php:852`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php#L852) (`.kuota-page-subtitle`)
   - **Masalah**: Subtitle di bawah judul *"Bulan & kuota"* tidak memberikan nilai tambah visual dan memperlebar ruang vertikal header.
5. **Screenshot 5 (`media_1793810092899_0484f2ba.png`)**:
   - **Teks**: `"Tentukan jumlah kuota, jurusan, jenjang, dan rentang durasi magang."`
   - **Lokasi**: [`resources/views/admin/kuota/index.blade.php:1016`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php#L1016) (`x-panel subtitle`)
   - **Masalah**: Subtitle pada panel *"Kuota per bidang"* membuat header kartu tabel padat.

### B. Teks Redundan & Ramai Tambahan (Waiting List, Penempatan, Laporan & Ekspor)
1. **Waiting List ([`resources/views/admin/waiting-list/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/waiting-list/index.blade.php))**:
   - Baris 802: `description="Pengelolaan antrean cadangan peserta saat kuota jurusan penuh berdasarkan waktu pengajuan secara transparan, adil, dan objektif."` pada `page-title` — kalimat sangat panjang dan bertele-tele.
   - Baris 853: `subtitle="Penegakan transparansi dan objektivitas penempatan magang"` pada `x-panel` antrean — redundan terhadap judul kartu.
2. **Penempatan Peserta ([`resources/views/admin/penempatan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/penempatan/index.blade.php))**:
   - Baris 5: `@section('page_description', 'Kelola penugasan pembimbing lapangan, pantau peserta aktif, dan kelola administrasi penempatan magang.')` — deskripsi panjang di header halaman atas.
   - Baris 858: `subtitle="Daftar peserta magang aktif dan riwayat penugasan pembimbing"` pada `x-panel`.
   - Baris 859–863: Blok counter `Menampilkan {{ $penempatanList->firstItem() ?? 0 }}–{{ $penempatanList->lastItem() ?? 0 }} dari {{ $penempatanList->total() }}` yang melayang di atas tabel, padahal di kaki tabel (footer paginasi) sudah ada informasi jumlah data yang sama.
3. **Hub Laporan & Ekspor ([`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php))**:
   - Baris 5: `@section('page_description', 'Rekapitulasi berkas pendaftaran, alokasi kuota, penempatan, dan evaluasi peserta magang (AC-17-1, AC-17-2).')` — memuat kode PRD teknis yang membuat header kotor.
   - Baris 197 & 239: Subtitle pada kartu analitik ringkasan (`Alokasi kursi reguler...`, `Komposisi pendaftar...`).
   - Baris 359, 639, 822, 922: Subtitle deskripsi di setiap header panel tab (Tab 1, Tab 2, Tab 3, Tab 4).
4. **Sertifikat ([`resources/views/admin/sertifikat/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/sertifikat/index.blade.php))**:
   - Baris 318: Badge kode PRD internal `<span ...>AC-12 & BR-10</span>`.
   - Baris 321–323: Paragraf deskripsi panjang di header: `<p class="cert-page-subtitle">Penerbitan dan pengarsipan Sertifikat Kelulusan Magang bagi alumni...</p>`.

---

## 2. Tujuan & Strategi Desain (Clean & Minimalist)

1. **Prinsip Less is More (Modern Dashboard UI)**:
   - Menghilangkan kalimat deskriptif yang bersifat "menjelaskan hal yang sudah jelas" (*stating the obvious*). Pengguna admin sudah memahami bahwa tabel pendaftaran digunakan untuk verifikasi, kuota untuk mengatur kapasitas, dan penempatan untuk menugaskan peserta.
2. **Keterbacaan yang Fokus & Lapang (Visual Breathing Room)**:
   - Header halaman dan header kartu tabel (`x-panel`) menjadi bersih, padat, dan elegan tanpa baris teks abu-abu yang memenuhi antarmuka.
3. **Preservasi Aksesibilitas & Tes Otomatis (Accessibility & Test Safe)**:
   - Untuk teks `"Atur bulan pendaftaran dan kuota magang per bidang."` pada halaman Kuota yang diuji dalam `AdminKuotaRevampTest.php`, teks akan diberikan kelas utilitas `.sr-only` (tersembunyi secara visual sehingga layar bersih, namun tetap ada di DOM untuk pembaca layar/aksesibilitas dan tes otomatis tetap hijau 100%).

---

## 3. Rincian Perubahan File

| No | File Target | Elemen yang Dibersihkan | Alasan & Dampak |
| :-: | :--- | :--- | :--- |
| 1 | `resources/views/admin/dashboard/partials/_queue_table.blade.php` | Hapus `<p style="font-size: 0.85rem; margin: 0;">Tidak ada berkas yang menunggu tindak lanjut saat ini.</p>` pada baris 37. | Kotak status kosong menjadi ringkas hanya menampilkan ikon dan judul utama (*Screenshot 1*). |
| 2 | `resources/views/admin/pendaftaran/index.blade.php` | 1. Hapus `description="..."` dari `<x-gentelella.page-title>`.<br>2. Hapus `subtitle="..."` dari `<x-gentelella.x-panel>`. | Header halaman dan header tabel menjadi bersih dan tidak sesak (*Screenshot 2 & 3*). |
| 3 | `resources/views/admin/kuota/index.blade.php` | 1. Tambahkan kelas `sr-only` pada `<p class="kuota-page-subtitle">`.<br>2. Hapus `subtitle="..."` dari `<x-gentelella.x-panel title="Kuota per bidang">`. | Header kuota lapang dan kartu kuota bidang rapi tanpa mengorbankan tes otomatis (*Screenshot 4 & 5*). |
| 4 | `resources/views/admin/waiting-list/index.blade.php` | 1. Hapus `description="..."` dari `<x-gentelella.page-title>`.<br>2. Hapus `subtitle="..."` dari `<x-gentelella.x-panel>`. | Menghilangkan kalimat panjang bertele-tele di halaman antrean cadangan. |
| 5 | `resources/views/admin/penempatan/index.blade.php` | 1. Kosongkan `@section('page_description')`.<br>2. Hapus `subtitle="..."` dari `<x-gentelella.x-panel title="Data Penempatan Peserta">`.<br>3. Hapus counter duplikat di atas tabel (baris 859–863). | Menghilangkan teks ganda di atas dan bawah tabel, header penempatan menjadi ringkas. |
| 6 | `resources/views/admin/laporan/index.blade.php` | 1. Bersihkan `@section('page_description')` dari kode teknis.<br>2. Hapus `subtitle` dari 2 kartu analitik atas.<br>3. Hapus `subtitle` dari 4 panel tab tabel (Pendaftaran, Penempatan, Kuota, Nilai). | Seluruh tab laporan terlihat konsisten, minimalis, dan profesional. |
| 7 | `resources/views/admin/sertifikat/index.blade.php` | 1. Hapus badge teknis internal `AC-12 & BR-10`.<br>2. Hapus paragraf panjang `<p class="cert-page-subtitle">`. | Header sertifikat menjadi bersih sejajar dengan tombol aksi. |

---

## 4. Langkah Implementasi Bertahap

### Langkah 1: Eksekusi Pembersihan Teks Screenshot User (Dashboard, Pendaftaran, Kuota)
- [x] Edit `resources/views/admin/dashboard/partials/_queue_table.blade.php`: Hapus baris `<p>` deskripsi kosong.
- [x] Edit `resources/views/admin/pendaftaran/index.blade.php`: Hapus attribute `description` pada `page-title` dan `subtitle` pada `x-panel`.
- [x] Edit `resources/views/admin/kuota/index.blade.php`: Pasang `class="kuota-page-subtitle sr-only"` dan hapus `subtitle` pada `x-panel`.

### Langkah 2: Eksekusi Pembersihan Halaman Waiting List, Penempatan, Laporan, & Sertifikat
- [x] Edit `resources/views/admin/waiting-list/index.blade.php`: Hapus `description` dan `subtitle`.
- [x] Edit `resources/views/admin/penempatan/index.blade.php`: Kosongkan `page_description`, hapus `subtitle`, dan hapus counter baris 859–863.
- [x] Edit `resources/views/admin/laporan/index.blade.php`: Bersihkan `page_description`, hapus subtitle pada kartu analitik dan seluruh x-panel tab.
- [x] Edit `resources/views/admin/sertifikat/index.blade.php`: Hapus badge kode teknis dan subtitle panjang.

### Langkah 3: Kompilasi & Quality Gates
- [x] Jalankan kompilasi aset: `npm run build`.
- [x] Jalankan automated feature tests:
  ```bash
  php artisan test tests/Feature/AdminKuotaRevampTest.php tests/Feature/AdminTablePaginationStandardTest.php tests/Feature/AdminLaporanDanArsipTest.php tests/Feature/AdminChildPagesVisualTest.php
  ```
- [x] Jalankan pengecekan code formatting Pint:
  ```bash
  ./vendor/bin/pint --test
  ```

---

## 5. Kriteria Keberhasilan (Definition of Done)

- [x] Seluruh teks redundan dari 5 screenshot tidak lagi terlihat di layar.
- [x] Subtitle panjang di halaman Waiting List, Penempatan, Laporan, dan Sertifikat telah dibersihkan.
- [x] Counter duplikat di atas tabel Penempatan terhapus.
- [x] Tampilan antarmuka administrasi tampak lebih luas, tenang, modern, dan tidak sesak (*visual breathing room*).
- [x] Semua tes otomatis PHPUnit tetap hijau (100% PASS), termasuk pengujian header kuota.
- [x] Kode format rapi sesuai standar Laravel Pint.

