# MagKom: Addendum Keamanan dan Pengujian (QA)

Pelengkap `PRD-MagKom.md` v1.1 dan `TDD-MagKom.md` v1.1 · Addendum v1.0 (draft untuk review) · 9 Oktober 2026 · Penyusun dan reviewer: *[diisi]*

## 0. Cara Memakai Dokumen Ini

Dokumen ini hanya memuat **yang belum ada** di PRD dan TDD; isi keduanya tidak diulang. Tujuannya agar senior bisa menyerahkan pekerjaan keamanan dan QA ke junior atau AI berbiaya rendah tanpa tebak-tebakan: setiap butir punya ID, prioritas, cara membuat, dan cara menguji.

**Peta isi.** Bagian A Keamanan (A.1 ancaman, A.2 kebutuhan SEC, A.3 konfigurasi siap pakai, A.4 klasifikasi data, A.5 alarm dan insiden). Bagian B QA (B.1 strategi, B.2 data uji, B.3 matriks hak akses, B.4 katalog uji per AC, B.5 uji keamanan, B.6 uji non-fungsional, B.7 UAT lengkap, B.8 cacat dan gerbang rilis). Bagian C temuan celah dan ketidakjelasan di PRD/TDD. Bagian D pembagian tugas ke junior atau AI.

**Konvensi.** ID baru: `THR-` ancaman, `SEC-` kebutuhan keamanan, `TCS-` uji keamanan, `UAT-9` dan seterusnya, `S1` sampai `S4` tingkat cacat, `C-` temuan. Prioritas: **P0** wajib sebelum go-live R1; **P1** sebaiknya sebelum go-live R1 (bila ditunda, tulis catatan risiko yang disetujui Product Owner); **P2** boleh menyusul di R2. `[Usulan]` berarti sama seperti di PRD: boleh dipakai sampai ada keputusan.

**Bila bertentangan:** perilaku bisnis mengikuti PRD; cara teknis biasa mengikuti TDD; kontrol keamanan mengikuti dokumen ini bila lebih ketat. Laporkan pertentangan, jangan menebak.

**Catatan verifikasi.** Rujukan hukum (UU PDP) dan tanggal dukungan versi (Laravel, PHP) ditulis dari pengetahuan penyusun tanpa pencarian daring. Periksa ulang dengan bagian hukum instansi dan php.net/supported-versions sebelum dijadikan dasar keputusan.

### 0.1 Ringkasan Celah

| Area | Sudah ada di PRD/TDD | Yang kurang |
| --- | --- | --- |
| Ancaman | Pemetaan OWASP Top 10 satu baris per risiko | Model ancaman dan daftar serangan yang wajib diuji (A.1, B.5) |
| Akun dan sesi | Hash, throttle, regenerasi session, respons seragam | Sesi tetap hidup setelah akun dinonaktifkan atau password diganti; password awal staf diketik manual; tidak ada MFA; limit login mudah dilewati |
| Otorisasi | Role, Policy, scope pemilik | Tidak ada Policy dan rute unduh untuk berkas progres dan lampiran logbook; SuperAdmin hanya-baca hanya satu lapis; tidak ada penjaga SuperAdmin terakhir; kolom turunan belum dilarang diisi dari request |
| Berkas | Tipe dari isi, nama acak, disk privat, tautan bertanda tangan | Tanpa pemindaian malware, kuota total, pembersihan berkas lama, konfigurasi disk, batas PHP dan Nginx |
| Web dan server | Daftar header, contoh CSP | Proxy dan host tepercaya, no-store, hardening dompdf, hardening Nginx dan PHP, rencana CSP bertahap, log bebas data pribadi |
| Data pribadi | Persetujuan dan Kebijakan Privasi | Klasifikasi data, data anak (SMK), prosedur hak subjek data, prosedur kebocoran |
| Operasi | Uptime, disk, scheduler | Alarm keamanan, runbook insiden, kebijakan `composer audit` untuk Laravel 10 yang sudah EOL, jadwal upgrade bertanggal |
| QA: rencana | Lapisan uji, DoD, delapan UAT satu baris | Katalog uji per AC dengan kasus batas, data uji, kriteria masuk dan keluar, tingkat cacat, gerbang rilis |
| QA: uji khusus | Konkurensi dan matriks RBAC (baru konsep) | Matriks RBAC konkret, uji keamanan terperinci, performa, aksesibilitas, email, PDF, backup dan restore, tanggal dan zona waktu, invarian data |
| UAT | Delapan skenario satu baris | Skrip langkah demi langkah, hasil yang diharapkan, formulir persetujuan, cara menguji masa tenggang tanpa menunggu sebulan |

---

## A. Keamanan

### A.1 Model Ancaman Ringkas

Aset yang dilindungi: (1) berkas pribadi peserta (rapor, transkrip, CV); (2) integritas kuota dan penempatan; (3) nilai dan Lembar Nilai; (4) akun staf, terutama SuperAdmin; (5) audit log; (6) reputasi domain instansi. Pelaku: pengunjung anonim, peserta jahat atau iseng, staf nakal atau akunnya dibajak, penyerang luar yang memindai server, dan rantai pasok dependensi.

| ID | Pelaku | Serangan | Dampak | Kontrol | Uji |
| --- | --- | --- | --- | --- | --- |
| THR-01 | Peserta | Mengganti ID di URL untuk membuka berkas, nilai, atau logbook orang lain (IDOR) | Kebocoran data pribadi | SEC-10, 11 | TCS-01, 02, 09 |
| THR-02 | Peserta | Menyisipkan `role`, `status`, `kuota_id`, `pembimbing_id` pada request | Naik hak akses, lompat status, ambil kuota | SEC-13, 14 | TCS-03 |
| THR-03 | Luar | Tebak password, credential stuffing, tebak email terdaftar | Akun diambil alih | SEC-01 sampai 08 | TCS-10, 14 |
| THR-04 | Luar | Registrasi massal akun palsu | Antrean tercemar, reputasi email turun | SEC-06, 15, 53 (captcha di R2) | TCS-10, 26 |
| THR-05 | Peserta | Unggah berkas berbahaya (skrip, PDF jahat, malware) | Eksekusi kode, PC staf terinfeksi | SEC-20 sampai 25 | TCS-08 |
| THR-06 | Peserta | Menyisipkan skrip pada nama, kegiatan, catatan yang dibuka staf (XSS tersimpan) | Sesi staf dibajak | SEC-22, 33, 36 | TCS-06, 17 |
| THR-07 | Luar | Host header poisoning pada email reset dan verifikasi | Token reset dicuri | SEC-30 | TCS-13 |
| THR-08 | Peserta atau luar | Permintaan bersamaan pada kuota, keputusan, atau beban Pembimbing | Kuota minus, batas beban terlampaui | TDD 5.3, C-04 | Uji K (B.4) |
| THR-09 | Staf nakal | Mengunduh massal berkas, menyalahgunakan akses | Kebocoran data | SEC-71, audit akses data | TCS-17, alarm |
| THR-10 | Luar | Membajak akun SuperAdmin | Kerusakan total | SEC-02, 05, 56 | TCS-14, 19 |
| THR-11 | Luar | Memanfaatkan kerentanan Laravel 10 yang tidak lagi dipatch | Server diambil alih | SEC-56, 60, 70 | TCS-20, 21 |
| THR-12 | Staf atau penyerang | Mengubah atau menghapus jejak audit | Akuntabilitas hilang | TDD 9.4, SEC-55 | TCS-18 |
| THR-13 | Luar | Memicu error untuk membaca detail internal | Informasi bocor | SEC-35 | TCS-12 |
| THR-14 | Peserta | PDF dan ekspor dipakai menyerang (sumber remote dompdf, formula injection) | SSRF, eksekusi rumus | SEC-34, AC-17-2 | TCS-15, 16 |
| THR-15 | Luar | Memindai server: port DB atau SSH terbuka, `.env` atau `.git` terbaca | Server dikompromi | SEC-50 sampai 55 | TCS-12, 19 |
| THR-16 | Peserta | Komputer bersama: orang berikutnya menekan tombol back setelah logout | Data terlihat | SEC-32 | TCS-04, 11 |

### A.2 Kebutuhan Keamanan (SEC)

Kolom **Uji** merujuk ke TCS di B.5. Kolom **Alasan** menunjuk celah yang ada di PRD/TDD saat ini.

#### A.2.1 Akun dan sesi

