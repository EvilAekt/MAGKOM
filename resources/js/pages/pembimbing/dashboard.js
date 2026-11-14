/**
 * ==============================================================================
 * MagKom Sleman - Dashboard Pembimbing (Bento Grid ES Module)
 * Resources: resources/js/pages/pembimbing/dashboard.js
 * ==============================================================================
 */

import gsap from 'gsap';
import { initScrollCraft } from '../../services/scroll-craft.js';

export function switchPembimbingTab(tabId) {
    if (!tabId) return;

    // Sembunyikan semua tab pane
    const panes = document.querySelectorAll('.tab-pane');
    panes.forEach((el) => {
        el.classList.remove('active');
    });

    // Nonaktifkan semua tombol tab
    const tabBtns = document.querySelectorAll('.pembimbing-tab-btn, [data-tab]');
    tabBtns.forEach((el) => {
        if (el.getAttribute('data-tab') === tabId || el.id === 'btn-' + tabId) {
            el.classList.add('active');
        } else {
            el.classList.remove('active');
        }
    });

    // Aktifkan pane yang dituju
    const targetPane = document.getElementById(tabId);
    if (targetPane) {
        targetPane.classList.add('active');
    }

    try {
        localStorage.setItem('magkom_pembimbing_active_tab', tabId);
    } catch (e) {}
}

