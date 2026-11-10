/**
 * ==============================================================================
 * MagKom Sleman - Pembimbing Motion Service (GSAP Non-Intrusive Engine)
 * Reusable animation engine for Pembimbing role pages (Tile stats, charts, cards)
 * ==============================================================================
 */

import gsap from 'gsap';

/**
 * Cek apakah animasi diizinkan (Aksesibilitas & Initial Load Guard)
 */
function shouldAnimatePage(storageKey = 'magkom_last_pembimbing_page') {
    // 1. Cek WCAG prefers-reduced-motion
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return false;

    // 2. Cek sessionStorage agar animasi hanya jalan 1x saat initial page entrance
    const currentPath = window.location.pathname;
    let lastPath = null;
    try {
        lastPath = sessionStorage.getItem(storageKey);
    } catch (e) {
        lastPath = null;
    }

    if (lastPath === currentPath) {
        return false; // Interaksi in-page pada halaman yang sama: jangan diulang
    }

    try {
        sessionStorage.setItem(storageKey, currentPath);
    } catch (e) {}

    return true;
}

/**
 * Animasikan Sparkline Tile Stats (Beban Mentee, Tugas, Logbook, dsb.)
 * @param {string|HTMLElement} containerSelector 
 */
export function animateTileStats(containerSelector = '.tiles-grid, .tile_count') {
    if (!shouldAnimatePage('magkom_tile_stats_motion')) return;

    const container = typeof containerSelector === 'string' 
        ? document.querySelector(containerSelector) 
        : containerSelector;
    if (!container) return;

    const cards = container.querySelectorAll('.tile_stats_count');
    if (!cards.length) return;

    const tl = gsap.timeline({
        defaults: { ease: 'power2.out' }
    });

    // 1. Fade & Subtle Slide-Up pada Kartu Tile
    tl.fromTo(cards, 
        { opacity: 0, y: 8 }, 
        { opacity: 1, y: 0, duration: 0.45, stagger: 0.08 },
        0
    );

    // 2. Staggered Rise pada 9 Mini Bar Sparkline
    const sparkBars = container.querySelectorAll('.sa-spark i, .stat-spark i');
    if (sparkBars.length) {
        tl.fromTo(sparkBars,
            { scaleY: 0, opacity: 0.3 },
            { scaleY: 1, opacity: 0.85, duration: 0.4, stagger: 0.015, transformOrigin: 'bottom center' },
            0.15
        );
    }

    // 3. Smooth Numeric Counter Rolling pada Nilai Angka
    cards.forEach((card) => {
        const valEl = card.querySelector('.stat-value, .count');
        if (valEl) {
            const rawText = valEl.textContent.trim();
            const rawVal = parseInt(rawText, 10);
            if (!isNaN(rawVal) && rawVal > 0) {
                const counterObj = { val: 0 };
                valEl.textContent = '0';
                tl.to(counterObj, {
                    val: rawVal,
                    duration: 0.6,
                    ease: 'power2.out',
                    onUpdate: () => {
                        valEl.textContent = Math.round(counterObj.val);
                    }
                }, 0.1);
            }
        }
    });

    return tl;
}

/**
 * Animasikan Chart & Timeline pada Halaman Profil 360 Mentee (peserta/show)
 */
export function animateProfil360Charts() {
    if (!shouldAnimatePage('magkom_profil_360_motion')) return;

    const weeklyBars = document.querySelectorAll('.weekly-bars-container .weekly-bar-pill');
    const progressBar = document.querySelector('.kpi-progress-bar');
    const donutGraphic = document.querySelector('.donut-chart-graphic');

    if (!weeklyBars.length && !progressBar && !donutGraphic) return;

    const tl = gsap.timeline({
        defaults: { ease: 'power2.out' }
    });

    // 1. Progress Bar Horizontal Fill
    if (progressBar) {
        tl.fromTo(progressBar,
            { scaleX: 0 },
            { scaleX: 1, duration: 0.65, transformOrigin: 'left center' },
            0.05
        );
    }

    // 2. Donut Graphic Scale-In
    if (donutGraphic) {
        tl.fromTo(donutGraphic,
            { scale: 0.88, opacity: 0 },
            { scale: 1, opacity: 1, duration: 0.5 },
            0.15
        );
    }

    // 3. Weekly 12-Bars Timeline Rise Up
    if (weeklyBars.length) {
        tl.fromTo(weeklyBars,
            { scaleY: 0, opacity: 0.2 },
            { scaleY: 1, opacity: 1, duration: 0.55, stagger: 0.035, transformOrigin: 'bottom center' },
            0.2
        );
    }

    return tl;
}

/**
 * Helper Universal untuk Inisialisasi Halaman Pembimbing Standar
 */
export function initPembimbingPageMotion(pageType = 'standard') {
    if (pageType === 'profil-360') {
        animateProfil360Charts();
    } else {
        animateTileStats();
    }
}

if (typeof window !== 'undefined') {
    window.MagKomPembimbingMotion = {
        animateTileStats,
        animateProfil360Charts,
        initPembimbingPageMotion
    };
}

