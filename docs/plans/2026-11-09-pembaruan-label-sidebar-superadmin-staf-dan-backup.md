# Rencana Kerja: Pembaruan Label Sidebar SuperAdmin ("Data Staf" & "Backup Database")

> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Status Dokumen:** COMPLETED & VERIFIED (SEMUA QUALITY GATES LOLOS)  
> **Tingkat Dampak:** Ringan (*Frontend Markup Presentation & Localization*)  
> **Dokumen Terkait:** `AGENTS.md`, `.agents/rules/AGENTS.md`, `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`  

---

## 1. 🎯 Tujuan
Memperbarui label navigasi pada sidebar SuperAdmin di bawah grup navigasi **SISTEM**:
1. Mengubah menu **"Data Pengguna"** (`route('superadmin.staf.index')`) menjadi **"Data Staf"**.
2. Mengubah menu **"Cadangan Database"** (`route('superadmin.backup.index')`) menjadi **"Backup Database"**.
3. Memastikan pembaruan label tetap mematuhi standar aksesibilitas WCAG 2.1 AA (*screen-reader text* `sr-only`) dan mendukung lokalisasi bilingual (ID & EN) pada sistem kamus bahasa Laravel.

---

## 2. 💡 Asumsi & Pertanyaan Terbuka

### 2.1 Asumsi Teknis
1. **Scope Fokus Sidebar**: Perubahan difokuskan secara spesifik pada teks label navigasi sidebar SuperAdmin di [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php).
2. **Kontrak Rute & Backend Tetap**: Nama rute (`superadmin.staf.index`, `superadmin.backup.index`), Controller, Service, dan kebijakan otorisasi (RBAC) tidak mengalami perubahan apa pun.
3. **Ikon Navigasi**: Ikon SVG eksisting (Ikon User-Plus untuk Staf dan Ikon Tabung/Silinder Database untuk Backup) dipertahankan karena sudah representatif.
4. **Kesesuaian Halaman Tujuan**:
   - Halaman `superadmin.staf.index` sudah menggunakan tajuk *"Manajemen Staf Internal"*, sehingga perubahan label sidebar ke *"Data Staf"* membuat antarmuka menjadi jauh lebih selaras dan konsisten.
   - Halaman `superadmin.backup.index` menangani pembuatan snapshot database (disaster recovery).

