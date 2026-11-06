# [TASK-SENIOR-DEV] Migrasi Form Catatan Logbook Pembimbing: Dari Modal Pop-up Menjadi Inline Expansion

**Dokumen**: Technical Task & Implementation Plan (PRD Specification)  
**Target Pelaksana**: Junior Developer / AI Agent (Gemini 3.8 Flash / Claude Haiku / Cursor)  
**Reviewer**: Senior Lead Full-Stack Architect  
**Repositori**: `MAGKOM` (Portal Manajemen Magang Diskominfo Sleman)  
**Status**: Ready for Implementation  
**Tanggal**: 06 November 2026  

---

## 1. Executive Summary & Problem Statement

### 1.1 Latar Belakang & Masalah
Saat ini pada halaman rincian logbook peserta (`/pembimbing/logbook?masuk={penempatan_id}`), ketika pembimbing ingin memberikan catatan apresiasi/evaluasi atau mengubah catatan melalui tombol **[Beri Catatan]** atau **[Ubah Catatan]**, sistem memunculkan sebuah **Modal Pop-up** (`#modal-catatan-logbook`).

Hal ini menimbulkan friksi pengalaman pengguna (*UX friction*):
1. **Kehilangan Konteks Logbook**: Pop-up menutupi daftar logbook peserta di latar belakang dan mengharuskan modal mengulang teks uraian aktivitas peserta.
2. **Kikuk & Berat**: Pembimbing lapangan memeriksa puluhan entri logbook harian, membuka-tutup modal untuk setiap entri membuang waktu dan terasa kaku.
3. **Double Action Paradox**: Tombol ada di baris logbook, namun form berada di tengah layar secara terpisah.

### 1.2 Target Solusi (User Requirement)
> *"membuat tampilan dari ubah atau beri catatan ga berbentuk pop up tetapi awalnya berbentuk kosong dan ketika di klik beri catatan muncul di bawah logbook yg di kasih oleh peserta trs kita bisa nulis di situ"*

Kita akan merombak interaksi ini menjadi **Inline Expansion Form (Accordion Inline)**:
- **Kondisi Awal (Default)**:
  - Jika logbook **belum ada catatan**: Area bawah uraian kosong (hanya ada uraian kegiatan peserta dan tombol `[Beri Catatan]` di kanan atas).
  - Jika logbook **sudah ada catatan**: Menampilkan kotak catatan bergaris kiri biru khas MagKom (`.supervisor-notes-box`) dan tombol `[Ubah Catatan]`.
- **Kondisi Aktif (Saat Tombol Diklik)**:
  - **TIDAK ADA POP-UP**.
  - Form input catatan langsung muncul secara mulus (*smooth transition*) tepat di bawah teks uraian aktivitas logbook entri tersebut.
  - Textarea otomatis aktif dan fokus (*autofocus*).
  - Terdapat dua tombol aksi di bawah textarea: **[Simpan Catatan]** (biru utama MagKom) dan **[Batal]** (sekunder abu-abu).
  - Tombol **[Batal]** menyembunyikan kembali form inline tanpa merusak data lama.
  - Tombol **[Simpan Catatan]** memicu submit HTTP POST ke endpoint backend `/pembimbing/logbook/{logbook}/review`.

---

## 2. Referensi Desain & Analisis Visual

### 2.1 Kondisi Eksisting Saat Ini
1. **Daftar Logbook dengan Box Catatan & Tombol**:
   - Logbook memiliki badge tanggal, tautan laporan (PDF / Tautan eksternal), teks kegiatan, dan tombol `[Ubah Catatan]` atau `[Beri Catatan]`.
   - Box catatan pembimbing berlatar `#F8FAFC`, border-left `3px solid #01347C`, font `0.8125rem`.
2. **Modal Pop-up yang Harus Dihapus**:
   - Modal `.modal-overlay` `#modal-catatan-logbook` yang membungkus dialog form catatan. Modal ini **dihapus sepenuhnya** dari Blade dan DOM.

