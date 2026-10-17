# Master Engineering Handbook & Architecture PRD: SuperAdmin Full-Spectrum Multi-Channel Audit Logging (15 Channels), System Health Diagnostic Telemetry, and Dynamic Low-Code CRUD Module Engine

- **Dokumen ID**: `MASTER-PLAN-2026-10-15-SUPERADMIN-TRI-CORE-ARCHITECTURE`
- **Tanggal**: 2026-10-15
- **Status**: Disetujui untuk Implementasi Penuh (Approved for Master Execution)
- **Author**: Lead Principal Software Architect & Head of Security Engineering
- **Target Pelaksana**: Junior Full-Stack Developer / Fast LLM Executor (e.g. Gemini 3.8 Flash)
- **Target Lingkup**: Seluruh Sub-Sistem SuperAdmin MagKom (`/superadmin/*`) & Observabilitas Global
- **Kepatuhan Dokumen Inti MagKom**:
  - `docs/01-PRD-MagKom.md` (F-11 Manajemen Staf, F-12 Arsip, F-13 Audit Trail, NFR-A1 s/d NFR-A5)
  - `docs/02-TDD-MagKom.md` (Spesifikasi Pengujian Otomatis & Standar 100% Pass)
  - `docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md` (Penyempurnaan Arsitektur SuperAdmin Fase 4 & 5)
  - `docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md` (Tabel `audit_log`, `users`, integritas referensial)
  - `docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md` (Strict Append-Only, Policy Isolation, Sanitasi Data Sensitif)
  - `docs/06-ADDENDUM-KEAMANAN-DAN-QA.md` (OWASP Top 10, Anti-BOLA/IDOR, Anti-Mass-Assignment)
  - `docs/07-DESIGN-SYSTEM-MAGKOM.md` (Design tokens, Dark mode `#0F172A`/`#182234`, WCAG AAA/AA)

---

## 1. Grand Executive Summary & Strategic Architecture

Rencana kerja ini menyatukan 3 inovasi besar portal SuperAdmin ke dalam satu kesatuan arsitektur enterprise (**Tri-Core Architecture**):

```
+====================================================================================================+
|                                SUPERADMIN MASTER ARCHITECTURE ENGINE                               |
+====================================================================================================+
       |                                           |                                          |
       v                                           v                                          v
+-------------------------------+   +-------------------------------+   +----------------------------+
| CORE 1: MULTI-CHANNEL LOGS    |   | CORE 2: SYSTEM HEALTH HUB     |   | CORE 3: DYNAMIC BUILDER    |
| (15 Discord-Style Channels)   |   | (Proactive Telemetry Monitor) |   | (Low-Code Custom Modules)  |
+-------------------------------+   +-------------------------------+   +----------------------------+
| • 100% Full-Spectrum Capture  |   | • Live SLA 99.98% Uptime      |   | • Visual Modul Creator     |
| • 15 Kanal Terisolasi Spesifik|   | • Database Ping Latency (ms)  |   | • 10 Tipe Field + Relasi   |
| • Global Model Observer       |   | • Storage & Document Capacity |   | • Universal CRUD Engine    |
| • Terminable Audit Middleware |   | • RAM & Server Thread Tracker |   | • Auto Sidebar Injection   |
| • JSON Diff Inspector Modal   |   | • Queue & Failed Jobs Monitor |   | • Auto Audit Trail Sync    |
| • Counter & Unread Badges     |   | • One-Click Diagnostic Sweep  |   | • Granular RBAC Policy     |
| • Stream Export CSV/JSON      |   | • Cache & Storage Write-Test  |   | • CSV Data Export          |
+-------------------------------+   +-------------------------------+   +----------------------------+
```

### Masalah yang Diselesaikan:
1. **Audit Log Monolitik & Buta Aksi**: Seluruh log sistem sebelumnya ditumpuk dalam satu tabel tanpa pemisahan kanal. Insiden kritis seperti percobaan pembobolan sandi atau kegagalan transaksi tertimbun di antara ribuan log baca data biasa. Melalui **15 Kanal Log Terdedikasi**, setiap aksi memiliki kanalnya sendiri persis seperti server logging Discord.
2. **Kesehatan Sistem Pasif**: Telemetri sebelumnya tidak terpasang di sidebar dan bersifat pasif. Kini ditingkatkan menjadi **Pusat Diagnostik Proaktif** dengan pengukur latensi, grafik kapasitas, dan 4 tombol pemeliharaan otomatis.
3. **Keterbatasan Fleksibilitas Entitas Data**: Dinas sering membutuhkan modul tambahan (misal: *Inventaris Laptop Magang*, *Daftar MoU Kampus*, *Jadwal Evaluasi Akhir*). SuperAdmin kini dapat **menciptakan modul kustom apa pun secara instan dari dashboard** tanpa perlu koding ulang.

---

## 2. Core 1: Full-Spectrum 15-Channel Audit Logging Engine

### 2.1 Taksonomi 15 Kanal Log Terdedikasi (Zero Blind Spots)

Setiap peristiwa di dalam sistem MagKom (baik aksi manual pengguna maupun tugas latar belakang otomatis) langsung disalurkan ke kanal yang sesuai:

