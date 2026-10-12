# Rencana Kerja: Revamp Desain Filter & Search Toolbar Antrean Verifikasi Berkas

Dokumen ini merinci rencana transformasi desain untuk area **Filter Status & Form Pencarian** (*Filter & Search Toolbar*) pada halaman Antrean Verifikasi Berkas ([`resources/views/admin/pendaftaran/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/pendaftaran/index.blade.php)). Tujuannya adalah menghilangkan kesan sesak, bertumpuk, dan berantakan pada toolbar filter menjadi antarmuka yang lapang (*spacious*), bersih (*clean*), estetis, dan intuitif.

---

## 1. Masalah Desain Saat Ini (Berdasarkan Feedback & Screenshot Pengguna)

1. **Baris Status Kaku**:
   - Terdapat label teks kaku `"Status:"` di awal baris yang memakan ruang dan terasa seperti form jadul.
   - Tombol status pill berbentuk kapsul putih dengan border abu-abu kaku dan angka counter yang pudar, kurang harmonis.
2. **Input Fields Mepet & Crowded**:
   - Baris input pencarian, dropdown periode, dropdown jurusan, dropdown urutan, dan dua tombol aksi dipaksakan dalam satu baris `flex-wrap` datar.
   - Akibatnya, input pencarian utama menjadi sempit dan teks *placeholder* terpotong (`Ketik nama, no. registrasi (#REG-XXXXX), institusi, a...`).
   - Tombol *"Terapkan"* dan *"Reset"* menempel di ujung kanan secara desak-desakan.
3. **Label Kapital Terlalu Padat**:
   - Label di atas input menggunakan huruf kapital penuh (`CARI CALON PESERTA`, `PERIODE MAGANG`, dll.) dengan ukuran font yang memakan ruang vertikal dan berkesan kaku.
4. **Pemisah Garis Datar Kuno**:
   - Garis pemisah tipis antara pills status dan form input membuat kartu tampak seperti dua tabel mini yang ditumpuk tanpa kesatuan konsep visual.

---

## 2. Pendekatan Desain Baru (Ergonomis, Modern & Bernapas Lega)

### A. Evaluasi Opsi Desain

| Aspek | Opsi A (Tingkat Bertumpuk / 2 Card Terpisah) | Opsi B: Unified Grid Toolbar dengan Segmented Pill Bar (Rekomendasi) |
|---|---|---|
| **Struktur** | Memisahkan Filter Status ke Card sendiri dan Search Bar ke Card sendiri. | Menyatukan ke dalam **1 Card Elegan** dengan zona visual yang teratur via CSS Grid. |
| **Kelebihan** | Memisahkan konteks status dan filter. | Hemat ruang vertikal, terasa utuh, profesional, dan serasi dengan tema MagKom. |
| **Kekurangan** | Menambah jumlah card di layar sehingga halaman makin panjang ke bawah. | Memerlukan pengaturan grid responsif yang presisi. |
| **Keputusan** | *Ditolak* (terlalu banyak card kotak-kotak bertumpuk). | **DIPILIH**: 1 Card terpadu dengan zona visual yang bernapas lega. |

---

### B. Detail Rancangan Baru

1. **Zona Atas: Segmented Status Switcher (Modern Pill Bar)**:
   - Menghilangkan teks `"Status:"` yang kaku.
   - Mengelompokkan status (*Semua*, *Diajukan*, *Perlu Perbaikan*, *Waiting List*, *Diterima*, *Ditolak*) dalam **Segmented Strip Bar** berlatar belakang lembut `#F8FAFC` dengan border halus `#E2E8F0`.
   - Pill aktif berwarna **Biru Signature MagKom (`#0641CC`)** dengan teks putih dan badge counter transparan yang kontras.
   - Pill tidak aktif berlatar transparan/bersih, teks abu-abu gelap `#475569`, dan hover lembut.
   - Di sisi kanan baris status: ditambahkan indikator cepat total hasil filter atau tombol ringkas *"Reset Filter"* jika ada filter yang sedang aktif.

2. **Zona Bawah: Form Kontrol Berbasis Grid Lapang (Spacious Responsive Grid)**:
   - Mengganti `flex` yang berantakan dengan **CSS Grid Layout** yang terstruktur rapi:
     - **Kolom 1 (Utama - 38%)**: Input pencarian calon peserta yang luas dan lega, dilengkapi **icon kaca pembesar di dalam input (left-aligned icon)**, dengan placeholder yang jelas dan nyaman dibaca.
     - **Kolom 2 (20%)**: Dropdown Periode Magang.
     - **Kolom 3 (20%)**: Dropdown Jurusan Magang.
     - **Kolom 4 (12%)**: Dropdown Urutan (FIFO / Terbaru / Terlama).
     - **Kolom 5 (Aksi - 10%)**: Tombol *"Terapkan"* berkontras tinggi dan tombol *"Reset"* yang proporsional.
   - Label diubah menjadi huruf *Title Case* yang ramah (misal: `Pencarian Peserta`, `Periode`, `Jurusan`, `Urutan Data`), warna slate yang elegan (`#475569`), font weight 600.
   - Input & select menggunakan tinggi yang nyaman (42px), radius 10px, background putih bersih, border `#CBD5E1`, dan transition focus ring biru Kominfo.

---

## 3. Rencana Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| [`resources/views/admin/pendaftaran/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/pendaftaran/index.blade.php) | **Ubah** | Revamp markup HTML dan styling CSS untuk `.adm-filter-card`, `.adm-status-pills-row`, dan `.adm-filter-form-grid`. |

---

## 4. Langkah-Langkah Implementasi

- [x] **Langkah 1: Perbarui Styling CSS Toolbar Filter di `index.blade.php`**
  - Buat style CSS `.adm-filter-card` dengan padding yang seimbang (24px) dan border-radius 16px.
  - Rancang CSS untuk `.adm-status-track` (container segmented status modern).
  - Rancang CSS untuk `.adm-filter-grid` dengan pembagian kolom grid yang lapang dan breakpoint responsif (desktop, tablet, mobile).
  - Tambahkan icon search di dalam input teks `.adm-input-search`.
- [x] **Langkah 2: Perbarui Markup Blade Toolbar Filter**
  - Tata ulang baris status pill menjadi segmented switcher yang bersih tanpa teks kaku `"Status:"`.
  - Pasang form grid terstruktur dengan label yang ramah dan input yang luas.
  - Gabungkan tombol Terapkan & Reset dengan visual yang seimbang.
- [x] **Langkah 3: Validasi Render & Fungsionalitas**
  - Jalankan script pengujian render PHP untuk memastikan tidak ada Blade syntax error (Render sukses: 82.1 KB).
  - Pastikan seluruh parameter GET (status, q, bulan_magang_id, jurusan_id, sort) tetap terkirim dan diproses dengan benar.

---

## 5. Strategi Testing & Risiko

1. **Strategi Testing**:
   - Render Test via scratch PHP script: memastikan view ter-compile sempurna tanpa error.
   - Parameter Integrity Test: memastikan URL query parameter (`?status=diajukan&q=ahmad&...`) tetap sinkron saat tombol Terapkan diklik atau saat pill status diklik.
2. **Risiko & Mitigasi**:
   - *Risiko*: Nilai filter status hilang saat form disubmit via tombol Terapkan.
   - *Mitigasi*: Pastikan `<input type="hidden" name="status" value="{{ request('status') }}">` tetap ada di dalam form pencarian.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Tampilan filter bar tidak lagi berdesakan atau terpotong teksnya.
- [x] Baris status pill tampak bersih, modern, dan menyatu (tidak ada teks label kaku `"Status:"`).
- [x] Kolom pencarian calon peserta lapang dan mudah diketik.
- [x] Semua fungsi filter (klik status, cari teks, pilih dropdown, reset) berjalan 100% normal.
