@extends('layouts.dashboard')

@section('title', 'Bulan & Kuota - Admin MagKom Sleman')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #64748B;">
        <a href="{{ route('admin.dashboard') }}" style="color: #64748B; text-decoration: none;">Dashboard</a>
        <span>/</span>
        <span style="color: #0F172A; font-weight: 700;">Manajemen Kuota Magang</span>
    </nav>
@endsection

@section('content')
<div class="adm-kuota-revamp-wrap">

    <style>
        /* ==========================================================================
           REVAMP DESAIN: BULAN & KUOTA MAGANG (DISKOMINFO KABUPATEN SLEMAN)
           ========================================================================== */
        .adm-kuota-revamp-wrap {
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            font-family: inherit;
            color: #1E293B;
        }

        /* 1. Header Toolbar */
        .kuota-page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .kuota-page-title {
            font-size: 1.625rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0 0 0.25rem 0;
            letter-spacing: -0.02em;
        }

        .kuota-page-subtitle {
            font-size: 0.875rem;
            color: #64748B;
            margin: 0;
        }

        .kuota-page-actions {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            flex-wrap: wrap;
        }

        .btn-outline-action {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.55rem 1.1rem;
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #1E293B;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-outline-action:hover {
            background: #F8FAFC;
            border-color: #94A3B8;
        }

        .btn-primary-action {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.55rem 1.25rem;
            background: #2563EB;
            border: 1px solid #2563EB;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 700;
            color: #FFFFFF;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.3);
        }

        .btn-primary-action:hover {
            background: #1D4ED8;
            border-color: #1D4ED8;
        }

        /* 2. Hero Card Biru Modern (Persis Dashboard Gambar 2) */
        .hero-banner-blue {
            background: linear-gradient(95deg, rgba(6, 65, 204, 0.96) 0%, rgba(6, 65, 204, 0.88) 45%, rgba(6, 65, 204, 0.38) 100%),
                        url('{{ asset('images/dashboard_hero_bg.jpg') }}') right center / cover no-repeat;
            border-radius: 16px;
            padding: 2rem 2.25rem;
            color: #FFFFFF;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(6, 65, 204, 0.25), 0 8px 10px -6px rgba(6, 65, 204, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
            min-height: 190px;
        }

        .hero-banner-blue::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 85% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 60%);
            pointer-events: none;
        }

        .hero-left-content {
            position: relative;
            z-index: 2;
            max-width: 680px;
        }

        .hero-badge-caps {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(8px);
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }

        .hero-main-title {
            font-size: 2rem;
            font-weight: 800;
            color: #FFFFFF;
            margin: 0 0 1.25rem 0;
            display: flex;
            align-items: center;
            gap: 0.875rem;
            flex-wrap: wrap;
        }

        .hero-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .hero-status-open {
            background: #FFFFFF;
            color: #16A34A;
        }

        .hero-status-closed {
            background: rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .hero-meta-grid {
            display: flex;
            align-items: center;
            gap: 1.75rem;
            flex-wrap: wrap;
        }

        .hero-meta-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .hero-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .hero-meta-label {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.85);
            display: block;
            margin-bottom: 2px;
        }

        .hero-meta-val {
            font-size: 0.9375rem;
            font-weight: 700;
            color: #FFFFFF;
            display: block;
        }

        /* Glassmorphism Floating Card Kanan */
        .hero-stat-card {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.24);
            backdrop-filter: blur(12px);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            min-width: 250px;
        }

        .stat-card-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0.35rem;
        }

        .stat-card-number-row {
            display: flex;
            align-items: baseline;
            gap: 0.35rem;
        }

        .stat-card-num-big {
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1;
            color: #FFFFFF;
        }

        .stat-card-num-sub {
            font-size: 0.9375rem;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
        }

        .stat-card-bar-wrap {
            height: 6px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 9999px;
            overflow: hidden;
            margin: 0.875rem 0 0.5rem 0;
        }

        .stat-card-bar-fill {
            height: 100%;
            background: #FFFFFF;
            border-radius: 9999px;
            transition: width 0.4s ease;
        }

        .stat-card-countdown {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.75rem;
            color: #FEF08A;
            font-weight: 600;
        }

        /* 3. Section Kuota per Bidang */
        .section-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 0.5rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0 0 0.25rem 0;
        }

        .section-desc {
            font-size: 0.85rem;
            color: #64748B;
            margin: 0;
        }

        /* 4. Table Wrapper */
        .card-table-wrap {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .table-bidang {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            text-align: left;
        }

        .table-bidang thead th {
            background: #F8FAFC;
            border-bottom: 1px solid #E2E8F0;
            padding: 1rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table-bidang tbody tr {
            border-bottom: 1px solid #F1F5F9;
            transition: background 0.15s ease;
        }

        .table-bidang tbody tr:last-child {
            border-bottom: none;
        }

        .table-bidang tbody tr:hover {
            background: #F8FAFC;
        }

        .table-bidang td {
            padding: 1.1rem 1.25rem;
            vertical-align: middle;
        }

        /* Bidang Avatar */
        .bidang-cell {
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }

        .avatar-initial {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .bidang-name {
            font-weight: 700;
            color: #0F172A;
            font-size: 0.9375rem;
        }

        .jurusan-text-muted {
            color: #475569;
            font-size: 0.8125rem;
            line-height: 1.45;
            max-width: 320px;
        }

        /* Jenjang Badges */
        .jenjang-badge-group {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .badge-jenjang-pill {
            display: inline-block;
            padding: 2px 7px;
            background: #EFF6FF;
            color: #1D4ED8;
            border: 1px solid #DBEAFE;
            border-radius: 6px;
            font-size: 0.725rem;
            font-weight: 700;
        }

        /* Progress Mini */
        .kuota-occupancy-wrap {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 110px;
        }

        .occupancy-numbers {
            font-size: 0.8125rem;
            color: #1E293B;
        }

        .occupancy-ratio {
            font-weight: 800;
        }

        .occupancy-percent {
            font-size: 0.75rem;
            color: #64748B;
            margin-left: 4px;
        }

        .mini-progress-bar {
            height: 5px;
            background: #E2E8F0;
            border-radius: 9999px;
            overflow: hidden;
            width: 100%;
        }

        .mini-progress-fill {
            height: 100%;
            background: #2563EB;
            border-radius: 9999px;
        }

        .kapasitas-text {
            font-weight: 700;
            color: #0F172A;
            font-size: 0.875rem;
            white-space: nowrap;
        }

        .btn-edit-action {
            display: inline-block;
            padding: 0.35rem 0.95rem;
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            color: #2563EB;
            border-radius: 6px;
            font-size: 0.8125rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-edit-action:hover {
            background: #DBEAFE;
            color: #1D4ED8;
        }

        /* 5. Alert Info Pembimbing */
        .alert-validation-box {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            border-radius: 12px;
            padding: 0.875rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #1E40AF;
            font-size: 0.8125rem;
            line-height: 1.45;
        }

        /* 6. Selector Bulan Bar Ringkas & Tombol Kontrol */
        .month-switch-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem 1.25rem;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            font-size: 0.8125rem;
            color: #475569;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .month-switch-strip select {
            padding: 0.45rem 0.85rem;
            font-size: 0.8125rem;
            font-weight: 700;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            background: #F8FAFC;
            color: #0F172A;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .month-switch-strip select:focus {
            outline: none;
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* 7. Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .modal-card {
            background: #FFFFFF;
            border-radius: 16px;
            width: 100%;
            max-width: 520px;
            padding: 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid #E2E8F0;
        }

        .modal-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 0.875rem;
            border-bottom: 1px solid #E2E8F0;
        }

        .modal-title {
            font-size: 1.125rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0;
        }

        .modal-btn-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #64748B;
            cursor: pointer;
            line-height: 1;
        }

        .modal-btn-close-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            color: #64748B;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .modal-btn-close-box:hover {
            background: #F8FAFC;
            color: #0F172A;
            border-color: #CBD5E1;
        }

        .modal-header-desc {
            font-size: 0.8125rem;
            color: #64748B;
            margin: 0.25rem 0 0 0;
        }

        .form-row-group {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            margin-bottom: 1rem;
        }

        .form-row-group label {
            font-size: 0.8125rem;
            font-weight: 700;
            color: #1E293B;
        }

        .form-row-group input,
        .form-row-group select {
            height: 42px;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            padding: 0 0.875rem;
            font-size: 0.875rem;
            font-family: inherit;
        }

        .input-calendar-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .input-calendar-wrap input {
            width: 100%;
            height: 42px;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            padding: 0 2.25rem 0 0.875rem;
            font-size: 0.875rem;
            font-family: inherit;
            color: #1E293B;
        }

        .input-calendar-icon {
            position: absolute;
            right: 0.75rem;
            pointer-events: none;
            color: #94A3B8;
            display: flex;
            align-items: center;
        }

        .form-helper-text {
            font-size: 0.75rem;
            color: #64748B;
            margin-top: 0.3rem;
        }

        .status-pills-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 1rem;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #E2E8F0;
            background: #FFFFFF;
            color: #475569;
            transition: all 0.15s ease;
        }

        .btn-status-pill.active {
            border: 1.5px solid #2563EB;
            background: #EFF6FF;
            color: #2563EB;
            font-weight: 700;
        }

        .modal-callout-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #EFF6FF;
            border-radius: 8px;
            padding: 0.85rem 1rem;
            margin-top: 1rem;
            color: #1D4ED8;
            font-size: 0.8125rem;
            line-height: 1.45;
        }

        /* Chips Jurusan & Jenjang Pills */
        .jurusan-chips-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
        }

        .chip-jurusan {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 0.85rem;
            background: #EFF6FF;
            border: 1.5px solid #2563EB;
            border-radius: 8px;
            color: #1D4ED8;
            font-size: 0.8125rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .chip-jurusan.inactive {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            color: #475569;
            font-weight: 600;
        }

        .btn-add-jurusan-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.45rem 0.85rem;
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            color: #475569;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-add-jurusan-chip:hover {
            background: #F8FAFC;
            border-color: #94A3B8;
            color: #0F172A;
        }

        .jenjang-pills-wrap {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .btn-jenjang-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.4rem 0.95rem;
            border-radius: 8px;
            border: 1px solid #CBD5E1;
            background: #FFFFFF;
            color: #475569;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-jenjang-pill.active {
            border: 1.5px solid #2563EB;
            background: #EFF6FF;
            color: #2563EB;
            font-weight: 700;
        }

        /* Box Warning Kapasitas */
        .box-capacity-warning {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            color: #991B1B;
            font-size: 0.8125rem;
            line-height: 1.45;
            margin-top: 1rem;
        }

        .box-capacity-warning strong {
            display: block;
            font-weight: 800;
            color: #991B1B;
            margin-bottom: 0.2rem;
        }

        .modal-footer-btns {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid #E2E8F0;
        }

        /* Dark Mode Compatibility */
        [data-theme="dark"] .adm-kuota-revamp-wrap {
            color: #F8FAFC;
        }
        [data-theme="dark"] .kuota-page-title,
        [data-theme="dark"] .section-title,
        [data-theme="dark"] .bidang-name,
        [data-theme="dark"] .kapasitas-text,
        [data-theme="dark"] .modal-title {
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .btn-outline-action,
        [data-theme="dark"] .card-table-wrap,
        [data-theme="dark"] .month-switch-strip,
        [data-theme="dark"] .modal-card {
            background-color: #182234 !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .table-bidang thead th {
            background-color: #141E30 !important;
            border-bottom-color: #26354D !important;
            color: #94A3B8 !important;
        }
        [data-theme="dark"] .table-bidang tbody tr {
            border-bottom-color: #26354D !important;
        }
        [data-theme="dark"] .table-bidang tbody tr:hover {
            background-color: #162238 !important;
        }
        [data-theme="dark"] .jurusan-text-muted {
            color: #94A3B8 !important;
        }
        [data-theme="dark"] .badge-jenjang-pill {
            background: rgba(37, 99, 235, 0.2) !important;
            color: #93C5FD !important;
            border-color: rgba(59, 130, 246, 0.3) !important;
        }
        [data-theme="dark"] .alert-validation-box {
            background: rgba(30, 58, 138, 0.3) !important;
            border-color: #1E3A8A !important;
            color: #BFDBFE !important;
        }
        [data-theme="dark"] .btn-edit-action {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #60A5FA !important;
        }
    </style>

    {{-- =========================================================================
         1. HEADER HALAMAN: BULAN & KUOTA
         ========================================================================= --}}
    <div class="kuota-page-header">
        <div>
            <h1 class="kuota-page-title">Bulan &amp; kuota</h1>
            <p class="kuota-page-subtitle">Atur bulan pendaftaran dan kuota magang per bidang.</p>
        </div>
    </div>

    {{-- =========================================================================
         2. HERO BANNER: INFORMASI BULAN AKTIF (PERSIS DASHBOARD GAMBAR 2)
         ========================================================================= --}}
    @if($selectedBulan)
        <div class="hero-banner-blue">
            <div class="hero-left-content">
                <span class="hero-badge-caps">Bulan Aktif</span>
                
                <div class="hero-main-title">
                    <span>{{ $selectedBulan->nama_bulan ?? ($selectedBulan->bulan_mulai?->translatedFormat('F Y') ?? 'Gelombang 1') }}</span>
                    @if($isPendaftaranBuka)
                        <span class="hero-status-pill hero-status-open">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #16A34A;"></span>
                            <span>Dibuka</span>
                        </span>
                    @else
                        <span class="hero-status-pill hero-status-closed">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #CBD5E1;"></span>
                            <span>Ditutup</span>
                        </span>
                    @endif
                </div>

                <div class="hero-meta-grid">
                    {{-- 1. Tanggal Buka --}}
                    <div class="hero-meta-item">
                        <div class="hero-icon-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <div>
                            <span class="hero-meta-label">Tanggal buka</span>
                            <span class="hero-meta-val">
                                {{ $selectedBulan->tgl_buka ? \Carbon\Carbon::parse($selectedBulan->tgl_buka)->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- 2. Tanggal Tutup --}}
                    <div class="hero-meta-item">
                        <div class="hero-icon-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                                <line x1="4" y1="22" x2="4" y2="15"></line>
                            </svg>
                        </div>
                        <div>
                            <span class="hero-meta-label">Tanggal tutup</span>
                            <span class="hero-meta-val">
                                {{ $selectedBulan->tgl_tutup ? \Carbon\Carbon::parse($selectedBulan->tgl_tutup)->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- 3. Mulai Magang --}}
                    <div class="hero-meta-item">
                        <div class="hero-icon-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="hero-meta-label">Mulai magang</span>
                            <span class="hero-meta-val">
                                {{ $selectedBulan->bulan_mulai ? $selectedBulan->bulan_mulai->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stat Box Kanan --}}
            <div class="hero-stat-card">
                <div class="stat-card-label">Kuota terisi <span class="sr-only">Okupansi:</span></div>
                <div class="stat-card-number-row">
                    <span class="stat-card-num-big">{{ $totalTerisi }}</span>
                    <span class="stat-card-num-sub">/ {{ $totalKapasitas }} peserta</span>
                </div>
                <div class="stat-card-bar-wrap">
                    <div class="stat-card-bar-fill" style="width: {{ $globalPersen }}%;"></div>
                </div>
                <div class="stat-card-countdown">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>
                        @if($isPendaftaranBuka && $sisaHariTutup >= 0)
                            Ditutup dalam {{ $sisaHariTutup }} hari
                        @elseif($isPendaftaranBuka)
                            Pendaftaran sedang dibuka
                        @else
                            Pendaftaran ditutup
                        @endif
                    </span>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================================================
         3. CONTROL SUB-BAR: SELECTOR BULAN & TOMBOL AKSI DI BAWAH KOTAKAN BIRU
         ========================================================================= --}}
    @if($bulanList->isNotEmpty())
        <div class="month-switch-strip">
            <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.45rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0641CC" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span style="font-weight: 700; color: #1E293B;">Bulan Magang:</span>
                </div>
                <form method="GET" action="{{ route('admin.kuota.index') }}" style="display: flex; align-items: center; margin: 0;">
                    <select name="bulan_magang_id" onchange="this.form.submit()">
                        @foreach($bulanList as $b)
                            <option value="{{ $b->id }}" {{ $selectedBulan && $selectedBulan->id === $b->id ? 'selected' : '' }}>
                                {{ $b->nama_bulan ? $b->nama_bulan . ' (' . $b->bulan_mulai?->translatedFormat('F Y') . ')' : $b->bulan_mulai?->translatedFormat('F Y') }} {{ $b->aktif ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="kuota-page-actions">
                @if($selectedBulan)
                    <button type="button" onclick="openUbahBulanModal()" class="btn-outline-action">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                        <span>Ubah bulan</span>
                    </button>
                @endif

                <button type="button" onclick="openBuatBulanModal()" class="btn-primary-action">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Buat bulan baru</span>
                </button>
            </div>
        </div>
    @endif

    {{-- =========================================================================
         4. SECTION & TABEL KUOTA PER BIDANG
         ========================================================================= --}}
    <div>
        <div class="section-header-row">
            <div>
                <h2 class="section-title">Kuota per bidang</h2>
                <p class="section-desc">Tentukan jumlah kuota, jurusan, jenjang, dan rentang durasi magang.</p>
            </div>
            @if($selectedBulan)
                <button type="button" onclick="openTambahKuotaModal()" class="btn-primary-action">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Tambah kuota</span>
                </button>
            @endif
        </div>

        <div class="card-table-wrap" style="margin-top: 1rem;">
            <div style="overflow-x: auto;">
                <table class="table-bidang">
                    <thead>
                        <tr>
                            <th style="width: 220px;">Bidang</th>
                            <th>Jurusan yang Dibutuhkan</th>
                            <th style="width: 140px;">Jenjang</th>
                            <th style="width: 120px;">Durasi</th>
                            <th style="width: 150px;">Kuota Terisi</th>
                            <th style="width: 120px;">Kapasitas</th>
                            <th style="width: 90px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bidangList as $item)
                            <tr>
                                {{-- 1. Bidang --}}
                                <td>
                                    <div class="bidang-cell">
                                        <div class="avatar-initial" style="background: {{ $item['bg_color'] }}; color: {{ $item['text_color'] }};">
                                            {{ $item['inisial'] }}
                                        </div>
                                        <span class="bidang-name">{{ $item['nama'] }}</span>
                                    </div>
                                </td>

                                {{-- 2. Jurusan yang Dibutuhkan --}}
                                <td>
                                    <div class="jurusan-text-muted">
                                        {{ $item['jurusan_names'] }}
                                    </div>
                                </td>

                                {{-- 3. Jenjang --}}
                                <td>
                                    <div class="jenjang-badge-group">
                                        @foreach($item['jenjang_badges'] as $badge)
                                            <span class="badge-jenjang-pill">{{ $badge }}</span>
                                        @endforeach
                                    </div>
                                </td>

                                {{-- 4. Durasi --}}
                                <td>
                                    <span style="font-weight: 600; font-size: 0.8125rem;">
                                        {{ $item['durasi'] }}
                                    </span>
                                </td>

                                {{-- 5. Kuota Terisi --}}
                                <td>
                                    <div class="kuota-occupancy-wrap">
                                        <div class="occupancy-numbers">
                                            <span class="occupancy-ratio">{{ $item['terisi'] }}/{{ $item['kapasitas'] }}</span>
                                            <span class="occupancy-percent">{{ $item['persen'] }}%</span>
                                        </div>
                                        <div class="mini-progress-bar">
                                            <div class="mini-progress-fill" style="width: {{ $item['persen'] }}%;"></div>
                                        </div>
                                    </div>
                                </td>

                                {{-- 6. Kapasitas --}}
                                <td>
                                    <span class="kapasitas-text">{{ $item['kapasitas'] }} peserta</span>
                                </td>

                                {{-- 7. Aksi --}}
                                <td style="text-align: right;">
                                    <button type="button" 
                                            onclick="openEditBidangModal({{ $item['id'] }}, '{{ addslashes($item['nama']) }}', {{ json_encode($item['jurusan_list']->map(fn($j) => ['id' => $j->id, 'nama' => $j->nama_jurusan, 'jumlah' => $selectedBulan?->kuota->where('jurusan_id', $j->id)->first()?->jumlah ?? 0, 'terisi' => $selectedBulan?->kuota->where('jurusan_id', $j->id)->first()?->terisi ?? 0])) }})" 
                                            class="btn-edit-action">
                                        Ubah
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem; color: #64748B;">
                                    Belum ada data bidang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- =====================================================================
             5. ALERT VALIDASI PEMBIMBING AKTIF
             ===================================================================== --}}
        <x-gentelella.callout type="info" class="alert-validation-box" title="Validasi Kapasitas Pembimbing">
            Sistem otomatis memvalidasi agar total kuota tidak melebihi kapasitas bimbingan, yaitu jumlah Pembimbing aktif di setiap bidang.
        </x-gentelella.callout>
    </div>