### 2.2 Pertanyaan Terbuka / Opsi Perluasan (Untuk Pengguna)
- **Pertanyaan**: Apakah Anda juga ingin menyelaraskan teks judul halaman (*topbar title* & *breadcrumb*) pada halaman backup ([`resources/views/superadmin/backup/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/backup/index.blade.php)) dari *"Cadangan Basis Data"* menjadi *"Backup Database"*, atau perubahan cukup dibatasi pada sidebar saja?  
  *(Rencana kerja di bawah mengasumsikan fokus utama pada sidebar sesuai instruksi).*

---

## 3. 🛠️ Pendekatan Teknis

### Perbandingan Pendekatan

| Aspek | Opsi A: Blade & Localization JSON (Direkomendasikan) | Opsi B: Hardcoded String di Blade |
| :--- | :--- | :--- |
| **Implementasi** | Menggunakan `{{ __('Data Staf') }}` dan `{{ __('Backup Database') }}` serta mendaftarkan kuncinya di `lang/id.json` dan `lang/en.json`. | Mengganti teks langsung di Blade tanpa mendaftarkan ke file bahasa. |
| **Kelebihan** | Mendukung fitur pengalihan bahasa (ID/EN) yang sudah aktif di MagKom; mematuhi standar arsitektur proyek. | Cepat dibuat. |
| **Kekurangan** | Memerlukan pembaruan kecil pada file JSON kamus bahasa. | Jika bahasa portal diubah ke EN oleh SuperAdmin, label akan tetap berbahasa campuran / hardcoded. |
| **Rekomendasi** | **Dipilih (Opsi A)** karena menjaga integritas sistem lokalisasi. | Tidak direkomendasikan. |

---

## 4. 📂 Rincian Perubahan File

| No | File | Aksi | Ringkasan Perubahan |
|:---:| :--- | :---: | :--- |
| 1 | `resources/views/layouts/dashboard.blade.php` | **Modify** | Memperbarui label navigasi baris ~3345 (`Data Pengguna` -> `Data Staf`) dan baris ~3391 (`Cadangan Database` -> `Backup Database`) beserta teks pendukung aksesibilitas `sr-only`. |
| 2 | `lang/id.json` | **Modify** | Mendaftarkan key translasi `"Data Staf": "Data Staf"` dan `"Backup Database": "Backup Database"`. |
| 3 | `lang/en.json` | **Modify** | Mendaftarkan key translasi `"Data Staf": "Staff Data"` dan `"Backup Database": "Backup Database"`. |
| 4 | `tests/Feature/SuperAdminPortalVisualTest.php` | **Modify** | Menambahkan pengujian integrasi asserting bahwa sidebar SuperAdmin merender "Data Staf" dan "Backup Database" serta tidak merender teks lama. |

> ⚠️ **Catatan Kepatuhan Aturan Blade (`AGENTS.md`)**:  
> Perubahan pada file Blade `dashboard.blade.php` **hanya** menyentuh node teks di dalam tag `<span>` navigasi. Tidak ada penambahan tag `<style>`, skrip `<script>`, inline style `style="..."`, maupun inline event handler.

---

## 5. 📋 Langkah Implementasi Bertahap

- [x] **Langkah 1: Registrasi Kamus Bahasa (Localization)**
  - [x] Tambahkan pasangan key-value pada `lang/id.json`:
    - `"Data Staf": "Data Staf"`
    - `"Backup Database": "Backup Database"`
  - [x] Tambahkan pasangan key-value pada `lang/en.json`:
    - `"Data Staf": "Staff Data"`
    - `"Backup Database": "Backup Database"`

- [x] **Langkah 2: Pembaruan Markup Sidebar SuperAdmin**
  - [x] Buka file [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) pada bagian `<!-- GRUP 2: SISTEM -->`.
  - [x] Ubah item menu rute `superadmin.staf.index`:
    ```html
    <!-- Sebelum -->
    <span>{{ __('Data Pengguna') }}<span class="sr-only"> / {{ __('Manajemen Staf') }} {{ __('Pengguna') }}</span></span>

    <!-- Sesudah -->
    <span>{{ __('Data Staf') }}<span class="sr-only"> / {{ __('Manajemen Staf') }}</span></span>
    ```
  - [x] Ubah item menu rute `superadmin.backup.index`:
    ```html
    <!-- Sebelum -->
    <span>{{ __('Cadangan Database') }}</span>
    <span class="sr-only">Disaster Recovery Cadangan Basis Data</span>

    <!-- Sesudah -->
    <span>{{ __('Backup Database') }}</span>
    <span class="sr-only">Disaster Recovery Backup Database</span>
    ```

- [x] **Langkah 3: Pembuatan & Pembaruan Test Suite**
  - [x] Tambahkan method test baru di [`tests/Feature/SuperAdminPortalVisualTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminPortalVisualTest.php):
    - `test_superadmin_sidebar_renders_updated_navigation_labels_for_staf_and_backup()`:
      - Memastikan halaman dashboard SuperAdmin menampilkan teks `"Data Staf"` dan `"Backup Database"`.
      - Memastikan teks lama `"Data Pengguna"` dan `"Cadangan Database"` tidak lagi muncul di sidebar.

- [x] **Langkah 4: Verifikasi & Quality Gates**
  - [x] Jalankan test visual/layout: `php artisan test --filter=SuperAdminPortalVisualTest`
  - [x] Jalankan test lokalisasi: `php artisan test --filter=LanguageLocalizationTest`
  - [x] Jalankan linter format: `./vendor/bin/pint --test`
  - [x] Periksa `git diff` untuk memastikan zero side-effects.

---

## 6. 🧪 Strategi Testing & Quality Assurance

1. **Feature Test (Automated)**:
   - Command: `php artisan test --filter=SuperAdminPortalVisualTest`
   - Target verifikasi:
     - Response status: `200 OK`.
     - Teks `Data Staf` terlihat di view.
     - Teks `Backup Database` terlihat di view.
     - Tidak ada regresi pada menu-menu sistem lainnya (Hak Akses, Audit Log, Status Sistem, Konfigurasi).
2. **Localization Test (Automated)**:
   - Command: `php artisan test --filter=LanguageLocalizationTest`
   - Memastikan saat berganti bahasa ke English (`en`), teks berubah menjadi `"Staff Data"` dan `"Backup Database"`.
3. **Manual / Visual Verification**:
   - Memeriksa rendering sidebar pada desktop (lebar normal) dan kondisi *nav-sm* (sidebar menciut) untuk memastikan tooltip & text flow tetap proporsional dan tidak terpotong.

---

## 7. ⚠️ Risiko & Mitigasi

| Risiko | Tingkat | Mitigasi |
| :--- | :---: | :--- |
| Kerusakan translasi jika format JSON rusak | Rendah | Validasi JSON linter sebelum menyimpan `lang/id.json` dan `lang/en.json`. |
| Pelanggaran aturan Blade AGENTS.md | Rendah | Perubahan dibatasi strictly pada text node terjemahan tanpa mengubah markup wrapper, class CSS, atau menambahkan atribut inline. |
| Dampak regresi pada testing eksisting | Rendah | Telah diverifikasi bahwa tidak ada unit test eksisting yang memvalidasi string `"Cadangan Database"` atau `"Data Pengguna"`. |

---

## 8. ✅ Kriteria Selesai (Definition of Done)

- [x] Teks menu navigasi SuperAdmin untuk modul staf tertulis **"Data Staf"**.
- [x] Teks menu navigasi SuperAdmin untuk modul backup tertulis **"Backup Database"**.
- [x] Aksesibilitas screen-reader (`sr-only`) tetap terpasang dengan semantik yang tepat.
- [x] File translasi `lang/id.json` dan `lang/en.json` terkonfigurasi dengan benar.
- [x] Test suite `SuperAdminPortalVisualTest` berhasil dijalankan dan berstatus HIJAU (PASS).
- [x] Kode terformat rapi sesuai standar Laravel Pint.

