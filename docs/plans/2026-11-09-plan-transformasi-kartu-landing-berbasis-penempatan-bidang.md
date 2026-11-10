# Rencana Kerja: Transformasi Kartu Kuota Landing Page Berbasis Penempatan / Bidang

**Tanggal**: 9 November 2026  
**Dokumen**: `docs/plans/2026-11-09-plan-transformasi-kartu-landing-berbasis-penempatan-bidang.md`  
**Status**: Selesai Dikerjakan (Completed & Verified)  
**Terkait**:
- Blok Kuota & Penempatan: `resources/views/public/blocks/kuota.blade.php`
- Controller Publik: `app/Http/Controllers/Public/LandingController.php`
- CMS & Preview Controller: `app/Http/Controllers/SuperAdmin/LandingCmsController.php`, `app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php`
- Controller & View Pendaftaran: `app/Http/Controllers/Auth/AuthController.php`, `resources/views/auth/register.blade.php`
- Frontend Style & Interaksi: `resources/css/pages/public/landing.css`, `resources/js/pages/public/landing.js`

---

## 1. Latar Belakang & Kebutuhan Pengguna

Pada implementasi sebelumnya di blok **Kuota & Penempatan** (`#penempatan`), kartu yang ditampilkan di carousel berpusat pada **Jurusan** (misal: "Administrasi Perkantoran", "Desain Grafis (SMK)", "Rekayasa Perangkat Lunak (RPL)"), dengan nama bidang penempatan hanya disematkan sebagai label kecil di bagian bawah.

**Kebutuhan Baru Pengguna**:
Pengguna menginginkan hierarki dan struktur tampilan kartu dibalik agar selaras dengan konsep **Penempatan**:
1. **Entitas Utama Kartu**: Ditampilkan berbasis **Penempatan / Bidang** (misal: "Sekretariat Dinas", "Aplikasi Informatika & E-Government (EGOV)", "Informasi dan Komunikasi Publik (IKP)", "Infrastruktur Teknologi Informasi (ITI)").
2. **Konten di Dalam Kartu**: Menampilkan deskripsi bidang dan daftar **Jurusan yang relevan/sesuai** yang diterima di bidang tersebut sesuai filter jenjang aktif (Siswa SMK atau Mahasiswa D3/S1).
3. **Kalkulasi Kuota & Kapasitas**: Dihitung dari akumulasi kuota jurusan-jurusan yang berada di bawah bidang penempatan terkait untuk periode bulan magang yang dipilih.
4. **Alur Pendaftaran (CTA)**: Tombol *Daftar Sekarang* meneruskan parameter bidang dan jenjang ke halaman pendaftaran (`/register`), sehingga peserta langsung diarahkan ke form dengan pilihan jurusan yang sesuai di bidang penempatan tersebut.

---

## 2. Analisis Arsitektur & Relasi Database

### 2.1 Relasi Bidang & Jurusan
- Database menggunakan tabel `bidang` dan `jurusan` yang terhubung secara many-to-many melalui tabel pivot `bidang_jurusan` (`bidang_id`, `jurusan_id`).
- Setiap model `Bidang` memiliki relasi `$bidang->jurusan()` (`BelongsToMany`).
- Setiap model `Jurusan` memiliki atribut `kategori` (`smk` atau `mahasiswa`).
- Setiap entri di tabel `kuota` terikat dengan `jurusan_id` dan `bulan_magang_id`.

### 2.2 Agregasi Kuota per Bidang Penempatan
Untuk setiap `Bidang`, `BulanMagang`, dan kategori `Jenjang` (`smk` / `mahasiswa`):
- Filter jurusan di bawah bidang: `$jurusanJenjang = $bidang->jurusan->filter(fn($j) => $j->kategori === $jenjang && $j->aktif)`.
- Jika bidang tidak memiliki jurusan untuk jenjang tersebut, bidang tidak perlu ditampilkan pada filter jenjang tersebut.
- Total Kuota Bidang: `$total = $bulan->kuota->whereIn('jurusan_id', $jurusanJenjang->pluck('id'))->sum('jumlah')`.
- Total Terisi Bidang: `$terisi = $bulan->kuota->whereIn('jurusan_id', $jurusanJenjang->pluck('id'))->sum('terisi')`.
- Total Sisa Kursi Bidang: `$sisa = max(0, $total - $terisi)`.
- Status Ketersediaan:
  - Jika bulan ditutup: `StatusKetersediaanEnum::Tutup`.
  - Jika `$sisa > 0`: `StatusKetersediaanEnum::Tersedia` (badge *Tersedia* atau *Tinggal sedikit* jika rasio tersisa $\le 34\%$).
  - Jika `$sisa === 0`: `StatusKetersediaanEnum::Tersedia` dengan kuota penuh (badge *Kuota Penuh* & tombol dinonaktifkan).