</div>

{{-- =========================================================================
     MODAL 1: UBAH BULAN (JADWAL PENDAFTARAN & BULAN MULAI)
     ========================================================================= --}}
{{-- =========================================================================
     MODAL 1: UBAH BULAN (JADWAL PENDAFTARAN & BULAN MULAI)
     ========================================================================= --}}
@if($selectedBulan)
<div id="modalUbahBulan" class="modal-overlay">
    <div class="modal-card" style="max-width: 480px; padding: 1.75rem;">
        <div class="modal-header-row" style="align-items: flex-start; margin-bottom: 1.25rem; padding-bottom: 0.75rem;">
            <div>
                <h3 class="modal-title" style="font-size: 1.25rem; font-weight: 800; color: #0F172A;">Ubah bulan</h3>
                <p class="modal-header-desc" style="font-size: 0.8125rem; color: #64748B; margin: 0.25rem 0 0 0;">Tentukan jadwal pendaftaran dan mulai magang.</p>
            </div>
            <button type="button" onclick="closeUbahBulanModal()" class="modal-btn-close-box" aria-label="Tutup">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.kuota.bulan.update', $selectedBulan->id) }}" id="formUbahBulan">
            @csrf
            @method('PUT')

            {{-- 1. Nama Bulan --}}
            <div class="form-row-group">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Nama bulan</label>
                <input type="text" 
                       name="nama_bulan" 
                       id="ubahNamaBulan"
                       value="{{ $selectedBulan->nama_bulan }}" 
                       placeholder="Contoh: Gelombang 2" 
                       style="height: 42px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
            </div>

            {{-- 2. Tanggal Buka & Tanggal Tutup (Grid 2 Kolom) --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem;">
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Tanggal buka</label>
                    <div class="input-calendar-wrap">
                        <input type="date" 
                               name="tgl_buka" 
                               id="ubahBulanTglBuka"
                               value="{{ $selectedBulan->tgl_buka ? \Carbon\Carbon::parse($selectedBulan->tgl_buka)->format('Y-m-d') : '' }}"
                               required 
                               placeholder="Pilih tanggal">
                        <span class="input-calendar-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Tanggal tutup</label>
                    <div class="input-calendar-wrap">
                        <input type="date" 
                               name="tgl_tutup" 
                               id="ubahBulanTglTutup"
                               value="{{ $selectedBulan->tgl_tutup ? \Carbon\Carbon::parse($selectedBulan->tgl_tutup)->format('Y-m-d') : '' }}"
                               required 
                               placeholder="Pilih tanggal">
                        <span class="input-calendar-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            {{-- 3. Tanggal Mulai Magang --}}
            <div class="form-row-group">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Tanggal mulai magang</label>
                <div class="input-calendar-wrap">
                    <input type="date" 
                           name="bulan_mulai" 
                           id="ubahBulanMulai"
                           value="{{ $selectedBulan->bulan_mulai?->format('Y-m-d') }}"
                           required 
                           placeholder="Pilih tanggal">
                    <span class="input-calendar-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </span>
                </div>
                <span class="form-helper-text">Harus setelah tanggal tutup pendaftaran.</span>
            </div>

            {{-- 4. Status Bulan (Segmented Pills) --}}
            <div class="form-row-group" style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.4rem;">Status bulan</label>
                <input type="hidden" name="status_awal" id="statusUbahInput" value="{{ $selectedBulan->aktif ? 'dibuka' : 'draft' }}">
                <div class="status-pills-group">
                    <button type="button" 
                            id="btnUbahStatusDraft"
                            onclick="setStatusUbahBulan('draft')" 
                            class="btn-status-pill {{ ! $selectedBulan->aktif ? 'active' : '' }}">
                        <span id="checkIconUbahDraft" style="display: {{ ! $selectedBulan->aktif ? 'inline-flex' : 'none' }}; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span>Draft</span>
                    </button>
                    <button type="button" 
                            id="btnUbahStatusDibuka"
                            onclick="setStatusUbahBulan('dibuka')" 
                            class="btn-status-pill {{ $selectedBulan->aktif ? 'active' : '' }}">
                        <span id="checkIconUbahDibuka" style="display: {{ $selectedBulan->aktif ? 'inline-flex' : 'none' }}; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span>Dibuka</span>
                    </button>
                </div>
            </div>

            {{-- 5. Alert Callout Box --}}
            <div class="modal-callout-info">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Perubahan jadwal pendaftaran dan mulai magang akan langsung disinkronkan ke portal publik.</span>
            </div>

            {{-- 6. Footer Buttons --}}
            <div class="modal-footer-btns" style="margin-top: 1.5rem; padding-top: 1rem; border-top: none;">
                <button type="button" onclick="closeUbahBulanModal()" class="btn-outline-action" style="padding: 0.6rem 1.35rem; font-weight: 600;">
                    Batal
                </button>
                <button type="submit" id="btnSubmitUbahBulan" class="btn-primary-action" style="padding: 0.6rem 1.4rem; font-weight: 700;">
                    Simpan perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- =========================================================================
     MODAL 2: BUAT BULAN BARU
     ========================================================================= --}}
