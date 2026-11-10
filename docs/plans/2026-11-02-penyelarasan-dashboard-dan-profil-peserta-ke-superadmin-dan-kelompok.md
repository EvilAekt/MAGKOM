# Rencana Kerja: Penyelarasan Dashboard & Profil Peserta ke Standar SuperAdmin (Warna, Font, Layout) & Integrasi Akun Kelompok

- **Tanggal**: 2026-11-02
- **Dokumen Referensi**:
  - `docs/plans/2026-10-28-rebranding-css-peeper-palette-header-sidebar-all-dashboards.md` (PRD-TASK-2026-10-28-UI-02)
  - `resources/views/layouts/dashboard.blade.php` (Master Layout SuperAdmin / Admin)
  - `resources/views/superadmin/dashboard.blade.php` & `public/css/superadmin-dashboard.css`
  - `resources/views/layouts/peserta.blade.php`
  - `resources/views/peserta/dashboard.blade.php` & `resources/views/peserta/dashboard/partials/`
  - `resources/views/peserta/profil/show.blade.php`
  - `app/Http/Controllers/Peserta/DashboardController.php`
  - `app/Http/Controllers/Peserta/ProfilController.php`

---

## 1. Tujuan
1. **Penyelarasan Dashboard Peserta dengan SuperAdmin**:
   - **Warna**: Mengadopsi penuh *11 Master CSS Peeper Tokens* (`#01347C` *Royal Sleman Navy*, `#003566` *Midnight Navy*, `#02275D` *Abyssal Navy*, `#FFCB05` *Gold Accent*, `#3F51B5` *Indigo Accent*), menggantikan gradien lama yang kusam (`#1b3860` / `#152d4e`).
   - **Font & Tipografi**: Menyeragamkan hierarki tipografi dengan standar SuperAdmin (`Open Sans` / `Lexend`, metric tile stats tebal, subtitle muted, dan badge uppercase).
   - **Layout & Komponen**: Menyelaraskan struktur visual dengan layout modular SuperAdmin:
     - **Baris 1**: Atmospheric Hero Studio Banner Full-Width (`.sa-hero` style).
     - **Baris 2**: 4 Kartu Metrik Utama (`.sa-metrics-row` / `.tile_count` solid navy `#01347C`).
     - **Baris 3**: Split Cockpit Layout 12-kolom (Kolom Utama 8-kolom dengan *Statusline Ribbon* dan *Attention Card*, serta Kolom Samping 4-kolom).
2. **Integrasi Khusus Akun Kelompok pada Dashboard, Biodata, & Profil**:
   - **Di Dashboard**: Menampilkan identitas tim kelompok pada Hero Banner, statusline, dan **Kartu Kolaborasi Tim Kelompok** di area Aside/Main (Nama Kelompok, Badge Jumlah Anggota, Roster Ketua & Rekan dengan kontak cepat).
   - **Di Biodata & Profil Peserta (`profil/show.blade.php`)**:
     - Mengubah styling cover banner dan panel form menjadi seragam dengan SuperAdmin (`border-top: 3.5px solid #01347C`).
     - Menambahkan **Kartu Profil & Roster Anggota Tim Kelompok** yang memuat Nama Tim, status kepemimpinan ketua, dan tabel informasi seluruh anggota tim (Nama Lengkap, NIM/NISN, Email, No. HP).

---

## 2. Asumsi & Asumsi Kerja Logis

1. **Deteksi Akun Kelompok**:
   - User dianggap sebagai akun kelompok jika memiliki relasi `$user->kelompokMagang` (sebagai ketua) atau pendaftaran bertipe `'kelompok'` (`$pendaftaran->tipe === 'kelompok'`).
   - Akun mandiri tetap tampil bersih dan elegan tanpa kartu kelompok yang mengganggu.
2. **Kinerja & Query Eager Loading**:
   - Di `PesertaDashboardController` dan `PesertaProfilController`, relasi `kelompokMagang.anggota` di-*eager load* untuk menghindari masalah N+1 query.
3. **Dual Theme (Light & Dark Mode)**:
   - Seluruh kartu metrik, statusline ribbon, dan panel profil harus mewarisi token tema otomatis dari `layouts/peserta.blade.php` (tanpa hardcode warna gelap statis yang pecah saat ganti tema).
4. **Zero Regresi Fungsional**:
   - Perubahan murni menyelaraskan estetika visual, tata letak, dan pengayaan informasi kelompok tanpa merusak form submission profil, alur pendaftaran, upload berkas, logbook, maupun tugas.

---

## 3. Evaluasi Opsi Arsitektur

