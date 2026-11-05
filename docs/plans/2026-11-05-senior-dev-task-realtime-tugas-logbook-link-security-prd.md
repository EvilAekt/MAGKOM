# PRD & Implementation Plan: Integrasi Real-Time Dua Arah & Pengerasan Keamanan Tautan (Anti-Crash, HTTPS Enforced, Anti-SSRF, & XSS Sanitization)

**Dokumen**: Task Panduan Senior Developer untuk Junior Developer / AI Agent (Tier Gemini 3.8 Flash)  
**Status**: Draf Perencanaan Siap Eksekusi  
**Versi**: 1.0  
**Tanggal**: 05 November 2026  
**Stack Baseline**: PHP 8.3, Laravel 10.x, Vanilla JS ES Modules, Vite, MySQL 8.4, PHPUnit

---

## 1. Executive Summary & Audit Temuan Eksisting

### 1.1 Hasil Pengecekan Sistem (Fakta di Lapangan)
Berdasarkan audit kode pada backend dan frontend MAGKOM:
1. **Arsitektur Real-Time Dasar Telah Tersedia**:
   - Sistem sudah memiliki `App\Services\RealtimeEventService`, model `RealtimeEvent`, tabel `realtime_events` (Outbox pattern), dan endpoint streaming `/realtime/stream` berbasis Server-Sent Events (SSE).
   - Frontend sudah memiliki engine klien `resources/js/services/realtime.js` (`MagKomRealtimeEngine`) yang mendengarkan event SSE dan menampilkan *toast alert* serta suara notifikasi.
2. **Kesenjangan (GAP) yang Ditemukan**:
   - **Tugas - Pengembalian / Revisi Progres**: Di `Pembimbing\TugasController::reviewProgres()`, ketika pembimbing memberikan catatan revisi (`status_review = perlu_revisi` atau `diterima`), **SAMA SEKALI BELUM ADA event SSE yang dipublikasikan ke peserta**. Peserta tidak tahu tugasnya dikembalikan kecuali me-refresh halaman secara manual.
   - **Logbook - Catatan Kurang / Perlu Revisi**: Di `Pembimbing\LogbookReviewController::review()`, event dipublikasikan (`LogbookDireviu`), tetapi **payload-nya buta konteks (statis)**—tidak memuat status (`perlu_revisi` vs `disetujui`) dan tidak memuat `catatan_pembimbing`. Peserta tidak tahu apa catatannya.
   - **Logbook - Bulk Review**: Pada `Pembimbing\LogbookReviewController::bulkReview()`, **tidak ada event realtime sama sekali**.
   - **Logbook - Resubmit / Perbaikan Logbook**: Ketika peserta memperbarui logbook hasil revisi di `Peserta\LogbookController::update()`, **tidak ada notifikasi realtime kembali ke pembimbing**.
   - **Keamanan Tautan (Link Validation & Crash Prevention)**:
     - Di `ProgresLogRequest`, field `link` hanya divalidasi dengan `'nullable', 'url', 'max:2048'`. **TIDAK ada kewajiban protokol HTTPS**, tidak ada sanitasi terhadap URL lokal/internal (SSRF), dan tidak ada sanitasi protokol berbahaya (`javascript:`, `data:`).
     - Di `LogbookRequest`, regex HTTPS sudah ada, namun belum menangani URL rusak dengan *leading/trailing whitespace*, karakter unicode aneh, atau tautan internal yang dapat memicu crash saat diproses peramban/backend.
     - Pada tampilan Blade/UI, tautan yang diklik oleh pembimbing/peserta belum memiliki pelindung keamanan standar (`rel="noopener noreferrer nofollow"`), membuka celah *reverse tab-napping* dan *referrer leakage*.

---

## 2. Arsitektur Solusi & Standar Keamanan

### 2.1 Alur Real-Time Dua Arah (Closed-Loop Lifecycle)

```
[Peserta] --(1. Unggah Progres / Logbook)--> [Pembimbing Diberitahu Real-Time (SSE)]
    ^                                                          |
    |                                                          v
[Peserta Diberitahu Real-Time (SSE)] <--(2. Beri Catatan / Kembalikan Revisi)-- [Pembimbing]
    |                                                          |
    v                                                          v
(Muncul Toast Khusus Revisi + Link Cepat)               (Status Antrean Berubah Real-Time)
```

### 2.2 Event Contract Matrix (SSE Payloads)

