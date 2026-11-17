/**
 * MagKom Anti-Flash Theme Initializer
 * Executed synchronously in <head> to prevent FOUC (Flash of Unstyled Content)
 * Fully compliant with strict Content-Security-Policy (script-src 'self')
 */
(function() {
    try {
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
            return null;
        }

        // Cek apakah halaman saat ini merupakan portal dashboard yang diizinkan (Peserta, Pembimbing, Admin, SuperAdmin)
        const isDashboard = document.documentElement.getAttribute('data-portal') === 'dashboard' ||
            /^\/(peserta|pembimbing|admin|superadmin)($|\/)/i.test(window.location.pathname);

        if (!isDashboard) {
            document.documentElement.setAttribute('data-theme', 'light');
            document.documentElement.setAttribute('data-pref-theme', 'terang');
        } else {
            const cookieTheme = getCookie('tema_tampilan') || 'terang';
            const savedPref = localStorage.getItem('magkom_theme') || cookieTheme;
            let effectiveTheme = 'light';

            if (savedPref === 'sistem') {
                effectiveTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            } else if (savedPref === 'gelap') {
                effectiveTheme = 'dark';
            } else {
                effectiveTheme = 'light';
            }

            document.documentElement.setAttribute('data-theme', effectiveTheme);
            document.documentElement.setAttribute('data-pref-theme', savedPref);

            // Anti-flicker untuk state sidebar collapsed di desktop
            const savedSidebar = localStorage.getItem('magkom_peserta_sidebar_collapsed');
            if (savedSidebar === 'true' && window.innerWidth > 1024) {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        }
    } catch (e) {}

    // Global listener for history back & window print actions
    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('[data-action="history-back"]')) {
            window.history.back();
        } else if (e.target && e.target.closest('[data-action="window-print"]')) {
            window.print();
        }
    });
})();
