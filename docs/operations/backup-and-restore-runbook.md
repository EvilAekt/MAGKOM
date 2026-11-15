# Standar Operasional Prosedur & Runbook Pemulihan Bencana (DR Runbook)

**Aplikasi:** MAGKOM (Sistem Informasi Manajemen Magang Kominfo)  
**Versi Runbook:** 2.0 (Post-Hardening Enkripsi AES-256-CBC)  
**Dokumen:** `docs/operations/backup-and-restore-runbook.md`  
**Target Pemulihan Teruji:**  
- **RTO (Recovery Time Objective):** $\le$ 15 Menit (Aktual restore drill basis data: **44.69 detik**)  
- **RPO (Recovery Point Objective):** $\le$ 24 Jam (Jadwal snapshot harian pukul 02:00 WIB)  
**Tingkat Kerahasiaan:** Internal Instansi / Terbatas (SuperAdmin & Tim Infrastruktur)  

---

## 1. Pendahuluan & Prinsip Keamanan

Dokumen ini merupakan panduan resmi bagi tim administrator infrastruktur dan SuperAdmin untuk melakukan pencadangan terenkripsi dan pemulihan darurat (*disaster recovery drill/incident*) ketika terjadi kerusakan server, korupsi basis data, serangan ransomware, atau kehilangan disk penyimpanan.

### Ketentuan Keamanan Utama (Guardrails):
1. **Dilarang Melakukan Restore di Web:** Endpoint pemulihan basis data tidak pernah dibuka melalui antarmuka web publik maupun panel CMS demi mencegah eksploitasi web shell atau penghapusan data massal.
2. **Enkripsi Kuat Standar Militer (AES-256-CBC):** Seluruh berkas cadangan dienkripsi menggunakan algoritma `AES-256-CBC` dengan otentikasi integritas `HMAC-SHA256` (*Encrypt-then-MAC*) berheader `MAGKOM_ENC_v1`.
3. **Pemisahan Kunci Enkripsi:** Kunci enkripsi (`BACKUP_ENCRYPTION_KEY`) disimpan pada environment vault server dan dilarang dimasukkan ke dalam arsip cadangan.
4. **Isolasi Lingkungan Pengujian:** Uji coba pemulihan (*restore drill*) wajib dilakukan pada koneksi atau basis data terisolasi (misal: `mysql_test` / `magkom_test`), dilarang langsung dieksekusi pada basis data produksi tanpa persetujuan insidental.

---

## 2. Parameter & Konfigurasi Lingkungan (`.env`)

```ini
# ==========================================
# MAGKOM BACKUP & DISASTER RECOVERY CONFIG
# ==========================================
# Lokasi direktori penyimpanan lokal (default: storage/app/backups)
BACKUP_STORAGE_DIR="storage/app/backups"

# Enkripsi arsip cadangan (Wajib true di production)
BACKUP_ENCRYPTION_ENABLED=true

# Kunci rahasia 32-karakter atau base64 untuk enkripsi/dekripsi AES-256-CBC
# Catatan: Simpan salinan kunci ini di Password Manager / Vault fisik terpisah!
BACKUP_ENCRYPTION_KEY="base64:YOUR_SECRET_BACKUP_KEY_HERE_MIN_32_BYTES"

# Ambang batas ruang disk minimum (MB) sebelum snapshot diizinkan dibuat
BACKUP_MIN_FREE_DISK_MB=500

# Retensi maksimum arsip snapshot (FIFO)
BACKUP_RETENTION_MAX=30

# Salinan offsite disk (s3, sftp, atau nfs - opsional)
BACKUP_OFFSITE_DISK=
```

---

## 3. Prosedur Pembuatan Cadangan (Backup Routine)

### 3.1 Pencadangan Otomatis Harian (Cron / Task Scheduler)
Sistem telah menjadwalkan snapshot basis data otomatis setiap hari pada pukul **02:00 WIB** melalui `App\Console\Kernel`:
```bash
# Dipanggil otomatis oleh Laravel Scheduler
php artisan magkom:backup-database --clean-old
```

