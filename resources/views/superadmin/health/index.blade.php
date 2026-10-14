@extends('layouts.dashboard')

@section('title', 'Telemetri Kesehatan Sistem - SuperAdmin MagKom')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1400px; margin: 0 auto; width: 100%;">

    <style>
        .health-card {
            background: var(--neutral-0, #ffffff);
            border-radius: 14px;
            border: 1px solid var(--neutral-200, #E2E8F0);
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .metric-badge-ok {
            background: #DCFCE7;
            color: #166534;
            border: 1px solid #86EFAC;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .metric-badge-warn {
            background: #FEF3C7;
            color: #B45309;
            border: 1px solid #FDE68A;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        [data-theme="dark"] .health-card {
            background: var(--neutral-0, #182234) !important;
            border-color: var(--neutral-200, #26354D) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
        }
        [data-theme="dark"] .health-card h3,
        [data-theme="dark"] .health-card strong {
            color: var(--neutral-900, #F8FAFC) !important;
        }
        [data-theme="dark"] .health-card span {
            color: var(--neutral-500, #94A3B8);
        }
        [data-theme="dark"] .metric-badge-ok {
            background: rgba(34, 197, 94, 0.15) !important;
            color: #4ADE80 !important;
            border-color: rgba(34, 197, 94, 0.3) !important;
        }
        [data-theme="dark"] .metric-badge-warn {
            background: rgba(234, 179, 8, 0.15) !important;
            color: #FBBF24 !important;
            border-color: rgba(234, 179, 8, 0.3) !important;
        }
        [data-theme="dark"] .health-btn-hub {
            background: var(--neutral-100, #0F172A) !important;
            color: #60A5FA !important;
            border-color: #1E3A8A !important;
        }
        [data-theme="dark"] .health-btn-sweep {
            background: rgba(220, 38, 38, 0.15) !important;
            color: #F87171 !important;
            border-color: rgba(220, 38, 38, 0.3) !important;
        }
        [data-theme="dark"] .health-divider {
            border-bottom-color: var(--neutral-200, #26354D) !important;
        }
        [data-theme="dark"] .health-progress-bg {
            background: var(--neutral-100, #0F172A) !important;
        }
        [data-theme="dark"] h1 {
            color: #F8FAFC !important;
        }
        [data-theme="dark"] div[style*="border-bottom: 1px solid var(--neutral-200"] {
            border-bottom-color: #26354D !important;
        }
    </style>

    {{-- 1. HEADER & BREADCRUMB --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); padding-bottom: 1rem;">
        <div>
            <nav style="font-size: 0.8rem; color: var(--neutral-500, #64748B); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('superadmin.dashboard') }}" style="color: var(--neutral-600, #475569); text-decoration: none;">Dashboard SuperAdmin</a>
                <span>/</span>
                <span style="color: var(--neutral-900, #0F172A); font-weight: 600;">Telemetri Kesehatan Sistem</span>
            </nav>
            <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; letter-spacing: -0.02em;">
                    Panel Telemetri Kesehatan Infrastruktur
                </h1>
                <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: #DCFCE7; color: #166534; text-transform: uppercase; letter-spacing: 0.04em;">
                    ● All Systems Operational
                </span>
            </div>
            <p style="font-size: 0.85rem; color: var(--neutral-600, #475569); margin: 0.3rem 0 0 0;">
                Pemantauan instan integritas basis data, ketersediaan ruang penyimpanan dokumen, dan stabilitas server runtime.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="{{ route('superadmin.backup.index') }}" class="health-btn-hub" style="height: 38px; padding: 0 1rem; background: #ffffff; color: #0641CC; border: 1px solid #BFDBFE; border-radius: 8px; font-size: 0.85rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                <span>Backup Hub</span>
            </a>
            <a href="{{ route('superadmin.dashboard') }}" style="height: 38px; padding: 0 1rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                <span>Dashboard Utama</span>
            </a>
        </div>
    </div>



    {{-- 2. GRID TELEMETRI --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">

        {{-- KARTU 1: KESEHATAN BASIS DATA --}}
        <div class="health-card" style="border-top: 4px solid #0641CC;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0641CC" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                    <span>Koneksi Basis Data</span>
                </h3>
                @if($dbStatus === 'OK')
                    <span class="metric-badge-ok">● Terhubung (OK)</span>
                @else
                    <span class="metric-badge-warn">⚠️ Masalah Koneksi</span>
                @endif
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Driver Basis Data:</span>
                    <strong style="color: #0F172A; text-transform: uppercase;">{{ $dbDriver }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Latensi Query (Ping):</span>
                    <strong style="color: #16A34A;">{{ $dbLatency }} ms</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Nama Database:</span>
                    <strong style="color: #0F172A; font-family: monospace;">{{ $dbName }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">Jumlah Tabel Terdaftar:</span>
                    <strong style="color: #0641CC;">{{ $totalTables }} Tabel</strong>
                </div>
            </div>
        </div>

        {{-- KARTU 2: PENYIMPANAN STORAGE & BERKAS --}}
        <div class="health-card" style="border-top: 4px solid #10B981;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                    <span>Storage & Berkas Dokumen</span>
                </h3>
                @if($isWritable)
                    <span class="metric-badge-ok">● Izin Tulis Aktif</span>
                @else
                    <span class="metric-badge-warn">⚠️ Read-Only</span>
                @endif
            </div>

            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.35rem;">
                    <span style="color: #475569;">Kapasitas Disk Terpakai:</span>
                    <span style="color: #0641CC;">{{ $diskUsedPercent }}% (Sisa {{ $diskFreeGb }} GB)</span>
                </div>
                <div style="width: 100%; height: 8px; background: #E2E8F0; border-radius: 9999px; overflow: hidden;">
                    <div style="height: 100%; width: {{ min(100, $diskUsedPercent) }}%; background: #10B981; border-radius: 9999px;"></div>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Total Berkas Dokumen:</span>
                    <strong style="color: #0F172A;">{{ number_format($totalDokumenCount) }} Berkas</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Ukuran Berkas Tersimpan:</span>
                    <strong style="color: #0641CC;">{{ $totalDokumenMb }} MB</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">Snapshot Terakhir:</span>
                    <strong style="color: #475569;">{{ $lastBackupTime ?? 'Belum ada cadangan' }}</strong>
                </div>
            </div>
        </div>

        {{-- KARTU 3: RUNTIME & FRAMEWORK TELEMETRI --}}
        <div class="health-card" style="border-top: 4px solid #7C3AED;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2"><polyline points="4 17 10 11 4 5"></polyline><line x1="12" y1="19" x2="20" y2="19"></line></svg>
                    <span>Runtime & Environment</span>
                </h3>
                <span style="font-size: 0.75rem; font-weight: 700; color: #7C3AED; background: #F3E8FF; padding: 0.2rem 0.6rem; border-radius: 9999px;">
                    PHP {{ $runtimeInfo['php_version'] }}
                </span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Framework Version:</span>
                    <strong style="color: #0F172A;">Laravel {{ $runtimeInfo['laravel_version'] }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">App Environment:</span>
                    <strong style="color: #0641CC; text-transform: uppercase;">{{ $runtimeInfo['app_env'] }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Cache / Session:</span>
                    <strong style="color: #0F172A;">{{ $runtimeInfo['cache_driver'] }} / {{ $runtimeInfo['session_driver'] }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">Audit Log Trail:</span>
                    <strong style="color: #10B981;">{{ number_format($totalAudit) }} Log Tercatat</strong>
                </div>
            </div>
        </div>

        {{-- KARTU 4: HIGIENITAS KEAMANAN (SECURITY SWEEPER) --}}
        <div class="health-card" style="border-top: 4px solid #DC2626; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        <span>Higienitas Keamanan</span>
                    </h3>
                    <span class="{{ ($sweeperStats['pending_tokens'] > 0 || $sweeperStats['pending_temp_files'] > 0 || $sweeperStats['pending_unverified_users'] > 0) ? 'metric-badge-warn' : 'metric-badge-ok' }}">
                        {{ ($sweeperStats['pending_tokens'] > 0 || $sweeperStats['pending_temp_files'] > 0 || $sweeperStats['pending_unverified_users'] > 0) ? 'Perlu Pembersihan' : 'Higienis' }}
                    </span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.85rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                        <span style="color: #64748B;">Token Reset Kedaluwarsa:</span>
                        <strong style="color: {{ $sweeperStats['pending_tokens'] > 0 ? '#DC2626' : '#166534' }};">{{ $sweeperStats['pending_tokens'] }} Token</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                        <span style="color: #64748B;">Berkas ZIP Sementara (>2h):</span>
                        <strong style="color: {{ $sweeperStats['pending_temp_files'] > 0 ? '#DC2626' : '#166534' }};">{{ $sweeperStats['pending_temp_files'] }} Berkas</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #E2E8F0; padding-bottom: 0.5rem;">
                        <span style="color: #64748B;">Unverified Pendaftar (>72h):</span>
                        <strong style="color: {{ $sweeperStats['pending_unverified_users'] > 0 ? '#DC2626' : '#166534' }};">{{ $sweeperStats['pending_unverified_users'] }} Akun</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">Pembersihan Terakhir:</span>
                        <strong style="color: #475569;">{{ $sweeperStats['last_sweep_at'] ?? 'Belum ada log sweeper' }}</strong>
                    </div>
                </div>
            </div>

            <form action="{{ route('superadmin.health.security-sweep') }}" method="POST" onsubmit="return confirm('Jalankan pembersihan higienitas keamanan sistem sekarang? Token kedaluwarsa, residu arsip sementara, dan pendaftar tidak terverifikasi akan dibersihkan.');">
                @csrf
                <button type="submit" style="width: 100%; height: 38px; background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; border-radius: 8px; font-size: 0.8125rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.4rem; transition: all 0.15s ease;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    <span>Jalankan Security Sweep Sekarang</span>
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
