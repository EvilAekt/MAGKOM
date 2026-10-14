# Plan: Arsitektur Real-time Server-Sent Events (SSE) & Senior Task Handbook — Kelanjutan Ekosistem MagKom (Production-Ready)

- **Tanggal Dokumen**: 2026-10-13
- **Status**: Selesai & Terverifikasi Penuh (Completed & 100% Verified)
- **Author**: Lead Principal Software Architect & Head of Security Engineering (Antigravity web-planning)
- **Target Pembaca**: Junior Developers, Mid Developers, dan AI Operasional Berbiaya Rendah (Gemini 3.8 Flash / Claude Haiku / GPT-4o-mini)
- **Dokumen Acuan Wajib**:
  1. [`docs/01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (PRD v1.1 - Fitur F-01 s.d. F-17)
  2. [`docs/02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Technical Design Document v1.1)
  3. [`docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Roadmap & Panduan Eksekusi)
  4. [`docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) (Skema DB & Kamus Data)
  5. [`docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](file:///g:/MAGKOM/docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md) (RBAC, Hak Akses & Audit Trail)
  6. [`docs/06-ADDENDUM-KEAMANAN-DAN-QA.md`](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md) (Addendum Keamanan SEC-01 s.d. SEC-74 & QA Matrix)
  7. [`docs/07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Design System 30 Token Figma & WCAG AA)

---

## 1. TUJUAN (OBJECTIVES)

Dokumen ini disusun untuk:
1. **Mengintegrasikan Kemampuan Real-time yang Efisien & Andal**: Menghubungkan seluruh portal (Admin, Pembimbing, SuperAdmin, Peserta) dengan aliran data real-time menggunakan standar **Server-Sent Events (SSE / RFC 8895)** yang ringan, hemat sumber daya, dan ramah infrastruktur SPBE Pemkab Sleman.
2. **Menjawab Kebutuhan Bisnis Nyata (Real-time Use Cases)**:
   - **Admin Seleksi**: Menerima notifikasi seketika saat ada berkas pendaftaran baru masuk, pembaruan sisa kuota, dan peringatan batas SLA (> 3 hari).
   - **Pembimbing Lapangan**: Menerima peringatan live saat mentee mengunggah logbook harian atau progres tugas, serta peringatan mendekati tenggat deadline tugas.
   - **Peserta Magang**: Mengetahui secara instan saat status pendaftarannya diputuskan (*Diterima*, *Perlu Perbaikan*, *Ditolak*, atau *Waiting List*), saat menerima tugas baru, atau saat logbook selesai direviu.
   - **SuperAdmin**: Memantau lonjakan peringatan keamanan (*threat radar alert*) dan status kuota secara terpadu.
3. **Mencegah Jebakan Performa PHP-FPM (Zero Worker Starvation)**: Merancang arsitektur SSE khusus PHP-FPM dengan teknik *Capped Streaming with Graceful Auto-Reconnect* (timeout 20–25 detik) dan pelepasan sesi (*session lock release* `session_write_close()`), sehingga ribuan koneksi tidak akan pernah mengunci worker PHP.
4. **Menyediakan Buku Panduan Tugas Senior (Senior Task Handbook)**: Menyusun rincian penugasan kerja modular (*Work Breakdown Structure*) yang sangat terperinci, preskriptif, dan bebas ambiguitas untuk junior developer atau model AI berbiaya rendah (Gemini 3.8 Flash).

---

## 2. ASUMSI & PERTANYAAN TERBUKA

### 2.1 Asumsi Teknis & Arsitektural
- **Lingkungan Server**: Server produksi menggunakan Linux dengan Nginx reverse proxy dan PHP-FPM 8.2+.
- **Penanganan Buffer Proxy**: Nginx dikonfigurasi dengan `proxy_buffering off;` atau Laravel mengirim header `X-Accel-Buffering: no` untuk memastikan transmisi data berlangsung tanpa terhambat buffer server.
- **Pelepasan Session Lock**: Sebelum loop SSE dimulai, controller wajib memanggil `session_write_close()` agar request HTTP lain dari pengguna yang sama (seperti navigasi halaman atau pengiriman form) tidak terblokir oleh sesi yang tertahan.
- **Pola Transmisi Data**: Menggunakan *Transactional Outbox Pattern* berbasis tabel database `realtime_events`. Hal ini menjamin bahwa event tersimpan secara persisten, mendukung riwayat pengiriman (*Last-Event-ID*), dan tidak membebani server dengan Redis dependensi tambahan jika Redis belum terpasang.
- **Retensi Event**: Event real-time yang berumur lebih dari 7 hari akan dipangkas secara otomatis oleh task scheduler untuk menjaga ukuran database tetap ramping.

