# Rencana Kerja: Pembaruan Master Data Bidang dan Pemetaan Jurusan yang Dibutuhkan

**Tanggal Pembuatan**: 19 Oktober 2026  
**Status**: ✅ Selesai Diimplementasikan & Lolos Seluruh Pengujian  
**Branch**: `test-1`  
**Referensi Kebutuhan**:
1. **IKP** (Informasi dan Komunikasi Publik):
   1. Statistik
   2. Ilmu Komunikasi
   3. Desain Grafis (Grafik Desain)
   4. Komunikasi Penyiaran Islam
2. **EGOV** (Aplikasi Informatika & E-Government):
   1. Informatika
   2. Manajemen Informatika
   3. Sistem Informasi
   4. Teknologi Informasi
3. **ITI** (Infrastruktur Teknologi Informasi):
   1. Informatika
   2. Teknik Komputer dan Jaringan
   3. Teknologi Informasi
4. **Sekretariat** (Sekretariat Dinas):
   1. Ilmu Komunikasi
   2. Komunikasi Penyiaran Islam
   3. Desain Grafis (Grafik Desain)
   4. Ekonomi dan Sosial (KPE)
   5. Administrasi Perkantoran
   6. Manajemen Perkantoran
   7. Ilmu Kearsipan
   8. Manajemen Rekod

---

## 1. Tujuan
Menyelaraskan master data Bidang, master data Jurusan, dan relasi pivot `bidang_jurusan` pada database aplikasi MAGKOM secara menyeluruh dan permanen:
1. Memastikan seluruh nama bidang resmi Dinas Kominfo Sleman (IKP, EGOV, ITI, dan Sekretariat) terdaftar dan terkonfigurasi dengan tepat.
2. Memastikan seluruh jurusan yang dibutuhkan di atas terdaftar di tabel `jurusan` dengan jenjang kategori (`smk` / `mahasiswa`) yang tepat dan status aktif.
3. Menghubungkan secara presisi relasi *many-to-many* antara bidang dan jurusan melalui tabel pivot `bidang_jurusan` sehingga:
   - Pada **Manajemen Kuota Admin** (`/admin/kuota`), saat memilih bidang pada modal *Tambah Kuota*, daftar chips jurusan yang muncul otomatis sesuai spesifikasi masing-masing bidang.
   - Pada tabel agregasi kuota per bidang, kolom *"Jurusan yang Dibutuhkan"* menampilkan daftar kejuruan resmi ini.
   - Pada **Portal Publik / Landing Page**, kartu jurusan menampilkan *badge chip* bidang penerima yang tepat (`IKP`, `E-Gov`, `ITI`, `Sekretariat`).
   - Pada formulir **Pendaftaran Peserta**, calon peserta dapat memilih jurusan-jurusan resmi tersebut.

---

## 2. Asumsi & Pemetaan Data

1. **Standarisasi Penulisan Nama Jurusan**:
   - `Statistik` $\rightarrow$ Kategori: `mahasiswa` (D3/D4/S1)
   - `Ilmu Komunikasi` $\rightarrow$ Kategori: `mahasiswa` (S1)
   - `Desain Grafis` (grafik desain) $\rightarrow$ Kategori: `mahasiswa` (dengan varian SMK `Desain Grafis (SMK)` jika dibutuhkan)
   - `Komunikasi Penyiaran Islam` $\rightarrow$ Kategori: `mahasiswa` (S1)
   - `Informatika` $\rightarrow$ Kategori: `mahasiswa` (S1)
   - `Manajemen Informatika` $\rightarrow$ Kategori: `mahasiswa` (D3/S1)
   - `Sistem Informasi` $\rightarrow$ Kategori: `mahasiswa` (S1)
   - `Teknologi Informasi` $\rightarrow$ Kategori: `mahasiswa` (S1)
   - `Teknik Komputer dan Jaringan` $\rightarrow$ Kategori: `smk` (TKJ)
   - `Ekonomi dan Sosial (KPE)` $\rightarrow$ Kategori: `mahasiswa` (S1)
   - `Administrasi Perkantoran` $\rightarrow$ Kategori: `smk` / `mahasiswa`
   - `Manajemen Perkantoran` $\rightarrow$ Kategori: `mahasiswa` / `smk`
   - `Ilmu Kearsipan` $\rightarrow$ Kategori: `mahasiswa` (D3/S1)
   - `Manajemen Rekod` $\rightarrow$ Kategori: `mahasiswa` (D3/S1)
