# Panduan & Daftar Periksa Rotasi Kredensial (Credential Rotation Checklist)

**Project:** MAGKOM (Sistem Informasi Manajemen Magang Kominfo)  
**Tujuan:** Panduan mitigasi dan langkah operasional bagi Administrator Sistem / Devops jika berkas `.env` atau `.mcp.json` pernah terdistribusi secara tidak sengaja melalui arsip ZIP atau media non-aman sebelum soft-launch.

> [!WARNING]
> Jangan pernah melakukan rotasi kredensial produksi secara otomatis tanpa koordinasi dengan pemilik sistem. Ikuti langkah-langkah terstruktur di bawah ini secara manual.

---

## 1. Daftar Kredensial yang Perlu Ditinjau / Dirotasi

| No | Nama Variabel / Token | Lokasi Asli | Tingkat Risiko | Tindakan Rotasi Administrator |
|---|---|---|---|---|
| 1 | `APP_KEY` | `.env` | **Kritis** | Jalankan `php artisan key:generate`. **Perhatian:** Token sesi, password reset token, dan payload terenkripsi lama akan menjadi invalid setelah key dirotasi. |
| 2 | `DB_PASSWORD` | `.env` | **Tinggi** | Ubah password user database MySQL pada server DBMS (`ALTER USER 'magkom_user'@'...' IDENTIFIED BY '...';`), lalu perbarui `.env` server. |
| 3 | `MAIL_PASSWORD` | `.env` | **Tinggi** | Jika menggunakan akun Google Workspace / Gmail App Password atau SMTP relay, cabut (revoke) App Password lama di Google Account Console dan terbitkan 16-digit App Password baru. |
| 4 | `GOOGLE_CLIENT_SECRET` | `.env` | **Tinggi** | Buka Google Cloud Console -> APIs & Services -> Credentials -> OAuth 2.0 Client IDs. Buat secret baru dan hapus secret yang lama. Perbarui `GOOGLE_CLIENT_SECRET` pada environment server. |
| 5 | `SENTRY_LARAVEL_DSN` | `.env` | **Sedang** | Jika DSN terisi, buka dashboard Sentry -> Project Settings -> Client Keys (DSN), buat key baru dan nonaktifkan key lama. |
| 6 | `GITHUB_PERSONAL_ACCESS_TOKEN` | `.mcp.json` | **Tinggi** | Buka GitHub Settings -> Developer settings -> Personal access tokens. Revoke token lama dan terbitkan token baru dengan least-privilege scopes. |
| 7 | `BRAVE_API_KEY` | `.mcp.json` | **Sedang** | Buka Brave Search API Console, reset API key lama dan perbarui konfigurasi MCP lokal pengembang. |
| 8 | `API_KEY` (TestSprite) | `.mcp.json` | **Sedang** | Cabut dan buat ulang API key di dashboard TestSprite. |

---

## 2. Prosedur Aman Pengemasan Rilis (Release Packaging)

Untuk mencegah berkas kredensial terbawa ke lingkungan staging atau production:

### Menggunakan Skrip Otomatis
- **Di Windows PowerShell:**
  ```powershell
  powershell -ExecutionPolicy Bypass -File scripts/package-release.ps1
  ```
- **Di Linux / Bash / CI Pipeline:**
  ```bash
  chmod +x scripts/package-release.sh
  ./scripts/package-release.sh
  ```

Skrip di atas secara otomatis mengecualikan:
- `.env`, `.env.*` (kecuali `.env.example`)
- `.mcp.json` dan folder `.agents/`
- `.git/` dan `.github/`
- `storage/logs/*.log`
- `storage/app/backups/*`, `storage/app/private/*`
- Folder `scratch/` dan `dist/`
- Skrip diagnostik `tests/check_status.php` dan `tests/reset_data.php`

### Menggunakan Git Archive
Repositori telah dikonfigurasi dengan aturan `export-ignore` pada berkas `.gitattributes`:
```bash
git archive --format=zip HEAD -o dist/magkom-source.zip
```
Berkas non-produksi akan otomatis terabaikan saat `git archive` dijalankan.

---

## 3. Checklist Verifikasi Administrator Pasca-Rotasi

- [ ] `APP_KEY` baru telah aktif dan aplikasi dapat melakukan enkripsi/dekripsi dengan normal (`php artisan tinker` -> `encrypt('test')`).
- [ ] Koneksi database MySQL berhasil tersambung dengan kredensial baru.
- [ ] Pengiriman email verifikasi berhasil (`php artisan auth:test-reset user@domain.com` diuji pada staging).
- [ ] Login Google OAuth berfungsi dengan `GOOGLE_CLIENT_SECRET` baru.
- [ ] Log Sentry berhasil merekam error diagnostik baru.
- [ ] Berkas `.env` di server production memiliki hak akses ketat (`chmod 600 .env` dan pemilik `www-data` / service user).
