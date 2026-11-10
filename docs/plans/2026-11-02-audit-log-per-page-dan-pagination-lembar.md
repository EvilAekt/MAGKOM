# Rencana Kerja: Fitur Pilihan Menampilkan (10, 20, 30) & Navigasi Lembar Halaman (1, 2, 3, 4) pada Audit Log

**Dokumen Rencana**: `docs/plans/2026-11-02-audit-log-per-page-dan-pagination-lembar.md`  
**Status**: Selesai Diimplementasikan  
**Tujuan**: Menambahkan kontrol interaktif untuk memilih jumlah data log yang ditampilkan per halaman (`10`, `20`, atau `30` entri) dan menampilkan navigasi lembar halaman bernomor (`1, 2, 3, 4...`) di bawahnya pada modul Jejak Audit SuperAdmin (`#all-actions-stream`).

---

## 1. Analisis Kebutuhan & Masalah Saat Ini

Berdasarkan tangkapan layar `media_1793563189790_077f6a2c.jpg` dan inspeksi codebase:
1. **Jumlah Data Hardcoded di Backend**:
   - Di `app/Http/Controllers/SuperAdmin/AuditLogController.php:216`, query saat ini hardcoded:
     `$logs = $query->paginate(30)->withQueryString();`
   - Tidak ada penanganan parameter query `per_page` di backend, sehingga SuperAdmin tidak dapat memilih jumlah baris data yang ingin ditampilkan (misal 10 atau 20 baris).
2. **Tidak Ada Pilihan Kontrol "Menampilkan (10, 20, 30)" di UI**:
   - Di tampilan `resources/views/superadmin/audit-log/partials/_stream_table.blade.php`, belum terdapat tombol/selector pilihan entri per halaman.
3. **Navigasi Lembar Halaman Terbatas**:
   - Saat ini pagination hanya menggunakan rentang sempit `max(1, currentPage - 2)` sampai `min(lastPage, currentPage + 2)`. Saat berada di halaman 1, hanya angka `1, 2, 3` yang muncul (angka `4` belum tampak meskipun total entri 131 data / 14 halaman).
   - Pengguna menginginkan di bawah kontrol per-page tersedia pilihan lembar halaman yang jelas: `1, 2, 3, 4...`.

---

## 2. Rancangan Desain Antarmuka (UI/UX Mockup)

Struktur Footer Tabel Audit Log akan dibagi menjadi 2 baris terstruktur:

```text
+----------------------------------------------------------------------------------------------------+
| [Tabel Log Aktivitas Audit #all-actions-stream]                                                    |
+----------------------------------------------------------------------------------------------------+
| BARIS 1 (Info Counter & Pilihan Menampilkan):                                                      |
| Menampilkan 1 - 10 dari 131 entri   [🛡️ Audit Log Immutable]        Menampilkan: [ 10 | 20 | 30 ]  |
+----------------------------------------------------------------------------------------------------+
| BARIS 2 (Pilihan Lembar Halaman / Pagination):                                                     |
| Lembar Halaman: 1 dari 14              [ « Sebelumnya ]  [ 1 ]*  [ 2 ]  [ 3 ]  [ 4 ] ... [ 14 ]    |
+----------------------------------------------------------------------------------------------------+
```

### Karakteristik Desain:
1. **Selector Pilihan Menampilkan (`10`, `20`, `30`)**:
   - Berupa *segmented pill buttons* modern (`[ 10 | 20 | 30 ]`) dengan active state kontras (warna biru Kominfo di Light Mode, indigo terang di Dark Mode).
   - Mengubah parameter `per_page` pada URL secara otomatis tanpa mereset filter yang sedang aktif (`channel`, `q`, `user_id`, `entitas`, `severity`, `tipe`).
   - Default awal diatur ke `10` entri per halaman.
2. **Navigasi Lembar Halaman (`1, 2, 3, 4...`)**:
   - Menampilkan angka lembar secara eksplisit: `[ 1 ]`, `[ 2 ]`, `[ 3 ]`, `[ 4 ]`, dst.
   - Dilengkapi tombol navigasi `[ « Sebelumnya ]` dan `[ Selanjutnya » ]`.
   - Menggunakan kalkulasi sliding window yang memastikan minimal halaman `1, 2, 3, 4` selalu terlihat pada awal penjelajahan.
   - Mendukung penuh Mode Gelap (*Dark Mode*) dan ramah layar sentuh (*mobile responsive*).

---

## 3. Evaluasi Pendekatan Teknis

