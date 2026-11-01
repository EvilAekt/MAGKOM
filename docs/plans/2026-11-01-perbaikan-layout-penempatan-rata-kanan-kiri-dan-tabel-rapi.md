# Rencana Kerja: Perbaikan Layout Fluid Rata Kanan-Kiri & Perapian Tabel Data Penempatan Peserta

Dokumen ini merinci analisis masalah tata letak, perbandingan opsi arsitektur, dan langkah implementasi terstruktur untuk mengubah layout halaman **Penempatan Peserta** (`/admin/penempatan`) agar tampil penuh rata kanan-kiri (*full-width edge-to-edge*) seperti halaman Dashboard, serta membersihkan teks duplikat pada tabel peserta dan pembimbing lapangan.

---

## 1. Tujuan
1. **Layout Rata Kanan-Kiri (Full-Width / Edge-to-Edge Parity)**: Menghilangkan batasan kaku `max-width: 1400px; margin: 0 auto;` pada container konten penempatan, sehingga kartu metrik, filter pencarian, dan tabel data merentang penuh secara proporsional dan sejajar dengan judul halaman serta tombol aksi di pojok kanan atas, konsisten dengan tampilan **Dashboard Operasional Administrasi**.
2. **Pembersihan Teks Duplikat & Desain Tabel yang Terstruktur**:
   - Menghilangkan teks asal bidang `[{{ $p->pembimbing->bidang?->nama_bidang }}]` pada kolom *Pembimbing Lapangan* karena informasinya sudah termuat secara jelas di kolom sebelahnya (*Jalur / Bidang Dinas*).
   - Menghilangkan label berulang seperti `(Pembimbing Lapangan)` dan `(Peserta Magang)` yang tercantum pada nama personil di data seeder.
   - Merapikan baris pembimbing dengan chip beban mentee yang kompak (`Beban: X Mentee Aktif`) dan tombol aksi ganti pembimbing yang proporsional.
3. **Konsistensi Visual Antarmuka (Gentelella Design System)**: Menyelaraskan padding, radius, border, dan responsivitas tabel pada tampilan tema terang (*Light Mode*) maupun tema gelap (*Dark Mode*).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
- **Konvensi Layout Admin**: Standar layout administrasi MagKom Sleman mengacu pada `resources/views/admin/dashboard.blade.php`, yaitu menggunakan lebar kontainer `width: 100%` tanpa pembatas `max-width` kaku di tengah layar desktop.
- **Hierarki Kolom Tabel Penempatan**:
  - Kolom 1: Checkbox seleksi massal.
  - Kolom 2: Peserta Magang (Avatar inisial, nama bersih, instansi, NIM).
  - Kolom 3: Pembimbing Lapangan (Nama bersih, pill beban mentee, tombol ubah pembimbing — tanpa nama bidang yang redundan).
  - Kolom 4: Jalur / Bidang Dinas (Nama bidang dinas tebal + badge jalur reguler / mitra).
  - Kolom 5: Periode Magang (Tanggal mulai s/d selesai dalam bahasa Indonesia).
  - Kolom 6: Status Penempatan (Pill badge status: Aktif, Selesai, Mengundurkan Diri).
  - Kolom 7: Aksi (Tombol Detail, Pratinjau Surat, Unduh PDF, Batalkan).
- **Seeder Sanitasi**: Data di `AcceptedPesertaSeeder.php` dinormalisasi agar nama pembimbing dan peserta tidak lagi mengandung teks peran dalam tanda kurung.

### Pertanyaan Terbuka:
- *Tidak ada ambiguitas kritis.* Format visual sudah dicontohkan secara gamblang oleh user melalui tangkapan layar Dashboard dan tabel data penempatan.

---

## 3. Evaluasi Pendekatan Desain

| Kriteria | Opsi A: Tweak CSS Inline Parsial | Opsi B (Direkomendasikan): Standardisasi Container Fluid 100% + Refactor Elemen Tabel |
| :--- | :--- | :--- |
| **Lebar Kontainer** | Hanya menghapus `max-width: 1400px` tanpa penyesuaian grid card. | Menghapus `max-width: 1400px; margin: 0 auto;`, menyetel `width: 100%` pada root wrapper, serta menyelaraskan grid hero stats dan filter bar. |
| **Redundansi Teks** | Teks bidang disembunyikan via CSS `display:none`. | Dihapus langsung dari template Blade, merapikan beban mentee menjadi chip terstruktur dengan ikon, dan menormalisasi nama di seeder. |
| **Konsistensi Antar-Halaman** | Terisolasi pada 1 halaman saja. | Diterapkan konsisten pada `admin/penempatan/index.blade.php` dan `admin/penempatan/show.blade.php`. |
| **Kerapian & Keterbacaan** | Baris tabel tetap tinggi dan berdesakan. | Baris tabel jauh lebih ringkas, elegan, dan proporsional secara vertikal. |

