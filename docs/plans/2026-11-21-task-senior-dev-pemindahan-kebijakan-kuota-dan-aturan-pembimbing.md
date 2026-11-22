# TASK & IMPLEMENTATION PLAN: PEMINDAHAN MODUL KEBIJAKAN KUOTA KE ADMINISTRASI & ATURAN PELAKSANAAN KE PEMBIMBING DENGAN ELIMINASI SUPERADMIN ACCESS

> **Dari:** Lead Software Architect / Senior Developer  
> **Kepada:** Junior Developer / AI Implementation Engineer (Gemini 3.8 Flash Compatible)  
> **Target Repositori:** `MAGKOM` (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Status:** READY FOR IMPLEMENTATION / MENUNGGU PERSETUJUAN PENGGUNA  
> **Tingkat Prioritas:** High (Domain Responsibility Segregation, UI/UX Restructuring, RBAC Hardening)  
> **Dokumen Terkait:** `docs/01-PRD-MagKom.md`, `docs/security/rbac-permission-matrix.md`, `resources/views/superadmin/pengaturan/index.blade.php`  
> **Tanggal Dokumen:** 21 November 2026  

---

## 1. 🎯 RINGKASAN EKSEKUTIF & LATAR BELAKANG

### 1.1 Masalah Saat Ini (Current State)
Saat ini pada halaman Pengaturan Sistem SuperAdmin (`/superadmin/pengaturan`):
1. **Kartu 1 ("Kebijakan Kuota, Pendaftaran & Kapasitas Magang")** masih dikelola oleh SuperAdmin. Padahal secara tugas pokok dan fungsi (tupoksi), kuota bulanan, alokasi slot khusus, dan kapasitas mentee adalah tanggung jawab mutlak **Staf Administrasi (`admin`)**.
2. **Kartu 2 ("Aturan Pelaksanaan, Logbook & Dokumen")** masih berada di SuperAdmin. Padahal aturan terkait upload dokumen tugas dan tenggat waktu bimbingan adalah wewenang teknis **Pembimbing Lapangan (`pembimbing`)**.
3. Pada Kartu 2, terdapat parameter input `Batas Mundur Pengisian Logbook (Hari)` (`logbook_maks_mundur_hari`) yang **tidak lagi diperlukan dan harus dihapus/dihilangkan dari antarmuka**.
4. SuperAdmin memegang terlalu banyak kendali operasional mikro (*micromanagement*) yang melanggar prinsip *Separation of Concerns* dan *Least Privilege*.

### 1.2 Target Akhir yang Diinginkan (Target State)
1. **Pindahkan Kartu 1 ke Tampilan Administrasi (`admin`)**:
   - Seluruh antarmuka dan *backend business logic* (controller, form request, update action, audit log) menjadi milik **Administrasi**.
   - Dihapus 100% dari tampilan dan controller SuperAdmin.
2. **Pindahkan Kartu 2 ke Tampilan Pembimbing (`pembimbing`)**:
   - Seluruh antarmuka dan *backend business logic* menjadi milik **Pembimbing**.
   - Input `Batas Mundur Pengisian Logbook (Hari)` **dihapus permanen dari tampilan form**.
   - Dihapus 100% dari tampilan dan controller SuperAdmin.
3. **Isolasi Role Keras (Strict Role Access Control)**:
   - Hak akses role diubah secara total: rute pembaruan hanya bisa diakses oleh role yang bersangkutan (`role:admin` untuk Kebijakan Kuota; `role:pembimbing` untuk Aturan Pelaksanaan).
   - SuperAdmin **tidak lagi memiliki akses edit** terhadap kedua kelompok parameter ini.
4. **Halaman SuperAdmin Dirampingkan**:
   - Halaman `/superadmin/pengaturan` hanya mengelola parameter tata kelola sistem murni:
     - Kartu 3: Kebijakan Retensi Akun & Audit Trail (`masa_tenggang_bulan`, `retensi_audit_akses_data_hari`, Purge Audit).
     - Kartu 4: Konten Publik Landing Page (Hero Banner & Panduan PDF).

---

## 2. 📊 PERBANDINGAN PERUBAHAN MATRIKS & DATA (BEFORE VS AFTER)

| Komponen / Parameter | Lokasi Lama (Current) | Lokasi Baru (Target) | Role Pengelola | Status Input Form |
| :--- | :--- | :--- | :--- | :--- |
| **Batas Kuota Umum Publik Per Bulan** (`kuota_umum_maks_per_bulan`) | SuperAdmin | **Administrasi** | `role:admin` | Aktif (Wajib, 1-100) |
| **Default Alokasi Slot Khusus** (`slot_khusus_default`) | SuperAdmin | **Administrasi** | `role:admin` | Aktif (Wajib, 0-50) |
| **Beban Maksimal Mentee Per Pembimbing** (`maks_peserta_aktif_per_pembimbing`) | SuperAdmin | **Administrasi** | `role:admin` | Aktif (Wajib, 1-50) |
| **Batas Maksimal Anggota Kelompok** (`maks_anggota_kelompok`) | SuperAdmin | **Administrasi** | `role:admin` | Aktif (Wajib, 1-10) |
| **Durasi Magang Maksimal yang Diizinkan** (`durasi_magang_maks_bulan`) | SuperAdmin | **Administrasi** | `role:admin` | Aktif (Wajib, 1-12) |
| **Batas Mundur Pengisian Logbook** (`logbook_maks_mundur_hari`) | SuperAdmin | ❌ **DIHAPUS DARI UI** | None | ❌ **Dihilangkan dari antarmuka** |
| **Batas Ukuran Maksimal Berkas Upload** (`berkas_maks_mb`) | SuperAdmin | **Pembimbing** | `role:pembimbing` | Aktif (Wajib, 1-20 MB) |
| **Peringatan Mendekati Deadline Tugas** (`deadline_mendekati_hari`) | SuperAdmin | **Pembimbing** | `role:pembimbing` | Aktif (Wajib, 1-14 Hari) |
| **Target Batas Waktu SLA Verifikasi Berkas** (`sla_verifikasi_hari`) | SuperAdmin | **Pembimbing** | `role:pembimbing` | Aktif (Wajib, 1-30 Hari) |
| **Kebijakan Retensi Akun & Audit Trail** | SuperAdmin | Tetap di SuperAdmin | `role:superadmin` | Aktif |
| **Konten Publik Landing Page** | SuperAdmin | Tetap di SuperAdmin | `role:superadmin` | Aktif |

---

## 3. 🛡️ BENTENG KEAMANAN & INTEGRITAS DATA (SECURITY GUARDRAILS)

1. **Role Middleware Guard**:
   - Rute pembaruan kebijakan Administrasi dilindungi `role:admin`. Jika SuperAdmin atau Peserta mencoba mengirim request `PUT /admin/kebijakan`, sistem mengembalikan `403 Forbidden`.
   - Rute pembaruan aturan Pembimbing dilindungi `role:pembimbing`. Jika role lain mencoba mengakses, sistem mengembalikan `403 Forbidden`.
2. **Database Fallback Safety untuk `logbook_maks_mundur_hari`**:
   - Menghapus input dari form **tidak boleh merusak database** atau alur submit logbook peserta.
   - Nilai eksisting di tabel `pengaturan` (atau default `3`) tetap dibiarkan ada di database dan dibaca secara transparan oleh model peserta, namun tidak ada pihak yang dapat mengubahnya via form UI lagi.
3. **Atomic Transaction & Cache Invalidation**:
   - Setiap pembaruan parameter wajib dibungkus dalam `DB::transaction()`.
   - Setelah transaksi berhasil disimpan, wajib memanggil `Pengaturan::clearCache()` agar nilai terbaru langsung terbaca oleh seluruh sistem.
4. **Audit Logging Preservasi**:
   - Setiap perubahan nilai parameter wajib dicatat ke dalam tabel `audit_logs` melalui `AuditService::catat()` dengan merekam `nilaiLama`, `nilaiBaru`, dan ID admin/pembimbing yang mengeksekusi.

---

## 4. 📁 SPESIFIKASI BERKAS & ARSITEKTUR KODE

```text
MAGKOM REPOSITORY
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── KebijakanMagangController.php     <-- [BARU] Controller Kebijakan Kuota Admin
│   │   │   ├── Pembimbing/
│   │   │   │   └── AturanBimbinganController.php     <-- [BARU] Controller Aturan Pelaksanaan Pembimbing
│   │   │   └── SuperAdmin/
│   │   │       └── PengaturanController.php          <-- [MODIFIKASI] Hapus validasi & logika Kartu 1 & 2
│   │   └── Requests/
│   │       ├── UpdateKebijakanMagangRequest.php       <-- [BARU] Form Request Validasi Admin
│   │       └── UpdateAturanBimbinganRequest.php       <-- [BARU] Form Request Validasi Pembimbing
├── resources/
│   └── views/
│       ├── admin/
│       │   └── kebijakan/
│       │       └── index.blade.php                   <-- [BARU] Tampilan Kartu 1 di Administrasi
│       ├── pembimbing/
│       │   └── aturan/
│       │       └── index.blade.php                   <-- [BARU] Tampilan Kartu 2 di Pembimbing (tanpa batas mundur)
│       ├── superadmin/
│       │   └── pengaturan/
│       │       └── index.blade.php                   <-- [MODIFIKASI] Hapus markup Kartu 1 & 2 serta tile stats terkait
│       └── layouts/
│           └── dashboard.blade.php                   <-- [MODIFIKASI] Tambah menu sidebar di Admin & Pembimbing
├── routes/
│   └── web.php                                       <-- [MODIFIKASI] Registrasi route baru di Admin & Pembimbing
└── tests/
    └── Feature/
        ├── AdminKebijakanMagangTest.php              <-- [BARU] Feature test untuk modul Administrasi
        ├── PembimbingAturanBimbinganTest.php         <-- [BARU] Feature test untuk modul Pembimbing
        └── SuperAdminSettingsStrippedTest.php        <-- [BARU] Verifikasi SuperAdmin tidak bisa edit lagi
```

---

## 5. 📋 PANDUAN EKSEKUSI TAHAP DEMI TAHAP (STEP-BY-STEP RECIPE)

### TAHAP 1: IMPLEMENTASI MODUL ADMINISTRASI (KARTU 1)

#### Langkah 1.1: Buat Form Request `UpdateKebijakanMagangRequest.php`
- Buat file `app/Http/Requests/UpdateKebijakanMagangRequest.php`.
- Rules:
  ```php
  public function rules(): array
  {
      return [
          'kuota_umum_maks_per_bulan' => ['required', 'integer', 'min:1', 'max:100'],
          'slot_khusus_default' => ['required', 'integer', 'min:0', 'max:50'],
          'maks_peserta_aktif_per_pembimbing' => ['nullable', 'integer', 'min:1', 'max:50'],
          'maks_anggota_kelompok' => ['required', 'integer', 'min:1', 'max:10'],
          'durasi_magang_maks_bulan' => ['required', 'integer', 'min:1', 'max:12'],
      ];
  }
  ```

#### Langkah 1.2: Buat Controller `Admin\KebijakanMagangController.php`
- Method `index()`:
  - Mengambil parameter: `kuota_umum_maks_per_bulan`, `slot_khusus_default`, `maks_peserta_aktif_per_pembimbing`, `maks_anggota_kelompok`, `durasi_magang_maks_bulan`.
  - Mengembalikan view `admin.kebijakan.index`.
- Method `update(UpdateKebijakanMagangRequest $request)`:
  - Menyimpan data dalam `DB::transaction()`.
  - Memanggil `Pengaturan::clearCache()`.
  - Mencatat audit log: `tipe: TipeAuditEnum::Aksi`, `detail: ['aksi' => 'Memperbarui kebijakan kuota dan kapasitas magang oleh Admin']`.
  - Mengembalikan redirect dengan notifikasi sukses.

#### Langkah 1.3: Buat View `resources/views/admin/kebijakan/index.blade.php`
- Layout: `@extends('layouts.dashboard')`.
- Rancang antarmuka Kartu "Kebijakan Kuota, Pendaftaran & Kapasitas Magang" menggunakan styling Gentelella / modern SaaS yang sudah ada.
- Tidak ada inline CSS/JS (patuh aturan repositori).

#### Langkah 1.4: Daftarkan Rute di `routes/web.php`
- Di dalam grup `Route::middleware(['role:admin'])`:
  ```php
  Route::get('/kebijakan', [KebijakanMagangController::class, 'index'])->name('kebijakan.index');
  Route::put('/kebijakan', [KebijakanMagangController::class, 'update'])->name('kebijakan.update');
  ```

---

### TAHAP 2: IMPLEMENTASI MODUL PEMBIMBING (KARTU 2 TANPA BATAS MUNDUR)

#### Langkah 2.1: Buat Form Request `UpdateAturanBimbinganRequest.php`
- Buat file `app/Http/Requests/UpdateAturanBimbinganRequest.php`.
- Rules (perhatikan: **TIDAK ADA** `logbook_maks_mundur_hari`):
  ```php
  public function rules(): array
  {
      return [
          'berkas_maks_mb' => ['required', 'integer', 'min:1', 'max:20'],
          'deadline_mendekati_hari' => ['required', 'integer', 'min:1', 'max:14'],
          'sla_verifikasi_hari' => ['required', 'integer', 'min:1', 'max:30'],
      ];
  }
  ```

#### Langkah 2.2: Buat Controller `Pembimbing\AturanBimbinganController.php`
- Method `index()`:
  - Mengambil parameter: `berkas_maks_mb`, `deadline_mendekati_hari`, `sla_verifikasi_hari`.
  - Mengembalikan view `pembimbing.aturan.index`.
- Method `update(UpdateAturanBimbinganRequest $request)`:
  - Menyimpan 3 parameter tersebut dalam `DB::transaction()`.
  - Memanggil `Pengaturan::clearCache()`.
  - Mencatat audit log oleh Pembimbing.
  - Redirect kembali dengan pesan sukses.

#### Langkah 2.3: Buat View `resources/views/pembimbing/aturan/index.blade.php`
- Layout: `@extends('layouts.dashboard')`.
- Rancang Kartu "Aturan Pelaksanaan, Logbook & Dokumen" dengan 3 field:
  1. Batas Ukuran Maksimal Berkas Upload (MB)
  2. Peringatan Mendekati Deadline Tugas (Hari)
  3. Target Batas Waktu SLA Verifikasi Berkas (Hari Kerja)
- **PASTIKAN input "Batas Mundur Pengisian Logbook (Hari)" TIDAK ADA di markup Blade ini.**

#### Langkah 2.4: Daftarkan Rute di `routes/web.php`
- Di dalam grup `Route::middleware(['role:pembimbing'])`:
  ```php
  Route::get('/aturan', [AturanBimbinganController::class, 'index'])->name('aturan.index');
  Route::put('/aturan', [AturanBimbinganController::class, 'update'])->name('aturan.update');
  ```

---

### TAHAP 3: PEMBERSIHAN TOTAL DI SUPERADMIN

#### Langkah 3.1: Modifikasi `SuperAdmin\PengaturanController.php`
- Hapus seluruh field Kartu 1 dan Kartu 2 dari `$kunciOperasional` di `index()`:
  Hanya tersisa: `masa_tenggang_bulan`, `retensi_audit_akses_data_hari`.
- Pada method `update()`:
  Hapus validasi untuk:
  - `kuota_umum_maks_per_bulan`
  - `slot_khusus_default`
  - `maks_peserta_aktif_per_pembimbing`
  - `maks_anggota_kelompok`
  - `durasi_magang_maks_bulan`
  - `logbook_maks_mundur_hari`
  - `berkas_maks_mb`
  - `deadline_mendekati_hari`
  - `sla_verifikasi_hari`
- SuperAdmin `update()` hanya memvalidasi dan menyimpan:
  `masa_tenggang_bulan`, `retensi_audit_akses_data_hari`, `hero_image`, `panduan_pdf`.

#### Langkah 3.2: Modifikasi `resources/views/superadmin/pengaturan/index.blade.php`
- Hapus blok markup Kartu 1 (baris 134-188).
- Hapus blok markup Kartu 2 (baris 190-237).
- Hapus Tile Stat Kuota Umum Bulanan dan Rasio Mentee yang berada di bagian atas header dashboard SuperAdmin.
- Halaman SuperAdmin kini hanya menampilkan:
  - Tile Stat: Total Parameter Sistem & Masa Retensi Audit.
  - Kartu 3: Kebijakan Retensi Akun & Audit Trail.
  - Kartu 4: Konten Publik Landing Page (Hero Banner & Dokumen Panduan Resmi).

---

### TAHAP 4: INTEGRASI NAVIGASI SIDEBAR (`dashboard.blade.php`)

#### Langkah 4.1: Sidebar Administrasi (`userRole === 'admin'`)
- Tambahkan link menu di bawah `Administrasi Magang`:
  ```blade
  <a href="{{ route('admin.kebijakan.index') }}" class="nav-item-link {{ request()->routeIs('admin.kebijakan.*') ? 'active' : '' }}">
      <span class="nav-item-icon">
          <!-- Icon Sliders / Settings -->
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 21v-7m0-4V3m8 18v-9m0-4V3m8 18v-5m0-4V3M1 14h6m2-6h6m2 8h6"></path>
          </svg>
      </span>
      <span>{{ __('Kebijakan Kuota') }}</span>
  </a>
  ```

#### Langkah 4.2: Sidebar Pembimbing (`userRole === 'pembimbing'`)
- Tambahkan link menu di bawah `Bimbingan Magang`:
  ```blade
  <a href="{{ route('pembimbing.aturan.index') }}" class="nav-item-link {{ request()->routeIs('pembimbing.aturan.*') ? 'active' : '' }}">
      <span class="nav-item-icon">
          <!-- Icon File Text / Rules -->
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="16" y1="13" x2="8" y2="13"></line>
              <line x1="16" y1="17" x2="8" y2="17"></line>
          </svg>
      </span>
      <span>{{ __('Aturan Pelaksanaan') }}</span>
  </a>
  ```

---

## 6. 🧪 STRATEGI TESTING & AUTOMATED REGRESSION TESTS

Junior developer atau AI wajib membuat dan menjalankan test suite berikut:

### Test 1: `tests/Feature/AdminKebijakanMagangTest.php`
- `test_admin_can_view_kebijakan_page_with_five_quota_parameters()`: Admin bisa buka halaman, melihat nilai default.
- `test_admin_can_update_kebijakan_parameters()`: Admin input angka baru, tersimpan di DB, audit log tercatat, cache ter-refresh.
- `test_non_admin_cannot_access_or_update_kebijakan()`: SuperAdmin, Pembimbing, dan Peserta ditolak dengan HTTP 403 saat POST/PUT ke rute ini.

### Test 2: `tests/Feature/PembimbingAturanBimbinganTest.php`
- `test_pembimbing_can_view_aturan_page_without_logbook_mundur_field()`:
  - Assert halaman merender `berkas_maks_mb`, `deadline_mendekati_hari`, `sla_verifikasi_hari`.
  - Assert `assertDontSee('logbook_maks_mundur_hari')` dan `assertDontSee('Batas Mundur Pengisian Logbook')`.
- `test_pembimbing_can_update_aturan_parameters()`: Nilai baru tersimpan di DB.
- `test_non_pembimbing_cannot_access_or_update_aturan()`: Ditolak dengan HTTP 403.

### Test 3: `tests/Feature/SuperAdminSettingsStrippedTest.php`
- `test_superadmin_settings_no_longer_displays_card_1_and_card_2()`:
  - Buka `/superadmin/pengaturan`.
  - Assert `assertDontSee('Kebijakan Kuota, Pendaftaran & Kapasitas Magang')`.
  - Assert `assertDontSee('Aturan Pelaksanaan, Logbook & Dokumen')`.
  - Assert hanya melihat Retensi Akun dan Landing Page.
- `test_superadmin_update_rejects_or_ignores_transferred_parameters()`:
  - Kirim payload lama yang memuat kuota atau batas mundur.
  - Parameter tersebut tidak lagi diolah atau disimpan oleh endpoint SuperAdmin.

---

## 7. ✅ DEFINISI SELESAI (DEFINITION OF DONE)

- [ ] Kartu "Kebijakan Kuota, Pendaftaran & Kapasitas Magang" berpindah ke Administrasi (`/admin/kebijakan`).
- [ ] Admin dapat mengubah 5 parameter kuota dengan validasi ketat dan tersimpan di database.
- [ ] Kartu "Aturan Pelaksanaan, Logbook & Dokumen" berpindah ke Pembimbing (`/pembimbing/aturan`).
- [ ] Input "Batas Mundur Pengisian Logbook (Hari)" 100% hilang dari antarmuka dan tidak ada di form Pembimbing.
- [ ] Pembimbing dapat mengubah 3 parameter aturan pelaksanaan magang.
- [ ] SuperAdmin sama sekali tidak lagi menampilkan kedua kartu tersebut di `/superadmin/pengaturan`.
- [ ] Endpoint update SuperAdmin tidak lagi memproses field-field yang sudah dipindahkan.
- [ ] Role protection aktif (`role:admin` untuk kebijakan kuota, `role:pembimbing` untuk aturan bimbingan).
- [ ] Navigasi sidebar Admin dan Pembimbing memuat link menu baru secara responsif dan semantik.
- [ ] 100% automated test suite lulus tanpa regresi (`php artisan test`).
- [ ] Lolos PSR-12 code style (`./vendor/bin/pint --test`) dan PHPStan `[OK] No errors`.
