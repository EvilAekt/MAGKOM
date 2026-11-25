# Rencana Kerja: Penambahan Card Biodata Peserta di Atas Formulir Unggah Surat Selesai

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Cakupan:** Halaman Detail Penempatan Peserta Kelompok (`admin.penempatan.show` → partial `_show_kelompok.blade.php`)

---

## 1. Tujuan
Menambahkan kartu **Biodata Peserta** (menampilkan data identitas akademik & kontak peserta, seperti peran dalam kelompok, institusi, NIM/NISN, program studi/jurusan, jenjang, email, dan nomor HP) tepat **di atas** kartu **Formulir Unggah Surat Selesai** pada kolom kanan grid `.focused-content-grid` di `resources/views/admin/penempatan/partials/_show_kelompok.blade.php`. Dengan penempatan ini, tinggi kolom kanan menjadi seimbang dengan kartu **Informasi Penempatan Peserta** di kolom kiri sehingga tidak ada ruang kosong (*empty space*) berlebih di bawah formulir unggah.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis:**
  1. Halaman yang dimaksud adalah tampilan detail penempatan peserta anggota/ketua kelompok pada `resources/views/admin/penempatan/partials/_show_kelompok.blade.php` (yang di-include oleh `resources/views/admin/penempatan/show.blade.php`), sesuai tangkapan layar yang dilampirkan.
  2. Data biodata diambil dari relasi yang sudah di-*eager load* di `PenempatanController@show` (`peserta`, `peserta.profil`, `peserta.profil.jurusan`, serta `pendaftaran.kelompokMagang`), sehingga tidak memerlukan perubahan query atau skema database.
  3. Field biodata yang ditampilkan menggunakan data aman untuk Admin/SuperAdmin (`Institusi`, `NIM/NISN`, `Program Studi / Jurusan`, `Jenjang`, `Email`, `No. HP / Telepon`, serta `Pembimbing Sekolah` bila tersedia) tanpa menampilkan kredensial atau data sensitif.
  4. Perubahan hanya menyentuh tampilan `_show_kelompok.blade.php` dan CSS scoped di `resources/css/pages/admin/surat-selesai-index.css`, tanpa mengubah tampilan `resources/views/admin/surat-selesai/index.blade.php`.
  5. **Persetujuan Perubahan Blade:** Sesuai aturan proyek (`.agents/rules/AGENTS.md` bagian 3.3), perubahan markup di `resources/views/admin/penempatan/partials/_show_kelompok.blade.php` memerlukan persetujuan eksplisit pengguna sebelum eksekusi.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Desain & Tata Letak
1. **Opsi A (Direkomendasikan): Pembungkus Kolom Kanan (`.focused-right-column`) dengan Dua Card Terpisah**
   - Membungkus kolom kanan `.focused-content-grid` dengan container `<div class="focused-right-column">` (`display: flex; flex-direction: column; gap: 1.25rem;`).
   - Di dalamnya terdapat dua `.focused-card` terpisah:
     1. Card **Biodata Peserta** (`.focused-card.biodata-card`) dengan grid 2 kolom ringkas (`.biodata-grid`) untuk menampilkan metadata akademik & kontak peserta.
     2. Card **Formulir Unggah Surat Selesai** (`.focused-card.upload-card`) tepat di bawahnya.
   - **Kelebihan:** Konsisten dengan design token & gaya `.focused-card` yang ada; pemisahan tanggung jawab visual jelas (informasi read-only terpisah dari form aksi); mudah diatur keseimbangan tingginya terhadap `.info-card` di sebelah kiri; tidak memengaruhi halaman `admin/surat-selesai/index.blade.php`.
   - **Kekurangan:** Menambah satu wrapper `<div>` di kolom kanan partial `_show_kelompok.blade.php`.

2. **Opsi B: Menggabungkan Biodata dan Formulir Unggah dalam Satu `.focused-card`**
   - Menaruh section biodata dan dropzone upload dalam satu card yang sama dengan pemisah garis horizontal.
   - **Kelebihan:** Tidak perlu wrapper kolom tambahan.
   - **Kekurangan:** Card kanan menjadi terlalu padat dan mencampurkan blok informasi read-only dengan formulir unggah berkas, serta kurang selaras dengan struktur modular `.focused-card`.

**Keputusan:** Memilih **Opsi A** karena menjaga konsistensi visual, pemisahan fungsi antar-card, dan keseimbangan tinggi kolom kiri-kanan pada desktop maupun responsif mobile (`<= 900px`).

---

## 4. Perubahan File

