/**
 * ==============================================================================
 * MagKom Sleman - Dashboard SuperAdmin (Bento Grid ES Module)
 * Resources: resources/js/pages/superadmin/dashboard.js
 * Features: Scroll-Craft Viewport Reveal, GSAP Micro-Animations, Dynamic Bento Calendar
 * ==============================================================================
 */

import gsap from 'gsap';
import { initScrollCraft } from '../../services/scroll-craft.js';

/**
 * Inisialisasi Switch Kran Pendaftaran (Interaktif)
 */
function initKranToggle() {
    const kranToggle = document.getElementById('sa-toggle-kran');
    if (!kranToggle) return;

    kranToggle.addEventListener('click', () => {
        const isChecked = kranToggle.getAttribute('aria-checked') === 'true';
        const newChecked = !isChecked;
        kranToggle.setAttribute('aria-checked', String(newChecked));
        const knob = kranToggle.querySelector('.sa-switch-knob');
        if (knob) {
            if (newChecked) {
                knob.style.transform = 'translateX(0)';
                kranToggle.style.backgroundColor = 'var(--navy-800, #0A2A6A)';
            } else {
                knob.style.transform = 'translateX(-16px)';
                kranToggle.style.backgroundColor = 'var(--track, #E6EAF4)';
            }
        }
    });
}

/**
 * Inisialisasi Interaktivitas Mini Kalender Bento (Identik dengan Kalender Pembimbing)
 * Menampilkan Popover Bulan (12 Bulan), Popover Tahun (Jump 6 Tahun),
 * Sel Kapsul Semi-Oval 11px, Seleksi Tanggal & Weekend Aksen.
 */
