# Plan: Revamp Panel Upload Tugas Peserta (Referensi Google Classroom: Link & File Modal)

| Metadata | Keterangan |
|---|---|
| **Tanggal Pembuatan** | 3 November 2026 |
| **Status Dokumen** | Menunggu Persetujuan (Pending Approval) |
| **Target Area** | Portal Peserta: Modul Detail Tugas (`resources/views/peserta/tugas/show.blade.php`, `resources/css/pages/peserta/tugas-detail.css`, `resources/js/pages/peserta/tugas-detail.js`) |
| **Referensi Desain** | Google Classroom Assignment Turn-in Panel (Gambar 1, 2, 3, 4 dari pengguna) |

---

## 1. Tujuan
Merekstrukturisasi dan memodernisasi antarmuka panel kanan (*Your Work / Tugas Anda*) pada halaman **Detail Tugas Bimbingan Peserta** (`peserta/tugas/show.blade.php`) agar mengadopsi alur interaksi Google Classroom sesuai referensi tangkapan layar pengguna:
1. **Dropdown Tambah Berkas (Hanya 2 Opsi)**: Tombol `+ Tambah berkas` memunculkan dropdown yang secara ketat hanya memiliki 2 opsi:
   - 🔗 **Link** (Tautan)
   - 📎 **File** (Berkas)
   *(Opsi Google Drive API, Docs, Sheets, dan Slides dihilangkan sesuai permintaan pengguna agar tidak bergantung pada API Drive yang kompleks).*
2. **Modal Input Tautan (Link)**: Menampilkan dialog modal bersih untuk memasukkan URL tautan tugas/demo dengan validasi format.
3. **Modal Sisipkan Berkas (File)**: Menampilkan dialog modal dengan 2 tab:
   - **Upload**: Area *drag & drop* + tombol "Browse" untuk memilih file dari komputer lokal (maks. 5MB, format PDF, ZIP, RAR, DOCX).
   - **Recent**: Daftar riwayat berkas yang pernah diunggah oleh peserta pada tugas-tugas sebelumnya, sehingga peserta dapat memilih ulang berkas tanpa harus upload ulang dari awal.
4. **Daftar Lampiran Terpilih & Aktivasi Tombol Serahkan**: Berkas/link yang dipilih akan muncul sebagai kartu/pill lampiran di panel "Tugas Anda" dengan tombol hapus (`✕`), dan tombol "Serahkan" berubah menjadi aktif.
5. **Panel Komentar Pribadi**: Kartu komentar pribadi di bawah panel tugas untuk menulis catatan progres kepada pembimbing lapangan.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis:
- **A-1 (Pemisahan Asset Sesuai AGENTS.md)**: Seluruh styling CSS baru diletakkan di `resources/css/pages/peserta/tugas-detail.css` dan seluruh interaksi JavaScript (modal, tabs, dropzone, state tombol) diletakkan di `resources/js/pages/peserta/tugas-detail.js` (ES Modules). Blade `show.blade.php` hanya bertindak sebagai semantic markup.
- **A-2 (Sumber Data Tab Recent)**: Data berkas pada tab "Recent" diambil dari riwayat pengumpulan berkas peserta yang tersimpan pada tabel `progres_log` (dan/atau berkas lampiran logbook) milik akun peserta yang sedang login, di-load aman melalui `TugasController@show`.
- **A-3 (Catatan Progres)**: Input "Komentar pribadi" di bawah panel tugas akan bertindak sebagai `catatan` pada pengiriman progres ke pembimbing (memenuhi validasi `catatan` di `ProgresLogRequest`). Jika peserta menekan "Serahkan" tanpa mengisi komentar pribadi, sistem memberikan placeholder catatan otomatis (misal: *"Mengunggah berkas pekerjaan tugas"*).
- **A-4 (Dukungan Berkas Recent)**: Jika peserta memilih berkas dari tab "Recent", backend akan mereferensikan path berkas lama yang sudah ada atau membuat salinan aman di storage private tanpa meminta file fisik baru.

