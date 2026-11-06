# [SENIOR DEV TASK] Implementasi Material Floating Label Card ala Google Classroom pada Catatan Logbook Pembimbing

**ID Task**: `TASK-UI-LOGBOOK-FLOATING-CARD-01`  
**Target Eksekutor**: Junior Full-Stack Developer / AI Model (Gemini 3.8 Flash / Claude Haiku / Cursor)  
**Author / Reviewer**: Senior Lead Full-Stack Architect  
**Repositori**: `MAGKOM` (Portal Manajemen Magang Diskominfo Sleman)  
**Status**: Ready for Implementation (Plan / PRD)  
**Tanggal**: 06 November 2026  

---

## 1. Background & Deskripsi Kebutuhan (User Story)

### 1.1 Konteks Masalah
Saat ini, form inline editor catatan logbook (`.inline-note-editor`) menggunakan input textarea standar dengan label statis di bagian luar:
```html
<label class="inline-note-label">Catatan & Arahan Pembimbing *</label>
<textarea placeholder="Tuliskan catatan apresiasi..."></textarea>
```
Hal ini terasa kaku, generic, dan tidak selaras dengan bahasa visual interaktif modern yang sudah ada di halaman **Buat Tugas** (`pembimbing/tugas/create.blade.php`), di mana input field menggunakan **Material Floating Label Input Card ala Google Classroom** (`.tugas-field-filled`).

### 1.2 User Requirement
> *"untuk membuat tampilan Catatan & Arahan Pembimbing \* ini di dalam card untuk mengganti text tulis catatan dan ketika cursor klik ke card catatan ada animasinya sama seperti dibuat tugas itu"*

### 1.3 Expected User Experience (UX)
1. **Posisi Label di Dalam Card**:
   - Teks `"Catatan & Arahan Pembimbing *"` berada **di dalam card field**, bukan sebagai label statis di luar.
   - Placeholder teks default dihapus karena fungsinya telah digantikan secara elegan oleh label tersebut.
2. **Animasi Floating & Focus (Selaras Halaman Buat Tugas)**:
   - **Kondisi Kosong & Tidak Aktif (Default)**:
     - Label berada di posisi tengah/placeholder dengan warna abu-abu netral (`#5F6368`), ukuran font `14px`, dan garis border bawah abu-abu tipis.
   - **Kondisi Kursor Klik / Fokus (Focused)**:
     - Label mengapung ke atas (*floating label*) secara mulus dengan animasi transisi `transform: translateY(-8px) scale(0.80)`.
     - Warna label berubah menjadi biru khas MagKom (`#01347C` / `#1A73E8`) dan font menjadi lebih tegas (`font-weight: 600`).
     - Garis indikator aktif biru (`::after`) mekar melebar dari tengah (*center-origin scaleX(0) to scaleX(1)*) di tepi bawah card.
   - **Kondisi Sudah Terisi Teks (Has Value / Mode Ubah Catatan)**:
     - Label tetap bertahan mengapung di posisi atas (*persistent floating state*), sehingga tidak bertumpuk dengan teks ketikan pembimbing.

---

## 2. Bedah Komponen Acuan Eksisting (`tugas-create.css`)

Pada halaman `pembimbing/tugas/create.blade.php` dan stylesheet `resources/css/pages/pembimbing/tugas-create.css`, pola ini telah diimplementasikan dengan sangat stabil:

```css
/* Acuan Teknis dari tugas-create.css */
.tugas-field-filled {
    position: relative;
    background: #e8edf5;
    border-bottom: 1px solid #5f6368;
    border-radius: 4px 4px 0 0;
    min-height: 56px;
    padding: 0 16px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    overflow: hidden;
    transition: background-color 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.tugas-field-filled::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: #1a73e8;
    transform: scaleX(0);
    transform-origin: center;
    transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
}

.tugas-field-filled:focus-within::after,
.tugas-field-filled.is-focused::after {
    transform: scaleX(1);
}

.tugas-field-filled-label {
    position: absolute;
    left: 16px;
    top: 18px;
    font-size: 16px;
    color: #5f6368;
    pointer-events: none;
    transform-origin: left top;
    transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1), color 0.22s cubic-bezier(0.4, 0, 0.2, 1);
}

.tugas-field-filled:focus-within .tugas-field-filled-label,
.tugas-field-filled.has-value .tugas-field-filled-label {
    transform: translateY(-10px) scale(0.75);
    color: #1a73e8;
    font-weight: 600;
}
```

Tugas junior developer / AI adalah **mengadaptasi pola ini untuk input multiline (Textarea)** pada inline note logbook dengan class namespace scoped: `.logbook-note-field-filled`.

---

## 3. Aturan & Standar Wajib Repositori (Guardrails)

