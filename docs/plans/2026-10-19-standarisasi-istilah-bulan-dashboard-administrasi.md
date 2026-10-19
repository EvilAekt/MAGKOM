# Rencana Kerja: Standardisasi Istilah "Periode" Menjadi "Bulan" di Dashboard Administrasi Kominfo

**Tanggal Pembuatan**: 19 Oktober 2026  
**Status**: ✅ Selesai Diimplementasikan & Lolos Seluruh Pengujian Otomatis  
**Branch**: `test-1`  
**Konteks**: Berdasarkan permintaan pengguna untuk menyelaraskan istilah pada Dashboard Administrasi Kominfo dari "Periode" menjadi "Bulan" (contoh: *Periode Magang* &rarr; *Bulan Magang*, *Periode November 2026* &rarr; *Bulan November 2026*, *Pilih Periode* &rarr; *Pilih Bulan*).

---

## 1. Tujuan
Menstandardisasi seluruh istilah dan label pada antarmuka **Dashboard Administrasi Kominfo** (Portal Admin & Super Admin) agar konsisten menggunakan istilah **"Bulan"** / **"Bulan Magang"** alih-alih "Periode", selaras dengan penamaan model data resmi (`BulanMagang`), menu sidebar kedinasan, dan arahan pengguna.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi 1**: Perubahan berfokus pada lapisan visual/antarmuka (Blade Views) di seluruh modul administrasi internal (`admin.*` dan `superadmin.*`).
- **Asumsi 2**: Struktur database (`tabel bulan_magang`), kolom database, nama relasi Eloquent (`bulanMagang()`), nama rute (`route('admin.dashboard')`), dan parameter query (`?bulan_id=`) **TIDAK DIUBAH** agar tidak menimbulkan *breaking changes* pada logika backend.
- **Asumsi 3**: Teks "Periode" yang merujuk pada rentang tanggal spesifik penempatan personal (misalnya `01 Nov 2026 s/d 31 Jan 2027` pada lembar tugas/logbook) tetap dapat dipertahankan bila merujuk rentang durasi kerja individu, namun semua konteks pemilihan gelombang, kuota, antrean, dan pendaftaran diubah menjadi "Bulan / Bulan Magang".

---

## 3. Evaluasi Pendekatan Teknis

| Parameter | Opsi 1: Standardisasi Layer Presentasi & UI Dashboard (Rekomendasi) | Opsi 2: Refactor Total Backend & UI |
| :--- | :--- | :--- |
| **Deskripsi** | Memperbarui seluruh label, placeholder, opsi select, badge, modal, dan tabel header di Blade views Admin & Super Admin. | Mengubah nama kolom database, rute, nama controller method, dan skema migrasi. |
| **Kelebihan** | 1. Bebas risiko *breaking changes* pada database.<br>2. Perubahan terfokus, rapi, dan langsung tampak pada UI.<br>3. 100% kompatibel dengan data yang sudah ada. | Konsistensi penuh hingga level kode terdalam. |
| **Kekurangan** | Nama variabel internal PHP (misal `$periodeCount`) tetap ada di beberapa controller, namun tidak terlihat oleh user. | Sangat berisiko (*high risk*), memicu refactor besar pada migrasi dan puluhan file backend. |
| **Keputusan** | **DIPILIH (Opsi 1)**. | Ditolak karena melanggar batasan larangan ubah backend/route. |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/dashboard.blade.php` | `Modify` | Ubah `Periode: [Bulan Y]` &rarr; `Bulan: [Bulan Y]`, `Pilih Periode:` &rarr; `Pilih Bulan:`, `Periode Magang` &rarr; `Bulan Magang`, `Realisasi kursi magang periode ...` &rarr; `... bulan ...`, `Jadwal Pendaftaran Periode Ini` &rarr; `... Bulan Ini`, dan empty state kuota. |
| `resources/views/admin/kuota/index.blade.php` | `Modify` | Ubah `Semua Periode` &rarr; `Semua Bulan`, `Periode Magang:` &rarr; `Bulan Magang:`, `Kuota periode ini penuh` &rarr; `... bulan ini ...`, `alokasi kuota ... periode ini` &rarr; `... bulan ini`. |
| `resources/views/admin/pendaftaran/index.blade.php` | `Modify` | Ubah filter label `Periode Magang` &rarr; `Bulan Magang`, opsi `Semua Periode` &rarr; `Semua Bulan`, dan tabel header `Periode Magang` &rarr; `Bulan Magang`. |
| `resources/views/admin/pendaftaran/show.blade.php` | `Modify` | Ubah section `Rangkuman Biodata & Periode Magang` &rarr; `Rangkuman Biodata & Bulan Magang`, dan label `Periode Magang & Kuota` &rarr; `Bulan Magang & Kuota`. |
| `resources/views/admin/waiting-list/index.blade.php` | `Modify` | Ubah KPI `Periode Magang Terdampak` &rarr; `Bulan Magang Terdampak`, filter `Periode Magang` &rarr; `Bulan Magang`, `Semua Periode Bulan` &rarr; `Semua Bulan Magang`, alert penutupan, dialog konfirmasi, dan tabel header. |
| `resources/views/admin/penempatan/index.blade.php` | `Modify` | Ubah tabel header `Periode Magang` &rarr; `Bulan Magang` dan teks modal `Periode:` &rarr; `Bulan:`. |
| `resources/views/admin/sertifikat/index.blade.php` | `Modify` | Ubah tabel header `Periode Magang` &rarr; `Bulan Magang`. |
| `resources/views/admin/laporan/index.blade.php` | `Modify` | Ubah label filter `Periode Bulan Magang` &rarr; `Bulan Magang`, `Semua Periode Bulan` &rarr; `Semua Bulan Magang`, header `Periode Mulai` &rarr; `Bulan Mulai`, `Periode Bulan` &rarr; `Bulan Magang`. |
| `resources/views/admin/laporan/tahunan.blade.php` | `Modify` | Ubah subtitle `Menampilkan seluruh data periode magang tahun ...` &rarr; `... data bulan magang tahun ...`. |
| `resources/views/superadmin/dashboard.blade.php` | `Modify` | Ubah tombol `+ Periode Magang` &rarr; `+ Bulan Magang`, label selector `Periode:` &rarr; `Bulan:`, header card `Periode: [Bulan]` &rarr; `Bulan: [Bulan]`, dan tabel header `Jurusan & Periode` &rarr; `Jurusan & Bulan`. |
| `resources/views/superadmin/bulan/index.blade.php` | `Modify` | Ubah page title `Periode Bulan Magang` &rarr; `Bulan Magang`, tombol `Tambah Periode Bulan` &rarr; `Tambah Bulan Magang`, header tabel `Periode Buka Pendaftaran` &rarr; `Jadwal Buka Pendaftaran`, modal title, dan tombol submit. |
| `resources/views/superadmin/slot-khusus/index.blade.php` | `Modify` | Ubah header tabel `Bulan Periode` &rarr; `Bulan Magang`. |
| `tests/Feature/AdminTerminologyDesignSystemTest.php` | `Create` | Feature test otomatis untuk memvalidasi seluruh label baru dan memastikan ketiadaan teks lama pada dashboard administrasi. |

---

## 5. Rencana Pemetaan Kata (Terminology Mapping)

| Konteks / Teks Lama | Teks Baru yang Distandardisasi | Lokasi View |
| :--- | :--- | :--- |
| `Periode: November 2026` | `Bulan: November 2026` | Hero chip `admin.dashboard`, `superadmin.dashboard` |
| `Pilih Periode:` | `Pilih Bulan:` | Control sub-bar `admin.dashboard`, filter `superadmin.dashboard` |
| `Periode Magang` | `Bulan Magang` | Header tabel di pendaftaran, penempatan, waiting-list, kuota, sertifikat |
| `Semua Periode` / `Semua Periode Bulan` | `Semua Bulan` / `Semua Bulan Magang` | Dropdown filter di pendaftaran, waiting list, laporan, kuota |
| `Jadwal Pendaftaran Periode Ini` | `Jadwal Pendaftaran Bulan Ini` | Countdown card `admin.dashboard` |
| `Realisasi kursi magang periode ...` | `Realisasi kursi magang bulan ...` | Card realisasi `admin.dashboard` |
| `+ Periode Magang` | `+ Bulan Magang` | Tombol aksi cepat `superadmin.dashboard` |
| `Tambah Periode Bulan` | `Tambah Bulan Magang` | Tombol modal `superadmin.bulan.index` |
| `Daftar Periode Bulan Magang` | `Daftar Bulan Magang` | Judul card `superadmin.bulan.index` |
| `Total: X Periode` | `Total: X Bulan Magang` | Counter badge `superadmin.bulan.index` |

---

## 6. Langkah Implementasi Terstruktur

- [x] **Fase 1: Implementasi Dashboard Admin (`resources/views/admin/dashboard.blade.php`)**
  - [x] Ganti badge hero `Periode:` menjadi `Bulan:`.
  - [x] Ganti label form control `Pilih Periode:` menjadi `Pilih Bulan:`.
  - [x] Ganti header tabel kuota `Periode Magang` menjadi `Bulan Magang`.
  - [x] Ganti deskripsi realisasi dan jadwal countdown `Periode Ini` menjadi `Bulan Ini`.
- [x] **Fase 2: Implementasi Modul Operasional Admin**
  - [x] Perbarui `admin/kuota/index.blade.php` (label metadata, empty state, modal edit kuota).
  - [x] Perbarui `admin/pendaftaran/index.blade.php` & `show.blade.php` (filter, table header, section summary).
  - [x] Perbarui `admin/waiting-list/index.blade.php` (KPI card, filter, confirm alert, table header).
  - [x] Perbarui `admin/penempatan/index.blade.php` (table header, modal detail slot).
  - [x] Perbarui `admin/sertifikat/index.blade.php` & `admin/laporan/*.blade.php` (table header, filter, teks laporan).
- [x] **Fase 3: Implementasi Portal Super Admin**
  - [x] Perbarui `superadmin/dashboard.blade.php` (tombol tambah, label selektor, badge status, table header).
  - [x] Perbarui `superadmin/bulan/index.blade.php` (title, tombol, table header, form modal tambah/edit).
  - [x] Perbarui `superadmin/slot-khusus/index.blade.php` (table header).
- [x] **Fase 4: Pengujian & Validasi Otomatis**
  - [x] Buat file pengujian `tests/Feature/AdminTerminologyDesignSystemTest.php`.
  - [x] Jalankan `php artisan test --filter=AdminTerminologyDesignSystemTest` (100% lulus, 4 test, 34 assertions).
  - [x] Jalankan `php artisan test --filter=BrandingDesignSystemTest` untuk menjamin tidak ada regresi (100% lulus, 5 test, 26 assertions).
  - [x] Lakukan inspeksi visual di peramban untuk memastikan tata letak dan estetika tetap presisi.

---

## 7. Strategi Testing

1. **Unit & Feature Test Baru**:
   - `test_admin_dashboard_displays_bulan_terminology`: Memastikan `Bulan:`, `Pilih Bulan:`, dan `Bulan Magang` tampil pada dashboard admin, serta memastikan `Pilih Periode:` dan `Periode Magang` tidak muncul.
   - `test_admin_kuota_and_pendaftaran_display_bulan_terminology`: Memastikan filter dropdown dan tabel di modul pendaftaran dan kuota menggunakan `Bulan Magang` dan `Semua Bulan`.
   - `test_superadmin_dashboard_displays_bulan_terminology`: Memastikan tombol `+ Bulan Magang` dan label `Bulan:` tampil dengan benar.
2. **Regression Testing**:
   - Menjalankan `php artisan test` untuk modul branding dan otentikasi guna memastikan seluruh fungsionalitas tetap 100% hijau.

---

## 8. Risiko & Mitigasi
- **Risiko**: Ada assertion test lama yang memeriksa kata spesifik `Periode` (misal di test superadmin lama).
  - **Mitigasi**: Telusuri test terkait dan sesuaikan assertion bila ada yang secara spesifik menguji label UI yang diperbarui, tanpa mengubah logika pengujian.
- **Risiko**: Inkonsistensi teks pada dialog JavaScript `confirm(...)`.
  - **Mitigasi**: Pastikan dialog konfirmasi (seperti penutupan massal waiting list) juga diperbarui dari `periode` menjadi `bulan`.

---

## 9. Kriteria Selesai (Definition of Done)
- [x] Seluruh tampilan Dashboard Admin (`admin/dashboard.blade.php`) menggunakan istilah `Bulan` dan `Pilih Bulan`.
- [x] Seluruh modul administrasi operasional admin (Kuota, Pendaftaran, Waiting List, Penempatan, Laporan, Sertifikat) menggunakan istilah `Bulan Magang`.
- [x] Seluruh modul Super Admin (Dashboard & Manajemen Bulan) konsisten menggunakan istilah `Bulan Magang`.
- [x] Tidak ada perubahan pada skema database, controller logic, atau routing.
- [x] Seluruh pengujian otomatis di `tests/Feature/AdminTerminologyDesignSystemTest.php` dan `BrandingDesignSystemTest.php` lulus 100%.
