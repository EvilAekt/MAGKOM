@extends('layouts.dashboard')

@section('title', 'Verifikasi Berkas Pendaftaran - Admin MagKom Sleman')

@section('content')
<div class="adm-verifikasi-wrapper">

    <style>
        /* ==========================================================================
           MAGKOM ADMIN VERIFIKASI BERKAS - SIGNATURE THEME
           Theme: Dinas Komunikasi dan Informatika Kabupaten Sleman
           ========================================================================== */
        .adm-verifikasi-wrapper {
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
            .adm-hero-right { align-items: flex-start; }
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

        /* 2. UNIFIED KPI STRIP CARD */
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
        .adm-kpi-icon-amber { background: #FEF3C7; color: #D97706; }
        .adm-kpi-icon-orange { background: #FFEDD5; color: #EA580C; }
        .adm-kpi-icon-red { background: #FEE2E2; color: #DC2626; }

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

        /* 3. FILTER & SEARCH TOOLBAR CARD */
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

        .adm-status-track-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .adm-status-track {
            display: inline-flex;
            align-items: center;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 9999px;
            padding: 4px;
            gap: 2px;
            flex-wrap: wrap;
        }

        .adm-status-tab {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
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
            background: #E2E8F0;
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

        .adm-filter-grid {
            display: grid;
            grid-template-columns: 2.2fr 1.2fr 1.2fr 1.1fr auto;
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
            gap: 6px;
        }
        .adm-field-group label {
            font-size: 0.785rem;
            font-weight: 600;
            color: #475569;
            letter-spacing: -0.01em;
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
            box-shadow: 0 2px 6px rgba(6, 65, 204, 0.3);
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
            border-color: #CBD5E1;
        }

        /* 4. SPACIOUS TABLE CARD */
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
        }
        .adm-table-card-title {
            font-size: 1rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            white-space: nowrap;
        }
        .adm-table td {
            padding: 1.1rem 1.25rem;
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
        .adm-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
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
            font-size: 0.8rem;
            box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);
            transition: all 0.15s ease;
        }
        .adm-btn-tinjau:hover {
            background: #0533A8;
            transform: translateY(-1px);
            color: #ffffff;
        }
    </style>

    {{-- ==========================================================================
       1. SIGNATURE HERO BANNER
       ========================================================================== --}}
    <div class="adm-hero">
        <div class="adm-hero-content">
            <div class="adm-hero-left">
                <div class="adm-hero-pill-row">
                    <span class="adm-hero-status-pill">● Antrean Verifikasi Aktif</span>
                    <span class="adm-hero-role-pill">Prinsip FIFO & SLA</span>
                </div>
                
                <h1 class="adm-hero-title">
                    Antrean Verifikasi Berkas Pendaftaran 📋
                </h1>
                
                <p class="adm-hero-desc">
                    Tinjau kelengkapan dokumen persyaratan calon peserta magang dan tetapkan keputusan secara objektif dan tertib.
                </p>

                <div class="adm-hero-meta-badges">
                    <div class="adm-hero-chip">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <span>Menunggu Ditinjau: <strong>{{ number_format($queueStats['diajukan'] ?? 0) }} Berkas</strong></span>
                    </div>
                    <div class="adm-hero-chip">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        <span>Perlu Revisi: <strong>{{ number_format($queueStats['perlu_perbaikan'] ?? 0) }} Berkas</strong></span>
                    </div>
                    @if(($queueStats['sla_warning'] ?? 0) > 0)
                        <div class="adm-hero-chip" style="background: rgba(220, 38, 38, 0.25); border-color: rgba(254, 202, 202, 0.4);">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <span>⚠️ <strong>{{ number_format($queueStats['sla_warning']) }} Berkas</strong> &gt; 3 Hari</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="adm-hero-right">
                <div class="adm-hero-date-badge">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </div>

                <div class="adm-hero-actions">
                    <a href="{{ route('admin.dashboard') }}" class="adm-hero-glass-btn btn-solid">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        <span>Kembali ke Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================================================
       2. UNIFIED KPI STRIP CARD
       ========================================================================== --}}
    <div class="adm-kpi-unified-card">
        {{-- Item 1: Total Pengajuan --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Total Pengajuan</span>
                <span class="adm-kpi-val">{{ number_format($queueStats['total'] ?? $pendaftaranList->total()) }}</span>
                <span class="adm-kpi-hint">Semua riwayat pendaftaran</span>
            </div>
        </div>

        {{-- Item 2: Menunggu Ditinjau --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-amber">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Menunggu Ditinjau</span>
                <span class="adm-kpi-val">{{ number_format($queueStats['diajukan'] ?? 0) }}</span>
                <span class="adm-kpi-hint">Prioritas antrean FIFO</span>
            </div>
        </div>

        {{-- Item 3: Perlu Perbaikan --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-orange">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Perlu Revisi Dokumen</span>
                <span class="adm-kpi-val">{{ number_format($queueStats['perlu_perbaikan'] ?? 0) }}</span>
                <span class="adm-kpi-hint">Menunggu respon peserta</span>
            </div>
        </div>

        {{-- Item 4: SLA Kritis --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-red">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Tertahan &gt; 3 Hari</span>
                <span class="adm-kpi-val" style="color: {{ ($queueStats['sla_warning'] ?? 0) > 0 ? '#DC2626' : '#0F172A' }};">
                    {{ number_format($queueStats['sla_warning'] ?? 0) }}
                </span>
                <span class="adm-kpi-hint" style="color: {{ ($queueStats['sla_warning'] ?? 0) > 0 ? '#DC2626' : '#64748B' }};">
                    Batas SLA verifikasi target &le; 5 hari
                </span>
            </div>
        </div>
    </div>

    {{-- ==========================================================================
       3. FILTER & SEARCH TOOLBAR TERPADU
       ========================================================================== --}}
    <div class="adm-filter-card">
        {{-- Zona 1: Segmented Status Switcher --}}
        @php
            $currentStatus = request('status', '');
            $makeFilterUrl = function($statusVal) {
                $params = request()->except('status', 'page');
                if ($statusVal !== '') {
                    $params['status'] = $statusVal;
                }
                return route('admin.pendaftaran.index', $params);
            };
            $hasActiveFilters = request()->hasAny(['q', 'bulan_magang_id', 'jurusan_id', 'status', 'sort']);
        @endphp
        <div class="adm-status-track-wrapper">
            <div class="adm-status-track">
                <a href="{{ $makeFilterUrl('') }}" class="adm-status-tab {{ $currentStatus === '' ? 'active' : '' }}">
                    <span>Semua Status</span>
                    <span class="adm-status-counter">{{ $queueStats['total'] ?? $pendaftaranList->total() }}</span>
                </a>
                <a href="{{ $makeFilterUrl('diajukan') }}" class="adm-status-tab {{ $currentStatus === 'diajukan' ? 'active' : '' }}">
                    <span>Diajukan</span>
                    <span class="adm-status-counter">{{ $queueStats['diajukan'] ?? 0 }}</span>
                </a>
                <a href="{{ $makeFilterUrl('perlu_perbaikan') }}" class="adm-status-tab {{ $currentStatus === 'perlu_perbaikan' ? 'active' : '' }}">
                    <span>Perlu Perbaikan</span>
                    <span class="adm-status-counter">{{ $queueStats['perlu_perbaikan'] ?? 0 }}</span>
                </a>
                <a href="{{ $makeFilterUrl('waiting_list') }}" class="adm-status-tab {{ $currentStatus === 'waiting_list' ? 'active' : '' }}">
                    <span>Waiting List</span>
                    <span class="adm-status-counter">{{ $queueStats['waiting_list'] ?? 0 }}</span>
                </a>
                <a href="{{ $makeFilterUrl('diterima') }}" class="adm-status-tab {{ $currentStatus === 'diterima' ? 'active' : '' }}">
                    <span>Diterima</span>
                    <span class="adm-status-counter">{{ $queueStats['diterima'] ?? 0 }}</span>
                </a>
                <a href="{{ $makeFilterUrl('ditolak') }}" class="adm-status-tab {{ $currentStatus === 'ditolak' ? 'active' : '' }}">
                    <span>Ditolak</span>
                    <span class="adm-status-counter">{{ $queueStats['ditolak'] ?? 0 }}</span>
                </a>
            </div>

            @if($hasActiveFilters)
                <a href="{{ route('admin.pendaftaran.index') }}" class="adm-filter-reset-link" title="Bersihkan semua parameter pencarian dan filter">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    <span>Reset Filter</span>
                </a>
            @endif
        </div>

        {{-- Zona 2: Form Pencarian & Filter Grid Lapang --}}
        <form method="GET" action="{{ route('admin.pendaftaran.index') }}" class="adm-filter-grid">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="adm-field-group adm-field-search">
                <label for="search-input">Pencarian Calon Peserta</label>
                <div class="adm-search-input-wrap">
                    <svg class="adm-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="search-input" name="q" value="{{ request('q') }}" placeholder="Cari nama, no. registrasi (#REG-...), universitas, atau NIM..." class="adm-input-search">
                </div>
            </div>

            <div class="adm-field-group">
                <label for="select-bulan">Periode Magang</label>
                <select name="bulan_magang_id" id="select-bulan" class="adm-select">
                    <option value="">Semua Periode</option>
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
                    <option value="fifo" {{ request('sort', 'fifo') === 'fifo' ? 'selected' : '' }}>FIFO (Terawal Masuk)</option>
                    <option value="terbaru" {{ request('sort') === 'terbaru' ? 'selected' : '' }}>Paling Baru Dibuat</option>
                    <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Paling Lama Dibuat</option>
                </select>
            </div>

            <div class="adm-filter-actions">
                <button type="submit" class="adm-btn-filter-submit">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('admin.pendaftaran.index') }}" class="adm-btn-filter-reset" title="Kembalikan pencarian ke kondisi awal">
                    Reset
                </a>
                <a href="{{ route('admin.pendaftaran.download-zip', request()->query()) }}" style="height: 42px; padding: 0 1rem; background: #ffffff; color: #0641CC; font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 0.45rem; border: 1px solid #BFDBFE; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: background 0.15s ease;" title="Unduh seluruh berkas pendaftaran peserta hasil filter saat ini ke dalam satu arsip ZIP">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Unduh Bundel (ZIP)</span>
                </a>
            </div>
        </form>
    </div>

    {{-- ==========================================================================
       4. SPACIOUS TABLE CARD
       ========================================================================== --}}
    <div class="adm-table-card">
        @if($pendaftaranList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: #64748B;">
                <div style="width: 56px; height: 56px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: #0641CC;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 0.35rem 0;">
                    Tidak Ada Berkas yang Cocok
                </h3>
                <p style="font-size: 0.85rem; max-width: 480px; margin: 0 auto 1.25rem auto; line-height: 1.5;">
                    Tidak ditemukan berkas pendaftaran calon peserta magang yang sesuai dengan kriteria filter atau pencarian saat ini.
                </p>
                <a href="{{ route('admin.pendaftaran.index') }}" class="adm-btn-tinjau" style="display: inline-flex;">
                    Reset Semua Filter
                </a>
            </div>
        @else
            <div class="adm-table-card-header">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <h3 class="adm-table-card-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0641CC" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        <span>Daftar Berkas Pendaftaran Masuk</span>
                    </h3>
                    <span style="padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; background: #EFF6FF; color: #1E40AF;">
                        {{ $pendaftaranList->total() }} Pengajuan
                    </span>
                </div>
                <div style="font-size: 0.8rem; color: #64748B;">
                    Menampilkan {{ $pendaftaranList->firstItem() }}–{{ $pendaftaranList->lastItem() }} dari {{ $pendaftaranList->total() }} berkas
                </div>
            </div>

            <div style="overflow-x: auto; width: 100%;">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">No. Registrasi</th>
                            <th>Calon Peserta</th>
                            <th>Institusi & Jurusan</th>
                            <th>Periode Magang</th>
                            <th>Status & Usia Berkas</th>
                            <th style="text-align: right; width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaranList as $item)
                            @php
                                $val = $item->status->value ?? (string)$item->status;
                                $badgeStyle = match($val) {
                                    'berkas_belum_lengkap' => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1'],
                                    'diajukan' => ['bg' => '#FEF3C7', 'color' => '#92400E', 'border' => '#FCD34D'],
                                    'perlu_perbaikan' => ['bg' => '#FFEDD5', 'color' => '#C2410C', 'border' => '#FDBA74'],
                                    'waiting_list' => ['bg' => '#E0F2FE', 'color' => '#0369A1', 'border' => '#BAE6FD'],
                                    'diterima' => ['bg' => '#D1FAE5', 'color' => '#065F46', 'border' => '#A7F3D0'],
                                    'ditolak' => ['bg' => '#FEE2E2', 'color' => '#991B1B', 'border' => '#FECACA'],
                                    default => ['bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#CBD5E1'],
                                };

                                $slaDays = null;
                                if ($val === 'diajukan' && $item->diajukan_at) {
                                    $slaDays = (int) $item->diajukan_at->diffInDays(now());
                                }
                                $initial = strtoupper(substr($item->user?->nama ?? 'P', 0, 1));
                            @endphp
                            <tr>
                                {{-- No. Registrasi --}}
                                <td>
                                    <span style="display: inline-block; font-family: monospace; font-size: 0.825rem; font-weight: 700; color: #0641CC; background: #EFF6FF; padding: 0.2rem 0.55rem; border-radius: 6px; border: 1px solid #BFDBFE;">
                                        {{ $item->nomor_pendaftaran }}
                                    </span>
                                    <div style="font-size: 0.72rem; color: #64748B; margin-top: 0.25rem;">
                                        {{ $item->diajukan_at ? $item->diajukan_at->translatedFormat('d M Y') : ($item->created_at?->translatedFormat('d M Y') ?? '-') }}
                                    </div>
                                </td>

                                {{-- Calon Peserta --}}
                                <td>
                                    <div class="adm-user-cell">
                                        <div class="adm-avatar">{{ $initial }}</div>
                                        <div>
                                            <div style="font-weight: 700; color: #0F172A; font-size: 0.9rem;">
                                                {{ $item->user?->nama ?? 'Calon Peserta' }}
                                            </div>
                                            <div style="font-size: 0.775rem; color: #64748B;">
                                                {{ $item->user?->email }}
                                            </div>
                                            @if($item->user?->profil?->nim_nisn)
                                                <div style="font-size: 0.72rem; color: #94A3B8;">
                                                    NIM/NISN: {{ $item->user->profil->nim_nisn }}
                                                </div>
                                            @endif
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

                                {{-- Status & SLA --}}
                                <td>
                                    <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.725rem; font-weight: 700; background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; border: 1px solid {{ $badgeStyle['border'] }};">
                                        {{ $item->status->label() }}
                                    </span>
                                    @if($slaDays !== null)
                                        <div style="margin-top: 0.35rem;">
                                            @if($slaDays >= 5)
                                                <span style="font-size: 0.725rem; font-weight: 700; color: #DC2626; background: #FEF2F2; padding: 0.15rem 0.45rem; border-radius: 4px; border: 1px solid #FECACA;">
                                                    ⚠️ Melebihi SLA ({{ $slaDays }} hari)
                                                </span>
                                            @elseif($slaDays >= 3)
                                                <span style="font-size: 0.725rem; font-weight: 700; color: #D97706; background: #FFFBEB; padding: 0.15rem 0.45rem; border-radius: 4px; border: 1px solid #FDE68A;">
                                                    ⏳ Tertahan {{ $slaDays }} hari
                                                </span>
                                            @else
                                                <span style="font-size: 0.725rem; color: #64748B;">
                                                    {{ $slaDays === 0 ? 'Hari ini' : $slaDays . ' hari lalu' }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.pendaftaran.show', $item) }}" class="adm-btn-tinjau">
                                        <span>Tinjau</span>
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 1.25rem 1.5rem; border-top: 1px solid #E2E8F0; background: #F8FAFC;">
                {{ $pendaftaranList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
