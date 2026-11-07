{{-- ==========================================================================
   Bento Hero Banner - Sleman Deep Navy
   Reference: Pantau progres dan penugasan peserta magang Anda
   ========================================================================== --}}
<div class="bento-hero-banner sc-reveal-hero" data-scroll-reveal>
    <div class="bento-hero-content">
        <span class="bento-hero-tag">
            {{ strtoupper(auth()->user()->bidang?->nama_bidang ?? 'BIDANG EGOV') }} &bull; DISKOMINFO SLEMAN
        </span>
        <h1 class="bento-hero-title">
            Pantau progres dan penugasan<br>peserta magang Anda
        </h1>
        <div class="bento-hero-actions">
            <a href="{{ route('pembimbing.tugas.create') }}" class="btn-bento-hero-cta">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Buat Tugas</span>
            </a>
        </div>
    </div>
    <div class="bento-hero-graphic" aria-hidden="true">
        <div class="bento-hero-bar bar-1"></div>
        <div class="bento-hero-bar bar-2"></div>
        <div class="bento-hero-bar bar-3"></div>
        <div class="bento-hero-bar bar-4"></div>
    </div>
</div>

