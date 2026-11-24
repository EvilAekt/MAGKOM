# [TASK / PRD SENIOR DEV] Redesign Halaman Detail Penempatan Peserta Anggota Kelompok (Single-Participant Focus with Team Context)

> **Ticket ID**: `TASK-MAGKOM-SS-005`  
> **Author**: Senior Lead Developer (Antigravity)  
> **Assignee**: Junior Full-Stack Developer / Autonomous Coding AI (Gemini 3.8 Flash)  
> **Status**: `PROPOSED - WAITING USER APPROVAL`  
> **Target Release**: Sprint MagKom v1.1.5  
> **Tech Stack**: Laravel 10.x, PHP 8.3, Blade, Vanilla CSS (Vite Scoped), Vanilla JS (ES Modules)

---

## 1. Executive Summary & Problem Statement

### 1.1 Latar Belakang & Masalah Eksisting
Sebelumnya, ketika Admin mengklik tombol `[ Detail ]` pada baris peserta anggota kelompok (misal: *Farhan Aditya Pratama* dari *Tim CSIRT & Keamanan Siber Sleman*) di tabel Surat Selesai Magang, sistem menampilkan seluruh workspace kelompok lengkap dengan tabel seluruh anggota tim (*Daftar Anggota Kelompok: Tim CSIRT & Keamanan Siber Sleman (3 Orang)*) di bagian bawah.

Tampilan tersebut menimbulkan redundansi dan kebingungan:
1. **Redundansi Informasi**: Tabel anggota kelompok sudah ada di halaman depan Surat Selesai Magang (Tab Kelompok). Menampilkannya kembali di halaman detail penempatan perorangan adalah pemborosan ruang (*clutter*).
2. **Kehilangan Fokus Individu**: Admin mengklik `[ Detail ]` untuk memeriksa atau mengelola **peserta spesifik tersebut** (NIM, institusi, jurusan, peran di tim, status surat, dan unduhan berkas), bukan mengulang tampilan grup secara umum.

### 1.2 Solusi Desain Baru (The Vision)
Halaman Detail Penempatan untuk peserta kelompok (`admin.penempatan.show`) diubah menjadi **Single-Participant Focus with Team Context**:
- **Subjek Utama**: Peserta individu yang dipilih (Foto profil, Nama Lengkap, NIM/NISN, Institusi, Jurusan, Status Magang).
- **Konteks Tim**: Diberikan penanda yang jelas dan elegan bahwa peserta ini adalah bagian dari suatu kelompok (misal: badge `Kelompok: Tim CSIRT & Keamanan Siber Sleman` dan badge peran `Ketua Kelompok` atau `Anggota Tim`).
- **Grid 2-Kolom Terfokus**:
  - **Kolom Kiri**: *Informasi Penempatan Peserta* (Nama, NIM, Tim & Peran, Bidang, Pembimbing Lapangan, Periode, Tanggal Selesai, Status Surat Selesai & tombol unduh jika sudah terbit).
  - **Kolom Kanan**: *Formulir Unggah Surat Selesai* (Custom File Picker PDF Maks 2MB + Tombol Sleman Navy) dengan keterangan bahwa surat ini akan tersinkronisasi ke akun peserta dan rombongan timnya.
- **Bagian Bawah (Bottom Section)**: **TIDAK ADA LAGI** tabel `Daftar Anggota Kelompok (3 Orang)`. Halaman berakhir bersih, rapi, dan terarah.

---

## 2. Perbandingan Visual (Before vs After Wireframe)

