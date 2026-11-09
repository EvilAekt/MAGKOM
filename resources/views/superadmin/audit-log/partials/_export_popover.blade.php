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
                    <p class="audit-format-desc">Rekapitulasi cetak Berita Acara Resmi verifikasi BPKP</p>
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
