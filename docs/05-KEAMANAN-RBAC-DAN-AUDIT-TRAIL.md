# Spesifikasi Keamanan, RBAC & Audit Trail MagKom

Dokumen ini memuat blueprint implementasi pengamanan tingkat aplikasi, matriks otorisasi Role-Based Access Control (RBAC), arsitektur Policy, serta spesifikasi jejak audit (*Audit Trail*) untuk sistem MagKom (Magang Kominfo).

---

## 1. Matriks Otorisasi RBAC Komprehensif

Sistem membedakan hak akses secara ketat berdasarkan peran: **SuperAdmin**, **Admin** (Administrasi Kominfo), **Pembimbing**, dan **Peserta**.

| Fungsi & Kemampuan | SuperAdmin | Admin | Pembimbing | Peserta | Mekanisme Penegakan |
|---|---|---|---|---|---|
| Kelola akun staf & reset password staf | **Ya** | – | – | – | Middleware `role:superadmin` |
| Kelola master data (jurusan, bidang, rubrik) | **Ya** | – | – | – | Middleware `role:superadmin` |
| Buka / atur tanggal bulan magang | **Ya** | – | – | – | Middleware `role:superadmin` |
| Atur status landing page (override) | **Ya** | – | – | – | Middleware `role:superadmin` |
| Atur kuota slot khusus | **Ya** | – | – | – | Middleware `role:superadmin` |
| Isi kuota umum per jurusan | **Ya** | **Ya** | – | – | Middleware `role:admin,superadmin` |
| Verifikasi pendaftaran & berkas | **Ya** | **Ya** | – | – | Middleware `role:admin,superadmin` |
| Tetapkan penempatan & pindah pembimbing | **Ya** | **Ya** | – | – | Middleware `role:admin,superadmin` |
| Input peserta manual (khusus/umum) | **Ya** | **Ya** | – | – | Middleware `role:admin,superadmin` |
| Beri tugas & review progres tugas | **Lihat** | – | **Ya** | – | `TugasPolicy` (SuperAdmin ditolak mutasi) |
| Review & minta revisi logbook | **Lihat** | – | **Ya** | – | `LogbookPolicy` (SuperAdmin ditolak mutasi) |
| Input nilai rubrik & ubah nilai | **Lihat** | – | **Ya** | – | `PenilaianPolicy` (SuperAdmin ditolak mutasi) |
| Tetapkan status penempatan Selesai | **Lihat** | – | **Ya** | – | `PenilaianPolicy` (SuperAdmin ditolak mutasi) |
| Lihat nilai & unduh Lembar Nilai PDF | **Ya** (semua) | – | **Ya** (bimbingan) | **Ya** (milik sendiri) | `PenilaianPolicy@downloadLembarNilai` |
| Lihat audit log (append-only) | **Lihat** | – | – | – | Middleware `role:superadmin` (tanpa edit/hapus) |
| Ubah pengaturan konfigurasi sistem | **Ya** | – | – | – | Middleware `role:superadmin` |
| Daftar magang, upload berkas, isi logbook| – | – | – | **Ya** | Middleware `role:peserta` & Policy kepemilikan |

---

## 2. Pola Isolasi SuperAdmin Read-Only pada Ranah Pembimbing

Salah satu aturan bisnis paling krusial adalah: **SuperAdmin hanya boleh membaca (read-only) fungsi bimbingan (tugas, logbook, penilaian), dan dilarang keras melakukan aksi penulisan/mutasi.**

### Pola Implementasi Policy Laravel 10

Pada Policy bawaan Laravel, method `before()` sering dipakai untuk memberi hak SuperAdmin melakukan apa saja (bypass). Pada MagKom, implementasinya **wajib dibatasi hanya untuk kemampuan baca**:

```php
// app/Policies/PenilaianPolicy.php
namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Penilaian;
use App\Models\User;

class PenilaianPolicy
{
    /**
     * Intersep sebelum method spesifik dipanggil.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === RoleEnum::SuperAdmin) {
            // Daftar kemampuan yang diizinkan untuk SuperAdmin
            $kemampuanBaca = [
                'viewAny',
                'view',
                'viewRiwayat',
                'downloadLembarNilai',
            ];

            if (in_array($ability, $kemampuanBaca, true)) {
                return true; // Diizinkan membaca
            }

            // Kembalikan NULL (jangan kembalikan false atau true)
            // agar evaluasi berlanjut ke method spesifik di bawah
            return null;
        }

        return null;
    }

    /**
     * Pembimbing pemilik berhak mengedit nilai.
     * SuperAdmin akan ditolak di sini karena role bukan Pembimbing!
     */
    public function update(User $user, Penilaian $penilaian): bool
    {
        return $user->role === RoleEnum::Pembimbing
            && $penilaian->pembimbing_id === $user->id;
    }

    public function tetapkanSelesai(User $user, Penilaian $penilaian): bool
    {
        return $user->role === RoleEnum::Pembimbing
            && $penilaian->pembimbing_id === $user->id;
    }
}
```

---

## 3. Arsitektur Audit Trail Komprehensif

Sistem Audit Trail dirancang untuk memenuhi persyaratan akuntabilitas mutlak instansi pemerintah (D-08, NFR-A1 s/d NFR-A5).

