# Pusat Cadangan & Pemulihan Basis Data (Backup & Restore)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Modul Sistem | `sistem.cadangan-database` |
| Rute Terkait | `/superadmin/backup`, `/superadmin/backup/create`, `/superadmin/backup/{file}/restore` |
| Referensi Implementasi | `app/Services/BackupDatabaseService.php`, `app/Http/Controllers/SuperAdmin/BackupController.php` |

---

## 1. Arsitektur Pencadangan Mandiri (Disaster Recovery)

MAGKOM mengimplementasikan modul pencadangan basis data mandiri (*native service*) melalui kelas `App\Services\BackupDatabaseService` tanpa bergantung pada pustaka pihak ketiga yang berat.

### Karakteristik Teknis Pencadangan:
1. **Kompresi Gzip On-the-Fly**: Data diekspor langsung menggunakan utilitas `mysqldump` dengan kompresi Gzip menjadi berkas `.sql.gz` untuk meminimalkan konsumsi penyimpanan disk.
2. **Kekebalan Forensik (Hash SHA-256)**: Setiap berkas cadangan yang selesai dibuat dihitung nilai checksum kriptografis SHA-256 dan disimpan pada metadata cadangan untuk memverifikasi integritas arsip saat proses pemulihan.
3. **Penyimpanan Privat**: Seluruh berkas cadangan disimpan pada direktori terisolasi `storage/app/backups/` yang tidak dapat diakses langsung oleh peramban publik.
4. **Pencegahan Kehabisan Ruang Disk (Disk Exhaustion Guard)**: Sistem memeriksa ruang disk bebas sebelum membuat cadangan. Jika sisa disk kurang dari **500 MB** (`MIN_FREE_DISK_MB = 500`), proses dibatalkan otomatis untuk menjaga stabilitas server.
5. **Kebijakan Retensi Otomatis (FIFO Retention)**: Sistem mempertahankan maksimal **30 berkas cadangan terakhir** (`MAX_BACKUPS_RETAINED = 30`). Cadangan yang lebih lama otomatis dihapus saat cadangan baru dibuat.

---

## 2. Prosedur Pembuatan Cadangan (Backup Creation)

### A. Melalui Antarmuka Web SuperAdmin
1. Masuk sebagai SuperAdmin dan buka rute `/superadmin/backup`.
2. Klik tombol **"Buat Cadangan Baru"**.
3. Sistem mengeksekusi `BackupDatabaseService::createBackup()`.
4. Berkas cadangan baru akan muncul pada tabel riwayat lengkap dengan informasi ukuran file (MB), tanggal pembuatan, dan nilai hash SHA-256.

### B. Melalui Baris Perintah (Artisan CLI)
```bash
php artisan magkom:backup-db
```

---

## 3. Prosedur Pemulihan Darurat (Disaster Recovery Restore)

> [!CAUTION]
> Tindakan pemulihan (*restore*) akan menimpa seluruh skema dan data basis data saat ini dengan isi berkas cadangan yang dipilih. Selalu buat cadangan cadangan darurat terkini sebelum mengeksekusi pemulihan!

### Langkah 1: Aktifkan Mode Perawatan (Maintenance Mode)
Hentikan akses pengguna ke aplikasi agar tidak ada transaksi baru yang masuk saat basis data sedang dipulihkan:

```bash
php artisan down --secret="kunci-bypass-rahasia" --render="errors.503"
```

### Langkah 2: Eksekusi Pemulihan Basis Data
#### Opsi 1: Melalui Antarmuka SuperAdmin
1. Buka menu `/superadmin/backup` (menggunakan URL bypass rahasia jika maintenance aktif).
2. Pilih berkas cadangan yang ingin dipulihkan.
3. Klik tombol **"Pulihkan Basis Data"**.
4. Ketik teks konfirmasi pada modal interaktif untuk menyetujui penimpaan data.
5. Sistem memverifikasi integritas berkas dan menjalankan impor via `BackupDatabaseService::restoreBackup()`.

#### Opsi 2: Melalui Terminal Server (CLI Manual)
```bash
# Ekstrak dan impor langsung ke MySQL
gunzip < storage/app/backups/backup_magkom_2026-11-15_120000.sql.gz | mysql -u user_magkom -p magkom_production
```

### Langkah 3: Bersihkan Cache dan Verifikasi Sistem
Setelah basis data terpulihkan:

```bash
php artisan cache:clear
php artisan view:clear
php artisan config:cache
```

Periksa integritas sistem melalui rute `/health` atau login SuperAdmin.

### Langkah 4: Matikan Mode Perawatan
Kembalikan sistem ke status online:

```bash
php artisan up
```

---

## 4. Keamanan dan Verifikasi Integritas

Sebelum sebuah berkas cadangan diizinkan untuk dipulihkan, `BackupDatabaseService` menjalankan 3 tahap validasi:
1. **Validasi Format**: Memastikan berkas memiliki ekstensi valid `.sql.gz` dan magic bytes Gzip valid (`\x1f\x8b`).
2. **Validasi Ukuran**: Memastikan ukuran berkas > 0 byte.
3. **Pencatatan Audit Trail**: Seluruh aktivitas pembuatan, pengunduhan, dan pemulihan cadangan dicatat secara permanen ke tabel `audit_log` pada kanal stream `service`.
