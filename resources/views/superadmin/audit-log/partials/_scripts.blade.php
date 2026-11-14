@php
    /** @var string $activeChannel */
@endphp
<script>
/**
 * DS-MagKom Audit Log Interaction Engine
 * Clean Vanilla JavaScript (Zero Dependencies, Fully Accessible)
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. POPOVER EXPORT CONTROLS (Gaya Laporan Peserta Pembimbing)
    const exportDropdownWrap = document.getElementById('exportDropdownWrap');
    const toggleExportBtn = document.getElementById('btnToggleExportPopover');
    const exportPopover = document.getElementById('exportPopover');
    const exportCaret = document.getElementById('exportSplitCaret');
    const quickExportBtn = document.getElementById('btnQuickExport');

    if (toggleExportBtn) {
        toggleExportBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleExportPopover();
        });
    }

    if (exportCaret) {
        exportCaret.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleExportPopover();
        });
    }

    if (quickExportBtn) {
        quickExportBtn.addEventListener('click', function () {
            triggerAuditExport('csv');
        });
    }

    // Dismiss Popover on click outside
    document.addEventListener('click', function (e) {
        if (exportPopover && exportPopover.style.display !== 'none') {
            const clickedInside = exportPopover.contains(e.target);
            const clickedToggle = (toggleExportBtn && toggleExportBtn.contains(e.target)) || (exportCaret && exportCaret.contains(e.target));
            if (!clickedInside && !clickedToggle) {
                toggleExportPopover(false);
            }
        }
    });

    // Dismiss Popover on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (exportPopover && exportPopover.style.display !== 'none') {
                toggleExportPopover(false);
            }
            closeModalLogDetail();
        }
    });

    // 2. SHORTCUT [Ctrl+K] UNTUK PENCARIAN CEPAT
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            const searchInput = document.getElementById('headerSearchInput') || document.getElementById('mainSearchInput');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });

    // Header compact search input Enter key submit
    const headerSearch = document.getElementById('headerSearchInput');
    if (headerSearch) {
        headerSearch.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const qVal = this.value.trim();
                const url = new URL(window.location.href);
                if (qVal) {
                    url.searchParams.set('q', qVal);
                } else {
                    url.searchParams.delete('q');
                }
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }
        });
    }

    // Header Date Range Select Change
    const headerDateRange = document.getElementById('headerDateRange');
    if (headerDateRange) {
        headerDateRange.addEventListener('change', function () {
            const rangeVal = this.value;
            const url = new URL(window.location.href);
            if (rangeVal && rangeVal !== 'custom') {
                url.searchParams.set('range', rangeVal);
                url.searchParams.delete('dari_tanggal');
                url.searchParams.delete('sampai_tanggal');
            } else if (!rangeVal) {
                url.searchParams.delete('range');
                url.searchParams.delete('dari_tanggal');
                url.searchParams.delete('sampai_tanggal');
            }
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    }

    // 3. CHANNEL FILTER INPUT (SIDEBAR CLIENT-SIDE SEARCH)
    const channelFilterInput = document.getElementById('channelFilterInput');
    if (channelFilterInput) {
        channelFilterInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const links = document.querySelectorAll('.audit-channel-link');
            links.forEach(function (link) {
                const label = link.getAttribute('data-channel-label') || '';
                const key = link.getAttribute('data-channel-key') || '';
                if (label.includes(query) || key.includes(query)) {
                    link.style.display = 'flex';
                } else {
                    link.style.display = 'none';
                }
            });
        });
    }
});

/**
 * Kontrol Buka/Tutup Popover Ekspor
 */
function toggleExportPopover(forceOpen = null) {
    const exportPopover = document.getElementById('exportPopover');
    const toggleBtn = document.getElementById('btnToggleExportPopover');
    const dropdownWrap = document.getElementById('exportDropdownWrap');
    const exportCaret = document.getElementById('exportSplitCaret');
    const caretSvg = document.getElementById('caretSvg');
    if (!exportPopover) return;

    let shouldOpen;
    if (forceOpen === true) {
        shouldOpen = true;
    } else if (forceOpen === false) {
        shouldOpen = false;
    } else {
        shouldOpen = exportPopover.style.display === 'none' || exportPopover.style.display === '';
    }

    if (shouldOpen) {
        exportPopover.style.display = 'flex';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
        if (dropdownWrap) dropdownWrap.classList.add('is-open');
        if (exportCaret) {
            exportCaret.setAttribute('aria-expanded', 'true');
            exportCaret.classList.add('active');
        }
        if (caretSvg) caretSvg.style.transform = 'rotate(180deg)';
    } else {
        exportPopover.style.display = 'none';
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
        if (dropdownWrap) dropdownWrap.classList.remove('is-open');
        if (exportCaret) {
            exportCaret.setAttribute('aria-expanded', 'false');
            exportCaret.classList.remove('active');
        }
        if (caretSvg) caretSvg.style.transform = 'rotate(0deg)';
    }
}
window.toggleExportPopover = toggleExportPopover;

