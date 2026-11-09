# TASK BRIEF SENIOR DEVELOPER: IMPLEMENTASI PAGINASI (10 DATA PER HALAMAN) PADA TABEL CADANGAN BASIS DATA SUPERADMIN

> **Dari:** Senior Developer / Lead Software Architect  
> **Kepada:** Junior Developer / AI Agent (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Status:** COMPLETED & VERIFIED 100% (PAGINATION IMPLEMENTED & TESTED)  
> **Tingkat Kesulitan:** Menengah-Rendah (LengthAwarePaginator, Collection Pagination, Blade Template Links, Test Verification)  
> **Referensi Desain (Screenshot User):** `media_1794210517118_2b0054d5.png`  
> **Template Bawaan Proyek:** [`resources/views/vendor/pagination/default.blade.php`](file:///g:/MAGKOM/resources/views/vendor/pagination/default.blade.php)  
> **Dokumentasi Terkait:** `AGENTS.md`, `resources/css/components/pagination.css`

---

## 1. 🔍 LATAR BELAKANG & KEBUTUHAN PENGGUNA (USER REQUIREMENT)

### 1.1 Kebutuhan Nyata Berdasarkan Tangkapan Layar
Berdasarkan tangkapan layar yang diunggah oleh pengguna (`media_1794210517118_2b0054d5.png`), pada bagian bawah tabel daftar arsip di halaman **Pusat Cadangan & Pemulihan Basis Data** ([`superadmin/backup/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/backup/index.blade.php)), dibutuhkan **bilah navigasi paginasi (Pagination Bar)** dengan spesifikasi visual dan fungsional:
1. **Batas Maksimal Tampilan Per Halaman**: Menampilkan maksimal **10 baris data** per halaman (`per_page = 10`).
2. **Penomoran Halaman Dinamis**: Jika data melebihi 10, pengguna dapat berpindah halaman (`1, 2, 3, 4 ...`) dengan tombol:
   - `« Sebelumnya` (aktif/nonaktif secara dinamis jika di halaman pertama).
   - Angka nomor halaman dengan status aktif (kotak biru tua `#01347C` dengan teks putih).
   - `Berikutnya »` (aktif/nonaktif secara dinamis jika di halaman terakhir).
3. **Teks Rangkuman Data (Sisi Kiri)**:
   - Format: `Menampilkan 1 sampai 10 dari 27 entri` (angka dicetak tebal `<strong>`, nilai dinamis sesuai jumlah data aktual).

---

### 1.2 Analisis Arsitektural: Paginasi Berkas Non-Eloquent di Laravel
Berbeda dengan data database biasa yang menggunakan query Eloquent (`Model::paginate(10)`), data cadangan basis data dibaca langsung dari sistem berkas fisik (`storage/app/backups/`).

Oleh karena itu, standar industri Laravel untuk mempaginasi data berbasis `Collection` atau `Array` adalah menggunakan **`Illuminate\Pagination\LengthAwarePaginator`**:
```php
$page = (int) ($page ?? request()->input('page', 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$paginatedItems = $allFiles->slice($offset, $perPage)->values();

$paginator = new LengthAwarePaginator(
    items: $paginatedItems,
    total: $allFiles->count(),
    perPage: $perPage,
    currentPage: $page,
    options: [
        'path' => request()->url(),
        'query' => request()->query(),
    ]
);
```

### 1.3 Penyesuaian Batas Retensi Cadangan (Retention Limit)
- Sebelumnya, konstanta `MAX_BACKUPS_RETAINED` pada `BackupDatabaseService` dibatasi hanya `10` file.
- Jika retensi hanya 10 file, maka jumlah data tidak akan pernah mencapai halaman ke-2.
- Agar paginasi dapat berfungsi secara nyata dan memungkinkan pengguna melihat halaman 1, 2, 3 (sebagaimana contoh di screenshot yang memiliki 27 entri), batas retensi arsip cadangan disesuaikan dari **10** menjadi **30 arsip terbaru**.

---

## 2. 🏛️ BLUEPRINT ANTARMUKA (UI BLUEPRINT)

Susunan panel tabel di [`resources/views/superadmin/backup/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/backup/index.blade.php):

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [X-PANEL TABLE: <x-gentelella.x-panel class="has-table">]                              │
│  Daftar Snapshot Cadangan Basis Data                                      [^ Collapse] │
│ ┌────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ NAMA BERKAS SNAPSHOT │ FORMAT │ WAKTU PEMBUATAN │ UKURAN │ CHECKSUM SHA-256 │ AKSI │ │
│ ├──────────────────────┼────────┼─────────────────┼────────┼──────────────────┼──────┤ │
│ │ backup-xxx-10.sql.gz │ [Gzip] │ 09 Nov 2026 ... │ 1.8 MB │ 4136...371a      │ [📥] │ │
│ │ ... (Baris 1 s/d 10) │ ...    │ ...             │ ...    │ ...              │ ...  │ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ [PAGINATION FOOTER: resources/views/vendor/pagination/default.blade.php]               │
│                                                                                        │
│  Menampilkan 1 sampai 10 dari 27 entri                 [« Sebelumnya] [1] [2] [3] [>]  │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. 🗺️ SPESIFIKASI FILE YANG DIBUAT / DIUBAH (FILE CONTRACT)

| No | Path Berkas | Aksi | Tanggung Jawab & Spesifikasi Perubahan |
|:---:|---|:---:|---|
| 1 | [`app/Services/BackupDatabaseService.php`](file:///g:/MAGKOM/app/Services/BackupDatabaseService.php) | **MODIFY** | 1. Naikkan `MAX_BACKUPS_RETAINED` dari `10` menjadi `30`.<br>2. Tambahkan metode `getPaginatedBackups(int $perPage = 10, ?int $page = null): LengthAwarePaginator`. |
| 2 | [`app/Http/Controllers/SuperAdmin/BackupController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/BackupController.php) | **MODIFY** | Pada method `index()`, panggil `getPaginatedBackups(10, $request->input('page'))` dan teruskan instance paginator ke view. |
| 3 | [`resources/views/superadmin/backup/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/backup/index.blade.php) | **MODIFY** | Tepat di bawah penutup tag `</table>`, tambahkan kontainer footer paginasi yang merender `{{ $backupFiles->links() }}`. |
| 4 | [`tests/Feature/SuperAdmin/BackupDatabaseTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdmin/BackupDatabaseTest.php) | **MODIFY** | Tambahkan skenario pengujian paginasi (verifikasi halaman 1, halaman 2, dan query parameter `?page=2`). |

---

## 4. 📝 DETAIL KODE IMPLEMENTASI UNTUK JUNIOR DEVELOPER

### 4.1 Modifikasi `app/Services/BackupDatabaseService.php`
Tambahkan import `Illuminate\Pagination\LengthAwarePaginator`:
```php
use Illuminate\Pagination\LengthAwarePaginator;

// Ubah batas retensi dari 10 menjadi 30 arsip
public const MAX_BACKUPS_RETAINED = 30;

/**
 * Mendapatkan daftar berkas cadangan dalam bentuk paginasi (LengthAwarePaginator).
 */
public function getPaginatedBackups(int $perPage = 10, ?int $page = null): LengthAwarePaginator
{
    $allBackups = $this->listBackups();
    $currentPage = max(1, (int) ($page ?? request()->input('page', 1)));
    $offset = ($currentPage - 1) * $perPage;

    $itemsForCurrentPage = $allBackups->slice($offset, $perPage)->values();

    return new LengthAwarePaginator(
        items: $itemsForCurrentPage,
        total: $allBackups->count(),
        perPage: $perPage,
        currentPage: $currentPage,
        options: [
            'path' => request()->url(),
            'query' => request()->query(),
        ]
    );
}
```

---

### 4.2 Modifikasi `app/Http/Controllers/SuperAdmin/BackupController.php`
Pada method `index()`:
```php
public function index(Request $request): View
{
    $page = $request->has('page') ? (int) $request->input('page') : 1;
    $backupFiles = $this->backupService->getPaginatedBackups(perPage: 10, page: $page);
    $diskTelemetry = $this->backupService->getDiskTelemetry();

    return view('superadmin.backup.index', [
        'backupFiles' => $backupFiles,
        'totalBackups' => $diskTelemetry['total_backups'],
        'totalSizeMb' => $diskTelemetry['total_size_mb'],
        'diskTelemetry' => $diskTelemetry,
    ]);
}
```

---

### 4.3 Modifikasi `resources/views/superadmin/backup/index.blade.php`
Di dalam `<x-gentelella.x-panel class="has-table" ...>`, tepat setelah penutup tag `</div>` dari tabel, tambahkan:
```blade
            {{-- BILAH PAGINASI SESUAI STANDAR DESAIN (Menampilkan 1-10 dari X entri) --}}
            @if($backupFiles->total() > 0)
                <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-0);">
                    {{ $backupFiles->links() }}
                </div>
            @endif
```
*Catatan Arsitek:* Karena template `resources/views/vendor/pagination/default.blade.php` sudah terpasang di proyek, pemanggilan `{{ $backupFiles->links() }}` otomatis menghasilkan:
- Teks kiri: `Menampilkan 1 sampai 10 dari X entri`.
- Tombol kanan: `« Sebelumnya`, nomor halaman `1, 2, 3`, dan `Berikutnya »`.

---

## 5. 🚀 PANDUAN EKSEKUSI TAHAP DEMI TAHAP (STEP-BY-STEP CHECKLIST)

- [x] **Langkah 1**: Perbarui `app/Services/BackupDatabaseService.php` (ubah `MAX_BACKUPS_RETAINED = 30` dan tambahkan metode `getPaginatedBackups()`).
- [x] **Langkah 2**: Perbarui `app/Http/Controllers/SuperAdmin/BackupController.php` pada method `index()` untuk menggunakan `getPaginatedBackups(10, ...)`.
- [x] **Langkah 3**: Perbarui `resources/views/superadmin/backup/index.blade.php` dengan menambahkan `{{ $backupFiles->links() }}` di bawah tabel.
- [x] **Langkah 4**: Tambahkan test case di `tests/Feature/SuperAdmin/BackupDatabaseTest.php` untuk memverifikasi paginasi.
- [x] **Langkah 5**: Jalankan test suite PHP 8.3:
  ```powershell
  & "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=BackupDatabaseTest
  ```
- [x] **Langkah 6**: Jalankan formatting Pint:
  ```powershell
  & "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/pint --test
  ```

---

## 6. ✅ KRITERIA SELESAI (DEFINITION OF DONE - DOD)
1. [x] Tabel Cadangan Basis Data menampilkan maksimal 10 data per halaman secara default.
2. [x] Bilah navigasi paginasi muncul di bagian bawah tabel menampilkan ringkasan: `Menampilkan X sampai Y dari Z entri`.
3. [x] Tombol halaman `« Sebelumnya`, `1`, `2`, `3`, dan `Berikutnya »` dapat diklik dan mengarahkan ke halaman terkait (`?page=2`).
4. [x] Test suite otomatis `BackupDatabaseTest` lulus 100% tanpa regresi (9 passed, 36 assertions).

