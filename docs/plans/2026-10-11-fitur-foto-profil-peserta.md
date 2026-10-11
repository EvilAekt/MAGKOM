# Plan: Fitur Unggah & Penampilan Foto Profil Peserta MagKom

| Metadata | Keterangan |
|---|---|
| **Tanggal Pembuatan** | 11 Oktober 2026 |
| **Status Dokumen** | Selesai Diimplementasikan (Completed) |
| **Target Area** | Portal Peserta: Modul Profil & Biodata (`resources/views/peserta/profil/show.blade.php`), Controller (`Peserta\ProfilController`), Model (`PesertaProfil`), Migrasi Database, Layout Topbar (`layouts/peserta.blade.php`) |
| **Kebutuhan Pengguna** | Menambahkan fitur **Foto Profil** pada data biodata peserta agar peserta dapat mengunggah pasfoto/foto profil resmi, melihat pratinjau langsung, dan foto tersebut ditampilkan secara terintegrasi di Hero Banner, Kartu Identitas Magang (Student ID Card), dan Topbar. |

---

## 1. Tujuan

1. **Penyimpanan Berkas Foto Profil**:
   - Menambahkan kolom `foto` (nullable string) pada tabel `peserta_profil` untuk menyimpan path berkas foto.
   - Menyediakan fungsionalitas unggah foto profil dengan validasi ketat (JPG, JPEG, PNG, WEBP, ukuran maks 2 MB).
   - Menghapus berkas foto lama secara otomatis saat peserta mengunggah foto baru guna mencegah berkas sampah (*orphaned storage files*).
   - Menyediakan opsi hapus foto jika peserta ingin kembali ke avatar inisial bawaan.

2. **Pengalaman Pengguna (UI/UX) yang Dinamis & Modern**:
   - **Kartu Unggah Foto Profil Interaktif** di kolom kanan biodata:
     - Avatar preview lingkaran (100px) dengan indikator status.
     - Live preview gambar instan di sisi klien (*client-side file reader*) saat file baru dipilih sebelum form disubmit.
     - Tombol pilih berkas kustom dan tombol hapus foto.
     - Petunjuk format & rekomendasi: *"Gunakan pasfoto formal atau foto berasio 1:1, format JPG/PNG/WEBP maks 2 MB."*
   - **Integrasi Tampilan Foto**:
     - **Profile Hero Banner**: Menampilkan foto profil melingkar dengan bingkai putih dan *object-fit: cover*.
     - **Student ID Card (Kolom Kiri)**: Menampilkan pasfoto peserta pada kartu tanda magang virtual.
     - **Topbar Shell**: Avatar topbar kanan atas dan menu dropdown profil menampilkan foto peserta.

3. **Integritas Sistem & Backward Compatibility**:
   - Jika peserta belum mengunggah foto, sistem secara *graceful* tetap menampilkan avatar inisial nama tanpa broken image.
   - Seluruh test suite yang ada tetap lolos tanpa breaking change.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis:
- **A-1**: Berkas foto profil disimpan pada disk `public` di folder `profil-peserta/` dan dapat diakses publik via URL `/storage/profil-peserta/...` melalui symbolic link `public/storage`.
- **A-2**: Kolom `foto` bersifat opsional (`nullable`), sehingga peserta yang belum mengunggah foto tetap dapat menyimpan data akademik dan kontak darurat tanpa kendala.
- **A-3**: Form biodata pada `peserta.profil.show` saat ini belum memiliki atribut `enctype="multipart/form-data"`. Atribut ini wajib ditambahkan agar pengiriman berkas gambar berhasil.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan pemblokir.

---

## 3. Evaluasi Opsi Desain

### 3.1 Penempatan Kolom Database
- **Opsi 1: Menambahkan kolom `foto` pada tabel `users`.**
  - *Kekurangan*: Tabel `users` bersifat generik untuk semua peran staf (SuperAdmin, Admin, Pembimbing). Memodifikasi tabel inti users berpotensi menimbulkan dampak samping pada seeder dan flow registrasi publik.
- **Opsi 2: Menambahkan kolom `foto` pada tabel `peserta_profil` (Direkomendasikan).**
  - *Kelebihan*: Sangat modular, terisolasi khusus di domain profil peserta magang, dan selaras dengan `PesertaProfilRequest` serta `PesertaProfil::updateOrCreate()`.
  - *Rekomendasi*: **Opsi 2**.

### 3.2 Penempatan Komponen Unggah di Tampilan Profil
- **Opsi 1: Modal terpisah untuk ganti foto.**
  - *Kekurangan*: Menambah klik berlebih dan alur modal tambahan.
- **Opsi 2: Seksi Terdedikasi "Foto Profil Resmi" di bagian paling atas kolom kanan (Direkomendasikan).**
  - *Kelebihan*: Alur pengisian sangat alami: peserta melihat/memilih foto profil terlebih dahulu sebelum mengisi informasi akademik dan kontak, lengkap dengan live preview instan.
  - *Rekomendasi*: **Opsi 2**.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| `database/migrations/2026_10_11_000002_add_foto_to_peserta_profil_table.php` | **Buat** | Menambahkan kolom `foto` (string, nullable) pada tabel `peserta_profil`. |
