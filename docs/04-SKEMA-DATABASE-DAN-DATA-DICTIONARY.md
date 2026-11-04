# Kamus Data, Skema Database & Seeder MagKom

Dokumen ini memuat spesifikasi menyeluruh untuk 32 tabel domain dan operasional database MagKom (Magang Kominfo) menggunakan engine **InnoDB** pada **MySQL 8.0.16+** / **MariaDB 10.5+**.

---

## 1. Diagram Relasi Entitas (ERD)

```mermaid
erDiagram
    users ||--o| peserta_profil : "memiliki profil"
    jurusan ||--o{ peserta_profil : "jurusan peserta"
    jurusan ||--o{ kuota : "kuota jurusan"
    bulan_magang ||--o{ kuota : "kuota bulanan"
    users ||--o{ pendaftaran : "mengajukan"
    bulan_magang ||--o{ pendaftaran : "dipilih pada"
    kuota ||--o{ pendaftaran : "alokasi kuota"
    jenis_dokumen ||--o{ dokumen : "kategori berkas"
    pendaftaran ||--o{ dokumen : "unggah dokumen"
    pendaftaran ||--o| penempatan : "realisasi penempatan"
    users ||--o{ penempatan : "peserta magang"
    users ||--o{ penempatan : "pembimbing"
    kuota ||--o{ penempatan : "jalur umum"
    slot_khusus ||--o{ penempatan : "jalur khusus"
    bidang ||--o{ penempatan : "bidang kerja"
    penempatan ||--o{ tugas : "diberikan tugas"
    tugas ||--o{ progres_log : "laporan progres"
    penempatan ||--o{ logbook : "catatan harian/mingguan"
    penempatan ||--o| penilaian : "lembar nilai"
    penilaian ||--o{ penilaian_detail : "rincian kriteria"
    rubrik ||--o{ penilaian_detail : "rubrik acuan"
    penilaian ||--o{ penilaian_riwayat : "riwayat perubahan"
    users ||--o{ audit_log : "pelaku aktivitas"
    users ||--o{ kelompok_magang : "ketua kelompok"
    kelompok_magang ||--o{ kelompok_anggota : "beranggotakan"
    kelompok_magang ||--o{ pendaftaran : "kelompok pendaftar"
    penempatan ||--o{ agenda_bimbingan : "sesi bimbingan"
    users ||--o{ agenda_bimbingan : "pembimbing agenda"
    penempatan ||--o{ dokumen_verifikasi : "dokumen sah terbit"
    penempatan ||--o| survei_kepuasan : "survei pasca magang"
    users ||--o{ tugas_template : "template tugas pembimbing"
    users ||--o{ realtime_events : "notifikasi realtime SSE"
    bidang ||--o{ bidang_jurusan : "pemetaan bidang"
    jurusan ||--o{ bidang_jurusan : "pemetaan jurusan"
    modul_dinamis ||--o{ modul_dinamis_field : "struktur kolom"
    modul_dinamis ||--o{ modul_dinamis_data : "baris data modul"
    users ||--o{ route_metrics : "telemetri request"
```

---

## 2. Kamus Data 32 Tabel Domain & Operasional

### 1. `users`
Menyimpan akun seluruh pengguna sistem (SuperAdmin, Admin, Pembimbing, Peserta).

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `nama` | VARCHAR(100) | Tidak | - | Nama lengkap pengguna |
| `email` | VARCHAR(255) | Tidak | - | Alamat email (**UNIQUE**) |
| `no_telp` | VARCHAR(20) | Ya | NULL | Nomor telepon pengguna |
| `email_verified_at` | TIMESTAMP | Ya | NULL | Waktu verifikasi email |
| `password` | VARCHAR(255) | Ya | NULL | Bcrypt hash (null bila via OAuth) |
| `role` | VARCHAR(20) | Tidak | - | `superadmin`, `admin`, `pembimbing`, `peserta` |
| `provider` | VARCHAR(20) | Ya | NULL | OAuth provider (`google`, `facebook`) |
| `provider_id` | VARCHAR(191) | Ya | NULL | ID unik dari provider OAuth |
| `bidang_id` | BIGINT UNSIGNED | Ya | NULL | FK ke `bidang.id` (staf) |
| `status_aktif` | BOOLEAN | Tidak | TRUE | Status aktif akun (flag) |
| `must_change_password`| BOOLEAN | Tidak | FALSE | Penanda wajib ubah password saat login |
| `dibuat_oleh` | BIGINT UNSIGNED | Ya | NULL | FK ke `users.id` (pembuat akun) |
| `remember_token` | VARCHAR(100) | Ya | NULL | Token sesi "ingat saya" |
| `foto` | VARCHAR(255) | Ya | NULL | Path berkas foto profil pengguna |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Waktu pembuatan / update |

**Indeks**: `UNIQUE(email)`, `INDEX(role, status_aktif)`, `UNIQUE(provider, provider_id)`.

---

