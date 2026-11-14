/**
 * Modul Pusat Cadangan & Pemulihan Basis Data SuperAdmin
 * - Penanganan modal konfirmasi hapus snapshot database
 * - Peringatan privasi UU PDP & Disaster Recovery
 */
import { initSuperAdminPageMotion } from '../../services/superadmin-motion.js';

document.addEventListener('DOMContentLoaded', () => {
    initSuperAdminPageMotion();

    const modalHapus = document.getElementById('modalKonfirmasiHapusBackup');
    const formHapus = document.getElementById('formHapusBackup');
    const filenameEl = document.getElementById('hapusBackupFilename');
    const metaEl = document.getElementById('hapusBackupMeta');

    document.addEventListener('click', (event) => {
        // Trigger Buka Modal Hapus Snapshot
        const triggerDelete = event.target.closest('.btn-trigger-delete-backup');
        if (triggerDelete) {
            event.preventDefault();
            const filename = triggerDelete.dataset.filename || 'snapshot.sql.gz';
            const size = triggerDelete.dataset.size || '0';
            const created = triggerDelete.dataset.created || '-';
            const actionUrl = triggerDelete.dataset.actionUrl;

            if (formHapus && actionUrl) formHapus.action = actionUrl;
            if (filenameEl) filenameEl.textContent = filename;
            if (metaEl) metaEl.textContent = `Ukuran: ${size} MB • Dibuat: ${created}`;
            if (modalHapus) modalHapus.style.display = 'flex';
            return;
        }

        // Trigger Tutup Modal via data-modal-dismiss
        const dismissBtn = event.target.closest('[data-modal-dismiss]');
        if (dismissBtn) {
            event.preventDefault();
            const targetId = dismissBtn.dataset.modalDismiss;
            const targetModal = document.getElementById(targetId);
            if (targetModal) targetModal.style.display = 'none';
            return;
        }

        // Klik Backdrop
        if (event.target === modalHapus) {
            modalHapus.style.display = 'none';
        }
    });

    // Keyboard ESC
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (modalHapus) modalHapus.style.display = 'none';
        }
    });
});
