# Rencana Kerja: Redesign Halaman Manajemen Kuota Berbasis Bidang & Bulan Magang

**Tanggal Pembuatan**: 19 Oktober 2026  
**Status**: ✅ Selesai Diimplementasikan  
**Branch**: `test-1`  
**Referensi Desain**: Tangkapan layar antarmuka pengguna (`Bulan & kuota`, Card Hero Gelombang Aktif, Tabel Kuota per Bidang, dan Validasi Kapasitas Pembimbing).  
**Catatan Wajib Pengguna**: Tetap menggunakan istilah **"Bulan"** / **"Bulan Magang"** (bukan "Periode").

---

## 1. Tujuan
Mentransformasi antarmuka halaman **Manajemen Kuota Magang** (`resources/views/admin/kuota/index.blade.php`) agar 100% presisi dan selaras dengan layout desain baru yang dilampirkan pengguna:
1. Mengadopsi struktur tampilan tingkat atas dengan judul **Bulan & kuota**, deskripsi ringkas, serta tombol aksi cepat **`Ubah bulan`** dan **`+ Buat bulan baru`**.
2. Merombak hero card menjadi **Banner Biru Royal Glassmorphic** yang menampilkan status **BULAN AKTIF**, nama gelombang/bulan magang, status pendaftaran (`● Dibuka` / `● Ditutup`), 3 parameter jadwal vertikal (Tanggal buka, Tanggal tutup, Mulai magang), serta **Glassmorphism Floating Metric Card** di sisi kanan (rasio kuota terisi, progress bar horizontal putih, dan countdown sisa hari pendaftaran).
3. Mengubah tabel kuota dari daftar perorangan jurusan menjadi **Tabel Agregasi Kuota per Bidang**:
   - Kolom: `BIDANG`, `JURUSAN YANG DIBUTUHKAN`, `JENJANG`, `DURASI`, `KUOTA TERISI`, `KAPASITAS`, dan `AKSI`.
   - Menampilkan visual inisial avatar berwarna unik untuk setiap bidang (Aplikasi Informatika, Statistik/IKP, ITI/Persandian, Sekretariat).
   - Menampilkan tag jenjang (`SMK`, `D3`, `S1`) dan rentang durasi (`1–6 bulan`).
   - Progress bar okupansi mini di setiap baris bidang dan tombol aksi `Ubah`.
4. Menyediakan **Info Banner Validasi Kapasitas Pembimbing** di bagian bawah tabel sesuai spesifikasi desain.
5. Memastikan ketersediaan modal interaktif:
   - Modal **`Ubah bulan`** (edit jadwal buka/tutup & tanggal mulai bulan aktif).
   - Modal **`+ Buat bulan baru`** (buat bulan magang baru langsung dari halaman kuota).
   - Modal **`+ Tambah kuota`** & **`Ubah kuota bidang`** (alokasi kapasitas kuota per bidang/jurusan).

---

## 2. Asumsi & Pemetaan Desain

1. **Istilah Resmi**: Sesuai instruksi mutlak pengguna, seluruh label dan teks antarmuka menggunakan kata **"Bulan"** / **"Bulan Magang"** (misal: *Bulan & kuota*, *Ubah bulan*, *+ Buat bulan baru*, *Bulan Aktif*, *Bulan November 2026* alih-alih kata "Periode").
2. **Kapasitas & Okupansi per Bidang**:
   - Kuota terisi dan kapasitas bidang diagregasikan dari data relasi `bidang -> jurusan -> kuota` pada bulan magang yang sedang aktif.
   - Total kapasitas bimbingan bidang merujuk pada jumlah pembimbing lapangan berstatus aktif pada bidang terkait dikalikan rasio bimbingan (BR-01 / BR-12).
3. **Kompatibilitas Logika Backend (Zero Breaking Changes)**:
   - Skema database tabel `kuota`, relasi `pendaftaran.kuota_id`, dan controller aksi yang sudah ada tetap dipertahankan 100% kompatibel sehingga seluruh pengujian (`EndToEndUatTest`, `AdminTerminologyDesignSystemTest`, dll.) tetap lulus tanpa regresi.
