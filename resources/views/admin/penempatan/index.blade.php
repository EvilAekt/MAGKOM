@extends('layouts.dashboard')

@section('title', 'Penempatan Peserta - Admin MagKom Sleman')

@section('topbar_title') Penempatan Peserta Magang <span class="sr-only">Manajemen Penempatan</span> @endsection

@section('content')
<div class="gentelella-penempatan-admin" style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">

    <style>
        /* ==========================================================================
           MAGKOM SLEMAN NAVY HERO BANNER (Signature Theme)
           Dinas Komunikasi dan Informatika Kabupaten Sleman
           ========================================================================== */
        .adm-hero-banner {
            background: radial-gradient(80% 120% at 100% 0%, rgba(63, 81, 181, 0.45), transparent 60%),
                        linear-gradient(135deg, #01347C 0%, #003566 100%);
            border-radius: 16px;
            padding: 24px 30px;
            color: #ffffff;
            position: relative;
            overflow: visible;
            box-shadow: 0 4px 20px -2px rgba(1, 52, 124, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.12);
            margin-bottom: 0.5rem;
        }

        .adm-hero-content {
            display: flex;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 1.25rem;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
            width: 100%;
        }

        .adm-hero-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 6px 0;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }

        .adm-hero-subtitle {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            line-height: 1.5;
        }

        .adm-hero-subtitle .sep {
            color: rgba(255, 255, 255, 0.5);
        }

        .adm-hero-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .adm-hero-btn-primary {
            background: #ffffff;
            color: #01347C;
            text-decoration: none;
            padding: 0.65rem 1.25rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        .adm-hero-btn-primary:hover {
            background: #F8FAFC;
            color: #0641CC;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
        }

        .adm-hero-btn-secondary {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            text-decoration: none;
            padding: 0.65rem 1.15rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.22);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .adm-hero-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .adm-hero-banner {
                padding: 20px 22px;
            }
            .adm-hero-content {
                flex-direction: column;
                align-items: flex-start;
            }
            .adm-hero-actions {
                width: 100%;
            }
            .adm-hero-btn-primary, .adm-hero-btn-secondary {
                flex: 1;
                justify-content: center;
            }
        }

        [data-theme="dark"] .adm-hero-banner {
            background: radial-gradient(80% 120% at 100% 0%, rgba(99, 102, 241, 0.2), transparent 60%),
                        linear-gradient(135deg, #0A192F 0%, #0F172A 100%);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.5);
        }

        .admin-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1.5px solid #CBD5E1;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }
        .admin-table th {
            background: #F8FAFC;
            padding: 0.85rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--neutral-600, #475569);
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
        }
        .admin-table td {
            padding: 0.95rem 1.25rem;
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
            vertical-align: middle;
            color: var(--neutral-800, #1E293B);
        }
        .admin-table tr:hover {
            background: #F8FAFC;
        }

        /* Action Table Buttons: Detail & Surat */
        .btn-table-action-detail {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.4rem 0.65rem;
            background: #EEF2FF;
            color: #4338CA;
            border: 1px solid #C7D2FE;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.75rem;
            text-decoration: none;
            transition: all 0.18s ease;
            cursor: pointer;
        }
        .btn-table-action-detail svg {
            transition: stroke 0.18s ease;
        }
        .btn-table-action-detail:hover {
            background: #0641CC !important;
            color: #FFFFFF !important;
            border-color: #0641CC !important;
            box-shadow: 0 2px 6px rgba(6, 65, 204, 0.35) !important;
            transform: translateY(-1px);
        }
        .btn-table-action-detail:hover svg {
            color: #FFFFFF !important;
            stroke: #FFFFFF !important;
        }

        .btn-table-action-surat {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.4rem 0.65rem;
            background: #EFF6FF;
            color: #1D4ED8;
            border: 1px solid #DBEAFE;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.18s ease;
        }
        .btn-table-action-surat svg {
            transition: stroke 0.18s ease;
        }
        .btn-table-action-surat:hover {
            background: #0641CC !important;
            color: #FFFFFF !important;
            border-color: #0641CC !important;
            box-shadow: 0 2px 6px rgba(6, 65, 204, 0.35) !important;
            transform: translateY(-1px);
        }
        .btn-table-action-surat:hover svg {
            color: #FFFFFF !important;
            stroke: #FFFFFF !important;
        }

        /* Action Dropdown Menu (Opsi 1: More Actions) */
        .action-dropdown-wrap {
            position: relative;
            display: inline-block;
        }
        .action-dropdown-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background: #FFFFFF;
            border: 1px solid var(--neutral-300, #CBD5E1);
            border-radius: 6px;
            color: var(--neutral-700, #334155);
            cursor: pointer;
            transition: all 0.18s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .action-dropdown-btn:hover,
        .action-dropdown-btn.active {
            background: #0641CC !important;
            border-color: #0641CC !important;
            color: #FFFFFF !important;
            box-shadow: 0 2px 6px rgba(6, 65, 204, 0.35) !important;
            transform: translateY(-1px);
        }
        .action-dropdown-btn:hover svg,
        .action-dropdown-btn.active svg {
            stroke: #FFFFFF !important;
            fill: none !important;
        }
        .action-dropdown-btn:hover circle,
        .action-dropdown-btn.active circle {
            fill: #FFFFFF !important;
            stroke: #FFFFFF !important;
        }
        .action-dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 4px);
            min-width: 195px;
            background: #FFFFFF;
            border: 1px solid var(--neutral-200, #E2E8F0);
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            padding: 0.35rem;
            z-index: 50;
            flex-direction: column;
            gap: 0.2rem;
            text-align: left;
        }
        .action-dropdown-menu.show {
            display: flex;
        }
        .action-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--neutral-700, #334155);
            background: transparent;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.12s ease;
            box-sizing: border-box;
            white-space: nowrap;
        }
        .action-dropdown-item:hover {
            background: #F1F5F9;
            color: #0F172A;
        }
        .action-dropdown-item.danger {
            color: #DC2626;
        }
        .action-dropdown-item.danger:hover {
            background: #FEF2F2;
            color: #B91C1C;
        }
        .action-dropdown-divider {
            height: 1px;
            background-color: var(--neutral-200, #E2E8F0);
            margin: 0.25rem 0;
        }
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(6px);
            z-index: 1050;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-container {
            background: #ffffff;
            border-radius: 16px;
            max-width: 540px;
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            border: 1px solid var(--neutral-200, #E2E8F0);
            animation: modalFadeIn 0.2s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        /* =====================================================================
           DARK MODE OVERRIDES (DS-MagKom Standards)
           ===================================================================== */
        [data-theme="dark"] a[href*="ekspor-surat-diterima"][href*="format=csv"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] a[href*="ekspor-surat-diterima"][href*="format=csv"]:hover {
            background: #162238 !important;
            border-color: #3B82F6 !important;
            color: #60A5FA !important;
        }

        [data-theme="dark"] a[href*="ekspor-surat-diterima"][href*="format=xlsx"] {
            background: #059669 !important;
            color: #FFFFFF !important;
            border: none !important;
        }

        [data-theme="dark"] a[href*="ekspor-surat-diterima"][href*="format=xlsx"]:hover {
            background: #047857 !important;
        }

        [data-theme="dark"] .admin-card {
            background-color: #182234 !important;
            border-color: #26354D !important;
        }

        [data-theme="dark"] .admin-card label {
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .admin-card input#q-input {
            background-color: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .admin-card select {
            background-color: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .admin-card select option {
            background-color: #0F172A !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .admin-card a[href*="admin.penempatan.index"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .admin-card a[href*="admin.penempatan.index"]:hover {
            background: #162238 !important;
            color: #F8FAFC !important;
            border-color: #3B82F6 !important;
        }

        [data-theme="dark"] .admin-card div[style*="background: #ffffff"] {
            background-color: #141E30 !important;
            border-color: #26354D !important;
        }

        [data-theme="dark"] .admin-card h3 {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .admin-table th {
            background-color: #0E1726 !important;
            color: #94A3B8 !important;
            border-bottom-color: #26354D !important;
        }

        [data-theme="dark"] .admin-table td {
            background-color: #182234 !important;
            color: #CBD5E1 !important;
            border-bottom-color: #26354D !important;
        }

        [data-theme="dark"] .admin-table tr:hover td,
        [data-theme="dark"] .admin-table tr:hover {
            background-color: #162238 !important;
        }

        [data-theme="dark"] .admin-table td div[style*="color: var(--neutral-900"] {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .admin-table td div[style*="color: var(--neutral-800"] {
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .admin-table td div[style*="color: var(--neutral-500"] {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .admin-table td a[style*="background: #ffffff"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .admin-table td a[style*="background: #ffffff"]:hover {
            background: #162238 !important;
            border-color: #3B82F6 !important;
            color: #60A5FA !important;
        }

        [data-theme="dark"] .admin-table td span[style*="background: var(--neutral-100"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .btn-table-action-detail {
            background: #1E1B4B !important;
            color: #A5B4FC !important;
            border-color: #3730A3 !important;
        }

        [data-theme="dark"] .btn-table-action-detail:hover {
            background: #2563EB !important;
            border-color: #3B82F6 !important;
            color: #FFFFFF !important;
        }

        [data-theme="dark"] .btn-table-action-surat {
            background: #172554 !important;
            color: #93C5FD !important;
            border-color: #1E40AF !important;
        }

        [data-theme="dark"] .btn-table-action-surat:hover {
            background: #2563EB !important;
            border-color: #3B82F6 !important;
            color: #FFFFFF !important;
        }

        [data-theme="dark"] .action-dropdown-btn {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .action-dropdown-btn:hover,
        [data-theme="dark"] .action-dropdown-btn.active {
            background: #2563EB !important;
            border-color: #3B82F6 !important;
            color: #FFFFFF !important;
        }

        [data-theme="dark"] .action-dropdown-menu {
            background: #182234 !important;
            border-color: #26354D !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4) !important;
        }

        [data-theme="dark"] .action-dropdown-item {
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .action-dropdown-item:hover {
            background: #233249 !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .action-dropdown-item.danger {
            color: #F87171 !important;
        }

        [data-theme="dark"] .action-dropdown-item.danger:hover {
            background: rgba(220, 38, 38, 0.15) !important;
            color: #EF4444 !important;
        }

        [data-theme="dark"] .action-dropdown-divider {
            background-color: #26354D !important;
        }

        [data-theme="dark"] .modal-container {
            background-color: #182234 !important;
            border-color: #26354D !important;
            color: #F8FAFC;
        }

        [data-theme="dark"] .modal-container div[style*="background: #F8FAFC"] {
            background-color: #141E30 !important;
            border-bottom-color: #26354D !important;
        }

        [data-theme="dark"] .modal-container h3 {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .modal-container p {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .modal-container label {
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .modal-container select,
        [data-theme="dark"] .modal-container input,
        [data-theme="dark"] .modal-container textarea {
            background-color: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .modal-container select option {
            background-color: #0F172A !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .modal-container div[style*="background: var(--neutral-100"] {
            background-color: #0F172A !important;
            border: 1px solid #26354D !important;
        }

        [data-theme="dark"] .modal-container div[style*="border-top: 1px solid"] {
            border-top-color: #26354D !important;
        }

        [data-theme="dark"] .modal-container button[style*="background: #ffffff"],
        [data-theme="dark"] .modal-container button[style*="background: var(--neutral-0"] {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] #bulkActionBar {
            background-color: #182234 !important;
            border-color: #26354D !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
        }

        [data-theme="dark"] .penempatan-nav-tabs {
            background: #111C2E !important;
            border-color: #26354D !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.4) !important;
        }

        [data-theme="dark"] #tab-btn-sudah[aria-selected="true"],
        [data-theme="dark"] #tab-btn-belum[aria-selected="true"] {
            background-color: #1E293B !important;
            color: #60A5FA !important;
            border-color: #3B82F6 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4) !important;
        }

        [data-theme="dark"] #tab-btn-sudah[aria-selected="false"],
        [data-theme="dark"] #tab-btn-belum[aria-selected="false"] {
            background-color: transparent !important;
            color: #94A3B8 !important;
            border-color: transparent !important;
        }

        [data-theme="dark"] #tab-badge-sudah,
        [data-theme="dark"] #tab-badge-belum {
            background: #0F172A !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .admin-card input#q-input-belum,
        [data-theme="dark"] #panel-penempatan-belum select {
            background-color: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }
    </style>

    {{-- 0. SLEMAN NAVY HERO BANNER (Khas MagKom) --}}
    <div class="adm-hero-banner">
        <div class="adm-hero-content">
            <div class="adm-hero-text">
                <h1 class="adm-hero-title">Alokasi Penempatan &amp; Pembimbing Magang</h1>
                <div class="adm-hero-subtitle">
                    <span>Penetapan Bidang Dinas &amp; Pembimbing Lapangan</span>
                    <span class="sep">&bull;</span>
                    <span>Dinas Komunikasi dan Informatika Kabupaten Sleman</span>
                </div>
            </div>
            <div class="adm-hero-actions">
                {{-- Unified Export Popover Trigger & Container --}}
                <div class="adm-export-dropdown-wrap">
                    <button type="button" 
                            id="btnToggleExportPopover" 
                            class="adm-btn-export-main adm-hero-btn-secondary" 
                            aria-expanded="false" 
                            aria-haspopup="menu" 
                            aria-controls="admExportPopover"
                            title="Pilih dan unduh rekap data penempatan (Excel, CSV, PDF)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Ekspor Rekap</span>
                        <svg id="admExportCaretSvg" class="adm-export-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    {{-- Floating Dropdown Popover Container (Right-Aligned) --}}
                    <div id="admExportPopover" class="adm-export-popover adm-export-popover-right" style="display: none;" role="menu" aria-label="Menu Pilihan Format Ekspor">
                        <div class="adm-popover-pointer adm-pointer-right"></div>
                        <div class="adm-popover-header">
                            <div>
                                <div class="adm-popover-title">Ekspor Rekap Cepat</div>
                                <div class="adm-popover-desc">Data hasil filter aktif</div>
                            </div>
                            <span class="adm-popover-row-count tabular-nums">
                                {{ number_format($penempatanList->total()) }} Baris
                            </span>
                        </div>

                        <div class="adm-popover-formats" role="radiogroup" aria-label="Format Berkas">
                            {{-- Format 1: Excel (.xlsx) - Rekomendasi --}}
                            <label class="adm-format-card adm-format-selected" id="cardFormatXlsx" onclick="handleAdmExportFormatChange('xlsx')">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_format" id="radioFormatXlsx" value="xlsx" checked class="adm-format-radio" onchange="handleAdmExportFormatChange('xlsx')">
                                    <div class="adm-format-details">
                                        <div class="adm-format-name-wrap">
                                            <span class="adm-format-name">Microsoft Excel</span>
                                            <span class="badge-format-ext badge-ext-xlsx">.xlsx</span>
                                        </div>
                                        <p class="adm-format-desc">Tabel resmi bergaris rapi &amp; filterable</p>
                                    </div>
                                </div>
                                <div class="adm-format-meta">
                                    <span class="adm-badge-recommend">Rekomendasi</span>
                                </div>
                            </label>

                            {{-- Format 2: CSV Tabular (.csv) --}}
                            <label class="adm-format-card" id="cardFormatCsv" onclick="handleAdmExportFormatChange('csv')">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_format" id="radioFormatCsv" value="csv" class="adm-format-radio" onchange="handleAdmExportFormatChange('csv')">
                                    <div class="adm-format-details">
                                        <div class="adm-format-name-wrap">
                                            <span class="adm-format-name">CSV Tabular</span>
                                            <span class="badge-format-ext badge-ext-csv">.csv</span>
                                        </div>
                                        <p class="adm-format-desc">Data mentah teks pemisah koma terstandarisasi</p>
                                    </div>
                                </div>
                                <div class="adm-format-meta">
                                    <span class="adm-badge-quick">Cepat</span>
                                </div>
                            </label>

                            {{-- Format 3: Dokumen Resmi PDF (.pdf) --}}
                            <label class="adm-format-card" id="cardFormatPdf" onclick="handleAdmExportFormatChange('pdf')">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_format" id="radioFormatPdf" value="pdf" class="adm-format-radio" onchange="handleAdmExportFormatChange('pdf')">
                                    <div class="adm-format-details">
                                        <div class="adm-format-name-wrap">
                                            <span class="adm-format-name">Dokumen Resmi (.pdf)</span>
                                            <span class="badge-format-ext badge-ext-pdf">.pdf</span>
                                        </div>
                                        <p class="adm-format-desc">Rekapitulasi cetak ber-kop resmi kedinasan</p>
                                    </div>
                                </div>
                                <div class="adm-format-meta">
                                    <span class="adm-badge-legal">Legal Sah</span>
                                </div>
                            </label>
                        </div>

                        <div class="adm-popover-cta">
                            <button type="button" class="adm-btn-popover-cancel" onclick="toggleAdmExportPopover(false)">
                                Batal
                            </button>
                            <button type="button" id="btnDoExportDownload" class="adm-btn-popover-download" onclick="triggerPenempatanExport()">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span id="btnExportDownloadText">Unduh Excel (.xlsx)</span>
                            </button>
                        </div>
                    </div>
                </div>

                @if(auth()->user()?->isSuperAdmin())
                    <span style="display: inline-flex; align-items: center; gap: 0.4rem; background: rgba(255, 255, 255, 0.15); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; padding: 0.6rem 1rem; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(4px);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        <span>Mode Lihat Saja</span>
                    </span>
                @elseif(isset($slotKhususList) && $slotKhususList->isNotEmpty())
                    <button type="button" onclick="openManualModal()" class="adm-hero-btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Penempatan Jalur Khusus</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ALERT KREDENSIAL AKUN BARU JALUR KHUSUS --}}
    @if(session('akun_baru_dibuat'))
        <div class="admin-card" style="border-top: 3.5px solid #10B981; border-left: none; background: #ECFDF5; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12);">
            <div style="display: flex; align-items: flex-start; gap: 0.85rem;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #D1FAE5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: #065F46; margin: 0;">
                        Akun Peserta Jalur Khusus Berhasil Dibuat &amp; Ditempatkan!
                    </h3>
                    <p style="font-size: 0.8125rem; color: #047857; margin: 0.25rem 0 0.5rem 0;">
                        Berikan kredensial login berikut kepada <strong>{{ session('nama_peserta') }}</strong>. Peserta dapat langsung login dan wajib mengganti kata sandi saat pertama kali masuk.
                    </p>
                    <div style="display: inline-flex; align-items: center; gap: 1rem; background: #ffffff; border: 1px solid #A7F3D0; border-radius: 8px; padding: 0.5rem 0.85rem; font-size: 0.8125rem; color: #1E293B; font-family: monospace;">
                        <span><strong>Email:</strong> {{ session('email_peserta') }}</span>
                        <span style="color: #CBD5E1;">|</span>
                        <span><strong>Password Awal:</strong> <span style="font-weight: 700; color: #0641CC;">{{ session('password_baru') }}</span></span>
                    </div>
                </div>
            </div>
            <button type="button" onclick="navigator.clipboard.writeText('Email: {{ session('email_peserta') }}\nPassword: {{ session('password_baru') }}'); alert('Kredensial login berhasil disalin!');" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1rem; background: #059669; color: #ffffff; border: none; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer; box-shadow: 0 1px 3px rgba(5, 150, 105, 0.25);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                <span>Salin Kredensial</span>
            </button>
        </div>
    @endif

    {{-- 1. HERO STATS CARDS --}}
    <div class="row tile_count" style="margin-bottom: 1.5rem; width: 100%;">
        {{-- Total Penempatan --}}
        <x-gentelella.tile-stat 
            title="Total Penempatan" 
            :value="number_format($penempatanStats['total'] ?? $penempatanList->total())" 
            icon="users"
            color="blue"
            trendText="Total"
            trendDirection="up"
            subText="Semua riwayat peserta" 
            :sparkBars="[35, 50, 45, 65, 70, 85, 95]"
            class="admin-stat-card admin-stat-card--blue"
        />

        {{-- Peserta Aktif --}}
        <x-gentelella.tile-stat 
            title="Peserta Magang Aktif" 
            :value="number_format($penempatanStats['aktif'] ?? 0)" 
            icon="user-check"
            color="teal"
            trendText="Aktif"
            trendDirection="up"
            subText="Sedang masa bimbingan" 
            :sparkBars="[40, 50, 60, 68, 75, 82, 90]"
            class="admin-stat-card admin-stat-card--green"
        />

        {{-- Selesai Magang --}}
        <x-gentelella.tile-stat 
            title="Selesai Magang" 
            :value="number_format($penempatanStats['selesai'] ?? 0)" 
            icon="award"
            color="blue"
            trendText="Selesai"
            trendDirection="up"
            subText="Telah tuntas & dinilai" 
            :sparkBars="[25, 40, 45, 60, 70, 80, 90]"
            class="admin-stat-card admin-stat-card--indigo"
        />

        {{-- Jalur Khusus (Kemitraan/Slot) --}}
        <x-gentelella.tile-stat 
            title="Jalur Khusus Mitra" 
            :value="number_format($penempatanStats['khusus'] ?? 0)" 
            icon="briefcase"
            color="yellow"
            trendText="Mitra"
            trendDirection="up"
            subText="MoU & kuota terproteksi" 
            :sparkBars="[30, 40, 35, 50, 45, 55, 60]"
            class="admin-stat-card admin-stat-card--amber"
        />

        {{-- Menunggu Penempatan (Antrean Lolos Berkas) --}}
        @if(($penempatanStats['belum_ditempatkan'] ?? 0) > 0)
            <x-gentelella.tile-stat 
                title="Menunggu Plotting" 
                :value="number_format($penempatanStats['belum_ditempatkan'] ?? 0)" 
                icon="clock"
                color="yellow"
                trendType="warning"
                trendText="Tahap 1"
                trendDirection="down"
                subText="Lolos berkas pendaftaran" 
                :sparkBars="[50, 60, 55, 70, 65, 80, 75]"
                class="admin-stat-card admin-stat-card--orange"
            />
        @endif
    </div>

    {{-- 2. NAVIGASI DUA TAB UTAMA: SUDAH DITEMPATKAN VS BELUM DITEMPATKAN (COMPACT SEGMENTED PILL BAR) --}}
    <div style="margin-bottom: 1.25rem;">
        <div class="penempatan-nav-tabs" role="tablist" aria-label="Status Penempatan Peserta" style="display: inline-flex; align-items: center; background: #F1F5F9; border: 1px solid #E2E8F0; border-radius: 10px; padding: 4px; gap: 4px; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04);">
            {{-- Tombol 1: Sudah Ditempatkan --}}
            <button type="button" 
                    id="tab-btn-sudah" 
                    role="tab"
                    aria-selected="{{ $activeTab === 'sudah' ? 'true' : 'false' }}"
                    onclick="switchPenempatanTab('sudah')"
                    title="Tampilkan peserta magang yang sudah memiliki pembimbing lapangan"
                    style="display: inline-flex; align-items: center; gap: 0.5rem; height: 38px; padding: 0 1rem; font-size: 0.84375rem; font-weight: {{ $activeTab === 'sudah' ? '700' : '600' }}; border-radius: 7px; border: 1px solid {{ $activeTab === 'sudah' ? 'rgba(226, 232, 240, 0.8)' : 'transparent' }}; background: {{ $activeTab === 'sudah' ? '#FFFFFF' : 'transparent' }}; color: {{ $activeTab === 'sudah' ? '#0641CC' : '#64748B' }}; cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: {{ $activeTab === 'sudah' ? '0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04)' : 'none' }}; white-space: nowrap;">
                <svg id="tab-icon-sudah" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; color: {{ $activeTab === 'sudah' ? '#0641CC' : '#64748B' }};">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <polyline points="16 11 18 13 22 9"></polyline>
                </svg>
                <span id="tab-title-sudah">Sudah Ditempatkan</span>
                <span id="tab-badge-sudah" style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 0.45rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $activeTab === 'sudah' ? '#EFF6FF' : '#E2E8F0' }}; color: {{ $activeTab === 'sudah' ? '#0641CC' : '#475569' }};">
                    {{ number_format($penempatanStats['total'] ?? $penempatanList->total()) }}
                </span>
            </button>

            {{-- Tombol 2: Belum Ditempatkan --}}
            <button type="button" 
                    id="tab-btn-belum" 
                    role="tab"
                    aria-selected="{{ $activeTab === 'belum' ? 'true' : 'false' }}"
                    onclick="switchPenempatanTab('belum')"
                    title="Tampilkan antrean peserta lolos berkas yang menunggu penetapan pembimbing"
                    style="display: inline-flex; align-items: center; gap: 0.5rem; height: 38px; padding: 0 1rem; font-size: 0.84375rem; font-weight: {{ $activeTab === 'belum' ? '700' : '600' }}; border-radius: 7px; border: 1px solid {{ $activeTab === 'belum' ? 'rgba(226, 232, 240, 0.8)' : 'transparent' }}; background: {{ $activeTab === 'belum' ? '#FFFFFF' : 'transparent' }}; color: {{ $activeTab === 'belum' ? '#B45309' : '#64748B' }}; cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: {{ $activeTab === 'belum' ? '0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04)' : 'none' }}; white-space: nowrap;">
                <svg id="tab-icon-belum" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; color: {{ $activeTab === 'belum' ? '#D97706' : '#64748B' }};">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span id="tab-title-belum">Belum Ditempatkan</span>
                <span id="tab-badge-belum" style="display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; padding: 0 0.45rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ ($penempatanStats['belum_ditempatkan'] ?? 0) > 0 ? '#FEF3C7' : '#E2E8F0' }}; color: {{ ($penempatanStats['belum_ditempatkan'] ?? 0) > 0 ? '#B45309' : '#475569' }};">
                    {{ number_format($penempatanStats['belum_ditempatkan'] ?? 0) }}
                </span>
            </button>
        </div>
    </div>

    {{-- PANEL 1: DATA PESERTA SUDAH DITEMPATKAN --}}
    <div id="panel-penempatan-sudah" style="{{ $activeTab === 'belum' ? 'display: none;' : '' }}">
        {{-- Filter Bar Card --}}
        <div class="admin-card" style="padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
            <form method="GET" action="{{ route('admin.penempatan.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                <input type="hidden" name="tab" value="sudah">
                {{-- Search Bar --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 2; min-width: 240px;">
                    <label for="q-input" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Cari Nama</label>
                    <div style="position: relative;">
                        <input type="text" id="q-input" name="q" value="{{ request('tab') !== 'belum' ? request('q') : '' }}" placeholder="Cari nama..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem 0 2.4rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-900, #0F172A);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 13px; color: var(--neutral-400, #94A3B8);">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                </div>

                {{-- Filter Bulan Magang --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Bulan Magang</label>
                    <select name="bulan_magang_id" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Bulan Magang</option>
                        @foreach($bulanList as $bln)
                            <option value="{{ $bln->id }}" {{ (request('tab') !== 'belum' && request('bulan_magang_id') == $bln->id) ? 'selected' : '' }}>
                                {{ $bln->nama_bulan ? $bln->nama_bulan . ' (' . $bln->bulan_mulai?->translatedFormat('F Y') . ')' : $bln->bulan_mulai?->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Status --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Status Penempatan</label>
                    <select name="status" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Status</option>
                        @foreach($statusEnum as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Pembimbing --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 200px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Pembimbing Lapangan</label>
                    <select name="pembimbing_id" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Pembimbing</option>
                        @foreach($pembimbingList as $pemb)
                            <option value="{{ $pemb->id }}" {{ request('pembimbing_id') == $pemb->id ? 'selected' : '' }}>
                                {{ $pemb->nama }} [{{ $pemb->bidang?->nama_bidang ?? 'Dinas' }}]
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Bidang --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 180px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Bidang Dinas</label>
                    <select name="bidang_id" style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Bidang</option>
                        @foreach($bidangList as $bdg)
                            <option value="{{ $bdg->id }}" {{ request('bidang_id') == $bdg->id ? 'selected' : '' }}>
                                {{ $bdg->nama_bidang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <button type="submit" style="height: 42px; padding: 0 1.25rem; background: #0641CC; color: #FFFFFF; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s ease; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                        Terapkan
                    </button>
                    @if((request('tab') === 'sudah' || !request('tab')) && (request()->filled('q') || request()->filled('status') || request()->filled('pembimbing_id') || request()->filled('bidang_id') || request()->filled('bulan_magang_id')))
                        <a href="{{ route('admin.penempatan.index', ['tab' => 'sudah']) }}" style="height: 42px; padding: 0 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; border: 1px solid var(--neutral-200, #E2E8F0);" title="Kembalikan pencarian ke kondisi awal">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- 3. DATA TABLE CARD --}}
        <x-gentelella.x-panel title="Data Penempatan Peserta">
            @if($penempatanList->isEmpty())
                <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600, #64748B);">
                    <div style="width: 64px; height: 64px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: #1D4ED8;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin-bottom: 0.375rem;">
                        Belum Ada Data Penempatan
                    </h4>
                    <p style="font-size: 0.875rem; max-width: 460px; margin: 0 auto;">Belum ada peserta magang yang sesuai dengan kriteria filter yang dipilih.</p>
                </div>
            @else
                <div class="adm-table-container">
                    <div style="overflow-x: auto; width: 100%;">
                        <table class="adm-table admin-table bulk_action">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">
                                    <input type="checkbox" id="checkAllPenempatan" onclick="toggleAllPenempatan(this)" style="cursor: pointer; width: 16px; height: 16px;">
                                </th>
                                <th>Peserta Magang</th>
                                <th>Pembimbing Lapangan</th>
                                <th>Jalur / Bidang Dinas</th>
                                <th>Bulan Magang</th>
                                <th>Status</th>
                                <th style="text-align: right; width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penempatanList as $p)
                                @php
                                    $statusVal = is_object($p->status) ? $p->status->value : $p->status;
                                    $statusObj = is_object($p->status) ? $p->status : \App\Enums\StatusPenempatanEnum::tryFrom($p->status);
                                    $badgeStyle = match($statusVal) {
                                        'aktif' => ['bg' => '#D1FAE5', 'color' => '#065F46', 'border' => '#A7F3D0'],
                                        'selesai' => ['bg' => '#EFF6FF', 'color' => '#1D4ED8', 'border' => '#DBEAFE'],
                                        'mengundurkan_diri' => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1'],
                                        'nonaktif' => ['bg' => '#FEE2E2', 'color' => '#991B1B', 'border' => '#FECACA'],
                                        default => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1'],
                                    };
                                @endphp
                                <tr>
                                    <td style="text-align: center;">
                                        <input type="checkbox" value="{{ $p->id }}" class="penempatan-checkbox" onchange="updateBulkUI()" style="cursor: pointer; width: 16px; height: 16px;">
                                    </td>
                                    {{-- Peserta Magang --}}
                                    <td>
                                        @php
                                            $cleanPesertaNama = str_replace(' (Peserta Magang)', '', (string) ($p->peserta?->nama ?? '-'));
                                        @endphp
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #0641CC 0%, #2563EB 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; flex-shrink: 0; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                                                {{ strtoupper(substr($cleanPesertaNama, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.875rem;">
                                                    {{ $cleanPesertaNama }}
                                                </div>
                                                <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin-top: 0.1rem;">
                                                    {{ $p->peserta?->profil?->institusi ?? '-' }}
                                                </div>
                                                <small style="font-size: 0.7rem; color: var(--neutral-400, #94A3B8);">
                                                    NIM: {{ $p->peserta?->profil?->nim_nisn ?? '-' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Pembimbing Lapangan --}}
                                    <td>
                                        @php
                                            $cleanPembimbingNama = str_replace(' (Pembimbing Lapangan)', '', (string) ($p->pembimbing?->nama ?? 'Belum Ditentukan'));
                                        @endphp
                                        <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.875rem;">
                                            {{ $cleanPembimbingNama }}
                                        </div>
                                        @if($p->pembimbing)
                                            <div style="display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 0.35rem; flex-wrap: wrap;">
                                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.725rem; color: var(--neutral-600, #475569); background: var(--neutral-100, #F1F5F9); padding: 0.15rem 0.5rem; border-radius: 6px; border: 1px solid var(--neutral-200, #E2E8F0); font-weight: 600;">
                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                                                    <span>{{ $p->pembimbing->mentee_aktif_count ?? 1 }} Mentee Aktif</span>
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Jalur & Bidang Dinas --}}
                                    <td>
                                        <div style="font-weight: 600; color: var(--neutral-900, #0F172A);">
                                            {{ $p->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }}
                                        </div>
                                        <div style="margin-top: 0.25rem;">
                                            @if($p->slotKhusus)
                                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-500, #64748B);" title="{{ $p->slotKhusus->nama_mitra ? 'Mitra: '.$p->slotKhusus->nama_mitra : 'Jalur Khusus' }}">
                                                    Khusus
                                                </span>
                                            @else
                                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-500, #64748B);">
                                                    Umum
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Periode Magang --}}
                                    <td style="font-size: 0.8125rem;">
                                        <div style="font-weight: 700; color: var(--neutral-800, #1E293B);">
                                            {{ \Carbon\Carbon::parse($p->tgl_mulai)->translatedFormat('d M Y') }} s/d
                                        </div>
                                        <div style="font-weight: 700; color: var(--neutral-800, #1E293B);">
                                            {{ \Carbon\Carbon::parse($p->tgl_selesai)->translatedFormat('d M Y') }}
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; border: 1px solid {{ $badgeStyle['border'] }};">
                                            {{ $statusObj ? $statusObj->label() : ucfirst($statusVal) }}
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td style="text-align: right;">
                                        <div style="display: inline-flex; gap: 0.35rem; align-items: center; justify-content: flex-end;">
                                            @if($p->pembimbing && !auth()->user()?->isSuperAdmin())
                                                <button type="button" 
                                                        class="action-dropdown-btn" 
                                                        onclick="openPindahModal({{ $p->id }}, '{{ addslashes($cleanPesertaNama) }}', {{ $p->pembimbing_id ?? 'null' }})" 
                                                        title="Ganti Pembimbing Lapangan"
                                                        aria-label="Ganti Pembimbing Lapangan">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                                        <circle cx="9" cy="7" r="4"></circle>
                                                        <polyline points="16 11 18 13 22 9"></polyline>
                                                    </svg>
                                                </button>
                                            @elseif(auth()->user()?->isSuperAdmin())
                                                <span style="font-size: 0.75rem; color: #94A3B8; font-style: italic;">Read-Only</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                    <div class="adm-table-footer">
                        <x-gentelella.pagination-bar 
                            :paginator="$penempatanList" 
                            :perPageOptions="[5, 10, 25]" 
                            entity="peserta magang" 
                            badgeText="Penempatan Aktif" 
                        />
                    </div>
                </div>
            @endif
        </x-gentelella.x-panel>
    </div>

    {{-- PANEL 2: DATA PESERTA BELUM DITEMPATKAN (ANTREAN LOLOS BERKAS) --}}
    <div id="panel-penempatan-belum" style="{{ $activeTab === 'belum' ? '' : 'display: none;' }}">
        {{-- Filter Bar Khusus Antrean Belum Ditempatkan --}}
        <div class="admin-card" style="padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
            <form method="GET" action="{{ route('admin.penempatan.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                <input type="hidden" name="tab" value="belum">
                
                {{-- 1. Search Bar --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 2; min-width: 240px;">
                    <label for="q-input-belum" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Cari Nama</label>
                    <div style="position: relative;">
                        <input type="text" id="q-input-belum" name="q" value="{{ request('tab') === 'belum' ? request('q') : '' }}" placeholder="Cari nama..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem 0 2.4rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-900, #0F172A);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 13px; color: var(--neutral-400, #94A3B8);">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                </div>

                {{-- 2. Filter Bulan Magang --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1.2; min-width: 180px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Bulan Magang</label>
                    <select name="bulan_magang_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Bulan Magang</option>
                        @foreach($bulanList as $bln)
                            <option value="{{ $bln->id }}" {{ (request('tab') === 'belum' && request('bulan_magang_id') == $bln->id) ? 'selected' : '' }}>
                                {{ $bln->nama_bulan ? $bln->nama_bulan . ' (' . $bln->bulan_mulai?->translatedFormat('F Y') . ')' : $bln->bulan_mulai?->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Filter Jurusan --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1.2; min-width: 180px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Jurusan</label>
                    <select name="jurusan_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusanList as $jrs)
                            <option value="{{ $jrs->id }}" {{ (request('tab') === 'belum' && request('jurusan_id') == $jrs->id) ? 'selected' : '' }}>
                                {{ $jrs->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. Filter Rekomendasi Bidang Dinas --}}
                <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1.2; min-width: 180px;">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Bidang Rekomendasi</label>
                    <select name="bidang_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: var(--neutral-800, #1E293B);">
                        <option value="">Semua Bidang</option>
                        @foreach($bidangList as $bdg)
                            <option value="{{ $bdg->id }}" {{ (request('tab') === 'belum' && request('bidang_id') == $bdg->id) ? 'selected' : '' }}>
                                {{ $bdg->nama_bidang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 5. Tombol Aksi --}}
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <button type="submit" style="height: 42px; padding: 0 1.25rem; background: #0641CC; color: #FFFFFF; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s ease; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                        Terapkan
                    </button>
                    @if(request('tab') === 'belum' && (request()->filled('q') || request()->filled('bulan_magang_id') || request()->filled('jurusan_id') || request()->filled('bidang_id')))
                        <a href="{{ route('admin.penempatan.index', ['tab' => 'belum']) }}" style="height: 42px; padding: 0 1rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; border: 1px solid var(--neutral-200, #E2E8F0);" title="Kembalikan pencarian ke kondisi awal">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="admin-card" style="border-top: 3px solid #01347C; border-left: none; background: #FFFFFF;">
            <div style="padding: 1.15rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #FFFFFF; flex-wrap: wrap; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; flex-shrink: 0;" title="ID Card Tanda Pengenal Peserta Magang Dinas">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="14" x="3" y="7" rx="2"></rect>
                            <path d="M9 5h6"></path>
                            <path d="M12 2v3"></path>
                            <circle cx="12" cy="13" r="2.5"></circle>
                            <path d="M8 18h8"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">
                            Antrean Peserta Lolos Berkas (Menunggu Penempatan)
                        </h3>
                        <p style="font-size: 0.8125rem; color: var(--neutral-500, #64748B); margin: 0.2rem 0 0 0;">
                            Peserta di bawah ini telah disetujui berkasnya pada Tahap 1 dan siap ditetapkan Pembimbing Lapangan serta Bidang Dinas.
                        </p>
                    </div>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr style="background: var(--neutral-50, #F8FAFC); border-bottom: 1px solid var(--neutral-200, #E2E8F0);">
                            <th style="color: var(--neutral-600, #475569); font-weight: 700;">Peserta Magang</th>
                            <th style="color: var(--neutral-600, #475569); font-weight: 700;">Jurusan</th>
                            <th style="color: var(--neutral-600, #475569); font-weight: 700;">Bulan Magang</th>
                            <th style="color: var(--neutral-600, #475569); font-weight: 700;">Diterima Pada</th>
                            <th style="text-align: right; width: 150px; color: var(--neutral-600, #475569); font-weight: 700;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($antreanBelumDitempatkan as $antrean)
                            @php
                                $firstBidang = $antrean->kuota?->jurusan?->bidang?->first();
                                $defBidangId = $firstBidang?->id;
                                $defBidangNama = $firstBidang?->nama_bidang ?? 'Dinas Kominfo';
                            @endphp
                            <tr style="background: #FFFFFF;">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                                        <div style="width: 34px; height: 34px; border-radius: 8px; background: linear-gradient(135deg, #01347C 0%, #0641CC 100%); color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                                            {{ strtoupper(substr($antrean->user?->nama ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #1E293B; font-size: 0.875rem;">
                                                {{ $antrean->user?->nama }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: #64748B;">
                                                NIM: {{ $antrean->user?->profil?->nim_nisn ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1E293B;">
                                        {{ $antrean->kuota?->jurusan?->nama_jurusan ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #64748B;">
                                        {{ $antrean->user?->profil?->institusi ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: #0641CC; font-size: 0.8125rem;">
                                        {{ $antrean->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                    </span>
                                    <div style="font-size: 0.75rem; color: #64748B;">
                                        Durasi: {{ $antrean->durasi_bulan }} Bulan
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 0.8125rem; color: #475569;">
                                        {{ $antrean->diputuskan_at?->translatedFormat('d M Y H:i') ?? '-' }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    @if(auth()->user()?->isSuperAdmin())
                                        <span style="font-size: 0.75rem; color: #94A3B8; font-weight: 500;">Menunggu Penempatan</span>
                                    @else
                                        <button type="button" 
                                                onclick="openTetapkanModal({{ $antrean->id }}, '{{ addslashes($antrean->user?->nama) }}', '{{ addslashes($antrean->kuota?->jurusan?->nama_jurusan ?? '') }}', {{ $defBidangId ?? 'null' }}, '{{ addslashes($defBidangNama) }}')" 
                                                style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 0.95rem; background: #0641CC; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25); transition: background 0.15s ease;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                                            <span>Tempatkan</span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 3.5rem 1.5rem; background: #FFFFFF;">
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem;">
                                        <div style="width: 48px; height: 48px; border-radius: 50%; background: #F8FAFC; display: flex; align-items: center; justify-content: center; color: #94A3B8; margin-bottom: 0.25rem; border: 1px solid #E2E8F0;">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                        </div>
                                        <span style="font-weight: 700; font-size: 0.9375rem; color: #334155;">
                                            Tidak ada antrean peserta belum ditempatkan
                                        </span>
                                        <span style="font-size: 0.8125rem; color: #64748B; max-width: 480px; line-height: 1.5;">
                                            Seluruh calon peserta lolos berkas telah ditetapkan pembimbing dan bidang dinas.
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- MODAL PENEMPATAN MANUAL (JALUR KHUSUS) --}}
<div id="modal-penempatan-manual" class="modal-overlay">
    <div class="modal-container" style="max-width: 620px; max-height: 90vh; overflow-y: auto;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Penempatan Peserta Jalur Khusus</h3>
                <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 0.15rem 0 0 0;">
                    Alokasi peserta magang berdasarkan kuota kemitraan slot khusus dinas (AC-07-3)
                </p>
            </div>
            <button type="button" onclick="closeManualModal()" style="background: transparent; border: none; font-size: 1.25rem; color: var(--neutral-400, #94A3B8); cursor: pointer;">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.penempatan.manual') }}" id="form-penempatan-manual" style="padding: 1.5rem;">
            @csrf

            @if($errors->any() && (old('mode') || $errors->has('nama') || $errors->has('email') || $errors->has('nim_nisn') || $errors->has('slot_khusus_id') || $errors->has('peserta_id')))
                <div style="background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.8125rem; color: #991B1B;">
                    <div style="font-weight: 700; margin-bottom: 0.25rem;">Gagal menyimpan penempatan:</div>
                    <ul style="margin: 0 0 0 1.25rem; padding: 0;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Switcher Mode Peserta --}}
            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; background: var(--neutral-100, #F1F5F9); padding: 0.35rem; border-radius: 8px;">
                <button type="button" id="btn-mode-terdaftar" onclick="switchPesertaMode('terdaftar')" style="flex: 1; padding: 0.5rem; font-size: 0.8125rem; font-weight: 700; border: none; border-radius: 6px; cursor: pointer; background: #0641CC; color: #FFFFFF;">
                    Peserta Terdaftar
                </button>
                <button type="button" id="btn-mode-baru" onclick="switchPesertaMode('baru')" style="flex: 1; padding: 0.5rem; font-size: 0.8125rem; font-weight: 700; border: none; border-radius: 6px; cursor: pointer; background: transparent; color: var(--neutral-700, #334155);">
                    + Daftarkan Peserta Baru
                </button>
            </div>
            <input type="hidden" name="mode" id="input-mode-peserta" value="terdaftar">
            <input type="hidden" name="bulan_mulai" id="input-bulan-mulai" value="{{ date('Y-m-01') }}">

            {{-- Mode 1: Peserta Terdaftar --}}
            <div id="panel-peserta-terdaftar" style="margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B); display: block; margin-bottom: 0.375rem;">
                    Pilih Calon Peserta Terdaftar <span style="color: #DC2626;">*</span>
                </label>
                <select name="peserta_id" id="select-peserta-id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">-- Pilih Peserta Aktif --</option>
                    @foreach($pesertaList ?? [] as $pes)
                        <option value="{{ $pes->id }}">{{ $pes->nama }} ({{ $pes->email }}) &bull; {{ $pes->profil?->institusi ?? '-' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mode 2: Daftarkan Peserta Baru --}}
            <div id="panel-peserta-baru" style="display: none; flex-direction: column; gap: 0.875rem; margin-bottom: 1.25rem; background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 8px; padding: 1rem;">
                <div style="font-size: 0.8125rem; font-weight: 700; color: #1E40AF;">
                    Identitas Peserta Baru (Akun akan dibuat otomatis)
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Nama Lengkap <span style="color: #DC2626;">*</span></label>
                        <input type="text" name="nama" id="input-nama-baru" value="{{ old('nama') }}" placeholder="Nama siswa/mahasiswa" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Email Aktif <span style="color: #DC2626;">*</span></label>
                        <input type="email" name="email" id="input-email-baru" value="{{ old('email') }}" placeholder="peserta.magang@gmail.com" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">NIM / NISN <span style="color: #DC2626;">*</span></label>
                        <input type="text" name="nim_nisn" id="input-nim-baru" value="{{ old('nim_nisn') }}" placeholder="Nomor induk siswa/mhs" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Jenjang Pendidikan <span style="color: #DC2626;">*</span></label>
                        <select name="jenjang" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                            <option value="s1" {{ old('jenjang') === 's1' ? 'selected' : '' }}>S1 (Strata 1)</option>
                            <option value="d3" {{ old('jenjang') === 'd3' ? 'selected' : '' }}>D3 (Diploma 3)</option>
                            <option value="smk" {{ old('jenjang') === 'smk' ? 'selected' : '' }}>SMK</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Asal Kampus / Sekolah <span style="color: #DC2626;">*</span></label>
                        <input type="text" name="institusi" id="input-institusi-baru" value="{{ old('institusi') }}" placeholder="Nama Universitas / SMK" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Jurusan Asal <span style="color: #DC2626;">*</span></label>
                        <select name="jurusan_id" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                            <option value="">-- Pilih Jurusan Magang --</option>
                            @foreach($jurusanList ?? [] as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }} ({{ strtoupper(is_object($j->kategori) ? $j->kategori->value : (string)$j->kategori) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-800, #1E293B);">Kata Sandi Awal (Default: Magkom2026!)</label>
                    <input type="text" name="password" id="input-password-baru" value="{{ old('password', 'Magkom2026!') }}" placeholder="Magkom2026!" style="width: 100%; height: 38px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; background: #ffffff;">
                    <small style="font-size: 0.7rem; color: var(--neutral-500, #64748B);">Peserta wajib mengganti kata sandi ini saat pertama kali login.</small>
                </div>
            </div>

            {{-- Pilihan Slot Khusus --}}
            <div id="wrapper-slot-khusus" style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                    Pilih Slot Kemitraan Khusus <span style="color: #DC2626;">*</span>
                </label>
                <select name="slot_khusus_id" id="select-slot-khusus" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Pilih Slot Kemitraan Khusus --</option>
                    @foreach($slotKhususList as $sk)
                        <option value="{{ $sk->id }}" {{ old('slot_khusus_id') == $sk->id ? 'selected' : '' }}>
                            {{ $sk->nama_mitra }} &bull; Bulan: {{ $sk->bulan_mulai?->translatedFormat('F Y') }} (Sisa: {{ $sk->jumlah - $sk->terisi }} Kursi)
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Pembimbing & Bidang --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Pembimbing Lapangan <span style="color: #DC2626;">*</span></label>
                    <select name="pembimbing_id" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <option value="">-- Pilih Pembimbing --</option>
                        @foreach($pembimbingList as $p)
                            <option value="{{ $p->id }}" {{ old('pembimbing_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }} [{{ $p->bidang?->nama_bidang ?? 'Dinas' }}] ({{ $p->mentee_aktif_count ?? 0 }} Mentee)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Bidang Dinas</label>
                    <select name="bidang_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <option value="">-- Sesuai Pembimbing --</option>
                        @foreach($bidangList as $b)
                            <option value="{{ $b->id }}" {{ old('bidang_id') == $b->id ? 'selected' : '' }}>{{ $b->nama_bidang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tanggal Mulai, Durasi & Tanggal Selesai --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Tanggal Mulai <span style="color: #DC2626;">*</span></label>
                    <input type="date" name="tgl_mulai" id="input-tgl-mulai" required value="{{ old('tgl_mulai', date('Y-m-d')) }}" onchange="syncManualDates()" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Durasi Magang <span style="color: #DC2626;">*</span></label>
                    <select name="durasi_bulan" id="select-durasi-bulan" onchange="syncManualDates()" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                        <option value="1" {{ old('durasi_bulan') == 1 ? 'selected' : '' }}>1 Bulan</option>
                        <option value="2" {{ old('durasi_bulan') == 2 ? 'selected' : '' }}>2 Bulan</option>
                        <option value="3" {{ old('durasi_bulan', 3) == 3 ? 'selected' : '' }}>3 Bulan (Standar)</option>
                        <option value="4" {{ old('durasi_bulan') == 4 ? 'selected' : '' }}>4 Bulan</option>
                        <option value="5" {{ old('durasi_bulan') == 5 ? 'selected' : '' }}>5 Bulan</option>
                        <option value="6" {{ old('durasi_bulan') == 6 ? 'selected' : '' }}>6 Bulan</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Tanggal Selesai <span style="color: #DC2626;">*</span></label>
                    <input type="date" name="tgl_selesai" id="input-tgl-selesai" required value="{{ old('tgl_selesai', date('Y-m-d', strtotime('+3 months'))) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1.25rem;">
                <button type="button" onclick="closeManualModal()" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
                    Tempatkan Peserta
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL PINDAH PEMBIMBING --}}
<div id="modal-pindah-pembimbing" class="modal-overlay">
    <div class="modal-container">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Mutasi / Ganti Pembimbing</h3>
                <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 0.15rem 0 0 0;" id="pindah-peserta-nama">
                    Peserta: -
                </p>
            </div>
            <button type="button" onclick="closePindahModal()" style="background: transparent; border: none; font-size: 1.25rem; color: var(--neutral-400, #94A3B8); cursor: pointer;">✕</button>
        </div>

        <form method="POST" id="form-pindah-pembimbing" action="" style="padding: 1.5rem;">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Pilih Pembimbing Baru <span style="color: #DC2626;">*</span></label>
                <select name="pembimbing_id" id="select-pembimbing-baru" required style="height: 42px; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    @foreach($pembimbingList as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }} [{{ $p->bidang?->nama_bidang ?? 'Dinas' }}] (Beban: {{ $p->mentee_aktif_count ?? 0 }} Mentee Aktif)</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1.25rem;">
                <button type="button" onclick="closePindahModal()" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL BATALKAN PENEMPATAN --}}
<div id="modal-batal-penempatan" class="modal-overlay">
    <div class="modal-container">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: center; background: #FEF2F2;">
            <div>
                <h3 style="font-size: 1.0625rem; font-weight: 800; color: #991B1B; margin: 0;">Pembatalan / Pengunduran Diri</h3>
                <p style="font-size: 0.75rem; color: #B91C1C; margin: 0.15rem 0 0 0;" id="batal-peserta-nama"></p>
            </div>
            <button type="button" onclick="closeBatalModal()" style="background: transparent; border: none; font-size: 1.25rem; color: #B91C1C; cursor: pointer;">✕</button>
        </div>

        <form method="POST" id="form-batal-penempatan" action="" style="padding: 1.5rem;">
            @csrf
            <div style="background: #FFFBEB; border: 1px solid #FCD34D; border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; font-size: 0.75rem; color: #92400E; line-height: 1.4;">
                <strong>Ketentuan Pembatalan & Pengunduran Diri (BR-07):</strong>
                <ul style="margin: 0.25rem 0 0 1rem; padding: 0;">
                    <li><strong>Sebelum tanggal mulai:</strong> Kuota jurusan/mitra akan otomatis dikembalikan ke sistem.</li>
                    <li><strong>Sejak/setelah tanggal mulai:</strong> Kuota hangus (tidak dikembalikan) dan akun peserta otomatis dinonaktifkan.</li>
                </ul>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.5rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800, #1E293B);">Alasan Pembatalan / Pengunduran Diri <span style="color: #DC2626;">*</span></label>
                <textarea name="alasan" required rows="3" placeholder="Sebutkan alasan pembatalan atau pengunduran diri secara detail..." style="border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit; color: var(--neutral-900, #0F172A);"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1.25rem;">
                <button type="button" onclick="closeBatalModal()" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid var(--neutral-300, #CBD5E1); color: var(--neutral-700, #334155); border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Kembali
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #DC2626; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Konfirmasi Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL IN-APP PRATINJAU SURAT DITERIMA --}}
<div id="modal-surat-preview" onclick="if(event.target === this) closeSuratPreview()" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(6px); z-index: 9999; align-items: center; justify-content: center; padding: 1.25rem;">
    <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 960px; max-height: 92vh; display: flex; flex-direction: column; padding: 1.25rem 1.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden; border: 1px solid var(--neutral-200, #E2E8F0);">
        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #D1FAE5; color: #065F46;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <div>
                    <h3 id="surat-modal-title" style="font-size: 1rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">Surat Keterangan Diterima Magang</h3>
                    <p style="font-size: 0.75rem; color: var(--neutral-500, #64748B); margin: 2px 0 0 0;">Dokumen Resmi Dinas Kominfo Kabupaten Sleman</p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <a id="surat-modal-download" href="#" style="display: inline-flex; align-items: center; gap: 0.375rem; background: #0641CC; color: #FFFFFF; padding: 0.45rem 0.95rem; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; text-decoration: none; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);">
                    Unduh PDF
                </a>
                <button type="button" onclick="closeSuratPreview()" style="background: none; border: none; font-size: 1.5rem; line-height: 1; cursor: pointer; color: var(--neutral-400, #94A3B8); padding: 0 0.25rem;">&times;</button>
            </div>
        </div>

        <div style="flex: 1; min-height: 60vh; max-height: 75vh; position: relative; background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; margin-top: 0.875rem; overflow: hidden;">
            <iframe id="surat-iframe" src="about:blank" style="width: 100%; height: 75vh; border: none;"></iframe>
        </div>
    </div>
</div>

{{-- MODAL PENETAPAN PEMBIMBING & BIDANG (TAHAP 2) --}}
<div id="modal-tetapkan-penempatan" class="modal-overlay">
    <div class="modal-container" style="max-width: 480px; border-radius: 14px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); display: flex; justify-content: space-between; align-items: flex-start; background: #FFFFFF;">
            <div>
                <h3 style="font-size: 1.125rem; font-weight: 800; color: #0F172A; margin: 0; letter-spacing: -0.01em;">Penetapan Penempatan</h3>
                <p style="font-size: 0.8125rem; color: #64748B; margin: 0.25rem 0 0 0; font-weight: 500;" id="tetapkan-peserta-info">
                    Peserta: -
                </p>
            </div>
            <button type="button" onclick="closeTetapkanModal()" style="background: transparent; border: none; font-size: 1.25rem; color: #94A3B8; cursor: pointer; padding: 0.25rem; line-height: 1;" title="Tutup">✕</button>
        </div>

        <form method="POST" id="form-tetapkan-penempatan" action="{{ route('admin.penempatan.tetapkan') }}" style="padding: 1.5rem;">
            @csrf
            <input type="hidden" name="pendaftaran_id" id="tetapkan-pendaftaran-id" value="">
            <input type="hidden" name="bidang_id" id="tetapkan-bidang-id" value="">

            {{-- Bidang Dinas --}}
            <div style="display: flex; flex-direction: column; gap: 0.35rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.04em;">Bidang Dinas</label>
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <path d="M3 9h18"></path>
                            <path d="M9 21V9"></path>
                        </svg>
                    </div>
                    <div id="tetapkan-bidang-display" style="font-weight: 700; font-size: 0.875rem; color: #0F172A; line-height: 1.35;">
                        Dinas Komunikasi dan Informatika
                    </div>
                </div>
            </div>

            {{-- Pembimbing Lapangan --}}
            <div style="display: flex; flex-direction: column; gap: 0.35rem; margin-bottom: 1.5rem;">
                <label for="select-tetapkan-pembimbing" style="font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.04em;">Pembimbing Lapangan <span style="color: #DC2626;">*</span></label>
                <select name="pembimbing_id" id="select-tetapkan-pembimbing" required style="height: 42px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #FFFFFF; color: #0F172A; width: 100%;">
                    <option value="">-- Pilih Pembimbing Dinas --</option>
                    @foreach($pembimbingList as $p)
                        <option value="{{ $p->id }}" data-bidang-id="{{ $p->bidang_id ?? '' }}">
                            {{ $p->nama }} [{{ $p->bidang?->nama_bidang ?? 'Dinas Kominfo' }}] ({{ $p->mentee_aktif_count ?? 0 }} Mentee Aktif)
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.65rem; border-top: 1px solid #E2E8F0; padding-top: 1.25rem;">
                <button type="button" onclick="closeTetapkanModal()" style="padding: 0.55rem 1.15rem; background: #FFFFFF; border: 1px solid #CBD5E1; color: #475569; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; cursor: pointer; transition: all 0.15s ease;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.55rem 1.35rem; background: #0641CC; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25); transition: background 0.15s ease;">
                    Tetapkan Pembimbing
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchPenempatanTab(tabName) {
        const sudahPanel = document.getElementById('panel-penempatan-sudah');
        const belumPanel = document.getElementById('panel-penempatan-belum');
        const btnSudah = document.getElementById('tab-btn-sudah');
        const btnBelum = document.getElementById('tab-btn-belum');
        const iconSudah = document.getElementById('tab-icon-sudah');
        const iconBelum = document.getElementById('tab-icon-belum');
        const badgeSudah = document.getElementById('tab-badge-sudah');
        const badgeBelum = document.getElementById('tab-badge-belum');

        if (tabName === 'belum') {
            if (sudahPanel) sudahPanel.style.display = 'none';
            if (belumPanel) belumPanel.style.display = 'block';

            if (btnSudah) {
                btnSudah.style.background = 'transparent';
                btnSudah.style.color = '#64748B';
                btnSudah.style.fontWeight = '600';
                btnSudah.style.boxShadow = 'none';
                btnSudah.style.borderColor = 'transparent';
                btnSudah.setAttribute('aria-selected', 'false');
            }
            if (iconSudah) iconSudah.style.color = '#64748B';
            if (badgeSudah) {
                badgeSudah.style.background = '#E2E8F0';
                badgeSudah.style.color = '#475569';
            }

            if (btnBelum) {
                btnBelum.style.background = '#FFFFFF';
                btnBelum.style.color = '#B45309';
                btnBelum.style.fontWeight = '700';
                btnBelum.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04)';
                btnBelum.style.borderColor = 'rgba(226, 232, 240, 0.8)';
                btnBelum.setAttribute('aria-selected', 'true');
            }
            if (iconBelum) iconBelum.style.color = '#D97706';
            if (badgeBelum) {
                badgeBelum.style.background = '#FEF3C7';
                badgeBelum.style.color = '#B45309';
            }
        } else {
            if (sudahPanel) sudahPanel.style.display = 'block';
            if (belumPanel) belumPanel.style.display = 'none';

            if (btnSudah) {
                btnSudah.style.background = '#FFFFFF';
                btnSudah.style.color = '#0641CC';
                btnSudah.style.fontWeight = '700';
                btnSudah.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04)';
                btnSudah.style.borderColor = 'rgba(226, 232, 240, 0.8)';
                btnSudah.setAttribute('aria-selected', 'true');
            }
            if (iconSudah) iconSudah.style.color = '#0641CC';
            if (badgeSudah) {
                badgeSudah.style.background = '#EFF6FF';
                badgeSudah.style.color = '#0641CC';
            }

            if (btnBelum) {
                btnBelum.style.background = 'transparent';
                btnBelum.style.color = '#64748B';
                btnBelum.style.fontWeight = '600';
                btnBelum.style.boxShadow = 'none';
                btnBelum.style.borderColor = 'transparent';
                btnBelum.setAttribute('aria-selected', 'false');
            }
            if (iconBelum) iconBelum.style.color = '#64748B';
            if (badgeBelum) {
                badgeBelum.style.background = '#E2E8F0';
                badgeBelum.style.color = '#475569';
            }
        }

        try {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url.toString());
        } catch (e) {
            // fallback
        }
    }

    function openTetapkanModal(pendaftaranId, namaPeserta, namaJurusan, defaultBidangId, defaultBidangNama) {
        const modal = document.getElementById('modal-tetapkan-penempatan');
        const infoEl = document.getElementById('tetapkan-peserta-info');
        const idInput = document.getElementById('tetapkan-pendaftaran-id');
        const bidangInput = document.getElementById('tetapkan-bidang-id');
        const bidangDisplay = document.getElementById('tetapkan-bidang-display');

        if (infoEl) infoEl.innerText = 'Peserta: ' + namaPeserta + ' (' + namaJurusan + ')';
        if (idInput) idInput.value = pendaftaranId;
        if (bidangInput && defaultBidangId) bidangInput.value = defaultBidangId;
        if (bidangDisplay) bidangDisplay.innerText = defaultBidangNama || 'Dinas Komunikasi dan Informatika';

        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeTetapkanModal() {
        const modal = document.getElementById('modal-tetapkan-penempatan');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    function openManualModal() {
        const modal = document.getElementById('modal-penempatan-manual');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    function closeManualModal() {
        const modal = document.getElementById('modal-penempatan-manual');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function syncManualDates() {
        const tglMulaiEl = document.getElementById('input-tgl-mulai');
        const durasiEl = document.getElementById('select-durasi-bulan');
        const tglSelesaiEl = document.getElementById('input-tgl-selesai');
        const bulanMulaiEl = document.getElementById('input-bulan-mulai');

        if (!tglMulaiEl || !tglMulaiEl.value) return;

        const startDate = new Date(tglMulaiEl.value);
        if (isNaN(startDate.getTime())) return;

        const y = startDate.getFullYear();
        const m = String(startDate.getMonth() + 1).padStart(2, '0');
        if (bulanMulaiEl) bulanMulaiEl.value = `${y}-${m}-01`;

        const durasi = durasiEl ? parseInt(durasiEl.value, 10) || 3 : 3;
        const endDate = new Date(startDate);
        endDate.setMonth(endDate.getMonth() + durasi);
        const ey = endDate.getFullYear();
        const em = String(endDate.getMonth() + 1).padStart(2, '0');
        const ed = String(endDate.getDate()).padStart(2, '0');
        if (tglSelesaiEl) tglSelesaiEl.value = `${ey}-${em}-${ed}`;
    }

    @if($errors->any() && (old('mode') || $errors->has('nama') || $errors->has('email') || $errors->has('nim_nisn') || $errors->has('slot_khusus_id') || $errors->has('peserta_id')))
        document.addEventListener('DOMContentLoaded', function() {
            openManualModal();
            @if(old('mode') === 'baru')
                switchPesertaMode('baru');
            @endif
        });
    @endif

    function switchPesertaMode(mode) {
        const btnTerdaftar = document.getElementById('btn-mode-terdaftar');
        const btnBaru = document.getElementById('btn-mode-baru');
        const panelTerdaftar = document.getElementById('panel-peserta-terdaftar');
        const panelBaru = document.getElementById('panel-peserta-baru');
        const inputMode = document.getElementById('input-mode-peserta');
        const selectPeserta = document.getElementById('select-peserta-id');

        if (!inputMode) return;
        inputMode.value = mode;

        if (mode === 'baru') {
            if (btnBaru) {
                btnBaru.style.background = '#0641CC';
                btnBaru.style.color = '#FFFFFF';
            }
            if (btnTerdaftar) {
                btnTerdaftar.style.background = 'transparent';
                btnTerdaftar.style.color = 'var(--neutral-700, #334155)';
            }
            if (panelTerdaftar) panelTerdaftar.style.display = 'none';
            if (panelBaru) panelBaru.style.display = 'flex';
            if (selectPeserta) selectPeserta.removeAttribute('required');
        } else {
            if (btnTerdaftar) {
                btnTerdaftar.style.background = '#0641CC';
                btnTerdaftar.style.color = '#FFFFFF';
            }
            if (btnBaru) {
                btnBaru.style.background = 'transparent';
                btnBaru.style.color = 'var(--neutral-700, #334155)';
            }
            if (panelTerdaftar) panelTerdaftar.style.display = 'block';
            if (panelBaru) panelBaru.style.display = 'none';
            if (selectPeserta) selectPeserta.setAttribute('required', 'required');
        }
    }

    function toggleRowDropdown(btn, event) {
        event.stopPropagation();
        const wrap = btn.closest('.action-dropdown-wrap');
        if (!wrap) return;
        const menu = wrap.querySelector('.action-dropdown-menu');
        if (!menu) return;

        const isOpen = menu.classList.contains('show');

        // Tutup semua dropdown lain yang terbuka
        document.querySelectorAll('.action-dropdown-menu.show').forEach(m => {
            if (m !== menu) {
                m.classList.remove('show');
                m.closest('.action-dropdown-wrap')?.querySelector('.action-dropdown-btn')?.classList.remove('active');
            }
        });

        if (!isOpen) {
            menu.classList.add('show');
            btn.classList.add('active');

            // Cek apakah posisi dekat dengan bagian bawah viewport
            const rect = menu.getBoundingClientRect();
            if (rect.bottom > window.innerHeight - 20) {
                menu.style.top = 'auto';
                menu.style.bottom = 'calc(100% + 4px)';
            } else {
                menu.style.top = 'calc(100% + 4px)';
                menu.style.bottom = 'auto';
            }
        } else {
            menu.classList.remove('show');
            btn.classList.remove('active');
        }
    }

    // Auto-close dropdown saat klik di luar area
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.action-dropdown-wrap')) {
            document.querySelectorAll('.action-dropdown-menu.show').forEach(m => {
                m.classList.remove('show');
                m.closest('.action-dropdown-wrap')?.querySelector('.action-dropdown-btn')?.classList.remove('active');
            });
        }
    });

    function openPindahModal(penempatanId, namaPeserta, pembimbingId) {
        document.querySelectorAll('.action-dropdown-menu.show').forEach(m => {
            m.classList.remove('show');
            m.closest('.action-dropdown-wrap')?.querySelector('.action-dropdown-btn')?.classList.remove('active');
        });
        const modal = document.getElementById('modal-pindah-pembimbing');
        const nameEl = document.getElementById('pindah-peserta-nama');
        const formEl = document.getElementById('form-pindah-pembimbing');
        const selectEl = document.getElementById('select-pembimbing-baru');

        if (nameEl) nameEl.innerText = 'Peserta: ' + namaPeserta;
        if (formEl) formEl.action = '/admin/penempatan/' + penempatanId + '/pindah-pembimbing';
        if (selectEl && pembimbingId) selectEl.value = pembimbingId;
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closePindahModal() {
        const modal = document.getElementById('modal-pindah-pembimbing');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function openBatalModal(penempatanId, namaPeserta) {
        const modal = document.getElementById('modal-batal-penempatan');
        const nameEl = document.getElementById('batal-peserta-nama');
        const formEl = document.getElementById('form-batal-penempatan');

        if (nameEl) nameEl.innerText = 'Peserta: ' + namaPeserta;
        if (formEl) formEl.action = '/admin/penempatan/' + penempatanId + '/batalkan';
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeBatalModal() {
        const modal = document.getElementById('modal-batal-penempatan');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function openSuratPreview(previewUrl, downloadUrl, title) {
        const modal = document.getElementById('modal-surat-preview');
        const iframe = document.getElementById('surat-iframe');
        const titleEl = document.getElementById('surat-modal-title');
        const downloadEl = document.getElementById('surat-modal-download');

        if (titleEl) titleEl.innerText = title;
        if (downloadEl) downloadEl.href = downloadUrl;
        if (iframe) iframe.src = previewUrl;
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSuratPreview() {
        const modal = document.getElementById('modal-surat-preview');
        const iframe = document.getElementById('surat-iframe');

        if (iframe) iframe.src = 'about:blank';
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSuratPreview();
            closeManualModal();
            closePindahModal();
            closeBatalModal();
        }
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeManualModal();
                closePindahModal();
                closeBatalModal();
                closeBulkReassignModal();
            }
        });
    });

    function toggleAllPenempatan(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.penempatan-checkbox');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateBulkUI();
    }

    function updateBulkUI() {
        const checkedBoxes = document.querySelectorAll('.penempatan-checkbox:checked');
        const count = checkedBoxes.length;
        const floatingBar = document.getElementById('floating-bulk-bar');
        const countBadge = document.getElementById('bulk-selected-count');

        if (countBadge) countBadge.innerText = count;

        if (floatingBar) {
            if (count > 0) {
                floatingBar.style.display = 'flex';
            } else {
                floatingBar.style.display = 'none';
            }
        }
    }

    function openBulkReassignModal() {
        const checkedBoxes = document.querySelectorAll('.penempatan-checkbox:checked');
        if (checkedBoxes.length === 0) {
            const msg = 'Pilih minimal satu penempatan peserta terlebih dahulu.';
            if (window.MagKomToast) {
                window.MagKomToast.warning(msg);
            } else {
                alert(msg);
            }
            return;
        }

        const container = document.getElementById('bulk-ids-container');
        if (container) {
            container.innerHTML = '';
            checkedBoxes.forEach(cb => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'penempatan_ids[]';
                hidden.value = cb.value;
                container.appendChild(hidden);
            });
        }

        const countEl = document.getElementById('bulk-modal-count');
        if (countEl) countEl.innerText = checkedBoxes.length;

        const modal = document.getElementById('modal-bulk-reassign');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeBulkReassignModal() {
        const modal = document.getElementById('modal-bulk-reassign');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // =========================================================================
    // EXPORT REKAP POPOVER MANAGEMENT
    // =========================================================================
    function toggleAdmExportPopover(forceOpen = null) {
        const popover = document.getElementById('admExportPopover');
        const trigger = document.getElementById('btnToggleExportPopover');
        const wrap = trigger ? trigger.closest('.adm-export-dropdown-wrap') : null;
        if (!popover) return;

        const shouldOpen = forceOpen !== null ? forceOpen : popover.style.display === 'none';
        if (shouldOpen) {
            popover.style.display = 'flex';
            if (trigger) trigger.setAttribute('aria-expanded', 'true');
            if (wrap) wrap.classList.add('is-open');
        } else {
            popover.style.display = 'none';
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
            if (wrap) wrap.classList.remove('is-open');
        }
    }

    function handleAdmExportFormatChange(format) {
        const cards = document.querySelectorAll('.adm-format-card');
        cards.forEach(c => c.classList.remove('adm-format-selected'));

        const radio = document.getElementById('radioFormat' + format.charAt(0).toUpperCase() + format.slice(1));
        if (radio) radio.checked = true;

        const card = document.getElementById('cardFormat' + format.charAt(0).toUpperCase() + format.slice(1));
        if (card) card.classList.add('adm-format-selected');

        const btnText = document.getElementById('btnExportDownloadText');
        if (btnText) {
            if (format === 'xlsx') {
                btnText.textContent = 'Unduh Excel (.xlsx)';
            } else if (format === 'csv') {
                btnText.textContent = 'Unduh CSV (.csv)';
            } else if (format === 'pdf') {
                btnText.textContent = 'Unduh PDF (.pdf)';
            }
        }
    }

    function triggerPenempatanExport(forcedFormat = null) {
        let format = forcedFormat;
        if (!format) {
            const selectedRadio = document.querySelector('input[name="popover_export_format"]:checked');
            format = selectedRadio ? selectedRadio.value : 'xlsx';
        }

        const currentUrl = new URL(window.location.href);
        const exportUrl = new URL("{{ route('admin.penempatan.ekspor-surat-diterima') }}", window.location.origin);

        // Salin seluruh filter query string aktif
        currentUrl.searchParams.forEach((val, key) => {
            if (key !== 'page') {
                exportUrl.searchParams.set(key, val);
            }
        });

        exportUrl.searchParams.set('format', format);

        // Tutup popover
        toggleAdmExportPopover(false);

        // Jalankan download file
        window.location.href = exportUrl.toString();
    }

    // Event listener click outside & Escape key
    document.addEventListener('DOMContentLoaded', function() {
        const trigger = document.getElementById('btnToggleExportPopover');
        const popover = document.getElementById('admExportPopover');

        if (trigger) {
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleAdmExportPopover();
            });
        }

        if (popover) {
            popover.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        document.addEventListener('click', function(e) {
            if (popover && popover.style.display !== 'none') {
                if (!popover.contains(e.target) && trigger && !trigger.contains(e.target)) {
                    toggleAdmExportPopover(false);
                }
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && popover && popover.style.display !== 'none') {
                toggleAdmExportPopover(false);
            }
        });
    });
</script>

{{-- FLOATING BULK ACTION BAR --}}
@if(!auth()->user()?->isSuperAdmin())
<div id="floating-bulk-bar" style="display: none; position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%); background: #0F172A; color: #ffffff; padding: 0.85rem 1.5rem; border-radius: 9999px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3); z-index: 1040; align-items: center; gap: 1.25rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
        <span id="bulk-selected-count" style="background: #0641CC; color: #ffffff; padding: 0.15rem 0.55rem; border-radius: 999px; font-weight: 700; font-size: 0.8125rem;">0</span>
        <span>Peserta Dipilih</span>
    </div>
    <div style="display: flex; align-items: center; gap: 0.5rem;">
        <button type="button" onclick="openBulkReassignModal()" style="padding: 0.45rem 1rem; background: #0641CC; color: #ffffff; border: none; border-radius: 999px; font-weight: 700; font-size: 0.8125rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
            <span>Pindahkan Pembimbing Massal</span>
        </button>
        <button type="button" onclick="toggleAllPenempatan({checked: false})" style="background: transparent; border: none; color: #94A3B8; font-size: 0.8125rem; cursor: pointer; padding: 0.25rem 0.5rem;">
            Batal
        </button>
    </div>
</div>
@endif

{{-- MODAL BULK REASSIGN PEMBIMBING --}}
<div id="modal-bulk-reassign" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(6px); z-index: 1060; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: #ffffff; border-radius: 14px; width: 100%; max-width: 520px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); padding: 1.75rem; border: 1px solid var(--neutral-200);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.85rem;">
            <div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Pindahkan Pembimbing Massal</h3>
                <p style="font-size: 0.8rem; color: var(--neutral-500); margin: 0.2rem 0 0 0;">
                    Alihkan <span id="bulk-modal-count" style="font-weight: 700; color: #0641CC;">0</span> penempatan peserta ke pembimbing baru.
                </p>
            </div>
            <button type="button" onclick="closeBulkReassignModal()" style="background: transparent; border: none; cursor: pointer; color: var(--neutral-500); font-size: 1.25rem;">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.penempatan.bulk-reassign') }}">
            @csrf
            <div id="bulk-ids-container"></div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.4rem;">
                    Pilih Pembimbing Lapangan Baru <span style="color: #ef4444;">*</span>
                </label>
                <select name="pembimbing_id" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">-- Pilih Pembimbing Aktif --</option>
                    @foreach($pembimbingList as $pem)
                        <option value="{{ $pem->id }}">
                            {{ $pem->nama }} (Beban: {{ $pem->mentee_aktif_count }} mentee aktif)
                        </option>
                    @endforeach
                </select>
                <small style="display: block; font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.35rem;">
                    Sistem akan memvalidasi batas kapasitas <code>maks_peserta_aktif_per_pembimbing</code> (BR-13).
                </small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="closeBulkReassignModal()" style="padding: 0.5rem 1.25rem; background: var(--neutral-200); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.5rem 1.5rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Konfirmasi Pindah Pembimbing
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
