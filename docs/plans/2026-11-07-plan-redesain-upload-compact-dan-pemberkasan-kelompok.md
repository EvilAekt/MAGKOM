# Rencana Desain Upload Compact & Sistem Pemberkasan Anggota Kelompok

**Tanggal**: 7 November 2026  
**Status**: Draf Usulan (Menunggu Konfirmasi User)  
**Tujuan**:
1. Menata ulang desain kartu upload berkas persyaratan pada halaman pemberkasan (`resources/views/peserta/pendaftaran/detail.blade.php`) agar **compact, ramping, modern, dan hemat ruang vertikal** (menggantikan dropzone yang saat ini terlalu besar).
2. Mengimplementasikan alur pengunggahan berkas untuk **Pendaftaran Kelompok**, di mana:
   - **Berkas Bersama Tim**: Surat Pengantar Magang dan Proposal (hanya 1 berkas per tim/pendaftaran).
   - **Berkas Individu**: Curriculum Vitae (CV) dan Transkrip Nilai Sementara diunggah oleh Ketua Tim untuk dirinya sendiri dan masing-masing anggota kelompoknya melalui antarmuka tab/segmented switcher yang rapi dan tidak memenuhi layar.

---

## 1. Analisis Masalah & Visual Evidence

Berdasarkan screenshot pengguna (`media_1794047566494_2bb4dc51.png`):
1. **Area Dropzone Terlalu Besar**:
   - Komponen dropzone saat ini menggunakan padding tebal (`22px 18px`), ikon upload bulat besar (`44px`), dan teks multi-baris tinggi (`~150px` per dokumen).
   - Jika terdapat 4 berkas pada pendaftaran mandiri atau 8+ berkas pada pendaftaran kelompok (2 berkas umum + 2 berkas per anggota), halaman menjadi sangat panjang dan melelahkan untuk digulir (*visual clutter*).
2. **Ketiadaan Fitur Pengunggahan Berkas Anggota Kelompok**:
   - Saat ini, tabel `dokumen` hanya memiliki relasi `pendaftaran_id` dan `jenis_dokumen_id` dengan unique constraint tunggal.
   - Belum ada mekanisme bagi Ketua Tim untuk mengunggah CV dan Transkrip Nilai khusus untuk masing-masing anggota kelompok (`kelompok_anggota`), sehingga berkas anggota kelompok belum dapat terakomodasi di sistem.

---

## 2. Strategi Solusi: Compact Row-Based Design & Segmented Member Switcher

### A. Compact Modern Upload Dropzone (Hemat Ruang 60%)
- **Sebelum**: Box dropzone vertikal besar dengan border tebal setinggi ~150px.
- **Sesudah**: **Compact Inline Dropzone (~44px–48px)**:
  - Tata letak horizontal satu baris yang bersih dan elegan:
    - Kiri: Ikon cloud upload ramping (20px) + teks ajakan: *"Seret file ke sini atau **Pilih File**"*.
    - Kanan: Chip format ringkas *"PDF, JPG, PNG • Maks 2 MB"*.
  - Status berkas terunggah (*uploaded box*) dipadatkan menjadi *single clean action strip* dengan tombol "Lihat Berkas", "Unduh", dan "Ganti File".

### B. Arsitektur Pemberkasan Pendaftaran Kelompok
Untuk pendaftaran bertipe **Kelompok** (`tipe === 'kelompok'`):
1. **Klasifikasi Dokumen**:
   - **Dokumen Kolektif Tim**:
     - *Surat Pengantar Magang Resmi* (dari Dekanat / Fakultas / Sekolah).
     - *Proposal Rencana Kegiatan Magang* (untuk D3 & S1) atau *Surat Persetujuan Wali* (SMK).
     -> Cukup diunggah 1 kali oleh Ketua untuk seluruh tim.
   - **Dokumen Individu Anggota**:
     - *Curriculum Vitae (CV) & Portofolio*.
     - *Transkrip Nilai Akademik Sementara* (atau Kartu Pelajar untuk SMK).
     -> Wajib diunggah oleh Ketua untuk: **Ketua Tim (Diri Sendiri)** + **Setiap Anggota Kelompok**.

