# Rencana Kerja: Redesain Dashboard Peserta (Soft, Simple, Bersih, & Bebas Clutter ala Landing Page)

**Tanggal:** 27 Oktober 2026  
**Status:** Menunggu Persetujuan Pengguna (*Pending Approval*)  
**Prinsip Desain:** *Less is More* — Mengeliminasi seluruh informasi yang tidak penting, menghilangkan istilah teknis yang kaku, serta menata ruang bernapas (*whitespace*) agar tampilan terasa tenang, manusiawi, elegan, dan menyejukkan mata seperti Landing Page baru.

---

## 1. Audit Eliminasi: Membuang Informasi & Elemen yang Tidak Penting

| Area | Elemen yang Dihapus / Dieliminasi | Alasan Penghapusan (*Why Remove?*) |
| :--- | :--- | :--- |
| **Hero Banner** | - Badge panjang *"MAGKOM SLEMAN • MAGANG AKTIF"* dengan dot neon.<br>- Banner gelap pekat yang kontras tajam seperti billboard.<br>- Teks duplikat nama pembimbing & periode yang diulang-ulang. | Peserta sudah login ke akunnya sendiri; tidak perlu badge status teknis yang berisik. Latar gelap pekat membuat mata lelah dan tidak menyatu dengan halaman yang terang. |
| **Kartu Metrik KPI** | - Indikator trend panah naik/turun (*chevron stock market indicator*).<br>- Teks teknis seperti *"5 rubrik dinas"*, *"trend 100% tuntas"*.<br>- Garis progress bar mikro warna-warni neon (biru, hijau, amber, ungu). | Dashboard magang mahasiswa bukan cockpit bursa saham. Peserta hanya butuh angka esensial yang jelas dan tenang. |
| **Sidebar Kanan (Aside)** | - Istilah teknis software internal seperti *"Aturan Logbook Harian (BR-09)"*.<br>- Teks dummy/generik seperti *"Ruang Kerja: Gedung Smart Room / Bidang"*.<br>- Kartu *"Akses Ringkas"* yang isinya mengulang link menu yang sudah ada di sidebar kiri. | Mengurangi beban kognitif (*cognitive overload*). Informasi teknis tidak bermanfaat bagi peserta dan link duplikat membuat tata letak berantakan. |
| **List Tugas & Logbook** | - Badge tenggat berwarna merah/kuning mencolok ("⚡ Hari Ini", "⚠️ Besok").<br>- Tombol-tombol kecil "Kerjakan" / "Buka" yang berdesakan. | Tampilan tabel/list dibuat bersih dengan tipografi yang tenang, tanggal tenggat yang mudah dibaca secara manusiawi, dan interaksi klik baris yang mulus. |

---

## 2. Konsep Desain Baru: Soft, Simple, & Enak Dilihat

### A. Sapaan Hero yang Bersih & Tenang (*Calm Welcome Section*)
- **Permukaan**: Kartu putih bersih (`#ffffff`) dengan border halus `#e2e8f0`, aksen vertikal tipis Sleman Navy `#1b3860` di sisi kiri.
- **Konten**:
  - Sapaan ramah: **"Halo, [Nama Peserta] 👋"**
  - Subtitle sejuk: **"Peserta Magang di [Nama Bidang Dinas] • Periode [Tgl Mulai] – [Tgl Selesai]"**
  - Hanya **1 Tombol Utama**: `+ Tulis Logbook Hari Ini` (Sleman Navy `#1b3860`, teks putih, sudut melengkung 10px, tanpa bayangan neon).
  - Tautan dokumen resmi: `Unduh SK Penerimaan (PDF)` dengan ikon dokumen minimalis.

### B. 4 Kartu Metrik Esensial & Elegan (*Clean Minimal KPI*)
Empat kartu persegi panjang berlatar putih bersih dengan jarak lega:
1. **Logbook Terisi**: Angka besar `Lexend` yang tenang (misal: **8 Minggu**), keterangan: *"Laporan tercatat rapi"* (atau *"1 laporan perlu revisi"* jika ada).
2. **Tugas Bimbingan**: Angka besar (misal: **2 Tugas Aktif**), keterangan: *"Menunggu penyelesaian"*.
3. **Masa Magang**: Angka besar (misal: **24 Hari Tersisa**), keterangan: *"Berakhir pada [Tanggal Selesai]"*.
4. **Evaluasi Akhir**: Nilai atau status (misal: **Dalam Proses** atau **Predikat A**), keterangan: *"Evaluasi pembimbing"*.
- **Tanpa progress bar warna-warni, tanpa panah saham**. Bersih dan proporsional.

