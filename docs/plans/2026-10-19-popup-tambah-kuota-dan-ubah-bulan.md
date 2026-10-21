# Rencana Kerja: Redesign Popup Modal "Tambah Kuota" dan Penyelarasan "Ubah Bulan"

**Tanggal Pembuatan**: 19 Oktober 2026  
**Status**: ✅ Selesai Diimplementasikan & Lolos Seluruh Pengujian  
**Branch**: `test-1`  
**Referensi Desain**:
1. Tangkapan layar popup **Tambah Kuota** (Bulan/Periode disabled, dropdown Bidang, chips Jurusan yang dibutuhkan dengan tombol `+ Jurusan`, filter jenjang pills, grid jumlah kuota & durasi min/max, box warning merah "Melebihi kapasitas pembimbing", dan tombol Batal/Simpan kuota).
2. Popup **Ubah Bulan** yang diselaraskan 100% dengan gaya visual dan struktur popup **Buat Bulan Baru** sebelumnya.
**Catatan Wajib Pengguna**: Tetap menggunakan istilah **"Bulan"** / **"Bulan Magang"** (dilarang menggunakan kata "Periode").

---

## 1. Tujuan
Merombak dua modal interaktif utama pada halaman Manajemen Kuota (`resources/views/admin/kuota/index.blade.php`):
1. **Modal `Tambah Kuota` (`#modalTambahKuota`)**:
   - Dirombak total agar 100% presisi dengan desain visual yang dilampirkan:
     - Header: Judul **`Tambah kuota`**, subtitle *"Atur kuota, jurusan, jenjang, dan durasi untuk satu bidang."*, dan close button rounded box `×`.
     - Baris 1: Kolom `Bulan` (read-only menampilkan nama bulan aktif) & dropdown `Bidang` (memilih bidang sasaran: Persandian, Aplikasi Informatika, Statistik, Sekretariat).
     - Baris 2: `Jurusan yang dibutuhkan` berupa chips tag yang dapat dipilih/centang interaktif (`✓ Informatika`, `✓ Teknik Komputer`, `✓ Keamanan Siber`) beserta tombol aksi `+ Jurusan`.
     - Baris 3: `Jenjang` berupa toggle pills multi-pilih (`SMK`, `D3`, `✓ S1`).
     - Baris 4: 3 kolom sejajar: `Jumlah kuota` (input number), `Durasi minimum` (dropdown select `1–6 bulan`), dan `Durasi maksimum` (dropdown select `1–6 bulan`).
     - Baris 5: Box alert merah/warning dinamis **"Melebihi kapasitas pembimbing"** yang otomatis muncul jika kuota yang diinput melebihi sisa kapasitas bimbingan bidang terkait.
     - Footer: Tombol outline **`Batal`** dan tombol primary **`Simpan kuota`**.
2. **Modal `Ubah Bulan` (`#modalUbahBulan`)**:
   - Diselaraskan penuh dengan tata letak dan visual **Buat Bulan Baru**:
     - Header: Judul **`Ubah bulan`**, subtitle *"Tentukan jadwal pendaftaran dan mulai magang."*, dan close button rounded box `×`.
     - Formulir: Input `Nama bulan` (pre-filled), grid 2 kolom `Tanggal buka` & `Tanggal tutup` dengan ikon kalender, baris `Tanggal mulai magang` dengan helper text *"Harus setelah tanggal tutup pendaftaran."*, serta segmented control pills `Status bulan` (`✓ Draft` / `Dibuka`).
     - Callout info box biru: *"Perubahan jadwal pendaftaran dan mulai magang akan langsung disinkronkan ke portal publik."*
     - Footer: Tombol outline **`Batal`** dan tombol primary **`Simpan perubahan`**.

---

## 2. Asumsi & Pemetaan Desain

1. **Konsistensi Istilah Resmi**:
   - Seluruh label input, teks petunjuk, placeholder, dan pesan peringatan wajib menggunakan kata **"Bulan"** / **"Bulan Magang"** (dilarang menggunakan kata "Periode", misal label field pertama diubah dari *Periode* menjadi *Bulan*).
