# Rencana Kerja: Pembersihan Badge Status & Penyelarasan Tombol Terbitkan dan Rollback di Canvas Site Editor

**Dokumen Rencana:** `docs/plans/2026-11-24-perbaikan-alur-tombol-terbitkan-dan-rollback-site-editor.md`  
**Tanggal:** 24 November 2026  
**Status:** Menunggu Persetujuan Pengguna  

---

## 1. Tujuan

1. **Pembersihan Badge Status di Header**: Menghapus dan membersihkan seluruh elemen status pill draf (`● ✓ Tersimpan (...)` / `⚠️ Belum Disimpan`) dari topbar Site Editor agar tampilan header kembali bersih, minimalis, dan tidak memicu kebingungan.
2. **Fokus Penuh pada Dua Tombol Aksi (Terbitkan & Rollback)**:
   - **Tombol Terbitkan**:
     - SuperAdmin klik "Terbitkan" langsung di topbar Site Editor.
     - Muncul pop-up konfirmasi penerbitan publik.
     - Sistem langsung mempublikasikan seluruh draf ke publik (tanpa dialihkan ke halaman pratinjau terlebih dahulu).
     - Menampilkan pop-up / notifikasi bahwa publikasi telah berhasil.
     - Memuat ulang halaman situs (`window.location.reload()`) secara mulus, dan menampilkan kembali notifikasi sukses pasca-reload agar SuperAdmin memiliki kepastian visual penuh bahwa perubahan telah live.
   - **Tombol Rollback**:
     - SuperAdmin klik "Rollback".
     - Muncul pop-up konfirmasi pemulihan ke versi sebelum publikasi terakhir.
     - Sistem memulihkan backup live publik.
     - Menampilkan notifikasi / pop-up sukses rollback dan memuat ulang situs.
     - Jika tidak ada backup yang tersedia, memunculkan notifikasi peringatan tanpa reload.

---

## 2. Asumsi & Batasan Kerja

1. **Penghapusan Total Badge Status Header**: Elemen `#wp-save-status` beserta container `.wp-status-container` di topbar header dihapus sepenuhnya, baik dari Blade, CSS, maupun manipulasi DOM di JavaScript.
2. **Publikasi Langsung (Single-Action Publish)**: Alur penerbitan tidak lagi memerlukan navigasi ke tab pratinjau (`/cms-landing/preview`) untuk menekan tombol "Publikasikan ke Publik" terpisah. Tombol "Terbitkan" di topbar Site Editor adalah aksi langsung dan tunggal.
3. **Penyimpanan Otomatis (Pre-Publish Auto-save)**: Sebelum request publikasi dieksekusi ke backend, sistem secara otomatis memastikan perubahan draf terakhir yang ada di memori disimpan terlebih dahulu agar tidak ada data yang tercecer.
4. **Persistensi Notifikasi Pasca-Reload**: Karena halaman akan memuat ulang (`window.location.reload()`), notifikasi sukses disimpan di `sessionStorage` sebelum reload dan dipicu secara otomatis saat halaman baru selesai dimuat.

---

## 3. Pendekatan Teknis

### Perbandingan Pendekatan

| Kriteria | Opsi A: Mengandalkan Browser Default Dialog (`alert` / `confirm`) | Opsi B: In-App Pop-up Dialog & Toast Persisten (Direkomendasikan) |
| :--- | :--- | :--- |
| **Pembersihan Header** | Badge dihapus dari Blade saja, masih meninggalkan kode sisa di JS. | Badge dibersihkan total dari Blade, CSS, dan JS agar tidak ada error referensi `null`. |
| **Pengalaman Pop-up** | Kotak dialog browser terlihat kaku dan tidak dapat menampilkan spinner loading atau pesan sukses pasca-reload. | Pop-up modal in-app yang elegan dengan tombol konfirmasi jelas, indikator loading spinner saat memproses, serta pop-up/toast sukses. |
| **Kejelasan Status** | Pengguna sering ragu apakah data sudah benar-benar terbit setelah halaman me-reload. | Notifikasi pop-up/toast hijau muncul seketika setelah reload dari `sessionStorage`, memberikan konfirmasi nyata bahwa data sudah terbit ke publik. |