4. **Fleksibilitas Pengelolaan Bulan**:
   - Admin operasional dan SuperAdmin dapat langsung mengakses fungsi pembaruan jadwal bulan aktif dan pembuatan bulan magang baru melalui modal tanpa harus berpindah halaman.

---

## 3. Evaluasi Pendekatan Teknis

| Parameter | Opsi 1: Agregasi Domain Model Bidang & Hybrid Modal (Rekomendasi) | Opsi 2: Migrasi Skema Database Kuota ke Bidang |
| :--- | :--- | :--- |
| **Deskripsi** | Menggunakan relasi `Bidang::with(['jurusan', 'users'])` yang sudah ada di database, memetakan kapasitas serta pendaftar jurusan ke level bidang secara dinamis, dan menambahkan modal interaktif. | Mengubah kolom database `kuota` menjadi `bidang_id` dan membuang relasi `jurusan_id`. |
| **Kelebihan** | 1. 100% bebas risiko *breaking changes* pada tabel `pendaftaran`, `penempatan`, dan `waiting_list`.<br>2. Tampilan visual 100% identik dengan desain tangkapan layar user.<br>3. Mendukung alokasi jurusan di dalam bidang secara transparan.<br>4. Semua pengujian otomatis eksisting tetap hijau. | Data di tabel kuota langsung mengacu pada bidang saja. |
| **Kekurangan** | Membutuhkan kalkulasi agregasi yang rapi di controller (dengan eager loading agar bebas N+1 query). | Memerlukan migrasi database besar (*destructive*), merombak rute, controller pendaftaran, dan memicu *breaking changes* masif. |
| **Keputusan** | **DIPILIH (Opsi 1)**. | Ditolak karena melanggar aturan stabilitas data. |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Controllers/Admin/KuotaController.php` | `Modify` | Siapkan data agregasi `bidangList` lengkap dengan jurusan, pembimbing aktif, total kuota, okupansi, dan durasi magang; tambahkan handler untuk simpan/ubah bulan magang langsung dari konteks kuota. |
| `resources/views/admin/kuota/index.blade.php` | `Modify` | Rombak total struktur HTML & CSS sesuai desain: Header `Bulan & kuota`, Hero Card Gelombang Aktif dengan glassmorphism stat box, Section `Kuota per bidang`, Tabel Agregasi Bidang, Footer Alert Pembimbing, serta Modal Ubah Bulan, Buat Bulan, dan Tambah/Edit Kuota. |
| `routes/web.php` | `Modify` | Daftarkan route baru `admin.kuota.bulan.store`, `admin.kuota.bulan.update`, dan `admin.kuota.bidang.update`. |
| `tests/Feature/AdminKuotaRevampTest.php` | `Create` | Feature test otomatis untuk memvalidasi tampilan layout baru, keberadaan komponen hero banner, tabel kuota per bidang, dan fungsionalitas modal. |

---

## 5. Rincian Spesifikasi Komponen Desain

### A. Header Halaman
- Judul: `Bulan & kuota`
- Subtitle: `Atur bulan pendaftaran dan kuota magang per bidang.`
- Aksi Kanan:
  - Tombol **`Ubah bulan`**: Tombol outline/putih dengan ikon pensil edit (membuka `#modalUbahBulan`).
  - Tombol **`+ Buat bulan baru`**: Tombol primary blue dengan ikon plus (membuka `#modalBuatBulan`).

### B. Banner Hero Gelombang Aktif
- **Badge Status**: `BULAN AKTIF` (pill semi-transparan putih di pojok kiri atas banner).
- **Judul Gelombang**: `Gelombang 1` (atau nama bulan magang aktif, misal `Bulan November 2026`) berdampingan dengan badge status:
  - Jika buka: `● Dibuka` (background putih semi-transparan, teks `#10B981`).
  - Jika tutup: `● Ditutup` (background putih semi-transparan, teks `#EF4444`).
