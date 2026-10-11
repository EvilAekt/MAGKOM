# Plan: Penyempurnaan Dashboard SuperAdmin Lengkap Sesuai PRD, TDD, Roadmap & Pusat Monitoring Keamanan Sistem

- **Tanggal**: 2026-10-10
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning)

---

## 1. Tujuan

Menyempurnakan antarmuka dan backend **Dashboard SuperAdmin MagKom** secara komprehensif agar mencakup seluruh metrik keberhasilan produk (PRD Bab 2.3), aturan bisnis (PRD Bab 5 & 8), hak akses eksklusif SuperAdmin (PRD Bab 3 & 4.3), serta menambahkan **Pusat Monitoring Keamanan Sistem (System Security & Threat Monitoring Hub)** yang detail dan real-time sesuai PRD Bab 10 (NFR-S1 s/d NFR-S10) dan TDD Bab 8 & 9.

Penyempurnaan ini mentransformasikan Dashboard SuperAdmin menjadi **Pusat Komando Eksekutif & Keamanan Siber Terpadu (Executive & Security Mission Control)** yang menyatukan tata kelola operasional magang dengan pemantauan postur keamanan, proteksi berkas sensitif, deteksi anomali login, dan integritas audit trail append-only.

---

## 2. Asumsi & Pertanyaan Terbuka

- **Asumsi**:
  1. SuperAdmin memerlukan visibilitas langsung terhadap 4 Metrik Keberhasilan Utama dari PRD Bab 2.3 (Integritas Kuota 0 Pelanggaran, SLA Waktu Verifikasi Administrasi <= 5 Hari Kerja, Kelulusan dengan Lembar Nilai 100%, dan Keterlacakan Audit Log 100%).
  2. Pusat Monitoring Keamanan Sistem ditempatkan secara ergonomis pada **Tab 4 (Tata Kelola & Keamanan Sistem)** serta widget status keamanan ringkas di **Hero Section / Tab Ringkasan**.
  3. Kunci pengaturan database disinkronkan dengan standar resmi PRD Bab 8.6 (`logbook_maks_mundur_hari`, `masa_tenggang_bulan`, `kuota_umum_maks_per_bulan`, `slot_khusus_default`, `berkas_maks_mb`, `predikat_batas`) dengan fallback aman.
  4. Desain antarmuka tetap konsisten dengan palet Sleman Blue 700 (`#0641CC`), layout segmented tabs Figma Node 560-26404, dan bebas dari scrollbar abu-abu yang mengganggu.
- **Pertanyaan Terbuka**: Tidak ada ambiguitas kritis; seluruh aturan merujuk langsung ke PRD, TDD, dan Roadmap resmi MagKom.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Teknis:

| Aspek | Opsi A: Menambahkan Menu Keamanan Terpisah | Opsi B: Unified Dashboard & Security Hub (Terpilih) |
| :--- | :--- | :--- |
| **Aksesibilitas Eksekutif** | SuperAdmin harus membuka tab baru untuk memeriksa status keamanan dan operasional secara bergantian. | Seluruh metrik operasional dan postur keamanan terpantau di satu dashboard terpadu dengan navigasi tab instan (0 reload). |
| **Kecepatan Deteksi Ancaman** | Lambat, potensi anomali login atau kegagalan isolasi disk tidak segera disadari. | Deteksi real-time: indikator ancaman login, status disk privat, dan kepatuhan sandi staf langsung terlihat saat SuperAdmin membuka dashboard. |
| **Kesesuaian PRD Bab 10 & TDD Bab 8** | Parsial di level command/middleware. | Divisualisasikan secara penuh sebagai panel kendali postur keamanan sistem. |

---

### Fitur-Fitur Baru yang Ditambahkan ke Dashboard SuperAdmin:

#### A. Pusat Monitoring Keamanan Sistem (System Security & Threat Hub - PRD Bab 10 & TDD Bab 8-9):
1. **Radar Percobaan Intrusi & Anomali Login (Auth Threat & Brute Force Radar)**:
   - Hitungan insiden login gagal dalam 24 jam terakhir (dari tabel `audit_log` tipe `auth` aksi `login_gagal`).
   - Indikator kepatuhan Rate Limiting login (AC-02-5: batas 5 percobaan gagal per menit).
   - Log insiden login mencurigakan terkini (IP penyerang, target email, timestamp, status mitigasi).