### 2.1 Kondisi Saat Ini (Before - Redundant Group View)
```
+-----------------------------------------------------------------------------------+
| Workspace Penerbitan Berkas Resmi Rombongan Kelompok                              |
+-----------------------------------------------------------------------------------+
| [👥] Tim CSIRT & Keamanan Siber Sleman                            [ BELUM DIUNGGAH ]|
|      Universitas Negeri Yogyakarta • 3 Anggota Terdaftar • Bidang: ITI            |
+-----------------------------------------------------------------------------------+
| [ INFORMASI PENEMPATAN KELOMPOK ]         | [ FORMULIR UNGGAH SURAT SELESAI ]     |
| - Nama Kelompok: Tim CSIRT...             | - Upload File PDF (Maks 2MB)          |
| - Bidang: ITI                             | - [ Pilih Berkas PDF ]                |
| - Pembimbing: Sri Wahyuni                 | - [ Unggah Surat Resmi ]              |
| - Periode: 24 Agt - 22 Nov 2026           |                                       |
| - Status: Menunggu Pengunggahan           |                                       |
+-------------------------------------------+---------------------------------------+
| ⚠️ REDUNDAN:                                                                       |
| Daftar Anggota Kelompok: Tim CSIRT & Keamanan Siber Sleman (3 Orang)              |
| +----+----------------------+-----------------+----------------+----------------+ |
| | NO | NAMA ANGGOTA         | PERAN TIM       | NIM/INSTITUSI  | AKSI INDIVIDUAL| |
| +----+----------------------+-----------------+----------------+----------------+ |
| | 1  | Farhan (Sedang Dilihat)| Ketua Kelompok| 21520244001    | [ Halaman Ini ]| |
| | 2  | Maya Safira Kusuma   | Anggota         | 21520244002    | [ Detail ]     | |
| | 3  | Gilang Pamungkas     | Anggota         | 21520244003    | [ Detail ]     | |
| +----+----------------------+-----------------+----------------+----------------+ |
+-----------------------------------------------------------------------------------+
```

### 2.2 Kondisi Baru yang Diinginkan (After - Clean Participant Focus)
```
+-----------------------------------------------------------------------------------+
| Detail Penempatan Peserta: Farhan Aditya Pratama          [< Kembali ke Daftar]   |
| Workspace Penerbitan Berkas Resmi Peserta • Dinas Kominfo Kabupaten Sleman        |
+-----------------------------------------------------------------------------------+
| [Foto] Farhan Aditya Pratama  [ Selesai Magang ] [ Tim: Tim CSIRT... ] [ Ketua ] |
|        Universitas Negeri Yogyakarta • NIM/NISN: 21520244001 • Teknik Informatika |
|                                                            [ CHIP STATUS BERKAS ] |
+-----------------------------------------------------------------------------------+
| [ INFORMASI PENEMPATAN PESERTA ]          | [ FORMULIR UNGGAH SURAT SELESAI ]     |
|                                           |                                       |
| NAMA PESERTA                              | Unggah berkas resmi Surat Keterangan  |
| Farhan Aditya Pratama                     | Selesai Magang format PDF (maks 2MB). |
|                                           |                                       |
| ROMBONGAN KELOMPOK                        | +-----------------------------------+ |
| Tim CSIRT & Keamanan Siber Sleman         | | [Pilih Berkas PDF] Belum ada file | |
| (Peran: Ketua Kelompok)                   | +-----------------------------------+ |
|                                           |                                       |
| BIDANG PENEMPATAN                         | +-----------------------------------+ |
| Infrastruktur Teknologi Informasi (ITI)   | | [▲] Unggah Surat Resmi (Sleman)   | |
|                                           | +-----------------------------------+ |
| PEMBIMBING LAPANGAN                       |                                       |
| Sri Wahyuni, S.I.Kom                      | Catatan: Berkas yang diunggah akan    |
|                                           | otomatis tersinkronisasi ke seluruh   |
| PERIODE MAGANG                            | anggota rombongan tim ini.            |
| 24 Agt 2026 s/d 22 Nov 2026               |                                       |
|                                           |                                       |
| TANGGAL SELESAI                           |                                       |
| 22 November 2026                          |                                       |
|                                           |                                       |
| [BOX STATUS SURAT SELESAI]                |                                       |
| Status: Menunggu Berkas / Sudah Terbit    |                                       |
| [ Unduh Salinan Surat Selesai (PDF) ]     |                                       |
+-------------------------------------------+---------------------------------------+
|                                                                                   |
|  (TIDAK ADA LAGI TABEL ANGGOTA KELOMPOK DI SINI - HALAMAN BERSIH & FOKUS 100%)    |
|                                                                                   |
+-----------------------------------------------------------------------------------+
```