export function filterDashboardTasks(filterType, btnEl) {
    const filterBtns = document.querySelectorAll('.task-filter-btn, [data-task-filter]');
    filterBtns.forEach((b) => {
        b.classList.remove('active');
    });

    if (btnEl) {
        btnEl.classList.add('active');
    }

    const rows = document.querySelectorAll('.task-item-row');
    let visibleCount = 0;

    rows.forEach((row) => {
        const isUrgent = row.getAttribute('data-is-urgent') === '1';
        const hasPending = row.getAttribute('data-has-pending') === '1';

        if (filterType === 'semua') {
            row.style.display = 'flex';
            visibleCount++;
        } else if (filterType === 'mendesak') {
            if (isUrgent) {
                row.style.display = 'flex';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        } else if (filterType === 'reviu' || filterType === 'review') {
            if (hasPending) {
                row.style.display = 'flex';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        }
    });

    const emptyBox = document.getElementById('task-filter-empty-state');
    if (emptyBox) {
        emptyBox.style.display = (visibleCount === 0) ? 'block' : 'none';
    }
}

export function filterTableByInput(inputEl, tableId) {
    if (!inputEl) return;
    const filter = inputEl.value.toLowerCase().trim();
    const table = document.getElementById(tableId);
    if (!table) return;
    const tbody = table.getElementsByTagName('tbody')[0];
    if (!tbody) return;
    const trs = tbody.getElementsByTagName('tr');

    for (let i = 0; i < trs.length; i++) {
        const rowText = trs[i].textContent.toLowerCase();
        if (rowText.indexOf(filter) > -1) {
            trs[i].style.display = '';
        } else {
            trs[i].style.display = 'none';
        }
    }
}

/**
 * Inisialisasi Interaktivitas Mini Kalender Bento (Universal Dynamic Calendar Engine)
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

    // Acuan hari ini riil dari sistem
    const realToday = new Date();
    const realYear = realToday.getFullYear();
    const realMonth = realToday.getMonth();
    const realDate = realToday.getDate();

    // State kalender yang sedang dilihat
    let viewYear = realYear;
    let viewMonth = realMonth;
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
            const isToday = (isCurrentMonthView && d === realDate);
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
                    btn.classList.remove('date-selected', 'sa-date-pop');
                } else {
                    selectedDate = { year: viewYear, month: viewMonth, date: d };
                    calendarGrid.querySelectorAll('.cal-date').forEach((el) => {
                        el.classList.remove('date-selected', 'sa-date-pop');
                    });
                    btn.classList.add('date-selected', 'sa-date-pop');
                }
            });

            btn.addEventListener('dblclick', (e) => {
                e.preventDefault();
                selectedDate = null;
                calendarGrid.querySelectorAll('.cal-date').forEach((el) => {
                    el.classList.remove('date-selected', 'sa-date-pop');
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
 * Hanya berjalan 1 kali saat initial load (Fresh Page Entrance).
 */
function initGsapChartTimeline() {
    // 1. Cek prefers-reduced-motion (Aksesibilitas WCAG 2.1 AA)
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    // 2. Proteksi Initial Load Only (Cegah re-trigger saat interaksi in-page)
    const kuotaCard = document.querySelector('.bento-kuota-card');
    if (!kuotaCard || kuotaCard.classList.contains('sc-instant-reveal')) return;

    // 3. Persiapan Elemen Target
    const bars = document.querySelectorAll('.bento-distribusi-chart-box .bento-bar');
    const barValues = document.querySelectorAll('.bento-distribusi-chart-box .bento-bar-val');
    const gaugeFill = document.querySelector('.bento-kuota-gauge-box .gauge-fill');
    const percentEl = document.querySelector('.gauge-center-percent');
    const rosterDashes = document.querySelectorAll('.roster-segments-bar .dash-filled-navy, .roster-segments-bar .dash-filled-white');
    const matrixDots = document.querySelectorAll('.bento-dot-matrix .dot-filled');

    // Jika komponen utama tidak ditemukan di DOM, batalkan
    if (!bars.length && !gaugeFill) return;

    // 4. Inisialisasi GSAP Master Timeline
    const masterTl = gsap.timeline({
        defaults: {
            ease: "power2.out"
        }
    });

    // --------------------------------------------------------------------------
    // A. Bar Chart Distribusi Tugas: Tumbuh Vertikal dari Dasar
    // --------------------------------------------------------------------------
    if (bars.length) {
        masterTl.fromTo(bars,
            {
                scaleY: 0,
                opacity: 0.25
            },
            {
                scaleY: 1,
                opacity: 1,
                duration: 0.75,
                stagger: 0.09,
                transformOrigin: "bottom center"
            },
            0
        );

        // Nilai badge teks angka di dalam bar: Muncul halus saat bar hampir puncak
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
                    stagger: 0.09
                },
                0.35
            );
        }
    }

    // --------------------------------------------------------------------------
    // B. Semi-Circle Radial Gauge & Rolling Percentage Counter
    // --------------------------------------------------------------------------
    if (gaugeFill) {
        const targetOffsetAttr = gaugeFill.getAttribute('stroke-dashoffset');
        const targetOffset = targetOffsetAttr !== null ? parseFloat(targetOffsetAttr) : 0;
        const gaugePerimeter = 188.5; // Keliling setengah lingkaran (PI * 60)

        // Sapuan busur SVG
        masterTl.fromTo(gaugeFill,
            {
                strokeDashoffset: gaugePerimeter
            },
            {
                strokeDashoffset: targetOffset,
                duration: 1.05,
                ease: "power2.out"
            },
            0.1
        );

        // Rolling Counter Angka Persentase (misal dari 0% ke 65%)
        if (percentEl) {
            const rawText = percentEl.textContent.replace('%', '').trim();
            const targetVal = parseInt(rawText, 10);
            if (!isNaN(targetVal)) {
                const counterObj = { val: 0 };
                percentEl.textContent = '0%';

                masterTl.to(counterObj, {
                    val: targetVal,
                    duration: 1.05,
                    ease: "power2.out",
                    onUpdate: () => {
                        percentEl.textContent = `${Math.round(counterObj.val)}%`;
                    }
                }, 0.1);
            }
        }
    }

    // --------------------------------------------------------------------------
    // C. Highlight Roster Segmented Dashes (Wave Entrance)
    // --------------------------------------------------------------------------
    if (rosterDashes.length) {
        masterTl.fromTo(rosterDashes,
            {
                scale: 0.7,
                opacity: 0.2
            },
            {
                scale: 1,
                opacity: 1,
                duration: 0.3,
                stagger: 0.025
            },
            0.25
        );
    }

    // --------------------------------------------------------------------------
    // D. KPI Dot Matrix (Subtle Entrance)
    // --------------------------------------------------------------------------
    if (matrixDots.length) {
        masterTl.fromTo(matrixDots,
            {
                scale: 0.6,
                opacity: 0.2
            },
            {
                scale: 1,
                opacity: 1,
                duration: 0.25,
                stagger: 0.012
            },
            0.35
        );
    }
}

// Global window exposure for backward-compatibility with partials
if (typeof window !== 'undefined') {
    window.switchPembimbingTab = switchPembimbingTab;
    window.filterDashboardTasks = filterDashboardTasks;
    window.filterTableByInput = filterTableByInput;
    window.initGsapChartTimeline = initGsapChartTimeline;
}

// State untuk quick review logbook di drawer
let currentReviewUrl = '';
let currentReviewRow = null;