/**
 * Handle perubahan format ekspor radio card di popover
 */
function handleAuditFormatSelection(format) {
    const cards = document.querySelectorAll('.audit-format-card');
    cards.forEach(card => card.classList.remove('audit-format-selected'));

    const activeCard = document.getElementById('cardFormat' + format.charAt(0).toUpperCase() + format.slice(1));
    if (activeCard) {
        activeCard.classList.add('audit-format-selected');
    }

    const radio = document.getElementById('radioFormat' + format.charAt(0).toUpperCase() + format.slice(1));
    if (radio && !radio.checked) {
        radio.checked = true;
    }

    const btnText = document.getElementById('btnExportText');
    if (btnText) {
        if (format === 'csv') {
            btnText.textContent = 'Unduh CSV';
        } else if (format === 'ndjson') {
            btnText.textContent = 'Unduh NDJSON';
        } else if (format === 'pdf') {
            btnText.textContent = 'Unduh Dokumen (.pdf)';
        } else {
            btnText.textContent = 'Unduh Berkas';
        }
    }
}
window.handleAuditFormatSelection = handleAuditFormatSelection;
window.handleExportFormatChange = handleAuditFormatSelection;

/**
 * Memicu ekspor file audit sesuai format dan filter aktif
 */
function triggerAuditExport(forcedFormat = null) {
    let format = forcedFormat;
    if (!format) {
        const selectedRadio = document.querySelector('input[name="popover_format"]:checked');
        format = selectedRadio ? selectedRadio.value : 'csv';
    }

    const currentUrl = new URL(window.location.href);
    const exportUrl = new URL("{{ route('superadmin.audit-log.export') }}", window.location.origin);

    // Salin seluruh filter query string aktif
    currentUrl.searchParams.forEach((val, key) => {
        if (key !== 'page') {
            exportUrl.searchParams.set(key, val);
        }
    });

    @php /** @var string $activeChannel */ @endphp
    const activeChannel = "{{ $activeChannel ?? '' }}";
    if (activeChannel) {
        exportUrl.searchParams.set('channel', activeChannel);
    }

    exportUrl.searchParams.set('format', format);

    const toggleHmac = document.getElementById('toggleHmac');
    if (toggleHmac) {
        exportUrl.searchParams.set('include_hmac', toggleHmac.checked ? '1' : '0');
    }

    const toggleDiff = document.getElementById('toggleDiff');
    if (toggleDiff) {
        exportUrl.searchParams.set('include_diff', toggleDiff.checked ? '1' : '0');
    }

    // Jalankan download
    window.location.href = exportUrl.toString();
}

/**
 * Opsi Lengkap / Advanced Export Config
 */
function showAdvancedExportOptions() {
    triggerAuditExport();
}

/**
 * Live Inspection Data Payload & On-Demand Async Cache (PERF-P0-02)
 */
let currentInspectorPayload = null;
const auditDetailCache = new Map();
const pendingAuditFetches = new Set();

function inspectAuditMutation(btn) {
    if (!btn) return;

    // 1. Dukungan fallback jika data-log-payload lama masih ada (misal transition cache)
    const rawPayload = btn.getAttribute('data-log-payload');
    if (rawPayload) {
        try {
            const parsed = JSON.parse(rawPayload);
            renderAuditInspection(parsed);
            return;
        } catch (e) {
            console.error('Failed to parse legacy audit payload', e);
        }
    }

    const logId = btn.getAttribute('data-log-id');
    if (!logId) return;

    // 2. Cek apakah sudah ada dalam memory cache JavaScript
    if (auditDetailCache.has(logId)) {
        renderAuditInspection(auditDetailCache.get(logId));
        return;
    }

    // 3. Cegah request duplikat simultan untuk ID yang sama
    if (pendingAuditFetches.has(logId)) {
        return;
    }

    // 4. Set state loading pada UI (Inline Card & Modal)
    setAuditLoadingState(logId, btn);
    pendingAuditFetches.add(logId);

    // 5. Fetch detail secara asinkron dari endpoint terotorisasi
    const url = "{{ url('/superadmin/audit-log') }}/" + encodeURIComponent(logId);
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async (response) => {
        if (!response.ok) {
            const errData = await response.json().catch(() => ({}));
            throw {
                status: response.status,
                message: errData.message || ('HTTP ' + response.status)
            };
        }
        return response.json();
    })
    .then((json) => {
        if (json.status === 'success' && json.data) {
            auditDetailCache.set(logId, json.data);
            renderAuditInspection(json.data);
        } else {
            throw { status: 500, message: 'Format data tidak valid.' };
        }
    })
    .catch((err) => {
        console.error('Failed to fetch audit log detail:', err);
        setAuditErrorState(logId, err, btn);
    })
    .finally(() => {
        pendingAuditFetches.delete(logId);
        resetButtonLoadingState(btn);
    });
}

