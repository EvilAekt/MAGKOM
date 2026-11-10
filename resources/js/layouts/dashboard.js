/**
 * MagKom Dashboard Layout Interactions (Vanilla JS)
 * Modularized for Vite & CSP Compliance
 */

export function toggleSidebar(forceState) {
    const sidebar = document.getElementById('mainSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (!sidebar || !backdrop) return;

    const shouldOpen = typeof forceState === 'boolean'
        ? forceState
        : !sidebar.classList.contains('drawer-open');

    if (shouldOpen) {
        sidebar.classList.add('drawer-open');
        backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
    } else {
        sidebar.classList.remove('drawer-open');
        backdrop.classList.remove('active');
        document.body.style.overflow = '';
    }
}

export function toggleNavGroup(btn) {
    if (!btn) return;
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
    window.toggleNavGroup = toggleNavGroup;
    window.toggleUserMenu = toggleUserMenu;
    window.applyPortalTheme = applyPortalTheme;
    window.togglePortalThemeQuick = togglePortalThemeQuick;

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('userDropdown');
        const btn = document.getElementById('userMenuBtn');
        if (menu && btn && !btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.remove('show');
            btn.setAttribute('aria-expanded', 'false');
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
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            toggleSidebar(false);
            const menu = document.getElementById('userDropdown');
            const btn = document.getElementById('userMenuBtn');
            if (menu && btn) {
                menu.classList.remove('show');
                btn.setAttribute('aria-expanded', 'false');
            }
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

