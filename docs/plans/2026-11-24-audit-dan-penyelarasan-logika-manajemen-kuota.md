# TASK & PRD PLAN: AUDIT, PENYELARASAN LOGIKA SISTEM, DAN PERBAIKAN MANAJEMEN KUOTA MAGANG

> **Dari:** Senior Software Architect / Lead AI Engineer  
> **Kepada:** Tim Pengembang Web & AI Antigravity  
> **Target Repositori:** `MAGKOM` (Portal Manajemen Magang Dinas Kominfo Kabupaten Sleman)  
> **Status:** APPROVED FOR EXECUTION  
> **Tingkat Prioritas:** High (System Integrity, Business Rules Enforcement, Zero-Bug Quality Gate)  
> **Tanggal Dokumen:** 24 November 2026  
> **Referensi Gambar Permintaan:** Screenshot Halaman Manajemen Kuota Magang (`/admin/kuota`)  

---

## 1. 🎯 RINGKASAN EKSEKUTIF & TUJUAN TASK

### 1.1 Latar Belakang
Pengguna meminta audit menyeluruh terhadap fitur **Manajemen Kuota & Periode Magang** (`/admin/kuota`):
1. **Pemeriksaan Keterhubungan Logika**: Memastikan seluruh operasi Admin saat membuat, mengubah, atau memperbarui bulan magang dan kuota jurusan/bidang terhubung secara benar, konsisten, dan sinkron ke seluruh modul sistem (Portal Publik/Landing Page, Registrasi Peserta, Penempatan Peserta, Dashboard Admin & SuperAdmin, serta Laporan).
2. **Analisis Bebas Bug & Kegagalan**: Memeriksa potensi bug, exception tidak tertangani, pelanggaran integritas database, dan celah aturan bisnis (BR-01 s/d BR-17).
3. **Pengujian & Quality Gate**: Menjalankan pengujian otomatis end-to-end dengan PHP 8.3 aktual dan memastikan seluruh parameter quality gate terpenuhi.

---

## 2. 🔍 HASIL AUDIT KETERHUBUNGAN ANTAR MODUL

Berdasarkan penelusuran arsitektur kode di repositori MAGKOM, berikut hasil pemetaan keterhubungan fitur Manajemen Kuota:

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                    ADMIN MANAJEMEN KUOTA (/admin/kuota)                         │
│  - Buat Bulan Baru (storeBulan / admin.bulan.store)                             │
│  - Ubah Bulan & Jadwal (updateBulan / admin.bulan.update)                       │
│  - Toggle Aktif Bulan (toggleAktif)                                             │
│  - Tambah Kuota Jurusan (store / StoreKuotaAction)                              │
│  - Ubah Kuota Bidang (updateBidangKuota / UpdateBidangKuotaAction)              │
│  - Toggle Kuota Jurusan (toggleAktif / ToggleKuotaAction)                       │
└────────┬──────────────────────┬───────────────────────┬─────────────────────────┘
         │                      │                       │
         ▼                      ▼                       ▼
