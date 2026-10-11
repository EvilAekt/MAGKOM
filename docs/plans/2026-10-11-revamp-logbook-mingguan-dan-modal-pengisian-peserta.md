# Plan: Revamp Logbook Mingguan, Modal Interaktif, dan Peningkatan Antarmuka Ramah Pengguna (User-Friendly)

| Metadata | Keterangan |
|---|---|
| **Tanggal Pembuatan** | 11 Oktober 2026 |
| **Status Dokumen** | Selesai Diimplementasikan (Completed) |
| **Target Area** | Portal Peserta: Modul Logbook (`peserta/logbook/index.blade.php`), Controller (`Peserta\LogbookController`), Request (`LogbookRequest`), Model (`Logbook`, `Penempatan`), Penyelarasan Dashboard & Pembimbing |
| **Kebutuhan Pengguna** | 1. Konversi paradigma dari logbook harian ke **Logbook Mingguan**.<br>2. Halaman utama **tidak langsung menampilkan form pengisian**, melainkan menampilkan riwayat mingguan + tombol aksi klik yang memicu form pengisian (Modal Interaktif).<br>3. Desain dan alur dibuat **sangat ramah pengguna (User-Friendly)** dengan indikator visual, panduan praktis, dan interaksi yang intuitif. |

---

## 1. Tujuan
1. **Mengubah Format Logbook menjadi Logbook Mingguan (Weekly Logbook)**:
   - Mengelompokkan pelaporan aktivitas per minggu magang (Minggu ke-1, Minggu ke-2, dst.) yang dihitung dari tanggal mulai penempatan aktif peserta.
   - Menyajikan informasi rentang tanggal mingguan yang jelas dan akurat (misal: *Minggu ke-1: 06 Okt 2026 – 12 Okt 2026*).
   - Menyelaraskan seluruh label dan istilah di portal peserta dan pembimbing dari "Harian" ke "Mingguan".
2. **Memperbaiki UX Alur Pengisian Logbook (Form on Demand via Modal)**:
   - Menghilangkan panel form statis yang sebelumnya memakan sepertiga sisi kiri layar.
   - Halaman utama kini bersih, lapang, dan berfokus pada ringkasan progres dan daftar jurnal mingguan.
   - Tombol utama **`+ Isi Logbook Mingguan`** membuka formulir dalam **Modal Dialog Modern** dengan animasi halus (*smooth backdrop-blur & scale transition*).
3. **Peningkatan Menyeluruh Aspek Ramah Pengguna (User-Friendly Features)**:
   - **Progress Tracker & Mini Stepper Mingguan**: Peserta dapat melihat sekilas capaian pengisian logbook (misal: "3 dari 12 Minggu Terisi" dengan bar progres persentase dan pil status per minggu).
   - **Smart Reminder Banner**: Jika minggu berjalan saat ini belum diisi, muncul kotak pengingat ramah di bagian atas dengan tombol cepat `[+ Isi Minggu Ini]` yang otomatis memilih minggu tersebut.
   - **Formulir Cerdas & Komunikatif di dalam Modal**:
     - *Auto-Selection*: Dropdown minggu otomatis memilih minggu magang aktif yang belum diisi.
     - *Live Character Counter*: Indikator penghitung karakter real-time (misal: "Minimal 10 karakter | 145 karakter").
     - *Panduan Menulis Praktis*: Hint/bantuan yang memberi contoh apa saja yang perlu dicatat (tugas utama, capaian, kendala, solusi).
     - *Dropzone Berkas Interaktif*: Menampilkan nama berkas PDF dan ukuran yang dipilih dengan opsi hapus/ganti berkas sebelum dikirim.
     - *Anti Double-Submit*: State tombol berubah menjadi disabled dengan teks "Menyimpan..." saat diklik untuk mencegah duplikasi request.
   - **Navigasi & Aksesibilitas**: Modal dapat ditutup via tombol Batal, ikon silang (✕), klik di luar backdrop, atau menekan tombol keyboard `Esc`.
   - **Status Card Semantik**: Kartu mingguan menampilkan status warna yang ramah (Hijau: Disetujui, Biru: Terkirim/Menunggu Reviu, Kuning/Oranye: Perlu Revisi) lengkap dengan kotak catatan pembimbing yang kontras dan jelas jika ada saran revisi.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis:
