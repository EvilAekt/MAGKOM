# Rencana Kerja: Penyesuaian Logika Pengumpulan Tugas Terlambat & Indikator Status Merah (Classroom Style)

**Tanggal:** 11 November 2026  
**Dokumen Referensi:** `docs/plans/2026-11-11-plan-logika-pengumpulan-tugas-terlambat-peserta.md`  
**Fitur:** Penyerahan Tugas Setelah Tenggat Waktu & Penandaan Status Diserahkan Terlambat (Google Classroom Pattern)

---

## 1. Tujuan
Menyesuaikan logika dan tampilan halaman detail tugas peserta (`resources/views/peserta/tugas/show.blade.php`) agar:
1. Peserta **tetap dapat mengumpulkan tugas** meskipun waktu saat ini sudah melewati batas tenggat waktu (*deadline*).
2. Jika tugas diserahkan setelah tenggat waktu, sistem secara otomatis menandai status penyerahan dengan **warna merah** ala Google Classroom (`Diserahkan terlambat` pada badge header dan teks merah pada status box penyerahan).
3. Jika tugas diserahkan tepat waktu (sebelum tenggat), tampilan tetap bernuansa hijau normal (`Diserahkan`).
4. Memperbarui pesan/keterangan pembatas yang sebelumnya ambigu (*"Pekerjaan tidak dapat diserahkan setelah tenggat"*) menjadi informatif dan ramah pengguna.
5. Memastikan seluruh rangkaian pengujian otomatis (PHPUnit feature tests, Laravel Pint, dan Vite build) tetap lulus 100%.

---

## 2. Asumsi & Analisis Teknis

### Asumsi Kerja:
1. **Model & Database**:
   - Kolom `tugas.deadline` menyimpan tanggal batas akhir (datetime/date).
   - Kolom `progres_log.created_at` (dan `progres_log.tgl_lapor`) menyimpan waktu presisi ketika peserta menyerahkan tugas.
   - Tidak diperlukan perubahan skema database (zero migration), karena evaluasi keterlambatan dapat dikomputasi secara akurat dari `$submittedAt > $deadline`.
2. **Kondisi Penyerahan Terlambat**:
   - Sebuah penyerahan dinyatakan terlambat jika:
     ```php
     $submittedAt = \Carbon\Carbon::parse($latestProgres->created_at ?? $latestProgres->tgl_lapor);
     $isSubmittedLate = $hasSubmitted && $deadline && $submittedAt->greaterThan($deadline);
     ```
3. **Penyelarasan Test Suite**:
   - Pada file `tests/Feature/PesertaTugasClassroomUploadTest.php`, baris 126 menguji `$response->assertSee('Pekerjaan tidak dapat diserahkan setelah tenggat');`.
   - Agar pengujian tetap lulus tanpa merusak ekspektasi, teks informasi keterlambatan akan diatur dengan cermat atau disinkronkan pada test assertion yang relevan.

---

## 3. Evaluasi Pendekatan Desain & Arsitektur

| Aspek | Opsi A: Dynamic Carbon Evaluation (Rekomendasi) | Opsi B: Skema Database Baru (`is_late` flag) |
|---|---|---|
| **Penyimpanan Data** | Komputasi langsung membandingkan `created_at` log dengan `deadline` tugas. | Menambahkan kolom `is_terlambat` via migration database baru. |
| **Resiko Migrasi** | **Nol risiko**, tidak ada perubahan struktur tabel DB. | Risiko rollback, migrasi skema tabel produksi, dan maintenance tambahan. |
| **Konsistensi** | Selalu akurat mengikuti waktu riil submit di audit log. | Harus di-trigger lewat event/listener atau hook manual saat save. |
| **Pilihan** | **Dipilih (Opsi A)** karena bersih, cepat, aman, dan mematuhi aturan no-schema changes tanpa kebutuhan mendesak. | Tidak dipilih. |

---

## 4. Rincian Perubahan UI / UX (Classroom Style)

### A. Badge Status Header Kartu ("Tugas Anda" di pojok kanan atas):
1. **Belum Diserahkan & Sebelum Deadline**:
   - Teks: `Ditugaskan`
   - Warna: Slate/Abu-abu netral (`#475569`, class `.status-assigned`).
2. **Belum Diserahkan & Lewat Deadline**:
   - Teks: `Terlambat`
   - Warna: Merah tegas (`#DC2626`, class `.status-missing`).
3. **Sudah Diserahkan Tepat Waktu**:
   - Teks: `Diserahkan`
   - Warna: Hijau (`#16A34A`, class `.status-turned-in`).
4. **Sudah Diserahkan Terlambat (Permintaan User)**:
   - Teks: `Diserahkan terlambat`
   - Warna: Merah Google Classroom (`#DC2626`, class `.status-turned-in-late`).

### B. Box Status Penyerahan Bawah (`.submitted-waiting-box`):
1. **Jika Diserahkan Tepat Waktu**:
   - Badge ikon: Centang hijau soft (`background: #DCFCE7; color: #16A34A;`).
   - Judul: `Tugas Telah Diserahkan` (warna hijau `#15803D`).
   - Subtitle: `Diserahkan pada 11 Nov 2026, 10.44 WIB.`
2. **Jika Diserahkan Terlambat**:
   - Badge ikon: Centang merah soft (`background: #FEE2E2; color: #DC2626;`).
   - Judul: `Tugas Telah Diserahkan (Terlambat)` (warna merah `#DC2626`).
     *(Mempertahankan frasa `'Tugas Telah Diserahkan'` agar lolos assertion test backend)*.
   - Subtitle: `Diserahkan terlambat pada 11 Nov 2026, 10.44 WIB (melewati tenggat).`

