# STANDAR OPERASIONAL PROSEDUR & RUNBOOK PEMULIHAN BENCANA (DR RUNBOOK)

**Aplikasi:** MAGKOM (Sistem Informasi Manajemen Magang Kominfo)  
**Versi Runbook:** 3.0 (Streaming Chunked Encryption `MAGKOM_ENC_v2` & Mandatory Offsite Storage)  
**Dokumen:** `docs/operations/backup-and-restore-runbook.md`  
**Target Pemulihan Teruji:**  
- **RTO (Recovery Time Objective):** $\le$ 15 Menit (Aktual restore drill basis data: **44.69 detik**)  
- **RPO (Recovery Point Objective):** $\le$ 24 Jam (Jadwal snapshot harian pukul 02:00 WIB + Replikasi Off-site S3)  
**Tingkat Kerahasiaan:** Internal Instansi / Terbatas (SuperAdmin & Tim Infrastruktur)  

---

## 1. PENDAHULUAN & PRINSIP KEAMANAN

Dokumen ini merupakan panduan resmi bagi tim administrator infrastruktur dan SuperAdmin untuk melakukan pencadangan terenkripsi dan pemulihan darurat (*disaster recovery*) ketika terjadi kerusakan server, korupsi basis data, insiden keamanan, atau kehilangan disk penyimpanan.

### Ketentuan Keamanan Utama (Guardrails):
1. **Antarmuka Jujur (Interface Honesty - Dilarang Restore di Web):**  
   Tidak ada tombol atau fitur "Restore Basis Data" pada dashboard web pengguna maupun panel SuperAdmin. Hal ini untuk mencegah risiko destruktif fatal akibat CSRF, kebocoran sesi admin, atau injeksi SQL melalui web. Seluruh proses restorasi basis data **wajib dieksekusi melalui CLI** oleh administrator yang memiliki akses shell server.
2. **Streaming Chunked Encryption (MAGKOM_ENC_v2):**  
   Seluruh berkas cadangan diproses secara streaming chunked 64 KB menggunakan `AES-256-CBC` dengan autentikasi `HMAC-SHA256` per-chunk (*Encrypt-then-MAC*). Memori PHP dibatasi secara ketat (< 32 MB RAM) terlepas dari skala data. Format warisan `MAGKOM_ENC_v1` tetap didukung secara otomatis untuk arsip lama.
3. **Pemisahan Kunci Enkripsi (Decoupled Key Management):**  
   Kunci enkripsi cadangan (`BACKUP_ENCRYPTION_KEY`) disimpan di environment server secara mandiri. **Dilarang keras menyamakan kunci backup dengan `APP_KEY`** di lingkungan produksi (sistem memiliki guardrail otomatis yang menggagalkan proses jika kunci sama).
4. **Mandatory Off-Site Replication:**  
   Pada lingkungan produksi, arsip cadangan wajib direplikasi ke object storage sekunder (`BACKUP_OFFSITE_DISK=s3`). Jika replikasi gagal, status backup dinyatakan gagal (*fail-closed*).

---

## 2. PARAMETER KONFIGURASI ENVIRONMENT (`.env`)

```ini
# ==========================================
# MAGKOM BACKUP & DISASTER RECOVERY CONFIG
# ==========================================
# Enkripsi arsip cadangan (Wajib true di production)
BACKUP_ENCRYPTION_ENABLED=true

# KUNCI MANDIRI (DILARANG SAMA DENGAN APP_KEY PADA PRODUCTION!)
BACKUP_ENCRYPTION_KEY=base64:YOUR_DEDICATED_SECRET_KEY_MIN_32_BYTES

# Identitas versi kunci aktif
BACKUP_KEY_ID=prod-2026-v1

# Kunci cadangan sebelumnya untuk mendukung dekripsi arsip lama saat rotasi kunci
BACKUP_FALLBACK_KEYS=base64:FALLBACK_KEY_1...,base64:FALLBACK_KEY_2...

# Ambang batas sisa disk minimum (MB) sebelum snapshot diizinkan dibuat
BACKUP_MIN_FREE_DISK_MB=500

# Retensi maksimum arsip snapshot lokal (FIFO)
BACKUP_RETENTION_MAX=30

# Disk offsite wajib untuk produksi (misal: 's3', 'r2')
BACKUP_OFFSITE_DISK=s3
```

---

## 3. PROSEDUR PEMBUATAN CADANGAN (BACKUP ROUTINE)

### 3.1 Pencadangan Otomatis Harian (Scheduler)
Sistem menjalankan snapshot basis data otomatis setiap hari pada pukul **02:00 WIB** melalui Scheduler Laravel:
```bash
php artisan magkom:backup-database --clean-old
```

### 3.2 Pencadangan Manual via CLI (Pra-Migrasi / Pra-Deployment)
Sebelum melakukan rilis kode atau migrasi tabel:
```bash
# 1. Buat snapshot basis data terenkripsi (AES-256-CBC)
php artisan magkom:backup-database --clean-old

# 2. Buat snapshot berkas dokumen privat peserta (storage/app/private)
php artisan tinker --execute="app(App\Services\BackupDatabaseService::class)->createFilesSnapshot();"
```

---

## 4. PROSEDUR PEMULIHAN BASIS DATA (DATABASE RESTORE)

### 4.1 Langkah Persiapan
1. Pastikan server basis data target aktif dan dapat diakses.
2. Tentukan berkas arsip yang akan dipulihkan (tersedia di `storage/app/backups/`).
3. Pastikan `BACKUP_ENCRYPTION_KEY` tersedia di `.env` atau siapkan opsi `--key`.

### 4.2 Perintah Eksekusi Restore CLI

Gunakan perintah standar `php artisan backup:restore` atau `magkom:restore-database`:

```bash
# Skenario 1: Simulasi Restore Drill ke Database Testing Terisolasi (Direkomendasikan)
php artisan backup:restore backup-magkom-mysql-2026-11-15-181838.sql.gz.enc --connection=mysql_test

# Skenario 2: Pemulihan Bencana ke Basis Data Utama (Interaktif dengan Konfirmasi)
php artisan backup:restore backup-magkom-mysql-2026-11-15-181838.sql.gz.enc --connection=mysql

# Skenario 3: Otomatisasi Recovery / Pipeline CI (Non-Interaktif)
php artisan backup:restore backup-magkom-mysql-2026-11-15-181838.sql.gz.enc --connection=mysql --force

# Skenario 4: Pemulihan dengan Kunci Spesifik (Bypass .env)
php artisan backup:restore backup-magkom-mysql-2026-11-15-181838.sql.gz.enc --connection=mysql --key="base64:SpesifikKey..."
```

### 4.3 Verifikasi Integritas Pasca Pemulihan
Jalankan verifikasi integritas model dan jumlah baris data:
```bash
php artisan tinker --execute="
echo 'Users: ' . App\Models\User::count() . PHP_EOL;
echo 'Pendaftaran: ' . App\Models\Pendaftaran::count() . PHP_EOL;
echo 'Kuota: ' . App\Models\Kuota::count() . PHP_EOL;
"
```

---

## 5. PROSEDUR PEMULIHAN BERKAS PRIVAT PESERTA (FILES RESTORE)

Jika storage fisik dokumen rusak atau berkas privat terhapus:

```bash
# Memulihkan berkas dokumen dari arsip zip terenkripsi ke direktori storage/app/private
php artisan backup:restore-files backup-magkom-files-2026-11-15-181838.zip.enc
```

Opsi kustom direktori ekstraksi:
```bash
php artisan backup:restore-files backup-magkom-files-2026-11-15-181838.zip.enc --target-dir="/mnt/secure-storage/private"
```

---

## 6. PROSEDUR ROTASI KUNCI (KEY ROTATION)

1. Buat kunci baru:
   ```bash
   php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
   ```
2. Perbarui `.env`:
   - Pasang kunci baru pada `BACKUP_ENCRYPTION_KEY`.
   - Update `BACKUP_KEY_ID` (misal: `prod-2027-v1`).
   - Masukkan kunci lama ke `BACKUP_FALLBACK_KEYS` dipisahkan tanda koma.
3. Seluruh snapshot baru menggunakan kunci baru, sedangkan snapshot lama tetap dapat didekripsi melalui fallback keys.

---

## 7. PROSEDUR TROUBLESHOOTING

| Gejala Masalah | Penyebab | Solusi |
|---|---|---|
| `Verifikasi integritas HMAC gagal` | Kunci enkripsi tidak cocok atau berkas rusak. | Periksa apakah kunci lama sudah dimasukkan ke `BACKUP_FALLBACK_KEYS`. Periksa checksum SHA-256 berkas. |
| `Ruang penyimpanan server tidak mencukupi` | Sisa disk < 500 MB. | Bersihkan berkas log usang atau snapshot lama via `pruneOldBackups()`. |
| `Offsite backup disk belum dikonfigurasi` | Pada production, `BACKUP_OFFSITE_DISK` kosong. | Konfigurasikan disk offsite (misal: `s3`) di `.env` produksi. |
| `Kunci enkripsi cadangan tidak boleh menggunakan APP_KEY` | Guardrail produksi mendeteksi kunci identik. | Hasilkan kunci terpisah untuk `BACKUP_ENCRYPTION_KEY`. |
