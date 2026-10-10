# Plan: Perbaikan & Penguatan Dashboard Eksekutif dan Audit Log SuperAdmin

- **Tanggal**: 2026-10-10
- **Status**: Selesai (Implemented & Verified)
- **Author**: Antigravity (web-planning & web-implementation)
- **Modul Terkait**: SuperAdmin Dashboard Eksekutif (`/superadmin/dashboard`), Audit Log Trail (`/superadmin/audit-log`)
- **Referensi PRD**: Bab 3.3, Bab 4.3, Bab 9.2, Bab 10 (NFR-A1 s/d NFR-A5), AC-12, AC-13 (AC-13-1 s/d AC-13-7), BR-02, BR-13

---

## 1. Tujuan

Menyempurnakan dan memperbaiki modul **Dashboard Eksekutif SuperAdmin** dan **Audit Log Trail SuperAdmin** agar bebas dari kesalahan query database/view rendering (*unknown column `created_at`*, relasi `slot_khusus`), menyajikan indikator analitik strategis tata kelola magang untuk pimpinan dinas Kominfo (PRD 4.3 & 9.2), serta memperkuat kemampuan penelusuran rekam jejak audit (*keyword search*, pemetaan atribut `entitas_id`/`ip`, dan ekspor audit compliance terotorisasi per AC-13-6).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis & Bisnis:
1. **AuditLog Schema**: Tabel `audit_log` bersifat *append-only* (`$timestamps = false`), menggunakan kolom `waktu` (DATETIME), `entitas`, `entitas_id`, `ip`, `request_id`, `tipe`, `aksi`, `nilai_lama`, `nilai_baru`, dan `detail` (AC-13-1 s/d AC-13-6).
2. **SlotKhusus Schema**: Tabel `slot_khusus` tidak memiliki kolom `bulan_magang_id`; asosiasi dengan periode magang dilakukan berdasarkan `bulan_mulai` (DATE) yang bersesuaian dengan `bulan_magang.bulan_mulai`.
3. **Peran SuperAdmin**: SuperAdmin adalah otoritas tertinggi pimpinan teknis Kominfo; dashboard harus berfokus pada kesehatan sistem, utilisasi kuota umum vs slot khusus mitra, pengawasan beban pembimbing lapangan (BR-13), dan ringkasan kepatuhan keamanan.

### Pertanyaan Terbuka:
- Tidak ada isu pemblokir arsitektural yang membutuhkan konfirmasi user sebelum perencanaan; opsi teknis diuraikan pada Bagian 3.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Penelusuran Pencarian Audit Trail (AC-13-6):
- **Opsi A (Pencarian Multi-Kolom Parsial di Database via Eloquent)**:
  - Menggunakan conditional group query `where(function ($q) { $q->where('aksi', 'like', $search)->orWhere('entitas', 'like', $search)->orWhere('request_id', 'like', $search)->orWhereHas('user', ...); })`.
  - *Kelebihan*: Sangat fleksibel, tidak membutuhkan indeks eksternal, cepat untuk dataset berukuran puluhan ribu baris, kompatibel penuh dengan SQLite test dan MySQL production.
  - *Kekurangan*: Sedikit penambahan beban komputasi LIKE string.
  - *Rekomendasi*: **Dipilih (Opsi A)** karena sesuai dengan standar query controller lain di MAGKOM (`PendaftaranController`, `ArsipPesertaController`).
- **Opsi B (Pencarian Teks Penuh / Full-Text Index)**:
  - Menggunakan MySQL MATCH AGAINST.
  - *Kekurangan*: Tidak portabel ke SQLite in-memory yang digunakan oleh test suite Laravel.

