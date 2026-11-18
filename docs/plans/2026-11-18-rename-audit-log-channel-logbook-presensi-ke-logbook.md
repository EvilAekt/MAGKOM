# Rencana Kerja: Perubahan Kanal Audit Log dari #logbook-presensi ke #logbook

- **Tanggal Dokumen**: 18 November 2026
- **Status**: Selesai (Completed)
- **Terkait Modul**: SuperAdmin — Audit Log Multi-Channel Navigator & Tracking
- **Dokumen Referensi**:
  - `docs/01-PRD-MagKom.md`
  - `docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`
  - `docs/plans/2026-11-09-senior-dev-task-pembersihan-kanal-audit-log-fitur-nonaktif-prd.md`
  - Rule Proyek: `RULE[g:\MAGKOM\.agents\rules\AGENTS.md]`

---

## 1. Tujuan

Mengubah identifikasi kanal dan label audit log SuperAdmin yang sebelumnya bernama:
- **Slug / Key Kanal**: `#logbook-presensi` (`logbook-presensi`)
- **Label Tampilan**: `Logbook & Presensi`

Menjadi:
- **Slug / Key Kanal**: `#logbook` (`logbook`)
- **Label Tampilan**: `Logbook`

Perubahan ini dilakukan karena fitur presensi telah resmi dihapus dan ditiadakan dari sistem MagKom, sehingga seluruh pencatatan kegiatan peserta kini berpusat pada modul **Logbook** (jurnal harian dan penguncian mingguan).

---

## 2. Asumsi & Investigasi Awal

1. **Investigasi Frontend (Blade & JS)**:
   - File template `resources/views/superadmin/audit-log/partials/_channels_sidebar.blade.php` me-render daftar kanal secara dinamis melalui perulangan `$channels` yang disediakan oleh `AuditLogController::CHANNELS`.
   - File tabel `resources/views/superadmin/audit-log/partials/_stream_table.blade.php` menampilkan slug kanal dengan `#{{ $log->channel ?: 'system' }}`.
   - Skrip `resources/js/pages/superadmin/audit-log-index.js` membaca `data-channel-label` dan `data-channel-key` langsung dari atribut DOM tanpa hardcode string nama kanal.
   - **Kesimpulan**: *Tidak ada perubahan pada markup Blade maupun skrip JavaScript.* Perubahan sepenuhnya dilakukan pada lapisan Controller, Service, Seeder, dan Test Suite (mematuhi aturan larangan manipulasi Blade yang tidak perlu).

2. **Skema Basis Data**:
   - Kolom `channel` pada tabel `audit_log` bertipe `VARCHAR(40)` (bukan ENUM).
   - Tidak ada modifikasi skema DDL (*zero schema migration*).

3. **Integritas Jejak Audit & Backward Compatibility**:
   - Di lingkungan staging atau riwayat lokal mungkin terdapat rekaman log lama dengan `channel = 'logbook-presensi'`.
   - Query filter kanal `logbook` harus tetap mampu menampilkan log historis tersebut (`whereIn('channel', ['logbook', 'logbook-presensi'])`) dan counter 24 jam harus mengakumulasi keduanya agar data riwayat tidak tersembunyi.
   - Akses URL lama `/superadmin/audit-log/channel/logbook-presensi` harus secara anggun dialihkan (mapped) ke kanal `logbook` daripada jatuh ke fallback `all-actions-stream`.

---

## 3. Evaluasi Pendekatan Teknis

### Opsi A: Rename Kanal dengan Kompatibilitas Transisi Data (Direkomendasikan)
- Mengubah konstanta `AuditLogController::CHANNELS`: key `logbook`, label `Logbook`.
- Mengubah `AuditService::resolveChannel()` agar mengembalikan `'logbook'`.
- Pada `AuditLogController`, tambahkan toleransi: jika kanal aktif adalah `logbook`, filter database mengambil `channel IN ('logbook', 'logbook-presensi')`, dan jika ada parameter URL `logbook-presensi`, dialihkan ke `logbook`.
- **Kelebihan**: Sangat aman, nol risiko kehilangan jejak historis, tidak memerlukan migrasi data destruktif, tautan lama tetap berfungsi.
- **Kekurangan**: Membutuhkan mapping backward compatibility sementara di controller.

