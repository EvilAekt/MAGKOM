@extends('layouts.dashboard')

@section('title', 'Jejak Audit Trail - SuperAdmin MagKom')
@section('page_title', 'Jejak Audit Sistem (Audit Trail)')
@section('page_description', 'Log pencatatan aktivitas, mutasi data, dan keamanan portal yang bersifat strictly read-only (AC-13-6).')

@section('breadcrumbs')
    <span class="breadcrumb-current">Audit Log</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Filter Toolbar Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <form method="GET" action="{{ route('superadmin.audit-log.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="display: flex; flex-direction: column; gap: 0.25rem; min-width: 220px; flex: 1;">
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Kata Kunci / Pencarian</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari aksi, user, entitas, IP, request ID..." style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.25rem; min-width: 150px;">
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Tipe Log</label>
                <select name="tipe" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: var(--neutral-0);">
                    <option value="">Semua Tipe</option>
                    @foreach($tipeEnum as $tipe)
                        <option value="{{ $tipe->value }}" {{ request('tipe') === $tipe->value ? 'selected' : '' }}>
                            {{ ucfirst($tipe->value) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.25rem; min-width: 170px;">
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Aktor / Staf</label>
                <select name="user_id" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem; background: var(--neutral-0);">
                    <option value="">Semua Staf & Akun</option>
                    @foreach($internalUsers ?? [] as $staf)
                        <option value="{{ $staf->id }}" {{ request('user_id') == $staf->id ? 'selected' : '' }}>
                            {{ $staf->nama }} ({{ $staf->role->label() }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.25rem; min-width: 130px;">
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Entitas / Tabel</label>
                <input type="text" name="entitas" value="{{ request('entitas') }}" placeholder="pendaftaran..." style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.25rem; min-width: 140px;">
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Request ID</label>
                <input type="text" name="request_id" value="{{ request('request_id') }}" placeholder="req-..." style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem; font-family: monospace;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Dari Tanggal</label>
                <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem;">
            </div>

            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <button type="submit" style="height: 38px; padding: 0 1.25rem; background: var(--neutral-800); color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; border: none; border-radius: 6px; cursor: pointer;">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'tipe', 'entitas', 'dari_tanggal', 'sampai_tanggal', 'request_id', 'user_id']))
                    <a href="{{ route('superadmin.audit-log.index') }}" style="height: 38px; padding: 0 0.75rem; display: inline-flex; align-items: center; font-size: 0.8125rem; color: var(--neutral-600); text-decoration: none;">
                        Reset
                    </a>
                @endif
                <a href="{{ route('superadmin.audit-log.export', request()->query()) }}" style="height: 38px; padding: 0 1rem; background: var(--green-700); color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.375rem; white-space: nowrap;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Ekspor CSV
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        @if($logs->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <p style="font-size: 1rem; margin: 0;">Tidak ada rekaman jejak audit yang sesuai dengan filter.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 0.875rem 1rem; font-weight: 700; width: 140px;">Waktu & Tanggal</th>
                            <th style="padding: 0.875rem 1rem; font-weight: 700;">Pelaku / User</th>
                            <th style="padding: 0.875rem 1rem; font-weight: 700; width: 110px;">Tipe Log</th>
                            <th style="padding: 0.875rem 1rem; font-weight: 700;">Entitas & ID</th>
                            <th style="padding: 0.875rem 1rem; font-weight: 700; width: 120px;">Alamat IP</th>
                            <th style="padding: 0.875rem 1rem; font-weight: 700; text-align: right; width: 100px;">Inspeksi</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid var(--neutral-300);">
                        @foreach($logs as $log)
                            @php
                                $tipeBadgeBg = match($log->tipe->value ?? (string)$log->tipe) {
                                    'auth' => 'var(--purple-50, #F3E8FF)',
                                    'aksi' => 'var(--blue-50)',
                                    'service' => 'var(--yellow-50)',
                                    'akses_data' => 'var(--green-50)',
                                    default => 'var(--neutral-200)'
                                };
                                $tipeBadgeText = match($log->tipe->value ?? (string)$log->tipe) {
                                    'auth' => 'var(--purple-800, #6B21A8)',
                                    'aksi' => 'var(--blue-700)',
                                    'service' => 'var(--yellow-800)',
                                    'akses_data' => 'var(--green-800)',
                                    default => 'var(--neutral-700)'
                                };
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-300); transition: background 0.15s;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 0.875rem 1rem; white-space: nowrap; font-family: monospace;">
                                    {{ $log->waktu?->format('Y-m-d H:i:s') ?? '-' }}
                                </td>
                                <td style="padding: 0.875rem 1rem;">
                                    @if($log->user)
                                        <div style="font-weight: 700; color: var(--neutral-900);">{{ $log->user->nama ?? $log->user->name }}</div>
                                        <div style="font-size: 0.75rem; color: var(--neutral-600);">{{ $log->user->role?->label() ?? $log->user->role }}</div>
                                    @else
                                        <span style="color: var(--neutral-500); font-style: italic;">Sistem / Tamu</span>
                                    @endif
                                </td>
                                <td style="padding: 0.875rem 1rem; white-space: nowrap;">
                                    <span style="display: inline-block; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: {{ $tipeBadgeBg }}; color: {{ $tipeBadgeText }};">
                                        {{ ucfirst($log->tipe->value ?? (string)$log->tipe) }}
                                    </span>
                                </td>
                                <td style="padding: 0.875rem 1rem;">
                                    <div style="font-weight: 600; color: var(--neutral-800);">
                                        {{ $log->entitas ?? '-' }}
                                    </div>
                                    @if($log->aksi)
                                        <div style="font-size: 0.75rem; color: var(--blue-700); font-family: monospace;">{{ $log->aksi }}</div>
                                    @endif
                                    @if($log->entitas_id)
                                        <div style="font-size: 0.75rem; color: var(--neutral-600); font-family: monospace;">ID: #{{ $log->entitas_id }}</div>
                                    @endif
                                </td>
                                <td style="padding: 0.875rem 1rem; font-family: monospace; color: var(--neutral-700); white-space: nowrap;">
                                    {{ $log->ip ?? '-' }}
                                </td>
                                <td style="padding: 0.875rem 1rem; text-align: right; white-space: nowrap;">
                                    <button onclick="openModalLogDetail({{ json_encode([
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
                                    ]) }})" style="padding: 0.25rem 0.625rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 4px; font-size: 0.75rem; font-weight: 600; color: var(--neutral-800); cursor: pointer;">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--neutral-300);">
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Modal Detail JSON Audit Log -->
<div id="modalLogDetail" onclick="if(event.target === this) closeModalLogDetail()" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 680px; padding: 1.75rem 2rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); position: relative; max-height: 85vh; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Inspeksi Detail Audit Trail #<span id="logId"></span></h3>
                <span class="role-chip-badge" style="background: var(--blue-tint); color: var(--blue-700); font-size: 0.6875rem;">Append-Only</span>
            </div>
            <button onclick="closeModalLogDetail()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-600);">&times;</button>
        </div>

        <div style="overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; padding-right: 0.5rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.8125rem; background: var(--neutral-100); padding: 0.875rem 1rem; border-radius: 8px; border: 1px solid var(--neutral-300);">
                <div><strong>Pelaku:</strong> <span id="logUser">-</span></div>
                <div><strong>Waktu:</strong> <span id="logWaktu">-</span></div>
                <div><strong>Entitas / ID:</strong> <span id="logEntitas">-</span></div>
                <div><strong>IP Address:</strong> <span id="logIp">-</span></div>
                <div style="grid-column: span 2;"><strong>Request ID:</strong> <span id="logRequestId" style="font-family: monospace;">-</span></div>
                <div style="grid-column: span 2;"><strong>Konteks HTTP:</strong> <span id="logHttp" style="font-family: monospace; color: var(--blue-700);">-</span></div>
            </div>

            <div>
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Detail Peristiwa (Payload Event)</label>
                <pre id="logPayload" style="background: #1e293b; color: #f8fafc; padding: 0.875rem; border-radius: 8px; font-size: 0.75rem; overflow-x: auto; margin-top: 0.25rem; max-height: 160px; font-family: monospace;"></pre>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--red-800); text-transform: uppercase;">Nilai Lama (Sebelum Mutasi)</label>
                    <pre id="logNilaiLama" style="background: #fff5f5; color: #7f1d1d; border: 1px solid #fecaca; padding: 0.75rem; border-radius: 8px; font-size: 0.75rem; overflow-x: auto; margin-top: 0.25rem; max-height: 160px; font-family: monospace;"></pre>
                </div>

                <div>
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--green-800); text-transform: uppercase;">Nilai Baru (Setelah Mutasi)</label>
                    <pre id="logNilaiBaru" style="background: #f0fdf4; color: #14532d; border: 1px solid #bbf7d0; padding: 0.75rem; border-radius: 8px; font-size: 0.75rem; overflow-x: auto; margin-top: 0.25rem; max-height: 160px; font-family: monospace;"></pre>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem; border-top: 1px solid var(--neutral-300); padding-top: 0.75rem;">
            <button onclick="closeModalLogDetail()" style="height: 38px; padding: 0 1.25rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                Tutup
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

