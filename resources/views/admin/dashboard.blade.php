@extends('layouts.dashboard')

@section('title', 'Dashboard Operasional Administrasi - MagKom Sleman')

@section('content')
<div class="adm-cockpit-wrapper">

    <style>
        /* ==========================================================================
           MAGKOM ADMIN OPERATIONAL DASHBOARD - CLEAN, SIMPLE & AESTHETIC
           Standard: Dinas Komunikasi dan Informatika Kabupaten Sleman
           ========================================================================== */
        .adm-cockpit-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            max-width: 1440px;
            margin: 0 auto;
            width: 100%;
            color: #1E293B;
            font-family: inherit;
        }

        /* 1. SIGNATURE HERO BANNER (Persis Tema Peserta MagKom) */
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
            max-width: 720px;
        }

        .adm-hero-pill-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .adm-hero-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: #10B981;
            color: #ffffff;
            border-radius: 9999px;
            font-size: 0.725rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .adm-hero-role-pill {
            display: inline-block;
            padding: 4px 12px;
            background: rgba(255, 255, 255, 0.95);
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

        .adm-hero-meta-badges {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-top: 14px;
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
            .adm-hero-right {
                align-items: flex-start;
            }
        }

        .adm-hero-date-badge {
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

        .adm-hero-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .adm-hero-glass-btn {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: #ffffff;
            padding: 8px 18px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .adm-hero-glass-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.6);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .adm-hero-glass-btn.btn-solid {
            background: #ffffff;
            color: #0641CC;
            border-color: #ffffff;
        }
        .adm-hero-glass-btn.btn-solid:hover {
            background: #F8FAFC;
            color: #0533A8;
        }

        /* 2. Sub-Bar Control (Periode Selector) */
        .adm-control-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 0.1rem 0.25rem;
        }
        .adm-period-form {
            display: flex;
            align-items: center;
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            padding: 0.35rem 0.85rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            gap: 0.5rem;
        }
        .adm-period-form label {
            font-size: 0.775rem;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin: 0;
            white-space: nowrap;
        }
        .adm-period-form select {
            border: none;
            background: transparent;
            font-size: 0.85rem;
            font-weight: 700;
            color: #0F172A;
            outline: none;
            cursor: pointer;
            padding-right: 0.5rem;
        }

        /* 3. UNIFIED KPI STRIP CARD (Panel Metrik Terpadu) */
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
        .adm-kpi-icon-amber { background: #FEF3C7; color: #D97706; }
        .adm-kpi-icon-blue { background: #EFF6FF; color: #0641CC; }
        .adm-kpi-icon-purple { background: #F5F3FF; color: #7C3AED; }
        .adm-kpi-icon-emerald { background: #ECFDF5; color: #10B981; }

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

        /* ==========================================================================
           ZONA 1: ANTREAN VERIFIKASI UTAMA (FULL-WIDTH SPATIOUS CARD)
           ========================================================================== */
        .adm-card-full {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .adm-card-header-spacious {
            padding: 1.35rem 1.75rem;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            background: #FFFFFF;
        }
        .adm-section-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            letter-spacing: -0.015em;
        }
        .adm-section-subtitle {
            font-size: 0.825rem;
            color: #64748B;
            margin: 0.2rem 0 0 0;
        }

        /* Filter Pills Minimalis */
        .adm-filter-pills {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            background: #F1F5F9;
            padding: 0.25rem;
            border-radius: 8px;
        }
        .adm-pill-btn {
            border: none;
            background: transparent;
            font-size: 0.775rem;
            font-weight: 700;
            color: #64748B;
            padding: 0.35rem 0.85rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .adm-pill-btn.active {
            background: #FFFFFF;
            color: #0641CC;
            box-shadow: 0 1px 2px rgba(0,0,0,0.06);
        }

        /* Spacious Table */
        .adm-table-spacious {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }
        .adm-table-spacious th {
            background: #F8FAFC;
            padding: 0.95rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748B;
            border-bottom: 1px solid #E2E8F0;
            white-space: nowrap;
        }
        .adm-table-spacious td {
            padding: 1.15rem 1.5rem;
            border-bottom: 1px solid #E2E8F0;
            vertical-align: middle;
            color: #1E293B;
            background: #FFFFFF;
            transition: background 0.15s ease;
        }
        .adm-table-spacious tr:hover td {
            background: #F8FAFC;
        }
        .adm-table-spacious tr:last-child td {
            border-bottom: none;
        }

        .adm-user-cell-spacious {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }
        .adm-avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #EFF6FF;
            color: #0641CC;
            border: 1px solid #BFDBFE;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .adm-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.725rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .adm-status-badge.diajukan {
            background: #FEF3C7;
            color: #92400E;
            border: 1px solid #FCD34D;
        }
        .adm-status-badge.perbaikan {
            background: #EFF6FF;
            color: #1E40AF;
            border: 1px solid #BFDBFE;
        }

        .adm-sla-pill-norm {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748B;
            background: #F1F5F9;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
        }
        .adm-sla-pill-alert {
            font-size: 0.75rem;
            font-weight: 700;
            color: #DC2626;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
        }

        .adm-btn-action-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.95rem;
            background: #0641CC;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.8rem;
            box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);
            transition: all 0.15s ease;
        }
        .adm-btn-action-primary:hover {
            background: #0533A8;
            transform: translateY(-1px);
            color: #ffffff;
        }

        /* ==========================================================================
           ZONA 2: DUA KOLOM SEIMBANG (50% : 50% GRID BERSIH & RAPI)
           ========================================================================== */
        .adm-balanced-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            align-items: start;
        }
        @media (max-width: 960px) {
            .adm-balanced-grid {
                grid-template-columns: 1fr;
            }
        }

        .adm-clean-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }
        .adm-clean-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .adm-clean-card-body {
            padding: 1.5rem;
        }

        /* Sleek Quota List Rows (Minimalis & Elegan) */
        .adm-quota-list-clean {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .adm-quota-row-item {
            padding: 0.85rem 1rem;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            transition: all 0.15s ease;
        }
        .adm-quota-row-item:hover {
            background: #FFFFFF;
            border-color: #CBD5E1;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }

        .adm-quota-info {
            flex: 1;
            min-width: 0;
        }
        .adm-quota-title-text {
            font-weight: 700;
            font-size: 0.9rem;
            color: #0F172A;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .adm-quota-cat-badge {
            font-size: 0.7rem;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .adm-quota-meter-box {
            width: 140px;
            flex-shrink: 0;
        }
        .adm-quota-meter-track {
            width: 100%;
            height: 6px;
            background: #E2E8F0;
            border-radius: 999px;
            overflow: hidden;
            margin-top: 0.35rem;
        }
        .adm-quota-meter-fill {
            height: 100%;
            border-radius: 999px;
        }

        .adm-quota-status-pill {
            font-size: 0.725rem;
            font-weight: 800;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* Sub-Sections in Right Card */
        .adm-sub-section {
            margin-bottom: 1.5rem;
        }
        .adm-sub-section:last-child {
            margin-bottom: 0;
        }
        .adm-sub-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 0.75rem 0;
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        /* Mentor Item */
        .adm-mentor-clean-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            margin-bottom: 0.65rem;
        }
        .adm-mentor-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #DBEAFE;
            color: #1E40AF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        /* Quick Action Bar */
        .adm-action-link-clean {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            text-decoration: none;
            color: #1E293B;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.15s ease;
            margin-bottom: 0.45rem;
        }
        .adm-action-link-clean:hover {
            border-color: #BFDBFE;
            background: #F8FAFC;
            color: #0641CC;
            transform: translateX(3px);
        }

        .adm-link-more-clean {
            font-size: 0.825rem;
            font-weight: 700;
            color: #0641CC;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .adm-link-more-clean:hover {
            text-decoration: underline;
        }
    </style>

    {{-- ==========================================================================
       1. SIGNATURE HERO BANNER (BACKGROUND GAMBAR RESMI MAGKOM)
       ========================================================================== --}}
    <div class="adm-hero">
        <div class="adm-hero-content">
            <div class="adm-hero-left">
                <div class="adm-hero-pill-row">
                    <span class="adm-hero-status-pill">● Sistem Operasional Aktif</span>
                    <span class="adm-hero-role-pill">Administrator MagKom</span>
                </div>
                
                <h1 class="adm-hero-title">
                    Selamat Bertugas, {{ Auth::user()->nama ?? Auth::user()->name ?? 'Administrator' }}! 👋
                </h1>
                
                <p class="adm-hero-desc">
                    Pusat Kendali Administrasi Magang &ndash; Dinas Komunikasi dan Informatika Kabupaten Sleman
                </p>

                <div class="adm-hero-meta-badges">
                    @if($bulanAktif)
                        <div class="adm-hero-chip">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span>Periode: <strong>{{ $bulanAktif->bulan_mulai?->translatedFormat('F Y') }}</strong></span>
                        </div>
                        <div class="adm-hero-chip">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>Kapasitas: <strong>{{ $totalKuotaTerisi }}/{{ $totalKuotaUmum }} Kursi Terisi</strong></span>
                        </div>
                    @endif
                    <div class="adm-hero-chip">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        <span>Menunggu Verifikasi: <strong>{{ $pengajuanMenunggu }} Berkas</strong></span>
                    </div>
                </div>
            </div>

            <div class="adm-hero-right">
                <div class="adm-hero-date-badge">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>

                <div class="adm-hero-actions">
                    <a href="{{ route('admin.sertifikat.index') }}" class="adm-hero-glass-btn btn-solid">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        <span>Hub Sertifikat</span>
                    </a>
                    <a href="{{ route('admin.laporan.index') }}" class="adm-hero-glass-btn">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>Ekspor Laporan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================================================
       2. CONTROL SUB-BAR (PILIHAN PERIODE)
       ========================================================================== --}}
    <div class="adm-control-bar">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="adm-period-form">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0641CC" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <label for="selectBulan">Pilih Periode:</label>
            <select name="bulan_id" id="selectBulan" onchange="this.form.submit()">
                @foreach($daftarBulan as $b)
                    <option value="{{ $b->id }}" {{ $bulanAktif && $bulanAktif->id === $b->id ? 'selected' : '' }}>
                        {{ $b->bulan_mulai?->translatedFormat('F Y') }} {{ $b->aktif ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>

        <div style="font-size: 0.825rem; color: #64748B; font-weight: 600;">
            Pantau aktivitas pendaftaran dan beban bimbingan secara real-time
        </div>
    </div>

    {{-- ==========================================================================
       3. UNIFIED KPI STRIP CARD (PANEL METRIK TERPADU)
       ========================================================================== --}}
    <div class="adm-kpi-unified-card">
        
        {{-- Item 1: Menunggu Verifikasi --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-amber">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Menunggu Verifikasi</span>
                <span class="adm-kpi-val">{{ number_format($pengajuanMenunggu) }}</span>
                @if($pengajuanSlaKritisCount > 0)
                    <span class="adm-kpi-hint" style="color: #DC2626; font-weight: 700;">
                        ⚠️ {{ $pengajuanSlaKritisCount }} tertahan &gt; 3 hari
                    </span>
                @else
                    <span class="adm-kpi-hint">Berkas baru masuk</span>
                @endif
            </div>
        </div>

        {{-- Item 2: Revisi Dokumen --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Revisi Dokumen</span>
                <span class="adm-kpi-val">{{ number_format($perluPerbaikan) }}</span>
                <span class="adm-kpi-hint">Menunggu revisi pemohon</span>
            </div>
        </div>

        {{-- Item 3: Antrean Cadangan --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-purple">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Antrean Cadangan</span>
                <span class="adm-kpi-val">{{ number_format($waitingListCount) }}</span>
                <span class="adm-kpi-hint">Daftar tunggu antrean</span>
            </div>
        </div>

        {{-- Item 4: Peserta Magang Aktif --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-emerald">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Peserta Magang Aktif</span>
                <span class="adm-kpi-val">{{ number_format($pesertaAktifCount) }}</span>
                <span class="adm-kpi-hint">Sedang bertugas di dinas</span>
            </div>
        </div>

    </div>

    {{-- 3b. QUICK OPERATIONAL EFFICIENCY METRICS (A11) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
        <div class="admin-card" style="padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #0641CC;">
            <div>
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500, #64748B); text-transform: uppercase;">Kecepatan Verifikasi Berkas</span>
                <div style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin-top: 0.15rem;">
                    {{ $avgVerifikasiHari }} <span style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-500, #64748B);">Hari Kalender</span>
                </div>
                <span style="font-size: 0.72rem; color: #16A34A; font-weight: 700;">✓ SLA Sesuai Target (Maks. 3 Hari)</span>
            </div>
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
        </div>

        <div class="admin-card" style="padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #10B981;">
            <div>
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500, #64748B); text-transform: uppercase;">Tingkat Keterisian Kuota</span>
                <div style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin-top: 0.15rem;">
                    {{ $realisasiKuotaPersen }}% <span style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-500, #64748B);">Realisasi Alokasi</span>
                </div>
                <span style="font-size: 0.72rem; color: var(--neutral-600, #475569); font-weight: 600;">Total Kuota Aktif Dinas Terproteksi</span>
            </div>
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
            </div>
        </div>

        <div class="admin-card" style="padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #7C3AED;">
            <div>
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-500, #64748B); text-transform: uppercase;">Pendaftar 7 Hari Terakhir</span>
                <div style="font-size: 1.35rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin-top: 0.15rem;">
                    {{ number_format($pendaftaranMingguIni) }} <span style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-500, #64748B);">Pengajuan Baru</span>
                </div>
                <span style="font-size: 0.72rem; color: #7C3AED; font-weight: 700;">Aktivitas Masuk Mingguan</span>
            </div>
            <div style="width: 42px; height: 42px; border-radius: 10px; background: #F5F3FF; color: #7C3AED; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
            </div>
        </div>
    </div>

    {{-- ==========================================================================
       ZONA 1: ANTREAN VERIFIKASI UTAMA (FULL-WIDTH SPATIOUS CARD)
       ========================================================================== --}}
    <div class="adm-card-full">
        <div class="adm-card-header-spacious">
            <div>
                <h2 class="adm-section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0641CC" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    <span>Antrean Verifikasi Berkas Pendaftaran</span>
                </h2>
                <p class="adm-section-subtitle">Tinjau dan proses berkas pendaftaran calon peserta magang yang membutuhkan verifikasi.</p>
            </div>

            {{-- Quick Filter Pills --}}
            <div class="adm-filter-pills">
                <button type="button" class="adm-pill-btn active" onclick="filterQueueTable('all', this)">
                    Semua ({{ $pendaftaranTerbaru->count() }})
                </button>
                <button type="button" class="adm-pill-btn" onclick="filterQueueTable('diajukan', this)">
                    Diajukan
                </button>
                <button type="button" class="adm-pill-btn" onclick="filterQueueTable('perlu_perbaikan', this)">
                    Perlu Perbaikan
                </button>
            </div>
        </div>

        @if($pendaftaranTerbaru->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: #64748B;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: #ECFDF5; color: #10B981; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.85rem auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <h4 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0 0 0.25rem 0;">Semua Berkas Sudah Selesai Diverifikasi</h4>
                <p style="font-size: 0.85rem; margin: 0;">Tidak ada berkas yang menunggu tindak lanjut saat ini.</p>
            </div>
        @else
            <div style="overflow-x: auto; width: 100%;">
                <table class="adm-table-spacious" id="queueTable">
                    <thead>
                        <tr>
                            <th>Calon Peserta</th>
                            <th>Institusi & Jurusan</th>
                            <th>Periode Magang</th>
                            <th>Usia Pengajuan</th>
                            <th style="text-align: center;">Status Berkas</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaranTerbaru as $item)
                            @php
                                $tgl = $item->diajukan_at ?? $item->created_at;
                                $umur = (int) $tgl->diffInDays(now());
                                $isCritical = $umur >= 3;
                                $initial = strtoupper(substr($item->user?->nama ?? 'P', 0, 1));
                                $statusClass = $item->status->value === 'diajukan' ? 'status-diajukan' : 'status-perlu-perbaikan';
                            @endphp
                            <tr class="queue-row {{ $statusClass }}">
                                <td>
                                    <div class="adm-user-cell-spacious">
                                        <div class="adm-avatar-circle">{{ $initial }}</div>
                                        <div>
                                            <div style="font-weight: 700; color: #0F172A; font-size: 0.925rem;">{{ $item->user?->nama }}</div>
                                            <small style="color: #64748B; font-family: monospace;">{{ $item->nomor_pendaftaran }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1E293B;">{{ $item->user?->profil?->institusi ?? '-' }}</div>
                                    <small style="color: #64748B;">{{ $item->kuota?->jurusan?->nama_jurusan }}</small>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: #334155;">
                                        {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                    </span>
                                </td>
                                <td>
                                    @if($isCritical)
                                        <span class="adm-sla-pill-alert">⚠️ {{ $umur }} hari lalu</span>
                                    @else
                                        <span class="adm-sla-pill-norm">{{ $umur === 0 ? 'Hari ini' : $umur . ' hari lalu' }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($item->status->value === 'diajukan')
                                        <span class="adm-status-badge diajukan">Diajukan</span>
                                    @else
                                        <span class="adm-status-badge perbaikan">Perlu Perbaikan</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.pendaftaran.show', $item) }}" class="adm-btn-action-primary">
                                        <span>Tinjau Berkas</span>
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div style="padding: 1rem 1.75rem; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end;">
            <a href="{{ route('admin.pendaftaran.index') }}" class="adm-link-more-clean">
                <span>Lihat Seluruh Arsip Pendaftaran</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
        </div>
    </div>

    {{-- ==========================================================================
       ZONA 2: DUA KOLOM SEIMBANG (50% : 50% GRID BERSIH & RAPI)
       ========================================================================== --}}
    <div class="adm-balanced-grid">

        {{-- KOLOM KIRI: MONITORING KUOTA JURUSAN (SLEEK LIST ROWS) --}}
        <div class="adm-clean-card">
            <div class="adm-clean-card-header">
                <div>
                    <h3 class="adm-section-title" style="font-size: 0.975rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0641CC" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                        <span>Alokasi & Keterisian Kuota Jurusan</span>
                    </h3>
                    <p class="adm-section-subtitle">
                        Realisasi kursi magang periode {{ $bulanAktif ? $bulanAktif->bulan_mulai?->translatedFormat('F Y') : 'Aktif' }}.
                    </p>
                </div>
                <a href="{{ route('admin.kuota.index') }}" class="adm-link-more-clean">
                    <span>Kelola Kuota &rarr;</span>
                </a>
            </div>

            <div class="adm-clean-card-body">
                {{-- Total Capacity Strip --}}
                @if($bulanAktif)
                    @php
                        $persenTotal = $totalKuotaUmum > 0 ? min(100, round(($totalKuotaTerisi / $totalKuotaUmum) * 100)) : 0;
                    @endphp
                    <div style="padding: 1rem 1.15rem; background: #F0F7FF; border: 1px solid #BFDBFE; border-radius: 12px; margin-bottom: 1.25rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.825rem; font-weight: 700; color: #1E40AF; margin-bottom: 0.4rem;">
                            <span>Total Kuota Umum Terisi</span>
                            <span>{{ $totalKuotaTerisi }} / {{ $totalKuotaUmum }} Kursi ({{ $persenTotal }}%)</span>
                        </div>
                        <div style="width: 100%; height: 7px; background: #DBEAFE; border-radius: 999px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $persenTotal }}%; background: #0641CC; border-radius: 999px;"></div>
                        </div>
                    </div>
                @endif

                {{-- Sleek List Rows --}}
                @if($kuotaList->isEmpty())
                    <p style="margin: 0; font-size: 0.85rem; color: #64748B; text-align: center;">Belum ada kuota jurusan yang dikonfigurasi untuk periode ini.</p>
                @else
                    <div class="adm-quota-list-clean">
                        @foreach($kuotaList as $kuota)
                            @php
                                $pct = $kuota->jumlah > 0 ? min(100, round(($kuota->terisi / $kuota->jumlah) * 100)) : 0;
                                $sisa = max(0, $kuota->jumlah - $kuota->terisi);
                                $color = $pct >= 100 ? '#DC2626' : ($pct >= 75 ? '#F59E0B' : '#0641CC');
                                $pillBg = $sisa > 0 ? '#ECFDF5' : '#FEF2F2';
                                $pillColor = $sisa > 0 ? '#047857' : '#DC2626';
                            @endphp
                            <div class="adm-quota-row-item">
                                <div class="adm-quota-info">
                                    <span class="adm-quota-title-text">{{ $kuota->jurusan?->nama_jurusan }}</span>
                                    <span class="adm-quota-cat-badge">{{ $kuota->jurusan?->kategori === 'smk' ? 'SMK' : 'Perguruan Tinggi' }}</span>
                                </div>

                                <div class="adm-quota-meter-box">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #64748B; font-weight: 600;">
                                        <span>{{ $kuota->terisi }}/{{ $kuota->jumlah }}</span>
                                        <span style="color: {{ $color }}; font-weight: 700;">{{ $pct }}%</span>
                                    </div>
                                    <div class="adm-quota-meter-track">
                                        <div class="adm-quota-meter-fill" style="width: {{ $pct }}%; background: {{ $color }};"></div>
                                    </div>
                                </div>

                                <span class="adm-quota-status-pill" style="background: {{ $pillBg }}; color: {{ $pillColor }};">
                                    {{ $sisa > 0 ? 'Sisa ' . $sisa . ' Kursi' : 'Penuh' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- KOLOM KANAN: STATUS OPERASIONAL & TIM TERPADU --}}
        <div class="adm-clean-card">
            <div class="adm-clean-card-header">
                <div>
                    <h3 class="adm-section-title" style="font-size: 0.975rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0641CC" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>Status Operasional & Tim Kerja</span>
                    </h3>
                    <p class="adm-section-subtitle">Ringkasan jadwal pendaftaran dan kapasitas pembimbing dinas.</p>
                </div>
            </div>

            <div class="adm-clean-card-body">
                
                {{-- 1. Status Periode & Countdown --}}
                @if($bulanAktif)
                    @php
                        $tglTutup = $bulanAktif->tgl_tutup ? \Carbon\Carbon::parse($bulanAktif->tgl_tutup) : null;
                        $tglBuka = $bulanAktif->tgl_buka ? \Carbon\Carbon::parse($bulanAktif->tgl_buka) : null;
                        $isBuka = $tglBuka && $tglTutup && now()->between($tglBuka, $tglTutup);
                        $sisaHari = $tglTutup && $tglTutup->isFuture() ? (int) now()->diffInDays($tglTutup, false) : 0;
                    @endphp
                    <div class="adm-sub-section">
                        <div class="adm-sub-title">
                            <span>Jadwal Pendaftaran Periode Ini</span>
                        </div>
                        <div style="padding: 0.85rem 1rem; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-size: 0.875rem; font-weight: 700; color: #0F172A; display: block;">
                                    Batas: {{ $tglTutup ? $tglTutup->translatedFormat('d F Y') : '-' }}
                                </span>
                                <small style="color: #64748B;">
                                    @if($isBuka && $sisaHari >= 0)
                                        Tersisa <strong style="color: #0641CC;">{{ $sisaHari }} hari kalender</strong>
                                    @else
                                        Pendaftaran tidak sedang berlangsung
                                    @endif
                                </small>
                            </div>
                            @if($isBuka)
                                <span style="padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.725rem; font-weight: 700; background: #DCFCE7; color: #166534;">
                                    ● Dibuka
                                </span>
                            @else
                                <span style="padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.725rem; font-weight: 700; background: #F1F5F9; color: #64748B;">
                                    ● Ditutup
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- 2. Beban Pembimbing Lapangan --}}
                <div class="adm-sub-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span class="adm-sub-title" style="margin: 0;">Distribusi Pembimbing Lapangan</span>
                        <a href="{{ route('admin.penempatan.index') }}" class="adm-link-more-clean" style="font-size: 0.775rem;">
                            <span>Atur &rarr;</span>
                        </a>
                    </div>

                    @if($pembimbingWorkload->isEmpty())
                        <p style="font-size: 0.825rem; color: #64748B; margin: 0;">Belum ada pembimbing yang terdaftar.</p>
                    @else
                        @foreach($pembimbingWorkload->take(3) as $pmb)
                            <div class="adm-mentor-clean-row">
                                <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                                    <div class="adm-mentor-avatar">
                                        {{ strtoupper(substr($pmb->nama, 0, 1)) }}
                                    </div>
                                    <div style="min-width: 0;">
                                        <div style="font-weight: 700; font-size: 0.85rem; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $pmb->nama }}
                                        </div>
                                        <small style="color: #64748B; font-size: 0.725rem;">{{ $pmb->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }}</small>
                                    </div>
                                </div>
                                <span style="font-weight: 800; font-size: 0.8rem; color: #0641CC; white-space: nowrap;">
                                    {{ $pmb->aktif_count }} Mentee
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- 3. Tenggat Selesai Magang (Jika ada yang mendekati selesai) --}}
                @if(!$upcomingOffboardingList->isEmpty())
                    <div class="adm-sub-section">
                        <span class="adm-sub-title" style="color: #D97706;">Peringatan Selesai Magang (H-14)</span>
                        @foreach($upcomingOffboardingList->take(2) as $offboard)
                            @php
                                $pen = $offboard['penempatan'];
                                $days = $offboard['days_remaining'];
                            @endphp
                            <div style="padding: 0.65rem 0.85rem; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                <div style="font-size: 0.825rem;">
                                    <strong style="color: #92400E;">{{ $pen->peserta?->nama }}</strong>
                                    <span style="color: #B45309; display: block; font-size: 0.725rem;">{{ $pen->bidang?->nama_bidang ?? '-' }}</span>
                                </div>
                                <span style="font-size: 0.725rem; font-weight: 700; background: #FEF3C7; color: #92400E; padding: 0.2rem 0.5rem; border-radius: 4px;">
                                    {{ $days }} Hari Lagi
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- 4. Akses Modul Cepat --}}
                <div class="adm-sub-section">
                    <span class="adm-sub-title">Akses Modul Administrasi</span>
                    <a href="{{ route('admin.waiting-list.index') }}" class="adm-action-link-clean">
                        <span>Antrean Waiting List</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                    <a href="{{ route('admin.penempatan.index') }}" class="adm-action-link-clean">
                        <span>Penempatan & Pembimbing</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                    <a href="{{ route('admin.sertifikat.index') }}" class="adm-action-link-clean">
                        <span>Penerbitan Sertifikat Kelulusan</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </div>

            </div>
        </div>

    </div>

</div>

<script>
/**
 * Instant filter untuk tabel antrean verifikasi tanpa reload halaman
 */
function filterQueueTable(filterType, btn) {
    document.querySelectorAll('.adm-pill-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const rows = document.querySelectorAll('.queue-row');
    rows.forEach(row => {
        if (filterType === 'all') {
            row.style.display = '';
        } else if (filterType === 'diajukan') {
            row.style.display = row.classList.contains('status-diajukan') ? '' : 'none';
        } else if (filterType === 'perlu_perbaikan') {
            row.style.display = row.classList.contains('status-perlu-perbaikan') ? '' : 'none';
        }
    });
}
</script>
@endsection
