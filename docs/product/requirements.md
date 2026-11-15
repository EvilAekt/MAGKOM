# Spesifikasi Kebutuhan dan Aturan Bisnis MAGKOM

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | User stories 4 aktor, aturan bisnis definitif (BR-01 s/d BR-19), kriteria penerimaan |
| Referensi Implementasi | `app/Actions/`, `app/Queries/`, `app/Services/`, `app/Models/`, `tests/Feature/` |

---

## 1. Pendahuluan

Dokumen ini mendefinisikan spesifikasi kebutuhan fungsional dan aturan bisnis resmi (*Single Source of Truth*) untuk sistem MAGKOM. Seluruh implementasi kode pada lapisan Controller, Action, Query, Service, Form Request, Model, dan Automated Tests wajib tunduk pada aturan yang tercantum di sini.

---

## 2. Kebutuhan Pengguna Berdasarkan Aktor (User Stories)

### 2.1 Peserta Magang (Siswa SMK & Mahasiswa D3/S1)
1. **US-PST-01 (Pemantauan Kuota)**: Sebagai calon peserta, saya ingin melihat ketersediaan sisa kuota jurusan per bulan secara transparan di portal publik, sehingga saya dapat menentukan jadwal pengajuan magang yang tepat.
2. **US-PST-02 (Registrasi Akun)**: Sebagai calon peserta, saya ingin mendaftarkan akun menggunakan email aktif atau akun Google, dan menerima email verifikasi sebelum dapat mengajukan permohonan.
3. **US-PST-03 (Pendaftaran Mandiri & Kelompok)**: Sebagai calon peserta, saya ingin dapat mendaftar secara individu atau mendaftarkan kelompok (ketua dan maksimal 3 anggota tambahan), sehingga rombongan sekolah atau kampus saya dapat mendaftar bersama.
4. **US-PST-04 (Unggah Berkas Persyaratan)**: Sebagai pendaftar, saya ingin mengunggah berkas persyaratan sesuai jenjang pendidikan saya (KTP, KTM/Kartu Pelajar, Surat Pengantar Dinas/Sekolah, Proposal Magang) dengan batasan ukuran berkas yang jelas.
5. **US-PST-05 (Pemantauan Status & Perbaikan)**: Sebagai pendaftar, saya ingin memantau perkembangan seleksi berkas saya dan menerima catatan perbaikan jika berkas dinyatakan perlu direvisi oleh admin verifikator.
6. **US-PST-06 (Pelaporan Logbook Harian)**: Sebagai peserta aktif, saya ingin mengisi logbook kegiatan harian lengkap dengan ringkasan pekerjaan dan lampiran bukti kerja, dengan batas pengisian maksimal 3 hari kerja ke belakang.
7. **US-PST-07 (Pengerjaan & Pengumpulan Tugas)**: Sebagai peserta aktif, saya ingin melihat instruksi penugasan dari pembimbing, mengunduh lampiran tugas, dan mengunggah hasil pengerjaan sebelum tenggat waktu berakhir.
8. **US-PST-08 (Batal Kirim Tugas / Unsubmit)**: Sebagai peserta aktif, saya ingin dapat membatalkan pengiriman tugas (*unsubmit*) untuk merevisi berkas jawaban selama tugas tersebut belum dinilai oleh pembimbing lapangan.

