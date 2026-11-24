# Rencana Kerja: Implementasi Pop-up Modal Konfirmasi & Toast Notifikasi Modern (Terbitkan & Rollback) Serta Pembersihan Status Topbar

**Dokumen**: `docs/plans/2026-11-24-pop-up-modal-dan-toast-notifikasi-terbitkan-rollback.md`  
**Tanggal**: 24 November 2026  
**Status**: Selesai Diimplementasikan  
**Terkait**: Canvas Site Editor SuperAdmin (`/superadmin/site-editor`), DS-MagKom Design Tokens

---

## 1. Tujuan

1. **Pop-up Konfirmasi Interaktif (Referensi SS 1)**: Mengimplementasikan modal pop-up konfirmasi berstandar GovTech MAGKOM (`magkom-modal-*`) saat SuperAdmin menekan tombol **Terbitkan** dan **Rollback**. Modal ini menyajikan identitas objek yang dipublikasikan, dampak operasional, peringatan backup otomatis, serta tombol eksekusi yang jelas dan tegas.
2. **Toast Notifikasi Mengambang Modern (Referensi SS 2)**: Menggantikan notifikasi banner/toast sederhana dengan sistem *floating stackable toast* (`.app-toast` di dalam `.toast-floating-container`) di pojok kanan atas. Toast dilengkapi badge ikon sirkular (sukses/info/error), label status "Baru saja", judul tebal, deskripsi konteks yang jelas, serta animasi transisi mulus (*slide-in* dan auto-dismiss).
3. **Pembersihan Status Topbar (Permintaan Hapus SS 3)**: Menghapus badge statis hijau `● ✓ Tersimpan (24 November 2026 18:30 WIB)` (`#wp-save-status` / `.wp-status-container`) dari header topbar editor agar topbar menjadi bersih, minimalis, dan elegan tanpa elemen visual yang kaku.

---

## 2. Asumsi & Analisis Kebutuhan

- **Komponen Standar MAGKOM Sudah Tersedia**:
  - `resources/css/components/confirm-modal.css` telah mendefinisikan gaya lengkap untuk `.magkom-modal-backdrop`, `.magkom-modal-dialog`, `.magkom-modal-header-left`, `.magkom-modal-icon-badge`, `.magkom-modal-identity-strip`, `.magkom-modal-impact-banner`, dan `.magkom-modal-footer`.
  - `resources/css/components/notifications.css` telah mendefinisikan gaya lengkap untuk `.toast-floating-container`, `.app-toast`, `.toast-icon-badge`, `.toast-content`, `.toast-title`, `.toast-desc`, `.toast-close-btn`, dan animasi `toastSlideInRight` / `toastSlideOutRight`.
- **Pelepasan Badge Topbar (SS 3)**:
  - Badge `#wp-save-status` pada topbar sebelumnya digunakan untuk menampilkan status timestamp penyimpanan draf. Pengguna meminta elemen ini dihapus agar header lebih fokus pada navigasi dan tombol aksi.
  - Feedback penyimpanan dan sinkronisasi akan dialihkan ke sistem floating toast yang muncul secara halus tanpa mengotori tata letak header utama.
  - Di sisi JavaScript (`editor-state.js` dan `editor-app.js`), pemanggilan `document.getElementById('wp-save-status')` harus diamankan (*null-safe*) agar tidak memicu error ketika elemen tersebut ditiadakan dari Blade.
- **Kesesuaian Pengujian**:
  - Pada `tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php`, baris assertion `$response->assertSee('id="wp-save-status"', false);` akan diperbarui menjadi `$response->assertDontSee('id="wp-save-status"', false);`, dan ditambahkan assertion untuk memverifikasi kehadiran `#wp-toast-container`, `#magkom-publish-modal`, serta `#magkom-rollback-modal`.

---

## 3. Evaluasi Pendekatan Teknis

### Opsi A: Menggunakan Modul Komponen Standar DS-MagKom (`confirm-modal.css` & `notifications.css`) (Direkomendasikan)
- **Deskripsi**:
  - Mengimpor `@import '../../components/confirm-modal.css';` dan `@import '../../components/notifications.css';` ke dalam `resources/css/pages/superadmin/site-editor.css`.
  - Membangun struktur markup modal di `_action_modals.blade.php` persis mengikuti pola `resources/views/admin/pendaftaran/show.blade.php` (seperti yang terlihat pada tangkapan layar 1).
  - Menyediakan kontainer `<div id="wp-toast-container" class="toast-floating-container alerts-container"></div>` di `index.blade.php` dan fungsi helper JavaScript dinamis di `editor-app.js` untuk membuat toast instan dengan durasi auto-dismiss 5 detik.