| ID | Prio | Kebutuhan dan cara membuatnya | Alasan | Uji |
| --- | --- | --- | --- | --- |
| SEC-01 | P0 | Hash bcrypt dengan `BCRYPT_ROUNDS=12` (bawaan Laravel 10 hanya 10). Password maksimum 64 karakter (bcrypt hanya membaca 72 byte). Peserta: aturan PRD ditambah `Password::uncompromised()` (butuh akses keluar ke layanan pengecekan; bila gagal terhubung, validasi lolos). Staf: minimal 12 karakter | PRD hanya minimal 8, padahal staf melihat data semua peserta | TCS-14 |
| SEC-02 | P0 | Password awal staf dan peserta manual **dibuat acak oleh sistem** (16 karakter dari `random_int`, pasti ada huruf dan angka), ditampilkan **sekali** di layar pembuatnya, tidak disimpan atau dicatat dalam bentuk asli, `must_change_password` aktif. Pembuat tidak mengetik password | PRD 8.2 mewajibkan field password awal yang diketik manual: rawan Magkom123 | TCS-14 |
| SEC-03 | P0 | Middleware `EnsureAccountActive` (A.3) di semua grup rute setelah `auth`: bila `status_aktif=false`, logout paksa dan invalidasi sesi | AC-02-8 hanya menutup login; sesi yang sedang berjalan (staf dinonaktifkan, peserta mengundurkan diri) tetap hidup | TCS-04 |
| SEC-04 | P0 | Aktifkan `AuthenticateSession` di grup `web` dan panggil `Auth::logoutOtherDevices()` saat ganti password; reset password lewat email mematikan semua sesi lama | Tidak ada aturan nasib sesi lama setelah password berganti | TCS-04 |
| SEC-05 | P1 | MFA TOTP untuk SuperAdmin (wajib), Admin dan Pembimbing (sebaiknya, boleh P2). Pengganti murah bila MFA ditunda: SEC-56 | Satu password SuperAdmin bocor berarti seluruh data peserta dan audit terbuka | TCS-14 |
| SEC-06 | P0 | Rate limit login ditambah dua kunci selain `email+IP` (5 per menit): **per email saja** (20 per 10 menit, semua IP) dan **per IP saja** (30 per menit). Pakai IP asli klien (SEC-30) | Serangan terdistribusi dengan banyak IP lolos dari kunci email+IP | TCS-10 |
| SEC-07 | P1 | Staf: kotak remember me dihilangkan; sesi idle 60 menit `[Usulan]` (peserta tetap 120) | Sesi staf berumur sama dengan peserta | TCS-04 |
| SEC-08 | P1 | Lupa password: kirim email **setelah respons** dikirim (`dispatch(fn)->afterResponse()`), agar waktu respons tidak membedakan email terdaftar dan tidak. Soal registrasi: lihat C-01 | NFR-S10 menyeragamkan isi respons, bukan waktunya | TCS-14 |

#### A.2.2 Otorisasi dan integritas data

| ID | Prio | Kebutuhan dan cara membuatnya | Alasan | Uji |
| --- | --- | --- | --- | --- |
| SEC-10 | P0 | Lengkapi Policy dan rute unduh: `ProgresLogPolicy`, ability `download` pada `LogbookPolicy`, rute `GET /berkas/progres/{progres}/unduh` dan `GET /berkas/logbook/{logbook}/unduh` dengan pola sama seperti berkas pendaftaran (auth, signed 5 menit, Policy, nosniff). Pembimbing **tidak** boleh mengunduh berkas pendaftaran (PRD 3.3) | TDD bab 7 hanya punya rute unduh berkas pendaftaran; Policy baru untuk 6 model | TCS-01, 09 |
| SEC-11 | P0 | Rute bersarang memakai scoped binding atau cek kepemilikan eksplisit, misalnya `PUT /pembimbing/tugas/{id}` memastikan tugas milik penempatan bimbingan si pengguna. Dilarang `Model::find($id)` tanpa scope pemilik | Rute berparameter ID adalah sumber IDOR terbanyak | TCS-01, 02 |
| SEC-12 | P0 | SuperAdmin hanya-baca dijaga **dua lapis**: Policy (sudah ada) dan middleware `superadmin.readonly` (A.3) pada seluruh grup `/pembimbing/*`, menolak 403 bagi SuperAdmin pada method selain GET dan HEAD | Satu lapis: lupa `authorize()` di satu Controller sudah membuka akses tulis | TCS-01 |
| SEC-13 | P0 | Controller hanya memakai `$request->validated()`. Nilai turunan **dihitung server**, tidak pernah dari request: `kuota_id` (dari bulan dan jurusan), `status`, `verifikator_id`, `terisi`, `role`, `status_aktif`, `must_change_password`, `email_verified_at`, `provider`, `provider_id`. `pembimbing_id` hanya dari daftar Pembimbing aktif. `$fillable` ketat; `$guarded = []` dilarang (SEC-61) | TDD hanya menyebut `role`; kolom lain sama berbahayanya | TCS-03 |
| SEC-14 | P0 | Penjaga akun staf: SuperAdmin tidak dapat menonaktifkan atau mengubah role dirinya sendiri; harus selalu ada minimal satu SuperAdmin aktif; `role` hanya diisi saat membuat akun (nilai `admin` atau `pembimbing`) dan tidak ada di form ubah | BR-12 hanya menjaga Pembimbing terakhir | TCS-03 |
| SEC-15 | P1 | `jenjang` dan `jurusan` di profil terkunci selama ada pendaftaran aktif (BR-17). Detail pendaftaran di sisi Admin menampilkan peringatan bila NIM/NISN yang sama dipakai akun lain (tanda saja, bukan blokir) | Satu orang dapat memakai banyak akun atau mengganti jenjang agar daftar berkas wajib berubah | TCS-26 |

#### A.2.3 Berkas

| ID | Prio | Kebutuhan dan cara membuatnya | Alasan | Uji |
| --- | --- | --- | --- | --- |
| SEC-20 | P0 | Disk `private` didefinisikan eksplisit (A.3): root di luar `public/`, izin 0600 dan 0700, `throw=true`; tidak ada `storage:link` untuk folder ini | TDD menyebut disk private, tetapi bawaan Laravel 10 tidak punya disk itu | TCS-08 |
| SEC-21 | P0 | Batas ukuran berlapis: Nginx `client_max_body_size 12m`, PHP `upload_max_filesize=10M` dan `post_max_size=12M`, pengaturan `berkas_maks_mb` dibatasi 1 sampai 10. Kesalahan 413 menampilkan pesan berbahasa Indonesia | Pengaturan dapat dinaikkan melebihi batas PHP sehingga unggah gagal tanpa pesan jelas | TCS-08 |
| SEC-22 | P0 | `nama_asli` disanitasi: buang path dan karakter kontrol (termasuk U+202E), potong 100 karakter, tidak pernah dipakai membentuk path, selalu tampil ter-escape | Nama berkas adalah input pengguna yang tampil di halaman Admin dan audit | TCS-08, 17 |
| SEC-23 | P0 | Respons unduh: `Content-Disposition: attachment`, `X-Content-Type-Options: nosniff`, `Cache-Control: private, no-store`. Berkas hilang dari disk: 404 dan log error, bukan 500 | TDD hanya menyebut nosniff | TCS-09 |
| SEC-24 | P1 | Pindai malware dengan ClamAV (`clamdscan`) sinkron saat unggah (paket Laravel yang masih terawat atau panggil socket clamd langsung). Terinfeksi: ditolak dan diaudit. clamd mati: unggah ditolak dengan pesan coba lagi (fail closed) `[Usulan]`. Bila tidak dipasang: catat risiko dan wajibkan staf memakai penampil PDF yang selalu diperbarui | Staf membuka berkas dari internet di PC kantor; PDF dan gambar dapat membawa muatan berbahaya | TCS-08 |
| SEC-25 | P1 | Kuota penyimpanan `[Usulan]`: total 50 MB per pengguna, maksimal 5 berkas progres per tugas, 1 lampiran per entri logbook. Penggantian berkas (AC-03-7) menghapus berkas lama dari disk **setelah** transaksi sukses; hash lama tetap di audit | Unggah 20 per menit tanpa batas total dapat memenuhi disk | TCS-08 |

#### A.2.4 Aplikasi web

