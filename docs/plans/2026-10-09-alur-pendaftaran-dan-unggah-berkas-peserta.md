# Plan: Alur Pendaftaran & Unggah Berkas Peserta (Jalur A)

- **Tanggal**: 2026-10-09
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning)
- **Dokumen Acuan**: 
  - [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Bab 4, Bab 8.2, Bab 8.4, Bab 8.5)
  - [`07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Bab 2, Bab 3, Bab 4)
  - [`layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php)

---

## 1. Tujuan
Membangun antarmuka pendaftaran magang sisi Peserta secara menyeluruh dan terintegrasi dari mulai pemilihan periode bulan magang & kuota jurusan, halaman detail pendaftaran ([`peserta.pendaftaran.detail`](file:///g:/MAGKOM/resources/views/peserta/pendaftaran/detail.blade.php)), visualisasi tahapan seleksi (*status stepper*), formulir unggah berkas persyaratan sesuai jenjang dengan fitur *drag-and-drop* dan validasi ukuran (maks 2 MB), hingga tombol aksi "Ajukan Pendaftaran" dan tautan unduh instan dokumen resmi PDF "Surat Keterangan Diterima Magang" saat peserta dinyatakan lolos.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi**:
  1. Batas maksimal ukuran berkas diambil secara dinamis dari `Pengaturan::int('berkas_maks_mb', 2)` dengan nilai default 2 MB sesuai PRD Bab 8.4.
  2. Format berkas yang didukung adalah PDF, JPG, JPEG, dan PNG sesuai validasi backend `UnggahDokumenRequest`.
  3. Dokumen persyaratan ditentukan secara otomatis berdasarkan jenjang pada profil peserta (`smk`, `d3`, atau `s1`). Jika profil peserta belum lengkap, sistem memberikan peringatan informatif dengan tombol pintas ke halaman profil.
  4. Setiap peserta hanya diperbolehkan memiliki 1 pendaftaran aktif pada satu waktu sesuai aturan bisnis BR-17.
- **Pertanyaan Terbuka**: Tidak ada ambiguitas kritis. Seluruh aturan bisnis dan kontrak data telah didefinisikan secara presisi pada PRD dan skema database eksisting.

---

## 3. Pendekatan Desain & Arsitektur

### Perbandingan Opsi Desain:

| Aspek | Opsi 1: Halaman Terpisah Per Berkas (Wizard Form Tradisional) | Opsi 2: Dashboard Detail Terpadu dengan Stepper & Grid Upload (Terpilih) |
| :--- | :--- | :--- |
| **Pengalaman Pengguna (UX)** | Peserta harus berpindah-pindah halaman untuk setiap berkas; membingungkan jika perlu revisi 1 berkas. | Satu layar komprehensif: ringkasan status di atas, stepper progres di tengah, dan kartu berkas interaktif di bawah. |
| **Kesesuaian Design System** | Sulit menyelaraskan status badge dan feedback riwayat perbaikan. | Sepenuhnya selaras dengan [07-DESIGN-SYSTEM-MAGKOM.md](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) Bab 3.7 (Pola Khusus MagKom) & Bab 4 (Pemetaan Status). |
| **Efisiensi Interaksi** | Membutuhkan banyak round-trip navigasi HTTP. | Peserta dapat mengunggah berkas satu per satu secara modular, langsung melihat status berkas "Siap/Terunggah", lalu menekan "Ajukan Pendaftaran". |

- **Opsi Terpilih**: **Opsi 2 (Dashboard Detail Terpadu)**.
- **Alasan Pemilihan**: Memudahkan peserta memantau kelengkapan berkas secara visual, memahami posisi tahapan seleksi secara transparan (*self-service* tanpa notifikasi blast email sesuai BR-14), serta mempermudah pengunggahan ulang berkas jika berstatus `perlu_perbaikan`.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`app/Http/Controllers/Peserta/PendaftaranController.php`](file:///g:/MAGKOM/app/Http/Controllers/Peserta/PendaftaranController.php) | Modify | Pada method `index()`, sediakan data `$bulanAktifList` dan kuota yang tersedia agar peserta baru dapat langsung membuat draf pendaftaran dari dashboard. Eager-load relasi `penempatan` dan `kuota.jurusan` pada `detail()`. |
| [`resources/views/peserta/pendaftaran/index.blade.php`](file:///g:/MAGKOM/resources/views/peserta/pendaftaran/index.blade.php) | Modify | Sediakan modal / kartu formulir "Daftar Magang Baru" untuk memilih Periode Bulan & Jurusan Kuota yang sedang buka jika peserta belum memiliki pendaftaran aktif. |
| [`resources/views/peserta/pendaftaran/detail.blade.php`](file:///g:/MAGKOM/resources/views/peserta/pendaftaran/detail.blade.php) | Create | Halaman detail pengajuan pendaftaran baru yang memuat: Header metadata pendaftaran, Stepper 4 langkah alur status, Alert catatan perbaikan/revisi jika ada, Grid kartu unggah berkas (drag-and-drop, validasi ukuran 2MB, preview file terunggah, link unduh privat), Tombol konfirmasi "Ajukan Pendaftaran", dan Kartu Banner Unduh Surat Keterangan Diterima PDF resmi jika status `diterima`. |
| [`tests/Feature/PesertaPendaftaranFlowTest.php`](file:///g:/MAGKOM/tests/Feature/PesertaPendaftaranFlowTest.php) | Create | Pengujian fitur otomatis untuk alur pendaftaran peserta: render dashboard pendaftaran, pembuatan draft, render halaman detail berkas, unggah berkas simulasi, validasi ukuran berkas, pengajuan pendaftaran, dan tombol unduh surat diterima. |

---

## 5. Langkah Implementasi

### Fase 1: Penyesuaian Controller & Penyediaan Data
- [x] **Langkah 1.1**: Update `PesertaPendaftaranController@index` untuk memuat data periode bulan magang berstatus `Tersedia` beserta relasi kuotanya agar peserta yang belum mendaftar dapat langsung memilih periode dari dashboard.
- [x] **Langkah 1.2**: Pastikan `PesertaPendaftaranController@detail` memuat relasi `penempatan`, `bulanMagang`, `kuota.jurusan`, dan `dokumen.jenisDokumen` secara optimal.

### Fase 2: Form Pembuatan Pengajuan di `pendaftaran/index.blade.php`
- [x] **Langkah 2.1**: Tambahkan tombol dan modal "Buat Pengajuan Pendaftaran" pada [peserta/pendaftaran/index.blade.php](file:///g:/MAGKOM/resources/views/peserta/pendaftaran/index.blade.php) yang memuat pilihan Periode Bulan Magang, Pilihan Jurusan (dari kuota aktif), dan Durasi Magang (1–6 bulan).
- [x] **Langkah 2.2**: Pasang pesan validasi error dan peringatan BR-17 (jika sudah ada pendaftaran aktif).

### Fase 3: Pembuatan View Terpadu `pendaftaran/detail.blade.php`
- [x] **Langkah 3.1**: Buat struktur dasar yang meng-extend `layouts.dashboard` dengan judul halaman dinamis sesuai nomor pendaftaran.
- [x] **Langkah 3.2**: Buat komponen **Status Stepper** responsif 4 langkah:
  1. *Pengisian Berkas* (`berkas_belum_lengkap`)
  2. *Verifikasi Admin* (`diajukan` / `waiting_list`)
  3. *Tinjauan / Perbaikan* (`perlu_perbaikan`)
  4. *Keputusan Akhir* (`diterima` / `ditolak` / `dibatalkan`)
- [x] **Langkah 3.3**: Buat komponen **Alert Status Khusus**:
  - Banner peringatan kuning jika status `perlu_perbaikan` disertai catatan verifikator admin.
  - Banner info biru jika status `waiting_list` (menjelaskan posisi antrean tunggu karena kuota penuh).
  - Banner sukses hijau jika status `diterima` disertai informasi penempatan dan tombol unduh surat diterima.
- [x] **Langkah 3.4**: Buat komponen **Kartu Unggah Berkas Persyaratan**:
  - Iterasi daftar `jenisDokumenList` sesuai jenjang profil peserta.
  - Tampilkan penanda wajib/opsional dan batas ukuran file maks 2 MB.
  - Sediakan zona *drag-and-drop* file dengan input file hidden dan tombol "Pilih Berkas".
  - Jika berkas sudah terunggah: tampilkan nama file asli, ukuran file, tanggal unggah, tombol "Unduh Berkas" (signed URL 5 menit), dan form unggah ulang/ganti berkas (jika status masih `berkas_belum_lengkap` atau `perlu_perbaikan`).
  - Berikan skrip JavaScript ringan tanpa dependensi eksternal untuk validasi ukuran file di sisi browser sebelum submit (mencegah payload berlebih ke server).
- [x] **Langkah 3.5**: Buat bilah aksi bawah (Bottom Action Bar):
  - Tombol **"Ajukan Pendaftaran"** (`POST peserta.pendaftaran.ajukan`).
  - Tombol otomatis terkunci/disabled jika masih ada berkas wajib yang belum diunggah, disertai teks bantuan penjelas.
  - Dialog konfirmasi interaktif sebelum pengajuan dikirim.
- [x] **Langkah 3.6**: Buat tombol unduh dokumen resmi **Surat Keterangan Diterima PDF** (`GET peserta.penempatan.surat-diterima`) yang muncul saat status pendaftaran adalah `diterima` dan telah memiliki penempatan.

### Fase 4: Pengujian & Validasi
- [x] **Langkah 4.1**: Tulis dan jalankan feature test [tests/Feature/PesertaPendaftaranFlowTest.php](file:///g:/MAGKOM/tests/Feature/PesertaPendaftaranFlowTest.php) untuk memverifikasi seluruh skenario.
- [x] **Langkah 4.2**: Jalankan pengujian visual di browser lokal untuk memastikan tampilan presisi di layar desktop (1440px) dan mobile (390px).

---

## 6. Strategi Testing
- **Automated Feature Test**:
  1. Akses halaman index pendaftaran oleh peserta.
  2. Pembuatan draf pendaftaran baru dengan kuota valid.
  3. Akses halaman detail pendaftaran dan verifikasi daftar berkas sesuai jenjang SMK vs D3/S1.
  4. Unggah berkas wajib ke disk privat dan pastikan tercatat di database & audit log.
  5. Pengajuan pendaftaran dengan berkas lengkap -> status berubah menjadi `diajukan` atau `waiting_list`.
  6. Percobaan pengajuan dengan berkas belum lengkap -> ditolak validasi.
  7. Akses unduh surat keterangan diterima saat pendaftaran berstatus `diterima`.
- **UI & Manual Verification**:
  - Uji drag-and-drop file di browser.
  - Uji validasi file > 2 MB (muncul alert peringatan instan).
  - Uji tampilan badge status 5 varian warna Design System.

---

## 7. Risiko & Mitigasi
- **Keamanan Penyimpanan Berkas**:
  - *Risiko*: Berkas tersimpan di folder publik dan dapat diakses sembarang orang.
  - *Mitigasi*: Dokumen tetap disimpan di disk `private` melalui `DocumentStorageService`, dan diunduh hanya lewat signed route berumur 5 menit dengan policy otorisasi ketat.
- **Validasi MIME Type & Magic Bytes**:
  - *Risiko*: File executable disamarkan dengan ekstensi `.pdf`.
  - *Mitigasi*: Validasi backend `UnggahDokumenRequest` memeriksa ekstensi dan MIME type valid (`pdf, jpg, jpeg, png`).
- **Aksesibilitas (WCAG 2.1 AA)**:
  - *Risiko*: Kontras teks badge atau tombol tidak terbaca.
  - *Mitigasi*: Menggunakan token warna resmi MagKom yang telah lolos uji kontras rasio minimal 4.5:1 (misal `Neutral 800` pada latar putih, `Green 700` pada `Green 100`).

---

## 8. Kriteria Selesai (Definition of Done)
- [x] View [`resources/views/peserta/pendaftaran/detail.blade.php`](file:///g:/MAGKOM/resources/views/peserta/pendaftaran/detail.blade.php) berhasil dibuat dan meng-extend layout master dashboard.
- [x] Stepper alur status seleksi tampil presisi dengan status pendaftaran terkini.
- [x] Form berkas menyesuaikan otomatis sesuai jenjang (SMK vs D3/S1).
- [x] Drag-and-drop file dan validasi ukuran maks 2 MB berjalan mulus.
- [x] Tombol "Ajukan Pendaftaran" hanya aktif ketika seluruh berkas wajib lengkap.
- [x] Tombol "Unduh Surat Keterangan Diterima" muncul dan berfungsi saat pendaftaran diterima.
- [x] Seluruh pengujian otomatis di `PesertaPendaftaranFlowTest.php` lulus 100% (`PASS`).

---

## 9. Catatan Perubahan & Saran Lanjutan
- **Perubahan selama pengerjaan**: *(Akan dicatat saat eksekusi)*
- **Saran Lanjutan (Out of Scope)**:
  - Penambahan preview thumbnail berkas gambar langsung di browser sebelum diunggah (dapat ditambahkan pada iterasi berikutnya).
