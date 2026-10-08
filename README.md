# MagKom (Magang Kominfo)

**Portal Manajemen Magang Terpadu Dinas Komunikasi dan Informatika**

Portal web terpadu untuk mengelola seluruh siklus magang di Dinas Komunikasi dan Informatika: pendaftaran online transparan, alokasi kuota per jurusan berbasis atomisitas database, verifikasi berkas privat, pembagian tugas ber-deadline, logbook aktivitas harian, penilaian berbasis rubrik kompetensi terbobot, dan penerbitan Lembar Nilai PDF resmi on-the-fly.

---

## 🚀 Fitur Utama (Rilis 1 - MVP)

1. **Transparansi Kuota & Zero Leak Jalur Khusus (F-01, F-05, F-06)**:
   - Tampilan ketersediaan kuota per jurusan (SMK & Mahasiswa) secara real-time di Landing Page.
   - Slot khusus internal terisolasi penuh dari konsumsi publik (*Zero Leak Protection*).
   - Penanganan otomatis antrean *Waiting List* saat kuota reguler penuh.
2. **Pendaftaran & Berkas Privat (F-02, F-03, F-04)**:
   - Manajemen kelengkapan profil peserta (NIM/NISN, asal institusi, jenjang).
   - Pengunggahan berkas persyaratan ke disk privat terlindungi *signed URL*.
   - Verifikasi berkas oleh admin dengan alur revisi (*Perlu Perbaikan*).
3. **Penempatan Atomik & Beban Kerja (F-07)**:
   - Transaksi database atomik ber-lock (`lockForUpdate`) untuk mencegah kondisi *race-condition* atau over-quota.
   - Alokasi pembimbing lapangan dan unit bidang kerja.
4. **Pelaksanaan Magang (F-08, F-09)**:
   - Pemberian tugas dengan penghitungan status terlambat dinamis pada runtime (`BR-16`).
   - Logbook harian berbatas waktu pengisian mundur maksimal (`BR-09`) dan review catatan pembimbing.
5. **Penilaian Terbobot & Dokumen Resmi (F-10, F-12)**:
   - Penilaian akhir berbasis 5 rubrik kompetensi dengan kalkulasi terbobot dan predikat (A–E).
   - Audit trail pencatatan riwayat revisi nilai beserta alasan perubahan (`AC-10`).
   - Unduh langsung dokumen resmi *Lembar Nilai PDF* on-the-fly (`AC-12`).
   - Otomatisasi masa tenggang akun peserta via console scheduler idempotent (`BR-07`).
6. **Audit Trail Komprehensif & Keamanan (F-13)**:
   - Log audit append-only yang kebal manipulasi (`AuditLog`).
   - Sanitasi otomatis kredensial sensitif (`[DISAMARKAN]`).
   - Header keamanan HTTP ketat (`nosniff`, `DENY`, CSP) & Rate Limiting.

---

## 🛠️ Tech Stack & Prasyarat

- **Framework**: Laravel 10.x
- **Bahasa**: PHP 8.2 / 8.3
- **Database**: MySQL 8.0+ / MariaDB (InnoDB Engine)
- **PDF Engine**: Barryvdh DomPDF (`barryvdh/laravel-dompdf`)
- **Penyimpanan**: Storage Disk Private (`local` / S3-ready)

---

## 📦 Panduan Instalasi Lokal

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/EvilAekt/MAGKOM.git
   cd MAGKOM
   ```

2. **Instal Dependensi**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Sesuaikan konfigurasi koneksi basis data di `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=magkom_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Migrasi & Seeding Data Awal**:
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**:
   ```bash
   php artisan serve
   ```

---

## 🧪 Pengujian Otomatis (Test Suite)

Proyek ini dilengkapi dengan 100% automated feature test suite untuk seluruh aturan bisnis dan acceptance criteria:

```bash
php artisan test
```

Rangkaian uji mencakup:
- `CoreFoundationTest`: RBAC, enum casting, proteksi audit trail append-only.
- `LandingPageTest`: Visualisasi kuota umum, proteksi zero leak slot khusus.
- `PendaftaranPesertaTest`: Alur pendaftaran, upload berkas, dan verifikasi admin.
- `QuotaConcurrencyTest`: Integritas transaksi atomik alokasi kuota.
- `TugasLogbookTest`: Pengisian logbook batas mundur dan runtime accessor terlambat.
- `ScoringAndCompletionTest`: Rubrik penilaian terbobot, revisi nilai, ekspor PDF, scheduler masa tenggang.
- `EndToEndUatTest`: Validasi lengkap skenario **UAT-1 s/d UAT-8** sesuai PRD Bab 12.3.

---

## 📚 Dokumentasi Proyek

Dokumentasi rancangan lengkap tersedia pada direktori [`docs/`](docs/):
- [`01-PRD-MagKom.md`](docs/01-PRD-MagKom.md): Product Requirements Document v1.1
- [`02-TDD-MagKom.md`](docs/02-TDD-MagKom.md): Technical Design Document
- [`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md): Roadmap Pelaksanaan & Riwayat Gelombang
- [`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md): Kamus Data & Skema Basis Data
- [`05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md): Matriks Keamanan & Hak Akses

---

## 📄 Lisensi
Hak Cipta © 2026 Dinas Komunikasi dan Informatika. Dikembangkan untuk efisiensi dan transparansi tata kelola magang.