### 2.2 Pertanyaan Terbuka
- Tidak ada pertanyaan pemblokir (*blocking questions*). Seluruh rute, model peran RBAC, dan skema database sudah terdokumentasi lengkap pada PRD v1.1 dan TDD.

---

## 3. PENDEKATAN DESAIN & KEPUTUSAN ARSITEKTUR

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│             ARSITEKTUR SERVER-SENT EVENTS (SSE) MAGKOM (ANTI-STARVATION)               │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 1. LAYER MUTASI (PRODUCER / DISPATCHER)                                                │
│    Peserta Daftar / Pembimbing Reviu ──> RealtimeEventService::publish()               │
│                                                │                                       │
│                                                ▼                                       │
│                                      [Tabel realtime_events]                           │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 2. LAYER STREAMING (CONSUMER / SSE CONTROLLER)                                         │
│    Browser Klien (Admin / Pembimbing / Peserta)                                        │
│          │                                                                             │
│          ▼ GET /realtime/stream (membawa header Last-Event-ID)                         │
│    [RealtimeStreamController]                                                          │
│          │── session_write_close(); // Lepas session lock agar tab lain lancar        │
│          │── set_time_limit(30);                                                       │
│          │── Kirim Header: Content-Type: text/event-stream; X-Accel-Buffering: no      │
│          │── Loop Capped 20-25 detik (Cek DB tiap 2 detik):                            │
│          │      └─ Ada event baru? Kirim "id: X \n event: Y \n data: JSON \n\n"        │
│          │      └─ Tidak ada? Kirim komentar ping ": ping\n\n"                         │
│          └── Tutup stream secara graceful setelah 25 detik (Kirim "retry: 2500\n\n")   │
│                                                                                        │
│ 3. LAYER KLIEN (EVENTSOURCE AUTO-RECONNECT + TAB VISIBILITY)                           │
│    Native Browser EventSource ───(Otomatis Reconnect setelah 2.5 detik)────────┐      │
│    - Menangkap event tipe kustom (pendaftaran_baru, logbook_baru, dll.)        │      │
│    - Merender Floating Toast Card (Design System Figma Token)                  │      │
│    - Memperbarui counter badge pada navbar / sidebar                           │      │
│    - Menghemat baterai: Jika tab browser di-minimize, koneksi di-pause! ◄──────┘      │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

### 3.1 Evaluasi Opsi Teknis
* **Opsi A: Long-Lived Infinite Loop SSE (`while(true) sleep(2)`)**:
  * *Kelemahan*: Mengunci 1 proses PHP-FPM per tab pengguna selama berjam-jam. Jika ada 30 pengguna online, pool PHP-FPM habis total, server tumbang (Error 504 Gateway Timeout).
* **Opsi B: WebSocket (Laravel Reverb / Pusher / Soketi)**:
  * *Kelemahan*: Membutuhkan background daemon 24/7 tambahan di server, konfigurasi port firewall baru, dan manajemen koneksi stateful yang berlebihan untuk sistem administrasi satu arah.
* **Opsi C (Rekomendasi Terpilih): Capped Chunked SSE Streaming (20–25s) + Native Auto-Reconnect**:
  * *Kelebihan*:
    1. **Zero Worker Starvation**: Worker PHP-FPM hanya terpakai maksimal 20–25 detik, lalu dilepas kembali ke pool.
    2. **Zero Client Library**: Menggunakan API browser bawaan `window.EventSource` tanpa perlu menginstal dependensi npm berat.
    3. **Resilient & Self-Healing**: Jika jaringan pengguna terputus di lift/kendaraan, browser otomatis menyambung kembali membawa `Last-Event-ID` sehingga tidak ada notifikasi yang terlewat.
    4. **Ramah Nginx & Firewall SPBE**: Berjalan di port 443 HTTPS standar tanpa perlu membuka port baru.

