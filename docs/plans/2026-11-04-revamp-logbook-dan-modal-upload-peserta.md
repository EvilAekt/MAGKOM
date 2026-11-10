# Plan: Revamp Halaman Logbook Peserta & Redesain Modal Isi Logbook (Pola Tugas Classroom)

| Metadata | Keterangan |
|---|---|
| **Tanggal Pembuatan** | 4 November 2026 |
| **Status Dokumen** | Menunggu Persetujuan (Pending Approval) |
| **Target Area** | Portal Peserta: Modul Logbook & Jurnal Mingguan (`resources/views/peserta/logbook/index.blade.php`, `resources/css/pages/peserta/logbook-index.css`, `resources/js/pages/peserta/logbook-index.js`) |
| **Referensi Desain** | Sleman MagKom Modern Cards & Google Classroom Attachment Paradigm (Lampiran Gambar 1, 2, 3 Pengguna) |

---

## 1. Tujuan
Merekstrukturisasi dan membenahi modul **Logbook & Jurnal Mingguan Peserta** (`peserta/logbook/index.blade.php`) agar lebih rapi, proporsional, dan nyaman digunakan sesuai 4 poin arahan pengguna:
1. **Pembersihan Tombol Duplikat**: Menghapus tombol `+ Isi Logbook` yang berada di kanan atas header halaman (sebelah judul halaman), menyisakan tombol aksi kontekstual yang jelas di bagian header Riwayat Jurnal Mingguan (`+ Isi Logbook Mingguan`).
2. **Efek Hover Kartu Riwayat Logbook (Sleman SuperAdmin Parity)**: Memberikan efek hover elevasi, border highlight Sleman Royal Blue (`#01347C`), transisi halus, dan styling tombol *Edit* yang elegan pada kartu logbook (`.weekly-card`), setara dengan efek kartu penugasan (`.assignment-card`).
3. **Penyederhanaan Tampilan Riwayat (Default Collapsed)**: Menghapus tombol kontrol "Buka Semua" dan "Tutup Semua", serta menetapkan seluruh kartu logbook dalam status tertutup (*collapsed*) secara default sehingga daftar riwayat tampil rapi dan ringkas.
4. **Redesain Modal "Isi Logbook Mingguan" (Compact & Attachment ala Pengumpulan Tugas)**:
   - Menghilangkan kesan modal yang "terlalu memanjang ke bawah dan kebanyakan teks".
   - Menghapus box panduan panjang (*tips-box*) di bagian bawah modal.
   - Mengatur input `Pilih Minggu Magang` dan `Tanggal Pelaporan` menjadi sejajar 2 kolom berdampingan untuk menghemat ruang vertikal.
   - **Modernisasi Lampiran (Link & File ala Pengumpulan Tugas)**: Mengganti tumpukan input mentah (`<input type="file">` dan `<input type="url">`) dengan pemilih lampiran bertab/segmented yang bersih:
     - 📎 **Upload Berkas (PDF)**: Area upload yang rapi dengan preview nama file dan tombol hapus.
     - 🔗 **Tautan Link**: Input URL terintegrasi dengan ikon tautan.
   - Modal menjadi proporsional, tidak meluap ke bawah layar (*no awkward vertical scrolling*).
5. **Pembersihan Arsitektur Sesuai Aturan `AGENTS.md`**:
   - Menghapus lebih dari 900 baris inline `<style>` dan 200 baris inline `<script>` dari `peserta/logbook/index.blade.php`.
   - Mengekstrak stylesheet ke `resources/css/pages/peserta/logbook-index.css` dan modul interaksi ke `resources/js/pages/peserta/logbook-index.js`, lalu dikompilasi resmi via Vite.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis:
- **A-1 (Pemisahan Asset Sesuai AGENTS.md)**: File Blade `peserta/logbook/index.blade.php` harus menjadi murni lapisan markup semantik tanpa blok `<style>` dan `<script>` inline. Seluruh CSS diletakkan di `resources/css/pages/peserta/logbook-index.css` dan JavaScript di `resources/js/pages/peserta/logbook-index.js`.
- **A-2 (Kompatibilitas Form & Validasi Backend)**: Form modal tambah dan edit logbook tetap mengirimkan field yang sesuai dengan kontrak controller Laravel (`minggu_ke`, `tanggal`, `kegiatan`, `file_laporan`, `link_laporan`). Tidak ada perubahan pada skema database atau backend service.
- **A-3 (Zero Regression Test)**: Pengujian existing di `PesertaClassroomTugasLogbookTest.php` dan `PesertaFullPortalFeatureTest.php` menguji string `'Isi Logbook Mingguan'`, `'Riwayat Jurnal Mingguan'`, dan `'Progres Pelaporan Logbook Mingguan'`. Seluruh teks ini tetap dipertahankan pada header riwayat dan judul modal, sehingga seluruh tes otomatis tetap 100% hijau.
- **A-4 (Modal Tambah & Modal Edit)**: Pola attachment baru dan layout ringkas 2 kolom akan diterapkan baik pada modal tambah (`#modalTambahLogbook`) maupun modal edit (`#modalEditLogbook`) demi konsistensi UX.

