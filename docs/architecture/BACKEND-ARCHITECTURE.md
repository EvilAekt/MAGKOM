# ARSITEKTUR BACKEND MAGKOM
**Sistem:** Magang Kominfo Sleman (MAGKOM)  
**Versi Platform:** PHP 8.3 / Laravel 10.x  
**Pola Desain:** Clean Layered Architecture (Controller &rarr; Form Request &rarr; Service &rarr; Model &rarr; Database)  

---

## 1. RINGKASAN ARSITEKTUR

Aplikasi MAGKOM dibangun di atas framework Laravel 10 native dengan pendekatan clean engineering berlapis. Arsitektur backend memisahkan secara tegas tanggung jawab penanganan HTTP, validasi input, aturan bisnis inti, integritas data, dan otorisasi.

```
       [ HTTP Request ]
              │
              ▼
    [ Routing & Middleware ]  ── (SecurityHeaders, Auth, Role/RBAC, CSRF)
              │
              ▼
     [ Form Request Layer ]   ── (Input Validation, Authorization, Data Sanitization)
              │
              ▼
      [ Controller Layer ]    ── (HTTP Orchestration, Response / View / Redirect)
              │
              ▼
       [ Service Layer ]      ── (Core Business Logic, Quota, Transactions, AuditedService)
              │
              ▼
   [ Eloquent Models & DB ]   ── (Relationships, Scopes, Accessors, MySQL Database)
              │
              ▼
     [ Events & Realtime ]    ── (RealtimeEvent, SSE Stream RFC 8895)
```

---

## 2. LAPISAN ARSITEKTUR (ARCHITECTURAL LAYERS)

### 2.1 Routing & Middleware
- **Rute Web (`routes/web.php`):** Mengatur rute modul publik, otentikasi, alur peserta, pembimbing, admin, dan superadmin.
- **Keamanan HTTP (`App\Http\Middleware\SecurityHeaders`):** Menginjeksi security headers standar OWASP:
  - `Content-Security-Policy`: Menetapkan `default-src 'self'`, `script-src 'self' https://challenges.cloudflare.com https://cdn.tailwindcss.com`, `style-src 'self' 'unsafe-inline' https://fonts.googleapis.com`, dan `font-src 'self' https://fonts.gstatic.com data:`.
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: SAMEORIGIN`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy` dan HTTP Cache-Control ketat untuk dokumen privat peserta.
- **Proteksi Logout (SEC-04):** Endpoint `/logout` hanya menerima method `POST` dengan verifikasi CSRF token.

### 2.2 Form Request Layer
Bertanggung jawab memvalidasi dan menormalisasi input sebelum controller dieksekusi:
- `app/Http/Requests/PendaftaranRequest.php`: Validasi pendaftaran mandiri peserta.
- `app/Http/Requests/TetapkanPenempatanRequest.php`: Validasi penempatan pembimbing dan bidang.
- `app/Http/Requests/PenempatanManualRequest.php`: Validasi penempatan jalur khusus/manual.
- `app/Http/Requests/LogbookRequest.php`: Validasi jurnal harian/mingguan logbook.
- `app/Http/Requests/TugasRequest.php`: Validasi tenggat waktu tugas terhadap batas akhir masa magang peserta.
- `app/Http/Requests/BulanMagangRequest.php` & `KuotaRequest.php`: Pengelolaan siklus periode kuota.

### 2.3 Controller Layer
Controller hanya bertugas mengorkestrasi request, memanggil service layer, dan mengembalikan HTTP Response atau Blade View. Controller dilarang memuat direct database transactions, business calculations, atau hardcoded password logic:
- `Admin/PendaftaranController.php`: Menangani verifikasi berkas dan seleksi peserta.
- `Admin/PenempatanController.php`: Menangani antrean penempatan dua tahap dan pendelegasian mutasi pembimbing ke `PlacementService`.
- `SuperAdmin/ModulDataController.php`: Orkestrasi modul dinamis dengan validasi schema identifier strict.
- `Peserta/LogbookController.php` & `TugasController.php`: Pengelolaan aktivitas magang peserta.

### 2.4 Service Layer
Pusat business logic sistem, diturunkan dari `AuditedService` untuk pencatatan otomatis ke `audit_log`:
- `PlacementService`: Penempatan umum dua tahap, penempatan khusus, dan pemindahan pembimbing massal (`alihkanBatch`). Menghilangkan shared password fallback di environment production.
- `QuotaAllocationService`: Penguncian pesimistik (`lockForUpdate`) untuk alokasi kuota anti-race-condition.
- `AuditService`: Pencatatan audit append-only dengan sanitasi rekursif regex untuk masking seluruh variasi kata sandi, token, dan kredensial rahasia (`SEC-06`).
- `DocumentStorageService` & `DocumentVerificationService`: Pengelolaan penyimpanan dokumen privat dan verifikasi keabsahan berkas bertanda tangan digital.
- `RealtimeEventService`: Dispatching notifikasi instan berbasis server-sent events.

### 2.5 Eloquent Model Layer
Model mengelola relasi, casting tipe data native/enum, dan local query scopes:
- `User`: Otentikasi, RBAC multi-role (`RoleEnum`), relasi profil dan penempatan.
- `Penempatan`: Periode aktif magang, relasi kuota, slot khusus, dan generator status minggu logbook.
- `ModulDinamis`: Konfigurasi metadata modul dinamis, schema tabel fisik, dan granular RBAC matrix.
- `AuditLog`: Log forensik transaksi dan aktivitas sistem append-only.

### 2.6 Policy Layer
Memastikan otorisasi berbasis peran (RBAC) dan mencegah kerentanan IDOR (Insecure Direct Object Reference):
- `PendaftaranPolicy`: Mencegah peserta membaca atau membatalkan pendaftaran peserta lain.
- `PenempatanPolicy`: Mengatur hak akses pembimbing hanya terhadap mentee aktif binaannya.
- `LogbookPolicy` & `TugasPolicy`: Membatasi pembimbing agar tidak dapat merevisi atau menilai tugas di luar bidang/mentee binaannya.

---

## 3. REALTIME & TELEMETRI

Sistem menggunakan Server-Sent Events (SSE) native pada `/realtime/stream` (`RealtimeStreamController`) yang mematuhi standar RFC 8895. Event ditransmisikan secara spesifik ke `user_id` atau broadcast berdasarkan peran (`RoleEnum`), dan dikonsumsi di frontend melalui modul `resources/js/services/realtime.js`.