---

## 3. Spesifikasi Fungsional & Komponen Tampilan

### 3.1 Header Identitas Peserta (Top Identity Card)
1. **Avatar/Foto Profil**:
   - Jika peserta memiliki foto profil terunggah, render gambar avatar `foto_url`.
   - Jika tidak ada, render inisial 2 huruf peserta (*Farhan Aditya Pratama* -> `FA`).
2. **Nama Lengkap & Atribut Akademik**:
   - Nama peserta ukuran 1.25rem bold warna teks Sleman Charcoal (`#0F172A`).
   - Sub-teks: Institusi kampus/sekolah &bull; NIM/NISN &bull; Program Studi/Jurusan.
3. **Pill & Badge Konteks Tim**:
   - **Badge Tim**: `<span class="team-context-pill"><svg>...</svg> Tim: {nama_kelompok}</span>`
   - **Badge Peran**: `<span class="role-badge-pill ketua">Ketua Kelompok</span>` atau `<span class="role-badge-pill anggota">Anggota Tim</span>`.
4. **Status Chip Kanan**:
   - Status penerbitan berkas:
     - Hijau: `SURAT TERBIT` / `TERSIKRONSASI DARI TIM` (jika file surat sudah ada di local disk).
     - Kuning/Oranye: `BELUM DIUNGGAH` (jika file surat belum tersedia).

### 3.2 Kolom Kiri: Card Informasi Penempatan Peserta
Card styling menggunakan token `focused-card info-card`:
- **Header Card**: `Informasi Penempatan Peserta`
- **Field List**:
  1. `Nama Peserta`: Nama lengkap peserta aktif.
  2. `Rombongan Tim`: Nama kelompok magang (`$kelompok->nama_kelompok`) disertai peran tim peserta.
  3. `Bidang Penempatan`: Nama bidang penempatan dinas.
  4. `Pembimbing Lapangan`: Nama pembimbing lapangan kedinasan.
  5. `Periode Magang`: Tanggal mulai s/d tanggal selesai (format lokal `d M Y`).
  6. `Tanggal Selesai`: Tanggal selesai magang (format lokal `d F Y`).
- **Box Status Surat Selesai**:
  - Background alert dinamis (`published-status-box`):
    - Jika sudah terbit: warna background hijau lembut (`#ECFDF5`), teks status `Sudah Diterbitkan & Tersedia di Dashboard`, meta waktu upload, dan tombol hijau `[ Unduh Salinan Surat Selesai (PDF) ]`.
    - Jika belum terbit: background kuning lembut (`#FFFBEB`), teks status `Menunggu Pengunggahan Berkas`.

### 3.3 Kolom Kanan: Card Formulir Unggah Surat Selesai
Card styling menggunakan token `focused-card upload-card`:
- **Header Card**:
  - Judul: `Formulir Unggah Surat Selesai` (atau `Perbarui / Ganti Berkas Surat`).
  - Deskripsi: `Unggah berkas resmi Surat Keterangan Selesai Magang format PDF dengan ukuran maksimal 2MB.`
- **Interaksi Upload**:
  - Tombol custom file picker: `[ Pilih Berkas PDF ]` yang men-trigger input file tersembunyi (`#file_surat_kel_show`).
  - Preview file: Wadah teks abu-abu yang menampilkan nama file terpilih (default: `Belum ada berkas PDF dipilih (Maks 2.0 MB)`).
  - Tombol Submit Sleman Navy (`#0F3974` hover `#0c2e5e`): `Unggah Surat Resmi` / `Simpan Perubahan Berkas`.
  - Helper note di bawah: `Catatan: Berkas yang diunggah akan otomatis terdistribusi ke akun peserta ini dan seluruh anggota rombongan tim.`
