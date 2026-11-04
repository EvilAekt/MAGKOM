# Plan: Redesain Halaman Tugas & Progres Magang Pembimbing (Selaras Standar Peserta & SuperAdmin)

| Metadata | Keterangan |
|---|---|
| **Tanggal Pembuatan** | 4 November 2026 |
| **Status Dokumen** | Menunggu Persetujuan Pengguna (Pending Approval) |
| **Target Area** | Portal Pembimbing: Modul Daftar Penugasan & Progres (`resources/views/pembimbing/tugas/index.blade.php`, `resources/css/pages/pembimbing/tugas-index.css`, `resources/js/pages/pembimbing/tugas-index.js`, `vite.config.js`) |
| **Referensi Desain** | Tampilan Tugas Peserta MagKom Terkini (`peserta/tugas/index.blade.php`, Lampiran Gambar 3 Pengguna) & Sleman SuperAdmin Studio Parity |
| **Design System Skill** | UI/UX Pro Max (`Inter` typography, Sleman Deep Blue `#01347C` / `#003566`, Gentelella Clean Components) |

---

## 1. Ringkasan Permintaan & Analisis Masalah

Berdasarkan perbandingan antara tampilan pembimbing saat ini (Foto 1 & 2) dengan tampilan acuan dari peserta (Foto 3), pengguna menghendaki perombakan tampilan agar:
1. **Lebih Rapi & Lebih Bersih**:
   - Menghilangkan kesan "penuh sesak" (*visual clutter*) dan background ilustrasi 3D komputer yang ramai/distracting pada banner.
   - Menggunakan warna resmi Sleman MagKom terkini: Deep Navy Blue (`#01347C` / `#003566`) dengan gradien lembut.
2. **Menghapus Teks Kode `(F-08)`**:
   - Judul banner `"Manajemen Penugasan & Progres (F-08)"` disederhanakan menjadi `"Tugas & Progres Magang"` atau `"Manajemen Penugasan & Progres"`.
3. **Menghapus Teks Subtitle Header**:
   - Menghapus teks deskripsi di bagian atas dashboard: `"Manajemen penugasan terstruktur dan evaluasi kemajuan kerja mentee bimbingan."` (diatur via `@section('page_description')`).
4. **Menghapus Bagian "Sedang Dikerjakan"**:
   - Sesuai screenshot crop pengguna (Foto 2), bagian status/tab **"Sedang Dikerjakan"** dihilangkan dari antarmuka filter tab dan chip ringkasan, sehingga alur status menjadi lebih ringkas dan fokus pada tindakan (*Action-driven*).
5. **Mengatasi Masalah Teks Rata Kanan-Kiri Kartu yang Berjejal**:
   - Pada kartu lama, informasi mentee, berbagai tag (kategori, kompleksitas, progres, lampiran file, link acuan) bertumpuk di sebelah kiri bawah, sementara status pill dan tombol di kanan bawah, dan judul di kiri atas serta tenggat di kanan atas. Ini menyebabkan "efek bolak-balik pandangan" (*table tennis scanning*) yang melelahkan.
   - Pada redesain ini, tata letak kartu distandarisasi persis seperti pada kartu tugas peserta:
     - Header: Judul tugas di kiri + Tanggal tenggat di kanan (dengan styling minimalis atau badge merah jika lewat tenggat).
     - Subheader: Chip mentee yang rapi dan elegan (`[Avatar Inisial] Nama Mentee • Institusi`).
     - Deskripsi: Cuplikan deskripsi ringkas (*line clamp* 2 baris).
     - Footer: Kiri berisi tag esensial (Kategori Tugas & Badge Laporan jika ada); Kanan berisi Status Badge Gentelella + Tombol Aksi terstandarisasi (`.btn-tugas-action` "Detail & Reviu >").
