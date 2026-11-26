@extends('layouts.app')

@section('title', __('Peta Interaktif Alur Magang & PKL - Portal MagKom'))
@section('meta_description', 'Eksplorasi visual dan panduan alur tahapan magang Diskominfo Sleman dari registrasi kuota, seleksi berkas, pelaksanaan logbook, hingga pelepasan resmi selesai magang.')
@section('og_title', 'Peta Interaktif Alur Magang Diskominfo - MagKom')
@section('og_description', 'Pelajari alur resmi pendaftaran dan pelaksanaan magang secara visual, transparan, dan interaktif.')


@section('content')
<div class="alur-page-wrapper">
    <!-- Hero Header -->
    <header class="alur-hero">
        <div class="container">
            <div class="alur-hero-content">
                <div class="alur-hero-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    {{ __('Panduan Resmi Diskominfo Sleman') }}
                </div>
                <h1>{{ __('Peta Interaktif Alur & Siklus Magang') }}</h1>
                <p>{{ __('Jelajahi alur resmi pendaftaran, verifikasi berkas kuota, bimbingan penugasan harian, hingga penyelesaian program magang secara komprehensif.') }}</p>
            </div>

            <!-- KPI Strip -->
            <div class="alur-kpi-strip">
                <div class="alur-kpi-card">
                    <div class="alur-kpi-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                    </div>
                    <div>
                        <div class="alur-kpi-val">{{ __('4 Fase') }}</div>
                        <div class="alur-kpi-label">{{ __('Siklus Magang Resmi') }}</div>
                    </div>
                </div>

                <div class="alur-kpi-card">
                    <div class="alur-kpi-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <div class="alur-kpi-val">{{ __('1–3 Hari Kerja') }}</div>
                        <div class="alur-kpi-label">{{ __('Batas Waktu Verifikasi') }}</div>
                    </div>
                </div>

                <div class="alur-kpi-card">
                    <div class="alur-kpi-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <div>
                        <div class="alur-kpi-val">{{ __('100 Poin') }}</div>
                        <div class="alur-kpi-label">{{ __('Evaluasi Capaian Magang') }}</div>
                    </div>
                </div>

                <div class="alur-kpi-card">
                    <div class="alur-kpi-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                    </div>
                    <div>
                        <div class="alur-kpi-val">{{ __('Kelulusan Resmi') }}</div>
                        <div class="alur-kpi-label">{{ __('Pelepasan Magang') }}</div>
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
                <span class="alur-filter-title">{{ __('Jenjang:') }}</span>
                <div class="pill-group" role="radiogroup" aria-label="{{ __('Filter Jenjang') }}">
                    <button type="button" class="pill-btn active" data-sim-jenjang="semua">{{ __('Semua Jenjang') }}</button>
                    <button type="button" class="pill-btn" data-sim-jenjang="smk">{{ __('Siswa SMK (PKL)') }}</button>
                    <button type="button" class="pill-btn" data-sim-jenjang="mahasiswa">{{ __('Mahasiswa (D3/S1)') }}</button>
                </div>
            </div>

            <!-- Filter Tipe -->
            <div class="alur-filter-group">
                <span class="alur-filter-title">{{ __('Tipe Permohonan:') }}</span>
                <div class="pill-group" role="radiogroup" aria-label="{{ __('Filter Tipe Permohonan') }}">
                    <button type="button" class="pill-btn active" data-sim-tipe="mandiri">{{ __('Mandiri (1 Orang)') }}</button>
                    <button type="button" class="pill-btn" data-sim-tipe="kelompok">{{ __('Tim (2–4 Orang)') }}</button>
                </div>
            </div>

            <!-- View Mode Switch -->
            <div class="view-mode-switch" role="tablist" aria-label="{{ __('Mode Tampilan') }}">
                <button type="button" id="btn-view-graph" class="view-btn active" data-main-view="graph">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    {{ __('Diagram Graf') }}
                </button>
                <button type="button" id="btn-view-roadmap" class="view-btn" data-main-view="roadmap">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    {{ __('Roadmap Linier') }}
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
                        <button type="button" id="btnPlayTour" class="dag-sim-btn primary" data-dag-action="toggle-play" aria-label="Mulai atau jeda simulasi alur">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                            <span>Mulai Simulasi Alur</span>
                        </button>
                        <button type="button" id="btnPrevStep" class="dag-sim-btn" data-dag-action="prev" aria-label="Langkah sebelumnya">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                            <span>Sebelumnya</span>
                        </button>
                        <button type="button" id="btnNextStep" class="dag-sim-btn" data-dag-action="next" aria-label="Langkah selanjutnya">
                            <span>Selanjutnya</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                        <button type="button" id="btnResetStep" class="dag-sim-btn" data-dag-action="reset" aria-label="Reset alur ke langkah 1">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><polyline points="3 3 3 8 8 8"/></svg>
                            <span>Reset</span>
                        </button>
                    </div>
                    <div class="dag-sim-controls-right">
                        <!-- Canvas Zoom Controls -->
                        <div class="flowchart-zoom-group" role="group" aria-label="Kontrol Perbesaran Canvas">
                            <button type="button" id="btnZoomOut" class="dag-sim-btn zoom-btn" data-dag-zoom="-0.15" aria-label="Perkecil Canvas (-)" title="Perkecil Canvas (-)">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </button>
                            <button type="button" id="btnZoomReset" class="dag-sim-btn zoom-btn" data-dag-zoom="reset" aria-label="Reset Ukuran Canvas (100%)" title="Reset Ukuran (100%)">
                                <span id="zoomPercentLabel">100%</span>
                            </button>
                            <button type="button" id="btnZoomIn" class="dag-sim-btn zoom-btn" data-dag-zoom="0.15" aria-label="Perbesar Canvas (+)" title="Perbesar Canvas (+)">
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
                    <div class="stepper-node-dot active" data-step="1" data-node="1" tabindex="0" role="button" aria-label="Lompat ke Tahap 01 Cek Kuota">01</div>
                    <div class="stepper-node-dot" data-step="2" data-node="2" tabindex="0" role="button" aria-label="Lompat ke Tahap 02 Registrasi Akun">02</div>
                    <div class="stepper-node-dot" data-step="3" data-node="3" tabindex="0" role="button" aria-label="Lompat ke Tahap 03 Unggah Berkas">03</div>
                    <div class="stepper-node-dot" data-step="4" data-node="4" tabindex="0" role="button" aria-label="Lompat ke Tahap 04 Verifikasi Dokumen">04</div>
                    <div class="stepper-node-dot" data-step="5" data-node="5" tabindex="0" role="button" aria-label="Lompat ke Tahap 05 Orientasi & Mentor">05</div>
                    <div class="stepper-node-dot" data-step="6" data-node="6" tabindex="0" role="button" aria-label="Lompat ke Tahap 06 Logbook Harian">06</div>
                    <div class="stepper-node-dot" data-step="7" data-node="7" tabindex="0" role="button" aria-label="Lompat ke Tahap 07 Selesai Magang">07</div>
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

                                <!-- Step 06 (957, 224) -> Step 07 Selesai Magang (957, 252) -->
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
                            <div id="flow-node-start" class="flow-node-item flow-terminator-pill start svg-node-group" style="position: absolute; left: 20px; top: 22px; z-index: 4; border-radius: 9999px; background: #0F172A; color: #ffffff; padding: 5px 14px; font-size: 0.72rem; font-weight: 800; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.2);" data-node="1" tabindex="0" role="button" aria-label="Mulai: Akses Portal MagKom">
                                <span class="terminator-pulse-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #38BDF8; display: inline-block;"></span>
                                <span class="terminator-title">{!! __('START &bull; Akses Portal MagKom') !!}</span>
                            </div>

                            <!-- Ingress 1: Siswa SMK -->
                            <div id="node-smk" class="flow-node-item flow-node-circle svg-node-group" style="left: 20px; top: 88px;" data-sim-jenjang="smk" data-node="1" tabindex="0" role="button" aria-label="Jalur Siswa SMK (PKL)">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="right: -4px; top: 50%; transform: translateY(-50%);"></div>
                                <svg class="flow-circle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                                <span class="flow-circle-title">Siswa SMK</span>
                                <span class="flow-circle-sub">PKL 3 Bln</span>
                            </div>

                            <!-- Ingress 2: Mahasiswa -->
                            <div id="node-mhs" class="flow-node-item flow-node-circle svg-node-group" style="left: 20px; top: 205px;" data-sim-jenjang="mahasiswa" data-node="1" tabindex="0" role="button" aria-label="Jalur Mahasiswa (D3/S1)">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="right: -4px; top: 50%; transform: translateY(-50%);"></div>
                                <svg class="flow-circle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                <span class="flow-circle-title">Mahasiswa</span>
                                <span class="flow-circle-sub">D3 / S1</span>
                            </div>

                            <!-- Process Step 01: Cek Kuota -->
                            <div id="node-1" class="flow-node-item flow-node-rect step-1 svg-node-group active" style="left: 155px; top: 148px; width: 125px;" data-node="1" tabindex="0" role="button" aria-label="Tahap 01 Cek Kuota Formasi">
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
                            <div id="node-2" class="flow-node-item flow-node-rect step-2 svg-node-group" style="left: 305px; top: 148px; width: 125px;" data-node="2" tabindex="0" role="button" aria-label="Tahap 02 Registrasi Akun">
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
                            <div id="node-3" class="flow-node-item flow-node-rect step-3 svg-node-group" style="left: 455px; top: 148px; width: 130px;" data-node="3" tabindex="0" role="button" aria-label="Tahap 03 Unggah Berkas">
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
                            <div id="node-4" class="flow-node-item flow-node-diamond-wrapper svg-node-group" style="left: 610px; top: 157px;" data-node="4" tabindex="0" role="button" aria-label="Tahap 04 Evaluasi Dokumen Admin">
                                <div class="decision-rhombus-gateway flow-node-diamond-box">
                                    <div class="flow-node-diamond-content">
                                        <svg class="flow-diamond-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 22 12 12 22 2 12 12 2"/></svg>
                                        <div class="flow-diamond-text rhombus-title">Verifikasi Admin</div>
                                    </div>
                                </div>
                            </div>
                            <div class="visually-hidden" aria-hidden="true">{{ __('Gerbang Keputusan Evaluasi Dokumen Admin') }}</div>

                            <!-- Outcome Branch 1: Diterima Resmi (Pill Hijau) -->
                            <div id="node-diterima" class="flow-node-item flow-node-pill diterima svg-node-group" style="left: 735px; top: 55px; width: 130px;" data-node="diterima" tabindex="0" role="button" aria-label="Keputusan: Diterima Resmi">
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
                            <div id="node-waiting" class="flow-node-item flow-node-pill waiting svg-node-group" style="left: 735px; top: 160px; width: 130px;" data-node="waiting" tabindex="0" role="button" aria-label="Keputusan: Waiting List">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="left: -4px; top: 50%; transform: translateY(-50%); background:#3B82F6;"></div>
                                <svg class="flow-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <div>
                                    <div class="flow-pill-text">Waiting List</div>
                                    <div class="flow-pill-sub">Antrean FIFO</div>
                                </div>
                            </div>

                            <!-- Outcome Branch 3: Perlu Perbaikan (Pill Oranye - Loopback Source) -->
                            <div id="node-perbaikan" class="flow-node-item flow-node-pill perbaikan svg-node-group" style="left: 735px; top: 265px; width: 140px;" data-node="perbaikan" tabindex="0" role="button" aria-label="Keputusan: Perlu Perbaikan">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="left: -4px; top: 50%; transform: translateY(-50%); background:#D97706;"></div>
                                <div class="port-anchor" style="bottom: -4px; left: 50%; transform: translateX(-50%); background:#EA580C;"></div>
                                <svg class="flow-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><line x1="12" y1="16" x2="12" y2="16"/></svg>
                                <div>
                                    <div class="flow-pill-text">Perlu Perbaikan</div>
                                    <div class="flow-pill-sub">{{ __('Loopback ke Tahap 03') }}</div>
                                </div>
                            </div>

                            <!-- Process Step 05: Orientasi & Mentor -->
                            <div id="node-5" class="flow-node-item flow-node-rect step-5 svg-node-group" style="left: 895px; top: 43px; width: 125px;" data-node="5" tabindex="0" role="button" aria-label="Tahap 05 Orientasi & Mentor">
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
                            <div id="node-6" class="flow-node-item flow-node-rect step-6 svg-node-group" style="left: 895px; top: 148px; width: 125px;" data-node="6" tabindex="0" role="button" aria-label="Tahap 06 Logbook Harian">
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

                            <!-- Process Step 07: Selesai Magang -->
                            <div id="node-7" class="flow-node-item flow-node-rect step-7 svg-node-group" style="left: 895px; top: 252px; width: 125px;" data-node="7" tabindex="0" role="button" aria-label="Tahap 07 Selesai Magang">
                                <div class="selection-handle tl"></div><div class="selection-handle tr"></div><div class="selection-handle bl"></div><div class="selection-handle br"></div>
                                <div class="port-anchor" style="top: -4px; left: 50%; transform: translateX(-50%);"></div>
                                <div class="port-anchor" style="bottom: -4px; left: 50%; transform: translateX(-50%);"></div>
                                <div class="flow-rect-top">
                                    <span class="flow-rect-step-num" style="background:#DCFCE7;color:#166534;">07</span>
                                    <span class="flow-rect-tag" style="background:#DCFCE7;color:#166534;">Tuntas &amp; Lulus</span>
                                </div>
                                <h4 class="flow-rect-title">Selesai Magang</h4>
                                <p class="flow-rect-desc">Laporan &amp; Pelepasan</p>
                            </div>

                            <!-- Terminator Finish Pill -->
                            <div id="flow-node-finish" class="flow-node-item flow-node-finish-pill svg-node-group" style="left: 835px; top: 365px;" data-node="7" tabindex="0" role="button" aria-label="Pelepasan Magang Resmi">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                                <span class="terminator-title">{!! __('FINISH &bull; Selesai Magang &amp; Pelepasan') !!}</span>
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
                        <strong>Jalur Revisi Loopback Aktif:</strong> Berkas perlu diperbaiki oleh pemohon dalam tempo <strong>3x24 Jam Kalender</strong>. Alur berputar kembali ke <button type="button" data-node="3" style="background:none;border:none;color:#92400E;text-decoration:underline;font-weight:800;cursor:pointer;padding:0;font-size:inherit;">Tahap 03 (Unggah Berkas)</button>.
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
                        <label>{{ __('Estimasi / Batas Waktu') }}</label>
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
                        <span>Memilih mode Mandiri (1 orang) atau Tim (2–5 orang)</span>
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
                        <p style="font-size:0.875rem;color:var(--neutral-600);margin:0;">Penyelesaian tugas proyek, pengunggahan laporan akhir, dan pelepasan resmi peserta.</p>
                    </div>
                </div>
                <div class="timeline-steps-grid">
                    <div class="timeline-step-card">
                        <h4>Laporan Akhir</h4>
                        <p>Pengunggahan dokumen laporan akhir magang yang telah disahkan pembimbing.</p>
                    </div>
                    <div class="timeline-step-card">
                        <h4>Evaluasi 100 Poin</h4>
                        <p>Evaluasi komprehensif aspek kedisiplinan, kualitas kerja teknis, inisiatif, dan etika penugasan.</p>
                    </div>
                    <div class="timeline-step-card">
                        <h4>Pelepasan Magang (Masa 30 Hari)</h4>
                        <p>Penutupan masa magang dan masa tenggang akun 30 hari kalender (BR-10).</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================================
             FAQ & ATURAN BISNIS KUNCI ACCORDION
             ================================================================= -->
        <section class="alur-faq-section">
            <div class="faq-heading">
                <h2>{{ __('Pertanyaan Seputar Alur & Aturan Magang') }}</h2>
                <p style="font-size:0.95rem;color:var(--neutral-600);margin:0;">Pahami regulasi kedinasan untuk kelancaran permohonan magang Anda.</p>
            </div>

            <div class="faq-grid">
                <div class="faq-item">
                    <button type="button" class="faq-question-btn" data-toggle="faq">
                        <span>Apakah calon peserta dapat mendaftar di 2 bidang atau periode sekaligus?</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="faq-answer">
                        <strong>Tidak diperbolehkan (Aturan Anti-Monopoli BR-01).</strong> Setiap calon peserta hanya berhak mengajukan 1 permohonan pada 1 bidang dalam 1 periode bulan yang sedang berstatus 'Buka'. Hal ini untuk memberikan azas keadilan bagi seluruh pelajar dan mahasiswa.
                    </div>
                </div>

                <div class="faq-item">
                    <button type="button" class="faq-question-btn" data-toggle="faq">
                        <span>Bagaimana mekanisme antrean Waiting List jika kuota penuh?</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="faq-answer">
                        Sistem menggunakan prinsip <strong>First-Come First-Served (FIFO)</strong>. Jika kuota utama terisi penuh, pengajuan Anda akan dimasukkan ke antrean cadangan. Bila ada peserta yang mengundurkan diri atau ditolak verifikasinya, antrean teratas otomatis naik menjadi penerima kuota.
                    </div>
                </div>

                <div class="faq-item">
                    <button type="button" class="faq-question-btn" data-toggle="faq">
                        <span>Apa konsekuensi jika terlambat mengisi logbook harian?</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="faq-answer">
                        Sesuai aturan <strong>BR-09</strong>, logbook harian hanya dapat diisi paling lambat <strong>H-1 (kemarin)</strong>. Sistem secara otomatis mengunci tanggal lebih lampau guna menegakkan integritas data dan disiplin kerja kedinasan. Keterlambatan pengisian berisiko mengurangi nilai kedisiplinan pada evaluasi akhir.
                    </div>
                </div>

                <div class="faq-item">
                    <button type="button" class="faq-question-btn" data-toggle="faq">
                        <span>Berapa lama masa tenggang akses akun setelah selesai magang?</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="faq-answer">
                        Akun peserta diberikan <strong>Masa Tenggang Baca-Saja selama 30 Hari Kalender (BR-10)</strong> terhitung sejak tanggal magang berakhir. Selama 30 hari tersebut, Anda dapat mengakses riwayat aktivitas dan mengarsipkan laporan akhir. Setelahnya, akun dinonaktifkan secara otomatis.
                    </div>
                </div>

                <div class="faq-item">
                    <button type="button" class="faq-question-btn" data-toggle="faq">
                        <span>Bagaimana mekanisme penetapan status selesai dan kelulusan magang?</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="faq-answer">
                        Setelah seluruh penugasan dan logbook mingguan tuntas serta laporan akhir disetujui oleh Pembimbing Lapangan, status peserta secara resmi ditetapkan menjadi <strong>Selesai Magang</strong> pada portal MAGKOM dan data kepesertaan terarsip secara permanen.
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Card -->
        <section class="alur-cta-banner">
            <h3>{{ __('Siap Bergabung dengan Tim Diskominfo?') }}</h3>
            <p>Periksa ketersediaan kuota magang pada bidang pilihan Anda sekarang sebelum kuota bulan ini terpenuhi.</p>
            <a href="{{ route('landing') }}#kuota" class="alur-btn-primary">
                <span>{{ __('Lihat Kuota & Jadwal Magang') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </section>
    </main>
</div>
@endsection

@push('scripts')
@vite(['resources/js/pages/public/panduan-alur.js'])
@endpush
