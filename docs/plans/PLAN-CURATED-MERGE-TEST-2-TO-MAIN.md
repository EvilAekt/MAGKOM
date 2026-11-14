# TASK SPECIFICATION & MERGE STRATEGY PRD (v1.0)
## Integrasi Terkurasi (*Curated Merge*) dari `test-2` ke `main` & Standarisasi Higienitas Dokumentasi

- **Dokumen Referensi:** PRD MagKom v1.2, TDD MagKom v1.1, Design System Sleman (PRD 07), Git Workflow Rule 5 & 12 AGENTS.md
- **Peran Pembuat Dokumen:** Senior Lead Developer
- **Target Pembaca / Pelaksana:** Junior Developer & AI Assistant (Gemini 3.8 Flash / Claude / Cursor)
- **Tingkat Kerumitan:** Tinggi (*Git Release Engineering, Code Curation, Zero Regression, Living Documentation Architecture*)
- **Target Cabang:** Integrasi dari `test-2` &rarr; `main`
- **Task ID:** `TASK-RELEASE-CURATED-MERGE-TEST2-TO-MAIN-01`
- **Status Dokumen:** **SELESAI DIEKSEKUSI & DIVERIFIKASI (*COMPLETED & VERIFIED*)**
- **Pull Request Resmi:** [GitHub PR #11](https://github.com/EvilAekt/MAGKOM/pull/11)

---

## 1. DIRECTIVE & PESAN DARI SENIOR LEAD DEVELOPER

Halo Junior Developer / AI Assistant! 👋

Kita telah menyelesaikan serangkaian pengembangan besar di branch `test-2`:
- Standarisasi Floating Flyout Submenu pada Sidebar Mini (`nav-sm`) dengan tema biru Sleman.
- Standarisasi Modal Konfirmasi Aksi Kritis pada modul SuperAdmin (Bidang, Bulan, Jurusan, Slot Khusus, Backup).
- Dynamic SVG Anti-Bot Captcha dengan DOM Shuffling & Decoy Fields.
- Enforce registrasi sebelum login & pembaruan diagnostik reset password.
- Perbaikan alokasi kuota kelompok dan penempatan magang.
- Seeder pembersihan akun non-Gmail (`PurgeAndSeedCleanGmailAccountsSeeder`).
- Bento layout dan optimasi responsive universal di seluruh modul.

Sekarang pengguna meminta:
> *"test-2 merger ke main dong biar gabung tapi di bersihin yaa ga semua di masukin ke main yaa kayak yg penting aja yg di masukin sama update lagi docsnya sama rapiiin docsnya juga biar rapi yg ada di main sama dari test-2 yg mau di masukin ke main saya mau kerapian terjaga docsnya sihh yg ada di branch main..."*

### ⚠️ PERINGATAN KERAS: JANGAN JALANKAN `git merge test-2` SECARA BUTA!
Jika kamu hanya menjalankan `git merge test-2` langsung ke `main`, repositori `main` akan **KOTOR KEMBALI** karena menarik kembali:
1. Ratusan berkas internal AI tools di `.agents/skills/*` dan `.agents/rules/*` yang sebelumnya sudah sengaja dibersihkan dari `main`.
2. Berkas sampah lockfile `skills-lock.json`.
3. 8+ berkas drafting rencana kerja sementara di `docs/plans/*`.
4. Berkas dump gambar sementara di `docs/screenshots/*`.
5. Menimpa berkas dokumentasi resmi `main` yang sudah rapi seperti `docs/08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md` dan file tata kelola tim (`LICENSE`, `CONTRIBUTING.md`, `CHANGELOG.md`, `SECURITY.md`).

**Misi kita adalah melakukan *Curated & Clean Merge*:**
Hanya membawa fitur aplikasi, aset Vite, test suite, dan seeder yang valid ke `main`, menyelaraskan seluruh dokumentasi di `docs/` agar rapi, higienis, dan terstruktur standar enterprise!

---

## 2. MATRIKS KURASI: APA YANG MASUK & APA YANG DIBERSIHKAN

Berikut adalah aturan kurasi berkas yang wajib dipatuhi:

### 2.1 Berkas yang WAJIB Dimerge ke `main` (Fitur & Kode Produksi):
| Kategori Berkas | Direktori / Berkas | Cakupan Fitur |
| :--- | :--- | :--- |
| **Backend Core** | `app/` | Controller, Form Requests, Services, Actions, Queries, Models, Notifications, Commands, ViewComposers, Policies |
| **Frontend Styling** | `resources/css/` | CSS Layouts (Gentelella v4, Flyout Sleman Blue, Bento Hero, responsive styling, design tokens) |
| **Frontend Scripts** | `resources/js/` | Modular ES Modules (Flyout logic, dynamic captcha, modal handlers, responsive drawer, GSAP motion) |
| **Blade Views** | `resources/views/` | Seluruh partials, dashboard bento hero, modal standard, layout nav-sm, unified profile & account dropdown |
| **Routes** | `routes/web.php`, `routes/api.php` | Routing resmi web dan endpoint stream SSE |
| **Database** | `database/migrations/`, `database/seeders/` | Skema database dan seeder baru `PurgeAndSeedCleanGmailAccountsSeeder.php` |
| **Automated Tests** | `tests/Feature/`, `tests/Unit/` | Seluruh test suite pengujian (Auth, Sidebar, Kuota, Logbook, SuperAdmin, Captcha) |
| **Config & Build** | `config/`, `vite.config.js`, `package.json` | Konfigurasi runtime dan build pipeline Vite |

### 2.2 Berkas yang DILARANG Masuk ke `main` (Wajib Dikecualikan/Dibersihkan):
| Kategori Berkas | Direktori / Berkas | Alasan Pembersihan |
| :--- | :--- | :--- |
| **AI Skills Internal** | `.agents/skills/*` | Berkas prompt internal agent yang tidak relevan untuk production `main` |
| **AI Rules Internal** | `.agents/rules/*` | Aturan internal agent (di `main` sudah terarsip di `docs/wiki/project-rules/`) |
| **Lockfile Agent** | `skills-lock.json` | Lockfile dependensi AI prompt |
| **Drafting Plans Sementara** | `docs/plans/*` | Rencana kerja teknis harian; isinya dirangkum ke dalam dokumentasi resmi `docs/08-...` |
| **Raw Screenshots Dump** | `docs/screenshots/*` | File gambar mentah yang memperberat riwayat git |
| **Obsolete Testing Routes** | File sisa rute telemetri lama yang dihapus | Menghindari file zombie |

### 2.3 Berkas Tata Kelola `main` yang WAJIB DIPERTAHANKAN:
| Berkas | Lokasi | Nilai Penting yang Harus Dijaga |
| :--- | :--- | :--- |
| **`LICENSE`** | Root | Lisensi resmi kepemilikan Diskominfo |
| **`CONTRIBUTING.md`** | Root | Panduan kontribusi kolaborator tim |
| **`CHANGELOG.md`** | Root | Riwayat versi Keep a Changelog (akan di-bump ke `v1.3.0`) |
| **`SECURITY.md`** | Root | Kebijakan pelaporan kerentanan keamanan |
| **`README.md`** | Root | Tabel kontributor tim resmi |
| **`docs/PRODUCT.md`** | `docs/` | Spesifikasi produk high-level |
| **`docs/wiki/`** | `docs/` | Wiki arsitektur internal |

---

## 3. RESTRUKTURISASI & HIGIENITAS DOKUMENTASI (`docs/`)

Untuk memastikan kerapian dokumen di branch `main` tetap terjaga pada standar tertinggi:

### 3.1 Resolusi Konflik Penomoran Dokumen
- Di `test-2` ada file: `docs/08-PANDUAN-SMTP-DAN-NOTIFIKASI-EMAIL.md`.
- Di `main` ada file: `docs/08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md`.
- **Solusi Standarisasi**:
  Ubah nama berkas panduan SMTP menjadi **`docs/09-PANDUAN-SMTP-DAN-NOTIFIKASI-EMAIL.md`**, sehingga urutan dokumen master menjadi berurutan tanpa tumpang tindih:
  - `docs/01-PRD-MagKom.md`
  - `docs/02-TDD-MagKom.md`
  - `docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`
  - `docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`
  - `docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`
  - `docs/06-ADDENDUM-KEAMANAN-DAN-QA.md`
  - `docs/07-DESIGN-SYSTEM-MAGKOM.md`
  - `docs/08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md`
  - `docs/09-PANDUAN-SMTP-DAN-NOTIFIKASI-EMAIL.md`

### 3.2 Pembaruan `docs/08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md`
Tambahkan subbab baru yang mendokumentasikan fitur-fitur mutakhir dari `test-2`:
1. **Subbab 2.5: Navigasi Menu Melayang Sidebar Mini (*Mini-Sidebar Floating Flyout*)**:
   - Arsitektur Portal Fixed (`position: fixed; left: 72px; z-index: 1060;`).
   - Penyelarasan Palet Biru Sleman (`#02275D` / `#011C44`).
   - Tooltip otomatis untuk tautan menu mandiri.
2. **Subbab 2.6: Standarisasi Modal Konfirmasi Aksi Kritis SuperAdmin**:
   - Penyeragaman dialog modal pada Master Bidang, Master Bulan, Master Jurusan, Slot Khusus, dan Backup Basis Data.
   - Peniadaan dialog native browser `confirm()` dan `prompt()`.
3. **Subbab 2.7: Dynamic Anti-Bot SVG Captcha & DOM Shuffling**:
   - Proteksi brute force tanpa ketergantungan library luar.
   - Decoy honeypot inputs dan verifikasi server-side `CaptchaRule`.
4. **Subbab 2.8: Manajemen Akun Terkurasi & Seeder Gmail**:
   - Dokumentasi `PurgeAndSeedCleanGmailAccountsSeeder`.
5. **Subbab 2.9: Bento Grid Layout & Optimasi Antarmuka Universal**:
   - Redesain hero bento peserta, kartu antrean pembimbing, dan tabel data responsif.

### 3.3 Pembaruan `docs/07-DESIGN-SYSTEM-MAGKOM.md`
- Menambahkan dokumentasi token CSS `.nav-sm-flyout-portal`, `.nav-sm-flyout-item`, `.nav-sm-tooltip`.

### 3.4 Pembaruan `CHANGELOG.md`
- Terbitkan rilis versi **`[1.3.0] - 2026-11-14`** dengan kategori *Added*, *Changed*, *Fixed*, dan *Security*.

---

## 4. PROSEDUR EKSEKUSI TEKNIS (STEP-BY-STEP MERGE WORKFLOW)

Eksekusi wajib dilakukan dengan urutan langkah disiplin berikut:

```
[test-2] ──(Persiapan)──> [Branch Sementara: sync/test-2-to-main] ──(Kurasi & Clean Docs)──> [main]
```

### Langkah 1: Buat Branch Integrasi Sementara
Dari `main` terkini, buat branch transisi untuk melakukan kurasi:
```bash
git checkout main
git pull origin main
git checkout -b sync/test-2-to-main
```

### Langkah 2: Ambil Berkas Produksi Terpilih dari `test-2`
Gunakan `git checkout test-2 -- <path>` untuk mengimpor hanya berkas-berkas esensial:
```bash
# 1. Aplikasi Backend & Logika
git checkout test-2 -- app/

# 2. Frontend Assets (CSS, JS, Views)
git checkout test-2 -- resources/css/
git checkout test-2 -- resources/js/
git checkout test-2 -- resources/views/

# 3. Routing & Konfigurasi
git checkout test-2 -- routes/
git checkout test-2 -- config/
git checkout test-2 -- vite.config.js
git checkout test-2 -- package.json

# 4. Basis Data & Seeders
git checkout test-2 -- database/migrations/
git checkout test-2 -- database/seeders/

# 5. Automated Tests
git checkout test-2 -- tests/

# 6. Panduan Tambahan Tertentu
git checkout test-2 -- docs/08-PANDUAN-SMTP-DAN-NOTIFIKASI-EMAIL.md
```

### Langkah 3: Restrukturisasi dan Pembersihan di Branch Sinkronisasi
1. Ubah nama panduan SMTP:
   ```bash
   mv docs/08-PANDUAN-SMTP-DAN-NOTIFIKASI-EMAIL.md docs/09-PANDUAN-SMTP-DAN-NOTIFIKASI-EMAIL.md
   ```
2. Pastikan direktori `.agents/skills/`, `docs/plans/`, `docs/screenshots/`, dan file `skills-lock.json` **TIDAK ADA** di working tree.
3. Perbarui `docs/08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md` dengan fitur terbaru.
4. Perbarui `docs/07-DESIGN-SYSTEM-MAGKOM.md`.
5. Perbarui `CHANGELOG.md` untuk versi `[1.3.0]`.

### Langkah 4: Jalankan Quality Gates Lengkap
```bash
# 1. Build Aset Frontend
npm run build

# 2. Formatter PHP
./vendor/bin/pint --test

# 3. Analisis Statis PHPStan
./vendor/bin/phpstan analyse app database/seeders --memory-limit=1G

# 4. Seluruh Test Otomatis
php artisan test
```

### Langkah 5: Commit & Gabungkan ke `main`
```bash
git add .
git commit -m "feat: curated merge of features, UI optimizations, and clean living docs from test-2"

# Gabungkan ke branch main
git checkout main
git merge sync/test-2-to-main --no-ff -m "Merge branch 'sync/test-2-to-main' into main (Curated Release v1.3.0)"

# Hapus branch sementara
git branch -d sync/test-2-to-main
```

### Langkah 6: Push ke Remote `origin/main`
*(Hanya dilakukan setelah semua quality gate lulus 100%)*
```bash
git push origin main
```

---

## 5. QUALITY GATES & VERIFIKASI PENGUJIAN

Sebelum branch `main` dipush, seluruh pengujian berikut harus berstatus hijau:
1. **Asset Pipeline**: `npm run build` berhasil tanpa error.
2. **PHP Formatting**: `./vendor/bin/pint --test` 0 violation.
3. **Static Analysis**: `./vendor/bin/phpstan analyse` [OK] 0 error.
4. **Feature & Unit Tests**: `php artisan test` &rarr; 740+ assertions lulus.
5. **Kebersihan Git**:
   - `git status` bersih.
   - Tidak ada file `.agents/skills/` di `main`.
   - Tidak ada file `docs/plans/` di `main`.
   - Berkas `docs/` terurut rapi dari `01` sampai `09`.

---

## 6. DEFINITION OF DONE (DoD)

- [x] Seluruh kode fitur, perbaikan UI, seeder, dan test suite dari `test-2` berhasil diintegrasikan ke `main`.
- [x] Berkas internal (`.agents/skills/`, `skills-lock.json`, `docs/plans/`, `docs/screenshots/`) bersih 100% dari branch `main`.
- [x] Berkas tata kelola (`LICENSE`, `CONTRIBUTING.md`, `SECURITY.md`, `README.md`) terjaga utuh.
- [x] Berkas panduan SMTP dinomori sebagai `docs/09-PANDUAN-SMTP-DAN-NOTIFIKASI-EMAIL.md`.
- [x] Dokumen `docs/08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md` diperbarui dengan modul mini-sidebar flyout, modal konfirmasi, seeder Gmail, dan captcha anti-bot.
- [x] Dokumen `CHANGELOG.md` diperbarui dengan entri versi `[1.3.0]`.
- [x] Seluruh automated test suite (942 tests, 6,132 assertions), PHPStan (0 errors), Pint, dan Vite build lulus 100%.
- [x] Hasil integrasi di-push dan Pull Request #11 dibuka ke `origin/main` secara aman.
