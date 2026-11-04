# Rencana Kerja: Redesign Halaman Buat Tugas Baru Dashboard Pembimbing MAGKOM

- **Tanggal**: 2026-11-03
- **Slug**: `redesign-buat-tugas-pembimbing`
- **Target Halaman**: Dashboard Pembimbing — Buat Tugas Baru (`/pembimbing/tugas/create`)
- **Referensi Desain**: `Designnnn/` (`Redesign_Tugas_Magang_Semua_Tampilan.html`, `Buat Tugas Baru@1x.png`, `Dialog – Tugaskan ke.png`, `Dialog – Tambahkan link@1x.png`, `Dialog – Upload File@1x.png`, `Menu – Opsi Tugaskan@1x.png`, `Popup – Tenggat@1x.png`)

---

## 1. Tujuan & Ruang Lingkup

Mengimplementasikan redesign menyeluruh pada halaman **Buat Tugas Baru** di Dashboard Pembimbing sesuai desain mockup di folder `Designnnn/` dengan standar arsitektur MAGKOM (PHP 8.3, Laravel 10, Vite, Vanilla CSS & Vanilla JS ES Modules).

### Fitur yang Disediakan
1. Form Judul Penugasan & Petunjuk Instruksi dengan format toolbar (B, I, U, Numbering, Clear).
2. Pemilihan Peserta Multi-Mentee:
   - Modal interaktif "Tugaskan ke" dengan avatar inisial, institusi/jurusan.
   - Pilihan "Semua peserta" (kewenangan pembimbing aktif) & checkbox individual.
   - Pencarian peserta jika jumlah mentee banyak.
   - Ringkasan peserta terpilih di sidebar.
3. Parameter Penugasan:
   - Kategori Bidang (dropdown/picker).
   - Tingkat Kompleksitas (Segmented: Rendah, Sedang, Tinggi).
   - Tenggat (Deadline) dengan popup kalender interaktif (HH/BB/TTTT, berakhir 23:59 WIB, proteksi melewati batas magang peserta).
4. Lampiran Acuan Kerja (HANYA 2 Opsi):
   - **Upload File**: Dialog modal drag-and-drop, validasi format (PDF, DOCX, XLSX, PPTX, ZIP, Gambar maks 10MB/5MB), preview badge, file name, file size, tombol hapus.
   - **Tambahkan Link**: Dialog modal input URL valid dengan validasi frontend & backend, render kartu link acuan.
5. Mekanisme Publikasi Terintegrasi:
   - **Tugaskan Sekarang**: Validasi input, simpan tugas, status publikasi langsung aktif (`dipublikasikan`), notifikasi real-time SSE ke peserta.
   - **Jadwalkan**: Modal input tanggal & waktu publikasi (Asia/Jakarta), simpan dengan status `terjadwal`, otomatis dipublikasikan via Laravel Scheduler saat waktu tiba.
   - **Simpan Draf**: Tugas tersimpan dengan status `draft` dan belum dapat diakses peserta.
6. Isolasi Hak Akses & Keamanan:
   - Pembimbing hanya dapat menugaskan mentee yang berada dalam kewenangannya (`pembimbing_id == auth()->id()`).
   - Mencegah IDOR via manipulasi request `penempatan_ids`.
   - Peserta hanya dapat melihat tugas yang ditugaskan kepada mereka setelah waktu publikasi aktif tiba.

### Elemen yang Dihilangkan Sesuai Instruksi
1. Menu Rubrik
2. Pemilihan Kelas
3. Integrasi Google Drive
4. Integrasi YouTube
5. Google Docs / Slides / Sheets
6. Poin atau sistem penilaian poin
7. Topik

---

## 2. Investigasi Arsitektur & Analisis Baseline

