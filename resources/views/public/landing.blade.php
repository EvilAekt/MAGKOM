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
       2. BIDANG DAN KUOTA HORIZONTAL INTERACTIVE CAROUSEL & MODERN CARDS
       ===================================================================== */
    .quota-controls-bar {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-radius: 1.25rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 2.25rem;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
        display: flex;
        flex-direction: column;
        gap: 1.15rem;
    }

    .quota-control-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.25rem;
        flex-wrap: wrap;
    }

    .quota-control-label {
        font-size: 0.8125rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748B;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .quota-control-group {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #F8FAFC;
        padding: 0.3rem;
        border-radius: 9999px;
        border: 1px solid #E2E8F0;
        flex-wrap: wrap;
    }

    .mode-pill-btn {
        padding: 0.45rem 1.15rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        border-radius: 9999px;
        cursor: pointer;
        background: transparent;
        border: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .mode-pill-btn:hover {
        color: #0751FF;
        background: rgba(7, 81, 255, 0.06);
    }

    .mode-pill-btn.active {
        background: #ffffff;
        color: #0751FF;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(7, 81, 255, 0.16);
        border: 1px solid rgba(7, 81, 255, 0.2);
    }

    .member-selector-wrap {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding-left: 0.5rem;
        margin-left: 0.25rem;
        border-left: 1.5px solid #CBD5E1;
        transition: all 0.3s ease;
    }

    .member-pill-btn {
        padding: 0.3rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 9999px;
        border: 1px solid #CBD5E1;
        background: #ffffff;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .member-pill-btn:hover {
        border-color: #3B82F6;
        color: #0751FF;
    }

    .member-pill-btn.active {
        background: #0751FF;
        color: #ffffff;
        border-color: #0751FF;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(7, 81, 255, 0.25);
    }

    .quota-indicator-badge {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #1D4ED8;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
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

    /* Floating Glassmorphic Arrow Buttons */
    .carousel-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid #CBD5E1;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.12);
        color: #1E293B;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
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
        left: -22px;
    }

    .carousel-nav-btn.next {
        right: -22px;
    }

    @media (max-width: 860px) {
        .carousel-nav-btn.prev { left: 4px; }
        .carousel-nav-btn.next { right: 4px; }
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

    .btn-panduan {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #ffffff;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.925rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-panduan:hover {
        background: rgba(255, 255, 255, 0.18);
        border-color: rgba(255, 255, 255, 0.45);
        color: #ffffff;
    }

    /* =====================================================================
       4. PENGUMUMAN SECTION (MATCHING FIGMA NODE 562-55160)
       ===================================================================== */
    .section-pengumuman {
        padding: 5.5rem 0 5rem;
        background: var(--neutral-0);
    }

    .pengumuman-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.75rem;
    }

    .pengumuman-card {
        background: #ffffff;
        border: 1px solid var(--neutral-400);
        border-radius: 0.875rem;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .pengumuman-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        border-color: var(--blue-600);
    }

    .pengumuman-card h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--neutral-800);
        margin-bottom: 0.65rem;
        line-height: 1.35;
    }

    .pengumuman-card p {
        font-size: 0.875rem;
        color: var(--neutral-600);
        line-height: 1.5;
        margin-bottom: 1.5rem;
    }

    .pengumuman-date {
        color: var(--blue-600);
        font-size: 0.825rem;
        font-weight: 700;
    }

    /* =====================================================================
       5. LAYANAN SECTION (MATCHING FIGMA NODE 562-55179)
       ===================================================================== */
    .section-layanan {
        padding: 5.5rem 0 5rem;
        background: var(--neutral-100);
    }

    .layanan-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    .layanan-card {
        background: #ffffff;
        border: 1px solid var(--neutral-400);
        border-radius: 0.875rem;
        padding: 2.25rem 1.5rem;
        text-align: center;
        text-decoration: none;
        color: var(--neutral-800);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.15rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .layanan-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
        border-color: var(--blue-600);
    }

    .layanan-icon-box {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 0.75rem;
        background: var(--blue-tint);
        color: var(--blue-600);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .layanan-icon-box svg {
        width: 1.75rem;
        height: 1.75rem;
    }

    .layanan-card span {
        font-size: 1rem;
        font-weight: 700;
        color: var(--neutral-800);
    }

    @media (max-width: 992px) {
        .hero-grid {
            grid-template-columns: 1fr;
            gap: 2.5rem;
        }
        .alur-steps-row {
            grid-template-columns: 1fr 1fr;
        }
        .pengumuman-grid {
            grid-template-columns: 1fr;
        }
        .layanan-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 640px) {
        .alur-steps-row {
            grid-template-columns: 1fr;
        }
        .layanan-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    /* Dark Mode Overrides for Landing Page */
    [data-theme="dark"] .figma-quota-card,
    [data-theme="dark"] .pengumuman-card,
    [data-theme="dark"] .layanan-card {
        background: var(--neutral-0, #182234) !important;
        border-color: var(--neutral-200, #26354D) !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
    }

    [data-theme="dark"] .figma-quota-card:hover,
    [data-theme="dark"] .pengumuman-card:hover,
    [data-theme="dark"] .layanan-card:hover {
        border-color: var(--blue-600, #3B82F6) !important;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.45) !important;
    }

    [data-theme="dark"] .card-period-box,
    [data-theme="dark"] .card-capacity-box {
        background: var(--neutral-100, #0F172A) !important;
        border-color: var(--neutral-200, #26354D) !important;
    }

    [data-theme="dark"] .quota-controls-bar {
        background: var(--neutral-0, #182234) !important;
        border-color: var(--neutral-200, #26354D) !important;
    }

    [data-theme="dark"] .quota-control-group {
        background: var(--neutral-100, #0F172A) !important;
        border-color: var(--neutral-200, #26354D) !important;
    }

    [data-theme="dark"] .mode-pill-btn {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .mode-pill-btn.active {
        background: #1E293B !important;
        color: #60A5FA !important;
        border-color: #3B82F6 !important;
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
            <!-- Control Bar: Jalur Pendidikan & Tipe Pendaftaran (Mandiri vs Kelompok) -->
            <div class="quota-controls-bar">
                <div class="quota-control-row">
                    <!-- Tab Switcher SMK vs Mahasiswa (AC-01-1) -->
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                        <span class="quota-control-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            Jalur Pendidikan:
                        </span>
                        <div class="quota-control-group">
                            <button type="button" class="tab-pill-btn active" id="tab-btn-smk" onclick="switchTab('smk')">
                                Jalur SMK (Sekolah Menengah Kejuruan)
                            </button>
                            <button type="button" class="tab-pill-btn" id="tab-btn-mahasiswa" onclick="switchTab('mahasiswa')">
                                Jalur Mahasiswa (D3 / S1)
                            </button>
                        </div>
                    </div>

                    <!-- Mode Pendaftaran: Mandiri vs Kelompok (1, 2, 3, 4 Orang) -->
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                        <span class="quota-control-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            Pendaftaran:
                        </span>
                        <div class="quota-control-group">
                            <button type="button" class="mode-pill-btn active" id="mode-btn-mandiri" onclick="setRegistrationMode('mandiri')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span>Mandiri (1 Orang)</span>
                            </button>
                            <button type="button" class="mode-pill-btn" id="mode-btn-kelompok" onclick="setRegistrationMode('kelompok')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                <span>Kelompok / Tim</span>
                            </button>

                            <!-- Pilihan Anggota Kelompok: 2, 3, 4 Orang -->
                            <div class="member-selector-wrap" id="member-selector-wrap" style="display: none;">
                                <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; margin-right: 0.15rem;">Anggota:</span>
                                <button type="button" class="member-pill-btn active" data-count="2" onclick="setMemberCount(2)">2 Orang</button>
                                <button type="button" class="member-pill-btn" data-count="3" onclick="setMemberCount(3)">3 Orang</button>
                                <button type="button" class="member-pill-btn" data-count="4" onclick="setMemberCount(4)">4 Orang</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feedback Real-Time Indikator -->
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap; padding-top: 0.5rem; border-top: 1px solid #F1F5F9;">
                    <div class="quota-indicator-badge" id="quota-indicator-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span id="quota-indicator-text">Pendaftaran Mandiri (1 Orang) · Kuota otomatis dicek untuk 1 calon peserta</span>
                    </div>
                    <div style="font-size: 0.8125rem; color: #64748B;">
                        Geser kartu atau gunakan tombol panah untuk menelusuri jurusan ➔
                    </div>
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
            <div class="alur-step-card">
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
            </div>

            <!-- Step 02 -->
            <div class="alur-step-card">
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
            </div>

            <!-- Step 03 -->
            <div class="alur-step-card">
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
            </div>

            <!-- Step 04 -->
            <div class="alur-step-card">
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
            </div>
        </div>

        <div>
            <a href="#alur-section" class="btn-panduan">Lihat panduan lengkap</a>
        </div>
    </div>
</section>

<!-- =====================================================================
     4. PENGUMUMAN SECTION (MATCHING FIGMA NODE 562-55160)
     ===================================================================== -->
<section id="pengumuman-section" class="section-pengumuman">
    <div class="container">
        <div class="section-heading-center">
            <h2>Pengumuman</h2>
        </div>

        <div class="pengumuman-grid">
            <div class="pengumuman-card">
                <div>
                    <h3>Pendaftaran magang gelombang 2 resmi dibuka</h3>
                    <p>Pendaftaran berlangsung 1 sampai 15 Oktober 2026 untuk semua bidang.</p>
                </div>
                <div class="pengumuman-date">28 Sep 2026</div>
            </div>

            <div class="pengumuman-card">
                <div>
                    <h3>Persyaratan berkas pendaftaran</h3>
                    <p>Siapkan surat pengantar institusi, CV, dan transkrip dalam format PDF.</p>
                </div>
                <div class="pengumuman-date">20 Sep 2026</div>
            </div>

            <div class="pengumuman-card">
                <div>
                    <h3>Hasil seleksi gelombang 1</h3>
                    <p>Peserta yang diterima dapat masuk ke dashboard dan melihat penempatan.</p>
                </div>
                <div class="pengumuman-date">12 Sep 2026</div>
            </div>
        </div>
    </div>
</section>

<!-- =====================================================================
     5. LAYANAN SECTION (MATCHING FIGMA NODE 562-55179)
     ===================================================================== -->
<section id="layanan-section" class="section-layanan">
    <div class="container">
        <div class="section-heading-center">
            <h2>Layanan</h2>
        </div>

        <div class="layanan-grid">
            <a href="#kuota-section" class="layanan-card">
                <div class="layanan-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="12" y1="18" x2="12" y2="12"/>
                        <line x1="9" y1="15" x2="15" y2="15"/>
                    </svg>
                </div>
                <span>Daftar Magang</span>
            </a>

            <a href="{{ route('login') }}" class="layanan-card">
                <div class="layanan-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
                <span>Cek Status</span>
            </a>

            <a href="{{ route('login') }}" class="layanan-card">
                <div class="layanan-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                </div>
                <span>Logbook</span>
            </a>

            <a href="{{ route('login') }}" class="layanan-card">
                <div class="layanan-icon-box">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="6"/>
                        <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
                    </svg>
                </div>
                <span>Lembar Nilai</span>
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    let carouselSmk = null;
    let carouselMahasiswa = null;
    let currentRegMode = 'mandiri'; // 'mandiri' | 'kelompok'
    let currentMemberCount = 1;

    function setRegistrationMode(mode) {
        currentRegMode = mode;
        const btnMandiri = document.getElementById('mode-btn-mandiri');
        const btnKelompok = document.getElementById('mode-btn-kelompok');
        const memberWrap = document.getElementById('member-selector-wrap');
        const indicatorText = document.getElementById('quota-indicator-text');

        if (mode === 'mandiri') {
            currentMemberCount = 1;
            if (btnMandiri) btnMandiri.classList.add('active');
            if (btnKelompok) btnKelompok.classList.remove('active');
            if (memberWrap) memberWrap.style.display = 'none';
            if (indicatorText) {
                indicatorText.textContent = 'Pendaftaran Mandiri (1 Orang) · Kuota otomatis dicek untuk 1 calon peserta';
            }
        } else {
            if (currentMemberCount < 2) currentMemberCount = 2;
            if (btnKelompok) btnKelompok.classList.add('active');
            if (btnMandiri) btnMandiri.classList.remove('active');
            if (memberWrap) memberWrap.style.display = 'inline-flex';
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
            const isWaiting = panel.getAttribute('data-is-waiting') === '1';
            const bulanId = panel.getAttribute('data-bulan-id');
            const jurusanId = panel.getAttribute('data-jurusan-id');

            const pill = panel.querySelector('.status-pill-target');
            const barFill = panel.querySelector('.capacity-bar-fill');
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
                        if (barFill) barFill.className = 'capacity-bar-fill waiting';
                        if (btnLink) {
                            btnLink.className = 'card-action-btn btn-cta-target btn-waiting';
                            btnLink.href = `/register?bulan=${bulanId}&jurusan=${jurusanId}&tipe=mandiri&jumlah=1`;
                        }
                        if (btnText) btnText.textContent = 'Daftar Waiting List';
                    } else {
                        if (pill) {
                            pill.className = 'capacity-status-pill status-pill-target tersedia';
                            pill.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Tersedia (${sisa} Kursi)</span>`;
                        }
                        if (barFill) barFill.className = 'capacity-bar-fill tersedia';
                        if (btnLink) {
                            btnLink.className = 'card-action-btn btn-cta-target btn-daftar';
                            btnLink.href = `/register?bulan=${bulanId}&jurusan=${jurusanId}&tipe=mandiri&jumlah=1`;
                        }
                        if (btnText) btnText.textContent = 'Daftar Sekarang ➔';
                    }
                } else {
                    // Mode Kelompok
                    if (hasEnoughQuota) {
                        if (pill) {
                            pill.className = 'capacity-status-pill status-pill-target tersedia';
                            pill.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>Kuota Cukup (${sisa} Kursi)</span>`;
                        }
                        if (barFill) barFill.className = 'capacity-bar-fill tersedia';
                        if (btnLink) {
                            btnLink.className = 'card-action-btn btn-cta-target btn-daftar';
                            btnLink.href = `/register?bulan=${bulanId}&jurusan=${jurusanId}&tipe=kelompok&jumlah=${currentMemberCount}`;
                        }
                        if (btnText) btnText.textContent = `Daftar Kelompok (${currentMemberCount} Orang) ➔`;
                    } else {
                        // Kuota tidak mencukupi untuk seluruh anggota kelompok -> Masuk Waiting List
                        if (pill) {
                            pill.className = 'capacity-status-pill status-pill-target kurang';
                            pill.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> <span>Kuota Penuh (Waiting List)</span>`;
                        }
                        if (barFill) barFill.className = 'capacity-bar-fill kurang';
                        if (btnLink) {
                            btnLink.className = 'card-action-btn btn-cta-target btn-waiting';
                            btnLink.href = `/register?bulan=${bulanId}&jurusan=${jurusanId}&tipe=kelompok&jumlah=${currentMemberCount}`;
                        }
                        if (btnText) btnText.textContent = `Daftar Waiting List (${currentMemberCount} Orang)`;
                    }
                }
            }
        });
    }

    function setupQuotaCarousel(trackId, prevBtnId, nextBtnId) {
        const track = document.getElementById(trackId);
        const prevBtn = document.getElementById(prevBtnId);
        const nextBtn = document.getElementById(nextBtnId);

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

        // Arrow button navigation (Smooth Step per card)
        const stepWidth = 394; // 370px card width + 24px gap

        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                pauseWithTimeout(4000);
                track.scrollBy({ left: -stepWidth, behavior: 'smooth' });
                // Check wrap-around boundary
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
                // Check wrap-around boundary
                setTimeout(() => {
                    if (hasClones && track.scrollLeft >= halfScrollWidth) {
                        track.scrollLeft -= halfScrollWidth;
                    }
                }, 350);
            });
        }

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

        return {
            pause: () => { isPaused = true; },
            resume: () => { isPaused = false; },
            reset: () => { track.scrollLeft = hasClones ? halfScrollWidth : 0; }
        };
    }

    function switchTab(type) {
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
</script>
@endpush