2. **Idempotensi Sinkronisasi Data (Non-Destruktif)**:
   - Pembaruan master data **dilarang menghapus atau mereset data pendaftaran dan user** yang sedang berjalan di sistem lokal maupun produksi.
   - Digunakan metode `updateOrCreate` pada master data dan `syncWithoutDetaching()` atau `sync()` pada tabel pivot `bidang_jurusan`.
3. **Penyelarasan Alias Bidang di Controller**:
   - Menyelaraskan mapping alias visual di `KuotaController` agar nama alias dan inisial mencerminkan singkatan resmi:
     - `Informasi dan Komunikasi Publik (IKP)` $\rightarrow$ Alias: `IKP` (Inisial: `IKP`, dengan warna identitas ungu/pink muda)
     - `Aplikasi Informatika & E-Government (EGOV)` $\rightarrow$ Alias: `Aplikasi Informatika & EGOV` (Inisial: `E`, warna indigo)
     - `Infrastruktur Teknologi Informasi (ITI)` $\rightarrow$ Alias: `Infrastruktur TI (ITI)` (Inisial: `ITI`, warna hijau/teal)
     - `Sekretariat Dinas` $\rightarrow$ Alias: `Sekretariat` (Inisial: `S`, warna amber/kuning)

---

## 3. Evaluasi Opsi Arsitektur

| Parameter | Opsi 1: Idempotent Database Seeding & Seeder Synchronization (Rekomendasi) | Opsi 2: Database Migration Script dengan Raw SQL Update |
| :--- | :--- | :--- |
| **Deskripsi** | Memperbarui `JurusanSeeder`, `BidangSeeder`, dan `BidangJurusanSeeder` lalu mengeksekusinya menggunakan `php artisan db:seed --class=...`. | Membuat file migration baru berstempel waktu yang mengeksekusi `DB::table('jurusan')->insertOrIgnore(...)` dan mapping pivot langsung di migrasi. |
| **Kelebihan** | 1. Sangat rapi, menjadi *single source of truth* untuk seluruh lingkungan (dev, testing, production).<br>2. Unit test (`RefreshDatabase`) yang memanggil `$this->seed()` langsung mendapatkan data terbaru yang konsisten.<br>3. Aman tanpa risiko *migration collision* atau duplikasi *foreign key*. | Dapat dijalankan otomatis saat `php artisan migrate`. |
| **Kekurangan** | Harus memastikan seeder dipanggil di database lokal saat ini. | Menumpuk file migration untuk perubahan data master (anti-pattern dalam best practices Laravel jika master data semestinya ada di Seeder). |
| **Keputusan** | **DIPILIH (Opsi 1)** didukung dengan eksekusi seeder terarah pada database lokal. | Ditolak karena seeder adalah tempat resmi master data aplikasi MAGKOM. |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `database/seeders/JurusanSeeder.php` | `Modify` | Menambahkan dan menstandarisasi seluruh jurusan yang dibutuhkan (Statistik, Ilmu Komunikasi, Desain Grafis, KPI, Informatika, MI, SI, TI, TKJ, KPE, Administrasi Perkantoran, Manajemen Perkantoran, Ilmu Kearsipan, Manajemen Rekod). |
| `database/seeders/BidangSeeder.php` | `Modify` | Memastikan 4 nama bidang baku (`Informasi dan Komunikasi Publik (IKP)`, `Aplikasi Informatika & E-Government (EGOV)`, `Infrastruktur Teknologi Informasi (ITI)`, `Sekretariat Dinas`). |
| `database/seeders/BidangJurusanSeeder.php` | `Modify` | Memperbarui array relasi pemetaan 100% presisi sesuai instruksi user (IKP: 4 jurusan, EGOV: 4 jurusan, ITI: 3 jurusan, Sekretariat: 8 jurusan). |
| `app/Http/Controllers/Admin/KuotaController.php` | `Modify` | Menyelaraskan `$themeMap` dan deteksi alias bidang agar IKP, EGOV, ITI, dan Sekretariat teridentifikasi dengan alias yang elegan dan tetap mempertahankan *backward compatibility* pada tes eksisting. |
| `tests/Feature/BidangJurusanMappingTest.php` | `Create` | Membuat automated feature test untuk memverifikasi pemetaan relasi bidang-jurusan, agregasi di halaman manajemen kuota, dan badge di landing page. |