| ID | Prio | Kebutuhan dan cara membuatnya | Alasan | Uji |
| --- | --- | --- | --- | --- |
| SEC-30 | P0 | Tentukan ada tidaknya reverse proxy, CDN, atau WAF di depan Nginx. **Ada:** `TrustProxies` diisi IP proxy itu saja (bukan `*`). **Tidak ada:** biarkan kosong dan pastikan header `X-Forwarded-*` dari klien diabaikan. Aktifkan `TrustHosts` (hanya domain resmi) dan `default_server` Nginx yang menolak Host asing (A.3). Opsional di produksi: `URL::forceRootUrl(config('app.url'))` | IP palsu merusak rate limit dan audit; Host palsu meracuni tautan reset dan verifikasi di email | TCS-10, 13 |
| SEC-31 | P0 | CSP bertahap: pasang `Content-Security-Policy-Report-Only` di staging, perbaiki semua pelanggaran, baru enforce di produksi. Semua JS dari berkas Vite tanpa inline script atau handler (`onclick=`); Alpine memakai build CSP; captcha R2 menambah host | CSP contoh di TDD memblokir inline script dan Alpine standar | TCS-11 |
| SEC-32 | P0 | `Cache-Control: no-store, private` pada semua respons terautentikasi dan unduhan (A.3) | Peserta SMK sering memakai komputer sekolah atau warnet; tombol back setelah logout tidak boleh menampilkan data | TCS-04, 11 |
| SEC-33 | P0 | Anti injeksi: tanpa `{!! !!}`, `DB::raw`, `whereRaw`, `orderByRaw` yang memuat input pengguna; `sort` memakai whitelist kolom; `per_page` maksimal 100; pencarian meng-escape `%` dan `_`. Dijaga CI (SEC-61) | PRD punya filter, pencarian, pagination, tetapi tidak ada aturan parameternya | TCS-07 |
| SEC-34 | P0 | dompdf: `enable_remote=false`, `enable_php=false`, `chroot` dibatasi ke folder view dan font, font lokal, semua data pengguna di-escape pada view PDF. Nama kunci bergantung versi paket: cek `config/dompdf.php` hasil publish | Nama dan catatan Pembimbing masuk PDF; dompdf punya riwayat kerentanan sumber remote dan eksekusi kode | TCS-15 |
| SEC-35 | P0 | `APP_DEBUG=false`; halaman kustom 403, 404, 419, 429, 500; tanpa Telescope, Debugbar, Ignition di produksi (`composer install --no-dev`); `/health` hanya mengembalikan status | NFR-O2 ada, tetapi tidak ada uji kebocoran | TCS-12 |
| SEC-36 | P0 | Tampilan audit log dan semua data dari pengguna (user-agent, filter, nama berkas, catatan) selalu di-escape; `audit_log.detail` dipotong 4 KB dan `user_agent` 255 karakter | Audit log adalah target XSS tersimpan terhadap SuperAdmin | TCS-17 |
| SEC-37 | P1 | Email: tambahkan DMARC (mulai `p=none` dengan laporan, naik ke `quarantine` setelah 4 minggu bersih); email hanya memuat tautan ke `APP_URL` | R-04 hanya menyebut SPF dan DKIM | TCS-11 |

#### A.2.5 Data pribadi dan log

| ID | Prio | Kebutuhan dan cara membuatnya | Alasan | Uji |
| --- | --- | --- | --- | --- |
| SEC-40 | P0 | Klasifikasi data di A.4 menjadi acuan siapa boleh melihat apa dan berapa lama disimpan. Setiap halaman baru harus cocok dengan tabel itu | PRD tidak punya inventaris data | Review |
| SEC-41 | P0 | **Data anak.** Sebagian peserta SMK berusia di bawah 18 tahun, dan UU PDP mensyaratkan persetujuan orang tua atau wali untuk memproses data anak. Default sementara `[Usulan]`: pada form profil, saat jenjang `smk` dipilih, tampilkan centang Saya berusia minimal 18 tahun atau sudah mendapat persetujuan orang tua/wali, disimpan beserta waktunya. Keputusan final di tangan hukum instansi (C-14) | NFR-R1 hanya menyebut persetujuan peserta sendiri | UAT-2 |
| SEC-42 | P0 | Log aplikasi bebas data pribadi: tidak ada `Log::*` yang memuat data peserta, password, atau token. Pesan `QueryException` memuat nilai binding, jadi berkas log berizin 0640, retensi 14 hari, dan pelacak error (mis. Sentry) memakai `send_default_pii=false` dengan scrubbing field | Log produksi adalah tempat kebocoran data pribadi yang paling sering terlupa | TCS-25 |
| SEC-43 | P1 | Enkripsi saat disimpan: volume server terenkripsi (LUKS atau enkripsi disk penyedia) `[Usulan]`; backup terenkripsi (restic atau gpg), kunci disimpan terpisah dari server dan dari backup, akses ke backup dibatasi dua orang. Kredensial backup di server aplikasi hanya boleh menulis, tidak boleh menghapus | TDD hanya menyebut backup terenkripsi | Drill B.6 |
| SEC-44 | P1 | Hak subjek data (akses, koreksi, penghapusan): SuperAdmin memproses permintaan tertulis, target 14 hari `[Usulan]`. Penghapusan berarti **anonimisasi**: nama, email, no_hp, nim_nisn diganti penanda dan berkas dihapus dari disk, lewat command `magkom:anonimkan-peserta` yang dijalankan di server dan diaudit (command P2, prosedur tertulis P1) | NFR-R2 menyerahkan ke Admin yang tidak punya alatnya; ADR-08 melarang hapus | Dokumen |
| SEC-45 | P0 | Prosedur kebocoran data: UU PDP mewajibkan pemberitahuan tertulis kepada subjek data dan lembaga PDP paling lambat 3 x 24 jam setelah kebocoran diketahui (verifikasi dengan hukum instansi). Langkahnya ada di runbook A.5 | Belum ada prosedur | Latihan SEC-72 |

#### A.2.6 Server dan infrastruktur

| ID | Prio | Kebutuhan dan cara membuatnya | Alasan | Uji |
| --- | --- | --- | --- | --- |
| SEC-50 | P0 | Hardening server: SSH hanya kunci dan tanpa login root, firewall hanya 80 dan 443 (22 dibatasi IP), MySQL `bind-address=127.0.0.1`, fail2ban, pembaruan keamanan otomatis, user deploy berbeda dari user PHP-FPM | TDD 10.2 hanya menyebut Nginx, PHP-FPM, HTTPS | TCS-19 |
| SEC-51 | P0 | Nginx: `server_tokens off`, tolak dotfile dan ekstensi sensitif, TLS 1.2 ke atas, HTTP dialihkan ke HTTPS, `default_server` penolak Host asing (A.3) | Idem | TCS-11, 12, 19 |
| SEC-52 | P0 | PHP-FPM: `expose_php=Off`, `display_errors=Off`, `disable_functions` untuk fungsi eksekusi shell (**hanya php-fpm**, jangan CLI karena scheduler memakai proses), `open_basedir` ke direktori aplikasi dan `/tmp` (A.3). Uji di staging | Idem | TCS-12 |
| SEC-53 | P1 | Lapis kedua rate limit di Nginx (`limit_req`) untuk `/login`, `/register`, `/forgot-password` (A.3) | Rate limit aplikasi memakai cache lokal dan ikut mati bila aplikasi macet | TCS-10 |
| SEC-54 | P0 | Izin berkas: `.env` 0640 (pemilik user deploy, grup PHP-FPM); kode hanya-baca bagi user PHP-FPM kecuali `storage/` dan `bootstrap/cache/` | TDD hanya menyebut storage dan cache | TCS-12 |
| SEC-55 | P0 | Database: akun runtime tanpa `DROP`, `ALTER`, `FILE`, `GRANT`; `local_infile=0`; password acak minimal 24 karakter; port 3306 tidak terjangkau dari luar server | TDD memisah akun tetapi tidak mengatur sisanya | TCS-18, 19 |
| SEC-56 | P1 | Batasi area staf: `/superadmin/*` (dan `/admin/*` bila memungkinkan) hanya dari IP kantor atau VPN lewat Nginx `allow` dan `deny`. Bila staf sering bekerja dari luar, pakai MFA (SEC-05) | Akun staf adalah akun paling berharga | TCS-19 |

#### A.2.7 CI dan rantai pasok

| ID | Prio | Kebutuhan dan cara membuatnya | Alasan | Uji |
| --- | --- | --- | --- | --- |
| SEC-60 | P0 | Kebijakan `composer audit` untuk Laravel 10: advisori tanpa perbaikan di cabang 10.x akan menggagalkan CI selamanya. Pakai `config.audit.ignore` (Composer versi baru; cek dokumentasi versi yang dipakai) per ID advisori, dengan alasan, tanggal, dan tinjau ulang tiap 30 hari. Ignore tanpa catatan ditolak saat review | TDD 10.5 hanya menulis `composer audit` | TCS-20 |
| SEC-61 | P0 | Pemeriksaan pola terlarang di CI (SAST murah): `grep -rnF -e '{!!' -e 'DB::raw' -e 'whereRaw' -e 'orderByRaw' -e 'selectRaw' -e '$guarded = []' app resources`. Baris di luar allowlist berkomentar alasan membuat CI gagal. Tambah Larastan level 6 `[Usulan]` | Larastan tidak menangkap pola ini | TCS-06, 07 |
| SEC-62 | P0 | Pemindaian rahasia dengan `gitleaks` di pipeline dan pre-commit; `.env*` ada di `.gitignore` | TDD menyebut pemindaian tanpa nama alat | TCS-20 |
| SEC-63 | P1 | `npm audit --omit=dev`; pembaruan dependensi terjadwal (Renovate atau Dependabot, mingguan); `composer.lock` dan `package-lock.json` wajib dikomit | Hanya composer yang dicek | TCS-20 |
| SEC-64 | P1 | DAST: OWASP ZAP baseline terhadap staging pada release candidate; 0 temuan High, Medium ditriase tertulis | Tidak ada uji dinamis | TCS-21 |
| SEC-65 | P1 | Akun repositori dan CI: 2FA wajib bagi semua kontributor, kredensial deploy khusus hak deploy dan disimpan sebagai secret CI, force-push ke branch utama dilarang | Hanya ada proteksi branch | Review |

