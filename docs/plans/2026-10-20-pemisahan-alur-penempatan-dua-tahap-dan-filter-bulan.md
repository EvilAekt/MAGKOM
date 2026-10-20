# Rencana Kerja: Pemisahan Alur Penempatan Peserta Dua Tahap (Mandiri), Filter Bulan Magang, dan Penyelarasan Nama Menu

**Tanggal Pembuatan**: 20 Oktober 2026  
**Status**: ✅ Selesai Diimplementasikan  
**Branch**: `test-1`  

---

## 1. Tujuan
Merestrukturisasi alur verifikasi berkas dan penempatan peserta magang di lingkungan Admin Diskominfo agar selaras dengan ritme kerja birokrasi pemerintahan nyata:
1. **Pemisahan Alur Dua Tahap (Manual & Mandiri)**:
   - **Tahap 1 (Verifikasi Berkas)**: Admin fokus memeriksa berkas pendaftaran (KTP, proposal, surat kampus) dan memutuskan **"Diterima"** tanpa dibebani keharusan memilih pembimbing lapangan terlebih dahulu. Kuota jurusan teralokasi, pendaftaran resmi berstatus *Diterima*.
   - **Tahap 2 (Penempatan Peserta)**: Di menu *Penempatan Peserta*, tersedia antrean peserta yang sudah lolos seleksi berkas namun belum ditempatkan. Admin/Koordinator mengklik tombol **"Tempatkan"** untuk memilih Bidang Dinas dan Pembimbing Lapangan secara cermat dan fleksibel.
2. **Filter Bulan Magang di Menu Penempatan Peserta**:
   - Menambahkan dropdown filter **Bulan Magang** pada halaman Penempatan Peserta agar admin dapat menyaring data penempatan per gelombang/bulan magang (seperti halnya di Dashboard & Manajemen Kuota).
3. **Label Asal Bidang pada Nama Pembimbing**:
   - Menampilkan unit bidang dinas di samping nama pembimbing pada semua dropdown pembimbing lapangan (contoh: `Budi Santoso, S.Kom [E-Government] (2 Mentee Aktif)`).
4. **Penyelarasan Nama Menu**:
   - Mengubah nama menu `Penempatan` di sidebar dan judul halaman menjadi **`Penempatan Peserta`**.

---

## 2. Asumsi & Rujukan Desain

1. **Integritas Database**:
   - Tabel `penempatan` tetap mempertahankan foreign key `pembimbing_id` NOT NULL.
   - Pendaftaran yang berstatus `diterima` namun belum ditetapkan pembimbingnya memiliki relasi `penempatan` kosong (`null`).
   - Setelah admin menetapkan pembimbing di Tahap 2, baris di tabel `penempatan` dibuat dan Surat Keterangan Diterima resmi dapat diunduh/dicetak.
2. **Pola UX Antrean di Menu Penempatan**:
   - Menu Penempatan Peserta akan memiliki 2 komponen utama:
     - **Komponen 1 (Atas)**: Card/Section *"Antrean Peserta Lolos (Belum Ditempatkan)"* yang muncul dinamis jika terdapat peserta berstatus Diterima yang belum memiliki pembimbing.
     - **Komponen 2 (Bawah)**: Tabel data seluruh peserta magang yang sudah ditempatkan (lengkap dengan filter Pencarian, Bulan Magang, Status, Pembimbing, dan Bidang).
3. **Konsistensi Desain**:
   - Mengikuti MagKom Design System (warna signature `#0641CC`, border-radius 12px/14px, shadow halus, dan dukungan penuh Dark Mode).

---

## 3. Evaluasi Opsi Arsitektur Alur Kerja

