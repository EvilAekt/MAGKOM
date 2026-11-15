# Dokumentasi Fitur: Administrasi & Seleksi Magang

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Modul Sistem | `seleksi.verifikasi-berkas`, `kuota.manajemen` |
| Rute Terkait | `/admin/dashboard`, `/admin/pendaftaran/*`, `/admin/kuota/*`, `/admin/penempatan/*`, `/admin/laporan/*` |
| Referensi Implementasi | `app/Http/Controllers/Admin/`, `app/Actions/Admin/`, `app/Queries/Admin/` |

---

## 1. Overview
Modul Administrasi ditujukan bagi staf tata usaha dan sekretariat Diskominfo Sleman. Modul ini bertanggung jawab mengelola antrean verifikasi berkas permohonan pendaftar dengan pengawasan waktu layanan **SLA Watchdog** (standar 5 hari kerja), penetapan keputusan seleksi administratif, pengunduhan bundel berkas pendaftar dalam arsip ZIP, pengelolaan kuota formasi bulanan per kejuruan, serta pelaporan data kedinasan.

---

## 2. Business Context
Sebelum adanya MAGKOM, berkas fisik pendaftaran magang menumpuk di meja sekretariat tanpa kejelasan batas waktu verifikasi, menimbulkan komplain dari pihak sekolah dan kampus pemohon. Pengawasan SLA digital memberikan kepastian hukum dan waktu proses bagi para pendaftar, sekaligus menyajikan metrik akuntabilitas bagi pimpinan dinas.

---

## 3. User Roles
- **Admin Verifikator (Administrasi Kominfo)**: Memeriksa berkas permohonan, memberikan keputusan verifikasi, mengunduh bundel arsip ZIP, mengontrol kapasitas kuota formasi, dan memantau antrean penempatan dua tahap.

---

## 4. Preconditions
- Pengguna terotentikasi dengan peran `admin` atau `superadmin`.
- Akun berstatus aktif dan terdaftar secara sah pada sistem kedinasan.

---

## 5. Workflow
```mermaid
flowchart TD
    A["Admin Membuka Dashboard (/admin/dashboard)"] --> B["Pantau Indikator Antrean SLA Watchdog & Antrean Penempatan"]
    
    B --> C{"Aktivitas Admin"}

    subgraph VerifikasiBerkas ["1. Verifikasi Berkas & SLA (BR-06)"]
        C --> D["Buka Antrean Berkas (/admin/pendaftaran)"]
        D --> E["Tinjau Indikator Visual SLA: Hijau (1-3 hari), Kuning (4 hari), Merah (>=5 hari)"]
        E --> F["Buka Detail Pendaftar & Unduh Berkas Persyaratan (KTP, KTM, Surat, Proposal)"]
        F --> G{"Keputusan Verifikator"}
        G -- "Disetujui" --> H["Status Menjadi 'diterima' -> Otomatis Masuk Antrean Belum Ditempatkan"]
        G -- "Perlu Perbaikan" --> I["Status Menjadi 'perlu_perbaikan' + Catatan Koreksi untuk Peserta"]
        G -- "Ditolak" --> J["Status Menjadi 'ditolak' -> Kuota Dikembalikan ke Formasi Jurusan"]
    end

    subgraph ArsipZip ["2. Pengunduhan Bundel Berkas ZIP"]
        C --> K["Klik 'Unduh Semua Berkas (ZIP)' pada Detail Pendaftar"]
        K --> L["DocumentArchiveService Mengemas Seluruh Lampiran Menjadi 1 Berkas ZIP"]
        L --> M["Unduhan Dikirimkan ke Peramban Admin"]
    end

    subgraph KelolaKuota ["3. Kontrol Formasi Bulanan"]
        C --> N["Buka Menu Kuota (/admin/kuota)"]
        N --> O["Buka/Tutup Periode Bulan Magang"]
        O --> P["Sesuaikan Kapasitas Kuota per Jurusan (StoreKuotaAction / UpdateKuotaAction)"]
    end
```

---

