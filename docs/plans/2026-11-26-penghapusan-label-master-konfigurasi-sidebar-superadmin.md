# Rencana Kerja: Penghapusan Label Sub-divider "MASTER & KONFIGURASI" pada Sidebar SuperAdmin

> **Dokumen Rencana Kerja (Planning Document)**  
> **Tanggal Pembuatan**: 26 November 2026  
> **Status**: Selesai Diimplementasikan & Terverifikasi (Completed & Verified)  
> **Referensi Permintaan**: Menghapus teks sub-divider label "MASTER & KONFIGURASI" pada menu dropdown *Administrasi* di bilah samping (*sidebar*) SuperAdmin sesuai tangkapan layar 1 & 2 (`media_1795694285887_017732b6.png` dan `media_1795694302790_6550a7ce.png`).

---

## 1. Tujuan
Menghapus elemen teks label sub-divider **`MASTER & KONFIGURASI`** yang terletak di dalam menu bertingkat (*dropdown submenu*) **Administrasi** pada panel navigasi samping (*sidebar*) SuperAdmin.  
Dengan dihapusnya label ini, sub-menu master (*Jalur Khusus*, *Data Jurusan*, dan *Unit Kerja*) akan langsung tersusun rapi menyatu di bawah item operasional (*Arsip Peserta*) tanpa jeda teks pemisah yang memecah hierarki menu.

---

## 2. Asumsi & Analisis Konteks

### 2.1 Asumsi Kerja Logis
1. **Target Tepat Sesuai Tangkapan Layar**:
   - Pada tangkapan layar 1 (`media_1795694285887_017732b6.png`) dan tangkapan layar 2 (`media_1795694302790_6550a7ce.png`), teks yang dimaksud adalah elemen `<div class="nav-sub-divider-label">{{ __('MASTER & KONFIGURASI') }}</div>` di dalam navigasi SuperAdmin (`resources/views/layouts/dashboard.blade.php` baris 502).
2. **Kepatuhan Aturan Repositori (AGENTS.md - Modifikasi Blade)**:
   - Modifikasi file Blade `resources/views/layouts/dashboard.blade.php` diperbolehkan karena tugas ini secara eksplisit meminta penghapusan elemen markup HTML yang tampil di layar.
   - Penghapusan elemen tidak menyentuh tautan sub-item (*Jalur Khusus*, *Data Jurusan*, *Unit Kerja*), routing, permission, maupun fungsionalitas backend.
3. **Penyelarasan Pengujian Otomatis (Automated Test)**:
   - Terdapat pengujian eksisting pada `tests/Feature/SuperAdminAdministrasiMenuAlignmentTest.php` baris 45 yang memvalidasi `$response->assertSee('MASTER & KONFIGURASI');`.
   - File test tersebut wajib diperbarui menjadi `$response->assertDontSee('MASTER & KONFIGURASI');` agar test suite tetap selaras dengan kebutuhan baru dan 100% *passed*.

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter Evaluasi | Opsi 1 (Direkomendasikan): Penghapusan Bersih dari Template Blade | Opsi 2: Penyembunyian Visual via CSS (`display: none`) |
| :--- | :--- | :--- |
| **Kebersihan Kode** | **Sangat Bersih**: Menghilangkan markup yang tidak lagi digunakan langsung dari sumber Blade. | Menyisakan elemen DOM yang tidak terpakai (*dead markup*) di HTML. |
| **Integritas Test** | Test PHPUnit dapat dengan tegas memvalidasi bahwa teks tersebut benar-benar tidak dirender ke browser. | Test HTML `$response->assertSee` masih melihat teks tersebut ada di DOM meskipun disembunyikan CSS. |
| **Kinerja & Semantik** | Mengurangi ukuran payload HTML dan struktur aksesibilitas (*screen reader*) menjadi lebih bersih. | Screen reader tetap membaca divider kosong jika tidak diatur `aria-hidden`. |