### 2.2 Desain Komponen Baru: Inline Expansion Form
```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [📅 01 Nov 2026] [🔗 Tautan ↗]                                        [✏️ Beri Catatan] │
│ Melakukan pengujian sistem integrasi, evaluasi data, serta menyiapkan rekap capaian.   │
│                                                                                        │
│ ┌── Inline Catatan Form (Muncul saat tombol diklik) ─────────────────────────────────┐ │
│ │ ✍️ Catatan & Arahan Pembimbing                                                     │ │
│ │ ┌────────────────────────────────────────────────────────────────────────────────┐ │ │
│ │ │ Tuliskan catatan apresiasi, evaluasi aktivitas, atau arahan kerja...           │ │ │
│ │ │                                                                                │ │ │
│ │ └────────────────────────────────────────────────────────────────────────────────┘ │ │
│ │                                                  [ Batal ]  [ 💾 Simpan Catatan ] │ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Spesifikasi Arsitektur & Aturan Strict Repositori

> [!CAUTION]
> **ATURAN WAJIB (MAGKOM AI AGENT RULES) - JANGAN DILANGGAR:**
> 1. **DILARANG** menulis CSS di `<style>` atau JavaScript di `<script>` dalam file Blade `resources/views/**/*.blade.php`.
> 2. Semua CSS baru wajib berada di `resources/css/pages/pembimbing/logbook-index.css`.
> 3. Semua JavaScript interaksi wajib berada di `resources/js/pages/pembimbing/logbook-index.js`.
> 4. Asset wajib dikompilasi dengan `npm run build` dan bebas error.
> 5. Pertahankan kontrak backend, route name `pembimbing.logbook.review`, CSRF token `@csrf`, dan autentikasi/otorisasi yang ada.

---

## 4. Rincian Teknis Implementasi (Step-by-Step Task)

### Task 1: Refaktor File Blade (`resources/views/pembimbing/logbook/index.blade.php`)

1. **Hapus Modal Pop-up Eksisting**:
   - Hapus seluruh blok kontainer `{{-- 5. MODAL MEMBERIKAN CATATAN LOGBOOK --}}` (div `#modal-catatan-logbook` beserta seluruh isi dialognya) dari baris 300 ke bawah.
2. **Sematkan Inline Form di Dalam Baris Logbook**:
   - Di dalam loop `@foreach($masukLogbooks as $log)`:
   - Pada kolom `.task-row-title-wrap`:
     - Tampilkan catatan eksisting (jika ada):
       ```blade
       @if($log->catatan_pembimbing)
           <div id="notes-view-{{ $log->id }}" class="supervisor-notes-box">
               <strong class="notes-label">Catatan Pembimbing:</strong> 
               <span class="notes-text">{{ $log->catatan_pembimbing }}</span>
           </div>
       @endif
       ```
     - Tambahkan kontainer **Inline Form Catatan** (secara default `hidden` atau `style="display: none;"`):
       ```blade
       <div id="inline-note-form-{{ $log->id }}" class="inline-note-editor" hidden>
           <form method="POST" action="{{ route('pembimbing.logbook.review', $log->id) }}" class="note-edit-form">
               @csrf
               <label for="textarea-note-{{ $log->id }}" class="inline-note-label">
                   Catatan &amp; Arahan Pembimbing <span class="required-star">*</span>
               </label>
               <textarea name="catatan_pembimbing" 
                         id="textarea-note-{{ $log->id }}" 
                         rows="3" 
                         required 
                         placeholder="Tuliskan catatan apresiasi, evaluasi aktivitas, atau arahan kerja untuk peserta..." 
                         class="inline-note-textarea">{{ $log->catatan_pembimbing ?? '' }}</textarea>
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
3. **Perbarui Atribut Tombol Aksi Pembimbing**:
   - Ganti class `btn-open-catatan-modal` menjadi `btn-toggle-inline-note`.
   - Simpan data `data-log-id="{{ $log->id }}"`.
   - Label tombol tetap dinamis: `{{ $log->catatan_pembimbing ? 'Ubah Catatan' : 'Beri Catatan' }}`.

---

### Task 2: Penataan Styling CSS (`resources/css/pages/pembimbing/logbook-index.css`)

Tambahkan token dan styling khusus untuk inline note form:

```css
/* ========================================================
   INLINE NOTE EDITOR ACCORDION FOR LOGBOOK ENTRIES
   ======================================================== */

