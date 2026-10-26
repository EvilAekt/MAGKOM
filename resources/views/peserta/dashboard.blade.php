@extends('layouts.peserta')

@section('title', 'Dashboard Peserta')
@section('page_title', 'Dashboard Peserta')

@push('styles')
<style>
    /* Dashboard Hero Banner (Clearview Atmospheric DNA) */
    .dashboard-hero {
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='600' height='300' viewBox='0 0 600 300' fill='none' stroke='white' stroke-opacity='.09' stroke-width='1.2'%3E%3Cpath d='M-20 220C80 180 120 250 220 200S380 90 480 130 620 60 640 40'/%3E%3Cpath d='M-20 250C90 210 140 280 240 230S390 120 500 160 630 90 650 70'/%3E%3Cpath d='M-20 190C70 150 110 220 200 170S370 60 460 100 610 30 630 10'/%3E%3Cpath d='M-20 280C100 240 160 300 260 260S400 150 520 190 640 120 660 100'/%3E%3Cpath d='M-20 160C60 120 100 190 180 140S360 30 440 70 600 0 620-20'/%3E%3Cellipse cx='470' cy='120' rx='60' ry='26'/%3E%3Cellipse cx='470' cy='120' rx='34' ry='14'/%3E%3Cellipse cx='470' cy='120' rx='12' ry='5'/%3E%3C/svg%3E") right -40px top -20px / 720px auto no-repeat,
                    radial-gradient(80% 120% at 100% 0%, rgba(7, 81, 255, 0.4), transparent 60%),
                    linear-gradient(160deg, #06318C 0%, #061E5B 100%);
        border-radius: var(--radius-lg);
        padding: 32px 36px;
        color: #ffffff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 24px -4px rgba(6, 49, 140, 0.35);
        min-height: 180px;
    }

    .dashboard-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        pointer-events: none;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.22);
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
        border-left: 4px solid #F59E0B;
        border-radius: var(--radius-md);
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
    }

    .attention-title {
        font-size: var(--text-sm);
        font-weight: var(--fw-bold);
        color: #92400E;
        margin-right: 6px;
    }

    .attention-desc {
        font-size: var(--text-sm);
        color: #B45309;
    }

    .btn-attention-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #F59E0B;
        color: #ffffff;
        padding: 8px 16px;
        border-radius: var(--radius-default);
        font-size: var(--text-xs);
        font-weight: var(--fw-bold);
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-attention-action:hover {
        background: #D97706;
        color: #ffffff;
        transform: translateY(-1px);
    }

    /* Stepper List (Fase 1 - Onboarding) */
    .stepper-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .step-item {
        background: var(--neutral-100);
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-default);
        padding: 18px 20px;
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
        background: var(--blue-tint);
        border-color: var(--blue-tint-line);
        box-shadow: 0 2px 8px rgba(7, 81, 255, 0.1);
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

    /* Item Row & Deadline Pill */
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

    .micro-progress-bar {
        height: 6px;
        background: var(--neutral-200);
        border-radius: 999px;
        overflow: hidden;
        margin-top: 6px;
        width: 100%;
    }

    .micro-progress-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.3s ease;
    }

    .progress-fill-blue { background: var(--blue-600); }
    .progress-fill-green { background: var(--green-600); }
    .progress-fill-amber { background: #D97706; }
    .progress-fill-purple { background: #7E22CE; }

    .completion-grade-box {
        text-align: right;
        background: var(--neutral-0);
        border: 1px solid var(--neutral-300);
        border-radius: var(--radius-md);
        padding: 16px 24px;
    }

    .completion-grade-score {
        font-size: 36px;
        font-weight: 800;
        color: var(--green-700);
        line-height: 1.1;
    }

    .completion-notes-box {
        margin-top: 20px;
        padding: 16px;
        background: var(--neutral-0);
        border: 1px solid var(--neutral-200);
        border-left: 4px solid var(--green-600);
        border-radius: var(--radius-default);
    }

    .dashboard-empty-state {
        text-align: center;
        padding: 36px 16px;
        color: var(--neutral-500);
        margin: auto 0;
    }

    .dashboard-empty-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--neutral-800);
        margin: 0 0 4px 0;
    }

    .dashboard-empty-subtext {
        font-size: 12px;
        color: var(--neutral-500);
        margin: 0;
    }

    /* Dark Mode Harmonization */
    [data-theme="dark"] .dashboard-hero {
        background: linear-gradient(95deg, rgba(15, 23, 42, 0.94) 0%, rgba(30, 58, 138, 0.88) 50%, rgba(6, 65, 204, 0.45) 100%),
                    url('{{ asset('images/dashboard_hero_bg.jpg') }}') right center / cover no-repeat;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.35);
        border: 1px solid rgba(59, 130, 246, 0.25);
    }
    [data-theme="dark"] .hero-role-pill {
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff;
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
    [data-theme="dark"] .step-title {
        color: #F8FAFC;
    }
    [data-theme="dark"] .step-desc {
        color: #94A3B8;
    }
    [data-theme="dark"] .info-strip {
        background: #0F172A;
        border: 1px solid #26354D;
    }
    [data-theme="dark"] .info-strip-item span {
        color: #94A3B8;
    }
    [data-theme="dark"] .info-strip-item strong {
        color: #F8FAFC;
    }
    [data-theme="dark"] .item-row {
        border-bottom-color: #26354D;
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
    [data-theme="dark"] .deadline-pill.is-urgent {
        background: rgba(239, 68, 68, 0.2);
        color: #F87171;
        border-color: rgba(239, 68, 68, 0.4);
    }
    [data-theme="dark"] .deadline-pill.is-warning {
        background: rgba(245, 158, 11, 0.2);
        color: #FBBF24;
        border-color: rgba(245, 158, 11, 0.4);
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
    [data-theme="dark"] .completion-grade-box {
        background: #182234 !important;
        border-color: #26354D !important;
    }
    [data-theme="dark"] .completion-grade-score {
        color: #34D399 !important;
    }
    [data-theme="dark"] .completion-notes-box {
        background: #0F172A !important;
        border-color: #26354D !important;
        border-left-color: #34D399 !important;
    }
</style>
@endpush

@section('content')
{{-- =========================================================================
   Gentelella Workspace Dashboard - Peserta Magang Dinas Kominfo Sleman
   Standar Visual Gentelella Theme (Zero-Regression & Adaptive Responsive)
   ========================================================================= --}}
<div class="gentelella-dashboard-workspace" style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">

    {{-- 1. Header & Atmospheric Hero Greeting Banner --}}
    @include('peserta.dashboard.partials._header')

    {{-- 2. Cockpit 2-Column Asymmetrical Grid (Main 1fr + Sticky Aside 340px) --}}
    <div class="cv-cockpit-layout" style="width: 100%;">

        {{-- Kolom Utama: Berdasarkan Fase Magang --}}
        <div class="cv-cockpit-main" style="display: flex; flex-direction: column; gap: 1.5rem; min-width: 0;">
            @if($fase === 'onboarding')
                @include('peserta.dashboard.partials._phase_onboarding')
            @elseif($fase === 'seleksi')
                @include('peserta.dashboard.partials._phase_seleksi')
            @elseif($fase === 'aktif')
                @include('peserta.dashboard.partials._phase_aktif')
            @elseif($fase === 'selesai')
                @include('peserta.dashboard.partials._phase_selesai')
            @endif
        </div>

        {{-- Kolom Samping: Sticky Aside 340px --}}
        <aside class="cv-aside-sticky">
            @include('peserta.dashboard.partials._aside')
        </aside>

    </div>

    {{-- Slide-Over Drawer: Input Logbook Kilat --}}
    @include('peserta.dashboard.partials._modal_quick_logbook')

</div>
@endsection
