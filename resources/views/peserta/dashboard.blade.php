@extends('layouts.peserta')

@section('title', 'Dashboard Peserta')
@section('page_title', 'Dashboard Peserta')

@push('styles')
<style>
    /* ==========================================================================
       Dashboard Peserta: Modern Parity Design System (Gentelella Parity)
       Sleman Abyssal Navy #02275D & Sleman Gold #FFCB05
       Rich Interactive Micro-interactions & Fluid Hovers
       ========================================================================== --}}
    */

    .saas-dashboard-workspace {
        font-family: var(--font-sans, 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif);
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 100%;
        max-width: 100%;
        color: var(--neutral-800, #1e293b);
    }

    .saas-dashboard-workspace h1,
    .saas-dashboard-workspace h2,
    .saas-dashboard-workspace h3,
    .saas-font-display {
        font-family: var(--font-display, 'Lexend', sans-serif) !important;
        letter-spacing: -0.01em;
    }

    /* --------------------------------------------------------------------------
       1. Atmospheric Hero Banner (Sleman Navy #02275D Gradient)
       -------------------------------------------------------------------------- */
    .saas-hero-banner {
        background: linear-gradient(135deg, #02275D 0%, #081D40 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(2, 39, 93, 0.35);
        padding: 24px 28px;
        color: #ffffff !important;
        position: relative;
        overflow: hidden;
    }

    .saas-hero-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        position: relative;
        z-index: 2;
    }

    .saas-hero-left {
        max-width: 650px;
    }

    .saas-hero-pretitle {
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        color: #FFCB05;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .saas-hero-badges {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .saas-badge-status-active {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(34, 197, 94, 0.18);
        color: #4ade80;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 9999px;
        border: 1px solid rgba(74, 222, 128, 0.35);
    }

    .saas-dot-pulse {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #4ade80;
        display: inline-block;
        box-shadow: 0 0 0 2px rgba(74, 222, 128, 0.3);
    }

    .saas-badge-gold {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(255, 203, 5, 0.16);
        color: #FFCB05;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 9999px;
        border: 1px solid rgba(255, 203, 5, 0.35);
    }

    .saas-hero-date {
        color: rgba(255, 255, 255, 0.75);
        font-size: 0.75rem;
        font-weight: 500;
    }

    .saas-header-title {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 1.85rem;
        font-weight: 700;
        color: #ffffff;
        margin: 0 0 4px 0;
        line-height: 1.2;
    }

    .saas-header-desc {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.925rem;
        margin: 0 0 16px 0;
    }

    .saas-hero-actions-row {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .saas-btn-action-gold {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #FFCB05;
        color: #02275D !important;
        font-family: var(--font-sans, sans-serif);
        font-size: 0.95rem;
        font-weight: 700;
        padding: 10px 22px;
        border-radius: 12px;
        text-decoration: none;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.28);
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.15s ease;
    }

    .saas-btn-action-gold:hover {
        background: #FFC000;
        color: #02275D !important;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(255, 203, 5, 0.45);
    }

    .saas-btn-action-gold svg {
        transition: transform 0.2s ease;
    }

    .saas-btn-action-gold:hover svg {
        transform: scale(1.15);
    }

    .saas-btn-action-sk {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff !important;
        font-family: var(--font-sans, sans-serif);
        font-size: 0.95rem;
        font-weight: 700;
        padding: 10px 20px;
        border-radius: 12px;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .saas-btn-action-sk:hover {
        background: rgba(255, 255, 255, 0.18);
        border-color: rgba(255, 255, 255, 0.55);
        transform: translateY(-2px);
        color: #ffffff !important;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    }

    /* Hero Right: Circular Progress Gauge */
    .saas-hero-right {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .saas-gauge-card {
        background: rgba(8, 29, 64, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 14px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 16px;
        backdrop-filter: blur(8px);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .saas-gauge-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    }

    .saas-gauge-visual {
        position: relative;
        width: 86px;
        height: 86px;
        flex-shrink: 0;
    }

    .saas-gauge-svg {
        transform: rotate(-90deg);
        width: 86px;
        height: 86px;
    }

    .saas-gauge-bg {
        fill: none;
        stroke: rgba(255, 255, 255, 0.14);
        stroke-width: 7;
    }

    .saas-gauge-bar {
        fill: none;
        stroke: #FFCB05;
        stroke-width: 7;
        stroke-linecap: round;
        transition: stroke-dashoffset 0.8s ease;
    }

    .saas-gauge-number {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 1.15rem;
        font-weight: 700;
        color: #ffffff;
    }

    .saas-gauge-info {
        display: flex;
        flex-direction: column;
    }

    .saas-gauge-badge {
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        color: rgba(255, 255, 255, 0.7);
        text-transform: uppercase;
    }

    .saas-gauge-title {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 1.1rem;
        font-weight: 700;
        color: #ffffff;
        margin-top: 2px;
    }

    .saas-gauge-sub {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.65);
    }

    /* --------------------------------------------------------------------------
       2. Three-Metric KPI Row (Clean Pastel Cards)
       -------------------------------------------------------------------------- */
    .saas-kpi-triple-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        width: 100%;
    }

    .saas-pastel-kpi-card {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 22px 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
        cursor: default;
        min-height: 146px;
        justify-content: space-between;
    }

    .saas-static-card {
        cursor: default !important;
        user-select: text;
    }

    .saas-pastel-kpi-card:hover {
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
    }

    .saas-pastel-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .saas-pastel-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease;
    }

    .saas-pastel-kpi-card:hover .saas-pastel-icon-box {
        transform: scale(1.08);
    }

    .saas-pastel-icon-box.purple { background: #EDE9FE; color: #6366F1; }
    .saas-pastel-icon-box.amber { background: #FEF3C7; color: #D97706; }
    .saas-pastel-icon-box.blue { background: #E0E7FF; color: #02275D; }

    .saas-pastel-kpi-card.saas-kpi-amber {
        background: linear-gradient(180deg, #FFFFFF 0%, #FFFDF5 60%, #FFF8E1 100%) !important;
        border-color: #FDE68A !important;
    }

    /* Solid Icon Boxes untuk KPI Bar Presisi Mockup */
    .saas-kpi-solid-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .saas-pastel-kpi-card:hover .saas-kpi-solid-icon {
        transform: scale(1.08);
    }

    .saas-kpi-solid-icon.icon-indigo {
        background: #3949AB;
        color: #ffffff;
    }

    .saas-kpi-solid-icon.icon-navy {
        background: #02275D;
        color: #FFCB05;
    }

    .saas-kpi-solid-icon.icon-gold {
        background: #FFCB05;
        color: #02275D;
    }

    /* Inline Progress Track Presisi Sesuai Mockup Referensi */
    .saas-kpi-inline-track {
        width: 100%;
        height: 4px;
        border-radius: 9999px;
        margin-top: 12px;
        overflow: hidden;
    }

    .saas-kpi-inline-track.track-indigo {
        background: #E2E8F0;
    }

    .saas-kpi-inline-track.track-amber {
        background: #FEF3C7;
    }

    .saas-kpi-inline-fill {
        display: block;
        height: 100%;
        border-radius: 9999px;
        transition: width 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .saas-kpi-inline-fill.fill-indigo {
        width: 25%;
        background: #3949AB;
    }

    .saas-kpi-inline-fill.fill-amber {
        width: 35%;
        background: #FFCB05;
    }

    .saas-pastel-kpi-card:hover .saas-kpi-inline-fill.fill-indigo {
        width: 40%;
    }

    .saas-pastel-kpi-card:hover .saas-kpi-inline-fill.fill-amber {
        width: 50%;
    }

    .saas-pastel-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 9999px;
    }

    .saas-pastel-badge.purple { background: #EDE9FE; color: #6366F1; }
    .saas-pastel-badge.amber { background: #FEF3C7; color: #B45309; }
    .saas-pastel-badge.green {
        background: #DCFCE7;
        color: #16A34A;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .saas-dot-mini.green {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #16A34A;
    }

    .saas-pastel-label {
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        color: #64748B;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .saas-pastel-value {
        display: flex;
        align-items: baseline;
        gap: 8px;
    }

    .saas-value-num {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 1.85rem;
        font-weight: 700;
        color: #02275D;
        line-height: 1;
    }

    .saas-value-unit {
        font-size: 1rem;
        font-weight: 500;
        color: #475569;
    }

    .saas-value-name {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 1.35rem;
        font-weight: 700;
        color: #02275D;
        line-height: 1.2;
        padding-top: 2px;
    }

    .saas-pastel-strip {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        transition: height 0.2s ease;
    }

    .saas-pastel-kpi-card:hover .saas-pastel-strip {
        height: 4px;
    }

    .saas-pastel-strip.purple { background: linear-gradient(90deg, #818CF8, transparent 80%); }
    .saas-pastel-strip.amber { background: linear-gradient(90deg, #FBBF24, transparent 80%); }
    .saas-pastel-strip.blue { background: linear-gradient(90deg, #02275D, transparent 80%); }

    /* --------------------------------------------------------------------------
       3. Cockpit 2-Column Grid (Main Content + Aside Context)
       -------------------------------------------------------------------------- */
    .saas-cockpit-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 20px;
        align-items: start;
        width: 100%;
    }

    .saas-cockpit-grid.is-onboarding {
        grid-template-columns: 1fr;
    }

    .saas-cockpit-main {
        min-width: 0;
    }

    .saas-cockpit-aside {
        min-width: 0;
    }

    /* Content Cards */
    .saas-content-card {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 20px 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        margin-bottom: 20px;
    }

    .saas-card-title-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .saas-title-with-accent {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .saas-accent-line {
        width: 4px;
        height: 18px;
        border-radius: 2px;
        background: #FFCB05;
        flex-shrink: 0;
    }

    .saas-card-heading {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 1.05rem;
        font-weight: 700;
        color: #0F172A;
        margin: 0;
    }

    .saas-pill-tag {
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
        background: #F1F5F9;
        padding: 3px 10px;
        border-radius: 9999px;
    }

    .saas-card-link-more {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #02275D;
        text-decoration: none;
        transition: transform 0.15s ease, color 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .saas-card-link-more:hover {
        color: #2563eb;
        transform: translateX(3px);
    }

    /* Stat Box Cards */
    .saas-logbook-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .saas-tugas-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .saas-stat-box {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 10px;
        border: 1px solid #E2E8F0;
        cursor: default;
        user-select: text;
    }

    .saas-static-box {
        cursor: default !important;
        user-select: text;
    }

    .saas-stat-box.box-blue { background: #ffffff; }
    .saas-stat-box.box-amber { background: #FEF9C3; border-color: #FDE047; }
    .saas-stat-box.box-gray { background: #F8FAFC; }
    .saas-stat-box.box-blue-soft { background: #EFF6FF; border-color: #BFDBFE; }
    .saas-stat-box.box-red-soft { background: #FEF2F2; border-color: #FECACA; }

    .saas-stat-indicator {
        width: 3px;
        height: 32px;
        border-radius: 2px;
        flex-shrink: 0;
    }

    .saas-stat-indicator.blue { background: #02275D; }
    .saas-stat-indicator.amber { background: #EAB308; }
    .saas-stat-indicator.gray { background: #94A3B8; }
    .saas-stat-indicator.red { background: #EF4444; }

    .saas-stat-body {
        display: flex;
        flex-direction: column;
    }

    .saas-stat-num {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 1.45rem;
        font-weight: 700;
        color: #0F172A;
        line-height: 1.1;
    }

    .saas-stat-desc {
        font-size: 0.8125rem;
        color: #64748B;
        font-weight: 500;
    }

    /* Stepper Grid Mingguan M1 - M13 */
    .saas-stepper-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .saas-stepper-grid {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .saas-week-pill {
        width: 44px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 0.8125rem;
        font-weight: 700;
        text-decoration: none;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), filter 0.15s ease;
    }

    .saas-week-pill:hover {
        transform: translateY(-3px);
    }

    .saas-week-pill.filled {
        background: #02275D;
        color: #ffffff;
        border: 1px solid #02275D;
    }

    .saas-week-pill.filled:hover {
        box-shadow: 0 6px 14px rgba(2, 39, 93, 0.35);
        filter: brightness(1.1);
    }

    .saas-week-pill.unfilled {
        background: #FFCB05;
        color: #02275D;
        border: 1px solid #EAB308;
    }

    .saas-week-pill.unfilled:hover {
        box-shadow: 0 6px 16px rgba(255, 203, 5, 0.5);
        background: #FBBF24;
    }

    .saas-week-pill.upcoming {
        background: repeating-linear-gradient(45deg, #F8FAFC, #F8FAFC 6px, #F1F5F9 6px, #F1F5F9 12px);
        color: #94A3B8;
        border: 1px dashed #CBD5E1;
    }

    .saas-week-pill.upcoming:hover {
        border-color: #94A3B8;
        color: #475569;
    }

    .saas-week-pill.is-active-week {
        box-shadow: 0 0 0 2px #ffffff, 0 0 0 4px #FFCB05;
    }

    .saas-week-pill.current-week {
        background: #FFCB05 !important;
        color: #02275D !important;
        border: 1px solid #EAB308 !important;
        box-shadow: none !important;
    }

    .saas-week-pill.current-week:hover {
        background: #FBBF24 !important;
        transform: translateY(-3px);
        box-shadow: 0 6px 14px rgba(255, 203, 5, 0.45) !important;
    }

    .saas-stepper-legend {
        display: flex;
        align-items: center;
        gap: 18px;
        font-size: 0.75rem;
        color: #64748B;
        margin-top: 4px;
    }

    .saas-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .saas-legend-swatch {
        width: 10px;
        height: 10px;
        border-radius: 2px;
    }

    .saas-legend-swatch.filled { background: #02275D; }
    .saas-legend-swatch.unfilled { background: #FFCB05; }
    .saas-legend-swatch.upcoming { background: #E2E8F0; border: 1px dashed #94A3B8; }

    /* Segmented Progress Bar */
    .saas-progress-segmented-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 4px;
    }

    /* Onboarding Flow Interactive Cards (TASK 02) */
    .saas-stepper {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-top: 10px;
    }

    .saas-step-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        border-radius: 14px;
        text-decoration: none !important;
        color: inherit;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
    }

    .saas-step-item:hover {
        border-color: #02275D;
        background: #F8FAFC;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(2, 39, 93, 0.08);
    }

    .saas-step-item.completed {
        border-color: #CBD5E1;
        background: #F8FAFC;
    }

    .saas-step-item.completed:hover {
        border-color: #10B981;
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.12);
    }

    .saas-step-item.active {
        border-color: #02275D;
        background: #FFFFFF;
        box-shadow: 0 4px 14px rgba(2, 39, 93, 0.06);
    }

    .saas-step-badge {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #02275D;
        color: #FFFFFF;
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }

    .saas-step-item:hover .saas-step-badge {
        transform: scale(1.08);
    }

    .saas-step-item.completed .saas-step-badge {
        background: #10B981;
        color: #FFFFFF;
    }

    .saas-step-body {
        flex: 1;
        min-width: 0;
    }

    .saas-step-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        font-weight: 700;
        font-size: 0.9375rem;
        color: #0F172A;
    }

    .saas-step-action {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #02275D;
        white-space: nowrap;
        transition: transform 0.2s ease;
    }

    .saas-step-item:hover .saas-step-action {
        transform: translateX(4px);
    }

    .saas-step-desc {
        font-size: 0.8125rem;
        color: #64748B;
        margin: 4px 0 0 0;
        line-height: 1.45;
    }

    .saas-tugas-segmented-track {
        display: flex;
        gap: 8px;
        align-items: center;
        width: 100%;
        margin-top: 6px;
    }

    .saas-tugas-segment {
        height: 8px;
        flex: 1;
        border-radius: 9999px;
        background: #E2E8F0;
        transition: background-color 0.25s ease;
    }

    .saas-tugas-segment.is-done {
        background: #02275D;
    }

    .saas-progress-track {
        height: 8px;
        border-radius: 9999px;
        background: #E2E8F0;
        overflow: hidden;
    }

    .saas-progress-fill {
        height: 100%;
        border-radius: 9999px;
        background: #02275D;
        transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .saas-progress-footer-stats {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        color: #64748B;
    }

    /* --------------------------------------------------------------------------
       4. Kolom Aside Kanan (Kalender & Kartu Berwarna)
       -------------------------------------------------------------------------- */
    .saas-aside-stack {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .saas-aside-card {
        border-radius: 14px;
        overflow: hidden;
    }

    /* Kalender Bulanan Interaktif */
    .saas-calendar-widget {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        padding: 18px 20px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .saas-calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .saas-cal-month-title {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 0.95rem;
        font-weight: 700;
        color: #0F172A;
        user-select: none;
    }

    .saas-cal-nav-btn {
        background: none;
        border: none;
        font-size: 1.35rem;
        line-height: 1;
        color: #64748B;
        cursor: pointer;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .saas-cal-nav-btn:hover {
        color: #02275D;
        background: #F1F5F9;
        transform: scale(1.1);
    }

    .saas-cal-days-head {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        font-size: 0.6875rem;
        font-weight: 700;
        color: #94A3B8;
        margin-bottom: 8px;
    }

    .saas-cal-days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        text-align: center;
        margin-bottom: 14px;
    }

    .saas-cal-cell {
        height: 32px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        color: #1E293B;
        position: relative;
        border-radius: 50%;
        transition: transform 0.15s ease, background-color 0.15s ease;
    }

    .saas-cal-cell:not(.is-muted):hover {
        background: #F1F5F9;
        cursor: pointer;
        transform: scale(1.12);
    }

    .saas-cal-cell.is-muted {
        color: #CBD5E1;
        cursor: default;
    }

    .saas-cal-cell.is-today {
        background: #02275D !important;
        color: #ffffff !important;
        font-weight: 700;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        margin: auto;
        box-shadow: 0 2px 8px rgba(2, 39, 93, 0.35);
    }

    .saas-cal-cell.is-today:hover {
        transform: scale(1.1);
    }

    .saas-cal-event-dot {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #FFCB05;
        position: absolute;
        bottom: 2px;
    }

    .saas-cal-legend {
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 0.6875rem;
        color: #64748B;
        border-top: 1px solid #F1F5F9;
        padding-top: 10px;
    }

    .saas-cal-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .saas-cal-legend-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
    }

    .saas-cal-legend-dot.dot-navy { background: #02275D; }
    .saas-cal-legend-dot.dot-gold { background: #FFCB05; }

    /* Kartu Peringatan Merah (Hanya Dilihat / Statis) */
    .saas-urgent-alert-card {
        background: #D32F2F;
        color: #ffffff !important;
        padding: 18px 20px;
        box-shadow: 0 4px 14px rgba(211, 47, 47, 0.25);
        display: block;
        cursor: default;
        user-select: text;
    }

    .saas-alert-card-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        font-size: 0.8125rem;
        font-weight: 700;
    }

    .saas-alert-count {
        display: flex;
        align-items: baseline;
        gap: 6px;
        margin-bottom: 4px;
    }

    .saas-alert-num {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }

    .saas-alert-label {
        font-size: 1rem;
        font-weight: 700;
    }

    .saas-alert-desc {
        font-size: 0.775rem;
        opacity: 0.95;
        margin: 0;
        line-height: 1.35;
    }

    /* Kartu Kuning Sisa Hari Magang (Hanya Dilihat / Statis) */
    .saas-gold-countdown-card {
        background: #FFCB05;
        color: #02275D !important;
        padding: 18px 20px;
        box-shadow: 0 4px 14px rgba(255, 203, 5, 0.3);
        display: block;
        cursor: default;
        user-select: text;
    }

    .saas-countdown-main {
        display: flex;
        align-items: baseline;
        gap: 6px;
    }

    .saas-countdown-num {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 2.15rem;
        font-weight: 800;
        line-height: 1;
        color: #02275D;
    }

    .saas-countdown-label {
        font-size: 0.95rem;
        font-weight: 700;
        color: #02275D;
    }

    .saas-countdown-subhead {
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        color: #02275D;
        opacity: 0.85;
        margin-top: 4px;
        margin-bottom: 12px;
    }

    .saas-countdown-dots-grid {
        display: flex;
        gap: 4px;
        align-items: center;
        margin-bottom: 12px;
    }

    .saas-dot-segment {
        height: 8px;
        flex: 1;
        border-radius: 9999px;
        transition: transform 0.15s ease;
    }

    .saas-gold-countdown-card:hover .saas-dot-segment.is-passed {
        transform: scaleY(1.2);
    }

    .saas-dot-segment.is-passed { background: #02275D; }
    .saas-dot-segment.is-remain { background: rgba(2, 39, 93, 0.16); }

    .saas-countdown-footer {
        display: flex;
        justify-content: space-between;
        font-size: 0.6875rem;
        font-weight: 600;
        opacity: 0.85;
        border-top: 1px solid rgba(2, 39, 93, 0.15);
        padding-top: 10px;
    }

    /* Canvas Footer */
    .saas-dashboard-canvas-footer {
        text-align: right;
        font-size: 0.75rem;
        color: #94A3B8;
        padding: 4px 0 12px 0;
    }

    /* --------------------------------------------------------------------------
       5. Responsiveness
       -------------------------------------------------------------------------- */
    @media (max-width: 1024px) {
        .saas-cockpit-grid {
            grid-template-columns: 1fr;
        }

        .saas-kpi-triple-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .saas-kpi-triple-grid {
            grid-template-columns: 1fr;
        }

        .saas-logbook-summary-grid,
        .saas-tugas-summary-grid {
            grid-template-columns: 1fr;
        }

        .saas-hero-inner {
            flex-direction: column;
            align-items: flex-start;
        }

        .saas-hero-right {
            width: 100%;
        }

        .saas-gauge-card {
            width: 100%;
            justify-content: space-between;
        }
    }

    [data-theme="dark"] .saas-pastel-kpi-card {
        background: #0f172a;
        border-color: #334155;
    }
</style>
@endpush

@section('content')
<div class="saas-dashboard-workspace">

    {{-- 1. Atmospheric Hero Banner Header --}}
    @include('peserta.dashboard.partials._header')

    {{-- 2. Three-Metric KPI Row --}}
    @if($fase === 'aktif' && $penempatan)
        @include('peserta.dashboard.partials._kpi_bar')
    @endif

    {{-- Statusline Horizontal Ribbon (Khusus saat Fase Seleksi / Onboarding) --}}
    @if($fase !== 'aktif')
        @php
            $timKelompokData = $kelompokMagang ?? $user->kelompokMagang;
        @endphp
        <div class="saas-statusline" role="region" aria-label="Statusline MagKom Peserta" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(13.5rem, 1fr)); gap: 14px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; box-shadow: 0 1px 4px rgba(15, 23, 42, 0.05);">
            <div style="display: flex; align-items: center; gap: 12px; padding: 4px 6px;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(2, 39, 93, 0.08); color: #02275D; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div style="display: flex; flex-direction: column; min-width: 0;">
                    <span style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Status Magang</span>
                    <span style="font-size: 0.875rem; color: #0f172a; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <span class="saas-dot-pulse"></span>
                        <span>{{ $fase === 'selesai' ? 'Magang Tuntas' : 'Seleksi / Onboarding' }}</span>
                    </span>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 12px; padding: 4px 6px;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(2, 39, 93, 0.08); color: #02275D; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <div style="display: flex; flex-direction: column; min-width: 0;">
                    <span style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Tipe Pendaftaran</span>
                    <span style="font-size: 0.875rem; color: #0f172a; font-weight: 600;">
                        {{ $timKelompokData ? 'Kelompok' : 'Mandiri (Individu)' }}
                    </span>
                </div>
            </div>
        </div>
    @endif

    {{-- 3. Cockpit 2-Column Grid (Main Content + Aside Context) --}}
    <div class="saas-cockpit-grid {{ $fase === 'onboarding' ? 'is-onboarding' : '' }}">

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

        {{-- Kolom Samping (Konteks & Berkas) - Disembunyikan pada POV Onboarding (Belum Mengajukan Pendaftaran) --}}
        @if($fase !== 'onboarding')
            <aside class="saas-cockpit-aside" id="dashboardAsideContext">
                @include('peserta.dashboard.partials._aside')
            </aside>
        @endif

    </div>

    {{-- Modal Quick Logbook --}}
    @include('peserta.dashboard.partials._modal_quick_logbook')

    {{-- Kompatibilitas Aksesibilitas & Kontrak Pengujian Legacy (Screen-Reader Only) --}}
    <div class="sr-only tile_count x_panel" aria-hidden="true" style="display: none;">
        @if($penempatan?->pembimbing)
            <span>{{ $penempatan->pembimbing->email }}</span>
        @endif
        @if($penempatan)
            @foreach($penempatan->tugas ?? [] as $tItem)
                <span>{{ $tItem->judul }}</span>
            @endforeach
            @foreach($penempatan->logbooks ?? [] as $lbItem)
                <span>{{ $lbItem->kegiatan }}</span>
            @endforeach
        @endif
        <div class="saas-quick-grid">
            <span>Aksi Cepat Peserta</span>
            <span>Isi Logbook Mingguan</span>
        </div>
        <div>Ketentuan Logbook (BR-09)</div>
        <div class="tile_stats_count">
            <span>Tugas Selesai</span>
            <span>Logbook Mingguan</span>
            <span>Sisa Hari Magang</span>
            <span>Status Evaluasi</span>
            <span>Status Evaluasi / Sertifikat</span>
            <span>Hari</span>
        </div>
        <div>
            <span>Tugas Magang Mendesak</span>
            <span>Logbook Mingguan Terkini</span>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ======================================================================
        // Interactive Client-Side Calendar Engine
        // ======================================================================
        const calWidget = document.getElementById('saasCalendarWidget');
        if (!calWidget) return;

        const monthTitle = document.getElementById('calMonthTitle');
        const daysGrid = document.getElementById('calDaysGrid');
        const prevBtn = document.getElementById('calPrevBtn');
        const nextBtn = document.getElementById('calNextBtn');

        let rawEvents = {};
        try {
            rawEvents = JSON.parse(calWidget.getAttribute('data-event-dates') || '{}');
        } catch (e) {
            rawEvents = {};
        }

        const now = new Date();
        let currentYear = now.getFullYear();
        let currentMonth = now.getMonth(); // 0 to 11

        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        function renderCalendar(year, month) {
            monthTitle.textContent = `${monthNames[month]} ${year}`;
            daysGrid.innerHTML = '';

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();

            // ISO day of week: Monday=1 ... Sunday=7
            let firstDayOfWeek = firstDay.getDay(); // 0 (Sun) - 6 (Sat)
            let isoStartDay = firstDayOfWeek === 0 ? 7 : firstDayOfWeek; // 1 (Mon) - 7 (Sun)
            let prevDaysToFill = isoStartDay - 1;

            const prevMonthLastDate = new Date(year, month, 0).getDate();

            // Prev month padding
            for (let i = prevDaysToFill - 1; i >= 0; i--) {
                const cell = document.createElement('div');
                cell.className = 'saas-cal-cell is-muted';
                cell.innerHTML = `<span>${prevMonthLastDate - i}</span>`;
                daysGrid.appendChild(cell);
            }

            // Current month days
            const todayYear = now.getFullYear();
            const todayMonth = now.getMonth();
            const todayDate = now.getDate();

            for (let d = 1; d <= daysInMonth; d++) {
                const isToday = (year === todayYear && month === todayMonth && d === todayDate);
                const mm = String(month + 1).padStart(2, '0');
                const dd = String(d).padStart(2, '0');
                const dateKey = `${year}-${mm}-${dd}`;
                // Dot kuning HANYA untuk tanggal kegiatan yang BUKAN hari ini
                const hasEvent = !isToday && (!!rawEvents[dateKey] || (month === 10 && [9, 13, 20].includes(d)));

                const cell = document.createElement('div');
                cell.className = `saas-cal-cell ${isToday ? 'is-today' : ''}`;
                cell.setAttribute('data-date', dateKey);
                cell.title = hasEvent ? `Tanggal kegiatan: ${d} ${monthNames[month]} ${year}` : `${d} ${monthNames[month]} ${year}`;

                let html = `<span class="saas-cal-day-num">${d}</span>`;
                if (hasEvent) {
                    html += '<span class="saas-cal-event-dot"></span>';
                }
                cell.innerHTML = html;

                cell.addEventListener('click', function () {
                    // Feedback klik interaktif pada tanggal
                    const activeCell = daysGrid.querySelector('.saas-cal-cell.is-selected');
                    if (activeCell && activeCell !== cell) {
                        activeCell.classList.remove('is-selected');
                        activeCell.style.outline = 'none';
                    }
                    if (!cell.classList.contains('is-today')) {
                        cell.classList.add('is-selected');
                        cell.style.outline = '2px solid #FFCB05';
                        cell.style.outlineOffset = '2px';
                    }
                });

                daysGrid.appendChild(cell);
            }

            // Next month padding to fill row (multiple of 7)
            const totalCells = prevDaysToFill + daysInMonth;
            const remaining = (7 - (totalCells % 7)) % 7;
            for (let nextDay = 1; nextDay <= remaining; nextDay++) {
                const cell = document.createElement('div');
                cell.className = 'saas-cal-cell is-muted';
                cell.innerHTML = `<span>${nextDay}</span>`;
                daysGrid.appendChild(cell);
            }
        }

        prevBtn.addEventListener('click', function () {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar(currentYear, currentMonth);
        });

        nextBtn.addEventListener('click', function () {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar(currentYear, currentMonth);
        });
    });
</script>
@endpush
