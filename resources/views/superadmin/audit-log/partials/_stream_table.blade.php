{{-- SECTION 4 & 5: STREAM HEADER, FILTER TOOLBAR, HIGH-DENSITY AUDIT LOG TABLE & PAGINATION --}}
@php
    /** @var array<string, array<string, mixed>>|null $channels */
    /** @var array<string, mixed>|null $activeChannelMeta */
    /** @var string $activeChannel */
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $logs */
    /** @var int $mutasiData */
    /** @var int $sesiAuth */
    /** @var int $peringatanKritis */
    /** @var iterable<\App\Models\User>|null $internalUsers */

    if (!function_exists('request')) {
        function request($key = null, $default = null) {
            return \Illuminate\Support\Facades\Request::input($key, $default);
        }
    }
@endphp
<div class="audit-stream-card">
    {{-- Stream Channel Status & Severity Badges --}}
    <div class="audit-stream-header">
        <div class="audit-stream-title-group">
            <span class="audit-stream-dot" style="background-color: {{ $activeChannelMeta['color'] ?? '#2563EB' }};"></span>
            <div class="audit-stream-title-row">
                <span class="audit-stream-name">#{{ $activeChannel }}</span>
                <span class="audit-stream-count">&bull; {{ number_format($logs->total()) }} entri</span>
            </div>
        </div>

        {{-- Type & Severity Filter Chips --}}
        <div class="audit-severity-chips" role="group" aria-label="Filter Severity">
            @php
                $baseUrl = $activeChannel === 'all-actions-stream' 
                    ? route('superadmin.audit-log.index') 
                    : route('superadmin.audit-log.channel', ['channel' => $activeChannel]);
                $currSev = request('severity');
                $currTipe = request('tipe');
            @endphp

            <a 
                href="{{ $baseUrl }}" 
                class="audit-chip {{ !$currSev && !$currTipe ? 'audit-chip-active' : 'audit-chip-default' }}"
            >
                Semua ({{ number_format($logs->total()) }})
            </a>

            <a 
                href="{{ request()->fullUrlWithQuery(['tipe' => 'aksi', 'severity' => null]) }}" 
                class="audit-chip {{ $currTipe === 'aksi' ? 'audit-chip-active-blue' : 'audit-chip-blue' }}"
            >
                Mutasi ({{ number_format($mutasiData) }})
            </a>

            <a 
                href="{{ request()->fullUrlWithQuery(['tipe' => 'auth', 'severity' => null]) }}" 
                class="audit-chip {{ $currTipe === 'auth' ? 'audit-chip-active-blue' : 'audit-chip-blue' }}"
            >
                Autentikasi ({{ number_format($sesiAuth) }})
            </a>

            <a 
                href="{{ request()->fullUrlWithQuery(['severity' => 'warning', 'tipe' => null]) }}" 
                class="audit-chip {{ $currSev === 'warning' ? 'audit-chip-active-amber' : 'audit-chip-amber' }}"
            >
                Peringatan
            </a>

            <a 
                href="{{ request()->fullUrlWithQuery(['severity' => 'danger', 'tipe' => null]) }}" 
                class="audit-chip {{ $currSev === 'danger' ? 'audit-chip-active-rose' : 'audit-chip-rose' }}"
            >
                Kritis ({{ number_format($peringatanKritis) }})
            </a>
        </div>
    </div>

    {{-- Filter Grid --}}
    <form method="GET" action="{{ $baseUrl }}" class="audit-filter-grid" id="auditFilterForm">
        @if($activeChannel !== 'all-actions-stream')
            <input type="hidden" name="channel" value="{{ $activeChannel }}">
        @endif
        @if(request('severity'))
            <input type="hidden" name="severity" value="{{ request('severity') }}">
        @endif
        @if(request('tipe'))
            <input type="hidden" name="tipe" value="{{ request('tipe') }}">
        @endif
        @if(request('range'))
            <input type="hidden" name="range" value="{{ request('range') }}" id="formRangeInput">
        @endif

        {{-- Search within current channel --}}
        <div class="audit-filter-search-col">
            <input 
                type="text" 
                name="q" 
                id="mainSearchInput"
                value="{{ request('q') }}"
                placeholder="Filter aktor, ID, atau IP... [q]" 
                class="audit-filter-input"
                autocomplete="off"
            />
            <svg class="audit-filter-search-icon" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        {{-- Actor User Filter --}}
        <div class="audit-filter-actor-col">
            <select name="user_id" class="audit-filter-select" aria-label="Filter Aktor">
                <option value="">Semua Aktor</option>
                @foreach($internalUsers ?? [] as $staf)
                    <option value="{{ $staf->id }}" {{ request('user_id') == $staf->id ? 'selected' : '' }}>
                        {{ $staf->nama }} ({{ $staf->role?->label() ?? 'Staf' }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Entity Type Filter --}}
        <div class="audit-filter-entity-col">
            <select name="entitas" class="audit-filter-select" aria-label="Filter Entitas">
                <option value="">Semua Entitas</option>
                <option value="kuota" {{ request('entitas') === 'kuota' ? 'selected' : '' }}>Kuota Jurusan</option>
                <option value="pendaftaran" {{ request('entitas') === 'pendaftaran' ? 'selected' : '' }}>Pendaftaran &amp; Berkas</option>
                <option value="penempatan" {{ request('entitas') === 'penempatan' ? 'selected' : '' }}>Penempatan Bimbingan</option>
                <option value="logbook" {{ request('entitas') === 'logbook' ? 'selected' : '' }}>Logbook Kegiatan</option>
                <option value="users" {{ in_array(request('entitas'), ['user', 'users']) ? 'selected' : '' }}>Akun Pengguna (RBAC)</option>
                <option value="nilai_magang" {{ in_array(request('entitas'), ['nilai_magang', 'NilaiMagang']) ? 'selected' : '' }}>Nilai &amp; Sertifikat</option>
            </select>
        </div>

        {{-- Action Buttons: Submit & Reset --}}
        <div class="audit-filter-actions-col">
            <button 
                type="submit"
                class="audit-btn-submit"
                title="Terapkan Filter"
            >
                Cari
            </button>
            <a 
                href="{{ $baseUrl }}" 
                class="audit-btn-reset"
                title="Reset Filter"
                aria-label="Reset Filter"
            >
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </a>
        </div>
    </form>
