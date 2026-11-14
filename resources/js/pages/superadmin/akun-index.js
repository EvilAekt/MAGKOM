/**
 * Modul Pengaturan Akun & Keamanan SuperAdmin
 * - Toggle visibilitas kata sandi via data-toggle-target
 * - Pemilihan tema tampilan portal via event delegation
 * - Sistem Animasi Universal (Scroll-Craft + GSAP Paritas Pembimbing)
 */
import { initSuperAdminPageMotion } from '../../services/superadmin-motion.js';

export function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (!input || !btn) return;

    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    if (isPassword) {
        btn.innerHTML = `
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
            </svg>
        `;
        btn.setAttribute('aria-label', 'Sembunyikan kata sandi');
    } else {
        btn.innerHTML = `
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
        `;
        btn.setAttribute('aria-label', 'Lihat kata sandi');
    }
}

export function handleThemeSelection(card, themeValue) {
    if (!card) return;
    document.querySelectorAll('.theme-card-option').forEach(el => el.classList.remove('selected'));
    card.classList.add('selected');
    const radio = card.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;

    if (typeof window.applyPortalTheme === 'function') {
        window.applyPortalTheme(themeValue);
    }
}

// Global backward compatibility
window.togglePasswordVisibility = togglePasswordVisibility;
window.handleThemeSelection = handleThemeSelection;

document.addEventListener('DOMContentLoaded', () => {
    initSuperAdminPageMotion();

    // Event delegation untuk toggle password dan theme cards
    document.addEventListener('click', (event) => {
        const toggleBtn = event.target.closest('.toggle-password-btn');
        if (toggleBtn) {
            const fieldId = toggleBtn.dataset.toggleTarget;
            if (fieldId) {
                event.preventDefault();
                togglePasswordVisibility(fieldId, toggleBtn);
                return;
            }
        }

        const themeCard = event.target.closest('.theme-card-option');
        if (themeCard) {
            const themeVal = themeCard.dataset.themeVal;
            if (themeVal) {
                handleThemeSelection(themeCard, themeVal);
            }
        }
    });

    try {
        const activeThemeContainer = document.querySelector('[data-active-theme]');
        const fallbackTheme = activeThemeContainer ? activeThemeContainer.dataset.activeTheme : 'terang';
        const currentSavedTheme = localStorage.getItem('magkom_theme') || fallbackTheme;
        const targetCard = document.querySelector(`.theme-card-option[data-theme-val="${currentSavedTheme}"]`);
        if (targetCard) {
            document.querySelectorAll('.theme-card-option').forEach(el => el.classList.remove('selected'));
            targetCard.classList.add('selected');
            const radio = targetCard.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        }
    } catch (e) {}
});

