/**
 * MagKom Peserta Layout Interactions (Vanilla JS)
 * Modularized for Vite & Strict CSP (script-src 'self')
 */

export function toggleSidebarPeserta(forceState) {
    if (typeof window !== 'undefined' && !/^\/peserta($|\/)/i.test(window.location.pathname)) {
        return;
    }
    const isMobile = window.innerWidth <= 1024;
    const sidebar = document.getElementById('sidebarNav');
    const backdrop = document.getElementById('drawerBackdrop');

    if (isMobile) {
        if (!sidebar) return;
        const isCurrentlyOpen = sidebar.classList.contains('open') || sidebar.classList.contains('drawer-open');
        const shouldOpen = typeof forceState === 'boolean' ? forceState : !isCurrentlyOpen;

        if (shouldOpen) {
            sidebar.classList.add('open', 'drawer-open');
            document.body.classList.add('drawer-open');
            if (backdrop) backdrop.classList.add('open', 'active');
            document.body.style.overflow = 'hidden';
        } else {
            sidebar.classList.remove('open', 'drawer-open');
            document.body.classList.remove('drawer-open');
            if (backdrop) backdrop.classList.remove('open', 'active');
            document.body.style.overflow = '';
        }
    } else {
        const html = document.documentElement;
        const isCurrentlyCollapsed = html.classList.contains('sidebar-collapsed');
        const shouldCollapse = typeof forceState === 'boolean' ? forceState : !isCurrentlyCollapsed;

        if (shouldCollapse) {
            html.classList.add('sidebar-collapsed');
        } else {
            html.classList.remove('sidebar-collapsed');
        }
        try {
            localStorage.setItem('magkom_peserta_sidebar_collapsed', shouldCollapse ? 'true' : 'false');
        } catch (e) {}
    }
}

export function notifyLockedFeature(featureName) {
    if (window.MagKomToast) {
        window.MagKomToast.show({
            type: 'warning',
            title: featureName + ' Masih Terkunci',
            message: 'Fitur ' + featureName + ' akan aktif secara otomatis setelah permohonan magang Anda disetujui & penempatan telah dimulai.',
            duration: 5000
        });
    } else {
        alert(featureName + ' masih terkunci. Fitur akan terbuka setelah permohonan magang Anda disetujui.');
    }
}

export function toggleProfileMenu(e) {
    if (e) e.stopPropagation();
    const btn = document.getElementById('userProfileBtn');
    const menu = document.getElementById('userProfileMenu');
    if (!btn || !menu) return;

    const isOpen = menu.classList.contains('open');
    if (isOpen) {
        closeProfileMenu();
    } else {
        menu.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
    }
}

export function closeProfileMenu() {
    const btn = document.getElementById('userProfileBtn');
    const menu = document.getElementById('userProfileMenu');
    if (menu && menu.classList.contains('open')) {
        menu.classList.remove('open');
        if (btn) btn.setAttribute('aria-expanded', 'false');
    }
}

if (typeof window !== 'undefined') {
    window.toggleSidebarPeserta = toggleSidebarPeserta;
    window.notifyLockedFeature = notifyLockedFeature;
    window.toggleProfileMenu = toggleProfileMenu;
    window.closeProfileMenu = closeProfileMenu;

    document.addEventListener('DOMContentLoaded', () => {
        // Hanya jalankan interaksi layout peserta jika rute saat ini adalah portal peserta
        if (!/^\/peserta($|\/)/i.test(window.location.pathname)) {
            // Bersihkan residu class sidebar-collapsed jika sebelumnya sempat tertempel
            document.documentElement.classList.remove('sidebar-collapsed');
            return;
        }

        document.addEventListener('click', (e) => {
            const toggleSidebar = e.target.closest('[data-action="toggle-sidebar-peserta"]');
            if (toggleSidebar) {
                e.preventDefault();
                toggleSidebarPeserta();
                return;
            }

            const closeSidebar = e.target.closest('[data-action="close-sidebar-peserta"]');
            if (closeSidebar) {
                e.preventDefault();
                toggleSidebarPeserta(false);
                return;
            }

            const lockedEl = e.target.closest('[data-locked-feature]');
            if (lockedEl) {
                e.preventDefault();
                notifyLockedFeature(lockedEl.getAttribute('data-locked-feature') || 'Fitur');
                return;
            }

            const profileMenuBtn = e.target.closest('[data-action="toggle-profile-menu"]');
            if (profileMenuBtn) {
                toggleProfileMenu(e);
                return;
            }

            const container = document.querySelector('.profile-dropdown-container');
            if (container && !container.contains(e.target)) {
                closeProfileMenu();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' || e.key === 'Esc') {
                closeProfileMenu();
                toggleSidebarPeserta(false);
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 1024) {
                const sidebar = document.getElementById('sidebarNav');
                const backdrop = document.getElementById('drawerBackdrop');
                if (sidebar) sidebar.classList.remove('open', 'drawer-open');
                if (backdrop) backdrop.classList.remove('open', 'active');
                document.body.classList.remove('drawer-open');
                document.body.style.overflow = '';
            }
        });
    });
}
