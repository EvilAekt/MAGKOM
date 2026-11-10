@extends('layouts.dashboard')

@section('title', 'Verifikasi Berkas Pendaftaran Peserta - Admin MagKom Sleman')

@section('topbar_title', 'Verifikasi Berkas Pendaftaran Peserta')

@section('content')
<div class="adm-verifikasi-wrapper">

    <style>
        /* ==========================================================================
           MAGKOM ADMIN VERIFIKASI BERKAS - SIGNATURE THEME
           Dinas Komunikasi dan Informatika Kabupaten Sleman
           ========================================================================== */
        .adm-verifikasi-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            width: 100%;
            color: #1E293B;
            font-family: inherit;
        }

        /* 0. SLEMAN NAVY HERO BANNER (Khas MagKom) */
        .adm-hero-banner {
            background: radial-gradient(80% 120% at 100% 0%, rgba(63, 81, 181, 0.45), transparent 60%),
                        linear-gradient(135deg, #01347C 0%, #003566 100%);
            border-radius: 16px;
            padding: 24px 30px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px -2px rgba(1, 52, 124, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.12);
            margin-bottom: 1.5rem;
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

        /* 1. TOP 4 KPI CARDS GRID */
        .adm-tile-count-grid {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 1.25rem !important;
            margin: 0 0 1.5rem 0 !important;
            width: 100% !important;
        }
        .adm-tile-count-grid .tile_stats_count {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            min-width: 0 !important;
            flex: none !important;
        }
        .adm-tile-count-grid .tile_stats_inner {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 1.15rem 1.25rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 2px 4px -1px rgba(15, 23, 42, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .adm-tile-count-grid .tile_stats_inner:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        }
        @media (max-width: 1024px) {
            .adm-tile-count-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }
        @media (max-width: 640px) {
            .adm-tile-count-grid {
                grid-template-columns: 1fr !important;
            }
        }

        /* 2. TOP-LEVEL CATEGORY SWITCHER (SEGMENTED CONTROL PILL CONTAINER) */
        .adm-category-nav-bar {
            display: inline-flex;
            align-items: center;
            background: #F1F5F9;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 4px;
            gap: 4px;
            margin-bottom: 1.25rem;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .adm-category-nav-item {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1rem;
            font-size: 0.84375rem;
            font-weight: 600;
            color: #64748B;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            border: 1px solid transparent;
        }
        .adm-category-nav-item:hover {
            color: #1E293B;
            background: rgba(255, 255, 255, 0.6);
        }
        .adm-category-nav-item.active {
            background: #FFFFFF;
            color: #01347C;
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
            border-color: rgba(226, 232, 240, 0.8);
        }
        .adm-category-nav-item.active-individu {
            background: #FFFFFF;
            color: #0284C7;
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
            border-color: rgba(226, 232, 240, 0.8);
        }
        .adm-category-nav-item.active-kelompok {
            background: #FFFFFF;
            color: #7C3AED;
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
            border-color: rgba(226, 232, 240, 0.8);
        }

        .adm-category-nav-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: #E2E8F0;
            color: #475569;
            transition: all 0.2s ease;
        }
        .adm-category-nav-item:hover .adm-category-nav-badge {
            background: #CBD5E1;
            color: #0F172A;
        }
        .adm-category-nav-item.active .adm-category-nav-badge {
            background: #EFF6FF;
            color: #01347C;
        }
        .adm-category-nav-item.active-individu .adm-category-nav-badge {
            background: #F0F9FF;
            color: #0284C7;
        }
        .adm-category-nav-item.active-kelompok .adm-category-nav-badge {
            background: #F5F3FF;
            color: #7C3AED;
        }

        /* 3. STANDALONE FILTER CARD (PERSIS PENEMPATAN PESERTA) */
        .adm-filter-card,
        .adm-filter-toolbar {
            background: #ffffff;
            border: 1.5px solid #CBD5E1;
            border-radius: 14px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .adm-status-track-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .adm-status-track {
            display: inline-flex;
            align-items: center;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 9999px;
            padding: 3px;
            gap: 2px;
            flex-wrap: wrap;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .adm-status-tab {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 700;
            text-decoration: none;
            color: #475569;
            transition: all 0.15s ease;
        }
        .adm-status-tab:hover {
            color: #0641CC;
            background: rgba(6, 65, 204, 0.05);
        }
        .adm-status-tab.active {
            background: #0641CC;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(6, 65, 204, 0.25);
        }

        .adm-status-counter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 9999px;
            font-size: 0.725rem;
            font-weight: 800;
            background: #F1F5F9;
            color: #475569;
            transition: all 0.15s ease;
        }
        .adm-status-tab.active .adm-status-counter {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .adm-filter-reset-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748B;
            text-decoration: none;
            padding: 5px 12px;
            border-radius: 8px;
            transition: all 0.15s ease;
        }
        .adm-filter-reset-link:hover {
            color: #DC2626;
            background: #FEE2E2;
        }

        /* Filter Grid (42px Unified Height - 4 Balanced Columns) */
        .adm-filter-grid {
            display: grid;
            grid-template-columns: minmax(260px, 2.5fr) minmax(160px, 1.3fr) minmax(170px, 1.4fr) minmax(140px, 1fr) auto;
            gap: 1rem;
            align-items: flex-end;
        }
        @media (max-width: 1200px) {
            .adm-filter-grid {
                grid-template-columns: 1fr 1fr;
            }
            .adm-filter-grid .adm-field-search {
                grid-column: 1 / -1;
            }
            .adm-filter-grid .adm-filter-actions {
                grid-column: 1 / -1;
                justify-content: flex-end;
            }
        }
        @media (max-width: 640px) {
            .adm-filter-grid {
                grid-template-columns: 1fr;
            }
        }

        .adm-field-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .adm-field-group label {
            font-size: 0.775rem;
            font-weight: 700;
            color: #475569;
            letter-spacing: -0.01em;
        }

        .adm-search-input-wrap {
            position: relative;
            width: 100%;
        }
        .adm-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            pointer-events: none;
        }

        .adm-input-search {
            height: 42px;
            width: 100%;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            padding: 0 14px 0 38px;
            font-size: 0.85rem;
            color: #0F172A;
            background: #FFFFFF;
            transition: all 0.15s ease;
            outline: none;
        }
        .adm-input-search:focus {
            border-color: #0641CC;
            box-shadow: 0 0 0 3px rgba(6, 65, 204, 0.1);
        }

        .adm-select {
            height: 42px;
            width: 100%;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            padding: 0 12px;
            font-size: 0.85rem;
            color: #0F172A;
            background: #FFFFFF;
            outline: none;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .adm-select:focus {
            border-color: #0641CC;
            box-shadow: 0 0 0 3px rgba(6, 65, 204, 0.1);
        }

        .adm-filter-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            height: 42px;
        }

        .adm-btn-filter-submit {
            height: 42px;
            padding: 0 1.25rem;
            background: #0641CC;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
            box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);
            white-space: nowrap;
        }
        .adm-btn-filter-submit:hover {
            background: #0533A8;
            box-shadow: 0 2px 6px rgba(6, 65, 204, 0.3);
            transform: translateY(-1px);
        }

        .adm-btn-filter-reset {
            height: 42px;
            padding: 0 1rem;
            background: #FFFFFF;
            color: #64748B;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #CBD5E1;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .adm-btn-filter-reset:hover {
            background: #F1F5F9;
            color: #0F172A;
            border-color: #94A3B8;
        }

        /* 3. TABLE WRAPPER (SEAMLESS SINGLE CARD) */
        .adm-table-container {
            border: 1.5px solid #CBD5E1;
            border-radius: 12px;
            overflow: hidden;
            background: #FFFFFF;
        }

        .adm-table-meta-bar {
            padding: 0.75rem 1.25rem;
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .adm-reg-badge {
            display: inline-block;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.8125rem;
            font-weight: 700;
            color: #0641CC;
            background: #EFF6FF;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            border: 1px solid #BFDBFE;
            letter-spacing: -0.01em;
        }

        .adm-reg-date {
            font-size: 0.75rem;
            color: #64748B;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .adm-badge-kelompok,
        .adm-badge-individu {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #64748B;
            background: transparent;
            border: none;
            padding: 0;
            margin-top: 0.15rem;
            line-height: 1.25;
        }
        .adm-badge-kelompok svg,
        .adm-badge-individu svg {
            color: #94A3B8;
            flex-shrink: 0;
        }

        .adm-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }
        .adm-table th {
            background: #F8FAFC;
            padding: 0.875rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748B;
            border-bottom: 1px solid #E2E8F0;
            white-space: nowrap;
        }
        .adm-table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #E2E8F0;
            vertical-align: middle;
            color: #1E293B;
            background: #FFFFFF;
            transition: background 0.15s ease;
        }
        .adm-table tr:hover td {
            background: #F8FAFC;
        }
        .adm-table tr:last-child td {
            border-bottom: none;
        }

        .adm-user-cell {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .adm-btn-tinjau {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.95rem;
            background: #0641CC;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.8125rem;
            box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .adm-btn-tinjau:hover {
            background: #0533A8;
            transform: translateY(-1px);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(6, 65, 204, 0.3);
        }

        /* =====================================================================
           DARK MODE OVERRIDES (DS-MagKom Standards)
           ===================================================================== */
        [data-theme="dark"] .adm-verifikasi-wrapper {
            color: #E2E8F0;
        }

        [data-theme="dark"] .adm-tile-count-grid .tile_stats_inner {
            background: #182234;
            border-color: #26354D;
        }

        [data-theme="dark"] .adm-filter-card,
        [data-theme="dark"] .adm-filter-toolbar {
            background-color: #182234 !important;
            border-color: #26354D !important;
        }

        [data-theme="dark"] .adm-status-track {
            background-color: #0F172A;
            border-color: #26354D;
        }

        [data-theme="dark"] .adm-category-nav-bar {
            background: #0B132B;
            border-color: #1E293B;
        }

        [data-theme="dark"] .adm-category-nav-item {
            color: #94A3B8;
        }

        [data-theme="dark"] .adm-category-nav-item:hover {
            color: #F8FAFC;
            background: rgba(30, 41, 59, 0.6);
        }

        [data-theme="dark"] .adm-category-nav-item.active {
            background: #1E293B;
            color: #38BDF8;
            border-color: #334155;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        [data-theme="dark"] .adm-category-nav-item.active-individu {
            background: #1E293B;
            color: #38BDF8;
            border-color: #334155;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        [data-theme="dark"] .adm-category-nav-item.active-kelompok {
            background: #1E293B;
            color: #C084FC;
            border-color: #334155;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        [data-theme="dark"] .adm-category-nav-badge {
            background: #1E293B;
            color: #94A3B8;
        }

        [data-theme="dark"] .adm-category-nav-item.active .adm-category-nav-badge,
        [data-theme="dark"] .adm-category-nav-item.active-individu .adm-category-nav-badge {
            background: rgba(56, 189, 248, 0.2);
            color: #38BDF8;
        }

        [data-theme="dark"] .adm-category-nav-item.active-kelompok .adm-category-nav-badge {
            background: rgba(192, 132, 252, 0.2);
            color: #C084FC;
        }

        [data-theme="dark"] .adm-status-tab {
            color: #94A3B8;
        }

        [data-theme="dark"] .adm-status-tab:hover {
            color: #F8FAFC;
            background: rgba(255, 255, 255, 0.05);
        }

        [data-theme="dark"] .adm-status-tab.active {
            background-color: #1E3A8A;
            color: #93C5FD;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        [data-theme="dark"] .adm-status-counter {
            background-color: #26354D;
            color: #E2E8F0;
        }

        [data-theme="dark"] .adm-status-tab.active .adm-status-counter {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        [data-theme="dark"] .adm-field-group label {
            color: #94A3B8;
        }

        [data-theme="dark"] .adm-input-search,
        [data-theme="dark"] .adm-select {
            background-color: #0F172A;
            border-color: #26354D;
            color: #F8FAFC;
        }

        [data-theme="dark"] .adm-select option {
            background-color: #0F172A;
            color: #F8FAFC;
        }

        [data-theme="dark"] .adm-input-search:focus,
        [data-theme="dark"] .adm-select:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        [data-theme="dark"] .adm-btn-filter-reset {
            background-color: #0F172A;
            border-color: #26354D;
            color: #94A3B8;
        }

        [data-theme="dark"] .adm-btn-filter-reset:hover {
            background-color: #162238;
            color: #F8FAFC;
            border-color: #3B82F6;
        }

        [data-theme="dark"] .adm-table-container {
            background-color: #182234;
            border-color: #26354D;
        }

        [data-theme="dark"] .adm-table-meta-bar {
            background-color: #182234;
            border-bottom-color: #26354D;
        }

        [data-theme="dark"] .adm-table-meta-bar strong {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .adm-reg-badge {
            color: #93C5FD;
            background: rgba(30, 58, 138, 0.35);
            border-color: rgba(59, 130, 246, 0.35);
        }

        [data-theme="dark"] .adm-reg-date {
            color: #94A3B8;
        }

        [data-theme="dark"] .adm-badge-kelompok,
        [data-theme="dark"] .adm-badge-individu {
            color: #94A3B8;
            background: transparent;
            border: none;
            padding: 0;
        }
        [data-theme="dark"] .adm-badge-kelompok svg,
        [data-theme="dark"] .adm-badge-individu svg {
            color: #64748B;
        }

        [data-theme="dark"] .adm-table th {
            background-color: #0E1726;
            color: #94A3B8;
            border-bottom-color: #26354D;
        }

        [data-theme="dark"] .adm-table td {
            background-color: #182234;
            color: #CBD5E1;
            border-bottom-color: #26354D;
        }

        [data-theme="dark"] .adm-table tr:hover td {
            background-color: #162238;
        }

        [data-theme="dark"] .adm-table td div[style*="color: #0F172A"],
        [data-theme="dark"] .adm-table td div[style*="color: #1E293B"] {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .adm-table td span[style*="color: #0641CC"] {
            background-color: rgba(37, 99, 235, 0.2) !important;
            border-color: rgba(59, 130, 246, 0.3) !important;
            color: #93C5FD !important;
        }
    </style>

    {{-- 0. SLEMAN NAVY HERO BANNER (Khas MagKom) --}}
    <div class="adm-hero-banner">
        <div class="adm-hero-content">
            <div class="adm-hero-text">
                <h1 class="adm-hero-title">Daftar Pendaftaran Peserta Magang</h1>
                <div class="adm-hero-subtitle">
                    <span>Verifikasi Berkas Pendaftaran Peserta</span>
                    <span class="sep">&bull;</span>
                    <span>Dinas Komunikasi dan Informatika Kabupaten Sleman</span>
                </div>
            </div>
            <div class="adm-hero-actions">
                {{-- Unified Export Popover Trigger & Container --}}
                <div class="adm-export-dropdown-wrap">
                    <button type="button" 
                            id="btnToggleExportPopoverPendaftaran" 
                            class="adm-btn-export-main adm-hero-btn-secondary" 
                            aria-expanded="false" 
                            aria-haspopup="menu" 
                            aria-controls="admExportPopoverPendaftaran"
                            title="Pilih dan unduh rekap data pendaftaran (Excel, CSV, PDF)">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Ekspor Rekap</span>
                        <svg id="admExportCaretSvgPendaftaran" class="adm-export-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    {{-- Floating Dropdown Popover Container (Right-Aligned) --}}
                    <div id="admExportPopoverPendaftaran" class="adm-export-popover adm-export-popover-right" style="display: none;" role="menu" aria-label="Menu Pilihan Format Ekspor Pendaftaran">
                        <div class="adm-popover-pointer adm-pointer-right"></div>
                        <div class="adm-popover-header">
                            <div>
                                <div class="adm-popover-title">Ekspor Rekap Cepat</div>
                                <div class="adm-popover-desc">Data pendaftaran hasil filter aktif</div>
                            </div>
                            <span class="adm-popover-row-count tabular-nums">
                                {{ number_format($pendaftaranList->total()) }} Baris
                            </span>
                        </div>

                        <div class="adm-popover-formats" role="radiogroup" aria-label="Format Berkas">
                            {{-- Format 1: Excel (.xlsx) - Rekomendasi --}}
                            <label class="adm-format-card adm-format-selected" id="cardFormatPendaftaranXlsx" onclick="handleAdmExportPendaftaranFormatChange('xlsx')">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_pendaftaran_format" id="radioFormatPendaftaranXlsx" value="xlsx" checked class="adm-format-radio" onchange="handleAdmExportPendaftaranFormatChange('xlsx')">
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
                            <label class="adm-format-card" id="cardFormatPendaftaranCsv" onclick="handleAdmExportPendaftaranFormatChange('csv')">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_pendaftaran_format" id="radioFormatPendaftaranCsv" value="csv" class="adm-format-radio" onchange="handleAdmExportPendaftaranFormatChange('csv')">
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
                            <label class="adm-format-card" id="cardFormatPendaftaranPdf" onclick="handleAdmExportPendaftaranFormatChange('pdf')">
                                <div class="adm-format-content">
                                    <input type="radio" name="popover_export_pendaftaran_format" id="radioFormatPendaftaranPdf" value="pdf" class="adm-format-radio" onchange="handleAdmExportPendaftaranFormatChange('pdf')">
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
                            <button type="button" class="adm-btn-popover-cancel" onclick="toggleAdmExportPopoverPendaftaran(false)">
                                Batal
                            </button>
                            <button type="button" id="btnDoExportDownloadPendaftaran" class="adm-btn-popover-download" onclick="triggerPendaftaranExport()">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span id="btnExportDownloadTextPendaftaran">Unduh Excel (.xlsx)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 1. TOP 4 KPI CARDS ROW (COCKPIT PARITY) --}}
    <div class="row tile_count adm-tile-count-grid">
        <x-gentelella.tile-stat 
            title="Total Pengajuan" 
            :value="number_format($queueStats['total'] ?? $pendaftaranList->total())" 
            icon="file-text"
            color="blue"
            trendText="Total"
            trendDirection="up"
            subText="Semua riwayat pendaftaran" 
            :sparkBars="[35, 50, 45, 60, 65, 80, 95]"
        />
        <x-gentelella.tile-stat 
            title="Menunggu Ditinjau" 
            :value="number_format($queueStats['diajukan'] ?? 0)" 
            icon="clipboard-check"
            color="yellow"
            trendType="warning"
            trendText="Antrean"
            trendDirection="up"
            subText="Prioritas antrean FIFO" 
            :sparkBars="[70, 75, 65, 80, 85, 60, 55]"
        />
        <x-gentelella.tile-stat 
            title="Pendaftaran Diterima" 
            :value="number_format($queueStats['diterima'] ?? 0)" 
            icon="check-circle"
            color="green"
            trendType="success"
            trendText="Diterima"
            trendDirection="up"
            subText="Telah lolos verifikasi berkas" 
            :sparkBars="[25, 35, 45, 50, 65, 80, 95]"
        />
        <x-gentelella.tile-stat 
            title="Tertahan > 3 Hari" 
            :value="number_format($queueStats['sla_warning'] ?? 0)" 
            icon="clock"
            color="red"
            trendType="danger"
            trendText="SLA Target"
            trendDirection="down"
            subText="Batas SLA verifikasi target" 
            :sparkBars="[40, 30, 45, 25, 35, 20, 15]"
        />
    </div>

    {{-- NAVIGASI TAB UTAMA: Kategori Pendaftaran (Berdiri Sendiri di Luar Frame Kartu) --}}
    @php
        $currentStatus = request('status', '');
        $currentTipe = request('tipe', '');
        $makeFilterUrl = function($statusVal = null, $tipeVal = null) {
            $params = request()->except('page');
            if ($statusVal !== null) {
                if ($statusVal === '') {
                    unset($params['status']);
                } else {
                    $params['status'] = $statusVal;
                }
            }
            if ($tipeVal !== null) {
                if ($tipeVal === '') {
                    unset($params['tipe']);
                } else {
                    $params['tipe'] = $tipeVal;
                }
            }
            return route('admin.pendaftaran.index', $params);
        };
        $hasActiveSearch = request()->filled('q') 
            || request()->filled('bulan_magang_id') 
            || request()->filled('jurusan_id') 
            || (request()->filled('sort') && request('sort') !== 'terbaru');
    @endphp

    <div style="margin-bottom: 1.15rem;">
        <div class="adm-category-nav-bar" role="tablist" aria-label="Kategori Pendaftaran Peserta" style="margin-bottom: 0;">
            <a href="{{ $makeFilterUrl(null, '') }}" class="adm-category-nav-item {{ $currentTipe === '' ? 'active' : '' }}" role="tab" aria-selected="{{ $currentTipe === '' ? 'true' : 'false' }}" title="Tampilkan semua kategori pendaftaran">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>Semua Kategori</span>
                <span class="adm-category-nav-badge">{{ $queueStats['total'] ?? $pendaftaranList->total() }}</span>
            </a>
            <a href="{{ $makeFilterUrl(null, 'individu') }}" class="adm-category-nav-item {{ ($currentTipe === 'individu' || $currentTipe === 'mandiri') ? 'active-individu' : '' }}" role="tab" aria-selected="{{ ($currentTipe === 'individu' || $currentTipe === 'mandiri') ? 'true' : 'false' }}" title="Hanya pendaftaran individu">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Individu</span>
                <span class="adm-category-nav-badge">{{ $queueStats['total_individu'] ?? 0 }}</span>
            </a>
            <a href="{{ $makeFilterUrl(null, 'kelompok') }}" class="adm-category-nav-item {{ $currentTipe === 'kelompok' ? 'active-kelompok' : '' }}" role="tab" aria-selected="{{ $currentTipe === 'kelompok' ? 'true' : 'false' }}" title="Hanya pendaftaran kelompok">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Kelompok</span>
                <span class="adm-category-nav-badge">{{ $queueStats['total_kelompok'] ?? 0 }}</span>
            </a>
        </div>
    </div>

    {{-- 2. STANDALONE FILTER CARD (TERPISAH SEPERTI DI PENEMPATAN PESERTA) --}}
    <div class="adm-filter-card">
        {{-- Form Pencarian & Filter Grid Lapang (42px Unified Height - 4 Kolom) --}}
        <form method="GET" action="{{ route('admin.pendaftaran.index') }}" class="adm-filter-grid">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('tipe'))
                <input type="hidden" name="tipe" value="{{ request('tipe') }}">
            @endif

            <div class="adm-field-group adm-field-search">
                <label for="search-input">Pencarian Calon Peserta</label>
                <div class="adm-search-input-wrap">
                    <svg class="adm-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="search-input" name="q" value="{{ request('q') }}" placeholder="Cari nama..." class="adm-input-search">
                </div>
            </div>

            <div class="adm-field-group">
                <label for="select-bulan">Bulan Magang</label>
                <select name="bulan_magang_id" id="select-bulan" class="adm-select">
                    <option value="">Semua Bulan</option>
                    @foreach($bulanList as $bulan)
                        <option value="{{ $bulan->id }}" {{ request('bulan_magang_id') == $bulan->id ? 'selected' : '' }}>
                            {{ $bulan->bulan_mulai?->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="adm-field-group">
                <label for="select-jurusan">Jurusan</label>
                <select name="jurusan_id" id="select-jurusan" class="adm-select">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusanList as $jur)
                        <option value="{{ $jur->id }}" {{ request('jurusan_id') == $jur->id ? 'selected' : '' }}>
                            {{ $jur->nama_jurusan }} ({{ strtoupper($jur->kategori) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="adm-field-group">
                <label for="select-sort">Urutan Data</label>
                <select name="sort" id="select-sort" class="adm-select">
                    <option value="terbaru" {{ request('sort', 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                </select>
            </div>

            <div class="adm-filter-actions">
                <button type="submit" class="adm-btn-filter-submit">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span>Terapkan</span>
                </button>
                @if($hasActiveSearch)
                    <a href="{{ route('admin.pendaftaran.index', array_filter(['status' => request('status'), 'tipe' => request('tipe')])) }}" class="adm-btn-filter-reset" title="Kembalikan pencarian ke kondisi awal">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 3. MAIN X-PANEL CARD (TABEL CALON PESERTA) --}}
    <x-gentelella.x-panel :toolbox="false">

        {{-- 3A. TABEL DATA CALON PESERTA TERPADU --}}
        <div class="adm-table-container">
            {{-- Toolbar Status Alur Kerja (Tepat Menempel di Atas Tabel) --}}
            <div class="adm-table-meta-bar">
                {{-- Sisi Kiri: Switcher Status Alur Kerja (Workflow State) --}}
                <div class="adm-status-track">
                    <a href="{{ $makeFilterUrl('', null) }}" class="adm-status-tab {{ $currentStatus === '' ? 'active' : '' }}">
                        <span>Semua Status</span>
                        <span class="adm-status-counter">{{ $queueStats['total'] ?? $pendaftaranList->total() }}</span>
                    </a>
                    <a href="{{ $makeFilterUrl('diajukan', null) }}" class="adm-status-tab {{ $currentStatus === 'diajukan' ? 'active' : '' }}">
                        <span>Diajukan</span>
                        <span class="adm-status-counter">{{ $queueStats['diajukan'] ?? 0 }}</span>
                    </a>
                    <a href="{{ $makeFilterUrl('diterima', null) }}" class="adm-status-tab {{ $currentStatus === 'diterima' ? 'active' : '' }}">
                        <span>Diterima</span>
                        <span class="adm-status-counter">{{ $queueStats['diterima'] ?? 0 }}</span>
                    </a>
                    <a href="{{ $makeFilterUrl('ditolak', null) }}" class="adm-status-tab {{ $currentStatus === 'ditolak' ? 'active' : '' }}">
                        <span>Ditolak</span>
                        <span class="adm-status-counter">{{ $queueStats['ditolak'] ?? 0 }}</span>
                    </a>
                </div>
            </div>

            @if($pendaftaranList->isEmpty())
                <div style="padding: 3.5rem 1.5rem; text-align: center; color: #64748B;">
                    <div style="width: 52px; height: 52px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: #0641CC;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 1.25rem 0;">
                        Tidak Ada Berkas
                    </h3>
                    <a href="{{ route('admin.pendaftaran.index') }}" class="adm-btn-tinjau" style="display: inline-flex;">
                        Reset Semua Filter
                    </a>
                </div>
            @else
                <div style="overflow-x: auto; width: 100%;">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th style="width: 140px;">No. Registrasi</th>
                                <th>Calon Peserta</th>
                                <th>Institusi & Jurusan</th>
                                <th>Bulan Magang</th>
                                <th>Status</th>
                                <th style="text-align: right; width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendaftaranList as $item)
                                @php
                                    $val = $item->status->value ?? (string)$item->status;
                                    $statusDotType = match($val) {
                                        'diterima' => 'success',
                                        'diajukan' => 'warning',
                                        'waiting_list' => 'info',
                                        'ditolak' => 'danger',
                                        'perlu_perbaikan' => 'warning',
                                        default => 'neutral',
                                    };

                                    // Dynamic 2-letter uppercase initials with rotating palette
                                    $userName = trim($item->user?->nama ?? 'Peserta');
                                    $nameWords = preg_split('/\s+/', $userName);
                                    if (count($nameWords) >= 2) {
                                        $initials = mb_substr($nameWords[0], 0, 1) . mb_substr($nameWords[1], 0, 1);
                                    } else {
                                        $initials = mb_substr($userName, 0, min(2, mb_strlen($userName)));
                                    }
                                    $initials = strtoupper($initials);
                                    $paletteIdx = (($item->id ?? 1) % 5) + 1;
                                @endphp
                                <tr>
                                    {{-- No. Registrasi --}}
                                    <td>
                                        <span class="adm-reg-badge">
                                            {{ $item->nomor_pendaftaran }}
                                        </span>
                                    </td>

                                    {{-- Calon Peserta --}}
                                    <td>
                                        @php
                                            $isSmk = ($item->user?->profil?->jenjang?->value ?? (string)($item->user?->profil?->jenjang ?? '')) === 'smk'
                                                || ($item->kuota?->jurusan?->kategori ?? '') === 'smk';
                                            $idLabel = $isSmk ? 'NISN' : 'NIM';
                                        @endphp
                                        <div class="adm-user-cell">
                                            <div class="cv-avatar-sm cv-avatar-palette-{{ $paletteIdx }}" style="width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.825rem; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.06);">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; color: #0F172A; font-size: 0.875rem;">
                                                    {{ $item->user?->nama ?? 'Calon Peserta' }}
                                                </div>
                                                @if($item->tipe === 'kelompok' || $item->kelompokMagang)
                                                    <div>
                                                        <span class="adm-badge-kelompok" title="{{ $item->kelompokMagang?->nama_kelompok ? 'Tim: ' . $item->kelompokMagang->nama_kelompok : 'Kelompok' }}">
                                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                                            Kelompok
                                                            @if($item->kelompokMagang)
                                                                <span class="sr-only">Tim: {{ $item->kelompokMagang->nama_kelompok }}</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                @else
                                                    <div>
                                                        <span class="adm-badge-individu">
                                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                                            Individu
                                                        </span>
                                                    </div>
                                                @endif
                                                <span class="sr-only">{{ $item->user?->email }} {{ $item->user?->profil?->nim_nisn }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Institusi & Jurusan --}}
                                    <td>
                                        <div style="font-weight: 600; color: #1E293B;">{{ $item->user?->profil?->institusi ?? '-' }}</div>
                                        <small style="color: #64748B;">{{ $item->kuota?->jurusan?->nama_jurusan ?? ($item->user?->profil?->jurusan ?? '-') }}</small>
                                    </td>

                                    {{-- Periode --}}
                                    <td>
                                        <div style="font-weight: 600; color: #0F172A;">
                                            {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                        </div>
                                        <small style="color: #64748B;">Durasi: {{ $item->durasi_bulan }} Bulan</small>
                                    </td>

                                    {{-- Status Permohonan --}}
                                    <td>
                                        <span class="cv-status-dot-badge {{ $statusDotType }}">
                                            <span class="dot"></span>
                                            <span>{{ $item->status->label() }}</span>
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td style="text-align: right;">
                                        <a href="{{ route('admin.pendaftaran.show', $item) }}" class="adm-btn-tinjau">
                                            <span>Tinjau Berkas</span>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="adm-table-footer">
                    <x-gentelella.pagination-bar 
                        :paginator="$pendaftaranList" 
                        :perPageOptions="[5, 10, 25]" 
                        entity="berkas pendaftaran" 
                        badgeText="Pendaftaran Terverifikasi" 
                    />
                </div>
            @endif
        </div>

    </x-gentelella.x-panel>

</div>
@endsection

@push('scripts')
<script>
    // =========================================================================
    // EXPORT REKAP PENDAFTARAN POPOVER MANAGEMENT
    // =========================================================================
    function toggleAdmExportPopoverPendaftaran(forceOpen = null) {
        const popover = document.getElementById('admExportPopoverPendaftaran');
        const trigger = document.getElementById('btnToggleExportPopoverPendaftaran');
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

    function handleAdmExportPendaftaranFormatChange(format) {
        const cards = document.querySelectorAll('#admExportPopoverPendaftaran .adm-format-card');
        cards.forEach(c => c.classList.remove('adm-format-selected'));

        const radio = document.getElementById('radioFormatPendaftaran' + format.charAt(0).toUpperCase() + format.slice(1));
        if (radio) radio.checked = true;

        const card = document.getElementById('cardFormatPendaftaran' + format.charAt(0).toUpperCase() + format.slice(1));
        if (card) card.classList.add('adm-format-selected');

        const btnText = document.getElementById('btnExportDownloadTextPendaftaran');
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

    function triggerPendaftaranExport(forcedFormat = null) {
        let format = forcedFormat;
        if (!format) {
            const selectedRadio = document.querySelector('input[name="popover_export_pendaftaran_format"]:checked');
            format = selectedRadio ? selectedRadio.value : 'xlsx';
        }

        const currentUrl = new URL(window.location.href);
        const exportUrl = new URL("{{ route('admin.laporan.pendaftaran') }}", window.location.origin);

        // Salin seluruh filter query string aktif
        currentUrl.searchParams.forEach((val, key) => {
            if (key !== 'page') {
                exportUrl.searchParams.set(key, val);
            }
        });

        exportUrl.searchParams.set('format', format);

        // Tutup popover
        toggleAdmExportPopoverPendaftaran(false);

        // Jalankan download file
        window.location.href = exportUrl.toString();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const trigger = document.getElementById('btnToggleExportPopoverPendaftaran');
        const popover = document.getElementById('admExportPopoverPendaftaran');

        if (trigger) {
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleAdmExportPopoverPendaftaran();
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
                    toggleAdmExportPopoverPendaftaran(false);
                }
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && popover && popover.style.display !== 'none') {
                toggleAdmExportPopoverPendaftaran(false);
            }
        });
    });
</script>
@endpush
