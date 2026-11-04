/**
 * MAGKOM: Agenda Mentoring Module (ES Modules)
 * Handles client-side modal operations without inline onclick attributes
 */

document.addEventListener('DOMContentLoaded', () => {
    const createModal = document.getElementById('createModal');
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    const editStatus = document.getElementById('editStatus');
    const editNotulensi = document.getElementById('editNotulensi');

    // Open create modal
    const btnOpenCreate = document.getElementById('btnOpenCreateAgenda');
    if (btnOpenCreate && createModal) {
        btnOpenCreate.addEventListener('click', (e) => {
            e.preventDefault();
            createModal.style.display = 'flex';
        });
    }

    // Modal close buttons
    document.querySelectorAll('[data-modal-close]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = btn.getAttribute('data-modal-close');
            const targetModal = targetId ? document.querySelector(targetId) : btn.closest('.modal-overlay');
            if (targetModal) {
                targetModal.style.display = 'none';
            }
        });
    });

    // Close on overlay backdrop click
    [createModal, editModal].forEach((modal) => {
        if (!modal) return;
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Open edit modal
    document.querySelectorAll('.btn-open-edit-agenda').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const agendaId = btn.getAttribute('data-agenda-id');
            const agendaStatus = btn.getAttribute('data-agenda-status') || 'terjadwal';
            const agendaNotulensi = btn.getAttribute('data-agenda-notulensi') || '';

            if (editForm && agendaId) {
                editForm.action = `/pembimbing/agenda/${agendaId}`;
            }
            if (editStatus) {
                editStatus.value = agendaStatus;
            }
            if (editNotulensi) {
                editNotulensi.value = agendaNotulensi;
            }
            if (editModal) {
                editModal.style.display = 'flex';
            }
        });
    });
});