| File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/penempatan/partials/_show_kelompok.blade.php` | Modify | Membungkus kolom kanan dengan `.focused-right-column`, menambahkan card **Biodata Peserta** di atas card **Formulir Unggah Surat Selesai**, serta merapikan tata letak tanpa inline CSS/JS baru. |
| `resources/css/pages/admin/surat-selesai-index.css` | Modify | Menambahkan style scoped `.focused-right-column`, `.focused-card.biodata-card`, `.biodata-grid`, dan penyesuaian `.focused-content-grid` (`align-items: stretch` / proporsi kolom kanan) agar tinggi kolom kiri dan kanan seimbang serta responsif. |
| `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` | Modify | Menambahkan assertion bahwa halaman detail penempatan kelompok menampilkan blok **Biodata Peserta** (institusi, NIM/NISN, jurusan, email/kontak) bersama **Formulir Unggah Surat Selesai**. |

---

## 5. Langkah Implementasi

- [x] **Persetujuan Perubahan Blade**: Memastikan persetujuan eksplisit user atas perubahan markup di `resources/views/admin/penempatan/partials/_show_kelompok.blade.php`.
- [x] **Update Markup Partial `_show_kelompok.blade.php`**:
  - Bungkus kolom kanan di dalam `.focused-content-grid` menggunakan `<div class="focused-right-column">`.
  - Tambahkan card `<div class="focused-card biodata-card">` di posisi pertama kolom kanan dengan judul `{{ __('Biodata Peserta') }}` dan badge peran (`Ketua Kelompok` / `Anggota Tim`).
  - Tampilkan field biodata secara ringkas dalam `.biodata-grid` (2 kolom pada desktop, 1 kolom pada layar sempit): `Institusi / Asal`, `NIM / NISN`, `Program Studi / Jurusan`, `Jenjang`, `Email`, `No. HP / Telepon`, dan `Pembimbing Sekolah` (dengan fallback `'-'` bila kosong dan escaping `{{ }}`).
  - Pertahankan card `.focused-card.upload-card` (`Formulir Unggah Surat Selesai`) tepat di bawah card biodata tanpa mengubah `action`, `@csrf`, `name="file_surat"`, `id="file_surat_kel_show"`, maupun logika `@if($statusVal === 'selesai' && !auth()->user()?->isSuperAdmin())`.
- [x] **Update Styling di `resources/css/pages/admin/surat-selesai-index.css`**:
  - Tambahkan `.focused-right-column` (`display: flex; flex-direction: column; gap: 1.25rem; min-width: 0;`).
  - Tambahkan `.focused-card.biodata-card` dan `.biodata-grid` (`display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 16px;`) dengan media query `@media (max-width: 640px)` menjadi 1 kolom.
  - Atur `.focused-right-column .upload-card` (`flex: 1;` atau padding proporsional) dan `.focused-content-grid` (`align-items: stretch;`) agar bagian bawah card **Formulir Unggah Surat Selesai** sejajar dengan bagian bawah card **Informasi Penempatan Peserta** tanpa menyisakan ruang kosong jomplang.
  - Pastikan selector tidak merusak layout `.focused-content-grid` di `resources/views/admin/surat-selesai/index.blade.php`.
- [x] **Update Feature Test**:
  - Tambahkan skenario verifikasi di `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` untuk memastikan **Biodata Peserta** ter-render pada `route('admin.penempatan.show', ...)` untuk peserta kelompok (ketua/anggota) beserta data profilnya.
- [x] **Quality Gate & Verifikasi**:
  - Jalankan `npm run build` untuk memastikan kompilasi Vite sukses.
  - Jalankan `php artisan test --filter=AdminSuratSelesai` dan `./vendor/bin/pint --test` (dengan PHP 8.3).

---

## 6. Strategi Testing
- **Feature Test (`php artisan test --filter=AdminSuratSelesai`):**
  - *Happy Path:* Mengakses `admin.penempatan.show` untuk peserta kelompok (ketua dan anggota), memastikan teks `Biodata Peserta`, `NIM/NISN`, `Institusi`, `Jurusan`, `Email`, dan `Formulir Unggah Surat Selesai` muncul dengan benar.
  - *Fallback / Sad Path:* Peserta dengan data `PesertaProfil` minimal/kosong (`no_hp` atau `nama_pembimbing_sekolah` null) tetap ter-render aman dengan fallback `-` tanpa error 500.
  - *Regression Check:* Halaman `admin.surat-selesai.index` (tab individu & kelompok) tetap lolos test tanpa regresi layout atau assertion.
- **Asset Build & Code Style:**
  - `npm run build` berjalan tanpa error.
  - `./vendor/bin/pint --test` lolos tanpa pelanggaran formatting.

---

## 7. Risiko & Mitigasi
- **Risiko 1 — Regresi Layout pada `admin/surat-selesai/index.blade.php`:**  
  Karena `surat-selesai-index.css` dipakai bersama oleh `admin/penempatan/show.blade.php` dan `admin/surat-selesai/index.blade.php`, perubahan langsung pada `.focused-content-grid` atau `.upload-card` berisiko mengubah tampilan halaman lain.  
  *Mitigasi:* Gunakan class khusus `.focused-right-column` dan scoping selector `.focused-right-column .upload-card` sehingga halaman lain tidak terdampak.
- **Risiko 2 — Ketimpangan Tinggi Saat Status Belum `selesai`:**  
  Ketika `$statusVal !== 'selesai'`, `upload-card` hanya menampilkan `.notice-non-selesai-box` yang lebih pendek.  
  *Mitigasi:* Dengan `align-items: stretch` pada `.focused-content-grid` dan `flex: 1` pada `.focused-right-column .upload-card`, tinggi card kanan tetap sejajar rapi dengan card kiri di segala status.
- **Risiko 3 — N+1 Query atau Data Null:**  
  Mengakses relasi yang belum di-load atau properti null pada `$profil`.  
  *Mitigasi:* Gunakan relasi yang sudah di-eager-load di `PenempatanController@show` (`peserta.profil.jurusan`) dan gunakan null-safe operator (`?->`) serta fallback `'-'`.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Card **Biodata Peserta** tampil di atas card **Formulir Unggah Surat Selesai** pada kolom kanan `_show_kelompok.blade.php`.
- [x] Tinggi kolom kanan (Biodata + Formulir Unggah) sejajar dan proporsional dengan card **Informasi Penempatan Peserta** di kolom kiri (tidak ada ruang kosong berlebih).
- [x] Tidak ada CSS atau JS inline baru yang ditulis di file Blade; seluruh styling berada di `resources/css/pages/admin/surat-selesai-index.css`.
- [x] `npm run build`, `php artisan test --filter=AdminSuratSelesai`, dan `./vendor/bin/pint --test` seluruhnya lulus.
