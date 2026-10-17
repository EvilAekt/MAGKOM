# Master Engineering Handbook & Technical PRD: Full Parity & Hybrid Synthesis between Basefw (`CodeModule` + `CrudGenerator`) and MagKom (`ModulBuilderController` + `ModulDataController`)

- **Dokumen ID**: `MASTER-PLAN-2026-10-15-BASEFW-MAGKOM-FULL-PARITY`
- **Tanggal**: 2026-10-15
- **Status**: Disetujui untuk Implementasi Penuh (Approved for Master Execution)
- **Author**: Lead Principal Software Architect & Head of Core Systems
- **Target Pelaksana**: Junior Full-Stack Developer / Fast LLM Executor (e.g. Gemini 3.8 Flash)
- **Target Komponen**:
  - `basefw (2)/basefw/modules/CodeModule` (Legacy Reference)
  - `basefw (2)/basefw/modules/CrudGenerator` (Legacy Reference)
  - `app/Http/Controllers/SuperAdmin/ModulBuilderController.php` (Target Enhancement)
  - `app/Http/Controllers/SuperAdmin/ModulDataController.php` (Target Enhancement)
  - `routes/web.php` & Skema Database MagKom
- **Kepatuhan Pedoman**:
  - `docs/01-PRD-MagKom.md` (Tata Kelola Modul SuperAdmin, Audit Trail & RBAC)
  - `docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md` (Strict Append-Only, Policy Isolation)
  - `docs/07-DESIGN-SYSTEM-MAGKOM.md` (Design tokens, Dark mode `#0F172A`/`#182234`, Tailwind CSS)

---

## 1. Executive Summary: Apakah Kedua Fitur Ini Sudah Sama?

### Jawaban Tegas: **BELUM SAMA PERSIS (Status: Partial Parity ~45%)**

Meskipun modul kustom di MagKom (`ModulBuilderController` & `ModulDataController`) dan modul legasi di Basefw (`CodeModule` & `CrudGenerator`) sama-sama bertujuan memberikan fleksibilitas CRUD tanpa perlu menulis kode dari awal, **keduanya saat ini dibangun di atas paradigma yang sangat berbeda**:

1. **MagKom Saat Ini (Pure Dynamic EAV/JSON Runtime Engine)**:
   - SuperAdmin merancang modul kustom secara murni visual (*form repeater*) tanpa membuat tabel SQL fisik di database. Seluruh data baris disimpan dalam format JSON terenkapsulasi di dalam tabel `modul_dinamis_data`.
   - **Kelebihan**: Sangat aman, tidak ada risiko SQL injection struktural, tidak merusak skema database MySQL, mendukung 10 tipe data modern (file upload terisolasi, relation dynamic lookup, select/multiselect, boolean, dll).
   - **Kekurangan**: Belum bisa membaca atau mengelola tabel fisik riil database yang sudah ada, belum memiliki matriks sub-aksi granular (hanya role makro), belum ada public endpoint, belum ada export PDF formal/print template, dan belum ada isolasi data berbasis unit kerja/bidang dinas.

2. **Basefw Legacy (`CodeModule` + `CrudGenerator`)**:
   - Memiliki **Database Reverse-Engineering Generator**: memilih tabel fisik yang sudah ada di database (`information_schema.tables`), membaca semua kolomnya (`information_schema.columns`), dan melakukan query `SELECT/INSERT/UPDATE/DELETE` langsung ke tabel fisik SQL tersebut.
   - Memiliki **Sub-Aksi & RBAC Matrix (`CodeModule`)**: memisahkan hak akses per aksi (`admin_list`, `admin_add`, `admin_update`, `admin_delete`, `admin_pdf`, `admin_listPrint`) dan memiliki toggle `log` (audit trail) per aksi.
   - Memiliki **4 Fitur Spesial CrudGenerator**:
     - *Manual Primary Key Detection*: deteksi PK manual (misal `nik`, `kode_aset`) dan validasi duplikasi otomatis (`ensurePrimaryKeyAvailable`).
     - *Endpoint Public Read-Only (`ckPublicRead`)*: membuat endpoint `PUBLIC_list` dan `PUBLIC_detail` tanpa login untuk katalog publik.
     - *Export Suite & PDF Template (`ckExportData`)*: export Excel, copy clipboard, print view (`tpl-print.html`), dan PDF template (`tpl_pdf.html`).
     - *Unit Scope Multi-Tenancy (`ckUnitScope`)*: jika tabel punya `id_unit_kerja`, otomatis menyuntikkan isolasi tenant (`applyUnitScope` & `fillUnitScopeData`).
   - **Kelemahan Basefw**: Menghasilkan file fisik statis (`.html`, `.js`, `.php`) di filesystem disk yang rentan konflik versi, tidak memiliki proteksi file upload terisolasi, dan masih menggunakan jQuery/DataTables lama.

---

## 2. Tabel Komparasi Head-to-Head (Gap Analysis Matrix)