┌──────────────────┐  ┌──────────────────┐    ┌───────────────────────────────────┐
│   LANDING PAGE   │  │   PENDAFTARAN    │    │       PENEMPATAN PESERTA          │
│  (LandingCtrl)   │  │ (PendaftaranCtrl)│    │       (PenempatanCtrl)            │
│                  │  │                  │    │                                   │
│ • Hanya bulan    │  │ • Validasi via   │    │ • Menampilkan kuota aktif         │
│   aktif & dalam  │  │   BulanStatus    │    │   pada periode terpilih.          │
│   rentang tanggal│  │   Service: jika  │    │ • Penetapan peserta mengonsumsi   │
│   yang tampil    │  │   Tutup / Draft, │    │   kuota atomik dengan row lock    │
│   (BR-04).       │  │   tolak regist.  │    │   (BR-05).                        │
│ • Menampilkan    │  │ • Kuota penuh    │    │ • Pembatalan penempatan sebelum   │
│   sisa kuota live│  │   diarahkan ke   │    │   mulai mengembalikan kuota;      │
│   per jurusan.   │  │   Waiting List   │    │   setelah mulai kuota hangus      │
│ • Jika kuota = 0,│  │   (BR-04).       │    │   (BR-06).                        │
│   tombol daftar  │  │ • Konsumsi kuota │    │                                   │
│   menjadi label  │  │   atomik lock.   │    │                                   │
│   "Kuota Penuh". │  │                  │    │                                   │
└──────────────────┘  └──────────────────┘    └───────────────────────────────────┘
```

### 2.1 Hubungan ke Modul Publik / Landing Page
- **Service Penghubung:** `App\Services\BulanStatusService`.
- **Perilaku Teruji:**
  - Jika admin mengubah `tgl_buka` atau `tgl_tutup`, status periode di landing page publik (`Tersedia`, `Segera`, `Ditutup`) langsung berubah secara otomatis sesuai waktu nyata.
  - Jika admin menyetel status menjadi `Draft` (`aktif = false`), periode tersebut seketika **tidak ditampilkan** di landing page publik (BR-04).
  - Jika kuota suatu jurusan habis (`jumlah == terisi`), landing page menampilkan badge "Kuota Penuh" dan tombol registrasi dinonaktifkan.

### 2.2 Hubungan ke Modul Pendaftaran Peserta
- **Form Request & Service:** `App\Http\Requests\PendaftaranRequest` & `App\Services\QuotaAllocationService`.
- **Perilaku Teruji:**
  - Pendaftaran peserta memvalidasi ketersediaan periode bulan magang (`StatusKetersediaanEnum::Tersedia`). Jika bulan magang berstatus Draft atau Ditutup, pengajuan ditolak.
  - Pengajuan mengonsumsi kuota secara atomik menggunakan `lockForUpdate()` untuk mencegah kondisi balapan (*race condition*).
  - Peserta yang mendaftar saat kuota habis otomatis masuk ke status antrean `WaitingList` (BR-04).

### 2.3 Hubungan ke Modul Penempatan Peserta
- **Controller & Action:** `App\Http\Controllers\Admin\PenempatanController` & `PlacementService`.
- **Perilaku Teruji:**
  - Modal penetapan peserta mengambil data kuota aktif pada periode bulan terkait.
  - Jika penempatan dibatalkan sebelum tanggal mulai magang, kuota otomatis dikembalikan secara atomik (`decrement('terisi')`). Jika dibatalkan setelah tanggal mulai magang, kuota tidak dikembalikan sesuai BR-06.

### 2.4 Hubungan ke Dashboard Admin & SuperAdmin
- **Service:** `AdminDashboardMetricsService` & `SuperAdminExecutiveMetricsService`.
- **Perilaku Teruji:**
  - Filter periode bulan magang di dashboard admin menghitung total kapasitas, total terisi, dan persentase okupansi secara akurat berdasarkan kuota aktif.

---

## 3. 🚨 TEMUAN ANOMALI, CELAH LOGIKA, DAN PERBAIKAN

Meskipun sistem telah berjalan baik, audit menyeluruh menemukan **3 celah logika/bug kritis** yang perlu segera diperbaiki:

### Bug 1: Missing Unique & Range Validation pada Tanggal `bulan_mulai` di `KuotaController::storeBulan` & `updateBulan`
- **Lokasi:** `app/Http/Controllers/Admin/KuotaController.php` (baris 94-142).
- **Masalah:**
  - Di skema database (`bulan_magang`), kolom `bulan_mulai` memiliki indeks `UNIQUE`.
  - Di `KuotaController::storeBulan`, validasi `bulan_mulai` hanya mengecek `required|date|after:tgl_tutup`.
  - **Dampak:** Jika admin membuat bulan baru dari hero banner dengan tanggal mulai yang sama dengan bulan eksisting, Laravel akan melempar `QueryException` (Database Integrity Violation HTTP 500) yang merusak UX!
  - Pada `updateBulan`, tidak ada validasi `Rule::unique('bulan_magang', 'bulan_mulai')->ignore($bulanMagang->id)` dan tidak ada pengecekan `bulan_mulai` harus `after:tgl_tutup` (atau `after_or_equal:tgl_tutup`).
- **Solusi:**
  - Tambahkan rule `Rule::unique('bulan_magang', 'bulan_mulai')` pada `storeBulan` dan `Rule::unique(...)->ignore($bulanMagang->id)` pada `updateBulan`.
  - Tambahkan validasi tanggal mulai magang harus setelah tanggal tutup pendaftaran pada `updateBulan`.

---

### Bug 2: Bypass Penegakan Aturan Bisnis BR-12 saat Men-Draftkan Bulan dari Modal Hero Banner
- **Lokasi:** `app/Actions/Admin/Kuota/UpdateBulanMagangAction.php` dan `app/Http/Controllers/Admin/KuotaController.php`.
- **Masalah:**
  - Pada `BulanController::toggleAktif`, terdapat penegakan ketat aturan BR-12: penonaktifan bulan ditolak jika masih terdapat pendaftaran aktif (`Diajukan`, `WaitingList`, `PerluPerbaikan`, `BerkasBelumLengkap`, `Diterima`) atau penempatan aktif.
  - Namun di hero banner modal "Ubah bulan" (`#modalUbahBulan`), admin dapat memilih pill status "Draft".
  - Pada `UpdateBulanMagangAction`, status langsung diubah menjadi `aktif = false` tanpa memeriksa keberadaan pendaftaran aktif maupun penempatan aktif!
  - **Dampak:** Admin dapat secara tidak sengaja menonaktifkan/men-draftkan bulan yang sudah memiliki peserta aktif, memutuskan alur peserta di sistem.