### Pertanyaan Terbuka untuk Pengguna:
1. *Apakah tombol "Serahkan" memerlukan dialog konfirmasi popup (seperti Classroom: "Serahkan tugas Anda? 1 lampiran akan dikirimkan...") atau cukup langsung melakukan submit form?*
   - **Rekomendasi**: Langsung submit dengan feedback loading spinner pada tombol "Serahkan" agar cepat dan tidak berbelit-belit.

---

## 3. Evaluasi Pendekatan Teknis

### Evaluasi Penanganan Tab "Recent":
- **Opsi 1: Direct View Injection via Controller (Direkomendasikan)**
  - Mengambil data berkas riwayat peserta di `TugasController@show` dan menyediakannya sebagai koleksi `$recentFiles` ke view.
  - *Kelebihan*: Sangat cepat (zero network latency), tidak perlu membuat route/endpoint API baru, minim risiko authorization bypass, dan data langsung siap pakai saat modal "Recent" dibuka.
  - *Kekurangan*: Terbatas pada maksimal 10–15 berkas terbaru.
  - *Rekomendasi*: **Opsi 1**.

- **Opsi 2: AJAX Dynamic Endpoint (`GET /peserta/tugas/recent-files`)**
  - Mengambil daftar berkas secara asinkron setiap kali tab "Recent" diklik.
  - *Kelebihan*: Bisa mendukung pagination jika berkas sangat banyak.
  - *Kekurangan*: Menambah latensi HTTP, membutuhkan endpoint baru, dan memerlukan penanganan loading skeleton/error state tambahan di frontend.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/tugas/show.blade.php` | Modify | Menyesuaikan markup panel kanan "Tugas Anda", tombol dropdown "+ Tambah berkas", list lampiran terpilih, modal Link, modal File (tab Upload & Recent), dan kartu Komentar Pribadi. Menghapus inline style/script lama sesuai aturan `AGENTS.md`. |
| `resources/css/pages/peserta/tugas-detail.css` | Create | Styling khusus Classroom panel kanan: dropdown menu, attachment pills, modal dialog backdrop, dropzone upload, tab navigation, dan kartu komentar pribadi. |
| `resources/js/pages/peserta/tugas-detail.js` | Create | Logika interaktif: toggle dropdown, open/close modal Link & File, switch tab Upload/Recent, drag-and-drop file upload handler, validasi URL link, sinkronisasi state tombol "Serahkan". |
| `app/Http/Controllers/Peserta/TugasController.php` | Modify | Menambahkan query `$recentFiles` pada method `show()` dan menangani input `recent_file_path` pada method `unggahProgres()` jika peserta memilih berkas dari riwayat. |
| `app/Http/Requests/ProgresLogRequest.php` | Modify | Menambahkan aturan validasi untuk `recent_file_path` (nullable, string) dan membuat `catatan` tetap terisi (atau default fallback). |
| `vite.config.js` | Modify | Mendaftarkan entry point asset baru `resources/css/pages/peserta/tugas-detail.css` dan `resources/js/pages/peserta/tugas-detail.js`. |
| `tests/Feature/PesertaTugasClassroomUploadTest.php` | Create | Feature test untuk memverifikasi rendering modal, pengiriman berkas lokal, pengiriman link tautan, dan pemilihan berkas recent. |

---

## 5. Permohonan Izin Perubahan Blade (Aturan AGENTS.md 3.3)

Sesuai aturan `AGENTS.md` sub-pasal 3.3 mengenai *Pengecualian Perubahan Blade*:
1. **Alasan Perubahan**: Fitur ini membutuhkan elemen semantik baru yang belum ada pada markup Blade sebelumnya, yaitu:
   - Kontainer Dropdown Menu untuk opsi Link dan File.
   - Kontainer Modal Dialog untuk "Tambah Tautan" (Link).
   - Kontainer Modal Dialog dengan sistem Tab untuk "Sisipkan Berkas" (Upload & Recent).
   - Kontainer Attachment List untuk menampilkan lampiran yang telah dipilih sebelum diserahkan.
   - Restrukturisasi kartu Komentar Pribadi agar terpisah secara visual dari form utama.
2. **File Blade yang Disentuh**: `resources/views/peserta/tugas/show.blade.php` (hanya pada baris panel kanan `your-work-panel` dan penambahan struktur modal di akhir view).
3. **Jaminan Kepatuhan**:
   - Seluruh `<style>` dan `<script>` inline lama pada `show.blade.php` akan dibersihkan dan dipindahkan ke `resources/css/` dan `resources/js/`.
   - Tidak ada inline event handler (`onclick`, `onchange`, dll.) yang akan ditambahkan ke Blade.

---

## 6. Langkah Implementasi Terperinci

### Tahap 1: Backend & Controller Preparation
- [x] Buka `app/Http/Controllers/Peserta/TugasController.php`.
- [x] Di method `show()`, tambahkan query `$recentFiles` untuk mengambil riwayat unggahan berkas peserta dari `ProgresLog` milik user bersangkutan (nama berkas, ukuran format, ekstensi/tipe, tgl unggah, path tersimpan).
- [x] Kirim `$recentFiles` ke view `peserta.tugas.show`.
- [x] Di method `unggahProgres()`, dukung parameter `recent_file_path`: jika diisi dan valid milik peserta, referensikan path berkas tersebut untuk progres baru.
- [x] Sesuaikan `app/Http/Requests/ProgresLogRequest.php` untuk mengizinkan `recent_file_path` dan memastikan minimal salah satu dari `file`, `link`, atau `recent_file_path` tersedia jika `catatan` sudah diisi.

### Tahap 2: Asset Frontend (CSS & JavaScript)
- [x] Buat file `resources/css/pages/peserta/tugas-detail.css`:
  - Styling panel kanan "Tugas Anda" (header, status badge, tombol outlined `+ Tambah berkas`, tombol `Serahkan`).
  - Styling dropdown menu mengambang (shadow halus, hover effect, icon item Link & File).
  - Styling modal dialog backdrop dan content box (animasi fade/scale halus, responsive).
  - Styling modal Tab (Upload vs Recent) dengan indikator aktif yang jelas.
  - Styling dropzone unggah berkas (border dashed, ilustrasi awan, tombol browse, hover drag-over highlight).
  - Styling daftar berkas Recent (list/grid item dengan ikon ekstensi berkas, nama, ukuran, waktu, dan checkbox/status selected).
  - Styling kartu "Komentar pribadi" (input box komentar dan tombol kirim).
- [x] Buat file `resources/js/pages/peserta/tugas-detail.js`:
  - Modular ES Module dengan event listener terstruktur (tanpa inline handler).
  - Handler dropdown: toggle open/close saat tombol `+ Tambah berkas` diklik, close on click outside.
  - Handler modal Link: buka modal, fokus input, validasi URL, tambah ke state lampiran, update UI list.
  - Handler modal File: buka modal, tab switching (Upload vs Recent), drag-over & drop event, file input change event, recent file selection, update UI list.
  - State manager: mengaktifkan/menonaktifkan tombol "Serahkan" bergantung pada apakah berkas/link sudah dipilih.
- [x] Daftarkan kedua file asset di `vite.config.js`.

### Tahap 3: Penataan Markup Blade (`peserta/tugas/show.blade.php`)
- [x] Buka `resources/views/peserta/tugas/show.blade.php`.
- [x] Muat asset via Vite directive `@vite(['resources/css/pages/peserta/tugas-detail.css', 'resources/js/pages/peserta/tugas-detail.js'])`.
- [x] Bersihkan tag inline `<style>` dan `<script>` lama dari view.
- [x] Susun ulang markup panel kanan:
  - Header "Tugas Anda" + status chip (Belum diserahkan / Diserahkan / Selesai).
  - Kontainer daftar item lampiran terpilih (Link / File) dengan tombol hapus.
  - Tombol `+ Tambah berkas` beserta menu dropdown (`#menuTambahBerkas`):
    - Opsi 1: Link (`data-action="open-modal-link"`)
    - Opsi 2: File (`data-action="open-modal-file"`)
  - Tombol utama `Serahkan` (`#btnSerahkanTugas`).
  - Catatan kaki: *"Pekerjaan tidak dapat diserahkan setelah tenggat"*.
  - Kartu "Komentar pribadi" di bawahnya dengan field input catatan progres ke pembimbing.