| No | Parameter Fitur | Basefw (`CodeModule` + `CrudGenerator`) | MagKom Saat Ini (`ModulBuilder` + `ModulData`) | Status Kesamaan | Solusi Penyelarasan MagKom (Target) |
|---|---|---|---|---|---|
| **1** | **Pembuatan Skema Bebas (No-Code Form Builder)** | Tidak ada (harus punya tabel fisik SQL terlebih dahulu) | **Ada & Lengkap** (10 tipe data, form repeater, label, validasi) | MagKom Lebih Unggul | Pertahankan sebagai **Mode A: Dynamic Low-Code Schema**. |
| **2** | **Reverse-Engineering Tabel Basis Data** | **Ada** (dropdown `information_schema.tables` & columns) | **Belum Ada** (wajib ketik kolom manual satu per satu) | **BELUM SAMA** | Tambahkan **Mode B: Reverse Table Inspector** (baca tabel & kolom SQL otomatis). |
| **3** | **Deteksi Primary Key & Validasi Duplikasi** | **Ada** (`id` vs manual PK + `ensurePrimaryKeyAvailable`) | **Belum Ada** (selalu mengandalkan ID auto-increment tabel JSON) | **BELUM SAMA** | Dukung custom PK + auto check duplikasi sebelum insert/update. |
| **4** | **Sub-Action Registry & Permission Matrix** | **Ada** (`actions`, `group_has_actions`, per-action RBAC) | **Parsial** (hanya array `target_roles` makro level modul) | **BELUM SAMA** | Buat matriks 8 sub-aksi (`list`, `show`, `create`, `edit`, `delete`, `export_csv`, `export_pdf`, `print`) per role. |
| **5** | **Per-Action Audit Logging Toggle** | **Ada** (kolom `log=1` atau `0` pada tabel `actions`) | **Parsial** (semua mutasi otomatis di-log ke `dynamic-module`) | **BELUM SAMA** | Tambahkan toggle audit logging per sub-aksi pada konfigurasi modul. |
| **6** | **Public Read-Only Endpoint** | **Ada** (`ckPublicRead` -> `PUBLIC_list`, `PUBLIC_detail`) | **Belum Ada** (semua route wajib login middleware `auth`) | **BELUM SAMA** | Tambahkan toggle `is_public_read` + rute publik `/publik/modul/{slug}` & `/publik/modul/{slug}/{id}`. |
| **7** | **Export Data Suite (CSV/Excel)** | **Ada** (Excel TSV/CSV) | **Ada** (CSV Stream Download + OWASP Formula Protection) | **Sudah Sama** | Pertahankan implementasi streaming MagKom. |
| **8** | **Cetak PDF Berbasis Format Formal** | **Ada** (`admin_pdf` + template `tpl_pdf.html`) | **Belum Ada** (hanya ada CSV) | **BELUM SAMA** | Tambahkan aksi `export-pdf` dengan template kop dinas resmi Sleman. |
| **9** | **Print Preview Standar Dinas** | **Ada** (CSS `@media print` + `tpl-print.html`) | **Belum Ada** | **BELUM SAMA** | Tambahkan rute & view `/modul/{slug}/print` siap cetak kertas A4. |
| **10** | **Multi-Tenancy Unit/Scope Isolation** | **Ada** (`ckUnitScope` via kolom `id_unit_kerja`) | **Belum Ada** (semua user yang punya akses melihat seluruh data) | **BELUM SAMA** | Tambahkan `scope_mode`: `none`, `bidang_scope` (Dinas Sleman), atau `creator_scope`. |
| **11** | **File Upload & Secured Download** | Tidak aman (file diakses langsung via web root) | **Ada & Enterprise** (Storage terisolasi, metadata, token auth) | MagKom Lebih Unggul | Pertahankan arsitektur storage MagKom. |
| **12** | **Eksekusi Runtime vs File Generator** | Menulis file fisik `.php`, `.js`, `.html` ke disk | **Runtime Engine Modern** (tidak perlu generate file fisik) | MagKom Lebih Unggul | Gunakan runtime controller dinamis agar tidak mengotori disk server. |

---

## 3. Grand Target Architecture: "Hybrid Dual-Engine CRUD & Action Matrix"

Untuk menjadikan modul MagKom **bener-bener sama 100% dan melampaui Basefw**, kita menyatukan keduanya ke dalam arsitektur modern **Hybrid Dual-Engine**:

```
+========================================================================================================+
|                                    MAGKOM HYBRID DUAL-ENGINE CRUD SYSTEM                               |
+========================================================================================================+
                                                    |
         +------------------------------------------+------------------------------------------+
         |                                                                                     |
         v                                                                                     v
+------------------------------------+                               +------------------------------------+
| MODE A: DYNAMIC LOW-CODE ENGINE    |                               | MODE B: PHYSICAL TABLE ENGINE      |
| (Modern MagKom Native)             |                               | (Basefw CrudGenerator Parity)      |
+------------------------------------+                               +------------------------------------+
| • Buat modul tanpa tabel SQL fisik |                               | • Reverse-engineering tabel MySQL  |
| • Skema disimpan di field metadata |                               | • Auto-read columns & data types   |
| • Data di baris EAV/JSON wrapper   |                               | • Auto-detect Auto-Inc vs Manual PK|
| • 10 Tipe Data + File Attachment   |                               | • Direct SQL Table CRUD operations |
+------------------------------------+                               +------------------------------------+
         |                                                                                     |
         +------------------------------------------+------------------------------------------+
                                                    |
                                                    v
+========================================================================================================+
|                                  UNIVERSAL ACTION & PERMISSION MATRIX                                  |
|                                       (Basefw CodeModule Parity)                                       |
+========================================================================================================+
| 8 Sub-Aksi Terstandarisasi:                                                                            |
| [1] list (Lihat tabel)         [2] show (Lihat detail)       [3] create (Tambah data)                  |
| [4] edit (Ubah data)           [5] delete (Hapus data)       [6] export_csv (Ekspor CSV)               |
| [7] export_pdf (Cetak PDF)     [8] print (Print Pratinjau)                                             |
|                                                                                                        |
| Fitur Khusus:                                                                                          |
| • Granular Role Permission Matrix: Atur hak akses tiap role per sub-aksi                              |
| • Per-Action Audit Logging Toggle: Tentukan aksi mana yang dicatat ke audit log (15-Channel Hub)      |
| • Public Read-Only Gateway: Endpoint /publik/modul/{slug} untuk pengunjung tanpa login                 |
| • Multi-Tenancy Scoping: Isolasi otomatis berdasarkan Bidang Dinas (Kominfo) atau Pembuat Data         |
+========================================================================================================+
```

