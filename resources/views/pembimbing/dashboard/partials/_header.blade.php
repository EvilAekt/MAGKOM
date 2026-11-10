{{-- ==========================================================================
   Header Greeting - Dashboard Pembimbing Lapangan Khas Sleman MagKom
   ========================================================================== --}}
<div class="classroom-banner">
    <div class="banner-top-row">
        <div>
            <h1 class="banner-title">Dashboard Pembimbing</h1>
            <div class="banner-subtitle">
                <span>Bidang: <strong>{{ $pembimbingBidang }}</strong></span>
                <span>&bull;</span>
                <span>Pembimbing: <strong>{{ auth()->user()->nama }}</strong></span>
                <span>&bull;</span>
                <span>Diskominfo Kabupaten Sleman</span>
            </div>
        </div>
        <div>
            <a href="{{ route('pembimbing.tugas.create') }}" class="btn-create-task">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Buat Tugas Baru</span>
            </a>
        </div>
    </div>
</div>
