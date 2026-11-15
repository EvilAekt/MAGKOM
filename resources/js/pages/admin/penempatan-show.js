/**
 * Admin Penempatan Show Module
 * CSP Compliant (no inline scripts or inline event handlers)
 */

document.addEventListener('DOMContentLoaded', () => {
    function openPindahModal() {
        const modal = document.getElementById('modal-pindah-pembimbing');
        if (modal) modal.style.display = 'flex';
    }

    function closePindahModal() {
        const modal = document.getElementById('modal-pindah-pembimbing');
        if (modal) modal.style.display = 'none';
    }

    function openBatalModal() {
        const modal = document.getElementById('modal-batalkan-penempatan');
        if (modal) modal.style.display = 'flex';
    }

    function closeBatalModal() {
        const modal = document.getElementById('modal-batalkan-penempatan');
        if (modal) modal.style.display = 'none';
    }

    function togglePenempatanDropdown(e) {
        if (e) e.stopPropagation();
        const menu = document.getElementById('menuPenempatanDropdown');
        const trigger = document.getElementById('btnPenempatanDropdown');
        if (menu) {
            menu.classList.toggle('show');
            if (trigger) {
                trigger.classList.toggle('is-active', menu.classList.contains('show'));
            }
        }
    }

    function closePenempatanDropdown() {
        const menu = document.getElementById('menuPenempatanDropdown');
        const trigger = document.getElementById('btnPenempatanDropdown');
        if (menu) menu.classList.remove('show');
        if (trigger) trigger.classList.remove('is-active');
    }

    document.addEventListener('click', (e) => {
        const target = e.target;

        const dropdownBtn = target.closest('#btnPenempatanDropdown');
        if (dropdownBtn) {
            e.preventDefault();
            togglePenempatanDropdown(e);
            return;
        }

        const openPindahBtn = target.closest('[data-action="open-pindah-modal"]');
        if (openPindahBtn) {
            e.preventDefault();
            closePenempatanDropdown();
            openPindahModal();
            return;
        }

        const closePindahBtn = target.closest('[data-action="close-pindah-modal"]');
        if (closePindahBtn) {
            e.preventDefault();
            closePindahModal();
            return;
        }

        const openBatalBtn = target.closest('[data-action="open-batal-modal"]');
        if (openBatalBtn) {
            e.preventDefault();
            closePenempatanDropdown();
            openBatalModal();
            return;
        }

        const closeBatalBtn = target.closest('[data-action="close-batal-modal"]');
        if (closeBatalBtn) {
            e.preventDefault();
            closeBatalModal();
            return;
        }

        // Click outside dropdown
        const wrap = document.querySelector('.penempatan-dropdown-wrap');
        if (wrap && !wrap.contains(target)) {
            closePenempatanDropdown();
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closePindahModal();
            closeBatalModal();
            closePenempatanDropdown();
        }
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) {
                closePindahModal();
                closeBatalModal();
            }
        });
    });
});
