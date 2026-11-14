/**
 * MagKom Dashboard Layout Interactions (Vanilla JS)
 * Modularized for Vite & CSP Compliance
 */

export function toggleSidebar(forceState) {
    if (typeof window.toggleGentelellaSidebar === 'function' && window.toggleGentelellaSidebar !== toggleSidebar) {
        window.toggleGentelellaSidebar(forceState);
        return;
    }

    const body = document.body;
    const sidebar = document.getElementById('mainSidebar') || document.getElementById('sidebarNav') || document.querySelector('.dashboard-sidebar');
    const backdrop = document.getElementById('sidebarBackdrop') || document.getElementById('drawerBackdrop');
    const isMobile = window.innerWidth <= 1024;

    if (isMobile) {
        if (!sidebar) return;
        const isCurrentlyOpen = sidebar.classList.contains('drawer-open') || sidebar.classList.contains('open');
        const shouldOpen = typeof forceState === 'boolean'
            ? forceState
            : !isCurrentlyOpen;

        if (shouldOpen) {
            sidebar.classList.add('drawer-open', 'open');
            body.classList.add('drawer-open');
            if (backdrop) backdrop.classList.add('active', 'open');
            body.style.overflow = 'hidden';
        } else {
            sidebar.classList.remove('drawer-open', 'open');
            body.classList.remove('drawer-open');
            if (backdrop) backdrop.classList.remove('active', 'open');
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
            closeActiveFlyout();
            closeActiveTooltip();
            try {
                localStorage.setItem('magkom_sidebar_state', 'sm');
                localStorage.setItem('sidebar_state', 'sm');
            } catch (e) {}
        } else {
            body.classList.remove('nav-sm');
            body.classList.add('nav-md');
            closeActiveFlyout();
            closeActiveTooltip();
            try {
                localStorage.setItem('magkom_sidebar_state', 'md');
                localStorage.setItem('sidebar_state', 'md');
            } catch (e) {}
        }
    }
}

let activeFlyout = null;
let activeTooltip = null;

export function closeActiveFlyout() {
    if (activeFlyout && activeFlyout.parentNode) {
        activeFlyout.remove();
        activeFlyout = null;
    }
    document.querySelectorAll('.nav-group-btn.flyout-open').forEach(b => {
        b.classList.remove('flyout-open');
    });
}

export function closeActiveTooltip() {
    if (activeTooltip && activeTooltip.parentNode) {
        activeTooltip.remove();
        activeTooltip = null;
    }
}

export function triggerNavSmFlyout(btn) {
    if (!btn) return;
    const isAlreadyOpen = btn.classList.contains('flyout-open');

    closeActiveFlyout();
    closeActiveTooltip();

    if (isAlreadyOpen) {
        return;
    }

    const container = btn.closest('.nav-group-container');
    const subMenu = container ? container.querySelector('.nav-sub-menu') : null;
    if (!subMenu) return;

    const labelSpan = btn.querySelector('.nav-group-btn-left span:not(.nav-item-icon)');
    const groupTitle = labelSpan ? labelSpan.textContent.trim() : 'Menu';

    const rect = btn.getBoundingClientRect();
    const flyout = document.createElement('div');
    flyout.className = 'nav-sm-flyout-portal';
    flyout.setAttribute('role', 'menu');
    flyout.setAttribute('aria-label', groupTitle);

    // Header Grup Flyout
    const header = document.createElement('div');
    header.className = 'nav-sm-flyout-header';
    header.textContent = groupTitle;
    flyout.appendChild(header);

    // Daftar link submenu
    const links = subMenu.querySelectorAll('.nav-sub-item');
    links.forEach(link => {
        const a = document.createElement('a');
        a.href = link.href;
        a.className = 'nav-sm-flyout-item' + (link.classList.contains('active') ? ' active' : '');
        a.textContent = link.textContent.trim();
        a.setAttribute('role', 'menuitem');
        a.addEventListener('click', () => {
            closeActiveFlyout();
        });
        flyout.appendChild(a);
    });

    document.body.appendChild(flyout);

    // Hitung posisi vertikal agar tidak terpotong tepi layar
    const flyoutHeight = flyout.offsetHeight;
    const windowHeight = window.innerHeight;
    let topPos = rect.top;

    if (topPos + flyoutHeight > windowHeight - 16) {
        topPos = Math.max(16, windowHeight - flyoutHeight - 16);
    }

    flyout.style.left = '72px';
    flyout.style.top = topPos + 'px';

    btn.classList.add('flyout-open');
    activeFlyout = flyout;
}

