# Rencana Teknis: Penghapusan Tombol Penempatan Jalur Khusus pada Halaman Penempatan Peserta Admin

Dokumen ini disusun berdasarkan skill `web-planning` untuk merencanakan penghapusan tombol redundan **"+ Penempatan Jalur Khusus"** pada hero bar halaman Penempatan Peserta Admin (`/admin/penempatan`), karena pengelolaan alokasi slot dan pendaftaran peserta jalur khusus telah terpusat di menu **MASTER & KONFIGURASI -> Jalur Khusus** (`/admin/slot-khusus`).

---

## 1. Tujuan

Menghapus tombol **"+ Penempatan Jalur Khusus"** (`data-action="open-manual-modal"`) dari header action bar pada halaman Penempatan Peserta Admin (`resources/views/admin/penempatan/index.blade.php`), menyederhanakan alur kerja pengguna agar tidak terjadi redundansi dengan modul **Jalur Khusus** di menu Master & Konfigurasi.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. Tombol yang dimaksud adalah tombol berwarna putih beraksen biru dengan ikon tambah bertuliskan **"+ Penempatan Jalur Khusus"** di sisi kanan hero banner penempatan (sesuai screenshot `media_1795438092328_dd0aee66.png`).
2. Modul pendaftaran dan penempatan peserta jalur khusus tetap tersedia dan dapat diakses melalui menu navigasi sidebar **MASTER & KONFIGURASI -> Jalur Khusus** (`/admin/slot-khusus` dan `/admin/peserta-khusus/create`).
3. Endpoint backend `POST /admin/penempatan/manual` tetap dipertahankan agar tidak memicu breaking change pada integritas test suite atau integrasi API yang ada.
4. Tombol aksi lain pada hero bar, yaitu tombol **"Ekspor Rekap"** beserta popover 3 formatnya (Excel, CSV, PDF), tetap tampil utuh dan berfungsi normal.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan terbuka; kebutuhan penghapusan tombol sudah sangat jelas dan spesifik.

---

## 3. Pendekatan Teknis & Evaluasi Opsi

### Perbandingan Pendekatan:

| Kriteria | Opsi 1: Hapus Tombol di Hero Action Bar Saja (Rekomendasi) | Opsi 2: Hapus Tombol Sekaligus Hapus Modal & Rute Backend Manual |
|---|---|---|
| **Dampak Visual** | **Sempurna**: Tombol di hero bar hilang sesuai screenshot dan instruksi user. | Tombol di hero bar hilang. |
| **Resiko Regresi Backend** | **Nol**: Rute `POST admin.penempatan.manual` tetap aman untuk test suite. | **Tinggi**: Mematahkan test `AdminPenempatanJalurKhususTest` dan `EndToEndUatTest`. |
| **Kepatuhan Aturan Repositori** | **Sangat Baik**: Perubahan Blade minimal (*minimal, focused change*), mempertahankan business logic. | Melanggar aturan pemeliharaan business logic eksisting. |
| **Kompleksitas Implementasi** | Rendah dan terisolasi. | Tinggi karena harus merevisi controller, form request, dan test suites. |

**Rekomendasi**: Mengadopsi **Opsi 1** dengan menghapus elemen tombol `open-manual-modal` pada hero action bar di `resources/views/admin/penempatan/index.blade.php`.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
|---|---|---|
| `resources/views/admin/penempatan/index.blade.php` | **Modify** | Menghapus elemen tombol `open-manual-modal` ("Penempatan Jalur Khusus") dari blok `@elseif(isset($slotKhususList) && $slotKhususList->isNotEmpty())` di hero bar. |
| `docs/plans/2026-11-23-penghapusan-tombol-penempatan-jalur-khusus-pada-admin-penempatan.md` | **Create** | Dokumen rencana kerja teknis ini. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1: Penyesuaian Markup Blade di `resources/views/admin/penempatan/index.blade.php`**
  - Cari blok tombol hero di baris 134-142:
    ```blade
    @elseif(isset($slotKhususList) && $slotKhususList->isNotEmpty())
        <button type="button" data-action="open-manual-modal" class="adm-hero-btn-primary">
            ...
            <span>Penempatan Jalur Khusus</span>
        </button>
    @endif
    ```
  - Hapus blok `@elseif` tombol tersebut sehingga sisi kanan hero action bar hanya memuat tombol "Ekspor Rekap" (dan badge "Mode Lihat Saja" untuk SuperAdmin).
- [x] **Langkah 2: Kompilasi Aset Frontend (Vite Build)**
  - Jalankan `npm run build` untuk memverifikasi konsistensi manifest aset.
- [x] **Langkah 3: Jalankan Quality Gates & Verifikasi Otomatis**
  - Jalankan test suite penempatan:
    `php artisan test tests/Feature/AdminPenempatanHeroStatsVisualTest.php tests/Feature/AdminChildPagesVisualTest.php tests/Feature/AdminPenempatanDuaTahapFeatureTest.php tests/Feature/AdminPenempatanJalurKhususTest.php tests/Feature/AdminSuperAdminPortalTest.php`
  - Jalankan formatting check `vendor/bin/pint --test` dan analisis statis `vendor/bin/phpstan analyse --memory-limit=1G`.
- [x] **Langkah 4: Verifikasi Visual**
  - Pastikan halaman `/admin/penempatan` tidak lagi merender tombol "+ Penempatan Jalur Khusus" di hero banner desktop maupun mobile.

---

## 6. Strategi Testing

1. **Uji Tampilan & Regresi Visual**:
   - `AdminPenempatanHeroStatsVisualTest` lolos tanpa kegagalan.
   - `AdminChildPagesVisualTest` lolos tanpa kegagalan.
   - Halaman `GET /admin/penempatan` mengembalikan status 200 dan meng-assert `assertDontSee('Penempatan Jalur Khusus')`.
2. **Uji Fungsionalitas Ekspor & Antrean Penempatan**:
   - Tombol popover ekspor rekap tetap dapat dibuka dan diunduh.
   - Tab "Belum Ditempatkan" dan "Sudah Ditempatkan" tetap berfungsi normal.
3. **Uji Kualitas Kode**:
   - Pint dan PHPStan lolos dengan 0 error.

---

## 7. Risiko & Mitigasi

- **Risiko**: Tata letak hero action bar menjadi tidak seimbang jika tombol dihapus.  
  **Mitigasi**: Hero action bar menggunakan kontainer flex (`gap: 0.75rem; align-items: center;`), sehingga saat tombol penempatan jalur khusus dihapus, tombol Ekspor Rekap tetap terposisikan rapi di sisi kanan banner.
- **Risiko**: Ada skrip JS yang mencari elemen tombol dan memicu error `null reference`.  
  **Mitigasi**: Di `resources/js/pages/admin/penempatan-index.js`, tombol ditangani melalui *event delegation* (`target.closest('[data-action="open-manual-modal"]')`), sehingga jika elemen tidak ada di DOM, tidak akan pernah memicu error runtime JS.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tombol "+ Penempatan Jalur Khusus" tidak lagi tampil di hero header halaman Penempatan Peserta Admin (`/admin/penempatan`).
- [x] Tombol "Ekspor Rekap" tetap tampil dan berfungsi normal.
- [x] Akses pendaftaran jalur khusus tetap tersedia melalui menu sidebar MASTER & KONFIGURASI -> Jalur Khusus.
- [x] Seluruh test suite penempatan, Pint, PHPStan, dan build Vite lolos 100% tanpa error.

