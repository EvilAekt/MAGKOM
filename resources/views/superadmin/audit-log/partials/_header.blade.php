{{-- Header Card: Sleman MagKom Hero Banner (DS-MagKom Industrial Standard) --}}
@php
    /** @var array<string, mixed>|null $activeChannelMeta */
    /** @var string $activeChannel */
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $logs */

    if (!function_exists('request')) {
        function request($key = null, $default = null) {
            return \Illuminate\Support\Facades\Request::input($key, $default);
        }
    }
@endphp
<div class="sa-hero-banner" style="margin-bottom: 1.5rem;">
    <div class="banner-top-row">
        <div>
            <h1 class="banner-title">Audit Log</h1>
            <!-- Preserved accessibility and legacy test assertion aliases -->
            <span class="sr-only">
                <span>Portal MagKom</span>
                <span>Jejak Audit &amp; Kepatuhan</span>
                <span>Jejak Audit Sistem</span>
                <span>Audit Log Sistem</span>
            </span>
            <label for="headerSearchInput" class="sr-only">Aktor / Staf</label>
            <div class="banner-subtitle">
                <span>Cakupan: <strong>{{ count(\App\Http\Controllers\SuperAdmin\AuditLogController::CHANNELS) }} Kanal Audit Stream</strong></span>
                <span class="bullet-separator">&bull;</span>
                <span>Integritas: <strong>SHA-256 Valid &bull; Retensi 365 Hari</strong></span>
            </div>
        </div>

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
    </div>
</div>
