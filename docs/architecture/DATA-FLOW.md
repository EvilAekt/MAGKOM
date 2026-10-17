> Diperbarui: 2026-10-13 | commit: 1553e56 | mode: A

# Alur Data & Skema Database (DATA-FLOW.md)

Dokumen ini menjelaskan aliran data untuk proses bisnis inti, relasi antar entitas, serta Entity-Relationship Diagram (ERD) dari 25 model Eloquent dalam Portal MagKom.

---

## 1. Alur Data Fitur Utama

### A. Alur Pendaftaran & Seleksi Magang
```mermaid
sequenceDiagram
    autonumber
    actor P as Calon Peserta
    participant C as PesertaPendaftaranController
    participant S as PlacementService
    participant DB as Database (Pendaftaran, Kuota)
    participant SEC as DocumentStorageService

    P->>C: Mengisi Form & Mengunggah Berkas (KTP, KTM, Surat Pengantar)
    C->>SEC: Enkripsi nama & simpan berkas ke storage/app/berkas/
    C->>DB: Buat record Pendaftaran (status: DRAFT)
    P->>C: Klik "Ajukan Pendaftaran"
    C->>S: Evaluasi kuota jurusan di bulan terpilih
    alt Kuota Tersedia
        S->>DB: Ubah status menjadi SUBMITTED / MENUNGGU_VERIFIKASI
    else Kuota Penuh
        S->>DB: Masukkan ke status WAITING_LIST (FIFO antrean)
    end
    C-->>P: Redirect dengan Flash Message Sukses
```

### B. Alur Eksekusi Magang, Logbook & Penilaian
```mermaid
sequenceDiagram
    autonumber
    actor M as Peserta Magang
    actor B as Pembimbing Lapangan
    participant T as Tugas & Logbook Controller
    participant SC as ScoringService
    participant PDF as DomPDF Generator

    B->>T: Buat Tugas Mandiri / Ambil dari TugasTemplate
    M->>T: Mengunggah Progres Pengerjaan Tugas
    B->>T: Review & Tandai Tugas Selesai
    M->>T: Mengisi Logbook Harian (Kegiatan, Capaian, Lampiran)
    B->>T: Bulk Review / Verifikasi Logbook Mingguan
    Note over B,M: Magang Berakhir (Masa Selesai)
    B->>SC: Masukkan Nilai per Komponen Rubrik (Disiplin, Teknis, Inisiatif)
    SC->>SC: Hitung Rata-rata Terbobot & Tentukan Predikat (A/B/C)
    SC->>PDF: Generate Lembar Nilai & Terbitkan Sertifikat Digital
```

---

## 2. Entity-Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS ||--o{ PENDAFTARAN : "memiliki"
    USERS ||--o| PESERTA_PROFIL : "memiliki"
    USERS ||--o{ PENEMPATAN : "dibimbing oleh / ditempatkan"
    USERS ||--o{ AUDIT_LOG : "mencatat aktivitas"
    
    JURUSAN ||--o{ KUOTA : "alokasi kuota"
    JURUSAN ||--o{ PENDAFTARAN : "jurusan peserta"
    BULAN_MAGANG ||--o{ KUOTA : "periode alokasi"
    BULAN_MAGANG ||--o{ PENDAFTARAN : "periode dipilih"
    
    BIDANG ||--o{ PENEMPATAN : "unit kerja magang"
    
    PENDAFTARAN ||--o{ DOKUMEN : "melampirkan berkas"
    PENDAFTARAN ||--o| PENEMPATAN : "diterima menjadi"
    
    PENEMPATAN ||--o{ TUGAS : "menerima tugas"
    PENEMPATAN ||--o{ LOGBOOK : "mengisi logbook"
    PENEMPATAN ||--o| PENILAIAN : "dievaluasi"
    PENEMPATAN ||--o| SURVEI_KEPUASAN : "mengisi umpan balik"
    PENEMPATAN ||--o{ AGENDA_BIMBINGAN : "menghadiri mentoring"
    
    TUGAS ||--o{ PROGRES_LOG : "riwayat progres"
    PENILAIAN ||--o{ PENILAIAN_DETAIL : "rincian komponen rubrik"
    RUBRIK ||--o{ PENILAIAN_DETAIL : "standar acuan"
    
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
        boolean persetujuan_wali
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
        string status "draft|submitted|diterima|ditolak|waiting_list"
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

    PENILAIAN {
        bigint id PK
        bigint penempatan_id FK
        decimal nilai_akhir
        string predikat "Sangat Baik|Baik|Cukup"
        text catatan_evaluasi
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
   - Seluruh sesi pengguna disimpan dalam tabel `sessions` MySQL untuk ketahanan serverless/multi-worker dan memungkinkan fitur pembatalan sesi simultan (*single session concurrency* bila diperlukan).
2. **Cache Store (`database`)**:
   - Menggunakan tabel `cache` untuk agregasi data kuota berkala dan metrik dashboard harian guna meminimalkan query repetitif ke tabel transaksi utama.
3. **Penyimpanan Berkas Privat (`local storage/app/berkas`)**:
   - Berkas fisik dipisahkan sepenuhnya dari direktori public web. Unduhan divalidasi melalui rute URL bertanda tangan relatif (`signed:relative`) dengan pengecekan kepemilikan data (IDOR mitigation).
