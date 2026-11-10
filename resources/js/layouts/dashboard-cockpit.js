/**
 * MagKom Sleman - Unified Dashboard Cockpit Interactive Engine
 * Pure Vanilla JavaScript (ES6+) - Zero External Dependencies - CSP Compliant
 */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // 1. Slide-Over Drawer & Modal Management
    function getBackdrop() {
        let backdrop = document.querySelector('.cv-drawer-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'cv-drawer-backdrop';
            backdrop.setAttribute('data-action', 'close-drawer');
            document.body.appendChild(backdrop);
        }
        return backdrop;
    }

    function openDrawer(drawerSelector, triggerEl) {
        const drawer = document.querySelector(drawerSelector);
        if (!drawer) return;

        const backdrop = getBackdrop();

        // Populate dynamic data if provided on trigger
        if (triggerEl) {
            const mappings = [
                { attr: 'data-nama', target: '.cv-dyn-nama' },
                { attr: 'data-nomor', target: '.cv-dyn-nomor' },
                { attr: 'data-institusi', target: '.cv-dyn-institusi' },
                { attr: 'data-jurusan', target: '.cv-dyn-jurusan' },
                { attr: 'data-status', target: '.cv-dyn-status' },
                { attr: 'data-url', target: '.cv-dyn-url', isLink: true },
                { attr: 'data-kegiatan', target: '.cv-dyn-kegiatan' },
                { attr: 'data-tanggal', target: '.cv-dyn-tanggal' },
                { attr: 'data-user', target: '.cv-dyn-user' },
                { attr: 'data-ip', target: '.cv-dyn-ip' },
                { attr: 'data-action-name', target: '.cv-dyn-action' },
                { attr: 'data-waktu', target: '.cv-dyn-waktu' }
            ];

            mappings.forEach(m => {
                const val = triggerEl.getAttribute(m.attr);
                if (val !== null) {
                    const el = drawer.querySelector(m.target);
                    if (el) {
                        if (m.isLink) {
                            el.setAttribute('href', val);
                        } else {
                            el.textContent = val;
                        }
                    }
                }
            });
        }

        backdrop.classList.add('active');
        drawer.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeAllDrawers() {
        document.querySelectorAll('.cv-slide-drawer.active').forEach(d => {
            d.classList.remove('active');
        });
        const backdrop = document.querySelector('.cv-drawer-backdrop');
        if (backdrop) {
            backdrop.classList.remove('active');
        }
        document.body.style.overflow = '';
    }

    // 2. Global Event Delegation
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('[data-action]');
        if (!trigger) return;

        const action = trigger.getAttribute('data-action');

        if (action === 'open-drawer') {
            e.preventDefault();
            const target = trigger.getAttribute('data-drawer');
            if (target) openDrawer(target, trigger);
        } else if (action === 'close-drawer') {
            e.preventDefault();
            closeAllDrawers();
        } else if (action === 'toggle-mobile-aside') {
            e.preventDefault();
            const aside = document.querySelector('.cv-aside-sticky, .saas-cockpit-aside, aside.saas-cockpit-aside');
            if (aside) {
                aside.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });

    // Expose drawer helper globally
    window.openMagkomDrawer = function(selector) {
        openDrawer(selector);
    };
    window.closeMagkomDrawer = function() {
        closeAllDrawers();
    };

    // 3. Keyboard Escape to Close Drawers/Modals
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            closeAllDrawers();
        }
    });

    // 4. Live Table Search (Debounced 200ms)
    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    document.querySelectorAll('[data-table-search]').forEach(input => {
        const targetTableSelector = input.getAttribute('data-table-search');
        const targetTable = document.querySelector(targetTableSelector);
        if (!targetTable) return;

        const handleSearch = debounce(function () {
            const query = input.value.toLowerCase().trim();
            const rows = targetTable.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (!query || text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }, 200);

        input.addEventListener('input', handleSearch);
    });

    // 5. Table Filter Pills (Unobtrusive)
    document.querySelectorAll('[data-filter-target]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const targetTableSelector = this.getAttribute('data-filter-target');
            const filterValue = this.getAttribute('data-filter-value');
            const targetTable = document.querySelector(targetTableSelector);
            if (!targetTable) return;

            // Update active state among siblings
            const parent = this.parentElement;
            if (parent) {
                parent.querySelectorAll('[data-filter-target]').forEach(sibling => {
                    sibling.classList.remove('active');
                });
            }
            this.classList.add('active');

            // Filter rows
            const rows = targetTable.querySelectorAll('tbody tr');
            rows.forEach(row => {
                if (filterValue === 'all') {
                    row.style.display = '';
                } else {
                    const rowStatus = row.getAttribute('data-status') || '';
                    if (rowStatus === filterValue || row.classList.contains('status-' + filterValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    });
});