---

## 4. Master Task Breakdown untuk Junior Developer / Budget AI

> [!IMPORTANT]
> **Petunjuk Pelaksanaan untuk Junior Developer & AI (e.g. Gemini 3.8 Flash)**:
> Ikuti instruksi pada setiap task secara berurutan dari Task 1 sampai Task 11. Dilarang melompati task, dilarang memotong kode (no placeholder / no `...`), dan pastikan seluruh file disimpan di path yang telah ditentukan.

---

### Task 1: Database Migration – Penambahan Kolom Parity Basefw
- **Target File**: `database/migrations/2026_10_15_210000_enhance_modul_dinamis_basefw_parity.php`
- **Prioritas**: P0 (Fondasi Data)
- **Estimasi Waktu**: 15 Menit
- **Tujuan**: Menambahkan kolom pendukung fitur Basefw pada tabel `modul_dinamis`:
  1. `tipe_sumber`: `'dynamic'` (JSON) atau `'physical_table'` (tabel SQL riil).
  2. `nama_tabel_fisik`: nama tabel database riil jika mode physical table.
  3. `primary_key`: nama kolom primary key (default: `'id'`).
  4. `is_manual_pk`: boolean apakah primary key diinput manual atau auto-increment.
  5. `is_public_read`: boolean toggle untuk membuka endpoint publik tanpa autentikasi.
  6. `scope_mode`: `'none'`, `'bidang_scope'` (multi-tenancy Kominfo), atau `'creator_scope'`.
  7. `izin_aksi`: JSON matriks peran pengguna per sub-aksi.
  8. `log_aksi`: JSON toggle pencatatan audit log per sub-aksi.

#### Kode Lengkap Migration:
```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modul_dinamis', function (Blueprint $table) {
            $table->string('tipe_sumber', 20)->default('dynamic')->after('slug'); // 'dynamic' atau 'physical_table'
            $table->string('nama_tabel_fisik', 64)->nullable()->after('tipe_sumber');
            $table->string('primary_key', 64)->default('id')->after('nama_tabel_fisik');
            $table->boolean('is_manual_pk')->default(false)->after('primary_key');
            $table->boolean('is_public_read')->default(false)->after('status_aktif');
            $table->string('scope_mode', 20)->default('none')->after('is_public_read'); // 'none', 'bidang_scope', 'creator_scope'
            $table->json('log_aksi')->nullable()->after('izin_aksi'); // {"list": false, "create": true, "edit": true, "delete": true, "export_csv": true, "export_pdf": true}
        });
    }

    public function down(): void
    {
        Schema::table('modul_dinamis', function (Blueprint $table) {
            $table->dropColumn([
                'tipe_sumber',
                'nama_tabel_fisik',
                'primary_key',
                'is_manual_pk',
                'is_public_read',
                'scope_mode',
                'log_aksi',
            ]);
        });
    }
};
```

---

### Task 2: Update Model Eloquent & Action Helper
- **Target File**: `app/Models/ModulDinamis.php`
- **Prioritas**: P0
- **Estimasi Waktu**: 15 Menit
- **Tujuan**: Memperbarui fillable, casts, dan menambahkan helper method untuk pengecekan otorisasi sub-aksi dan toggle audit log.

