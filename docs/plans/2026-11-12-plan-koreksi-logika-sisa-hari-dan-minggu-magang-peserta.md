# Rencana Kerja: Koreksi Logika Perhitungan Sisa Hari & Minggu Magang Peserta

**Tanggal:** 12 November 2026  
**Dokumen Rencana:** `docs/plans/2026-11-12-plan-koreksi-logika-sisa-hari-dan-minggu-magang-peserta.md`  
**Cakupan Fitur:** Perbaikan logika perhitungan sisa hari magang, minggu berjalan, penanganan fase pra-magang (sebelum tanggal mulai), dan dinamisasi indikator bar pada Dashboard Peserta.

---

## 1. Tujuan
Memperbaiki kalkulasi dan visualisasi durasi magang pada Dashboard Peserta (`peserta.dashboard`) agar:
1. **Sisa Hari Magang Akurat**:
   - Jika magang **belum dimulai** (`today < tgl_mulai`): Sisa hari magang dihitung dari **total durasi magang** (`tgl_mulai` s/d `tgl_selesai`, misal 90 hari / 3 bulan), BUKAN dihitung dari hari ini sebelum tanggal mulai yang menghasilkan angka bengkak (seperti 108 hari).
   - Jika magang **sedang berjalan** (`tgl_mulai <= today <= tgl_selesai`): Sisa hari dihitung presisi dari hari ini sampai `tgl_selesai`.
   - Jika magang **telah berakhir** (`today > tgl_selesai`): Sisa hari bernilai `0`.
2. **Koreksi Minggu Magang (Minggu Berjalan & Pra-Magang)**:
   - Jika magang belum dimulai: Sistem tidak memaksa mengklaim "Minggu ke-1 berjalan (8%)", melainkan menandai status pra-magang/belum dimulai dengan info hitung mundur (*"Dimulai dalam X hari"*), dan chip logbook berstatus mendatang.
   - Jika magang sedang berjalan: Nomor minggu berjalan (`minggu_aktif`) dihitung presisi berdasarkan blok 7 harian dari `tgl_mulai` (Minggu 1 s/d Total Minggu).
3. **Dinamisasi 4 Bar Indikator pada Kartu "Sisa Hari Magang" (`_bento_alerts.blade.php`)**:
   - Menghilangkan kode hardcode `@for($d = 1; $d <= 4; $d++) {{ $d <= 3 ? ... }}`.
   - Menggantinya dengan perhitungan proporsi dinamis berbasis sisa hari magang riil.

---

## 2. Analisis Masalah Saat Ini (Root Cause Analysis)

1. **`app/Http/Controllers/Peserta/DashboardController.php` (Baris 120-126)**:
   ```php
   // Logika Lama:
   $diff = now()->startOfDay()->diffInDays(Carbon::parse($tglSelesai)->startOfDay(), false);
   $sisaHari = max(0, (int) $diff);
   ```
   *Akar Masalah:* Tidak mengecek `$penempatan->tgl_mulai`. Jika magang baru mulai bulan depan (misal 1 Des 2026 s/d 28 Feb 2027), sistem menghitung hari dari 12 Nov ke 28 Feb (108 hari), padahal durasi magang sebenarnya hanya 90 hari!
2. **`app/Models/Penempatan.php` (`getMingguAktifAttribute`)**:
   ```php
   // Logika Lama:
   if ($today->lt($mulai)) {
       return 1;
   }
   ```
   *Akar Masalah:* Ketika magang belum dimulai, fungsi langsung mengembalikan `1`, sehingga dashboard menampilkan seolah-olah peserta sudah berada di Minggu ke-1, padahal kegiatan magang belum dimulai.
3. **`resources/views/peserta/dashboard/partials/_bento_alerts.blade.php` (Baris 30-34)**:
   ```blade
   {{-- Logika Lama: --}}
   @for($d = 1; $d <= 4; $d++)
       <span class="matrix-dot {{ $d <= 3 ? 'dot-filled-dark' : 'dot-faded-dark' }}"></span>
   @endfor
   ```
   *Akar Masalah:* Bar di-hardcode `3` dari `4` terisi statis, tidak bereaksi terhadap sisa hari magang aktual.

