# Rencana Kerja: Desain Ulang Popup Modal "Buat Bulan Baru"

**Tanggal Pembuatan**: 19 Oktober 2026  
**Status**: ✅ Selesai Diimplementasikan  
**Branch**: `test-1`  
**Referensi Desain**: Tangkapan layar antarmuka pengguna (`Buat periode baru` dengan field Nama, Tanggal buka, Tanggal tutup, Tanggal mulai magang, Status awal pills [Draft / Dibuka], Info box, dan tombol aksi).  
**Catatan Wajib Pengguna**: Tetap menggunakan istilah **"Bulan"** / **"Bulan Magang"** (dilarang menggunakan kata "Periode").

---

## 1. Tujuan
Menyesuaikan struktur antarmuka dan interaksi modal popup **Buat Bulan Baru** (`#modalBuatBulan`) pada halaman Manajemen Kuota (`resources/views/admin/kuota/index.blade.php`) agar 100% presisi dengan desain visual yang dilampirkan:
1. **Header Modal**:
   - Judul: **`Buat bulan baru`** (menggantikan kata "periode").
   - Deskripsi/Subtitle: *"Tentukan jadwal pendaftaran dan mulai magang."*
   - Tombol tutup silang (`×`) berbentuk kotak rounded di pojok kanan atas.
2. **Formulir Input Terstruktur**:
   - **`Nama bulan`**: Input teks dengan placeholder *"Contoh: Gelombang 3"* (atau *"Contoh: Bulan November 2026"*).
   - **`Tanggal buka` & `Tanggal tutup`**: Grid 2 kolom dengan input date berikon kalender di sisi kanan dan placeholder *"Pilih tanggal"*.
   - **`Tanggal mulai magang`**: Input date 1 kolom penuh berikon kalender dengan helper text *"Harus setelah tanggal tutup pendaftaran."*
   - **`Status awal`**: Pilihan segmented button/pills yang interaktif:
     - `✓ Draft` (default aktif, border biru, latar biru lembut, ikon centang `✓`).
     - `Dibuka` (border netral, latar putih).
3. **Banner Alert Callout (Biru Muda)**:
   - Ikon lingkaran info `(i)` biru royal.
   - Pesan informasi: *"Setelah bulan magang disimpan, atur kuota tiap bidang lewat tombol Tambah kuota."*
4. **Footer Aksi Modal**:
   - Tombol outline **`Batal`**.
   - Tombol primary **`Simpan bulan`** (bukan "Simpan periode").

---

## 2. Asumsi & Pemetaan Desain

1. **Konsistensi Terminologi**: Sesuai instruksi mutlak pengguna, seluruh label, placeholder, helper text, dan tombol aksi wajib menggunakan kata **"Bulan"** / **"Bulan Magang"** (tidak menggunakan istilah "Periode").
2. **Penyimpanan Nama Bulan**:
   - Kolom `nama_bulan` (opsional/nullable string 100) ditambahkan ke tabel `bulan_magang` agar nama gelombang (misal: "Gelombang 3" atau "Bulan November 2026") tersimpan dengan baik di database dan dapat ditampilkan pada banner hero utama. Jika tidak diisi, sistem otomatis menggunakan format tanggal bulan mulai (misal: "November 2026").
3. **Logika Status Awal**:
   - Jika admin memilih `Draft`: `aktif = false` (bulan magang tersimpan sebagai draft dan belum tampil di portal pendaftaran publik).
   - Jika admin memilih `Dibuka`: `aktif = true` (bulan magang langsung aktif dan siap dibuka sesuai jadwal pendaftaran).
4. **Validasi Tanggal**:
   - Tanggal buka: `required`, format tanggal valid.
   - Tanggal tutup: `required`, harus setelah atau sama dengan tanggal buka (`after_or_equal:tgl_buka`).
   - Tanggal mulai magang: `required`, harus setelah tanggal tutup pendaftaran (`after:tgl_tutup`).

---

## 3. Evaluasi Opsi Arsitektur