### 2.2 Pembimbing Lapangan (Aparatur Sipil Negara Dinas Teknis)
1. **US-PBD-01 (Monitoring Mentee Binaan)**: Sebagai pembimbing, saya ingin melihat daftar mentee aktif yang ditempatkan pada bidang saya, lengkap dengan ringkasan progres pengerjaan tugas dan presensi logbook mereka.
2. **US-PBD-02 (Penerbitan Tugas Mandiri & Batch)**: Sebagai pembimbing, saya ingin menerbitkan tugas proyek bimbingan kepada satu mentee atau secara serentak ke beberapa mentee binaan sekaligus (*batch assignment*) dengan instruksi dan tenggat waktu seragam.
3. **US-PBD-03 (Review & Validasi Logbook)**: Sebagai pembimbing, saya ingin meninjau isi logbook kegiatan harian mentee dan memberikan status Disetujui atau Perlu Revisi, baik secara individual maupun massal (*bulk review*).
4. **US-PBD-04 (Penilaian Tugas & Umpan Balik)**: Sebagai pembimbing, saya ingin mengoreksi laporan hasil tugas mentee, memberikan catatan umpan balik, dan menandai status tugas selesai.
5. **US-PBD-05 (Pengelolaan Agenda Bimbingan)**: Sebagai pembimbing, saya ingin mencatat jadwal pertemuan bimbingan tatap muka atau daring beserta notulensi arahan teknis.
6. **US-PBD-06 (Ekspor Kinerja & Penyelesaian Magang)**: Sebagai pembimbing, saya ingin mengunduh rekapitulasi laporan kinerja pembimbingan (format Excel, CSV, atau PDF) dan menetapkan kelulusan selesai magang mentee binaan setelah target kerja terpenuhi.

### 2.3 Admin Verifikator (Administrasi Kominfo)
1. **US-ADM-01 (Pengawasan Antrean SLA)**: Sebagai admin, saya ingin melihat antrean berkas pendaftaran yang diurutkan berdasarkan kedekatan dengan target batas SLA (5 hari kerja), sehingga berkas tidak terlambat diproses.
2. **US-ADM-02 (Verifikasi Berkas)**: Sebagai admin, saya ingin memeriksa kelengkapan setiap berkas pendaftar dan memberikan keputusan: Disetujui, Perlu Perbaikan, atau Ditolak.
3. **US-ADM-03 (Manajemen Kuota Formasi Bulanan)**: Sebagai admin, saya ingin mengatur kapasitas kuota per jurusan, membuka atau menutup periode bulan magang, dan memantau pemanfaatan kuota.
4. **US-ADM-04 (Penempatan Dua Tahap)**: Sebagai admin, saya ingin membagi alur kerja antara pendaftar yang lolos berkas dan penetapan penempatan, lalu menempatkan peserta yang memenuhi syarat ke bidang dinas dan pembimbing lapangan.
5. **US-ADM-05 (Unduh Bundel Berkas)**: Sebagai admin, saya ingin mengunduh seluruh berkas lampiran peserta dalam satu arsip ZIP untuk keperluan kearsipan fisik kedinasan.
6. **US-ADM-06 (Pelaporan Data Kedinasan)**: Sebagai admin, saya ingin mengekspor rekapitulasi data pendaftar dan penempatan ke format spreadsheet terstandarisasi.

### 2.4 SuperAdmin (Administrator Utama Sistem)
1. **US-SUP-01 (Konfigurasi Parameter Operasional)**: Sebagai SuperAdmin, saya ingin mengonfigurasi 12 parameter operasional sistem melalui antarmuka web tanpa memerlukan deploy ulang kode sumber.
2. **US-SUP-02 (Pengelolaan Hak Akses Dinamis)**: Sebagai SuperAdmin, saya ingin mengatur matriks izin akses untuk setiap peran pengguna pada 13 modul sistem definitif.
3. **US-SUP-03 (Manajemen Akun Staf)**: Sebagai SuperAdmin, saya ingin membuat akun Admin dan Pembimbing dengan kredensial awal acak dan mewajibkan penggantian kata sandi pada login pertama.
4. **US-SUP-04 (Alokasi Jalur Khusus Kemitraan)**: Sebagai SuperAdmin, saya ingin mengalokasikan slot kemitraan khusus instansi dinas secara terisolasi tanpa mengurangi kuota publik umum.
5. **US-SUP-05 (Pencadangan & Pemulihan Basis Data)**: Sebagai SuperAdmin, saya ingin membuat snapshot cadangan basis data terkompresi `.sql.gz` dengan checksum SHA-256 dan memulihkan data jika terjadi insiden darurat.
6. **US-SUP-06 (Inspeksi Forensik Jejak Audit)**: Sebagai SuperAdmin, saya ingin memeriksa log audit mutasi data lintas 4 kanal (`auth`, `aksi`, `service`, `akses_data`) dan mengekspornya ke format spreadsheet.
7. **US-SUP-07 (Pemantauan Telemetri Kesehatan)**: Sebagai SuperAdmin, saya ingin memantau latensi server, koneksi database, integritas disk penyimpanan, dan menjalankan tindakan diagnostik server.
8. **US-SUP-08 (Tata Kelola Konten Landing Page)**: Sebagai SuperAdmin, saya ingin mengedit tata letak dan teks portal informasi publik menggunakan editor blok visual.
9. **US-SUP-09 (Batasan Read-Only Ranah Pembimbing)**: Sebagai SuperAdmin, saya memiliki visibilitas pemantauan menyeluruh, namun secara mutlak tidak memiliki izin untuk memanipulasi tugas mentee, memvalidasi logbook, atau merotasi penempatan bimbingan yang menjadi wewenang pembimbing lapangan.

