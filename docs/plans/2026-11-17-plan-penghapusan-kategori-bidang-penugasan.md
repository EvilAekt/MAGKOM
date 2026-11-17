# Rencana Kerja: Penghapusan Dropdown Manual "Kategori Bidang" pada Formulir Penugasan Peserta

| Informasi Dokumen | Nilai |
| :--- | :--- |
| **Tanggal Pembuatan** | 17 November 2026 |
| **Status Dokumen** | Draf Rencana (Menunggu Persetujuan Pengguna) |
| **Target Fitur** | Manajemen Penugasan Peserta (Formulir Buat & Edit Tugas Pembimbing) |
| **Branch Target** | `test-2` |
| **Referensi Aturan** | `.agents/rules/AGENTS.md` (Poin 3, 5, 7, 10, 11) |

---

## 1. Analisis Kritis & Latar Belakang Masalah

### A. Evaluasi Logika Bisnis (Sesuai Arahan Pengguna)
1. **Status Bidang Peserta Sejak Registrasi & Seleksi**:
   - Sejak awal alur pendaftaran (`pendaftaran.create`), calon peserta telah memilih **Bidang Penempatan** (misal: *Aplikasi Informatika*, *Informasi Komunikasi Publik*, *Tata Kelola TI*, dsb.) dan telah diverifikasi oleh tim Seleksi Dinas Kominfo Sleman.
   - Saat proses penempatan (`penempatan`), peserta dihubungkan secara definitif ke bidang tersebut melalui relasi foreign key `penempatan.bidang_id` $\rightarrow$ `bidang.id` dan diasosiasikan langsung dengan **Pembimbing Lapangan ASN** dari bidang bersangkutan.
2. **Kejanggalan / Redundansi pada Formulir Tugas**:
   - Pada halaman **Buat Tugas Baru** (`resources/views/pembimbing/tugas/create.blade.php`) dan **Edit Tugas** (`resources/views/pembimbing/tugas/edit.blade.php`), saat ini terdapat field dropdown wajib:
     `Kategori Bidang *` (dengan pilihan opsi statis seperti *Pengembangan Web*, *UI/UX Design*, *Jaringan & Keamanan*, *Analisis Data*, dsb.).
   - Hal ini **tidak logis dan menimbulkan friksi ganda** bagi pembimbing:
     - Peserta yang ditugaskan sudah memiliki bidang resmi dari sistem.
     - Pembimbing terpaksa memilih ulang opsi yang tidak selalu mencerminkan nama bidang organisasi resmi Diskominfo.
     - Terjadi inkonsistensi terminologi antara *Bidang Organisasi Pemkab* dan *Kategori Bebas*.
3. **Solusi Elegan & Berkelanjutan**:
   - **Hapus dropdown input manual `Kategori Bidang *`** dari antarmuka formulir (baik pembuatan tugas maupun pengubahan tugas).
   - **Otomatisasi Nilai di Backend**: Sistem backend secara cerdas mengisi kolom `kategori` pada tabel `tugas` langsung dari relasi penempatan peserta (`$penempatan->bidang->nama_bidang ?? 'Umum'`).
   - Dengan pendekatan ini:
     - Formulir penugasan menjadi jauh lebih bersih, ringkas, dan fokus (persis gaya *Google Classroom*: hanya *Tugaskan Ke*, *Tenggat Waktu*, dan *Publikasi*).
     - Data di database tetap terpenuhi secara konsisten tanpa ada kegagalan relasi atau error *Not Null*.
     - Seluruh *test suite* eksisting tetap kompatibel 100% (*zero regression*).

---

## 2. Ruang Lingkup Perubahan File

