@extends('layouts.peserta')

@section('title', 'Dashboard Peserta')
@section('page_title', 'Dashboard Peserta')

@push('styles')
<style>
    /* Dashboard Hero Banner */
    .dashboard-hero {
        background: linear-gradient(95deg, rgba(6, 65, 204, 0.96) 0%, rgba(6, 65, 204, 0.88) 45%, rgba(6, 65, 204, 0.35) 100%),
                    url('{{ asset('images/dashboard_hero_bg.jpg') }}') right center / cover no-repeat;
        border-radius: var(--radius-lg);
        padding: 32px 36px;
        color: #ffffff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(6, 65, 204, 0.16);
        min-height: 180px;
    }

    .dashboard-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 50%, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .hero-greeting h1 {
        font-size: var(--text-2xl);
        font-weight: var(--fw-bold);
        color: #ffffff;
        letter-spacing: -0.01em;
        margin-bottom: 6px;
        line-height: 1.25;
    }

    .hero-placement-title {
        font-size: var(--text-xl);
        font-weight: var(--fw-bold);
        color: #ffffff;
        margin: 4px 0 10px 0;
        line-height: 1.3;
    }

    .hero-greeting p {
        font-size: var(--text-base);
        color: rgba(255, 255, 255, 0.92);
        max-width: 600px;
        line-height: 1.5;
    }

    .hero-meta-badges {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }

    .hero-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(6px);
        padding: 5px 12px;
        border-radius: var(--radius-full);
        font-size: var(--text-xs);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.22);
    }

    .hero-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background: #10B981;
        color: #ffffff;
        border-radius: var(--radius-full);
        font-size: var(--text-2xs);
        font-weight: var(--fw-bold);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .hero-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
    }

    .hero-date-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        padding: 6px 14px;
        border-radius: var(--radius-full);
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    .hero-glass-btn {
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff;
        padding: 8px 16px;
        border-radius: var(--radius-full);
        font-size: var(--text-xs);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .hero-glass-btn:hover {
        background: rgba(255, 255, 255, 0.28);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.6);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .hero-role-pill {
        display: inline-block;
        padding: 4px 12px;
        background: #ffffff;
        color: var(--blue-700);
        border-radius: var(--radius-full);
        font-size: var(--text-2xs);
        font-weight: var(--fw-bold);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Attention Banner (Catatan Perhatian) */
    .attention-banner {
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        border-left: 4px solid #D97706;
        border-radius: var(--radius-md);
        padding: 14px 20px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(217, 119, 6, 0.08);
    }

    .btn-attention-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #D97706;
        color: #ffffff;
        padding: 6px 14px;
        border-radius: var(--radius-full);
        font-size: var(--text-xs);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-attention-action:hover {
        background: #B45309;
        color: #ffffff;
    }

    /* Quick Action Buttons */
    .quick-actions-bar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        margin-bottom: 22px;
        flex-wrap: wrap;
    }

    /* KPI Unified Strip Card (Panel Metrik Terpadu) */
    .kpi-unified-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-200);
        border-radius: var(--radius-md);
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        margin-bottom: 28px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .kpi-unified-item {
        padding: 18px 20px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        position: relative;
        transition: background-color 0.15s ease;
    }

    .kpi-unified-item:hover {
        background-color: var(--neutral-100);
    }

    .kpi-unified-item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 15%;
        height: 70%;
        width: 1px;
        background-color: var(--neutral-200);
    }

    .kpi-icon-box {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-default);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .kpi-icon-blue { background: var(--blue-tint); color: var(--blue-600); }
    .kpi-icon-green { background: var(--green-100); color: var(--green-600); }
    .kpi-icon-amber { background: var(--amber-100); color: var(--amber-700); }
    .kpi-icon-purple { background: #F3E8FF; color: #7E22CE; }

    .kpi-details {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
    }

    .kpi-label {
        font-size: var(--text-xs);
        font-weight: var(--fw-semibold);
        color: var(--neutral-600);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 2px;
    }

    .kpi-value {
        font-size: var(--text-xl);
        font-weight: var(--fw-bold);
        color: var(--neutral-900);
        line-height: 1.25;
    }

    .micro-progress-bar {
        height: 5px;
        background: var(--neutral-200);
        border-radius: 999px;
        overflow: hidden;
        margin: 6px 0 4px 0;
    }

    .micro-progress-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.3s ease;
    }

    .kpi-subtext {
        font-size: 11px;
        color: var(--neutral-500);
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Onboarding Card */
    .onboarding-card {
        background: var(--neutral-0);
        border: 1px solid var(--blue-tint-line);
        border-radius: var(--radius-md);
        padding: 28px;
        margin-bottom: 28px;
        box-shadow: 0 2px 8px rgba(7, 81, 255, 0.06);
    }

    .stepper-list {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin: 24px 0 20px 0;
    }

    .step-item {
        background: var(--neutral-100);
        border: 1px solid var(--neutral-200);
        border-radius: var(--radius-default);
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
    }

    .step-item.completed {
        background: #F0FDF4;
        border-color: #BBF7D0;
    }

    .step-item.active {
        background: #EFF6FF;
        border-color: #BFDBFE;
    }

    .step-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--text-xs);
        font-weight: var(--fw-bold);
        background: var(--neutral-400);
        color: #ffffff;
    }

    .step-item.completed .step-badge { background: var(--green-600); }
    .step-item.active .step-badge { background: var(--blue-600); }

    .step-title {
        font-size: var(--text-base);
        font-weight: var(--fw-semibold);
        color: var(--neutral-800);
        line-height: 1.3;
    }

    .step-desc {
        font-size: var(--text-xs);
        color: var(--neutral-600);
        line-height: 1.4;
    }

    /* Phase Selection Tracker */
    .status-tracker-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-400);
        border-radius: var(--radius-md);
        padding: 24px;
        margin-bottom: 28px;
    }

    .tracker-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .info-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        background: var(--neutral-100);
        padding: 16px;
        border-radius: var(--radius-default);
        margin-bottom: 16px;
    }

    .info-strip-item span {
        font-size: var(--text-xs);
        color: var(--neutral-500);
        display: block;
    }

    .info-strip-item strong {
        font-size: var(--text-base);
        color: var(--neutral-800);
        font-weight: var(--fw-semibold);
    }

    /* Two Column Content Grid */
    .dashboard-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }

    .list-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-md);
        padding: 20px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        height: 100%;
    }

    .list-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--neutral-200);
    }

    .list-card-title {
        font-size: var(--text-base);
        font-weight: var(--fw-bold);
        color: var(--neutral-900);
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.3;
        margin: 0;
    }

    .item-row {
        padding: 12px 0;
        border-bottom: 1px solid var(--neutral-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        transition: background-color 0.15s ease;
    }

    .item-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .item-meta {
        flex: 1;
        min-width: 0;
    }

    .item-meta h4 {
        font-size: var(--text-sm);
        font-weight: var(--fw-semibold);
        color: var(--neutral-900);
        margin-bottom: 4px;
        line-height: 1.35;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .item-meta p {
        font-size: 12px;
        color: var(--neutral-500);
        line-height: 1.4;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .deadline-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        background: var(--neutral-100);
        color: var(--neutral-600);
        border: 1px solid var(--neutral-300);
        white-space: nowrap;
    }

    .deadline-pill.is-urgent {
        background: #FEF2F2;
        color: #DC2626;
        border-color: #FECACA;
    }

    .deadline-pill.is-warning {
        background: #FFFBEB;
        color: #D97706;
        border-color: #FDE68A;
    }

    .week-mini-badge {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: var(--blue-tint);
        color: var(--blue-700);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
        border: 1px solid rgba(7, 81, 255, 0.18);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .kpi-unified-card { grid-template-columns: repeat(2, 1fr); }
        .kpi-unified-item:nth-child(2)::after { display: none; }
        .kpi-unified-item:nth-child(1),
        .kpi-unified-item:nth-child(2) { border-bottom: 1px solid var(--neutral-200); }
        .dashboard-columns { grid-template-columns: 1fr; }
        .stepper-list { grid-template-columns: 1fr; }
        .hero-meta { align-items: flex-start; }
    }

    @media (max-width: 640px) {
        .kpi-unified-card { grid-template-columns: 1fr; }
        .kpi-unified-item::after { display: none; }
        .kpi-unified-item:not(:last-child) { border-bottom: 1px solid var(--neutral-200); }
    }

    /* Dark Mode Harmonization ala Menu Tugas */
    [data-theme="dark"] .kpi-unified-card {
        background: #182234;
        border-color: #26354D;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
    }

    [data-theme="dark"] .kpi-unified-item:hover {
        background-color: #1E293B;
    }

    [data-theme="dark"] .kpi-unified-item:not(:last-child)::after {
        background-color: #26354D;
    }

    [data-theme="dark"] .kpi-label {
        color: #94A3B8;
    }

    [data-theme="dark"] .kpi-value {
        color: #F8FAFC;
    }

    [data-theme="dark"] .micro-progress-bar {
        background: #26354D;
    }

    [data-theme="dark"] .kpi-subtext {
        color: #94A3B8;
    }

    [data-theme="dark"] .kpi-icon-blue {
        background: rgba(6, 65, 204, 0.35);
        border: 1px solid rgba(7, 81, 255, 0.4);
        color: #60A5FA;
    }

    [data-theme="dark"] .kpi-icon-green {
        background: rgba(16, 185, 129, 0.25);
        border: 1px solid rgba(34, 197, 94, 0.4);
        color: #34D399;
    }

    [data-theme="dark"] .kpi-icon-amber {
        background: rgba(217, 119, 6, 0.25);
        border: 1px solid rgba(245, 158, 11, 0.4);
        color: #FBBF24;
    }

    [data-theme="dark"] .kpi-icon-purple {
        background: rgba(147, 51, 234, 0.25);
        border: 1px solid rgba(168, 85, 247, 0.4);
        color: #C084FC;
    }

    [data-theme="dark"] .onboarding-card {
        background: #182234;
        border-color: #26354D;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
    }

    [data-theme="dark"] .step-item {
        background: #0F172A;
        border-color: #26354D;
        color: #CBD5E1;
    }

    [data-theme="dark"] .step-item.completed {
        background: rgba(6, 78, 59, 0.25);
        border-color: rgba(52, 211, 153, 0.4);
    }

    [data-theme="dark"] .step-item.active {
        background: rgba(30, 58, 138, 0.25);
        border-color: rgba(96, 165, 250, 0.4);
    }

    [data-theme="dark"] .list-card {
        background: #182234;
        border-color: #26354D;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
    }

    [data-theme="dark"] .list-card-header {
        border-bottom-color: #26354D;
    }

    [data-theme="dark"] .list-card-title {
        color: #F8FAFC;
    }

    [data-theme="dark"] .item-row {
        border-bottom-color: #26354D;
    }

    [data-theme="dark"] .item-row:hover {
        background-color: rgba(30, 41, 59, 0.4);
    }

    [data-theme="dark"] .item-meta h4 {
        color: #F8FAFC;
    }

    [data-theme="dark"] .item-meta p {
        color: #94A3B8;
    }

    [data-theme="dark"] .deadline-pill {
        background: #1E293B;
        border-color: #334155;
        color: #CBD5E1;
    }

    [data-theme="dark"] .week-mini-badge {
        background: rgba(6, 65, 204, 0.35);
        border-color: rgba(7, 81, 255, 0.4);
        color: #60A5FA;
    }

    [data-theme="dark"] .attention-banner {
        background: rgba(120, 53, 15, 0.35);
        border-color: rgba(245, 158, 11, 0.4);
        border-left-color: #F59E0B;
    }

    [data-theme="dark"] .attention-banner strong {
        color: #FCD34D !important;
    }

    [data-theme="dark"] .attention-banner span {
        color: #FEF3C7 !important;
    }

    [data-theme="dark"] .quick-action-btn {
        background: #182234;
        border-color: #26354D;
        color: #E2E8F0;
    }

    [data-theme="dark"] .quick-action-btn:hover {
        background: #1E293B;
        border-color: #3B82F6;
        color: #60A5FA;
    }
</style>
@endpush

@section('content')

{{-- 1. Hero Greeting Banner --}}
@if($fase === 'aktif' && $penempatan)
    <div class="dashboard-hero">
        <div class="hero-content">
            <div class="hero-greeting">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <span class="hero-status-pill">● Status Penempatan: Magang Aktif</span>
                </div>
                <h1>Selamat Datang, {{ $user->nama ?? $user->name }}! 👋</h1>
                <h2 class="hero-placement-title">{{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika Kabupaten Sleman' }}</h2>
                
                <div class="hero-meta-badges">
                    <div class="hero-meta-chip">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Pembimbing: <strong>{{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? 'Belum Ditugaskan' }}</strong></span>
                    </div>
                    <div class="hero-meta-chip">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Periode: {{ \Carbon\Carbon::parse($penempatan->tgl_mulai ?? $penempatan->tanggal_mulai)->isoFormat('D MMM Y') }} &ndash; {{ \Carbon\Carbon::parse($penempatan->tgl_selesai ?? $penempatan->tanggal_selesai)->isoFormat('D MMM Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="hero-meta">
                <div class="hero-date-badge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
                <a href="{{ route('peserta.penempatan.surat-diterima.pratinjau', $penempatan) }}" class="hero-glass-btn" target="_blank">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-1.943-9.542-7z"></path>
                    </svg>
                    Pratinjau SK Diterima
                </a>
            </div>
        </div>
    </div>

    {{-- Banner Alert Catatan Perhatian --}}
    @if($logbookPerluRevisi > 0)
        <div class="attention-banner">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 20px; line-height: 1;">⚠️</span>
                <div>
                    <strong style="color: #92400E; font-size: 14px;">Tindak Lanjut Diperlukan:</strong>
                    <span style="color: #78350F; font-size: 13px; margin-left: 4px;">
                        Terdapat <strong>{{ $logbookPerluRevisi }}</strong> laporan logbook mingguan yang memerlukan perbaikan dari pembimbing lapangan.
                    </span>
                </div>
            </div>
            <a href="{{ route('peserta.logbook.index') }}" class="btn-attention-action">
                Perbaiki Logbook Sekarang &rarr;
            </a>
        </div>
    @endif
@else
    <div class="dashboard-hero">
        <div class="hero-content">
            <div class="hero-greeting">
                <h1>Selamat Datang, {{ $user->nama ?? $user->name }}! 👋</h1>
                <p>
                    Selamat datang di portal terpadu magang Dinas Komunikasi dan Informatika Kabupaten Sleman. Pantau status pendaftaran, kegiatan harian, tugas, dan evaluasi hasil kerja Anda secara terpusat.
                </p>
            </div>
            <div class="hero-meta">
                <div class="hero-date-badge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
                <span class="hero-role-pill">Peran: Peserta Magang</span>
            </div>
        </div>
    </div>
@endif

{{-- 2. Modul Berdasarkan Fase --}}

{{-- FASE 1: ONBOARDING (Belum Mendaftar / Profil Baru) --}}
@if($fase === 'onboarding')
    <div class="onboarding-card">
        <div class="card-header" style="margin-bottom: 8px;">
            <div>
                <h3 class="card-title">Panduan Alur Pendaftaran Magang</h3>
                <p class="card-subtitle">Ikuti langkah-langkah di bawah ini untuk memulai pendaftaran magang di Dinas Kominfo Sleman.</p>
            </div>
        </div>

        <div class="stepper-list">
            <div class="step-item {{ $profil ? 'completed' : 'active' }}">
                <div class="step-badge">{{ $profil ? '✓' : '1' }}</div>
                <div class="step-title">1. Lengkapi Biodata & Profil</div>
                <div class="step-desc">
                    Isi asal institusi/sekolah, NISN/NIM, jurusan, jenjang, kontak WhatsApp, dan kontak darurat.
                </div>
            </div>

            <div class="step-item {{ $profil ? 'active' : '' }}">
                <div class="step-badge">2</div>
                <div class="step-title">2. Pilih Periode & Kuota</div>
                <div class="step-desc">
                    Pilih bulan magang aktif yang kuotanya masih tersedia sesuai jurusan Anda dan buat draf pengajuan.
                </div>
            </div>

            <div class="step-item">
                <div class="step-badge">3</div>
                <div class="step-title">3. Unggah Berkas & Ajukan</div>
                <div class="step-desc">
                    Unggah surat pengantar, proposal, transkrip nilai, lalu kirim pengajuan untuk diverifikasi admin.
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            @if(!$profil)
                <a href="{{ route('peserta.profil.show') }}" class="btn btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Lengkapi Biodata Sekarang
                </a>
            @else
                <a href="{{ route('peserta.pendaftaran.index') }}" class="btn btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Pengajuan Pendaftaran Magang
                </a>
                <a href="{{ route('peserta.profil.show') }}" class="btn btn-secondary">
                    Periksa Biodata Saya
                </a>
            @endif
        </div>
    </div>
@endif

{{-- FASE 2: SELEKSI PENDAFTARAN (Pendaftaran Aktif Berjalan) --}}
@if($fase === 'seleksi' && $pendaftaran)
    <div class="status-tracker-card">
        <div class="tracker-header">
            <div>
                <h3 class="card-title">Status Pendaftaran Magang Aktif</h3>
                <p class="card-subtitle">Pengajuan nomor #{{ $pendaftaran->id }} sedang diproses dalam tahapan seleksi.</p>
            </div>
            <div>
                @php
                    $status = $pendaftaran->status;
                    $badgeClass = match($status) {
                        \App\Enums\StatusPendaftaranEnum::Diterima => 'badge-success',
                        \App\Enums\StatusPendaftaranEnum::Diajukan, \App\Enums\StatusPendaftaranEnum::WaitingList => 'badge-warning',
                        \App\Enums\StatusPendaftaranEnum::PerluPerbaikan, \App\Enums\StatusPendaftaranEnum::Ditolak, \App\Enums\StatusPendaftaranEnum::Dibatalkan => 'badge-danger',
                        default => 'badge-neutral',
                    };
                @endphp
                <span class="badge {{ $badgeClass }}" style="font-size: 13px; padding: 6px 14px;">
                    ● {{ $status->label() }}
                </span>
            </div>
        </div>

        <div class="info-strip">
            <div class="info-strip-item">
                <span>Periode Magang:</span>
                <strong>{{ $pendaftaran->bulanMagang ? \Carbon\Carbon::parse($pendaftaran->bulanMagang->bulan_mulai)->isoFormat('MMMM Y') : '-' }}</strong>
            </div>
            <div class="info-strip-item">
                <span>Jurusan Terdaftar:</span>
                <strong>{{ $pendaftaran->kuota->jurusan->nama_jurusan ?? '-' }}</strong>
            </div>
            <div class="info-strip-item">
                <span>Durasi:</span>
                <strong>{{ $pendaftaran->durasi_bulan }} Bulan</strong>
            </div>
            <div class="info-strip-item">
                <span>Kelengkapan Berkas:</span>
                <strong>{{ $pendaftaran->dokumen->count() }} Berkas Terunggah</strong>
            </div>
        </div>

        @if($pendaftaran->catatan_revisi)
            <div class="alert alert-warning" style="margin-bottom: 16px;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <strong>Catatan Admin Verifikator:</strong> {{ $pendaftaran->catatan_revisi }}
                </div>
            </div>
        @endif

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('peserta.pendaftaran.detail', $pendaftaran) }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-1.943-9.542-7z"></path>
                </svg>
                Buka Berkas & Detail Pendaftaran
            </a>

            @if($pendaftaran->penempatan)
                <a href="{{ route('peserta.penempatan.surat-diterima', $pendaftaran->penempatan) }}" class="btn btn-secondary" target="_blank">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Unduh Surat Keterangan Diterima (PDF)
                </a>
            @endif
        </div>
    </div>
@endif

{{-- FASE 3: AKTIF MAGANG (Sedang Berjalan) --}}
@if($fase === 'aktif' && $penempatan)
    @php
        $persenTugas = $totalTugas > 0 ? min(100, round(($tugasSelesai / $totalTugas) * 100)) : 0;
        
        $tglMulaiCarbon = $penempatan ? \Carbon\Carbon::parse($penempatan->tgl_mulai ?? $penempatan->tanggal_mulai)->startOfDay() : null;
        $tglSelesaiCarbon = $penempatan ? \Carbon\Carbon::parse($penempatan->tgl_selesai ?? $penempatan->tanggal_selesai)->startOfDay() : null;
        $totalHariMagang = ($tglMulaiCarbon && $tglSelesaiCarbon) ? max(1, $tglMulaiCarbon->diffInDays($tglSelesaiCarbon)) : 1;
        $hariBerjalan = ($tglMulaiCarbon) ? max(0, min($totalHariMagang, (int)$tglMulaiCarbon->diffInDays(now()->startOfDay()))) : 0;
        $persenHariMagang = min(100, round(($hariBerjalan / $totalHariMagang) * 100));
    @endphp

    {{-- Quick Action Bar --}}
    <div class="quick-actions-bar">
        <a href="{{ route('peserta.logbook.index') }}" class="btn btn-primary" style="box-shadow: 0 4px 12px rgba(7, 81, 255, 0.22);">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            + Isi Logbook Mingguan
        </a>

        <a href="{{ route('peserta.tugas.index') }}" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            Lihat Daftar Tugas
        </a>

        <a href="{{ route('peserta.nilai.show') }}" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
            </svg>
            Cek Lembar Nilai
        </a>
    </div>

    {{-- Panel Metrik Terpadu (Unified KPI Card dengan Micro-Progress) --}}
    <div class="kpi-unified-card">
        <div class="kpi-unified-item">
            <div class="kpi-icon-box kpi-icon-blue">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <div class="kpi-details">
                <span class="kpi-label">Tugas Selesai</span>
                <span class="kpi-value">{{ $tugasSelesai }} / {{ $totalTugas }}</span>
                <div class="micro-progress-bar">
                    <div class="micro-progress-fill" style="width: {{ $persenTugas }}%; background: var(--blue-600);"></div>
                </div>
                <span class="kpi-subtext">{{ $persenTugas }}% tuntas &bull; {{ $tugasBelum }} dalam proses</span>
            </div>
        </div>

        <div class="kpi-unified-item">
            <div class="kpi-icon-box kpi-icon-green">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div class="kpi-details">
                <span class="kpi-label">Logbook Mingguan</span>
                <span class="kpi-value">{{ $totalLogbook }} Laporan</span>
                <div class="micro-progress-bar">
                    <div class="micro-progress-fill" style="width: 100%; background: var(--green-600);"></div>
                </div>
                <span class="kpi-subtext">
                    @if($logbookPerluRevisi > 0)
                        <strong style="color: var(--red-600);">{{ $logbookPerluRevisi }} laporan perlu revisi</strong>
                    @else
                        Aktivitas tercatat rapi
                    @endif
                </span>
            </div>
        </div>

        <div class="kpi-unified-item">
            <div class="kpi-icon-box kpi-icon-amber">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="kpi-details">
                <span class="kpi-label">Sisa Hari Magang</span>
                <span class="kpi-value">{{ $sisaHari !== null ? $sisaHari . ' Hari' : '-' }}</span>
                <div class="micro-progress-bar">
                    <div class="micro-progress-fill" style="width: {{ $persenHariMagang }}%; background: #D97706;"></div>
                </div>
                <span class="kpi-subtext">Hari ke-{{ $hariBerjalan }} dari {{ $totalHariMagang }} ({{ $persenHariMagang }}%)</span>
            </div>
        </div>

        <div class="kpi-unified-item">
            <div class="kpi-icon-box kpi-icon-purple">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
            </div>
            <div class="kpi-details">
                <span class="kpi-label">Status Evaluasi</span>
                <span class="kpi-value" style="font-size: 1.125rem;">
                    {{ $isFinalNilai ? ($penilaian->predikat ?? 'A') . ' (' . number_format($penilaian->nilai_akhir, 1) . ')' : 'Dalam Proses' }}
                </span>
                <div class="micro-progress-bar">
                    <div class="micro-progress-fill" style="width: {{ $isFinalNilai ? 100 : 50 }}%; background: #7E22CE;"></div>
                </div>
                <span class="kpi-subtext">{{ $isFinalNilai ? 'Nilai final resmi' : '5 rubrik pembimbing' }}</span>
            </div>
        </div>
    </div>

    {{-- 2 Kolom: Tugas Mendesak & Logbook Terbaru --}}
    <div class="dashboard-columns">
        {{-- Kolom Tugas --}}
        <div class="list-card">
            <div class="list-card-header">
                <h4 class="list-card-title">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--blue-600);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Tugas Magang Mendesak
                </h4>
                <a href="{{ route('peserta.tugas.index') }}" style="font-size: 13px; color: var(--blue-600); text-decoration: none; font-weight: 600;">Lihat Semua &rarr;</a>
            </div>

            @if($tugasMendesak->isNotEmpty())
                @foreach($tugasMendesak as $t)
                    @php
                        $deadlineCarbon = \Carbon\Carbon::parse($t->deadline)->startOfDay();
                        $diffDays = now()->startOfDay()->diffInDays($deadlineCarbon, false);
                    @endphp
                    <div class="item-row">
                        <div class="item-meta">
                            <h4>{{ $t->judul }}</h4>
                            <p style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                                @if($diffDays < 0)
                                    <span class="deadline-pill is-urgent">Lewat Tenggat</span>
                                @elseif($diffDays === 0)
                                    <span class="deadline-pill is-urgent">⚡ Hari Ini</span>
                                @elseif($diffDays === 1)
                                    <span class="deadline-pill is-urgent">⚠️ Besok</span>
                                @elseif($diffDays <= 3)
                                    <span class="deadline-pill is-warning">⏳ {{ $diffDays }} Hari Lagi</span>
                                @else
                                    <span class="deadline-pill">📅 {{ $deadlineCarbon->isoFormat('D MMM') }}</span>
                                @endif
                                <span>&bull; {{ $t->kategori ?? 'Tugas Lapangan' }}</span>
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('peserta.tugas.show', $t) }}" class="btn btn-secondary" style="height: 32px; padding: 0 12px; font-size: 12px; white-space: nowrap;">
                                Kerjakan
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 36px 16px; color: var(--neutral-500); margin: auto 0;">
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px auto; color: var(--green-600);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p style="font-size: 14px; font-weight: 700; color: var(--neutral-800); margin: 0 0 4px 0;">Semua tugas selesai!</p>
                    <p style="font-size: 12px; margin: 0;">Tidak ada tugas yang menunggu pengerjaan saat ini.</p>
                </div>
            @endif
        </div>

        {{-- Kolom Logbook --}}
        <div class="list-card">
            <div class="list-card-header">
                <h4 class="list-card-title">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--blue-600);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Logbook Mingguan Terkini
                </h4>
                <a href="{{ route('peserta.logbook.index') }}" style="font-size: 13px; color: var(--blue-600); text-decoration: none; font-weight: 600;">Lihat Semua &rarr;</a>
            </div>

            @if($logbookTerbaru->isNotEmpty())
                @foreach($logbookTerbaru as $lb)
                    @php
                        $statusValue = $lb->status->value ?? (string)$lb->status;
                    @endphp
                    <div class="item-row">
                        <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                            <span class="week-mini-badge">
                                MG{{ $lb->minggu_ke ?? $loop->iteration }}
                            </span>
                            <div class="item-meta">
                                <h4>Minggu ke-{{ $lb->minggu_ke ?? ($loop->iteration) }} &bull; {{ \Carbon\Carbon::parse($lb->tanggal)->isoFormat('D MMM Y') }}</h4>
                                <p>{{ \Illuminate\Support\Str::limit($lb->kegiatan, 65) }}</p>
                            </div>
                        </div>
                        <div>
                            @if($statusValue === 'perlu_revisi' || $statusValue === 'ditolak')
                                <span class="badge badge-danger">Perlu Revisi</span>
                            @elseif($statusValue === 'disetujui')
                                <span class="badge badge-success">Disetujui</span>
                            @else
                                <span class="badge badge-info">Terkirim</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 36px 16px; color: var(--neutral-500); margin: auto 0;">
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px auto; color: var(--neutral-400);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <p style="font-size: 14px; font-weight: 700; color: var(--neutral-800); margin: 0 0 4px 0;">Belum ada catatan logbook mingguan</p>
                    <p style="font-size: 12px; margin: 0;">Catat aktivitas mingguan magang Anda secara rutin.</p>
                </div>
            @endif
        </div>
    </div>
@endif

{{-- FASE 4: SELESAI MAGANG (Pasca Penilaian / Kelulusan) --}}
@if($fase === 'selesai' && $penempatan)
    <div class="card" style="border: 2px solid var(--green-600); background: #FAFDFB; padding: 32px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
            <div>
                <span class="badge badge-success" style="font-size: 14px; padding: 6px 16px; margin-bottom: 12px;">
                    🎉 Selamat! Masa Magang Telah Selesai
                </span>
                <h2 style="font-size: 22px; font-weight: 800; color: var(--neutral-800); margin-bottom: 8px;">
                    Penyelesaian Program Magang Dinas Kominfo Sleman
                </h2>
                <p style="font-size: 14px; color: var(--neutral-600); max-width: 650px; line-height: 1.5;">
                    Anda telah menuntaskan seluruh kewajiban magang pada <strong>{{ $penempatan->bidang->nama_bidang ?? 'Dinas Kominfo Sleman' }}</strong> bersama pembimbing lapangan <strong>{{ $penempatan->pembimbing->nama ?? $penempatan->pembimbing->name ?? '-' }}</strong>.
                </p>
            </div>

            @if($isFinalNilai)
                <div style="text-align: right; background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: var(--radius-md); padding: 16px 24px;">
                    <span style="font-size: 12px; font-weight: 600; color: var(--neutral-500); text-transform: uppercase;">Predikat Kelulusan</span>
                    <div style="font-size: 36px; font-weight: 800; color: var(--green-700); line-height: 1.1;">
                        {{ $penilaian->predikat ?? 'A' }}
                    </div>
                    <span style="font-size: 13px; color: var(--neutral-600);">Skor: {{ number_format($penilaian->nilai_akhir, 2) }} / 100</span>
                </div>
            @endif
        </div>

        @if($isFinalNilai && $penilaian->catatan)
            <div style="margin-top: 20px; padding: 16px; background: var(--neutral-0); border-left: 4px solid var(--green-600); border-radius: var(--radius-default);">
                <strong style="font-size: 13px; color: var(--neutral-700); display: block; margin-bottom: 4px;">Catatan & Evaluasi Pembimbing:</strong>
                <p style="font-size: 14px; color: var(--neutral-800); font-style: italic;">"{{ $penilaian->catatan }}"</p>
            </div>
        @endif

        <div style="margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('peserta.nilai.show') }}" class="btn btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Lihat Rincian Nilai 5 Rubrik
            </a>

            @if($isFinalNilai)
                <a href="{{ route('peserta.nilai.unduh') }}" class="btn btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh Lembar Nilai Resmi (PDF)
                </a>
            @endif
        </div>
    </div>
@endif

@endsection