- **A-1 (Skema Database)**: Tabel `logbook` saat ini memiliki kolom `tanggal` (tipe `date`, unique dengan `penempatan_id`), `kegiatan`, `file_laporan`, `link_laporan`, `status`, dan `catatan_pembimbing`.
  - Kita menambahkan kolom `minggu_ke` (unsignedInteger, nullable) pada tabel `logbook` via migrasi baru `2026_10_11_000001_add_minggu_ke_to_logbook_table.php`.
  - Kolom `tanggal` tetap diisi dengan tanggal laporan akhir minggu (misal hari Jumat/Minggu atau hari saat peserta mengisi) sehingga seluruh relasi dan query lama tetap 100% kompatibel (*zero breaking change*).
- **A-2 (Perhitungan Rentang Tanggal Mingguan)**: Model `Penempatan` menyediakan accessor/helper yang menghitung rentang tanggal tiap minggu (7 hari per siklus minggu) dari `tgl_mulai` hingga `tgl_selesai`.
- **A-3 (Frontend Styling)**: Menggunakan Vanilla CSS murni yang menyatu dengan token MagKom (`--blue-600`, `--neutral-*`, skala tipografi rem resmi) tanpa dependensi eksternal, ringan, dan cepat di-render di semua browser.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan pemblokir. Semua kebutuhan fungsional dan kenyamanan pengguna telah dirumuskan secara rinci.

---

## 3. Pendekatan Desain & UX

### 3.1 Struktur Data & Penomoran Minggu
- **Opsi Terpilih: Migrasi penambahan kolom `minggu_ke` pada tabel `logbook`**.
  - Menyimpan nomor minggu secara eksplisit di database, menjamin pencarian/filter cepat, dan memudahkan pembimbing serta admin memantau laporan per minggu tertentu.

### 3.2 Alur Pengisian Logbook Ramah Pengguna (User-Friendly Flow)

```
[Halaman Logbook Mingguan]
       │
       ├───> Banner Penempatan & Ringkasan Pembimbing
       │
       ├───> Progress Tracker Mingguan (X dari N Minggu Terisi - Progress Bar)
       │
       ├───> Smart Reminder (Jika minggu aktif belum diisi -> Tombol "+ Isi Minggu Ini")
       │
       ├───> Tombol Utama: "+ Isi Logbook Mingguan"
       │         │
       │         └───> Buka MODAL PENGISIAN MINGGUAN:
       │                   • Pilihan Minggu (Otomatis terisi minggu berjalan)
       │                   • Rentang Tanggal Otomatis (Senin - Minggu)
       │                   • Panduan Singkat Penulisan Laporan
       │                   • Live Character Counter (Min. 10 karakter)
       │                   • Upload PDF (Preview nama berkas)
       │                   • Link Eksternal (Google Drive / GitHub)
       │                   • Tombol Batal & Simpan Jurnal
       │
       └───> Daftar Kartu Logbook Mingguan (Cards Grid):
             • Badge Minggu ke-X & Rentang Tanggal
             • Status Reviu (Terkirim / Disetujui / Perlu Revisi)
             • Isi Uraian Kegiatan
             • Lampiran PDF & Link Eksternal
             • Catatan Feedback Pembimbing (jika ada)
             • Tombol "Edit" -> Buka MODAL EDIT MINGGUAN
```

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| `database/migrations/2026_10_11_000001_add_minggu_ke_to_logbook_table.php` | **Buat** | Menambahkan kolom `minggu_ke` (unsignedInteger nullable) pada tabel `logbook` dengan index `['penempatan_id', 'minggu_ke']`. |
| `app/Models/Logbook.php` | **Ubah** | Mendaftarkan `minggu_ke` pada `$fillable`, menambahkan helper/accessor label periode minggu format ramah bahasa Indonesia. |
| `app/Models/Penempatan.php` | **Ubah** | Menambahkan helper `getDaftarMingguMagang()` dan `getMingguAktif()` untuk menghitung total minggu magang, rentang tanggal per minggu, dan status pengisiannya. |
| `app/Http/Requests/LogbookRequest.php` | **Ubah** | Menyesuaikan validasi `minggu_ke` dan tanggal laporan mingguan dengan pesan error bahasa Indonesia yang komunikatif. |
| `app/Http/Controllers/Peserta/LogbookController.php` | **Ubah** | Menghitung data minggu magang, statistik capaian progres pengisian, validasi keunikan minggu, dan penyimpanan data via modal. |
| `resources/views/peserta/logbook/index.blade.php` | **Ubah** | Merombak total antarmuka: menghilangkan panel form statis di sisi kiri, menambahkan Weekly Progress Tracker & Smart Reminder, kartu logbook mingguan, serta Modal Tambah dan Modal Edit yang interaktif dan ramah pengguna. |
| `resources/views/peserta/dashboard.blade.php` | **Ubah** | Menyelaraskan teks widget dan kartu riwayat logbook dari "Logbook Harian" menjadi "Logbook Mingguan". |
| `resources/views/layouts/peserta.blade.php` | **Ubah** | Menyelaraskan label navigasi menu sidebar dari "Logbook Harian" menjadi "Logbook Mingguan". |
| `tests/Feature/PesertaClassroomTugasLogbookTest.php` | **Ubah/Perluas** | Memperbarui feature test untuk menguji alur mingguan, validasi keunikan minggu, dan fungsionalitas modal. |

