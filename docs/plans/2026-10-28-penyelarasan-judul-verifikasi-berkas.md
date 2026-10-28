# [PLAN] Penyelarasan Judul Halaman: "Seleksi & Verifikasi Berkas" Menjadi "Verifikasi Berkas"

Dokumen rencana kerja ini disusun berdasarkan permintaan pengguna dan telaah tangkapan layar antarmuka Admin Portal MagKom Sleman.

---

## 1. Tujuan
Menyelaraskan teks judul halaman pendaftaran admin pada [resources/views/admin/pendaftaran/index.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php) dari **"Seleksi & Verifikasi Berkas"** menjadi **"Verifikasi Berkas"**, agar 100% konsisten dengan label menu navigasi sidebar (`Verifikasi Berkas`), judul tab browser (`@section('title', 'Verifikasi Berkas Pendaftaran - Admin MagKom Sleman')`), dan desain sistem MagKom secara keseluruhan.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. **Deskripsi Subtitle Tetap**: Deskripsi di bawah judul utama (`description="Pusat validasi berkas pendaftaran calon peserta magang dan monitoring SLA permohonan dinas."`) tetap dipertahankan karena sudah sangat akurat menjelaskan fungsi pengawasan SLA dan validasi dokumen.
2. **Kesesuaian Halaman Detail**: Pada [resources/views/admin/pendaftaran/show.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php), teks breadcrumb `Antrean Verifikasi` dapat diselaraskan menjadi `Verifikasi Berkas` demi konsistensi hierarki navigasi.
3. **Cakupan Perubahan**: Perubahan bersifat murni presentasional antarmuka (UI/Blade template) dan pengujian otomatis (Feature Test), tanpa menyentuh skema database, controller, maupun routing.

---

## 3. Pendekatan Teknis & Evaluasi Alternatif

| Aspek | Opsi A: Penyelarasan Terfokus pada Komponen Page-Title & Breadcrumb (Direkomendasikan) | Opsi B: Mengubah Menjadi String Konfigurasi/Localization Dinamis |
| :--- | :--- | :--- |
| **Deskripsi** | Mengubah langsung atribut `title="Verifikasi Berkas"` pada komponen `<x-gentelella.page-title>` di `index.blade.php` serta breadcrumbs di `show.blade.php`. | Membuat key baru di file bahasa/konfigurasi `lang/id/admin.php` lalu memanggilnya melalui `__('admin.verifikasi_berkas')`. |
| **Kelebihan** | - Cepat, bersih, dan langsung sesuai pola kode Blade yang sudah ada di seluruh modul Gentelella MagKom.<br>- Zero overhead komputasi dan tidak berisiko merusak modul lain. | Memudahkan lokalisasi di masa depan jika ada dukungan multi-bahasa penuh. |
| **Kekurangan** | Tidak terpusat dalam satu berkas bahasa (namun saat ini modul admin MagKom sudah terbiasa dengan string UI Indonesia langsung). | Menambahkan kompleksitas file baru yang tidak diperlukan untuk perubahan teks tunggal. |
| **Keputusan** | **Dipilih Opsi A** karena konsisten dengan arsitektur antarmuka Blade Gentelella yang aktif di repositori saat ini. | Ditolak karena over-engineering untuk perbaikan label halaman. |

---

## 4. Perubahan Berkas (Files to Modify)

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [resources/views/admin/pendaftaran/index.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php) | **Modify** | Ubah atribut `title="Seleksi & Verifikasi Berkas"` menjadi `title="Verifikasi Berkas"` pada baris 792. |
| [resources/views/admin/pendaftaran/show.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php) | **Modify** | Selaraskan breadcrumb navigasi dari `Antrean Verifikasi` menjadi `Verifikasi Berkas`. |
| [tests/Feature/AdminTerminologyDesignSystemTest.php](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminTerminologyDesignSystemTest.php) | **Modify** | Tambahkan pengujian otomatis bahwa `admin.pendaftaran.index` menampilkan `Verifikasi Berkas` dan tidak lagi memuat `Seleksi & Verifikasi Berkas`. |

---

## 5. Langkah Implementasi Terstruktur

- [x] **Langkah 1: Perbarui Judul Halaman di `index.blade.php`**
  - Buka file [resources/views/admin/pendaftaran/index.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php).
  - Pada baris 792, perbarui:
    ```blade
    {{-- Sebelum --}}
    <x-gentelella.page-title title="Seleksi & Verifikasi Berkas" description="Pusat validasi berkas pendaftaran calon peserta magang dan monitoring SLA permohonan dinas.">

    {{-- Sesudah --}}
    <x-gentelella.page-title title="Verifikasi Berkas" description="Pusat validasi berkas pendaftaran calon peserta magang dan monitoring SLA permohonan dinas.">
    ```

- [x] **Langkah 2: Selaraskan Breadcrumb di `show.blade.php`**
  - Buka file [resources/views/admin/pendaftaran/show.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php).
  - Pada bagian `@section('breadcrumbs')`, perbarui teks link dari `Antrean Verifikasi` menjadi `Verifikasi Berkas`.

- [x] **Langkah 3: Tambahkan Automated Feature Test**
  - Buka file [tests/Feature/AdminTerminologyDesignSystemTest.php](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminTerminologyDesignSystemTest.php) dan [tests/Feature/AdminChildPagesVisualTest.php](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminChildPagesVisualTest.php).
  - Tambahkan assertion untuk memverifikasi teks `Verifikasi Berkas` tampil dan teks `Seleksi & Verifikasi Berkas` tidak muncul.

- [x] **Langkah 4: Bersihkan View Cache & Verifikasi Hasil**
  - Jalankan perintah `php artisan view:clear` untuk memastikan template Blade yang ter-cache diperbarui.
  - Jalankan `php artisan test --filter=AdminChildPagesVisualTest` untuk memverifikasi seluruh test lolos 100%.

---

## 6. Strategi Testing

1. **Automated Testing**:
   - Jalankan `php artisan test --filter=AdminTerminologyDesignSystemTest`.
   - Jalankan `php artisan test --filter=AdminChildPagesVisualTest` untuk memastikan visual antrean tetap berfungsi normal.
2. **Manual Visual Verification**:
   - Akses rute `/admin/pendaftaran` di browser.
   - Verifikasi bahwa judul besar halaman kini tertulis **Verifikasi Berkas**, persis sama dengan label di sidebar kiri.

---

## 7. Risiko & Mitigasi

- **Risiko Breaking Change**: Tidak ada risiko breaking change fungsional, routing, maupun relasi database.
- **Risiko Cache View**: Jika browser atau Laravel masih menyimpan view terkompilasi lama, teks belum langsung berubah.
  - *Mitigasi*: Jalankan `php artisan view:clear` segera setelah perubahan diterapkan.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Judul halaman di `/admin/pendaftaran` tampil sebagai **"Verifikasi Berkas"**.
- [x] Teks "Seleksi & Verifikasi Berkas" tidak lagi muncul di halaman index pendaftaran admin.
- [x] Breadcrumb pada halaman detail pendaftaran admin konsisten menggunakan **"Verifikasi Berkas"**.
- [x] Seluruh automated test (`php artisan test --filter=AdminChildPagesVisualTest`) lulus tanpa kegagalan.

