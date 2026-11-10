/**
 * ==============================================================================
 * MagKom Sleman - Profil 360° Peserta Bimbingan GSAP Timeline Entrance Engine
 * Clean, lightweight, professional animated entrance for /pembimbing/peserta/{id}
 * ==============================================================================
 */

import { gsap } from 'gsap';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Accessibility Check: Hormati preferensi reduced-motion pengguna (WCAG 2.2)
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
        return; // Jangan jalankan animasi jika pengguna meminta reduced motion
    }

    const pageContainer = document.querySelector('.mentee-profile-page');
    if (!pageContainer) return;

    // 2. Inisialisasi Master Timeline
    const tl = gsap.timeline({
        defaults: {
            ease: 'power2.out',
            duration: 0.5
        }
    });

    // --------------------------------------------------------------------------
    // STAGE 1: Top Action & Date Bar (Slide down & fade)
    // --------------------------------------------------------------------------
    const topBar = pageContainer.querySelector('.mentee-date-action-bar');
    if (topBar) {
        tl.fromTo(topBar, 
            { opacity: 0, y: -16 }, 
            { opacity: 1, y: 0, duration: 0.45 },
            0
        );

        const datePill = topBar.querySelector('.date-pill-amber');
        const backBtn = topBar.querySelector('.btn-back-navy');
        if (datePill && backBtn) {
            tl.fromTo([datePill, backBtn],
                { scale: 0.94, opacity: 0 },
                { scale: 1, opacity: 1, duration: 0.35, stagger: 0.06 },
                0.1
            );
        }
    }

    // --------------------------------------------------------------------------
    // STAGE 2: Mentee Hero Card (Slide up, avatar pop & details reveal)
    // --------------------------------------------------------------------------
    const heroCard = pageContainer.querySelector('.mentee-hero-card');
    if (heroCard) {
        tl.fromTo(heroCard,
            { opacity: 0, y: 24 },
            { opacity: 1, y: 0, duration: 0.55, ease: 'power3.out' },
            0.08
        );

        const avatarRing = heroCard.querySelector('.mentee-avatar-gold-ring');
        if (avatarRing) {
            tl.fromTo(avatarRing,
                { opacity: 0, scale: 0.75, rotation: -10 },
                { opacity: 1, scale: 1, rotation: 0, duration: 0.5, ease: 'back.out(1.6)' },
                0.18
            );
        }

        const actionBtns = heroCard.querySelectorAll('.btn-pill-action');
        if (actionBtns.length) {
            tl.fromTo(actionBtns,
                { opacity: 0, y: 10, scale: 0.92 },
                { opacity: 1, y: 0, scale: 1, stagger: 0.08, duration: 0.35 },
                0.25
            );
        }

        const dataCells = heroCard.querySelectorAll('.data-peserta-cell');
        if (dataCells.length) {
            tl.fromTo(dataCells,
                { opacity: 0, x: 12 },
                { opacity: 1, x: 0, stagger: 0.04, duration: 0.35 },
                0.22
            );
        }
    }

    // --------------------------------------------------------------------------
    // STAGE 3: 3-Column KPI Summary Cards & Mini-Visuals
    // --------------------------------------------------------------------------
    const kpiCards = pageContainer.querySelectorAll('.mentee-kpi-summary-grid .kpi-card');
    if (kpiCards.length) {
        tl.fromTo(kpiCards,
            { opacity: 0, y: 28 },
            { opacity: 1, y: 0, stagger: 0.12, duration: 0.5, ease: 'power2.out' },
            0.28
        );

        // Donut Chart Pop & Rotate
        const donutCircle = pageContainer.querySelector('.donut-chart-circle');
        if (donutCircle) {
            tl.fromTo(donutCircle,
                { opacity: 0, scale: 0.75, rotation: -50 },
                { opacity: 1, scale: 1, rotation: 0, duration: 0.6, ease: 'back.out(1.4)' },
                0.38
            );
        }

        const donutLegends = pageContainer.querySelectorAll('.donut-legend-pill');
        if (donutLegends.length) {
            tl.fromTo(donutLegends,
                { opacity: 0, x: -10 },
                { opacity: 1, x: 0, stagger: 0.06, duration: 0.35 },
                0.42
            );
        }

        // Periode Magang Progress Fill
        const periodeFill = pageContainer.querySelector('.periode-progress-fill');
        if (periodeFill) {
            tl.fromTo(periodeFill,
                { scaleX: 0, transformOrigin: 'left center' },
                { scaleX: 1, duration: 0.7, ease: 'power2.out' },
                0.4
            );
        }

        // Logbook 12-Weeks Boxes Cascade
        const weekBoxes = pageContainer.querySelectorAll('.logbook-week-box');
        if (weekBoxes.length) {
            tl.fromTo(weekBoxes,
                { opacity: 0, scale: 0.7 },
                { opacity: 1, scale: 1, stagger: 0.025, duration: 0.3, ease: 'back.out(2)' },
                0.36
            );
        }
    }

    // --------------------------------------------------------------------------
    // STAGE 4: Riwayat Penugasan (Kanban Board & Task Cards)
    // --------------------------------------------------------------------------
    const kanbanCard = pageContainer.querySelector('.mentee-kanban-board-card');
    if (kanbanCard) {
        tl.fromTo(kanbanCard,
            { opacity: 0, y: 22 },
            { opacity: 1, y: 0, duration: 0.5 },
            0.48
        );

        const segmentFills = kanbanCard.querySelectorAll('.kanban-segmented-bar .segment-fill');
        if (segmentFills.length) {
            tl.fromTo(segmentFills,
                { scaleX: 0, transformOrigin: 'left center' },
                { scaleX: 1, duration: 0.6, stagger: 0.06, ease: 'power2.out' },
                0.55
            );
        }

        const kanbanCols = kanbanCard.querySelectorAll('.kanban-column');
        if (kanbanCols.length) {
            tl.fromTo(kanbanCols,
                { opacity: 0, y: 18 },
                { opacity: 1, y: 0, stagger: 0.09, duration: 0.45 },
                0.58
            );
        }

        const taskCards = kanbanCard.querySelectorAll('.kanban-task-card');
        if (taskCards.length) {
            tl.fromTo(taskCards,
                { opacity: 0, y: 12 },
                { opacity: 1, y: 0, stagger: 0.04, duration: 0.35 },
                0.65
            );
        }
    }
});