| Komponen | Status Eksisting | Analisis Kebutuhan Redesign |
| :--- | :--- | :--- |
| **Database `tugas`** | Memiliki `penempatan_id`, `deadline`, `status` (`belum`, `proses`, `selesai`), `file_lampiran`, `link_referensi`. | Perlu mendukung: (1) Multi-mentee assignment tanpa merusak lifecycle submission mandiri per mentee, (2) Status publikasi (`dipublikasikan`, `terjadwal`, `draft`), (3) `jadwal_publikasi` (timestamp). |
| **Scheduler** | `app/Console/Kernel.php` sudah memiliki cron scheduler aktif dengan timezone `Asia/Jakarta`. | Tambahkan Artisan command `magkom:publikasikan-tugas-terjadwal` yang dijalankan setiap menit (`->everyMinute()->withoutOverlapping()`). |
| **Frontend Assets** | `create.blade.php` sebelumnya mencampur styling statis `<style>` dan inline `<script>`. | Pindahkan seluruh CSS ke `resources/css/pages/pembimbing/tugas-create.css` dan interaksi ke `resources/js/pages/pembimbing/tugas-create.js`, didaftarkan di Vite. Blade bersih dari `<style>` dan inline JS. |
| **Kebijakan Akses (Policy)** | `TugasPolicy` memeriksa `penempatan->peserta_id === $user->id` dan `pembimbing_id === $user->id`. | Perlu ditambahkan pembatasan: peserta hanya boleh melihat tugas jika status publikasi sudah `dipublikasikan`. |

---

## 3. Evaluasi Opsi Arsitektur

### Evaluasi A: Relasi Tugas ke Banyak Peserta (Multi-Mentee)

- **Opsi 1: Multi-Record Batch Insertion (Rekomendasi)**
  - *Cara Kerja*: Ketika pembimbing memilih $N$ peserta, sistem menyimpan $N$ baris record `Tugas` yang saling terhubung dengan `batch_id` (UUID). Lampiran file fisik disimpan satu kali di private storage dan di-link ke semua record tersebut.
  - *Kelebihan*:
    1. **Zero Breaking Change**: `progres_log` (`tugas_id`), `StatusTugasEnum` (belum, proses, selesai), reviu pembimbing, dan penilaian tetap mandiri untuk setiap mentee. Mentee A yang sudah selesai tidak akan mengubah status Mentee B yang masih proses.
    2. Seluruh controller, service laporan, dashboard metrics, dan 33 unit test yang sudah ada tetap berfungsi 100%.
    3. Sangat aman, terisolasi, dan mudah di-audit per mentee.
  - *Kekurangan*: Sedikit duplikasi baris metadata `tugas` saat penugasan massal (namun data tugas di MAGKOM bertaraf ratusan, bukan jutaan).

- **Opsi 2: Pivot Table Many-to-Many (`penempatan_tugas`)**
  - *Cara Kerja*: Mengubah tabel `tugas` agar tidak memiliki `penempatan_id`, membuat tabel pivot `penempatan_tugas`.
  - *Kekurangan*: **Breaking Change Masif**. Menghancurkan relasi `progres_log`, memecah `status` pengerjaan tugas, mematahkan eager loading `penempatan`, merusak modul penilaian, modul laporan kinerja, dan membutuhkan penulisan ulang puluhan file controller/service.
  - *Keputusan*: **Pilih Opsi 1**.

### Evaluasi B: Mekanisme Penjadwalan Publikasi

- **Opsi 1: Laravel Console Scheduler `everyMinute()` (Rekomendasi)**
  - *Cara Kerja*: Menambahkan command `magkom:publikasikan-tugas-terjadwal` yang berjalan otomatis di `app/Console/Kernel.php` setiap menit.
  - *Kelebihan*:
    1. Tidak bergantung pada driver queue eksternal atau database queue worker yang sedang menyala atau mati di local/staging.
    2. Idempoten: query hanya mengambil `where('status_publikasi', 'terjadwal')->where('jadwal_publikasi', '<=', now('Asia/Jakarta'))`, mengubahnya menjadi `dipublikasikan`, mencatat audit, dan mengirimkan SSE notification.
    3. Tugas tidak akan dipublikasikan ganda.
  - *Kekurangan*: Resolusi waktu maksimal 1 menit (standar industri untuk scheduling tugas/pengumuman).

- **Opsi 2: Delayed Queue Job**
  - *Kekurangan*: Membutuhkan `queue:work` yang berjalan terus-menerus. Jika worker restart atau queue database di-reset, job yang ter-delay dapat hilang.
  - *Keputusan*: **Pilih Opsi 1** (didukung opsi dispatch job jika queue driver aktif).

---

## 4. Perubahan Database (Migration Aman)