> **Rekomendasi Terpilih**: **Opsi 1**. Menghapus baris markup `<div class="nav-sub-divider-label">{{ __('MASTER & KONFIGURASI') }}</div>` dari `resources/views/layouts/dashboard.blade.php` dan menyesuaikan assertion test di `SuperAdminAdministrasiMenuAlignmentTest.php`.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/layouts/dashboard.blade.php` | **Modify** | Menghapus elemen baris 502: `<div class="nav-sub-divider-label">{{ __('MASTER & KONFIGURASI') }}</div>` pada navigasi submenu Administrasi SuperAdmin. |
| `tests/Feature/SuperAdminAdministrasiMenuAlignmentTest.php` | **Modify** | Memperbarui baris 45 dari `$response->assertSee('MASTER & KONFIGURASI');` menjadi `$response->assertDontSee('MASTER & KONFIGURASI');`. |
| `docs/plans/2026-11-26-penghapusan-label-master-konfigurasi-sidebar-superadmin.md` | **Create** | Dokumen rencana kerja terstruktur ini. |

---

## 5. Langkah Implementasi Bertahap

- [x] **Langkah 1: Persiapan & Konfirmasi Working Tree**
  - Pastikan working tree git bersih dan berada di branch `test-2`.
- [x] **Langkah 2: Penghapusan Elemen di `dashboard.blade.php`**
  - Hapus baris 502 `<div class="nav-sub-divider-label">{{ __('MASTER & KONFIGURASI') }}</div>` di `resources/views/layouts/dashboard.blade.php`.
- [x] **Langkah 3: Pembersihan Cache Template Blade**
  - Jalankan `php artisan optimize:clear` (menggunakan runtime PHP 8.3) agar template Blade yang dicache ter-refresh.
- [x] **Langkah 4: Pembaruan & Eksekusi Test Suite**
  - Perbarui assertion di `tests/Feature/SuperAdminAdministrasiMenuAlignmentTest.php`.
  - Jalankan pengujian `php artisan test tests/Feature/SuperAdminAdministrasiMenuAlignmentTest.php`.
- [x] **Langkah 5: Penyelesaian Dokumen Plan & Laporan ke Pengguna**
  - Tandai checklist selesai pada dokumen plan.
  - Laporkan hasil kepada pengguna.

---

## 6. Strategi Testing & Validasi

1. **Automated Feature Test**:
   ```bash
   php artisan test tests/Feature/SuperAdminAdministrasiMenuAlignmentTest.php
   ```
   - Memastikan respons halaman dashboard SuperAdmin mengembalikan HTTP 200 OK.
   - Memastikan 6 item operasional (*Verifikasi Berkas, Kuota, Penempatan, Surat Selesai, Laporan, Arsip*) tetap muncul.
   - Memastikan 3 item master (*Jalur Khusus, Data Jurusan, Unit Kerja*) tetap muncul.
   - Memastikan teks `MASTER & KONFIGURASI` **tidak lagi muncul** di sidebar SuperAdmin (`assertDontSee`).
2. **Visual Check**:
   - Memastikan alur tree menu di bawah *Administrasi* mengalir mulus dari *Arsip Peserta* langsung ke *Jalur Khusus*.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat | Rencana Mitigasi |
| :--- | :--- | :--- |
| **Kegagalan Unit/Feature Test** | Rendah | Mengupdate assertion di `SuperAdminAdministrasiMenuAlignmentTest.php` agar selaras dengan penghapusan label. |
| **Gangguan Styling Submenu Tree** | Sangat Rendah | Garis penghubung tree sidebar (`.nav-sub-item`) terikat pada elemen link `<a>`, sehingga penghapusan div label pembatas justru membuat garis vertikal tree tersambung rapi tanpa jeda. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Elemen `<div class="nav-sub-divider-label">{{ __('MASTER & KONFIGURASI') }}</div>` telah dihapus dari `resources/views/layouts/dashboard.blade.php`.
- [x] Teks "MASTER & KONFIGURASI" tidak lagi muncul di sidebar SuperAdmin.
- [x] Menu "Jalur Khusus", "Data Jurusan", dan "Unit Kerja" tetap berfungsi normal.
- [x] Pengujian otomatis pada `tests/Feature/SuperAdminAdministrasiMenuAlignmentTest.php` lolos 100% (Green).