```
Logging Hub v (+)
├── # 🌐 all-actions-stream        (Master Feed gabungan semua aktivitas real-time)
│
├── [1] AKUN & OTENTIKASI
│   ├── # 🔐 auth-session-log      (Login, Logout, Gagal Login, Lockout 5x, Reset Sandi, 2FA)
│   └── # 👥 user-management-log   (Pendaftaran staf, Ubah role/hak akses, Suspend/Aktivasi)
│
├── [2] SIKLUS PENDAFTARAN & VERIFIKASI
│   ├── # 📋 pendaftaran-log       (Pendaftaran mandiri/kelompok, Unggah berkas, Revisi)
│   ├── # 🔎 verifikasi-admin-log  (Setujui berkas, Tolak berkas, Masa sanggah, Waiting list)
│   ├── # 🏢 penempatan-mentor-log (Penetapan bidang, Pindah pembimbing, Input manual dinas)
│   └── # 📊 kuota-periode-log     (Perubahan kuota jurusan, Buka/Tutup bulan, Slot khusus)
│
├── [3] PROSES BIMBINGAN & KELULUSAN
│   ├── # 📝 tugas-classroom-log   (Buat tugas baru, Kumpul tugas peserta, Nilai tugas mentor)
│   ├── # 📅 logbook-presensi-log  (Jurnal harian, Submit rekap mingguan, ACC/Revisi mentor)
│   ├── # 🎓 penilaian-sk-log      (Input nilai rubrik 100 poin, Kunci nilai, Cetak SK & Sertifikat)
│   └── # 💬 survei-evaluasi-log   (Pengisian kuesioner kepuasan, Ekspor analitik survei)
│
└── [4] INFRASTRUKTUR & MODUL DINAMIS
    ├── # ⚙️ konfigurasi-master-log(Ubah parameter sistem, Master jurusan, Bidang, Rubrik)
    ├── # 💾 backup-database-log   (Snapshot basis data, Unduh cadangan, Hapus snapshot)
    ├── # 🛡️ security-alert-log    (Akses 403 terlarang, Token CSRF invalid, Security sweep)
    └── # ⚡ dynamic-module-log    (Seluruh aksi Create/Update/Delete pada modul dinamis kustom)
```

### 2.2 Arsitektur Perekaman Otomatis 100% (Zero-Manual Code)

Agar **semua aksi pasti kelihatan tanpa ada yang terlewat**, sistem menggunakan 3 lapis perekam otomatis (*Automatic Interceptors*):

1. **Global Model Observer (`AuditableObserver`)**:
   - Mendengarkan event `created`, `updated`, dan `deleted` pada seluruh model Eloquent.
   - Mengambil *Diff*: nilai lama vs nilai baru secara otomatis.
2. **Global HTTP Request Middleware (`AuditAccessMiddleware`)**:
   - Menangkap seluruh request mutasi dan download berkas sensitif, mencatat latency milidetik, IP, dan user-agent.
3. **Laravel Security Event Listeners**:
   - Menangkap login, logout, failed login, dan lockout.

---

## 3. Core 2: System Health & Diagnostic Telemetry Monitor

### 3.1 Komponen Telemetri Real-Time
1. **SLA Uptime Banner**: Indikator operasional instansi pemerintah (`● ALL SYSTEMS OPERATIONAL - 99.98% Uptime SLA`).
2. **Database Ping Latency Gauge**: Mengukur kecepatan query `SELECT 1` secara presisi dalam milidetik ($< 25$ ms = Hijau Prima, $25-100$ ms = Kuning Waspada, $> 100$ ms = Merah Bahaya).
3. **Storage & Disk Capacity Footprint**: Menghitung total dokumen pendaftaran magang (MB/GB), sisa kapasitas server, dan memeriksa izin tulis direktori (`storage/app` is writable).
4. **Runtime & Thread Telemetry**: Informasi versi PHP 8.2+, Laravel 10+, driver antrean (Queue driver), dan penghitung pekerjaan gagal (`failed_jobs`).

### 3.2 Empat Aksi Pemeliharaan Cepat (One-Click Diagnostic Suite)
- **`Aksi 1: Security Sweep`**: Memindai berkas-berkas lampiran yatim piatu (*orphaned files*) dan memvalidasi integritas hash dokumen.
- **`Aksi 2: Optimasi Basis Data`**: Menjalankan perintah `VACUUM` (SQLite) atau `OPTIMIZE TABLE` (MySQL) untuk membersihkan fragmentasi tabel.
- **`Aksi 3: Bersihkan Cache Aplikasi`**: Membersihkan cache aplikasi, konfigurasi, rute, dan view blade dengan feedback instan.
- **`Aksi 4: Uji Izin Tulis Penyimpanan`**: Melakukan uji tulis-baca-hapus berkas dummy di storage untuk mencegah kegagalan upload peserta.

---

## 4. Core 3: Deep-Dive Dynamic Low-Code CRUD Module Engine

Fitur ini merupakan **modul terbesar dan tercanggih** di dalam sistem SuperAdmin. Dirancang sebagai *Internal Low-Code App Platform* khusus kebutuhan MagKom.

```
+====================================================================================================+
|                         DYNAMIC LOW-CODE CRUD MODULE ENGINE ARCHITECTURE                           |
+====================================================================================================+
  1. DEFINISI (SuperAdmin)         2. GENERASI (Backend Engine)         3. EKSEKUSI (Universal Runner)
  +-----------------------+        +--------------------------+        +---------------------------+
  | Form Visual Modul:    |        | Dynamic Validation Rules |        | Universal Index Table:    |
  | • Nama Modul & Slug   |        | • Text / Number / Email  |        | • Multi-Column Search     |
  | • Ikon SVG & Deskripsi| =====> | • Date / File / Select   | =====> | • Dynamic Column Sort     |
  | • Target Roles Access |        | • Relational Key Exists  |        | • Pagination & Export CSV |
  | • Dynamic Field List  |        +--------------------------+        +---------------------------+
  |   (10 Tipe Data)      |        | Dynamic Form Component   |        | Universal CRUD Modal:     |
  +-----------------------+        | • Auto Input Renderer    |        | • Auto-built Create Form  |
                                   | • Secure File Uploader   |        | • Pre-filled Edit Form    |
                                   | • Dynamic Sidebar Cache  |        | • Safe Confirm Delete     |
                                   +--------------------------+        +---------------------------+
```