| Parameter | Opsi 1: Tambah Kolom `nama_bulan` & State `Status Awal` (Rekomendasi) | Opsi 2: Konversi Otomatis Nama Bulan ke Tanggal Saja (Tanpa Migrasi) |
| :--- | :--- | :--- |
| **Deskripsi** | Menambahkan migrasi non-destruktif untuk kolom `nama_bulan` (nullable) pada tabel `bulan_magang` dan menangani state `status_awal` (`draft` vs `dibuka`). | Tidak mengubah skema database, nama gelombang diabaikan dan hanya tanggal bulan mulai yang disimpan. |
| **Kelebihan** | 1. Input *"Nama bulan (Contoh: Gelombang 3)"* benar-benar tersimpan dan tampil di Hero Banner & dropdown pemilih bulan.<br>2. 100% selaras dengan tangkapan layar user.<br>3. Aman (*backward compatible*), kolom bersifat nullable. | Tidak perlu menjalankan migrasi database. |
| **Kekurangan** | Memerlukan 1 file migrasi baru (`add_nama_bulan_to_bulan_magang_table`). | Data yang diketik pengguna pada *"Nama bulan"* hilang setelah disimpan; pengalaman pengguna terasa kurang lengkap. |
| **Keputusan** | **DIPILIH (Opsi 1)**. | Ditolak karena mengurangi fungsi input yang ada pada desain. |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `database/migrations/2026_10_19_150000_add_nama_bulan_to_bulan_magang_table.php` | `Create` | Tambahkan kolom `nama_bulan` (string 100, nullable) setelah `id`. |
| `app/Models/BulanMagang.php` | `Modify` | Tambahkan `nama_bulan` ke `$fillable`. |
| `app/Http/Controllers/Admin/KuotaController.php` | `Modify` | Perbarui `storeBulan` untuk memvalidasi `nama_bulan` (nullable string), `status_awal` (`draft` / `dibuka`), serta validasi tanggal mulai magang `after:tgl_tutup`. |
| `resources/views/admin/kuota/index.blade.php` | `Modify` | Terapkan desain modal `#modalBuatBulan` baru: box rounded close button, input field nama bulan, grid tanggal 2 kolom berikon kalender, input tanggal mulai magang dengan helper, segmented button status awal (`✓ Draft` / `Dibuka`), info box callout, dan tombol aksi. |
| `tests/Feature/AdminKuotaRevampTest.php` | `Modify` | Tambahkan pengujian modal popup buat bulan baru dengan status awal Draft/Dibuka dan penyimpanan `nama_bulan`. |

---

## 5. Spesifikasi Komponen Desain Popup Modal

```
┌────────────────────────────────────────────────────────┐
│ Buat bulan baru                                    [X] │
│ Tentukan jadwal pendaftaran dan mulai magang.          │
│                                                        │
│ Nama bulan                                             │
│ [ Contoh: Gelombang 3                                ] │
│                                                        │
│ Tanggal buka                    Tanggal tutup          │
│ [ Pilih tanggal            📅 ] [ Pilih tanggal    📅 ] │
│                                                        │
│ Tanggal mulai magang                                   │
│ [ Pilih tanggal                                    📅 ] │
│ Harus setelah tanggal tutup pendaftaran.               │
│                                                        │
│ Status awal                                            │
│ [ ✓ Draft ]  [ Dibuka ]                                │
│                                                        │
│ ┌────────────────────────────────────────────────────┐ │
│ │ (i) Setelah bulan magang disimpan, atur kuota tiap │ │
│ │     bidang lewat tombol Tambah kuota.              │ │
│ └────────────────────────────────────────────────────┘ │
│                                                        │
│                            [ Batal ]  [ Simpan bulan ] │
└────────────────────────────────────────────────────────┘
```

### Rincian Gaya CSS:
- **Card Modal**: Lebar `max-width: 480px`, background `#FFFFFF`, border-radius `16px`, padding `1.75rem`.
- **Tombol Close**: Kotak `34px x 34px`, border `1px solid #E2E8F0`, border-radius `8px`, warna icon `#64748B`.
- **Input Text & Date**:
  - Tinggi `44px`, border `1px solid #CBD5E1`, border-radius `8px`, padding horizontal `0.875rem`, font-size `0.875rem`.
  - Icon kalender di pojok kanan input.
- **Segmented Radio Pills ("Status Awal")**:
  - Tombol pilihan berjajar horizontal:
    - State Aktif (`Draft`): Border `1.5px solid #2563EB`, background `#EFF6FF`, text `#2563EB`, ikon `✓`.
    - State Normal (`Dibuka`): Border `1px solid #E2E8F0`, background `#FFFFFF`, text `#475569`.
    - Interaksi JS: Klik untuk beralih status secara dinamis, mengupdate hidden input `status_awal`.
- **Callout Box**:
  - Background `#EFF6FF`, border `1px solid #DBEAFE`, border-radius `10px`, padding `0.875rem 1rem`.
  - Ikon info lingkaran `(i)` SVG warna `#2563EB`.
  - Teks ukuran `0.8125rem`, warna `#1E40AF`, font-weight `500`.
- **Tombol Footer**:
  - `Batal`: Background `#FFFFFF`, border `1px solid #CBD5E1`, border-radius `8px`, padding `0.6rem 1.25rem`, warna `#1E293B`, font-weight `600`.
  - `Simpan bulan`: Background `#2563EB` (hover: `#1D4ED8`), border none, border-radius `8px`, padding `0.6rem 1.4rem`, warna `#FFFFFF`, font-weight `700`.

---

## 6. Langkah Implementasi Terstruktur

- [x] **Fase 1: Database & Model**
  - [x] Buat file migrasi `database/migrations/2026_10_19_150000_add_nama_bulan_to_bulan_magang_table.php` untuk menambahkan kolom `nama_bulan` (string, nullable).
  - [x] Jalankan migrasi `php artisan migrate`.
  - [x] Daftarkan atribut `nama_bulan` ke properti `$fillable` di `app/Models/BulanMagang.php`.
