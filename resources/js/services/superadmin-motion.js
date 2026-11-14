/**
 * ==============================================================================
 * MagKom Sleman - SuperAdmin Motion Service (GSAP Timeline Engine)
 * Reusable animation engine for SuperAdmin pages (Entrance timeline & Chart hover)
 * ==============================================================================
 */

import gsap from 'gsap';
import { initScrollCraft } from './scroll-craft.js';

/**
 * Cek apakah animasi entrance diizinkan (Aksesibilitas & Initial Load Guard)
 */
export function shouldAnimatePage(storageKeyPrefix = 'magkom_superadmin_entrance') {
    // 1. Cek WCAG prefers-reduced-motion (Aksesibilitas Wajib)
    const prefersReducedMotion = typeof window !== 'undefined' &&
        window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return false;

    // 2. Cek sessionStorage agar entrance timeline hanya jalan 1x saat initial load
    if (typeof window === 'undefined' || !window.sessionStorage) return true;

    const currentPath = window.location.pathname;
    const storageKey = `${storageKeyPrefix}_${currentPath}`;
    let isAnimated = false;

    try {
        isAnimated = sessionStorage.getItem(storageKey) === 'true';
    } catch (e) {
        isAnimated = false;
    }

    if (isAnimated) {
        return false; // Halaman sudah pernah dimuat dalam sesi ini: jangan diulang
    }

    try {
        sessionStorage.setItem(storageKey, 'true');
    } catch (e) {}

    return true;
}

/**
 * Animasikan Efek Angka Bergulir (Rolling Counter)
 * @param {HTMLElement} valEl
 * @param {gsap.core.Timeline} tl
 * @param {number} position
 */
function attachRollingCounter(valEl, tl, position = 0.18) {
    if (!valEl) return;
    const rawText = valEl.textContent.trim().replace(/[^0-9]/g, '');
    const targetVal = parseInt(rawText, 10);
    if (isNaN(targetVal) || targetVal <= 0) return;

    const counterObj = { val: 0 };
    valEl.textContent = '0';
    tl.to(counterObj, {
        val: targetVal,
        duration: 0.65,
        ease: 'power2.out',
        onUpdate: () => {
            valEl.textContent = Math.round(counterObj.val).toLocaleString();
        }
    }, position);
}

/**
 * Orkestrasi Animasi Entrance Berjenjang (Initial Load Only)
 */
export function animateSuperAdminEntrance() {
    const tl = gsap.timeline({
        defaults: { ease: 'power2.out' }
    });

    // 1. Hero Studio Banner (Slide-Down & Fade-In)
    const heroBanners = document.querySelectorAll('.sa-hero-banner, .sa-hero, .gentelella-page-header');
    if (heroBanners.length) {
        tl.fromTo(heroBanners,
            { opacity: 0, y: -14 },
            { opacity: 1, y: 0, duration: 0.45 },
            0
        );
    }

    // 2. 4 Kartu KPI Tile Stats / Metrics Grid (Staggered Rise Up)
    const statCards = document.querySelectorAll('.sa-metrics-row > *, .tiles-grid > *, .sa-stat-grid > *, .audit-kpi-card, .tile_stats_count');
    if (statCards.length) {
        tl.fromTo(statCards,
            { opacity: 0, y: 16, scale: 0.98 },
            { opacity: 1, y: 0, scale: 1, duration: 0.42, stagger: 0.07 },
            0.12
        );
    }

    // 3. Mini Sparkline Bars Rise (Jika Ada)
    const sparkBars = document.querySelectorAll('.sa-spark i, .stat-spark i');
    if (sparkBars.length) {
        tl.fromTo(sparkBars,
            { scaleY: 0, opacity: 0.2 },
            { scaleY: 1, opacity: 0.85, duration: 0.38, stagger: 0.015, transformOrigin: 'bottom center' },
            0.22
        );
    }

    // 4. Rolling Counter pada Angka Metrik Utama
    const metricNumbers = document.querySelectorAll('.sa-stat-value, .stat-value, .count, .audit-kpi-val, .gt-hero-stat__number');
    metricNumbers.forEach((numEl, idx) => {
        // Batasi counter rolling ke maksimal 8 elemen teratas agar performa optimal
        if (idx < 8) {
            attachRollingCounter(numEl, tl, 0.18 + (idx * 0.04));
        }
    });

    // 5. Main Cockpit Layout & Content Surfaces
    const mainSurfaces = document.querySelectorAll('.sa-cockpit-main, .audit-main-layout, .sa-card, .table-responsive, .superadmin-akun-wrapper, .audit-workspace-root main, .telemetry-grid');
    if (mainSurfaces.length) {
        tl.fromTo(mainSurfaces,
            { opacity: 0, y: 12 },
            { opacity: 1, y: 0, duration: 0.45 },
            0.28
        );
    }

    // 6. Aside Widgets (Mini Kalender, Channel Sidebar)
    const asideWidgets = document.querySelectorAll('.sa-cockpit-aside, .channels-sidebar, .sa-calendar-widget');
    if (asideWidgets.length) {
        tl.fromTo(asideWidgets,
            { opacity: 0, x: 14 },
            { opacity: 1, x: 0, duration: 0.45 },
            0.35
        );
    }

    // 7. Initial Entrance Chart (Stroke Draw & Data Points Pop-in)
    const primCurve = document.querySelector('.gt-curve-primary');
    if (primCurve) {
        try {
            const length = typeof primCurve.getTotalLength === 'function' ? primCurve.getTotalLength() : 800;
            tl.fromTo(primCurve,
                { strokeDasharray: length, strokeDashoffset: length },
                { strokeDashoffset: 0, duration: 0.8, ease: 'power2.inOut' },
                0.38
            );
        } catch (e) {
            tl.fromTo(primCurve, { opacity: 0 }, { opacity: 1, duration: 0.5 }, 0.38);
        }

        const areaFill = document.querySelector('path[fill*="gtTealGradient"]');
        if (areaFill) {
            tl.fromTo(areaFill,
                { opacity: 0 },
                { opacity: 1, duration: 0.45 },
                0.52
            );
        }

        const chartPoints = document.querySelectorAll('.gt-chart-point');
        if (chartPoints.length) {
            tl.fromTo(chartPoints,
                { scale: 0, opacity: 0 },
                { scale: 1, opacity: 1, stagger: 0.035, transformOrigin: 'center center', duration: 0.35, ease: 'back.out(2)' },
                0.55
            );
        }
    }

    return tl;
}

