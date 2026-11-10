/**
 * Modul Interaksi Master Bidang SuperAdmin
 * - Penanganan modal tambah & edit bidang via event delegation
 * - Pengisian form aman berbasis atribut HTML5 data-* (kebal newline & kutip)
 */
document.addEventListener('DOMContentLoaded', () => {
    const modalTambah = document.getElementById('modalTambahBidang');
    const modalEdit = document.getElementById('modalEditBidang');
    const editNama = document.getElementById('editNamaBidang');
    const editDeskripsi = document.getElementById('editDeskripsiBidang');
    const formEdit = document.getElementById('formEditBidang');

    document.addEventListener('click', (event) => {
        // Trigger Buka Modal Tambah
        const triggerTambah = event.target.closest('.btn-trigger-tambah-bidang') || event.target.closest('#btnOpenTambahBidang');
        if (triggerTambah) {
            event.preventDefault();
            if (modalTambah) modalTambah.style.display = 'flex';
            return;
        }

        // Trigger Buka Modal Edit
        const triggerEdit = event.target.closest('.btn-trigger-edit-bidang');
        if (triggerEdit) {
            event.preventDefault();
            const id = triggerEdit.dataset.id;
            const nama = triggerEdit.dataset.nama || '';
            const deskripsi = triggerEdit.dataset.deskripsi || '';
            const actionUrl = triggerEdit.dataset.actionUrl || `/superadmin/bidang/${id}`;

            if (editNama) editNama.value = nama;
            if (editDeskripsi) editDeskripsi.value = deskripsi;
            if (formEdit) formEdit.action = actionUrl;
            if (modalEdit) modalEdit.style.display = 'flex';
            return;
        }

        // Trigger Tutup Modal
        const dismissBtn = event.target.closest('[data-modal-dismiss]');
        if (dismissBtn) {
            event.preventDefault();
            const targetId = dismissBtn.dataset.modalDismiss;
            const targetModal = document.getElementById(targetId);
            if (targetModal) targetModal.style.display = 'none';
            return;
        }

        // Tutup jika klik latar belakang (backdrop)
        if (event.target === modalTambah) {
            modalTambah.style.display = 'none';
        }
        if (event.target === modalEdit) {
            modalEdit.style.display = 'none';
        }
    });

    // Keyboard ESC untuk menutup modal
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (modalTambah) modalTambah.style.display = 'none';
            if (modalEdit) modalEdit.style.display = 'none';
        }
    });
});

