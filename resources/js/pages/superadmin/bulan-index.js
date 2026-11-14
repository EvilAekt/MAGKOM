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

        // Trigger Buka Modal Konfirmasi Toggle Bulan Magang
        const triggerToggle = event.target.closest('.btn-trigger-toggle-bulan');
        if (triggerToggle) {
            event.preventDefault();
            const nama = triggerToggle.dataset.nama || 'Periode Bulan';
            const isAktif = triggerToggle.dataset.statusAktif === '1';
            const actionUrl = triggerToggle.dataset.actionUrl;

            const modalToggle = document.getElementById('modalKonfirmasiToggleBulan');
            const formToggle = document.getElementById('formToggleBulan');
            const namaEl = document.getElementById('toggleBulanNama');
            const titleEl = document.getElementById('modalToggleBulanTitle');
            const msgEl = document.getElementById('toggleBulanMessage');
            const consequenceEl = document.getElementById('toggleBulanConsequence');
            const boxAlert = document.getElementById('toggleBulanBoxAlert');
            const iconWrap = document.getElementById('toggleBulanIconWrap');
            const submitBtn = document.getElementById('btnSubmitToggleBulan');
            const submitText = document.getElementById('btnSubmitToggleBulanText');

            if (formToggle && actionUrl) formToggle.action = actionUrl;
            if (namaEl) namaEl.textContent = nama;

            if (isAktif) {
                if (titleEl) titleEl.textContent = 'Konfirmasi Penonaktifan Bulan';
                if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin menonaktifkan periode bulan magang ini?';
                if (consequenceEl) consequenceEl.textContent = '• Menonaktifkan bulan ini akan menutup akses pendaftaran calon peserta baru pada siklus terkait.';
                if (boxAlert) { boxAlert.style.background = '#FFF1F2'; boxAlert.style.borderColor = '#FECDD3'; }
                if (iconWrap) { iconWrap.style.background = '#FEE2E2'; iconWrap.style.color = '#DC2626'; }
                if (submitBtn) submitBtn.style.background = '#DC2626';
                if (submitText) submitText.textContent = 'Ya, Nonaktifkan Bulan';
            } else {
                if (titleEl) titleEl.textContent = 'Konfirmasi Pengaktifan Bulan';
                if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin mengaktifkan kembali periode bulan magang ini?';
                if (consequenceEl) consequenceEl.textContent = '• Periode bulan ini akan aktif dan siap menerima pengajuan berkas pendaftaran peserta.';
                if (boxAlert) { boxAlert.style.background = '#F0FDF4'; boxAlert.style.borderColor = '#BBF7D0'; }
                if (iconWrap) { iconWrap.style.background = '#DCFCE7'; iconWrap.style.color = '#16A34A'; }
                if (submitBtn) submitBtn.style.background = '#16A34A';
                if (submitText) submitText.textContent = 'Ya, Aktifkan Bulan';
            }

            if (modalToggle) modalToggle.style.display = 'flex';
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
        const modalToggle = document.getElementById('modalKonfirmasiToggleBulan');
        if (event.target === modalTambah) closeTambahBulanModal();
        if (event.target === modalEdit) closeEditBulanModal();
        if (event.target === modalToggle) modalToggle.style.display = 'none';
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeTambahBulanModal();
            closeEditBulanModal();
            const modalToggle = document.getElementById('modalKonfirmasiToggleBulan');
            if (modalToggle) modalToggle.style.display = 'none';
        }
    });
});

