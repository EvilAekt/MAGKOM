# Aliran Data dan Siklus Proses MAGKOM

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | Alur data lintas entitas, sekuens interaksi komponen, konkurensi atomik |
| Referensi Implementasi | `app/Actions/`, `app/Queries/`, `app/Services/`, `routes/web.php` |

---

## 1. Pendahuluan

Dokumen ini menjelaskan bagaimana data mengalir di dalam sistem MAGKOM pada seluruh proses bisnis utama. Setiap diagram menggambarkan interaksi antara Klien (Browser), Lapisan Keamanan, Controller, Action/Query, Service, dan Basis Data.

---

## 2. Alur 1: Pendaftaran dan Alokasi Kuota Atomik (BR-05)

Pendaftaran magang membutuhkan proteksi konkurensi ketat agar alokasi kuota jurusan tidak mengalami *overselling* atau bernilai negatif saat banyak pendaftar mengakses sistem secara simultan.

```mermaid
sequenceDiagram
    autonumber
    actor Peserta as Peserta / Ketua Tim
    participant Browser as Browser Client
    participant Controller as PendaftaranController
    participant Action as AjukanPendaftaranAction
    participant QuotaService as QuotaAllocationService
    participant DB as MySQL Database
    participant Audit as AuditService

    Peserta->>Browser: Klik "Ajukan Pendaftaran"
    Browser->>Controller: POST /peserta/pendaftaran/{id}/ajukan (CSRF Token)
    Controller->>Controller: $this->authorize('ajukan', $pendaftaran)
    Controller->>Action: execute($pendaftaran)
    
    Action->>DB: DB::beginTransaction()
    Action->>QuotaService: alokasikanKuota($jurusanId, $bulanId, $totalAnggota)
    
    critical Penguncian Pesimistik Kuota
        QuotaService->>DB: SELECT * FROM kuota WHERE jurusan_id=? AND bulan_id=? FOR UPDATE
        DB-->>QuotaService: Record Kuota Terkunci (Pessimistic Lock)
        alt Sisa Kuota Cukup (terisi + anggota <= jumlah)
            QuotaService->>DB: UPDATE kuota SET terisi = terisi + $totalAnggota
            Action->>DB: UPDATE pendaftaran SET status='diajukan', tgl_pengajuan=NOW()
            Action->>Audit: catat(TipeAuditEnum::Aksi, 'pengajuan_pendaftaran', ...)
            Action->>DB: DB::commit()
            Controller-->>Browser: Redirect ke Dashboard + Flash Sukses
            Browser-->>Peserta: Tampilan Status "Menunggu Verifikasi"
        else Kuota Penuh / Tidak Cukup
            Action->>DB: DB::rollBack()
            Controller-->>Browser: Redirect Kembali + Error "Kuota Formasi Tidak Mencukupi"
            Browser-->>Peserta: Tampilan Peringatan Kuota Habis
        end
    end
```

---

## 3. Alur 2: Verifikasi Berkas & SLA Watchdog (BR-06)

Antrean verifikasi berkas dipantau oleh SLA Watchdog dengan batasan waktu 5 hari kerja.

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Admin Verifikator
    participant Browser as Browser Admin
    participant Controller as Admin\PendaftaranController
    participant Action as PutuskanPendaftaranAction
    participant Storage as DocumentStorageService
    participant DB as MySQL Database

    Admin->>Browser: Buka Antrean Verifikasi (/admin/pendaftaran)
    Browser->>Controller: GET /admin/pendaftaran
    Controller->>DB: PendaftaranIndexQuery (Hitung selisih hari kerja SLA)
    DB-->>Controller: Daftar pendaftar dengan indikator visual SLA (Hijau/Kuning/Merah)
    Controller-->>Browser: Render antrean verifikasi
    
    Admin->>Browser: Tinjau Berkas Pendaftar
    Browser->>Controller: GET /admin/pendaftaran/{id}/berkas/{docId}
    Controller->>Storage: getTemporarySignedUrl($dokumen, ttl: 5 menit)
    Storage-->>Controller: Temporary Signed URL
    Controller-->>Browser: Tampilkan pratinjau berkas PDF/Gambar

    Admin->>Browser: Tetapkan Keputusan (Disetujui / Perlu Perbaikan / Ditolak)
    Browser->>Controller: POST /admin/pendaftaran/{id}/putuskan
    Controller->>Action: execute($pendaftaranId, $keputusan, $catatan)
    Action->>DB: UPDATE pendaftaran SET status=$keputusan, catatan_verifikasi=$catatan
    Action->>DB: INSERT INTO dokumen_verifikasi (riwayat pemeriksaan)
    
    alt Keputusan: Disetujui
        Note over Action,DB: Pendaftar otomatis masuk ke antrean Belum Ditempatkan
    else Keputusan: Ditolak
        Action->>DB: Kembalikan alokasi kuota jurusan (UPDATE kuota SET terisi = terisi - $anggota)
    end
    
    Controller-->>Browser: Redirect + Notifikasi Sukses
