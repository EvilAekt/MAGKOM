# Rencana Kerja: Redesign Tata Letak & Estetika Toolbar Filter Verifikasi Berkas Admin

**Tanggal Dokumen:** 6 November 2026  
**Status:** Menunggu Persetujuan Pengguna (Awaiting Feedback)  
**Terkait Dokumen:** `.agents/rules/AGENTS.md`, `resources/views/admin/pendaftaran/index.blade.php`

---

## 1. Latar Belakang Masalah (Evaluasi Gambar 1 & 2)

Berdasarkan tinjauan visual langsung terhadap antarmuka saat ini:
1. **Dua Baris Kapsul Bertumpuk (Vertical Track Stacking):**
   - Baris 1: Kategori (`Semua Kategori`, `Individu`, `Kelompok`) mengambang pendek di kiri atas.
   - Baris 2: Status (`Semua Status`, `Diajukan`, `Diterima`, `Ditolak`) terbungkus dalam track kapsul yang melar melompong (*stretched full-width*) ke kanan dengan ruang kosong besar yang tidak estetik.
2. **Redundansi Kontrol Filter (Double Control):**
   - Di baris kapsul sudah ada pilihan Kategori (`Semua Kategori`, `Individu`, `Kelompok`).
   - Namun di bawahnya pada form filter grid, terdapat dropdown `Kategori` lagi. Hal ini membuat tata letak grid menjadi sempit/berdesakan (6 kolom) dan membingungkan pengguna.
3. **Hirarki Visual Kurang Terarah:**
   - Admin kesulitan membedakan mana filter alur kerja utama (*workflow state*) dan mana klasifikasi jenis peserta (*registration type*).

---

## 2. Analisis & Rekomendasi Solusi Senior Developer

### 🏆 Solusi Rekomendasi: "Dual-Control Balanced Header + 4-Column Clean Filter Grid"

Kami merekomendasikan tata letak modern berstandar enterprise (seperti Stripe, Linear, dan GitHub):

```
+--------------------------------------------------------------------------------------------------------+
| DAFTAR PERMOHONAN MAGANG MASUK                                                                         |
|                                                                                                        |
| [ Semua Status (4) | Diajukan (1) | Diterima (2) | Ditolak (0) ]       [ Semua (4) | 👤 Individu (4) | 👥 Kelompok (0) ] |
| <----------- Workflow State Tabs (Kiri) ----------->       <------ Kategori Pendaftaran (Kanan) ------>|
|                                                                                                        |
| +----------------------------------------------------------------------------------------------------+ |
| | [ 🔍 Cari nama...                ] [ Semua Bulan ▾ ] [ Semua Jurusan ▾ ] [ Terbaru ▾ ] [ Terapkan ] [ Reset ] | |
| +----------------------------------------------------------------------------------------------------+ |
+--------------------------------------------------------------------------------------------------------+
```

### Keunggulan Desain Ini:
1. **Satu Baris Fleksibel & Seimbang (Single Balanced Row):**
   - **Sisi Kiri**: Status Alur Kerja (Workflow Status: Semua, Diajukan, Diterima, Ditolak).
   - **Sisi Kanan**: Kategori Pendaftaran (Semua, Individu, Kelompok) dengan badge angka yang rapi dan proporsional.
   - Tidak ada lagi kapsul yang melar kosong melompong. Kedua kontrol memiliki lebar alami (*inline-flex fit-content*).
2. **Eliminasi Redundansi Kategori:**
   - Hapus dropdown `Kategori` dari form grid bawah. Kategori pendaftaran sepenuhnya dikontrol secara instan (1-klik) lewat segmented switcher di kanan atas.
3. **Grid Filter Bawah Menjadi Lapang & Proporsional (4 Kolom):**
   - Kolom 1: Input Pencarian nama (lebar & lega, `minmax(260px, 2.5fr)`).
   - Kolom 2: Dropdown Bulan Magang (`1.2fr`).
   - Kolom 3: Dropdown Jurusan (`1.3fr`).
   - Kolom 4: Dropdown Urutan Data (`1fr`).
   - Kolom 5: Tombol aksi `Terapkan` & `Reset`.
4. **Penyempurnaan Styling & Polish:**
   - Warna aktif Kategori menggunakan aksen Indigo/Purple yang lembut agar kontras dan mudah dibedakan dari warna biru Status utama.
   - Responsif di tablet dan mobile: status dan kategori membungkus (*wrap*) dengan anggun tanpa merusak layout.

---

## 3. Rincian Perubahan Kode

### 3.1. File `resources/views/admin/pendaftaran/index.blade.php`

1. **Struktur Toolbar Atas:**
   - Mengubah `.adm-status-track-wrapper` menjadi baris fleksibel:
     ```html
     <div class="adm-toolbar-header">
         <div class="adm-status-track">
             <!-- Status Tabs: Semua, Diajukan, Diterima, Ditolak -->
         </div>
         <div class="adm-category-track">
             <!-- Category Switcher: Semua, Individu, Kelompok -->
         </div>
     </div>
     ```
2. **Form Filter Grid:**
   - Menghapus input dropdown `<select name="tipe">` dari form grid, menjaga nilai `tipe` tetap tersimpan melalui `<input type="hidden" name="tipe">` saat filter submit.
   - Mengembalikan layout CSS `.adm-filter-grid` ke proporsi 4 kolom yang lega.
3. **CSS Scoped Polish:**
   - Menetapkan `display: inline-flex; width: fit-content;` pada track agar tidak memanjang melompong.
   - Memberikan styling active state yang tegas pada tab terpilih.

---

## 4. Permohonan Persetujuan Perubahan Markup Blade (Sesuai AGENTS.md Bagian 3.3)

| File Blade | Bagian yang Diubah | Alasan Markup Baru Diperlukan |
|---|---|---|
| `resources/views/admin/pendaftaran/index.blade.php` | Toolbar switcher (`.adm-filter-toolbar`) & Filter Grid | Menyusun ulang layout dari 2 baris kapsul bertumpuk menjadi 1 baris dual-control yang seimbang, menghapus dropdown kategori redundan dari form grid, dan merapikan spacing. |

---

## 5. Rencana Pengujian

1. **Visual & Responsive Testing:** Memverifikasi tampilan desktop, tablet, dan mobile tidak ada elemen yang melar atau tumpang tindih.
2. **Feature Tests:** Menjalankan `php artisan test --filter AdminVerifikasiKategoriDanUploadSuratTest` dan `php artisan test --filter Pendaftaran`.
3. **Pint Code Formatting:** Memastikan file Blade dan CSS tetap rapi dan valid.