async function handleQuickApproveLogbook(buttonEl) {
    if (!currentReviewUrl) return;

    const originalHtml = buttonEl.innerHTML;
    buttonEl.disabled = true;
    buttonEl.innerHTML = 'Menyetujui...';

    try {
        const response = await window.axios.post(currentReviewUrl, {
            status: 'disetujui'
        }, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.data && response.data.success) {
            if (window.MagKomToast) {
                window.MagKomToast.success('Logbook mentee berhasil disetujui!');
            }

            // Tutup drawer
            const closeBtn = document.querySelector('#drawerReviewLogbook [data-action="close-drawer"]');
            if (closeBtn) closeBtn.click();

            // Hapus baris dari antrean dengan transisi halus
            if (currentReviewRow) {
                currentReviewRow.style.transition = 'opacity 0.3s ease';
                currentReviewRow.style.opacity = '0';
                setTimeout(() => {
                    currentReviewRow.remove();
                }, 300);
            }
        }
    } catch (error) {
        let errorMsg = 'Gagal menyetujui logbook.';
        if (error.response && error.response.data && error.response.data.message) {
            errorMsg = error.response.data.message;
        }
        if (window.MagKomToast) {
            window.MagKomToast.error(errorMsg);
        } else {
            alert(errorMsg);
        }
    } finally {
        buttonEl.disabled = false;
        buttonEl.innerHTML = originalHtml;
    }
}

async function handleSelesaikanMentee(buttonEl) {
    const nama = buttonEl.getAttribute('data-nama') || 'Mentee';
    const url = buttonEl.getAttribute('data-url');
    if (!url) return;

    const confirmed = window.confirm(`Apakah Anda yakin ingin menyelesaikan masa bimbingan magang untuk "${nama}"?\n\nPenetapan Selesai 1-Klik akan mengaktifkan masa tenggang akun (2 bulan) dan membuka akses penerbitan sertifikat.`);
    if (!confirmed) return;

    const originalText = buttonEl.innerHTML;
    buttonEl.disabled = true;
    buttonEl.innerHTML = 'Memproses...';

    try {
        const response = await window.axios.post(url, {}, {
            headers: { 'Accept': 'application/json' }
        });

        if (response.data && response.data.success) {
            if (window.MagKomToast) {
                window.MagKomToast.success(response.data.message || 'Status bimbingan mentee resmi diselesaikan!');
            }
            setTimeout(() => {
                window.location.reload();
            }, 800);
        }
    } catch (error) {
        let errorMsg = 'Gagal menyelesaikan bimbingan mentee.';
        if (error.response && error.response.data && error.response.data.message) {
            errorMsg = error.response.data.message;
        }
        if (window.MagKomToast) {
            window.MagKomToast.error(errorMsg);
        } else {
            alert(errorMsg);
        }
    } finally {
        buttonEl.disabled = false;
        buttonEl.innerHTML = originalText;
    }
}

// Inisialisasi saat DOM siap
document.addEventListener('DOMContentLoaded', () => {
    initScrollCraft();
    initGsapChartTimeline();
    initBentoCalendar();

    // Restore tab aktif dari hash URL atau localStorage
    const hash = window.location.hash.replace('#', '');
    const savedTab = hash || (typeof localStorage !== 'undefined' ? localStorage.getItem('magkom_pembimbing_active_tab') : null);
    if (savedTab && document.getElementById(savedTab)) {
        switchPembimbingTab(savedTab);
    }

    // Delegasi Event untuk Tab Switching, Filter & Aksi Dashboard
    document.addEventListener('click', (event) => {
        // Tangkap trigger pembuka drawer review logbook
        const reviewTrigger = event.target.closest('[data-drawer="#drawerReviewLogbook"]');
        if (reviewTrigger) {
            currentReviewUrl = reviewTrigger.getAttribute('data-review-action') || '';
            currentReviewRow = reviewTrigger.closest('tr');
        }

        // Quick Approve Logbook dari Drawer
        const approveBtn = event.target.closest('[data-action="quick-approve-logbook"]');
        if (approveBtn && currentReviewUrl) {
            event.preventDefault();
            handleQuickApproveLogbook(approveBtn);
            return;
        }

        // Selesaikan Bimbingan Mentee (1-Klik BR-10)
        const selesaikanBtn = event.target.closest('[data-action="selesaikan-mentee"]');
        if (selesaikanBtn) {
            event.preventDefault();
            handleSelesaikanMentee(selesaikanBtn);
            return;
        }

        const tabTrigger = event.target.closest('[data-tab]');
        if (tabTrigger) {
            const tabId = tabTrigger.getAttribute('data-tab');
            if (tabId) {
                switchPembimbingTab(tabId);
            }
            return;
        }

        const taskFilterTrigger = event.target.closest('[data-task-filter]');
        if (taskFilterTrigger) {
            const filterType = taskFilterTrigger.getAttribute('data-task-filter');
            filterDashboardTasks(filterType, taskFilterTrigger);
        }
    });

    // Realtime Table Search Input Listener
    document.addEventListener('input', (event) => {
        const tableFilterInput = event.target.closest('[data-table-filter-target]');
        if (tableFilterInput) {
            const tableId = tableFilterInput.getAttribute('data-table-filter-target');
            if (tableId) {
                filterTableByInput(tableFilterInput, tableId);
            }
        }
    });
});
