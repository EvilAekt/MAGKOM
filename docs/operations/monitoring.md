# Pemantauan Sistem & Telemetri Diagnostik (Monitoring)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Modul Sistem | `sistem.telemetri-kesehatan` |
| Rute Terkait | `/health`, `/superadmin/dashboard`, `/realtime/stream` |
| Referensi Implementasi | `app/Services/SecuritySweepService.php`, `app/Http/Controllers/Public/HealthController.php` |

---

## 1. Pendahuluan

Pemantauan sistem pada MAGKOM dirancang untuk menyajikan visibilitas kesehatan operasional server, latensi kueri basis data, integritas disk penyimpanan privat, serta pelacakan kesalahan runtime (*error tracking*) secara real-time.

---

## 2. Titik Akhir Kesehatan Sistem (Health Check Endpoint)

MAGKOM menyediakan endpoint publik `/health` yang dapat diakses oleh layanan monitoring eksternal (seperti UptimeRobot, Prometheus, atau load balancer kedinasan):

```http
GET /health HTTP/1.1
Host: magang.kominfo.slemankab.go.id
```

### Format Respon Sukses (HTTP 200 OK):
```json
{
  "status": "ok",
  "timestamp": "2026-11-15T12:00:00+07:00",
  "environment": "production",
  "services": {
    "database": {
      "status": "connected",
      "latency_ms": 2.4
    },
    "storage": {
      "status": "writable",
      "free_space_mb": 14250.0
    }
  }
}
```

Jika koneksi basis data terputus, endpoint mengembalikan kode status **HTTP 503 Service Unavailable** tanpa membocorkan kredensial atau stack trace internal.

---

## 3. Cockpit Telemetri SuperAdmin

Pada panel `/superadmin/dashboard`, SuperAdmin dapat memantau:
1. **Latensi Koneksi HTTP & DB**: Waktu respons eksekusi kueri MySQL.
2. **Utilisasi Disk Privat**: Kapasitas berkas dokumen pendaftar pada `storage/app/private/`.
3. **Koneksi Realtime SSE Aktif**: Jumlah aliran stream Server-Sent Events yang sedang tersambung.

---

## 4. Empat Aksi Diagnostik Server (Server Diagnostic Actions)

SuperAdmin dapat menjalankan 4 aksi pemeliharaan preventif langsung melalui antarmuka web:

| Aksi Diagnostik | Kelas Pelaksana | Tindakan yang Dijalankan |
|---|---|---|
| **Security Sweep** | `SecuritySweepService` | Memindai status `APP_DEBUG`, memeriksa kerentanan eksposur berkas `.env`, memvalidasi perizinan folder storage (775), dan memeriksa kepatuhan header CSP. |
| **Optimize Database** | `DatabaseInspectorService` | Menjalankan `OPTIMIZE TABLE` pada 24 tabel domain aplikasi untuk mengompresi indeks InnoDB dan membuang fragmentasi data. |
| **Clear Cache** | `Artisan::call('optimize:clear')` | Mengosongkan cache aplikasi, cache rute, cache view, dan cache konfigurasi secara aman. |
| **Storage Test** | `DocumentStorageService` | Menguji operasi tulis dan baca (*I/O benchmark*) pada disk privat untuk mendeteksi potensi kerusakan filesystem server. |

---

## 5. Integrasi Pemantauan Kesalahan (Sentry Error Tracking)

Aplikasi mengintegrasikan pustaka resmi `sentry/sentry-laravel` untuk menangkap crash report dan exception yang tidak tertangani di lingkungan produksi:

### Konfigurasi `.env`:
```env
SENTRY_LARAVEL_DSN="https://kunci-rahasia@sentry.kominfo.slemankab.go.id/1"
SENTRY_TRACES_SAMPLE_RATE=0.2
```

### Informasi yang Ditangkap:
- **Konteks Pengguna**: `id`, `role` (kata sandi dan data pribadi disensor secara otomatis melalui `send_default_pii=false`).
- **Rute HTTP**: URL endpoint, HTTP method, status code.
- **Breadcrumbs**: Jejak pemanggilan query SQL sebelum exception terjadi.

---

## 6. Strategi Logging Produksi & Rotasi Berkas (Daily Log Rotation)

Untuk mencegah pembengkakan berkas `laravel.log` (seperti temuan audit awal berukuran 28.7 MB), lingkungan produksi menerapkan strategi rotasi harian otomatis:

### Konfigurasi Direkomendasikan:
```env
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_DAILY_DAYS=14
LOG_LEVEL=warning
```

### Karakteristik & Kebijakan Keamanan Log:
1. **Rotasi Harian (Daily Rotation):** Berkas log otomatis dirotasi per hari (`laravel-YYYY-MM-DD.log`) dan disimpan maksimal 14 hari (`days = 14`), mencegah exhaustion kapasitas disk server.
2. **Level Log Produksi (`warning`):** Di production, level disetel ke `warning` atau `error` agar log tidak dipenuhi trace debug rutin, namun tetap merekam kegagalan autentikasi, ancaman keamanan, dan fatal error.
3. **Penyensoran Data Sensitif:** Kata sandi, token reset, kode verifikasi, session cookies, dan informasi kredensial sensitif dilarang dicatat ke dalam log. Nilai sensitif wajib disamarkan (`[REDACTED]`).
4. **Integrasi Sentry Monolog:** Channel `sentry` telah didaftarkan pada Monolog. Administrator dapat menggabungkan logging lokal dan cloud dengan menyetel `LOG_STACK=daily,sentry`.

---

## 7. Tata Kelola Konkurensi Realtime SSE

Endpoint streaming notifikasi `/realtime/stream` menerapkan batasan konkurensi:
- **Maksimal 1 Koneksi per Pengguna**: Mencegah penumpukan thread worker PHP-FPM akibat multiple tab browser yang dibuka oleh pengguna yang sama (`RealtimeStreamConcurrencyTest`).
- **Heartbeat Interval**: Mengirimkan sinyal ping setiap 15 detik untuk menjaga koneksi tetap hidup melalui proxy dan firewall.
