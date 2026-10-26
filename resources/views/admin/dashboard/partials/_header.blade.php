{{-- ==========================================================================
   Header & Statusline Ribbon - Dashboard Operasional Administrasi (MagKom Sleman)
   Standar Visual Gentelella Theme (Clearview DNA & Sleman Navy #0641CC)
   ========================================================================== --}}
<div class="cv-hero-banner gentelella-hero-banner">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div class="cv-eyebrow">{{ now()->translatedFormat('l, d F Y') }} &bull; PORTAL RESMI KOMINFO SLEMAN</div>
            <h1 class="cv-hero-title">Dashboard Operasional Administrasi</h1>
            <p class="cv-hero-sub">Pusat pengelolaan verifikasi berkas pendaftaran, pengawasan SLA, alokasi kuota bidang dinas, penempatan, dan laporan magang.</p>
            
            <div class="cv-stamp">
                <span class="dot-pulse"></span>
                <span>PERIODE: {{ $bulanAktif ? strtoupper($bulanAktif->bulan_mulai?->translatedFormat('F Y')) : 'TIDAK AKTIF' }} {{ $bulanAktif && $bulanAktif->aktif ? '(AKTIF)' : '' }}</span>
            </div>
        </div>

        <div style="display: flex; gap: 0.625rem; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('admin.laporan.index') }}" style="height: 40px; padding: 0 1.15rem; border-radius: 8px; background: rgba(255,255,255,0.12); color: #ffffff; border: 1px solid rgba(255,255,255,0.25); text-decoration: none; font-size: 0.8125rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; backdrop-filter: blur(4px);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                <span>Ekspor Laporan</span>
            </a>
            <a href="{{ route('admin.pendaftaran.index') }}" style="height: 40px; padding: 0 1.35rem; border-radius: 8px; background: #ffffff; color: var(--blue-700, #0641CC); border: none; text-decoration: none; font-size: 0.8125rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 4px 14px rgba(0,0,0,0.25);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                <span>Tinjau Berkas</span>
            </a>
        </div>
    </div>

    {{-- Integrated Four-Metric Dock --}}
    @include('admin.dashboard.partials._metrics')
</div>

{{-- Statusline Horizontal Ribbon --}}
<div class="cv-statusline gentelella-statusline">
    <div class="cv-statusline-item">
        <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 2px rgba(16,185,129,0.25);"></span>
        <dt>Landing Publik:</dt>
        <dd>Aktif</dd>
    </div>
    <div style="width: 1px; height: 16px; background: var(--neutral-300);"></div>
    <div class="cv-statusline-item">
        <dt>Pendaftar Menunggu Verifikasi:</dt>
        <dd>{{ $pengajuanMenunggu }} Berkas</dd>
    </div>
    <div style="width: 1px; height: 16px; background: var(--neutral-300);"></div>
    <div class="cv-statusline-item">
        <dt>SLA Maksimal:</dt>
        <dd>3 Hari Kerja (BR-04 FIFO)</dd>
    </div>
    <div style="width: 1px; height: 16px; background: var(--neutral-300);"></div>
    <div class="cv-statusline-item">
        <dt>Beban Mentor Rata-rata:</dt>
        <dd>{{ $pembimbingWorkload->isNotEmpty() ? round($pembimbingWorkload->avg('aktif_count'), 1) : 0 }} / 8 Mentee</dd>
    </div>

    <div style="margin-left: auto; display: flex; align-items: center; gap: 0.5rem;">
        <span style="font-size: 0.72rem; color: var(--neutral-500); font-weight: 600;">Status Operasional Aktif</span>
    </div>
</div>
