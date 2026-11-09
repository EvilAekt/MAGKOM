# Rencana Kerja: Otomatisasi Bidang Dinas & Penyederhanaan Penetapan Pembimbing

**Dokumen Rencana**: `docs/plans/2026-11-09-otomatisasi-bidang-dan-penyederhanaan-penempatan-pembimbing.md`  
**Tanggal**: 9 November 2026  
**Status**: Draf Perencanaan (Menunggu Persetujuan Pengguna)  
**Target Modul**: 
- `resources/views/admin/penempatan/index.blade.php` (Modal `#modal-tetapkan-penempatan`)
- `app/Http/Controllers/Admin/PenempatanController.php` (`tetapkan`)
- `app/Services/PlacementService.php` (`tetapkanPenempatan`)
- `tests/Feature/AdminPenempatanDuaTahapTest.php`

---

## 1. Latar Belakang & Analisis Kebutuhan

Saat calon peserta mendaftar magang (*registrasi*), peserta telah memilih Kuota / Jurusan yang secara sistem telah dipetakan dan terhubung ke **Bidang Dinas** tertentu di lingkungan Diskominfo Sleman (misal: Jurusan Informatika terhubung ke Bidang Aplikasi Informatika / E-Government).

### Masalah Saat Ini:
Pada modal penetapan penempatan Tahap 2 (`modal-tetapkan-penempatan`), Admin masih dibebani dengan dropdown pilihan manual:
1. `Unit Bidang Dinas *` (`<select name="bidang_id">`)
2. `Pembimbing Lapangan *` (`<select name="pembimbing_id">`)

Hal ini tidak efisien dan rentan terjadi salah pilih bidang yang tidak sesuai dengan kualifikasi jurusan pendaftar.

### Solusi yang Diinginkan Pengguna:
- Admin **tidak perlu lagi memilih bidang dinas secara manual**.
- Bidang dinas otomatis terhubung dan terisi dari data registrasi peserta (kuota/jurusan).
- Admin **hanya tinggal memilih Pembimbing Lapangan** yang akan membimbing peserta tersebut.
- Bidang dinas tetap ditampilkan di modal sebagai informasi *read-only* agar Admin mengetahui unit kerja peserta dengan jelas.

---

## 2. Evaluasi Opsi Arsitektur

### Opsi A (Rekomendasi Utama): Read-Only Preview + Hidden Input + Robust Backend Fallback
1. **Di Modal View (`index.blade.php`)**:
   - Menghapus elemen `<select name="bidang_id">`.
   - Menggantinya dengan kartu informasi *read-only* yang rapi:
     - **Peserta**: Budi Santoso (NIM & Kampus/Sekolah)
     - **Jurusan**: Teknik Informatika
     - **Unit Bidang Dinas (Otomatis)**: Aplikasi Informatika & E-Government (EGOV)
   - Menyertakan `<input type="hidden" name="bidang_id" id="tetapkan-bidang-id">` yang otomatis diisi nilai bidang terhubung oleh JavaScript `openTetapkanModal`.
   - Admin hanya fokus pada satu kontrol interaktif: dropdown `<select name="pembimbing_id" required>`.
2. **Di Backend (`PenempatanController.php` & `PlacementService.php`)**:
   - Jika `bidang_id` tidak ada di request form, sistem secara otomatis mengambil bidang dari relasi:
     `$pendaftaran->kuota?->jurusan?->bidang?->first() ?? $pembimbing->bidang`.
   - Di `PlacementService`, fallback `$bidangId` diperkuat agar memeriksa relasi jurusan ke bidang.

*Kelebihan*:
- Alur kerja admin menjadi sangat cepat dan bebas salah pilih unit dinas.
- Zero breaking changes terhadap test atau kontrak yang sudah ada.
- Transparan: Admin tetap melihat konfirmasi bidang dinas peserta sebelum menetapkan pembimbing.

### Opsi B: Hapus Total Input Bidang dari Frontend
- Menghapus input `bidang_id` sepenuhnya tanpa hidden input, dan 100% resolusi dilakukan di backend.
- *Kekurangan*: Admin kehilangan kepastian visual mengenai unit bidang dinas tujuan saat memilih pembimbing di modal.

