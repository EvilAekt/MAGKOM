# Rencana Kerja: Pemindahan Card Foto & Nama ke Atas Informasi Penempatan (Mode Fokus Surat Selesai)

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Cakupan:** Mode Fokus Surat Selesai Individu (`resources/views/admin/surat-selesai/index.blade.php`), Mode Fokus Anggota Kelompok (`resources/views/admin/penempatan/partials/_show_kelompok.blade.php`), dan styling (`resources/css/pages/admin/surat-selesai-index.css`)

---

## 1. Tujuan
Menghapus bar identitas penuh di atas grid (`.focused-identity-bar` yang memuat foto, nama, dan chip status `SURAT SUDAH TERBIT` / `SURAT TERBIT`) serta memindahkan card foto & nama peserta ke dalam **kolom kiri** tepat di atas card **Informasi Penempatan**, sehingga posisi card Informasi Penempatan turun dan ruang kosong di bawahnya menjadi proporsional terhadap kolom kanan (*Biodata Peserta* + *Berkas Surat Selesai*).

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis:**
  1. Perubahan diterapkan pada **kedua** tampilan fokus agar konsisten:
     - Mode Fokus Individu di `resources/views/admin/surat-selesai/index.blade.php`
     - Mode Fokus Anggota Kelompok di `resources/views/admin/penempatan/partials/_show_kelompok.blade.php`
  2. Pada card foto & nama yang dipindahkan ke kolom kiri, hanya menampilkan **foto avatar + nama peserta** (dan pada mode kelompok tetap menjaga konteks ringkas atau mengikuti gaya bersih foto + nama tanpa chip `SURAT TERBIT`, karena status surat sudah tampil pada card *Berkas Surat Selesai Terbit* / *Formulir Unggah* di kolom kanan).
  3. Chip status `SURAT SUDAH TERBIT` / `SURAT TERBIT` / `BELUM DIUNGGAH` ikut dihapus bersama bar atas sesuai permintaan.
  4. **Persetujuan Perubahan Blade:** Sesuai `.agents/rules/AGENTS.md` bagian 3.3, pemindahan struktur HTML ini memerlukan perubahan markup Blade dan persetujuan eksplisit pengguna sebelum eksekusi.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Implementasi
1. **Opsi A (Direkomendasikan): Membuat `.focused-left-column` Simetris dengan `.focused-right-column`**
   - Menghapus `.focused-identity-bar` di atas `.focused-content-grid`.
   - Membungkus isi kolom kiri dalam `<div class="focused-left-column">` (dengan `display: flex; flex-direction: column; gap: 1.25rem;` — identik dengan `.focused-right-column`).
   - Menempatkan card `.focused-card.focused-profile-card` (berisi avatar + nama peserta) di urutan pertama kolom kiri, diikuti `.focused-card.info-card` (`Informasi Penempatan`) di bawahnya dengan `flex: 1` (opsional/proporsional agar sejajar rapi dengan kolom kanan).
   - **Kelebihan:** Struktur DOM bersih dan simetris antara kolom kiri dan kanan, responsif otomatis pada `@media (max-width: 900px)`, tanpa CSS hack atau duplikasi elemen.
   - **Kekurangan:** Memerlukan penyesuaian markup pada 2 file Blade.

2. **Opsi B: Memanfaatkan CSS Grid Area / `order` tanpa Wrapper Kolom Kiri**
   - Menjadikan `.focused-content-grid` memiliki 2 baris eksplisit.
   - **Kelebihan:** Tidak menambah wrapper `div`.
   - **Kekurangan:** Tinggi baris pertama akan memaksa card *Biodata Peserta* di kanan mengikuti tinggi card profil yang kecil atau menyisakan celah vertikal yang sulit dikontrol saat konten dinamis.

**Keputusan:** Memilih **Opsi A** karena paling rapi, simetris dengan `.focused-right-column`, dan mudah dipelihara.

---

## 4. Perubahan File

