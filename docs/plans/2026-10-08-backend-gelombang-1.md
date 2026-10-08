# Plan: Implementasi Backend Gelombang 1 (Konfigurasi, Periode, Kuota & Landing Page)

- **Tanggal**: 2026-10-08
- **Status**: Completed (Lulus Semua Pengujian)
- **Author**: Antigravity (`web-implementation`)
- **Referensi**: [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md), [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md), [`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md), [`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md)

---

## 1. Tujuan
Mengimplementasikan fungsionalitas Gelombang 1 pada portal MagKom:
1. **F-11 & Master Periode**: Manajemen bulan magang (`bulan_magang`) beserta override status oleh SuperAdmin.
2. **F-05**: Pengaturan alokasi kuota umum per jurusan oleh Admin dengan validasi ketat (tidak boleh menurunkan kuota di bawah jumlah terisi).
3. **F-06**: Pengelolaan slot khusus internal oleh SuperAdmin (terisolasi dan rahasia).
4. **F-01**: Landing page publik yang menampilkan informasi kuota per jurusan (dikelompokkan SMK & Mahasiswa), status (Tersedia, Tutup, Segera), label Waiting List, dengan jaminan mutlak **slot khusus tidak pernah bocor** ke publik.

---

## 2. Rincian Komponen & File

| Komponen | File Path | Aksi | Deskripsi |
| :--- | :--- | :---: | :--- |
| **Migrations** | `database/migrations/2026_10_08_000001_create_bulan_magang_table.php` | Create | Tabel `bulan_magang` dengan `bulan_mulai` unik |
| | `database/migrations/2026_10_08_000002_create_kuota_table.php` | Create | Tabel `kuota` dengan foreign key, unique, check constraint |
| | `database/migrations/2026_10_08_000003_create_slot_khusus_table.php` | Create | Tabel `slot_khusus` dengan check constraint |
| **Models** | `app/Models/BulanMagang.php` | Create | Model periode bulan magang, casts, relasi |
| | `app/Models/Kuota.php` | Create | Model kuota jurusan, relasi ke BulanMagang & Jurusan |
| | `app/Models/SlotKhusus.php` | Create | Model slot khusus internal SuperAdmin |
| **Support & Service** | `app/Support/PeriodeMagang.php` | Create | Kalkulasi rentang tanggal magang (BR-08) |
| | `app/Support/Pengaturan.php` | Create | Cache helper untuk konfigurasi dinamis |
| | `app/Services/BulanStatusService.php` | Create | Single source of truth status ketersediaan & filter landing |
| **Requests** | `app/Http/Requests/BulanMagangRequest.php` | Create | Validasi form kelola bulan magang & override |
| | `app/Http/Requests/KuotaRequest.php` | Create | Validasi kuota (menolak jika kuota < terisi) |
| | `app/Http/Requests/SlotKhususRequest.php` | Create | Validasi slot khusus SuperAdmin |
| **Controllers** | `app/Http/Controllers/Public/LandingController.php` | Create | Controller landing page publik (F-01) |
| | `app/Http/Controllers/SuperAdmin/BulanController.php` | Create | CRUD bulan & override status (Audited) |
| | `app/Http/Controllers/SuperAdmin/SlotKhususController.php` | Create | CRUD slot khusus internal (Audited) |
| | `app/Http/Controllers/Admin/KuotaController.php` | Create | Pengaturan alokasi kuota jurusan (Audited) |
| **Routes** | `routes/web.php` | Modify | Daftarkan rute publik, admin kuota, dan superadmin slot |
| **Views** | `resources/views/layouts/app.blade.php` | Create | Base layout Blade modern & responsif |
| | `resources/views/public/landing.blade.php` | Create | Tampilan landing page publik sesuai PRD AC-01-1 s/d 6 |
| **Seeders** | `database/seeders/PeriodeKuotaSeeder.php` | Create | Sample bulan magang dan kuota untuk dev/test |
| **Tests** | `tests/Feature/LandingPageTest.php` | Create | Validasi AC-01-1..6, isolasi slot khusus, dan AC-05-3 |

---

## 3. Checklist Eksekusi
- [x] Buat migrasi `bulan_magang`, `kuota`, dan `slot_khusus` dengan foreign keys dan check constraints.
- [x] Buat model Eloquent `BulanMagang`, `Kuota`, dan `SlotKhusus`.
- [x] Buat class `PeriodeMagang`, `Pengaturan` helper, dan `BulanStatusService`.
- [x] Buat Form Requests dengan validasi aturan bisnis (AC-05-3).
- [x] Buat Controllers untuk Public, Admin, dan SuperAdmin yang mengintegrasikan `AuditService`.
- [x] Daftarkan rute di `routes/web.php` dengan middleware RBAC (`role:admin`, `role:superadmin`).
- [x] Implementasikan tampilan landing page di `resources/views/public/landing.blade.php`.
- [x] Jalankan migrasi dan seeder `PeriodeKuotaSeeder`.
- [x] Tulis dan jalankan pengujian otomatis `LandingPageTest`.