<div id="modalBuatBulan" class="modal-overlay">
    <div class="modal-card" style="max-width: 480px; padding: 1.75rem;">
        <div class="modal-header-row" style="align-items: flex-start; margin-bottom: 1.25rem; padding-bottom: 0.75rem;">
            <div>
                <h3 class="modal-title" style="font-size: 1.25rem; font-weight: 800; color: #0F172A;">Buat bulan baru</h3>
                <p class="modal-header-desc" style="font-size: 0.8125rem; color: #64748B; margin: 0.25rem 0 0 0;">Tentukan jadwal pendaftaran dan mulai magang.</p>
            </div>
            <button type="button" onclick="closeBuatBulanModal()" class="modal-btn-close-box" aria-label="Tutup">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.kuota.bulan.store') }}" id="formBuatBulan">
            @csrf

            {{-- 1. Nama Bulan --}}
            <div class="form-row-group">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Nama bulan</label>
                <input type="text" 
                       name="nama_bulan" 
                       placeholder="Contoh: Gelombang 3" 
                       style="height: 42px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
            </div>

            {{-- 2. Tanggal Buka & Tanggal Tutup (Grid 2 Kolom) --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem;">
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Tanggal buka</label>
                    <div class="input-calendar-wrap">
                        <input type="date" 
                               name="tgl_buka" 
                               id="buatBulanTglBuka"
                               required 
                               placeholder="Pilih tanggal">
                        <span class="input-calendar-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Tanggal tutup</label>
                    <div class="input-calendar-wrap">
                        <input type="date" 
                               name="tgl_tutup" 
                               id="buatBulanTglTutup"
                               required 
                               placeholder="Pilih tanggal">
                        <span class="input-calendar-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            {{-- 3. Tanggal Mulai Magang --}}
            <div class="form-row-group">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Tanggal mulai magang</label>
                <div class="input-calendar-wrap">
                    <input type="date" 
                           name="bulan_mulai" 
                           id="buatBulanMulai"
                           required 
                           placeholder="Pilih tanggal">
                    <span class="input-calendar-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </span>
                </div>
                <span class="form-helper-text">Harus setelah tanggal tutup pendaftaran.</span>
            </div>

            {{-- 4. Status Awal (Segmented Pills) --}}
            <div class="form-row-group" style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.4rem;">Status awal</label>
                <input type="hidden" name="status_awal" id="statusAwalInput" value="draft">
                <div class="status-pills-group">
                    <button type="button" 
                            id="btnStatusDraft"
                            onclick="setStatusAwal('draft')" 
                            class="btn-status-pill active">
                        <span id="checkIconDraft" style="display: inline-flex; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span>Draft</span>
                    </button>
                    <button type="button" 
                            id="btnStatusDibuka"
                            onclick="setStatusAwal('dibuka')" 
                            class="btn-status-pill">
                        <span id="checkIconDibuka" style="display: none; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span>Dibuka</span>
                    </button>
                </div>
            </div>

            {{-- 5. Alert Callout Box --}}
            <div class="modal-callout-info">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Setelah bulan magang disimpan, atur kuota tiap bidang lewat tombol Tambah kuota.</span>
            </div>

            {{-- 6. Footer Buttons --}}
            <div class="modal-footer-btns" style="margin-top: 1.5rem; padding-top: 1rem; border-top: none;">
                <button type="button" onclick="closeBuatBulanModal()" class="btn-outline-action" style="padding: 0.6rem 1.35rem; font-weight: 600;">
                    Batal
                </button>
                <button type="submit" class="btn-primary-action" style="padding: 0.6rem 1.4rem; font-weight: 700;">
                    Simpan bulan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =========================================================================
     MODAL 3: TAMBAH KUOTA JURUSAN
     ========================================================================= --}}