| Kriteria | Opsi 1: Unified Styling & Komponen Cockpit Modular (Rekomendasi) | Opsi 2: Penggantian Total View Peserta dengan View SuperAdmin |
| :--- | :--- | :--- |
| **Deskripsi** | Mengintegrasikan token CSS SuperAdmin (`public/css/superadmin-dashboard.css` atau class modular selaras) ke `layouts/peserta.blade.php`, merestrukturisasi `peserta/dashboard.blade.php` dan `peserta/profil/show.blade.php` dengan grid cockpit dan kartu kelompok. | Memaksa Dashboard Peserta menggunakan file blade milik SuperAdmin secara langsung. |
| **Kelebihan** | - Menjaga logika bisnis spesifik peserta (fase magang, logbook harian, tugas mingguan, lembar nilai).<br>- Tampilan 100% konsisten dengan SuperAdmin tanpa merusak alur peserta.<br>- Kode terisolasi rapi dan mudah dirawat. | Sangat sedikit file yang diubah. |
| **Kekurangan** | Memerlukan penyesuaian pada beberapa partials view peserta. | Berisiko tinggi (*high risk*), hak akses role bisa bocor, logika controller superadmin dan peserta sangat berbeda. |
| **Rekomendasi** | **Dipilih (Opsi 1)** | Ditolak keras |

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`app/Http/Controllers/Peserta/DashboardController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Peserta/DashboardController.php) | **Modify** | Eager load relasi `kelompokMagang.anggota` pada `$user` dan `$pendaftaran` serta kirim variabel `$kelompokMagang` ke view dashboard. |
| [`app/Http/Controllers/Peserta/ProfilController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Peserta/ProfilController.php) | **Modify** | Eager load `kelompokMagang.anggota` dan kirimkan data kelompok ke `peserta.profil.show`. |
| [`resources/views/layouts/peserta.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/peserta.blade.php) | **Modify** | Pastikan master token CSS Peeper (`--magkom-primary-blue`, dll) dan link stylesheet cockpit siap digunakan secara seragam di seluruh portal peserta. |
| [`resources/views/peserta/dashboard.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard.blade.php) | **Modify** | Restrukturisasi layout utama ke pola `.sa-page`, `.sa-cockpit-layout`, dan penyelarasan warna dasar kanvas, font, serta spasi. |
| [`resources/views/peserta/dashboard/partials/_header.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_header.blade.php) | **Modify** | Update background Hero Banner ke `#01347C` -> `#003566`, perbarui badge instansi gold `#FFCB05`, dan sematkan badge info tim kelompok yang mencolok. |
| [`resources/views/peserta/dashboard/partials/_kpi_bar.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_kpi_bar.blade.php) | **Modify** | Ubah style 4 kartu metrik ke format Solid Tile Metrik SuperAdmin (`#01347C` di mode terang dengan angka tebal kontras putih dan aksen gold). |
| [`resources/views/peserta/dashboard/partials/_aside.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_aside.blade.php) | **Modify** | Desain ulang kartu kelompok di aside dengan style `.sa-panel` border top `#01347C`, avatar inisial rapi, badge ketua, dan daftar kontak anggota tim. |
| [`resources/views/peserta/profil/show.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/profil/show.blade.php) | **Modify** | 1. Update cover banner identitas ke warna `#01347C` -> `#003566`.<br>2. Tambahkan **Section / Card Roster Anggota Tim Kelompok** jika akun kelompok.<br>3. Selaraskan panel biodata dengan aksen top border `#01347C` dan tombol simpan `#01347C`. |

---

## 5. Langkah Implementasi Terperinci

### Tahap 1: Backend Data Preparation (Eager Loading)
- [x] **Langkah 1.1**: Pada [`app/Http/Controllers/Peserta/DashboardController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Peserta/DashboardController.php), tambahkan eager loading `kelompokMagang.anggota` agar data tim tersedia lengkap tanpa N+1 query.
- [x] **Langkah 1.2**: Pada [`app/Http/Controllers/Peserta/ProfilController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Peserta/ProfilController.php), muat relasi `kelompokMagang.anggota` dan sertakan dalam `view('peserta.profil.show')`.

### Tahap 2: Penyelarasan Layout & Token Desain Peserta
- [x] **Langkah 2.1**: Periksa [`resources/views/layouts/peserta.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/peserta.blade.php) untuk memastikan variabel 11 warna CSS Peeper dan tipografi `Open Sans` / `Lexend` terpasang konsisten dengan `layouts/dashboard.blade.php`.
- [x] **Langkah 2.2**: Update file [`resources/views/peserta/dashboard.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard.blade.php) untuk mengadopsi struktur grid cockpit SuperAdmin (`.sa-cockpit-layout`, `.sa-cockpit-main`, `.sa-cockpit-aside`).

### Tahap 3: Redesain Komponen Dashboard Peserta
- [x] **Langkah 3.1**: Perbarui [`resources/views/peserta/dashboard/partials/_header.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_header.blade.php):
  - Ganti warna background dari `#1b3860` ke `linear-gradient(135deg, #01347C 0%, #003566 100%)`.
  - Tambahkan badge status kelompok: `[KELOMPOK - X ORANG]` beserta nama tim `Tim Inovasi Smart Sleman`.
- [x] **Langkah 3.2**: Perbarui [`resources/views/peserta/dashboard/partials/_kpi_bar.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_kpi_bar.blade.php):
  - Ubah kartu KPI agar mengadopsi standar Metrik SuperAdmin (solid navy `#01347C` / border aksen `#3F51B5`, angka tebal `#FFFFFF`, subtitle soft, dan trend pill).
- [x] **Langkah 3.3**: Tambahkan **Statusline Ribbon** di dashboard peserta (Status Penempatan, Tipe Magang Mandiri/Kelompok, Pembimbing, Periode).
- [x] **Langkah 3.4**: Perbarui [`resources/views/peserta/dashboard/partials/_aside.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_aside.blade.php) untuk mempercantik kartu tim kelompok (border top navy `#01347C`, badge ketua tim, dan kontak rekan).

### Tahap 4: Redesain Biodata & Profil Peserta untuk Akun Kelompok
- [x] **Langkah 4.1**: Di [`resources/views/peserta/profil/show.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/profil/show.blade.php), perbarui cover profile card ke gradien Sleman Navy `#01347C` ke `#003566`.
- [x] **Langkah 4.2**: Tambahkan **Panel Roster Anggota Tim Kelompok** di sisi kanan/atas biodata jika akun bertipe kelompok:
  - Header: Nama Kelompok + Badge "Ketua Tim".
  - Tabel/List Anggota: Menampilkan Nama, NIM/NISN, Email, No. HP seluruh anggota.
  - Keterangan resmi mengenai tanggung jawab ketua kelompok.
- [x] **Langkah 4.3**: Selaraskan form biodata (border input, focus ring `#01347C`, panel border top `#01347C`, tombol submit `#01347C`).

### Tahap 5: Verifikasi & Testing
- [x] **Langkah 5.1**: Jalankan unit test dan feature test: `php artisan test tests/Feature/PendaftaranKelompokTest.php` dan `PesertaFullPortalFeatureTest.php`.
- [x] **Langkah 5.2**: Lakukan verifikasi visual di browser lokal untuk POV akun Mandiri (`peserta@magkom.go.id`) dan POV akun Kelompok (`kelompok@magkom.go.id`).
- [x] **Langkah 5.3**: Uji tampilan pada Mode Terang dan Mode Gelap.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - `php artisan test tests/Feature/PendaftaranKelompokTest.php` -> Memastikan data kelompok tetap utuh dan valid.
   - `php artisan test tests/Feature/PesertaFullPortalFeatureTest.php` -> Memastikan alur dashboard peserta tidak mengalami regresi.
2. **Visual Verification di Browser**:
   - **Login sebagai akun Kelompok**: `kelompok@magkom.go.id` / `Password123!`
     - Buka `http://magkom.test/peserta/dashboard`:
       - Cek warna banner header: biru navy `#01347C` selaras dengan SuperAdmin.
       - Cek 4 kartu metrik: font, warna, dan status tebal.
       - Cek kartu tim kelompok di sidebar/aside: nama kelompok "Tim Inovasi Smart Sleman", Dimas Pratama (Ketua), Citra Lestari (Anggota), Bagas Wicaksono (Anggota).
     - Buka `http://magkom.test/peserta/profil`:
       - Cek cover profile dan identitas.
       - Cek adanya bagian khusus **Roster Anggota Tim Kelompok**.
   - **Login sebagai akun Mandiri**: `peserta@magkom.go.id` / `Password123!`
     - Pastikan dashboard dan profil tampil bersih tanpa section kelompok.

---

## 7. Analisis Risiko & Mitigasi

- **Risiko Dual Theme Contrast**:
  - Warna kartu navy `#01347C` di mode gelap harus memiliki border penegas (`rgba(63, 81, 181, 0.4)`) agar tidak menyatu dengan background gelap.
  - *Mitigasi*: Gunakan CSS variabel yang sudah teruji di SuperAdmin (`--card-surface`, `--card-highlight-bg`, `--neutral-0`).
- **Risiko Akun Tanpa Kelompok**:
  - Null pointer exception jika `$user->kelompokMagang` dipanggil tanpa pengecekan.
  - *Mitigasi*: Gunakan safe navigation `optional($user->kelompokMagang)` atau `@if($user->kelompokMagang)`.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Dashboard peserta mengadopsi warna (`#01347C`, `#003566`, aksen gold `#FFCB05`), font, dan struktur layout cockpit selaras dengan SuperAdmin.
- [x] Hero Banner dan 4 kartu metrik peserta memiliki visual hierarchy yang identik dengan standar SuperAdmin.
- [x] Akun kelompok menampilkan informasi nama tim, status ketua, dan daftar rekan tim di Dashboard Peserta.
- [x] Biodata & Profil Peserta menampilkan Card Roster Kelompok yang rapi dan terpadu untuk akun kelompok.
- [x] Akun mandiri tetap bersih, rapi, dan tidak memunculkan elemen kelompok yang tidak relevan.
- [x] Seluruh automated test lulus (0 failure).
- [x] Halaman Biodata & Profil Peserta (`profil/show.blade.php`) menampilkan Section Roster Anggota Tim Kelompok secara lengkap untuk akun kelompok.
- [x] Mode terang dan mode gelap teruji dengan kontras yang nyaman dan terbaca jelas.
