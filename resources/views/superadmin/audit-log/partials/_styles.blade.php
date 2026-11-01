<style>
/* =========================================================================
   DS-MAGKOM AUDIT LOG REDESIGN (Canvas 5a4d7a8b & 68749aa8 Parity)
   Scoped CSS Stylesheet (Dual Theme & Zero Clutter)
   ========================================================================= */

:root {
    --sa-audit-bg: #F8FAFC;
    --sa-audit-surface: #FFFFFF;
    --sa-audit-border: #E2E8F0;
    --sa-audit-border-subtle: #F1F5F9;
    --sa-audit-text-main: #0F172A;
    --sa-audit-text-muted: #64748B;
    --sa-audit-text-sub: #94A3B8;
    --sa-audit-blue: #0751FF;
    --sa-audit-blue-hover: #0641CC;
    --sa-audit-blue-tint: #E6EEFF;
}

[data-theme="dark"] {
    --sa-audit-bg: #030712;
    --sa-audit-surface: #0F172A;
    --sa-audit-border: #1E293B;
    --sa-audit-border-subtle: #1E293B80;
    --sa-audit-text-main: #F8FAFC;
    --sa-audit-text-muted: #94A3B8;
    --sa-audit-text-sub: #64748B;
    --sa-audit-blue: #3B82F6;
    --sa-audit-blue-hover: #2563EB;
    --sa-audit-blue-tint: #1E3A8A40;
}

/* SVG Protection - Zero Stretched SVGs in Gentelella Layout */
.audit-workspace-root svg {
    max-width: 100%;
    vertical-align: middle;
    flex-shrink: 0;
    display: inline-block;
}

/* Base Container */
.audit-workspace-root {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    font-family: 'Open Sans', Arial, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--sa-audit-text-main);
    padding-bottom: 2.5rem;
}

/* 1. EXECUTIVE COMMAND HEADER CARD (Dual Theme & Vibrant Contrast) */
.audit-header-card {
    background: #FFFFFF;
    border: 1px solid #CBD5E1;
    border-top: 3.5px solid var(--magkom-primary-blue, #01347C);
    border-radius: 14px;
    padding: 1.125rem 1.35rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    box-shadow: 0 4px 16px -2px rgba(1, 52, 124, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: all 0.2s ease;
}

[data-theme="dark"] .audit-header-card {
    background: #0F172A;
    border: 1px solid rgba(59, 130, 246, 0.25);
    border-top: 3.5px solid #3B82F6;
    box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.5), 0 2px 6px -1px rgba(59, 130, 246, 0.1);
}

[data-theme="dark"] .audit-panel-card,
[data-theme="dark"] .audit-stream-card,
[data-theme="dark"] .audit-table-card,
[data-theme="dark"] .audit-diff-card {
    border-top: 3.5px solid #3B82F6;
}

@media (min-width: 1024px) {
    .audit-header-card {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}

.audit-header-title-block {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.audit-breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--sa-audit-text-muted);
}

.audit-breadcrumb-link {
    color: #64748B;
    text-decoration: none;
    transition: color 0.15s ease;
}

[data-theme="dark"] .audit-breadcrumb-link {
    color: #94A3B8;
}

.audit-breadcrumb-link:hover {
    color: var(--sa-audit-blue);
    text-decoration: none;
}

.audit-breadcrumb-sep {
    color: #CBD5E1;
}

[data-theme="dark"] .audit-breadcrumb-sep {
    color: #334155;
}

.audit-breadcrumb-current {
    color: var(--sa-audit-text-main);
    font-weight: 600;
}

.audit-title-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.75rem;
}

.audit-page-title {
    font-size: 1.375rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #003566;
    margin: 0;
    line-height: 1.25;
}

[data-theme="dark"] .audit-page-title {
    color: #F8FAFC;
}

.audit-integrity-badges {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.badge-integrity-valid {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.6875rem;
    font-weight: 700;
    background: #ECFDF5;
    color: #047857;
    border: 1px solid #A7F3D0;
    box-shadow: 0 1px 2px rgba(16, 185, 129, 0.1);
}

[data-theme="dark"] .badge-integrity-valid {
    background: rgba(6, 78, 59, 0.45);
    color: #34D399;
    border-color: rgba(5, 150, 105, 0.6);
    box-shadow: 0 0 8px rgba(16, 185, 129, 0.2);
}

@keyframes emeraldDotPulse {
    0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
    50% { opacity: 0.75; transform: scale(0.9); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0); }
}

.badge-dot-emerald {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background-color: #10B981;
    display: inline-block;
    animation: emeraldDotPulse 2s infinite ease-in-out;
}

.badge-retention {
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.6875rem;
    font-weight: 600;
    background: rgba(1, 52, 124, 0.07);
    color: #01347C;
    border: 1px solid rgba(1, 52, 124, 0.18);
    font-variant-numeric: tabular-nums;
}

[data-theme="dark"] .badge-retention {
    background: rgba(30, 58, 138, 0.35);
    color: #93C5FD;
    border-color: rgba(59, 130, 246, 0.35);
}

/* Unified Action Toolbar 34px */
.audit-toolbar-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
}

.audit-search-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.audit-input-compact {
    width: 220px;
    height: 34px;
    padding-left: 2rem;
    padding-right: 0.75rem;
    font-size: 0.75rem;
    border-radius: 8px;
    background: #FFFFFF;
    border: 1px solid #CBD5E1;
    color: var(--sa-audit-text-main);
    outline: none;
    transition: all 0.15s ease;
}

