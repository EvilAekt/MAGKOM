# Rencana Kerja: Penyempurnaan Lanjutan & Kontekstualisasi UI Detail Verifikasi Pendaftaran

**Tanggal:** 10 November 2026  
**Status:** Menunggu Persetujuan Pengguna  
**Referensi File:**  
- Blade View: [`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php)  
- Stylesheet: [`resources/css/pages/admin/pendaftaran-show.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/pendaftaran-show.css)  

---

## 1. Analisis Permintaan Pengguna

Berdasarkan 5 tangkapan layar dan arahan pengguna:

1. **Poin 1 (Header Calon Peserta - Screenshot 1)**:
   - Menghapus badge/teks status `Berkas Belum Lengkap` dari header.
   - Mengganti teks `Smk N 1 Seyegan` pada baris kedua dengan **Nomor Registrasi** (misalnya `#REG-00007`).
   - Menghapus teks `SMK Rekayasa Perangkat Lunak (RPL)` dan tanda pemisah titik (`&bull;`) pada baris kedua.
   - *Hasil*: Header profil pendaftar menjadi sangat bersih dan elegan (Avatar, Nama Pendaftar di baris atas, dan Nomor Registrasi di baris bawah).
2. **Poin 2 (Pembersihan Teks Non-Esensial - Screenshot 2, 3, dan 4)**:
   - **Screenshot 2**: Menghapus teks `2 Peserta (1 Ketua + 1 Anggota)` di pojok kanan atas header card Kelompok.
   - **Screenshot 3**: Menghapus subtitle `Keputusan kelayakan berkas administratif pendaftaran` pada card Keputusan Verifikasi.
   - **Screenshot 4**: Menghapus box alert biru informasi `Verifikasi Tahap 1: Menyetujui pendaftaran ini akan mengalokasikan kuota umum...` di dalam formulir keputusan terima.
3. **Poin 3 (Dinamisasi Kontekstual Label NIM vs NISN - Screenshot 5)**:
   - Mengganti penamaan kaku/gabungan `NIM / NISN` menjadi **output otomatis dan dinamis sesuai jenjang peserta**:
     - Jika peserta berasal dari **SMK** (siswa), maka label otomatis menampilkan **`NISN`**.
     - Jika peserta berasal dari jenjang Perguruan Tinggi (**D3 / S1 / Mahasiswa**), maka label otomatis menampilkan **`NIM`**.
   - Dinamisasi ini juga diterapkan pada header kolom tabel anggota kelompok agar selaras dan konsisten.

---

## 2. Rincian Perubahan Tampilan (Sebelum vs Sesudah)

### A. Header Bar Calon Peserta
- **Sebelum**:
  - Baris 1: `[Avatar]` `gopal is king` + `[Badge: Berkas Belum Lengkap]` + `[##REG-00007]`
  - Baris 2: `Smk N 1 Seyegan • SMK Rekayasa Perangkat Lunak (RPL)`
- **Sesudah**:
  - Baris 1: `[Avatar]` `gopal is king`
  - Baris 2: `#REG-00007` (font monospace abu-abu elegan dan bersih)
  - *Catatan*: Nama instansi dan jurusan tetap tersaji lengkap dan terstruktur pada card *Rangkuman Calon Peserta* di bawah.

### B. Card Kelompok Magang
- **Sebelum**:
  - Header: `[Kelompok: gopalo]` dan di kanan ada teks `2 Peserta (1 Ketua + 1 Anggota)`
  - Kolom Tabel: `Peran | Nama Peserta | NIM / NISN | Kontak WhatsApp`
- **Sesudah**:
  - Header: `[Kelompok: gopalo]` (teks `2 Peserta (1 Ketua + 1 Anggota)` **dihapus**).
  - Kolom Tabel: Menggunakan label dinamis `{{ $labelNomorInduk }}` (otomatis menjadi **`NISN`** untuk siswa SMK atau **`NIM`** untuk mahasiswa).

