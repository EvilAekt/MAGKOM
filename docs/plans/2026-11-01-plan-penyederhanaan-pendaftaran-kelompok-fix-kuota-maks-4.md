# Rencana Kerja: Penyederhanaan Alur Registrasi Kelompok, Kuota Anggota Fix (Maksimal 4 Orang), dan Pembersihan Redundansi UI

- **Tanggal**: 2026-11-01
- **Status**: Selesai / Terverifikasi (Passed 100%)
- **Dokumen Terkait**:
  - `resources/views/auth/register.blade.php`
  - `app/Http/Controllers/Auth/AuthController.php`
  - `resources/views/public/panduan-alur.blade.php`
  - `tests/Feature/PendaftaranKelompokTest.php`

---

## 1. Analisis Masalah Berdasarkan Permintaan & Screenshot

1. **Kelompok Maksimal 4 Orang (Bukan 5)**:
   - Sebelumnya sistem masih mengizinkan hingga 5 orang (1 ketua + 4 rekan), label pada formulir tertulis *"Kelompok (2–5 Orang)"*, dan tombol tertulis *"+ Tambah Anggota (Maks. 5 Orang)"*.
   - **Kebutuhan**: Kelompok dibatasi maksimal 4 orang (1 ketua + maksimal 3 rekan anggota, sehingga total tim adalah 2 s/d 4 orang).

2. **Jumlah Anggota Harus FIX (Tanpa Tombol Tambah Anggota)**:
   - Sebelumnya, terdapat tombol `+ Tambah Anggota` dan tombol `Hapus` dinamis yang membuat jumlah anggota dapat diubah-ubah semaunya oleh calon pendaftar.
   - Jika pengguna datang dari landing page dengan pilihan kuota (misal: "Kelompok (3 Orang)"), form registrasi sebelumnya mengabaikan pilihan tersebut dan tetap memulai dengan 2 orang + tombol tambah.
   - **Kebutuhan**: Saat kelompok dipilih (misal 3 orang), form anggota langsung menampilkan slot yang **FIX** (Anggota 2 dan Anggota 3) tanpa tombol tambah anggota dinamis.

3. **UI pada Screenshot Terlalu Rumit (*Overcomplicated*) & Redundan**:
   - Di screenshot terlihat 4 blok bertumpuk vertikal yang membingungkan:
     1. Banner Kuota Terpilih (*Kelompok 3 Orang*).
     2. Alert box biru pertama (*"Anda mendaftar sebagai perwakilan kelompok (3 orang)..."*).
     3. Toggle Tipe Pendaftaran `[ Mandiri (1 Orang) ]` vs `[ Kelompok (2–5 Orang) ]`.
     4. Alert box biru kedua (*"Pendaftaran Akun Ketua Kelompok: Masukkan nama kelompok dan identitas rekan tim di bawah"*).
   - Terdapat **2 alert box biru yang isinya saling tumpang tindih dan bertentangan** (satu menyebut berkas diisi setelah akun aktif, satunya menyuruh isi di bawah).
   - Menampilkan tombol toggle pemilihan tipe magang ketika pengguna **sudah memilih kuota kelompok di landing page** membuat alur terasa redundan dan rawan salah klik.

---

## 2. Solusi Desain & Alur Pengguna (User Flow)

### A. Skenario 1: Calon Peserta Datang dari Landing Page (Sudah Pilih Kuota)
*Contoh URL: `/register?bulan=X&jurusan=Y&tipe=kelompok&jumlah=3`*
1. **Kartu Ringkasan Kuota Bersih & Terpadu**:
   - Menampilkan formasi jurusan, periode bulan, dan badge `Kelompok (3 Orang)`.
   - Di dalam kartu ini langsung disematkan instruksi ringkas:  
     *"Anda mendaftar sebagai ketua tim untuk 3 orang. Lengkapi nama kelompok dan data 2 rekan tim Anda di bawah ini."*
2. **Hilangkan Alert Box Biru Ganda**:
   - Kedua alert box biru yang bertumpuk dihapus total.