#### A.2.8 Operasi dan tata kelola

| ID | Prio | Kebutuhan dan cara membuatnya | Alasan | Uji |
| --- | --- | --- | --- | --- |
| SEC-70 | P0 | Isi jadwal upgrade ADR-01 (masih kosong): **paling lambat 3 bulan setelah go-live R1** `[Usulan]`. Product Owner menandatangani penerimaan risiko R-01 yang memuat tanggal itu. Catat juga bahwa dukungan keamanan PHP 8.3 berakhir sekitar akhir 2027 (cek php.net/supported-versions). Selama itu berlaku SEC-56, SEC-60, dan langganan pengumuman keamanan Laravel | R-01 dicatat tetapi tanpa tenggat dan penanggung jawab | Dokumen |
| SEC-71 | P0 | Alarm keamanan minimal sembilan aturan di A.5, dikirim ke email pemilik sistem | TDD 10.7 hanya uptime, disk, scheduler | Simulasi |
| SEC-72 | P0 | Runbook insiden A.5 dan satu kali latihan sebelum go-live (skenario: password SuperAdmin bocor) | Belum ada | Latihan |
| SEC-73 | P0 | Definisi selesai untuk gelombang Penguatan: semua TCS berprioritas P0 lulus, ZAP baseline bersih, tinjauan kode oleh senior pada titik rawan D.2, dan (opsional) uji penetrasi oleh unit Persandian atau keamanan informasi dinas atau pihak ketiga. **Konfirmasi apakah ada kewajiban uji keamanan SPBE/BSSN** untuk aplikasi pemerintah daerah | PRD 4.2 menulis uji keamanan tanpa isi | TCS-* |
| SEC-74 | P2 | Sediakan `/.well-known/security.txt` berisi kontak pelaporan kerentanan | Belum ada jalur lapor | Cek manual |

### A.3 Konfigurasi Siap Pakai

Kode ini ilustrasi pola untuk Laravel 10 (sama statusnya dengan kode di TDD). Sesuaikan nama dan namespace proyek.

**Middleware `EnsureAccountActive` (SEC-03).** Daftarkan alias `account.active` di `Kernel::$middlewareAliases` dan tambahkan ke setiap grup rute yang memuat `auth`.

```php
public function handle(Request $request, Closure $next): Response
{
    $user = $request->user();

    if ($user && ! $user->status_aktif) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->withErrors(['email' => 'Akun tidak aktif. Hubungi Admin Kominfo']);
    }

    return $next($request);
}
```

**Middleware `superadmin.readonly` (SEC-12).**

```php
public function handle(Request $request, Closure $next): Response
{
    if ($request->user()?->role === RoleEnum::SuperAdmin && ! $request->isMethodSafe()) {
        abort(403);
    }

    return $next($request);
}
// Route::prefix('pembimbing')->middleware(['auth', 'account.active', 'role:pembimbing,superadmin', 'superadmin.readonly'])
```

**Tambahan di `SecurityHeaders` (SEC-32).** Landing publik tidak diberi header ini; bila ingin di-cache, maksimal 30 detik agar AC-01-4 tetap benar.

```php
if ($request->user() || $request->routeIs('berkas.*')) {
    $response->headers->set('Cache-Control', 'no-store, private');
    $response->headers->set('Pragma', 'no-cache');
}
```

**TrustHosts dan TrustProxies (SEC-30).** Hapus komentar baris `TrustHosts` di `$middleware` pada `Kernel.php` (bawaan Laravel 10 mengomentarinya).

```php
// app/Http/Middleware/TrustHosts.php
public function hosts(): array
{
    return ['^'.preg_quote(parse_url(config('app.url'), PHP_URL_HOST)).'$'];
}

// app/Http/Middleware/TrustProxies.php, hanya bila ada proxy di depan Nginx
protected $proxies = ['10.0.0.5'];   // IP proxy atau WAF, bukan '*'
```

**Disk privat (SEC-20)** di `config/filesystems.php`, dalam `disks`:

```php
'private' => [
    'driver' => 'local',
    'root' => storage_path('app/private'),
    'visibility' => 'private',
    'permissions' => [
        'file' => ['public' => 0600, 'private' => 0600],
        'dir' => ['public' => 0700, 'private' => 0700],
    ],
    'throw' => true,
],
```

**Aturan password (SEC-01).** Peserta di `AppServiceProvider::boot()` atau Form Request; staf memakai `min(12)` tanpa `uncompromised()` bila server tidak punya akses keluar.

```php
'password' => ['required', 'confirmed', 'max:64', Password::min(8)->letters()->numbers()->uncompromised()],
```

**Nginx (SEC-51, 53, 21, 30).** Butuh nginx 1.19.4 ke atas untuk `ssl_reject_handshake`.

```nginx
# blok http
server_tokens off;
limit_req_zone $binary_remote_addr zone=auth:10m rate=10r/m;

# server cadangan: tolak Host yang bukan domain resmi
server { listen 80 default_server; return 444; }
server { listen 443 ssl default_server; ssl_reject_handshake on; }

# server domain resmi
client_max_body_size 12m;
ssl_protocols TLSv1.2 TLSv1.3;
location ~ /[.](?!well-known) { deny all; }
location ~* [.](env|git|sql|bak|old|log|sh|ini)$ { deny all; }
location ~ ^/(login|register|forgot-password)$ {
    limit_req zone=auth burst=5 nodelay;
    try_files $uri /index.php?$query_string;
}
```

**PHP-FPM (SEC-52, 21).** Hanya untuk pool FPM, bukan CLI. Pastikan `open_basedir` mencakup direktori rilis, `shared/`, dan tautan simbolik `current`; uji deploy di staging.

```ini
expose_php = Off
display_errors = Off
log_errors = On
upload_max_filesize = 10M
post_max_size = 12M
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,pcntl_exec
open_basedir = /var/www/magkom/:/tmp/
```

### A.4 Klasifikasi Data

| Data | Contoh | Siapa boleh melihat | Catatan |
| --- | --- | --- | --- |
| Identitas dan kontak | nama, email, no_hp | Pemilik, Admin, SuperAdmin; Pembimbing hanya peserta bimbingannya | Retensi menunggu OQ-08 |
| Identitas akademik | NIM/NISN, institusi, jurusan | Sama seperti di atas | Dapat memuat data anak (SEC-41) |
| Berkas pendaftaran | surat pengantar, CV, rapor, transkrip | Pemilik, Admin, SuperAdmin. **Pembimbing tidak** | Paling sensitif; hanya lewat tautan bertanda tangan dan Policy |
| Nilai dan Lembar Nilai | skor, predikat, catatan | Pemilik (hanya `final`), Pembimbing bimbingannya, SuperAdmin baca-saja. **Admin tidak** | Perubahan tercatat di riwayat |
| Tugas, progres, logbook | catatan kerja, lampiran | Pemilik, Pembimbing bimbingannya, SuperAdmin baca-saja | Lampiran lewat rute unduh SEC-10 |
| Kredensial | hash password, token | Tidak seorang pun | Tidak pernah masuk log atau audit |
| Audit log | IP, user-agent, nilai lama dan baru | SuperAdmin baca-saja | Aksi tanpa batas; akses data 1 tahun `[Usulan]` |

### A.5 Alarm Keamanan dan Runbook Insiden

**Alarm minimal (SEC-71).** Cara murah: query terjadwal (cron tiap 10 menit) atas `audit_log` yang mengirim email lewat command artisan; tidak perlu alat baru. Nama aksi mengikuti implementasi.

