@props([
    'lastSweepAt' => null,
])

{{-- SUITE PEMELIHARAAN PROAKTIF (AC-5, AC-7 Canvas Parity) --}}
<div id="maintenanceSuite" class="telemetry-card mb-0">

    <!-- Header -->
    <div class="telemetry-card-header">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span class="flex h-6 w-6 items-center justify-center rounded-lg shadow-xs"
                  style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; display: inline-flex; align-items: center; justify-content: center; background: rgba(59, 130, 246, 0.15); color: #38bdf8; border: 1px solid rgba(59, 130, 246, 0.35); border-radius: 6px;">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14" style="width: 14px; height: 14px; max-width: 14px; max-height: 14px;" aria-hidden="true">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
            </span>
            <h3 class="telemetry-card-title">
                Aksi Pemeliharaan Proaktif
            </h3>
        </div>
        <span class="rounded px-2 py-0.5 text-[10px] font-mono"
              style="background: var(--tel-surface-elevated); color: var(--tel-text-muted); border: 1px solid var(--tel-card-border); border-radius: 4px;">
            SuperAdmin
        </span>
    </div>

    <!-- 4 Action Cards Matrix with Distinct Logo/Icon per Action (Pure CSS Grid Parity) -->
    <div class="maintenance-actions-grid">

        <!-- Action 1: Security Sweep -->
        <div class="telemetry-action-card">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl"
                      style="width: 36px; height: 36px; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(244, 63, 94, 0.15); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.35); border-radius: 10px;">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </span>
                <div>
                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                        <h4 style="font-weight: 700; font-size: 12px; color: var(--tel-text-primary); margin: 0;">Security Sweep</h4>
                        <span class="rounded text-[8px] font-mono font-bold"
                              style="background: rgba(244, 63, 94, 0.25); color: #fda4af; padding: 1px 4px; border-radius: 3px;">AUTH</span>
                    </div>
                </div>
            </div>
            <form action="{{ route('superadmin.health.security-sweep') }}" method="POST" style="margin: 0; flex-shrink: 0;"
                  onsubmit="return confirm('Jalankan pembersihan higienitas keamanan sistem sekarang? Token kedaluwarsa, berkas arsip sementara (>2 jam), dan pendaftar unverified (>72 jam) akan dibersihkan.');">
                @csrf
                <button type="submit" class="btn-telemetry-action btn-sweep">
                    Sweep
                </button>
            </form>
        </div>

        <!-- Action 2: InnoDB Vacuum -->
        <div class="telemetry-action-card">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl"
                      style="width: 36px; height: 36px; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.35); border-radius: 10px;">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7M4 7c0-2 1.5-3 3.5-3h9c2 0 3.5-1 3.5 3M4 7h16M4 12c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3" />
                    </svg>
                </span>
                <div>
                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                        <h4 style="font-weight: 700; font-size: 12px; color: var(--tel-text-primary); margin: 0;">InnoDB Vacuum</h4>
                        <span class="rounded text-[8px] font-mono font-bold"
                              style="background: rgba(59, 130, 246, 0.25); color: #93c5fd; padding: 1px 4px; border-radius: 3px;">DB</span>
                    </div>
                </div>
            </div>
            <form action="{{ route('superadmin.health.optimize-db') }}" method="POST" style="margin: 0; flex-shrink: 0;"
                  onsubmit="return confirm('Jalankan optimasi dan defragmentasi basis data sekarang? Perintah VACUUM/OPTIMIZE akan dijalankan pada tabel-tabel utama.');">
                @csrf
                <button type="submit" class="btn-telemetry-action btn-vacuum">
                    Vacuum
                </button>
            </form>
        </div>

        <!-- Action 3: Purge Cache -->
        <div class="telemetry-action-card">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl"
                      style="width: 36px; height: 36px; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.35); border-radius: 10px;">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </span>
                <div>
                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                        <h4 style="font-weight: 700; font-size: 12px; color: var(--tel-text-primary); margin: 0;">Purge Cache</h4>
                        <span class="rounded text-[8px] font-mono font-bold"
                              style="background: rgba(16, 185, 129, 0.25); color: #6ee7b7; padding: 1px 4px; border-radius: 3px;">REDIS</span>
                    </div>
                </div>
            </div>
            <form action="{{ route('superadmin.health.clear-cache') }}" method="POST" style="margin: 0; flex-shrink: 0;"
                  onsubmit="return confirm('Bersihkan seluruh cache aplikasi, views, konfigurasi, dan rute sekarang? Data cache akan di-refresh otomatis.');">
                @csrf
                <button type="submit" class="btn-telemetry-action btn-purge">
                    Purge
                </button>
            </form>
        </div>

        <!-- Action 4: Storage Probe -->
        <div class="telemetry-action-card">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl"
                      style="width: 36px; height: 36px; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(6, 182, 212, 0.15); color: #22d3ee; border: 1px solid rgba(6, 182, 212, 0.35); border-radius: 10px;">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
                <div>
                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                        <h4 style="font-weight: 700; font-size: 12px; color: var(--tel-text-primary); margin: 0;">Storage Probe</h4>
                        <span class="rounded text-[8px] font-mono font-bold"
                              style="background: rgba(6, 182, 212, 0.25); color: #a5f3fc; padding: 1px 4px; border-radius: 3px;">I/O</span>
                    </div>
                </div>
            </div>
            <form action="{{ route('superadmin.health.test-storage') }}" method="POST" style="margin: 0; flex-shrink: 0;"
                  onsubmit="return confirm('Jalankan pengujian I/O disk storage sekarang? Berkas probe sementara akan ditulis dan diverifikasi.');">
                @csrf
                <button type="submit" class="btn-telemetry-action btn-probe">
                    Probe
                </button>
            </form>
        </div>

    </div>

    <!-- Action Footer with Status Info (Canvas Parity) -->
    <div style="margin-top: 14px; padding-top: 10px; border-top: 1px solid var(--tel-card-border); display: flex; align-items: center; justify-content: space-between; font-size: 11px; font-family: monospace; color: var(--tel-text-muted);">
        <span style="display: inline-flex; align-items: center; gap: 6px;">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" style="width: 6px; height: 6px; display: inline-block;"></span>
            <span>Terakhir: {{ $lastSweepAt ?? '4m lalu' }}</span>
        </span>
        <span style="color: var(--tel-text-muted);">Timeout: 30s</span>
    </div>

</div>
