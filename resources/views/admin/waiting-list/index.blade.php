@extends('layouts.dashboard')

@section('title', 'Antrean Waiting List - Admin MagKom Sleman')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #64748B;">
        <a href="{{ route('admin.dashboard') }}" style="color: #64748B; text-decoration: none;">Dashboard</a>
        <span>/</span>
        <span style="color: #0F172A; font-weight: 700;">Antrean Waiting List</span>
    </nav>
@endsection

@section('content')
@php
    $totalWaiting = $waitingList->total();
    
    // Hitung peserta yang kuotanya sudah terbuka pada halaman saat ini
    $siapPromosiCount = 0;
    foreach ($waitingList as $item) {
        $kuotaModel = $item->kuota;
        if ($kuotaModel && ($kuotaModel->jumlah - $kuotaModel->terisi) > 0) {
            $siapPromosiCount++;
        }
    }

    $periodeCount = $bulanAntreanList->count();
@endphp

<div class="adm-waiting-wrapper">

    <style>
        /* ==========================================================================
           MAGKOM ADMIN ANTREAN WAITING LIST - SIGNATURE THEME
           Theme: Dinas Komunikasi dan Informatika Kabupaten Sleman
           ========================================================================== */
        .adm-waiting-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            max-width: 1440px;
            margin: 0 auto;
            width: 100%;
            color: #1E293B;
            font-family: inherit;
        }

        /* 1. Signature Hero Banner */
        .adm-hero {
            background: linear-gradient(95deg, rgba(6, 65, 204, 0.96) 0%, rgba(6, 65, 204, 0.88) 45%, rgba(6, 65, 204, 0.38) 100%),
                        url('{{ asset('images/dashboard_hero_bg.jpg') }}') right center / cover no-repeat;
            border-radius: 16px;
            padding: 32px 36px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(6, 65, 204, 0.25), 0 8px 10px -6px rgba(6, 65, 204, 0.15);
            min-height: 190px;
        }

        .adm-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 85% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 60%);
            pointer-events: none;
        }

        .adm-hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 24px;
        }

        .adm-hero-left {
            max-width: 760px;
        }

        .adm-hero-pill-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .adm-hero-badge-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: #ffffff;
            border-radius: 9999px;
            font-size: 0.725rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .adm-hero-rule-pill {
            display: inline-block;
            padding: 4px 12px;
            background: #ffffff;
            color: #0641CC;
            border-radius: 9999px;
            font-size: 0.725rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .adm-hero-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            margin: 0 0 6px 0;
            line-height: 1.25;
        }

        .adm-hero-desc {
            font-size: 0.925rem;
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            line-height: 1.5;
        }

        .adm-hero-chips {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .adm-hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(8px);
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 0.785rem;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .adm-hero-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 12px;
        }
        @media (max-width: 900px) {
            .adm-hero-right { align-items: flex-start; }
        }

        .adm-hero-date-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            padding: 6px 16px;
            border-radius: 9999px;
            font-size: 0.825rem;
            font-weight: 600;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        /* 2. Unified KPI Strip Card */
        .adm-kpi-unified-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }
        @media (max-width: 1024px) {
            .adm-kpi-unified-card { grid-template-columns: repeat(2, 1fr); }
            .adm-kpi-unified-item:nth-child(2)::after { display: none; }
            .adm-kpi-unified-item:nth-child(1),
            .adm-kpi-unified-item:nth-child(2) { border-bottom: 1px solid #E2E8F0; }
        }
        @media (max-width: 600px) {
            .adm-kpi-unified-card { grid-template-columns: 1fr; }
            .adm-kpi-unified-item::after { display: none; }
            .adm-kpi-unified-item:not(:last-child) { border-bottom: 1px solid #E2E8F0; }
        }

        .adm-kpi-unified-item {
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: relative;
            transition: background-color 0.15s ease;
        }
        .adm-kpi-unified-item:hover {
            background-color: #F8FAFC;
        }
        .adm-kpi-unified-item:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 20%;
            height: 60%;
            width: 1px;
            background-color: #E2E8F0;
        }

        .adm-kpi-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .adm-kpi-icon-blue { background: #EFF6FF; color: #0641CC; }
        .adm-kpi-icon-green { background: #D1FAE5; color: #059669; }
        .adm-kpi-icon-amber { background: #FEF3C7; color: #D97706; }
        .adm-kpi-icon-indigo { background: #EEF2FF; color: #4F46E5; }

        .adm-kpi-details {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
        }
        .adm-kpi-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 2px;
        }
        .adm-kpi-val {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0F172A;
            line-height: 1.2;
        }
        .adm-kpi-hint {
            font-size: 0.75rem;
            color: #64748B;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* 3. Toolbar Pencarian & Filter */
        .adm-filter-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 1.5rem 1.75rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .adm-filter-grid {
            display: grid;
            grid-template-columns: 2.5fr 1.3fr 1.3fr auto;
            gap: 1rem;
            align-items: flex-end;
        }
        @media (max-width: 1100px) {
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
            gap: 6px;
        }
        .adm-field-group label {
            font-size: 0.785rem;
            font-weight: 600;
            color: #475569;
        }

        .adm-search-input-wrap {
            position: relative;
            width: 100%;
        }
        .adm-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            pointer-events: none;
        }

        .adm-input-search {
            height: 42px;
            width: 100%;
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            padding: 0 14px 0 40px;
            font-size: 0.875rem;
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
            border-radius: 10px;
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
            border-radius: 10px;
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
        }

        .adm-btn-filter-reset {
            height: 42px;
            padding: 0 1rem;
            background: #F8FAFC;
            color: #64748B;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #E2E8F0;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .adm-btn-filter-reset:hover {
            background: #F1F5F9;
            color: #0F172A;
        }

        /* Bulk Closure Strip */
        .adm-bulk-closure-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding-top: 1rem;
            border-top: 1px solid #F1F5F9;
            font-size: 0.8125rem;
        }

        .adm-btn-closure {
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .adm-btn-closure.can-close {
            background: #FEE2E2;
            color: #DC2626;
            border-color: #FECACA;
        }
        .adm-btn-closure.can-close:hover {
            background: #FCA5A5;
            color: #991B1B;
        }
        .adm-btn-closure.disabled {
            background: #F1F5F9;
            color: #94A3B8;
            border-color: #E2E8F0;
            cursor: not-allowed;
        }

        /* 4. Spacious Table Card */
        .adm-table-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .adm-table-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
            background: #FFFFFF;
        }

        .adm-table-card-title {
            font-size: 1rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .adm-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }
        .adm-table th {
            background: #F8FAFC;
            padding: 0.95rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748B;
            border-bottom: 1px solid #E2E8F0;
        }
        .adm-table td {
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            color: #1E293B;
        }
        .adm-table tbody tr {
            transition: background-color 0.15s ease;
        }
        .adm-table tbody tr:hover {
            background-color: #F8FAFC;
        }

        .adm-fifo-rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-weight: 800;
            font-size: 0.8125rem;
        }
        .adm-fifo-rank-1 {
            background: #0641CC;
            color: #FFFFFF;
            box-shadow: 0 2px 6px rgba(6, 65, 204, 0.35);
        }
        .adm-fifo-rank-other {
            background: #F1F5F9;
            color: #475569;
            border: 1px solid #E2E8F0;
        }

        .adm-candidate-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #EFF6FF;
            color: #0641CC;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            border: 1px solid #DBEAFE;
            flex-shrink: 0;
        }

        .adm-btn-action-promote {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 14px;
            background: #10B981;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.8rem;
            box-shadow: 0 1px 3px rgba(16, 185, 129, 0.25);
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .adm-btn-action-promote:hover {
            background: #059669;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.35);
            transform: translateY(-1px);
        }

        .adm-btn-action-review {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 14px;
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            color: #0641CC;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.8rem;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .adm-btn-action-review:hover {
            border-color: #0641CC;
            background: #EFF6FF;
            transform: translateY(-1px);
        }

        /* =====================================================================
           DARK MODE OVERRIDES (DS-MagKom Standards)
           ===================================================================== */
        [data-theme="dark"] .adm-waiting-wrapper {
            color: #E2E8F0;
        }

        [data-theme="dark"] .adm-kpi-unified-card {
            background-color: #182234;
            border-color: #26354D;
        }

        [data-theme="dark"] .adm-kpi-unified-item:hover {
            background-color: #162238;
        }

        [data-theme="dark"] .adm-kpi-unified-item:not(:last-child)::after {
            background-color: #26354D;
        }

        [data-theme="dark"] .adm-kpi-label {
            color: #94A3B8;
        }

        [data-theme="dark"] .adm-kpi-val {
            color: #F8FAFC;
        }

        [data-theme="dark"] .adm-filter-card {
            background-color: #182234 !important;
            border-color: #26354D !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) !important;
        }

        [data-theme="dark"] .adm-field-group label {
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .adm-input-search,
        [data-theme="dark"] .adm-select {
            background-color: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .adm-input-search:focus,
        [data-theme="dark"] .adm-select:focus {
            border-color: #3B82F6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
        }

        [data-theme="dark"] .adm-select option {
            background-color: #0F172A !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .adm-btn-filter-reset {
            background-color: #111C2E !important;
            border-color: #26354D !important;
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .adm-btn-filter-reset:hover {
            background-color: #162238 !important;
            color: #F8FAFC !important;
            border-color: #3B82F6 !important;
        }

        [data-theme="dark"] .adm-bulk-closure-strip {
            border-top-color: #26354D !important;
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .adm-bulk-closure-strip div[style*="color: #475569"] {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .adm-btn-closure.disabled {
            background-color: #111C2E !important;
            border-color: #26354D !important;
            color: #64748B !important;
        }

        [data-theme="dark"] .adm-table-card {
            background-color: #182234 !important;
            border-color: #26354D !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) !important;
        }

        [data-theme="dark"] .adm-table-card-header {
            background-color: #141E30 !important;
            border-bottom-color: #26354D !important;
        }

        [data-theme="dark"] .adm-table-card-title {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .adm-table th {
            background-color: #0E1726 !important;
            color: #94A3B8 !important;
            border-bottom-color: #26354D !important;
        }

        [data-theme="dark"] .adm-table td {
            background-color: #182234 !important;
            color: #CBD5E1 !important;
            border-bottom-color: #26354D !important;
        }

        [data-theme="dark"] .adm-table tbody tr:hover td {
            background-color: #162238 !important;
        }

        [data-theme="dark"] .adm-fifo-rank-other {
            background-color: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .adm-candidate-avatar {
            background-color: rgba(37, 99, 235, 0.25) !important;
            border-color: rgba(59, 130, 246, 0.3) !important;
            color: #93C5FD !important;
        }

        [data-theme="dark"] .adm-table td div[style*="color: #0F172A"],
        [data-theme="dark"] .adm-table td div[style*="color: #1E293B"] {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .adm-table td div[style*="color: #64748B"] {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .adm-table td[style*="font-family: ui-monospace"] {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .adm-btn-action-review {
            background-color: #111C2E !important;
            border-color: #26354D !important;
            color: #93C5FD !important;
        }

        [data-theme="dark"] .adm-btn-action-review:hover {
            background-color: #162238 !important;
            border-color: #3B82F6 !important;
            color: #60A5FA !important;
        }

        [data-theme="dark"] .adm-table-card div[style*="background: #ffffff"] {
            background-color: #141E30 !important;
            border-top-color: #26354D !important;
        }
    </style>

    {{-- ==========================================================================
       1. SIGNATURE HERO BANNER
       ========================================================================== --}}
    <div class="adm-hero">
        <div class="adm-hero-content">
            <div class="adm-hero-left">
                <div class="adm-hero-pill-row">
                    <span class="adm-hero-badge-tag">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Penegakan Aturan FIFO (BR-05)
                    </span>
                    <span class="adm-hero-rule-pill">
                        Sistem Cadangan Kuota
                    </span>
                </div>
                <h1 class="adm-hero-title">Antrean Daftar Tunggu (Waiting List)</h1>
                <p class="adm-hero-desc">
                    Pengelolaan antrean cadangan peserta saat kuota jurusan penuh berdasarkan waktu pengajuan secara transparan, adil, dan objektif.
                </p>
                <div class="adm-hero-chips">
                    <div class="adm-hero-chip">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>{{ number_format($totalWaiting) }} Calon Peserta Menunggu</span>
                    </div>
                    @if($siapPromosiCount > 0)
                        <div class="adm-hero-chip" style="background: rgba(16, 185, 129, 0.25); border-color: rgba(16, 185, 129, 0.5);">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981;"></span>
                            <span>{{ $siapPromosiCount }} Siap Dipromosikan</span>
                        </div>
                    @endif
                    <div class="adm-hero-chip">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                        <span>{{ $periodeCount }} Bulan Magang Terdampak</span>
                    </div>
                </div>
            </div>

            <div class="adm-hero-right">
                <div class="adm-hero-date-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <span>Prioritas Otomatis Sesuai Timestamp Submit</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================================================
       2. UNIFIED KPI STRIP CARD (METRIK TERPADU 4-KOLOM)
       ========================================================================== --}}
    <div class="adm-kpi-unified-card">
        {{-- KPI 1: Total Waiting List --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Total Antrean Tunggu</span>
                <span class="adm-kpi-val">{{ number_format($totalWaiting) }}</span>
                <span class="adm-kpi-hint">Calon peserta terdaftar FIFO</span>
            </div>
        </div>

        {{-- KPI 2: Siap Dipromosikan --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box {{ $siapPromosiCount > 0 ? 'adm-kpi-icon-green' : 'adm-kpi-icon-amber' }}">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Siap Dipromosikan</span>
                <span class="adm-kpi-val {{ $siapPromosiCount > 0 ? 'text-emerald-600' : 'text-slate-900' }}">{{ number_format($siapPromosiCount) }}</span>
                <span class="adm-kpi-hint {{ $siapPromosiCount > 0 ? 'text-emerald-600' : 'text-slate-500' }}">
                    {{ $siapPromosiCount > 0 ? 'Kursi kuota telah terbuka' : 'Menunggu kuota terbuka' }}
                </span>
            </div>
        </div>

        {{-- KPI 3: Kuota Tertahan --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-amber">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Tertahan Kuota Penuh</span>
                <span class="adm-kpi-val text-amber-600">{{ number_format(max(0, $totalWaiting - $siapPromosiCount)) }}</span>
                <span class="adm-kpi-hint">Menunggu penambahan kursi dinas</span>
            </div>
        </div>

        {{-- KPI 4: Periode Terdampak --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-indigo">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Bulan Magang</span>
                <span class="adm-kpi-val" style="color: #4F46E5;">{{ $periodeCount }}</span>
                <span class="adm-kpi-hint">Bulan dengan antrean aktif</span>
            </div>
        </div>
    </div>

    {{-- ==========================================================================
       3. TOOLBAR PENCARIAN & KONTROL PENUTUPAN MASSAL
       ========================================================================== --}}
    <div class="adm-filter-card">
        <form method="GET" action="{{ route('admin.waiting-list.index') }}" class="adm-filter-grid">
            <div class="adm-field-group adm-field-search">
                <label for="search-input">Pencarian Calon Peserta</label>
                <div class="adm-search-input-wrap">
                    <svg class="adm-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="search-input" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NIM/NISN, atau asal institusi..." class="adm-input-search">
                </div>
            </div>

            <div class="adm-field-group">
                <label for="select-bulan">Bulan Magang</label>
                <select name="bulan_magang_id" id="select-bulan" class="adm-select">
                    <option value="">Semua Bulan Magang</option>
                    @foreach($bulanAntreanList as $b)
                        <option value="{{ $b->id }}" {{ request('bulan_magang_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->bulan_mulai?->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="adm-field-group">
                <label for="select-jurusan">Kejuruan Pilihan</label>
                <select name="jurusan_id" id="select-jurusan" class="adm-select">
                    <option value="">Semua Kejuruan</option>
                    @foreach($jurusanList ?? [] as $j)
                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama_jurusan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="adm-filter-actions">
                <button type="submit" class="adm-btn-filter-submit">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'bulan_magang_id', 'jurusan_id']))
                    <a href="{{ route('admin.waiting-list.index') }}" class="adm-btn-filter-reset">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Panel Penutupan Antrean Massal Awal Bulan (AC-04-7) --}}
        @if(isset($bulanAntreanList) && $bulanAntreanList->isNotEmpty())
            <div class="adm-bulk-closure-strip">
                <div style="display: flex; align-items: center; gap: 8px; color: #475569;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span><strong>Penutupan Massal Awal Bulan (AC-04-7):</strong> Sisa antrean ditolak otomatis jika bulan magang telah berjalan.</span>
                </div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    @foreach($bulanAntreanList as $bAntrean)
                        @php
                            $hariIni = today('Asia/Jakarta');
                            $tglMulai = \Carbon\Carbon::parse($bAntrean->bulan_mulai . '-01', 'Asia/Jakarta');
                            $bisaTutup = $hariIni->gte($tglMulai);
                        @endphp
                        <form method="POST" action="{{ route('admin.waiting-list.tutup', $bAntrean) }}" onsubmit="return confirm('Apakah Anda yakin menutup sisa antrean waiting list bulan {{ $bAntrean->bulan_mulai?->translatedFormat('F Y') }}? Pendaftaran yang tersisa akan ditolak secara otomatis.');" style="display: inline;">
                            @csrf
                            <button type="submit" {{ !$bisaTutup ? 'disabled' : '' }} class="adm-btn-closure {{ $bisaTutup ? 'can-close' : 'disabled' }}" title="{{ $bisaTutup ? 'Tutup sisa antrean bulan ini' : 'Hanya bisa ditutup setelah tanggal 1 bulan mulai magang' }}">
                                Tutup Antrean {{ $bAntrean->bulan_mulai?->translatedFormat('M Y') }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- ==========================================================================
       4. SPACIOUS TABLE CARD
       ========================================================================== --}}
    <div class="adm-table-card">
        <div class="adm-table-card-header">
            <div>
                <h3 class="adm-table-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span>Daftar Prioritas Antrean Calon Peserta</span>
                </h3>
                <p style="font-size: 0.8125rem; color: #64748B; margin: 3px 0 0 0;">
                    Urutan prioritas terjamin ketat berdasarkan stempel waktu pengajuan awal (FIFO)
                </p>
            </div>
            <span style="font-size: 0.8125rem; font-weight: 700; color: #0641CC; background: #EFF6FF; padding: 4px 12px; border-radius: 9999px; border: 1px solid #DBEAFE;">
                {{ $waitingList->total() }} Calon Peserta Menunggu
            </span>
        </div>

        @if($waitingList->isEmpty())
            <div style="padding: 2rem 1.5rem;">
                <x-empty-state 
                    title="Tidak Ada Antrean Waiting List" 
                    description="Saat ini tidak ada pendaftaran calon peserta magang yang tertahan dalam antrean tunggu." 
                />
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th style="width: 80px; text-align: center;">Urutan</th>
                            <th style="width: 150px;">No. Registrasi</th>
                            <th>Calon Peserta</th>
                            <th>Instansi &amp; Kejuruan</th>
                            <th>Bulan Magang</th>
                            <th style="text-align: center;">Status Kuota</th>
                            <th>Waktu Submit (FIFO)</th>
                            <th style="text-align: right; width: 140px;">Tindak Lanjut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($waitingList as $index => $item)
                            @php
                                $kuotaModel = $item->kuota;
                                $sisaKuota = $kuotaModel ? max(0, $kuotaModel->jumlah - $kuotaModel->terisi) : 0;
                                $urutanNomor = $waitingList->firstItem() + $index;
                                
                                $namaInitial = '';
                                if ($item->user?->nama) {
                                    $w = explode(' ', trim($item->user->nama));
                                    $namaInitial = strtoupper(substr($w[0], 0, 1) . (isset($w[1]) ? substr($w[1], 0, 1) : ''));
                                }
                            @endphp
                            <tr>
                                {{-- Urutan FIFO --}}
                                <td style="text-align: center;">
                                    <span class="adm-fifo-rank-badge {{ $urutanNomor === 1 ? 'adm-fifo-rank-1' : 'adm-fifo-rank-other' }}">
                                        #{{ $urutanNomor }}
                                    </span>
                                </td>

                                {{-- No. Registrasi --}}
                                <td>
                                    <x-badge variant="info">
                                        {{ $item->nomor_pendaftaran }}
                                    </x-badge>
                                </td>

                                {{-- Calon Peserta --}}
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="adm-candidate-avatar">
                                            {{ $namaInitial ?: 'MG' }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #0F172A; font-size: 0.875rem;">
                                                {{ $item->user?->nama }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: #64748B; margin-top: 2px;">
                                                {{ $item->user?->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Instansi & Jurusan --}}
                                <td>
                                    <div style="font-weight: 600; color: #0F172A; font-size: 0.85rem;">
                                        {{ $item->asal_instansi ?? ($item->user?->profil?->institusi ?? '-') }}
                                    </div>
                                    <div style="font-size: 0.775rem; color: #64748B; margin-top: 2px;">
                                        {{ $kuotaModel?->jurusan?->nama_jurusan ?? '-' }}
                                    </div>
                                </td>

                                {{-- Periode --}}
                                <td>
                                    <div style="font-weight: 700; color: #1E293B; font-size: 0.85rem;">
                                        {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #64748B;">
                                        {{ $item->durasi_bulan }} Bulan
                                    </div>
                                </td>

                                {{-- Status Kuota --}}
                                <td style="text-align: center;">
                                    @if($sisaKuota > 0)
                                        <x-badge variant="success" dot>
                                            Tersedia {{ $sisaKuota }} Kursi
                                        </x-badge>
                                    @else
                                        <x-badge variant="secondary">
                                            Kuota Penuh
                                        </x-badge>
                                    @endif
                                </td>

                                {{-- Waktu Submit (FIFO) --}}
                                <td style="color: #64748B; font-family: ui-monospace, SFMono-Regular, monospace; font-size: 0.8125rem;">
                                    {{ $item->diajukan_at ? $item->diajukan_at->format('d/m/Y H:i:s') : '-' }}
                                </td>

                                {{-- Aksi --}}
                                <td style="text-align: right;">
                                    @if($sisaKuota > 0)
                                        <form method="POST" action="{{ route('admin.waiting-list.promote') }}" style="display: inline-block; margin: 0;" onsubmit="return confirm('Promosikan {{ addslashes($item->user?->nama ?? 'peserta') }} menjadi Diterima secara instan sesuai kuota yang tersedia?');">
                                            @csrf
                                            <input type="hidden" name="pendaftaran_id" value="{{ $item->id }}">
                                            <button type="submit" class="adm-btn-action-promote" style="border: none; cursor: pointer;" title="Promosikan 1-klik langsung sesuai kuota kosong">
                                                <span>Promosikan</span>
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.pendaftaran.show', $item) }}" class="adm-btn-action-review" title="Tinjau berkas pendaftaran calon peserta">
                                            <span>Tinjau</span>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($waitingList->hasPages())
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid #E2E8F0; background: #ffffff;">
                    {{ $waitingList->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
