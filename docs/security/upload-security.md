# ARSITEKTUR INTEGRITAS UPLOAD & PEMINDAIAN ANTIMALWARE (UPLOAD-001, UPLOAD-002)

Dokumen ini menjelaskan alur kerja pengamanan berkas unggahan pengguna (*document upload integrity and antimalware scanning pipeline*) pada aplikasi **MAGKOM**. Sistem dirancang untuk menggagalkan serangan *unrestricted file upload*, eksekusi kode jarak jauh (RCE), serta mencegah rusaknya integritas berkas saat terjadi gangguan penyimpanan.

---

## 1. ALUR PIPA PENGUNGGAHAN DOKUMEN (UPLOAD PIPELINE)

Setiap berkas yang diunggah diproses melalui pipa keamanan bertingkat di `App\Services\DocumentStorageService`:

```
[ Berkas Diunggah Pengguna ]
            │
            ▼
┌───────────────────────────────────────────────┐
│ 1. Validasi Magic Bytes & MIME Type           │  Memverifikasi byte awal berkas
│    (PDF: %PDF-, JPEG: \xFF\xD8, PNG: \x89PNG) │  Menolak ekstensi palsu (mis. malware.exe -> .pdf)
└───────────────────────┬───────────────────────┘
                        ▼
┌───────────────────────────────────────────────┐
│ 2. Validasi Batas Ukuran Berkas               │  Maksimal 2 MB (dapat dikonfigurasi via
│    (Default: 2 MB via Pengaturan)             │  parameter operasional sistem)
└───────────────────────┬───────────────────────┘
                        ▼
┌───────────────────────────────────────────────┐
│ 3. Isolasi ke Direktori Karantina             │  Disimpan sementara ke storage/app/private/quarantine/
│    (Filename: scan_{uuid}.tmp)                │  Terisolasi dari akses publik dan pengguna
└───────────────────────┬───────────────────────┘
                        ▼
┌───────────────────────────────────────────────┐
│ 4. Pemindaian Malware & Inspeksi Heuristik    │  Memindai virus EICAR, header biner PE/ELF,
│    (AntivirusScanService)                     │  webshell PHP (<?php), dan aksi aktif PDF
└───────────────────────┬───────────────────────┘
                        │
       Bersih? ─────────┴───────── Terinfeksi?
          │                             │
          │                             ▼
          │              ┌──────────────────────────────────────────────┐
          │              │ - Hapus berkas karantina seketika            │
          │              │ - Catat insiden keamanan ke Audit Log        │
          │              │ - Kembalikan ValidationException (Ditolak)   │
          │              └──────────────────────────────────────────────┘
          ▼
┌───────────────────────────────────────────────┐
│ 5. Relokasi Berkas & Verifikasi Ukuran        │  Disalin ke direktori final:
│    (Filename acak unik: doc_{uuid}.{ext})     │  documents/{pendaftaran_id}/doc_{uuid}.pdf
│    (Hapus salinan di karantina)               │  Verifikasi ukuran fisik sesuai byte sumber
└───────────────────────┬───────────────────────┘
                        ▼
┌───────────────────────────────────────────────┐
│ 6. Perhitungan Hash SHA-256 Berkas Final      │  Menghasilkan sidik jari kriptografi (checksum)
└───────────────────────┬───────────────────────┘
                        ▼
┌───────────────────────────────────────────────┐
│ 7. Transaksi Database Atomik & Penggantian    │  DB::transaction():
│    (Atomic Replacement)                       │  - Simpan / perbarui metadata dokumen di tabel dokumen
│                                               │  - Jika sukses: hapus berkas fisik lama dari disk
│                                               │  - Jika gagal (rollback): hapus berkas fisik baru,
│                                               │    berkas fisik lama tetap utuh di disk!
└───────────────────────────────────────────────┘
```

---

## 2. SPESIFIKASI PEMINDAI HEURISTIK & CLAMAV (UPLOAD-001)

Layanan `App\Services\AntivirusScanService` menyediakan dua lapis pemindaian:

### A. Engine Heuristik Mandiri (Zero-Dependency)
Bekerja secara native di PHP tanpa dependensi software eksternal:
1. **Tanda Tangan EICAR:** Mendeteksi signature standar industri antivirus EICAR.
2. **Windows Executable (MZ / PE):** Mendeteksi header `MZ` dan magic byte Windows Portable Executable (`0x4D 0x5A`).
3. **Linux / UNIX Executable (ELF):** Mendeteksi header `\x7fELF`.
4. **Script Executable Shell:** Mendeteksi shebang shell executable (`#!/bin/sh`, `#!/bin/bash`, `#!/usr/bin/env`).
5. **Embedded PHP Webshell:** Memindai seluruh berkas terhadap tag eksekusi PHP (`<?php`, `<?=`, `<script language="php">`, `eval(`, `base64_decode(`).
6. **PDF Malicious Actions:** Mendeteksi objek berbahaya di dalam dokumen PDF seperti `/Launch`, `/EmbeddedFile`, dan `/JavaScript`.

### B. Integrasi ClamAV Daemon / Clamscan
Jika server produksi memiliki ClamAV terpasang:
- Konfigurasikan `ANTIVIRUS_DRIVER=clamscan` di `.env`.
- Sistem memanggil binary `clamscan --no-summary --infected` untuk pemindaian tanda tangan ribuan virus global.

---

## 3. INTEGRITAS PENYIMPANAN & FAIL-SAFE ROLLBACK (UPLOAD-002)

1. **Konfigurasi Disk Privat Throw:**
   Pada `config/filesystems.php`, disk `private` dikonfigurasi dengan `'throw' => true`. Setiap kegagalan I/O storage seketika memicu exception dan mencegah kegagalan diam-diam (*silent failure*).
2. **Kompensasi Rollback Transaksional:**
   Jika query database gagal (misal koneksi terputus saat menyimpan model `Dokumen`), berkas baru yang telah disalin ke disk otomatis dihapus (`Storage::disk('private')->delete($newPath)`).
3. **Pelestarian Berkas Lama:**
   Berkas lama **tidak pernah dihapus** sebelum transaksi database berhasil di-commit secara penuh. Jika proses update gagal di tengah jalan, pengguna tidak akan kehilangan dokumen sebelumnya.
