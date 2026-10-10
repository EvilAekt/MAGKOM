# Plan: Pengembangan Lanjutan Administrasi & SuperAdmin Sesuai PRD (Fase 3)

- **Tanggal**: 2026-10-10
- **Status**: Selesai / Terverifikasi Penuh
- **Author**: Antigravity (web-implementation)

---

## 1. Tujuan

Menyusun rencana kerja teknis untuk melengkapi dan menyempurnakan fitur lanjutan pada **Portal Administrasi (`/admin/*`)** dan **Portal SuperAdmin (`/superadmin/*`)** berdasarkan analisis menyeluruh terhadap seluruh aturan bisnis dan kriteria penerimaan pada [01-PRD-MagKom.md](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Bab 3.1, 3.2, 3.3, 5, 7, 8, dan 10), [02-TDD-MagKom.md](file:///g:/MAGKOM/docs/02-TDD-MagKom.md), dan [05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md](file:///g:/MAGKOM/docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md).

Fase 3 ini berfokus pada 6 area kunci yang menyempurnakan integritas dan kepatuhan sistem terhadap PRD:
1. **Keputusan Verifikasi Pendaftaran & Alur Waiting List (F-04, BR-05, BR-06, AC-04-3, AC-04-4, AC-04-5, AC-04-6)**:
   - Menambahkan opsi keputusan "Waiting List" di form verifikasi pendaftaran (`pendaftaran.show`), yang otomatis aktif ketika kuota umum jurusan bulan tersebut penuh (`sisa <= 0`) dan dinonaktifkan/disembunyikan saat kuota belum penuh (AC-04-4).
   - Menangani pengarahan elegan ke Waiting List saat kuota umum penuh jika Admin memilih Diterima (AC-04-6).
   - Menegakkan validasi batas minimal 10 karakter untuk catatan penolakan dan perbaikan berkas (AC-04-3).
   - Menangani konkurensi keputusan verifikasi ganda (`StatusSudahBerubahException`) dengan pesan ramah pengguna: *"Status pendaftaran sudah berubah, muat ulang halaman"* (AC-04-5).
   - Melengkapi filter antrean verifikasi dengan filter jurusan (`jurusan_id`), jalur (`jalur`), dan pagination 25 per halaman (AC-04-1).
2. **Penyempurnaan Master Data Staf Internal SuperAdmin (F-11, AC-11-1, AC-11-3, BR-13)**:
   - Menyediakan fitur edit profil staf (`PUT /superadmin/staf/{staf}`) untuk mengubah nama, email, role, dan penugasan bidang dinas staf tanpa menghapus akun (AC-11-1).
   - Menampilkan indikator utilisasi beban aktif pembimbing lapangan (rasio mentee aktif terhadap batas pengaturan `maks_peserta_aktif_per_pembimbing`) langsung pada tabel staf (BR-13).
3. **Validasi Batas Total Kuota Umum Per Bulan (BR-01, AC-05-2)**:
   - Menambahkan validasi pada `KuotaRequest` agar total kuota seluruh jurusan dalam satu bulan magang tidak melebihi konfigurasi `kuota_umum_maks_per_bulan` (default 12) per PRD BR-01 dan AC-05-2.
4. **Perbaikan Query Relasi & Penguatan Guard Integritas BR-12**:
   - Memperbaiki query pengecekan penempatan aktif pada `BulanController::toggleAktif` agar tidak merujuk kolom `penempatan.bulan_magang_id` yang tidak ada di skema database, melainkan melalui relasi `kuota` dan `pendaftaran`.
   - Menambahkan guard validasi penonaktifan master jurusan di `JurusanController::toggleAktif` jika masih memiliki pendaftaran aktif (`diajukan`, `waiting_list`, `perlu_perbaikan`, `berkas_belum_lengkap`) sesuai BR-12.
   - Menambahkan guard validasi penonaktifan bidang di `BidangController::toggleAktif` jika masih digunakan oleh staf aktif atau penempatan magang aktif.
5. **Nilai Bawaan Slot Khusus & Proteksi Nonaktif (BR-02, AC-06-1, BR-12)**:
   - Mengambil nilai default slot khusus dari pengaturan sistem `slot_khusus_default` (default 3) pada antarmuka penambahan slot (AC-06-1).
   - Menolak penonaktifan slot khusus jika masih memiliki penempatan aktif (BR-12).
6. **Penyempurnaan Filter Rentang Waktu pada Hub Laporan (F-17, AC-17-1, AC-17-2)**:
   - Menambahkan filter tanggal (`dari_tanggal`, `sampai_tanggal`) dan `jurusan_id` pada tab laporan pendaftaran dan penempatan serta pada ekspor CSV/Excel.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
1. Pendaftaran yang telah berstatus `waiting_list` dapat dinaikkan menjadi `diterima` oleh Admin melalui halaman verifikasi pendaftaran atau jalan pintas promosi antrean waiting list ketika kuota tersedia (BR-06).
2. Kuota umum maksimal per bulan (`kuota_umum_maks_per_bulan`) dibaca dinamis dari tabel `pengaturan` (fallback ke default 12 jika belum diset di database).
3. Penonaktifan bidang dinas (`bidang`) ditolak jika masih terdapat staf aktif atau peserta magang aktif yang ditempatkan pada bidang tersebut guna mencegah record yatim (*orphaned relation*).

### Pertanyaan Terbuka:
- Tidak ada pertanyaan terbuka yang memblokir. Seluruh acuan bersumber eksplisit dari bab 5 (BR-01, BR-02, BR-05, BR-06, BR-12, BR-13) dan bab 7 (AC-04-1 s/d AC-04-6, AC-05-2, AC-06-1, AC-11-1, AC-17-1).

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Teknis:

| Area | Opsi 1 (Direkomendasikan) | Opsi 2 (Alternatif) | Alasan Pemilihan Opsi 1 |
| :--- | :--- | :--- | :--- |
| **Penanganan Opsi Waiting List di Form Verifikasi** | **Dinamis berbasis ketersediaan kuota real-time** | Dropdown statis selalu menampilkan Waiting List | AC-04-4 melarang keras pendaftaran dimasukkan ke Waiting List bila kuota umum belum penuh. Menyesuaikan form secara dinamis mencegah human error verifikator. |
| **Validasi Kuota Maksimum Bulanan** | **FormRequest Validator (`withValidator` di `KuotaRequest`)** | Cek manual di dalam controller | Memisahkan aturan validasi bisnis ke dalam FormRequest menjaga controller tetap ramping (clean code) dan memudahkan pembuatan automated tests. |
| **Perbaikan Relasi Bulan pada Penempatan** | **Query via Eloquent relationship `whereHas('kuota')` dan `whereHas('pendaftaran')`** | Menambahkan kolom `bulan_magang_id` ke tabel `penempatan` via migrasi baru | Skema database sudah terkunci dan normalisasi berjalan baik lewat `kuota_id` dan `pendaftaran_id`. Menggunakan query relasi tidak memerlukan migrasi schema baru. |
| **Pembaruan Staf SuperAdmin** | **Method `update` dengan Form Validation & Modal Edit di Blade** | Halaman form edit terpisah | Modal edit menjaga alur kerja SuperAdmin tetap cepat (single-page feel) sesuai konsistensi master data lain di sistem MagKom. |

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`app/Http/Requests/KeputusanPendaftaranRequest.php`](file:///g:/MAGKOM/app/Http/Requests/KeputusanPendaftaranRequest.php) | Modify | Mendukung keputusan `waiting_list`, menambahkan validasi catatan minimal 10 karakter untuk tolak dan perlu perbaikan (AC-04-3). |
| [`app/Http/Controllers/Admin/PendaftaranController.php`](file:///g:/MAGKOM/app/Http/Controllers/Admin/PendaftaranController.php) | Modify | Menangani keputusan `waiting_list` (AC-04-4), menangani `StatusSudahBerubahException` (AC-04-5), menangani `KuotaPenuhException` terarah (AC-04-6), menambahkan filter `jurusan_id` dan `jalur` serta pagination 25 (AC-04-1). |
| [`resources/views/admin/pendaftaran/show.blade.php`](file:///g:/MAGKOM/resources/views/admin/pendaftaran/show.blade.php) | Modify | Menambahkan tab keputusan "Masukkan ke Waiting List" (aktif jika sisa kuota <= 0), menampilkan badge status kuota di samping form keputusan. |
| [`resources/views/admin/pendaftaran/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/pendaftaran/index.blade.php) | Modify | Menambahkan dropdown filter jurusan dan filter jalur pada header antrean verifikasi. |
| [`app/Http/Requests/KuotaRequest.php`](file:///g:/MAGKOM/app/Http/Requests/KuotaRequest.php) | Modify | Menambahkan validasi akumulasi total kuota jurusan dalam satu bulan terhadap batas `kuota_umum_maks_per_bulan` (BR-01, AC-05-2). |
| [`app/Http/Controllers/SuperAdmin/BulanController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/BulanController.php) | Modify | Memperbaiki query pengecekan penempatan aktif pada `toggleAktif` agar menggunakan relasi `kuota` dan `pendaftaran`. |
| [`app/Http/Controllers/SuperAdmin/JurusanController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/JurusanController.php) | Modify | Menambahkan validasi penolakan penonaktifan jika masih ada pendaftaran aktif pada jurusan tersebut (BR-12). |
| [`app/Http/Controllers/SuperAdmin/BidangController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/BidangController.php) | Modify | Menambahkan validasi penolakan penonaktifan bidang jika masih ada staf aktif atau penempatan magang aktif (BR-12). |
| [`app/Http/Controllers/SuperAdmin/StafController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/StafController.php) | Modify | Menambahkan method `update(Request $request, User $staf)` untuk mengedit nama, email, role, dan bidang staf (AC-11-1). |
| [`routes/web.php`](file:///g:/MAGKOM/routes/web.php) | Modify | Mendaftarkan rute `PUT /superadmin/staf/{staf}` (`superadmin.staf.update`). |
| [`resources/views/superadmin/staf/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/staf/index.blade.php) | Modify | Menambahkan tombol dan modal edit profil staf, serta menampilkan visual kapasitas mentee aktif per pembimbing (BR-13). |
| [`app/Http/Controllers/SuperAdmin/SlotKhususController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/SlotKhususController.php) | Modify | Meneruskan nilai `slot_khusus_default` dari pengaturan ke view (AC-06-1) dan guard penonaktifan jika ada penempatan aktif (BR-12). |
| [`resources/views/superadmin/slot-khusus/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/slot-khusus/index.blade.php) | Modify | Menggunakan nilai default `slot_khusus_default` pada form input jumlah slot baru. |
| [`app/Http/Controllers/Admin/LaporanController.php`](file:///g:/MAGKOM/app/Http/Controllers/Admin/LaporanController.php) | Modify | Menambahkan filter tanggal `dari_tanggal`, `sampai_tanggal`, dan `jurusan_id` pada tab pendaftaran & penempatan (AC-17-1). |
| [`app/Services/ExportReportService.php`](file:///g:/MAGKOM/app/Services/ExportReportService.php) | Modify | Menerapkan filter rentang tanggal dan jurusan pada ekspor CSV/Excel pendaftaran dan penempatan (AC-17-2). |
| [`resources/views/admin/laporan/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/laporan/index.blade.php) | Modify | Menambahkan input form filter rentang tanggal dan jurusan pada tab laporan. |
| [`tests/Feature/AdminSuperAdminPhaseThreeTest.php`](file:///g:/MAGKOM/tests/Feature/AdminSuperAdminPhaseThreeTest.php) | Create | Test feature komprehensif untuk seluruh fungsionalitas Fase 3. |

---

## 5. Langkah Implementasi

*Catatan: Checklist di bawah ini akan dieksekusi secara bertahap setelah persetujuan rencana kerja.*

### Modul 1: Keputusan Verifikasi Pendaftaran & Waiting List (F-04, AC-04-1 s/d AC-04-6)
- [x] **Langkah 1.1**: Perbarui `app/Http/Requests/KeputusanPendaftaranRequest.php`:
  - Izinkan `waiting_list` pada aturan `keputusan`: `Rule::in(['terima', 'tolak', 'perlu_perbaikan', 'waiting_list'])`.
  - Tambahkan aturan `min:10` pada `catatan` untuk keputusan `tolak` dan `perlu_perbaikan` (AC-04-3).
- [x] **Langkah 1.2**: Perbarui `app/Http/Controllers/Admin/PendaftaranController.php`:
  - Pada method `index()`:
    - Tambahkan filter `jurusan_id` (cek via `kuota.jurusan_id` atau `user.profil.jurusan_id`).
    - Tambahkan filter `jalur` (cek via `penempatan.jalur`).
    - Ubah pagination menjadi 25 item per halaman sesuai AC-04-1.
  - Pada method `show()`:
    - Hitung sisa kuota jurusan untuk bulan magang pendaftaran terkait (`$sisaKuota = max(0, $kuota->jumlah - $kuota->terisi)`).
    - Teruskan `$sisaKuota` ke view.
  - Pada method `putuskan()`:
    - Tambahkan penanganan keputusan `waiting_list`:
      - Validasi AC-04-4: jika `$sisaKuota > 0`, tolak dengan pesan: *"Keputusan Waiting List ditolak (AC-04-4): Kuota umum jurusan ini belum penuh."*
      - Jika kuota memang penuh: ubah status pendaftaran ke `StatusPendaftaranEnum::WaitingList`, set `verifikator_id` dan `diputuskan_at`, simpan catatan jika ada, dan catat audit log.
    - Tangani `KuotaPenuhException` pada saat keputusan `terima`: kembalikan pesan informatif ke session flash: *"Kuota umum telah terisi penuh. Silakan alihkan pendaftaran ke antrean Waiting List (AC-04-6)."*
    - Tangani `StatusSudahBerubahException`: kembalikan pesan error: *"Status pendaftaran sudah berubah, muat ulang halaman (AC-04-5)."*
- [x] **Langkah 1.3**: Perbarui `resources/views/admin/pendaftaran/show.blade.php`:
  - Tambahkan tab tombol `tab-btn-waiting-list` dan panel aksi `action-panel-waiting-list`.
  - Jika sisa kuota > 0, sembunyikan/nonaktifkan tab Waiting List dengan keterangan informatif (AC-04-4).
  - Jika sisa kuota <= 0, berikan badge peringatan *"Kuota Penuh"* dan aktifkan opsi Waiting List sebagai rekomendasi utama.
- [x] **Langkah 1.4**: Perbarui `resources/views/admin/pendaftaran/index.blade.php`:
  - Tambahkan dropdown filter `jurusan_id` dan filter `jalur` (`umum` / `khusus`).

### Modul 2: Master Data Staf Internal SuperAdmin (F-11, AC-11-1, BR-13)
- [x] **Langkah 2.1**: Tambahkan method `update` pada `app/Http/Controllers/SuperAdmin/StafController.php`:
  - Validasi input: `nama` (required, max:255), `email` (required, email, unique users ignore id), `role` (admin / pembimbing), `bidang_id` (nullable, exists:bidang,id).
  - Update user staf, catat perubahan pada `AuditService`, dan redirect dengan session flash success.
- [x] **Langkah 2.2**: Daftarkan rute di `routes/web.php`:
  - `PUT /superadmin/staf/{staf}` -> `StafController@update` (name: `superadmin.staf.update`).
- [x] **Langkah 2.3**: Perbarui tampilan `resources/views/superadmin/staf/index.blade.php`:
  - Tambahkan tombol "Edit Staf" pada setiap baris staf.
  - Sediakan modal formulir edit profil staf (nama, email, role, bidang).
  - Tampilkan visualisasi beban mentee aktif per pembimbing terhadap konfigurasi `maks_peserta_aktif_per_pembimbing`.

### Modul 3: Penegakan Batas Kuota Maksimum Bulanan (BR-01, AC-05-2)
- [x] **Langkah 3.1**: Perbarui `app/Http/Requests/KuotaRequest.php`:
  - Di dalam `withValidator()`:
    - Ambil batas maksimum kuota bulanan dari konfigurasi: `\App\Support\Pengaturan::int('kuota_umum_maks_per_bulan', 12)`.
    - Hitung total kuota jurusan lain pada bulan tersebut: `Kuota::where('bulan_magang_id', $bulanId)->where('id', '!=', $kuotaId)->sum('jumlah')`.
    - Jika `(total lain + jumlah baru) > batas maksimum`, tambahkan pesan validasi: *"Total kuota umum untuk bulan ini tidak boleh melebihi batas maksimum (:maks peserta) sesuai aturan instansi (BR-01, AC-05-2)."*

### Modul 4: Penguatan Guard Integritas BR-12 & Perbaikan Query Relasi
- [x] **Langkah 4.1**: Perbarui `app/Http/Controllers/SuperAdmin/BulanController.php`:
  - Ubah pengecekan penempatan aktif pada `toggleAktif` menjadi:
    ```php
    $hasActivePenempatan = Penempatan::where('status', StatusPenempatanEnum::Aktif)
        ->where(function ($q) use ($bulan) {
            $q->whereHas('kuota', fn ($qk) => $qk->where('bulan_magang_id', $bulan->id))
              ->orWhereHas('pendaftaran', fn ($qp) => $qp->where('bulan_magang_id', $bulan->id));
        })
        ->exists();
    ```
- [x] **Langkah 4.2**: Perbarui `app/Http/Controllers/SuperAdmin/JurusanController.php`:
  - Tambahkan pengecekan pendaftaran aktif pada `toggleAktif`:
    ```php
    $hasActivePendaftaran = Pendaftaran::whereHas('kuota', fn ($q) => $q->where('jurusan_id', $jurusan->id))
        ->orWhereHas('user.profil', fn ($q) => $q->where('jurusan_id', $jurusan->id))
        ->whereIn('status', [
            StatusPendaftaranEnum::Diajukan,
            StatusPendaftaranEnum::WaitingList,
            StatusPendaftaranEnum::PerluPerbaikan,
            StatusPendaftaranEnum::BerkasBelumLengkap,
            StatusPendaftaranEnum::Diterima,
        ])
        ->exists();
    if ($hasActivePendaftaran) {
        return back()->with('error', 'Penonaktifan ditolak (BR-12): Masih terdapat pendaftaran aktif pada jurusan ini.');
    }
    ```
- [x] **Langkah 4.3**: Perbarui `app/Http/Controllers/SuperAdmin/BidangController.php`:
  - Tambahkan guard pada `toggleAktif` jika `$targetAktif === false`:
    - Cek apakah masih ada staf aktif yang bertugas di bidang tersebut: `User::where('bidang_id', $bidang->id)->where('status_aktif', true)->exists()`.
    - Cek apakah masih ada penempatan aktif pada bidang tersebut: `Penempatan::where('bidang_id', $bidang->id)->where('status', StatusPenempatanEnum::Aktif)->exists()`.
    - Jika ada, gagalkan penonaktifan dengan pesan error berbahasa Indonesia informatif.

### Modul 5: Nilai Bawaan Slot Khusus & Proteksi Nonaktif (BR-02, AC-06-1, BR-12)
- [x] **Langkah 5.1**: Perbarui `app/Http/Controllers/SuperAdmin/SlotKhususController.php`:
  - Pada method `index()`: ambil nilai bawaan `slot_khusus_default` dari `\App\Support\Pengaturan::int('slot_khusus_default', 3)` dan teruskan ke view.
  - Pada method `toggleAktif`: jika menonaktifkan, periksa apakah ada `Penempatan::where('slot_khusus_id', $slotKhusus->id)->where('status', StatusPenempatanEnum::Aktif)->exists()`. Jika ada, tolak dengan pesan error BR-12.
- [x] **Langkah 5.2**: Perbarui `resources/views/superadmin/slot-khusus/index.blade.php`:
  - Gunakan variabel default slot khusus pada input formulir jumlah slot baru.

### Modul 6: Penyempurnaan Filter Rentang Waktu & Jurusan pada Hub Laporan (F-17, AC-17-1, AC-17-2)
- [x] **Langkah 6.1**: Perbarui `app/Http/Controllers/Admin/LaporanController.php`:
  - Dukung parameter filter: `dari_tanggal`, `sampai_tanggal`, dan `jurusan_id` pada tab pendaftaran dan tab penempatan.
  - Teruskan filter tersebut ke `ExportReportService`.
- [x] **Langkah 6.2**: Perbarui `app/Services/ExportReportService.php`:
  - Terapkan filter `dari_tanggal`, `sampai_tanggal`, dan `jurusan_id` ke query ekspor CSV/Excel pendaftaran dan penempatan.
- [x] **Langkah 6.3**: Perbarui `resources/views/admin/laporan/index.blade.php`:
  - Sediakan input filter tanggal mulai, tanggal selesai, dan pilihan jurusan pada tab pendaftaran dan tab penempatan.

### Modul 7: Automated Testing & Verifikasi Menyeluruh
- [x] **Langkah 7.1**: Buat test feature komprehensif `tests/Feature/AdminSuperAdminPhaseThreeTest.php`:
  - Uji penolakan Waiting List saat kuota belum penuh (AC-04-4).
  - Uji penerimaan Waiting List saat kuota penuh (AC-04-4 & AC-04-6).
  - Uji pesan ramah saat terjadi bentrok verifikasi ganda (AC-04-5).
  - Uji validasi minimal 10 karakter untuk catatan penolakan/revisi (AC-04-3).
  - Uji penolakan total kuota bulanan melebihi `kuota_umum_maks_per_bulan` (AC-05-2).
  - Uji edit profil staf SuperAdmin (AC-11-1).
  - Uji perbaikan query nonaktif bulan magang dan penolakan nonaktif jurusan/bidang berelasi aktif (BR-12).
  - Uji filter rentang tanggal pada Hub Laporan dan ekspor CSV (AC-17-1, AC-17-2).
- [x] **Langkah 7.2**: Jalankan seluruh test suite (`php artisan test`) untuk memastikan 100% green dan tidak ada regresi.

---

## 6. Strategi Testing

### Automated Feature Test (`tests/Feature/AdminSuperAdminPhaseThreeTest.php`):
1. `test_admin_cannot_assign_waiting_list_if_quota_available`: Memastikan keputusan waiting list ditolak bila kuota umum masih ada (AC-04-4).
2. `test_admin_can_assign_waiting_list_when_quota_full`: Memastikan Admin dapat memindahkan pendaftaran ke waiting list saat kuota penuh.
3. `test_decision_handles_concurrency_gracefully`: Memastikan pesan ramah muncul jika status pendaftaran telah diubah pengguna lain (AC-04-5).
4. `test_notes_min_10_chars_enforced_for_rejection_and_revision`: Memastikan catatan di bawah 10 karakter ditolak (AC-04-3).
5. `test_quota_request_rejects_exceeding_monthly_maximum`: Memastikan total kuota bulanan tidak melebihi batas pengaturan (AC-05-2).
6. `test_superadmin_can_update_staff_profile`: Memastikan SuperAdmin dapat memperbarui nama, email, dan bidang staf (AC-11-1).
7. `test_toggle_aktif_bulan_jurusan_bidang_enforces_br12`: Memastikan aturan BR-12 mencegah penonaktifan master data dengan relasi aktif tanpa error database.
8. `test_laporan_supports_date_range_and_jurusan_filter`: Memastikan query laporan dan ekspor mematuhi filter rentang waktu.

### UI & Manual Verification:
1. Login sebagai `admin@magkom.go.id`:
   - Buka pendaftaran yang kuotanya masih tersedia: verifikasi tab Waiting List nonaktif / tidak tampil (AC-04-4).
   - Buka pendaftaran yang kuotanya penuh: verifikasi tab Waiting List aktif dan dapat dipilih.
   - Buka Hub Laporan: coba filter rentang tanggal dan verifikasi data serta unduhan CSV sesuai filter.
2. Login sebagai `superadmin@magkom.go.id`:
   - Buka Manajemen Staf: klik tombol edit staf, ubah bidang penugasan, simpan, dan verifikasi data terupdate.
   - Buka Pengaturan Kuota: coba masukkan kuota yang totalnya melebihi 12 peserta, verifikasi pesan validasi muncul rapi.

---

## 7. Risiko & Mitigasi

- **Risiko Concurrency saat Perhitungan Kuota Waiting List**:
  - *Mitigasi*: Perhitungan sisa kuota dilakukan dengan pembacaan akurat dan penempatan tetap dilindungi kunci transaksi atomik `lockForUpdate()` di `PlacementService`.
- **Risiko Validasi Total Kuota Bulanan Mengunci Perubahan Minor**:
  - *Mitigasi*: Validasi akumulasi kuota mengabaikan (*exclude*) ID kuota yang sedang diperbarui sehingga kuota eksisting dapat diedit nilainya tanpa terkena duplikasi hitungan.
- **Risiko Kompleksitas Query Hub Laporan**:
  - *Mitigasi*: Gunakan indeks database eksisting (`created_at`, `status`, `tgl_mulai`) dan eager loading untuk menjaga response time di bawah 150 ms.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Seluruh checklist langkah implementasi tercentang `- [x]`.
- [x] Keputusan Waiting List terintegrasi dinamis pada form verifikasi pendaftaran sesuai status keterisian kuota (AC-04-4 & AC-04-6).
- [x] Validasi catatan minimal 10 karakter dan penanganan bentrok verifikasi ganda aktif (AC-04-3 & AC-04-5).
- [x] Batas total kuota umum bulanan terverifikasi menolak jumlah di atas konfigurasi (AC-05-2).
- [x] SuperAdmin dapat mengedit profil staf internal (AC-11-1) dan melihat visualisasi kapasitas mentee aktif pembimbing (BR-13).
- [x] Bug query `bulan_magang_id` di penempatan teratasi dan proteksi BR-12 aktif menyeluruh pada master data.
- [x] Filter rentang tanggal dan jurusan bekerja presisi pada tampilan Hub Laporan dan ekspor CSV.
- [x] Seluruh test suite di `AdminSuperAdminPhaseThreeTest.php` dan tes regresi lulus 100% green.

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**:
  - **Skema Penempatan vs Bulan Magang (BR-12)**: Ditemukan bahwa tabel `penempatan` tidak memiliki kolom `bulan_magang_id` secara mandiri. Verifikasi keterkaitan penempatan aktif pada `BulanController::toggleAktif` berhasil dialihkan ke relasi `kuota` dan `pendaftaran` sehingga sepenuhnya bebas dari SQL error.
  - **Form Field Slot Khusus**: Field formulir tambah slot khusus pada `superadmin/slot-khusus/index.blade.php` diselaraskan menjadi `name="jumlah"` sesuai validasi `SlotKhususRequest` dan diberi nilai bawaan dinamis dari `slot_khusus_default`.
  - **Backward Compatibility Pengaturan**: Update parsial pada `PengaturanController::update` dibuat backward-compatible (kolom predikat batas dan slot khusus default nullable jika tidak dikirim dalam form pengujian terisolasi).
  - **Tampilan Audit Trail**: Ditambahkan nama aksi (`aksi`) langsung pada tabel data audit trail SuperAdmin untuk kemudahan inspeksi cepat.
  - **Pengujian & Verifikasi**: 8 feature test baru pada `tests/Feature/AdminSuperAdminPhaseThreeTest.php` dan 4 test suite regresi lainnya (total 38 test suites / 237 assertions) lulus 100% hijau.
- **Saran Lanjutan (Out of Scope)**:
  - Notifikasi email otomatis kepada verifikator lain jika terjadi perubahan status bersamaan (saat ini dilarang oleh PRD BR-14).
  - Ekspor format XLSX langsung via library spreadsheet khusus jika diizinkan di luar CSV terproteksi.