| File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/surat-selesai/index.blade.php` | Modify | Menghapus `.focused-identity-bar` di atas grid fokus individu; membungkus kolom kiri dengan `.focused-left-column` berisi `.focused-card.focused-profile-card` (foto + nama) di atas `.focused-card.info-card`. |
| `resources/views/admin/penempatan/partials/_show_kelompok.blade.php` | Modify | Menghapus `.focused-identity-bar` di atas grid fokus anggota kelompok; membungkus kolom kiri dengan `.focused-left-column` berisi `.focused-card.focused-profile-card` (foto + nama) di atas `.focused-card.info-card`. |
| `resources/css/pages/admin/surat-selesai-index.css` | Modify | Menambahkan rule `.focused-left-column`, `.focused-profile-card`, serta penyesuaian `.focused-left-column .info-card` (`flex: 1`) agar kolom kiri dan kanan seimbang. |

---

## 5. Langkah Implementasi

- [ ] **Persetujuan Perubahan Blade**: Memastikan persetujuan eksplisit pengguna untuk mengubah struktur markup pada `index.blade.php` dan `_show_kelompok.blade.php`.
- [ ] **Update `resources/css/pages/admin/surat-selesai-index.css`**:
  - Tambahkan `.focused-left-column` (`display: flex; flex-direction: column; gap: 1.25rem; min-width: 0;`).
  - Tambahkan `.focused-card.focused-profile-card` untuk tampilan kartu foto avatar + nama peserta yang ringkas dan proporsional.
  - Atur `.focused-left-column .info-card { flex: 1; }` agar bagian bawah kolom kiri sejajar dengan kolom kanan tanpa ruang kosong berlebih.
- [ ] **Update `resources/views/admin/surat-selesai/index.blade.php`** (Mode Fokus Individu):
  - Hapus blok `.focused-identity-bar`.
  - Bungkus kolom kiri dengan `<div class="focused-left-column">` yang berisi `.focused-card.focused-profile-card` (avatar + nama `$peserta->nama`) di atas `.focused-card.info-card`.
- [ ] **Update `resources/views/admin/penempatan/partials/_show_kelompok.blade.php`** (Mode Fokus Kelompok):
  - Hapus blok `.focused-identity-bar`.
  - Bungkus kolom kiri dengan `<div class="focused-left-column">` yang berisi `.focused-card.focused-profile-card` (avatar + nama `$peserta->nama`) di atas `.focused-card.info-card`.
- [ ] **Quality Gate & Verifikasi**:
  - Jalankan `npm run build`.
  - Jalankan `C:\Users\galan\php83\php.exe artisan test --filter=AdminSuratSelesai`.
  - Jalankan `C:\Users\galan\php83\php.exe vendor/bin/pint --test` pada file Blade yang diubah.

---

## 6. Strategi Testing
- **Asset Build:** `npm run build` berhasil tanpa error.
- **Feature Test (`php artisan test --filter=AdminSuratSelesai`):** Memastikan seluruh 22 skenario test pada `AdminSuratSelesaiIndividuKelompokTest` dan `AdminSuratSelesaiKonektivitasEndToEndTest` tetap lulus (tidak ada teks/elemen wajib test yang hilang).
- **Pengecekan Regresi:** Memastikan tidak ada test lain yang bergantung pada teks `SURAT TERBIT` / `SURAT SUDAH TERBIT` di `.focused-identity-bar`.

---

## 7. Risiko & Mitigasi
- **Risiko:** Ada test fitur yang meng-assert keberadaan teks `SURAT TERBIT` atau `BELUM DIUNGGAH` pada halaman fokus.  
  *Mitigasi:* Verifikasi dengan `php artisan test --filter=AdminSuratSelesai` setelah perubahan; jika ada assertion terkait bar atas yang dihapus, sesuaikan hanya setelah konfirmasi.
- **Risiko:** Avatar peserta kelompok yang sebelumnya menggunakan inline `style` pada `_show_kelompok.blade.php`.  
  *Mitigasi:* Gunakan class `.mentee-avatar-circle` yang sudah tersedia di `surat-selesai-index.css` agar konsisten tanpa inline style.

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] Card `.focused-identity-bar` di atas grid sudah dihapus pada mode fokus individu dan kelompok.
- [ ] Card foto & nama peserta tampil di kolom kiri tepat di atas card Informasi Penempatan.
- [ ] Tinggi kolom kiri dan kolom kanan tampak seimbang tanpa ruang kosong berlebih di bawah Informasi Penempatan.
- [ ] `npm run build` dan seluruh test `AdminSuratSelesai` lulus pada PHP 8.3.
