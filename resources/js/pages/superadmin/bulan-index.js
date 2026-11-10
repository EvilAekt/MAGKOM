/**
 * Modul Master Periode Bulan Magang SuperAdmin
 * - Penanganan modal tambah & edit periode bulan via event delegation
 * - Pengisian dataset HTML5 data-* (bulan mulai, tgl buka, tgl tutup, override status)
 * - Sistem Animasi Universal (Scroll-Craft + GSAP Paritas Pembimbing)
 */
import { initSuperAdminPageMotion } from '../../services/superadmin-motion.js';

export function openTambahBulanModal() {
    const modal = document.getElementById('modal-tambah-bulan');
    if (modal) modal.style.display = 'flex';
}

export function closeTambahBulanModal() {
    const modal = document.getElementById('modal-tambah-bulan');
    if (modal) modal.style.display = 'none';
}

export function openEditBulanModal(bulanId, bulanMulai, tglBuka, tglTutup, overrideStatus, actionUrl) {
    const form = document.getElementById('form-edit-bulan');
    const inputBulanMulai = document.getElementById('edit-bulan-mulai');
    const inputTglBuka = document.getElementById('edit-tgl-buka');
    const inputTglTutup = document.getElementById('edit-tgl-tutup');
    const inputOverride = document.getElementById('edit-override-status');
    const modal = document.getElementById('modal-edit-bulan');

    if (form) form.action = actionUrl || `/superadmin/bulan/${bulanId}`;
    if (inputBulanMulai) inputBulanMulai.value = bulanMulai || '';
    if (inputTglBuka) inputTglBuka.value = tglBuka || '';
    if (inputTglTutup) inputTglTutup.value = tglTutup || '';
    if (inputOverride) inputOverride.value = overrideStatus || '';
    if (modal) modal.style.display = 'flex';
}

export function closeEditBulanModal() {
    const modal = document.getElementById('modal-edit-bulan');
    if (modal) modal.style.display = 'none';
}

// Global backward compatibility
window.openTambahBulanModal = openTambahBulanModal;
window.closeTambahBulanModal = closeTambahBulanModal;
window.openEditBulanModal = openEditBulanModal;
window.closeEditBulanModal = closeEditBulanModal;

document.addEventListener('DOMContentLoaded', () => {
    initSuperAdminPageMotion();

    document.addEventListener('click', (event) => {
        // Trigger Buka Tambah
        const triggerTambah = event.target.closest('.btn-trigger-tambah-bulan');
        if (triggerTambah) {
            event.preventDefault();
            openTambahBulanModal();
            return;
        }

        // Trigger Buka Edit
        const triggerEdit = event.target.closest('.btn-trigger-edit-bulan');
        if (triggerEdit) {
            event.preventDefault();
            const id = triggerEdit.dataset.id;
            const bulanMulai = triggerEdit.dataset.bulanMulai || '';
            const tglBuka = triggerEdit.dataset.tglBuka || '';
            const tglTutup = triggerEdit.dataset.tglTutup || '';
            const overrideStatus = triggerEdit.dataset.overrideStatus || '';
            const actionUrl = triggerEdit.dataset.actionUrl || `/superadmin/bulan/${id}`;
            openEditBulanModal(id, bulanMulai, tglBuka, tglTutup, overrideStatus, actionUrl);
            return;
        }

        // Trigger Tutup via data-modal-dismiss
        const dismissBtn = event.target.closest('[data-modal-dismiss]');
        if (dismissBtn) {
            event.preventDefault();
            const targetId = dismissBtn.dataset.modalDismiss;
            const targetModal = document.getElementById(targetId);
            if (targetModal) targetModal.style.display = 'none';
            return;
        }

        // Backdrop click
        const modalTambah = document.getElementById('modal-tambah-bulan');
        const modalEdit = document.getElementById('modal-edit-bulan');
        if (event.target === modalTambah) closeTambahBulanModal();
        if (event.target === modalEdit) closeEditBulanModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeTambahBulanModal();
            closeEditBulanModal();
        }
    });
});

