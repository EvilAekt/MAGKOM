# MANAJEMEN KUNCI ENKRIPSI & ARSITEKTUR CADANGAN DATA (BACKUP-001, BACKUP-002, BACKUP-003)

Dokumen ini menjelaskan arsitektur enkripsi cadangan mandiri, format arsip terenkripsi `MAGKOM_ENC_v2`, strategi rotasi kunci (*key rotation*), serta penegakan replikasi off-site (*fail-closed*) pada aplikasi **MAGKOM**.

---

## 1. ARSITEKTUR STREAMING CHUNKED ENCRYPTION (BACKUP-001)

Sistem cadangan sebelumnya memuat seluruh berkas arsip ke dalam RAM via `file_get_contents()`. Pada basis data berukuran gigabyte, hal ini berisiko memicu *Out-Of-Memory* (OOM) fatal pada server.

### Desain Streaming Bounded Memory (< 32 MB RAM):
- **Chunk-Based Encryption:** Berkas dialirkan dan diproses dalam blok tetap sebesar **64 KB (`65,536 bytes`)**.
- **Independensi Memori:** Berapa pun ukuran dump basis data (100 MB, 10 GB, 50 GB), penggunaan RAM PHP tetap konstan di bawah **5 MB**, jauh di bawah ambang batas aman 32 MB.
- **Encrypt-then-MAC Per Chunk:** Setiap chunk 64 KB dienkripsi secara mandiri dengan AES-256-CBC menggunakan IV acak tersendiri, lalu di-hash dengan HMAC-SHA256 untuk mendeteksi manipulasi atau pemotongan berkas di tengah jalan.

---

## 2. SPESIFIKASI FORMAT ARSIP `MAGKOM_ENC_v2`

```
┌────────────────────────────────────────────────────────────────────────┐
│ Header Berkas (Header Paket Utama):                                    │
│ - Magic Identifier (13 Bytes): "MAGKOM_ENC_v2"                         │
│ - Key ID Length (1 Byte uint8): N                                      │
│ - Key ID String (N Bytes ASCII): mis. "prod-2026-v1"                   │
│ - PBKDF2 Salt (16 Bytes random): Salt untuk derivasi kunci master      │
├────────────────────────────────────────────────────────────────────────┤
│ Sequence of Chunks (Berulang hingga End-of-File):                      │
│ ┌────────────────────────────────────────────────────────────────────┐ │
│ │ Chunk 1:                                                           │ │
│ │ - Payload Length (4 Bytes uint32 BE): Panjang ciphertext chunk     │ │
│ │ - Chunk IV (16 Bytes random): Vektor inisialisasi AES-256-CBC      │ │
│ │ - Chunk HMAC (32 Bytes SHA-256): hash_hmac(IV + Ciphertext)        │ │
│ │ - Chunk Ciphertext: Data terenkripsi                               │ │
│ └────────────────────────────────────────────────────────────────────┘ │
│ ┌────────────────────────────────────────────────────────────────────┐ │
│ │ Chunk 2 ... Chunk N                                                │ │
│ └────────────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────┘
```

### Kompatibilitas ke Belakang (Backward Compatibility):
Saat memulihkan cadangan, sistem membaca 13 byte pertama:
- Jika berkas diawali `MAGKOM_ENC_v2`: Menggunakan streaming chunked decryptor.
- Jika berkas diawali `MAGKOM_ENC_v1`: Menggunakan legacy block decryptor.
Hal ini menjamin arsip cadangan versi lama tetap dapat dipulihkan tanpa kegagalan.

---

## 3. PEMISAHAN KUNCI & GUARDRAIL PRODUKSI (BACKUP-002)

### A. Larangan Menggunakan `APP_KEY`
Kunci enkripsi cadangan kini terpisah secara mutlak melalui konfigurasi `BACKUP_ENCRYPTION_KEY`:

```ini
# .env produksi
BACKUP_ENCRYPTION_ENABLED=true
BACKUP_ENCRYPTION_KEY=base64:Xk82qM...DEDICATED_KEY_BUKAN_APP_KEY...
BACKUP_KEY_ID=prod-2026-v1
```

### B. Guardrail Otomatis Lingkungan Produksi:
Pada saat pembuatan atau pemulihan cadangan (`resolveEncryptionKey()`):
1. Jika `APP_ENV=production` dan `BACKUP_ENCRYPTION_KEY` kosong: Sistem seketika memicu `\RuntimeException` (Fail-Closed).
2. Jika `APP_ENV=production` dan `BACKUP_ENCRYPTION_KEY === APP_KEY`: Sistem seketika memicu `\RuntimeException` ("*Kunci enkripsi cadangan tidak boleh menggunakan APP_KEY pada lingkungan produksi*").

---

## 4. STRATEGI ROTASI KUNCI (KEY ROTATION WORKFLOW)

Ketika kebijakan keamanan mewajibkan rotasi kunci enkripsi secara berkala:

1. Buat kunci baru 32-byte acak:
   ```bash
   php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
   ```
2. Perbarui konfigurasi `.env`:
   - Pasang kunci baru pada `BACKUP_ENCRYPTION_KEY`.
   - Perbarui versi `BACKUP_KEY_ID` (mis. `prod-2027-v1`).
   - Masukkan kunci lama ke dalam `BACKUP_FALLBACK_KEYS`:
   ```ini
   BACKUP_ENCRYPTION_KEY=base64:KUNCI_BARU_2027...
   BACKUP_KEY_ID=prod-2027-v1
   BACKUP_FALLBACK_KEYS=base64:KUNCI_LAMA_2026...,base64:KUNCI_LAMA_2025...
   ```
3. **Hasil:**
   - Semua snapshot baru akan dienkripsi dengan kunci 2027.
   - Semua arsip snapshot historis tahun 2026 tetap dapat didekripsi secara otomatis karena kunci lama terdaftar di fallback list!

---

## 5. KEWAJIBAN REPLIKASI OFF-SITE PRODUKSI (BACKUP-003)

1. Pada server produksi (`app()->isProduction()`):
   - Parameter `BACKUP_OFFSITE_DISK` (misal: `s3`, `r2`, atau SFTP storage) **wajib dikonfigurasi**.
   - Jika kosong, proses snapshot dibatalkan seketika dengan `\RuntimeException`.
   - Jika proses pengunggahan ke off-site storage mengalami gangguan (timeout, kegagalan network, credential invalid), proses pembuatan snapshot berstatus **GAGAL (Fail-Closed)** dan memunculkan error, alih-alih hanya dicatat sebagai silent warning.
2. Pada lingkungan lokal atau testing:
   - Jika `BACKUP_OFFSITE_DISK` tidak dikonfigurasi, sistem mengizinkan cadangan lokal tersimpan.