---

## 3. Desain Tampilan Kartu Baru (Anatomi Komponen)

```
+---------------------------------------------------------------+
| [Icon Bidang]  Sekretariat Dinas                   [Tersedia] |
|                                                               |
| 4 kursi tersisa dari 4 kursi                                  |
| [=========================== Progress Bar 0% ===============] |
|                                                               |
| Mengelola kesekretariatan dinas, administrasi kepegawaian,    |
| keuangan, tata persuratan, dan kearsipan terpadu.             |
|                                                               |
| Jurusan yang Sesuai:                                          |
| [ Administrasi Perkantoran ]                                  |
|                                                               |
| [                     Daftar Sekarang ➔                     ] |
+---------------------------------------------------------------+
```

### 3.1 Elemen Kartu
1. **Header Kartu**:
   - Icon penempatan bidang yang elegan (SVG vector).
   - Nama Bidang / Penempatan sebagai judul utama (`<h3>`).
   - Badge status dinamis (*Tersedia* / *Tinggal sedikit* / *Kuota Penuh* / *Tutup*).
2. **Statistik Kursi**:
   - Angka besar sisa kursi yang tersedia di bidang tersebut.
   - Total kapasitas dan progress bar visual persentase terisi.
3. **Deskripsi Bidang**:
   - Ringkasan ruang lingkup tugas/pekerjaan di bidang dinas terkait.
4. **Jurusan yang Sesuai (Relevan)**:
   - Label: `Jurusan yang Sesuai:`.
   - Deretan tag/pills nama-nama jurusan aktif yang diterima pada bidang tersebut sesuai jenjang yang sedang dipilih.
5. **Tombol CTA**:
   - Mengarahkan ke form pendaftaran `/register?bulan={id}&bidang={id}&jenjang={smk/mahasiswa}&tipe={mandiri/kelompok}&jumlah={n}`.
   - Jika kuota penuh, tombol dinonaktifkan dengan label *Kuota Penuh*.

---

## 4. Perubahan File & Rencana Teknis

