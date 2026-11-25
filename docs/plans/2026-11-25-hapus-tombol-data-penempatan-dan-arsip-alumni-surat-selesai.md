# Rencana Kerja: Hapus Tombol Data Penempatan dan Arsip Alumni pada Halaman Surat Selesai

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Cakupan:** Hero Banner Halaman Daftar Surat Selesai Magang (`resources/views/admin/surat-selesai/index.blade.php`)

---

## 1. Tujuan
Menghapus tombol aksi **Data Penempatan** (`route('admin.penempatan.index')`) dan **Arsip Alumni** (`route('admin.arsip.index')`) dari area `.adm-hero-actions` pada hero banner halaman daftar utama **Surat Selesai Magang** (`resources/views/admin/surat-selesai/index.blade.php`), sementara tombol kembali pada mode fokus (`Kembali ke Daftar Peserta` dan `Kembali ke Daftar Kelompok`) tetap dipertahankan.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis:**
  1. Tombol yang dimaksud adalah dua tautan `.adm-hero-btn-secondary` (`Data Penempatan` dan `Arsip Alumni`) di `resources/views/admin/surat-selesai/index.blade.php` baris 78–92 pada kondisi `@else` (mode daftar utama).
  2. Route `admin.penempatan.index` dan `admin.arsip.index` serta menu navigasi di sidebar tetap ada dan tidak diubah.
  3. **Persetujuan Perubahan Blade:** Sesuai aturan proyek (`.agents/rules/AGENTS.md` bagian 3.3), penghapusan blok markup di `resources/views/admin/surat-selesai/index.blade.php` memerlukan persetujuan eksplisit pengguna sebelum eksekusi.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Implementasi
1. **Opsi A (Direkomendasikan): Membungkus `.adm-hero-actions` dengan `@if(!empty($masukPenempatan) || !empty($masukPendaftaran))`**
   - Menghapus cabang `@else` beserta kedua tombol `Data Penempatan` dan `Arsip Alumni`, sekaligus hanya merender `<div class="adm-hero-actions">` saat berada di mode fokus individu atau kelompok.
   - **Kelebihan:** Tidak menyisakan elemen `<div class="adm-hero-actions"></div>` kosong di DOM pada halaman daftar utama, menjaga layout hero banner tetap bersih.
   - **Kekurangan:** Tidak ada.

2. **Opsi B: Hanya Menghapus Kedua Tag `<a>` dan Membiarkan `.adm-hero-actions` Kosong**
   - **Kelebihan:** Diff lebih sedikit 2 baris.
   - **Kekurangan:** Menyisakan container `.adm-hero-actions` kosong di DOM yang bisa memengaruhi spacing flex pada hero banner.

**Keputusan:** Memilih **Opsi A** agar DOM bersih dan layout `.adm-hero-banner` tetap proporsional.

---

## 4. Perubahan File

| File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/surat-selesai/index.blade.php` | Modify | Menghapus tombol `Data Penempatan` dan `Arsip Alumni` (baris 77–93) serta membungkus `.adm-hero-actions` agar hanya tampil pada mode fokus individu/kelompok. |
| `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` | Modify | Menambahkan assertion `assertDontSee('Data Penempatan')` dan `assertDontSee('Arsip Alumni')` pada `test_admin_dapat_mengakses_halaman_surat_selesai_index`. |

---

## 5. Langkah Implementasi

- [x] **Persetujuan Perubahan Blade**: Memastikan persetujuan eksplisit user untuk menghapus tombol `Data Penempatan` dan `Arsip Alumni` di `resources/views/admin/surat-selesai/index.blade.php`.
- [x] **Update Markup `resources/views/admin/surat-selesai/index.blade.php`**:
  - Bungkus `<div class="adm-hero-actions">` dengan `@if(!empty($masukPenempatan) || !empty($masukPendaftaran))` dan hapus cabang `@else` yang berisi tombol `Data Penempatan` dan `Arsip Alumni`.
- [x] **Update Feature Test**:
  - Tambahkan `assertDontSee('Data Penempatan')` dan `assertDontSee('Arsip Alumni')` pada `test_admin_dapat_mengakses_halaman_surat_selesai_index` di `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php`.
- [x] **Quality Gate & Verifikasi**:
  - Jalankan `php artisan test --filter=AdminSuratSelesai` dan `./vendor/bin/pint --test tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php`.

---

## 6. Strategi Testing
- **Feature Test (`php artisan test --filter=AdminSuratSelesai`):**
  - Halaman `admin.surat-selesai.index` tidak menampilkan `Data Penempatan` dan `Arsip Alumni` di hero banner.
  - Mode fokus individu dan kelompok tetap menampilkan tombol `Kembali ke Daftar Peserta` dan `Kembali ke Daftar Kelompok`.
- **Code Style:**
  - `./vendor/bin/pint --test` pada file PHP yang diubah lulus.

---

## 7. Risiko & Mitigasi
- **Risiko:** Pengguna kehilangan shortcut ke Penempatan / Arsip dari hero banner Surat Selesai.  
  *Mitigasi:* Kedua menu tersebut tetap dapat diakses melalui sidebar navigasi Admin.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Tombol `Data Penempatan` dan `Arsip Alumni` tidak lagi tampil pada hero banner `admin/surat-selesai/index.blade.php`.
- [x] Tombol kembali pada mode fokus individu & kelompok tetap berfungsi normal.
- [x] `php artisan test --filter=AdminSuratSelesai` dan `./vendor/bin/pint --test` seluruhnya lulus.