---

## 5. Tabel Pemetaan Lengkap Bidang & Jurusan yang Dibutuhkan

```
┌───────────────────────────────────────────────┬──────────────────────────────────────────────────────┬───────────┐
│ Bidang Dinas                                  │ Jurusan yang Dibutuhkan                              │ Jenjang   │
├───────────────────────────────────────────────┼──────────────────────────────────────────────────────┼───────────┤
│ 1. IKP                                        │ 1. Statistik                                         │ Mahasiswa │
│    (Informasi dan Komunikasi Publik)          │ 2. Ilmu Komunikasi                                   │ Mahasiswa │
│                                               │ 3. Desain Grafis                                     │ Mhs / SMK │
│                                               │ 4. Komunikasi Penyiaran Islam                        │ Mahasiswa │
├───────────────────────────────────────────────┼──────────────────────────────────────────────────────┼───────────┤
│ 2. EGOV                                       │ 1. Informatika                                       │ Mahasiswa │
│    (Aplikasi Informatika & E-Government)      │ 2. Manajemen Informatika                             │ Mahasiswa │
│                                               │ 3. Sistem Informasi                                  │ Mahasiswa │
│                                               │ 4. Teknologi Informasi                               │ Mahasiswa │
├───────────────────────────────────────────────┼──────────────────────────────────────────────────────┼───────────┤
│ 3. ITI                                        │ 1. Informatika                                       │ Mahasiswa │
│    (Infrastruktur Teknologi Informasi)        │ 2. Teknik Komputer dan Jaringan                      │ SMK (TKJ) │
│                                               │ 3. Teknologi Informasi                               │ Mahasiswa │
├───────────────────────────────────────────────┼──────────────────────────────────────────────────────┼───────────┤
│ 4. Sekretariat                                │ 1. Ilmu Komunikasi                                   │ Mahasiswa │
│    (Sekretariat Dinas)                        │ 2. Komunikasi Penyiaran Islam                        │ Mahasiswa │
│                                               │ 3. Desain Grafis                                     │ Mhs / SMK │
│                                               │ 4. Ekonomi dan Sosial (KPE)                          │ Mahasiswa │
│                                               │ 5. Administrasi Perkantoran                          │ SMK / Mhs │
│                                               │ 6. Manajemen Perkantoran                             │ Mhs / SMK │
│                                               │ 7. Ilmu Kearsipan                                    │ Mahasiswa │
│                                               │ 8. Manajemen Rekod                                   │ Mahasiswa │
└───────────────────────────────────────────────┴──────────────────────────────────────────────────────┴───────────┘
```

---

## 6. Langkah Implementasi Terstruktur

- [x] **Fase 1: Pembaruan Master Data Seeder**
  - [x] Perbarui `database/seeders/JurusanSeeder.php`: Daftarkan seluruh jurusan dengan `updateOrCreate` berbasis `nama_jurusan`.
  - [x] Perbarui `database/seeders/BidangSeeder.php`: Pastikan 4 bidang IKP, EGOV, ITI, dan Sekretariat terdaftar rapi.
  - [x] Perbarui `database/seeders/BidangJurusanSeeder.php`: Perbarui array `$mapping` dengan daftar jurusan per bidang sesuai spesifikasi di atas, lakukan `$bidang->jurusan()->syncWithoutDetaching($jurusanIds)`.
- [x] **Fase 2: Eksekusi Seeding Sinkronisasi Database Lokal**
  - [x] Jalankan seeder terarah:
    `php artisan db:seed --class=BidangSeeder`
    `php artisan db:seed --class=JurusanSeeder`
    `php artisan db:seed --class=BidangJurusanSeeder`
  - [x] Verifikasi isi tabel `jurusan` dan `bidang_jurusan` melalui tinker untuk memastikan relasi tersambung 100%.