---

### 4.1 Spesifikasi 10 Tipe Data Kolom (Field Data Types)

Setiap kolom yang ditambahkan SuperAdmin melalui antarmuka Form Builder mendukung salah satu dari 10 tipe data industri berikut:

| Tipe Data | Representasi Input HTML | Format Penyimpanan JSON | Aturan Validasi Laravel Otomatis | Contoh Penggunaan |
| :--- | :--- | :--- | :--- | :--- |
| **`text`** | `<input type="text">` | String biasa | `['string', 'max:255', $wajib ? 'required' : 'nullable']` | Nomor Seri Laptop, Judul Buku |
| **`number`** | `<input type="number" step="any">` | Float / Integer | `['numeric', $wajib ? 'required' : 'nullable']` | Jumlah Unit, Nilai Nominal, Bobot |
| **`textarea`**| `<textarea rows="3">` | Multi-line String | `['string', 'max:5000', $wajib ? 'required' : 'nullable']` | Deskripsi, Catatan Kondisi, Spesifikasi |
| **`select`** | `<select class="form-select">` | String (Opsi terpilih) | `['string', Rule::in($opsi), $wajib ? 'required' : 'nullable']` | Kondisi Fisik: `["Baik", "Rusak"]` |
| **`multiselect`**| `<select multiple>` | Array of Strings | `['array', $wajib ? 'required' : 'nullable']` | Kategori Keahlian, Tagging Fitur |
| **`date`** | `<input type="date">` | String format `Y-m-d` | `['date_format:Y-m-d', $wajib ? 'required' : 'nullable']` | Tanggal Serah Terima, Tanggal Expired |
| **`datetime`** | `<input type="datetime-local">` | String format `Y-m-d H:i`| `['date', $wajib ? 'required' : 'nullable']` | Waktu Seminar, Jadwal Wawancara |
| **`file`** | File Dropzone (`.pdf,.jpg,.png`)| Object JSON metadata | `['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120']` | Dokumen MoU Kampus, Foto Berita Acara |
| **`boolean`** | Switch Toggle `[ON / OFF]` | Boolean `true`/`false` | `['boolean']` | Status Tersedia, Konfirmasi Hadir |
| **`relation`**| Searchable Select Dropdown | Integer ID referensi | `['integer', 'exists:'.$tabel.',id', $wajib ? 'required' : 'nullable']` | Memilih Peserta Magang dari tabel `users` |

---

### 4.2 Relational Foreign Lookup (Relasi ke Tabel Internal MagKom)
Salah satu fitur enterprise yang membedakan engine ini adalah kemampuannya menghubungkan data modul kustom dengan data inti portal MagKom:
- Kolom bertipe **`relation`** memiliki atribut `relasi_tabel` (misal: `users`, `jurusan`, `bidang`) dan `relasi_kolom_tampil` (misal: `nama`, `nama_jurusan`, `nama_bidang`).
- Saat pengguna mengisi form pada modul kustom, sistem otomatis menampilkan dropdown pencarian yang berisi data asli dari database MagKom!
- Contoh: Pada modul *"Inventaris Laptop"*, kolom *"Penerima Laptop"* berelasi ke tabel `users` (hanya menampilkan peserta magang aktif).

---

### 4.3 Penanganan Unggah Berkas Aman (Secure File Upload Pipeline)
Untuk field bertipe `file`:
1. File diverifikasi di backend dengan whitelist ekstensi ketat: `pdf, jpg, jpeg, png, webp, docx, xlsx` (maksimal 5 MB).
2. Nama file di-hash menggunakan format: `storage/app/public/modul-dinamis/{slug}/{Y}/{m}/{uuid}_{filename}`.
3. Metadata berkas disimpan dalam JSON record:
   ```json
   {
       "path": "modul-dinamis/inventaris/2026/10/a1b2c3d4.pdf",
       "original_name": "Berita_Acara_Serah_Terima.pdf",
       "mime": "application/pdf",
       "size_bytes": 1048576
   }
   ```
4. Pengunduhan berkas dikontrol melalui rute terlindungi `superadmin.modul.download-file` yang memverifikasi hak akses role sebelum me-return stream file.

---

### 4.4 Injeksi Navigasi Sidebar Otomatis & Caching
- Modul dinamis yang berstatus aktif (`status_aktif = true`) otomatis muncul di Sidebar `resources/views/layouts/dashboard.blade.php` di bawah grup navigasi **"MODUL KUSTOM"**.
- Menggunakan **View Composer** dengan cache TTL 1 jam:
  ```php
  Cache::remember('dynamic_modules_sidebar', 3600, function () {
      return ModulDinamis::where('status_aktif', true)->orderBy('urutan')->get();
  });
  ```
- **Cache Invalidation**: Setiap kali ada penambahan, pengeditan, atau penghapusan modul pada `ModulBuilderController`, cache `dynamic_modules_sidebar` otomatis dibersihkan (`Cache::forget(...)`).

---

### 4.5 Kebijakan Keamanan & Otorisasi RBAC Granular
File: `app/Policies/ModulDinamisPolicy.php`

