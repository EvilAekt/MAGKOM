# Rencana Implementasi Gelombang 3: Pelaksanaan & Penilaian MagKom

## 1. Ikhtisar & Tujuan
Gelombang 3 mengimplementasikan siklus aktif pelaksanaan magang dan evaluasi penilaian akhir:
1. **Penugasan & Progres (F-08)**: Pembimbing memberikan tugas kerja dengan deadline & kompleksitas. Peserta mengunggah progres berkas/catatan. Runtime accessor `terlambat` (**BR-16**; tidak pernah disimpan di DB). SuperAdmin read-only (**AC-08-5**).
2. **Logbook Harian (F-09)**: Peserta mengisi logbook kegiatan harian dengan pembatasan mundur maksimal H-3 (**BR-09**) dan relasi unik per tanggal per penempatan. Pembimbing memberikan evaluasi/catatan.
3. **Penilaian Rubrik Terbobot (F-10)**: Pembimbing memberikan skor 0-100 per kriteria rubrik aktif. Sistem mengkalkulasi nilai akhir terbobot dan predikat huruf (A-E). Dukungan draft, final, serta rekam jejak revisi final ke `penilaian_riwayat` dengan alasan min 10 karakter. SuperAdmin strictly read-only (**AC-10-7**).
4. **Penyelesaian Magang & Masa Tenggang (BR-07, BR-10)**: Penempatan hanya dapat ditandai `selesai` jika penilaian telah `final`. Tanggal nonaktif dihitung otomatis berdasarkan setting `masa_tenggang_bulan` (default 1 bulan). Scheduler `magkom:nonaktifkan-peserta` menonaktifkan akun yang melewati batas masa tenggang secara idempotent.
5. **Ekspor Lembar Nilai PDF (F-12, AC-12)**: Menghasilkan PDF resmi Lembar Nilai Magang on-the-fly untuk peserta dengan status penilaian `final`.

---

## 2. Struktur Komponen yang Dibuat

### A. Database Migrations
- `database/migrations/2026_10_08_000009_create_tugas_table.php`
- `database/migrations/2026_10_08_000010_create_progres_log_table.php`
- `database/migrations/2026_10_08_000011_create_logbook_table.php`
- `database/migrations/2026_10_08_000012_create_penilaian_tables.php` (`penilaian`, `penilaian_detail`, `penilaian_riwayat`)

### B. Eloquent Models
- `app/Models/Tugas.php` (dengan accessor runtime `terlambat` & scope `scopeTerlambat`)
- `app/Models/ProgresLog.php`
- `app/Models/Logbook.php`
- `app/Models/Penilaian.php`
- `app/Models/PenilaianDetail.php`
- `app/Models/PenilaianRiwayat.php`
- Update relasi di `app/Models/Penempatan.php`

### C. Services & Command
- `app/Services/ScoringService.php` (kalkulasi nilai terbobot, predikat huruf, simpan draft/final, catat riwayat revisi)
- `app/Services/MasaTenggangService.php` (kalkulasi tanggal nonaktif, validasi BR-10 penyelesaian magang, penonaktifan akun kedaluwarsa)
- `app/Console/Commands/NonaktifkanPesertaSelesai.php` (signature `magkom:nonaktifkan-peserta`)

### D. PDF Template (Blade)
- `resources/views/pdf/lembar-nilai.blade.php` (format resmi A4, kop surat, identitas peserta & pembimbing, tabel rincian nilai rubrik, nilai akhir, predikat, tanggal penetapan, tanda tangan)

### E. Form Requests & Policies
- Form Requests: `TugasRequest`, `ProgresLogRequest`, `ReviewProgresRequest`, `LogbookRequest`, `ReviewLogbookRequest`, `PenilaianRequest`.
- Policies: `TugasPolicy`, `LogbookPolicy`, `PenilaianPolicy`.

### F. Controllers & Routes
- Pembimbing: `Pembimbing\TugasController`, `Pembimbing\LogbookReviewController`, `Pembimbing\PenilaianController`.
- Peserta: `Peserta\TugasController`, `Peserta\LogbookController`, `Peserta\NilaiController`.
- Integrasi rute di `routes/web.php`.

### G. Automated Tests
- `tests/Feature/TugasLogbookTest.php`
- `tests/Feature/ScoringAndCompletionTest.php`
