{{-- ==========================================================================
   Header & Statusline Ribbon - Dashboard Operasional Administrasi (MagKom Sleman)
   Standar Visual Gentelella Theme (Clearview DNA & Sleman Navy #0641CC)
   ========================================================================== --}}
<div class="cv-hero-banner gentelella-hero-banner">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.25rem;">
        <div>
            <div class="cv-eyebrow">{{ now()->translatedFormat('l, d F Y') }}</div>
            <h1 class="cv-hero-title">Dashboard Operasional Administrasi</h1>
            <p class="cv-hero-sub">Pusat kendali verifikasi berkas pendaftaran, pengawasan SLA, dan alokasi kuota kejuruan dinas.</p>
            
            <div class="cv-stamp" style="margin-top: 0.65rem;">
                <span class="dot-pulse"></span>
                <span>Bulan: <strong>{{ $bulanAktif ? $bulanAktif->bulan_mulai?->translatedFormat('F Y') : 'Tidak Aktif' }}</strong> {{ $bulanAktif && $bulanAktif->aktif ? '(Aktif)' : '' }}</span>
            </div>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="adm-period-form" style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.28); padding: 0.45rem 0.85rem; border-radius: 8px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #93C5FD;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <label for="selectBulan" style="font-size: 0.75rem; font-weight: 700; color: #ffffff; margin: 0; text-transform: uppercase;">Pilih Bulan: <span style="font-size: 0.7rem; color: rgba(255, 255, 255, 0.8);">(Bulan Magang)</span> <span class="sr-only">Bulan:</span></label>
                <select name="bulan_id" id="selectBulan" onchange="this.form.submit()" style="background: transparent; border: none; font-size: 0.85rem; font-weight: 700; color: #ffffff; outline: none; cursor: pointer;">
                    @foreach($daftarBulan as $b)
                        <option value="{{ $b->id }}" {{ $bulanAktif && $bulanAktif->id === $b->id ? 'selected' : '' }} style="background: #0F172A; color: #ffffff;">
                            {{ $b->bulan_mulai?->translatedFormat('F Y') }} {{ $b->aktif ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </form>

            <a href="{{ route('admin.laporan.index') }}" style="height: 38px; padding: 0 1.15rem; border-radius: 8px; background: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.28); text-decoration: none; font-size: 0.8125rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; backdrop-filter: blur(8px); transition: all 0.2s ease;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                <span>Ekspor Laporan</span>
            </a>
        </div>
    </div>

    {{-- Integrated Four-Metric Dock --}}
    @include('admin.dashboard.partials._metrics')
</div>

{{-- Statusline Horizontal Ribbon --}}
<div class="cv-statusline gentelella-statusline">
    <div class="cv-statusline-item">
        <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981; box-shadow: 0 0 0 2px rgba(16,185,129,0.25);"></span>
        <dt>Portal Pendaftaran:</dt>
        <dd>Aktif</dd>
    </div>
    <div style="width: 1px; height: 16px; background: var(--neutral-300);"></div>
    <div class="cv-statusline-item">
        <dt>Standar SLA:</dt>
        <dd>Maks. 3 Hari Kerja</dd>
    </div>
    <div style="width: 1px; height: 16px; background: var(--neutral-300);"></div>
    <div class="cv-statusline-item">
        <dt>Rasio Pembimbing:</dt>
        <dd>{{ $pembimbingWorkload->isNotEmpty() ? round($pembimbingWorkload->avg('aktif_count'), 1) : 0 }} / 8 Mentee</dd>
    </div>
</div>