- **Kelebihan**:
  - Konsistensi visual 100% dengan antarmuka inti sistem MAGKOM (warna biru navy Sleman, border radius 20px, efek blur backdrop, badge sirkular).
  - Tidak membuat CSS duplikat baru; memanfaatkan styling yang sudah teruji dan stabil.
  - Sesuai dengan aturan `AGENTS.md` (no inline styles, zero styling di Blade, modular Vite).
- **Kekurangan**: Tidak ada.

### Opsi B: Membuat CSS Kustom Mandiri Khusus Editor Tanpa Mengimpor Komponen Inti
- **Deskripsi**: Menulis kelas-kelas baru secara mandiri di dalam `site-editor.css` dengan meniru bentuk visual.
- **Kelebihan**: Terisolasi hanya di dalam berkas editor.
- **Kekurangan**: Terjadi duplikasi kode CSS hingga ratusan baris, melanggar prinsip *Single Source of Truth* token desain MAGKOM, dan rentan terjadi inkonsistensi saat tema diperbarui.

> **Keputusan**: Menggunakan **Opsi A** (Memanfaatkan standar komponen inti DS-MagKom).

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/superadmin/site-editor/partials/_topbar.blade.php` | `Modify` | Menghapus blok status badge `.wp-status-container` / `#wp-save-status` (SS 3). |
| `resources/views/superadmin/site-editor/partials/_action_modals.blade.php` | `Modify` | Mengubah struktur modal konfirmasi Terbitkan dan Rollback agar presisi 1:1 mengikuti `magkom-modal-*` (SS 1). |
| `resources/views/superadmin/site-editor/index.blade.php` | `Modify` | Mengganti `#wp-toast` lama dengan kontainer toast mengambang `#wp-toast-container` (SS 2). |
| `resources/css/pages/superadmin/site-editor.css` | `Modify` | Menambahkan `@import '../../components/confirm-modal.css';` dan `@import '../../components/notifications.css';` serta styling mikro penyesuaian editor. |
| `resources/js/pages/superadmin/site-editor/editor-app.js` | `Modify` | Memperbarui interaksi modal Terbitkan & Rollback (buka, tutup, transisi loading) dan fungsi `showToast()` dinamis yang menghasilkan markup `.app-toast` modern. |
| `resources/js/pages/superadmin/site-editor/editor-state.js` | `Modify` | Mengamankan pembaruan status draf agar null-safe saat `#wp-save-status` sudah tidak ada di topbar. |
| `tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php` | `Modify` | Memperbarui assertion status topbar (assertDontSee `#wp-save-status`) dan memverifikasi keberadaan modal serta kontainer toast. |

---

## 5. Rincian Desain Antarmuka

### A. Modal Konfirmasi Terbitkan (Pop Up Ref SS 1)
- **Backdrop**: `#magkom-publish-modal` (`.magkom-modal-backdrop`).
- **Header**:
  - Badge Ikon: Lingkaran biru lembut (`.magkom-modal-icon-badge.variant-primary`) dengan ikon SVG roket/centang putih.
  - Judul: **Konfirmasi Publikasi Halaman**
  - Subtitle: *Perubahan Draf Siap Dipublikasikan ke Portal Publik*
  - Tombol Tutup: Ikon silang (×) di pojok kanan atas.
- **Body**:
  - Pertanyaan: *"Apakah Anda yakin ingin mempublikasikan seluruh perubahan draf ini ke halaman publik?"*
  - Kartu Objek (`.magkom-modal-identity-strip`):
    - Avatar: Kotak biru navy bertuliskan `CMS`.
    - Nama: **Landing Page Portal MAGKOM**
    - Tag Badge: `#LIVE-VERSION`
    - Subtitle: Dinas Komunikasi dan Informatika (7 Seksi Terkelola).
  - Banner Dampak (`.magkom-modal-impact-banner.variant-primary`):
    - Ikon: `(i)` informasi.
    - Keterangan: *"Seluruh pengunjung umum akan langsung melihat tata letak, banner, dan konten terbaru. Sistem otomatis menyimpan salinan cadangan (backup snapshot) sebelum penerbitan."*