---

## 5. Langkah Implementasi Terperinci

### Langkah 1: Migrasi Database & Logika Model Eloquent
- [x] Buat file migrasi `2026_10_11_000001_add_minggu_ke_to_logbook_table.php`.
- [x] Jalankan migrasi menggunakan `php artisan migrate`.
- [x] Perbarui [app/Models/Logbook.php](file:///d:/MAGKOM/MAGKOM/app/Models/Logbook.php) dengan `$fillable` baru dan accessor rentang minggu.
- [x] Tambahkan helper pada [app/Models/Penempatan.php](file:///d:/MAGKOM/MAGKOM/app/Models/Penempatan.php) untuk mengembalikan kalkulasi minggu magang (nomor minggu, tanggal awal, tanggal akhir, status pengisian, dan identifikasi minggu aktif saat ini).

### Langkah 2: Controller & Validasi Form Request
- [x] Perbarui [app/Http/Requests/LogbookRequest.php](file:///d:/MAGKOM/MAGKOM/app/Http/Requests/LogbookRequest.php):
  - Validasi `minggu_ke` (wajib, integer, min: 1).
  - Validasi `kegiatan` (min: 10 karakter) dengan pesan error yang jelas.
  - Validasi `file_laporan` (PDF maks 2MB) dan `link_laporan` (URL HTTPS).
- [x] Perbarui [app/Http/Controllers/Peserta/LogbookController.php](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Peserta/LogbookController.php):
  - Menghitung progres pengisian (misal: total minggu terisi vs total minggu penempatan).
  - Menyiapkan daftar opsi minggu magang untuk dropdown modal.
  - Memastikan validasi pencegahan entri ganda untuk minggu yang sama pada satu penempatan.
  - Mendukung respons JSON maupun redirect Blade dengan flash message sukses.

### Langkah 3: Revamp Antarmuka Ramah Pengguna di `peserta/logbook/index.blade.php`
- [x] **Hapus Layout 2 Kolom Kaku**: Hilangkan panel sticky form di sisi kiri.
- [x] **Header & Banner Penempatan**: Tampilkan banner resmi berlatar grafis MagKom dengan informasi pembimbing, bidang, dan minggu aktif saat ini.
- [x] **Weekly Progress Tracker Card**:
  - Tampilkan baris progres visual: persentase logbook terisi, jumlah minggu disetujui, dan jumlah minggu tersisa.
  - Stepper horizontal/chip pill minggu yang bisa diklik untuk navigasi cepat.
- [x] **Smart Reminder Box**:
  - Jika minggu berjalan belum diisi, tampilkan notifikasi ramah berwarna biru-muda dengan tombol aksi cepat `[+ Isi Logbook Minggu Ini]`.
- [x] **Action Bar**:
  - Tombol utama yang mencolok: `+ Isi Logbook Mingguan` di sisi kanan atas daftar kartu.
- [x] **Daftar Kartu Logbook Mingguan (Weekly Cards)**:
  - Format kartu luas dan bersih dengan tipografi yang nyaman dibaca.
  - Chip status dengan warna kontras yang ramah mata.
  - Box feedback pembimbing dengan aksen Amber/Kuning jika terdapat catatan reviu atau instruksi revisi.
  - Tombol `Edit` untuk membuka modal ubah data.
- [x] **Modal Dialog Tambah Logbook Mingguan (`#modalTambahLogbook`)**:
  - Backdrop blur dengan animasi muncul yang halus.
  - Auto-select ke minggu berjalan yang belum diisi.
  - Rentang tanggal terisi/terpilih otomatis sesuai minggu yang dipilih.
  - Kotak petunjuk penulisan (*Tips Pengisian Jurnal Mingguan*).
  - Live character count untuk kolom deskripsi kegiatan.
  - Dropzone berkas PDF interaktif yang menampilkan nama file terpilih.
  - Tombol simpan dengan state loading anti-double submit.
  - Tombol tutup modal: tombol 'Batal', tombol silang '✕', klik di luar area, dan tombol `Esc`.
- [x] **Modal Dialog Edit Logbook Mingguan (`#modalEditLogbook`)**:
  - Mengisi form secara dinamis dengan data logbook yang sudah ada saat tombol Edit diklik.

### Langkah 4: Penyelarasan Dashboard & Modul Pembimbing
- [x] Sesuaikan widget ringkasan logbook pada [resources/views/peserta/dashboard.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard.blade.php) menjadi format mingguan.
- [x] Sesuaikan label menu navigasi sidebar di [resources/views/layouts/peserta.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/peserta.blade.php) menjadi "Logbook Mingguan".

### Langkah 5: Pengujian Kualitas & TDD
- [x] Perbarui pengujian pada `PesertaClassroomTugasLogbookTest` untuk memverifikasi fungsionalitas logbook mingguan dan modal.
- [x] Jalankan test suite dan verifikasi seluruh pengujian lolos 100% (17 tests passed, 99 assertions).

---

## 6. Kriteria Ramah Pengguna (User-Friendly Checklist)

- [x] **Kejelasan Informasi**: Peserta tidak perlu menebak minggu ke berapa saat ini karena sistem langsung menampilkannya secara otomatis.
- [x] **Pengurangan Kepadatan Visual**: Halaman utama tidak lagi sesak dengan form kosong yang menyita layar; form hanya muncul saat dibutuhkan (*on demand*).
- [x] **Minimalkan Kesalahan Input**: Sistem otomatis mengunci nomor minggu yang sudah pernah dibuat agar tidak terjadi duplikasi entri minggu.
- [x] **Umpan Balik Cepat**: Terdapat live character counter sehingga peserta tahu apakah panjang tulisan sudah memenuhi batas minimal 10 karakter.
- [x] **Navigasi Fleksibel**: Modal mudah dibuka dan ditutup dengan berbagai cara (tombol, klik backdrop, tombol `Esc`).

---

## 7. Kriteria Selesai (Definition of Done)

- [x] Kolom `minggu_ke` terpasang di database dan terdaftar di model `Logbook`.
- [x] Halaman `/peserta/logbook` bersih dari form statis di sisi kiri.
- [x] Terdapat Progress Tracker Mingguan dan Smart Reminder yang informatif.
- [x] Tombol **`+ Isi Logbook Mingguan`** membuka Modal Pengisian Mingguan dengan fitur ramah pengguna lengkap.
- [x] Modal Edit dapat memperbarui jurnal mingguan yang sudah tersimpan.
- [x] Teks dan label di dashboard dan modul pembimbing tersinkronisasi ke format mingguan.
- [x] Seluruh unit & feature tests lulus 100% tanpa error atau regresi.
