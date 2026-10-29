{{-- ==========================================================================
   Four-Metric KPI Bar - Dashboard Peserta Magang
   Desain Modern Clean SaaS & DS-MagKom (Gentelella Parity)
   Surface Putih Bersih #ffffff / Dark Mode #003566, Soft Diffuse Shadow
   ========================================================================== --}}
@php
    /** @var \App\Models\Penempatan|null $penempatan */
    /** @var bool $isFinalNilai */
    /** @var int $totalLogbook */
    /** @var int $logbookPerluRevisi */
    /** @var int $totalTugas */
    /** @var int $tugasPending */
    /** @var int $sisaHari */
    /** @var int $durasiHari */
    /** @var \App\Models\Penilaian|null $penilaian */

    $tglSelesaiCarbon = $penempatan ? \Carbon\Carbon::parse($penempatan->tgl_selesai ?? $penempatan->tanggal_selesai) : null;
    $rawHp = $penempatan?->pembimbing?->no_hp;
    $cleanHp = $rawHp ? preg_replace('/[^0-9]/', '', (string)$rawHp) : null;
    if (is_string($cleanHp) && str_starts_with($cleanHp, '0')) {
        $cleanHp = '62' . substr($cleanHp, 1);
    }
    $hasWaPembimbing = !empty($cleanHp);
    $card4Url = $isFinalNilai ? route('peserta.nilai.show') : ($hasWaPembimbing ? ('https://wa.me/' . $cleanHp) : route('peserta.nilai.show'));
@endphp

<div class="saas-kpi-grid tile_count">
    <!-- Metrik 1: Logbook Aktivitas / Logbook Mingguan -->
    <a href="{{ route('peserta.logbook.index') }}" class="saas-card saas-kpi-card tile_stats_count" title="Buka Logbook Mingguan">
        <div class="saas-kpi-header">
            <span class="saas-kpi-label count_top">Logbook Aktivitas</span>
            <span class="saas-kpi-icon-blue" aria-hidden="true">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </span>
        </div>
        <div class="saas-kpi-content">
            <div class="saas-kpi-value count">
                {{ $totalLogbook }} <span class="saas-kpi-unit">Laporan</span>
            </div>
            <div class="saas-kpi-subtext count_bottom {{ $logbookPerluRevisi > 0 ? 'saas-kpi-danger' : 'saas-kpi-success' }}">
                @if($logbookPerluRevisi > 0)
                    <span class="saas-kpi-dot saas-dot-danger"></span>
                    <span>{{ $logbookPerluRevisi }} laporan perlu revisi &rarr;</span>
                @else
                    <span class="saas-kpi-dot saas-dot-success"></span>
                    <span>Logbook Mingguan tercatat rapi</span>
                @endif
            </div>
        </div>
    </a>

    <!-- Metrik 2: Tugas Magang / Tugas Bimbingan -->
    <a href="{{ route('peserta.tugas.index') }}" class="saas-card saas-kpi-card tile_stats_count" title="Buka Tugas Magang">
        <div class="saas-kpi-header">
            <span class="saas-kpi-label count_top">Tugas Magang</span>
            <span class="saas-kpi-icon-green" aria-hidden="true">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </span>
        </div>
        <div class="saas-kpi-content">
            <div class="saas-kpi-value count">
                {{ $tugasBelum }} <span class="saas-kpi-unit">Aktif</span>
            </div>
            <div class="saas-kpi-subtext count_bottom saas-kpi-muted">
                <span class="saas-kpi-dot saas-dot-muted"></span>
                <span>{{ $tugasSelesai }} Tugas Selesai &rarr;</span>
            </div>
        </div>
    </a>

    <!-- Metrik 3: Sisa Hari Magang -->
    <a href="{{ route('peserta.pendaftaran.index') }}" class="saas-card saas-kpi-card tile_stats_count" title="Lihat Sisa Hari Magang">
        <div class="saas-kpi-header">
            <span class="saas-kpi-label count_top">Sisa Hari Magang</span>
            <span class="saas-kpi-icon-amber" aria-hidden="true">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </span>
        </div>
        <div class="saas-kpi-content">
            <div class="saas-kpi-value count">
                {{ $sisaHari !== null ? $sisaHari : '-' }} <span class="saas-kpi-unit">Hari</span>
            </div>
            <div class="saas-kpi-subtext count_bottom saas-kpi-amber">
                <span class="saas-kpi-dot saas-dot-amber"></span>
                <span>Selesai {{ $tglSelesaiCarbon ? $tglSelesaiCarbon->isoFormat('D MMM Y') : '-' }}</span>
            </div>
        </div>
    </a>

    <!-- Metrik 4: Status Evaluasi / Sertifikat & Pembimbing -->
    <a href="{{ $card4Url }}" @if(!$isFinalNilai && $hasWaPembimbing) target="_blank" rel="noopener noreferrer" @endif class="saas-card saas-kpi-card tile_stats_count" title="Buka Informasi Status Evaluasi">
        <div class="saas-kpi-header">
            <span class="saas-kpi-label count_top">Status Evaluasi / Sertifikat</span>
            <span class="saas-kpi-icon-indigo" aria-hidden="true">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </span>
        </div>
        <div class="saas-kpi-content">
            <div class="saas-kpi-value count saas-kpi-value-truncate">
                {{ $penempatan->pembimbing->nama ?? 'Tim Diskominfo' }}
            </div>
            <div class="saas-kpi-subtext count_bottom saas-kpi-success">
                <span class="saas-kpi-dot saas-dot-success"></span>
                <span>{{ $isFinalNilai ? 'Nilai resmi diterbitkan &rarr;' : 'Pendampingan aktif &rarr;' }}</span>
            </div>
        </div>
    </a>
</div>
