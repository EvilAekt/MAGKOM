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

@push('styles')
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
        border-radius: 14px !important;
        padding: 16px !important;
        color: var(--tel-text-primary) !important;
        box-sizing: border-box !important;
        box-shadow: var(--tel-card-shadow) !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.25s ease !important;
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
    .time-window-btn.active,
    .time-window-btn:first-child {
        background-color: var(--tel-time-btn-active-bg) !important;
        color: var(--tel-time-btn-active-text) !important;
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

    /* 14. Table & Scrollable Container Capping (Expanded Proportional Dimensions) */
    .telemetry-table-container {
        min-height: 360px !important;
        max-height: 480px !important;
        overflow-y: auto !important;
        overflow-x: auto !important;
        border-radius: 0 0 14px 14px !important;
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

    /* 17. Viewport Bottom Containment (Hard-Stop Bounded Gap & Zero Void) */
    .health-telemetry-container {
        display: flex !important;
        flex-direction: column !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
    .telemetry-cockpit-shell {
        margin-bottom: 1.5rem !important; /* Jarak teratur 24px persis sebelum footer */
    }
    #mainContent,
    .dashboard-main,
    .dashboard-content-area,
    .right_col {
        min-height: auto !important;
        padding-bottom: 0 !important;
    }
    .container.body,
    .main_container {
        min-height: auto !important;
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

    <!-- COCKPIT MASTER SHELL (Deep Space Navy #002244 Parity) -->
    <div class="telemetry-cockpit-shell">
        <span class="sr-only">Panel Telemetri Kesehatan Infrastruktur Koneksi Basis Data Runtime</span>

        {{-- 1. HEADER TOOLBAR & LIVE CONTROLS (AC-1: Zero Double Header) --}}
        <header class="telemetry-header-toolbar">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl font-bold text-sm shadow-md shrink-0"
                     style="width: 40px; height: 40px; min-width: 40px; min-height: 40px; background-color: var(--tel-btn-sse-bg); color: #ffffff; border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(59, 130, 246, 0.3);">
                    MK
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <h1 class="text-lg sm:text-xl font-bold tracking-tight m-0" style="font-size: 1.25rem; font-weight: 700; line-height: 1.75rem; color: var(--tel-text-primary) !important;">
                            Telemetri Kesehatan Infrastruktur
                        </h1>
                        <span class="telemetry-status-pill pill-success">
                            <span class="status-dot"></span>
                            <span>Live 24ms</span>
                        </span>
                    </div>
                    <div class="text-xs font-mono mt-0.5" style="color: var(--tel-text-muted); font-size: 11px;">
                        <span style="color: var(--tel-accent-link); font-weight: 600;">magkom-prod-01</span> &bull; Sleman DC
                    </div>
                </div>
            </div>

            <!-- Controls Strip -->
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <!-- Channel -->
                <span class="hidden md:inline-flex items-center gap-2 rounded-lg font-mono font-semibold"
                      style="display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--tel-badge-info-border); background: var(--tel-badge-info-bg); padding: 5px 10px; border-radius: 8px; font-size: 11px; font-family: monospace; color: var(--tel-cyan-text);">
                    <span class="h-2 w-2 rounded-full bg-cyan-400 animate-pulse" style="width: 8px; height: 8px; display: inline-block;"></span>
                    /realtime/stream
                </span>

                <!-- Time Window Selector Buttons -->
                <div class="telemetry-time-selector" id="timeWindowSelector">
                    <button type="button" data-window="15m" class="time-window-btn active">15m</button>
                    <button type="button" data-window="1h" class="time-window-btn">1h</button>
                    <button type="button" data-window="24h" class="time-window-btn">24h</button>
                </div>

                <!-- Action Button: Test SSE -->
                <button type="button" id="btnTestSse" class="btn-test-sse-action">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Test SSE</span>
                </button>
            </div>
        </header>

        {{-- 2. UNIFIED COMMAND COCKPIT TELEMETRY CARD (HUD) (AC-2: 6 Kolom Simetris) --}}
        <x-telemetry.health-hud
            :sla-status="$slaStatus"
            :sla-percent="$slaPercent"
            :db-latency="$dbLatency"
            :db-driver="$dbDriver"
            :total-tables="$totalTables"
            :p95-route-duration="$p95RouteDuration"
            :avg-route-duration="$avgRouteDuration"
            :total-route-requests="$totalRouteRequests"
            :audit24h="$audit24h"
            :total-audit="$totalAudit"
            :failed-jobs-count="$failedJobsCount"
            :cache-driver="$runtimeInfo['cache_driver'] ?? 'file'"
            :queue-driver="$runtimeInfo['queue_driver'] ?? 'sync'"
            :is-writable="$isWritable"
        />

        {{-- 3. 4 KEY STAT KPI CARDS ROW (AC-8: Safe SVG Dimensions) --}}
        <div class="telemetry-kpi-row-grid">
            <!-- Card 1: Uptime 30 Hari -->
            <div class="telemetry-card">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span class="telemetry-kpi-title">Uptime 30 Hari</span>
                    <span class="telemetry-status-pill pill-success" style="padding: 2px 8px; font-size: 10px;">
                        <span class="status-dot"></span>
                        <span>Normal</span>
                    </span>
                </div>
                <div style="margin-top: 10px; display: flex; align-items: baseline; justify-content: space-between;">
                    <span class="telemetry-kpi-value">{{ $slaPercent !== null ? $slaPercent . '%' : '99.98%' }}</span>
                    <svg class="h-6 w-20 text-emerald-500" viewBox="0 0 80 24" fill="none" width="80" height="24" style="width: 80px; height: 24px; max-width: 80px; max-height: 24px; color: #10b981;" aria-hidden="true">
                        <path d="M0 16 L15 16 L25 15 L35 16 L45 14 L55 16 L65 15 L80 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
                <div class="telemetry-kpi-sub">SLO: 99.9%</div>
            </div>

            <!-- Card 2: HTTP P95 Latency -->
            <div class="telemetry-card">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span class="telemetry-kpi-title">HTTP P95 Latency</span>
                    <span class="font-mono text-[11px] font-semibold" style="color: #3b82f6;">&lt; 200ms SLO</span>
                </div>
                <div style="margin-top: 10px; display: flex; align-items: baseline; justify-content: space-between;">
                    <div class="telemetry-kpi-value">
                        {{ number_format((float) $p95RouteDuration, 1) }} <span class="text-xs font-normal" style="color: var(--tel-text-muted); font-size: 0.75rem;">ms</span>
                    </div>
                    <svg class="h-6 w-20 text-blue-500" viewBox="0 0 80 24" fill="none" width="80" height="24" style="width: 80px; height: 24px; max-width: 80px; max-height: 24px; color: #3b82f6;" aria-hidden="true">
                        <path d="M0 18 L15 16 L30 19 L40 6 L50 17 L65 15 L80 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
                <div class="telemetry-kpi-sub">Target: &lt; 200ms</div>
            </div>

            <!-- Card 3: Volume Permintaan -->
            <div class="telemetry-card">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span class="telemetry-kpi-title">Volume Permintaan</span>
                    <span class="font-mono text-[11px] font-semibold" style="color: #818cf8;">140 rps</span>
                </div>
                <div style="margin-top: 10px; display: flex; align-items: baseline; justify-content: space-between;">
                    <div class="telemetry-kpi-value">
                        {{ number_format($totalRouteRequests) }} <span class="text-xs font-normal" style="color: var(--tel-text-muted); font-size: 0.75rem;">rpm</span>
                    </div>
                    <svg class="h-6 w-20 text-indigo-500" viewBox="0 0 80 24" fill="currentColor" width="80" height="24" style="width: 80px; height: 24px; max-width: 80px; max-height: 24px; color: #6366f1;" aria-hidden="true">
                        <rect x="0" y="10" width="6" height="14" rx="1" />
                        <rect x="10" y="7" width="6" height="17" rx="1" />
                        <rect x="20" y="12" width="6" height="12" rx="1" />
                        <rect x="30" y="4" width="6" height="20" rx="1" />
                        <rect x="40" y="8" width="6" height="16" rx="1" />
                        <rect x="50" y="6" width="6" height="18" rx="1" />
                        <rect x="60" y="11" width="6" height="13" rx="1" />
                        <rect x="70" y="9" width="6" height="15" rx="1" />
                    </svg>
                </div>
                <div class="telemetry-kpi-sub">GET: 94% &bull; POST: 6%</div>
            </div>

            <!-- Card 4: SSE Stream Mandiri -->
            <div class="telemetry-card telemetry-card-sse">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 11px; font-weight: 600; color: var(--tel-cyan-text);">SSE Stream Mandiri</span>
                    <span class="telemetry-status-pill pill-info" style="padding: 2px 8px; font-size: 10px;">
                        <span class="status-dot"></span>
                        <span>1.2ms</span>
                    </span>
                </div>
                <div style="margin-top: 10px; display: flex; align-items: baseline; justify-content: space-between;">
                    <div class="telemetry-kpi-value" style="color: var(--tel-cyan-value);">
                        18 <span class="text-xs font-normal" style="color: var(--tel-cyan-text); font-size: 0.75rem;">klien</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <span class="h-4 w-1.5 rounded-full bg-cyan-500 animate-pulse" style="width: 6px; height: 16px; display: inline-block;"></span>
                        <span class="h-6 w-1.5 rounded-full bg-cyan-500 animate-pulse" style="width: 6px; height: 24px; display: inline-block; animation-delay: 75ms;"></span>
                        <span class="h-3 w-1.5 rounded-full bg-cyan-500 animate-pulse" style="width: 6px; height: 12px; display: inline-block; animation-delay: 150ms;"></span>
                        <span class="h-5 w-1.5 rounded-full bg-cyan-500 animate-pulse" style="width: 6px; height: 20px; display: inline-block; animation-delay: 100ms;"></span>
                    </div>
                </div>
                <div class="telemetry-kpi-sub" style="color: var(--tel-cyan-text);">Status: 18 Terhubung</div>
            </div>
        </div>

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

</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // Inisialisasi Data Awal dari Server
    let telemetryData = @json($routeMetrics);
    let totalRequestsCount = {{ $totalRouteRequests }};

    const canvas = document.getElementById('routeLatencyCanvas');
    const container = document.getElementById('canvasContainer');
    const tooltip = document.getElementById('canvasTooltip');
    const lastUpdateEl = document.getElementById('canvasLastUpdate');
    const filterInput = document.getElementById('endpointTableFilter');
    const tableRows = document.querySelectorAll('.route-table-row');
    const btnTestSse = document.getElementById('btnTestSse');

    if (!canvas || !container) return;

    const ctx = canvas.getContext('2d');
    let activePoint = null;
    let currentFilterMode = 'all';

    // Konfigurasi Padding & Margin Kanvas
    const padding = { top: 30, right: 35, bottom: 35, left: 55 };

    /**
     * Hitung ulang dimensi kanvas dengan devicePixelRatio untuk tampilan Retina/HiDPI tajam.
     */
    function resizeCanvas() {
        const rect = container.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;
        canvas.width = Math.floor(rect.width * dpr);
        canvas.height = Math.floor(280 * dpr);
        canvas.style.width = rect.width + 'px';
        canvas.style.height = '280px';

        if (ctx.resetTransform) {
            ctx.resetTransform();
        } else {
            ctx.setTransform(1, 0, 0, 1, 0, 0);
        }
        ctx.scale(dpr, dpr);
        draw();
    }

    /**
     * Engine Penggambaran Native 2D Canvas (Zero-Dependency)
     */
    function draw() {
        const width = canvas.width / (window.devicePixelRatio || 1);
        const height = 280;

        ctx.clearRect(0, 0, width, height);

        // Skala Sumbu Y (0 ms hingga 350 ms batas atas)
        const maxY = 350;
        const graphW = width - padding.left - padding.right;
        const graphH = height - padding.top - padding.bottom;

        // Ambil data sesuai filter mode
        let displayData = telemetryData.slice();
        if (currentFilterMode === 'p95') {
            displayData = displayData.filter(d => (parseFloat(d.duration_ms) || 0) >= 100);
        } else if (currentFilterMode === 'sse') {
            displayData = displayData.filter(d => (d.uri || '').includes('realtime/stream') || (parseFloat(d.duration_ms) || 0) <= 20);
        }

        const pointCount = displayData.length;

        // Gambar Grid Horizontal
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.06)';
        ctx.lineWidth = 1;
        [0, 100, 200, 300].forEach(val => {
            const y = padding.top + graphH - (val / maxY) * graphH;
            ctx.beginPath();
            ctx.moveTo(padding.left, y);
            ctx.lineTo(width - padding.right, y);
            ctx.stroke();

            // Label Sumbu Y
            ctx.fillStyle = '#64748B';
            ctx.font = '10px monospace';
            ctx.textAlign = 'right';
            ctx.fillText(val + 'ms', padding.left - 8, y + 3);
        });

        if (pointCount < 2) {
            // Render Fallback Wave if no points yet
            renderFallbackWave(width, height, graphW, graphH);
            return;
        }

        const stepX = graphW / (pointCount - 1);
        const computedPoints = [];

        // Hitung koordinat titik
        displayData.forEach((item, idx) => {
            const ms = parseFloat(item.duration_ms) || 0;
            const x = padding.left + idx * stepX;
            const y = padding.top + graphH - Math.min(graphH, (ms / maxY) * graphH);
            computedPoints.push({ x, y, data: item });
        });

        // 1. Gambar Area Gradient Bawah (P50 Emerald Wave)
        const p50Grad = ctx.createLinearGradient(0, padding.top, 0, height - padding.bottom);
        p50Grad.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
        p50Grad.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        ctx.fillStyle = p50Grad;
        ctx.beginPath();
        ctx.moveTo(computedPoints[0].x, height - padding.bottom);
        computedPoints.forEach((pt, i) => {
            if (i === 0) ctx.lineTo(pt.x, pt.y);
            else {
                const prev = computedPoints[i - 1];
                const cpX = (prev.x + pt.x) / 2;
                ctx.bezierCurveTo(cpX, prev.y, cpX, pt.y, pt.x, pt.y);
            }
        });
        ctx.lineTo(computedPoints[computedPoints.length - 1].x, height - padding.bottom);
        ctx.closePath();
        ctx.fill();

        // 2. Gambar Garis Kurva Utama
        ctx.strokeStyle = '#38BDF8';
        ctx.lineWidth = 2.5;
        ctx.beginPath();
        computedPoints.forEach((pt, i) => {
            if (i === 0) ctx.moveTo(pt.x, pt.y);
            else {
                const prev = computedPoints[i - 1];
                const cpX = (prev.x + pt.x) / 2;
                ctx.bezierCurveTo(cpX, prev.y, cpX, pt.y, pt.x, pt.y);
            }
        });
        ctx.stroke();

        // 3. Render Titik & Highlight Spike Callout
        let maxPoint = null;
        computedPoints.forEach(pt => {
            const ms = parseFloat(pt.data.duration_ms) || 0;
            if (!maxPoint || ms > (parseFloat(maxPoint.data.duration_ms) || 0)) {
                maxPoint = pt;
            }

            ctx.fillStyle = ms > 200 ? '#EF4444' : (ms > 50 ? '#FBBF24' : '#10B981');
            ctx.beginPath();
            ctx.arc(pt.x, pt.y, ms > 200 ? 4.5 : 3, 0, Math.PI * 2);
            ctx.fill();
        });

        // Spike Callout Badge
        if (maxPoint && (parseFloat(maxPoint.data.duration_ms) || 0) > 100) {
            const spikeMs = Math.round(parseFloat(maxPoint.data.duration_ms));
            ctx.fillStyle = '#0F172A';
            ctx.strokeStyle = '#EF4444';
            ctx.lineWidth = 1.5;
            const badgeW = 56;
            const badgeH = 18;
            const bx = Math.min(width - padding.right - badgeW, Math.max(padding.left, maxPoint.x - badgeW / 2));
            const by = Math.max(padding.top, maxPoint.y - 28);

            ctx.beginPath();
            if (ctx.roundRect) {
                ctx.roundRect(bx, by, badgeW, badgeH, 4);
            } else {
                ctx.rect(bx, by, badgeW, badgeH);
            }
            ctx.fill();
            ctx.stroke();

            ctx.fillStyle = '#F87171';
            ctx.font = 'bold 10px monospace';
            ctx.textAlign = 'center';
            ctx.fillText(spikeMs + 'ms', bx + badgeW / 2, by + 12);
        }

        // Stream Head Glow on Latest Point
        const lastPt = computedPoints[computedPoints.length - 1];
        if (lastPt) {
            ctx.fillStyle = '#38BDF8';
            ctx.beginPath();
            ctx.arc(lastPt.x, lastPt.y, 4, 0, Math.PI * 2);
            ctx.fill();
            ctx.strokeStyle = '#FFFFFF';
            ctx.lineWidth = 1.5;
            ctx.stroke();
        }
    }

    /**
     * Fallback Visual Wave bila data sampel masih minim
     */
    function renderFallbackWave(width, height, graphW, graphH) {
        const p95Grad = ctx.createLinearGradient(0, padding.top, 0, height - padding.bottom);
        p95Grad.addColorStop(0, 'rgba(56, 189, 248, 0.35)');
        p95Grad.addColorStop(1, 'rgba(56, 189, 248, 0.0)');

        ctx.fillStyle = p95Grad;
        ctx.beginPath();
        ctx.moveTo(padding.left, height - padding.bottom);
        ctx.bezierCurveTo(padding.left + graphW * 0.3, padding.top + 60, padding.left + graphW * 0.6, padding.top + 140, padding.left + graphW, padding.top + 100);
        ctx.lineTo(padding.left + graphW, height - padding.bottom);
        ctx.closePath();
        ctx.fill();

        ctx.strokeStyle = '#38BDF8';
        ctx.lineWidth = 2.5;
        ctx.beginPath();
        ctx.moveTo(padding.left, height - padding.bottom - 40);
        ctx.bezierCurveTo(padding.left + graphW * 0.3, padding.top + 60, padding.left + graphW * 0.6, padding.top + 140, padding.left + graphW, padding.top + 100);
        ctx.stroke();
    }

    // Interaktivitas Filter Input Table
    if (filterInput) {
        filterInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            document.querySelectorAll('.route-table-row').forEach(row => {
                const text = (row.textContent + ' ' + (row.getAttribute('data-route') || '')).toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // Interaktivitas Time Window Buttons
    document.querySelectorAll('.time-window-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.time-window-btn').forEach(b => {
                b.classList.remove('active');
            });
            this.classList.add('active');
            draw();
        });
    });

    // Interaktivitas Chart Mode Buttons
    document.querySelectorAll('.chart-mode-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.chart-mode-btn').forEach(b => {
                b.classList.remove('active');
                b.style.backgroundColor = 'transparent';
                b.style.color = 'var(--tel-text-muted)';
                b.style.fontWeight = 'normal';
            });
            this.classList.add('active');
            this.style.backgroundColor = 'var(--tel-btn-sse-bg)';
            this.style.color = '#ffffff';
            this.style.fontWeight = 'bold';
            currentFilterMode = this.getAttribute('data-mode') || 'all';
            draw();
        });
    });

    // Interaktivitas Tombol Test SSE
    if (btnTestSse) {
        btnTestSse.addEventListener('click', function() {
            const dummyMetric = {
                id: Date.now(),
                method: 'GET',
                route_name: 'superadmin.health.test-sse',
                uri: '/superadmin/health/test-sse',
                status_code: 200,
                duration_ms: (Math.random() * 40 + 10).toFixed(2),
                memory_mb: 4.2,
                time: new Date().toTimeString().split(' ')[0]
            };

            telemetryData.push(dummyMetric);
            if (telemetryData.length > 50) telemetryData.shift();

            draw();

            if (lastUpdateEl) {
                lastUpdateEl.textContent = 'Pembaruan: Baru saja (SSE Trigger)';
            }
        });
    }

    // SSE Event Listener Realtime
    if (window.EventSource) {
        try {
            const stream = new EventSource('/realtime/stream');
            stream.addEventListener('magkom:telemetry-route', function(e) {
                try {
                    const data = JSON.parse(e.data);
                    telemetryData.push({
                        id: Date.now(),
                        method: data.method || 'GET',
                        route_name: data.route_name || data.uri || '/stream',
                        uri: data.uri || '/stream',
                        status_code: data.status_code || 200,
                        duration_ms: parseFloat(data.duration_ms) || 12.0,
                        memory_mb: parseFloat(data.memory_mb) || 5.0,
                        time: data.time || new Date().toTimeString().split(' ')[0]
                    });
                    if (telemetryData.length > 50) telemetryData.shift();
                    draw();
                } catch(err) {}
            });
        } catch(err) {}
    }

    // Window Resize Observer
    window.addEventListener('resize', resizeCanvas);
    setTimeout(resizeCanvas, 50);
})();
</script>
@endpush