### 2. `peserta_profil`
Data biodata pelengkap khusus pengguna dengan peran `peserta`.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `user_id` | BIGINT UNSIGNED | Tidak | - | FK ke `users.id` (**UNIQUE**) |
| `nim_nisn` | VARCHAR(20) | Tidak | - | Nomor Induk Siswa/Mahasiswa |
| `institusi` | VARCHAR(150) | Tidak | - | Nama Sekolah / Perguruan Tinggi |
| `jurusan_id` | BIGINT UNSIGNED | Tidak | - | FK ke `jurusan.id` |
| `jenjang` | VARCHAR(5) | Tidak | - | Enum: `smk`, `d3`, `s1` |
| `no_hp` | VARCHAR(20) | Tidak | - | Nomor WhatsApp / Telepon aktif |
| `kontak_darurat` | VARCHAR(20) | Ya | NULL | Nomor kontak darurat keluarga/wali |
| `foto` | VARCHAR(255) | Ya | NULL | Path pasfoto biodata peserta |
| `persetujuan_wali_at` | TIMESTAMP | Ya | NULL | Waktu persetujuan orang tua/wali (khusus SMK) |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

---

### 3. `jurusan`
Master data program keahlian / kejuruan peserta magang.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `nama_jurusan` | VARCHAR(100) | Tidak | - | Nama kejuruan (**UNIQUE**) |
| `kategori` | VARCHAR(10) | Tidak | - | `smk` atau `mahasiswa` |
| `aktif` | BOOLEAN | Tidak | TRUE | Flag status aktif |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

---

### 4. `bidang`
Master unit kerja internal di lingkungan Dinas Kominfo.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `nama_bidang` | VARCHAR(100) | Tidak | - | Nama unit kerja / bidang |
| `deskripsi` | TEXT | Ya | NULL | Uraian tugas fungsi |
| `aktif` | BOOLEAN | Tidak | TRUE | Flag status aktif |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

---

### 5. `bulan_magang`
Periode pembukaan magang (selalu tanggal 1 pada bulan terkait).

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `bulan_mulai` | DATE | Tidak | - | Tanggal 1 bulan magang (**UNIQUE**) |
| `nama_bulan` | VARCHAR(50) | Ya | NULL | Nama representasi bulan (misal: Januari 2027) |
| `tgl_buka` | DATE | Tidak | - | Tanggal mulai pendaftaran dibuka |
| `tgl_tutup` | DATE | Tidak | - | Tanggal penutupan pendaftaran |
| `override_status` | VARCHAR(10) | Ya | NULL | `tersedia`, `tutup`, `segera`, atau NULL |
| `aktif` | BOOLEAN | Tidak | TRUE | Flag status aktif |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

---

### 6. `kuota`
Alokasi penerimaan jalur umum per bulan per jurusan.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `bulan_magang_id` | BIGINT UNSIGNED | Tidak | - | FK ke `bulan_magang.id` |
| `jurusan_id` | BIGINT UNSIGNED | Tidak | - | FK ke `jurusan.id` |
| `jumlah` | SMALLINT UNSIGNED | Tidak | 0 | Total daya tampung kuota |
| `terisi` | SMALLINT UNSIGNED | Tidak | 0 | Jumlah kuota yang telah diterima |
| `aktif` | BOOLEAN | Tidak | TRUE | Flag status aktif |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Constraints**: `UNIQUE(bulan_magang_id, jurusan_id)`, `CHECK(terisi <= jumlah)`.

---

### 7. `slot_khusus`
Daya tampung penerimaan magang jalur khusus per bulan (internal SuperAdmin).

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `bulan_mulai` | DATE | Tidak | - | Tanggal 1 bulan magang (**UNIQUE**) |
| `jumlah` | SMALLINT UNSIGNED | Tidak | 3 | Total slot khusus |
| `terisi` | SMALLINT UNSIGNED | Tidak | 0 | Slot khusus yang telah terpakai |
| `aktif` | BOOLEAN | Tidak | TRUE | Flag status aktif |
| `dibuat_oleh` | BIGINT UNSIGNED | Tidak | - | FK ke `users.id` (SuperAdmin) |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Constraint**: `CHECK(terisi <= jumlah)`.

---

### 8. `jenis_dokumen`
Master jenis berkas persyaratan pendaftaran berdasarkan jenjang pendidikan.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `kode` | VARCHAR(40) | Tidak | - | Kode unik dokumen (`cv`, dsb.) |
| `nama` | VARCHAR(100) | Tidak | - | Nama berkas tampilan form |
| `jenjang` | VARCHAR(5) | Tidak | - | `smk`, `d3`, `s1` |
| `wajib` | BOOLEAN | Tidak | TRUE | Apakah berkas mandatory |
| `aktif` | BOOLEAN | Tidak | TRUE | Flag status aktif |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Constraint**: `UNIQUE(kode, jenjang)`.

---

