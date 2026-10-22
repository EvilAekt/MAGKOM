@extends('layouts.dashboard')

@section('title', 'Hub Cadangan Basis Data (Disaster Recovery) - SuperAdmin MagKom')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1400px; margin: 0 auto; width: 100%;">

    <style>
        .backup-stat-card {
            background: var(--neutral-0, #ffffff);
            border-radius: 14px;
            border: 1px solid var(--neutral-200, #E2E8F0);
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .backup-container {
            background: var(--neutral-0, #ffffff);
            border: 1px solid var(--neutral-200, #E2E8F0);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .backup-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }
        .backup-table th {
            background: #F8FAFC;
            padding: 0.85rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--neutral-600, #475569);
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
        }
        .backup-table td {
            padding: 0.95rem 1.25rem;
            border-bottom: 1px solid var(--neutral-200, #E2E8F0);
            vertical-align: middle;
            color: var(--neutral-800, #1E293B);
        }
        .backup-table tr:hover {
            background: #F8FAFC;
        }
        [data-theme="dark"] .backup-stat-card {
            background: var(--neutral-0, #182234) !important;
            border-color: var(--neutral-200, #26354D) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
        }
        [data-theme="dark"] .backup-stat-card span {
            color: var(--neutral-400, #94A3B8);
        }
        [data-theme="dark"] .backup-container {
            background: var(--neutral-0, #182234) !important;
            border-color: var(--neutral-200, #26354D) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
        }
        [data-theme="dark"] .backup-table th {
            background: var(--neutral-100, #0E1726) !important;
            color: var(--neutral-400, #94A3B8) !important;
            border-bottom-color: var(--neutral-200, #26354D) !important;
        }
        [data-theme="dark"] .backup-table td {
            color: var(--neutral-700, #E2E8F0) !important;
            border-bottom-color: var(--neutral-200, #26354D) !important;
        }
        [data-theme="dark"] .backup-table tr:hover {
            background: var(--neutral-100, #141F30) !important;
        }
        [data-theme="dark"] .backup-checksum-badge {
            background: var(--neutral-100, #0F172A) !important;
            color: var(--neutral-400, #94A3B8) !important;
            border-color: var(--neutral-200, #26354D) !important;
        }
        [data-theme="dark"] .backup-btn-delete {
            background: var(--neutral-100, #0F172A) !important;
            color: #F87171 !important;
            border-color: rgba(220, 38, 38, 0.35) !important;
        }
        [data-theme="dark"] a[href*="health"] {
            background-color: #111C2E !important;
            border-color: #26354D !important;
            color: #94A3B8 !important;
        }
        [data-theme="dark"] h1,
        [data-theme="dark"] .backup-stat-card strong {
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
                <span style="color: var(--neutral-900, #0F172A); font-weight: 600;">Disaster Recovery Hub</span>
            </nav>
            <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; letter-spacing: -0.02em;">
                    Pusat Cadangan & Pemulihan Basis Data
                </h1>
                <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: #FEF3C7; color: #92400E; text-transform: uppercase; letter-spacing: 0.04em;">
                    SEC-03 SuperAdmin Only
                </span>
            </div>
            <p style="font-size: 0.85rem; color: var(--neutral-600, #475569); margin: 0.3rem 0 0 0;">
                Pembuatan snapshot cadangan basis data terisolasi dan verifikasi integritas checksum forensik SHA-256.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="{{ route('superadmin.health.index') }}" style="height: 38px; padding: 0 1rem; background: #ffffff; color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                <span>Telemetri Sistem</span>
            </a>

            <form method="POST" action="{{ route('superadmin.backup.snapshot') }}" onsubmit="return confirm('Picu pembuatan snapshot database baru sekarang?')">
                @csrf
                <button type="submit" style="height: 38px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: 0 2px 6px rgba(6, 65, 204, 0.25);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                    <span>Buat Snapshot Baru</span>
                </button>
            </form>
        </div>
    </div>



    {{-- 2. METRIK KPI --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
        <x-gentelella.card style="border-left: 4px solid #0641CC;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="font-size: 0.8rem; font-weight: 700; color: var(--neutral-600, #475569); display: block; margin-bottom: 0.25rem;">Total Snapshot Tersedia</span>
                    <span style="font-size: 1.85rem; font-weight: 800; color: #0641CC;">{{ number_format($totalBackups) }}</span>
                    <span style="font-size: 0.75rem; color: var(--neutral-500, #64748B); display: block; margin-top: 0.25rem;">Berkas cadangan valid</span>
                </div>
                <div style="width: 46px; height: 46px; border-radius: 10px; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                </div>
            </div>
        </x-gentelella.card>

        <x-gentelella.card style="border-left: 4px solid #10B981;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <span style="font-size: 0.8rem; font-weight: 700; color: var(--neutral-600, #475569); display: block; margin-bottom: 0.25rem;">Total Ukuran Berkas</span>
                    <span style="font-size: 1.85rem; font-weight: 800; color: var(--neutral-900, #0F172A);">{{ $totalSizeMb }} MB</span>
                    <span style="font-size: 0.75rem; color: #166534; display: block; margin-top: 0.25rem;">Tersimpan di storage lokal</span>
                </div>
                <div style="width: 46px; height: 46px; border-radius: 10px; background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                </div>
            </div>
        </x-gentelella.card>
    </div>

    {{-- 3. TABEL DAFTAR BACKUP --}}
    <x-gentelella.card
        title="Daftar Snapshot Cadangan Basis Data"
        subtitle="Arsip cadangan data magang Kominfo dengan validasi integritas checksum SHA-256."
    >
        <x-slot:icon>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-primary">
                <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
            </svg>
        </x-slot:icon>

        @if($backupFiles->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600, #475569);">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: #EFF6FF; color: #0641CC; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0 0 0.25rem 0;">Belum Ada Snapshot Cadangan</h3>
                <p style="font-size: 0.825rem; margin: 0 0 1rem 0;">Buat snapshot pertama basis data untuk menjamin keamanan pemulihan bencana.</p>
                <form method="POST" action="{{ route('superadmin.backup.snapshot') }}" style="display: inline-block;">
                    @csrf
                    <button type="submit" style="height: 36px; padding: 0 1rem; background: #0641CC; color: #ffffff; border: none; border-radius: 6px; font-size: 0.825rem; font-weight: 700; cursor: pointer;">
                        + Buat Snapshot Sekarang
                    </button>
                </form>
            </div>
        @else
            <div style="overflow-x: auto; margin: -10px -20px -20px -20px;">
                <table class="backup-table">
                    <thead>
                        <tr>
                            <th>Nama Berkas Snapshot</th>
                            <th>Waktu Pembuatan</th>
                            <th>Ukuran Berkas</th>
                            <th>Integritas Checksum (SHA-256)</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backupFiles as $item)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: #0641CC; font-family: monospace;">{{ $item['filename'] }}</div>
                                </td>
                                <td>
                                    <div style="font-size: 0.825rem; color: #334155;">{{ $item['created_at'] }}</div>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: var(--neutral-900, #0F172A);">{{ $item['size_mb'] }} MB</span>
                                </td>
                                <td>
                                    <span class="backup-checksum-badge" style="font-family: monospace; font-size: 0.72rem; color: #64748B; background: #F1F5F9; padding: 0.2rem 0.5rem; border-radius: 4px; border: 1px solid #E2E8F0;" title="{{ $item['checksum'] }}">
                                        {{ substr($item['checksum'], 0, 16) }}...{{ substr($item['checksum'], -8) }}
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="{{ route('superadmin.backup.download', $item['filename']) }}" 
                                       style="padding: 0.35rem 0.75rem; background: #0641CC; color: #ffffff; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none; margin-right: 0.25rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        <span>Unduh</span>
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.backup.destroy', $item['filename']) }}" style="display: inline-block;" onsubmit="return confirm('Hapus berkas snapshot ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="backup-btn-delete" style="padding: 0.35rem 0.65rem; background: #ffffff; border: 1px solid #FECACA; color: #DC2626; border-radius: 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-gentelella.card>

</div>
@endsection