1. **Kelola Desain Modul (`ModulBuilder`)**:
   - `SuperAdmin`: Diizinkan 100% (Create, Edit Schema, Delete Module).
   - `Admin`, `Pembimbing`, `Peserta`: **Ditolak keras (HTTP 403)**.
2. **Akses Data Modul (`ModulData`)**:
   - Dicek dinamis terhadap kolom `target_roles` pada tabel `modul_dinamis`.
   - Jika modul diset untuk `["superadmin", "admin"]`, maka user dengan role `pembimbing` atau `peserta` akan menerima respons 403 Forbidden.

---

## 5. Skema Basis Data & Migrasi Lengkap

### 5.1 Migrasi 1: Kolom Channel & Severity pada `audit_log`
File: `database/migrations/2026_10_15_000001_add_channel_to_audit_log_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_log', 'channel')) {
                $table->string('channel', 40)->default('system-security')->after('tipe')->index();
            }
            if (!Schema::hasColumn('audit_log', 'severity')) {
                $table->string('severity', 20)->default('info')->after('channel')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {
            if (Schema::hasColumn('audit_log', 'severity')) {
                $table->dropColumn('severity');
            }
            if (Schema::hasColumn('audit_log', 'channel')) {
                $table->dropColumn('channel');
            }
        });
    }
};
```

---

### 5.2 Migrasi 2: Tabel Dynamic CRUD Module Engine (Ultra-Detailed)
File: `database/migrations/2026_10_15_000002_create_dynamic_modules_tables.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Definisi Modul Kustom
        Schema::create('modul_dinamis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_modul', 100);
            $table->string('slug', 100)->unique();
            $table->text('deskripsi')->nullable();
            $table->string('ikon', 50)->default('box'); // Nama ikon Lucide / SVG
            $table->json('target_roles'); // Array JSON: ["superadmin", "admin", "pembimbing"]
            $table->json('izin_aksi')->nullable(); // Granular: {"create":["admin"],"delete":["superadmin"]}
            $table->boolean('status_aktif')->default(true)->index();
            $table->unsignedInteger('urutan')->default(0);
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Skema Field / Kolom Modul Dinamis
        Schema::create('modul_dinamis_field', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('modul_dinamis')->cascadeOnDelete();
            $table->string('nama_kolom', 60); // snake_case unik per modul
            $table->string('label', 100);
            $table->string('tipe_data', 30); // text, number, textarea, select, multiselect, date, datetime, file, boolean, relation
            $table->json('opsi_pilihan')->nullable(); // Opsi array jika tipe select/multiselect
            $table->string('relasi_tabel', 60)->nullable(); // Tabel tujuan jika tipe relation
            $table->string('relasi_kolom_tampil', 60)->nullable(); // Kolom label tujuan
            $table->boolean('wajib')->default(false);
            $table->boolean('tampil_di_tabel')->default(true);
            $table->boolean('dapat_dicari')->default(true);
            $table->boolean('dapat_difilter')->default(false);
            $table->string('placeholder', 150)->nullable();
            $table->string('bantuan_teks', 255)->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->unique(['modul_id', 'nama_kolom']);
            $table->index(['modul_id', 'urutan']);
        });

        // 3. Penyimpanan Data Baris Modul (EAV/JSON Hybrid dengan Soft Deletes)
        Schema::create('modul_dinamis_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('modul_dinamis')->cascadeOnDelete();
            $table->json('data'); // Key-Value pair tersanitasi
            $table->json('files_metadata')->nullable(); // Info berkas yang diunggah
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['modul_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modul_dinamis_data');
        Schema::dropIfExists('modul_dinamis_field');
        Schema::dropIfExists('modul_dinamis');
    }
};
```

---

## 6. Blueprint Backend Lengkap (Controllers & Components)

### 6.1 `ModulBuilderController.php` (Manajer Skema Modul)
File: `app/Http/Controllers/SuperAdmin/ModulBuilderController.php`