2. **Kalkulasi Kapasitas Pembimbing**:
   - Kapasitas maksimal bimbingan satu bidang dihitung dari rumus:
     $$\text{Kapasitas Bidang} = \text{Jumlah Pembimbing Aktif} \times 6\text{ peserta}$$
     *(atau minimal 12 peserta jika belum ada pembimbing)*.
   - Sisa kapasitas bidang dihitung dari:
     $$\text{Sisa Kapasitas} = \max(0, \text{Kapasitas Bidang} - \text{Total Kuota Terpasang Bidang})$$
   - Jika `Jumlah kuota` yang dimasukkan admin melebihi `Sisa Kapasitas`, kotak peringatan merah **"Melebihi kapasitas pembimbing"** langsung muncul di antarmuka modal.
3. **Kompatibilitas Backend Kuota (Zero Breaking Changes)**:
   - Form `Tambah kuota` mendukung payload penugasan berbasis bidang dan jurusan terpilih, sekaligus tetap mempertahankan fallback single `jurusan_id` sehingga seluruh pengujian eksisting (seperti `EndToEndUatTest` dan `AdminSuperAdminPhaseThreeTest`) tetap lulus 100%.

---

## 3. Evaluasi Opsi Arsitektur

| Parameter | Opsi 1: Dynamic Interactive Modal dengan Data Binding Bidang (Rekomendasi) | Opsi 2: Form Statis dengan Validasi Server-Side Saja |
| :--- | :--- | :--- |
| **Deskripsi** | Menyiapkan data master bidang, relasi jurusan, rasio pembimbing, dan kuota terpasang sebagai JSON client-side di view. Saat admin memilih bidang, chips jurusan, jenjang, dan batas kapasitas pembimbing langsung berganti secara instan (real-time). | Formulir hanya berupa input biasa tanpa interaksi dinamis. Peringatan kapasitas hanya muncul setelah form disubmit (refresh halaman). |
| **Kelebihan** | 1. Pengalaman pengguna (UX) sangat modern, presisi 100% dengan gambar referensi.<br>2. Admin langsung melihat peringatan kapasitas merah tanpa perlu reload halaman.<br>3. Pemilihan jurusan dan jenjang intuitif menggunakan chip toggle. | Implementasi view lebih sederhana. |
| **Kekurangan** | Memerlukan script JavaScript client-side untuk menangani event perubahan bidang dan input kuota. | Kurang interaktif; tidak memberikan umpan balik visual langsung kepada admin seperti pada gambar. |
| **Keputusan** | **DIPILIH (Opsi 1)**. | Ditolak karena tidak memenuhi tuntutan interaksi visual pada desain. |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/kuota/index.blade.php` | `Modify` | Rombak modal `#modalTambahKuota` sesuai desain screenshot (field Bulan, Bidang, chips Jurusan `+ Jurusan`, pills Jenjang, 3 kolom jumlah/durasi, warning box kapasitas, dan footer); selaraskan modal `#modalUbahBulan` dengan gaya `#modalBuatBulan`; tambahkan JavaScript pendukung. |
| `app/Http/Controllers/Admin/KuotaController.php` | `Modify` | Perbarui `updateBulan` untuk mendukung `nama_bulan` dan `status_awal` (`draft` vs `dibuka`); perbarui `store` agar dapat menerima kuota berbasis bidang dan jurusan array secara transparan. |
| `app/Http/Requests/KuotaRequest.php` | `Modify` | Berikan fleksibilitas validasi payload kuota (mendukung `bidang_id` dan `jurusan_ids[]` tanpa merusak payload lama `jurusan_id`). |
| `tests/Feature/AdminKuotaRevampTest.php` | `Modify` | Tambahkan feature test untuk modal Tambah Kuota (tampilan chips, jenjang, kapasitas warning) dan pengujian modal Ubah Bulan baru. |

---

## 5. Spesifikasi Komponen Desain