### 3.2 Pencadangan Manual via CLI (Sebelum Deployment / Migrasi Besar)
Sebelum melakukan rilis kode atau migrasi skema tabel baru:
```bash
# 1. Snapshot basis data terenkripsi
php artisan magkom:backup-database --clean-old

# 2. Snapshot seluruh dokumen privat peserta (CV, KTM, Surat Pengantar)
# Perintah ini mengarsipkan storage/app/private ke dalam zip terenkripsi
php artisan tinker --execute="app(App\Services\BackupDatabaseService::class)->createFilesSnapshot();"
```

---

## 4. Prosedur Pemulihan Basis Data (Database Restore)

### 4.1 Langkah Persiapan
1. Pastikan server basis data target aktif dan dapat diakses.
2. Siapkan nama berkas arsip yang akan dipulihkan (tersedia di `storage/app/backups/`).
3. Pastikan `BACKUP_ENCRYPTION_KEY` tersedia di `.env` atau siapkan kunci spesifik.

### 4.2 Perintah Eksekusi Restore CLI

```bash
# Skenario 1: Simulasi Restore Drill ke Database Testing Terisolasi (Aman)
php artisan magkom:restore-database backup-magkom-mysql-2026-11-15-181838.sql.gz.enc --connection=mysql_test

# Skenario 2: Pemulihan Bencana Riil ke Basis Data Produksi (Membutuhkan Konfirmasi)
php artisan magkom:restore-database backup-magkom-mysql-2026-11-15-181838.sql.gz.enc --connection=mysql

# Skenario 3: Otomatisasi CI / Script Bencana (Non-Interaktif)
php artisan magkom:restore-database backup-magkom-mysql-2026-11-15-181838.sql.gz.enc --connection=mysql --force
```

### 4.3 Verifikasi Pasca Pemulihan Basis Data
Segera periksa integritas tabel dan keterkaitan data:
```bash
php artisan tinker --execute="
echo 'Users: ' . App\Models\User::count() . PHP_EOL;
echo 'Pendaftaran: ' . App\Models\Pendaftaran::count() . PHP_EOL;
echo 'Kuota: ' . App\Models\Kuota::count() . PHP_EOL;
"
```

---

## 5. Prosedur Pemulihan Berkas Privat Peserta (Files Restore)

Jika disk penyimpanan berkas mengalami kerusakan atau kehilangan data unggahan:

```bash
# Memulihkan berkas dokumen dari arsip terenkripsi ke direktori storage/app/private
php artisan magkom:restore-files backup-magkom-files-2026-11-15-181838.zip.enc
```

Verifikasi keterbacaan berkas fisik:
```bash
ls -la storage/app/private/
```

---

## 6. Prosedur Rollback & Troubleshooting

| Gejala Masalah | Penyebab | Tindakan Perbaikan |
|---|---|---|
| `Verifikasi integritas HMAC gagal` | Kunci enkripsi (`BACKUP_ENCRYPTION_KEY`) tidak cocok atau berkas rusak saat transfer. | Pastikan menggunakan kunci enkripsi yang sama saat snapshot dibuat. Periksa nilai checksum `sha256` berkas. |
| `Ruang penyimpanan server tidak mencukupi` | Sisa disk kurang dari batas minimum (500 MB). | Bersihkan log lama atau pangkas berkas usang: `rm storage/logs/*.log`. |
| `Koneksi MySQL timeout / packet too large` | Kueri multi-statement melebihi `max_allowed_packet`. | Periksa konfigurasi MySQL `my.cnf`: pastikan `max_allowed_packet = 64M` atau lebih tinggi. |

---

## 7. Catatan Bukti Drill Pemulihan Aktual (Evidence)

Uji coba pemulihan riil (*Live Restore Drill*) berhasil dibuktikan pada audit hardening:
- **Tanggal Uji Coba:** 15 November 2026
- **Berkas Sumber:** `backup-magkom-mysql-2026-11-15-181838.sql.gz.enc` (0.31 MB, AES-256-CBC)
- **Target Koneksi:** `mysql_test` (MySQL 8.4.3 Port 3306)
- **Hasil:** **32 Tabel** dipulihkan secara atomik dalam **44.69 detik**. Seluruh relasi integritas (29 pengguna, 20 pendaftaran, 72 entri kuota) terverifikasi utuh tanpa data corrupt.