| `app/Models/PesertaProfil.php` | **Ubah** | Daftarkan kolom `foto` ke `$fillable` dan tambahkan helper accessor `foto_url`. |
| `app/Http/Requests/PesertaProfilRequest.php` | **Ubah** | Tambahkan aturan validasi `'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048']` dan opsi `'hapus_foto' => ['nullable', 'boolean']`. |
| `app/Http/Controllers/Peserta/ProfilController.php` | **Ubah** | Tangani upload berkas foto ke disk `public`, hapus foto lama jika diganti/dihapus, dan perbarui kalkulasi checklist kelengkapan profil. |
| `resources/views/peserta/profil/show.blade.php` | **Ubah** | Tambahkan `enctype="multipart/form-data"`, buat seksi unggah foto profil dengan live preview di kolom kanan, tampilkan foto di Profile Hero Banner dan Student ID Card. |
| `resources/views/layouts/peserta.blade.php` | **Ubah** | Tampilkan foto profil di avatar topbar dan dropdown profil jika foto tersedia. |
| `tests/Feature/PesertaPortalWorkflowTest.php` | **Ubah** | Tambahkan skenario pengujian upload foto profil, verifikasi file tersimpan di storage, dan verifikasi penghapusan foto. |

---

## 5. Langkah Implementasi Terperinci

### Langkah 1: Migrasi Database & Model Eloquent
- [x] Buat file migrasi `database/migrations/2026_10_11_000002_add_foto_to_peserta_profil_table.php` dengan penambahan kolom `$table->string('foto', 255)->nullable()->after('user_id');`.
- [x] Jalankan migrasi via `php artisan migrate`.
- [x] Buka `app/Models/PesertaProfil.php`:
  - Tambahkan `'foto'` ke properti `$fillable`.
  - Tambahkan accessor `getFotoUrlAttribute()`: mengembalikan URL storage jika berkas ada, atau null jika belum ada.

### Langkah 2: Request & Controller Handling
- [x] Buka `app/Http/Requests/PesertaProfilRequest.php`:
  - Tambahkan rule `'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048']`.
  - Tambahkan pesan error bahasa Indonesia untuk ukuran dan format gambar.
- [x] Buka `app/Http/Controllers/Peserta/ProfilController.php`:
  - Perbarui metode `simpan()`:
    - Jika terdapat berkas `foto` yang diunggah: hapus berkas foto lama dari disk `public`, simpan berkas baru ke direktori `profil-peserta`, dan masukkan path ke data update.
    - Jika terdapat flag `hapus_foto == true`: hapus berkas foto lama dan set kolom `foto` menjadi `null`.
  - Tambahkan indikator `foto` ke array `$checklist` di metode `show()` untuk menghitung persentase kelengkapan profil.

### Langkah 3: Revamp Tampilan Profil & Blade Template
- [x] Buka `resources/views/peserta/profil/show.blade.php`:
  - Tambahkan `enctype="multipart/form-data"` pada `<form id="formBiodataPeserta">`.
  - Pada **Profile Hero Banner**: jika `$profil?->foto_url` ada, tampilkan `<img src="{{ $profil->foto_url }}" class="hero-avatar-img">`, jika tidak tampilkan avatar inisial.
  - Pada **Student ID Card (Kolom Kiri)**: tampilkan avatar/pasfoto peserta di header kartu identitas.
  - Pada **Kolom Kanan (Formulir Biodata)**: tambahkan kartu baru di urutan teratas:
    - **Seksi Foto Profil Resmi (`📷`)**:
      - Box preview lingkaran 100px.
      - Input file kustom tersembunyi dengan tombol pemicu "Pilih Foto Baru".
      - Tombol "Hapus Foto" (bila foto sudah ada).
      - Live preview JavaScript saat pengguna memilih file gambar baru dari komputernya.
- [x] Buka `resources/views/layouts/peserta.blade.php`:
  - Tampilkan `<img>` foto pada topbar avatar dan dropdown header jika `$user->profil?->foto_url` tersedia.

### Langkah 4: Pengujian & Quality Assurance
- [x] Perbarui dan jalankan test suite:
  - `php artisan test --filter=PesertaPortalWorkflowTest`
  - `php artisan test --filter=PesertaFullPortalFeatureTest`
- [x] Pastikan upload berkas palsu berhasil tersimpan dan URL dapat diakses.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Kolom `foto` tersedia pada tabel `peserta_profil` di database.
- [x] Form profil memiliki kemampuan unggah foto dengan validasi format (JPG/PNG/WEBP) dan ukuran (maks 2MB).
- [x] Terdapat fitur live preview instan sebelum pengguna menekan tombol simpan.
- [x] Foto yang diunggah tampil di Hero Banner, Student ID Card, dan Topbar profil.
- [x] Opsi penghapusan foto berfungsi dan mengembalikan avatar ke inisial default.
- [x] Seluruh unit dan feature test berjalan 100% Green.
