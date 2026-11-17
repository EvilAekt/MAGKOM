# Rencana Kerja: Harmonisasi Warna Keputusan Diterima Menjadi Biru Sleman (Admin Verifikasi)

**File Path**: `docs/plans/2026-11-17-harmonisasi-warna-biru-keputusan-diterima-admin.md`  
**Tanggal**: 17 November 2026  
**Status**: ✅ Selesai Diimplementasikan & Lolos Seluruh Quality Gate

---

## 1. Tujuan
Menyelaraskan tema warna seluruh elemen keputusan **Diterima** pada halaman verifikasi pendaftaran admin (`admin.pendaftaran.show`) dari warna **hijau** (`#ECFDF5`, `#065F46`, `#16A34A`) menjadi **Biru Sleman** (`#EFF6FF`, `#BFDBFE`, `#0641CC`, `#1E40AF`), serta mempertahankan warna **merah** tegas (`#FEF2F2`, `#FECACA`, `#DC2626`, `#991B1B`) untuk keputusan **Ditolak**.

Cakupan perbaikan mencakup:
1. **Modal Konfirmasi Penerimaan Berkas** (beralih dari `variant-success` hijau ke `variant-primary` biru).
2. **Panel Kartu Keputusan Verifikasi Final** (saat pendaftaran berstatus `diterima`, beralih dari kotak hijau ke kotak biru bernuansa Sleman Blue, termasuk ikon, kutipan catatan, dan tombol berkas surat keterangan).
3. **Tab Switcher "Terima"** (`.adm-tab-pill.active-terima` beralih dari teks/ikon hijau `#065F46` ke biru `#0641CC`).

---

## 2. Asumsi & Batasan Kerja

### Asumsi Kerja:
1. **Keputusan Ditolak**: Tetap menggunakan tema warna **merah** (`variant-danger` / `#DC2626`) sesuai kondisi yang sudah berjalan dengan baik.
2. **Indikator Kuota**: Indikator kuota kursi di atas tab switcher (`Status Kuota: Tersedia (X Kursi)`) tetap mempertahankan warna hijau/merah bawaan ketersediaan kuota (status logis kapasitas, bukan keputusan pendaftaran), kecuali pengguna menghendaki penyeragaman.
3. **Modal Reusable**: Komponen modal `resources/css/components/confirm-modal.css` sudah memiliki styling lengkap `.variant-primary` (baik Light Mode maupun Dark Mode) sehingga tidak memerlukan perubahan style CSS baru pada modal, cukup mengaktifkan varian tersebut via JavaScript.
4. **Dark Mode**: Seluruh elemen yang diubah wajib memiliki paritas kontras tinggi pada Dark Mode (`[data-theme="dark"]`).

---

## 3. Pendekatan Teknis (Evaluasi 2 Alternatif)

### Opsi A: Ekstraksi Class Semantik CSS & Migrasi Inline Style ke `resources/css/` (⭐ Direkomendasikan)
- Mengganti styling inline statis pada panel hasil keputusan di `resources/views/admin/pendaftaran/show.blade.php` dengan class semantik: `.adm-decision-result-card`, `.is-diterima`, dan `.is-ditolak`.
- Mendefinisikan styling `.adm-decision-result-card` di `resources/css/pages/admin/pendaftaran-show.css` dengan token Sleman Blue:
  - Background: `#EFF6FF` (Dark mode: `rgba(59, 130, 246, 0.12)`)
  - Border: `1px solid #BFDBFE` (Dark mode: `rgba(59, 130, 246, 0.25)`)
  - Teks Judul & Ikon: `#1E40AF` (Dark mode: `#93C5FD`)
  - Border Aksen Kutipan Catatan: `#0641CC` (Dark mode: `#3B82F6`)
  - Tombol Surat Pratinjau: `background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE;` (Dark mode: terintegrasi)
  - Tombol Surat Unduh PDF: `background: #0641CC; color: #FFFFFF;` (Dark mode: `#0641CC` dengan hover `#2563EB`)
