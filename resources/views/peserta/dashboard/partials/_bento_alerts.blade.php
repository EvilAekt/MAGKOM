{{-- ==========================================================================
   Bento Side Alerts:
   Sisa Hari Magang (Amber Card - X hari + Dot Matrix)
   Reference: Precision Visual Parity with Pembimbing Dashboard
   ========================================================================== --}}
@php
    /** @var int|null $sisaHari */
    /** @var \App\Models\Penempatan|null $penempatan */

    $sisaHariVal = $sisaHari ?? 45;
@endphp

<div class="bento-side-alerts">
    <!-- Alert: Sisa Hari Magang (Amber) -->
    <div class="bento-alert-card card-alert-amber">
        <div class="alert-card-header">
            <div class="alert-icon-wrap icon-wrap-amber-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                    <line x1="4" y1="22" x2="4" y2="15"></line>
                </svg>
            </div>
            <span class="alert-card-title">Sisa Hari Magang</span>
        </div>
        <div class="alert-card-body">
            <div class="alert-val-row">
                <span class="alert-big-number">{{ $sisaHariVal }}</span>
                <span class="alert-sub-slash">hari</span>
            </div>
            <div class="bento-dot-matrix matrix-alert-bars matrix-alert-dark" aria-label="{{ $sisaHariVal }} hari sisa magang">
                @for($d = 1; $d <= 4; $d++)
                    <span class="matrix-dot {{ $d <= 3 ? 'dot-filled-dark' : 'dot-faded-dark' }}"></span>
                @endfor
            </div>
        </div>
    </div>
</div>
