{{-- LEFT SIDEBAR: CHANNEL NAVIGATOR & COMPLIANCE TOKENS (3 of 12 cols) --}}
@php
    /** @var array<string, array<string, mixed>> $channels */
    /** @var string $activeChannel */

    if (!function_exists('e')) {
        function e($value, $doubleEncode = true) {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8', $doubleEncode);
        }
    }
    if (!function_exists('route')) {
        function route($name, $parameters = [], $absolute = true) {
            return \Illuminate\Support\Facades\URL::route($name, $parameters, $absolute);
        }
    }
@endphp
<aside class="audit-sidebar-col" aria-label="Navigasi Kanal &amp; Kepatuhan">

    {{-- Panel 1: 15 Audit Channels --}}
    <div class="audit-panel-card">
        <div class="audit-panel-header">
            <div class="audit-panel-header-left">
                <span class="audit-panel-title">Kanal Audit</span>
                <span class="audit-panel-count-badge">15</span>
            </div>
            <span class="audit-panel-header-sub">24 Jam</span>
        </div>

        {{-- Channel Filter Search Input --}}
        <div class="audit-channel-search-wrap">
            <input 
                type="text" 
                id="channelFilterInput"
                placeholder="Filter kanal..." 
                class="audit-channel-filter-input"
                aria-label="Filter daftar kanal"
                autocomplete="off"
            />
            <svg class="audit-channel-search-icon" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        {{-- Channel Links List with Scroll --}}
        <nav class="audit-channel-nav-list" id="channelLinksNav" aria-label="Daftar Kanal Audit">
            @foreach($channels as $key => $c)
                @php
                    $isActive = ($activeChannel === $key);
                    $dotColor = $c['color'] ?? '#2563EB';
                @endphp
                <a 
                    href="{{ $key === 'all-actions-stream' ? route('superadmin.audit-log.index') : route('superadmin.audit-log.channel', ['channel' => $key]) }}"
                    class="audit-channel-link {{ $isActive ? 'active' : '' }}"
                    data-channel-key="{{ $key }}"
                    data-channel-label="{{ strtolower($c['label']) }}"
                    @if($isActive) aria-current="page" @endif
                    title="{{ $c['desc'] ?? $c['label'] }}"
                >
                    <div class="audit-channel-label-wrap">
                        <span class="audit-channel-dot" style="background-color: {{ $dotColor }};"></span>
                        <span class="audit-channel-text">{{ $c['label'] }}</span>
                    </div>
                    <span class="audit-channel-count {{ $isActive ? 'audit-count-active' : '' }}">
                        {{ number_format($c['count_24h'] ?? 0) }}
                    </span>
                </a>
            @endforeach
        </nav>
    </div>

    {{-- Panel 2: Standar Kepatuhan (Decluttered Key-Value Spec, Zero Paragraphs) --}}
    <div class="audit-panel-card audit-compliance-card">
        <div class="audit-panel-header">
            <span class="audit-panel-title">Integritas Log</span>
            <span class="badge-worm-policy">WORM Policy</span>
        </div>

        <div class="audit-compliance-specs">
            <div class="audit-compliance-row">
                <span class="audit-compliance-label">Rantai Hash</span>
                <span class="audit-spec-emerald font-mono">SHA-256 Valid</span>
            </div>

            <div class="audit-compliance-row">
                <span class="audit-compliance-label">Perekaman Diff</span>
                <span class="audit-spec-slate font-mono">Atomic Old/New</span>
            </div>

            <div class="audit-compliance-row border-b-0">
                <span class="audit-compliance-label">Standar Audit</span>
                <span class="audit-spec-purple font-mono">ISO/IEC 27001</span>
            </div>
        </div>
    </div>

</aside>