### 9. `pendaftaran`
Pengajuan pendaftaran calon peserta magang.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `user_id` | BIGINT UNSIGNED | Tidak | - | FK ke `users.id` (calon peserta) |
| `bulan_magang_id` | BIGINT UNSIGNED | Tidak | - | FK ke `bulan_magang.id` |
| `kuota_id` | BIGINT UNSIGNED | Tidak | - | FK ke `kuota.id` |
| `tipe` | VARCHAR(20) | Tidak | 'mandiri' | Tipe pendaftaran: `mandiri` atau `kelompok` |
| `kelompok_magang_id` | BIGINT UNSIGNED | Ya | NULL | FK ke `kelompok_magang.id` (jika pendaftaran kelompok) |
| `durasi_bulan` | TINYINT UNSIGNED | Tidak | - | Nilai 1 sampai 6 (**CHECK**) |
| `status` | VARCHAR(25) | Tidak | `berkas_belum_lengkap` | Status alur seleksi |
| `verifikator_id` | BIGINT UNSIGNED | Ya | NULL | FK ke `users.id` (Admin pemutus) |
| `catatan` | TEXT | Ya | NULL | Catatan revisi atau alasan penolakan |
| `diajukan_at` | TIMESTAMP | Ya | NULL | Waktu berkas lengkap diajukan |
| `diputuskan_at` | TIMESTAMP | Ya | NULL | Waktu keputusan verifikasi |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Indeks**: `UNIQUE(user_id, bulan_magang_id)`, `INDEX(bulan_magang_id, status)`, `INDEX(status, diajukan_at)`, `INDEX(kelompok_magang_id)`.  
**Constraint**: `CHECK(durasi_bulan BETWEEN 1 AND 6)`.

---

### 10. `dokumen`
Berkas fisik pendaftaran yang tersimpan di disk privat server.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `pendaftaran_id` | BIGINT UNSIGNED | Tidak | - | FK ke `pendaftaran.id` |
| `jenis_dokumen_id`| BIGINT UNSIGNED | Tidak | - | FK ke `jenis_dokumen.id` |
| `path` | VARCHAR(255) | Tidak | - | Lokasi simpan file pada private disk |
| `nama_asli` | VARCHAR(255) | Tidak | - | Nama berkas asli saat diunggah |
| `mime` | VARCHAR(100) | Tidak | - | MIME type hasil inspeksi magic bytes |
| `ukuran` | INT UNSIGNED | Tidak | - | Ukuran berkas dalam bytes |
| `hash` | CHAR(64) | Tidak | - | SHA-256 checksum integritas berkas |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Constraint**: `UNIQUE(pendaftaran_id, jenis_dokumen_id)`.

---

### 11. `penempatan`
Catatan penugasan resmi peserta magang di instansi.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `pendaftaran_id` | BIGINT UNSIGNED | Ya | NULL | FK ke `pendaftaran.id` (**UNIQUE** jika terisi) |
| `peserta_id` | BIGINT UNSIGNED | Tidak | - | FK ke `users.id` (peserta) |
| `kuota_id` | BIGINT UNSIGNED | Ya | NULL | FK ke `kuota.id` (jalur umum) |
| `slot_khusus_id` | BIGINT UNSIGNED | Ya | NULL | FK ke `slot_khusus.id` (jalur khusus) |
| `bidang_id` | BIGINT UNSIGNED | Ya | NULL | FK ke `bidang.id` |
| `pembimbing_id` | BIGINT UNSIGNED | Tidak | - | FK ke `users.id` (pembimbing) |
| `jalur` | VARCHAR(10) | Tidak | - | `umum` atau `khusus` |
| `sumber` | VARCHAR(10) | Tidak | - | `online` atau `manual` |
| `dibuat_oleh` | BIGINT UNSIGNED | Ya | NULL | FK ke `users.id` (Admin pembuat) |
| `tgl_mulai` | DATE | Tidak | - | Tanggal awal magang (tgl 1) |
| `tgl_selesai` | DATE | Tidak | - | Tanggal akhir masa magang |
| `tgl_ditetapkan_selesai` | DATE | Ya | NULL | Tanggal saat pembimbing klik selesai |
| `tgl_nonaktif` | DATE | Ya | NULL | Tanggal masa tenggang berakhir |
| `status` | VARCHAR(20) | Tidak | `aktif` | `aktif`, `selesai`, `mengundurkan_diri`, `nonaktif` |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Constraints**:
- Tepat satu dari `kuota_id` atau `slot_khusus_id` terisi: `CHECK((kuota_id IS NULL) <> (slot_khusus_id IS NULL))`
- Integritas relasi jalur: `CHECK((jalur = 'umum' AND kuota_id IS NOT NULL) OR (jalur = 'khusus' AND slot_khusus_id IS NOT NULL))`

---

