# SENIOR DEVELOPER TASK & TECHNICAL PRD
## Refaktor Input "Catatan Umpan Balik" Menjadi Floating Card Box dengan Animasi Material Focus ala Buat Tugas

**Target Audience:** Junior Developers & AI Agents (Gemini 3.8 Flash / Claude Haiku / Cursor)  
**Status:** DRAFT / PROPOSED (Menunggu Konfirmasi User)  
**Tanggal:** 06 November 2026  
**Modul:** Portal Pembimbing — Review & Evaluasi Penugasan (`pembimbing.tugas.show`)  
**Referensi Desain:** Google Classroom Material Filled Box (`.tugas-field-filled` pada Halaman Buat Tugas)  

---

## 1. Executive Summary & Objective

### 1.1 Problem Statement
Pada halaman **Review & Evaluasi Penugasan** (`resources/views/pembimbing/tugas/show.blade.php`), komponen input catatan pembimbing saat ini menggunakan form input konvensional:
1. Label *"Catatan Umpan Balik untuk Mentee"* diletakkan di **luar** box (di atas textarea).
2. Di dalam textarea terdapat teks placeholder yang panjang: *"Tuliskan catatan apresiasi atau instruksi perbaikan untuk peserta..."*.
3. Tampilan terasa kaku, tidak memiliki feedback interaktif saat pengguna mengklik/mengetik, dan tidak selaras (*inconsistent*) dengan antarmuka modern yang sudah diterapkan pada form "Buat Tugas" (yang menggunakan Material Filled Box dengan animasi garis aksen Sleman Blue dan floating label).

### 1.2 Objective & Acceptance Criteria
1. **Label Di Dalam Card**: Pindahkan teks label *"Catatan Umpan Balik"* ke **dalam card/box** input menulis (floating label pattern).
2. **Hapus Teks Panjang**: Hapus placeholder panjang *"Tuliskan catatan apresiasi atau instruksi perbaikan untuk peserta..."*, ganti menjadi label ringkas *"Catatan Umpan Balik"* dengan badge indikator `*(Wajib jika dikembalikan)` di pojok kanan label.
3. **Animasi Halus ala Buat Tugas**:
   - **Default State**: Label berada di dalam box textarea dengan ukuran teks normal.
   - **Focus State (`:focus-within` / `.is-focused`)**: Label bertransisi halus mengecil ke atas (`translateY(-8px) scale(0.82)`), warna label berubah menjadi biru Sleman (`#0641CC` / `#01347C`), dan garis aksen bawah melebar dari tengah dengan animasi `transform: scaleX(1)`.
   - **Value State (`.has-value`)**: Jika textarea memiliki teks (termasuk saat old input reload), label tetap mengambang di atas agar tidak menumpuk dengan teks ketikan pembimbing.
4. **Zero Regresi**:
   - Nilai textarea tetap terkirim dengan sempurna ke hidden form `formActionKembalikan` melalui JavaScript.
   - Validasi minimal 5 karakter saat klik *Kembalikan Tugas* tetap berjalan 100%.
   - Tidak ada inline CSS `<style>` atau `<script>` di Blade (patuh aturan arsitektur MAGKOM).
   - Seluruh styling ditempatkan di `resources/css/pages/pembimbing/tugas-review.css`.
   - Logika interaksi fokus ditempatkan di `resources/js/pages/pembimbing/tugas-review.js`.

---

## 2. File Mapping & Impact Analysis

| Path File | Tanggung Jawab | Jenis Perubahan |
|---|---|---|
| `resources/views/pembimbing/tugas/show.blade.php` | Markup Blade halaman Review Tugas | **Update Markup**: Ganti pembungkus textarea lama menjadi `.review-feedback-field` berstruktur floating label |
| `resources/css/pages/pembimbing/tugas-review.css` | Styling CSS halaman Review Tugas | **Update Stylesheet**: Tambahkan selector `.review-feedback-field`, animasi garis `::after`, floating label, reset textarea clean, dan dukungan dark mode |
| `resources/js/pages/pembimbing/tugas-review.js` | Skrip interaksi evaluasi & sidebar | **Update Script**: Tambahkan listener focus, blur, dan input untuk toggle class `.is-focused` dan `.has-value` |
| `tests/Feature/PembimbingFocusedMenteeTugasViewTest.php` | Automated Feature Test | **Verification Test**: Pastikan view dirender dengan status 200 OK dan elemen `#feedbackInput` ada di dalam DOM |