> [!IMPORTANT]
> **ATURAN WAJIB MAGKOM (JANGAN DILANGGAR):**
> 1. **LARANGAN KERAS BLADE**: Dilarang menulis tag `<style>` atau `<script>` di dalam file Blade `resources/views/**/*.blade.php`.
> 2. **Satu Sumber Kebenaran Styling**: Semua styling CSS baru wajib berada di `resources/css/pages/pembimbing/logbook-index.css`.
> 3. **Interaksi Modular**: Semua interaksi JavaScript (class toggle, listener) wajib berada di `resources/js/pages/pembimbing/logbook-index.js`.
> 4. **Build & Quality Gates**: Wajib menjalankan `npm run build` dan `./vendor/bin/pint --test` setelah perubahan.

---

## 4. Rincian Langkah Kerja (Actionable Step-by-Step)

### Step 1: Penyesuaian Markup Blade (`resources/views/pembimbing/logbook/index.blade.php`)

Ganti struktur form di dalam `.inline-note-editor` dari form standar menjadi format filled card dengan floating label:

**Lokasi Berkas**: [`resources/views/pembimbing/logbook/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/logbook/index.blade.php)  
**Target Edit**: Di dalam loop `@foreach($masukLogbooks as $log)` pada container `#inline-note-form-{{ $log->id }}`.

```blade
{{-- Form Inline Pemberian / Pengubahan Catatan Pembimbing --}}
<div id="inline-note-form-{{ $log->id }}" class="inline-note-editor" hidden>
    <form method="POST" action="{{ route('pembimbing.logbook.review', $log->id) }}" class="note-edit-form">
        @csrf
        
        @php
            $hasInitialNote = !empty($log->catatan_pembimbing);
        @endphp

        {{-- Material Floating Card Field ala Google Classroom --}}
        <div class="logbook-note-field-filled {{ $hasInitialNote ? 'has-value' : '' }}">
            <label for="textarea-note-{{ $log->id }}" class="logbook-note-floating-label">
                <span>Catatan &amp; Arahan Pembimbing</span>
                <span class="required-star" aria-hidden="true">*</span>
            </label>
            <textarea name="catatan_pembimbing" 
                      id="textarea-note-{{ $log->id }}" 
                      rows="3" 
                      required 
                      class="logbook-note-clean-textarea">{{ $log->catatan_pembimbing ?? '' }}</textarea>
        </div>

        <div class="inline-note-actions">
            <button type="button" 
                    class="btn-note-cancel" 
                    data-action="cancel-inline-note" 
                    data-log-id="{{ $log->id }}">
                Batal
            </button>
            <button type="submit" class="btn-note-save">
                Simpan Catatan
            </button>
        </div>
    </form>
</div>
```

---

### Step 2: Implementasi CSS Styling (`resources/css/pages/pembimbing/logbook-index.css`)

**Lokasi Berkas**: [`resources/css/pages/pembimbing/logbook-index.css`](file:///g:/MAGKOM/resources/css/pages/pembimbing/logbook-index.css)  

Tambahkan aturan CSS berikut pada bagian bawah stylesheet:

```css
/* ========================================================
   MATERIAL FLOATING CARD FIELD FOR LOGBOOK NOTES
   Mengadopsi Desain Google Classroom & Buat Tugas
   ======================================================== */

.logbook-note-field-filled {
    position: relative;
    background: #E8EDF5;
    border-bottom: 1px solid #5F6368;
    border-radius: 6px 6px 0 0;
    padding: 22px 16px 8px 16px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: background-color 0.25s cubic-bezier(0.4, 0, 0.2, 1), 
                border-color 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.logbook-note-field-filled:hover {
    background: #DDE3ED;
    border-bottom-color: #202124;
}

.logbook-note-field-filled:focus-within,
.logbook-note-field-filled.is-focused {
    background: #E8EDF5;
    border-bottom-color: transparent;
}

/* Garis Animasi Biru di Tepi Bawah (Mekar dari Tengah) */
.logbook-note-field-filled::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: #01347C;
    transform: scaleX(0);
    transform-origin: center;
    transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
    z-index: 2;
}

.logbook-note-field-filled:focus-within::after,
.logbook-note-field-filled.is-focused::after {
    transform: scaleX(1);
}

/* Floating Label Transition */
.logbook-note-floating-label {
    position: absolute;
    left: 16px;
    top: 18px;
    font-size: 0.875rem;
    font-weight: 400;
    color: #5F6368;
    line-height: 1;
    pointer-events: none;
    transform-origin: left top;
    transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1), 
                color 0.22s cubic-bezier(0.4, 0, 0.2, 1),
                font-weight 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    gap: 2px;
}

.logbook-note-floating-label .required-star {
    color: #EF4444;
    font-weight: 700;
}

/* Floating State: Saat Fokus ATAU Saat Memiliki Nilai/Isi */
.logbook-note-field-filled:focus-within .logbook-note-floating-label,
.logbook-note-field-filled.is-focused .logbook-note-floating-label,
.logbook-note-field-filled.has-value .logbook-note-floating-label {
    transform: translateY(-10px) scale(0.78);
}

/* Label Color saat Aktif Fokus */
.logbook-note-field-filled:focus-within .logbook-note-floating-label,
.logbook-note-field-filled.is-focused .logbook-note-floating-label {
    color: #01347C;
    font-weight: 600;
}

/* Label Color saat Terisi tapi sedang tidak fokus */
.logbook-note-field-filled.has-value:not(:focus-within):not(.is-focused) .logbook-note-floating-label {
    color: #5F6368;
    font-weight: 500;
}

/* Clean Textarea Tanpa Border Bawaan */
.logbook-note-clean-textarea {
    width: 100%;
    min-height: 72px;
    padding: 0;
    margin: 0;
    background: transparent;
    border: none;
    outline: none;
    box-shadow: none;
    font-family: inherit;
    font-size: 0.875rem;
    color: #1E293B;
    line-height: 1.5;
    resize: vertical;
    box-sizing: border-box;
    z-index: 1;
}

.logbook-note-clean-textarea:focus {
    outline: none;
    box-shadow: none;
    border: none;
}

/* Dark Theme Compatibility */
[data-theme="dark"] .logbook-note-field-filled {
    background: #1E293B;
    border-bottom-color: #475569;
}

[data-theme="dark"] .logbook-note-field-filled:hover {
    background: #27354A;
}

[data-theme="dark"] .logbook-note-field-filled:focus-within,
[data-theme="dark"] .logbook-note-field-filled.is-focused {
    background: #1E293B;
}

[data-theme="dark"] .logbook-note-field-filled::after {
    background: #38BDF8;
}

[data-theme="dark"] .logbook-note-floating-label {
    color: #94A3B8;
}

[data-theme="dark"] .logbook-note-field-filled:focus-within .logbook-note-floating-label,
[data-theme="dark"] .logbook-note-field-filled.is-focused .logbook-note-floating-label {
    color: #38BDF8;
}

[data-theme="dark"] .logbook-note-clean-textarea {
    color: #F8FAFC;
}
```

---

### Step 3: Interaksi JavaScript (`resources/js/pages/pembimbing/logbook-index.js`)

**Lokasi Berkas**: [`resources/js/pages/pembimbing/logbook-index.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/logbook-index.js)  

Tambahkan logic handler untuk otomatis menambahkan/menghapus class `.has-value` ketika textarea diketik atau diisi:

1. **Sinkronisasi State `.has-value`**:
   - Pasang event delegation pada input:
     ```javascript
     document.addEventListener('input', (e) => {
         if (e.target.matches('.logbook-note-clean-textarea')) {
             const fieldContainer = e.target.closest('.logbook-note-field-filled');
             if (fieldContainer) {
                 if (e.target.value.trim().length > 0) {
                     fieldContainer.classList.add('has-value');
                 } else {
                     fieldContainer.classList.remove('has-value');
                 }
             }
         }
     });
     ```
2. **Saat Form Dibuka via Tombol [Beri Catatan] / [Ubah Catatan]**:
   - Pastikan jika textarea sudah memiliki isi, class `has-value` terpasang.
   - Textarea otomatis menerima fokus (`textarea.focus()`).

---

### Step 4: Quality Gate & Verifikasi

1. **Vite Compilation**:
   Jalankan:
   ```powershell
   npm run build
   ```
   Pastikan tidak ada error sintaks CSS atau JS.
2. **Laravel Pint Formatting**:
   Jalankan:
   ```powershell
   ./vendor/bin/pint --test
   ```
3. **Feature Test**:
   Jalankan:
   ```powershell
   php artisan test tests/Feature/PembimbingFocusedMenteeLogbookViewTest.php
   php artisan test --filter=Logbook
   ```

---

## 5. Checklist Verifikasi Mandiri (Definition of Done)

- [ ] Label *"Catatan & Arahan Pembimbing \*"* berada di dalam kotak card field, bukan label statis di luar.
- [ ] Textarea tidak memiliki placeholder default (sudah bersih).
- [ ] Ketika kursor mengklik textarea, label mengapung ke atas dan warnanya berubah menjadi biru.
- [ ] Garis biru indikator di tepi bawah card mekar memanjang dari tengah dengan animasi mulus.
- [ ] Ketika catatan sudah terisi (atau pada logbook yang sudah ada catatan sebelumnya), label otomatis tetap mengapung di atas dan tidak menimpa teks.
- [ ] Tombol **[Batal]** menutup form tanpa glitch visual.
- [ ] Tombol **[Simpan Catatan]** berhasil memproses simpan catatan ke database.
- [ ] Tidak ada kode CSS/JS yang disisipkan di dalam tag Blade.
- [ ] Semua pengujian PHPUnit hijau (100% Passed).