/**
 * Pasang Animasi Interaktif Hover pada Seluruh Komponen Chart/Grafik
 */
export function bindChartHoverAnimations() {
    // 1. SVG Trend Chart Pendaftaran (Dashboard SuperAdmin)
    const chartWrappers = document.querySelectorAll('.gt-chart-wrapper');
    chartWrappers.forEach((wrapper) => {
        if (wrapper.dataset.gsapHoverBound) return;
        wrapper.dataset.gsapHoverBound = 'true';

        wrapper.addEventListener('mouseenter', () => {
            gsap.to(wrapper, {
                boxShadow: '0 10px 28px -6px rgba(1, 52, 124, 0.12)',
                borderColor: 'rgba(63, 81, 181, 0.35)',
                duration: 0.3,
                ease: 'power2.out'
            });

            const curve = wrapper.querySelector('.gt-curve-primary');
            if (curve) {
                gsap.to(curve, {
                    strokeWidth: 4,
                    filter: 'drop-shadow(0 2px 8px rgba(13, 148, 136, 0.45))',
                    duration: 0.25,
                    ease: 'power2.out'
                });
            }
        });

        wrapper.addEventListener('mouseleave', () => {
            gsap.to(wrapper, {
                boxShadow: 'none',
                borderColor: '',
                duration: 0.25,
                ease: 'power2.inOut'
            });

            const curve = wrapper.querySelector('.gt-curve-primary');
            if (curve) {
                gsap.to(curve, {
                    strokeWidth: 3,
                    filter: 'none',
                    duration: 0.25,
                    ease: 'power2.inOut'
                });
            }
        });

        // Event delegation untuk titik data SVG lingkaran (.gt-chart-point)
        const svg = wrapper.querySelector('.gt-chart-svg');
        if (svg && !svg.dataset.gsapPointsBound) {
            svg.dataset.gsapPointsBound = 'true';

            svg.addEventListener('mouseover', (e) => {
                const pt = e.target.closest('.gt-chart-point');
                if (pt) {
                    gsap.to(pt, {
                        scale: 1.85,
                        transformOrigin: 'center center',
                        stroke: '#003566',
                        strokeWidth: 3,
                        duration: 0.22,
                        ease: 'back.out(2.2)'
                    });
                }
            });

            svg.addEventListener('mouseout', (e) => {
                const pt = e.target.closest('.gt-chart-point');
                if (pt) {
                    gsap.to(pt, {
                        scale: 1,
                        stroke: '#0d9488',
                        strokeWidth: 2,
                        duration: 0.2,
                        ease: 'power1.inOut'
                    });
                }
            });
        }
    });

    // 2. HTML5 Canvas Telemetri & Latensi (SuperAdmin Status Sistem / Health)
    const telemetryCanvasWrappers = document.querySelectorAll('.telemetry-canvas-wrapper, #canvasContainer');
    telemetryCanvasWrappers.forEach((wrapper) => {
        if (wrapper.dataset.gsapHoverBound) return;
        wrapper.dataset.gsapHoverBound = 'true';

        wrapper.addEventListener('mouseenter', () => {
            gsap.to(wrapper, {
                scale: 1.004,
                boxShadow: '0 8px 24px -4px rgba(1, 52, 124, 0.15)',
                borderColor: 'rgba(63, 81, 181, 0.4)',
                duration: 0.3,
                ease: 'power2.out'
            });

            const laser = wrapper.querySelector('.telemetry-scanner-laser');
            if (laser) {
                gsap.to(laser, { opacity: 0.95, duration: 0.2 });
            }
        });

        wrapper.addEventListener('mouseleave', () => {
            gsap.to(wrapper, {
                scale: 1,
                boxShadow: '',
                borderColor: '',
                duration: 0.25,
                ease: 'power2.inOut'
            });

            const laser = wrapper.querySelector('.telemetry-scanner-laser');
            if (laser) {
                gsap.to(laser, { opacity: '', duration: 0.25 });
            }
        });
    });
}

