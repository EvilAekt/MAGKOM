{{-- ==========================================================================
   Bento Metrics 3 Mini-Cards
   Reference: Mentee Aktif (13/20), Tugas Berjalan (20), Perlu Evaluasi (3)
   ========================================================================== --}}
<div class="bento-kpi-row">
    <!-- Card 1: Mentee Aktif -->
    <div class="bento-card bento-kpi-card kpi-card-mentee sc-reveal-up sc-delay-1" data-scroll-reveal>
        <div class="bento-kpi-top">
            <div class="bento-icon-box bento-icon-blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <span class="bento-pill-badge badge-green">{{ $persentaseKapasitas }}% kuota</span>
        </div>
        <div class="bento-kpi-body">
            <span class="bento-kpi-label">MENTEE AKTIF</span>
            <div class="bento-kpi-val-row">
                <span class="bento-kpi-number">{{ $totalMenteeAktif }}</span>
                <span class="bento-kpi-sub">/{{ $maxBebanKapasitas }}</span>
            </div>
            <div class="bento-dot-matrix matrix-kpi-bars matrix-blue" aria-label="{{ $totalMenteeAktif }} dari {{ $maxBebanKapasitas }} slot terisi">
                @for($i = 1; $i <= max(20, $maxBebanKapasitas); $i++)
                    <span class="matrix-dot {{ $i <= $totalMenteeAktif ? 'dot-filled' : 'dot-empty' }}"></span>
                @endfor
            </div>
        </div>
    </div>

    <!-- Card 2: Tugas Berjalan -->
    <div class="bento-card bento-kpi-card kpi-card-tugas sc-reveal-up sc-delay-2" data-scroll-reveal>
        <div class="bento-kpi-top">
            <div class="bento-icon-box bento-icon-indigo">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 11 12 14 22 4"></polyline>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                </svg>
            </div>
            <span class="bento-pill-badge badge-indigo">{{ $persenTugasKumulatif }}% tuntas</span>
        </div>
        <div class="bento-kpi-body">
            <span class="bento-kpi-label">TUGAS BERJALAN</span>
            <div class="bento-kpi-val-row">
                <span class="bento-kpi-number">{{ $totalTugasKumulatif }}</span>
            </div>
            @php
                $filledDotsTugas = $totalTugasKumulatif > 0
                    ? max(1, (int) round(($tugasSelesaiKumulatif / max(1, $totalTugasKumulatif)) * 20))
                    : 6;
            @endphp
            <div class="bento-dot-matrix matrix-kpi-bars matrix-indigo" aria-label="{{ $persenTugasKumulatif }}% tugas tuntas">
                @for($i = 1; $i <= 20; $i++)
                    <span class="matrix-dot {{ $i <= $filledDotsTugas ? 'dot-filled' : 'dot-empty' }}"></span>
                @endfor
            </div>
        </div>
    </div>

    <!-- Card 3: Perlu Evaluasi -->
    <div class="bento-card bento-kpi-card kpi-card-evaluasi sc-reveal-up sc-delay-3" data-scroll-reveal>
        <div class="bento-kpi-top">
            <div class="bento-icon-box bento-icon-amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
            </div>
            <span class="bento-pill-badge badge-amber">Review</span>
        </div>
        <div class="bento-kpi-body">
            <span class="bento-kpi-label">PERLU EVALUASI</span>
            <div class="bento-kpi-val-row">
                <span class="bento-kpi-number">{{ $allPendingProgresLogs->count() }}</span>
            </div>
            <div class="bento-dot-matrix matrix-kpi-bars matrix-amber" aria-label="{{ $allPendingProgresLogs->count() }} progres menunggu evaluasi">
                @for($i = 1; $i <= 20; $i++)
                    <span class="matrix-dot {{ $i <= $allPendingProgresLogs->count() ? 'dot-filled' : 'dot-empty' }}"></span>
                @endfor
            </div>
        </div>
    </div>
</div>