**Keputusan:** Memilih **Opsi B** untuk menjamin antarmuka yang bersih, profesional, dan memberikan kepastian penuh bagi SuperAdmin.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/superadmin/site-editor/partials/_topbar.blade.php` | Modify | 1. **Hapus** elemen `.wp-status-container` dan `#wp-save-status` (membersihkan badge pill).<br>2. Pastikan tombol `#btn-publish` dan `#btn-rollback` terpasang rapi dan mandiri di grup aksi topbar. |
| `resources/views/superadmin/site-editor/index.blade.php` | Modify | Menambahkan kontainer modal pop-up konfirmasi & notifikasi aksi (`#wp-action-dialog-modal`) yang bersih tanpa inline script/style. |
| `resources/css/pages/superadmin/site-editor.css` | Modify | 1. Bersihkan aturan styling `.wp-status-container` dan `.wp-save-status-pill`.<br>2. Tambahkan styling scoped untuk modal pop-up dialog konfirmasi & notifikasi sukses (`.wp-action-modal`). |
| `resources/js/pages/superadmin/site-editor/editor-app.js` | Modify | 1. Hapus inisialisasi dinamis dan listener DOM yang mencari `#wp-save-status`.<br>2. Implementasi pengendali pop-up konfirmasi dan eksekusi untuk tombol **Terbitkan** (`#btn-publish`).<br>3. Implementasi pengendali pop-up konfirmasi dan eksekusi untuk tombol **Rollback** (`#btn-rollback`).<br>4. Implementasi penyimpanan notifikasi ke `sessionStorage` sebelum `window.location.reload()`, dan pemanggilan pop-up/toast pasca-reload. |
| `resources/js/pages/superadmin/site-editor/editor-state.js` | Modify | Menghapus dependensi update DOM `#wp-save-status` pada method `updateStatusPill()` agar tidak melempar error saat elemen tidak lagi ada di header. |
| `app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php` | Modify | Memastikan method `publish` dan `rollback` mengembalikan payload JSON sukses yang jelas serta fallback redirect yang aman jika dipanggil di luar AJAX. |
| `tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php` | Modify | Penyesuaian pengujian agar memverifikasi topbar yang bersih tanpa badge serta memastikan publish dan rollback sukses. |

---

## 5. Langkah Implementasi Bertahap

### Tahap 1: Pembersihan Badge Status Header (Blade & CSS)
- [x] Buka `resources/views/superadmin/site-editor/partials/_topbar.blade.php`.
- [x] Hapus blok markup `.wp-status-container` dan `#wp-save-status`.
- [x] Buka `resources/css/pages/superadmin/site-editor.css`, bersihkan selector `.wp-status-container`, `.wp-save-status-pill`, dan keyframes terkait.

### Tahap 2: Pembersihan Referensi DOM di JavaScript
- [x] Buka `resources/js/pages/superadmin/site-editor/editor-app.js`, hapus kode injeksi dinamis `statusPill = document.createElement('span')` dan semua manipulasi DOM pada `wp-save-status`.
- [x] Buka `resources/js/pages/superadmin/site-editor/editor-state.js`, rapikan `updateStatusPill()` agar tidak lagi mengakses elemen `#wp-save-status`.