- **3 Kolom Informasi Jadwal**:
  1. Ikon Kalender: `Tanggal buka` (font 0.75rem semi-transparan) &rarr; `1 Oktober 2026` (font 0.95rem bold putih).
  2. Ikon Bendera: `Tanggal tutup` (font 0.75rem semi-transparan) &rarr; `15 Oktober 2026` (font 0.95rem bold putih).
  3. Ikon Peserta: `Mulai magang` (font 0.75rem semi-transparan) &rarr; `2 November 2026` (font 0.95rem bold putih).
- **Glassmorphic Floating Box (Kanan)**:
  - Background `rgba(255, 255, 255, 0.15)`, backdrop-filter `blur(10px)`, border `1px solid rgba(255, 255, 255, 0.25)`.
  - Label: `Kuota terisi`.
  - Angka: `<strong style="font-size: 2.5rem;">X</strong> / Y peserta`.
  - Progress Bar: Bar ramping horizontal dengan fill putih.
  - Status Countdown: Ikon jam + teks `Ditutup dalam X hari` / `Pendaftaran ditutup`.

### C. Section & Tabel Kuota per Bidang
- **Header Section**:
  - Judul: `Kuota per bidang`
  - Subtitle: `Tentukan jumlah kuota, jurusan, jenjang, dan rentang durasi magang.`
  - Tombol: `+ Tambah kuota` (membuka `#modalTambahKuota`).
- **Tabel Desain**:
  - `BIDANG`: Avatar lingkaran berwarna (Inisial A, S, P, S) + Nama Bidang tegas.
  - `JURUSAN YANG DIBUTUHKAN`: Teks daftar jurusan yang bernaung di bawah bidang tersebut.
  - `JENJANG`: Badges pill (`SMK`, `D3`, `S1`).
  - `DURASI`: `1–6 bulan` (atau rentang yang diizinkan).
  - `KUOTA TERISI`: Format `X / Y  Z%` dengan bar progress biru di bawahnya.
  - `KAPASITAS`: `Y peserta`.
  - `AKSI`: Tombol `Ubah` (membuka modal edit alokasi kuota bidang terkait).

### D. Footer Alert Validasi Pembimbing
- Box biru lembut (`#EFF6FF`), border `1px solid #BFDBFE`, radius 12px.
- Ikon informasi `(i)` biru royal.
- Teks: `Sistem otomatis memvalidasi agar total kuota tidak melebihi kapasitas bimbingan, yaitu jumlah Pembimbing aktif di setiap bidang.`

---

## 6. Langkah Implementasi Terstruktur

- [x] **Fase 1: Penyiapan Data Controller (`app/Http/Controllers/Admin/KuotaController.php`)**
  - [x] Eager load relasi `Bidang` dengan `jurusan` dan `users` (pembimbing aktif).
  - [x] Hitung agregasi kapasitas, keterisian, persentase okupansi, dan daftar jenjang per bidang.
  - [x] Sediakan endpoint/handler untuk pembaruan jadwal bulan aktif (`updateBulan`) dan pembuatan bulan baru (`storeBulan`) yang terintegrasi audit log.
- [x] **Fase 2: Perombakan Layout & Desain Blade (`resources/views/admin/kuota/index.blade.php`)**
  - [x] Implementasikan Header halaman: Judul `Bulan & kuota`, deskripsi, tombol `Ubah bulan` dan `+ Buat bulan baru`.
  - [x] Implementasikan Hero Banner: Badge `BULAN AKTIF`, nama bulan/gelombang, badge `● Dibuka`, 3 item jadwal vertikal, dan Glassmorphic Floating Stat Box.
  - [x] Implementasikan Section `Kuota per bidang` lengkap dengan tombol `+ Tambah kuota`.
  - [x] Rancang Tabel Kuota per Bidang dengan avatar inisial berwarna, daftar jurusan, badges jenjang, durasi, progress bar mini kuota terisi, kapasitas, dan tombol `Ubah`.
  - [x] Tambahkan Box Alert Validasi Pembimbing di bawah tabel.