- **Footer**:
  - Tombol Batal: `.magkom-modal-btn-cancel` (*Batal*).
  - Tombol Konfirmasi: `.magkom-modal-btn-confirm.variant-primary` (*Ya, Terbitkan Halaman*).

### B. Modal Konfirmasi Rollback (Pop Up Ref SS 1)
- **Backdrop**: `#magkom-rollback-modal` (`.magkom-modal-backdrop`).
- **Header**:
  - Badge Ikon: Lingkaran merah/rose lembut (`.magkom-modal-icon-badge.variant-danger`) dengan ikon SVG riwayat/undo.
  - Judul: **Konfirmasi Rollback Halaman**
  - Subtitle: *Kembalikan Tampilan Publik ke Versi Sebelum Terbit Terakhir*
  - Tombol Tutup: Ikon silang (×).
- **Body**:
  - Pertanyaan: *"Apakah Anda yakin ingin membatalkan perubahan draf dan mengembalikan ke versi sebelum publikasi terakhir?"*
  - Kartu Objek:
    - Avatar: Kotak merah-rose bertuliskan `BCK`.
    - Nama: **Cadangan Snapshot Otomatis**
    - Tag Badge: `#RESTORE-BACKUP`
    - Subtitle: Versi snapshot tersimpan di server.
  - Banner Dampak (`.magkom-modal-impact-banner.variant-danger`):
    - Ikon: `(!)` peringatan bahaya.
    - Keterangan: *"PERINGATAN: Perubahan draf aktif yang belum dipublikasikan akan digantikan oleh versi cadangan terakhir. Halaman publik akan langsung kembali ke versi sebelumnya."*
- **Footer**:
  - Tombol Batal: `.magkom-modal-btn-cancel` (*Batal*).
  - Tombol Konfirmasi: `.magkom-modal-btn-confirm.variant-danger` (*Ya, Rollback Versi*).

### C. Sistem Toast Notifikasi Mengambang (Ref SS 2)
- **Kontainer**: `<div id="wp-toast-container" class="toast-floating-container alerts-container" aria-live="polite"></div>` (menempel di kanan atas jendela editor).
- **Elemen Toast (`.app-toast`)**:
  - **Ikon Bulat**:
    - Sukses: `.toast-icon-badge.success` (lingkaran hijau lembut + ikon perisai centang).
    - Info: `.toast-icon-badge.info` (lingkaran biru lembut + ikon informasi).
    - Error: `.toast-icon-badge.error` (lingkaran merah lembut + ikon silang/peringatan).
  - **Konten**:
    - Baris Atas: Judul tebal (misal: **"Perubahan Berhasil Disimpan"** / **"Publikasi Berhasil"**) + pill waktu *"Baru saja"* + tombol tutup (*"×"*).
    - Deskripsi: Teks penjelasan lengkap (misal: *"Halaman landing page publik berhasil diperbarui. Perubahan draf Anda kini telah aktif dan dapat langsung diakses oleh masyarakat umum."*).
  - **Interaksi**: Auto-dismiss setelah 5.000 ms, dapat ditutup secara manual dengan mengklik tombol silang, serta animasi mulus *slide in* dan *slide out*.

### D. Penghapusan Elemen Header (SS 3)
- Elemen `<div class="wp-status-container"><span id="wp-save-status"...>...</span></div>` dihapus seluruhnya dari `_topbar.blade.php`.
- Area kanan topbar kini hanya memuat:
  1. Tombol Pratinjau Eksternal (*Pratinjau Draf*).
  2. Tombol Navigasi Situs Resmi (*Situs Live*).
  3. Tombol Aksi Utama (*Terbitkan* dan *Rollback*).

---

## 6. Langkah Implementasi Terurut

