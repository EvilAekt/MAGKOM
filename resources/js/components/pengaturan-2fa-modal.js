/**
 * Modul Pengaturan 2FA Modal Handler (Event Delegation & Keyboard A11y)
 * MAGKOM Industrial Design System
 */

export function initPengaturan2faModal() {
    // 1. Delegasi Event Klik untuk Buka / Tutup Modal
    document.addEventListener('click', (e) => {
        const target = e.target;
        if (!target) return;

        // Buka Modal Nonaktifkan 2FA
        const openDisableBtn = target.closest('[data-action="open-modal-2fa-disable"]');
        if (openDisableBtn) {
            e.preventDefault();
            const modal = document.getElementById('modal2faDisable');
            if (modal) {
                modal.removeAttribute('hidden');
                const input = modal.querySelector('input[name="current_password"]');
                if (input) input.focus();
            }
            return;
        }

        // Buka Modal Regenerasi Recovery Codes
        const openRecoveryBtn = target.closest('[data-action="open-modal-2fa-recovery"]');
        if (openRecoveryBtn) {
            e.preventDefault();
            const modal = document.getElementById('modal2faRecovery');
            if (modal) {
                modal.removeAttribute('hidden');
                const input = modal.querySelector('input[name="current_password"]');
                if (input) input.focus();
            }
            return;
        }

        // Tutup Modal via Tombol Close / Batal
        const closeBtn = target.closest('[data-action="close-modal-2fa"]');
        if (closeBtn) {
            e.preventDefault();
            const modal = closeBtn.closest('.modal-2fa-backdrop');
            if (modal) {
                closeModal(modal);
            }
            return;
        }

        // Tutup Modal jika Mengklik Backdrop Luar Dialog
        if (target.classList.contains('modal-2fa-backdrop')) {
            closeModal(target);
        }
    });

    // 2. Keyboard Accessibility: Tekan 'Escape' untuk Menutup Modal Aktif
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' || e.key === 'Esc') {
            const activeModal = document.querySelector('.modal-2fa-backdrop:not([hidden])');
            if (activeModal) {
                closeModal(activeModal);
            }
        }
    });

    function closeModal(modal) {
        modal.setAttribute('hidden', '');
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}