---

## 3. Aturan Bisnis Baku Sistem (Business Rules BR-01 s/d BR-19)

### BR-01: Durasi Pelaksanaan Magang
- Masa pelaksanaan magang yang diizinkan berada pada rentang **1 hingga 6 bulan kalender**.
- Batas maksimal dikontrol melalui parameter operasional `durasi_magang_maks_bulan` (nilai default: 6).
- Tanggal selesai magang wajib lebih besar daripada tanggal mulai magang.

### BR-02: Batas Kuota Formasi dan Slot Khusus
- Kuota umum pendaftaran reguler dibatasi maksimal per bulan per jurusan melalui parameter `kuota_umum_maks_per_bulan` (nilai default: 12).
- Slot jalur khusus kemitraan kelembagaan dikontrol melalui parameter `slot_khusus_default` (nilai default: 3).
- Slot khusus dialokasikan terpisah dan tidak boleh mengurangi sisa kuota umum publik (*zero quota leakage*).

### BR-03: Kategori Jenjang Pendidikan & Persyaratan Berkas
- Sistem mendukung 3 kategori jenjang pendidikan: **SMK**, **D3**, dan **S1**.
- Persyaratan berkas wajib ditentukan berdasarkan master `jenis_dokumen`:
  - Jenjang SMK: Surat Pengantar Sekolah, Transkrip/Rapor Terakhir, Salinan KTP/Kartu Pelajar, Biodata Diri.
  - Jenjang D3 & S1: Surat Pengantar Kampus/Fakultas, Transkrip Akademik Kumulatif, Proposal Magang, Salinan KTP, Kartu Tanda Mahasiswa (KTM).
- Batas ukuran setiap berkas unggahan maksimal dikontrol oleh parameter `berkas_maks_mb` (nilai default: 10 MB) dengan format yang diizinkan: PDF, JPG, PNG.

### BR-04: Ketentuan Pendaftaran Kelompok / Tim
- Pendaftaran kelompok hanya diizinkan apabila parameter `fitur_pendaftaran_kelompok_aktif` bernilai `1` (aktif).
- Jumlah anggota dalam satu kelompok dibatasi maksimal oleh parameter `maks_anggota_kelompok` (nilai default: 4 orang, terdiri dari 1 ketua dan maksimal 3 anggota).
- Seluruh anggota kelompok wajib memilih jurusan dan periode bulan magang yang sama dengan ketua kelompok.
- Kuota formasi jurusan akan dipotong sejumlah total anggota aktif di dalam kelompok tersebut saat pendaftaran diajukan.

