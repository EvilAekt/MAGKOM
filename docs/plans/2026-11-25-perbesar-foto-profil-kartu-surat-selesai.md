# Rencana Kerja: Memperbesar Ukuran Foto Profil pada Card Identitas Kolom Kiri (Surat Selesai)

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Cakupan:** Styling `.focused-profile-card` di `resources/css/pages/admin/surat-selesai-index.css` (tanpa perubahan Blade)

---

## 1. Tujuan
Memperbesar ukuran foto profil peserta (`.mentee-avatar-circle`) dan tipografi nama pada `.focused-profile-card` di kolom kiri mode fokus Surat Selesai (Individu & Kelompok) agar tidak terlihat kecil, lebih proporsional terhadap lebar card, sekaligus membantu menyeimbangkan tinggi kolom kiri sehingga ruang kosong di bawah *Informasi Penempatan* berkurang.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis:**
  1. Perubahan cukup dilakukan melalui CSS pada `resources/css/pages/admin/surat-selesai-index.css` dengan selector scoped `.focused-profile-card ...` sehingga **tidak memerlukan perubahan markup Blade** dan tidak memengaruhi `.mentee-avatar-circle` pada daftar utama (Tier 1).
  2. Bentuk avatar tetap lingkaran (`border-radius: 50%`) agar konsisten dengan desain existing.
  3. Berlaku otomatis untuk mode fokus **Individu** (`surat-selesai/index.blade.php`) dan **Kelompok** (`_show_kelompok.blade.php`) karena keduanya memakai class `.focused-profile-card`.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Desain
1. **Opsi A (Direkomendasikan): Perbesar Avatar & Tipografi dalam Layout Horizontal**
   - Memperbesar `.focused-profile-card .mentee-avatar-circle` dari `44px × 44px` menjadi `88px × 88px` (inisial fallback `font-size: 1.6rem`, `border: 2.5px solid #BFDBFE`).
   - Menyesuaikan `.focused-card.focused-profile-card` menjadi `padding: 20px 22px; gap: 18px;`.
   - Memperbesar `.focused-profile-card .focused-identity-name` menjadi `font-size: 1.35rem; line-height: 1.3;`.
   - **Kelebihan:** Tetap menjaga tata letak horizontal yang rapi, foto tampak jelas dan proporsional, menambah tinggi card profil secara natural untuk mengurangi ruang kosong di card bawah, dan nol perubahan Blade.
   - **Kekurangan:** Tidak ada.

2. **Opsi B: Layout Vertikal Centered (Avatar di Atas Nama)**
   - Mengubah `flex-direction: column; text-align: center;` dengan avatar `112px × 112px`.
   - **Kelebihan:** Tinggi card bertambah lebih banyak.
   - **Kekurangan:** Mengubah orientasi visual horizontal yang sudah ada pada `.focused-profile-card`.

**Keputusan:** Memilih **Opsi A** karena mempertahankan konsistensi arah horizontal sekaligus menyelesaikan masalah proporsi foto dan ruang kosong.

---

## 4. Perubahan File

| File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/admin/surat-selesai-index.css` | Modify | Memperbesar ukuran `.focused-profile-card .mentee-avatar-circle` (`88px × 88px`), menyesuaikan `padding`/`gap` pada `.focused-card.focused-profile-card`, dan memperbesar `.focused-profile-card .focused-identity-name` (`1.35rem`). |

---

## 5. Langkah Implementasi

- [ ] **Update `resources/css/pages/admin/surat-selesai-index.css`**:
  - Sesuaikan `.focused-card.focused-profile-card` (`padding: 20px 22px; gap: 18px;`).
  - Tambahkan rule `.focused-profile-card .mentee-avatar-circle` (`width: 88px; height: 88px; font-size: 1.6rem; border-width: 2.5px;`).
  - Sesuaikan `.focused-profile-card .focused-identity-name` (`margin: 0; font-size: 1.35rem;`).
- [ ] **Quality Gate & Verifikasi**:
  - Jalankan `npm run build`.
  - Jalankan `C:\Users\galan\php83\php.exe artisan test --filter=AdminSuratSelesai`.

---

## 6. Strategi Testing
- **Asset Build:** `npm run build` berhasil tanpa error.
- **Feature Test:** Seluruh 22 test pada `AdminSuratSelesaiIndividuKelompokTest` dan `AdminSuratSelesaiKonektivitasEndToEndTest` tetap lulus.

---

## 7. Risiko & Mitigasi
- **Risiko:** Ukuran `.mentee-avatar-circle` pada daftar kartu utama ikut membesar.  
  *Mitigasi:* Menggunakan selector spesifik `.focused-profile-card .mentee-avatar-circle` sehingga terisolasi hanya di card profil fokus.
- **Risiko:** Pada layar mobile sempit, nama panjang bisa terpotong atau berdesakan.  
  *Mitigasi:* `.focused-identity-text` sudah memiliki `min-width: 0; flex: 1;` dan nama akan wrap rapi secara otomatis.

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] Foto profil pada `.focused-profile-card` tampil besar (`88px × 88px`) dan proporsional dengan nama peserta (`1.35rem`).
- [ ] Avatar pada daftar kartu utama (`Tier 1`) tidak berubah ukurannya.
- [ ] `npm run build` dan seluruh test `AdminSuratSelesai` lulus pada PHP 8.3.
