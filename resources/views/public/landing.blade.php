@extends('layouts.app')

@section('title', 'Portal MagKom - Informasi Kuota & Pendaftaran Magang Kominfo')
@section('meta_description', 'Transparansi kuota pendaftaran magang Dinas Komunikasi dan Informatika untuk siswa SMK dan mahasiswa D3/S1.')
@section('og_title', 'Informasi Kuota Magang Dinas Kominfo - MagKom')
@section('og_description', 'Lihat ketersediaan kuota magang per jurusan secara langsung dan transparan.')

@push('styles')
<style>
    /* =====================================================================
       1. HERO SECTION (MATCHING FIGMA NODE 562-54996: 1440 Fill x 560)
       ===================================================================== */
    .hero-section {
        background: linear-gradient(rgba(15, 23, 42, 0.80), rgba(29, 37, 48, 0.90)), 
                    url('/images/hero_office_bg.png') center/cover no-repeat;
        color: #ffffff;
        padding: 5rem 0 4.5rem;
        position: relative;
    }

    .hero-grid {
        display: grid;
        grid-template-columns: 1.25fr 0.85fr;
        gap: 3.5rem;
        align-items: center;
    }

    /* Gelombang 2 Pill Badge */
    .hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.25);
        padding: 0.4rem 1rem;
        border-radius: 9999px;
        font-size: 0.8125rem;
        color: #ffffff;
        font-weight: 600;
        margin-bottom: 1.25rem;
    }

    .green-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
        display: inline-block;
    }

    .hero-title {
        font-size: clamp(2rem, 3.6vw, 2.75rem);
        font-weight: 700;
        line-height: 1.22;
        letter-spacing: -0.02em;
        margin-bottom: 1.25rem;
        color: #ffffff;
    }

    .hero-desc {
        font-size: 1.05rem;
        color: rgba(255, 255, 255, 0.88);
        line-height: 1.6;
        max-width: 500px;
        margin-bottom: 2rem;
    }

    .hero-cta-group {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* Hero Right Glass Card (Sisa kuota Gelombang 2) */
    .hero-glass-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 1rem;
        padding: 1.75rem 2rem;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5);
    }

    .card-label-top {
        font-size: 0.8125rem;
        color: #cbd5e1;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .big-stat-row {
        display: flex;
        align-items: baseline;
        gap: 0.6rem;
        margin-bottom: 1rem;
    }

    .big-number {
        font-size: 3rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1;
    }

    .stat-total {
        font-size: 0.95rem;
        color: #cbd5e1;
        font-weight: 500;
    }

    /* Progress bar */
    .stat-progress-track {
        background: rgba(255, 255, 255, 0.2);
        height: 6px;
        border-radius: 9999px;
        width: 100%;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .stat-progress-fill {
        background: #38bdf8;
        height: 100%;
        border-radius: 9999px;
        width: 80%;
    }

    /* Table rows */
    .bidang-stat-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .bidang-stat-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.875rem;
        color: #ffffff;
    }

    .bidang-stat-count {
        font-weight: 600;
        color: #cbd5e1;
    }

    .card-footer-note {
        font-size: 0.75rem;
        color: #94a3b8;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 0.85rem;
    }

            /* =====================================================================
       2. BIDANG DAN KUOTA SECTION (Figma + AC-01 Matrix)
       ===================================================================== */
    .section-bidang-kuota {
        padding: 5.5rem 0 5rem;
        background: var(--neutral-0);
    }

    .section-heading-center {
        text-align: center;
        margin-bottom: 2.75rem;
    }

    .section-heading-center h2 {
        font-size: 2.125rem;
        font-weight: 700;
        color: var(--neutral-800);
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .section-heading-center p {
        font-size: 1rem;
        color: var(--neutral-600);
    }

    /* Tab Switcher: Design System v1.0 */
    .tab-pills-wrap {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        margin-bottom: 3rem;
        flex-wrap: wrap;
    }

    .tab-pill-btn {
        background: var(--neutral-100);
        border: 1px solid var(--neutral-400);
        padding: 0.75rem 1.85rem;
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--neutral-600);
        border-radius: 9999px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .tab-pill-btn:hover {
        border-color: var(--blue-600);
        color: var(--blue-600);
    }

    .tab-pill-btn.active {
        background: var(--blue-600);
        color: #ffffff;
        border-color: var(--blue-600);
        box-shadow: 0 4px 14px rgba(7, 81, 255, 0.28);
    }

    /* =====================================================================
       2. BIDANG DAN KUOTA HORIZONTAL CAROUSEL & ELEVATED COMMAND HUB
       ===================================================================== */
    .quota-controls-bar {
        background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 1.5rem;
        padding: 1.6rem 1.85rem;
        margin-bottom: 2.25rem;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.04), 0 8px 10px -6px rgba(15, 23, 42, 0.02);
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        transition: all 0.25s ease;
    }

    .quota-hub-main {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.75rem;
        align-items: center;
    }

    @media (max-width: 868px) {
        .quota-hub-main {
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
    }

    .filter-hub-col {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .filter-hub-label {
        font-size: 0.8125rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .filter-hub-label svg {
        color: #0751FF;
    }

    /* Modern Capsule Track (Clean Surface, No Nested Box Look) */
    .filter-capsule-track {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.35rem;
        background: #F1F5F9;
        padding: 0.3rem;
        border-radius: 0.85rem;
        border: 1px solid #E2E8F0;
    }

    .filter-capsule-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.65rem 1rem;
        border-radius: 0.65rem;
        border: none !important;
        background: transparent !important;
        color: #475569 !important;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        width: 100%;
        text-align: center;
        text-decoration: none;
        outline: none;
    }

    .filter-capsule-btn:hover {
        color: #0751FF !important;
        background: rgba(255, 255, 255, 0.75) !important;
    }

    .filter-capsule-btn.active {
        background: linear-gradient(135deg, #0751FF 0%, #0043DB 100%) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 14px rgba(7, 81, 255, 0.32) !important;
    }

    .filter-capsule-btn .capsule-chip {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.15rem 0.45rem;
        border-radius: 9999px;
        background: rgba(100, 116, 139, 0.12);
        color: inherit;
        transition: all 0.2s ease;
    }

    .filter-capsule-btn.active .capsule-chip {
        background: rgba(255, 255, 255, 0.22);
        color: #ffffff;
    }

    /* Sub-bar Pilihan Jumlah Anggota Kelompok */
    .team-size-stepper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 0.85rem;
        padding: 0.55rem 1rem;
        flex-wrap: wrap;
        animation: slideDownFade 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes slideDownFade {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .team-stepper-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #1E40AF;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        white-space: nowrap;
    }

    .team-pills-cluster {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .member-pill-btn {
        padding: 0.35rem 0.85rem;
        font-size: 0.8125rem;
        font-weight: 600;
        border-radius: 9999px;
        border: 1px solid #BFDBFE;
        background: #ffffff;
        color: #1E40AF;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .member-pill-btn:hover {
        border-color: #0751FF;
        color: #0751FF;
        background: #DBEAFE;
    }

    .member-pill-btn.active {
        background: #0751FF !important;
        color: #ffffff !important;
        border-color: #0751FF !important;
        font-weight: 700 !important;
        box-shadow: 0 2px 8px rgba(7, 81, 255, 0.28) !important;
    }

    .team-role-note {
        font-size: 0.75rem;
        color: #64748B;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    /* Controls Footer Info Bar */
    .quota-controls-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        padding-top: 1rem;
        border-top: 1px solid #F1F5F9;
    }

    .quota-indicator-badge {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #1D4ED8;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        padding: 0.4rem 0.95rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #2563EB;
        position: relative;
        flex-shrink: 0;
    }

    .pulse-dot::after {
        content: '';
        position: absolute;
        top: -3px;
        left: -3px;
        right: -3px;
        bottom: -3px;
        border-radius: 50%;
        border: 2px solid #3B82F6;
        animation: pulseAnimation 2s infinite;
    }

    @keyframes pulseAnimation {
        0% { transform: scale(0.9); opacity: 0.8; }
        70% { transform: scale(1.6); opacity: 0; }
        100% { transform: scale(1.6); opacity: 0; }
    }

    .quota-nav-hint {
        font-size: 0.8125rem;
        color: #64748B;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .quota-carousel-wrapper {
        position: relative;
        width: 100%;
        margin-bottom: 2.5rem;
    }

    .quota-carousel-track {
        display: flex;
        gap: 1.5rem;
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: none;
        -ms-overflow-style: none;
        padding: 0.75rem 0.5rem 1.75rem;
        cursor: grab;
    }

    .quota-carousel-track:active {
        cursor: grabbing;
    }

    .quota-carousel-track::-webkit-scrollbar {
        display: none;
    }

    /* Fixed Width for Cards in Horizontal Slider */
    .quota-carousel-track .figma-quota-card {
        flex: 0 0 370px;
        width: 370px;
        min-width: 370px;
        box-sizing: border-box;
    }

    @media (max-width: 640px) {
        .quota-carousel-track .figma-quota-card {
            flex: 0 0 86vw;
            width: 86vw;
            min-width: 86vw;
        }
    }

    /* Floating Arrow Buttons on the Sides of Carousel */
    .carousel-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: #ffffff;
        border: 1.5px solid #CBD5E1;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.16);
        color: #1E293B;
        display: flex !important;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 15;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        outline: none;
    }

    .carousel-nav-btn:hover {
        background: #0751FF;
        color: #ffffff;
        border-color: #0751FF;
        box-shadow: 0 8px 24px rgba(7, 81, 255, 0.35);
        transform: translateY(-50%) scale(1.1);
    }

    .carousel-nav-btn:active {
        transform: translateY(-50%) scale(0.95);
    }

    .carousel-nav-btn.prev {
        left: -18px;
    }

    .carousel-nav-btn.next {
        right: -18px;
    }

    @media (max-width: 860px) {
        .carousel-nav-btn.prev { left: -6px; }
        .carousel-nav-btn.next { right: -6px; }
    }

    /* Interactive Notice: Group Registration Guidance (Only 1 Representative) */
    .group-education-card {
        background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
        border: 1.5px solid #93C5FD;
        border-radius: 1.125rem;
        padding: 1.15rem 1.35rem;
        margin-top: 1.25rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        box-shadow: 0 4px 16px rgba(7, 81, 255, 0.08);
        animation: fadeInSlideDown 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .group-education-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #0751FF;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(7, 81, 255, 0.3);
    }

    .group-education-body {
        flex: 1;
        min-width: 0;
    }

    .group-education-headline {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: wrap;
        margin-bottom: 0.35rem;
    }

    .group-education-headline strong {
        font-size: 0.95rem;
        font-weight: 800;
        color: #1E3A8A;
    }

    .group-badge-perwakilan {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.65rem;
        border-radius: 9999px;
        background: #1E40AF;
        color: #ffffff;
        font-size: 0.725rem;
        font-weight: 700;
        letter-spacing: 0.04em;
    }

    .group-education-text {
        font-size: 0.875rem;
        color: #1E3A8A;
        line-height: 1.55;
        margin: 0;
    }

    .group-education-text strong {
        color: #0F172A;
    }

    @keyframes fadeInSlideDown {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Modern Elevated Quota Card */
    .figma-quota-card {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-radius: 1.25rem;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05);
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .figma-quota-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #0751FF 0%, #38BDF8 100%);
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .figma-quota-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 35px -8px rgba(7, 81, 255, 0.12), 0 2px 6px rgba(0, 0, 0, 0.04);
        border-color: #93C5FD;
    }

    .figma-quota-card:hover::before {
        opacity: 1;
    }

    .card-header-meta {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .card-jenjang-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        background: #F1F5F9;
        color: #334155;
        border: 1px solid #E2E8F0;
        letter-spacing: 0.02em;
        flex-shrink: 0;
    }

    .card-bidang-cluster {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        justify-content: flex-end;
    }

    .bidang-chip {
        font-size: 0.6875rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        letter-spacing: 0.02em;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .bidang-chip-ikp {
        background: #F0FDF4;
        color: #166534;
        border: 1px solid #BBF7D0;
    }
    .bidang-chip-egov {
        background: #EFF6FF;
        color: #1E40AF;
        border: 1px solid #BFDBFE;
    }
    .bidang-chip-iti {
        background: #FAF5FF;
        color: #6B21A8;
        border: 1px solid #E9D5FF;
    }
    .bidang-chip-sekretariat {
        background: #FFF7ED;
        color: #9A3412;
        border: 1px solid #FED7AA;
    }

    .card-jurusan-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0F172A;
        margin-bottom: 0.35rem;
        line-height: 1.35;
        letter-spacing: -0.015em;
    }

    .card-jurusan-desc {
        font-size: 0.8125rem;
        color: #64748B;
        margin-bottom: 1.15rem;
        line-height: 1.45;
        min-height: 38px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Card Period Segmented Bar (if multiple months) */
    .card-period-nav {
        display: flex;
        gap: 0.35rem;
        background: #F1F5F9;
        padding: 0.25rem;
        border-radius: 0.625rem;
        margin-bottom: 1rem;
    }

    .card-period-tab-btn {
        flex: 1;
        padding: 0.35rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748B;
        border-radius: 0.375rem;
        border: none;
        background: transparent;
        cursor: pointer;
        text-align: center;
        transition: all 0.15s ease;
    }

    .card-period-tab-btn:hover {
        color: #0F172A;
    }

    .card-period-tab-btn.active {
        background: #ffffff;
        color: #0F172A;
        font-weight: 700;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    /* Quota Capacity Tracker Surface */
    .card-capacity-box {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 0.875rem;
        padding: 1rem;
        margin-bottom: 1.25rem;
        transition: all 0.2s ease;
    }

    .capacity-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.625rem;
        gap: 0.5rem;
    }

    .capacity-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748B;
    }

    .capacity-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.2rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .capacity-status-pill.tersedia {
        background: #ECFDF5;
        color: #065F46;
        border: 1px solid #A7F3D0;
    }

    .capacity-status-pill.waiting {
        background: #EFF6FF;
        color: #1E40AF;
        border: 1px solid #BFDBFE;
    }

    .capacity-status-pill.kurang {
        background: #FFFBEB;
        color: #92400E;
        border: 1px solid #FDE68A;
    }

    .capacity-status-pill.segera {
        background: #EFF6FF;
        color: #1E40AF;
        border: 1px solid #BFDBFE;
    }

    .capacity-status-pill.tutup {
        background: #F1F5F9;
        color: #64748B;
        border: 1px solid #CBD5E1;
    }

    /* Capacity Progress Bar */
    .capacity-bar-track {
        height: 7px;
        background: #E2E8F0;
        border-radius: 9999px;
        overflow: hidden;
        margin-bottom: 0.5rem;
        position: relative;
    }

    .capacity-bar-fill {
        height: 100%;
        border-radius: 9999px;
        transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1), background 0.3s ease;
    }

    .capacity-bar-fill.tersedia {
        background: linear-gradient(90deg, #10B981 0%, #059669 100%);
    }

    .capacity-bar-fill.kurang {
        background: linear-gradient(90deg, #F59E0B 0%, #D97706 100%);
    }

    .capacity-bar-fill.waiting {
        background: linear-gradient(90deg, #3B82F6 0%, #6366F1 100%);
    }

    .capacity-bar-fill.tutup {
        background: #94A3B8;
    }

    .capacity-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.75rem;
        color: #64748B;
    }

    .capacity-meta-row strong {
        color: #0F172A;
    }

    /* Single High-Converting CTA Button */
    .card-action-btn {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        font-weight: 700;
        border-radius: 0.625rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        border: none;
    }

    .card-action-btn.btn-daftar {
        background: #0751FF;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(7, 81, 255, 0.22);
    }

    .card-action-btn.btn-daftar:hover {
        background: #0438b3;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(7, 81, 255, 0.32);
        color: #ffffff;
    }

    .card-action-btn.btn-waiting {
        background: #FEF3C7;
        color: #92400E;
        border: 1px solid #FCD34D;
    }

    .card-action-btn.btn-waiting:hover {
        background: #FDE68A;
        color: #78350F;
    }

    .card-action-btn.btn-disabled {
        background: #F1F5F9;
        color: #94A3B8;
        border: 1px solid #E2E8F0;
        cursor: not-allowed;
    }

    /* =====================================================================
       3. ALUR MAGANG SECTION (MATCHING FIGMA NODE 562-55102: 1440 Fill x 530)
       ===================================================================== */
    .section-alur-magang {
        background: linear-gradient(rgba(7, 26, 51, 0.88), rgba(11, 26, 48, 0.94)), 
                    url('/images/hero_office_bg.png') center/cover no-repeat;
        color: #ffffff;
        padding: 5.5rem 0 5rem;
        text-align: center;
        position: relative;
    }

    .alur-heading h2 {
        font-size: 2.125rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .alur-heading p {
        font-size: 1rem;
        color: #cbd5e1;
        margin-bottom: 3.5rem;
    }

    .alur-steps-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 3rem;
        position: relative;
    }

    .alur-step-card {
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 0.875rem;
        padding: 1.75rem 1.4rem;
        text-align: left;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.35);
        transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
    }

    .alur-step-card:hover {
        transform: translateY(-3px);
        background: rgba(255, 255, 255, 0.11);
        border-color: rgba(56, 189, 248, 0.5);
    }

    .alur-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .alur-icon-wrap {
        width: 2.5rem;
        height: 2.5rem;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #38bdf8;
    }

    .alur-icon-wrap svg {
        width: 1.35rem;
        height: 1.35rem;
    }

    .alur-step-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: #38bdf8;
        line-height: 1;
    }

    .alur-step-card h3 {
        font-size: 1.15rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.45rem;
    }

    .alur-step-card p {
        font-size: 0.85rem;
        color: #cbd5e1;
        line-height: 1.55;
    }

    .alur-interactive-showcase-bar {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.20);
        border-radius: 1.15rem;
        padding: 1.35rem 2rem;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        max-width: 960px;
        margin: 0 auto;
        flex-wrap: wrap;
    }

    .alur-showcase-info {
        flex: 1;
        min-width: 280px;
        text-align: left;
    }

    .alur-showcase-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #38bdf8;
        margin-bottom: 0.35rem;
    }

    .alur-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #38bdf8;
        box-shadow: 0 0 10px #38bdf8;
        display: inline-block;
    }

    .alur-showcase-text {
        font-size: 0.875rem;
        color: #cbd5e1;
        line-height: 1.5;
        margin: 0;
    }

    .btn-panduan-showcase {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--blue-600);
        color: #ffffff;
        font-size: 0.925rem;
        font-weight: 700;
        padding: 0.85rem 1.65rem;
        border-radius: 0.65rem;
        text-decoration: none;
        white-space: nowrap;
        box-shadow: 0 6px 18px rgba(7, 81, 255, 0.35);
        transition: all 0.2s ease;
    }

    .btn-panduan-showcase:hover {
        background: var(--blue-700);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(7, 81, 255, 0.5);
    }

    /* Backward compatibility helper */
    .btn-panduan {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    /* =====================================================================

    /* =====================================================================
       4. PENGUMUMAN SECTION (MODERN EDITORIAL GOVTECH SPEC)
       ===================================================================== */
    .section-pengumuman {
        padding: 6rem 0 5.5rem;
        background: var(--neutral-0, #ffffff);
        position: relative;
    }

    .pengumuman-header-wrap {
        text-align: center;
        max-width: 680px;
        margin: 0 auto 2.5rem;
    }

    .pengumuman-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.875rem;
        border-radius: 9999px;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        color: #1D4ED8;
        font-size: 0.8125rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 0.875rem;
    }

    .pengumuman-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #2563EB;
        box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7);
        animation: pulseDot 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
    }

    @keyframes pulseDot {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7);
        }
        50% {
            box-shadow: 0 0 0 6px rgba(37, 99, 235, 0);
        }
    }

    .pengumuman-title {
        font-size: 2.125rem;
        font-weight: 800;
        color: var(--neutral-900, #0F172A);
        letter-spacing: -0.025em;
        line-height: 1.25;
        margin-bottom: 0.75rem;
        text-wrap: balance;
    }

    .pengumuman-subtitle {
        font-size: 0.95rem;
        color: var(--neutral-600, #475569);
        line-height: 1.6;
        text-wrap: balance;
    }

    /* Filter Tabs Kategori Pengumuman */
    .pengumuman-filter-bar {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 3rem;
    }

    .pengumuman-tab-btn {
        appearance: none;
        background: #F8FAFC;
        border: 1px solid var(--neutral-300, #E2E8F0);
        color: var(--neutral-700, #334155);
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.5rem 1.15rem;
        border-radius: 9999px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.2, 0, 0, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pengumuman-tab-btn:hover {
        background: #EFF6FF;
        color: #1D4ED8;
        border-color: #BFDBFE;
    }

    .pengumuman-tab-btn.active {
        background: #0751FF;
        color: #ffffff;
        border-color: #0751FF;
        box-shadow: 0 4px 12px rgba(7, 81, 255, 0.25);
    }

    .pengumuman-tab-badge {
        font-size: 0.75rem;
        padding: 0.1rem 0.45rem;
        border-radius: 9999px;
        background: rgba(0, 0, 0, 0.08);
        font-weight: 700;
    }

    .pengumuman-tab-btn.active .pengumuman-tab-badge {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Pengumuman Grid */
    .pengumuman-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.75rem;
    }

    .pengumuman-card {
        background: #ffffff;
        border: 1px solid var(--neutral-200, #E2E8F0);
        border-radius: 1rem;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05);
        transition: transform 0.25s cubic-bezier(0.2, 0, 0, 1), box-shadow 0.25s cubic-bezier(0.2, 0, 0, 1), border-color 0.25s ease;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .pengumuman-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: transparent;
        transition: background 0.2s ease;
    }

    .pengumuman-card[data-category="pendaftaran"]::before { background: #2563EB; }
    .pengumuman-card[data-category="persyaratan"]::before { background: #D97706; }
    .pengumuman-card[data-category="hasil"]::before { background: #16A34A; }

    .pengumuman-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.12);
        border-color: #93C5FD;
    }

    .pengumuman-card:focus-visible {
        outline: 2px solid #0751FF;
        outline-offset: 2px;
    }

    .pengumuman-meta-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1.15rem;
    }

    .pengumuman-category-tag {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.65rem;
        border-radius: 0.375rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .tag-blue { background: #EFF6FF; color: #1E40AF; border: 1px solid #DBEAFE; }
    .tag-amber { background: #FFFBEB; color: #92400E; border: 1px solid #FEF3C7; }
    .tag-green { background: #ECFDF5; color: #065F46; border: 1px solid #D1FAE5; }

    .pengumuman-pin-badge {
        font-size: 0.75rem;
        font-weight: 700;
        color: #2563EB;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        padding: 0.2rem 0.55rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .pengumuman-card h3 {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--neutral-900, #0F172A);
        margin-bottom: 0.75rem;
        line-height: 1.4;
        transition: color 0.2s ease;
    }

    .pengumuman-card:hover h3 {
        color: #0751FF;
    }

    .pengumuman-card p {
        font-size: 0.875rem;
        color: var(--neutral-600, #475569);
        line-height: 1.6;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .pengumuman-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 1.15rem;
        border-top: 1px solid var(--neutral-200, #F1F5F9);
        font-size: 0.8125rem;
    }

    .pengumuman-date-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        color: var(--neutral-500, #64748B);
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }

    .pengumuman-action-link {
        font-weight: 700;
        color: #0751FF;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: gap 0.2s ease;
    }

    .pengumuman-card:hover .pengumuman-action-link {
        gap: 0.6rem;
    }

    /* Modal Pop-up Warta Resmi */
    .pengumuman-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .pengumuman-modal-backdrop.open {
        display: flex;
        opacity: 1;
    }

    .pengumuman-modal-card {
        background: #ffffff;
        border-radius: 1.25rem;
        max-width: 650px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.5);
        padding: 2.25rem;
        position: relative;
        transform: scale(0.95);
        transition: transform 0.25s cubic-bezier(0.2, 0, 0, 1);
    }

    .pengumuman-modal-backdrop.open .pengumuman-modal-card {
        transform: scale(1);
    }

    .modal-kop-surat {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding-bottom: 1.25rem;
        border-bottom: 2px solid var(--neutral-900, #0F172A);
        margin-bottom: 1.5rem;
    }

    .modal-kop-logo {
        width: 48px;
        height: 48px;
        object-fit: contain;
    }

    .modal-kop-text h4 {
        font-size: 0.875rem;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin: 0;
    }

    .modal-kop-text p {
        font-size: 0.75rem;
        color: #475569;
        margin: 0;
        line-height: 1.4;
    }

    .modal-content-body {
        font-size: 0.9375rem;
        color: #334155;
        line-height: 1.7;
    }

    .modal-close-btn {
        position: absolute;
        top: 1.25rem;
        right: 1.25rem;
        background: #F1F5F9;
        border: none;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #64748B;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .modal-close-btn:hover {
        background: #E2E8F0;
        color: #0F172A;
    }

    /* =====================================================================
       5. LAYANAN SECTION (ENTERPRISE ACTION TILES SPEC)
       ===================================================================== */
    .section-layanan {
        padding: 6rem 0 5.5rem;
        background: linear-gradient(180deg, #F8FAFC 0%, #EFF6FF 100%);
        border-top: 1px solid var(--neutral-200, #E2E8F0);
        position: relative;
    }

    .layanan-header-wrap {
        text-align: center;
        max-width: 680px;
        margin: 0 auto 3.5rem;
    }

    .layanan-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.875rem;
        border-radius: 9999px;
        background: #E0E7FF;
        border: 1px solid #C7D2FE;
        color: #4338CA;
        font-size: 0.8125rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 0.875rem;
    }

    .layanan-title {
        font-size: 2.125rem;
        font-weight: 800;
        color: var(--neutral-900, #0F172A);
        letter-spacing: -0.025em;
        line-height: 1.25;
        margin-bottom: 0.75rem;
        text-wrap: balance;
    }

    .layanan-subtitle {
        font-size: 0.95rem;
        color: var(--neutral-600, #475569);
        line-height: 1.6;
        text-wrap: balance;
    }

    .layanan-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    .layanan-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 1.125rem;
        padding: 2rem 1.65rem 1.75rem;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.04);
        transition: transform 0.25s cubic-bezier(0.2, 0, 0, 1), box-shadow 0.25s cubic-bezier(0.2, 0, 0, 1), border-color 0.25s ease;
    }

    /* Ambient corner glow */
    .layanan-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle at top right, var(--card-glow, rgba(7, 81, 255, 0.08)), transparent 70%);
        pointer-events: none;
        border-radius: 0 1.125rem 0 0;
    }

    .layanan-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 32px -8px rgba(15, 23, 42, 0.1);
        border-color: #93C5FD;
    }

    .layanan-card:focus-visible {
        outline: 2px solid #0751FF;
        outline-offset: 2px;
    }

    .layanan-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.35rem;
    }

    .layanan-icon-box {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.25s ease;
    }

    .layanan-card:hover .layanan-icon-box {
        transform: scale(1.06);
    }

    .icon-box-blue { background: #EFF6FF; color: #2563EB; }
    .icon-box-amber { background: #FFFBEB; color: #D97706; }
    .icon-box-emerald { background: #ECFDF5; color: #059669; }
    .icon-box-purple { background: #F5F3FF; color: #7C3AED; }

    .layanan-target-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        letter-spacing: 0.02em;
    }

    .target-badge-calon { background: #EFF6FF; color: #1D4ED8; border: 1px solid #DBEAFE; }
    .target-badge-status { background: #FFF7ED; color: #C2410C; border: 1px solid #FFEDD5; }
    .target-badge-aktif { background: #ECFDF5; color: #047857; border: 1px solid #D1FAE5; }
    .target-badge-alumni { background: #FAF5FF; color: #6B21A8; border: 1px solid #F3E8FF; }

    .layanan-card-body h3 {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--neutral-900, #0F172A);
        margin-bottom: 0.65rem;
        line-height: 1.35;
        transition: color 0.2s ease;
    }

    .layanan-card:hover .layanan-card-body h3 {
        color: #0751FF;
    }

    .layanan-card-body p {
        font-size: 0.875rem;
        color: var(--neutral-600, #475569);
        line-height: 1.55;
        margin-bottom: 1.5rem;
    }

    .layanan-card-cta {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: #0751FF;
        transition: gap 0.2s ease;
        margin-top: auto;
    }

    .layanan-card:hover .layanan-card-cta {
        gap: 0.75rem;
    }

    /* Responsivitas Media Queries */
    @media (max-width: 992px) {
        .hero-grid {
            grid-template-columns: 1fr;
            gap: 2.5rem;
        }
        .alur-steps-row {
            grid-template-columns: 1fr 1fr;
        }
        .pengumuman-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
        .layanan-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
    }

    @media (max-width: 640px) {
        .alur-steps-row {
            grid-template-columns: 1fr;
        }
        .pengumuman-grid {
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
        .layanan-grid {
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
        .pengumuman-title,
        .layanan-title {
            font-size: 1.625rem;
        }
        .pengumuman-filter-bar {
            overflow-x: auto;
            justify-content: flex-start;
            padding-bottom: 0.5rem;
            -webkit-overflow-scrolling: touch;
        }
        .pengumuman-modal-card {
            padding: 1.5rem;
        }
    }

    /* Dark Mode Overrides for Landing Page */
    [data-theme="dark"] .figma-quota-card {
        background: var(--neutral-0, #182234) !important;
        border-color: var(--neutral-200, #26354D) !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
    }

    [data-theme="dark"] .figma-quota-card:hover {
        border-color: var(--blue-600, #3B82F6) !important;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.45) !important;
    }

    [data-theme="dark"] .section-pengumuman {
        background: #0B1120 !important;
    }

    [data-theme="dark"] .section-layanan {
        background: linear-gradient(180deg, #0F172A 0%, #0B1120 100%) !important;
        border-color: #1E293B !important;
    }

    [data-theme="dark"] .pengumuman-title,
    [data-theme="dark"] .layanan-title {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .pengumuman-subtitle,
    [data-theme="dark"] .layanan-subtitle {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .pengumuman-tab-btn {
        background: #1E293B !important;
        border-color: #334155 !important;
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .pengumuman-tab-btn.active {
        background: #2563EB !important;
        border-color: #3B82F6 !important;
        color: #ffffff !important;
    }

    [data-theme="dark"] .pengumuman-card,
    [data-theme="dark"] .layanan-card {
        background: #111C2E !important;
        border-color: #1E293B !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4) !important;
    }

    [data-theme="dark"] .pengumuman-card:hover,
    [data-theme="dark"] .layanan-card:hover {
        border-color: #3B82F6 !important;
        box-shadow: 0 16px 32px rgba(0, 0, 0, 0.6) !important;
    }

    [data-theme="dark"] .pengumuman-card h3,
    [data-theme="dark"] .layanan-card-body h3 {
        color: #F1F5F9 !important;
    }

    [data-theme="dark"] .pengumuman-card p,
    [data-theme="dark"] .layanan-card-body p {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .pengumuman-card-footer {
        border-color: #1E293B !important;
    }

    [data-theme="dark"] .pengumuman-date-chip {
        color: #64748B !important;
    }

    [data-theme="dark"] .pengumuman-modal-card {
        background: #0F172A !important;
        border-color: #1E293B !important;
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .modal-kop-surat {
        border-color: #334155 !important;
    }

    [data-theme="dark"] .modal-kop-text h4 {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .modal-kop-text p {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .modal-content-body {
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .modal-close-btn {
        background: #1E293B !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .card-period-box,
    [data-theme="dark"] .card-capacity-box {
        background: var(--neutral-100, #0F172A) !important;
        border-color: var(--neutral-200, #26354D) !important;
    }

    [data-theme="dark"] .quota-controls-bar {
        background: linear-gradient(180deg, #111C2E 0%, #0B1120 100%) !important;
        border-color: #1E293B !important;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.45) !important;
    }

    [data-theme="dark"] .filter-hub-label {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .filter-hub-label svg {
        color: #60A5FA !important;
    }

    [data-theme="dark"] .filter-capsule-track {
        background: #0B1120 !important;
        border-color: #1E293B !important;
    }

    [data-theme="dark"] .filter-capsule-btn {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .filter-capsule-btn:hover {
        color: #60A5FA !important;
        background: rgba(30, 41, 59, 0.6) !important;
    }

    [data-theme="dark"] .filter-capsule-btn.active {
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4) !important;
    }

    [data-theme="dark"] .team-size-stepper {
        background: #131D31 !important;
        border-color: #1E3A8A !important;
    }

    [data-theme="dark"] .team-stepper-label {
        color: #93C5FD !important;
    }

    [data-theme="dark"] .team-role-note {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .member-pill-btn {
        background: #1E293B !important;
        border-color: #334155 !important;
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .member-pill-btn:hover {
        background: #26354D !important;
        border-color: #3B82F6 !important;
        color: #60A5FA !important;
    }

    [data-theme="dark"] .member-pill-btn.active {
        background: #2563EB !important;
        color: #ffffff !important;
        border-color: #2563EB !important;
    }

    [data-theme="dark"] .quota-controls-footer {
        border-top-color: #1E293B !important;
    }

    [data-theme="dark"] .quota-indicator-badge {
        background: #1E293B !important;
        border-color: #3B82F6 !important;
        color: #93C5FD !important;
    }

    [data-theme="dark"] .quota-nav-hint {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .card-jenjang-pill {
        background: #0F172A !important;
        border-color: #334155 !important;
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .card-period-nav {
        background: #0F172A !important;
    }

    [data-theme="dark"] .card-period-tab-btn {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .card-period-tab-btn.active {
        background: #1E293B !important;
        color: #FFFFFF !important;
    }

    [data-theme="dark"] .carousel-nav-btn {
        background: #1E293B !important;
        border-color: #334155 !important;
        color: #F8FAFC !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.45) !important;
    }

    [data-theme="dark"] .carousel-nav-btn:hover {
        background: #3B82F6 !important;
        color: #ffffff !important;
        border-color: #3B82F6 !important;
    }

    [data-theme="dark"] .group-education-card {
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%) !important;
        border-color: #3B82F6 !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4) !important;
    }

    [data-theme="dark"] .group-education-headline strong {
        color: #93C5FD !important;
    }

    [data-theme="dark"] .group-education-text {
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .group-education-text strong {
        color: #FFFFFF !important;
    }

    [data-theme="dark"] .tab-pill-btn {
        background: var(--neutral-0, #182234) !important;
        border-color: var(--neutral-200, #26354D) !important;
        color: var(--neutral-600, #CBD5E1) !important;
    }

    [data-theme="dark"] .tab-pill-btn.active {
        background: var(--blue-600, #3B82F6) !important;
        color: #ffffff !important;
        border-color: var(--blue-600, #3B82F6) !important;
    }

    [data-theme="dark"] .card-jenjang-badge {
        background: var(--neutral-100, #0F172A) !important;
        color: var(--neutral-600, #CBD5E1) !important;
    }

    [data-theme="dark"] .card-jurusan-title,
    [data-theme="dark"] .pengumuman-card h3,
    [data-theme="dark"] .layanan-card span {
        color: var(--neutral-900, #FFFFFF) !important;
    }

    [data-theme="dark"] .card-jurusan-desc,
    [data-theme="dark"] .pengumuman-card p {
        color: var(--neutral-500, #94A3B8) !important;
    }

    [data-theme="dark"] .period-title {
        color: var(--neutral-700, #E2E8F0) !important;
    }

    [data-theme="dark"] .quota-count {
        color: var(--neutral-800, #F8FAFC) !important;
    }

    [data-theme="dark"] .section-pengumuman {
        background: var(--neutral-150, #0A0F1D) !important;
    }

    [data-theme="dark"] .section-layanan {
        background: var(--neutral-100, #0F172A) !important;
    }
</style>
@endpush

@section('content')
<!-- =====================================================================
     1. HERO SECTION (Figma node 562-54996)
     ===================================================================== -->
<section class="hero-section">
    <div class="container">
        <div class="hero-grid">
            <!-- Left Hero Content -->
            <div>
                <span class="sr-only">Magang Kominfo Sleman</span>
                <div class="hero-pill">
                    <span class="green-dot"></span>
                    <span>Gelombang 2 dibuka · 1–15 Okt 2026</span>
                </div>
                <h1 class="hero-title">
                    Magang di Dinas Komunikasi dan Informatika Kabupaten Sleman
                </h1>
                <p class="hero-desc">
                    Daftar, pantau status, kerjakan tugas, dan unduh lembar nilai magangmu dalam satu portal.
                </p>
                <div class="hero-cta-group">
                    <a href="#kuota-section" class="btn btn-white">Daftar sekarang</a>
                    <a href="#kuota-section" class="btn btn-ghost-white">Lihat kuota</a>
                </div>
            </div>

            <!-- Right Hero Glass Card (Sisa Kuota Gelombang 2) -->
            <div>
                <div class="hero-glass-card">
                    <div class="card-label-top">Sisa kuota Gelombang 2</div>
                    <div class="big-stat-row">
                        <span class="big-number">24</span>
                        <span class="stat-total">dari 30 tempat</span>
                    </div>

                    <div class="stat-progress-track">
                        <div class="stat-progress-fill"></div>
                    </div>

                    <div class="bidang-stat-list">
                        <div class="bidang-stat-item">
                            <span>Aplikasi Informatika</span>
                            <span class="bidang-stat-count">8 dari 10</span>
                        </div>
                        <div class="bidang-stat-item">
                            <span>Statistik</span>
                            <span class="bidang-stat-count">6 dari 8</span>
                        </div>
                        <div class="bidang-stat-item">
                            <span>Persandian</span>
                            <span class="bidang-stat-count">5 dari 6</span>
                        </div>
                        <div class="bidang-stat-item">
                            <span>Sekretariat</span>
                            <span class="bidang-stat-count">5 dari 6</span>
                        </div>
                    </div>

                    <div class="card-footer-note">
                        Pendaftaran ditutup 15 Okt 2026 · magang mulai 2 Nov 2026
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =====================================================================
     2. BIDANG DAN KUOTA SECTION (Figma + AC-01 Matrix)
     ===================================================================== -->
<section id="kuota-section" class="section-bidang-kuota">
    <div class="container">
        <div class="section-heading-center">
            <h2>Bidang dan kuota</h2>
            <p>Pendaftaran Magang & PKL Dinas Komunikasi dan Informatika Kabupaten Sleman</p>
        </div>

        @if($bulanTampil->isEmpty())
            <div style="text-align: center; background: #ffffff; padding: 3rem; border-radius: 1rem; border: 1px dashed var(--gray-300);">
                <p style="color: var(--gray-600); font-size: 1rem;">
                    Saat ini belum ada periode pendaftaran magang yang dibuka untuk publik. Silakan cek kembali secara berkala.
                </p>
            </div>
        @else
            <!-- Control Bar: Elevated Command Hub (Sistem Simetris Bersih & Modern) -->
            <div class="quota-controls-bar">
                <div class="quota-hub-main">
                    <!-- Kolom 1: Jalur Pendidikan -->
                    <div class="filter-hub-col">
                        <div class="filter-hub-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                            <span>Jalur Pendidikan</span>
                        </div>
                        <div class="filter-capsule-track" role="tablist" aria-label="Pilih Jalur Pendidikan">
                            <button type="button" class="filter-capsule-btn active" id="tab-btn-smk" onclick="switchTab('smk')" role="tab" aria-selected="true">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                <span>Jalur SMK</span>
                                <span class="capsule-chip">Kejuruan</span>
                            </button>
                            <button type="button" class="filter-capsule-btn" id="tab-btn-mahasiswa" onclick="switchTab('mahasiswa')" role="tab" aria-selected="false">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                                <span>Mahasiswa</span>
                                <span class="capsule-chip">D3 / S1</span>
                            </button>
                        </div>
                    </div>

                    <!-- Kolom 2: Tipe Pendaftaran -->
                    <div class="filter-hub-col">
                        <div class="filter-hub-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span>Tipe Pendaftaran</span>
                        </div>
                        <div class="filter-capsule-track" role="group" aria-label="Pilih Tipe Pendaftaran">
                            <button type="button" class="filter-capsule-btn active" id="mode-btn-mandiri" onclick="setRegistrationMode('mandiri')">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span>Mandiri</span>
                                <span class="capsule-chip">1 Orang</span>
                            </button>
                            <button type="button" class="filter-capsule-btn" id="mode-btn-kelompok" onclick="setRegistrationMode('kelompok')">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                <span>Kelompok</span>
                                <span class="capsule-chip">2–4 Org</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sub-bar Pilihan Jumlah Anggota Kelompok (Muncul otomatis saat mode Kelompok dipilih) -->
                <div class="team-size-stepper" id="member-selector-wrap" style="display: none;">
                    <div class="team-stepper-label">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                        <span>Jumlah Calon Peserta Tim:</span>
                    </div>
                    <div class="team-pills-cluster">
                        <button type="button" class="member-pill-btn active" data-count="2" onclick="setMemberCount(2)">2 Orang</button>
                        <button type="button" class="member-pill-btn" data-count="3" onclick="setMemberCount(3)">3 Orang</button>
                        <button type="button" class="member-pill-btn" data-count="4" onclick="setMemberCount(4)">4 Orang</button>
                    </div>
                    <div class="team-role-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        <span>Cukup 1 perwakilan/ketua yang mengisi pendaftaran</span>
                    </div>
                </div>

                <!-- Feedback Real-Time Indikator & Action Navigation Controls -->
                <div class="quota-controls-footer">
                    <div class="quota-indicator-badge" id="quota-indicator-badge">
                        <span class="pulse-dot"></span>
                        <span id="quota-indicator-text">Pendaftaran Mandiri (1 Orang) · Kuota otomatis dicek untuk 1 calon peserta</span>
                    </div>

                    <div class="quota-nav-hint">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        <span>Geser kartu atau gunakan tombol anak panah di samping</span>
                    </div>
                </div>
            </div>

            <!-- Banner Edukasi: Pendaftaran Kelompok Hanya Oleh 1 Perwakilan/Ketua -->
            <div id="banner-kelompok-edukasi" class="group-education-card" style="display: none;">
                <div class="group-education-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="group-education-body">
                    <div class="group-education-headline">
                        <strong>Penting untuk Calon Peserta Kelompok / Tim:</strong>
                        <span class="group-badge-perwakilan">Hanya 1 Akun Perwakilan</span>
                    </div>
                    <p class="group-education-text">
                        Untuk pendaftaran kelompok/tim (2–4 orang), <strong>cukup Ketua atau Perwakilan Kelompok yang membuat akun dan mendaftar</strong>. Rekan tim lainnya <strong>tidak perlu membuat akun terpisah</strong>. Seluruh data & berkas anggota tim akan diisi langsung oleh perwakilan saat melengkapi formulir permohonan magang.
                    </p>
                </div>
            </div>

            <!-- Tab Content: SMK -->
            <div id="tab-content-smk">
                <div class="quota-carousel-wrapper" id="carousel-wrapper-smk">
                    <button type="button" class="carousel-nav-btn prev" id="carousel-prev-smk" aria-label="Geser ke kiri" title="Geser ke kiri">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>

                    <div class="quota-carousel-track" id="carousel-track-smk">
                        @forelse($jurusanSmk as $jurusan)
                        <div class="figma-quota-card" data-jurusan-id="{{ $jurusan->id }}">
                            <div>
                                <!-- Top Meta Row -->
                                <div class="card-header-meta">
                                    <span class="card-jenjang-pill">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="M6 8h.01M10 8h.01M14 8h.01"></path></svg>
                                        SMK
                                    </span>

                                    @if($jurusan->bidang->isNotEmpty())
                                        <div class="card-bidang-cluster">
                                            @foreach($jurusan->bidang as $bdg)
                                                @php
                                                    $rawName = $bdg->nama_bidang;
                                                    $code = 'Bidang';
                                                    $chipClass = 'bidang-chip-egov';
                                                    if (stripos($rawName, 'IKP') !== false || stripos($rawName, 'Informasi dan Komunikasi') !== false) {
                                                        $code = 'IKP';
                                                        $chipClass = 'bidang-chip-ikp';
                                                    } elseif (stripos($rawName, 'E-Government') !== false || stripos($rawName, 'EGOV') !== false) {
                                                        $code = 'E-Gov';
                                                        $chipClass = 'bidang-chip-egov';
                                                    } elseif (stripos($rawName, 'Infrastruktur') !== false || stripos($rawName, 'ITI') !== false) {
                                                        $code = 'ITI';
                                                        $chipClass = 'bidang-chip-iti';
                                                    } elseif (stripos($rawName, 'Sekretariat') !== false) {
                                                        $code = 'Sekretariat';
                                                        $chipClass = 'bidang-chip-sekretariat';
                                                    }
                                                @endphp
                                                <span class="bidang-chip {{ $chipClass }}" title="Diterima di bidang {{ $rawName }}">
                                                    {{ $code }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Major Title & Description -->
                                <h3 class="card-jurusan-title">{{ $jurusan->nama_jurusan }}</h3>
                                <p class="card-jurusan-desc">{{ $jurusan->deskripsi ?? 'Kompetensi keahlian kejuruan tingkat menengah.' }}</p>

                                <!-- Period Switcher (if multiple months exist) -->
                                @if($bulanTampil->count() > 1)
                                    <div class="card-period-nav">
                                        @foreach($bulanTampil as $bIdx => $bulan)
                                            <button type="button" 
                                                class="card-period-tab-btn {{ $bIdx === 0 ? 'active' : '' }}" 
                                                data-target-month="{{ $bulan->id }}"
                                                onclick="switchCardMonth(this, '{{ $jurusan->id }}', '{{ $bulan->id }}')">
                                                {{ \Carbon\CarbonImmutable::parse($bulan->bulan_mulai)->translatedFormat('M Y') }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Month Panels -->
                                @foreach($bulanTampil as $bIdx => $bulan)
                                    @php
                                        $data = $matriksKetersediaan[$jurusan->id][$bulan->id] ?? null;
                                        $status = $data['status'] ?? \App\Enums\StatusKetersediaanEnum::Tutup;
                                        $sisa = $data['sisa_kuota'] ?? 0;
                                        $total = $data['total_kuota'] ?? 0;
                                        $terisi = $data['terisi'] ?? 0;
                                        $persen = $data['persen_terisi'] ?? 0;
                                        $isWaiting = $data['is_waiting_list'] ?? false;
                                        $buka = \Carbon\CarbonImmutable::parse($bulan->tgl_buka)->translatedFormat('d M Y');
                                        $bulanMulaiStr = \Carbon\CarbonImmutable::parse($bulan->bulan_mulai)->translatedFormat('F Y');
                                    @endphp
                                    <div class="card-month-panel" 
                                        id="panel-j{{ $jurusan->id }}-b{{ $bulan->id }}"
                                        style="{{ $bIdx === 0 ? '' : 'display: none;' }}"
                                        data-jurusan-id="{{ $jurusan->id }}"
                                        data-bulan-id="{{ $bulan->id }}"
                                        data-status="{{ $status->value }}"
                                        data-sisa="{{ $sisa }}"
                                        data-total="{{ $total }}"
                                        data-terisi="{{ $terisi }}"
                                        data-persen="{{ $persen }}"
                                        data-is-waiting="{{ $isWaiting ? '1' : '0' }}"
                                        data-buka-str="{{ $buka }}"
                                        data-bulan-str="{{ $bulanMulaiStr }}">
                                        
                                        <!-- Capacity Box Surface -->
                                        <div class="card-capacity-box">
                                            <div class="capacity-header">
                                                <span class="capacity-label">Mulai: {{ $bulanMulaiStr }}</span>
                                                <span class="capacity-status-pill status-pill-target {{ $status === \App\Enums\StatusKetersediaanEnum::Tersedia ? ($isWaiting ? 'waiting' : 'tersedia') : ($status === \App\Enums\StatusKetersediaanEnum::Segera ? 'segera' : 'tutup') }}">
                                                    @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                                        @if($isWaiting)
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                            <span>Kuota Penuh (Waiting List)</span>
                                                        @else
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                            <span>Tersedia</span>
                                                        @endif
                                                    @elseif($status === \App\Enums\StatusKetersediaanEnum::Segera)
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                                        <span>Segera Dibuka</span>
                                                    @else
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                        <span>Tutup</span>
                                                    @endif
                                                </span>
                                            </div>

                                            <!-- Visual Capacity Bar -->
                                            <div class="capacity-bar-track">
                                                <div class="capacity-bar-fill {{ $status === \App\Enums\StatusKetersediaanEnum::Tersedia ? ($isWaiting ? 'waiting' : 'tersedia') : ($status === \App\Enums\StatusKetersediaanEnum::Segera ? 'waiting' : 'tutup') }}" 
                                                    style="width: {{ $persen }}%;"></div>
                                            </div>

                                            <div class="capacity-meta-row">
                                                <span class="capacity-meta-left">
                                                    @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                                        @if($isWaiting)
                                                            <span style="color: #b45309; font-weight: 600;">Pendaftaran masuk antrean tunggu</span>
                                                        @else
                                                            Sisa: <strong class="sisa-val">{{ $sisa }}</strong> dari {{ $total }} Kursi
                                                        @endif
                                                    @elseif($status === \App\Enums\StatusKetersediaanEnum::Segera)
                                                        Buka: <strong>{{ $buka }}</strong>
                                                    @else
                                                        Pendaftaran Ditutup
                                                    @endif
                                                </span>
                                                <span class="capacity-meta-right">
                                                    @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                                        <span class="persen-val">{{ $persen }}%</span> Terisi
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Action Button CTA -->
                                        <div>
                                            @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                                <a href="{{ url('/register?bulan=' . $bulan->id . '&jurusan=' . $jurusan->id . '&tipe=mandiri&jumlah=1') }}" 
                                                   class="card-action-btn btn-cta-target {{ $isWaiting ? 'btn-waiting' : 'btn-daftar' }}">
                                                    <span class="btn-text">
                                                        {{ $isWaiting ? 'Daftar Waiting List' : 'Daftar Sekarang ➔' }}
                                                    </span>
                                                </a>
                                            @else
                                                <button type="button" class="card-action-btn btn-disabled" disabled>
                                                    {{ $status === \App\Enums\StatusKetersediaanEnum::Segera ? 'Segera Dibuka' : 'Pendaftaran Ditutup' }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p style="color: var(--gray-600); text-align: center; width: 100%; padding: 2rem;">Belum ada master jurusan SMK yang aktif.</p>
                    @endforelse
                    </div>

                    <button type="button" class="carousel-nav-btn next" id="carousel-next-smk" aria-label="Geser ke kanan" title="Geser ke kanan">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
            </div>

            <!-- Tab Content: Mahasiswa -->
            <div id="tab-content-mahasiswa" style="display: none;">
                <div class="quota-carousel-wrapper" id="carousel-wrapper-mahasiswa">
                    <button type="button" class="carousel-nav-btn prev" id="carousel-prev-mahasiswa" aria-label="Geser ke kiri" title="Geser ke kiri">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>

                    <div class="quota-carousel-track" id="carousel-track-mahasiswa">
                        @forelse($jurusanMahasiswa as $jurusan)
                        <div class="figma-quota-card" data-jurusan-id="{{ $jurusan->id }}">
                            <div>
                                <!-- Top Meta Row -->
                                <div class="card-header-meta">
                                    <span class="card-jenjang-pill" style="background: #EFF6FF; color: #1E40AF; border-color: #BFDBFE;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                                        D3 · S1
                                    </span>

                                    @if($jurusan->bidang->isNotEmpty())
                                        <div class="card-bidang-cluster">
                                            @foreach($jurusan->bidang as $bdg)
                                                @php
                                                    $rawName = $bdg->nama_bidang;
                                                    $code = 'Bidang';
                                                    $chipClass = 'bidang-chip-egov';
                                                    if (stripos($rawName, 'IKP') !== false || stripos($rawName, 'Informasi dan Komunikasi') !== false) {
                                                        $code = 'IKP';
                                                        $chipClass = 'bidang-chip-ikp';
                                                    } elseif (stripos($rawName, 'E-Government') !== false || stripos($rawName, 'EGOV') !== false) {
                                                        $code = 'E-Gov';
                                                        $chipClass = 'bidang-chip-egov';
                                                    } elseif (stripos($rawName, 'Infrastruktur') !== false || stripos($rawName, 'ITI') !== false) {
                                                        $code = 'ITI';
                                                        $chipClass = 'bidang-chip-iti';
                                                    } elseif (stripos($rawName, 'Sekretariat') !== false) {
                                                        $code = 'Sekretariat';
                                                        $chipClass = 'bidang-chip-sekretariat';
                                                    }
                                                @endphp
                                                <span class="bidang-chip {{ $chipClass }}" title="Diterima di bidang {{ $rawName }}">
                                                    {{ $code }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Major Title & Description -->
                                <h3 class="card-jurusan-title">{{ $jurusan->nama_jurusan }}</h3>
                                <p class="card-jurusan-desc">{{ $jurusan->deskripsi ?? 'Program studi perguruan tinggi (D3 / D4 / S1).' }}</p>

                                <!-- Period Switcher (if multiple months exist) -->
                                @if($bulanTampil->count() > 1)
                                    <div class="card-period-nav">
                                        @foreach($bulanTampil as $bIdx => $bulan)
                                            <button type="button" 
                                                class="card-period-tab-btn {{ $bIdx === 0 ? 'active' : '' }}" 
                                                data-target-month="{{ $bulan->id }}"
                                                onclick="switchCardMonth(this, '{{ $jurusan->id }}', '{{ $bulan->id }}')">
                                                {{ \Carbon\CarbonImmutable::parse($bulan->bulan_mulai)->translatedFormat('M Y') }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Month Panels -->
                                @foreach($bulanTampil as $bIdx => $bulan)
                                    @php
                                        $data = $matriksKetersediaan[$jurusan->id][$bulan->id] ?? null;
                                        $status = $data['status'] ?? \App\Enums\StatusKetersediaanEnum::Tutup;
                                        $sisa = $data['sisa_kuota'] ?? 0;
                                        $total = $data['total_kuota'] ?? 0;
                                        $terisi = $data['terisi'] ?? 0;
                                        $persen = $data['persen_terisi'] ?? 0;
                                        $isWaiting = $data['is_waiting_list'] ?? false;
                                        $buka = \Carbon\CarbonImmutable::parse($bulan->tgl_buka)->translatedFormat('d M Y');
                                        $bulanMulaiStr = \Carbon\CarbonImmutable::parse($bulan->bulan_mulai)->translatedFormat('F Y');
                                    @endphp
                                    <div class="card-month-panel" 
                                        id="panel-j{{ $jurusan->id }}-b{{ $bulan->id }}"
                                        style="{{ $bIdx === 0 ? '' : 'display: none;' }}"
                                        data-jurusan-id="{{ $jurusan->id }}"
                                        data-bulan-id="{{ $bulan->id }}"
                                        data-status="{{ $status->value }}"
                                        data-sisa="{{ $sisa }}"
                                        data-total="{{ $total }}"
                                        data-terisi="{{ $terisi }}"
                                        data-persen="{{ $persen }}"
                                        data-is-waiting="{{ $isWaiting ? '1' : '0' }}"
                                        data-buka-str="{{ $buka }}"
                                        data-bulan-str="{{ $bulanMulaiStr }}">
                                        
                                        <!-- Capacity Box Surface -->
                                        <div class="card-capacity-box">
                                            <div class="capacity-header">
                                                <span class="capacity-label">Mulai: {{ $bulanMulaiStr }}</span>
                                                <span class="capacity-status-pill status-pill-target {{ $status === \App\Enums\StatusKetersediaanEnum::Tersedia ? ($isWaiting ? 'waiting' : 'tersedia') : ($status === \App\Enums\StatusKetersediaanEnum::Segera ? 'segera' : 'tutup') }}">
                                                    @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                                        @if($isWaiting)
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                            <span>Kuota Penuh (Waiting List)</span>
                                                        @else
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                            <span>Tersedia</span>
                                                        @endif
                                                    @elseif($status === \App\Enums\StatusKetersediaanEnum::Segera)
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                                        <span>Segera Dibuka</span>
                                                    @else
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                        <span>Tutup</span>
                                                    @endif
                                                </span>
                                            </div>

                                            <!-- Visual Capacity Bar -->
                                            <div class="capacity-bar-track">
                                                <div class="capacity-bar-fill {{ $status === \App\Enums\StatusKetersediaanEnum::Tersedia ? ($isWaiting ? 'waiting' : 'tersedia') : ($status === \App\Enums\StatusKetersediaanEnum::Segera ? 'waiting' : 'tutup') }}" 
                                                    style="width: {{ $persen }}%;"></div>
                                            </div>

                                            <div class="capacity-meta-row">
                                                <span class="capacity-meta-left">
                                                    @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                                        @if($isWaiting)
                                                            <span style="color: #b45309; font-weight: 600;">Pendaftaran masuk antrean tunggu</span>
                                                        @else
                                                            Sisa: <strong class="sisa-val">{{ $sisa }}</strong> dari {{ $total }} Kursi
                                                        @endif
                                                    @elseif($status === \App\Enums\StatusKetersediaanEnum::Segera)
                                                        Buka: <strong>{{ $buka }}</strong>
                                                    @else
                                                        Pendaftaran Ditutup
                                                    @endif
                                                </span>
                                                <span class="capacity-meta-right">
                                                    @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                                        <span class="persen-val">{{ $persen }}%</span> Terisi
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Action Button CTA -->
                                        <div>
                                            @if($status === \App\Enums\StatusKetersediaanEnum::Tersedia)
                                                <a href="{{ url('/register?bulan=' . $bulan->id . '&jurusan=' . $jurusan->id . '&tipe=mandiri&jumlah=1') }}" 
                                                   class="card-action-btn btn-cta-target {{ $isWaiting ? 'btn-waiting' : 'btn-daftar' }}">
                                                    <span class="btn-text">
                                                        {{ $isWaiting ? 'Daftar Waiting List' : 'Daftar Sekarang ➔' }}
                                                    </span>
                                                </a>
                                            @else
                                                <button type="button" class="card-action-btn btn-disabled" disabled>
                                                    {{ $status === \App\Enums\StatusKetersediaanEnum::Segera ? 'Segera Dibuka' : 'Pendaftaran Ditutup' }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p style="color: var(--gray-600); text-align: center; width: 100%; padding: 2rem;">Belum ada master jurusan Mahasiswa yang aktif.</p>
                    @endforelse
                    </div>

                    <button type="button" class="carousel-nav-btn next" id="carousel-next-mahasiswa" aria-label="Geser ke kanan" title="Geser ke kanan">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- =====================================================================
     3. ALUR MAGANG SECTION (MATCHING FIGMA NODE 562-55102: 1440 Fill x 530)
     ===================================================================== -->
<section id="alur-section" class="section-alur-magang">
    <div class="container">
        <div class="alur-heading">
            <h2>Alur magang</h2>
            <p>Empat langkah dari pendaftaran sampai lembar nilai.</p>
        </div>

        <div class="alur-steps-row">
            <!-- Step 01 -->
            <a href="{{ route('panduan-alur') }}?step=1" class="alur-step-card" title="Pelajari Tahap Buat Akun">
                <div>
                    <div class="alur-card-top">
                        <div class="alur-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        </div>
                        <span class="alur-step-number">01</span>
                    </div>
                    <h3>Buat Akun</h3>
                    <p>Isi profil dan pilih bidang sesuai jurusanmu.</p>
                </div>
            </a>

            <!-- Step 02 -->
            <a href="{{ route('panduan-alur') }}?step=2" class="alur-step-card" title="Pelajari Tahap Kirim Berkas">
                <div>
                    <div class="alur-card-top">
                        <div class="alur-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        </div>
                        <span class="alur-step-number">02</span>
                    </div>
                    <h3>Kirim Berkas</h3>
                    <p>Unggah surat pengantar, CV, dan transkrip. Admin memutuskan hasilnya.</p>
                </div>
            </a>

            <!-- Step 03 -->
            <a href="{{ route('panduan-alur') }}?step=3" class="alur-step-card" title="Pelajari Tahap Kerjakan Tugas">
                <div>
                    <div class="alur-card-top">
                        <div class="alur-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="m9 14 2 2 4-4"/></svg>
                        </div>
                        <span class="alur-step-number">03</span>
                    </div>
                    <h3>Kerjakan Tugas</h3>
                    <p>Terima tugas, unggah progres, dan isi logbook harian.</p>
                </div>
            </a>

            <!-- Step 04 -->
            <a href="{{ route('panduan-alur') }}?step=4" class="alur-step-card" title="Pelajari Tahap Terima Nilai">
                <div>
                    <div class="alur-card-top">
                        <div class="alur-icon-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                        </div>
                        <span class="alur-step-number">04</span>
                    </div>
                    <h3>Terima Nilai</h3>
                    <p>Unduh Lembar Nilai Magang (PDF) dari Pembimbing.</p>
                </div>
            </a>
        </div>

        <!-- Interactive Showcase Action Bar (Terpusat & Menyatu) -->
        <div class="alur-interactive-showcase-bar">
            <div class="alur-showcase-info">
                <div class="alur-showcase-badge">
                    <span class="alur-pulse-dot"></span>
                    <span>Peta Alur &amp; Panduan Lengkap</span>
                </div>
                <p class="alur-showcase-text">
                    Pelajari rincian 4 fase, kelengkapan berkas wajib, SLA verifikasi 1–3 hari, dan simulasi jalur SMK / Mahasiswa.
                </p>
            </div>
            <a href="{{ route('panduan-alur') }}" class="btn-panduan-showcase btn-panduan">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                <span>Eksplorasi Peta Alur Interaktif &rarr;</span>
            </a>
        </div>
    </div>
</section>

<!-- =====================================================================
     4. PENGUMUMAN SECTION (MODERN EDITORIAL GOVTECH SPEC)
     ===================================================================== -->
<section id="pengumuman-section" class="section-pengumuman" aria-labelledby="pengumuman-heading">
    <div class="container">
        <!-- Section Header -->
        <div class="pengumuman-header-wrap">
            <div class="pengumuman-eyebrow">
                <span class="pengumuman-pulse-dot" aria-hidden="true"></span>
                <span>Warta Resmi &amp; Informasi Terkini</span>
            </div>
            <h2 id="pengumuman-heading" class="pengumuman-title">Pengumuman &amp; Warta Magang</h2>
            <p class="pengumuman-subtitle">
                Pantau pengumuman pembukaan gelombang, persyaratan administrasi, dan pengumuman hasil seleksi penempatan di Diskominfo Sleman.
            </p>
        </div>

        <!-- Filter Tab Kategori -->
        <div class="pengumuman-filter-bar" role="tablist" aria-label="Kategori Pengumuman">
            <button type="button" class="pengumuman-tab-btn active" data-filter="all" role="tab" aria-selected="true">
                Semua Warta <span class="pengumuman-tab-badge">3</span>
            </button>
            <button type="button" class="pengumuman-tab-btn" data-filter="pendaftaran" role="tab" aria-selected="false">
                Pendaftaran &amp; Kuota <span class="pengumuman-tab-badge">1</span>
            </button>
            <button type="button" class="pengumuman-tab-btn" data-filter="persyaratan" role="tab" aria-selected="false">
                Persyaratan Berkas <span class="pengumuman-tab-badge">1</span>
            </button>
            <button type="button" class="pengumuman-tab-btn" data-filter="hasil" role="tab" aria-selected="false">
                Hasil Seleksi <span class="pengumuman-tab-badge">1</span>
            </button>
        </div>

        <!-- Pengumuman Cards Grid -->
        <div class="pengumuman-grid" id="pengumuman-grid-container">
            <!-- Card 1: Pendaftaran -->
            <article class="pengumuman-card" data-category="pendaftaran" tabindex="0" role="button" aria-label="Buka detail pengumuman Pendaftaran magang gelombang 2 resmi dibuka"
                     data-title="Pendaftaran Magang Gelombang 2 Resmi Dibuka"
                     data-date="28 Sep 2026"
                     data-nosurat="500.12/KOMINFO/IX/2026"
                     data-content="Dinas Komunikasi dan Informatika Kabupaten Sleman secara resmi membuka pendaftaran Praktik Kerja Lapangan (PKL) dan Magang Riset untuk Gelombang 2 Periode Pelaksanaan November–Desember 2026. Seluruh pendaftar mandiri maupun perwakilan kelompok wajib melengkapi berkas administrasi dan mengajukan jurusan yang bersesuaian dengan kuota yang tersedia sebelum 15 Oktober 2026 pukul 23:59 WIB.">
                <div>
                    <div class="pengumuman-meta-top">
                        <span class="pengumuman-category-tag tag-blue">Pendaftaran</span>
                        <span class="pengumuman-pin-badge">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            Baru
                        </span>
                    </div>
                    <h3>Pendaftaran magang gelombang 2 resmi dibuka</h3>
                    <p>Pendaftaran berlangsung 1 sampai 15 Oktober 2026 untuk semua bidang penempatan Diskominfo Sleman.</p>
                </div>
                <div class="pengumuman-card-footer">
                    <div class="pengumuman-date-chip">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>28 Sep 2026</span>
                    </div>
                    <span class="pengumuman-action-link">
                        Baca Selengkapnya
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </span>
                </div>
            </article>

            <!-- Card 2: Persyaratan -->
            <article class="pengumuman-card" data-category="persyaratan" tabindex="0" role="button" aria-label="Buka detail pengumuman Persyaratan berkas pendaftaran"
                     data-title="Ketentuan &amp; Standar Berkas Pendaftaran Magang"
                     data-date="20 Sep 2026"
                     data-nosurat="500.10/SE-BERKAS/IX/2026"
                     data-content="Guna menjamin kelancaran telaah verifikasi administrasi (SLA 1–3 hari kerja), seluruh calon peserta diwajibkan mengunggah 3 dokumen utama dalam format PDF (maks. 2 MB per berkas): 1) Surat Pengantar Resmi bertandatangan basah/elektronik dari Dekanat/Kepala Sekolah; 2) Curriculum Vitae (CV) terbaru yang memuat portofolio keahlian; 3) Transkrip Nilai / Rapor akademik terakhir dengan legalitas sah.">
                <div>
                    <div class="pengumuman-meta-top">
                        <span class="pengumuman-category-tag tag-amber">Persyaratan</span>
                        <span class="pengumuman-date-chip">Wajib Disimak</span>
                    </div>
                    <h3>Persyaratan berkas pendaftaran &amp; verifikasi</h3>
                    <p>Siapkan surat pengantar institusi, CV, dan transkrip nilai dalam format PDF berukuran maksimal 2 MB.</p>
                </div>
                <div class="pengumuman-card-footer">
                    <div class="pengumuman-date-chip">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>20 Sep 2026</span>
                    </div>
                    <span class="pengumuman-action-link">
                        Baca Selengkapnya
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </span>
                </div>
            </article>

            <!-- Card 3: Hasil Seleksi -->
            <article class="pengumuman-card" data-category="hasil" tabindex="0" role="button" aria-label="Buka detail pengumuman Hasil seleksi gelombang 1"
                     data-title="Pengumuman Kelulusan Seleksi Administrasi Gelombang 1"
                     data-date="12 Sep 2026"
                     data-nosurat="500.08/PENG-KELULUSAN/IX/2026"
                     data-content="Berdasarkan hasil verifikasi berkas dan ketersediaan kuota pembimbing bidang, peserta yang dinyatakan DITERIMA pada Gelombang 1 diwajibkan untuk: 1) Masuk ke dashboard akun portal; 2) Memeriksa bidang penempatan dan kontak mentor pendamping; 3) Mengunduh Surat Penerimaan Resmi (SK Penerimaan) ber-QR Code untuk diserahkan ke pihak kampus/sekolah.">
                <div>
                    <div class="pengumuman-meta-top">
                        <span class="pengumuman-category-tag tag-green">Hasil Seleksi</span>
                        <span class="pengumuman-date-chip">Resmi</span>
                    </div>
                    <h3>Hasil seleksi gelombang 1 diumumkan</h3>
                    <p>Peserta yang diterima dapat masuk ke dashboard untuk melihat bidang penempatan dan mengunduh SK resmi.</p>
                </div>
                <div class="pengumuman-card-footer">
                    <div class="pengumuman-date-chip">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>12 Sep 2026</span>
                    </div>
                    <span class="pengumuman-action-link">
                        Baca Selengkapnya
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </span>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- Pop-up Modal Detail Warta Resmi (Accessible Modal) -->
<div id="pengumuman-modal" class="pengumuman-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal-warta-title" aria-hidden="true">
    <div class="pengumuman-modal-card">
        <button type="button" class="modal-close-btn" id="modal-close-btn" aria-label="Tutup jendela warta">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        <!-- Kop Surat Dinas -->
        <div class="modal-kop-surat">
            <img src="{{ asset('images/logo-sleman.png') }}" alt="Logo Kabupaten Sleman" class="modal-kop-logo" onerror="this.style.display='none'">
            <div class="modal-kop-text">
                <h4>Pemerintah Kabupaten Sleman</h4>
                <p>Dinas Komunikasi dan Informatika &bull; Bidang Aplikasi Informatika</p>
                <p id="modal-warta-nosurat" style="font-weight: 600; color: #2563EB; margin-top: 2px;">Nomor: -</p>
            </div>
        </div>

        <!-- Body Pengumuman -->
        <h3 id="modal-warta-title" style="font-size: 1.35rem; font-weight: 800; color: #0F172A; margin-bottom: 0.5rem; line-height: 1.35;">-</h3>
        <p id="modal-warta-date" style="font-size: 0.8125rem; color: #64748B; margin-bottom: 1.25rem; font-weight: 600;">-</p>
        
        <div id="modal-warta-content" class="modal-content-body">
            -
        </div>

        <div style="margin-top: 2rem; padding-top: 1.25rem; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end; gap: 0.75rem;">
            <button type="button" class="btn-panduan" id="modal-close-btn-footer" style="padding: 0.55rem 1.25rem; font-size: 0.875rem; border-radius: 0.5rem;">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- =====================================================================
     5. LAYANAN SECTION (ENTERPRISE ACTION TILES SPEC)
     ===================================================================== -->
<section id="layanan-section" class="section-layanan" aria-labelledby="layanan-heading">
    <div class="container">
        <!-- Section Header -->
        <div class="layanan-header-wrap">
            <div class="layanan-eyebrow">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Akses Terpadu &amp; Layanan Mandiri</span>
            </div>
            <h2 id="layanan-heading" class="layanan-title">Pusat Layanan &amp; Fitur Utama Magang</h2>
            <p class="layanan-subtitle">
                Akses cepat menuju seluruh instrumen magang mulai dari pendaftaran kuota, pelacakan berkas, hingga sistem logbook harian dan sertifikasi resmi.
            </p>
        </div>

        <!-- 4 Enterprise Action Tiles -->
        <div class="layanan-grid">
            <!-- Tile 1: Pendaftaran Magang -->
            <a href="#kuota-section" class="layanan-card" style="--card-glow: rgba(37, 99, 235, 0.12);" id="layanan-card-daftar">
                <div>
                    <div class="layanan-card-header">
                        <div class="layanan-icon-box icon-box-blue">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="12" y1="18" x2="12" y2="12"/>
                                <line x1="9" y1="15" x2="15" y2="15"/>
                            </svg>
                        </div>
                        <span class="layanan-target-badge target-badge-calon">Calon Peserta</span>
                    </div>
                    <div class="layanan-card-body">
                        <h3>Daftar Magang</h3>
                        <p>Pilih periode dan ajukan pendaftaran mandiri atau perwakilan kelompok dengan pengecekan kuota real-time.</p>
                    </div>
                </div>
                <div class="layanan-card-cta">
                    <span>Lihat Kuota &amp; Daftar</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
            </a>

            <!-- Tile 2: Cek Status -->
            <a href="{{ route('login') }}" class="layanan-card" style="--card-glow: rgba(217, 119, 6, 0.12);" id="layanan-card-status">
                <div>
                    <div class="layanan-card-header">
                        <div class="layanan-icon-box icon-box-amber">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                <path d="m9 11 2 2 4-4"/>
                            </svg>
                        </div>
                        <span class="layanan-target-badge target-badge-status">Pendaftar</span>
                    </div>
                    <div class="layanan-card-body">
                        <h3>Cek Status Berkas</h3>
                        <p>Pantau progres telaah administrasi, posisi nomor antrean waiting list, dan unduh SK Diterima resmi.</p>
                    </div>
                </div>
                <div class="layanan-card-cta">
                    <span>Masuk &amp; Pantau Status</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
            </a>

            <!-- Tile 3: Logbook & Presensi -->
            <a href="{{ route('login') }}" class="layanan-card" style="--card-glow: rgba(5, 150, 105, 0.12);" id="layanan-card-logbook">
                <div>
                    <div class="layanan-card-header">
                        <div class="layanan-icon-box icon-box-emerald">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                <line x1="6" y1="8" x2="8" y2="8"/>
                                <line x1="6" y1="12" x2="8" y2="12"/>
                            </svg>
                        </div>
                        <span class="layanan-target-badge target-badge-aktif">Peserta Aktif</span>
                    </div>
                    <div class="layanan-card-body">
                        <h3>Logbook Harian</h3>
                        <p>Akses pelaporan jurnal aktivitas, unggah dokumentasi proyek, dan terima reviu mingguan dari pembimbing.</p>
                    </div>
                </div>
                <div class="layanan-card-cta">
                    <span>Akses Logbook Harian</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
            </a>

            <!-- Tile 4: Lembar Nilai & Sertifikat -->
            <a href="{{ route('login') }}" class="layanan-card" style="--card-glow: rgba(124, 58, 237, 0.12);" id="layanan-card-nilai">
                <div>
                    <div class="layanan-card-header">
                        <div class="layanan-icon-box icon-box-purple">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="8" r="6"/>
                                <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
                                <polyline points="9 8 11 10 15 6"/>
                            </svg>
                        </div>
                        <span class="layanan-target-badge target-badge-alumni">Selesai Magang</span>
                    </div>
                    <div class="layanan-card-body">
                        <h3>Lembar Nilai &amp; SK</h3>
                        <p>Unduh lembar nilai akhir berbasis rubrik kompetensi 100 poin dan sertifikat tanda selesai bimbingan dinas.</p>
                    </div>
                </div>
                <div class="layanan-card-cta">
                    <span>Unduh Lembar Nilai</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    let carouselSmk = null;
    let carouselMahasiswa = null;
    let currentActiveTab = 'smk'; // 'smk' | 'mahasiswa'
    let currentRegMode = 'mandiri'; // 'mandiri' | 'kelompok'
    let currentMemberCount = 1;

    function setRegistrationMode(mode) {
        currentRegMode = mode;
        const btnMandiri = document.getElementById('mode-btn-mandiri');
        const btnKelompok = document.getElementById('mode-btn-kelompok');
        const memberWrap = document.getElementById('member-selector-wrap');
        const indicatorText = document.getElementById('quota-indicator-text');
        const bannerKelompok = document.getElementById('banner-kelompok-edukasi');

        if (mode === 'mandiri') {
            currentMemberCount = 1;
            if (btnMandiri) btnMandiri.classList.add('active');
            if (btnKelompok) btnKelompok.classList.remove('active');
            if (memberWrap) memberWrap.style.display = 'none';
            if (bannerKelompok) bannerKelompok.style.display = 'none';
            if (indicatorText) {
                indicatorText.textContent = 'Pendaftaran Mandiri (1 Orang) · Kuota otomatis dicek untuk 1 calon peserta';
            }
        } else {
            if (currentMemberCount < 2) currentMemberCount = 2;
            if (btnKelompok) btnKelompok.classList.add('active');
            if (btnMandiri) btnMandiri.classList.remove('active');
            if (memberWrap) memberWrap.style.display = 'flex';
            if (bannerKelompok) bannerKelompok.style.display = 'flex';
            updateMemberPillActive();
            if (indicatorText) {
                indicatorText.textContent = `Pendaftaran Kelompok (${currentMemberCount} Orang) · Kuota otomatis dicek untuk ${currentMemberCount} calon peserta`;
            }
        }

        recalculateAllCards();
    }

    function setMemberCount(count) {
        currentMemberCount = parseInt(count, 10);
        updateMemberPillActive();
        const indicatorText = document.getElementById('quota-indicator-text');
        if (indicatorText) {
            indicatorText.textContent = `Pendaftaran Kelompok (${currentMemberCount} Orang) · Kuota otomatis dicek untuk ${currentMemberCount} calon peserta`;
        }
        recalculateAllCards();
    }

    function updateMemberPillActive() {
        document.querySelectorAll('.member-pill-btn').forEach(btn => {
            if (parseInt(btn.getAttribute('data-count'), 10) === currentMemberCount) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    function switchCardMonth(btn, jurusanId, targetBulanId) {
        const card = btn.closest('.figma-quota-card');
        if (!card) return;

        // Update active period tab in this card
        card.querySelectorAll('.card-period-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Show target month panel, hide others
        card.querySelectorAll('.card-month-panel').forEach(panel => {
            if (panel.getAttribute('data-bulan-id') === String(targetBulanId)) {
                panel.style.display = 'block';
            } else {
                panel.style.display = 'none';
            }
        });
    }

    function recalculateAllCards() {
        const panels = document.querySelectorAll('.card-month-panel');
        panels.forEach(panel => {
            const status = panel.getAttribute('data-status');
            const sisa = parseInt(panel.getAttribute('data-sisa'), 10) || 0;
            const total = parseInt(panel.getAttribute('data-total'), 10) || 0;
            const isWaiting = panel.getAttribute('data-is-waiting') === '1';
            const bulanId = panel.getAttribute('data-bulan-id');
            const jurusanId = panel.getAttribute('data-jurusan-id');

            const pill = panel.querySelector('.status-pill-target');
            const barFill = panel.querySelector('.capacity-bar-fill');
            const metaLeft = panel.querySelector('.capacity-meta-left');
            const btnLink = panel.querySelector('.btn-cta-target');
            const btnText = panel.querySelector('.btn-text');

            if (status === 'tersedia') {
                const hasEnoughQuota = (sisa >= currentMemberCount);

                if (currentRegMode === 'mandiri') {
                    if (isWaiting || sisa === 0) {
                        if (pill) {
                            pill.className = 'capacity-status-pill status-pill-target waiting';
                            pill.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> <span>Kuota Penuh (Waiting List)</span>`;
                        }
                        if (metaLeft) {
                            metaLeft.innerHTML = `<span style="color: #b45309; font-weight: 600;">Kuota periode ini terisi. Masuk Waiting List:</span>`;
                        }
                        if (barFill) barFill.className = 'capacity-bar-fill waiting';
                        if (btnLink) {
                            btnLink.className = 'card-action-btn btn-cta-target btn-waiting';
                            btnLink.href = `/register?bulan=${bulanId}&jurusan=${jurusanId}&tipe=mandiri&jumlah=1`;
                        }
                        if (btnText) btnText.textContent = 'Daftar Waiting List (1 Orang) ➔';
                    } else {
                        if (pill) {
                            pill.className = 'capacity-status-pill status-pill-target tersedia';
                            pill.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Tersedia (${sisa} Kursi)</span>`;
                        }
                        if (metaLeft) {
                            metaLeft.innerHTML = `Sisa: <strong class="sisa-val">${sisa}</strong> dari ${total} Kursi`;
                        }
                        if (barFill) barFill.className = 'capacity-bar-fill tersedia';
                        if (btnLink) {
                            btnLink.className = 'card-action-btn btn-cta-target btn-daftar';
                            btnLink.href = `/register?bulan=${bulanId}&jurusan=${jurusanId}&tipe=mandiri&jumlah=1`;
                        }
                        if (btnText) btnText.textContent = 'Daftar Sekarang ➔';
                    }
                } else {
                    // Mode Kelompok (Tim)
                    if (hasEnoughQuota) {
                        if (pill) {
                            pill.className = 'capacity-status-pill status-pill-target tersedia';
                            pill.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Kuota Cukup (${sisa} Kursi Tersedia)</span>`;
                        }
                        if (metaLeft) {
                            metaLeft.innerHTML = `Sisa: <strong class="sisa-val">${sisa}</strong> dari ${total} Kursi (Cukup untuk tim ${currentMemberCount} org)`;
                        }
                        if (barFill) barFill.className = 'capacity-bar-fill tersedia';
                        if (btnLink) {
                            btnLink.className = 'card-action-btn btn-cta-target btn-daftar';
                            btnLink.href = `/register?bulan=${bulanId}&jurusan=${jurusanId}&tipe=kelompok&jumlah=${currentMemberCount}`;
                        }
                        if (btnText) btnText.textContent = `Daftar Kelompok (${currentMemberCount} Orang) ➔`;
                    } else if (sisa > 0) {
                        // Kuota sisa ADA, namun tidak cukup untuk seluruh rombongan kelompok
                        const kekurangan = currentMemberCount - sisa;
                        if (pill) {
                            pill.className = 'capacity-status-pill status-pill-target kurang';
                            pill.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> <span>Tersisa ${sisa} Kursi · Tim Butuh ${currentMemberCount}</span>`;
                        }
                        if (metaLeft) {
                            metaLeft.innerHTML = `<span style="color: #b45309; font-weight: 600;">Sisa ${sisa} kursi (kurang ${kekurangan} kursi untuk tim). Masuk Waiting List:</span>`;
                        }
                        if (barFill) barFill.className = 'capacity-bar-fill kurang';
                        if (btnLink) {
                            btnLink.className = 'card-action-btn btn-cta-target btn-waiting';
                            btnLink.href = `/register?bulan=${bulanId}&jurusan=${jurusanId}&tipe=kelompok&jumlah=${currentMemberCount}`;
                        }
                        if (btnText) btnText.textContent = `Daftar Waiting List (${currentMemberCount} Orang) ➔`;
                    } else {
                        // Kuota benar-benar habis (0 kursi)
                        if (pill) {
                            pill.className = 'capacity-status-pill status-pill-target waiting';
                            pill.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> <span>Kuota Penuh (Waiting List)</span>`;
                        }
                        if (metaLeft) {
                            metaLeft.innerHTML = `<span style="color: #b45309; font-weight: 600;">0 dari ${total} kursi tersisa. Pendaftaran masuk antrean:</span>`;
                        }
                        if (barFill) barFill.className = 'capacity-bar-fill waiting';
                        if (btnLink) {
                            btnLink.className = 'card-action-btn btn-cta-target btn-waiting';
                            btnLink.href = `/register?bulan=${bulanId}&jurusan=${jurusanId}&tipe=kelompok&jumlah=${currentMemberCount}`;
                        }
                        if (btnText) btnText.textContent = `Daftar Waiting List (${currentMemberCount} Orang) ➔`;
                    }
                }
            }
        });
    }

    function scrollActiveCarousel(direction) {
        const trackId = (currentActiveTab === 'smk') ? 'carousel-track-smk' : 'carousel-track-mahasiswa';
        const track = document.getElementById(trackId);
        if (!track) return;

        const activeObj = (currentActiveTab === 'smk') ? carouselSmk : carouselMahasiswa;
        if (activeObj && typeof activeObj.pauseWithTimeout === 'function') {
            activeObj.pauseWithTimeout(4000);
        }

        const stepWidth = 394; // 370px card width + 24px gap
        track.scrollBy({ left: direction * stepWidth, behavior: 'smooth' });
    }

    function setupQuotaCarousel(trackId) {
        const track = document.getElementById(trackId);
        if (!track) return null;

        // Clone kartu agar terjadi infinite seamless loop tanpa jeda/patah
        const originalCards = Array.from(track.querySelectorAll('.figma-quota-card'));
        let hasClones = false;
        if (originalCards.length > 1) {
            originalCards.forEach(card => {
                const clone = card.cloneNode(true);
                clone.setAttribute('data-is-clone', 'true');
                track.appendChild(clone);
            });
            hasClones = true;
        }

        let halfScrollWidth = hasClones ? (track.scrollWidth / 2) : track.scrollWidth;
        
        // Posisikan awal di tengah (set kedua) agar bisa langsung mengalir ke kanan
        if (hasClones) {
            track.scrollLeft = halfScrollWidth;
        }

        window.addEventListener('resize', () => {
            if (hasClones) {
                halfScrollWidth = track.scrollWidth / 2;
            }
        });

        let isPaused = false;
        let animId = null;
        let resumeTimeout = null;
        const scrollSpeed = 0.65; // pixels per animation frame

        function step() {
            if (!isPaused && track.offsetParent !== null && hasClones) {
                // Pergerakan kiri ke kanan: scrollLeft berkurang
                track.scrollLeft -= scrollSpeed;

                // Seamless wrap: jika mendekati/mencapai ujung kiri (<= 0), kembali ke halfScrollWidth
                if (track.scrollLeft <= 0) {
                    track.scrollLeft += halfScrollWidth;
                }
            }
            animId = requestAnimationFrame(step);
        }

        const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!prefersReducedMotion && hasClones) {
            animId = requestAnimationFrame(step);
        }

        function pauseWithTimeout(duration = 3500) {
            isPaused = true;
            clearTimeout(resumeTimeout);
            resumeTimeout = setTimeout(() => {
                isPaused = false;
            }, duration);
        }

        // Hover & Touch event listeners (Smart Pause saat pengguna membaca kartu)
        track.addEventListener('mouseenter', () => { isPaused = true; });
        track.addEventListener('mouseleave', () => { isPaused = false; });
        track.addEventListener('touchstart', () => { isPaused = true; }, { passive: true });
        track.addEventListener('touchend', () => { pauseWithTimeout(2500); }, { passive: true });

        // Mouse Drag-to-Scroll support
        let isDown = false;
        let startX = 0;
        let scrollStart = 0;

        track.addEventListener('mousedown', (e) => {
            if (e.target.closest('a, button, input')) return;
            isDown = true;
            isPaused = true;
            startX = e.pageX - track.offsetLeft;
            scrollStart = track.scrollLeft;
        });

        window.addEventListener('mouseup', () => {
            if (isDown) {
                isDown = false;
                pauseWithTimeout(2500);
            }
        });

        track.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - track.offsetLeft;
            const walk = (x - startX) * 1.4;
            track.scrollLeft = scrollStart - walk;

            if (hasClones) {
                if (track.scrollLeft <= 0) {
                    track.scrollLeft += halfScrollWidth;
                    scrollStart += halfScrollWidth;
                } else if (track.scrollLeft >= halfScrollWidth * 2 - track.clientWidth) {
                    track.scrollLeft -= halfScrollWidth;
                    scrollStart -= halfScrollWidth;
                }
            }
        });

        // Arrow button navigation on the sides (Smooth Step per card like reference)
        const prevBtn = prevBtnId ? document.getElementById(prevBtnId) : null;
        const nextBtn = nextBtnId ? document.getElementById(nextBtnId) : null;
        const stepWidth = 394; // 370px card width + 24px gap

        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                pauseWithTimeout(4000);
                track.scrollBy({ left: -stepWidth, behavior: 'smooth' });
                setTimeout(() => {
                    if (hasClones && track.scrollLeft <= 0) {
                        track.scrollLeft += halfScrollWidth;
                    }
                }, 350);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                pauseWithTimeout(4000);
                track.scrollBy({ left: stepWidth, behavior: 'smooth' });
                setTimeout(() => {
                    if (hasClones && track.scrollLeft >= halfScrollWidth) {
                        track.scrollLeft -= halfScrollWidth;
                    }
                }, 350);
            });
        }

        return {
            pause: () => { isPaused = true; },
            resume: () => { isPaused = false; },
            pauseWithTimeout: pauseWithTimeout,
            reset: () => { track.scrollLeft = hasClones ? halfScrollWidth : 0; }
        };
    }

    function switchTab(type) {
        currentActiveTab = type;
        const btnSmk = document.getElementById('tab-btn-smk');
        const btnMahasiswa = document.getElementById('tab-btn-mahasiswa');
        const contentSmk = document.getElementById('tab-content-smk');
        const contentMahasiswa = document.getElementById('tab-content-mahasiswa');

        if (!btnSmk || !btnMahasiswa || !contentSmk || !contentMahasiswa) return;

        if (type === 'smk') {
            btnSmk.classList.add('active');
            btnMahasiswa.classList.remove('active');
            contentSmk.style.display = 'block';
            contentMahasiswa.style.display = 'none';

            if (carouselMahasiswa) carouselMahasiswa.pause();
            if (carouselSmk) carouselSmk.resume();
        } else {
            btnMahasiswa.classList.add('active');
            btnSmk.classList.remove('active');
            contentMahasiswa.style.display = 'block';
            contentSmk.style.display = 'none';

            if (carouselSmk) carouselSmk.pause();
            if (carouselMahasiswa) carouselMahasiswa.resume();
        }
    }

    // Explicitly expose functions to window scope for inline onclick handlers
    window.setRegistrationMode = setRegistrationMode;
    window.setMemberCount = setMemberCount;
    window.switchCardMonth = switchCardMonth;
    window.switchTab = switchTab;


    // Initialize carousels & initial recalculation once DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        carouselSmk = setupQuotaCarousel('carousel-track-smk', 'carousel-prev-smk', 'carousel-next-smk');
        carouselMahasiswa = setupQuotaCarousel('carousel-track-mahasiswa', 'carousel-prev-mahasiswa', 'carousel-next-mahasiswa');

        // Initially pause mahasiswa if SMK is active
        if (carouselMahasiswa) {
            carouselMahasiswa.pause();
        }

        recalculateAllCards();
    });

    // =====================================================================
    // LOGIKA INTERAKTIF SECTION PENGUMUMAN (FILTER TABS & MODAL WARTA)
    // =====================================================================
    document.addEventListener('DOMContentLoaded', function () {
        // 1. FILTER TAB PENGUMUMAN
        const tabBtns = document.querySelectorAll('.pengumuman-tab-btn');
        const cards = document.querySelectorAll('.pengumuman-card');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const targetFilter = this.getAttribute('data-filter');

                // Update active state tab
                tabBtns.forEach(b => {
                    b.classList.remove('active');
                    b.setAttribute('aria-selected', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');

                // Filter cards with smooth visibility
                cards.forEach(card => {
                    const cardCat = card.getAttribute('data-category');
                    if (targetFilter === 'all' || cardCat === targetFilter) {
                        card.style.display = 'flex';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    } else {
                        card.style.display = 'none';
                        card.style.opacity = '0';
                    }
                });
            });
        });

        // 2. MODAL WARTA RESMI PENGUMUMAN
        const modal = document.getElementById('pengumuman-modal');
        const modalCloseBtn = document.getElementById('modal-close-btn');
        const modalCloseFooter = document.getElementById('modal-close-btn-footer');
        const modalTitle = document.getElementById('modal-warta-title');
        const modalDate = document.getElementById('modal-warta-date');
        const modalNoSurat = document.getElementById('modal-warta-nosurat');
        const modalContent = document.getElementById('modal-warta-content');

        function openAnnouncementModal(card) {
            const title = card.getAttribute('data-title') || card.querySelector('h3').textContent;
            const date = card.getAttribute('data-date') || '';
            const noSurat = card.getAttribute('data-nosurat') || '500.12/KOMINFO/2026';
            const content = card.getAttribute('data-content') || card.querySelector('p').textContent;

            if (modalTitle) modalTitle.textContent = title;
            if (modalDate) modalDate.textContent = 'Diterbitkan pada: ' + date;
            if (modalNoSurat) modalNoSurat.textContent = 'Nomor: ' + noSurat;
            if (modalContent) modalContent.textContent = content;

            if (modal) {
                modal.classList.add('open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden'; // cegah scroll latar belakang
                if (modalCloseBtn) modalCloseBtn.focus();
            }
        }

        function closeAnnouncementModal() {
            if (modal) {
                modal.classList.remove('open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
        }

        // Buka modal saat kartu diklik atau di-Enter
        cards.forEach(card => {
            card.addEventListener('click', function () {
                openAnnouncementModal(this);
            });

            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openAnnouncementModal(this);
                }
            });
        });

        if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeAnnouncementModal);
        if (modalCloseFooter) modalCloseFooter.addEventListener('click', closeAnnouncementModal);

        // Tutup modal jika klik di luar box (backdrop)
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeAnnouncementModal();
                }
            });
        }

        // Tutup modal saat tombol Escape ditekan
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && modal.classList.contains('open')) {
                closeAnnouncementModal();
            }
        });
    });
</script>
@endpush