### BR-05: Konkurensi Alokasi Kuota Atomik (Atomic Locking)
- Pengurangan kuota pendaftaran formasi bulanan **wajib** diapit oleh transaksi basis data dengan penguncian pesimistik (`lockForUpdate()`).
- Pengecekan sisa kuota di luar transaksi database dilarang keras.
- Jika permintaan pendaftaran masuk secara bersamaan saat sisa kuota kurang dari jumlah pendaftar (individu atau total tim), sistem harus membatalkan transaksi dan mengembalikan respon penolakan kuota penuh (*zero negative quota*).

### BR-06: SLA Verifikasi Berkas (SLA Watchdog)
- Setiap berkas pendaftaran yang berstatus `diajukan` memiliki target penyelesaian verifikasi dalam kurun waktu `sla_verifikasi_hari` (nilai default: 5 hari kerja).
- Sistem menghitung selisih hari kerja berjalan sejak tanggal pengajuan.
- Antrean verifikasi admin menampilkan indikator visual:
  - Hijau: Waktu tunggu 1–3 hari kerja.
  - Kuning: Mendekati batas SLA (hari kerja ke-4).
  - Merah: Melampaui batas SLA (≥ 5 hari kerja).

### BR-07: Alur Penempatan Dua Tahap (Two-Stage Placement)
- Pemohon yang dinyatakan memenuhi syarat administrasi oleh verifikator tidak langsung ditempatkan ke pembimbing, melainkan masuk ke status `diterima` dan antrean *Belum Ditempatkan*.
- Administrator melakukan plotting penempatan ke salah satu dari 5 bidang dinas dan menunjuk pembimbing lapangan ASN yang bertugas pada bidang tersebut.
- Penempatan dapat berasal dari alur pendaftaran online (`sumber = online`) maupun penugasan resmi kedinasan manual (`sumber = manual`).

### BR-08: Pembatasan Beban Bimbingan Pembimbing (Mentee Limiter)
- Jumlah mentee aktif yang dibimbing oleh seorang pembimbing lapangan pada periode waktu yang sama tidak boleh melebihi parameter `maks_peserta_aktif_per_pembimbing` (nilai default: 20 mentee).
- Sistem memblokir aksi penugasan mentee baru kepada pembimbing apabila kuota bimbingan staf yang bersangkutan telah mencapai batas maksimum.

### BR-09: Batas Mundur Pengisian Logbook Harian
- Peserta magang wajib mengisi aktivitas logbook secara berkala.
- Untuk mencegah penumpukan atau pelaporan rapel di akhir program, sistem membatasi batas mundur pengisian tanggal logbook maksimal `logbook_maks_mundur_hari` (nilai default: 3 hari kerja sebelum tanggal hari ini).
- Tanggal aktivitas logbook yang lebih lampau dari H-3 otomatis ditolak oleh validasi sistem.

### BR-10: Multi-Lampiran Pendukung Logbook
- Setiap entri logbook dapat memuat multi-lampiran dokumen pendukung kegiatan (gambar dokumentasi atau berkas laporan pekerjaan).
- Seluruh berkas lampiran disimpan pada storage privat dan divalidasi format serta ukurannya.

### BR-11: Penyelesaian Masa Magang 1-Klik
- Pembimbing lapangan dapat menandai selesainya masa magang peserta binaannya melalui tombol aksi penyelesaian bimbingan 1-klik (`MarkTugasCompletedAction` / penyelesaian penempatan).
- Prasyarat penyelesaian: Peserta berada dalam status penempatan `aktif`, seluruh tugas yang ditugaskan telah dievaluasi, dan masa magang telah mencapai batas tanggal akhir yang ditetapkan.
- Penetapan selesai mengubah status penempatan menjadi `selesai`.

### BR-12: Masa Tenggang Akun Pasca-Magang (Grace Period)
- Akun peserta yang berstatus `selesai` memasuki masa tenggang operasional selama `masa_tenggang_bulan` (nilai default: 2 bulan).
- Selama masa tenggang, peserta masih dapat masuk ke sistem untuk melihat riwayat tugas, mengunduh portofolio, dan memeriksa arsip dokumen.
- Setelah masa tenggang berakhir, perintah scheduler sistem (`App\Console\Commands\NonaktifkanAkunPascaMagang`) otomatis menonaktifkan akun peserta (`is_active = false`) dan mengarsipkan data secara permanen.

