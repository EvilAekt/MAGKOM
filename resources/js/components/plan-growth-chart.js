/**
 * Plan Growth Stacked / Curved Area SVG Chart
 * Vanilla JS (ES Module) for MagKom Annual Analytics Dashboard
 * Renders smooth cubic Bézier curves, semi-transparent gradients, and interactive hover tooltips.
 */

export function initPlanGrowthChart() {
    const containers = document.querySelectorAll('[data-plan-growth-chart]');
    if (!containers.length) return;

    containers.forEach((container) => {
        // Prevent duplicate initialization
        if (container.dataset.chartInitialized === 'true') return;
        container.dataset.chartInitialized = 'true';

        let data = [];
        try {
            data = JSON.parse(container.getAttribute('data-series') || '[]');
        } catch (e) {
            console.error('[PlanGrowthChart] Failed to parse data-series JSON', e);
            return;
        }

        if (!Array.isArray(data) || data.length === 0) return;

        renderChart(container, data);
    });
}

function renderChart(container, data) {
    const width = 620;
    const height = 290;
    const padding = { top: 25, right: 25, bottom: 35, left: 35 };

    const plotWidth = width - padding.left - padding.right;
    const plotHeight = height - padding.top - padding.bottom;

    // Calculate maximum value across all 3 series for dynamic Y scale
    let maxY = 0;
    data.forEach((item) => {
        const val = Math.max(item.pendaftar || 0, item.diterima || 0, item.selesai || 0);
        if (val > maxY) maxY = val;
    });

    // Provide sensible minimum ceiling and head-room
    if (maxY <= 4) maxY = 5;
    else if (maxY <= 8) maxY = 10;
    else maxY = Math.ceil(maxY * 1.25);

    const stepX = plotWidth / (data.length - 1);

    // Compute coordinate points (X, Y)
    const pointsPendaftar = data.map((d, i) => ({
        x: padding.left + i * stepX,
        y: padding.top + plotHeight - ((d.pendaftar || 0) / maxY) * plotHeight,
        val: d.pendaftar || 0,
    }));

    const pointsDiterima = data.map((d, i) => ({
        x: padding.left + i * stepX,
        y: padding.top + plotHeight - ((d.diterima || 0) / maxY) * plotHeight,
        val: d.diterima || 0,
    }));

    const pointsAlumni = data.map((d, i) => ({
        x: padding.left + i * stepX,
        y: padding.top + plotHeight - ((d.selesai || 0) / maxY) * plotHeight,
        val: d.selesai || 0,
    }));

    // Generate smooth cubic Bézier SVG path
    function createSmoothLine(pts) {
        if (!pts.length) return '';
        let path = `M ${pts[0].x.toFixed(1)} ${pts[0].y.toFixed(1)}`;
        for (let i = 0; i < pts.length - 1; i++) {
            const p0 = pts[i === 0 ? 0 : i - 1];
            const p1 = pts[i];
            const p2 = pts[i + 1];
            const p3 = pts[i + 2 < pts.length ? i + 2 : i + 1];

            const cp1x = p1.x + (p2.x - p0.x) / 6;
            const cp1y = p1.y + (p2.y - p0.y) / 6;
            const cp2x = p2.x - (p3.x - p1.x) / 6;
            const cp2y = p2.y - (p3.y - p1.y) / 6;

            path += ` C ${cp1x.toFixed(1)} ${cp1y.toFixed(1)}, ${cp2x.toFixed(1)} ${cp2y.toFixed(1)}, ${p2.x.toFixed(1)} ${p2.y.toFixed(1)}`;
        }
        return path;
    }

    function createSmoothArea(pts) {
        const line = createSmoothLine(pts);
        if (!line) return '';
        const baselineY = (padding.top + plotHeight).toFixed(1);
        const lastX = pts[pts.length - 1].x.toFixed(1);
        const firstX = pts[0].x.toFixed(1);
        return `${line} L ${lastX} ${baselineY} L ${firstX} ${baselineY} Z`;
    }

    const areaPendaftar = createSmoothArea(pointsPendaftar);
    const linePendaftar = createSmoothLine(pointsPendaftar);

    const areaDiterima = createSmoothArea(pointsDiterima);
    const lineDiterima = createSmoothLine(pointsDiterima);

    const areaAlumni = createSmoothArea(pointsAlumni);
    const lineAlumni = createSmoothLine(pointsAlumni);

    // Build SVG markup
    const gridRows = 4;
    let gridLinesHtml = '';
    for (let r = 0; r <= gridRows; r++) {
        const y = padding.top + (plotHeight / gridRows) * r;
        const val = Math.round(maxY - (maxY / gridRows) * r);
        gridLinesHtml += `
            <line x1="${padding.left}" y1="${y}" x2="${width - padding.right}" y2="${y}" stroke="currentColor" stroke-opacity="0.08" stroke-dasharray="3 3"/>
            <text x="${padding.left - 10}" y="${y + 4}" fill="currentColor" fill-opacity="0.45" font-size="11" text-anchor="end" font-weight="600">${val}</text>
        `;
    }

    let xAxisHtml = '';
    const shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    data.forEach((d, i) => {
        const x = padding.left + i * stepX;
        const label = shortMonths[i] || d.nama_bulan.substring(0, 3);
        xAxisHtml += `
            <text x="${x}" y="${height - 10}" fill="currentColor" fill-opacity="0.6" font-size="11.5" text-anchor="middle" font-weight="600">${label}</text>
        `;
    });

    const svgHtml = `
        <svg viewBox="0 0 ${width} ${height}" class="plan-growth-svg" preserveAspectRatio="none">
            <defs>
                <!-- Pendaftar Gradient (Emerald) -->
                <linearGradient id="pgGradPendaftar" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#10B981" stop-opacity="0.35"/>
                    <stop offset="100%" stop-color="#10B981" stop-opacity="0.01"/>
                </linearGradient>

                <!-- Diterima Gradient (Royal Blue) -->
                <linearGradient id="pgGradDiterima" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#3F51B5" stop-opacity="0.32"/>
                    <stop offset="100%" stop-color="#3F51B5" stop-opacity="0.01"/>
                </linearGradient>

                <!-- Alumni Gradient (Amber Gold) -->
                <linearGradient id="pgGradAlumni" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#F59E0B" stop-opacity="0.28"/>
                    <stop offset="100%" stop-color="#F59E0B" stop-opacity="0.01"/>
                </linearGradient>
            </defs>

            <!-- Grid Lines -->
            ${gridLinesHtml}

            <!-- X Axis Labels -->
            ${xAxisHtml}

            <!-- 1. Pendaftar Masuk Area & Line -->
            <path d="${areaPendaftar}" fill="url(#pgGradPendaftar)"/>
            <path d="${linePendaftar}" fill="none" stroke="#10B981" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>

            <!-- 2. Peserta Diterima Area & Line -->
            <path d="${areaDiterima}" fill="url(#pgGradDiterima)"/>
            <path d="${lineDiterima}" fill="none" stroke="#3F51B5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>

            <!-- 3. Alumni Selesai Area & Line -->
            <path d="${areaAlumni}" fill="url(#pgGradAlumni)"/>
            <path d="${lineAlumni}" fill="none" stroke="#F59E0B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    `;

    container.innerHTML = `
        ${svgHtml}
        <div class="plan-growth-crosshair"></div>
        <div class="plan-growth-tooltip"></div>
    `;

    // Interactive Hover Listeners
    const crosshair = container.querySelector('.plan-growth-crosshair');
    const tooltip = container.querySelector('.plan-growth-tooltip');
    const svgElem = container.querySelector('.plan-growth-svg');

    function handlePointerMove(e) {
        const rect = svgElem.getBoundingClientRect();
        const clientX = e.clientX || (e.touches && e.touches[0].clientX);
        if (!clientX) return;

        const relX = clientX - rect.left;
        const scale = width / rect.width;
        const svgX = relX * scale;

        // Find nearest month index
        let closestIdx = 0;
        let minDist = Infinity;
        for (let i = 0; i < data.length; i++) {
            const ptX = padding.left + i * stepX;
            const dist = Math.abs(svgX - ptX);
            if (dist < minDist) {
                minDist = dist;
                closestIdx = i;
            }
        }

        const activePt = data[closestIdx];
        const screenX = (padding.left + closestIdx * stepX) / scale;

        // Position crosshair
        if (crosshair) {
            crosshair.style.opacity = '1';
            crosshair.style.left = `${screenX}px`;
        }

        // Render & Position Tooltip with Boundary Protection
        if (tooltip && activePt) {
            tooltip.style.opacity = '1';
            const tooltipWidth = 160;
            const leftPos = (screenX + tooltipWidth + 10 > rect.width)
                ? (screenX - tooltipWidth - 10)
                : (screenX + 10);
            tooltip.style.left = `${Math.max(10, leftPos)}px`;
            tooltip.style.top = '15px';
            tooltip.innerHTML = `
                <div style="font-weight: 700; border-bottom: 1px solid rgba(255,255,255,0.15); padding-bottom: 4px; margin-bottom: 5px;">
                    ${activePt.nama_bulan}
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 2px;">
                    <span style="display: inline-flex; align-items: center; gap: 5px; color: #6EE7B7;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981;"></span> Pendaftar:
                    </span>
                    <strong>${activePt.pendaftar || 0}</strong>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 2px;">
                    <span style="display: inline-flex; align-items: center; gap: 5px; color: #93C5FD;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #3F51B5;"></span> Diterima:
                    </span>
                    <strong>${activePt.diterima || 0} (${activePt.rasio || 0}%)</strong>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                    <span style="display: inline-flex; align-items: center; gap: 5px; color: #FCD34D;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #F59E0B;"></span> Selesai:
                    </span>
                    <strong>${activePt.selesai || 0}</strong>
                </div>
            `;
        }
    }

    function handlePointerLeave() {
        if (crosshair) crosshair.style.opacity = '0';
        if (tooltip) tooltip.style.opacity = '0';
    }

    container.addEventListener('mousemove', handlePointerMove);
    container.addEventListener('mouseleave', handlePointerLeave);
    container.addEventListener('touchstart', handlePointerMove, { passive: true });
    container.addEventListener('touchend', handlePointerLeave, { passive: true });
}

// Auto-run on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPlanGrowthChart);
} else {
    initPlanGrowthChart();
}

