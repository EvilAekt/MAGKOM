# Rencana Kerja: Penghapusan Opsi "Perlu Perbaikan" pada Fitur Verifikasi Berkas Pendaftaran Peserta (Admin)

**Dokumen**: `docs/plans/2026-11-17-penghapusan-opsi-perlu-perbaikan-admin-verifikasi.md`  
**Tanggal**: 17 November 2026  
**Status**: Selesai Diimplementasikan (100% Verified)  
**Referensi Aturan**: [`AGENTS.md`](file:///g:/MAGKOM/.agents/rules/AGENTS.md), [`web-planning`](file:///g:/MAGKOM/.agents/skills/web-planning/SKILL.md)

---

## 1. Tujuan

Menghapus opsi keputusan **"Perlu Perbaikan"** dari alur verifikasi berkas pendaftaran peserta oleh Admin pada halaman detail verifikasi pendaftaran (`admin.pendaftaran.show` dan endpoint `admin.pendaftaran.putuskan`). 

Dengan perubahan ini:
1. Panel keputusan admin disederhanakan menjadi **Segmented Control 2 Opsi Simetris**:
   - **Terima**: Menyetujui pendaftaran dan mengalokasikan kuota serta penempatan pembimbing/bidang.
   - **Tolak**: Menolak pendaftaran dengan kewajiban mengisi catatan alasan penolakan minimal 10 karakter.
2. Memperbaiki tampilan UI tombol switcher keputusan (`.adm-decision-tabs`) yang sebelumnya mengalami *wrap* ke baris kedua karena berformat grid 2 kolom (`grid-template-columns: repeat(2, 1fr)`), sehingga menjadi rapi, simetris, dan intuitif.
3. Menyelaraskan validasi backend (`KeputusanPendaftaranRequest`), aksi domain (`KeputusanPendaftaranAction`), serta modul frontend JavaScript (`pendaftaran-show.js`).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Integritas Data Historis Terjaga**: Status enum `StatusPendaftaranEnum::PerluPerbaikan` dan method `PlacementService::mintaPerbaikan()` **tidak dihapus** dari sistem. Hal ini penting agar:
   - Data pendaftar historis yang sudah berstatus `perlu_perbaikan` di masa lampau tetap dapat dibaca, difilter pada antrean index/laporan, dan dirender status badge-nya tanpa error.
   - Tidak memerlukan operasi migrasi skema database yang berisiko merusak audit trail atau data seeder.
2. **Kewajiban Catatan**:
   - Keputusan `terima`: Catatan bersifat opsional.
   - Keputusan `tolak`: Catatan alasan penolakan **wajib diisi minimal 10 karakter** dan maksimal 1000 karakter.
3. **Portal Peserta**: Alur penanganan dokumen peserta tidak diubah; perubahan strictly dibatasi pada kemampuan Admin Verifikator saat mengambil keputusan verifikasi berkas pendaftar baru.

### Pertanyaan Terbuka:
- *Tidak ada ambiguitas kritis yang memblokir perencanaan, karena instruksi pengguna dan bukti visual sangat jelas menunjukkan tombol tab "Perlu Perbaikan" yang ingin dihilangkan dari panel keputusan verifikasi berkas.*

---

## 3. Pendekatan Teknis (Evaluasi Alternatif)

### Opsi A (Direkomendasikan): Penghapusan Opsi pada Level Antarmuka, Validasi, dan Action Admin
- **Mekanisme**:
  - Menghapus tombol tab `#tab-btn-perlu_perbaikan` dan panel form `#action-panel-perlu_perbaikan` di view Blade admin.
  - Memperbarui array tab dan event handler di `resources/js/pages/admin/pendaftaran-show.js` agar hanya mengelola `['terima', 'tolak']`.
  - Membatasi validasi `KeputusanPendaftaranRequest` menjadi `Rule::in(['terima', 'tolak'])`.
  - Menghapus percabangan `if ($keputusan === 'perlu_perbaikan')` di `KeputusanPendaftaranAction`.
  - Mempertahankan method `PlacementService::mintaPerbaikan()` dan badge styling CSS `.adm-badge-perlu_perbaikan` untuk kompatibilitas data riwayat.
- **Kelebihan**:
  - Sangat aman, terarah, dan memenuhi prinsip *minimal, focused change*.
  - Bebas dari risiko *database breakage* atau inkonsistensi audit trail.
  - UI tersejajarkan sempurna 50% - 50% dalam `.adm-decision-tabs` tanpa perubahan CSS baru.
- **Kekurangan**:
  - Class enum dan method service perbaikan tetap tersimpan di backend sebagai *dormant logic* untuk audit data lama.

### Opsi B: Pembersihan Total (Hard Delete Enum & Database Migration)
- **Mekanisme**:
  - Mengubah tipe ENUM database kolom `status` pada tabel `pendaftaran`, menghapus nilai `perlu_perbaikan`.
  - Menghapus case enum `StatusPendaftaranEnum::PerluPerbaikan`.
  - Menghapus seluruh method terkait revisi berkas di `PlacementService`, `PesertaPendaftaranController`, dan tabel dokumen.
- **Kelebihan**:
  - Menghapus seluruh jejak kode revisi berkas hingga ke akar backend.
- **Kekurangan**:
  - **Sangat berisiko & destruktif**: Berpotensi merusak integritas database lama, memutus riwayat log audit, dan mengubah kontrak portal peserta secara masif.
  - Melanggar panduan *Destructive Commands Guardrail* dan *Stable Engineering Baseline*.

> **Rekomendasi**: Mengadopsi **Opsi A**, karena aman, terfokus pada fitur admin verifikasi berkas sesuai permintaan pengguna, serta menjamin stabilitas sistem.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/pendaftaran/show.blade.php` | Modify | Menghapus tombol tab `#tab-btn-perlu_perbaikan` dan kontainer form `#action-panel-perlu_perbaikan`. Memastikan switcher keputusan hanya memiliki 2 opsi: Terima & Tolak. |
| `resources/js/pages/admin/pendaftaran-show.js` | Modify | Memperbarui array `panels` menjadi `['terima', 'tolak']` serta menghapus manipulasi elemen `catatan_perbaikan`. |
| `app/Http/Requests/KeputusanPendaftaranRequest.php` | Modify | Mengubah aturan validasi `keputusan` menjadi `Rule::in(['terima', 'tolak'])` dan menyesuaikan aturan `catatan` hanya wajib untuk `tolak`. Memperbarui pesan validasi bahasa Indonesia. |
| `app/Actions/Admin/Pendaftaran/KeputusanPendaftaranAction.php` | Modify | Menghapus penanganan percabangan status `'perlu_perbaikan'` sehingga hanya mengeksekusi alur `terima` dan `tolak`. |
| `tests/Feature/AdminPendaftaranPerbaikanFlowTest.php` | Modify | Memperbarui assertions: memastikan opsi `perlu_perbaikan` tidak muncul di halaman show, pengiriman `perlu_perbaikan` ditolak oleh validasi backend (422), dan filter riwayat antrean tetap berfungsi. |

---

## 5. Justifikasi Khusus Modifikasi File Blade (`AGENTS.md` Pasal 3.3)

Sesuai aturan tata kelola [`AGENTS.md`](file:///g:/MAGKOM/.agents/rules/AGENTS.md), file Blade bersifat *read-only secara default* dan memerlukan izin eksplisit jika terdapat modifikasi markup:

1. **Alasan Perubahan Markup**:
   Opsi "Perlu Perbaikan" dirender secara deklaratif di dalam markup HTML [`resources/views/admin/pendaftaran/show.blade.php`](file:///g:/MAGKOM/resources/views/admin/pendaftaran/show.blade.php) pada:
   - Baris 321–324: Tombol `<button type="button" id="tab-btn-perlu_perbaikan"...>`
   - Baris 362–376: Blok kontainer `<div id="action-panel-perlu_perbaikan"...>`
   Penghapusan elemen-elemen ini tidak dapat dilakukan semata-mata via CSS/JS demi alasan semantik HTML, aksesibilitas keyboard (a11y), dan kepatuhan CSP.
2. **Lingkup Perubahan**:
   Hanya menghapus 4 baris tombol tab dan 15 baris panel form perbaikan di dalam form `#decision-form`. Tidak ada penambahan tag `<style>` maupun `<script>` di dalam Blade.

---

## 6. Langkah Implementasi Terstruktur

### Fase 1: Backend Validation & Action
- [x] **Langkah 1.1**: Perbarui [`app/Http/Requests/KeputusanPendaftaranRequest.php`](file:///g:/MAGKOM/app/Http/Requests/KeputusanPendaftaranRequest.php):
  - Ubah `Rule::in(['terima', 'tolak', 'perlu_perbaikan'])` menjadi `Rule::in(['terima', 'tolak'])`.
  - Ubah `Rule::requiredIf(...)` dan `Rule::when(...)` agar field `catatan` hanya wajib minimal 10 karakter saat `keputusan === 'tolak'`.
  - Perbarui teks pesan error validasi `keputusan.in` dan `catatan.required`.
- [x] **Langkah 1.2**: Perbarui [`app/Actions/Admin/Pendaftaran/KeputusanPendaftaranAction.php`](file:///g:/MAGKOM/app/Actions/Admin/Pendaftaran/KeputusanPendaftaranAction.php):
  - Hapus blok kondisi `if ($keputusan === 'perlu_perbaikan')`.
  - Pertahankan alur eksekusi `$this->placementService->terimaBerkas(...)` dan fallback `$this->placementService->tolak(...)`.

### Fase 2: Frontend View & JavaScript
- [x] **Langkah 2.1**: Perbarui [`resources/views/admin/pendaftaran/show.blade.php`](file:///g:/MAGKOM/resources/views/admin/pendaftaran/show.blade.php):
  - Hapus elemen `<button id="tab-btn-perlu_perbaikan">`.
  - Hapus blok panel formulir `<div id="action-panel-perlu_perbaikan">`.
- [x] **Langkah 2.2**: Perbarui [`resources/js/pages/admin/pendaftaran-show.js`](file:///g:/MAGKOM/resources/js/pages/admin/pendaftaran-show.js):
  - Ubah `const panels = ['terima', 'perlu_perbaikan', 'tolak'];` menjadi `const panels = ['terima', 'tolak'];`.
  - Hapus kode event listener / toggle untuk elemen `catatanPerbaikan`.
- [x] **Langkah 2.3**: Jalankan kompilasi aset frontend via Vite:
  - `npm run build` untuk memverifikasi tidak ada error sintaks pada bundle JavaScript/CSS.

### Fase 3: Penyelarasan Automated Test Suite
- [x] **Langkah 3.1**: Perbarui [`tests/Feature/AdminPendaftaranPerbaikanFlowTest.php`](file:///g:/MAGKOM/tests/Feature/AdminPendaftaranPerbaikanFlowTest.php):
  - Ubah `test_halaman_show_pendaftaran_menampilkan_opsi_perlu_perbaikan` menjadi pengujian negatif bahwa tab dan panel perbaikan **tidak ada** (`assertDontSee('tab-btn-perlu_perbaikan')` dan `assertDontSee('action-panel-perlu_perbaikan')`).
  - Ubah skenario submit keputusan `'perlu_perbaikan'` agar memverifikasi bahwa sistem menolaknya dengan error validasi `keputusan` (HTTP 302 dengan session errors).
  - Pertahankan skenario pengujian filter status antrean `status=perlu_perbaikan` untuk data pendaftar riwayat lama.
- [x] **Langkah 3.2**: Jalankan seluruh rangkaian test terkait modul pendaftaran dan administrasi:
  - `php artisan test --filter=AdminPendaftaranPerbaikanFlowTest`
  - `php artisan test --filter=AdminSuperAdminPhaseThreeTest`
  - `php artisan test --filter=AdminSuperAdminPortalTest`
  - `php artisan test --filter=EndToEndUatTest`

---

## 7. Strategi Testing

| Jenis Test | Nama Test Case / Skenario | Ekspektasi |
| :--- | :--- | :--- |
| **Feature Test** | `test_halaman_show_pendaftaran_hanya_menampilkan_opsi_terima_dan_tolak` | Halaman 200 OK, melihat `tab-btn-terima` dan `tab-btn-tolak`, tidak melihat `tab-btn-perlu_perbaikan`. |
| **Feature Test** | `test_admin_dapat_menerima_berkas_pendaftaran` | Pendaftaran berhasil diubah ke status `diterima`, penempatan tercatat. |
| **Feature Test** | `test_admin_dapat_menolak_berkas_dengan_catatan_valid` | Pendaftaran berhasil diubah ke status `ditolak` dengan catatan >= 10 karakter. |
| **Validation Sad Path** | `test_keputusan_perlu_perbaikan_ditolak_validasi` | Pengiriman payload `keputusan => 'perlu_perbaikan'` menghasilkan error validasi pada field `keputusan`. |
| **Validation Sad Path** | `test_menolak_berkas_tanpa_catatan_memicu_error` | Pengiriman payload `keputusan => 'tolak'` dengan catatan kosong / < 10 karakter menghasilkan error validasi `catatan`. |
| **Regression Test** | `test_filter_status_perlu_perbaikan_pada_index_antrean` | Filter antrean pendaftaran dengan status `perlu_perbaikan` tetap dapat menampilkan data historis yang ada. |

---

## 8. Risiko & Mitigasi

1. **Risiko**: Tampilan tombol admin menjadi kosong atau rusak jika JS gagal load.  
   *Mitigasi*: Tab "Terima" disetel aktif secara default via markup dan CSS (`active-terima`), form `#decision-form` memiliki nilai input default `value="terima"`.
2. **Risiko**: Peserta lama yang berstatus `perlu_perbaikan` mengalami error saat dilihat detailnya oleh admin.  
   *Mitigasi*: Peta badge status (`$badgeClassMap` dan `$badgeBorderMap`) di `show.blade.php` serta timeline rekam jejak tetap mempertahankan styling untuk status `perlu_perbaikan`.
3. **Risiko**: Benturan dengan regression test existing.  
   *Mitigasi*: Seluruh test suite pendaftaran (92 tests) telah diverifikasi pada baseline; test yang menguji keputusan `perlu_perbaikan` akan diperbarui secara sinkron.

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Tombol switcher keputusan verifikasi berkas di `admin.pendaftaran.show` hanya menampilkan 2 opsi: **Terima** dan **Tolak**.
- [x] Tombol switcher berformat 2 kolom simetris yang rapi tanpa elemen yang terdorong ke baris bawah.
- [x] Request payload verifikasi berkas hanya menerima `'terima'` dan `'tolak'`.
- [x] Opsi `'perlu_perbaikan'` tidak lagi dapat disubmit melalui form maupun API endpoint.
- [x] Script JavaScript `pendaftaran-show.js` bersih dari referensi `catatan_perbaikan`.
- [x] Seluruh automated test pendaftaran (termasuk `AdminPendaftaranPerbaikanFlowTest`) berjalan 100% hijau (PASS).
- [x] Build Vite `npm run build` sukses tanpa warning/error.