2. **Higienitas Kredensial & Kepatuhan Kata Sandi (Credential Posture & BR-15 Enforcement)**:
   - Indikator staf internal (Admin / Pembimbing / SuperAdmin) yang belum mengganti kata sandi awal bawaan (`must_change_password = true`).
   - Rasio akun aktif vs nonaktif dan proteksi pencegahan akses akun nonaktif (AC-02-8).
3. **Integritas Vault Dokumen Sensitif (Private Storage Vault Health - NFR-S2 & AC-03-5)**:
   - Verifikasi status isolasi berkas KTP, proposal, dan transkrip di disk privat (bebas akses URL langsung).
   - Status mekanisme unduhan dokumen: hanya melalui *signed temporary URL* berumur 5 menit (AC-04-2).
   - Pengujian integritas validasi format berkas berbasis magic bytes & batas 2 MB (PRD Bab 8.4).
4. **Postur Proteksi HTTP & Kebijakan Header (Security Headers & OWASP Defense - NFR-S8)**:
   - Pemantauan status aktif middleware keamanan:
     - `X-Frame-Options: DENY` (Proteksi Clickjacking)
     - `X-Content-Type-Options: nosniff` (Proteksi MIME Sniffing)
     - `Referrer-Policy: strict-origin-when-cross-origin`
     - Content Security Policy (CSP) & Perlindungan CSRF Token pada mutasi data.
5. **Integritas Imutabilitas Audit Trail (Append-Only & Anti-Tampering - NFR-S6 & AC-13-6)**:
   - Verifikasi bahwa log audit berstatus *append-only* (proteksi hak database anti-UPDATE & anti-DELETE).
   - Pemeriksaan sanitasi data sensitif: memastikan 0 kebocoran kata sandi, token, atau secret pada seluruh rekaman audit log (AC-13-5).
   - Distribusi tipe audit trail: `auth`, `aksi`, `service`, `akses_data` (AC-13-1 s/d AC-13-4).
6. **Status Proteksi Bot & Rate Limiting (Bot Defense - AC-14-1 & NFR-S7)**:
   - Status integrasi Cloudflare Turnstile / Captcha.
   - Status Rate Limiter (Auth 5 req/min, Registrasi 5 req/min, Pendaftaran 10 req/jam).

#### B. Panel Metrik Keberhasilan Produk & KPI Dinas (PRD Bab 2.3):
1. **Integritas Kuota (Target 0 Kejadian)**: Query diagnosa otomatis `terisi > jumlah` pada tabel `kuota` dan `slot_khusus`. Menampilkan badge hijau "0 Pelanggaran (100% Aman)" atau alert darurat jika ada anomali.
2. **SLA Kecepatan Verifikasi Admin (Target <= 5 Hari Kerja)**: Perhitungan rata-rata durasi `diputuskan_at - diajukan_at` pada bulan aktif, plus indikator jumlah berkas yang menunggu melebihi 5 hari kerja (peringatan SLA).
3. **Kepatuhan Penilaian Kelulusan (Target 100%)**: Indikator penempatan selesai yang telah memiliki nilai final 100% vs yang masih pending nilai.
4. **Keterlacakan Audit Trail (Target 100%)**: Persentase aksi tulis yang tercatat di audit trail.

#### C. Jadwal Kalender Pendaftaran & Countdown Periode (PRD F-01, F-11, BR-04):
- Menampilkan tanggal buka s/d tutup pendaftaran (`tgl_buka` - `tgl_tutup`), sisa hari pendaftaran aktif, atau tanggal pembukaan berikutnya untuk status "Segera".
- Quick switch status override landing page langsung dari toolbar atas.