| No | Kejadian | Sumber | Ambang | Tindakan |
| --- | --- | --- | --- | --- |
| 1 | Lonjakan gagal login | `audit_log` tipe `auth` | Lebih dari 50 per 10 menit total, atau lebih dari 10 untuk satu email | Email segera; periksa SEC-06 |
| 2 | Akun staf dibuat, role atau status diubah, password direset | `audit_log` tipe `aksi` | Setiap kejadian | Cocokkan dengan permintaan resmi |
| 3 | Login SuperAdmin dari IP yang belum pernah dipakai | `audit_log` tipe `auth` | Setiap kejadian | Konfirmasi ke pemilik akun |
| 4 | Unduhan berkas massal oleh satu staf | `audit_log` tipe `akses_data` | Lebih dari 60 per jam | Hubungi staf, tinjau akses |
| 5 | Lonjakan 403 atau 404 dari satu IP (indikasi IDOR atau pemindaian) | Log akses Nginx | Lebih dari 30 per 10 menit | Blokir sementara (fail2ban) |
| 6 | Kesalahan 5xx | Pelacak error atau log | Lebih dari 5 per 5 menit | Tetapkan S1 atau S2 sesuai dampak |
| 7 | Kegagalan menulis audit | Log aplikasi | Setiap kejadian | Periksa DB dan disk segera, karena aksi bisnis ikut gagal |
| 8 | Scheduler, disk, sertifikat, backup | Pemantau | Scheduler lebih dari 36 jam; disk 80%; sertifikat kurang dari 14 hari; backup baru tidak ada lebih dari 26 jam | Perbaiki sebelum jam kerja berikutnya |
| 9 | `/health` bukan 200 | Uptime monitor | 2 kali berturut-turut | Tetapkan S1 |

```sql
-- contoh kueri alarm 1
SELECT COUNT(*) FROM audit_log
WHERE tipe = 'auth' AND aksi = 'login.gagal' AND waktu > NOW() - INTERVAL 10 MINUTE;
```

**Runbook insiden (SEC-72).** Daftar kontak (pemilik sistem, tech lead, infrastruktur, hukum, pimpinan) diisi di `docs/kontak-insiden.md` dan dicetak.

| Langkah | Tindakan | Penanggung jawab |
| --- | --- | --- |
| 1. Deteksi dan klasifikasi | Catat waktu, sumber, dampak awal. Anggap S1 bila ada dugaan data pribadi bocor atau akun SuperAdmin dikuasai | Siapa pun, lapor ke pemilik sistem |
| 2. Isolasi | `php artisan down --secret=<kode>`; nonaktifkan akun terdampak; blokir IP di firewall; **jangan reboot** bila server diduga dikompromi | Tim infrastruktur |
| 3. Amankan bukti | Salin log Nginx dan aplikasi, ekspor `audit_log`, snapshot DB dan disk **sebelum** memperbaiki | Tim infrastruktur |
| 4. Berantas | Ganti semua kredensial (DB, SMTP, OAuth, captcha); ganti `APP_KEY` (semua pengguna login ulang dan tautan bertanda tangan lama mati); reset password staf; tambal celah | Tech lead |
| 5. Pulihkan | Bila server dikompromi: bangun ulang dan pulihkan dari backup bersih (RPO 24 jam, RTO 4 jam) | Tim infrastruktur |
| 6. Beritahu | Pimpinan dan hukum segera. Bila data pribadi bocor: pemberitahuan tertulis kepada subjek data dan lembaga PDP paling lambat 3 x 24 jam (SEC-45) | Pimpinan instansi, hukum |
| 7. Pasca insiden | Laporan dalam 5 hari kerja: kronologi, akar masalah, perbaikan. Tambahkan test yang akan menangkapnya | Tech lead |

---

## B. Pengujian (QA)

### B.1 Strategi Pengujian

Melengkapi TDD bab 11 (isinya tidak diulang).

| Lapisan | Isi | Penulis | Dijalankan | Gerbang |
| --- | --- | --- | --- | --- |
| Unit (U) | `PeriodeMagang`, `BulanStatusService`, `ScoringService`, aksesor Terlambat, sanitasi audit | Developer | CI setiap PR | Hijau; cakupan Services dan Policies minimal 80% |
| Feature (F) | Semua AC lewat HTTP dan MySQL | Developer | CI setiap PR | Hijau |
| Security (S) | TCS yang dapat diotomasi, folder `tests/Security` | Developer atau QA | CI setiap PR | Hijau |
| Konkurensi (K) | Grup `concurrency`, proses PHP terpisah | Senior atau QA | CI pada PR yang menyentuh Service kuota, penempatan, keputusan; dan tiap malam | Hijau |
| E2E (E) | Playwright untuk 6 alur kritis (B.6) | QA | Tiap malam dan setelah deploy staging | Hijau |
| Non-fungsional | k6, axe, email, PDF, backup (B.6) | QA | Sebelum go-live dan tiap rilis besar | Lulus B.6 |
| UAT (M) | UAT-1 sampai UAT-11 (B.7) | Admin, Pembimbing, perwakilan peserta | Staging | Tanda tangan UAT |

**Kriteria masuk QA** sebuah fitur: DoR PRD 12.1 terpenuhi, build ada di staging, data B.2 termuat. **Kriteria keluar:** DoD PRD 12.2 ditambah gerbang B.8.

**Konvensi test.** (1) Setiap test diberi grup AC: `#[Group('AC-05-4')]` (PHPUnit 10), `@group AC-05-4` (PHPUnit 9), atau `->group('AC-05-4')` (Pest). (2) Nama method: `test_ac_05_4_hanya_satu_penerimaan_berhasil`. (3) AC yang hanya bisa diuji manual dicatat di `tests/ManualAc.php` sebagai komentar `// AC-01-6 manual: UAT-1`. (4) CI gagal bila ada AC R1 tanpa test:

```bash
# scripts/cek-ac.sh
grep -ohE 'AC-[0-9]{2}-[0-9]+' docs/PRD-MagKom.md | sort -u > /tmp/ac_prd.txt
grep -rohE 'AC-[0-9]{2}-[0-9]+' tests | sort -u > /tmp/ac_test.txt
comm -23 /tmp/ac_prd.txt /tmp/ac_test.txt | tee /tmp/ac_hilang.txt
test ! -s /tmp/ac_hilang.txt   # gagal bila ada AC tanpa test (kecualikan AC R2 dan R3 sampai rilisnya)
```

**Tambahan pipeline CI** (melengkapi TDD 10.5):

| Tahap | Kapan |
| --- | --- |
| `scripts/cek-ac.sh` | Setiap PR |
| `phpunit --testsuite Security` | Setiap PR |
| `phpunit --group concurrency` (MySQL asli) | PR yang menyentuh kuota atau penempatan, dan tiap malam |
| Pola terlarang (SEC-61), `gitleaks`, `npm audit --omit=dev` | Setiap PR |
| `migrate:fresh --seed` lalu `migrate:rollback` sampai habis | Setiap PR yang menambah migrasi |
| ZAP baseline terhadap staging | Release candidate |
| Playwright smoke | Tiap malam dan setelah deploy staging |
| k6 | Sebelum go-live |

Pakai satu mesin database yang sama dengan produksi di CI (MySQL 8.0.x atau MariaDB 10.5 ke atas; jangan dicampur).

### B.2 Data dan Lingkungan Uji

Satu `UatSeeder` (hanya berjalan di lokal dan staging, menolak jalan bila `APP_ENV=production`) membuat data berikut. Semua tanggal relatif terhadap hari ini supaya masa tenggang dan penjadwal dapat diuji. Password akun uji berasal dari variabel lingkungan, bukan literal.

| Kode | Akun | Keadaan | Dipakai untuk |
| --- | --- | --- | --- |
| P-SA | SuperAdmin | aktif | hanya-baca, audit, master data |
| P-AD1, P-AD2 | Admin | aktif | keputusan ganda bersamaan |
| P-PB1, P-PB2 | Pembimbing | aktif | pindah Pembimbing, uji bukan pemilik |
| P-PS1 | Peserta smk | email belum terverifikasi | AC-02-3 |
| P-PS2 | Peserta s1 | terverifikasi, profil lengkap, belum mendaftar | AC-03 |
| P-PS3 | Peserta smk | `berkas_belum_lengkap` (2 dari 3 berkas) | AC-03-6 |
| P-PS4 | Peserta d3 | `diajukan` | AC-04 |
| P-PS5 | Peserta s1 | `waiting_list` | BR-06 |
| P-PS6 | Peserta | penempatan aktif di P-PB1; 3 tugas (belum, proses, terlambat); 5 entri logbook | F-08, F-09 |
| P-PS7 | Peserta | penempatan aktif di P-PB2 | non-pemilik bagi P-PB1 |
| P-PS8 | Peserta | `selesai`, `tgl_nonaktif` besok | masa tenggang |
| P-PS9 | Peserta | `selesai`, `tgl_nonaktif` hari ini | penjadwal |
| P-PS10 | Peserta | `nonaktif` | AC-12-4 |

Bulan uji: **M0** sudah mulai (tidak tampil), **M1** Tersedia (kuota 12 terbagi 6 jurusan, slot khusus 3 terisi 2), **M2** Segera, **M3** Tersedia dengan kuota 1 sisa 1 (uji konkurensi), **M4** override Tutup, **M5** terlalu awal (tidak tampil). Rubrik awal memakai PRD 8.3 (total 100).