/**
 * Animasikan Kartu Statistik & Sparkline Mini Bars (Paritas Pembimbing)
 * @param {string|HTMLElement} containerSelector
 */
export function animateSuperAdminTileStats(containerSelector = '.tiles-grid, .tile_count, .sa-stat-grid, .backup-stats-grid, .audit-kpi-strip') {
    const container = typeof containerSelector === 'string'
        ? document.querySelector(containerSelector)
        : containerSelector;
    if (!container) return;

    const cards = container.querySelectorAll('.tile_stats_count, .sa-stat-card, .backup-stat-card, .audit-kpi-card');
    if (!cards.length) return;

    const tl = gsap.timeline({
        defaults: { ease: 'power2.out' }
    });

    // 1. Fade & Slide-up pada kartu tile
    tl.fromTo(cards,
        { opacity: 0, y: 12 },
        { opacity: 1, y: 0, duration: 0.42, stagger: 0.07 },
        0
    );

    // 2. Staggered Rise pada mini bars sparkline
    const sparkBars = container.querySelectorAll('.sa-spark i, .stat-spark i');
    if (sparkBars.length) {
        tl.fromTo(sparkBars,
            { scaleY: 0, opacity: 0.2 },
            { scaleY: 1, opacity: 0.85, duration: 0.38, stagger: 0.015, transformOrigin: 'bottom center' },
            0.15
        );
    }

    // 3. Smooth Numeric Counter Rolling pada nilai angka
    cards.forEach((card, idx) => {
        const valEl = card.querySelector('.stat-value, .count, .backup-stat-value, .sa-stat-value, .audit-kpi-val');
        if (valEl) {
            attachRollingCounter(valEl, tl, 0.12 + (idx * 0.04));
        }
    });

    return tl;
}

/**
 * Animasikan Kemunculan Halus Baris Tabel (Staggered Fade-Slide)
 * @param {string} tableSelector
 */
export function animateTableRows(tableSelector = '.sa-table, .gentelella-table, .backup-table, .data-table, .audit-table') {
    const rows = document.querySelectorAll(`${tableSelector} tbody tr`);
    if (!rows.length) return;

    // Batasi ke 15 baris pertama untuk efisiensi
    const animatedRows = Array.from(rows).slice(0, 15);
    return gsap.fromTo(animatedRows,
        { opacity: 0, y: 8 },
        { opacity: 1, y: 0, duration: 0.35, stagger: 0.025, ease: 'power2.out' }
    );
}

/**
 * Inisialisasi Universal SuperAdmin Page Motion (One-Liner Entry Point)
 * Menggabungkan Scroll-Craft Viewport Reveal + GSAP Tile Stats & Table Rows
 * @param {Object} options
 */
export function initSuperAdminPageMotion(options = {}) {
    // 1. Jalankan Scroll-Craft Viewport Reveal Engine
    initScrollCraft(document, options);

    // 2. Cek apakah animasi entrance diizinkan (Aksesibilitas & Session Guard)
    const isAllowed = shouldAnimatePage('magkom_superadmin_motion');
    if (!isAllowed) return;

    // 3. Jalankan Tile Stats Entrance & Rolling Counter
    animateSuperAdminTileStats(options.tileContainer || '.tiles-grid, .tile_count, .sa-stat-grid, .backup-stats-grid, .sa-metrics-row, .audit-kpi-strip');

    // 4. Jalankan Baris Tabel Entrance
    if (options.animateTables !== false) {
        animateTableRows(options.tableSelector || '.sa-table, .gentelella-table, .backup-table, .data-table, .audit-table');
    }

    // 5. Bind Interaksi Chart Hover
    bindChartHoverAnimations();
}

/**
 * Inisialisasi Universal SuperAdmin Motion (Legacy / Fallback)
 * @param {'global'|'dashboard'} context
 */
export function initSuperAdminMotion(context = 'global') {
    // 1. Selalu bind interaksi hover chart
    bindChartHoverAnimations();

    // 2. Jalankan animasi entrance hanya jika diizinkan (Initial load & a11y guard)
    if (shouldAnimatePage('magkom_superadmin_entrance')) {
        animateSuperAdminEntrance();
    }
}

// Global window registration untuk kemudahan akses dan modularitas
if (typeof window !== 'undefined') {
    window.initScrollCraft = initScrollCraft;
    window.initSuperAdminPageMotion = initSuperAdminPageMotion;
    window.MagKomSuperAdminMotion = {
        shouldAnimatePage,
        animateSuperAdminEntrance,
        animateSuperAdminTileStats,
        animateTableRows,
        bindChartHoverAnimations,
        initSuperAdminMotion,
        initSuperAdminPageMotion
    };
}