function initBentoCalendar() {
    const calendarGrid = document.getElementById('bentoCalendarGrid');
    const monthTitle = document.getElementById('calMonthTitle');
    const monthTriggerBtn = document.getElementById('calMonthTriggerBtn');
    const monthPopover = document.getElementById('calMonthPopover');
    const monthGrid = document.getElementById('calMonthGrid');
    const yearVal = document.getElementById('calYearVal');
    const yearTriggerBtn = document.getElementById('calYearTriggerBtn');
    const yearPopover = document.getElementById('calYearPopover');
    const yearGrid = document.getElementById('calYearGrid');
    const yearDecBtn = document.getElementById('calYearDecBtn');
    const yearIncBtn = document.getElementById('calYearIncBtn');
    const prevBtn = document.getElementById('calPrevBtn');
    const nextBtn = document.getElementById('calNextBtn');

    if (!calendarGrid || !monthTitle) return;

    const MONTH_NAMES = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    const MONTH_SHORT_NAMES = [
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
    ];

    // Acuan tanggal hari ini dari sistem
    const realToday = new Date();
    const realYear = realToday.getFullYear();
    const realMonth = realToday.getMonth();
    const realDate = realToday.getDate();

    // Default tampilan acuan: November 2026 (atau tahun berjalan jika di masa depan)
    let viewYear = realYear >= 2026 ? realYear : 2026;
    let viewMonth = realYear >= 2026 ? realMonth : 10; // 10 = November (0-based)
    let popoverBaseYear = viewYear;

    // State tanggal yang diklik user: { year, month, date }
    let selectedDate = null;

    function renderGrid() {
        calendarGrid.innerHTML = '';
        const isCurrentMonthView = (viewYear === realYear && viewMonth === realMonth);

        // Hari pertama bulan (0: Minggu, 1: Senin, ..., 6: Sabtu)
        const firstDay = new Date(viewYear, viewMonth, 1).getDay();
        // Konversi ke ISO offset (Senin = 0, Minggu = 6)
        const startOffset = (firstDay === 0) ? 6 : (firstDay - 1);

        const daysInCurrentMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
        const daysInPrevMonth = new Date(viewYear, viewMonth, 0).getDate();

        const fragment = document.createDocumentFragment();

        // 1. Trailing days dari bulan sebelumnya
        for (let p = startOffset; p > 0; p--) {
            const span = document.createElement('span');
            span.className = 'cal-date date-muted';
            span.tabIndex = -1;
            span.textContent = daysInPrevMonth - p + 1;
            fragment.appendChild(span);
        }

        // 2. Active days bulan ini
        for (let d = 1; d <= daysInCurrentMonth; d++) {
            const dayCol = (startOffset + d - 1) % 7;
            const isWeekend = (dayCol === 5 || dayCol === 6);
            const isToday = (isCurrentMonthView && d === realDate) || (!isCurrentMonthView && viewMonth === 10 && viewYear === 2026 && d === 9);
            const isSelected = selectedDate && (selectedDate.year === viewYear && selectedDate.month === viewMonth && selectedDate.date === d);

            const btn = document.createElement('button');
            btn.type = 'button';

            const classList = ['cal-date'];
            if (isWeekend) classList.push('date-weekend');
            if (isToday) classList.push('date-today');
            if (isSelected) classList.push('date-selected');

            btn.className = classList.join(' ');
            btn.setAttribute('data-date', d);
            btn.setAttribute('aria-label', `Tanggal ${d} ${MONTH_NAMES[viewMonth]} ${viewYear}` + (isToday ? ' (Hari ini)' : ''));
            btn.textContent = d;

            btn.addEventListener('click', () => {
                const isAlreadySelected = selectedDate &&
                    selectedDate.year === viewYear &&
                    selectedDate.month === viewMonth &&
                    selectedDate.date === d;

                if (isAlreadySelected) {
                    selectedDate = null;
                    btn.classList.remove('date-selected');
                } else {
                    selectedDate = { year: viewYear, month: viewMonth, date: d };
                    calendarGrid.querySelectorAll('.cal-date').forEach((el) => {
                        el.classList.remove('date-selected');
                    });
                    btn.classList.add('date-selected');
                }
            });

            btn.addEventListener('dblclick', (e) => {
                e.preventDefault();
                selectedDate = null;
                calendarGrid.querySelectorAll('.cal-date').forEach((el) => {
                    el.classList.remove('date-selected');
                });
            });

            fragment.appendChild(btn);
        }

        // 3. Leading days bulan berikutnya (genapkan baris ke kelipatan 7)
        const totalCells = startOffset + daysInCurrentMonth;
        const nextFillers = (7 - (totalCells % 7)) % 7;
        for (let n = 1; n <= nextFillers; n++) {
            const span = document.createElement('span');
            span.className = 'cal-date date-muted';
            span.tabIndex = -1;
            span.textContent = n;
            fragment.appendChild(span);
        }

        calendarGrid.appendChild(fragment);
    }

    function renderYearPopover() {
        if (!yearGrid) return;
        yearGrid.innerHTML = '';
        const startYear = popoverBaseYear - 2;
        const endYear = popoverBaseYear + 3; // 6 pilihan tahun (2x3 grid)

        for (let y = startYear; y <= endYear; y++) {
            const yBtn = document.createElement('button');
            yBtn.type = 'button';
            yBtn.className = 'cal-year-option-btn' + (y === viewYear ? ' active' : '');
            yBtn.textContent = y;
            yBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                viewYear = y;
                closeYearPopover();
                updateCalendar();
            });
            yearGrid.appendChild(yBtn);
        }
    }

    function closeYearPopover() {
        if (yearPopover) {
            yearPopover.hidden = true;
        }
        if (yearTriggerBtn) {
            yearTriggerBtn.setAttribute('aria-expanded', 'false');
        }
    }

    function renderMonthPopover() {
        if (!monthGrid) return;
        monthGrid.innerHTML = '';

        for (let m = 0; m < 12; m++) {
            const mBtn = document.createElement('button');
            mBtn.type = 'button';
            mBtn.className = 'cal-month-option-btn' + (m === viewMonth ? ' active' : '');
            mBtn.textContent = MONTH_SHORT_NAMES[m];
            mBtn.setAttribute('data-month', m);
            mBtn.setAttribute('title', MONTH_NAMES[m]);
            mBtn.setAttribute('aria-label', `Pilih Bulan ${MONTH_NAMES[m]}`);
            mBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                viewMonth = m;
                closeMonthPopover();
                updateCalendar();
            });
            monthGrid.appendChild(mBtn);
        }
    }

    function closeMonthPopover() {
        if (monthPopover) {
            monthPopover.hidden = true;
        }
        if (monthTriggerBtn) {
            monthTriggerBtn.setAttribute('aria-expanded', 'false');
        }
    }

    function toggleMonthPopover() {
        if (!monthPopover) return;
        const isHidden = monthPopover.hidden;
        if (isHidden) {
            closeYearPopover();
            renderMonthPopover();
            monthPopover.hidden = false;
            if (monthTriggerBtn) monthTriggerBtn.setAttribute('aria-expanded', 'true');
        } else {
            closeMonthPopover();
        }
    }

    function toggleYearPopover() {
        if (!yearPopover) return;
        const isHidden = yearPopover.hidden;
        if (isHidden) {
            closeMonthPopover();
            popoverBaseYear = viewYear;
            renderYearPopover();
            yearPopover.hidden = false;
            if (yearTriggerBtn) yearTriggerBtn.setAttribute('aria-expanded', 'true');
        } else {
            closeYearPopover();
        }
    }

    function updateCalendar() {
        monthTitle.textContent = MONTH_NAMES[viewMonth];
        if (yearVal) yearVal.textContent = viewYear;
        renderGrid();
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            viewMonth--;
            if (viewMonth < 0) {
                viewMonth = 11;
                viewYear--;
            }
            updateCalendar();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            viewMonth++;
            if (viewMonth > 11) {
                viewMonth = 0;
                viewYear++;
            }
            updateCalendar();
        });
    }

    if (monthTriggerBtn) {
        monthTriggerBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMonthPopover();
        });
    }

    if (yearTriggerBtn) {
        yearTriggerBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleYearPopover();
        });
    }

    if (yearDecBtn) {
        yearDecBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            popoverBaseYear -= 6;
            renderYearPopover();
        });
    }

    if (yearIncBtn) {
        yearIncBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            popoverBaseYear += 6;
            renderYearPopover();
        });
    }

    document.addEventListener('click', (e) => {
        if (monthPopover && !monthPopover.hidden) {
            if (!monthPopover.contains(e.target) && (!monthTriggerBtn || !monthTriggerBtn.contains(e.target))) {
                closeMonthPopover();
            }
        }
        if (yearPopover && !yearPopover.hidden) {
            if (!yearPopover.contains(e.target) && (!yearTriggerBtn || !yearTriggerBtn.contains(e.target))) {
                closeYearPopover();
            }
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' || e.keyCode === 27) {
            closeMonthPopover();
            closeYearPopover();
        }
    });

    updateCalendar();
}

