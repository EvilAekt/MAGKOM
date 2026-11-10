# DOKUMEN TUGAS SENIOR DEVELOPER & PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Pembersihan & Penataan Kanal Audit Log dari Fitur yang Sudah Ditiadakan (Audit Log Stream Alignment)

---

**Kepada:** Junior Developer / AI Implementation Agent (Gemini 3.8 Flash, Cursor, Copilot)  
**Dari:** Senior Lead Software Engineer & Backend-Frontend Architect MAGKOM  
**Tanggal:** 9 November 2026  
**Status:** Ready for Implementation / Plan Approved  
**Prioritas:** P1 — High Usability, Code Cleanliness & Domain Parity  
**Target Modul:** Modul SuperAdmin — `Audit Log` (`superadmin.audit-log.*`)  

---

## 📌 1. RINGKASAN EKSEKUTIF (Executive Summary)

Pada modul **SuperAdmin Audit Log** (`/superadmin/audit-log`), navigasi sidebar kiri saat ini menampilkan 15 kanal audit peristiwa. Berdasarkan audit berkala dan arahan terbaru dari tim produk dan pemangku kepentingan:
1. **Fitur "Penilaian & Sertifikat" (`penilaian-sk`)** telah resmi ditiadakan dari alur operasional MagKom (alur kelulusan difokuskan pada pemenuhan logbook dan evaluasi bimbingan, bukan pencetakan sertifikat/rubrik nilai formal via web). Oleh karena itu, kanal ini **WAJIB DIHAPUS** dari daftar kanal Audit Log agar tidak membingungkan staf administrator.
2. Hasil audit mendalam terhadap seluruh 15 kanal audit menemukan 2 fitur tambahan yang juga tidak aktif/tidak tersedia di sistem antarmuka MagKom:
   - **`backup-database` ("Cadangan Basis Data")**: Fitur ini **tidak memiliki menu** pada sidebar sistem SuperAdmin (`resources/views/layouts/dashboard.blade.php`), memiliki 0 data audit di basis data, dan proses backup ditangani langsung pada level infrastruktur OS/server.
   - **`survei-evaluasi` ("Survei & Mutu Alumni")**: Fitur ini telah dimatikan secara permanen pada konfigurasi sistem (`fitur_survei_aktif: 0`), dan seluruh tautan menu survei telah disembunyikan.
3. Badge jumlah kanal (`15`) dan teks hero banner (`15 Kanal Audit Stream`) saat ini masih tertulis secara hardcoded statis. Elemen ini harus diubah menjadi **dinamis berbasis ukuran array `AuditLogController::CHANNELS`** sehingga otomatis akurat dan tahan perubahan di masa depan.

---

## 🔍 2. MATRIKS AUDIT KOMPREHENSIF SELURUH KANAL AUDIT LOG

Berikut adalah hasil audit lengkap dari seluruh 15 kanal yang ada saat ini terhadap keberadaan fitur aktual di MagKom:

| No | Key Kanal | Label Tampilan | Status Fitur Aktual di MagKom | Rekomendasi Aksi |
|:--:|:---|:---|:---|:---:|
| 1 | `all-actions-stream` | Semua Aktivitas Stream | **AKTIF** (Agregator seluruh kanal) | **PERTAHANKAN** |
| 2 | `auth-session` | Autentikasi & Sesi | **AKTIF** (Login, logout, lockout) | **PERTAHANKAN** |
| 3 | `pendaftaran` | Pendaftaran & Berkas | **AKTIF** (Form registrasi, upload CV/KTP) | **PERTAHANKAN** |
| 4 | `verifikasi-admin` | Verifikasi Admin | **AKTIF** (Pemeriksaan berkas pendaftaran) | **PERTAHANKAN** |
| 5 | `penempatan-mentor`| Penempatan & Mentor | **AKTIF** (Plotting bidang dinas & mentor) | **PERTAHANKAN** |
| 6 | `kuota-periode` | Kuota & Periode | **AKTIF** (Bulan magang, kuota jurusan, slot khusus) | **PERTAHANKAN** |
| 7 | `tugas-classroom` | Tugas & Progres | **AKTIF** (Tugas dinas & pengumpulan) | **PERTAHANKAN** |
| 8 | `logbook-presensi` | Logbook & Presensi | **AKTIF** (Jurnal harian & paraf mentor) | **PERTAHANKAN** |
| 9 | `penilaian-sk` | **Penilaian & Sertifikat** | ❌ **TIDAK ADA / DITIADAKAN** (Instruksi User) | 🗑️ **HAPUS DARI KANAL** |
| 10 | `survei-evaluasi` | **Survei & Mutu Alumni** | ❌ **NON-AKTIF** (`fitur_survei_aktif = 0` di pengaturan) | 🗑️ **HAPUS / DEPRECATE** |
| 11 | `backup-database` | **Cadangan Basis Data** | ❌ **TIDAK ADA DI UI** (Menu tidak ada, DB: 0 records) | 🗑️ **HAPUS / DEPRECATE** |
| 12 | `user-management` | Manajemen Pengguna | **AKTIF** (Staf dinas & matriks RBAC role) | **PERTAHANKAN** |
| 13 | `konfigurasi-master`| Konfigurasi & Master | **AKTIF** (Data jurusan, bidang, general settings) | **PERTAHANKAN** |
| 14 | `dynamic-module` | Modul Kustom Dinamis | **AKTIF** (Studio Modul builder SuperAdmin) | **PERTAHANKAN** |
| 15 | `security-alert` | Peringatan Keamanan | **AKTIF** (Deteksi 403, CSRF error, anomali) | **PERTAHANKAN** |

