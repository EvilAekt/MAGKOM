# Rencana Kerja: Penyelarasan Tampilan Mode Fokus Individu dengan Tampilan Kelompok (Card Biodata Peserta)

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Cakupan:** Halaman Surat Selesai Magang — Mode Fokus Individu (`admin.surat-selesai.index` dengan `tab=individu` & `masuk_individu`)

---

## 1. Tujuan
Menyelaraskan tata letak tampilan **Mode Fokus Individu** di `resources/views/admin/surat-selesai/index.blade.php` agar sama persis dengan tampilan detail peserta di `resources/views/admin/penempatan/partials/_show_kelompok.blade.php`:
1. Menghapus baris teks sub-identitas (`Institusi • NIM/NISN • Jurusan`) di header `.focused-identity-bar`.
2. Menambahkan card **Biodata Peserta** di atas card **Formulir Unggah Surat Selesai** pada kolom kanan (`.focused-right-column`) agar tinggi kolom kanan seimbang dengan card **Informasi Penempatan Magang** di kolom kiri tanpa ruang kosong berlebih.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis:**
  1. Halaman yang dimaksud adalah Mode Fokus Individu pada `resources/views/admin/surat-selesai/index.blade.php` (baris 157–288), sesuai tangkapan layar pertama yang dilampirkan (`Unggah Surat Selesai: Aditya Pratama`, `Kembali ke Daftar Peserta`).
  2. Relasi yang dibutuhkan (`peserta.profil.jurusan`, `bidang`, `pembimbing`, `pendaftaran`) sudah di-*eager load* di `SuratSelesaiController@index` (baris 67), sehingga tidak diperlukan perubahan controller maupun query database.
  3. Class CSS yang diperlukan (`.focused-content-grid--balanced`, `.focused-right-column`, `.biodata-card-header`, `.biodata-grid`, `.biodata-item`) sudah tersedia di `resources/css/pages/admin/surat-selesai-index.css`, sehingga dapat langsung digunakan ulang tanpa duplikasi CSS.
  4. **Persetujuan Perubahan Blade:** Sesuai aturan proyek (`.agents/rules/AGENTS.md` bagian 3.3), perubahan markup di `resources/views/admin/surat-selesai/index.blade.php` memerlukan persetujuan eksplisit pengguna sebelum eksekusi.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Implementasi
1. **Opsi A (Direkomendasikan): Gunakan Langsung Class Layout yang Sudah Ada di `index.blade.php`**
   - Menghapus `<p class="focused-identity-sub">` pada header mode fokus individu.
   - Menambahkan `Nama Peserta` pada `.info-list` di card kiri (`Informasi Penempatan Magang`) agar setara dengan `_show_kelompok.blade.php`.
   - Menambahkan modifier `.focused-content-grid--balanced` dan membungkus kolom kanan dengan `<div class="focused-right-column">`, lalu menempatkan `<div class="focused-card biodata-card">` di atas `<div class="focused-card upload-card">`.
   - **Kelebihan:** Memanfaatkan 100% class CSS yang sudah ada di `surat-selesai-index.css` tanpa menambah CSS duplikat, perubahan terfokus di 1 section Blade dan 1 test file.
   - **Kekurangan:** Ada sedikit pengulangan markup card biodata antara `_show_kelompok.blade.php` dan `index.blade.php`.

2. **Opsi B: Mengekstrak Card Biodata Menjadi Blade Partial Terpisah**
   - Membuat file partial baru `resources/views/admin/penempatan/partials/_biodata_card.blade.php` dan meng-`@include`-nya di kedua tempat.
   - **Kelebihan:** Menghilangkan duplikasi markup antar 2 view.
   - **Kekurangan:** Menambah file Blade baru di luar ruang lingkup minimal yang diminta. *(Dapat dijadikan saran lanjutan bila di masa depan ada lebih banyak halaman yang memakai card biodata yang sama).*

**Keputusan:** Memilih **Opsi A** karena paling ringkas, aman, dan langsung memanfaatkan CSS yang sudah teruji di `surat-selesai-index.css`.

---

## 4. Perubahan File