#### D. Pengawasan Pelaksanaan & Tugas Terlambat (PRD F-08, BR-16):
- Indikator tugas **Terlambat (Overdue)** di Tab Bimbingan (tugas yang melewati deadline dan belum selesai).
- Indikator tugas mendekati deadline (<= 3 hari) sesuai BR-16.
- Pelacak peserta yang mendekati akhir masa dinas magang (<= 14 hari) untuk peringatan persiapan penilaian akhir oleh Pembimbing (*Off-boarding Tracker*).

#### E. Transparansi Slot Khusus Kemitraan (PRD F-06):
- Detail utilisasi slot kemitraan khusus instansi/MoU Pemkab Sleman dan verifikasi isolasi data dari landing page publik (AC-01-5).

#### F. Penyelarasan Parameter Konfigurasi Resmi PRD Bab 8.6:
- Menyelaraskan pembacaan kunci konfigurasi ke standar resmi PRD: `logbook_maks_mundur_hari` (3 hari), `masa_tenggang_bulan` (1 bulan), `berkas_maks_mb` (2 MB), dan `predikat_batas` (skala A-E).

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`app/Http/Controllers/SuperAdmin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/DashboardController.php) | Modify | 1. Tambahkan kalkulasi metrik keamanan: insiden login gagal 24 jam terakhir, daftar log anomali auth terkini, status isolasi storage berkas privat, higienitas sandi staf (BR-15), status append-only audit log, dan status middleware header keamanan.<br>2. Tambahkan kalkulasi KPI PRD 2.3: integritas kuota (`terisi > jumlah`), SLA rata-rata verifikasi berkas dan jumlah berkas > 5 hari, kelulusan nilai final 100%.<br>3. Tambahkan hitungan tugas terlambat (overdue), tugas mendekati deadline (BR-16), dan peserta mendekati akhir magang (off-boarding).<br>4. Tambahkan distribusi tipe audit log (`auth`, `aksi`, `service`, `akses_data`).<br>5. Sinkronkan pembacaan konfigurasi dengan nama kunci PRD Bab 8.6. |
| [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php) | Modify | 1. Tambahkan panel **Pusat Monitoring Keamanan Sistem (Security Operations Center)** di Tab 4 (Tata Kelola & Keamanan): radar anomali login, higienitas kredensial staf, status vault berkas privat, status security headers, dan imutabilitas audit trail.<br>2. Tambahkan badge status keamanan ringkas di Hero Toolbar atas.<br>3. Tambahkan panel **Pusat Metrik Keberhasilan Produk & KPI Dinas (PRD Bab 2.3)** di Tab Ringkasan Utama.<br>4. Tambahkan info jadwal kalender pendaftaran (`tgl_buka` - `tgl_tutup`) dan sisa waktu aktif.<br>5. Tambahkan kartu pengawasan tugas terlambat dan off-boarding di Tab Bimbingan.<br>6. Perjelas kartu slot khusus kemitraan di Tab Seleksi.<br>7. Tampilkan parameter resmi sesuai PRD Bab 8.6. |
| [`tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php) | Modify | Tambahkan test cases komprehensif untuk memverifikasi metrik monitoring keamanan sistem (login failure radar, credential hygiene, private vault status, security headers), KPI PRD 2.3, dan tugas terlambat. |

---

## 5. Langkah Implementasi

