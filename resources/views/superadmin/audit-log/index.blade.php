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
    <a href="{{ route('superadmin.audit-log.export', array_merge(request()->query(), ['channel' => $activeChannel])) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.25rem; background: var(--green-700); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border-radius: 8px; text-decoration: none; box-shadow: var(--shadow-subtle); transition: all 0.15s ease;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        <span>Ekspor CSV ({{ $activeChannelMeta['label'] }})</span>
    </a>
@endsection

@push('styles')
<style>
    /* Multi-Channel Grid Layout */
    .audit-layout-grid {
        display: grid;
        grid-template-columns: 310px 1fr;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 1024px) {
        .audit-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Channel Item ala Discord */
    .discord-channel-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.65rem 0.875rem;
        border-radius: 8px;
        text-decoration: none;
        color: var(--neutral-700);
        font-size: 0.8125rem;
        font-weight: 600;
        transition: all 0.15s ease;
        border: 1px solid transparent;
        background: transparent;
    }

    .discord-channel-link:hover {
        background: var(--neutral-100);
        color: var(--neutral-900);
    }

    .discord-channel-link.active {
        background: var(--blue-tint);
        color: #1D4ED8;
        border-color: rgba(37, 99, 235, 0.2);
    }

    [data-theme="dark"] .discord-channel-link {
        color: #94A3B8;
    }

    [data-theme="dark"] .discord-channel-link:hover {
        background: #1E293B;
        color: #F8FAFC;
    }

    [data-theme="dark"] .discord-channel-link.active {
        background: rgba(37, 99, 235, 0.2);
        color: #93C5FD;
        border-color: rgba(59, 130, 246, 0.4);
    }

    /* Severity Badges */
    .badge-severity {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .badge-severity-info {
        background: rgba(59, 130, 246, 0.12);
        color: #2563EB;
        border: 1px solid rgba(59, 130, 246, 0.25);
    }

    .badge-severity-success {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .badge-severity-warning {
        background: rgba(245, 158, 11, 0.12);
        color: #D97706;
        border: 1px solid rgba(245, 158, 11, 0.25);
    }

    .badge-severity-danger {
        background: rgba(239, 68, 68, 0.12);
        color: #DC2626;
        border: 1px solid rgba(239, 68, 68, 0.25);
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

    /* Dark Mode Overrides */
    [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
        background-color: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] input[type="text"],
    [data-theme="dark"] input[type="date"],
    [data-theme="dark"] select {
        background-color: #0F172A !important;
        color: #F8FAFC !important;
        border-color: #26354D !important;
        color-scheme: dark;
    }

    [data-theme="dark"] div[style*="background: var(--neutral-100)"] {
        background-color: #111C2E !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] table thead tr {
        background-color: #141E30 !important;
        border-bottom-color: #26354D !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] table tbody tr:hover {
        background-color: #162238 !important;
    }

    [data-theme="dark"] #modalLogDetail > div {
        background-color: #182234 !important;
        border-color: #26354D !important;
        color: #F8FAFC !important;
    }
</style>
@endpush

@section('content')
<div class="audit-layout-grid">

    <!-- =====================================================================
         KOLOM KIRI: DAFTAR 15 KANAL DISCORD-STYLE
         ===================================================================== -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle); display: flex; flex-direction: column; gap: 1rem; position: sticky; top: 1.5rem;">
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                <span style="font-size: 0.75rem; font-weight: 800; color: var(--neutral-500); text-transform: uppercase; letter-spacing: 0.05em;"># KANAL PERISTIWA</span>
                <span style="font-size: 0.7rem; font-weight: 700; padding: 0.15rem 0.45rem; border-radius: 6px; background: var(--neutral-200); color: var(--neutral-700);">15 Kanal</span>
            </div>
            <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0;">Pilih kanal spesifik untuk memfilter aliran audit secara terisolasi.</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.25rem; max-height: calc(100vh - 280px); overflow-y: auto; padding-right: 0.25rem;">
            @foreach($channels as $key => $c)
                @php
                    $isActive = ($activeChannel === $key);
                @endphp
                <a href="{{ route('superadmin.audit-log.channel', ['channel' => $key]) }}" class="discord-channel-link {{ $isActive ? 'active' : '' }}" title="{{ $c['desc'] }}">
                    <div style="display: flex; align-items: center; gap: 0.625rem; min-width: 0;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $c['color'] }}; flex-shrink: 0; box-shadow: 0 0 6px {{ $c['color'] }}66;"></span>
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            # {{ $c['label'] }}
                        </span>
                    </div>
                    @if($c['count_24h'] > 0)
                        <span style="font-size: 0.6875rem; font-weight: 700; padding: 0.125rem 0.5rem; border-radius: 9999px; background: {{ $isActive ? 'var(--blue-600)' : 'var(--neutral-200)' }}; color: {{ $isActive ? '#FFFFFF' : 'var(--neutral-700)' }}; flex-shrink: 0; margin-left: 0.5rem;">
                            {{ $c['count_24h'] }}
                        </span>
                    @else
                        <span style="font-size: 0.6875rem; color: var(--neutral-400); flex-shrink: 0;">0</span>
                    @endif
                </a>
            @endforeach
        </div>

        <!-- Kartu Telemetri Singkat Audit -->
        <div style="border-top: 1px solid var(--neutral-200); padding-top: 0.875rem; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.75rem;">
            <div style="display: flex; justify-content: space-between; color: var(--neutral-600);">
                <span>Total 24 Jam:</span>
                <strong style="color: var(--neutral-900);">{{ number_format($total24h) }} peristiwa</strong>
            </div>
            <div style="display: flex; justify-content: space-between; color: var(--neutral-600);">
                <span>Total Log Database:</span>
                <strong style="color: var(--neutral-900);">{{ number_format($totalLogs) }} baris</strong>
            </div>
            <div style="display: flex; align-items: center; gap: 0.35rem; color: var(--green-700); font-weight: 700; margin-top: 0.25rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Append-Only Immutable Guarantee</span>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         KOLOM KANAN: HEADER KANAL, FILTER TOOLBAR & STREAM PERISTIWA
         ===================================================================== -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem; min-width: 0;">

        <!-- 1. ACTIVE CHANNEL HERO BANNER -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: {{ $activeChannelMeta['color'] }}1A; color: {{ $activeChannelMeta['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800;">
                    #
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                            {{ $activeChannelMeta['label'] }}
                        </h2>
                        <span style="font-size: 0.6875rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 6px; background: {{ $activeChannelMeta['color'] }}20; color: {{ $activeChannelMeta['color'] }};">
                            channel: {{ $activeChannel }}
                        </span>
                    </div>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.2rem 0 0 0;">
                        {{ $activeChannelMeta['desc'] }}
                    </p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                    Total Rekaman: <strong>{{ method_exists($logs, 'total') ? number_format($logs->total()) : number_format($logs->count()) }} Baris</strong>
                </span>
            </div>
        </div>

        <!-- 2. FILTER TOOLBAR CARD -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-subtle);">
            <form method="GET" action="{{ $activeChannel === 'all-actions-stream' ? route('superadmin.audit-log.index') : route('superadmin.audit-log.channel', ['channel' => $activeChannel]) }}" style="display: flex; flex-direction: column; gap: 1rem;">
                <input type="hidden" name="channel" value="{{ $activeChannel }}">

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.875rem;">
                    <!-- Keyword Search -->
                    <div style="grid-column: span 2; min-width: 260px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Kata Kunci / Pencarian</label>
                        <div style="position: relative;">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari aksi, user, entitas, IP, request ID..." style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem 0 2.25rem; font-size: 0.8125rem; font-family: inherit;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--neutral-500);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                    </div>

                    <!-- Severity Filter -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Tingkat Severity</label>
                        <select name="severity" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.8125rem; background: var(--neutral-0); font-family: inherit;">
                            <option value="">Semua Tingkat</option>
                            <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>Info (Biru)</option>
                            <option value="success" {{ request('severity') === 'success' ? 'selected' : '' }}>Success (Hijau)</option>
                            <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Warning (Kuning)</option>
                            <option value="danger" {{ request('severity') === 'danger' ? 'selected' : '' }}>Danger (Merah)</option>
                        </select>
                    </div>

                    <!-- Tipe Audit -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Tipe Audit</label>
                        <select name="tipe" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.8125rem; background: var(--neutral-0); font-family: inherit;">
                            <option value="">Semua Tipe</option>
                            @foreach($tipeEnum as $tipe)
                                <option value="{{ $tipe->value }}" {{ request('tipe') === $tipe->value ? 'selected' : '' }}>
                                    {{ ucfirst($tipe->value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Aktor / Staf -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Aktor / Staf</label>
                        <select name="user_id" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.8125rem; background: var(--neutral-0); font-family: inherit;">
                            <option value="">Semua Staf & Akun</option>
                            @foreach($internalUsers ?? [] as $staf)
                                <option value="{{ $staf->id }}" {{ request('user_id') == $staf->id ? 'selected' : '' }}>
                                    {{ $staf->nama }} ({{ $staf->role->label() }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dari Tanggal -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Dari Tanggal</label>
                        <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.8125rem; font-family: inherit;">
                    </div>

                    <!-- Sampai Tanggal -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.35rem;">Sampai Tanggal</label>
                        <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.8125rem; font-family: inherit;">
                    </div>
                </div>

                <div style="display: flex; gap: 0.75rem; align-items: center; justify-content: flex-end; border-top: 1px solid var(--neutral-200); padding-top: 0.875rem;">
                    @if(request()->hasAny(['q', 'tipe', 'severity', 'user_id', 'dari_tanggal', 'sampai_tanggal']))
                        <a href="{{ $activeChannel === 'all-actions-stream' ? route('superadmin.audit-log.index') : route('superadmin.audit-log.channel', ['channel' => $activeChannel]) }}" style="height: 36px; padding: 0 1rem; display: inline-flex; align-items: center; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); text-decoration: none;">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" style="height: 36px; padding: 0 1.25rem; background: var(--blue-700); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.375rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. DATA TABLE CARD -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
            @if($logs->isEmpty())
                <div style="padding: 3.5rem 1.5rem; text-align: center;">
                    <div style="width: 52px; height: 52px; border-radius: 50%; background: var(--neutral-100); margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.35rem 0;">Tidak Ada Peristiwa Audit Ditemukan</h4>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); max-width: 440px; margin: 0 auto;">Belum ada aktivitas yang tercatat pada kanal ini dengan kriteria filter yang Anda tentukan.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.8125rem;">
                        <thead>
                            <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-600); font-weight: 700; text-transform: uppercase; font-size: 0.6875rem; letter-spacing: 0.05em;">
                                <th style="padding: 0.875rem 1rem;">Waktu (WIB)</th>
                                <th style="padding: 0.875rem 1rem;">Kanal / Severity</th>
                                <th style="padding: 0.875rem 1rem;">Aktor / Pelaku</th>
                                <th style="padding: 0.875rem 1rem;">Aksi & Entitas</th>
                                <th style="padding: 0.875rem 1rem;">Konteks HTTP</th>
                                <th style="padding: 0.875rem 1rem; text-align: right;">Inspeksi</th>
                            </tr>
                        </thead>
                        <tbody style="divide-y: divide-gray-200;">
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
                                <tr style="border-bottom: 1px solid var(--neutral-200); transition: background 0.15s ease;">
                                    <!-- Waktu -->
                                    <td style="padding: 0.875rem 1rem; vertical-align: top; white-space: nowrap;">
                                        <div style="font-weight: 700; color: var(--neutral-900);">{{ $log->waktu?->format('H:i:s') ?? '-' }}</div>
                                        <div style="font-size: 0.6875rem; color: var(--neutral-500);">{{ $log->waktu?->format('d M Y') ?? '-' }}</div>
                                        <div style="font-size: 0.625rem; font-family: monospace; color: var(--neutral-400); margin-top: 0.2rem;">#{{ $log->id }}</div>
                                    </td>

                                    <!-- Kanal & Severity -->
                                    <td style="padding: 0.875rem 1rem; vertical-align: top;">
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
                                    <td style="padding: 0.875rem 1rem; vertical-align: top;">
                                        @if($log->user)
                                            <div style="font-weight: 700; color: var(--neutral-900);">{{ $log->user->nama }}</div>
                                            <div style="font-size: 0.75rem; color: var(--blue-700); font-weight: 600;">{{ $log->user->role?->label() ?? 'Pengguna' }}</div>
                                            <div style="font-size: 0.6875rem; color: var(--neutral-500);">{{ $log->user->email }}</div>
                                        @else
                                            <span style="font-style: italic; color: var(--neutral-500); font-weight: 600;">Sistem / Tamu Anonim</span>
                                        @endif
                                    </td>

                                    <!-- Aksi & Entitas -->
                                    <td style="padding: 0.875rem 1rem; vertical-align: top; max-width: 300px;">
                                        <div style="font-weight: 700; color: var(--neutral-900); word-break: break-word;">
                                            {{ $log->aksi }}
                                        </div>
                                        @if($log->entitas)
                                            <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.2rem;">
                                                Tabel: <code style="font-size: 0.6875rem; background: var(--neutral-200); padding: 0.1rem 0.35rem; border-radius: 4px;">{{ $log->entitas }}</code>
                                                @if($log->entitas_id)
                                                    <span style="font-weight: 700;">#{{ $log->entitas_id }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Konteks HTTP -->
                                    <td style="padding: 0.875rem 1rem; vertical-align: top; white-space: nowrap;">
                                        <div style="display: flex; align-items: center; gap: 0.35rem;">
                                            <span style="font-size: 0.6875rem; font-weight: 700; padding: 0.1rem 0.35rem; border-radius: 4px; background: var(--neutral-200); color: var(--neutral-800); font-family: monospace;">
                                                {{ $log->metode ?? 'GET' }}
                                            </span>
                                            @if($log->status_http)
                                                <span style="font-size: 0.6875rem; font-weight: 700; color: {{ $log->status_http >= 400 ? '#DC2626' : '#059669' }};">
                                                    {{ $log->status_http }}
                                                </span>
                                            @endif
                                            @if($log->durasi_ms)
                                                <span style="font-size: 0.6875rem; color: var(--neutral-500);">({{ $log->durasi_ms }}ms)</span>
                                            @endif
                                        </div>
                                        <div style="font-size: 0.6875rem; font-family: monospace; color: var(--neutral-600); margin-top: 0.25rem;">
                                            IP: {{ $log->ip ?? '-' }}
                                        </div>
                                        <div style="font-size: 0.625rem; font-family: monospace; color: var(--neutral-400); max-width: 160px; overflow: hidden; text-overflow: ellipsis;">
                                            req: {{ $log->request_id ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Tombol Inspeksi Diff -->
                                    <td style="padding: 0.875rem 1rem; vertical-align: top; text-align: right; white-space: nowrap;">
                                        <button type="button" onclick="openModalLogDetail({{ json_encode([
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
                                        ]) }})" style="padding: 0.45rem 0.875rem; background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 6px; font-size: 0.75rem; font-weight: 700; color: var(--neutral-800); cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 0.35rem;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='var(--neutral-0)'">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                            Diff &amp; Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
                        {{ $logs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- =====================================================================
     MODAL DETAIL INSPEKSI JSON & DIFF VIEWER (Tri-Core Inspector)
     ===================================================================== -->
<div id="modalLogDetail" onclick="if(event.target === this) closeModalLogDetail()" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 820px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; max-height: 90vh; display: flex; flex-direction: column; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line></svg>
                </div>
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        Inspeksi Audit Trail #<span id="logId"></span>
                    </h3>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.15rem;">
                        <span style="font-size: 0.6875rem; font-weight: 700; color: var(--blue-700); text-transform: uppercase;">
                            Kanal: #<span id="logChannel"></span>
                        </span>
                        <span style="font-size: 0.6875rem; font-weight: 700; color: var(--neutral-500);">•</span>
                        <span id="logSeverity" class="badge-severity badge-severity-info"></span>
                    </div>
                </div>
            </div>
            <button type="button" onclick="closeModalLogDetail()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <div style="overflow-y: auto; display: flex; flex-direction: column; gap: 1.25rem; padding-right: 0.25rem;">
            <!-- Metadata Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.8125rem; background: var(--neutral-100); padding: 1rem 1.25rem; border-radius: 8px; border: 1px solid var(--neutral-300);">
                <div><span style="color: var(--neutral-600);">Aktor:</span> <strong id="logUser" style="color: var(--neutral-900);">-</strong></div>
                <div><span style="color: var(--neutral-600);">Waktu:</span> <strong id="logWaktu" style="color: var(--neutral-900); font-family: monospace;">-</strong></div>
                <div><span style="color: var(--neutral-600);">Entitas / ID:</span> <strong id="logEntitas" style="color: var(--neutral-900);">-</strong></div>
                <div><span style="color: var(--neutral-600);">IP Address:</span> <strong id="logIp" style="color: var(--neutral-900); font-family: monospace;">-</strong></div>
                <div style="grid-column: span 2;"><span style="color: var(--neutral-600);">Aksi Sistem:</span> <strong id="logAksi" style="color: var(--neutral-900);">-</strong></div>
                <div style="grid-column: span 2;"><span style="color: var(--neutral-600);">Request ID:</span> <span id="logRequestId" style="font-family: monospace; font-weight: 700; color: var(--neutral-900);">-</span></div>
                <div style="grid-column: span 2;"><span style="color: var(--neutral-600);">Konteks HTTP:</span> <span id="logHttp" style="font-family: monospace; color: var(--blue-700); font-weight: 700;">-</span></div>
            </div>

            <!-- Side-by-Side Diff Mutasi -->
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.375rem;">
                    Perbandingan Mutasi Data (Diff Inspector)
                </label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <div style="font-size: 0.6875rem; font-weight: 700; color: #DC2626; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                            <span>[-]</span> Nilai Lama (Sebelum Mutasi)
                        </div>
                        <pre id="logNilaiLama" style="background: rgba(239, 68, 68, 0.08); color: #B91C1C; border: 1px solid rgba(239, 68, 68, 0.25); padding: 0.875rem; border-radius: 8px; font-size: 0.75rem; overflow-x: auto; margin: 0; max-height: 200px; font-family: monospace; line-height: 1.4;"></pre>
                    </div>

                    <div>
                        <div style="font-size: 0.6875rem; font-weight: 700; color: #059669; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.25rem;">
                            <span>[+]</span> Nilai Baru (Setelah Mutasi)
                        </div>
                        <pre id="logNilaiBaru" style="background: rgba(16, 185, 129, 0.08); color: #047857; border: 1px solid rgba(16, 185, 129, 0.25); padding: 0.875rem; border-radius: 8px; font-size: 0.75rem; overflow-x: auto; margin: 0; max-height: 200px; font-family: monospace; line-height: 1.4;"></pre>
                    </div>
                </div>
            </div>

            <!-- Detail Event Payload -->
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.375rem;">
                    Detail Tambahan (Event Payload / Context)
                </label>
                <pre id="logPayload" style="background: #0f172a; color: #f8fafc; padding: 1rem; border-radius: 8px; font-size: 0.75rem; overflow-x: auto; margin: 0; max-height: 160px; font-family: monospace; line-height: 1.45;"></pre>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
            <button type="button" onclick="closeModalLogDetail()" style="height: 38px; padding: 0 1.5rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.8125rem; color: var(--neutral-800); cursor: pointer;">
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