@if($selectedBulan)
<div id="modalTambahKuota" class="modal-overlay modal-tambah-kuota">
    <div class="modal-card" style="max-width: 520px; padding: 1.75rem;">
        <div class="modal-header-row" style="align-items: flex-start; margin-bottom: 1.25rem; padding-bottom: 0.75rem;">
            <div>
                <h3 class="modal-title" style="font-size: 1.25rem; font-weight: 800; color: #0F172A;">Tambah kuota <span class="sr-only">Alokasikan Kuota Jurusan</span></h3>
                <p class="modal-header-desc" style="font-size: 0.8125rem; color: #64748B; margin: 0.25rem 0 0 0;">Atur kuota, jurusan, jenjang, dan durasi untuk satu bidang.</p>
            </div>
            <button type="button" onclick="closeTambahKuotaModal()" class="modal-btn-close-box" aria-label="Tutup">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.kuota.store') }}" id="formTambahKuota">
            @csrf
            <input type="hidden" name="bulan_magang_id" value="{{ $selectedBulan->id }}">

            {{-- 1. Baris 1: Bulan (Read-Only) & Bidang (Dropdown) --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem;">
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Bulan</label>
                    <input type="text" 
                           value="{{ $selectedBulan->nama_bulan ?? ($selectedBulan->bulan_mulai?->translatedFormat('F Y') ?? 'Gelombang 1') }}" 
                           disabled 
                           style="background: #F8FAFC; color: #475569; font-weight: 600; cursor: not-allowed;">
                </div>
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Bidang <span style="color: #DC2626;">*</span></label>
                    <select name="bidang_id" id="tambahKuotaBidangSelect" onchange="onBidangSelected(this.value)" required>
                        @foreach($bidangList as $b)
                            <option value="{{ $b['id'] }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $b['nama'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 2. Baris 2: Jurusan yang Dibutuhkan (Chips) --}}
            <div class="form-row-group">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.35rem;">Jurusan yang dibutuhkan</label>
                <div class="jurusan-chips-wrap" id="jurusanChipsContainer">
                    {{-- Dinamis diisi via JavaScript --}}
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.4rem;">
                    <button type="button" class="btn-add-jurusan-chip" onclick="toggleExtraJurusanSelect()">
                        <span>+ Jurusan</span>
                    </button>
                    <select id="extraJurusanSelect" onchange="addExtraJurusan(this)" style="display: none; height: 34px; font-size: 0.8125rem; padding: 0 0.5rem; border-radius: 6px; border: 1px solid #CBD5E1;">
                        <option value="">-- Pilih Jurusan Lain --</option>
                        @foreach($jurusanList as $jur)
                            <option value="{{ $jur->id }}" data-nama="{{ $jur->nama_jurusan }}">
                                {{ $jur->nama_jurusan }} ({{ strtoupper($jur->kategori) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="hiddenJurusanInputs"></div>
            </div>

            {{-- 3. Baris 3: Jenjang --}}
            <div class="form-row-group">
                <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B; margin-bottom: 0.35rem;">Jenjang</label>
                <div class="jenjang-pills-wrap">
                    <button type="button" id="pillJenjangSMK" onclick="toggleJenjang('SMK')" class="btn-jenjang-pill">
                        <span class="jenjang-check" style="display: none;">✓ </span>SMK
                    </button>
                    <button type="button" id="pillJenjangD3" onclick="toggleJenjang('D3')" class="btn-jenjang-pill">
                        <span class="jenjang-check" style="display: none;">✓ </span>D3
                    </button>
                    <button type="button" id="pillJenjangS1" onclick="toggleJenjang('S1')" class="btn-jenjang-pill active">
                        <span class="jenjang-check" style="display: inline;">✓ </span>S1
                    </button>
                </div>
                <input type="hidden" name="jenjang[]" id="inputJenjangSMK" value="" disabled>
                <input type="hidden" name="jenjang[]" id="inputJenjangD3" value="" disabled>
                <input type="hidden" name="jenjang[]" id="inputJenjangS1" value="S1">
            </div>

            {{-- 4. Baris 4: Jumlah Kuota, Durasi Minimum, Durasi Maksimum (3 Kolom) --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Jumlah kuota <span style="color: #DC2626;">*</span></label>
                    <input type="number" 
                           name="jumlah" 
                           id="tambahKuotaJumlahInput" 
                           min="1" 
                           max="100" 
                           value="3" 
                           oninput="checkKapasitasLimit()" 
                           required>
                </div>
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Durasi minimum</label>
                    <select name="durasi_min">
                        <option value="1 bulan">1 bulan</option>
                        <option value="2 bulan">2 bulan</option>
                        <option value="3 bulan" selected>3 bulan</option>
                        <option value="4 bulan">4 bulan</option>
                        <option value="5 bulan">5 bulan</option>
                        <option value="6 bulan">6 bulan</option>
                    </select>
                </div>
                <div class="form-row-group">
                    <label style="font-size: 0.8125rem; font-weight: 700; color: #1E293B;">Durasi maksimum</label>
                    <select name="durasi_max">
                        <option value="1 bulan">1 bulan</option>
                        <option value="2 bulan">2 bulan</option>
                        <option value="3 bulan">3 bulan</option>
                        <option value="4 bulan">4 bulan</option>
                        <option value="5 bulan">5 bulan</option>
                        <option value="6 bulan" selected>6 bulan</option>
                    </select>
                </div>
            </div>

            {{-- 5. Baris 5: Warning Box Peringatan Kapasitas Pembimbing --}}
            <div id="boxWarningKapasitas" class="box-capacity-warning" style="display: none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <div>
                    <strong>Melebihi kapasitas pembimbing</strong>
                    <span id="textWarningKapasitasDetail">
                        Kapasitas Persandian 12 peserta (2 Pembimbing x 6) dan kuota terpasang sudah 6, sisa kapasitas 6. Kurangi jumlah kuota yang ditambahkan atau tambah Pembimbing aktif di bidang ini.
                    </span>
                </div>
            </div>

            {{-- 6. Footer Buttons --}}
            <div class="modal-footer-btns" style="margin-top: 1.5rem; padding-top: 1rem; border-top: none;">
                <button type="button" onclick="closeTambahKuotaModal()" class="btn-outline-action" style="padding: 0.6rem 1.35rem; font-weight: 600;">
                    Batal
                </button>
                <button type="submit" id="btnSubmitTambahKuota" class="btn-primary-action" style="padding: 0.6rem 1.4rem; font-weight: 700;">
                    Simpan kuota
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- =========================================================================
     MODAL 4: UBAH KUOTA PER BIDANG
     ========================================================================= --}}
<div id="modalEditBidang" class="modal-overlay modal-edit-kuota">
    <div class="modal-card" style="max-width: 580px;">
        <div class="modal-header-row">
            <div>
                <h3 class="modal-title" id="editBidangTitle">Ubah Kuota Bidang</h3>
                <small style="color: #64748B; font-size: 0.75rem;">Sesuaikan alokasi kursi peserta untuk kejuruan di bidang ini.</small>
            </div>
            <button type="button" onclick="closeEditBidangModal()" class="modal-btn-close">&times;</button>
        </div>
        <form id="formEditBidang" method="POST" action="">
            @csrf
            @method('PUT')
            <input type="hidden" name="bulan_magang_id" value="{{ $selectedBulan?->id }}">

            <div id="jurusanAllocList" style="display: flex; flex-direction: column; gap: 0.85rem; max-height: 340px; overflow-y: auto; padding-right: 0.25rem;">
                {{-- Dinamis diisi via JavaScript --}}
            </div>

            <div class="modal-footer-btns">
                <button type="button" onclick="closeEditBidangModal()" class="btn-outline-action">Batal</button>
                <button type="submit" class="btn-primary-action">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUbahBulanModal() {
        const modal = document.getElementById('modalUbahBulan');
        if (modal) { modal.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    }
    function closeUbahBulanModal() {
        const modal = document.getElementById('modalUbahBulan');
        if (modal) { modal.style.display = 'none'; document.body.style.overflow = ''; }
    }

    function setStatusAwal(status) {
        const input = document.getElementById('statusAwalInput');
        const btnDraft = document.getElementById('btnStatusDraft');
        const btnDibuka = document.getElementById('btnStatusDibuka');
        const iconDraft = document.getElementById('checkIconDraft');
        const iconDibuka = document.getElementById('checkIconDibuka');

        if (input) input.value = status;

        if (status === 'draft') {
            if (btnDraft) btnDraft.classList.add('active');
            if (btnDibuka) btnDibuka.classList.remove('active');
            if (iconDraft) iconDraft.style.display = 'inline-flex';
            if (iconDibuka) iconDibuka.style.display = 'none';
        } else {
            if (btnDraft) btnDraft.classList.remove('active');
            if (btnDibuka) btnDibuka.classList.add('active');
            if (iconDraft) iconDraft.style.display = 'none';
            if (iconDibuka) iconDibuka.style.display = 'inline-flex';
        }
    }

    function openBuatBulanModal() {
        const modal = document.getElementById('modalBuatBulan');
        if (modal) { 
            modal.style.display = 'flex'; 
            document.body.style.overflow = 'hidden'; 
            
            // Pasang event listener untuk validasi tanggal dinamis
            const tglBuka = document.getElementById('buatBulanTglBuka');
            const tglTutup = document.getElementById('buatBulanTglTutup');
            const tglMulai = document.getElementById('buatBulanMulai');

            if (tglBuka && tglTutup) {
                tglBuka.addEventListener('change', function() {
                    if (this.value) {
                        tglTutup.min = this.value;
                        if (tglTutup.value && tglTutup.value < this.value) {
                            tglTutup.value = this.value;
                        }
                    }
                });
            }

            if (tglTutup && tglMulai) {
                tglTutup.addEventListener('change', function() {
                    if (this.value) {
                        const nextDay = new Date(this.value);
                        nextDay.setDate(nextDay.getDate() + 1);
                        const isoStr = nextDay.toISOString().split('T')[0];
                        tglMulai.min = isoStr;
                        if (tglMulai.value && tglMulai.value <= this.value) {
                            tglMulai.value = isoStr;
                        }
                    }
                });
            }
        }
    }
    function closeBuatBulanModal() {
        const modal = document.getElementById('modalBuatBulan');
        if (modal) { modal.style.display = 'none'; document.body.style.overflow = ''; }
    }

    function setStatusUbahBulan(status) {
        const input = document.getElementById('statusUbahInput');
        const btnDraft = document.getElementById('btnUbahStatusDraft');
        const btnDibuka = document.getElementById('btnUbahStatusDibuka');
        const iconDraft = document.getElementById('checkIconUbahDraft');
        const iconDibuka = document.getElementById('checkIconUbahDibuka');

        if (input) input.value = status;

        if (status === 'draft') {
            if (btnDraft) btnDraft.classList.add('active');
            if (btnDibuka) btnDibuka.classList.remove('active');
            if (iconDraft) iconDraft.style.display = 'inline-flex';
            if (iconDibuka) iconDibuka.style.display = 'none';
        } else {
            if (btnDraft) btnDraft.classList.remove('active');
            if (btnDibuka) btnDibuka.classList.add('active');
            if (iconDraft) iconDraft.style.display = 'none';
            if (iconDibuka) iconDibuka.style.display = 'inline-flex';
        }
    }

    /* Master Data Bidang & Interaksi Modal Tambah Kuota */
    const masterBidangData = @json($bidangJsonData ?? []);
    let activeJurusanIds = [];

    function onBidangSelected(bidangId) {
        const bidang = masterBidangData.find(b => b.id == bidangId);
        const container = document.getElementById('jurusanChipsContainer');
        if (!bidang || !container) return;

        container.innerHTML = '';
        activeJurusanIds = [];

        if (bidang.jurusans && bidang.jurusans.length > 0) {
            bidang.jurusans.forEach(j => {
                activeJurusanIds.push(j.id);
                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'chip-jurusan';
                chip.id = 'chipJurusan_' + j.id;
                chip.onclick = function() { toggleJurusanChip(j.id, this); };
                chip.innerHTML = `<span class="chip-check">✓ </span><span>${j.nama}</span>`;
                container.appendChild(chip);
            });
        } else {
            container.innerHTML = '<span style="font-size: 0.8125rem; color: #64748B;">Belum ada kejuruan terikat di bidang ini.</span>';
        }

        updateHiddenJurusanInputs();
        checkKapasitasLimit();
    }

    function toggleJurusanChip(jurusanId, element) {
        const idx = activeJurusanIds.indexOf(jurusanId);
        const checkSpan = element.querySelector('.chip-check');

        if (idx > -1) {
            // Uncheck
            activeJurusanIds.splice(idx, 1);
            element.classList.add('inactive');
            if (checkSpan) checkSpan.style.display = 'none';
        } else {
            // Check
            activeJurusanIds.push(jurusanId);
            element.classList.remove('inactive');
            if (checkSpan) checkSpan.style.display = 'inline';
        }

        updateHiddenJurusanInputs();
    }

    function toggleExtraJurusanSelect() {
        const select = document.getElementById('extraJurusanSelect');
        if (!select) return;
        select.style.display = select.style.display === 'none' ? 'inline-block' : 'none';
        if (select.style.display !== 'none') select.focus();
    }

    function addExtraJurusan(selectEl) {
        const jurId = parseInt(selectEl.value);
        if (!jurId) return;

        const opt = selectEl.options[selectEl.selectedIndex];
        const jurNama = opt.getAttribute('data-nama') || opt.text;
        const container = document.getElementById('jurusanChipsContainer');

        if (!activeJurusanIds.includes(jurId)) {
            activeJurusanIds.push(jurId);
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'chip-jurusan';
            chip.id = 'chipJurusan_' + jurId;
            chip.onclick = function() { toggleJurusanChip(jurId, this); };
            chip.innerHTML = `<span class="chip-check">✓ </span><span>${jurNama}</span>`;
            if (container) container.appendChild(chip);
            updateHiddenJurusanInputs();
        }

        selectEl.value = '';
        selectEl.style.display = 'none';
    }

    function updateHiddenJurusanInputs() {
        const hiddenWrap = document.getElementById('hiddenJurusanInputs');
        if (!hiddenWrap) return;
        hiddenWrap.innerHTML = '';
        activeJurusanIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'jurusan_ids[]';
            input.value = id;
            hiddenWrap.appendChild(input);
        });

        // Pastikan fallback single jurusan_id jika ada
        if (activeJurusanIds.length > 0) {
            const singleInput = document.createElement('input');
            singleInput.type = 'hidden';
            singleInput.name = 'jurusan_id';
            singleInput.value = activeJurusanIds[0];
            hiddenWrap.appendChild(singleInput);
        }
    }

    function toggleJenjang(jenjangCode) {
        const pill = document.getElementById('pillJenjang' + jenjangCode);
        const input = document.getElementById('inputJenjang' + jenjangCode);
        if (!pill || !input) return;

        const checkSpan = pill.querySelector('.jenjang-check');
        const isActive = pill.classList.contains('active');

        if (isActive) {
            pill.classList.remove('active');
            input.disabled = true;
            if (checkSpan) checkSpan.style.display = 'none';
        } else {
            pill.classList.add('active');
            input.disabled = false;
            input.value = jenjangCode;
            if (checkSpan) checkSpan.style.display = 'inline';
        }
    }

    function checkKapasitasLimit() {
        const bidangSelect = document.getElementById('tambahKuotaBidangSelect');
        const jumlahInput = document.getElementById('tambahKuotaJumlahInput');
        const boxWarning = document.getElementById('boxWarningKapasitas');
        const textDetail = document.getElementById('textWarningKapasitasDetail');

        if (!bidangSelect || !jumlahInput || !boxWarning || !textDetail) return;

        const bidangId = bidangSelect.value;
        const bidang = masterBidangData.find(b => b.id == bidangId);
        const inputVal = parseInt(jumlahInput.value) || 0;

        if (!bidang) {
            boxWarning.style.display = 'none';
            return;
        }

        if (inputVal > bidang.sisa_kapasitas) {
            boxWarning.style.display = 'flex';
            textDetail.innerText = `Kapasitas ${bidang.nama} ${bidang.kapasitas_bimbingan} peserta (${bidang.pembimbing_count} Pembimbing x 6) dan kuota terpasang sudah ${bidang.kuota_terpasang}, sisa kapasitas ${bidang.sisa_kapasitas}. Kurangi jumlah kuota yang ditambahkan atau tambah Pembimbing aktif di bidang ini.`;
        } else {
            boxWarning.style.display = 'none';
        }
    }

    function openTambahKuotaModal() {
        const modal = document.getElementById('modalTambahKuota');
        if (modal) { 
            modal.style.display = 'flex'; 
            document.body.style.overflow = 'hidden'; 
            const bidangSelect = document.getElementById('tambahKuotaBidangSelect');
            if (bidangSelect && bidangSelect.value) {
                onBidangSelected(bidangSelect.value);
            }
        }
    }
    function closeTambahKuotaModal() {
        const modal = document.getElementById('modalTambahKuota');
        if (modal) { modal.style.display = 'none'; document.body.style.overflow = ''; }
    }

    function openEditBidangModal(bidangId, namaBidang, jurusans) {
        const modal = document.getElementById('modalEditBidang');
        const form = document.getElementById('formEditBidang');
        const title = document.getElementById('editBidangTitle');
        const listWrap = document.getElementById('jurusanAllocList');

        if (title) title.innerText = 'Ubah Kuota: ' + namaBidang;
        if (form) form.action = '/admin/kuota/bidang/' + bidangId;

        if (listWrap) {
            listWrap.innerHTML = '';
            if (!jurusans || jurusans.length === 0) {
                listWrap.innerHTML = '<p style="color: #64748B; font-size: 0.85rem; text-align: center; padding: 1.5rem 0;">Belum ada kejuruan terikat di bidang ini.</p>';
            } else {
                jurusans.forEach(j => {
                    const row = document.createElement('div');
                    row.style = 'display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 0.65rem 0.85rem; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px;';
                    row.innerHTML = `
                        <div style="flex: 1;">
                            <strong style="font-size: 0.875rem; color: #0F172A; display: block;">${j.nama}</strong>
                            <small style="color: #64748B; font-size: 0.725rem;">Terisi: ${j.terisi} kursi</small>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <label style="font-size: 0.75rem; color: #475569; font-weight: 600;">Kuota:</label>
                            <input type="number" 
                                   name="alokasi[${j.id}]" 
                                   value="${j.jumlah}" 
                                   min="${j.terisi}" 
                                   max="100" 
                                   required 
                                   style="width: 75px; height: 36px; padding: 0 0.5rem; text-align: center; font-weight: 700; border: 1px solid #CBD5E1; border-radius: 6px;">
                        </div>
                    `;
                    listWrap.appendChild(row);
                });
            }
        }

        if (modal) { modal.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    }

    function closeEditBidangModal() {
        const modal = document.getElementById('modalEditBidang');
        if (modal) { modal.style.display = 'none'; document.body.style.overflow = ''; }
    }

    // Escape listener
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeUbahBulanModal();
            closeBuatBulanModal();
            closeTambahKuotaModal();
            closeEditBidangModal();
        }
    });

    // Backdrop click listener
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeUbahBulanModal();
                closeBuatBulanModal();
                closeTambahKuotaModal();
                closeEditBidangModal();
            }
        });
    });
</script>
@endsection
