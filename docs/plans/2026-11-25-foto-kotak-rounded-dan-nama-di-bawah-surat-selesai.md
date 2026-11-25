# Rencana Kerja: Foto Kotak Rounded Besar & Nama di Bawah Foto (Kolom Kiri Surat Selesai)

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Cakupan:** Styling `.focused-profile-card` dan `.focused-left-column` di `resources/css/pages/admin/surat-selesai-index.css` (tanpa perubahan Blade)

---

## 1. Tujuan
Mengubah tampilan card profil peserta (`.focused-profile-card`) pada kolom kiri mode fokus Surat Selesai (Individu & Kelompok) menjadi **foto kotak besar dengan sudut membulat lembut (rounded rectangle)** dan **nama peserta di bawah foto**, sekaligus menghilangkan ruang kosong berlebih di bawah field *Tanggal Selesai* pada card *Informasi Penempatan*.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis:**
  1. Seluruh perubahan dapat diselesaikan melalui CSS di `resources/css/pages/admin/surat-selesai-index.css` menggunakan selector scoped `.focused-profile-card` dan `.focused-left-column` tanpa menyentuh file Blade.
  2. Sudut membulat lembut menggunakan `border-radius: 16px` (tidak lancip/kotak kaku dan tidak bulat penuh `50%`).
  3. Nama peserta rata tengah tepat di bawah foto (`flex-direction: column; align-items: center; text-align: center;`).
  4. Untuk memastikan tidak ada ruang kosong di bawah *Tanggal Selesai*, `.focused-left-column .info-card` tidak lagi dipaksa meregang (`flex: 1` dihapus/diganti agar tinggi card pas dengan kontennya, atau fleksibilitas ruang berada pada card profil di atasnya).

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Desain
1. **Opsi A (Direkomendasikan): Foto Kotak Rounded Besar (`156px × 156px`, `border-radius: 16px`) + `.info-card` Pas Konten**
   - Mengubah `.focused-card.focused-profile-card` menjadi vertikal rata tengah (`flex-direction: column; align-items: center; text-align: center; gap: 14px; padding: 24px 22px;`).
   - Mengubah `.focused-profile-card .mentee-avatar-circle` menjadi `width: 156px; height: 156px; border-radius: 16px; font-size: 2.25rem; border: 2.5px solid #BFDBFE;`.
   - Menghapus `flex: 1` pada `.focused-left-column .info-card` (atau memindahkan `flex: 1; justify-content: center;` ke `.focused-profile-card`) sehingga bagian bawah card *Informasi Penempatan* pas tepat di bawah *Tanggal Selesai* tanpa ruang putih kosong, sementara total tinggi kolom kiri tetap seimbang dengan kolom kanan.
   - **Kelebihan:** Nol ruang kosong di bawah *Tanggal Selesai*, foto tampil menonjol dengan sudut rounded yang modern, dan tidak memerlukan perubahan Blade.
   - **Kekurangan:** Tidak ada.

2. **Opsi B: Tetap Menggunakan `flex: 1` pada `.info-card` dengan Foto Sangat Besar**
   - Mengandalkan ukuran foto saja untuk menutup ruang kosong pada `.info-card`.
   - **Kelebihan:** Mengikuti perilaku stretch grid.
   - **Kekurangan:** Jika isi kolom kanan sedikit lebih tinggi (misalnya saat panel `replace-upload-panel` dibuka), ruang kosong di bawah *Tanggal Selesai* akan muncul kembali.

**Keputusan:** Memilih **Opsi A** dengan menempatkan `justify-content: center` pada `.focused-profile-card` (dan membiarkan `.info-card` pas mengikuti kontennya) sehingga di bawah *Tanggal Selesai* dijamin tidak ada space kosong berlebih dalam kondisi apa pun.

---

## 4. Perubahan File

| File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/admin/surat-selesai-index.css` | Modify | Mengubah `.focused-card.focused-profile-card` menjadi layout kolom rata tengah (`flex-direction: column; text-align: center;`), mengubah `.focused-profile-card .mentee-avatar-circle` menjadi `156px × 156px` dengan `border-radius: 16px`, dan memindahkan `flex: 1` dari `.info-card` ke `.focused-profile-card` (`justify-content: center`) agar tidak ada ruang kosong di bawah *Tanggal Selesai*. |

---

## 5. Langkah Implementasi

- [ ] **Update `resources/css/pages/admin/surat-selesai-index.css`**:
  - Ganti `.focused-left-column .info-card { flex: 1; }` menjadi `.focused-left-column .focused-profile-card { flex: 1; justify-content: center; }` agar `.info-card` pas mengikuti tinggi kontennya (*Tanggal Selesai* rapat ke bawah).
  - Ubah `.focused-card.focused-profile-card` menjadi `flex-direction: column; align-items: center; text-align: center; gap: 14px; padding: 24px 22px;`.
  - Ubah `.focused-profile-card .mentee-avatar-circle` menjadi `width: 156px; height: 156px; border-radius: 16px; font-size: 2.25rem; border-width: 2.5px;`.
  - Pastikan `.focused-profile-card .focused-identity-text` memiliki `width: 100%;` dan `.focused-profile-card .focused-identity-name` tetap rapi (`font-size: 1.3rem; line-height: 1.3;`).
- [ ] **Quality Gate & Verifikasi**:
  - Jalankan `npm run build`.
  - Jalankan `C:\Users\galan\php83\php.exe artisan test --filter=AdminSuratSelesai`.

---

## 6. Strategi Testing
- **Asset Build:** `npm run build` berhasil tanpa error.
- **Feature Test:** Seluruh 22 test pada `AdminSuratSelesaiIndividuKelompokTest` dan `AdminSuratSelesaiKonektivitasEndToEndTest` tetap lulus.

---

## 7. Risiko & Mitigasi
- **Risiko:** Gambar foto profil dengan rasio bukan 1:1 tampak gepeng pada kotak `156px × 156px`.  
  *Mitigasi:* `.mentee-avatar-circle .avatar-img` sudah menggunakan `width: 100%; height: 100%; object-fit: cover;` sehingga foto ter-crop proporsional.
- **Risiko:** Avatar pada daftar utama (`Tier 1`) ikut berubah menjadi kotak.  
  *Mitigasi:* Selector terisolasi pada `.focused-profile-card .mentee-avatar-circle`.

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] Foto profil tampil kotak besar (`156px × 156px`) dengan sudut rounded lembut (`16px`).
- [ ] Nama peserta berada rata tengah tepat di bawah foto.
- [ ] Tidak ada ruang kosong berlebih di bawah *Tanggal Selesai* pada card *Informasi Penempatan*.
- [ ] `npm run build` dan seluruh test `AdminSuratSelesai` lulus pada PHP 8.3.