6. **Penyelarasan Arsitektur Frontend Sesuai `AGENTS.md`**:
   - Halaman eksisting saat ini memiliki **640+ baris `<style>` inline** dan **30 baris `<script>` inline** di dalam file Blade!
   - Sesuai aturan wajib MAGKOM `AGENTS.md`:
     - Seluruh styling diekstrak ke file modular `resources/css/pages/pembimbing/tugas-index.css`.
     - Seluruh skrip diekstrak ke modular ES Modules `resources/js/pages/pembimbing/tugas-index.js`.
     - Daftarkan asset baru ke `vite.config.js` dan muat menggunakan `@vite`.

---

## 2. Perbandingan Visual: Sebelum (Existing) vs Sesudah (Redesign)

```
========================================================================================
SEBELUM (FOTO 1 - EXISTING PEMBIMBING)
========================================================================================
[Header]: Tugas & Progres Magang
          "Manajemen penugasan terstruktur dan evaluasi kemajuan kerja..."  <-- (DIHAPUS)
----------------------------------------------------------------------------------------
[Banner Biru Terang + Gambar Kartun 3D Laptop/Buku]:
  Manajemen Penugasan & Progres (F-08)                             [+ Buat Tugas Baru]
  Bidang: ... • Pembimbing: ... • 13 Mentee Aktif
  [Total: 22] [Ditugaskan: 7] [Sedang Dikerjakan: 8] [Selesai: 7] [Perlu Evaluasi: 3]
----------------------------------------------------------------------------------------
[Tab Underline]: Semua Tugas (22) | Ditugaskan (7) | Sedang Dikerjakan (8) | Selesai (7)
----------------------------------------------------------------------------------------
[Toolbar Filter Mentee & Search Input]
----------------------------------------------------------------------------------------
[Kartu Tugas Berjejal]:
  (Icon)  afa                                                     Tenggat: 11 Nov 2026
          afaf
          [Avatar] Joko • UGM  (Kategori) (Tinggi) (Progres) (Link)  ● Belum  [Detail ->]
========================================================================================

========================================================================================
SESUDAH (FOTO 3 PARITY - REDESIGN ALA TUGAS PESERTA)
========================================================================================
[Header]: Tugas & Progres Magang  (Bersih, tanpa subtitle panjang)
----------------------------------------------------------------------------------------
[Classroom Hero Banner - Gradien Biru Sleman MagKom Elegan #01347C]:
  Tugas & Progres Magang                                          [+ Buat Tugas Baru]
  Bidang: Aplikasi Informatika & EGOV • Pembimbing: Budi Santoso • 13 Mentee Aktif
----------------------------------------------------------------------------------------
[Grid 3/4 Tile Stats Gentelella]:
  +---------------+   +---------------+   +---------------+   +---------------+
  |  TOTAL TUGAS  |   |  DITUGASKAN   |   |    SELESAI    |   | PERLU EVALUASI|
  |      22       |   |       7       |   |       7       |   |       3       |
  +---------------+   +---------------+   +---------------+   +---------------+
----------------------------------------------------------------------------------------
[X-Panel Konten Utama: Daftar Penugasan & Progres Mentee]:
  - Tab Segmented Pill:  [ Semua Tugas 22 ]  [ Ditugaskan 7 ]  [ Selesai 7 ]  [ ⚠️ Evaluasi 3 ]
    *(Bagian "Sedang Dikerjakan" TELAH DIHAPUS)*
  - Toolbar Filter & Search yang rapi dan terintegrasi
  - Daftar Kartu Tugas Bersih (Classroom Stream):
    +--------------------------------------------------------------------------------+
    | (Icon) Judul Tugas yang Jelas                             Tenggat: 11 Nov 2026 |
    |        (Mentee): Joko Susilo • Universitas Gadjah Mada                         |
    |        Deskripsi singkat tugas yang mudah dibaca dan tidak bertumpukan...      |
    |        ----------------------------------------------------------------------- |
    |        [Tag Kategori]  [1 Laporan Diserahkan]       [Badge Status] [Detail >]  |
    +--------------------------------------------------------------------------------+
========================================================================================
```

---

## 3. Komponen Perubahan Terperinci