### 12. `tugas`
Instruksi penugasan kerja yang diberikan pembimbing.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `penempatan_id` | BIGINT UNSIGNED | Tidak | - | FK ke `penempatan.id` |
| `judul` | VARCHAR(150) | Tidak | - | Judul tugas |
| `deskripsi` | TEXT | Ya | NULL | Deskripsi dan instruksi pengerjaan |
| `lampiran` | VARCHAR(255) | Ya | NULL | Berkas lampiran acuan tugas dari pembimbing |
| `link` | VARCHAR(255) | Ya | NULL | Tautan URL referensi tugas |
| `kategori` | VARCHAR(50) | Tidak | - | Kategori tugas (free text) |
| `kompleksitas` | VARCHAR(10) | Tidak | - | `rendah`, `sedang`, `tinggi` |
| `deadline` | DATE | Tidak | - | Batas akhir pengumpulan |
| `status` | VARCHAR(10) | Tidak | `belum` | `belum`, `proses`, `selesai` |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

---

### 13. `progres_log`
Laporan progres pekerjaan yang diunggah peserta.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `tugas_id` | BIGINT UNSIGNED | Tidak | - | FK ke `tugas.id` |
| `catatan` | TEXT | Tidak | - | Penjelasan progres pekerjaan |
| `file` | VARCHAR(255) | Ya | NULL | Berkas lampiran di disk privat |
| `link` | VARCHAR(255) | Ya | NULL | Tautan hasil kerja (e.g. GitHub, Figma, Google Drive) |
| `tgl_lapor` | DATE | Tidak | - | Tanggal pengiriman laporan |
| `status_review` | VARCHAR(15) | Tidak | `menunggu` | `menunggu`, `diterima`, `perlu_revisi` |
| `feedback` | TEXT | Ya | NULL | Umpan balik dari pembimbing |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

---

### 14. `logbook`
Catatan aktivitas mingguan/harian peserta selama masa magang aktif.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `penempatan_id` | BIGINT UNSIGNED | Tidak | - | FK ke `penempatan.id` |
| `minggu_ke` | TINYINT UNSIGNED | Tidak | 1 | Nomor minggu penempatan pelaksanaan magang |
| `tanggal` | DATE | Tidak | - | Tanggal kegiatan |
| `kegiatan` | TEXT | Tidak | - | Rincian aktivitas yang dilakukan |
| `file_laporan` | VARCHAR(255) | Ya | NULL | Berkas PDF lampiran (opsional R2) |
| `link_laporan` | VARCHAR(500) | Ya | NULL | Tautan eksternal https (opsional R2) |
| `status` | VARCHAR(15) | Tidak | `terkirim` | `terkirim`, `perlu_revisi` |
| `catatan_pembimbing`| TEXT | Ya | NULL | Catatan evaluasi pembimbing |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Constraint**: `UNIQUE(penempatan_id, tanggal)`.

---

### 15. `rubrik`
Master instrumen kriteria penilaian magang beserta bobotnya.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `kriteria` | VARCHAR(100) | Tidak | - | Nama kriteria penilaian |
| `bobot` | TINYINT UNSIGNED | Tidak | - | Persentase bobot (1-100, total = 100) |
| `aktif` | BOOLEAN | Tidak | TRUE | Flag status aktif |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

---

### 16. `penilaian`
Lembar evaluasi nilai akhir peserta magang.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `penempatan_id` | BIGINT UNSIGNED | Tidak | - | FK ke `penempatan.id` (**UNIQUE**) |
| `pembimbing_id` | BIGINT UNSIGNED | Tidak | - | FK ke `users.id` (penilai) |
| `nilai_akhir` | DECIMAL(5,2) | Ya | NULL | Nilai terbobot (0.00 - 100.00) |
| `predikat` | CHAR(1) | Ya | NULL | Mutu huruf (`A`, `B`, `C`, `D`, `E`) |
| `catatan` | TEXT | Ya | NULL | Evaluasi akhir pembimbing |
| `status` | VARCHAR(10) | Tidak | `draft` | `draft` atau `final` |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

---

### 17. `penilaian_detail`
Rincian skor per kriteria rubrik beserta snapshot bobot saat penilaian dibuat.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `penilaian_id` | BIGINT UNSIGNED | Tidak | - | FK ke `penilaian.id` |
| `rubrik_id` | BIGINT UNSIGNED | Tidak | - | FK ke `rubrik.id` |
| `skor` | TINYINT UNSIGNED | Tidak | - | Skor 0 sampai 100 (**CHECK**) |
| `bobot_snapshot`| TINYINT UNSIGNED | Tidak | - | Snapshot persentase bobot acuan |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Constraints**: `UNIQUE(penilaian_id, rubrik_id)`, `CHECK(skor BETWEEN 0 AND 100)`.

---