### Pertanyaan Terbuka untuk Pengguna:
- Tidak ada pertanyaan kritis yang menghambat arsitektur. Rencana kerja telah merangkum seluruh poin pengguna secara presisi.

---

## 3. Evaluasi Pendekatan Teknis

### Evaluasi Antarmuka Lampiran (Link & File) dalam Modal Logbook:
- **Opsi 1: Segmented Attachment Tabs (Direkomendasikan)**
  - Menyediakan tab kecil di dalam modal: `[ 📎 Berkas PDF ]` dan `[ 🔗 Tautan Link ]`.
  - Peserta dapat memilih tab yang relevan untuk melampirkan berkas bukti atau memasukkan link repositori/drive.
  - *Kelebihan*: Sangat hemat ruang vertikal (modal tidak lagi memanjang ke bawah), tampilan bersih mirip dengan panel pengumpulan tugas, dan input terfokus.
  - *Rekomendasi*: **Opsi 1 (Sangat Direkomendasikan)**.

- **Opsi 2: Compact Dual Field Stack**
  - Menampilkan kedua input (File dan Link) secara vertikal namun dengan styling mini input group tanpa box panduan.
  - *Kekurangan*: Masih memakan ruang tinggi form vertikal dan terasa agak penuh di layar laptop/tablet.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/logbook/index.blade.php` | Modify | 1. Hapus tombol duplikat `+ Isi Logbook` di kanan atas header halaman.<br>2. Hapus tombol "Buka Semua" dan "Tutup Semua".<br>3. Set kartu logbook default tertutup (`$isExpandedDefault = false`).<br>4. Rombak modal tambah & edit menjadi compact (2 kolom tanggal/minggu, attachment switcher, hapus box panduan panjang).<br>5. Hapus seluruh inline style (900+ baris) & script (200+ baris), ganti dengan `@vite`. |
| `resources/css/pages/peserta/logbook-index.css` | Create | Stylesheet modular untuk banner penempatan, progres tracker, kartu riwayat logbook (dengan efek hover `translateY(-2px)`, border `#01347C`, shadow), dan modal compact. |
| `resources/js/pages/peserta/logbook-index.js` | Create | Modul JavaScript ES6 untuk pengelolaan modal tambah/edit, accordion kartu individual, pencarian logbook, dan file/link attachment switching. |
| `vite.config.js` | Modify | Mendaftarkan entry point baru `resources/css/pages/peserta/logbook-index.css` dan `resources/js/pages/peserta/logbook-index.js`. |
| `tests/Feature/PesertaLogbookRevampTest.php` | Create | Feature test untuk memverifikasi halaman logbook, interaksi modal, kartu riwayat, dan upload berkas/link. |

---

## 5. Langkah Implementasi

### Fase 1: Pendaftaran Asset & Pembuatan Stylesheet Modular
- [x] Daftarkan `resources/css/pages/peserta/logbook-index.css` dan `resources/js/pages/peserta/logbook-index.js` pada `vite.config.js`.
- [x] Buat file stylesheet `resources/css/pages/peserta/logbook-index.css`:
  - Styling banner penempatan magang & progres tracker mingguan.
  - Styling kartu riwayat logbook `.weekly-card` dengan efek hover ala tugas (`border-color: #01347C; transform: translateY(-2px); box-shadow: 0 6px 18px rgba(1, 52, 124, 0.10);`).
  - Styling modal compact `.modal-box-compact` (max-height terkelola, padding proporsional).
  - Styling segmented attachment selector (Tab File & Tab Link).
  - Dukungan tema gelap (*dark mode*) menyeluruh.

### Fase 2: Implementasi Modul JavaScript ES6
- [x] Buat file `resources/js/pages/peserta/logbook-index.js`:
  - Fungsi modal open/close (Tambah & Edit) dengan keyboard Esc dan backdrop click.
  - Accordion toggle per kartu (klik header untuk buka/tutup rincian).
  - Logika pencarian logbook mingguan secara realtime.
  - Tab switcher lampiran (File PDF vs Tautan Link).
  - Karakter counter pada textarea kegiatan.