**Keputusan Desain**: Memilih **Opsi B** untuk menghasilkan antarmuka yang bersih, profesional, konsisten dengan dashboard, dan bebas dari informasi yang berulang.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/penempatan/index.blade.php` | Modify | 1. Hapus `max-width: 1400px; margin: 0 auto;` pada kontainer utama `@section('content')` dan gunakan `width: 100%`.<br>2. Hapus rendering bidang redundan `[{{ $p->pembimbing->bidang?->nama_bidang }}]` pada kolom Pembimbing Lapangan.<br>3. Bersihkan nama dari teks peran `(Pembimbing Lapangan)` dan `(Peserta Magang)`.<br>4. Tata ulang informasi beban mentee dan tombol "Ganti" menjadi chip ringkas.<br>5. Pastikan hero stat cards dan filter bar merentang penuh rata kanan-kiri. |
| `resources/views/admin/penempatan/show.blade.php` | Modify | Hapus `max-width: 1400px; margin: 0 auto;` pada wrapper detail penempatan agar konsisten rata kanan-kiri dengan halaman index. |
| `database/seeders/AcceptedPesertaSeeder.php` | Modify | Normalisasi nama dummy pembimbing menjadi `Budi Santoso, S.Kom` dan peserta menjadi `Naufal Pratama`. |
| `tests/Feature/AdminChildPagesExtendedVisualTest.php` | Modify / Test | Tambahkan verifikasi visual assertion untuk memastikan layout penempatan render penuh tanpa class/inline max-width 1400px kaku dan tidak memuat teks ganda. |

---

## 5. Langkah Implementasi

### Tahap 1: Kontainer Layout Rata Kanan-Kiri (Fluid Width)
- [x] **1.1** Buka `resources/views/admin/penempatan/index.blade.php`, ganti kontainer pembungkus utama baris 59:
  - Dari: `<div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1400px; margin: 0 auto; width: 100%;">`
  - Menjadi: `<div class="gentelella-penempatan-admin" style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">`
- [x] **1.2** Periksa baris hero stat cards dan filter bar di `index.blade.php`, pastikan menggunakan `width: 100%` agar grid 4 kartu metrik otomatis terdistribusi merata dari tepi kiri ke tepi kanan.
- [x] **1.3** Buka `resources/views/admin/penempatan/show.blade.php`, terapkan perataan kontainer yang sama agar halaman detail penempatan juga konsisten penuh (*fluid*).

### Tahap 2: Pembersihan Redundansi & Perapian Kolom Tabel
- [x] **2.1** Buka bagian `<tbody>` pada `resources/views/admin/penempatan/index.blade.php`:
  - Pada sel **Peserta Magang**: tampilkan nama bersih menggunakan helper/string filter agar terbebas dari sufiks peran `(Peserta Magang)`.
  - Pada sel **Pembimbing Lapangan**:
    - Tampilkan nama pembimbing bersih tanpa sufiks `(Pembimbing Lapangan)`.
    - **Hapus baris teks**: `<div style="font-size: 0.75rem; color: #0641CC; ...">[{{ $p->pembimbing->bidang?->nama_bidang }}]</div>`.
    - Kemas beban mentee dan tombol ganti menjadi flex row horizontal yang rapi: chip `Beban: X Mentee` + tautan `Ganti`.
- [x] **2.2** Perbarui `database/seeders/AcceptedPesertaSeeder.php`:
  - Ubah `Budi Santoso, S.Kom (Pembimbing Lapangan)` -> `Budi Santoso, S.Kom`.
  - Ubah `Naufal Pratama (Peserta Magang)` -> `Naufal Pratama`.
- [x] **2.3** Jalankan perbaikan data pengguna aktif pada database lokal jika diperlukan agar tampilan langsung bersih saat diakses.

### Tahap 3: Verifikasi Visual & Pengujian Otomatis
- [x] **3.1** Jalankan `php artisan test --filter="AdminChildPagesExtendedVisualTest"` dan suite pengujian terkait penempatan.
- [x] **3.2** Buka halaman `http://127.0.0.1:8000/admin/penempatan` menggunakan Playwright browser tool.
- [x] **3.3** Ambil tangkapan layar verifikasi (*desktop* 1440px / 1920px dan tema gelap) untuk memastikan:
  - Header dan tombol aksi sejajar sempurna di ujung kiri dan kanan tabel.
  - Tidak ada lagi teks bidang ganda pada kolom pembimbing.
  - Baris tabel rapi, terstruktur, dan enak dibaca.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - Memastikan respons HTTP 200 pada route `admin.penempatan.index`.
   - Memastikan tidak ada error relasi database (`$p->peserta`, `$p->pembimbing`, `$p->bidang`).
   - Memastikan tombol aksi dan link modal tetap berfungsi normal.
2. **Visual Snapshot Tests**:
   - Membandingkan tampilan sebelum dan sesudah: tata letak penuh tanpa celah kosong di samping, dan kolom pembimbing yang ringkas.

---

## 7. Analisis Risiko & Mitigasi

- **Risiko**: Layar monitor kecil (< 1200px) menyebabkan tabel overflow horizontal.
  - *Mitigasi*: Tabel sudah dibungkus di dalam container `.table-responsive-gentelella` dengan `overflow-x: auto` sehingga pada layar kecil atau laptop 13 inch tetap dapat digeser (*scroll*) dengan mulus tanpa merusak tata letak.
- **Risiko**: Perubahan nama di seeder memengaruhi tes yang mencari string `(Peserta Magang)`.
  - *Mitigasi*: Pengujian automated di proyek ini tidak bergantung pada string literal `(Peserta Magang)`. Kita tetap menjalankan `php artisan test` untuk memverifikasi 100% hijau.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Kontainer halaman Penempatan Peserta merentang penuh (*width: 100%*) rata kanan-kiri, sejajar dengan judul halaman dan tombol aksi di kanan atas.
- [x] Teks asal bidang `[Aplikasi Informatika & E-Government (EGOV)]` tidak lagi muncul di kolom Pembimbing Lapangan.
- [x] Teks peran seperti `(Pembimbing Lapangan)` dan `(Peserta Magang)` telah dibersihkan dari nama di tabel.
- [x] Informasi beban mentee tertata rapi sebagai chip/badge kompak.
- [x] Halaman tampil konsisten dan responsif di tema terang dan gelap.
- [x] Semua pengujian otomatis (`php artisan test`) lulus 100%.

