# Rencana Kerja: Penanganan Pemilihan Bidang, Jurusan, dan Jenjang pada Registrasi Peserta

**Status Dokumen**: Menunggu Persetujuan Pengguna  
**Tanggal**: 5 November 2026  
**Target Modul**: Otentikasi Registrasi (`auth/register`), Profil Peserta (`peserta/profil`), dan Alur Pendaftaran Magang  

---

## 1. Analisis Kebutuhan & Kondisi Existing

### 1.1 Temuan Investigasi Alur Saat Ini (2-Stage Onboarding Flow)
Saat ini MAGKOM menerapkan pemisahan 2 tahap onboarding:
1. **Tahap 1: Registrasi Akun (`/register`)**
   - Calon peserta mendaftarkan akun dasar (`nama`, `email`, `no_telp`, `password`, dan pilihan `tipe_pendaftaran` mandiri/kelompok).
   - **Kecuali jika peserta datang dari Landing Page (`#kuota`)**: Link pendaftaran kuota membawa query parameter `?bulan=...&jurusan=...&tipe=...&jumlah=...`. Pada kondisi ini, halaman register menampilkan kartu ringkasan kuota jurusan yang terkunci.
2. **Tahap 2: Pengisian Data Akademik di Profil (`/peserta/profil`)**
   - Setelah akun dibuat dan login otomatis, sistem mengarahkan peserta ke menu Profil untuk melengkapi:
     - **Jenjang Pendidikan**: SMK (Siswa), D3 (Diploma Tiga), atau S1/D4 (Sarjana).
     - **Jurusan / Program Studi**: Dropdown jurusan yang bernaung di bawah Bidang Diskominfo Sleman.
     - **Nama Sekolah / Kampus & NIM / NISN**.
     - *(Khusus SMK: Checkbox persetujuan orang tua/wali sesuai regulasi perlindungan data UU PDP / SEC-41)*.
3. **Tahap 3: Pengajuan Pendaftaran Magang (`/peserta/pendaftaran`)**
   - Sistem mencocokkan kuota yang tersedia hanya untuk jurusan dan jenjang yang sudah disimpan di profil peserta.

### 1.2 Masalah yang Dihadapi Pengguna
Jika calon peserta mengklik menu **"Register"** secara langsung dari topbar/navbar (bukan melalui kartu kuota di landing page), formulir register **belum menampilkan pilihan Jenjang, Bidang, maupun Jurusan**. Hal ini menimbulkan kebingungan bagi pengguna yang mengira pengisian jurusan harus dilakukan langsung saat registrasi.

---

## 2. Evaluasi Opsi Solusi

| Aspek | Opsi A: Pertahankan 2 Tahap dengan UX Stepper & Shortcut Kuota (Rekomendasi Aman) | Opsi B: Pindahkan & Integrasikan Jenjang & Jurusan Langsung ke Form Register |
| :--- | :--- | :--- |
| **Deskripsi** | Menjaga register tetap ringan untuk akun, menambahkan **Stepper Indikator Alur 3-Langkah** di halaman register, serta tombol cepat *"Pilih Kuota Terlebih Dahulu dari Landing Page"* jika ingin langsung memilih jurusan di awal. | Menambahkan input Jenjang, Bidang/Jurusan, Asal Sekolah/Kampus, dan NIM/NISN langsung ke dalam formulir `/register`. Setelah submit, otomatis membuat record `PesertaProfil`. |
| **Kompleksitas Form** | Rendah (Form register tetap cepat dan nyaman diakses via perangkat bergerak). | Tinggi (Form register menjadi sangat panjang, terutama jika user memilih tipe Kelompok 2–4 orang). |
| **Kepatuhan SEC-41 (UU PDP)** | Aman (Validasi usia/persetujuan wali anak di bawah umur tetap berada di form profil). | Memerlukan penanganan checkbox persetujuan wali dinamis di form register jika memilih SMK. |
| **Dampak Testing & Arsitektur** | Minimal (Tidak mengubah kontrak `AuthController@register` atau test autentikasi yang sudah ada). | Memerlukan pembaruan test autentikasi (`AuthFeatureTest`, `PesertaFullPortalFeatureTest`) dan penyesuaian request validasi. |
| **Rekomendasi** | **Disarankan jika memprioritaskan kemudahan pengisian & stabilitas baseline.** | **Disarankan jika pengguna secara mutlak ingin jenjang & jurusan ditentukan di awal registrasi.** |

---

## 3. Pendekatan Teknis Terpilih: Opsi B (Integrasi Jenjang & Jurusan ke Register)
*(Disesuaikan secara spesifik dengan permintaan pengguna agar di menu register dapat mengisi jenjang, bidang, dan jurusan).*

### 3.1 Perubahan Data Flow
1. `AuthController@showRegister`:
   - Mengambil data master `JenjangEnum::cases()`, `Jurusan::where('aktif', true)->with('bidang')->get()`, dan list `Bidang::where('aktif', true)->get()`.
   - Mengirimkannya ke view `auth.register`.