### 3.1. Header & Banner Khas Sleman MagKom
- **Hapus Subtitle**: Menghapus `@section('page_description', 'Manajemen penugasan terstruktur dan evaluasi kemajuan kerja mentee bimbingan.')` sehingga breadcrumb dan judul utama tampil bersih.
- **Title Banner**: Ubah dari `Manajemen Penugasan & Progres (F-08)` menjadi `Tugas & Progres Magang` (atau `Manajemen Penugasan & Progres` tanpa kode F-08).
- **Background Banner**: Mengganti `classroom_banner_bg.jpg` dan warna `#0641CC` menjadi palet resmi Sleman MagKom:
  ```css
  background: radial-gradient(80% 120% at 100% 0%, rgba(63, 81, 181, 0.45), transparent 60%),
              linear-gradient(135deg, #01347C 0%, #003566 100%);
  ```
- **Tombol Buat Tugas**: Pertahankan tombol `+ Buat Tugas Baru` di sisi kanan banner dengan elevasi yang elegan.

### 3.2. Tile Stats Grid MagKom Gentelella (Di Bawah Banner)
Memanfaatkan komponen Blade yang sudah ada: `<x-gentelella.tile-stat>` dengan tata letak grid rapi 4 kolom (atau 3 kolom):
1. **Total Tugas**: Metric card warna primary (`teal` / `#01347C`) menampilkan total seluruh tugas pembimbing.
2. **Ditugaskan**: Metric card warna info (`blue` / `#2563EB`) menampilkan tugas berstatus `belum` dikerjakan.
3. **Selesai**: Metric card warna success (`teal`/`green` / `#16A34A`) menampilkan tugas berstatus `selesai`.
4. **Perlu Evaluasi**: Metric card warna warning (`yellow`/`amber` / `#D97706`) menampilkan tugas dengan laporan mentee yang butuh reviu.
*(Catatan: Metrik "Sedang Dikerjakan" ditiadakan sesuai instruksi).*

### 3.3. Segmented Pill Tab Filter (Tanpa "Sedang Dikerjakan")
Mengadopsi pola pill tab dari halaman peserta:
- `.tab-pill` berbentuk lonjong (*rounded-full*) dengan border lembut `#CBD5E1`.
- Active state berlatar Sleman Blue `#01347C` dengan teks putih dan badge counter dinamis.
- Tab yang ditampilkan:
  1. **Semua Tugas** (`$counts['total']`)
  2. **Ditugaskan** (`$counts['belum']`)
  3. **Selesai** (`$counts['selesai']`)
  4. **Perlu Evaluasi** (`$counts['perlu_review']`) — hanya muncul / diberi aksen amber jika nilainya > 0.
- **Tab "Sedang Dikerjakan" DIHAPUS**.

### 3.4. Redesain Kartu Tugas (Task Card Layout)
Mengatasi tata letak teks rata kanan-kiri yang berjejal menjadi struktur bernapas:
1. **Icon Sisi Kiri**:
   - Lingkaran 44px terpusat dengan ikon SVG semantik (Checkmark hijau jika selesai, Warning amber jika perlu review, Dokumen biru/slate jika ditugaskan, Exclamation merah jika overdue).
2. **Body Utama**:
   - **Baris Atas (Header Card)**:
     - Judul tugas (tebal, link hover biru Sleman).
     - Di sisi kanan: Tanggal tenggat (`Tenggat: 11 Nov 2026`) dengan warna merah tegas jika lewat tenggat.
   - **Baris Mentee (Subheader Card)**:
     - Info mentee penerima tugas: `[Avatar Inisial] Nama Mentee • Institusi/Universitas` yang tertata ringkas di bawah judul.
   - **Baris Deskripsi**:
     - Deskripsi tugas dengan `-webkit-line-clamp: 2` agar rapi dan tidak merusak ritme vertikal.
   - **Baris Bawah (Footer Card)**:
     - Dibatasi oleh garis tipis halus (`border-top: 1px solid #E2E8F0`).
     - **Sisi Kiri**: Tag esensial berupa kategori tugas dan badge laporan progres (`X Laporan Diserahkan`). Tag sekunder (seperti link acuan atau kompleksitas) dibuat lebih minimalis atau diprioritaskan di halaman detail agar kartu tidak kepenuhan.
     - **Sisi Kanan**: Badge status penugasan (`<x-gentelella.badge>`) berdampingan dengan tombol aksi Sleman (`.btn-tugas-action` bertuliskan `"Detail & Reviu >"`).

