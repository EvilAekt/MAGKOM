# Ikhtisar Keamanan Sistem (Security Overview)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | Pertahanan berlapis, model ancaman THR-01 s/d THR-16, mitigasi SEC-01 s/d SEC-06 |
| Referensi Implementasi | `app/Http/Middleware/SecurityHeaders.php`, `tests/Feature/SecurityHeadersProductionTest.php` |

---

## 1. Filosofi Pertahanan Berlapis (Defense-in-Depth)

Keamanan informasi pada MAGKOM dirancang untuk melindungi kerahasiaan (*confidentiality*), integritas (*integrity*), dan ketersediaan (*availability*) data permohonan magang kedinasan sesuai amanat **Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)**.

Sistem menerapkan strategi pertahanan berlapis pada seluruh tingkatan:
1. **Network & Ingress**: Penegakan HTTPS terenkripsi, konfigurasi trusted proxies anti-IP spoofing, dan pembatasan laju (*rate limiting*).
2. **HTTP Header Security**: Kebijakan Content Security Policy (CSP) ketat, perlindungan clickjacking, dan pencegahan MIME sniffing.
3. **Application & Code**: Zero inline script pada seluruh antarmuka Blade, parameter binding PDO bawaan Eloquent (anti-SQL injection), perlindungan CSRF, dan otorisasi kepemilikan objek (anti-IDOR via Policies).
4. **Data & Storage**: Isolasi berkas privat non-publik ber-signed URL (TTL 5 menit), transaksi atomik penguncian kuota (`lockForUpdate`), dan rekam jejak audit forensik append-only dengan penyensoran data sensitif.

---

## 2. Enam Standar Mitigasi Keamanan Inti (SEC-01 s/d SEC-06)

| Kode Mitigasi | Target Perlindungan | Implementasi Teknis | Verifikasi Pengujian |
|---|---|---|---|
| **SEC-01** | Cross-Site Request Forgery (CSRF) | Verifikasi token CSRF ketat pada setiap permintaan HTTP non-idempotent (`POST`, `PUT`, `DELETE`). | `CoreFoundationTest` |
| **SEC-02** | Insecure Direct Object Reference (IDOR) | Pengecekan otorisasi berbasis kepemilikan data melalui 5 kelas Laravel Policy pada seluruh berkas, tugas, dan logbook. | `DocumentSecurityTest`, `TugasSecurityTest` |
| **SEC-03** | SQL Injection (SQLi) | Penegakan parameter binding PDO via Eloquent ORM; pelarangan konkatenasi string SQL mentah. | `DatabaseSecurityTest` |
| **SEC-04** | Cross-Site Scripting (XSS) & Kepatuhan CSP | Header CSP `script-src 'self'` tanpa `unsafe-inline`; penegakan 0 skrip inline di seluruh 185 Blade view. | `SecurityHeadersProductionTest` |
| **SEC-05** | Brute Force & Password Spraying | Pembatasan laju multi-tier pada rute login dan registrasi berbasis IP dan kombinasi akun (`throttle:login`). | `LoginAggregateThrottleTest` |
| **SEC-06** | Quota Race Condition | Transaksi basis data dengan penguncian pesimistik (`lockForUpdate()`) saat pemotongan dan pengembalian kuota. | `QuotaConcurrencyTest` |

---

## 3. Matriks Model Ancaman (Threat Models THR-01 s/d THR-16)

Sistem memetakan dan memitigasi 16 model ancaman potensial:

1. **THR-01 (Pencurian Berkas Pribadi KTP/KTM)**: Mitigasi: Penyimpanan pada disk privat non-publik `storage/app/private/` dengan akses URL bertanda tangan HMAC (TTL 5 menit).
2. **THR-02 (IDOR pada Berkas Peserta Lain)**: Mitigasi: `DokumenPolicy` memeriksa apakah `pendaftaran.user_id === auth()->id()`.
3. **THR-03 (Eksploitasi Kuota Minus / Race Condition)**: Mitigasi: Penguncian baris `SELECT ... FOR UPDATE` dalam transaksi database.
4. **THR-04 (Injeksi Skrip XSS via Deskripsi Tugas)**: Mitigasi: Auto-escaping Blade `{{ ... }}` dan sanitasi teks HTML melalui `TugasDeskripsiSecurityTest`.
5. **THR-05 (Bypass CSP via Inline Script/Handler)**: Mitigasi: Penghapusan total tag `<script>`, `<style>`, dan atribut `onclick` dari 185 Blade views.
6. **THR-06 (Brute Force Kredensial Login)**: Mitigasi: Rate limiter bertingkat dan generator captcha dinamis SVG internal.
7. **THR-07 (Pemalsuan IP Klien via Header Forwarded)**: Mitigasi: Konfigurasi `TrustProxies` yang membatasi proxy tepercaya secara ketat.
8. **THR-08 (Manipulasi Nilai Parameter Sistem)**: Mitigasi: Validasi rentang nilai pada Form Request dan isolasi wewenang wewenang SuperAdmin.
9. **THR-09 (Bypass Otorisasi Pembimbing Lapangan)**: Mitigasi: `PenempatanPolicy` membatasi akses pembimbing hanya untuk mentee binaan resmi.
10. **THR-10 (Intervensi SuperAdmin pada Ranah Tugas)**: Mitigasi: Middleware `EnsureSuperAdminReadOnly` memblokir mutasi tugas/logbook oleh SuperAdmin.
11. **THR-11 (Kebocoran Kredensial pada Log Audit)**: Mitigasi: Sanitasi regex rekursif otomatis pada `AuditService` untuk seluruh kata kunci rahasia.
12. **THR-12 (Manipulasi Jejak Audit Forensik)**: Mitigasi: Larangan mutasi `UPDATE` dan `DELETE` pada tabel `audit_log` (append-only enforcement).
13. **THR-13 (Formula Injection pada Ekspor Spreadsheet)**: Mitigasi: Sanitasi prefix kutip tunggal (`'`) pada sel yang diawali karakter berbahaya via `XlsxExportService`.
14. **THR-14 (Eksekusi Berkas Berbahaya / Webshell)**: Mitigasi: Validasi ekstensi dan MIME type berkas, penyimpanan di luar web root publik.
15. **THR-15 (Akses Tidak Sah Pasca-Magang)**: Mitigasi: Penonaktifan otomatis akun peserta setelah masa tenggang 2 bulan (`EnsureParticipantWritable`).
16. **THR-16 (Ketiadaan Kesiapan Pemulihan Bencana)**: Mitigasi: Modul pencadangan basis data instan format `.sql.gz` dengan verifikasi hash integritas SHA-256.

---

## 4. Konfigurasi Header Keamanan HTTP (SecurityHeaders Middleware)

Seluruh respons HTTP aplikasi diinjeksi dengan header keamanan terstandarisasi:

```http
Content-Security-Policy: default-src 'self'; script-src 'self' https://challenges.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com data:; img-src 'self' data: https:; connect-src 'self';
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
```

- **`script-src 'self'`**: Mencegah pemuatan skrip eksternal tak dikenal atau eksekusi skrip inline ilegal di browser klien.
- **`X-Frame-Options: SAMEORIGIN`**: Melindungi portal dari serangan Clickjacking / UI Redressing.
- **`X-Content-Type-Options: nosniff`**: Mencegah browser mengeksekusi berkas non-executable yang disamarkan sebagai gambar atau teks.
