# Technical Plan: Penyederhanaan Opsi Urutan Data dan Placeholder Pencarian pada Modul Administrasi Pendaftaran

**Dokumen**: `docs/plans/2026-11-06-penyederhanaan-filter-urutan-dan-pencarian-administrasi.md`  
**Author**: Senior Frontend & Backend Architect  
**Tanggal**: 6 November 2026  
**Status**: Selesai Diimplementasikan & Terverifikasi Penuh  
**Aturan Tata Kelola**: Sesuai [`AGENTS.md`](file:///d:/laragon/www/MAGKOM/.agents/rules/AGENTS.md) dan skill `web-planning`  

---

## 1. Tujuan & Latar Belakang

Berdasarkan instruksi pengguna dan tangkapan layar yang dilampirkan:
1. **Gambar 1 (Urutan Data)**:
   - Menghapus opsi `FIFO (Terawal Masuk)` dari dropdown "Urutan Data" pada filter administrasi pendaftaran.
   - Pilihan urutan disederhanakan hanya menjadi 2 opsi:
     - **Paling Baru Dibuat** (`terbaru`) — disetel sebagai *default order*.
     - **Paling Lama Dibuat** (`terlama`).
2. **Gambar 2 (Pencarian Calon Peserta)**:
   - Menyederhanakan teks placeholder pada kolom input pencarian:
     - **Sebelum**: `Cari nama, no. registrasi (#REG-...), universitas, atau NIM...`
     - **Sesudah**: **`Cari nama...`** (menghilangkan teks `no. registrasi`, `universitas`, dan `NIM`).

Perubahan ini bertujuan membuat antarmuka filter pada halaman pendaftaran administrasi (`admin.pendaftaran.index`) lebih bersih, intuitif, dan tidak membebani kognitif administrator.

---

## 2. Asumsi Kerja & Analisis Dampak

1. **Default Sorting**:
   - Ketika administrator membuka halaman daftar permohonan magang tanpa parameter URL `sort`, urutan *default* yang digunakan adalah **Paling Baru Dibuat** (`created_at DESC, id DESC`).
   - Opsi `terlama` mengurutkan pendaftaran dari yang paling lama dibuat (`created_at ASC, id ASC`).
   - Kode pengurutan lama `fifo` di Controller tetap dapat di-handle sebagai alias aman (`fallback`) ke `terbaru` atau `terlama` untuk mencegah error URL bookmark lama.
2. **Backend Query Pencarian**:
   - Query pencarian di controller tetap mempertahankan pencarian nama calon peserta `where('nama', 'like', $search)` dengan efisiensi tinggi, sementara placeholder input di UI disederhanakan murni menjadi `Cari nama...`.
3. **Penyelarasan Automated Test**:
   - Test suite `tests/Feature/AdminSuperAdminPhaseFourTest.php` yang sebelumnya menguji urutan default FIFO akan diselaraskan dengan urutan default `terbaru` dan pengurutan `terlama`.

---

## 3. Evaluasi Opsi Teknis

### Opsi A (Rekomendasi Utama): Sederhanakan Dropdown Menjadi 2 Opsi & Default `terbaru`
- **UI**:
  ```blade
  <select name="sort" id="select-sort" class="adm-select">
      <option value="terbaru" {{ request('sort', 'terbaru') === 'terbaru' ? 'selected' : '' }}>Paling Baru Dibuat</option>
      <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Paling Lama Dibuat</option>
  </select>
  ```
- **Placeholder**:
  ```blade
  <input type="text" id="search-input" name="q" value="{{ request('q') }}" placeholder="Cari nama..." class="adm-input-search">
  ```
- **Backend Controller**:
  ```php
  $sort = $request->input('sort', 'terbaru');
  if ($sort === 'terlama') {
      $query->orderBy('created_at', 'asc')->orderBy('id', 'asc');
  } else {
      $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
  }
  ```
- **Kelebihan**: Sangat bersih, intuitif, konsisten antara frontend dan backend, serta administrator langsung melihat permohonan terkini di baris teratas.

---

## 4. Daftar Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/admin/pendaftaran/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php) | **Modify** | 1. Hapus opsi `fifo` pada `<select name="sort">` dan jadikan `terbaru` sebagai nilai default.<br>2. Ubah placeholder input search menjadi `Cari nama...`. |
| [`app/Http/Controllers/Admin/PendaftaranController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/PendaftaranController.php) | **Modify** | Sesuaikan urutan default pencarian dari `fifo` ke `terbaru` (`created_at DESC, id DESC`), dan urutan `terlama` (`created_at ASC, id ASC`). |
| [`tests/Feature/AdminSuperAdminPhaseFourTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminSuperAdminPhaseFourTest.php) | **Modify** | Sesuaikan pengujian pengurutan data pendaftaran dengan urutan default terbaru dan pengurutan terlama. |

---

## 5. Rencana Langkah Kerja Terperinci (Step-by-Step Task Checklist)

- [x] **Langkah 1 (Controller Logic)**:
  - Pada [`app/Http/Controllers/Admin/PendaftaranController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/PendaftaranController.php), sesuaikan penanganan `$sort`:
    - Set default ke `'terbaru'`.
    - Jika `'terlama'`, urutkan `created_at ASC, id ASC`.
    - Selain itu, urutkan `created_at DESC, id DESC`.
- [x] **Langkah 2 (Blade View - Placeholder & Dropdown Sort)**:
  - Pada [`resources/views/admin/pendaftaran/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php):
    - Ganti `placeholder="Cari nama, no. registrasi (#REG-...), universitas, atau NIM..."` menjadi `placeholder="Cari nama..."`.
    - Hapus `<option value="fifo">FIFO (Terawal Masuk)</option>`.
    - Sisakan hanya opsi:
      - `<option value="terbaru" {{ request('sort', 'terbaru') === 'terbaru' ? 'selected' : '' }}>Paling Baru Dibuat</option>`
      - `<option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Paling Lama Dibuat</option>`
- [x] **Langkah 3 (Automated Testing)**:
  - Selaraskan assertion pada `tests/Feature/AdminSuperAdminPhaseFourTest.php` agar memvalidasi pengurutan `terbaru` (default) dan `terlama`.
- [x] **Langkah 4 (Quality Gate)**:
  - Jalankan `php artisan test tests/Feature/AdminSuperAdminPhaseFourTest.php tests/Feature/AdminSuperAdminPortalTest.php` (17 passed, 83 assertions).
  - Jalankan `./vendor/bin/pint --test` (Passed).
  - Jalankan `php -l resources/views/admin/pendaftaran/index.blade.php` (No syntax errors).
  - Jalankan `npm run build` (Built in 1.22s).

---

## 6. Permohonan Persetujuan Perubahan Markup Blade (Sesuai AGENTS.md Bagian 3.3)

Sesuai aturan ketat **AGENTS.md Bagian 3.3**:
1. **Mengapa Perubahan Markup Diperlukan**: Mengubah teks placeholder input dan menghapus elemen `<option value="fifo">` memerlukan modifikasi langsung pada file presentasi Blade.
2. **File Target**: [`resources/views/admin/pendaftaran/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php) pada baris input pencarian dan elemen `<select name="sort">`.
3. **Jaminan Integritas**:
   - Tidak ada inline CSS atau inline JS baru.
   - Styling form control 42px dan responsivitas tetap terjaga.

