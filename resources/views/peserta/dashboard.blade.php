@extends('layouts.peserta')

@section('title', 'Dashboard Peserta')
@section('page_title', 'Dashboard Peserta')

@push('styles')
<style>
    /* ==========================================================================
       Dashboard Peserta: Modern Clean SaaS (Linear / Shadcn UI Inspired)
       Desain Bersih, Rapi, Simetris & Selaras Penuh dengan Landing Page Baru
       ========================================================================== */

    .saas-dashboard-workspace {
        font-family: var(--font-sans, 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif);
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 100%;
        color: var(--neutral-800, #1e293b);
    }

    .saas-dashboard-workspace h1,
    .saas-dashboard-workspace h2,
    .saas-dashboard-workspace h3,
    .saas-dashboard-workspace h4,
    .saas-dashboard-workspace h5,
    .saas-font-display {
        font-family: var(--font-display, 'Lexend', sans-serif) !important;
        letter-spacing: -0.01em;
    }

    /* Grid Cockpit 2-Kolom (Main Content + Aside Context) */
    .saas-cockpit-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 24px;
        align-items: start;
        width: 100%;
    }

    .saas-cockpit-main {
        display: flex;
        flex-direction: column;
        gap: 24px;
        min-width: 0;
    }

    .saas-cockpit-aside {
        display: flex;
        flex-direction: column;
        gap: 20px;
        position: sticky;
        top: 24px;
    }

    /* Card Dasar Clean SaaS */
    .saas-card {
        background: #ffffff;
        border: 1px solid var(--neutral-200, #e2e8f0);
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03), 0 6px 18px -4px rgba(15, 23, 42, 0.03);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }

    .saas-card:hover {
        border-color: #cbd5e1;
    }

    .saas-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--neutral-150, #f1f5f9);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .saas-card-body {
        padding: 24px;
    }

    /* Tombol Aksi Terstandarisasi */
    .saas-btn-primary {
        background: #1b3860;
        color: #ffffff !important;
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.84rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #162e4f;
        box-shadow: 0 2px 6px rgba(27, 56, 96, 0.18);
        transition: all 0.15s ease;
    }

    .saas-btn-primary:hover {
        background: #162e4f;
        box-shadow: 0 4px 12px rgba(27, 56, 96, 0.25);
        transform: translateY(-1px);
    }

    .saas-btn-secondary {
        background: #ffffff;
        color: var(--neutral-700, #334155) !important;
        padding: 9px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.84rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid var(--neutral-200, #e2e8f0);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        transition: all 0.15s ease;
    }

    .saas-btn-secondary:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: var(--neutral-900, #0f172a) !important;
    }

    /* Stepper Modern untuk Fase Onboarding */
    .saas-stepper {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .saas-step-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 16px;
        border-radius: 12px;
        border: 1px solid var(--neutral-200, #e2e8f0);
        background: #ffffff;
        transition: all 0.15s ease;
    }

    .saas-step-item.active {
        border-color: #93c5fd;
        background: #eff6ff;
    }

    .saas-step-item.completed {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .saas-step-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
        background: var(--neutral-200, #e2e8f0);
        color: var(--neutral-700, #334155);
    }

    .saas-step-item.active .saas-step-badge {
        background: #2563eb;
        color: #ffffff;
    }

    .saas-step-item.completed .saas-step-badge {
        background: #16a34a;
        color: #ffffff;
    }

    /* Info Strip Key-Value Modern */
    .saas-info-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
    }

    .saas-info-item span {
        display: block;
        font-size: 0.75rem;
        color: var(--neutral-500, #64748b);
        margin-bottom: 3px;
    }

    .saas-info-item strong {
        font-size: 0.875rem;
        color: var(--neutral-900, #0f172a);
        font-weight: 700;
    }

    /* ==========================================================================
       Dark Mode Harmonization (Selaras Penuh dengan Slate/Navy Portal Peserta)
       ========================================================================== */
    [data-theme="dark"] .saas-dashboard-workspace {
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .saas-dashboard-workspace h1,
    [data-theme="dark"] .saas-dashboard-workspace h2,
    [data-theme="dark"] .saas-dashboard-workspace h3,
    [data-theme="dark"] .saas-dashboard-workspace h4,
    [data-theme="dark"] .saas-dashboard-workspace h5,
    [data-theme="dark"] .saas-font-display {
        color: #F8FAFC !important;
    }

    /* Cards & Header */
    [data-theme="dark"] .saas-card {
        background: #182234 !important;
        border-color: #26354D !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.3) !important;
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .saas-card:hover {
        border-color: #3b82f6 !important;
    }

    [data-theme="dark"] .saas-card-header {
        border-bottom-color: #26354D !important;
    }

    /* Buttons */
    [data-theme="dark"] .saas-btn-primary {
        background: #2563EB !important;
        border-color: #1D4ED8 !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35) !important;
        color: #FFFFFF !important;
    }

    [data-theme="dark"] .saas-btn-primary:hover {
        background: #1D4ED8 !important;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45) !important;
    }

    [data-theme="dark"] .saas-btn-secondary {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .saas-btn-secondary:hover {
        background: #1E293B !important;
        border-color: #60A5FA !important;
        color: #F8FAFC !important;
    }

    /* Header Welcome Card */
    [data-theme="dark"] .saas-header-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-header-desc {
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .saas-header-desc strong {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-header-date {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .saas-badge-success {
        background: rgba(6, 78, 59, 0.45) !important;
        color: #34D399 !important;
        border-color: rgba(52, 211, 153, 0.35) !important;
    }

    [data-theme="dark"] .saas-dot-success {
        background: #34D399 !important;
    }

    [data-theme="dark"] .saas-badge-portal {
        background: rgba(30, 58, 138, 0.45) !important;
        color: #60A5FA !important;
        border-color: rgba(96, 165, 250, 0.35) !important;
    }

    [data-theme="dark"] .saas-header-periode {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .saas-header-periode strong {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-link-sk {
        color: #60A5FA !important;
    }

    [data-theme="dark"] .saas-link-sk:hover {
        background: rgba(37, 99, 235, 0.15) !important;
    }

    /* KPI Bar */
    [data-theme="dark"] .saas-kpi-card {
        background: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .saas-kpi-card:hover {
        border-color: #3B82F6 !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45) !important;
    }

    [data-theme="dark"] .saas-kpi-label {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .saas-kpi-value {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-kpi-unit {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .saas-kpi-subtext {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .saas-kpi-icon-blue {
        background: rgba(37, 99, 235, 0.22) !important;
        color: #60A5FA !important;
    }

    [data-theme="dark"] .saas-kpi-icon-green {
        background: rgba(16, 185, 129, 0.22) !important;
        color: #34D399 !important;
    }

    [data-theme="dark"] .saas-kpi-icon-amber {
        background: rgba(245, 158, 11, 0.22) !important;
        color: #FBBF24 !important;
    }

    [data-theme="dark"] .saas-kpi-icon-pink {
        background: rgba(236, 72, 153, 0.22) !important;
        color: #F472B6 !important;
    }

    /* Phase Aktif: Lists & Badges */
    [data-theme="dark"] .saas-card-subtitle {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .saas-link-more {
        color: #60A5FA !important;
    }

    [data-theme="dark"] .saas-list-item {
        border-bottom-color: #26354D !important;
    }

    [data-theme="dark"] .saas-item-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-item-meta {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .saas-item-meta strong {
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .saas-tag-category {
        background: #0F172A !important;
        color: #CBD5E1 !important;
        border: 1px solid #26354D !important;
    }

    [data-theme="dark"] .saas-btn-detail {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .saas-btn-detail:hover {
        background: #1E293B !important;
        border-color: #60A5FA !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-week-tag {
        background: rgba(30, 58, 138, 0.45) !important;
        color: #93C5FD !important;
    }

    [data-theme="dark"] .saas-badge-revisi {
        background: rgba(127, 29, 29, 0.45) !important;
        color: #F87171 !important;
        border-color: rgba(248, 113, 113, 0.35) !important;
    }

    [data-theme="dark"] .saas-badge-approved {
        background: rgba(6, 78, 59, 0.45) !important;
        color: #34D399 !important;
        border-color: rgba(52, 211, 153, 0.35) !important;
    }

    [data-theme="dark"] .saas-badge-submitted {
        background: rgba(30, 58, 138, 0.45) !important;
        color: #60A5FA !important;
        border-color: rgba(96, 165, 250, 0.35) !important;
    }

    [data-theme="dark"] .saas-empty-box {
        background: #0F172A !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .saas-empty-box-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-empty-icon-green {
        background: rgba(16, 185, 129, 0.22) !important;
        color: #34D399 !important;
    }

    [data-theme="dark"] .saas-empty-icon-blue {
        background: rgba(37, 99, 235, 0.22) !important;
        color: #60A5FA !important;
    }

    [data-theme="dark"] .saas-alert-revisi {
        background: rgba(120, 53, 15, 0.35) !important;
        border-color: rgba(180, 83, 9, 0.5) !important;
    }

    [data-theme="dark"] .saas-alert-revisi-text {
        color: #FDE68A !important;
    }

    /* Aside Widget Context */
    [data-theme="dark"] .saas-aside-section-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-aside-avatar {
        background: rgba(37, 99, 235, 0.25) !important;
        color: #93C5FD !important;
        border-color: rgba(59, 130, 246, 0.4) !important;
    }

    [data-theme="dark"] .saas-aside-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-aside-subtitle {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .saas-btn-whatsapp {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #93C5FD !important;
    }

    [data-theme="dark"] .saas-btn-whatsapp:hover {
        background: #1E293B !important;
        border-color: #60A5FA !important;
        color: #FFFFFF !important;
    }

    [data-theme="dark"] .saas-doc-row {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .saas-doc-row:hover {
        background: #1E293B !important;
        border-color: #3B82F6 !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-doc-row span {
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .saas-aside-info-box {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .saas-aside-info-box strong {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-btn-guide {
        background: rgba(30, 58, 138, 0.35) !important;
        border-color: rgba(96, 165, 250, 0.4) !important;
        color: #93C5FD !important;
    }

    [data-theme="dark"] .saas-btn-guide:hover {
        background: rgba(30, 58, 138, 0.55) !important;
        color: #FFFFFF !important;
    }

    /* Onboarding, Seleksi & Selesai */
    [data-theme="dark"] .saas-stepper .saas-step-item {
        background: #0F172A !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .saas-stepper .saas-step-item.active {
        background: rgba(30, 58, 138, 0.3) !important;
        border-color: #3B82F6 !important;
    }

    [data-theme="dark"] .saas-stepper .saas-step-item.completed {
        background: rgba(6, 78, 59, 0.25) !important;
        border-color: rgba(52, 211, 153, 0.45) !important;
    }

    [data-theme="dark"] .saas-step-badge {
        background: #1E293B !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .saas-step-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-step-desc {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .saas-info-strip {
        background: #0F172A !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .saas-info-item span {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .saas-info-item strong {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-divider {
        border-top-color: #26354D !important;
    }

    [data-theme="dark"] .saas-alert-catatan {
        background: rgba(120, 53, 15, 0.35) !important;
        border-color: rgba(180, 83, 9, 0.5) !important;
    }

    [data-theme="dark"] .saas-alert-catatan h5 {
        color: #FCD34D !important;
    }

    [data-theme="dark"] .saas-alert-catatan p {
        color: #FDE68A !important;
    }

    [data-theme="dark"] .saas-selesai-banner {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .saas-predikat-card {
        background: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .saas-evaluasi-box {
        background: rgba(30, 58, 138, 0.25) !important;
        border-color: rgba(96, 165, 250, 0.35) !important;
    }

    [data-theme="dark"] .saas-evaluasi-box strong {
        color: #93C5FD !important;
    }

    [data-theme="dark"] .saas-evaluasi-box p {
        color: #BFDBFE !important;
    }

    /* Modal Slide Drawer */
    .cv-slide-drawer {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: min(560px, 100vw);
        background: #ffffff;
        box-shadow: -10px 0 30px rgba(0, 0, 0, 0.15);
        z-index: 1050;
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
    }

    .cv-slide-drawer.active {
        transform: translateX(0);
    }

    .cv-drawer-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--neutral-150, #f1f5f9);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
    }

    .cv-drawer-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--neutral-900, #0f172a);
    }

    .cv-drawer-close {
        background: none;
        border: none;
        font-size: 1.25rem;
        line-height: 1;
        color: #64748b;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 6px;
        transition: all 0.15s ease;
    }

    .cv-drawer-close:hover {
        background: #e2e8f0;
    }

    .cv-drawer-body {
        padding: 1.5rem;
        overflow-y: auto;
        flex: 1;
    }

    .cv-drawer-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--neutral-150, #f1f5f9);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        background: #f8fafc;
    }

    [data-theme="dark"] .cv-slide-drawer {
        background: #182234 !important;
        color: #E2E8F0 !important;
        box-shadow: -10px 0 30px rgba(0, 0, 0, 0.6) !important;
    }

    [data-theme="dark"] .cv-drawer-header {
        background: #0F172A !important;
        border-bottom-color: #26354D !important;
    }

    [data-theme="dark"] .cv-drawer-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .cv-drawer-close {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .cv-drawer-close:hover {
        background: #1E293B !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .cv-drawer-body {
        background: #182234 !important;
    }

    [data-theme="dark"] .cv-drawer-footer {
        background: #0F172A !important;
        border-top-color: #26354D !important;
    }

    [data-theme="dark"] .cv-slide-drawer label {
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .cv-slide-drawer input[type="date"],
    [data-theme="dark"] .cv-slide-drawer input[type="url"],
    [data-theme="dark"] .cv-slide-drawer textarea {
        background: #0F172A !important;
        border-color: #3E516E !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .cv-slide-drawer input:focus,
    [data-theme="dark"] .cv-slide-drawer textarea:focus {
        border-color: #2563EB !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3) !important;
    }

    /* Responsive Breakpoints */
    @media (max-width: 1024px) {
        .saas-cockpit-grid {
            grid-template-columns: 1fr;
        }

        .saas-cockpit-aside {
            position: static;
        }
    }
</style>
@endpush

@section('content')
<div class="saas-dashboard-workspace">

    {{-- 1. Clean Welcome Card Header --}}
    @include('peserta.dashboard.partials._header')

    {{-- 2. Four-Metric KPI Bar (Full Width) --}}
    @if($fase === 'aktif' && $penempatan)
        @include('peserta.dashboard.partials._kpi_bar')
    @endif

    {{-- 3. Cockpit 2-Column Grid (Main Content + Aside) --}}
    <div class="saas-cockpit-grid">

        {{-- Kolom Utama Berdasarkan Fase --}}
        <div class="saas-cockpit-main">
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

        {{-- Kolom Samping (Konteks & Berkas) --}}
        <aside class="saas-cockpit-aside cv-aside-sticky" id="dashboardAsideContext">
            @include('peserta.dashboard.partials._aside')
        </aside>

    </div>

    {{-- Drawer Modal Input Logbook Kilat --}}
    @include('peserta.dashboard.partials._modal_quick_logbook')

</div>
@endsection