---

## 3. Step-by-Step Implementation Guide

### Langkah 1: Perbarui Markup Blade (`resources/views/pembimbing/tugas/show.blade.php`)

**Lokasi Baris:** Sekitar baris 404 - 415 pada file `resources/views/pembimbing/tugas/show.blade.php`.

#### 🔴 Kode Lama (Sebelumnya):
```blade
{{-- Textarea Arahan / Umpan Balik untuk Mentee (Template sudah dihapus) --}}
<div style="margin-bottom: 14px;">
    <label for="feedbackInput" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem; font-weight: 700; color: #475569; margin-bottom: 6px;">
        <span>Catatan Umpan Balik untuk Mentee</span>
        <span id="feedbackReqBadge" style="color: #DC2626; font-size: 0.725rem;">*(Wajib jika dikembalikan)</span>
    </label>
    <textarea id="feedbackInput" 
              class="review-feedback-textarea" 
              placeholder="Tuliskan catatan apresiasi atau instruksi perbaikan untuk peserta...">{{ old('feedback', $latestLog?->feedback) }}</textarea>
</div>
```

#### 🟢 Kode Baru (Pengganti):
```blade
{{-- Field Catatan Umpan Balik Bergaya Modern Floating Card Box (Material Classroom Style) --}}
@php
    $initialFeedback = old('feedback', $latestLog?->feedback);
    $hasFeedbackVal = !empty(trim((string)$initialFeedback));
@endphp
<div class="review-feedback-field {{ $hasFeedbackVal ? 'has-value' : '' }} {{ $errors->has('feedback') ? 'has-error' : '' }}" id="feedbackFieldBox" style="margin-bottom: 16px;">
    <label for="feedbackInput" class="review-feedback-label">
        <span class="label-title">Catatan Umpan Balik</span>
        <span id="feedbackReqBadge" class="label-badge-req">*(Wajib jika dikembalikan)</span>
    </label>
    <textarea id="feedbackInput" 
              name="feedback_display" 
              class="review-feedback-textarea-clean" 
              rows="4" 
              spellcheck="false">{{ $initialFeedback }}</textarea>
</div>
```

> **Catatan Penting untuk Junior Dev/AI:**
> - Atribut `placeholder="..."` dihapus sepenuhnya agar tidak menutupi label saat state default.
> - ID `#feedbackInput` dan `#feedbackReqBadge` **wajib dipertahankan** persis sama karena digunakan oleh validasi JavaScript di `tugas-review.js`.

---

### Langkah 2: Perbarui Styling CSS (`resources/css/pages/pembimbing/tugas-review.css`)

**Lokasi Baris:** Sekitar baris 1230 (gantikan blok `.review-feedback-textarea` lama) dan tambahkan rule dark mode di blok theme.