### Evaluasi Visualisasi Dashboard Eksekutif:
- Mengintegrasikan metrik strategis:
  1. **Executive Stat Cards**: Peserta Aktif, Total Alumni Selesai, Rasio Pembatalan/Mundur, Staf Internal Aktif, Rasio Utilisasi Kuota Berjalan (Umum vs Slot Khusus).
  2. **Monitoring Utilisasi & Beban Pembimbing (BR-13)**: Indikator visual progres beban mentee dengan badge *Normal / Penuh / Overload* berbasis konfigurasi `maks_peserta_aktif_per_pembimbing`.
  3. **Demografi Pendidikan & Top 5 Institusi**: Distribusi jenjang pendidikan (SMK, D3, S1) dan perguruan tinggi/sekolah penyumbang peserta terbanyak.
  4. **Sebaran Jalur Magang**: Proporsi penempatan jalur reguler (umum) vs jalur khusus kemitraan (F-06/F-07).
  5. **Live Feed Audit Trail (5 Aksi Terakhir)**: Menampilkan aksi mutasi mutakhir secara realtime dengan atribut yang valid (`waktu`, `entitas`, `aksi`, `user`).

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`app/Http/Controllers/SuperAdmin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/DashboardController.php) | Modifikasi | Perbaiki query `slot_khusus` (ganti `bulan_magang_id` ke `bulan_mulai`), perbaiki pengurutan `audit_log` (`waktu desc`), tambahkan agregasi rasio jalur umum vs khusus & status peserta. |
| [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php) | Modifikasi | Perbaiki binding atribut audit (`waktu`, `entitas`, `aksi`), perbaiki penanganan nilai null, tambahkan indikator ringkasan rasio jalur penempatan dan status antrean verifikasi. |
| [`app/Http/Controllers/SuperAdmin/AuditLogController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/AuditLogController.php) | Modifikasi | Tambahkan filter pencarian kata kunci multi-kolom (`q`: aksi, entitas, request_id, nama user) dan aksi ekspor compliance CSV terotorisasi khusus SuperAdmin. |
| [`resources/views/superadmin/audit-log/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/audit-log/index.blade.php) | Modifikasi | Perbaiki pemetaan atribut JavaScript modal (`entitas_id`, `ip`), tambahkan input search bar `q`, tampilkan informasi `durasi_ms` & `status_http` pada inspeksi modal, dan tambahkan tombol ekspor log audit. |
| [`tests/Feature/SuperAdminDashboardAndAuditLogTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminDashboardAndAuditLogTest.php) | Pembuatan | Test feature baru untuk menguji kebenaran metrik dashboard eksekutif, rendering feed audit tanpa error, keyword search audit trail, dan modal inspeksi. |

---

## 5. Langkah Implementasi

### Modul 1: Perbaikan Bug & Penguatan Controller Dashboard Eksekutif
- [x] **Langkah 1.1**: Buka dan perbarui [`app/Http/Controllers/SuperAdmin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/DashboardController.php):
  - Perbaiki pencarian slot khusus pada bulan aktif:
    ```php
    $slotKhusus = SlotKhusus::whereDate('bulan_mulai', $bulanAktif->bulan_mulai)->where('aktif', true)->first();
    ```
  - Perbaiki query feed jejak audit 5 terbaru agar menggunakan kolom yang valid:
    ```php
    $auditTerbaru = AuditLog::with('user')
        ->orderBy('waktu', 'desc')
        ->orderBy('id', 'desc')
        ->take(5)
        ->get();
    ```
  - Tambahkan metrik rasio penempatan aktif:
    - Jumlah peserta jalur umum vs jalur khusus (`Penempatan::where('status', 'aktif')->where('jalur', ...)->count()`).
    - Jumlah peserta yang mengundurkan diri (`Penempatan::where('status', 'mengundurkan_diri')->count()`).
    - Jumlah antrean pendaftaran yang masih menunggu verifikasi admin (`Pendaftaran::where('status', 'diajukan')->count()`).

### Modul 2: Perbaikan Tampilan Blade Dashboard Eksekutif
- [x] **Langkah 2.1**: Buka dan perbarui [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php):
  - Perbaiki baris feed audit tabel:
    - Ganti `$log->created_at->format(...)` menjadi `$log->waktu?->translatedFormat('d M Y H:i:s') ?? '-'`.
    - Ganti `$log->tabel` menjadi `$log->entitas ?? '-'`.
    - Ganti `$log->detail['aksi']` menjadi `$log->aksi ?? ($log->detail['aksi'] ?? '-')`.
  - Perbaiki tampilan card Slot Khusus vs Kuota Umum dengan penanganan fallback yang elegan jika belum ada periode magang aktif.
  - Tambahkan widget ringkasan status jalur magang (Reguler/Umum vs Kemitraan Khusus) serta status antrean berkas pendaftar baru.

### Modul 3: Penguatan Controller Audit Log SuperAdmin (Keyword Search & Ekspor Compliance)
- [x] **Langkah 3.1**: Buka dan perbarui [`app/Http/Controllers/SuperAdmin/AuditLogController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/AuditLogController.php):
  - Tambahkan penanganan parameter pencarian `q` pada query builder:
    ```php
    if ($request->filled('q')) {
        $search = '%' . trim((string) $request->input('q')) . '%';
        $query->where(function ($q) use ($search) {
            $q->where('aksi', 'like', $search)
              ->orWhere('entitas', 'like', $search)
              ->orWhere('request_id', 'like', $search)
              ->orWhereHas('user', function ($qu) use ($search) {
                  $qu->where('nama', 'like', $search)
                     ->orWhere('email', 'like', $search);
              });
        });
    }
    ```
  - Tambahkan method `export(Request $request)` untuk mengunduh log audit terfilter ke format CSV yang disanitasi (anti-formula injection) dengan UTF-8 BOM, serta mencatat peristiwa akses data ke audit trail itu sendiri (AC-13-4, AC-13-6).