- [ ] **Langkah 1**: Perbarui Backend di `app/Http/Controllers/SuperAdmin/DashboardController.php`:
  - **Monitoring Keamanan**:
    - Hitung `$loginGagal24Jam`: AuditLog tipe `auth` aksi `login_gagal` dalam 24 jam terakhir.
    - Ambil `$insidenKeamananTerbaru`: 5 catatan login gagal atau percobaan akses ilegal terkini.
    - Hitung `$stafPendingGantiPassword`: Staf aktif dengan `must_change_password = true` (BR-15).
    - Status `$privateVaultStatus`: Validasi direktori `storage/app/private`, pemeriksaan isolasi symlink publik, dan ketersediaan temporary signed URL.
    - Status `$securityHeadersStatus`: Kesiapan middleware `SecurityHeaders` (X-Frame-Options, CSP, HSTS, X-Content-Type-Options).
    - Status `$auditImmutability`: Pemeriksaan integritas append-only audit trail dan sanitasi kredensial.
    - Sebaran `$auditTipeDistribution`: Hitungan audit log berdasarkan 4 tipe (`auth`, `aksi`, `service`, `akses_data`).
  - **KPI Produk PRD Bab 2.3**:
    - Hitung `$anomaliKuotaCount`: Jumlah kuota atau slot khusus yang `terisi > jumlah`.
    - Hitung `$rataRataSlaVerifikasiHari`: Rata-rata selisih hari pendaftaran diajukan s/d diputuskan.
    - Hitung `$antreanMelebihiSla`: Berkas berstatus Diajukan yang telah menunggu > 5 hari kerja.
    - Hitung `$penempatanSelesaiTanpaNilai`: Penempatan selesai yang belum memiliki penilaian final (target: 0).
  - **Pengawasan Tugas & Kalender**:
    - Hitung `$totalTugasTerlambat`: Tugas dengan deadline lewat dan belum selesai.
    - Hitung `$totalTugasMendekatiDeadline`: Tugas dengan deadline <= 3 hari ke depan (BR-16).
    - Hitung `$pesertaMendekatiSelesai`: Peserta aktif dengan `tgl_selesai <= now() + 14 hari`.
    - Sinkronkan parameter PRD Bab 8.6 (`logbook_maks_mundur_hari`, `masa_tenggang_bulan`, `berkas_maks_mb`, `predikat_batas`).
- [ ] **Langkah 2**: Perbarui Tampilan Hero & Toolbar di `resources/views/superadmin/dashboard.blade.php`:
  - Tampilkan jadwal kalender pendaftaran (`tgl_buka` - `tgl_tutup`), sisa hari pendaftaran aktif, dan badge ringkas "Postur Keamanan: Prima".
- [ ] **Langkah 3**: Perbarui Tab 1 (Ringkasan Utama):
  - Tambahkan kartu "Pusat Metrik Keberhasilan Produk & KPI Dinas (PRD Bab 2.3)":
    - Integritas Kuota (0 Pelanggaran).
    - SLA Kecepatan Verifikasi Admin (Target <= 5 Hari Kerja).
    - Kelulusan dengan Lembar Nilai (Target 100%).
    - Keterlacakan Audit Trail (Target 100%).
- [ ] **Langkah 4**: Perbarui Tab 2 (Seleksi & Kuota):
  - Tambahkan penanda antrean yang melewati batas SLA (> 5 hari kerja).
  - Tampilkan rincian slot khusus kemitraan MoU Pemkab Sleman dan verifikasi isolasi publik.
- [ ] **Langkah 5**: Perbarui Tab 3 (Pelaksanaan & Bimbingan):
  - Tambahkan kartu tugas terlambat (*overdue*) dan tugas mendekati tenggat (BR-16).
  - Tambahkan widget pelacak peserta mendekati akhir magang (14 hari) untuk persiapan penilaian.
- [ ] **Langkah 6**: Perbarui Tab 4 (Tata Kelola & Keamanan Sistem):
  - Tambahkan panel komprehensif **Pusat Monitoring Keamanan Sistem (Security Operations Center)**:
    - Radar Deteksi Anomali Login & Brute Force (24 Jam).
    - Higienitas Kredensial & Kepatuhan Ganti Sandi Staf (BR-15).
    - Status Vault Penyimpanan Berkas Privat (NFR-S2).
    - Status Perlindungan Header Keamanan HTTP (NFR-S8).
    - Status Integritas Audit Trail Append-Only & Sanitasi Rahasia (AC-13-5, AC-13-6).
    - Sebaran Volume Audit Trail per Kategori PRD (auth, aksi, service, akses_data).
  - Tampilkan kartu parameter resmi sesuai PRD Bab 8.6.
- [ ] **Langkah 7**: Tambahkan pengujian otomatis di `tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`:
  - Test metrik monitoring keamanan (login anomaly radar, credential hygiene, private vault status, security headers).
  - Test KPI PRD Bab 2.3 (integritas kuota, SLA verifikasi, tugas terlambat).
