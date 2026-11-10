# Technical Plan: Penyederhanaan Keputusan Verifikasi Berkas Admin (Terima & Tolak Saja)

**Dokumen**: `docs/plans/2026-11-06-penyederhanaan-verifikasi-berkas-admin-terima-tolak.md`  
**Author**: Senior Backend & Frontend Developer  
**Target Modul**: Modul Verifikasi Berkas Administrasi MagKom (`admin.pendaftaran.show` & `admin.pendaftaran.putuskan`)  
**Status**: Selesai Diimplementasikan & Terverifikasi Penuh  
**Aturan Tata Kelola**: Sesuai [`AGENTS.md`](file:///d:/laragon/www/MAGKOM/.agents/rules/AGENTS.md)  

---

## 1. Latar Belakang & Analisis Kebutuhan

Berdasarkan instruksi pengguna:
> *"perubahan di bagian verifikasi berkas admin hanya bisa terima dan menolak saja waitinglist dan revisi di hapus"*

Pada alur verifikasi berkas administratif calon peserta magang saat ini ([`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php)), Administrator dihadapkan pada 4 tab keputusan:
1. **Terima**: Menyetujui pendaftaran dan mengalokasikan kuota umum.
2. **Waitlist (`waiting_list`)**: Memindahkan pendaftaran ke antrean cadangan (hanya aktif jika kuota penuh).
3. **Revisi (`perlu_perbaikan`)**: Meminta calon peserta mengunggah ulang dokumen persyaratan yang kurang lengkap.
4. **Tolak**: Menolak pengajuan pendaftaran secara permanen.

### Masalah pada Alur 4 Opsi:
- **Kompleksitas Verifikasi**: Administrator seringkali terbebani oleh status mengambang (*hanging status*) seperti `perlu_perbaikan` yang memerlukan pemantauan berulang dan tindak lanjut manual dari peserta.
- **Inkonsistensi Alokasi Kuota**: Pengalihan manual ke `waiting_list` saat kuota belum penuh sering membingungkan admin karena terbentur aturan validasi sistem (AC-04-4).
- **Kebutuhan Bisnis**: Dinas Kominfo memerlukan proses seleksi administratif yang tegas, cepat, dan berkepastian hukum: berkas yang memenuhi syarat **Diterima**, berkas yang tidak memenuhi syarat langsung **Ditolak**.

---

## 2. Ruang Lingkup Perubahan (Scope of Work)

Perubahan ini mencakup dua lapisan utama:

### 2.1 Lapangan Antarmuka Pengguna (UI/UX - Blade View)
- **Berkas**: [`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php)
- **Perubahan Spesifik**:
  1. Mengubah switcher tab keputusan `.adm-decision-tabs` dari grid 4 kolom (`repeat(4, 1fr)`) menjadi **Segmented Control 2 Pilihan Simetris (`repeat(2, 1fr)`)**:
     - **Pilihan 1: Terima** (Warna Hijau Emerald / Biru Sleman dengan ikon centang).
     - **Pilihan 2: Tolak** (Warna Merah Rose dengan ikon silang).
  2. Menghapus total tombol tab **Waitlist** (`#tab-btn-waiting_list`) dan **Revisi** (`#tab-btn-revisi`).
  3. Menghapus kontainer formulir **Panel Waiting List** (`#action-panel-waiting_list`) dan **Panel Perlu Perbaikan** (`#action-panel-perlu_perbaikan`).
  4. Menyederhanakan banner informasi kuota di atas form:
     - Jika kuota > 0: `Tersedia ({$sisaKuota} Kursi)`
     - Jika kuota <= 0: `Kuota Penuh (0 Kursi Tersedia)` (menghapus label teks *'Masuk Waiting List'*).
  5. Menyederhanakan skrip JavaScript fungsi `switchActionTab(action)`:
     - Hanya mengelola transisi aktif antara `'terima'` dan `'tolak'`.
     - Field `catatan_tolak` disetel `required` saat tab Tolak aktif; field `catatan_terima` bersifat opsional.

### 2.2 Lapangan Validasi & Controller (Backend Logic)
- **Form Request**: [`app/Http/Requests/KeputusanPendaftaranRequest.php`](file:///d:/laragon/www/MAGKOM/app/Http/Requests/KeputusanPendaftaranRequest.php)
  - Membatasi aturan validasi keputusan murni ke dua opsi:
    ```php
    'keputusan' => ['required', Rule::in(['terima', 'tolak'])],
    'catatan' => [
        Rule::requiredIf(fn () => $this->input('keputusan') === 'tolak'),
        'nullable',
        'string',
        Rule::when(
            fn () => $this->input('keputusan') === 'tolak',
            ['min:10']
        ),
        'max:1000',
    ],
    ```
- **Controller**: [`app/Http/Controllers/Admin/PendaftaranController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/PendaftaranController.php)
  - Pada method `putuskan()`, menyederhanakan alur eksekusi sehingga hanya mengeksekusi:
    - Cabang `terima`: `$this->placementService->terimaBerkas(...)`
    - Cabang `tolak`: `$this->placementService->tolak(...)`
  - Blok penanganan manual `waiting_list` dan `perlu_perbaikan` di method `putuskan()` dihapus.
- **Service Layer ([`PlacementService.php`](file:///d:/laragon/www/MAGKOM/app/Services/PlacementService.php))**:
  - Method `mintaPerbaikan()` dan `tutupWaitingListKadaluarsa()` tetap dipertahankan di level class untuk menjaga integritas data riwayat lama, namun tidak lagi dipanggil dari verifikasi admin.

---

## 3. Analisis Dampak & Mitigasi Pengujian (Automated Tests)

Beberapa berkas test fitur saat ini memiliki *test case* yang menguji pengiriman keputusan `perlu_perbaikan` dan `waiting_list` melalui endpoint `admin.pendaftaran.putuskan`:
1. `tests/Feature/AdminSuperAdminPhaseThreeTest.php`
   - Kasus: `test_admin_can_assign_waiting_list_when_quota_full`
   - Kasus: `test_validasi_catatan_minimal_10_karakter_pada_tolak_dan_revisi`
2. `tests/Feature/AdminSuperAdminPortalTest.php`
   - Kasus: `test_admin_dapat_memutuskan_status_pendaftaran` (alur revisi berkas)
3. `tests/Feature/EndToEndUatTest.php`
   - Kasus: simulasi revisi sebelum diterima

### Rencana Penyelarasan Pengujian:
- Memperbarui pengujian-pengujian tersebut agar selaras dengan aturan bisnis baru: memverifikasi bahwa pengiriman keputusan di luar `terima` dan `tolak` akan ditolak oleh validasi (`422 Unprocessable Entity`), serta memvalidasi alur sukses `terima` dan `tolak`.
- Menjalankan test suite penuh untuk menjamin semua skenario 100% Green.

---

## 4. Rencana Langkah Kerja Terperinci (Step-by-Step Task Checklist)

- [x] **Langkah 1**: Perbarui validasi form pada [`app/Http/Requests/KeputusanPendaftaranRequest.php`](file:///d:/laragon/www/MAGKOM/app/Http/Requests/KeputusanPendaftaranRequest.php):
  - Ubah `Rule::in(['terima', 'tolak', 'perlu_perbaikan', 'waiting_list'])` menjadi `Rule::in(['terima', 'tolak'])`.
  - Sesuaikan kewajiban catatan (hanya wajib min 10 karakter pada keputusan `tolak`).
- [x] **Langkah 2**: Perbarui logika penanganan keputusan pada [`app/Http/Controllers/Admin/PendaftaranController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/PendaftaranController.php) di method `putuskan()`:
  - Hapus blok cabang `waiting_list` dan `perlu_perbaikan`.
- [x] **Langkah 3**: Rombak antarmuka keputusan pada [`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php):
  - Ubah grid tab `.adm-decision-tabs` menjadi 2 kolom (`grid-template-columns: repeat(2, 1fr)`).
  - Hapus tombol tab `Waitlist` dan `Revisi`.
  - Hapus blok HTML `#action-panel-waiting_list` dan `#action-panel-perlu_perbaikan`.
  - Perbarui skrip `switchActionTab` untuk hanya beralih antara `terima` dan `tolak`.
  - Perbarui teks indikator kuota.
- [x] **Langkah 4**: Selaraskan test suite otomatis:
  - Sesuaikan skenario test di `tests/Feature/AdminSuperAdminPhaseThreeTest.php`, `tests/Feature/AdminSuperAdminPortalTest.php`, dan `tests/Feature/EndToEndUatTest.php`.
- [x] **Langkah 5**: Jalankan seluruh quality gate:
  - `php artisan test tests/Feature/AdminSuperAdminPhaseThreeTest.php tests/Feature/AdminSuperAdminPortalTest.php tests/Feature/EndToEndUatTest.php tests/Feature/PendaftaranPesertaTest.php` (29 passed, 201 assertions)
  - `npm run build` (Passed, 1.50s)
  - `./vendor/bin/pint --test` (Passed)
  - `php -l resources/views/admin/pendaftaran/show.blade.php` (No syntax errors)

---

## 5. Permohonan Persetujuan Perubahan Markup Blade (Sesuai AGENTS.md Bagian 3.3)

Sesuai aturan ketat **AGENTS.md Bagian 3.3**:
1. **Mengapa Perubahan Markup Diperlukan**: Menghilangkan opsi "Waitlist" dan "Revisi" dari antarmuka verifikasi berkas secara mutlak memerlukan penghapusan elemen tombol tab dan form container `#action-panel-waiting_list` serta `#action-panel-perlu_perbaikan` di dalam file Blade.
2. **File Target**: [`resources/views/admin/pendaftaran/show.blade.php:615-730`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php#L615-L730).
3. **Jaminan Integritas**:
   - Struktur data identitas pendaftar, pratinjau berkas PDF, riwayat status, dan audit log tidak terganggu.
   - Tidak ada styling CSS inline baru di dalam Blade.
   - Endpoint aksi form tetap mengarah ke route resmi `admin.pendaftaran.putuskan`.