Buat migration baru: `database/migrations/2026_11_03_000001_add_scheduling_and_batch_to_tugas_table.php`:
- `batch_id` (string 36 nullable, index)
- `status_publikasi` (string 20, default `'dipublikasikan'`, index) -> nilai: `dipublikasikan`, `terjadwal`, `draft`
- `jadwal_publikasi` (timestamp nullable, index)

> [!NOTE]
> Nilai default `status_publikasi` adalah `'dipublikasikan'` sehingga seluruh data tugas yang sudah ada di database tetap aktif dan tidak terdampak.

---

## 5. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `database/migrations/2026_11_03_000001_add_scheduling_and_batch_to_tugas_table.php` | Create | Tambah kolom `batch_id`, `status_publikasi`, `jadwal_publikasi`. |
| `app/Models/Tugas.php` | Modify | Tambah `$fillable` (`batch_id`, `status_publikasi`, `jadwal_publikasi`), `$casts` (`jadwal_publikasi` => datetime), scope `dipublikasikan()`. |
| `app/Http/Requests/TugasRequest.php` | Modify | Dukung `penempatan_ids` (array required/min:1) & fallback `penempatan_id`, validasi `aksi_publikasi` (`sekarang`, `terjadwal`, `draft`), validasi `tgl_publikasi` & `waktu_publikasi` di masa depan dan sebelum deadline. |
| `app/Policies/TugasPolicy.php` | Modify | Pembatasan view/submit: mentee tidak dapat melihat/mengirim progres tugas jika `status_publikasi !== 'dipublikasikan'`. |
| `app/Http/Controllers/Pembimbing/TugasController.php` | Modify | Update method `create` (siapkan data mentee lengkap dengan avatar/bidang) dan `store` (tangani multi-mentee insert dalam `DB::transaction`, file upload tunggal bersama, penjadwalan & draf). |
| `app/Http/Controllers/Peserta/TugasController.php` | Modify | Pastikan query daftar & detail tugas peserta hanya menyertakan tugas yang berstatus publikasi `dipublikasikan`. |
| `app/Console/Commands/PublikasikanTugasTerjadwalCommand.php` | Create | Artisan command untuk memeriksa dan mempublikasikan tugas terjadwal yang sudah mencapai waktunya. |
| `app/Console/Kernel.php` | Modify | Daftarkan `magkom:publikasikan-tugas-terjadwal` setiap menit (`->everyMinute()->withoutOverlapping()`). |
| `resources/css/pages/pembimbing/tugas-create.css` | Create | Seluruh styling halaman Buat Tugas Baru, layout dua kolom, modal dialogs, dropzone, segmented control, date picker, dropdown menu. |
| `resources/js/pages/pembimbing/tugas-create.js` | Create | Modul Vanilla JS ES Modules untuk modal peserta (checkbox & "Semua"), dialog link, dialog upload berkas, popup datepicker kalender, split button & modal penjadwalan, toolbar format deskripsi. |
| `vite.config.js` | Modify | Daftarkan entry point CSS & JS baru. |
| `resources/views/pembimbing/tugas/create.blade.php` | Modify | Markup Blade bersih tanpa `<style>` atau inline `<script>`, mengimplementasikan struktur visual sesuai `Designnnn/`. |
| `tests/Feature/PembimbingMultiMenteeTugasSchedulingTest.php` | Create | Unit/Feature test lengkap untuk skenario multi-mentee, upload, link, deadline, penjadwalan, command scheduler, dan IDOR. |

---

## 6. Langkah Implementasi Bertahap

