@props([
    'lastSweepAt' => null,
])

{{-- SUITE PEMELIHARAAN PROAKTIF 1-KLIK (MISSION CONTROL) (AC-3, AC-6) --}}
<div id="maintenanceSuite" class="telemetry-maintenance-card rounded-xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900 transition-all hover:shadow-sm">

    <!-- Header -->
    <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-2.5 dark:border-slate-800">
        <div class="flex items-center gap-2">
            <span class="flex h-5 w-5 items-center justify-center rounded-md text-white text-[10px] font-bold shadow-xs"
                  style="background: #003566;">
                ⚡
            </span>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white m-0">
                Suite Pemeliharaan Cepat Proaktif
            </h3>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-mono text-slate-500 rounded bg-slate-100 px-2 py-0.5 dark:bg-slate-800 dark:text-slate-400">
                SUPERADMIN
            </span>
        </div>
    </div>

    <!-- 4 Action Cards Matrix with Distinct SVG Logos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

        <!-- Action 1: Security Sweep -->
        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 p-3 hover:border-rose-400 hover:bg-rose-50/30 hover:shadow-xs transition-all dark:border-slate-800 dark:bg-slate-950/50 dark:hover:border-rose-900 dark:hover:bg-rose-950/20">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-500/15 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 border border-rose-500/30 shadow-xs shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </span>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white m-0">Security Sweep</h4>
                        <span class="rounded bg-rose-100 px-1 py-0.2 text-[8px] font-mono font-bold text-rose-700 dark:bg-rose-950 dark:text-rose-300">AUTH</span>
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">Sanitasi token & residu</div>
                </div>
            </div>
            <form action="{{ route('superadmin.health.security-sweep') }}" method="POST" class="m-0 shrink-0"
                  onsubmit="return confirm('Jalankan pembersihan higienitas keamanan sistem sekarang? Token kedaluwarsa, berkas arsip sementara (>2 jam), dan pendaftar unverified (>72 jam) akan dibersihkan.');">
                @csrf
                <button type="submit" class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-mono font-bold text-white shadow-2xs hover:bg-rose-700 transition-colors cursor-pointer focus-visible:ring-2 focus-visible:ring-[#3F51B5]">
                    Sweep
                </button>
            </form>
        </div>

        <!-- Action 2: InnoDB Vacuum -->
        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 p-3 hover:border-blue-400 hover:bg-blue-50/30 hover:shadow-xs transition-all dark:border-slate-800 dark:bg-slate-950/50 dark:hover:border-blue-900 dark:hover:bg-blue-950/20">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600/15 text-[#01347C] dark:bg-blue-500/20 dark:text-blue-300 border border-blue-500/30 shadow-xs shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7M4 7c0-2 1.5-3 3.5-3h9c2 0 3.5-1 3.5 3M4 7h16M4 12c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3" />
                    </svg>
                </span>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white m-0">InnoDB Vacuum</h4>
                        <span class="rounded bg-blue-100 px-1 py-0.2 text-[8px] font-mono font-bold text-blue-700 dark:bg-blue-950 dark:text-blue-300">DB</span>
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">Defrag tabel & indeks</div>
                </div>
            </div>
            <form action="{{ route('superadmin.health.optimize-db') }}" method="POST" class="m-0 shrink-0"
                  onsubmit="return confirm('Jalankan optimasi dan defragmentasi basis data sekarang? Perintah VACUUM/OPTIMIZE akan dijalankan pada tabel-tabel utama.');">
                @csrf
                <button type="submit" class="rounded-lg bg-[#01347C] px-3 py-1.5 text-xs font-mono font-bold text-white shadow-2xs hover:bg-[#02275D] transition-colors cursor-pointer focus-visible:ring-2 focus-visible:ring-[#3F51B5]">
                    Vacuum
                </button>
            </form>
        </div>

        <!-- Action 3: Purge Cache -->
        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 p-3 hover:border-emerald-400 hover:bg-emerald-50/30 hover:shadow-xs transition-all dark:border-slate-800 dark:bg-slate-950/50 dark:hover:border-emerald-900 dark:hover:bg-emerald-950/20">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 border border-emerald-500/30 shadow-xs shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </span>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white m-0">Purge Cache</h4>
                        <span class="rounded bg-emerald-100 px-1 py-0.2 text-[8px] font-mono font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">CACHE</span>
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">Flush views & config</div>
                </div>
            </div>
            <form action="{{ route('superadmin.health.clear-cache') }}" method="POST" class="m-0 shrink-0"
                  onsubmit="return confirm('Bersihkan seluruh cache aplikasi, views, konfigurasi, dan rute sekarang? Data cache akan di-refresh otomatis.');">
                @csrf
                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-mono font-bold text-white shadow-2xs hover:bg-emerald-700 transition-colors cursor-pointer focus-visible:ring-2 focus-visible:ring-[#3F51B5]">
                    Purge
                </button>
            </form>
        </div>

        <!-- Action 4: Storage Probe -->
        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 p-3 hover:border-cyan-400 hover:bg-cyan-50/30 hover:shadow-xs transition-all dark:border-slate-800 dark:bg-slate-950/50 dark:hover:border-cyan-900 dark:hover:bg-cyan-950/20">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-500/15 text-cyan-600 dark:bg-cyan-500/20 dark:text-cyan-300 border border-cyan-500/30 shadow-xs shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white m-0">Storage Probe</h4>
                        <span class="rounded bg-cyan-100 px-1 py-0.2 text-[8px] font-mono font-bold text-cyan-700 dark:bg-cyan-950 dark:text-cyan-300">I/O</span>
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">Uji integritas tulis/baca</div>
                </div>
            </div>
            <form action="{{ route('superadmin.health.test-storage') }}" method="POST" class="m-0 shrink-0">
                @csrf
                <button type="submit" class="rounded-lg bg-cyan-600 px-3 py-1.5 text-xs font-mono font-bold text-white shadow-2xs hover:bg-cyan-700 transition-colors cursor-pointer focus-visible:ring-2 focus-visible:ring-[#3F51B5]">
                    Probe
                </button>
            </form>
        </div>

    </div>

    <!-- Operational Footer -->
    <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-[11px] font-mono text-slate-400">
        <span class="flex items-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
            <span>Sweep Terakhir: {{ $lastSweepAt ?? 'Belum ada' }}</span>
        </span>
        <span>Audit: <strong class="text-emerald-600 dark:text-emerald-400">Sealed</strong></span>
    </div>

</div>
