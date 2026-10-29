@extends('layouts.peserta')

@section('title', 'Dashboard Peserta')
@section('page_title', 'Dashboard Peserta')

@push('styles')
<style>
    /* ==========================================================================
       Dashboard Peserta: Modern Clean SaaS (Gentelella Parity & DS-MagKom)
       Atmospheric Hero Banner, 4 KPI Bar, Aside Context & Stepper 4-Fase
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

    /* --------------------------------------------------------------------------
       1. Atmospheric Hero Banner (Sleman Navy Solid Gradient)
       -------------------------------------------------------------------------- */
    .saas-hero-banner {
        background: linear-gradient(135deg, #1b3860 0%, #152d4e 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(27, 56, 96, 0.18), 0 1px 3px rgba(0, 0, 0, 0.05);
        padding: 28px 32px;
        color: #ffffff !important;
        position: relative;
        overflow: hidden;
    }

    .saas-hero-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
        position: relative;
        z-index: 2;
    }

    .saas-hero-left {
        max-width: 680px;
    }

    .saas-hero-badges {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .saas-badge-status-active {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(34, 197, 94, 0.18);
        color: #4ade80;
        font-size: 0.775rem;
        font-weight: 700;
        padding: 4px 12px;
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
        gap: 6px;
        background: rgba(245, 158, 11, 0.18);
        color: #fbbf24;
        font-size: 0.775rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 9999px;
        border: 1px solid rgba(251, 191, 36, 0.35);
        letter-spacing: 0.02em;
    }

    .saas-badge-portal-hero {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(59, 130, 246, 0.2);
        color: #93c5fd;
        font-size: 0.775rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 9999px;
        border: 1px solid rgba(147, 197, 253, 0.35);
    }

    .saas-hero-date {
        font-size: 0.8125rem;
        color: #cbd5e1;
        font-weight: 500;
    }

    .saas-header-title {
        font-family: var(--font-display, 'Lexend', sans-serif) !important;
        font-size: 1.65rem;
        font-weight: 700;
        color: #ffffff !important;
        margin: 0 0 8px 0;
        line-height: 1.25;
    }

    .saas-header-desc {
        font-size: 0.9rem;
        color: #e2e8f0;
        margin: 0 0 12px 0;
        line-height: 1.5;
    }

    .saas-header-desc strong {
        color: #ffffff;
    }

    .saas-header-periode {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8125rem;
        color: #f1f5f9;
        background: rgba(255, 255, 255, 0.1);
        padding: 6px 14px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(4px);
    }

    .saas-header-periode strong {
        color: #ffffff;
    }

    .saas-hero-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
    }

    .saas-hero-action-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .saas-hero-btn-primary {
        background: #2563eb;
        color: #ffffff !important;
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.84rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.15s ease;
    }

    .saas-hero-btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
    }

    .saas-hero-btn-secondary {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff !important;
        padding: 9px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.84rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255, 255, 255, 0.22);
        backdrop-filter: blur(4px);
        transition: all 0.15s ease;
    }

    .saas-hero-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.35);
        color: #ffffff !important;
    }

    .saas-hero-link-sk {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #fbbf24 !important;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 8px;
        border-radius: 6px;
        transition: all 0.15s ease;
    }

    .saas-hero-link-sk:hover {
        color: #fef08a !important;
        background: rgba(251, 191, 36, 0.12);
    }

    /* --------------------------------------------------------------------------
       2. Four-Metric KPI Bar
       -------------------------------------------------------------------------- */
    .saas-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        width: 100%;
    }

    .saas-kpi-card {
        padding: 20px 22px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 12px;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .saas-kpi-card:hover {
        transform: translateY(-2px);
        border-color: #93c5fd !important;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.08) !important;
    }

    .saas-kpi-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .saas-kpi-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--neutral-500, #64748b);
    }

    .saas-kpi-content {
        display: flex;
        flex-direction: column;
    }

    .saas-kpi-value {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 1.65rem;
        font-weight: 700;
        color: var(--neutral-900, #0f172a);
        line-height: 1.2;
    }

    .saas-kpi-value-truncate {
        font-size: 1.05rem;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .saas-kpi-unit {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--neutral-500, #64748b);
    }

    .saas-kpi-subtext {
        font-size: 0.775rem;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .saas-kpi-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    .saas-dot-success { background: #16a34a; }
    .saas-dot-danger { background: #dc2626; }
    .saas-dot-amber { background: #d97706; }
    .saas-dot-muted { background: #64748b; }

    .saas-kpi-success { color: #166534; }
    .saas-kpi-danger { color: #dc2626; }
    .saas-kpi-amber { color: var(--neutral-500, #64748b); }
    .saas-kpi-muted { color: var(--neutral-500, #64748b); }

    .saas-kpi-icon-blue {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .saas-kpi-icon-green {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f0fdf4;
        color: #16a34a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .saas-kpi-icon-amber {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #fffbeb;
        color: #d97706;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .saas-kpi-icon-indigo {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f5f3ff;
        color: #7c3aed;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* --------------------------------------------------------------------------
       3. Cockpit 2-Column Grid (Main Content + Aside Context)
       -------------------------------------------------------------------------- */
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

    /* Base Card */
    .saas-card {
        background: #ffffff;
        border: 1px solid var(--neutral-200, #e2e8f0);
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 4px 12px -2px rgba(15, 23, 42, 0.03);
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

    .saas-card-body-compact {
        padding: 16px 20px;
    }

    .saas-item-title {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--neutral-900, #0f172a);
        margin: 0;
    }

    .saas-card-subtitle {
        font-size: 0.775rem;
        color: var(--neutral-500, #64748b);
        margin: 2px 0 0 0;
    }

    .saas-link-more {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 0.8125rem;
        color: #2563eb;
        text-decoration: none;
        font-weight: 600;
    }

    .saas-link-more:hover {
        text-decoration: underline;
    }

    .saas-badge-portal {
        font-size: 0.75rem;
        font-weight: 700;
        color: #2563eb;
        background: #eff6ff;
        padding: 4px 10px;
        border-radius: 9999px;
        border: 1px solid #bfdbfe;
    }

    /* Buttons */
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

    .saas-btn-detail {
        padding: 6px 12px;
        font-size: 0.775rem;
        font-weight: 600;
        color: var(--neutral-700, #334155);
        background: #ffffff;
        border: 1px solid var(--neutral-200, #e2e8f0);
        border-radius: 8px;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.15s ease;
    }

    .saas-btn-detail:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        color: #0f172a;
    }

    .saas-btn-revisi {
        background: #d97706;
        color: #ffffff !important;
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 8px;
        padding: 6px 14px;
        transition: background 0.15s ease;
    }

    .saas-btn-revisi:hover {
        background: #b45309;
    }

    /* --------------------------------------------------------------------------
       4. Aside Context Sidebar Components
       -------------------------------------------------------------------------- */
    .saas-aside-card {
        padding: 20px;
    }

    .saas-aside-section-title {
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--neutral-900, #0f172a);
        letter-spacing: 0.02em;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .saas-aside-title-icon {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .saas-icon-badge-blue { color: #2563eb; }
    .saas-icon-badge-amber { color: #f59e0b; }

    .saas-badge-success-sm {
        font-size: 0.725rem;
        font-weight: 600;
        color: #16a34a;
        background: #f0fdf4;
        padding: 2px 8px;
        border-radius: 9999px;
        border: 1px solid #bbf7d0;
    }

    .saas-aside-mentor-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .saas-aside-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #eff6ff;
        color: #1b3860;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-weight: 700;
        font-size: 1rem;
        border: 1px solid #bfdbfe;
        flex-shrink: 0;
    }

    .saas-aside-mentor-info {
        min-width: 0;
    }

    .saas-aside-title {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--neutral-900, #0f172a);
        margin: 0 0 2px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .saas-aside-subtitle {
        font-size: 0.75rem;
        color: var(--neutral-500, #64748b);
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .saas-aside-meta-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 14px;
        background: var(--neutral-100, #f8fafc);
        border: 1px solid var(--neutral-200, #e2e8f0);
        border-radius: 8px;
        padding: 10px 12px;
    }

    .saas-aside-contact-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.775rem;
        color: var(--neutral-600, #475569);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .saas-btn-whatsapp {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 8px 14px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid var(--neutral-200, #e2e8f0);
        color: #1b3860;
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .saas-btn-whatsapp:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #2563eb;
    }

    /* Quick Action 2x2 Grid */
    .saas-quick-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .saas-quick-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 10px;
        border-radius: 10px;
        border: 1px solid var(--neutral-200, #e2e8f0);
        background: #ffffff;
        text-decoration: none;
        color: var(--neutral-800, #1e293b);
        transition: all 0.15s ease;
        text-align: center;
    }

    .saas-quick-card:hover {
        background: #f8fafc;
        border-color: #93c5fd;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    }

    .saas-quick-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .saas-quick-icon-blue { background: #eff6ff; color: #2563eb; }
    .saas-quick-icon-green { background: #f0fdf4; color: #16a34a; }
    .saas-quick-icon-indigo { background: #f5f3ff; color: #7c3aed; }
    .saas-quick-icon-amber { background: #fffbeb; color: #d97706; }

    .saas-quick-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--neutral-800, #1e293b);
    }

    /* Banner Panduan / Ketentuan BR-09 */
    .saas-br09-banner {
        background: #fffbeb !important;
        border: 1px solid #fde68a !important;
        padding: 16px 18px;
        border-radius: 12px;
    }

    .saas-br09-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .saas-br09-title {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 0.8125rem;
        font-weight: 700;
        color: #92400e;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .saas-br09-badge {
        font-size: 0.7rem;
        font-weight: 700;
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
        padding: 1px 6px;
        border-radius: 4px;
    }

    .saas-br09-text {
        font-size: 0.775rem;
        color: #b45309;
        line-height: 1.5;
        margin: 0;
    }

    .saas-aside-subtitle-block {
        font-size: 0.8125rem;
        color: var(--neutral-600, #475569);
        line-height: 1.5;
        margin: 0 0 12px 0;
    }

    .saas-aside-info-box {
        background: var(--neutral-100, #f8fafc);
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 0.8125rem;
        color: var(--neutral-700, #334155);
        display: flex;
        flex-direction: column;
        gap: 6px;
        border: 1px solid var(--neutral-200, #e2e8f0);
        margin-bottom: 14px;
    }

    .saas-aside-doc-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .saas-doc-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid var(--neutral-200, #e2e8f0);
        background: #ffffff;
        text-decoration: none;
        font-size: 0.8125rem;
        color: var(--neutral-800, #1e293b);
        font-weight: 600;
        transition: all 0.15s ease;
    }

    .saas-doc-row:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .saas-doc-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .saas-doc-right-amber {
        font-size: 0.75rem;
        color: #f59e0b;
        font-weight: 700;
    }

    /* --------------------------------------------------------------------------
       5. Stepper & Phase Components
       -------------------------------------------------------------------------- */
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
        text-decoration: none;
        color: inherit;
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

    .saas-step-body {
        flex: 1;
    }

    .saas-step-title {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--neutral-900, #0f172a);
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .saas-step-action {
        font-size: 0.775rem;
        color: #2563eb;
        font-weight: 600;
    }

    .saas-step-desc {
        font-size: 0.8125rem;
        color: var(--neutral-600, #475569);
        margin: 0;
        line-height: 1.5;
    }

    .saas-divider-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--neutral-150, #f1f5f9);
    }

    /* Info Strip */
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

    .saas-status-pill {
        font-size: 0.775rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 9999px;
    }

    .saas-status-success {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }

    .saas-status-warning {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }

    .saas-status-danger {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .saas-status-default {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .saas-alert-catatan {
        margin-top: 18px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .saas-alert-catatan-icon {
        font-size: 1.1rem;
        line-height: 1;
    }

    .saas-alert-catatan-title {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 0.8125rem;
        font-weight: 700;
        color: #92400e;
        margin: 0 0 4px 0;
    }

    .saas-alert-catatan-desc {
        font-size: 0.8125rem;
        color: #b45309;
        line-height: 1.5;
        margin: 0;
    }

    /* Phase Aktif Elements */
    .saas-alert-revisi {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .saas-alert-revisi-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .saas-alert-icon { font-size: 1.1rem; }

    .saas-alert-revisi-text {
        font-size: 0.875rem;
        color: #92400e;
    }

    .saas-phase-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
        width: 100%;
    }

    .saas-list-container {
        display: flex;
        flex-direction: column;
    }

    .saas-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--neutral-150, #f1f5f9);
        gap: 12px;
    }

    .saas-list-item:last-child {
        border-bottom: none;
    }

    .saas-item-main {
        min-width: 0;
        flex: 1;
    }

    .saas-item-title-heading {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--neutral-900, #0f172a);
        margin: 0 0 4px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .saas-item-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        color: var(--neutral-500, #64748b);
    }

    .saas-meta-sep { color: #cbd5e1; }

    .saas-tag-category {
        background: var(--neutral-100, #f1f5f9);
        padding: 1px 6px;
        border-radius: 4px;
        color: #475569;
    }

    .saas-item-header-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 3px;
    }

    .saas-item-summary {
        font-size: 0.8125rem;
        color: var(--neutral-700, #334155);
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .saas-item-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .saas-week-tag {
        font-size: 0.725rem;
        font-weight: 700;
        color: #1b3860;
        background: #eff6ff;
        padding: 2px 6px;
        border-radius: 4px;
    }

    .saas-badge-revisi {
        font-size: 0.725rem;
        font-weight: 600;
        color: #dc2626;
        background: #fef2f2;
        border: 1px solid #fecaca;
        padding: 3px 8px;
        border-radius: 9999px;
    }

    .saas-badge-approved {
        font-size: 0.725rem;
        font-weight: 600;
        color: #16a34a;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        padding: 3px 8px;
        border-radius: 9999px;
    }

    .saas-badge-submitted {
        font-size: 0.725rem;
        font-weight: 600;
        color: #2563eb;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 3px 8px;
        border-radius: 9999px;
    }

    .saas-empty-box {
        text-align: center;
        padding: 32px 16px;
        color: var(--neutral-500, #64748b);
    }

    .saas-empty-icon-green {
        width: 40px;
        height: 40px;
        margin: 0 auto 10px auto;
        border-radius: 10px;
        background: #f0fdf4;
        color: #16a34a;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .saas-empty-icon-blue {
        width: 40px;
        height: 40px;
        margin: 0 auto 10px auto;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .saas-empty-box-title {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--neutral-800, #1e293b);
    }

    .saas-empty-box-sub {
        font-size: 0.775rem;
        margin: 2px 0 0 0;
    }

    /* Phase Selesai */
    .saas-selesai-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px 24px;
    }

    .saas-selesai-text-box {
        max-width: 580px;
    }

    .saas-selesai-desc {
        font-size: 0.875rem;
        color: var(--neutral-700, #334155);
        line-height: 1.6;
        margin: 0;
    }

    .saas-predikat-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 24px;
        min-width: 140px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .saas-predikat-label {
        font-size: 0.725rem;
        font-weight: 700;
        color: var(--neutral-500, #64748b);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .saas-predikat-val {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 2.2rem;
        font-weight: 800;
        color: #16a34a;
        line-height: 1.1;
        margin: 4px 0;
    }

    .saas-predikat-score {
        font-size: 0.775rem;
        font-weight: 600;
        color: var(--neutral-600, #475569);
    }

    .saas-evaluasi-box {
        margin-top: 18px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 16px 20px;
    }

    .saas-evaluasi-title {
        font-family: var(--font-display, 'Lexend', sans-serif);
        font-size: 0.8125rem;
        color: #1e40af;
        display: block;
        margin-bottom: 4px;
    }

    .saas-evaluasi-content {
        font-size: 0.84rem;
        color: #1e3a8a;
        line-height: 1.5;
        margin: 0;
        font-style: italic;
    }

    /* --------------------------------------------------------------------------
       6. Drawer Modal Quick Logbook
       -------------------------------------------------------------------------- */
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

    /* --------------------------------------------------------------------------
       7. Responsive Layout Breakpoints
       -------------------------------------------------------------------------- */
    @media (max-width: 1024px) {
        .saas-cockpit-grid {
            grid-template-columns: 1fr;
        }

        .saas-cockpit-aside {
            position: static;
        }

        .saas-kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .saas-kpi-grid {
            grid-template-columns: 1fr;
        }

        .saas-hero-inner {
            flex-direction: column;
            align-items: flex-start;
        }

        .saas-hero-right {
            align-items: flex-start;
            width: 100%;
        }

        .saas-hero-action-buttons {
            width: 100%;
        }

        .saas-hero-btn-primary,
        .saas-hero-btn-secondary {
            width: 100%;
            justify-content: center;
        }
    }

    /* ==========================================================================
       8. Dark Mode Harmonization (WCAG 2.1 AA Compliant)
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

    /* Dark Hero Banner */
    [data-theme="dark"] .saas-hero-banner {
        background: linear-gradient(135deg, #142844 0%, #0d1b2e 100%) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.45) !important;
    }

    /* Dark KPI Bar (Surface #003566) */
    [data-theme="dark"] .saas-kpi-card {
        background: #003566 !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.35) !important;
    }

    [data-theme="dark"] .saas-kpi-card:hover {
        border-color: #3b82f6 !important;
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
        background: rgba(37, 99, 235, 0.25) !important;
        color: #93C5FD !important;
    }

    [data-theme="dark"] .saas-kpi-icon-green {
        background: rgba(16, 185, 129, 0.25) !important;
        color: #34D399 !important;
    }

    [data-theme="dark"] .saas-kpi-icon-amber {
        background: rgba(245, 158, 11, 0.25) !important;
        color: #FBBF24 !important;
    }

    [data-theme="dark"] .saas-kpi-icon-indigo {
        background: rgba(124, 58, 237, 0.25) !important;
        color: #C4B5FD !important;
    }

    /* Dark Cards & Panels */
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

    /* Dark Buttons */
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

    /* Dark Aside Context */
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

    [data-theme="dark"] .saas-aside-meta-list {
        background: #0F172A !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .saas-aside-contact-item {
        color: #CBD5E1 !important;
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

    [data-theme="dark"] .saas-quick-card {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .saas-quick-card:hover {
        background: #1E293B !important;
        border-color: #60A5FA !important;
    }

    [data-theme="dark"] .saas-quick-label {
        color: #E2E8F0 !important;
    }

    [data-theme="dark"] .saas-br09-banner {
        background: #241804 !important;
        border-color: #78350F !important;
    }

    [data-theme="dark"] .saas-br09-title {
        color: #FCD34D !important;
    }

    [data-theme="dark"] .saas-br09-badge {
        background: #451A03 !important;
        color: #FDE68A !important;
        border-color: #78350F !important;
    }

    [data-theme="dark"] .saas-br09-text {
        color: #FDE68A !important;
    }

    [data-theme="dark"] .saas-aside-subtitle-block {
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .saas-aside-info-box {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .saas-aside-info-box strong {
        color: #F8FAFC !important;
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

    /* Dark Stepper & Lists */
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

    [data-theme="dark"] .saas-divider-actions {
        border-top-color: #26354D !important;
    }

    [data-theme="dark"] .saas-alert-catatan {
        background: rgba(120, 53, 15, 0.35) !important;
        border-color: rgba(180, 83, 9, 0.5) !important;
    }

    [data-theme="dark"] .saas-alert-catatan-title {
        color: #FCD34D !important;
    }

    [data-theme="dark"] .saas-alert-catatan-desc {
        color: #FDE68A !important;
    }

    [data-theme="dark"] .saas-alert-revisi {
        background: rgba(120, 53, 15, 0.35) !important;
        border-color: rgba(180, 83, 9, 0.5) !important;
    }

    [data-theme="dark"] .saas-alert-revisi-text {
        color: #FDE68A !important;
    }

    [data-theme="dark"] .saas-list-item {
        border-bottom-color: #26354D !important;
    }

    [data-theme="dark"] .saas-item-title-heading {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-item-meta {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .saas-item-summary {
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .saas-tag-category {
        background: #0F172A !important;
        color: #CBD5E1 !important;
        border: 1px solid #26354D !important;
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

    [data-theme="dark"] .saas-selesai-banner {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .saas-selesai-desc {
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .saas-selesai-desc strong {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .saas-predikat-card {
        background: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .saas-evaluasi-box {
        background: rgba(30, 58, 138, 0.25) !important;
        border-color: rgba(96, 165, 250, 0.35) !important;
    }

    [data-theme="dark"] .saas-evaluasi-title {
        color: #93C5FD !important;
    }

    [data-theme="dark"] .saas-evaluasi-content {
        color: #BFDBFE !important;
    }

    /* Dark Drawer */
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
</style>
@endpush

@section('content')
<div class="saas-dashboard-workspace">

    {{-- 1. Atmospheric Hero Banner Header --}}
    @include('peserta.dashboard.partials._header')

    {{-- 2. Four-Metric KPI Bar (Full Width) --}}
    @if($fase === 'aktif' && $penempatan)
        @include('peserta.dashboard.partials._kpi_bar')
    @endif

    {{-- 3. Cockpit 2-Column Grid (Main Content + Aside Context) --}}
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
        <aside class="saas-cockpit-aside" id="dashboardAsideContext">
            @include('peserta.dashboard.partials._aside')
        </aside>

    </div>

    {{-- Drawer Modal Input Logbook Kilat: partial legacy yang styling CSS drawer miliknya (.cv-slide-drawer) kini di-manage secara scoped di dashboard peserta --}}
    @include('peserta.dashboard.partials._modal_quick_logbook')

</div>
@endsection