### 3.5. Toolbar Filter & Search Terpadu
- Dropdown pilih mentee bimbingan dan kolom pencarian kata kunci ditata dengan tinggi terstandarisasi (40px), rounded border rapi, dan tombol `Filter` serta `Reset` yang konsisten dengan desain Gentelella.

### 3.6. Mode Tampilan Alternatif (Tabel Administratif)
- Tetap mempertahankan switcher mode tampilan (*Kartu* vs *Tabel*) untuk pembimbing yang membutuhkan tampilan tabel data padat, namun dengan styling tabel Gentelella yang bersih dan selaras.

---

## 4. Rencana Perubahan File & Arsitektur Frontend

Sesuai aturan `AGENTS.md`, seluruh pemisahan CSS dan JS dilakukan secara ketat:

| File | Aksi | Alasan & Ringkasan Perubahan |
|---|---|---|
| `resources/views/pembimbing/tugas/index.blade.php` | **Modify** | 1. Hapus `@section('page_description')`.<br>2. Hapus teks `(F-08)` pada judul banner.<br>3. Hapus 640+ baris `<style>` inline dan 30 baris `<script>` inline.<br>4. Gunakan komponen `<x-gentelella.tile-stat>` untuk metrik.<br>5. Perbarui tab pill filter (hapus tab "Sedang Dikerjakan").<br>6. Terapkan struktur kartu tugas yang bersih dan proporsional selaras dengan peserta.<br>7. Muat asset terkompilasi melalui `@vite(['resources/css/pages/pembimbing/tugas-index.css', 'resources/js/pages/pembimbing/tugas-index.js'])`. |
| `resources/css/pages/pembimbing/tugas-index.css` | **Create** | Berisi seluruh styling khusus modul penugasan pembimbing: hero banner gradient Sleman, tile stats grid, segmented pill tabs, assignment cards, avatar mentee, dan tombol aksi `.btn-tugas-action`. |
| `resources/js/pages/pembimbing/tugas-index.js` | **Create** | Berisi interaksi frontend: switcher tampilan (Card vs Table) dengan penyimpanan `localStorage`, filter status, dan event handling tanpa inline `onclick`. |
| `vite.config.js` | **Modify** | Mendaftarkan entry point `resources/css/pages/pembimbing/tugas-index.css` dan `resources/js/pages/pembimbing/tugas-index.js` agar terbundle secara optimal. |

---

## 5. Pertanyaan & Konfirmasi untuk Pengguna

Sebelum eksekusi implementasi dimulai, mohon konfirmasi untuk aspek berikut:
1. **Pilihan Status Tab**:
   - Tab yang akan aktif adalah: `Semua Tugas`, `Ditugaskan`, `Selesai`, dan `⚠️ Perlu Evaluasi`. Bagian `Sedang Dikerjakan` telah dihapus sesuai permintaan. Apakah susunan 4 tab ini sudah sesuai keinginan Anda?
2. **Judul Banner**:
   - Teks `(F-08)` akan dihapus sehingga menjadi `"Tugas & Progres Magang"` (selaras dengan peserta) atau `"Manajemen Penugasan & Progres"`. Mana yang Anda lebih sukai?
3. **Persetujuan Perubahan Markup Blade**:
   - Sesuai Bagian 3.3 aturan `AGENTS.md`, mohon konfirmasi persetujuan untuk mengubah markup Blade pada `resources/views/pembimbing/tugas/index.blade.php` guna merealisasikan redesain ini dan memisahkan styling/skrip inline ke Vite assets.

