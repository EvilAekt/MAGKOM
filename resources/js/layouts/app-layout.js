/**
 * MagKom App Layout Interactions (Public & Global Header/Navigation)
 * Fully compliant with strict CSP (script-src 'self') via event delegation.
 */
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
        const captchaTrigger = e.target.closest('#captcha-img, [data-captcha-img], #btn-refresh-captcha, [data-action="refresh-captcha"]');
        if (captchaTrigger) {
            const img = document.getElementById('captcha-img') || document.querySelector('[data-captcha-img]');
            const btn = document.getElementById('btn-refresh-captcha');
            const input = document.getElementById('captcha');
            if (btn) {
                btn.classList.add('spinning');
                setTimeout(() => btn.classList.remove('spinning'), 500);
            }
            if (img) {
                img.src = '/captcha/image?t=' + Date.now();
            }
            if (input) {
                input.value = '';
                input.focus();
            }
            return;
        }

        const devFillCaptcha = e.target.closest('[data-dev-fill-captcha]');
        if (devFillCaptcha) {
            const input = document.getElementById('captcha');
            if (input) {
                input.value = devFillCaptcha.getAttribute('data-dev-fill-captcha') || '1234';
            }
            return;
        }

        const passwordToggleBtn = e.target.closest('.toggle-password-btn');
        if (passwordToggleBtn) {
            const targetId = passwordToggleBtn.getAttribute('data-target') || passwordToggleBtn.getAttribute('data-toggle-target');
            const input = document.getElementById(targetId);
            if (input) {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                const eyeOpen = passwordToggleBtn.querySelector('.eye-open');
                const eyeClosed = passwordToggleBtn.querySelector('.eye-closed');
                if (eyeOpen && eyeClosed) {
                    eyeOpen.style.display = isPassword ? 'none' : 'block';
                    eyeClosed.style.display = isPassword ? 'block' : 'none';
                }
                passwordToggleBtn.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Lihat kata sandi');
            }
            return;
        }

        const userMenuBtn = e.target.closest('#landingUserMenuBtn');
        if (userMenuBtn) {
            e.stopPropagation();
            const menu = document.getElementById('landingUserDropdown');
            if (menu) {
                const isOpen = menu.classList.contains('show');
                if (isOpen) {
                    menu.classList.remove('show');
                    userMenuBtn.setAttribute('aria-expanded', 'false');
                } else {
                    menu.classList.add('show');
                    userMenuBtn.setAttribute('aria-expanded', 'true');
                }
            }
            return;
        }

        const toggleDrawerBtn = e.target.closest('[data-action="toggle-app-drawer"]');
        if (toggleDrawerBtn) {
            const drawer = document.getElementById('mobile-drawer');
            const backdrop = document.getElementById('appDrawerBackdrop');
            if (drawer) {
                const isOpen = drawer.classList.toggle('open');
                toggleDrawerBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                if (backdrop) backdrop.classList.toggle('open', isOpen);
            }
            return;
        }

        const closeDrawerBtn = e.target.closest('[data-action="close-app-drawer"]');
        if (closeDrawerBtn) {
            const drawer = document.getElementById('mobile-drawer');
            const backdrop = document.getElementById('appDrawerBackdrop');
            const toggleBtn = document.getElementById('appMobileToggle');
            if (drawer) {
                drawer.classList.remove('open');
                if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
                if (backdrop) backdrop.classList.remove('open');
            }
            return;
        }

        // Close user dropdown if clicking outside
        const wrap = document.getElementById('landingUserMenuWrap');
        const menu = document.getElementById('landingUserDropdown');
        const menuBtn = document.getElementById('landingUserMenuBtn');
        if (menu && menu.classList.contains('show') && wrap && !wrap.contains(e.target)) {
            menu.classList.remove('show');
            if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const menu = document.getElementById('landingUserDropdown');
            const btn = document.getElementById('landingUserMenuBtn');
            if (menu && menu.classList.contains('show')) {
                menu.classList.remove('show');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }

            const drawer = document.getElementById('mobile-drawer');
            const backdrop = document.getElementById('appDrawerBackdrop');
            const toggleBtn = document.getElementById('appMobileToggle');
            if (drawer && drawer.classList.contains('open')) {
                drawer.classList.remove('open');
                if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
                if (backdrop) backdrop.classList.remove('open');
            }
        }
    });
});