function setAuditLoadingState(logId, btn) {
    if (btn) {
        btn.dataset.originalText = btn.textContent;
        btn.textContent = 'Memuat...';
        btn.disabled = true;
        btn.style.opacity = '0.7';
    }

    // Update modal ke state loading
    const modal = document.getElementById('modalLogDetail');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    const idElem = document.getElementById('logId');
    if (idElem) idElem.textContent = logId + ' (Memuat data...)';

    const oldPre = document.getElementById('logNilaiLama');
    if (oldPre) oldPre.textContent = 'Sedang memuat data nilai lama dari server...';

    const newPre = document.getElementById('logNilaiBaru');
    if (newPre) newPre.textContent = 'Sedang memuat data nilai baru dari server...';

    const payloadPre = document.getElementById('logPayload');
    if (payloadPre) payloadPre.textContent = 'Sedang mengambil detail aktivitas...';

    // Update inline card jika ada
    const preOldElem = document.getElementById('diffPreOld');
    if (preOldElem) preOldElem.textContent = 'Memuat snapshot lama...';

    const preNewElem = document.getElementById('diffPreNew');
    if (preNewElem) preNewElem.textContent = 'Memuat snapshot baru...';
}

function resetButtonLoadingState(btn) {
    if (btn && btn.dataset.originalText) {
        btn.textContent = btn.dataset.originalText;
        btn.disabled = false;
        btn.style.opacity = '1';
    }
}

function setAuditErrorState(logId, err, btn) {
    let errorMsg = 'Gagal memuat jejak audit. Silakan coba lagi.';
    if (err.status === 403) {
        errorMsg = 'Akses ditolak: Anda tidak memiliki wewenang untuk memeriksa log ini.';
    } else if (err.status === 404) {
        errorMsg = 'Catatan jejak audit #' + logId + ' tidak ditemukan di sistem.';
    }

    const oldPre = document.getElementById('logNilaiLama');
    if (oldPre) oldPre.textContent = errorMsg;

    const newPre = document.getElementById('logNilaiBaru');
    if (newPre) newPre.textContent = errorMsg;

    const preOldElem = document.getElementById('diffPreOld');
    if (preOldElem) preOldElem.textContent = errorMsg;

    const preNewElem = document.getElementById('diffPreNew');
    if (preNewElem) preNewElem.textContent = errorMsg;

    const payloadPre = document.getElementById('logPayload');
    if (payloadPre) {
        payloadPre.textContent = 'Error: ' + (err.message || errorMsg);
    }
}

function renderAuditInspection(data) {
    if (!data) return;
    currentInspectorPayload = data;

    // 1. Update Inline Inspector Card Elements
    const idElem = document.getElementById('diffInspectorId');
    if (idElem) idElem.textContent = data.id;

    const reqIdElem = document.getElementById('diffInspectorReqId');
    if (reqIdElem) reqIdElem.textContent = data.request_id || '-';

    const subElem = document.getElementById('diffInspectorSub');
    if (subElem) {
        subElem.textContent = (data.aksi || '') + ' • ' + (data.entitas ? (data.entitas + (data.entitas_id ? (' #' + data.entitas_id) : '')) : 'Sistem');
    }

    const timeOldElem = document.getElementById('diffTimeOld');
    if (timeOldElem) timeOldElem.textContent = data.time_clock || data.waktu || '-';

    const timeNewElem = document.getElementById('diffTimeNew');
    if (timeNewElem) timeNewElem.textContent = data.time_clock || data.waktu || '-';

    const preOldElem = document.getElementById('diffPreOld');
    if (preOldElem) {
        preOldElem.textContent = data.nilai_lama ? (typeof data.nilai_lama === 'string' ? data.nilai_lama : JSON.stringify(data.nilai_lama, null, 2)) : '- (Tidak ada data lama)';
    }

    const preNewElem = document.getElementById('diffPreNew');
    if (preNewElem) {
        preNewElem.textContent = data.nilai_baru ? (typeof data.nilai_baru === 'string' ? data.nilai_baru : JSON.stringify(data.nilai_baru, null, 2)) : '- (Tidak ada data baru)';
    }

    const hmacElem = document.getElementById('diffHmac');
    if (hmacElem) hmacElem.textContent = data.hmac || 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855';

    const actorMetaElem = document.getElementById('diffActorMeta');
    if (actorMetaElem) actorMetaElem.textContent = 'Aktor: ' + (data.actor_name || '-') + (data.actor_id ? (' (#' + data.actor_id + ')') : '');

    const ipMetaElem = document.getElementById('diffIpMeta');
    if (ipMetaElem) ipMetaElem.textContent = 'IP: ' + (data.ip || '127.0.0.1');

    // Scroll smooth to inspector if on screen
    const inlineCard = document.getElementById('auditDiffInlineCard');
    if (inlineCard) {
        inlineCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Juga siapkan data di modal #modalLogDetail
    openModalLogDetail(data);
}

/**
 * Salin JSON diff payload ke clipboard
 */
function copyDiffJson() {
    let payloadToCopy = currentInspectorPayload;
    if (!payloadToCopy) {
        const oldPre = document.getElementById('diffPreOld');
        const newPre = document.getElementById('diffPreNew');
        payloadToCopy = {
            nilai_lama: oldPre ? oldPre.textContent : null,
            nilai_baru: newPre ? newPre.textContent : null,
        };
    }

    const jsonText = JSON.stringify(payloadToCopy, null, 2);
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(jsonText).then(() => {
            showCopyFeedback();
        }).catch(() => {
            fallbackCopy(jsonText);
        });
    } else {
        fallbackCopy(jsonText);
    }
}