- [x] Tambahkan elemen Modal di akhir dokumen:
  - Modal `#modalTambahLink` (Form URL link tugas).
  - Modal `#modalSisipkanBerkas` (Tab Upload Dropzone & Tab Recent List).

### Tahap 4: Build & Quality Gate
- [x] Jalankan kompilasi asset: `npm run build`.
- [x] Jalankan linter formatting: `./vendor/bin/pint --test`.
- [x] Jalankan analisis statis: `./vendor/bin/phpstan analyse --memory-limit=1G`.
- [x] Jalankan unit & feature test: `php artisan test --filter=PesertaTugasClassroomUploadTest`.

---

## 7. Strategi Testing

1. **Feature Test Otomatis (`tests/Feature/PesertaTugasClassroomUploadTest.php`)**:
   - `test_peserta_can_view_tugas_detail_with_classroom_panel`: Memastikan halaman detail tugas me-render komponen dropdown, modal, dan daftar recent files dengan status HTTP 200.
   - `test_peserta_can_submit_tugas_with_uploaded_file`: Memastikan pengiriman tugas dengan file fisik tersimpan di disk private dan status berubah.
   - `test_peserta_can_submit_tugas_with_link`: Memastikan pengiriman tugas dengan link URL valid berhasil.
   - `test_peserta_can_submit_tugas_with_recent_file`: Memastikan pengiriman tugas menggunakan referensi berkas dari riwayat recent berhasil.
   - `test_peserta_cannot_submit_other_user_recent_file`: Memastikan peserta lain tidak bisa menyusupkan ID/path berkas milik peserta lain (anti-IDOR).
