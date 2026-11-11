# Kebijakan Keamanan (Security Policy) - MAGKOM

Dinas Komunikasi dan Informatika berkomitmen penuh untuk menjaga keamanan informasi dan privasi data seluruh peserta, staf, dan pengelola pada portal **MAGKOM (Portal Manajemen Magang Kominfo)**.

---

## 1. Versi yang Didukung (Supported Versions)

Saat ini hanya versi aktif yang mendapatkan pembaruan keamanan dan perbaikan celah secara langsung:

| Versi | Didukung Keamanan | Catatan |
| :--- | :--- | :--- |
| `main` / `test-2` (Baseline) | :white_check_mark: Ya | PHP 8.3.x, Laravel 10.x |
| `< 0.1.0` / Legacy | :x: Tidak | Versi arsip/eksperimental |

---

## 2. Pelaporan Kerentanan Secara Privat (Reporting a Vulnerability)

> [!CAUTION]
> **JANGAN PERNAH** mempublikasikan temuan celah keamanan (vulnerability), bukti eksploitasi (PoC), credential bocor, atau detail kelemahan sistem melalui **GitHub Issues publik** atau Pull Request terbuka.

Jika Anda menemukan atau mencurigai adanya celah keamanan pada sistem MAGKOM, silakan laporkan melalui saluran resmi berikut:

1. **Email Tim Keamanan Informasi**:
   Kirimkan email ke: **[keamanan-informasi@magkom.go.id](mailto:keamanan-informasi@magkom.go.id)**
2. **GitHub Private Vulnerability Reporting**:
   Jika fitur pelaporan privat GitHub Security diaktifkan pada repositori ini, Anda dapat memanfaatkan tab **Security → Report a vulnerability**.

### Informasi yang Wajib Disertakan dalam Laporan:
- Ringkasan kerentanan (jenis kelemahan, misal: IDOR, SQLi, CSRF, SSRF, XSS).
- Modul atau endpoint yang terdampak (URL, Controller, Form Request).
- Langkah demi langkah reproduksi (*step-by-step reproduction guide*).
- Dampak potensial (*impact analysis*) terhadap kerahasiaan (*confidentiality*), integritas (*integrity*), atau ketersediaan (*availability*) data magang.
- Rekomendasi perbaikan atau mitigasi (jika ada).

---

## 3. Komitmen Penanganan & Respons (Response Timeline)

Tim Keamanan Informasi MagKom akan menindaklanjuti setiap laporan dengan tahapan berikut:

- **Konfirmasi Penerimaan**: Maksimal **1x24 jam** pada hari kerja.
- **Triage & Validasi**: Tim teknis akan memvalidasi bukti konsep (PoC) dalam kurun waktu **3 hari kerja**.
- **Perbaikan & Verifikasi**: Patch keamanan akan dibuat pada branch privat terisolasi dan diuji melalui pipeline CI Security Gate sebelum di-merge ke branch utama.
- **Pengungkapan Terkoordinasi (Coordinated Disclosure)**: Detail kerentanan hanya dapat dipublikasikan setelah patch keamanan resmi dirilis ke server produksi dan disetujui bersama oleh pelapor serta pengelola sistem.

---

## 4. Standar Pengujian Keamanan Internal (DevSecOps Baseline)

Repositori ini menerapkan kontrol keamanan berlapis otomatis pada setiap pull request dan commit:
- **Secret Scanning**: Pendeteksian kebocoran token dan private key secara otomatis (Gitleaks).
- **Static Application Security Testing (SAST)**: Pemindaian pola kode rentan (Semgrep Laravel Security Rules).
- **In-Tree Security Unit & Feature Tests**: Pengujian ketat terhadap RBAC, IDOR, brute-force protection, signed URL, dan kuota atomik (`lockForUpdate`).
- **Dependency Audit**: Pemantauan kerentanan CVE pada Composer dan NPM.

