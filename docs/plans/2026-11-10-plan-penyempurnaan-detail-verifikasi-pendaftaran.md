# Rencana Kerja: Penyempurnaan & Penyelarasan UI Detail Verifikasi Pendaftaran Peserta

**Tanggal:** 10 November 2026  
**Status:** Menunggu Persetujuan Pengguna  
**Referensi File:**  
- Blade View: [`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php)  
- Stylesheet: [`resources/css/pages/admin/pendaftaran-show.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/pendaftaran-show.css)  

---

## 1. Analisis 8 Poin Permintaan Pengguna

Berdasarkan 5 tangkapan layar dan arahan pengguna:

1. **Poin 1 (Screenshot 1)**: Menghapus chart/banner peringatan berwarna oranye (`SEC-15 / BR-17`) di bagian atas halaman.
2. **Poin 2 (Screenshot 2)**: Menghapus badge ungu `Kelompok (2 Peserta)` pada header profil calon peserta (karena di bawahnya sudah ada panel khusus kelompok). Menata tata letak header agar hanya memuat: Nama Calon Peserta, Badge Status (`Berkas Belum Lengkap`), dan Badge Nomor Registrasi (`#REG-00007`) secara rapi dan proporsional.
3. **Poin 3 (Rekomendasi Terminologi Tim vs Kelompok)**:
   - **Rekomendasi Desain**: Gunakan istilah tunggal **`Kelompok`** (atau `Kelompok Magang`).
   - **Alasan**:
     - *Kesesuaian Regulasi & Skema Data*: Seluruh PRD, SOP DISKOMINFO, dan database model menggunakan `KelompokMagang` (bukan tim).
     - *Konteks Akademik*: Lembaga pendidikan (SMK/Universitas) mengeluarkan Surat Pengantar untuk "Kelompok PKL/Magang".
     - *Menghilangkan Ambiguitas*: Menghapus tanda garis miring `Tim / Kelompok` menjadi satu istilah baku yang konsisten di seluruh aplikasi.