### Opsi B: Hard Rename & Migrasi Data Seketika (Hard Cut)
- Mengubah konstanta menjadi `logbook` dan langsung menjalankan perintah update massal pada tabel `audit_log`: `UPDATE audit_log SET channel = 'logbook' WHERE channel = 'logbook-presensi'`.
- **Kelebihan**: Kode controller murni hanya mengecek `'logbook'`.
- **Kekurangan**: Menyentuh data audit log secara langsung (WORM compliance note) dan jika ada cache/link lama akan langsung menghasilkan stream default.

**Rekomendasi**: Menggunakan **Opsi A** untuk keandalan runtime, dengan opsi pembaruan data lokal melalui perintah Artisan/Tinker jika user menghendaki pembersihan data riwayat.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Controllers/SuperAdmin/AuditLogController.php` | Modify | 1. Ubah key `logbook-presensi` menjadi `logbook` dan label `'Logbook & Presensi'` menjadi `'Logbook'` pada `CHANNELS`.<br>2. Tambahkan pemetaan kompatibilitas `$activeChannel === 'logbook-presensi' => 'logbook'`.<br>3. Tambahkan agregasi counter 24 jam untuk kanal `logbook` yang mencakup data legacy `logbook-presensi`.<br>4. Perbarui `buildFilterQuery` agar kanal `logbook` mencakup `['logbook', 'logbook-presensi']`. |
| `app/Services/AuditService.php` | Modify | Pada metode `resolveChannel()`, ubah return value dari `'logbook-presensi'` menjadi `'logbook'`. |
| `database/seeders/ProductionRealisticDataSeeder.php` | Modify | Ganti contoh data audit log berkanal `'logbook-presensi'` menjadi `'logbook'`. |
| `tests/Feature/SuperAdminMasterArchitectureTest.php` | Modify | Perbarui assertion pada `test_superadmin_can_access_multi_channel_audit_log_and_view_15_channels` untuk memeriksa `'Logbook'`, slug `#logbook`, dan rute kanal `/superadmin/audit-log/channel/logbook`, serta memastikan `Logbook & Presensi` sudah tidak muncul. |

---

## 5. Langkah Implementasi Bertahap

- [x] **Langkah 1: Perbarui Service Audit Resolver (`app/Services/AuditService.php`)**
  - Ubah blok resolusi logbook:
    ```php
    if ($entitas === 'logbook' || $entitas === 'progres_log' || str_contains($text, 'logbook') || str_contains($text, 'presensi')) {
        return 'logbook';
    }
    ```
- [x] **Langkah 2: Perbarui Controller Audit Log (`app/Http/Controllers/SuperAdmin/AuditLogController.php`)**
  - Perbarui definisi kanal `CHANNELS`:
    ```php
    'logbook' => [
        'key' => 'logbook',
        'label' => 'Logbook',
        'icon' => 'book-open',
        'color' => '#14B8A6',
        'desc' => 'Pencatatan aktivitas harian, penguncian mingguan, & verifikasi mentor',
    ],
    ```
  - Pada method `index()`:
    - Normalisasi channel: jika `$activeChannel === 'logbook-presensi'`, set `$activeChannel = 'logbook'`.
    - Counter 24 jam: pastikan `$rawCounts['logbook']` menambahkan `($rawCounts['logbook-presensi'] ?? 0)`.
  - Pada method `buildFilterQuery()`:
    - Jika `$activeChannel === 'logbook'`, gunakan `$query->whereIn('channel', ['logbook', 'logbook-presensi'])`.