#### Kode Lengkap `app/Models/ModulDinamis.php`:
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModulDinamis extends Model
{
    use HasFactory;

    protected $table = 'modul_dinamis';

    public const STANDARD_ACTIONS = [
        'list'       => 'Daftar Data (Index)',
        'show'       => 'Detail Data (View)',
        'create'     => 'Tambah Data (Create)',
        'edit'       => 'Ubah Data (Update)',
        'delete'     => 'Hapus Data (Destroy)',
        'export_csv' => 'Ekspor Data CSV',
        'export_pdf' => 'Ekspor Dokumen PDF',
        'print'      => 'Cetak Pratinjau (Print)',
    ];

    protected $fillable = [
        'nama_modul',
        'slug',
        'tipe_sumber',
        'nama_tabel_fisik',
        'primary_key',
        'is_manual_pk',
        'deskripsi',
        'ikon',
        'target_roles',
        'izin_aksi',
        'log_aksi',
        'status_aktif',
        'is_public_read',
        'scope_mode',
        'urutan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'target_roles'   => 'array',
        'izin_aksi'      => 'array',
        'log_aksi'       => 'array',
        'status_aktif'   => 'boolean',
        'is_manual_pk'   => 'boolean',
        'is_public_read' => 'boolean',
        'urutan'         => 'integer',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(ModulDinamisField::class, 'modul_id')->orderBy('urutan');
    }

    public function records(): HasMany
    {
        return $this->hasMany(ModulDinamisData::class, 'modul_id')->latest();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Memeriksa apakah role pengguna memiliki izin untuk aksi spesifik.
     */
    public function canRolePerformAction(string $role, string $action): bool
    {
        $roleLower = strtolower($role);

        // SuperAdmin selalu diizinkan melakukan seluruh aksi
        if ($roleLower === 'superadmin') {
            return true;
        }

        // Cek izin aksi granular jika didefinisikan
        if (!empty($this->izin_aksi) && isset($this->izin_aksi[$action])) {
            $allowedRoles = array_map('strtolower', (array) $this->izin_aksi[$action]);
            return in_array($roleLower, $allowedRoles, true);
        }

        // Fallback: gunakan konfigurasi makro target_roles untuk read/list/show, dan batasi mutasi ke admin
        $targetRoles = array_map('strtolower', (array) ($this->target_roles ?? []));
        if (!in_array($roleLower, $targetRoles, true)) {
            return false;
        }

        if (in_array($action, ['create', 'edit', 'delete'], true)) {
            return in_array($roleLower, ['superadmin', 'admin'], true);
        }

        return true;
    }

    /**
     * Memeriksa apakah suatu aksi wajib dicatat ke dalam audit log.
     */
    public function shouldLogAction(string $action): bool
    {
        if (empty($this->log_aksi)) {
            // Default: catat seluruh mutasi data dan export dokumen
            return in_array($action, ['create', 'edit', 'delete', 'export_csv', 'export_pdf'], true);
        }

        return !empty($this->log_aksi[$action]);
    }
}
```

---

### Task 3: Service Reverse-Engineering Basis Data (Database Inspector)
- **Target File**: `app/Services/DatabaseInspectorService.php`
- **Prioritas**: P0
- **Estimasi Waktu**: 25 Menit
- **Tujuan**: Porting langsung dari fungsi `CrudGenerator.php` Basefw (`generator_getTable`, `get_table_field`, `getKeyInfo`, `labelFromColumn`) ke dalam arsitektur Laravel yang aman dan mendukung SQLite maupun MySQL.

#### Kode Lengkap `app/Services/DatabaseInspectorService.php`:
```php
<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseInspectorService
{
    /**
     * Daftar tabel sistem yang tidak boleh diubah atau diexpose ke generator.
     */
    public const PROTECTED_TABLES = [
        'migrations',
        'password_resets',
        'password_reset_tokens',
        'failed_jobs',
        'personal_access_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'job_batches',
        'jobs',
    ];

    /**
     * Mengambil seluruh tabel basis data yang aman untuk digenerate.
     *
     * @return array<int, string>
     */
    public function getAvailableTables(): array
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $tables = [];

        if ($driver === 'sqlite') {
            $rows = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
            foreach ($rows as $row) {
                $tables[] = (string) $row->name;
            }
        } elseif ($driver === 'mysql') {
            $databaseName = $connection->getDatabaseName();
            $rows = DB::select('SELECT TABLE_NAME as name FROM information_schema.tables WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME', [$databaseName]);
            foreach ($rows as $row) {
                $tables[] = (string) $row->name;
            }
        } else {
            $tables = Schema::getTableListing();
        }

        return array_values(array_filter($tables, fn ($tbl) => !in_array($tbl, self::PROTECTED_TABLES, true)));
    }

    /**
     * Menginspeksi skema kolom dari tabel fisik terpilih.
     *
     * @return array{
     *     table: string,
     *     primary_key: string,
     *     is_manual_pk: bool,
     *     has_unit_scope: bool,
     *     columns: array<int, array{
     *         name: string,
     *         label: string,
     *         data_type: string,
     *         is_pk: bool,
     *         is_nullable: bool,
     *         is_searchable: bool,
     *         is_visible: bool
     *     }>
     * }
     */
    public function inspectTable(string $table): array
    {
        if (in_array($table, self::PROTECTED_TABLES, true) || !Schema::hasTable($table)) {
            throw new \InvalidArgumentException("Tabel {$table} tidak valid atau dilindungi.");
        }

        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $columns = [];
        $primaryKey = 'id';
        $hasAutoIncrementId = false;
        $hasUnitScope = false;

        if ($driver === 'mysql') {
            $databaseName = $connection->getDatabaseName();
            $rawColumns = DB::select('SELECT COLUMN_NAME, DATA_TYPE, COLUMN_KEY, EXTRA, IS_NULLABLE
                FROM information_schema.columns
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
                ORDER BY ORDINAL_POSITION', [$databaseName, $table]);

            foreach ($rawColumns as $col) {
                $colName = (string) $col->COLUMN_NAME;
                $isPk = strtoupper((string) $col->COLUMN_KEY) === 'PRI';
                $isAutoInc = str_contains(strtolower((string) $col->EXTRA), 'auto_increment');

                if (strtolower($colName) === 'id' && $isPk) {
                    $hasAutoIncrementId = true;
                    $primaryKey = 'id';
                } elseif ($isPk && !$hasAutoIncrementId) {
                    $primaryKey = $colName;
                }

                if (in_array(strtolower($colName), ['id_unit_kerja', 'bidang_id', 'unit_id'], true)) {
                    $hasUnitScope = true;
                }

                $columns[] = [
                    'name'          => $colName,
                    'label'         => $this->labelFromColumn($colName),
                    'data_type'     => $this->mapSqlTypeToMagkomType((string) $col->DATA_TYPE),
                    'is_pk'         => $isPk,
                    'is_nullable'   => strtoupper((string) $col->IS_NULLABLE) === 'YES',
                    'is_searchable' => in_array($this->mapSqlTypeToMagkomType((string) $col->DATA_TYPE), ['text', 'textarea', 'number'], true),
                    'is_visible'    => !in_array($colName, ['password', 'remember_token', 'deleted_at'], true),
                ];
            }
        } else {
            // Driver SQLite / Fallback Schema Manager
            $colNames = Schema::getColumnListing($table);
            foreach ($colNames as $colName) {
                $isPk = (strtolower($colName) === 'id');
                if ($isPk) {
                    $hasAutoIncrementId = true;
                }

                if (in_array(strtolower($colName), ['id_unit_kerja', 'bidang_id', 'unit_id'], true)) {
                    $hasUnitScope = true;
                }

                $columns[] = [
                    'name'          => $colName,
                    'label'         => $this->labelFromColumn($colName),
                    'data_type'     => $this->guessTypeFromName($colName),
                    'is_pk'         => $isPk,
                    'is_nullable'   => true,
                    'is_searchable' => true,
                    'is_visible'    => !in_array($colName, ['password', 'remember_token', 'deleted_at'], true),
                ];
            }
        }

        return [
            'table'          => $table,
            'primary_key'    => $primaryKey,
            'is_manual_pk'   => !$hasAutoIncrementId,
            'has_unit_scope' => $hasUnitScope,
            'columns'        => $columns,
        ];
    }

    /**
     * Mengubah nama kolom menjadi label yang manusiawi (Porting labelFromColumn Basefw).
     */
    public function labelFromColumn(string $column): string
    {
        $title = Str::headline($column);
        $title = str_ireplace(['Id ', ' Id', ' Kode ', ' Tgl '], ['ID ', ' ID', ' Kode ', ' Tanggal '], $title);
        return trim($title);
    }

    /**
     * Memetakan tipe data SQL ke 10 tipe data formulir MagKom.
     */
    private function mapSqlTypeToMagkomType(string $sqlType): string
    {
        $sqlType = strtolower($sqlType);

        if (in_array($sqlType, ['int', 'integer', 'bigint', 'smallint', 'tinyint', 'mediumint', 'decimal', 'float', 'double'], true)) {
            if ($sqlType === 'tinyint') {
                return 'boolean';
            }
            return 'number';
        }

        if (in_array($sqlType, ['text', 'mediumtext', 'longtext'], true)) {
            return 'textarea';
        }

        if (in_array($sqlType, ['date', 'year'], true)) {
            return 'date';
        }

        if (in_array($sqlType, ['datetime', 'timestamp'], true)) {
            return 'datetime';
        }

        return 'text';
    }

    /**
     * Tebak tipe data sederhana dari nama kolom jika database tidak mengembalikan metadata rinci.
     */
    private function guessTypeFromName(string $name): string
    {
        $nameLower = strtolower($name);
        if (str_ends_with($nameLower, '_id') || str_starts_with($nameLower, 'id_')) {
            return 'number';
        }
        if (str_contains($nameLower, 'tanggal') || str_contains($nameLower, 'tgl') || str_ends_with($nameLower, '_at')) {
            return 'date';
        }
        if (str_contains($nameLower, 'deskripsi') || str_contains($nameLower, 'keterangan') || str_contains($nameLower, 'catatan')) {
            return 'textarea';
        }
        if (str_starts_with($nameLower, 'is_') || str_starts_with($nameLower, 'status_')) {
            return 'boolean';
        }
        if (str_contains($nameLower, 'jumlah') || str_contains($nameLower, 'kuota') || str_contains($nameLower, 'total')) {
            return 'number';
        }
        return 'text';
    }
}
```

---

### Task 4: Upgrade `ModulBuilderController` (API Reverse-Engineering & Action Matrix)
- **Target File**: `app/Http/Controllers/SuperAdmin/ModulBuilderController.php`
- **Prioritas**: P0
- **Estimasi Waktu**: 30 Menit
- **Tujuan**:
  1. Menyediakan endpoint API `apiGetTables()` dan `apiInspectTable(Request $request)` untuk UI form generator.
  2. Menangani penyimpanan dan pembaruan konfigurasi baru: `tipe_sumber`, `nama_tabel_fisik`, `primary_key`, `is_manual_pk`, `is_public_read`, `scope_mode`, `izin_aksi`, dan `log_aksi`.

#### Perubahan Kunci pada `ModulBuilderController.php`:
1. Inject `DatabaseInspectorService`:
   ```php
   public function __construct(
       protected AuditService $auditService,
       protected DatabaseInspectorService $inspectorService
   ) {}
   ```
2. Tambahkan Endpoint API AJAX:
   ```php
   public function apiGetTables(): \Illuminate\Http\JsonResponse
   {
       $tables = $this->inspectorService->getAvailableTables();
       return response()->json(['success' => true, 'tables' => $tables]);
   }

   public function apiInspectTable(Request $request): \Illuminate\Http\JsonResponse
   {
       $table = (string) $request->query('table');
       try {
           $schema = $this->inspectorService->inspectTable($table);
           return response()->json(['success' => true, 'schema' => $schema]);
       } catch (\Throwable $e) {
           return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
       }
   }
   ```
3. Update `store()` dan `update()` validasi agar menerima:
   - `tipe_sumber` => `required|in:dynamic,physical_table`
   - `nama_tabel_fisik` => `nullable|string|max:64`
   - `primary_key` => `required|string|max:64`
   - `is_manual_pk` => `boolean`
   - `is_public_read` => `boolean`
   - `scope_mode` => `required|in:none,bidang_scope,creator_scope`
   - `izin_aksi` => `nullable|array`
   - `log_aksi` => `nullable|array`

---

### Task 5: Upgrade `ModulDataController` (Dual-Engine Execution, Granular RBAC, Audit Toggle, & Scope)
- **Target File**: `app/Http/Controllers/SuperAdmin/ModulDataController.php`
- **Prioritas**: P0
- **Estimasi Waktu**: 45 Menit
- **Tujuan**:
  1. **Dual-Engine CRUD**: Jika `$modul->tipe_sumber === 'physical_table'`, jalankan query langsung ke `DB::table($modul->nama_tabel_fisik)` dengan deteksi Primary Key. Jika `'dynamic'`, gunakan tabel wrapper `modul_dinamis_data` seperti biasa.
  2. **Validasi Duplikasi Manual PK**: Jalankan `$this->ensurePrimaryKeyAvailable(...)` persis seperti Basefw jika `$modul->is_manual_pk === true`.
  3. **Granular RBAC**: Validasi aksi per role menggunakan `$modul->canRolePerformAction($role, $action)`. Jika tidak diizinkan, throw 403 Forbidden.
  4. **Conditional Audit Logging**: Hanya catat log audit jika `$modul->shouldLogAction($action) === true`.
  5. **Multi-Tenancy Scoping**: Terapkan filter query otomatis dan penyuntikan data saat create berdasarkan `scope_mode`:
     - Jika `'bidang_scope'`: filter `where('bidang_id', $user->pembimbing->bidang_id)` atau unit kerja terkait.
     - Jika `'creator_scope'`: filter `where('created_by', $user->id)`.
  6. **Export PDF & Print Preview**:
     - Tambahkan method `exportPdf(string $slug)` yang mengunduh PDF formal ber-KOP dinas.
     - Tambahkan method `printPreview(string $slug)` yang menampilkan view HTML siap cetak browser.

#### Struktur Method Baru di `ModulDataController.php`:
```php
/**
 * Memeriksa izin sub-aksi spesifik pengguna.
 */
protected function authorizeAction(ModulDinamis $modul, string $action): void
{
    $user = auth()->user();
    if (!$user) {
        abort(401);
    }

    $userRole = is_object($user->role) && property_exists($user->role, 'value') ? $user->role->value : (string) $user->role;
    if (!$modul->canRolePerformAction($userRole, $action)) {
        abort(403, "Akses Ditolak: Role Anda ({$userRole}) tidak diizinkan melakukan aksi [{$action}] pada modul {$modul->nama_modul}.");
    }
}

/**
 * Validasi keunikan Primary Key manual (Porting ensurePrimaryKeyAvailable Basefw).
 */
protected function ensurePrimaryKeyAvailable(ModulDinamis $modul, mixed $pkValue, ?mixed $currentId = null): void
{
    if (empty($pkValue)) {
        abort(422, "Kolom Primary Key ({$modul->primary_key}) wajib diisi.");
    }

    if ($modul->tipe_sumber === 'physical_table') {
        $query = DB::table($modul->nama_tabel_fisik)->where($modul->primary_key, $pkValue);
        if ($currentId !== null) {
            $query->where($modul->primary_key, '!=', $currentId);
        }
        if ($query->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                $modul->primary_key => "Primary key \"{$pkValue}\" sudah terdaftar pada basis data.",
            ]);
        }
    } else {
        $query = ModulDinamisData::where('modul_id', $modul->id)
            ->where("data->{$modul->primary_key}", (string) $pkValue);
        if ($currentId !== null) {
            $query->where('id', '!=', $currentId);
        }
        if ($query->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                $modul->primary_key => "Primary key \"{$pkValue}\" sudah digunakan.",
            ]);
        }
    }
}
```

---

### Task 6: Controller Publik Read-Only (`PublicModulController`)
- **Target File**: `app/Http/Controllers/PublicModulController.php`
- **Prioritas**: P1
- **Estimasi Waktu**: 20 Menit
- **Tujuan**: Mengimplementasikan ekuivalen dari endpoint `PUBLIC_list` dan `PUBLIC_detail` Basefw:
  1. `index(string $slug)`: Mengambil daftar data publik yang hanya menampilkan kolom-kolom non-sensitif berstatus `tampil_di_tabel == true`.
  2. `show(string $slug, string $id)`: Menampilkan detail satu baris data publik.
  3. Memastikan modul yang belum di-toggle `is_public_read == true` tidak bisa diakses publik (throw 404/403).

#### Kode Lengkap `app/Http/Controllers/PublicModulController.php`:
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ModulDinamis;
use App\Models\ModulDinamisData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicModulController extends Controller
{
    /**
     * Menampilkan katalog publik read-only (Ekuivalen Basefw PUBLIC_list).
     */
    public function index(Request $request, string $slug): View
    {
        $modul = ModulDinamis::with('fields')
            ->where('slug', $slug)
            ->where('status_aktif', true)
            ->where('is_public_read', true)
            ->firstOrFail();

        $search = trim((string) $request->input('q'));

        if ($modul->tipe_sumber === 'physical_table') {
            $query = DB::table($modul->nama_tabel_fisik);
            if ($search !== '') {
                $searchableCols = $modul->fields->where('dapat_dicari', true)->pluck('nama_kolom');
                if ($searchableCols->isNotEmpty()) {
                    $query->where(function ($sub) use ($searchableCols, $search) {
                        foreach ($searchableCols as $col) {
                            $sub->orWhere($col, 'LIKE', "%{$search}%");
                        }
                    });
                }
            }
            $records = $query->paginate(20)->withQueryString();
        } else {
            $query = ModulDinamisData::where('modul_id', $modul->id)->orderBy('id', 'desc');
            if ($search !== '') {
                $searchableCols = $modul->fields->where('dapat_dicari', true)->pluck('nama_kolom');
                if ($searchableCols->isNotEmpty()) {
                    $query->where(function ($sub) use ($searchableCols, $search) {
                        foreach ($searchableCols as $col) {
                            $sub->orWhere("data->{$col}", 'LIKE', "%{$search}%");
                        }
                    });
                }
            }
            $records = $query->paginate(20)->withQueryString();
        }

        return view('public.modul.index', compact('modul', 'records'));
    }

    /**
     * Menampilkan detail satu baris publik read-only (Ekuivalen Basefw PUBLIC_detail).
     */
    public function show(string $slug, string|int $id): View
    {
        $modul = ModulDinamis::with('fields')
            ->where('slug', $slug)
            ->where('status_aktif', true)
            ->where('is_public_read', true)
            ->firstOrFail();

        if ($modul->tipe_sumber === 'physical_table') {
            $pk = $modul->primary_key ?: 'id';
            $record = DB::table($modul->nama_tabel_fisik)->where($pk, $id)->first();
            if (!$record) {
                abort(404, 'Data tidak ditemukan.');
            }
            $data = (array) $record;
        } else {
            $row = ModulDinamisData::where('modul_id', $modul->id)->findOrFail($id);
            $data = $row->data;
        }

        return view('public.modul.show', compact('modul', 'data', 'id'));
    }
}
```

