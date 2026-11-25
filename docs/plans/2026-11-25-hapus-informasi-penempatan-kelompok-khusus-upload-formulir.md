# Rencana Kerja: Hapus Card Informasi Penempatan Kelompok (Khusus Formulir Unggah Surat Selesai)

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Cakupan:** Halaman Surat Selesai Magang — Mode Fokus Kelompok (`admin.surat-selesai.index` dengan `tab=kelompok` & `masuk_kelompok`)

---

## 1. Tujuan
Menghapus kartu **Informasi Penempatan Kelompok** (kolom kiri) pada tampilan mode fokus kelompok di `resources/views/admin/surat-selesai/index.blade.php`, sehingga area di atas tabel **Daftar Anggota Kelompok** hanya menampilkan **Formulir Unggah Surat Selesai** secara penuh (*full-width*) dan ringkas tanpa ruang kolom kiri yang kosong.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis:**
  1. Halaman yang dimaksud adalah mode fokus kelompok pada `resources/views/admin/surat-selesai/index.blade.php` (baris 491–590), sesuai tangkapan layar yang dilampirkan (`Unggah Surat Selesai: ...`, tombol `Kembali ke Daftar Kelompok`, card `Informasi Penempatan Kelompok`, card `Formulir Unggah Surat Selesai`, dan tabel `Daftar Anggota Kelompok`).
  2. Mode fokus **individu** (`tab=individu` & `masuk_individu`) serta tampilan detail peserta di `admin/penempatan/partials/_show_kelompok.blade.php` **tidak** diubah.
  3. Di dalam card `Informasi Penempatan Kelompok` sebelumnya terdapat kotak `Status Surat Selesai Tim` (yang berisi tombol `Unduh Salinan Surat Tim (PDF)` saat berkas sudah terbit). Agar fungsi unduh cepat tetap tersedia bila berkas tim sudah terbit, tombol unduh ringkas dapat diletakkan di header card `Formulir Unggah Surat Selesai` (hanya muncul saat `$hasSuratTim && $primaryPenempatan`), sedangkan formulir utama tetap fokus pada unggah/ganti PDF.
  4. **Persetujuan Perubahan Blade:** Sesuai aturan proyek (`.agents/rules/AGENTS.md` bagian 3.3), penghapusan blok markup di `resources/views/admin/surat-selesai/index.blade.php` memerlukan persetujuan eksplisit pengguna sebelum eksekusi.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Tata Letak
1. **Opsi A (Direkomendasikan): Hapus Card Kiri & Jadikan Grid 1 Kolom Full-Width (`.focused-content-grid--single`)**
   - Menghapus blok `<div class="focused-card info-card">...</div>` pada mode fokus kelompok di `resources/views/admin/surat-selesai/index.blade.php`.
   - Menambahkan modifier CSS `.focused-content-grid--single { grid-template-columns: 1fr; }` di `resources/css/pages/admin/surat-selesai-index.css` agar card `Formulir Unggah Surat Selesai` membentang penuh sejajar dengan lebar banner identitas di atasnya dan tabel anggota di bawahnya.
   - **Kelebihan:** Menghemat ruang vertikal, menghilangkan duplikasi informasi (nama kelompok & bidang sudah ada di banner identitas di atasnya), tidak memengaruhi mode fokus individu maupun `_show_kelompok.blade.php`.
   - **Kekurangan:** Mengubah 1 assertion test eksisting (`assertSee('Informasi Penempatan Kelompok')` → `assertDontSee('Informasi Penempatan Kelompok')`).

2. **Opsi B: Mengganti Container `.focused-content-grid` dengan `.compact-upload-card` Horizontal**
   - Mengubah markup form dropzone menjadi baris kontrol horizontal seperti `.compact-upload-card`.
   - **Kelebihan:** Lebih pendek secara vertikal.
   - **Kekurangan:** Mengubah UX dropzone drag-and-drop yang sudah ada dan berpotensi memengaruhi selector JS yang terkait `.upload-dropzone-panel`.

**Keputusan:** Memilih **Opsi A** karena menjaga UX dropzone eksisting, tetap konsisten dengan desain `.focused-card.upload-card`, dan merupakan perubahan paling minimal serta aman.

---

## 4. Perubahan File

