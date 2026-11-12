# Rencana Kerja: Penghapusan Banner Teks "Validasi Kapasitas Pembimbing" di Manajemen Kuota

**Tanggal:** 12 November 2026  
**Status:** Diimplementasikan (Menunggu Verifikasi Visual Browser oleh Pengguna)  
**Branch Saat Ini:** `test-2`  
**Acuan Desain:** Tangkapan layar pengguna (`media_1794460822493_65ae617b.png`)  
**Aturan Repositori:** `.agents/rules/AGENTS.md` (PHP 8.3, Laravel 10, minimal focused change, dan persetujuan eksplisit sebelum modifikasi file Blade)

---

## 1. Tujuan

Menghapus blok banner informasi (*callout*) **"Validasi Kapasitas Pembimbing"** beserta teks penjelasannya:

> *"Sistem otomatis memvalidasi agar total kuota tidak melebihi kapasitas bimbingan, yaitu jumlah Pembimbing aktif di setiap bidang."*

dari halaman **Manajemen Kuota Magang** (`/admin/kuota`), tanpa mengubah logika validasi kapasitas pembimbing di backend maupun peringatan kapasitas dinamis di dalam modal ubah kuota bidang.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis
1. **Penghapusan Satu Blok Banner Utuh (Sesuai Tangkapan Layar Acuan)**:
   Acuan gambar `media_1794460822493_65ae617b.png` memperlihatkan satu komponen kotak info biru utuh (ikon informasi `(i)`, judul `Validasi Kapasitas Pembimbing`, dan teks deskripsi di bawahnya). Dengan demikian, seluruh blok `<div class="adm-kuota-callout-wrap">...<x-gentelella.callout ...>...</x-gentelella.callout></div>` dihapus agar tidak menyisakan kotak callout biru kosong di bawah tabel.
