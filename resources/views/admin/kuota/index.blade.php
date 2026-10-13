@extends('layouts.dashboard')

@section('title', 'Manajemen Kuota - Admin MagKom Sleman')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #64748B;">
        <a href="{{ route('admin.dashboard') }}" style="color: #64748B; text-decoration: none;">Dashboard</a>
        <span>/</span>
        <span style="color: #0F172A; font-weight: 700;">Manajemen Kuota Magang</span>
    </nav>
@endsection

@section('content')
@php
    $totalKapasitas = $selectedBulan ? $selectedBulan->kuota->sum('jumlah') : 0;
    $totalTerisi = $selectedBulan ? $selectedBulan->kuota->sum('terisi') : 0;
    $sisaKursi = max(0, $totalKapasitas - $totalTerisi);
    $globalPersen = $totalKapasitas > 0 ? min(100, round(($totalTerisi / $totalKapasitas) * 100)) : 0;

    $isPendaftaranBuka = false;
    if ($selectedBulan) {
        $now = now();
        $tglBuka = \Carbon\Carbon::parse($selectedBulan->tgl_buka)->startOfDay();
        $tglTutup = \Carbon\Carbon::parse($selectedBulan->tgl_tutup)->endOfDay();
        $isPendaftaranBuka = $now->between($tglBuka, $tglTutup);
    }
@endphp