```php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ModulDinamis;
use App\Models\ModulDinamisField;
use App\Services\AuditService;
use App\Enums\TipeAuditEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ModulBuilderController extends Controller
{
    public function __construct(protected AuditService $auditService) {}

    public function index(): View
    {
        $modules = ModulDinamis::withCount(['fields', 'records'])->orderBy('urutan')->get();
        return view('superadmin.modul-builder.index', compact('modules'));
    }

    public function create(): View
    {
        $availableIcons = ['box', 'laptop', 'shield', 'award', 'book', 'file-text', 'database', 'users', 'calendar', 'check-circle'];
        return view('superadmin.modul-builder.create', compact('availableIcons'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_modul'   => 'required|string|max:100',
            'slug'         => 'nullable|string|max:100|unique:modul_dinamis,slug',
            'deskripsi'    => 'nullable|string|max:1000',
            'ikon'         => 'required|string|max:50',
            'target_roles' => 'required|array|min:1',
            'fields'       => 'required|array|min:1',
            'fields.*.nama_kolom' => 'required|string|max:60|regex:/^[a-z0-9_]+$/',
            'fields.*.label'      => 'required|string|max:100',
            'fields.*.tipe_data'  => 'required|string|in:text,number,textarea,select,multiselect,date,datetime,file,boolean,relation',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['nama_modul']);

        DB::transaction(function () use ($validated, $slug, $request) {
            $modul = ModulDinamis::create([
                'nama_modul'   => $validated['nama_modul'],
                'slug'         => $slug,
                'deskripsi'    => $validated['deskripsi'] ?? null,
                'ikon'         => $validated['ikon'],
                'target_roles' => $validated['target_roles'],
                'status_aktif' => true,
                'dibuat_oleh'  => auth()->id(),
            ]);

            foreach ($request->input('fields', []) as $idx => $f) {
                ModulDinamisField::create([
                    'modul_id'            => $modul->id,
                    'nama_kolom'          => Str::snake($f['nama_kolom']),
                    'label'               => $f['label'],
                    'tipe_data'           => $f['tipe_data'],
                    'opsi_pilihan'        => !empty($f['opsi_pilihan']) ? array_map('trim', explode(',', $f['opsi_pilihan'])) : null,
                    'relasi_tabel'        => $f['relasi_tabel'] ?? null,
                    'relasi_kolom_tampil' => $f['relasi_kolom_tampil'] ?? null,
                    'wajib'               => !empty($f['wajib']),
                    'tampil_di_tabel'     => !empty($f['tampil_di_tabel']),
                    'dapat_dicari'        => !empty($f['dapat_dicari']),
                    'urutan'              => $idx + 1,
                ]);
            }

            Cache::forget('dynamic_modules_sidebar');

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'modul_dinamis',
                idData: $modul->id,
                detail: ['aksi' => "Membuat Modul Kustom Baru: {$modul->nama_modul} ({$modul->slug})"],
                channel: 'dynamic-module',
                severity: 'success'
            );
        });

        return redirect()->route('superadmin.modul-builder.index')->with('success', "Modul {$validated['nama_modul']} berhasil diciptakan!");
    }

    public function toggleAktif(ModulDinamis $modul): RedirectResponse
    {
        $modul->update(['status_aktif' => !$modul->status_aktif]);
        Cache::forget('dynamic_modules_sidebar');
        return back()->with('success', 'Status keaktifan modul berhasil diperbarui.');
    }

    public function destroy(ModulDinamis $modul): RedirectResponse
    {
        $nama = $modul->nama_modul;
        $modul->delete();
        Cache::forget('dynamic_modules_sidebar');

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'modul_dinamis',
            detail: ['aksi' => "Menghapus Modul Kustom: {$nama}"],
            channel: 'dynamic-module',
            severity: 'warning'
        );

        return redirect()->route('superadmin.modul-builder.index')->with('success', "Modul {$nama} berhasil dihapus.");
    }
}
```

---

### 6.2 `ModulDataController.php` (Universal Dynamic CRUD Runner)
File: `app/Http/Controllers/SuperAdmin/ModulDataController.php`

