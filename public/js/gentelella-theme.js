/**
 * MAGKOM - GENTELELLA THEME JAVASCRIPT ENGINE (VANILLA JS)
 * Standar: Dinas Komunikasi dan Informatika Kabupaten Sleman
 * Bebas dependensi jQuery & Bootstrap legacy untuk performa maksimal.
 * Aksesibilitas WCAG 2.1 AA & Dukungan Responsif Multi-Platform.
 */

(function () {
    'use strict';

    /**
     * Toggle Mini-Sidebar (nav-sm) vs Full Sidebar (nav-md) pada Desktop,
     * serta Mobile Drawer pada viewport sempit (< 992px).
     */
    window.toggleGentelellaSidebar = function (forceState) {
        const body = document.body;
        const sidebar = document.getElementById('sidebarContainer');
        const backdrop = document.getElementById('sidebarBackdrop');
        const isMobile = window.innerWidth < 992;

        if (isMobile) {
            if (!sidebar) return;
            const isCurrentlyOpen = sidebar.classList.contains('drawer-open');
            const shouldOpen = typeof forceState === 'boolean' ? forceState : !isCurrentlyOpen;

            if (shouldOpen) {
                sidebar.classList.add('drawer-open');
                if (backdrop) backdrop.classList.add('active');
                body.style.overflow = 'hidden';
            } else {
                sidebar.classList.remove('drawer-open');
                if (backdrop) backdrop.classList.remove('active');
                body.style.overflow = '';
            }
        } else {
            // Desktop Mode: Toggle nav-sm (small 70px) vs nav-md (medium 230px)
            const isCurrentlySmall = body.classList.contains('nav-sm');
            const targetSmall = typeof forceState === 'boolean'
                ? forceState
                : !isCurrentlySmall;

            if (targetSmall) {
                body.classList.remove('nav-md');
                body.classList.add('nav-sm');
                try {
                    localStorage.setItem('magkom_sidebar_state', 'sm');
                    localStorage.setItem('sidebar_state', 'sm');
                } catch (e) {}
            } else {
                body.classList.remove('nav-sm');
                body.classList.add('nav-md');
                try {
                    localStorage.setItem('magkom_sidebar_state', 'md');
                    localStorage.setItem('sidebar_state', 'md');
                } catch (e) {}
            }
        }
    };

    /**
     * Collapse / Expand interaktivitas x_panel Gentelella
     */
    window.toggleGentelellaPanel = function (btn) {
        if (!btn) return;
        const panel = btn.closest('.x_panel');
        if (!panel) return;

        const content = panel.querySelector('.x_content');
        const chevron = btn.querySelector('.panel_chevron, svg');

        if (!content) return;

        const isCollapsed = panel.classList.contains('panel-collapsed');

        if (isCollapsed) {
            panel.classList.remove('panel-collapsed');
            content.style.display = '';
            if (chevron) {
                chevron.style.transform = 'rotate(0deg)';
            }
            btn.setAttribute('aria-expanded', 'true');
        } else {
            panel.classList.add('panel-collapsed');
            content.style.display = 'none';
            if (chevron) {
                chevron.style.transform = 'rotate(180deg)';
            }
            btn.setAttribute('aria-expanded', 'false');
        }
    };

    /**
     * Tutup / Sembunyikan x_panel Gentelella dengan animasi fade-out
     */
    window.closeGentelellaPanel = function (btn) {
        if (!btn) return;
        const panel = btn.closest('.x_panel');
        if (!panel) return;

        panel.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
        panel.style.opacity = '0';
        panel.style.transform = 'scale(0.98)';
        setTimeout(() => {
            panel.style.display = 'none';
        }, 250);
    };

    // Inisialisasi preferensi sidebar & event listeners saat DOM siap
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Pulihkan Preferensi Sidebar Desktop
        if (window.innerWidth >= 992) {
            try {
                const savedState = localStorage.getItem('magkom_sidebar_state') || localStorage.getItem('sidebar_state');
                if (savedState === 'sm') {
                    document.body.classList.remove('nav-md');
                    document.body.classList.add('nav-sm');
                } else {
                    document.body.classList.add('nav-md');
                }
            } catch (e) {
                document.body.classList.add('nav-md');
            }
        } else {
            document.body.classList.add('nav-md');
        }

        // 2. Event Delegation untuk Interaksi Panel & Sidebar
        document.addEventListener('click', function (e) {
            // Collapse panel button
            const collapseBtn = e.target.closest('.collapse-link');
            if (collapseBtn) {
                e.preventDefault();
                window.toggleGentelellaPanel(collapseBtn);
                return;
            }

            // Close panel button
            const closeBtn = e.target.closest('.close-link');
            if (closeBtn) {
                e.preventDefault();
                window.closeGentelellaPanel(closeBtn);
                return;
            }

            // Mobile Backdrop scrim click
            if (e.target.id === 'sidebarBackdrop') {
                window.toggleGentelellaSidebar(false);
                return;
            }
        });

        // 3. Aksesibilitas Keyboard WCAG: Tutup drawer dengan tombol ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                const sidebar = document.getElementById('sidebarContainer');
                if (sidebar && sidebar.classList.contains('drawer-open')) {
                    window.toggleGentelellaSidebar(false);
                }
            }
        });

        // 4. Listener Resize untuk Normalisasi Tampilan saat Rotasi Layar / Resize
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) {
                const sidebar = document.getElementById('sidebarContainer');
                const backdrop = document.getElementById('sidebarBackdrop');
                if (sidebar) sidebar.classList.remove('drawer-open');
                if (backdrop) backdrop.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
})();