```

---

## 4. Alur 3: Penempatan Dua Tahap & Plotting Pembimbing (BR-07 & BR-08)

Pemisahan antara tahap kelolosan seleksi berkas dan penetapan penempatan bidang dinas.

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Admin Verifikator
    participant Controller as Admin\PenempatanController
    participant Action as AssignPesertaAction
    participant PlacementService as PlacementService
    participant DB as MySQL Database
    participant PdfService as SuratDiterimaPdfService

    Admin->>Controller: POST /admin/penempatan (pendaftaran_id, bidang_id, pembimbing_id, periode)
    Controller->>Controller: $this->authorize('assign', Penempatan::class)
    Controller->>Action: execute(...)
    
    Action->>PlacementService: validasiBebanPembimbing($pembimbingId)
    PlacementService->>DB: SELECT COUNT(*) FROM penempatan WHERE pembimbing_id=? AND status='aktif'
    DB-->>PlacementService: Jumlah mentee aktif saat ini
    
    alt Mentee Aktif >= 20 (BR-08)
        PlacementService-->>Action: Exception "Beban bimbingan pembimbing telah mencapai batas maksimal"
        Action-->>Controller: Gagal
        Controller-->>Admin: Pesan Kesalahan Batas Beban Bimbingan
    else Mentee Aktif < 20
        Action->>DB: DB::beginTransaction()
        Action->>DB: INSERT INTO penempatan (pendaftaran_id, bidang_id, pembimbing_id, status='aktif', ...)
        Action->>PdfService: terbitkanSuratPenerimaan($penempatan)
        PdfService->>DB: Simpan path PDF Surat Penerimaan ber-QR Code
        Action->>DB: DB::commit()
        Controller-->>Admin: Redirect + Pesan Penempatan Sukses
    end
```

---

## 5. Alur 4: Tugas Terstruktur, Batch Assignment & Batal Kirim (BR-18 & BR-19)

Menggambarkan alur penugasan serentak (*batch classroom*) oleh pembimbing serta hak peserta untuk melakukan *unsubmit* laporan sebelum dinilai.

```mermaid
sequenceDiagram
    autonumber
    actor Pembimbing as Pembimbing Lapangan
    actor Peserta as Peserta Magang
    participant ControllerPbd as Pembimbing\TugasController
    participant ControllerPst as Peserta\TugasController
    participant BatchAction as CreateBatchTugasAction
    participant DB as MySQL Database

    Pembimbing->>ControllerPbd: POST /pembimbing/tugas/batch (mentee_ids[], judul, deskripsi, deadline)
    ControllerPbd->>BatchAction: execute($data)
    loop Setiap Mentee Terpilih
        BatchAction->>DB: INSERT INTO tugas (penempatan_id, judul, deskripsi, deadline, status='belum')
    end
    ControllerPbd-->>Pembimbing: Notifikasi "Tugas serentak berhasil diterbitkan"

    Note over Peserta,ControllerPst: Peserta Mengerjakan dan Mengirimkan Tugas
    Peserta->>ControllerPst: POST /peserta/tugas/{id}/submit (berkas_jawaban, catatan)
    ControllerPst->>DB: INSERT INTO progres_log (tugas_id, status='menunggu', file_hasil)
    ControllerPst->>DB: UPDATE tugas SET status='proses'
    ControllerPst-->>Peserta: Notifikasi "Tugas berhasil dikumpulkan"

    Note over Peserta,ControllerPst: Skenario Pembatalan Pengiriman (Unsubmit - BR-18)
    opt Peserta Menyadari Kesalahan Berkas SEBELUM Dinilai
        Peserta->>ControllerPst: POST /peserta/tugas/{id}/unsubmit
        ControllerPst->>DB: SELECT status FROM progres_log WHERE id=?
        alt Status Progres == 'menunggu' (Belum dinilai)
            ControllerPst->>DB: DELETE FROM progres_log WHERE id=? (atau status='draf')
            ControllerPst->>DB: UPDATE tugas SET status='belum'
            ControllerPst-->>Peserta: "Pengiriman tugas berhasil dibatalkan. Silakan unggah berkas revisi."
        else Status Progres == 'diterima'
            ControllerPst-->>Peserta: Ditolak "Tugas telah dinilai pembimbing, tidak dapat dibatalkan."
        end
    end
```