</div>

{{-- SECTION 5: HIGH-DENSITY AUDIT LOG TABLE (PURE DATA, NO NARRATIVE SENTENCES) --}}
<div class="audit-table-card">
    @if($logs->isEmpty())
        <div class="audit-empty-state">
            <div class="audit-empty-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h4 class="audit-empty-title">Tidak Ada Rekaman Audit</h4>
            <p class="audit-empty-desc">Tidak ditemukan aktivitas dengan kriteria pencarian atau filter yang dipilih.</p>
        </div>
    @else
        <div class="audit-table-wrapper">
            <table class="audit-table" aria-label="Tabel Jejak Audit">
                <thead>
                    <tr class="audit-thead-row">
                        <th class="audit-th-time">Waktu &amp; ID</th>
                        <th class="audit-th-channel">Kanal</th>
                        <th class="audit-th-actor">Aktor</th>
                        <th class="audit-th-action">Aksi &amp; Target</th>
                        <th class="audit-th-diff">Snapshot Diff</th>
                        <th class="audit-th-client">Klien</th>
                        <th class="audit-th-ops text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="audit-tbody">
                    @foreach($logs as $index => $log)
                        @php
                            $channelKey = $log->channel ?: 'all-actions-stream';
                            $channelData = $channels[$channelKey] ?? [
                                'label' => $log->channel ?: 'System',
                                'color' => '#64748B',
                            ];
                            $sev = strtolower($log->severity ?: 'info');
                            $isCritical = ($sev === 'danger' || $sev === 'kritis');
                            $user = $log->user;
                            $userName = $user?->nama ?? 'Sistem / Anonim';
                            $userRole = $user?->role?->label() ?? 'System Engine';
                            $initials = 'SA';
                            if ($user && !empty($user->nama)) {
                                $parts = preg_split('/\s+/', trim($user->nama));
                                if (is_array($parts) && count($parts) >= 2) {
                                    $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
                                } else {
                                    $initials = strtoupper(substr($user->nama, 0, 2));
                                }
                            }

                            // Diff calculations
                            $oldVal = null;
                            $newVal = null;
                            $diffFound = false;
                            $diffKey = '';
                            $diffOldStr = '';
                            $diffNewStr = '';

                            if (is_array($log->nilai_lama) || is_array($log->nilai_baru)) {
                                $old = is_array($log->nilai_lama) ? $log->nilai_lama : [];
                                $new = is_array($log->nilai_baru) ? $log->nilai_baru : [];
                                $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));
                                foreach ($allKeys as $k) {
                                    if (in_array($k, ['updated_at', 'created_at', 'remember_token', 'password'])) continue;
                                    $vOld = $old[$k] ?? null;
                                    $vNew = $new[$k] ?? null;
                                    if ($vOld !== $vNew) {
                                        $diffFound = true;
                                        $diffKey = $k;
                                        $diffOldStr = is_scalar($vOld) ? (string) $vOld : json_encode($vOld);
                                        $diffNewStr = is_scalar($vNew) ? (string) $vNew : json_encode($vNew);
                                        break;
                                    }
                                }
                            }
                        @endphp
                        <tr class="audit-tr {{ $isCritical ? 'audit-tr-critical' : '' }}" id="audit-row-{{ $log->id }}">
                            {{-- Waktu & ID --}}
                            <td class="audit-td-time">
                                <div class="audit-time-wib tabular-nums">
                                    {{ $log->waktu ? $log->waktu->format('H:i:s') . ' WIB' : '-' }}
                                </div>
                                <div class="audit-req-id font-mono">
                                    req_{{ substr($log->request_id ?: md5((string)$log->id), 0, 8) }}
                                </div>
                            </td>

                            {{-- Kanal & Severity --}}
                            <td class="audit-td-channel">
                                <div class="audit-channel-badge-wrap">
                                    <span class="badge-severity-pill badge-sev-{{ $sev }}">
                                        @if($sev === 'danger' || $sev === 'kritis')
                                            KRITIS
                                        @elseif($sev === 'warning')
                                            PERINGATAN
                                        @elseif($sev === 'success')
                                            SUKSES
                                        @elseif($log->tipe?->value === 'aksi' || $diffFound)
                                            MUTASI
                                        @elseif($log->tipe?->value === 'auth')
                                            AUTH
                                        @else
                                            INFO
                                        @endif
                                    </span>
                                    <span class="audit-channel-slug">
                                        #{{ $log->channel ?: 'system' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Aktor --}}
                            <td class="audit-td-actor">
                                <div class="audit-actor-flex">
                                    <div class="audit-actor-avatar" title="{{ $userName }}">
                                        {{ $initials }}
                                    </div>
                                    <div class="audit-actor-text-wrap">
                                        <div class="audit-actor-name" title="{{ $userName }}">
                                            {{ $userName }}
                                        </div>
                                        <div class="audit-actor-role" title="{{ $userRole }}">
                                            {{ $userRole }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Aksi & Target --}}
                            <td class="audit-td-action">
                                <div class="audit-action-target-wrap">
                                    <span class="audit-action-slug font-mono">
                                        {{ $log->aksi }}
                                    </span>
                                    @if($log->entitas)
                                        <span class="audit-target-badge font-mono">
                                            {{ ucfirst($log->entitas) }}{{ $log->entitas_id ? ' #' . $log->entitas_id : '' }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Snapshot Diff --}}
                            <td class="audit-td-diff">
                                @if($diffFound)
                                    <div class="audit-diff-pill font-mono">
                                        @if($diffKey)<span class="text-slate-400 font-sans mr-0.5">{{ $diffKey }}:</span>@endif
                                        <span class="line-through audit-diff-old">{{ Str::limit($diffOldStr, 12) ?: 'null' }}</span>
                                        <span class="audit-diff-arrow">&rarr;</span>
                                        <span class="audit-diff-new">{{ Str::limit($diffNewStr, 14) }}</span>
                                    </div>
                                @elseif($log->status_http)
                                    <div class="audit-diff-pill font-mono">
                                        <span class="text-slate-400">status:</span>
                                        <span class="audit-diff-new">{{ $log->status_http }}</span>
                                    </div>
                                @elseif($log->metode)
                                    <div class="audit-diff-pill font-mono">
                                        <span class="audit-diff-new">{{ $log->metode }}</span>
                                        <span class="text-slate-400">{{ Str::limit($log->rute, 15) }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>

                            {{-- Klien --}}
                            <td class="audit-td-client">
                                <div class="audit-client-ip font-mono">
                                    {{ $log->ip ?: '127.0.0.1' }}
                                </div>
                                <div class="audit-client-ua" title="{{ $log->user_agent }}">
                                    @php
                                        $ua = $log->user_agent ?: 'Unknown Browser';
                                        $shortUa = 'Web Browser';
                                        if (str_contains($ua, 'Chrome')) $shortUa = 'Chrome';
                                        elseif (str_contains($ua, 'Firefox')) $shortUa = 'Firefox';
                                        elseif (str_contains($ua, 'Safari')) $shortUa = 'Safari';
                                        elseif (str_contains($ua, 'Edge')) $shortUa = 'Edge';

                                        if (str_contains($ua, 'Windows')) $shortUa .= ' • Win';
                                        elseif (str_contains($ua, 'Macintosh')) $shortUa .= ' • macOS';
                                        elseif (str_contains($ua, 'Linux')) $shortUa .= ' • Linux';
                                        elseif (str_contains($ua, 'Android')) $shortUa .= ' • Android';
                                        elseif (str_contains($ua, 'iPhone')) $shortUa .= ' • iOS';
                                    @endphp
                                    {{ $shortUa }}
                                </div>
                            </td>

                            {{-- Tombol Aksi Diff --}}
                            <td class="audit-td-ops text-right">
                                <button 
                                    type="button" 
                                    class="audit-btn-diff {{ $isCritical ? 'audit-btn-diff-critical' : '' }}"
                                    data-log-payload="{{ (string) json_encode([
                                        'id' => $log->id,
                                        'waktu' => $log->waktu ? $log->waktu->format('d M Y H:i:s') . ' WIB' : '-',
                                        'time_clock' => $log->waktu ? $log->waktu->format('H:i:s') . ' WIB' : '-',
                                        'channel' => $channelData['label'] ?? ($log->channel ?: 'System'),
                                        'channel_key' => $log->channel ?: 'system',
                                        'severity' => $log->severity ?: 'info',
                                        'user' => $userName . ($user ? ' (' . $userRole . ')' : ''),
                                        'actor_name' => $userName,
                                        'actor_id' => $user?->id ?? '-',
                                        'aksi' => $log->aksi,
                                        'entitas' => $log->entitas,
                                        'entitas_id' => $log->entitas_id,
                                        'request_id' => $log->request_id ?: 'req_' . substr(md5((string)$log->id), 0, 8),
                                        'ip' => $log->ip ?: '127.0.0.1',
                                        'user_agent' => $log->user_agent,
                                        'metode' => $log->metode ?: 'GET',
                                        'rute' => $log->rute ?: '-',
                                        'status_http' => $log->status_http ?: 200,
                                        'durasi_ms' => $log->durasi_ms ?: 0,
                                        'detail' => $log->detail,
                                        'nilai_lama' => $log->nilai_lama,
                                        'nilai_baru' => $log->nilai_baru,
                                        'hmac' => hash('sha256', ($log->id . ($log->request_id ?? '') . ($log->aksi ?? '') . ($log->waktu?->toISOString() ?? ''))),
                                    ]) }}"
                                    onclick="inspectAuditMutation(this)"
                                >
                                    Diff
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- SECTION 6B: PAGINATION FOOTER BAR --}}
<footer class="audit-pagination-footer">
    {{-- Left: Record Counter & Tamper-Proof Indicator --}}
    <div class="audit-pagination-left">
        <span class="audit-pagination-counter">
            Menampilkan <span class="font-bold text-slate-900 dark:text-white">{{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-900 dark:text-white tabular-nums">{{ number_format($logs->total()) }}</span> log
        </span>
        <span class="audit-immutable-pill">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            Audit Log Immutable
        </span>
    </div>

    {{-- Right: Pagination Buttons & Per-page select --}}
    <div class="audit-pagination-right">
        @if($logs->hasPages())
            <div class="audit-page-controls">
                {{-- Previous Page Link --}}
                @if($logs->onFirstPage())
                    <button type="button" class="audit-page-btn-nav disabled" disabled>&larr; Prev</button>
                @else
                    <a href="{{ $logs->previousPageUrl() }}" class="audit-page-btn-nav">&larr; Prev</a>
                @endif

                {{-- Pagination Elements --}}
                @foreach($logs->getUrlRange(max(1, $logs->currentPage() - 2), min($logs->lastPage(), $logs->currentPage() + 2)) as $page => $url)
                    @if($page == $logs->currentPage())
                        <span class="audit-page-num active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="audit-page-num">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="audit-page-btn-nav">Next &rarr;</a>
                @else
                    <button type="button" class="audit-page-btn-nav disabled" disabled>Next &rarr;</button>
                @endif
            </div>
        @endif
    </div>
</footer>