> [!IMPORTANT]
> **Keputusan Arsitektur:**
> - **Opsi Utama (Direkomendasikan)**: Menghapus 3 kanal fitur yang ditiadakan/non-aktif (`penilaian-sk`, `survei-evaluasi`, dan `backup-database`), sehingga menyisakan **12 Kanal Riil Aktif** yang 100% selaras dengan alur kerja MagKom saat ini.
> - **Opsi Minimal**: Menghapus hanya `penilaian-sk` (sesuai instruksi harfiah pengguna), menyisakan 14 kanal.
> *Panduan ini dirancang untuk mengeksekusi Opsi Utama secara bersih, atau opsi minimal dengan langkah modular yang terisolasi.*

---

## 🏗️ 3. INVENTARIS FILE & ARSITEKTUR PERUBAHAN

```
┌────────────────────────────────────────────────────────────────────────┐
│ 1. app/Http/Controllers/SuperAdmin/AuditLogController.php             │
│    -> Menghapus entri kanal yang ditiadakan dari konstanta CHANNELS.   │
│                                                                        │
│ 2. app/Services/AuditService.php                                       │
│    -> Menyesuaikan fungsi resolveChannel() agar tidak mengarahkan      │
│       event baru ke kanal yang sudah dihapus.                          │
│                                                                        │
│ 3. resources/views/superadmin/audit-log/partials/_header.blade.php     │
│    -> Mengubah teks statis "15 Kanal" menjadi dinamis berbasis array.  │
│                                                                        │
│ 4. resources/views/superadmin/audit-log/partials/_channels_sidebar.blade.php
│    -> Mengubah badge hitungan "15" menjadi dinamis: {{ count($channels) }}
│                                                                        │
│ 5. tests/Feature/SuperAdminMasterArchitectureTest.php                  │
│    -> Menyesuaikan assertion kanal agar lulus 100%.                    │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 🛠️ 4. SPESIFIKASI TEKNIS & PANDUAN LANGKAH (Atomic Tasks)

Junior Developer / AI Agent (Gemini 3.8 Flash) **WAJIB** mengikuti langkah implementasi di bawah ini secara runut:

---

### TUGAS 1: Hapus Kanal yang Ditiadakan dari Konstanta `CHANNELS`
**Lokasi Berkas:** `app/Http/Controllers/SuperAdmin/AuditLogController.php`

Hapus definisi kanal `penilaian-sk`, `survei-evaluasi`, dan `backup-database` dari array `public const CHANNELS`.

#### Kode SEBELUM:
```php
        'logbook-presensi' => [
            'key' => 'logbook-presensi',
            'label' => 'Logbook & Presensi',
            'icon' => 'book-open',
            'color' => '#14B8A6',
            'desc' => 'Pencatatan aktivitas harian, penguncian mingguan, & verifikasi mentor',
        ],
        'penilaian-sk' => [
            'key' => 'penilaian-sk',
            'label' => 'Penilaian & Sertifikat',
            'icon' => 'award',
            'color' => '#EC4899',
            'desc' => 'Input nilai rubrik, konversi predikat, penerbitan sertifikat digital',
        ],
        'survei-evaluasi' => [
            'key' => 'survei-evaluasi',
            'label' => 'Survei & Mutu Alumni',
            'icon' => 'smile',
            'color' => '#84CC16',
            'desc' => 'Kuesioner kepuasan, evaluasi fasilitas, dan kritik saran alumni',
        ],
        'user-management' => [
            'key' => 'user-management',
            'label' => 'Manajemen Pengguna',
            'icon' => 'user-check',
            'color' => '#06B6D4',
            'desc' => 'Pembuatan staf internal, reset kata sandi, dan perubahan hak role',
        ],
        'konfigurasi-master' => [
            'key' => 'konfigurasi-master',
            'label' => 'Konfigurasi & Master',
            'icon' => 'settings',
            'color' => '#64748B',
            'desc' => 'Parameter sistem, master jurusan, bidang dinas, dan rubrik penilaian',
        ],
        'backup-database' => [
            'key' => 'backup-database',
            'label' => 'Cadangan Basis Data',
            'icon' => 'database',
            'color' => '#F97316',
            'desc' => 'Pemicuan snapshot backup SQL, download arsip, & disaster recovery',
        ],
        'security-alert' => [
```

#### Kode SESUDAH (Bersih 12 Kanal Riil):
```php
        'logbook-presensi' => [
            'key' => 'logbook-presensi',
            'label' => 'Logbook & Presensi',
            'icon' => 'book-open',
            'color' => '#14B8A6',
            'desc' => 'Pencatatan aktivitas harian, penguncian mingguan, & verifikasi mentor',
        ],
        'user-management' => [
            'key' => 'user-management',
            'label' => 'Manajemen Pengguna',
            'icon' => 'user-check',
            'color' => '#06B6D4',
            'desc' => 'Pembuatan staf internal, reset kata sandi, dan perubahan hak role',
        ],
        'konfigurasi-master' => [
            'key' => 'konfigurasi-master',
            'label' => 'Konfigurasi & Master',
            'icon' => 'settings',
            'color' => '#64748B',
            'desc' => 'Parameter sistem, master jurusan, bidang dinas, dan pengaturan portal',
        ],
        'security-alert' => [
```

---

### TUGAS 2: Sesuaikan Fallback Resolver Kanal di Service
**Lokasi Berkas:** `app/Services/AuditService.php`

Pada method `resolveChannel($entitas, $aksi, $detail)`:
Alihkan event yang sebelumnya masuk ke `penilaian-sk`, `survei-evaluasi`, atau `backup-database` agar masuk ke kanal yang relevan (`konfigurasi-master` atau `penempatan-mentor`).

#### Kode SEBELUM:
```php
        if ($entitas === 'penilaian' || $entitas === 'sertifikat' || str_contains($text, 'nilai') || str_contains($text, 'sk') || str_contains($text, 'sertifikat')) {
            return 'penilaian-sk';
        }
        if ($entitas === 'survei' || $entitas === 'survei_kepuasan' || str_contains($text, 'survei') || str_contains($text, 'evaluasi')) {
            return 'survei-evaluasi';
        }
        if (str_starts_with((string) $entitas, 'modul_dinamis') || str_starts_with((string) $entitas, 'modul_data') || str_contains($text, 'modul')) {
            return 'dynamic-module';
        }
        if (str_contains($text, 'backup') || str_contains($text, 'snapshot') || str_contains($text, 'database')) {
            return 'backup-database';
        }
```

#### Kode SESUDAH:
```php
        if (str_starts_with((string) $entitas, 'modul_dinamis') || str_starts_with((string) $entitas, 'modul_data') || str_contains($text, 'modul')) {
            return 'dynamic-module';
        }
        if ($entitas === 'penilaian' || $entitas === 'sertifikat' || str_contains($text, 'nilai') || str_contains($text, 'sertifikat')) {
            return 'penempatan-mentor';
        }
```

---

### TUGAS 3: Buat Indikator Jumlah Kanal Menjadi Dinamis di Blade
**Lokasi Berkas:** 
1. `resources/views/superadmin/audit-log/partials/_header.blade.php`
2. `resources/views/superadmin/audit-log/partials/_channels_sidebar.blade.php`

#### 3.1 Pada `_header.blade.php`:
Ubah teks statis baris 26:
```blade
{{-- SEBELUM --}}
<span>Cakupan: <strong>15 Kanal Audit Stream</strong></span>

{{-- SESUDAH --}}
@php
    $totalKanalAktif = count(\App\Http\Controllers\SuperAdmin\AuditLogController::CHANNELS);
@endphp
<span>Cakupan: <strong>{{ $totalKanalAktif }} Kanal Audit Stream</strong></span>
```

#### 3.2 Pada `_channels_sidebar.blade.php`:
Ubah badge count statis baris 25:
```blade
{{-- SEBELUM --}}
<span class="audit-panel-count-badge">15</span>

{{-- SESUDAH --}}
<span class="audit-panel-count-badge">{{ count($channels) }}</span>
```

---

### TUGAS 4: Perbarui Test Suite Arsitektur SuperAdmin
**Lokasi Berkas:** `tests/Feature/SuperAdminMasterArchitectureTest.php`

Pada method `test_superadmin_can_access_multi_channel_audit_log_and_view_15_channels()`:
Perbarui assertion agar memeriksa kanal aktif yang ada dan memastikan kanal `Penilaian & Sertifikat` tidak lagi ditampilkan.

#### Kode SESUDAH:
```php
        $response->assertStatus(200);
        $response->assertSee('KANAL PERISTIWA');
        $response->assertSee('Semua Aktivitas Stream');
        $response->assertSee('Autentikasi & Sesi');
        $response->assertSee('Pendaftaran & Berkas');
        $response->assertSee('Verifikasi Admin');
        $response->assertSee('Penempatan & Mentor');
        $response->assertSee('Kuota & Periode');
        $response->assertSee('Tugas & Progres');
        $response->assertSee('Logbook & Presensi');
        $response->assertSee('Manajemen Pengguna');
        $response->assertSee('Konfigurasi & Master');
        $response->assertSee('Peringatan Keamanan');
        $response->assertSee('Modul Kustom Dinamis');

        // Pastikan kanal fitur yang ditiadakan tidak lagi muncul di sidebar
        $response->assertDontSee('Penilaian & Sertifikat');
```

---

## 🛡️ 5. QUALITY GATES & VERIFIKASI SEBELUM SELESAI

Setelah kode diterapkan, jalankan serangkaian verifikasi wajib berikut:

1. **Automated Unit & Feature Tests**:
   ```bash
   php artisan test --filter=SuperAdminMasterArchitectureTest
   php artisan test --filter=SuperAdminDashboardAndAuditLogTest
   ```
   *Ekspektasi:* **PASS 100%**.

2. **Kompilasi Aset Frontend (Vite)**:
   ```bash
   npm run build
   ```
   *Ekspektasi:* Berhasil tanpa galat kompilasi.

3. **Verifikasi Visual di Browser**:
   Buka `http://localhost:8000/superadmin/audit-log` atau `http://magkom.test:8080/superadmin/audit-log`:
   - [ ] Pada panel sidebar kiri, kanal `Penilaian & Sertifikat` sudah tidak ada sama sekali.
   - [ ] Badge angka pada header panel menampilkan jumlah kanal riil yang aktif secara dinamis.
   - [ ] Subtitle pada hero banner di atas menampilkan teks jumlah kanal dinamis.
   - [ ] Filter klik antar kanal berfungsi normal dan tidak ada tautan rusak.

---

## 📋 6. CHECKLIST PENYELESAIAN (Acceptance Criteria)

| No | Kriteria Penerimaan | Status |
|:--:|:--------------------|:------:|
| 1  | Kanal `penilaian-sk` ("Penilaian & Sertifikat") telah dihapus dari `AuditLogController::CHANNELS`. | [ ] |
| 2  | Kanal non-aktif lainnya (`backup-database` dan/atau `survei-evaluasi`) telah dirapikan. | [ ] |
| 3  | Header dan badge total kanal di Blade menggunakan nilai dinamis `count($channels)`. | [ ] |
| 4  | Event audit resolver di `AuditService` tidak lagi mengembalikan key kanal yang dihapus. | [ ] |
| 5  | Seluruh automated test (`SuperAdminMasterArchitectureTest` & `SuperAdminDashboardAndAuditLogTest`) PASS. | [ ] |
| 6  | Kompilasi Vite (`npm run build`) berjalan sukses. | [ ] |

---
*Dokumen PRD & Rencana Kerja ini diterbitkan oleh Senior Lead Software Engineer MAGKOM.*