## 6. Business Rules
- **BR-02 (Kapasitas Kuota Bulanan)**: Admin mengalokasikan kuota per jurusan dalam batas maksimal yang diizinkan oleh parameter global (`kuota_umum_maks_per_bulan = 12`).
- **BR-06 (SLA Verifikasi Berkas 5 Hari Kerja)**: Sistem secara otomatis menghitung selisih hari kerja sejak berkas diajukan. Antrean verifikasi menampilkan lencana visual urgensi agar berkas yang mendekati atau melampaui batas SLA dapat segera ditindaklanjuti.
- **BR-07 (Penempatan Dua Tahap)**: Keputusan `diterima` pada tahap verifikasi berkas tidak langsung menempatkan peserta ke pembimbing, melainkan memindahkannya ke antrean *Belum Ditempatkan*.

---

## 7. Technical Implementation
- **Controller**:
  - `Admin\DashboardController`: Menghitung metrik antrean verifikasi, utilisasi kuota, dan sisa SLA.
  - `Admin\PendaftaranController`: Menangani antrean seleksi, pratinjau berkas, keputusan verifikasi, dan download ZIP.
  - `Admin\KuotaController`: Mengelola master periode bulan dan alokasi kapasitas jurusan.
  - `Admin\PenempatanController`: Mengelola antrean penempatan dua tahap.
  - `Admin\LaporanController`: Menyediakan antarmuka rekapitulasi data pendaftaran.
- **Action Classes**:
  - `PutuskanPendaftaranAction`: Menjalankan mutasi status verifikasi dan pembaruan riwayat pemeriksaan berkas.
  - `StoreKuotaAction` & `UpdateKuotaAction`: Membuat dan memperbarui kapasitas kuota jurusan.
  - `ToggleKuotaAction`: Mengubah status aktif/nonaktif kuota secara cepat.
- **Query Classes**:
  - `Admin\Pendaftaran\PendaftaranIndexQuery`: Mengambil data pendaftar dengan kalkulasi hari kerja SLA dan filter status.
  - `Admin\Kuota\KuotaIndexQuery`: Mengambil matriks kuota jurusan per periode bulan.
- **Service**:
  - `DocumentArchiveService`: Menggunakan pustaka `ZipArchive` bawaan PHP untuk mengemas berkas pendaftar menjadi arsip ZIP terkompresi.

---

## 8. Database Interaction
- Membaca dan memperbarui tabel `pendaftaran`.
- Membaca dan memperbarui tabel `dokumen` dan `dokumen_verifikasi`.
- Membaca dan memperbarui tabel `kuota` dan `bulan_magang`.
- Mencatat seluruh keputusan verifikasi berkas ke dalam tabel `audit_log` pada kanal stream `aksi`.

---

## 9. Validation and Authorization
- Seluruh rute dilindungi oleh middleware `auth` dan `role:admin`.
- Aksi keputusan verifikasi divalidasi memastikan hanya pendaftaran berstatus `diajukan` yang dapat diputuskan.
- Otorisasi penolakan permohonan wajib menyertakan alasan penolakan pada kolom `catatan_verifikasi`.

---

## 10. Error Handling
- Jika terjadi kesalahan saat pengemasan berkas ZIP (misalnya ada satu berkas yang hilang secara fisik dari disk penyimpanan), sistem tetap melanjutkan pengemasan berkas yang tersedia dan menambahkan berkas teks catatan `README_BERKAS_HILANG.txt` di dalam ZIP untuk menginformasikan file yang terlewat.
- Percobaan verifikasi ganda dicegah melalui transaksi basis data.

---

## 11. Testing
- `Tests\Feature\AdminPendaftaranTest`: Menguji pemfilteran antrean, perhitungan SLA hari kerja, dan eksekusi putusan verifikasi.
- `Tests\Feature\AdminKuotaTest`: Menguji pembuatan dan pembatasan alokasi kuota jurusan.

---

## 12. Limitations
- Admin tidak memiliki wewenang untuk mengubah parameter operasional sistem atau memanipulasi matriks izin peran (wewenang eksklusif SuperAdmin).
