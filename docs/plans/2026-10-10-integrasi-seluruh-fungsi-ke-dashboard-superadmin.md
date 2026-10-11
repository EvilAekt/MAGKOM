# Plan Roadmap: Integrasi Seluruh Fungsi Tata Kelola & Operasional ke Dashboard SuperAdmin

- **Tanggal**: 2026-10-10
- **Status**: Completed (Selesai Diimplementasikan & Lolos Uji)
- **Author**: Antigravity (web-implementation)
- **Rujukan Desain**:
  - [Figma Node 560-26404 (SA-00 Dashboard SuperAdmin)](https://www.figma.com/design/uKH1OZjwBIi8iRoKQXEbQr/MAGKOM-Kominfo-Sleman?node-id=560-26404&t=lO2UnW9u7qtM1mDq-4)
  - [Figma Node 560-24892 (Komponen SuperAdmin & Sidebar Grouping)](https://www.figma.com/design/uKH1OZjwBIi8iRoKQXEbQr/MAGKOM-Kominfo-Sleman?node-id=560-24892&t=lO2UnW9u7qtM1mDq-4)
- **Rujukan Fungsional & PRD**:
  - PRD Bab 3.1 & 3.3 (Hak Akses & Kapabilitas SuperAdmin)
  - PRD Bab 4 (Alur Pendaftaran, Kuota, & Verifikasi F-04, F-05)
  - PRD Bab 5 (Penempatan & Penunjukan Pembimbing F-06, F-07)
  - PRD Bab 6 & 7 (Pelaksanaan, Tugas & Logbook F-08, F-09)
  - PRD Bab 8 & 9 (Penilaian, Konfigurasi, Audit Trail & Laporan F-10 s/d F-17)

---

## 1. Tujuan

Mengintegrasikan **seluruh kapabilitas operasional dan kontrol sistem MagKom** yang telah dibangun pada sprint-sprint sebelumnya ke dalam tampilan **Dashboard SuperAdmin baru**, sehingga SuperAdmin memiliki satu pusat kendali (*Unified Command Center*) yang komprehensif, intuitif, dan nyaman digunakan, tanpa merusak atau mengubah estetika visual bersih dari desain resmi Figma Node `560-26404`.

Hasil akhir yang diharapkan:
1. SuperAdmin dapat memantau dan mengeksekusi tindakan langsung (*Quick Actions & Direct Control*) dari Dashboard untuk seluruh siklus magang:
   - **Administrasi & Seleksi**: Verifikasi berkas tertunda, alokasi kuota per jurusan, antrean waiting list, dan penempatan peserta.
   - **Bimbingan & Pelaksanaan**: Utilisasi beban pembimbing lapangan (BR-13), progres tugas, serta reviu logbook harian.
   - **Tata Kelola Sistem & Keamanan**: Tambah staf baru, parameter batas sistem, matriks hak akses, ringkasan audit log, dan ekspor instan 4 jenis laporan resmi dinas.
2. Pengorganisasian informasi menggunakan **Segmented Tab Controller** yang modern (*Ringkasan Eksekutif*, *Operasional Seleksi*, *Pelaksanaan Bimbingan*, dan *Tata Kelola Sistem*) agar halaman tetap rapi, cepat diakses, dan tidak terjadi *information overload*.

---

## 2. Asumsi & Pertanyaan Terbuka

### 2.1. Asumsi Teknis & Fungsional
1. **Hierarki Akses SuperAdmin (PRD 3.3)**: SuperAdmin mewarisi hak akses administratif penuh atas semua modul Administrasi dan Pembimbing, sehingga menampilkan ringkasan operasional di dashboard SuperAdmin sepenuhnya sesuai dengan spesifikasi PRD.
2. **Zero Breaking Changes**: Integrasi ini murni pengayaan antarmuka dashboard dan orkestrasi kontrol di level view & controller, tanpa mengubah schema database atau merusak controller modul masing-masing.
3. **Performa & Query Optimization**: Semua query relasi menggunakan *eager loading* (`with()`), agregasi database, serta pagination/limit (maksimal 5 item per feed) agar TTFB (Time to First Byte) tetap berada di bawah 200ms.
4. **Interaktivitas Vanilla JS**: Perpindahan antar tab operasional dan interaksi modal berjalan mulus berbasis Vanilla JavaScript tanpa ketergantungan framework frontend tambahan.

### 2.2. Pertanyaan Terbuka
- Tidak ada pertanyaan terbuka yang memblokir. Rencana disusun fleksibel dan modular sehingga tab tambahan dapat langsung digunakan setelah disetujui.

---

## 3. Pendekatan Desain & Arsitektur

### 3.1. Perbandingan Opsi Pendekatan Teknis

| Parameter | Opsi A: Satu Halaman Panjang Bertumpuk (Flat Stack) | Opsi B: Segmented Tab Controller + Quick Action Header (Rekomendasi) |
| :--- | :--- | :--- |
| **Kerapian & Kepatuhan Figma** | ❌ Halaman menjadi sangat panjang ke bawah (scroll > 3000px), merusak hierarki visual Figma Node `560-26404`. | ✅ **Mempertahankan tampilan Figma Node 560-26404 pada tab pertama**, sementara modul fungsi lain rapi di tab masing-masing. |
| **Kenyamanan Pengguna (UX)** | ⚠️ Melelahkan mata; SuperAdmin harus scrolling jauh untuk menemukan fungsi tertentu. | ✅ **Sangat nyaman dan intuitif**; navigasi berbasis konteks kerja (Ringkasan, Seleksi, Bimbingan, Sistem). |
| **Kecepatan Tindakan (Actionability)** | ⚠️ Tombol aksi tersembunyi di bagian tengah dan bawah tabel. | ✅ Dilengkapi **Quick Actions Bar** di bagian atas untuk aksi rutin dinas (1-klik akses). |
| **Performa Rendering** | ⚠️ Browser me-render semua DOM sekaligus. | ✅ DOM terstruktur rapi, tab switching instan di sisi klien. |

> **Rekomendasi**: Memilih **Opsi B**. Opsi ini mempertahankan tampilan Figma Node `560-26404` secara default pada tab awal, sekaligus mengintegrasikan seluruh fungsi sistem lainnya ke dalam tab bertema fungsional yang elegan.

---

### 3.2. Struktur Visual Dashboard Terpadu

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ TOPBAR: Breadcrumb (SuperAdmin / Dashboard) + Profil Akun                              │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ HERO SAPAAN EKSEKUTIF (Figma 560-26404)                                                │
│ • Selamat datang, SuperAdmin Kominfo                                                   │
│ • QUICK ACTIONS BAR (Pintasan 1-Klik):                                                 │
│   [+ Akun Staf]  [+ Periode Magang]  [Tinjau Berkas]  [Penempatan]  [Ekspor Laporan]  │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 4 KARTU STATISTIK FIGMA (Clickable Shortcuts):                                         │
│ ┌────────────────┐ ┌────────────────┐ ┌────────────────┐ ┌────────────────┐          │
│ │ Total pengguna │ │ Menunggu verif │ │ Peserta aktif  │ │ Akun nonaktif  │          │
│ │   [users]      │ │    [inbox]     │ │ [clipboard]    │ │    [lock]      │          │
│ └────────────────┘ └────────────────┘ └────────────────┘ └────────────────┘          │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ SEGMENTED TAB CONTROLLER:                                                              │
│  [★ Ringkasan Utama]   [📋 Seleksi & Kuota]   [👥 Bimbingan]   [⚙️ Tata Kelola Sistem]│
├────────────────────────────────────────────────────────────────────────────────────────┤
│ KONTEN TAB:                                                                            │
│                                                                                        │
│ [TAB 1: RINGKASAN UTAMA (Figma Default Node 560-26404)]                                │
│ ├── Kolom Kiri: Pengguna terbaru (5 akun terkini + avatar pastel + role badges)        │
│ └── Kolom Kanan: Audit log terbaru (5 mutasi database + stempel waktu + staf aktor)    │
│                                                                                        │
│ [TAB 2: SELEKSI & OPERASIONAL KUOTA]                                                   │
│ ├── Antrean Berkas Pendaftaran Diajukan (Tabel 5 berkas tertunda + aksi verifikasi)   │
│ ├── Utilisasi Kuota per Jurusan (Progress meter kuota terisi vs daya tampung)         │
│ ├── Status Waiting List FIFO (Pendaftar menunggu kuota buka)                          │
│ └── Slot Khusus Kemitraan (Alokasi MoU institusi mitra)                                │
│                                                                                        │
│ [TAB 3: PELAKSANAAN & BIMBINGAN]                                                       │
│ ├── Utilisasi Beban Pembimbing Lapangan (Batas mentee BR-13, peringatan overload)      │
│ ├── Monitoring Tugas Berjalan (Kategori tugas, progres pengerjaan)                     │
│ ├── Kepatuhan Logbook Harian (Status review pembimbing, batas mundur BR-09)            │
│ └── Rekap Nilai & Sertifikasi (Status finalisasi nilai, grace period unduh BR-12)      │
│                                                                                        │
│ [TAB 4: TATA KELOLA SISTEM & LAPORAN]                                                  │
│ ├── Hub Ekspor Laporan Resmi Dinas 1-Klik (Pendaftaran, Penempatan, Kuota, Nilai)     │
│ ├── Konfigurasi Parameter Kunci (Batas Beban BR-13, Logbook BR-09, Grace Period)       │
│ ├── Status Kalender Bulan Magang (Override ketersediaan & jadwal buka/tutup)           │
│ └── Matriks Ringkas Hak Akses 4 Peran & Rekam Audit Log                                │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`app/Http/Controllers/SuperAdmin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/DashboardController.php) | Modifikasi | Menambahkan query komprehensif: antrean verifikasi pendaftaran tertunda (`pendaftaranMenunggu`), rincian kuota per jurusan bulan aktif (`kuotaList`), ringkasan antrean waiting list, metrik logbook/tugas bimbingan, dan parameter konfigurasi aktif. |
| [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php) | Modifikasi | Mengimplementasikan Segmented Tab Controller, Quick Action Toolbar di hero section, tab Seleksi & Kuota, tab Pelaksanaan Bimbingan, dan tab Tata Kelola Sistem terintegrasi dengan estetika token Sleman. |
| [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) | Modifikasi | Memastikan styling CSS tab controller (`.dashboard-tabs`, `.tab-btn`, `.tab-pane`) terintegrasi rapi ke layout global MagKom. |
| [`tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php) | Pembuatan | Test suite komprehensif untuk memvalidasi seluruh fungsi yang diintegrasikan: Quick actions, tab switching, antrean verifikasi, kuota per jurusan, beban pembimbing, dan ekspor laporan dari dashboard. |

---

## 5. Rencana Langkah Implementasi (Roadmap)

### Fase 1: Persiapan Backend Controller & Data Aggregation
- [x] **Langkah 1.1**: Perbarui [`app/Http/Controllers/SuperAdmin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/DashboardController.php):
  - Query pendaftaran tertunda (`pendaftaranMenunggu`): 5 berkas dengan status `Diajukan` dan `PerluPerbaikan` terurut FIFO (`diajukan_at asc`).
  - Query kuota jurusan bulan aktif (`kuotaList`): daftar alokasi kuota per jurusan beserta persentase keterisian.
  - Query metrik bimbingan: total logbook belum direviu, total tugas aktif berjalan, dan status penilaian selesai.
  - Query parameter sistem aktif: ambil nilai konfigurasi kunci (`maks_peserta_aktif_per_pembimbing`, `batas_hari_logbook_mundur`, `masa_tenggang_unduh_nilai_hari`).
  - Kirim seluruh variabel ke view `superadmin.dashboard`.

### Fase 2: Penambahan CSS Tab Controller & Quick Action Toolbar di Layout
- [x] **Langkah 2.1**: Tambahkan styling token CSS untuk komponen Tab & Toolbar di [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php):
  - `.dashboard-tab-bar`: bar navigasi tab modern bergaya segmented pill.
  - `.tab-btn`: tombol tab dengan state aktif, icon, dan counter badge.
  - `.tab-content-panel`: transisi halus saat tab berganti (*fade in*).
  - `.quick-action-chip`: tombol aksi cepat dengan icon aksen dinas.

### Fase 3: Integrasi Seluruh Modul Fungsi ke View Dashboard SuperAdmin
- [x] **Langkah 3.1**: Perbarui Hero Section di [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php):
  - Tambahkan Quick Actions Toolbar:
    - Tombol `+ Akun Staf` &rarr; mengarah ke form tambah staf di `superadmin.staf.index`.
    - Tombol `+ Periode Magang` &rarr; modal/link ke `superadmin.bulan.index`.
    - Tombol `Verifikasi Berkas` &rarr; link langsung ke antrean `admin.pendaftaran.index`.
    - Tombol `Alokasi Kuota` &rarr; link ke `admin.kuota.index`.
    - Tombol `Ekspor Laporan` &rarr; dropdown/link ke `admin.laporan.index`.
- [x] **Langkah 3.2**: Terapkan **Tab 1: Ringkasan Utama (Figma SA-00)**:
  - Pertahankan grid dua kolom Figma: *Pengguna terbaru* dan *Audit log terbaru*.
- [x] **Langkah 3.3**: Terapkan **Tab 2: Seleksi & Operasional Kuota**:
  - Mini-tabel Antrean Verifikasi Berkas (nama peserta, institusi, jurusan, waktu ajukan, tombol aksi cepat *"Periksa & Putuskan"*).
  - Grid Kuota per Jurusan bulan berjalan dengan meter visual (terisi / kapasitas).
  - Kartu Antrean Waiting List & Slot Khusus Kemitraan Sleman.
- [x] **Langkah 3.4**: Terapkan **Tab 3: Pelaksanaan & Bimbingan**:
  - Matriks Beban Pembimbing Lapangan (BR-13) dengan indikator batas aman / peringatan *overload*.
  - Ringkasan Kepatuhan Logbook & Tugas Mentee.
  - Ringkasan Penilaian Akhir & Pengunduhan Lembar Nilai PDF.
- [x] **Langkah 3.5**: Terapkan **Tab 4: Tata Kelola Sistem & Kepatuhan**:
  - Hub Ekspor 1-Klik 4 Laporan Resmi Dinas (Pendaftaran, Penempatan, Kuota, Rekap Nilai).
  - Kartu Ringkasan Parameter Sistem (Nilai ambang predikat, toleransi logbook, masa tenggang akun).
  - Kalender Status Bulan Magang (Bulan mulai, tanggal buka, tanggal tutup, status override).
- [x] **Langkah 3.6**: Tambahkan script interaktivitas Vanilla JS `switchDashboardTab(tabId)` untuk perpindahan tab instan dan persistensi state tab via `localStorage`.

### Fase 4: Automated Testing & Verifikasi Regresi
- [x] **Langkah 4.1**: Buat test suite baru [`tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php):
  - Uji seluruh variabel data dan fungsi baru yang terpasang di dashboard SuperAdmin.
  - Uji rendering tab seleksi & verifikasi antrean.
  - Uji rendering tab bimbingan & beban pembimbing.
  - Uji rendering tab tata kelola & link ekspor laporan.
- [x] **Langkah 4.2**: Jalankan pengujian regresi penuh via `php artisan test` untuk memastikan 100% test lulus hijau.

---

## 6. Strategi Testing

1. **Automated Feature Testing**:
   - `SuperAdminUnifiedDashboardFeaturesTest`: Memastikan semua data dari modul seleksi, kuota, pembimbing, dan sistem ter-pass dan ter-render dengan benar.
   - `SuperAdminFigmaDesignAlignmentTest`: Memastikan estetika dan token Figma Node 560-26404 tetap terjaga 100%.
   - `SuperAdminDashboardAndAuditLogTest`: Memastikan jejak audit log dan akses eksekutif tetap valid.
   - `DashboardModulesRenderingTest`: Memastikan rendering seluruh modul tidak mengalami regresi.
2. **Visual & Responsive Testing**:
   - Desktop 1920x1080 & 1440x900: Memastikan layout tab dan grid proporsional.
   - Mobile (<1024px): Memastikan tab controller dan tabel pendaftaran dapat di-scroll secara horizontal tanpa merusak layout.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Strategi Mitigasi |
| :--- | :--- | :--- |
| **Beban Query Database Meningkat** | Response time melambat jika data pendaftaran/kuota besar | Gunakan agregasi langsung di database (`count()`, `sum()`) dan batasi daftar pendaftaran serta log audit maksimal 5 baris dengan index kolom waktu. |
| **Kekacauan Tampilan (Clutter)** | Tampilan menjadi terlalu padat dan membingungkan | Gunakan Segmented Tabs sehingga SuperAdmin hanya melihat modul yang relevan dengan fokus kerjanya saat itu. |
| **Duplikasi Logika Bisnis** | Logika keputusan berkas tersebar | Dashboard hanya menyediakan tombol pintasan navigasi cepat ke detail aksi resmi; seluruh validasi bisnis tetap terpusat di Controller dan Service yang sudah teruji. |

---

## 8. Kriteria Selesai (Definition of Done)

1. Dashboard SuperAdmin memuat seluruh fungsi yang telah dibuat (Seleksi, Kuota, Penempatan, Bimbingan, Evaluasi, Pengguna, Parameter Sistem, dan Laporan).
2. Tampilan default dashboard tetap presisi dan selaras 100% dengan mockup Figma Node `560-26404`.
3. Navigasi antar modul fungsi menggunakan Segmented Tabs yang responsif dan nyaman digunakan.
4. Quick Actions Toolbar berfungsi mengarahkan aksi penting dalam 1 klik.
5. Seluruh automated test suite (test baru dan tes eksisting) berstatus **100% PASS (Green)**.