**Menguji waktu tanpa menunggu.** UAT-8 tidak bisa berjalan real-time. Pakai P-PS8 dan P-PS9 lalu jalankan `php artisan magkom:nonaktifkan-peserta-selesai` manual; pakai M0 untuk uji tutup antrean. Opsi `[Usulan]`: command khusus staging `magkom:uat-geser-tanggal --hari=N` yang menggeser kolom tanggal dan **menolak jalan bila `APP_ENV=production`**.

**Email.** Lokal: Mailpit atau setara. Staging: SMTP uji ke kotak surat nyata (Gmail, Outlook, Yahoo, alamat `.ac.id` dan `.sch.id`). Captcha memakai kunci uji dari penyedia. Layanan luar di-fake (`Http::fake`).

**Berkas uji** (`tests/fixtures/berkas`, dibuat lewat skrip): PDF sah 100 KB; PDF tepat 2.097.152 byte dan 2.097.153 byte; PNG; JPG; berkas kosong; teks berekstensi `.pdf`; berkas PHP berekstensi `.jpg`; string uji EICAR; nama berkas 300 karakter; nama berkas berkarakter Unicode.

### B.3 Matriks Hak Akses (kontrak TCS-01)

Test dibuat berbasis data dari tabel ini: satu baris tabel menjadi beberapa kasus (setiap rute di TDD bab 7 dikali identitas). Legenda: **L** = 302 ke `/login`; **200** = berhasil (atau 302 setelah POST sukses); **403** = ditolak; **-** = tidak berlaku. Bukan pemilik = peserta atau Pembimbing lain yang mengakses record orang lain.

| Kelompok rute | Tamu | Peserta pemilik | Peserta bukan pemilik | Admin | Pembimbing pemilik | Pembimbing bukan pemilik | SuperAdmin |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `/`, `/kebijakan-privasi`, `/health` | 200 | 200 | 200 | 200 | 200 | 200 | 200 |
| `/login`, `/register` | 200 | 302 | 302 | 302 | 302 | 302 | 302 |
| `/peserta/*` GET tanpa ID (dashboard, profil, tugas, logbook, nilai) | L | 200 | - | 403 | 403 | 403 | 403 |
| `/peserta/*` tulis sah (POST, PUT, PATCH) | L | 200 | - | 403 | 403 | 403 | 403 |
| `/peserta/tugas/{id}`, `/peserta/logbook/{id}`, `/peserta/pendaftaran/{id}/*` | L | 200 | 403 | 403 | 403 | 403 | 403 |
| `/berkas/{dokumen}/unduh` (berkas pendaftaran, tautan bertanda tangan sah) | L | 200 | 403 | 200 | 403 | 403 | 200 |
| `/berkas/progres/{id}/unduh`, `/berkas/logbook/{id}/unduh` (SEC-10) | L | 200 | 403 | 403 | 200 | 403 | 200 |
| `/peserta/nilai/lembar-nilai` (nilai berstatus final) | L | 200 | - | 403 | 403 | 403 | 403 |
| `/admin/*` GET dan tulis sah | L | 403 | 403 | 200 | 403 | 403 | 200 |
| `/admin/laporan/nilai` (R2, rekap nilai) | L | 403 | 403 | 403 | 403 | 403 | 200 |
| `/pembimbing/*` GET (peserta, tugas, logbook, penilaian) | L | 403 | 403 | 403 | 200 | 403 | 200 |
| `/pembimbing/*` tulis (tugas, review, penilaian, selesai) | L | 403 | 403 | 403 | 200 | 403 | **403** |
| `/superadmin/*` GET | L | 403 | 403 | 403 | 403 | 403 | 200 |
| `/superadmin/*` tulis (akun staf, master, bulan, slot khusus, pengaturan) | L | 403 | 403 | 403 | 403 | 403 | 200 |
| `/superadmin/penempatan`, `/arsip`, `/audit-log` dengan method tulis | L | 403 | 403 | 403 | 403 | 403 | 405 |
| Method DELETE ke rute mana pun | 405 | 405 | 405 | 405 | 405 | 405 | 405 |

Baris tambahan menurut **keadaan akun**:

| Keadaan | Hasil yang diharapkan |
| --- | --- |
| Peserta belum verifikasi email | Semua rute kecuali verifikasi, kirim ulang, logout: 302 ke `/verify-email` |
| Peserta masa tenggang | GET 200; POST, PUT, PATCH 403 (logout tetap boleh) |
| Akun nonaktif dengan sesi masih hidup | Request berikutnya 302 ke `/login` (SEC-03) |
| Staf dengan `must_change_password` | Semua rute kecuali `/password/ganti` dan logout: 302 ke `/password/ganti` |
| Peserta tanpa penempatan aktif | `/peserta/logbook` terkunci; tulis 403 |
| Penempatan `selesai` atau `nonaktif` | Semua tulis peserta 403 |

Kerangka test (ilustrasi) dan test penjaga agar rute baru tidak lolos tanpa uji:

```php
#[DataProvider('matriks')]
public function test_matriks_hak_akses(string $peran, string $method, string $uri, int $status): void
{
    $user = $this->buatPengguna($peran);              // factory sesuai persona B.2
    $this->actingAs($user)->call($method, $this->isiParameter($uri))->assertStatus($status);
}

public static function matriks(): array
{
    return [
        'peserta ke admin pendaftaran' => ['peserta', 'GET', '/admin/pendaftaran', 403],
        'admin ke pembimbing peserta' => ['admin', 'GET', '/pembimbing/peserta', 403],
        // satu baris per sel tabel di atas
    ];
}

// Penjaga: bandingkan route:list --json dengan seluruh pola di matriks; gagal bila ada rute yang belum tercakup
```

### B.4 Katalog Uji per AC

Melengkapi PRD bab 12 dan TDD 11.1. Jenis: **U** unit, **F** feature, **K** konkurensi (proses terpisah), **M** manual atau UAT, **S** skrip atau infrastruktur. Kasus **positif** (jalur normal sesuai bunyi AC) selalu wajib; kolom di bawah hanya mencantumkan kasus tambahan negatif dan batas yang tidak boleh terlewat. Beri setiap test grup AC (B.1).