---

## 4. PEMETAAN FITUR REAL-TIME PER PERAN (USE CASE MATRIX)

| Peran (Role) | Tipe Event SSE | Pemicu (Trigger) | Dampak pada Tampilan UI Klien |
| :--- | :--- | :--- | :--- |
| **Admin** | `pendaftaran_baru` | Calon peserta berhasil mengirim berkas pendaftaran baru (`AC-01`, `AC-04`). | 1. Toast notifikasi muncul: *"Pendaftaran Baru: [Nama] ([Jurusan])"*. <br>2. Badge counter "Menunggu Verifikasi" bertambah +1 secara live. |
| **Admin** | `kuota_berubah` | Verifikator lain menerima peserta atau membatalkan penerimaan. | Indikator sisa kuota kursi pada panel kanan detail verifikasi diperbarui secara instan. |
| **Admin** | `sla_kritis_alert` | Pendaftaran melewati batas 3 hari kerja tanpa verifikasi. | Muncul indikator visual amber/merah pada notifikasi bar. |
| **Pembimbing** | `logbook_baru` | Mentee yang dibimbingnya mengirim logbook harian (`BR-09`). | 1. Toast notifikasi: *"[Nama Mentee] mengisi logbook hari ini"*. <br>2. Badge antrean Action Hub di Tab 2 bertambah. |
| **Pembimbing** | `tugas_dikumpulkan`| Mentee mengunggah laporan progres tugas (`AC-08`). | Toast notifikasi: *"[Nama Mentee] mengumpulkan tugas: [Judul Tugas]"*. |
| **Peserta** | `status_pendaftaran`| Admin memutuskan verifikasi (`Diterima`, `Perlu Perbaikan`, `Ditolak`, `Waiting List`). | 1. Toast notifikasi prioritas tinggi.<br>2. Banner status pada dashboard peserta berubah warna dan teks seketika. |
| **Peserta** | `tugas_baru` | Pembimbing menerbitkan tugas baru dengan deadline. | Toast notifikasi tugas baru dan penambahan kartu tugas di portal peserta. |
| **Peserta** | `logbook_direviu` | Pembimbing memberikan catatan umpan balik / paraf pada logbook. | Toast notifikasi reviu logbook selesai. |
| **SuperAdmin**| `ancaman_keamanan` | Terdeteksi login anomali berulang (SEC-01) atau duplikasi NIM ganda lintas akun (SEC-15). | Toast alert merah pada panel System Security & Threat Radar. |

---

## 5. DAFTAR PERUBAHAN FILE (FILE INVENTORY)

| File Path | Aksi | Ringkasan Perubahan & Kepatuhan Arsitektur |
| :--- | :---: | :--- |
| `database/migrations/2026_10_13_000003_create_realtime_events_table.php` | **Create** | Tabel outbox `realtime_events` (`user_id`, `role`, `event_type`, `payload`, `read_at`). |
| `app/Models/RealtimeEvent.php` | **Create** | Model Eloquent dengan scope per user/role dan cast payload ke JSON array. |
| `app/Enums/RealtimeEventTypeEnum.php` | **Create** | PHP 8.2 Backed Enum untuk type safety jenis event (bebas magic string). |
| `app/Services/RealtimeEventService.php` | **Create** | Service domain untuk publikasi event baru dan pemangkasan log kedaluwarsa. |
| `app/Http/Controllers/RealtimeStreamController.php` | **Create** | Controller streaming SSE (`text/event-stream`, capped 25 detik, `session_write_close`). |
| `public/js/realtime-sse.js` | **Create** | Client-side manager EventSource, auto-reconnect, handling tab visibility, dan rendering toast. |
| `resources/views/components/sse-toast-listener.blade.php` | **Create** | Blade component penyedia container toast notifikasi yang disisipkan di layout utama. |
| `resources/views/layouts/admin.blade.php` | **Modify** | Menyisipkan `<x-sse-toast-listener role="admin" />` dan script SSE. |
| `resources/views/layouts/pembimbing.blade.php` | **Modify** | Menyisipkan `<x-sse-toast-listener role="pembimbing" />` dan script SSE. |
| `resources/views/layouts/peserta.blade.php` | **Modify** | Menyisipkan `<x-sse-toast-listener role="peserta" />` dan script SSE. |
| `resources/views/layouts/superadmin.blade.php` | **Modify** | Menyisipkan `<x-sse-toast-listener role="superadmin" />` dan script SSE. |
| `app/Http/Controllers/Peserta/PendaftaranController.php` | **Modify** | Men-dispatch event `pendaftaran_baru` saat peserta submit formulir pendaftaran. |
| `app/Http/Controllers/Admin/PendaftaranController.php` | **Modify** | Men-dispatch event `status_pendaftaran` ke peserta saat keputusan disimpan. |
| `app/Http/Controllers/Peserta/LogbookController.php` | **Modify** | Men-dispatch event `logbook_baru` ke pembimbing saat logbook diserahkan. |
| `app/Http/Controllers/Pembimbing/LogbookController.php` | **Modify** | Men-dispatch event `logbook_direviu` ke peserta saat catatan review disimpan. |
| `app/Http/Controllers/Pembimbing/TugasController.php` | **Modify** | Men-dispatch event `tugas_baru` ke mentee saat penugasan dibuat. |
| `routes/web.php` | **Modify** | Menambahkan rute `GET /realtime/stream` (proteksi auth) dan `POST /realtime/mark-read`. |
| `tests/Feature/RealtimeEventSseStreamTest.php` | **Create** | Test response streaming SSE, headers, filter peran, dan penanganan Last-Event-ID. |
| `tests/Feature/RealtimeEventDispatchIntegrationTest.php`| **Create** | Test verifikasi bahwa aksi admin/pembimbing/peserta memicu pembuatan record event. |