### 18. `penilaian_riwayat`
Rekam jejak perubahan nilai bila pembimbing mengubah nilai final yang telah tersimpan.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `penilaian_id` | BIGINT UNSIGNED | Tidak | - | FK ke `penilaian.id` |
| `diubah_oleh` | BIGINT UNSIGNED | Tidak | - | FK ke `users.id` (Pembimbing) |
| `nilai_lama` | JSON | Tidak | - | Snapshot data nilai sebelum diedit |
| `nilai_baru` | JSON | Tidak | - | Snapshot data nilai setelah diedit |
| `alasan` | TEXT | Tidak | - | Alasan perubahan nilai (min 10 karakter) |
| `waktu` | TIMESTAMP | Tidak | CURRENT_TIMESTAMP | Waktu terjadinya perubahan |

---

### 19. `audit_log`
Tabel penampung jejak audit komprehensif berstatus append-only.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `request_id` | VARCHAR(36) | Tidak | - | UUID penanda unik request |
| `user_id` | BIGINT UNSIGNED | Ya | NULL | FK ke `users.id` (pelaku) |
| `tipe` | VARCHAR(15) | Tidak | - | `auth`, `aksi`, `service`, `akses_data` |
| `channel` | VARCHAR(20) | Tidak | 'web' | Kanal audit log (`web`, `api`, `console`, `system`) |
| `aksi` | VARCHAR(100) | Tidak | - | Nama tindakan bisnis |
| `entitas` | VARCHAR(60) | Ya | NULL | Nama model / tabel terkait |
| `entitas_id` | BIGINT UNSIGNED | Ya | NULL | ID entitas |
| `nilai_lama` | JSON | Ya | NULL | Data sebelum mutasi |
| `nilai_baru` | JSON | Ya | NULL | Data setelah mutasi |
| `detail` | JSON | Ya | NULL | Argumen service atau filter & total row |
| `metode` | VARCHAR(10) | Ya | NULL | HTTP Method (`GET`, `POST`, dll.) |
| `rute` | VARCHAR(150) | Ya | NULL | Route name / path |
| `status_http` | SMALLINT UNSIGNED | Ya | NULL | HTTP Response code |
| `durasi_ms` | INT UNSIGNED | Ya | NULL | Durasi eksekusi dalam milidetik |
| `ip` | VARCHAR(45) | Tidak | - | IP Address klien (IPv4 / IPv6) |
| `user_agent` | VARCHAR(255) | Ya | NULL | User Agent browser klien |
| `waktu` | TIMESTAMP | Tidak | CURRENT_TIMESTAMP | Timestamp pencatatan |

**Indeks**: `INDEX(user_id, waktu)`, `INDEX(entitas, entitas_id)`, `INDEX(request_id)`, `INDEX(tipe, waktu)`, `INDEX(channel)`.

---

### 20. `pengaturan`
Tabel pasangan key-value untuk seluruh parameter konfigurasi aplikasi dinamis.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `kunci` | VARCHAR(100) | Tidak | - | Key konfigurasi unik (**UNIQUE**) |
| `nilai` | TEXT | Ya | NULL | Nilai konfigurasi (string/JSON) |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

---

### 21. `agenda_bimbingan`
Jadwal dan notulensi sesi bimbingan tatap muka atau daring antara pembimbing dan peserta.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `penempatan_id` | BIGINT UNSIGNED | Tidak | - | FK ke `penempatan.id` (cascade delete) |
| `pembimbing_id` | BIGINT UNSIGNED | Tidak | - | FK ke `users.id` (pembimbing, cascade delete) |
| `judul_kegiatan` | VARCHAR(150) | Tidak | - | Topik / materi sesi bimbingan |
| `tipe` | VARCHAR(20) | Tidak | 'tatap_muka' | `tatap_muka` atau `daring` |
| `tgl_bimbingan` | DATE | Tidak | - | Tanggal pelaksanaan bimbingan |
| `jam_mulai` | VARCHAR(10) | Tidak | '09:00' | Waktu pelaksanaan (format HH:mm) |
| `lokasi_ruang` | VARCHAR(150) | Ya | NULL | Ruang kerja kantor atau link meeting virtual |
| `notulensi_arahan` | TEXT | Ya | NULL | Catatan, evaluasi, dan arahan pembimbing |
| `status` | VARCHAR(20) | Tidak | 'terjadwal' | `terjadwal`, `selesai`, `dibatalkan` |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Indeks**: `INDEX(pembimbing_id, status)`, `INDEX(penempatan_id, status)`, `INDEX(tgl_bimbingan)`.

---

### 22. `dokumen_verifikasi`
Pencatatan kode verifikasi keabsahan digital dokumen resmi (Surat Penerimaan, Lembar Nilai, Sertifikat).

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `kode_unik` | VARCHAR(64) | Tidak | - | Hash kode unik acak (**UNIQUE**) untuk QR verification |
| `tipe_dokumen` | VARCHAR(32) | Tidak | - | `surat_diterima`, `lembar_nilai`, `sertifikat` |
| `penempatan_id` | BIGINT UNSIGNED | Tidak | - | FK ke `penempatan.id` (cascade delete) |
| `nomor_dokumen` | VARCHAR(100) | Tidak | - | Nomor surat resmi instansi |
| `tanggal_terbit` | DATE | Tidak | - | Tanggal penerbitan dokumen |
| `status_keabsahan` | VARCHAR(20) | Tidak | 'sah' | `sah`, `dicabut`, `kedaluwarsa` |
| `metadata` | JSON | Ya | NULL | Metadata penandatangan, instansi, dan payload |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Indeks**: `UNIQUE(kode_unik)`, `INDEX(kode_unik, status_keabsahan)`, `INDEX(penempatan_id)`.