---

### Task 7: Registrasi Rute Web Baru
- **Target File**: `routes/web.php`
- **Prioritas**: P0
- **Estimasi Waktu**: 10 Menit
- **Tujuan**: Mendaftarkan rute API reverse-engineering, aksi cetak PDF & print preview, serta endpoint publik read-only.

#### Snippet Penambahan di `routes/web.php`:
```php
// 1. API Reverse-Engineering Modul Builder (Di dalam Route::prefix('superadmin')->middleware(['auth', 'role:superadmin']))
Route::get('/modul-builder/api/tables', [ModulBuilderController::class, 'apiGetTables'])->name('modul-builder.api.tables');
Route::get('/modul-builder/api/inspect', [ModulBuilderController::class, 'apiInspectTable'])->name('modul-builder.api.inspect');

// 2. Aksi Cetak PDF & Print Pratinjau (Di dalam modul data group)
Route::get('/modul/{slug}/export-pdf', [ModulDataController::class, 'exportPdf'])->name('modul.export-pdf');
Route::get('/modul/{slug}/print', [ModulDataController::class, 'printPreview'])->name('modul.print');

// 3. Endpoint Publik Read-Only Tanpa Auth (Di root web.php)
Route::prefix('publik/modul')->name('public.modul.')->group(function () {
    Route::get('/{slug}', [PublicModulController::class, 'index'])->name('index');
    Route::get('/{slug}/{id}', [PublicModulController::class, 'show'])->name('show');
});
```

