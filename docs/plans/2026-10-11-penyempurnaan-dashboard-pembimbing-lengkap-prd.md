# Plan: Penyempurnaan Tampilan & Fitur Dashboard Operasional Pembimbing Sesuai PRD, TDD, dan Figma

- **Tanggal**: 2026-10-11
- **Status**: Selesai (Completed)
- **Author**: Antigravity (web-implementation)
- **Target Halaman**: `/pembimbing/dashboard` ([`resources/views/pembimbing/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/dashboard.blade.php)) & [`app/Http/Controllers/Pembimbing/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/DashboardController.php)

---

## 1. Tujuan

Menyempurnakan dan melengkapi tampilan serta fungsionalitas **Dashboard Operasional Pembimbing Lapangan** agar 100% selaras dengan seluruh aturan bisnis dan kebutuhan operasional di **PRD** (`01-PRD-MagKom.md`), **TDD** (`02-TDD-MagKom.md`), dan standar visual modern **Figma** (Node 560-41611, Sleman Blue `#0641CC`). 

Berdasarkan audit komparatif terhadap PRD dan implementasi saat ini, ditemukan beberapa kekurangan dan celah operasional yang perlu ditambahkan:
1. **Quick Action Center (Aksi Cepat Langsung di Header/Hero)**: Tombol aksi instan untuk `+ Buat Tugas Baru` dan `Tinjau Logbook Hari Ini` langsung dari dashboard tanpa harus membuka modul lain.
2. **Upcoming Deadline & Overdue Timeline Tracker (F-08, BR-16)**: Visualisasi widget tugas yang mendekati tenggat waktu ($\le 3$ hari, sesuai setting `deadline_mendekati_hari`) dan tugas overdue agar pembimbing dapat melakukan intervensi proaktif.
3. **Logbook Compliance & Inactivity Alert (BR-09)**: Peringatan dini jika ada mentee aktif yang belum mengisi logbook selama $\ge 2$ hari kerja, mencegah mentee melewati batas maksimal mundur 3 hari (`logbook_maks_mundur_hari`).
4. **Distribusi Jenjang & Asal Institusi Mentee**: Visualisasi ringkas demografi mentee (SMK vs D3/S1 dan top asal sekolah/kampus) untuk mendukung pemantauan proporsionalitas bimbingan.
5. **Perbaikan Bug Audit Trail Revisi Nilai di Tab 4**: Sinkronisasi kolom timestamp (`waktu`) dan ekstraksi JSON nilai akhir pada `PenilaianRiwayat` agar tidak terjadi error atau tampilan nilai 0.00.
6. **Live Filter & Search di Tab 2 (Antrean) & Tab 3 (Monitoring Mentee)**: Input filter interaktif instan untuk memudahkan pembimbing mencari nama mentee secara real-time pada tabel.

---

## 2. Asumsi & Pertanyaan Terbuka

- **Asumsi**:
  1. Penambahan widget baru tidak mengubah skema tabel database (zero-migration); semua data dihitung menggunakan kueri Eloquent terindeks yang efisien di `DashboardController`.
  2. Batas peringatan tugas mendekati tenggat waktu mengacu pada konfigurasi `Pengaturan::where('kunci', 'deadline_mendekati_hari')->value('nilai')` (default 3 hari).
  3. Batas toleransi keterlambatan logbook adalah 2 hari sebelum hangus di hari ke-3 (`logbook_maks_mundur_hari = 3`).
- **Pertanyaan Terbuka**: Tidak ada ambiguitas arsitektur kritis; semua kebutuhan telah terdefinisi secara presisi di PRD bab 7, 8, dan 9.

---

## 3. Pendekatan Desain & Arsitektur

### Perbandingan Pendekatan:

| Kriteria Evaluasi | Opsi A: Tampilan Monolitik (1 Halaman Memanjang) | Opsi B: Tabbed Operational Workspace + Contextual Insights (Rekomendasi) |
| :--- | :--- | :--- |
| **Beban Kognitif Pembimbing** | Berat: Banyak scroll vertikal ke bawah, sulit membedakan prioritas tindakan darurat vs laporan analitik. | **Sangat Ringan**: 4 tab tersegregasi rapi; Tab 1 fokus pada tindakan hari ini (Actionable Insights), Tab 2 untuk antrean kerja, Tab 3 untuk riwayat mentee, Tab 4 untuk penilaian kelulusan. |
| **Integrasi Fitur Baru** | Widget baru ditumpuk di bawah, berisiko terlewatkan oleh pembimbing. | **Konteks Tepat**: Widget upcoming deadline & logbook compliance disematkan langsung di Tab 1 (Ringkasan Utama) sebagai *Command Center*. |
| **Konsistensi Desain Figma** | Kurang rapi jika terlalu panjang. | **100% Selaras Figma**: Tetap mempertahankan header Sleman Blue gradient, kartu metrik modern, dan micro-interactions responsif. |

> **Keputusan**: Memilih **Opsi B** dengan memperkaya Tab 1 (Ringkasan Utama) dengan **Quick Action Buttons**, **Upcoming Deadline Bar**, dan **Logbook Compliance Warning Banner**, serta menyempurnakan Tab 2, 3, dan 4 dengan fitur live search dan perbaikan audit trail.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`app/Http/Controllers/Pembimbing/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/DashboardController.php) | Modify | 1. Hitung daftar tugas mendekati deadline ($\le 3$ hari) dan overdue (BR-16).<br>2. Hitung deteksi mentee aktif yang tidak mengisi logbook $\ge 2$ hari terakhir (BR-09).<br>3. Hitung ringkasan proporsi jenjang mentee (SMK, D3, S1) dan bidang.<br>4. Perbaiki eager loading relasi jurusan dan riwayat nilai. |
| [`resources/views/pembimbing/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/dashboard.blade.php) | Modify | 1. **Hero Header**: Tambahkan tombol Quick Action (`+ Buat Tugas Baru` & `Reviu Logbook Hari Ini`).<br>2. **Tab 1**: Tambahkan widget *Upcoming Deadlines Tracker* & *Logbook Compliance Alert Banner*.<br>3. **Tab 1**: Tambahkan mini chart / pill distribusi jenjang mentee.<br>4. **Tab 2 & 3**: Tambahkan client-side live search filter pada tabel antrean dan mentee.<br>5. **Tab 4**: Perbaiki rendering audit trail revisi nilai (`$rev->waktu` dan snapshot JSON). |
| [`tests/Feature/PembimbingDashboardEnhancementTest.php`](file:///g:/MAGKOM/tests/Feature/PembimbingDashboardEnhancementTest.php) | Modify | Tambahkan pengujian assertion untuk data baru: `upcomingDeadlines`, `logbookInactivityAlerts`, dan `jenjangDistribution`. |

---

## 5. Langkah Implementasi

### Fase 1: Backend Controller (`DashboardController.php`)
- [x] **Langkah 1.1**: Ambil setting `deadline_mendekati_hari` dari tabel `pengaturan` (fallback 3 hari). Kueri tugas aktif yang memiliki deadline antara hari ini s/d $N$ hari ke depan (`upcomingDeadlines`), diurutkan dari deadline terdekat.
- [x] **Langkah 1.2**: Implementasikan logika deteksi kedisiplinan logbook: cek mentee aktif yang tanggal logbook terakhirnya $< \text{hari ini} - 2\text{ hari}$ atau belum pernah mengisi logbook sama sekali sejak magang dimulai $\ge 2$ hari yang lalu (`inactiveLogbookMentees`).
- [x] **Langkah 1.3**: Hitung distribusi jenjang mentee aktif (`jenjangCounts`: jumlah SMK, D3, S1).
- [x] **Langkah 1.4**: Teruskan variabel baru (`upcomingDeadlines`, `inactiveLogbookMentees`, `jenjangCounts`, `deadlineMendekatiHari`) ke view `pembimbing.dashboard`.

### Fase 2: Antarmuka Hero & Quick Action (`dashboard.blade.php`)
- [x] **Langkah 2.1**: Pada banner Hero Sleman Blue, tambahkan baris tombol aksi cepat:
  - Tombol primer putih: `+ Buat Tugas Baru` (membuka modal pembuatan tugas langsung atau pintasan ke `/pembimbing/tugas`).
  - Tombol sekunder transparan-blur: `Reviu Antrean Hari Ini` (lompat ke Tab 2 dengan badge counter).
  - Tombol tersier: `Input Penilaian` (lompat ke Tab 4).

### Fase 3: Widget Tambahan Tab 1 (Command Center)
- [x] **Langkah 3.1**: Tambahkan banner **"Peringatan Kedisiplinan Logbook"** jika ada mentee yang tidak mengisi logbook $> 2$ hari, lengkap dengan nama mentee dan tombol tegur/ingatkan.
- [x] **Langkah 3.2**: Tambahkan panel **"Tenggat Waktu Tugas Mendatang (Upcoming Deadlines)"** yang menampilkan daftar tugas jatuh tempo dalam 3 hari ke depan dengan countdown sisa hari dan badge kategori tugas.
- [x] **Langkah 3.3**: Tambahkan widget ringkas **"Komposisi Mentee Bimbingan"** yang menunjukkan persentase jenjang (SMK vs Perguruan Tinggi).

### Fase 4: Peningkatan Tab 2, 3, dan 4
- [x] **Langkah 4.1**: Tambahkan kotak input pencarian instan (*real-time client-side filter*) di atas tabel antrean logbook & progres tugas (Tab 2) dan direktori mentee 360° (Tab 3).
- [x] **Langkah 4.2**: Perbaiki tabel **Jejak Audit Revisi Nilai Final (AC-10-3)** di Tab 4:
  - Ganti `$rev->created_at` menjadi `$rev->waktu?->format('d/m/Y H:i')`.
  - Ekstrak nilai akhir dari array JSON `nilai_lama` dan `nilai_baru` dengan aman (`$rev->nilai_lama['nilai_akhir'] ?? '-'`).

### Fase 5: Pengujian Otomatis & Verifikasi Visual
- [x] **Langkah 5.1**: Perbarui dan jalankan `tests/Feature/PembimbingDashboardEnhancementTest.php` untuk memastikan semua variabel dan view ter-render tanpa regresi.
- [x] **Langkah 5.2**: Jalankan seluruh 5 test suite fitur pembimbing (`PembimbingFullJobDeskTest`, `PembimbingPortalFeatureTest`, `TugasLogbookTest`, `ScoringAndCompletionTest`).
- [x] **Langkah 5.3**: Lakukan verifikasi visual melalui browser subagent di `http://127.0.0.1:8000/pembimbing/dashboard`, periksa seluruh tab dan responsivitas elemen baru.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - `test_dashboard_menampilkan_upcoming_deadlines_dan_logbook_alert`: Memastikan tugas yang jatuh tempo dalam $\le 3$ hari muncul di widget mendatang.
   - `test_dashboard_menghitung_distribusi_jenjang_dan_kapasitas`: Memastikan agregasi SMK/D3/S1 akurat.
   - `test_jejak_audit_revisi_nilai_tidak_error_pada_tab_4`: Memastikan tabel audit trail merender format array JSON dan kolom `waktu` dengan sempurna tanpa error.
2. **Visual & UI Verification**:
   - Memastikan tidak ada pergeseran tata letak (*layout shift*).
   - Memastikan hierarki tipografi rapi, warna kontras sesuai WCAG AA, dan estetika Sleman Blue `#0641CC` tetap elegan.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat Dampak | Strategi Mitigasi |
| :--- | :---: | :--- |
| **Kueri deteksi logbook mentee lambat jika data riwayat besar** | Rendah | Menggunakan kueri berbasis subquery `whereIn('penempatan_id', ...)` dengan agregasi `MAX(tanggal)` per mentee yang terindeks database. |
| **Kepadatan tampilan di layar kecil/ponsel** | Sedang | Memakai CSS grid responsif (`repeat(auto-fit, minmax(...))`) dan flex-wrap pada hero action buttons. |
| **Data nilai lama/baru tidak bertipe array** | Rendah | Menyiapkan fallback helper pemeriksaan `is_array($rev->nilai_lama) ? ... : ...` untuk menangani kompatibilitas data historis. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Seluruh widget baru (Quick Action, Upcoming Deadlines, Logbook Compliance Alert, Komposisi Jenjang) terintegrasi harmonis di Dashboard Pembimbing.
- [x] Filter pencarian instan pada Tab 2 dan Tab 3 berfungsi mulus.
- [x] Audit trail revisi nilai di Tab 4 terbebas dari bug timestamp dan konversi array JSON.
- [x] 100% test suite pembimbing lulus (**Green** - 35 passed, 172 assertions).
- [x] Tampilan terverifikasi bersih, profesional, dan modern melalui browser testing (4 tab diaudit & disimpan screenshot).

---

## 9. Catatan Perubahan & Hasil Implementasi

- **Hasil Eksekusi**:
  1. Backend `DashboardController.php` telah mengagregasi data dengan efisien tanpa migrasi tambahan.
  2. View `dashboard.blade.php` diperkaya dengan 3 tombol quick action di hero, panel upcoming deadlines, banner kedisiplinan logbook, widget jenjang pendidikan, serta live search instan.
  3. Tabel jejak revisi nilai di Tab 4 kini menampilkan waktu perubahan dan snapshot nilai akhir dengan format angka yang tepat.
  4. Pengujian otomatis: 5 suite pengujian pembimbing (`PembimbingDashboardEnhancementTest`, `PembimbingFullJobDeskTest`, `PembimbingPortalFeatureTest`, `TugasLogbookTest`, `ScoringAndCompletionTest`) lulus 100% (35 passed, 172 assertions).
  5. Pengujian antarmuka: Terverifikasi visual dengan 4 screenshot tab di browser browser subagent.
- **Saran Lanjutan (Out of Scope)**:
  - Fitur kirim notifikasi WhatsApp otomatis ke mentee yang telat mengisi logbook (memerlukan integrasi gateway SMS/WA eksternal).
  - Ekspor rekap kehadiran/logbook mentee per bulan dalam bentuk PDF atau Excel.