2. **Browser & Visual Verification**:
   - Uji klik tombol `+ Tambah berkas` (dropdown muncul dengan 2 opsi: Link & File).
   - Uji klik opsi `Link` (modal input tautan muncul).
   - Uji klik opsi `File` (modal berkas muncul dengan tab Upload & Recent).
   - Uji drag & drop berkas ke tab Upload dan pemilihan berkas di tab Recent.
   - Uji tombol "Serahkan" (aktif saat lampiran terisi, mengirim form dengan benar).

---

## 8. Risiko & Mitigasi

| Risiko | Tingkat | Mitigasi |
|---|---|---|
| **Keamanan IDOR pada Berkas Recent** | Tinggi | Validasi kepemilikan berkas di controller: pastikan berkas recent benar-benar milik peserta yang sedang terotentikasi sebelum dihubungkan ke `progres_log`. |
| **Validasi Ukuran dan Tipe Berkas** | Sedang | Validasi ganda: di sisi frontend (filter ekstensi & batas 5MB) dan di sisi backend `ProgresLogRequest` (`mimes:pdf,jpg,jpeg,png,zip,rar,doc,docx` dan `max:5120`). |
| **Kompatibilitas Tampilan Mobile** | Rendah | Menggunakan layout responsive flex/grid yang otomatis bertumpuk pada layar kecil (<768px). |

---

## 9. Kriteria Selesai (Definition of Done)
- [x] Tombol `+ Tambah berkas` menampilkan dropdown berisi hanya 2 opsi: Link dan File.
- [x] Modal Link berfungsi untuk menambahkan URL dan menampilkan item tautan di panel Tugas Anda.
- [x] Modal File menampilkan 2 tab: Upload (Dropzone + Browse) dan Recent (daftar berkas riwayat peserta).
- [x] Tidak ada integrasi langsung Google Drive API atau Docs/Sheets/Slides yang rumit.
- [x] Tombol Serahkan terintegrasi dengan pengiriman progres ke pembimbing.
- [x] Seluruh CSS berada di `resources/css/` dan JS di `resources/js/` tanpa inline script/style di Blade.
- [x] Seluruh pengujian otomatis (PHPUnit), PHPStan, Pint, dan `npm run build` lulus 100% hijau.