| File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/surat-selesai/index.blade.php` | Modify | Pada section Mode Fokus Individu (baris 157–288): hapus `<p class="focused-identity-sub">`, tambahkan `Nama Peserta` di `.info-list`, gunakan `.focused-content-grid--balanced`, bungkus kolom kanan dengan `.focused-right-column`, dan tambahkan card **Biodata Peserta** di atas **Formulir Unggah Surat Selesai**. |
| `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` | Modify | Tambahkan assertion pada `test_admin_dapat_masuk_ke_mode_fokus_individu_tier_2` untuk memverifikasi `Biodata Peserta`, `Institusi / Asal`, `NIM / NISN`, dan `Formulir Unggah Surat Selesai`. |

---

## 5. Langkah Implementasi

- [x] **Persetujuan Perubahan Blade**: Memastikan persetujuan eksplisit user atas perubahan markup pada Mode Fokus Individu di `resources/views/admin/surat-selesai/index.blade.php`.
- [x] **Update Markup Mode Fokus Individu di `resources/views/admin/surat-selesai/index.blade.php`**:
  - Hapus blok `<p class="focused-identity-sub">...</p>` di `.focused-identity-bar`.
  - Tambahkan item `Nama Peserta` (`{{ $peserta?->nama ?? '-' }}`) pada `.info-list` di card `Informasi Penempatan Magang`.
  - Ubah `.focused-content-grid` menjadi `.focused-content-grid.focused-content-grid--balanced`.
  - Bungkus kolom kanan dengan `<div class="focused-right-column">` dan tambahkan `<div class="focused-card biodata-card">` (badge `Peserta Individu` + 8 field biodata dalam `.biodata-grid`) di atas `<div class="focused-card upload-card">`.
  - Pertahankan seluruh kontrak form unggah (`action`, `@csrf`, `name="file_surat"`, `id="file_surat_ind"`, `#previewFileNameInd`) tanpa perubahan.
- [x] **Update Feature Test**:
  - Tambahkan assertion `Biodata Peserta`, `Institusi / Asal`, dan `NIM / NISN` di `test_admin_dapat_masuk_ke_mode_fokus_individu_tier_2` pada `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php`.
- [x] **Quality Gate & Verifikasi**:
  - Jalankan `npm run build` (bila perlu verifikasi manifest), `php artisan test --filter=AdminSuratSelesai`, dan `./vendor/bin/pint --test tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php`.

---

## 6. Strategi Testing
- **Feature Test (`php artisan test --filter=AdminSuratSelesai`):**
  - *Happy Path:* Mengakses `admin.surat-selesai.index` dengan `tab=individu&masuk_individu=...` menampilkan `Biodata Peserta`, data profil (`Institusi / Asal`, `NIM / NISN`, `Program Studi / Jurusan`, `Jenjang`, `Email`, `No. HP / Telepon`, `Pembimbing Sekolah`, `Alamat`), `Informasi Penempatan Magang`, dan `Formulir Unggah Surat Selesai`.
  - *Regression Check:* Mode fokus kelompok dan `admin.penempatan.show` tetap berjalan 100% lulus.
- **Code Style:**
  - `./vendor/bin/pint --test` pada file PHP yang diubah lulus.

---

## 7. Risiko & Mitigasi
- **Risiko 1 — Properti Profil Null pada Peserta Lama:**  
  *Mitigasi:* Menggunakan null-safe operator (`?->`), pengecekan `instanceof \App\Enums\JenjangEnum`, dan fallback `'-'`.
- **Risiko 2 — Terjadinya Perubahan pada Mode Fokus Kelompok:**  
  *Mitigasi:* Edit hanya menyentuh blok `@if(!empty($masukPenempatan))` (baris 157–288) dan tidak menyentuh blok `@elseif(!empty($masukPendaftaran))`.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Sub-teks di `.focused-identity-bar` Mode Fokus Individu terhapus.
- [x] Card **Biodata Peserta** tampil di atas card **Formulir Unggah Surat Selesai** pada kolom kanan Mode Fokus Individu.
- [x] Tinggi kolom kanan seimbang dengan kolom kiri (`.focused-content-grid--balanced`).
- [x] `php artisan test --filter=AdminSuratSelesai` dan `./vendor/bin/pint --test` lulus seluruhnya.
