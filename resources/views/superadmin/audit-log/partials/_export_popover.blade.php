{{-- EXPORT SYSTEM REDESIGN: PATTERN A (QUICK-ACTION CONTEXTUAL TOOLBAR POPOVER) --}}
{{-- Canvas Node 68749aa8-f0a9-4d99-82f8-ac0ef6a3ba33 --}}
@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $logs */
@endphp

<div 
    id="exportPopover" 
    class="audit-export-popover" 
    style="display: none;" 
    role="menu" 
    aria-label="Menu Ekspor Cepat"
>
    {{-- Popover Pointer Accent (Top Right) --}}
    <div class="audit-popover-pointer"></div>

    {{-- Popover Top Meta --}}
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
                    value="csv" 
                    checked 
                    class="audit-format-radio"
                    onchange="handleExportFormatChange('csv')"
                />
                <div class="audit-format-details">
                    <div class="audit-format-name-wrap">
                        <span class="audit-format-name">CSV Tabular</span>
                        <span class="badge-format-ext badge-ext-csv">.csv</span>
                    </div>
                    <p class="audit-format-desc">Analisis cepat Excel &amp; Google Sheets</p>
                </div>
            </div>
            <div class="audit-format-meta">
                <span class="audit-badge-recommend">Rekomendasi</span>
                <span class="audit-format-size">~1.4 MB</span>
            </div>
        </label>

        {{-- Format 2: NDJSON --}}
        <label class="audit-format-card" id="cardFormatNdjson">
            <div class="audit-format-content">
                <input 
                    type="radio" 
                    name="popover_format" 
                    value="ndjson" 
                    class="audit-format-radio"
                    onchange="handleExportFormatChange('ndjson')"
                />
                <div class="audit-format-details">
                    <div class="audit-format-name-wrap">
                        <span class="audit-format-name">NDJSON SIEM Stream</span>
                        <span class="badge-format-ext badge-ext-ndjson">.jsonl</span>
                    </div>
                    <p class="audit-format-desc">Payload terstruktur untuk Wazuh / ELK</p>
                </div>
            </div>
            <div class="audit-format-meta">
                <span class="audit-badge-forensic">Forensik</span>
                <span class="audit-format-size">~3.8 MB</span>
            </div>
        </label>

        {{-- Format 3: PDF --}}
        <label class="audit-format-card" id="cardFormatPdf">
            <div class="audit-format-content">
                <input 
                    type="radio" 
                    name="popover_format" 
                    value="pdf" 
                    class="audit-format-radio"
                    onchange="handleExportFormatChange('pdf')"
                />
                <div class="audit-format-details">
                    <div class="audit-format-name-wrap">
                        <span class="audit-format-name">Berita Acara Resmi</span>
                        <span class="badge-format-ext badge-ext-pdf">.pdf</span>
                    </div>
                    <p class="audit-format-desc">Dokumen ber-QR code verifikasi BPKP</p>
                </div>
            </div>
            <div class="audit-format-meta">
                <span class="audit-badge-legal">Legal Sah</span>
                <span class="audit-format-size">~2.1 MB</span>
            </div>
        </label>
    </div>

    {{-- Quick Security Toggles --}}
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

    {{-- Action CTA --}}
    <div class="audit-popover-cta">
        <button 
            type="button" 
            class="audit-btn-more-options"
            onclick="showAdvancedExportOptions()"
        >
            Opsi Lengkap...
        </button>
        <button 
            type="button" 
            id="btnDoPopoverExport"
            class="audit-btn-download-export"
            onclick="triggerAuditExport()"
        >
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span id="btnExportText">Unduh CSV</span>
        </button>
    </div>
</div>
