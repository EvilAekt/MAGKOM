# Rekam Jejak Audit Forensik (Audit Trail)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Modul Sistem | `keamanan.audit-trail` |
| Rute Terkait | `/superadmin/audit-log`, `/superadmin/audit-log/export` |
| Referensi Implementasi | `app/Services/AuditService.php`, `app/Models/AuditLog.php` |

---

## 1. Landasan Hukum dan Prinsip Append-Only

Pencatatan jejak audit forensik pada MAGKOM dibangun untuk memenuhi kepatuhan terhadap **Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)** dan **Undang-Undang Informasi dan Transaksi Elektronik (UU ITE)**.

### Prinsip Kekebalan Manipulasi (Immutability):
1. **Append-Only**: Tabel `audit_log` hanya menerima operasi penambahan baris (`INSERT`).
2. **Larangan Mutasi**: Aplikasi dilarang keras menyediakan rute, method, atau kueri untuk mengeksekusi `UPDATE` atau `DELETE` pada tabel audit. Aturan ini diverifikasi oleh suite pengujian otomatis `CoreFoundationTest::test_audit_log_bersifat_append_only_dan_menolak_update_serta_delete()`.
3. **Indeks Komposit**: Dilengkapi indeks komposit `(user_id, tipe, created_at)` untuk menjamin kecepatan kueri inspeksi forensik pada jutaan baris rekaman data.

---

## 2. Klasifikasi 4 Kanal Stream Audit

Setiap rekaman audit dikelompokkan secara terstruktur ke dalam 4 kanal stream (`TipeAuditEnum`):

| Kanal Stream | Lingkup Rekaman Aktivitas | Contoh Peristiwa |
|---|---|---|
| **`auth`** | Seluruh siklus sesi pengguna dan autentikasi. | `login_sukses`, `login_gagal`, `logout`, `reset_password`, `oauth_callback`. |
| **`aksi`** | Mutasi data eksplisit yang dipicu langsung oleh pengguna. | `ajukan_pendaftaran`, `verifikasi_berkas`, `tetapkan_penempatan`, `terbitkan_tugas`, `submit_tugas`, `unsubmit_tugas`, `setujui_logbook`. |
| **`service`** | Aktivitas otomatisasi background jobs dan layanan sistem. | `backup_basis_data_sukses`, `restore_basis_data`, `pembersihan_masa_tenggang_scheduler`, `quota_auto_sync`. |
| **`akses_data`** | Pembacaan dan inspeksi dokumen berkas sensitif kedinasan. | `unduh_berkas_ktp`, `unduh_bundel_zip`, `inspeksi_profil_360_mentee`. |

---

## 3. Sanitasi Kredensial Rekursif Otomatis

Untuk mencegah kebocoran informasi rahasia ke dalam basis data log, kelas `App\Services\AuditService` secara otomatis menyensor seluruh parameter sensitif sebelum disimpan:

```php
protected array $sensitiveKeys = [
    'password',
    'password_confirmation',
    'password_awal',
    'token',
    'remember_token',
    'secret',
    'api_key',
    'pin',
    'credential',
];

public function sanitizePayload(mixed $data): mixed
{
    if (! is_array($data)) {
        return $data;
    }

    foreach ($data as $key => $value) {
        if ($this->isSensitiveKey((string) $key)) {
            $data[$key] = '***SANITIZED***';
        } elseif (is_array($value)) {
            $data[$key] = $this->sanitizePayload($value);
        }
    }

    return $data;
}
```

Metode ini bekerja secara rekursif hingga ke kedalaman struktur array bersarang, menjamin tidak ada kata sandi atau token otentikasi yang tersimpan dalam teks mentah pada kolom `data_lama` maupun `data_baru`.

---

## 4. Struktur Metadata Jejak Audit

Setiap baris rekaman pada tabel `audit_log` memuat metadata forensik lengkap:
- **`user_id`**: ID pengguna yang melakukan aksi (atau `null` jika dilakukan oleh tamu anonim sebelum login).
- **`tipe`**: Klasifikasi kanal (`auth`, `aksi`, `service`, `akses_data`).
- **`aksi`**: Identifier string peristiwa teknis.
- **`deskripsi`**: Uraian kalimat penjelasan yang mudah dipahami manusia.
- **`data_lama`**: Snapshot data sebelum mutasi dalam format JSON (tersanitasi).
- **`data_baru`**: Snapshot data setelah mutasi dalam format JSON (tersanitasi).
- **`ip_address`**: Alamat IP klien (tervalidasi terhadap trusted proxy header).
- **`user_agent`**: String identitas peramban dan sistem operasi klien.
- **`created_at`**: Timestamp presisi tinggi saat peristiwa terjadi.

---

## 5. Proteksi Ekspor Data (Anti-Formula Injection)

SuperAdmin dapat mengekspor riwayat audit trail ke format CSV atau Excel XLSX untuk keperluan pelaporan eksternal atau penyelidikan forensik.

Sistem melindungi pengunduh dari serangan **Formula Injection (CSV Injection / DDE Injection)** via `XlsxExportService::sanitizeCell()`:
- Setiap nilai sel yang diawali dengan simbol formula berbahaya seperti `=`, `+`, `-`, `@`, tab `\t`, atau carriage return `\r` otomatis diprefiks dengan tanda kutip tunggal (`'`).
- Tanda kutip tunggal memaksa aplikasi pembuka spreadsheet (Microsoft Excel, LibreOffice Calc) untuk memperlakukan sel sebagai teks biasa dan mematikan eksekusi formula atau program command line jahat.

---

## 6. Kebijakan Retensi Data Audit

- Masa retensi data audit dikendalikan melalui parameter operasional `retensi_audit_akses_data_hari` (nilai default: 365 hari / 1 tahun kalender).
- Pembersihan data audit yang telah melampaui batas retensi hanya dapat dipicu secara manual oleh SuperAdmin melalui tombol pembersihan (*purge*) berotorisasi ganda dengan konfirmasi modal interaktif.