export function toggleNavGroup(btn) {
    if (!btn) return;
    // Buka flyout melayang jika dalam mode mini (nav-sm) pada desktop
    if (document.body.classList.contains('nav-sm') && window.innerWidth >= 992) {
        triggerNavSmFlyout(btn);
        return;
    }

    const isExpanded = btn.getAttribute('aria-expanded') === 'true';
    const subMenu = btn.nextElementSibling;
    if (subMenu) {
        if (isExpanded) {
            btn.setAttribute('aria-expanded', 'false');
            subMenu.classList.remove('show');
        } else {
            btn.setAttribute('aria-expanded', 'true');
            subMenu.classList.add('show');
        }
    }
}

export function toggleUserMenu() {
    const menu = document.getElementById('userDropdown');
    const btn = document.getElementById('userMenuBtn');
    if (!menu || !btn) return;

    const isExpanded = menu.classList.contains('show');
    if (isExpanded) {
        menu.classList.remove('show');
        btn.setAttribute('aria-expanded', 'false');
    } else {
        menu.classList.add('show');
        btn.setAttribute('aria-expanded', 'true');
    }
}

export function applyPortalTheme(theme) {
    let effective = 'light';
    if (theme === 'sistem') {
        effective = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    } else if (theme === 'gelap') {
        effective = 'dark';
    } else {
        effective = 'light';
    }
    document.documentElement.setAttribute('data-theme', effective);
    document.documentElement.setAttribute('data-pref-theme', theme);
    const toggleBtn = document.getElementById('sidebarThemeToggleBtn');
    if (toggleBtn) {
        const isDark = effective === 'dark';
        toggleBtn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        const titleText = isDark ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap';
        toggleBtn.setAttribute('title', titleText);
        toggleBtn.setAttribute('aria-label', titleText);
    }
    try {
        localStorage.setItem('magkom_theme', theme);
    } catch (e) {}
}

export function togglePortalThemeQuick() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const nextTheme = currentTheme === 'dark' ? 'terang' : 'gelap';
    applyPortalTheme(nextTheme);

    const preferensiUrl = document.body.getAttribute('data-preferensi-url') || '';
    if (preferensiUrl && preferensiUrl !== '#') {
        fetch(preferensiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ tema: nextTheme })
        }).catch(() => {});
    }
}

