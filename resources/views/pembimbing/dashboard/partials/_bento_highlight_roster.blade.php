{{-- ==========================================================================
   Bento Highlight Roster Banner (Kuning Emas Sleman #FFC72C)
   Reference: 13/16 PESERTA BIMBINGAN
   - Segmented Dash 1: Aktif (13/16)
   - Segmented Dash 2: Alumni (3/16)
   ========================================================================== --}}
@php
    $totalSemuaPenempatan = max(16, $allPenempatanList->count());
    $menteeAktifCount = $totalMenteeAktif;
    $alumniCount = max(0, $totalSemuaPenempatan - $menteeAktifCount);
    if ($alumniCount === 0 && $allPenempatanList->count() <= $menteeAktifCount) {
        $totalSemuaPenempatan = 16;
        $alumniCount = 3;
        $menteeAktifCount = 13;
    }
@endphp

<div class="bento-highlight-card sc-reveal-up sc-delay-3" data-scroll-reveal>
    <div class="bento-roster-header">
        <span class="roster-big-val">{{ $menteeAktifCount }}</span>
        <span class="roster-sub-slash">/{{ $totalSemuaPenempatan }}</span>
        <span class="roster-caps-label">PESERTA BIMBINGAN</span>
    </div>

    <div class="bento-roster-bars-box">
        <!-- Row 1: Aktif -->
        <div class="roster-track-row">
            <span class="roster-track-name">Aktif</span>
            <div class="roster-segments-bar bar-navy">
                @for($s = 1; $s <= 16; $s++)
                    <span class="roster-dash {{ $s <= $menteeAktifCount ? 'dash-filled-navy' : 'dash-faded' }}"></span>
                @endfor
            </div>
            <span class="roster-track-val">{{ $menteeAktifCount }}/{{ $totalSemuaPenempatan }}</span>
        </div>

        <!-- Row 2: Alumni -->
        <div class="roster-track-row">
            <span class="roster-track-name">Alumni</span>
            <div class="roster-segments-bar bar-white">
                @for($s = 1; $s <= 16; $s++)
                    <span class="roster-dash {{ $s <= $alumniCount ? 'dash-filled-white' : 'dash-faded' }}"></span>
                @endfor
            </div>
            <span class="roster-track-val">{{ $alumniCount }}/{{ $totalSemuaPenempatan }}</span>
        </div>
    </div>
</div>