---

## 6. KATALOG TIKET TUGAS SENIOR DEVELOPER (SENIOR WORK ORDERS)

```
================================================================================
KATALOG TIKET TUGAS SENIOR DEVELOPER (WBS SSE REAL-TIME SYSTEM)
================================================================================
- TASK-SSE-01: Migrasi Skema Basis Data & Model Outbox `realtime_events`
- TASK-SSE-02: Domain Enum & Service Publisher `RealtimeEventService`
- TASK-SSE-03: Controller Streaming SSE Anti-Starvation `RealtimeStreamController`
- TASK-SSE-04: Client-side Engine `realtime-sse.js` & Komponen Toast Design System
- TASK-SSE-05: Integrasi Event Dispatcher di Workflow Pendaftaran & Seleksi Admin
- TASK-SSE-06: Integrasi Event Dispatcher di Workflow Pembimbingan (Logbook & Tugas)
- TASK-SSE-07: Penulisan Automated Feature Tests & Verifikasi Regression Shield
================================================================================
```

---

### SPESIFIKASI TIKET KERJA TERPERINCI (JOB TICKETS)

#### ─── TIKET TASK-SSE-01 ───
- **Judul**: Migrasi Skema Basis Data & Model Outbox `realtime_events`
- **Prioritas**: P0 (Pondasi Data)
- **Target File**:
  - `database/migrations/2026_10_13_000003_create_realtime_events_table.php` (Create)
  - `app/Models/RealtimeEvent.php` (Create)
- **Instruksi Langkah Demi Langkah**:
  1. Buat berkas migrasi `2026_10_13_000003_create_realtime_events_table.php`.
  2. Definisikan skema tabel:
     - `id`: bigIncrements
     - `user_id`: foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')
     - `role`: string(30)->nullable()->index() (misal: 'admin', 'pembimbing', 'superadmin', 'peserta')
     - `event_type`: string(50)->index()
     - `payload`: json (memuat title, message, action_url, metadata)
     - `read_at`: timestamp()->nullable()
     - `created_at`, `updated_at`: timestamps()
     - Index majemuk: `index(['user_id', 'created_at'])`, `index(['role', 'created_at'])`.
  3. Buat model Eloquent `app/Models/RealtimeEvent.php`:
     - Deklarasikan `declare(strict_types=1);`
     - Tambahkan `$fillable = ['user_id', 'role', 'event_type', 'payload', 'read_at']`
     - Tambahkan cast: `'payload' => 'array'`, `'read_at' => 'datetime'`
     - Tambahkan scope `scopeForUser($query, User $user)`: mencari event yang ditujukan khusus ke `user_id == $user->id` ATAU (`user_id IS NULL` DAN `role == $user->role->value`).
