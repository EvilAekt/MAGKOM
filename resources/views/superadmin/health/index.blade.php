@extends('layouts.dashboard')

@section('title', 'Telemetri Kesehatan & Diagnostik Sistem - SuperAdmin MagKom')
@section('page_title', 'Panel Telemetri Kesehatan Infrastruktur')
@section('page_description', 'Pemantauan real-time latensi basis data, kapasitas storage, integritas server, dan suite pemeliharaan proaktif 1-klik.')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Telemetri Kesehatan</span>
@endsection

@section('page_actions')
    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <a href="{{ route('superadmin.backup.index') }}" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-0); color: var(--blue-700); border: 1px solid var(--blue-300); border-radius: 8px; font-size: 0.8125rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: var(--shadow-subtle);">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Backup Hub</span>
        </a>
        <a href="{{ route('superadmin.audit-log.index') }}" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-900); color: #FFFFFF; border: none; border-radius: 8px; font-size: 0.8125rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <span>Audit Trail Stream</span>
        </a>
    </div>
@endsection

@push('styles')
<style>
    .health-card {
        background: var(--neutral-0, #ffffff);
        border-radius: 14px;
        border: 1px solid var(--neutral-300, #E2E8F0);
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .metric-badge-ok {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.25);
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .metric-badge-warn {
        background: rgba(245, 158, 11, 0.12);
        color: #D97706;
        border: 1px solid rgba(245, 158, 11, 0.25);
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .action-diag-btn {
        width: 100%;
        height: 42px;
        border-radius: 8px;
        font-size: 0.8125rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.15s ease;
        border: 1px solid transparent;
        text-decoration: none;
    }

    .action-diag-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    [data-theme="dark"] .health-card {
        background-color: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] div[style*="background: var(--neutral-0)"] {
        background-color: #182234 !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] div[style*="background: var(--neutral-100)"] {
        background-color: #111C2E !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] h1, [data-theme="dark"] h2, [data-theme="dark"] h3, [data-theme="dark"] strong {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] span[style*="color: var(--neutral-600)"],
    [data-theme="dark"] p[style*="color: var(--neutral-600)"] {
        color: #94A3B8 !important;
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">

    {{-- ALERT FEEDBACK NOTIFIKASI --}}
    @if(session('status_keberhasilan'))
        <x-gentelella.callout type="success" title="Operasi Berhasil">
            {{ session('status_keberhasilan') }}
        </x-gentelella.callout>
    @endif

    @if(session('status_kesalahan'))
        <x-gentelella.callout type="danger" title="Operasi Gagal">
            {{ session('status_kesalahan') }}
        </x-gentelella.callout>
    @endif

    {{-- 1. SLA UPTIME & HEALTH STATUS HERO BANNER --}}
    <div style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); border-radius: 16px; padding: 1.75rem 2rem; color: #FFFFFF; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; flex-wrap: wrap;">
                <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; background: rgba(16, 185, 129, 0.2); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.4); text-transform: uppercase; letter-spacing: 0.05em;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #34D399; box-shadow: 0 0 8px #34D399;"></span>
                    {{ $slaStatus }}
                </span>
                <span style="font-size: 0.8125rem; color: #94A3B8;">Tingkat Keandalan Layanan Instansi: <strong>{{ $slaPercent }}% Uptime</strong></span>
            </div>
            <h2 style="font-size: 1.625rem; font-weight: 800; margin: 0; letter-spacing: -0.02em; color: #FFFFFF;">
                Pusat Telemetri Infrastruktur Portal MagKom
            </h2>
            <p style="font-size: 0.875rem; color: #CBD5E1; margin: 0.35rem 0 0 0; max-width: 680px;">
                Dinas Komunikasi dan Informatika Kabupaten Sleman &mdash; Sistem aktif memantau integritas data, ketersediaan disk storage, latensi query, dan thread pemrosesan.
            </p>
        </div>

        <div style="display: flex; gap: 1.5rem; align-items: center;">
            <div style="text-align: right; border-right: 1px solid rgba(255, 255, 255, 0.15); padding-right: 1.5rem;">
                <div style="font-size: 0.75rem; color: #94A3B8; text-transform: uppercase; font-weight: 700;">Ping Latensi DB</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: {{ $dbLatency < 25 ? '#34D399' : ($dbLatency < 100 ? '#FBBF24' : '#F87171') }}; font-family: monospace;">
                    {{ $dbLatency }} ms
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.75rem; color: #94A3B8; text-transform: uppercase; font-weight: 700;">Event Log 24 Jam</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #60A5FA; font-family: monospace;">
                    {{ number_format($audit24h) }}
                </div>
            </div>
        </div>
    </div>

    {{-- 2. SUITE PEMELIHARAAN CEPAT (ONE-CLICK DIAGNOSTIC SUITE - 4 ACTIONS) --}}
    <x-gentelella.card
        title="Suite Pemeliharaan Cepat Proaktif (One-Click Diagnostic Suite)"
        subtitle="Jalankan aksi pemeliharaan langsung untuk mencegah hambatan operasional dan memastikan kestabilan portal."
    >
        <x-slot:icon>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--blue-700, #0641CC)" stroke-width="2.5">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
            </svg>
        </x-slot:icon>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <!-- Aksi 1: Security Sweep -->
            <form action="{{ route('superadmin.health.security-sweep') }}" method="POST" onsubmit="return confirm('Jalankan pembersihan higienitas keamanan sistem sekarang? Token kedaluwarsa, berkas arsip sementara (>2 jam), dan pendaftar unverified (>72 jam) akan dibersihkan.');">
                @csrf
                <button type="submit" class="action-diag-btn" style="background: rgba(239, 68, 68, 0.1); color: #DC2626; border-color: rgba(239, 68, 68, 0.25);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <span>1. Security Sweep Higienis</span>
                </button>
            </form>

            <!-- Aksi 2: Optimasi Basis Data -->
            <form action="{{ route('superadmin.health.optimize-db') }}" method="POST" onsubmit="return confirm('Jalankan optimasi dan defragmentasi basis data sekarang? Perintah VACUUM/OPTIMIZE akan dijalankan pada tabel-tabel utama.');">
                @csrf
                <button type="submit" class="action-diag-btn" style="background: rgba(37, 99, 235, 0.1); color: #2563EB; border-color: rgba(37, 99, 235, 0.25);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                    <span>2. Optimasi &amp; Defrag Basis Data</span>
                </button>
            </form>

            <!-- Aksi 3: Bersihkan Seluruh Cache -->
            <form action="{{ route('superadmin.health.clear-cache') }}" method="POST" onsubmit="return confirm('Bersihkan seluruh cache aplikasi, views, konfigurasi, dan rute sekarang? Data cache akan di-refresh otomatis.');">
                @csrf
                <button type="submit" class="action-diag-btn" style="background: rgba(16, 185, 129, 0.1); color: #059669; border-color: rgba(16, 185, 129, 0.25);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                    <span>3. Bersihkan Cache Sistem</span>
                </button>
            </form>

            <!-- Aksi 4: Uji Izin Tulis Storage Disk -->
            <form action="{{ route('superadmin.health.test-storage') }}" method="POST">
                @csrf
                <button type="submit" class="action-diag-btn" style="background: rgba(124, 58, 237, 0.1); color: #7C3AED; border-color: rgba(124, 58, 237, 0.25);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                    <span>4. Uji Tulis Storage Disk Probe</span>
                </button>
            </form>
        </div>
    </x-gentelella.card>

    {{-- 3. GRID TELEMETRI UTAMA --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">

        {{-- KARTU 1: BASIS DATA --}}
        <x-gentelella.card
            title="Koneksi Basis Data"
            style="border-top: 4px solid #0641CC;"
        >
            <x-slot:icon>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0641CC" stroke-width="2">
                    <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                </svg>
            </x-slot:icon>

            <x-slot:actions>
                @if($dbStatus === 'OK')
                    <span class="metric-badge-ok">● Terhubung (OK)</span>
                @else
                    <span class="metric-badge-warn">⚠️ Bermasalah</span>
                @endif
            </x-slot:actions>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.8125rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--neutral-300); padding-bottom: 0.5rem;">
                    <span style="color: var(--neutral-600);">Driver Basis Data:</span>
                    <strong style="color: var(--neutral-900); text-transform: uppercase;">{{ $dbDriver }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--neutral-300); padding-bottom: 0.5rem;">
                    <span style="color: var(--neutral-600);">Latensi Query (SELECT 1):</span>
                    <strong style="color: {{ $dbLatency < 25 ? '#059669' : ($dbLatency < 100 ? '#D97706' : '#DC2626') }}; font-family: monospace;">{{ $dbLatency }} ms</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--neutral-300); padding-bottom: 0.5rem;">
                    <span style="color: var(--neutral-600);">Nama Database:</span>
                    <strong style="color: var(--neutral-900); font-family: monospace;">{{ $dbName }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-600);">Jumlah Tabel Terdaftar:</span>
                    <strong style="color: var(--blue-700);">{{ $totalTables }} Tabel</strong>
                </div>
            </div>
        </x-gentelella.card>

        {{-- KARTU 2: PENYIMPANAN STORAGE & DISK --}}
        <x-gentelella.card
            title="Storage &amp; Kapasitas Disk"
            style="border-top: 4px solid #10B981;"
        >
            <x-slot:icon>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                </svg>
            </x-slot:icon>

            <x-slot:actions>
                @if($isWritable)
                    <span class="metric-badge-ok">● Izin Tulis Aktif</span>
                @else
                    <span class="metric-badge-warn">⚠️ Read-Only</span>
                @endif
            </x-slot:actions>

            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.35rem;">
                    <span style="color: var(--neutral-600);">Kapasitas Disk Terpakai:</span>
                    <span style="color: var(--blue-700);">{{ $diskUsedPercent }}% (Sisa {{ $diskFreeGb }} GB)</span>
                </div>
                <div style="width: 100%; height: 8px; background: var(--neutral-200); border-radius: 9999px; overflow: hidden;">
                    <div style="height: 100%; width: {{ min(100, $diskUsedPercent) }}%; background: {{ $diskUsedPercent > 85 ? '#DC2626' : '#10B981' }}; border-radius: 9999px;"></div>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.8125rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--neutral-300); padding-bottom: 0.5rem;">
                    <span style="color: var(--neutral-600);">Total Berkas Dokumen:</span>
                    <strong style="color: var(--neutral-900);">{{ number_format($totalDokumenCount) }} Berkas</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--neutral-300); padding-bottom: 0.5rem;">
                    <span style="color: var(--neutral-600);">Ukuran Berkas Dokumen:</span>
                    <strong style="color: var(--blue-700);">{{ $totalDokumenMb }} MB</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-600);">Snapshot Backup Terakhir:</span>
                    <strong style="color: var(--neutral-700);">{{ $lastBackupTime ?? 'Belum ada cadangan' }}</strong>
                </div>
            </div>
        </x-gentelella.card>

        {{-- KARTU 3: RUNTIME & TELEMETRI FRAMEWORK --}}
        <x-gentelella.card
            title="Runtime & Threads"
            style="border-top: 4px solid #7C3AED;"
        >
            <x-slot:icon>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2">
                    <polyline points="4 17 10 11 4 5"></polyline>
                    <line x1="12" y1="19" x2="20" y2="19"></line>
                </svg>
            </x-slot:icon>

            <x-slot:actions>
                <span style="font-size: 0.72rem; font-weight: 700; color: #7C3AED; background: #F3E8FF; padding: 0.2rem 0.6rem; border-radius: 9999px;">
                    PHP {{ $runtimeInfo['php_version'] }}
                </span>
            </x-slot:actions>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.8125rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--neutral-300); padding-bottom: 0.5rem;">
                    <span style="color: var(--neutral-600);">Laravel Framework:</span>
                    <strong style="color: var(--neutral-900);">v{{ $runtimeInfo['laravel_version'] }} ({{ strtoupper($runtimeInfo['app_env']) }})</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--neutral-300); padding-bottom: 0.5rem;">
                    <span style="color: var(--neutral-600);">Driver Cache / Session:</span>
                    <strong style="color: var(--neutral-900);">{{ $runtimeInfo['cache_driver'] }} / {{ $runtimeInfo['session_driver'] }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--neutral-300); padding-bottom: 0.5rem;">
                    <span style="color: var(--neutral-600);">Queue Failed Jobs:</span>
                    <strong style="color: {{ $failedJobsCount > 0 ? '#DC2626' : '#059669' }};">{{ $failedJobsCount }} Gagal</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-600);">Total Log Audit:</span>
                    <strong style="color: #059669;">{{ number_format($totalAudit) }} Log</strong>
                </div>
            </div>
        </x-gentelella.card>

        {{-- KARTU 4: HIGIENITAS KEAMANAN --}}
        <x-gentelella.card
            title="Higienitas Keamanan"
            style="border-top: 4px solid #DC2626;"
        >
            <x-slot:icon>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </x-slot:icon>

            <x-slot:actions>
                <span class="{{ ($sweeperStats['pending_tokens'] > 0 || $sweeperStats['pending_temp_files'] > 0 || $sweeperStats['pending_unverified_users'] > 0) ? 'metric-badge-warn' : 'metric-badge-ok' }}">
                    {{ ($sweeperStats['pending_tokens'] > 0 || $sweeperStats['pending_temp_files'] > 0 || $sweeperStats['pending_unverified_users'] > 0) ? 'Perlu Sweep' : 'Higienis' }}
                </span>
            </x-slot:actions>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.8125rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--neutral-300); padding-bottom: 0.5rem;">
                    <span style="color: var(--neutral-600);">Token Reset Kedaluwarsa:</span>
                    <strong style="color: {{ $sweeperStats['pending_tokens'] > 0 ? '#DC2626' : '#059669' }};">{{ $sweeperStats['pending_tokens'] }} Token</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--neutral-300); padding-bottom: 0.5rem;">
                    <span style="color: var(--neutral-600);">Berkas ZIP Sementara (>2 jam):</span>
                    <strong style="color: {{ $sweeperStats['pending_temp_files'] > 0 ? '#DC2626' : '#059669' }};">{{ $sweeperStats['pending_temp_files'] }} Berkas</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--neutral-300); padding-bottom: 0.5rem;">
                    <span style="color: var(--neutral-600);">Pendaftar Unverified (>72 jam):</span>
                    <strong style="color: {{ $sweeperStats['pending_unverified_users'] > 0 ? '#DC2626' : '#059669' }};">{{ $sweeperStats['pending_unverified_users'] }} Akun</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--neutral-600);">Sweep Terakhir:</span>
                    <strong style="color: var(--neutral-700);">{{ $sweeperStats['last_sweep_at'] ?? 'Belum ada catatan' }}</strong>
                </div>
            </div>
        </x-gentelella.card>

    </div>
</div>
@endsection