---

## 6. Alur 5: Logbook Harian dengan Batas Mundur H-3 (BR-09)

Validasi batas pengisian logbook untuk mencegah pelaporan rapel di akhir masa magang.

```mermaid
sequenceDiagram
    autonumber
    actor Peserta as Peserta Magang
    participant Controller as Peserta\LogbookController
    participant FormRequest as LogbookRequest
    participant Action as CreateLogbookAction
    participant DB as MySQL Database

    Peserta->>Controller: POST /peserta/logbook (tgl_kegiatan, uraian, lampiran[])
    Controller->>FormRequest: Validasi Input
    
    critical Pemeriksaan Batas Mundur (BR-09)
        FormRequest->>FormRequest: Hitung selisih: today() - tgl_kegiatan
        alt Selisih Hari Kerja > 3 Hari
            FormRequest-->>Peserta: HTTP 422 "Pengisian logbook maksimal mundur 3 hari kerja"
        else Selisih <= 3 Hari & Tidak Melebihi Hari Ini
            FormRequest-->>Controller: Data Valid
            Controller->>Action: execute($validatedData)
            Action->>DB: INSERT INTO logbook (penempatan_id, tgl_kegiatan, uraian, status='terkirim')
            Action->>DB: Simpan lampiran berkas ke storage privat
            Controller-->>Peserta: Redirect + Flash "Logbook harian berhasil dikirim"
        end
    end
```

---

## 7. Alur 6: Siklus Penyelesaian Magang, Masa Tenggang & Penonaktifan (BR-11 & BR-12)

```mermaid
sequenceDiagram
    autonumber
    actor Pembimbing as Pembimbing Lapangan
    participant Controller as Pembimbing\TugasController
    participant Scheduler as Artisan Console Scheduler
    participant DB as MySQL Database
    participant Audit as AuditService

    Pembimbing->>Controller: POST /pembimbing/peserta/{id}/selesai
    Controller->>DB: UPDATE penempatan SET status='selesai', tgl_selesai_aktual=NOW()
    Controller->>Audit: catat(TipeAuditEnum::Aksi, 'penyelesaian_magang', ...)
    Controller-->>Pembimbing: "Masa magang mentee berhasil diselesaikan"

    Note over DB: Akun peserta memasuki Masa Tenggang (2 Bulan)
    Note over DB: Peserta masih dapat login read-only untuk unduh dokumen

    Note over Scheduler,DB: Eksekusi Harian: php artisan magkom:purge-grace-period
    Scheduler->>DB: SELECT * FROM users WHERE role='peserta' AND penempatan.status='selesai' AND tgl_selesai < (NOW() - 2 Bulan)
    DB-->>Scheduler: Daftar akun kedaluwarsa
    loop Setiap Akun Kedaluwarsa
        Scheduler->>DB: UPDATE users SET is_active = false
        Scheduler->>Audit: catat(TipeAuditEnum::Service, 'penonaktifan_akun_otomatis', ...)
    end
```

---

## 8. Alur 7: Audit Trail Multi-Kanal & Sanitasi Kredensial (BR-14)

Seluruh mutasi sistem melewati sanitasi rekursif untuk mencegah kebocoran informasi sensitif ke dalam log forensik:

```mermaid
flowchart TD
    A["Operasi Terjadi (Login / Update Profil / Mutasi Data)"] --> B["Panggilan AuditService::catat(Kanal, Aksi, Data)"]
    B --> C["Kanal Stream Klasifikasi: auth | aksi | service | akses_data"]
    C --> D["Sanitasi Rekursif Regex (maskingKredensial)"]
    D --> E{"Apakah data memuat kata kunci sensitif?<br>(password, token, credential, secret, pin)"}
    E -- "Ya" --> F["Ganti nilai dengan '***SANITIZED***'"]
    E -- "Tidak" --> G["Pertahankan nilai asli"]
    F --> H["Kompilasi Metadata: user_id, ip_address, user_agent, timestamp"]
    G --> H
    H --> I["INSERT INTO audit_log (Append-Only)"]
```