| AC | Jenis | Kasus tambahan yang wajib |
| --- | --- | --- |
| AC-01-1 | F | Terisi sama dengan jumlah menampilkan 0 (tidak pernah negatif). Bulan tampil tepat 1 bulan sebelum `tgl_buka`, 1 hari lebih awal tidak tampil; bulan yang sudah mulai tidak tampil; bulan atau kuota nonaktif berstatus Tutup |
| AC-01-2 | F | Tutup dan Segera: tombol nonaktif **dan** POST langsung ke pendaftaran ditolak; Segera menampilkan `tgl_buka` |
| AC-01-3 | F | Sisa 0 berstatus Tersedia: label Waiting List muncul dan tombol tetap aktif |
| AC-01-4 | F | Ubah override atau kuota lalu muat ulang: berubah dalam 60 detik (tidak ada cache lebih lama) |
| AC-01-5 | F | Pindai HTML, JSON, semua rute publik: tidak ada data slot khusus. Pasang `DB::listen`: tidak ada query ke `slot_khusus` pada request publik |
| AC-01-6 | M | `<title>`, meta description, Open Graph; layar 360 px tanpa scroll horizontal; tanpa login; Lighthouse sesuai NFR-P1 |
| AC-02-1 | F | Tanpa centang persetujuan ditolak; `role=admin` di body diabaikan; login otomatis dan menuju halaman verifikasi |
| AC-02-2 | F | Email ganda (termasuk huruf besar); password 7 karakter, hanya huruf, hanya angka, konfirmasi beda ditolak; 8 karakter lolos; 65 karakter ditolak; nama 2 dan 3 karakter; email 255 dan 256 karakter; email di-trim dan huruf kecil |
| AC-02-3 | F | Belum terverifikasi: semua rute selain verifikasi, kirim ulang, logout dialihkan |
| AC-02-4 | F | Tautan sah; hash diubah; tautan milik user lain; kedaluwarsa (`travelTo` +61 menit) menawarkan kirim ulang; kirim ulang ke-7 dalam semenit: 429 |
| AC-02-5 | F | Gagal ke-5 lalu ke-6 terkunci dengan sisa waktu; pesan sama untuk email tak ada dan password salah; IP lain tidak ikut terkunci; berhasil setelah kunci habis |
| AC-02-6 | F | Respons identik (status, isi, redirect) untuk email terdaftar, tidak terdaftar, akun staf; email hanya ke peserta; tautan 60 menit sah, 61 menit gagal; tidak bisa dipakai dua kali |
| AC-02-7 | F | Ganti email: `email_verified_at` kosong dan menu pendaftaran terkunci; email yang sudah dipakai ditolak |
| AC-02-8 | F | Kredensial benar pada akun nonaktif: pesan khusus; kredensial salah: pesan seragam (status akun tidak bocor) |
| AC-02-9 | M | Teks Indonesia; domain pengirim resmi; SPF, DKIM, DMARC lulus di header penerima; masuk inbox |
| AC-02-10 | F | Registrasi ke-6 dalam semenit dari satu IP: 429; IP lain tidak terpengaruh (uji dengan dan tanpa `X-Forwarded-For`, SEC-30) |
| AC-03-1 | F | Kosongkan tiap field profil satu per satu: menu terkunci; akses langsung lewat URL dan POST juga ditolak |
| AC-03-2 | F | SMK tidak melihat jurusan Mahasiswa dan sebaliknya; bulan hanya Tersedia dan berkuota aktif; durasi 0 dan 7 ditolak, 1 dan 6 lolos |
| AC-03-3 | F | Bulan menjadi Tutup antara render dan submit: pesan, tidak tersimpan; `jurusan_id` tak sesuai jenjang dikirim manual: 422 |
| AC-03-4 | F | Pendaftaran kedua untuk bulan sama; pendaftaran aktif lain (BR-17); bulan sama setelah Ditolak atau Dibatalkan tetap ditolak, bulan lain boleh; uji unique constraint langsung di DB |
| AC-03-5 | F | PDF asli lulus; `.exe` bernama `.pdf` ditolak; PHP bernama `.jpg` ditolak; 2.097.152 byte lulus, 2.097.153 ditolak; berkas kosong ditolak; tersimpan di disk privat dengan nama acak dan tidak terbuka lewat URL |
| AC-03-6 | F | 2 dari 3 berkas: tetap `berkas_belum_lengkap`, tombol Ajukan nonaktif dan POST Ajukan ditolak; lengkap: `diajukan` dengan `diajukan_at` WIB |
| AC-03-7 | F | Ganti berkas pada `berkas_belum_lengkap` dan `perlu_perbaikan` sukses; pada `diajukan`, `diterima`, `ditolak` ditolak; audit memuat nama asli dan hash lama dan baru; berkas lama terhapus dari disk (SEC-25) |
| AC-03-8 | F, K | Dua POST Ajukan paralel: satu berhasil; dua unggah paralel jenis sama: satu record |
| AC-03-9 | F | Batal dari tiap status sebelum diterima menjadi `dibatalkan` final; tidak bisa diajukan lagi; peserta membatalkan yang sudah `diterima` ditolak |
| AC-03-10 | F, M | Dashboard menampilkan status, catatan Admin **terakhir** (bukan yang lama), timeline berurutan waktu, petunjuk cek status |
| AC-04-1 | F | Urut `diajukan_at` terlama; tiap filter dan pencarian (nama, email, NIM/NISN); 26 data memunculkan halaman 2; tanpa N+1 (`assertQueryCount`); pencarian `%` dan `_` bukan wildcard |
| AC-04-2 | F | Tautan unduh 5 menit sah, 6 menit 403; peserta lain atau Pembimbing 403 walau memegang tautan sah |
| AC-04-3 | F | Catatan 9 karakter ditolak, 10 lulus, 1001 ditolak, untuk Ditolak dan Perlu Perbaikan; opsional untuk keputusan lain |
| AC-04-4 | F | Waiting List saat kuota belum penuh: opsi tidak ada dan POST manual ditolak; saat terisi sama dengan jumlah: boleh |
| AC-04-5 | K | Dua Admin memutuskan bersamaan: satu berhasil, satu menerima pesan status sudah berubah; audit keputusan hanya satu |
| AC-04-6 | F, K | Kuota penuh: status tidak berubah, tidak ada penempatan, `terisi` tidak berubah, tidak ada audit `diterima`. Sisa 1 dan dua penerimaan paralel: tepat satu berhasil |
| AC-04-7 | F | Tutup antrean sebelum bulan dimulai ditolak; sesudahnya semua `waiting_list` bulan itu `ditolak` dengan catatan otomatis, status lain tidak tersentuh, ada audit per baris (update massal tidak memicu event: audit manual) |
| AC-04-8 | F | Audit memuat pelaku, waktu, status lama dan baru, catatan |
| AC-04-9 | F | `Mail::fake()`: tidak ada email keluar pada semua keputusan |
| AC-05-1 | F | Simpan sukses dan landing mengikuti; kuota pada bulan nonaktif ditolak |
| AC-05-2 | F | Total tepat 12 lulus, 13 ditolak; ubah satu jurusan tetap menghitung jurusan lain; batas yang diturunkan: lihat C-06 |
| AC-05-3 | F | Turunkan ke `terisi` dikurangi 1 ditolak; ke `terisi` lulus |
| AC-05-4 | K | Dua proses pada sisa 1: tepat satu berhasil dan `terisi` sama dengan `jumlah`. Insert langsung `terisi` lebih besar dari `jumlah` ke DB gagal oleh CHECK |
| AC-05-5 | F | Batal sebelum `tgl_mulai`: `terisi` turun 1 (tidak di bawah 0). Pada hari `tgl_mulai` dan sesudahnya: tidak berubah. Uji 23:59 hari sebelumnya dan 00:00 hari mulai (WIB) |
| AC-05-6 | F | Nonaktifkan kuota atau bulan yang masih punya pendaftaran atau penempatan aktif ditolak dengan pesan penyebab; tanpa itu sukses |
| AC-06-1 | F | SuperAdmin 200, Admin 403; nilai awal form 3 |
| AC-06-2 | F | Mengisi kuota umum tidak mengurangi slot, dan sebaliknya |
| AC-06-3 | F, K | Slot sisa 1 dan dua input paralel: satu berhasil. Slot penuh atau belum ada: ditolak dan **tidak ada akun setengah jadi** (rollback ikut membatalkan pembuatan user) |
| AC-06-4 | F | Sama dengan AC-01-5 (satu test, dua grup) |
| AC-06-5 | F | `jumlah` lebih kecil dari `terisi` ditolak; tidak ada aksi Waiting List untuk jalur khusus |
| AC-07-1 | U, F | Periode BR-08: Jan 2027 durasi 3 adalah 1 Jan sampai 31 Mar 2027; kasus lain di tabel tanggal B.6 |
| AC-07-2 | F, K | Satu Pembimbing aktif: otomatis; dua: wajib memilih. Batas beban: aktif sama dengan batas ditolak, di bawah batas lolos, batas kosong tanpa batas. Dua penerimaan paralel pada jurusan berbeda saat sisa beban 1: tepat satu berhasil (C-04) |
| AC-07-3 | F | User `peserta` terverifikasi, `must_change_password`, `pendaftaran_id` kosong, `sumber=manual`, `jalur=khusus` (atau `umum` bila kuota ada); email ganda ditolak |
| AC-07-4 | F | Pindah ke Pembimbing aktif lain sukses; ke Pembimbing nonaktif, ke diri sendiri, atau melewati batas beban ditolak; audit lama dan baru; peserta tidak bisa |
| AC-07-5 | F | Batal sebelum mulai dan mundur sejak mulai sesuai BR-07 dalam satu transaksi: paksa gagal di langkah audit lalu pastikan semuanya rollback |
| AC-07-6 | F | Tugas dan Logbook terkunci sebelum penempatan, terbuka sesudahnya |
| AC-07-7 | F | SuperAdmin melihat semua penempatan termasuk nonaktif; method tulis ditolak |
| AC-08-1 | F | Deadline hari ini lulus, kemarin ditolak; sama dengan `tgl_selesai` lulus, lebih satu hari ditolak; penempatan tidak aktif ditolak; Pembimbing lain 403 |
| AC-08-2 | U, F | Hanya tugas milik sendiri (ID orang lain 403). Deadline hari ini pukul 23:59 belum Terlambat, 00:00 besok Terlambat; sisa 3 hari bertanda mendekati, 4 hari tidak; tugas `selesai` tidak pernah Terlambat |
| AC-08-3 | F | Catatan 4 karakter ditolak, 5 lulus; berkas sesuai bab 8.4; `belum` menjadi `proses`; saat masa tenggang ditolak |
| AC-08-4 | F | `perlu_revisi` tanpa feedback ditolak; peserta tidak dapat menandai tugas `selesai`; Pembimbing lain 403 |
| AC-08-5 | F | SuperAdmin pada setiap rute tulis `/pembimbing/*`: 403, GET: 200 (menguji dua lapis SEC-12) |
| AC-08-6 | F | Masa tenggang dan nonaktif: semua tulis peserta ditolak, GET tetap |
| AC-08-7 | F | Edit tugas selesai ditolak; rute hapus tidak ada (404 atau 405) |
| AC-09-1 | F | Tanpa penempatan aktif: menu terkunci dan POST ditolak |
| AC-09-2 | U, F | Hari ini lulus; hari ini dikurangi 3 lulus; dikurangi 4 ditolak; besok ditolak; sebelum `tgl_mulai` dan setelah `tgl_selesai` ditolak; tanggal ganda ditolak (dan unique DB); kegiatan 9 karakter ditolak, 10 lulus, 2001 ditolak |
| AC-09-3 | F | Edit dalam batas mundur lulus; di luar batas berstatus `terkirim` ditolak; di luar batas berstatus `perlu_revisi` lulus; setelah edit status `terkirim`; entri orang lain 403 |
| AC-09-4 | F | Filter peserta dan tanggal; `perlu_revisi` tanpa catatan ditolak; SuperAdmin 403 pada review |
| AC-09-5 | F | Selesai atau nonaktif: baca-saja |
| AC-09-6 | F (R2) | Tautan `http://` dan `javascript:` ditolak, `https://` lulus dan memuat `rel=noopener noreferrer`; PDF sesuai batas ukuran |
| AC-10-1 | U | Skor 80, 70, 90, 60, 100 dengan bobot 30, 20, 20, 15, 15 menghasilkan 80,00 dan B. Batas predikat: 84,99 B; 85,00 A; 70,00 B; 69,99 C; 55,00 C; 54,99 D; 40,00 D; 39,99 E. Semua 0 menghasilkan 0,00 E; semua 100 menghasilkan 100,00 A. Hitung dengan bilangan bulat (total bobot 100) agar tanpa galat desimal (C-05) |
| AC-10-2 | F | Draft tidak tampil ke peserta dan Lembar Nilai tidak bisa diunduh peserta; final tampil; final dengan kriteria kosong ditolak; skor -1, 101, 50,5, abc ditolak |
| AC-10-3 | F | Ubah tanpa alasan ditolak; 9 karakter ditolak, 10 lulus; riwayat menyimpan nilai lama, baru, pelaku, waktu; nilai akhir dan predikat dihitung ulang |
| AC-10-4 | F | Ubah bobot rubrik tidak mengubah penilaian lama; total bobot bukan 100 ditolak; kriteria yang dinonaktifkan hanya berlaku untuk penilaian baru |
| AC-10-5 | F | Selesai tanpa penilaian, atau dengan draft, ditolak; dengan final: `selesai`, `tgl_ditetapkan_selesai`, dan `tgl_nonaktif` terisi |
| AC-10-6 | F | Ubah nilai lalu unduh lagi memuat nilai baru; isi sesuai bab 8.5; nama berapostrof, Unicode, 100 karakter; berkas diawali `%PDF`; peserta lain 403 |
| AC-10-7 | F | SuperAdmin melihat nilai dan riwayat; PUT 403 |
| AC-11-1 | F | Tambah dan ubah lulus; tidak ada rute hapus; non-SuperAdmin 403 |
| AC-11-2 | F | Menonaktifkan jurusan, bulan, kuota, atau Pembimbing terakhir yang melanggar BR-12 ditolak dengan pesan penyebab |
| AC-11-3 | F | Akun baru: `must_change_password`, terverifikasi, `Mail::fake()` tidak mengirim apa pun; `role=superadmin` ditolak; password dibuat sistem (SEC-02) |
| AC-11-4 | F | Reset dan nonaktifkan tercatat di audit; password lama tidak berlaku; sesi aktif staf yang dinonaktifkan terputus (SEC-03) |
| AC-11-5 | F | Login pertama: semua rute selain ganti password dialihkan; password baru sama dengan lama ditolak; setelah sukses `must_change_password` false |
| AC-11-6 | U, F | Tabel kebenaran BR-04: bulan nonaktif, override (tiga nilai dan kosong), dan otomatis, semua kombinasi |
| AC-11-7 | F | Nilai -1, abc, JSON `predikat_batas` rusak atau batas tidak menurun ditolak; ada audit; nilai baru terbaca di request berikutnya (cache dibersihkan) |
| AC-11-8 | S | Seeder: 6 jurusan, 9 jenis dokumen, rubrik 5 (total 100), 8 pengaturan, 1 SuperAdmin dari env; tanpa env seeder gagal dengan pesan jelas (bukan password bawaan); dijalankan dua kali tidak menggandakan data `[Usulan]` |
| AC-12-1 | U | Tabel tanggal B.6 (31 Jan ditambah 1 bulan menjadi 28 Feb, bukan 3 Mar); mengubah pengaturan tidak mengubah nilai yang sudah dihitung |
| AC-12-2 | F | Login lulus; semua POST, PUT, PATCH 403; banner sisa hari tampil; unduh Lembar Nilai lulus |
| AC-12-3 | F | `tgl_nonaktif` hari ini dinonaktifkan, besok tidak; dijalankan dua kali hasil sama; audit sistem tercatat; `status_aktif` false |
| AC-12-4 | F | Nonaktif: login ditolak, jumlah baris data sama sebelum dan sesudah, SuperAdmin membuka arsip |
| AC-12-5 | F | Terlewat 3 hari lalu dijalankan: semua terproses; 150 baris (lebih dari satu chunk) terproses |
| AC-13-1 | F | `X-Request-Id` ada di setiap respons terautentikasi; login sukses, gagal, logout, verifikasi, reset tercatat tanpa token |
| AC-13-2 | F | Test generik: ambil semua rute POST, PUT, PATCH dari `route:list`, panggil dengan data sah, tegaskan ada baris `aksi`; pengecualian harus tertulis eksplisit |
| AC-13-3 | F | Method Service sukses dan gagal tercatat (`ok` atau `gagal:Kelas`), argumen tersanitasi, durasi tidak negatif |
| AC-13-4 | F | Daftar 25 baris menghasilkan satu baris `akses_data` berisi filter dan jumlah; unduh dan ekspor tercatat |
| AC-13-5 | F | Kirim password, token, captcha, berkas ke semua endpoint dengan penanda unik, lalu cari penanda itu di `audit_log` dan log aplikasi: 0 hasil |
| AC-13-6 | F, S | Filter berfungsi; tidak ada rute ubah atau hapus; akun `magkom_app` yang menjalankan UPDATE atau DELETE pada `audit_log` menerima error izin |
| AC-13-7 | F | Paksa `AuditService` gagal: aksi bisnis rollback (kuota tidak berubah); audit akses data gagal: respons tetap 200 dan error masuk log |
| AC-14-1 | F | Token kosong, tidak valid, kedaluwarsa, atau dipakai ulang ditolak (`Http::fake`) |
| AC-14-2 | F | Layanan captcha timeout: registrasi ditolak (fail closed), login tetap jalan dengan rate limit (fail open) |
| AC-16-1 | F | PDF sesuai bab 8.5 untuk penempatan online dan manual; peserta lain dan Pembimbing 403 |
| AC-17-1 | F | Laporan sesuai filter; rekap nilai: Admin 403, SuperAdmin 200 |
| AC-17-2 | F | Sel berawalan `=`, `+`, `-`, `@`, tab, atau CR diberi awalan `'`; CSV dan XLSX; ekspor tercatat di audit |
| AC-18-1 | F | Akun baru; tautan ke akun terverifikasi; ke akun belum terverifikasi: password dikosongkan; email belum terverifikasi dari penyedia tidak dipercaya; callback tanpa `state` ditolak; staf tidak bisa; role selalu peserta |
| AC-19-1 | F | Sama dengan AC-18-1 |

**Uji lintas fitur** (tidak terikat satu AC):

| ID | Jenis | Kasus |
| --- | --- | --- |
| X-01 | F | **Transisi status lengkap.** Bangkitkan semua pasangan (dari, ke) untuk status pendaftaran dan penempatan. Hanya pasangan yang ada di tabel PRD 6.3 dan dilakukan peran yang benar yang berhasil; sisanya ditolak tanpa mengubah data |
| X-02 | K | Dua penerimaan paralel pada jurusan berbeda saat sisa beban Pembimbing 1; Admin A Diterima dan Admin B Ditolak bersamaan; pembatalan dan kenaikan Waiting List bersamaan pada sisa kuota 1; dua input manual khusus pada sisa slot 1; penjadwal berjalan bersamaan dengan Pembimbing menetapkan Selesai |
| X-03 | S | Jalankan kueri invarian B.6 setelah setiap test K dan setelah setiap sesi UAT |
| X-04 | F | `Mail::fake()` pada skenario penuh R1: hanya dua jenis email (verifikasi dan reset) yang pernah terkirim (BR-14) |
| X-05 | F | Jalankan seluruh suite dengan zona waktu sistem UTC dan `app.timezone=Asia/Jakarta`; semua test tanggal tetap hijau |