[data-theme="dark"] .audit-input-compact {
    background: #111827;
    border-color: #334155;
}

.audit-input-compact:focus {
    border-color: var(--sa-audit-blue);
    box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.18);
    background: #FFFFFF;
}

[data-theme="dark"] .audit-input-compact:focus {
    background: #111827;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
}

.audit-search-icon {
    position: absolute;
    left: 0.625rem;
    color: #94A3B8;
    pointer-events: none;
}

.audit-select-compact {
    height: 34px;
    padding: 0 0.625rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 8px;
    background: #FFFFFF;
    border: 1px solid #CBD5E1;
    color: var(--sa-audit-text-main);
    outline: none;
    cursor: pointer;
    transition: all 0.15s ease;
}

[data-theme="dark"] .audit-select-compact {
    background: #111827;
    border-color: #334155;
}

.audit-select-compact:focus,
.audit-select-compact:hover {
    border-color: var(--sa-audit-blue);
}

.audit-btn-icon {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: #FFFFFF;
    border: 1px solid #CBD5E1;
    color: #475569;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
}

[data-theme="dark"] .audit-btn-icon {
    background: #111827;
    border-color: #334155;
    color: #94A3B8;
}

.audit-btn-icon:hover {
    background: #F1F5F9;
    color: var(--magkom-primary-blue, #01347C);
    border-color: #94A3B8;
    text-decoration: none;
}

[data-theme="dark"] .audit-btn-icon:hover {
    background: #1F2937;
    color: #60A5FA;
    border-color: #4B5563;
}

/* Split Button Trigger (Pola A - Flat Solid Blue, Zero Gradient) */
.audit-export-split-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(7, 81, 255, 0.2);
}

.audit-export-btn-primary {
    height: 34px;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0 0.875rem;
    font-size: 0.75rem;
    font-weight: 700;
    background: var(--sa-audit-blue);
    color: #FFFFFF;
    border: 1px solid var(--sa-audit-blue);
    border-right: none;
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
    cursor: pointer;
    transition: background-color 0.15s ease;
}

.audit-export-btn-primary:hover {
    background: var(--sa-audit-blue-hover);
    border-color: var(--sa-audit-blue-hover);
}

.audit-export-btn-caret {
    height: 34px;
    width: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--sa-audit-blue-hover);
    color: #FFFFFF;
    border: 1px solid var(--sa-audit-blue-hover);
    border-left: 1px solid rgba(255, 255, 255, 0.25);
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
    cursor: pointer;
    transition: background-color 0.15s ease;
}

.audit-export-btn-caret:hover {
    background: #053399;
    border-color: #053399;
}

/* 2. GENTELELLA V4 4-UP STAT KPI METRICS STRIP */
.audit-kpi-row {
    display: grid;
    grid-template-columns: repeat(1, minmax(0, 1fr));
    gap: 1rem;
    width: 100%;
}