- [ ] **Langkah 1**: Buat dan jalankan migration penambahan kolom `batch_id`, `status_publikasi`, dan `jadwal_publikasi`.
- [ ] **Langkah 2**: Update `Tugas.php` dengan fillable, casts, dan scope publikasi.
- [ ] **Langkah 3**: Buat Artisan Command `PublikasikanTugasTerjadwalCommand` dan daftarkan di `app/Console/Kernel.php`.
- [ ] **Langkah 4**: Update `TugasPolicy.php` dan `Peserta/TugasController.php` untuk membatasi visibilitas tugas terjadwal/draft dari peserta.
- [ ] **Langkah 5**: Update `TugasRequest.php` untuk memvalidasi multi-mentee (`penempatan_ids`), validasi kepemilikan pembimbing, dan validasi waktu penjadwalan.
- [ ] **Langkah 6**: Update `Pembimbing/TugasController.php` method `create()` dan `store()` dengan transaksi database atomik.
- [ ] **Langkah 7**: Susun stylesheet terisolasi di `resources/css/pages/pembimbing/tugas-create.css` mengacu pada token warna MAGKOM dan desain di `Designnnn/`.
- [ ] **Langkah 8**: Susun modul JavaScript di `resources/js/pages/pembimbing/tugas-create.js` (dialog peserta, dialog link, dialog upload, popup datepicker, split button dropdown, modal jadwal).
- [ ] **Langkah 9**: Daftarkan asset di `vite.config.js`.
- [ ] **Langkah 10**: Refactor `resources/views/pembimbing/tugas/create.blade.php` dengan markup semantik rapi, `@push('styles')` dan `@push('scripts')`, tanpa inline CSS/JS.
- [ ] **Langkah 11**: Eksekusi build Vite (`npm run build`).
- [ ] **Langkah 12**: Jalankan pengujian otomatis (`php artisan test`) dan pastikan seluruh skenario hijau tanpa regresi.

---

## 7. Rencana Pengujian (Test Suite)

Minimal 17 skenario wajib yang akan diuji:
1. Pembimbing berhasil membuat tugas untuk 1 peserta secara instan.
2. Pembimbing berhasil memilih dan membuat tugas untuk beberapa peserta sekaligus (`penempatan_ids`).
3. Pembimbing berhasil memilih opsi "Semua peserta bimbingan".
4. Pembimbing ditolak (403 / validation error) jika mencoba menyisipkan peserta di luar kewenangannya (Anti-IDOR).
5. Upload file acuan kerja berhasil dan tersimpan di storage `private`.
6. Upload file dengan format atau ukuran tidak valid ditolak.
7. Tautan referensi berhasil ditambahkan dan divalidasi format URL-nya.
8. Tautan URL tidak valid ditolak.
9. Deadline berhasil divalidasi (tidak boleh di masa lampau dan tidak boleh melampaui `tgl_selesai` magang).
10. Tugas "Tugaskan Sekarang" langsung berstatus `dipublikasikan` dan dapat diakses peserta.
11. Tugas "Jadwalkan" tersimpan dengan status `terjadwal` dan `jadwal_publikasi` di masa depan.
12. Peserta terpilih TIDAK dapat melihat tugas terjadwal sebelum waktu publikasi tiba (baik di daftar tugas maupun detail tugas).
13. Peserta yang tidak dipilih TIDAK dapat melihat atau mengakses tugas tersebut.
14. Artisan command `magkom:publikasikan-tugas-terjadwal` berhasil mempublikasikan tugas yang waktunya sudah tiba.
15. Artisan command tidak mempublikasikan tugas secara berulang (idempoten).
16. Setelah dipublikasikan via command, peserta terpilih dapat melihat tugas dan menerima notifikasi.
17. Tidak terjadi regresi pada 33 test tugas yang sudah ada sebelumnya.

---

## 8. Persetujuan Perubahan Blade (Sesuai AGENTS.md Rule 3.3)

Sesuai aturan permanen **AGENTS.md Bagian 3.3**:
- **Alasan Markup Baru Diperlukan**: Halaman lama menggunakan layout panel Gentelella standar dengan satu dropdown mentee, field URL terbuka, dan dropzone statis. Desain final `Designnnn/` memerlukan container baru untuk modal dialog "Tugaskan ke" (multi-select peserta dengan avatar), modal dialog "Tambahkan link", modal dialog "Upload File", popup datepicker kalender tenggat, toolbar format instruksi, dan split button menu opsi tugaskan.
- **File Blade yang Diubah**: `resources/views/pembimbing/tugas/create.blade.php`.
- **Jaminan Kepatuhan**: Tidak ada CSS di `<style>`, tidak ada inline JS di `<script>`, tidak ada inline event handlers (`onclick` dll). Semua styling ditempatkan di `resources/css/pages/pembimbing/tugas-create.css` dan interaksi di `resources/js/pages/pembimbing/tugas-create.js`.