2. **Antarmuka UI yang Rapi & Tidak Memenuhi Layar (Segmented Tabs / Switcher)**:
   - **Bagian 1: Berkas Bersama Tim**: Ditampilkan dalam 2 row kompak yang rapi di bagian atas.
   - **Bagian 2: Berkas Individu Anggota Tim**:
     - Dilengkapi **Segmented Member Switcher / Tab**:
       - `[★ Ketua (Anda)]` `[Anggota 1: Budi Pratama]` `[Anggota 2: Siti Aisyah]`
     - Masing-masing tab memiliki indikator status kelengkapan mini (badge hijau *2/2 Lengkap* atau kuning *1/2*).
     - Saat tab diklik, hanya ditampilkan 2 baris upload ringkas (CV & Transkrip) untuk anggota yang aktif dipilih.
     - Layar tetap ringkas, bersih, dan tidak memanjang ke bawah.

---

## 3. Rencana Teknis & Perubahan Kode

### A. Database Migration & Model
1. **Migrasi Baru**: `add_kelompok_anggota_id_to_dokumen_table`:
   - Tambah kolom `$table->foreignId('kelompok_anggota_id')->nullable()->after('jenis_dokumen_id')->constrained('kelompok_anggota')->nullOnDelete();`.
   - Update unique index menjadi `unique(['pendaftaran_id', 'jenis_dokumen_id', 'kelompok_anggota_id'])` agar mendukung CV/Transkrip untuk masing-masing anggota.
2. **Model `Dokumen`**:
   - Tambahkan `'kelompok_anggota_id'` ke `$fillable`.
   - Tambahkan relasi `public function kelompokAnggota(): BelongsTo`.

### B. Backend Controller & Service
1. **`UnggahDokumenRequest`**:
   - Tambah validasi `'kelompok_anggota_id' => ['nullable', 'integer', 'exists:kelompok_anggota,id']`.
2. **`DocumentStorageService`**:
   - Perbarui method `simpan(..., ?int $kelompokAnggotaId = null)` agar mendukung pencatatan dan penggantian berkas spesifik anggota kelompok.
3. **`BerkasController`**:
   - Ambil `kelompok_anggota_id` dari request dan teruskan ke service storage.
4. **`PendaftaranController`**:
   - **Method `detail()`**:
     - Memuat data `kelompokMagang.anggota`.
     - Mengelompokkan status dokumen terunggah berdasarkan dokumen tim vs dokumen per anggota.
   - **Method `ajukan()`**:
     - Jika pendaftaran kelompok, validasi kelengkapan berkas mencakup: berkas kolektif tim lengkap + berkas individual ketua lengkap + berkas individual seluruh anggota kelompok lengkap.

### C. Frontend CSS & Blade View
1. **`resources/css/layouts/dashboard-cockpit.css`**:
   - Menambahkan class `.doc-card-compact`, `.dropzone-compact-row`, `.member-tabs-nav`, `.member-tab-btn`, `.member-tab-btn.is-active`, `.member-status-pill`.
   - Memangkas padding dan tinggi dropzone agar hemat ruang 60%.
2. **`resources/views/peserta/pendaftaran/detail.blade.php`**:
   - Mengubah markup dropzone menjadi horizontal inline compact bar.
   - Menyediakan komponen *Segmented Member Switcher* untuk kelompok dengan vanilla JavaScript switcher instan tanpa reload halaman.
3. **`resources/views/admin/pendaftaran/show.blade.php`**:
   - Menampilkan berkas pada verifikasi admin secara terstruktur: berkas tim, berkas ketua, dan berkas masing-masing anggota.

---

## 4. Rencana Verifikasi & Quality Gates

1. **Migrasi Database**:
   - Menjalankan `php artisan migrate`.
2. **Kompilasi Aset Frontend**:
   - Menjalankan `npm run build` dengan Vite.
3. **Feature Test Suite**:
   - Menjalankan seluruh test pendaftaran (`PendaftaranPesertaTest`) dan test integrasi portal peserta.
   - Menambahkan test case baru untuk pengunggahan berkas anggota kelompok.
4. **PHP Code Style**:
   - Menjalankan `./vendor/bin/pint --test` untuk memastikan 100% kepatuhan PSR-12.