- [x] **Langkah 1 (Blade Topbar)**: Hapus elemen `.wp-status-container` dan `#wp-save-status` dari `resources/views/superadmin/site-editor/partials/_topbar.blade.php`.
- [x] **Langkah 2 (Blade Action Modals)**: Rancang ulang `resources/views/superadmin/site-editor/partials/_action_modals.blade.php` menggunakan struktur `magkom-modal-*` yang sesuai 1:1 dengan SS 1 untuk kedua modal (Terbitkan dan Rollback).
- [x] **Langkah 3 (Blade Main Layout)**: Tambahkan kontainer toast mengambang `<div id="wp-toast-container" class="toast-floating-container alerts-container"></div>` pada `resources/views/superadmin/site-editor/index.blade.php` menggantikan toast lama.
- [x] **Langkah 4 (CSS Integration)**: Tambahkan `@import '../../components/confirm-modal.css';` dan `@import '../../components/notifications.css';` ke dalam `resources/css/pages/superadmin/site-editor.css`. Pastikan z-index modal (99999) dan toast (999999) berada di atas canvas dan toolbar editor.
- [x] **Langkah 5 (JavaScript State & App)**:
  - Di `editor-state.js`, amankan pembaruan status agar tidak memicu error saat `#wp-save-status` bernilai null.
  - Di `editor-app.js`, perbarui fungsi pembuka dan penutup modal (menambahkan class `is-open` ke `.magkom-modal-backdrop`), serta perbarui fungsi helper `showToast(title, message, type = 'success')` agar menghasilkan elemen DOM `.app-toast` dengan layout lengkap sesuai SS 2.
- [x] **Langkah 6 (Asset Compilation)**: Jalankan `npm run build` untuk mengompilasi CSS dan JS Vite yang telah diperbarui.
- [x] **Langkah 7 (Automated Testing)**:
  - Perbarui baris assertion pada `tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php`.
  - Jalankan `vendor/bin/pint --test` dan `php artisan test --filter=LandingCmsSiteEditorTest`.
- [x] **Langkah 8 (Verifikasi Visual)**: Buka editor di browser untuk memverifikasi bahwa header bersih dari status pill lama (SS 3 terhapus), klik tombol Terbitkan menampilkan modal pop up elegan (SS 1), dan setelah konfirmasi muncul toast notifikasi hijau yang rapi di kanan atas (SS 2).

---

## 7. Strategi Testing

- **Feature Test Automation**:
  - Menjalankan `php artisan test --filter=LandingCmsSiteEditorTest`.
  - Memastikan 19 test case tetap hijau, termasuk verifikasi AJAX draft saving, publish, dan rollback.
- **Form & Flow Testing**:
  - Test klik tombol **Terbitkan**: Pop-up modal muncul dengan backdrop blur. Tombol *Batal* menutup modal. Tombol *Ya, Terbitkan Halaman* memicu AJAX POST ke `/superadmin/site-editor/publish`.
  - Test status loading: Tombol konfirmasi menampilkan spinner dan disabled sementara permintaan dikirim.
  - Test respons sukses: Modal tertutup dan toast hijau mengambang muncul dengan teks "Perubahan Berhasil Disimpan" / "Halaman Berhasil Diterbitkan".
  - Test klik tombol **Rollback**: Pop-up modal merah muncul. Konfirmasi memicu AJAX POST ke `/superadmin/site-editor/rollback`. Toast sukses muncul setelah selesai.

---

## 8. Risiko & Mitigasi

- **Risiko 1**: Konflik CSS z-index antara iframe kanvas, floating toolbar, dan backdrop modal.
  - *Mitigasi*: `.magkom-modal-backdrop` menggunakan `z-index: 99999` dan `.toast-floating-container` menggunakan `z-index: 999999`, jauh melampaui z-index kanvas (`z-index: 10`) dan toolbar (`z-index: 100`).
- **Risiko 2**: JavaScript crash ketika auto-save mencoba mengakses `#wp-save-status` yang sudah dihapus.
  - *Mitigasi*: Tambahkan pemeriksaan guard clause `const statusPill = document.getElementById('wp-save-status'); if (statusPill) { ... }` pada `editor-state.js`.

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Status badge hijau (SS 3) terhapus bersih dari header topbar editor.
- [x] Mengklik tombol **Terbitkan** membuka modal konfirmasi GovTech DS-MagKom dengan rincian kartu CMS dan banner informasi (sesuai referensi SS 1).
- [x] Mengklik tombol **Rollback** membuka modal konfirmasi merah dengan peringatan data backup (sesuai referensi SS 1).
- [x] Hasil publikasi dan rollback menampilkan notifikasi *floating toast* modern dengan badge ikon bulat, waktu "Baru saja", dan pesan informatif (sesuai referensi SS 2).
- [x] Asset Vite berhasil dikompilasi dengan `npm run build` tanpa error.
- [x] Seluruh test `LandingCmsSiteEditorTest` dan format Pint lulus 100%.