- [x] **Fase 2: Backend Controller & Validasi (`app/Http/Controllers/Admin/KuotaController.php`)**
  - [x] Perbarui method `storeBulan` dengan aturan validasi:
    - `nama_bulan`: `['nullable', 'string', 'max:100']`
    - `tgl_buka`: `['required', 'date']`
    - `tgl_tutup`: `['required', 'date', 'after_or_equal:tgl_buka']`
    - `bulan_mulai`: `['required', 'date', 'after:tgl_tutup']`
    - `status_awal`: `['required', 'in:draft,dibuka']`
  - [x] Set atribut `aktif` berdasarkan `status_awal === 'dibuka'`.
  - [x] Catat pembuatan bulan magang ke `AuditService`.
- [x] **Fase 3: Antarmuka Blade (`resources/views/admin/kuota/index.blade.php`)**
  - [x] Rombak struktur HTML `#modalBuatBulan` agar presisi 100% dengan gambar referensi.
  - [x] Tambahkan stylesheet scoped untuk styling input kalender, segmented pills status awal, callout alert, dan close button.
  - [x] Tambahkan JavaScript interaktif untuk:
    - Buka/tutup modal.
    - Toggle segmented button status awal (`draft` vs `dibuka`).
    - Validasi interaktif tanggal buka, tanggal tutup, dan tanggal mulai magang.
  - [x] Perbarui rendering nama bulan di Hero Banner agar menampilkan `nama_bulan` jika ada (contoh: "Gelombang 3" atau "Bulan November 2026").
- [x] **Fase 4: Pengujian & Validasi Otomatis**
  - [x] Perbarui `tests/Feature/AdminKuotaRevampTest.php` untuk memvalidasi:
    - Tampilan elemen modal baru (input `nama_bulan`, field tanggal, segmented button `Draft` / `Dibuka`, callout info box).
    - Penyimpanan bulan magang dengan status Draft (`aktif = false`).
    - Penyimpanan bulan magang dengan status Dibuka (`aktif = true`).
    - Validasi tanggal mulai magang harus setelah tanggal tutup.
  - [x] Jalankan `php artisan test --filter=AdminKuotaRevampTest`.
  - [x] Jalankan tes regresi `AdminTerminologyDesignSystemTest` dan `DashboardModulesRenderingTest`.

---

## 7. Strategi Testing

1. **Feature Test (`AdminKuotaRevampTest`)**:
   - `test_modal_buat_bulan_displays_all_elements_from_design`: Memastikan label `Nama bulan`, input tanggal buka/tutup/mulai magang, segmented pill `Draft` dan `Dibuka`, serta callout box tampil di DOM.
   - `test_admin_can_store_bulan_as_draft_with_nama_bulan`: Memastikan payload dengan `nama_bulan = 'Gelombang 3'` dan `status_awal = 'draft'` tersimpan dengan `aktif = false`.
   - `test_admin_can_store_bulan_as_dibuka_with_nama_bulan`: Memastikan payload dengan `status_awal = 'dibuka'` tersimpan dengan `aktif = true`.
   - `test_validation_rejects_bulan_mulai_before_tgl_tutup`: Memastikan validasi error jika tanggal mulai magang sebelum atau sama dengan tanggal tutup pendaftaran.
2. **Regression Testing**:
   - Menjamin seluruh modul kuota dan dashboard tetap berfungsi 100% tanpa regresi.

---

## 8. Risiko & Mitigasi

- **Risiko**: Nilai `bulan_mulai` di tabel `bulan_magang` sebelumnya memiliki constraint `unique`. Jika admin membuat dua gelombang dalam satu bulan yang sama (misal Gelombang 1 dan Gelombang 2 di November), constraint unique date bisa konflik.
  - **Mitigasi**: Pastikan `bulan_mulai` tetap valid sebagai tanggal mulai magang, dan jika diperlukan untuk multi-gelombang, constraint disesuaikan atau tanggal spesifik (misal 2026-11-02 dan 2026-11-16) diizinkan.
- **Risiko**: Tampilan tanggal native browser berbeda di beberapa platform.
  - **Mitigasi**: Gunakan styling modern dengan custom wrapper dan icon kalender SVG untuk memastikan konsistensi visual di seluruh browser.

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Judul modal adalah `Buat bulan baru` dan deskripsi *"Tentukan jadwal pendaftaran dan mulai magang."*
- [x] Input `Nama bulan` memiliki placeholder *"Contoh: Gelombang 3"*.
- [x] Dua kolom sejajar untuk `Tanggal buka` dan `Tanggal tutup` dengan icon kalender.
- [x] Satu baris `Tanggal mulai magang` dengan petunjuk *"Harus setelah tanggal tutup pendaftaran."*
- [x] Pilihan `Status awal` berupa segmented pills dengan opsi `✓ Draft` (default) dan `Dibuka`.
- [x] Kotak callout biru menampilkan pesan: *"Setelah bulan magang disimpan, atur kuota tiap bidang lewat tombol Tambah kuota."*
- [x] Tombol footer adalah `Batal` dan `Simpan bulan`.
- [x] Tidak ada penggunaan kata "Periode" pada modal ini.
- [x] Seluruh pengujian otomatis di `AdminKuotaRevampTest` lulus 100%.