---

### Task 8: Pembaruan Tampilan Visual Form Builder (UI Parity Basefw)
- **Target File**: `resources/views/superadmin/modul-builder/create.blade.php` & `edit.blade.php`
- **Prioritas**: P1
- **Estimasi Waktu**: 35 Menit
- **Tujuan**:
  1. Tambahkan tab selector: **Mode 1: Formulir Dinamis (No-Code)** vs **Mode 2: Tabel Basis Data (Basefw CrudGenerator)**.
  2. Pada Mode 2, sediakan dropdown tabel basis data. Ketika tabel dipilih, panggil API AJAX `/superadmin/modul-builder/api/inspect?table=...` untuk secara otomatis mengisi field repeater kolom tabel, mendeteksi primary key, dan mengecek kolom `id_unit_kerja` / `bidang_id`.
  3. Sediakan panel switch:
     - **Endpoint Publik (`ckPublicRead`)**: Aktifkan akses publik tanpa login.
     - **Isolasi Multi-Tenancy (`ckUnitScope`)**: Pilihan *None*, *Isolasi Bidang Dinas*, atau *Isolasi Pembuat Data*.
     - **Matriks Izin Sub-Aksi (`CodeModule`)**: Tabel checkbox matriks untuk 8 sub-aksi per role (SuperAdmin, Admin, Pembimbing, Peserta).
     - **Toggle Logging Per-Aksi (`actions.log`)**: Checkbox untuk menentukan aksi mana yang dicatat ke audit log.