// Global exposure for backward-compatibility with inline Blade directives
if (typeof window !== 'undefined') {
    window.toggleSidebar = toggleSidebar;
    if (typeof window.toggleGentelellaSidebar !== 'function') {
        window.toggleGentelellaSidebar = toggleSidebar;
    }
    window.toggleNavGroup = toggleNavGroup;
    window.triggerNavSmFlyout = triggerNavSmFlyout;
    window.closeActiveFlyout = closeActiveFlyout;
    window.closeActiveTooltip = closeActiveTooltip;
    window.toggleUserMenu = toggleUserMenu;
    window.applyPortalTheme = applyPortalTheme;
    window.togglePortalThemeQuick = togglePortalThemeQuick;

    document.addEventListener('click', function(e) {
        // Tutup flyout melayang jika klik di luar
        if (activeFlyout && !e.target.closest('.nav-sm-flyout-portal') && !e.target.closest('.nav-group-btn')) {
            closeActiveFlyout();
        }

        const menu = document.getElementById('userDropdown');
        const btn = document.getElementById('userMenuBtn');
        if (menu && btn && !btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.remove('show');
            btn.setAttribute('aria-expanded', 'false');
        }

        // Close drawer button
        const closeBtn = e.target.closest('.sidebar-drawer-close-btn, [data-action="close-sidebar"]');
        if (closeBtn) {
            e.preventDefault();
            toggleSidebar(false);
            return;
        }

        // Mobile Backdrop scrim click
        if (e.target.id === 'sidebarBackdrop' || e.target.id === 'drawerBackdrop' || e.target.classList.contains('mobile-drawer-backdrop') || e.target.classList.contains('drawer-backdrop')) {
            toggleSidebar(false);
            return;
        }

        // Delegated data-action listeners (removes need for inline onclick)
        const actionTarget = e.target.closest('[data-action]');
        if (actionTarget) {
            const action = actionTarget.getAttribute('data-action');
            if (action === 'toggle-sidebar') {
                toggleSidebar();
            } else if (action === 'toggle-user-menu') {
                toggleUserMenu();
            } else if (action === 'toggle-theme-quick') {
                togglePortalThemeQuick();
            }
        }

        // Mobile menu link click auto-close (except for submenu group headers)
        if (window.innerWidth <= 1024) {
            const navLink = e.target.closest('.nav-item-link, .nav-sub-item');
            if (navLink && !navLink.classList.contains('nav-group-btn')) {
                toggleSidebar(false);
            }
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            closeActiveFlyout();
            closeActiveTooltip();
            toggleSidebar(false);
            const menu = document.getElementById('userDropdown');
            const btn = document.getElementById('userMenuBtn');
            if (menu && btn) {
                menu.classList.remove('show');
                btn.setAttribute('aria-expanded', 'false');
            }
        }
    });

    // Tooltip pada Single Nav Item saat mode kecil (nav-sm)
    document.addEventListener('mouseover', function(e) {
        const isNavSm = document.body.classList.contains('nav-sm') && window.innerWidth >= 992;
        if (!isNavSm) {
            closeActiveTooltip();
            return;
        }

        const navLink = e.target.closest('.nav-item-link');
        if (navLink && !navLink.closest('.nav-group-container')) {
            const spanText = navLink.querySelector('span:not(.nav-item-icon)');
            if (!spanText) return;

            const text = spanText.textContent.trim();
            if (!text) return;

            closeActiveTooltip();

            const rect = navLink.getBoundingClientRect();
            const tooltip = document.createElement('div');
            tooltip.className = 'nav-sm-tooltip';
            tooltip.textContent = text;
            document.body.appendChild(tooltip);

            tooltip.style.left = '72px';
            tooltip.style.top = (rect.top + (rect.height - tooltip.offsetHeight) / 2) + 'px';
            activeTooltip = tooltip;
        }
    });

    document.addEventListener('mouseout', function(e) {
        const navLink = e.target.closest('.nav-item-link');
        if (navLink) {
            closeActiveTooltip();
        }
    });

    window.addEventListener('scroll', function() {
        closeActiveFlyout();
        closeActiveTooltip();
    }, { passive: true });

    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            const sidebar = document.getElementById('mainSidebar') || document.getElementById('sidebarNav') || document.querySelector('.dashboard-sidebar');
            const backdrop = document.getElementById('sidebarBackdrop') || document.getElementById('drawerBackdrop');
            if (sidebar) sidebar.classList.remove('drawer-open', 'open');
            if (backdrop) backdrop.classList.remove('active', 'open');
            document.body.classList.remove('drawer-open');
            document.body.style.overflow = '';
        }
    });

    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            const currentPref = document.documentElement.getAttribute('data-pref-theme') || localStorage.getItem('magkom_theme');
            if (currentPref === 'sistem') {
                const effective = e.matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', effective);
                const toggleBtn = document.getElementById('sidebarThemeToggleBtn');
                if (toggleBtn) {
                    const isDark = effective === 'dark';
                    toggleBtn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                    const titleText = isDark ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap';
                    toggleBtn.setAttribute('title', titleText);
                    toggleBtn.setAttribute('aria-label', titleText);
                }
            }
        });
    }
}