.inline-note-editor {
    margin-top: 12px;
    padding: 14px 16px;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    animation: inlineNoteSlideDown 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes inlineNoteSlideDown {
    from {
        opacity: 0;
        transform: translateY(-6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.inline-note-label {
    display: block;
    font-size: 0.8125rem;
    font-weight: 700;
    color: #0F172A;
    margin-bottom: 6px;
}

.inline-note-label .required-star {
    color: #EF4444;
}

.inline-note-textarea {
    width: 100%;
    min-height: 80px;
    padding: 10px 12px;
    background: #FFFFFF;
    border: 1.5px solid #CBD5E1;
    border-radius: 6px;
    font-size: 0.875rem;
    color: #1E293B;
    font-family: inherit;
    line-height: 1.5;
    resize: vertical;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
    box-sizing: border-box;
}

.inline-note-textarea:focus {
    outline: none;
    border-color: #01347C;
    box-shadow: 0 0 0 3px rgba(1, 52, 124, 0.15);
}

.inline-note-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
}

.btn-note-cancel {
    padding: 6px 14px;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #64748B;
    background: transparent;
    border: 1px solid #CBD5E1;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.btn-note-cancel:hover {
    background: #F1F5F9;
    color: #334155;
}

.btn-note-save {
    padding: 6px 16px;
    font-size: 0.8125rem;
    font-weight: 700;
    color: #FFFFFF;
    background: #01347C;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(1, 52, 124, 0.25);
    transition: all 0.15s ease;
}

.btn-note-save:hover {
    background: #002244;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(1, 52, 124, 0.35);
}

/* Penyesuaian Supervisor Notes Box */
.supervisor-notes-box {
    margin-top: 8px;
    padding: 8px 12px;
    background: #F8FAFC;
    border-left: 3px solid #01347C;
    border-radius: 4px;
    font-size: 0.8125rem;
    color: #334155;
    line-height: 1.5;
}

.supervisor-notes-box .notes-label {
    color: #01347C;
    font-weight: 700;
}
```

---

### Task 3: Interaksi JavaScript ES Modules (`resources/js/pages/pembimbing/logbook-index.js`)

1. **Hapus Listener Modal Lama**:
   - Hapus selektor dan event listener untuk `#modal-catatan-logbook`, `.btn-open-catatan-modal`, dan handler tombol tutup modal.
2. **Implementasikan Inline Toggle Controller**:
   - Gunakan Event Delegation pada container logbook:
     - Ketika tombol `[data-action="toggle-inline-note"]` (atau `.btn-toggle-inline-note`) diklik:
       - Dapatkan `logId = btn.dataset.logId`.
       - Temukan target editor `#inline-note-form-${logId}`.
       - Jika form sedang tersembunyi (`hidden`):
         - Buka form (`editor.hidden = false`).
         - Sembunyikan preview catatan lama `#notes-view-${logId}` sementara agar tidak dobel.
         - Berikan fokus ke textarea (`textarea.focus()`).
       - Jika form sudah terbuka:
         - Tutup form (`editor.hidden = true`).
         - Munculkan kembali preview catatan lama (jika ada).
     - Ketika tombol `[data-action="cancel-inline-note"]` diklik:
       - Sembunyikan editor `#inline-note-form-${logId}` (`editor.hidden = true`).
       - Munculkan kembali preview catatan lama `#notes-view-${logId}`.
       - Kembalikan nilai textarea ke nilai semula (reset form).

---

### Task 4: Verifikasi & Penyesuaian Feature Test

Periksa test suite yang menguji pemberian catatan logbook:
- File test: `tests/Feature/PembimbingReviewLogbookTest.php` / `tests/Feature/RealtimeTugasLogbookFeedbackTest.php` / `tests/Feature/PembimbingPortalStandardizationTest.php`.
- Pastikan assertion tidak lagi mencari `id="modal-catatan-logbook"`, melainkan mencari form inline `inline-note-editor` atau route submit `route('pembimbing.logbook.review', ...)`.
- Verifikasi bahwa submit form `POST /pembimbing/logbook/{logbook}/review` tetap berjalan 100% mulus dengan validasi `ReviewLogbookRequest`.

---

## 5. Matriks Pengujian & Quality Gates

| Tahapan Uji | Perintah Eksekusi | Kriteria Sukses |
|---|---|---|
| **1. Asset Build** | `npm run build` | Kompilasi chunk Vite selesai tanpa error / warning. |
| **2. Code Formatter** | `./vendor/bin/pint --test` | Lolos PSR-12 tanpa pelanggaran styling kode. |
| **3. Unit & Feature Test** | `php artisan test --filter=PembimbingFocusedMenteeLogbookViewTest` | Semua test view logbook lulus 100%. |
| **4. Regression Suite** | `php artisan test --filter=Logbook` | Tidak ada fitur logbook atau review yang rusak. |

---

## 6. Prompt Instruksi Eksekusi untuk AI Ringan (Gemini 3.8 Flash)

Jika tugas ini didelegasikan ke model AI ringan, gunakan prompt ringkas berikut:

```markdown
Kamu adalah Junior Laravel Full-Stack Developer. Tolong selesaikan PRD Task ini pada repositori MAGKOM:
1. Buka docs/plans/2026-11-06-inline-catatan-logbook-pembimbing-task.md.
2. Pada resources/views/pembimbing/logbook/index.blade.php:
   - Hapus modal #modal-catatan-logbook di bagian bawah.
   - Di dalam loop logbook mentee, tambahkan form inline <div id="inline-note-form-{{ $log->id }}" class="inline-note-editor" hidden> tepat di bawah uraian kegiatan peserta.
   - Ubah tombol catatan menjadi trigger inline toggle (bukan modal).
3. Pada resources/css/pages/pembimbing/logbook-index.css:
   - Tambahkan styling .inline-note-editor, .inline-note-textarea, dan tombol-tombol aksinya.
4. Pada resources/js/pages/pembimbing/logbook-index.js:
   - Hapus kode modal lama.
   - Pasang event handler untuk buka/tutup form inline dan tombol Batal.
5. Jalankan `npm run build` dan `php artisan test --filter=Logbook`.
6. Jangan menulis CSS/JS di dalam Blade!
```