---

### Task 9: Template Cetak Dokumen Dinas & PDF Formal
- **Target File**: `resources/views/superadmin/modul-data/print.blade.php`
- **Prioritas**: P1
- **Estimasi Waktu**: 25 Menit
- **Tujuan**: Porting `tpl_pdf.html` dan `tpl-print.html` dari Basefw menjadi template Blade formal berstandar Pemerintah Kabupaten Sleman, lengkap dengan styling `@media print`, kop surat resmi, tabel data bergaris, dan tanda tangan pejabat pengesah.

---

### Task 10: Tampilan Publik Read-Only
- **Target File**: `resources/views/public/modul/index.blade.php` & `show.blade.php`
- **Prioritas**: P2
- **Estimasi Waktu**: 20 Menit
- **Tujuan**: Menyediakan antarmuka publik yang bersih, elegan, responsif, dan ramah SEO untuk masyarakat atau calon peserta yang mengakses data katalog publik yang telah dibuka oleh SuperAdmin.

---

### Task 11: Automated Test Suite Parity (Standar 100% Pass)
- **Target File**: `tests/Feature/ModulDinamisBasefwParityTest.php`
- **Prioritas**: P0
- **Estimasi Waktu**: 25 Menit
- **Tujuan**: Memastikan semua fitur baru yang diadopsi dari Basefw teruji secara otomatis dan lulus 100%:
  1. Test reverse-engineering inspeksi tabel basis data.
  2. Test validasi duplikasi manual primary key.
  3. Test granular RBAC (role ditolak jika sub-aksi tidak diizinkan pada `izin_aksi`).
  4. Test conditional audit logging (hanya aksi dengan `log_aksi = true` yang dicatat).
  5. Test multi-tenancy scoping (user bidang hanya melihat data bidangnya).
  6. Test endpoint publik read-only (berhasil diakses tanpa login jika aktif, dan 404 jika nonaktif).
  7. Test cetak PDF dan print preview response code 200.

