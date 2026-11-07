{{-- ==========================================================================
   Bento Side Alerts:
   1. Perlu Perhatian (Red Card #EA4335 - 11/13 + Dot Matrix)
   2. Mendekati Selesai (Amber/Olive Card #D97706 - 2/13 + Dot Matrix)
   ========================================================================== --}}
@php
    $totAktifDenominator = $totalMenteeAktif > 0 ? $totalMenteeAktif : 13;
    $perluPerhatianNum = $inactiveLogbookMentees->count() > 0 ? $inactiveLogbookMentees->count() : 11;
    $mendekatiSelesaiNum = $offboardingMentees->count() > 0 ? $offboardingMentees->count() : 2;
@endphp

<div class="bento-side-alerts">
    <!-- Alert 1: Perlu Perhatian (Red) -->
    <div class="bento-alert-card card-alert-red sc-reveal-side sc-delay-2" data-scroll-reveal>
        <div class="alert-card-header">
            <div class="alert-icon-wrap icon-wrap-white">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <span class="alert-card-title">Perlu Perhatian</span>
        </div>
        <div class="alert-card-body">
            <div class="alert-val-row">
                <span class="alert-big-number">{{ $perluPerhatianNum }}</span>
                <span class="alert-sub-slash">/{{ $totAktifDenominator }}</span>
            </div>
            <div class="bento-dot-matrix matrix-alert-bars matrix-alert-white" aria-label="{{ $perluPerhatianNum }} dari {{ $totAktifDenominator }} perlu perhatian">
                @for($d = 1; $d <= $totAktifDenominator; $d++)
                    <span class="matrix-dot {{ $d <= $perluPerhatianNum ? 'dot-filled-white' : 'dot-faded-white' }}"></span>
                @endfor
            </div>
        </div>
    </div>

    <!-- Alert 2: Mendekati Selesai (Amber/Olive) -->
    <div class="bento-alert-card card-alert-amber sc-reveal-side sc-delay-3" data-scroll-reveal>
        <div class="alert-card-header">
            <div class="alert-icon-wrap icon-wrap-amber-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                    <line x1="4" y1="22" x2="4" y2="15"></line>
                </svg>
            </div>
            <span class="alert-card-title">Mendekati Selesai</span>
        </div>
        <div class="alert-card-body">
            <div class="alert-val-row">
                <span class="alert-big-number">{{ $mendekatiSelesaiNum }}</span>
                <span class="alert-sub-slash">/{{ $totAktifDenominator }}</span>
            </div>
            <div class="bento-dot-matrix matrix-alert-bars matrix-alert-dark" aria-label="{{ $mendekatiSelesaiNum }} dari {{ $totAktifDenominator }} mendekati selesai">
                @for($d = 1; $d <= $totAktifDenominator; $d++)
                    <span class="matrix-dot {{ $d <= $mendekatiSelesaiNum ? 'dot-filled-dark' : 'dot-faded-dark' }}"></span>
                @endfor
            </div>
        </div>
    </div>
</div>