3. **Sembunyikan Toggle Pilihan Tipe**:
   - Tidak perlu menampilkan tombol toggle `[ Mandiri ]` vs `[ Kelompok ]` lagi karena kuota sudah terkunci dari landing page. Parameter `tipe_pendaftaran` dan `jumlah` dikirim otomatis via input hidden.
4. **Form Anggota Langsung Tampil FIX**:
   - Form nama kelompok otomatis aktif.
   - Slot anggota langsung di-render sejumlah `jumlah - 1` (jika pilih 3 orang = Anggota 2 dan Anggota 3 langsung siap diisi).
   - **Tidak ada tombol "+ Tambah Anggota" atau tombol "Hapus"**. Jumlah anggota terkunci sesuai pilihan kuota landing page.

### B. Skenario 2: Calon Peserta Akses Langsung (`/register` Tanpa Kuota Landing)
1. Tampilkan selector bersih:
   - `[ Mandiri (1 Orang) ]` | `[ Kelompok (2–4 Orang) ]`
2. Jika memilih `Kelompok`:
   - Muncul selector ukuran tim yang tegas & simpel:
     `[ 2 Orang ]` `[ 3 Orang ]` `[ 4 Orang ]` (Maksimal 4 orang).
   - Begitu angka diklik (misal 4 orang):
     - Slot Anggota 2, Anggota 3, dan Anggota 4 langsung ditampilkan secara FIX.
     - Tidak ada tombol tambah/hapus dinamis yang membingungkan.

---

## 3. Rencana Perubahan Kode & File

| File | Bagian yang Diubah | Rincian Perubahan | Status |
| :--- | :--- | :--- | :--- |
| [resources/views/auth/register.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/auth/register.blade.php) | Template & JavaScript | 1. Hapus duplikasi banner alert biru ganda.<br>2. Sembunyikan toggle Mandiri/Kelompok jika sudah ada parameter `selectedJurusan`/`selectedTipe`.<br>3. Jika akses langsung, buat selector jumlah tim: 2, 3, 4 orang.<br>4. Hilangkan tombol `btnTambahAnggota` dan tombol hapus; slot anggota ter-render fix.<br>5. Ubah semua teks label 5 orang menjadi maksimal 4 orang. | Selesai |
| [app/Http/Controllers/Auth/AuthController.php](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Auth/AuthController.php) | `register()` | 1. Sesuaikan aturan validasi array `anggota`: `'min:1', 'max:3'` (maksimal 3 rekan + 1 ketua = 4 orang).<br>2. Validasi jumlah anggota cocok persis dengan kuota fix `redirect_jumlah` / `jumlah_kelompok`. | Selesai |
| [resources/views/public/panduan-alur.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/panduan-alur.blade.php) | Teks panduan | Sinkronkan teks panduan dari "Kelompok (2–5 Orang)" menjadi "Kelompok (2–4 Orang)". | Selesai |
| [tests/Feature/PendaftaranKelompokTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/PendaftaranKelompokTest.php) | Test cases | 1. Tambah test case validasi kelompok maksimal 4 orang.<br>2. Uji pendaftaran kelompok dengan kuota fix (harus cocok jumlah rekan). | Selesai |
| [tests/Feature/AlurMagangPageTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/AlurMagangPageTest.php) | Assertions | Sesuaikan assertion teks diselaraskan ke 2–4 orang. | Selesai |

---

## 4. Eksekusi Pengujian dengan Laragon PHP
- [x] Pengujian dijalankan menggunakan runtime Laragon yang terpasang di sistem (`D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test tests/Feature/PendaftaranKelompokTest.php tests/Feature/AlurMagangPageTest.php`).
- [x] Seluruh 11 test assertions (6 pada PendaftaranKelompokTest dan 5 pada AlurMagangPageTest) berhasil lulus 100% tanpa regresi.
- [x] Kode PHP diformat dengan Laravel Pint (`./vendor/bin/pint`).
