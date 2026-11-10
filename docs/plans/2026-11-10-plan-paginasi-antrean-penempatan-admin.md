# Rencana Kerja: Implementasi Paginasi dan Kontrol Jumlah Tampilan Antrean Peserta Belum Ditempatkan

**Tanggal:** 10 November 2026  
**Status:** Selesai (Completed & Verified)  
**Tautan Dokumen Rencana:** `docs/plans/2026-11-10-plan-paginasi-antrean-penempatan-admin.md`  
**Referensi Permintaan Pengguna:**
- Menambahkan bilah informasi jumlah tampilan dan kontrol paginasi (per berapa orang: 5, 10, 25) pada tabel **Antrean Peserta Lolos Berkas (Menunggu Penempatan)** di halaman Penempatan Admin (`/admin/penempatan?tab=belum`), persis seperti pada tangkapan layar SS 2 yang sudah ada di Tab Penempatan Aktif.
- Dokumen acuan standar: `AGENTS.md` (PHP 8.3, Laravel 10, Eloquent Paginator, Zero-Regression, PSR-12).

---

## 1. Analisis Kebutuhan & Kondisi Saat Ini

Berdasarkan 2 gambar yang diunggah pengguna:
1. **SS 1 (`media_1794307495053_9dabcb29.png`)**:
   - Menampilkan tabel *Antrean Peserta Lolos Berkas (Menunggu Penempatan)* di `/admin/penempatan?tab=belum`.
   - Di bagian bawah tabel, saat ini belum terdapat bilah footer kontrol paginasi (`adm-table-footer`).
2. **SS 2 (`media_1794307519839_899662f5.png`)**:
   - Menampilkan acuan komponen paginasi standar MagKom yang sudah terpasang di Tab 1 (*Penempatan Aktif*):
     - **Baris 1**: `Menampilkan X - Y dari Z peserta magang` dengan badge identitas pill (misal: `[✓ Antrean Penempatan]`), serta tombol pilihan `Menampilkan: [5] 10 25`.
     - **Baris 2**: `Lembar Halaman X dari Y` serta navigasi `« Prev`, nomor lembar, dan `Next »`.

### Identifikasi Teknis:
- **Di Controller (`app/Http/Controllers/Admin/PenempatanController.php`)**:
  - Tab 1 menggunakan:
    ```php
    $penempatanList = $query->paginate($perPage)->withQueryString();
    ```
  - Namun query antrean Tab 2 saat ini masih menggunakan `->get()`:
    ```php
    $antreanBelumDitempatkan = $antreanQuery->get();
    ```
    sehingga variabel `$antreanBelumDitempatkan` bertipe `Illuminate\Database\Eloquent\Collection` (bukan `LengthAwarePaginator`).
- **Di Blade (`resources/views/admin/penempatan/index.blade.php`)**:
  - Tab 1 merender komponen `<x-gentelella.pagination-bar :paginator="$penempatanList" ... />` di dalam `<div class="adm-table-footer">`.
  - Tab 2 belum memanggil `<x-gentelella.pagination-bar>`.

---

## 2. Rencana Solusi Teknis

### A. Backend (`app/Http/Controllers/Admin/PenempatanController.php`)
1. Mengubah pengambilan data antrean dari:
   ```php
   $antreanBelumDitempatkan = $antreanQuery->get();
   ```
   menjadi paginasi standar:
   ```php
   $antreanBelumDitempatkan = $antreanQuery->paginate($perPage)->withQueryString();
   ```
2. Parameter `$perPage` yang sudah tervalidasi (`5`, `10`, `25`, `50`) akan berlaku konsisten pada kedua tab, dan method `withQueryString()` memastikan filter pencarian nama (`q`), jurusan (`jurusan_id`), bidang (`bidang_id`), serta bulan magang (`bulan_magang_id`) tetap terjaga utuh saat berpindah halaman atau mengganti opsi jumlah data per halaman.