### 3.1 Klasifikasi 4 Tipe Audit

1. **`auth`**:
   - Peristiwa login, logout, gagal login, email verification, dan password reset.
   - Dicatat instan pada event listener autentikasi. Token dan kata sandi otomatis disensor.
2. **`aksi`**:
   - Seluruh mutasi data bisnis (pendaftaran disetujui, pembatalan penempatan, perubahan nilai).
   - **Wajib ditulis dalam transaksi database yang sama dengan aksi bisnis**. Jika penulisan audit gagal, maka mutasi bisnis di-rollback (AC-13-7).
3. **`service`**:
   - Rekam pemanggilan method pada lapisan Service (`AuditedService`).
   - Menyimpan nama method, argumen tersanitasi, status hasil (`ok` atau nama exception), serta durasi waktu eksekusi dalam milidetik.
4. **`akses_data`**:
   - Pencatatan pembacaan data sensitif (view detail pendaftaran, ekspor rekapitulasi, unduh berkas).
   - Ditulis secara efisien: **1 baris per HTTP request** melalui `terminable middleware` setelah response dikirim ke browser.

### 3.2 Pola Sanitasi Sensor Data Sensitif

`AuditService` memiliki filter penyaringan otomatis sebelum objek disimpan ke kolom `detail`, `nilai_lama`, atau `nilai_baru`:

```php
// app/Services/AuditService.php
namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class AuditService
{
    private const KUNCI_RAHASIA = [
        'password',
        'password_confirmation',
        'token',
        'remember_token',
        'captcha',
        'cf-turnstile-response',
        'g-recaptcha-response',
        'secret',
    ];

    public function sanitasi(mixed $nilai): mixed
    {
        return match (true) {
            $nilai instanceof Model => [
                'model' => class_basename($nilai),
                'id'    => $nilai->getKey(),
            ],
            $nilai instanceof UploadedFile => [
                'nama_file' => $nilai->getClientOriginalName(),
                'ukuran'    => $nilai->getSize(),
                'mime'      => $nilai->getClientMimeType(),
            ],
            is_array($nilai) => collect($nilai)->map(function ($val, $key) {
                if (in_array(strtolower((string) $key), self::KUNCI_RAHASIA, true)) {
                    return '[DISAMARKAN]';
                }
                return $this->sanitasi($val);
            })->all(),
            is_scalar($nilai) || $nilai === null => $nilai,
            default => get_debug_type($nilai),
        };
    }
}
```

### 3.3 Penegakan Integritas Append-Only di Level Database

Tabel `audit_log` bersifat **kekal (append-only)**:
1. Model Eloquent `AuditLog` menonaktifkan kolom `updated_at` dan melempar exception bila dipanggil method `update()` atau `delete()`.
2. Di tingkat engine database MySQL, akun runtime aplikasi (`magkom_app`) **hanya diberikan hak `SELECT` dan `INSERT`**:

```sql
-- Diberikan saat setup server produksi
GRANT SELECT, INSERT ON magkom_db.audit_log TO 'magkom_app'@'localhost';
FLUSH PRIVILEGES;
```

### 3.4 Retensi dan Pemangkasan Data Lama

- Log bertipe `aksi` dan `auth` disimpan **selamanya** untuk kepatuhan hukum arsip negara.
- Log bertipe `akses_data` dan `service` yang telah berusia lebih dari 1 tahun dipangkas melalui maintenance script terpisah menggunakan kredensial database pemeliharaan khusus (`magkom_maint`) yang dijalankan via cron server bulanan:

```bash
# Cron bulanan server Linux (bukan dari runtime aplikasi)
0 2 1 * * /usr/bin/php /var/www/magkom/scripts/prune_access_audit.php >> /var/log/magkom_maint.log 2>&1
```

---

## 4. Keamanan Berkas & Pencegahan IDOR

### 4.1 Penyimpanan Disk Privat
- Semua dokumen peserta (surat pengantar, CV, rapor, transkrip) disimpan pada disk `private` (`storage/app/private/documents/`).
- Direktori ini **tidak pernah** dibuatkan symbolic link ke direktori `public/`.

### 4.2 Temporary Signed Route
- Unduhan dokumen hanya dapat diakses melalui rute bertanda tangan HMAC dengan masa berlaku 5 menit.
- Controller unduhan memvalidasi Policy sebelum mengirimkan stream file:

```php
// app/Http/Controllers/Peserta/BerkasController.php
public function unduh(Dokumen $dokumen)
{
    // Memastikan user adalah pemilik pendaftaran atau Admin/SuperAdmin
    $this->authorize('download', $dokumen);

    return Storage::disk('private')->download(
        $dokumen->path,
        $dokumen->nama_asli,
        ['X-Content-Type-Options' => 'nosniff']
    );
}
```

---

## 5. Header Keamanan HTTP (Security Headers)

Middleware `SecurityHeaders` memastikan browser menerapkan proteksi perimeter berlapis:

```php
// app/Http/Middleware/SecurityHeaders.php
public function handle(Request $request, Closure $next): Response
{
    $response = $next($request);

    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-Frame-Options', 'DENY');
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

    if ($request->isSecure()) {
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    if (app()->isProduction()) {
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; img-src 'self' data:; script-src 'self'; style-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self';"
        );
    }

    return $response;
}
```