---

## 3. Evaluasi Opsi Solusi

| Kriteria | Opsi 1: Unified Lifecycle Magang (Rekomendasi) | Opsi 2: Patch Parsial Inline |
|---|---|---|
| **Penanganan Pra-Magang** | Lengkap: Membedakan status pra-magang, aktif berjalan, dan selesai. | Hanya mengubah rumus `$diff`, tampilan status tetap rancu. |
| **Dinamisasi Bar** | Bar 4 segmen dihitung dinamis sesuai persentase sisa hari. | Bar dibiarkan statis atau dihilangkan. |
| **Konsistensi UI** | Selaras antara Hero Banner, Alert Sisa Hari, Stepper Logbook, dan KPI. | Rawan desinkronisasi antar widget di dashboard. |
| **Pilihan** | **Dipilih (Opsi 1)** | Ditolak |

---

## 4. Rincian Teknis Perubahan

### A. Backend Controller & Model
1. **`app/Http/Controllers/Peserta/DashboardController.php`**:
   - Menghitung siklus waktu magang yang komprehensif:
     ```php
     $today = now()->startOfDay();
     $tglMulai = $penempatan?->tgl_mulai ? Carbon::parse($penempatan->tgl_mulai)->startOfDay() : null;
     $tglSelesai = $penempatan?->tgl_selesai ? Carbon::parse($penempatan->tgl_selesai)->startOfDay() : null;
     
     $isBelumMulai = false;
     $hariMenujuMulai = 0;
     $totalHariMagang = 1;
     $sisaHari = null;
     $progresHariPct = 0;

     if ($penempatan && $tglMulai && $tglSelesai) {
         $totalHariMagang = max(1, $tglMulai->diffInDays($tglSelesai));

         if ($today->lt($tglMulai)) {
             // Fase Pra-Magang (Belum dimulai)
             $isBelumMulai = true;
             $hariMenujuMulai = (int) $today->diffInDays($tglMulai);
             $sisaHari = $totalHariMagang;
             $progresHariPct = 0;
         } elseif ($today->gt($tglSelesai)) {
             // Fase Selesai
             $sisaHari = 0;
             $progresHariPct = 100;
         } else {
             // Fase Aktif Berjalan
             $sisaHari = max(0, (int) $today->diffInDays($tglSelesai));
             $hariBerjalan = max(0, (int) $tglMulai->diffInDays($today));
             $progresHariPct = min(100, (int) round(($hariBerjalan / $totalHariMagang) * 100));
         }
     }
     ```
   - Mengirimkan variabel pendukung: `isBelumMulai`, `hariMenujuMulai`, `totalHariMagang`, `progresHariPct` ke view.

2. **`app/Models/Penempatan.php`**:
   - Perbarui accessor `getMingguAktifAttribute()`:
     - Jika `$today->lt($mulai)`: kembalikan `0` (atau flag indikator pra-magang).
     - Jika sedang berjalan: `(int) floor($mulai->diffInDays($today) / 7) + 1`.
   - Perbarui `getDaftarMingguMagang()` agar jika `$isBelumMulai`, semua minggu berstatus `mendatang` (`is_mendatang = true`).

### B. Frontend Blade Views
1. **`resources/views/peserta/dashboard/partials/_bento_alerts.blade.php`**:
   - Menghitung bar terisi dinamis:
     ```php
     // 4 Bar mewakili 4 kuartil sisa waktu magang
     $barsFilled = $totalHariMagang > 0 ? max(0, min(4, (int) round(($sisaHariVal / $totalHariMagang) * 4))) : 4;
     ```
   - Tampilkan sub-info ramah jika magang belum dimulai:
     `"Mulai dalam {$hariMenujuMulai} hari"` atau `"Durasi {$totalHariMagang} hari"`.
2. **`resources/views/peserta/dashboard/partials/_bento_hero.blade.php`**:
   - Jika `$isBelumMulai`: Hero gauge menampilkan status pra-magang (`H-{$hariMenujuMulai}` dan `"Segera Dimulai"` alih-alih `"Minggu ke-1"`).