- **Guardrail Keamanan**:
  - Hanya dapat diunggah jika status penempatan peserta bernilai `selesai`.
  - SuperAdmin bersifat *Read-Only* (form unggah dinonaktifkan/disembunyikan).

### 3.4 Penghapusan Tabel Anggota Kelompok (Crucial)
- File `_show_kelompok.blade.php` **TIDAK LAGI** me-render markup `<div class="dedicated-members-panel">...</div>`.
- Seluruh markup tabel anggota, perulangan `@foreach($rekanPenempatans as ...)`, dan kolom aksi individual dihilangkan dari halaman ini.

---

## 4. Arsitektur Data & Logic Mapping

### 4.1 Data Variabel di View
```php
@php
    $peserta = $penempatan->peserta;
    $profil = $peserta?->profil;
    $kelompok = $penempatan->pendaftaran?->kelompokMagang;
    $isKetua = $kelompok && (int) $kelompok->ketua_user_id === (int) $peserta?->id;
    $hasSurat = ! empty($penempatan->file_surat_selesai) && \Illuminate\Support\Facades\Storage::disk('local')->exists($penempatan->file_surat_selesai);
    $statusPenempatanVal = $penempatan->status instanceof \App\Enums\StatusPenempatanEnum 
        ? $penempatan->status->value 
        : (string) $penempatan->status;
@endphp
```

### 4.2 Endpoint & Routes yang Digunakan
1. **Action Form Upload**:
   - Route: `route('admin.penempatan.surat-selesai.upload', $penempatan)`
   - Method: `POST` (dengan `@csrf` dan `enctype="multipart/form-data"`).
   - Controller: `Admin\PenempatanController::uploadSuratSelesai()`
   - Logic: Karena `$penempatan->isKelompok()` bernilai `true`, service backend otomatis mendistribusikan file surat ini ke seluruh anggota kelompok yang penempatannya berstatus `selesai`.
2. **Action Unduh Berkas**:
   - Route: `route('admin.penempatan.surat-selesai.unduh', $penempatan)`
   - Method: `GET` (Secure Stream Response dari local disk).
3. **Tombol Navigasi Kembali**:
   - Link: `route('admin.surat-selesai.index', ['tab' => 'kelompok', 'fokus_kelompok' => $kelompok?->id])` jika referer datang dari surat selesai, atau fallback ke `route('admin.penempatan.index')`.

---

## 5. Rencana Langkah Implementasi (Implementation Steps)

### Step 1: Modifikasi Partial View Blade
- **Target File**: `resources/views/admin/penempatan/partials/_show_kelompok.blade.php`
- **Tugas**:
  1. Ubah Top Identity Bar agar fokus ke **Peserta Tersebut** (`$peserta->nama`, NIM, Institusi, Jurusan) dengan badge kelompok (`$kelompok->nama_kelompok`) dan badge peran (`Ketua Kelompok` / `Anggota Tim`).
  2. Perbarui Kolom Kiri Grid: Tambahkan baris informasi nama peserta dan timnya secara proporsional.
  3. Perbarui Kolom Kanan Grid: Formulir unggah compact dengan custom file picker.
  4. Hapus total komponen `dedicated-members-panel` (tabel anggota kelompok).
- **Aturan Ketat**: DILARANG menulis `<style>` atau `<script>` di Blade.

### Step 2: Sinkronisasi Header Halaman Utama
- **Target File**: `resources/views/admin/penempatan/show.blade.php`
- **Tugas**:
  1. Pastikan breadcrumb dan header judul menampilkan nama peserta dengan benar: `Detail Penempatan Peserta: {{ $peserta?->nama }}`.
  2. Tombol kembali `[ Kembali ]` mengarahkan pengguna kembali ke workspace surat selesai kelompok atau daftar penempatan.