/**
 * Inisialisasi Orkestrasi Animasi Chart Menggunakan GSAP Timeline
 * Berjalan saat initial load (Fresh Page Entrance).
 */
function initGsapChartTimeline() {
    // 1. Cek prefers-reduced-motion (Aksesibilitas WCAG 2.1 AA)
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    // 2. Persiapan Elemen Target
    const bars = document.querySelectorAll('.sa-antrean-chart .sa-striped-bar');
    const barValues = document.querySelectorAll('.sa-antrean-chart .sa-antrean-val');
    const gaugeFill = document.querySelector('.sa-gauge-fill');
    const percentEl = document.querySelector('.sa-gauge-percent');
    const dots = document.querySelectorAll('.sa-dots-row .sa-dot');

    if (!bars.length && !gaugeFill) return;

    // 3. Inisialisasi GSAP Master Timeline
    const masterTl = gsap.timeline({
        defaults: {
            ease: "power2.out"
        }
    });

    // A. Bar Chart Status Antrean: Tumbuh Vertikal dari Dasar
    if (bars.length) {
        masterTl.fromTo(bars,
            {
                scaleY: 0,
                opacity: 0.2
            },
            {
                scaleY: 1,
                opacity: 1,
                duration: 0.75,
                stagger: 0.08,
                transformOrigin: "bottom center"
            },
            0.1
        );

        if (barValues.length) {
            masterTl.fromTo(barValues,
                {
                    opacity: 0,
                    y: 6
                },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.35,
                    stagger: 0.08
                },
                0.4
            );
        }
    }

    // B. Semi-Circle Radial Gauge & Rolling Percentage Counter
    if (gaugeFill) {
        const targetOffset = 157.08; // 50% dari 314.16
        const gaugePerimeter = 314.16;

        masterTl.fromTo(gaugeFill,
            {
                strokeDashoffset: gaugePerimeter
            },
            {
                strokeDashoffset: targetOffset,
                duration: 1.1,
                ease: "power2.out"
            },
            0.15
        );

        if (percentEl) {
            const counterObj = { val: 0 };
            percentEl.textContent = '0%';

            masterTl.to(counterObj, {
                val: 50,
                duration: 1.1,
                ease: "power2.out",
                onUpdate: () => {
                    percentEl.textContent = `${Math.round(counterObj.val)}%`;
                }
            }, 0.15);
        }
    }

    // C. Dot Matrix Indicator Entrance
    if (dots.length) {
        masterTl.fromTo(dots,
            {
                scale: 0.6,
                opacity: 0.2
            },
            {
                scale: 1,
                opacity: 1,
                duration: 0.25,
                stagger: 0.015
            },
            0.3
        );
    }
}

// Global window exposure
if (typeof window !== 'undefined') {
    window.initBentoCalendar = initBentoCalendar;
    window.initGsapChartTimeline = initGsapChartTimeline;
}

// Inisialisasi saat DOM siap
document.addEventListener('DOMContentLoaded', () => {
    initScrollCraft();
    initGsapChartTimeline();
    initBentoCalendar();
    initKranToggle();
});