4. **Poin 4 (Screenshot 4)**: Menghapus teks deskripsi panjang (*"Pemberkasan ini diunggah secara kolektif untuk seluruh anggota tim di bawah ini..."*) agar tampilan kartu kelompok menjadi padat, ringkas, dan langsung fokus ke data tabel.
5. **Poin 5**: Menghapus teks subtitle `0 Berkas Persyaratan Terlampir` pada Card Verifikasi Berkas dan teks subtitle `Durasi Pengajuan: 4 Bulan` pada Card Rangkuman Calon Peserta.
6. **Poin 6 (Screenshot 5)**: Mengubah label pada kartu biodata dari `NOMOR INDUK / JENJANG` menjadi **`NIM / NISN`**, menampilkan nomor identitas secara bersih, dan **menghapus badge `SMK`** di sisi kanannya (karena jenjang SMK sudah tercantum di header profil).
7. **Poin 7**: Mengganti icon/logo default info `(i)` pada Card **Rekam Jejak Pendaftaran** menjadi icon audit trail / history berputar (*clock-rewind / activity history*) yang modern dan profesional.
8. **Poin 8**: Menyelaraskan seluruh styling ke dalam `resources/css/pages/admin/pendaftaran-show.css` tanpa menulis inline CSS/JS di Blade, mematuhi WCAG 2.1 AA dan aturan [`AGENTS.md`](file:///d:/laragon/www/MAGKOM/.agents/rules/AGENTS.md).

---

## 2. Rincian Perubahan Tata Letak (Sebelum vs Sesudah)

### A. Header Bar Calon Peserta
- **Sebelum**:
  - Baris 1: `gopal is king` + [Badge: Berkas Belum Lengkap] + [Badge Ungu: Kelompok (2 Peserta)]
  - Baris 2: `Smk N 1 Seyegan • SMK Rekayasa Perangkat Lunak (RPL) • #REG-00007`
- **Sesudah**:
  - Baris 1: `gopal is king` + [Badge: Berkas Belum Lengkap] + [Pill Monospace: #REG-00007]
  - Baris 2: `Smk N 1 Seyegan • SMK Rekayasa Perangkat Lunak (RPL)`
  - *Kelebihan*: Rapi, tidak berjejal, tidak ada pengulangan kata kelompok, dan nomor registrasi terlihat tegas sebagai kode identitas sistem.

### B. Info Kelompok Magang
- **Sebelum**:
  - Header: Badge `Tim / Kelompok` + Judul Kelompok + Subteks `2 Peserta (1 Ketua + 1 Anggota)`
  - Deskripsi: `Pemberkasan ini diunggah secara kolektif untuk seluruh anggota tim di bawah ini...` (paragraf panjang)
  - Tabel: Peran `Ketua Tim` dan `Anggota`
- **Sesudah**:
  - Header: Badge tunggal `Kelompok` + Nama Kelompok (`gopalo`) + Subteks kanan `2 Peserta (1 Ketua + 1 Anggota)`
  - Deskripsi panjang **dihapus**.
  - Tabel: Peran selaras `Ketua Kelompok` dan `Anggota`.

### C. Judul & Subtitle Panel
- Panel Dokumen: Title `Verifikasi Berkas Calon Peserta` (subtitle `X Berkas Persyaratan Terlampir` **dihapus**).
- Panel Rangkuman: Title `Rangkuman Calon Peserta` (subtitle `Durasi Pengajuan: X Bulan` **dihapus**).

### D. Rangkuman Biodata (Cell NIM/NISN)
- **Sebelum**:
  - Label: `NOMOR INDUK / JENJANG`
  - Konten: `24.04.1976` + Badge `SMK` di pojok kanan
- **Sesudah**:
  - Label: `NIM / NISN`
  - Konten: `24.04.1976` (font monospace presisi, badge `SMK` **dihapus**)

### E. Icon Card Rekam Jejak Pendaftaran
- **Sebelum**: Icon default `fa fa-history` yang tidak terdaftar di SVG library sehingga menghasilkan icon fallback `(i)` info biasa.
- **Sesudah**: Menggunakan icon SVG `clock` resmi dari library `<x-sa.icon name="clock" :size="18" />` (atau SVG putaran jarum jam riwayat) yang tampil tajam dan profesional.

---

## 3. Rencana Eksekusi Kode

### File 1: `resources/views/admin/pendaftaran/show.blade.php`
1. Hapus blok `{{-- SEC-15: Indikator Peringatan Deteksi NIM/NISN Ganda Lintas Akun --}}` (div oranye).
2. Di `.adm-identity-left`:
   - Hapus badge ungu `Kelompok (X Peserta)`.
   - Pindahkan `$pendaftaran->nomor_pendaftaran` ke baris pertama di samping badge status sebagai `.adm-badge-reg-pill`.
   - Rapikan baris kedua subtitle agar hanya menampilkan institusi dan jurusan.
3. Di blok `Info Kelompok Magang`:
   - Ganti teks badge `Tim / Kelompok` menjadi `Kelompok`.
   - Hapus paragraf `<p>` deskripsi kolektif.
   - Ganti label `Ketua Tim` di tabel menjadi `Ketua Kelompok`.
4. Di panel `Verifikasi Berkas Calon Peserta`:
   - Hapus atribut `subtitle`.
5. Di panel `Rangkuman Calon Peserta`:
   - Hapus atribut `subtitle`.
   - Ubah label `NOMOR INDUK / JENJANG` menjadi `NIM / NISN`.
   - Hapus badge jenjang di sebelah kanan NIM/NISN.
6. Di card `Rekam Jejak Pendaftaran`:
   - Ubah atribut icon menjadi `clock` (atau inline SVG audit trail).

### File 2: `resources/css/pages/admin/pendaftaran-show.css`
1. Menambahkan class `.adm-badge-reg-pill` untuk nomor registrasi di samping badge status:
   - Font monospace, background `#F1F5F9`, border `#E2E8F0`, warna teks `#475569`, border-radius 9999px.
2. Menyesuaikan padding header kelompok agar proporsional tanpa paragraf deskripsi.

---

## 4. Quality Gates & Verifikasi

1. **Vite Production Build**: `npm run build`
2. **Feature Tests Modul Pendaftaran Peserta (PHP 8.3)**:
   `& "d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" vendor/bin/phpunit tests/Feature/PendaftaranPesertaTest.php tests/Feature/AdminEksporLaporanPendaftaranPopoverTest.php tests/Feature/PendaftaranKelompokTest.php`
3. **Laravel Pint**:
   `& "d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" vendor/bin/pint --test resources/views/admin/pendaftaran/show.blade.php`
4. **Full Test Suite (750 Tests)**:
   `& "d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" vendor/bin/phpunit`

