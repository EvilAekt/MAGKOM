/**
 * Modul Telemetri Kesehatan Infrastruktur & Server SuperAdmin
 * - Rendering Kurva Latensi HTTP & SSE (Native HTML5 Canvas 2D)
 * - Selektor filter rentang waktu (15m, 1h, 24h) & mode grafik (Semua, P95, SSE)
 * - Paginasi client-side tabel endpoint & live SSE telemetry stream
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('routeLatencyCanvas');
        const container = document.getElementById('canvasContainer');
        const tooltip = document.getElementById('canvasTooltip');
        const lastUpdateEl = document.getElementById('canvasLastUpdate');
        const filterInput = document.getElementById('endpointTableFilter');
        const btnTestSse = document.getElementById('btnTestSse');

        if (!canvas || !container) return;

        // Inisialisasi Data Awal dari Elemen Data JSON
        let telemetryData = [];
        let totalRequestsCount = 0;
        const payloadEl = document.getElementById('telemetry-initial-data');
        if (payloadEl) {
            try {
                const parsed = JSON.parse(payloadEl.textContent || '{}');
                telemetryData = parsed.routeMetrics || [];
                totalRequestsCount = parsed.totalRouteRequests || 0;
            } catch (e) {
                telemetryData = [];
            }
        }

        const ctx = canvas.getContext('2d');
        let currentFilterMode = 'all';
        let currentTimeWindow = '15m';
        let telemetryCurrentPage = 1;
        let telemetryPerPage = 10;

        // Konfigurasi Padding & Margin Kanvas
        const padding = { top: 30, right: 35, bottom: 35, left: 55 };

        function filterDataByWindow(data, windowKey) {
            if (!data || !data.length) return [];
            const now = Date.now();
            let windowMs = 15 * 60 * 1000;
            let fallbackCount = 15;

            if (windowKey === '1h') {
                windowMs = 60 * 60 * 1000;
                fallbackCount = 30;
            } else if (windowKey === '24h') {
                windowMs = 24 * 60 * 60 * 1000;
                fallbackCount = 60;
            }

            const timeFiltered = data.filter((d) => {
                let t = null;
                if (d.timestamp) {
                    t = d.timestamp > 1e11 ? d.timestamp : d.timestamp * 1000;
                } else if (d.created_at) {
                    t = new Date(d.created_at).getTime();
                }
                if (t !== null && !isNaN(t)) {
                    return now - t <= windowMs;
                }
                return true;
            });

            if (timeFiltered.length >= 3) {
                return timeFiltered;
            }
            return data.slice(-fallbackCount);
        }

        function resizeCanvas() {
            const rect = container.getBoundingClientRect();
            const dpr = window.devicePixelRatio || 1;
            canvas.width = Math.floor(rect.width * dpr);
            canvas.height = Math.floor(280 * dpr);
            canvas.style.width = rect.width + 'px';
            canvas.style.height = '280px';

            if (ctx.resetTransform) {
                ctx.resetTransform();
            } else {
                ctx.setTransform(1, 0, 0, 1, 0, 0);
            }
            ctx.scale(dpr, dpr);
            draw();
        }

        function draw() {
            const width = canvas.width / (window.devicePixelRatio || 1);
            const height = 280;

            ctx.clearRect(0, 0, width, height);

            const maxY = 350;
            const graphW = width - padding.left - padding.right;
            const graphH = height - padding.top - padding.bottom;

            let displayData = telemetryData.slice();
            if (currentFilterMode === 'p95') {
                displayData = displayData.filter((d) => (parseFloat(d.duration_ms) || 0) >= 100);
            } else if (currentFilterMode === 'sse') {
                displayData = displayData.filter(
                    (d) => (d.uri || '').includes('realtime/stream') || (parseFloat(d.duration_ms) || 0) <= 20
                );
            }

            displayData = filterDataByWindow(displayData, currentTimeWindow);
            const pointCount = displayData.length;

            ctx.strokeStyle = 'rgba(255, 255, 255, 0.06)';
            ctx.lineWidth = 1;
            [0, 100, 200, 300].forEach((val) => {
                const y = padding.top + graphH - (val / maxY) * graphH;
                ctx.beginPath();
                ctx.moveTo(padding.left, y);
                ctx.lineTo(width - padding.right, y);
                ctx.stroke();

                ctx.fillStyle = '#64748B';
                ctx.font = '10px monospace';
                ctx.textAlign = 'right';
                ctx.fillText(val + 'ms', padding.left - 8, y + 3);
            });

            if (pointCount < 2) {
                renderFallbackWave(width, height, graphW, graphH);
                return;
            }

            const stepX = graphW / (pointCount - 1);
            const computedPoints = [];

            displayData.forEach((item, idx) => {
                const ms = parseFloat(item.duration_ms) || 0;
                const x = padding.left + idx * stepX;
                const y = padding.top + graphH - Math.min(graphH, (ms / maxY) * graphH);
                computedPoints.push({ x, y, data: item });
            });

            // 1. Gambar Area Gradient Bawah
            const p50Grad = ctx.createLinearGradient(0, padding.top, 0, height - padding.bottom);
            p50Grad.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
            p50Grad.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            ctx.fillStyle = p50Grad;
            ctx.beginPath();
            ctx.moveTo(computedPoints[0].x, height - padding.bottom);
            computedPoints.forEach((pt, i) => {
                if (i === 0) ctx.lineTo(pt.x, pt.y);
                else {
                    const prev = computedPoints[i - 1];
                    const cpX = (prev.x + pt.x) / 2;
                    ctx.bezierCurveTo(cpX, prev.y, cpX, pt.y, pt.x, pt.y);
                }
            });
            ctx.lineTo(computedPoints[computedPoints.length - 1].x, height - padding.bottom);
            ctx.closePath();
            ctx.fill();

            // 2. Gambar Garis Kurva Utama
            ctx.strokeStyle = '#38BDF8';
            ctx.lineWidth = 2.5;
            ctx.beginPath();
            computedPoints.forEach((pt, i) => {
                if (i === 0) ctx.moveTo(pt.x, pt.y);
                else {
                    const prev = computedPoints[i - 1];
                    const cpX = (prev.x + pt.x) / 2;
                    ctx.bezierCurveTo(cpX, prev.y, cpX, pt.y, pt.x, pt.y);
                }
            });
            ctx.stroke();

            // 3. Render Titik & Highlight Spike Callout
            let maxPoint = null;
            computedPoints.forEach((pt) => {
                const ms = parseFloat(pt.data.duration_ms) || 0;
                if (!maxPoint || ms > (parseFloat(maxPoint.data.duration_ms) || 0)) {
                    maxPoint = pt;
                }

                ctx.fillStyle = ms > 200 ? '#EF4444' : ms > 50 ? '#FBBF24' : '#10B981';
                ctx.beginPath();
                ctx.arc(pt.x, pt.y, ms > 200 ? 4.5 : 3, 0, Math.PI * 2);
                ctx.fill();
            });

            // Spike Callout Badge
            if (maxPoint && (parseFloat(maxPoint.data.duration_ms) || 0) > 100) {
                const spikeMs = Math.round(parseFloat(maxPoint.data.duration_ms));
                ctx.fillStyle = '#0F172A';
                ctx.strokeStyle = '#EF4444';
                ctx.lineWidth = 1.5;
                const badgeW = 56;
                const badgeH = 18;
                const bx = Math.min(width - padding.right - badgeW, Math.max(padding.left, maxPoint.x - badgeW / 2));
                const by = Math.max(padding.top, maxPoint.y - 28);

                ctx.beginPath();
                if (ctx.roundRect) {
                    ctx.roundRect(bx, by, badgeW, badgeH, 4);
                } else {
                    ctx.rect(bx, by, badgeW, badgeH);
                }
                ctx.fill();
                ctx.stroke();

                ctx.fillStyle = '#F87171';
                ctx.font = 'bold 10px monospace';
                ctx.textAlign = 'center';
                ctx.fillText(spikeMs + 'ms', bx + badgeW / 2, by + 12);
            }

            const lastPt = computedPoints[computedPoints.length - 1];
            if (lastPt) {
                ctx.fillStyle = '#38BDF8';
                ctx.beginPath();
                ctx.arc(lastPt.x, lastPt.y, 4, 0, Math.PI * 2);
                ctx.fill();
                ctx.strokeStyle = '#FFFFFF';
                ctx.lineWidth = 1.5;
                ctx.stroke();
            }
        }

        function renderFallbackWave(width, height, graphW, graphH) {
            const p95Grad = ctx.createLinearGradient(0, padding.top, 0, height - padding.bottom);
            p95Grad.addColorStop(0, 'rgba(56, 189, 248, 0.35)');
            p95Grad.addColorStop(1, 'rgba(56, 189, 248, 0.0)');

            ctx.fillStyle = p95Grad;
            ctx.beginPath();
            ctx.moveTo(padding.left, height - padding.bottom);
            ctx.bezierCurveTo(
                padding.left + graphW * 0.3,
                padding.top + 60,
                padding.left + graphW * 0.6,
                padding.top + 140,
                padding.left + graphW,
                padding.top + 100
            );
            ctx.lineTo(padding.left + graphW, height - padding.bottom);
            ctx.closePath();
            ctx.fill();

            ctx.strokeStyle = '#38BDF8';
            ctx.lineWidth = 2.5;
            ctx.beginPath();
            ctx.moveTo(padding.left, height - padding.bottom - 40);
            ctx.bezierCurveTo(
                padding.left + graphW * 0.3,
                padding.top + 60,
                padding.left + graphW * 0.6,
                padding.top + 140,
                padding.left + graphW,
                padding.top + 100
            );
            ctx.stroke();
        }

        function renderTelemetryPaginationControls(totalPages) {
            const controlsContainer = document.getElementById('telemetryPageControls');
            if (!controlsContainer) return;

            controlsContainer.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.type = 'button';
            prevBtn.className = 'telemetry-page-btn-nav' + (telemetryCurrentPage <= 1 ? ' disabled' : '');
            prevBtn.disabled = telemetryCurrentPage <= 1;
            prevBtn.innerHTML = '&laquo; Prev';
            prevBtn.setAttribute('aria-label', 'Lembar Sebelumnya');
            prevBtn.addEventListener('click', function () {
                if (telemetryCurrentPage > 1) {
                    telemetryCurrentPage--;
                    filterTableByWindow(currentTimeWindow);
                }
            });
            controlsContainer.appendChild(prevBtn);

            let pages = [];
            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) pages.push(i);
            } else {
                pages.push(1);
                if (telemetryCurrentPage > 3) {
                    pages.push('...');
                }
                const start = Math.max(2, telemetryCurrentPage - 1);
                const end = Math.min(totalPages - 1, telemetryCurrentPage + 1);
                for (let i = start; i <= end; i++) {
                    if (!pages.includes(i)) pages.push(i);
                }
                if (telemetryCurrentPage < totalPages - 2) {
                    pages.push('...');
                }
                if (!pages.includes(totalPages)) {
                    pages.push(totalPages);
                }
            }

            pages.forEach((p) => {
                if (p === '...') {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'telemetry-page-ellipsis';
                    ellipsis.textContent = '...';
                    controlsContainer.appendChild(ellipsis);
                } else {
                    const pageBtn = document.createElement('button');
                    pageBtn.type = 'button';
                    pageBtn.className = 'telemetry-page-num' + (p === telemetryCurrentPage ? ' active' : '');
                    pageBtn.textContent = p;
                    pageBtn.setAttribute('aria-label', 'Lembar ' + p);
                    if (p === telemetryCurrentPage) {
                        pageBtn.setAttribute('aria-current', 'page');
                    }
                    pageBtn.addEventListener('click', function () {
                        telemetryCurrentPage = p;
                        filterTableByWindow(currentTimeWindow);
                    });
                    controlsContainer.appendChild(pageBtn);
                }
            });

            const nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = 'telemetry-page-btn-nav' + (telemetryCurrentPage >= totalPages ? ' disabled' : '');
            nextBtn.disabled = telemetryCurrentPage >= totalPages;
            nextBtn.innerHTML = 'Next &raquo;';
            nextBtn.setAttribute('aria-label', 'Lembar Selanjutnya');
            nextBtn.addEventListener('click', function () {
                if (telemetryCurrentPage < totalPages) {
                    telemetryCurrentPage++;
                    filterTableByWindow(currentTimeWindow);
                }
            });
            controlsContainer.appendChild(nextBtn);
        }

        function filterTableByWindow(windowKey) {
            const rows = document.querySelectorAll('.route-table-row');
            if (!rows.length) return;
            const now = Date.now();
            let windowMs = 15 * 60 * 1000;
            let fallbackLimit = 15;

            if (windowKey === '1h') {
                windowMs = 60 * 60 * 1000;
                fallbackLimit = 30;
            } else if (windowKey === '24h') {
                windowMs = 24 * 60 * 60 * 1000;
                fallbackLimit = 60;
            }

            const query = (filterInput ? filterInput.value : '').toLowerCase().trim();

            const matchedRows = [];
            rows.forEach((row, index) => {
                const tsAttr = row.getAttribute('data-timestamp');
                const ts = tsAttr ? parseInt(tsAttr, 10) : 0;
                let timeMatch = true;

                if (ts > 0) {
                    const diff = now - (ts > 1e11 ? ts : ts * 1000);
                    timeMatch = diff <= windowMs;
                } else {
                    timeMatch = index < fallbackLimit;
                }

                if (!timeMatch && index < fallbackLimit) {
                    timeMatch = true;
                }

                const text = (row.textContent + ' ' + (row.getAttribute('data-route') || '')).toLowerCase();
                const textMatch = !query || text.includes(query);

                if (timeMatch && textMatch) {
                    matchedRows.push(row);
                }
            });

            const totalRows = matchedRows.length;
            const totalPages = Math.max(1, Math.ceil(totalRows / telemetryPerPage));
            if (telemetryCurrentPage > totalPages) {
                telemetryCurrentPage = totalPages;
            }
            if (telemetryCurrentPage < 1) {
                telemetryCurrentPage = 1;
            }

            const startIndex = totalRows === 0 ? 0 : (telemetryCurrentPage - 1) * telemetryPerPage;
            const endIndex = totalRows === 0 ? 0 : Math.min(startIndex + telemetryPerPage, totalRows);

            const activeRowsSet = new Set(matchedRows.slice(startIndex, endIndex));
            rows.forEach((row) => {
                if (activeRowsSet.has(row)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            const startRowEl = document.getElementById('telemetryStartRow');
            const endRowEl = document.getElementById('telemetryEndRow');
            const totalRowsEl = document.getElementById('telemetryTotalRows');
            const curPageEl = document.getElementById('telemetryCurrentPage');
            const totalPagesEl = document.getElementById('telemetryTotalPages');

            if (startRowEl) startRowEl.textContent = totalRows === 0 ? '0' : startIndex + 1;
            if (endRowEl) endRowEl.textContent = endIndex;
            if (totalRowsEl) totalRowsEl.textContent = totalRows;
            if (curPageEl) curPageEl.textContent = telemetryCurrentPage;
            if (totalPagesEl) totalPagesEl.textContent = totalPages;

            renderTelemetryPaginationControls(totalPages);
        }

        document.querySelectorAll('.telemetry-per-page-pill').forEach((btn) => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.telemetry-per-page-pill').forEach((b) => b.classList.remove('active'));
                this.classList.add('active');
                telemetryPerPage = parseInt(this.getAttribute('data-per-page'), 10) || 10;
                telemetryCurrentPage = 1;
                filterTableByWindow(currentTimeWindow);
            });
        });

        if (filterInput) {
            filterInput.addEventListener('input', function () {
                telemetryCurrentPage = 1;
                filterTableByWindow(currentTimeWindow);
            });
        }

        document.querySelectorAll('.time-window-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.time-window-btn').forEach((b) => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                currentTimeWindow = this.getAttribute('data-window') || '15m';
                telemetryCurrentPage = 1;

                draw();
                filterTableByWindow(currentTimeWindow);

                if (lastUpdateEl) {
                    const labelMap = { '15m': '15 Menit', '1h': '1 Jam', '24h': '24 Jam' };
                    lastUpdateEl.textContent = `Rentang: ${labelMap[currentTimeWindow] || currentTimeWindow} aktif`;
                }

                fetch(`/superadmin/health-monitor/telemetry-data?window=${encodeURIComponent(currentTimeWindow)}`)
                    .then((res) => res.json())
                    .then((data) => {
                        if (
                            data &&
                            data.telemetry &&
                            Array.isArray(data.telemetry.recent_routes) &&
                            data.telemetry.recent_routes.length > 0
                        ) {
                            data.telemetry.recent_routes.forEach((item) => {
                                if (!telemetryData.some((d) => d.id === item.id)) {
                                    telemetryData.push(item);
                                }
                            });
                            draw();
                            filterTableByWindow(currentTimeWindow);
                        }
                    })
                    .catch(() => {});
            });
        });

        filterTableByWindow(currentTimeWindow);

        document.querySelectorAll('.chart-mode-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.chart-mode-btn').forEach((b) => {
                    b.classList.remove('active');
                    b.style.backgroundColor = 'transparent';
                    b.style.color = 'var(--tel-text-muted)';
                    b.style.fontWeight = 'normal';
                });
                this.classList.add('active');
                this.style.backgroundColor = 'var(--tel-btn-sse-bg)';
                this.style.color = '#ffffff';
                this.style.fontWeight = 'bold';
                currentFilterMode = this.getAttribute('data-mode') || 'all';
                draw();
            });
        });

        if (btnTestSse) {
            btnTestSse.addEventListener('click', function () {
                const now = Date.now();
                const dummyMetric = {
                    id: now,
                    method: 'GET',
                    route_name: 'superadmin.health.test-sse',
                    uri: '/superadmin/health/test-sse',
                    status_code: 200,
                    duration_ms: (Math.random() * 40 + 10).toFixed(2),
                    memory_mb: 4.2,
                    time: new Date().toTimeString().split(' ')[0],
                    timestamp: now,
                    created_at: new Date(now).toISOString(),
                };

                telemetryData.push(dummyMetric);
                if (telemetryData.length > 80) telemetryData.shift();

                draw();
                filterTableByWindow(currentTimeWindow);

                if (lastUpdateEl) {
                    lastUpdateEl.textContent = 'Pembaruan: Baru saja (SSE Trigger)';
                }
            });
        }

        if (window.EventSource) {
            try {
                const stream = new EventSource('/realtime/stream');
                stream.addEventListener('magkom:telemetry-route', function (e) {
                    try {
                        const data = JSON.parse(e.data);
                        const now = Date.now();
                        telemetryData.push({
                            id: data.id || now,
                            method: data.method || 'GET',
                            route_name: data.route_name || data.uri || '/stream',
                            uri: data.uri || '/stream',
                            status_code: data.status_code || 200,
                            duration_ms: parseFloat(data.duration_ms) || 12.0,
                            memory_mb: parseFloat(data.memory_mb) || 5.0,
                            time: data.time || new Date().toTimeString().split(' ')[0],
                            timestamp: data.timestamp || now,
                            created_at: data.created_at || new Date(now).toISOString(),
                        });
                        if (telemetryData.length > 80) telemetryData.shift();
                        draw();
                        filterTableByWindow(currentTimeWindow);
                    } catch (err) {}
                });
            } catch (err) {}
        }

        window.addEventListener('resize', resizeCanvas);
        setTimeout(resizeCanvas, 50);
    });
})();