<div class="adm-kuota-wrapper">

    <style>
        /* ==========================================================================
           MAGKOM ADMIN MANAJEMEN KUOTA - SIGNATURE THEME
           Theme: Dinas Komunikasi dan Informatika Kabupaten Sleman
           ========================================================================== */
        .adm-kuota-wrapper {
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

        .adm-hero-period-pill {
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

        .adm-hero-btn-add {
            background: #ffffff;
            color: #0641CC;
            border: 1px solid #ffffff;
            padding: 9px 20px;
            border-radius: 9999px;
            font-size: 0.825rem;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }
        .adm-hero-btn-add:hover {
            background: #F8FAFC;
            color: #0533A8;
            transform: translateY(-1px);
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
        .adm-kpi-icon-amber { background: #FEF3C7; color: #D97706; }
        .adm-kpi-icon-green { background: #D1FAE5; color: #059669; }
        .adm-kpi-icon-red { background: #FEE2E2; color: #DC2626; }
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

        /* 3. Integrated Period Selector Bar */
        .adm-period-bar {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.25rem;
        }

        .adm-period-selector-form {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 320px;
            max-width: 600px;
        }

        .adm-period-select {
            height: 42px;
            width: 100%;
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            padding: 0 14px;
            font-size: 0.875rem;
            color: #0F172A;
            background: #FFFFFF;
            font-weight: 600;
            outline: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .adm-period-select:focus {
            border-color: #0641CC;
            box-shadow: 0 0 0 3px rgba(6, 65, 204, 0.1);
        }

        .adm-period-info-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 0.8125rem;
            font-weight: 600;
        }
        .adm-period-info-open {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }
        .adm-period-info-closed {
            background: #F8FAFC;
            color: #475569;
            border: 1px solid #E2E8F0;
        }

        /* 4. Spacious Data Table */
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

        .adm-badge-jenjang {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 0.725rem;
            font-weight: 700;
            background: #EFF6FF;
            color: #0641CC;
            border: 1px solid #DBEAFE;
        }

        .adm-progress-bar-wrap {
            width: 100%;
            max-width: 130px;
            height: 6px;
            background: #E2E8F0;
            border-radius: 9999px;
            overflow: hidden;
            margin: 4px auto;
        }
        .adm-progress-bar-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.3s ease;
        }

        .adm-btn-edit {
            padding: 6px 14px;
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            color: #334155;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .adm-btn-edit:hover {
            border-color: #0641CC;
            color: #0641CC;
            background: #F8FAFC;
        }

        .adm-btn-toggle {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }

        /* 5. Modern Modal Dialog */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(6px);
            z-index: 1050;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-container {
            background: #ffffff;
            border-radius: 16px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            border: 1px solid #E2E8F0;
            animation: modalFadeIn 0.2s ease-out;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
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
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                        Kontrol Alokasi Kuota
                    </span>
                    <span class="adm-hero-period-pill">
                        {{ $selectedBulan ? $selectedBulan->bulan_mulai?->translatedFormat('F Y') : 'Semua Periode' }}
                    </span>
                </div>
                <h1 class="adm-hero-title">Alokasi &amp; Manajemen Kuota Magang</h1>
                <p class="adm-hero-desc">
                    Distribusi kuota kursi magang per kejuruan secara transparan, adaptif, dan akurat merujuk kapasitas daya tampung dinas.
                </p>
                <div class="adm-hero-chips">
                    <div class="adm-hero-chip">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <span>{{ number_format($totalKapasitas) }} Total Kursi</span>
                    </div>
                    <div class="adm-hero-chip">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                        <span>{{ $selectedBulan ? $selectedBulan->kuota->count() : 0 }} Kejuruan Dialokasikan</span>
                    </div>
                    @if($isPendaftaranBuka)
                        <div class="adm-hero-chip" style="background: rgba(16, 185, 129, 0.25); border-color: rgba(16, 185, 129, 0.5);">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981;"></span>
                            <span>Pendaftaran Sedang Dibuka</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="adm-hero-right">
                @if($selectedBulan)
                    <div class="adm-hero-date-box">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>{{ \Carbon\Carbon::parse($selectedBulan->tgl_buka)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($selectedBulan->tgl_tutup)->translatedFormat('d M Y') }}</span>
                    </div>
                    <button type="button" onclick="openTambahKuotaModal()" class="adm-hero-btn-add">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Alokasikan Kuota Jurusan</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ==========================================================================
       2. UNIFIED KPI STRIP CARD (METRIK TERPADU 4-KOLOM)
       ========================================================================== --}}
    <div class="adm-kpi-unified-card">
        {{-- KPI 1: Total Kapasitas Kursi --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Total Kapasitas Kursi</span>
                <span class="adm-kpi-val">{{ number_format($totalKapasitas) }}</span>
                <span class="adm-kpi-hint">Alokasi seluruh kejuruan</span>
            </div>
        </div>

        {{-- KPI 2: Kursi Terisi --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-amber">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Kursi Terisi</span>
                <span class="adm-kpi-val" style="color: #D97706;">{{ number_format($totalTerisi) }}</span>
                <span class="adm-kpi-hint">Peserta magang diterima</span>
            </div>
        </div>

        {{-- KPI 3: Sisa Kursi Tersedia --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box {{ $sisaKursi > 0 ? 'adm-kpi-icon-green' : 'adm-kpi-icon-red' }}">
                @if($sisaKursi > 0)
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                @else
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                @endif
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Sisa Kursi Siap Daftar</span>
                <span class="adm-kpi-val" style="color: {{ $sisaKursi > 0 ? '#059669' : '#DC2626' }};">{{ number_format($sisaKursi) }}</span>
                <span class="adm-kpi-hint" style="color: {{ $sisaKursi > 0 ? '#059669' : '#DC2626' }};">
                    {{ $sisaKursi > 0 ? 'Tersedia untuk pendaftar' : 'Kuota periode ini penuh' }}
                </span>
            </div>
        </div>

        {{-- KPI 4: Okupansi Global --}}
        <div class="adm-kpi-unified-item">
            <div class="adm-kpi-icon-box adm-kpi-icon-indigo">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
            </div>
            <div class="adm-kpi-details">
                <span class="adm-kpi-label">Tingkat Okupansi Global</span>
                <span class="adm-kpi-val" style="color: #4F46E5;">{{ $globalPersen }}%</span>
                <div style="margin-top: 4px; width: 100%; max-width: 120px; height: 5px; background: #E2E8F0; border-radius: 9999px; overflow: hidden;">
                    <div style="width: {{ $globalPersen }}%; height: 100%; background: {{ $globalPersen >= 100 ? '#EF4444' : ($globalPersen >= 75 ? '#F59E0B' : '#0641CC') }}; border-radius: 9999px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================================================
       3. INTEGRATED PERIOD SELECTOR BAR
       ========================================================================== --}}
    <div class="adm-period-bar">
        <form method="GET" action="{{ route('admin.kuota.index') }}" class="adm-period-selector-form">
            <label for="bulan-select" style="font-size: 0.825rem; font-weight: 700; color: #475569; white-space: nowrap; display: flex; align-items: center; gap: 6px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>Periode Magang:</span>
            </label>
            <select id="bulan-select" name="bulan_magang_id" onchange="this.form.submit()" class="adm-period-select">
                @foreach($bulanList as $b)
                    <option value="{{ $b->id }}" {{ $selectedBulan && $selectedBulan->id === $b->id ? 'selected' : '' }}>
                        {{ $b->bulan_mulai?->translatedFormat('F Y') }} (Pendaftaran: {{ \Carbon\Carbon::parse($b->tgl_buka)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($b->tgl_tutup)->translatedFormat('d M Y') }})
                    </option>
                @endforeach
            </select>
        </form>

        @if($selectedBulan)
            <div class="adm-period-info-badge {{ $isPendaftaranBuka ? 'adm-period-info-open' : 'adm-period-info-closed' }}">
                <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $isPendaftaranBuka ? '#10B981' : '#94A3B8' }};"></span>
                <span>
                    Jadwal Pendaftaran: <strong>{{ \Carbon\Carbon::parse($selectedBulan->tgl_buka)->translatedFormat('d F Y') }}</strong> s.d. <strong>{{ \Carbon\Carbon::parse($selectedBulan->tgl_tutup)->translatedFormat('d F Y') }}</strong>
                </span>
            </div>
        @endif
    </div>

    {{-- ==========================================================================
       4. SPACIOUS DATA TABLE CARD
       ========================================================================== --}}
    <div class="adm-table-card">
        <div class="adm-table-card-header">
            <div>
                <h3 class="adm-table-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                    <span>Alokasi Kuota Jurusan: {{ $selectedBulan ? $selectedBulan->bulan_mulai?->translatedFormat('F Y') : '-' }}</span>
                </h3>
                <p style="font-size: 0.8125rem; color: #64748B; margin: 3px 0 0 0;">
                    Distribusi alokasi kursi magang per jurusan pada periode pendaftaran ini
                </p>
            </div>
            <span style="font-size: 0.8125rem; font-weight: 700; color: #0641CC; background: #EFF6FF; padding: 4px 12px; border-radius: 9999px; border: 1px solid #DBEAFE;">
                {{ $selectedBulan ? $selectedBulan->kuota->count() : 0 }} Jurusan Terdaftar
            </span>
        </div>

        @if(!$selectedBulan || $selectedBulan->kuota->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: #64748B;">
                <div style="width: 60px; height: 60px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: #0641CC;">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h4 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 0.35rem 0;">
                    Belum Ada Alokasi Kuota Terdaftar
                </h4>
                <p style="font-size: 0.85rem; max-width: 480px; margin: 0 auto 1.5rem auto; line-height: 1.5;">
                    Belum ada alokasi kuota jurusan untuk periode bulan ini. Klik tombol di bawah untuk mulai mengalokasikan kuota kursi.
                </p>
                @if($selectedBulan)
                    <button type="button" onclick="openTambahKuotaModal()" class="adm-hero-btn-add" style="background: #0641CC; color: #ffffff; border-color: #0641CC; margin: 0 auto;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Alokasikan Kuota Jurusan Sekarang</span>
                    </button>
                @endif
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Nama Jurusan</th>
                            <th>Kategori Jenjang</th>
                            <th style="text-align: center; width: 120px;">Total Kuota</th>
                            <th style="text-align: center; width: 180px;">Terisi &amp; Okupansi</th>
                            <th style="text-align: center; width: 120px;">Sisa Kuota</th>
                            <th>Status Alokasi</th>
                            <th style="text-align: right; width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedBulan->kuota as $k)
                            @php
                                $sisa = max(0, $k->jumlah - $k->terisi);
                                $persen = $k->jumlah > 0 ? min(100, round(($k->terisi / $k->jumlah) * 100)) : 0;
                                $barColor = $persen >= 100 ? '#EF4444' : ($persen >= 75 ? '#F59E0B' : '#0641CC');
                            @endphp
                            <tr>
                                {{-- Nama Jurusan --}}
                                <td>
                                    <div style="font-weight: 700; color: #0F172A; font-size: 0.9375rem;">
                                        {{ $k->jurusan?->nama_jurusan ?? 'Semua Jurusan' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 2px; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;">
                                        #KT-{{ str_pad((string)$k->id, 4, '0', STR_PAD_LEFT) }}
                                    </div>
                                </td>

                                {{-- Kategori Jenjang --}}
                                <td>
                                    <span class="adm-badge-jenjang">
                                        {{ $k->jurusan?->kategori === 'smk' ? 'SMK' : 'Mahasiswa (D3/S1)' }}
                                    </span>
                                </td>

                                {{-- Total Kuota --}}
                                <td style="text-align: center; font-weight: 800; color: #0F172A; font-size: 1rem;">
                                    {{ $k->jumlah }}
                                </td>

                                {{-- Terisi & Okupansi --}}
                                <td style="text-align: center;">
                                    <div style="display: flex; align-items: baseline; justify-content: center; gap: 4px;">
                                        <span style="font-weight: 800; color: {{ $persen >= 100 ? '#DC2626' : '#D97706' }}; font-size: 0.9375rem;">
                                            {{ $k->terisi }}
                                        </span>
                                        <span style="font-size: 0.75rem; color: #64748B;">/ {{ $k->jumlah }}</span>
                                    </div>

                                    <div class="adm-progress-bar-wrap" title="Okupansi: {{ $persen }}%">
                                        <div class="adm-progress-bar-fill" style="width: {{ $persen }}%; background: {{ $barColor }};"></div>
                                    </div>
                                    <span style="font-size: 0.72rem; font-weight: 700; color: #64748B;">
                                        Okupansi: {{ $persen }}%
                                    </span>
                                </td>

                                {{-- Sisa Kuota --}}
                                <td style="text-align: center;">
                                    <span style="font-weight: 800; font-size: 1rem; color: {{ $sisa > 0 ? '#059669' : '#DC2626' }};">
                                        {{ $sisa }}
                                    </span>
                                    <div style="font-size: 0.72rem; color: {{ $sisa > 0 ? '#059669' : '#DC2626' }}; font-weight: 700;">
                                        {{ $sisa > 0 ? 'Tersedia' : 'Penuh' }}
                                    </div>
                                </td>

                                {{-- Status Alokasi --}}
                                <td>
                                    <span style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $k->aktif ? '#D1FAE5' : '#F1F5F9' }}; color: {{ $k->aktif ? '#065F46' : '#475569' }}; border: 1px solid {{ $k->aktif ? '#A7F3D0' : '#CBD5E1' }};">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $k->aktif ? '#10B981' : '#94A3B8' }};"></span>
                                        <span>{{ $k->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px; align-items: center;">
                                        <button type="button" onclick="openEditKuotaModal({{ $k->id }}, {{ $k->jumlah }}, {{ $k->terisi }}, '{{ addslashes($k->jurusan?->nama_jurusan ?? '') }}')" class="adm-btn-edit">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('admin.kuota.toggle', $k) }}" style="display: inline;" onsubmit="return confirm('Ubah status aktif kuota jurusan ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="adm-btn-toggle" style="background: {{ $k->aktif ? '#FEE2E2' : '#D1FAE5' }}; color: {{ $k->aktif ? '#991B1B' : '#065F46' }}; border-color: {{ $k->aktif ? '#FECACA' : '#A7F3D0' }};">
                                                {{ $k->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

{{-- ==========================================================================
     5. MODAL TAMBAH KUOTA JURUSAN
     ========================================================================== --}}
@if($selectedBulan)
<div id="modal-tambah-kuota" class="modal-overlay">
    <div class="modal-container">
        {{-- Modal Header --}}
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </div>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0;">Tambah Kuota Jurusan</h3>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 2px 0 0 0;">
                        Periode: <strong>{{ $selectedBulan->bulan_mulai?->translatedFormat('F Y') }}</strong>
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeTambahKuotaModal()" style="background: transparent; border: none; font-size: 1.5rem; color: #94A3B8; cursor: pointer; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                &times;
            </button>
        </div>

        {{-- Modal Form --}}
        <form method="POST" action="{{ route('admin.kuota.store') }}" style="padding: 1.5rem;">
            @csrf
            <input type="hidden" name="bulan_magang_id" value="{{ $selectedBulan->id }}">

            <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8rem; font-weight: 700; color: #1E293B;">
                    Pilih Kejuruan <span style="color: #DC2626;">*</span>
                </label>
                <select name="jurusan_id" required style="height: 42px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: #0F172A; outline: none;">
                    <option value="">-- Pilih Jurusan yang Tersedia --</option>
                    @foreach($jurusanList as $jur)
                        <option value="{{ $jur->id }}">{{ $jur->nama_jurusan }} ({{ strtoupper($jur->kategori) }})</option>
                    @endforeach
                </select>
                <span style="font-size: 0.75rem; color: #64748B;">Pastikan jurusan aktif belum dialokasikan pada periode bulan ini.</span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 1.5rem;">
                <label style="font-size: 0.8rem; font-weight: 700; color: #1E293B;">
                    Jumlah Kuota Kursi <span style="color: #DC2626;">*</span>
                </label>
                <input type="number" name="jumlah" min="1" max="100" value="5" required style="height: 42px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: #0F172A; outline: none;">
                <span style="font-size: 0.75rem; color: #64748B;">Alokasi umum disarankan mempertimbangkan daya tampung maksimal dinas.</span>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #E2E8F0; padding-top: 1.25rem;">
                <button type="button" onclick="closeTambahKuotaModal()" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid #CBD5E1; color: #475569; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
                    Simpan Alokasi Kuota
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ==========================================================================
     6. MODAL EDIT ALOKASI KUOTA
     ========================================================================== --}}
<div id="modal-edit-kuota" class="modal-overlay">
    <div class="modal-container">
        {{-- Modal Header --}}
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center; background: #F8FAFC;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </div>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0;">Perbarui Alokasi Kuota</h3>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 2px 0 0 0;" id="edit-kuota-jurusan-title">
                        Jurusan: -
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeEditKuotaModal()" style="background: transparent; border: none; font-size: 1.5rem; color: #94A3B8; cursor: pointer; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                &times;
            </button>
        </div>

        {{-- Modal Form --}}
        <form method="POST" id="form-edit-kuota" action="" style="padding: 1.5rem;">
            @csrf
            @method('PUT')
            <input type="hidden" name="bulan_magang_id" value="{{ $selectedBulan->id }}">

            <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 1.5rem;">
                <label style="font-size: 0.8rem; font-weight: 700; color: #1E293B;">
                    Jumlah Kuota Baru (Kursi) <span style="color: #DC2626;">*</span>
                </label>
                <input type="number" id="edit-input-jumlah" name="jumlah" required style="height: 42px; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff; color: #0F172A; outline: none;">
                <div id="edit-info-terisi" style="font-size: 0.75rem; color: #B45309; background: #FEF3C7; padding: 0.35rem 0.65rem; border-radius: 6px; margin-top: 4px; font-weight: 600;">
                    Saat ini telah terisi: 0 kursi.
                </div>
                <span style="font-size: 0.72rem; color: #64748B; margin-top: 2px;">
                    Sesuai aturan sistem: Kuota baru tidak boleh lebih kecil dari jumlah peserta yang telah diterima.
                </span>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #E2E8F0; padding-top: 1.25rem;">
                <button type="button" onclick="closeEditKuotaModal()" style="padding: 0.55rem 1.25rem; background: #ffffff; border: 1px solid #CBD5E1; color: #475569; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.55rem 1.4rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; box-shadow: 0 1px 3px rgba(6, 65, 204, 0.25);">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function openTambahKuotaModal() {
        const modal = document.getElementById('modal-tambah-kuota');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    function closeTambahKuotaModal() {
        const modal = document.getElementById('modal-tambah-kuota');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function openEditKuotaModal(kuotaId, jumlah, terisi, namaJurusan) {
        const titleEl = document.getElementById('edit-kuota-jurusan-title');
        const inputEl = document.getElementById('edit-input-jumlah');
        const infoEl = document.getElementById('edit-info-terisi');
        const formEl = document.getElementById('form-edit-kuota');
        const modal = document.getElementById('modal-edit-kuota');

        if (titleEl) titleEl.innerText = 'Jurusan: ' + namaJurusan;
        if (inputEl) {
            inputEl.value = jumlah;
            inputEl.min = terisi;
        }
        if (infoEl) infoEl.innerText = 'Saat ini telah terisi: ' + terisi + ' kursi (batas minimum perubahan).';
        if (formEl) formEl.action = '/admin/kuota/' + kuotaId;
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    function closeEditKuotaModal() {
        const modal = document.getElementById('modal-edit-kuota');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // Close on Escape key
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTambahKuotaModal();
            closeEditKuotaModal();
        }
    });

    // Close on Backdrop Click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeTambahKuotaModal();
                closeEditKuotaModal();
            }
        });
    });
</script>
@endsection
