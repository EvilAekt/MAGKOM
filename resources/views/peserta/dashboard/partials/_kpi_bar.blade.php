{{-- ==========================================================================
   Four-Metric KPI Bar - Dashboard Peserta Magang
   Desain Modern Clean SaaS (Linear / Shadcn UI Inspired)
   ========================================================================== --}}
@php
    $tglSelesaiCarbon = $penempatan ? \Carbon\Carbon::parse($penempatan->tgl_selesai ?? $penempatan->tanggal_selesai) : null;
@endphp

<style>
    .saas-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        width: 100%;
    }
    .saas-kpi-card {
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .saas-kpi-card:hover {
        transform: translateY(-2px);
        border-color: #93c5fd !important;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.08) !important;
    }
    @media (max-width: 1024px) {
        .saas-kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .saas-kpi-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="saas-kpi-grid">
    <!-- Metrik 1: Logbook Mingguan -->
    <a href="{{ route('peserta.logbook.index') }}" class="saas-card saas-kpi-card" style="padding: 20px 22px; display: flex; flex-direction: column; justify-content: space-between; gap: 12px; text-decoration: none; color: inherit; cursor: pointer;" title="Buka Logbook Mingguan">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span class="saas-kpi-label" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-500, #64748b);">Logbook Mingguan</span>
            <span class="saas-kpi-icon-blue" style="width: 34px; height: 34px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center;">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </span>
        </div>
        <div>
            <div class="saas-kpi-value" style="font-family: var(--font-display); font-size: 1.65rem; font-weight: 700; color: var(--neutral-900, #0f172a); line-height: 1.2;">
                {{ $totalLogbook }} <span class="saas-kpi-unit" style="font-size: 0.875rem; font-weight: 500; color: var(--neutral-500, #64748b);">Laporan</span>
            </div>
            <div class="saas-kpi-subtext" style="font-size: 0.775rem; color: {{ $logbookPerluRevisi > 0 ? '#dc2626' : 'var(--neutral-500, #64748b)' }}; margin-top: 6px; display: flex; align-items: center; gap: 4px;">
                @if($logbookPerluRevisi > 0)
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #dc2626;"></span>
                    <span>{{ $logbookPerluRevisi }} laporan perlu revisi &rarr;</span>
                @else
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #16a34a;"></span>
                    <span>Aktivitas tercatat rapi</span>
                @endif
            </div>
        </div>
    </a>

    <!-- Metrik 2: Tugas Bimbingan -->
    <a href="{{ route('peserta.tugas.index') }}" class="saas-card saas-kpi-card" style="padding: 20px 22px; display: flex; flex-direction: column; justify-content: space-between; gap: 12px; text-decoration: none; color: inherit; cursor: pointer;" title="Buka Tugas Bimbingan">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span class="saas-kpi-label" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-500, #64748b);">Tugas Bimbingan</span>
            <span class="saas-kpi-icon-green" style="width: 34px; height: 34px; border-radius: 10px; background: #f0fdf4; color: #16a34a; display: inline-flex; align-items: center; justify-content: center;">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </span>
        </div>
        <div>
            <div class="saas-kpi-value" style="font-family: var(--font-display); font-size: 1.65rem; font-weight: 700; color: var(--neutral-900, #0f172a); line-height: 1.2;">
                {{ $tugasBelum }} <span class="saas-kpi-unit" style="font-size: 0.875rem; font-weight: 500; color: var(--neutral-500, #64748b);">Aktif</span>
            </div>
            <div class="saas-kpi-subtext" style="font-size: 0.775rem; color: var(--neutral-500, #64748b); margin-top: 6px; display: flex; align-items: center; gap: 4px;">
                <span style="width: 6px; height: 6px; border-radius: 50%; background: #64748b;"></span>
                <span>{{ $tugasSelesai }} tugas telah diselesaikan &rarr;</span>
            </div>
        </div>
    </a>

    <!-- Metrik 3: Sisa Hari Magang -->
    <a href="{{ route('peserta.pendaftaran.index') }}" class="saas-card saas-kpi-card" style="padding: 20px 22px; display: flex; flex-direction: column; justify-content: space-between; gap: 12px; text-decoration: none; color: inherit; cursor: pointer;" title="Lihat Riwayat & Dokumen Magang">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span class="saas-kpi-label" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-500, #64748b);">Masa Magang</span>
            <span class="saas-kpi-icon-amber" style="width: 34px; height: 34px; border-radius: 10px; background: #fffbeb; color: #d97706; display: inline-flex; align-items: center; justify-content: center;">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </span>
        </div>
        <div>
            <div class="saas-kpi-value" style="font-family: var(--font-display); font-size: 1.65rem; font-weight: 700; color: var(--neutral-900, #0f172a); line-height: 1.2;">
                {{ $sisaHari !== null ? $sisaHari : '-' }} <span class="saas-kpi-unit" style="font-size: 0.875rem; font-weight: 500; color: var(--neutral-500, #64748b);">Hari</span>
            </div>
            <div class="saas-kpi-subtext" style="font-size: 0.775rem; color: var(--neutral-500, #64748b); margin-top: 6px; display: flex; align-items: center; gap: 4px;">
                <span style="width: 6px; height: 6px; border-radius: 50%; background: #d97706;"></span>
                <span>Selesai {{ $tglSelesaiCarbon ? $tglSelesaiCarbon->isoFormat('D MMM Y') : '-' }}</span>
            </div>
        </div>
    </a>

    <!-- Metrik 4: Status Bimbingan Lapangan -->
    @php
        $waPembimbingUrl = !empty($penempatan->pembimbing->no_hp) 
            ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $penempatan->pembimbing->no_hp) 
            : '#dashboardAsideContext';
    @endphp
    <a href="{{ $waPembimbingUrl }}" {{ !empty($penempatan->pembimbing->no_hp) ? 'target="_blank"' : '' }} class="saas-card saas-kpi-card" style="padding: 20px 22px; display: flex; flex-direction: column; justify-content: space-between; gap: 12px; text-decoration: none; color: inherit; cursor: pointer;" title="Hubungi Pembimbing">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span class="saas-kpi-label" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-500, #64748b);">Pembimbing</span>
            <span class="saas-kpi-icon-pink" style="width: 34px; height: 34px; border-radius: 10px; background: #fdf2f8; color: #db2777; display: inline-flex; align-items: center; justify-content: center;">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </span>
        </div>
        <div>
            <div class="saas-kpi-value" style="font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; color: var(--neutral-900, #0f172a); line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                {{ $penempatan->pembimbing->nama ?? 'Tim Diskominfo' }}
            </div>
            <div class="saas-kpi-subtext" style="font-size: 0.775rem; color: var(--neutral-500, #64748b); margin-top: 6px; display: flex; align-items: center; gap: 4px;">
                <span style="width: 6px; height: 6px; border-radius: 50%; background: #16a34a;"></span>
                <span>Pendampingan Aktif &rarr;</span>
            </div>
        </div>
    </a>
</div>