2. **Aturan Bisnis & Modal Warning Tetap Berjalan**:
   Penghapusan ini hanya menyasar informasi statis pada bagian bawah tabel `Kuota per bidang`. Validasi backend kapasitas bimbingan saat penyimpanan kuota (controller/action) serta kotak peringatan dinamis `#boxWarningKapasitasBidang` (`Melebihi kapasitas bimbingan!...`) di dalam `#modalEditBidang` pada [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php#L1764-L1774) tetap dipertahankan.
3. **Kepatuhan Baseline & Perubahan Minimal (`AGENTS.md`)**:
   Halaman `admin/kuota/index.blade.php` masih berstatus tampilan legacy sebelum migrasi asset penuh, sehingga tidak dilakukan migrasi ulang CSS/JS atau pembersihan CSS legacy di luar kebutuhan langsung tugas.
4. **Dampak Kontrak Feature Test**:
   Pengujian [`tests/Feature/AdminKuotaRevampTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminKuotaRevampTest.php#L113-L124) saat ini memiliki test `test_kuota_page_displays_mentor_capacity_validation_banner()` yang memeriksa kemunculan teks tersebut dengan `assertSee`. Test ini perlu diperbarui menjadi assertion negatif (`assertDontSee`) agar sesuai dengan kebutuhan baru.

### Pertanyaan Konfirmasi (Membutuhkan Persetujuan Sebelum Implementasi)
Sesuai `.agents/rules/AGENTS.md` Pasal 3.3 (*Pengecualian perubahan Blade*):
- Perubahan ini tidak dapat dilakukan secara bersih hanya dari sisi CSS/JS tanpa memodifikasi struktur HTML Blade. Apakah Anda menyetujui perubahan minimal pada [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php#L1214-L1221) serta pembaruan test pada [`tests/Feature/AdminKuotaRevampTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminKuotaRevampTest.php#L113-L124)?

---

## 3. Evaluasi Opsi Arsitektur & Pendekatan Teknis

| Aspek | Opsi A: Hapus Blok Markup Callout di Blade + Update Test *(Direkomendasikan)* | Opsi B: Sembunyikan Secara Visual via CSS (`display: none`) |
| :--- | :--- | :--- |
| **Mekanisme** | Menghapus baris `1214–1221` pada `resources/views/admin/kuota/index.blade.php` dan memperbarui assertion test di `AdminKuotaRevampTest.php`. | Menambahkan `display: none` pada `.adm-kuota-callout-wrap` atau `.alert-validation-box` tanpa menyentuh file Blade. |
| **Kebersihan DOM & Payload** | Bersih sepenuhnya; elemen `role="alert"` dan teks tidak lagi dirender oleh server. | Buruk; markup, teks, dan atribut `role="alert"` tetap dikirim ke HTML DOM (*dead DOM*). |
| **Kepatuhan Feature Test** | Lugas; `AdminKuotaRevampTest` dapat langsung menguji `assertDontSee` secara akurat terhadap respons HTTP. | Membingungkan; pengujian HTML berbasis `assertSee` tetap mendeteksi teks meski tersembunyi di browser. |
| **Pemeliharaan & Risiko** | Zero side effect terhadap komponen `x-gentelella.callout` di halaman lain. | Menyentuh `public/css/dashboard-cockpit-unified.css` (aset legacy) dan berisiko membingungkan developer berikutnya. |

**Rekomendasi:** Pilih **Opsi A**, karena menghapus elemen langsung di sumber markup Blade menghasilkan DOM dan output respons HTML yang benar-benar bersih, akurat diuji secara otomatis, dan tidak meninggalkan teks tersembunyi yang membingungkan.

---

## 4. Perubahan File

| File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php#L1214-L1221) | Modify | Menghapus blok komentar `{{-- 5. ALERT VALIDASI PEMBIMBING AKTIF --}}` beserta wrapper `<div class="adm-kuota-callout-wrap">` dan komponen `<x-gentelella.callout type="info" class="alert-validation-box" title="Validasi Kapasitas Pembimbing">...</x-gentelella.callout>` pada baris 1214–1221. |
| [`tests/Feature/AdminKuotaRevampTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminKuotaRevampTest.php#L113-L124) | Modify | Memperbarui `test_kuota_page_displays_mentor_capacity_validation_banner()` menjadi verifikasi ketiadaan teks/banner (`assertDontSee('Validasi Kapasitas Pembimbing', false)` dan `assertDontSee('Sistem otomatis memvalidasi agar total kuota tidak melebihi kapasitas bimbingan, yaitu jumlah Pembimbing aktif di setiap bidang.', false)`). |

---

## 5. Langkah Implementasi

- [x] **Langkah 1 — Persetujuan Eksplisit**: Dapatkan konfirmasi persetujuan pengguna untuk memodifikasi markup pada `resources/views/admin/kuota/index.blade.php` dan kontrak test pada `tests/Feature/AdminKuotaRevampTest.php`.
- [x] **Langkah 2 — Hapus Blok Callout pada Blade View**:
  - Buka [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php#L1212-L1223).
  - Hapus tepat baris `1214–1221` (blok `{{-- 5. ALERT VALIDASI PEMBIMBING AKTIF --}}` dan `<div class="adm-kuota-callout-wrap">...</div>`).
  - Pastikan tag penutup `</div>` milik `.card-table-wrap` (baris 1212) dan penutup `</x-gentelella.x-panel>` (baris 1222) tetap lengkap dan sejajar.
- [x] **Langkah 3 — Perbarui Feature Test Kontrak Halaman Kuota**:
  - Buka [`tests/Feature/AdminKuotaRevampTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminKuotaRevampTest.php#L113-L124).
  - Ubah method `test_kuota_page_displays_mentor_capacity_validation_banner` menjadi `test_kuota_page_does_not_display_mentor_capacity_validation_banner` (atau sesuaikan nama & docblock-nya) agar memeriksa HTTP 200 sekaligus memverifikasi kedua string banner tidak lagi muncul dengan `assertDontSee(..., false)`.
- [x] **Langkah 4 — Bersihkan Cache View**:
  - Jalankan `d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe artisan view:clear`.
- [x] **Langkah 5 — Jalankan Quality Gate Otomatis (PHP 8.3)**:
  - Jalankan feature tests kuota terkait:
    `d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe artisan test tests/Feature/AdminKuotaRevampTest.php tests/Feature/AdminChildPagesVisualTest.php`
  - Jalankan linter formatting Laravel Pint:
    `d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe ./vendor/bin/pint --test tests/Feature/AdminKuotaRevampTest.php`
- [ ] **Langkah 6 — Verifikasi Visual Halaman**:
  - Periksa halaman `/admin/kuota` pada mode terang dan mode gelap untuk memastikan banner *Validasi Kapasitas Pembimbing* tidak lagi muncul serta batas bawah panel `Kuota per bidang` tetap rapi.

---

## 6. Strategi Testing

1. **Automated Feature Tests (`php artisan test`)**:
   - **Happy path**: User dengan role Admin (`RoleEnum::Admin`) mengakses `route('admin.kuota.index')` -> menerima status `200`, tabel `Kuota per bidang` serta modal pengelolaan kuota tetap tampil lengkap, dan string `'Validasi Kapasitas Pembimbing'` maupun `'Sistem otomatis memvalidasi agar total kuota tidak melebihi kapasitas bimbingan, yaitu jumlah Pembimbing aktif di setiap bidang.'` tidak ditemukan (`assertDontSee`).
   - **Regression / negative path**: Memastikan test lain di [`AdminKuotaRevampTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminKuotaRevampTest.php) dan [`AdminChildPagesVisualTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminChildPagesVisualTest.php) (termasuk render tabel, modal ubah/buat bulan, modal tambah/edit kuota, dan otorisasi) tetap lulus tanpa regresi.
2. **Code Formatting Check**:
   - Memastikan file PHP yang diubah mematuhi standar PSR-12 melalui `./vendor/bin/pint --test`.

---

## 7. Risiko & Mitigasi

| Kategori | Risiko | Mitigasi |
| :--- | :--- | :--- |
| **Test Suite** | Test lama `test_kuota_page_displays_mentor_capacity_validation_banner()` di `AdminKuotaRevampTest.php` gagal akibat `assertSee` terhadap teks yang memang sengaja dihapus. | Perbarui method test terkait pada Langkah 3 untuk memverifikasi ketiadaan banner dengan `assertDontSee`. |
| **Tata Letak / UI** | Hilangnya elemen terakhir di dalam `.x_content` berpotensi membuat tepi bawah tabel `.card-table-wrap` tampak terlalu dekat dengan tepi panel jika `.x_content` tidak memiliki padding bawah. | Cek komponen `.x_content` pada verifikasi visual Langkah 6 (light & dark mode) sebelum menyatakan selesai. |
| **Aturan Bisnis & Keamanan** | Kesalahpahaman bahwa validasi kapasitas pembimbing ikut dinonaktifkan di backend/modal. | Tidak menyentuh controller, action, maupun `#boxWarningKapasitasBidang`; cakupan perubahan hanya menghapus banner informasi statis di bawah tabel. |
| **Database, Query (N+1), & OWASP** | Tidak ada perubahan query database, migration, model, route, maupun permission/policy. | Risiko nol terhadap integritas database maupun celah keamanan. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Persetujuan eksplisit pengguna diperoleh sebelum mengubah file Blade.
- [x] Banner **"Validasi Kapasitas Pembimbing"** dan teks penjelasannya tidak lagi dirender di [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php).
- [x] Pengujian [`tests/Feature/AdminKuotaRevampTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminKuotaRevampTest.php) diperbarui untuk menegaskan ketiadaan banner tersebut dan seluruh skenario di `AdminKuotaRevampTest` serta `AdminChildPagesVisualTest` lulus 100% menggunakan PHP 8.3.
- [x] `./vendor/bin/pint --test` pada file PHP yang diubah lulus tanpa error.
- [ ] Cache view dibersihkan dan tampilan `/admin/kuota` terverifikasi bersih tanpa artefak visual atau error console. *(Catatan: `php artisan view:clear` sudah dijalankan, namun inspeksi browser live tertahan CAPTCHA login).*

---

## Catatan Perubahan (Deviations)

1. **Audit Browser Live (`/admin/kuota`) Tertahan CAPTCHA**: Saat membuka `http://127.0.0.1:8000/login` menggunakan Playwright MCP, formulir masuk diproteksi oleh CAPTCHA (`Kode verifikasi keamanan`). Sesuai aturan keamanan, agen tidak menyelesaikan atau melewati CAPTCHA tersebut, sehingga verifikasi halaman dilakukan lewat feature test HTTP Laravel sedangkan audit visual langsung (desktop 1440px, mobile 375px, light/dark mode, dan console browser) menunggu pengecekan manual pengguna.
2. **Anomali File `phpstan.neon`**: Setelah eksekusi `./vendor/bin/phpstan analyse --memory-limit=1G` gagal dengan error `Some parallel worker jobs have not finished`, `phpstan.neon` terpantau berstatus terhapus (`D phpstan.neon`) pada `git status`. File tidak dikembalikan secara sepihak untuk mematuhi larangan `git restore` pada `AGENTS.md` Pasal 12.

---

## Saran Lanjutan (Di Luar Scope Utama)

- Jika diinginkan di masa mendatang agar tidak ada aturan CSS yang tidak terpakai (*dead CSS*), selector `.alert-validation-box` pada blok `<style>` [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php#L494-L506) (light & dark mode di baris 494–506 dan 904–908) serta `.adm-kuota-callout-wrap` pada [`public/css/dashboard-cockpit-unified.css`](file:///d:/laragon/www/MAGKOM/public/css/dashboard-cockpit-unified.css#L1330-L1332) dapat dievaluasi dan dibersihkan secara terpisah setelah dipastikan tidak dirujuk di halaman lain.