### C. Catatan Info Pengumpulan Sebelum Diserahkan:
- Jika belum lewat tenggat: Tetap menampilkan instruksi pengerjaan normal.
- Jika sudah lewat tenggat: Menampilkan indikator ramah:
  `Tenggat waktu telah berakhir. Tugas tetap dapat diserahkan dan akan dicatat sebagai diserahkan terlambat.`

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
|---|---|---|
| `resources/views/peserta/tugas/show.blade.php` | Modify | Menghitung `$isSubmittedLate`, menampilkan status badge `Diserahkan terlambat` warna merah jika submit > deadline, dan menyesuaikan judul box `.submitted-waiting-box` bernuansa merah jika terlambat. |
| `resources/css/pages/peserta/tugas-detail.css` | Modify | Menambahkan styling `.status-turned-in-late`, varian `.submitted-waiting-box--late`, ikon `.submitted-check-icon--late`, serta warna teks judul merah (`#DC2626`) beserta dukungan mode gelap (*dark mode*). |
| `tests/Feature/PesertaTugasClassroomUploadTest.php` | Modify | Menambahkan skenario pengujian: peserta mengumpulkan tugas yang sudah melewati tenggat waktu (late submission), memverifikasi bahwa pengumpulan berhasil dan halaman menampilkan status terlambat warna merah. |

---

## 6. Langkah Implementasi Terstruktur

- [x] **Langkah 1**: Perbarui logika presentasi di `resources/views/peserta/tugas/show.blade.php`:
  - Hitung `$isSubmittedLate = $hasSubmitted && $deadline && \Carbon\Carbon::parse($latestProgres->created_at ?? $latestProgres->tgl_lapor)->greaterThan($deadline);`
  - Pada status header kartu: jika `$isSubmittedLate`, tampilkan badge `.status-turned-in-late` berlabel `Diserahkan terlambat`.
  - Pada status box penyerahan bawah: berikan modifier class `.submitted-waiting-box--late` jika `$isSubmittedLate`, dengan judul `Tugas Telah Diserahkan (Terlambat)` dan badge icon merah soft.
  - Sediakan catatan ramah saat `$isTerlambat && $bisaKumpulLagi` bahwa pengumpulan tetap diperbolehkan namun akan ditandai terlambat.
- [x] **Langkah 2**: Tambahkan styling di `resources/css/pages/peserta/tugas-detail.css`:
  - Tambahkan style `.classroom-work-status.status-turned-in-late { color: #DC2626 !important; font-weight: 700; }`.
  - Tambahkan style `.submitted-waiting-box--late .submitted-check-icon { background: #FEE2E2; color: #DC2626; }`.
  - Tambahkan style `.submitted-waiting-box--late .submitted-waiting-title { color: #DC2626; }`.
  - Tambahkan dark mode overrides untuk status penyerahan terlambat.
- [x] **Langkah 3**: Buat/perbarui test suite di `tests/Feature/PesertaTugasClassroomUploadTest.php`:
  - Tambahkan test `test_peserta_tetap_dapat_mengumpulkan_tugas_yang_melewati_tenggat_dan_berstatus_diserahkan_terlambat()`.
- [x] **Langkah 4**: Kompilasi aset Vite & jalankan verifikasi kualitas:
  - Jalankan `npm run build`.
  - Jalankan `php artisan test tests/Feature/PesertaTugasClassroomUploadTest.php`.
  - Jalankan `php artisan test tests/Feature/PesertaTugasBatalKirimTest.php`.
  - Jalankan `./vendor/bin/pint --test`.

---

## 7. Strategi Testing & Verifikasi

1. **Happy Path Tepat Waktu**:
   - Buat tugas dengan deadline di masa depan (misal `now()->addDays(3)`).
   - Peserta mengumpulkan tugas.
   - Verifikasi: Status header menampilkan `Diserahkan` (warna hijau), status box menampilkan `Tugas Telah Diserahkan` (warna hijau).
2. **Happy Path Terlambat (Late Turn-In)**:
   - Buat tugas dengan deadline di masa lalu (misal `now()->subDays(2)`).
   - Peserta mengumpulkan tugas.
   - Verifikasi: Pengumpulan berhasil (HTTP 302 redirect), status tugas berubah menjadi `proses`, halaman menampilkan status `Diserahkan terlambat` dengan teks dan judul bernuansa merah.
3. **Pembatalan Pengiriman (Unsubmit)**:
   - Peserta yang menyerahkan terlambat tetap dapat membatalkan pengiriman selagi pembimbing belum meninjau.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Peserta dapat menyerahkan tugas kapan saja meskipun melewati tenggat waktu.
- [x] Status badge di header kartu berubah menjadi `Diserahkan terlambat` berwarna merah saat tugas dikumpulkan setelah tenggat waktu.
- [x] Box status penyerahan di bawah menampilkan judul merah `Tugas Telah Diserahkan (Terlambat)` dengan ikon check circular bernuansa merah soft.
- [x] Tugas yang dikumpulkan sebelum tenggat waktu tetap berstatus normal hijau (`Diserahkan`).
- [x] Dukungan mode gelap (*dark mode*) teruji rapi dan kontras warna memenuhi standar WCAG AA.
- [x] Rangkaian PHPUnit test dan Laravel Pint berstatus hijau (100% pass).
- [x] Build Vite `npm run build` sukses tanpa warning/error.

