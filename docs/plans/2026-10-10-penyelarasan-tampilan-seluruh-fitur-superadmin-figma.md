# Plan: Penyelarasan Seluruh Tampilan Modul Fitur SuperAdmin ke Standar Layout & Desain Baru Figma

- **Tanggal**: 2026-10-10
- **Status**: Selesai (Completed & Verified)
- **Target Desain**: Figma Sleman MagKom (Node `560-26404` & `560-24892`)
- **Ruang Lingkup**: Seluruh Modul SuperAdmin di `resources/views/superadmin/*`

---

## 1. Latar Belakang & Tujuan

Seluruh fungsi logika bisnis dan aturan operasional untuk peran **SuperAdmin** telah selesai dibangun sesuai dengan dokumen spesifikasi PRD ([01-PRD-MagKom.md](file:///g:/MAGKOM/docs/01-PRD-MagKom.md)) dan telah lolos uji coba otomatis 100% (27+ feature tests). Selain itu, halaman utama SuperAdmin ([dashboard.blade.php](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php)) serta matriks RBAC ([role/index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/role/index.blade.php)) telah berhasil dimutakhirkan mengikuti acuan desain Figma Sleman MagKom (Node `560-26404` & `560-24892`).

Namun, tampilan dari modul-modul individual SuperAdmin lainnya saat ini masih menggunakan gaya antarmuka lama (inline styling `style="..."`, tata letak terfragmentasi, dan belum menggunakan komponen seragam dari layout baru).

**Tujuan Rencana Ini**:
Menyelaraskan dan memasukkan seluruh tampilan modul fitur SuperAdmin ke dalam standar layout dan design token baru ([layouts/dashboard.blade.php](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php)) agar memiliki pengalaman pengguna (UX) yang konsisten, estetika visual modern tingkat kedinasan (Kabupaten Sleman), responsif di semua ukuran layar, serta tanpa merusak satu pun fungsi bisnis atau automated test yang sudah ada.

---

## 2. Cakupan Modul & Inventarisasi File

Terdapat **11 file view** dalam **9 modul fitur SuperAdmin** yang akan diselaraskan:

| No | Modul Fitur | File View Target | Fungsi Bisnis Eksisting (Sesuai PRD) |
| :---: | :--- | :--- | :--- |
| 1 | **Master Rubrik Penilaian** | [`resources/views/superadmin/rubrik/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/rubrik/index.blade.php) | Validasi akumulasi bobot tepat 100% (AC-10-4), tambah/edit kriteria, toggle aktif/nonaktif. |
| 2 | **Master Jurusan / Pendidikan** | [`resources/views/superadmin/jurusan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/jurusan/index.blade.php) | CRUD jurusan, klasifikasi kategori (Mahasiswa/SMK), toggle status aktif. |
| 3 | **Master Bidang Penempatan** | [`resources/views/superadmin/bidang/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/bidang/index.blade.php) | CRUD bidang penempatan dinas Kominfo (Aptika, IKP, dll.), toggle status aktif. |
| 4 | **Periode & Kuota Magang** | [`resources/views/superadmin/bulan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/bulan/index.blade.php) | Buka/tutup periode bulan magang, alokasi kuota per jurusan, monitoring kuota terisi. |
| 5 | **Slot Khusus Kemitraan** | [`resources/views/superadmin/slot-khusus/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/slot-khusus/index.blade.php) | Manajemen slot kemitraan khusus (F-06), monitoring token reservasi instan & penempatan langsung. |
| 6 | **Manajemen Staf & Pengguna** | [`resources/views/superadmin/staf/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/staf/index.blade.php) | CRUD staf dinas (Admin, Pembimbing), toggle aktif, reset password dengan flag `must_change_password`. |
| 7 | **Jejak Audit Trail** | [`resources/views/superadmin/audit-log/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/audit-log/index.blade.php) | Log mutasi data strictly read-only (AC-13-6), filter multi-parameter, detail drawer JSON payload. |
| 8 | **Konfigurasi Kebijakan Sistem** | [`resources/views/superadmin/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/pengaturan/index.blade.php) | Parameter batas kuota, rasio mentee pembimbing, ambang batas predikat A-D, batas waktu logbook. |
| 9 | **Monitoring Peserta Magang** | [`resources/views/superadmin/monitoring/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/monitoring/index.blade.php)<br>[`resources/views/superadmin/monitoring/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/monitoring/show.blade.php) | Monitoring progres magang lintas bidang, penugasan, logbook harian, serta intervensi status peserta. |
| 10 | **Arsip Peserta Magang** | [`resources/views/superadmin/arsip/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/index.blade.php)<br>[`resources/views/superadmin/arsip/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/show.blade.php) | Bank data alumni magang, riwayat penilaian rubrik, transkrip nilai, sertifikat, & status arsip. |

---

## 3. Standar Desain Visual & Komponen Baru (Figma Design Token)

Seluruh modul akan mengadopsi struktur visual yang telah dibakukan pada layout utama:

```
+---------------------------------------------------------------------------------------------------+
| BREADCRUMBS: Beranda > [Kategori Modul] > [Nama Halaman]                                         |
| PAGE HEADER: Title (Bold 1.5rem) + Subtitle/Deskripsi Kebijakan        [ACTION BUTTONS: + Tambah] |
+---------------------------------------------------------------------------------------------------+
| STAT / SUMMARY CHIPS (Metrik ringkas: Total Aktif, Kapasitas, Status Kepatuhan 100%)              |
+---------------------------------------------------------------------------------------------------+
| TOOLBAR & FILTER BAR CARD (Search input, Select filter, Submit & Reset filter)                    |
+---------------------------------------------------------------------------------------------------+
| DATA TABLE CONTAINER                                                                              |
| - Header: #F8FAFC, Typography Open Sans, Bold 0.75rem Uppercase                                   |
| - Row: Hover subtle transition, clean padding 1rem                                               |
| - Badges: Sleman Pill Tokens (Emerald=Aktif, Amber=Pending/Cadangan, Red=Nonaktif, Blue=Proses)   |
| - Actions: Grouped action buttons (Detail/Edit/Toggle/Reset)                                      |
| - Pagination: Sleman clean pagination nav                                                         |
+---------------------------------------------------------------------------------------------------+
| MODAL FORM DIALOG (Rounded-12, Backdrop blur, Header with Icon Badge, Clean Form Inputs, Buttons) |
+---------------------------------------------------------------------------------------------------+
```

### Rincian Standar Komponen:
1. **Header & Action Bar**:
   - Memanfaatkan `@section('page_actions')` untuk menempatkan tombol utama (seperti `+ Tambah Data`, `Export CSV`, `Simpan Konfigurasi`) di sisi kanan header halaman secara konsisten.
   - Breadcrumb rapi dengan pemisah SVG chevron.
2. **Stat Indicator Chips**:
   - Untuk modul dengan ambang batas (seperti Rubrik 100%, Kuota Bulan, Slot Khusus, Akun Staf), sertakan kartu indikator ringkas di atas tabel dengan border tipis dan aksen warna penanda status.
3. **Filter Bar & Search**:
   - Menggunakan form bar berkontur halus dengan background `var(--neutral-0)`, input dengan `focus:ring` biru Sleman (`var(--blue-500)`), tombol `Terapkan Filter` dan link `Reset`.
4. **Tabel Data Modern**:
   - Menggantikan tabel inline lama dengan styling terstruktur (`.table-container`, `.table-modern`).
   - Badge status seragam menggunakan pill rounded-full dengan typography tebal 0.75rem.
5. **Modal Form & Drawer**:
   - Struktur dialog modal yang konsisten: backdrop semi-transparan `rgba(15, 23, 42, 0.45)` dengan `backdrop-filter: blur(4px)`, header dengan icon bulat aksen, input text/select rounded-8, helper text, dan tombol batal/simpan yang tegas.

---

## 4. Rencana Kerja Bertahap (4 Batch Implementasi)

Pekerjaan dikelompokkan ke dalam 4 Batch terstruktur untuk memastikan validitas dan kemudahan peninjauan:

### Batch 1: Master Data Inti (Rubrik, Jurusan, Bidang)
- **Modul Rubrik Penilaian** ([rubrik/index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/rubrik/index.blade.php)):
  - Pindahkan tombol "+ Tambah Kriteria Baru" ke `@section('page_actions')`.
  - Desain ulang kartu validasi bobot 100% (AC-10-4) menjadi status banner modern dengan progress/pill badge hijau/merah.
  - Perbarui tabel data kriteria, bobot persen, dan tombol aksi (Edit & Toggle Status).
  - Percantik Modal Tambah dan Modal Edit kriteria rubrik dengan backdrop blur dan validasi input.
- **Modul Master Jurusan** ([jurusan/index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/jurusan/index.blade.php)):
  - Tombol "+ Tambah Jurusan" diposisikan di `@section('page_actions')`.
  - Filter bar pencarian nama jurusan dan filter kategori (Mahasiswa vs SMK).
  - Tabel modern dengan badge kategori dan toggle status aktif.
  - Modal form create/edit jurusan.
- **Modul Master Bidang Penempatan** ([bidang/index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/bidang/index.blade.php)):
  - Tombol "+ Tambah Bidang" di `@section('page_actions')`.
  - Tabel daftar bidang penempatan dinas, jumlah pembimbing terhubung, deskripsi tugas, dan toggle aktif.
  - Modal tambah & edit bidang.

### Batch 2: Operasional Waktu & Kuota Khusus (Bulan Magang & Slot Khusus)
- **Modul Periode Bulan Magang** ([bulan/index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/bulan/index.blade.php)):
  - Header action: "+ Buka Periode Baru".
  - Kartu ringkasan periode aktif vs periode ditutup.
  - Tabel periode magang, tanggal buka-tutup pendaftaran, alokasi total kuota, kuota terisi, dan tombol kelola detail kuota jurusan.
  - Modal buka periode baru dan modal alokasi kuota per jurusan.
- **Modul Slot Khusus Kemitraan** ([slot-khusus/index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/slot-khusus/index.blade.php)):
  - Header action: "+ Alokasikan Slot Khusus".
  - Kartu indikator: Total Slot Terbit, Terpakai, dan Tersedia.
  - Tabel slot khusus dengan badge instansi mitra, token akses, periode bulan, status pemakaian, dan aksi pembatalan/salin token.
  - Modal pembuatan slot khusus baru.

### Batch 3: Pengguna & Keamanan Sistem (Staf & Jejak Audit Trail)
- **Modul Manajemen Staf & Pengguna** ([staf/index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/staf/index.blade.php)):
  - Header action: "+ Tambah Akun Staf".
  - Filter bar: filter role (Admin, Pembimbing, SuperAdmin), status aktif/nonaktif, dan pencarian nama/email.
  - Tabel pengguna internal dengan badge role warna Sleman, bidang penempatan, status login, dan tombol aksi: Edit Profil, Toggle Status, dan Reset Password.
  - Modal Tambah Staf Baru dan Modal Konfirmasi Reset Password (dengan instruksi pergantian sandi awal).
- **Modul Jejak Audit Trail** ([audit-log/index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/audit-log/index.blade.php)):
  - Filter bar lengkap yang rapi dan responsif: filter kata kunci, tipe log (login, aksi, ekspor), aktor staf, entitas tabel, request ID, dan rentang tanggal.
  - Tabel log interaktif dengan badge tipe aksi warna-warni, timestamp WIB, IP address, dan tombol "Detail Log".
  - Drawer / Modal Pratinjau JSON Payload yang terformat rapi dengan syntax highlighting atau code block yang mudah dibaca.

### Batch 4: Tata Kelola Kebijakan & Manajemen Peserta (Pengaturan, Monitoring, Arsip)
- **Modul Pengaturan Kebijakan Sistem** ([pengaturan/index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/pengaturan/index.blade.php)):
  - Reorganisasi form pengaturan ke dalam kartu segmented berkelas:
    1. *Kebijakan Kuota & Kapasitas Magang* (Batas umum per bulan, slot khusus default, beban mentee per pembimbing).
    2. *Aturan Pelaksanaan & Logbook* (Batas jam submit logbook harian, toleransi izin/sakit).
    3. *Ambang Batas Predikat Penilaian* (Input batas nilai huruf A, B, C, D sesuai PRD 8.6).
  - Floating save action bar atau tombol simpan yang elegan di header `@section('page_actions')`.
- **Modul Monitoring Peserta Magang** ([monitoring/index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/monitoring/index.blade.php) & [`show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/monitoring/show.blade.php)):
  - Index: Filter bidang penempatan, pembimbing, dan status penempatan. Tabel progres logbook dan rekap penugasan.
  - Detail Show: Profil lengkap peserta, timeline kegiatan harian, rekap tugas dan nilai sementara, serta tombol intervensi administratif.
- **Modul Arsip Peserta Magang** ([arsip/index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/arsip/index.blade.php) & [`show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/show.blade.php)):
  - Index: Filter tahun lulus, jurusan, institusi. Tabel arsip alumni dengan predikat akhir dan nomor sertifikat.
  - Detail Show: Halaman arsip baca-saja elegan dengan lembar evaluasi rubrik, profil alumni, dan tautan unduh berkas final.

---

## 5. Strategi Pengujian & Jaminan Bebas Regresi (Zero Regression)

Setiap langkah penyelarasan UI **wajib mempertahankan 100% fungsionalitas backend dan kontrak antarmuka**:
1. **Atribut Form & JavaScript**:
   - Seluruh tag form action (`route('superadmin.*')`), method (`POST`, `PUT`, `PATCH`), `@csrf`, `@method`, dan nama input (`name="..."`) dipertahankan tanpa perubahan.
   - Fungsi JS untuk modal (`openModal...`, `closeModal...`, dsb.) dipertahankan agar interaksi tombol tidak putus.
2. **Automated Test Validation**:
   Setelah setiap batch diterapkan, rangkaian test suite berikut akan dijalankan secara otomatis:
   - `tests/Feature/DashboardModulesRenderingTest.php`
   - `tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`
   - `tests/Feature/SuperAdminFigmaDesignAlignmentTest.php`
   - `tests/Feature/SuperAdminDashboardAndAuditLogTest.php`
   - `tests/Feature/AdminLaporanDanArsipTest.php`
   - `tests/Feature/AdminSuperAdminPhaseTwoTest.php`
   - `tests/Feature/AdminSuperAdminPhaseThreeTest.php`
   - `tests/Feature/AdminSuperAdminPhaseFourTest.php`
3. **Browser Visual Inspection**:
   - Menggunakan `browser_subagent` untuk mengambil screenshot halaman-halaman yang dimodifikasi guna memverifikasi rendering di browser nyata (resolusi 1920x1080 dan mobile viewport).

---

## 6. Analisis Risiko & Mitigasi

| Potensi Risiko | Tingkat Dampak | Strategi Mitigasi |
| :--- | :---: | :--- |
| **Kerusakan Trigger Modal JavaScript** | Sedang | Tetap menggunakan ID modal eksisting (`modalTambahRubrik`, `modalEditRubrik`, dll.) dan fungsi pembuka JS yang sama persis, hanya memodernisasi styling CSS modal dan layout strukturnya. |
| **Gagal Render Komponen saat Data Kosong** | Rendah | Menggunakan empty-state container khusus berdesain Figma dengan ilustrasi icon SVG informatif untuk setiap tabel saat `$items->isEmpty()`. |
| **Regresi Assertion String pada Unit Test** | Sedang | Memastikan teks-teks kunci yang diperiksa di PHPUnit (misalnya `Total Bobot Aktif`, `Aturan Validasi Integritas Rubrik`, `Parameter Konfigurasi Sistem`, dsb.) tetap ada di dalam template Blade. |

---

## 7. Kriteria Selesai (Definition of Done)

1. [x] Seluruh 11 file view modul SuperAdmin di `resources/views/superadmin/` telah dimutakhirkan menggunakan token desain Sleman dan standar layout baru.
2. [x] Tidak ada lagi inline style usang yang tidak konsisten; seluruh form, tabel, kartu statistik, badge, dan modal terlihat elegan dan profesional.
3. [x] 100% automated test suite SuperAdmin (56 feature tests, 370 assertions) lulus tanpa ada kegagalan (`0 failures, 0 errors`).
4. [x] Verifikasi browser membuktikan tampilan rapi, bersih, dan nyaman digunakan oleh SuperAdmin dinas.
