/**
 * Pembimbing Tugas Template Index Module
 * CSP Compliant (no inline scripts or inline event handlers)
 */

document.addEventListener('DOMContentLoaded', () => {
    function openModalTambahTemplate() {
        const modal = document.getElementById('modalTambahTemplate');
        if (modal) modal.style.display = 'flex';
    }

    function closeModalTambahTemplate() {
        const modal = document.getElementById('modalTambahTemplate');
        if (modal) modal.style.display = 'none';
    }

    document.addEventListener('click', (e) => {
        const target = e.target;
        if (target.closest('[data-action="open-modal-tambah-template"]')) {
            e.preventDefault();
            openModalTambahTemplate();
        } else if (target.closest('[data-action="close-modal-tambah-template"]')) {
            e.preventDefault();
            closeModalTambahTemplate();
        }
    });

    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form.hasAttribute('data-confirm')) {
            if (!confirm(form.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModalTambahTemplate();
        }
    });
});