- Mengubah pemicu varian modal di `resources/js/pages/admin/pendaftaran-show.js` dari `setModalVariant('success')` menjadi `setModalVariant('primary')`.
- Mengubah `.adm-tab-pill.active-terima` di `resources/css/pages/admin/pendaftaran-show.css` menjadi `color: #0641CC;`.
- **Kelebihan**: Sangat patuh pada aturan repositori AGENTS.md (memisahkan CSS dari Blade), mendukung Dark Mode secara native dan bersih, mudah dirawat tanpa duplikasi inline style.
- **Kekurangan**: Membutuhkan sedikit penyesuaian markup Blade untuk mengganti atribut `style` menjadi atribut `class`.

### Opsi B: Hardcoded Inline Hex Replacement di Blade & JS Varian Swap
- Hanya menimpa kode warna hex di dalam atribut `style="..."` pada file `show.blade.php` (misal mengganti `#ECFDF5` -> `#EFF6FF`).
- Mengubah `setModalVariant('success')` menjadi `setModalVariant('primary')` di file JS.
- **Kelebihan**: Tidak menyentuh struktur penamaan class.
- **Kekurangan**: Melanggar panduan larangan inline style di Blade (AGENTS.md Bagian 3), tidak mendukung Dark Mode pada panel keputusan final, dan menyulitkan pemeliharaan jangka panjang.

👉 **Pilihan Terpilih**: **Opsi A** demi konsistensi arsitektur kode dan dukungan Dark Mode menyeluruh.

---

## 4. Rincian Perubahan File

| No | File | Aksi | Ringkasan Perubahan |
|---|---|---|---|
| 1 | `resources/js/pages/admin/pendaftaran-show.js` | Modify | Ubah `setModalVariant('success')` menjadi `setModalVariant('primary')` saat `action !== 'tolak'` pada dialog konfirmasi penerimaan berkas. |
| 2 | `resources/css/pages/admin/pendaftaran-show.css` | Modify | 1. Perbarui `.adm-tab-pill.active-terima` dengan teks/ikon biru `#0641CC`.<br>2. Tambahkan class semantik `.adm-decision-result-card`, `.is-diterima`, `.is-ditolak`, dan badge surat keputusan (Light & Dark Mode). |
| 3 | `resources/views/admin/pendaftaran/show.blade.php` | Modify | Ganti inline styles pada kartu keputusan verifikasi final (baris 273–296) menggunakan class semantik `.adm-decision-result-card` (`is-diterima` / `is-ditolak`). |
| 4 | `tests/Feature/AdminPendaftaranPerbaikanFlowTest.php` | Modify | Tambahkan assertion untuk memastikan class warna/varian tombol dan modal verifikasi berstatus primer/biru. |

---

## 5. Langkah-Langkah Implementasi

### Tahap 1: Frontend JavaScript (`resources/js/`)
- [x] Buka `resources/js/pages/admin/pendaftaran-show.js`.
- [x] Ubah pemanggilan `setModalVariant('success')` pada fungsi `openConfirmModal(action)` menjadi `setModalVariant('primary')`.
- [x] Pastikan ikon polyline checkmark tetap ditampilkan dengan kontainer biru `#DBEAFE` dan ikon `#0641CC`.

### Tahap 2: Frontend Styling CSS (`resources/css/`)
- [x] Buka `resources/css/pages/admin/pendaftaran-show.css`.
- [x] Ubah warna aktif tab terima:
  ```css
  .adm-tab-pill.active-terima {
      background: #FFFFFF;
      color: #0641CC;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
      font-weight: 700;
  }
  ```