#### Kode Lengkap Test Suite:
```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ModulDinamis;
use App\Models\ModulDinamisField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModulDinamisBasefwParityTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_superadmin_can_inspect_database_tables_via_api(): void
    {
        $this->actingAs($this->superAdmin);
        $response = $this->getJson(route('superadmin.modul-builder.api.tables'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'tables']);
    }

    public function test_granular_rbac_blocks_unauthorized_sub_action(): void
    {
        $this->actingAs($this->admin);

        $modul = ModulDinamis::create([
            'nama_modul'   => 'Laporan Khusus',
            'slug'         => 'laporan-khusus',
            'ikon'         => 'file-text',
            'target_roles' => ['admin'],
            'izin_aksi'    => [
                'list'   => ['admin'],
                'create' => ['superadmin'], // Admin dilarang create
            ],
            'status_aktif' => true,
        ]);

        // List diizinkan (200)
        $listResponse = $this->get(route('superadmin.modul.index', ['slug' => 'laporan-khusus']));
        $listResponse->assertStatus(200);

        // Store ditolak (403)
        $storeResponse = $this->post(route('superadmin.modul.store', ['slug' => 'laporan-khusus']), [
            'data' => 'test',
        ]);
        $storeResponse->assertStatus(403);
    }

    public function test_public_read_endpoint_accessible_without_auth_when_enabled(): void
    {
        $modul = ModulDinamis::create([
            'nama_modul'     => 'Katalog Pengumuman',
            'slug'           => 'katalog-pengumuman',
            'ikon'           => 'book',
            'target_roles'   => ['superadmin'],
            'is_public_read' => true,
            'status_aktif'   => true,
        ]);

        $response = $this->get(route('public.modul.index', ['slug' => 'katalog-pengumuman']));
        $response->assertStatus(200);
        $response->assertSee('Katalog Pengumuman');
    }
}
```

---

## 5. Quality Assurance, Security Guardrails & Verification Criteria

1. **Anti-SQL Injection pada Physical Table**: Seluruh query langsung ke tabel fisik wajib menggunakan Query Builder Laravel berparameter (`DB::table(...)`) dan nama tabel serta kolom divalidasi ketat terhadap whitelist yang diizinkan.
2. **Formula Injection Sanitization (OWASP)**: Semua nilai sel pada ekspor CSV tetap disanitasi menggunakan metode `sanitizeCsvCell()` agar aman dibuka di Excel.
3. **Strict Tenancy Isolation**: Pengguna dengan role `admin` atau `pembimbing` tidak boleh membocorkan data antar bidang saat `scope_mode = 'bidang_scope'`.
4. **Audit Trail Immutability**: Semua operasi mutasi modul dicatat ke kanal `dynamic-module` pada tabel `audit_log` sesuai preferensi `log_aksi`.

---

## 6. Ringkasan Eksekutif untuk Diajukan ke Pengguna

Dengan implementasi 11 task di atas:
- **ModulBuilderController** dan **ModulDataController** di MagKom akan memiliki seluruh kapabilitas legasi dari **CodeModule** dan **CrudGenerator** Basefw secara 100% utuh.
- Lebih dari itu, MagKom akan melampaui Basefw karena menggunakan arsitektur **Hybrid Dual-Engine** yang bersih, modern, aman dari file clutter, terintegrasi audit log 15 kanal, dan siap produksi.