| Nama Event (`event_type`) | Penerima | Pemicu (Trigger) | Isi Payload Wajib |
| :--- | :--- | :--- | :--- |
| `progres_direviu` *(Baru)* | `peserta_id` | Pembimbing submit reviu progres tugas | `title`, `message`, `status_review`, `feedback`, `tugas_id`, `tugas_judul`, `action_url` |
| `logbook_direviu` *(Update)* | `peserta_id` | Pembimbing submit reviu logbook harian | `title`, `message`, `status`, `catatan_pembimbing`, `logbook_id`, `tanggal`, `action_url` |
| `logbook_baru` *(Update)* | `pembimbing_id` | Peserta isi baru ATAU perbarui logbook revisi | `title`, `message`, `peserta_nama`, `tipe_aksi` (baru/revisi), `logbook_id`, `action_url` |
| `tugas_dikumpulkan` *(Update)*| `pembimbing_id`| Peserta kirim progres tugas baru/revisi | `title`, `message`, `peserta_nama`, `tugas_id`, `tugas_judul`, `action_url` |

### 2.3 Standar Keamanan Tautan (Anti-Crash, Anti-SSRF, Safe Protocol)
Setiap input tautan (`link`, `link_laporan`, `link_referensi`) wajib melewati aturan validasi 5 lapis:
1. **Trim & Normalisasi**: Menghilangkan spasi liar (*leading/trailing whitespace*) secara otomatis pada `prepareForValidation()`.
2. **Protokol Wajib HTTPS**: Menggunakan aturan regex `/^https:\/\//i`. Menolak mentah-mentah protokol `http://`, `ftp://`, `javascript:`, `data:`, `file:`.
3. **Anti-SSRF (Server-Side Request Forgery & Private IP Block)**:
   - Menolak hostname internal/privat: `localhost`, `127.0.0.1`, `0.0.0.0`, `169.254.169.254` (cloud metadata), subnet privat RFC 1918 (`10.*`, `172.16-31.*`, `192.168.*`), dan TLD `.local`/`.internal`.
4. **Validasi Sintaks Domain (RFC 3986)**: Menggunakan `filter_var(..., FILTER_VALIDATE_URL)`.
5. **Safe Rendering di Blade**: Seluruh link eksternal yang dirender pada Blade wajib menggunakan:
   ```html
   <a href="{{ e($safeUrl) }}" target="_blank" rel="noopener noreferrer nofollow" class="...">
   ```

---

## 3. Rincian Task Senior Developer (Langkah-demi-Langkah untuk Junior / AI)

### Task 1: Tambahkan Enum Baru pada `App\Enums\RealtimeEventTypeEnum`
- **File**: `app/Enums/RealtimeEventTypeEnum.php`
- **Instruksi**:
  Tambahkan case `ProgresDireviu`:
  ```php
  case ProgresDireviu = 'progres_direviu';
  ```
  Dan daftarkan di method `label()`:
  ```php
  self::ProgresDireviu => 'Umpan Balik Progres Tugas Mentee',
  ```

---

### Task 2: Buat Helper / Rule Validasi Tautan Aman (`SafeExternalUrlRule`)
- **File Baru**: `app/Rules/SafeExternalUrlRule.php`
- **Tujuan**: Memastikan link valid, menggunakan HTTPS, bukan IP lokal/SSRF, dan aman diklik tanpa membuat crash sistem.
- **Logika Validasi**:
  1. Pastikan string valid URL via `filter_var($value, FILTER_VALIDATE_URL)`.
  2. Parse URL dengan `parse_url($value)`. Jika `false`, tolak dengan pesan "Format tautan tidak dapat diurai".
  3. Cek scheme: wajib `https`. Jika bukan, tolak: "Tautan wajib menggunakan protokol aman HTTPS (https://)".
  4. Cek host:
     - Host tidak boleh kosong.
     - Host tidak boleh `localhost`, `*.test`, `*.local`, `*.internal`.
     - Host tidak boleh berupa IP privat (`FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE`).
  5. Batas panjang URL maksimal 2048 karakter.

---

### Task 3: Terapkan `SafeExternalUrlRule` pada Seluruh Form Request Terkait
1. **`app/Http/Requests/ProgresLogRequest.php`**:
   - Pada `prepareForValidation()`: lakukan `trim()` pada field `link`.
   - Pada `rules()`:
     ```php
     'link' => ['nullable', 'string', 'max:2048', new \App\Rules\SafeExternalUrlRule()],
     ```
