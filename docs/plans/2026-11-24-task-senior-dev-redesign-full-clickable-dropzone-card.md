# [TASK / PRD SENIOR DEV] Redesain Full Clickable Dropzone Card (Seluruh Area Dropzone Dapat Diklik Langsung)

> **Ticket ID**: `TASK-MAGKOM-SS-008`  
> **Author**: Senior Lead Developer (Antigravity)  
> **Assignee**: Junior Full-Stack Developer / Autonomous Coding AI (Gemini 3.8 Flash)  
> **Status**: `COMPLETED & VERIFIED`  
> **Target Release**: Sprint MagKom v1.1.5  
> **Files Affected**:
> - `resources/css/pages/admin/surat-selesai-index.css`
> - `resources/views/admin/penempatan/partials/_show_kelompok.blade.php`
> - `resources/views/admin/surat-selesai/index.blade.php`
> - `resources/js/pages/admin/penempatan-show.js`
> - `resources/js/pages/admin/surat-selesai-index.js`
> - `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php`

---

## 1. Latar Belakang & Analisis Permintaan Pengguna

### 1.1 Kebutuhan Pengguna (User Request)
Pengguna memberikan masukan UX terkait area upload:
> *"di buat hilang aja jadi seluruh cart dari pilih berkas pdf di klik langsung jang yg kecil telusuri itu"*

Pengguna menyertakan 2 bukti visual:
1. **Target yang Dihilangkan**: Tombol kecil `[ 📎 Telusuri Berkas PDF ]` (`.btn-custom-file-picker`).
2. **Perilaku Baru yang Diinginkan**: Seluruh area dashed card `Pilih Berkas PDF Surat Selesai` (`.upload-dropzone-panel`) dapat **diklik langsung di mana saja** untuk membuka dialog pemilihan berkas PDF sistem (file picker).

### 1.2 Masalah UX Saat Ini
- Saat ini, pengguna harus mengarahkan kursor secara presisi ke tombol kecil `[ 📎 Telusuri Berkas PDF ]` di tengah card untuk memilih file. Mengklik di area dashed card lain tidak melakukan apa pun.
- Tombol kecil tersebut menciptakan lapisan visual redundan di dalam card dashed yang sudah berstatus dropzone.

### 1.3 Solusi Senior Dev
- Ubah elemen pembungkus `.upload-dropzone-panel` menjadi elemen `<label for="...">` dengan styling `cursor: pointer;`.
- Browser secara native akan membuka dialog pemilihan file saat pengguna mengklik **di bagian mana pun** dalam card dashed tersebut (zero friction).
- Hapus markup tombol kecil `[ 📎 Telusuri Berkas PDF ]`.
- Tambahkan status visual interaktif:
  - **Hover**: border navy Sleman (`#003566`), background lembut (`#F0F7FF`).
  - **Dragover**: border aktif biru Sleman (`#1D4ED8`), indikator drag-and-drop aktif.
  - **File Selected**: chip nama berkas (`.dynamic-file-chip`) tampil anggun di dalam card dengan icon centang hijau `✓ [Nama File] ([Ukuran] MB)`.

---

## 2. Perbandingan Wireframe (Before vs After)

### 2.1 Kondisi Saat Ini (Before - Tombol Kecil di Tengah)
```
+-------------------------------------------------------------------------------+
| Formulir Unggah Surat Selesai                                                 |
| Unggah berkas resmi format PDF (Maks. 2.0 MB).                                |
|                                                                               |
| + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - + |
| |                         [ ☁️ ] (Upload Icon)                               | |
| |                 Pilih Berkas PDF Surat Selesai                            | |
| |                                                                           | |
| |             [ 📎 Telusuri Berkas PDF ]  <--- ❌ HAPUS TOMBOL KECIL INI      | |
| |                                                                           | |
| + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - + |
|                                                                               |
| [ ⭡ Unggah Surat Resmi (Sleman Navy)                                        ] |
+-------------------------------------------------------------------------------+
```

### 2.2 Desain Baru yang Diinginkan (After - Full Clickable Dropzone Card)
```
+-------------------------------------------------------------------------------+
| Formulir Unggah Surat Selesai                                                 |
| Unggah berkas resmi format PDF (Maks. 2.0 MB).                                |
|                                                                               |
| + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - + |
| |                                                                           | |
| |                         [ ☁️ ] (Upload Icon)                               | |
| |                 Pilih Berkas PDF Surat Selesai                            | |
| |              Format berkas resmi .PDF • Maksimal 2.0 MB                   | |
| |                                                                           | |
| |     (SELURUH AREA DASHED BOX INI DAPAT DIKLIK DI MANA SAJA: CURSOR POINTER) |
| |                                                                           | |
| |        [ ✓ Surat_Selesai_Resmi.pdf (1.24 MB) ] (Muncul saat dipilih)      | |
| |                                                                           | |
| + - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - + |
|                                                                               |
| [ ⭡ Unggah Surat Resmi (Sleman Navy)                                        ] |
+-------------------------------------------------------------------------------+
```

---

## 3. Rincian Teknis Implementasi (Untuk Junior Dev & AI)

### 3.1 Perubahan HTML / Blade
Pada 3 lokasi:
1. `resources/views/admin/penempatan/partials/_show_kelompok.blade.php`
2. `resources/views/admin/surat-selesai/index.blade.php` (Mode Individu)
3. `resources/views/admin/surat-selesai/index.blade.php` (Mode Kelompok)