```php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ModulDinamis;
use App\Models\ModulDinamisData;
use App\Services\AuditService;
use App\Enums\TipeAuditEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ModulDataController extends Controller
{
    public function __construct(protected AuditService $auditService) {}

    public function index(Request $request, string $slug): View
    {
        $modul = ModulDinamis::with('fields')->where('slug', $slug)->firstOrFail();
        $this->authorizeRoleAccess($modul);

        $query = ModulDinamisData::where('modul_id', $modul->id)->with('creator');

        // Pencarian multi-kolom JSON
        if ($search = $request->input('q')) {
            $searchableFields = $modul->fields->where('dapat_dicari', true)->pluck('nama_kolom');
            $query->where(function ($q) use ($searchableFields, $search) {
                foreach ($searchableFields as $col) {
                    $q->orWhere("data->{$col}", 'LIKE', "%{$search}%");
                }
            });
        }

        $records = $query->paginate(25)->withQueryString();

        return view('superadmin.modul-data.index', compact('modul', 'records'));
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $modul = ModulDinamis::with('fields')->where('slug', $slug)->firstOrFail();
        $this->authorizeRoleAccess($modul);

        $rules = $this->buildValidationRules($modul);
        $validated = $request->validate($rules);

        $dataPayload = [];
        $filesMetadata = [];

        foreach ($modul->fields as $field) {
            $col = $field->nama_kolom;

            if ($field->tipe_data === 'file' && $request->hasFile($col)) {
                $file = $request->file($col);
                $path = $file->store("public/modul-dinamis/{$modul->slug}/" . date('Y/m'));
                $dataPayload[$col] = Storage::url($path);
                $filesMetadata[$col] = [
                    'original_name' => $file->getClientOriginalName(),
                    'mime'          => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                    'path'          => $path,
                ];
            } else {
                $dataPayload[$col] = $validated[$col] ?? null;
            }
        }

        $record = ModulDinamisData::create([
            'modul_id'       => $modul->id,
            'data'           => $dataPayload,
            'files_metadata' => $filesMetadata,
            'created_by'     => auth()->id(),
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: "modul_data_{$modul->slug}",
            idData: $record->id,
            nilaiBaru: $dataPayload,
            detail: ['aksi' => "Menambah data baris pada modul {$modul->nama_modul} #{$record->id}"],
            channel: 'dynamic-module'
        );

        return back()->with('success', 'Data baru berhasil disimpan.');
    }

    public function update(Request $request, string $slug, int $id): RedirectResponse
    {
        $modul = ModulDinamis::with('fields')->where('slug', $slug)->firstOrFail();
        $this->authorizeRoleAccess($modul);

        $record = ModulDinamisData::where('modul_id', $modul->id)->findOrFail($id);
        $rules = $this->buildValidationRules($modul, isUpdate: true);
        $validated = $request->validate($rules);

        $dataPayload = $record->data;
        $filesMetadata = $record->files_metadata ?? [];

        foreach ($modul->fields as $field) {
            $col = $field->nama_kolom;

            if ($field->tipe_data === 'file' && $request->hasFile($col)) {
                $file = $request->file($col);
                $path = $file->store("public/modul-dinamis/{$modul->slug}/" . date('Y/m'));
                $dataPayload[$col] = Storage::url($path);
                $filesMetadata[$col] = [
                    'original_name' => $file->getClientOriginalName(),
                    'mime'          => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                    'path'          => $path,
                ];
            } elseif ($request->has($col)) {
                $dataPayload[$col] = $validated[$col] ?? null;
            }
        }

        $oldData = $record->data;
        $record->update([
            'data'           => $dataPayload,
            'files_metadata' => $filesMetadata,
            'updated_by'     => auth()->id(),
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: "modul_data_{$modul->slug}",
            idData: $record->id,
            nilaiLama: $oldData,
            nilaiBaru: $dataPayload,
            detail: ['aksi' => "Memperbarui data baris pada modul {$modul->nama_modul} #{$record->id}"],
            channel: 'dynamic-module'
        );

        return back()->with('success', 'Data baris berhasil diperbarui.');
    }

    public function destroy(string $slug, int $id): RedirectResponse
    {
        $modul = ModulDinamis::where('slug', $slug)->firstOrFail();
        $this->authorizeRoleAccess($modul);

        $record = ModulDinamisData::where('modul_id', $modul->id)->findOrFail($id);
        $oldData = $record->data;
        $record->delete();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: "modul_data_{$modul->slug}",
            idData: $id,
            nilaiLama: $oldData,
            detail: ['aksi' => "Menghapus data baris pada modul {$modul->nama_modul} #{$id}"],
            channel: 'dynamic-module',
            severity: 'warning'
        );

        return back()->with('success', 'Data baris berhasil dihapus.');
    }

    public function export(string $slug): StreamedResponse
    {
        $modul = ModulDinamis::with('fields')->where('slug', $slug)->firstOrFail();
        $this->authorizeRoleAccess($modul);

        $fileName = "ekspor-modul-{$modul->slug}-" . date('Ymd-His') . ".csv";

        return response()->streamDownload(function () use ($modul) {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            // Header baris CSV
            $headers = ['ID', 'Dibuat Pada'];
            foreach ($modul->fields as $f) {
                $headers[] = $f->label;
            }
            fputcsv($handle, $headers);

            ModulDinamisData::where('modul_id', $modul->id)->chunk(250, function ($rows) use ($handle, $modul) {
                foreach ($rows as $row) {
                    $line = [$row->id, $row->created_at->format('Y-m-d H:i')];
                    foreach ($modul->fields as $f) {
                        $line[] = $row->data[$f->nama_kolom] ?? '-';
                    }
                    fputcsv($handle, $line);
                }
            });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    protected function buildValidationRules(ModulDinamis $modul, bool $isUpdate = false): array
    {
        $rules = [];
        foreach ($modul->fields as $field) {
            $rule = [];
            $rule[] = $field->wajib && !$isUpdate ? 'required' : 'nullable';

            switch ($field->tipe_data) {
                case 'number':
                    $rule[] = 'numeric';
                    break;
                case 'email':
                    $rule[] = 'email:rfc,dns';
                    $rule[] = 'max:255';
                    break;
                case 'date':
                    $rule[] = 'date_format:Y-m-d';
                    break;
                case 'boolean':
                    $rule[] = 'boolean';
                    break;
                case 'file':
                    $rule[] = 'file';
                    $rule[] = 'mimes:pdf,jpg,jpeg,png,webp,docx,xlsx';
                    $rule[] = 'max:5120';
                    break;
                case 'select':
                    if (!empty($field->opsi_pilihan)) {
                        $rule[] = \Illuminate\Validation\Rule::in($field->opsi_pilihan);
                    }
                    break;
                default:
                    $rule[] = 'string';
                    $rule[] = 'max:5000';
                    break;
            }

            $rules[$field->nama_kolom] = $rule;
        }

        return $rules;
    }

    protected function authorizeRoleAccess(ModulDinamis $modul): void
    {
        $userRole = auth()->user()->role?->value ?? 'peserta';
        if (!in_array($userRole, $modul->target_roles, true) && $userRole !== 'superadmin') {
            abort(403, 'Anda tidak memiliki hak otorisasi untuk mengakses modul kustom ini.');
        }
    }
}
```

---

## 7. Rincian Antarmuka Pengguna & JavaScript Field Repeater

### 7.1 Visual Modul Builder Form (`superadmin/modul-builder/create.blade.php`)
Fitur ini dilengkapi JavaScript Field Repeater interaktif yang memungkinkan SuperAdmin menambah, mengatur, dan menghapus field secara dinamis:

```html
@extends('layouts.dashboard')
@section('title', 'Buat Modul Kustom Baru')

@section('content')
<div class="dashboard-content" style="max-width: 1000px; margin: 0 auto; width: 100%;">
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900);">Buat Modul Fitur Baru</h1>
        <p style="font-size: 0.875rem; color: var(--neutral-600);">Rancang modul kustom mandiri tanpa koding dengan skema kolom fleksibel.</p>
    </div>

    <form action="{{ route('superadmin.modul-builder.store') }}" method="POST">
        @csrf
        <!-- 1. Metadata Modul -->
        <div class="card" style="padding: 1.5rem; margin-bottom: 1.5rem; border-radius: 12px; background: var(--neutral-0); border: 1px solid var(--neutral-200);">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">1. Informasi Utama Modul</h3>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Nama Modul *</label>
                    <input type="text" name="nama_modul" class="form-input" placeholder="Contoh: Inventaris Perangkat Magang" required>
                </div>
                <div>
                    <label class="form-label">Pilihan Ikon *</label>
                    <select name="ikon" class="form-select" required>
                        @foreach($availableIcons as $icon)
                            <option value="{{ $icon }}">{{ ucfirst($icon) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Deskripsi Singkat</label>
                <textarea name="deskripsi" class="form-textarea" rows="2" placeholder="Jelaskan tujuan dan fungsi modul ini..."></textarea>
            </div>

            <div>
                <label class="form-label">Target Role yang Berhak Mengakses *</label>
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 0.5rem;">
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                        <input type="checkbox" name="target_roles[]" value="superadmin" checked disabled> SuperAdmin (Wajib)
                        <input type="hidden" name="target_roles[]" value="superadmin">
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                        <input type="checkbox" name="target_roles[]" value="admin" checked> Admin MagKom
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                        <input type="checkbox" name="target_roles[]" value="pembimbing"> Pembimbing Dinas
                    </label>
                </div>
            </div>
        </div>

        <!-- 2. Skema Kolom Dinamis (Field Repeater) -->
        <div class="card" style="padding: 1.5rem; margin-bottom: 1.5rem; border-radius: 12px; background: var(--neutral-0); border: 1px solid var(--neutral-200);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">2. Daftar Kolom / Atribut Data</h3>
                    <p style="font-size: 0.8125rem; color: var(--neutral-500); margin: 0.2rem 0 0 0;">Tentukan atribut data yang akan dicatat di dalam modul ini.</p>
                </div>
                <button type="button" id="btn-add-field" class="btn-secondary" style="font-size: 0.8125rem; padding: 0.5rem 1rem;">
                    + Tambah Kolom Baru
                </button>
            </div>

            <div id="fields-container" style="display: flex; flex-direction: column; gap: 1rem;">
                <!-- Baris Field Pertama Default -->
                <div class="field-row" style="background: var(--neutral-100); padding: 1rem; border-radius: 8px; border: 1px solid var(--neutral-300); display: grid; grid-template-columns: 2fr 2fr 1.5fr 1fr auto; gap: 0.75rem; align-items: flex-end;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 700;">Nama Kolom (snake_case) *</label>
                        <input type="text" name="fields[0][nama_kolom]" value="nomor_seri" class="form-input" style="font-size: 0.8125rem;" required>
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 700;">Label Tampilan *</label>
                        <input type="text" name="fields[0][label]" value="Nomor Seri / SN" class="form-input" style="font-size: 0.8125rem;" required>
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 700;">Tipe Data *</label>
                        <select name="fields[0][tipe_data]" class="form-select" style="font-size: 0.8125rem;" required>
                            <option value="text" selected>Teks (String)</option>
                            <option value="number">Angka (Number)</option>
                            <option value="textarea">Teks Panjang</option>
                            <option value="select">Dropdown Pilihan</option>
                            <option value="date">Tanggal</option>
                            <option value="file">Unggah Berkas</option>
                            <option value="boolean">Switch Ya/Tidak</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center; padding-bottom: 0.5rem;">
                        <label style="font-size: 0.75rem;"><input type="checkbox" name="fields[0][wajib]" value="1" checked> Wajib</label>
                        <label style="font-size: 0.75rem;"><input type="checkbox" name="fields[0][tampil_di_tabel]" value="1" checked> Tabel</label>
                    </div>
                    <div style="padding-bottom: 0.35rem;">
                        <button type="button" class="btn-remove-row" style="background: transparent; border: none; color: #EF4444; cursor: pointer;" title="Hapus Kolom">✕</button>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('superadmin.modul-builder.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary" style="padding: 0.75rem 2rem;">Simpan &amp; Terbitkan Modul</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let fieldIndex = 1;
        const container = document.getElementById('fields-container');
        const btnAdd = document.getElementById('btn-add-field');

        btnAdd.addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'field-row';
            row.style = 'background: var(--neutral-100); padding: 1rem; border-radius: 8px; border: 1px solid var(--neutral-300); display: grid; grid-template-columns: 2fr 2fr 1.5fr 1fr auto; gap: 0.75rem; align-items: flex-end;';
            row.innerHTML = `
                <div>
                    <label style="font-size: 0.75rem; font-weight: 700;">Nama Kolom (snake_case) *</label>
                    <input type="text" name="fields[${fieldIndex}][nama_kolom]" class="form-input" style="font-size: 0.8125rem;" placeholder="contoh_kolom" required>
                </div>
                <div>
                    <label style="font-size: 0.75rem; font-weight: 700;">Label Tampilan *</label>
                    <input type="text" name="fields[${fieldIndex}][label]" class="form-input" style="font-size: 0.8125rem;" placeholder="Contoh Kolom" required>
                </div>
                <div>
                    <label style="font-size: 0.75rem; font-weight: 700;">Tipe Data *</label>
                    <select name="fields[${fieldIndex}][tipe_data]" class="form-select" style="font-size: 0.8125rem;" required>
                        <option value="text">Teks (String)</option>
                        <option value="number">Angka (Number)</option>
                        <option value="textarea">Teks Panjang</option>
                        <option value="select">Dropdown Pilihan</option>
                        <option value="date">Tanggal</option>
                        <option value="file">Unggah Berkas</option>
                        <option value="boolean">Switch Ya/Tidak</option>
                    </select>
                </div>
                <div style="display: flex; gap: 0.5rem; align-items: center; padding-bottom: 0.5rem;">
                    <label style="font-size: 0.75rem;"><input type="checkbox" name="fields[${fieldIndex}][wajib]" value="1"> Wajib</label>
                    <label style="font-size: 0.75rem;"><input type="checkbox" name="fields[${fieldIndex}][tampil_di_tabel]" value="1" checked> Tabel</label>
                </div>
                <div style="padding-bottom: 0.35rem;">
                    <button type="button" class="btn-remove-row" style="background: transparent; border: none; color: #EF4444; cursor: pointer;" title="Hapus Kolom">✕</button>
                </div>
            `;
            container.appendChild(row);
            fieldIndex++;
        });

        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-remove-row')) {
                if (container.querySelectorAll('.field-row').length > 1) {
                    e.target.closest('.field-row').remove();
                } else {
                    alert('Modul wajib memiliki minimal 1 kolom data.');
                }
            }
        });
    });
</script>
@endsection
```