| Kriteria | **Opsi A: Segmented Pill Switcher (Rekomendasi)** | **Opsi B: Dropdown `<select>` Tradisional** |
| :--- | :--- | :--- |
| **Deskripsi** | Menampilkan tombol pill `[ 10 | 20 | 30 ]` yang dapat diklik langsung dengan 1 ketukan. | Menampilkan dropdown select `Tampilkan [ 10 ▾ ] entri`. |
| **Ergonomi** | **Sangat Cepat**: 1 klik langsung mengubah tampilan halaman. | Butuh 2 klik (klik dropdown -> pilih opsi). |
| **Estetika** | Modern, konsisten dengan *pill filter chips* yang ada di bagian atas tabel audit log. | Tampilan form standar lama. |
| **Responsivitas** | Menyesuaikan lebar layar dengan rapi tanpa overflow. | Rapi namun kurang interaktif. |
| **Rekomendasi** | **Dipilih (Opsi A)** untuk kenyamanan operasional SuperAdmin. | Alternatif sekunder. |

---

## 4. Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Controllers/SuperAdmin/AuditLogController.php` | `Modify` | Tangkap input `per_page` (opsi: 10, 20, 30; default: 10) dan gunakan pada query `$query->paginate($perPage)`. |
| `resources/views/superadmin/audit-log/partials/_stream_table.blade.php` | `Modify` | Tambahkan kontrol baris atas `Menampilkan: [ 10 | 20 | 30 ]`, tambahkan hidden input `per_page` pada form filter, dan tata ulang baris bawah untuk pilihan lembar `1, 2, 3, 4...`. |
| `resources/views/superadmin/audit-log/partials/_styles.blade.php` | `Modify` | Tambahkan styling kelas `.audit-per-page-group`, `.audit-per-page-pill`, dan responsivitas flexbox untuk Light & Dark Mode. |
| `tests/Feature/SuperAdminDashboardAndAuditLogTest.php` | `Modify` | Tambahkan automated test untuk memverifikasi fitur per-page (10, 20, 30) dan navigasi lembar halaman pada Audit Log. |

---

## 5. Langkah Implementasi (Checklist)

- [x] **Langkah 1: Controller Backend**:
  - Di `AuditLogController.php`: baca `request('per_page', 10)`, validasi hanya menerima integer `[10, 20, 30]`, dan kirimkan ke `$query->paginate($perPage)->withQueryString()`.
- [x] **Langkah 2: Blade View Header & Filter**:
  - Di `_stream_table.blade.php`: tambahkan `<input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">` di dalam `#auditFilterForm` agar filter pencarian tetap mempertahankan setting baris yang dipilih.
- [x] **Langkah 3: Blade View Footer Baris 1 (Pilihan Menampilkan 10, 20, 30)**:
  - Buat grup tombol segmented pill `10`, `20`, `30` yang otomatis men-generate URL dengan `per_page` baru dan mereset halaman ke `page=1`.
- [x] **Langkah 4: Blade View Footer Baris 2 (Pilihan Lembar 1, 2, 3, 4...)**:
  - Perbaiki kalkulasi range pagination agar halaman `1, 2, 3, 4` selalu tampil ketika data berjumlah banyak.
  - Tampilkan label *"Lembar Halaman X dari Y"* beserta tombol Sebelumnya/Selanjutnya.
- [x] **Langkah 5: CSS Stylesheet**:
  - Tambahkan definisi CSS `.audit-per-page-pill`, active states, dan adaptasi Dark Mode.
- [x] **Langkah 6: Automated Testing**:
  - Buat unit/feature test:
    - Memastikan `per_page=10` menghasilkan 10 data per halaman.
    - Memastikan `per_page=20` dan `per_page=30` berjalan sesuai parameter.
    - Memastikan nomor lembar 1, 2, 3, 4 tampil di respons HTML.
  - Jalankan test suite dan verifikasi 100% *passed*.
- [x] **Langkah 7: Knowledge Graph**:
  - Jalankan `graphify update .`.

---

## 6. Strategi Testing

1. **Feature Test**:
   - `test_superadmin_can_select_audit_log_per_page_options()`:
     - Request dengan `?per_page=10` &rarr; pastikan jumlah item di halaman pertama maksimal 10.
     - Request dengan `?per_page=20` &rarr; pastikan per_page 20 diterapkan.
     - Request dengan `?per_page=30` &rarr; pastikan per_page 30 diterapkan.
     - Periksa keberadaan elemen pagination `Lembar` dan link angka `1`, `2`, `3`, `4`.
2. **Regression Check**:
   - Jalankan `SuperAdminPortalVisualTest`, `SuperAdminDashboardAndAuditLogTest`, dan `SuperAdminUnifiedDashboardFeaturesTest`.

---

## 7. Kriteria Selesai (Definition of Done)

- [x] Pengguna dapat memilih opsi menampilkan `10`, `20`, atau `30` entri dengan satu kali klik.
- [x] Pilihan lembar `1, 2, 3, 4...` tampil dengan jelas di bawahnya dan dapat berpindah halaman dengan lancar.
- [x] Parameter filter pencarian dan kanal tetap bertahan saat berganti jumlah entri atau berpindah lembar.
- [x] Tampilan konsisten dan rapi baik di Light Mode maupun Dark Mode.
- [x] Seluruh automated test lolos 100% (*green*).
