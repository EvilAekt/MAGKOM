> Diperbarui: 2026-11-09 | mode: Verified Living Documentation

# Alur Data & Skema Database (DATA-FLOW.md)

Dokumen ini menjelaskan aliran data untuk proses bisnis inti, relasi antar entitas, serta Entity-Relationship Diagram (ERD) dari 24 tabel domain model Eloquent aktif dalam Portal MagKom.

> **Catatan Singkronisasi**:  
> Untuk rincian kamus data (*data dictionary*) lengkap 24 tabel domain dan 5 tabel framework, rujuk dokumen utama [docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md](../04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md).

---

## 1. Alur Data Fitur Utama

### A. Alur Pendaftaran & Seleksi Magang (Tanpa Waiting List)
```mermaid
sequenceDiagram
    autonumber
    actor P as Calon Peserta
    participant C as PesertaPendaftaranController
    participant S as PlacementService
    participant DB as Database (Pendaftaran, Kuota)
    participant SEC as DocumentStorageService

    P->>C: Mengisi Form & Mengunggah Berkas (KTP, KTM, Surat Pengantar)
    C->>SEC: Enkripsi nama acak & simpan berkas ke storage/app/berkas/
    C->>DB: Buat record Pendaftaran (status: DRAFT)
    P->>C: Klik "Ajukan Pendaftaran"
    C->>S: Evaluasi kuota jurusan di bulan terpilih via lockForUpdate()
    alt Kuota Tersedia
        S->>DB: Ubah status menjadi MENUNGGU_VERIFIKASI
        C-->>P: Redirect sukses dengan Flash Message
    else Kuota Penuh
        S->>DB: Pendaftaran ditolak tegas (Kuota Habis / DITOLAK)
        C-->>P: Notifikasi kuota penuh (Seleksi transparan tanpa antrean tersembunyi)
    end
```

### B. Alur Eksekusi Magang, Logbook & Penyelesaian Bimbingan 1-Klik
```mermaid
sequenceDiagram
    autonumber
    actor M as Peserta Magang
    actor B as Pembimbing Lapangan
    participant T as Tugas & Logbook Controller
    participant P as Penempatan Model
    participant PDF as DomPDF Generator

    B->>T: Buat Tugas Mandiri / Ambil dari TugasTemplate
    M->>T: Mengunggah Progres Pengerjaan Tugas & Berkas
    B->>T: Review & Setujui Tugas (Status: SELESAI)
    M->>T: Mengisi Logbook Harian (Kegiatan, Capaian, Lampiran)
    B->>T: Review & Verifikasi Logbook Mingguan
    Note over B,M: Periode Magang Berakhir
    B->>P: Klik Tombol "Selesaikan Bimbingan" (1-Klik Completion)
    P->>P: Update status Penempatan menjadi SELESAI
    P->>PDF: Opsional cetak Surat Keterangan / Laporan Rekapitulasi
```

---

## 2. Entity-Relationship Diagram (ERD) Domain Inti

```mermaid
erDiagram
    USERS ||--o{ PENDAFTARAN : "memiliki"
    USERS ||--o| PESERTA_PROFIL : "memiliki profil"
    USERS ||--o{ PENEMPATAN : "peserta / pembimbing"
    USERS ||--o{ AUDIT_LOG : "mencatat aktivitas"
    USERS ||--o{ PESERTA_KELOMPOK : "anggota kelompok"
    
    JURUSAN ||--o{ KUOTA : "alokasi kuota"
    JURUSAN ||--o{ PENDAFTARAN : "jurusan peserta"
    BULAN_MAGANG ||--o{ KUOTA : "periode alokasi"
    BULAN_MAGANG ||--o{ PENDAFTARAN : "periode dipilih"
    
    BIDANG ||--o{ PENEMPATAN : "unit kerja magang"
    BIDANG ||--o{ TUGAS_TEMPLATE : "template tugas bidang"
    
    PENDAFTARAN ||--o{ DOKUMEN : "melampirkan berkas"
    PENDAFTARAN ||--o| PENEMPATAN : "diterima menjadi"
    
    PENEMPATAN ||--o{ TUGAS : "menerima tugas"
    PENEMPATAN ||--o{ LOGBOOK : "mengisi logbook"
    
    TUGAS ||--o{ PROGRES_LOG : "riwayat progres"
    
    USERS {
        bigint id PK
        string name
        string email UK
        string role "peserta|pembimbing|admin|superadmin"
        boolean is_active
        boolean must_change_password
    }

    PESERTA_PROFIL {
        bigint id PK
        bigint user_id FK
        string nim_nis
        string institusi
        string no_hp
        string kontak_darurat
    }

    BULAN_MAGANG {
        bigint id PK
        string nama_bulan
        int tahun
        boolean is_aktif
        date batas_pendaftaran
    }

    KUOTA {
        bigint id PK
        bigint bulan_magang_id FK
        bigint jurusan_id FK
        int jumlah_kuota
        int terisi
    }

    PENDAFTARAN {
        bigint id PK
        bigint user_id FK
        bigint bulan_magang_id FK
        bigint jurusan_id FK
        string status "draft|menunggu_verifikasi|diterima|ditolak"
        datetime tanggal_pengajuan
    }

    PENEMPATAN {
        bigint id PK
        bigint pendaftaran_id FK
        bigint peserta_id FK
        bigint pembimbing_id FK
        bigint bidang_id FK
        string status "aktif|selesai|dibatalkan"
        date tanggal_mulai
        date tanggal_selesai
    }

    TUGAS {
        bigint id PK
        bigint penempatan_id FK
        string judul
        text deskripsi
        date tenggat_waktu
        string status "pending|dikerjakan|selesai"
    }

    LOGBOOK {
        bigint id PK
        bigint penempatan_id FK
        date tanggal
        int minggu_ke
        text kegiatan
        string status_verifikasi "draft|disetujui|catatan"
    }

    AUDIT_LOG {
        bigint id PK
        bigint user_id FK
        string event
        string ip_address
        text detail
        timestamp created_at
    }
```

---

## 3. Strategi Penyimpanan Data & Session State

1. **Session Driver (`database`)**:
   - Seluruh sesi pengguna disimpan dalam tabel framework `sessions` MySQL untuk ketahanan serverless/multi-worker dan memungkinkan fitur pembatalan sesi simultan bila diperlukan.
2. **Cache Store (`database`)**:
   - Menggunakan tabel framework `cache` untuk agregasi data kuota berkala dan metrik dashboard harian guna meminimalkan query repetitif ke tabel transaksi utama.
3. **Penyimpanan Berkas Privat (`storage/app/berkas`)**:
   - Berkas fisik dipisahkan sepenuhnya dari direktori public web. Unduhan divalidasi melalui rute URL bertanda tangan relatif (`signed:relative`) dengan verifikasi otorisasi Form Request / Policy (IDOR mitigation).