2. **`app/Http/Requests/LogbookRequest.php`**:
   - Pada `prepareForValidation()`: lakukan `trim()` pada field `link_laporan`.
   - Pada `rules()`:
     ```php
     'link_laporan' => ['nullable', 'string', 'max:2048', new \App\Rules\SafeExternalUrlRule()],
     ```
3. **`app/Http/Requests/TugasRequest.php`**:
   - Pada `prepareForValidation()`: lakukan `trim()` pada field `link_referensi`.
   - Pada `rules()`:
     ```php
     'link_referensi' => ['nullable', 'string', 'max:2048', new \App\Rules\SafeExternalUrlRule()],
     ```

---

### Task 4: Perbarui Backend Controller untuk Event Real-Time Tertarget

#### 4.1. `app/Http/Controllers/Pembimbing/TugasController.php` (Method: `reviewProgres`)
- **Lokasi**: Baris 494–525.
- **Tindakan**:
  Setelah `$progres->update(...)` dan `$this->auditService->catat(...)`, tambahkan publikasi event realtime ke peserta:
  ```php
  $pesertaUser = $tugas->penempatan?->peserta;
  if ($pesertaUser) {
      $statusLabel = $progres->status_review === \App\Enums\StatusReviewProgresEnum::PerluRevisi
          ? 'Perlu Revisi'
          : 'Diterima';

      $pesan = $progres->status_review === \App\Enums\StatusReviewProgresEnum::PerluRevisi
          ? "Pembimbing meminta revisi tugas \"{$tugas->judul}\": {$progres->feedback}"
          : "Laporan progres tugas \"{$tugas->judul}\" telah diterima oleh pembimbing.";

      app(\App\Services\RealtimeEventService::class)->publish(
          \App\Enums\RealtimeEventTypeEnum::ProgresDireviu,
          [
              'title' => "Evaluasi Tugas: {$statusLabel}",
              'message' => $pesan,
              'status_review' => $progres->status_review->value,
              'feedback' => $progres->feedback,
              'tugas_id' => $tugas->id,
              'action_url' => route('peserta.tugas.show', $tugas->id),
              'badge_target' => 'tugas-revisi-badge',
          ],
          $pesertaUser->id
      );
  }
  ```

#### 4.2. `app/Http/Controllers/Pembimbing/LogbookReviewController.php` (Method: `review` & `bulkReview`)
- **Method `review()`**:
  Perkaya payload event agar tidak buta status:
  ```php
  $pesertaId = $logbook->penempatan?->peserta_id;
  if ($pesertaId) {
      $isRevisi = $logbook->status === \App\Enums\StatusLogbookEnum::PerluRevisi;
      $title = $isRevisi ? 'Logbook Memerlukan Revisi' : 'Logbook Harian Disetujui';
      $message = $isRevisi && filled($logbook->catatan_pembimbing)
          ? "Catatan Pembimbing: {$logbook->catatan_pembimbing}"
          : "Catatan logbook tanggal {$logbook->tanggal->translatedFormat('d F Y')} telah dinilai ({$logbook->status->label()}).";

      app(\App\Services\RealtimeEventService::class)->publish(
          \App\Enums\RealtimeEventTypeEnum::LogbookDireviu,
          [
              'title' => $title,
              'message' => $message,
              'status' => $logbook->status->value,
              'catatan_pembimbing' => $logbook->catatan_pembimbing,
              'logbook_id' => $logbook->id,
              'action_url' => route('peserta.logbook.index'),
              'badge_target' => $isRevisi ? 'logbook-revisi-badge' : null,
          ],
          $pesertaId
      );
  }
  ```
- **Method `bulkReview()`**:
  Tambahkan notifikasi realtime per peserta yang terkena dampak persetujuan massal.

#### 4.3. `app/Http/Controllers/Peserta/LogbookController.php` (Method: `update`)
- **Lokasi**: Method `update()`, setelah logbook diperbarui ke status `Terkirim`.
- **Tindakan**:
  Kirimkan notifikasi realtime ke pembimbing bahwa peserta telah mengirimkan revisi:
  ```php
  $pembimbingId = $logbook->penempatan?->pembimbing_id;
  if ($pembimbingId) {
      $user = $request->user();
      app(\App\Services\RealtimeEventService::class)->publish(
          \App\Enums\RealtimeEventTypeEnum::LogbookBaru,
          [
              'title' => 'Revisi Logbook Masuk',
              'message' => "{$user->nama} telah memperbarui logbook revisi tanggal {$logbook->tanggal->translatedFormat('d F Y')}.",
              'action_url' => route('pembimbing.dashboard'),
              'badge_target' => 'antrean-logbook-badge',
          ],
          $pembimbingId
      );
  }
  ```