---

### 23. `survei_kepuasan`
Evaluasi kepuasan peserta pasca pelaksanaan magang (Indeks Kepuasan & Net Promoter Score).

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `penempatan_id` | BIGINT UNSIGNED | Tidak | - | FK ke `penempatan.id` (**UNIQUE**, cascade delete) |
| `skor_mentoring` | UNSIGNED TINYINT | Tidak | - | Skala 1 - 5 (kualitas bimbingan) |
| `skor_tugas` | UNSIGNED TINYINT | Tidak | - | Skala 1 - 5 (kesesuaian penugasan) |
| `skor_fasilitas` | UNSIGNED TINYINT | Tidak | - | Skala 1 - 5 (dukungan sarana kantor) |
| `skor_nps` | UNSIGNED TINYINT | Tidak | - | Skala 0 - 10 (Net Promoter Score) |
| `umpan_balik` | TEXT | Ya | NULL | Uraian kesan dan kritik konstruktif |
| `saran` | TEXT | Ya | NULL | Usulan perbaikan operasional magang |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Indeks**: `UNIQUE(penempatan_id)`, `INDEX(skor_nps)`.

---

### 24. `tugas_template`
Pustaka template penugasan siap pakai yang dapat digunakan pembimbing untuk delegasi tugas cepat.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `pembimbing_id` | BIGINT UNSIGNED | Ya | NULL | FK ke `users.id` (pemilik template, null jika global) |
| `judul` | VARCHAR(150) | Tidak | - | Judul rancangan tugas |
| `deskripsi` | TEXT | Ya | NULL | Petunjuk teknis pengerjaan |
| `kategori` | VARCHAR(50) | Tidak | 'Umum' | Kategori tugas |
| `kompleksitas` | VARCHAR(10) | Tidak | 'sedang' | `rendah`, `sedang`, `tinggi` |
| `link_referensi` | VARCHAR(255) | Ya | NULL | Tautan dokumentasi/panduan |
| `is_global` | BOOLEAN | Tidak | FALSE | Flag template lintas pembimbing |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Indeks**: `INDEX(pembimbing_id, kategori)`, `INDEX(is_global)`.

---

### 25. `realtime_events`
Penampung antrean notifikasi instan Server-Sent Events (SSE) `/realtime/stream` (RFC 8895).

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `user_id` | BIGINT UNSIGNED | Ya | NULL | FK ke `users.id` (null jika siaran broadcast role) |
| `role` | VARCHAR(30) | Ya | NULL | Role target siaran (`admin`, `pembimbing`, dll.) |
| `event_type` | VARCHAR(50) | Tidak | - | Jenis event (e.g. `pendaftaran_baru`, `logbook_baru`) |
| `payload` | JSON | Tidak | - | Muatan data notifikasi JSON tersanitasi |
| `read_at` | TIMESTAMP | Ya | NULL | Waktu pengguna membaca/membuka notifikasi |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Indeks**: `INDEX(role)`, `INDEX(event_type)`, `INDEX(read_at)`, `INDEX(user_id, created_at)`, `INDEX(role, created_at)`.

---

### 26. `bidang_jurusan`
Tabel relasi many-to-many antara Unit Kerja (Bidang) di Kominfo dan Jurusan yang relevan.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `bidang_id` | BIGINT UNSIGNED | Tidak | - | FK ke `bidang.id` (cascade delete) |
| `jurusan_id` | BIGINT UNSIGNED | Tidak | - | FK ke `jurusan.id` (cascade delete) |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Constraint**: `UNIQUE(bidang_id, jurusan_id)`.

---

