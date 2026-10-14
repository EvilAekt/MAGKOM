@extends('layouts.dashboard')

@section('title', 'Jejak Audit Trail - SuperAdmin MagKom')
@section('page_title', 'Jejak Audit Sistem (Audit Trail)')
@section('page_description', 'Log pencatatan aktivitas, mutasi data, dan keamanan portal yang bersifat strictly read-only (AC-13-6).')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Audit Log</span>
@endsection

@section('page_actions')
    <a href="{{ route('superadmin.audit-log.export', request()->query()) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.25rem; background: var(--green-700); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border-radius: 8px; text-decoration: none; box-shadow: var(--shadow-subtle); transition: all 0.15s ease;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        <span>Ekspor CSV</span>
    </a>
@endsection

@push('styles')
<style>
    /* Dark Mode Overrides for SuperAdmin Audit Log */
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

    [data-theme="dark"] button[style*="background: var(--neutral-900)"] {
        background-color: #2563EB !important;
        color: #FFFFFF !important;
    }

    [data-theme="dark"] div[style*="border: 1px solid var(--neutral-400)"],
    [data-theme="dark"] div[style*="border-bottom: 1px solid var(--neutral-400)"],
    [data-theme="dark"] div[style*="border-top: 1px solid var(--neutral-200)"] {
        border-color: #26354D !important;
    }

    [data-theme="dark"] table thead tr {
        background-color: #141E30 !important;
        border-bottom-color: #26354D !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] table th {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] table tbody tr {
        border-bottom-color: #26354D !important;
    }

    [data-theme="dark"] table tbody tr:hover {
        background-color: #162238 !important;
    }

    [data-theme="dark"] span[style*="color: var(--neutral-900)"],
    [data-theme="dark"] div[style*="color: var(--neutral-900)"],
    [data-theme="dark"] strong[style*="color: var(--neutral-900)"],
    [data-theme="dark"] span[style*="color: var(--neutral-800)"],
    [data-theme="dark"] h3[style*="color: var(--neutral-800)"] {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] span[style*="color: var(--neutral-600)"],
    [data-theme="dark"] div[style*="color: var(--neutral-600)"],
    [data-theme="dark"] p[style*="color: var(--neutral-600)"],
    [data-theme="dark"] label[style*="color: var(--neutral-700)"] {
        color: #94A3B8 !important;
    }

    /* Modal Inspection Dark Mode */
    [data-theme="dark"] #modalLogDetail > div {
        background-color: #182234 !important;
        border-color: #26354D !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] #modalLogDetail h3 {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] #modalLogDetail div[style*="background: var(--neutral-100)"] {
        background-color: #0F172A !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] button[style*="background: var(--neutral-100)"] {
        background-color: #111C2E !important;
        border-color: #26354D !important;
        color: #94A3B8 !important;
    }

    [data-theme="dark"] button[style*="background: var(--neutral-100)"]:hover {
        background-color: #162238 !important;
        color: #F8FAFC !important;
    }
