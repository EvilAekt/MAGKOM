# Rencana Kerja: Hapus Formulir Unggah Surat Selesai pada Mode Fokus Kelompok

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Cakupan:** Halaman Surat Selesai Magang — Mode Fokus Kelompok (`resources/views/admin/surat-selesai/index.blade.php` baris 791–841)

---

## 1. Tujuan
Menghapus blok card **Formulir Unggah Surat Selesai** (`.focused-content-grid--single` beserta `.focused-card.upload-card`) dari tampilan **Mode Fokus Kelompok** (`tab=kelompok&masuk_kelompok=...`) di `resources/views/admin/surat-selesai/index.blade.php`, sehingga halaman fokus kelompok langsung menampilkan header identitas tim dan tabel **Daftar Anggota Kelompok** (di mana admin dapat mengklik `Detail` pada anggota untuk masuk ke halaman `_show_kelompok.blade.php` yang memiliki Biodata Peserta & Formulir Unggah).

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis:**
  1. Penghapusan ini **khusus** pada Mode Fokus Kelompok (`masuk_kelompok`) di `resources/views/admin/surat-selesai/index.blade.php`, sesuai dua tangkapan layar yang dilampirkan (`Pilih Berkas PDF Surat Selesai Tim` & `Daftar Anggota Kelompok`).
  2. Formulir Unggah Surat Selesai pada **Mode Fokus Individu** (`masuk_individu`) dan pada halaman **Detail Penempatan Peserta** (`_show_kelompok.blade.php`) tetap dipertahankan.
  3. Endpoint `POST admin.penempatan.surat-selesai.upload` dan distribusi otomatis ke seluruh anggota tim tetap dipertahankan (karena masih digunakan oleh `_show_kelompok.blade.php`).
  4. **Persetujuan Perubahan Blade:** Sesuai aturan proyek (`.agents/rules/AGENTS.md` bagian 3.3), penghapusan blok markup di `resources/views/admin/surat-selesai/index.blade.php` memerlukan persetujuan eksplisit pengguna sebelum eksekusi.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Implementasi
1. **Opsi A (Direkomendasikan): Hapus Blok `.focused-content-grid--single` di Mode Fokus Kelompok & Perbarui Subtitle Tabel**
   - Menghapus baris 791–841 (`.focused-content-grid.focused-content-grid--single` + `.focused-card.upload-card`) pada `@elseif(!empty($masukPendaftaran))` di `resources/views/admin/surat-selesai/index.blade.php`.
   - Memperbarui subtitle tabel `Daftar Anggota Kelompok` agar tidak lagi menyebut *"surat selesai yang diunggah di atas"*, melainkan *"Tabel khusus rincian anggota kelompok — klik Detail pada anggota untuk mengelola atau mengunggah surat selesai tim."*
   - **Kelebihan:** Perubahan bersih, minimal, tidak memengaruhi Mode Fokus Individu maupun `_show_kelompok.blade.php`, dan tidak merusak endpoint upload.
   - **Kekurangan:** Perlu memperbarui 2 assertion test pada `test_admin_dapat_masuk_ke_mode_fokus_kelompok_dan_melihat_tabel_anggota_mandiri_tier_2`.

2. **Opsi B: Menyembunyikan Card Upload dengan CSS `display: none`**
   - **Kelebihan:** Tidak mengubah markup Blade.
   - **Kekurangan:** Melanggar prinsip kebersihan DOM & meninggalkan form upload tersembunyi yang tidak diperlukan di halaman.

**Keputusan:** Memilih **Opsi A** agar markup bersih dan konsisten.

---

## 4. Perubahan File

| File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/surat-selesai/index.blade.php` | Modify | Menghapus blok `.focused-content-grid--single` (card `Formulir Unggah Surat Selesai` tim) pada Mode Fokus Kelompok (baris 791–841) dan menyesuaikan teks subtitle tabel `Daftar Anggota Kelompok`. |
| `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` | Modify | Memperbarui `test_admin_dapat_masuk_ke_mode_fokus_kelompok_dan_melihat_tabel_anggota_mandiri_tier_2` agar memverifikasi `assertDontSee('Formulir Unggah Surat Selesai')` dan `assertDontSee('Unggah Surat Resmi')`. |

---

## 5. Langkah Implementasi

- [x] **Persetujuan Perubahan Blade**: Memastikan persetujuan eksplisit user untuk menghapus blok `Formulir Unggah Surat Selesai` pada Mode Fokus Kelompok di `resources/views/admin/surat-selesai/index.blade.php`.
- [x] **Update Markup `resources/views/admin/surat-selesai/index.blade.php`**:
  - Hapus blok `.focused-content-grid.focused-content-grid--single` beserta `.focused-card.upload-card` pada section `@elseif(!empty($masukPendaftaran))`.
  - Perbarui subtitle panel `Daftar Anggota Kelompok`.
- [x] **Update Feature Test**:
  - Perbarui `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` baris 333–334 menjadi `assertDontSee('Formulir Unggah Surat Selesai')` dan `assertDontSee('Unggah Surat Resmi')`.
- [x] **Quality Gate & Verifikasi**:
  - Jalankan `php artisan test --filter=AdminSuratSelesai` dan `./vendor/bin/pint --test tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php`.

---

## 6. Strategi Testing
- **Feature Test (`php artisan test --filter=AdminSuratSelesai`):**
  - Mode fokus kelompok (`tab=kelompok&masuk_kelompok=...`) tetap menampilkan header tim dan `Daftar Anggota Kelompok`, serta **tidak** menampilkan `Formulir Unggah Surat Selesai` maupun `Unggah Surat Resmi`.
  - Mode fokus individu dan `admin.penempatan.show` (`_show_kelompok.blade.php`) tetap menampilkan `Formulir Unggah Surat Selesai` dan seluruh fungsi upload/download tim tetap lulus 100%.

---

## 7. Risiko & Mitigasi
- **Risiko:** Admin ingin mengunggah surat selesai tim dari halaman fokus kelompok.  
  *Mitigasi:* Setiap baris pada tabel `Daftar Anggota Kelompok` memiliki tombol `Detail` yang langsung mengarah ke halaman detail peserta (`_show_kelompok.blade.php`), tempat **Biodata Peserta** dan **Formulir Unggah Surat Selesai** berada (dan 1x unggah tetap otomatis tersinkronisasi ke seluruh anggota tim).

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Card **Formulir Unggah Surat Selesai** tidak lagi tampil pada Mode Fokus Kelompok di `admin/surat-selesai/index.blade.php`.
- [x] Mode Fokus Individu dan `admin.penempatan.show` tetap berfungsi normal.
- [x] `php artisan test --filter=AdminSuratSelesai` dan `./vendor/bin/pint --test` seluruhnya lulus.