### B. View Presentation (`resources/views/admin/penempatan/index.blade.php`)
1. Pada kartu tabel Antrean Peserta Belum Ditempatkan (`#panel-penempatan-belum`), tepat di bawah tabel penempatan, kita sematkan footer paginasi:
   ```blade
   @if($antreanBelumDitempatkan->isNotEmpty())
       <div class="adm-table-footer">
           <x-gentelella.pagination-bar 
               :paginator="$antreanBelumDitempatkan" 
               :perPageOptions="[5, 10, 25]" 
               entity="peserta magang" 
               badgeText="Antrean Penempatan" 
           />
       </div>
   @endif
   ```
2. Styling `.adm-table-footer` secara otomatis terintegrasi dengan desain kartu admin (`border-top: 1.5px solid #CBD5E1; background: #F8FAFC;`), serasi dengan tema terang maupun tema gelap (*Dark Mode*).

---

## 3. Rencana Berkas yang Disentuh

| Berkas | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `app/Http/Controllers/Admin/PenempatanController.php` | **Modify** | Mengubah `$antreanBelumDitempatkan` menjadi paginator berbasis `$perPage` dengan `withQueryString()`. |
| `resources/views/admin/penempatan/index.blade.php` | **Modify** | Menambahkan footer paginasi `<x-gentelella.pagination-bar>` pada tabel antrean peserta belum ditempatkan. |
| `tests/Feature/AdminTablePaginationStandardTest.php` | **Modify** | Menambahkan unit/feature assertion untuk memastikan tab `belum` memiliki paginasi default 5 dan dapat memilih 10/25 data per halaman. |

---

## 4. Langkah Implementasi (Checklist)

- [x] **Langkah 1**: Perbarui `app/Http/Controllers/Admin/PenempatanController.php` & `AdminPenempatanController.php`:
  - Ubah query antrean belum ditempatkan dari `->get()` ke `->paginate($perPage)->withQueryString()`.
- [x] **Langkah 2**: Perbarui `resources/views/admin/penempatan/index.blade.php`:
  - Tambahkan container `<div class="adm-table-footer">` dengan komponen `<x-gentelella.pagination-bar>` setelah tabel antrean peserta.
- [x] **Langkah 3**: Perbarui test `tests/Feature/AdminTablePaginationStandardTest.php`:
  - Tambahkan pengujian `test_admin_penempatan_belum_tab_defaults_to_5_per_page_and_accepts_custom_per_page`.
- [x] **Langkah 4**: Kompilasi Aset Frontend:
  - Jalankan `npm run build` untuk memverifikasi tidak ada konflik aset.
- [x] **Langkah 5**: Quality Gate & Zero-Regression Verification:
  - Jalankan `vendor/bin/phpunit tests/Feature/AdminTablePaginationStandardTest.php tests/Feature/AdminPenempatanDuaTahapTest.php tests/Feature/AdminChildPagesVisualTest.php`.
  - Jalankan `./vendor/bin/pint --test` untuk memastikan kerapian kode PSR-12.

---

## 5. Kriteria Selesai (Definition of Done)

- [x] Tab *Antrean Peserta Lolos Berkas (Menunggu Penempatan)* menampilkan footer paginasi standar MagKom persis seperti SS 2.
- [x] Menampilkan teks counter data: `Menampilkan X - Y dari Z peserta magang` dengan badge `[✓ Antrean Penempatan]`.
- [x] Pilihan tombol `Menampilkan: [5] 10 25` berfungsi dengan benar mengubah jumlah data per halaman.
- [x] Filter tab `tab=belum`, pencarian nama, jurusan, bidang, dan bulan magang tetap dipertahankan (*preserved*) di URL saat paginasi diklik.
- [x] Semua pengujian PHPUnit (`AdminTablePaginationStandardTest`, `AdminPenempatanDuaTahapTest`, dsb.) lulus 100% tanpa regresi.