- [ ] **Langkah 8**: Jalankan seluruh automated test suite (`php artisan test`) untuk memastikan *zero regression*.
- [ ] **Langkah 9**: Verifikasi visual di browser untuk memastikan layout responsif, elegan, dan bebas dari scrollbar abu-abu.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - `php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`
   - `php artisan test tests/Feature/SuperAdminFigmaDesignAlignmentTest.php`
   - `php artisan test tests/Feature/SuperAdminDashboardAndAuditLogTest.php`
   - `php artisan test tests/Feature/DashboardModulesRenderingTest.php`
2. **Skenario Pengujian Unit & Security Monitoring**:
   - Uji radar login gagal saat ada aktivitas login salah kredensial.
   - Uji indikator kepatuhan kata sandi saat ada staf dengan `must_change_password = true`.
   - Uji verifikasi integritas kuota (0 anomali pada kondisi normal vs alert merah saat diuji kuota minus).
   - Uji deteksi tugas terlambat saat ada tugas melewati deadline.
   - Uji kalkulasi SLA verifikasi dan berkas > 5 hari.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat Dampak | Strategi Mitigasi |
| :--- | :---: | :--- |
| **Kueri audit log besar membebani dashboard** | Rendah | Menggunakan filter rentang waktu `where('waktu', '>=', now()->subDay())` dan limit record terindeks (`take(5)`). |
| **Pendaftaran baru belum ada putusan (SLA null)** | Rendah | Fallback aman jika belum ada pendaftaran yang diputuskan (tampilkan "- Hari"). |
| **UI terlalu padat informasi** | Rendah | Mengelompokkan metrik keamanan secara modular ke dalam sub-panel bergrid bersih dengan badge visual yang jelas. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Panel Pusat Monitoring Keamanan Sistem terpasang lengkap di Tab Tata Kelola & Keamanan (radar anomali login, higienitas kredensial staf, status vault berkas privat, status header keamanan HTTP, dan audit immutability).
- [x] 4 KPI Keberhasilan Produk PRD Bab 2.3 terpasang dan terhitung otomatis di Tab Ringkasan.
- [x] Jadwal buka-tutup pendaftaran dan sisa hari pendaftaran aktif tampil di hero/toolbar.
- [x] Peringatan SLA verifikasi berkas (> 5 hari) aktif di Tab Seleksi.
- [x] Pengawasan tugas terlambat (overdue) dan peserta mendekati batas selesai (off-boarding) aktif di Tab Bimbingan.
- [x] Distribusi tipe audit trail (auth, aksi, service, akses_data) aktif.
- [x] Seluruh parameter sistem sinkron dengan PRD Bab 8.6.
- [x] 100% automated test suite lulus tanpa regresi (11 tests / 98 assertions pass di SuperAdminUnifiedDashboardFeaturesTest).

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**:
  - `app/Http/Controllers/SuperAdmin/DashboardController.php`: Menambahkan kalkulasi KPI PRD Bab 2.3, radar anomali login 24 jam, pengawasan tugas terlambat F-08 & BR-16, pelacak peserta magang off-boarding, dan parameter PRD Bab 8.6.
  - `resources/views/superadmin/dashboard.blade.php`: Memasang jadwal pendaftaran di hero toolbar, panel metrik keberhasilan PRD Bab 2.3 di Tab Ringkasan, peringatan SLA verifikasi di Tab Seleksi, pengawasan tugas overdue & peserta mendekati selesai di Tab Bimbingan, dan panel Pusat Monitoring Keamanan Sistem (Threat Radar, 4 metrik keamanan, radar login gagal 24 jam, distribusi 4 domain audit, postur hardening HTTP) di Tab Tata Kelola & Keamanan.
  - `tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`: Memperluas automated test suite hingga 11 tests dan 98 assertions, mencakup verifikasi KPI PRD 2.3, Security Monitoring Hub, dan Pengawasan Tugas/Peserta.
- **Saran Lanjutan (Out of Scope)**:
  - Integrasi webhook alert instan (misal Telegram Bot / Email Alert ke SuperAdmin) jika terdeteksi lonjakan login gagal lebih dari 10 kali dalam 1 jam.