---

### Task 5: Perbarui Frontend SSE Engine & Toast Renderer
- **File**: `resources/js/services/realtime.js`
- **Instruksi**:
  1. Tambahkan event `'progres_direviu'` ke dalam array listener `connect()`:
     ```javascript
     const events = [
         'pendaftaran_baru',
         'status_pendaftaran',
         'kuota_berubah',
         'logbook_baru',
         'logbook_direviu',
         'tugas_baru',
         'tugas_dikumpulkan',
         'progres_direviu', // Tipe event baru
         'ancaman_keamanan',
         'telemetri_route'
     ];
     ```
  2. Di method `getEventVisualConfig(eventType)`:
     - Jika event adalah `progres_direviu` atau `logbook_direviu` dengan status `perlu_revisi`, berikan warna border kuning/amber (`border-amber-500 bg-amber-50 text-amber-900`) dan ikon peringatan agar mentee langsung menyadari tugas/logbook-nya perlu perbaikan.
  3. Di method `showToast()`:
     - Periksa validitas `actionUrl` menggunakan fungsi parser URL yang aman (anti-crash): jika URL rusak atau bukan dimulai dari `/` atau `https://`, abaikan tanpa membuat aplikasi crash.

---

### Task 6: Audit Tautan pada Template Blade (Anti-Crash & Anti-Tabnapping)
- **File Terkait**:
  - `resources/views/pembimbing/tugas/partials/_stream_card.blade.php` (dan show)
  - `resources/views/peserta/tugas/show.blade.php`
  - `resources/views/peserta/logbook/partials/_table.blade.php`
- **Instruksi**:
  Setiap elemen `<a>` yang merender `$progres->link` atau `$logbook->link_laporan`:
  ```blade
  @if($progres->link)
      <a href="{{ e($progres->link) }}"
         target="_blank"
         rel="noopener noreferrer nofollow"
         class="inline-flex items-center gap-1.5 text-blue-700 hover:text-blue-900 font-semibold underline text-sm"
         title="Buka Tautan Tugas">
          <svg ...></svg>
          <span>Buka Tautan Pengerjaan</span>
      </a>
  @endif
  ```
  Ini menjamin tidak ada celah XSS atau tabnapping.

---

## 4. Quality Gate & Acceptance Criteria

### 4.1 Unit & Feature Testing (PHPUnit)
Wajib membuat file test baru: `tests/Feature/RealtimeTugasLogbookFeedbackTest.php` yang memverifikasi:
1. `test_pembimbing_review_progres_menerbitkan_event_realtime_progres_direviu_ke_peserta()`:
   - Pembimbing memberi status `perlu_revisi` + `feedback`.
   - Event `progres_direviu` tersimpan di database outbox `realtime_events` dengan `user_id == peserta_id`.
2. `test_pembimbing_review_logbook_menerbitkan_event_berisi_catatan_revisi()`:
   - Pembimbing memberi catatan revisi logbook.
   - Event `logbook_direviu` memuat isi `catatan_pembimbing` dan status `perlu_revisi`.
3. `test_peserta_update_logbook_revisi_menerbitkan_event_ke_pembimbing()`:
   - Peserta menyimpan perbaikan logbook.
   - Event `logbook_baru` dengan judul "Revisi Logbook Masuk" diterbitkan ke pembimbing.
4. `test_validasi_tautan_menolak_http_biasa_dan_ip_lokal_ssrf()`:
   - Kirim link `http://contoh.com` $\to$ Ditolak (wajib HTTPS).
   - Kirim link `https://localhost/exploit` $\to$ Ditolak (anti-SSRF).
   - Kirim link `https://127.0.0.1:8000` $\to$ Ditolak.
   - Kirim link `javascript:alert(1)` $\to$ Ditolak.
   - Kirim link `   https://drive.google.com/file/d/abc/view   ` $\to$ Berhasil di-trim dan diterima.

### 4.2 Quality Checks
- `npm run build`: Lolos tanpa error kompilasi.
- `./vendor/bin/pint --test`: 0 pelanggaran format kode.
- `./vendor/bin/phpstan analyse --memory-limit=1G`: 0 error tipe data.
- Seluruh 109 test eksisting Pembimbing wajib tetap 100% HIJAU.

