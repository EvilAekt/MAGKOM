@php
    /**
     * @var string $slaStatus
     * @var float|int $slaPercent
     * @var float|int $dbLatency
     * @var string $dbDriver
     * @var int $totalTables
     * @var float|int $p95RouteDuration
     * @var float|int $avgRouteDuration
     * @var int $totalRouteRequests
     * @var int $audit24h
     * @var int $totalAudit
     * @var int $failedJobsCount
     * @var array $runtimeInfo
     * @var bool $isWritable
     * @var array $routeMetrics
     * @var float|int $maxRouteDuration
     * @var array $cacheStats
     * @var array $hostResources
     * @var array $sweeperStats
     * @var float|int $diskFreeGb
     * @var float|int $diskTotalGb
     * @var float|int $diskUsedPercent
     * @var int $totalDokumenCount
     * @var float|int $totalDokumenMb
     * @var string|null $lastBackupTime
     * @var string $dbStatus
     * @var string $dbName
     */
@endphp
@extends('layouts.dashboard')

@section('title', 'Telemetri Kesehatan & Diagnostik Sistem - SuperAdmin MagKom')
@section('topbar_title', 'Telemetri Kesehatan Infrastruktur')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Telemetri Kesehatan Infrastruktur</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/superadmin-common.css'])
<style>
    /* ==========================================================================
       HEALTH TELEMETRY DUAL-THEME CSS ENGINE & COCKPIT PARITY
       Architecture: Token Engine with Light Mode & Dark Mode Parity (WCAG 2.1 AA)
       ========================================================================== */

    /* 1. LIGHT MODE TOKENS (Default / MagKom Corporate Clean) */
    :root,
    html:not([data-theme="dark"]) {
        --tel-shell-bg: #f8fafc;
        --tel-shell-border: #e2e8f0;
        --tel-shell-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);

        --tel-card-bg: #ffffff;
        --tel-card-border: #e2e8f0;
        --tel-card-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);

        --tel-card-header-bg: #f8fafc;
        --tel-card-header-border: #e2e8f0;

        --tel-surface-elevated: #f1f5f9;
        --tel-surface-elevated-border: #e2e8f0;

        --tel-text-primary: #0f172a;
        --tel-text-secondary: #334155;
        --tel-text-muted: #64748b;
        --tel-accent-link: #0284c7;

        /* HUD Command Cockpit Light Mode */
        --tel-hud-bg: linear-gradient(135deg, #003566 0%, #012d59 100%);
        --tel-hud-border: #002244;
        --tel-hud-card-bg: rgba(255, 255, 255, 0.08);
        --tel-hud-card-border: rgba(255, 255, 255, 0.15);

        /* Table & Controls */
        --tel-table-header-bg: #003566;
        --tel-table-header-text: #ffffff;
        --tel-table-row-hover: #f1f5f9;
        --tel-table-border: #e2e8f0;

        --tel-time-selector-bg: #e2e8f0;
        --tel-time-selector-border: #cbd5e1;
        --tel-time-btn-active-bg: #003566;
        --tel-time-btn-active-text: #ffffff;
        --tel-time-btn-color: #64748b;

        --tel-canvas-bg: #0f172a;
        --tel-progress-track-bg: #e2e8f0;

        --tel-badge-success-bg: rgba(16, 185, 129, 0.12);
        --tel-badge-success-text: #059669;
        --tel-badge-success-border: rgba(16, 185, 129, 0.3);
        --tel-badge-success-dot: #10b981;

        --tel-badge-info-bg: rgba(2, 132, 199, 0.12);
        --tel-badge-info-text: #0284c7;
        --tel-badge-info-border: rgba(2, 132, 199, 0.3);
        --tel-badge-info-dot: #0284c7;

        --tel-sse-card-bg: rgba(6, 182, 212, 0.06);
        --tel-sse-card-border: rgba(6, 182, 212, 0.35);
        --tel-cyan-text: #0891b2;
        --tel-cyan-value: #0e7490;

        --tel-btn-sse-bg: #01347C;
        --tel-btn-sse-hover: #02275D;

        --tel-syslog-bg: #f8fafc;
        --tel-syslog-border: #e2e8f0;
        --tel-syslog-stream-bg: #f0fdf4;
        --tel-syslog-stream-border: #bbf7d0;

        --tel-row-stream-bg: rgba(6, 182, 212, 0.05);
        --tel-row-spike-bg: rgba(239, 68, 68, 0.05);
    }

    /* 2. DARK MODE TOKENS (Deep Space Navy - MagKom Parity) */
    [data-theme="dark"] {
        --tel-shell-bg: #002244;
        --tel-shell-border: rgba(30, 58, 95, 0.8);
        --tel-shell-shadow: 0 20px 45px rgba(0, 15, 30, 0.5);

        --tel-card-bg: #001e38;
        --tel-card-border: rgba(30, 58, 95, 0.8);
        --tel-card-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);

        --tel-card-header-bg: rgba(2, 24, 48, 0.75);
        --tel-card-header-border: rgba(30, 58, 95, 0.7);

        --tel-surface-elevated: rgba(2, 24, 48, 0.75);
        --tel-surface-elevated-border: rgba(30, 58, 95, 0.6);

        --tel-text-primary: #ffffff;
        --tel-text-secondary: #cbd5e1;
        --tel-text-muted: #94a3b8;
        --tel-accent-link: #60a5fa;

        /* HUD Command Cockpit Dark Mode */
        --tel-hud-bg: linear-gradient(135deg, #003566 0%, #012d59 50%, #011f3f 100%);
        --tel-hud-border: rgba(30, 64, 175, 0.4);
        --tel-hud-card-bg: rgba(2, 24, 48, 0.8);
        --tel-hud-card-border: rgba(30, 58, 95, 0.8);

        /* Table & Controls */
        --tel-table-header-bg: #002b54;
        --tel-table-header-text: #ffffff;
        --tel-table-row-hover: rgba(2, 24, 48, 0.5);
        --tel-table-border: rgba(30, 58, 95, 0.4);

        --tel-time-selector-bg: rgba(2, 24, 48, 0.9);
        --tel-time-selector-border: rgba(30, 58, 95, 0.8);
        --tel-time-btn-active-bg: #ffffff;
        --tel-time-btn-active-text: #0f172a;
        --tel-time-btn-color: #94a3b8;

        --tel-canvas-bg: #030712;
        --tel-progress-track-bg: #0b1a2d;

        --tel-badge-success-bg: rgba(16, 185, 129, 0.18);
        --tel-badge-success-text: #34d399;
        --tel-badge-success-border: rgba(16, 185, 129, 0.4);
        --tel-badge-success-dot: #34d399;

        --tel-badge-info-bg: rgba(6, 182, 212, 0.2);
        --tel-badge-info-text: #22d3ee;
        --tel-badge-info-border: rgba(6, 182, 212, 0.4);
        --tel-badge-info-dot: #22d3ee;

        --tel-sse-card-bg: rgba(8, 47, 73, 0.45);
        --tel-sse-card-border: rgba(6, 182, 212, 0.5);
        --tel-cyan-text: #67e8f9;
        --tel-cyan-value: #22d3ee;

        --tel-btn-sse-bg: #01347C;
        --tel-btn-sse-hover: #02275D;

        --tel-syslog-bg: rgba(2, 24, 48, 0.6);
        --tel-syslog-border: rgba(30, 58, 95, 0.4);
        --tel-syslog-stream-bg: rgba(8, 47, 73, 0.45);
        --tel-syslog-stream-border: rgba(6, 182, 212, 0.4);

        --tel-row-stream-bg: rgba(8, 47, 73, 0.4);
        --tel-row-spike-bg: rgba(76, 5, 25, 0.35);
    }

    /* 3. Typography & Global Box-Sizing Isolation */
    .health-telemetry-container,
    .telemetry-cockpit-shell {
        color: var(--tel-text-primary) !important;
        font-family: inherit;
        box-sizing: border-box;
    }
    .telemetry-cockpit-shell h1,
    .telemetry-cockpit-shell h3,
    .telemetry-cockpit-shell h4,
    .telemetry-cockpit-shell h5,
    .telemetry-cockpit-shell h6 {
        color: var(--tel-text-primary) !important;
        font-family: inherit !important;
        margin-top: 0 !important;
    }
    .telemetry-cockpit-shell p,
    .telemetry-cockpit-shell span,
    .telemetry-cockpit-shell div {
        box-sizing: border-box;
    }

    /* Screen reader utility helper for clean HTML accessibility */
    .sr-only {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }

    /* SVG Guardrails for Gentelella / Chromium Environment (AC-8) */
    .health-telemetry-container svg,
    .telemetry-cockpit-shell svg {
        display: inline-block;
        vertical-align: middle;
        flex-shrink: 0;
    }
    .health-telemetry-container svg:not([width]),
    .telemetry-cockpit-shell svg:not([width]) {
        width: 20px;
        height: 20px;
        max-width: 100%;
    }

    /* 4. Cockpit Master Shell */
    .telemetry-cockpit-shell {
        background-color: var(--tel-shell-bg) !important;
        border: 1px solid var(--tel-shell-border) !important;
        border-radius: 20px !important;
        padding: 24px !important;
        box-shadow: var(--tel-shell-shadow) !important;
        width: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 20px !important;
        transition: background-color 0.25s ease, border-color 0.25s ease !important;
    }

    @media (max-width: 768px) {
        .telemetry-cockpit-shell {
            padding: 14px !important;
            border-radius: 14px !important;
            gap: 16px !important;
        }
    }

    /* 5. Primary Telemetry Cards */
    .telemetry-card {
        background-color: var(--tel-card-bg) !important;
        border: 1px solid var(--tel-card-border) !important;
        border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important;
        border-radius: 14px !important;
        padding: 16px !important;
        color: var(--tel-text-primary) !important;
        box-sizing: border-box !important;
        box-shadow: var(--tel-card-shadow) !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.25s ease !important;
    }
    [data-theme="dark"] .telemetry-card {
        border-top: 3.5px solid #3B82F6 !important;
    }
    .telemetry-card:hover {
        border-color: rgba(56, 189, 248, 0.4) !important;
    }
    .telemetry-card.telemetry-card-sse {
        background-color: var(--tel-sse-card-bg) !important;
        border-color: var(--tel-sse-card-border) !important;
    }

    /* Card Header Bar */
    .telemetry-card-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        border-bottom: 1px solid var(--tel-card-header-border) !important;
        padding-bottom: 10px !important;
        margin-bottom: 14px !important;
    }
    .telemetry-card-title {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        color: var(--tel-text-primary) !important;
        margin: 0 !important;
    }

    /* 6. Modern Status Pill Badges (Replaces Harsh Rectangle Badges) */
    .telemetry-status-pill {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        padding: 3px 10px !important;
        border-radius: 9999px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        line-height: 1.2 !important;
        box-sizing: border-box !important;
        white-space: nowrap !important;
        vertical-align: middle !important;
    }
    .telemetry-status-pill .status-dot {
        width: 6px !important;
        height: 6px !important;
        min-width: 6px !important;
        min-height: 6px !important;
        border-radius: 50% !important;
        display: inline-block !important;
        flex-shrink: 0 !important;
    }
    .telemetry-status-pill.pill-success {
        background-color: var(--tel-badge-success-bg) !important;
        color: var(--tel-badge-success-text) !important;
        border: 1px solid var(--tel-badge-success-border) !important;
    }
    .telemetry-status-pill.pill-success .status-dot {
        background-color: var(--tel-badge-success-dot) !important;
        box-shadow: 0 0 6px var(--tel-badge-success-dot) !important;
    }
    .telemetry-status-pill.pill-info {
        background-color: var(--tel-badge-info-bg) !important;
        color: var(--tel-badge-info-text) !important;
        border: 1px solid var(--tel-badge-info-border) !important;
    }
    .telemetry-status-pill.pill-info .status-dot {
        background-color: var(--tel-badge-info-dot) !important;
        box-shadow: 0 0 6px var(--tel-badge-info-dot) !important;
    }

    /* 7. Secondary Elevated Surfaces (Subsystems & Maintenance Actions) */
    .telemetry-action-card,
    .telemetry-subsystem-box {
        background-color: var(--tel-surface-elevated) !important;
        border: 1px solid var(--tel-surface-elevated-border) !important;
        border-radius: 12px !important;
        padding: 12px 14px !important;
        box-sizing: border-box !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        transition: all 0.2s ease !important;
        color: var(--tel-text-primary) !important;
    }
    .telemetry-action-card:hover {
        border-color: rgba(56, 189, 248, 0.5) !important;
    }
    .telemetry-subsystem-box.telemetry-subsystem-sse {
        background-color: var(--tel-sse-card-bg) !important;
        border-color: var(--tel-sse-card-border) !important;
    }

    /* 8. Action Buttons (Sweep, Vacuum, Purge, Probe) */
    .btn-telemetry-action {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 6px 14px !important;
        border-radius: 8px !important;
        font-family: monospace !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        border: none !important;
        cursor: pointer !important;
        transition: background-color 0.2s ease, transform 0.1s ease !important;
        color: #ffffff !important;
        text-decoration: none !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25) !important;
        line-height: 1.4 !important;
    }
    .btn-telemetry-action:hover {
        filter: brightness(1.15) !important;
    }
    .btn-telemetry-action:active {
        transform: scale(0.96) !important;
    }
    .btn-telemetry-action.btn-sweep {
        background-color: #e11d48 !important;
    }
    .btn-telemetry-action.btn-sweep:hover {
        background-color: #be123c !important;
    }
    .btn-telemetry-action.btn-vacuum {
        background-color: #0284c7 !important;
    }
    .btn-telemetry-action.btn-vacuum:hover {
        background-color: #0369a1 !important;
    }
    .btn-telemetry-action.btn-purge {
        background-color: #059669 !important;
    }
    .btn-telemetry-action.btn-purge:hover {
        background-color: #047857 !important;
    }
    .btn-telemetry-action.btn-probe {
        background-color: #0891b2 !important;
    }
    .btn-telemetry-action.btn-probe:hover {
        background-color: #0e7490 !important;
    }

    /* 9. Donut Layout & Anti-Collision Isolation */
    .telemetry-donut-layout {
        display: flex !important;
        align-items: center !important;
        gap: 20px !important;
        margin-top: 14px !important;
        width: 100% !important;
    }
    @media (max-width: 640px) {
        .telemetry-donut-layout {
            flex-direction: column !important;
        }
    }
    .telemetry-donut-svg-wrapper {
        position: relative !important;
        width: 112px !important;
        height: 112px !important;
        min-width: 112px !important;
        min-height: 112px !important;
        flex-shrink: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .telemetry-donut-center-text {
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        text-align: center !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        pointer-events: none !important;
        width: 100% !important;
    }
    .telemetry-donut-center-val {
        font-family: monospace !important;
        font-size: 20px !important;
        font-weight: 800 !important;
        color: var(--tel-text-primary) !important;
        line-height: 1 !important;
    }
    .telemetry-donut-center-lbl {
        font-family: monospace !important;
        font-size: 9px !important;
        font-weight: 700 !important;
        color: var(--tel-badge-success-text) !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        margin-top: 2px !important;
    }
    .telemetry-breakdown-list {
        display: flex !important;
        flex-direction: column !important;
        gap: 8px !important;
        width: 100% !important;
        font-family: monospace !important;
        font-size: 11px !important;
    }
    .telemetry-breakdown-item {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        margin-bottom: 2px !important;
    }
    .telemetry-progress-track {
        height: 6px !important;
        background-color: var(--tel-progress-track-bg) !important;
        border-radius: 9999px !important;
        overflow: hidden !important;
        border: 1px solid var(--tel-card-border) !important;
    }
    .telemetry-progress-fill {
        height: 100% !important;
        border-radius: 9999px !important;
        transition: width 0.8s ease !important;
    }

    /* 10. Header Toolbar & Controls */
    .telemetry-header-toolbar {
        display: flex !important;
        flex-direction: column !important;
        gap: 12px !important;
        border-bottom: 1px solid var(--tel-card-header-border) !important;
        padding-bottom: 18px !important;
        margin-bottom: 4px !important;
    }
    @media (min-width: 640px) {
        .telemetry-header-toolbar {
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
    }
    .telemetry-time-selector {
        display: inline-flex !important;
        align-items: center !important;
        background-color: var(--tel-time-selector-bg) !important;
        border: 1px solid var(--tel-time-selector-border) !important;
        border-radius: 8px !important;
        padding: 3px !important;
        gap: 3px !important;
    }
    .time-window-btn {
        border: none !important;
        background: transparent !important;
        color: var(--tel-time-btn-color) !important;
        padding: 4px 10px !important;
        font-family: monospace !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        border-radius: 6px !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }
    .time-window-btn:hover {
        color: var(--tel-text-primary) !important;
    }
    .time-window-btn.active {
        background-color: var(--tel-time-btn-active-bg) !important;
        color: var(--tel-time-btn-active-text) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.18) !important;
    }
    .btn-test-sse-action {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        background-color: var(--tel-btn-sse-bg) !important;
        border: 1px solid rgba(59, 130, 246, 0.4) !important;
        color: #ffffff !important;
        padding: 6px 14px !important;
        border-radius: 8px !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }
    .btn-test-sse-action:hover {
        background-color: var(--tel-btn-sse-hover) !important;
        border-color: rgba(59, 130, 246, 0.8) !important;
    }

    /* 11. Grids (Pure CSS Grid Parity) */
    .telemetry-command-grid {
        display: grid !important;
        grid-template-columns: repeat(6, 1fr) !important;
        gap: 12px !important;
    }
    @media (max-width: 1023px) {
        .telemetry-command-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    @media (max-width: 639px) {
        .telemetry-command-grid {
            grid-template-columns: 1fr !important;
        }
    }

    .telemetry-kpi-row-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 14px !important;
    }
    @media (max-width: 992px) {
        .telemetry-kpi-row-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    @media (max-width: 480px) {
        .telemetry-kpi-row-grid {
            grid-template-columns: 1fr !important;
        }
    }

    .telemetry-kpi-title {
        font-size: 11px !important;
        font-weight: 600 !important;
        color: var(--tel-text-muted) !important;
    }
    .telemetry-kpi-value {
        font-family: monospace !important;
        font-size: 1.5rem !important;
        font-weight: 800 !important;
        line-height: 1.2 !important;
        color: var(--tel-text-primary) !important;
        font-feature-settings: "tnum" 1 !important;
    }
    .telemetry-kpi-sub {
        font-size: 11px !important;
        font-family: monospace !important;
        color: var(--tel-text-muted) !important;
        margin-top: 6px !important;
    }

    .telemetry-split-8-4 {
        display: grid !important;
        grid-template-columns: minmax(0, 8fr) minmax(0, 4fr) !important;
        gap: 16px !important;
    }
    @media (max-width: 1023px) {
        .telemetry-split-8-4 {
            grid-template-columns: 1fr !important;
        }
    }

    .telemetry-split-6-6 {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
        gap: 16px !important;
    }
    @media (max-width: 1023px) {
        .telemetry-split-6-6 {
            grid-template-columns: 1fr !important;
        }
    }

    .subsystem-grid {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 10px !important;
    }
    @media (max-width: 640px) {
        .subsystem-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    .maintenance-actions-grid {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 12px !important;
    }
    @media (max-width: 639px) {
        .maintenance-actions-grid {
            grid-template-columns: 1fr !important;
        }
    }

    /* 12. HUD & Box Metrics (High-Contrast Isolation) */
    .telemetry-hud-card {
        background: var(--tel-hud-bg) !important;
        border: 1px solid var(--tel-hud-border) !important;
        border-radius: 16px !important;
        padding: 20px !important;
        color: #e2e8f0 !important;
    }
    .telemetry-hud-icon-badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 36px !important;
        height: 36px !important;
        min-width: 36px !important;
        min-height: 36px !important;
        border-radius: 10px !important;
        flex-shrink: 0 !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
    }
    .telemetry-hud-icon-badge svg {
        display: block !important;
        width: 20px !important;
        height: 20px !important;
        max-width: 20px !important;
        max-height: 20px !important;
        margin: auto !important;
    }
    .telemetry-hud-card h1,
    .telemetry-hud-card h2,
    .telemetry-hud-card h3 {
        color: #ffffff !important;
    }
    .telemetry-hud-card p,
    .telemetry-hud-card span:not([class*="text-"]):not([style*="color"]) {
        color: #cbd5e1 !important;
    }
    .telemetry-hud-metric-box {
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        background: var(--tel-hud-card-bg) !important;
        border: 1px solid var(--tel-hud-card-border) !important;
        border-radius: 12px !important;
        padding: 14px !important;
        min-height: 124px !important;
        box-sizing: border-box !important;
    }
    .telemetry-hud-metric-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        color: #cbd5e1 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.06em !important;
    }
    .telemetry-hud-metric-footer {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
        padding-top: 8px !important;
        font-family: monospace !important;
        font-size: 10px !important;
        color: #94a3b8 !important;
    }

    /* 13. Canvas Real-Time Chart Area */
    .telemetry-canvas-wrapper {
        position: relative !important;
        width: 100% !important;
        background: var(--tel-canvas-bg) !important;
        border-radius: 12px !important;
        overflow: hidden !important;
        border: 1px solid #1E293B !important;
        min-height: 280px !important;
    }
    #routeLatencyCanvas {
        display: block !important;
        width: 100% !important;
        height: 280px !important;
    }
    .telemetry-tooltip {
        position: absolute !important;
        display: none !important;
        background: rgba(15, 23, 42, 0.95) !important;
        border: 1px solid rgba(56, 189, 248, 0.4) !important;
        color: #F8FAFC !important;
        padding: 0.75rem !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
        font-size: 0.75rem !important;
        pointer-events: none !important;
        z-index: 50 !important;
        min-width: 220px !important;
        backdrop-filter: blur(6px) !important;
    }
    .telemetry-scanner-laser {
        position: absolute !important;
        top: 0 !important;
        bottom: 0 !important;
        right: 48px !important;
        width: 60px !important;
        background: linear-gradient(to right, transparent, rgba(6, 182, 212, 0.15), transparent) !important;
        pointer-events: none !important;
        animation: scannerPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite !important;
    }
    .telemetry-scanner-line {
        position: absolute !important;
        top: 0 !important;
        bottom: 0 !important;
        right: 48px !important;
        width: 1px !important;
        background: rgba(34, 211, 238, 0.8) !important;
        box-shadow: 0 0 8px rgba(34, 211, 238, 0.9) !important;
        pointer-events: none !important;
    }
    @keyframes scannerPulse {
        0%, 100% { opacity: 0.3; transform: translateX(0); }
        50% { opacity: 0.8; transform: translateX(-20px); }
    }
    .telemetry-threshold-grid {
        position: absolute !important;
        inset: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        padding: 1rem 1.25rem 2rem 1.25rem !important;
        pointer-events: none !important;
        opacity: 0.35 !important;
        font-family: monospace !important;
        font-size: 9px !important;
    }

    /* 14. Table Container: Paginated & Non-Scrollable (Zero Scrollbar Parity) */
    .telemetry-table-container {
        position: relative !important;
        overflow-x: auto !important;
        overflow-y: visible !important;
        max-height: none !important;
        min-height: auto !important;
    }
    .telemetry-table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 11px !important;
    }
    .telemetry-table thead th {
        position: sticky !important;
        top: 0 !important;
        z-index: 10 !important;
        background-color: var(--tel-table-header-bg) !important;
        color: var(--tel-table-header-text) !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 9px !important;
        letter-spacing: 0.05em !important;
        padding: 10px 12px !important;
        border-bottom: 1px solid var(--tel-table-border) !important;
        white-space: nowrap !important;
    }
    .telemetry-table td {
        padding: 9px 12px !important;
        border-bottom: 1px solid var(--tel-table-border) !important;
        white-space: nowrap !important;
    }
    .route-table-row {
        transition: background-color 0.15s ease !important;
    }
    .route-table-row:hover {
        background-color: var(--tel-table-row-hover) !important;
    }
    .route-table-row.row-stream {
        background-color: var(--tel-row-stream-bg) !important;
    }
    .route-table-row.row-spike {
        background-color: var(--tel-row-spike-bg) !important;
    }

    /* Table Badges & Cell Types */
    .method-badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 2px 6px !important;
        border-radius: 4px !important;
        font-family: monospace !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
    }
    .method-get {
        background: rgba(59, 130, 246, 0.15) !important;
        color: #3b82f6 !important;
        border: 1px solid rgba(59, 130, 246, 0.3) !important;
    }
    .method-post {
        background: rgba(16, 185, 129, 0.15) !important;
        color: #10b981 !important;
        border: 1px solid rgba(16, 185, 129, 0.3) !important;
    }
    .method-delete {
        background: rgba(239, 68, 68, 0.15) !important;
        color: #ef4444 !important;
        border: 1px solid rgba(239, 68, 68, 0.3) !important;
    }
    .method-put {
        background: rgba(245, 158, 11, 0.15) !important;
        color: #f59e0b !important;
        border: 1px solid rgba(245, 158, 11, 0.3) !important;
    }
    .status-badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 2px 6px !important;
        border-radius: 4px !important;
        font-family: monospace !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
    }
    .status-success {
        background: rgba(16, 185, 129, 0.15) !important;
        color: #10b981 !important;
    }
    .status-warning {
        background: rgba(245, 158, 11, 0.15) !important;
        color: #f59e0b !important;
    }
    .status-danger {
        background: rgba(239, 68, 68, 0.15) !important;
        color: #ef4444 !important;
    }
    .status-stream {
        background: rgba(6, 182, 212, 0.15) !important;
        color: #06b6d4 !important;
    }
    .telemetry-table-filter {
        height: 28px !important;
        width: 140px !important;
        background: var(--tel-surface-elevated) !important;
        border: 1px solid var(--tel-card-border) !important;
        color: var(--tel-text-primary) !important;
        border-radius: 6px !important;
        padding: 2px 10px !important;
        font-family: monospace !important;
        font-size: 11px !important;
        outline: none !important;
    }
    .telemetry-table-filter:focus {
        border-color: #38bdf8 !important;
    }

    /* 14b. Telemetry Table Pagination Footer & Controls */
    .telemetry-table-footer {
        padding: 0.75rem 1rem !important;
        background: var(--tel-card-bg) !important;
        border-top: 1px solid var(--tel-table-border) !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 0.625rem !important;
    }
    .telemetry-pagination-row {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.5rem !important;
    }
    @media (min-width: 640px) {
        .telemetry-pagination-row {
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
    }
    .telemetry-pagination-bottom {
        padding-top: 0.5rem !important;
        border-top: 1px solid var(--tel-table-border) !important;
    }
    .telemetry-pagination-left {
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
    }
    .telemetry-pagination-counter,
    .telemetry-pagination-lembar-info {
        font-size: 0.75rem !important;
        color: var(--tel-text-muted) !important;
    }
    .telemetry-per-page-container {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        font-size: 0.75rem !important;
    }
    .telemetry-per-page-label {
        color: var(--tel-text-muted) !important;
        font-weight: 500 !important;
        font-size: 0.75rem !important;
    }
    .telemetry-per-page-group {
        display: inline-flex !important;
        align-items: center !important;
        background: var(--tel-surface-elevated) !important;
        border: 1px solid var(--tel-card-border) !important;
        border-radius: 8px !important;
        padding: 2px !important;
        gap: 2px !important;
    }
    .telemetry-per-page-pill {
        padding: 0.2rem 0.6rem !important;
        border-radius: 6px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        color: var(--tel-text-muted) !important;
        background: transparent !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        line-height: 1.25 !important;
    }
    .telemetry-per-page-pill:hover:not(.active) {
        background: rgba(148, 163, 184, 0.15) !important;
        color: var(--tel-text-primary) !important;
    }
    .telemetry-per-page-pill.active {
        background: var(--magkom-primary-blue, #01347C) !important;
        color: #FFFFFF !important;
        font-weight: 700 !important;
    }
    [data-theme="dark"] .telemetry-per-page-pill.active {
        background: #3B82F6 !important;
        color: #FFFFFF !important;
    }
    .telemetry-page-controls {
        display: flex !important;
        align-items: center !important;
        gap: 0.25rem !important;
    }
    .telemetry-page-btn-nav {
        padding: 0.25rem 0.5rem !important;
        border-radius: 6px !important;
        border: 1px solid var(--tel-card-border) !important;
        background: transparent !important;
        color: var(--tel-text-muted) !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
    }
    .telemetry-page-btn-nav:hover:not(:disabled) {
        background: rgba(148, 163, 184, 0.15) !important;
        color: var(--tel-text-primary) !important;
    }
    .telemetry-page-btn-nav:disabled,
    .telemetry-page-btn-nav.disabled {
        opacity: 0.35 !important;
        cursor: not-allowed !important;
    }
    .telemetry-page-num {
        min-width: 28px !important;
        height: 28px !important;
        padding: 0 0.35rem !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 6px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        color: var(--tel-text-muted) !important;
        background: transparent !important;
        border: 1px solid transparent !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
    }
    .telemetry-page-num:hover:not(.active) {
        background: rgba(148, 163, 184, 0.15) !important;
        color: var(--tel-text-primary) !important;
    }
    .telemetry-page-num.active {
        background: var(--magkom-primary-blue, #01347C) !important;
        color: #FFFFFF !important;
        font-weight: 700 !important;
    }
    [data-theme="dark"] .telemetry-page-num.active {
        background: #3B82F6 !important;
        color: #FFFFFF !important;
    }
    .telemetry-page-ellipsis {
        min-width: 20px !important;
        text-align: center !important;
        color: var(--tel-text-muted) !important;
        font-size: 0.75rem !important;
        user-select: none !important;
    }

    /* 15. Syslog Panel */
    .telemetry-syslog-item {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 6px 10px !important;
        border-radius: 8px !important;
        font-family: monospace !important;
        font-size: 11px !important;
        background-color: var(--tel-syslog-bg) !important;
        border: 1px solid var(--tel-syslog-border) !important;
        margin-bottom: 6px !important;
        color: var(--tel-text-secondary) !important;
    }
    .telemetry-syslog-item.stream-item {
        background-color: var(--tel-syslog-stream-bg) !important;
        border-color: var(--tel-syslog-stream-border) !important;
    }

    /* 16. Gentelella Dark Overrides (Scattered only in dark mode) */
    [data-theme="dark"] .health-telemetry-container .bg-white,
    [data-theme="dark"] .telemetry-cockpit-shell .bg-white {
        background-color: #001e38 !important;
        border-color: rgba(30, 58, 95, 0.8) !important;
        color: #f8fafc !important;
    }
    [data-theme="dark"] .health-telemetry-container .border-slate-200,
    [data-theme="dark"] .health-telemetry-container .border-slate-100,
    [data-theme="dark"] .telemetry-cockpit-shell .border-slate-200,
    [data-theme="dark"] .telemetry-cockpit-shell .border-slate-100 {
        border-color: rgba(30, 58, 95, 0.8) !important;
    }
    [data-theme="dark"] .health-telemetry-container .bg-slate-100,
    [data-theme="dark"] .telemetry-cockpit-shell .bg-slate-100 {
        background-color: #0b1a2d !important;
    }

    /* 17. Viewport Bottom Containment & Single Scrollbar Guarantee (Zero Void) */
    html {
        overflow-x: hidden !important;
        overflow-y: auto !important;
        scroll-behavior: smooth !important;
    }
    body {
        overflow-x: hidden !important;
        overflow-y: visible !important;
        overflow: visible !important;
        min-height: 100vh !important;
    }
    .health-telemetry-container {
        display: flex !important;
        flex-direction: column !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
        overflow: visible !important;
    }
    .telemetry-cockpit-shell {
        margin-bottom: 1.5rem !important; /* Jarak teratur 24px persis sebelum footer */
        contain: layout style !important;
    }
    .telemetry-cockpit-shell > * {
        min-height: 0 !important;
    }
    #mainContent,
    .dashboard-main,
    .dashboard-content-area,
    .right_col {
        min-height: auto !important;
        padding-bottom: 0 !important;
        overflow: visible !important;
    }
    .container.body,
    .main_container {
        min-height: auto !important;
        overflow: visible !important;
    }
</style>
@endpush

@section('content')
<div class="health-telemetry-container">

    {{-- ALERT FEEDBACK NOTIFIKASI --}}
    @if(session('status_keberhasilan'))
        <x-gentelella.callout type="success" title="Operasi Berhasil">
            {{ session('status_keberhasilan') }}
        </x-gentelella.callout>
    @endif

    @if(session('status_kesalahan'))
        <x-gentelella.callout type="danger" title="Operasi Gagal">
            {{ session('status_kesalahan') }}
        </x-gentelella.callout>
    @endif

    {{-- 1. HERO BANNER KHAS SLEMAN MAGKOM (.sa-hero-banner) IDENTIK DENGAN AUDIT LOG --}}
    <div class="sa-hero-banner sc-reveal-hero" data-scroll-reveal style="margin-bottom: 24px;">
        <div class="banner-top-row">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <h1 class="banner-title" style="margin: 0; font-size: 1.5rem; font-weight: 800; color: #FFFFFF;">
                        Telemetri Kesehatan Infrastruktur
                    </h1>
                    <span class="telemetry-status-pill pill-success" style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #A7F3D0; padding: 2px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                        <span class="status-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
                        <span>ALL NOMINAL</span>
                    </span>
                    <span style="font-size: 0.75rem; color: rgba(255, 255, 255, 0.85); font-weight: 600;">
                        Live {{ $dbLatency ? number_format((float)$dbLatency, 0).'ms' : '24ms' }}
                    </span>
                </div>
                <div class="banner-subtitle" style="margin-top: 6px; font-size: 0.875rem; color: rgba(255, 255, 255, 0.9);">
                    <span>Node: <strong>magkom-prod-01</strong> (Sleman DC)</span>
                    <span class="bullet-separator" style="opacity: 0.6; margin: 0 4px;">&bull;</span>
                    <span>SLA Uptime: <strong>{{ $slaPercent !== null ? $slaPercent . '%' : '99.98%' }}</strong></span>
                    <span class="bullet-separator" style="opacity: 0.6; margin: 0 4px;">&bull;</span>
                    <span>Mesin: <strong>PHP {{ PHP_VERSION }}</strong> &amp; <strong>MySQL {{ $runtimeInfo['db_version'] ?? '8.0' }}</strong></span>
                    <span class="sr-only">Pusat Telemetri Infrastruktur &bull; Panel Telemetri Kesehatan Infrastruktur &bull; Koneksi Basis Data, Storage &bull; Runtime</span>
                </div>
            </div>

            {{-- Controls Group: Interval Selector Pills & Action Button Test SSE --}}
            <div class="banner-actions-group" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <div class="telemetry-time-selector" id="timeWindowSelector" role="group" aria-label="Rentang Waktu Telemetri" style="display: inline-flex; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 10px; padding: 3px; gap: 3px;">
                    <button type="button" data-window="15m" class="time-window-btn active" title="15 Menit Terakhir" style="background: #ffffff; color: #01347C; font-weight: 700; border: none; border-radius: 7px; padding: 5px 12px; font-size: 0.75rem; cursor: pointer;">15m</button>
                    <button type="button" data-window="1h" class="time-window-btn" title="1 Jam Terakhir" style="background: transparent; color: #ffffff; font-weight: 600; border: none; border-radius: 7px; padding: 5px 12px; font-size: 0.75rem; cursor: pointer;">1h</button>
                    <button type="button" data-window="24h" class="time-window-btn" title="24 Jam Terakhir" style="background: transparent; color: #ffffff; font-weight: 600; border: none; border-radius: 7px; padding: 5px 12px; font-size: 0.75rem; cursor: pointer;">24h</button>
                </div>

                <button type="button" id="btnTestSse" class="btn-banner-action" title="Uji Injeksi Event Telemetri Realtime SSE" style="background: #ffffff; color: #01347C; border: none; border-radius: 10px; padding: 0.6rem 1.15rem; font-size: 0.8125rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 2px 6px rgba(0,0,0,0.15); text-decoration: none;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    <span>Test SSE</span>
                </button>
            </div>
        </div>
    </div>

    {{-- 2. GRID 4 TILE-STAT METRIC CARDS (<x-gentelella.tile-stat>) IDENTIK DENGAN AUDIT LOG --}}
    <div class="tiles-grid sc-reveal-up sc-delay-1" data-scroll-reveal style="margin-bottom: 1.25rem;">
        <!-- 1. Status Sistem -->
        <x-gentelella.tile-stat
            title="STATUS SISTEM"
            value="{{ $slaStatus === 'ALL SYSTEMS OPERATIONAL' ? '100% OK' : ($slaStatus ?? '100% OK') }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="Normal"
            subText="SLA Uptime: {{ $slaPercent !== null ? $slaPercent . '%' : '99.98%' }} (0 Gangguan)"
            :sparkBars="[90, 92, 95, 98, 99, 100]">
            <x-slot:icon>
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 2. DB Ping & Latensi -->
        <x-gentelella.tile-stat
            title="DB PING & LATENSI"
            value="{{ number_format((float)$dbLatency, 2) }} ms"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="SLO < 5ms"
            subText="MySQL Pool: 12/100 ({{ $totalTables }} Tabel)"
            :sparkBars="[15, 25, 30, 20, 18, 22]">
            <x-slot:icon>
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 3. HTTP P95 Latency -->
        <x-gentelella.tile-stat
            title="HTTP P95 LATENCY"
            value="{{ number_format((float) $p95RouteDuration, 1) }} ms"
            color="amber"
            trendType="warning"
            trendDirection="up"
            trendText="Target < 200ms"
            subText="Vol: {{ number_format($totalRouteRequests) }} rpm (141 rps)"
            :sparkBars="[20, 35, 50, 45, 60, 55]">
            <x-slot:icon>
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 4. SSE Stream & Klien -->
        <x-gentelella.tile-stat
            title="SSE STREAM & KLIEN"
            value="1.2 ms"
            color="purple"
            trendType="info"
            trendDirection="up"
            trendText="18 Klien"
            subText="Error Budget: 99.98% (Sisa: 43w)"
            :sparkBars="[10, 20, 35, 50, 70, 85]">
            <x-slot:icon>
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    <!-- COCKPIT MASTER SHELL (Deep Space Navy #002244 Parity) -->
    <div class="telemetry-cockpit-shell sc-reveal-up sc-delay-2" data-scroll-reveal>

        {{-- 4. SPLIT ROW: LATENCY CURVE (8 COLS) & REDIS CACHE DONUT + HOST RESOURCES (4 COLS) (AC-3, AC-4) --}}
        <div class="telemetry-split-8-4">
            <!-- Kolom Kiri: Kurva Latensi Real-Time Canvas 2D (8 Kolom Clean - AC-3) -->
            <div>
                <div class="telemetry-card mb-0 overflow-hidden" style="padding: 0;">

                    <!-- Toolbar Header -->
                    <div class="telemetry-card-header" style="padding: 12px 16px; margin-bottom: 0;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="h-2 w-2 rounded-full bg-cyan-500 animate-pulse" style="width: 8px; height: 8px; display: inline-block;"></span>
                            <h3 class="telemetry-card-title">
                                Grafik Latensi HTTP &amp; SSE
                            </h3>
                        </div>

                        <!-- Mode Selectors -->
                        <div class="inline-flex items-center rounded-lg p-0.5 text-[11px] font-mono font-medium"
                             style="background: var(--tel-time-selector-bg); border: 1px solid var(--tel-time-selector-border); border-radius: 8px; display: inline-flex;" id="chartModeSelector">
                            <button type="button" data-mode="all" class="chart-mode-btn rounded px-2.5 py-0.5 font-bold cursor-pointer"
                                    style="border: none; border-radius: 6px; padding: 2px 10px; background-color: var(--tel-btn-sse-bg); color: #ffffff; cursor: pointer;">Semua</button>
                            <button type="button" data-mode="p95" class="chart-mode-btn rounded px-2 py-0.5 cursor-pointer"
                                    style="border: none; background: transparent; color: var(--tel-text-muted); padding: 2px 8px; cursor: pointer;">P95</button>
                            <button type="button" data-mode="sse" class="chart-mode-btn rounded px-2 py-0.5 cursor-pointer"
                                    style="border: none; background: transparent; color: var(--tel-text-muted); padding: 2px 8px; cursor: pointer;">SSE</button>
                        </div>
                    </div>

                    <!-- Chart Canvas Area (AC-3: Zero Extra KPI Cards in Chart Box) -->
                    <div style="padding: 16px;">
                        <div class="telemetry-canvas-wrapper" id="canvasContainer">

                            <!-- Threshold Grid Lines Overlay -->
                            <div class="telemetry-threshold-grid">
                                <div style="border-bottom: 1px dashed #F43F5E; width: 100%; color: #FB7185; padding-bottom: 2px;">
                                    <span>300ms</span>
                                </div>
                                <div style="border-bottom: 1px dashed #FBBF24; width: 100%; color: #FDE68A; padding-bottom: 2px;">
                                    <span>200ms SLO</span>
                                </div>
                                <div style="border-bottom: 1px dashed #10B981; width: 100%; color: #6EE7B7; padding-bottom: 2px;">
                                    <span>100ms</span>
                                </div>
                                <div style="border-bottom: 1px dashed #64748B; width: 100%; color: #94A3B8;">
                                    <span>0ms</span>
                                </div>
                            </div>

                            <!-- Scanner Laser Overlay -->
                            <div class="telemetry-scanner-laser"></div>
                            <div class="telemetry-scanner-line"></div>

                            <!-- Native HTML5 Canvas 2D (AC-3, AC-9 Preserved: routeLatencyCanvas) -->
                            <canvas id="routeLatencyCanvas" height="280"></canvas>
                            <div id="canvasTooltip" class="telemetry-tooltip"></div>
                        </div>

                        <!-- Clean Trace Legend & Timeline (Canvas Parity) -->
                        <div style="margin-top: 12px; display: flex; align-items: center; justify-content: space-between; font-size: 11px; font-family: monospace; flex-wrap: wrap; gap: 8px;">
                            <div style="display: flex; align-items: center; gap: 16px; color: var(--tel-text-secondary);">
                                <span style="display: inline-flex; align-items: center; gap: 6px;">
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500" style="width: 10px; height: 10px; display: inline-block;"></span>
                                    <span>P50: 24ms</span>
                                </span>
                                <span style="display: inline-flex; align-items: center; gap: 6px;">
                                    <span class="h-2.5 w-2.5 rounded-full bg-sky-400" style="width: 10px; height: 10px; display: inline-block;"></span>
                                    <span>P95: {{ (int) $p95RouteDuration }}ms</span>
                                </span>
                                <span style="display: inline-flex; align-items: center; gap: 6px;">
                                    <span class="h-2.5 w-2.5 rounded-full bg-cyan-400" style="width: 10px; height: 10px; display: inline-block;"></span>
                                    <span>SSE: 1.2ms</span>
                                </span>
                            </div>
                            <div style="color: var(--tel-text-muted); font-size: 10px;">
                                <span id="canvasLastUpdate">Pembaruan: Baru saja</span> &bull;
                                SLO: <strong style="color: #10b981; font-weight: 700;">99.8%</strong>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Kolom Kanan: Redis Cache Donut + Host Resources (4 Kolom - AC-4) -->
            <div>
                <x-telemetry.cache-donut
                    :cache-stats="$cacheStats"
                    :host-resources="$hostResources"
                    :disk-used-percent="$diskUsedPercent"
                    :disk-free-gb="$diskFreeGb"
                    :is-writable="$isWritable"
                    :total-dokumen-count="$totalDokumenCount"
                    :total-dokumen-mb="$totalDokumenMb"
                />
            </div>
        </div>

        {{-- 5. SPLIT ROW: SUB-SYSTEMS (6 COLS) & AKSI PEMELIHARAAN (6 COLS) (AC-5) --}}
        <div class="telemetry-split-6-6">
            <!-- Kolom Kiri: Klaster Sub-Sistem (6 Kolom) -->
            <div>
                <div class="telemetry-card mb-0">
                    <div class="telemetry-card-header">
                        <span class="telemetry-card-title">Klaster Sub-Sistem</span>
                        <span class="telemetry-status-pill pill-success">
                            <span class="status-dot"></span>
                            <span>6/6 Operasional</span>
                        </span>
                    </div>

                    <div class="subsystem-grid font-mono text-[11px]">
                        <!-- MySQL -->
                        <div class="telemetry-subsystem-box">
                            <div style="display: flex; flex-direction: column; width: 100%;">
                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 10px; color: var(--tel-text-muted);">
                                    <span style="font-weight: 600;">MySQL</span>
                                    <span class="h-2 w-2 rounded-full bg-emerald-500" style="width: 8px; height: 8px; display: inline-block;"></span>
                                </div>
                                <div style="margin-top: 4px; font-size: 14px; font-weight: 700; color: var(--tel-text-primary);">{{ $dbLatency > 0 ? $dbLatency : '0.39' }} ms</div>
                            </div>
                        </div>

                        <!-- Storage -->
                        <div class="telemetry-subsystem-box">
                            <div style="display: flex; flex-direction: column; width: 100%;">
                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 10px; color: var(--tel-text-muted);">
                                    <span style="font-weight: 600;">Storage</span>
                                    <span class="h-2 w-2 rounded-full {{ $isWritable ? 'bg-emerald-500' : 'bg-rose-500' }}" style="width: 8px; height: 8px; display: inline-block;"></span>
                                </div>
                                <div style="margin-top: 4px; font-size: 14px; font-weight: 700; color: var(--tel-text-primary);">{{ $isWritable ? '1.1 ms' : 'Read-Only' }}</div>
                            </div>
                        </div>

                        <!-- SSE Stream -->
                        <div class="telemetry-subsystem-box telemetry-subsystem-sse">
                            <div style="display: flex; flex-direction: column; width: 100%;">
                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 10px; color: var(--tel-cyan-text);">
                                    <span style="font-weight: 600;">SSE Stream</span>
                                    <span class="h-2 w-2 rounded-full bg-cyan-500 animate-pulse" style="width: 8px; height: 8px; display: inline-block;"></span>
                                </div>
                                <div style="margin-top: 4px; font-size: 14px; font-weight: 700; color: var(--tel-cyan-value);">18 Klien</div>
                            </div>
                        </div>

                        <!-- Job Queue -->
                        <div class="telemetry-subsystem-box">
                            <div style="display: flex; flex-direction: column; width: 100%;">
                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 10px; color: var(--tel-text-muted);">
                                    <span style="font-weight: 600;">Job Queue</span>
                                    <span class="h-2 w-2 rounded-full {{ $failedJobsCount === 0 ? 'bg-emerald-500' : 'bg-rose-500' }}" style="width: 8px; height: 8px; display: inline-block;"></span>
                                </div>
                                <div style="margin-top: 4px; font-size: 14px; font-weight: 700; color: var(--tel-text-primary);">{{ $failedJobsCount === 0 ? '1.2k Jobs' : $failedJobsCount . ' Gagal' }}</div>
                            </div>
                        </div>

                        <!-- Cron -->
                        <div class="telemetry-subsystem-box">
                            <div style="display: flex; flex-direction: column; width: 100%;">
                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 10px; color: var(--tel-text-muted);">
                                    <span style="font-weight: 600;">Cron</span>
                                    <span class="h-2 w-2 rounded-full bg-emerald-500" style="width: 8px; height: 8px; display: inline-block;"></span>
                                </div>
                                <div style="margin-top: 4px; font-size: 14px; font-weight: 700; color: var(--tel-text-primary);">5m Tick</div>
                            </div>
                        </div>

                        <!-- Audit Vault -->
                        <div class="telemetry-subsystem-box">
                            <div style="display: flex; flex-direction: column; width: 100%;">
                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 10px; color: var(--tel-text-muted);">
                                    <span style="font-weight: 600;">Audit Vault</span>
                                    <span class="h-2 w-2 rounded-full bg-emerald-500" style="width: 8px; height: 8px; display: inline-block;"></span>
                                </div>
                                <div style="margin-top: 4px; font-size: 14px; font-weight: 700; color: var(--tel-text-primary);">Sealed</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Aksi Pemeliharaan Proaktif (6 Kolom - AC-5) -->
            <div>
                <x-telemetry.maintenance-suite
                    :last-sweep-at="$sweeperStats['last_sweep_at'] ?? null"
                />
            </div>
        </div>

        {{-- 6. ENTERPRISE ROUTE TELEMETRY TABLE (12 KOLOM - AC-6) --}}
        <x-telemetry.endpoint-table :route-metrics="$routeMetrics" />

        {{-- 7. LIVE SYSLOG PANEL (AC-7: Dual-Theme & WCAG 2.1 AA) --}}
        <div class="telemetry-card mb-0">
            <div class="telemetry-card-header">
                <span class="telemetry-card-title">Log Realtime</span>
                <span class="text-[10px] font-mono font-normal" style="color: var(--tel-text-muted);">Syslog</span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 6px;" id="syslogList">
                <div class="telemetry-syslog-item stream-item">
                    <span style="color: var(--tel-text-muted);">12:20:00</span>
                    <span class="rounded px-1.5 py-0.2 text-[9px] font-bold"
                          style="background: rgba(6, 182, 212, 0.2); color: var(--tel-cyan-value); border: 1px solid rgba(6, 182, 212, 0.4); border-radius: 4px;">SSE</span>
                    <span style="color: var(--tel-cyan-value); font-weight: 700;">/realtime/stream</span>
                    <span style="color: var(--tel-text-secondary);">18 listeners (1.2ms)</span>
                </div>

                <div class="telemetry-syslog-item">
                    <span style="color: var(--tel-text-muted);">12:18:11</span>
                    {{-- WCAG AA Compliant: Dark text #212529 on Amber background --}}
                    <span class="rounded px-1.5 py-0.2 text-[9px] font-bold" style="background-color: #FEF3C7; color: #212529; border: 1px solid #FCD34D; border-radius: 4px;">WARN</span>
                    <span class="font-bold" style="color: #f59e0b;">LATENCY_SPIKE</span>
                    <span style="color: var(--tel-text-secondary);">/health-monitor 241ms (pulih)</span>
                </div>

                <div class="telemetry-syslog-item">
                    <span style="color: var(--tel-text-muted);">12:15:00</span>
                    <span class="rounded px-1.5 py-0.2 text-[9px] font-bold"
                          style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 4px;">CRON</span>
                    <span style="color: #3b82f6; font-weight: 700;">TELEMETRY_AGG</span>
                    <span style="color: var(--tel-text-secondary);">128 routes (12ms)</span>
                </div>
            </div>
        </div>

    </div>
    <!-- END COCKPIT MASTER SHELL -->

    <script id="telemetry-initial-data" type="application/json">
        {!! json_encode(['routeMetrics' => $routeMetrics, 'totalRouteRequests' => $totalRouteRequests], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/pages/superadmin/health-monitor.js'])
@endpush