#### 🟢 Kode CSS yang Ditambahkan/Disesuaikan:
```css
/* ==========================================================================
   Modern Material Floating Field: Catatan Umpan Balik Pembimbing
   (Selaras dengan .tugas-field-filled di Google Classroom Buat Tugas)
   ========================================================================== */
.review-feedback-field {
    position: relative;
    background: #F1F5F9;
    border-bottom: 1px solid #94A3B8;
    border-radius: 8px 8px 0 0;
    padding: 24px 14px 10px 14px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    overflow: hidden;
    transition: background-color 0.25s cubic-bezier(0.4, 0, 0.2, 1),
                border-color 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.review-feedback-field:hover {
    background: #E8EDF5;
    border-bottom-color: #64748B;
}

.review-feedback-field:focus-within,
.review-feedback-field.is-focused {
    background: #E8EDF5;
    border-bottom-color: transparent;
}

/* Animasi Garis Aksen Bawah (Material Active Indicator) */
.review-feedback-field::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: var(--review-primary, #0641CC);
    transform: scaleX(0);
    transform-origin: center;
    transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
    z-index: 10;
}

.review-feedback-field:focus-within::after,
.review-feedback-field.is-focused::after {
    transform: scaleX(1);
}

.review-feedback-field.has-error {
    border-bottom-color: #DC2626;
    background: #FEF2F2;
}

.review-feedback-field.has-error::after {
    background: #DC2626;
    transform: scaleX(1);
}

/* Floating Label di Dalam Box */
.review-feedback-label {
    position: absolute;
    top: 14px;
    left: 14px;
    right: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    pointer-events: none;
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748B;
    transform-origin: top left;
    transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1),
                color 0.22s cubic-bezier(0.4, 0, 0.2, 1),
                font-weight 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
    z-index: 2;
}

.review-feedback-label .label-title {
    font-size: inherit;
    font-weight: inherit;
    color: inherit;
}

.review-feedback-label .label-badge-req {
    font-size: 0.725rem;
    font-weight: 600;
    color: #DC2626;
    transition: opacity 0.2s ease;
}

/* Floating Animation saat Fokus ATAU Memiliki Nilai */
.review-feedback-field:focus-within .review-feedback-label,
.review-feedback-field.is-focused .review-feedback-label,
.review-feedback-field.has-value .review-feedback-label {
    transform: translateY(-8px) scale(0.82);
}

/* Pewarnaan Label saat Fokus Aktif */
.review-feedback-field:focus-within .review-feedback-label .label-title,
.review-feedback-field.is-focused .review-feedback-label .label-title {
    color: var(--review-primary, #0641CC);
    font-weight: 700;
}

.review-feedback-field.has-value:not(:focus-within):not(.is-focused) .review-feedback-label .label-title {
    color: #475569;
    font-weight: 600;
}

.review-feedback-field.has-error .review-feedback-label .label-title {
    color: #DC2626 !important;
}

/* Textarea Bersih Tanpa Border Tambahan di Dalam Card Box */
.review-feedback-textarea-clean {
    width: 100%;
    min-height: 84px;
    box-sizing: border-box;
    border: 0;
    background: transparent;
    outline: none;
    font-size: 0.875rem;
    line-height: 1.5;
    font-family: inherit;
    color: var(--review-text-main, #1D2530);
    resize: vertical;
    padding: 0;
    margin: 0;
    z-index: 1;
}

/* Dukungan Dark Mode */
[data-theme="dark"] .review-feedback-field {
    background: #1E293B;
    border-bottom-color: #475569;
}

[data-theme="dark"] .review-feedback-field:hover {
    background: #25334D;
    border-bottom-color: #64748B;
}

[data-theme="dark"] .review-feedback-field:focus-within,
[data-theme="dark"] .review-feedback-field.is-focused {
    background: #1E293B;
}

[data-theme="dark"] .review-feedback-label {
    color: #94A3B8;
}

[data-theme="dark"] .review-feedback-textarea-clean {
    color: #F8FAFC;
}
```

---

### Langkah 3: Perbarui JavaScript Interaktif (`resources/js/pages/pembimbing/tugas-review.js`)

**Lokasi Baris:** Di dalam handler inisialisasi pada file `resources/js/pages/pembimbing/tugas-review.js`.