### BR-13: Penyimpanan Berkas Privat & Signed URLs
- Seluruh dokumen identitas, surat pengantar, berkas persyaratan, dan lampiran tugas disimpan di direktori privat (`storage/app/private/`).
- Berkas dilarang keras dapat diakses secara publik melalui URL statis.
- Pengunduhan berkas hanya dapat dilakukan melalui rute perantara yang memverifikasi tanda tangan digital URL bertanda tangan HMAC (*Temporary Signed URL*) dengan masa kedaluwarsa 5 menit, diperkuat otorisasi kepemilikan data melalui Laravel Policy.

### BR-14: Audit Trail Forensik Append-Only
- Seluruh operasi mutasi data penting pada entitas pendaftaran, kuota, penempatan, akun staf, dan konfigurasi sistem wajib dicatat ke tabel `audit_log`.
- Log audit diklasifikasikan ke dalam 4 kanal stream: `auth`, `aksi`, `service`, dan `akses_data`.
- Seluruh parameter sensitif (kata sandi, token otentikasi, nomor rahasia) wajib disensor secara rekursif sebelum dicatat ke database.
- Tabel `audit_log` bersifat append-only: aplikasi dilarang menyediakan rute atau fungsi untuk melakukan `UPDATE` atau `DELETE` pada baris audit.
- Masa retensi data audit dikendalikan oleh parameter `retensi_audit_akses_data_hari` (nilai default: 365 hari).

### BR-15: Pengaturan Parameter Terpusat (12 Parameter Operasional)
Konfigurasi operasional sistem dikendalikan melalui 12 kunci baku pada tabel `pengaturan`:
1. `kuota_umum_maks_per_bulan`: Batas maksimal kuota pendaftaran umum per jurusan per bulan.
2. `slot_khusus_default`: Alokasi default slot kuota jalur kemitraan dinas.
3. `maks_peserta_aktif_per_pembimbing`: Batas rasio maksimal mentee aktif per pembimbing ASN.
4. `maks_anggota_kelompok`: Batas maksimal anggota per kelompok pendaftaran.
5. `durasi_magang_maks_bulan`: Batas durasi magang terlama yang diizinkan (bulan).
6. `logbook_maks_mundur_hari`: Batas toleransi mundur pengisian tanggal logbook (hari kerja).
7. `berkas_maks_mb`: Batas ukuran maksimal tiap berkas unggahan (MB).
8. `deadline_mendekati_hari`: Ambang batas peringatan mendekati batas waktu tugas (hari).
9. `sla_verifikasi_hari`: Batas toleransi waktu penyelesaian verifikasi berkas (hari kerja).
10. `masa_tenggang_bulan`: Durasi akses akun peserta setelah masa magang berakhir (bulan).
11. `retensi_audit_akses_data_hari`: Masa simpan rekam jejak audit sebelum dapat dipurging (hari).
12. `fitur_pendaftaran_kelompok_aktif`: Sakelar global pengaktifan pendaftaran kelompok (1 = aktif, 0 = nonaktif).

### BR-16: Perhitungan Dinamis Status Batas Waktu Tugas
- Tenggat waktu tugas (*due date*) ditentukan oleh pembimbing saat pembuatan tugas.
- Status ketepatan waktu pengumpulan dihitung secara dinamis pada saat penyerahan:
  - Tepat Waktu: Penyerahan dilakukan sebelum atau sama dengan batas waktu tenggat tugas.
  - Terlambat: Penyerahan dilakukan setelah batas waktu tenggat tugas terlewati.
- Peringatan mendekati batas waktu ditampilkan pada dashboard peserta apabila sisa waktu pengerjaan ≤ `deadline_mendekati_hari`.