### Tahap 3: Implementasi Modal Pop-up Konfirmasi & Sukses untuk 2 Tombol
- [x] Buka `resources/views/superadmin/site-editor/index.blade.php`, tambahkan struktur modal dialog `#wp-action-dialog-modal` (Backdrop, Dialog Box, Icon, Judul, Keterangan, Loading Spinner, Tombol Batal & Aksi Utama).
- [x] Tambahkan styling `.wp-action-dialog-modal` di `resources/css/pages/superadmin/site-editor.css`.
- [x] Di `editor-app.js`, buat logika interaksi:
  1. **Tombol Terbitkan (`#btn-publish`)**:
     - Klik tombol &rarr; Pop-up konfirmasi: *"Publikasikan Seluruh Perubahan ke Publik?"*.
     - Klik "Ya, Terbitkan" &rarr; Pop-up berubah menampilkan animasi loading *"Menyimpan dan mempublikasikan..."*.
     - Simpan memori draf & POST ke `/superadmin/site-editor/publish`.
     - Saat respons `success: true`:
       - Simpan ke `sessionStorage.setItem('magkom_editor_flash', JSON.stringify({ type: 'success', message: '✓ Berhasil Diterbitkan! Seluruh perubahan telah aktif di halaman publik.' }))`.
       - Tampilkan pesan sukses pada pop-up, lalu dalam 1.2 detik jalankan `window.location.reload()`.
  2. **Tombol Rollback (`#btn-rollback`)**:
     - Klik tombol &rarr; Pop-up konfirmasi peringatan: *"Kembalikan ke Versi Sebelum Terbit Terakhir?"*.
     - Klik "Ya, Rollback" &rarr; Pop-up menampilkan animasi loading *"Memulihkan data backup publik..."*.
     - POST ke `/superadmin/site-editor/rollback`.
     - Saat respons `success: true`:
       - Simpan ke `sessionStorage.setItem('magkom_editor_flash', JSON.stringify({ type: 'success', message: '✓ Berhasil Rollback! Tampilan publik dikembalikan ke versi sebelum publikasi terakhir.' }))`.
       - Jalankan `window.location.reload()`.
     - Jika respons `success: false` (misal 404 belum ada backup):
       - Tampilkan pesan error di pop-up tanpa reload halaman.
  3. **Handler Notifikasi Pasca-Reload**:
     - Di awal fungsi `initEditorApp()`, periksa `sessionStorage.getItem('magkom_editor_flash')`.
     - Jika ditemukan, tampilkan pop-up toast konfirmasi sukses selama 4 detik, lalu hapus dari `sessionStorage`.

### Tahap 4: Kompilasi Aset & Pengujian Quality Gates
- [x] Jalankan `npm run build` untuk mengompilasi CSS dan JS terbaru.
- [x] Bersihkan cache Laravel: `php artisan view:clear; php artisan route:clear`.
- [x] Jalankan test suite: `php artisan test --filter=LandingCmsSiteEditorTest` dengan PHP 8.3.
- [x] Jalankan linter dan formatting: `vendor/bin/pint --test`.
- [x] Jalankan analisis statis: `vendor/bin/phpstan analyse --memory-limit=1G`.

---

## 6. Strategi Testing

1. **Uji Tampilan Header**:
   - Header topbar tidak lagi menampilkan pill badge tanggal/tersimpan yang mengganggu.
   - Tombol "Simpan Draf", "Terbitkan", dan "Rollback" tertata rapi.
2. **Uji Tombol Terbitkan (Direct Publish)**:
   - Klik "Terbitkan" &rarr; Modal pop-up konfirmasi muncul.
   - Konfirmasi &rarr; Loading spinner &rarr; Pop-up sukses &rarr; Reload halaman.
   - Pasca reload &rarr; Pop-up toast hijau muncul menyatakan berhasil diterbitkan.
   - Halaman publik (`/`) memuat data live terbaru yang telah dipublikasikan.
3. **Uji Tombol Rollback**:
   - Klik "Rollback" &rarr; Modal konfirmasi muncul.
   - Konfirmasi &rarr; Loading spinner &rarr; Pop-up sukses &rarr; Reload halaman.
   - Pasca reload &rarr; Pop-up toast hijau muncul menyatakan berhasil di-rollback.
   - Halaman publik (`/`) kembali ke versi backup sebelum terbit terakhir.
4. **Uji Kasus Gagal**:
   - Jika rollback ditekan saat tidak ada backup &rarr; Pop-up menampilkan pesan error tanpa me-reload.

---

## 7. Kriteria Selesai (Definition of Done)

- [x] Status pill badge `● ✓ Tersimpan (...)` di topbar header telah dihapus bersih.
- [x] Tombol Terbitkan langsung menerbitkan draf ke publik tanpa perlu masuk ke tab pratinjau.
- [x] Pop-up konfirmasi dan notifikasi berhasil muncul saat klik Terbitkan, lalu me-reload situs.
- [x] Notifikasi sukses tetap tampil setelah reload selesai (`sessionStorage`).
- [x] Tombol Rollback memiliki pop-up konfirmasi, notifikasi sukses, dan reload yang selaras.
- [x] Seluruh test `LandingCmsSiteEditorTest` (18+ tests) lulus dengan PHP 8.3.
- [x] Hasil lint Pint dan PHPStan bersih tanpa error.