- **Solusi:**
  - Tambahkan validasi penegakan BR-12 pada `UpdateBulanMagangAction` atau `KuotaController::updateBulan`. Jika bulan memiliki pendaftaran atau penempatan aktif dan admin mencoba mengubah status menjadi Draft, tolak dengan pesan kesalahan yang informatif.

---

### Bug 3: Missing Validasi Penurunan Kuota di Bawah Peserta Terisi pada `StoreKuotaAction` & `KuotaRequest::store`
- **Lokasi:** `app/Actions/Admin/Kuota/StoreKuotaAction.php` & `app/Http/Requests/KuotaRequest.php`.
- **Masalah:**
  - Pada `KuotaRequest::withValidator`, pengecekan `jumlah < kuota->terisi` hanya membaca `$this->route('kuota')`. Pada route `admin.kuota.store` (`POST /admin/kuota`), route parameter `{kuota}` bernilai `null`.
  - Pada `StoreKuotaAction`:
    ```php
    $existing = Kuota::where('bulan_magang_id', $bulanMagangId)->where('jurusan_id', $jId)->lockForUpdate()->first();
    if ($existing) {
        $existing->jumlah = $jumlah; // <-- Tidak dicek apakah $jumlah < $existing->terisi!
        $existing->save();
    }
    ```
  - **Dampak:** Jika suatu jurusan sudah terisi 5 peserta, lalu admin membuka modal "+ Tambah kuota" dan memasukkan angka 2 untuk jurusan tersebut, kuota akan ter-overwrite menjadi 2 (di bawah jumlah terisi), melanggar aturan integritas data BR-12!
- **Solusi:**
  - Tambahkan validasi di `StoreKuotaAction` (dan `KuotaRequest`): jika baris kuota eksisting ditemukan dan `$jumlah < $existing->terisi`, tolak pembaruan dan berikan pesan kesalahan: *"Jumlah kuota tidak boleh lebih kecil dari peserta yang sudah diterima ({$existing->terisi})."*.