### BR-17: Proteksi Sanitasi Ekspor Data (Anti-Formula Injection)
- Seluruh layanan ekspor dokumen spreadsheet (CSV, Excel XLSX via `XlsxExportService`) wajib menerapkan sanitasi sel.
- Nilai sel yang diawali oleh karakter berbahaya (`=`, `+`, `-`, `@`, tab `\t`, carriage return `\r`) wajib diprefiks dengan tanda kutip tunggal (`'`) untuk mencegah eksekusi formula injection pada aplikasi pembaca spreadsheet.

### BR-18: Alur Batal Kirim Tugas (Unsubmit Workflow)
- Peserta yang telah menyerahkan berkas laporan tugas memiliki hak untuk membatalkan pengiriman (*unsubmit*) secara mandiri.
- Aksi unsubmit hanya diizinkan selama tugas tersebut **belum dinilai atau direview** oleh pembimbing lapangan (`progres_log.status != 'diterima'`).
- Setelah unsubmit berhasil, status penyerahan kembali ke draf dan peserta dapat mengunggah ulang berkas perbaikan tanpa perlu intervensi manual dari staf administrator.

### BR-19: Penugasan Serentak Multi-Mentee (Batch Classroom Assignment)
- Pembimbing lapangan dapat membuat dan mendistribusikan tugas proyek bimbingan serentak (*batch assignment*) ke beberapa mentee binaan sekaligus melalui `CreateBatchTugasAction`.
- Seluruh tugas yang diterbitkan dalam satu batch memiliki judul, instruksi deskripsi, lampiran pedoman kerja, dan batas waktu pengumpulan yang identik, namun memiliki rekam progres pengerjaan yang terisolasi per mentee.

---

## 4. Kriteria Penerimaan (Acceptance Criteria)

Kepatuhan sistem terhadap aturan bisnis diverifikasi melalui rangkaian pengujian otomatis (*Automated Feature Tests*):

| Kode Aturan | Kriteria Penerimaan | Verifikasi Pengujian |
|---|---|---|
| **BR-05** | Tidak terjadi kuota minus saat beberapa pendaftar mendaftar secara konkuren. | `Tests\Feature\QuotaConcurrencyTest` |
| **BR-06** | Status SLA terhitung akurat sesuai hari kerja dan antrean memunculkan indikator keterlambatan. | `Tests\Feature\AdminPendaftaranTest` |
| **BR-07** | Alur verifikasi berkas terpisah dari penempatan; peserta lolos masuk ke antrean belum ditempatkan. | `Tests\Feature\AdminPenempatanDuaTahapTest` |
| **BR-08** | Sistem menolak plotting jika pembimbing telah mencapai batas 20 mentee aktif. | `Tests\Feature\AdminPenempatanArchitectureAndSecurityTest` |
| **BR-09** | Logbook dengan tanggal lebih lampau dari H-3 ditolak dengan validasi 422. | `Tests\Feature\TugasLogbookTest` |
| **BR-13** | Dokumen privat tidak dapat diakses tanpa signed URL valid dan dicegah oleh Policy. | `Tests\Feature\DocumentSecurityTest` |
| **BR-14** | Aksi mutasi penting mencatat log audit; kata sandi dan token otomatis disamarkan. | `Tests\Feature\CoreFoundationTest` |
| **BR-15** | Nilai 12 parameter operasional dapat diperbarui oleh SuperAdmin dan tervalidasi batasan nilainya. | `Tests\Feature\PredikatBatasValidationTest` |
| **BR-17** | Sel yang memuat `=CMD` otomatis disanitasi menjadi `'=CMD` pada ekspor CSV dan XLSX. | `Tests\Feature\PembimbingLaporanKinerjaExportArchitectureTest` |
| **BR-18** | Peserta dapat membatalkan pengiriman tugas yang belum direview pembimbing. | `Tests\Feature\PesertaTugasBatalKirimTest` |
| **BR-19** | Pembimbing berhasil membuat tugas serentak ke 5 mentee dengan 1 kali aksi submission. | `Tests\Feature\PembimbingTugasArchitectureSafetyTest` |