### Step 3: Verifikasi JavaScript File Picker
- **Target File**: `resources/js/pages/admin/penempatan-show.js`
- **Tugas**:
  1. Pastikan event listener untuk `#file_surat_kel_show` dan `#previewFileNameKelShow` tetap terpasang dengan baik.
  2. Validasi client-side (maksimal 2.0 MB dan format PDF) berjalan tanpa alert browser jadul (gunakan notifikasi UI ramah pengguna jika file melebihi batas).

### Step 4: Penyesuaian Automated Feature Tests
- **Target File**: `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php`
- **Tugas**:
  1. Perbarui method `test_admin_melihat_indikator_kelompok_pada_halaman_detail_penempatan()`:
     - `assertSee($this->andi->nama)`: Memastikan nama peserta individu terlihat.
     - `assertSee('Tim Dev Diskominfo')`: Memastikan nama kelompok terlihat sebagai konteks.
     - `assertSee('Ketua Kelompok')`: Memastikan peran terlihat.
     - `assertSee('Informasi Penempatan Peserta')`: Memastikan card penempatan peserta ada.
     - `assertSee('Formulir Unggah Surat Selesai')`: Memastikan form unggah ada.
     - `assertDontSee('Daftar Anggota Kelompok: Tim Dev Diskominfo')`: Memastikan tabel anggota **TIDAK LAGI MUNCUL**.
     - `assertDontSee('Tabel khusus rincian anggota kelompok')`: Memastikan keterangan tabel hilang.

### Step 5: Eksekusi Quality Gate
- Jalankan pemeriksaan formatting Pint:
  ```powershell
  F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe ./vendor/bin/pint --test
  ```
- Jalankan Feature Test lengkap:
  ```powershell
  F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe artisan test tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php
  ```
- Jalankan asset bundling Vite:
  ```powershell
  npm run build
  ```

---

## 6. Analisis Risiko & Mitigasi

| Potensi Risiko | Tingkat | Dampak | Mitigasi Pencegahan |
| :--- | :---: | :--- | :--- |
| **Distribusi Surat Tim Terganggu** | Rendah | Admin mengunggah surat dari halaman peserta, takutnya tidak terdistribusi ke anggota lain. | Method backend `uploadSuratSelesai()` sudah otomatis mendeteksi `$penempatan->isKelompok()` dan menyinkronkan file ke seluruh anggota kelompok. Logika backend tidak diubah. |
| **Breaking Test di Penempatan** | Sedang | Test lama masih mengecek string `Daftar Anggota Kelompok:`. | Test diselaraskan pada Step 4 agar memverifikasi tampilan peserta individu dan memastikan tabel anggota `assertDontSee`. |
| **Navigasi Terputus** | Rendah | Admin tidak tahu cara kembali ke daftar kelompok. | Sediakan tombol `[< Kembali ke Daftar Kelompok]` di header atas halaman yang membawa admin langsung kembali ke tab kelompok. |

---

## 7. Definition of Done (DoD)

- [x] Halaman `admin.penempatan.show` saat peserta kelompok dibuka menampilkan data identitas individual peserta (Foto, Nama, NIM, Institusi, Jurusan).
- [x] Terdapat penanda yang jelas berupa badge kelompok (*Tim CSIRT & Keamanan Siber Sleman*) dan badge peran (*Ketua Kelompok* / *Anggota Tim*).
- [x] Grid 2-kolom memuat *Informasi Penempatan Peserta* dan *Formulir Unggah Surat Selesai* (dengan file picker modern).
- [x] **Tabel Daftar Anggota Kelompok (3 Orang) sama sekali TIDAK dirender** di halaman ini.
- [x] Tidak ada tag `<style>` atau `<script>` yang disisipkan di file Blade.
- [x] Seluruh aset CSS dan JS terkelola rapi melalui Vite bundle.
- [x] Semua pengujian PHPUnit lulus (`100% PASS`) di runtime PHP 8.3.
- [x] `npm run build` sukses tanpa warning / error.
- [x] Working tree git terjaga rapi tanpa file artefak yang tercecer.
