# DOKUMEN TUGAS SENIOR DEVELOPER & PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Redesain Visual Kartu Popover Ekspor Audit Log Mengadopsi Desain Laporan Peserta (Gentelella Modern UX Standard)

---

**Kepada:** Junior Developer / AI Implementation Agent (Gemini 3.8 Flash, Cursor, Copilot)  
**Dari:** Senior Lead Software Engineer & Frontend Architect MAGKOM  
**Tanggal:** 9 November 2026  
**Status:** Ready for Implementation  
**Prioritas:** P1 — High Usability, Visual Polish & Design System Parity  
**Target Modul:** Modul SuperAdmin — `Audit Log` (`superadmin.audit-log.*`)  

---

## 📌 1. RINGKASAN EKSEKUTIF (Executive Summary)

Berdasarkan arahan desain UI/UX terkini, komponen dropdown/popover ekspor pada halaman **SuperAdmin Audit Log** perlu dirombak total agar mengadopsi tampilan antarmuka modern yang sudah terbukti rapi dan nyaman dilihat pada halaman **Laporan Peserta Pembimbing** (lihat gambar referensi di bawah).

### Target Perubahan:
1. **Penyatuan Tombol Trigger Ekspor**:  
   Mengganti tombol trigger bertipe *split button* yang kaku menjadi tombol banner terpadu yang elegan:
   `[ 📥 Ekspor Log  v ]` dengan ikon unduh, teks tebal, dan ikon panah chevron yang berputar halus saat popover terbuka.
2. **Desain Popover Mengambang (Floating Card)**:  
   Mengadopsi struktur visual kartu popover dari `resources/views/pembimbing/laporan-kinerja/index.blade.php`:
   - Segitiga penunjuk (*pointer accent*) di pojok kanan atas yang presisi mengarah ke tombol trigger.
   - Header popover: Judul *"Ekspor Log Cepat"*, sub-judul *"Data hasil filter aktif"*, serta badge kapsul baris aktif (*e.g. "4,216 Baris"*).
   - Kartu pilihan format interaktif berbasis radio-button bergaris rapi (*Format Cards*) dengan status aktif border biru kontras.
   - Penyesuaian opsi format khusus Audit Log:
     - **CSV Tabular** (`.csv`) — Badge *Rekomendasi* (Hijau) / *Cepat* (Biru).
     - **NDJSON Stream** (`.jsonl`) — Badge *Forensik* (Ungu/Indigo) untuk SIEM & Wazuh log analyzer.
     - **Dokumen Resmi (.pdf)** (`.pdf`) — Badge *Legal Sah* (Merah) untuk bukti fisik audit BPKP.
   - Panel toggle keamanan forensik audit: *"Sertakan Hash HMAC-SHA256"* & *"Sertakan Snapshot Diff Mutasi"*.
   - Footer tombol aksi ganda yang seimbang: tombol netral `[ Batal ]` di kiri dan tombol primer `[ 📥 Unduh CSV ]` di kanan.

---

## 📸 2. DOKUMENTASI VISUAL & REFERENSI DESAIN