3. **`resources/views/peserta/dashboard/partials/_bento_stepper_logbook.blade.php`**:
   - Jika `$isBelumMulai`: Label stepper menampilkan `"Mulai [Tgl Mulai]"` tanpa menyorot M1 sebagai minggu berjalan aktif.

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
|---|---|---|
| `app/Http/Controllers/Peserta/DashboardController.php` | Modify | Menghitung `$sisaHari`, `$isBelumMulai`, `$hariMenujuMulai`, dan `$progresHariPct` secara presisi. |
| `app/Models/Penempatan.php` | Modify | Menyempurnakan `getMingguAktifAttribute` untuk menangani kondisi pra-magang (`0`). |
| `resources/views/peserta/dashboard/partials/_bento_alerts.blade.php` | Modify | Menjadikan 4 bar indikator bergerak dinamis sesuai persentase sisa hari dan menampilkan status pra-magang. |
| `resources/views/peserta/dashboard/partials/_bento_hero.blade.php` | Modify | Menyesuaikan gauge lingkaran saat magang belum dimulai. |
| `resources/views/peserta/dashboard/partials/_bento_stepper_logbook.blade.php` | Modify | Menyesuaikan status chip minggu berjalan saat pra-magang. |
| `tests/Feature/DashboardLayoutTest.php` | Modify | Menambahkan pengujian kalkulasi sisa hari dan minggu berjalan (pra-magang & aktif). |

---

## 6. Langkah Implementasi Terstruktur

- [x] **Langkah 1**: Perbarui logika di `app/Http/Controllers/Peserta/DashboardController.php` untuk memvalidasi tanggal mulai dan tanggal selesai penempatan.
- [x] **Langkah 2**: Perbarui model `app/Models/Penempatan.php` pada accessor `getMingguAktifAttribute` dan `getDurasiBulanAttribute`.
- [x] **Langkah 3**: Perbarui view `_bento_alerts.blade.php` untuk membuat bar indikator dinamis berbasis proporsi riil dan jumlah bulan pilihan peserta.
- [x] **Langkah 4**: Perbarui view `_bento_hero.blade.php` dan `_bento_stepper_logbook.blade.php` agar ramah fase pra-magang.
- [x] **Langkah 5**: Jalankan `php artisan view:clear`, `npm run build`, dan eksekusi test suite PHPUnit serta Laravel Pint.

---

## 7. Strategi Testing & Verifikasi

1. **Skenario 1 (Magang Belum Dimulai / Pra-Magang)**:
   - Penempatan mulai 1 Desember 2026, selesai 28 Februari 2027 (hari ini 12 Nov 2026).
   - Verifikasi: Sisa hari = 89/90 hari (total durasi), bukan 108 hari.
   - Verifikasi: Bar terisi penuh sesuai durasi bulan (misal 3 dari 3 bar), status "Segera Dimulai (H-X)".
2. **Skenario 2 (Magang Sedang Berjalan Aktif)**:
   - Penempatan mulai 19 Oktober 2026, selesai 17 Januari 2027 (hari ini 12 Nov 2026).
   - Verifikasi: Sisa hari = 66 hari.
   - Verifikasi: Minggu aktif = Minggu ke-4 dari 13 minggu.
   - Verifikasi: Bar terisi = proporsional sisa waktu.
3. **Skenario 3 (Magang Selesai)**:
   - Penempatan selesai di masa lalu.
   - Verifikasi: Sisa hari = 0 hari, bar = 0 terisi.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Sisa hari magang tidak bengkak saat akun baru ditempatkan di bulan mendatang (menggunakan total durasi magang yang dipilih).
- [x] Minggu magang mencerminkan tanggal nyata (tidak mengklaim Minggu ke-1 jika magang belum dimulai; menampilkan Pra-Magang H-X).
- [x] Bar indikator pada kartu "Sisa Hari Magang" dinamis berbasis jumlah bulan pilihan peserta (1, 2, 3, 6) dan proporsi sisa hari.
- [x] Seluruh automated test suite PHPUnit berstatus 100% PASS (13/13 test passed, 88 assertions).
- [x] Laravel Pint lulus format kode bersih tanpa pelanggaran.

