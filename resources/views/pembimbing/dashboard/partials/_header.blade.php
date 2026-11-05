{{-- ==========================================================================
   Header Greeting - Dashboard Pembimbing Lapangan (Zenius Clean Greeting)
   ========================================================================== --}}
<h1 class="sr-only">Dashboard Pembimbing</h1>

<header class="zenius-greeting-card">
    <div class="zenius-greeting-info">
        <span class="zenius-bidang-pill">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
            {{ $pembimbingBidang }} &bull; Diskominfo Sleman
        </span>
        <h2 class="zenius-greeting-title">
            Selamat Datang, {{ auth()->user()->nama }}! 👋
        </h2>
        <p class="zenius-greeting-subtitle">
            Pusat kendali dan evaluasi bimbingan peserta magang dinas hari ini.
        </p>
    </div>
</header>