### A. Modal Tambah Kuota (`#modalTambahKuota`)
```
┌────────────────────────────────────────────────────────┐
│ Tambah kuota                                       [X] │
│ Atur kuota, jurusan, jenjang, dan durasi untuk         │
│ satu bidang.                                           │
│                                                        │
│ Bulan                           Bidang                 │
│ [ Gelombang 2 (Read-only)    ]  [ Persandian         v] │
│                                                        │
│ Jurusan yang dibutuhkan                                │
│ [✓ Informatika] [✓ Teknik Komputer] [✓ Keamanan Siber] │
│ [+ Jurusan]                                            │
│                                                        │
│ Jenjang                                                │
│ [ SMK ]  [ D3 ]  [✓ S1 ]                               │
│                                                        │
│ Jumlah kuota    Durasi minimum    Durasi maksimum      │
│ [ 3          ]  [ 3 bulan    v]   [ 6 bulan         v] │
│                                                        │
│ ┌────────────────────────────────────────────────────┐ │
│ │ ⚠️ Melebihi kapasitas pembimbing                   │ │
│ │    Kapasitas Persandian 12 peserta (2 Pembimbing   │ │
│ │    x 6) dan kuota terpasang sudah 6, sisa          │ │
│ │    kapasitas 6. Kurangi jumlah kuota yang          │ │
│ │    ditambahkan atau tambah Pembimbing aktif.       │ │
│ └────────────────────────────────────────────────────┘ │
│                                                        │
│                            [ Batal ]   [ Simpan kuota ] │
└────────────────────────────────────────────────────────┘
```

### B. Modal Ubah Bulan (`#modalUbahBulan`)
```
┌────────────────────────────────────────────────────────┐
│ Ubah bulan                                         [X] │
│ Tentukan jadwal pendaftaran dan mulai magang.          │
│                                                        │
│ Nama bulan                                             │
│ [ Contoh: Gelombang 2                                ] │
│                                                        │
│ Tanggal buka                    Tanggal tutup          │
│ [ 01/10/2026               📅 ] [ 15/10/2026       📅 ] │
│                                                        │
│ Tanggal mulai magang                                   │
│ [ 02/11/2026                                       📅 ] │
│ Harus setelah tanggal tutup pendaftaran.               │
│                                                        │
│ Status bulan                                           │
│ [ Draft ]  [✓ Dibuka ]                                 │
│                                                        │
│ ┌────────────────────────────────────────────────────┐ │
│ │ (i) Perubahan jadwal pendaftaran dan mulai magang  │ │
│ │     akan langsung disinkronkan ke portal publik.   │ │
│ └────────────────────────────────────────────────────┘ │
│                                                        │
│                        [ Batal ]   [ Simpan perubahan ] │
└────────────────────────────────────────────────────────┘
```

---

## 6. Langkah Implementasi Terstruktur

- [x] **Fase 1: Backend Controller & Request Update**
  - [x] Perbarui `app/Http/Requests/KuotaRequest.php` agar mendukung payload `bidang_id`, `jurusan_ids`, serta durasi/jenjang tanpa membatalkan aturan dasar.
  - [x] Perbarui method `KuotaController::store` untuk memproses kuota jurusan di bawah bidang yang dipilih dan mencatat audit log.
  - [x] Perbarui method `KuotaController::updateBulan` untuk memproses pembaruan `nama_bulan`, jadwal tanggal, dan status `aktif` (`status_awal === 'dibuka'`).
  - [x] Siapkan payload JSON master bidang (`bidangJsonData`) di controller/view lengkap dengan daftar jurusan terikat, jumlah pembimbing aktif, dan kuota terpasang.
- [x] **Fase 2: Redesign Modal "Ubah Bulan" (`#modalUbahBulan`)**
  - [x] Rombak struktur HTML `#modalUbahBulan` di `resources/views/admin/kuota/index.blade.php` mengikuti desain `#modalBuatBulan`.
  - [x] Pre-fill field nama bulan, tanggal buka, tanggal tutup, tanggal mulai magang, dan status bulan aktif dari data `$selectedBulan`.
  - [x] Pasang JavaScript helper `openUbahBulanModal()` dan `setStatusUbahBulan(status)`.
