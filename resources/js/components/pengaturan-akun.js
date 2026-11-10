/**
 * MAGKOM INDUSTRIAL DESIGN SYSTEM: PENGATURAN AKUN & PREFERENSI
 * Modular ES Module for Account & Settings Pages across all Roles
 */

export function initPengaturanAkun() {
    initPasswordToggles();
    initThemeSelection();
}

/**
 * Handle password show/hide eye toggle button
 */
function initPasswordToggles() {
    const toggleButtons = document.querySelectorAll('.toggle-eye-btn, [data-toggle-target]');

    toggleButtons.forEach((btn) => {
        // Prevent duplicate listener attachments
        if (btn.dataset.toggleInitialized === 'true') {
            return;
        }
        btn.dataset.toggleInitialized = 'true';

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = btn.getAttribute('data-toggle-target');
            if (!targetId) return;

            const input = document.getElementById(targetId);
            if (!input) return;

            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            btn.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Lihat kata sandi');

            // Toggle SVG icon between open eye and closed eye
            if (isPassword) {
                // Eye with slash (hide)
                btn.innerHTML = `
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                    </svg>
                `;
            } else {
                // Eye open (show)
                btn.innerHTML = `
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                `;
            }
        });
    });
}

/**
 * Handle theme radio cards visual selection synchronization
 */
function initThemeSelection() {
    const themeCards = document.querySelectorAll('.theme-preview-card');
    if (!themeCards.length) return;

    themeCards.forEach((card) => {
        const radio = card.querySelector('input[type="radio"]');

        // Sync initial state
        if (radio && radio.checked) {
            card.classList.add('selected');
        }

        card.addEventListener('click', () => {
            if (!radio) return;
            radio.checked = true;
            radio.dispatchEvent(new Event('change', { bubbles: true }));

            // Update UI class
            themeCards.forEach((c) => c.classList.remove('selected'));
            card.classList.add('selected');
        });

        if (radio) {
            radio.addEventListener('change', () => {
                if (radio.checked) {
                    themeCards.forEach((c) => c.classList.remove('selected'));
                    card.classList.add('selected');
                }
            });
        }
    });
}

// Auto-run if loaded directly in browser
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPengaturanAkun);
    } else {
        initPengaturanAkun();
    }
}

