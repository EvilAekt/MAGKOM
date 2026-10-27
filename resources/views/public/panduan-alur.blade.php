@extends('layouts.app')

@section('title', 'Peta Interaktif Alur Magang & PKL - Portal MagKom')
@section('meta_description', 'Eksplorasi visual dan panduan alur tahapan magang Diskominfo Sleman dari registrasi kuota, seleksi berkas, pelaksanaan logbook, hingga sertifikat resmi.')
@section('og_title', 'Peta Interaktif Alur Magang Diskominfo - MagKom')
@section('og_description', 'Pelajari alur resmi pendaftaran dan pelaksanaan magang secara visual, transparan, dan interaktif.')

@push('styles')
<style>
    /* =====================================================================
       PETA INTERAKTIF ALUR MAGANG STYLING (DESIGN SYSTEM DS-MAGKOM)
       ===================================================================== */
    .alur-page-wrapper {
        background-color: var(--neutral-100);
        min-height: 100vh;
        padding-bottom: 5rem;
    }

    /* Hero Header */
    .alur-hero {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.96) 0%, rgba(7, 81, 255, 0.88) 100%),
                    url('/images/hero_office_bg.png') center/cover no-repeat;
        color: #ffffff;
        padding: 4.5rem 0 3.5rem;
        position: relative;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .alur-hero-content {
        max-width: 840px;
    }

    .alur-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.25);
        padding: 0.35rem 0.95rem;
        border-radius: 9999px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 1rem;
        color: #ffffff;
    }

    .alur-hero h1 {
        font-size: clamp(2rem, 3.4vw, 2.75rem);
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 0.85rem;
        letter-spacing: -0.02em;
    }

    .alur-hero p {
        font-size: 1.05rem;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 1.75rem;
        max-width: 680px;
    }

    /* Quick KPI Strip */
    .alur-kpi-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
    }

    @media (max-width: 840px) {
        .alur-kpi-strip {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .alur-kpi-strip {
            grid-template-columns: 1fr;
        }
    }

    .alur-kpi-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 0.85rem;
        padding: 1rem 1.15rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .alur-kpi-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #ffffff;
    }

    .alur-kpi-val {
        font-size: 1.05rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.2;
    }

    .alur-kpi-label {
        font-size: 0.75rem;
        color: #cbd5e1;
        line-height: 1.3;
    }

    /* Controls & Simulator Bar */
    .alur-control-bar {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-400);
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        margin-top: -1.75rem;
        position: relative;
        z-index: 20;
        box-shadow: 0 12px 30px -6px rgba(15, 23, 42, 0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1.25rem;
    }

    .alur-filter-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.85rem;
    }

    .alur-filter-title {
        font-size: 0.8125rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--neutral-600);
        margin-right: 0.25rem;
    }

    .pill-group {
        display: inline-flex;
        background: var(--neutral-100);
        padding: 0.25rem;
        border-radius: 9999px;
        border: 1px solid var(--neutral-400);
    }

    .pill-btn {
        background: transparent;
        border: none;
        padding: 0.4rem 0.95rem;
        border-radius: 9999px;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--neutral-600);
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .pill-btn:hover {
        color: var(--blue-600);
    }

    .pill-btn.active {
        background: var(--blue-600);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(7, 81, 255, 0.3);
    }

    .view-mode-switch {
        display: inline-flex;
        background: var(--neutral-100);
        padding: 0.25rem;
        border-radius: 0.65rem;
        border: 1px solid var(--neutral-400);
    }

    .view-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: transparent;
        border: none;
        padding: 0.45rem 0.85rem;
        border-radius: 0.5rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--neutral-600);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .view-btn.active {
        background: var(--neutral-0);
        color: var(--neutral-900);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    /* Simulator Alert Notification */
    .simulator-banner {
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 0.75rem;
        padding: 0.85rem 1.25rem;
        margin-top: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        font-size: 0.85rem;
        color: #1E40AF;
        line-height: 1.5;
        transition: all 0.3s ease;
    }

    .simulator-banner svg {
        flex-shrink: 0;
        color: #2563EB;
    }

    /* Interactive Graph Canvas Area */
    .alur-main-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1.75rem;
        margin-top: 1.75rem;
        align-items: start;
    }

    @media (max-width: 1080px) {
        .alur-main-grid {
            grid-template-columns: 1fr;
        }
    }

    .graph-canvas-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-400);
        border-radius: 1.15rem;
        padding: 1.75rem;
        box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.05);
        position: relative;
        overflow: hidden;
    }

    .graph-canvas-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid var(--neutral-200);
    }

    .graph-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .graph-hint {
        font-size: 0.75rem;
        color: var(--neutral-500);
    }

    /* =====================================================================
       INTERACTIVE DAG WORKFLOW CANVAS & PIPELINE STYLING (DS-MAGKOM)
       ===================================================================== */
    .journey-board-container,
    .dag-canvas-container,
    .flowchart-canvas-wrapper {
        display: flex;
        flex-direction: column;
        gap: 0;
        position: relative;
        width: 100%;
        background: #ffffff;
        background-image: radial-gradient(rgba(15, 23, 42, 0.08) 1.2px, transparent 1.2px);
        background-size: 22px 22px;
        border: 1px solid var(--neutral-300);
        border-radius: 1.25rem;
        padding: 1.75rem 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.05), 0 2px 4px -2px rgba(15, 23, 42, 0.03);
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    .flowchart-canvas-inner {
        width: 100%;
        min-width: 580px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0;
        transform-origin: top center;
        transition: transform 200ms cubic-bezier(0.2, 0, 0, 1);
        position: relative;
    }

    /* Zoom Controls in Toolbar */
    .flowchart-zoom-group {
        display: inline-flex;
        align-items: center;
        background: var(--neutral-0);
        border: 1px solid var(--neutral-300);
        border-radius: 0.5rem;
        padding: 2px;
        gap: 2px;
    }

    .zoom-btn {
        min-height: 32px !important;
        padding: 0.25rem 0.55rem !important;
        font-size: 0.75rem !important;
        border-radius: 0.35rem !important;
        border: none !important;
        background: transparent !important;
        color: var(--neutral-700) !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-weight: 700;
        transition: all 120ms ease;
    }
    .zoom-btn:hover {
        background: var(--neutral-100) !important;
        color: var(--blue-600) !important;
    }
    .zoom-btn:active {
        transform: scale(0.95);
    }

    /* Flowchart Terminator Pills (Start & Finish) */
    .flow-terminator-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.55rem 1.4rem;
        border-radius: 9999px;
        font-size: 0.8125rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 180ms cubic-bezier(0.2, 0, 0, 1);
        user-select: none;
        z-index: 5;
        border: 2px solid transparent;
    }

    .flow-terminator-pill.start {
        background: #0F172A;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.25);
    }
    .flow-terminator-pill.start:hover {
        background: #1E293B;
        border-color: #38BDF8;
        transform: scale(1.02);
    }

    .flow-terminator-pill.finish {
        background: linear-gradient(135deg, #047857 0%, #10B981 100%);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
    }
    .flow-terminator-pill.finish:hover {
        filter: brightness(1.1);
        transform: scale(1.02);
    }

    .terminator-pulse-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #38BDF8;
        box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.6);
        animation: pulseRing 1.8s infinite;
    }

    @keyframes pulseRing {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(56, 189, 248, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(56, 189, 248, 0); }
    }

    /* Flowchart Vertical Edge Pipe */
    .flow-vertical-edge {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        height: 38px;
        margin: -2px 0;
    }

    .flow-edge-svg {
        width: 40px;
        height: 38px;
        overflow: visible;
    }

    .flow-edge-track {
        stroke: var(--neutral-300);
        stroke-width: 2.5px;
        fill: none;
        stroke-linecap: round;
    }

    .flow-edge-pulse {
        stroke: var(--blue-600);
        stroke-width: 3px;
        fill: none;
        stroke-dasharray: 6 4;
        opacity: 0;
        transition: opacity 0.25s ease;
        stroke-linecap: round;
    }

    .flow-vertical-edge.active .flow-edge-pulse {
        opacity: 1;
        animation: beamFlow 1.2s linear infinite;
    }

    .flow-edge-arrow {
        fill: var(--neutral-400);
        transition: fill 0.25s ease;
    }

    .flow-vertical-edge.active .flow-edge-arrow {
        fill: var(--blue-600);
    }

    /* Decision Rhombus Gateway */
    .decision-rhombus-gateway {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        margin: 1.15rem 0 0.85rem;
        padding: 0.65rem 1rem;
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        border-radius: 0.75rem;
        position: relative;
    }

    .rhombus-diamond-box {
        width: 30px;
        height: 30px;
        background: #D97706;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: rotate(45deg);
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(217, 119, 6, 0.35);
        flex-shrink: 0;
    }

    .rhombus-diamond-box svg {
        transform: rotate(-45deg);
    }

    .rhombus-title {
        font-size: 0.8125rem;
        font-weight: 800;
        color: #92400E;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    /* Loopback Target Anchor & Curved SVG Connector */
    .loopback-target-anchor {
        position: absolute;
        right: -8px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: var(--neutral-0, #ffffff);
        border: 2px solid #D97706;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .anchor-pulse {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #D97706;
    }

    .loopback-target-anchor.active,
    .journey-node-card.active .loopback-target-anchor {
        box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.35);
        transform: translateY(-50%) scale(1.15);
    }

    /* Curved Loopback SVG Canvas Container */
    .loopback-curved-svg-container {
        position: absolute;
        right: 8px;
        top: 240px;
        bottom: 520px;
        width: 70px;
        pointer-events: none;
        z-index: 8;
        display: none;
    }

    .loopback-curved-svg-container.active {
        display: block;
    }

    .loopback-curved-svg {
        width: 100%;
        height: 100%;
        overflow: visible;
    }

    .loopback-svg-path {
        fill: none;
        stroke: #D97706;
        stroke-width: 3px;
        stroke-dasharray: 6 4;
        animation: beamFlow 1.2s linear infinite;
    }

    .loopback-floating-badge {
        position: absolute;
        right: -10px;
        top: 50%;
        transform: translateY(-50%) rotate(90deg);
        transform-origin: center center;
        background: #FEF3C7;
        border: 1px solid #D97706;
        color: #92400E;
        font-size: 0.68rem;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 9999px;
        white-space: nowrap;
        box-shadow: 0 2px 6px rgba(217, 119, 6, 0.2);
    }

    /* 1. Simulation Control Toolbar */
    .dag-sim-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.85rem;
        padding: 0.85rem 1.15rem;
        background: var(--neutral-100);
        border: 1px solid var(--neutral-300);
        border-radius: 0.85rem;
        margin-bottom: 0.85rem;
    }

    .dag-sim-controls-left {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .dag-sim-controls-right {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .dag-sim-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.95rem;
        font-size: 0.8125rem;
        font-weight: 700;
        border-radius: 0.5rem;
        border: 1px solid var(--neutral-300);
        background: var(--neutral-0);
        color: var(--neutral-800);
        cursor: pointer;
        min-height: 40px;
        transition: all 150ms cubic-bezier(0.2, 0, 0, 1);
        user-select: none;
    }

    .dag-sim-btn:active {
        transform: scale(0.96);
    }

    .dag-sim-btn:hover {
        border-color: var(--blue-600);
        color: var(--blue-600);
        background: var(--blue-tint, #EFF6FF);
    }

    .dag-sim-btn.primary {
        background: var(--blue-600);
        border-color: var(--blue-600);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(7, 81, 255, 0.25);
    }

    .dag-sim-btn.primary:hover {
        background: var(--blue-700, #0043DB);
        color: #ffffff;
    }

    .dag-step-counter {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--neutral-700);
        font-variant-numeric: tabular-nums;
        background: var(--neutral-0);
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        border: 1px solid var(--neutral-300);
    }

    .dag-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--blue-600);
        background: rgba(7, 81, 255, 0.08);
        padding: 0.3rem 0.65rem;
        border-radius: 9999px;
        border: 1px solid rgba(7, 81, 255, 0.2);
    }

    /* 2. Journey Stepper Track */
    .journey-stepper-track {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        padding: 0.85rem 1.5rem;
        background: var(--neutral-100);
        border: 1px solid var(--neutral-300);
        border-radius: 0.85rem;
        margin-bottom: 1.25rem;
    }

    .stepper-progress-fill {
        position: absolute;
        top: 50%;
        left: 32px;
        right: 32px;
        height: 4px;
        background: var(--neutral-300);
        transform: translateY(-50%);
        z-index: 1;
        border-radius: 9999px;
    }

    .stepper-progress-bar {
        height: 100%;
        background: var(--blue-600);
        width: 14.28%;
        transition: width 300ms cubic-bezier(0.2, 0, 0, 1);
        border-radius: 9999px;
    }

    .stepper-node-dot {
        position: relative;
        z-index: 2;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--neutral-0, #ffffff);
        border: 2px solid var(--neutral-400);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8125rem;
        font-weight: 800;
        color: var(--neutral-700);
        cursor: pointer;
        transition: all 180ms cubic-bezier(0.2, 0, 0, 1);
        font-variant-numeric: tabular-nums;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
    }

    .stepper-node-dot:active {
        transform: scale(0.94);
    }

    .stepper-node-dot:hover {
        border-color: var(--blue-600);
        color: var(--blue-600);
    }

    .stepper-node-dot.active {
        background: var(--blue-600);
        border-color: var(--blue-600);
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(7, 81, 255, 0.25);
    }

    .stepper-node-dot.completed {
        background: #16A34A;
        border-color: #16A34A;
        color: #ffffff;
    }

    /* =====================================================================
       3. TRUE 2D FLOWCHART CANVAS & NODE GRAPH ENGINE (FIGMA/MIRO STYLE)
       ===================================================================== */
    .flowchart-viewport {
        position: relative;
        width: 100%;
        overflow: hidden; /* ZERO-SCROLL GUARANTEE */
        border-radius: 1rem;
        background: var(--neutral-100, #F8FAFC);
        border: 1px solid var(--neutral-300, #CBD5E1);
        padding: 0;
        box-shadow: inset 0 2px 6px rgba(15, 23, 42, 0.04);
        box-sizing: border-box;
    }

    .flowchart-autofit-stage {
        position: relative;
        width: 100%;
        overflow: hidden;
        transform-origin: top left;
        transition: height 0.2s cubic-bezier(0.2, 0, 0, 1);
    }

    .flowchart-canvas-plane {
        position: absolute;
        top: 0;
        left: 0;
        width: 1040px;
        min-width: 1040px;
        height: 500px;
        background-color: var(--neutral-0, #ffffff);
        background-image: radial-gradient(var(--neutral-300, #CBD5E1) 1.25px, transparent 1.25px);
        background-size: 20px 20px;
        border-radius: 0.85rem;
        user-select: none;
        transform-origin: top left;
        transition: transform 0.25s cubic-bezier(0.2, 0, 0, 1);
    }

    /* SVG Connector Overlay Layer (Behind Nodes) */
    .flow-svg-layer {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
        overflow: visible;
    }

    .flow-edge-track {
        fill: none;
        stroke: var(--neutral-300, #CBD5E1);
        stroke-width: 2px;
        stroke-linecap: round;
        stroke-linejoin: round;
        transition: stroke 0.2s ease, stroke-width 0.2s ease;
    }

    .flow-edge-track.active {
        stroke: var(--blue-600, #0751FF);
        stroke-width: 2.5px;
    }

    /* Living Motion: SVG Laser Beam Flow */
    .flow-edge-pulse {
        fill: none;
        stroke: #38BDF8;
        stroke-width: 3px;
        stroke-linecap: round;
        stroke-linejoin: round;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .flow-edge-pulse.active {
        opacity: 1;
        stroke-dasharray: 8 10;
        animation: laserBeamFlow 0.9s linear infinite;
    }

    .flow-edge-loopback {
        fill: none;
        stroke: #EA580C;
        stroke-width: 2.5px;
        stroke-dasharray: 6 4;
        stroke-linecap: round;
        stroke-linejoin: round;
        opacity: 0.75;
        transition: opacity 0.25s ease, stroke-width 0.25s ease;
    }

    .flow-edge-loopback.active {
        opacity: 1;
        stroke-width: 3px;
        stroke-dasharray: 6 6;
        animation: loopbackReverseFlow 1.1s linear infinite;
    }

    @keyframes laserBeamFlow {
        from { stroke-dashoffset: 36; }
        to { stroke-dashoffset: 0; }
    }

    @keyframes loopbackReverseFlow {
        from { stroke-dashoffset: 0; }
        to { stroke-dashoffset: 24; }
    }

    @media (prefers-reduced-motion: reduce) {
        .flow-edge-pulse.active,
        .flow-edge-loopback.active {
            animation: none !important;
        }
    }

    /* Edge Condition Badges (HTML over SVG) */
    .flow-edge-badge {
        position: absolute;
        z-index: 2;
        background: var(--neutral-0, #ffffff);
        border: 1px solid var(--neutral-300, #CBD5E1);
        padding: 3px 9px;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 700;
        color: var(--neutral-600, #475569);
        white-space: nowrap;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
        pointer-events: none;
        transform: translate(-50%, -50%);
        transition: all 0.2s ease;
    }

    .flow-edge-badge.active {
        color: var(--blue-600, #0751FF);
        border-color: var(--blue-400, #60A5FA);
        background: var(--blue-tint, #EFF6FF);
        box-shadow: 0 2px 6px rgba(7, 81, 255, 0.16);
    }

    .flow-edge-badge.loopback-badge {
        color: #9A3412;
        border-color: #FDBA74;
        background: #FFF7ED;
    }

    .flow-edge-badge.loopback-badge.active {
        background: #FFEDD5;
        border-color: #EA580C;
        color: #9A3412;
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.22);
    }

    /* HTML Interactive Node Layer (Z-Index 3) */
    .flow-node-item {
        position: absolute;
        z-index: 3;
        cursor: pointer;
        outline: none;
        transition: transform 150ms cubic-bezier(0.2, 0, 0, 1),
                    border-color 150ms ease,
                    box-shadow 150ms ease,
                    background-color 150ms ease;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
    }

    .flow-node-item:active {
        transform: scale(0.96) !important;
    }

    /* Node Port Anchors (Visual In/Out connection dots) */
    .port-anchor {
        position: absolute;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--neutral-400, #94A3B8);
        border: 1.5px solid var(--neutral-0, #ffffff);
        pointer-events: none;
        z-index: 4;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .flow-node-item:hover .port-anchor,
    .flow-node-item.active .port-anchor {
        background: var(--blue-600, #0751FF);
        transform: scale(1.25);
    }

    /* Figma-style Selection Handles (When Active) */
    .selection-handle {
        position: absolute;
        width: 6px;
        height: 6px;
        background: #ffffff;
        border: 1.5px solid var(--blue-600, #0751FF);
        border-radius: 1px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.15s ease;
        z-index: 5;
    }

    .selection-handle.tl { top: -4px; left: -4px; }
    .selection-handle.tr { top: -4px; right: -4px; }
    .selection-handle.bl { bottom: -4px; left: -4px; }
    .selection-handle.br { bottom: -4px; right: -4px; }

    .flow-node-item.active .selection-handle {
        opacity: 1;
    }

    /* =====================================================================
       NODE SHAPE 1: CIRCLE INGRESS NODES (PURPLE / LAVENDER)
       ===================================================================== */
    .flow-node-circle {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #FAF5FF;
        border: 2px solid #C084FC;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6B21A8;
        box-shadow: 0 4px 12px rgba(168, 85, 247, 0.14);
    }

    .flow-node-circle:hover {
        border-color: #9333EA;
        background: #F3E8FF;
        box-shadow: 0 6px 18px rgba(168, 85, 247, 0.24);
    }

    .flow-node-circle.active {
        border-color: var(--blue-600, #0751FF);
        box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.25), 0 6px 20px rgba(168, 85, 247, 0.25);
        background: #F3E8FF;
    }

    .flow-circle-icon {
        width: 20px;
        height: 20px;
        margin-bottom: 2px;
        color: #7E22CE;
    }

    .flow-circle-title {
        font-size: 0.72rem;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.01em;
    }

    .flow-circle-sub {
        font-size: 0.6rem;
        font-weight: 600;
        opacity: 0.8;
    }

    /* =====================================================================
       NODE SHAPE 2: PROCESS BOXES (ROUNDED RECTANGLES)
       ===================================================================== */
    .flow-node-rect {
        border-radius: 1rem;
        background: var(--neutral-0, #ffffff);
        border: 1.5px solid var(--neutral-300, #CBD5E1);
        padding: 0.75rem 0.85rem;
        box-shadow: 0 3px 10px rgba(15, 23, 42, 0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 84px;
        box-sizing: border-box;
    }

    .flow-node-rect:hover {
        border-color: var(--blue-400, #60A5FA);
        background: rgba(7, 81, 255, 0.015);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
    }

    .flow-node-rect.active {
        border-color: var(--blue-600, #0751FF);
        background: rgba(7, 81, 255, 0.04);
        box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.22), 0 8px 20px rgba(7, 81, 255, 0.14);
    }

    .flow-node-rect.step-1 { border-left: 4px solid #10B981; }
    .flow-node-rect.step-2 { border-left: 4px solid #0751FF; }
    .flow-node-rect.step-3 { border-left: 4px solid #F59E0B; }
    .flow-node-rect.step-5 { border-left: 4px solid #2563EB; }
    .flow-node-rect.step-6 { border-left: 4px solid #D97706; }
    .flow-node-rect.step-7 { border-left: 4px solid #16A34A; }

    .flow-rect-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.35rem;
    }

    .flow-rect-step-num {
        font-size: 0.6875rem;
        font-weight: 800;
        color: var(--blue-600, #0751FF);
        background: var(--blue-tint, #EFF6FF);
        padding: 1px 6px;
        border-radius: 4px;
        font-variant-numeric: tabular-nums;
    }

    .flow-rect-tag {
        font-size: 0.6rem;
        font-weight: 700;
        color: var(--neutral-500, #64748B);
        background: var(--neutral-150, #F1F5F9);
        padding: 1px 6px;
        border-radius: 9999px;
    }

    .flow-rect-title {
        font-size: 0.8125rem;
        font-weight: 800;
        color: var(--neutral-900, #0F172A);
        line-height: 1.25;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .flow-rect-desc {
        font-size: 0.6875rem;
        color: var(--neutral-600, #475569);
        margin: 0.25rem 0 0;
        line-height: 1.35;
        text-wrap: pretty;
    }

    /* Target Loopback Glow on Node 3 */
    .flow-node-rect.loopback-target-active {
        border-color: #EA580C !important;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.3) !important;
    }

    /* =====================================================================
       NODE SHAPE 3: DECISION DIAMOND (BELAH KETUPAT ROTATED 45 DEG)
       ===================================================================== */
    .flow-node-diamond-wrapper {
        width: 86px;
        height: 86px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .flow-node-diamond-box {
        width: 66px;
        height: 66px;
        background: #FFF7ED;
        border: 2px solid #F97316;
        border-radius: 10px;
        transform: rotate(45deg);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(249, 115, 22, 0.16);
        transition: all 180ms cubic-bezier(0.2, 0, 0, 1);
    }

    .flow-node-diamond-wrapper:hover .flow-node-diamond-box {
        border-color: #EA580C;
        background: #FFEDD5;
        box-shadow: 0 6px 18px rgba(249, 115, 22, 0.26);
    }

    .flow-node-diamond-wrapper.active .flow-node-diamond-box {
        border-color: var(--blue-600, #0751FF);
        box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.25), 0 6px 20px rgba(249, 115, 22, 0.25);
        background: #FFEDD5;
    }

    .flow-node-diamond-content {
        transform: rotate(-45deg); /* Counter-rotate text */
        text-align: center;
        color: #9A3412;
        padding: 2px;
    }

    .flow-diamond-icon {
        width: 16px;
        height: 16px;
        margin: 0 auto 2px;
        color: #C2410C;
    }

    .flow-diamond-text {
        font-size: 0.65rem;
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -0.01em;
    }

    /* =====================================================================
       NODE SHAPE 4: OUTCOME PILLS (BRANCH RESULTS)
       ===================================================================== */
    .flow-node-pill {
        border-radius: 0.65rem;
        padding: 0.55rem 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border: 1.5px solid transparent;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
    }

    .flow-node-pill.diterima {
        background: #DCFCE7;
        border-color: #86EFAC;
        color: #166534;
    }
    .flow-node-pill.diterima:hover,
    .flow-node-pill.diterima.active {
        border-color: #16A34A;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.25), 0 4px 12px rgba(22, 163, 74, 0.15);
    }

    .flow-node-pill.waiting {
        background: #EFF6FF;
        border-color: #BFDBFE;
        color: #1E40AF;
    }
    .flow-node-pill.waiting:hover,
    .flow-node-pill.waiting.active {
        border-color: #3B82F6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25), 0 4px 12px rgba(59, 130, 246, 0.15);
    }

    .flow-node-pill.perbaikan {
        background: #FEF3C7;
        border-color: #FDE68A;
        color: #92400E;
    }
    .flow-node-pill.perbaikan:hover,
    .flow-node-pill.perbaikan.active {
        border-color: #D97706;
        box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.25), 0 4px 12px rgba(217, 119, 6, 0.15);
    }

    .flow-pill-icon {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    .flow-pill-text {
        font-size: 0.75rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .flow-pill-sub {
        font-size: 0.625rem;
        opacity: 0.85;
    }

    /* Terminator End Pill */
    .flow-node-finish-pill {
        border-radius: 9999px;
        background: #F0FDF4;
        border: 2px solid #86EFAC;
        color: #166534;
        padding: 0.45rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.72rem;
        font-weight: 800;
        box-shadow: 0 2px 8px rgba(22, 163, 74, 0.12);
    }

    .flow-node-finish-pill:hover,
    .flow-node-finish-pill.active {
        border-color: #16A34A;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.25);
    }

    /* =====================================================================
       5. MULTIPLAYER CURSOR TAGS (FIGMA / MIRO COLLABORATIVE FEEL)
       ===================================================================== */
    .flow-cursor-tag {
        position: absolute;
        z-index: 6;
        pointer-events: none;
        display: flex;
        align-items: flex-start;
        gap: 2px;
        transition: transform 0.3s ease;
        animation: cursorFloat 4s ease-in-out infinite;
    }

    .flow-cursor-tag.tag-pink { color: #EC4899; }
    .flow-cursor-tag.tag-green { color: #10B981; animation-delay: -1.5s; }
    .flow-cursor-tag.tag-purple { color: #8B5CF6; animation-delay: -3s; }

    .cursor-badge-pill {
        font-size: 0.6875rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
        color: #ffffff;
        margin-top: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .flow-cursor-tag.tag-pink .cursor-badge-pill { background: #EC4899; }
    .flow-cursor-tag.tag-green .cursor-badge-pill { background: #10B981; }
    .flow-cursor-tag.tag-purple .cursor-badge-pill { background: #8B5CF6; }

    @keyframes cursorFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-4px); }
    }

    @media (prefers-reduced-motion: reduce) {
        .flow-edge-pulse.active,
        .flow-edge-loopback.active,
        .flow-cursor-tag {
            animation: none !important;
        }
    }

    /* Compatibility wrapper */
    .svg-node-group {
        cursor: pointer;
    }

    /* Loopback Ribbon Alert */
    .dag-loopback-ribbon {
        display: none;
        align-items: center;
        gap: 0.65rem;
        background: #FFFBEB;
        border: 1.5px dashed #D97706;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        margin-top: 0.85rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #92400E;
    }
    .dag-loopback-ribbon.active {
        display: flex;
    }

    /* Inspector Side Card (DS-MAGKOM Compliant) */
    .inspector-card {
        background: var(--neutral-0, #ffffff);
        border: 1px solid var(--neutral-300);
        border-radius: 1.15rem;
        padding: 1.75rem;
        box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.08);
        position: sticky;
        top: 90px;
    }

    .inspector-phase-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.85rem;
    }

    .inspector-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--neutral-900);
        margin: 0 0 0.5rem;
        line-height: 1.3;
        letter-spacing: -0.015em;
    }

    .inspector-desc {
        font-size: 0.875rem;
        color: var(--neutral-600);
        line-height: 1.55;
        margin-bottom: 1.25rem;
    }

    .inspector-meta-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.85rem;
        padding: 0.85rem;
        background: var(--neutral-100);
        border-radius: 0.75rem;
        border: 1px solid var(--neutral-200);
        margin-bottom: 1.25rem;
    }

    .inspector-meta-item label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--neutral-500);
        margin-bottom: 0.2rem;
    }

    .inspector-meta-item span {
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--neutral-800);
        font-variant-numeric: tabular-nums;
    }

    .inspector-section-label {
        font-size: 0.8125rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--neutral-700);
        margin: 1.25rem 0 0.65rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .inspector-checklist {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .inspector-check-item {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        font-size: 0.8125rem;
        color: var(--neutral-700);
        line-height: 1.45;
    }

    .inspector-check-item svg {
        color: var(--green-600);
        flex-shrink: 0;
        margin-top: 2px;
    }

    .inspector-callout {
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        border-left: 4px solid #D97706;
        border-radius: 0.65rem;
        padding: 0.85rem 1rem;
        margin-top: 1.25rem;
        font-size: 0.8rem;
        color: #92400E;
        line-height: 1.45;
    }

    .inspector-cta-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        background: var(--blue-600);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 0.75rem 1.25rem;
        border-radius: 0.65rem;
        text-decoration: none;
        margin-top: 1.5rem;
        transition: all 0.2s cubic-bezier(0.2, 0, 0, 1);
        box-shadow: 0 4px 14px rgba(7, 81, 255, 0.25);
    }

    .inspector-cta-btn:active {
        transform: scale(0.97);
    }

    .inspector-cta-btn:hover {
        background: var(--blue-700);
        color: #ffffff;
        box-shadow: 0 6px 20px rgba(7, 81, 255, 0.35);
    }

    /* Roadmap Linear View */
    .roadmap-timeline-container {
        display: none;
        margin-top: 1.75rem;
    }

    .roadmap-timeline-container.active {
        display: block;
    }

    .timeline-phase-block {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-400);
        border-radius: 1.15rem;
        padding: 1.75rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.05);
    }

    .timeline-phase-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--neutral-200);
    }

    .timeline-phase-num {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .timeline-steps-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.25rem;
    }

    .timeline-step-card {
        background: var(--neutral-100);
        border: 1px solid var(--neutral-200);
        border-radius: 0.85rem;
        padding: 1.25rem;
        transition: all 0.2s ease;
    }

    .timeline-step-card:hover {
        border-color: var(--blue-600);
        background: var(--neutral-0);
        box-shadow: 0 8px 20px -4px rgba(7, 81, 255, 0.1);
        transform: translateY(-2px);
    }

    .timeline-step-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .timeline-step-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        background: rgba(0, 0, 0, 0.06);
        color: var(--neutral-600);
    }

    .timeline-step-card h4 {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--neutral-900);
        margin: 0 0 0.35rem;
    }

    .timeline-step-card p {
        font-size: 0.8125rem;
        color: var(--neutral-600);
        margin: 0;
        line-height: 1.5;
    }

    /* FAQ Section */
    .alur-faq-section {
        margin-top: 3.5rem;
    }

    .faq-heading {
        text-align: center;
        margin-bottom: 2rem;
    }

    .faq-heading h2 {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--neutral-900);
        margin-bottom: 0.5rem;
    }

    .faq-grid {
        max-width: 860px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .faq-item {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-400);
        border-radius: 0.85rem;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .faq-question-btn {
        width: 100%;
        padding: 1.15rem 1.35rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: transparent;
        border: none;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--neutral-900);
        text-align: left;
        cursor: pointer;
    }

    .faq-question-btn svg {
        transition: transform 0.2s ease;
        flex-shrink: 0;
        color: var(--neutral-500);
    }

    .faq-item.open .faq-question-btn svg {
        transform: rotate(180deg);
        color: var(--blue-600);
    }

    .faq-answer {
        display: none;
        padding: 0 1.35rem 1.25rem;
        font-size: 0.875rem;
        color: var(--neutral-600);
        line-height: 1.6;
        border-top: 1px solid var(--neutral-200);
        margin-top: 0.25rem;
        padding-top: 1rem;
    }

    .faq-item.open .faq-answer {
        display: block;
    }

    /* Final CTA Card */
    .alur-cta-banner {
        margin-top: 3.5rem;
        background: linear-gradient(135deg, var(--neutral-900) 0%, #1e3a8a 100%);
        border-radius: 1.25rem;
        padding: 2.75rem 2rem;
        text-align: center;
        color: #ffffff;
        box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.25);
    }

    .alur-cta-banner h3 {
        font-size: clamp(1.5rem, 2.5vw, 2rem);
        font-weight: 800;
        margin-bottom: 0.75rem;
    }

    .alur-cta-banner p {
        font-size: 0.95rem;
        color: #cbd5e1;
        max-width: 600px;
        margin: 0 auto 1.75rem;
    }

    .alur-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--blue-600);
        color: #ffffff;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 0.85rem 2rem;
        border-radius: 0.65rem;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 6px 20px rgba(7, 81, 255, 0.4);
    }

    .alur-btn-primary:hover {
        background: var(--blue-700);
        color: #ffffff;
        transform: translateY(-2px);
    }

    /* Dark Mode Overrides */
    [data-theme="dark"] .alur-page-wrapper {
        background-color: var(--neutral-150);
    }

    [data-theme="dark"] .alur-control-bar,
    [data-theme="dark"] .graph-canvas-card,
    [data-theme="dark"] .inspector-card,
    [data-theme="dark"] .timeline-phase-block,
    [data-theme="dark"] .faq-item {
        background-color: var(--neutral-0);
        border-color: var(--neutral-200);
    }

    [data-theme="dark"] .pill-group,
    [data-theme="dark"] .view-mode-switch,
    [data-theme="dark"] .timeline-step-card {
        background-color: var(--neutral-100);
        border-color: var(--neutral-200);
    }

    [data-theme="dark"] .view-btn.active {
        background-color: var(--neutral-200);
        color: var(--neutral-900);
    }

    [data-theme="dark"] .simulator-banner {
        background: rgba(30, 58, 138, 0.3);
        border-color: #3b82f6;
        color: #93c5fd;
    }

    [data-theme="dark"] .inspector-meta-row {
        background: var(--neutral-100);
        border-color: var(--neutral-200);
    }

    [data-theme="dark"] .inspector-callout {
        background: rgba(146, 64, 14, 0.2);
        border-color: #b45309;
        color: #fde68a;
    }

    [data-theme="dark"] .dag-sim-toolbar,
    [data-theme="dark"] .journey-stepper-track {
        background: #0F172A;
        border-color: #26354D;
    }

    [data-theme="dark"] .dag-sim-btn {
        background: #182234;
        border-color: #26354D;
        color: #E2E8F0;
    }
    [data-theme="dark"] .dag-sim-btn:hover {
        border-color: #3B82F6;
        color: #60A5FA;
        background: #1E293B;
    }
    [data-theme="dark"] .dag-sim-btn.primary {
        background: #0751FF;
        border-color: #0751FF;
        color: #ffffff;
    }
    [data-theme="dark"] .dag-step-counter {
        background: #182234;
        border-color: #26354D;
        color: #E2E8F0;
    }

    [data-theme="dark"] .stepper-node-dot {
        background: #182234;
        border-color: #334155;
        color: #94A3B8;
    }
    [data-theme="dark"] .stepper-node-dot.active {
        background: #0751FF;
        border-color: #0751FF;
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(7, 81, 255, 0.4);
    }
    [data-theme="dark"] .stepper-node-dot.completed {
        background: #16A34A;
        border-color: #16A34A;
        color: #ffffff;
    }

    [data-theme="dark"] .decision-gateway-header {
        background: #1E293B;
        color: #94A3B8;
    }

    [data-theme="dark"] .journey-phase-track {
        background: var(--neutral-100, #0F172A) !important;
        border-color: var(--neutral-200, #26354D) !important;
    }
    [data-theme="dark"] .journey-node-card {
        background: var(--neutral-0, #182234) !important;
        border-color: var(--neutral-200, #26354D) !important;
    }
    [data-theme="dark"] .journey-node-card:hover {
        background: rgba(59, 130, 246, 0.12) !important;
        border-color: var(--blue-600, #3B82F6) !important;
    }
    [data-theme="dark"] .journey-node-card.active {
        background: rgba(59, 130, 246, 0.18) !important;
        border-color: var(--blue-600, #3B82F6) !important;
    }
    [data-theme="dark"] .node-card-title {
        color: var(--neutral-900, #FFFFFF) !important;
    }
    [data-theme="dark"] .node-card-subtitle {
        color: var(--neutral-500, #94A3B8) !important;
    }
    [data-theme="dark"] .node-number-badge {
        background: rgba(59, 130, 246, 0.25) !important;
        color: #60a5fa !important;
        border-color: rgba(59, 130, 246, 0.35) !important;
    }
    [data-theme="dark"] .node-tag-pill {
        background: var(--neutral-200, #1E293B) !important;
        color: var(--neutral-500, #CBD5E1) !important;
    }
    [data-theme="dark"] .decision-branch-card.diterima {
        background: rgba(22, 101, 52, 0.35) !important;
        border-color: rgba(34, 197, 94, 0.45) !important;
        color: #86efac !important;
    }
    [data-theme="dark"] .decision-branch-card.waiting {
        background: rgba(30, 64, 175, 0.35) !important;
        border-color: rgba(59, 130, 246, 0.45) !important;
        color: #93c5fd !important;
    }
    [data-theme="dark"] .decision-branch-card.perbaikan {
        background: rgba(153, 27, 27, 0.35) !important;
        border-color: rgba(239, 68, 68, 0.45) !important;
        color: #fca5a5 !important;
    }
    [data-theme="dark"] .dag-canvas-container,
    [data-theme="dark"] .flowchart-canvas-wrapper {
        background: #0F172A !important;
        background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1.2px, transparent 1.2px) !important;
        border-color: #26354D !important;
    }
    [data-theme="dark"] .flowchart-zoom-group {
        background: #182234 !important;
        border-color: #26354D !important;
    }
    [data-theme="dark"] .zoom-btn {
        color: #E2E8F0 !important;
    }
    [data-theme="dark"] .zoom-btn:hover {
        background: #26354D !important;
        color: #60A5FA !important;
    }
    [data-theme="dark"] .decision-rhombus-gateway {
        background: rgba(146, 64, 14, 0.25) !important;
        border-color: #B45309 !important;
    }
    [data-theme="dark"] .rhombus-title {
        color: #FDE68A !important;
    }
    [data-theme="dark"] .loopback-target-anchor {
        background: #182234 !important;
        border-color: #F59E0B !important;
    }
    [data-theme="dark"] .anchor-pulse {
        background: #F59E0B !important;
    }
    [data-theme="dark"] .loopback-floating-badge {
        background: #1E293B !important;
        border-color: #D97706 !important;
        color: #FDE68A !important;
    }
    [data-theme="dark"] .dag-loopback-ribbon {
        background: rgba(146, 64, 14, 0.25) !important;
        border-color: #D97706 !important;
        color: #FDE68A !important;
    }
    [data-theme="dark"] .dag-beam-badge {
        background: #182234 !important;
        border-color: #26354D !important;
        color: #94A3B8 !important;
    }
    [data-theme="dark"] .dag-pipeline-beam-connector.active .dag-beam-badge {
        color: #60A5FA !important;
        border-color: rgba(59, 130, 246, 0.45) !important;
        background: rgba(30, 58, 138, 0.4) !important;
    }
</style>
@endpush

@section('content')
<div class="alur-page-wrapper">
    <!-- Hero Header -->
    <header class="alur-hero">
        <div class="container">
            <div class="alur-hero-content">
                <div class="alur-hero-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Panduan Resmi Diskominfo Sleman
                </div>
                <h1>Peta Interaktif Alur &amp; Siklus Magang</h1>
                <p>Jelajahi alur resmi pendaftaran, verifikasi berkas kuota, bimbingan penugasan harian, hingga penerbitan lembar nilai dan sertifikat kelulusan digital secara komprehensif.</p>
            </div>

            <!-- KPI Strip -->
            <div class="alur-kpi-strip">
                <div class="alur-kpi-card">
                    <div class="alur-kpi-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                    </div>
                    <div>
                        <div class="alur-kpi-val">4 Fase</div>
                        <div class="alur-kpi-label">Siklus Magang Resmi</div>
                    </div>
                </div>

                <div class="alur-kpi-card">
                    <div class="alur-kpi-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <div class="alur-kpi-val">1–3 Hari Kerja</div>
                        <div class="alur-kpi-label">SLA Verifikasi Admin</div>
                    </div>
                </div>

                <div class="alur-kpi-card">
                    <div class="alur-kpi-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <div>
                        <div class="alur-kpi-val">100 Poin</div>
                        <div class="alur-kpi-label">Rubrik Evaluasi Objektif</div>
                    </div>
                </div>

                <div class="alur-kpi-card">
                    <div class="alur-kpi-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                    </div>
                    <div>
                        <div class="alur-kpi-val">QR Code Resmi</div>
                        <div class="alur-kpi-label">Sertifikat Terverifikasi</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <!-- Controls & Simulator Bar -->
        <div class="alur-control-bar">
            <!-- Filter Jenjang -->
            <div class="alur-filter-group">
                <span class="alur-filter-title">Jenjang:</span>
                <div class="pill-group" role="radiogroup" aria-label="Filter Jenjang">
                    <button type="button" class="pill-btn active" onclick="setSimJenjang('semua')">Semua Jenjang</button>
                    <button type="button" class="pill-btn" onclick="setSimJenjang('smk')">Siswa SMK (PKL)</button>
                    <button type="button" class="pill-btn" onclick="setSimJenjang('mahasiswa')">Mahasiswa (D3/S1)</button>
                </div>
            </div>

            <!-- Filter Tipe -->
            <div class="alur-filter-group">
                <span class="alur-filter-title">Tipe Permohonan:</span>
                <div class="pill-group" role="radiogroup" aria-label="Filter Tipe Permohonan">
                    <button type="button" class="pill-btn active" onclick="setSimTipe('mandiri')">Mandiri (1 Orang)</button>
                    <button type="button" class="pill-btn" onclick="setSimTipe('kelompok')">Kelompok (2–5 Orang)</button>
                </div>
            </div>

            <!-- View Mode Switch -->
            <div class="view-mode-switch" role="tablist" aria-label="Mode Tampilan">
                <button type="button" id="btn-view-graph" class="view-btn active" onclick="switchMainView('graph')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    Diagram Graf
                </button>
                <button type="button" id="btn-view-roadmap" class="view-btn" onclick="switchMainView('roadmap')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    Roadmap Linier
                </button>
            </div>
        </div>

        <!-- Simulator Dynamic Banner -->
        <div id="simulatorBanner" class="simulator-banner">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <div id="simulatorBannerText">
                <strong>Simulasi Mandiri Aktif:</strong> Pendaftaran individu (1 orang) untuk seluruh jenjang. Siapkan berkas Surat Pengantar Institusi, CV, dan Transkrip Nilai/Rapor dalam format PDF.
            </div>
        </div>

        <!-- =================================================================
             VIEW 1: INTERACTIVE SVG NODE GRAPH & JOURNEY FLOWCHART
             ================================================================= -->
        <div id="viewGraphArea" class="alur-main-grid">
            <!-- Left Column: Interactive Graph Canvas -->
            <div class="graph-canvas-card">
                <div class="graph-canvas-header">
                    <h2 class="graph-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Peta Hubungan Node Alur Magang
                    </h2>
                    <span class="graph-hint">Klik node untuk melihat detail persyaratan &amp; aturan</span>
                </div>

                <!-- Simulation Toolbar -->
                <div class="dag-sim-toolbar">
                    <div class="dag-sim-controls-left">
                        <button type="button" id="btnPlayTour" class="dag-sim-btn primary" onclick="toggleAutoPlayTour()" aria-label="Mulai atau jeda simulasi alur">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                            <span>Mulai Simulasi Alur</span>
                        </button>
                        <button type="button" id="btnPrevStep" class="dag-sim-btn" onclick="prevDagStep()" aria-label="Langkah sebelumnya">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                            <span>Sebelumnya</span>
                        </button>
                        <button type="button" id="btnNextStep" class="dag-sim-btn" onclick="nextDagStep()" aria-label="Langkah selanjutnya">
                            <span>Selanjutnya</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                        <button type="button" id="btnResetStep" class="dag-sim-btn" onclick="resetDagTour()" aria-label="Reset alur ke langkah 1">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><polyline points="3 3 3 8 8 8"/></svg>
                            <span>Reset</span>
                        </button>
                    </div>
                    <div class="dag-sim-controls-right">
                        <!-- Canvas Zoom Controls -->
                        <div class="flowchart-zoom-group" role="group" aria-label="Kontrol Perbesaran Canvas">
                            <button type="button" id="btnZoomOut" class="dag-sim-btn zoom-btn" onclick="zoomCanvas(-0.15)" aria-label="Perkecil Canvas (-)" title="Perkecil Canvas (-)">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </button>
                            <button type="button" id="btnZoomReset" class="dag-sim-btn zoom-btn" onclick="resetCanvasZoom()" aria-label="Reset Ukuran Canvas (100%)" title="Reset Ukuran (100%)">
                                <span id="zoomPercentLabel">100%</span>
                            </button>
                            <button type="button" id="btnZoomIn" class="dag-sim-btn zoom-btn" onclick="zoomCanvas(0.15)" aria-label="Perbesar Canvas (+)" title="Perbesar Canvas (+)">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </button>
                        </div>

                        <span id="dagStepCounter" class="dag-step-counter">Langkah 1 dari 7</span>
                        <span class="dag-status-pill">
                            <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                            Diagram Alur Aktif
                        </span>
                    </div>
                </div>

                <!-- Milestone Journey Stepper Track -->
                <div class="journey-stepper-track" role="navigation" aria-label="Pelacak Progres Alur">
                    <div class="stepper-progress-fill">
                        <div id="dagProgressBar" class="stepper-progress-bar"></div>
                    </div>
                    <div class="stepper-node-dot active" data-step="1" onclick="selectDagNode(1)" tabindex="0" role="button" aria-label="Lompat ke Tahap 01 Cek Kuota">01</div>
                    <div class="stepper-node-dot" data-step="2" onclick="selectDagNode(2)" tabindex="0" role="button" aria-label="Lompat ke Tahap 02 Registrasi Akun">02</div>
                    <div class="stepper-node-dot" data-step="3" onclick="selectDagNode(3)" tabindex="0" role="button" aria-label="Lompat ke Tahap 03 Unggah Berkas">03</div>
                    <div class="stepper-node-dot" data-step="4" onclick="selectDagNode(4)" tabindex="0" role="button" aria-label="Lompat ke Tahap 04 Verifikasi Dokumen">04</div>
                    <div class="stepper-node-dot" data-step="5" onclick="selectDagNode(5)" tabindex="0" role="button" aria-label="Lompat ke Tahap 05 Orientasi & Mentor">05</div>
                    <div class="stepper-node-dot" data-step="6" onclick="selectDagNode(6)" tabindex="0" role="button" aria-label="Lompat ke Tahap 06 Logbook Harian">06</div>
                    <div class="stepper-node-dot" data-step="7" onclick="selectDagNode(7)" tabindex="0" role="button" aria-label="Lompat ke Tahap 07 Nilai & Sertifikat">07</div>
                </div>

                <!-- =================================================================
                     TRUE 2D FLOWCHART CANVAS (ZERO-SCROLL AUTO-FIT LIVING GRAPH ENGINE)
                     ================================================================= -->
                <div class="flowchart-viewport journey-board-container dag-canvas-container flowchart-canvas-wrapper" id="flowchartViewport" role="region" aria-label="Kanvas Diagram Flowchart Interaktif Magang">
                    <div class="flowchart-autofit-stage" id="flowchartStage">
                        <div class="flowchart-canvas-plane flowchart-canvas-inner" id="flowchartCanvasPlane">

                            <!-- LAYER 1: SVG ORTHOGONAL CONNECTORS & ARROWS (BASE: 1040x500) -->
                            <svg class="flow-svg-layer" viewBox="0 0 1040 500" preserveAspectRatio="none" aria-hidden="true">
                                <defs>
                                    <marker id="arrow-default" viewBox="0 0 10 10" refX="7" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                        <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#94A3B8"/>
                                    </marker>
                                    <marker id="arrow-active" viewBox="0 0 10 10" refX="7" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                        <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#0751FF"/>
                                    </marker>
                                    <marker id="arrow-green" viewBox="0 0 10 10" refX="7" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                        <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#16A34A"/>
                                    </marker>
                                    <marker id="arrow-loopback" viewBox="0 0 10 10" refX="7" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                        <path d="M 0 1.5 L 8 5 L 0 8.5 z" fill="#EA580C"/>
                                    </marker>
                                </defs>

                                <!-- Ingress: START (X:55, Y:45) -> SMK (X:55, Y:88) & MHS (X:92, Y:241) menyatu ke Step 01 (X:155, Y:185) -->
                                <path id="edge-start-1" d="M 55 45 V 88" class="flow-edge-track active" marker-end="url(#arrow-default)" />
                                <path id="edge-smk-1" d="M 92 124 H 125 V 185 H 155" class="flow-edge-track active" marker-end="url(#arrow-default)" />
                                <path id="edge-mhs-1" d="M 92 241 H 125 V 185 H 155" class="flow-edge-track active" marker-end="url(#arrow-default)" />

                                <!-- Main Highway: Step 01 (280, 185) -> Step 02 (305, 185) -> Step 03 (455, 185) -->
                                <path id="edge-1-2" d="M 280 185 H 305" class="flow-edge-track active" marker-end="url(#arrow-default)" />
                                <path id="edge-1-2-pulse" d="M 280 185 H 305" class="flow-edge-pulse active" />

                                <path id="edge-2-3" d="M 430 185 H 455" class="flow-edge-track active" marker-end="url(#arrow-default)" />
                                <path id="edge-2-3-pulse" d="M 430 185 H 455" class="flow-edge-pulse active" />

                                <!-- Step 03 (585, 185) -> Decision Diamond (Verifikasi Admin X:610, Y:185) -->
                                <path id="edge-3-4" d="M 585 185 H 610" class="flow-edge-track active" marker-end="url(#arrow-default)" />
                                <path id="edge-3-4-pulse" d="M 585 185 H 610" class="flow-edge-pulse active" />

                                <!-- Decision Diamond Branches: (Diamond Center X:638, Y:185) -->
                                <!-- Branch 1 (Atas): Lolos Seleksi -> Diterima Resmi (X:735, Y:80) -->
                                <path id="edge-4-diterima" d="M 638 157 V 80 H 735" class="flow-edge-track active" marker-end="url(#arrow-green)" />
                                <path id="edge-4-diterima-pulse" d="M 638 157 V 80 H 735" class="flow-edge-pulse" />

                                <!-- Branch 2 (Tengah): Kuota Penuh -> Waiting List (X:735, Y:185) -->
                                <path id="edge-4-waiting" d="M 666 185 H 735" class="flow-edge-track" marker-end="url(#arrow-default)" />

                                <!-- Branch 3 (Bawah): Berkas Kurang -> Perlu Perbaikan (X:735, Y:290) -->
                                <path id="edge-4-perbaikan" d="M 638 213 V 290 H 735" class="flow-edge-track" marker-end="url(#arrow-loopback)" />

                                <!-- LOOPBACK REVISI: Dari Perlu Perbaikan (805, 318) memutar balik ke Step 03 Unggah Berkas (520, 226) -->
                                <path id="edge-loopback-flow" d="M 805 318 V 385 H 520 V 226" class="flow-edge-loopback" marker-end="url(#arrow-loopback)" />

                                <!-- Branch Diterima (865, 80) -> Step 05 Orientasi & Mentor (895, 80) -->
                                <path id="edge-diterima-5" d="M 865 80 H 895" class="flow-edge-track active" marker-end="url(#arrow-default)" />

                                <!-- Step 05 (957, 120) -> Step 06 Logbook Harian (957, 148) -->
                                <path id="edge-5-6" d="M 957 120 V 148" class="flow-edge-track" marker-end="url(#arrow-default)" />

                                <!-- Step 06 (957, 224) -> Step 07 Nilai & Sertifikat (957, 252) -->
                                <path id="edge-6-7" d="M 957 224 V 252" class="flow-edge-track" marker-end="url(#arrow-default)" />

                                <!-- Step 07 (957, 328) -> FINISH (957, 365) -->
                                <path id="edge-7-finish" d="M 957 328 V 365" class="flow-edge-track" marker-end="url(#arrow-green)" />
                            </svg>

                            <!-- EDGE CONDITION LABELS (HTML OVERLAYS) -->
                            <div class="flow-edge-badge" style="left: 125px; top: 160px;">PKL / Magang</div>
                            <div class="flow-edge-badge" style="left: 686px; top: 68px;">Lolos Seleksi</div>
                            <div class="flow-edge-badge" style="left: 700px; top: 172px;">Kuota Penuh</div>
                            <div class="flow-edge-badge" style="left: 686px; top: 302px;">Format Keliru</div>
                            <div class="flow-edge-badge loopback-badge" id="badgeLoopback" style="left: 660px; top: 385px;">
                                <span>&larr; Alur Loopback: Perbaikan Berkas 3&times;24 Jam</span>
                            </div>

                            <!-- LAYER 2: INTERACTIVE FLOWCHART NODES -->

                            <!-- Terminator Start Node (Pill) -->
                            <div id="flow-node-start" class="flow-node-item flow-terminator-pill start svg-node-group" style="position: absolute; left: 20px; top: 22px; z-index: 4; border-radius: 9999px; background: #0F172A; color: #ffffff; padding: 5px 14px; font-size: 0.72rem; font-weight: 800; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.2);" onclick="selectDagNode(1)" tabindex="0" role="button" aria-label="Mulai: Akses Portal MagKom">
                                <span class="terminator-pulse-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #38BDF8; display: inline-block;"></span>
                                <span class="terminator-title">START &bull; Akses Portal MagKom</span>
                            </div>

                            <!-- Ingress 1: Siswa SMK -->
                            <div id="node-smk" class="flow-node-item flow-node-circle svg-node-group" style="left: 20px; top: 88px;" onclick="setSimJenjang('smk'); selectDagNode(1);" tabindex="0" role="button" aria-label="Jalur Siswa SMK (PKL)">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="right: -4px; top: 50%; transform: translateY(-50%);"></div>
                                <svg class="flow-circle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                                <span class="flow-circle-title">Siswa SMK</span>
                                <span class="flow-circle-sub">PKL 3 Bln</span>
                            </div>

                            <!-- Ingress 2: Mahasiswa -->
                            <div id="node-mhs" class="flow-node-item flow-node-circle svg-node-group" style="left: 20px; top: 205px;" onclick="setSimJenjang('mahasiswa'); selectDagNode(1);" tabindex="0" role="button" aria-label="Jalur Mahasiswa (D3/S1)">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="right: -4px; top: 50%; transform: translateY(-50%);"></div>
                                <svg class="flow-circle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                <span class="flow-circle-title">Mahasiswa</span>
                                <span class="flow-circle-sub">D3 / S1</span>
                            </div>

                            <!-- Process Step 01: Cek Kuota -->
                            <div id="node-1" class="flow-node-item flow-node-rect step-1 svg-node-group active" style="left: 155px; top: 148px; width: 125px;" onclick="selectDagNode(1)" tabindex="0" role="button" aria-label="Tahap 01 Cek Kuota Formasi">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="left: -4px; top: 50%; transform: translateY(-50%);"></div>
                                <div class="port-anchor" style="right: -4px; top: 50%; transform: translateY(-50%);"></div>
                                <div class="flow-rect-top">
                                    <span class="flow-rect-step-num">01</span>
                                    <span class="flow-rect-tag" style="background:#DCFCE7;color:#166534;">Instan</span>
                                </div>
                                <h4 class="flow-rect-title">Cek Kuota</h4>
                                <p class="flow-rect-desc">Pantau kursi dinas</p>
                            </div>

                            <!-- Process Step 02: Registrasi Akun -->
                            <div id="node-2" class="flow-node-item flow-node-rect step-2 svg-node-group" style="left: 305px; top: 148px; width: 125px;" onclick="selectDagNode(2)" tabindex="0" role="button" aria-label="Tahap 02 Registrasi Akun">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="left: -4px; top: 50%; transform: translateY(-50%);"></div>
                                <div class="port-anchor" style="right: -4px; top: 50%; transform: translateY(-50%);"></div>
                                <div class="flow-rect-top">
                                    <span class="flow-rect-step-num">02</span>
                                    <span class="flow-rect-tag">Akun</span>
                                </div>
                                <h4 class="flow-rect-title">Registrasi Akun</h4>
                                <p class="flow-rect-desc">NISN/NIM &amp; WhatsApp</p>
                            </div>

                            <!-- Process Step 03: Unggah Berkas (Target Loopback) -->
                            <div id="node-3" class="flow-node-item flow-node-rect step-3 svg-node-group" style="left: 455px; top: 148px; width: 130px;" onclick="selectDagNode(3)" tabindex="0" role="button" aria-label="Tahap 03 Unggah Berkas">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="left: -4px; top: 50%; transform: translateY(-50%);"></div>
                                <div class="port-anchor" style="right: -4px; top: 50%; transform: translateY(-50%);"></div>
                                <div class="port-anchor" id="loopbackTargetAnchor" style="bottom: -4px; left: 50%; transform: translateX(-50%); background:#EA580C;" title="Port Masuk Loopback Revisi"></div>
                                <div class="flow-rect-top">
                                    <span class="flow-rect-step-num" style="background:#FEF3C7;color:#D97706;">03</span>
                                    <span class="flow-rect-tag" style="background:#FEF3C7;color:#D97706;">PDF 2MB</span>
                                </div>
                                <h4 class="flow-rect-title">Unggah Berkas</h4>
                                <p class="flow-rect-desc">Surat Pengantar &amp; CV</p>
                            </div>

                            <!-- Decision Diamond: Verifikasi Dokumen & Seleksi Admin -->
                            <div id="node-4" class="flow-node-item flow-node-diamond-wrapper svg-node-group" style="left: 610px; top: 157px;" onclick="selectDagNode(4)" tabindex="0" role="button" aria-label="Tahap 04 Evaluasi Dokumen Admin">
                                <div class="decision-rhombus-gateway flow-node-diamond-box">
                                    <div class="flow-node-diamond-content">
                                        <svg class="flow-diamond-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 22 12 12 22 2 12 12 2"/></svg>
                                        <div class="flow-diamond-text rhombus-title">Verifikasi Admin</div>
                                    </div>
                                </div>
                            </div>
                            <div class="visually-hidden" aria-hidden="true">Gerbang Keputusan Evaluasi Dokumen Admin</div>

                            <!-- Outcome Branch 1: Diterima Resmi (Pill Hijau) -->
                            <div id="node-diterima" class="flow-node-item flow-node-pill diterima svg-node-group" style="left: 735px; top: 55px; width: 130px;" onclick="selectDagNode('diterima')" tabindex="0" role="button" aria-label="Keputusan: Diterima Resmi">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="left: -4px; top: 50%; transform: translateY(-50%); background:#16A34A;"></div>
                                <div class="port-anchor" style="right: -4px; top: 50%; transform: translateY(-50%); background:#16A34A;"></div>
                                <svg class="flow-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                <div>
                                    <div class="flow-pill-text">Diterima Resmi</div>
                                    <div class="flow-pill-sub">Terbit SK Digital</div>
                                </div>
                            </div>

                            <!-- Outcome Branch 2: Waiting List (Pill Biru) -->
                            <div id="node-waiting" class="flow-node-item flow-node-pill waiting svg-node-group" style="left: 735px; top: 160px; width: 130px;" onclick="selectDagNode('waiting')" tabindex="0" role="button" aria-label="Keputusan: Waiting List">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="left: -4px; top: 50%; transform: translateY(-50%); background:#3B82F6;"></div>
                                <svg class="flow-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <div>
                                    <div class="flow-pill-text">Waiting List</div>
                                    <div class="flow-pill-sub">Antrean FIFO</div>
                                </div>
                            </div>

                            <!-- Outcome Branch 3: Perlu Perbaikan (Pill Oranye - Loopback Source) -->
                            <div id="node-perbaikan" class="flow-node-item flow-node-pill perbaikan svg-node-group" style="left: 735px; top: 265px; width: 140px;" onclick="selectDagNode('perbaikan')" tabindex="0" role="button" aria-label="Keputusan: Perlu Perbaikan">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="left: -4px; top: 50%; transform: translateY(-50%); background:#D97706;"></div>
                                <div class="port-anchor" style="bottom: -4px; left: 50%; transform: translateX(-50%); background:#EA580C;"></div>
                                <svg class="flow-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><line x1="12" y1="16" x2="12" y2="16"/></svg>
                                <div>
                                    <div class="flow-pill-text">Perlu Perbaikan</div>
                                    <div class="flow-pill-sub">Loopback ke Tahap 03</div>
                                </div>
                            </div>

                            <!-- Process Step 05: Orientasi & Mentor -->
                            <div id="node-5" class="flow-node-item flow-node-rect step-5 svg-node-group" style="left: 895px; top: 43px; width: 125px;" onclick="selectDagNode(5)" tabindex="0" role="button" aria-label="Tahap 05 Orientasi & Mentor">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="left: -4px; top: 50%; transform: translateY(-50%);"></div>
                                <div class="port-anchor" style="bottom: -4px; left: 50%; transform: translateX(-50%);"></div>
                                <div class="flow-rect-top">
                                    <span class="flow-rect-step-num">05</span>
                                    <span class="flow-rect-tag" style="background:#EFF6FF;color:#2563EB;">Hari Ke-1</span>
                                </div>
                                <h4 class="flow-rect-title">Orientasi Dinas</h4>
                                <p class="flow-rect-desc">Mentor &amp; Proyek</p>
                            </div>

                            <!-- Process Step 06: Logbook Harian -->
                            <div id="node-6" class="flow-node-item flow-node-rect step-6 svg-node-group" style="left: 895px; top: 148px; width: 125px;" onclick="selectDagNode(6)" tabindex="0" role="button" aria-label="Tahap 06 Logbook Harian">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="top: -4px; left: 50%; transform: translateX(-50%);"></div>
                                <div class="port-anchor" style="bottom: -4px; left: 50%; transform: translateX(-50%);"></div>
                                <div class="flow-rect-top">
                                    <span class="flow-rect-step-num" style="background:#FEF3C7;color:#D97706;">06</span>
                                    <span class="flow-rect-tag" style="background:#FEF3C7;color:#D97706;">Maks. H+1</span>
                                </div>
                                <h4 class="flow-rect-title">Logbook Harian</h4>
                                <p class="flow-rect-desc">Disiplin Harian (BR-09)</p>
                            </div>

                            <!-- Process Step 07: Nilai & Sertifikat QR -->
                            <div id="node-7" class="flow-node-item flow-node-rect step-7 svg-node-group" style="left: 895px; top: 252px; width: 125px;" onclick="selectDagNode(7)" tabindex="0" role="button" aria-label="Tahap 07 Nilai & Sertifikat">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="top: -4px; left: 50%; transform: translateX(-50%);"></div>
                                <div class="port-anchor" style="bottom: -4px; left: 50%; transform: translateX(-50%);"></div>
                                <div class="flow-rect-top">
                                    <span class="flow-rect-step-num" style="background:#DCFCE7;color:#166534;">07</span>
                                    <span class="flow-rect-tag" style="background:#DCFCE7;color:#166534;">100 Poin &amp; QR</span>
                                </div>
                                <h4 class="flow-rect-title">Nilai &amp; Sertifikat</h4>
                                <p class="flow-rect-desc">Evaluasi &amp; QR Code</p>
                            </div>

                            <!-- Terminator Finish Pill -->
                            <div id="flow-node-finish" class="flow-node-item flow-node-finish-pill svg-node-group" style="left: 835px; top: 365px;" onclick="selectDagNode(7)" tabindex="0" role="button" aria-label="Penerbitan Sertifikat Resmi">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                                <span class="terminator-title">FINISH &bull; Sertifikat QR &amp; Lembar Nilai</span>
                            </div>

                            <!-- MULTIPLAYER CURSOR TAGS (FIGMA/MIRO COLLABORATIVE VIBE) -->
                            <div class="flow-cursor-tag tag-pink" style="left: 195px; top: 100px;" title="Kursor Pemohon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="3 3 10 21 13 14 20 11 3 3"/></svg>
                                <span class="cursor-badge-pill">Ally (Pemohon SMK)</span>
                            </div>

                            <div class="flow-cursor-tag tag-green" style="left: 660px; top: 25px;" title="Kursor Tim Administrasi">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="3 3 10 21 13 14 20 11 3 3"/></svg>
                                <span class="cursor-badge-pill">Admin Diskominfo</span>
                            </div>

                            <div class="flow-cursor-tag tag-purple" style="left: 880px; top: 115px;" title="Kursor Pembimbing Lapangan">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="3 3 10 21 13 14 20 11 3 3"/></svg>
                                <span class="cursor-badge-pill">Pembimbing Lapangan</span>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Animated Loopback Return Ribbon Alert -->
                <div id="loopbackRibbon" class="dag-loopback-ribbon" role="alert" aria-live="polite">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><polyline points="3 3 3 8 8 8"/></svg>
                    <div>
                        <strong>Jalur Revisi Loopback Aktif:</strong> Berkas perlu diperbaiki oleh pemohon dalam tempo <strong>3x24 Jam Kalender</strong>. Alur berputar kembali ke <button type="button" onclick="selectDagNode(3)" style="background:none;border:none;color:#92400E;text-decoration:underline;font-weight:800;cursor:pointer;padding:0;font-size:inherit;">Tahap 03 (Unggah Berkas)</button>.
                    </div>
                </div>
            </div>

            <!-- Right Column: Interactive Node Inspector -->
            <aside class="inspector-card" id="inspectorCard" aria-live="polite">
                <div id="inspectorPhaseBadge" class="inspector-phase-badge" style="background:#E6EEFF;color:#0751FF;">
                    Fase 1: Pendaftaran &amp; Kuota
                </div>
                <h3 id="inspectorTitle" class="inspector-title">Pilih Formasi &amp; Cek Kuota Real-Time</h3>
                <p id="inspectorDesc" class="inspector-desc">
                    Calon peserta memantau sisa kuota kursi magang pada kalender bulanan secara langsung. Pendaftaran hanya dibuka untuk periode yang berstatus 'Buka'.
                </p>

                <div class="inspector-meta-row">
                    <div class="inspector-meta-item">
                        <label>Aktor Utama</label>
                        <span id="inspectorActor">Calon Peserta Magang</span>
                    </div>
                    <div class="inspector-meta-item">
                        <label>Estimasi / SLA</label>
                        <span id="inspectorSLA">Instan (Real-Time)</span>
                    </div>
                </div>

                <div class="inspector-section-label">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Persyaratan / Output Dokumen
                </div>
                <ul id="inspectorChecklist" class="inspector-checklist">
                    <li class="inspector-check-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Menentukan Jalur SMK (PKL) atau Mahasiswa (D3/S1)</span>
                    </li>
                    <li class="inspector-check-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Memilih mode Mandiri (1 orang) atau Kelompok (2–5 orang)</span>
                    </li>
                </ul>

                <div id="inspectorCallout" class="inspector-callout">
                    <strong>Aturan Bisnis Kunci (BR-01):</strong> Calon peserta hanya dapat mendaftar pada 1 bidang dalam 1 periode bulan yang sedang aktif.
                </div>

                <a id="inspectorCTA" href="{{ route('landing') }}#kuota" class="inspector-cta-btn">
                    <span>Lihat Kuota Tersedia Sekarang</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </aside>
        </div>

        <!-- =================================================================
             VIEW 2: LINEAR ROADMAP (MOBILE & ACCESSIBLE VIEW)
             ================================================================= -->
        <div id="viewRoadmapArea" class="roadmap-timeline-container">
            <!-- Phase 1 Block -->
            <div class="timeline-phase-block">
                <div class="timeline-phase-header">
                    <div class="timeline-phase-num" style="background:#E6EEFF;color:#0751FF;">1</div>
                    <div>
                        <h3 style="font-size:1.25rem;font-weight:800;color:var(--neutral-900);margin:0 0 4px;">Fase 1: Pendaftaran Akun &amp; Kuota</h3>
                        <p style="font-size:0.875rem;color:var(--neutral-600);margin:0;">Eksplorasi ketersediaan formasi, pembuatan akun, dan pengunggahan berkas.</p>
                    </div>
                </div>
                <div class="timeline-steps-grid">
                    <div class="timeline-step-card">
                        <div class="timeline-step-top">
                            <span class="timeline-step-badge">Langkah 01</span>
                            <span style="font-size:0.75rem;color:#16A34A;font-weight:700;">Publik</span>
                        </div>
                        <h4>Pilih Bidang &amp; Kuota</h4>
                        <p>Pantau kalender kuota bulanan. Pendaftaran dibuka untuk jalur SMK dan Mahasiswa.</p>
                    </div>
                    <div class="timeline-step-card">
                        <div class="timeline-step-top">
                            <span class="timeline-step-badge">Langkah 02</span>
                            <span style="font-size:0.75rem;color:#0751FF;font-weight:700;">Akun</span>
                        </div>
                        <h4>Registrasi &amp; Validasi</h4>
                        <p>Isi biodata lengkap, NISN/NIM, instansi pendidikan, dan kontak aktif WhatsApp.</p>
                    </div>
                    <div class="timeline-step-card">
                        <div class="timeline-step-top">
                            <span class="timeline-step-badge">Langkah 03</span>
                            <span style="font-size:0.75rem;color:#D97706;font-weight:700;">Dokumen</span>
                        </div>
                        <h4>Unggah Berkas PDF</h4>
                        <p>Surat pengantar resmi instansi, CV terbaru, dan transkrip nilai (maks. 2MB).</p>
                    </div>
                </div>
            </div>

            <!-- Phase 2 Block -->
            <div class="timeline-phase-block">
                <div class="timeline-phase-header">
                    <div class="timeline-phase-num" style="background:#E8F7EE;color:#16A34A;">2</div>
                    <div>
                        <h3 style="font-size:1.25rem;font-weight:800;color:var(--neutral-900);margin:0 0 4px;">Fase 2: Seleksi &amp; Keputusan Resmi</h3>
                        <p style="font-size:0.875rem;color:var(--neutral-600);margin:0;">Verifikasi berkas oleh Tim Admin Diskominfo dalam 1–3 hari kerja.</p>
                    </div>
                </div>
                <div class="timeline-steps-grid">
                    <div class="timeline-step-card" style="border-left: 4px solid #16A34A;">
                        <div class="timeline-step-top">
                            <span class="timeline-step-badge" style="background:#DCFCE7;color:#166534;">Status: Diterima</span>
                        </div>
                        <h4>Surat Keterangan Diterima</h4>
                        <p>Sistem otomatis menerbitkan SK Diterima resmi bertanda tangan digital &amp; QR Code.</p>
                    </div>
                    <div class="timeline-step-card" style="border-left: 4px solid #3B82F6;">
                        <div class="timeline-step-top">
                            <span class="timeline-step-badge" style="background:#EFF6FF;color:#1E40AF;">Status: Waiting List</span>
                        </div>
                        <h4>Antrean Prioritas FIFO</h4>
                        <p>Jika kuota penuh, peserta masuk antrean dan otomatis naik status jika ada pembatalan.</p>
                    </div>
                    <div class="timeline-step-card" style="border-left: 4px solid #DC2626;">
                        <div class="timeline-step-top">
                            <span class="timeline-step-badge" style="background:#FEF2F2;color:#991B1B;">Status: Perlu Perbaikan</span>
                        </div>
                        <h4>Jendela Waktu 3x24 Jam</h4>
                        <p>Bila dokumen buram, pemohon diberi waktu 3 hari kalender untuk mengunggah ulang.</p>
                    </div>
                </div>
            </div>

            <!-- Phase 3 Block -->
            <div class="timeline-phase-block">
                <div class="timeline-phase-header">
                    <div class="timeline-phase-num" style="background:#EFF6FF;color:#2563EB;">3</div>
                    <div>
                        <h3 style="font-size:1.25rem;font-weight:800;color:var(--neutral-900);margin:0 0 4px;">Fase 3: Pelaksanaan &amp; Logbook Harian</h3>
                        <p style="font-size:0.875rem;color:var(--neutral-600);margin:0;">Aktivitas kedinasan di bawah supervisi Pembimbing Lapangan profesional.</p>
                    </div>
                </div>
                <div class="timeline-steps-grid">
                    <div class="timeline-step-card">
                        <h4>Orientasi &amp; Penugasan</h4>
                        <p>Hari pertama pengenalan lingkungan dinas dan penetapan mentor teknis proyek.</p>
                    </div>
                    <div class="timeline-step-card">
                        <h4>Logbook Harian (Maks. H-1)</h4>
                        <p>Wajib diisi setiap hari kerja. Sistem otomatis mengunci tanggal lampau guna menegakkan disiplin (BR-09).</p>
                    </div>
                    <div class="timeline-step-card">
                        <h4>Review &amp; Bimbingan</h4>
                        <p>Pembimbing meninjau laporan progres mingguan dan memberikan arahan berkala.</p>
                    </div>
                </div>
            </div>

            <!-- Phase 4 Block -->
            <div class="timeline-phase-block">
                <div class="timeline-phase-header">
                    <div class="timeline-phase-num" style="background:#FEF3C7;color:#D97706;">4</div>
                    <div>
                        <h3 style="font-size:1.25rem;font-weight:800;color:var(--neutral-900);margin:0 0 4px;">Fase 4: Evaluasi &amp; Kelulusan</h3>
                        <p style="font-size:0.875rem;color:var(--neutral-600);margin:0;">Penilaian rubrik 100 poin, lembar nilai resmi, dan sertifikat digital.</p>
                    </div>
                </div>
                <div class="timeline-steps-grid">
                    <div class="timeline-step-card">
                        <h4>Laporan Akhir</h4>
                        <p>Pengunggahan dokumen laporan akhir magang yang telah disahkan pembimbing.</p>
                    </div>
                    <div class="timeline-step-card">
                        <h4>Rubrik 100 Poin</h4>
                        <p>Evaluasi objektif aspek kedisiplinan, kualitas kerja teknis, inisiatif, dan etika.</p>
                    </div>
                    <div class="timeline-step-card">
                        <h4>Sertifikat Digital (Masa 30 Hari)</h4>
                        <p>Unduh Lembar Nilai &amp; Sertifikat digital QR Code dalam masa tenggang 30 hari (BR-10).</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================================
             FAQ & ATURAN BISNIS KUNCI ACCORDION
             ================================================================= -->
        <section class="alur-faq-section">
            <div class="faq-heading">
                <h2>Pertanyaan Seputar Alur &amp; Aturan Magang</h2>
                <p style="font-size:0.95rem;color:var(--neutral-600);margin:0;">Pahami regulasi kedinasan untuk kelancaran permohonan magang Anda.</p>
            </div>

            <div class="faq-grid">
                <div class="faq-item">
                    <button type="button" class="faq-question-btn" onclick="toggleFaq(this)">
                        <span>Apakah calon peserta dapat mendaftar di 2 bidang atau periode sekaligus?</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="faq-answer">
                        <strong>Tidak diperbolehkan (Aturan Anti-Monopoli BR-01).</strong> Setiap calon peserta hanya berhak mengajukan 1 permohonan pada 1 bidang dalam 1 periode bulan yang sedang berstatus 'Buka'. Hal ini untuk memberikan azas keadilan bagi seluruh pelajar dan mahasiswa.
                    </div>
                </div>

                <div class="faq-item">
                    <button type="button" class="faq-question-btn" onclick="toggleFaq(this)">
                        <span>Bagaimana mekanisme antrean Waiting List jika kuota penuh?</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="faq-answer">
                        Sistem menggunakan prinsip <strong>First-Come First-Served (FIFO)</strong>. Jika kuota utama terisi penuh, pengajuan Anda akan dimasukkan ke antrean cadangan. Bila ada peserta yang mengundurkan diri atau ditolak verifikasinya, antrean teratas otomatis naik menjadi penerima kuota.
                    </div>
                </div>

                <div class="faq-item">
                    <button type="button" class="faq-question-btn" onclick="toggleFaq(this)">
                        <span>Apa konsekuensi jika terlambat mengisi logbook harian?</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="faq-answer">
                        Sesuai aturan <strong>BR-09</strong>, logbook harian hanya dapat diisi paling lambat <strong>H-1 (kemarin)</strong>. Sistem secara otomatis mengunci tanggal lebih lampau guna menegakkan integritas data dan disiplin kerja kedinasan. Keterlambatan pengisian berisiko mengurangi nilai kedisiplinan pada evaluasi akhir.
                    </div>
                </div>

                <div class="faq-item">
                    <button type="button" class="faq-question-btn" onclick="toggleFaq(this)">
                        <span>Berapa lama batas waktu mengunduh sertifikat dan lembar nilai?</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="faq-answer">
                        Akun peserta diberikan <strong>Masa Tenggang Baca-Saja selama 30 Hari Kalender (BR-10)</strong> terhitung sejak tanggal magang berakhir. Selama 30 hari tersebut, Anda dapat mengunduh Lembar Nilai Magang resmi dan Sertifikat Digital. Setelahnya, akun dinonaktifkan dan data diarsipkan secara permanen.
                    </div>
                </div>

                <div class="faq-item">
                    <button type="button" class="faq-question-btn" onclick="toggleFaq(this)">
                        <span>Bagaimana institusi atau kampus memvalidasi keaslian sertifikat?</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="faq-answer">
                        Setiap sertifikat digital dan lembar nilai resmi dilengkapi dengan <strong>QR Code Keabsahan Dokumen (AC-12)</strong>. Pihak sekolah, universitas, maupun rekruter kerja dapat memindai QR code tersebut untuk mengakses halaman verifikasi resmi Diskominfo yang memvalidasi nama, nomor registrasi, dan nilai akhir yang sah.
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Card -->
        <section class="alur-cta-banner">
            <h3>Siap Bergabung dengan Tim Diskominfo?</h3>
            <p>Periksa ketersediaan kuota magang pada bidang pilihan Anda sekarang sebelum kuota bulan ini terpenuhi.</p>
            <a href="{{ route('landing') }}#kuota" class="alur-btn-primary">
                <span>Lihat Kuota &amp; Jadwal Magang</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </section>
    </main>
</div>
@endsection

@push('scripts')
<script>
    // Data Node Dictionary for Dynamic Inspector
    const nodeData = {
        1: {
            phase: 'Fase 1: Pendaftaran & Kuota',
            phaseColor: '#0751FF',
            phaseBg: '#E6EEFF',
            title: 'Pilih Formasi & Cek Kuota Real-Time',
            desc: 'Calon peserta memantau sisa kuota kursi magang pada kalender bulanan secara langsung. Pendaftaran hanya dibuka untuk periode yang berstatus "Buka".',
            actor: 'Calon Peserta Magang',
            sla: 'Instan (Real-Time)',
            checklist: [
                'Menentukan Jalur SMK (PKL) atau Mahasiswa (D3/S1)',
                'Memilih mode Mandiri (1 orang) atau Kelompok (2–5 orang)',
                'Memastikan periode magang berstatus "Buka"'
            ],
            callout: '<strong>Aturan Bisnis Kunci (BR-01):</strong> Calon peserta hanya dapat mendaftar pada 1 bidang dalam 1 periode bulan yang sedang aktif.',
            ctaText: 'Lihat Kuota Tersedia Sekarang',
            ctaUrl: '{{ route("landing") }}#kuota'
        },
        2: {
            phase: 'Fase 1: Pendaftaran & Kuota',
            phaseColor: '#0751FF',
            phaseBg: '#E6EEFF',
            title: 'Registrasi Akun & Pengisian Biodata',
            desc: 'Mendaftarkan akun menggunakan alamat email aktif, verifikasi email, serta melengkapi biodata instansi pendidikan dan nomor WhatsApp aktif untuk notifikasi sistem.',
            actor: 'Calon Peserta / Ketua Kelompok',
            sla: '5–10 Menit',
            checklist: [
                'Alamat email aktif yang dapat diverifikasi',
                'Nomor WhatsApp aktif untuk koordinasi darurat',
                'Data NISN (SMK) atau NIM (Mahasiswa) yang valid'
            ],
            callout: '<strong>Jalur Kelompok (BR-02):</strong> Pendaftaran kelompok didaftarkan oleh 1 orang Ketua Kelompok yang menginput data seluruh anggota perwakilan.',
            ctaText: 'Mulai Buat Akun Peserta',
            ctaUrl: '{{ route("login") }}'
        },
        3: {
            phase: 'Fase 1: Pendaftaran & Kuota',
            phaseColor: '#0751FF',
            phaseBg: '#E6EEFF',
            title: 'Unggah Dokumen Persyaratan Wajib (PDF)',
            desc: 'Mengunggah seluruh kelengkapan berkas administratif dalam format PDF dengan ukuran maksimal 2MB per file sebelum batas waktu penutupan pendaftaran.',
            actor: 'Calon Peserta Magang',
            sla: 'Sebelum Batas Periode',
            checklist: [
                'Surat Pengantar Resmi Sekolah/Kampus (stempel basah/digital)',
                'Curriculum Vitae (CV) terbaru memuat keahlian & proyek',
                'Transkrip Nilai Kumulatif atau Rapor Semester Terakhir',
                'Proposal Magang (Opsional, sangat disarankan untuk kelompok)'
            ],
            callout: '<strong>Validitas Dokumen:</strong> Pastikan berkas terbaca jelas dan tidak berbayang agar proses seleksi berkas berjalan lancar tanpa status perbaikan.',
            ctaText: 'Unduh Panduan Lengkap PDF',
            ctaUrl: '{{ route("panduan.pdf") }}'
        },
        4: {
            phase: 'Fase 2: Seleksi & Penempatan',
            phaseColor: '#16A34A',
            phaseBg: '#E8F7EE',
            title: 'Verifikasi Berkas oleh Tim Admin Diskominfo',
            desc: 'Tim Administrasi Diskominfo menelaah keabsahan surat pengantar sekolah/kampus, keselarasan jurusan dengan bidang yang dituju, dan ketersediaan kuota.',
            actor: 'Tim Admin Diskominfo Sleman',
            sla: '1 sampai 3 Hari Kerja',
            checklist: [
                'Pengecekan stempel resmi dan legalitas surat pengantar',
                'Pencocokan kompetensi jurusan dengan kualifikasi bidang',
                'Alokasi ketersediaan ruangan kerja dan pembimbing lapangan'
            ],
            callout: '<strong>SLA Verifikasi:</strong> Hasil telaah diumumkan secara transparan melalui dashboard peserta maksimal dalam 3 hari kerja setelah berkas diajukan.',
            ctaText: 'Pantau Status Seleksi',
            ctaUrl: '{{ route("login") }}'
        },
        'diterima': {
            phase: 'Fase 2: Seleksi & Penempatan',
            phaseColor: '#166534',
            phaseBg: '#DCFCE7',
            title: 'Hasil Seleksi: Status DITERIMA RESMI',
            desc: 'Selamat! Berkas dan kuota Anda terkonfirmasi lolos. Sistem secara otomatis menerbitkan Surat Keterangan Diterima resmi lengkap dengan tanda tangan digital & QR Code.',
            actor: 'Peserta Magang Terpilih',
            sla: 'Konfirmasi Maks. 3 Hari',
            checklist: [
                'Mengunduh Surat Keterangan Diterima (PDF)',
                'Melakukan konfirmasi kehadiran melalui portal peserta',
                'Mempersiapkan seragam almamater dan perlengkapan dinas'
            ],
            callout: '<strong>Kewajiban Konfirmasi (BR-05):</strong> Peserta yang tidak melakukan konfirmasi kehadiran berisiko digantikan oleh antrean Waiting List.',
            ctaText: 'Masuk ke Dashboard Peserta',
            ctaUrl: '{{ route("login") }}'
        },
        'waiting': {
            phase: 'Fase 2: Seleksi & Penempatan',
            phaseColor: '#1E40AF',
            phaseBg: '#EFF6FF',
            title: 'Hasil Seleksi: WAITING LIST (Daftar Tunggu)',
            desc: 'Kuota formasi telah terisi penuh oleh pemohon yang mendaftar lebih awal. Anda masuk ke dalam antrean prioritas berbasis First-Come First-Served (FIFO).',
            actor: 'Sistem Antrean Otomatis',
            sla: 'Otomatis Promosi',
            checklist: [
                'Memantau pergerakan antrean berkala pada dashboard',
                'Otomatis naik menjadi Diterima jika kuota dibuka kembali',
                'Dapat memilih periode berikutnya jika kuota periode ini tertutup'
            ],
            callout: '<strong>Keadilan Alokasi (BR-03):</strong> Antrean bersifat otomatis tanpa intervensi manual guna menjaga transparansi penuh.',
            ctaText: 'Cek Posisi Antrean Saya',
            ctaUrl: '{{ route("login") }}'
        },
        'perbaikan': {
            phase: 'Fase 2: Seleksi & Penempatan',
            phaseColor: '#991B1B',
            phaseBg: '#FEF2F2',
            title: 'Hasil Seleksi: PERLU PERBAIKAN BERKAS',
            desc: 'Terdapat dokumen yang buram, tidak terbaca, atau keliru lampiran. Anda diberikan kesempatan waktu 3x24 jam untuk mengunggah ulang dokumen yang benar.',
            actor: 'Calon Peserta Magang',
            sla: '3x24 Jam Kalender',
            checklist: [
                'Membaca catatan revisi spesifik dari verifikator admin',
                'Mengunggah kembali dokumen PDF yang telah diperbaiki',
                'Menekan tombol kirim ulang perbaikan di dashboard'
            ],
            callout: '<strong>Batas Waktu:</strong> Apabila dalam 3x24 jam perbaikan tidak dikirimkan, permohonan otomatis berstatus tidak memenuhi syarat.',
            ctaText: 'Unggah Ulang Perbaikan',
            ctaUrl: '{{ route("login") }}'
        },
        5: {
            phase: 'Fase 3: Pelaksanaan & Logbook',
            phaseColor: '#2563EB',
            phaseBg: '#EFF6FF',
            title: 'Orientasi Dinas & Penetapan Mentor Lapangan',
            desc: 'Hari pertama kegiatan magang diawali dengan orientasi pengenalan lingkungan kantor Diskominfo, penugasan ruangan, serta penetapan Pembimbing Lapangan.',
            actor: 'Pembimbing Lapangan & Peserta',
            sla: 'Hari Pertama Magang',
            checklist: [
                'Pengenalan visi misi dan tata kelola Diskominfo',
                'Pemberian kartu identitas tamu / badge peserta magang',
                'Penyusunan rencana kerja proyek bersama Pembimbing'
            ],
            callout: '<strong>Tata Tertib:</strong> Wajib mematuhi jam kerja 07.30 s/d 16.00 WIB dan mengenakan seragam almamater atau pakaian formal rapi.',
            ctaText: 'Lihat Panduan Kedinasan',
            ctaUrl: '{{ route("landing") }}'
        },
        6: {
            phase: 'Fase 3: Pelaksanaan & Logbook',
            phaseColor: '#D97706',
            phaseBg: '#FEF3C7',
            title: 'Pengisian Logbook Harian (Batas Mundur H-1)',
            desc: 'Peserta wajib mencatat ringkasan aktivitas dan bukti progres kerja setiap hari kerja melalui portal peserta. Pembimbing akan meninjau dan menyetujui logbook berkala.',
            actor: 'Peserta Magang & Mentor',
            sla: 'Harian (Maks. H-1)',
            checklist: [
                'Uraian aktivitas harian spesifik dan terukur',
                'Melampirkan bukti output (link repositori, dokumen, atau foto)',
                'Mendapatkan persetujuan (Approve) mingguan dari Pembimbing'
            ],
            callout: '<strong>Aturan Disiplin (BR-09):</strong> Logbook hanya dapat diisi maksimal H-1 (kemarin). Sistem secara otomatis mengunci tanggal lampau guna menjamin keaslian kegiatan.',
            ctaText: 'Buka Logbook Harian',
            ctaUrl: '{{ route("login") }}'
        },
        7: {
            phase: 'Fase 4: Evaluasi & Kelulusan',
            phaseColor: '#D97706',
            phaseBg: '#FEF3C7',
            title: 'Penilaian Rubrik 100 Poin & Sertifikat Digital',
            desc: 'Penutupan magang, pengesahan laporan akhir, evaluasi rubrik objektif 100 poin oleh Pembimbing, dan penerbitan Lembar Nilai Magang resmi & Sertifikat Digital QR Code.',
            actor: 'Pembimbing & Pimpinan Dinas',
            sla: 'Masa Akses 30 Hari',
            checklist: [
                'Pengunggahan Laporan Akhir Magang yang telah disahkan',
                'Penilaian 5 aspek: Kehadiran, Kualitas Kerja, Teknis, Inisiatif, Etika',
                'Unduh Lembar Nilai Resmi (PDF) dan Sertifikat Kelulusan Digital'
            ],
            callout: '<strong>Masa Tenggang 30 Hari (BR-10):</strong> Akun peserta tetap aktif selama 30 hari kalender setelah masa magang berakhir untuk mengunduh seluruh dokumen kelulusan.',
            ctaText: 'Lihat Contoh Sertifikat QR',
            ctaUrl: '{{ route("landing") }}'
        }
    };

    let currentSimJenjang = 'semua';
    let currentSimTipe = 'mandiri';
    let currentActiveNodeId = 1;
    let isAutoPlaying = false;
    let autoPlayTimer = null;

    const tourSequence = [1, 2, 3, 4, 'diterima', 5, 6, 7];
    const nodeStepMapping = {
        1: { stepNum: 1, label: 'Langkah 1 dari 7: Cek Kuota' },
        2: { stepNum: 2, label: 'Langkah 2 dari 7: Registrasi Akun' },
        3: { stepNum: 3, label: 'Langkah 3 dari 7: Unggah Berkas' },
        4: { stepNum: 4, label: 'Langkah 4 dari 7: Verifikasi Admin' },
        'diterima': { stepNum: 4, label: 'Keputusan: Diterima Resmi' },
        'waiting': { stepNum: 4, label: 'Keputusan: Waiting List' },
        'perbaikan': { stepNum: 4, label: 'Keputusan: Perlu Perbaikan (Loopback)' },
        5: { stepNum: 5, label: 'Langkah 5 dari 7: Orientasi & Mentor' },
        6: { stepNum: 6, label: 'Langkah 6 dari 7: Logbook Harian' },
        7: { stepNum: 7, label: 'Langkah 7 dari 7: Nilai & Sertifikat' }
    };

    function setSimJenjang(val) {
        currentSimJenjang = val;
        updateSimPills();
        updateSimulatorState();
    }

    function setSimTipe(val) {
        currentSimTipe = val;
        updateSimPills();
        updateSimulatorState();
    }

    function updateSimPills() {
        const jenjangBtns = document.querySelectorAll('[aria-label="Filter Jenjang"] .pill-btn');
        jenjangBtns.forEach(btn => {
            const text = btn.innerText.toLowerCase();
            if (currentSimJenjang === 'semua' && text.includes('semua')) btn.classList.add('active');
            else if (currentSimJenjang === 'smk' && text.includes('smk')) btn.classList.add('active');
            else if (currentSimJenjang === 'mahasiswa' && text.includes('mahasiswa')) btn.classList.add('active');
            else btn.classList.remove('active');
        });

        const tipeBtns = document.querySelectorAll('[aria-label="Filter Tipe Permohonan"] .pill-btn');
        tipeBtns.forEach(btn => {
            const text = btn.innerText.toLowerCase();
            if (currentSimTipe === 'mandiri' && text.includes('mandiri')) btn.classList.add('active');
            else if (currentSimTipe === 'kelompok' && text.includes('kelompok')) btn.classList.add('active');
            else btn.classList.remove('active');
        });
    }

    function updateSimulatorState() {
        const bannerText = document.getElementById('simulatorBannerText');
        let text = '';

        if (currentSimJenjang === 'smk' && currentSimTipe === 'mandiri') {
            text = '<strong>Simulasi Siswa SMK (Mandiri):</strong> Pendaftaran perseorangan untuk Prakerin/PKL SMK. Berkas wajib: Surat Pengantar Sekolah berstempel resmi, CV siswa, dan Rapor semester terakhir (PDF maks. 2MB).';
        } else if (currentSimJenjang === 'smk' && currentSimTipe === 'kelompok') {
            text = '<strong>Simulasi Siswa SMK (Kelompok 2–5 Orang):</strong> Pendaftaran PKL beregu dari SMK yang sama. Ketua kelompok menginput data seluruh anggota + melampirkan Surat Pengantar kolektif dan Proposal Magang Tim.';
        } else if (currentSimJenjang === 'mahasiswa' && currentSimTipe === 'mandiri') {
            text = '<strong>Simulasi Mahasiswa D3/S1 (Mandiri):</strong> Magang riset/industri perseorangan. Berkas wajib: Surat Pengantar Fakultas/Jurusan, CV memuat portofolio proyek/keahlian, dan Transkrip Kumulatif resmi.';
        } else if (currentSimJenjang === 'mahasiswa' && currentSimTipe === 'kelompok') {
            text = '<strong>Simulasi Mahasiswa D3/S1 (Kelompok 2–5 Orang):</strong> Magang proyek tim atau riset kelompok. Wajib melampirkan Proposal Proyek Magang yang memuat pembagian peran anggota tim.';
        } else {
            text = '<strong>Peta Alur Penuh (Semua Jenjang):</strong> Menampilkan visualisasi siklus lengkap pendaftaran mandiri untuk seluruh formasi SMK dan Perguruan Tinggi di Diskominfo.';
        }

        bannerText.innerHTML = text;

        // Visual feedback on Node 3
        const node3 = document.getElementById('node-3');
        if (node3) {
            node3.classList.add('highlighted');
            setTimeout(() => node3.classList.remove('highlighted'), 1200);
        }
    }

    function selectDagNode(nodeId) {
        currentActiveNodeId = nodeId;

        // Deselect previous
        document.querySelectorAll('.svg-node-group, .dag-node-card, .dag-branch-pill').forEach(el => el.classList.remove('active'));

        // Highlight selected
        const selectedEl = document.getElementById(`node-${nodeId}`);
        if (selectedEl) {
            selectedEl.classList.add('active');
        }

        // Update step counter & stepper progress
        const mapping = nodeStepMapping[nodeId] || { stepNum: 1, label: 'Langkah 1 dari 7' };
        const counterEl = document.getElementById('dagStepCounter');
        if (counterEl) {
            counterEl.innerText = mapping.label;
        }

        const progressBar = document.getElementById('dagProgressBar');
        if (progressBar) {
            let pct = Math.round((mapping.stepNum / 7) * 100);
            if (nodeId === 'perbaikan') pct = 42; // Loopback indication
            progressBar.style.width = `${pct}%`;
        }

        // Update Stepper dots
        document.querySelectorAll('.stepper-node-dot').forEach(dot => {
            const dotStep = parseInt(dot.getAttribute('data-step'), 10);
            dot.classList.remove('active', 'completed');
            if (dotStep < mapping.stepNum) {
                dot.classList.add('completed');
            } else if (dotStep === mapping.stepNum) {
                dot.classList.add('active');
            }
        });

        // Update SVG Flow Pulses & Highlights
        const pulse12 = document.getElementById('edge-1-2-pulse');
        const pulse23 = document.getElementById('edge-2-3-pulse');
        const pulse34 = document.getElementById('edge-3-4-pulse');
        const pulse4Diterima = document.getElementById('edge-4-diterima-pulse');

        if (pulse12) pulse12.classList.toggle('active', mapping.stepNum >= 2);
        if (pulse23) pulse23.classList.toggle('active', mapping.stepNum >= 3);
        if (pulse34) pulse34.classList.toggle('active', mapping.stepNum >= 4);
        if (pulse4Diterima) pulse4Diterima.classList.toggle('active', (nodeId === 'diterima' || mapping.stepNum >= 5));

        // Update Loopback Edge & Target Highlight
        const loopbackRibbon = document.getElementById('loopbackRibbon');
        const loopbackTargetAnchor = document.getElementById('loopbackTargetAnchor');
        const loopbackEdge = document.getElementById('edge-loopback-flow');
        const loopbackBadge = document.getElementById('badgeLoopback');
        const node3 = document.getElementById('node-3');

        const isPerbaikan = (nodeId === 'perbaikan');
        if (loopbackRibbon) loopbackRibbon.classList.toggle('active', isPerbaikan);
        if (loopbackEdge) loopbackEdge.classList.toggle('active', isPerbaikan);
        if (loopbackBadge) loopbackBadge.classList.toggle('active', isPerbaikan);
        if (loopbackTargetAnchor) loopbackTargetAnchor.style.transform = isPerbaikan ? 'translateX(-50%) scale(1.6)' : 'translateX(-50%) scale(1)';

        if (node3) {
            node3.classList.toggle('loopback-target-active', isPerbaikan);
        }

        // Update Start & Finish Terminator Nodes
        const finishTerminator = document.getElementById('flow-node-finish');
        const edge7Finish = document.getElementById('edge-7-finish');

        if (finishTerminator) finishTerminator.classList.toggle('active', mapping.stepNum === 7);
        if (edge7Finish) edge7Finish.classList.toggle('active', mapping.stepNum >= 7);

        // Update Inspector Card
        const data = nodeData[nodeId];
        if (!data) return;

        const badge = document.getElementById('inspectorPhaseBadge');
        if (badge) {
            badge.innerText = data.phase;
            badge.style.color = data.phaseColor;
            badge.style.backgroundColor = data.phaseBg;
        }

        const titleEl = document.getElementById('inspectorTitle');
        if (titleEl) titleEl.innerText = data.title;

        const descEl = document.getElementById('inspectorDesc');
        if (descEl) descEl.innerText = data.desc;

        const actorEl = document.getElementById('inspectorActor');
        if (actorEl) actorEl.innerText = data.actor;

        const slaEl = document.getElementById('inspectorSLA');
        if (slaEl) slaEl.innerText = data.sla;

        const calloutEl = document.getElementById('inspectorCallout');
        if (calloutEl) calloutEl.innerHTML = data.callout;

        const ctaBtn = document.getElementById('inspectorCTA');
        if (ctaBtn) {
            const spanText = ctaBtn.querySelector('span');
            if (spanText) spanText.innerText = data.ctaText;
            ctaBtn.href = data.ctaUrl;
        }

        // Render checklist
        const listContainer = document.getElementById('inspectorChecklist');
        if (listContainer) {
            listContainer.innerHTML = '';
            data.checklist.forEach(item => {
                const li = document.createElement('li');
                li.className = 'inspector-check-item';
                li.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>${item}</span>
                `;
                listContainer.appendChild(li);
            });
        }

        // Smooth URL sync
        if (window.history && window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.set('step', nodeId);
            window.history.replaceState({}, '', url);
        }
    }

    // Zero-Scroll Auto-Fit & Responsive Zoom Engine
    let userZoomMultiplier = 1.0;
    const BASE_CANVAS_WIDTH = 1040;
    const BASE_CANVAS_HEIGHT = 500;

    function autoFitFlowchartCanvas() {
        const viewport = document.getElementById('flowchartViewport');
        const plane = document.getElementById('flowchartCanvasPlane');
        const stage = document.getElementById('flowchartStage');
        if (!viewport || !plane) return;

        // Ambil lebar kontainer viewport aktual (tanpa scrollbar)
        const availableWidth = viewport.clientWidth;
        if (availableWidth <= 0) return;

        // Auto scale proporsional terhadap base width 1040px
        const autoFitScale = Math.min(1.0, Math.max(0.40, availableWidth / BASE_CANVAS_WIDTH));
        const effectiveScale = parseFloat((autoFitScale * userZoomMultiplier).toFixed(3));

        plane.style.transform = `scale(${effectiveScale})`;
        plane.style.transformOrigin = 'top left';

        if (stage) {
            stage.style.height = `${Math.round(BASE_CANVAS_HEIGHT * effectiveScale)}px`;
            stage.style.width = '100%';
        }

        const zoomLabel = document.getElementById('zoomPercentLabel');
        if (zoomLabel) {
            const isAuto = (userZoomMultiplier === 1.0);
            zoomLabel.innerText = isAuto ? `${Math.round(effectiveScale * 100)}% (Auto)` : `${Math.round(effectiveScale * 100)}%`;
        }
    }

    function zoomCanvas(delta) {
        userZoomMultiplier = Math.min(1.6, Math.max(0.6, parseFloat((userZoomMultiplier + delta).toFixed(2))));
        autoFitFlowchartCanvas();
    }

    function resetCanvasZoom() {
        userZoomMultiplier = 1.0;
        autoFitFlowchartCanvas();
    }

    // Auto-Tour Player Functions
    function toggleAutoPlayTour() {
        if (isAutoPlaying) {
            stopAutoPlayTour();
        } else {
            startAutoPlayTour();
        }
    }

    function startAutoPlayTour() {
        isAutoPlaying = true;
        const playBtn = document.getElementById('btnPlayTour');
        if (playBtn) {
            playBtn.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                <span>Jeda Simulasi</span>
            `;
            playBtn.classList.remove('primary');
            playBtn.style.background = '#D97706';
            playBtn.style.borderColor = '#D97706';
            playBtn.style.color = '#ffffff';
        }
        runTourLoop();
    }

    function runTourLoop() {
        if (!isAutoPlaying) return;
        let currentIndex = tourSequence.indexOf(currentActiveNodeId);
        let nextIndex = (currentIndex + 1) % tourSequence.length;
        selectDagNode(tourSequence[nextIndex]);

        autoPlayTimer = setTimeout(() => {
            runTourLoop();
        }, 2800);
    }

    function stopAutoPlayTour() {
        isAutoPlaying = false;
        clearTimeout(autoPlayTimer);
        const playBtn = document.getElementById('btnPlayTour');
        if (playBtn) {
            playBtn.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                <span>Mulai Simulasi Alur</span>
            `;
            playBtn.classList.add('primary');
            playBtn.style.background = '';
            playBtn.style.borderColor = '';
            playBtn.style.color = '';
        }
    }

    function nextDagStep() {
        stopAutoPlayTour();
        let currentIndex = tourSequence.indexOf(currentActiveNodeId);
        let nextIndex = (currentIndex + 1) % tourSequence.length;
        selectDagNode(tourSequence[nextIndex]);
    }

    function prevDagStep() {
        stopAutoPlayTour();
        let currentIndex = tourSequence.indexOf(currentActiveNodeId);
        let prevIndex = (currentIndex - 1 + tourSequence.length) % tourSequence.length;
        selectDagNode(tourSequence[prevIndex]);
    }

    function resetDagTour() {
        stopAutoPlayTour();
        selectDagNode(1);
    }

    function switchMainView(mode) {
        const btnGraph = document.getElementById('btn-view-graph');
        const btnRoadmap = document.getElementById('btn-view-roadmap');
        const viewGraph = document.getElementById('viewGraphArea');
        const viewRoadmap = document.getElementById('viewRoadmapArea');

        if (mode === 'graph') {
            btnGraph.classList.add('active');
            btnRoadmap.classList.remove('active');
            viewGraph.style.display = 'grid';
            viewRoadmap.classList.remove('active');
            requestAnimationFrame(() => autoFitFlowchartCanvas());
        } else {
            btnRoadmap.classList.add('active');
            btnGraph.classList.remove('active');
            viewGraph.style.display = 'none';
            viewRoadmap.classList.add('active');
        }
    }

    function toggleFaq(btn) {
        const item = btn.closest('.faq-item');
        item.classList.toggle('open');
    }

    // Window & Viewport Resize Observers for Zero-Scroll
    window.addEventListener('resize', autoFitFlowchartCanvas);
    window.addEventListener('load', autoFitFlowchartCanvas);

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        // Panggil initial auto-fit layout
        autoFitFlowchartCanvas();

        const urlParams = new URLSearchParams(window.location.search);
        const stepParam = urlParams.get('step') || urlParams.get('fase');
        if (stepParam && nodeData[stepParam]) {
            selectDagNode(stepParam);
        } else {
            selectDagNode(1);
        }

        // Add keyboard Enter/Space support for milestone cards
        document.querySelectorAll('.journey-node-card, .decision-branch-card, .stepper-node-dot, .flow-terminator-pill').forEach(el => {
            el.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    el.click();
                }
            });
        });

        // Global Arrow key & Zoom navigation
        document.addEventListener('keydown', (e) => {
            if (['input', 'textarea', 'select'].includes(document.activeElement.tagName.toLowerCase())) {
                return;
            }
            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                e.preventDefault();
                nextDagStep();
            } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                e.preventDefault();
                prevDagStep();
            } else if (e.key === '+' || e.key === '=') {
                e.preventDefault();
                zoomCanvas(0.15);
            } else if (e.key === '-' || e.key === '_') {
                e.preventDefault();
                zoomCanvas(-0.15);
            } else if (e.key === '0') {
                e.preventDefault();
                resetCanvasZoom();
            }
        });
    });

    // Expose functions to window (Backward compatibility with selectNode)
    window.selectNode = selectDagNode;
    window.selectDagNode = selectDagNode;
    window.toggleAutoPlayTour = toggleAutoPlayTour;
    window.nextDagStep = nextDagStep;
    window.prevDagStep = prevDagStep;
    window.resetDagTour = resetDagTour;
    window.zoomCanvas = zoomCanvas;
    window.resetCanvasZoom = resetCanvasZoom;
    window.autoFitFlowchartCanvas = autoFitFlowchartCanvas;
    window.setSimJenjang = setSimJenjang;
    window.setSimTipe = setSimTipe;
    window.switchMainView = switchMainView;
    window.toggleFaq = toggleFaq;
</script>
@endpush
