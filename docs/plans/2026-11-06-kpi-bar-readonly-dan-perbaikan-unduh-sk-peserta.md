# Rencana Kerja: Kartu KPI Bar Read-Only & Perbaikan Tautan Unduh SK Magang

## 1. Konteks & Permintaan Pengguna
Pengguna meminta dua penyesuaian pada tampilan dashboard peserta (`/peserta/dashboard`):
1. **Kartu Informasi Metrik di Bawah Banner (`_kpi_bar.blade.php`)**:
   - Diubah menjadi hanya bisa dilihat saja (read-only/statis).
   - Menghilangkan tombol-tombol link aksi (`Buka →`, `Detail →`, dan raw HTML entity `Nilai &rarr;`).
   - Mengubah elemen pembungkus dari tag `<a>` menjadi `<div>` sehingga tidak ada hover interaksi klik / tooltip title yang mengganggu.
2. **Perbaikan Tautan Unduh SK Magang (`_header.blade.php`)**:
   - Memastikan tautan "Unduh SK Magang (PDF)" mengarah ke route unduh berkas yang benar (`peserta.penempatan.surat-diterima`) sehingga file PDF langsung terunduh secara resmi saat diklik.
   - Memoles tampilan tautan agar serasi, jelas, dan profesional dengan visual token Sleman Gold.

---

## 2. Analisis Perubahan File

### A. `resources/views/peserta/dashboard/partials/_kpi_bar.blade.php`
- **Sebelumnya**:
  - Kartu 1: `<a href="{{ route('peserta.logbook.index') }}" class="tile_stats_count saas-kpi-link" title="Buka Logbook Mingguan">` dengan `<span ...>Buka &rarr;</span>`.
  - Kartu 2: `<a href="{{ route('peserta.tugas.index') }}" class="tile_stats_count saas-kpi-link" title="Buka Tugas Magang">` dengan `<span ...>Detail &rarr;</span>`.
  - Kartu 3: `<a href="{{ route('peserta.pendaftaran.index') }}" class="tile_stats_count saas-kpi-link" title="Lihat Sisa Hari Magang">`.
  - Kartu 4: `<a href="{{ $card4Url }}" ... class="tile_stats_count saas-kpi-link">` dengan `<span ...>{{ $hasWaPembimbing && !$isFinalNilai ? 'WhatsApp &rarr;' : 'Nilai &rarr;' }}</span>`.
- **Perubahan**:
  - Ganti keempat pembungkus `<a>` menjadi `<div class="tile_stats_count" style="padding: 0;">`.
  - Hapus tombol/span `Buka →`, `Detail →`, dan `Nilai &rarr;` di footer `.count_bottom`.
  - Hapus variabel `$card4Url` yang tidak lagi digunakan.
  - Kartu kini 100% murni informational display statis.

### B. `resources/views/peserta/dashboard/partials/_header.blade.php`
- **Sebelumnya**:
  - Mengarah ke rute stream pratinjau: `route('peserta.penempatan.surat-diterima.pratinjau', $penempatan)`.
- **Perubahan**:
  - Ubah rute menjadi download langsung: `route('peserta.penempatan.surat-diterima', $penempatan)`.
  - Perbaiki ikon dan styling pill tombol dengan border halus Sleman Gold agar kontras, proporsional, dan mudah diakses.

### C. `resources/views/peserta/dashboard.blade.php` (CSS)
- Menyesuaikan style `.saas-hero-link-sk` dan memastikan `.saas-kpi-grid .tile_stats_count` tidak memiliki `cursor: pointer`.

---

## 3. Tahapan Eksekusi
1. Update `resources/views/peserta/dashboard/partials/_kpi_bar.blade.php`.
2. Update `resources/views/peserta/dashboard/partials/_header.blade.php`.
3. Update styling di `resources/views/peserta/dashboard.blade.php`.
4. Jalankan build asset `npm run build` dan automated test PHPUnit.
5. Verifikasi visual dan fungsional.
