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
            <div style="display: flex; flex-direction: column; gap: 0.25rem; min-width: 160px;">
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

            <div style="display: flex; flex-direction: column; gap: 0.25rem; min-width: 140px;">
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Entitas / Tabel</label>
                <input type="text" name="entitas" value="{{ request('entitas') }}" placeholder="Contoh: pendaftaran" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.625rem; font-size: 0.8125rem;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Dari Tanggal</label>
                <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" style="height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.8125rem;">
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 38px; padding: 0 1.25rem; background: var(--neutral-800); color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; border: none; border-radius: 6px; cursor: pointer;">
                    Filter
                </button>
                @if(request()->hasAny(['tipe', 'entitas', 'dari_tanggal', 'sampai_tanggal', 'request_id', 'user_id']))
                    <a href="{{ route('superadmin.audit-log.index') }}" style="height: 38px; padding: 0 0.75rem; display: inline-flex; align-items: center; font-size: 0.8125rem; color: var(--neutral-600); text-decoration: none;">
                        Reset
                    </a>
                @endif
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
                                    {{ $log->waktu ? $log->waktu->format('Y-m-d H:i:s') : $log->created_at->format('Y-m-d H:i:s') }}
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
                                        {{ $log->entitas ?? $log->tabel ?? '-' }}
                                    </div>
                                    @if($log->id_data)
                                        <div style="font-size: 0.75rem; color: var(--neutral-600); font-family: monospace;">ID: #{{ $log->id_data }}</div>
                                    @endif
                                </td>
                                <td style="padding: 0.875rem 1rem; font-family: monospace; color: var(--neutral-700); white-space: nowrap;">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                                <td style="padding: 0.875rem 1rem; text-align: right; white-space: nowrap;">
                                    <button onclick="openModalLogDetail({{ json_encode([
                                        'id' => $log->id,
                                        'waktu' => $log->waktu ? $log->waktu->format('Y-m-d H:i:s') : $log->created_at->format('Y-m-d H:i:s'),
                                        'user' => $log->user ? ($log->user->nama ?? $log->user->name) : 'Sistem / Anonim',
                                        'entitas' => $log->entitas ?? $log->tabel,
                                        'id_data' => $log->id_data,
                                        'ip_address' => $log->ip_address,
                                        'request_id' => $log->request_id,
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
<div id="modalLogDetail" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 640px; padding: 2rem; box-shadow: var(--shadow-card); position: relative; max-height: 85vh; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Inspeksi Detail Audit Trail #<span id="logId"></span></h3>
            <button onclick="document.getElementById('modalLogDetail').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--neutral-600);">&times;</button>
        </div>

        <div style="overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; padding-right: 0.5rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.8125rem; background: var(--neutral-50); padding: 0.875rem; border-radius: 8px; border: 1px solid var(--neutral-200);">
                <div><strong>Pelaku:</strong> <span id="logUser">-</span></div>
                <div><strong>Waktu:</strong> <span id="logWaktu">-</span></div>
                <div><strong>Entitas / ID:</strong> <span id="logEntitas">-</span></div>
                <div><strong>IP Address:</strong> <span id="logIp">-</span></div>
                <div style="grid-column: span 2;"><strong>Request ID:</strong> <span id="logRequestId" style="font-family: monospace;">-</span></div>
            </div>

            <div>
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Detail Peristiwa (Payload)</label>
                <pre id="logPayload" style="background: var(--neutral-900); color: var(--neutral-100); padding: 0.875rem; border-radius: 8px; font-size: 0.75rem; overflow-x: auto; margin-top: 0.25rem; max-height: 180px;"></pre>
            </div>

            <div>
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Nilai Lama (Sebelum Mutasi)</label>
                <pre id="logNilaiLama" style="background: var(--neutral-100); color: var(--neutral-800); border: 1px solid var(--neutral-300); padding: 0.875rem; border-radius: 8px; font-size: 0.75rem; overflow-x: auto; margin-top: 0.25rem; max-height: 140px;"></pre>
            </div>

            <div>
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); text-transform: uppercase;">Nilai Baru (Setelah Mutasi)</label>
                <pre id="logNilaiBaru" style="background: var(--neutral-100); color: var(--neutral-800); border: 1px solid var(--neutral-300); padding: 0.875rem; border-radius: 8px; font-size: 0.75rem; overflow-x: auto; margin-top: 0.25rem; max-height: 140px;"></pre>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem; border-top: 1px solid var(--neutral-300); padding-top: 0.75rem;">
            <button onclick="document.getElementById('modalLogDetail').style.display='none'" style="height: 38px; padding: 0 1.25rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
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
        document.getElementById('logEntitas').textContent = (data.entitas || '-') + ' #' + (data.id_data || '-');
        document.getElementById('logIp').textContent = data.ip_address || '-';
        document.getElementById('logRequestId').textContent = data.request_id || '-';
        document.getElementById('logPayload').textContent = JSON.stringify(data.detail, null, 2);
        document.getElementById('logNilaiLama').textContent = JSON.stringify(data.nilai_lama, null, 2);
        document.getElementById('logNilaiBaru').textContent = JSON.stringify(data.nilai_baru, null, 2);
        document.getElementById('modalLogDetail').style.display = 'flex';
    }
</script>
@endsection