- [x] Tambahkan styling `.adm-decision-result-card` untuk Light Mode dan Dark Mode:
  - Varian `.is-diterima`: Background `#EFF6FF`, Border `#BFDBFE`, Header `#1E40AF`, Aksen quote `#0641CC`, Tombol Pratinjau `#EFF6FF`/`#1D4ED8`, Tombol Unduh `#0641CC`.
  - Varian `.is-ditolak`: Background `#FEF2F2`, Border `#FECACA`, Header `#991B1B`, Aksen quote `#DC2626`.
  - Dark Mode overrides untuk kontras WCAG 2.1 AA.

### Tahap 3: Blade View Refactor (`resources/views/`)
- [x] Buka `resources/views/admin/pendaftaran/show.blade.php`.
- [x] Bersihkan styling inline pada blok `@if($isFinal)` (baris 273–296), gantikan dengan class semantik `.adm-decision-result-card {{ $statusValue === 'diterima' ? 'is-diterima' : 'is-ditolak' }}`.

### Tahap 4: Build Aset & Verifikasi
- [x] Jalankan `npm run build` untuk mengompilasi CSS dan JS Vite.
- [x] Jalankan pengujian otomatis `php artisan test tests/Feature/AdminPendaftaranPerbaikanFlowTest.php`.
- [x] Lakukan verifikasi visual di browser pada 2 kondisi:
  1. Status pendaftaran `diajukan` -> Klik "Diterima" -> Modal konfirmasi tampil dalam balutan warna **Biru**.
  2. Status pendaftaran `diterima` -> Card hasil keputusan verifikasi tampil dalam warna **Biru** (bukan hijau).
  3. Status pendaftaran `ditolak` -> Tetap tampil dalam warna **Merah**.

---

## 6. Strategi Testing

1. **Feature Test Otomatis**:
   - `php artisan test tests/Feature/AdminPendaftaranPerbaikanFlowTest.php`
   - Memastikan respons halaman `admin.pendaftaran.show` memuat tombol keputusan dan elemen keputusan verifikasi tanpa error 500/exception.
2. **Pengujian Visual & Interaksi**:
   - Uji klik tombol "Diterima" pada pendaftaran `diajukan`: Verifikasi modal konfirmasi memiliki badge biru, alert callout biru, dan tombol aksi "Ya, Terima Berkas" berwarna biru `#0641CC`.
   - Uji klik tombol "Tolak Pengajuan": Verifikasi modal konfirmasi memiliki badge merah, callout merah, dan tombol aksi "Ya, Tolak Pengajuan" berwarna merah `#DC2626`.
   - Uji tampilan pendaftaran yang sudah berstatus `diterima`: Panel keputusan menampilkan tema biru.
   - Uji toggle Dark Mode: Memastikan teks tetap terbaca jelas (rasio kontras > 4.5:1).

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Kontras warna biru pada teks putih atau latar belakang terang kurang terbaca | Aksesibilitas UI menurun (WCAG fail) | Gunakan kombinasi token teruji: Background `#EFF6FF` dengan teks `#1E40AF` (rasio kontras > 7:1) dan tombol `#0641CC` dengan teks `#FFFFFF` (rasio kontras > 8:1). |
| Regresi pada modal penolakan | Alur tolak terganggu | Varian `variant-danger` tidak diubah sama sekali; pengujian terpisah dijalankan untuk aksi tolak. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Modal konfirmasi penerimaan berkas menggunakan warna biru (`variant-primary` / `#0641CC`), bukan hijau.
- [x] Panel kartu keputusan verifikasi saat status `diterima` menggunakan warna biru Sleman (bukan hijau).
- [x] Panel kartu keputusan verifikasi saat status `ditolak` tetap menggunakan warna merah.
- [x] Tab switcher verifikasi "Terima" saat aktif menggunakan warna biru `#0641CC`.
- [x] Tampilan konsisten pada Light Mode dan Dark Mode.
- [x] `npm run build` sukses tanpa warning/error.
- [x] Seluruh test `php artisan test` terkait lolos 100%.