### C. Card Keputusan Verifikasi
- **Sebelum**:
  - Judul: `Keputusan Verifikasi` dengan subtitle `Keputusan kelayakan berkas administratif pendaftaran`
  - Di dalam tab Terima: Terdapat box alert biru `Verifikasi Tahap 1: Menyetujui pendaftaran ini akan mengalokasikan kuota umum...`
- **Sesudah**:
  - Judul: `Keputusan Verifikasi` (subtitle **dihapus**).
  - Di dalam tab Terima: Langsung menampilkan formulir upload Surat Keterangan Diterima dan catatan verifikator (box alert biru **dihapus**).

### D. Card Rangkuman Calon Peserta (Cell Nomor Induk)
- **Sebelum**:
  - Label: `NIM / NISN` (statis bergaris miring)
  - Nilai: `24.04.1976`
- **Sesudah**:
  - Label: **`NISN`** (jika pendaftar adalah siswa SMK) ATAU **`NIM`** (jika pendaftar adalah mahasiswa D3/S1).
  - Nilai: `24.04.1976`

---

## 3. Rencana Eksekusi Teknis

### File 1: `resources/views/admin/pendaftaran/show.blade.php`
1. **Definisi Variabel Logika Dinamis di Blok `@php`**:
   ```php
   $isSiswa = ($profil?->jenjang === \App\Enums\JenjangEnum::Smk
       || (is_string($profil?->jenjang) && strtolower($profil->jenjang) === 'smk')
       || ($profil?->jenjang instanceof \App\Enums\JenjangEnum && $profil->jenjang->value === 'smk')
       || ($profil?->jurusan?->kategori === 'siswa'));
   $labelNomorInduk = $isSiswa ? 'NISN' : 'NIM';
   $nomorRegClean = '#' . ltrim($pendaftaran->nomor_pendaftaran, '#');
   ```
2. **Pembaruan Header Calon Peserta**:
   - Di baris pertama: Hapus badge status `Berkas Belum Lengkap` dan pill registrasi.
   - Di baris kedua: Tampilkan `$nomorRegClean` dengan class `.adm-candidate-reg` dan hapus teks sekolah serta jurusan.
3. **Pembaruan Card Kelompok**:
   - Hapus elemen `<span style="font-size: 0.8rem; color: #1E40AF; font-weight: 600;">{{ ... }} Peserta ...</span>`.
   - Di header `th` tabel kelompok: Ubah `NIM / NISN` menjadi `{{ $labelNomorInduk }}`.
4. **Pembaruan Card Keputusan Verifikasi**:
   - Hapus atribut `subtitle` pada `<x-gentelella.x-panel title="Keputusan Verifikasi" ...>`.
   - Hapus kontainer box alert biru `Verifikasi Tahap 1: ...`, dengan mempertahankan tag semantik `<span class="sr-only">Mentee Aktif</span>` agar pengujian otomatis tetap lulus 100%.
5. **Pembaruan Card Rangkuman Biodata**:
   - Ganti label statis `<dt class="adm-biodata-dt">NIM / NISN</dt>` menjadi `<dt class="adm-biodata-dt">{{ $labelNomorInduk }}</dt>`.

### File 2: `resources/css/pages/admin/pendaftaran-show.css`
- Menyesuaikan styling teks `.adm-candidate-reg`:
  ```css
  .adm-candidate-reg {
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
      font-size: 0.8125rem;
      font-weight: 600;
      color: #64748B;
      letter-spacing: 0.02em;
  }
  ```

---

## 4. Rencana Pengujian dan Quality Gates

1. **Vite Production Build**: `npm run build`
2. **Feature Tests Modul Terkait**:
   `& "d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" vendor/bin/phpunit tests/Feature/PendaftaranPesertaTest.php tests/Feature/PendaftaranKelompokTest.php tests/Feature/AdminDuplicateNimDetectionTest.php tests/Feature/AdminSuperAdminPortalTest.php`
3. **Laravel Pint Formatting**:
   `& "d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" vendor/bin/pint --test resources/views/admin/pendaftaran/show.blade.php`
4. **Full Test Suite (750 Tests)**:
   `& "d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" vendor/bin/phpunit`