- [x] **Fase 3: Pembuatan & Integrasi Modal Interaktif**
  - [x] Modal `Ubah bulan`: Form edit tanggal buka, tutup, dan tanggal mulai magang.
  - [x] Modal `+ Buat bulan baru`: Form pembuatan bulan magang baru (bulan mulai, tgl buka, tgl tutup).
  - [x] Modal `+ Tambah kuota` & `Ubah Kuota Bidang`: Form alokasi kuota per jurusan dalam bidang.
- [x] **Fase 4: Pengujian & Validasi Otomatis**
  - [x] Buat file test `tests/Feature/AdminKuotaRevampTest.php` untuk memvalidasi seluruh elemen visual, data agregasi bidang, dan modal.
  - [x] Jalankan `php artisan test --filter=AdminKuotaRevampTest`.
  - [x] Jalankan regression test: `php artisan test --filter=AdminTerminologyDesignSystemTest` dan `php artisan test --filter=DashboardModulesRenderingTest`.
  - [x] Verifikasi konsistensi istilah bahwa tidak ada kata "Periode" yang tertinggal.

---

## 7. Strategi Testing

1. **Feature Test Baru (`AdminKuotaRevampTest`)**:
   - `test_kuota_page_displays_redesigned_header_and_hero_banner`: Memastikan judul `Bulan & kuota`, tombol `Ubah bulan`, `+ Buat bulan baru`, badge `BULAN AKTIF`, dan 3 parameter tanggal tampil presisi.
   - `test_kuota_page_displays_quota_per_bidang_table`: Memastikan tabel memuat baris-baris bidang dengan avatar inisial, daftar jurusan, tag jenjang, durasi `1–6 bulan`, kapasitas peserta, dan tombol `Ubah`.
   - `test_kuota_page_displays_mentor_capacity_validation_banner`: Memastikan banner informasi kapasitas pembimbing tampil dengan benar.
   - `test_kuota_modals_exist_and_functional`: Memastikan seluruh modal (`modalUbahBulan`, `modalBuatBulan`, `modalTambahKuota`, `modalEditKuota`) ter-render dengan form yang valid.
2. **Regression Testing**:
   - Memastikan seluruh pengujian otentikasi, terminologi bulan, dan pendaftaran tetap 100% hijau.

---

## 8. Risiko & Mitigasi

- **Risiko**: Bidang yang belum memiliki jurusan terikat atau belum ada kuota yang dialokasikan pada bulan aktif.
  - **Mitigasi**: Sediakan empty state anggun pada baris bidang yang belum memiliki kuota (`0 / 0 peserta` dengan tombol `Alokasikan`).
- **Risiko**: Ada jurusan yang belum dimasukkan ke pivot `bidang_jurusan`.
  - **Mitigasi**: Pastikan relasi fallback menampilkan grup jurusan secara dinamis dan tidak ada kuota jurusan yang tersembunyi.
- **Risiko**: Perbedaan format role Admin vs SuperAdmin saat mengubah bulan magang.
  - **Mitigasi**: Sediakan rute controller khusus admin yang terproteksi middleware otentikasi dan izin yang jelas.

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Judul halaman adalah `Bulan & kuota` dan tombol atas menampilkan `Ubah bulan` serta `+ Buat bulan baru`.
- [x] Tampilan Banner Hero berbentuk biru royal modern dengan badge `BULAN AKTIF`, nama bulan/gelombang, status `● Dibuka`, 3 tanggal jadwal, dan glassmorphic card kuota terisi di kanan.
- [x] Tabel kuota berformat per **BIDANG** dengan avatar inisial warna-warni, jurusan yang dibutuhkan, jenjang (`SMK`, `D3`, `S1`), durasi, kuota terisi dengan progress bar, kapasitas peserta, dan tombol `Ubah`.
- [x] Banner validasi kapasitas pembimbing tampil di bawah tabel.
- [x] Modal Ubah Bulan, Buat Bulan Baru, dan Tambah/Ubah Kuota berfungsi dengan interaktif.
- [x] Tidak ada penggunaan istilah "Periode" (selaras dengan catatan user: "tetap pakai bulan bukan periode").
- [x] Seluruh pengujian otomatis di `AdminKuotaRevampTest` dan pengujian eksisting lulus 100%.