| File | Aksi | Deskripsi Perubahan |
| :--- | :---: | :--- |
| [`app/Http/Controllers/Public/LandingController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Public/LandingController.php) | **Modify** | Siapkan struktur `$matriksBidang` (agregasi kuota per bidang, bulan, dan jenjang) serta koleksi `$bidangSmk` dan `$bidangMahasiswa`. |
| [`app/Http/Controllers/SuperAdmin/LandingCmsController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/SuperAdmin/LandingCmsController.php) | **Modify** | Selaraskan penyiapan data `$matriksBidang` untuk staging preview CMS. |
| [`app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php) | **Modify** | Selaraskan penyiapan data `$matriksBidang` untuk Site Editor preview. |
| [`resources/views/public/blocks/kuota.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/blocks/kuota.blade.php) | **Modify** | Ubah rendering looping kartu carousel dari per jurusan menjadi per **Bidang Penempatan**, lengkap dengan daftar tag jurusan yang sesuai di bawahnya. |
| [`resources/js/pages/public/landing.js`](file:///d:/MAGKOM/MAGKOM/resources/js/pages/public/landing.js) | **Modify** | Sesuaikan fungsi `initKuotaExplorer()` untuk membaca atribut data bidang (`data-bidang-id`, `data-jenjang`, `data-bulan-id`) dan memperbarui link CTA ke `/register` dengan parameter bidang. |
| [`app/Http/Controllers/Auth/AuthController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Auth/AuthController.php) | **Modify** | Tangkap query parameter `bidang` di `showRegistrationForm()` untuk pre-selection bidang dan auto-filter daftar jurusan relevan di form pendaftaran. |
| [`resources/views/auth/register.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/auth/register.blade.php) | **Modify** | Dukung pre-selection dan tampilan ringkasan penempatan bidang jika peserta datang dari kartu bidang landing page. |
| [`resources/css/pages/public/landing.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css) | **Modify** | Polishing visual tag jurusan di dalam kartu penempatan (wrapping rapi, warna badge kontras sesuai palet MAGKOM). |
| [`public/css/landing-modern.css`](file:///d:/MAGKOM/MAGKOM/public/css/landing-modern.css) & [`public/js/landing-interactive.js`](file:///d:/MAGKOM/MAGKOM/public/js/landing-interactive.js) | **Sync** | Sinkronisasi asset kompilasi statis. |

---

## 5. Langkah-Langkah Implementasi Bertahap

- [x] **Langkah 1**: Backend Data Aggregation di `LandingController.php`, `LandingCmsController.php`, dan `LandingCmsSiteEditorController.php`:
  - Hitung matriks kuota agregat per bidang:
    - Akumulasi total kuota, terisi, dan sisa per bulan dan jenjang (`smk` vs `mahasiswa`).
    - Filter hanya bidang yang memiliki jurusan aktif pada jenjang terkait.
- [x] **Langkah 2**: Perbarui Markup Blok Kuota di `resources/views/public/blocks/kuota.blade.php`:
  - Ubah kartu loop utama menjadi iterasi `$bidangSmk` dan `$bidangMahasiswa`.
  - Judul kartu: `$bidang->nama_bidang`.
  - Deskripsi kartu: `$bidang->deskripsi`.
  - Komponen bawah: Menampilkan label `Penempatan: Jurusan yang Sesuai` dan daftar badge jurusan relevan.
  - Atribut dataset: `data-bidang-id="{{ $bidang->id }}"`, `data-jenjang="{{ $jenjang }}"`, `data-bulan-id="{{ $bulan->id }}"`, `data-sisa="{{ $sisa }}"`, `data-total="{{ $total }}"`.
- [x] **Langkah 3**: Penyesuaian Interaksi di `resources/js/pages/public/landing.js`:
  - Perbarui fungsi `updateCardActionButtons()` dan `updateKuotaView()` untuk menyusun link CTA:
    `/register?bulan=${bulanId}&bidang=${bidangId}&jenjang=${currentJenjang}&tipe=${currentRegMode}&jumlah=${currentMemberCount}`.
- [x] **Langkah 4**: Dukungan Query `bidang` pada Formulir Registrasi di `AuthController.php` & `register.blade.php`:
  - Membaca parameter `bidang` dari URL.
  - Memfilter atau mem-preselect pilihan jurusan yang sesuai dengan bidang tersebut.
- [x] **Langkah 5**: Build Asset Vite & Sinkronisasi:
  - Jalankan `npm run build`.
  - Salin hasil ke `public/css/` dan `public/js/`.
  - Jalankan `artisan view:clear`.
- [x] **Langkah 6**: Pengujian Otomatis & Quality Gates:
  - Jalankan test suite PHP 8.3:
    `artisan test tests/Feature/LandingPageTest.php tests/Feature/LandingPageFaqRedesignTest.php tests/Feature/LandingCmsStagingTest.php tests/Feature/LandingCmsVisualBuilderAndThemeTest.php tests/Feature/LandingPage/FinalQualityAssuranceTest.php tests/Feature/LandingPage/InspectorDesignSystemTest.php tests/Feature/PendaftaranPesertaTest.php tests/Feature/PesertaPendaftaranFlowTest.php tests/Feature/BidangJurusanMappingTest.php`
  - Jalankan Laravel Pint: `./vendor/bin/pint --test`.

---

## 6. Kriteria Selesai (Definition of Done)

1. [x] Kartu utama pada blok Kuota menampilkan **Nama Penempatan / Bidang** sebagai judul utama.
2. [x] Di dalam setiap kartu bidang, tercantum daftar **Jurusan yang Sesuai** yang diterima di bidang tersebut sesuai jenjang yang dipilih (SMK atau Mahasiswa).
3. [x] Angka kuota, sisa kursi, dan status ketersediaan pada kartu bidang terhitung akurat secara real-time dari relasi database.
4. [x] Klik tombol *Daftar Sekarang* meneruskan informasi bidang dan periode magang ke form pendaftaran `/register`.
5. [x] Seluruh rangkaian automated test suite (72 tests, 507 assertions) lulus 100% dan kode mematuhi standar PSR-12 / Pint.

