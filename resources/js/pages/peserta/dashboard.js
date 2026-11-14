/**
 * ==============================================================================
 * MagKom Sleman - Dashboard Peserta JavaScript (Bento Grid ES Module)
 * Resources: resources/js/pages/peserta/dashboard.js
 * Universal Dynamic Calendar Engine with Popover Month/Year Selection
 * ==============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    initBentoCalendar();
    initQuickLogbookForm();
});

/**
 * Inisialisasi Interaktivitas Mini Kalender Bento (Universal Dynamic Calendar Engine)
 */
function initBentoCalendar() {
    const calendarCard = document.getElementById('bentoCalendarCard');
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

    if (!calendarCard || !calendarGrid || !monthTitle) return;

    let eventDates = {};
    try {
        const raw = calendarCard.getAttribute('data-event-dates');
        if (raw) eventDates = JSON.parse(raw);
    } catch {
        eventDates = {};
    }

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

            const padMonth = String(viewMonth + 1).padStart(2, '0');
            const padDate = String(d).padStart(2, '0');
            const dateKey = `${viewYear}-${padMonth}-${padDate}`;
            const hasEvent = Boolean(eventDates && eventDates[dateKey]);

            const btn = document.createElement('button');
            btn.type = 'button';

            const classList = ['cal-date'];
            if (isWeekend) classList.push('date-weekend');
            if (isToday) classList.push('date-today');
            if (isSelected) classList.push('date-selected');
            if (hasEvent) classList.push('has-event');

            btn.className = classList.join(' ');
            btn.setAttribute('data-date', d);
            btn.setAttribute('aria-label', `Tanggal ${d} ${MONTH_NAMES[viewMonth]} ${viewYear}` + (isToday ? ' (Hari ini)' : '') + (hasEvent ? ' (Ada Jadwal/Kegiatan)' : ''));
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
        if (yearVal) {
            yearVal.textContent = viewYear;
        }
        renderGrid();
    }

    // Event listener navigasi panah kiri/kanan
    prevBtn?.addEventListener('click', () => {
        viewMonth--;
        if (viewMonth < 0) {
            viewMonth = 11;
            viewYear--;
        }
        closeMonthPopover();
        closeYearPopover();
        updateCalendar();
    });

    nextBtn?.addEventListener('click', () => {
        viewMonth++;
        if (viewMonth > 11) {
            viewMonth = 0;
            viewYear++;
        }
        closeMonthPopover();
        closeYearPopover();
        updateCalendar();
    });

    // Event listener popover
    monthTriggerBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleMonthPopover();
    });

    yearTriggerBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleYearPopover();
    });

    yearDecBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        popoverBaseYear -= 5;
        renderYearPopover();
    });

    yearIncBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        popoverBaseYear += 5;
        renderYearPopover();
    });

    // Menutup popover saat mengklik di luar area
    document.addEventListener('click', (e) => {
        if (monthPopover && !monthPopover.hidden && !monthPopover.contains(e.target) && e.target !== monthTriggerBtn) {
            closeMonthPopover();
        }
        if (yearPopover && !yearPopover.hidden && !yearPopover.contains(e.target) && e.target !== yearTriggerBtn) {
            closeYearPopover();
        }
    });

    // Keyboard ESC untuk menutup popovers
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeMonthPopover();
            closeYearPopover();
        }
    });

    // Inisialisasi awal
    updateCalendar();
}

/**
 * Inisialisasi Pengisian Logbook Kilat via Slide-Over Drawer
 */
function initQuickLogbookForm() {
    const form = document.getElementById('quickLogbookForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]') || document.querySelector('button[form="quickLogbookForm"]');
        const originalText = submitBtn ? submitBtn.innerHTML : '';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...';
        }

        try {
            const formData = new FormData(form);
            const response = await window.axios.post(form.action, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Accept': 'application/json'
                }
            });

            if (response.data && response.data.success) {
                if (window.MagKomToast) {
                    window.MagKomToast.success(response.data.message || 'Logbook harian berhasil disimpan!');
                }

                // Tutup drawer secara aman
                const closeBtn = document.querySelector('#drawerQuickLogbook [data-action="close-drawer"]');
                if (closeBtn) {
                    closeBtn.click();
                }

                form.reset();

                // Refresh halus untuk merefleksikan metrik baru
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            }
        } catch (error) {
            let errorMsg = 'Terjadi kesalahan saat menyimpan logbook.';

            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                if (errors) {
                    errorMsg = Object.values(errors).flat().join('<br>');
                } else if (error.response.data.message) {
                    errorMsg = error.response.data.message;
                }
            } else if (error.response && error.response.data && error.response.data.message) {
                errorMsg = error.response.data.message;
            }

            if (window.MagKomToast) {
                window.MagKomToast.error(errorMsg);
            } else {
                alert(errorMsg);
            }
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }
    });
}