---

### Bug 4: Sinkronisasi Validasi Pagu Kuota Bulanan di `KuotaRequest`
- **Lokasi:** `app/Http/Requests/KuotaRequest.php` (baris 70-92).
- **Masalah:**
  - `KuotaRequest` membatasi total kuota bulanan dengan `Pengaturan::int('kuota_umum_maks_per_bulan', 12)`.
  - Namun pada modul Manajemen Kuota terpadu yang baru, kapasitas diatur per bidang berdasarkan ketersediaan pembimbing (seperti pada screenshot user: total 110 peserta).
  - Jika admin menambah kuota jurusan baru lewat modal "+ Tambah Kuota", validasi `totalSetelahInput > $maksPerBulan` akan memblokir input jika nilai pagu di database masih default lama (12).
- **Solusi:**
  - Di `KuotaRequest`, lakukan evaluasi cerdas: jika total kuota eksisting di bulan tersebut sudah melebihi pagu lama atau kapasitas bidang telah dialokasikan, validasi pagu tidak boleh memblokir kuota valid kecuali melebihi pagu maksimal dinas yang sebenarnya (misal 500) atau pagu dinamis yang relevan.

---

## 4. 🛠️ RENCANA EKSEKUSI PERBAIKAN (ACTION PLAN)

| No | Target Berkas | Aksi | Deskripsi Perubahan |
| :--- | :--- | :---: | :--- |
| 1 | `app/Http/Controllers/Admin/KuotaController.php` | **Modify** | 1. Tambahkan validasi `Rule::unique('bulan_magang', 'bulan_mulai')` pada `storeBulan`.<br>2. Tambahkan validasi `Rule::unique(...)->ignore(...)` dan `after_or_equal:tgl_tutup` pada `updateBulan`.<br>3. Tangani hasil validasi BR-12 dari `UpdateBulanMagangAction`. |
| 2 | `app/Actions/Admin/Kuota/UpdateBulanMagangAction.php` | **Modify** | Terapkan proteksi BR-12: tolak perubahan status menjadi `draft` jika periode bulan magang masih memiliki pendaftaran aktif atau penempatan aktif. |
| 3 | `app/Actions/Admin/Kuota/StoreKuotaAction.php` | **Modify** | Tambahkan proteksi BR-12: jika kuota jurusan eksisting ditemukan dan `$jumlah < $existing->terisi`, lemparkan exception / tolak perubahan kuota di bawah jumlah terisi. |
| 4 | `app/Http/Requests/KuotaRequest.php` | **Modify** | Pastikan validasi `jumlah >= terisi` juga mengecek data kuota eksisting pada skenario `store` ketika jurusan sudah terdaftar. |
| 5 | `tests/Feature/AdminKuotaRevampTest.php` | **Modify** | Tambahkan unit & feature test komprehensif untuk menguji seluruh perbaikan celah di atas (pencegahan duplikasi tanggal mulai, proteksi BR-12 saat draf hero banner, dan proteksi kuota di bawah terisi). |

---

## 5. 🧪 VERIFIKASI & QUALITY GATES

Setelah perubahan diimplementasikan:
1. **PHPUnit Test**:
   - Jalankan `tests/Feature/AdminKuotaRevampTest.php`
   - Jalankan `tests/Feature/QuotaConcurrencyTest.php`
   - Jalankan `tests/Feature/LandingPageTest.php`
   - Jalankan `tests/Feature/PesertaPendaftaranFlowTest.php`
   - Jalankan `tests/Feature/AdminPenempatanLifecycleFeatureTest.php`
2. **PHPStan Static Analysis**:
   - Jalankan `./vendor/bin/phpstan analyse --memory-limit=1G`
3. **Laravel Pint Code Formatting**:
   - Jalankan `./vendor/bin/pint --test`
4. **Vite Asset Build**:
   - Jalankan `npm run build`