---

## 8. Step-by-Step Task Breakdown untuk Junior Developer / Budget AI

Berikut adalah 10 langkah kerja berurutan yang siap dijalankan secara bertahap tanpa error:

1. **Task 1: Eksekusi Migrasi Basis Data**:
   - Terapkan `2026_10_15_000001_add_channel_to_audit_log_table.php`.
   - Terapkan `2026_10_15_000002_create_dynamic_modules_tables.php`.
   - Jalankan `php artisan migrate`.

2. **Task 2: Model & Observer Layer**:
   - Buat Model `ModulDinamis`, `ModulDinamisField`, dan `ModulDinamisData` lengkap dengan relationship dan casts.
   - Buat `app/Observers/AuditableObserver.php` dan hubungkan ke model di `EventServiceProvider`.

3. **Task 3: Enhancement AuditService**:
   - Perbarui method `catat()` di `AuditService` dengan resolusi 15 channel dan sensor data rahasia.

4. **Task 4: Rombak Multi-Channel Audit Log View**:
   - Perbarui `AuditLogController` dengan method `channelView()`.
   - Rombak `resources/views/superadmin/audit-log/index.blade.php` dengan Discord-style 2-kolom dan modal JSON diff.

5. **Task 5: Telemetri Kesehatan Proaktif**:
   - Lengkapi `HealthMonitorController` dengan 4 aksi pemeliharaan (`runSecuritySweep`, `optimizeDatabase`, `clearSystemCache`, `testStorageWrite`).
   - Perbarui `resources/views/superadmin/health/index.blade.php` dengan widget gauge dan status SLA.

6. **Task 6: Pembuat Modul Dinamis (Modul Builder)**:
   - Buat `ModulBuilderController` dengan validasi snake_case.
   - Buat tampilan `superadmin/modul-builder/index.blade.php` dan `create.blade.php` (JS Field Repeater).

7. **Task 7: Eksekutor Universal CRUD Data Dinamis**:
   - Buat `ModulDataController` dengan pencarian multi-kolom JSON, upload file terenkripsi, dan ekspor CSV.
   - Buat view `superadmin/modul-data/index.blade.php` yang otomatis me-render tabel dan modal input.

8. **Task 8: Injeksi Sidebar Dinamis & Caching**:
   - Pasang menu `Telemetri Kesehatan` dan `Pembuat Modul` di `layouts/dashboard.blade.php`.
   - Render otomatis modul aktif di bawah seksi `"MODUL KUSTOM"`.

9. **Task 9: Pendaftaran Rute `routes/web.php`**:
   - Daftarkan grup rute `/audit-log`, `/health-monitor`, `/modul-builder`, dan `/modul/{slug}` di grup SuperAdmin.

10. **Task 10: Pengujian TDD 100% Pass**:
    - Buat file test `tests/Feature/SuperAdminMasterArchitectureTest.php`.
    - Uji 15 kanal audit log, telemetri health, modul builder, dan operasi data CRUD dinamis.

---

## 9. Skenario Pengujian TDD & QA Checklist

- [ ] **Multi-Channel Audit Log (15 Channels)**:
  - [ ] Membuka `/superadmin/audit-log` menampilkan 15 kanal Discord-style di panel kiri.
  - [ ] Setiap aksi (login, verifikasi berkas, ubah kuota, tambah tugas, input nilai) masuk ke kanalnya masing-masing.
  - [ ] Counter badge kanal menampilkan jumlah event 24 jam secara akurat.
  - [ ] Modal inspeksi JSON diff menampilkan perubahan data lama vs baru dengan format bersih.
  - [ ] Ekspor CSV hanya mengekspor data kanal yang sedang aktif.
- [ ] **System Health Monitor**:
  - [ ] Halaman `/superadmin/health-monitor` dapat diakses langsung dari sidebar.
  - [ ] Keempat tombol pemeliharaan (Security Sweep, Optimasi DB, Bersihkan Cache, Uji Tulis Storage) berfungsi sukses dengan notifikasi toast.
  - [ ] Pengukur latensi ping database berjalan akurat ($< 50$ ms).
- [ ] **Dynamic Low-Code CRUD Module Engine**:
  - [ ] SuperAdmin berhasil membuat modul baru (misal: "Inventaris Laptop") dengan field text, select, date, dan file.
  - [ ] Modul baru langsung muncul otomatis di sidebar navigasi SuperAdmin.
  - [ ] SuperAdmin dapat menginput baris data baru melalui modal form otomatis; data tersimpan di `modul_dinamis_data`.
  - [ ] Data dapat diedit, dihapus, dan diekspor ke format CSV.
  - [ ] Seluruh aksi pada modul kustom otomatis tercatat di kanal `# dynamic-module-log`.
- [ ] **Keamanan RBAC**:
  - [ ] Role Admin, Pembimbing, dan Peserta ditolak dengan status HTTP 403 saat mencoba mengakses modul builder.