- [x] **Fase 3: Penyelarasan Controller & Tampilan Admin Kuota**
  - [x] Sesuaikan `$themeMap` di `app/Http/Controllers/Admin/KuotaController.php` untuk menampilkan alias resmi IKP, EGOV, ITI, dan Sekretariat.
  - [x] Verifikasi bahwa saat modal `#modalTambahKuota` dibuka di browser, memilih IKP menampilkan 4 chips jurusan, memilih EGOV menampilkan 4 chips jurusan, memilih ITI menampilkan 3 chips jurusan, dan memilih Sekretariat menampilkan 8 chips jurusan.
- [x] **Fase 4: Pembuatan Automated Feature Test & Validasi**
  - [x] Buat file test baru `tests/Feature/BidangJurusanMappingTest.php` untuk memvalidasi:
    - Verifikasi database: Tiap bidang memiliki relasi jurusan yang persis sesuai pemetaan.
    - Verifikasi UI Kuota Admin: Dropdown bidang dan daftar jurusan yang dibutuhkan tampil akurat.
    - Verifikasi Landing Page: Badge chip bidang muncul di kartu masing-masing jurusan.
  - [x] Jalankan test suite:
    `php artisan test --filter=BidangJurusanMappingTest`
    `php artisan test --filter=AdminKuotaRevampTest`
  - [x] Jalankan linter Pint: `vendor\bin\pint`.

---

## 7. Strategi Testing

1. **Unit & Feature Test (`BidangJurusanMappingTest`)**:
   - `test_ikp_has_exact_required_jurusans`: Memastikan IKP terhubung ke Statistik, Ilmu Komunikasi, Desain Grafis, dan KPI.
   - `test_egov_has_exact_required_jurusans`: Memastikan EGOV terhubung ke Informatika, Manajemen Informatika, Sistem Informasi, dan Teknologi Informasi.
   - `test_iti_has_exact_required_jurusans`: Memastikan ITI terhubung ke Informatika, TKJ, dan Teknologi Informasi.
   - `test_sekretariat_has_exact_required_jurusans`: Memastikan Sekretariat terhubung ke 8 jurusan terkait.
   - `test_admin_kuota_index_renders_bidang_and_jurusans_correctly`: Memastikan JSON client-side `bidangJsonData` memuat seluruh jurusan per bidang.
2. **Regression Testing**:
   - Memastikan pengujian `AdminKuotaRevampTest`, `AdminTerminologyDesignSystemTest`, dan `EndToEndUatTest` tetap lulus 100%.

---

## 8. Risiko & Mitigasi

- **Risiko**: Jurusan eksisting yang sudah memiliki pendaftaran (misal: ID 1 TKJ, ID 5 Informatika) terhapus atau berubah ID-nya.
  - **Mitigasi**: Gunakan `updateOrCreate` dengan pencocokan nama jurusan (`nama_jurusan`) sehingga ID baris yang sudah ada tetap utuh dan tidak ada *foreign key constraint violation*.
- **Risiko**: Duplikasi nama jurusan karena perbedaan penulisan huruf besar/kecil.
  - **Mitigasi**: Gunakan format nama baku yang seragam (Title Case).
- **Risiko**: Tes eksisting yang mengharapkan teks spesifik pada alias bidang.
  - **Mitigasi**: Pastikan alias mempertahankan kompatibilitas teks yang sudah teruji.

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Master data jurusan mencakup seluruh jurusan yang diminta tanpa ada yang terlewat.
- [x] Pivot `bidang_jurusan` menghubungkan:
  - IKP $\rightarrow$ 4 jurusan
  - EGOV $\rightarrow$ 4 jurusan
  - ITI $\rightarrow$ 3 jurusan
  - Sekretariat $\rightarrow$ 8 jurusan
- [x] Modal *Tambah Kuota* di Manajemen Kuota menampilkan chips jurusan yang sesuai secara dinamis saat bidang dipilih.
- [x] Landing page publik menampilkan badge bidang yang tepat di setiap kartu jurusan.
- [x] Seluruh pengujian otomatis di `BidangJurusanMappingTest` dan tes regresi lulus 100%.
- [x] Kode diformat bersih sesuai standar PSR/Laravel Pint.