| Parameter | Opsi 1: Dua Tahap Mandiri (Pendaftaran Diterima -> Penempatan) [Rekomendasi] | Opsi 2: Penempatan Dibuat Nullable (Status: Draft/Menunggu Pembimbing) |
| :--- | :--- | :--- |
| **Deskripsi** | Di Tahap 1, `Pendaftaran` diupdate ke `diterima` dan kuota umum dikunci. Tabel `penempatan` baru dibuat di Tahap 2 saat pembimbing & bidang ditetapkan. | Di Tahap 1, tabel `penempatan` langsung dibuat namun kolom `pembimbing_id` diizinkan NULL dengan status `menunggu_pembimbing`. |
| **Kelebihan** | 1. **Tidak merusak skema database yang sudah ada** (tidak perlu migrasi mengubah constraint FK `pembimbing_id`).<br>2. Menghindari data sampah/inkonsisten di tabel bimbingan, logbook, dan presensi.<br>3. Pemisahan tanggung jawab jelas: Pendaftaran = seleksi berkas, Penempatan = plotting penugasan kerja. | Semua data langsung terpusat di tabel `penempatan`. |
| **Kekurangan** | Memerlukan query `whereDoesntHave('penempatan')` untuk menampilkan antrean peserta yang belum ditempatkan. | Memerlukan migrasi database untuk membuat `pembimbing_id` nullable, yang berisiko merusak relasi model, query laporan, dan audit log lama. |
| **Keputusan** | **DIPILIH (Opsi 1)**. Sangat aman, bersih, dan sesuai standar arsitektur relasional. | Ditolak karena menimbulkan risiko dependensi migrasi yang tidak perlu. |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Requests/KeputusanPendaftaranRequest.php` | `Modify` | Hapus aturan `required` untuk `pembimbing_id` pada keputusan `terima`. |
| `app/Services/PlacementService.php` | `Modify` | Pisahkan logika verifikasi berkas (`terimaBerkas`) dan penetapan bimbingan (`tetapkanPenempatan`). Kuota umum dialokasikan di Tahap 1, entri `Penempatan` dibuat di Tahap 2. |
| `app/Http/Controllers/Admin/PendaftaranController.php` | `Modify` | Sederhanakan aksi `putuskan` untuk keputusan `terima` tanpa input pembimbing. |
| `resources/views/admin/pendaftaran/show.blade.php` | `Modify` | Perbarui panel "Terima": hilangkan dropdown pembimbing/bidang yang memaksa, ganti dengan konfirmasi ringkas kelolosan berkas. |
| `app/Http/Requests/TetapkanPenempatanRequest.php` | `Create` | Form request validasi penetapan penempatan peserta (`pendaftaran_id`, `bidang_id`, `pembimbing_id`). |
| `routes/web.php` | `Modify` | Daftarkan route POST `admin.penempatan.tetapkan`. |
| `app/Http/Controllers/Admin/PenempatanController.php` | `Modify` | 1. Tambahkan query filter `bulan_magang_id`.<br>2. Ambil data antrean peserta lolos yang belum ditempatkan.<br>3. Eager load relasi `bidang` pada `$pembimbingList`.<br>4. Tambahkan method `tetapkan(TetapkanPenempatanRequest $request)`. |
| `resources/views/admin/penempatan/index.blade.php` | `Modify` | 1. Tampilkan section Antrean Peserta Belum Ditempatkan.<br>2. Modal popup penetapan pembimbing & bidang.<br>3. Tambahkan dropdown filter Bulan Magang.<br>4. Tampilkan label bidang `[Nama Bidang]` di dropdown pembimbing.<br>5. Perbarui judul menjadi "Penempatan Peserta". |
| `resources/views/layouts/dashboard.blade.php` | `Modify` | Ubah label navigasi menu sidebar dari `Penempatan` menjadi `Penempatan Peserta`. |
| `tests/Feature/AdminPenempatanDuaTahapTest.php` | `Create` | Test suite otomatis untuk memverifikasi alur dua tahap, filter bulan magang, dan label bidang pembimbing. |

---

## 5. Langkah Implementasi Terstruktur

- [x] **Fase 1: Backend - Refactor Service & Request Verifikasi (Tahap 1)**
  - [x] Ubah `app/Http/Requests/KeputusanPendaftaranRequest.php`: jadikan `pembimbing_id` dan `bidang_id` tidak lagi wajib pada keputusan `terima`.
  - [x] Di `app/Services/PlacementService.php`:
    - Buat method `terimaBerkas(Pendaftaran $pendaftaran, User $admin, ?string $catatan = null): Pendaftaran` untuk mengalokasikan kuota umum, update status pendaftaran menjadi `diterima`, dan mencatat audit log.
    - Buat method `tetapkanPenempatan(Pendaftaran $pendaftaran, User $admin, User $pembimbing, ?Bidang $bidang = null): Penempatan` untuk membuat record `Penempatan` dengan tanggal mulai/selesai yang dihitung otomatis.
  - [x] Perbarui `app/Http/Controllers/Admin/PendaftaranController.php@putuskan` agar memanggil `terimaBerkas`.

- [x] **Fase 2: Frontend - Tampilan Verifikasi Berkas (Tahap 1)**
  - [x] Di `resources/views/admin/pendaftaran/show.blade.php`:
    - Hapus input dropdown pembimbing dan bidang pada panel keputusan `Terima`.
    - Tampilkan pesan informatif: *"Menyetujui berkas pendaftaran ini akan mengalokasikan kuota umum. Penugasan pembimbing lapangan dilakukan di menu Penempatan Peserta."*
    - Ubah label tombol menjadi ringkas dan tegas: **"Diterima"**.

- [x] **Fase 3: Backend - Endpoint Penetapan & Filter Bulan (Tahap 2)**
  - [x] Buat request `app/Http/Requests/TetapkanPenempatanRequest.php`.
  - [x] Daftarkan route `POST /admin/penempatan/tetapkan` di `routes/web.php`.
  - [x] Di `app/Http/Controllers/Admin/PenempatanController.php`:
    - Query `$antreanBelumDitempatkan = Pendaftaran::with(['user.profil', 'bulanMagang', 'kuota.jurusan.bidang'])->where('status', StatusPendaftaranEnum::Diterima)->doesntHave('penempatan')->get()`.
    - Tambahkan penanganan filter `$request->filled('bulan_magang_id')`.
    - Load `BulanMagang::orderBy('bulan_mulai', 'desc')->get()` untuk opsi dropdown filter.
    - Eager load relasi `bidang` pada `$pembimbingList`.
    - Implementasikan method `tetapkan(TetapkanPenempatanRequest $request)`.

- [x] **Fase 4: Frontend - Tampilan Penempatan Peserta & Modal (Tahap 2)**
  - [x] Di `resources/views/admin/penempatan/index.blade.php`:
    - Perbarui judul halaman dan breadcrumb menjadi **Penempatan Peserta**.
    - Tambahkan card / banner antrean jika `$antreanBelumDitempatkan->isNotEmpty()`.
    - Tambahkan modal popup `openTetapkanModal(pendaftaranId, namaPeserta, defaultBidangId)`.
    - Tambahkan dropdown filter **Bulan Magang** di dalam filter bar.
    - Perbarui seluruh dropdown pembimbing agar menyertakan label bidang: `{{ $p->nama }} [{{ $p->bidang?->nama_bidang ?? 'Dinas' }}] ({{ $p->mentee_aktif_count }} Mentee)`.
  - [x] Di `resources/views/layouts/dashboard.blade.php`:
    - Ubah menu navigasi sidebar: `{{ __('Penempatan') }}` ➔ `{{ __('Penempatan Peserta') }}`.

- [x] **Fase 5: Testing & Verifikasi**
  - [x] Buat file test `tests/Feature/AdminPenempatanDuaTahapTest.php`:
    1. Verifikasi berkas diterima tanpa memilih pembimbing.
    2. Peserta lolos muncul di antrean belum ditempatkan pada menu Penempatan Peserta.
    3. Admin menetapkan pembimbing & bidang dari menu Penempatan Peserta.
    4. Filter bulan magang di menu Penempatan Peserta berfungsi menyaring data dengan benar.
    5. Dropdown pembimbing memuat nama bidang dinas.
  - [x] Jalankan seluruh test suite terkait:
    - `php artisan test --filter=AdminPenempatanDuaTahapTest`
    - `php artisan test --filter=AdminKuotaRevampTest`
    - `php artisan test --filter=AdminTerminologyDesignSystemTest`
  - [x] Jalankan `vendor\bin\pint` untuk memastikan standar kode.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Verifikasi berkas (Tahap 1) dapat diselesaikan tanpa perlu memilih pembimbing lapangan.
- [x] Peserta yang berkasnya diterima otomatis muncul di antrean penempatan pada menu *Penempatan Peserta*.
- [x] Penetapan pembimbing & bidang (Tahap 2) dapat dilakukan dengan mudah melalui modal popup di menu *Penempatan Peserta*.
- [x] Filter Bulan Magang tersedia dan berfungsi aktif pada halaman Penempatan Peserta.
- [x] Dropdown pembimbing di seluruh modal menampilkan label asal bidang dinas.
- [x] Nama menu di sidebar dan judul halaman telah diselaraskan menjadi *Penempatan Peserta*.
- [x] Seluruh pengujian otomatis lolos 100%.
