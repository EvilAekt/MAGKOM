@extends('layouts.dashboard')

@section('title', 'Jejak Audit Trail (15 Kanal) - SuperAdmin MagKom')
@section('page_title', 'Jejak Audit Sistem Multi-Kanal (Audit Trail)')
@section('page_description', 'Pencatatan real-time seluruh aktivitas, mutasi data, dan insiden keamanan dalam 15 kanal terisolasi ala Discord Stream.')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Audit Log</span>
    @if($activeChannel !== 'all-actions-stream')
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">{{ $activeChannelMeta['label'] }}</span>
    @endif
@endsection

@section('page_actions')
    <a href="{{ route('superadmin.audit-log.export', array_merge(request()->query(), ['channel' => $activeChannel])) }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 38px; padding: 0 1.25rem; font-weight: 700; font-size: 0.8125rem; border-radius: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); text-decoration: none;">
        <i class="fa fa-download"></i>
        <span>Ekspor CSV ({{ $activeChannelMeta['label'] }})</span>
    </a>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    /* =========================================================================
       Gentelella Parity & DS-MagKom Industrial Standard for Audit Log
       ========================================================================= */

    /* 1. TOP STAT TILES */
    .audit-tile-stat {
        background: var(--neutral-0, #FFFFFF);
        border: 1px solid var(--neutral-200, #EEF0F5);
        border-radius: 8px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .audit-tile-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }
    .audit-tile-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .audit-tile-number {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1.2;
        color: var(--neutral-900, #0F172A);
        font-variant-numeric: tabular-nums;
    }
    .audit-tile-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--neutral-500, #7A8494);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-top: 0.25rem;
    }

    /* 2. KANAL NAVIGATION LIST */
    .channel-nav-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.65rem 0.85rem;
        border-radius: 6px;
        text-decoration: none;
        color: var(--neutral-700, #334155);
        font-size: 0.8125rem;
        font-weight: 600;
        transition: all 0.15s ease;
        margin-bottom: 0.25rem;
        border: 1px solid transparent;
        background: transparent;
    }
    .channel-nav-item:hover {
        background: var(--neutral-100, #F8FAFC);
        color: var(--neutral-900, #0F172A);
        text-decoration: none;
    }
    .channel-nav-item.active {
        background: var(--blue-tint, rgba(1, 52, 124, 0.08));
        color: var(--blue-600, #01347C);
        border-color: rgba(1, 52, 124, 0.2);
        border-left: 3px solid var(--blue-600, #01347C);
    }
    .channel-badge-count {
        font-size: 0.6875rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        font-variant-numeric: tabular-nums;
    }

    /* 3. SEVERITY BADGES (WCAG AA COMPLIANT) */
    .badge-severity {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.2rem 0.55rem;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .badge-severity-info {
        background: rgba(1, 52, 124, 0.08);
        color: #01347C;
        border: 1px solid rgba(1, 52, 124, 0.2);
    }
    .badge-severity-success {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }
    .badge-severity-warning {
        background: rgba(245, 158, 11, 0.12);
        color: #B45309;
        border: 1px solid rgba(245, 158, 11, 0.25);
    }
    .badge-severity-danger {
        background: rgba(239, 68, 68, 0.12);
        color: #B91C1C;
        border: 1px solid rgba(239, 68, 68, 0.25);
    }

    /* 4. TABLE TOP ACCENT RIBBON */
    .x_panel.has-table {
        border-top: 3px solid #01347C;
    }

    /* 5. GENTELELLA FORM CONTROL OVERRIDES */
    .form-control-gentelella {
        width: 100%;
        height: 38px;
        border: 1px solid var(--neutral-400, #CBD5E1);
        border-radius: 6px;
        padding: 0 0.75rem;
        font-size: 0.8125rem;
        background: var(--neutral-0, #FFFFFF);
        color: var(--neutral-800, #1D2530);
        font-family: inherit;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .form-control-gentelella:focus {
        border-color: var(--blue-600, #01347C);
        outline: none;
        box-shadow: 0 0 0 3px rgba(1, 52, 124, 0.15);
    }

    /* 6. DARK MODE HARMONIZATION */
    [data-theme="dark"] .audit-tile-stat {
        background-color: #003566;
        border-color: #3F51B5;
    }
    [data-theme="dark"] .audit-tile-number {
        color: #F8FAFC;
    }
    [data-theme="dark"] .audit-tile-label {
        color: #94A3B8;
    }
    [data-theme="dark"] .channel-nav-item {
        color: #CBD5E1;
    }
    [data-theme="dark"] .channel-nav-item:hover {
        background-color: rgba(59, 130, 246, 0.15);
        color: #F8FAFC;
    }
    [data-theme="dark"] .channel-nav-item.active {
        background-color: rgba(59, 130, 246, 0.25);
        color: #93C5FD;
        border-left-color: #3B82F6;
        border-color: rgba(59, 130, 246, 0.4);
    }
    [data-theme="dark"] .badge-severity-info {
        background: rgba(59, 130, 246, 0.2);
        color: #93C5FD;
        border-color: rgba(59, 130, 246, 0.4);
    }
    [data-theme="dark"] .badge-severity-success {
        background: rgba(16, 185, 129, 0.2);
        color: #6EE7B7;
        border-color: rgba(16, 185, 129, 0.4);
    }
    [data-theme="dark"] .badge-severity-warning {
        background: rgba(245, 158, 11, 0.2);
        color: #FCD34D;
        border-color: rgba(245, 158, 11, 0.4);
    }
    [data-theme="dark"] .badge-severity-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #FCA5A5;
        border-color: rgba(239, 68, 68, 0.4);
    }
    [data-theme="dark"] .form-control-gentelella {
        background-color: #011C44;
        color: #F8FAFC;
        border-color: rgba(63, 81, 181, 0.45);
        color-scheme: dark;
    }
    [data-theme="dark"] .x_panel.has-table {
        border-top: 3px solid #3B82F6;
    }
    [data-theme="dark"] #modalLogDetail > div {
        background-color: #003566;
        border-color: #3F51B5;
        color: #F8FAFC;
    }
</style>
@endpush

@section('content')
<div class="row">
    <!-- =====================================================================
         1. TOP STAT TILES (Gentelella Parity)
         ===================================================================== -->
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="audit-tile-stat">
            <div>
                <div class="audit-tile-number">{{ number_format($total24h) }}</div>
                <div class="audit-tile-label">Peristiwa (24 Jam)</div>
            </div>
            <div class="audit-tile-icon" style="background: rgba(1, 52, 124, 0.1); color: var(--blue-600, #01347C);">
                <i class="fa fa-clock-o"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="audit-tile-stat">
            <div>
                <div class="audit-tile-number">{{ number_format($totalLogs) }}</div>
                <div class="audit-tile-label">Total Log Database</div>
            </div>
            <div class="audit-tile-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                <i class="fa fa-database"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="audit-tile-stat">
            <div>
                <div class="audit-tile-number">{{ number_format($channels['security-alert']['count_24h'] ?? 0) }}</div>
                <div class="audit-tile-label">Peringatan Keamanan</div>
            </div>
            <div class="audit-tile-icon" style="background: rgba(239, 68, 68, 0.12); color: #DC2626;">
                <i class="fa fa-shield"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="audit-tile-stat">
            <div>
                <div class="audit-tile-number">15 Kanal</div>
                <div class="audit-tile-label">Aliran Terpantau</div>
            </div>
            <div class="audit-tile-icon" style="background: rgba(139, 92, 246, 0.12); color: #7C3AED;">
                <i class="fa fa-sitemap"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- =====================================================================
         2. KOLOM KIRI: DAFTAR 15 KANAL AUDIT (Gentelella Sidebar Panel)
         ===================================================================== -->
    <div class="col-md-3 col-sm-4 col-xs-12">
        <x-gentelella.x-panel title="<i class='fa fa-filter'></i> KANAL PERISTIWA" subtitle="15 Aliran Peristiwa" :toolbox="true" :collapsible="true">
            <div style="display: flex; flex-direction: column; gap: 0.25rem; max-height: calc(100vh - 280px); overflow-y: auto; padding-right: 0.25rem;">
                @foreach($channels as $key => $c)
                    @php
                        $isActive = ($activeChannel === $key);
                        $iconClass = match($key) {
                            'all-actions-stream' => 'fa fa-rss',
                            'auth-session' => 'fa fa-lock',
                            'pendaftaran' => 'fa fa-file-text-o',
                            'verifikasi-admin' => 'fa fa-check-square-o',
                            'penempatan-mentor' => 'fa fa-users',
                            'kuota-periode' => 'fa fa-calendar',
                            'tugas-classroom' => 'fa fa-tasks',
                            'logbook-presensi' => 'fa fa-book',
                            'penilaian-sk' => 'fa fa-graduation-cap',
                            'survei-evaluasi' => 'fa fa-star-o',
                            'user-management' => 'fa fa-user-circle',
                            'konfigurasi-master' => 'fa fa-cogs',
                            'backup-database' => 'fa fa-database',
                            'security-alert' => 'fa fa-shield',
                            'dynamic-module' => 'fa fa-cubes',
                            default => 'fa fa-hashtag',
                        };
                    @endphp
                    <a href="{{ route('superadmin.audit-log.channel', ['channel' => $key]) }}" class="channel-nav-item {{ $isActive ? 'active' : '' }}" title="{{ $c['desc'] }}">
                        <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0;">
                            <i class="{{ $iconClass }}" style="color: {{ $c['color'] }}; font-size: 0.875rem; width: 16px; text-align: center;"></i>
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $c['label'] }}
                            </span>
                        </div>
                        @if($c['count_24h'] > 0)
                            <span class="channel-badge-count" style="background: {{ $isActive ? 'var(--blue-600, #01347C)' : 'var(--neutral-200, #EEF0F5)' }}; color: {{ $isActive ? '#FFFFFF' : 'var(--neutral-700, #334155)' }};">
                                {{ $c['count_24h'] }}
                            </span>
                        @else
                            <span style="font-size: 0.6875rem; color: var(--neutral-400, #94A3B8);">0</span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div style="border-top: 1px solid var(--neutral-200, #EEF0F5); padding-top: 0.875rem; margin-top: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #16A34A; font-weight: 700; font-size: 0.75rem;">
                    <i class="fa fa-shield" style="font-size: 1rem;"></i>
                    <span>Append-Only Immutability</span>
                </div>
                <p style="font-size: 0.6875rem; color: var(--neutral-500, #64748B); margin: 0.25rem 0 0 0; line-height: 1.4;">
                    Seluruh rekaman audit tersanitasi anti-tamper dan tersimpan secara permanen.
                </p>
            </div>
        </x-gentelella.x-panel>
    </div>

    <!-- =====================================================================
         3. KOLOM KANAN: STREAM PERISTIWA, FILTER TOOLBAR & TABEL AUDIT
         ===================================================================== -->
    <div class="col-md-9 col-sm-8 col-xs-12">

        <!-- 3A. HERO BANNER KANAL AKTIF -->
        <div class="x_panel" style="border-left: 4px solid {{ $activeChannelMeta['color'] }}; padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    @php
                        $activeIcon = match($activeChannel) {
                            'all-actions-stream' => 'fa fa-rss',
                            'auth-session' => 'fa fa-lock',
                            'pendaftaran' => 'fa fa-file-text-o',
                            'verifikasi-admin' => 'fa fa-check-square-o',
                            'penempatan-mentor' => 'fa fa-users',
                            'kuota-periode' => 'fa fa-calendar',
                            'tugas-classroom' => 'fa fa-tasks',
                            'logbook-presensi' => 'fa fa-book',
                            'penilaian-sk' => 'fa fa-graduation-cap',
                            'survei-evaluasi' => 'fa fa-star-o',
                            'user-management' => 'fa fa-user-circle',
                            'konfigurasi-master' => 'fa fa-cogs',
                            'backup-database' => 'fa fa-database',
                            'security-alert' => 'fa fa-shield',
                            'dynamic-module' => 'fa fa-cubes',
                            default => 'fa fa-hashtag',
                        };
                    @endphp
                    <div style="width: 44px; height: 44px; border-radius: 8px; background: {{ $activeChannelMeta['color'] }}1A; color: {{ $activeChannelMeta['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800;">
                        <i class="{{ $activeIcon }}"></i>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.625rem;">
                            <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">
                                {{ $activeChannelMeta['label'] }}
                            </h2>
                            <span class="x_badge" style="background: {{ $activeChannelMeta['color'] }}20; color: {{ $activeChannelMeta['color'] }};">
                                channel: {{ $activeChannel }}
                            </span>
                        </div>
                        <p style="font-size: 0.8125rem; color: var(--neutral-600, #475569); margin: 0.2rem 0 0 0;">
                            {{ $activeChannelMeta['desc'] }}
                        </p>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 0.8125rem; color: var(--neutral-600, #475569); font-weight: 600;">
                        <i class="fa fa-database"></i> Total Rekaman: <strong>{{ method_exists($logs, 'total') ? number_format($logs->total()) : number_format($logs->count()) }} Baris</strong>
                    </span>
                </div>
            </div>
        </div>

        <!-- 3B. PENCARIAN & FILTER PANEL (Collapsible) -->
        <x-gentelella.x-panel title="<i class='fa fa-search'></i> Pencarian &amp; Filter Aliran Audit" subtitle="Filter multi-parameter kata kunci, severity, staf & rentang waktu" :toolbox="true" :collapsible="true">
            <form method="GET" action="{{ $activeChannel === 'all-actions-stream' ? route('superadmin.audit-log.index') : route('superadmin.audit-log.channel', ['channel' => $activeChannel]) }}">
                <input type="hidden" name="channel" value="{{ $activeChannel }}">

                <div class="row">
                    <!-- Keyword Search -->
                    <div class="col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 0.875rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700, #334155); text-transform: uppercase; margin-bottom: 0.35rem;">
                            <i class="fa fa-search"></i> Kata Kunci / Pencarian
                        </label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari aksi, user, entitas, IP, request ID..." class="form-control-gentelella">
                    </div>

                    <!-- Severity Filter -->
                    <div class="col-md-2 col-sm-6 col-xs-12" style="margin-bottom: 0.875rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700, #334155); text-transform: uppercase; margin-bottom: 0.35rem;">
                            <i class="fa fa-shield"></i> Severity
                        </label>
                        <select name="severity" class="form-control-gentelella">
                            <option value="">Semua Tingkat</option>
                            <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>Info</option>
                            <option value="success" {{ request('severity') === 'success' ? 'selected' : '' }}>Success</option>
                            <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="danger" {{ request('severity') === 'danger' ? 'selected' : '' }}>Danger</option>
                        </select>
                    </div>

                    <!-- Tipe Audit -->
                    <div class="col-md-2 col-sm-6 col-xs-12" style="margin-bottom: 0.875rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700, #334155); text-transform: uppercase; margin-bottom: 0.35rem;">
                            <i class="fa fa-tag"></i> Tipe Audit
                        </label>
                        <select name="tipe" class="form-control-gentelella">
                            <option value="">Semua Tipe</option>
                            @foreach($tipeEnum as $tipe)
                                <option value="{{ $tipe->value }}" {{ request('tipe') === $tipe->value ? 'selected' : '' }}>
                                    {{ ucfirst($tipe->value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Aktor / Staf -->
                    <div class="col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 0.875rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700, #334155); text-transform: uppercase; margin-bottom: 0.35rem;">
                            <i class="fa fa-user"></i> Aktor / Pelaku
                        </label>
                        <select name="user_id" class="form-control-gentelella">
                            <option value="">Semua Staf & Akun</option>
                            @foreach($internalUsers ?? [] as $staf)
                                <option value="{{ $staf->id }}" {{ request('user_id') == $staf->id ? 'selected' : '' }}>
                                    {{ $staf->nama }} ({{ $staf->role->label() }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dari Tanggal -->
                    <div class="col-md-3 col-sm-6 col-xs-12" style="margin-bottom: 0.875rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700, #334155); text-transform: uppercase; margin-bottom: 0.35rem;">
                            <i class="fa fa-calendar"></i> Dari Tanggal
                        </label>
                        <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="form-control-gentelella">
                    </div>

                    <!-- Sampai Tanggal -->
                    <div class="col-md-3 col-sm-6 col-xs-12" style="margin-bottom: 0.875rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700, #334155); text-transform: uppercase; margin-bottom: 0.35rem;">
                            <i class="fa fa-calendar"></i> Sampai Tanggal
                        </label>
                        <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="form-control-gentelella">
                    </div>

                    <!-- Filter Actions Buttons -->
                    <div class="col-md-6 col-sm-12 col-xs-12" style="display: flex; align-items: flex-end; justify-content: flex-end; gap: 0.5rem; margin-bottom: 0.875rem;">
                        @if(request()->hasAny(['q', 'tipe', 'severity', 'user_id', 'dari_tanggal', 'sampai_tanggal']))
                            <a href="{{ $activeChannel === 'all-actions-stream' ? route('superadmin.audit-log.index') : route('superadmin.audit-log.channel', ['channel' => $activeChannel]) }}" class="btn btn-default" style="height: 38px; display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.8125rem; font-weight: 600;">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary" style="height: 38px; display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.8125rem; font-weight: 700; padding: 0 1.25rem;">
                            <i class="fa fa-filter"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </x-gentelella.x-panel>

        <!-- 3C. ENTERPRISE DATA TABLE AUDIT -->
        <x-gentelella.x-panel title="<i class='fa fa-list'></i> Aliran Peristiwa Audit" subtitle="Rekaman tersanitasi anti-tamper (append-only)" :toolbox="true" :collapsible="true" class="has-table">
            @if($logs->isEmpty())
                <div style="padding: 3.5rem 1.5rem; text-align: center;">
                    <div style="width: 52px; height: 52px; border-radius: 50%; background: var(--neutral-100, #F8FAFC); margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center; color: var(--neutral-500, #7A8494); font-size: 1.5rem;">
                        <i class="fa fa-info-circle"></i>
                    </div>
                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800, #1D2530); margin: 0 0 0.35rem 0;">Tidak Ada Peristiwa Audit Ditemukan</h4>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600, #475569); max-width: 440px; margin: 0 auto;">Belum ada aktivitas yang tercatat pada kanal ini dengan kriteria filter yang Anda tentukan.</p>
                </div>
            @else
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table table-striped jambo_table bulk_action" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.8125rem;">
                        <thead>
                            <tr class="headings">
                                <th style="width: 120px;">Waktu (WIB)</th>
                                <th style="width: 150px;">Kanal / Severity</th>
                                <th style="width: 180px;">Aktor / Pelaku</th>
                                <th>Aksi & Entitas</th>
                                <th style="width: 170px;">Konteks HTTP</th>
                                <th style="text-align: right; width: 110px;">Inspeksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                @php
                                    $channelMeta = $channels[$log->channel] ?? [
                                        'label' => $log->channel ?: 'system',
                                        'color' => '#64748B',
                                    ];
                                    $sevClass = match($log->severity) {
                                        'success' => 'badge-severity-success',
                                        'warning' => 'badge-severity-warning',
                                        'danger'  => 'badge-severity-danger',
                                        default   => 'badge-severity-info',
                                    };
                                @endphp
                                <tr class="sa-table-row">
                                    <!-- Waktu -->
                                    <td style="padding: 0.75rem 0.875rem; vertical-align: top; white-space: nowrap;">
                                        <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-variant-numeric: tabular-nums;">
                                            {{ $log->waktu?->format('H:i:s') ?? '-' }}
                                        </div>
                                        <div style="font-size: 0.6875rem; color: var(--neutral-500, #7A8494);">
                                            {{ $log->waktu?->format('d M Y') ?? '-' }}
                                        </div>
                                        <div style="font-size: 0.625rem; font-family: monospace; color: var(--neutral-400, #94A3B8); margin-top: 0.15rem;">
                                            #{{ $log->id }}
                                        </div>
                                    </td>

                                    <!-- Kanal & Severity -->
                                    <td style="padding: 0.75rem 0.875rem; vertical-align: top;">
                                        <div style="display: flex; flex-direction: column; gap: 0.35rem; align-items: flex-start;">
                                            <span style="font-size: 0.6875rem; font-weight: 700; color: {{ $channelMeta['color'] }}; display: inline-flex; align-items: center; gap: 0.3rem;">
                                                <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $channelMeta['color'] }};"></span>
                                                #{{ $channelMeta['label'] }}
                                            </span>
                                            <span class="badge-severity {{ $sevClass }}">
                                                {{ $log->severity ?? 'info' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Aktor -->
                                    <td style="padding: 0.75rem 0.875rem; vertical-align: top;">
                                        @if($log->user)
                                            <div style="font-weight: 700; color: var(--neutral-900, #0F172A);">{{ $log->user->nama }}</div>
                                            <div style="font-size: 0.75rem; color: var(--blue-700, #01347C); font-weight: 600;">{{ $log->user->role?->label() ?? 'Pengguna' }}</div>
                                            <div style="font-size: 0.6875rem; color: var(--neutral-500, #7A8494);">{{ $log->user->email }}</div>
                                        @else
                                            <span style="font-style: italic; color: var(--neutral-500, #7A8494); font-weight: 600;">Sistem / Anonim</span>
                                        @endif
                                    </td>

                                    <!-- Aksi & Entitas -->
                                    <td style="padding: 0.75rem 0.875rem; vertical-align: top; max-width: 280px;">
                                        <div style="font-weight: 700; color: var(--neutral-900, #0F172A); word-break: break-word;">
                                            {{ $log->aksi }}
                                        </div>
                                        @if($log->entitas)
                                            <div style="font-size: 0.75rem; color: var(--neutral-600, #475569); margin-top: 0.2rem;">
                                                Tabel: <code style="font-size: 0.6875rem; background: var(--neutral-200, #EEF0F5); padding: 0.1rem 0.35rem; border-radius: 4px;">{{ $log->entitas }}</code>
                                                @if($log->entitas_id)
                                                    <span style="font-weight: 700;">#{{ $log->entitas_id }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Konteks HTTP -->
                                    <td style="padding: 0.75rem 0.875rem; vertical-align: top; white-space: nowrap;">
                                        <div style="display: flex; align-items: center; gap: 0.35rem;">
                                            <span style="font-size: 0.6875rem; font-weight: 700; padding: 0.1rem 0.35rem; border-radius: 4px; background: var(--neutral-200, #EEF0F5); color: var(--neutral-800, #1D2530); font-family: monospace;">
                                                {{ $log->metode ?? 'GET' }}
                                            </span>
                                            @if($log->status_http)
                                                <span style="font-size: 0.6875rem; font-weight: 700; color: {{ $log->status_http >= 400 ? '#DC2626' : '#059669' }};">
                                                    {{ $log->status_http }}
                                                </span>
                                            @endif
                                            @if($log->durasi_ms)
                                                <span style="font-size: 0.6875rem; color: var(--neutral-500, #7A8494);">({{ $log->durasi_ms }}ms)</span>
                                            @endif
                                        </div>
                                        <div style="font-size: 0.6875rem; font-family: monospace; color: var(--neutral-600, #475569); margin-top: 0.25rem;">
                                            IP: {{ $log->ip ?? '-' }}
                                        </div>
                                        <div style="font-size: 0.625rem; font-family: monospace; color: var(--neutral-400, #94A3B8); max-width: 160px; overflow: hidden; text-overflow: ellipsis;">
                                            req: {{ $log->request_id ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Tombol Inspeksi Diff -->
                                    <td style="padding: 0.75rem 0.875rem; vertical-align: top; text-align: right; white-space: nowrap;">
                                        <button type="button" onclick="openModalLogDetail({{ (string) json_encode([
                                            'id' => $log->id,
                                            'waktu' => $log->waktu?->format('d M Y H:i:s') . ' WIB',
                                            'channel' => $channelMeta['label'],
                                            'severity' => $log->severity ?? 'info',
                                            'user' => $log->user ? ($log->user->nama . ' (' . ($log->user->role?->label() ?? '') . ')') : 'Sistem / Anonim',
                                            'aksi' => $log->aksi,
                                            'entitas' => $log->entitas,
                                            'entitas_id' => $log->entitas_id,
                                            'request_id' => $log->request_id,
                                            'ip' => $log->ip,
                                            'user_agent' => $log->user_agent,
                                            'metode' => $log->metode,
                                            'rute' => $log->rute,
                                            'status_http' => $log->status_http,
                                            'durasi_ms' => $log->durasi_ms,
                                            'detail' => $log->detail,
                                            'nilai_lama' => $log->nilai_lama,
                                            'nilai_baru' => $log->nilai_baru,
                                        ]) }})" class="btn btn-default btn-xs" style="font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.625rem;">
                                            <i class="fa fa-search"></i> Diff &amp; Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--neutral-200, #EEF0F5); background: var(--neutral-100, #F8FAFC);">
                        {{ $logs->links() }}
                    </div>
                @endif
            @endif
        </x-gentelella.x-panel>
    </div>
</div>

<!-- =====================================================================
     4. MODAL DETAIL INSPEKSI JSON & DIFF VIEWER (Tri-Core Inspector)
     ===================================================================== -->
<div id="modalLogDetail" onclick="if(event.target === this) closeModalLogDetail()" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0, #FFFFFF); border-radius: 12px; width: 100%; max-width: 840px; padding: 1.75rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.04); position: relative; max-height: 90vh; display: flex; flex-direction: column; border: 1px solid var(--neutral-300, #CBD5E1);">
        <!-- Modal Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200, #EEF0F5); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; border-radius: 8px; background: var(--blue-tint, rgba(1, 52, 124, 0.08)); color: var(--blue-600, #01347C); display: flex; align-items: center; justify-content: center; font-size: 1.125rem;">
                    <i class="fa fa-search"></i>
                </div>
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">
                        Inspeksi Audit Trail #<span id="logId"></span>
                    </h3>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.15rem;">
                        <span style="font-size: 0.6875rem; font-weight: 700; color: var(--blue-600, #01347C); text-transform: uppercase;">
                            Kanal: #<span id="logChannel"></span>
                        </span>
                        <span style="font-size: 0.6875rem; font-weight: 700; color: var(--neutral-400, #94A3B8);">•</span>
                        <span id="logSeverity" class="badge-severity badge-severity-info"></span>
                    </div>
                </div>
            </div>
            <button type="button" onclick="closeModalLogDetail()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500, #7A8494); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <!-- Modal Body Scrollable -->
        <div style="overflow-y: auto; display: flex; flex-direction: column; gap: 1.25rem; padding-right: 0.25rem;">
            <!-- Metadata Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.8125rem; background: var(--neutral-100, #F8FAFC); padding: 1rem 1.25rem; border-radius: 8px; border: 1px solid var(--neutral-200, #EEF0F5);">
                <div><span style="color: var(--neutral-500, #64748B);">Aktor:</span> <strong id="logUser" style="color: var(--neutral-900, #0F172A);">-</strong></div>
                <div><span style="color: var(--neutral-500, #64748B);">Waktu:</span> <strong id="logWaktu" style="color: var(--neutral-900, #0F172A); font-variant-numeric: tabular-nums;">-</strong></div>
                <div><span style="color: var(--neutral-500, #64748B);">Entitas / ID:</span> <strong id="logEntitas" style="color: var(--neutral-900, #0F172A);">-</strong></div>
                <div><span style="color: var(--neutral-500, #64748B);">IP Address:</span> <strong id="logIp" style="color: var(--neutral-900, #0F172A); font-family: monospace;">-</strong></div>
                <div style="grid-column: span 2;"><span style="color: var(--neutral-500, #64748B);">Aksi Sistem:</span> <strong id="logAksi" style="color: var(--neutral-900, #0F172A);">-</strong></div>
                <div style="grid-column: span 2;"><span style="color: var(--neutral-500, #64748B);">Request ID:</span> <span id="logRequestId" style="font-family: monospace; font-weight: 700; color: var(--neutral-900, #0F172A);">-</span></div>
                <div style="grid-column: span 2;"><span style="color: var(--neutral-500, #64748B);">Konteks HTTP:</span> <span id="logHttp" style="font-family: monospace; color: var(--blue-600, #01347C); font-weight: 700;">-</span></div>
            </div>

            <!-- Side-by-Side Diff Mutasi -->
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700, #334155); text-transform: uppercase; margin-bottom: 0.375rem;">
                    Perbandingan Mutasi Data (Diff Inspector)
                </label>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 0.5rem;">
                        <div style="font-size: 0.6875rem; font-weight: 700; color: #DC2626; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                            <i class="fa fa-minus-circle"></i> Nilai Lama (Sebelum Mutasi)
                        </div>
                        <pre id="logNilaiLama" style="background: rgba(239, 68, 68, 0.06); color: #B91C1C; border: 1px solid rgba(239, 68, 68, 0.25); padding: 0.875rem; border-radius: 6px; font-size: 0.75rem; overflow-x: auto; margin: 0; max-height: 180px; font-family: monospace; line-height: 1.4;"></pre>
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 0.5rem;">
                        <div style="font-size: 0.6875rem; font-weight: 700; color: #059669; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                            <i class="fa fa-plus-circle"></i> Nilai Baru (Setelah Mutasi)
                        </div>
                        <pre id="logNilaiBaru" style="background: rgba(16, 185, 129, 0.06); color: #047857; border: 1px solid rgba(16, 185, 129, 0.25); padding: 0.875rem; border-radius: 6px; font-size: 0.75rem; overflow-x: auto; margin: 0; max-height: 180px; font-family: monospace; line-height: 1.4;"></pre>
                    </div>
                </div>
            </div>

            <!-- Detail Event Payload -->
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700, #334155); text-transform: uppercase; margin-bottom: 0.375rem;">
                    Detail Konteks Event Payload (JSON)
                </label>
                <pre id="logPayload" style="background: #0f172a; color: #f8fafc; padding: 1rem; border-radius: 6px; font-size: 0.75rem; overflow-x: auto; margin: 0; max-height: 160px; font-family: monospace; line-height: 1.45;"></pre>
            </div>
        </div>

        <!-- Modal Footer -->
        <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem; border-top: 1px solid var(--neutral-200, #EEF0F5); padding-top: 1rem;">
            <button type="button" onclick="closeModalLogDetail()" class="btn btn-default" style="height: 38px; padding: 0 1.5rem; font-weight: 700; font-size: 0.8125rem;">
                Tutup Inspeksi
            </button>
        </div>
    </div>
</div>

<script>
    function openModalLogDetail(data) {
        document.getElementById('logId').textContent = data.id;
        document.getElementById('logChannel').textContent = data.channel;
        document.getElementById('logUser').textContent = data.user;
        document.getElementById('logWaktu').textContent = data.waktu;
        document.getElementById('logAksi').textContent = data.aksi;
        document.getElementById('logEntitas').textContent = (data.entitas || '-') + (data.entitas_id ? (' #' + data.entitas_id) : '');
        document.getElementById('logIp').textContent = data.ip || '-';
        document.getElementById('logRequestId').textContent = data.request_id || '-';
        document.getElementById('logHttp').textContent = (data.metode || '-') + ' ' + (data.rute || '-') + (data.status_http ? (' [' + data.status_http + ']') : '') + (data.durasi_ms ? (' (' + data.durasi_ms + 'ms)') : '');

        const sevElem = document.getElementById('logSeverity');
        sevElem.textContent = data.severity;
        sevElem.className = 'badge-severity badge-severity-' + data.severity;

        document.getElementById('logPayload').textContent = data.detail ? JSON.stringify(data.detail, null, 2) : '- (Tidak ada data tambahan)';
        document.getElementById('logNilaiLama').textContent = data.nilai_lama ? JSON.stringify(data.nilai_lama, null, 2) : '- (Tidak ada nilai lama)';
        document.getElementById('logNilaiBaru').textContent = data.nilai_baru ? JSON.stringify(data.nilai_baru, null, 2) : '- (Tidak ada nilai baru)';

        document.getElementById('modalLogDetail').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModalLogDetail() {
        document.getElementById('modalLogDetail').style.display = 'none';
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModalLogDetail();
        }
    });
</script>
@endsection