function showCopyFeedback() {
    const btnText = document.getElementById('copyJsonBtnText');
    if (btnText) {
        const orig = btnText.textContent;
        btnText.textContent = '✓ Tersalin!';
        setTimeout(() => {
            btnText.textContent = orig;
        }, 1500);
    }
}

function fallbackCopy(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        showCopyFeedback();
    } catch (e) {
        console.error('Fallback copy failed', e);
    }
    document.body.removeChild(textarea);
}

/**
 * Cetak bukti forensik PDF
 */
function printForensicProof() {
    window.print();
}

/**
 * Modal Compatibility: openModalLogDetail & closeModalLogDetail
 */
function openModalLogDetail(data) {
    if (!data) return;
    const logId = document.getElementById('logId');
    if (logId) logId.textContent = data.id;

    const logChannel = document.getElementById('logChannel');
    if (logChannel) logChannel.textContent = data.channel || data.channel_key || '-';

    const logUser = document.getElementById('logUser');
    if (logUser) logUser.textContent = data.user || data.actor_name || '-';

    const logWaktu = document.getElementById('logWaktu');
    if (logWaktu) logWaktu.textContent = data.waktu;

    const logAksi = document.getElementById('logAksi');
    if (logAksi) logAksi.textContent = data.aksi;

    const logEntitas = document.getElementById('logEntitas');
    if (logEntitas) logEntitas.textContent = (data.entitas || '-') + (data.entitas_id ? (' #' + data.entitas_id) : '');

    const logIp = document.getElementById('logIp');
    if (logIp) logIp.textContent = data.ip || '-';

    const logRequestId = document.getElementById('logRequestId');
    if (logRequestId) logRequestId.textContent = data.request_id || '-';

    const logHttp = document.getElementById('logHttp');
    if (logHttp) {
        logHttp.textContent = (data.metode || 'GET') + ' ' + (data.rute || '-') + (data.status_http ? (' [' + data.status_http + ']') : '') + (data.durasi_ms ? (' (' + data.durasi_ms + 'ms)') : '');
    }

    const sevElem = document.getElementById('logSeverity');
    if (sevElem) {
        const s = (data.severity || 'info').toLowerCase();
        sevElem.textContent = s.toUpperCase();
        sevElem.className = 'badge-severity-pill badge-sev-' + s;
    }

    const logNilaiLama = document.getElementById('logNilaiLama');
    if (logNilaiLama) {
        logNilaiLama.textContent = typeof data.nilai_lama === 'string'
            ? data.nilai_lama
            : (data.nilai_lama ? JSON.stringify(data.nilai_lama, null, 2) : '- (Tidak ada nilai lama)');
    }

    const logNilaiBaru = document.getElementById('logNilaiBaru');
    if (logNilaiBaru) {
        logNilaiBaru.textContent = typeof data.nilai_baru === 'string'
            ? data.nilai_baru
            : (data.nilai_baru ? JSON.stringify(data.nilai_baru, null, 2) : '- (Tidak ada nilai baru)');
    }

    const logPayload = document.getElementById('logPayload');
    if (logPayload) {
        logPayload.textContent = typeof data.detail === 'string'
            ? data.detail
            : (data.detail ? JSON.stringify(data.detail, null, 2) : '- (Tidak ada data tambahan)');
    }

    const modal = document.getElementById('modalLogDetail');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModalLogDetail() {
    const modal = document.getElementById('modalLogDetail');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}
</script>