2. `auth/register.blade.php`:
   - Menambahkan seksi *"Informasi Pendidikan & Pilihan Jurusan"*:
     - Pilihan **Jenjang Pendidikan** (Radio/Select: SMK, D3, S1).
     - Pilihan **Bidang & Jurusan** (Dropdown jurusan berfilter atau dikelompokkan per kategori/bidang).
     - Checkbox persetujuan orang tua/wali khusus jenjang SMK (kepatuhan SEC-41 UU PDP).
   - Jika halaman dibuka dengan URL parameter `?jurusan=...`, input jenjang & jurusan terkunci otomatis sesuai kuota terpilih.
3. `AuthController@register`:
   - Menambahkan validasi `jenjang` (`required|in:smk,d3,s1`), `jurusan_id` (`required|exists:jurusan,id`), dan `persetujuan_wali` (wajib jika `jenjang === 'smk'`).
   - Di dalam `DB::transaction()`, setelah `User` dibuat, langsung membuat record awal `PesertaProfil` dengan `jenjang`, `jurusan_id`, dan `persetujuan_wali_at`.
4. `peserta/profil/show.blade.php`:
   - Data jenjang dan jurusan yang sudah diisi saat register otomatis terisi (*pre-filled* dan sinkron). Peserta tinggal melengkapi pasfoto dan kontak darurat.

---

## 4. Rincian Perubahan Berkas

| No | Berkas | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- | :--- |
| 1 | `app/Http/Controllers/Auth/AuthController.php` | Modify | Tambah passing master data `jurusanList` di `showRegister()`, tambahkan validasi `jenjang` & `jurusan_id`, serta `PesertaProfil::create()` di `register()`. |
| 2 | `resources/views/auth/register.blade.php` | Modify | Tambahkan field input Jenjang Pendidikan (SMK/D3/S1), Jurusan/Program Studi, dan kepatuhan persetujuan wali SMK. |
| 3 | `resources/css/pages/auth/register.css` (atau CSS auth yang relevan) | Modify | Penataan styling form section pendidikan agar rapi dan selaras dengan design token MAGKOM. |
| 4 | `resources/js/pages/auth/register.js` | Modify/Create | Interaktivitas filter jurusan berdasarkan jenjang yang dipilih (SMK hanya menampilkan jurusan SMK, D3/S1 menampilkan jurusan mahasiswa) dan toggle persetujuan wali. |
| 5 | `tests/Feature/AuthFeatureTest.php` | Modify | Penyesuaian payload test pendaftaran akun agar menyertakan data jenjang dan jurusan yang valid. |

---

## 5. Rencana Langkah Kerja (Checklist Implementasi)

- [x] **Fase 1: Backend & Controller Logic**
  - [x] Update method `showRegister` di `AuthController.php` untuk memuat data `$jurusanList` dan `$bidangList`.
  - [x] Tambahkan aturan validasi `jenjang`, `jurusan_id`, dan kondisi `persetujuan_wali` di method `register`.
  - [x] Tambahkan pembuatan record `PesertaProfil` di dalam transaksi registrasi akun baru.
- [x] **Fase 2: Antarmuka Blade & Styling**
  - [x] Tambahkan elemen field `jenjang` dan `jurusan_id` pada `resources/views/auth/register.blade.php`.
  - [x] Pastikan atribut accessibility (ARIA label, error state) dan design token MAGKOM diterapkan.
  - [x] Tambahkan filter JavaScript vanilla di `resources/js/` agar saat peserta memilih "SMK", pilihan jurusan yang muncul hanya kategori SMK, dan sebaliknya untuk D3/S1.
- [x] **Fase 3: Asset Build & Pengujian**
  - [x] Jalankan `npm run build` untuk mengompilasi asset frontend.
  - [x] Jalankan unit & feature test: `php artisan test tests/Feature/AuthFeatureTest.php` dan `tests/Feature/PesertaFullPortalFeatureTest.php`.
  - [x] Jalankan pemeriksaan static analysis dan code formatting: `vendor/bin/pint --test`.

---

## 6. Kriteria Selesai (Definition of Done)
- [x] Calon peserta dapat memilih Jenjang (SMK, D3, S1) dan Jurusan langsung di halaman `/register`.
- [x] Jika mendaftar dari landing page lewat tombol kuota, jurusan dan jenjang terkunci secara otomatis sesuai pilihan.
- [x] Setelah akun terdaftar dan peserta diarahkan ke dashboard/profil, data jenjang dan jurusan sudah tersimpan dengan benar di `peserta_profil`.
- [x] Kepatuhan UU PDP (persetujuan wali bagi SMK) tetap terjaga.
- [x] Seluruh unit dan feature test otomatis lulus (`100% green`).

