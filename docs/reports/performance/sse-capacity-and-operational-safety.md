# Laporan Analisis Kapasitas & Keamanan Operasional Server-Sent Events (SSE)

**Project:** MAGKOM (Sistem Informasi Manajemen Magang Kominfo)  
**Endpoint:** `GET /realtime/stream`  
**Task ID:** PERF-001  
**Status:** Sizing & Safety Assessment Verified

---

## 1. Analisis Arsitektur Endpoint `/realtime/stream`

Endpoint `/realtime/stream` pada `App\Http\Controllers\RealtimeStreamController` menyediakan aliran Server-Sent Events (RFC 8895) untuk pembaruan status pendaftaran, perubahan kuota, penugasan, dan notifikasi keamanan secara langsung ke peramban pengguna.

### Karakteristik Eksekusi:
1. **Model Eksekusi:** Menjalankan loop terkontrol hingga maksimal 20 detik (`$maxDuration = 20`) dengan interval tidur 2 detik (`sleep(2)`).
2. **Penggunaan Worker:** Selama loop 20 detik berlangsung, **satu thread PHP-FPM worker tertahan (held busy)** melayani koneksi streaming tersebut.
3. **Mekanisme Reconnect:** Pada akhir durasi 20 detik, koneksi ditutup secara bersih dengan instruksi `retry: 2500` ms, sehingga peramban pengguna akan menyambung kembali secara otomatis setelah jeda 2.5 detik.
4. **Proteksi Anti-Exhaustion Pengguna Tunggal:** Menggunakan cache lock atomik `Cache::add("sse:stream:user:{$userId}", true, 30)`. Jika seorang pengguna membuka multi-tab, tab kedua akan menerima respons `HTTP 429 Too Many Concurrent SSE Connections` dengan header `Retry-After: 10`.
5. **Pelepasan Session Lock:** Memanggil `session_write_close()` sebelum loop dimulai, sehingga navigasi atau klik tombol di tab lain milik pengguna yang sama tidak terblokir oleh session mutex PHP.

---

## 2. Kalkulasi Beban & Analisis Risiko PHP-FPM Worker Starvation

Pada arsitektur synchronous PHP-FPM standar, setiap worker hanya dapat melayani satu HTTP request dalam satu waktu.

### Model Matematis Sizing Worker:
Jika server memiliki $N$ pengguna aktif simultan yang sedang membuka halaman ber-SSE:
- Setiap pengguna menahan worker selama 20 detik dari siklus 22.5 detik (duty cycle $\approx 88.9\%$).
- Jumlah worker yang terserap secara terus menerus untuk SSE adalah $\approx 0.89 \times N$.

### Tabel Skenario Kapasitas Server:

| Konfigurasi Server | `pm.max_children` | Batas Pengguna SSE Simultan | Sisa Worker untuk Web Biasa | Risiko Operasional |
|---|---|---|---|---|
| **Entry VPS (2 GB RAM)** | 15 worker | Maksimal 10 pengguna | 5 worker | **Tinggi:** 12 pengguna membuka dashboard akan menghabiskan seluruh worker. Web reguler akan mengalami antrean (lag/timeout 504). |
| **Standard VM (4 GB RAM)** | 40 worker | Maksimal 25 pengguna | 15 worker | **Sedang:** Cukup untuk operasional harian kantor/dinas internal. |
| **High-Traffic (8 GB+ RAM)** | 100 worker | Maksimal 70 pengguna | 30 worker | **Rendah:** Aman untuk soft-launch terkontrol. |

---

## 3. Rekomendasi Konfigurasi & Mitigasi Operasional

Berdasarkan temuan di atas, tim pengembang memberikan rekomendasi tegas:

### A. Rekomendasi Tahap Soft-Launch / Server Sumber Daya Terbatas (PERF-001)
Jika aplikasi di-deploy pada server dengan RAM $\le$ 4 GB atau pool worker PHP-FPM $\le$ 30:
- **Setel `REALTIME_SSE_ENABLED=false` pada berkas `.env` server.**
- **Mekanisme Polling Fallback:** Ketika SSE dimatikan, endpoint `/realtime/stream` merespons dengan HTTP 503 yang aman dan client JavaScript secara transparan beralih ke `GET /realtime/poll?last_event_id=...`.
- Endpoint polling hanya menjalankan satu kueri database cepat (< 5ms) tanpa `sleep()` ataupun loop, membebaskan worker PHP-FPM seketika.
- Rate limiting diterapkan pada polling (`throttle:60,1`) dan jeda default adalah 15 detik (`REALTIME_POLLING_INTERVAL=15`), sehingga tidak membebani basis data.
- Polling otomatis dihentikan saat tab diminimalkan atau tidak aktif (`visibilitychange`) dan dilanjutkan kembali saat pengguna membuka kembali peramban.

### B. Konfigurasi Nginx Production (FastCGI Socket)
Pastikan Nginx dikonfigurasi tanpa buffering untuk lokasi `/realtime/stream`:
```nginx
location /realtime/stream {
    try_files $uri $uri/ /index.php?$query_string;
    fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root/index.php;
    include fastcgi_params;
    fastcgi_buffering off;
    fastcgi_cache off;
    fastcgi_read_timeout 60s;
    fastcgi_hide_header X-Powered-By;
}
```
> [!IMPORTANT]
> Jangan menggunakan directive `proxy_pass http://127.0.0.1:8000` pada instalasi Nginx + PHP-FPM standar, karena port 8000 tidak terbuka di server production dan akan memicu error `502 Bad Gateway`.

### C. Roadmap Jangka Panjang (Skala Tinggi)
Jika kebutuhan realtime simultan meningkat di atas 100+ pengguna aktif tanpa membebani worker PHP:
- Migrasi event realtime ke asynchronous engine seperti **Laravel Reverb** (WebSocket resmi Laravel) atau **Soketi/Pusher**.
- Atau adopsi **Laravel Octane** (Swoole / RoadRunner) yang tidak berbasis process-per-request blocking.