### C. Tata Letak 2 Kolom Seimbang (*Balanced Content Rhythm*)
- **Kolom Kiri (Konten Utama, 66%)**:
  1. **Tugas Bimbingan Aktif**: Kartu putih dengan daftar tugas bernapas lega. Menampilkan judul tugas, kategori, dan tenggat waktu berformat tanggal santun (*"Tenggat: 31 Okt 2026"*).
  2. **Logbook Mingguan Terbaru**: Riwayat minggu per minggu yang rapi dengan status pill pastel lembut (Hijau pastel untuk Disetujui, Abu pastel untuk Terkirim).
- **Kolom Kanan (Informasi Pendamping, 34%)**:
  1. **Pembimbing Lapangan**: Profil pembimbing yang bersahabat (Avatar inisial Sleman Navy, Nama Pejabat/Pembimbing, Bidang Dinas, dan Email resmi).
  2. **Surat Keputusan (SK)**: Wadah download dokumen resmi yang rapi.
  3. **Pengingat Ramah**: Kalimat singkat menenangkan: *"Catat kegiatan magang Anda secara berkala untuk memudahkan penyusunan laporan akhir."*

### D. Tipografi, Warna, & Spasi Natural
- **Font Utama**:
  - `Lexend` untuk judul, sapaan, dan angka metrik (kesan ramah, modern, dan kokoh).
  - `Source Sans 3` untuk paragraf, keterangan, dan tabel (keterbacaan tinggi, nyaman dibaca lama).
- **Palet Warna**:
  - Background Halaman: Slate-50 `#f8fafc`.
  - Surface Kartu: Putih `#ffffff`.
  - Border: Subtle Slate `#e2e8f0` (1px).
  - Text Primary: Slate-900 `#0f172a`.
  - Text Secondary: Slate-600 `#475569`.
  - Text Muted: Slate-400 `#94a3b8`.
  - Aksen Primer: Sleman Navy `#1b3860`.
  - Aksen Sekunder: Gold `#f59e0b`.
- **Spacing**: Padding `24px` di setiap kartu, gap `20px` antar komponen, sudut lengkung `14px`.

---

## 3. Rincian Modifikasi File

| File | Tindakan Perubahan |
| :--- | :--- |
| `resources/views/peserta/dashboard/partials/_header.blade.php` | Merombak hero banner menjadi Clean Welcome Card (latar putih bersih atau soft navy halus, sapaan hangat, tanpa badge MAGKOM SLEMAN kaku, tanpa double header). |
| `resources/views/peserta/dashboard/partials/_kpi_bar.blade.php` | Menyederhanakan 4 metrik KPI: menghapus progress bar mikro warna-warni, menghapus trend indicator saham, fokus pada angka bersih dan keterangan natural. |
| `resources/views/peserta/dashboard/partials/_phase_aktif.blade.php` | Menata list tugas dan logbook dengan tipografi bersih, spasi vertikal lega, dan status pill pastel lembut. |
| `resources/views/peserta/dashboard/partials/_aside.blade.php` | Membuang teks "BR-09", membuang "Gedung Smart Room", membuang link duplikat ke sidebar. Menyisakan kartu profil pembimbing yang hangat dan kartu pengingat ramah. |
| `resources/views/peserta/dashboard.blade.php` | Mengoptimalkan scoped styles untuk mendukung estetika clean & soft ini. |

---

## 4. Langkah Implementasi & Validasi

1. **Langkah 1**: Rombak `_header.blade.php` menjadi Calm Welcome Card.
2. **Langkah 2**: Sederhanakan `_kpi_bar.blade.php` menjadi 4 kartu esensial tanpa elemen saham/progress bar neon.
3. **Langkah 3**: Bersihkan `_aside.blade.php` dari teks teknis dan duplikasi menu.
4. **Langkah 4**: Perbaiki tampilan daftar tugas dan logbook di `_phase_aktif.blade.php`.
5. **Langkah 5**: Kompilasi Blade views (`php artisan view:clear`) dan verifikasi keindahan visualnya.

---

## 5. Konvensi Bahasa
- Laporan dan komunikasi ke pengguna dalam **Bahasa Indonesia**.
- Kode sumber, kelas CSS, dan variabel dalam **Bahasa Inggris**.