**Struktur Kode Baru**:
```blade
{{-- Seluruh Dropzone adalah Label HTML Native yang Terhubung ke Input File --}}
<label for="file_surat_ind" class="upload-dropzone-panel" tabindex="0" role="button" aria-label="{{ __('Pilih atau seret berkas PDF surat selesai magang') }}">
    <div class="dropzone-icon-circle" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="17 8 12 3 7 8"></polyline>
            <line x1="12" y1="3" x2="12" y2="15"></line>
        </svg>
    </div>
    
    <h4 class="dropzone-prompt-title">{{ __('Pilih Berkas PDF Surat Selesai') }}</h4>
    <p class="dropzone-prompt-desc">{{ __('Klik di mana saja atau seret berkas .PDF (Maks. 2.0 MB)') }}</p>

    {{-- File Input Disembunyikan Secara Visual tapi Tetap Fungsional & Accessible --}}
    <input type="file" name="file_surat" id="file_surat_ind" accept="application/pdf" required class="visually-hidden-file-input">

    {{-- Chip Nama File: Tampil Dinamis di Dalam Card --}}
    <div class="dynamic-file-chip" id="previewFileNameInd"></div>
</label>
```

### 3.2 Perubahan CSS (`resources/css/pages/admin/surat-selesai-index.css`)
```css
.upload-dropzone-panel {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 20px 16px;
    margin: 4px 0 0;
    background: #F8FAFC;
    border: 1.5px dashed #CBD5E1;
    border-radius: 10px;
    cursor: pointer;
    user-select: none;
    position: relative;
    outline: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.upload-dropzone-panel:hover,
.upload-dropzone-panel:focus-visible {
    background: #F0F7FF;
    border-color: #003566;
    box-shadow: 0 4px 12px rgba(0, 53, 102, 0.08);
}

.upload-dropzone-panel.is-dragover {
    background: #EFF6FF;
    border-color: #1D4ED8;
    border-style: solid;
    box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.2);
}

.dropzone-prompt-desc {
    margin: 0 0 4px;
    font-size: 0.775rem;
    color: #64748B;
    line-height: 1.35;
    pointer-events: none;
}
```

### 3.3 Perubahan JavaScript (`resources/js/pages/admin/`)
Di `penempatan-show.js` dan `surat-selesai-index.js`:
- Event keyboard (Enter / Space) pada label dengan `tabindex="0"` membuka file picker secara accessible.
- Listener `dragover`, `dragleave`, `drop` pada `.upload-dropzone-panel`:
  - `dragover`: cegah default, tambahkan class `.is-dragover`.
  - `dragleave` & `drop`: hapus class `.is-dragover`.
  - `drop`: cegah default, ambil `e.dataTransfer.files`, setel ke `input.files`, dan dispatch event `'change'`.

---

## 4. Checklist Rencana Kerja (Implementation Checklist)

- [x] **Step 1**: Modifikasi CSS di `resources/css/pages/admin/surat-selesai-index.css`:
  - Atur `.upload-dropzone-panel` menjadi `cursor: pointer; user-select: none;`
  - Tambahkan efek visual hover & active dragover.
  - Tambahkan style `.dropzone-prompt-desc`.
- [x] **Step 2**: Modifikasi Blade di `resources/views/admin/penempatan/partials/_show_kelompok.blade.php`:
  - Ubah kontainer menjadi `<label for="file_surat_kel_show" class="upload-dropzone-panel">`.
  - Hapus tombol kecil `[ 📎 Telusuri Berkas PDF ]`.
  - Sertakan teks petunjuk ramah dan chip status di dalam label.
- [x] **Step 3**: Modifikasi Blade di `resources/views/admin/surat-selesai/index.blade.php`:
  - Terapkan hal yang sama pada Mode Individu (`#file_surat_ind`) dan Mode Kelompok (`#file_surat_kel`).
- [x] **Step 4**: Sesuaikan JavaScript di `penempatan-show.js` dan `surat-selesai-index.js`:
  - Tambahkan dukungan drag-and-drop native yang menyambung langsung ke event validasi file yang sudah ada.
- [x] **Step 5**: Jalankan `npm run build` untuk kompilasi Vite.
- [x] **Step 6**: Jalankan PHPUnit feature tests di PHP 8.3 (`php artisan test tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php`).
- [x] **Step 7**: Jalankan `./vendor/bin/pint --test` dan update Graphify AST.

---

## 5. Kriteria Selesai (Definition of Done)

- [x] Tombol kecil `[ 📎 Telusuri Berkas PDF ]` telah dihapus sepenuhnya dari semua tampilan terkait.
- [x] Seluruh area dashed card dropzone dapat diklik di mana saja untuk langsung memicu pemilihan berkas PDF.
- [x] Kursor mouse berubah menjadi `pointer` saat berada di atas card dropzone.
- [x] Efek visual hover/dragover memberikan umpan balik yang jelas kepada admin.
- [x] Nama berkas yang dipilih muncul sebagai chip status rapi di dalam card dropzone.
- [x] Tidak ada tag `<style>` atau `<script>` yang dimasukkan ke Blade.
- [x] Seluruh automated test (13 tests, 113 assertions) 100% PASS di runtime PHP 8.3.
- [x] Build Vite `npm run build` berhasil tanpa error.