### Modul 4: Penyempurnaan Tampilan & Modal Inspeksi Audit Log
- [x] **Langkah 4.1**: Buka dan perbarui [`resources/views/superadmin/audit-log/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/audit-log/index.blade.php):
  - Tambahkan input search keyword `q` ("Cari aksi, entitas, ID request, atau staf...") pada toolbar filter.
  - Tambahkan tombol "Ekspor Log CSV" berdampingan dengan tombol Filter.
  - Perbaiki fungsi JavaScript `openModalLogDetail(data)` dan passing parameternya di tabel:
    - Gunakan `id_data: $log->entitas_id ?? $log->id_data`.
    - Gunakan `ip_address: $log->ip ?? $log->ip_address`.
    - Tambahkan informasi `metode`, `rute`, `status_http`, dan `durasi_ms` ke dalam grid info teknis modal inspeksi.
    - Sediakan visualisasi format JSON yang rapi dengan highlight payload mutasi.

### Modul 5: Automated Testing & Verifikasi Kualitas
- [x] **Langkah 5.1**: Buat test feature komprehensif [`tests/Feature/SuperAdminDashboardAndAuditLogTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminDashboardAndAuditLogTest.php):
  - Uji rendering dashboard eksekutif SuperAdmin: pastikan tidak ada error query SQL, metrik peserta aktif, slot khusus, dan beban pembimbing terhitung akurat.
  - Uji feed audit log pada dashboard: pastikan membaca kolom `waktu` dan `entitas`.
  - Uji pencarian kata kunci audit log (`q`): uji pencarian berdasarkan `aksi`, `entitas`, dan `nama` staf.
  - Uji filter staf, tipe log, dan tanggal pada audit log.
  - Uji unduh ekspor compliance log audit CSV: verifikasi UTF-8 BOM dan header CSV yang valid.
- [x] **Langkah 5.2**: Jalankan pengujian penuh via `php artisan test` untuk memastikan 100% green tanpa regresi.

---

## 6. Strategi Testing

### Automated Feature Tests:
1. `test_superadmin_executive_dashboard_renders_successfully_with_correct_metrics`: Menguji load dashboard SuperAdmin tanpa error SQL, memverifikasi agregasi kuota, slot khusus, dan beban mentee.
2. `test_superadmin_dashboard_handles_empty_or_inactive_months_gracefully`: Menguji rendering dashboard saat belum ada bulan magang aktif tanpa null pointer exception.
3. `test_audit_log_index_renders_and_supports_keyword_search`: Menguji pencarian multi-kolom `q` pada audit trail.
4. `test_audit_log_inspection_modal_payload_contains_correct_attributes`: Menguji parameter data yang dikirimkan ke modal (`entitas_id`, `ip`, `status_http`).
5. `test_superadmin_can_export_filtered_audit_logs_to_csv`: Menguji ekspor file CSV audit log terfilter dan memastikan anti-formula injection.

---

## 7. Risiko & Mitigasi

- **Risiko Ukuran Data Audit Log yang Besar**:
  - *Mitigasi*: Paginasi tetap 25 per halaman (`paginate(25)`). Pada ekspor CSV, gunakan chunking atau streaming response (`streamDownload`) untuk mencegah memory exhaustion.
- **Risiko Nilai Kolom Waktu Null pada Data Historis**:
  - *Mitigasi*: Gunakan fallback aman `$log->waktu?->format(...) ?? '-'` pada Blade dan JSON exporter.
- **Risiko Keamanan Hak Akses Log (AC-13-6)**:
  - *Mitigasi*: Seluruh route audit log tetap strictly di dalam grup middleware `role:superadmin` dan strictly *read-only* (tidak ada rute UPDATE/DELETE).

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Seluruh checklist langkah implementasi tercentang `- [x]`.
- [x] Halaman Dashboard Eksekutif SuperAdmin (`/superadmin/dashboard`) bebas error `created_at` dan `bulan_magang_id`.
- [x] Halaman Audit Log (`/superadmin/audit-log`) memiliki fitur pencarian keyword `q`, filter staf dropdown, dan tombol ekspor CSV compliance.
- [x] Modal inspeksi detail audit log menampilkan Entitas ID, IP Address, HTTP Status, dan durasi secara presisi.
- [x] Seluruh pengujian di `SuperAdminDashboardAndAuditLogTest.php` serta tes regresi terkait lulus 100% green.

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Catatan Hasil Analisis**:
  - Ditemukan dua bug struktural pada `DashboardController`: pemanggilan kolom `created_at` pada tabel `audit_log` (yang menggunakan `waktu`), dan pemanggilan `bulan_magang_id` pada model `SlotKhusus` (yang menggunakan `bulan_mulai`).
  - Ditemukan ketidakcocokan nama atribut di view `dashboard.blade.php` (`created_at` vs `waktu`, `tabel` vs `entitas`).
  - Ditemukan atribut modal JavaScript di `audit-log/index.blade.php` yang belum memetakan `entitas_id` dan `ip`.
- **Saran Lanjutan**:
  - Grafik visual interaktif (misal via Chart.js / ApexCharts lokal tanpa CDN eksternal) untuk tren pendaftaran bulanan jika instansi menghendaki visualisasi grafik batang/garis.