@media (min-width: 640px) {
    .audit-kpi-row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (min-width: 1024px) {
    .audit-kpi-row {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

.audit-stat-card {
    background: var(--sa-audit-surface);
    border: 1px solid var(--sa-audit-border);
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    transition: box-shadow 0.15s ease;
}

.audit-stat-card:hover {
    box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.08);
}

.audit-stat-card .stat {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
}

.audit-stat-card .stat-content {
    flex: 1;
    min-width: 0;
}

.audit-stat-card .stat-label {
    color: var(--sa-audit-text-muted);
    font-size: 11.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.35px;
    line-height: 1.3;
    margin-bottom: 2px;
}

.audit-stat-card .stat-value-row {
    display: flex;
    align-items: baseline;
    gap: 8px;
}

.audit-stat-card .stat-value {
    font-size: 22px;
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -0.5px;
    color: var(--sa-audit-text-main);
    font-variant-numeric: tabular-nums;
}

.audit-stat-card .stat-change {
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 2px;
}

.audit-stat-card .stat-change.up { color: #16A34A; }
.audit-stat-card .stat-change.down { color: #DC2626; }

.audit-stat-card .stat-subtext {
    font-size: 11px;
    color: var(--sa-audit-text-sub);
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

[data-theme="dark"] .audit-stat-card {
    background: #1A2332;
    border-color: rgba(255, 255, 255, 0.08);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
}

[data-theme="dark"] .audit-stat-card .stat-value { color: #E2E8F0; }
[data-theme="dark"] .audit-stat-card .stat-label { color: #94A3B8; }
[data-theme="dark"] .audit-stat-card .stat-subtext { color: #64748B; }
[data-theme="dark"] .audit-stat-card .stat-change.up { color: #4ADE80; }
[data-theme="dark"] .audit-stat-card .stat-change.down { color: #F87171; }

.badge-dot-rose {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: #EF4444;
    display: inline-block;
}

/* Gentelella v4 stat icon badges */
.audit-stat-card .stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.audit-stat-card .stat-icon svg {
    width: 20px;
    height: 20px;
}

.audit-stat-card .stat-icon.green {
    background: #E8F7EE;
    color: #16A34A;
}

.audit-stat-card .stat-icon.blue {
    background: #E6EEFF;
    color: #2563EB;
}

.audit-stat-card .stat-icon.purple {
    background: #F3E8FF;
    color: #9333EA;
}

.audit-stat-card .stat-icon.yellow {
    background: #FEF3C7;
    color: #D97706;
}

.audit-stat-card .stat-icon.red {
    background: #FEE2E2;
    color: #DC2626;
}

[data-theme="dark"] .audit-stat-card .stat-icon.green {
    background: rgba(22, 163, 74, 0.16);
    color: #4ADE80;
}

[data-theme="dark"] .audit-stat-card .stat-icon.blue {
    background: rgba(37, 99, 235, 0.16);
    color: #60A5FA;
}

[data-theme="dark"] .audit-stat-card .stat-icon.purple {
    background: rgba(147, 51, 234, 0.16);
    color: #C084FC;
}

[data-theme="dark"] .audit-stat-card .stat-icon.yellow {
    background: rgba(217, 119, 6, 0.16);
    color: #FBBF24;
}

[data-theme="dark"] .audit-stat-card .stat-icon.red {
    background: rgba(220, 38, 38, 0.16);
    color: #F87171;
}

/* 3. 2-COLUMN GRID WRAPPER */
.audit-main-layout {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 1.5rem;
    align-items: start;
}

@media (min-width: 1024px) {
    .audit-main-layout {
        grid-template-columns: repeat(12, 1fr);
    }
    .audit-sidebar-col {
        grid-column: span 3;
        position: sticky;
        top: 1.5rem;
    }
    .audit-stream-col {
        grid-column: span 9;
    }
}

.audit-sidebar-col {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.audit-stream-col {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    min-width: 0;
}

/* 4. LEFT SIDEBAR: CHANNELS & COMPLIANCE */
.audit-panel-card {
    background: var(--sa-audit-surface);
    border: 1px solid var(--sa-audit-border);
    border-top: 3.5px solid var(--magkom-primary-blue, #01347C);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}

.audit-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 0.625rem;
    border-bottom: 1px solid var(--sa-audit-border-subtle);
}

.audit-panel-header-left {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.audit-panel-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--sa-audit-text-main);
}

.audit-panel-count-badge {
    font-size: 0.625rem;
    font-weight: 700;
    padding: 0.1rem 0.4rem;
    border-radius: 4px;
    background: #F1F5F9;
    color: #475569;
}

[data-theme="dark"] .audit-panel-count-badge {
    background: #1E293B;
    color: #94A3B8;
}

.audit-panel-header-sub {
    font-size: 0.6875rem;
    font-family: monospace;
    color: var(--sa-audit-text-sub);
}

.audit-channel-search-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.audit-channel-filter-input {
    width: 100%;
    height: 32px;
    padding-left: 1.75rem;
    padding-right: 0.625rem;
    font-size: 0.75rem;
    border-radius: 6px;
    background: #F8FAFC;
    border: 1px solid var(--sa-audit-border);
    color: var(--sa-audit-text-main);
    outline: none;
}

[data-theme="dark"] .audit-channel-filter-input {
    background: #111827;
}

.audit-channel-filter-input:focus {
    border-color: var(--sa-audit-blue);
}

.audit-channel-search-icon {
    position: absolute;
    left: 0.5rem;
    color: #94A3B8;
    pointer-events: none;
}

.audit-channel-nav-list {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    max-height: 480px;
    overflow-y: auto;
    padding-right: 0.25rem;
}

.audit-channel-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.45rem 0.625rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--sa-audit-text-muted);
    text-decoration: none;
    transition: all 0.15s ease;
}

.audit-channel-link:hover {
    background: #F1F5F9;
    color: var(--sa-audit-text-main);
    text-decoration: none;
}

[data-theme="dark"] .audit-channel-link:hover {
    background: #1E293B;
}

.audit-channel-link.active {
    background: var(--sa-audit-blue-tint);
    color: var(--sa-audit-blue);
    font-weight: 700;
}

.audit-channel-label-wrap {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 0;
}

.audit-channel-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}

.audit-channel-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.audit-channel-count {
    font-size: 0.6875rem;
    font-weight: 600;
    color: var(--sa-audit-text-sub);
    font-variant-numeric: tabular-nums;
}

.audit-count-active {
    font-weight: 800;
    color: var(--sa-audit-blue);
}

/* Compliance Panel */
.badge-worm-policy {
    font-size: 0.625rem;
    font-weight: 800;
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
    background: #EFF6FF;
    color: #1D4ED8;
}

[data-theme="dark"] .badge-worm-policy {
    background: rgba(30, 58, 138, 0.4);
    color: #93C5FD;
}

.audit-compliance-specs {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    font-size: 0.75rem;
}

.audit-compliance-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.35rem 0;
    border-bottom: 1px solid var(--sa-audit-border-subtle);
}

.audit-compliance-row.border-b-0 {
    border-bottom: none;
}

.audit-compliance-label {
    color: var(--sa-audit-text-muted);
}

.audit-spec-emerald {
    font-weight: 700;
    font-size: 0.6875rem;
    color: #059669;
}
[data-theme="dark"] .audit-spec-emerald { color: #34D399; }

.audit-spec-slate {
    font-weight: 700;
    font-size: 0.6875rem;
    color: var(--sa-audit-text-main);
}

.audit-spec-purple {
    font-weight: 700;
    font-size: 0.6875rem;
    color: #7C3AED;
}
[data-theme="dark"] .audit-spec-purple { color: #A78BFA; }

/* 5. STREAM HEADER & AUDIT FILTER TOOLBAR */
.audit-stream-card {
    background: var(--sa-audit-surface);
    border: 1px solid var(--sa-audit-border);
    border-top: 3.5px solid var(--magkom-primary-blue, #01347C);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.875rem;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}

.audit-stream-header {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--sa-audit-border-subtle);
}

@media (min-width: 640px) {
    .audit-stream-header {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}

.audit-stream-title-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.audit-stream-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.audit-stream-title-row {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.audit-stream-name {
    font-size: 0.9375rem;
    font-weight: 800;
    color: var(--sa-audit-text-main);
}

.audit-stream-count {
    font-size: 0.75rem;
    font-family: monospace;
    color: var(--sa-audit-text-sub);
}

.audit-severity-chips {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.375rem;
}

.audit-chip {
    padding: 0.25rem 0.625rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.15s ease;
}

.audit-chip-default {
    background: transparent;
    color: var(--sa-audit-text-muted);
}
.audit-chip-default:hover {
    background: #F1F5F9;
    color: var(--sa-audit-text-main);
    text-decoration: none;
}
[data-theme="dark"] .audit-chip-default:hover {
    background: #1E293B;
}

.audit-chip-active {
    background: var(--sa-audit-blue);
    color: #FFFFFF;
}

.audit-chip-blue {
    background: #EFF6FF;
    color: #1D4ED8;
}
[data-theme="dark"] .audit-chip-blue {
    background: rgba(30, 58, 138, 0.3);
    color: #93C5FD;
}
.audit-chip-active-blue {
    background: #2563EB;
    color: #FFFFFF;
}

.audit-chip-amber {
    background: #FFFBEB;
    color: #B45309;
}
[data-theme="dark"] .audit-chip-amber {
    background: rgba(120, 53, 15, 0.3);
    color: #FCD34D;
}
.audit-chip-active-amber {
    background: #D97706;
    color: #FFFFFF;
}

.audit-chip-rose {
    background: #FEF2F2;
    color: #B91C1C;
}
[data-theme="dark"] .audit-chip-rose {
    background: rgba(136, 19, 55, 0.3);
    color: #FDA4AF;
}
.audit-chip-active-rose {
    background: #DC2626;
    color: #FFFFFF;
}

/* Filter Grid (Toolbar) */
.audit-filter-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 0.625rem;
    align-items: center;
}

@media (min-width: 640px) {
    .audit-filter-grid {
        grid-template-columns: repeat(12, 1fr);
    }
    .audit-filter-search-col { grid-column: span 5; }
    .audit-filter-actor-col { grid-column: span 3; }
    .audit-filter-entity-col { grid-column: span 3; }
    .audit-filter-actions-col { grid-column: span 1; }
}

.audit-filter-search-col {
    position: relative;
    display: flex;
    align-items: center;
}

.audit-filter-input {
    width: 100%;
    height: 34px;
    padding-left: 2rem;
    padding-right: 0.625rem;
    font-size: 0.75rem;
    border-radius: 8px;
    background: #F8FAFC;
    border: 1px solid var(--sa-audit-border);
    color: var(--sa-audit-text-main);
    outline: none;
}

[data-theme="dark"] .audit-filter-input {
    background: #111827;
}

.audit-filter-input:focus {
    border-color: var(--sa-audit-blue);
}

.audit-filter-search-icon {
    position: absolute;
    left: 0.625rem;
    color: #94A3B8;
    pointer-events: none;
}

.audit-filter-select {
    width: 100%;
    height: 34px;
    padding: 0 0.625rem;
    font-size: 0.75rem;
    border-radius: 8px;
    background: #F8FAFC;
    border: 1px solid var(--sa-audit-border);
    color: var(--sa-audit-text-main);
    outline: none;
    cursor: pointer;
}

[data-theme="dark"] .audit-filter-select {
    background: #111827;
}

.audit-filter-actions-col {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    justify-content: flex-end;
}

.audit-btn-submit {
    height: 34px;
    padding: 0 0.75rem;
    font-size: 0.75rem;
    font-weight: 700;
    background: var(--sa-audit-blue);
    color: #FFFFFF;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.15s ease;
}

.audit-btn-submit:hover {
    background: var(--sa-audit-blue-hover);
}

.audit-btn-reset {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: #F8FAFC;
    border: 1px solid var(--sa-audit-border);
    color: var(--sa-audit-text-muted);
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
}

[data-theme="dark"] .audit-btn-reset {
    background: #111827;
}

.audit-btn-reset:hover {
    background: #F1F5F9;
    color: var(--sa-audit-text-main);
    text-decoration: none;
}

[data-theme="dark"] .audit-btn-reset:hover {
    background: #1E293B;
}

/* 6. HIGH-DENSITY AUDIT LOG TABLE */
.audit-table-card {
    background: var(--sa-audit-surface);
    border: 1px solid var(--sa-audit-border);
    border-top: 3.5px solid var(--magkom-primary-blue, #01347C);
    border-radius: 12px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}

.audit-table-wrapper {
    overflow-x: auto;
    width: 100%;
}

.audit-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 0.75rem;
}

.audit-thead-row {
    border-bottom: 1px solid var(--sa-audit-border);
    background: rgba(248, 250, 252, 0.8);
    font-size: 0.6875rem;
    font-weight: 700;
    color: var(--sa-audit-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

[data-theme="dark"] .audit-thead-row {
    background: rgba(17, 24, 39, 0.6);
}

.audit-table th {
    padding: 0.625rem 0.875rem;
    white-space: nowrap;
}

.audit-th-time { width: 130px; }
.audit-th-channel { width: 140px; }
.audit-th-actor { width: 175px; }
.audit-th-diff { width: 230px; }
.audit-th-client { width: 145px; }
.audit-th-ops { width: 75px; text-align: right; }

.audit-tbody tr {
    border-bottom: 1px solid var(--sa-audit-border-subtle);
    transition: background-color 0.15s ease;
}

.audit-tbody tr:hover {
    background-color: rgba(248, 250, 252, 0.7);
}

[data-theme="dark"] .audit-tbody tr:hover {
    background-color: rgba(30, 41, 59, 0.4);
}

.audit-tr-critical {
    background-color: rgba(254, 242, 242, 0.35);
}

[data-theme="dark"] .audit-tr-critical {
    background-color: rgba(136, 19, 55, 0.12);
}

.audit-table td {
    padding: 0.625rem 0.875rem;
    vertical-align: middle;
}

.audit-time-wib {
    font-weight: 700;
    color: var(--sa-audit-text-main);
    font-variant-numeric: tabular-nums;
}

.audit-req-id {
    font-size: 0.625rem;
    color: var(--sa-audit-text-sub);
}

.audit-channel-badge-wrap {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    white-space: nowrap;
}

.badge-severity-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.15rem 0.4rem;
    border-radius: 4px;
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.badge-sev-info {
    background: #EFF6FF;
    color: #1D4ED8;
    border: 1px solid #BFDBFE;
}
[data-theme="dark"] .badge-sev-info {
    background: rgba(30, 58, 138, 0.4);
    color: #93C5FD;
    border-color: rgba(59, 130, 246, 0.4);
}

.badge-sev-success {
    background: #ECFDF5;
    color: #047857;
    border: 1px solid #A7F3D0;
}
[data-theme="dark"] .badge-sev-success {
    background: rgba(6, 78, 59, 0.4);
    color: #34D399;
    border-color: rgba(5, 150, 105, 0.4);
}

.badge-sev-warning {
    background: #FFFBEB;
    color: #B45309;
    border: 1px solid #FDE68A;
}
[data-theme="dark"] .badge-sev-warning {
    background: rgba(120, 53, 15, 0.4);
    color: #FCD34D;
    border-color: rgba(245, 158, 11, 0.4);
}

.badge-sev-danger, .badge-sev-kritis {
    background: #FEF2F2;
    color: #B91C1C;
    border: 1px solid #FECACA;
}
[data-theme="dark"] .badge-sev-danger, [data-theme="dark"] .badge-sev-kritis {
    background: rgba(136, 19, 55, 0.4);
    color: #FDA4AF;
    border-color: rgba(239, 68, 68, 0.4);
}

.audit-channel-slug {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--sa-audit-text-muted);
}

/* Actor */
.audit-actor-flex {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.audit-actor-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #334155;
    color: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.625rem;
    font-weight: 700;
    flex-shrink: 0;
}

[data-theme="dark"] .audit-actor-avatar {
    background: #1E293B;
    color: #CBD5E1;
}

.audit-actor-text-wrap {
    min-width: 0;
}

.audit-actor-name {
    font-weight: 600;
    color: var(--sa-audit-text-main);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 140px;
}

.audit-actor-role {
    font-size: 0.625rem;
    color: var(--sa-audit-text-sub);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 140px;
}

/* Action & Target */
.audit-action-target-wrap {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.35rem;
}

.audit-action-slug {
    font-weight: 700;
    font-size: 0.6875rem;
    color: var(--sa-audit-text-main);
}

.audit-target-badge {
    padding: 0.1rem 0.35rem;
    border-radius: 4px;
    font-size: 0.625rem;
    background: #F1F5F9;
    color: var(--sa-audit-text-muted);
}

[data-theme="dark"] .audit-target-badge {
    background: #1E293B;
    color: #94A3B8;
}

/* Diff Snapshot Pill */
.audit-diff-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.15rem 0.45rem;
    border-radius: 6px;
    background: #F8FAFC;
    border: 1px solid var(--sa-audit-border);
    font-size: 0.6875rem;
    white-space: nowrap;
}

[data-theme="dark"] .audit-diff-pill {
    background: #111827;
}

.audit-diff-old {
    color: #DC2626;
}
[data-theme="dark"] .audit-diff-old { color: #F87171; }

.audit-diff-arrow {
    color: #94A3B8;
}

.audit-diff-new {
    font-weight: 700;
    color: #059669;
}
[data-theme="dark"] .audit-diff-new { color: #34D399; }

/* Client */
.audit-client-ip {
    font-size: 0.6875rem;
    font-weight: 600;
    color: var(--sa-audit-text-main);
}

.audit-client-ua {
    font-size: 0.625rem;
    color: var(--sa-audit-text-sub);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 130px;
}

/* Action Button Diff */
.audit-btn-diff {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    background: #EFF6FF;
    color: #1D4ED8;
    border: none;
    cursor: pointer;
    transition: background-color 0.15s ease;
}

[data-theme="dark"] .audit-btn-diff {
    background: rgba(30, 58, 138, 0.4);
    color: #93C5FD;
}

.audit-btn-diff:hover {
    background: #DBEAFE;
}

[data-theme="dark"] .audit-btn-diff:hover {
    background: rgba(30, 58, 138, 0.6);
}

.audit-btn-diff-critical {
    background: #FEF2F2;
    color: #B91C1C;
}

[data-theme="dark"] .audit-btn-diff-critical {
    background: rgba(136, 19, 55, 0.4);
    color: #FDA4AF;
}

.audit-btn-diff-critical:hover {
    background: #FEE2E2;
}

/* Empty State */
.audit-empty-state {
    padding: 3rem 1.5rem;
    text-align: center;
}

.audit-empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #F1F5F9;
    color: #94A3B8;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem auto;
}

[data-theme="dark"] .audit-empty-icon {
    background: #1E293B;
}

.audit-empty-title {
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--sa-audit-text-main);
    margin: 0 0 0.25rem 0;
}

.audit-empty-desc {
    font-size: 0.75rem;
    color: var(--sa-audit-text-muted);
    margin: 0;
}

/* 7. PAGINATION FOOTER BAR */
.audit-pagination-footer {
    background: var(--sa-audit-surface);
    border: 1px solid var(--sa-audit-border);
    border-radius: 12px;
    padding: 0.875rem 1.125rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}

.audit-pagination-row {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

@media (min-width: 640px) {
    .audit-pagination-row {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}

.audit-pagination-bottom {
    padding-top: 0.625rem;
    border-top: 1px solid var(--sa-audit-border);
}

.audit-pagination-left {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.75rem;
}

.audit-pagination-counter {
    font-size: 0.75rem;
    color: var(--sa-audit-text-muted);
}

.audit-immutable-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.6875rem;
    font-weight: 600;
    color: #059669;
}

[data-theme="dark"] .audit-immutable-pill { color: #34D399; }

/* PER-PAGE SELECTOR */
.audit-per-page-container {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
}

.audit-per-page-label {
    color: var(--sa-audit-text-muted);
    font-weight: 500;
    font-size: 0.75rem;
}

.audit-per-page-group {
    display: inline-flex;
    align-items: center;
    background: var(--sa-audit-bg);
    border: 1px solid var(--sa-audit-border);
    border-radius: 8px;
    padding: 2px;
    gap: 2px;
}

.audit-per-page-pill {
    padding: 0.2rem 0.6rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--sa-audit-text-muted);
    text-decoration: none;
    transition: all 0.15s ease;
    line-height: 1.25;
}

.audit-per-page-pill:hover:not(.active) {
    background: #E2E8F0;
    color: var(--sa-audit-text-main);
    text-decoration: none;
}

.audit-per-page-pill.active {
    background: var(--sa-audit-blue);
    color: #FFFFFF;
    font-weight: 700;
    box-shadow: 0 1px 2px rgba(37, 99, 235, 0.2);
}

[data-theme="dark"] .audit-per-page-group {
    background: #0F172A;
    border-color: #334155;
}

[data-theme="dark"] .audit-per-page-pill:hover:not(.active) {
    background: #1E293B;
    color: #F8FAFC;
}

[data-theme="dark"] .audit-per-page-pill.active {
    background: #3B82F6;
    color: #FFFFFF;
}

.audit-pagination-lembar-info {
    font-size: 0.75rem;
    color: var(--sa-audit-text-muted);
}

.audit-page-controls {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.audit-page-btn-nav {
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    border: 1px solid var(--sa-audit-border);
    color: var(--sa-audit-text-muted);
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.15s ease;
}

.audit-page-btn-nav:hover:not(.disabled) {
    background: #F1F5F9;
    color: var(--sa-audit-text-main);
    text-decoration: none;
}

.audit-page-btn-nav.disabled {
    color: var(--sa-audit-text-sub);
    cursor: not-allowed;
    background: transparent;
}

.audit-page-num {
    min-width: 28px;
    height: 28px;
    padding: 0 0.35rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--sa-audit-text-muted);
    text-decoration: none;
    transition: all 0.15s ease;
}

.audit-page-num:hover:not(.active) {
    background: #F1F5F9;
    color: var(--sa-audit-text-main);
    text-decoration: none;
}

.audit-page-num.active {
    background: var(--sa-audit-blue);
    color: #FFFFFF;
    font-weight: 700;
}

.audit-page-ellipsis {
    min-width: 20px;
    text-align: center;
    color: var(--sa-audit-text-muted);
    font-size: 0.75rem;
    user-select: none;
}

/* 8. FORENSIC DIFF INSPECTOR CARD */
.audit-diff-card {
    background: var(--sa-audit-surface);
    border: 1px solid var(--sa-audit-border);
    border-top: 3.5px solid var(--magkom-primary-blue, #01347C);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}

.audit-diff-header {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--sa-audit-border-subtle);
}

@media (min-width: 640px) {
    .audit-diff-header {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}

.audit-diff-header-left {
    display: flex;
    align-items: center;
    gap: 0.625rem;
}

.audit-diff-delta-badge {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: #FFFBEB;
    color: #D97706;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.75rem;
    flex-shrink: 0;
}

[data-theme="dark"] .audit-diff-delta-badge {
    background: rgba(120, 53, 15, 0.4);
    color: #FBBF24;
}

.audit-diff-title-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.audit-diff-title {
    font-size: 0.75rem;
    font-weight: 800;
    color: var(--sa-audit-text-main);
}

.audit-diff-req-badge {
    padding: 0.1rem 0.35rem;
    border-radius: 4px;
    font-size: 0.625rem;
    background: #F1F5F9;
    color: var(--sa-audit-text-muted);
}

[data-theme="dark"] .audit-diff-req-badge {
    background: #1E293B;
    color: #94A3B8;
}

.audit-diff-subtitle {
    font-size: 0.6875rem;
    color: var(--sa-audit-text-muted);
    margin: 0.15rem 0 0 0;
}

.audit-diff-header-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.audit-diff-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.35rem 0.625rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    background: transparent;
    border: 1px solid var(--sa-audit-border);
    color: var(--sa-audit-text-main);
    cursor: pointer;
    transition: all 0.15s ease;
}

.audit-diff-action-btn:hover {
    background: #F1F5F9;
}

[data-theme="dark"] .audit-diff-action-btn:hover {
    background: #1E293B;
}

.audit-diff-btn-pdf {
    background: #EFF6FF;
    color: #1D4ED8;
    border-color: #BFDBFE;
}

[data-theme="dark"] .audit-diff-btn-pdf {
    background: rgba(30, 58, 138, 0.4);
    color: #93C5FD;
    border-color: rgba(59, 130, 246, 0.4);
}

.audit-diff-btn-pdf:hover {
    background: #DBEAFE;
}

/* Side-by-Side Panels */
.audit-diff-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 0.75rem;
}

@media (min-width: 768px) {
    .audit-diff-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.audit-diff-panel {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid var(--sa-audit-border);
}

.audit-diff-panel-old {
    background: rgba(254, 242, 242, 0.25);
    border-color: #FECACA;
}

[data-theme="dark"] .audit-diff-panel-old {
    background: rgba(136, 19, 55, 0.12);
    border-color: rgba(239, 68, 68, 0.4);
}

.audit-diff-panel-new {
    background: rgba(236, 253, 245, 0.25);
    border-color: #A7F3D0;
}

[data-theme="dark"] .audit-diff-panel-new {
    background: rgba(6, 78, 59, 0.12);
    border-color: rgba(5, 150, 105, 0.4);
}

.audit-diff-panel-bar {
    padding: 0.375rem 0.75rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.6875rem;
    font-weight: 700;
}

.audit-bar-rose {
    background: rgba(254, 226, 226, 0.6);
    border-bottom: 1px solid #FECACA;
}

[data-theme="dark"] .audit-bar-rose {
    background: rgba(136, 19, 55, 0.3);
    border-bottom-color: rgba(239, 68, 68, 0.4);
}

.audit-bar-emerald {
    background: rgba(209, 250, 229, 0.6);
    border-bottom: 1px solid #A7F3D0;
}

[data-theme="dark"] .audit-bar-emerald {
    background: rgba(6, 78, 59, 0.3);
    border-bottom-color: rgba(5, 150, 105, 0.4);
}

.audit-diff-panel-title {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.audit-diff-panel-time {
    font-size: 0.625rem;
    color: var(--sa-audit-text-muted);
}

.audit-diff-pre {
    padding: 0.75rem;
    font-family: monospace;
    font-size: 0.6875rem;
    line-height: 1.5;
    margin: 0;
    max-height: 220px;
    overflow-x: auto;
    background: transparent;
    border: none;
}

/* Forensic Footer */
.audit-forensic-footer {
    padding: 0.625rem 0.75rem;
    border-radius: 6px;
    background: #F8FAFC;
    border: 1px solid var(--sa-audit-border);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    font-size: 0.6875rem;
    color: var(--sa-audit-text-muted);
}

[data-theme="dark"] .audit-forensic-footer {
    background: #111827;
}

.audit-forensic-hmac-wrap {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    max-width: 400px;
}

.audit-forensic-hmac-label {
    font-weight: 700;
    color: var(--sa-audit-text-main);
}

.audit-forensic-hmac-val {
    color: var(--sa-audit-blue);
}

.audit-forensic-meta-wrap {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
}

.audit-integrity-verified {
    font-weight: 700;
    color: #059669;
}
[data-theme="dark"] .audit-integrity-verified { color: #34D399; }

/* 9. EXPORT POPOVER (Pola A Node 68749aa8) */
.audit-export-popover {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 320px;
    background: var(--sa-audit-surface);
    border: 1px solid var(--sa-audit-border);
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    padding: 1rem;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.audit-popover-pointer {
    position: absolute;
    top: -6px;
    right: 14px;
    width: 12px;
    height: 12px;
    background: var(--sa-audit-surface);
    border-top: 1px solid var(--sa-audit-border);
    border-left: 1px solid var(--sa-audit-border);
    transform: rotate(45deg);
}

.audit-popover-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--sa-audit-border-subtle);
}

.audit-popover-title {
    font-size: 0.75rem;
    font-weight: 800;
    color: var(--sa-audit-text-main);
}

.audit-popover-desc {
    font-size: 0.6875rem;
    color: var(--sa-audit-text-muted);
}

.audit-popover-row-count {
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
    font-size: 0.6875rem;
    font-weight: 700;
    background: #EFF6FF;
    color: #1D4ED8;
}

[data-theme="dark"] .audit-popover-row-count {
    background: rgba(30, 58, 138, 0.4);
    color: #93C5FD;
}

.audit-popover-formats {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.audit-format-card {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 0.5rem 0.625rem;
    border-radius: 8px;
    border: 1px solid var(--sa-audit-border);
    background: #F8FAFC;
    cursor: pointer;
    transition: all 0.15s ease;
    margin: 0;
}

[data-theme="dark"] .audit-format-card {
    background: #111827;
}

.audit-format-card:hover {
    background: #F1F5F9;
}

[data-theme="dark"] .audit-format-card:hover {
    background: #1F2937;
}

.audit-format-card.audit-format-selected {
    border: 2px solid var(--sa-audit-blue);
    background: rgba(7, 81, 255, 0.05);
}

[data-theme="dark"] .audit-format-card.audit-format-selected {
    background: rgba(59, 130, 246, 0.15);
}

.audit-format-content {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}

.audit-format-radio {
    margin-top: 2px;
}

.audit-format-name-wrap {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.audit-format-name {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--sa-audit-text-main);
}

.badge-format-ext {
    font-size: 0.625rem;
    font-family: monospace;
    font-weight: 700;
    padding: 0.05rem 0.3rem;
    border-radius: 4px;
    color: #FFFFFF;
}

.badge-ext-csv { background: #2563EB; }
.badge-ext-ndjson { background: #475569; }
.badge-ext-pdf { background: #DC2626; }

.audit-format-desc {
    font-size: 0.6875rem;
    color: var(--sa-audit-text-muted);
    margin: 0.15rem 0 0 0;
    line-height: 1.3;
}

.audit-format-meta {
    text-align: right;
    font-size: 0.625rem;
    font-family: monospace;
    color: var(--sa-audit-text-sub);
    flex-shrink: 0;
}

.audit-badge-recommend {
    font-weight: 700;
    color: var(--sa-audit-blue);
    display: block;
}

.audit-badge-forensic {
    font-weight: 600;
    color: var(--sa-audit-text-muted);
    display: block;
}

.audit-badge-legal {
    font-weight: 600;
    color: var(--sa-audit-text-muted);
    display: block;
}

.audit-format-size {
    display: block;
}

.audit-popover-security {
    padding-top: 0.5rem;
    border-top: 1px solid var(--sa-audit-border-subtle);
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    font-size: 0.6875rem;
}

.audit-security-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: var(--sa-audit-text-main);
    cursor: pointer;
    user-select: none;
    margin: 0;
}

.audit-security-checkbox {
    width: 14px;
    height: 14px;
    cursor: pointer;
}

.audit-popover-cta {
    padding-top: 0.5rem;
    border-top: 1px solid var(--sa-audit-border-subtle);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.audit-btn-more-options {
    background: none;
    border: none;
    font-size: 0.6875rem;
    font-weight: 600;
    color: var(--sa-audit-text-muted);
    cursor: pointer;
    padding: 0;
    transition: color 0.15s ease;
}

.audit-btn-more-options:hover {
    color: var(--sa-audit-blue);
}

.audit-btn-download-export {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    background: var(--sa-audit-blue);
    color: #FFFFFF;
    border: none;
    cursor: pointer;
    transition: background-color 0.15s ease;
}

.audit-btn-download-export:hover {
    background: var(--sa-audit-blue-hover);
}

/* 10. MODAL BACKDROP & DIALOG (#modalLogDetail) */
.audit-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
}

.audit-modal-content {
    background: var(--sa-audit-surface);
    border: 1px solid var(--sa-audit-border);
    border-radius: 12px;
    width: 100%;
    max-width: 820px;
    max-height: 90vh;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.audit-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--sa-audit-border);
}

.audit-modal-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.audit-modal-title {
    font-size: 1rem;
    font-weight: 800;
    color: var(--sa-audit-text-main);
    margin: 0;
}

.audit-modal-meta-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.2rem;
    font-size: 0.6875rem;
}

.audit-modal-channel {
    font-weight: 700;
    color: var(--sa-audit-blue);
}

.audit-modal-close-btn {
    background: transparent;
    border: none;
    font-size: 1.5rem;
    line-height: 1;
    color: var(--sa-audit-text-muted);
    cursor: pointer;
    padding: 0.25rem;
}

.audit-modal-close-btn:hover {
    color: var(--sa-audit-text-main);
}

.audit-modal-body {
    padding: 1.25rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.audit-modal-meta-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.625rem;
    padding: 0.875rem 1rem;
    border-radius: 8px;
    background: #F8FAFC;
    border: 1px solid var(--sa-audit-border);
    font-size: 0.75rem;
}

[data-theme="dark"] .audit-modal-meta-grid {
    background: #111827;
}

.audit-meta-label {
    color: var(--sa-audit-text-muted);
}

.audit-meta-val {
    color: var(--sa-audit-text-main);
}

.audit-modal-section-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--sa-audit-text-muted);
    margin-bottom: 0.375rem;
}

.audit-modal-payload-pre {
    padding: 0.875rem;
    border-radius: 6px;
    background: #0F172A;
    color: #F8FAFC;
    font-family: monospace;
    font-size: 0.6875rem;
    line-height: 1.45;
    max-height: 180px;
    overflow-x: auto;
    margin: 0;
}

.audit-modal-footer {
    padding: 0.75rem 1.25rem;
    border-top: 1px solid var(--sa-audit-border);
    display: flex;
    justify-content: flex-end;
}

.audit-modal-btn-dismiss {
    height: 34px;
    padding: 0 1rem;
    border-radius: 6px;
    border: 1px solid var(--sa-audit-border);
    background: transparent;
    color: var(--sa-audit-text-main);
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
}

.audit-modal-btn-dismiss:hover {
    background: #F1F5F9;
}
[data-theme="dark"] .audit-modal-btn-dismiss:hover {
    background: #1E293B;
}
</style>