### 2.1 Desain Acuan Standar (Gold Standard):
Halaman Laporan Peserta Pembimbing (`/pembimbing/laporan-kinerja`):
![Referensi Desain Popover Laporan Peserta](file:///g:/MAGKOM/docs/plans/2026-11-09-audit-log-export-popover-reference-design.jpg)

### 2.2 Komparasi Desain:
| Elemen | Desain Lama (Audit Log) | Desain Baru yang Diinginkan (Adaptasi Laporan Peserta) |
|---|---|---|
| **Trigger Button** | Split button 2 bagian (`#btnQuickExport` + caret terpisah) | Tombol tunggal menyatu `[ 📥 Ekspor Log  v ]` dengan chevron terintegrasi |
| **Card Shape** | Box kaku tanpa pointer penunjuk yang jelas | Kartu mengambang halus (`border-radius: 12px`, shadow lembut, segitiga pointer di kanan atas) |
| **Format Selection** | Radio button polos | Kartu berbingkai rapi (`.audit-format-card`) dengan efek hover dan border seleksi biru terang |
| **Footer Actions** | Tombol "Opsi Lengkap..." & "Unduh CSV" | Tombol `[ Batal ]` (batal/tutup) dan tombol `[ 📥 Unduh (Format) ]` |
| **Interaktivitas** | Teks tombol tidak berubah | Teks tombol unduh dinamis berubah sesuai format yang dipilih (CSV, NDJSON, PDF) |

---

## 🏗️ 3. INVENTARIS FILE & ARSITEKTUR TEKNIS

Perubahan ini melibatkan 4 file utama:

```
┌────────────────────────────────────────────────────────────────────────┐
│ 1. resources/views/superadmin/audit-log/partials/_header.blade.php     │
│    -> Penyesuaian struktur tombol trigger ekspor di hero banner.       │
│                                                                        │
│ 2. resources/views/superadmin/audit-log/partials/_export_popover.blade.php
│    -> Rekonstruksi markup popover card sesuai acuan desain.            │
│                                                                        │
│ 3. resources/views/superadmin/audit-log/partials/_styles.blade.php     │
│    -> Penerapan styling CSS popover, pointer, card radio, dan footer.  │
│                                                                        │
│ 4. resources/views/superadmin/audit-log/partials/_scripts.blade.php    │
│    -> Penanganan interaksi buka/tutup, ganti format, dan tombol batal. │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 🛠️ 4. SPESIFIKASI TEKNIS & LANGKAH IMPLEMENTASI KODE (Atomic Tasks)

Junior Developer atau AI Agent **WAJIB** mengikuti langkah-langkah implementasi di bawah ini secara presisi:

---

### TUGAS 1: Perbarui Tombol Trigger di Hero Banner Card
**Lokasi File:** `resources/views/superadmin/audit-log/partials/_header.blade.php`

Ganti elemen `.audit-export-split-wrap` dan split button lama dengan tombol trigger terpadu `.btn-export-log-banner` yang memiliki ikon unduh dan panah chevron.

#### Kode SEBELUM:
```blade
        <div class="banner-actions-group">
            {{-- EXPORT ACTION: Streamlined Split Button with Format Dropdown Indicator --}}
            <div class="audit-export-split-wrap" id="exportSplitWrapper">
                <button 
                    type="button" 
                    id="btnQuickExport"
                    class="btn-banner-action"
                    title="Ekspor cepat CSV data terfilter"
                >
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Unduh Audit CSV</span>
                </button>
                <button 
                    type="button" 
                    id="exportSplitCaret"
                    class="audit-export-btn-caret"
                    title="Pilih format ekspor (CSV, NDJSON, PDF)"
                    aria-expanded="false"
                    aria-haspopup="menu"
                    aria-controls="exportPopover"
                    style="border-radius: 0 10px 10px 0; background: #e0e7ff; color: #01347C; height: 38px;"
                >
                    <svg id="caretSvg" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                {{-- Floating Dropdown Popover Container --}}
                @include('superadmin.audit-log.partials._export_popover', ['logs' => $logs])
            </div>
        </div>
```

#### Kode SESUDAH (Wajib Tepat):
```blade
        <div class="banner-actions-group">
            {{-- EXPORT ACTION: Single Unified Button with Sleman MagKom Popover --}}
            <div class="audit-export-dropdown-wrap" id="exportDropdownWrap">
                <button 
                    type="button" 
                    id="btnToggleExportPopover"
                    class="btn-export-log-banner"
                    aria-expanded="false"
                    aria-haspopup="menu"
                    aria-controls="exportPopover"
                    title="Pilih dan unduh log aktivitas (CSV, NDJSON, PDF)"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Ekspor Log</span>
                    <svg id="auditExportCaretSvg" class="audit-export-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>

                {{-- Floating Dropdown Popover Container --}}
                @include('superadmin.audit-log.partials._export_popover', ['logs' => $logs])
            </div>
        </div>
```

---

### TUGAS 2: Rekonstruksi Markup Popover Card Sesuai Referensi Laporan Peserta
**Lokasi File:** `resources/views/superadmin/audit-log/partials/_export_popover.blade.php`

Ganti seluruh isi file dengan struktur card yang memiliki header rapi, radio card modern, toggle opsi forensik, dan tombol Batal/Unduh di bagian footer.

#### Kode SESUDAH (Gantikan Seluruh Isi File):
```blade
{{-- EXPORT SYSTEM REDESIGN: ADOPTING PEMBIMBING LAPORAN PESERTA PATTERN --}}
@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $logs */
@endphp

<div 
    id="exportPopover" 
    class="audit-export-popover audit-export-popover-right" 
    style="display: none;" 
    role="menu" 
    aria-label="Menu Pilihan Format Ekspor"
>
    {{-- Popover Pointer Accent (Top Right) --}}
    <div class="audit-popover-pointer audit-pointer-right"></div>

    {{-- Popover Top Meta Header --}}
    <div class="audit-popover-header">
        <div>
            <div class="audit-popover-title">Ekspor Log Cepat</div>
            <div class="audit-popover-desc">Data hasil filter aktif</div>
        </div>
        <span class="audit-popover-row-count tabular-nums">
            {{ number_format($logs->total()) }} Baris
        </span>
    </div>

    {{-- Format Choice List --}}
    <div class="audit-popover-formats" role="radiogroup" aria-label="Format Berkas">
        {{-- Format 1: CSV (Selected / Default) --}}
        <label class="audit-format-card audit-format-selected" id="cardFormatCsv">
            <div class="audit-format-content">
                <input 
                    type="radio" 
                    name="popover_format" 
                    id="radioFormatCsv" 
                    value="csv" 
                    checked 
                    class="audit-format-radio"
                    onchange="handleAuditFormatSelection('csv')"
                />
                <div class="audit-format-details">
                    <div class="audit-format-name-wrap">
                        <span class="audit-format-name">CSV Tabular</span>
                        <span class="badge-format-ext badge-ext-csv">.csv</span>
                    </div>
                    <p class="audit-format-desc">Data mentah teks pemisah koma terstandarisasi</p>
                </div>
            </div>
            <div class="audit-format-meta">
                <span class="audit-badge-recommend">Rekomendasi</span>
            </div>
        </label>

        {{-- Format 2: NDJSON SIEM Stream --}}
        <label class="audit-format-card" id="cardFormatNdjson">
            <div class="audit-format-content">
                <input 
                    type="radio" 
                    name="popover_format" 
                    id="radioFormatNdjson" 
                    value="ndjson" 
                    class="audit-format-radio"
                    onchange="handleAuditFormatSelection('ndjson')"
                />
                <div class="audit-format-details">
                    <div class="audit-format-name-wrap">
                        <span class="audit-format-name">NDJSON SIEM Stream</span>
                        <span class="badge-format-ext badge-ext-ndjson">.jsonl</span>
                    </div>
                    <p class="audit-format-desc">Payload baris JSON terstruktur untuk Wazuh / SIEM</p>
                </div>
            </div>
            <div class="audit-format-meta">
                <span class="audit-badge-forensic">Forensik</span>
            </div>
        </label>

        {{-- Format 3: Dokumen Resmi PDF --}}
        <label class="audit-format-card" id="cardFormatPdf">
            <div class="audit-format-content">
                <input 
                    type="radio" 
                    name="popover_format" 
                    id="radioFormatPdf" 
                    value="pdf" 
                    class="audit-format-radio"
                    onchange="handleAuditFormatSelection('pdf')"
                />
                <div class="audit-format-details">
                    <div class="audit-format-name-wrap">
                        <span class="audit-format-name">Dokumen Resmi (.pdf)</span>
                        <span class="badge-format-ext badge-ext-pdf">.pdf</span>
                    </div>
                    <p class="audit-format-desc">Rekapitulasi cetak ber-kop resmi verifikasi BPKP</p>
                </div>
            </div>
            <div class="audit-format-meta">
                <span class="audit-badge-legal">Legal Sah</span>
            </div>
        </label>
    </div>

    {{-- Security Toggles Khusus Audit Log --}}
    <div class="audit-popover-security">
        <label class="audit-security-toggle">
            <span>Sertakan Hash HMAC-SHA256</span>
            <input type="checkbox" id="toggleHmac" checked class="audit-security-checkbox" />
        </label>
        <label class="audit-security-toggle">
            <span>Sertakan Snapshot Diff Mutasi</span>
            <input type="checkbox" id="toggleDiff" checked class="audit-security-checkbox" />
        </label>
    </div>

    {{-- Action CTA: Batal (Kiri) dan Unduh (Kanan) --}}
    <div class="audit-popover-cta">
        <button 
            type="button" 
            id="btnCancelExportPopover" 
            class="audit-btn-popover-cancel"
            onclick="toggleExportPopover(false)"
        >
            Batal
        </button>
        <button 
            type="button" 
            id="btnDoPopoverExport" 
            class="audit-btn-popover-download"
            onclick="triggerAuditExport()"
        >
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span id="btnExportText">Unduh CSV</span>
        </button>
    </div>
</div>
```

---

### TUGAS 3: Terapkan Styling CSS Presisi (Scoped Theme Engine)
**Lokasi File:** `resources/views/superadmin/audit-log/partials/_styles.blade.php`

Ganti styling lama untuk bagian `/* 9. EXPORT POPOVER */` dengan implementasi lengkap yang mengadopsi styling `laporan-kinerja-index.css` (termasuk pointer, card selection, badge warna, dan dark mode).

#### Kode CSS yang Diterapkan di `_styles.blade.php`:
```css
/* =========================================================================
   9. EXPORT POPOVER REDESIGN (Adopsi Laporan Peserta Pembimbing)
   ========================================================================= */

.audit-export-dropdown-wrap {
    position: relative;
    display: inline-block;
}

/* Tombol Ekspor di dalam Hero Banner Card */
.btn-export-log-banner {
    background: #ffffff;
    color: #01347C;
    text-decoration: none;
    padding: 0.65rem 1.35rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    white-space: nowrap;
}

.btn-export-log-banner:hover {
    background: #f8fafc;
    color: #002244;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.22);
}

.audit-export-chevron {
    transition: transform 0.2s ease;
}

.audit-export-dropdown-wrap.is-open .audit-export-chevron {
    transform: rotate(180deg);
}

/* Popover Floating Card */
.audit-export-popover,
.audit-export-popover.audit-export-popover-right {
    position: absolute;
    top: calc(100% + 12px);
    right: 0 !important;
    left: auto !important;
    width: 340px;
    max-width: calc(100vw - 32px);
    background: var(--sa-audit-surface, #FFFFFF);
    border: 1px solid var(--sa-audit-border, #E2E8F0);
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    padding: 1rem;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    animation: auditPopoverFadeIn 0.15s ease-out;
}

@keyframes auditPopoverFadeIn {
    from {
        opacity: 0;
        transform: translateY(-6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.audit-popover-pointer,
.audit-popover-pointer.audit-pointer-right {
    position: absolute;
    top: -6px;
    right: 24px !important;
    left: auto !important;
    width: 12px;
    height: 12px;
    background: var(--sa-audit-surface, #FFFFFF);
    border-top: 1px solid var(--sa-audit-border, #E2E8F0);
    border-left: 1px solid var(--sa-audit-border, #E2E8F0);
    transform: rotate(45deg);
}

.audit-popover-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--sa-audit-border-subtle, #F1F5F9);
}

.audit-popover-title {
    font-size: 0.8125rem;
    font-weight: 800;
    color: var(--sa-audit-text-main, #0F172A);
}

.audit-popover-desc {
    font-size: 0.6875rem;
    color: var(--sa-audit-text-muted, #64748B);
}

.audit-popover-row-count {
    padding: 0.15rem 0.5rem;
    border-radius: 4px;
    font-size: 0.6875rem;
    font-weight: 700;
    background: #EFF6FF;
    color: #1D4ED8;
    white-space: nowrap;
}

.audit-popover-formats {
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
}

.audit-format-card {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 0.6rem 0.75rem;
    border-radius: 8px;
    border: 1px solid var(--sa-audit-border, #E2E8F0);
    background: #F8FAFC;
    cursor: pointer;
    transition: all 0.15s ease;
    margin: 0;
    user-select: none;
}

.audit-format-card:hover {
    background: #F1F5F9;
    border-color: #CBD5E1;
}

.audit-format-card.audit-format-selected {
    border: 2px solid #0641CC;
    background: rgba(6, 65, 204, 0.05);
}

.audit-format-content {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
}

.audit-format-radio {
    margin-top: 3px;
    cursor: pointer;
    accent-color: #0641CC;
}

.audit-format-details {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
}

.audit-format-name-wrap {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.audit-format-name {
    font-size: 0.8125rem;
    font-weight: 700;
    color: var(--sa-audit-text-main, #0F172A);
}

.badge-format-ext {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.05rem 0.35rem;
    border-radius: 4px;
}

.badge-ext-csv {
    background: #DBEAFE;
    color: #1E40AF;
}

.badge-ext-ndjson {
    background: #EDE9FE;
    color: #5B21B6;
}

.badge-ext-pdf {
    background: #FEE2E2;
    color: #991B1B;
}

.audit-format-desc {
    font-size: 0.6875rem;
    color: var(--sa-audit-text-muted, #64748B);
    margin: 0;
    line-height: 1.3;
}

.audit-format-meta {
    display: flex;
    align-items: center;
}

.audit-badge-recommend {
    font-size: 0.65rem;
    font-weight: 700;
    color: #059669;
    background: #ECFDF5;
    padding: 0.1rem 0.4rem;
    border-radius: 4px;
    border: 1px solid #A7F3D0;
}

.audit-badge-forensic {
    font-size: 0.65rem;
    font-weight: 700;
    color: #6366F1;
    background: #EEF2FF;
    padding: 0.1rem 0.4rem;
    border-radius: 4px;
    border: 1px solid #C7D2FE;
}

.audit-badge-legal {
    font-size: 0.65rem;
    font-weight: 700;
    color: #DC2626;
    background: #FEF2F2;
    padding: 0.1rem 0.4rem;
    border-radius: 4px;
    border: 1px solid #FECACA;
}

/* Security Checkboxes */
.audit-popover-security {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    padding: 0.5rem;
    border-radius: 6px;
    background: #F1F5F9;
    font-size: 0.6875rem;
    color: var(--sa-audit-text-muted, #475569);
}

.audit-security-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    margin: 0;
}

.audit-security-checkbox {
    cursor: pointer;
    accent-color: #0641CC;
}

/* Footer CTA Actions */
.audit-popover-cta {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid var(--sa-audit-border-subtle, #F1F5F9);
}

.audit-btn-popover-cancel {
    height: 32px;
    padding: 0 0.85rem;
    background: transparent;
    border: 1px solid #CBD5E1;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
}

.audit-btn-popover-cancel:hover {
    background: #F1F5F9;
    color: #1E293B;
}

.audit-btn-popover-download {
    height: 32px;
    padding: 0 0.95rem;
    background: #0641CC;
    border: none;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #FFFFFF;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    transition: background 0.15s ease;
}

.audit-btn-popover-download:hover {
    background: #0533A3;
}

/* Dark Mode Overrides */
[data-theme="dark"] .audit-export-popover,
[data-theme="dark"] .audit-popover-pointer {
    background: #1E293B !important;
    border-color: #334155 !important;
}

[data-theme="dark"] .audit-format-card {
    background: #0F172A;
    border-color: #334155;
}

[data-theme="dark"] .audit-format-card:hover {
    background: #1E293B;
}

[data-theme="dark"] .audit-format-card.audit-format-selected {
    border-color: #3B82F6;
    background: rgba(59, 130, 246, 0.1);
}

[data-theme="dark"] .audit-popover-security {
    background: #0F172A;
    color: #94A3B8;
}

[data-theme="dark"] .audit-popover-row-count {
    background: rgba(30, 58, 138, 0.4) !important;
    color: #93C5FD !important;
}
```

---

### TUGAS 4: Penanganan Logika JavaScript & Interaktivitas Dinamis
**Lokasi File:** `resources/views/superadmin/audit-log/partials/_scripts.blade.php`

Perbarui fungsi kontrol dropdown popover agar mengikat tombol baru `btnToggleExportPopover`, merotasi chevron class `is-open`, merespons klik `Batal`, dan mengubah teks tombol unduh secara real-time saat format radio dipilih.

#### Kode yang Diperbarui di `_scripts.blade.php`:
```javascript
    // 1. KONTROL POPOVER EKSPOR (Gaya Laporan Peserta Pembimbing)
    const exportDropdownWrap = document.getElementById('exportDropdownWrap');
    const toggleExportBtn = document.getElementById('btnToggleExportPopover');
    const exportPopover = document.getElementById('exportPopover');
    const exportCaretSvg = document.getElementById('auditExportCaretSvg');

    function toggleExportPopover(forceOpen = null) {
        if (!exportPopover) return;
        const isOpen = forceOpen !== null ? forceOpen : exportPopover.style.display !== 'none';
        
        if (isOpen) {
            exportPopover.style.display = 'none';
            if (toggleExportBtn) toggleExportBtn.setAttribute('aria-expanded', 'false');
            if (exportDropdownWrap) exportDropdownWrap.classList.remove('is-open');
        } else {
            exportPopover.style.display = 'flex';
            if (toggleExportBtn) toggleExportBtn.setAttribute('aria-expanded', 'true');
            if (exportDropdownWrap) exportDropdownWrap.classList.add('is-open');
        }
    }

    if (toggleExportBtn) {
        toggleExportBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleExportPopover();
        });
    }

    // Dismiss Popover saat klik di luar
    document.addEventListener('click', function (e) {
        if (exportPopover && exportPopover.style.display !== 'none') {
            if (!exportPopover.contains(e.target) && !toggleExportBtn.contains(e.target)) {
                toggleExportPopover(true);
            }
        }
    });

    // Dismiss Popover dengan tombol Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (exportPopover && exportPopover.style.display !== 'none') {
                toggleExportPopover(true);
            }
        }
    });

    /**
     * Interaktivitas Seleksi Format Radio Card
     */
    window.handleAuditFormatSelection = function(format) {
        // Hapus kelas aktif dari semua kartu
        document.querySelectorAll('.audit-format-card').forEach(card => {
            card.classList.remove('audit-format-selected');
        });

        // Tambah kelas aktif ke kartu yang dipilih
        const activeCard = document.getElementById('cardFormat' + format.charAt(0).toUpperCase() + format.slice(1));
        if (activeCard) {
            activeCard.classList.add('audit-format-selected');
        }

        // Perbarui teks tombol unduh secara dinamis
        const btnText = document.getElementById('btnExportText');
        if (btnText) {
            if (format === 'csv') btnText.textContent = 'Unduh CSV';
            else if (format === 'ndjson') btnText.textContent = 'Unduh NDJSON';
            else if (format === 'pdf') btnText.textContent = 'Unduh Dokumen (.pdf)';
            else btnText.textContent = 'Unduh Berkas';
        }
    };
```

---

## 🛡️ 5. QUALITY GATES & VERIFIKASI SEBELUM SELESAI

Setelah kode diimplementasikan, Junior Developer / AI Agent **WAJIB** menjalankan pengujian berikut:

1. **Kompilasi Aset Frontend (Vite)**:
   ```bash
   npm run build
   ```
   *Ekspektasi:* Berhasil tanpa galat kompilasi CSS/JS.

2. **Automated Regression Tests**:
   ```bash
   php artisan test --filter=SuperAdminDashboardAndAuditLogTest
   php artisan test --filter=SuperAdminPortalVisualTest
   ```
   *Ekspektasi:* Seluruh test hijau (PASS).

3. **Verifikasi Visual di Browser**:
   Buka `http://localhost:8000/superadmin/audit-log` atau `http://magkom.test:8080/superadmin/audit-log`:
   - [ ] Tombol di hero card tampil menyatu: `[ 📥 Ekspor Log  v ]` dengan latar putih bersih.
   - [ ] Saat diklik, panah chevron berputar 180 derajat ke atas dan popover muncul dengan animasi halus.
   - [ ] Popover memiliki segitiga pointer di kanan atas yang presisi terhubung ke tombol.
   - [ ] Format card memiliki layout bergaris, badge warna, dan efek border biru saat dipilih.
   - [ ] Mengklik radio format (CSV, NDJSON, PDF) otomatis memindahkan highlight biru dan memperbarui teks tombol CTA unduh.
   - [ ] Menekan tombol `[ Batal ]`, menekan tombol `Escape`, atau mengklik di luar area kartu langsung menutup popover dengan lancar.
   - [ ] Bagian bawah popover **tidak terpotong sama sekali** (*zero clipping*).

---

## 📋 6. CHECKLIST PENYELESAIAN (Acceptance Criteria)

| No | Kriteria Penerimaan | Status |
|:--:|:--------------------|:------:|
| 1  | Split button lama diganti tombol tunggal terpadu `btn-export-log-banner`. | [ ] |
| 2  | Popover mengadopsi struktur persis halaman Laporan Peserta Pembimbing. | [ ] |
| 3  | Format disesuaikan untuk Audit Log: CSV Tabular, NDJSON SIEM, Dokumen PDF. | [ ] |
| 4  | Terdapat segitiga pointer accent (`.audit-popover-pointer`) di kanan atas. | [ ] |
| 5  | Interaktivitas klik radio card otomatis memperbarui tombol aksi unduh. | [ ] |
| 6  | Tombol `Batal` dan dismiss klik luar berfungsi menutup popover. | [ ] |
| 7  | Tampilan popover tidak terpotong (overflow visible pada `.sa-hero-banner`). | [ ] |
| 8  | Seluruh automated test `SuperAdminDashboardAndAuditLogTest` tetap PASS. | [ ] |

---
*Dokumen ini diterbitkan oleh Senior Lead Software Engineer MAGKOM sebagai acuan resmi standar desain antarmuka.*