</style>
@endpush

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- 1. FILTER TOOLBAR CARD LENGKAP -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <form method="GET" action="{{ route('superadmin.audit-log.index') }}" style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div style="grid-column: span 2; min-width: 280px;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.375rem;">Kata Kunci / Pencarian</label>
                    <div style="position: relative;">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari aksi, user, entitas, IP, request ID..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem 0 2.5rem; font-size: 0.875rem; font-family: inherit;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: var(--neutral-500);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.375rem;">Tipe Log</label>
                    <select name="tipe" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                        <option value="">Semua Tipe</option>
                        @foreach($tipeEnum as $tipe)
                            <option value="{{ $tipe->value }}" {{ request('tipe') === $tipe->value ? 'selected' : '' }}>
                                {{ ucfirst($tipe->value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.375rem;">Aktor / Staf</label>
                    <select name="user_id" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0); font-family: inherit;">
                        <option value="">Semua Staf & Akun</option>
                        @foreach($internalUsers ?? [] as $staf)
                            <option value="{{ $staf->id }}" {{ request('user_id') == $staf->id ? 'selected' : '' }}>
                                {{ $staf->nama }} ({{ $staf->role->label() }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.375rem;">Entitas / Tabel</label>
                    <input type="text" name="entitas" value="{{ request('entitas') }}" placeholder="pendaftaran..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; font-family: inherit;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.375rem;">Request ID</label>
                    <input type="text" name="request_id" value="{{ request('request_id') }}" placeholder="req-..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; font-family: monospace;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.375rem;">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; font-family: inherit;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.375rem;">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; font-family: inherit;">
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; align-items: center; justify-content: flex-end; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                @if(request()->hasAny(['q', 'tipe', 'entitas', 'dari_tanggal', 'sampai_tanggal', 'request_id', 'user_id']))
                    <a href="{{ route('superadmin.audit-log.index') }}" style="height: 40px; padding: 0 1rem; display: inline-flex; align-items: center; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600); text-decoration: none;">
                        Reset Filter
                    </a>
                @endif
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--neutral-900); color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.375rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- 2. DATA TABLE CARD -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">Rekaman Peristiwa & Mutasi Data (Append-Only)</h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">Seluruh mutasi tercatat secara persisten dan terlindung dari manipulasi manual.</p>
            </div>
            <span style="font-size: 0.8125rem; color: var(--neutral-600); font-weight: 600;">
                Total Log: <strong>{{ method_exists($logs, 'total') ? $logs->total() : $logs->count() }} Baris</strong>
            </span>
        </div>

        @if($logs->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line></svg>
                </div>
                <p style="font-size: 1rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Tidak ada rekaman jejak audit yang sesuai dengan filter.</p>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Silakan ubah rentang tanggal atau bersihkan filter pencarian.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1rem; font-weight: 700; width: 160px;">Waktu & Tanggal</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Pelaku / User</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; width: 110px; text-align: center;">Tipe Log</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Entitas & ID</th>
                            <th style="padding: 1rem 1rem; font-weight: 700; width: 130px;">Alamat IP</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right; width: 110px;">Inspeksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            @php
                                $tipeVal = $log->tipe->value ?? (string)$log->tipe;
                                $tipeBadgeBg = match($tipeVal) {
                                    'auth' => '#F3E8FF',
                                    'aksi' => 'var(--blue-tint)',
                                    'service' => 'var(--amber-100)',
                                    'akses_data' => 'var(--green-100)',
                                    default => 'var(--neutral-150)'
                                };
                                $tipeBadgeText = match($tipeVal) {
                                    'auth' => '#6B21A8',
                                    'aksi' => 'var(--blue-700)',
                                    'service' => 'var(--amber-700)',
                                    'akses_data' => 'var(--green-700)',
                                    default => 'var(--neutral-700)'
                                };
                                $tipeBadgeBorder = match($tipeVal) {
                                    'auth' => 'rgba(107, 33, 168, 0.2)',
                                    'aksi' => 'var(--blue-tint-line)',
                                    'service' => 'var(--amber-300)',
                                    'akses_data' => 'rgba(22, 163, 74, 0.2)',
                                    default => 'var(--neutral-300)'
                                };
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-200); transition: background 0.15s ease;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 0.875rem 1rem; white-space: nowrap; font-family: monospace; font-size: 0.75rem; color: var(--neutral-700);">
                                    <div style="font-weight: 700; color: var(--neutral-900);">
                                        {{ $log->waktu?->format('d M Y') ?? '-' }}
                                    </div>
                                    <div>{{ $log->waktu?->format('H:i:s') ?? '-' }} WIB</div>
                                </td>
                                <td style="padding: 0.875rem 1rem;">
                                    @if($log->user)
                                        <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.875rem;">
                                            {{ $log->user->nama ?? $log->user->name }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--neutral-600);">
                                            {{ $log->user->role?->label() ?? $log->user->role }}
                                        </div>
                                    @else
                                        <span style="color: var(--neutral-500); font-style: italic; font-size: 0.8125rem;">
                                            Sistem Otomatis / Tamu
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 0.875rem 1rem; text-align: center; white-space: nowrap;">
                                    <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: {{ $tipeBadgeBg }}; color: {{ $tipeBadgeText }}; border: 1px solid {{ $tipeBadgeBorder }};">
                                        {{ ucfirst($tipeVal) }}
                                    </span>
                                </td>
                                <td style="padding: 0.875rem 1rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900);">
                                        {{ $log->entitas ?? '-' }}
                                    </div>
                                    @if($log->aksi)
                                        <div style="font-size: 0.75rem; color: var(--blue-700); font-family: monospace; margin-top: 0.15rem;">
                                            {{ $log->aksi }}
                                        </div>
                                    @endif
                                    @if($log->entitas_id)
                                        <div style="font-size: 0.6875rem; color: var(--neutral-500); font-family: monospace;">
                                            ID: #{{ $log->entitas_id }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 0.875rem 1rem; font-family: monospace; color: var(--neutral-700); white-space: nowrap; font-size: 0.75rem;">
                                    {{ $log->ip ?? '-' }}
                                </td>
                                <td style="padding: 0.875rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <button type="button" onclick="openModalLogDetail({{ json_encode([
                                        'id' => $log->id,
                                        'waktu' => $log->waktu?->format('Y-m-d H:i:s') ?? '-',
                                        'user' => $log->user ? ($log->user->nama ?? $log->user->name) : 'Sistem / Anonim',
                                        'entitas' => $log->entitas ?? '-',
                                        'entitas_id' => $log->entitas_id,
                                        'ip' => $log->ip ?? '-',
                                        'request_id' => $log->request_id,
                                        'metode' => $log->metode,
                                        'rute' => $log->rute,
                                        'status_http' => $log->status_http,
                                        'durasi_ms' => $log->durasi_ms,
                                        'detail' => $log->detail,
                                        'nilai_lama' => $log->nilai_lama,
                                        'nilai_baru' => $log->nilai_baru,
                                    ]) }})" style="padding: 0.4rem 0.75rem; background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 6px; font-size: 0.75rem; font-weight: 700; color: var(--neutral-800); cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 0.25rem;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='var(--neutral-0)'">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                        Detail
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

<!-- =====================================================================
     MODAL DETAIL JSON AUDIT LOG (Sleman Inspector)
     ===================================================================== -->
<div id="modalLogDetail" onclick="if(event.target === this) closeModalLogDetail()" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 720px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; max-height: 88vh; display: flex; flex-direction: column; border: 1px solid var(--neutral-400);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line></svg>
                </div>
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        Inspeksi Detail Audit Trail #<span id="logId"></span>
                    </h3>
                    <span style="font-size: 0.6875rem; font-weight: 700; color: var(--blue-700); text-transform: uppercase;">
                        Integritas Log: Append-Only Immutable
                    </span>
                </div>
            </div>
            <button type="button" onclick="closeModalLogDetail()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-500); line-height: 1; padding: 0.25rem;">&times;</button>
        </div>

        <div style="overflow-y: auto; display: flex; flex-direction: column; gap: 1.25rem; padding-right: 0.25rem;">
            <!-- Grid Metadata -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.8125rem; background: var(--neutral-100); padding: 1rem 1.25rem; border-radius: 8px; border: 1px solid var(--neutral-300);">
                <div><span style="color: var(--neutral-600);">Pelaku:</span> <strong id="logUser" style="color: var(--neutral-900);">-</strong></div>
                <div><span style="color: var(--neutral-600);">Waktu:</span> <strong id="logWaktu" style="color: var(--neutral-900); font-family: monospace;">-</strong></div>
                <div><span style="color: var(--neutral-600);">Entitas / ID:</span> <strong id="logEntitas" style="color: var(--neutral-900);">-</strong></div>
                <div><span style="color: var(--neutral-600);">IP Address:</span> <strong id="logIp" style="color: var(--neutral-900); font-family: monospace;">-</strong></div>
                <div style="grid-column: span 2;"><span style="color: var(--neutral-600);">Request ID:</span> <span id="logRequestId" style="font-family: monospace; font-weight: 700; color: var(--neutral-900);">-</span></div>
                <div style="grid-column: span 2;"><span style="color: var(--neutral-600);">Konteks HTTP:</span> <span id="logHttp" style="font-family: monospace; color: var(--blue-700); font-weight: 700;">-</span></div>
            </div>

            <!-- Detail Payload -->
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase; margin-bottom: 0.375rem;">
                    Detail Peristiwa (Payload Event)
                </label>
                <pre id="logPayload" style="background: #0f172a; color: #f8fafc; padding: 1rem; border-radius: 8px; font-size: 0.75rem; overflow-x: auto; margin: 0; max-height: 180px; font-family: monospace; line-height: 1.45;"></pre>
            </div>

            <!-- Diff Perubahan -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--red-700); text-transform: uppercase; margin-bottom: 0.375rem;">
                        Nilai Lama (Sebelum Mutasi)
                    </label>
                    <pre id="logNilaiLama" style="background: var(--red-100); color: var(--red-700); border: 1px solid var(--red-300); padding: 0.875rem; border-radius: 8px; font-size: 0.75rem; overflow-x: auto; margin: 0; max-height: 180px; font-family: monospace; line-height: 1.4;"></pre>
                </div>

                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--green-700); text-transform: uppercase; margin-bottom: 0.375rem;">
                        Nilai Baru (Setelah Mutasi)
                    </label>
                    <pre id="logNilaiBaru" style="background: var(--green-100); color: var(--green-700); border: 1px solid rgba(22, 163, 74, 0.3); padding: 0.875rem; border-radius: 8px; font-size: 0.75rem; overflow-x: auto; margin: 0; max-height: 180px; font-family: monospace; line-height: 1.4;"></pre>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
            <button type="button" onclick="closeModalLogDetail()" style="height: 40px; padding: 0 1.5rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-800); cursor: pointer;">
                Tutup Inspeksi
            </button>
        </div>
    </div>
</div>

<script>
    function openModalLogDetail(data) {
        document.getElementById('logId').textContent = data.id;
        document.getElementById('logUser').textContent = data.user;
        document.getElementById('logWaktu').textContent = data.waktu;
        document.getElementById('logEntitas').textContent = (data.entitas || '-') + (data.entitas_id ? (' #' + data.entitas_id) : '');
        document.getElementById('logIp').textContent = data.ip || '-';
        document.getElementById('logRequestId').textContent = data.request_id || '-';
        document.getElementById('logHttp').textContent = (data.metode || '-') + ' ' + (data.rute || '-') + (data.status_http ? (' [' + data.status_http + ']') : '') + (data.durasi_ms ? (' (' + data.durasi_ms + 'ms)') : '');
        document.getElementById('logPayload').textContent = data.detail ? JSON.stringify(data.detail, null, 2) : '-';
        document.getElementById('logNilaiLama').textContent = data.nilai_lama ? JSON.stringify(data.nilai_lama, null, 2) : '-';
        document.getElementById('logNilaiBaru').textContent = data.nilai_baru ? JSON.stringify(data.nilai_baru, null, 2) : '-';
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