### 27. `modul_dinamis`
Metadata konfigurasi modul builder dinamis low-code SuperAdmin.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `nama_modul` | VARCHAR(100) | Tidak | - | Nama modul dinamis |
| `slug` | VARCHAR(100) | Tidak | - | Identifier unik URL (**UNIQUE**) |
| `tipe_sumber` | VARCHAR(20) | Tidak | 'dynamic' | `dynamic` (JSON EAV) atau `physical_table` |
| `nama_tabel_fisik` | VARCHAR(64) | Ya | NULL | Nama tabel fisik jika physical table |
| `primary_key` | VARCHAR(64) | Tidak | 'id' | Nama primary key tabel fisik |
| `is_manual_pk` | BOOLEAN | Tidak | FALSE | Flag apakah primary key diisi manual |
| `deskripsi` | TEXT | Ya | NULL | Keterangan tujuan modul |
| `ikon` | VARCHAR(50) | Tidak | 'box' | Identifier icon Lucide / SVG |
| `target_roles` | JSON | Tidak | - | Array peran yang diizinkan mengakses |
| `izin_aksi` | JSON | Ya | NULL | Granular RBAC (`create`, `edit`, `delete`, dll.) |
| `log_aksi` | JSON | Ya | NULL | Konfigurasi pencatatan audit log per aksi modul |
| `status_aktif` | BOOLEAN | Tidak | TRUE | Status aktif publikasi modul |
| `is_public_read` | BOOLEAN | Tidak | FALSE | Flag apakah modul dapat dibaca publik tanpa login |
| `scope_mode` | VARCHAR(20) | Tidak | 'none' | `none`, `bidang_scope`, `creator_scope` |
| `urutan` | UNSIGNED INT | Tidak | 0 | Urutan render pada sidebar navigasi |
| `dibuat_oleh` | BIGINT UNSIGNED | Ya | NULL | FK ke `users.id` (SuperAdmin pembuat) |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Indeks**: `UNIQUE(slug)`, `INDEX(status_aktif)`.

---

### 28. `modul_dinamis_field`
Definisi atribut, kolom form, tipe input, dan validasi untuk modul dinamis.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `modul_id` | BIGINT UNSIGNED | Tidak | - | FK ke `modul_dinamis.id` (cascade delete) |
| `nama_kolom` | VARCHAR(60) | Tidak | - | Nama field identifier snake_case |
| `label` | VARCHAR(100) | Tidak | - | Label antarmuka form |
| `tipe_data` | VARCHAR(30) | Tidak | - | `text`, `number`, `textarea`, `select`, `multiselect`, `date`, `datetime`, `file`, `boolean`, `relation` |
| `opsi_pilihan` | JSON | Ya | NULL | Array opsi jika tipe `select` / `multiselect` |
| `relasi_tabel` | VARCHAR(60) | Ya | NULL | Tabel target referensi relasi |
| `relasi_kolom_tampil`| VARCHAR(60) | Ya | NULL | Kolom yang ditampilkan sebagai label relasi |
| `wajib` | BOOLEAN | Tidak | FALSE | Flag input mandatory |
| `tampil_di_tabel` | BOOLEAN | Tidak | TRUE | Ditampilkan pada grid tabel daftar |
| `dapat_dicari` | BOOLEAN | Tidak | TRUE | Diikutsertakan dalam query pencarian |
| `dapat_difilter` | BOOLEAN | Tidak | FALSE | Tersedia pada dropdown filter |
| `placeholder` | VARCHAR(150) | Ya | NULL | Teks placeholder input |
| `bantuan_teks` | VARCHAR(255) | Ya | NULL | Teks instruksi kecil di bawah field |
| `urutan` | UNSIGNED INT | Tidak | 0 | Urutan field pada form |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Indeks**: `UNIQUE(modul_id, nama_kolom)`, `INDEX(modul_id, urutan)`.

---

### 29. `modul_dinamis_data`
Penyimpanan baris data untuk modul bertipe dynamic (hybrid JSON EAV) dengan soft deletes.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `modul_id` | BIGINT UNSIGNED | Tidak | - | FK ke `modul_dinamis.id` (cascade delete) |
| `data` | JSON | Tidak | - | Key-value data baris tersanitasi |
| `files_metadata` | JSON | Ya | NULL | Metadata berkas lampiran yang diunggah |
| `created_by` | BIGINT UNSIGNED | Ya | NULL | FK ke `users.id` (pembuat entri) |
| `updated_by` | BIGINT UNSIGNED | Ya | NULL | FK ke `users.id` (pengubah entri) |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |
| `deleted_at` | TIMESTAMP | Ya | NULL | Soft delete timestamp |

**Indeks**: `INDEX(modul_id, created_at)`.

---

### 30. `route_metrics`
Telemetri performa rute HTTP server, latensi eksekusi, dan konsumsi memori.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `method` | VARCHAR(10) | Tidak | - | HTTP Method (`GET`, `POST`, dll.) |
| `route_name` | VARCHAR(150) | Ya | NULL | Nama rute terdaftar di Laravel |
| `uri` | VARCHAR(255) | Tidak | - | Request URI yang diakses |
| `status_code` | UNSIGNED SMALLINT | Tidak | - | HTTP Response status code (e.g. 200, 404, 500) |
| `duration_ms` | DECIMAL(8,2) | Tidak | - | Durasi eksekusi request dalam milidetik |
| `memory_mb` | DECIMAL(8,2) | Ya | NULL | Penggunaan memori puncak dalam Megabyte |
| `user_id` | BIGINT UNSIGNED | Ya | NULL | FK ke `users.id` (jika terautentikasi) |
| `ip` | VARCHAR(45) | Ya | NULL | IP address klien |
| `created_at` | TIMESTAMP | Tidak | CURRENT_TIMESTAMP | Waktu pencatatan telemetri |