### Fase 3: Rekstrukturisasi Markup Blade View
- [x] Buka `resources/views/peserta/logbook/index.blade.php`.
- [x] Hapus inline `<style>` dan inline `<script>`.
- [x] Hubungkan `@vite(['resources/css/pages/peserta/logbook-index.css', 'resources/js/pages/peserta/logbook-index.js'])`.
- [x] Hapus `<x-slot:actions>` pada `<x-gentelella.page-title>` (menghilangkan tombol duplikat `+ Isi Logbook` di kanan atas).
- [x] Hapus tombol kontrol "Buka Semua" dan "Tutup Semua" pada `.logbook-filter-bar`.
- [x] Ubah inisialisasi default kartu menjadi tertutup: `$isExpandedDefault = false;`.
- [x] Redesain modal `#modalTambahLogbook` dan `#modalEditLogbook`:
  - Baris atas: Grid 2 kolom untuk `Pilih Minggu Magang` dan `Tanggal Pelaporan`.
  - Textarea `Ringkasan Kegiatan & Capaian` (ringkas, `rows="4"`).
  - Bagian Lampiran Berkas / Tautan dengan switcher modern ala Classroom.
  - Hapus box panduan panjang `.tips-box-bottom`.

### Fase 4: Pengujian & Quality Gate
- [x] Jalankan kompilasi asset: `npm run build`.
- [x] Jalankan test suite logbook:
  - `php artisan test tests/Feature/PesertaClassroomTugasLogbookTest.php`
  - `php artisan test tests/Feature/PesertaLogbookRevampTest.php`
  - `php artisan test tests/Feature/PesertaFullPortalFeatureTest.php`
- [x] Jalankan static analysis: `./vendor/bin/phpstan analyse --memory-limit=1G`.
- [x] Jalankan linter code style: `./vendor/bin/pint --test`.

---

## 6. Strategi Testing

1. **Feature Tests**:
   - Memastikan halaman logbook peserta dapat dibuka dengan status HTTP 200 OK.
   - Memastikan tombol `+ Isi Logbook Mingguan` tetap hadir di section riwayat.
   - Memastikan formulir tambah dan edit logbook dapat memproses data kegiatan, tanggal, serta lampiran file/link secara valid.
   - Memastikan proteksi duplikasi minggu tetap aktif.
2. **Pengujian Tampilan & Interaktivitas UI**:
   - Header halaman tampil bersih tanpa tombol duplikat di kanan atas.
   - Kartu-kartu riwayat logbook tertutup secara default dan memiliki efek hover yang halus saat mouse digerakkan.
   - Mengklik kartu membuka rincian logbook minggu tersebut secara mulus.
   - Modal "Isi Logbook Mingguan" terbuka dengan proporsi tinggi yang pas (tidak melebihi layar), tanpa teks bertele-tele, serta input lampiran file/link berfungsi mulus.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| **Pembersihan Inline Style melanggar aturan AGENTS.md** | Rendah | Mengikuti aturan baku: seluruh CSS diekstrak ke `resources/css/` dan JS ke `resources/js/`, Blade hanya memuat markup semantik. |
| **Kegagalan Test Existing akibat perubahan tombol** | Sedang | Tombol di bagian riwayat `+ Isi Logbook Mingguan` tetap dipertahankan, memastikan semua asersi test tetap terpenuhi 100%. |
| **Input Lampiran Gagal Terkirim** | Sedang | Form tetap mempertahankan `name="file_laporan"` dan `name="link_laporan"` serta `enctype="multipart/form-data"`. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tombol duplikat di kanan atas header halaman logbook telah dihapus.
- [x] Kartu riwayat logbook memiliki efek hover elevasi dan border Sleman Royal Blue (`#01347C`).
- [x] Tombol "Buka Semua" dan "Tutup Semua" telah dihapus, dan semua kartu tertutup (*collapsed*) secara default.
- [x] Modal Isi Logbook dirancang ulang menjadi compact, proporsional, tanpa box panduan panjang yang memakan ruang.
- [x] Lampiran file dan tautan dalam modal mengadopsi pola rapi ala pengumpulan tugas Classroom.
- [x] Sebanyak 1.100+ baris inline CSS dan JS diekstrak bersih dari file Blade `peserta/logbook/index.blade.php`.
- [x] Asset berhasil dikompilasi oleh Vite (`npm run build`).
- [x] Seluruh automated feature tests, PHPStan (0 errors), dan Pint linter lolos 100% hijau.

