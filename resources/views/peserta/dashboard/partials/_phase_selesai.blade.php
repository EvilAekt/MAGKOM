{{-- ==========================================================================
   Fase 4: Selesai Magang (Kelulusan) - Dashboard Peserta
   Desain Modern Clean SaaS & DS-MagKom (Gentelella Parity)
   ========================================================================== --}}
@php
    /** @var \App\Models\Penempatan|null $penempatan */
@endphp
@if($penempatan)
    <div class="saas-card">
        <div class="saas-card-header">
            <div>
                <h3 class="saas-item-title">
                    Penyelesaian Program Magang Diskominfo Sleman
                </h3>
                <p class="saas-card-subtitle">
                    Selamat! Anda telah menuntaskan seluruh periode magang dengan baik.
                </p>
            </div>
            <span class="saas-badge-approved">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="8" r="7"></circle>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                </svg>
                <span>Lulus &amp; Selesai</span>
            </span>
        </div>

        <div class="saas-card-body">
            <div class="saas-selesai-banner">
                <div class="saas-selesai-text-box">
                    <p class="saas-selesai-desc">
                        Seluruh kewajiban magang pada <strong>{{ $penempatan->bidang->nama_bidang ?? 'Dinas Kominfo Sleman' }}</strong> bersama pembimbing lapangan <strong>{{ $penempatan->pembimbing->nama ?? 'Tim Pembimbing Diskominfo' }}</strong> telah diverifikasi dan dinyatakan selesai secara lengkap.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif
