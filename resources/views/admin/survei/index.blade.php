@extends('layouts.dashboard')

@section('title', 'Evaluasi Mutu & Survei Kepuasan - MagKom Sleman')
@section('page_title', 'Indeks Kepuasan Magang (IKM) & Net Promoter Score')
@section('page_description', 'Rekapitulasi evaluasi pengalaman bimbingan, relevansi penugasan, dan saran perbaikan dari alumni peserta (PRD 4.3 Poin 4).')

@section('breadcrumbs')
    <a href="{{ route('admin.laporan.index') }}" class="breadcrumb-item">Laporan & Ekspor</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Survei Kepuasan Magang</span>
@endsection

@push('styles')
<style>
    /* Styling Scoped: Dasbor Evaluasi Mutu & Survei Kepuasan Admin */
    .survei-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    /* 1. Header Toolbar Eksekutif */
    .survei-hero-toolbar {
        background: #ffffff;
        border: 1px solid var(--neutral-200, #E2E8F0);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .survei-badge-category {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.725rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #0641CC;
        background: #e0e7ff;
        padding: 3px 10px;
        border-radius: 999px;
        margin-bottom: 0.35rem;
    }

    .survei-toolbar-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--neutral-900, #0F172A);
        margin: 0;
        line-height: 1.35;
    }

    .survei-toolbar-subtitle {
        font-size: 0.85rem;
        color: var(--neutral-600, #64748B);
        margin-top: 0.25rem;
    }

    .survei-toolbar-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn-toolbar-secondary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.55rem 1rem;
        background: var(--neutral-100, #F1F5F9);
        color: var(--neutral-700, #334155);
        font-size: 0.825rem;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        border: 1px solid var(--neutral-300, #CBD5E1);
        transition: all 0.15s ease;
    }
    .btn-toolbar-secondary:hover {
        background: var(--neutral-200, #E2E8F0);
        color: #0F172A;
    }

    .btn-toolbar-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.55rem 1.15rem;
        background: #0641CC;
        color: #ffffff;
        font-size: 0.825rem;
        font-weight: 700;
        border-radius: 8px;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(6, 65, 204, 0.2);
        transition: all 0.15s ease;
    }
    .btn-toolbar-primary:hover {
        background: #042d91;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(6, 65, 204, 0.28);
    }

    /* 2. Grid Kartu Metrik Makro (IKM & NPS) */
    .macro-metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 1.25rem;
    }

    .macro-card {
        background: #ffffff;
        border: 1px solid var(--neutral-200, #E2E8F0);
        border-radius: 14px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .macro-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }

    .macro-card-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--neutral-600, #64748B);
    }

    .macro-card-score-row {
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
        margin-top: 0.25rem;
    }

    .macro-card-score {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.02em;
    }

    .macro-card-denom {
        font-size: 1.15rem;
        font-weight: 600;
        color: var(--neutral-500, #94A3B8);
    }

    .score-progress-track {
        height: 8px;
        width: 100%;
        background: var(--neutral-200, #E2E8F0);
        border-radius: 999px;
        overflow: hidden;
        margin: 1rem 0 0.65rem 0;
    }

    .score-progress-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Segmented NPS Bar */
    .nps-segmented-bar {
        display: flex;
        height: 8px;
        width: 100%;
        border-radius: 999px;
        overflow: hidden;
        margin: 1rem 0 0.65rem 0;
        background: var(--neutral-200, #E2E8F0);
    }

    .nps-seg-promoters { background: #10B981; }
    .nps-seg-passives { background: #F59E0B; }
    .nps-seg-detractors { background: #EF4444; }

    .nps-legend-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        font-size: 0.75rem;
        color: var(--neutral-600, #64748B);
        margin-top: 0.5rem;
    }

    .nps-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-weight: 600;
    }

    .nps-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    /* 3. Grid 3-Pilar Dimensi Mutu */
    .pillar-metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    .pillar-card {
        background: #ffffff;
        border: 1px solid var(--neutral-200, #E2E8F0);
        border-radius: 14px;
        padding: 1.25rem 1.4rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }

    .pillar-card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.65rem;
    }

    .pillar-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .pillar-icon-badge {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .pillar-score-row {
        display: flex;
        align-items: baseline;
        gap: 0.35rem;
        margin-bottom: 0.4rem;
    }

    .pillar-score {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .pillar-stars {
        display: flex;
        align-items: center;
        gap: 3px;
        color: #F59E0B;
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }

    .pillar-desc {
        font-size: 0.8rem;
        color: var(--neutral-600, #64748B);
        line-height: 1.4;
        margin: 0;
    }

    /* 4. Tabel Masukan & Testimoni Peserta */
    .evaluasi-table-card {
        background: #ffffff;
        border: 1px solid var(--neutral-200, #E2E8F0);
        border-radius: 14px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .evaluasi-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid var(--neutral-200, #F1F5F9);
        padding-bottom: 1rem;
    }

    .evaluasi-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--neutral-900, #0F172A);
        margin: 0;
    }

    .evaluasi-subtitle {
        font-size: 0.825rem;
        color: var(--neutral-600, #64748B);
        margin-top: 0.25rem;
    }

    .evaluasi-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
        text-align: left;
    }

    .evaluasi-table thead tr {
        background: #f8fafc;
        border-bottom: 1px solid var(--neutral-200, #E2E8F0);
    }

    .evaluasi-table th {
        padding: 0.85rem 1rem;
        font-size: 0.725rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--neutral-600, #64748B);
    }

    .evaluasi-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--neutral-100, #F1F5F9);
        vertical-align: top;
    }

    .evaluasi-table tbody tr:hover {
        background: #fafafa;
    }

    .mentee-profile-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mentee-avatar-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #e0e7ff;
        color: #0641CC;
        font-size: 0.75rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .feedback-bubble {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        font-size: 0.825rem;
        color: var(--neutral-800, #1E293B);
        line-height: 1.45;
        font-style: italic;
        margin-bottom: 0.35rem;
    }

    .suggestion-box {
        display: inline-flex;
        align-items: flex-start;
        gap: 6px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.775rem;
        line-height: 1.4;
    }

    .pill-score {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    /* 5. Empty State Elegan */
    .survei-empty-state {
        text-align: center;
        padding: 4rem 1.5rem;
    }

    .survei-empty-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #e0e7ff;
        color: #0641CC;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem auto;
    }

    /* Dark Mode Adjustments */
    [data-theme="dark"] .survei-hero-toolbar,
    [data-theme="dark"] .macro-card,
    [data-theme="dark"] .pillar-card,
    [data-theme="dark"] .evaluasi-table-card {
        background: var(--neutral-800, #1E293B);
        border-color: var(--neutral-700, #334155);
    }
    [data-theme="dark"] .survei-toolbar-title,
    [data-theme="dark"] .evaluasi-title {
        color: #F8FAFC;
    }
    [data-theme="dark"] .btn-toolbar-secondary {
        background: var(--neutral-900, #0F172A);
        border-color: var(--neutral-700, #334155);
        color: #E2E8F0;
    }
    [data-theme="dark"] .btn-toolbar-secondary:hover {
        background: var(--neutral-800, #1E293B);
    }
    [data-theme="dark"] .evaluasi-table thead tr {
        background: var(--neutral-900, #0F172A);
        border-color: var(--neutral-700, #334155);
    }
    [data-theme="dark"] .evaluasi-table td {
        border-color: var(--neutral-700, #334155);
    }
    [data-theme="dark"] .evaluasi-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }
    [data-theme="dark"] .feedback-bubble {
        background: var(--neutral-900, #0F172A);
        border-color: var(--neutral-700, #334155);
        color: #E2E8F0;
    }
    [data-theme="dark"] .suggestion-box {
        background: rgba(29, 78, 216, 0.2);
        border-color: rgba(59, 130, 246, 0.4);
        color: #93c5fd;
    }
    [data-theme="dark"] .score-progress-track,
    [data-theme="dark"] .nps-segmented-bar {
        background: var(--neutral-700, #334155);
    }

    @media (max-width: 768px) {
        .macro-metrics-grid,
        .pillar-metrics-grid {
            grid-template-columns: 1fr;
        }
        .survei-hero-toolbar {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="survei-container">

    {{-- 1. Executive Hero Toolbar --}}
    <div class="survei-hero-toolbar">
        <div>
            <div class="survei-badge-category">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                Evaluasi Mutu Penyelenggaraan
            </div>
            <h1 class="survei-toolbar-title">Indeks Kepuasan Magang (IKM) & Net Promoter Score</h1>
            <p class="survei-toolbar-subtitle">
                Total Responden Terdata: <strong>{{ number_format($totalResponden) }} Alumni Peserta</strong> &bull; Standar Penjaminan Mutu PRD 4.3 Poin 4
            </p>
        </div>

        <div class="survei-toolbar-actions">
            <a href="{{ route('admin.laporan.index') }}" class="btn-toolbar-secondary">
                &larr; Hub Laporan
            </a>
            <a href="{{ route('admin.survei.export') }}" class="btn-toolbar-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Hasil Survei (CSV)
            </a>
        </div>
    </div>

    {{-- 2. Ringkasan Metrik Makro (IKM & NPS) --}}
    @php
        $ikmPercent = min(100, max(0, $ikmSkala100));
        
        // Klasifikasi Kategori IKM
        if ($ikmSkala100 >= 80) {
            $ikmLabel = 'Sangat Memuaskan (A)';
            $ikmColor = '#059669';
            $ikmBg = '#d1fae5';
        } elseif ($ikmSkala100 >= 60) {
            $ikmLabel = 'Memuaskan (B)';
            $ikmColor = '#0641CC';
            $ikmBg = '#e0e7ff';
        } else {
            $ikmLabel = 'Cukup (C)';
            $ikmColor = '#b45309';
            $ikmBg = '#fef3c7';
        }

        // Klasifikasi NPS
        if ($npsScore >= 70) {
            $npsLabel = 'World Class';
            $npsColor = '#059669';
            $npsBg = '#d1fae5';
        } elseif ($npsScore >= 50) {
            $npsLabel = 'Sangat Baik';
            $npsColor = '#059669';
            $npsBg = '#d1fae5';
        } elseif ($npsScore >= 0) {
            $npsLabel = 'Positif';
            $npsColor = '#0641CC';
            $npsBg = '#e0e7ff';
        } else {
            $npsLabel = 'Perlu Perbaikan';
            $npsColor = '#dc2626';
            $npsBg = '#fee2e2';
        }

        // Proporsi NPS Bar
        $pctPromoters = $totalResponden > 0 ? round(($promoters / $totalResponden) * 100) : 0;
        $pctPassives = $totalResponden > 0 ? round(($passives / $totalResponden) * 100) : 0;
        $pctDetractors = $totalResponden > 0 ? round(($detractors / $totalResponden) * 100) : 0;
    @endphp

    <div class="macro-metrics-grid">
        <!-- Kartu 1: Indeks Kepuasan Magang (IKM) -->
        <div class="macro-card">
            <div>
                <div class="macro-card-header">
                    <div>
                        <div class="macro-card-label">Indeks Kepuasan Magang (IKM)</div>
                        <div class="macro-card-score-row">
                            <span class="macro-card-score" style="color: #0641CC;">{{ $ikmSkala100 }}</span>
                            <span class="macro-card-denom">/ 100</span>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: {{ $ikmColor }}; background: {{ $ikmBg }}; padding: 4px 10px; border-radius: 999px;">
                        {{ $ikmLabel }}
                    </span>
                </div>

                <div class="score-progress-track">
                    <div class="score-progress-fill" style="width: {{ $ikmPercent }}%; background: linear-gradient(90deg, #0641CC 0%, #10B981 100%);"></div>
                </div>
            </div>

            <div style="font-size: 0.775rem; color: var(--neutral-600, #64748B); display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--neutral-200, #F1F5F9); padding-top: 0.65rem; margin-top: 0.5rem;">
                <span>Skor Rata-Rata Komposit: <strong>{{ $kompositIkm }} / 5.0</strong></span>
                <span style="color: #0641CC; font-weight: 600;">{{ $ikmPercent }}% Capaian</span>
            </div>
        </div>

        <!-- Kartu 2: Net Promoter Score (NPS) -->
        <div class="macro-card">
            <div>
                <div class="macro-card-header">
                    <div>
                        <div class="macro-card-label">Net Promoter Score (NPS)</div>
                        <div class="macro-card-score-row">
                            <span class="macro-card-score" style="color: {{ $npsScore >= 0 ? '#059669' : '#dc2626' }};">
                                {{ $npsScore > 0 ? '+' : '' }}{{ $npsScore }}
                            </span>
                            <span class="macro-card-denom">pts</span>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: {{ $npsColor }}; background: {{ $npsBg }}; padding: 4px 10px; border-radius: 999px;">
                        {{ $npsLabel }}
                    </span>
                </div>

                <!-- NPS Segmented Breakdown Bar -->
                <div class="nps-segmented-bar" title="Promoters: {{ $pctPromoters }}% | Passives: {{ $pctPassives }}% | Detractors: {{ $pctDetractors }}%">
                    @if($totalResponden > 0)
                        <div class="nps-seg-promoters" style="width: {{ $pctPromoters }}%;"></div>
                        <div class="nps-seg-passives" style="width: {{ $pctPassives }}%;"></div>
                        <div class="nps-seg-detractors" style="width: {{ $pctDetractors }}%;"></div>
                    @else
                        <div style="width: 100%; background: var(--neutral-200, #E2E8F0);"></div>
                    @endif
                </div>
            </div>

            <div class="nps-legend-row" style="border-top: 1px solid var(--neutral-200, #F1F5F9); padding-top: 0.65rem; margin-top: 0.5rem;">
                <span class="nps-legend-item">
                    <span class="nps-dot" style="background: #10B981;"></span>
                    <span><strong>{{ $promoters }}</strong> Promoters (9-10)</span>
                </span>
                <span class="nps-legend-item">
                    <span class="nps-dot" style="background: #F59E0B;"></span>
                    <span><strong>{{ $passives }}</strong> Passives (7-8)</span>
                </span>
                <span class="nps-legend-item">
                    <span class="nps-dot" style="background: #EF4444;"></span>
                    <span><strong>{{ $detractors }}</strong> Detractors (&le;6)</span>
                </span>
            </div>
        </div>
    </div>

    {{-- 3. Grid 3 Pilar Dimensi Mutu Penyelenggaraan --}}
    <div class="pillar-metrics-grid">
        <!-- Pilar 1: Bimbingan Mentor -->
        <div class="pillar-card">
            <div>
                <div class="pillar-card-top">
                    <span class="pillar-label" style="color: #7E22CE;">Bimbingan Mentor</span>
                    <div class="pillar-icon-badge" style="background: #f3e8ff; color: #7E22CE;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                </div>

                <div class="pillar-score-row">
                    <span class="pillar-score" style="color: #7E22CE;">{{ $avgMentoring }}</span>
                    <span class="macro-card-denom">/ 5.0</span>
                </div>

                <div class="pillar-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $i <= round($avgMentoring) ? '#F59E0B' : '#E2E8F0' }}" stroke="none">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    @endfor
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700, #334155); margin-left: 4px;">
                        ({{ $avgMentoring > 0 ? round(($avgMentoring / 5) * 100) : 0 }}%)
                    </span>
                </div>
            </div>

            <p class="pillar-desc">
                Kualitas arahan, responsivitas, dan asistensi teknis pembimbing lapangan.
            </p>
        </div>

        <!-- Pilar 2: Relevansi Tugas & Proyek -->
        <div class="pillar-card">
            <div>
                <div class="pillar-card-top">
                    <span class="pillar-label" style="color: #0641CC;">Relevansi Tugas Proyek</span>
                    <div class="pillar-icon-badge" style="background: #e0e7ff; color: #0641CC;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                </div>

                <div class="pillar-score-row">
                    <span class="pillar-score" style="color: #0641CC;">{{ $avgTugas }}</span>
                    <span class="macro-card-denom">/ 5.0</span>
                </div>

                <div class="pillar-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $i <= round($avgTugas) ? '#F59E0B' : '#E2E8F0' }}" stroke="none">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    @endfor
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700, #334155); margin-left: 4px;">
                        ({{ $avgTugas > 0 ? round(($avgTugas / 5) * 100) : 0 }}%)
                    </span>
                </div>
            </div>

            <p class="pillar-desc">
                Kesesuaian kurikulum kejuruan & tantangan teknis proyek kedinasan.
            </p>
        </div>

        <!-- Pilar 3: Sarana & Fasilitas Kerja -->
        <div class="pillar-card">
            <div>
                <div class="pillar-card-top">
                    <span class="pillar-label" style="color: #059669;">Fasilitas & Ruang Kerja</span>
                    <div class="pillar-icon-badge" style="background: #d1fae5; color: #059669;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    </div>
                </div>

                <div class="pillar-score-row">
                    <span class="pillar-score" style="color: #059669;">{{ $avgFasilitas }}</span>
                    <span class="macro-card-denom">/ 5.0</span>
                </div>

                <div class="pillar-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $i <= round($avgFasilitas) ? '#F59E0B' : '#E2E8F0' }}" stroke="none">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    @endfor
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700, #334155); margin-left: 4px;">
                        ({{ $avgFasilitas > 0 ? round(($avgFasilitas / 5) * 100) : 0 }}%)
                    </span>
                </div>
            </div>

            <p class="pillar-desc">
                Kenyamanan sarana kerja dinas, kehandalan jaringan, & fasilitas penunjang.
            </p>
        </div>
    </div>

    {{-- 4. Daftar Evaluasi & Testimoni Peserta --}}
    <div class="evaluasi-table-card">
        <div class="evaluasi-header-row">
            <div>
                <h3 class="evaluasi-title">Daftar Evaluasi Kualitatif & Testimoni Alumni</h3>
                <p class="evaluasi-subtitle">Masukan terbuka dan saran perbaikan mutu dinas secara berkelanjutan.</p>
            </div>
            <span style="font-size: 0.75rem; font-weight: 700; color: #0641CC; background: #e0e7ff; padding: 4px 12px; border-radius: 999px;">
                Total: {{ number_format($totalResponden) }} Ulasan
            </span>
        </div>

        @if($daftarSurvei->isEmpty())
            <div class="survei-empty-state">
                <div class="survei-empty-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0 0 0.35rem 0;">
                    Belum Ada Pengisian Survei Kepuasan
                </h4>
                <p style="font-size: 0.85rem; color: var(--neutral-600, #64748B); max-width: 480px; margin: 0 auto; line-height: 1.5;">
                    Data evaluasi alumni akan muncul secara otomatis saat peserta menyelesaikan masa magang dan mengisi kuesioner mutu pada portal peserta.
                </p>
            </div>
        @else
            <div style="overflow-x: auto;" class="clean-scrollbar">
                <table class="evaluasi-table">
                    <thead>
                        <tr>
                            <th style="min-width: 200px;">Peserta & Institusi</th>
                            <th style="text-align: center; min-width: 90px;">Mentoring</th>
                            <th style="text-align: center; min-width: 90px;">Tugas</th>
                            <th style="text-align: center; min-width: 90px;">Fasilitas</th>
                            <th style="text-align: center; min-width: 80px;">NPS</th>
                            <th style="min-width: 320px; width: 42%;">Masukan & Saran Perbaikan</th>
                            <th style="text-align: right; min-width: 100px;">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarSurvei as $item)
                            @php
                                $peserta = $item->penempatan?->peserta;
                                $namaPeserta = $peserta?->nama ?? $peserta?->name ?? 'Alumni Peserta';
                                $institusi = $peserta?->profil?->institusi ?? 'Institusi Pendidikan';
                                $initials = collect(explode(' ', $namaPeserta))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            @endphp
                            <tr>
                                <td>
                                    <div class="mentee-profile-cell">
                                        <div class="mentee-avatar-circle">{{ $initials }}</div>
                                        <div>
                                            <strong style="color: var(--neutral-900, #0F172A); display: block; font-size: 0.875rem;">
                                                {{ $namaPeserta }}
                                            </strong>
                                            <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                                {{ $institusi }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td style="text-align: center;">
                                    <span class="pill-score" style="background: #f3e8ff; color: #7E22CE;">
                                        {{ $item->skor_mentoring }}/5
                                    </span>
                                </td>

                                <td style="text-align: center;">
                                    <span class="pill-score" style="background: #e0e7ff; color: #0641CC;">
                                        {{ $item->skor_tugas }}/5
                                    </span>
                                </td>

                                <td style="text-align: center;">
                                    <span class="pill-score" style="background: #d1fae5; color: #059669;">
                                        {{ $item->skor_fasilitas }}/5
                                    </span>
                                </td>

                                <td style="text-align: center;">
                                    @if($item->skor_nps >= 9)
                                        <span class="pill-score" style="background: #d1fae5; color: #065f46;" title="Promoter (9-10)">
                                            {{ $item->skor_nps }}/10
                                        </span>
                                    @elseif($item->skor_nps >= 7)
                                        <span class="pill-score" style="background: #fef3c7; color: #92400e;" title="Passive (7-8)">
                                            {{ $item->skor_nps }}/10
                                        </span>
                                    @else
                                        <span class="pill-score" style="background: #fee2e2; color: #991b1b;" title="Detractor (0-6)">
                                            {{ $item->skor_nps }}/10
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($item->umpan_balik)
                                        <div class="feedback-bubble">
                                            "{{ $item->umpan_balik }}"
                                        </div>
                                    @endif
                                    @if($item->saran)
                                        <div class="suggestion-box">
                                            <span>💡</span>
                                            <span><strong>Saran:</strong> {{ $item->saran }}</span>
                                        </div>
                                    @endif
                                    @if(!$item->umpan_balik && !$item->saran)
                                        <span style="font-size: 0.775rem; color: var(--neutral-400, #94A3B8); font-style: italic;">
                                            Tidak ada catatan kualitatif.
                                        </span>
                                    @endif
                                </td>

                                <td style="text-align: right; color: var(--neutral-600, #64748B); white-space: nowrap; font-size: 0.8rem;">
                                    {{ $item->created_at?->translatedFormat('d M Y') ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($daftarSurvei->hasPages())
                <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--neutral-200, #E2E8F0);">
                    {{ $daftarSurvei->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
