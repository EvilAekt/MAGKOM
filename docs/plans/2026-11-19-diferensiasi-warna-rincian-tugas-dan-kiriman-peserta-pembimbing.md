# Plan: Diferensiasi Visual Header Banner Rincian Penugasan (Biru MagKom) & Kiriman Tugas Peserta (Kuning MagKom) pada Review Tugas Pembimbing

Dokumen rencana kerja ini disesuaikan dengan instruksi eksplisit pengguna:
1. Menambahkan hero banner header bertema MagKom Sleman Blue pada kartu Rincian Penugasan.
2. Menambahkan hero banner header bertema MagKom Golden Yellow (`#FFC300`) dengan teks Sleman Navy (`#02275D`) persis sesuai tombol `+ Buat Tugas` pada kartu Kiriman Tugas Peserta.
3. Garis pinggir luar kartu disamakan menggunakan border netral biasa (`#E2E8F0`) tanpa garis pinggir berwarna.
4. Menjaga layout kanvas dan grid tetap utuh 100%.

---

## 1. Tujuan
1. **Hero Banner Biru Khas MagKom (Rincian Penugasan)**:
   - Gradien biru khas Pemkab Sleman MagKom (`radial-gradient + linear-gradient(#01347C ke #003566)`).
   - Teks judul putih tegas **"RINCIAN PENUGASAN"** dan badge label *"Instruksi Pembimbing"*.
2. **Hero Banner Kuning Resmi MagKom (Kiriman Peserta)**:
   - Warna kuning MagKom solid `#FFC300` persis sesuai tombol `+ Buat Tugas`.
   - Teks judul tegas warna Sleman Navy `#02275D` **"KIRIMAN TUGAS PESERTA"** dan ikon upload senada `#02275D`.
   - Pill status evaluasi penugasan (*"✓ Disetujui"*, dll) di sisi kanan header.
3. **Harmonisasi Kotak & Garis Pinggir**:
   - Kedua kartu disamakan bentuk kotaknya (`border-radius: 12px; padding: 0; overflow: hidden;`).
   - Garis pinggir luar kartu netral abu-abu biasa (`border: 1px solid #E2E8F0`), tanpa garis berwarna.
   - Box catatan progres pengerjaan juga menggunakan border netral (`border: 1px solid #E2E8F0; background: #F8FAFC`).
4. **Dukungan Dark Mode**:
   - Header biru dan kuning teradaptasi rapi dengan kontras tinggi di tema gelap.

---

## 2. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/pembimbing/tugas/show.blade.php` | Modify | Menyematkan struktur `.card-hero-banner.banner-instruction` dan `.card-hero-banner.banner-submission` dengan pembungkus `.instruction-card-body` dan `.submission-card-body`. |
| `resources/css/pages/pembimbing/tugas-review.css` | Modify | 1. Styling `.card-hero-banner` (Biru MagKom dan Kuning MagKom `#FFC300` / Navy `#02275D`).<br>2. Menyamakan kotak kartu atas & bawah dengan border netral abu-abu `#E2E8F0`.<br>3. Menyesuaikan box catatan progres dan aturan Dark Mode. |

---

## 3. Hasil Verifikasi
- [x] Header kartu Rincian Penugasan menggunakan banner biru khas MagKom.
- [x] Header kartu Kiriman Tugas Peserta menggunakan kuning resmi MagKom `#FFC300` & teks Sleman Navy `#02275D`.
- [x] Garis pinggir kartu biasa tanpa warna (`border: 1px solid #E2E8F0`).
- [x] Seluruh automated test lolos 100% (`Tests\Feature\PembimbingReviewClassroomTest`).
- [x] Vite asset terkompilasi bersih tanpa error.