### A. Lapisan Tampilan (Blade View)
1. [`resources/views/pembimbing/tugas/create.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/pembimbing/tugas/create.blade.php):
   - Menghapus blok container `#groupKategoriSelect` (baris 233-285) yang memuat dropdown kustom "Kategori Bidang *".
2. [`resources/views/pembimbing/tugas/edit.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/pembimbing/tugas/edit.blade.php):
   - Menghapus blok container `#groupKategoriSelect` (baris 221-275).
3. [`resources/views/peserta/tugas/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/peserta/tugas/index.blade.php):
   - Menyelaraskan teks empty state dari *"Tidak ada tugas yang sesuai dengan kategori ini."* menjadi *"Tidak ada tugas yang sesuai dengan filter ini."* agar konsisten dengan filter status tab.

### B. Lapisan Interaksi (JavaScript Frontend)
1. [`resources/js/pages/pembimbing/tugas-create.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/pembimbing/tugas-create.js):
   - Menghapus validasi client-side yang mewajibkan input kategori (`if (!isDraft && !kategori) ...`).
   - Memberikan guard pengecekan elemen pada fungsi `initKategoriDropdown()` agar tidak melempar error ketika dropdown tidak ditemukan di DOM.
2. [`resources/js/pages/pembimbing/tugas-edit.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/pembimbing/tugas-edit.js):
   - Memberikan guard pengecekan elemen pada `initKategoriDropdown()` di halaman edit.

### C. Lapisan Backend & Validasi
1. [`app/Http/Requests/TugasRequest.php`](file:///d:/laragon/www/MAGKOM/app/Http/Requests/TugasRequest.php):
   - Mengubah aturan validasi `'kategori' => ['required', 'string', 'max:50']` menjadi `'kategori' => ['nullable', 'string', 'max:50']`.
   - Ini memastikan form web yang tidak lagi mengirimkan parameter `kategori` tetap lolos validasi, sementara test lawas yang menyertakan kategori tetap diterima.
2. [`app/Actions/Pembimbing/Tugas/CreateBatchTugasAction.php`](file:///d:/laragon/www/MAGKOM/app/Actions/Pembimbing/Tugas/CreateBatchTugasAction.php):
   - Mengisi nilai `kategori` secara otomatis dari `$penempatan->bidang->nama_bidang ?? 'Umum'` jika payload `kategori` kosong/null.
3. [`app/Actions/Pembimbing/Tugas/UpdateTugasAction.php`](file:///d:/laragon/www/MAGKOM/app/Actions/Pembimbing/Tugas/UpdateTugasAction.php):
   - Mempertahankan nilai `kategori` tugas lama atau mengisinya dari bidang penempatan jika tidak ada nilai baru yang dikirim.

---

## 3. Rencana Pengujian & Quality Gate

1. **Pengujian Fungsional Penugasan Pembimbing**:
   - Membuat tugas baru tanpa mengirimkan field `kategori`: form berhasil disubmit, tugas terbuat dengan status terbit/draf, dan kolom `kategori` di database otomatis terisi nama bidang peserta yang bersangkutan.
   - Mengedit tugas yang sudah ada tanpa field `kategori`: data tugas berhasil diperbarui tanpa error validasi.
2. **Pengujian Regresi Test Suite Eksisting**:
   - `tests/Feature/TugasLogbookTest.php`
   - `tests/Feature/PembimbingFullJobDeskTest.php`
   - `tests/Feature/PembimbingMultiMenteeTugasSchedulingTest.php`
   - `tests/Feature/PesertaClassroomTugasLogbookTest.php`
3. **Kompilasi & Analisis Statis**:
   - `npm run build` (memastikan bundle Vite terkompilasi bersih).
   - `./vendor/bin/pint --test` (format PSR-12).
   - `./vendor/bin/phpstan analyse --memory-limit=1G` (bebas error tipe data).

---

## 4. Konfirmasi & Persetujuan

Sesuai aturan tata kelola [AGENTS.md](file:///d:/laragon/www/MAGKOM/.agents/rules/AGENTS.md) Poin 3.3 dan Poin 10:
- Perubahan ini melibatkan penyederhanaan markup pada 2 file Blade formulir pembimbing (`tugas/create.blade.php` dan `tugas/edit.blade.php`) dengan menghapus field form yang redundan.
- Mohon persetujuan Anda untuk mulai mengeksekusi rencana perbaikan ini.

