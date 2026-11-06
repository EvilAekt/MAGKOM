@extends('layouts.dashboard')

@section('title', 'Verifikasi Berkas Pendaftaran Peserta - Admin MagKom Sleman')

@section('breadcrumbs')
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #64748B;">
        <a href="{{ route('admin.dashboard') }}" style="color: #64748B; text-decoration: none;">Dashboard</a>
        <span>/</span>
        <span style="color: #0F172A; font-weight: 700;">Verifikasi Berkas Pendaftaran Peserta</span>
    </nav>
@endsection

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

        /* 2. UNIFIED FILTER TOOLBAR */
        .adm-filter-toolbar {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
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
            background: #FFFFFF;
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

        /* Filter Grid (42px Unified Height) */
        .adm-filter-grid {
            display: grid;
            grid-template-columns: minmax(240px, 2.2fr) minmax(150px, 1.2fr) minmax(160px, 1.2fr) minmax(140px, 1fr) auto;
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
            padding: 0.875rem 1.25rem;
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
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

        [data-theme="dark"] .adm-filter-toolbar {
            background-color: #162238;
            border-color: #26354D;
        }

        [data-theme="dark"] .adm-status-track {
            background-color: #0F172A;
            border-color: #26354D;
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

    {{-- PAGE HEADER WITH ACTIONS --}}
    <x-gentelella.page-title title="Verifikasi Berkas Pendaftaran Peserta">
        <a href="{{ route('admin.dashboard') }}" class="btn-action-gentelella btn-action-primary" style="padding: 0.5rem 1rem; font-size: 0.8125rem;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"></polyline></svg>
            <span>Kembali ke Dashboard</span>
        </a>
        <a href="{{ route('admin.pendaftaran.download-zip', request()->query()) }}" class="btn-action-gentelella btn-action-success" style="padding: 0.5rem 1rem; font-size: 0.8125rem;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Unduh Bundel ZIP</span>
        </a>
    </x-gentelella.page-title>

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

    {{-- 2. MAIN X-PANEL CARD --}}
    <x-gentelella.x-panel title="Daftar Permohonan Magang Masuk">

        {{-- 2A. FILTER & SEARCH TOOLBAR TERPADU --}}
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

        <div class="adm-filter-toolbar">
            {{-- Zona 1: Segmented Status Switcher --}}
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

            {{-- Zona 2: Form Pencarian & Filter Grid Lapang (42px Unified Height) --}}
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
                </div>
            </form>
        </div>

        {{-- 2B. TABEL DATA CALON PESERTA TERPADU --}}
        <div class="adm-table-container">
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
                <div class="adm-table-meta-bar">
                    <div style="font-size: 0.8125rem; font-weight: 600; color: #64748B;">
                        Menampilkan <strong style="color: #0F172A;">{{ $pendaftaranList->firstItem() }}–{{ $pendaftaranList->lastItem() }}</strong> dari <strong style="color: #0F172A;">{{ $pendaftaranList->total() }}</strong> berkas pendaftaran
                    </div>
                    @if($hasActiveFilters)
                        <span style="font-size: 0.75rem; color: #0284C7; background: #E0F2FE; padding: 0.2rem 0.65rem; border-radius: 9999px; font-weight: 700;">
                            Filter Aktif Diterapkan
                        </span>
                    @endif
                </div>

                <div style="overflow-x: auto; width: 100%;">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th style="width: 150px;">No. Registrasi</th>
                                <th>Calon Peserta</th>
                                <th>Institusi & Jurusan</th>
                                <th>Bulan Magang</th>
                                <th>Status & Usia Berkas</th>
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

                                    $slaDays = null;
                                    if ($val === 'diajukan' && $item->diajukan_at) {
                                        $slaDays = (int) $item->diajukan_at->diffInDays(now());
                                    }

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
                                        <span style="display: inline-block; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.8125rem; font-weight: 700; color: #0641CC; background: #EFF6FF; padding: 0.25rem 0.6rem; border-radius: 6px; border: 1px solid #BFDBFE;">
                                            {{ $item->nomor_pendaftaran }}
                                        </span>
                                        <div style="font-size: 0.725rem; color: #64748B; margin-top: 0.35rem;">
                                            {{ $item->diajukan_at ? $item->diajukan_at->translatedFormat('d M Y') : ($item->created_at?->translatedFormat('d M Y') ?? '-') }}
                                        </div>
                                    </td>

                                    {{-- Calon Peserta --}}
                                    <td>
                                        <div class="adm-user-cell">
                                            <div class="cv-avatar-sm cv-avatar-palette-{{ $paletteIdx }}" style="width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.825rem; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.06);">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; color: #0F172A; font-size: 0.875rem;">
                                                    {{ $item->user?->nama ?? 'Calon Peserta' }}
                                                </div>
                                                <div style="font-size: 0.775rem; color: #64748B;">
                                                    {{ $item->user?->email }}
                                                </div>
                                                @if($item->user?->profil?->nim_nisn)
                                                    <div style="font-size: 0.725rem; color: #94A3B8;">
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
                                        <span class="cv-status-dot-badge {{ $statusDotType }}">
                                            <span class="dot"></span>
                                            <span>{{ $item->status->label() }}</span>
                                        </span>
                                        @if($slaDays !== null)
                                            <div style="margin-top: 0.35rem;">
                                                @if($slaDays >= 5)
                                                    <span style="font-size: 0.725rem; font-weight: 700; color: #DC2626; background: #FEF2F2; padding: 0.15rem 0.5rem; border-radius: 6px; border: 1px solid #FECACA; display: inline-flex; align-items: center; gap: 4px;">
                                                        <span>⚠️</span>
                                                        <span>Melebihi SLA ({{ $slaDays }} hari)</span>
                                                    </span>
                                                @elseif($slaDays >= 3)
                                                    <span style="font-size: 0.725rem; font-weight: 700; color: #D97706; background: #FFFBEB; padding: 0.15rem 0.5rem; border-radius: 6px; border: 1px solid #FDE68A; display: inline-flex; align-items: center; gap: 4px;">
                                                        <span>⏳</span>
                                                        <span>Tertahan {{ $slaDays }} hari</span>
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
