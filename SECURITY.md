# Kebijakan Keamanan (Security Policy)
## MagKom — Portal Manajemen Magang Dinas Komunikasi dan Informatika

Dinas Komunikasi dan Informatika Kabupaten Sleman berkomitmen untuk menjaga keamanan sistem informasi kedinasan dan kerahasiaan data pribadi para pendaftar magang sesuai dengan amanat **Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)**.

---

## 1. Versi yang Didukung (Supported Versions)

Pembaruan keamanan dan perbaikan celah kerentanan secara aktif diberikan pada versi rilis berikut:

| Versi | Didukung Secara Aktif | Keterangan |
| :---: | :---: | :--- |
| **1.1.x** | :white_check_mark: Ya | Versi produksi stabil saat ini |
| **1.0.x** | :warning: Terbatas | Hanya pembaruan keamanan kritis |
| **< 1.0** | :x: Tidak | Versi prototipe / tahap pengembangan awal |

---

## 2. Pelaporan Kerentanan (Reporting a Vulnerability)

Jika Anda menemukan celah keamanan (*vulnerability*), kerentanan data pribadi, atau anomali sistem pada aplikasi **MagKom**, mohon untuk **TIDAK** mempublikasikannya secara terbuka melalui *GitHub Public Issues* atau media sosial.

Kami sangat menghargai prinsip *Responsible Disclosure*. Silakan laporkan temuan Anda secara rahasia melalui saluran resmi berikut:

- **Surel Tim Keamanan Siber**: `kominfo@slemankab.go.id`
- **Subjek Surel**: `[SECURITY REPORT] - <Judul Kerentanan Singkat>`

### Informasi yang Dibutuhkan dalam Laporan
Mohon sertakan detail yang memadai agar tim kami dapat mereproduksi dan memvalidasi temuan Anda:
1. Deskripsi singkat mengenai jenis kerentanan (misal: IDOR, Stored XSS, Broken Access Control, Logic Flaw).
2. Langkah-langkah detail untuk mereproduksi masalah (*Proof of Concept / PoC*).
3. URL endpoint, method HTTP, dan payload parameter yang terdampak.
4. Estimasi tingkat keparahan (*impact assessment*) terhadap kerahasiaan (*confidentiality*), integritas (*integrity*), atau ketersediaan (*availability*) data.
5. Usulan perbaikan atau remediasi teknis jika tersedia.

---

## 3. Komitmen & Waktu Tanggap (SLA)

- **Konfirmasi Awal**: Tim teknis kami akan mengonfirmasi penerimaan laporan Anda dalam kurun waktu **maksimal 2x24 jam kerja**.
- **Analisis & Remediasi**: Penilaian risiko mendalam dan penyusunan *patch* perbaikan akan diprioritaskan berdasarkan skor keparahan (CVSS).
- **Publikasi Perbaikan**: Setelah perbaikan berhasil diuji dan diterapkan pada sistem produksi, rilis pembaruan akan dicatat pada `CHANGELOG.md`.

---

## 4. Standar Pertahanan Siber yang Diterapkan

Sistem MagKom dibangun dengan menerapkan mitigasi pengamanan:
1. **SEC-01 (CSRF)**: Verifikasi token CSRF ketat pada setiap interaksi HTTP bermetode non-idempotent.
2. **SEC-02 (IDOR & Otorisasi Objek)**: Pengecekan otorisasi berbasis kepemilikan data melalui Laravel Policy pada setiap akses dokumen, berkas persyaratan, dan tugas.
3. **SEC-03 (SQL Injection)**: Penegakan parameter binding PDO berbasis Eloquent ORM.
4. **SEC-04 (XSS & Content Security)**: Pengamanan rendering HTML melalui auto-escaping Blade dan penegakan header Content Security Policy (CSP).
5. **SEC-05 (Brute Force Protection)**: Pembatasan laju (*rate limiting*) berlapis pada seluruh pintu masuk autentikasi dan API.
6. **SEC-06 (Atomic Concurrency Locking)**: Pengamanan transaksi alokasi kuota menggunakan `lockForUpdate()` untuk mencegah eksploitasi perebutan kuota (*race condition*).