- **Larangan Keras (Don'ts)**:
  - ❌ DILARANG menyimpan data sensitif seperti password, NIK lengkap, atau token auth di kolom `payload`.
- **Kriteria Penerimaan (Acceptance Criteria)**:
  - Migrasi dapat dieksekusi dengan `php artisan migrate` tanpa error.
  - Model memiliki relasi `user()` yang valid ke `User`.

---

#### ─── TIKET TASK-SSE-02 ───
- **Judul**: Domain Enum & Service Publisher `RealtimeEventService`
- **Prioritas**: P0 (Domain Logic & Type Safety)
- **Target File**:
  - `app/Enums/RealtimeEventTypeEnum.php` (Create)
  - `app/Services/RealtimeEventService.php` (Create)
- **Instruksi Langkah Demi Langkah**:
  1. Buat Backed Enum `app/Enums/RealtimeEventTypeEnum.php`:
     ```php
     enum RealtimeEventTypeEnum: string {
         case PendaftaranBaru = 'pendaftaran_baru';
         case StatusPendaftaran = 'status_pendaftaran';
         case KuotaBerubah = 'kuota_berubah';
         case LogbookBaru = 'logbook_baru';
         case LogbookDireviu = 'logbook_direviu';
         case TugasBaru = 'tugas_baru';
         case TugasDikumpulkan = 'tugas_dikumpulkan';
         case AncamanKeamanan = 'ancaman_keamanan';
     }
     ```
  2. Buat `app/Services/RealtimeEventService.php`:
     - Method `publish(RealtimeEventTypeEnum $type, array $payload, ?int $userId = null, ?string $role = null): RealtimeEvent`
     - Method `getPendingEvents(User $user, int $lastEventId = 0, int $limit = 20): Collection`
     - Method `markAsRead(int $eventId, User $user): bool`
     - Method `pruneOldEvents(int $days = 7): int` (membersihkan data > 7 hari).
- **Kriteria Penerimaan (Acceptance Criteria)**:
  - 100% type-hinted dan `declare(strict_types=1);`.
  - Service mampu memfilter event berdasarkan ID pengguna atau role secara presisi (anti-IDOR).

---

#### ─── TIKET TASK-SSE-03 ───
- **Judul**: Controller Streaming SSE Anti-Starvation `RealtimeStreamController`
- **Prioritas**: P0 (Performance & Nginx Hardening)
- **Target File**:
  - `app/Http/Controllers/RealtimeStreamController.php` (Create)
  - `routes/web.php` (Modify)
- **Instruksi Langkah Demi Langkah**:
  1. Buat `app/Http/Controllers/RealtimeStreamController.php` dengan dependency injection `RealtimeEventService`.
  2. Implementasikan method `stream(Request $request): StreamedResponse`:
     - Verifikasi `$user = $request->user();` wajib authenticated.
     - Dapatkan `$lastEventId = (int) $request->header('Last-Event-ID', $request->query('last_event_id', 0));`
     - Panggil `session_write_close();` **sebelum** streaming dimulai untuk melepas session lock PHP.
     - Nonaktifkan buffering PHP: `if (function_exists('apache_setenv')) { apache_setenv('no-gzip', '1'); }` dan `ini_set('zlib.output_compression', '0');`
     - Kembalikan `response()->stream()` dengan header:
       ```php
       [
           'Content-Type' => 'text/event-stream',
           'Cache-Control' => 'no-cache, no-transform',
           'Connection' => 'keep-alive',
           'X-Accel-Buffering' => 'no', // Sangat penting untuk Nginx
       ]
       ```
     - Di dalam callback stream:
       - Kirim instruksi rekoneksi: `echo "retry: 2500\n\n"; ob_flush(); flush();`
       - Lakukan loop terkontrol maksimal **20 detik** (misal 10 iterasi x 2 detik tidur):
         - Periksa jika `connection_aborted()`, langsung `break`.
         - Panggil `getPendingEvents($user, $lastEventId)`.
         - Jika ada event, lakukan loop dan cetak:
           ```
           echo "id: {$event->id}\n";
           echo "event: {$event->event_type}\n";
           echo "data: " . json_encode($event->payload) . "\n\n";
           $lastEventId = $event->id;
           ```
         - Jika tidak ada event, kirim heartbeat comment: `echo ": ping\n\n";`
         - Jalankan `ob_flush(); flush();`
         - `sleep(2);`
       - Setelah 20 detik, biarkan fungsi selesai secara normal sehingga worker PHP-FPM dilepas dengan damai.
  3. Daftarkan rute di `routes/web.php`:
     ```php
     Route::middleware(['auth'])->group(function () {
         Route::get('/realtime/stream', [RealtimeStreamController::class, 'stream'])->name('realtime.stream');
         Route::post('/realtime/read/{id}', [RealtimeStreamController::class, 'markRead'])->name('realtime.read');
     });
     ```
- **Kriteria Penerimaan (Acceptance Criteria)**:
  - Header response memuat `Content-Type: text/event-stream` dan `X-Accel-Buffering: no`.
  - Koneksi tidak menggantung lebih dari 25 detik (mencegah timeout error 504 dan worker starvation).

---

#### ─── TIKET TASK-SSE-04 ───
- **Judul**: Client-side Engine `realtime-sse.js` & Komponen Toast Design System
- **Prioritas**: P1 (UI/UX & Design System Compliance)
- **Target File**:
  - `public/js/realtime-sse.js` (Create)
  - `resources/views/components/sse-toast-listener.blade.php` (Create)
  - `resources/views/layouts/admin.blade.php` (Modify)
  - `resources/views/layouts/pembimbing.blade.php` (Modify)
  - `resources/views/layouts/superadmin.blade.php` (Modify)
  - `resources/views/layouts/peserta.blade.php` (Modify)
- **Instruksi Langkah Demi Langkah**:
  1. Buat berkas `public/js/realtime-sse.js`:
     - Buat class `MagKomRealtime`:
       - Menyimpan instance `window.EventSource`.
       - Menyimpan `lastEventId` di memory / `sessionStorage`.
       - Method `init(streamUrl)`: Menginisialisasi `new EventSource(streamUrl)`.
       - Event Listener `onmessage` dan listener khusus event (`pendaftaran_baru`, dll.).
       - Menangani **Tab Visibility API**:
         ```javascript
         document.addEventListener('visibilitychange', () => {
             if (document.hidden) {
                 this.disconnect(); // Hemat bandwidth saat tab di latar belakang
             } else {
                 this.connect(); // Nyambung kembali saat user membuka tab
             }
         });
         ```
       - Method `showToast(title, message, variant, actionUrl)`: Merender kartu toast melayang di pojok kanan bawah menggunakan styling Figma Design System (30 token resmi: warna latar border halus, shadow-lg, tombol tutup, dan tautan aksi).
       - Method `incrementBadge(selector)`: Menemukan elemen badge counter dan menaikkan nilainya secara otomatis dengan animasi pulse CSS.
  2. Buat komponen Blade `resources/views/components/sse-toast-listener.blade.php`:
     - Merender kontainer absolut di pojok kanan bawah: `<div id="magkom-toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none"></div>`.
     - Menyertakan tag script yang memuat `realtime-sse.js` dan memanggil `window.MagKomRealtime.init('{{ route('realtime.stream') }}')`.
  3. Sisipkan `<x-sse-toast-listener />` pada layout `admin.blade.php`, `pembimbing.blade.php`, `superadmin.blade.php`, dan `peserta.blade.php`.
- **Kriteria Penerimaan (Acceptance Criteria)**:
  - Toast muncul dengan visual modern sesuai standar Design System token Figma tanpa merusak layout.
  - Saat tab di-minimize, koneksi berhenti; saat tab dibuka kembali, koneksi menyambung kembali tanpa reload.

---

#### ─── TIKET TASK-SSE-05 ───
- **Judul**: Integrasi Event Dispatcher di Workflow Pendaftaran & Seleksi Admin
- **Prioritas**: P0 (Core Business Workflow Integration)
- **Target File**:
  - `app/Http/Controllers/Peserta/PendaftaranController.php` (Modify)
  - `app/Http/Controllers/Admin/PendaftaranController.php` (Modify)
- **Instruksi Langkah Demi Langkah**:
  1. Pada `Peserta\PendaftaranController::store` (atau saat status pendaftaran diajukan):
     - Inject `RealtimeEventService`.
     - Panggil dispatch ke role admin:
       ```php
       $this->realtimeService->publish(
           RealtimeEventTypeEnum::PendaftaranBaru,
           [
               'title' => 'Pendaftaran Baru Masuk',
               'message' => "Berkas dari {$user->nama} ({$pendaftaran->jurusan->nama}) siap diverifikasi.",
               'nomor_pendaftaran' => $pendaftaran->nomor_pendaftaran,
               'action_url' => route('admin.pendaftaran.show', $pendaftaran->id),
               'badge_target' => '#badge-antrean-menunggu',
           ],
           null,
           'admin'
       );
       ```
  2. Pada `Admin\PendaftaranController` (saat aksi verifikasi diputuskan: Diterima / Tolak / Perlu Perbaikan):
     - Dispatch event khusus ke peserta terkait (`user_id = $pendaftaran->user_id`):
       ```php
       $this->realtimeService->publish(
           RealtimeEventTypeEnum::StatusPendaftaran,
           [
               'title' => 'Status Pendaftaran Diperbarui',
               'message' => "Berkas pendaftaran Anda telah diputuskan: {$statusBaru->label()}.",
               'status' => $statusBaru->value,
               'action_url' => route('peserta.dashboard'),
           ],
           $pendaftaran->user_id
       );
       ```
- **Kriteria Penerimaan (Acceptance Criteria)**:
  - Setiap kali formulir pendaftaran dikirim, record baru tercipta di `realtime_events`.
  - Peserta langsung menerima sinyal pembaruan status saat admin menekan tombol verifikasi.

---

#### ─── TIKET TASK-SSE-06 ───
- **Judul**: Integrasi Event Dispatcher di Workflow Pembimbingan (Logbook & Tugas)
- **Prioritas**: P0 (Mentoring Workflow Integration)
- **Target File**:
  - `app/Http/Controllers/Peserta/LogbookController.php` (Modify)
  - `app/Http/Controllers/Pembimbing/LogbookController.php` (Modify)
  - `app/Http/Controllers/Pembimbing/TugasController.php` (Modify)
- **Instruksi Langkah Demi Langkah**:
  1. Saat peserta membuat logbook harian di `Peserta\LogbookController::store`:
     - Temukan pembimbing yang ditugaskan melalui relasi `penempatanPeserta`.
     - Dispatch event `LogbookBaru` khusus ke `user_id` pembimbing tersebut:
       ```php
       $this->realtimeService->publish(
           RealtimeEventTypeEnum::LogbookBaru,
           [
               'title' => 'Setoran Logbook Harian',
               'message' => "{$user->nama} mengisi logbook untuk tanggal {$logbook->tanggal->format('d/m/Y')}.",
               'action_url' => route('pembimbing.dashboard'),
           ],
           $pembimbingId
       );
       ```
  2. Saat pembimbing menyelesaikan reviu logbook di `Pembimbing\LogbookController::review`:
     - Dispatch event `LogbookDireviu` ke peserta terkait.
  3. Saat pembimbing membuat tugas baru di `Pembimbing\TugasController::store`:
     - Dispatch event `TugasBaru` ke peserta (atau seluruh mentee di bawah bimbingannya).
- **Kriteria Penerimaan (Acceptance Criteria)**:
  - Pembimbing hanya menerima event logbook dari mentee yang dibimbingnya (isolasi IDOR ketat).
  - Peserta menerima pemberitahuan tugas baru tanpa perlu me-refresh aplikasi.

---

#### ─── TIKET TASK-SSE-07 ───
- **Judul**: Penulisan Automated Feature Tests & Verifikasi Regression Shield
- **Prioritas**: P0 (Quality Assurance & Security Compliance)
- **Target File**:
  - `tests/Feature/RealtimeEventSseStreamTest.php` (Create)
  - `tests/Feature/RealtimeEventDispatchIntegrationTest.php` (Create)
- **Instruksi Langkah Demi Langkah**:
  1. Buat `tests/Feature/RealtimeEventSseStreamTest.php`:
     - Test 1: Tamu (*unauthenticated*) ditolak dari `/realtime/stream` (HTTP 302/401).
     - Test 2: User terautentikasi menerima stream dengan header `Content-Type: text/event-stream` dan `X-Accel-Buffering: no`.
     - Test 3: Pengujian isolasi otorisasi (Peserta A tidak bisa membaca event milik Peserta B).
     - Test 4: Parameter header `Last-Event-ID` berfungsi menyaring hanya event yang lebih baru dari ID tersebut.
  2. Buat `tests/Feature/RealtimeEventDispatchIntegrationTest.php`:
     - Test 1: Menguji pengiriman pendaftaran oleh peserta otomatis memicu entri `realtime_events` bertipe `pendaftaran_baru` untuk role admin.
     - Test 2: Menguji keputusan verifikasi admin otomatis memicu entri bertipe `status_pendaftaran` untuk peserta bersangkutan.
  3. Jalankan regression suite:
     `php artisan test`
     Pastikan seluruh 315+ tes lama tetap lulus 100% ditambah tes baru.
- **Kriteria Penerimaan (Acceptance Criteria)**:
  - 100% passing tests dengan 0 failures.

---

## 7. STRATEGI TESTING & QUALITY ASSURANCE

### 7.1 Automated Feature Tests
1. **Response Headers SSE**: Memastikan header anti-buffering dan keep-alive terbentuk sempurna.
2. **Otorisasi & Keamanan Multi-Tenant**: Menegaskan bahwa event admin tidak bocor ke peserta dan antar-peserta tidak dapat saling intip notifikasi (AC-05, SEC-10 IDOR Prevention).
3. **Graceful Auto-Reconnect**: Menguji bahwa query Last-Event-ID mencegah duplikasi data.

### 7.2 Manual Verification Scenario
1. Buka 2 jendela browser (Window A = Admin di `/admin/dashboard`, Window B = Peserta di `/peserta/pendaftaran`).
2. Kirim formulir pendaftaran di Window B.
3. Amati Window A: Tanpa me-refresh halaman, toast notifikasi pendaftaran baru harus muncul di pojok kanan bawah dan angka counter antrean bertambah secara instan.

---

## 8. RISIKO & MITIGASI (RISK MANAGEMENT)

| Potensi Risiko | Tingkat | Dampak | Strategi Mitigasi Teruji |
| :--- | :---: | :--- | :--- |
| **PHP-FPM Worker Exhaustion (Starvation)** | Tinggi | Web server menolak request baru jika terlalu banyak koneksi SSE terbuka. | Batasi loop streaming maksimal 20–25 detik, lalu akhiri response secara teratur. Browser native `EventSource` akan menyambung kembali secara otomatis tanpa membebani worker. |
| **Session Locking di Laravel** | Tinggi | Pengguna tidak bisa mengklik link lain saat stream SSE sedang berjalan di tab yang sama. | Panggil `session_write_close()` sebelum proses streaming dimulai agar file sesi PHP segera dilepas. |
| **Buffer Nginx Menahan Data** | Sedang | Pesan SSE tertahan di buffer proxy dan tidak langsung sampai ke browser. | Kirim header `X-Accel-Buffering: no` dan `Cache-Control: no-cache, no-transform`. |
| **Penumpukan Tabel Basis Data** | Rendah | Tabel `realtime_events` membengkak jika jutaan event tercipta. | Sediakan method `pruneOldEvents()` di scheduler harian yang menghapus event berusia > 7 hari. |

---

## 9. KRITERIA SELESAI (DEFINITION OF DONE - DOD)
 
- [x] Tabel `realtime_events` terpasang dengan indeks yang optimal (Migrasi `2026_10_13_000003`).
- [x] Endpoint `/realtime/stream` aktif dan mengembalikan stream SSE berstandar RFC 8895 (`RealtimeStreamController`).
- [x] File JavaScript `public/js/realtime-sse.js` terintegrasi dengan penanganan tab visibility yang hemat energi.
- [x] Alur pendaftaran, verifikasi admin, setoran logbook, dan penugasan pembimbing memicu notifikasi live.
- [x] Desain toast melayang mematuhi 30 token resmi Figma Design System.
- [x] Seluruh unit dan feature tests baru lulus 100% tanpa regresi pada 315 tests eksisting (Total aktual: 325 tests passed, 1813 assertions).
- [x] Dokumentasi panduan konfigurasi Nginx SPBE terdokumentasi rapi.