| File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/surat-selesai/index.blade.php` | Modify | Menghapus blok card `Informasi Penempatan Kelompok` pada mode fokus kelompok (baris 493–541), mengubah wrapper menjadi `.focused-content-grid.focused-content-grid--single`, serta memindahkan tombol unduh ringkas (jika `$hasSuratTim`) ke header card upload bila diperlukan. |
| `resources/css/pages/admin/surat-selesai-index.css` | Modify | Menambahkan class `.focused-content-grid--single { grid-template-columns: 1fr; }` yang scoped agar tidak memengaruhi grid 2 kolom di halaman lain. |
| `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` | Modify | Memperbarui `test_admin_dapat_masuk_ke_mode_fokus_kelompok_dan_melihat_tabel_anggota_mandiri_tier_2` agar memverifikasi `assertDontSee('Informasi Penempatan Kelompok')` dan tetap `assertSee('Formulir Unggah Surat Selesai')`. |

---

## 5. Langkah Implementasi

- [x] **Persetujuan Perubahan Blade**: Memastikan persetujuan eksplisit user untuk menghapus blok `Informasi Penempatan Kelompok` di `resources/views/admin/surat-selesai/index.blade.php`.
- [x] **Update Markup `resources/views/admin/surat-selesai/index.blade.php`**:
  - Hapus blok `<div class="focused-card info-card">` (`Informasi Penempatan Kelompok` dan `Status Surat Selesai Tim`) pada section mode fokus kelompok.
  - Tambahkan class `focused-content-grid--single` pada `.focused-content-grid` di section tersebut.
  - Pertahankan seluruh atribut form unggah (`action`, `@csrf`, `name="file_surat"`, `id="file_surat_kel"`, `#previewFileNameKel`, dan tombol submit) tanpa perubahan kontrak.
- [x] **Update Styling di `resources/css/pages/admin/surat-selesai-index.css`**:
  - Tambahkan `.focused-content-grid--single { grid-template-columns: 1fr; }`.
- [x] **Update Feature Test**:
  - Perbarui `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` baris 308 menjadi `$response->assertDontSee('Informasi Penempatan Kelompok');`.
- [x] **Quality Gate & Verifikasi**:
  - Jalankan `npm run build`.
  - Jalankan `php artisan test --filter=AdminSuratSelesai` dan `./vendor/bin/pint --test` pada file PHP yang diubah.

---

## 6. Strategi Testing
- **Feature Test (`php artisan test --filter=AdminSuratSelesai`):**
  - Mode fokus kelompok (`tab=kelompok&masuk_kelompok=...`) merender `Formulir Unggah Surat Selesai`, `Unggah Surat Resmi`, dan `Daftar Anggota Kelompok`, serta **tidak** merender `Informasi Penempatan Kelompok`.
  - Upload surat selesai kelompok tetap mendistribusikan file ke seluruh anggota tim.
  - Mode fokus individu dan `admin.penempatan.show` tetap berjalan tanpa regresi.
- **Asset Build & Code Style:**
  - `npm run build` dan `./vendor/bin/pint --test` lulus.

---

## 7. Risiko & Mitigasi
- **Risiko 1 — Hilangnya Akses Unduh Surat Tim di Atas Tabel:**  
  Saat `Informasi Penempatan Kelompok` dihapus, tombol `Unduh Salinan Surat Tim (PDF)` di kotak kuning/hijau ikut terhapus.  
  *Mitigasi:* Setiap baris di tabel `Daftar Anggota Kelompok` di bawahnya sudah memiliki tombol `Unduh`, dan bila diinginkan dapat ditambahkan tombol unduh ringkas di header card `Formulir Unggah Surat Selesai` saat `$hasSuratTim` bernilai `true`.
- **Risiko 2 — Regresi Layout Grid 2 Kolom Lain:**  
  *Mitigasi:* Menggunakan modifier `.focused-content-grid--single` khusus di section fokus kelompok agar mode fokus individu dan `_show_kelompok.blade.php` tetap 2 kolom.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Card **Informasi Penempatan Kelompok** tidak lagi tampil pada mode fokus kelompok di `admin/surat-selesai/index.blade.php`.
- [x] Card **Formulir Unggah Surat Selesai** tampil penuh (*full-width*) dan proporsional di atas tabel **Daftar Anggota Kelompok**.
- [x] `npm run build`, `php artisan test --filter=AdminSuratSelesai`, dan `./vendor/bin/pint --test` seluruhnya lulus.