- [x] **Fase 3: Redesign Modal "Tambah Kuota" (`#modalTambahKuota`)**
  - [x] Implementasikan layout input: `Bulan` (read-only) dan dropdown `Bidang`.
  - [x] Rancang komponen interaktif chips `Jurusan yang dibutuhkan` dengan status centang `✓` dan tombol popover/picker `+ Jurusan`.
  - [x] Rancang komponen pills `Jenjang` (`SMK`, `D3`, `S1`).
  - [x] Buat 3 kolom sejajar untuk `Jumlah kuota`, `Durasi minimum`, dan `Durasi maksimum`.
  - [x] Implementasikan Box Warning Peringatan Kapasitas Pembimbing (`#boxWarningKapasitas`) berwarna merah muda dengan ikon segitiga peringatan.
  - [x] Tulis JavaScript kalkulasi kapasitas: Saat bidang atau jumlah kuota berubah, hitung apakah `jumlah kuota > sisa kapasitas`. Jika ya, tampilkan warning box dan teks kapasitas real-time; jika tidak, sembunyikan warning box.
- [x] **Fase 4: Pengujian & Validasi Otomatis**
  - [x] Tambahkan skenario test di `tests/Feature/AdminKuotaRevampTest.php`:
    - `test_modal_tambah_kuota_displays_all_elements_from_design`: Memastikan field bidang, chips jurusan, jenjang, input kuota, dan durasi hadir di DOM.
    - `test_modal_ubah_bulan_matches_buat_bulan_design`: Memastikan modal ubah bulan memiliki field nama bulan, grid tanggal kalender, dan segmented pills status.
    - `test_admin_can_update_bulan_with_status_and_name`: Memastikan endpoint `updateBulan` memperbarui `nama_bulan` dan status `aktif`.
    - `test_admin_can_store_kuota_with_jurusan_ids_array`: Memastikan penyimpanan kuota jamak berbasis array jurusan berhasil.
  - [x] Jalankan `php artisan test --filter=AdminKuotaRevampTest`.
  - [x] Jalankan tes regresi `AdminTerminologyDesignSystemTest` dan `DashboardModulesRenderingTest`.
  - [x] Jalankan pemformatan kode dengan Pint: `./vendor/bin/pint`.

---

## 7. Strategi Testing

1. **Feature Test (`AdminKuotaRevampTest`)**:
   - Memvalidasi markup HTML kedua modal di halaman admin kuota.
   - Menguji aksi penyimpanan pembaruan bulan magang via `PUT /admin/kuota/bulan/{id}`.
   - Menguji aksi penyimpanan kuota jurusan via `POST /admin/kuota`.
2. **Regression Testing**:
   - Memastikan pengujian `EndToEndUatTest` dan pengujian modul pendaftaran tidak terganggu.

---

## 8. Risiko & Mitigasi

- **Risiko**: Bidang yang belum memiliki jurusan terikat pada tabel pivot `bidang_jurusan`.
  - **Mitigasi**: Sediakan fallback agar tombol `+ Jurusan` dapat memilih jurusan apa pun dari master data dan langsung mengaitkannya ke bidang tersebut.
- **Risiko**: Bidang dengan 0 pembimbing aktif menyebabkan sisa kapasitas 0.
  - **Mitigasi**: Tetapkan kapasitas dasar minimum (misal: 12 peserta) atau tampilkan pesan informatif yang jelas untuk segera menugaskan pembimbing di master data.

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Modal `Tambah kuota` menampilkan field Bulan (read-only), dropdown Bidang, chips Jurusan dengan centang `✓`, pills Jenjang, serta 3 kolom (Jumlah kuota, Durasi min, Durasi max).
- [x] Box peringatan merah kapasitas pembimbing tampil dengan format presisi sesuai screenshot saat kuota melebihi sisa kapasitas bimbingan.
- [x] Modal `Ubah bulan` selaras 100% dengan modal `Buat bulan baru` (Nama bulan, kalender buka/tutup/mulai magang, segmented status pills Draft/Dibuka).
- [x] Seluruh teks dan antarmuka menggunakan kata **"Bulan"** / **"Bulan Magang"** (tidak ada kata "Periode").
- [x] Seluruh pengujian otomatis di `AdminKuotaRevampTest` lulus 100%.
