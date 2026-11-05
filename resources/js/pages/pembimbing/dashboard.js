/**
 * ==============================================================================
 * MagKom Sleman - Dashboard Pembimbing (Zenius Modern ES Module)
 * Resources: resources/js/pages/pembimbing/dashboard.js
 * ==============================================================================
 */

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

    // Simpan ke localStorage untuk persistensi tab aktif
    try {
        localStorage.setItem('magkom_pembimbing_active_tab', tabId);
    } catch (e) {
        // Silently ignore storage quota/privacy issues
    }
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

// Global window exposure for backward-compatibility with partials
if (typeof window !== 'undefined') {
    window.switchPembimbingTab = switchPembimbingTab;
    window.filterDashboardTasks = filterDashboardTasks;
    window.filterTableByInput = filterTableByInput;
}

// Inisialisasi Event Listener saat DOM siap
document.addEventListener('DOMContentLoaded', () => {
    // 1. Delegasi Event untuk Tab Switching
    document.addEventListener('click', (event) => {
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

    // 2. Realtime Table Search Input Listener
    document.addEventListener('input', (event) => {
        const tableFilterInput = event.target.closest('[data-table-filter-target]');
        if (tableFilterInput) {
            const tableId = tableFilterInput.getAttribute('data-table-filter-target');
            if (tableId) {
                filterTableByInput(tableFilterInput, tableId);
            }
        }
    });

    // 3. Auto-restore active tab from URL hash or localStorage
    const hash = window.location.hash.replace('#', '');
    let savedTab = null;
    try {
        savedTab = localStorage.getItem('magkom_pembimbing_active_tab');
    } catch (e) {}

    const tabToActivate = hash || savedTab;
    if (tabToActivate && document.getElementById(tabToActivate)) {
        switchPembimbingTab(tabToActivate);
    }
});