Tambahkan fungsi pengendali floating label state:
```javascript
    // -------------------------------------------------------------------------
    // Floating Label Box Interactivity for Catatan Umpan Balik (Classroom Style)
    // -------------------------------------------------------------------------
    const feedbackFieldBox = document.getElementById('feedbackFieldBox');

    function updateFeedbackFloatingState() {
        if (!feedbackInput || !feedbackFieldBox) return;
        const hasVal = feedbackInput.value.trim().length > 0;
        feedbackFieldBox.classList.toggle('has-value', hasVal);
    }

    if (feedbackInput && feedbackFieldBox) {
        feedbackInput.addEventListener('focus', () => {
            feedbackFieldBox.classList.add('is-focused');
            feedbackFieldBox.classList.remove('has-error');
        });

        feedbackInput.addEventListener('blur', () => {
            feedbackFieldBox.classList.remove('is-focused');
            updateFeedbackFloatingState();
        });

        feedbackInput.addEventListener('input', () => {
            updateFeedbackFloatingState();
        });

        // Inisialisasi state awal (misal saat form reload atau ada data lama)
        updateFeedbackFloatingState();
    }
```

Dan pada saat validasi karakter kurang dari 5 (ketika mengembalikan tugas), tambahkan efek visual error:
```javascript
    if (feedbackVal.length < 5) {
        if (feedbackFieldBox) {
            feedbackFieldBox.classList.add('has-error');
        }
        alert('Catatan umpan balik wajib diisi minimal 5 karakter ketika mengembalikan tugas kepada peserta untuk diperbaiki.');
        feedbackInput.focus();
        feedbackInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
```

---

## 4. Verification & Quality Assurance (QA) Checklist

Junior Developer / AI Pelaksana **wajib** melakukan checklist verifikasi berikut setelah kode diterapkan:

| No | Poin Pemeriksaan | Kriteria Sukses |
|---|---|---|
| 1 | **Tampilan Awal (Kosong)** | Box input memiliki background lembut `#F1F5F9`, border bawah abu-abu tipis, label *"Catatan Umpan Balik"* dan badge berada di dalam card. Tidak ada placeholder teks panjang. |
| 2 | **Animasi Klik / Fokus** | Saat textarea diklik/difokuskan, label bertransisi halus melayang ke atas (`scale(0.82) translateY(-8px)`), warna label berubah menjadi biru Sleman (`#0641CC`), dan garis aksen bawah melebar secara horizontal (`scaleX(1)`). |
| 3 | **Ketik Teks & Blur** | Saat teks diketik lalu user mengklik di luar textarea (blur), label tetap berada di posisi mengambang di atas (`.has-value`), sehingga tulisan tidak tertimpa. |
| 4 | **Validasi Kembalikan Tugas** | Saat tombol *"Kembalikan Tugas"* diklik dengan isi catatan < 5 huruf, alert browser muncul, field mendapatkan fokus dan animasi error warna merah (`.has-error`). |
| 5 | **Submit Berhasil** | Saat catatan diisi valid (≥ 5 huruf) dan dikonfirmasi, data terkirim ke backend dan status tugas berubah menjadi `perlu_revisi`. |
| 6 | **Asset Build** | Jalankan `npm run build` tanpa warning/error sintaks CSS maupun JS. |
| 7 | **Automated Tests & Quality Gates** | Jalankan `php artisan test --filter=PembimbingFocusedMenteeTugasViewTest` (PASS), `./vendor/bin/phpstan analyse --memory-limit=1G` (0 errors), dan `./vendor/bin/pint --test` (Passed). |

---

## 5. Architectural Guardrails (Wajib Dipatuhi)

1. **JANGAN Menulis CSS atau JS di Blade**: Dilarang menambahkan tag `<style>` atau `<script>` pada `resources/views/pembimbing/tugas/show.blade.php`.
2. **Pertahankan Kontrak Form & Routing**: Tidak boleh mengubah ID `#feedbackInput`, ID `#formActionKembalikan`, atau rute `route('pembimbing.progres.review', ...)`.
3. **Vanilla Stack Saja**: Tidak boleh memasukkan dependency external baru (seperti Tailwind CSS, AlpineJS, dsb.).
4. **Verifikasi Nyata**: Dilarang menyatakan tugas selesai sebelum menjalankan `npm run build` dan PHPUnit test.

