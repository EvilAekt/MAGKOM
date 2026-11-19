# Plan: Dekomisioning Total & Penghapusan Bersih Fitur Peta Alur Interaktif

Dokumen ini merinci rencana kerja dan catatan eksekusi dekomisioning total serta penghapusan fitur **Peta Alur Interaktif** dari seluruh repositori MagKom, merespons persetujuan dan mandat eksplisit pengguna: *"gas dan benar2 di hapus di project ini"*.

---

## 1. Tujuan
1. **Pembersihan Menu & Tombol di Landing Page**:
   - Menghapus item menu navigasi **"Peta Alur"** (`#peta-alur`) dari navbar header landing page.
   - Menghapus tombol aksi **"Peta Alur Interaktif"** (`btn-tahapan-peta`) dan anchor `<span id="peta-alur"></span>` pada blok tahapan.
   - Menjadikan tombol **"Unduh Panduan PDF"** sebagai fokus aksi tunggal (*primary CTA*) yang elegan.
2. **Pembersihan Backend, Rute, dan Controller**:
   - Menghapus rute `GET /peta-alur` (`peta-alur`) dari `routes/web.php`.
   - Menghapus controller method `AlurMagangController@petaInteraktif`.
   - Menghapus penanganan halaman preview `peta-alur` pada `LandingCmsController` dan dropdown preview di `_topbar.blade.php`.
3. **Pembersihan Berkas View & Aset Frontend**:
   - Menghapus berkas view `resources/views/public/peta-alur-interaktif.blade.php`.
   - Menghapus berkas partial lama `resources/views/public/partials/_peta_alur_modal.blade.php`.
   - Menghapus berkas CSS & JS: `resources/css/pages/public/peta-alur.css`, `resources/js/pages/public/peta-alur.js`, dan `public/css/peta-alur-fullscreen.css`.
   - Menghapus entri berkas peta alur dari `vite.config.js`.
4. **Pembaruan Test Suite & Quality Gate**:
   - Menyesuaikan `LandingPageTest` agar memverifikasi ketiadaan fitur peta alur.
   - Menghapus `tests/Feature/PetaAlurPageTest.php` dan `tests/Feature/LandingPagePetaAlurModalTest.php`.
   - Membersihkan referensi rute `peta-alur` di `LanguageLocalizationTest`, `SecurityHeadersProductionTest`, dan `CanvasEditorConsolidatedFeaturesTest`.
   - Menjalankan `npm run build` dan `php artisan test` untuk memastikan 100% lulus tanpa regresi.

---

## 2. Perubahan File

| File / Direktori | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/landing.blade.php` | Modify | Hapus 'Peta Alur' dari `$defaultHeaderNav` dan filter runtime pemrosesan navbar. |
| `resources/views/public/blocks/tahapan.blade.php` | Modify | Hapus tombol `btn-tahapan-peta` dan anchor `#peta-alur`. |
| `app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php` | Modify | Hapus 'Peta Alur' dari default header nav. |
| `routes/web.php` | Modify | Hapus rute `GET /peta-alur`. |
| `app/Http/Controllers/Public/AlurMagangController.php` | Modify | Hapus method `petaInteraktif()`. |
| `app/Http/Controllers/SuperAdmin/LandingCmsController.php` | Modify | Hapus percabangan `$page === 'peta-alur'`. |
| `resources/views/superadmin/site-editor/partials/_topbar.blade.php` | Modify | Hapus `<option value="peta-alur">Peta Alur (/peta-alur)</option>`. |
| `vite.config.js` | Modify | Hapus `peta-alur.css` dan `peta-alur.js` dari build entry points. |
| `resources/views/public/peta-alur-interaktif.blade.php` | Delete | Hapus berkas view mandiri peta alur. |
| `resources/views/public/partials/_peta_alur_modal.blade.php` | Delete | Hapus berkas partial modal peta alur lama. |
| `resources/css/pages/public/peta-alur.css` | Delete | Hapus berkas CSS peta alur. |
| `resources/js/pages/public/peta-alur.js` | Delete | Hapus berkas JS peta alur. |
| `public/css/peta-alur-fullscreen.css` | Delete | Hapus berkas CSS legacy jika ada. |
| `tests/Feature/PetaAlurPageTest.php` | Delete | Hapus test suite halaman peta alur. |
| `tests/Feature/LandingPagePetaAlurModalTest.php` | Delete | Hapus test modal lama. |
| `tests/Feature/LandingPageTest.php` | Modify | Perbarui assertion ketiadaan peta alur di landing page. |
| `tests/Feature/LanguageLocalizationTest.php` | Modify | Hapus rute `/peta-alur` dari array pengujian lokalisasi. |
| `tests/Feature/SecurityHeadersProductionTest.php` | Modify | Hapus rute `/peta-alur` dari array pengujian security headers. |
| `tests/Feature/SuperAdmin/CanvasEditorConsolidatedFeaturesTest.php` | Modify | Hapus assert preview halaman `peta-alur`. |

---

## 3. Langkah Implementasi Bertahap

### Tahap 1: Pembersihan Frontend Landing Page & Blok Tahapan
- [x] **Langkah 1.1**: Bersihkan `$defaultHeaderNav` dan filter runtime di `resources/views/public/landing.blade.php`.
- [x] **Langkah 1.2**: Hapus tombol peta alur dan anchor `#peta-alur` di `resources/views/public/blocks/tahapan.blade.php`.
- [x] **Langkah 1.3**: Bersihkan `$defaultHeaderNav` di `app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php`.

### Tahap 2: Pembersihan Rute, Controller, & Site Editor
- [x] **Langkah 2.1**: Hapus rute `GET /peta-alur` di `routes/web.php`.
- [x] **Langkah 2.2**: Hapus method `petaInteraktif()` di `app/Http/Controllers/Public/AlurMagangController.php`.
- [x] **Langkah 2.3**: Bersihkan penanganan preview `peta-alur` di `app/Http/Controllers/SuperAdmin/LandingCmsController.php`.
- [x] **Langkah 2.4**: Hapus option `peta-alur` di `resources/views/superadmin/site-editor/partials/_topbar.blade.php`.

### Tahap 3: Penghapusan Berkas Fisik & Vite Config
- [x] **Langkah 3.1**: Hapus entri `peta-alur.css` dan `peta-alur.js` dari `vite.config.js`.
- [x] **Langkah 3.2**: Hapus file `resources/views/public/peta-alur-interaktif.blade.php`.
- [x] **Langkah 3.3**: Hapus file `resources/views/public/partials/_peta_alur_modal.blade.php`.
- [x] **Langkah 3.4**: Hapus file `resources/css/pages/public/peta-alur.css`.
- [x] **Langkah 3.5**: Hapus file `resources/js/pages/public/peta-alur.js`.
- [x] **Langkah 3.6**: Hapus file `public/css/peta-alur-fullscreen.css` jika ada.

### Tahap 4: Pembaruan & Penyelarasan Test Suite
- [x] **Langkah 4.1**: Hapus file test `tests/Feature/PetaAlurPageTest.php` dan `tests/Feature/LandingPagePetaAlurModalTest.php`.
- [x] **Langkah 4.2**: Perbarui assertion di `tests/Feature/LandingPageTest.php`.
- [x] **Langkah 4.3**: Bersihkan rute `/peta-alur` di `tests/Feature/LanguageLocalizationTest.php`, `tests/Feature/SecurityHeadersProductionTest.php`, dan `tests/Feature/SuperAdmin/CanvasEditorConsolidatedFeaturesTest.php`.

### Tahap 5: Kompilasi Aset & Verifikasi Akhir
- [x] **Langkah 5.1**: Jalankan `npm run build` dan pastikan tidak ada `public/hot`.
- [x] **Langkah 5.2**: Jalankan `php artisan test` untuk memastikan seluruh test suite lulus 100%.
