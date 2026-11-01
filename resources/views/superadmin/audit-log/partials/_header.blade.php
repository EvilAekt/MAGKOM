{{-- Header Card: Minimalist 1px Border, Zero Narrative Fluff, Unified Control Bar --}}
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
<header class="audit-header-card">
    {{-- Title & Integrity Badges (High Signal, No Narrative Sentences) --}}
    <div class="audit-header-title-block">
        <nav class="audit-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('superadmin.dashboard') }}" class="audit-breadcrumb-link">Portal MagKom</a>
            <span class="audit-breadcrumb-sep">/</span>
            <a href="{{ route('superadmin.dashboard') }}" class="audit-breadcrumb-link">SuperAdmin</a>
            <span class="audit-breadcrumb-sep">/</span>
            <span class="audit-breadcrumb-current">Jejak Audit</span>
            @if(isset($activeChannel) && $activeChannel !== 'all-actions-stream')
                <span class="audit-breadcrumb-sep">/</span>
                <span class="audit-breadcrumb-current">{{ $activeChannelMeta['label'] ?? $activeChannel }}</span>
            @endif
        </nav>

        <div class="audit-title-row">
            <h1 class="audit-page-title">
                Jejak Audit &amp; Kepatuhan
            </h1>
            <span class="sr-only">Jejak Audit Sistem</span>
            <div class="audit-integrity-badges">
                <span class="badge-integrity-valid">
                    <span class="badge-dot-emerald"></span>
                    SHA-256 Valid
                </span>
                <span class="badge-retention">
                    Retensi 365 Hari
                </span>
            </div>
        </div>
    </div>

    {{-- Unified Action Toolbar (Aligned 34px Height, Cohesive Controls) --}}
    <div class="audit-toolbar-row">
        {{-- Compact Search Input --}}
        <div class="audit-search-wrap">
            <label for="headerSearchInput" class="sr-only">Aktor / Staf</label>
            <input 
                type="text" 
                id="headerSearchInput"
                name="q_header"
                value="{{ request('q') }}"
                placeholder="Cari aktor, entitas, IP... [Ctrl+K]" 
                class="audit-input-compact"
                autocomplete="off"
            />
            <svg class="audit-search-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        {{-- Date Range Select --}}
        <select id="headerDateRange" class="audit-select-compact" aria-label="Rentang Waktu Log">
            <option value="" {{ !request('range') && !request('dari_tanggal') ? 'selected' : '' }}>Semua Waktu</option>
            <option value="today" {{ request('range') === 'today' ? 'selected' : '' }}>Hari Ini</option>
            <option value="7d" {{ request('range') === '7d' ? 'selected' : '' }}>7 Hari Terakhir</option>
            <option value="30d" {{ request('range') === '30d' ? 'selected' : '' }}>30 Hari Terakhir</option>
            <option value="custom" {{ request('dari_tanggal') ? 'selected' : '' }}>Kustom...</option>
        </select>

        {{-- Sync / Refresh Button --}}
        <a 
            href="{{ $activeChannel === 'all-actions-stream' ? route('superadmin.audit-log.index') : route('superadmin.audit-log.channel', ['channel' => $activeChannel]) }}"
            class="audit-btn-icon" 
            title="Muat ulang jejak audit"
            aria-label="Muat ulang jejak audit"
        >
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </a>

        {{-- EXPORT ACTION: Streamlined Split Button with Format Dropdown Indicator (Node 68749aa8 Pola A) --}}
        <div class="audit-export-split-wrap" id="exportSplitWrapper">
            <button 
                type="button" 
                id="btnQuickExport"
                class="audit-export-btn-primary"
                title="Ekspor cepat CSV data terfilter"
            >
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Ekspor</span>
            </button>
            <button 
                type="button" 
                id="exportSplitCaret"
                class="audit-export-btn-caret"
                title="Pilih format ekspor (CSV, NDJSON, PDF)"
                aria-expanded="false"
                aria-haspopup="menu"
                aria-controls="exportPopover"
            >
                <svg id="caretSvg" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            {{-- Floating Dropdown Popover Container --}}
            @include('superadmin.audit-log.partials._export_popover', ['logs' => $logs])
        </div>
    </div>
</header>