**Indeks**: `INDEX(created_at)`, `INDEX(route_name, created_at)`, `INDEX(status_code, created_at)`.

---

### 31. `kelompok_magang`
Pengelompokan pendaftar jalur rombongan/kelompok (maksimal 4 orang) yang dipimpin oleh seorang ketua.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `ketua_user_id` | BIGINT UNSIGNED | Tidak | - | FK ke `users.id` (ketua pendaftar, cascade delete) |
| `nama_kelompok` | VARCHAR(150) | Tidak | - | Nama tim / instansi kelompok magang |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Indeks**: `INDEX(ketua_user_id)`.

---

### 32. `kelompok_anggota`
Data mentee anggota kelompok magang (selain ketua) di bawah satu kelompok magang.

| Kolom | Tipe Data | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Tidak | Auto Inc | Primary Key |
| `kelompok_magang_id` | BIGINT UNSIGNED | Tidak | - | FK ke `kelompok_magang.id` (cascade delete) |
| `nama` | VARCHAR(255) | Tidak | - | Nama lengkap anggota |
| `nim_nisn` | VARCHAR(50) | Ya | NULL | NIM / NISN anggota |
| `no_hp` | VARCHAR(30) | Ya | NULL | Nomor telepon / WhatsApp anggota |
| `email` | VARCHAR(255) | Ya | NULL | Alamat email anggota |
| `created_at` / `updated_at` | TIMESTAMP | Ya | NULL | Timestamps |

**Indeks**: `INDEX(kelompok_magang_id)`.

---

## 3. Data Awal Seeder (Default Seeder)

### A. Master Jurusan (6 Record)
```sql
INSERT INTO jurusan (nama_jurusan, kategori, aktif, created_at, updated_at) VALUES
('Teknik Komputer dan Jaringan (TKJ)', 'smk', 1, NOW(), NOW()),
('Rekayasa Perangkat Lunak (RPL)', 'smk', 1, NOW(), NOW()),
('Sistem Informasi', 'mahasiswa', 1, NOW(), NOW()),
('Manajemen Informatika', 'mahasiswa', 1, NOW(), NOW()),
('Informatika', 'mahasiswa', 1, NOW(), NOW()),
('Teknik Komputer', 'mahasiswa', 1, NOW(), NOW());
```

### B. Master Jenis Dokumen (9 Record)
```sql
INSERT INTO jenis_dokumen (kode, nama, jenjang, wajib, aktif, created_at, updated_at) VALUES
('surat_pengantar', 'Surat Pengantar Sekolah', 'smk', 1, 1, NOW(), NOW()),
('cv', 'Curriculum Vitae (CV)', 'smk', 1, 1, NOW(), NOW()),
('rapor', 'Salinan Rapor', 'smk', 1, 1, NOW(), NOW()),
('surat_pengantar', 'Surat Pengantar Kampus', 'd3', 1, 1, NOW(), NOW()),
('cv', 'Curriculum Vitae (CV)', 'd3', 1, 1, NOW(), NOW()),
('transkrip', 'Transkrip Nilai Akademik', 'd3', 1, 1, NOW(), NOW()),
('surat_pengantar', 'Surat Pengantar Kampus', 's1', 1, 1, NOW(), NOW()),
('cv', 'Curriculum Vitae (CV)', 's1', 1, 1, NOW(), NOW()),
('transkrip', 'Transkrip Nilai Akademik', 's1', 1, 1, NOW(), NOW());
```

### C. Master Rubrik Penilaian Awal (5 Record)
```sql
INSERT INTO rubrik (kriteria, bobot, aktif, created_at, updated_at) VALUES
('Kualitas hasil kerja', 30, 1, NOW(), NOW()),
('Ketepatan waktu dan penyelesaian tugas', 20, 1, NOW(), NOW()),
('Inisiatif dan kemampuan belajar', 20, 1, NOW(), NOW()),
('Komunikasi dan kerja sama', 15, 1, NOW(), NOW()),
('Etika, disiplin, dan tanggung jawab', 15, 1, NOW(), NOW());
```

### D. Konfigurasi Sistem Awal (8 Record)
```sql
INSERT INTO pengaturan (kunci, nilai, created_at, updated_at) VALUES
('masa_tenggang_bulan', '1', NOW(), NOW()),
('kuota_umum_maks_per_bulan', '12', NOW(), NOW()),
('slot_khusus_default', '3', NOW(), NOW()),
('maks_peserta_aktif_per_pembimbing', NULL, NOW(), NOW()),
('logbook_maks_mundur_hari', '3', NOW(), NOW()),
('berkas_maks_mb', '2', NOW(), NOW()),
('deadline_mendekati_hari', '3', NOW(), NOW()),
('predikat_batas', '{"A":85,"B":70,"C":55,"D":40}', NOW(), NOW());
```