> **Keputusan**: Menggunakan **Opsi A**.

---

## 3. Rencana Perubahan Berkas

| Berkas | Aksi | Ringkasan Perubahan |
|---|:---:|---|
| `resources/views/admin/penempatan/index.blade.php` | Modify | 1. Modal `#modal-tetapkan-penempatan`: ganti `<select name="bidang_id">` dengan display kartu read-only unit bidang terhubung.<br>2. Tambahkan hidden input `name="bidang_id"`.<br>3. Perbarui JS `openTetapkanModal` untuk menginjeksi nama & ID bidang.<br>4. Sederhanakan info banner modal menjadi fokus penetapan pembimbing. |
| `app/Http/Controllers/Admin/PenempatanController.php` | Modify | Pada method `tetapkan()`, tambahkan fallback resolusi otomatis bidang dinas dari pendaftaran jika parameter `bidang_id` tidak disertakan. |
| `app/Services/PlacementService.php` | Modify | Perkuat fallback `$bidangId` di `tetapkanPenempatan()` agar memeriksa `$p->kuota?->jurusan?->bidang?->first()?->id` dan `$pembimbing->bidang_id`. |
| `tests/Feature/AdminPenempatanDuaTahapTest.php` | Modify | Tambahkan test assertion bahwa admin dapat menetapkan penempatan tanpa mengirim `bidang_id` manual (otomatis terhubung dari pendaftaran). |

---

## 4. Langkah Implementasi Terstruktur

- [ ] **Langkah 1**: Perbarui `app/Services/PlacementService.php` agar fallback penentuan bidang penempatan otomatis mengambil dari `$p->kuota?->jurusan?->bidang?->first()?->id` atau `$pembimbing->bidang_id`.
- [ ] **Langkah 2**: Perbarui `app/Http/Controllers/Admin/PenempatanController.php` pada method `tetapkan()` untuk menangani pendaftaran tanpa input manual `bidang_id`.
- [ ] **Langkah 3**: Redesain modal `#modal-tetapkan-penempatan` di `resources/views/admin/penempatan/index.blade.php`:
  - Hapus dropdown `<select name="bidang_id">`.
  - Pasang komponen display informasi read-only untuk Unit Bidang Dinas otomatis.
  - Sediakan hidden input `<input type="hidden" name="bidang_id" id="tetapkan-bidang-id">`.
  - Satu-satunya kontrol input interaktif yang dipilih admin adalah **Pembimbing Lapangan**.
- [ ] **Langkah 4**: Perbarui fungsi JavaScript `openTetapkanModal(...)` agar mengisikan nama bidang ke elemen display dan ID bidang ke hidden input.
- [ ] **Langkah 5**: Tambahkan test feature di `tests/Feature/AdminPenempatanDuaTahapTest.php` untuk memvalidasi alur otomatisasi bidang dinas saat admin hanya memilih pembimbing.
- [ ] **Langkah 6**: Jalankan Quality Gates:
  - `php artisan test --filter AdminPenempatanDuaTahapTest`
  - `php artisan test --filter AdminChildPagesVisualTest`
  - `vendor\bin\pint.bat --test`
  - `npm run build`

---

## 5. Strategi Pengujian & Mitigasi Risiko

1. **Integritas Relasi Bidang & Kuota**:
   - Memastikan bahwa setiap peserta antrean yang lolos berkas memiliki relasi jurusan yang terhubung ke bidang dinas.
   - Jika terdapat jurusan yang terhubung ke lebih dari 1 bidang atau bidang belum diset, fallback ke bidang asal pembimbing (`$pembimbing->bidang_id`) memastikan data tidak akan menghasilkan error foreign key null.
2. **Keamanan (OWASP & Authorization)**:
   - Tetap divalidasi oleh `TetapkanPenempatanRequest` (hanya Admin yang berhak menugaskan pembimbing).
   - Audit trail tetap mencatat penetapan pembimbing lapangan dan unit bidang dinas secara akurat.

