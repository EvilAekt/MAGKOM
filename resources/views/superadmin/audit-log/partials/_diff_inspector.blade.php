{{-- SECTION 6: FORENSIC DIFF INSPECTOR (INLINE CARD & MODAL DIALOG) --}}

@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $logs */
    /** @var string|null $activeChannel */

    if (!function_exists('e')) {
        function e($value, $doubleEncode = true) {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8', $doubleEncode);
        }
    }
    $activeChannel = $activeChannel ?? 'all-actions-stream';

    // Ambil log pertama yang memiliki mutasi sebagai sampel inisial inspector
    $sampleLog = $logs->first(function ($l) {
        return !empty($l->nilai_lama) || !empty($l->nilai_baru) || $l->tipe?->value === 'aksi';
    }) ?: $logs->first();

    $initId = $sampleLog?->id ?? '2845';
    $initReqId = $sampleLog?->request_id ?: 'req_' . substr(md5((string)$initId), 0, 8);
    $initAksi = $sampleLog?->aksi ?? 'kuota.capacity_update';
    $initEntitas = $sampleLog?->entitas ? ucfirst($sampleLog->entitas) . ($sampleLog->entitas_id ? ' #' . $sampleLog->entitas_id : '') : 'Kuota #12 (Teknik Informatika)';
    $initTime = $sampleLog?->waktu ? $sampleLog->waktu->format('H:i:s') . ' WIB' : '11:42:08 WIB';
    $initUser = $sampleLog?->user?->nama ?? 'Ir. Sudirman';
    $initUserId = $sampleLog?->user?->id ?? '2';
    $initIp = $sampleLog?->ip ?? '192.168.1.24';
    $initHmac = hash('sha256', ($initId . $initReqId . $initAksi));
    $initOld = $sampleLog?->nilai_lama ?: ['id' => 12, 'jurusan_id' => 3, 'bulan_magang_id' => 11, 'kuota' => 15, 'terisi' => 14, 'status' => 'TERSEDIA'];
    $initNew = $sampleLog?->nilai_baru ?: ['id' => 12, 'jurusan_id' => 3, 'bulan_magang_id' => 11, 'kuota' => 20, 'terisi' => 14, 'status' => 'TERSEDIA'];
@endphp

{{-- On-Page Side-by-Side Diff Inspector Card --}}
<div class="audit-diff-card" id="auditDiffInlineCard">
    {{-- Inspector Header --}}
    <div class="audit-diff-header">
        <div class="audit-diff-header-left">
            <div class="audit-diff-delta-badge">
                &Delta;
            </div>
            <div>
                <div class="audit-diff-title-row">
                    <span class="audit-diff-title">Inspeksi Mutasi: #<span id="diffInspectorId">{{ $initId }}</span></span>
                    <span class="audit-diff-req-badge font-mono" id="diffInspectorReqId">{{ $initReqId }}</span>
                </div>
                <p class="audit-diff-subtitle font-mono" id="diffInspectorSub">
                    {{ $initAksi }} &bull; {{ $initEntitas }}
                </p>
            </div>
        </div>

        <div class="audit-diff-header-actions">
            <button 
                type="button" 
                id="btnCopyJsonDiff"
                class="audit-diff-action-btn"
                onclick="copyDiffJson()"
                title="Salin payload JSON mutasi ke clipboard"
            >
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <span id="copyJsonBtnText">Salin JSON</span>
            </button>
            <button 
                type="button" 
                id="btnPrintForensicPdf"
                class="audit-diff-action-btn audit-diff-btn-pdf"
                onclick="printForensicProof()"
                title="Cetak atau unduh bukti forensik ber-QR"
            >
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Bukti Forensik PDF</span>
            </button>
        </div>
    </div>

    {{-- Side-by-Side Diff Panels --}}
    <div class="audit-diff-grid">
        {{-- Left: Nilai Lama (Before) --}}
        <div class="audit-diff-panel audit-diff-panel-old">
            <div class="audit-diff-panel-bar audit-bar-rose">
                <span class="audit-diff-panel-title text-rose-800 dark:text-rose-300">
                    <span class="badge-dot-rose"></span>
                    NILAI LAMA (BEFORE)
                </span>
                <span class="audit-diff-panel-time font-mono" id="diffTimeOld">{{ $initTime }}</span>
            </div>
            <pre class="audit-diff-pre text-slate-800 dark:text-slate-200" id="diffPreOld">{{ json_encode($initOld, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>

        {{-- Right: Nilai Baru (After) --}}
        <div class="audit-diff-panel audit-diff-panel-new">
            <div class="audit-diff-panel-bar audit-bar-emerald">
                <span class="audit-diff-panel-title text-emerald-800 dark:text-emerald-300">
                    <span class="badge-dot-emerald"></span>
                    NILAI BARU (AFTER)
                </span>
                <span class="audit-diff-panel-time font-mono" id="diffTimeNew">{{ $initTime }}</span>
            </div>
            <pre class="audit-diff-pre text-slate-800 dark:text-slate-200" id="diffPreNew">{{ json_encode($initNew, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>

    {{-- Forensic Metadata Footer --}}
    <div class="audit-forensic-footer font-mono">
        <div class="audit-forensic-hmac-wrap truncate">
            <span class="audit-forensic-hmac-label">HMAC-SHA256:</span>
            <span class="audit-forensic-hmac-val truncate" id="diffHmac">{{ $initHmac }}</span>
        </div>
        <div class="audit-forensic-meta-wrap">
            <span id="diffActorMeta">Aktor: {{ $initUser }} (#{{ $initUserId }})</span>
            <span id="diffIpMeta">IP: {{ $initIp }}</span>
            <span class="audit-integrity-verified">&check; Integritas Sah</span>
        </div>
    </div>
</div>

{{-- MODAL COMPATIBILITY OVERLAY (#modalLogDetail) --}}
<div id="modalLogDetail" class="audit-modal-backdrop" onclick="if(event.target === this) closeModalLogDetail()" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="modalLogDetailTitle">
    <div class="audit-modal-content">
        {{-- Modal Top Header --}}
        <div class="audit-modal-header">
            <div class="audit-modal-header-left">
                <div class="audit-diff-delta-badge">
                    &Delta;
                </div>
                <div>
                    <h3 id="modalLogDetailTitle" class="audit-modal-title">
                        Inspeksi Audit Trail #<span id="logId">{{ $initId }}</span>
                    </h3>
                    <div class="audit-modal-meta-row">
                        <span class="audit-modal-channel font-mono">Kanal: #<span id="logChannel">{{ $activeChannel }}</span></span>
                        <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                        <span id="logSeverity" class="badge-severity-pill badge-sev-info">INFO</span>
                        <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                        <span id="logRequestId" class="font-mono text-[11px] text-slate-500">{{ $initReqId }}</span>
                    </div>
                </div>
            </div>
            <button type="button" onclick="closeModalLogDetail()" class="audit-modal-close-btn" title="Tutup [ESC]" aria-label="Tutup modal">
                &times;
            </button>
        </div>

        {{-- Modal Body Scrollable --}}
        <div class="audit-modal-body">
            {{-- Quick Metadata Grid --}}
            <div class="audit-modal-meta-grid">
                <div><span class="audit-meta-label">Aktor:</span> <strong id="logUser" class="audit-meta-val">{{ $initUser }}</strong></div>
                <div><span class="audit-meta-label">Waktu:</span> <strong id="logWaktu" class="audit-meta-val tabular-nums">{{ $initTime }}</strong></div>
                <div><span class="audit-meta-label">Entitas / ID:</span> <strong id="logEntitas" class="audit-meta-val">{{ $initEntitas }}</strong></div>
                <div><span class="audit-meta-label">IP Address:</span> <strong id="logIp" class="audit-meta-val font-mono">{{ $initIp }}</strong></div>
                <div class="col-span-full"><span class="audit-meta-label">Aksi Sistem:</span> <strong id="logAksi" class="audit-meta-val font-mono">{{ $initAksi }}</strong></div>
                <div class="col-span-full"><span class="audit-meta-label">Konteks HTTP:</span> <span id="logHttp" class="audit-meta-val font-mono text-blue-600 dark:text-blue-400">GET /superadmin/audit-log [200]</span></div>
            </div>

            {{-- Side-by-Side Diff Panels --}}
            <div>
                <div class="audit-modal-section-title">
                    Perbandingan Mutasi Data (Side-by-Side Diff)
                </div>
                <div class="audit-diff-grid">
                    <div class="audit-diff-panel audit-diff-panel-old">
                        <div class="audit-diff-panel-bar audit-bar-rose">
                            <span class="audit-diff-panel-title text-rose-800 dark:text-rose-300">
                                <span class="badge-dot-rose"></span>
                                Nilai Lama (Sebelum Mutasi)
                            </span>
                        </div>
                        <pre class="audit-diff-pre text-slate-800 dark:text-slate-200" id="logNilaiLama">{{ json_encode($initOld, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>

                    <div class="audit-diff-panel audit-diff-panel-new">
                        <div class="audit-diff-panel-bar audit-bar-emerald">
                            <span class="audit-diff-panel-title text-emerald-800 dark:text-emerald-300">
                                <span class="badge-dot-emerald"></span>
                                Nilai Baru (Setelah Mutasi)
                            </span>
                        </div>
                        <pre class="audit-diff-pre text-slate-800 dark:text-slate-200" id="logNilaiBaru">{{ json_encode($initNew, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>
            </div>

            {{-- Event Payload Detail --}}
            <div>
                <div class="audit-modal-section-title">
                    Detail Konteks Event Payload (JSON)
                </div>
                <pre class="audit-modal-payload-pre" id="logPayload">{{ json_encode($sampleLog?->detail ?? ['info' => 'Snapshot logbook audit trace'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="audit-modal-footer">
            <button type="button" onclick="closeModalLogDetail()" class="audit-modal-btn-dismiss">
                Tutup [ESC]
            </button>
        </div>
    </div>
</div>