- [x] **Langkah 3: Selaraskan Seeder Data Realistis (`database/seeders/ProductionRealisticDataSeeder.php`)**
  - Ubah entri seeder kanal audit:
    ```php
    [TipeAuditEnum::Aksi, 'logbook', 'info', 'setujui_logbook', 'Logbook', 1, 'Pembimbing menyetujui logbook mingguan peserta.', 'POST', '/pembimbing/logbook/1/setujui', 302],
    ```
- [x] **Langkah 4: Perbarui dan Perluas Test Suite (`tests/Feature/SuperAdminMasterArchitectureTest.php`)**
  - Ubah assertion `$response->assertSee('Logbook & Presensi');` menjadi:
    - `$response->assertSee('Logbook');`
    - `$response->assertDontSee('Logbook & Presensi');`
  - Tambahkan assertion rute kanal:
    - Akses rute `superadmin.audit-log.channel` dengan parameter `channel => 'logbook'`.
    - Pastikan respons HTTP 200 dan elemen navigasi aktif menunjukkan kanal Logbook.
    - Akses URL legacy `superadmin/audit-log/channel/logbook-presensi` dan pastikan tetap menangani dengan baik (HTTP 200 pada kanal Logbook).
- [x] **Langkah 5: Quality Gate & Formatter Verification**
  - Jalankan `php artisan test tests/Feature/SuperAdminMasterArchitectureTest.php`.
  - Jalankan seluruh tes audit log: `php artisan test tests/Feature/SuperAdminAuditLogDetailTest.php tests/Feature/SuperAdminDashboardAndAuditLogTest.php`.
  - Jalankan `./vendor/bin/pint --test` untuk memastikan kepatuhan standar format PSR-12.

---

## 6. Strategi Testing

1. **Feature Test (Pest/PHPUnit)**:
   - `SuperAdminMasterArchitectureTest`:
     - **Happy Path 1**: SuperAdmin membuka `/superadmin/audit-log`, memastikan nama kanal `Logbook` tercantum di sidebar navigator, dan teks `Logbook & Presensi` tidak lagi tampil.
     - **Happy Path 2**: SuperAdmin memfilter kanal `/superadmin/audit-log/channel/logbook`, memastikan log dengan channel `logbook` muncul.
     - **Backward Compatibility Test**: Memastikan log audit yang dibuat dengan label lawas `logbook-presensi` tetap terambil saat memfilter kanal `logbook`.
     - **Legacy URL Mapping Test**: Memastikan navigasi ke `/superadmin/audit-log/channel/logbook-presensi` tidak error 404/500 dan memetakan ke kanal `logbook`.
2. **Quality Gate Commands**:
   - `php artisan test tests/Feature/SuperAdminMasterArchitectureTest.php`
   - `./vendor/bin/pint --test`

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| Log audit lama berlabel `logbook-presensi` tidak muncul di filter baru `logbook` | Sedang (Data historis terlihat seolah hilang) | Menerapkan `whereIn('channel', ['logbook', 'logbook-presensi'])` pada query builder controller dan menjumlahkan counter 24 jam. |
| Pengguna mengakses bookmark URL lama `/superadmin/audit-log/channel/logbook-presensi` | Rendah | Mengarahkan/memetakan parameter kanal lama ke kanal `logbook` secara transparan. |
| Pelanggaran aturan Blade read-only | Tinggi (Pelanggaran rule repositori) | Tidak ada modifikasi pada file Blade sama sekali karena antarmuka sudah sepenuhnya modular dan berbasis data Controller. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Kanal `#logbook-presensi` resmi diubah menjadi `#logbook`.
- [x] Label tampilan di sidebar audit log berubah dari `Logbook & Presensi` menjadi `Logbook`.
- [x] Pencatatan log baru melalui `AuditService` secara otomatis menggunakan channel `logbook`.
- [x] Rekaman log historis lama tetap dapat ditampilkan dengan mulus pada kanal `logbook`.
- [x] Tidak ada perubahan atau penambahan CSS/JS pada file Blade.
- [x] Seluruh unit & feature test terkait lulus 100% tanpa regresi.
- [x] Pemeriksaan Pint (`./vendor/bin/pint --test`) bersih tanpa pelanggaran kode gaya.
